<?php
//Rapor Ekleme Yetkisi
//permcontrol("reportadd");
ini_set('display_errors', 'On');
//error_reporting(E_ALL);

$id = $_GET["id"];


if ($_POST) {

    if (isset($_POST["cinsi"])) {



        $report_number = $_POST["report_number"];
        $customer_id = $_POST["customer"];
        $control_date = $_POST["control_date"];
        $next_control_date = $_POST["next_control_date"];
        $controller_id = $_POST["controller"];
        $updater = sesset("id");
        $update_date = date("Y-m-d H:i:s");

        // DOLAP BİLGİLERİ
        $criteria                   = $_POST["criteria"];
        $cinsi                      = @$_POST["cinsi"];
        $bulundugu_kisim            = @$_POST["bulundugu_kisim"];
        $ozellikler                 = @$_POST["ozellikler"];
        $control_date_closet        = @$_POST["control_date_closet"];
        $next_control_date_closet   = @$_POST["next_control_date_closet"];
        $vana_durum                 = @$_POST["vana_durum"];
        $hortum_baglanti_durum      = @$_POST["hortum_baglanti_durum"];
        $levha_durum                = @$_POST["levha_durum"];
        $pas_durum                  = @$_POST["pas_durum"];
        $kilit_durum                = @$_POST["kilit_durum"];
        $hortum_durum               = @$_POST["hortum_durum"];
        $basinc_degeri              = @$_POST["basinc_degeri"];
        $nozul_durum                = @$_POST["nozul_durum"];
        $aciklama                   = @$_POST["aciklama"];

        $data = array();

        // 1'den 30'a kadar olan maddeleri diziye ekle
        for ($i = 1; $i <= 30; $i++) {
            $data["madde$i"] = $_POST["madde$i"]; // $_POST["madde1"], $_POST["madde2"], ... şeklinde sırayla input değerlerini alır
        }

        // JSON formatına dönüştür
        $jsonData = json_encode($data);

        //Giriş Sayfası Kayıt Bilgilleri
        try {
            $query = $ac->prepare("UPDATE reports SET report_number = ?, 
                                                        customer_id = ?, control_date = ?, 
                                                        next_control_date = ?, controller_id = ? , report_matters = ?,
                                                        updater = ?, update_time = ?,subNotes = ? WHERE id = ? ");
            $query->execute(
                array(
                    $report_number,
                    $customer_id,
                    $control_date,
                    $next_control_date,
                    $controller_id,
                    $jsonData,
                    $updater,
                    $update_date,$criteria,
                    $id
                )
            );

            //DOLAP SAYFASI KAYIT BİLGİLERİ 
            $dolapSayisi = count($cinsi);

            //RAPORA AİT İÇERİKLER SİLİNİR
            $delete_content = $ac->prepare("DELETE FROM report_met_content WHERE report_id = ?");
            $delete_content->execute(array($id));


            for ($i = 0; $i < $dolapSayisi; $i++) {
                $insq = $ac->prepare("INSERT INTO report_met_content SET report_id = ? , 
                                        cinsi = ? ,                    bulundugu_kisim = ? ,         ozellikler = ? , 
                                        control_date_closet = ? ,      next_control_date_closet = ? , vana_durum = ? , 
                                        hortum_baglanti_durum = ? ,    levha_durum = ? ,             pas_durum = ? , 
                                        kilit_durum = ? ,              hortum_durum = ? ,            basinc_degeri = ? , 
                                        nozul_durum = ? ,              aciklama = ?       ");
                $insq->execute(
                    array(
                        $id,
                        $cinsi[$i],
                        $bulundugu_kisim[$i],
                        $ozellikler[$i],
                        $control_date_closet[$i],
                        $next_control_date_closet[$i],
                        $vana_durum[$i],
                        $hortum_baglanti_durum[$i],
                        $levha_durum[$i],
                        $pas_durum[$i],
                        $kilit_durum[$i],
                        $hortum_durum[$i],
                        $basinc_degeri[$i],
                        $nozul_durum[$i],
                        $aciklama[$i]
                    )
                );
            }
            //DOLAP SAYFASI KAYIT BİLGİLERİ\\

            // EKLER SAYFASI KAYIT BİLGİLERİ
            if (isset($_FILES["report_attach"])) {
                $dosyaSayisi = count($_FILES["report_attach"]["name"]);
                $dosya_aciklama = $_POST["attach_description"];

                // RAPORA AİT DOSYALAR GETİRİLİR VE FILES KLASORUNDEN SİLİNİR
                $report_files = $ac->prepare("SELECT * FROM files WHERE report_id = ?");
                $report_files->execute(array($id));

                while ($files = $report_files->fetch(PDO::FETCH_ASSOC)) {
                    $file_path = "files/reports" . $files["filename"]; // Dosya yolunu oluştur
                    if (file_exists($file_path)) { // Dosya var mı diye kontrol et
                        unlink($file_path); // Dosyayı sil
                    }
                }
                //RAPORA AİT DOSYALAR VERİTABANINDAN SİLİNİR
                $delete_files = $ac->prepare("DELETE FROM files WHERE report_id = ?");
                $delete_files->execute(array($id));


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
                            $ins->execute(array($id, $dosya_aciklama[$i], $rast1 . "_" . basename($_FILES["report_attach"]["name"][$i]), $_FILES["report_attach"]["size"][$i], sesset("id")));


                        }
                    }
                }
            }
            // EKLER SAYFASI KAYIT BİLGİLERİ

            header("Location: index.php?p=reports/met/report-edit-met&st=newsuccess&id=$id");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        header("Location: index.php?p=reports/met/report-edit-met&st=empties&id=$id");
    }

}

                                    


