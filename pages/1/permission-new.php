<?php
permcontrol("authdefine");
if ($_POST) {

    $authName = $_POST["authName"];
    $authDescription = $_POST["authDescription"];
    if ($authName != null) {

        //CHECKBOX'LARI SAY
        $checkcount = isset($_POST["checkedDataIds"]) ? count($_POST["checkedDataIds"]) : 0;

        //EN AZ BİR ADET YETKİ SEÇİLİ İSE İŞLEME DEVAM ET
        if ($checkcount > 0) {
            $query = $ac->prepare("INSERT INTO userroles (roleName, roleDescription, isActive) VALUES (?, ?, ?)");
            $query->execute(array($authName, $authDescription, 1));
            $lastid = $ac->lastInsertId();


            if ($query) {
                //seçilil olan checkbox'larda döngüye girerek veritabanına kaydeder
                foreach ($_POST["checkedDataIds"] as $chk) {
                    $sql = $ac->prepare("INSERT INTO userauths (roleID,authID) VALUES(?,?)");
                    $sql->execute(array($lastid, $chk));
                }

                if ($sql) {
                    header("Location:index.php?p=permission-new&st=success");
                    exit();
                }
            }
        } else {
            //en az bir adet yetki seçili değilse uyarı ver
            header("Location:index.php?p=permission-new&st=authempties");
            exit();
        }

    } else {


        header("Location:index.php?p=permission-new&st=empties");
        exit();
    }
}

if (@$_GET["st"] == "success") {
    showAlert("success", "Başarı ile kayıt yapıldı");
}


if (@$_GET["st"] == "empties") {
    showAlert("alert", "Lütfen Yetki adını giriniz");
    ?>
    <script>
        window.history.pushState({}, '', 'index.php?p=permission-new')
    </script>
<?php }

if (@$_GET["st"] == "authempties") {
    showAlert("alert", "Lütfen en az bir yetki seçiniz");
}

?>
<form action="" id="myForm" method="post">
    <div class="content pd-20 bg-white border-radius-16 box-shadow mb-20">
        <div class="clearfix mb-20">
            <div class="pull-left">
                <h4 class="text-blue">Yeni Yetkilendirme</h4>
            </div>
            <div class="float-right">
                <button type="button" id="submitButton" data-tooltip="Kaydet" data-tooltip-location="bottom"
                    class="btn btn-sm btn-primary"><i class="fa fa-save"></i>
                    Kaydet</button>
                <a type="button" href="index.php?p=permission-settings" data-tooltip="Listeye Dön"
                    data-tooltip-location="bottom" class="btn btn-sm btn-secondary"><i class="fa fa-list"></i>
                    Listeye Dön</a>
            </div>

        </div>
        <div class="col-md-12">
            <div class="form-group row">
                <label class="col-md-2" for="">Yetki Adı : </label>
                <input type="text" name="authName" id="authName" class="form-control col-md-10">
            </div>
            <div class="form-group row">
                <label class="col-md-2" for="">Yetki Açıklama : </label>
                <input type="text" name="authDescription" id="authDescription" class="form-control col-md-10">
            </div>

        </div>

    </div>

    <div class="content pd-20 bg-white border-radius-16 box-shadow mb-20">

        <div class="row">
            <?php

            $sql = $ac->prepare("SELECT * FROM authority WHERE isActive = ? ORDER BY authGroup ASC");
            $sql->execute(array(1));

            $group = 0;
            $number = 1;

            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                $check = "customCheck" . $row['authGroup'] . "-" . $row["id"];

                if ($group != $row["authGroup"]) {
                    echo $group == 0 ? "<div class='col-md-4 mt-4'>" : "</div><div class='col-md-4 mt-4'>";
                }
                ?>
                <div class="custom-control custom-checkbox mb-5">

                    <input name="auth[]" type="checkbox" data-id="<?php echo $row["id"] ?>" class="custom-control-input"
                        id="<?php echo $check ?>">
                    <label class="custom-control-label" for="<?php echo $check ?>">
                        <?php echo $row["authTitle"] ?>
                    </label>
                </div>
                <?php
                $group = $row["authGroup"];
                $number += 1;
            } ?>

        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
        $('#submitButton').on('click', function () {
            $('input[type="checkbox"]:checked').each(function () {
                // Checkbox'un data-id değeri
                var dataId = $(this).data('id');
                // Checkbox'un data-id değerini form verilerine ekle
                $('<input>').attr({
                    type: 'hidden',
                    name: 'checkedDataIds[]', // Gönderilecek alanın adı
                    value: dataId // Gönderilecek değer
                }).appendTo('form');
            });
            // Formu submit et
            $('#myForm').submit();
        });
    });
</script>