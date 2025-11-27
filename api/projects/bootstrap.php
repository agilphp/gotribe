<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

// Load Composer autoload
$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    throw new Exception("Composer autoload not found at: $autoloadPath");
}
require_once $autoloadPath;

// Environment variables are loaded by the root bootstrap.php

$paths = [__DIR__ . '/src/Domain'];
$isDevMode = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';

$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

$connectionParams = [
    'driver' => 'pdo_mysql',
    'host' => ($_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost'),
    'dbname' => ($_ENV['PROJECTS_DB_DATABASE'] ?? $_SERVER['PROJECTS_DB_DATABASE'] ?? getenv('PROJECTS_DB_DATABASE') ?: 'tribew_projects'),
    'user' => ($_ENV['DB_USERNAME'] ?? $_SERVER['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'root'),
    'password' => ($_ENV['DB_PASSWORD'] ?? $_SERVER['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: ''),
    'charset' => 'utf8mb4',
];

$entityManager = EntityManager::create($connectionParams, $config);

return $entityManager;
