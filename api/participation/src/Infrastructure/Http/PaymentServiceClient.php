<?php

namespace Trekly\Participation\Infrastructure\Http;

class PaymentServiceClient
{
    private string $baseUrl;

    public function __construct()
    {
        // Use environment variable or default to same domain
        $this->baseUrl = $_ENV['PAYMENT_SERVICE_URL'] ?? 'https://gotribe.co/api/payments';
    }

    /**
     * Notifies payment service when a member payment is validated
     * This triggers the 10% commission calculation
     */
    public function registerMemberPayment(
        string $creatorId,
        string $projectId,
        float $projectPrice,
        string $dueDate
    ): bool {
        $url = $this->baseUrl . '/cuentas';
        
        $data = [
            'creator_id' => $creatorId,
            'project_id' => $projectId,
            'monto' => $projectPrice, // The use case will calculate 10%
            'fecha_vencimiento' => $dueDate
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        // Log error but don't fail the validation
        error_log("Payment service error: HTTP $httpCode - $response");
        return false;
    }
}
