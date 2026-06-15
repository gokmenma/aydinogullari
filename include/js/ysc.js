$(document).on("click", "#addMultiRowModal", function () {
  var satir_sayisi = $("#eklenecek_satir_sayisi").val();

  if (satir_sayisi > 100) {
    swal.fire({
      title: "Uyarı!",
      text: "Bir seferde en fazla 100 satır ekleyebilirsiniz.",
      icon: "warning",
      confirmButtonText: "Tamam",
    });
    satir_sayisi = 100;
    return;
  }
  if (satir_sayisi > 0) {
    for (var i = 0; i < satir_sayisi; i++) {
      addRow();
    }
  }

  $(".selectpicker").selectpicker("refresh");

  $(".rpr-date").each(function () {
    $(this).datepicker({
      language: "tr",
      dateFormat: "dd-mm-yyyy",
      autoclose: "true",
    });
  });
});

function addRow() {

    var rowCount = $("#yscTable tbody tr").length + 1;
 
  // console.log("Row count: " + rowCount);
  $("#yscTable tbody").append(
    "<tr tabindex='" +
      rowCount +
      "'>" +
      '<td class="pl-2">' +
      '<button class="sil btn btn-sm btn-danger"> Sil</button>' +
      "</td>" +
      '<td><input required type="text" class="form-control satir_no" id="cihazno" value="' +
      rowCount +
      '" name="cihazno[]"></td>' +
      "<td>" +
      '<input type="text" class="form-control region" name="cihazbolge[]" style="min-width:200px" >' +
      "</td>" +
      "<td>" +
      '<input type="text" class="form-control region" data-tooltip="" name="cinsi[]" style="min-width:200px" ' +
      '">' +
      "</td>" +
      "<td data-tooltip='aa/yyyy veya aa-yyyy veya aa.yyyy şeklinde girebilirsiniz'>" +
      '<input type="text" ' +
      ' autocomplete="off" class="form-control filling-date" placeholder="aa/yyyy" name="dolumtarihi[]" >' +
      "</td>" +
      "<td>" +
      '<input type="text" autocomplete="off" ' +
      '" class="form-control expiration-date" placeholder="aa/yyyy" name="sonkullanimtarihi[]"' +
      "</td>" +
      "<td>" +
      '<input type="text" autocomplete="off" class="form-control" name="kontoltarihi1[]" >' +
      "</td>" +
      "<td>" +
      '<input type="text" autocomplete="off" class="form-control" name="kontoltarihi2[]" >' +
      "</td>" +
      "<td>" +
      '<div class="input-group m-0 p-0">' +
      '<input name="islemkontroltarihi1[]" type="text" class="form-control islemkontroltarihi" aria-label="Text input with dropdown button">' +
      '<div class="input-group-append">' +
      '<button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>' +
      '<div class="dropdown-menu dropdown-menu-right">' +
      '<a class="dropdown-item btn">Basınç</a>' +
      '<a class="dropdown-item btn">Kontrol</a>' +
      '<a class="dropdown-item btn">Dolum</a>' +
      "</div>" +
      "</div>" +
      "</div>" +
      "</td>" +
      "<td>" +
      '<div class="input-group m-0 p-0">' +
      '<input name="islemkontroltarihi2[]" value="" type="text" class="form-control islemkontroltarihi" aria-label="Text input with dropdown button">' +
      '<div class="input-group-append">' +
      '<button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>' +
      '<div class="dropdown-menu dropdown-menu-right">' +
      '<a class="dropdown-item btn">Basınç</a>' +
      '<a class="dropdown-item btn">Kontrol</a>' +
      '<a class="dropdown-item btn">Dolum</a>' +
      "</div>" +
      "</div>" +
      "</div>" +
      "</td>" +
      "<td>" +
      '<select name="dismuhafaza[]" class="form-control w-auto" data-style="bg-white border">' +
      '<option value="">Seçiniz</option>' +
      '<option value="0">UYGUN DEĞİL</option>' +
      '<option selected value="1">UYGUN</option>' +
      "</select>" +
      "</td>" +
      "<td>" +
      '<select name="cevrekontrolu[]" class="form-control w-auto" data-style="bg-white border">' +
      '<option value="">Seçiniz</option>' +
      '<option value="0">UYGUN DEĞİL</option>' +
      '<option selected value="1">UYGUN</option>' +
      "</select>" +
      "</td>" +
      "<td>" +
      '<select name="pimkontrolu[]" class="form-control w-auto" data-style="bg-white border">' +
      '<option value="">Seçiniz</option>' +
      '<option value="0">UYGUN DEĞİL</option>' +
      '<option selected value="1">UYGUN</option>' +
      "</select>" +
      "</td>" +
      "<td>" +
      '<select name="manometrekontrolu[]" class="form-control w-auto" data-style="bg-white border">' +
      '<option value="">Seçiniz</option>' +
      '<option value="0">UYGUN DEĞİL</option>' +
      '<option selected value="1">UYGUN</option>' +
      "</select>" +
      "</td>" +
      "<td>" +
      '<select name="hortumkontrolu[]" class="form-control w-auto" data-style="bg-white border">' +
      '<option value="">Seçiniz</option>' +
      '<option value="0">UYGUN DEĞİL</option>' +
      '<option selected value="1">UYGUN</option>' +
      "</select>" +
      "</td>" +
      "<td>" +
      '<select name="talimatkontrolu[]" class="form-control w-auto" data-style="bg-white border">' +
      '<option value="">Seçiniz</option>' +
      '<option value="0">UYGUN DEĞİL</option>' +
      '<option selected value="1">UYGUN</option>' +
      "</select>" +
      "</td>" +
      "<td>" +
      '<select name="agirlikkontrolu[]" class="form-control w-auto" data-style="bg-white border">' +
      '<option value="">Seçiniz</option>' +
      '<option value="0">UYGUN DEĞİL</option>' +
      '<option selected value="1">UYGUN</option>' +
      "</select>" +
      "</td>" +
      "</tr> "
  );
}
$("#addRow").click(function () {
  addRow();

  $(".selectpicker").selectpicker("refresh");

  $(".rpr-date").each(function () {
    $(this).datepicker({
      language: "tr",
      dateFormat: "dd-mm-yyyy",
      autoclose: "true",
    });
  });
});

