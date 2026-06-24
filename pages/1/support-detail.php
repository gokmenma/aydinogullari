<?php
permcontrol("support-request-view");

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$userId = $_SESSION["lid"];
$isAdmin = permtrue("support-request-process");
$isApprover = permtrue("support-request-approve");

// Bilet detaylarını ve ilgili kullanıcı adlarını getir
$sql = $ac->prepare("SELECT sr.*, u.username as requester_name, 
                            ap.username as approver_name, 
                            asg.username as assignee_name 
                     FROM support_requests sr 
                     LEFT JOIN users u ON u.id = sr.created_by 
                     LEFT JOIN users ap ON ap.id = sr.approved_by 
                     LEFT JOIN users asg ON asg.id = sr.assigned_to 
                     WHERE sr.id = ?");
$sql->execute([$id]);
$ticket = $sql->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    header("Location: index.php?p=support-list");
    exit();
}

// Güvenlik Kontrolü: Kullanıcı bu bileti görmeye yetkili mi?
$isOwner = ($ticket["created_by"] == $userId);
$isPendingAndApprover = ($ticket["status"] == 'pending_approval' && $isApprover);

if (!$isAdmin && !$isOwner && !$isPendingAndApprover) {
    header("Location: index.php?p=support-list");
    exit();
}

// Bilet için gönderilen son mesajın user_id değerini al
$lastSenderQuery = $ac->prepare("SELECT user_id FROM support_replies WHERE ticket_id = ? ORDER BY id DESC LIMIT 1");
$lastSenderQuery->execute([$id]);
$lastSenderId = $lastSenderQuery->fetchColumn();

// Talep sahibi ardışık cevap yazabilir mi? (Sıralı Cevaplama Kuralı)
$canOwnerReply = true;
if ($isOwner) {
    // Eğer hiç cevap yazılmadıysa veya son yazan bilet sahibi ise cevap yazamaz (yöneticiden cevap beklenir)
    if (!$lastSenderId || $lastSenderId == $ticket["created_by"]) {
        $canOwnerReply = false;
    }
}

// POST Form Aksiyonları
if ($_POST) {
    $actionType = isset($_POST["action_type"]) ? $_POST["action_type"] : '';
    
    // 1. Onay / Red İşlemi (Onaycı Kullanıcı)
    if ($actionType == "approval" && $isApprover && $ticket["status"] == 'pending_approval') {
        $status = $_POST["status_choice"]; // approved veya rejected
        $approvalNote = $_POST["approval_note"];
        
        $up = $ac->prepare("UPDATE support_requests SET status = ?, approved_by = ?, approval_note = ? WHERE id = ?");
        $success = $up->execute([$status, $userId, $approvalNote, $id]);
        
        if ($success) {
            // Onay/Red durumunu yazışmalara da ekle (Böylece son mesaj admin/onaycıya geçer)
            $msgText = ($status == "approved") ? "Talep Onaylandı. Not: " . $approvalNote : "Talep Reddedildi. Not: " . $approvalNote;
            $insReply = $ac->prepare("INSERT INTO support_replies SET ticket_id = ?, user_id = ?, message = ?");
            $insReply->execute([$id, $userId, $msgText]);
            
            header("Location: index.php?p=support-detail&id=" . $id . "&st=approvalsuccess");
            exit();
        }
    }
    
    // 2. İşleme Al / Tamamla İşlemi (Admin Kullanıcı)
    if ($actionType == "admin_action" && $isAdmin) {
        $status = $_POST["status_choice"]; // in_progress veya completed
        $adminNote = $_POST["admin_note"];
        
        $up = $ac->prepare("UPDATE support_requests SET status = ?, assigned_to = ?, admin_note = ? WHERE id = ?");
        $success = $up->execute([$status, $userId, $adminNote, $id]);
        
        if ($success) {
            // Admin işlem notunu yazışmalara da ekle
            $msgText = ($status == "in_progress") ? "Talep işleme alındı. Not: " . $adminNote : "Talep tamamlandı/kapatıldı. Çözüm Notu: " . $adminNote;
            $insReply = $ac->prepare("INSERT INTO support_replies SET ticket_id = ?, user_id = ?, message = ?");
            $insReply->execute([$id, $userId, $msgText]);
            
            header("Location: index.php?p=support-detail&id=" . $id . "&st=adminsuccess");
            exit();
        }
    }
    
    // 3. Mesaj Cevap Yazma İşlemi (Talep Sahibi veya Yetkili Kullanıcı)
    if ($actionType == "reply") {
        $message = trim($_POST["message"]);
        $reply_attachment = null;
        
        // Sıralı cevaplama kontrolü (Server-side)
        $allowPost = true;
        if ($isOwner && !$canOwnerReply) {
            $allowPost = false;
        }
        
        if ($allowPost && (!empty($message) || (isset($_FILES["reply_attachment"]) && $_FILES["reply_attachment"]["name"] != ""))) {
            // Dosya yükleme
            if (isset($_FILES["reply_attachment"]) && $_FILES["reply_attachment"]["name"] != "") {
                $dizin = "files/";
                $rast = rand(1000, 9999);
                $fileName = $rast . "_" . basename($_FILES["reply_attachment"]["name"]);
                $hedef = $dizin . $fileName;
                if (move_uploaded_file($_FILES["reply_attachment"]["tmp_name"], $hedef)) {
                    $reply_attachment = $fileName;
                }
            }
            
            $insReply = $ac->prepare("INSERT INTO support_replies SET ticket_id = ?, user_id = ?, message = ?, attachment_path = ?");
            $success = $insReply->execute([$id, $userId, $message, $reply_attachment]);
            
            if ($success) {
                // Eğer talep sahibi yanıt yazdıysa ve talep tamamlanmış/reddedilmiş/onaylanmış ise, durumu tekrar "İşlemde"ye (in_progress) çekelim
                if ($isOwner) {
                    if ($ticket["status"] == "completed" || $ticket["status"] == "rejected" || $ticket["status"] == "approved") {
                        $upStatus = $ac->prepare("UPDATE support_requests SET status = 'in_progress' WHERE id = ?");
                        $upStatus->execute([$id]);
                    }
                }
                header("Location: index.php?p=support-detail&id=" . $id . "&st=replysuccess");
                exit();
            }
        }
    }
}

