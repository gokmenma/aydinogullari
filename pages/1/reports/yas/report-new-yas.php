<?php
$id = $_GET["id"] ?? 0;
$is_edit = $id > 0;

if ($is_edit) {
    $query = $ac->prepare("SELECT * FROM reports WHERE id = ?");
    $query->execute(array($id));
    $report = $query->fetch(PDO::FETCH_ASSOC);
    
    $matters = json_decode($report["report_matters"] ?? "[]", true);
    $bakim = json_decode($report["bakim_bilgileri"] ?? "[]", true);
    $dedectors = json_decode($report["dedektor_info"] ?? "[]", true);
    $controller_peak = json_decode($report["controller_peak_info"] ?? "[]", true);
    
    $report_number = $report["report_number"];
} else {
    $getNumber = setNumber("yas");
    $getNumber = sprintf("%04d", $getNumber);
    $report_number = "YAS" . $getNumber;
    $matters = []; $bakim = []; $dedectors = []; $controller_peak = []; $report = [];
}

$type = 4;

if ($_POST) {
    if (isset($_POST["report_number"])) {
        $report_number_post = $_POST["report_number"];
        $isemrino = $_POST["isemrino"];
        $customer_id = $_POST["customer"];
        $control_date = $_POST["control_date"];
        $next_control_date = $_POST["next_control_date"];
        $controller_id = $_POST["controller"];
        
        $report_matters_data = array(
            "header_extra" => array(
                "kontrol_adresi" => $_POST["kontrol_adresi"], "isg_katip_id" => $_POST["isg_katip_id"], "sgk_sicil" => $_POST["sgk_sicil"], 
                "metot_kapsam" => $_POST["metot_kapsam"], "test_degerleri" => $_POST["test_degerleri"] ?? "",
                "kusur_aciklamalari" => $_POST["kusur_aciklamalari"], "notlar" => $_POST["notlar"], "sonuc_kanaat" => $_POST["sonuc_kanaat"]
            ),
            "tesis_detay" => array(
                "algilama_tipi" => $_POST["algilama_tipi"], "uyari_sistemi" => $_POST["uyari_sistemi"], "calisma_tipi" => $_POST["calisma_tipi"],
                "proje_onay_kurum" => $_POST["proje_onay_kurum"], "kontrol_nedeni" => $_POST["kontrol_nedeni"], "proje_onay_tarih" => $_POST["proje_onay_tarih"],
                "panel_marka" => $_POST["panel_marka"], "ilk_kontrol_tarihi" => $_POST["ilk_kontrol_tarihi"], "last_control_date" => $_POST["last_control_date"],
                "panel_seri_no" => $_POST["panel_seri_no"], "panel_gerilim" => $_POST["panel_gerilim"], "panel_yeri" => $_POST["panel_yeri"],
                "algilama_ekipmanlari" => $_POST["algilama_ekipmanlari"] ?? [], "uyari_ekipmanlari" => $_POST["uyari_ekipmanlari"] ?? [], 
                "sondurme_ekipmanlari" => $_POST["sondurme_ekipmanlari"] ?? []
            ),
            "bina_tespitleri" => array(
                "tesisat_degisiklik" => $_POST["tesisat_degisiklik"], "etiket_varmi" => $_POST["etiket_varmi"], "bina_sinifi" => $_POST["bina_sinifi"] ?? [],
                "tehlike_sinifi" => $_POST["tehlike_sinifi"], "tehlike_kategorisi" => $_POST["tehlike_kategorisi"], "alan" => $_POST["bina_alan"],
                "kat" => $_POST["bina_kat"] ?? "", "yukseklik" => $_POST["bina_yukseklik"] ?? "", "izin_tarihi" => $_POST["bina_izin_tarihi"] ?? "",
                "bolum_sayisi" => $_POST["bina_bolum_sayisi"] ?? "", "diger" => $_POST["bina_diger"] ?? ""
            ),
            "olcum_cihazlari" => array(
                array("ad" => $_POST["cihaz1_ad"], "seri" => $_POST["cihaz1_seri"], "kal_no" => $_POST["cihaz1_kal_no"], "kal_tar" => $_POST["cihaz1_kal_tar"], "gec_tar" => $_POST["cihaz1_gec_tar"]),
                array("ad" => $_POST["cihaz2_ad"], "seri" => $_POST["cihaz2_seri"], "kal_no" => $_POST["cihaz2_kal_no"], "kal_tar" => $_POST["cihaz2_kal_tar"], "gec_tar" => $_POST["cihaz2_gec_tar"])
            ),
            "inspections" => array()
        );

        for ($i = 1; $i <= 50; $i++) { $report_matters_data["inspections"]["madde$i"] = isset($_POST["madde$i"]) ? "UYGUN" : "UYGUN DEĞİL"; }
        $jsonDataMatters = json_encode($report_matters_data);
        
        $jsonDataDedektor = (isset($_POST["equipment_data_json"]) && !empty($_POST["equipment_data_json"])) ? $_POST["equipment_data_json"] : "[]";
        $jsonDataControllerPeak = json_encode(array("name" => $_POST["controller_peak"] ?? "", "diploma" => $_POST["controller_peak_diploma"] ?? "", "emo" => $_POST["controller_peak_emo"] ?? "", "ekipnet" => $_POST["controller_peak_ekipnet"] ?? ""));
        $jsonDataBakim = json_encode(array("result_note" => $_POST["sonuc_kanaat"] ?? ""));

        try {
            if ($is_edit) {
                $query = $ac->prepare("UPDATE reports SET report_number = ?, isemrino = ?, customer_id = ?, control_date = ?, next_control_date = ?, controller_id = ?, report_matters = ?, bakim_bilgileri = ?, dedektor_info = ?, controller_peak_info = ? WHERE id = ?");
                $query->execute(array($report_number_post, $isemrino, $customer_id, $control_date, $next_control_date, $controller_id, $jsonDataMatters, $jsonDataBakim, $jsonDataDedektor, $jsonDataControllerPeak, $id));
                $status_message = '<div class="alert alert-success shadow-sm" style="border-radius:10px;"><b>Başarılı!</b> Rapor güncellendi. <a href="index.php?p=reports/reports" class="alert-link">Listeye dön</a></div>';
            } else {
                $query = $ac->prepare("INSERT INTO reports SET report_number = ?, isemrino = ?, report_type = ?, customer_id = ?, control_date = ?, next_control_date = ?, controller_id = ?, report_matters = ?, bakim_bilgileri = ?, dedektor_info = ?, controller_peak_info = ?, creator = ?, create_time = ?");
                $query->execute(array($report_number_post, $isemrino, $type, $customer_id, $control_date, $next_control_date, $controller_id, $jsonDataMatters, $jsonDataBakim, $jsonDataDedektor, $jsonDataControllerPeak, sesset("id"), date("Y-m-d")));
                $status_message = '<div class="alert alert-success shadow-sm" style="border-radius:10px;"><b>Başarılı!</b> Yeni rapor oluşturuldu. <a href="index.php?p=reports/reports" class="alert-link">Listeye dön</a></div>';
            }
        } catch (PDOException $e) { 
            $status_message = '<div class="alert alert-danger shadow-sm" style="border-radius:10px;"><b>Kayıt Hatası!</b> Bir sorun oluştu:<br><code class="text-white">'.$e->getMessage().'</code></div>'; 
        }
    }
}

