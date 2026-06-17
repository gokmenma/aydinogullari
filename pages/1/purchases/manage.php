<?php


use App\Helper\customer;
use App\Helper\Financial;
use App\Helper\Helper;
use App\Model\PurchaseModel;

$Purchases = new PurchaseModel();

global $pdat; // IDE desteği ve global erişim için

//güncelleme işlem ise id alınır yoksa 0 atanır.
$id = isset($_GET["id"]) ? $_GET["id"] : 0;

$talep_id = isset($_GET["talep_id"]) ? $_GET["talep_id"] : 0;

//Satın alma talebinden mi geldi yoksa yeni bir satın alma işlemi mi yapılacak
$demand = isset($_GET["demand"]) ? true : false;

if ($demand) {
    $id = $talep_id;
}

//Güncelleme işlemi ise satın alma bilgilerini getirir.
$purchase = $Purchases->find($id);

if (!$purchase) {
    $purchase = new stdClass();
}


//Satın almanın ürünlerini getirir.
$purchaseItems = $Purchases->getPurchaseItems($id);


//eğer satın alma talebinden geldiyse veya yeni bir satın alma işlemi ise
//yeni bir sipariş numarası oluşturulur.
if ($demand == true || $id == 0) {
    $getNumber = setNumber("purchase");
    $siparisNo = "SA000" . $getNumber;
} else {
    $siparisNo = $purchase->siparisNo ?? '';
}

