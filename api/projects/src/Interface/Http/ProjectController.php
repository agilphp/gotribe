<?php

namespace Trekly\Project\Interface\Http;

use Trekly\Project\Application\CreateProjectUseCase;
use Trekly\Project\Application\ListProjectsUseCase;
use Trekly\Project\Application\PublishProjectUseCase;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ProjectController
{
    private CreateProjectUseCase $createUseCase;
    private PublishProjectUseCase $publishUseCase;
    private ListProjectsUseCase $listUseCase;
    private \Trekly\Project\Domain\Project\ProjectRepository $repository;

    public function __construct(
        CreateProjectUseCase $createUseCase,
        PublishProjectUseCase $publishUseCase,
        ListProjectsUseCase $listUseCase,
        \Trekly\Project\Domain\Project\ProjectRepository $repository
    ) {
        $this->createUseCase = $createUseCase;
        $this->publishUseCase = $publishUseCase;
        $this->listUseCase = $listUseCase;
        $this->repository = $repository;
    }

    public function create(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $creatorId = $_SERVER['HTTP_X_USER_ID'] ?? null;
        $userRole = $_SERVER['HTTP_X_USER_ROLE'] ?? null;

        // Fallback: Decode JWT if headers are missing (Nginx issue)
        if (!$creatorId || !$userRole) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            
            // Try getting header from apache_request_headers if missing
            if (empty($authHeader) && function_exists('apache_request_headers')) {
                $headers = apache_request_headers();
                $authHeader = $headers['Authorization'] ?? '';
            }

            error_log("Raw Auth Header: " . $authHeader);

            if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                try {
                    $jwt = $matches[1];
                    // MATCHING SECRET KEY WITH AUTH SERVICE
                    $key = new Key($_ENV['JWT_SECRET'] ?? 'gotribe_jwt_secret_change_this_in_production_32chars_minimum', 'HS256');
                    $decoded = JWT::decode($jwt, $key);
                    
                    $creatorId = $decoded->sub ?? $creatorId;
                    $userRole = $decoded->role ?? $userRole;
                    
                    error_log("Decoded JWT - Role: $userRole, ID: $creatorId");
                } catch (\Exception $e) {
                    error_log("JWT Decode Error: " . $e->getMessage());
                    // Token invalid or expired
                    http_response_code(401);
                    echo json_encode(['error' => 'Invalid token: ' . $e->getMessage()]);
                    return;
                }
            } else {
                error_log("No Bearer token found in Authorization header");
            }
        }

        error_log("Final User Role: " . ($userRole ?? 'NULL'));

        $creatorId = $creatorId ?? 'unknown';
        $userRole = $userRole ?? 'MEMBER';

        if ($userRole === 'MEMBER') {
            http_response_code(403);
            $debugHeaders = [];
            if (function_exists('apache_request_headers')) {
                $debugHeaders = apache_request_headers();
            }
            // Fallback to $_SERVER
            foreach ($_SERVER as $key => $value) {
                if (strpos($key, 'HTTP_') === 0) {
                    $debugHeaders[$key] = $value;
                }
            }
            
            echo json_encode([
                'error' => 'Only CREATOR and ADMIN users can create projects. Current role: ' . $userRole . '. Creator ID: ' . $creatorId,
                'debug_headers' => $debugHeaders,
                'env_jwt_secret_exists' => !empty($_ENV['JWT_SECRET'])
            ]);
            return;
        }

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
                'creatorId' => $p->getCreatorId(),
                'title' => $p->getTitle(),
                'activityType' => $p->getActivityType()->value,
                'startDateTime' => $p->getStartDateTime()->format(\DateTimeInterface::ATOM),
                'price' => $p->getPrice(),
                'currency' => $p->getCurrency(),
                'maxGuests' => $p->getMaxGuests(),
                'imageUrl' => $p->getImageUrl(),
                'isPublished' => $p->isPublished(),
                'isActive' => $p->isActive(),
                'createdAt' => $p->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }, $projects);

        echo json_encode($response);
    }

    public function getById(string $id): void
    {
        try {
            $project = $this->repository->findById($id);
            
            if (!$project) {
                http_response_code(404);
                echo json_encode(['error' => 'Project not found']);
                return;
            }

            $response = [
                'id' => $project->getId(),
                'creatorId' => $project->getCreatorId(),
                'title' => $project->getTitle(),
                'description' => $project->getDescription(),
                'activityType' => $project->getActivityType()->value,
                'startDateTime' => $project->getStartDateTime()->format(\DateTimeInterface::ATOM),
                'meetingPoint' => $project->getMeetingPoint(),
                'price' => $project->getPrice(),
                'currency' => $project->getCurrency(),
                'maxGuests' => $project->getMaxGuests(),
                'imageUrl' => $project->getImageUrl(),
                'isPublished' => $project->isPublished(),
                'isActive' => $project->isActive(),
                'createdAt' => $project->getCreatedAt()->format('Y-m-d H:i:s')
            ];

            echo json_encode($response);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
