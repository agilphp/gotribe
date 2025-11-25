<?php

namespace Trekly\Project\Infrastructure\Http;

class AuthServiceClient
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = $_ENV['AUTH_SERVICE_URL'] ?? 'https://gotribe.co/api/auth';
    }

    public function getUser(string $userId): ?array
    {
        $url = $this->baseUrl . "/users/{$userId}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            return json_decode($response, true);
        }

        error_log("AuthServiceClient - Failed to get user {$userId}: HTTP {$httpCode}");
        return null;
    }
}
