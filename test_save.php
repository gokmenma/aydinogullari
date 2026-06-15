<?php
require_once 'bootstrap.php';
global $ac;
$ac->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $data = [
        'kesif_tarihi' => date('Y-m-d H:i:s'),
        'gidecek_kisi' => 'Antigravity Test',
        'firma' => 'Test Firma',
        'yapilacak_is' => 'Test Is',
        'konum' => 'Test Konum',
        'durum' => 'bekliyor',
        'formun_bulundugu_kisi' => 'Ömer SEÇKİN',
        'kesif_sonu_notu' => 'Test Not',
        'kayit_yapan' => 1
    ];

    $keys = array_keys($data);
    $values = array_values($data);
    $sql = $ac->prepare("INSERT INTO kesifler (" . implode(',', $keys) . ") VALUES (" . str_repeat('?,', count($values) - 1) . "?)");
    $sql->execute($values);
    echo "Success! ID: " . $ac->lastInsertId();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
