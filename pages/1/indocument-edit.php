<?php
permcontrol("docedit");
$eid = $_GET["id"];

$cerq = $ac->prepare("SELECT * FROM evraktakip WHERE id = ?");
$cerq->execute(array($eid));
$cc = $cerq->fetch(PDO::FETCH_ASSOC);

if ($_POST) {
	if(!$_POST["firma"] || !$_POST["evrakturu"] || !$_POST["kategori"] || !$_POST["teslimeden"]){
		header("Location: index.php?p=indocument-edit&st=empties");
		exit;
	}
    $firma = $_POST["firma"];
    $evrakturu = $_POST["evrakturu"];
    $kategori = $_POST["kategori"];
    $adet = $_POST["adet"];
    $teslimalan = sesset("id"); 
   	$teslimtarihi = $_POST["teslimtarihi"];
	$estatu=$_POST["estatu"];
    $aciklama = $_POST["aciklama"];
	$teslimeden = $_POST["teslimeden"];
	$upxsx = $ac->prepare("UPDATE evraktakip SET
				firma = ?,
                evrakturu = ?,
				kategori = ?,
				adet = ?,
				teslimalan = ?,
                teslimeden = ?,
                teslimtarihi = ?,
				estatu = ?,
                aciklama = ? WHERE id = ?");

		$upxsx->execute(array($firma,$evrakturu,$kategori,$adet,$teslimalan,$teslimeden,$teslimtarihi,$estatu,$aciklama,$eid ));

		if($upxsx)
		{
			if ($evrakturu=="Gelen")
			{
				header("Location: index.php?p=view-indocument&id=$eid&up=success&st=yes&mdcode=14");
			}
			if ($evrakturu=="Giden")
			{
				header("Location: index.php?p=view-outdocument&id=$eid&up=success&st=yes&mdcode=14");
			}
	    	
		}
		
	}
	
	if(@$_GET["st"] == "empties"){
		showAlert('alert', '(*) ile işaretli alanları boş bırakmadan tekrar deneyin.');
		
	}


if (@$_GET["st"] == "empties") {
    ?>
    <div class="alert alert-danger" role="alert">
        (*) ile işaretli alanları boş bırakmadan tekrar deneyin.
    </div>
<?php
}

if (@$_GET["st"] == "newsuccess") {
    showAlert("success", "İşlem Başarı ile tamamlandı!");
}
?>


<form  method="POST" action="">
<div class="pd-20 bg-white border-radius-16 box-shadow mb-30">
	<div class="clearfix">
		<div class="pull-left">
			<h4 class="text-blue"><?php echo $pdat["p_title"]; ?></h4>
			<p class="mb-30 font-14">Sayfadaki <font color="red">(*)</font> yıldız ile belirtilen alanları boş
				bırakmayın..<br></p>
		</div>
		<input type="submit" id="submitbuton"  value="Güncelle" class="float-right btn btn-primary mr-2">
		
	</div>
	

		<div class="row">
        
        <div class="col-md-6 col-sm-12">
				<div class="form-group">
					<label>
						<font color="red">(*)</font>Firma
					</label>
					<select required name="firma" id="firma" class="selectpicker form-control" data-live-search="true"	data-style="border bg-white">
			<?php
					$selected_company = $cc["firma"];
					$tt = $ac->prepare("SELECT * FROM customers");
					$tt->execute();
					while ($mm2 = $tt->fetch(PDO::FETCH_ASSOC)) {
						$selected = ($mm2["id"] == $selected_company) ? "selected" : "";
			?>
						<option <?php echo $selected;?> value="<?php echo $mm2["id"];?>"><?php echo $mm2["company"];?></option>
			<?php 
				}
			?>
			</select>

				</div>
			</div>

			<div class="col-md-6 col-sm-12">
				<div class="form-group"><label>
						<font color="red">(*)</font>Evrak Türü
					</label>
					<select required name="evrakturu"  id="evrakturu" class="selectpicker form-control" data-style="border bg-white ">
						<option <?php echo $cc["evrakturu"] == "Gelen" ? "selected" : "";?> value="Gelen">Gelen Evrak</option>
                        <option <?php echo $cc["evrakturu"] == "Giden" ? "selected" : "";?> value="Giden">Giden Evrak</option>
					</select>
				</div>
			</div>
			<div class="col-md-6 col-sm-12">
				<div class="form-group">
					<label>
						<font color="red">(*)</font>Kategori
					</label>
					<input required name="kategori"  class="form-control" placeholder="Lütfen kategori yazınız."
					value="<?php echo $cc["kategori"];?>" type="text">
				</div>
			</div>

			<div class="col-md-6 col-sm-12">
				<div class="form-group"><label><font color="red">(*)</font>Adet</label>
                <input required name="adet"  placeholder="Evrak sayısını girin" class="form-control" type="number" value="<?php echo $cc["adet"]; ?>">
				</div>
			</div>
            <div class="col-md-4 col-sm-12">
				<div class="form-group"><label>
						<font color="red">(*)</font>Teslim Alan
					</label>
					<input disabled class="form-control" value="<?php echo sesset("username"); ?>" type="text">
				</div>
			</div>
            <div class="col-md-4 col-sm-12">
				<div class="form-group"><label>
						<font color="red">(*)</font>Teslim Eden
					</label>
					<select name="teslimeden" title="Seçiniz" class="selectpicker form-control" data-live-search="true"	data-style="border bg-white">
					<?php
					$selected_user = $cc["teslimeden"];
					$tt = $ac->prepare("SELECT * FROM users");
					$tt->execute();
					while ($mm2 = $tt->fetch(PDO::FETCH_ASSOC)) {
						$selected = ($mm2["id"] == $selected_user) ? "selected" : "";
					?>
						<option <?php echo $selected;?> value="<?php echo $mm2["id"];?>"><?php echo $mm2["username"];?></option>
					<?php 
						}
					?>
						</select>
				</div>
			</div>

            <div class="col-md-4 col-sm-12">
				<div class="form-group"><label>Teslim Alma Tarihi</label>
                <input name="teslimtarihi"	 type="text"  placeholder="Boş bırakırsanız otomatik bugün seçilir." class="custom-select col-12 date-picker" value="<?php echo $cc["teslimtarihi"]; ?>">
				</div>
			</div>

			<div class="col-md-8 col-sm-12">
				<div class="form-group">
					<label>Açıklama </label>
					<textarea name="aciklama"  class="form-control" type="text" placeholder="Evrakla ilgili açıklama girebilirsiniz"><?php echo $cc["aciklama"]; ?></textarea>
				</div>
				
			</div>
			<div class="col-md-4 col-sm-12">
				<div class="form-group">
					<label>Evrak Durumu</label>
					<select name="estatu" id="estatu" class="selectpicker form-control" data-style="btn-outline-secondary"  data-selected-text-format="count">
					<option <?php echo $cc["estatu"] == "Bekliyor" ? "selected" : ""; ?> data-content="<span class='badge badge-warning'>Bekliyor</span>" value="Bekliyor">Bekliyor</option>
						<option <?php echo $cc["estatu"] == "Çalışıyor" ? "selected" : ""; ?> data-content="<span class='badge badge-primary'>Çalışıyor</span>" value="Çalışıyor">Çalışıyor</option>
						<option <?php echo $cc["estatu"] == "Tamamlandı" ? "selected" : ""; ?> data-content="<span class='badge badge-success'>Tamamlandı</span>" value="Tamamlandı">Tamamlandı</option>
					</select>
				</div>
			</div>
		</div>
		
	</form>
</div>



					