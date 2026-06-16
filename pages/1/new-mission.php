<?php
use PHPMailer\PHPMailer\PHPMailer;

require 'vendor/autoload.php';

permcontrol('missionadd');
if ($_POST) {
	$userstring = '';

	$FirmaAdi = @$_POST['firma_adi'];
	$categoryName = @$_POST['categoryName'];
	$title = @$_POST['title'];
	$mdesc = @$_POST['mdesc'];
	$startdate = $_POST['startdate'] ?? date('d-m-Y H:i:s');
	$lastdate = @$_POST['lastdate'];
	$urg = @$_POST['urg'];
	$statu = @$_POST['statu'];

	foreach ($_POST['permings'] as $autx) {
		$userstring .= $autx . '|';
		$send_mail_address = $autx . ',';
	}

	$insq = $ac->prepare("INSERT INTO missions SET 
								FirmaAdi = ? , categoryName = ? , title = ? , 
								mdesc = ? , startdate = ? , 
								lastdate = ? , authors = ? , creativer = ? , 
								urgency = ? , okeydate = ? , statu = ? ");

	$insq->execute(
		array(
			$FirmaAdi,
			$categoryName,
			$title,
			$mdesc,
			$startdate,
			date_tr($lastdate),
			$userstring,
			sesset('id'),
			$urg,
			'-',
			0
		)
	);

	if ($insq) {
		if (!empty($_POST['permings'])) {
			$mailkonu = 'Görev Bildirimi';
			$site_url = set('panel_url');
			$mail_from = getUserInfo(sesset('id'), 'email');
			// $mail_from = 'beyzade83@hotmail.com';

			// include ("include/mailer/class.phpmailer.php");
			// require 'include/mailler/src/PHPMailer.php';

			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->SMTPDebug = 2;
			$mail->SMTPAuth = true;

			$mail->SMTPSecure = 'tls';  // Güvenli bağlantı için tls kullanıyoruz
			$mail->Host = set('mail_host');  // Mail sunucusunun adresi (IP de olabilir)
			$mail->Port = set('mail_port');
			$mail->IsHTML(true);
			$mail->Encoding = 'base64';
			$mail->SetLanguage('tr', 'phpmailer/language');
			$mail->Username = set('mail_username');  // Gönderici adresiniz (e-posta adresiniz)
			$mail->Password = set('mail_password');  // Mail adresimizin sifresi
			$mail->setFrom($mail_from, set('company_name'));
			$mail->AddAddress('beyzade83@gmail.com');  // Gönderilen Alıcı

			foreach ($_POST['permings'] as $user) {
				$user_mail = getUserInfo($user, 'username');
				$mail->AddAddress($user_mail);  // Gönderilen Alıcı
				$mailto .= $user . '|';
				$users_assigned_tasks .= $user_mail . ',';
			}

			// Burada mail içeriği oluşturulacak
			$mailicerik = 'Merhaba, <br> Tarafınıza bir görev atanmıştır. <br> 
				Görev Başlığı: ' . $title
				. '<br> Firma Adı : ' . $FirmaAdi
				. '<br> Görev Açıklaması: ' . $mdesc
				. '<br> Görev Başlangıç Tarihi: ' . $sdate
				. '<br> Görev Bitiş Tarihi: ' . $lastdate
				. '<br> Görev Aciliyeti: ' . $urg
				. '<br> Görev Kategorisi: ' . $cat
				. '<br> Görevi Oluşturan: ' . getUserInfo(sesset('id'), 'username')
				. '<br> Görev Atananlar: ' . $users_assigned_tasks 
				. '<br> Görevi Görüntülemek için <a href="' . $site_url . '/index.php?p=view-mission&mid=' . $ac->lastInsertId() . '">tıklayınız</a>';
			
			// $mail->AddAttachment($hedef); // Yüklenen dosyayı ekle
			$mail->Subject = $mailkonu;
			$mail->Body = $mailicerik;
			$mail->CharSet = 'UTF-8';
			if ($mail->Send()) {
				// Eğer başarılı ise veritabanına kayıt edilir
				//   $sql = $ac->prepare('INSERT INTO mail_logs SET tomail = ?, from_mail = ?, mail_file = ? , mail_body = ? ,sender = ?');
				//   $sql->execute(array($mailto, $mail_from, $mailicerik, sesset('id')));
				header('Location: index.php?p=all-missions&send-mail=true');
			} else {
				header('Location:index.php?p=new-mission&st=unsuccessful');
			}
		} else {
			header('Location:index.php?p=send-mail&st=nocustom');
			exit();
		}

		// header("Location: index.php?p=new-mission&st=newsuccess");
	}
} else {
}
// header("Location: index.php?p=new-mission&st=newsuccess");

if (@$_GET['st'] == 'empties') {
	showAlert('alert', '(*) ile işaretli alanları boş bırakmadan tekrar deneyin.');
}
if (@$_GET['st'] == 'newsuccess') {
	showAlert('success', 'Görev oluşturuldu.');
}
if (@$_GET['st'] == 'unsuccessful') {
	showAlert('alert', 'E-posta gönderimi başarısız.');
}
?>
<style>
    .urgency-selector-wrapper {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-top: 5px;
    }
    .urgency-option-premium {
        position: relative;
        cursor: pointer;
        flex: 1;
    }
    .urgency-option-premium input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }
    .urgency-custom-radio {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 16px;
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        background-color: #f8fafc;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        color: #4a5568;
        text-align: center;
    }
    .urgency-custom-radio i {
        font-size: 0.6rem;
        transition: transform 0.2s;
    }
    
    .urgency-high input:checked ~ .urgency-custom-radio {
        background-color: #fef2f2;
        border-color: #ef4444;
        color: #ef4444;
        box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1), 0 2px 4px -1px rgba(239, 68, 68, 0.06);
    }
    .urgency-high .urgency-custom-radio i {
        color: #ef4444;
    }
    
    .urgency-medium input:checked ~ .urgency-custom-radio {
        background-color: #eff6ff;
        border-color: #3b82f6;
        color: #3b82f6;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1), 0 2px 4px -1px rgba(59, 130, 246, 0.06);
    }
    .urgency-medium .urgency-custom-radio i {
        color: #3b82f6;
    }
    
    .urgency-low input:checked ~ .urgency-custom-radio {
        background-color: #f0fdf4;
        border-color: #22c55e;
        color: #22c55e;
        box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.1), 0 2px 4px -1px rgba(34, 197, 94, 0.06);
    }
    .urgency-low .urgency-custom-radio i {
        color: #22c55e;
    }
    
    .urgency-option-premium:hover .urgency-custom-radio {
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }
    .editor-wrapper {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }
