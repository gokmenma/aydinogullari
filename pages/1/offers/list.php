<?php
// require_once "App/Helper/date.php";
// require_once "App/Model/OfferModel.php";

use App\Helper\Date;
use App\Model\OfferModel;

$ois = @$_GET["id"];
$cid = @$_GET["cid"];
$sablonlari_goster = isset($_GET["sablon"]) ? true : false;
if($sablonlari_goster){
    $sayfa_basligi = "Şablon Teklifler";
}else{
    $sayfa_basligi = "Teklifleri Görüntüle";
};

$OfferModel = new OfferModel();


//$offers = $OfferModel->getOffersWithCompanyName($sablonlari_goster);


$offerCount = $OfferModel->getOfferCountWaitingAndDone();
$bekleyen_teklif_sayisi = $offerCount->bekleyen_teklif;
$tamamlanan_teklif_sayisi = $offerCount->tamamlanan_teklif;


if (@$_GET["st"] == "offercopy") {
    $creator = sesset("lid");
    $newoffernumber = "TK" . newNumber("offers");
    $ofcopy = $ac->prepare("INSERT INTO offers (offerNumber, cid, company_authors,
											   total_price,mycompany,
									   		   authors,reg_date,tax,creativer,
											   notes,currency,statu,
											   dollar,euro,payment_period,
											   offer_header,offer_header_content,
											   offer_footer,offer_footer_content,
											   description,
											   file, kdv,iskonto, subdescription,
											   buyTotal,saleTotal,amounttotal,
											   curDollar,curEuro,
											   DolarTotal ,EuroTotal ,TLTotal ) 
										SELECT ? , cid, company_authors,
											   total_price,mycompany,
											   authors,reg_date,tax,?,
											   notes,currency,statu,
											   dollar,euro,payment_period,
											   offer_header,offer_header_content,
											   offer_footer,offer_footer_content,
											   description,
											   file, kdv, iskonto, subdescription,
											   buyTotal,saleTotal,amounttotal,
											   curDollar,curEuro,
											   DolarTotal ,EuroTotal ,TLTotal
						   FROM offers	WHERE id = ?;");
    $ofcopy->execute(array($newoffernumber, $creator, $ois));
    $lastid = $ac->lastInsertId();

    $offmat = $ac->prepare("SELECT * FROM offermatters WHERE oid = ?");
    $offmat->execute(array($ois));

    $items = $offmat->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $item) {
        $insq = $ac->prepare("INSERT INTO offermatters SET xid = ?,oid = ?,stokKodu = ? , 
														   title = ? ,unit = ? ,amount = ? , 
														   buyprice = ? ,buycur = ?,saleprice = ? ,
														   salecur = ? ,total_price = ?");

        $insq->execute(
            array(
                $item["xid"],
                $lastid,
                $item["stokKodu"],
                $item["title"],
                $item["unit"],
                $item["amount"],
                $item["buyprice"],
                $item["buycur"],
                $item["saleprice"],
                $item["salecur"],
                $item["total_price"]
            )
        );
    }
}

///
if (@$_GET["st"] == "success-mail") {
    showAlert("success", "Mail başarı ile gönderildi!");
}

?>

<style>
    .responsive {
        overflow: auto;
    }
</style>
<!-- <link rel="stylesheet" href="src/plugins/bootstrap-select/dist/css/bootstrap-select.min.css"> -->

