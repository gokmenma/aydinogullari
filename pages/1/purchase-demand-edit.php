<?php
permcontrol("purchase-demand-add");

use App\Helper\Helper;

$pid = $_GET['id']; // GET parametresinden şifrelenmiş ID'yi al

if ($_POST) {
    $aciliyet = @$_POST["aciliyet"];
    $altToplam = @$_POST["altToplam"];
    $description = @$_POST["description"];
    $curEuro = @$_POST["curEuro"];
    $DolarTotal = @$_POST["DolarAlttoplam"];
    $EuroTotal = @$_POST["EuroAlttoplam"];
    $TLTotal = @$_POST["TLAlttoplam"];
    $Kdv = @$_POST["Kdv"];
    $iskonto = @$_POST["iskonto"];
    $ToplamTL = @$_POST["altToplam"];
    $updater = $_SESSION["lid"];
    $state = @$_POST["state"];

    // Ürün Bilgileri
    $urunAdi = $_POST['urunAdi'];
    $stokKodu = $_POST["stokKodu"];
    $amounts = $_POST["amount"];
    $units = $_POST["unit"];
    $buyprices = $_POST["buyprice"];
    $buycur = $_POST["buycur"];
    $rowdescription = $_POST["rowdescription"];
    $type = 1;


    // if (
    //     $altToplam < 1 || $urunAdi == null
    // ) {
    //     header("Location: index.php?p=purchase-demand-new&st=empties");
    //     exit();
    // }



    try {

        $insq = $ac->prepare("UPDATE purchases SET  aciliyeti = ? ,
													description1 = ? ,creator = ? , altToplam = ? ,
													DolarTotal = ? ,EuroTotal = ? ,TLTotal = ? ,ToplamTL =? , 
													state= ? , type= ? WHERE id = ? ");
        $insq->execute(
            array(
                $aciliyet,
                $description,
                sesset("id"),
                $altToplam,
                $DolarTotal,
                $EuroTotal,
                $TLTotal,
                $ToplamTL,
                $state,
                $type,
                $pid
            )
        );
        $lastid = $ac->lastInsertId();
        // Veritabanı işlemleri
        if ($lastid != null) {
            //Tablodaki tüm ürünleri sil ve yeni ürünleri ekle
            $sql = $ac->prepare("DELETE FROM purchase_items WHERE purID = ?");
            $sql->execute(array($pid));

            for ($i = 0; $i < count($urunAdi); $i++) {
                $insq = $ac->prepare("INSERT INTO purchase_items SET purID = ?, 
																stokKodu = ? ,
																product = ? , 
																amount = ? , 
																unit = ? , 
																price = ? ,
																currency = ? ,
                                                                rowdescription = ?");
                $insq->execute(array(
                    $pid,
                    $stokKodu[$i],
                    $urunAdi[$i],
                    $amounts[$i],
                    $units[$i],
                    $buyprices[$i],
                    $buycur[$i],
                    $rowdescription[$i]
                ));
            }
        }
        if ($insq) {
            header("Location: index.php?p=purchase-demand-edit&st=newsuccess&id=" . $pid);
        }

    } catch (PDOException $e) {
        echo "Hata : " . $e->getMessage();
    }

}


$query = $ac->prepare("SELECT * FROM purchases where id = ?");
$query->execute(array($pid));
$result = $query->fetch(PDO::FETCH_ASSOC);

if (@$_GET["st"] == "empties") {

    showAlert('alert', "(*) ile işaretli alanları boş bırakmadan tekrar deneyin.");
}
if (@$_GET["st"] == "newsuccess") {

    showAlert('success', "Bilgiler kaydedildi.");

}
if (@$_GET["st"] == "numericerror") {

    showAlert('warning', "Fiyat kısmına sadece rakamlardan oluşan değer girebilirsiniz.");
}
?>
<style>
    .input-group {
        margin-bottom: 0px !important;
    }
</style>

<form enctype="multipart/form-data" method="POST" id="myForm">
    <div class="content pd-20 bg-white border-radius-16 box-shadow mb-10">
        <div class="clearfix">
            <div class="pull-left">
                <h4 class="text-blue">
                    <?php echo $pdat["p_title"]; ?>
                </h4>
                <p class="mb-30 font-14">Sayfadaki <font color="red">(*)</font> yıldız ile belirtilen alanları boş
                    bırakmayın..<br>
                </p>
            </div>
            <div class="float-right">
                <button id="submitButton" onclick="validateForm()" data-tooltip="Kaydet" data-tooltip-location="bottom"
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

                <!-- Siparis Numarası -->
                <div class="form-group row">
                    <label for="" class="col-md-4">
                        <font color="red">(*)</font>Sipariş Numarası :
                    </label>
                    <div class="input-group col-md-8">
                        <h5>
                            <?php echo $result["siparisNo"]; ?>
                        </h5>
                    </div>
                </div>

                <!-- Siparis Numarası -->

                <!-- Firma Bilgileri -->
                <div class="form-group row">
                    <label for="customer" class="col-md-4">
                        Firma :
                    </label>
                    <div class="input-group col-md-8">

                        <?php customers("customer", $result["companyID"], ""); ?>

                    </div>
                </div>
                <!-- Firma Bilgileri -->

                <!-- Aciliyeti -->
                <div class="form-group row">
                    <div class="col-md-4 col-sm-12">
                        <label for="aciliyet">
                            Aciliyet Durumu :
                        </label>
                    </div>

                    <div class="col-sm-12 col-md-8">
                        <?php aciliyet_durumu('aciliyet', $result["aciliyeti"]) ?>
                    </div>
                </div>
                <!-- Aciliyeti-->



            </div>
            <!-- COLUMN ONE -->

            <!-- COLUMN TWO -->
            <div class="col-md-6">

                <!-- Para Birimi -->
                <div class="form-group row">
                    <div class="col-md-4 col-sm-12">
                        <label for="AlisFiyati">
                            Kur Türü :
                        </label>
                    </div>

                    <div class="col-sm-12 col-md-8">
                        <?php KurTuru('currency', $result["currency"]) ?>
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
            
                <div class="form-group row">
                    <div class="col-md-4 col-sm-12">
                        <label for="AlisFiyati">
                            Durumu : 
                        </label>
                    </div>

                    <div class="col-sm-12 col-md-8">

                        <?php

                        $state = $result["state"] ?? 1;

                        echo Helper::selectState("state", $state);
                        ?>

                    </div>
                </div>
                <!-- Kur Bilgileri -->
            </div>
            <!-- COLUMN TWO -->

        </div>
        <!-- Açıklama -->
        <div class="form-group row">
            <label class="col-md-2">
                Açıklama :
            </label>
            <div class="col-md-10">
                <textarea name="description" value="" placeholder="Talep formunda görünecek açıklama giriniz"
                    class="form-control" type="text"><?php echo $result["description1"]; ?></textarea>
            </div>
        </div>
        <!-- Açıklama -->
    </div>


    <div class="content pd-20 bg-white border-radius-16 box-shadow mb-10">

        <?php

        $alisToplam = "0.00";
        $iskontoToplam = "0.00";
        $kdv = "0.00";
        $kdvToplam = "0";

        ?>

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
                /* background-color: #111; */
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
                            <th style="width:300px">Açıklama</th>
                        </tr>
                    </thead>

                                     <tbody id="sortable">

                        <tr>
                            <?php

                            $sql = $ac->prepare("SELECT * FROM purchase_items WHERE purID = ?");
                            $sql->execute(array($pid));
                            $satirNo = 0;
                            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                                $satirNo += 1;
                                ?>
                            <tr>
                                <?php
                                $stokKodu = $row["stokKodu"];
                                $urunAdi = $row["product"];
                                $buyprice = $row["price"];
                                $unit = $row["unit"];
                                $amount = $row["amount"];
                                $buycur = $row["currency"];
                                $rowdescription = $row["rowdescription"];
                                include "purchase-demand-row.php";
                                ?>
                            </tr>
                        <?php } ?>

                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="10">
                                <button type="button" id="addRow" class="btn float-left btn-sm btn-primary mt-3 mb-3">
                                    <i class="fa fa-plus"></i> Yeni Satır
                                </button>

                            </td>
                        </tr>

                    </tfoot>
                </table>
                <input type="hidden" id="rowNumberId" value="<?php echo $satirNo + 1 ?>">

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
                        <th>Kdv (%)</th>
                        <th>İskonto Toplam</th>
                        <th>Toplam Tutar(TL)</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="sub-item-view">
                                <span class="badge badge-primary">Göster</span>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="EuroAlttoplam" id="EuroAlttoplam"
                                    value="<?php echo $result["EuroTotal"] ?>">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="DolarAlttoplam" id="DolarAlttoplam"
                                    value="<?php echo $result["DolarTotal"] ?>">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="TLAlttoplam" id="TLAlttoplam"
                                    value="<?php echo $result["TLTotal"] ?>">
                            </td>

                            <td>
                                <input type="number" autocomplete="off" class="form-control text-center" name="iskonto"
                                    value="" id="iskonto">
                            </td>
                            <td>

                                <?php KdvOranları("Kdv", $result["Kdv"]) ?>
                            </td>
                            <td>
                                <input type="text" autocomplete="off" class="form-control text-center" name="altToplam"
                                    id="altToplamInput" value="<?php echo $result["altToplam"] ?>">
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</form>


<script src="../../include/js/purchase.js"></script>
<script>
    $(document).ready(function () {

        getCurrencyData();
        $("table").on("input change", "tr input, tr select", function () {
            updateToplamPurchase();
        });

        $("#tProduct").on("click", ".sil", function (e) {
            e.preventDefault();
            $(this).closest("tr").remove();
            updateToplamPurchase();
        })

        $("#addRow").click(function () {
            var sayac = $("#rowNumberId");
            purchaseRowAdd(sayac.val());
            sayac.val(parseInt(sayac.val(), 10) + 1);
        })

        $("#currency").change(function () {
            getCurrencyData();
        })

        $("[id^='buycur']").each(function () {
            $(this).on("change", function () {
                updateToplamPurchase();
            });
        });


        $("#payment_period").on("keyup", function () {
            var paymentDays = parseInt($("#payment_period").val());
            var futureDate = new Date();
            futureDate.setDate(futureDate.getDate() + paymentDays);
            var formattedDate = formatDate(futureDate);
            $("#payment_date").val(formattedDate);
        });

    });
</script>
<script>
    $(document).on('click', '.selectProduct', function () {
        var buttonId = $(this).attr('id');
        //var idNumarasi = buttonId.replace('rowID', '');
        $('#rowID').val(buttonId);


    });
</script>

<script>
    $(function() {
       // $("#sortable").sortable();
    
      
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