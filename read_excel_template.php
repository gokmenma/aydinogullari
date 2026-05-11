<?php
// Direct ZipArchive approach to read Excel
$file = __DIR__ . '/templates/ÖRNEK ALGILAMA RAPORU.xlsx';

$zip = new ZipArchive;
if ($zip->open($file) !== TRUE) {
    die("Cannot open file");
}

// Read shared strings
$sharedStrings = [];
$ss = $zip->getFromName('xl/sharedStrings.xml');
if ($ss) {
    $xml = simplexml_load_string($ss);
    foreach ($xml->si as $si) {
        $text = '';
        if (isset($si->t)) {
            $text = (string)$si->t;
        } elseif (isset($si->r)) {
            foreach ($si->r as $r) {
                $text .= (string)$r->t;
            }
        }
        $sharedStrings[] = $text;
    }
}

// Read workbook to get sheet names
$workbook = $zip->getFromName('xl/workbook.xml');
$wbXml = simplexml_load_string($workbook);
$sheetNames = [];
foreach ($wbXml->sheets->sheet as $sheet) {
    $sheetNames[] = (string)$sheet['name'];
}

echo "=== EXCEL DOSYASI ANALİZİ ===\n";
echo "Sayfa Sayısı: " . count($sheetNames) . "\n";
echo "Sayfa İsimleri: " . implode(", ", $sheetNames) . "\n\n";

// Read each sheet
for ($sheetNum = 1; $sheetNum <= count($sheetNames); $sheetNum++) {
    $sheetXml = $zip->getFromName("xl/worksheets/sheet$sheetNum.xml");
    if (!$sheetXml) continue;
    
    echo "========================================\n";
    echo "SAYFA " . ($sheetNum-1) . ": " . $sheetNames[$sheetNum-1] . "\n";
    echo "========================================\n";
    
    $xml = simplexml_load_string($sheetXml);
    
    // Check merged cells
    if (isset($xml->mergeCells)) {
        echo "Merged cells: ";
        $merges = [];
        foreach ($xml->mergeCells->mergeCell as $mc) {
            $merges[] = (string)$mc['ref'];
        }
        echo implode(", ", $merges) . "\n";
    }
    echo "\n";
    
    if (isset($xml->sheetData)) {
        foreach ($xml->sheetData->row as $row) {
            $rowNum = (string)$row['r'];
            $cells = [];
            foreach ($row->c as $cell) {
                $ref = (string)$cell['r'];
                $value = (string)$cell->v;
                $type = (string)$cell['t'];
                
                // If type is 's', it's a shared string index
                if ($type === 's' && isset($sharedStrings[(int)$value])) {
                    $value = $sharedStrings[(int)$value];
                }
                
                $cells[] = "$ref: $value";
            }
            if (!empty($cells)) {
                echo "Row $rowNum: " . implode(" | ", $cells) . "\n";
            }
        }
    }
    echo "\n\n";
}

$zip->close();
