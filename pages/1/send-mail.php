<?php
permcontrol("mailandsmssend");
ini_set('display_errors', 1);
error_reporting(E_ALL);
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';

//MÜSTERİLER SAYFASINDAN MAİL GÖNDERMEK İÇİN
    $customer_id = isset($_GET['customer']) ? decrypt($_GET['customer']) : 0 ;

if ($_POST) {
	$dosya_adi = $_FILES["dosya"]["name"];
	
	//Dosya yükleme işlemini gerçekleştir
	if(isset($dosya_adi)){

		if ($_FILES["dosya"]["error"] === UPLOAD_ERR_OK) {
			$dosya_adi = $_FILES["dosya"]["name"];
			$dosya_yolu = $_FILES["dosya"]["tmp_name"];

			$dizin = "files/";
			$rast1 = rand(1, 100);
			$hedef = $dizin . $rast1 . "_" . basename($dosya_adi);

			if (move_uploaded_file($dosya_yolu, $hedef)) {
				// Dosya başarıyla yüklendi
				// E-posta gönderme işlemine devam et
			} else {
				echo "Dosya yükleme hatası.";
				
			}
		} else {
			echo "Dosya yükleme hatası: " . $_FILES["dosya"]["error"];
			
		}
	}	


	try {

			if (!empty($_POST["customers"])) {
				$mailkonu = $_POST["mailkonu"];
				$mailicerik = $_POST["mailicerik"];
				$mail_from = $_POST["mail_address"];


				// include ("include/mailer/class.phpmailer.php");
                //require 'include/mailler/src/PHPMailer.php';
                
				$mail = new PHPMailer();
				$mail->IsSMTP();
				$mail->SMTPDebug = 2;
				$mail->SMTPAuth = true;
           
				$mail->SMTPSecure   = 'tls'; // Güvenli bağlantı için tls kullanıyoruz
				$mail->Host         = set("mail_host"); // Mail sunucusunun adresi (IP de olabilir)
				$mail->Port         = set("mail_port");
				$mail->IsHTML(true);
   				$mail->Encoding     = 'base64';
				$mail->SetLanguage("tr", "phpmailer/language");
				$mail->Username     = set('mail_username'); // Gönderici adresiniz (e-posta adresiniz)
				$mail->Password     = set('mail_password'); // Mail adresimizin sifresi
				$mail->setFrom($mail_from, set("company_name"));

				foreach ($_POST["customers"] as $cust) {
					$mail->AddAddress($cust); // Gönderilen Alıcı
					$mailto .= $cust . "|";
				}

				$mail->AddAttachment($hedef); // Yüklenen dosyayı ekle
				$mail->Subject      = $mailkonu;
				$mail->Body         = $mailicerik;
                $mail->CharSet      = "UTF-8";
				if ($mail->Send()) {

					//Eğer başarılı ise veritabanına kayıt edilir
					$sql= $ac->prepare("INSERT INTO mail_logs SET tomail = ?, from_mail = ?, mail_file = ? , mail_body = ? ,sender = ?");
					$sql->execute(array($mailto,$mail_from,$dosya_adi,$mailicerik,sesset("id")));
					header("Location: index.php?p=send-mail&send=true");

				} else {
					header("Location:index.php?p=send-mail&st=unsuccessful");

				}
			} else {
				header("Location:index.php?p=send-mail&st=nocustom");

			}
			
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
		
	}
		
}

if (@$_GET["st"] == "unsuccessful") {
	showAlert("alert", "E-posta gönderimi başarısız oldu.");
}
if (@$_GET["st"] == "nocustom") {
	showAlert("alert", "En az 1 müşteri seçmelisiniz");
}
if (@$_GET["send"] == "true") {
	showAlert("success", "Mail başarı ile gönderildi!");
}


