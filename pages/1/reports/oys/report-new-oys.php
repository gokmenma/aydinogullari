<?php

$getNumber = setNumber("oys");
$getNumber = sprintf("%04d", $getNumber);
$new_report_number = "OYS" . $getNumber;

$type = 5;


if ($_POST) {
    //eğer dedektor varsa kayıt yapar
    if ($_POST["cinsi"]) {

        $report_number = $_POST["report_number"];
        $isemrino = $_POST["isemrino"];
        $customer_id = $_POST["customer"];
        $control_date = $_POST["control_date"];
        $next_control_date = $_POST["next_control_date"];
        $controller_id = $_POST["controller"];
        $creator = sesset("id");
        $regDate = date("Y-m-d");


        $cinsi = @$_POST["cinsi"];
        $bulundugu_bolge = @$_POST["bulundugu_bolge"];
        $cevre_kontrolu = @$_POST["cevre_kontrolu"];
        $dis_muhafaza = @$_POST["dis_muhafaza"];
        $calisabilirlik_testi = @$_POST["calisabilirlik_testi"];
        $dedektor_sayisi = count($_POST["cinsi"]);


        $data = array(); //madde dizisi
        $data2 = array(); //dedektor dizisi

        $controller_peak_info = array(
            "name" => $_POST["controller_peak"],
            "title" => $_POST["controller_peak_title"],
            "diploma" => $_POST["controller_peak_diploma"],
            "emo" => $_POST["controller_peak_emo"],
            "ekipnet" => $_POST["controller_peak_ekipnet"]
        ); //

        $jsonDataControllerPeak = json_encode($controller_peak_info);

        // 1'den 12'ye kadar olan maddeleri diziye ekle
        for ($i = 1; $i <= 12; $i++) {
            $data["oysmadde$i"] = $_POST["oysmadde$i"]; // 
        }
        // JSON formatına dönüştür
        $jsonData = json_encode($data);



        $bakim = array(
            "bakim1" => $_POST["is_control"],
            "bakim2" => $_POST["is_report"],
            "bakim3" => $_POST["last_control_date"],
        );

        $jsonDataBakim = json_encode($bakim);

        try {
            $query = $ac->prepare("INSERT INTO reports SET report_number = ?, isemrino = ?,
                                                report_type = ?,        customer_id = ?,    control_date = ?, 
                                                next_control_date = ?,  controller_id = ? , oys_general_matters = ?,
                                                bakim_bilgileri = ? ,
                                                controller_peak_info = ?,
                                                creator = ?,        create_time = ?");
            $query->execute(
                array(
                    $report_number,
                    $isemrino,
                    $type,
                    $customer_id,
                    $control_date,
                    $next_control_date,
                    $controller_id,
                    $jsonData,
                    $jsonDataBakim,
                    $jsonDataControllerPeak,
                    $creator,
                    $regDate

                )
            );
            $lastid = $ac->lastInsertId();

            // DEDEKTOR BİLGİLERİ SAYFASI KAYIT 
            for ($i = 0; $i < $dedektor_sayisi; $i++) {

                $insq = $ac->prepare("INSERT INTO report_contents SET report_id = ? , 
                    algilama_cinsi = ? , 
                    bulundugu_bolge = ? , 
                    cevre_kontrolu = ? , 
                    dis_muhafaza = ? , 
                    calisabilirlik_testi = ? ");
                $insq->execute(array($lastid, $cinsi[$i], $bulundugu_bolge[$i], $cevre_kontrolu[$i], $dis_muhafaza[$i], $calisabilirlik_testi[$i]));
            }
            // DEDEKTOR BİLGİLERİ SAYFASI KAYIT BİTİŞ

            // EKLER SAYFASI KAYIT BİLGİLERİ
            if (isset($_FILES["report_attach"])) {
                $dosyaSayisi = count($_FILES["report_attach"]["name"]);
                $dosya_aciklama = $_POST["attach_description"];

                for ($i = 0; $i < $dosyaSayisi; $i++) {
                    if ($_FILES["report_attach"]["error"][$i] == UPLOAD_ERR_OK) {

                        $dizin = "files/reports/";
                        $kaynak = $_FILES["report_attach"]["tmp_name"][$i];
                        $rast1 = uniqid();
                        $hedef = $dizin . $rast1 . "_" . basename($_FILES["report_attach"]["name"][$i]);

                        $upx = move_uploaded_file($kaynak, $hedef);

                        if ($upx) {

                            $ins = $ac->prepare("INSERT INTO files SET
                            report_id = ?,
                            fileDescription = ? ,
                            filename = ?,
                            size = ?,
                            creativer = ?");
                            $ins->execute(array($lastid, $dosya_aciklama[$i], $rast1 . "_" . basename($_FILES["report_attach"]["name"][$i]), $_FILES["report_attach"]["size"][$i], sesset("id")));


                        } else {
                            echo "Dosya yüklenirken bir hata oluştu. <br>";
                        }
                    } else {
                        echo "Dosya yükleme hatası: " . $_FILES["report_attach"]["error"][$i] . "<br>";
                    }
                }
            }
            // EKLER SAYFASI KAYIT BİLGİLERİ

            $getNumber += 1;
            $upquery = $ac->prepare("UPDATE define_numbers SET yas = ?");
            $upquery->execute(array($getNumber));

            header("Location: index.php?p=reports/yas/report-new-yas&st=newsuccess");

        } catch (PDOException $e) {
            echo "Hata : " . $e->getMessage();
        }
    } else {
        header("Location: index.php?p=reports/yas/report-new-yas&st=empties");
    }
}

if (@$_GET["st"] == "empties") {
    showAlert("alert", "Dedektor listesi sayfasında en az bir adet cihaz eklemeniz gerekmektedir!");

}
if (@$_GET["st"] == "newsuccess") {
    showAlert("success", "İşlem Başarı ile tamamlandı!");

}
?>


<form enctype="multipart/form-data" id="myForm" method="POST">
    <div class="content pd-20 bg-white border-radius-16 box-shadow mb-10">
        <div class="clearfix">
            <div class="pull-left">
                <h4 class="text-blue">
                    <?php echo $pdat["p_title"]; ?>
                </h4>
                <p class="mb-30 font-14">Sayfadaki <font color="red">(*)</font> yıldız ile belirtilen alanları boş
                    bırakmayın..<br></p>
            </div>
            <div class="float-right mb-20">
                <button type="button" id="submitButton" onclick="validateForm()" data-tooltip="Kaydet"
                    data-placement="bottom" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Kaydet</button>

                <a href="index.php?p=reports/reports" data-tooltip="Listeye Dön" data-tooltip-location="bottom"
                    class="btn btn-sm btn-secondary text-white">
                    <i class="fa fa-list mr-1"></i>Listeye Dön</a>
            </div>
        </div>



        <ul class="nav nav-pills mb-30" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home"
                    type="button" role="tab" aria-controls="pills-home" aria-selected="true">GİRİŞ</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-content-tab" data-bs-toggle="pill" data-bs-target="#pills-content"
                    type="button" role="tab" aria-controls="pills-content" aria-selected="false">Liste</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-result-tab" data-bs-toggle="pill" data-bs-target="#pills-result"
                    type="button" role="tab" aria-controls="pills-result" aria-selected="false">Sonuç Sayfası</button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-tab" data-bs-toggle="pill" data-bs-target="#pills-attach"
                    type="button" role="tab" aria-controls="pills-attach" aria-selected="false">Ekler</button>
            </li>
        </ul>
    </div>
    <div class="tab-content" id="pills-tabContent">

        <!-- HOME TAB   -->
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab"
            tabindex="0">
            <div class="content pd-20 bg-white border-radius-16 box-shadow mb-10">
                <div class="clearfix mb-20">
                    <div class="pull-left">
                        <h4 class="text-blue">
                            Giriş Bilgileri
                        </h4>
                    </div>
                </div>
                <div class="row">

                    <!-- 1.KOLON  -->
                    <div class="col-md-6 col-sm-12">


                        <!-- RAPOR NO -->
                        <div class="form-group row">
                            <label for="reportnumber" class="col-md-4"> Rapor No:</label>
                            <div class="col-md-8">
                                <input required name="report_number" type="text"
                                    value="<?php echo $new_report_number ?>" class="form-control">
                            </div>
                        </div>
                        <!-- RAPOR NO -->

                        <!-- FİRMA ADI -->
                        <div class="form-group row">

                            <label for="customer" class="col-md-4"> Firma :</label>
                            <div class="col-md-8">
                                <?php customers("customer", ""); ?>
                            </div>
                        </div>
                        <!-- FİRMA ADI -->


                        <!-- İŞEMRİ NO -->
                        <div class="form-group row">
                            <label for="" class="col-md-4">iş Emri No:</label>
                            <div class="col-md-8">
                                <select name="isemrino" id="isemrino" data-size="10" class="form-control selectpicker"
                                    data-style="bg-white border">
                                    <option value="">İs Emri Seçiniz</option>
                                    <?php
                                    $servicequery = $ac->prepare("SELECT * FROM projects ");
                                    $servicequery->execute();

                                    while ($isemri = $servicequery->fetch(PDO::FETCH_ASSOC)) {
                                        ?>
                                        <option value=" <?php echo "SN" . $isemri["id"] ?>">
                                            <?php echo "SN" . $isemri["id"] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                        </div>

                    </div>
                    <!-- 1.KOLON  -->

                    <!-- 2.KOLON  -->
                    <div class="col-md-6 col-sm-12">

                        <div class="form-group row">
                            <label for="controller" class="col-md-4"> Kontrolü Yapan Mühendis:</label>
                            <div class="col-md-8">
                                <?php userandjob("controller", ""); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="control_date" class="col-md-4" for="">Kontrol Tarihi</label>
                            <div class="col-md-8">
                                <input required type="text" name="control_date" class="form-control date-picker"
                                    autocomplete="off" placeholder="Kontrol Tarihi">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="next_control_date" class="col-md-4" for="">Sonraki Kontrol Tarihi</label>
                            <div class="col-md-8">
                                <input required type="text" name="next_control_date" class="form-control date-picker"
                                    autocomplete="off" placeholder="Sonraki Kontrol Tarihi">
                            </div>
                        </div>



                    </div>
                </div>
            </div>
            <!-- 2.KOLON  -->



            <div class="content pd-20 bg-white border-radius-16 box-shadow mb-10">

                <div class="clearfix">
                    <div class="pull-left">
                        <h4 class="text-blue">
                            Yangın Söndürme Sistemi Sınıfı
                        </h4>
                    </div>
                </div>

                <div class="row mt-20">

                    <!--SONDÜRME SINIFI -->
                    <div class="col-md-12 col-sm-12 matters-container">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group row">
                                <label for="reportnumber" class="col-md-4">Otomatik Yangın Söndürme Sistemi
                                    Sınıfı</label>
                                <div class="col-md-8">
                                    <?php yangin_sondurme_sinifi("sondurme_sinifi", ""); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group row">
                                <label for="reportnumber" class="col-md-4">Otomatik Yangın Söndürme Sistemi
                                    Çeşidi</label>
                                <div class="col-md-8">
                                    <input type="text" name="sondurme_cesidi" class="form-control">
                                </div>
                            </div>
                        </div>

                    </div>
                    <!--SONDÜRME SINIFI -->
                </div>
            </div>

            <!-- MADDELER -->
            <div class="content pd-20 bg-white border-radius-16 box-shadow mb-10">

                <div class="clearfix">
                    <div class="pull-left">
                        <h4 class="text-blue">
                            Önceki Bakım Bilgileri
                        </h4>
                    </div>
                </div>

                <div class="row mt-20">

                    <!-- MADDE KOLONLARI 1 -->
                    <div class="col-md-12 col-sm-12 matters-container">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group row">
                                <label for="reportnumber" class="col-md-4">Daha önce kontrolü yapılmış mı?</label>
                                <div class="col-md-8">
                                    <?php optionselect("is_control", "", "", "", "3"); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group row">
                                <label for="reportnumber" class="col-md-4">Önceki Bakım Tutanakları Mevcut Mu?</label>
                                <div class="col-md-8">
                                    <?php optionselect("is_report", "", "", "", "", "2"); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group row">
                                <label for="reportnumber" class="col-md-4">En Son Yapılan Kontrol Tarihi?</label>
                                <div class="col-md-8">
                                    <input type="text" name="last_control_date" class="form-control date-picker"
                                        placeholder="Tarih seçiniz!">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- MADDE KOLONLARI 1 -->
                </div>
            </div>

            <!-- GENEL KONTROLLER -->
            <div class="content pd-20 bg-white border-radius-16 box-shadow mb-10">

                <div class="clearfix">
                    <div class="pull-left">
                        <h4 class="text-blue">
                            Genel Bilgiler
                        </h4>
                    </div>
                </div>

                <div class="row mt-20">
                    <div class="col-md-12 col-sm-12 matters-container">
                        <?php
                        $sql = $ac->prepare("SELECT oys_genel as soru FROM report_questions where oys_genel != ''");
                        $sql->execute();
                        $questions = $sql->fetchAll(PDO::FETCH_ASSOC);
                        $questions_count = $sql->rowCount();


                        for ($i = 0; $i < $questions_count; $i++) {
                            $row_number = $i + 1;
                            $select_name = "oysmadde" . $row_number
                                ?>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group row">
                                    <label for="reportnumber" class="col-md-4">
                                        <?php echo $questions[$i]["soru"]; ?>
                                    </label>
                                    <div class="col-md-8">
                                        <?php optionselect($select_name, ""); ?>
                                    </div>
                                </div>
                            </div>
                        <?php }
                        ?>
                    </div>
                    <!-- GENEL KONTROLLER -->

                </div>
            </div>
        </div>
        <!-- HOME TAB   -->


        <!-- İÇERİK SAYFASI  -->
        <div class="tab-pane fade show" id="pills-content" role="tabpanel" aria-labelledby="pills-content-tab">

            <div class="content pd-20 bg-white border-radius-16 box-shadow mb-10">
                <div class="clearfix mb-20">
                    <div class="pull-left">
                        <h4 class="text-blue">
                            Periyodik Kontrol Raporu
                        </h4>
                    </div>
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
                        margin-bottom: 100px;
                    }

                    .hack2 {
                        display: table-cell;
                        overflow-x: auto;
                        width: 100%;

                    }

                    .table>thead {
                        background-color: #111;
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
                </style>
                <div class="hack1">
                    <div class="hack2">
                        <table id="yasTable" class="table">
                            <thead>
                                <tr>

                                    <th>İşlem</th>
                                    <th>Sistem Ekipman Cinsi</th>
                                    <th>Bulunduğu Bölge</th>
                                    <th>Çevre Kontrolü</th>
                                    <th>Dış Muhafaza</th>
                                    <th>Çalışabilirlik Testi</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include_once "report-row-oys.php";

                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="16" class="pt-3 pb-3 pl-2">
                                        <button type="button" class="btn btn-sm btn-primary" id="addRow">Yeni
                                            Satır</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- TABLO -->
            </div>
        </div>
        <!-- İÇERİK SAYFASI  -->


        <div class="tab-pane fade show" id="pills-result" role="tabpanel" aria-labelledby="pills-result-tab">

            <!-- MADDELER -->
            <div class="content pd-20 bg-white border-radius-16 box-shadow mb-10">

                <div class="clearfix">
                    <div class="pull-left">
                        <h4 class="text-blue">
                            Rapor Bilgileri
                        </h4>

                    </div>
                </div>
                <div class="row mt-20">


                    <!--************* 1.KOLON *******************  -->
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group row">
                            <label for="reportnumber" class="col-md-4"> Muayeneyi Yapan Mühendis:</label>
                            <div class="col-md-8">
                                <input type="text" name="controller_peak" class="form-control"
                                    placeholder="Mühendis giriniz!">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="reportnumber" class="col-md-4"> Unvanı :</label>
                            <div class="col-md-8">
                                <input type="text" name="controller_peak_title" class="form-control">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="reportnumber" class="col-md-4"> Diploma No :</label>
                            <div class="col-md-8">
                                <input type="text" name="controller_peak_diploma" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="reportnumber" class="col-md-4"> Emo Sicil No :</label>
                            <div class="col-md-8">
                                <input type="text" name="controller_peak_emo" class="form-control">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="reportnumber" class="col-md-4"> Ekipnet No :</label>
                            <div class="col-md-8">
                                <input type="text" name="controller_peak_ekipnet" class="form-control">
                            </div>
                        </div>
                    </div>
                    <!--************* 1.KOLON *******************  -->



                    <!--************* 2.KOLON *******************  -->
                    <div class="col-md-6 col-sm-12">
                        <!--KONTROL TARİHİ -->
                        <div class="form-group row">
                            <label for="reportnumber" class="col-md-4">Kontrol Tarihi :</label>
                            <div class="col-md-8">
                                <input required name="control_date" autocomplete="off" type="text"
                                    class="form-control date-picker">
                            </div>
                        </div>
                        <!-- KONTROL TARİHİ -->

                        <!--SON KONTROL TARİHİ -->
                        <div class="form-group row">
                            <label for="next_control_date_result" class="col-md-4"> Son Kontrol Tarihi :</label>
                            <div class="col-md-8">
                                <input required name="next_control_date_result" aut type="text"
                                    class="form-control date-picker">
                            </div>
                        </div>
                        <!--SON KONTROL TARİHİ -->
                    </div>
                </div>
            </div>
            <!--************* 2.KOLON *******************  -->




            <div class="content pd-20 bg-white border-radius-16 box-shadow mb-10">
                <div class="clearfix">
                    <div class="pull-left">
                        <h4 class="text-blue">
                            Maddeler
                        </h4>

                    </div>
                </div>


                <div class="col-md-12">
                    <div class="form-group row p-3">

                        1.7.2. İş Ekipmanlarına Ait teknik Özellikler:Raporun bu bölümünde periyodik kontrole tabi
                        tutulacak İG ekipmanlarının adı, markası, modeli, imal yılı, ekipmanın seri numarası, konumu,
                        kullanım amacı ile gerek görülen teknik özellikler ve diğer bilgilere yer verilir.
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group row p-3">
                        RAPORUN HAZIRLANMASINDA TEMEL DAYANAK:6331 SAYILI KANUNUN İŞ EKİPMANLARININ KULLANIMINDA
                        SAĞLIK VE GÜVENLİK ŞARTLARI YÖNETMELİĞİNİN EK-III BAKIM, ONARIM VE PERİYODİK KONTROLLER İLE
                        İLGİLİ HUSUSLAR MADDE 1.7
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group row p-3">
                        1.7.3. Periyodik Kontrol Metodu:İlgili standart numarası ve adı, periyodik kontrol esnasında
                        kullanılan ekipmanların özellikleri ve diğer bilgiler belirtilir.
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group row p-3">
                        1.7.7. Sonuç Ve Kanaat: Raporun bu bölümünde periyodik kontrole tabi tutulan iş ekipmanının
                        varsa tespit edilen ve giderilen noksanlıklar açıklanarak, bir sonraki periyodik kontrole
                        kadar geçecek süre içerisinde görevini güvenli bir şekilde yapıp yapamayacağını açıkça
                        belirtilir.
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group row p-3">
                        <textarea name="" class="form-control" style="max-height:120px"
                            id="">SONUÇ VE KANAAT : 6331 Sayılı Kanun gereği çıkartılan İş Ekipmanlarının Kullanımında Sağlık ve Güvenlik şartları Yönetmeliğine ve BİNALARDA YANGINDA KORUNMA YÖNETMELİĞİ Projede belirtilen kriterlere uygun olup olmadığının belirlenmesine yönelik olarak yapılan. Ayrıca TS ISO 15004-1, TS EN 15004-2,TS EN 15004-3,TS EN 15004-4,TS EN 15004-5, TS EN 15004-6, TS EN 15004-7,TS EN 15004-8,TS EN 15004-9,TS EN 15004-10 standartlarında belirtilen kritelere göre uygun olup OTOMATİK YANGIN SÖNDÜRME SİSTEMİ 1 YIL SÜREYLE KULLANIMA UYGUNDUR. Uygunluğunun devamlılığından işveren sorumludur.</textarea>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group row p-3">
                        1.7.8. ONAY: Bu bölümde periyodik kontrolleri yapmaya yetkili kişinin/kişilerin kimlik
                        bilgileri, mesleği, diploma tarihi ve numarasına ilişkin bilgiler, Bakanlık kayıt numarası
                        ile raporun kaç nüsha olarak düzenlendiğini belirterek, imza altına alınır.Yukarıdaki
                        bilgilerin veya yetkili kişinin imzasının bulunmadığı raporlar geçersizdir.
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group row p-3">
                        6331 SAYILI KANUNUN "İŞ EKİPMANLARININ KULLANIMINDA SAĞLIK VE GÜVENLİK ŞARTLARI YÖNETMELİĞİ"
                        GEREĞİ HAZIRLANAN BU RAPOR İKİ NÜSHA VE İKİ SAYFA OLARAK TANZİM EDİLMİŞ VE AŞAĞIDAKİ SERİ
                        NUMARISI İLE KAYIT ALTINA ALINARAK ONAYLANMIŞTIR.UYGUNLUĞUNUN DEVAMLILIĞINDAN İŞ VEREN
                        SORUMLUDUR.
                    </div>
                </div>

            </div>

        </div>

        <!-- EK SAYFASI  -->
        <div class="tab-pane fade show" id="pills-attach" role="tabpanel" aria-labelledby="pills-attach-tab">

            <div class="content pd-20 bg-white border-radius-16 box-shadow mb-30">
                <div class="clearfix mb-30">
                    <div class="pull-left">
                        <h4 class="text-blue">
                            Rapor Ek Dosyaları
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

                    .table>thead {
                        background-color: #111;
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
                </style>
                <div class="hack1">
                    <div class="hack2">
                        <table id="yasTablefile" class="table">
                            <thead>
                                <tr class="text-center">

                                    <th>İşlem</th>
                                    <th>Açıklama
                                    <th>Dosya
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include_once "report-row-oys-attach.php"
                                    ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="16" class="pt-3 pb-3 pl-2">
                                        <button type="button" class="btn btn-sm btn-primary" id="addRowfile">Yeni
                                            Satır</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- TABLO -->
            </div>
        </div>
        <!-- EK SAYFASI  -->
    </div>
</form>
<script src="include/js/yas.js"></script>