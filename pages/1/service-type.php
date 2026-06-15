<?php


if ($_POST && $_GET["type"] == "new") {
	$title = $_POST["title"];
	$ekle = $ac->prepare("INSERT INTO units SET
	title = ?,
	regdate = ?,
	statu = ?");
	$ekle->execute(array($title, TODAY . " - " . date("H:i:s") . "", 2));

}

if ($_POST && $_GET["type"] == "update") {
	$title = $_POST["title"];
	$id = $_POST["id"];

	$ekle = $ac->prepare("UPDATE units SET
	title = ?,
	regdate = ?,
	statu = ? WHERE id = ?", );
	$ekle->execute(array($title, TODAY . " - " . date("H:i:s") . "", 2, $id));

}

$id = @$_POST["id"];
if ($id && @$_GET["mode"] == "delete" && @$_GET["code"] == "04md177") {

	if ($id != 6) {
		$pdq = $ac->prepare("DELETE FROM units WHERE id = ?");
		$pdq->execute(array($id));
	}
}





?>
<div class="pd-20 bg-white border-radius-16 box-shadow mb-20">

	<form method="POST" id="myForm">
		<div class="clearfix justify-content-between mb-20">
			<div class="pull-left">
				<h5 class="text-blue"> <?php echo $pdat["p_title"]; ?></h5>
			</div>
			<div class="float-right">
				<!-- Button trigger modal -->
				<button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
					data-target="#exampleModalCenter">
					<i class="fa fa-plus"></i> Yeni
				</button>

				<!-- Modal -->
				<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
					aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="exampleModalLongTitle">Servis Konusu</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<div class="form-group">

									<label">
										<font color="red">(*)</font>Servis Konusu :
										</label>
										<input required id="title" name="title" autocomplete="off" placeholder="örn: Bakım"
											class="form-control" type="text">
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
								<button type="button" id="submitButtonByAjax" 
									class="btn btn-primary">Kaydet</button>
							</div>
							<input id="id" type="hidden" value="0">
						</div>
					</div>
				</div>
			</div>

		</div>
	</form>

	<table class="data-table table-bordered table-hover" style="text-align: center;">
		<thead>
			<tr>
				<th width="15" scope="col">S/N</th>
				<th>Tahsilat Tipi</th>
				<th>Eklenme Tarihi</th>
				<th class="datatable-nosort">İşlem</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$cq = $ac->prepare("SELECT * FROM units WHERE statu = '2' ");
			$cq->execute();
			$kx = 1;
			while ($as = $cq->fetch(PDO::FETCH_ASSOC)) {
				?>
				<tr>
					<td scope="row">
						<?php echo $kx; ?>
					</td>
					<td>
						<?php echo $as["title"]; ?>
					</td>
					<td>
						<?php echo $as["regdate"]; ?>
					</td>
					<td>

						<?php if (permtrue("customeredit")) { ?>
							<a href="#" class="edit" data-id="<?php echo $as["id"] ?>" data-tooltip="Düzenle"
								data-toggle="modal" data-target="#exampleModalCenter">
								<span class=" btn btn-sm btn-outline-info">
								<i class="fa fa-pencil"></i>
								</span>
							</a>
						<?php } ?>
						<a class="btn btn-sm btn-danger text-white" data-tooltip="Sil"
							onClick="deleteRecord('Kaydı silmek istediğinize emin misiniz?',<?php echo $as["id"]; ?>,'service-type','units')"><i
								class="fa fa-trash"></i></a>
					</td>
				</tr>
				<?php
				$kx = $kx + 1;
			} ?>
		</tbody>
		<tfoot>
			<tr>
				<th width="15" scope="col">S/N</th>
				<th>Servis Konusu</th>
				<th>Eklenme Tarihi</th>
				<th class="datatable-nosort">İşlem</th>
			</tr>
		</tfoot>
	</table>
</div>
<script src="include/js/data-table.js"></script>
<script src="../../include/js/define.js"></script>
<script>
	submitButton.on('click', function () {
		addType("service-type", "Servis Tipi");
	});
</script>