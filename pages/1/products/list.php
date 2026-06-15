<?php


use App\Helper\Helper;
use App\Helper\Security;
use App\Model\DefineModel;
use App\Model\ProductModel;





//DefineModel sınıfı çağrıldı
$Define = new DefineModel();
$ProductModel = new ProductModel();

$products = $ProductModel->getAllWithUnits();

$logger = \getLogger("Ürünler");

$logger->info("Ürün/Hizmet listesi görüntülendi.",[
    'username' => $_SESSION['username']
]);

$pids = @$_GET["id"];
if ($pids && @$_GET["mode"] == "delete" && @$_GET["code"] == "04md177") {
    permcontrol("productdelete");
    $qcont = $ac->prepare("SELECT * FROM products WHERE ID = ?");

    $qcont->execute(array($pids));
    $qkx = $qcont->fetch(PDO::FETCH_ASSOC);
    if ($qkx) {
        $pdq = $ac->prepare("DELETE FROM products WHERE id = ?");
        $pdq->execute(array($pids));

        header("Location: index.php?p=products&type=delete&code=0882md25&pid=$pids");
    }
}

?>


<div class="content pd-20 bg-white border-radius-16 box-shadow mb-30">
    <div class="clearfix mb-20">
        <div class="pull-left">
            <h5 class="text-blue">Ürün/Hizmet Listesi Yeni</h5>

        </div>
        <?php if (permtrue("productadd")) { ?>
            <a href="index.php?p=products/manage"><button type="button" class="btn btn-primary btn-sm float-right">
                    <i class="fa fa-plus"></i> Yeni Ekle
                </button></a>
        <?php } ?><br><br>
    </div>
    <table id="tblProducts" class="table-hover table-responsive-sm table-bordered data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#Sıra</th>
                <th style="width: 15%;">Stok Kodu</th>
                <th>Ürün/Hizmet Adı</th>
                <th>Birimi</th>
                <th>Alış Fiyatı</th>
                <th>Satış Fiyatı</th>
                <th>Açıklama</th>
                <th>İşlem</th>

            </tr>
        </thead>
        <tbody>
            <?php
            
            $siraNo = 1;
            foreach ($products as $product) {
                $enc_id = Security::encrypt($product->ID);
            
                ?>
                <tr>
                    <td class="wrap text-center">
                        <?php echo $siraNo; ?>
                    </td>
                    <td>
                        <?php echo $product->StokKodu; ?>
                    </td>
                    <td class="text-nowrap" data-tooltip="<?php echo $product->Adi; ?>">
                        <?php echo shorted($product->Adi, 40); ?>
                    </td>
                    <td class="text-center"> 
                        <?php echo $product->birim; ?>
                    </td>
                    <td>
                        <?php echo $product->AlisFiyati . " " . $product->AlisParaBirimi; ?>
                    </td>
                    <td>
                        <?php echo $product->SatisFiyati . " " . $product->SatisParaBirimi; ?>
                    </td>
                    <td>
                        <?php echo $product->Aciklama; ?>
                    </td>
                    <td class="text-center text-nowrap col-md-1 pl-3 pr-3">
                        <?php
                        if (permtrue("productedit")) { ?>

                            <a class="btn btn-sm btn-outline-info" data-tooltip="Düzenle"
                                href="index.php?p=products/manage&id=<?php echo $enc_id; ?>">
                                <i class="fa fa-edit"></i></a>
                        <?php } ?>

                        <!-- <?php if (permtrue("productdel")) { ?><a onClick="return confirm('<?php echo $product->Adi; ?> isimli ürün/hizmeti sistemden kaldırmak istediğinize emin misiniz?')" href="index.php?p=products&mode=delete&code=04md177&reg=true&md=active&pid=<?php echo $product->ID; ?>"><span class="badge badge-danger">Sil</span></a><?php } ?></td> -->
                        <a href="#" class="btn btn-sm btn-danger product-delete" data-tooltip="Sil!"  data-id="<?php echo $enc_id; ?>" data-name="<?php echo $product->Adi; ?>"
                            >
                            <i class="fa fa-trash"></i></a>


                        <div class="dropdown d-inline">
                            <button class="btn btn-secondary btn-sm" type="button" id="dropdownMenu2"
                                data-toggle="dropdown">
                                <i class="fa fa-ellipsis-v ml-1 mr-1"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-detail"
                                aria-labelledby="dropdownMenu2">

                                <a href="index.php?p=purchase-demand-detail&id=<?php echo "" ?>" target="_blank"
                                    class="dropdown-item" type="button">
                                    <i class="fa fa-list-ol" aria-hidden="true"></i>
                                    Stok Hareketleri</a>



                            </div>

                        </div>

                    </td>

                </tr>
                <?php
                $siraNo = $siraNo + 1;
            } ?>
        </tbody>
        
    </table>
</div>

<script src="include/js/data-table.js"></script>