<?php
permcontrol("support-request-view");

$userId = $_SESSION["lid"];
$isAdmin = permtrue("support-request-process");
$isApprover = permtrue("support-request-approve");

if ($isAdmin) {
    $queryStr = "SELECT sr.*, u.username as requester_name 
                 FROM support_requests sr 
                 LEFT JOIN users u ON u.id = sr.created_by 
                 ORDER BY sr.id DESC";
    $query = $ac->prepare($queryStr);
    $query->execute();
} elseif ($isApprover) {
    $queryStr = "SELECT sr.*, u.username as requester_name 
                 FROM support_requests sr 
                 LEFT JOIN users u ON u.id = sr.created_by 
                 WHERE sr.status = 'pending_approval' OR sr.created_by = ? 
                 ORDER BY sr.id DESC";
    $query = $ac->prepare($queryStr);
    $query->execute([$userId]);
} else {
    $queryStr = "SELECT sr.*, u.username as requester_name 
                 FROM support_requests sr 
                 LEFT JOIN users u ON u.id = sr.created_by 
                 WHERE sr.created_by = ? 
                 ORDER BY sr.id DESC";
    $query = $ac->prepare($queryStr);
    $query->execute([$userId]);
}

if (@$_GET["st"] == "newsuccess") {
    showAlert("success", "Destek talebiniz başarıyla oluşturuldu ve onaya gönderildi.");
}
?>

<div class="pd-20 bg-white border-radius-16 box-shadow mb-30">
    <div class="clearfix mb-20">
        <div class="pull-left">
            <h5 class="text-blue">Destek Talepleri</h5>
            <p class="font-14 text-muted">Sistemdeki destek taleplerinin takibini buradan yapabilirsiniz.</p>
        </div>
        <div class="float-right">
            <?php if (permtrue("support-request-add")) { ?>
                <a href="index.php?p=support-new" class="btn btn-success btn-sm">
                    <i class="fa fa-plus-circle"></i> Yeni Destek Talebi
                </a>
            <?php } ?>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="data-table table-hover table-bordered table-striped">
            <thead>
                <tr>
                    <th scope="col" style="width: 5%;" class="text-center">Sıra</th>
                    <th style="width: 12%;">Talep No</th>
                    <th>Başlık</th>
                    <th style="width: 12%;">Kategori</th>
                    <th style="width: 10%;">Aciliyet</th>
                    <th style="width: 15%;">Talep Eden</th>
                    <th style="width: 12%;" class="text-center">Durum</th>
                    <th style="width: 12%;">Oluşturma Tarihi</th>
                    <th style="width: 10%;" class="text-center">İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $kx = 1;
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    // Durum Badgelerini Hazırla
                    $statusBadge = '';
                    switch ($row["status"]) {
                        case 'pending_approval':
                            $statusBadge = '<span class="badge badge-warning" style="padding: 6px 10px; font-size: 0.85rem; display: inline-block; width: 110px; text-align: center;">Onay Bekliyor</span>';
                            break;
                        case 'approved':
                            $statusBadge = '<span class="badge badge-info" style="padding: 6px 10px; font-size: 0.85rem; display: inline-block; width: 110px; text-align: center;">Onaylandı</span>';
                            break;
                        case 'rejected':
                            $statusBadge = '<span class="badge badge-danger" style="padding: 6px 10px; font-size: 0.85rem; display: inline-block; width: 110px; text-align: center;">Reddedildi</span>';
                            break;
                        case 'in_progress':
                            $statusBadge = '<span class="badge badge-primary" style="padding: 6px 10px; font-size: 0.85rem; display: inline-block; width: 110px; text-align: center;">İşlemde</span>';
                            break;
                        case 'completed':
                            $statusBadge = '<span class="badge badge-success" style="padding: 6px 10px; font-size: 0.85rem; display: inline-block; width: 110px; text-align: center;">Tamamlandı</span>';
                            break;
                    }
                    
                    // Aciliyet Renklendirme
                    $urgencyText = htmlspecialchars($row["urgency"]);
                    $urgencyCell = $urgencyText;
                    if ($row["urgency"] == "Çok Acil") {
                        $urgencyCell = '<span class="text-danger font-weight-bold"><i class="fa fa-exclamation-triangle"></i> Çok Acil</span>';
                    } elseif ($row["urgency"] == "Acil") {
                        $urgencyCell = '<span class="text-warning font-weight-bold">' . $urgencyText . '</span>';
                    } elseif ($row["urgency"] == "Normal") {
                        $urgencyCell = '<span class="text-primary">' . $urgencyText . '</span>';
                    } else {
                        $urgencyCell = '<span class="text-success">' . $urgencyText . '</span>';
                    }
                ?>
                    <tr>
                        <td class="text-center"><?php echo $kx; ?></td>
                        <td class="font-weight-bold"><?php echo htmlspecialchars($row["ticket_no"]); ?></td>
                        <td><?php echo htmlspecialchars($row["title"]); ?></td>
                        <td><?php echo htmlspecialchars($row["category"]); ?></td>
                        <td><?php echo $urgencyCell; ?></td>
                        <td><?php echo htmlspecialchars($row["requester_name"]); ?></td>
                        <td class="text-center"><?php echo $statusBadge; ?></td>
                        <td><?php echo date("d-m-Y H:i", strtotime($row["created_at"])); ?></td>
                        <td class="text-center">
                            <a href="index.php?p=support-detail&id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-outline-info" data-tooltip="Detay">
                                <i class="fa fa-eye"></i>
                            </a>
                            <?php if ($isAdmin || ($row["created_by"] == $userId && $row["status"] == 'pending_approval')) { ?>
                                <button class="btn btn-sm btn-danger ml-1" data-tooltip="Sil" onClick="deleteRecord('Bu destek talebini tamamen silmek istediğinize emin misiniz?', <?php echo $row["id"]; ?>, 'support-list', 'support_requests')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            <?php } ?>
                        </td>
                    </tr>
                <?php
                    $kx++;
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="include/js/data-table.js"></script>
