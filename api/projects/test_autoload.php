<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Diagnóstico de Autoload</h2>";

$bootstrapPath = __DIR__ . '/bootstrap.php';
echo "Buscando bootstrap en: $bootstrapPath<br>";

if (!file_exists($bootstrapPath)) {
    die("❌ ERROR: No existe el archivo bootstrap.php");
}

echo "✅ bootstrap.php existe.<br>";

try {
    require_once $bootstrapPath;
    echo "✅ bootstrap.php cargado correctamente.<br>";
} catch (Throwable $e) {
    die("❌ ERROR al cargar bootstrap: " . $e->getMessage());
}

echo "<h3>Verificando librerías:</h3>";

// Verificar Vendor
$vendorPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($vendorPath)) {
    echo "✅ vendor/autoload.php existe.<br>";
} else {
    echo "❌ vendor/autoload.php NO existe. (Debes subir la carpeta vendor)<br>";
}

// Verificar Clase QR
if (class_exists('Endroid\QrCode\Builder\Builder')) {
    echo "✅ Clase QR (Endroid) encontrada.<br>";
} else {
    echo "❌ Clase QR (Endroid) NO encontrada. (El autoload no está funcionando)<br>";
}

// Verificar Clase Dompdf
if (class_exists('Dompdf\Dompdf')) {
    echo "✅ Clase Dompdf encontrada.<br>";
} else {
    echo "❌ Clase Dompdf NO encontrada.<br>";
}
