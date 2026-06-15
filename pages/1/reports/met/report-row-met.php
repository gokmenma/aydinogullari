<tr>
<?php
$cinsi = $cinsi ?? "";
$bulundugu_kisim = $bulundugu_kisim ?? "";
$ozellikler = $ozellikler ?? "";
$control_date_closet = $control_date_closet ?? "";
$next_control_date_closet = $next_control_date_closet ?? "";
$vana_durum = $vana_durum ?? "";
$hortum_baglanti_durum = $hortum_baglanti_durum ?? "";
$levha_durum = $levha_durum ?? "";
$pas_durum = $pas_durum ?? "";
$kilit_durum = $kilit_durum ?? "";
$hortum_durum = $hortum_durum ?? "";
$basinc_degeri = $basinc_degeri ?? "";
$nozul_durum = $nozul_durum ?? "";
$aciklama = $aciklama ?? "";
?>
    <td class="pl-2">
        <button class="sil btn btn-sm btn-danger"> Sil</button>
    </td>
    <td class="app-item-number">
        <input type="text" class="form-control" name="satirno[]" value="<?php echo $sirano ?? "" ?>">
    </td>
    <td><input required type="text" class="form-control region" name="cinsi[]" value="<?php echo $cinsi ?>">
    </td>
    <td><input required type="text" class="form-control region" name="bulundugu_kisim[]"
            value="<?php echo $bulundugu_kisim ?>">
    </td>

    <td><input required type="text" class="form-control region" name="ozellikler[]" value="<?php echo $ozellikler ?>">
    </td>
    <td><input required type="text" class="form-control region" name="control_date_closet[]"
            value="<?php echo $control_date_closet ?>">
    </td>
    <td><input required type="text" class="form-control region" name="next_control_date_closet[]"
            value="<?php echo $next_control_date_closet ?>">
    </td>
    <td>
        <?php optionselect("vana_durum[]", $vana_durum) ?>
    </td>
    <td>
        <?php optionselect("hortum_baglanti_durum[]", $hortum_baglanti_durum) ?>
    </td>
    <td>
        <?php optionselect("levha_durum[]", $levha_durum) ?>
    </td>

    <td>
        <?php optionselect("pas_durum[]", $pas_durum,'','','',2) ?>
    </td>
    <td>
        <?php optionselect("kilit_durum[]", $kilit_durum) ?>
    </td>
    <td>
        <?php optionselect("hortum_durum[]", $hortum_durum) ?>
    </td>

    <td>
        <input required type="text" class="form-control region" name="basinc_degeri[]"
            value="<?php echo $basinc_degeri ?>">
    </td>

    <td>
        <?php optionselect("nozul_durum[]", $nozul_durum) ?>
    </td>

    <td>
        <input type="text" class="form-control region" name="aciklama[]" value="<?php echo $aciklama ?>">
    </td>
</tr>