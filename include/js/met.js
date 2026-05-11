function metTableRowNumberUpdate() {
    $("#metTable tbody tr").each(function (index) {
        var satirNo = index + 1;
        $(this).find('input[name="satirno[]"]').val(satirNo);
    });
}

function addNewRow(data = {}) {
    var vanadurum = createSelect({name:"vana_durum[]", required :"required", val: data.vana_durum || ""});
    var hortumbaglantidurum = createSelect({name:"hortum_baglanti_durum[]", required:"required", val: data.hortum_baglanti_durum || ""});
    var levhadurum = createSelect({name:"levha_durum[]", required :"required", val: data.levha_durum || ""});
    var pasdurum = createSelect({name:"pas_dur_met[]", required :"required", type:"2", val: data.pas_durum || ""});
    var kilitdurum = createSelect({name:"kilit_durum[]", required :"required", val: data.kilit_durum || ""});
    var hortumdurum = createSelect({name:"hortum_durum[]", required :"required", val: data.hortum_durum || ""});
    var nozuldurum = createSelect({name:"nozul_durum[]", required :"required", val: data.nozuldurum || ""});

    $("#metTable tbody").append(
        "<tr> " +
        '<td class="pl-2">' +
        '<button type="button" class="sil btn btn-sm btn-danger"> Sil</button>' +
        "</td>" +
        '<td class="app-item-number">' +
        '<input type="text" class="form-control" name="satirno[]" value="" readonly>' +
        "</td>" +
        '<td><input required type="text" class="form-control region" name="cinsi[]" value="' + (data.cinsi || "") + '">' +
        "</td>" +
        '<td><input required type="text" class="form-control region" name="bulundugu_kisim[]" value="' + (data.bulundugu_kisim || "") + '">' +
        "</td>" +
        '<td><input required type="text" class="form-control region" name="ozellikler[]" value="' + (data.ozellikler || "") + '">' +
        "</td>" +
        '<td><input required type="text" class="form-control region" name="control_date_closet[]" value="' + (data.control_date_closet || "") + '">' +
        "</td>" +
        '<td><input required type="text" class="form-control region" name="next_control_date_closet[]" value="' + (data.next_control_date_closet || "") + '">' +
        "</td>" +
        "<td>" + vanadurum + "</td>" +
        "<td>" + hortumbaglantidurum + "</td>" +
        "<td>" + levhadurum + "</td>" +
        "<td>" + pasdurum + "</td>" +
        "<td>" + kilitdurum + "</td>" +
        "<td>" + hortumdurum + "</td>" +
        "<td>" +
        '<input required type="text" class="form-control region" name="basinc_degeri[]" value="' + (data.basinc_degeri || "") + '">' +
        "</td>" +
        "<td>" + nozuldurum + "</td>" +
        "<td>" +
        '<input type="text" class="form-control region" name="aciklama[]" value="' + (data.aciklama || "") + '">' + 
        "</td>" +
        "</tr>"
    );
    $(".selectpicker").selectpicker("refresh");
    metTableRowNumberUpdate();
}

$("#addRow").click(function () {
    addNewRow();
});

// Excel Modal ve Drag-Drop İşlemleri
let selectedFile = null;

// Modal açıldığında içeriği temizle
$(document).on('show.bs.modal', '#excelModal', function () {
    selectedFile = null;
    $("#file-info").hide();
    $("#uploadExcel").val("");
    $("#filename-display").text("");
    $("#processExcel").prop("disabled", true);
});

$("#drop-area").on("click", function() {
    $("#uploadExcel").click();
});

$("#uploadExcel").on("change", function(e) {
    handleSelectedFile(e.target.files[0]);
});

$("#drop-area").on("dragenter dragover", function(e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).addClass("highlight");
});

$("#drop-area").on("dragleave drop", function(e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).removeClass("highlight");
});

$("#drop-area").on("drop", function(e) {
    let dt = e.originalEvent.dataTransfer;
    let file = dt.files[0];
    handleSelectedFile(file);
});

function handleSelectedFile(file) {
    if (file && (file.name.endsWith(".xlsx") || file.name.endsWith(".xls"))) {
        selectedFile = file;
        $("#filename-display").text(file.name);
        $("#file-info").show();
        $("#processExcel").prop("disabled", false);
    } else {
        alert("Lütfen geçerli bir Excel dosyası seçin.");
        $("#processExcel").prop("disabled", true);
    }
}

