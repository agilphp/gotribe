<?php

namespace Trekly\Payments\Application;

use Trekly\Payments\Domain\Payment\Payment;
use Trekly\Payments\Domain\Payment\PaymentRepository;
use Trekly\Payments\Infrastructure\Payment\MercadoPagoService;

class CreatePaymentPreferenceUseCase
{
    public function __construct(
        private PaymentRepository $paymentRepository,
        private MercadoPagoService $mercadoPagoService
    ) {}

    /**
     * Create a payment preference for a project commission
     * 
     * @param string $projectId Project ID
     * @param float $projectBudget Total project budget
     * @param string $userId Creator user ID
     * @param string $currency Currency code (default: COP)
     * @return array Preference data with checkout URL
     */
    public function execute(
        string $projectId,
        float $projectBudget,
        string $userId,
        string $currency = 'COP'
    ): array {
        // Calculate 10% commission
        $commissionAmount = $projectBudget * 0.10;

        // Create payment record in database
        $payment = Payment::create(
            $projectId,
            $userId,
            $commissionAmount,
            $currency
        );

        $this->paymentRepository->save($payment);

        // Create Mercado Pago preference
        $description = "GoTribe Commission - Project #{$projectId}";
        $metadata = [
            'project_id' => $projectId,
            'user_id' => $userId,
            'payment_id' => $payment->getId(),
            'commission_percentage' => 10
        ];

        try {
            $preference = $this->mercadoPagoService->createPreference(
                $commissionAmount,
                $description,
                $metadata
            );

            // Update payment with preference ID
            $payment->setMercadoPagoPreferenceId($preference['preference_id']);
            $this->paymentRepository->save($payment);

            return [
                'payment_id' => $payment->getId(),
                'amount' => $commissionAmount,
                'currency' => $currency,
                'preference_id' => $preference['preference_id'],
                'checkout_url' => $preference['init_point']
            ];
        } catch (\Exception $e) {
            // Mark payment as failed
            $payment->setStatus('failed');
            $payment->setErrorMessage($e->getMessage());
            $this->paymentRepository->save($payment);
            
            throw $e;
        }
    }
}
