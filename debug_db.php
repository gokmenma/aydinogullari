<?php
require_once 'bootstrap.php';
global $ac;

echo "=== REPORT TYPES ===\n";
$query = $ac->prepare("SELECT * FROM report_types");
$query->execute();
print_r($query->fetchAll(PDO::FETCH_ASSOC));

echo "\n=== REPORT QUESTIONS STRUCTURE ===\n";
$query = $ac->prepare("DESCRIBE report_questions");
$query->execute();
print_r($query->fetchAll(PDO::FETCH_ASSOC));

echo "\n=== REPORTS TABLE STRUCTURE ===\n";
$query = $ac->prepare("DESCRIBE reports");
$query->execute();
print_r($query->fetchAll(PDO::FETCH_ASSOC));
?>
