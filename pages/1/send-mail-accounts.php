<?php
include_once ('../../configs/config.php');
include_once ('../../configs/functions.php');

if (!$_POST) {
    permcontrol("mail-accounts-manage");
    if (sesset("id") != 12 && sesset("id") != 1){
        header("Location:index.php?p=home");
        exit();
    }
}

if ($_POST) {

    try {
        $mail_address = $_POST["mail_address"];

        $id = $_POST["id"];
        $mail_user = $_POST["account_type"] == 2 ? $_POST["mail_user"] : 1;
        $password = encrypt($password);
        $description = $_POST["description"];
        $account_type = $_POST["account_type"];
        $create_time = date("Y-m-d H:i:s");
        $creator = sesset("id");

        if (checkemail($mail_address)) {
            if ($_POST["action"] == "new") {
                $insq = $ac->prepare("INSERT INTO mail_accounts SET mail_address = ?, 
																description = ?,
																mail_user = ?,
																create_time = ?,
																account_type = ?,
																creator = ?");
                $insq->execute(array($mail_address, $description, $mail_user, $create_time, $account_type, $creator));
                $message = "Mail Adresi Başarı ile Eklendi";

            } else if ($_POST["action"] == "update") {
                $insq = $ac->prepare("UPDATE mail_accounts SET mail_address = ?, mail_user = ?, description = ? WHERE id= ?");
                $insq->execute(array($mail_address, $mail_user, $description, $id));

                $message = "Mail Adresi Başarı ile Güncellendi";
            }
        }
        //Ekleme veya güncelleme başarılı ise 200 döndürür
        if ($insq) {
            $status = 200;
        } else {
            $message = "Ekleme başarısız!";
            $status = 400;
        }
        $res = array(
            "message" => $message,
            "status" => $status
        );
        echo json_encode($res);
        return false;

    } catch (PDOException $e) {
        $res = array(
            "message" => $e->getMessage(),
            "status" => 400
        );
        echo json_encode($res);
        return false;
    }
}


?>
<div class="pd-20 bg-white border-radius-16 box-shadow mb-20">

    <form id="myForm" method="post">

        <div class="clearfix justify-content-between mb-20">
            <div class="pull-left">
                <h5 class="text-blue">Mail Hesapları</h5>
            </div>
            <div class="float-right">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                    data-target="#exampleModalCenter">
                    <i class="fa fa-plus"></i> Yeni
                </button>

                <!-- Modal -->
                <div class="modal fade pd-5" id="exampleModalCenter" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Mail Hesapları Ekle/Düzenle</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group row">
                                    <style>
                                    .lbl {
                                        cursor: pointer !important;
                                    }
                                    </style>
                                    <div class="col-md-6">
                                            <div class="custom-control custom-radio custom-control-inline"
                                                data-tooltip="Genel hesap ile tüm kullanıcılar tarafından mail gönderilebilir">
                                                <input type="radio" id="general_account" name="account_type" value="1"
                                                    class="custom-control-input">
                                                <label class="custom-control-label font-weight-bold" for="general_account">
                                                    Genel Hesap
                                                </label>
                                            </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="custom-control custom-radio custom-control-inline"
                                            data-tooltip="Kullanıcı hesabı ile sadece giriş yapan kullanıcı mail gönderebilir">
                                            <input type="radio" id="user_account" name="account_type" value="2" checked
                                                class="custom-control-input">
                                            <label class="custom-control-label font-weight-bold" for="user_account">
                                                Kullanıcı Hesabı
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <div class="form-group">

                                    <label">
                                        <font color="red">(*)</font>Mail Adresi :
                                        </label>
                                        <input required id="mail_address" name="mail_address" autocomplete="off"
                                            placeholder="örn:info@aydinogullariysc.com" class="form-control" type="text"
                                            value="ornek@aydinogullariysc.com">
                                </div>

                                <div id="mail_user_div" class="form-group">

                                    <label>Mail Kullanıcısı :</label>
                                    <!-- <input required id="mail_password" name="mail_user" autocomplete="off"
                                        placeholder="Mail sahibi kullanıcıyı giriniz" class="form-control" type="text"> -->
                                    <?php users('mail_user', '') ?>
                                </div>


                                <div class="form-group">

                                    <label>Açıklama :</label>
                                    <input required id="description" name="description" autocomplete="off"
                                        placeholder="Açıklama giriniz" class="form-control" type="text">
                                </div>
                            </div>
                            <div class="modal-footer mb-2">
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
    <style>
    </style>
    <table style="width: 100%;" id="mailTable" class="data-table table-bordered table-hover table-responsive" style="text-align: center;">
 
        <thead>
            <tr>
                <th width="15" scope="col">S/N</th>
                <th>Mail Adresi</th>
                <th>Hesap Türü</th>
                <th>Açıklama</th>
                <th>Eklenme Tarihi</th>
                <th >Kullanıcı</th>
                <th>Ekleyen</th>
                <th class="datatable-nosort">İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = $ac->prepare("SELECT * FROM mail_accounts");
            $query->execute();
            $sira = 1;
            while ($account = $query->fetch(PDO::FETCH_ASSOC)) {
                ?>
            <tr>
                <td scope="row" class="text-center">
                    <?php echo $sira; ?>
                </td>

                <td>
                    <?php echo $account["mail_address"]; ?>
                </td>
                <td>
                    <?php echo $account["account_type"] == 1 ? 'Genel Mail' : 'Kullanıcı Maili'; ?>
                </td>
                <td>
                    <?php echo $account["description"] ?>
                </td>
                <td>
                    <?php echo $account["create_time"]; ?>
                </td>
                <td >
                    <?php echo getUserInfo($account["mail_user"]) ?>
                   
                </td>
                <td>
                    <?php echo getUsername($account["creator"]); ?>
                </td>
                <td>

                    <?php if (permtrue("customeredit")) { ?>
                    <a href="#" class="edit-mail" data-id="<?php echo $account["id"] ?>" data-tooltip="Düzenle"
                        data-toggle="modal" data-target="#exampleModalCenter">
                        <span class=" btn btn-sm btn-outline-info">
                            <i class="fa fa-pencil"></i>
                        </span>
                    </a>
                    <?php } ?>

                    <a class="btn btn-sm btn-danger text-white" data-tooltip="<?php echo $account["id"]; ?>"
                        onClick="deleteRecord('Kaydı silmek istediğinize emin misiniz?','<?php echo $account["id"]; ?>','send-mail-accounts','mail_accounts')"><i
                            class="fa fa-trash"></i></a>

                </td>
            </tr>
            <?php
                $sira += 1;
            } ?>
        </tbody>
        <tfoot>
            <tr>
                <th width="15" scope="col">S/N</th>
                <th>Mail Adresi</th>
                <th>Hesap Türü</th>
                <th>Açıklama</th>
                <th>Eklenme Tarihi</th>
                <th>Kullanıcı</th>
                <th>Eklenmeyen</th>
                <th class="datatable-nosort">İşlem</th>
            </tr>
        </tfoot>
    </table>
</div>
<script src="include/js/data-table.js"></script>
<script src="../../include/js/define.js"></script>
<script>
submitButton.on('click', function() {
    addMailAddress("myForm");

});

$('#user_account').on('click', function() {
    $('#mail_user_div').show(300);
});
$('#general_account').on('click', function() {
    $('#mail_user_div').hide(300);
});


</script>