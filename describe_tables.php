<?php
require_once 'bootstrap.php';
global $ac;

echo "=== REPORT_CONTENTS TABLE ===\n";
$q = $ac->prepare("DESCRIBE report_contents");
$q->execute();
print_r($q->fetchAll(PDO::FETCH_ASSOC));

echo "\n=== CUSTOMERS TABLE ===\n";
$q = $ac->prepare("DESCRIBE customers");
$q->execute();
print_r($q->fetchAll(PDO::FETCH_ASSOC));

echo "\n=== USERS TABLE (important columns) ===\n";
$q = $ac->prepare("DESCRIBE users");
$q->execute();
print_r($q->fetchAll(PDO::FETCH_ASSOC));

echo "\n=== REPORT QUESTIONS DATA ===\n";
$q = $ac->prepare("SELECT * FROM report_questions");
$q->execute();
print_r($q->fetchAll(PDO::FETCH_ASSOC));
?>