<div class="content pd-20 bg-white border-radius-16 box-shadow mb-10">
    <div class="clearfix mb-10">

        <!-- Özet bilgiler -->
        <div class="row">

            <div class="col-lg-6 col-md-6 col-sm-12 mb-10">
                <div class="sum-customer pd-20 box-shadow border-radius-5 height-100-p">
                    <div class="project-info">
                        <div class="project-info-left">
                            <div class="icon box-shadow bg-warning text-white">
                                <i class="fa fa-wrench"></i>
                            </div>
                        </div>
                        <div class="project-info-right">
                            <span class="no text-blue weight-500 font-24">
                                <?php echo $bekleyen_teklif_sayisi; ?>
                            </span>
                            <p>
                                <a target="_blank" class="weight-400 font-18" href="">Bekleyen Teklif Sayısı:</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 mb-10">
                <div class="sum-customer pd-20 box-shadow border-radius-5 height-100-p">
                    <div class="project-info clearfix">
                        <div class="project-info-left">
                            <div class="icon box-shadow bg-green text-white">
                                <i class="fa fa-check"></i>
                            </div>
                        </div>
                        <div class="project-info-right">

                            <span class="no text-blue weight-500 font-24">
                                <?php echo $tamamlanan_teklif_sayisi; ?>

                            </span>
                            <p class="weight-400 font-18">
                                <a target="_blank" href="">
                                    Tamamlanan Teklif Sayısı
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
<div class="content pd-20 bg-white border-radius-16 box-shadow mb-30">
    <div class="clearfix mb-30">
        <div class="pull-left">
            <h5 class="text-blue"><?php echo $sayfa_basligi; ?></h5>

        </div>

        <div class="float-right gap-2">

            <!-- Excele Aktar -->
            <?php if (permtrue("data_export_offers")) { ?>
                <a href="#" class="btn btn-secondary mr-1" id="exportExcel"><i class="fa fa-file-excel-o"></i>
                    Excele Aktar</a>
                <!-- Excele Aktar -->
            <?php } ?>

            <?php if (permtrue("offerAdd")) { ?>
                <a href="index.php?p=offers/offer-manage"><button style="float:right;" type="button"
                        class="btn btn-primary">
                        <i class="fa fa-plus pr-1"></i>Yeni Teklif Oluştur</button></a> <br><br>
            <?php } ?>
        </div>
    </div>

    <?php



    ?>
    <div class="content pd-20 bg-light border-radius-16 box-shadow mb-20">
        <div class="d-flex align-items-center justify-content-between mb-10">
            <h6 class="text-blue m-0">Filtreler</h6>
            <button type="button" id="filtersToggle" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-chevron-down mr-1"></i> Göster
            </button>
        </div>
        <div id="filtersCollapse" style="display:none;" class="filters-form">
        <div class="row">
            <div class="col-md-3 mb-10">
                <label class="form-label">Teklif No</label>
                <input type="text" id="filter_offer_no" class="form-control" placeholder="Teklif numarasını yazınız.">
            </div>
            <div class="col-md-3 mb-10">
                <label class="form-label">Firma</label>
                <select id="filter_company" class="form-control select-picker" data-live-search="true" title="Firma seçin veya yazın"></select>
            </div>
            <div class="col-md-3 mb-10">
                <label class="form-label">Ürün / Konu</label>
                <select id="filter_subject" class="form-control select-picker" data-live-search="true" title="Konu seçin veya yazın"></select>
            </div>
            <div class="col-md-3 mb-10">
                <label class="form-label">Durum</label>
                <select id="filter_status" class="form-control select-picker" data-live-search="true" title="Durum seçin veya yazın"></select>
            </div>
            <div class="col-md-3 mb-10">
                <label class="form-label">Başlangıç Tarihi</label>
                <input type="text" id="filter_date_start" class="form-control date-picker" placeholder="01.11.2024">
            </div>
            <div class="col-md-3 mb-10">
                <label class="form-label">Bitiş Tarihi</label>
                <input type="text" id="filter_date_end" class="form-control date-picker" placeholder="01.01.2025">
            </div>
            <div class="col-md-3 mb-10">
                <label class="form-label">Toplam (min)</label>
                <input type="number" step="0.01" id="filter_total_min" class="form-control" placeholder="Başlangıç toplamını yazınız.">
            </div>
            <div class="col-md-3 mb-10">
                <label class="form-label">Toplam (max)</label>
                <input type="number" step="0.01" id="filter_total_max" class="form-control" placeholder="Bitiş toplamını yazınız.">
            </div>
            <div class="col-md-3 mb-10">
                <label class="form-label">Para Birimi</label>
                <select id="filter_currency" class="form-control select-picker" data-live-search="true" title="Para birimi seçin veya yazın">
                    <option value="">Tümü</option>
                </select>
            </div>
            <div class="col-md-3 mb-10">
                <label class="form-label">Personel / Temsilci</label>
                <select id="filter_creator" class="form-control select-picker" data-live-search="true" title="Personel seçin veya yazın"></select>
            </div>
            <div class="col-md-3 mb-10">
                <label class="form-label">Ödeme Vadesi</label>
                <select id="filter_payment_period" class="form-control select-picker" data-live-search="true" title="Vade seçin veya yazın"></select>
            </div>
        </div>
        <div class="text-right mt-10">
            <button type="button" id="applyFilters" class="btn btn-success">ARA</button>
            <button type="button" id="clearFilters" class="btn btn-outline-secondary ml-1">Temizle</button>
        </div>
        </div>
    </div>
    <div class="responsive">

        <table id="offerTable" class="data-table table-hover table-bordered">
            <thead>
                <tr>

                    <th>Sıra No</th>
                    <th class="w-10">Oluşturma Tarihi</th>
                    <th>Teklif No</th>
                    <th>Müşteri</th>
                    <th>Toplam Tl Tutar</th>
                    <th>Durum</th>
                    <th>Onay Tarihi</th>
                    <th>Konusu</th>
                    <th>Ödeme Vadesi</th>
                    <th>Teklif Veren</th>
                    <th class="no-export" style="width: 7%;">İşlem</th>

                </tr>
            </thead>
            <tbody>
             
