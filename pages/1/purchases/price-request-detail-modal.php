<?php
require_once dirname(__DIR__, 3) . "/bootstrap.php";

use App\Helper\Helper;
use App\Model\PurchaseModel;

$id = $_GET['id'] ?? 0;
$Purchase = new PurchaseModel();
$purchase = $Purchase->find($id);

if (!$purchase) {
    echo '<div class="alert alert-warning m-3">Kayıt bulunamadı!</div>';
    exit;
}

$items = $Purchase->getPurchaseItems($id);
$customer_name = getCustomerName($purchase->companyID);
$creator_name = getUserName($purchase->creator);

function getFileIcon($path) {
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) return 'fa-image';
    if ($ext == 'pdf') return 'fa-file-pdf-o';
    if (in_array($ext, ['xls', 'xlsx', 'csv'])) return 'fa-file-excel-o';
    return 'fa-file-o';
}
?>

<style>
    .modal-detail-wrapper {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1e293b;
    }
    .detail-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 30px;
        border-bottom: 1px solid #e2e8f0;
    }
    .detail-id {
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: #0f172a;
        margin-bottom: 5px;
    }
    .detail-meta {
        font-size: 13px;
        color: #64748b;
    }
    .detail-customer-box {
        text-align: right;
    }
    .detail-customer-name {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
        line-height: 1.2;
    }
    .summary-stats {
        padding: 0 30px;
        margin-top: -25px;
    }
    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
    .stat-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        margin-bottom: 5px;
        display: block;
    }
    .stat-value {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
    }
    .items-container {
        padding: 40px 30px;
    }
    .items-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .items-table th {
        background: #f8fafc;
        padding: 12px 15px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 2px solid #e2e8f0;
    }
    .items-table td {
        padding: 15px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .items-table tr:hover td {
        background-color: #f8fafc;
    }
    .product-title {
        font-weight: 600;
        color: #0f172a;
        display: block;
        margin-bottom: 2px;
    }
    .product-desc {
        font-size: 12px;
        color: #94a3b8;
    }
    .amount-badge {
        background: #f1f5f9;
        color: #475569;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 12px;
    }
    .price-text {
        font-weight: 600;
        color: #0f172a;
    }
    .total-text {
        font-weight: 700;
        color: #2563eb;
    }
    .attachment-btn {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 8px;
        transition: all 0.2s;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 11px;
        font-weight: 600;
        text-decoration: none !important;
        max-width: 150px;
    }
    .attachment-btn i {
        font-size: 14px;
        margin-right: 6px;
    }
    .attachment-btn:hover {
        background: #eff6ff;
        border-color: #3b82f6;
        color: #3b82f6;
        transform: translateY(-1px);
    }

</style>

<div class="modal-detail-wrapper">
    <!-- Header Area -->
    <div class="detail-header">
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="detail-id"><?php echo $purchase->siparisNo; ?></div>
                <div class="detail-meta d-flex gap-3">
                    <span><i class="fa fa-calendar-plus-o mr-1"></i> <strong>Kayıt:</strong> <?php echo $purchase->create_time; ?></span>
                    <span class="ml-3"><i class="fa fa-calendar-check-o mr-1"></i> <strong>Termin:</strong> <?php echo $purchase->deadline; ?></span>
                </div>
                <div class="detail-meta mt-1">
                    <span><i class="fa fa-user-o mr-1"></i> <strong>Oluşturan:</strong> <?php echo $creator_name; ?></span>
                </div>
            </div>
            <div class="col-md-5 detail-customer-box">
                <div class="detail-customer-name"><?php echo $customer_name; ?></div>
                <div class="d-flex justify-content-end align-items-center mt-2">
                    <span class="mr-2 text-muted font-12">Durum:</span>
                    <?php echo Helper::getStateBadge($purchase->state); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="summary-stats">
        <div class="row">
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">ARA TOPLAM (TL)</span>
                            <div class="stat-value"><?php echo number_format((float)($purchase->TLTotal ?? 0), 2, ',', '.') . ' ₺'; ?></div>
                        </div>
                        <div class="rounded-circle bg-light p-3">
                            <i class="fa fa-calculator fa-lg text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card bg-primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label text-white-50">GENEL TOPLAM (TL)</span>
                            <div class="stat-value text-white"><?php echo number_format((float)($purchase->altToplam ?? 0), 2, ',', '.') . ' ₺'; ?></div>
                        </div>
                        <div class="rounded-circle bg-white-10 p-3" style="background: rgba(255,255,255,0.1)">
                            <i class="fa fa-money fa-lg text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Description -->
    <?php if (!empty($purchase->description1)): ?>
    <div class="px-30 mt-4">
        <div class="alert bg-blue-light border-0 py-3 px-4">
            <h6 class="font-12 font-weight-bold text-primary text-uppercase mb-1">Genel Açıklama</h6>
            <p class="mb-0 text-slate-700 font-14"><?php echo nl2br($purchase->description1); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Items Table -->
    <div class="items-container">
        <h6 class="font-14 font-weight-bold mb-3 d-flex align-items-center">
            <i class="fa fa-shopping-cart mr-2 text-primary"></i> Talep Edilen Kalemler
        </h6>
        <div class="table-responsive">
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Stok Kodu</th>
                        <th>Ürün / Hizmet Açıklaması</th>
                        <th class="text-center">Miktar</th>
                        <th class="text-right">Birim Fiyat</th>
                        <th class="text-right">Toplam</th>
                        <th class="text-center">Ekler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 0;
                    foreach ($items as $item): 
                        $i++;
                        $rowTotal = $item->amount * $item->price;
                    ?>
                    <tr>
                        <td class="text-center text-muted font-weight-bold"><?php echo $i; ?></td>
                        <td><code class="text-primary"><?php echo $item->stokKodu ?: '-'; ?></code></td>
                        <td>
                            <span class="product-title"><?php echo $item->product; ?></span>
                            <?php if(!empty($item->description)): ?>
                                <span class="product-desc"><?php echo $item->description; ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="amount-badge"><?php echo $item->amount . ' ' . $item->unit; ?></span>
                        </td>
                        <td class="text-right">
                            <span class="price-text"><?php echo number_format((float)($item->price ?? 0), 2, ',', '.') . ' ' . $item->currency; ?></span>
                        </td>
                        <td class="text-right">
                            <span class="total-text"><?php echo number_format((float)($rowTotal ?? 0), 2, ',', '.') . ' ' . $item->currency; ?></span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center flex-column gap-1">
                                <?php if(!empty($item->image)): ?>
                                    <a href="<?php echo $item->image; ?>" target="_blank" class="attachment-btn" title="Resim">
                                        <i class="fa <?php echo getFileIcon($item->image); ?>"></i>
                                        <span class="text-truncate"><?php echo basename($item->image); ?></span>
                                    </a>
                                <?php endif; ?>
                                <?php if(!empty($item->excel_file)): ?>
                                    <a href="<?php echo $item->excel_file; ?>" target="_blank" class="attachment-btn" title="Dosya">
                                        <i class="fa <?php echo getFileIcon($item->excel_file); ?>"></i>
                                        <span class="text-truncate"><?php echo basename($item->excel_file); ?></span>
                                    </a>
                                <?php endif; ?>
                                <?php if(empty($item->image) && empty($item->excel_file)): ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