$report_query = $ac->prepare("SELECT * FROM reports WHERE id = ?");
$report_query->execute(array($id));
$report = $report_query->fetch(PDO::FETCH_ASSOC);


// DOLAP BİLGİLERİ
$closet_query = $ac->prepare("SELECT * FROM report_met_content WHERE report_id = ?");
$closet_query->execute(array($id));

// dosya BİLGİLERİ
$file_query = $ac->prepare("SELECT * FROM files WHERE report_id = ?");
$file_query->execute(array($id));


// SEÇENEKLERİN DEĞERLERİ
$matters = json_decode($report["report_matters"]);

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

                <a href="index.php?p=reports/met/report-view-met&id=<?php echo $id; ?>" 
                    target="_blank" data-tooltip="Raporu Göster" data-tooltip-location="bottom"
                    class="btn btn-sm btn-danger text-white <?php echo $disabled ?>">
                    <i class="fa fa-file-pdf-o mr-1"></i></a> 
                
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
                <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-content"
                    type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Dolap Listesi</button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-tab" data-bs-toggle="pill" data-bs-target="#pills-attach"
                    type="button" role="tab" aria-controls="pills-disabled" aria-selected="false">Ekler</button>
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
                                    value="<?php echo $report["report_number"] ?>" class="form-control">
                            </div>
                        </div>
                        <!-- RAPOR NO -->

                        <!-- FİRMA ADI -->
                        <div class="form-group row">

                            <label for="customer" class="col-md-4"> Firma : </label>
                            <div class="col-md-8">
                                <?php customers("customer", $report["customer_id"]); ?>
                            </div>
                        </div>
                        <!-- FİRMA ADI -->


                    </div>
                    <!-- 1.KOLON  -->

                    <!-- 2.KOLON  -->
                    <div class="col-md-6 col-sm-12">

                        <div class="form-group row">
                            <label for="reportnumber" class="col-md-4"> Kontrolü Yapan Mühendis:</label>
                            <div class="col-md-8">
                                <?php userandjob("controller", $report["controller_id"]); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="control_date" class="col-md-4" for="">Kontrol Tarihi</label>
                            <div class="col-md-8">
                                <input required type="text" name="control_date" class="form-control date-picker"
                                    autocomplete="off" placeholder="Kontrol Tarihi"
                                    value="<?php echo $report["control_date"] ?> ">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="next_control_date" class="col-md-4" for="">Sonraki Kontrol Tarihi</label>
                            <div class="col-md-8">
                                <input required type="text" name="next_control_date" class="form-control date-picker"
                                    autocomplete="off" placeholder="Sonraki Kontrol Tarihi"
                                    value="<?php echo $report["next_control_date"] ?> ">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 2.KOLON  -->


            <!-- MADDELER -->
            <div class="content pd-20 bg-white border-radius-16 box-shadow mb-10">

                <div class="clearfix">
                    <div class="pull-left">
                        <h4 class="text-blue">
                            Maddeler
                        </h4>
                        <p class="mb-30 font-14">RAPORUN HAZIRLANMASINDA TEMEL DAYANAK:6331 SAYILI KANUNUN İŞ
                            EKİPMANLARININ
                            KULLANIMINDA SAĞLIK VE GÜVENLİK ŞARTLARI YÖNETMELİĞİNİN EK-III BAKIM, ONARIM VE
                            PERİYODİK
                            KONTROLLER İLE İLGİLİ HUSUSLAR(MADDE 1.7) <br></p>
                    </div>
                </div>

                <div class="row mt-20">
                    <div class="col-md-12">


                        <div class="form-group row">
                            <div class="col-md-3 col-sm-12 text-wrap">
                                1.7.2.
                                <p>
                                    İş Ekipmanlarına Ait Teknik Özellikler
                                </p>

                            </div>
                            <div class="col-md-9 col-sm-12">
                                Raporun bu bölümünde periyodik kontrole tabi tutulacak İG ekipmanlarının adı,
                                markası, modeli,
                                imal
                                yılı, ekipmanın seri numarası, konumu, kullanım amacı ile gerek görülen teknik
                                özellikler ve
                                diğer
                                bilgilere yer verilir.
                            </div>
                        </div>

                        <div class="form-group row ">
                            <div class="col-md-3 col-sm-12">
                                1.7.3.
                                <p>
                                    Periyodik Kontrol Metodu
                                </p>

                            </div>
                            <div class="col-md-9 col-sm-12 ">
                                İlgili standart numarası ve adı, periyodik kontrol esnasında kullanılan
                                ekipmanların özellikleri
                                ve
                                diğer bilgiler belirtilir.
                            </div>
                        </div>

                        <div class="form-group row ">
                            <div class="col-md-3 col-sm-12">
                                1.7.3. MADDE GEREĞİ:
                                <p>
                                    Kontrol Metodun İlgili Standart Numarası Ve Adı

                                </p>

                            </div>
                            <div class="col-md-9 col-sm-12">
                                TS 11368 YANGIN ÖNLEME-HORTUM DOLAPLARI
                                <p>
                                    TS 11926 YANGIN MUSLUKLARI TESİS VE KULLANIM KURALLARINA GÖRE YILLIK YAPILAN
                                    UYGUNLUK KONTROLÜ
                                </p>

                            </div>
                        </div>

                        <div class="form-group row ">
                            <div class="col-md-3 col-sm-12">
                                1.7.4.
                                <p>
                                    Tespit ve Değerlendirme
                                </p>

                            </div>
                            <div class="col-md-9 col-sm-12 ">
                                Raporun bu bölümünde EK-III madde 1.7.3' te belirlenen kurallar ve yapılan
                                periyodik kontrolden
                                elde edilen değerlerin , yine EK-III madde 1.7.2' de yer verilen iş ekipmanının
                                teknik
                                özelliklerini karşılayıp karşılamadığı husus ile ilgili standart ve teknik
                                litaretürde yer alan
                                sınır değerlere uygun olup olmadığı kıyaslanarak değerlendirilir.Periyodik
                                kontrolde uygulanan
                                test ve diğer işlemlere ilişkin bilgilere yer verilir.
                            </div>
                        </div>

                        <div class="form-group row ">
                            <div class="col-md-12 col-sm-12 text-blue">
                                1.7.4.
                                <p>
                                    Tespit ve Değerlendirme
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- MADDE KOLONLARI 1 -->

                    <?php
                    // BİRİNCİ KOLON SORULARI
                    $sql = $ac->prepare("SELECT met as soru FROM report_questions WHERE met != ''");
                    $sql->execute(array());
                    $question = $sql->fetchAll(PDO::FETCH_ASSOC);
                    $questions = $sql->rowCount();
                    $first_col = intVal($questions / 2);


                    ?>

                    <div class="col-md-12 col-sm-12 matters-container">
                        <div class="col-md-6 matters">
                            <?php
                            for ($i = 0; $i < 15; $i++) {
                                if ($i < $first_col) {
                                    $fv = 'madde' . ($i + 1);
                                    $first_val = $matters->$fv == "1" ? "UYGUN" : "UYGUN DEĞİL";


                                    $row_number = $i + 1;
                                    $select_name = "madde" . $row_number
                                        ?>
                                    <div class="form-group row d-flex">
                                        <label for="" class="col-md-1 text-warning">
                                            <?php echo $i + 1 ?>
                                        </label>
                                        <label for="" class="col-md-8">
                                            <?php echo $question[$i]["soru"]; ?>
                                        </label>
                                        <?php optionselect($select_name, $first_val, 'required', '', 'col-md-3') ?>
                                    </div>

                                <?php }
                            }
                            ?>
                        </div>
                        <div class="col-md-6 matters">
                            <?php
                            for ($i = 15; $i < count($question); $i++) {
                                if ($i >= $first_col) {

                                    $sv = 'madde' . ($i + 1);
                                    $second_val = $matters->$sv == "1" ? "UYGUN" : "UYGUN DEĞİL";

                                    $row_number = $i + 1;
                                    $select_name = "madde" . $row_number

                                        ?>

                                    <div class="form-group row d-flex">
                                        <label for="" class="col-md-1 text-warning">
                                            <?php echo $i + 1 ?>
                                        </label>
                                        <label for="" class="col-md-8">
                                            <?php echo $question[$i]["soru"]; ?>
                                        </label>
                                        <?php optionselect($select_name, $second_val, 'required', '', 'col-md-3') ?>
                                    </div>
                                <?php }
                            }
                            ?>
                        </div>
                    </div>

                    <!-- MADDE KOLONLARI 1 -->




                    <!-- MADDELER DEVAM EDİYOR -->
                    <div class="col-md-12 mt-20">

                        <div class="form-group row">
                            <div class="col-md-3 col-sm-12 text-wrap">
                                1.7.5.
                                <p>
                                    Test, Deney Ve Muayene:
                                </p>

                            </div>
                            <div class="col-md-9 col-sm-12">
                                İş ekipmanının periyodik kontrolü esnasında yapılan test deney ve muayene (hidrostatik
                                test, statik test,dinamik test, tahribatsız muayene yöntemleri ve benzeri) sonuçları
                                belirtilir.
                            </div>
                        </div>

                        <div class="form-group row ">
                            <div class="col-md-3 col-sm-12">
                                1.7.5. MADDE GEREĞİ
                                <p>
                                    İş Ekipmanın Periyodik Kontrolünde Yapılan Deney Ve Muayeneler

                                </p>

                            </div>
                            <div class="col-md-9 col-sm-12 ">
                                <textarea name="" class="form-control selectpicker" id="" style="height:60px"
                                    placeholder="">FONKSİYONEL TEST HİDROFOR HİDROSTATİK POMPA YAPILDI.</textarea>
                            </div>
                        </div>

                        <div class="form-group row ">
                            <div class="col-md-3 col-sm-12">
                                1.7.6.
                                <p>
                                    İkaz Ve Öneriler:

                                </p>

                            </div>
                            <div class="col-md-9 col-sm-12">
                                Yapılan periyodik kontrol sonucunda İG sağlığı ve güvenliği yönünden uygun bulunmayan
                                hususların belirlenmesi halinde, bunların nasıl uygun hale getirileceğine ilişkin
                                öneriler ile bu hususlar giderilmeden iş ekipmanlarının kullanımının güvenli olmayacağı
                                belirtilir.

                            </div>
                        </div>

                        <div class="form-group row ">
                            <div class="col-md-3 col-sm-12">
                                1.7.5. MADDE GEREĞİ
                                <p>
                                    İş Ekipmanın Periyodik Kontrolünde Yapılan Deney Ve Muayeneler

                                </p>

                            </div>
                            <div class="col-md-9 col-sm-12 ">
                                <textarea name="" class="form-control selectpicker" id=""
                                    style="height:120px">DOLAP  SİSTEMİNDE KRİTİK ALANDA YAPILAN BASINÇ TESTİNDE SORUN GÖZLENMEMİŞTİR. YANGIN DOLAPLARI HİDROFOR VE POMPALAR İNCELENMİŞTİR. FİRMADA BULUNAN .. M3 ELEKTRİKLİ.. BAR ANA YANGIN POMPASI,...  M3  DİZEL ... BAR YEDEK YANGIN POMPASI, ... M3 JOKEY POMPA,MEVCUT HALİ İLE UYGUNDUR. ... TONLUK SU TANKI UYGUNDUR,... ADET YANGIN DOLABI UYGUNDUR.</textarea>
                            </div>
                        </div>



                        <div class="form-group row ">
                            <div class="col-md-3 col-sm-12">
                                1.7.7.
                                <p>
                                    Sonuç Ve Kanaat
                                </p>

                            </div>
                            <div class="col-md-9 col-sm-12 ">
                                Raporun bu bölümünde periyodik kontrole tabi tutulan iş ekipmanının varsa tespit edilen
                                ve giderilen noksanlıklar açıklanarak, bir sonraki periyodik kontrole kadar geçecek süre
                                içerisinde görevini güvenli bir şekilde yapıp yapamayacağını açıkça belirtilir.
                            </div>
                        </div>


                        <div class="form-group row ">
                            <div class="col-md-3 col-sm-12">
                                1.7.7. MADDE GEREĞİ
                                <p>
                                    SONUÇ VE KANAAT

                                </p>

                            </div>
                            <div class="col-md-9 col-sm-12 ">
                                <textarea name="" class="form-control selectpicker"
                                    id="">6331 Sayılı Kanun gereği çıkartılan İş Ekipmanlarının Kullanımında Sağlık ve Güvenlik şartları Yönetmeliğine ve BİNALARDA YANGINDA KORUNMA YÖNETMELİĞİ Projede belirtilen kriterlere uygun olup olmadığının belirlenmesine yönelik olarak yapılan. Ayrıca TS 9811, TS EN 671-3, TS EN 12416-1 + A2, TS EN 12416-2 + A1,TS EN 12845 + A2 standartlarında belirtilen kritelere uygun olarak yapılan Periyodik Kontrole göre YANGIN SÖNDÜRME SİSTEMİ 1 YIL SÜREYLE KULLANIMA UYGUNDUR. Uygunluğunun devamlılığından işveren sorumludur.</textarea>
                            </div>
                        </div>


                        <div class="form-group row ">
                            <div class="col-md-3 col-sm-12">
                                1.7.8.
                                <p>
                                    ONAY
                                </p>

                            </div>
                            <div class="col-md-9 col-sm-12 ">
                                Bu bölümde periyodik kontrolleri yapmaya yetkili kişinin/kişilerin kimlik bilgileri,
                                mesleği, diploma tarihi ve numarasına ilişkin bilgiler, Bakanlık kayıt numarası ile
                                raporun kaç nüsha olarak düzenlendiğini belirterek, imza altına alınır.Yukarıdaki
                                bilgilerin veya yetkili kişinin imzasının bulunmadığı raporlar geçersizdir.
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
        <!-- HOME TAB   -->

        <!-- YANGIN DOLAP LİSTESİ -->
        <div class="tab-pane fade" id="pills-content" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
            <div class="content pd-20 bg-white border-radius-16 box-shadow mb-10">
                <div class="clearfix mb-20">
                    <div class="pull-left">
                        <h4 class="text-blue">
                            Yangın Dolap Listesi
                        </h4>
                    </div>
                </div>


                <!--  KONTROL KRİTERLERİ -->
                <div class="form-group row">
                    <label for="company" class="col-md-2">
                        Kontrol Kriterleri
                    </label>
                    <div class="col-md-10">
                        <div class="html-editor">
                            <textarea style="height:100px; resize:vertical" name="criteria"
                                class="textarea_editor form-control"
                                placeholder="İlgili Standartları yazınız"><?php echo $report["subNotes"]?></textarea><br>
                        </div>
                    </div>
                </div>
                <!--  KONTROL KRİTERLERİ -->

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

                    .table>thead {
                        background-color: rgba(0,0,0,0.03) !important;
                        color: inherit !important;
                    }

                    .table>thead th {
                        color: inherit !important;
                        border-bottom: 1px solid rgba(0,0,0,0.1);
                    }

                    .border-dashed {
                        border-style: dashed !important;
                        border-width: 2px !important;
                        border-color: #dee2e6 !important;
                        border-radius: 8px;
                        transition: all 0.3s ease;
                    }

                    .border-dashed:hover, .border-dashed.highlight {
                        border-color: #007bff !important;
                        background-color: #f8f9fa !important;
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
                        <table id="metTable" class="table">
                            <thead>
                                <tr class="text-center">

                                    <th>İşlem</th>
                                    <th>S.N</th>
                                    <th>Cihazın Cinsi
                                    <th>Bulunduğu Kısım
                                    <th>Özellikleri (Mt)
                                    <th>Kontrol Tarihi
                                    <th>Bir Sonraki Kontrol Tarihi
                                    <th>Vana Uygun Mu?
                                    <th>Hortum Bağlantıları Uygun Mu?
                                    <th>Levha Uygun Mu ?
                                    <th>Paslanma Var Mı ?
                                    <th>Kilit Uygun Mu?
                                    <th>Hortum Durumu Uygun Mu ?
                                    <th>Bulunduğu Hattaki Basınç Değeri Nedir?
                                    <th>Nozul Durumu Uygun Mu?
                                    <th>Açıklama
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sirano = 1;
                                while ($closet = $closet_query->fetch(PDO::FETCH_ASSOC)) {
                                    extract($closet);
                                    include "report-row-met.php";
                                }

                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                        <button type="button" class="btn btn-sm btn-primary" id="addRow">Yeni
                                            Satır</button>
                                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#excelModal" data-bs-toggle="modal" data-bs-target="#excelModal">
                                            <i class="fa fa-file-excel-o"></i> Excel'den Yükle
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- TABLO -->


            </div>

        </div>
        <!-- YANGIN DOLAP LİSTESİ -->

        <div class="tab-pane fade" id="pills-attach" role="tabpanel" aria-labelledby="pills-disabled-tab" tabindex="0">
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
                        background-color: rgba(0,0,0,0.03) !important;
                        color: inherit !important;
                    }

                    .rpr-date,
                    #cihazno,
                    .date-input {
                        min-width: 120px;
                    }

                    .region {
                        min-width: 150px;
                        left: 20;
                    }

                    .things {
                        min-width: 240px;
                    }
                </style>
                <div class="hack1">
                    <div class="hack2">
                        <table id="metTablefile" class="table">
                            <thead>
                                <tr class="text-center">

                                    <th class="app-item-action">İşlem</th>
                                    <th>Açıklama
                                    <th>Dosya
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                while ($file = $file_query->fetch(PDO::FETCH_ASSOC)) {
                                    $attach_description = $file["fileDescription"];
                                    $report_attach = $file["filename"];
                                    $type = "edit";


                                    include "report-row-met-attach.php";
                                }
                                ;
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

    </div>

