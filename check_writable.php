<?php
require_once 'bootstrap.php';
$dir = dirname(__DIR__, 3) . '/uploads/kesif/';
echo "Dir: " . $dir . "\n";
echo "Exists: " . (is_dir($dir) ? 'Yes' : 'No') . "\n";
echo "Writable: " . (is_writable($dir) ? 'Yes' : 'No') . "\n";
?>