</style>

<form action="" method="POST" id="myForm">
    <div class="new-mission-manage-wrapper">
        <!-- Header Card -->
        <div class="premium-header-card animate-fade-in">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fa fa-tasks"></i>
                    </div>
                    <div class="header-title">
                        <h4><?php echo $pdat['p_title'] ?? 'Görev Oluştur'; ?></h4>
                        <span class="header-number-badge">
                            <i class="fa fa-info-circle"></i> Yeni Görev Tanımlama
                        </span>
                    </div>
                </div>
                <div class="header-actions">
                    <button type="button" id="submitButton" onclick="validateForm()" class="btn-header btn-header-save">
                        <i class="fa fa-save"></i> Kaydet
                    </button>
                </div>
            </div>
        </div>

        <!-- Kart 1: Görev Bilgileri -->
        <div class="form-card mb-4 animate-fade-in">
            <div class="form-card-header">
                <div class="card-icon card-icon-blue">
                    <i class="fa fa-info-circle"></i>
                </div>
                <div>
                    <h5>Görev Bilgileri</h5>
                    <p>Atanacak göreve ait genel firma, kategori, konu ve zamanlama detayları</p>
                </div>
            </div>
            
            <div class="form-grid">
                <!-- Firma -->
                <div class="form-field">
                    <label for="FirmaAdi"><font color="red">(*)</font> Firma</label>
                    <div class="input-group m-0" style="flex-wrap: nowrap;">
                        <input type="text" class="form-control" name="firma_adi" id="FirmaAdi" placeholder="Firma seçiniz veya yazınız!">
                        <button type="button" class="btn btn-primary" style="border-top-left-radius: 0; border-bottom-left-radius: 0;" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            <i class="fa fa-hand-o-up"></i>
                        </button>
                    </div>
                </div>

                <!-- Kategori -->
                <div class="form-field">
                    <label for="categoryName"><font color="red">(*)</font> Kategori</label>
                    <div class="input-group m-0" style="flex-wrap: nowrap;">
                        <select name="categoryName" id="categoryName" class="selectpicker form-control" data-style="border bg-white" required autocomplete="off" autofocus="false">
                            <?php
                                $sql = $ac->prepare('SELECT * FROM `missioncategory` where NOT categoryName IS NULL');
                                $sql->execute();
                                $categories = $sql->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($categories as $category) {
                            ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo $category['categoryName']; ?>
                                </option>
                            <?php } ?>
                        </select>
                        <button type="button" class="btn btn-primary" style="border-top-left-radius: 0; border-bottom-left-radius: 0;" data-bs-toggle="modal" data-bs-target="#exampleModal2">
                            <i class="fa fa-plus-circle"></i>
                        </button>
                    </div>
                </div>

                <!-- Konu -->
                <div class="form-field">
                    <label for="title"><font color="red">(*)</font> Konu :</label>
                    <input name="title" id="title" value="" class="form-control" required type="text" placeholder="Görev konusunu giriniz">
                </div>

                <!-- Görevi Oluşturan -->
                <div class="form-field">
                    <label for="Olusturan"><font color="red">(*)</font> Görevi Oluşturan</label>
                    <select disabled name="Olusturan" id="Olusturan" class="selectpicker form-control" required>
                        <option selected value="1">Admin</option>
                    </select>
                </div>

                <!-- Başlangıç Tarihi -->
                <div class="form-field">
                    <label for="startdate">Başlangıç Tarihi</label>
                    <input name="startdate" id="startdate" class="form-control date-picker" autocomplete="off" autofocus="false" value="" placeholder="Tarih Seçin" type="text">
                </div>

                <!-- Son Tarih -->
                <div class="form-field">
                    <label for="lastdate">Son Tarih</label>
                    <input name="lastdate" id="lastdate" class="form-control date-picker" autocomplete="off" value="" placeholder="Tarih Seçin" type="text">
                </div>

                <!-- Görevin Atanacağı Kullanıcılar -->
                <div class="form-field">
                    <label for="permings">Görevin Atanacağı Kullanıcılar</label>
                    <select required name="permings[]" id="permings" class="selectpicker form-control" data-style="btn-outline-secondary"
                        multiple data-actions-box="true" data-selected-text-format="count">
                        <?php
                            $permq = $ac->prepare('SELECT * FROM perms ');
                            $permq->execute();
                            while ($pp = $permq->fetch(PDO::FETCH_ASSOC)) {
                                if ($pp['mistake'] == 'on') {
                        ?>
                                <optgroup label="<?php echo $pp['p_title']; ?>">
                                    <?php
                                    $permx = $ac->prepare('SELECT * FROM users WHERE permission = ? ');
                                    $permx->execute(array($pp['id']));
                                    while ($px = $permx->fetch(PDO::FETCH_ASSOC)) {
                                        ?>
                                        <option value="<?php echo $px['id']; ?>">
                                            <?php echo $px['username']; ?>
                                        </option>
                                    <?php
                                    }
                                                            ?>
                                </optgroup>
                            <?php }
                            } ?>
                    </select>
                </div>

                <!-- Aciliyet -->
                <div class="form-field">
                    <label>Aciliyet</label>
                    <div class="urgency-selector-wrapper">
                        <label class="urgency-option-premium urgency-high">
                            <input type="radio" id="customRadioInline1" name="urg" value="Yüksek">
                            <span class="urgency-custom-radio"><i class="fa fa-circle"></i> Yüksek</span>
                        </label>
                        <label class="urgency-option-premium urgency-medium">
                            <input type="radio" id="customRadioInline2" checked name="urg" value="Orta">
                            <span class="urgency-custom-radio"><i class="fa fa-circle"></i> Orta</span>
                        </label>
                        <label class="urgency-option-premium urgency-low">
                            <input type="radio" id="customRadioInline3" name="urg" value="Düşük">
                            <span class="urgency-custom-radio"><i class="fa fa-circle"></i> Düşük</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kart 2: Görev Açıklaması -->
        <div class="form-card mb-4 animate-fade-in">
            <div class="form-card-header">
                <div class="card-icon card-icon-purple">
                    <i class="fa fa-pencil-square-o"></i>
                </div>
                <div>
                    <h5>Görev Açıklaması</h5>
                    <p>Görevin yerine getirilmesi için gerekli tüm detayları giriniz</p>
                </div>
            </div>
            <div class="editor-wrapper">
                <textarea name="mdesc" class="textarea_editor form-control border-radius-8" placeholder="Bir şeyler yaz ..."></textarea>
            </div>
        </div>

        <!-- Modal 1: Firma Seç -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Firma Seç</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <select id="FirmaSec" name="FirmaSec" data-header="Firmalar" class="selectpicker form-control">
                            <?php
                                $cek = $ac->prepare('SELECT * FROM customers');
                                $cek->execute();
                                while ($dat = $cek->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                                <option value="<?php echo $dat['company']; ?>">
                                    <?php echo $dat['company']; ?>
                                </option>
                            <?php
                                }
                            ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Vazgeç</button>
                        <button type="button" id="ModalSaveButton" onclick="Sec()" data-bs-dismiss="modal" class="btn btn-primary">Seç</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal 2: Kategori Ekle -->
        <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Kategori Adı:</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" class="form-control" name="Addcategory" id="Addcategory" placeholder="Eklenecek kategori adını yazınız...">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Vazgeç</button>
                        <button type="button" id="ModalSaveButton" onclick="SaveNewKategory()" data-bs-dismiss="modal" class="btn btn-primary">Kaydet</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<script>
	function Sec() {
		// Seçilen değeri al
		var selectedValue = document.getElementById('FirmaSec').value;

		// Firma ID'li input alanına ata
		document.getElementById('FirmaAdi').value = selectedValue;

	}

	// function SaveNewKategory() {
	// 	var Addcategory = document.getElementById('Addcategory').value;

	// 	fetch('index.php?p=categoryAdd', {
	// 			method: 'POST',
	// 			headers: {
	// 				'Content-Type': 'application/x-www-form-urlencoded',
	// 			},
	// 			body: 'Addcategory=' + encodeURIComponent(Addcategory),
	// 		})
	// 		.then(response => {
	// 			var selectElement = document.getElementById('categoryName');
	// 			var newOption = document.createElement('option');
	// 			newOption.value = Addcategory;
	// 			newOption.textContent = Addcategory;
	// 			selectElement.appendChild(newOption);
	// 		})
	// 		.catch(error => {
	// 			// Hata durumunda burada işlemler yapabilirsiniz
	// 		});
	// }
</script>

<script>
	$(document).ready(function () {
		$(".selectpicker").selectpicker({
			selectAllText: "Tümünü Seç",
			deselectAllText: 'Seçimi Temizle',
			style: "border bg-white",
			liveSearch: true,
			liveSearchPlaceholder: "Ara..",
			noneResultsText: 'Eşleşen kayıt yok {0}',
			size: 5,
			noneSelectedText: "Seçim Yapınız!"
		})
	})
</script>