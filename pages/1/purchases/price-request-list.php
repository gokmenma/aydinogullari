<?php

use App\Helper\Helper;

$pids = @$_GET['id'];

if ((@$_GET["st"] ?? "") == "success-mail") {
    showAlert("success", "Mail başarı ile gönderildi!");
} else if ((@$_GET["st"] ?? "") == "unsuccessful") {
    showAlert("alert", "Mail gönderilirken bir hata oluştu");
}

?>
<div class="content pd-20 bg-white border-radius-8 box-shadow mb-30">
    <div class="clearfix mb-20">
        <div class="pull-left">
            <h5 class="text-blue">Fiyat Talepleri</h5>
        </div>
        <div class="float-right">
            <a href="index.php?p=purchases/price-request-manage"><button type="button" class="btn btn-primary btn-sm"><i
                        class="fa fa-plus"></i> Yeni Fiyat Talebi</button></a>
        </div>
    </div>

    <table id="myTable" class="data-table table-hover table-bordered table-responsive">
        <thead>
            <tr>
                <th scope="col">Talep No</th>
                <th>Firma Adı</th>
                <th>Kayıt Tarihi</th>
                <th>Termin Tarihi</th>
                <th>Toplam Fiyat</th>
                <th>Durum </th>
                <th>Oluşturan</th>
                <th class="text-nowrap">İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $where = "";
            if (!permtrue('tum_fiyat_taleplerini_gor')) {
                $where = " AND creator = " . sesset('id');
            }
            
            // Sadece FT ile başlayan ve type=2 olanları getir
            $query = $ac->prepare("SELECT * FROM purchases WHERE type = 2 AND siparisNo LIKE 'FT%' " . $where . " ORDER BY id DESC");
            $query->execute();

            while ($purc = $query->fetch(PDO::FETCH_ASSOC)) {
                $customer_name = getCustomerName($purc['companyID']);
                $pid = $purc['id'];
            ?>
                <tr>
                    <td><?php echo $purc['siparisNo']; ?></td>
                    <td data-tooltip="<?php echo $customer_name; ?>">
                        <?php echo shorted($customer_name, 40); ?>
                    </td>
                    <td><?php echo $purc['create_time'] ?></td>
                    <td><?php echo $purc['deadline'] ?></td>
                    <td><?php echo number_format((float)($purc['altToplam'] ?? 0), 2, ',', '.') . ' ₺'; ?></td>
                    <td><?php echo Helper::getStateBadge($purc['state']); ?></td>
                    <td>
                        <?php
                        $user = getUserName($purc['creator']);
                        echo shorted($user, 20);
                        ?>
                    </td>
                    <td style="width:1%; white-space: nowrap;">
                        <button type="button" class="btn btn-sm btn-outline-primary view-detail" 
                                data-id="<?php echo $pid; ?>" data-tooltip="Görüntüle">
                            <i class="fa fa-eye"></i>
                        </button>
                        
                        <a href="index.php?p=purchases/price-request-manage&id=<?php echo $pid; ?>"
                           class="btn btn-sm btn-outline-info" data-tooltip="Düzenle"><i class="fa fa-pencil"></i></a>

                        <?php if (permtrue("tum_fiyat_taleplerini_gor")) { 
                            $deleteLink = "deleteRecord('" . $purc['siparisNo'] . " nolu fiyat talebini silmek istediğinize emin misiniz?'," . $purc['id'] . ",'purchases')";
                        ?>
                            <button type="button" class="btn btn-sm btn-danger"
                                    data-tooltip="Sil" 
                                    onClick="<?php echo $deleteLink ?>">
                                <i class="fa fa-trash"></i></button>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Görüntüleme Modalı -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 95%;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-white border-bottom py-3 px-4">
                <h5 class="modal-title font-weight-bold text-dark" style="font-size: 1.1rem;">
                    <i class="fa fa-file-text-o mr-2 text-primary"></i> Fiyat Talebi Detayı
                </h5>
                <button type="button" class="close" data-dismiss="modal" onclick="$('#detailModal').modal('hide')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="detailBody" style="background: #fff; min-height: 400px; overflow-x: hidden;">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Yükleniyor...</span>
                    </div>
                    <p class="mt-2 text-muted">Veriler hazırlanıyor...</p>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-3 px-4">
                <div class="ml-auto d-flex gap-2">
                    <button type="button" class="btn btn-outline-info px-4 font-weight-bold" id="btnPrintModal">
                        <i class="fa fa-print mr-1"></i> Yazdır
                    </button>
                    <button type="button" class="btn btn-danger px-4 font-weight-bold shadow-sm" id="btnPdfModal">
                        <i class="fa fa-file-pdf-o mr-1"></i> PDF Olarak İndir
                    </button>
                    <button type="button" class="btn btn-light px-4 font-weight-bold border ml-2" data-dismiss="modal" onclick="$('#detailModal').modal('hide')">Kapat</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="include/js/data-table.js"></script>
<script>
$(document).ready(function() {
    $('.view-detail').on('click', function() {
        var id = $(this).data('id');
        $('#detailModal').modal('show');
        $('#detailBody').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Yükleniyor...</p></div>');
        
        $.ajax({
            url: 'pages/1/purchases/price-request-detail-modal.php',
            type: 'GET',
            data: { id: id },
            success: function(response) {
                $('#detailBody').html(response);
            },
            error: function() {
                $('#detailBody').html('<div class="alert alert-danger m-3">Veriler yüklenirken bir hata oluştu!</div>');
            }
        });
    });

    $('#btnPrintModal').on('click', function() {
        var printContents = document.getElementById('detailBody').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    });

    $('#btnPdfModal').on('click', function() {
        var id = $(this).data('current-id');
        // Gelecekte PDF route'u buraya eklenebilir
        window.open('pages/1/purchases/price-request-print.php?id=' + activeDetailId + '&pdf=1', '_blank');
    });
});

var activeDetailId = null;
$(document).on('click', '.view-detail', function() {
    activeDetailId = $(this).data('id');
});
</script>
