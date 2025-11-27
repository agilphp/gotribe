<?php


use Trekly\Project\Infrastructure\Persistence\DoctrineProjectRepository;
use Trekly\Project\Application\CreateProjectUseCase;
use Trekly\Project\Application\PublishProjectUseCase;
use Trekly\Project\Application\ListProjectsUseCase;
use Trekly\Project\Interface\Http\ProjectController;
use Trekly\Project\Infrastructure\Security\JwtTokenProvider;

// CORS headers are handled by root bootstrap.php

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Get EntityManager from bootstrap with error handling
    try {
        $entityManager = require __DIR__ . '/../bootstrap.php';
        if (!$entityManager) {
            throw new \Exception("Bootstrap returned nothing.");
        }
    } catch (\Throwable $e) {
        throw new \Exception("Bootstrap failed: " . $e->getMessage());
    }
    
    // Setup Repository with Doctrine
    $projectRepository = new DoctrineProjectRepository($entityManager);

    // Setup Use Cases
    $createProjectUseCase = new CreateProjectUseCase($projectRepository);
    $publishProjectUseCase = new PublishProjectUseCase(
        $projectRepository,
        new \Trekly\Project\Infrastructure\Services\CreatorQRService(),
        new \Trekly\Project\Infrastructure\Email\ProjectEmailService(),
        new \Trekly\Project\Infrastructure\Http\AuthServiceClient()
    );
    $listProjectsUseCase = new ListProjectsUseCase($projectRepository);

        // Setup JWT Token Provider
        $jwtTokenProvider = new JwtTokenProvider($_ENV['JWT_SECRET'] ?? 'gotribe_jwt_secret_change_this_in_production_32chars_minimum');

    // Setup Controller
        $controller = new ProjectController(
            $createProjectUseCase,
            $publishProjectUseCase,
            $listProjectsUseCase,
            $projectRepository,
            $jwtTokenProvider
        );

    // Router
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = rtrim($uri, '/'); // Normalize URI: remove trailing slash
    $method = $_SERVER['REQUEST_METHOD'];

    // DEBUG: Log request details
    error_log("DEBUG - URI: $uri, Method: $method, Raw Body: " . file_get_contents('php://input'));

    // Routes: /api/projects and /api/projects/{id}/publish
    // Support subdirectories (e.g., /gotribe/api/projects)
    if (preg_match('#/api/projects$#', $uri)) {
        if ($method === 'POST') {
            error_log("DEBUG - Calling create() method");
            $controller->create();
        } elseif ($method === 'GET') {
            error_log("DEBUG - Calling list() method");
            $controller->list();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed', 'received_method' => $method, 'uri' => $uri]);
        }
    } elseif (preg_match('#^/api/projects/([^/]+)/publish$#', $uri, $matches)) {
        $id = $matches[1];
        if ($method === 'POST') {
            $controller->publish($id);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
    } elseif (preg_match('#/api/projects/currencies$#', $uri) && $method === 'GET') {
        // Get currencies endpoint
        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4',
                    $_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost',
                    $_ENV['PROJECTS_DB_DATABASE'] ?? $_SERVER['PROJECTS_DB_DATABASE'] ?? getenv('PROJECTS_DB_DATABASE') ?: 'tribew_projects'
                ),
                $_ENV['DB_USERNAME'] ?? $_SERVER['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'root',
                $_ENV['DB_PASSWORD'] ?? $_SERVER['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $currencyRepository = new \Trekly\Project\Infrastructure\Persistence\DoctrineCurrencyRepository($pdo);
            $currencies = $currencyRepository->findAll();
            echo json_encode($currencies);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch currencies: ' . $e->getMessage()]);
        }
    } elseif (preg_match('#^/api/projects/([^/]+)$#', $uri, $matches)) {
        $id = $matches[1];
        if ($method === 'GET') {
            $controller->getById($id);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not Found', 'uri' => $uri, 'method' => $method]);
    }
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal Server Error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'env_vars_loaded' => !empty($_ENV)
    ]);
}
