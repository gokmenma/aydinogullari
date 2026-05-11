<tr>
    <td class="pl-2">
        <button class="sil btn btn-sm btn-danger"> Sil</button>
    </td>
    <td>
        <input required type="text" class="form-control region" name="cinsi[]" value="<?php echo $cinsi ?>">
    </td>
    <td>
        <input required type="text" class="form-control region" name="bulundugu_bolge[]"
            value="<?php echo $bulundugu_bolge ?>">
    </td>
    <td>
        <input required type="text" class="form-control region" name="markasi[]"
            value="<?php echo $markasi ?>">
    </td>
    <td>
        <?php optionselect("kontroltarihi[]", $kontrol_tarihi) ?>

    </td>
    <td>
        <?php optionselect("problems[]", $problems) ?>

    </td>
    <td>
        <?php optionselect("islemler[]", $islemler) ?>

    </td>
</tr>