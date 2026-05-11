<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$base_table = 'view_offers';

function ddmmyyyy_to_sql($s){
    $s = trim((string)$s);
    if ($s === '') return '';
    $s = str_replace(['/', '-'], '.', $s);
    $parts = explode('.', $s);
    if (count($parts) === 3) {
        $d = (int)$parts[0];
        $m = (int)$parts[1];
        $y = (int)$parts[2];
        if ($y > 0 && $m > 0 && $d > 0) {
            return sprintf('%04d-%02d-%02d', $y, $m, $d);
        }
    }
    return '';
}

$where_conditions = [];
$params = [];

// Sablon filtresi
$sablonlari_goster = isset($_GET['sablon']) && $_GET['sablon'] == '1';
if ($sablonlari_goster) {
    $where_conditions[] = 'is_template = 1';
} else {
    $where_conditions[] = 'is_template = 0';
}

// Form filtreleri
$filters = $_POST['filters'] ?? $_GET['filters'] ?? [];
if (is_string($filters)) {
    $decoded = json_decode($filters, true);
    if (is_array($decoded)) { $filters = $decoded; }
}
if (!empty($filters)) {
    if (!empty($filters['offer_no'])) { $where_conditions[] = 'offerNumber LIKE ?'; $params[] = '%' . $filters['offer_no'] . '%'; }
    if (!empty($filters['company'])) { $where_conditions[] = 'company_name LIKE ?'; $params[] = '%' . $filters['company'] . '%'; }
    if (!empty($filters['subject'])) { $where_conditions[] = 'offer_subject LIKE ?'; $params[] = '%' . $filters['subject'] . '%'; }
    if (!empty($filters['creator'])) { $where_conditions[] = 'creator_name LIKE ?'; $params[] = '%' . $filters['creator'] . '%'; }
    if (!empty($filters['payment_period'])) { $where_conditions[] = 'payment_period LIKE ?'; $params[] = '%' . $filters['payment_period'] . '%'; }
    if (!empty($filters['status'])) { $where_conditions[] = 'durum LIKE ?'; $params[] = '%' . $filters['status'] . '%'; }
    if (!empty($filters['currency'])) { $where_conditions[] = 'currency = ?'; $params[] = $filters['currency']; }
    $date_start = ddmmyyyy_to_sql($filters['date_start'] ?? '');
    $date_end   = ddmmyyyy_to_sql($filters['date_end'] ?? '');
    if (!empty($date_start) && !empty($date_end)) { $where_conditions[] = 'DATE(created_at) BETWEEN ? AND ?'; $params[] = $date_start; $params[] = $date_end; }
    elseif (!empty($date_start)) { $where_conditions[] = 'DATE(created_at) >= ?'; $params[] = $date_start; }
    elseif (!empty($date_end)) { $where_conditions[] = 'DATE(created_at) <= ?'; $params[] = $date_end; }
    $total_min = $filters['total_min'] ?? '';
    $total_max = $filters['total_max'] ?? '';
    if ($total_min !== '' && $total_min !== null) { $where_conditions[] = 'total_price >= ?'; $params[] = $total_min; }
    if ($total_max !== '' && $total_max !== null) { $where_conditions[] = 'total_price <= ?'; $params[] = $total_max; }
}

$where_clause = '';
if (!empty($where_conditions)) { $where_clause = ' WHERE ' . implode(' AND ', $where_conditions); }

$sql = "SELECT id, created_at, offerNumber, company_name, total_price, currency, durum, onay_tarihi, offer_subject, payment_period, creator_name FROM $base_table $where_clause ORDER BY created_at DESC";
$st = $ac->prepare($sql);
foreach ($params as $i => $p) { $st->bindValue($i+1, $p); }
$st->execute();
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

$sheet = new Spreadsheet();
$ws = $sheet->getActiveSheet();
$ws->setTitle('Teklifler');

// Başlıklar
$headers = ['Oluşturma Tarihi', 'Teklif No', 'Müşteri', 'Toplam Tutar', 'Para Birimi', 'Durum', 'Onay Tarihi', 'Konusu', 'Ödeme Vadesi', 'Teklif Veren'];
foreach ($headers as $c => $h) {
    $col = Coordinate::stringFromColumnIndex($c+1);
    $ws->setCellValue($col . '1', $h);
}

// Satırlar
$r = 2;
foreach ($rows as $row) {
    $ws->setCellValue(Coordinate::stringFromColumnIndex(1) . $r, !empty($row['created_at']) ? (new DateTime($row['created_at']))->format('d.m.Y H:i') : '');
    $ws->setCellValue(Coordinate::stringFromColumnIndex(2) . $r, $row['offerNumber']);
    $ws->setCellValue(Coordinate::stringFromColumnIndex(3) . $r, $row['company_name']);
    $ws->setCellValue(Coordinate::stringFromColumnIndex(4) . $r, $row['total_price']);
    $ws->setCellValue(Coordinate::stringFromColumnIndex(5) . $r, $row['currency']);
    $ws->setCellValue(Coordinate::stringFromColumnIndex(6) . $r, $row['durum']);
    $ws->setCellValue(Coordinate::stringFromColumnIndex(7) . $r, $row['onay_tarihi']);
    $ws->setCellValue(Coordinate::stringFromColumnIndex(8) . $r, $row['offer_subject']);
    $ws->setCellValue(Coordinate::stringFromColumnIndex(9) . $r, $row['payment_period']);
    $ws->setCellValue(Coordinate::stringFromColumnIndex(10) . $r, $row['creator_name']);
    $r++;
}

// Otomatik genişlik
for ($i=1; $i<=count($headers); $i++) {
    $ws->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
}

$filename = 'teklifler_' . date('Ymd_His') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($sheet);
$writer->save('php://output');
exit;
