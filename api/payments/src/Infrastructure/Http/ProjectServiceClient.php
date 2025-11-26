<?php

namespace Trekly\Payments\Infrastructure\Http;

class ProjectServiceClient
{
    private string $projectsApiUrl;

    public function __construct()
    {
        $this->projectsApiUrl = $_ENV['PROJECTS_API_URL'] ?? 'http://localhost/api/projects';
    }

    /**
     * Notify projects service to publish a project after payment approval
     * 
     * @param string $projectId Project ID to publish
     * @return array Response from projects service
     */
    public function publishProject(string $projectId): array
    {
        $url = "{$this->projectsApiUrl}/{$projectId}/publish";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Internal-Request: true' // Mark as internal service call
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            throw new \RuntimeException('cURL error: ' . curl_error($ch));
        }
        
        curl_close($ch);

        if ($httpCode >= 400) {
            throw new \RuntimeException("Failed to publish project: HTTP {$httpCode}");
        }

        return json_decode($response, true) ?? [];
    }
}
