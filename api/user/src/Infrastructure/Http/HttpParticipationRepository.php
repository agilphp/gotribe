<?php

namespace Trekly\User\Infrastructure\Http;

/**
 * Cliente HTTP para acceder al servicio de participaciones
 * Implementa solo los métodos necesarios para RateCreatorUseCase
 */
class HttpParticipationRepository implements \Trekly\User\Domain\Participation\ParticipationRepository
{
    private string $baseUrl;

    public function __construct(string $baseUrl = 'http://localhost/gotribe/api/participation')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function save($participation): void
    {
        throw new \Exception('Save operation not supported in HTTP client');
    }

    public function findById(string $id): ?object
    {
        throw new \Exception('FindById operation not supported in HTTP client');
    }

    public function findByProjectAndUser(string $projectId, string $userId): ?object
    {
        $url = "{$this->baseUrl}/api/participations/project/{$projectId}/user/{$userId}";
        
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
            
            public function getId(): string {
                return $this->data['id'] ?? '';
            }
            
            public function getProjectId(): string {
                return $this->data['projectId'] ?? '';
            }
            
            public function getUserId(): string {
                return $this->data['userId'] ?? '';
            }
            
            public function getStatus(): object {
                // Crear un objeto anónimo que simula el enum ParticipationStatus
                $statusValue = $this->data['status'] ?? 'REQUESTED';
                return new class($statusValue) {
                    public string $value;
                    
                    public function __construct(string $value) {
                        $this->value = $value;
                    }
                };
            }
        };
    }
}
