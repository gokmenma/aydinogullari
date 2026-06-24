<?php
permcontrol("support-request-add");

$getNumber = setNumber("support_ticket");
if (!$getNumber) {
    $getNumber = 1;
}
$ticketNo = "TKT" . str_pad($getNumber, 5, "0", STR_PAD_LEFT);

if ($_POST) {
    $title = @$_POST["title"];
    $category = @$_POST["category"];
    $urgency = @$_POST["urgency"];
    $description = @$_POST["description"];
    $creator = $_SESSION["lid"];
    
    $attachment_path = null;
    
    // Opsiyonel Dosya Yükleme İşlemi
    if (isset($_FILES["attachment"]) && $_FILES["attachment"]["name"] != "") {
        $dizin = "files/";
        // Eşsiz dosya adı oluştur
        $rast = rand(1000, 9999);
        $fileName = $rast . "_" . basename($_FILES["attachment"]["name"]);
        $hedef = $dizin . $fileName;
        
        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $hedef)) {
            $attachment_path = $fileName;
        }
    }
    
    try {
        $insq = $ac->prepare("INSERT INTO support_requests SET 
            ticket_no = ?, 
            title = ?, 
            description = ?, 
            category = ?, 
            urgency = ?, 
            status = 'pending_approval', 
            created_by = ?, 
            attachment_path = ?");
            
        $result = $insq->execute([
            $ticketNo,
            $title,
            $description,
            $category,
            $urgency,
            $creator,
            $attachment_path
        ]);
        
        if ($result) {
            // Sayaç numarasını arttır ve güncelle
            $getNumber += 1;
            $upquery = $ac->prepare("UPDATE define_numbers SET support_ticket = ?");
            $upquery->execute([$getNumber]);
            
            header("Location: index.php?p=support-list&st=newsuccess");
            exit();
        }
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
}
?>

<form enctype="multipart/form-data" method="POST" action="" id="myForm">
    <div class="support-request-manage-wrapper">
        <!-- Header Card -->
        <div class="premium-header-card animate-fade-in">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fa fa-life-ring"></i>
                    </div>
                    <div class="header-title">
                        <h4><?php global $pdat; echo $pdat["p_title"] ?? 'Yeni Destek Talebi'; ?></h4>
                        <span class="header-number-badge">
                            <i class="fa fa-tag"></i> Talep No: <?php echo $ticketNo; ?>
                        </span>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="index.php?p=support-list" class="btn-header btn-header-list mr-2">
                        <i class="fa fa-list"></i> Taleplerim
                    </a>
                    <button type="button" id="submitButton" onclick="validateForm()" class="btn-header btn-header-save">
                        <i class="fa fa-save"></i> Kaydet ve Gönder
                    </button>
                </div>
            </div>
        </div>

        <!-- Kart 1: Talep Bilgileri -->
        <div class="form-card mb-4 animate-fade-in">
            <div class="form-card-header">
                <div class="card-icon card-icon-blue">
                    <i class="fa fa-info-circle"></i>
                </div>
                <div>
                    <h5>Talep Detayları</h5>
                    <p>Destek talebinize ait kategori, aciliyet derecesi ve başlık bilgilerini girin</p>
                </div>
            </div>
            
            <div class="form-grid">
                <!-- Başlık -->
                <div class="form-field full-width">
                    <label for="title"><font color="red">(*)</font> Talep Başlığı</label>
                    <input name="title" id="title" value="" class="form-control" type="text" placeholder="Talebinizi özetleyen kısa bir başlık giriniz" required>
                </div>

                <!-- Kategori -->
                <div class="form-field">
                    <label for="category">Kategori</label>
                    <select name="category" id="category" class="selectpicker form-control" data-style="border bg-white" required>
                        <option value="Yazılım">Yazılım / Sistem Hatası</option>
                        <option value="Donanım">Donanım / Ekipman Talebi</option>
                        <option value="Altyapı">Network / İnternet / Altyapı</option>
                        <option value="Genel">Genel Destek / Soru</option>
                        <option value="Diğer">Diğer</option>
                    </select>
                </div>

                <!-- Aciliyet -->
                <div class="form-field">
                    <label for="urgency">Aciliyet Durumu</label>
                    <select name="urgency" id="urgency" class="selectpicker form-control" data-style="border bg-white" required>
                        <option value="Düşük">Düşük</option>
                        <option value="Normal" selected>Normal</option>
                        <option value="Acil">Acil</option>
                        <option value="Çok Acil">Çok Acil</option>
                    </select>
                </div>

                <!-- Dosya Eki -->
                <div class="form-field full-width">
                    <label for="attachment">Ek Dosya / Görsel <span class="text-muted">(Opsiyonel)</span></label>
                    <input name="attachment" id="attachment" type="file" class="form-control height-auto">
                    <small class="form-text text-muted">Hatanın ekran görüntüsü veya ilgili belgeyi yükleyebilirsiniz (Maks: 5MB).</small>
                </div>
            </div>
        </div>

        <!-- Kart 2: Detaylı Açıklama -->
        <div class="form-card mb-4 animate-fade-in">
            <div class="form-card-header">
                <div class="card-icon card-icon-purple">
                    <i class="fa fa-pencil-square-o"></i>
                </div>
                <div>
                    <h5>Talebin Açıklaması</h5>
                    <p>Karşılaştığınız sorunu veya talebinizin detaylarını ayrıntılı şekilde açıklayın</p>
                </div>
            </div>
            <div class="editor-wrapper" style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                <textarea name="description" id="description" class="form-control" rows="8" placeholder="Yaşadığınız sorunu buraya yazın..." required></textarea>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function () {
    $(".selectpicker").selectpicker({
        selectAllText: "Tümünü Seç",
        deselectAllText: 'Seçimi Temizle',
        style: "border bg-white",
        liveSearch: true,
        liveSearchPlaceholder: "Ara..",
        noneResultsText: 'Eşleşen kayıt yok {0}',
        size: 5,
        noneSelectedText: "Seçim Yapınız!"
    });
});
</script>
