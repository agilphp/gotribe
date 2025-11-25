<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

$paths = [__DIR__ . '/src/Domain'];
$isDevMode = true;

$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

$connectionParams = [
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'dbname' => 'tribew_auth',
    'user' => 'tribew_eli4as',
    'password' => '8TK4Nqp8d9SX4uxa',
    'charset' => 'utf8mb4',
];

$entityManager = EntityManager::create($connectionParams, $config);

return $entityManager;