//Satır silme Butonu
$("#yscTable").on("click", ".sil", function (e) {
  e.preventDefault();
  var current_row = parseInt($(this).closest("tr").find(".satir_no").val());

  $("#yscTable .satir_no").each(function () {
    var satir_no = parseInt($(this).val());
    if (satir_no > current_row) {
      $(this).val(satir_no - 1);
    }
  });

  $(this).closest("tr").remove();
});

$(document).on("keyup", ".filling-date", function () {
  var row = $(this).closest("tr");
  var expdate = row.find(".expiration-date");
  var currentDate = $(this).val();
  var parts = currentDate.split(/[\/\-\.]/);
  var month = parseInt(parts[0]);
  var year = parseInt(parts[1]);

  // Add 4 years to the current year
  year += 4;

  // Format the new date

  var newDate = month + "/" + year;

  if (year.toString().length === 4) {
    newDate = month.toString().padStart(2, "0") + "/" + year.toString();
    expdate.val(newDate);
  } else {
    newDate = currentDate;
    expdate.val(newDate);
  }
});

$(document).on("click", ".dropdown-item", function () {
  var text = $(this).text(); // Get the text of the clicked item
  $(this).closest(".input-group").find(".islemkontroltarihi").val(text); // Set the text as the value of the input within the same input group
});

$(document).keydown(function (event) {
  var table = $("#yscTable");
  var rows = table.find("tr");
  var focusedRowIndex = -1;
  var focusedColIndex = -1;

  // Find the index of the currently focused row and column
  rows.each(function (rowIndex) {
    $(this)
      .find("td")
      .each(function (colIndex) {
        if ($(this).find("input:focus, select:focus").length > 0) {
          focusedRowIndex = rowIndex;
          focusedColIndex = colIndex;
          return false; // Break out of both loops
        }
      });
    if (focusedRowIndex >= 0) {
      return false; // Break out of the loop
    }
  });

  // Handle arrow key navigation
  switch (event.key) {
    case "ArrowUp":
      if (focusedRowIndex > 0) {
        rows
          .eq(focusedRowIndex - 1)
          .find("td")
          .eq(focusedColIndex)
          .find("input, select")
          .focus();
      }
      break;
    case "ArrowDown":
      if (focusedRowIndex < rows.length - 1) {
        rows
          .eq(focusedRowIndex + 1)
          .find("td")
          .eq(focusedColIndex)
          .find("input, select")
          .focus();
      }
      break;
    case "ArrowLeft":
      if (focusedColIndex > 0) {
        rows
          .eq(focusedRowIndex)
          .find("td")
          .eq(focusedColIndex - 1)
          .find("input, select")
          .focus();
      }
      break;
    case "ArrowRight":
      var maxColIndex = rows.eq(focusedRowIndex).find("td").length - 1;
      if (focusedColIndex < maxColIndex) {
        rows
          .eq(focusedRowIndex)
          .find("td")
          .eq(focusedColIndex + 1)
          .find("input, select")
          .focus();
      }
      break;
  }
});

