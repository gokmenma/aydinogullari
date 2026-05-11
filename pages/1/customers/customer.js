

$(document).on("click", "#saveCustomer", function () {
    
  var form = $("#customerForm");


  form.validate({
    rules: {
      company: {
        required: true
      },
      categoryName: {
        required: true
      }
    },
    messages: {
      company: {
        required: "Firma adını giriniz"
      }
    },
    errorPlacement: function (error, element) {
      if (element.hasClass("selectpicker")) {
        element.next().addClass("is-invalid");
      } else {
        element.addClass("is-invalid");
      }
    },
    success: function (label, element) {
      if ($(element).hasClass("selectpicker")) {
        $(element).next().removeClass("is-invalid");
      } else {
        $(element).removeClass("is-invalid");
      }
    }
  });

  if (!form.valid()) {
    swal.fire({
      title: "Hata!",
      text: "Lütfen zorunlu alanları doldurunuz!",
      icon: "error",
      confirmButtonText: "Tamam"
    });
    return;
  }
    var formData = new FormData(form[0]);
  formData.append("action", "create");
    for (var pair of formData.entries()) {
      console.log(pair[0] + ", " + pair[1]);
    }

  fetch("/App/api/customer.php", {
    method: "POST",
    body: formData
  })
    .then((responce) => responce.json())
    .then((data) => {
      console.log(data);
      if (data.status === "success") {
        title = "Başarılı!";
      } else {
        title = "Hata!";
      }
      Swal.fire({
        title: title,
        text: data.message,
        icon: data.status,
        confirmButtonText: "Tamam"
      });
    })
    .catch((error) => {
      console.error("Error:", error);
    });
});
