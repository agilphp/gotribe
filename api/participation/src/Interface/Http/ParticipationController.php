<?php

namespace Trekly\Participation\Interface\Http;

use Trekly\Participation\Application\RequestParticipationUseCase;
use Trekly\Participation\Application\UpdateParticipationStatusUseCase;
use Trekly\Participation\Infrastructure\Persistence\DoctrineParticipationRepository;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ParticipationController
{
    public function __construct(
        private RequestParticipationUseCase $requestUseCase,
        private UpdateParticipationStatusUseCase $updateStatusUseCase,
        private \Trekly\Participation\Application\ValidateParticipationUseCase $validateUseCase,
        private DoctrineParticipationRepository $repository
    ) {}

    private function getUserIdFromToken(): ?string
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? null;

        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return null;
        }

        $jwt = $matches[1];
        try {
            $secret = $_ENV['JWT_SECRET'] ?? 'gotribe_jwt_secret_change_this_in_production_32chars_minimum';
            $decoded = JWT::decode($jwt, new Key($secret, 'HS256'));
            return $decoded->sub ?? null;
        } catch (\Exception $e) {
            error_log("JWT Decode Error: " . $e->getMessage());
            return null;
        }
    }

    public function request(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        error_log('[ParticipationController] Incoming payload: ' . json_encode($data));
        $userId = $this->getUserIdFromToken();
        error_log('[ParticipationController] UserId from token: ' . ($userId ?: 'NULL'));

        if (!$userId) {
            error_log('[ParticipationController] Unauthorized: No userId');
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        if (!isset($data['projectId'])) {
            error_log('[ParticipationController] Missing projectId in payload');
            http_response_code(400);
            echo json_encode(['error' => 'Missing projectId']);
            return;
        }

        try {
            error_log('[ParticipationController] Attempting to join project: ' . $data['projectId'] . ' for user: ' . $userId);
            $participation = $this->requestUseCase->execute($data['projectId'], $userId);
            http_response_code(201);
            echo json_encode(['id' => $participation->getId(), 'status' => $participation->getStatus()->value]);
        } catch (\Exception $e) {
            error_log('[ParticipationController] Exception: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateStatus(string $id): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['status'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing status']);
            return;
        }

        try {
            $this->updateStatusUseCase->execute($id, $data['status']);
            echo json_encode(['message' => 'Status updated']);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getUserParticipations(string $userId): void
    {
        try {
            $participations = $this->repository->findByUserId($userId);
            
            $response = array_map(function ($p) {
                return [
                    'id' => $p->getId(),
                    'projectId' => $p->getProjectId(),
                    'userId' => $p->getUserId(),
                    'status' => $p->getStatus()->value,
                    'requestedAt' => $p->getRequestedAt()->format('Y-m-d H:i:s')
                ];
            }, $participations);

            echo json_encode($response);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function validate(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $creatorId = $this->getUserIdFromToken();

        if (!$creatorId) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        if (!isset($data['participationId'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing participationId']);
            return;
        }

        try {
            $result = $this->validateUseCase->execute($data['participationId'], $creatorId);
            echo json_encode($result);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage(), 'valid' => false]);
        }
    }
}
