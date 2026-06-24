<?php

if (@$_GET["id"] && @$_GET["mode"] == "delete" && @$_GET["code"] == "04md177") {
    permcontrol("");
    $cdid = $_GET["id"];
    $contq = $ac->prepare("SELECT * FROM evraktakip WHERE id = ?");
    $contq->execute(array($cdid));
    if ($contq->fetch(PDO::FETCH_ASSOC)) {
        $deletq = $ac->prepare("DELETE FROM evraktakip WHERE id = ?");
        $deletq->execute(array($cdid));

        if ($deletq) {
            header("Location: index.php?p=view-indocument&id=$cdid&type=delete");
        }
    }
}
?>

<div class="content pd-20 bg-white border-radius-16 box-shadow mb-30">
    <div class="clearfix mb-20">
        <div class="pull-left">
            <h5 class="text-blue">Giden Evrak Listesi</h5>

        </div>
        
        <a href="index.php?p=new-indocument"><button type="button" class="btn btn-primary btn-sm float-right">
                <i class="fa fa-plus"></i> Yeni Ekle
            </button></a>
        <br><br>
    </div>
    <table id="tblInDocuments" class="table-hover table-responsive-sm table-bordered data-table">
        <thead>
            <tr>
                <th>Sıra</th>
                <th>Firma</th>
                <th>Evrak Türü</th>
                <th>Kategori</th>
                <th>Adet</th>
                <th>Teslim Eden</th>
                <th>Teslim Alan</th>
                <th>Teslim Tarihi</th>
                <th>Evrak Durumu</th>
                <th>Açıklama</th>
                <th>İşlem</th>

            </tr>
        </thead>
        <tbody>
            <?php
			$cq = $ac->prepare("SELECT e.*, u_teslimalan.username AS teslim_alan_username, u_teslimeden.username AS teslim_eden_username
                      FROM evraktakip e 
                      LEFT JOIN users u_teslimalan ON e.teslimalan = u_teslimalan.id 
                      LEFT JOIN users u_teslimeden ON e.teslimeden = u_teslimeden.id 
                      WHERE e.evrakturu = 'Gelen'");
			$cq->execute();
			$siraNo = 1;
			while ($as = $cq->fetch(PDO::FETCH_ASSOC)) {



				$miq = $ac->prepare("SELECT * FROM evraktakip WHERE id = ?");
				$miq->execute(array($as["id"]));
				$mms = $miq->fetch(PDO::FETCH_ASSOC);
				?>
            <tr>
                <td class="wrap text-center">
                    <?php echo $siraNo; ?>
                </td>
                <td>
                    <?php
                    $customerId = $as["firma"];
                    $customerQuery = $ac->prepare("SELECT company FROM customers WHERE id = ?");
                    $customerQuery->execute(array($customerId));
                    $customer = $customerQuery->fetch(PDO::FETCH_ASSOC);
                    echo $customer["company"];
                    ?>
                </td>
                <td class="text-nowrap" data-tooltip="<?php echo $as["evrakturu"]; ?>">
                    <?php echo shorted($as["evrakturu"],40); ?>
                </td>
                <td>
                    <?php echo $as["kategori"]; ?>
                </td>
                <td>
                    <?php echo $as["adet"]; ?>
                </td>
                <td>
                    <?php echo $as["teslim_alan_username"]; ?>
                </td>
                <td>
                    <?php echo $as["teslim_eden_username"]; ?>
                </td>
                <td>
                    <?php echo $as["teslimtarihi"]; ?>
                </td>
                <td>
                    <?php
                    $estatu = $as["estatu"];
                    if ($estatu == "Bekliyor") {
                        echo "<span class='badge badge-warning'>Bekliyor</span>";
                    } elseif ($estatu == "Çalışıyor") {
                        echo "<span class='badge badge-primary'>Çalışıyor</span>";
                    } elseif ($estatu == "Tamamlandı") {
                        echo "<span class='badge badge-success'>Tamamlandı</span>";
                    } else {
                        echo $estatu;
                    }
                    ?>
                </td>
                <td>
                    <?php echo $as["aciklama"]; ?>
                </td>
                <td class="text-center text-nowrap col-md-1 pl-3 pr-3">
                    

                    <a class="btn btn-sm btn-outline-info" data-tooltip="Düzenle"
                        href="index.php?p=indocument-edit&id=<?php echo $as["id"]; ?>">
                        <i class="fa fa-edit"></i></a>
                   

                    <a href="#" class="btn btn-sm btn-danger" data-tooltip="Sil!"
                        onClick="deleteRecord('<?php echo $customer["company"];?>  isimli firmaya ait evrağı kaldırmak istediğinize emin misiniz?',<?php echo $as["id"]; ?>,'view-indocument','evraktakip')">
                        <i class="fa fa-trash"></i></a>

            </tr>
            <?php
				$siraNo = $siraNo + 1;
			} ?>
        </tbody>
    </table>
</div>

<script src="include/js/data-table.js"></script>