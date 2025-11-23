<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

// Doctrine configuration using PHP 8 Attributes
$paths = [__DIR__ . '/src/Domain'];
$isDevMode = true;

// Create configuration for PHP 8 attributes
$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

// Database connection parameters
$connectionParams = [
    'driver' => 'pdo_mysql',
    'host' => $_ENV['DB_HOST'] ?? 'project-db',
    'dbname' => $_ENV['DB_NAME'] ?? 'project_db',
    'user' => $_ENV['DB_USER'] ?? 'trekly_user',
    'password' => $_ENV['DB_PASS'] ?? 'trekly_pass',
    'charset' => 'utf8mb4',
];

// Create EntityManager
$entityManager = EntityManager::create($connectionParams, $config);

return $entityManager;
