<?php

$cid = @$_GET["cid"]; //customer id
$sid = @$_GET["id"];
permcontrol("serviceView");


use App\Helper\Helper;
use App\Model\UnitsModel;
use App\Model\ServiceModel;

$Services = new ServiceModel();
$Units = new UnitsModel();

$bekleyen_id = $Units->getUnitId("Bekliyor")->id;
$calisilan_id = $Units->getUnitId("Çalışıyor")->id;
$tamamlanan_id = $Units->getUnitId("Tamamlandı")->id;
$iptal_id = $Units->getUnitId("İptal Edildi")->id;

$bekleyen_servis_sayisi = $Services->getServiceCount($bekleyen_id)->count;
$calisilan_servis_sayisi = $Services->getServiceCount($calisilan_id)->count;
$tamamlanan_servis_sayisi = $Services->getServiceCount($tamamlanan_id)->count;
$iptal_servis_sayisi = $Services->getServiceCount($iptal_id)->count;

// Yetki kontrollerini döngü dışında yap
$canEdit = permtrue("serviceEdit");
$canDel = permtrue("serviceDel");
$canAccountingReceipt = permtrue("muhasebe_teslim_alma_yetkisi");

try {
    $ac->exec("CREATE TABLE IF NOT EXISTS service_accounting_receipt_logs (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        service_id INT UNSIGNED NOT NULL,
        action VARCHAR(20) NOT NULL,
        action_by INT UNSIGNED NOT NULL,
        action_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_service_id (service_id),
        KEY idx_action_at (action_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (PDOException $e) {
    // Tablo oluşturulamasa da liste ekranı çalışmaya devam etsin.
}

// Optimize edilmiş tek sorgu ile tüm verileri çek
if ($cid) {
    $query = $ac->prepare("
        SELECT p.*, 
               c.company as company_name, 
               r.title as region_name, 
               s.title as service_title, 
               u.username as creator_username,
             uu.username as updater_username,
             ar.action as accounting_action,
             ar.action_at as accounting_action_at,
             au.username as accounting_actor_username,
               cs.title as contract_status_title,
               cs.colour as contract_status_color,
               st.title as status_title,
               st.colour as status_color
        FROM projects p
        LEFT JOIN customers c ON c.id = p.pcid
        LEFT JOIN units r ON r.id = p.region
        LEFT JOIN units s ON s.id = p.servicestype
        LEFT JOIN users u ON u.id = p.pcreativer
        LEFT JOIN users uu ON uu.id = p.updater
        LEFT JOIN (
            SELECT l.service_id, l.action, l.action_by, l.action_at
            FROM service_accounting_receipt_logs l
            INNER JOIN (
                SELECT service_id, MAX(id) as max_id
                FROM service_accounting_receipt_logs
                GROUP BY service_id
            ) lm ON lm.max_id = l.id
        ) ar ON ar.service_id = p.id
        LEFT JOIN users au ON au.id = ar.action_by
        LEFT JOIN units cs ON cs.id = p.contract_statu AND cs.statu = 4
        LEFT JOIN units st ON st.id = p.pstatu AND st.statu = 4
        WHERE p.pcid = ? 
        ORDER BY p.id desc
    ");
    $query->execute(array($cid));
} else if ($sid) {
    $query = $ac->prepare("
        SELECT p.*, 
               c.company as company_name, 
               r.title as region_name, 
               s.title as service_title, 
               u.username as creator_username,
             uu.username as updater_username,
             ar.action as accounting_action,
             ar.action_at as accounting_action_at,
             au.username as accounting_actor_username,
               cs.title as contract_status_title,
               cs.colour as contract_status_color,
               st.title as status_title,
               st.colour as status_color
        FROM projects p
        LEFT JOIN customers c ON c.id = p.pcid
        LEFT JOIN units r ON r.id = p.region
        LEFT JOIN units s ON s.id = p.servicestype
        LEFT JOIN users u ON u.id = p.pcreativer
        LEFT JOIN users uu ON uu.id = p.updater
        LEFT JOIN (
            SELECT l.service_id, l.action, l.action_by, l.action_at
            FROM service_accounting_receipt_logs l
            INNER JOIN (
                SELECT service_id, MAX(id) as max_id
                FROM service_accounting_receipt_logs
                GROUP BY service_id
            ) lm ON lm.max_id = l.id
        ) ar ON ar.service_id = p.id
        LEFT JOIN users au ON au.id = ar.action_by
        LEFT JOIN units cs ON cs.id = p.contract_statu AND cs.statu = 4
        LEFT JOIN units st ON st.id = p.pstatu AND st.statu = 4
        WHERE p.id = ? 
        ORDER BY p.id desc
    ");
    $query->execute(array($sid));
} else {
    $query = $ac->prepare("
        SELECT p.*, 
               c.company as company_name, 
               r.title as region_name, 
               s.title as service_title, 
               u.username as creator_username,
             uu.username as updater_username,
             ar.action as accounting_action,
             ar.action_at as accounting_action_at,
             au.username as accounting_actor_username,
               cs.title as contract_status_title,
               cs.colour as contract_status_color,
               st.title as status_title,
               st.colour as status_color
        FROM projects p
        LEFT JOIN customers c ON c.id = p.pcid
        LEFT JOIN units r ON r.id = p.region
        LEFT JOIN units s ON s.id = p.servicestype
        LEFT JOIN users u ON u.id = p.pcreativer
        LEFT JOIN users uu ON uu.id = p.updater
        LEFT JOIN (
            SELECT l.service_id, l.action, l.action_by, l.action_at
            FROM service_accounting_receipt_logs l
            INNER JOIN (
                SELECT service_id, MAX(id) as max_id
                FROM service_accounting_receipt_logs
                GROUP BY service_id
            ) lm ON lm.max_id = l.id
        ) ar ON ar.service_id = p.id
        LEFT JOIN users au ON au.id = ar.action_by
        LEFT JOIN units cs ON cs.id = p.contract_statu AND cs.statu = 4
        LEFT JOIN units st ON st.id = p.pstatu AND st.statu = 4
        ORDER BY p.id desc
    ");
    $query->execute();
}

$projects = $query->fetchAll(PDO::FETCH_ASSOC);



// Server-side processing için gerekli değişkenleri tanımla
$use_server_side = true; // Server-side processing aktif
$ajax_url = "api/services_datatables.php";

// Eğer spesifik bir müşteri veya servis ID'si varsa, server-side processing'i devre dışı bırak
if ($cid || $sid) {
    $use_server_side = false;
}

?>
<div class="bg-white premium-section-card box-shadow mb-4 animate-fade-in">
    <div class="row">
        <!-- Bekleyen Servisler -->
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4 mb-lg-0">
            <div class="dashboard-card card-yellow">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="d-block text-muted font-14 weight-500 mb-1">Bekleyen Servis Sayısı</span>
                        <span class="no text-warning weight-700 font-30">
                            <?php echo $bekleyen_servis_sayisi; ?>
                        </span>
                    </div>
                    <div class="icon bg-warning text-white box-shadow">
                        <i class="fa fa-hourglass-o"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Çalışılan Servisler -->
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4 mb-lg-0">
            <div class="dashboard-card card-blue">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="d-block text-muted font-14 weight-500 mb-1">Çalışılan Servis Sayısı</span>
                        <span class="no text-blue weight-700 font-30">
                            <?php echo $calisilan_servis_sayisi; ?>
                        </span>
                    </div>
                    <div class="icon bg-blue text-white box-shadow">
                        <i class="fa fa-wrench"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tamamlanan Servisler -->
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4 mb-md-0">
            <div class="dashboard-card card-green">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="d-block text-muted font-14 weight-500 mb-1">Tamamlanan Servis Sayısı</span>
                        <span class="no text-success weight-700 font-30">
                            <?php echo $tamamlanan_servis_sayisi; ?>
                        </span>
                    </div>
                    <div class="icon bg-success text-white box-shadow">
                        <i class="fa fa-check"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- İptal Edilen Servisler -->
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="dashboard-card card-red">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="d-block text-muted font-14 weight-500 mb-1">İptal Edilen Servis Sayısı</span>
                        <span class="no text-danger weight-700 font-30">
                            <?php echo $iptal_servis_sayisi; ?>
                        </span>
                    </div>
                    <div class="icon bg-danger text-white box-shadow">
                        <i class="fa fa-close"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white premium-section-card box-shadow mb-30 animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-30" style="flex-wrap: wrap; gap: 15px;">
        <div>
            <h4 class="text-blue weight-600 mb-0">Oluşturulan Tüm Servisler</h4>
        </div>
        <div>
            <!-- Excele Aktar -->
            <?php if (permtrue("data_export_service")) { ?>
                <a href="#" class="btn btn-outline-success mr-2" id="exportExcel"><i class="fa fa-file-excel-o mr-1"></i>
                    Excel'e Aktar</a>
            <?php } ?>
            <?php if (permtrue("serviceAdd")) { ?>
                <a href="index.php?p=service/manage" class="btn btn-primary"><i class="fa fa-plus-circle mr-1"></i> Yeni Servis
                    Oluştur</a>
            <?php } ?>
        </div>
    </div>
    <div class="search-input-area d-flex"></div>
    <div class="table-responsive">
        <table class="data-table table-hover table-bordered" id="service-table" style="width:100%">
            <thead>
                <tr>
                    <th scope="col">Sıra No</th>
                    <th scope="col">Servis No</th>
                    <th>Firma Adı</th>
                    <th>Bölge</th>
                    <th>Servis Konusu </th>
                    <th>İş Emri Oluşturma Tarihi</th>
                    <th>Servis Planlama Tarihi</th>
                    <th>Sözleşme Durum</th>
                    <th>Durum</th>
                    <th>İş Emrini Oluşturan</th>
                    <th>Son İşlem Yapan</th>
                    <th>Muhasebe Teslim</th>
                    <th>İşlemler</th>

                </tr>
            </thead>
            <tbody>
                <?php if (!$use_server_side): ?>
                    <?php $sirano = 1;
                    foreach ($projects as $purc) {
                        $pid = $purc["id"]; ?>
                        <tr>
                            <td class="text-center"><?php echo $sirano; ?></td>
                            <td><?php echo $purc["service_number"]; ?></td>
                            <td data-tooltip="<?php echo $purc['company_name']; ?>">
                                <?php echo shorted($purc['company_name'], 40); ?>
                            </td>
                            <td><?php echo $purc['region_name']; ?></td>
                            <td><?php echo $purc['service_title']; ?></td>
                            <td><?php echo $purc["pregdate"]; ?></td>
                            <td><?php echo $purc["pstart_date"]; ?></td>
                            <td class="text-center">
                                <?php $color = (!empty($purc['contract_status_color'])) ? $purc['contract_status_color'] : '#777';
                                $title = $purc['contract_status_title'] ?? '';
                                echo "<span class='badge' style='background-color:{$color}'>{$title}</span>"; ?>
                            </td>
                            <td class="text-center">
                                <?php $color = (!empty($purc['status_color'])) ? $purc['status_color'] : '#777';
                                $title = $purc['status_title'] ?? '';
                                echo "<span class='badge' style='background-color:{$color}'>{$title}</span>"; ?>
                            </td>
                            <td><?php echo $purc['creator_username']; ?></td>
                            <td><?php echo $purc['updater_username'] ?: $purc['creator_username']; ?></td>
                            <td class="text-center">
                                <?php
                                $isAccountingReceived = ($purc['accounting_action'] ?? '') === 'received';
                                $accLabel = $isAccountingReceived ? 'Teslim Alındı' : 'Teslim Bekliyor';
                                $accClass = $isAccountingReceived ? 'badge-success' : 'badge-warning';
                                echo "<span class='badge {$accClass}'>{$accLabel}</span>";

                                if (!empty($purc['accounting_actor_username']) && !empty($purc['accounting_action_at'])) {
                                    echo '<div class="small text-muted">' . htmlspecialchars($purc['accounting_actor_username']) . ' - ' . htmlspecialchars($purc['accounting_action_at']) . '</div>';
                                }
                                ?>
                            </td>
                            <td>
                                <div class="text-nowrap d-inline-flex align-items-center" style="flex-wrap:nowrap; gap:4px">

                                    <?php if ($canEdit): ?>
                                        <a type="button" href="index.php?p=service/manage&id=<?php echo $pid; ?>"
                                            class="btn btn-sm btn-outline-info" data-tooltip="Düzenle"><i
                                                class="fa fa-pencil"></i></a>
                                    <?php endif;
                                    if ($canDel): ?>
                                        <button type="button" class="btn btn-sm btn-danger" data-tooltip="Sil"
                                            onClick="deleteRecord('<?php echo $purc["id"]; ?> nolu Servisi silmek istediğinize emin misiniz?','<?php echo $pid; ?>','services','projects')"><i
                                                class="fa fa-trash"></i></button>
                                    <?php endif; ?>
                                    <a type="button" href="index.php?p=service-view&id=<?php echo encrypt($pid) ?>"
                                        target="_blank" class="btn btn-sm btn-secondary" data-tooltip="Detay"><i
                                            class="fa fa-info-circle"></i></a>
                                    <?php if ($canAccountingReceipt): ?>
                                        <?php
                                        $btnClass = $isAccountingReceived ? 'btn-outline-danger' : 'btn-outline-success';
                                        $btnText = $isAccountingReceived ? 'İade Al' : 'Teslim Al';
                                        $confirmText = $isAccountingReceived
                                            ? 'Bu servis için muhasebe teslim kaydını iade almak istediğinize emin misiniz?'
                                            : 'Bu servisi muhasebe teslim alındı olarak işaretlemek istediğinize emin misiniz?';
                                        ?>
                                        <button type="button" class="btn btn-sm <?php echo $btnClass; ?> js-accounting-receipt-toggle"
                                            data-service-id="<?php echo (int) $pid; ?>"
                                            data-confirm="<?php echo htmlspecialchars($confirmText); ?>"><?php echo $btnText; ?></button>
                                        <button type="button" class="btn btn-sm btn-dark js-accounting-log"
                                            data-service-id="<?php echo (int) $pid; ?>" data-service-number="<?php echo htmlspecialchars($purc['service_number']); ?>"
                                            data-tooltip="Muhasebe Teslim Log"><i class="fa fa-history"></i></button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php $sirano++;
                    } ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="accountingReceiptLogModal" tabindex="-1" role="dialog" aria-labelledby="accountingReceiptLogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accountingReceiptLogModalLabel">Muhasebe Teslim Logları</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0" id="accountingReceiptLogTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>İşlem</th>
                                <th>Yapan</th>
                                <th>Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Kayıt bulunamadı.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        var useServerSide = <?php echo $use_server_side ? 'true' : 'false'; ?>;
        
        var dtOptions = {
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            language: {
                url: 'include/js/tr.json',
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Yükleniyor...</span>'
            },
            responsive: true,
            order: [
                [0, 'desc']
            ],
            orderCellsTop: true,
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
                    if (header.find('input[type="checkbox"]').length === 0 && column.visible() && title && title.trim() !== 'İşlem' && title.trim() !== 'İşlemler') {

                        let input = $('<input type="text" class="form-control form-control-sm" placeholder="' + title + '" autocomplete="off">')
                            .appendTo($('<th class="search"></th>').appendTo("#" + tableId + " .search-input-row"))
                            .on('keyup change clear', function () {
                                // === ANAHTAR DEĞİŞİKLİK BURADA ===
                                // Eğer sütunun arama değeri bu input'un değeriyle aynı değilse,
                                // yeni değeri ata ve tabloyu yeniden çiz
                                if (column.search() !== this.value) {
                                    column.search(this.value).draw();
                                }
                            });
                    } else {
                        // Diğer sütunlar için boş bir <th> ekle
                        $("#" + tableId + " .search-input-row").append('<th></th>');
                    }
                });
            }
        };

        if (useServerSide) {
            dtOptions.processing = true;
            dtOptions.serverSide = true;
            dtOptions.ajax = {
                url: '<?php echo $ajax_url; ?>',
                type: 'GET'
            };
            dtOptions.columns = [{
                data: null,
                orderable: false,
                className: 'text-center',
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 1
            }, // service_number
            {
                data: 2
            }, // company_name
            {
                data: 3
            }, // region_name
            {
                data: 4
            }, // service_title
            {
                data: 5
            }, // pregdate
            {
                data: 6
            }, // pstart_date
            {
                data: 7
            }, // contract_status
            {
                data: 8
            }, // status
            {
                data: 9
            }, // creator_username
            {
                data: 10
            }, // updater_username
            {
                data: 11
            }, // accounting status
            {
                data: 12,
                orderable: false,
                className: 'all text-nowrap',
                responsivePriority: 1
            } // actions
            ];
        }

        $('#service-table').DataTable(dtOptions);
    });
</script>

<script src="include/js/data-table.js"></script>
<script>
    $(document).ready(function () {
        function showSwal(options) {
            if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                return Swal.fire(options);
            }
            if (typeof swal !== 'undefined' && typeof swal.fire === 'function') {
                return swal.fire(options);
            }
            return null;
        }

        function showSimpleMessage(icon, title, text) {
            var instance = showSwal({
                icon: icon,
                title: title,
                text: text,
                confirmButtonText: 'Tamam'
            });

            if (!instance) {
                window.alert(text || title);
            }
        }

        var hasDT = $.fn.DataTable && $.fn.DataTable.isDataTable('#service-table');
        var t = hasDT ? $('#service-table').DataTable() : null;
        $('#exportExcel').off('click').on('click', function (e) {
            e.preventDefault();
            var params = {};
            if (hasDT) {
                var order = t.order();
                if (order && order.length) {
                    params['order[0][column]'] = order[0][0];
                    params['order[0][dir]'] = order[0][1];
                }
                var gs = t.search();
                if (gs) params['search[value]'] = gs;
                t.columns().every(function (index) {
                    var v = this.search();
                    if (v) params['columns[' + index + '][search][value]'] = v;
                });
            }
        <?php if ($cid) { ?> params['cid'] = '<?php echo $cid; ?>';
            <?php } ?>
        <?php if ($sid) { ?> params['sid'] = '<?php echo $sid; ?>';
            <?php } ?>
            var qs = $.param(params);
            window.location = 'api/services_export.php' + (qs ? ('?' + qs) : '');
        });

        $(document).on('click', '.js-accounting-receipt-toggle', function () {
            var $btn = $(this);
            var serviceId = parseInt($btn.data('service-id'), 10);
            var confirmText = $btn.data('confirm') || 'Bu işlemi yapmak istediğinize emin misiniz?';

            if (!serviceId) {
                return;
            }

            var swalConfirm = showSwal({
                icon: 'warning',
                title: 'Emin misiniz?',
                text: confirmText,
                showCancelButton: true,
                confirmButtonText: 'Evet',
                cancelButtonText: 'Vazgeç'
            });

            var proceed = function () {
                $btn.prop('disabled', true);

                $.ajax({
                    url: 'api/services_datatables.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'toggle_accounting_receipt',
                        service_id: serviceId
                    }
                }).done(function (response) {
                    if (response && response.success) {
                        showSimpleMessage('success', 'Başarılı', response.message || 'İşlem tamamlandı.');
                        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#service-table')) {
                            $('#service-table').DataTable().ajax.reload(null, false);
                        } else {
                            window.location.reload();
                        }
                    } else {
                        showSimpleMessage('error', 'Hata', (response && response.message) ? response.message : 'İşlem başarısız oldu.');
                    }
                }).fail(function () {
                    showSimpleMessage('error', 'Hata', 'İşlem sırasında bir hata oluştu.');
                }).always(function () {
                    $btn.prop('disabled', false);
                });
            };

            if (swalConfirm && typeof swalConfirm.then === 'function') {
                swalConfirm.then(function (result) {
                    if (result && (result.isConfirmed || result.value === true)) {
                        proceed();
                    }
                });
            } else if (window.confirm(confirmText)) {
                proceed();
            }
        });

        $(document).on('click', '.js-accounting-log', function () {
            var serviceId = parseInt($(this).data('service-id'), 10);
            var serviceNumber = $(this).data('service-number') || '';

            if (!serviceId) {
                return;
            }

            $('#accountingReceiptLogModalLabel').text('Muhasebe Teslim Logları - Servis No: ' + serviceNumber);
            $('#accountingReceiptLogTable tbody').html('<tr><td colspan="4" class="text-center">Yükleniyor...</td></tr>');
            $('#accountingReceiptLogModal').modal('show');

            $.ajax({
                url: 'api/services_datatables.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'get_accounting_receipt_logs',
                    service_id: serviceId
                }
            }).done(function (response) {
                if (!response || !response.success) {
                    $('#accountingReceiptLogTable tbody').html('<tr><td colspan="4" class="text-center text-danger">Loglar alınamadı.</td></tr>');
                    return;
                }

                var logs = response.logs || [];
                if (!logs.length) {
                    $('#accountingReceiptLogTable tbody').html('<tr><td colspan="4" class="text-center text-muted">Kayıt bulunamadı.</td></tr>');
                    return;
                }

                var html = '';
                for (var i = 0; i < logs.length; i++) {
                    var log = logs[i];
                    var actionText = log.action === 'received' ? 'Teslim Alındı' : 'İade Alındı';
                    html += '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td>' + actionText + '</td>' +
                        '<td>' + (log.action_by_name || '-') + '</td>' +
                        '<td>' + (log.action_at || '-') + '</td>' +
                        '</tr>';
                }
                $('#accountingReceiptLogTable tbody').html(html);
            }).fail(function () {
                $('#accountingReceiptLogTable tbody').html('<tr><td colspan="4" class="text-center text-danger">Loglar alınırken hata oluştu.</td></tr>');
            });
        });
    });
</script>