$(document).on("click", "#addRow", function () {
  addRow();
});

function addRow() {
  var rowCount = $("#hstTable tbody tr").length + 1;
  console.log(rowCount);
  $("#hstTable tbody").append(
    "<tr tabindex='" +
      rowCount +
      "'>" +
      '<td class="pl-2">' +
      '<button class="sil btn btn-sm btn-danger"> Sil</button>' +
      "</td>" +
      '<td><input required type="text" class="form-control region" id="testno" name="testno[]" value=""></td>' +
      "<td>" +
      '<input type="text" class="form-control region" name="kg[]" value="">' +
      "</td>" +
      "<td>" +
      '<input type="text" required autocomplete="off" class="form-control region" name="cinsi[]" value="">' +
      "</td>" +
      "<td>" +
      '<input type="text" required autocomplete="off" class="form-control region" name="imalatci_firma[]"' +
      'value="">' +
      "</td>" +
      "<td>" +
      '<input type="text" required autocomplete="off" class="form-control imal" name="imal_tarihi[]" value="">' +
      "</td>" +
      "<td>" +
      '<input type="text" required autocomplete="off" class="form-control region" name="serino[]" value="">' +
      "</td>" +
      "<td>" +
      '<select required data-tooltip="" name="tse_belgesi[]" class="form-control" data-style="bg-white border">' +
      '<option value="">Seçiniz</option>' +
      '<option value="1">VAR</option>' +
      '<option value="0">YOK</option>' +
      "</select>" +
      "</td>" +
      "<td>" +
      '<select required data-tooltip="" name="yuzey_durumu[]" class="form-control" data-style="bg-white border">' +
      '<option value="">Seçiniz</option>' +
      '<option value="0">OLUMSUZ</option>' +
      '<option value="1">OLUMLU</option>' +
      "</select>" +
      "</td>" +
      "<td>" +
      '<select required name="sizdirmazlik_deneyi[]" class="form-control" data-style="bg-white border">' +
      '<option value="">Seçiniz</option>' +
      '<option value="1">VAR</option>' +
      '<option value="0">YOK</option>' +
      "</select>" +
      "</td>" +
      "<td>" +
      '<select required name="esneme_deneyi[]" class="form-control" data-style="bg-white border">' +
      '<option value="">Seçiniz</option>' +
      '<option value="1">OLUMLU</option>' +
      '<option value="0">OLUMSUZ</option>' +
      "</select>" +
      "</td>" +
      "<td>" +
      '<textarea type="text" autocomplete="off" class="form-control things" style="height:40px;resize:both" name="things[]"' +
      'value="<?php echo $notes; ?>"></textarea>' +
      "</td>" +
      "</tr>"
  );

}

//Satır silme Butonu
$("#hstTable").on("click", ".sil", function (e) {
  e.preventDefault();

  var removedRowIndex = $(this).closest("tr").index() + 1;
  $(this).closest("tr").remove();
});

$(document).on("click", "#addMultiRowModal", function () {
  var satir_sayisi = $("#eklenecek_satir_sayisi").val();

  if (satir_sayisi > 100) {
    swal.fire({
      title: "Uyarı!",
      text: "Bir seferde en fazla 100 satır ekleyebilirsiniz.",
      icon: "warning",
      confirmButtonText: "Tamam"
    });
    satir_sayisi = 100;
    return;
  }
  if (satir_sayisi > 0) {
    for (var i = 0; i < satir_sayisi; i++) {
      addRow();
    }
  }
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
        type: "binary"
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
  

  readExcel(file)
    .then(function (json_object) {
       //console.log(json_object); // Burada json_object ile işlemlerinizi yapabilirsiniz
      for (var i = 0; i < json_object.length; i++) {
        var row = json_object[i];
        
        var lastRow = $("#hstTable tbody tr:last").clone();
        $("#hstTable tbody").append(lastRow);
        lastRow.find("input, select, textarea").each(function () {
          //satır numarasını consola yazdırır
          var name = $(this).attr("name").replace("[]", "");
          var value = row[name]; // Atanacak değer

          if ($(this).is("select")) {
            $(this).removeAttr("selected");
            $(this).val(value); // Eşleşen değeri atayın
            
          } else {
            console.log('name:', name, 'value:', value);
            $(this).val(value); // Diğer input türleri için değeri doğrudan atayın
          }
        });
      }
    })
    .catch(function (error) {
      console.error(error); // Hata yönetimi
    });
    
  //setTooltip();
});

$("#deleteAll").click(function () {
  swal
    .fire({
      title: "Emin misiniz?",
      text: "Tüm satırları silmek istediğinize emin misiniz?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Evet",
      cancelButtonText: "Hayır"
    })
    .then((result) => {
      if (result.isConfirmed) {
        $("#hstTable tbody tr").remove();
        addRow();
      }
    });
});

function setTooltip() {
  // `data-tooltip` özelliğine sahip tüm elemanlar için döngü
  $(".region").each(function () {
    // Elemanın kendi metnini al
    // console.log('selfText:', $(this).val());

    var selfText = $(this).val();
    // `title` özelliğini elemanın metniyle güncelle
    $(this).attr("data-tooltip", selfText);
  });
}


