<?php

require_once __DIR__ . '/../bootstrap.php';

use Trekly\Participation\Infrastructure\Persistence\DoctrineParticipationRepository;
use Trekly\Participation\Application\RequestParticipationUseCase;
use Trekly\Participation\Application\UpdateParticipationStatusUseCase;
use Trekly\Participation\Interface\Http\ParticipationController;
use Trekly\Participation\Infrastructure\Http\ProjectServiceClient;
use Trekly\Participation\Infrastructure\Http\AuthServiceClient;
use Trekly\Participation\Infrastructure\Http\PaymentServiceClient;
use Trekly\Participation\Infrastructure\Email\EmailService;
use Trekly\Participation\Infrastructure\Services\TicketService;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $entityManager = require __DIR__ . '/../bootstrap.php';
    
    $repository = new DoctrineParticipationRepository($entityManager);
    $projectClient = new ProjectServiceClient();
    $authClient = new AuthServiceClient();
    $paymentClient = new PaymentServiceClient();
    $emailService = new EmailService();
    $ticketService = new TicketService();
    
    $requestUseCase = new RequestParticipationUseCase($repository, $projectClient, $authClient, $emailService, $ticketService);
    $updateStatusUseCase = new UpdateParticipationStatusUseCase($repository);
    $validateUseCase = new \Trekly\Participation\Application\ValidateParticipationUseCase($repository, $projectClient, $paymentClient);
    
    $controller = new ParticipationController($requestUseCase, $updateStatusUseCase, $validateUseCase, $repository);
    
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($uri === '/api/participations') {
        if ($method === 'POST') {
            $controller->request();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
    } elseif (preg_match('#^/api/participations/user/([^/]+)$#', $uri, $matches)) {
        $userId = $matches[1];
        if ($method === 'GET') {
            $controller->getUserParticipations($userId);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
    } elseif (preg_match('#^/api/participations/([^/]+)/status$#', $uri, $matches)) {
        $id = $matches[1];
        if ($method === 'PUT') {
            $controller->updateStatus($id);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
    } elseif ($uri === '/api/participations/validate' && $method === 'POST') {
        $controller->validate();
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not Found', 'uri' => $uri]);
    }
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
