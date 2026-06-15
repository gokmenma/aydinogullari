let apiUrl = "pages/1/kesif/api.php";

$(document).ready(function () {
  // Excel aktarma butonu
  $("#exportExcel").on("click", function () {
    window.location.href = "pages/1/kesif/export.php";
  });

  // Yeni keşif ekleme modalını aç
  $("#kesifModal").on("show.bs.modal", function () {
    if ($("#kesif_id").val() === "") {
      $("#kesifModalLabel").text("Yeni Keşif Ekle");
      $("#kesifForm")[0].reset();
    }
  });

  // Düzenle butonuna tıklandığında
  $(document).on("click", ".edit-btn", function () {
    var kesif_id = $(this).data("id");
    $("#kesifModalLabel").text("Keşifi Düzenle");

    // AJAX ile keşif verilerini getir
    $.ajax({
      url: apiUrl,
      type: "GET",
      data: {
        action: "get",
        id: kesif_id,
      },
      dataType: "json",
      success: function (data) {
        if (data.success) {
          var kesif = data.data;
          $("#kesif_id").val(kesif.id);
          // Tarihi dd.mm.yyyy H:m:s formatına çevir
          $("#kesif_tarihi").val(formatDateTime(kesif.kesif_tarihi));
          $("#gidecek_kisi").val(kesif.gidecek_kisi || "");
          $("#firma").val(kesif.firma);
          $("#yapilacak_is").val(kesif.yapilacak_is);
          $("#konum").val(kesif.konum);
          $("#formun_bulundugu_kisi").val(kesif.formun_bulundugu_kisi || "");
          $("#durum").val(kesif.durum || "bekliyor");
          $("#kesif_sonu_notu").val(kesif.kesif_sonu_notu || "");

          // Mevcut görselleri göster
          $("#current_gorseller").empty();
          if (kesif.gorseller) {
            var gorseller = JSON.parse(kesif.gorseller);
            gorseller.forEach(function (src) {
              $("#current_gorseller").append(`
                <div class="gorsel-item position-relative d-inline-block m-1" style="width:90px; height:90px;">
                    <img src="${src}" class="img-thumbnail" style="width:90px; height:90px; object-fit:cover; border-radius:8px; cursor:pointer;" onclick="window.open('${src}','_blank')">
                    <button type="button" class="btn-delete-gorsel" data-kesif-id="${kesif.id}" data-src="${src}" title="Görseli Sil">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
              `);
            });
          }
        }
      },
      error: function () {
        Swal.fire({
          icon: "error",
          title: "Hata!",
          text: "Keşif yüklenirken bir hata oluştu!",
          confirmButtonText: "Tamam",
          confirmButtonColor: "#d33",
        });
      },
    });
  });

  // Detayları Görüntüle butonuna tıklandığında
  $(document).on("click", ".view-btn", function (e) {
    e.preventDefault();
    var kesif_id = $(this).data("id");
    console.log("View button clicked, ID:", kesif_id);

    // AJAX ile keşif detaylarını getir
    $.ajax({
      url: apiUrl,
      type: "GET",
      data: {
        action: "get",
        id: kesif_id,
      },
      dataType: "json",
      success: function (data) {
        console.log("API Response:", data);
        if (data.success) {
          var kesif = data.data;

          // Durum badge
          var durum_badge = "";
          if (kesif.durum == "bekliyor") {
            durum_badge = '<span class="badge badge-warning">Bekliyor</span>';
          } else if (kesif.durum == "iptal_edildi") {
            durum_badge =
              '<span class="badge badge-danger">İptal Edildi</span>';
          } else if (kesif.durum == "kesif_tamamlandi") {
            durum_badge =
              '<span class="badge badge-info" style="background-color: #007bff;">Keşif Tamamlandı</span>';
          } else if (kesif.durum == "teklif_hazirlandi") {
            durum_badge =
              '<span class="badge badge-primary" style="background-color: #6f42c1;">Teklif Hazırlandı</span>';
          } else if (kesif.durum == "teklif_gonderildi") {
            durum_badge =
              '<span class="badge badge-success">Teklif Gönderildi</span>';
          } else {
            durum_badge =
              '<span class="badge badge-secondary">' + kesif.durum + "</span>";
          }

          // Detaylar modalını doldur
          $("#detail_kesif_tarihi").text(kesif.kesif_tarihi);
          $("#detail_gidecek_kisi").text(kesif.gidecek_kisi || "-");
          $("#detail_firma").text(kesif.firma);
          $("#detail_konum").text(kesif.konum);
          $("#detail_durum").html(durum_badge);
          $("#detail_form_kimde").text(kesif.formun_bulundugu_kisi || "-");
          $("#detail_yapilacak_is").text(kesif.yapilacak_is);
          $("#detail_kesif_sonu_notu").text(kesif.kesif_sonu_notu || "-");
          $("#detail_kayit_tarihi").text(kesif.kayit_tarihi);
          $("#detail_kayit_yapan").text(kesif.kullanici_adi || "-");

          // Görselleri göster
          $("#detail_gorseller").empty();
          if (kesif.gorseller) {
            var gorseller = JSON.parse(kesif.gorseller);
            gorseller.forEach(function (src) {
              $("#detail_gorseller").append(`
                                <a href="${src}" target="_blank">
                                    <img src="${src}" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                                </a>
                            `);
            });
          } else {
            $("#detail_gorseller").html(
              '<p class="text-muted">Görsel bulunmuyor.</p>',
            );
          }

          // Güncelleme bilgileri
          if (kesif.guncelleme_tarihi) {
            $("#detail_guncelleme_tarihi").text(kesif.guncelleme_tarihi);
            $("#detail_guncelleyen_kullanici").text(
              kesif.guncelleyen_adi || "-",
            );
          } else {
            $("#detail_guncelleme_tarihi").text("Güncellenmemiş");
            $("#detail_guncelleyen_kullanici").text("-");
          }

          // Modalı aç
          $("#detaylarModal").modal("show");
        } else {
          console.error("API Error:", data.message);
          Swal.fire({
            icon: "error",
            title: "Hata!",
            text: data.message,
            confirmButtonText: "Tamam",
            confirmButtonColor: "#d33",
          });
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error, xhr);
        Swal.fire({
          icon: "error",
          title: "Hata!",
          text: "Keşif detayları yüklenirken bir hata oluştu!",
          confirmButtonText: "Tamam",
          confirmButtonColor: "#d33",
        });
      },
    });
  });

  // PDF görüntüleme doğrudan link ile yeni sekmede açılıyor

  // Adresi Google Haritalar'da görüntüle (QR olmadan)
  $(document).on("click", ".map-view-btn", function (e) {
    e.preventDefault();
    var kesif_id = $(this).data("id");
    if (!kesif_id) return;

    $.ajax({
      url: apiUrl,
      type: "GET",
      data: { action: "get", id: kesif_id },
      dataType: "json",
      success: function (data) {
        if (data.success) {
          var kesif = data.data;
          var address = kesif.konum || "";
          var mapsUrl =
            "https://www.google.com/maps/search/?api=1&query=" +
            encodeURIComponent(address);
          var embedUrl =
            "https://maps.google.com/maps?q=" +
            encodeURIComponent(address) +
            "&output=embed";
          $("#mapsLink").attr("href", mapsUrl);
          $("#mapFrame").attr("src", embedUrl);
          $("#mapModal").modal("show");
        } else {
          Swal.fire({
            icon: "error",
            title: "Hata!",
            text: data.message,
            confirmButtonText: "Tamam",
            confirmButtonColor: "#d33",
          });
        }
      },
      error: function () {
        Swal.fire({
          icon: "error",
          title: "Hata!",
          text: "Adres bilgisi alınırken bir hata oluştu!",
          confirmButtonText: "Tamam",
          confirmButtonColor: "#d33",
        });
      },
    });
  });

  // Görsel silme butonu
  $(document).on("click", ".btn-delete-gorsel", function (e) {
    e.preventDefault();
    e.stopPropagation();

    var btn = $(this);
    var kesifId = btn.data("kesif-id");
    var src = btn.data("src");

    Swal.fire({
      title: "Görseli Sil",
      text: "Bu görseli silmek istediğinize emin misiniz?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Evet, Sil!",
      cancelButtonText: "İptal",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: apiUrl,
          type: "POST",
          data: {
            action: "delete_image",
            id: kesifId,
            image_path: src,
          },
          dataType: "json",
          success: function (data) {
            if (data.success) {
              btn.closest(".gorsel-item").fadeOut(300, function () {
                $(this).remove();
              });
              Swal.fire({
                icon: "success",
                title: "Silindi!",
                text: "Görsel başarıyla silindi.",
                timer: 1500,
                showConfirmButton: false,
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Hata!",
                text: data.message,
              });
            }
          },
          error: function () {
            Swal.fire({
              icon: "error",
              title: "Hata!",
              text: "Görsel silinirken bir hata oluştu!",
            });
          },
        });
      }
    });
  });

  // Formu gönder
  $("#kesifForm").on("submit", function (e) {
    e.preventDefault();

    var formData = new FormData(this);
    var action = $("#kesif_id").val() ? "update" : "create";
    formData.append("action", action);

    var btn = $("#btnSaveKesif");
    var btnText = btn.find(".btn-text");
    var originalText = btnText.text();

    $.ajax({
      url: apiUrl,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      beforeSend: function () {
        btn.prop("disabled", true);
        btnText.html(
          '<i class="fa fa-spinner fa-spin mr-2"></i> Kaydediliyor...',
        );
      },
      success: function (data) {
        if (data.success) {
          Swal.fire({
            icon: "success",
            title: "Başarılı!",
            text: data.message,
            confirmButtonText: "Tamam",
            confirmButtonColor: "#3085d6",
          }).then((result) => {
            if (result.isConfirmed) {
              $("#kesifModal").modal("hide");
              location.reload();
            }
          });
        } else {
          btn.prop("disabled", false);
          btnText.text(originalText);
          Swal.fire({
            icon: "error",
            title: "Hata!",
            text: data.message,
            confirmButtonText: "Tamam",
            confirmButtonColor: "#d33",
          });
        }
      },
      error: function () {
        btn.prop("disabled", false);
        btnText.text(originalText);
        Swal.fire({
          icon: "error",
          title: "Hata!",
          text: "İşlem sırasında bir hata oluştu!",
          confirmButtonText: "Tamam",
          confirmButtonColor: "#d33",
        });
      },
    });
  });

  // Sil butonuna tıklandığında
  $(document).on("click", ".delete-btn", function () {
    var kesif_id = $(this).data("id");

    Swal.fire({
      title: "Emin misiniz?",
      text: "Bu keşifi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Evet, Sil!",
      cancelButtonText: "İptal",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: apiUrl,
          type: "POST",
          data: {
            action: "delete",
            id: kesif_id,
          },
          dataType: "json",
          success: function (data) {
            if (data.success) {
              Swal.fire({
                icon: "success",
                title: "Silindi!",
                text: data.message,
                confirmButtonText: "Tamam",
                confirmButtonColor: "#3085d6",
              }).then((result) => {
                if (result.isConfirmed) {
                  location.reload();
                }
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Hata!",
                text: data.message,
                confirmButtonText: "Tamam",
                confirmButtonColor: "#d33",
              });
            }
          },
          error: function () {
            Swal.fire({
              icon: "error",
              title: "Hata!",
              text: "Silme işlemi sırasında bir hata oluştu!",
              confirmButtonText: "Tamam",
              confirmButtonColor: "#d33",
            });
          },
        });
      }
    });
  });

  function formatDateTime(dateStr) {
    const date = new Date(dateStr.replace(" ", "T"));
    const pad = (n) => (n < 10 ? "0" + n : n);
    return (
      pad(date.getDate()) +
      "." +
      pad(date.getMonth() + 1) +
      "." +
      date.getFullYear() +
      " " +
      pad(date.getHours()) +
      ":" +
      pad(date.getMinutes()) +
      ":" +
      pad(date.getSeconds())
    );
  }
});
