<?php
require_once 'bootstrap.php';
global $ac;
$ac->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $id = 15; // From the screenshot
    $data = [
        'id' => $id,
        'durum' => 'kesif_tamamlandi',
        'formun_bulundugu_kisi' => 'BERK CEYLAN',
        'gorseller' => json_encode(['uploads/kesif/test.jpg'])
    ];

    $keys = array_keys($data);
    $values = array_values($data);

    $set_parts = [];
    foreach ($keys as $key) {
        $set_parts[] = "`$key` = ?";
    }

    $sql_str = "UPDATE kesifler SET " . implode(', ', $set_parts) . " WHERE id = ?";
    echo "SQL: " . $sql_str . "\n";

    $values[] = $id;
    echo "Values: " . print_r($values, true) . "\n";

    $stmt = $ac->prepare($sql_str);
    $stmt->execute($values);
    echo "Update successful. Rows affected: " . $stmt->rowCount() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
