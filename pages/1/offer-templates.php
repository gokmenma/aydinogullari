<?php


if ($_POST) {
	
	$id = @$_POST["id"];
	$title = $_POST["title"];
    $state = $_POST["state"];
    $content = $_POST["editor"];
    $regDate = date("Y-m-d");
    $creator = sesset("id");

// YENİ EKLEME YAPILIYORSA
if($_GET["type"] == "new")

	try {
		$regxs = $ac->prepare("INSERT INTO offertemplate SET State = ? , Title = ?, content = ? , regDate = ? , creator = ?");
		$regxs->execute(array($state, $title, $content, $regDate, $creator));

		$lastid = $ac->lastInsertId();

		if (isset ($_POST["default_template"])) {
			//önce diğer varsayılan kaldırılır
			$upquery = $ac->prepare("UPDATE offertemplate set default_template = 0");
			$upquery->execute();

			//Eklenen şablon varsayılan yapılır
			$upquery = $ac->prepare("UPDATE offertemplate set default_template = 1 WHERE id = ?");
			$upquery->execute(array($lastid));
		}
	
	} catch (PDOException $e) {
		echo "Hata : " . $e->getMessage();
	}

}

if ($_GET["type"] == "update") {
	$regxs = $ac->prepare("UPDATE offertemplate SET State = ? , Title = ?, content = ? , regDate = ? , creator = ? WHERE id = ?");
	$regxs->execute(array($state, $title, $content, $regDate, $creator, $id));

}


if ($id && @$_GET["mode"] == "delete" && @$_GET["code"] == "04md177") {

		$pdq = $ac->prepare("DELETE FROM offertemplate WHERE id = ?");
		$pdq->execute(array($id));
	
}

?>
<div class="pd-20 bg-white border-radius-16 box-shadow mb-20">

	<form method="POST" id="myForm">
		<div class="clearfix justify-content-between mb-20">
			<div class="pull-left">
				<h5 class="text-blue">
					<?php echo $pdat["p_title"]; ?>
				</h5>
			</div>
			<div class="float-right">
				<!-- Button trigger modal -->
				<button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
					data-target="#exampleModalCenter">
					<i class="fa fa-plus"></i> Yeni
				</button>

				<!-- Modal -->
				<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
					aria-labelledby="exampleModalCenterTitle " aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="exampleModalLongTitle">Teklif Üst/Alt Şablon Tanımla</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body mr-3">
								<!-- MODAL BODY -->

								<div class="form-group row">
									<label for="default_template" class="col-md-2">Varsayılan Yap </label>
									<div class="custom-control custom-checkbox ml-3">
										<input class="custom-control-input" type="checkbox" value=""
											name="default_template" id="default_template">
										<label class="custom-control-label" for="default_template">

										</label>
									</div>
								</div>
								<div class="form-group row">

									<label for="Title" class="col-md-2">Üst Bilgi Adı : </label>
									<div class="col-md-10">
										<input required type="text" class="form-control" name="title" id="title">
									</div>
								</div>
								<div class="form-group row">
									<label for="headerType" class="col-md-2">Kategori : </label>
									<div class="col-md-10">
										<select required name="state" id="state" class="selectpicker form-control"
											data-style="border bg-white">
											
											<option <?php echo $type == "Header" ? ' selected' : '' ?>   value="Header">
												Üst Bilgi</option>
											<option <?php echo $type == "Footer" ? ' selected' : '' ?> data-id="Alt Bilgi" value="Footer">
												Alt Bilgi</option>
										</select>
									</div>
								</div>


								<div class="form-group row">
									<label for="" class="col-md-2">İçerik : </label>
									<div class="col-md-10">
										<div class="html-editor">
											<textarea required name="content" id="template-content"
												class="textarea_editor form-control border-radius-0"
												placeholder="Bir şeyler yaz ..."></textarea>
										</div>
									</div>
								</div>



								<!-- MODAL BODY -->
							</div>
							<div class="modal-footer mr-3">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
								<button type="button" id="submitButtonByAjax" class="btn btn-primary">Kaydet</button>
							</div>
							<input id="id" type="hidden" name="id" value="0">
						</div>
					</div>
				</div>
			</div>

		</div>
	</form>

	<table class="data-table table-hover table-bordered table-responsive">
		<thead>
			<tr>
				<td style="max-width:40px; width:40px">Sıra</td>
				<td>Şablon Başlığı</td>
				<td>Şablon Türü</td>
				<td>İçerik</td>
				<td style="max-width:50px; width:50px">İşlemler</td>

			</tr>
		</thead>
		<tbody>


			<?php
			$templates = $ac->prepare("Select * from offertemplate");
			$templates->execute();

			$satir = 1;
			while ($row = $templates->fetch(PDO::FETCH_ASSOC)) {


				?>
				<tr>
					<td class="text-center">
						<?php echo $satir ?>
					</td>
					<td>
						<?php echo $row["Title"] ?>
					</td>
					<td data-id="<?php echo $row["State"]?>" >
						<?php echo $row["State"] =="Header" ? "Üst Bilgi" : "Alt Bilgi"; ?>
						<!-- <?php echo $row["State"] ; ?> -->
					</td>
					<td>
						<?php echo $row["Content"] ?>
					</td>
					<td class="text-center">
						<a href="#" class="edit" data-id="<?php echo $row["id"] ?>" data-tooltip="Düzenle"
							data-toggle="modal" data-target="#exampleModalCenter">
							<span class=" btn btn-sm btn-outline-info">
								<i class="fa fa-pencil"></i>
							</span>
						</a>
						<button type="button" class="btn btn-sm btn-danger" data-tooltip="Sil"
								onClick="deleteRecord('<?php echo $row["Title"]; ?> başlıklı şablonu silmek istediğinize emin misiniz?','<?php echo $row["id"]; ?>','offer-templates','offertemplate')"><i
									class="fa fa-trash"></i></button>
					</td>
				</tr>
				<?php
				$satir++;
			} ?>

		</tbody>
	</table>


	<script src="include/js/data-table.js"></script>
	<script src="../../include/js/offer-template.js"></script>
	<script>
		submitButton.on('click', function () {
			addType("offer-templates", "Teklif Şablonu");
		});
	</script>