?>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
<form enctype="multipart/form-data" method="POST" id="myForm">
    <input type="hidden" name="satinAlmaTalebiniKapat" value="<?php echo $demand ? 1 : 0; ?>">
    <input type="hidden" name="talep_id" value="<?php echo $talep_id; ?>">
    
    <div class="purchase-manage-wrapper">
        <!-- Header Card -->
        <div class="purchase-header-card animate-fade-in">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <div class="header-title">
                        <h4><?php echo $id != 0 ? 'Siparişi Düzenle' : 'Yeni Sipariş Girişi'; ?></h4>
                        <span class="purchase-number-badge">
                            <i class="fa fa-tag"></i> Sipariş No: <?php echo $siparisNo; ?>
                        </span>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="index.php?p=purchases" class="btn-header btn-header-list">
                        <i class="fa fa-list"></i> Listeye Dön
                    </a>
                    <button type="button" id="saveButton" class="btn-header btn-header-save">
                        <i class="fa fa-save"></i> Kaydet
                    </button>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="form-card animate-fade-in">
            <div class="form-card-header d-flex justify-content-between align-items-center">
                <div class="header-left-inner">
                    <div class="card-icon card-icon-blue" style="background: #eff6ff; color: #3b82f6;">
                        <i class="fa fa-file-text-o"></i>
                    </div>
                    <div>
                        <h5>Sipariş Detayları</h5>
                        <p class="mb-0 text-muted font-12" style="margin-top: 2px;">
                            Sipariş genel bilgilerini ve teslimat tercihlerini bu alandan yönetebilirsiniz.
                        </p>
                    </div>
                </div>
            </div>

        <div class="row">
            <!-- COLUMN ONE -->
            <div class="col-md-6">
                <div class="form-group row align-items-center">
                    <label class="col-form-label col-md-4 font-weight-600 text-slate" style="font-size: 13.5px;">
                        <span class="text-danger">*</span> Sipariş Numarası
                    </label>
                    <div class="col-md-8">
                        <span class="badge badge-light text-dark font-15 weight-600 px-3 py-2" style="border-radius: 6px; background: #f1f5f9; border: 1px solid #cbd5e1;">
                            <?php echo $siparisNo; ?>
                        </span>
                        <input type="hidden" name="siparisNo" id="siparisNo" value="<?php echo $siparisNo; ?>">
                        <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
                        <input type="hidden" name="demand" id="demand" value="<?php echo $demand; ?>">
                    </div>
                </div>

                <!-- Firma Bilgileri -->
                <div class="form-group row align-items-center">
                    <label class="col-form-label col-md-4 font-weight-600 text-slate" style="font-size: 13.5px;">
                        <span class="text-danger">*</span> Firma
                    </label>
                    <div class="col-md-8 d-flex align-items-center">
                        <div class="w-100 mr-2">
                            <?php echo customer::getCustomerSelect('customers', $purchase->companyID ?? 0); ?>
                        </div>
                        <a href="index.php?p=new-customer" target="_blank"
                            class="btn btn-outline-primary d-flex align-items-center justify-content-center" style="height: 38px; width: 38px; border-radius: 8px; padding: 0;"
                            data-tooltip="Yeni Firma Ekle"><i class="fa fa-plus"></i>
                        </a>
                    </div>
                </div>
                <!-- Firma Bilgileri -->

                <!-- Termin Tarihi -->
                <div class="form-group row align-items-center">
                    <label class="col-form-label col-md-4 font-weight-600 text-slate" style="font-size: 13.5px;">
                        <span class="text-danger">*</span> Termin Tarihi
                    </label>
                    <div class="col-md-8">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0" style="border-radius: 8px 0 0 8px; border-color: #cbd5e1;"><i class="fa fa-calendar-o text-muted"></i></span>
                            </div>
                            <input required class="form-control date-picker border-left-0" style="border-radius: 0 8px 8px 0; border-color: #cbd5e1;" type="text"
                                value="<?php echo $purchase->deadline ?? date("d-m-Y") ?>" name="deadline"
                                autocomplete="off" placeholder="gg-aa-yyyy">
                        </div>
                    </div>
                </div>
                <!-- Termin Tarihi -->

                <!-- Ödeme Vadesi -->
                <div class="form-group row align-items-center">
                    <label class="col-form-label col-md-4 font-weight-600 text-slate" style="font-size: 13.5px;">
                        <span class="text-danger">*</span> Ödeme Vadesi
                    </label>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 pr-1">
                                <input type="text" required id="payPeriod" name="vadeGun" class="form-control"
                                    autocomplete="off" placeholder="Gün giriniz" style="border-radius: 8px; border-color: #cbd5e1;"
                                    value="<?php echo $purchase->vadeGun ?? date("d-m-Y") ?>">
                            </div>
                            <div class="col-md-6 pl-1">
                                <input type="text" readonly id="payment_date" name="payment_date" class="form-control bg-light" style="border-radius: 8px; border-color: #cbd5e1;"
                                    value="<?php echo $purchase->payment_date ?? date("d-m-Y") ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Ödeme Vadesi -->

                <!-- Açıklama -->
                <div class="form-group row">
                    <label class="col-form-label col-md-4 font-weight-600 text-slate" style="font-size: 13.5px;">
                        Açıklama 1
                    </label>
                    <div class="col-md-8">
                        <textarea name="description1" placeholder="Sipariş formunda görünecek açıklama giriniz"
                            class="form-control" style="border-radius: 8px; border-color: #cbd5e1; min-height: 80px;" type="text"><?php echo $purchase->description1 ?? '' ?></textarea>
                    </div>
                </div>
                <!-- Açıklama -->

            </div>
            <!-- COLUMN ONE -->

            <!-- COLUMN TWO -->
            <div class="col-md-6">
                <!-- Satın Alma Durumu -->
                <div class="form-group row align-items-center">
                    <label class="col-form-label col-md-4 font-weight-600 text-slate" style="font-size: 13.5px;">
                        Durumu
                    </label>
                    <div class="col-md-8">
                        <?php
                        $state = $purchase->state ?? 0;
                        echo Helper::selectState("state", $state);
                        ?>
                    </div>
                </div>
                <!-- Satın Alma Durumu -->
                
                <!-- Fatura Tarihi -->
                <div class="form-group row align-items-center">
                    <label class="col-form-label col-md-4 font-weight-600 text-slate" style="font-size: 13.5px;">
                        Fatura Tarihi/Numarası
                    </label>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 pr-1">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0" style="border-radius: 8px 0 0 8px; border-color: #cbd5e1;"><i class="fa fa-calendar-o text-muted"></i></span>
                                    </div>
                                    <input type="text" class="form-control date-picker border-left-0" style="border-radius: 0 8px 8px 0; border-color: #cbd5e1;" name="invoice_date"
                                        value="<?php echo $purchase->invoice_date ?? ''; ?>" placeholder="Fatura Tarihi">
                                </div>
                            </div>
                            <div class="col-md-6 pl-1">
                                <input type="text" class="form-control" style="border-radius: 8px; border-color: #cbd5e1;" name="invoice_number"
                                    value="<?php echo $purchase->invoice_number ?? ''; ?>" placeholder="Fatura Numarası">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fatura Tarihi -->

                <!-- Para Birimi -->
                <div class="form-group row align-items-center">
                    <label class="col-form-label col-md-4 font-weight-600 text-slate" style="font-size: 13.5px;">
                        Kur Türü
                    </label>
                    <div class="col-md-8">
                        <?php KurTuru('cur_type', $purchase->currency ?? '') ?>
                    </div>
                </div>
                <!-- Para Birimi -->

                <!-- Kur Bilgileri -->
                <div class="form-group row align-items-center">
                    <label class="col-form-label col-md-4 font-weight-600 text-slate" style="font-size: 13.5px;">
                        Dolar / Euro
                    </label>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 pr-1">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0 text-muted" style="border-radius: 8px 0 0 8px; border-color: #cbd5e1; font-weight: 600;">$</span>
                                    </div>
                                    <input type="text" readonly class="form-control bg-light border-left-0" style="border-radius: 0 8px 8px 0; border-color: #cbd5e1;" id="cur-Dollar" name="curDollar">
                                </div>
                            </div>
                            <div class="col-md-6 pl-1">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0 text-muted" style="border-radius: 8px 0 0 8px; border-color: #cbd5e1; font-weight: 600;">€</span>
                                    </div>
                                    <input type="text" readonly class="form-control bg-light border-left-0" style="border-radius: 0 8px 8px 0; border-color: #cbd5e1;" id="cur-Euro" name="curEuro">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Kur Bilgileri -->

                <!-- Açıklama -->
                <div class="form-group row">
                    <label class="col-form-label col-md-4 font-weight-600 text-slate" style="font-size: 13.5px;">
                        Açıklama 2
                    </label>
                    <div class="col-md-8">
                        <textarea name="description2" class="form-control" style="border-radius: 8px; border-color: #cbd5e1; min-height: 80px;"
                            type="text" placeholder="İç açıklama giriniz"><?php echo $purchase->description2 ?? ''; ?></textarea>
                    </div>
                </div>
                <!-- Açıklama -->

            </div>
            <!-- COLUMN TWO -->
        </div>
    </div>


    <div class="form-card animate-fade-in mt-4">

        <?php

        $alisToplam = $purchase->TLTotal ?? "0.00"; // Alış toplamı
        $iskontoToplam = $purchase->iskonto ?? "0.00"; // İskonto toplamı
        $kdv = $purchase->Kdv ?? "20"; // KDV oranı
        $kdvToplam = $purchase->altToplam ?? "0.00"; // KDV dahil toplam tutar

        ?>
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="sum-primary">
                    <label style="font-weight: 600;">Tutar TL</label>
                    <label id="buy-tl">
                        <?php echo $alisToplam ?>
                    </label>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="sum-warning">
                    <label style="font-weight: 600;">KDV Oranı(%)</label>
                    <label id="kdv-rate">
                        <?php echo $kdv ?>
                    </label>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="sum-success">
                    <label style="font-weight: 600;">İskonto TL</label>
                    <label id="discount">
                        <?php echo $iskontoToplam ?>
                    </label>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="sum-danger">
                    <label style="font-weight: 600;">KDV Dahil TL</label>
                    <label name="lblTotalTL" id="lblTotalTL">
                        <?php echo $kdvToplam ?>
                    </label>
                </div>
            </div>
        </div>

        <!-- Sipariş ürünleri -->

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="text-blue font-weight-700 m-0">Ürün Bilgileri</h5>
        </div>

        <div class="hack1">
            <div class="hack2">

                <table id="tProduct" class="table premium-table">
                    <thead>
                        <tr>
                            <th style="width: 35px;">Taşı</th>
                            <th style="width: 50px;">İşlem</th>
                            <th style="width: 45px;">Sıra</th>
                            <th style="width: 100px;">Stok Kodu</th>
                            <th>Ürün Adı</th>
                            <th style="width: 70px;">Miktar</th>
                            <th style="width: 100px;">Birim</th>
                            <th style="width: 90px;">Fiyat</th>
                            <th style="width: 100px;">Para Birimi</th>
                        </tr>
                    </thead>

                    <tbody id="sortable">
                        <?php
                        $i = 0;
                        //Eğer purchaseItems boş ise yeni bir satır eklenir.
                        if (empty($purchaseItems)) {
                            $purchaseItems = [new stdClass()];
                        }
                        foreach ($purchaseItems as $item) {
                            $i++;
                        ?>
                            <tr class="ui-state-default">
                                <td style="width: 10px; vertical-align: middle;"><a href="#" class="btn btn-sm"><i class="fa fa-arrows-alt"></i></a></td>

                                <td class="app-item-action" style="vertical-align: middle;">
                                    <a type="button" class="sil btn btn-sm btn-danger text-white" style="border-radius: 6px;">Sil</a>
                                </td>

                                <!--Sırano-->
                                <td class="app-item-number" style="vertical-align: middle;">
                                    <input class="form-control text-center" name="satirno[]" type="text" value="<?php echo $i; ?>" style="border-radius: 6px; border-color: #cbd5e1;">
                                </td>
                                <!--Sırano-->

                                <!-- Stok Kodu -->
                                <td class="app-item-stock" style="vertical-align: middle;"><input type="text" id="stokKodu<?php echo $i; ?>"
                                        value="<?php echo $item->stokKodu ?? ''; ?>" name="stokKodu[]" class="form-control"
                                        placeholder="Stok Kodu" style="border-radius: 6px; border-color: #cbd5e1;">
                                </td>
                                <!-- Stok Kodu -->

                                <td class="app-item-name" style="vertical-align: middle;">
                                    <!-- Button trigger modal -->
                                    <div class="input-group m-0">

                                        <input type="text" class="urunAdi form-control" name="urunAdi[]"
                                            id="urunAdi<?php echo $i; ?>" value="<?php echo $item->product ?? ''; ?>"
                                            placeholder="Ürün adı" style="border-radius: 6px 0 0 6px; border-color: #cbd5e1;">
                                        <button type="button" id="<?php echo $i; ?>"
                                            class="btn btn-sm btn-info selectProduct text-white" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop" style="border-radius: 0 6px 6px 0;">
                                            <i class="fa fa-plus-circle"></i>
                                        </button>
                                    </div>



                                </td>
                                <!-- MİKTAR -->
                                <td class="app-item-amount" style="vertical-align: middle;">
                                    <input type="number" autocomplete="off" required id="amount" name="amount[]"
                                        value="<?php echo $item->amount ?? ''; ?>" class="Adet form-control" style="border-radius: 6px; border-color: #cbd5e1;">
                                </td>
                                <!-- MİKTAR -->

                                <!-- ÖLÇÜ BİRİMLERİ -->
                                <td class="app-item-unit" style="vertical-align: middle;">
                                    <?php OlcuBirimleri('unit[]', $item->unit ?? '', "required", "unit" . $i) ?>
                                </td>
                                <!-- ÖLÇÜ BİRİMLERİ -->

                                <!-- FİYAT -->
                                <td class="app-item-price" style="vertical-align: middle;">
                                    <input required id="price<?php echo $i; ?>" name="price[]" type="number"
                                        value="<?php echo $item->price ?? ''; ?>" class="form-control" autocomplete="off" style="border-radius: 6px; border-color: #cbd5e1;">

                                </td>
                                <!-- FİYAT -->

                                <!-- PARA BİRİMLERİ -->
                                <td class="app-item-cur" style="vertical-align: middle;">
                                    <?php echo Financial::getCurrencySelect("currency[]", $item->currency ?? '', "currency" . $i) ?>
                                </td>
                                <!-- PARA BİRİMLERİ -->
                            </tr>

                        <?php } ?>

                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="9">
                                <button type="button" id="addRow" class="btn float-left btn-sm btn-primary mt-3 mb-3" style="border-radius: 8px;">
                                    <i class="fa fa-plus"></i> Yeni Satır
                                </button>

                            </td>
                        </tr>

                    </tfoot>
                </table>
                <input type="hidden" id="rowNumberId" value="<?php echo $i + 1 ?>">

            </div>
        </div>
    </div>
    <div class="form-card animate-fade-in mt-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="text-blue font-weight-700 m-0">Alt Toplamlar</h5>
        </div>
        <div class="hack1">
            <div class="hack2">
                <table id="tblAltToplam" class="table premium-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Göster</th>
                            <th style="width: 120px;">Euro Toplam</th>
                            <th style="width: 120px;">Dolar Toplam</th>
                            <th style="width: 120px;">TL Toplam</th>
                            <th style="width: 120px;">İskonto Toplam</th>
                            <th style="width: 100px;">Kdv (%)</th>
                            <th style="width: 130px;">Toplam Tutar (TL)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="sub-item-view" style="vertical-align: middle;">
                                <span class="badge badge-primary px-3 py-2" style="border-radius: 6px;">Göster</span>
                            </td>
                            <td style="vertical-align: middle;">
                                <input type="text" class="form-control text-center" name="EuroAlttoplam" id="EuroAlttoplam"
                                    value="<?php echo $purchase->EuroTotal ?? '0.00' ?>" style="border-radius: 6px; border-color: #cbd5e1;">
                            </td>
                            <td style="vertical-align: middle;">
                                <input type="text" class="form-control text-center" name="DolarAlttoplam" id="DolarAlttoplam"
                                    value="<?php echo $purchase->DolarTotal ?? '0.00' ?>" style="border-radius: 6px; border-color: #cbd5e1;">
                            </td>
                            <td style="vertical-align: middle;">
                                <input type="text" class="form-control text-center" name="TLAlttoplam" id="TLAlttoplam"
                                    value="<?php echo $purchase->TLTotal ?? '0.00' ?>" style="border-radius: 6px; border-color: #cbd5e1;">
                            </td>

                            <td style="vertical-align: middle;">
                                <input type="number" autocomplete="off" class="form-control text-center" name="iskonto"
                                    value="<?php echo $purchase->iskonto ?? '0.00'; ?>" id="iskonto" style="border-radius: 6px; border-color: #cbd5e1;">
                            </td>
                            <td style="vertical-align: middle;">
                                <?php KdvOranları("Kdv", $purchase->Kdv ?? 20) ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <input type="text" autocomplete="off" class="form-control text-center" name="altToplam"
                                    id="altToplamInput" value="<?php echo $purchase->altToplam ?? '0.00'; ?>" style="border-radius: 6px; border-color: #cbd5e1;">
                                <input type="hidden" id="araToplam" name="araToplam" value="">
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
</form>



<div class="modal show" id="staticBackdrop">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel">Listeden ürün seçiniz!</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php generateProductSelect("productName[]", '') ?>
                <input type="hidden" id="rowID">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-danger" onclick="getProductInfoPurchase()">Seç</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(function() {
    //    $("#sortable").sortable();

            var el = document.getElementById('sortable');
        var sortable = Sortable.create(el, {
            onUpdate: function (/**Event*/evt) {
                // Sıralama sonrası numaralandırma
                $("#tProduct tbody tr").each(function(index) {
                    // Numara hücresini güncelle (örneğin ilk <td>)
                    $(this).find("input[name='satirno[]']").val(index + 1);
                });
            }
        });
    });
</script>