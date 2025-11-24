<?php

require_once __DIR__ . '/../bootstrap.php';

use Trekly\Project\Infrastructure\Persistence\DoctrineProjectRepository;
use Trekly\Project\Application\CreateProjectUseCase;
use Trekly\Project\Application\PublishProjectUseCase;
use Trekly\Project\Application\ListProjectsUseCase;
use Trekly\Project\Interface\Http\ProjectController;

header('Content-Type: application/json');
$allowedOrigin = $_ENV['CORS_ALLOWED_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $allowedOrigin");
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

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
    $publishProjectUseCase = new PublishProjectUseCase($projectRepository);
    $listProjectsUseCase = new ListProjectsUseCase($projectRepository);

    // Setup Controller
    $controller = new ProjectController(
        $createProjectUseCase,
        $publishProjectUseCase,
        $listProjectsUseCase,
        $projectRepository
    );

    // Router
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = rtrim($uri, '/'); // Normalize URI: remove trailing slash
    $method = $_SERVER['REQUEST_METHOD'];

    // Routes: /api/projects and /api/projects/{id}/publish
    if ($uri === '/api/projects') {
        if ($method === 'POST') {
            $controller->create();
        } elseif ($method === 'GET') {
            $controller->list();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
    } elseif (preg_match('#^/api/projects/([^/]+)/publish$#', $uri, $matches)) {
        $id = $matches[1];
        if ($method === 'POST') {
            $controller->publish($id);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
    } elseif (preg_match('#^/api/projects/([^/]+)$#', $uri, $matches)) {
        $id = $matches[1];
        if ($method === 'GET') {
            $controller->getById($id);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
    } elseif ($uri === '/api/projects/currencies' && $method === 'GET') {
        // Get currencies endpoint
        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', 
                    $_ENV['DB_HOST'] ?? 'localhost',
                    $_ENV['DB_NAME'] ?? 'tribew_projects'
                ),
                $_ENV['DB_USER'] ?? 'tribew_eli4as',
                $_ENV['DB_PASS'] ?? '8TK4Nqp8d9SX4uxa',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            $currencyRepository = new \Trekly\Project\Infrastructure\Persistence\DoctrineCurrencyRepository($pdo);
            $currencies = $currencyRepository->findAll();
            echo json_encode($currencies);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch currencies: ' . $e->getMessage()]);
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