$header_extra = $matters["header_extra"] ?? [];
$tesis_detay = $matters["tesis_detay"] ?? [];
$bina_tespitleri = $matters["bina_tespitleri"] ?? [];
$olcum_cihazlari = $matters["olcum_cihazlari"] ?? [];
$inspections = $matters["inspections"] ?? [];

function getCheck($val, $arr) { return in_array($val, (array)$arr) ? "checked" : ""; }
function options($arr, $selected) { $o = ""; foreach($arr as $v) { $s = $v == $selected ? "selected" : ""; $o .= "<option value='$v' $s>$v</option>"; } return $o; }
?>

<style>
    #myForm.report-form-new { background: #f8fafc !important; }
    #myForm .card-soft { background: #fff !important; border: 1px solid #e2e8f0 !important; border-radius: 10px !important; padding: 20px !important; margin-bottom: 20px !important; }
    .minimal-tab-list { display: flex !important; list-style: none !important; padding: 0 !important; border-bottom: 2px solid #f1f5f9 !important; gap: 5px !important; overflow-x: auto; white-space: nowrap; }
    .minimal-tab-list .nav-link { padding: 12px 18px !important; color: #64748b !important; font-weight: 700 !important; font-size: 11px !important; border-bottom: 2px solid transparent !important; background: transparent !important; text-transform: uppercase !important; }
    .minimal-tab-list .nav-link.active { color: #2563eb !important; border-bottom: 2px solid #2563eb !important; }
    .ins-row { display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border: 1px solid #f1f5f9; border-radius: 6px; background: #fff; margin-bottom: 8px; }
    .ins-label { font-size: 11px; color: #334155; font-weight: 600; line-height: 1.4; }
    .switch { position: relative; display: inline-block; width: 34px; height: 18px; flex-shrink: 0; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .4s; border-radius: 20px; }
    .slider:before { position: absolute; content: ""; height: 12px; width: 12px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: #2563eb; }
    input:checked + .slider:before { transform: translateX(16px); }
    .group-title { font-size: 10px; font-weight: 800; color: #64748b; background: #f1f5f9; padding: 8px 15px; border-radius: 6px; margin: 20px 0 10px 0; text-align: center; text-transform: uppercase; border: 1px solid #e2e8f0; }
    .section-title { font-size: 14px; font-weight: 800; color: #0f172a; border-left: 4px solid #2563eb; padding-left: 12px; margin-bottom: 15px; text-transform: uppercase; }
    .cb-group { background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0; }
    .cb-item { font-size: 11px; font-weight: 600; color: #475569; display: flex; align-items: center; margin-bottom: 4px; }
    .cb-item input { margin-right: 8px; }
    .photo-preview { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; }
    .photo-item { position: relative; width: 128px; height: 128px; }
    .photo-item img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; border: 2px solid #e2e8f0; }
    .photo-remove { position: absolute; top: -5px; right: -5px; background: #ef4444; color: #fff; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; cursor: pointer; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    .btn-template { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-size: 11px; font-weight: 700; border-radius: 6px; padding: 5px 12px; transition: 0.2s; }
    .btn-template:hover { background: #e2e8f0; color: #1e293b; }
</style>

<form enctype="multipart/form-data" id="myForm" method="POST" class="report-form-new p-2">
    <?php if(isset($status_message)) echo $status_message; ?>
    <input type="hidden" name="equipment_data_json" id="equipment_data_json">
    <div class="card-soft">
        <div class="d-flex justify-content-between align-items-center">
            <div><h4 class="font-weight-bold text-dark mb-0"><?php echo $is_edit ? "Raporu Düzenle" : "Yeni Yangın Algılama Raporu"; ?></h4><p class="text-muted small mb-0">Eksiksiz ve Excel ile %100 Uyumlu Raporlama</p></div>
            <div class="d-flex" style="gap: 10px;">
                <button type="button" onclick="previewReport()" class="btn btn-warning btn-sm px-3 shadow-sm" style="border-radius: 6px; font-weight: 600;"><i class="fa fa-eye mr-1"></i> Raporu Önizle</button>
                <button type="submit" id="submitButton" onclick="return validateForm('submitButton')" class="btn btn-primary btn-sm px-4 shadow-sm" style="border-radius: 6px; font-weight: 600;"><i class="fa fa-save mr-1"></i> <?php echo $is_edit ? "Değişiklikleri Kaydet" : "Raporu Kaydet"; ?></button>
            </div>
        </div>
        <ul class="nav minimal-tab-list mt-3" role="tablist">
            <li><a class="nav-link active" data-toggle="pill" href="#p1">1. FİRMA BİLGİLERİ</a></li>
            <li><a class="nav-link" data-toggle="pill" href="#p2">2. TESİS/BİNA BİLGİLERİ</a></li>
            <li><a class="nav-link" data-toggle="pill" href="#p3">3. TEST DEĞERLERİ</a></li>
            <li><a class="nav-link" data-toggle="pill" href="#p4">4. ÖLÇÜM ALETLERİ</a></li>
            <li><a class="nav-link" data-toggle="pill" href="#p5">5. MUAYENE & ÜRÜNLER</a></li>
            <li><a class="nav-link" data-toggle="pill" href="#p69">6-9. SONUÇ VE ONAY</a></li>
        </ul>
    </div>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="p1">
            <div class="card-soft">
                <div class="section-title">1. FİRMA BİLGİLERİ</div>
                <div class="row">
                    <div class="col-md-7 mb-3"><label>Firma Adı</label><?php customers("customer", $report["customer_id"] ?? ""); ?></div>
                    <div class="col-md-5 mb-3"><label>Rapor Numarası</label><input required name="report_number" type="text" value="<?php echo $report_number ?>" class="form-control"></div>
                    <div class="col-md-7 mb-3"><label>Periyodik Kontrol Adresi</label><input name="kontrol_adresi" value="<?php echo $header_extra["kontrol_adresi"] ?? ""; ?>" class="form-control"></div>
                    <div class="col-md-5 mb-3"><div class="row"><div class="col-md-6"><label>Rapor Tarihi</label><input required name="control_date" value="<?php echo $report["control_date"] ?? ""; ?>" class="form-control date-picker"></div><div class="col-md-6"><label>İSG-KATİP Sözleşme ID</label><input name="isg_katip_id" value="<?php echo $header_extra["isg_katip_id"] ?? ""; ?>" class="form-control"></div></div></div>
                    <div class="col-md-7 mb-3"><label>SGK Sicil Numarası</label><input name="sgk_sicil" value="<?php echo $header_extra["sgk_sicil"] ?? ""; ?>" class="form-control"></div>
                    <div class="col-md-5 mb-3"><div class="row"><div class="col-md-6"><label>İş Emri No</label><input required name="isemrino" value="<?php echo $report["isemrino"] ?? ""; ?>" class="form-control"></div><div class="col-md-6"><label>Kontrol Personeli</label><?php users("controller", $report["controller_id"] ?? ""); ?></div></div></div>
                    <div class="col-md-5 mb-3"><label>Bir Sonraki Periyodik Kontrol Tarihi</label><input required name="next_control_date" value="<?php echo $report["next_control_date"] ?? ""; ?>" class="form-control date-picker"></div>
                    <div class="col-md-12 mb-3">
                        <label>Periyodik Kontrol Metodu ve Kapsamı</label>
                        <textarea name="metot_kapsam" class="form-control editor-wysi" style="height: 120px;"><?php echo $header_extra["metot_kapsam"] ?? "<ul><li>TSE CEN/TS 54-14: Yangın Algılama ve Yangın Alarm Sistemleri...</li></ul>"; ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="p2">
            <div class="card-soft">
                <div class="section-title">2.1. SİSTEM DETAY BİLGİLERİ</div>
                <div class="row">
                    <div class="col-md-3 mb-3"><label>Yangın algılama</label><select name="algilama_tipi" class="form-control"><?php echo options(["Otomatik","Manuel"], $tesis_detay["algilama_tipi"] ?? ""); ?></select></div>
                    <div class="col-md-3 mb-3"><label>Yangın uyarı sistemi</label><select name="uyari_sistemi" class="form-control"><?php echo options(["Işıklı+Sesli","Sesli","Işıklı","Anons"], $tesis_detay["uyari_sistemi"] ?? ""); ?></select></div>
                    <div class="col-md-3 mb-3"><label>Sistem çalışma tipi</label><select name="calisma_tipi" class="form-control"><?php echo options(["Adresli","Konvansiyonel"], $tesis_detay["calisma_tipi"] ?? ""); ?></select></div>
                    <div class="col-md-3 mb-3"><label>Proje onay kurumu</label><input name="proje_onay_kurum" value="<?php echo $tesis_detay["proje_onay_kurum"] ?? ""; ?>" class="form-control"></div>
                    <div class="col-md-3 mb-3"><label>Kontrol nedeni</label><select name="kontrol_nedeni" class="form-control"><?php echo options(["Periyodik Kontrol","İlk Kontrol"], $tesis_detay["kontrol_nedeni"] ?? ""); ?></select></div>
                    <div class="col-md-3 mb-3"><label>Proje onay tarih ve sayısı</label><input name="proje_onay_tarih" value="<?php echo $tesis_detay["proje_onay_tarih"] ?? ""; ?>" class="form-control"></div>
                    <div class="col-md-3 mb-3"><label>Kontrol paneli marka/model</label><input name="panel_marka" value="<?php echo $tesis_detay["panel_marka"] ?? ""; ?>" class="form-control"></div>
                    <div class="col-md-3 mb-3"><label>İlk kontrol tarihi</label><input name="ilk_kontrol_tarihi" value="<?php echo $tesis_detay["ilk_kontrol_tarihi"] ?? ""; ?>" class="form-control date-picker"></div>
                    <div class="col-md-3 mb-3"><label>Son kontrol tarihi</label><input name="last_control_date" value="<?php echo $tesis_detay["last_control_date"] ?? ""; ?>" class="form-control date-picker"></div>
                    <div class="col-md-3 mb-3"><label>Panel seri no / imal yılı</label><input name="panel_seri_no" value="<?php echo $tesis_detay["panel_seri_no"] ?? ""; ?>" class="form-control"></div>
                    <div class="col-md-3 mb-3"><label>Panel çalışma gerilimi</label><input name="panel_gerilim" value="<?php echo $tesis_detay["panel_gerilim"] ?? ""; ?>" class="form-control"></div>
                    <div class="col-md-3 mb-3"><label>Panel yeri</label><input name="panel_yeri" value="<?php echo $tesis_detay["panel_yeri"] ?? ""; ?>" class="form-control"></div>
                    <div class="col-md-4 mb-3"><label>Algılama ekipmanları</label><div class="cb-group"><div class="cb-item"><input type="checkbox" name="algilama_ekipmanlari[]" value="Duman" <?php echo getCheck("Duman", $tesis_detay["algilama_ekipmanlari"] ?? []); ?>> Duman (optik) dedektörü</div><div class="cb-item"><input type="checkbox" name="algilama_ekipmanlari[]" value="Isı" <?php echo getCheck("Isı", $tesis_detay["algilama_ekipmanlari"] ?? []); ?>> Isı dedektörü</div><div class="cb-item"><input type="checkbox" name="algilama_ekipmanlari[]" value="Buton" <?php echo getCheck("Buton", $tesis_detay["algilama_ekipmanlari"] ?? []); ?>> İhbar butonu</div></div></div>
                    <div class="col-md-3 mb-3"><label>Uyarı ekipmanları</label><div class="cb-group"><div class="cb-item"><input type="checkbox" name="uyari_ekipmanlari[]" value="Siren" <?php echo getCheck("Siren", $tesis_detay["uyari_ekipmanlari"] ?? []); ?>> Siren</div><div class="cb-item"><input type="checkbox" name="uyari_ekipmanlari[]" value="Flaşör" <?php echo getCheck("Flaşör", $tesis_detay["uyari_ekipmanlari"] ?? []); ?>> Flaşör</div></div></div>
                    <div class="col-md-5 mb-3"><label>Söndürme ekipmanları</label><div class="cb-group"><div class="cb-item"><input type="checkbox" name="sondurme_ekipmanlari[]" value="Otomatik" <?php echo getCheck("Otomatik", $tesis_detay["sondurme_ekipmanlari"] ?? []); ?>> Otomatik söndürme</div><div class="cb-item"><input type="checkbox" name="sondurme_ekipmanlari[]" value="KKT" <?php echo getCheck("KKT", $tesis_detay["sondurme_ekipmanlari"] ?? []); ?>> KKT Özellikli tüp</div><div class="cb-item"><input type="checkbox" name="sondurme_ekipmanlari[]" value="CO2" <?php echo getCheck("CO2", $tesis_detay["sondurme_ekipmanlari"] ?? []); ?>> CO2 Özellikli tüp</div><div class="cb-item"><input type="checkbox" name="sondurme_ekipmanlari[]" value="Hidrant" <?php echo getCheck("Hidrant", $tesis_detay["sondurme_ekipmanlari"] ?? []); ?>> Hidrantlar</div></div></div>
                </div>
            </div>
            <div class="card-soft">
                <div class="section-title">2.2. BİNA İLE İLGİLİ TESPİT EDİLEN BİLGİLER</div>
                <div class="row">
                    <div class="col-md-4 mb-3"><label>Tesisatta kapsamlı değişiklik</label><select name="tesisat_degisiklik" class="form-control"><?php echo options(["Belirlenemedi","Var","Yok"], $bina_tespitleri["tesisat_degisiklik"] ?? ""); ?></select></div>
                    <div class="col-md-4 mb-3"><label>Bir önceki periyodik kontrol etiketi var mı?</label><select name="etiket_varmi" class="form-control"><?php echo options(["Var","Yok"], $bina_tespitleri["etiket_varmi"] ?? ""); ?></select></div>
                    <div class="col-md-4 mb-3"><label>Bina tehlike sınıfı</label><select name="tehlike_sinifi" class="form-control"><?php echo options(["Düşük Tehlike","Orta Tehlike","Yüksek Tehlike"], $bina_tespitleri["tehlike_sinifi"] ?? ""); ?></select></div>
                    
                    <div class="col-md-12 mb-3">
                        <label>Bina kullanma sınıfı</label>
                        <div class="cb-group" style="display:grid; grid-template-columns: repeat(4, 1fr); gap: 5px;">
                            <?php 
                            $bina_listesi = ["Konut","Toplanma amaçlı bina","Depolama amaçlı tesis","Yüksek tehlikeli bina","Karışık kullanım amaçlı bina","Endüstriyel yapı","Konaklama amaçlı bina","Kurumsal bina","Büro binası","Ticari"];
                            foreach($bina_listesi as $bk){ 
                                echo '<div class="cb-item" style="font-size:11px;"><input type="checkbox" name="bina_sinifi[]" value="'.$bk.'" '.getCheck($bk, $bina_tespitleri["bina_sinifi"] ?? []).'> '.$bk.'</div>'; 
                            } 
                            ?>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3"><label>Tehlike kategorisi</label><select name="tehlike_kategorisi" class="form-control"><?php echo options(["1","2","3","4"], $bina_tespitleri["tehlike_kategorisi"] ?? ""); ?></select></div>
                    <div class="col-md-3 mb-3"><label>Bina toplam kullanım alanı (m²)</label><input name="bina_alan" value="<?php echo $bina_tespitleri["alan"] ?? ""; ?>" class="form-control"></div>
                    <div class="col-md-3 mb-3"><label>Kat sayısı</label><input name="bina_kat" value="<?php echo $bina_tespitleri["kat"] ?? ""; ?>" class="form-control"></div>
                    <div class="col-md-3 mb-3"><label>Bina yüksekliği / Yapı yüksekliği (m)</label><input name="bina_yukseklik" value="<?php echo $bina_tespitleri["yukseklik"] ?? ""; ?>" class="form-control"></div>
                    
                    <div class="col-md-3 mb-3"><label>Yapı kullanım izin tarihi</label><input name="bina_izin_tarihi" value="<?php echo $bina_tespitleri["izin_tarihi"] ?? ""; ?>" class="form-control date-picker"></div>
                    <div class="col-md-3 mb-3"><label>Bölüm sayısı</label><input name="bina_bolum_sayisi" value="<?php echo $bina_tespitleri["bolum_sayisi"] ?? ""; ?>" class="form-control"></div>
                    <div class="col-md-6 mb-3"><label>Varsa diğer tespitler</label><input name="bina_diger" value="<?php echo $bina_tespitleri["diger"] ?? ""; ?>" class="form-control"></div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="p3"><div class="card-soft"><div class="section-title">3. TEST DEĞERLERİ</div><textarea name="test_degerleri" class="form-control editor-wysi" style="height: 300px;"><?php echo $header_extra["test_degerleri"] ?? ""; ?></textarea></div></div>

        <div class="tab-pane fade" id="p4">
            <div class="card-soft">
                <div class="section-title">4. ÖLÇÜM ALETLERİ BİLGİLERİ</div>
                <div class="row">
                    <?php for($i=1;$i<=2;$i++){ $c = $olcum_cihazlari[$i-1] ?? []; ?>
                    <div class="col-md-6 mb-3"><div class="p-3 bg-light border"><label>Cihaz <?php echo $i; ?> Adı</label><input name="cihaz<?php echo $i; ?>_ad" value="<?php echo $c["ad"] ?? ""; ?>" class="form-control mb-2"><label>Seri / Kalibrasyon No</label><div class="d-flex mb-2" style="gap:5px;"><input name="cihaz<?php echo $i; ?>_seri" placeholder="Seri" value="<?php echo $c["seri"] ?? ""; ?>" class="form-control"><input name="cihaz<?php echo $i; ?>_kal_no" placeholder="Kal No" value="<?php echo $c["kal_no"] ?? ""; ?>" class="form-control"></div><label>Kalibrasyon / Geçerlilik Tarihi</label><div class="d-flex" style="gap:5px;"><input name="cihaz<?php echo $i; ?>_kal_tar" value="<?php echo $c["kal_tar"] ?? ""; ?>" class="form-control date-picker"><input name="cihaz<?php echo $i; ?>_gec_tar" value="<?php echo $c["gec_tar"] ?? ""; ?>" class="form-control date-picker"></div></div></div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="p5">
            <div class="card-soft">
                <div class="section-title">5.1. GÖZLE MUAYENELER</div>
                <?php
                $full_ins = [
                    "ÖN KONTROLLER" => ["Yetkili ve eğitimli personel var mı?","Acil durum anons sistemi mevcudiyeti","Yangın güvenliği sorumluları belirlenmiş mi?","Bakım/servis kayıtları tutuluyor mu?","Yangın alarm panelinin durumu","Sistem kütüğü belgesi var mı?"],
                    "YANGIN ALGILAMA VE TESİSAT" => ["Kontrol paneli ve tekrarlayıcı paneller","Kullanma talimatı var mı?","Kontrol paneli izlenebilirliği","Akü durumu","Adresleme/Harita var mı?","Dedektör uygunluğu","Paralel ihbar lambaları","Uyarı cihazları yeterliliği","Kısa/Açık devre koruması","Kablo uygunluğu","Güvenlik devre ayrılması"],
                    "ACİL AYDINLATMA VE YÖNLENDİRME" => ["Kaçış yolu armatürleri","Panel önü aydınlatma","Riskli alan aydınlatma","Çıkış yönlendirme","Kaçış yolu yönlendirme","Aydınlatma süreleri","Aydınlatma seviyeleri","Şebeke kesilme testi"],
                    "ENTEGRASYON VE DİĞER" => ["Duman damperleri entegrasyonu","İklimlendirme entegrasyonu","Asansör entegrasyonu","Yangın kapıları entegrasyonu","Gaz kesme valfleri entegrasyonu","Yangın butonları yerleşimi","Kablo tavaları yalıtımı","Sistem test edilmesi (Sprey)","Arıza geçmişi kontrolü","Sıçrama riski","Genel temizlik ve bakım"]
                ];
                $m_idx = 1;
                foreach($full_ins as $title => $items){
                    echo '<div class="group-title">'.$title.'</div><div class="row">';
                    foreach($items as $label){
                        $checked = ($inspections["madde$m_idx"] ?? "UYGUN") == "UYGUN" ? "checked" : "";
                        echo '<div class="col-md-6"><div class="ins-row"><span class="ins-label">'.$m_idx.'. '.$label.'</span><label class="switch"><input type="checkbox" name="madde'.$m_idx.'" '.$checked.'><span class="slider"></span></label></div></div>';
                        $m_idx++;
                    }
                    echo '</div>';
                }
                ?>
                <div class="section-title mt-5">5.2. ÜRÜN LİSTESİ</div>
                <div class="table-responsive"><table id="yasTable" class="table table-sm table-hover font-11"><thead class="bg-light"><tr><th>#</th><th>Kod</th><th>Bölüm</th><th>Ekipman</th><th>Yer</th><th>Erişim</th><th>Montaj</th><th>Test</th><th>Sesli</th><th>Işıklı</th><th>Adres</th></tr></thead><tbody></tbody></table></div>
                <button type="button" class="btn btn-outline-primary btn-sm mt-3" id="addRow"><i class="fa fa-plus"></i> Yeni Ürün Ekle</button>
            </div>
        </div>

        <div class="tab-pane fade" id="p69">
            <div class="card-soft">
                <div class="section-title">6-9. SONUÇ VE ONAY</div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label>6. Kusur Açıklamaları</label><textarea name="kusur_aciklamalari" class="form-control editor-wysi" style="height: 120px;"><?php echo $header_extra["kusur_aciklamalari"] ?? ""; ?></textarea></div>
                    <div class="col-md-6 mb-3"><label>7. Notlar</label><textarea name="notlar" class="form-control editor-wysi" style="height: 120px;"><?php echo $header_extra["notlar"] ?? ""; ?></textarea></div>
                    <div class="col-md-12 mb-3"><div class="d-flex justify-content-between align-items-center mb-2"><label class="mb-0">8. Sonuç ve Kanaat</label><button type="button" class="btn-template" onclick="applyExcelTemplate()"><i class="fa fa-copy mr-1"></i> Şablon Uygula</button></div><textarea id="sonuc_kanaat" name="sonuc_kanaat" class="form-control editor-wysi" style="height: 250px;"><?php echo $header_extra["sonuc_kanaat"] ?? ($bakim["result_note"] ?? ""); ?></textarea></div>
                </div>
                <div class="section-title mt-4">9. YETKİLİ BİLGİLERİ</div>
                <div class="row"><div class="col-md-3"><label>Adı Soyadı</label><input name="controller_peak" value="<?php echo $controller_peak["name"] ?? ""; ?>" class="form-control"></div><div class="col-md-3"><label>Diploma No</label><input name="controller_peak_diploma" value="<?php echo $controller_peak["diploma"] ?? ""; ?>" class="form-control"></div><div class="col-md-3"><label>EMO Sicil No</label><input name="controller_peak_emo" value="<?php echo $controller_peak["emo"] ?? ""; ?>" class="form-control"></div><div class="col-md-3"><label>Ekipnet No</label><input name="controller_peak_ekipnet" value="<?php echo $controller_peak["ekipnet"] ?? ""; ?>" class="form-control"></div></div>
            </div>
        </div>
    </div>
</form>

<script>
    var existingDedectors = <?php echo json_encode($dedectors); ?>;
    $(document).ready(function() {
        if(existingDedectors.length > 0) {
            existingDedectors.forEach(function(d) {
                if(typeof addRowWithData === "function") { addRowWithData(d); }
            });
        }
    });

    function previewReport() { 
        syncEditors();
        collectEquipmentData();
        var oldTarget = $('#myForm').attr('target'); 
        var oldAction = $('#myForm').attr('action'); 
        $('#myForm').attr('target', '_blank'); 
        $('#myForm').attr('action', 'index.php?p=reports/yas/report-view-yas&preview=1'); 
        $('#myForm').submit(); 
        $('#myForm').attr('target', oldTarget ? oldTarget : ''); 
        $('#myForm').attr('action', oldAction); 
    }
    
    function syncEditors() { if (typeof $.fn.wysihtml5 !== 'undefined') { $('.editor-wysi').each(function() { var editor = $(this).data("wysihtml5"); if(editor) { $(this).val(editor.editor.getValue()); } }); } }
    
    function collectEquipmentData() {
        var equipment = [];
        $('#yasTable tbody tr').each(function() {
            var row = $(this);
            equipment.push({
                kod: row.find('input[name="p_kod[]"]').val(),
                bolum: row.find('input[name="p_bolum[]"]').val(),
                ekipman: row.find('input[name="p_ekipman[]"]').val(),
                yer: row.find('select[name="p_yer[]"]').val(),
                erisim: row.find('select[name="p_erisim[]"]').val(),
                montaj: row.find('select[name="p_montaj[]"]').val(),
                test: row.find('select[name="p_test[]"]').val(),
                sesli: row.find('select[name="p_sesli[]"]').val(),
                isikli: row.find('select[name="p_isikli[]"]').val(),
                adresleme: row.find('select[name="p_adresleme[]"]').val()
            });
        });
        $('#equipment_data_json').val(JSON.stringify(equipment));
    }
    
    $('#myForm').on('submit', function() { syncEditors(); collectEquipmentData(); return validateForm('submitButton'); });
    function initEditors() { if (typeof $.fn.wysihtml5 !== 'undefined') { $('.editor-wysi').each(function() { if (!$(this).data("wysihtml5")) { $(this).wysihtml5({ "font-styles": true, "emphasis": true, "lists": true, "html": false, "link": true, "image": false, "color": false }); } }); } }
    $(document).ready(function() { initEditors(); $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) { initEditors(); }); });
    function applyExcelTemplate() { var template = "<b>Periyodik kontrol tarihi itibariyle...</b>"; var editorObj = $('#sonuc_kanaat').data("wysihtml5"); if(editorObj) { editorObj.editor.setValue(template); } }
</script>
<script src="include/js/yas.js"></script>