</form>

<!-- Excel Upload Modal -->
<div class="modal fade" id="excelModal" tabindex="-1" aria-labelledby="excelModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="excelModalLabel">Excel'den Cihaz Yükle</h5>
        <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="drop-area" class="border-dashed border-2 p-5 text-center bg-light cursor-pointer">
            <i class="fa fa-cloud-upload fa-3x text-primary mb-3"></i>
            <h5>Dosyayı buraya sürükleyin veya tıklayın</h5>
            <p class="text-muted">Desteklenen formatlar: .xlsx, .xls</p>
            <input type="file" id="uploadExcel" accept=".xlsx, .xls" style="display:none;">
        </div>
        
        <div id="file-info" class="mt-3" style="display:none;">
            <div class="alert alert-info d-flex justify-content-between align-items-center mb-0">
                <span id="filename-display" class="text-truncate mr-2"></span>
                <i class="fa fa-check-circle text-success"></i>
            </div>
        </div>
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <div>
            <a href="templates/cihaz_kontrol_sablonu.xlsx" class="btn btn-outline-primary">
                <i class="fa fa-download"></i> Şablonu İndir
            </a>
        </div>
        <div>
            <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Kapat</button>
            <button type="button" id="processExcel" class="btn btn-success" disabled>Yükle</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="include/js/met.js"></script>
<?php
if (@$_GET["st"] == "empties") {
    showAlert("alert", "Dolap Bilgisi sayfasında en az bir adet dolap eklemeniz gerekmektedir!");
}
if (@$_GET["st"] == "newsuccess") {
    showAlert("success", "İşlem Başarı ile tamamlandı!");
}
?>