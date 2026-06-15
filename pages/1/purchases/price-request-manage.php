<?php

use App\Helper\customer;
use App\Helper\Financial;
use App\Helper\Helper;
use App\Model\PurchaseModel;

$Purchases = new PurchaseModel();

global $pdat;

$id = isset($_GET["id"]) ? $_GET["id"] : 0;

// Güncelleme işlemi ise bilgileri getirir.
$purchase = $Purchases->find($id);

if (!$purchase) {
    $purchase = new stdClass();
}

if (!function_exists('getFileIcon')) {
    function getFileIcon($path) {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) return 'fa-image';
        if ($ext == 'pdf') return 'fa-file-pdf-o';
        if (in_array($ext, ['xls', 'xlsx', 'csv'])) return 'fa-file-excel-o';
        return 'fa-paperclip';
    }
}

// Satın almanın ürünlerini getirir.
$purchaseItems = $Purchases->getPurchaseItems($id);

if ($id == 0) {
    $getNumber = setNumber("price_request");
    $siparisNo = "FT000" . $getNumber;
} else {
    $siparisNo = $purchase->siparisNo ?? '';
}

?>

<style>
    .attachments-cell {
        min-width: 140px;
    }
    .file-input-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
    }
    .file-input-wrapper input[type="file"] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    .status-indicator {
        font-size: 11px;
        margin-top: 4px;
        display: block;
        text-align: center;
        font-weight: 500;
    }
    .table th {
        background-color: #f1f5f9;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        color: #475569;
        padding: 12px 8px;
    }
    .btn-attachment-toggle {
        width: 100%;
        text-align: left;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 10px;
        border: 1px dashed #cbd5e1;
        background: #f8fafc;
        border-radius: 6px;
        font-size: 12px;
        color: #64748b;
        transition: all 0.2s;
    }
    .btn-attachment-toggle:hover {
        border-color: #3b82f6;
        background: #eff6ff;
        color: #3b82f6;
    }
    .description-cell {
        min-width: 200px;
    }
    .summary-card {
        transition: transform 0.2s;
    }
    .status-indicator { 
        font-size: 10px; 
        font-weight: 500; 
        display: flex; 
        align-items: center; 
        gap: 8px; 
        text-decoration: none !important;
        position: relative;
        z-index: 50 !important;
    }
    .delete-file-btn {
        font-size: 14px !important;
        padding: 4px 6px;
        border-radius: 4px;
        transition: all 0.2s;
        cursor: pointer;
    }
    .delete-file-btn:hover {
        background-color: rgba(220, 53, 69, 0.1);
        transform: scale(1.2);
    }
    .bg-blue-light { background-color: #eff6ff !important; }

    /* DARK MODE TABLE HEADER */
    .dark-mode #tProduct thead th {
        background-color: #1e293b !important;
        color: #94a3b8 !important;
        border-color: #334155 !important;
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: 0.5px;
        padding: 12px 8px !important;
    }
    .dark-mode #tProduct td {
        border-color: #334155 !important;
    }
