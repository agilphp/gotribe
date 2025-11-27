<?php
function hasBom($filePath) {
    $handle = fopen($filePath, 'r');
    if (!$handle) return false;
    $bytes = fread($handle, 3);
    fclose($handle);
    return $bytes === "\xEF\xBB\xBF";
}

function scanDirForBom($dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            if (hasBom($file->getPathname())) {
                echo "BOM found in: " . $file->getPathname() . "\n";
            }
        }
    }
}

echo "Scanning for BOM in api/projects...\n";
scanDirForBom(__DIR__ . '/api/projects');
echo "Scanning for BOM in api/bootstrap.php...\n";
if (hasBom(__DIR__ . '/api/bootstrap.php')) {
    echo "BOM found in: " . __DIR__ . '/api/bootstrap.php' . "\n";
}
echo "Done.\n";
