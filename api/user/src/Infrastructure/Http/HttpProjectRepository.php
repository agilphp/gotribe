<?php

namespace Trekly\User\Infrastructure\Http;

/**
 * Cliente HTTP para acceder al servicio de proyectos
 * Implementa solo los métodos necesarios para RateCreatorUseCase
 */
class HttpProjectRepository implements \Trekly\User\Domain\Project\ProjectRepository
{
    private string $baseUrl;

    public function __construct(string $baseUrl = 'http://localhost/gotribe/api/projects')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function save($project): void
    {
        throw new \Exception('Save operation not supported in HTTP client');
    }

    public function findById(string $id): ?object
    {
        $url = "{$this->baseUrl}/api/projects/{$id}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            return null;
        }
        
        $data = json_decode($response, true);
        if (!$data) {
            return null;
        }
        
        // Crear un objeto anónimo simple con los datos necesarios
        return new class($data) {
            private array $data;
            
            public function __construct(array $data) {
                $this->data = $data;
            }
            
            public function getPrice(): float {
                return (float)($this->data['price'] ?? 0);
            }
            
            public function getId(): string {
                return $this->data['id'] ?? '';
            }
        };
    }

    public function findAll(array $filters = []): array
    {
        throw new \Exception('FindAll operation not supported in HTTP client');
    }
}
