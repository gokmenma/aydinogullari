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
    <div class="content pd-20 bg-white border-radius-16 box-shadow mb-30">
        <div class="clearfix">
            <div class="pull-left">
                <h4 class="text-blue">
                    <?php echo $pdat["p_title"] ?? 'Satın Alma Yönetimi'; ?>
                </h4>
                <p class="mb-30 font-14">Sayfadaki <font color="red">(*)</font> yıldız ile belirtilen alanları boş
                    bırakmayın..<br>
                </p>
            </div>
            <div class="float-right">
                <button type="button" id="saveButton" data-tooltip="Kaydet" data-tooltip-location="bottom"
                    class="btn btn-sm btn-primary text-white">
                    <i class="fa fa-save"></i>
                    Kaydet</button>
                <a href="index.php?p=purchases" class="btn btn-sm btn-secondary" data-tooltip="Listeye Dön"
                    data-tooltip-location="bottom"><i class="fa fa-list"></i> Listeye Dön</a>

            </div>
        </div>



        <div class="row">


            <!-- COLUMN ONE -->
            <div class="col-md-6">
                <div class="form-group row">
                    <label for="" class="col-md-4">
                        <font color="red">(*)</font>Sipariş Numarası :
                    </label>
                    <div class="input-group col-md-8">
                        <h5>
                            <?php echo $siparisNo; ?>
                        </h5>
                        <input type="hidden" name="siparisNo" id="siparisNo" value="<?php echo $siparisNo; ?>">
                        <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
                        <input type="hidden" name="demand" id="demand" value="<?php echo $demand; ?>">

                    </div>
                </div>

                <!-- Firma Bilgileri -->
                <div class="form-group row">
                    <label for="company" class="col-md-4">
                        <font color="red">(*)</font>Firma :
                    </label>
                    <div class="input-group col-md-8">
                        <?php echo customer::getCustomerSelect('customers', $purchase->companyID ?? 0); ?>
                        <a href="index.php?p=new-customer" target="_blank"
                            class="btn btn-secondary btn-sm d-flex align-items-center" type="button"
                            data-tooltip="Yeni Firma Eklemek için tıklayınız!"><i class="fa fa-plus"></i></a>
                        </a>

                    </div>
                </div>
                <!-- Firma Bilgileri -->

                <!-- Termin Tarihi -->
                <div class="form-group row">
                    <label for="deadline" class="col-md-4">
                        <font color="red">(*)</font>Termin Tarihi:
                    </label>
                    <div class="col-md-8">
                        <input required class="form-control date-picker" type="text"
                            value="<?php echo $purchase->deadline ?? date("d-m-Y") ?>" name="deadline"
                            autocomplete="off" placeholder="gg-aa-yyyy">

                    </div>

                </div>
                <!-- Termin Tarihi -->

                <!-- Ödeme Vadesi -->
                <div class="form-group row">
                    <label for="vadeGun" class="col-md-4">
                        <font color="red">(*)</font> Ödeme Vadesi:
                    </label>
                    <div class="col-md-4">
                        <input type="text" required id="payPeriod" name="vadeGun" class="form-control"
                            autocomplete="off" placeholder="Gün giriniz!"
                            value="<?php echo $purchase->vadeGun ?? date("d-m-Y") ?>">
                    </div>
                    <div class="col-md-4">
                        <input type="text" readonly id="payment_date" name="payment_date" class="form-control"
                            value="<?php echo $purchase->payment_date ?? date("d-m-Y") ?>">
                    </div>
                </div>
                <!-- Ödeme Vadesi -->


                <!-- Açıklama -->
                <div class="form-group row">
                    <label class="col-md-4">
                        Açıklama 1 :
                    </label>
                    <div class="col-md-8">
                        <textarea name="description1" placeholder="Siparis formunda görünecek açıklama giriniz"
                            class="form-control" type="text"><?php echo $purchase->description1 ?? '' ?></textarea>
                    </div>
                </div>
                <!-- Açıklama -->

            </div>
            <!-- COLUMN ONE -->

            <!-- COLUMN TWO -->
            <div class="col-md-6">
                <!-- Satın Alma Durumu -->
                <div class="form-group row">
                    <div class="col-md-4 col-sm-12">
                        <label for="AlisFiyati">
                            Durumu :
                        </label>
                    </div>

                    <div class="col-sm-12 col-md-8">
                        <?php

                        $state = $purchase->state ?? 0;

                        echo Helper::selectState("state", $state);
                        ?>

                    </div>
                </div>
                <!-- Satın Alma Durumu -->
                <!-- Fatura Tarihi -->
                <div class="form-group row">
                    <div class="col-md-4 col-sm-12">
                        <label for="invoice_date">
                            Fatura Tarihi/Numarası :
                        </label>
                    </div>

                    <div class="col-sm-12 col-md-4">
                        <input type="text" class="form-control date-picker" name="invoice_date"
                            value="<?php echo $purchase->invoice_date ?? ''; ?>">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <input type="text" class="form-control" name="invoice_number"
                            value="<?php echo $purchase->invoice_number ?? ''; ?>">
                    </div>
                </div>
                <!-- Fatura Tarihi -->


                <!-- Para Birimi -->
                <div class="form-group row">
                    <div class="col-md-4 col-sm-12">
                        <label for="AlisFiyati">
                            Kur Türü :
                        </label>
                    </div>

                    <div class="col-sm-12 col-md-8">
                        <?php KurTuru('cur_type', $purchase->currency ?? '') ?>
                    </div>
                </div>
                <!-- Para Birimi -->

                <!-- Kur Bilgileri -->
                <div class="form-group row">
                    <div class="col-md-4 col-sm-12">
                        <label for="AlisFiyati">
                            Dolar / Euro :
                        </label>
                    </div>

                    <div class="col-sm-12 col-md-4">
                        <input type="text" readonly class="form-control" id="cur-Dollar" name="curDollar">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <input type="text" readonly class="form-control" id="cur-Euro" name="curEuro">
                    </div>
                </div>
                <!-- Kur Bilgileri -->

                <!-- Açıklama -->
                <div class="form-group row">
                    <label class="col-md-4">
                        Açıklama 2 :
                    </label>
                    <div class="col-md-8">
                        <textarea name="description2" class="form-control"
                            type="text"><?php echo $purchase->description2 ?? ''; ?></textarea>
                    </div>
                </div>
                <!-- Açıklama -->

            </div>
            <!-- COLUMN TWO -->
        </div>
    </div>


    <div class="content pd-20 bg-white border-radius-16 box-shadow mb-30">

        <?php

        $alisToplam = $purchase->TLTotal ?? "0.00"; // Alış toplamı
        $iskontoToplam = $purchase->iskonto ?? "0.00"; // İskonto toplamı
        $kdv = $purchase->Kdv ?? "20"; // KDV oranı
        $kdvToplam = $purchase->altToplam ?? "0.00"; // KDV dahil toplam tutar

        ?>
        <div class="row ml-0 mr-0 mb-30">
            <div class="pd-5 col-lg-3 col-md-6 col-sm-12 mb-5">
                <div class="sum-primary">

                    <label style="font-weight: 600;" for="">Tutar TL </label>
                    <label id="buy-tl" for="">
                        <?php echo $alisToplam ?>
                    </label>
                </div>
            </div>

            <div class="pd-5 col-lg-3 col-md-6 col-sm-12 mb-5">
                <div class="sum-warning">

                    <label style="font-weight: 600;" for="">KDV Oranı(%)</label>
                    <label id="kdv-rate" for="">
                        <?php echo $kdv ?>
                    </label>
                </div>
            </div>
            <div class="pd-5 col-lg-3 col-md-6 col-sm-12 mb-5">
                <div class="sum-success">

                    <label style="font-weight: 600;" for="">İskonto TL</label>
                    <label id="discount" for="">
                        <?php echo $iskontoToplam ?>
                    </label>
                </div>
            </div>

            <div class="pd-5 col-lg-3 col-md-6 col-sm-12 mb-5">
                <div class="sum-danger">

                    <label style="font-weight: 600;" for="">KDV Dahil TL</label>
                    <label name="lblTotalTL" id="lblTotalTL" for="">
                        <?php echo $kdvToplam ?>
                    </label>
                </div>
            </div>
        </div>

        <!-- Sipariş ürünleri -->

        <div class="row margin-5 pd-10 justify-content-between">
            <h4 class="text-blue">
                Ürün Bilgileri
            </h4>

        </div>
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 0;
                border: 1px solid #444;
            }

            .hack1 {
                display: table;
                table-layout: fixed;
                width: 100%;
            }

            .hack2 {
                display: table-cell;
                overflow-x: auto;
                width: 100%;
            }

            .table>thead {
                background-color: #eee;
                height: 60px;
            }

            .dark-mode .table>thead {
                background-color: #333;
            }
        </style>
        <div class="hack1">
            <div class="hack2">

                <table id="tProduct" class="table">
                    <thead>
                        <tr>
                            <th>Taşı</th>
                            <th>İşlem</th>
                            <th>Sıra</th>
                            <th>Stok Kodu</th>
                            <th>Ürün Adı</th>
                            <th>Miktar</th>
                            <th>Birim</th>
                            <th>Fiyat</th>
                            <th>Para Birimi</th>
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
                                <td style="width: 10px;"><a href="#" class="btn btn-sm"><i class="fa fa-arrows-alt"></i></a></td>

                                <td class="app-item-action">
                                    <a type="button" class="sil btn btn-sm btn-danger">Sil</a>
                                </td>

                                <!--Sırano-->
                                <td class="app-item-number">
                                    <input class="form-control" name="satirno[]" type="text" value="<?php echo $i; ?>">
                                </td>
                                <!--Sırano-->

                                <!-- Stok Kodu -->
                                <td class="app-item-stock"><input type="text" id="stokKodu<?php echo $i; ?>"
                                        value="<?php echo $item->stokKodu ?? ''; ?>" name="stokKodu[]" class="form-control"
                                        placeholder="Stok Kodu giriniz!">
                                </td>
                                <!-- Stok Kodu -->

                                <td class="app-item-name">
                                    <!-- Button trigger modal -->
                                    <div class="input-group m-0">

                                        <input type="text" class="urunAdi form-control" name="urunAdi[]"
                                            id="urunAdi<?php echo $i; ?>" value="<?php echo $item->product ?? ''; ?>"
                                            placeholder="Ürün adını giriniz!">
                                        <button type="button" id="<?php echo $i; ?>"
                                            class="btn btn-sm btn-info selectProduct" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop">
                                            <i class="fa fa-plus-circle"></i>
                                        </button>
                                    </div>



                                </td>
                                <!-- MİKTAR -->
                                <td class="app-item-amount">
                                    <input type="number" autocomplete="off" required id="amount" name="amount[]"
                                        value="<?php echo $item->amount ?? ''; ?>" class="Adet form-control">
                                </td>
                                <!-- MİKTAR -->

                                <!-- ÖLÇÜ BİRİMLERİ -->
                                <td class="app-item-unit">
                                    <?php OlcuBirimleri('unit[]', $item->unit ?? '', "required", "unit" . $i) ?>
                                </td>
                                <!-- ÖLÇÜ BİRİMLERİ -->

                                <!-- FİYAT -->
                                <td class="app-item-price">
                                    <input required id="price<?php echo $i; ?>" name="price[]" type="number"
                                        value="<?php echo $item->price ?? ''; ?>" class="form-control mr-1" autocomplete="off">

                                </td>
                                <!-- FİYAT -->

                                <!-- PARA BİRİMLERİ -->
                                <td class="app-item-cur">
                                    <?php echo Financial::getCurrencySelect("currency[]", $item->currency ?? '', "currency" . $i) ?>
                                </td>
                                <!-- PARA BİRİMLERİ -->
                            </tr>

                        <?php } ?>

                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="8">
                                <button type="button" id="addRow" class="btn float-left btn-sm btn-primary mt-3 mb-3">
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
    <div class="content pd-20 bg-white border-radius-16 box-shadow mb-15">
        <div class="row margin-5 pd-10 justify-content-between">
            <div class="float-left">
                <h4 class="text-blue">
                    Alt Toplamlar </h4>
            </div>

        </div>
        <div class="hack1">
            <div class="hack2">
                <table id="tblAltToplam" class="table">
                    <thead>
                        <th style="min-width:120px">Göster</th>
                        <th>Euro Toplam</th>
                        <th>Dolar Toplam</th>
                        <th>TL Toplam</th>
                        <th>İskonto Toplam</th>
                        <th>Kdv (%)</th>
                        <th>Toplam Tutar(TL)</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="sub-item-view">
                                <span class="badge badge-primary">Göster</span>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="EuroAlttoplam" id="EuroAlttoplam"
                                    value="<?php echo $purchase->EuroTotal ?? '0.00' ?>">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="DolarAlttoplam" id="DolarAlttoplam"
                                    value="<?php echo $purchase->DolarTotal ?? '0.00' ?>">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="TLAlttoplam" id="TLAlttoplam"
                                    value="<?php echo $purchase->TLTotal ?? '0.00' ?>">
                            </td>

                            <td>
                                <input type="number" autocomplete="off" class="form-control text-center" name="iskonto"
                                    value="<?php echo $purchase->iskonto ?? '0.00'; ?>" id="iskonto">
                            </td>
                            <td>

                                <?php KdvOranları("Kdv", $purchase->Kdv ?? 20) ?>
                            </td>
                            <td>
                                <input type="text" autocomplete="off" class="form-control text-center" name="altToplam"
                                    id="altToplamInput" value="<?php echo $purchase->altToplam ?? '0.00'; ?>">
                                <input type="hidden" id="araToplam" name="araToplam" value="">
                            </td>
                        </tr>
                    </tbody>
                </table>

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