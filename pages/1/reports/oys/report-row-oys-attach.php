<tr >
    <td class="pl-2" >
        <button type="button" class="sil btn btn-sm btn-danger"> Sil</button>
    </td>
    <td>
        <input required type="text" class="form-control region" name="attach_description[]"
            value="<?php echo $attach_description ?>">
    </td>
    <td >
        <?php if ($type == "edit") { ?>

            <button type="button" class="btn btn-sm btn-success">Göster</button>

        <?php
        } else { ?>
            <input required type="file" class="form-control btn-sm region" name="report_attach[]"
                value="<?php echo $report_attach ?>">
        <?php } ?>

    </td>
</tr>