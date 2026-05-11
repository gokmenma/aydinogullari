$(document).ready(function() {
    var i = 0;

    // Yeni Ürün Satırı Ekleme
    $("#addRow").click(function() {
        i++;
        var html = '<tr id="row' + i + '">' +
            '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn-xs btn_remove"><i class="fa fa-trash"></i></button></td>' +
            '<td><input type="text" name="p_kod[]" placeholder="P1" class="form-control form-control-sm"></td>' +
            '<td><input type="text" name="p_bolum[]" placeholder="Bölüm" class="form-control form-control-sm"></td>' +
            '<td><input type="text" name="p_ekipman[]" placeholder="Ekipman/Adet" class="form-control form-control-sm"></td>' +
            '<td><label class="switch"><input type="checkbox" name="p_yer[' + (i-1) + ']" checked><span class="slider"></span></label></td>' +
            '<td><label class="switch"><input type="checkbox" name="p_erisim[' + (i-1) + ']" checked><span class="slider"></span></label></td>' +
            '<td><label class="switch"><input type="checkbox" name="p_montaj[' + (i-1) + ']" checked><span class="slider"></span></label></td>' +
            '<td><label class="switch"><input type="checkbox" name="p_test[' + (i-1) + ']" checked><span class="slider"></span></label></td>' +
            '<td><label class="switch"><input type="checkbox" name="p_sesli[' + (i-1) + ']" checked><span class="slider"></span></label></td>' +
            '<td><label class="switch"><input type="checkbox" name="p_isikli[' + (i-1) + ']" checked><span class="slider"></span></label></td>' +
            '<td><label class="switch"><input type="checkbox" name="p_adresleme[' + (i-1) + ']" checked><span class="slider"></span></label></td>' +
            '</tr>';
        $('#yasTable tbody').append(html);
    });

    // Satır Silme
    $(document).on('click', '.btn_remove', function() {
        var button_id = $(this).attr("id");
        $('#row' + button_id + '').remove();
    });

    // İlk satırı otomatik ekle
    $("#addRow").trigger("click");
});

// Form Doğrulama
function validateForm() {
    var isValid = true;
    if ($('input[name="report_number"]').val() === "") {
        alert("Rapor numarası boş bırakılamaz!");
        isValid = false;
    }
    
    if (isValid) {
        $("#submitButton").attr("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Kaydediliyor...');
        $("#myForm").submit();
    }
}
