<?php
//Rapor Ekleme Yetkisi
permcontrol("reportadd");

$getNumber = setNumber("ysc");
$new_report_number = "YSC000" . $getNumber;
$type = @$_GET["type"];
if ($_POST) {


    $cihazno = $_POST["cihazno"];
    $cihazSayisi = count($cihazno);

    if ($_POST["customer_id"] == null && $cihazSayisi < 1) {
        header("Location: index.php?p=reports/report-new-ysc&st=empties");
    }

    $report_type = $_POST["report_type"];
    $report_number = $_POST["reportnumber"];
    $isemrino = $_POST["isemrino"];
    $control_date = $_POST["control_date"];
    $control_period = $_POST["control_period"];
    $validity_date = $_POST["validity_date"];
    $controller_id = $_POST["controller_id"];
    $company_official = $_POST["company_official"];
    $customer_id = $_POST["customer_id"];
    $standarts = $_POST["standarts"];
    $warnings = $_POST["warnings"];
    $equipments = $_POST["equipment"];
    $notes = $_POST["notes"];
    $subnotes = $_POST["subNotes"];
    $creator = sesset("id");




    try {
        $insq = $ac->prepare("INSERT INTO reports SET report_type = ? ,
                                                       report_number = ? ,
                                                       isemrino = ? ,
                                                       control_date = ? ,
                                                       control_period = ? ,
                                                       validity_date = ? ,
                                                       customer_id= ? ,
                                                       controller_id = ? ,
                                                       company_official = ? ,
                                                       standarts = ? ,
                                                       warnings = ? , 
                                                       equipments = ? ,
                                                       notes = ? ,
                                                       subNotes = ? ,
                                                       creator = ?   ");


        $insq->execute(
            array(
                1,
                $report_number,
                $isemrino,
                $control_date,
                $control_period,
                $validity_date,
                $customer_id,
                $controller_id,
                $company_official,
                $standarts,
                $warnings,
                $equipments,
                $notes,
                $subnotes,
                $creator
            )
        );
        $lastid = $ac->lastInsertId();
        $bulundugu_bolge = @$_POST["cihazbolge"];
        $cinsi = @$_POST["cinsi"];
        $cihaz_dolum_tarihi = @$_POST["dolumtarihi"];
        $cihaz_sonkullanma_tarihi = @$_POST["sonkullanimtarihi"];
        $kontrol_tarihi_1 = @$_POST["kontoltarihi1"];
        $kontrol_tarihi_2 = @$_POST["kontoltarihi2"];
        $islem_kontrol_tarihi_1 = @$_POST["islemkontroltarihi1"];
        $islem_kontrol_tarihi_2 = @$_POST["islemkontroltarihi2"];
        $dis_muhafaza = @$_POST["dismuhafaza"];
        $cevre_kontrolu = @$_POST["cevrekontrolu"];
        $pim_kontrolu = @$_POST["pimkontrolu"];
        $manometre_kontrolu = @$_POST["manometrekontrolu"];
        $hortum_kontrolu = @$_POST["hortumkontrolu"];
        $talimat_kontrolu = @$_POST["talimatkontrolu"];
        $agirlik_kontrolu = @$_POST["agirlikkontrolu"];


        // if ($cihazSayisi > 0) {
        //     for ($i = 0; $i < count($cihazno); $i++) {
        //         $insq = $ac->prepare("INSERT INTO report_ysc_content SET report_id = ? ,  
        //                                                             cihaz_no = ? ,
        //                                                             bulundugu_bolge = ? , 
        //                                                             cinsi = ? ,
        //                                                             cihaz_dolum_tarihi = ? , 
        //                                                             cihaz_sonkullanma_tarihi = ? , 
        //                                                             kontrol_tarihi_1 = ? , 
        //                                                             kontrol_tarihi_2 = ? ,
        //                                                             islem_kontrol_tarihi_1 = ? , 
        //                                                             islem_kontrol_tarihi_2 = ? , 
        //                                                             dis_muhafaza = ? , 
        //                                                             cevre_kontrolu = ? , 
        //                                                             pim_kontrolu = ? , 
        //                                                             manometre_kontrolu = ? , 
        //                                                             hortum_kontrolu = ? , 
        //                                                             talimat_kontrolu = ? , 
        //                                                             agirlik_kontrolu = ?");

        //         $insq->execute(
        //             array(
        //                 $lastid,
        //                 $cihazno[$i],
        //                 $bulundugu_bolge[$i],
        //                 $cinsi[$i],
        //                 $cihaz_dolum_tarihi[$i],
        //                 $cihaz_sonkullanma_tarihi[$i],
        //                 $kontrol_tarihi_1[$i],
        //                 $kontrol_tarihi_2[$i],
        //                 $islem_kontrol_tarihi_1[$i],
        //                 $islem_kontrol_tarihi_2[$i],
        //                 $dis_muhafaza[$i],
        //                 $cevre_kontrolu[$i],
        //                 $pim_kontrolu[$i],
        //                 $manometre_kontrolu[$i],
        //                 $hortum_kontrolu[$i],
        //                 $talimat_kontrolu[$i],
        //                 $agirlik_kontrolu[$i]
        //             )
        //         );
        //     }
        // }
        if ($cihazSayisi > 0) {
            $placeholders = [];
            $data = [];
            for ($i = 0; $i < count($cihazno); $i++) {
                $placeholders[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                array_push($data, $lastid, $cihazno[$i], $bulundugu_bolge[$i], $cinsi[$i], $cihaz_dolum_tarihi[$i], $cihaz_sonkullanma_tarihi[$i], $kontrol_tarihi_1[$i], $kontrol_tarihi_2[$i], $islem_kontrol_tarihi_1[$i], $islem_kontrol_tarihi_2[$i], $dis_muhafaza[$i], $cevre_kontrolu[$i], $pim_kontrolu[$i], $manometre_kontrolu[$i], $hortum_kontrolu[$i], $talimat_kontrolu[$i], $agirlik_kontrolu[$i]);
            }

            $sql = "INSERT INTO report_ysc_content (report_id, cihaz_no, bulundugu_bolge, cinsi, cihaz_dolum_tarihi, cihaz_sonkullanma_tarihi, kontrol_tarihi_1, kontrol_tarihi_2, islem_kontrol_tarihi_1, islem_kontrol_tarihi_2, dis_muhafaza, cevre_kontrolu, pim_kontrolu, manometre_kontrolu, hortum_kontrolu, talimat_kontrolu, agirlik_kontrolu) VALUES " . implode(', ', $placeholders);
            $insq = $ac->prepare($sql);
            $insq->execute($data);
        }


        $getNumber += 1;
        $upquery = $ac->prepare("UPDATE define_numbers SET ysc = ?");
        $upquery->execute(array($getNumber));

        header("Location: index.php?p=reports/ysc/report-new-ysc&type=1&st=newsuccess");
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    };
}


if (@$_GET["st"] == "empties") {
    showAlert('alert', "(*) ile işaretli alanları boş bırakmadan tekrar deneyin.!");
}
if ($_GET["st"] == "newsuccess") {
    showAlert("success", "İşlem Başarı ile tamamlandı!", "type=1");
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
            <div class="float-right">
                <button id="submitButton" onclick="validateForm()" data-tooltip="Kaydet" data-placement="bottom" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Kaydet</button>

                <a href="index.php?p=reports/reports" data-tooltip="Listeye Dön" data-tooltip-location="bottom" class="btn btn-sm btn-secondary text-white <?php echo $disabled ?>">
                    <i class="fa fa-list mr-1"></i>Listeye Dön</a>
            </div>
        </div>

        <div class="row">
            <!--*************************** 1.inci KOLON***************************** -->
            <div class="col-md-6">

                <!-- RAPOR NO -->
                <div class="form-group row">
                    <label for="reportnumber" class="col-md-4"> Rapor No:</label>
                    <div class="col-md-8">
                        <input required name="reportnumber" type="text" value="<?php echo $new_report_number ?>" class="form-control">
                    </div>
                </div>
                <!-- RAPOR NO -->


                <!-- FİRMA ADI -->
                <div class="form-group row">
                    <label for="customer_id" class="col-md-4">Firma Adı :</label>
                    <div class="col-md-8">

                        <select required name="customer_id" id="customer_id" data-live-search="true" data-size="10" class="form-control selectpicker" data-style="bg-white border">
                            <option disabled selected value="">Firma Seçiniz</option>
                            <?php
                            $compquery = $ac->prepare("SELECT * FROM customers");
                            $compquery->execute();

                            while ($company = $compquery->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                                <option value="<?php echo $company["id"] ?>">
                                    <?php echo $company["company"] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <!-- FİRMA ADI -->

                <!-- KONTROL/GEÇERLİLİK TARİHİ -->
                <div class="form-group row">
                    <label for="control_date" class="col-md-4">Kontrol/Geçerlilik Tarihi</label>
                    <div class="col-md-4">
                        <input required type="text" name="control_date" autocomplete="off" class="form-control date-picker" placeholder="Tarih seçiniz">
                    </div>
                    <div class="col-md-4">
                        <input required type="text" name="validity_date" autocomplete="off" class="form-control date-picker" placeholder="Tarih seçiniz">
                    </div>
                </div>
                <!-- KONTROL/GEÇERLİLİK TARİHİ -->

                <!-- KONTROL PERİYODU -->
                <div class="form-group row">
                    <label for="control_period" class="col-md-4">Kontrol Periyodu</label>
                    <div class="col-md-8">
                        <input type="text" autocomplete="off" name="control_period" class="form-control">
                    </div>
                </div>
                <!-- KONTROL PERİYODU -->


            </div>
            <!--*************************** 1.inci KOLON***************************** -->


            <!--*************************** 2.inci KOLON***************************** -->

            <div class="col-md-6">
                <!-- İŞEMRİ NO -->
                <div class="form-group row">
                    <label for="" class="col-md-4">iş Emri No:</label>
                    <div class="col-md-8">
                        <select name="isemrino" id="isemrino" data-size="10" class="form-control selectpicker" data-style="bg-white border">
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
                <!-- KONTROL EDEN BİLGİLERİ -->


                <!-- KONTROL EDEN BİLGİLERİ -->
                <div class="form-group row">
                    <label for="controller_id" class="col-md-4">Kontrol Eden Mühendis</label>
                    <div class="col-md-8">
                        <select required name="controller_id" id="controller_id" class="form-control selectpicker" data-style="bg-white border">
                            <option value="">Mühendis Seçiniz</option>
                            <?php
                            $userquery = $ac->prepare("SELECT * FROM users WHERE id != 1 ");
                            $userquery->execute();

                            while ($user = $userquery->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                                <option value="<?php echo $user["id"] ?>">
                                    <?php echo $user["username"] . " - " . $user["meslek"] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                </div>
                <!-- KONTROL EDEN BİLGİLERİ -->

                <!-- ONAYLAYAN BİLGİLERİ -->
                <div class="form-group row">
                    <label for="company_official" class="col-md-4">Onaylayan Yetkili</label>
                    <div class="col-md-8">
                        <select required name="company_official" id="company_official" class="form-control selectpicker" data-style="bg-white border">
                            <option value="">Yetkili Seçiniz</option>
                            <?php
                            $userquery = $ac->prepare("SELECT * FROM users WHERE id != 1 ");
                            $userquery->execute();

                            while ($user = $userquery->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                                <option value="<?php echo $user["id"] ?>">
                                    <?php echo $user["username"] . " - " . $user["meslek"] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                </div>
                <!-- ONAYLAYAN BİLGİLERİ -->
            </div>

        </div>


        <!--*************************** 2.inci KOLON***************************** -->



        <!-- İLGİLİ STANDARTLAR -->
        <div class="form-group row">
            <label for="company" class="col-md-2">
                İlgili Standartlar
            </label>
            <div class="col-md-10">
                <div class="html-editor">
                    <textarea style="height:100px; resize:vertical" name="standarts" class="textarea_editor form-control" placeholder="İlgili Standartları yazınız"></textarea><br>
                </div>
            </div>
        </div>
        <!-- İLGİLİ STANDARTLAR -->

        <!-- İLGİLİ STANDARTLAR -->
        <div class="form-group row">
            <label for="company" class="col-md-2">
                Test Sırasında Kullanılan Ekipmanlar
            </label>
            <div class="col-md-10">

                <textarea style="height:100px; resize:vertical" name="equipment" class="form-control" placeholder="Ekipmanları yazınız"></textarea><br>

            </div>
        </div>
        <!-- İLGİLİ STANDARTLAR -->

        <!-- İKAZ VE UYARILAR -->
        <div class="form-group row">
            <label for="company" class="col-md-2">
                İkaz ve Uyarılar
            </label>
            <div class="col-md-10">
                <div class="html-editor">
                    <textarea style="height:100px; resize:vertical" name="warnings" class="textarea_editor form-control" placeholder="İkaz ve uyarıları yazınız"></textarea><br>
                </div>
            </div>
        </div>
        <!-- İKAZ VE UYARILAR -->

        <!-- SONUÇ VE KANAAT-->
        <div class="form-group row">
            <label for="company" class="col-md-2">
                Sonuç ve Kanaat
            </label>
            <div class="col-md-10">
                <textarea style="height:100px; resize:vertical" name="notes" class="form-control" placeholder="Rapor hakkında not yazınız"></textarea>

            </div>
        </div>
        <!-- SONUÇ VE KANAAT-->

        <!-- SONUÇ VE KANAAT-->
        <div class="form-group row">
            <label for="company" class="col-md-2">
                Alt Bilgi
            </label>
            <div class="col-md-10">
                <textarea style="height:100px; resize:vertical" name="subNotes" class="form-control" placeholder="Alt Bilgi">Yukarıda kontrol tarihinde teknik özellikleri belirtilen Yangın Söndürme Sistemi ; mevcut şartlar altında 25 Nisan 2013 tarih ve 28628 sayılı resmi gazetede yayınlanan "İş Ekipmanlarının Kullanımında Sağlık Ve Güvenlik Şartları Yönetmeliği" nde belirtilen kriterlere uygun olarak TS ISO 11602-2 standartı 4.2 maddesine göre Periyodik Kontrol'ü yapılmıştır. Bu kontrol raporu muayene tarihindeki durumunu yansıtır, uygunluğun devamlılığından işveren sorumludur.Yangın söndürme cihazlarının kullanılmasında teknik olarak sakınca yoktur
                </textarea><br>

            </div>
        </div>
        <!-- SONUÇ VE KANAAT-->
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
                min-width: 250px;
                width: 250px !important;
            }
        .w-auto{
            width: auto !important;
        }

        </style>
        <div class="hack1">
            <div class="hack2">
                <table id="yscTable" class="table">
                    <thead>
                        <tr class="text-center">
                            <th>İşlem</th>
                            <th>Cihaz No </th>
                            <th>Bulunduğu Bölge </th>
                            <th>Cihaz Cinsi </th>
                            <th colspan="2">Cihaz Kullanım Tarihleri </th>
                            <th colspan="2">Kontrol Tarihleri </th>
                            <th colspan="2">Kontrollerde Yapılan İşlemler</th>
                            <th>Dış Muhafaza/Renk Kontrolü</th>
                            <th>Çevre Kontrolü</th>
                            <th>Pim Mühür Kontrolü</th>
                            <th>Manometre Kontrolü</th>
                            <th>Hortum/Nozül Kontrolü</th>
                            <th>Talimat Kontrolü</th>
                            <th>Ağırlık Kontrolü</th>
                        </tr>
                        <tr class="text-center">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>Dolum Tarihi</th>
                            <th>Son Kullanma Tarihi</th>
                            <th>1.Kontrol Tarihi</th>
                            <th>2.Kontrol Tarihi</th>
                            <th>1.Kontrol</th>
                            <th>2.Kontrol</th>
                            <th colspan="8"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $tabindex = 0;
                        $dismuhafaza =        1;
                        $cevrekontrolu = 1;
                        $pimkontrolu =  1;
                        $manometrekontrolu = 1;
                        $hortumkontrolu = 1;
                        $talimatkontrolu = 1;
                        $agirlikkontrolu = 1;
                        include_once "report-row-ysc.php"
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="16" class="pt-3 pb-3 pl-2">
                                <button type="button" class="btn btn-primary" id="addRow">Yeni Satır</button>
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter" id="addMultiRow">Çoklu Satır Ekle</button>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#uploadfromxlsModal">
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


<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Çoklu Satır Ekleme</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-12">
                    <label for="eklenecek_satir_sayisi">Eklenecek Satır Sayısı</label>
                    <input type="text" class="form-control" id="eklenecek_satir_sayisi" placeholder="Satır sayısını yazınız.(En fazla 100)">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Vazgeç</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="addMultiRowModal">Ekle</button>
            </div>
        </div>
    </div>
</div>

<?php include_once "upload-from-xls-modal.php" ?>

<script src="include/js/ysc.js"></script>