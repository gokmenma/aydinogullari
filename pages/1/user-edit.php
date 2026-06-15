<?php

$uid = $_GET["id"];

if (!permtrue("useredit") && sesset("id") != $_GET["id"]) {
	header("Location: index.php");
}

$conts = $ac->prepare("SELECT * FROM users WHERE id = ?");
$conts->execute(array($uid));
$cc = $conts->fetch(PDO::FETCH_ASSOC);


if (!@$_GET["id"]) {
	header("Location:index.php?p=users");
	exit;
}



if ($_POST) {

	if (!$_POST["uname"]) {
		header("Location: index.php?p=user-new&st=empties&id=" . $uid);
		exit;
	}

	if (@$_POST["upassword"] && $_POST["upassword"] != "******") {
		$upassword = md5(md5(md5($_POST["upassword"])));
	} else {
		$upassword = $cc["password"];
	}


	$fullName = $_POST["uname"];
	$uemail = $_POST["uemail"];
	$unvan = $_POST["unvan"];
	$uperm = 1;
	$perm = $_POST["permission"];
	$ugsm = $_POST["ugsm"];
	$meslek = $_POST["meslek"];
	$odasicilno = $_POST["odasicilno"];
	$yetkinlikno = $_POST["yetkinlikno"];
	$ekipnetno = $_POST["ekipnetno"];
	$imza = $_FILES["imza_file"] ?? null;


	try {




		// Mevcut dosya adını veritabanından al
		$stmt = $ac->prepare("SELECT imza_file FROM users WHERE id = ?");
		$stmt->execute([$uid]);
		$currentFile = $stmt->fetch(PDO::FETCH_OBJ)->imza_file;
		$imza_file_name = $currentFile;
		
		if ($imza && $imza["size"] > 0) {
			$imza_file_name = uniqid() . $imza["name"];
			$imza_file_path = $imza["tmp_name"];
			$destination = "files/imzalar/" . $imza_file_name;

			// Mevcut dosya varsa sil
			if ($currentFile && file_exists("files/imzalar/" . $currentFile)) {
				unlink("files/imzalar/" . $currentFile);
			}

			// Yeni dosyayı yükle
			if (move_uploaded_file($imza_file_path, $destination)) {
				echo "Dosya başarıyla yüklendi.";
			} else {
				throw new Exception("Dosya yükleme başarısız.");
			}
		} 
		
		



		$regg = $ac->prepare("UPDATE users SET
										username = ?, 	
										password = ?,
										unvan = ? ,
										email = ?, 
										gsm = ?,
										perm = ?, 
										permission = ?, 
										regdate = ?,
										creativer = ?, 
										meslek = ?, 
										odasicilno = ?, 
										yetkinlikno = ?, 
										ekipnetno = ?, 
										statu = ?,
										imza_file = ?
										WHERE id = ? ");

		$regg->execute(array(
			$fullName,
			$upassword,
			$unvan,
			$uemail,
			$ugsm,
			$uperm,
			$perm,
			TODAY,
			sesset("id"),
			$meslek,
			$odasicilno,
			$yetkinlikno,
			$ekipnetno,
			1,
			$imza_file_name,
			$uid
		));
		header("Location: index.php?p=user-edit&st=newsuccess&id=" . $uid);
	} catch (PDOException $e) {
		echo "Hata: " . $e->getMessage();
	}




	// if ($regg) {
	// 	//header("Location:index.php?p=user-edit&id=$uid");
	// 	header("Location: index.php?p=user-edit&st=newsuccess&id=" . $uid);
	// }
}
if (@$_GET["st"] == "empties") {
	showAlert("alert", "Zorunlu alanları doldurun");
}


if (@$_GET["st"] == "newsuccess") {
	showAlert("success", "Ekip üyesi Başarı ile güncellendi!");
}


$query = $ac->prepare("SELECT * FROM users WHERE id = ?");
$query->execute(array($uid));
$user = $query->fetch(PDO::FETCH_ASSOC);

?>



<div class="content pd-20 bg-white border-radius-16 box-shadow mb-30">

	<div class="clearfix">
		<div class="pull-left">
			<h4 class="text-blue">
				<?php echo $pdat["p_title"]; ?>
			</h4>
			<p class="mb-30 font-14">Sayfadaki <font color="red">(*)</font> yıldız ile belirtilen alanları boş
				bırakmayın..<br></p>
		</div>
		<div class="float-right">

			<button id="submitButton" onclick="validateForm()" data-tooltip="Kaydet" data-tooltip-location="bottom"
				class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Kaydet</button>

			<?php


			if (permtrue("userview")) {
				$link = "index.php?p=users";
				$disabled = "";
			} else {
				$link = "#";
				$disabled = "disabled";
			}
			;


			?>
			<a href="<?php echo $link ?>" data-tooltip="Listeye Dön" data-tooltip-location="bottom"
				class="btn btn-sm btn-secondary text-white <?php echo $disabled ?>">
				<i class="fa fa-list mr-1"></i>Listeye Dön</a>
		</div>
	</div>
	<form enctype="multipart/form-data" id="myForm" method="POST">
		<div class="row">

			<div class="col-md-6 col-sm-12">
				<div class="form-group">
					<label for="uname">
						<font color="red">(*)</font> Adı Soyadı :
					</label>
					<input required type="text" name="uname" value="<?php echo $user["username"]; ?>"
						class="form-control">
				</div>
			</div>
			<div class="col-md-6 col-sm-12">
				<div class="form-group">
					<label for="uemail">
						<font color="red">(*)</font> E-Posta:
					</label>
					<input required name="uemail" type="text" value="<?php echo $cc["email"]; ?>" class="form-control">
				</div>
			</div>
		</div>
		<div class="row">

			<div class="col-md-3 col-sm-12">
				<div class="form-group">
					<label for="upassword">
						<font color="red">(*)</font> Parola:
					</label>
					<input required name="upassword" type="text" value="******" class="form-control">
				</div>
			</div>

			<div class="col-md-3 col-sm-12">
				<div class="form-group">
					<label for="ugsm"> Telefon:</label>
					<input name="ugsm" type="text" value="<?php echo $cc["gsm"]; ?>" class="form-control">
				</div>
			</div>

			<div class="col-md-6 col-sm-12">
				<div class="form-group">
					<label>
						<font color="red">(*)</font> Pozisyon:
					</label>
					<select name="permission" class="selectpicker form-control" data-style="border bg-white">
						<?php
						$pquery = $ac->prepare("SELECT * FROM userroles ");
						$pquery->execute();
						while ($pm = $pquery->fetch(PDO::FETCH_ASSOC)) {

							?>

							<option <?php echo $cc["permission"] == $pm["id"] ? "selected" : ""; ?>
								value="<?php echo $pm["id"]; ?>">
								<?php echo $pm["roleName"]; ?>
							</option>
						<?php } ?>

					</select>
				</div>


			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="unvan">Unvanı :</label>
					<input name="unvan" value="<?php echo $cc["Unvan"]; ?>" type="text" class="form-control">
				</div>
			</div>

			<div class="col-md-6 col-sm-12">
				<div class="form-group">
					<label for="meslek"> Mesleği:</label>
					<input name="meslek" type="text" value="<?php echo $cc["meslek"]; ?>" class="form-control">
				</div>
			</div>
		</div>

		<!-- SİCİL BİLGİLERİ -->
		<div class="row">

			<div class="col-md-3 col-sm-12">
				<div class="form-group">
					<label for="ekipnetno"> Ekipnet No:</label>
					<input name="ekipnetno" type="text" value="<?php echo $cc["ekipnetno"]; ?>" class="form-control">
				</div>
			</div>


			<div class="col-md-3 col-sm-12">
				<div class="form-group">
					<label for="odasicilno"> Oda Sicil No:</label>
					<input name="odasicilno" type="text" value="<?php echo $cc["odasicilno"]; ?>" class="form-control">
				</div>
			</div>


			<div class="col-md-3 col-sm-12">
				<div class="form-group">
					<label for="yetkinlikno"> Yetkinlik No:</label>
					<input name="yetkinlikno" type="text" value="<?php echo $cc["yetkinlikno"]; ?>"
						class="form-control">
				</div>
			</div>
			<div class="col-md-3 col-sm-12">
				<div class="form-group">
					<label for="imza_file"> İmza:</label>
					<input name="imza_file" type="file" value="<?php echo $cc["imza_file"]; ?>"
						class="form-control btn-sm">
				</div>
			</div>
		</div>
		<!-- SİCİL BİLGİLERİ -->
		<br>
	</form>


</div>