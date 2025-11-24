<?php
// This file redirects to public/index.php
// It's needed because Apache serves this when accessing /api/projects
// without the .htaccess working properly

// Simply include the public index, it will handle everything
chdir(__DIR__);
require __DIR__ . '/public/index.php';
