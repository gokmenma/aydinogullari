<?php

permcontrol("mailandsmssend");

if (set("sms_active") != "on") {

	header("Location: index.php");
	exit;

}

if ($_POST) {
	$customers=$_POST["customers"];

	if(strlen($_POST["message"]) < 160){
		if($_POST["customers"]){
			
			$message=$_POST["message"];
		try{
			$response= send_sms($customers,$message);
				echo showAlert("success", $phonesArray . " Mesaj Gönderme Başarılı!");
		}catch (Exception $exc)
		{
			echo showAlert("alert","Mesaj Gönderme Başarısız!");
		}
		}
	}

	
}


?>

<div class="pd-20 bg-white border-radius-16 box-shadow mb-30">
    <form method="POST" action="index.php?p=send-sms&post=true">
        <div class="clearfix">
            <div class="pull-left">
                <h4 class="text-blue">
                    <?php echo $pdat["p_title"]; ?>
                </h4>
                <p class="mb-30 font-14">Sayfadaki <font color="red">(*)</font> yıldız ile belirtilen alanları boş
                    bırakmayın..<br></p>
            </div>
            <div class="float-right">
                <button class="btn btn-sm btn-primary"><i class="fa fa-send"></i> Gönder</button>
            </div>

        </div>
        <div class="col-md-12 col-sm-12">
            <div class="form-group">
                <label>
                    <font color="red">(*)</font>Mesaj İçeriği <font color="red">(En fazla 160 karakter olmasına
                        dikkat ediniz.)</font>
                </label>
                <textarea name="message" value="" class="form-control" type="text"></textarea>

            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label>
                    <font color="red">(*)</font>Müşteri Seçimi
                </label>
                <select name="customers[]" class="selectpicker form-control" data-style="border bg-white" multiple
                    data-actions-box="true" data-selected-text-format="count">
                    <?php
					$mcek = $ac->prepare("SELECT * FROM customers ORDER BY company ASC");
					$mcek->execute();
					while ($mm = $mcek->fetch(PDO::FETCH_ASSOC)) {
						?>
                    <option value="9<?php echo $mm["gsm"]; ?>">
                        <?php echo $mm["company"] . " - [ " . $mm["yetkili"] . " - " . $mm["gsm"] . " ]"; ?>
                    </option>
                    <?php
					}


					?>


                </select>

                </select>
            </div>


        </div>
    </form>
</div>

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