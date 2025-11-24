<?php
/**
 * GoTribe API Router
 * Central orchestrator for all API requests
 * Routes requests to the appropriate service
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to user
ini_set('log_errors', 1);

// Set JSON header
header('Content-Type: application/json');

// --- FIX AUTHORIZATION HEADER FOR FASTCGI/CPANEL ---
if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    } elseif (isset($_ENV['HTTP_AUTHORIZATION'])) {
        $_SERVER['HTTP_AUTHORIZATION'] = $_ENV['HTTP_AUTHORIZATION'];
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        if (isset($requestHeaders['Authorization'])) {
            $_SERVER['HTTP_AUTHORIZATION'] = $requestHeaders['Authorization'];
        }
    }
}
// ---------------------------------------------------

// Get the request URI and method
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Parse the URI to extract the service and path
$uri = parse_url($requestUri, PHP_URL_PATH);

// Log the request for debugging
error_log("API Router - URI: $uri, Method: $requestMethod");

// Extract service from URI: /api/{service}/...
if (preg_match('#^/api/([^/]+)(/.*)?$#', $uri, $matches)) {
    $service = $matches[1];
    $servicePath = $matches[2] ?? '/';
    
    // Map service names to directories
    $serviceMap = [
        'auth' => __DIR__ . '/auth/public/index.php',
        'users' => __DIR__ . '/users/public/index.php',
        'projects' => __DIR__ . '/projects/public/index.php',
        'participations' => __DIR__ . '/participations/public/index.php',
    ];
    
    // Check if service exists
    if (isset($serviceMap[$service]) && file_exists($serviceMap[$service])) {
        // Change to service directory
        $serviceDir = dirname(dirname($serviceMap[$service]));
        chdir($serviceDir);
        
        // Log service routing
        error_log("API Router - Routing to service: $service, File: {$serviceMap[$service]}");
        
        // Include the service's index.php
        require $serviceMap[$service];
        exit;
    } else {
        // Service not found
        http_response_code(404);
        echo json_encode([
            'error' => 'Service not found',
            'service' => $service,
            'checked_path' => $serviceMap[$service] ?? 'N/A',
            'available_services' => array_keys($serviceMap)
        ]);
        exit;
    }
} else {
    // Invalid API path
    http_response_code(404);
    echo json_encode([
        'error' => 'Invalid API path',
        'uri' => $uri,
        'expected_format' => '/api/{service}/{endpoint}'
    ]);
    exit;
}
