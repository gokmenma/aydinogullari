
let url = "/pages/1/products/api.php";
$(document).on("click", "#submitButton", function () {
  var id = $("#id").val();
  var form = $("#productForm");
  $(".selectpicker").selectpicker("refresh");
  form.validate({
    rules: {
      urunAdi: {
        required: true
      },
      price: {
        required: true,
        number: true
      },
      description: {
        required: true
      }
    },
    messages: {
      urunAdi: {
        required: "Ürün adı giriniz"
      },
      price: {
        required: "Please enter a price",
        number: "Please enter a valid number"
      },
      description: {
        required: "Please enter a description"
      }
    },
    errorElement: "em",
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
    return;
  }

  var formData = new FormData(form[0]);
  formData.append("id", id);
  formData.append("action", "save-product");

  for (var pair of formData.entries()) {
    console.log(pair[0] + ", " + pair[1]);
  }

  fetch(url, {
    method: "POST",
    body: formData
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      title = data.status == "success" ? "Başarılı" : "Hata";
      Swal.fire({
        title: title,
        text: data.message,
        icon: data.status
      });
    })
    .catch((error) => {
      console.error("Error:", error);
    });
});

//Selectpicker da değişiklik olunca formu validate et
$(document).on("change", ".selectpicker", function () {
  $(this).valid();
});

$(document).on("click", ".product-delete", function () {
  let id = $(this).attr("data-id");
  let product_name = $(this).attr("data-name");

  let formData = new FormData();
  formData.append("id", id);
  formData.append("action", "delete-product");

  swal
    .fire({
      title: "Emin misiniz?",
      html: product_name + " <br> adlı ürün silinecektir!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Evet",
      cancelButtonText: "Hayır",
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6"
    })
    .then((result) => {
      if (result.isConfirmed) {
        fetch(url, {
          method: "POST",
          body: formData
        })
          .then((response) => response.json())
          .then((data) => {
            //console.log(data);
            title = data.status == "success" ? "Başarılı" : "Hata";
            Swal.fire({
              title: title,
              text: data.message,
              icon: data.status
            });
            table = $("#tblProducts").DataTable();
           //satırı sil
            table.row($(this).parents("tr")).remove().draw();
          })
          .catch((error) => {
            console.error("Error:", error);
          });
      }
    });
});
