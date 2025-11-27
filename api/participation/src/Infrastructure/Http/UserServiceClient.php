<?php

namespace Trekly\Participation\Infrastructure\Http;

class UserServiceClient
{
    private string $baseUrl;

    public function __construct(?string $baseUrl = null)
    {
        $this->baseUrl = $baseUrl ?? ($_ENV['USER_SERVICE_URL'] ?? $_SERVER['USER_SERVICE_URL'] ?? getenv('USER_SERVICE_URL') ?: 'http://localhost/gotribe');
    }

    public function getUser(string $userId): ?array
    {
        $url = "{$this->baseUrl}/api/users/{$userId}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            return json_decode($response, true);
        }
        
        return null;
    }
}
