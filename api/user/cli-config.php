<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

// Replace with file to your own project bootstrap
$entityManager = require_once __DIR__ . '/bootstrap.php';

return ConsoleRunner::createHelperSet($entityManager);
