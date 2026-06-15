<?php

permcontrol("productedit");


use App\Model\ProductModel;
use App\Helper\Security;

$Products = new ProductModel();

$enc_id = $_GET["id"] ?? 0;
$id = isset($_GET["id"]) ? Security::decrypt($_GET["id"]) : 0;

$product = $Products->find($id);

// Yeni kayıt değil ve ürün yoksa listeye dön
if (!$product && $id != 0) {
    header("Location: index.php?p=products/list");
}


?>


<div class="content pd-20 bg-white border-radius-16 box-shadow mb-30">
    <div class="clearfix">
        <div class="pull-left">
            <h4 class="text-blue">
                <?php echo $pdat["p_title"]; ?>
            </h4>
            <p class="mb-30 font-14">Sayfadaki <font color="red">(*)</font> yıldız ile belirtilen alanları boş
                bırakmayın..<br></p>
        </div>

        <div class="form-group">
            <div class="row float-right m-2">

                <button id="submitButton" data-tooltip="Kaydet" data-tooltip-location="bottom"
                    class="btn btn-primary btn-sm text-white">
                    <i class="fa fa-save"></i>
                    Kaydet</button>
                <a class="btn btn-secondary btn-sm ml-1 text-white" href="index.php?p=products/list"
                    data-tooltip="Listeye Dön">
                    <i class="fa fa-list p-1"></i>Ürün Listesi</a>

            </div>

        </div>
    </div>

    <form action="" method="POST" id="productForm">
        <!-- <form method="POST" action="index.php?p=new-product"> -->

        <input type="hidden" name="id" id="id" class="form-control mb-3" value="<?php echo $enc_id ?>">
        <div class="form-group row">


            <div class="col-md-2 col-sm-12">
                <label for="urunAdi">
                    <font color="red">(*)</font>Ürün/Hizmet Adı
                </label>
            </div>
            <div class="col-md-4 col-sm-12">
                <input required name="urunAdi" value="<?php echo $product->Adi ?? '' ?>" class="form-control" type="text">
            </div>

            <div class="col-md-2 col-sm-12">
                <label for="StokKodu">
                    Stok Kodu :
                </label>
            </div>
            <div class="col-md-4 col-sm-12">
                <input required name="StokKodu" value="<?php echo $product->StokKodu ?? '' ?>" class="form-control"
                    type="text">
            </div>

        </div>

        <div class="form-group row mb-3">


            <div class="col-md-2 col-sm-12">
                <label for="Birimi">
                    <font color="red">(*)</font>Birimi :
                </label>
            </div>
            <div class="col-sm-12 col-md-4">
                <?php OlcuBirimleriValID('Birimi', $product->Birimi ?? 0) ?>
            </div>

        </div>



        <div class="form-group row mb-3">
            <div class="col-md-2 col-sm-12">
                <label for="AlisFiyati">
                    Alış Fiyat :
                </label>
            </div>
            <div class="col-md-2 col-sm-12">
                <input name="AlisFiyati" value="<?php echo $product->AlisFiyati ?? '' ?>" class="form-control" type="text">
            </div>

            <div class="col-sm-12 col-md-2">
                <?php ParaBirimleri('AlisParaBirimi', $product->AlisParaBirimi ?? '', "cur") ?>
            </div>


            <div class="col-md-2 col-sm-12">
                <label for="SatisFiyati">
                    Satış Fiyat :
                </label>
            </div>
            <div class="col-md-2 col-sm-12">
                <input name="SatisFiyati" value="<?php echo $product->SatisFiyati ?? '' ?>" class="form-control" type="text">
            </div>

            <div class="col-sm-12 col-md-2">
                <?php ParaBirimleri('SatisParaBirimi', $product->SatisParaBirimi ?? '', "cur") ?>
            </div>
        </div>



        <div class="form-group row">
            <div class="col-md-2 col-sm-12">
                <label for="aciklama">
                    Açıklama :
                </label>
            </div>
            <div class="col-md-4 col-sm-12">

                <textarea name="aciklama" class="form-control"
                    type="textarea"><?php echo $product->Aciklama ?? '' ?></textarea>
            </div>


        </div>

    </form>
    <button class="border accordion">Detay</button>
    <div class="acordion-panel">
        <p>Eklenme Tarihi :
            <?php echo $product->OlusturmaTarihi ?? '' ?>
        </p>
    </div>

</div>


</div>