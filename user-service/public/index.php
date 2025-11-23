<?php

require_once __DIR__ . '/../bootstrap.php';

use Trekly\User\Infrastructure\Persistence\DoctrineUserProfileRepository;
use Trekly\User\Application\GetUserProfileUseCase;
use Trekly\User\Application\UpdateUserProfileUseCase;
use Trekly\User\Application\CreateCreatorProfileUseCase;
use Trekly\User\Interface\Http\ProfileController;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $entityManager = require __DIR__ . '/../bootstrap.php';
    
    $repository = new DoctrineUserProfileRepository($entityManager);
    
    $getUserProfileUseCase = new GetUserProfileUseCase($repository);
    $updateUserProfileUseCase = new UpdateUserProfileUseCase($repository);
    $createCreatorProfileUseCase = new CreateCreatorProfileUseCase($repository);
    
    $controller = new ProfileController(
        $getUserProfileUseCase,
        $updateUserProfileUseCase,
        $createCreatorProfileUseCase
    );
    
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    
    if (preg_match('#^/api/users/([^/]+)$#', $uri, $matches)) {
        $userId = $matches[1];
        if ($method === 'GET') {
            $controller->getProfile($userId);
        } elseif ($method === 'PUT') {
            $controller->updateProfile($userId);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
    } elseif (preg_match('#^/api/users/([^/]+)/creator-profile$#', $uri, $matches)) {
        $userId = $matches[1];
        if ($method === 'POST') {
            $controller->createCreatorProfile($userId);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not Found', 'uri' => $uri]);
    }
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
