<?php

$type = @$_GET["type"];
$id = $_GET["id"];

if ($_POST) {


    $report_number = $_POST["report_number"];
    $servisno = $_POST["servisno"];
    $customer_id = $_POST["customer"];
    $test_date = $_POST["test_date"];
    $notes = $_POST["notes"];
    $updater = sesset("id");
    $update_Date = date("Y-m-d H:i:s");


    // SATIRLARDAKİ VERİLER TANIMLANIYOR
    $testno = $_POST["testno"];
    $cihazSayisi = count($testno);

    $kg = $_POST["kg"];
    $cinsi = $_POST["cinsi"];
    $imalatci_firma = $_POST["imalatci_firma"];
    $imal_tarihi = $_POST["imal_tarihi"];
    $serino = $_POST["serino"];
    $tse_belgesi = $_POST["tse_belgesi"];
    $yuzey_durumu = $_POST["yuzey_durumu"];
    $sizdirmazlik_deneyi = $_POST["sizdirmazlik_deneyi"];
    $esneme_deneyi = $_POST["esneme_deneyi"];
    $things = $_POST["things"];



    if (!$customer && $servisno != null && $report_number != null) {
        try {


            $sql = $ac->prepare("UPDATE reports SET report_number = ?, 
                                                         report_type = ?, 
                                                         isemrino= ?, 
                                                         customer_id = ?, 
                                                         test_date = ?, 
                                                         notes = ?, 
                                                         updater = ? ,
                                                         update_time = ? WHERE id = ?");
            $sql->execute(array($report_number, 2, $servisno, $customer_id, $test_date, $notes, $updater, $update_Date, $id));


            header("Location: index.php?p=reports/hst/report-edit-hst&st=newsuccess&id=" . $id);

            try {


                if ($cihazSayisi > 0) {
                    $delquery = $ac->prepare("DELETE FROM report_hst_content WHERE report_id = ?");
                    $delquery->execute(array($id));
                    for ($i = 0; $i < $cihazSayisi; $i++) {


                        $rowquery = $ac->prepare("INSERT INTO report_hst_content SET 
                                                                            report_id = ? ,
                                                                            testno = ? , kg = ? , cinsi = ? , 
                                                                            imalatci_firma = ? , imal_tarihi = ? , serino = ? , 
                                                                            tse_belgesi = ? ,yuzey_durumu = ? ,sizdirmazlik_deneyi = ? , 
                                                                            esneme_deneyi = ? ,things = ?
                                                                            ");
                        $rowquery->execute(
                            array(
                                $id,
                                $testno[$i],
                                $kg[$i],
                                $cinsi[$i],
                                $imalatci_firma[$i],
                                $imal_tarihi[$i],
                                $serino[$i],
                                $tse_belgesi[$i],
                                $yuzey_durumu[$i],
                                $sizdirmazlik_deneyi[$i],
                                $esneme_deneyi[$i],
                                $things[$i]
                            )
                        );
                    }
                }
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
if ($_GET["st"] == "newsuccess") {
    showAlert("success", "İşlem Başarı ile tamamlandı!");

}


// RAPOR BİLGİLERİ GETİRİLİR
$sql = $ac->prepare("SELECT * FROM reports WHERE id = ?");
$sql->execute(array($id));
$report = $sql->fetch(PDO::FETCH_ASSOC);

?>

<form enctype="multipart/form-data" id="myForm" method="POST">
    <div class="content pd-20 bg-white border-radius-16 box-shadow mb-20">
        <div class="clearfix mb-20">
            <div class="pull-left">
                <h5 class="text-blue">Hidrostatik Test Raporu</h5>
                <p class="font-14"> </p>
            </div>
            <div class="float-right">
                <button id="submitButton" onclick="validateForm()" data-tooltip="Kaydet" data-placement="bottom"
                    class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Kaydet</button>
                <a href="index.php?p=reports/hst/report-view-hst&id=<?php echo $id ?>" target="_blank"
                    class="btn btn-sm btn-danger" data-tooltip="Raporu Göster" data-tooltip-location="bottom"><i
                        class="fa fa-file-pdf-o"></i>
                </a>
                <a href="index.php?p=reports/reports" class="btn btn-sm btn-secondary"><i class="fa fa-list"></i>
                    Listeye Dön</a>
            </div>
        </div>

        <div class="row">
            <!-- 1. KOLON -->


            <!-- RAPOR NO -->
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-md-4" for="">Rapor No :</label>
                    <div class="col-md-8">
                        <input type="text" name="report_number" class="form-control" readonly
                            value="<?php echo $report["report_number"] ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4" for="">Tüp Sahibi Firma :</label>
                    <div class="col-md-8">
                        <?php customers("customer", $report["customer_id"]) ?>
                    </div>
                </div>
            </div>
            <!-- RAPOR NO -->

            <!-- 1. KOLON SONU -->


            <!-- 2. KOLON -->
            <div class="col-md-6">
                <div class="form-group row">
                    <label for="servisno" class="col-md-4" for="">Servis Fiş No :</label>
                    <div class="col-md-8">
                        <input required type="text" name="servisno" class="form-control"
                            value="<?php echo $report["isemrino"] ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="test_date" class="col-md-4" for="">Deney Tarihi :</label>
                    <div class="col-md-8">
                        <input required type="text" autocomplete="off" name="test_date" class="form-control date-picker"
                            placeholder="Deney Tarihini giriniz!" value="<?php echo $report["test_date"] ?>">
                    </div>
                </div>
            </div>
            <!-- 2. KOLON -->
        </div>
        <!-- RAPOR ALTI AÇIKLAMA -->
        <div class="form-group row">
            <label for="company" class="col-md-2">
                Rapor Altı Açıklama
            </label>
            <div class="col-md-10">
                <div class="html-editor">
                    <textarea style="height:100px; resize:vertical" name="notes" class="textarea_editor form-control"
                        placeholder="Raporun altına yazılacak açıklamayı yazınız"><?php echo $report["notes"] != '' ? $report["notes"] : "Yukarıda künyesi belirtilen {cihazSayisi} Adet tüp / tüplerin 16-HYB-1004 Nolu 11.11.2010 Tarihli TSE HİZMET YETERLİLİK BELGESİ' ne dayanılarak bu rapor hazırlanmıştır." ?></textarea><br>
                </div>
            </div>
        </div>
        <!-- RAPOR ALTI AÇIKLAMA -->
    </div>

    <div class="content pd-20 bg-white border-radius-16 box-shadow mb-30">
        <div class="clearfix mb-30">
            <div class="pull-left">
                <h4 class="text-blue">
                    Cihaz Kontrol Bilgileri
                </h4>

            </div>

        </div>
        <!-- TABLO -->
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
                margin-bottom: 100px;
            }

            .hack2 {
                display: table-cell;
                overflow-x: auto;
                width: 100%;

            }

    

            .rpr-date,
            #cihazno,
            .date-input {
                min-width: 120px;
            }

            .region {
                min-width: 150px;
            }

            .things {
                min-width: 240px;
            } 
            select{
                min-width: 120px;
            }
        </style>
        <div class="hack1">
            <div class="hack2">
                <table id="hstTable" class="table">
                    <thead>
                    <tr>
                      
                        <tr class="text-center">

                            <th>İşlem</th>
                            <th>Test No</th>
                            <th>Kg</th>
                            <th>Cinsi</th>
                            <th>Tüp İmalatcı Firma</th>
                            <th>Tüp İmal Tarihi</th>
                            <th>Seri No</th>
                            <th>Tse Belgesi</th>
                            <th>Tüp Yüzey Durumu</th>
                            <th>Sızdırmazlık Deneyi</th>
                            <th>Esneme Deneyi</th>
                            <th>Düşünceler</th>

                        </tr>
                        <th colspan="12">
                                <button type="button" class="btn btn-outline-primary" id="deleteAll">Tüm Satırları Sil</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php


                        $contquery = $ac->prepare("SELECT * FROM report_hst_content where report_id = ?");
                        $contquery->execute(array($id));
                        $satir = 0;
                        while ($content = $contquery->fetch(PDO::FETCH_ASSOC)) {
                            $tabindex =  $satir;
                            $kg = $content["kg"];
                            $testno = $content["testno"];

                            $cinsi = $content["cinsi"];
                            $imalatci_firma = $content["imalatci_firma"];
                            $imal_tarihi = $content["imal_tarihi"];
                            $serino = $content["serino"];
                            $tse_belgesi = $content["tse_belgesi"];
                            $yuzey_durumu = $content["yuzey_durumu"];
                            $sizdirmazlik_deneyi = $content["sizdirmazlik_deneyi"];
                            $esneme_deneyi = $content["esneme_deneyi"];
                            $things = $content["things"];

                            include "report-row-hst.php";
                            $satir += 1;

                        }
                        if ($satir < 1) {
                            include_once "report-row-hst.php";
                        }

                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="16" class="pt-3 pb-3 pl-2">
                                <button type="button" class="btn btn-sm btn-primary" id="addRow">Yeni Satır</button>
                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal"
                                    data-target="#exampleModalCenter" id="addMultiRow">Çoklu Satır Ekle</button>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-sm btn-secondary" data-toggle="modal"
                                    data-target="#uploadfromxlsModal">
                                    Cihazları Excelden Yükle
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <!-- TABLO -->


    </div>



</form>

<?php include_once "upload-from-xls-modal.php" ?>
<?php include_once "addMultipleRow-modal.php" ?>
<script src="include/js/hst.js"></script>