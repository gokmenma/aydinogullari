<?php
permcontrol("authdefine");
$xid = @$_GET["pid"];
if ($xid && @$_GET["mode"] == "delete") {
    $stcontrol = $ac->prepare("SELECT * FROM userroles WHERE id = ?");
    $stcontrol->execute(array($xid));
    $sts = $stcontrol->fetch(PDO::FETCH_ASSOC);
    if (!$sts) {
        header("Location: index.php?p=permission-settings&err=true");
        exit;
    }

    // $ucek = $ac->prepare("SELECT * FROM users WHERE permission = ? ");
    // $ucek->execute(array($xid));
    // while ($usay = $ucek->fetch(PDO::FETCH_ASSOC)) {

    //   $upuser = $ac->prepare("UPDATE users SET permission = ?, statu = ? WHERE id = ?");
    //   $upuser->execute(array(0, 0, $usay["id"]));
    // }
    // $stdel = $ac->prepare("DELETE FROM perms WHERE id = ?");
    // $stdel->execute(array($xid));

    // header("Location: index.php?p=permission-settings&type=delete&code=0882md25&pid=$xid");
}


?>
<?php if (@$_GET["st"] == "newsuccess") {
    showAlert("success", "Yetki başarı ile kayıt yapıldı");
} ?>

<div class="content pd-20 bg-white border-radius-8 box-shadow mb-30">
    <div class="clearfix mb-20">
        <div class="pull-left">
            <h5 class="text-blue">Pozisyon Adlandırmaları & İzin Yönetimi</h5>

        </div>
        <a href="index.php?p=permission-new&cc=0014" class="float-right btn btn-primary btn-sm"><i class="fa fa-plus">
            </i>
            Yeni
            Oluştur</a>
    </div>

    <table class="data-table table-bordered table-hover ">
        <thead>
            <tr>
                <th class="text-center">Sıra</th>
                <th>Pozisyon Adı</th>
                <th>Açıklama</th>
                <th>İzinler</th>
                <th class="app-item-action">İşlem</th>

            </tr>
        </thead>
        <tbody>

            <tr>
                <?php

                //Burada kullanıcı rolleri getirilir
                $pxc = $ac->prepare("SELECT * FROM userroles");
                $pxc->execute();
                $kx = 1;
                while ($px = $pxc->fetch(PDO::FETCH_ASSOC)) {

                    //burada kullanıcılara atanan yetkiler id olarak getirilir.
                    $authsquery = $ac->prepare("SELECT * FROM userauths WHERE roleID =  ? ");
                    $authsquery->execute(array($px["id"]));

                    $userauths = "";
                    while ($ua = $authsquery->fetch(PDO::FETCH_ASSOC)) {

                        //Burada gelen id'ye göre yetkilerin isimleri getirilir
                        $query = $ac->prepare("SELECT * FROM authority WHERE id =  ? ");
                        $query->execute(array($ua["authID"]));

                        while ($auth = $query->fetch(PDO::FETCH_ASSOC)) {
                            $userauths .= $auth["authTitle"] . ", ";
                        }

                    }

                    ?>

                    <td class="app-item-number text-center">
                        <?php echo $kx; ?>
                    </td>
                    <td>
                        <?php echo $px["roleName"]; ?>
                    </td>
                    <td>
                        <?php echo $px["roleDescription"]; ?>
                    </td>
                    <td>
                        <?php echo strlen($userauths) > 100 ? substr($userauths, 0, 100) . "..." : $userauths; ?>

                    </td>
                    <td class="app-item-action">
                        <?php
                        if (permtrue("authEdit")) {
                            ?>
                            <a href="index.php?p=permission-edit&reg=true&md=update&id=<?php echo $px["id"]; ?>"
                                class="btn btn-sm btn-outline-info" data-tooltip="Düzenle"><i class="fa fa-pencil"></i></a>
                            <?php }
                        ?>
                        <?php if ($px["id"] != 1 && permtrue("authDel"))  { ?>
                            <button class="btn btn-sm btn-danger" data-tooltip="Sil!"
                                onClick="deleteRecord('Bu yetkiyi silmenizle birlikte, bu yetkiye sahip tüm kullanıcıların hesapları dondurulacaktır.Üye düzenleme sayfasından tekrar aktifleştirebilirsiniz.',<?php echo $px["id"]; ?>,'permission-settings')"><i
                                    class="fa fa-trash"></i></button>
                        </td>
                    <?php }
                        ?>
                </tr>
                <?php $kx++;

                } ?>


        </tbody>
        <tfoot>
            <tr>
                <th class="text-center">Sıra</th>
                <th>Pozisyon Adı</th>
                <th>Açıklama</th>
                <th>İzinler</th>
                <th class="app-item-action">İşlem</th>

            </tr>
        </tfoot>
    </table>
</div>
<script src="include/js/data-table.js"></script>