?>
<form method="POST" id="myForm" enctype="multipart/form-data">
    <div class="pd-20 bg-white border-radius-16 box-shadow mb-30">
        <div class="clearfix">
            <div class="pull-left">
                <h4 class="text-blue">
                    <?php echo $pdat["p_title"]; ?>
                </h4>
                <p class="mb-30 font-14">Sayfadaki <font color="red">(*)</font> yıldız ile belirtilen alanları boş
                    bırakmayın..<br></p>
            </div>
            <button id="submitButton" onclick="validateForm()" id="Gönder" style="float:right"
                class="btn btn-sm btn-primary mt-3 mb-3">
                <i class="fa fa-paper-plane"> </i> Mail Gönder
            </button>
        </div>

        <!-- GÖNDEREN MAİL ADRESİ -->
        <div class="form-group row">
            <label id="labelkategori" class="col-md-2 col-sm-6">
                <font color="red">(*)</font>Gönderen Mail Adresi:
            </label>

    
            <div class="col-md-10 col-sm-12">
                <select name="mail_address" class="selectpicker form-control" data-style="border bg-white">

                    <?php 

				$sql = $ac->prepare("SELECT * from mail_accounts where mail_user = ? or mail_user = ?");
				$sql->execute(array(1,sesset("id")));
				while ($row = $sql->fetch(PDO::FETCH_ASSOC)){
						echo "<option value=".$row["mail_address"]." > ". $row["mail_address"] . "</option>";
				}
				;?>

                </select>
            </div>
        </div>
        <!-- GÖNDEREN MAİL ADRESİ -->


        <!-- FİRMA SEÇİMİ -->
        <div class="form-group row">
            <label id="labelkategori" class="col-md-2 col-sm-6">
                <font color="red">(*)</font>Firma Seçimi :
            </label>
            <div class="col-md-10 col-sm-12">
                <select required name="customers[]" class="selectpicker form-control" data-style="border bg-white"
                    multiple data-actions-box="true" data-selected-text-format="count">
                    <?php
					$mcek = $ac->prepare("SELECT * FROM customers");
					$mcek->execute();
					while ($mm = $mcek->fetch(PDO::FETCH_ASSOC)) {
                        $selected = $customer_id == $mm['id'] ? ' selected' : '';
						?>
                    <option <?php echo $selected ;?> value="<?php echo $mm["email"]; ?>"><?php echo $mm["company"]; ?></option>
                    <?php
					}
					?>
                </select>
            </div>

        </div>
        <!-- FİRMA SEÇİMİ -->

        <!-- KONU -->
        <div class="form-group row">

            <label class="col-md-2 col-sm-6">Konu Başlığı :</label>
            <div class="col-md-10 col-sm-12">
                <input autocomplete="off" required type="text" class="form-control" name="mailkonu">

            </div>
        </div>
        <!-- KONU -->

        <!-- EK -->
        <div class="form-group row">

            <label class="col-md-2 col-sm-6">Ek :</label>
            <div class="col-md-10 col-sm-12">
                <input class="form-control form-control-sm" name="dosya" type="file">
            </div>
        </div>
        <!-- EK -->


        <div class="form-group row">
            <div class="col-md-2 col-sm-6">

                <label>
                    <font color="red">(*)</font>Mail İçeriği :
                </label>
                <p>

                    <button type="button" class="btn btn-sm btn-secondary" data-tooltip="Şablon Olarak Kaydet"
                        data-tooltip-location="right"><i class="fa fa-save"></i></button>
                    <button type="button" class="btn btn-sm btn-primary" data-tooltip="Şablondan Aktar"
                        data-tooltip-location="right"><i class="fa fa-hand-o-right"></i></button>
                </p>
            </div>
            <div class="col-md-10 col-sm-12">
                <textarea required class="textarea_editor form-control border-radius-0" name="mailicerik" value=""
                    type="text"></textarea>
            </div>
        </div>

    </div>


</form>
<script>
$(document).ready(function() {


    $(".selectpicker").selectpicker({
        noneSelectedText: "Listeden Firma Seçiniz!",
        size: 8,
        deselectAllText: "Seçimi Temizle",
        selectAllText: "Tümünü Seç",
        countSelectedText: "{0} Firma seçildi",
        liveSearch: "true"

    })
});
</script>