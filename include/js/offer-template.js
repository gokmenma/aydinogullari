var submitButton = $('#submitButtonByAjax');
function addType(page,messagecontent) {

    var id = $("#id").val();
    var type = '';
    var isValid = true;

    var type = (id > 0) ? "update" : "new";
    var message = (id > 0) ? " güncellendi!" : " eklendi!";

    var wysihtml5Content = $('.textarea_editor').val();
    var formData = {};
    $("#myForm").find("input, select").each(function(index, element){
        if (element.required && !element.value.trim()) {
            isValid = false;
        } else {
            // Alanlar doluysa
            formData[element.name] = element.value;    
        }
    });


    if (!wysihtml5Content) {
        isValid = false;
    } else {
        formData["editor"] = wysihtml5Content;
    }

     
     formData["type"] = type;

    if (isValid) {

        $.ajax({
            url: "index.php?p="+ page +"&type=" + type,
            type: "POST",
            data: formData,

        }).then(function (data) {
            Swal.fire({
                title: "Başarılı!",
                text: messagecontent + " başarı ile" + message,
                icon: "success",
            }).then(() => {
                window.location.href = "index.php?p=" + page;
            });
            var modal = $("#exampleModalCenter");
            var modalInstance = bootstrap.Modal.getInstance(modal);
            modalInstance.hide();

        });
    } else {
        Swal.fire({
            title: "Uyarı!",
            text: messagecontent + " türü boş bırakılamaz!",
            icon: "error"
        })
    }
}

$(document).on('click', '.edit', function () {
    var buttonId = $(this).attr('data-id');
    $('#id').val(buttonId);
    var currentrow = $(this).closest("tr");

    var title = currentrow.find('td:eq(1)').text().trim(); // İkinci kolonun değerini al
    $('#title').val(title); // Alınan değeri input alanına yaz
    
    var stateValue = currentrow.find('td:eq(2)').data("id"); // Tablodaki ikinci sütunun değerini al
    $('#state').val(stateValue); // Bu değeri select elementine aktar
    $("#state").selectpicker("refresh"); // Select elementini yenile

    var content =currentrow.find('td:eq(3)').text().trim()
    $(".wysihtml5-sandbox")
    .contents()
    .find("body")
    .html(content);
});