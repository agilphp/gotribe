<?php

namespace Trekly\Payments\Application;

use Trekly\Payments\Domain\Payment\PaymentRepository;
use Trekly\Payments\Infrastructure\Payment\MercadoPagoService;
use Trekly\Payments\Infrastructure\Http\ProjectServiceClient;

class ProcessWebhookUseCase
{
    public function __construct(
        private PaymentRepository $paymentRepository,
        private MercadoPagoService $mercadoPagoService,
        private ProjectServiceClient $projectServiceClient
    ) {}

    /**
     * Process Mercado Pago webhook notification
     * 
     * @param array $webhookData Webhook payload from Mercado Pago
     * @return array Processing result
     */
    public function execute(array $webhookData): array
    {
        try {
            // Process webhook and get payment data
            $paymentData = $this->mercadoPagoService->processWebhook($webhookData);

            // Find payment by external reference (project_id) or metadata
            $projectId = $paymentData['external_reference'];
            $payment = $this->paymentRepository->findByProjectId($projectId);

            if (!$payment) {
                throw new \RuntimeException("Payment not found for project: {$projectId}");
            }

            // Update payment with Mercado Pago payment ID and status
            $payment->setMercadoPagoPaymentId($paymentData['payment_id']);
            $payment->setStatus($paymentData['status']);
            $payment->setPaymentMethod($paymentData['payment_method'] ?? '');
            $payment->setPayerEmail($paymentData['payer_email'] ?? '');

            $this->paymentRepository->save($payment);

            // If payment is approved, notify projects service to publish project
            if ($paymentData['status'] === 'approved') {
                try {
                    $this->projectServiceClient->publishProject($projectId);
                } catch (\Exception $e) {
                    // Log error but don't fail the webhook
                    error_log("Failed to publish project {$projectId}: " . $e->getMessage());
                }
            }

            return [
                'success' => true,
                'payment_id' => $payment->getId(),
                'status' => $paymentData['status'],
                'project_published' => $paymentData['status'] === 'approved'
            ];
        } catch (\Exception $e) {
            error_log("Webhook processing error: " . $e->getMessage());
            throw $e;
        }
    }
}