// Cevapları veri tabanından getir
$repliesQuery = $ac->prepare("SELECT sr.*, u.username as sender_name, u.permission as sender_perm, p.p_title as sender_role 
                             FROM support_replies sr 
                             LEFT JOIN users u ON u.id = sr.user_id 
                             LEFT JOIN perms p ON p.id = u.permission 
                             WHERE sr.ticket_id = ? 
                             ORDER BY sr.created_at ASC");
$repliesQuery->execute([$id]);
$replies = $repliesQuery->fetchAll(PDO::FETCH_ASSOC);

// Başarı Mesajları
if (@$_GET["st"] == "approvalsuccess") {
    showAlert("success", "Talep onay/red işlemi başarıyla kaydedildi.");
}
if (@$_GET["st"] == "adminsuccess") {
    showAlert("success", "Talep işlem/tamamlama kaydı başarıyla güncellendi.");
}
if (@$_GET["st"] == "replysuccess") {
    showAlert("success", "Cevabınız başarıyla iletildi.");
}
?>

<div class="support-detail-wrapper">
    <!-- Header Card -->
    <div class="premium-header-card animate-fade-in mb-30">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fa fa-life-ring"></i>
                </div>
                <div class="header-title">
                    <h4><?php echo htmlspecialchars($ticket["title"]); ?></h4>
                    <span class="header-number-badge mr-2">
                        <i class="fa fa-tag"></i> Bilet No: <?php echo htmlspecialchars($ticket["ticket_no"]); ?>
                    </span>
                    <span class="header-number-badge">
                        <i class="fa fa-calendar"></i> Oluşturulma: <?php echo date("d-m-Y H:i", strtotime($ticket["created_at"])); ?>
                    </span>
                </div>
            </div>
            <div class="header-actions">
                <a href="index.php?p=support-list" class="btn-header btn-header-list">
                    <i class="fa fa-arrow-left"></i> Listeye Dön
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sol Panel: Talep İçeriği ve Yazışma Geçmişi -->
        <div class="col-lg-8 col-md-12">
            <!-- Kart 1: Talep Açıklaması -->
            <div class="card form-card mb-30 animate-fade-in">
                <div class="form-card-header">
                    <div class="card-icon card-icon-blue">
                        <i class="fa fa-align-left"></i>
                    </div>
                    <div>
                        <h5>Talep Detayları</h5>
                        <p>Kullanıcı tarafından iletilen ilk sorun kaydı</p>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Detay Bilgileri Grid -->
                    <div class="row mb-4" style="border-bottom: 1px solid #f3f4f6; padding-bottom: 15px;">
                        <div class="col-md-4">
                            <span class="text-muted d-block font-11 uppercase font-weight-bold">Talep Eden</span>
                            <span class="font-13 font-weight-600 text-blue"><i class="fa fa-user"></i> <?php echo htmlspecialchars($ticket["requester_name"]); ?></span>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted d-block font-11 uppercase font-weight-bold">Kategori</span>
                            <span class="badge" style="font-size: 0.8rem; padding: 4px 10px; border: 1px solid #1b00ff; color: #1b00ff; border-radius: 4px;"><?php echo htmlspecialchars($ticket["category"]); ?></span>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted d-block font-11 uppercase font-weight-bold">Aciliyet</span>
                            <span class="font-13 font-weight-600">
                                <?php
                                if ($ticket["urgency"] == "Çok Acil") {
                                    echo '<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> Çok Acil</span>';
                                } elseif ($ticket["urgency"] == "Acil") {
                                    echo '<span class="text-warning">' . htmlspecialchars($ticket["urgency"]) . '</span>';
                                } else {
                                    echo '<span class="text-success">' . htmlspecialchars($ticket["urgency"]) . '</span>';
                                }
                                ?>
                            </span>
                        </div>
                    </div>

                    <!-- Açıklama Metni -->
                    <div class="support-description-text mb-4" style="font-size: 1rem; line-height: 1.6; color: #374151; white-space: pre-line; background: #fafbfe; padding: 20px; border-radius: 8px; border-left: 4px solid #1b00ff;">
                        <?php echo htmlspecialchars($ticket["description"]); ?>
                    </div>

                    <!-- Ek Dosya -->
                    <?php if ($ticket["attachment_path"]): ?>
                        <div class="attachment-section p-3 border border-radius-8 bg-light">
                            <h6 class="font-12 mb-2 font-weight-bold text-blue"><i class="fa fa-paperclip"></i> Ekli Dosya</h6>
                            <?php 
                            $ext = strtolower(pathinfo($ticket["attachment_path"], PATHINFO_EXTENSION));
                            $isImg = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp']);
                            if ($isImg):
                            ?>
                                <div class="mb-3">
                                    <a href="files/<?php echo $ticket["attachment_path"]; ?>" target="_blank">
                                        <img src="files/<?php echo $ticket["attachment_path"]; ?>" alt="Bilet Eki" class="img-fluid border border-radius-8" style="max-height: 200px; object-fit: contain;">
                                    </a>
                                </div>
                            <?php endif; ?>
                            <a href="files/<?php echo $ticket["attachment_path"]; ?>" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fa fa-download"></i> Dosyayı İndir
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Kart 2: Yazışma Geçmişi (Timeline) -->
            <div class="card form-card mb-30 animate-fade-in">
                <div class="form-card-header">
                    <div class="card-icon card-icon-green">
                        <i class="fa fa-comments"></i>
                    </div>
                    <div>
                        <h5>Yazışma Geçmişi</h5>
                        <p>Destek ekibi ile yapılan tüm yazışmalar</p>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($replies)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fa fa-info-circle fa-2x mb-2 text-muted"></i>
                            <p class="mb-0">Henüz yazışma yapılmamıştır.</p>
                        </div>
                    <?php else: ?>
                        <div class="support-replies-timeline">
                            <?php foreach ($replies as $rep): 
                                $isRepOwner = ($rep["user_id"] == $ticket["created_by"]);
                                $senderRole = $isRepOwner ? "Talep Sahibi" : "Destek Ekibi / " . ($rep["sender_role"] ? $rep["sender_role"] : 'Sistem Kullanıcısı');
                                
                                // Stil Tanımlamaları
                                $bubbleBg = $isRepOwner ? "#f0f7ff" : "#f6fff6";
                                $borderLeft = $isRepOwner ? "4px solid #0056b3" : "4px solid #28a745";
                                $senderColor = $isRepOwner ? "#0056b3" : "#28a745";
                            ?>
                                <div class="reply-bubble mb-3 p-3 border-radius-8" style="background: <?php echo $bubbleBg; ?>; border-left: <?php echo $borderLeft; ?>; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                    <div class="d-flex justify-content-between align-items-center mb-2" style="border-bottom: 1px dashed rgba(0,0,0,0.08); padding-bottom: 5px;">
                                        <span class="font-weight-bold font-13" style="color: <?php echo $senderColor; ?>;">
                                            <i class="fa <?php echo $isRepOwner ? 'fa-user' : 'fa-support'; ?>"></i> 
                                            <?php echo htmlspecialchars($rep["sender_name"]); ?> 
                                            <small class="text-muted ml-2">(<?php echo htmlspecialchars($senderRole); ?>)</small>
                                        </span>
                                        <span class="text-muted font-11">
                                            <i class="fa fa-clock-o"></i> <?php echo date("d-m-Y H:i", strtotime($rep["created_at"])); ?>
                                        </span>
                                    </div>
                                    <div class="reply-text font-13" style="color: #374151; white-space: pre-line; line-height: 1.5;">
                                        <?php echo htmlspecialchars($rep["message"]); ?>
                                    </div>
                                    
                                    <!-- Mesaj Eki -->
                                    <?php if ($rep["attachment_path"]): ?>
                                        <div class="mt-3 pt-2 border-top" style="border-top-style: dashed !important; border-top-color: rgba(0,0,0,0.1) !important;">
                                            <?php 
                                            $repExt = strtolower(pathinfo($rep["attachment_path"], PATHINFO_EXTENSION));
                                            $isRepImg = in_array($repExt, ['png', 'jpg', 'jpeg', 'gif', 'webp']);
                                            if ($isRepImg):
                                            ?>
                                                <div class="mb-2">
                                                    <a href="files/<?php echo $rep["attachment_path"]; ?>" target="_blank">
                                                        <img src="files/<?php echo $rep["attachment_path"]; ?>" alt="Mesaj Eki" class="img-fluid border border-radius-8" style="max-height: 150px; object-fit: contain;">
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                            <a href="files/<?php echo $rep["attachment_path"]; ?>" target="_blank" class="btn btn-xs btn-outline-secondary">
                                                <i class="fa fa-paperclip"></i> Eki Görüntüle / İndir
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Kart 3: Cevap Yaz (Sıralı Cevaplama Kuralıyla) -->
            <div class="card form-card mb-30 animate-fade-in">
                <div class="form-card-header">
                    <div class="card-icon card-icon-purple">
                        <i class="fa fa-reply"></i>
                    </div>
                    <div>
                        <h5>Cevap Yaz</h5>
                        <p>Destek talebi ile ilgili mesaj yazıp dosya ekleyin</p>
                    </div>
                </div>
                <div class="card-body">
                    <?php 
                    // Cevap yazma formu gösterim mantığı
                    if ($isOwner && !$canOwnerReply): 
                    ?>
                        <div class="alert alert-info py-4 text-center">
                            <i class="fa fa-hourglass-half fa-2x mb-2 text-info"></i>
                            <h6 class="font-weight-bold">Destek ekibinden cevap bekleniyor</h6>
                            <p class="mb-0 mt-2 font-13 text-muted">Destek ekibi talebinizi yanıtlamadan veya işlem yapmadan üst üste yeni bir mesaj yazamazsınız. Lütfen ekibin yanıt vermesini bekleyin.</p>
                        </div>
                    <?php else: ?>
                        <?php if ($ticket["status"] == "completed" || $ticket["status"] == "rejected"): ?>
                            <div class="alert alert-warning mb-3">
                                <i class="fa fa-exclamation-triangle"></i> Bu destek talebi kapatılmıştır veya reddedilmiştir. Cevap yazarak talebi **tekrar aktif duruma (İşlemde)** alabilirsiniz.
                            </div>
                        <?php endif; ?>
                        
                        <form enctype="multipart/form-data" method="POST" action="" id="replyForm">
                            <input type="hidden" name="action_type" value="reply">
                            
                            <div class="form-group">
                                <label for="replyMessage"><font color="red">(*)</font> Mesajınız</label>
                                <textarea name="message" id="replyMessage" class="form-control" rows="4" placeholder="Cevabınızı buraya ayrıntılı olarak yazın..." required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="replyAttachment">Ek Dosya / Görsel</label>
                                <input name="reply_attachment" id="replyAttachment" type="file" class="form-control height-auto">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-send"></i> Cevap Gönder
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sağ Panel: Durum ve Aksiyonlar -->
        <div class="col-lg-4 col-md-12">
            <!-- Durum Kartı -->
            <div class="card form-card mb-30 animate-fade-in">
                <div class="form-card-header">
                    <div class="card-icon card-icon-green">
                        <i class="fa fa-tasks"></i>
                    </div>
                    <div>
                        <h5>İş Akışı Durumu</h5>
                        <p>Talebin güncel durumu ve geçmiş notları</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <?php
                        $statusBadge = '';
                        switch ($ticket["status"]) {
                            case 'pending_approval':
                                $statusBadge = '<span class="badge badge-warning font-14" style="padding: 8px 16px; border-radius: 20px;">Onay Bekliyor</span>';
                                break;
                            case 'approved':
                                $statusBadge = '<span class="badge badge-info font-14" style="padding: 8px 16px; border-radius: 20px;">Onaylandı</span>';
                                break;
                            case 'rejected':
                                $statusBadge = '<span class="badge badge-danger font-14" style="padding: 8px 16px; border-radius: 20px;">Reddedildi</span>';
                                break;
                            case 'in_progress':
                                $statusBadge = '<span class="badge badge-primary font-14" style="padding: 8px 16px; border-radius: 20px;">İşlemde</span>';
                                break;
                            case 'completed':
                                $statusBadge = '<span class="badge badge-success font-14" style="padding: 8px 16px; border-radius: 20px;">Tamamlandı</span>';
                                break;
                        }
                        echo $statusBadge;
                        ?>
                    </div>

                    <!-- Onay Geçmişi -->
                    <?php if ($ticket["approved_by"]): ?>
                        <div class="status-history-item mb-3 p-3 bg-light border-radius-8">
                            <span class="text-muted d-block font-11 uppercase font-weight-bold">Onay Durumu</span>
                            <span class="font-12 font-weight-bold">
                                <?php echo $ticket["status"] == 'rejected' ? 'Reddeden:' : 'Onaylayan:'; ?> 
                                <span class="text-blue"><?php echo htmlspecialchars($ticket["approver_name"]); ?></span>
                            </span>
                            <?php if ($ticket["approval_note"]): ?>
                                <p class="mt-2 mb-0 font-12 italic text-muted" style="border-top: 1px dashed #ddd; padding-top: 5px;">
                                    <strong>Not:</strong> <?php echo htmlspecialchars($ticket["approval_note"]); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Admin Çözüm/Çalışma Geçmişi -->
                    <?php if ($ticket["assigned_to"]): ?>
                        <div class="status-history-item p-3 bg-light border-radius-8">
                            <span class="text-muted d-block font-11 uppercase font-weight-bold">İşlem Durumu</span>
                            <span class="font-12 font-weight-bold">
                                Üstlenen/İşleyen: 
                                <span class="text-blue"><?php echo htmlspecialchars($ticket["assignee_name"]); ?></span>
                            </span>
                            <?php if ($ticket["admin_note"]): ?>
                                <p class="mt-2 mb-0 font-12 italic text-muted" style="border-top: 1px dashed #ddd; padding-top: 5px;">
                                    <strong>Çözüm/Admin Notu:</strong> <?php echo htmlspecialchars($ticket["admin_note"]); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Aksiyon Kartı (Dynamic Form) -->
            <?php if ($isPendingAndApprover): ?>
                <!-- Onaycı Formu -->
                <div class="card form-card mb-30 animate-fade-in">
                    <div class="form-card-header">
                        <div class="card-icon card-icon-purple">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <div>
                            <h5>Onay Aksiyonu</h5>
                            <p>Talebi onaylayın veya reddedin</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="approvalForm">
                            <input type="hidden" name="action_type" value="approval">
                            
                            <div class="form-group">
                                <label>Kararınız</label>
                                <select name="status_choice" class="form-control selectpicker" data-style="border bg-white" required>
                                    <option value="approved">Talebi Onayla</option>
                                    <option value="rejected">Talebi Reddet</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Onay/Red Açıklaması</label>
                                <textarea name="approval_note" class="form-control" rows="3" placeholder="Açıklama notu girin..." required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-save"></i> Kararı Gönder
                            </button>
                        </form>
                    </div>
                </div>
            <?php elseif ($isAdmin && in_array($ticket["status"], ['approved', 'in_progress', 'completed'])): ?>
                <!-- Admin İşleme Al / Tamamla Formu -->
                <div class="card form-card mb-30 animate-fade-in">
                    <div class="form-card-header">
                        <div class="card-icon card-icon-purple">
                            <i class="fa fa-wrench"></i>
                        </div>
                        <div>
                            <h5>Yönetici Aksiyonu</h5>
                            <p>Talebin genel durumunu ve admin çözümünü güncelleyin</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="adminForm">
                            <input type="hidden" name="action_type" value="admin_action">
                            
                            <div class="form-group">
                                <label>İşlem Durumu</label>
                                <select name="status_choice" class="form-control selectpicker" data-style="border bg-white" required>
                                    <option value="in_progress" <?php echo $ticket["status"] == 'in_progress' ? 'selected' : ''; ?>>İşleme Al (Çalışma Başladı)</option>
                                    <option value="completed" <?php echo $ticket["status"] == 'completed' ? 'selected' : ''; ?>>Tamamlandı (Sorun Çözüldü)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Yönetici/Çözüm Açıklaması</label>
                                <textarea name="admin_note" class="form-control" rows="3" placeholder="Çözüm detayları veya admin notu girin..." required><?php echo htmlspecialchars($ticket["admin_note"]); ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fa fa-save"></i> Durumu Güncelle
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $(".selectpicker").selectpicker({
        style: "border bg-white"
    });
});
</script>
