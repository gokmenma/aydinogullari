<?php
permcontrol("sercategory");
if ($_POST && @$_GET["mode"] != "delete") {

	$title = $_POST["title"];

	$ekle = $ac->prepare("INSERT INTO upfile_categories SET
	title = ?");
	$ekle->execute(array($title));
	header("Location:index.php?p=file-categories&st=newsuccess");
}

 
 if (@$_GET["id"] && @$_GET["mode"] == "delete" && @$_GET["code"] == "04md177") {
	// $stcontrol = $ac->prepare("SELECT * FROM upfile_categories WHERE id = ?");
	// $stcontrol->execute(array($xid));
	// $sts = $stcontrol->fetch(PDO::FETCH_ASSOC);
	// if (!$sts) {
	// 	header("Location: index.php?p=file-categories&err=true");
	// 	exit;
	// 	die;
	// }

	$stdel = $ac->prepare("DELETE FROM upfile_categories WHERE id = ?");
	$stdel->execute(array($_GET["id"]));

	//header("Location: index.php?p=file-categories&type=delete&code=0882md25&pid=$pids");

}


?>
<div class="pd-20 bg-white border-radius-16 box-shadow mb-30">
	<?php


	if (@$_GET["st"] == "newsuccess") {
		showAlert("success", "Dosya kategorisi başarı ile eklendi!");
		?>
		<script>
				window.history.pushState({}, '', 'index.php?p=file-categories')
		</script>
	<?php }
	?>
	<div class="clearfix mb-20">
		<div class="pull-left">
			<h5 class="text-blue">Dosya Kategorileri Yönetimi</h5>

		</div>
		<div>
			<button type="submit" id="submitButton" onclick="validateForm()"
				class="btn btn-primary float-right btn-sm"><i class="fa fa-save"></i> Kaydet</button>
		</div>

	</div>
	<form method="POST" id="myForm">

		<div class="row">
			<h4>&nbsp;&nbsp;Yeni Dosya Kategorisi Oluştur</h4><br><br>

			<div class="col-sm-12 col-md-12">



				<div class="form-group">
					<label for="title">
						<font color="red">(*)</font>Kategori Adı:
					</label>
					<input required name="title" value="" class="form-control" type="text">

				</div>

			</div>
		</div>
	</form>

</div>

<div class="pd-20 bg-white border-radius-16 box-shadow mb-30">
	<div class="pull-left mb-30">
		<h5 class="text-blue">Kategori Listesi</h5>

	</div>
	<table class="data-table table-bordered table-hover">
		<thead>
			<tr>
				<th style="max-width:40px; width:40px">Sıra</th>
				<th>Kategori <br> Adı</th>
				<th>Oluşturulma Tarihi</th>
				<th class="text-nowrap text-center datatable-nosort" style="max-width:70px; width:70px">İşlemler</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$categ1 = $ac->prepare("SELECT * FROM upfile_categories ORDER BY id ASC");
			$categ1->execute();
			$kx = 1;
			while ($ccs = $categ1->fetch(PDO::FETCH_ASSOC)) {


				?>
				<tr>
					<td class="text-center" >
						<?php echo $kx; ?>
					</td>
					<td>
						<?php echo $ccs["title"]; ?>
					</td>
					<td>
						<?php echo $ccs["regdate"]; ?>
					</td>


					<td class="text-center" >

						<a href="index.php?p=edit-fcategory&id=<?php echo $ccs["id"]; ?>" class="btn btn-sm btn-secondary"
							data-tooltip="Düzenle"><i class="fa fa-edit"></i></span></a>

							<?php if (permtrue("filedelete")) { ?>
							<button type="button" class="btn btn-sm btn-danger" data-tooltip="Sil"
								onClick="deleteRecord('Kategoriyi silmek istediğinize emin misiniz?','<?php echo $ccs["id"]; ?>','file-categories')">
								<i class="fa fa-trash"></i></button>
						<?php } ?>
						
							<!-- <a onClick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')"
							href="index.php?p=file-categories&mode=delete&code=04md177&md=active&xid=<?php echo $ccs["id"]; ?>"
							class="btn btn-danger btn-sm" data-tooltip="Sil!"><i class="fa fa-trash"></i></a> -->
					</td>

				</tr>
				<?php
				$kx = $kx + 1;
			} ?>
		</tbody>
	</table>



</div>