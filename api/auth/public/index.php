<?php

require_once __DIR__ . '/../bootstrap.php';

use Trekly\Auth\Infrastructure\Persistence\DoctrineUserRepository;
use Trekly\Auth\Infrastructure\Security\JwtTokenProvider;
use Trekly\Auth\Application\RegisterUserUseCase;
use Trekly\Auth\Application\LoginUserUseCase;
use Trekly\Auth\Interface\Http\RegisterController;
use Trekly\Auth\Interface\Http\LoginController;
use Trekly\Auth\Infrastructure\Security\GoogleOAuthService;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $entityManager = require __DIR__ . '/../bootstrap.php';
    
    $userRepository = new DoctrineUserRepository($entityManager);
    $tokenProvider = new JwtTokenProvider($_ENV['JWT_SECRET'] ?? 'secret_key_change_me');
    $emailService = new Trekly\Auth\Infrastructure\Email\EmailService();
    $googleOAuthService = new GoogleOAuthService($userRepository, $tokenProvider);
    
    $registerUseCase = new RegisterUserUseCase($userRepository, $emailService);
    $loginUseCase = new LoginUserUseCase($userRepository, $tokenProvider);
    
    $registerController = new RegisterController($registerUseCase);
    $loginController = new LoginController($loginUseCase);
    
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'GET' && $uri === '/api/auth/health') {
        echo json_encode(['status' => 'ok', 'service' => 'auth-service']);
        exit;
    }
    
    if ($method === 'POST' && $uri === '/api/auth/register') {
        $registerController->handle();
    } elseif ($method === 'POST' && $uri === '/api/auth/login') {
        $loginController->handle();
    } elseif ($method === 'GET' && $uri === '/api/auth/google') {
        // Redirect to Google OAuth consent screen
        $authUrl = $googleOAuthService->getAuthUrl();
        header('Location: ' . $authUrl);
        exit;
    } elseif ($method === 'GET' && preg_match('#^/api/auth/google/callback$#', $uri)) {
        // Handle Google callback with ?code=...
        $code = $_GET['code'] ?? null;
        if (!$code) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing code parameter']);
            exit;
        }
        try {
            $result = $googleOAuthService->handleCallback($code);
            echo json_encode(['token' => $result['token'], 'user' => $result['user']]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    } elseif ($method === 'GET' && preg_match('#^/api/auth/users/([^/]+)$#', $uri, $matches)) {
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
