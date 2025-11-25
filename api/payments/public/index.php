<?php

require_once __DIR__ . '/../bootstrap.php';

use Gotribe\Payments\Application\CreateCuentaPorPagarUseCase;
use Gotribe\Payments\Interface\Http\CuentaPorPagarController;
use Gotribe\Payments\Interface\Http\CuentaPorPagarConsultaController;
use Gotribe\Payments\Interface\Http\PagoController;
use Gotribe\Payments\Infrastructure\Persistence\PDOCuentaPorPagarRepository;

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
    
    // Get request details
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = rtrim($uri, '/');
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Health check endpoint
    if ($method === 'GET' && $uri === '/api/payments/health') {
        echo json_encode(['status' => 'ok', 'service' => 'payments-service']);
        exit;
    }
    
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
