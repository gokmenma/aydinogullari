<?php

if (@$_GET["id"] && @$_GET["mode"] == "delete" && @$_GET["code"] == "04md177") {
	permcontrol("userdelete");

	if (@$_GET["id"] == sesset("id") || $_GET["id"] == 1) {
		header("Location: index.php?p=users&st=cannotdeleted");
		?>
<script>
showMessage("Kendi üyeliğinizi silemezsiniz", "alert");
</script>
<?php
		exit;
	}
	$cdid = $_GET["id"];
	$contq = $ac->prepare("SELECT * FROM users WHERE id = ?");
	$contq->execute(array($cdid));


	if ($contq->fetch(PDO::FETCH_ASSOC)) {
		$deletq = $ac->prepare("DELETE FROM users WHERE id = ?");
		$deletq->execute(array($cdid));
		if ($deletq) {
			header("Location: index.php?p=users&uid=$cdid&type=delete");
		}
	}
}
if (@$_GET["id"] && @$_GET["mode"] == "updatest") {
	permcontrol("useredit");
	if (@$_GET["id"] == sesset("id") || $_GET["id"] == 1) {
		header("Location: index.php?p=users&st=cannotupdate");
		exit;
	}
	$cdid = $_GET["id"];
	$gunc = @$_GET["stu"];
	if ($gunc != 1 && $gunc != 0) {
		header("Location:index.php");
		exit;
	}

	$contq = $ac->prepare("UPDATE users SET statu = ? WHERE id = ?");
	$contq->execute(array($gunc, $cdid));

	header("Location:index.php?p=users");
}

?>
<div class="content pd-20 bg-white border-radius-16 box-shadow mb-30">
    <?php

	if (@$_GET["st"] == "cannotdeleted") {



		?>
    <div class="alert alert-error" role="alert">
        Kendi üyeliğinizi silemezsiniz.
    </div>
    <?php
	}
	if (@$_GET["type"] == "delete" and @$_GET["cid"]) {
		?>
    <div class="alert alert-success" role="alert">
        <?php echo "#" . $_GET["cid"]; ?> numaralı müşteri bilgileri başarıyla silindi.
    </div>
    <?php
	}
	?>
    <div class="clearfix mb-20">
        <div class="pull-left">
            <h5 class="text-blue">Ekip Listesi</h5>
            <p class="font-14"> </p>
        </div>
        <?php if (permtrue("useradd")) { ?>
        <a href="index.php?p=user-new"><button type="button" class="btn btn-primary btn-sm float-right"><i
                    class="fa fa-plus"></i> Yeni Üye </button></a>
        <?php } ?><br><br>
    </div>
    <table class="data-table table-hover table-bordered table-responsive" style="text-align: center;">
        <thead>
            <tr>
                <th class="text-nowrap">Sıra No</th>
                <th>Pozisyon</th>
                <th>Adı Soyadı</th>
                <th>E-Posta Adresi</th>
                <th>GSM</th>
                <th>Durum</th>
                <th>İşlem</th>

            </tr>
        </thead>
        <tbody>

            <?php
			$cq = $ac->prepare("SELECT * FROM users ORDER by id DESC");
			$cq->execute([]);
			$sirano = 1;
			while ($as = $cq->fetch(PDO::FETCH_ASSOC)) {

				$perqx = $ac->prepare("SELECT * FROM userroles WHERE id = ?");
				$perqx->execute(array($as["permission"]));
				$ppa = $perqx->fetch(PDO::FETCH_ASSOC);


				?>
            <tr>
                <td class="app-item-number">
                    <?php echo $sirano; ?>
                </td>
                <td>
                    <?php echo $ppa["roleName"]; ?>
                </td>
                <td>
                    <?php echo $as["username"]; ?>
                </td>
                <td>
                    <?php echo $as["email"]; ?>
                </td>
                <td>
                    <?php echo $as["gsm"]; ?>
                </td>
                <td>
                    <?php
						if ($as["statu"] == 0) {
							echo '<span class="btn btn-secondary btn-sm"><i class="fa fa-close"></i> Pasif</span>';
						} else {
							echo '<span class="btn btn-success btn-sm"><i class="fa fa-check"></i> Aktif</span>';
						}
						?>

                </td>
                <td class="text-nowrap">
                    <?php if (permtrue("useredit") && $as["id"] != 1) { ?>
                    <a href="index.php?p=user-edit&id=<?php echo $as["id"]; ?>" class="btn btn-sm btn-outline-info"
                        data-tooltip="Düzenle"><i class="fa fa-pencil"></i></a>
                    <?php
							if ($as["statu"] == 1 && $as["id"] != 1) {
								?>
                    <a class="btn btn-sm btn-secondary text-white" data-tooltip="Pasifleştir"
                        href="index.php?p=users&mode=updatest&code=3222891&reg=true&md=active&id=<?php echo $as["id"]; ?>&stu=0"><i
                            class="fa fa-user-times"></i></a>
                    <?php
							} elseif ($as["statu"] == 0 && $as["id"] != 1) {
								?>
                    <a class="btn btn-sm btn-success" data-tooltip="Aktifleştir"
                        href="index.php?p=users&mode=updatest&code=3222891&reg=true&md=active&id=<?php echo $as["id"]; ?>&stu=1"><i
                            class="fa fa-user-circle"></i></a>
                    <?php
							}
						}
						if (permtrue("userdelete") and $as["id"] != sesset("id") and $as["id"] != 1) { ?>
                    <a class="btn btn-sm btn-danger text-white" data-tooltip="Sil"
                        onClick="deleteRecord('Devam ettiğiniz takdirde,kullanıcıya ait tüm bilgiler ve kullanıcı adına düzenlenmiş olan teklif & projeler tamamen silinecektir. Devam etmek istiyor musunuz?','<?php echo $as['id']; ?>','users')">
                        <i class="fa fa-trash"></i></a>
                    <?php } ?>
                    <!-- <?php if ($as["id"] != 1) { ?>
							<a class="btn btn-sm btn-warning text-black" data-tooltip="Pozisyon"
								href="index.php?p=permission-edit&reg=true&md=update&id=<?php echo $ppa["id"]; ?>"><i
									class="fa fa-cog"></i></a>
						<?php } ?> -->
                </td>

            </tr>

            <?php
				$sirano += 1;
			} ?>
        </tbody>
        <tfoot>
            <tr>
                <td>Sıra No</td>
                <td>Pozisyon</td>
                <th>Adı Soyadı</th>
                <td>E-Posta Adresi</td>
                <td>GSM</td>
                <td>Durum</td>
                <td>İşlem</td>

            </tr>
        </tfoot>
    </table>
</div>
<script src="include/js/data-table.js"></script>