<?php

namespace Trekly\Participation\Infrastructure\Http;

class AuthServiceClient
{
    private string $baseUrl;

    public function __construct(?string $baseUrl = null)
    {
        $this->baseUrl = $baseUrl ?? ($_ENV['AUTH_SERVICE_URL'] ?? $_SERVER['AUTH_SERVICE_URL'] ?? getenv('AUTH_SERVICE_URL') ?: 'http://localhost/gotribe');
    }

    public function getUser(string $userId): ?array
    {
        $url = "{$this->baseUrl}/api/auth/users/{$userId}";
        
        error_log("AuthServiceClient - Fetching user from: {$url}");
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        error_log("AuthServiceClient - HTTP Code: {$httpCode}");
        
        if ($curlError) {
            error_log("AuthServiceClient - cURL Error: {$curlError}");
        }
        
        if ($httpCode === 200 && $response) {
            return json_decode($response, true);
        }
        
        return null;
    }
}
