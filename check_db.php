<?php
require_once 'bootstrap.php';
global $ac;
$query = $ac->query("DESCRIBE kesifler");
$columns = $query->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($columns, JSON_PRETTY_PRINT);
