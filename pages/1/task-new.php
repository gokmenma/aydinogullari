<?php
permcontrol("todoadd");
if ($_POST) {

	$title = @$_POST["title"];
	$desc = @$_POST["desc"];
	$okey = @$_POST["okey"];
	$ldate = date_tr($_POST["lastdate"]);
	$sdate = date_tr($_POST["startdate"]);

	if (empty($title) || empty($desc) || empty($ldate)) {
		header("Location: index.php?p=task-new&st=empties");
		exit;
	}


	$insq = $ac->prepare("INSERT INTO todolist SET
	title = ?,
	description = ?,
	regdate = ?,
	last_date = ?,
	creativer = ?,
	okey = ?");

	$insq->execute(array($title, $desc, $sdate, $ldate, sesset("id"), $okey));

	header("Location: index.php?p=task-new&st=newsuccess");
}



$dat = $ac->prepare("SELECT * FROM mainservices ");
$dat->execute();

if (@$_GET["st"] == "empties") {
	showAlert("alert", "Zorunlu alanları boş bırakmayınız");
}
if (@$_GET["st"] == "newsuccess") {
	showAlert("success", "Yapılacak görev başarı ile kaydedildi");
}
?>

<form method="POST" id="myForm">
	<div class="pd-20 bg-white border-radius-16 box-shadow mb-30">
		<div class="clearfix mb-30">
			<div class="pull-left">
				<h4 class="text-blue">
					<?php echo $pdat["p_title"]; ?>
				</h4>
				<p class="mb-30 font-14">Sayfadaki <font color="red">(*)</font> yıldız ile belirtilen alanları boş
					bırakmayın..<br></p>
			</div>
			<div class="float-right">

				<button type="button" id="submitButton" onclick="validateForm()" class="btn btn-sm btn-primary"><i
						class="fa fa-save"></i>
					Kaydet</button>
				<a type="button" href="index.php?p=tasks" class="btn btn-sm btn-secondary text-white"><i class="fa fa-list"></i>
					listeye Dön</a>
			</div>

		</div>


		<div class="form-group row">
			<label for="title" class="col-md-2 col-sm-12">
				<font color="red">(*)</font>Başlık
			</label>
			<div class="col-md-10">
				<input name="title" value="" required class="form-control" type="text">
			</div>

		</div>
		<div class="row">

			<div class="col-md-6">

				<div class="form-group row">

					<label class="col-md-4">
						<font color="red">(*)</font>Durum
					</label>
					<div class="col-sm-12 col-md-8">
						<select name="okey" class="selectpicker form-control" data-style="border bg-white">
							<option disabled value="1">Yapıldı</option>
							<option selected value="0">Yapılmadı</option>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<label for="startdate" class="col-md-4 col-sm-12">Başlangıç Tarihi</label>
					<div class="col-md-8 col-sm-12">
						<input name="startdate" autocomplete="off" class="form-control date-picker"
							placeholder="Tarih Seçin" type="text">
					</div>
				</div>

				<div class="form-group row">

					<label for="lastdate" class="col-md-4">
						<font color="red">(*)</font>Son Tarih
					</label>
					<div class="col-sm-12 col-md-8">
						<input name="lastdate" class="form-control date-picker" autocomplete="off" required
							placeholder="Tarih Seçin" type="text">
					</div>
				</div>
			</div>
			<div class="col-md-6">

				<div class="form-group row">
					<label for="desc" class="col-md-3 weight-500">Açıklama</label>

					<div class="col-md-9 col-sm-12">

						<textarea required name="desc" class=" form-control border-radius-0"
							placeholder="Bir şeyler yaz ..."></textarea><br>

					</div>
				</div>
			</div>

		</div><br>
	</div>
</form>