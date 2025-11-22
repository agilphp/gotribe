<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Trekly\Project\Infrastructure\Persistence\PDOProjectRepository;
use Trekly\Project\Application\CreateProjectUseCase;
use Trekly\Project\Application\PublishProjectUseCase;
use Trekly\Project\Application\ListProjectsUseCase;
use Trekly\Project\Interface\Http\ProjectController;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// Database Connection
$host = $_ENV['DB_HOST'] ?? 'project-db';
$db   = $_ENV['DB_NAME'] ?? 'project_db';
$user = $_ENV['DB_USER'] ?? 'trekly_user';
$pass = $_ENV['DB_PASS'] ?? 'trekly_pass';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Dependencies
$repository = new PDOProjectRepository($pdo);

// Use Cases
$createUseCase = new CreateProjectUseCase($repository);
$publishUseCase = new PublishProjectUseCase($repository);
$listUseCase = new ListProjectsUseCase($repository);

// Controller
$controller = new ProjectController($createUseCase, $publishUseCase, $listUseCase);

// Router
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// /api/projects
// /api/projects/{id}/publish

if ($uri === '/api/projects') {
    if ($method === 'POST') {
        $controller->create();
    } elseif ($method === 'GET') {
        $controller->list();
    } else {
        http_response_code(405);
    }
} elseif (preg_match('#^/api/projects/([^/]+)/publish$#', $uri, $matches)) {
    $id = $matches[1];
    if ($method === 'POST') {
        $controller->publish($id);
    } else {
        http_response_code(405);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found', 'uri' => $uri]);
}
