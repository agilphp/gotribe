<?php
require_once __DIR__ . '/bootstrap.php';

echo "--- ENV DUMP ---\n";
print_r($_ENV);
echo "----------------\n";

try {
    $entityManager = require __DIR__ . '/api/auth/bootstrap.php';
    $connection = $entityManager->getConnection();
    $connection->connect();
    echo "SUCCESS: Connected to DB.\n";
} catch (Exception $e) {
    echo "FAILURE: " . $e->getMessage() . "\n";
}
