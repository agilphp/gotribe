<?php

require_once __DIR__ . '/../bootstrap.php';

use Trekly\Auth\Infrastructure\Persistence\DoctrineUserRepository;
use Trekly\Auth\Infrastructure\Security\JwtTokenProvider;
use Trekly\Auth\Application\RegisterUserUseCase;
use Trekly\Auth\Application\LoginUserUseCase;
use Trekly\Auth\Interface\Http\RegisterController;
use Trekly\Auth\Interface\Http\LoginController;

// CORS headers are handled by root bootstrap.php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $entityManager = require __DIR__ . '/../bootstrap.php';
    
    $userRepository = new DoctrineUserRepository($entityManager);
    $tokenProvider = new JwtTokenProvider($_ENV['JWT_SECRET'] ?? 'gotribe_jwt_secret_change_this_in_production_32chars_minimum');
    $emailService = new Trekly\Auth\Infrastructure\Email\EmailService();
    
    $registerUseCase = new RegisterUserUseCase($userRepository, $emailService);
    $loginUseCase = new LoginUserUseCase($userRepository, $tokenProvider);
    
    $registerController = new RegisterController($registerUseCase);
    $loginController = new LoginController($loginUseCase);
    
    // Normalize URI for routing (remove query string)
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Simple router that allows for subdirectories (e.g. /gotribe/api/...)
    if ($method === 'GET' && preg_match('#/api/auth/health$#', $uri)) {
        echo json_encode(['status' => 'ok', 'service' => 'auth-service']);
        exit;
    }
    
    if ($method === 'POST' && preg_match('#/api/auth/register$#', $uri)) {
        $registerController->handle();
    } elseif ($method === 'POST' && preg_match('#/api/auth/login$#', $uri)) {
        $loginController->handle();
    } elseif ($method === 'GET' && preg_match('#/api/auth/users/([^/]+)$#', $uri, $matches)) {
        // Simple endpoint to get user by ID (for email notifications)
        $userId = $matches[1];
        $user = $userRepository->findById($userId);
        
        if ($user) {
            echo json_encode([
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'role' => $user->getRole()->value
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not Found', 'uri' => $uri]);
    }
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
