<?php
require_once 'bootstrap.php';
$path = dirname(__FILE__) . '/uploads/kesif/';
echo "Realpath: " . realpath(dirname(__FILE__)) . "/uploads/kesif/\n";
echo "Dir exists: " . (is_dir($path) ? 'Yes' : 'No') . "\n";
if (!is_dir($path)) {
    echo "Creating dir...\n";
    mkdir($path, 0777, true);
    echo "Dir created: " . (is_dir($path) ? 'Yes' : 'No') . "\n";
}
echo "Writable: " . (is_writable($path) ? 'Yes' : 'No') . "\n";
?>