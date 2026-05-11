<?php
permcontrol("fileview");
if (@$_GET["id"] && @$_GET["mode"] == "delete" && @$_GET["code"] == "04md177") {
	permcontrol("filedelete");
	$cdid = $_GET["id"];
	$contq = $ac->prepare("SELECT * FROM upfiles WHERE id = ?");
	$contq->execute(array($cdid));
	$contak = $contq->fetch(PDO::FETCH_ASSOC);
	if ($contak) {

		$fnm = $contak["filename"];
		unlink("files/" . $fnm);
		$deletq = $ac->prepare("DELETE FROM upfiles WHERE id = ?");
		$deletq->execute(array($cdid));

		if ($deletq) {
			header("Location: index.php?p=all-files&cid=$cdid&type=delete");
		}
	}
}

?>
<div class="pd-20 bg-white border-radius-16 box-shadow mb-30">
    <?php
	if (@$_GET["st"] == "newsuccess") {
		showAlert('success', 'Dosya yükleme işlemi başarılı!');
	}

	if (@$_GET["st"] == "imported") {

		?>

    <?php
	}
	if (@$_GET["type"] == "delete" and @$_GET["cid"]) {
		?>
    <div class="alert alert-success" role="alert">
        Dosya silme işlemi başarılı!
    </div>
    <?php
	}
	?>
    <div class="clearfix mb-20">
        <div class="pull-left">
            <h5 class="text-blue">Dosya Listesi</h5>
            <p class="font-14"> </p>
        </div>


        <?php if (permtrue("customeradd")) { ?>
        <a href="index.php?p=new-file"><button type="button" id="submitButton"
                class="btn btn-primary btn-sm float-right">
                <i class="fa fa-plus"></i> Yeni Dosya</button></a><br>
        <?php } ?>
    </div>
    <table class="data-table select-row table-bordered table-hover">
        <thead>
            <tr>
                <th>Sıra</th>
                <th>Dosya Adı</th>
                <th>Yükleyen</th>
                <th>Kategori</th>
                <th>Kayıt Tarihi</th>
                <th>Boyut</th>
                <th class="datatable-nosort">İşlem</th>

            </tr>
        </thead>
        <tbody>

            <?php
			$cq = $ac->prepare("SELECT * FROM upfiles ORDER by id DESC");
			$cq->execute();
			while ($as = $cq->fetch(PDO::FETCH_ASSOC)) {
				$getir = $ac->prepare("SELECT * FROM users WHERE id = ?");
				$getir->execute(array($as["creativer"]));
				$get = $getir->fetch(PDO::FETCH_ASSOC);

				if ($as["size"] > 1024) {
					$boyutu = number_format($as["size"] / 1024, 2) . " KB";
				} else {
					$boyutu = number_format($as["size"], 2) . " Byte";
				}

				$kget = $ac->prepare("SELECT * FROM upfile_categories WHERE id = ?");
				$kget->execute(array($as["cid"]));
				$katx = $kget->fetch(PDO::FETCH_ASSOC);
				?>
            <tr>
                <td scope="row">
                    <?php echo $as["id"]; ?>
                </td>
                <td>
                    <?php echo $as["filename"]; ?>
                </td>
                <td>
                    <?php echo $get["name"]; ?>
                </td>
                <td>
                    <?php echo $katx["title"]; ?>
                </td>
                <td>
                    <?php echo $as["regdate"]; ?>
                </td>
                <td>
                    <?php echo $boyutu; ?>
                </td>
                <?php $ptr = true; ?>
                <td>
                    <?php echo $ptr ? "<a class='btn btn-sm btn-secondary' target='_blank' href='files/" . $as["filename"] . "'>
						<i class='fa fa-download'></i></a>" : ""; ?>


                    <?php if (permtrue("filedelete")) { ?>
                    <button type="button" class="btn btn-sm btn-danger" data-tooltip="Sil"
                        onClick="deleteRecord('Dosyayı sistemden kalıcı olarak silmek istediğinize emin misiniz?','<?php echo $as["id"]; ?>','all-files')"><i
                            class="fa fa-trash"></i></button>
                    <?php } ?>

                </td>

            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Sıra</th>
                <th>Dosya Adı</th>
                <th>Yükleyen</th>
                <th>Kategori</th>
                <th>Kayıt Tarihi</th>
                <th>Boyut</th>
                <th class="datatable-nosort">İşlem</th>

            </tr>
        </tfoot>
    </table>
</div>
<script src="include/js/data-table.js"></script>