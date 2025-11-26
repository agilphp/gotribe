<?php

require_once __DIR__ . '/../bootstrap.php';

use Gotribe\Payments\Application\CreateCuentaPorPagarUseCase;
use Gotribe\Payments\Interface\Http\CuentaPorPagarController;
use Gotribe\Payments\Interface\Http\CuentaPorPagarConsultaController;
use Gotribe\Payments\Interface\Http\PagoController;
use Gotribe\Payments\Infrastructure\Persistence\PDOCuentaPorPagarRepository;
use Trekly\Payments\Infrastructure\Payment\MercadoPagoService;
use Trekly\Payments\Infrastructure\Persistence\DoctrinePaymentRepository;
use Trekly\Payments\Infrastructure\Http\ProjectServiceClient;
use Trekly\Payments\Application\CreatePaymentPreferenceUseCase;
use Trekly\Payments\Application\ProcessWebhookUseCase;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Get database connection from bootstrap
    $entityManager = require __DIR__ . '/../bootstrap.php';
    
    // Create PDO connection for repository
    $pdo = new PDO(
        sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4',
            $_ENV['DB_HOST'] ?? 'localhost',
            $_ENV['DB_NAME'] ?? 'tribew_projects'
        ),
        $_ENV['DB_USER'] ?? 'tribew_eli4as',
        $_ENV['DB_PASS'] ?? '8TK4Nqp8d9SX4uxa',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Initialize repository
    $repository = new PDOCuentaPorPagarRepository($pdo);
    
    // Initialize use cases and controllers
    $createUseCase = new CreateCuentaPorPagarUseCase($repository);
    $cuentaController = new CuentaPorPagarController($createUseCase);
    $consultaController = new CuentaPorPagarConsultaController($repository);
    $pagoController = new PagoController($repository);
    
    // Initialize Mercado Pago services
    $mercadoPagoService = new MercadoPagoService();
    $paymentRepository = new DoctrinePaymentRepository($entityManager);
    $projectServiceClient = new ProjectServiceClient();
    
    $createPreferenceUseCase = new CreatePaymentPreferenceUseCase(
        $paymentRepository,
        $mercadoPagoService
    );
    $processWebhookUseCase = new ProcessWebhookUseCase(
        $paymentRepository,
        $mercadoPagoService,
        $projectServiceClient
    );
    
    // Get request details
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = rtrim($uri, '/');
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Health check endpoint
    if ($method === 'GET' && $uri === '/api/payments/health') {
        echo json_encode(['status' => 'ok', 'service' => 'payments-service']);
        exit;
    }
    
    // ===== MERCADO PAGO ROUTES =====
    
    // Route: POST /api/payments/create-preference - Create payment preference
    if ($method === 'POST' && $uri === '/api/payments/create-preference') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['project_id']) || !isset($input['project_budget']) || !isset($input['user_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: project_id, project_budget, user_id']);
            exit;
        }
        
        try {
            $result = $createPreferenceUseCase->execute(
                $input['project_id'],
                (float)$input['project_budget'],
                $input['user_id'],
                $input['currency'] ?? 'COP'
            );
            
            http_response_code(201);
            echo json_encode($result);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
    
    // Route: POST /api/payments/webhook - Handle Mercado Pago webhook
    if ($method === 'POST' && $uri === '/api/payments/webhook') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        try {
            $result = $processWebhookUseCase->execute($input);
            http_response_code(200);
            echo json_encode($result);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
    
    // Route: GET /api/payments/{id}/status - Get payment status
    if ($method === 'GET' && preg_match('#^/api/payments/([^/]+)/status$#', $uri, $matches)) {
        $paymentId = $matches[1];
        
        try {
            $payment = $paymentRepository->findById($paymentId);
            
            if (!$payment) {
                http_response_code(404);
                echo json_encode(['error' => 'Payment not found']);
                exit;
            }
            
            echo json_encode([
                'payment_id' => $payment->getId(),
                'status' => $payment->getStatus(),
                'amount' => $payment->getAmount(),
                'currency' => $payment->getCurrency(),
                'created_at' => $payment->getCreatedAt()->format('Y-m-d H:i:s'),
                'paid_at' => $payment->getPaidAt()?->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
    
    // Route: GET /api/payments/config - Get Mercado Pago public key for frontend
    if ($method === 'GET' && $uri === '/api/payments/config') {
        echo json_encode([
            'public_key' => $mercadoPagoService->getPublicKey()
        ]);
        exit;
    }
    
    // ===== EXISTING ROUTES =====
    
    // Route: POST /api/payments/cuentas - Create cuenta por pagar
    if ($method === 'POST' && $uri === '/api/payments/cuentas') {
        $cuentaController->createCuentaPorPagar();
        exit;
    }
    
    // Route: GET /api/payments/cuentas/creator/{creatorId} - Get cuentas by creator
    if ($method === 'GET' && preg_match('#^/api/payments/cuentas/creator/([^/]+)$#', $uri, $matches)) {
        $creatorId = $matches[1];
        $consultaController->getCuentasPorCreator($creatorId);
        exit;
    }
    
    // Route: POST /api/payments/pagos - Register payment
    if ($method === 'POST' && $uri === '/api/payments/pagos') {
        $pagoController->registrarPago();
        exit;
    }
    
    // Route: POST /api/payments/vencidas - Mark overdue accounts (cron job)
    if ($method === 'POST' && $uri === '/api/payments/vencidas') {
        $pagoController->marcarCuentasVencidas();
        http_response_code(200);
        echo json_encode(['success' => true]);
        exit;
    }
    
    // No route matched
    http_response_code(404);
    echo json_encode(['error' => 'Not Found', 'uri' => $uri, 'method' => $method]);
    
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal Server Error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
