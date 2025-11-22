<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Trekly\User\Infrastructure\Persistence\PDOUserProfileRepository;
use Trekly\User\Application\GetUserProfileUseCase;
use Trekly\User\Application\UpdateUserProfileUseCase;
use Trekly\User\Application\CreateCreatorProfileUseCase;
use Trekly\User\Interface\Http\ProfileController;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// Database Connection
$host = $_ENV['DB_HOST'] ?? 'user-db';
$db   = $_ENV['DB_NAME'] ?? 'user_db';
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
$repository = new PDOUserProfileRepository($pdo);

// Use Cases
$getUserProfileUseCase = new GetUserProfileUseCase($repository);
$updateUserProfileUseCase = new UpdateUserProfileUseCase($repository);
$createCreatorProfileUseCase = new CreateCreatorProfileUseCase($repository);

// Controller
$controller = new ProfileController(
    $getUserProfileUseCase,
    $updateUserProfileUseCase,
    $createCreatorProfileUseCase
);

// Router
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Simple routing based on path segments
// /api/users/{id}
// /api/users/{id}/creator-profile

if (preg_match('#^/api/users/([^/]+)$#', $uri, $matches)) {
    $userId = $matches[1];
    if ($method === 'GET') {
        $controller->getProfile($userId);
    } elseif ($method === 'PUT') {
        $controller->updateProfile($userId);
    } else {
        http_response_code(405);
    }
} elseif (preg_match('#^/api/users/([^/]+)/creator-profile$#', $uri, $matches)) {
    $userId = $matches[1];
    if ($method === 'POST') {
        $controller->createCreatorProfile($userId);
    } else {
        http_response_code(405);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found', 'uri' => $uri]);
}
