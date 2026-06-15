var submitButton = $("#submitButtonByAjax");
function addType(page, messagecontent) {
  var id = $("#id").val();
  var type = "";
  var title = $("#title").val();
  var type = id > 0 ? "update" : "new";
  var message = id > 0 ? " güncellendi!" : " eklendi!";

  if (title != "") {
    $.ajax({
      url: "index.php?p=" + page + "&type=" + type,
      type: "POST",
      data: {
        title: title,
        id: id,
        type: type,
      },
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
      icon: "error",
    });
  }
}

$(document).on("click", ".edit", function () {
  var buttonId = $(this).attr("data-id");
  $("#id").val(buttonId);

  var title = $(this).closest("tr").find("td:eq(1)").text().trim(); // İkinci kolonun değerini al
  $("#title").val(title); // Alınan değeri input alanına yaz
});

$(document).on("click", ".edit-mail", function () {
  var buttonId = $(this).attr("data-id");
  $("#id").val(buttonId);

  var type = $(this).closest("tr").find("td:eq(2)").text().trim();

  if (type === 'Genel Mail') {
    $("#general_account").prop("checked", true);
    $('#mail_user_div').hide();
  }else{
    $("#user_account").prop("checked", true);
    $('#mail_user_div').show();
  }

  var title = $(this).closest("tr").find("td:eq(1)").text().trim();
  $("#mail_address").val(title);

  var mail_user = $(this).closest("tr").find("td:eq(5)").text().trim();
  $("#mail_user").val(mail_user);
  $(".selectpicker").selectpicker("render");

  var description = $(this).closest("tr").find("td:eq(3)").text().trim();
  $("#description").val(description);
});

function addMailAddress(form) {
  var id = $("#id").val();
  if (id > 0) {
    var type = "update";
  } else {
    var type = "new";
  }

  var form = document.getElementById(form);
  var formData = new FormData(form);
  formData.append("action", type);
  formData.append("id", id);
  formData.append("page", "send-mail-accounts");
  //   formData.forEach(function(value, key){
  //       console.log(key + ': ' + value);
  //   });

  $.ajax({
    url: "pages/1/send-mail-accounts.php",
    type: "POST",
    processData: false,
    contentType: false,
    data: formData,
    success: function (response) {
      var res = jQuery.parseJSON(response);
      if (res.status == 200) {
        swal
          .fire({
            icon: "success",
            title: "Başarılı",
            text: res.message,
          })
          .then((result) => {
            if (result.isConfirmed) {
              location.reload();
            }
          });
      } else {
        var res = jQuery.parseJSON(response);
        swal.fire({
          icon: "warning",
          title: "Hatalı",
          text: res.message,
        });
      }
    },
    error: function (error) {
      var res = jQuery.parseJSON(response);
      Swal.fire({
        title: "Hata!",
        text: res.message,
        icon: "error",
      });
    },
  });
}
