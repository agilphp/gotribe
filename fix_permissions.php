<?php
/**
 * Script para corregir permisos en cPanel recursivamente
 * Uso: Subir a public_html y ejecutar https://tudominio.com/fix_permissions.php
 */

header('Content-Type: text/plain');

// Directorio base (donde está este script)
$baseDir = __DIR__ . '/api';

if (!is_dir($baseDir)) {
    die("Error: No encuentro la carpeta 'api'. Asegúrate de subir este script a public_html.");
}

echo "Iniciando corrección de permisos en: $baseDir\n\n";

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$dirsCount = 0;
$filesCount = 0;

foreach ($iterator as $item) {
    if ($item->isDir()) {
        // Carpetas a 755
        if (chmod($item->getPathname(), 0755)) {
            // echo "DIR  OK: " . $item->getPathname() . "\n";
            $dirsCount++;
        } else {
            echo "DIR ERR: " . $item->getPathname() . "\n";
        }
    } else {
        // Archivos a 644
        if (chmod($item->getPathname(), 0644)) {
            // echo "FILE OK: " . $item->getPathname() . "\n";
            $filesCount++;
        } else {
            echo "FILE ERR: " . $item->getPathname() . "\n";
        }
    }
}

echo "\n---------------------------------------------------\n";
echo "PROCESO COMPLETADO\n";
echo "Carpetas corregidas (755): $dirsCount\n";
echo "Archivos corregidos (644): $filesCount\n";
echo "---------------------------------------------------\n";
echo "Ahora intenta acceder a tu sitio o API nuevamente.\n";
echo "IMPORTANTE: Borra este archivo (fix_permissions.php) cuando termines por seguridad.\n";
