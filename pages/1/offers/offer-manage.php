<?php
permcontrol('offeredit');

use App\Helper\Date;
use App\Helper\Helper;
use App\Helper\Financial;
use App\Helper\Security;
use App\Model\OfferModel;

$offerObj = new OfferModel();

$oid = $_GET['id'] ?? 0;
$offer = $offerObj->find($oid);

if ($oid != 0 && (!permtrue('template_offer_edit') && $offer->is_template == 1)) {
    header("Location: index.php?p=offers/list&sablon=true");
    exit;
}

$offer_number = $offer->offerNumber ?? Helper::generateNumber("offer", "TK");
$template_offer_number = $oid != 0 ? $offer->offerNumber : Helper::generateNumber("template_offer", "Ş");

$enc_id = Security::encrypt($oid);
?>
<form enctype="multipart/form-data" id="myForm" method="POST">
    <input type="hidden" class="form-control" name="offer_id" id="offer_id" value="<?php echo $oid; ?>">
    <input type="hidden" class="form-control" value="<?php echo $offer_number ?? 0; ?>">
    <div class="content pd-20 bg-white border-radius-16 box-shadow mb-15">
        <div class="clearfix justify-content-between">
            <div class="pull-left ">

                <style>
                    .btn-xls {
                        background-color: #1F8A70;
                    }

                    .btn-tl {
                        background-color: #4CB9E7;
                    }
                </style>
                <div class="form-group row ml-2">
                    <div class="d-flex flex-column">

                        <h4 class="text-blue m-2">
                            <?php echo $pdat['p_title'] ?>
                        </h4>

                        <div class="">


                            <a type="button" data-tooltip="Teklifi TL'ye Çevir" data-tooltip-location="right"
                                style="z-index: 1051;" id="convert_to_try"
                                class="float-left btn btn-tl mr-1 text-white"><i class="fa fa-dollar"></i></a>

                            <?php
                            $servicelink = 'javasctipt:';
                            $buttondisabled = 'disabled';
                            $statu = $offer->statu ?? 1;
                            if ($statu == '2') {
                                $servicelink = 'index.php?p=service/manage&oid=' . $oid;
                                $buttondisabled = '';
                            } ?>

                            <a type="button" id="servicebutton" aria-disabled="true" href="<?php echo $servicelink ?>"
                                data-tooltip="Servis Oluştur" data-tooltip-location="bottom"
                                class="float-left btn btn-info mr-1 text-white"><i class="fa fa-gear"></i></a>



                            <a type="button" href="/pages/1/offers/offer-to-xls.php?id=<?php echo $enc_id ?>"
                                data-tooltip="Teklifi Excele Aktar" data-tooltip-location="bottom"
                                class="float-left btn btn-xls mr-1 text-white"><i class="fa fa-file-excel-o"></i></a>

                            <?php
                            if (permtrue('offerview')) {
                                ?>
                                <a type="button" href="index.php?p=offer-view&id=<?php echo $oid; ?>" target="_blank"
                                    class="float-left btn btn-warning mr-1" data-tooltip="Teklifi Göster"
                                    data-tooltip-location="bottom"><i class="fa fa-file"></i></a>
                            <?php } ?>
                            <!-- <a type="button" data-tooltip="Teklifi Göster" data-tooltip-location="bottom"
                                class="float-left btn btn-warning mr-1"><i class="fa fa-file"></i></a> -->

                        </div>
                    </div>

                </div>

            </div>

            <div class="pull-right row ">

                <button type="button" id="btn_save_offer" data-tooltip="Teklifi Kaydet" data-tooltip-location="bottom"
                    class="btn btn-sm btn-primary text-white">
                    <i class="fa fa-save"></i>
                    Kaydet</button>

                <a href="index.php?p=offers/list" data-tooltip="Listeye Dön" data-tooltip-location="bottom"
                    class="btn btn-sm btn-secondary text-white ml-2 mr-3">
                    <i class="fa fa-list mr-1"></i>Listeye Dön</a>


            </div>
        </div>
        <div class="row">

            <div class="col-md-6 col-sm-12">
                <div class="form-group row">
                    <label for="company" class="col-md-4">
                        <font color="red">(*)</font>Teklif Numarası :
                    </label>
                    <div class="input-group col-md-8">
                        <h5 id="offerNumberLabel">

                            <?php

                            echo $offer_number;
                            ?>
                        </h5>
                    </div>
                    <input type="hidden" class="form-control" name="offerNumber" id="offerNumber"
                        value="<?php echo $offer_number ?>">

                    <input type="hidden" class="form-control" name="templateOfferNumber" id="templateOfferNumber"
                        value="<?php echo $template_offer_number ?>">

                </div>


                <!-- FİRMA ADI -->
                <div class="form-group row">
                    <label for="customers" class="col-sm-12 col-md-4 col-form-label">
                        <font color="red">(*)</font>Firma Adı:
                    </label>
                    <div class="input-group col-sm-12 col-md-8">
                        <select required name="customers" id="customers" title="Seçiniz..."
                            class="selectpicker form-control" data-style="bg-white" data-size="8"
                            data-live-search="true">
                            <?php
                            $customer_id = $offer->cid ?? 0;

                            $qct = $ac->prepare('SELECT * FROM customers ORDER BY id DESC');
                            $qct->execute();
                            while ($cscs = $qct->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                <option <?php echo $customer_id == $cscs['id'] ? ' selected' : '' ?>
                                    value="<?php echo $cscs['id']; ?>">
                                    <?php echo $cscs['company']; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                        <?php if (permtrue('customeradd')) { ?>
                            <a href="index.php?p=new-customer" target="_blank"
                                class="btn btn-info btn-sm d-flex align-items-center"
                                data-tooltip="Yeni Firma Eklemek için tıklayınız!">
                                <i class="fa fa-plus"></i></a>
                        <?php } ?>

                    </div>

                </div>


                <!-- FİRMA YETKİLİSİ -->
                <div class="form-group row">
                    <div class="col-md-4">
                        <label for="compAuths" class="col-form-label">
                            <font color="red">(*)</font>Firma Yetkilisi :
                        </label>
                    </div>
                    <div class="col-md-8">

                        <input type="text" class="form-control" placeholder="Yetkili ad soyad" id="compAuths"
                            name="compAuths" value="<?php echo $offer->company_authors ?? ''; ?>" required />
                        <!-- <button class="btn btn-info btn-sm" type="button" data-tooltip="Ekle"><i
                        class="fa fa-plus"></i></button> -->
                    </div>
                </div>


                <!-- ÖDEME VADESİ -->
                <div class="form-group row">
                    <div class="col-sm-12 col-md-4">
                        <label for="vade" class="col-form-label">Ödemesi Vadesi :</label>

                    </div>
                    <div class="col-sm-12 col-md-8">
                        <input type="text" id="payPeriod" name="payPeriod" class="form-control"
                            value="<?php echo $offer->payment_period ?? '' ?>" placeholder="Vade giriniz!">

                    </div>
                </div>

                <!-- TEKLİF KONUSU -->
                <div class="form-group row">
                    <div class="col-sm-12 col-md-4">
                        <label for="vade" class="col-form-label">Teklif Konusu :</label>

                    </div>
                    <div class="col-sm-12 col-md-8">
                        <input type="text" id="offer_subject" name="offer_subject" class="form-control"
                            value="<?php echo $offer->offer_subject ?? '' ?>" placeholder="Örn: Yeni Teklif">

                    </div>
                </div>

                <!-- TARİH -->
                <div class="form-group row">
                    <div class="col-md-4">
                        <label class="col-form-label">
                            <font color="red">(*)</font>Tarih :
                        </label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" id="offer_date" name="offer_date"
                            value="<?php echo $offer->offer_date ?? date('d.m.Y'); ?>" class="form-control date-picker"
                            placeholder="">
                    </div>
                </div>


                <!-- HAZIRLAYAN -->
                <div class="form-group row">
                    <div class="col-sm-12 col-md-4">
                        <label class="col-form-label">
                            <font color="red">(*)</font>Hazırlayan
                        </label>
                    </div>
                    <div class="col-sm-12 col-md-8">
                        <input readonly class="form-control"
                            value="<?php echo getUsername($offer->creativer ?? sesset("id")); ?>" type="text">
                    </div>

                </div>



                <!-- DOSYA -->
                <div class="form-group row">
                    <div class="col-md-4">
                        <label class="col-form-label mt-4">
                            <font color="red">(*)</font>Dosya:
                        </label>
                    </div>
                    <div class="input-group col-sm-12 col-md-8">
                        <?php
                        $offer_file = $offer->file ?? '';

                        ?>
                        <!-- EĞER DOSYA VARSA INPUT YOKSA FILE TİPİ OLUR -->
                        <?php $offer_file != '' ? $type = 'text' : $type = 'file'; ?>

                        <input type="<?php echo $type; ?>" id="offerFile" name="offerFile"
                            value="<?php echo $offer_file ?? '' ?>" class="form-control form-control-sm">

                        <?php if ($offer_file != '') {
                            ; ?>
                            <a type="button" id="downloadfile" href="files/offer/<?php echo $offer_file; ?>" target="_blank"
                                class="btn btn-info btn-sm d-flex align-items-center ml-1">Dosyayı İndir
                            </a>
                            <button id="deleteFile" onclick="DeleteFile(<?php echo $oid ?>)" type="button" target="_blank"
                                class="btn btn-danger btn-sm d-flex align-items-center ml-1" data-tooltip="Dosyayı Sil"><i
                                    class="fa fa-trash"></i></a>

                            <?php }
                        ; ?>
                    </div>
                </div>


                <!-- TEKLİF DURUMU -->
                <div class="form-group row">
                    <div class="col-md-4">
                        <label class="col-form-label mt-4">
                            <font color="red">(*)</font>Teklif Durumu:
                        </label>
                    </div>
                    <div class="col-md-8">
                        <?php
                        $offer_statu = $offer->statu ?? 1;
                        ?>
                        <select name="offerstatu" id="offerstatu" data-style="bg-white"
                            class="selectpicker form-control mt-4">
                            <option <?php echo $offer_statu == 1 ? ' selected' : '' ?> value="1">Bekleyen</option>
                            <option <?php echo $offer_statu == 2 ? ' selected' : '' ?> value="2">Tamamlandı</option>
                        </select>
                        <div id="create_service_div" class="mt-2"
                            style="<?php echo $offer_statu == 2 ? '' : 'display:none;'; ?>">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="createService"
                                    name="createService" value="1">
                                <label class="custom-control-label" for="createService">Otomatik Servis Oluştur</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- NOTLAR -->
                <div class="form-group row">
                    <div class="col-md-4">
                        <label>Notlar</label>
                    </div>
                    <div class="col-md-8">
                        <textarea name="description" value="<?php echo $offer->description ?? '' ?>"
                            placeholder="Teklif hakkında bilgilendirici nitelikte not ekleyiniz."
                            class="form-control"><?php echo $offer->description ?? ''; ?></textarea>
                    </div>
                </div>
                <?php if (permtrue('template_offer_create')) { ?>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label>Şablon Teklif Yap</label>
                        </div>
                        <div class="col-md-8">
                            <div class="custom-control custom-checkbox ml-3">
                                <?php
                                $checked = '';
                                $is_template = $offer->is_template ?? 0;
                                if (isset($is_template) && $is_template == 1) {
                                    $checked = 'checked';
                                }
                                ?>
                                <input class="custom-control-input" type="checkbox" value="<?php echo $is_template ?>"
                                    name="is_template" id="is_template" <?php echo $checked; ?> id="is_template">
                                <label class="custom-control-label" for="is_template">

                                </label>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- <>
            |
            |İKİNCİ KOLON
            |
            <> -->


            <!-- ÜST BİLGİ -->

            <?php

            ?>
            <div class="col-md-6 col-sm-12">
                <div class="form-group row">
                    <label for="company" class="col-md-4">
                        Üst Bilgi Seç:
                    </label>
                    <div class="input-group col-md-8">

                        <?php offerTemplate('offerHeader', $offer->offer_header ?? 12, 'Header'); ?>
                        <a href="index.php?p=offer-templates&type=Header" target="_blank"
                            class="btn btn-secondary btn-sm d-flex align-items-center" type="button"
                            data-tooltip="Yeni Şablon Eklemek için tıklayınız!" data-tooltip-location="left"><i
                                class="fa fa-plus"></i></a>
                    </div>
                </div>



                <!-- ALT BİLGİ -->
                <div class="form-group row">
                    <label for="offerFooter" class="col-md-4">
                        Alt Bilgi Seç:
                    </label>

                    <div class="input-group col-md-8">
                        <?php offerTemplate('offerFooter', $offer->offer_footer ?? 11, 'Footer'); ?>

                        <a href="index.php?p=offer-templates&type=Footer" target="_blank"
                            class="btn btn-secondary btn-sm d-flex align-items-center" type="button"
                            data-tooltip="Yeni Şablon Eklemek için tıklayınız!" data-tooltip-location="left"><i
                                class="fa fa-plus"></i></a>
                    </div>

                </div>
                <div class="form-group row">
                    <label for="offerFooterContent" class="col-md-4">
                        Alt Bilgi :
                    </label>
                    <div class="col-md-8">
                        <div id="offerFooterContent" class="offerFooterContent html-editor">
                            <textarea required name="offerFooterContent"
                                class="textarea_editor form-control border-radius-0" placeholder="Bir şeyler yaz ...">
                                <?php echo $offer->offer_footer_content ?? offerTemplateContent($offer->offer_footer ?? 11); ?>
                            </textarea>
                        </div>
                    </div>
                </div>

            </div>
        </div>



        <br>

    </div>


    <!-- TEKLİF KALEMLERİ ÖZET BİLGİ -->
    <div class="content pd-20 bg-white border-radius-16 box-shadow mb-15">
        <div class="row margin-5 pd-10 justify-content-between">
            <div class="float-left">
                <h4 class="text-blue">
                    Teklif Kalemleri</h4>
            </div>

        </div>
        <?php

        $alisToplam = $offer->tl_alis_toplam ?? 0;
        $satisToplam = $offer->tl_satis_toplam ?? 0;


        if (isset($satisToplam) && isset($alisToplam)) {
            $KarTL = $satisToplam - $alisToplam;
        }

        if (isset($satisToplam) && isset($alisToplam) && $alisToplam > 0) {
            $KarOrani = number_format(($satisToplam - $alisToplam) / $alisToplam * 100, 2);
        } else {
            $KarOrani = '0.00 TL';
        }

        //eğer alış tutarı 0 ve satış tutarı 0'dan büyükse kar oranı 100 olacak
        if ($alisToplam == 0 && $satisToplam > 0) {
            $KarOrani = '100';
        }
        ?>

        <!-- ÖZET ALANLARI -->
        <div class="row ml-0 mr-0 mb-30">
            <div class="pd-5 col-lg-3 col-md-6 col-sm-12 mb-5">
                <div class="sum-primary">

                    <label style="font-weight: 600;" for="">Alış TL</label>
                    <label id="buy-tl" for="">
                        <?php echo tlFormat($alisToplam ?? 0) ?>
                    </label>
                    <input type="hidden" name="buy-tl-input" id="buy-tl-input" value="<?php echo $alisToplam ?? 0 ?>">

                </div>
            </div>
            <div class="pd-5 col-lg-3 col-md-6 col-sm-12 mb-5">
                <div class="sum-success">

                    <label style="font-weight: 600;" for="">Satış TL</label>
                    <label id="sale-tl" for="">
                        <?php echo tlFormat($satisToplam ?? 0) ?>
                    </label>
                    <input type="hidden" name="sale-tl-input" id="sale-tl-input"
                        value="<?php echo $satisToplam ?? 0 ?>">
                </div>
            </div>
            <div class="pd-5 col-lg-3 col-md-6 col-sm-12 mb-5">
                <div class="sum-warning">

                    <label style="font-weight: 600;" for="">Kâr TL</label>
                    <label id="profit-tl" for="">
                        <?php echo tlFormat($KarTL ?? 0) ?>
                    </label>
                </div>
            </div>
            <div class="pd-5 col-lg-3 col-md-6 col-sm-12 mb-5">
                <div class="sum-danger">

                    <label style="font-weight: 600;" for="">Kâr Oranı</label>
                    <label name="profit-rate" id="profit-rate" for="">
                        <?php echo $KarOrani . ' %' ?>
                    </label>
                </div>
            </div>
        </div>
        <!-- ÖZET ALANLARI -->

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
        </style>

        <!-- TEKLİF KALEMLERİ TABLOSU -->
        <div class="hack1">
            <div class="hack2">
                <table id="kalem_ekle" class="table">
                    <thead>

                        <tr>
                            <th>Taşı</th>
                            <th>İşlem</th>
                            <th class="text-center">Sıra</th>
                            <th>Stok Kodu</th>
                            <th>Ürün/Malzeme</th>
                            <th><label for="amount[]" class="m-0">Miktar</label> </th>
                            <th>Birim</th>
                            <th class="text-center"><label for="price[]" class="m-0">Satış Fiyat</label> </th>
                            <th>Satış Para Birimi</th>
                            <th>Toplam Tutar</th>
                            <th class="text-center"><label for="price[]" class="m-0">Alış Fiyat</label> </th>
                            <th>Alış Para Birimi</th>
                        </tr>
                    </thead>

                    <tbody id="sortable">

                        <?php
                        $items = $ac->prepare('SELECT * FROM offermatters WHERE oid = ? ORDER BY satirno');
                        $items->execute(array($oid));
                        $satirNo = 0;
                        while ($item = $items->fetch(PDO::FETCH_ASSOC)) {
                            $satirNo += 1;
                            ?>
                            <tr class="ui-state-default">
                                <?php
                                $stokKodu = $item['stokKodu'];
                                $urunAdi = $item['title'];
                                $amount = $item['amount'];
                                $unit = $item['unit'];
                                $buyprice = $item['buyprice'];
                                $buycur = $item['buycur'];
                                $saleprice = $item['saleprice'];
                                $salecur = $item['salecur'];
                                $rowTotal = $item['total_price'];

                                include 'offer-row.php'
                                    ?>

                            </tr>
                            <?php
                        }
                        if ($satirNo == 0) {
                            $satirNo = 1;
                            $stokKodu = '';
                            $urunAdi = '';
                            $buyprice = '';
                            $saleprice = '';
                            $unit = '';
                            $amount = '';
                            $buycur = '';
                            $salecur = '';
                            $rowTotal = '0.00';

                            include_once 'offer-row.php';
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="10">
                                <button type="button" id="ekle" class="btn float-left btn-sm btn-primary mt-3 mb-3">
                                    <i class="fa fa-plus"></i> Yeni Satır
                                </button>

                            </td>
                        </tr>

                    </tfoot>

                </table>

            </div>
        </div>
    </div>
    <input type="hidden" id="rowNumberId" value="<?php echo $satirNo + 1 ?>">



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
                        <th style="min-width:120px">Teklifi Göster</th>
                        <th>Euro</th>
                        <th>Dolar</th>
                        <th>TL</th>

                    </thead>
                    <tbody>

                        <tr>

                            <!-- **********************ALT TOPLAM *****************************-->
                            <td>
                                Alt Toplam

                            </td>
                            <td>
                                <input type="text" readonly class="form-control text-center" name="euro_alt_toplam"
                                    id="euro_alt_toplam" value="<?php echo $offer->euro_alt_toplam ?? 0 ?>">
                            </td>
                            <td>
                                <input type="text" readonly class="form-control text-center" name="dolar_alt_toplam"
                                    id="dolar_alt_toplam" value="<?php echo $offer->dolar_alt_toplam ?? 0 ?>">
                            </td>
                            <td>
                                <input type="text" readonly class="form-control text-center" name="tl_alt_toplam"
                                    id="tl_alt_toplam" value="<?php echo $offer->tl_alt_toplam ?? 0 ?>">
                            </td>

                        </tr>
                        <!-- **********************ALT TOPLAM *****************************-->

                        <!-- ////////////////////////////////////////////////////////////// -->

                        <!-- *************************İSKONTO *****************************-->
                        <tr>
                            <td>
                                İskonto
                            </td>

                            <td>
                                <input type="number" autocomplete="off" class="form-control text-center"
                                    name="euro_iskonto" value="<?php echo $offer->euro_iskonto ?? 0 ?>"
                                    id="euro_iskonto">
                            </td>
                            <td>
                                <input type="number" autocomplete="off" class="form-control text-center"
                                    name="dolar_iskonto" value="<?php echo $offer->dolar_iskonto ?? 0 ?>"
                                    id="dolar_iskonto">
                            </td>
                            <td>
                                <input type="number" autocomplete="off" class="form-control text-center"
                                    name="tl_iskonto" value="<?php echo $offer->tl_iskonto ?? 0 ?>" id="tl_iskonto">
                            </td>
                        </tr>
                        <!-- **********************İSKONTO *****************************-->

                        <!-- ////////////////////////////////////////////////////////////// -->

                        <!-- *************************ARA TOPLAM *****************************-->

                        <tr>
                            <td>
                                Ara Toplam
                            </td>
                            <td>
                                <input type="text" readonly class="form-control text-center" name="euro_ara_toplam"
                                    value="<?php echo $offer->euro_ara_toplam ?? 0 ?>" id="euro_ara_toplam">
                            </td>
                            <td>
                                <input type="text" readonly class="form-control text-center" name="dolar_ara_toplam"
                                    value="<?php echo $offer->dolar_ara_toplam ?? 0 ?>" id="dolar_ara_toplam">
                            </td>
                            <td>
                                <input type="text" readonly class="form-control text-center" name="tl_ara_toplam"
                                    value="<?php echo $offer->tl_ara_toplam ?? 0 ?>" id="tl_ara_toplam">
                            </td>
                        </tr>
                        <!-- **********************ARA TOPLAM *****************************-->

                        <!-- ////////////////////////////////////////////////////////////// -->

                        <!-- *************************KDV *****************************-->
                        <tr>
                            <td>

                                <div class="d-flex">

                                    <Label class="mr-2">Kdv</Label> <?php KdvOranları('Kdv', $offer->Kdv ?? 20) ?>
                                </div>

                            </td>
                            <td>
                                <input type="text" autocomplete="off" class="form-control text-center" name="euro_kdv"
                                    readonly value="<?php echo $offer->euro_kdv ?? 0 ?>" id="euro_kdv">
                            </td>
                            <td>
                                <input type="text" autocomplete="off" class="form-control text-center" name="dolar_kdv"
                                    readonly value="<?php echo $offer->dolar_kdv ?? 0 ?>" id="dolar_kdv">
                            </td>
                            <td>
                                <input type="text" autocomplete="off" class="form-control text-center" name="tl_kdv"
                                    readonly value="<?php echo $offer->tl_kdv ?? 0 ?>" id="tl_kdv">
                            </td>
                        </tr>

                        <!-- **********************KDV *****************************-->

                        <!-- ////////////////////////////////////////////////////////////// -->

                        <!-- *************************KDVLİ TOPLAM *****************************-->
                        <tr>
                            <td>
                                KDV'li Toplam
                            </td>
                            <td>
                                <input type="text" readonly class="form-control text-center" name="euro_kdvli_toplam"
                                    value="<?php echo $offer->euro_kdvli_toplam ?? 0 ?>" id="euro_kdvli_toplam">
                            </td>
                            <td>
                                <input type="text" readonly class="form-control text-center" name="dolar_kdvli_toplam"
                                    value="<?php echo $offer->dolar_kdvli_toplam ?? 0 ?>" id="dolar_kdvli_toplam">
                            </td>
                            <td>
                                <input type="text" readonly class="form-control text-center" name="tl_kdvli_toplam"
                                    value="<?php echo $offer->tl_kdvli_toplam ?? 0 ?>" id="tl_kdvli_toplam">
                            </td>
                        </tr>
                        <!-- **********************KDVLİ TOPLAM *****************************-->

                        <!-- ////////////////////////////////////////////////////////////// -->

                        <!-- *************************KUR BİLGİLERİ *****************************-->

                        <tr>
                            <td>
                                <div class="d-flex">
                                    <Label class="mr-2">Kur</Label>
                                    <?php KurTuru('currency', $offer->currency ?? "Döviz Alış") ?>
                                </div>
                            </td>
                            <td>
                                <input id="cur-Euro" name="cur-Euro" value="<?php echo $offer->curEuro ?? 0 ?>" readonly
                                    value="" class="form-control text-center" type="text">

                            </td>
                            <td>
                                <input id="cur-Dollar" name="cur-Dollar" value="<?php echo $offer->curDollar ?? 0 ?>"
                                    readonly value="" class="form-control text-center" type="text">
                            </td>
                            <td>

                            </td>
                        </tr>
                        <!-- **********************KUR BİLGİLERİ *****************************-->

                        <!-- ////////////////////////////////////////////////////////////// -->

                        <!-- *************************TOPLAM TL KARŞILIK *****************************-->
                        <tr>
                            <td>

                                Toplam Tutar
                            </td>
                            <td colspan="4">
                                <input type="text" readonly class="form-control text-center" name="tl_toplam_karsilik"
                                    value="<?php echo tlFormat($offer->tl_toplam_karsilik ?? 0) ?? 0 ?>"
                                    id="tl_toplam_karsilik">
                            </td>
                        </tr>
                        <!-- *************************TOPLAM TL KARŞILIK *****************************-->
                    </tbody>
                </table>

            </div>
        </div>
    </div>



    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="pd-20 bg-white border-radius-16 box-shadow mb-10">
                <div class="row mb-3">
                    <div class="col-md-2">
                        Oluşturan
                    </div>
                    <div class="col-md-10">
                        <?php echo getUserName($offer->creativer ?? 0); ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2">
                        Oluşturma Tarihi
                    </div>
                    <div class="col-md-10">
                        <?php echo date_tr($offer->created_at ?? ''); ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2">
                        Güncelleyen
                    </div>
                    <div class="col-md-10">
                        <?php echo getUserName($offer->updater ?? 0); ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2">
                        Güncelleme Tarihi
                    </div>
                    <div class="col-md-10">
                        <?php echo ($offer->updated_at ?? ''); ?>
                    </div>
                </div>


            </div>
        </div>

    </div>

    <!-- Tablonun içine eklendiği zaman satır silince diğer satırlarda çalışmıyor -->
    <?php include_once 'offer-modal.php'; ?>
</form>

<!--buradan başlıyor-->
<script src="../../include/js/offer.js"></script>
<script src="pages/1/offers/offer.js?v=<?php echo filemtime("pages/1/offers/offer.js"); ?>"></script>

<script>
    $(document).ready(function () {
        updateAltToplam();

        // Teklif durumu tamamlandı seçildiğinde otomatik servis oluştur checkbox'ını göster
        $("#offerstatu").on("change", function () {
            if ($(this).val() == 2) {
                $("#create_service_div").show();
            } else {
                $("#create_service_div").hide();
                $("#createService").prop("checked", false);
            }
        });
    });

    $(function () {

        //date-picker ile çakıştığı için sortable kütüphaesi kullanıldı
        // $("#sortable").sortable({
        //     update: function(event, ui) {
        //         // Sıralama sonrası numaralandırma
        //         $("#kalem_ekle tbody tr").each(function(index) {
        //             // Numara hücresini güncelle (örneğin ilk <td>)
        //             $(this).find("input[name='satirno[]']").val(index + 1);
        //         });
        //     }
        // });


        var el = document.getElementById('sortable');
        var sortable = Sortable.create(el, {
            onUpdate: function (/**Event*/evt) {
                // Sıralama sonrası numaralandırma
                $("#kalem_ekle tbody tr").each(function (index) {
                    // Numara hücresini güncelle (örneğin ilk <td>)
                    $(this).find("input[name='satirno[]']").val(index + 1);
                });
            }
        });
    });

</script>