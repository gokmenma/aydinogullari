<?php
permcontrol("noteedit");
if (!$_GET["nid"]) {
	header("Location:index.php?p=all-notes");
	exit;
}
$nid = $_GET["nid"];
if ($_POST) {

	$title = @$_POST["title"];
	$desc = @$_POST["desc"];

	$sdate = @$_POST["startdate"] ? date_tr($_POST["startdate"]) : TODAY;
	$lastdate = @$_POST["lastdate"];

	$urg = $_POST["urgency"];
	$cat = $_POST["cat"];
	if (empty($title) || empty($desc)) {
		header("Location: index.php?p=new-note&st=empties");
		exit;
	}
	$insq = $ac->prepare("UPDATE notes SET
	category = ?,
	title = ?,
	dates = ?,
	lastdate = ?,
	urgency = ?,
	descs = ? WHERE id = ?");

	$result =$insq->execute(array($cat, $title, $sdate, $lastdate, $urg, $desc, $nid));

	if($result){
		header("Location: index.php?p=all-notes&st=newsuccess");
	}
}

$ckk = $ac->prepare("SELECT * FROM notes WHERE id = ?");
$ckk->execute(array($nid));
$ck = $ckk->fetch(PDO::FETCH_ASSOC);
$urgencyFromDatabase = $ck['urgency'];

?>
<style>
    .urgency-selector-wrapper {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-top: 5px;
    }
    .urgency-option-premium {
        position: relative;
        cursor: pointer;
        flex: 1;
    }
    .urgency-option-premium input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }
    .urgency-custom-radio {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 16px;
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        background-color: #f8fafc;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        color: #4a5568;
        text-align: center;
    }
    .urgency-custom-radio i {
        font-size: 0.6rem;
        transition: transform 0.2s;
    }
    
    .urgency-high input:checked ~ .urgency-custom-radio {
        background-color: #fef2f2;
        border-color: #ef4444;
        color: #ef4444;
        box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1), 0 2px 4px -1px rgba(239, 68, 68, 0.06);
    }
    .urgency-high .urgency-custom-radio i {
        color: #ef4444;
    }
    
    .urgency-medium input:checked ~ .urgency-custom-radio {
        background-color: #eff6ff;
        border-color: #3b82f6;
        color: #3b82f6;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1), 0 2px 4px -1px rgba(59, 130, 246, 0.06);
    }
    .urgency-medium .urgency-custom-radio i {
        color: #3b82f6;
    }
    
    .urgency-low input:checked ~ .urgency-custom-radio {
        background-color: #f0fdf4;
        border-color: #22c55e;
        color: #22c55e;
        box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.1), 0 2px 4px -1px rgba(34, 197, 94, 0.06);
    }
    .urgency-low .urgency-custom-radio i {
        color: #22c55e;
    }
    
    .urgency-option-premium:hover .urgency-custom-radio {
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }
</style>

<form enctype="multipart/form-data" method="POST" action="" id="myForm">
    <div class="edit-note-manage-wrapper">
        <!-- Header Card -->
        <div class="premium-header-card animate-fade-in">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fa fa-sticky-note"></i>
                    </div>
                    <div class="header-title">
                        <h4><?php echo $pdat["p_title"] ?? 'Not Düzenle'; ?></h4>
                        <span class="header-number-badge">
                            <i class="fa fa-tag"></i> Not ID: #<?php echo $nid; ?>
                        </span>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="index.php?p=all-notes" class="btn-header btn-header-list mr-2">
                        <i class="fa fa-list"></i> Listeye Dön
                    </a>
                    <button type="button" id="submitButton" onclick="validateForm()" class="btn-header btn-header-save">
                        <i class="fa fa-save"></i> Güncelle
                    </button>
                </div>
            </div>
        </div>

        <!-- Kart 1: Not Bilgileri -->
        <div class="form-card mb-4 animate-fade-in">
            <div class="form-card-header">
                <div class="card-icon card-icon-blue">
                    <i class="fa fa-info-circle"></i>
                </div>
                <div>
                    <h5>Not Bilgileri</h5>
                    <p>Notunuza ait genel başlık, aciliyet derecesi, kategori ve tarih detayları</p>
                </div>
            </div>
            
            <div class="form-grid">
                <!-- Başlık (Full Width) -->
                <div class="form-field full-width">
                    <label for="title"><font color="red">(*)</font> Başlık</label>
                    <input name="title" id="title" value="<?php echo htmlspecialchars($ck["title"] ?? '', ENT_QUOTES); ?>" class="form-control" type="text" placeholder="Not başlığını giriniz" required>
                </div>

                <!-- Aciliyet -->
                <div class="form-field">
                    <label>Aciliyet</label>
                    <div class="urgency-selector-wrapper">
                        <?php
                        $urgencyValues = ["Yüksek", "Orta", "Düşük"];
                        foreach ($urgencyValues as $urgency) {
                            $isChecked = ($urgency == $urgencyFromDatabase) ? "checked" : "";
                            $classSuffix = ($urgency == "Yüksek") ? "high" : (($urgency == "Orta") ? "medium" : "low");
                            echo '
                            <label class="urgency-option-premium urgency-' . $classSuffix . '">
                                <input type="radio" id="customRadioInline' . ucfirst($urgency) . '" name="urgency" value="' . $urgency . '" ' . $isChecked . '>
                                <span class="urgency-custom-radio"><i class="fa fa-circle"></i> ' . $urgency . '</span>
                            </label>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Kategori -->
                <div class="form-field">
                    <label for="cat">Kategori</label>
                    <select name="cat" id="cat" class="selectpicker form-control" data-style="border bg-white">
                        <?php
                        $nqu = $ac->prepare("SELECT * FROM note_categories");
                        $nqu->execute();
                        while ($nn = $nqu->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                            <option <?php echo $nn["id"] == $ck["category"] ? "selected" : ""; ?> value="<?php echo $nn["id"]; ?>"><?php echo $nn["title"]; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Başlangıç Tarihi -->
                <div class="form-field">
                    <label for="startdate">Başlangıç Tarihi</label>
                    <input name="startdate" id="startdate" class="form-control date-picker" autocomplete="off" value="<?php echo redate_tr($ck["dates"]); ?>" placeholder="Tarih Seçin" type="text">
                </div>

                <!-- Son Tarih -->
                <div class="form-field">
                    <label for="lastdate">Son Tarih</label>
                    <input name="lastdate" id="lastdate" class="form-control date-picker" autocomplete="off" value="<?php echo redate_tr($ck["lastdate"]); ?>" placeholder="Tarih Seçin" type="text">
                </div>
            </div>
        </div>

        <!-- Kart 2: Not İçeriği -->
        <div class="form-card mb-4 animate-fade-in">
            <div class="form-card-header">
                <div class="card-icon card-icon-purple">
                    <i class="fa fa-pencil-square-o"></i>
                </div>
                <div>
                    <h5>Not İçeriği</h5>
                    <p>Not içeriğini ve detaylı açıklamalarını giriniz</p>
                </div>
            </div>
            <div class="editor-wrapper" style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                <textarea name="desc" class="textarea_editor form-control border-radius-8" placeholder="Bir şeyler yaz ..."><?php echo $ck["descs"]; ?></textarea>
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
		})
	})
</script>










