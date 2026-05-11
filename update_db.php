<?php
require_once 'bootstrap.php';
global $ac;

try {
    $ac->exec("ALTER TABLE kesifler ADD COLUMN IF NOT EXISTS formun_bulundugu_kisi VARCHAR(100) DEFAULT NULL AFTER durum");
    $ac->exec("ALTER TABLE kesifler ADD COLUMN IF NOT EXISTS gorseller TEXT DEFAULT NULL AFTER formun_bulundugu_kisi");
    echo "Columns added successfully";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