</style>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
<form enctype="multipart/form-data" method="POST" id="myForm">
    <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
    <input type="hidden" name="type" value="2"> <!-- 2 = Fiyat Talebi -->
    
    <div class="content pd-20 bg-white border-radius-16 box-shadow mb-30">
        <div class="clearfix">
            <div class="pull-left">
                <h4 class="text-blue">
                    Fiyat Talebi Yönetimi
                </h4>
                <p class="mb-30 font-14">Lütfen kırmızı yıldız <span class="text-danger">(*)</span> ile işaretli zorunlu alanları doldurunuz.</p>
            </div>
            <div class="float-right">
                <button type="button" id="savePriceRequestButton" class="btn btn-primary shadow-sm">
                    <i class="fa fa-save mr-1"></i> Kaydet
                </button>
                <a href="index.php?p=purchases/price-request-list" class="btn btn-outline-secondary ml-2">
                    <i class="fa fa-list mr-1"></i> Listeye Dön
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group row align-items-center">
                    <label class="col-md-4 font-weight-bold">Talep Numarası:</label>
                    <div class="col-md-8">
                        <span class="badge badge-info py-2 px-3" style="font-size: 1.1rem;"><?php echo $siparisNo; ?></span>
                        <input type="hidden" name="siparisNo" id="siparisNo" value="<?php echo $siparisNo; ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="customers" class="col-md-4 font-weight-bold">
                        <span class="text-danger">*</span> Firma:
                    </label>
                    <div class="col-md-8">
                        <?php echo customer::getCustomerSelect('customers', $purchase->companyID ?? 0); ?>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-4 font-weight-bold">
                        <span class="text-danger">*</span> Termin Tarihi:
                    </label>
                    <div class="col-md-8">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white"><i class="fa fa-calendar text-primary"></i></span>
                            </div>
                            <input required class="form-control date-picker" type="text"
                                value="<?php echo $purchase->deadline ?? date("d-m-Y") ?>" name="deadline"
                                autocomplete="off" placeholder="gg-aa-yyyy">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-4 font-weight-bold">Açıklama:</label>
                    <div class="col-md-8">
                        <textarea name="description1" style="height: 100px;" placeholder="Genel talep açıklaması..."
                            class="form-control"><?php echo $purchase->description1 ?? '' ?></textarea>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-md-4 font-weight-bold">Durum:</label>
                    <div class="col-md-8">
                        <?php echo Helper::selectState("state", $purchase->state ?? 1); ?>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-md-4 font-weight-bold">Kur Türü:</label>
                    <div class="col-md-8">
                        <?php KurTuru('cur_type', $purchase->currency ?? '') ?>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-4 font-weight-bold">Döviz Kurları:</label>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-usd"></i></span></div>
                                    <input type="text" readonly class="form-control" id="cur-Dollar" name="Dollar" value="<?php echo $purchase->Dollar ?? ''; ?>">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-eur"></i></span></div>
                                    <input type="text" readonly class="form-control" id="cur-Euro" name="Euro" value="<?php echo $purchase->Euro ?? ''; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SUMMARY CARDS FULL ROW -->
        <div class="row mt-4 mb-2">
            <div class="col-md-6">
                <div class="card summary-card bg-light border-0 shadow-sm mb-3">
                    <div class="card-body py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-0 font-12 uppercase tracking-wider">ARA TOPLAM (TL)</h6>
                            <h3 class="mb-0 text-primary font-weight-bold" id="buy-tl"><?php echo $purchase->TLTotal ?? "0.00" ?></h3>
                        </div>
                        <i class="fa fa-calculator fa-2x text-light"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card summary-card bg-primary text-white border-0 shadow-sm">
                    <div class="card-body py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-0 font-12 uppercase tracking-wider">GENEL TOPLAM (TL)</h6>
                            <h3 class="mb-0 font-weight-bold" id="lblTotalTL"><?php echo $purchase->altToplam ?? "0.00" ?></h3>
                            <input type="hidden" name="altToplam" id="altToplamInput" value="<?php echo $purchase->altToplam ?? "0.00" ?>">
                        </div>
                        <i class="fa fa-money fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content pd-20 bg-white border-radius-16 box-shadow mb-30">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="text-blue mb-0"><i class="fa fa-shopping-cart mr-2"></i> Talep Edilen Ürünler</h5>
            <button type="button" id="addRow" class="btn btn-sm btn-success shadow-sm px-3">
                <i class="fa fa-plus-circle mr-1"></i> Yeni Ürün Ekle
            </button>
        </div>
        
        <div class="table-responsive">
            <table id="tProduct" class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 40px;">#</th>
                        <th style="width: 130px;">Stok Kodu</th>
                        <th>Ürün Adı / Açıklama</th>
                        <th style="width: 80px;">Miktar</th>
                        <th style="width: 100px;">Birim</th>
                        <th style="width: 80px;">Alış Fiyatı</th>
                        <th style="width: 80px;">Para Birimi</th>
                        <th class="attachments-cell text-center">Ekler (Resim/Excel)</th>
                        <th class="text-center" style="width: 50px;">İşlem</th>
                    </tr>
                </thead>
                <tbody id="sortable">
                    <?php
                    $i = 0;
                    if (empty($purchaseItems)) {
                        $purchaseItems = [new stdClass()];
                    }
                    foreach ($purchaseItems as $index => $item) {
                        $i++;
                        $hasFile = !empty($item->image) || !empty($item->excel_file);
                        $fileUrl = !empty($item->image) ? $item->image : ($item->excel_file ?? '#');
                    ?>
                        <tr class="ui-state-default">
                            <td class="text-center align-middle">
                                <span class="text-muted font-weight-bold"><?php echo $i; ?></span>
                                <input name="satirno[]" type="hidden" value="<?php echo $i; ?>">
                            </td>
                            <td class="align-middle">
                                <input type="text" id="stokKodu<?php echo $i; ?>" value="<?php echo $item->stokKodu ?? ''; ?>" name="stokKodu[]" class="form-control form-control-sm" placeholder="Kodu">
                            </td>
                            <td class="align-middle">
                                <div class="input-group input-group-sm mb-1">
                                    <input type="text" class="urunAdi form-control" name="urunAdi[]" id="urunAdi<?php echo $i; ?>" value="<?php echo $item->product ?? ''; ?>" placeholder="Ürün Adı" required>
                                    <div class="input-group-append">
                                        <button type="button" id="<?php echo $i; ?>" class="btn btn-info selectProduct" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <input type="text" name="rowdescription[]" value="<?php echo $item->description ?? ''; ?>" class="form-control form-control-sm" placeholder="Kalem açıklaması...">
                            </td>
                            <td class="align-middle">
                                <input type="number" required id="amount<?php echo $i; ?>" name="amount[]" value="<?php echo $item->amount ?? ''; ?>" class="form-control form-control-sm text-center">
                            </td>
                            <td class="align-middle">
                                <?php OlcuBirimleri('unit[]', $item->unit ?? '', "required", "unit" . $i) ?>
                            </td>
                            <td class="align-middle">
                                <input required id="price<?php echo $i; ?>" name="price[]" type="number" step="0.01" value="<?php echo $item->price ?? ''; ?>" class="form-control form-control-sm text-right">
                            </td>
                            <td class="align-middle">
                                <?php echo Financial::getCurrencySelect("currency[]", $item->currency ?? '', "currency" . $i) ?>
                            </td>
                            <td class="text-center align-middle attachments-cell">
                                <div class="file-input-wrapper">
                                    <div class="btn-attachment-toggle <?php echo $hasFile ? 'border-primary bg-blue-light' : ''; ?>">
                                        <span class="file-label text-truncate"><?php echo $hasFile ? (strlen(basename($fileUrl)) > 18 ? substr(basename($fileUrl), 0, 15) . '...' : basename($fileUrl)) : 'Dosya Seç...'; ?></span>
                                        <i class="fa <?php echo $hasFile ? getFileIcon($fileUrl) . ' text-primary' : 'fa-paperclip'; ?>"></i>
                                    </div>
                                    <input type="file" name="row_file_<?php echo $index; ?>" accept=".jpg,.jpeg,.png,.pdf,.xls,.xlsx,.csv" onchange="handleSingleFileChange(this)">
                                    
                                    <!-- HIDDEN INPUTS TO SECURELY PRESERVE EXISTING FILES -->
                                    <input type="hidden" name="existing_image[]" value="<?php echo $item->image ?? ''; ?>">
                                    <input type="hidden" name="existing_excel_file[]" value="<?php echo $item->excel_file ?? ''; ?>">
                                    <?php if($hasFile): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="<?php echo $fileUrl; ?>" target="_blank" class="status-indicator text-primary" title="<?php echo basename($fileUrl); ?>">
                                                <i class="fa fa-external-link"></i> <?php echo strlen(basename($fileUrl)) > 15 ? substr(basename($fileUrl), 0, 12) . '...' : basename($fileUrl); ?>
                                            </a>
                                            <a href="javascript:void(0)" class="status-indicator text-danger delete-file-btn" onclick="deleteItemFile(<?php echo $id; ?>, <?php echo $item->id; ?>, this)" title="Dosyayı Sil">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="status-indicator text-muted">Dosya yok</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <button type="button" class="sil btn btn-sm btn-outline-danger border-0"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <input type="hidden" id="rowNumberId" value="<?php echo $i + 1 ?>">
    </div>
</form>

<div class="modal fade" id="staticBackdrop">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Ürün Seçiniz</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php generateProductSelect("productName[]", '') ?>
                <input type="hidden" id="rowID">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-modal="modal">Kapat</button>
                <button type="button" class="btn btn-danger" onclick="getProductInfoPurchase()">Seç</button>
            </div>
        </div>
    </div>
</div>

<script src="pages/1/purchases/script.js"></script>
<script>
    function getFileIconClass(fileName) {
        const ext = fileName.split('.').pop().toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) return 'fa-image';
        if (ext === 'pdf') return 'fa-file-pdf-o';
        if (['xls', 'xlsx', 'csv'].includes(ext)) return 'fa-file-excel-o';
        return 'fa-file-o';
    }

    function handleSingleFileChange(input) {
        const wrapper = $(input).closest('.file-input-wrapper');
        const toggle = wrapper.find('.btn-attachment-toggle');
        const label = toggle.find('.file-label');
        const icon = toggle.find('i');
        const indicator = wrapper.find('.status-indicator');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB

            if (file.size > maxSize) {
                Swal.fire('Hata', 'Dosya boyutu 5MB\'tan büyük olamaz. Lütfen daha küçük bir dosya seçin.', 'error');
                input.value = ''; // Seçimi temizle
                label.text('Dosya Seç...').css('color', '');
                toggle.removeClass('border-primary bg-blue-light');
                icon.attr('class', 'fa fa-paperclip');
                indicator.html('<span class="status-indicator text-muted">Dosya yok</span>');
                return;
            }

            const fileName = file.name;
            const shortName = fileName.length > 15 ? fileName.substring(0, 12) + '...' : fileName;
            label.text(shortName).css('color', '#3b82f6');
            toggle.addClass('border-primary bg-blue-light');
            
            const iconClass = getFileIconClass(fileName);
            icon.attr('class', 'fa ' + iconClass + ' text-primary');
            
            indicator.html('<i class="fa fa-check text-success"></i> ' + shortName).removeClass('text-muted').addClass('text-primary');
        }
    }

    function deleteItemFile(purchaseId, itemId, btn) {
        Swal.fire({
            title: 'Emin misiniz?',
            text: "Bu dosyayı silmek istediğinize emin misiniz? Bu işlem geri alınamaz!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Evet, sil!',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'deleteItemFile');
                formData.append('purchaseId', purchaseId);
                formData.append('itemId', itemId);

                fetch('App/api/purchase.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const wrapper = $(btn).closest('.file-input-wrapper');
                        const toggle = wrapper.find('.btn-attachment-toggle');
                        const label = toggle.find('.file-label');
                        const icon = toggle.find('i');
                        
                        // CRITICAL: Clear hidden tracking fields so backend doesn't restore it on Save
                        wrapper.find("input[name='existing_image[]']").val("");
                        wrapper.find("input[name='existing_excel_file[]']").val("");
                        
                        label.text('Dosya Seç...').css('color', '');
                        toggle.removeClass('border-primary bg-blue-light');
                        icon.attr('class', 'fa fa-paperclip');
                        
                        $(btn).closest('.d-flex').html('<span class="status-indicator text-muted">Dosya yok</span>');
                        
                        Swal.fire(
                            'Silindi!',
                            'Dosya başarıyla silindi.',
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Hata!',
                            data.message,
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Hata!',
                        'Bir sorun oluştu.',
                        'error'
                    );
                });
            }
        });
    }

    async function priceRequestRowAdd(sayac) {
        $("#preloader").show();
        
        let selectUnit = '';
        let selectmoneys = '';
        
        let formData = new FormData();
        formData.append("action", "getUnits");
        
        try {
            const response = await fetch("App/api/units.php", {
                method: "POST",
                body: formData,
            });
            const data = await response.json();
            if (data.status === "success") {
                selectUnit = `<select required id="unit${sayac}" name="unit[]" class="selectpicker form-control form-control-sm" data-style="bg-white">`;
                data.data.forEach(u => {
                    selectUnit += `<option value="${u.title}">${u.title}</option>`;
                });
                selectUnit += `</select>`;
            }
        } catch (e) { console.error(e); }

        selectmoneys = `<select required id="currency${sayac}" name="currency[]" class="selectpicker form-control form-control-sm" data-style="bg-white">`;
        ["TRY", "USD", "EUR"].forEach(m => {
            selectmoneys += `<option value="${m}">${m}</option>`;
        });
        selectmoneys += `</select>`;

        const rowIndex = parseInt(sayac) - 1;
        
        let newRow = `
            <tr class="ui-state-default">
                <td class="text-center align-middle">
                    <span class="text-muted font-weight-bold">${sayac}</span>
                    <input name="satirno[]" type="hidden" value="${sayac}">
                </td>
                <td class="align-middle"><input type="text" id="stokKodu${sayac}" name="stokKodu[]" class="form-control form-control-sm" placeholder="Kodu"></td>
                <td class="align-middle">
                    <div class="input-group input-group-sm mb-1">
                        <input type="text" required name="urunAdi[]" id="urunAdi${sayac}" class="urunAdi form-control" placeholder="Ürün Adı">
                        <div class="input-group-append">
                            <button type="button" id="${sayac}" class="btn btn-info selectProduct">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <input type="text" name="rowdescription[]" class="form-control form-control-sm" placeholder="Kalem açıklaması...">
                </td>
                <td class="align-middle"><input type="number" required id="amount${sayac}" name="amount[]" class="form-control form-control-sm text-center"></td>
                <td class="align-middle">${selectUnit}</td>
                <td class="align-middle"><input type="number" step="0.01" required id="price${sayac}" name="price[]" class="form-control form-control-sm text-right"></td>
                <td class="align-middle">${selectmoneys}</td>
                <td class="text-center align-middle attachments-cell">
                    <div class="file-input-wrapper">
                        <div class="btn-attachment-toggle">
                            <span class="file-label text-truncate">Dosya Seç...</span>
                            <i class="fa fa-paperclip"></i>
                        </div>
                        <input type="file" name="row_file_${rowIndex}" accept=".jpg,.jpeg,.png,.pdf,.xls,.xlsx,.csv" onchange="handleSingleFileChange(this)">
                        <input type="hidden" name="existing_image[]" value="">
                        <input type="hidden" name="existing_excel_file[]" value="">
                        <span class="status-indicator text-muted">Dosya yok</span>
                    </div>
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="sil btn btn-sm btn-outline-danger border-0"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
        `;
        
        $("#tProduct tbody").append(newRow);
        $(".selectpicker").selectpicker("refresh");
        $("#preloader").hide();
    }

    $(document).ready(function() {
        $("#addRow").off("click").on("click", function (e) {
            e.preventDefault();
            var sayac = $("#rowNumberId");
            priceRequestRowAdd(sayac.val());
            sayac.val(parseInt(sayac.val(), 10) + 1);
        });

        $(document).off("click", "#savePriceRequestButton").on("click", "#savePriceRequestButton", function (e) {
            e.preventDefault();
            e.stopImmediatePropagation(); // Ensure other handlers don't run
            
            // Fix selector to be robust against generic Helper generated IDs
            const customerVal = $("select[name='customers']").val();
            if (!customerVal || customerVal === "") {
                Swal.fire("Uyarı", "Lütfen bir firma seçiniz!", "warning");
                return;
            }

            // DYNAMIC REINDEXING OF FILE INPUTS TO ENSURE PERFECT PHP SYNC
            // Loop through currently visible file inputs in the table row by row
            // and re-index their name attributes so they match exact index 0, 1, 2...
            // perfectly corresponding to PHP backend loop $i for $_FILES["row_file_$i"]
            $("#tProduct tbody tr").each(function(index) {
                $(this).find("input[type='file']").attr("name", "row_file_" + index);
            });

            var form = $("#myForm");
            var formData = new FormData(form[0]);
            formData.append("action", "savePurchases");

            // SHOW LOADER WITH SWEETALERT AS REQUESTED BY USER
            Swal.fire({
                title: 'İşlem Yapılıyor...',
                text: 'Lütfen bekleyiniz, veriler kaydediliyor.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch("App/api/purchase.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                $("#preloader").hide();
                Swal.fire({
                    title: data.status == "success" ? "Başarılı" : "Hata",
                    text: data.message,
                    icon: data.status,
                    confirmButtonText: "Tamam"
                }).then((result) => {
                    if (result.isConfirmed && data.status == "success") {
                        window.location.href = "index.php?p=purchases/price-request-list";
                    }
                });
            })
            .catch(err => {
                $("#preloader").hide();
                Swal.fire("Hata", "Sunucu hatası oluştu!", "error");
            });
        });
    });
</script>
