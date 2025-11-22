<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Trekly\Participation\Infrastructure\Persistence\PDOParticipationRepository;
use Trekly\Participation\Application\RequestParticipationUseCase;
use Trekly\Participation\Application\UpdateParticipationStatusUseCase;
use Trekly\Participation\Interface\Http\ParticipationController;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// Database Connection
$host = $_ENV['DB_HOST'] ?? 'participation-db';
$db   = $_ENV['DB_NAME'] ?? 'participation_db';
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
$repository = new PDOParticipationRepository($pdo);

// Use Cases
$requestUseCase = new RequestParticipationUseCase($repository);
$updateStatusUseCase = new UpdateParticipationStatusUseCase($repository);

// Controller
$controller = new ParticipationController($requestUseCase, $updateStatusUseCase);

// Router
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// /api/participations
// /api/participations/{id}/status

if ($uri === '/api/participations') {
    if ($method === 'POST') {
        $controller->request();
    } else {
        http_response_code(405);
    }
} elseif (preg_match('#^/api/participations/([^/]+)/status$#', $uri, $matches)) {
    $id = $matches[1];
    if ($method === 'PUT') {
        $controller->updateStatus($id);
    } else {
        http_response_code(405);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found', 'uri' => $uri]);
}
