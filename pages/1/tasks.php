<?php

$pids = @$_GET["id"];
if ($pids && @$_GET["mode"] == "delete" && @$_GET["code"] == "04md177") {
	permcontrol("tododelete");
		$pdq = $ac->prepare("DELETE FROM todolist WHERE id = ?");
		$pdq->execute(array($pids));

		//header("Location: index.php?p=tasks&tid=$pids");

}
if ($pids && @$_GET["md"] == "update" && @$_GET["tt"]) {
	 permcontrol("todoedit");
	$tts = $_GET["tt"];

	if ($tts == 1) {
		$gg = 1;
	} elseif ($tts == 2) {
		$gg = 2;
	} else {
		$gg = 0;
	}

	$gunc = $ac->prepare("UPDATE todolist SET okey = ? WHERE id = ?");
	$gunc->execute(array($gg, $pids));
	header("Location: index.php?p=tasks&st=newsuccess");
}

?>
<?php

?>
<div class="pd-20 bg-white border-radius-16 box-shadow mb-30">
	<div class="clearfix mb-30">
		<div class="pull-left">
			<h5 class="text-blue">Yapılacaklar Listesi</h5>

		</div>
		<div class="float-right">
			<?php if (permtrue("todoadd")) { ?>
				<a href="index.php?p=task-new" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Yeni Oluştur</a>
			<?php } ?>
		</div>
	</div>
	<table class="data-table table-hover table-bordered table-responsive-sm">
		<thead>
			<tr>
				<th class="app-item-number">#Sıra</th>
				<th>Başlık</th>
				<th>Oluşturan</th>
				<th>Durum</th>
				<th>Son Tarih</th>
				<th style="min-width:200px">İşlem</th>

			</tr>
		</thead>
		<tbody>

			<?php
			$cq = $ac->prepare("SELECT * FROM todolist ORDER by id DESC ");
			$cq->execute();
			$kx = 1;
			while ($as = $cq->fetch(PDO::FETCH_ASSOC)) {

				$miq = $ac->prepare("SELECT * FROM users WHERE id = ?");
				$miq->execute(array($as["creativer"]));
				$mms = $miq->fetch(PDO::FETCH_ASSOC);

				if ($as["okey"] == 0) {
					$durumx = '<font style="font-weight:bold; color:red">Yapılmadı</font>';
					$state="Yapılmadı";
				} elseif ($as["okey"] == 1) {
					$durumx = '<font style="font-weight:bold; color:green">Yapıldı</font>';
					$state="Yapıldı";
				} elseif ($as["okey"] == 2) {
					$durumx = '<font style="font-weight:bold; color:blue">Ertelendi</font>';
					$state="Ertelendi";
				}
				?>
				<tr >

					<td class="text-center">
						<?php echo $kx; ?>
					</td>
					<td data-tooltip="<?php echo $as["description"] ?>">
						<?php echo $as["okey"] == 2 ? "<font style='font-weight:bold'>[Ertelendi]</font> " : ""; ?>
						<?php echo $as["okey"] == 1 ? "<s>" : ""; ?>
						<?php echo $as["title"]; ?>
						<?php echo $as["okey"] == 1 ? "</s>" : ""; ?>
					</td>
					<td data-tooltip="<?php echo $as["description"] ?>">
						<?php echo $as["okey"] == 1 ? "<s>" : ""; ?>
						<?php echo $mms["username"]; ?>
						<?php echo $as["okey"] == 1 ? "</s>" : ""; ?>
					</td>
					<td class="text-center app-item-amount" data-tooltip="<?php echo $as["description"]; ?>">
						<?php echo $as["okey"] == 1 ? "<s>" : ""; ?>
						<?php echo $durumx; ?>
						<?php echo $as["okey"] == 1 ? "</s>" : ""; ?>
					</td>
					<td class="text-center app-item-amount" data-tooltip="<?php echo $as["description"]; ?>">
						<?php echo $as["okey"] == 1 ? "<s>" : ""; ?>
						<?php echo $as["last_date"]; ?>
						<?php echo $as["okey"] == 1 ? "</s>" : ""; ?>
					</td>
					<td>&nbsp;&nbsp;
						<?php if (permtrue("todoedit")) { ?><a
								href="index.php?p=task-edit&id=<?php echo $as["id"]; ?>&tt=2"><span
									class="badge badge-info">Düzenle</span></a>
						<?php }
						if (permtrue("tododelete") && $as["okey"] != 1) { ?>
							<a onClick="deleteRecord('<?php echo $as["title"]; ?> başlıklı maddeyi silmek istediğinize emin misiniz?','<?php echo $as["id"]; ?>','tasks','todolist')"><span
									class="badge badge-danger">Sil</span></a>
							<?php } ?>
							<?php
							if ($as["okey"] == 0 && permtrue("todoedit")) {
								?>
								<a href="index.php?p=tasks&reg=true&md=update&id=<?php echo $as["id"]; ?>&tt=1"><span
										class="badge badge-success">Yapıldı</span></a>
								<a href="index.php?p=tasks&reg=true&md=update&id=<?php echo $as["id"]; ?>&tt=2"><span
										class="badge badge-primary">Ertele</span></a>
								<?php
							} elseif ($as["okey"] == 1 && permtrue("todoedit")) {
								?>
								<a href="index.php?p=tasks&reg=true&md=update&id=<?php echo $as["id"]; ?>&tt=3"><span
										class="badge badge-danger">Yapılmadı</span></a>
								<?php
							} elseif ($as["okey"] == 2 && permtrue("todoedit")) {
								?>
								<a href="index.php?p=tasks&reg=true&md=updatetid=<?php echo $as["id"]; ?>&tt=1"><span
										class="badge badge-success">Yapıldı</span></a>
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
				<th class="">#Sıra</th>
				<th>Başlık</th>
				<th>Oluşturan</th>
				<th>Durum</th>
				<th>Son Tarih</th>
				<th>İşlem</th>

			</tr>
		</tfoot>
	</table>
</div>
<script src="include/js/data-table.js"></script>