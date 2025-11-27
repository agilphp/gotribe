<?php
$file = 'c:/xampp/htdocs/gotribe/api/projects/bootstrap.php';
$content = file_get_contents($file);
if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
    echo "Removing BOM from $file\n";
    $content = substr($content, 3);
    file_put_contents($file, $content);
    echo "BOM removed.\n";
} else {
    echo "No BOM found in $file\n";
}
