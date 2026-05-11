<tr>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-danger sil"><i class="fa fa-trash"></i></button>
    </td>
    <td>
        <input required type="text" class="form-control form-control-modern" name="attach_description[]" value="<?php echo @$attach_description; ?>" placeholder="Ek dosya açıklaması...">
    </td>
    <td>
        <div class="custom-file">
            <input required type="file" class="form-control-file" name="report_attach[]">
        </div>
    </td>
</tr>