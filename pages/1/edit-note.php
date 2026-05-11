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
<div class="pd-ltr-20 xs-pd-10-10">
<div class="min-height-200px">
<!-- Default Basic Forms Start -->
<div class="html-editor pd-20 bg-white border-radius-10 box-shadow mb-30">
	<div class="clearfix">
		<div class="pull-left">
			<h4 class="text-blue"><?php echo $pdat["p_title"]; ?></h4>
			<p class="mb-30 font-14">Sayfadaki <font color="red">(*)</font> yıldız ile belirtilen alanları boş bırakmayın..<br></p>
		</div>
		<div class="float-right">
		<!--	<button type="submit" style="float:right;" type="button" class="float-right btn btn-primary">Güncelle</button>
			<button type="submit" style="float:right;" type="button" class="btn btn-secondary">Listeye Dön</button> -->
			
			<button class="btn btn-primary mr-2" onclick="validateForm()">Güncelle</button>
				<a href="index.php?p=all-notes">
					<input type="submit" value="Listeye Dön" class="btn btn-success  mr-3">
				</a>
		</div>
	</div>
	<form enctype="multipart/form-data" method="POST" action="" id=myForm>

		<div class="row">
			<div class="col-md-12 col-sm-12">
				<div class="form-group">
					<label for="title">
						<font color="red">(*)</font>Başlık
					</label>
					<input name="title" value="<?php echo $ck["title"]; ?>" class="form-control" type="text">

				</div>
			</div>
		</div>

		<div class="form-group row">

		<label class="col-md-2">Başlangıç Tarihi</label>
			<div class="col-md-4 col-sm-12">
				<input name="startdate" class="form-control date-picker" value="<?php echo redate_tr($ck["dates"]); ?>" placeholder="Tarih Seçin" type="text">
			</div>

			<label class="col-md-2">Kategori</label>
			<div class="col-md-4">
				<select name="cat" class="form-control">
					<?php
					$nqu = $ac->prepare("SELECT * FROM note_categories");
					$nqu->execute();
					while ($nn = $nqu->fetch(PDO::FETCH_ASSOC)) {
					?>
						<option <?php echo $nn["id"] == $ck["category"] ? "selected" : ""; ?> value="<?php echo $nn["id"]; ?>"><?php echo $nn["title"]; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group row">

			<label class="col-md-2">Son Tarih</label>
			<div class="col-sm-12 col-md-4">
				<input name="lastdate" class="form-control date-picker" value="<?php echo redate_tr($ck["lastdate"]); ?>" placeholder="Tarih Seçin" type="text">
			</div>

		<label class="col-md-2" for="lastdate">Aciliyet</label>
    	<div class="col-md-4">
        <?php
        $urgencyValues = ["Yüksek", "Orta", "Düşük"];

        foreach ($urgencyValues as $urgency) {
            $isChecked = ($urgency == $urgencyFromDatabase) ? "checked" : "";
            $fontColor = ($urgency == "Yüksek") ? "red" : (($urgency == "Orta") ? "blue" : "green");

            echo '
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="customRadioInline' . ucfirst($urgency) . '" name="urgency" value="' . $urgency . '" class="custom-control-input" ' . $isChecked . '>
                <label class="custom-control-label font-weight-bold" for="customRadioInline' . ucfirst($urgency) . '">
                    <font color="' . $fontColor . '">' . $urgency . '</font>
                </label>
            </div>';
        }
        ?>
    </div>
		</div>
		<div class="form-group">
			<div class="html-editor">
				<h4>Not Oluştur</h4>
				<p></p>
				<textarea name="desc" class="textarea_editor form-control border-radius-0" placeholder="Bir şeyler yaz ..."><?php echo $ck["descs"]; ?></textarea><br>
			</div>
	</form>
</div>
</div>
</div>










