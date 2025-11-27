<?php
/**
 * Test JWT Configuration and Headers for Participation Service
 * 
 * Upload this file to api/participation/test_jwt.php
 * Access it via browser or Postman: GET /api/participation/test_jwt.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

header('Content-Type: application/json');

$response = [
    'status' => 'info',
    'env_file_exists' => file_exists(__DIR__ . '/.env'),
    'jwt_secret_source' => 'unknown',
    'jwt_secret_length' => 0,
    'headers_received' => [],
    'server_auth_header' => $_SERVER['HTTP_AUTHORIZATION'] ?? 'NOT_SET',
    'redirect_auth_header' => $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? 'NOT_SET',
];

// 1. Load Environment
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
    $response['jwt_secret_source'] = '.env';
} else {
    $response['jwt_secret_source'] = 'default_hardcoded';
}

$secret = $_ENV['JWT_SECRET'] ?? 'gotribe_jwt_secret_change_this_in_production_32chars_minimum';
$response['jwt_secret_length'] = strlen($secret);
$response['jwt_secret_preview'] = substr($secret, 0, 5) . '...'; // Show first 5 chars for verification

// 2. Check Headers
if (function_exists('getallheaders')) {
    $response['headers_received'] = getallheaders();
} else {
    $response['headers_received'] = 'getallheaders() function not available';
}

// 3. Test Token Decoding (if provided in Authorization header)
$authHeader = $response['headers_received']['Authorization'] 
    ?? $_SERVER['HTTP_AUTHORIZATION'] 
    ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] 
    ?? null;

if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
    $jwt = $matches[1];
    $response['token_received'] = true;
    
    try {
        $decoded = JWT::decode($jwt, new Key($secret, 'HS256'));
        $response['token_valid'] = true;
        $response['token_payload'] = $decoded;
    } catch (\Exception $e) {
        $response['token_valid'] = false;
        $response['token_error'] = $e->getMessage();
    }
} else {
    $response['token_received'] = false;
    $response['message'] = 'No Bearer token found in Authorization header. Please send a request with Authorization: Bearer <token>';
}

echo json_encode($response, JSON_PRETTY_PRINT);
