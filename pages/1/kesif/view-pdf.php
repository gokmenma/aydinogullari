<?php
require_once dirname(__DIR__, 3) . '/bootstrap.php';

use App\Helper\Security;
use App\Model\KesifModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

permcontrol('kesifView');

$encId = $_GET['id'] ?? '';
if (!$encId) {
    http_response_code(400);
    exit;
}

try {
    $id = Security::decrypt($encId);
} catch (Exception $e) {
    http_response_code(400);
    exit;
}

$kesifObj = new KesifModel();
$kesif = $kesifObj->findActive($id);
if (!$kesif) {
    http_response_code(404);
    exit;
}

$title = 'KEŞİF FORMU';

function toBase64($path) {
    $data = base64_encode(file_get_contents($path));
    return 'data:' . mime_content_type($path) . ';base64,' . $data;
}

$logoPath = $_SERVER['DOCUMENT_ROOT'] . '/src/images/logo.png';
$logoData = file_exists($logoPath) ? toBase64($logoPath) : '';

$address = $kesif->konum ?? '';
$mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($address);
$qrDataUri = '';
try {
    $qrCode = QrCode::create($mapsUrl)->setSize(180)->setMargin(10);
    $writer = new PngWriter();
    $qrDataUri = $writer->write($qrCode)->getDataUri();
} catch (\Exception $e) {
    $qrDataUri = '';
}

$html = '<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . $title . '</title>
    <style>
        body { font-family: Dejavu Sans, sans-serif; margin:0; }
        @page { margin: 25px; margin-bottom: 40px; }
        .header { border-bottom:1px solid #ccc; padding:8px 0; }
        .header:after { content:""; display:block; clear:both; }
        .logo { float:left; }
        .logo img { height:48px; }
        .title { text-align:center; font-size:16px; font-weight:700; }
        .section-title { background:#eee; color:#000; padding:6px; font-size:12px; margin-top:12px; }
        table { width:100%; border-collapse:collapse; }
        td { border:0.5px solid #808080; padding:8px; font-size:11px; }
        .qr { text-align:center; }
        .qr img { width:140px; height:140px; }
        footer { position: fixed; bottom: 0; left: 0; right: 0; border-top: 1px solid #ccc; padding: 6px; font-size: 10px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">' . ($logoData ? ('<img src="' . $logoData . '" alt="logo">') : '') . '</div>
        <div class="title">KEŞİF FORMU</div>
    </div>

    <div class="section-title">Keşif Bilgileri</div>
    <table>
        <tbody>
            <tr>
                <td>Keşif Tarihi</td>
                <td>' . ($kesif->kesif_tarihi) . '</td>
            </tr>
            <tr>
                <td>Firma</td>
                <td>' . htmlspecialchars($kesif->firma) . '</td>
            </tr>
            <tr>
                <td>Keşife Gidecek Kişi</td>
                <td>' . htmlspecialchars($kesif->gidecek_kisi ?? '-') . '</td>
            </tr>
            <tr>
                <td>Konum</td>
                <td>' . htmlspecialchars($kesif->konum) . '</td>
            </tr>
            <tr>
                <td>Durum</td>
                <td>' . htmlspecialchars($kesif->durum ?? 'bekliyor') . '</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Yapılacak İş</div>
    <table>
        <tbody>
            <tr>
                <td colspan="2">' . nl2br(htmlspecialchars($kesif->yapilacak_is)) . '</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Keşif Sonu Notu</div>
    <table>
        <tbody>
            <tr>
                <td colspan="2">' . nl2br(htmlspecialchars($kesif->kesif_sonu_notu ?? '')) . '</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Adres ve QR</div>
    <table>
        <tbody>
            <tr>
                <td style="width:70%">' . htmlspecialchars($kesif->konum) . '</td>
                <td class="qr">' . ($qrDataUri ? ('<img src="' . $qrDataUri . '" alt="Google Haritalar QR">') : '') . '</td>
            </tr>
        </tbody>
    </table>

<footer>Rapor Tarihi: ' . date('d.m.Y H:i') . '</footer>
</body>
</html>';

$options = new Options();
$options->set('isPhpEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
ob_end_clean();
$dompdf->stream($title, ["Attachment" => false]);
?>