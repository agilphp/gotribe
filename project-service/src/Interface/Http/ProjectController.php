<?php

namespace Trekly\Project\Interface\Http;

use Trekly\Project\Application\CreateProjectUseCase;
use Trekly\Project\Application\ListProjectsUseCase;
use Trekly\Project\Application\PublishProjectUseCase;

class ProjectController
{
    private CreateProjectUseCase $createUseCase;
    private PublishProjectUseCase $publishUseCase;
    private ListProjectsUseCase $listUseCase;

    public function __construct(
        CreateProjectUseCase $createUseCase,
        PublishProjectUseCase $publishUseCase,
        ListProjectsUseCase $listUseCase
    ) {
        $this->createUseCase = $createUseCase;
        $this->publishUseCase = $publishUseCase;
        $this->listUseCase = $listUseCase;
    }

    public function create(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        // Validation skipped for brevity
        
        // Mock getting creatorId from JWT (passed via header by Gateway or decoded here)
        // For MVP we assume Gateway passes X-User-Id header
        $creatorId = $_SERVER['HTTP_X_USER_ID'] ?? 'unknown';

        try {
            $project = $this->createUseCase->execute(
                $data['title'],
                $data['description'],
                $data['activityType'],
                $creatorId,
                $data['startDateTime'],
                $data['meetingPoint'],
                (float) $data['price'],
                $data['currency'],
                $data['imageUrl'] ?? null
            );
            http_response_code(201);
            echo json_encode(['id' => $project->getId(), 'message' => 'Project created']);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function publish(string $id): void
    {
        $creatorId = $_SERVER['HTTP_X_USER_ID'] ?? 'unknown';
        try {
            $this->publishUseCase->execute($id, $creatorId);
            echo json_encode(['message' => 'Project published']);
        } catch (\Exception $e) {
            http_response_code(403);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function list(): void
    {
        $filters = [];
        if (isset($_GET['activityType'])) {
            $filters['activityType'] = $_GET['activityType'];
        }
        
        $projects = $this->listUseCase->execute($filters);
        
        $response = array_map(function ($p) {
            return [
                'id' => $p->getId(),
                'title' => $p->getTitle(),
                'activityType' => $p->getActivityType()->value,
                'startDateTime' => $p->getStartDateTime()->format(\DateTimeInterface::ATOM),
                'price' => $p->getPrice(),
                'currency' => $p->getCurrency(),
                'imageUrl' => $p->getImageUrl()
            ];
        }, $projects);

        echo json_encode($response);
    }
}