function formatExcelDate(dateVal) {
    if (!dateVal) return "";
    
    // Eğer zaten tarih formatındaysa (string)
    if (typeof dateVal === 'string' && dateVal.includes('.')) return dateVal;
    
    // Excel sayısal tarihini (S/N) Date objesine çevir
    let date;
    if (typeof dateVal === 'number') {
        // Excel 1899-12-30'u 0 kabul eder
        date = new Date(Math.round((dateVal - 25569) * 86400 * 1000));
    } else {
        date = new Date(dateVal);
    }

    if (isNaN(date.getTime())) return dateVal; // Hata durumunda ham veriyi dön

    let day = ("0" + date.getDate()).slice(-2);
    let month = ("0" + (date.getMonth() + 1)).slice(-2);
    let year = date.getFullYear();

    return day + "." + month + "." + year;
}

$("#processExcel").on("click", function() {
    if (!selectedFile) return;

    var $btn = $(this);
    var originalContent = $btn.html();
    $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> İşleniyor...');

    var reader = new FileReader();
    reader.onload = function (e) {
        var data = new Uint8Array(e.target.result);
        var workbook = XLSX.read(data, { type: 'array', cellDates: true, dateNF: 'dd.mm.yyyy' });
        var firstSheet = workbook.Sheets[workbook.SheetNames[0]];
        var jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1, raw: false });

        // Temizlik: Tablodaki mevcut boş satırları temizle
        var existingRows = $("#metTable tbody tr");
        if (existingRows.length === 1) {
            var firstRowValue = existingRows.find('input[name="cinsi[]"]').val();
            if (!firstRowValue || firstRowValue.trim() === "") {
                $("#metTable tbody").empty();
            }
        }

        // Skip header row
        for (var i = 1; i < jsonData.length; i++) {
            var row = jsonData[i];
            // Cihazın cinsi boş ise atla (row[1])
            if (!row || !row[1] || row[1].toString().trim() === "") continue;

            var rowData = {
                cinsi: row[1],
                bulundugu_kisim: row[2],
                ozellikler: row[3],
                control_date_closet: formatExcelDate(row[4]),
                next_control_date_closet: formatExcelDate(row[5]),
                vana_durum: row[6],
                hortum_baglanti_durum: row[7],
                levha_durum: row[8],
                pas_durum: row[9],
                kilit_durum: row[10],
                hortum_durum: row[11],
                basinc_degeri: row[12],
                nozul_durum: row[13],
                aciklama: row[14]
            };
            addNewRow(rowData);
        }
        
        // Modal'ı kapat
        $("#excelModal").modal("hide");
        
        // Kısa bir süre sonra zorla temizle (BS sürüm çatışmaları için)
        setTimeout(function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
        }, 300);
        
        selectedFile = null;
        $("#file-info").hide();
        $("#uploadExcel").val("");
        $btn.prop("disabled", false).html(originalContent);
        
        metTableRowNumberUpdate();
    };
    reader.readAsArrayBuffer(selectedFile);
});

//Satır silme Butonu
$("#metTable").on("click", ".sil", function (e) {
  e.preventDefault();
  $(this).closest("tr").remove();
  metTableRowNumberUpdate();
});

$("#addRowfile").click(function() {
    $("#metTablefile tbody").append(
        '<tr>' +
            '<td class="pl-2">' +
                '<button type="button" class="sil btn btn-sm btn-danger"> Sil</button>' +
            '</td>' +
            '<td><input required type="text" class="form-control region" name="attach_description[]" value=""></td>' +
            '<td><input required type="file" class="form-control btn-sm region" name="report_attach[]"></td>' +
        '</tr>'
    );
});

//Satır silme Butonu
$("#metTablefile").on("click", ".sil", function (e) {
  e.preventDefault();

  var removedRowIndex = $(this).closest("tr").index() + 1;
  $(this).closest("tr").remove();
});

// Sayfa yüklendiğinde numaraları güncelle
$(document).ready(function() {
    metTableRowNumberUpdate();
});