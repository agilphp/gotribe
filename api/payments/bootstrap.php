<?php

// Cargar variables de entorno desde .env
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
            putenv(trim($name) . '=' . trim($value));
        }
    }
}

// Cargar clases manualmente (sin autoload)
// Domain
require_once __DIR__ . '/src/Domain/CuentaPorPagarRepository.php';

// Application
require_once __DIR__ . '/src/Application/CreateCuentaPorPagarUseCase.php';

// Infrastructure
require_once __DIR__ . '/src/Infrastructure/Persistence/PDOCuentaPorPagarRepository.php';

// Interface/Controllers
require_once __DIR__ . '/src/Interface/Http/CuentaPorPagarController.php';
require_once __DIR__ . '/src/Interface/Http/CuentaPorPagarConsultaController.php';
require_once __DIR__ . '/src/Interface/Http/PagoController.php';

// Mercado Pago - Domain
require_once __DIR__ . '/src/Domain/Payment/PaymentRepository.php';
require_once __DIR__ . '/src/Domain/Payment/Payment.php';

// Mercado Pago - Application
require_once __DIR__ . '/src/Application/CreatePaymentPreferenceUseCase.php';
require_once __DIR__ . '/src/Application/ProcessWebhookUseCase.php';

// Mercado Pago - Infrastructure
require_once __DIR__ . '/src/Infrastructure/Payment/MercadoPagoService.php';
require_once __DIR__ . '/src/Infrastructure/Persistence/PDOPaymentRepository.php';
require_once __DIR__ . '/src/Infrastructure/Http/ProjectServiceClient.php';

return null;