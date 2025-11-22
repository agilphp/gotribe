<?php

namespace Trekly\Participation\Interface\Http;

use Trekly\Participation\Application\RequestParticipationUseCase;
use Trekly\Participation\Application\UpdateParticipationStatusUseCase;

class ParticipationController
{
    private RequestParticipationUseCase $requestUseCase;
    private UpdateParticipationStatusUseCase $updateStatusUseCase;

    public function __construct(
        RequestParticipationUseCase $requestUseCase,
        UpdateParticipationStatusUseCase $updateStatusUseCase
    ) {
        $this->requestUseCase = $requestUseCase;
        $this->updateStatusUseCase = $updateStatusUseCase;
    }

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
}
