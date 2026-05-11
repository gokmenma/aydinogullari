<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';

$base = 'view_offers';
$where = [];
$params = [];

$sablonlari_goster = isset($_GET['sablon']) && $_GET['sablon'] == '1';
if ($sablonlari_goster) {
    $where[] = 'is_template = 1';
} else {
    $where[] = 'is_template = 0';
}

$wc = '';
if (!empty($where)) {
    $wc = ' WHERE ' . implode(' AND ', $where);
}

function distinct($ac, $base, $wc, $col) {
    $sql = "SELECT DISTINCT $col FROM $base $wc ORDER BY $col ASC";
    $st = $ac->prepare($sql);
    $st->execute();
    $rows = $st->fetchAll(PDO::FETCH_COLUMN);
    // temizle
    return array_values(array_filter(array_map(function($v){ return is_string($v) ? trim($v) : ($v===null ? '' : (string)$v); }, $rows), function($v){ return $v !== ''; }));
}

$resp = [
    'company_name'   => distinct($ac, $base, $wc, 'company_name'),
    'offer_subject'  => distinct($ac, $base, $wc, 'offer_subject'),
    'durum'          => distinct($ac, $base, $wc, 'durum'),
    'creator_name'   => distinct($ac, $base, $wc, 'creator_name'),
    'payment_period' => distinct($ac, $base, $wc, 'payment_period'),
    'currency'       => distinct($ac, $base, $wc, 'currency'),
];

header('Content-Type: application/json');
echo json_encode($resp);
exit;
