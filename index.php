<?php

/**
 * GoTribe Root Entrypoint
 */

require_once __DIR__ . '/bootstrap.php';

$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
file_put_contents(__DIR__ . '/debug_router.log', "URI: $requestUri\nPath: $path\n", FILE_APPEND);

// Simple Router
if (strpos($path, '/api') === 0) {
    // API Routes
    // Forward to existing API router for now, but we can refactor this later
    // We need to make sure the API router knows it's being included
    
    // Adjust path for the API router if needed, or just include it
    // The existing api/index.php expects to handle /api/...
    
    require __DIR__ . '/api/index.php';
    exit;
}

// Frontend Routes
// Serve static files if they exist
$frontendDir = __DIR__ . '/front/dist/trekly-front/browser';
$filePath = $frontendDir . $path;

// Remove /gotribe prefix if present in path for file lookup
// (Assuming the app is served at /gotribe, but the files are relative to root)
// Actually, $path from parse_url includes /gotribe. We need to strip it to find the file in browser dir?
// No, if the request is /gotribe/styles.css, we want front/dist/.../styles.css
// But if the base href is /gotribe/, the browser asks for /gotribe/styles.css.
// So we need to map /gotribe/X to front/dist/.../X.

$basePath = '/gotribe'; // Should be dynamic or from env, but hardcoded for now based on context
if (strpos($path, $basePath) === 0) {
    $relativePath = substr($path, strlen($basePath));
} else {
    $relativePath = $path;
}

$fileToServe = $frontendDir . $relativePath;

if (is_file($fileToServe)) {
    $ext = pathinfo($fileToServe, PATHINFO_EXTENSION);
    $mimeTypes = [
        'html' => 'text/html',
        'js'   => 'application/javascript',
        'css'  => 'text/css',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'json' => 'application/json',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf'
    ];
    
    $contentType = $mimeTypes[$ext] ?? 'application/octet-stream';
    header("Content-Type: $contentType");
    readfile($fileToServe);
    exit;
}

// SPA Fallback: Serve index.html for any other route (except /api)
if (strpos($path, '/api') !== 0) {
    if (file_exists($frontendDir . '/index.html')) {
        header('Content-Type: text/html');
        // We might need to inject base href here if it's not correct in the file
        // But let's assume the build or runtime handles it.
        // Actually, let's inject it dynamically to be safe.
        $content = file_get_contents($frontendDir . '/index.html');
        // Replace <base href="/"> with <base href="/gotribe/">
        $content = str_replace('<base href="/">', '<base href="/gotribe/">', $content);
        echo $content;
        exit;
    } else {
        echo "Frontend build not found. Please run 'npm run build' in /front.";
        exit;
    }
}

// 404 Not Found
http_response_code(404);
echo json_encode(['error' => 'Not Found', 'path' => $path]);