$(document).ready(function () {
  $(".selectpicker").selectpicker();

  $(".selectpicker").on("shown.bs.select", function () {
    $(this)
      .closest(".bootstrap-select")
      .find(".dropdown-toggle")
      .addClass("focused");
  });

  $(".selectpicker").on("hidden.bs.select", function () {
    $(this)
      .closest(".bootstrap-select")
      .find(".dropdown-toggle")
      .removeClass("focused");
  });
});

$("#file_name").change(function () {
  var filename = $(this).val();

  var fileExtension = ["xlsx", "xls", "csv"];
  if ($.inArray(filename.split(".").pop().toLowerCase(), fileExtension) == -1) {
    $("#lblWarning").show();
    $("#lblWarning").text(
      "Yalnızca xls veya xlsx uzantılı dosyalar yükleyebilirsiniz."
    );
    $("#file_name").val("");
  } else {
    $("#lblWarning").hide();
  }
});

function readExcel(file) {
  return new Promise((resolve, reject) => {
    var reader = new FileReader();
    reader.onload = function (e) {
      var data = e.target.result;
      var workbook = XLSX.read(data, {
        type: "binary",
      });
      var json_object = []; // JSON nesnelerini tutacak bir dizi
      workbook.SheetNames.forEach(function (sheetName) {
        var XL_row_object = XLSX.utils.sheet_to_row_object_array(
          workbook.Sheets[sheetName]
        );
        json_object = json_object.concat(XL_row_object); // Diziyi güncelle
      });
      resolve(json_object); // Promise'i çöz ve json_object'i döndür
    };
    reader.onerror = function (ex) {
      reject(ex); // Hata durumunda Promise'i reddet
    };
    reader.readAsBinaryString(file);
  });
}

// readExcel fonksiyonunu kullanma
$(document).on("click", "#uploadFromXlsButton", function () {
  var file_name = $("#file_name");
  var file = file_name[0].files[0];

  $(".selectpicker").selectpicker("refresh");
  readExcel(file)
    .then(function (json_object) {
      // console.log(json_object); // Burada json_object ile işlemlerinizi yapabilirsiniz
      for (var i = 0; i < json_object.length; i++) {
        var row = json_object[i];
        // console.log('Row:', row["Cihaz No"]);
        var lastRow = $("#yscTable tbody tr:last").clone();
        $("#yscTable tbody").append(lastRow);
        lastRow.find("input, select").each(function () {
          var name = $(this).attr("name").replace("[]", "");
          var value = row[name]; // Atanacak değer

          if ($(this).is("select")) {
            // Seçim elementi için, eşleşen bir option değeri olup olmadığını kontrol et

            $(this).removeAttr("selected");
            $(this).val(value); // Eşleşen değeri atayın
          } else {
            $(this).val(value); // Diğer input türleri için değeri doğrudan atayın
          }
        });
      }
    })
    .catch(function (error) {
      console.error(error); // Hata yönetimi
    });
    setTooltip();
});


$("#deleteAll").click(function () {
  swal
    .fire({
      title: "Emin misiniz?",
      text: "Tüm satırları silmek istediğinize emin misiniz?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Evet",
      cancelButtonText: "Hayır",
    })
    .then((result) => {
      if (result.isConfirmed) {
        $("#yscTable tbody tr").remove();
        addRow();
      }
    });
});

function setTooltip() {
  // `data-tooltip` özelliğine sahip tüm elemanlar için döngü
  $('.region').each(function() {
      // Elemanın kendi metnini al
      // console.log('selfText:', $(this).val());
      
      var selfText = $(this).val();
      // `title` özelliğini elemanın metniyle güncelle
      $(this).attr('data-tooltip', selfText);
  });

};