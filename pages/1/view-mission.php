<?php
permcontrol("missiontake");
if (!@$_GET["mid"]) {
	header("Location: index.php?p=home&errorcode=00254");
	exit;
}

$quer = $ac->prepare("SELECT * FROM missions WHERE id = ?");
$quer->execute(array($_GET["mid"]));
$as = $quer->fetch(PDO::FETCH_ASSOC);
if (!$as) {
	header("Location: index.php?p=home&errorcode=00784");
	exit;
}
if ($as["authors"] != sesset("id") && permfalse("allmisview") && $as["creativer"] != sesset("id")) {
	header("Location: index.php");
	exit;

}
if (@$_GET["mode"] == "update") {
	$upx = $ac->prepare("UPDATE missions SET okeydate = ?, statu = ? WHERE id = ?");
	$upx->execute(array(TODAY . " " . date("H:i:s"), 1, $_GET["mid"]));

	header("Location:index.php?p=my-missions&update=true&mid=" . $as["id"]);
	exit;
}
?>

<div class="view-mission-wrapper">
	<!-- Header Card -->
	<div class="premium-header-card animate-fade-in">
		<div class="header-content">
			<div class="header-left">
				<div class="header-icon">
					<i class="fa fa-clipboard"></i>
				</div>
				<div class="header-title">
					<h4><?php echo $pdat["p_title"] ?? 'Görev Detayları'; ?></h4>
					<span class="header-number-badge">
						<i class="fa fa-info-circle"></i> Görev detaylarını inceleyin ve yönetin
					</span>
				</div>
			</div>
			<div class="header-actions">
				<a href="index.php?p=all-missions" class="btn-header btn-header-list">
					<i class="fa fa-arrow-left"></i> Listeye Dön
				</a>

				<?php if ($as["statu"] == 0) { ?>
					<a OnClick="return confirm('Görevi yapıldı işaretlemeniz durumunda, bu işlemi geri alamazsınız.')"
					   href="index.php?p=view-mission&mode=update&statu=1&code=04md177&reg=true&md=active&mid=<?php echo $as["id"]; ?>"
					   class="btn-header btn-header-save">
						<i class="fa fa-check"></i> Yapıldı İşaretle
					</a>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="row">
		<!-- Sol Kolon: Görev Tanımı ve İçerik -->
		<div class="col-lg-8 col-md-12 mb-4 animate-fade-in">
			<div class="form-card" style="height: 100%;">
				<div class="form-card-header">
					<div class="card-icon card-icon-blue">
						<i class="fa fa-align-left"></i>
					</div>
					<div>
						<h5>Görev Detayları</h5>
						<p>Görevin konusu ve açıklaması</p>
					</div>
				</div>

				<div class="form-group mb-4">
					<label style="font-weight: 600; font-size: 13px; color: #475569;">Görev Başlığı</label>
					<input disabled name="title" value="<?php echo htmlspecialchars($as["title"] ?? ''); ?>" class="form-control" type="text" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; font-weight: 500; font-size: 15px; color: #1e293b; padding: 12px 16px;">
				</div>

				<div class="form-group">
					<label style="font-weight: 600; font-size: 13px; color: #475569;">Görev Açıklaması</label>
					<div class="p-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; min-height: 120px; font-size: 14px; line-height: 1.6; color: #334155;">
						<?php echo nl2br(htmlspecialchars($as["mdesc"] ?? '')); ?>
					</div>
				</div>
			</div>
		</div>

		<!-- Sağ Kolon: Durum & Tarih Çizelgesi -->
		<div class="col-lg-4 col-md-12 mb-4 animate-fade-in">
			<div class="form-card" style="height: 100%;">
				<div class="form-card-header">
					<div class="card-icon card-icon-purple">
						<i class="fa fa-history"></i>
					</div>
					<div>
						<h5>Durum & Bilgiler</h5>
						<p>Tarihler ve görev atamaları</p>
					</div>
				</div>

				<!-- Durum Rozeti -->
				<div class="text-center mb-4 p-3" style="background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
					<span style="font-size: 12px; font-weight: 600; display: block; color: #64748b; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">GÖREV DURUMU</span>
					<?php if ($as["statu"] == 1) { ?>
						<span class="badge badge-success" style="font-size: 13px; font-weight: 600; padding: 8px 16px; border-radius: 20px; border: none; box-shadow: 0 2px 4px rgba(34, 197, 94, 0.2);">
							<i class="fa fa-check-circle mr-1"></i> Görev Tamamlanmış
						</span>
					<?php } else { ?>
						<span class="badge badge-warning text-white" style="font-size: 13px; font-weight: 600; padding: 8px 16px; border-radius: 20px; border: none; box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);">
							<i class="fa fa-clock-o mr-1"></i> Görev Tamamlanmamış
						</span>
					<?php } ?>
				</div>

				<!-- Bilgi Satırları -->
				<div class="audit-trail-card" style="display: flex; flex-direction: column; gap: 16px;">
					<!-- Görevi Oluşturan -->
					<div class="audit-row" style="display: flex; align-items: flex-start; gap: 12px;">
						<div class="audit-icon" style="color: #6366f1; font-size: 14px; margin-top: 2px;"><i class="fa fa-user-circle"></i></div>
						<div style="display: flex; flex-direction: column;">
							<span style="font-size: 11px; color: #64748b; font-weight: 500;">Görevi Oluşturan</span>
							<span style="font-size: 13px; color: #1e293b; font-weight: 600;"><?php echo htmlspecialchars(uset($as["creativer"], "username") ?? ''); ?></span>
						</div>
					</div>

					<!-- Görev Alanlar -->
					<?php
					$authors = $as['authors'];
					$userstring = "";
					$userArrays = explode("|", $authors);
					foreach ($userArrays as $userid) {
						$userstring .= getUserInfo($userid, "username") . ", ";
					}
					$userstring = rtrim($userstring, ", ");
					?>
					<div class="audit-row" style="display: flex; align-items: flex-start; gap: 12px;">
						<div class="audit-icon" style="color: #10b981; font-size: 14px; margin-top: 2px;"><i class="fa fa-users"></i></div>
						<div style="display: flex; flex-direction: column;">
							<span style="font-size: 11px; color: #64748b; font-weight: 500;">Görevlendirilen Kişiler</span>
							<span style="font-size: 13px; color: #1e293b; font-weight: 600;"><?php echo htmlspecialchars($userstring); ?></span>
						</div>
					</div>

					<!-- Kayıt Tarihi -->
					<div class="audit-row" style="display: flex; align-items: flex-start; gap: 12px;">
						<div class="audit-icon" style="color: #3b82f6; font-size: 14px; margin-top: 2px;"><i class="fa fa-calendar-plus-o"></i></div>
						<div style="display: flex; flex-direction: column;">
							<span style="font-size: 11px; color: #64748b; font-weight: 500;">Görev Kayıt Tarihi</span>
							<span style="font-size: 13px; color: #334155; font-weight: 500;"><?php echo htmlspecialchars($as["reg_date"] ?? ''); ?></span>
						</div>
					</div>

					<!-- Başlangıç Tarihi -->
					<div class="audit-row" style="display: flex; align-items: flex-start; gap: 12px;">
						<div class="audit-icon" style="color: #3b82f6; font-size: 14px; margin-top: 2px;"><i class="fa fa-play-circle-o"></i></div>
						<div style="display: flex; flex-direction: column;">
							<span style="font-size: 11px; color: #64748b; font-weight: 500;">Başlangıç Tarihi</span>
							<span style="font-size: 13px; color: #334155; font-weight: 500;"><?php echo htmlspecialchars($as["regdate"] ?? ''); ?></span>
						</div>
					</div>

					<!-- Sonlanma Tarihi -->
					<div class="audit-row" style="display: flex; align-items: flex-start; gap: 12px;">
						<div class="audit-icon" style="color: #ef4444; font-size: 14px; margin-top: 2px;"><i class="fa fa-calendar-times-o"></i></div>
						<div style="display: flex; flex-direction: column;">
							<span style="font-size: 11px; color: #64748b; font-weight: 500;">Sonlanma Tarihi</span>
							<span style="font-size: 13px; color: #334155; font-weight: 500;"><?php echo htmlspecialchars($as["lastdate"] ?? ''); ?></span>
						</div>
					</div>

					<!-- Tamamlanma Tarihi -->
					<?php if ($as["statu"] == 1) { ?>
						<div class="audit-row" style="display: flex; align-items: flex-start; gap: 12px;">
							<div class="audit-icon" style="color: #10b981; font-size: 14px; margin-top: 2px;"><i class="fa fa-calendar-check-o"></i></div>
							<div style="display: flex; flex-direction: column;">
								<span style="font-size: 11px; color: #64748b; font-weight: 500;">Tamamlanma Tarihi</span>
								<span style="font-size: 13px; color: #334155; font-weight: 500;"><?php echo htmlspecialchars($as["okeydate"] ?? ''); ?></span>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>