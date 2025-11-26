<?php

namespace Trekly\Payments\Infrastructure\Payment;

class MercadoPagoService
{
    private string $accessToken;
    private string $publicKey;
    private string $baseUrl = 'https://api.mercadopago.com';

    public function __construct()
    {
        $this->accessToken = $_ENV['MP_ACCESS_TOKEN'] ?? '';
        $this->publicKey = $_ENV['MP_PUBLIC_KEY'] ?? '';
    }

    /**
     * Create a payment preference for Mercado Pago checkout
     * 
     * @param float $amount Total amount to charge
     * @param string $description Payment description
     * @param array $metadata Additional data (project_id, user_id, etc.)
     * @return array Preference data with checkout URL
     */
    public function createPreference(float $amount, string $description, array $metadata = []): array
    {
        $preference = [
            'items' => [
                [
                    'title' => $description,
                    'quantity' => 1,
                    'currency_id' => 'COP',
                    'unit_price' => $amount
                ]
            ],
            'back_urls' => [
                'success' => $_ENV['MP_SUCCESS_URL'] ?? 'https://gotribe.co/payment/success',
                'failure' => $_ENV['MP_FAILURE_URL'] ?? 'https://gotribe.co/payment/failure',
                'pending' => $_ENV['MP_PENDING_URL'] ?? 'https://gotribe.co/payment/pending'
            ],
            'auto_return' => 'approved',
            'notification_url' => $_ENV['MP_WEBHOOK_URL'] ?? 'https://gotribe.co/api/payments/webhook',
            'external_reference' => $metadata['project_id'] ?? '',
            'metadata' => $metadata
        ];

        $response = $this->post('/checkout/preferences', $preference);
        
        if (!isset($response['id'])) {
            throw new \RuntimeException('Failed to create payment preference: ' . json_encode($response));
        }

        return [
            'preference_id' => $response['id'],
            'init_point' => $response['init_point'], // Checkout URL
            'sandbox_init_point' => $response['sandbox_init_point'] ?? null
        ];
    }

    /**
     * Get payment information by ID
     * 
     * @param string $paymentId Mercado Pago payment ID
     * @return array Payment data
     */
    public function getPayment(string $paymentId): array
    {
        return $this->get("/v1/payments/{$paymentId}");
    }

    /**
     * Get payment status
     * 
     * @param string $paymentId Mercado Pago payment ID
     * @return string Payment status (approved, pending, rejected, etc.)
     */
    public function getPaymentStatus(string $paymentId): string
    {
        $payment = $this->getPayment($paymentId);
        return $payment['status'] ?? 'unknown';
    }

    /**
     * Verify webhook signature (if configured)
     * 
     * @param array $headers Request headers
     * @param string $body Request body
     * @return bool True if signature is valid
     */
    public function verifyWebhookSignature(array $headers, string $body): bool
    {
        // Mercado Pago doesn't require signature verification by default
        // but you can implement it if needed
        return true;
    }

    /**
     * Process webhook notification
     * 
     * @param array $data Webhook payload
     * @return array Processed payment data
     */
    public function processWebhook(array $data): array
    {
        if (!isset($data['type']) || !isset($data['data']['id'])) {
            throw new \InvalidArgumentException('Invalid webhook payload');
        }

        $type = $data['type'];
        $paymentId = $data['data']['id'];

        // Only process payment notifications
        if ($type !== 'payment') {
            return ['status' => 'ignored', 'type' => $type];
        }

        // Get full payment information
        $payment = $this->getPayment($paymentId);

        return [
            'payment_id' => $payment['id'],
            'status' => $payment['status'],
            'status_detail' => $payment['status_detail'] ?? '',
            'amount' => $payment['transaction_amount'] ?? 0,
            'currency' => $payment['currency_id'] ?? 'COP',
            'external_reference' => $payment['external_reference'] ?? '',
            'metadata' => $payment['metadata'] ?? [],
            'payer_email' => $payment['payer']['email'] ?? '',
            'payment_method' => $payment['payment_method_id'] ?? ''
        ];
    }

    /**
     * Make GET request to Mercado Pago API
     */
    private function get(string $endpoint): array
    {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            throw new \RuntimeException('cURL error: ' . curl_error($ch));
        }
        
        curl_close($ch);

        $data = json_decode($response, true);

        if ($httpCode >= 400) {
            throw new \RuntimeException('Mercado Pago API error: ' . json_encode($data));
        }

        return $data;
    }

    /**
     * Make POST request to Mercado Pago API
     */
    private function post(string $endpoint, array $data): array
    {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            throw new \RuntimeException('cURL error: ' . curl_error($ch));
        }
        
        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode >= 400) {
            throw new \RuntimeException('Mercado Pago API error: ' . json_encode($responseData));
        }

        return $responseData;
    }

    /**
     * Get public key for frontend integration
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}
