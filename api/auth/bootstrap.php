<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once __DIR__ . '/vendor/autoload.php';

// Environment variables are loaded by the root bootstrap.php

$paths = [__DIR__ . '/src/Domain'];
$isDevMode = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';

$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

$connectionParams = [
    'driver' => 'pdo_mysql',
    'host' => ($_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost'),
    'dbname' => ($_ENV['AUTH_DB_DATABASE'] ?? $_SERVER['AUTH_DB_DATABASE'] ?? getenv('AUTH_DB_DATABASE') ?: 'tribew_auth'),
    'user' => ($_ENV['DB_USERNAME'] ?? $_SERVER['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'root'),
    'password' => ($_ENV['DB_PASSWORD'] ?? $_SERVER['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: ''),
    'charset' => 'utf8mb4',
];

$entityManager = EntityManager::create($connectionParams, $config);

return $entityManager;