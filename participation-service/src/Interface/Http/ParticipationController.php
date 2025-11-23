<?php

namespace Trekly\Participation\Interface\Http;

use Trekly\Participation\Application\RequestParticipationUseCase;
use Trekly\Participation\Application\UpdateParticipationStatusUseCase;
use Trekly\Participation\Infrastructure\Persistence\DoctrineParticipationRepository;

class ParticipationController
{
    public function __construct(
        private RequestParticipationUseCase $requestUseCase,
        private UpdateParticipationStatusUseCase $updateStatusUseCase,
        private DoctrineParticipationRepository $repository
    ) {}

    public function request(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $userId = $_SERVER['HTTP_X_USER_ID'] ?? 'unknown';

        if (!isset($data['projectId'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing projectId']);
            return;
        }

        try {
            $participation = $this->requestUseCase->execute($data['projectId'], $userId);
            http_response_code(201);
            echo json_encode(['id' => $participation->getId(), 'status' => $participation->getStatus()->value]);
        } catch (\Exception $e) {
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
}
