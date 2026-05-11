<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';

function distinct($ac, $sql) {
    $st = $ac->prepare($sql);
    $st->execute();
    $rows = $st->fetchAll(PDO::FETCH_COLUMN);
    return array_values(array_filter(array_map(function($v){ return is_string($v) ? trim($v) : ($v===null ? '' : (string)$v); }, $rows), function($v){ return $v !== ''; }));
}

$resp = [
    'company_name'      => distinct($ac, "SELECT DISTINCT company_name FROM view_offers WHERE is_template=0 AND company_name IS NOT NULL AND company_name!='' ORDER BY company_name"),
    'company_authors'   => distinct($ac, "SELECT DISTINCT company_authors FROM view_offers WHERE is_template=0 AND company_authors IS NOT NULL AND company_authors!='' ORDER BY company_authors"),
    'creator_name'      => distinct($ac, "SELECT DISTINCT creator_name FROM view_offers WHERE is_template=0 AND creator_name IS NOT NULL AND creator_name!='' ORDER BY creator_name"),
    'durum'             => distinct($ac, "SELECT DISTINCT durum FROM view_offers WHERE is_template=0 ORDER BY durum"),
    'stok_kodu'         => distinct($ac, "SELECT DISTINCT om.stokKodu FROM offermatters om JOIN offers o ON om.oid=o.id WHERE o.is_template=0 AND om.stokKodu!='' ORDER BY om.stokKodu"),
    'salecur'           => distinct($ac, "SELECT DISTINCT salecur FROM offermatters om JOIN offers o ON om.oid=o.id WHERE o.is_template=0 ORDER BY salecur"),
];

header('Content-Type: application/json');
echo json_encode($resp);
exit;
