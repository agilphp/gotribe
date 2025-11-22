<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Trekly\Auth\Infrastructure\Persistence\PDOUserRepository;
use Trekly\Auth\Infrastructure\Security\JwtTokenProvider;
use Trekly\Auth\Application\RegisterUserUseCase;
use Trekly\Auth\Application\LoginUserUseCase;
use Trekly\Auth\Interface\Http\RegisterController;
use Trekly\Auth\Interface\Http\LoginController;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// Database Connection
$host = $_ENV['DB_HOST'] ?? 'auth-db';
$db   = $_ENV['DB_NAME'] ?? 'auth_db';
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
$userRepository = new PDOUserRepository($pdo);
$tokenProvider = new JwtTokenProvider('secret_key_change_me'); // In prod use env var

// Use Cases
$registerUseCase = new RegisterUserUseCase($userRepository);
$loginUseCase = new LoginUserUseCase($userRepository, $tokenProvider);

// Controllers
$registerController = new RegisterController($registerUseCase);
$loginController = new LoginController($loginUseCase);

// Simple Router
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Remove prefix if behind gateway (e.g. /api/auth)
// Assuming gateway forwards /api/auth/register -> /register or keeps it /api/auth/register
// Let's assume gateway forwards full path. Nginx config: location /api/auth { proxy_pass http://auth-service:80; }
// This means /api/auth/register hits auth-service as /api/auth/register.

if ($method === 'POST' && $uri === '/api/auth/register') {
    $registerController->handle();
} elseif ($method === 'POST' && $uri === '/api/auth/login') {
    $loginController->handle();
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found', 'uri' => $uri]);
}
