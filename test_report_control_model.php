<?php
// Quick smoke test: ensures ReportControlModel can be constructed and can prepare a query.
// Note: this will execute a DB query against configs/config.php connection.

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Model\ReportControlModel;

$month = '12';
$year = '2025';

$model = new ReportControlModel();

try {
    $rows = $model->getReportControlList($month, $year);
    header('Content-Type: text/plain; charset=utf-8');
    echo "OK - fetched " . count($rows) . " rows\n";
} catch (Throwable $e) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "FAIL: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
