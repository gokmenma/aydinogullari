<div class="pd-20 bg-white border-radius-16 box-shadow mb-20">

	<form method="POST" id="myForm">
		<div class="clearfix justify-content-between mb-20">
			<div class="pull-left">
				<h5 class="text-blue">Servis Durum Listesi</h5>
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
								<h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<div class="form-group">

									<label">
										<font color="red">(*)</font>Servis Durum Bilgisi :
										</label>
										<input required id="title" name="title" autocomplete="off"
											placeholder="örn: Çalışıyor" class="form-control" type="text">
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
								<button type="button" id="submitButtonByAjax" class="btn btn-primary">Kaydet</button>
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
				<th style="width:30px">ID</th>
				<th>Servis Durumu</th>
				<th>Ekleyen</th>
				<th>Eklenme Tarihi</th>
				<th class="datatable-nosort">İşlem</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$cq = $ac->prepare("SELECT * FROM units WHERE statu = '4' ");
			$cq->execute();
			$kx = 1;
			while ($as = $cq->fetch(PDO::FETCH_ASSOC)) {
				?>
				<tr>
					<td scope="row">
						<?php echo $kx; ?>
					</td>
					<td>
						<?php echo $as["id"]; ?>
					</td>
					<td>
						<?php echo $as["title"]; ?>
					</td>
					<td>
						<?php echo getUsername($as["creator"]) ?>
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

						<?php if (servisDurumuKullaniliyormu($as["id"]) == false) {
							?>
							<a class="btn btn-sm btn-danger text-white" data-tooltip="Sil"
								onClick="deleteRecord('Kaydı silmek istediğinize emin misiniz?',<?php echo $as["id"]; ?>,'service-status','units')"><i
									class="fa fa-trash"></i></a>

						<?php } else {
							?>

							<a class="btn btn-sm btn-danger text-white" id="delete" onclick="deleteServiceType()" data-tooltip="Sil"><i
									class="fa fa-trash"></i></a>
							<script>
								function deleteServiceType(){
										swal.fire({
											title: "Uyarı!",
											icon: "error",
											text:"Bu tanımlama kullanıldığından silinemez"

										})
													}
							</script>
							<?php
						}
						?>

					</td>
				</tr>
				<?php
				$kx = $kx + 1;
			} ?>
		</tbody>
		<tfoot>
			<tr>
				<th width="15" scope="col">S/N</th>
				<th>ID</th>
				<th>Servis Durumu</th>
				<th>Ekleyen</th>
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
		addType("service-status", "Servis Durumu");
	});
</script>