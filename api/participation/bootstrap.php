<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

$paths = [__DIR__ . '/src/Domain'];
$isDevMode = true;
$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

$connectionParams = [
    'driver' => 'pdo_mysql',
    'host' => $_ENV['DB_HOST'] ?? 'participation-db',
    'dbname' => $_ENV['DB_NAME'] ?? 'participation_db',
    'user' => $_ENV['DB_USER'] ?? 'trekly_user',
    'password' => $_ENV['DB_PASS'] ?? 'trekly_pass',
    'charset' => 'utf8mb4',
];

$entityManager = EntityManager::create($connectionParams, $config);
return $entityManager;