<tr class="odd data-row text-center">
    <td colspan="11">Veriler Yükleniyor...</td>
</tr>

            </tbody>
           
        </table>

    </div>
</div>
<style>
    /* DataTables'ın sabit genişliklerini ezmek için */
table.dataTable {
    width: 100% !important;
}

.dataTables_length{
    margin-left: 10px;
}



</style>
<script src="pages/1/offers/offer.js"></script>
<script src="src/plugins/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
<script src="src/plugins/bootstrap-select/dist/js/i18n/defaults-tr_TR.min.js"></script>

<!-- ÖNCEKİ `data-table.js` YERİNE DAHA DETAYLI BİR SCRIPT -->
<script>
$(document).ready(function() {
    $('.selectpicker').selectpicker();
    $.getJSON('App/api/get-offer-filter-options.php?sablon=<?php echo $sablonlari_goster ? 1 : 0; ?>', function(resp){
        function fillSelect(id, arr){
            var $s = $(id);
            $s.empty();
            $s.append('<option value=""></option>');
            if($s.attr('id') === 'filter_currency'){
                $s.append('<option value="">Tümü</option>');
            }
            (arr || []).forEach(function(v){
                if(v && v.trim() !== ''){
                    var esc = $('<div>').text(v).html();
                    $s.append('<option value="'+esc+'">'+esc+'</option>');
                }
            });
            $s.selectpicker('refresh');
            enableManualEntry('#'+$s.attr('id'));
        }
        fillSelect('#filter_company', resp.company_name);
        fillSelect('#filter_subject', resp.offer_subject);
        fillSelect('#filter_status', resp.durum);
        fillSelect('#filter_creator', resp.creator_name);
        fillSelect('#filter_payment_period', resp.payment_period);
        fillSelect('#filter_currency', resp.currency);
    });

    function enableManualEntry(sel){
        var $s = $(sel);
        var $wrap = $s.next('.bootstrap-select');
        $wrap.off('keydown.manual').on('keydown.manual', '.bs-searchbox input', function(e){
            if(e.key === 'Enter'){
                e.preventDefault();
                var q = $(this).val();
                if(!q) return;
                var exists = false;
                $s.find('option').each(function(){ if($(this).text() === q){ exists = true; } });
                var val = '__manual__:'+q;
                if(!exists){
                    $s.append('<option data-manual="1" value="'+val+'">'+q+'</option>');
                } else {
                    // if exists, set its value to its text
                    val = q;
                }
                $s.selectpicker('val', val);
            }
        });
    }
      
    // DataTable örneğini bir değişkende saklayın, böylece ona daha sonra erişebiliriz.
    var offerTable = $('#offerTable').DataTable({
        retrieve: true,
        "processing": false,
        "serverSide": true,
        "ajax": {
            "url": "App/api/get-offers.php?sablon=<?php echo $sablonlari_goster ? 1 : 0; ?>", 
            "type": "POST",
            "data": function(d){
                function sv(id){
                    var v = $(id).val();
                    if(typeof v === 'string' && v.indexOf('__manual__:') === 0){ v = v.replace('__manual__:', ''); }
                    return v;
                }
                d.filters = {
                    offer_no: $('#filter_offer_no').val(),
                    company: sv('#filter_company'),
                    subject: sv('#filter_subject'),
                    status: sv('#filter_status'),
                    date_start: $('#filter_date_start').val(),
                    date_end: $('#filter_date_end').val(),
                    total_min: $('#filter_total_min').val(),
                    total_max: $('#filter_total_max').val(),
                    currency: sv('#filter_currency'),
                    creator: sv('#filter_creator'),
                    payment_period: sv('#filter_payment_period')
                };
            },
            "error": function (xhr, error, thrown) {
                console.log("DataTables AJAX Hatası:", xhr, error, thrown);
                alert("Veriler yüklenirken bir hata oluştu. Sunucu yanıtını kontrol edin.");
            }
        },
        "columns": [
            { "data": "sira_no", "name": "sira_no", "orderable": false, "searchable": false },
            { "data": "islem_tarihi", "name": "created_at" }, 
            { "data": "teklif_no", "name": "offerNumber" },
            { "data": "musteri", "name": "company" },
            { "data": "toplam_tutar", "name": "total_price" },
            { "data": "durum", "name": "durum" },
            { "data": "onay_tarihi", "name": "onay_tarihi" },
            { "data": "konusu", "name": "offer_subject" },
            { "data": "odeme_vadesi", "name": "payment_period" },
            { "data": "teklif_veren", "name": "creator_name" },
            { "data": "islem", "orderable": false, "searchable": false, "className": "text-right" }
        ],
        "order": [[ 1, "desc" ]],
        "language": {
            "url": "include/js/tr.json"
        },
        initComplete: function () {
            var api = this.api();
            var tableId = api.table().node().id;
            // Arama satırını <thead> içine ekle
            $("#" + tableId + " thead").append('<tr class="search-input-row"></tr>');

            api.columns().every(function (index) { // Sütun index'ini al
                let column = this;
                let header = $(column.header());
                let title = header.text();

                // İşlem ve checkbox olmayan sütunlar için input oluştur
                if (header.find('input[type="checkbox"]').length === 0 && column.visible() && title && title.trim() !== 'İşlem') {
                    
                    let input = $('<input type="text" class="form-control form-control-sm" placeholder="' + title + '" autocomplete="off">')
                        .appendTo($('<th class="search"></th>').appendTo("#" + tableId + " .search-input-row"))
                        .on('keyup change clear', function () {
                            // === ANAHTAR DEĞİŞİKLİK BURADA ===
                            // Eğer sütunun arama değeri bu input'un değeriyle aynı değilse,
                            // yeni değeri ata ve tabloyu yeniden çiz (sunucuya yeni istek gönder)
                            if (column.search() !== this.value) {
                                column.search(this.value).draw();
                            }
                        });
                } else {
                    // Diğer sütunlar için boş bir <th> ekle
                    $("#" + tableId + " .search-input-row").append('<th></th>');
                }
                   // İkinci <thead> satırını sabitlemek için CSS ekleyin
  
            });
        }
    });
  
});



    // Excel'e aktar butonu için
    $('#exportExcel').on('click', function(e){
        e.preventDefault();
        var form = $('<form>', { action: 'App/api/export-offers.php?sablon=<?php echo $sablonlari_goster ? 1 : 0; ?>', method: 'POST' });
        function sv(id){
            var v = $(id).val();
            if(typeof v === 'string' && v.indexOf('__manual__:') === 0){ v = v.replace('__manual__:', ''); }
            return v;
        }
        var filters = {
            offer_no: $('#filter_offer_no').val(),
            company: sv('#filter_company'),
            subject: sv('#filter_subject'),
            status: sv('#filter_status'),
            date_start: $('#filter_date_start').val(),
            date_end: $('#filter_date_end').val(),
            total_min: $('#filter_total_min').val(),
            total_max: $('#filter_total_max').val(),
            currency: sv('#filter_currency'),
            creator: sv('#filter_creator'),
            payment_period: sv('#filter_payment_period')
        };
        form.append($('<input>', { type: 'hidden', name: 'filters', value: JSON.stringify(filters) }));
        $('body').append(form);
        form.submit();
        setTimeout(function(){ form.remove(); }, 1000);
    });




    $(document).on('click', '.teklif-sil', function() {
    
        var teklif_id = $(this).data('id');
        swal.fire({
            title: "Teklifi Sil",
            text: "Bu teklifi silmek istediğinizden emin misiniz?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Evet, Sil!",
            cancelButtonText: "İptal"
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX ile teklif silme işlemi
                $.ajax({
                    url: 'App/api/offer.php',
                    type: 'POST',
                    data: { id: teklif_id , action: 'deleteOffer' },
                    success: function(response) {
                        response = JSON.parse(response); // JSON verisini ayrıştır
                        
                        if (response.status === 'success') {
                            swal.fire("Silindi!", response.message, "success");
                            // Tabloyu yeniden yükle
                            $('#offerTable').DataTable().ajax.reload();
                        } else {
                            swal.fire("Hata!", response.message, "error");
                        }
                    },
                    error: function() {
                        swal.fire("Hata!", "Teklif silinirken bir hata oluştu.", "error");
                    }
                });
            }
        });
    });
 
    // Filtreleri uygula
    $(document).on('click', '#applyFilters', function() {
        $('#offerTable').DataTable().ajax.reload();
    });

    // Filtreleri temizle
    $(document).on('click', '#clearFilters', function() {
        $('#filter_offer_no, #filter_date_start, #filter_date_end, #filter_total_min, #filter_total_max')
            .val('');
        $('#filter_company, #filter_subject, #filter_status, #filter_currency, #filter_creator, #filter_payment_period')
            .find('option[data-manual="1"]').remove();
        $('#filter_company, #filter_subject, #filter_status, #filter_currency, #filter_creator, #filter_payment_period')
            .selectpicker('val', '')
            .selectpicker('render');
        $('#offerTable').DataTable().ajax.reload();
    });

    // Accordion toggle
    $(document).on('click', '#filtersToggle', function(){
        var $c = $('#filtersCollapse');
        if($c.is(':visible')){
            $c.slideUp(150);
            $('#filtersToggle').html('<i class="fa fa-chevron-down mr-1"></i> Göster');
        }else{
            $c.slideDown(150);
            $('#filtersToggle').html('<i class="fa fa-chevron-up mr-1"></i> Gizle');
        }
    });

    // Enter ile arama
    $(document).on('keydown', '#filtersCollapse input, #filtersCollapse select', function(e){
        if(e.key === 'Enter'){
            e.preventDefault();
            $('#applyFilters').trigger('click');
        }
    });
    $(document).on('input keyup change blur', '.date-picker', function(){
        var v = (this.value || '').replace(/[^0-9]/g,'');
        if(v.length > 2) v = v.slice(0,2) + '.' + v.slice(2);
        if(v.length > 5) v = v.slice(0,5) + '.' + v.slice(5);
        this.value = v.slice(0,10);
    });
</script>
<!-- <script src="include/js/data-table.js"></script> -->
