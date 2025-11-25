<?php

namespace Trekly\Participation\Infrastructure\Http;

class ProjectServiceClient
{
    private string $baseUrl;

    public function __construct(string $baseUrl = 'https://gotribe.co')
    {
        $this->baseUrl = $baseUrl;
    }

    public function getProject(string $projectId): ?array
    {
        $url = "{$this->baseUrl}/api/projects/{$projectId}";
        
        error_log("ProjectServiceClient - Fetching project from: {$url}");
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        error_log("ProjectServiceClient - HTTP Code: {$httpCode}, Response: " . substr($response, 0, 200));
        
        if ($curlError) {
            error_log("ProjectServiceClient - cURL Error: {$curlError}");
        }
        
        if ($httpCode === 200 && $response) {
            return json_decode($response, true);
        }
        
        return null;
    }
}
