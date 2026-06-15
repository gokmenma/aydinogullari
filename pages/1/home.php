<?php

use App\Model\ServiceModel;

$services = new ServiceModel();

use App\Helper\Date;

$projectquery = $ac->prepare('SELECT COUNT(*) FROM projects WHERE pstatu=?');
$projectquery->execute(array(1));
$contproject = $projectquery->fetchColumn();

$offerquery = $ac->prepare('SELECT COUNT(*) FROM offers WHERE statu = ?');
$offerquery->execute(array(1));
$contoffer = $offerquery->fetchColumn();

$offall = $ac->prepare('SELECT COUNT(*) FROM offers ');
$offall->execute();
$ofacs = $offall->fetchColumn();

$projectquerys = $ac->prepare('SELECT COUNT(*) FROM projects WHERE pstatu = ? or pstatu = ?');
$projectquerys->execute(array(1, 2));
$contokproject = $projectquerys->fetchColumn();

// $notqx = $ac->prepare('SELECT COUNT(*) FROM notes ');
// $notqx->execute();
// $nots = $notqx->fetchColumn();

// $todos = $ac->prepare('SELECT COUNT(*) FROM todolist WHERE okey != ?');
// $todos->execute(array(1));
// $tdq = $todos->fetchColumn();

$allpq = $ac->prepare('SELECT COUNT(*) FROM projects ');
$allpq->execute();
$pqr = $allpq->fetchColumn();

$pqr = $pqr <= 0 ? 1 : $pqr;
$ofacs = $ofacs <= 0 ? 1 : $ofacs;

// @$prgs = $contproject / $contokproject * 100;

// @$st1rep = @$contokproject / @$pqr * 100;
// @$st1reps = @$contproject / @$pqr * 100;
// @$of1rep = @$contoffer / @$ofacs * 100;

?>



<div class="main-container" id="content">
	<div id="maincontainer" class="content pd-10 pd-ltr-10 xs-pd-10-10 animate-fade-in">
		<div class="row m-0 mb-4">
			<!-- Devam eden Servisler -->
			<div class="col-lg-3 col-md-6 col-sm-12 mb-4">
				<div class="dashboard-card card-blue">
					<div class="d-flex justify-content-between align-items-start">
						<div>
							<span class="d-block text-muted font-14 weight-500 mb-1">Devam Eden Servisler</span>
							<span class="no text-blue weight-700 font-30">
								<?php echo $contproject != 0 ? $contproject : '0'; ?>
							</span>
						</div>
						<div class="icon bg-blue text-white box-shadow">
							<i class="fa fa-briefcase"></i>
						</div>
					</div>
					<div class="project-info-progress mt-4">
						<div class="d-flex justify-content-between text-muted font-12 mb-1">
							<span>Biten: 0</span>
							<span>Toplam: <?php echo $contokproject; ?></span>
						</div>
						<div class="progress" style="height: 6px; border-radius: 3px;">
							<div class="progress-bar bg-blue progress-bar-striped progress-bar-animated"
								role="progressbar" style="width: <?php echo $prgs; ?>%;" aria-valuenow="<?php echo $prgs; ?>"
								aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Bekleyen Teklifler -->
			<div class="col-lg-3 col-md-6 col-sm-12 mb-4">
				<div class="dashboard-card card-green">
					<div class="d-flex justify-content-between align-items-start">
						<div>
							<span class="d-block text-muted font-14 weight-500 mb-1">Bekleyen Teklif Sayısı</span>
							<span class="no text-light-green weight-700 font-30">
								<?php echo $contoffer > 0 ? $contoffer : '0'; ?>
							</span>
						</div>
						<div class="icon bg-light-green text-white box-shadow">
							<i class="fa fa-handshake-o"></i>
						</div>
					</div>
					<div class="project-info-progress mt-4">
						<div class="d-flex justify-content-between text-muted font-12 mb-1">
							<span>Kazanılan: 0</span>
							<span>Toplam: <?php echo $ofacs; ?></span>
						</div>
						<div class="progress" style="height: 6px; border-radius: 3px;">
							<div class="progress-bar bg-light-green progress-bar-striped progress-bar-animated"
								role="progressbar" style="width: <?php echo $of1rep; ?>%;" aria-valuenow="<?php echo $of1rep; ?>"
								aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Tamamlanmayan Görev -->
			<div class="col-lg-3 col-md-6 col-sm-12 mb-4">
				<div class="dashboard-card card-orange">
					<?php
					$gq = $ac->prepare('SELECT COUNT(*) FROM missions WHERE statu = ?');
					$gq->execute(array(0));
					$ab = $gq->fetchColumn();

					$gq1 = $ac->prepare('SELECT COUNT(*) FROM missions ');
					$gq1->execute();
					$ab2 = $gq1->fetchColumn();

					$ab2 = $ab2 != 0 ? $ab2 : 1;
					@$oran = ($ab2 - $ab) / $ab2 * 100;
					?>
					<div class="d-flex justify-content-between align-items-start">
						<div>
							<span class="d-block text-muted font-14 weight-500 mb-1">Tamamlanmayan Görev</span>
							<span class="no text-light-orange weight-700 font-30">
								<?php echo $ab; ?>
							</span>
						</div>
						<div class="icon bg-light-orange text-white box-shadow">
							<i class="fa fa-list-alt"></i>
						</div>
					</div>
					<div class="project-info-progress mt-4">
						<div class="d-flex justify-content-between text-muted font-12 mb-1">
							<span>Kalan: 0</span>
							<span>Toplam: <?php echo $ab2; ?></span>
						</div>
						<div class="progress" style="height: 6px; border-radius: 3px;">
							<div class="progress-bar bg-light-orange progress-bar-striped progress-bar-animated"
								role="progressbar" style="width: <?php echo $oran; ?>%;" aria-valuenow="<?php echo $oran; ?>"
								aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Yapılacaklar Listesi -->
			<div class="col-lg-3 col-md-6 col-sm-12 mb-4">
				<div class="dashboard-card card-purple">
					<div class="d-flex justify-content-between align-items-start">
						<div>
							<span class="d-block text-muted font-14 weight-500 mb-1">Yapılacaklar Listesi</span>
							<span class="no text-light-purple weight-700 font-30">
								<?php echo $tdq ?? 0; ?>
							</span>
						</div>
						<div class="icon bg-light-purple text-white box-shadow">
							<i class="fa fa-podcast"></i>
						</div>
					</div>
					<div class="project-info-progress mt-4">
						<div class="d-flex justify-content-between text-muted font-12 mb-1">
							<span>Durum: Kalan</span>
							<span><?php echo $tdxs ?? 0; ?></span>
						</div>
						<div class="progress" style="height: 6px; border-radius: 3px;">
							<div class="progress-bar bg-light-purple progress-bar-striped progress-bar-animated"
								role="progressbar" style="width: 100%;" aria-valuenow="100"
								aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
				</div>
			</div>
		</div>



		<div class="row m-0 clearfix d-block">
			<div class="main-card col-sm-12 mb-5 pt-0">
				<div class="bg-white premium-section-card box-shadow height-100-p">
					<h4 class="mb-4 text-blue weight-600">15 günlük Servis Listesi</h4>
					<?php
					$bekliyor = $services->getServiceBackColour(15)->colour;
					$calisiyor = $services->getServiceBackColour(16)->colour;
					$tamamlandi = $services->getServiceBackColour(17)->colour;

					?>
					<div class="mb-4">
						<span class="status-pill text-dark" style="background-color:<?php echo $bekliyor ?>; border-left: 4px solid rgba(0,0,0,0.15)">Bekliyor</span>
						<span class="status-pill text-dark" style="background-color:<?php echo $calisiyor ?>; border-left: 4px solid rgba(0,0,0,0.15)">Çalışıyor</span>
						<span class="status-pill text-dark" style="background-color:<?php echo $tamamlandi ?>; border-left: 4px solid rgba(0,0,0,0.15)">Tamamlandı</span>
					</div>

					<div class="table-container" id="container">
						<table class="table table-bordered table-striped">
							<thead>
								<tr class="planner-header">
									<!-- Bugünden itibaren 7 günün adını yaz -->
									<?php
									$daysOfWeek = [];
									$dates = [];
									$date = new DateTime();
									$date->modify('-5 days');  // 10 gün geri git
									for ($i = 0; $i < 15; $i++) {
										$daysOfWeek[] = $date->format('l');  // Gün adını al
										$dates[] = $date->format('Y-m-d');  // Tarihi al
										$date->modify('+1 day');  // Bir gün ileri git
									}
									foreach ($daysOfWeek as $day) {
										echo '<th style="width:6.66%">' . Date::getDayNames($day) . '</th>';
									}
									?>
								</tr>
								<tr class="planner-dates-row">
									<?php
									foreach ($dates as $date) {
										echo '<th style="width:6.66%">' . $date . '</th>';
									}
									?>
								</tr>
							</thead>
							<tbody>
								<tr>
									<?php
									foreach ($dates as $date) {
										$service = $services->getDailyServiceList($date);
										echo '<td style="width:6.66%">';
										echo '<ul class="p-0 m-0">';
										foreach ($service as $item) {
											$item->psecond_date = (new DateTime($item->psecond_date))->format('Y-m-d');
											if ($item->psecond_date != null && $item->psecond_date == $date) {
												$border = 'border: 2px solid #8062D6;';
											} else {
												$border = '';
											}
											$service_back_colour = $services->getServiceBackColour($item->pstatu);
											?>

											<li class="list-item"
												style="background:<?php echo $service_back_colour->colour . ";" . $border ?>; color: #1e293b;"
												data-tooltip="SERVİSİ GÖRÜNTÜLE">
												<a href="index.php?p=services&id=<?php echo $item->id ?>">
													<div class="row m-0" style="font-size: 13px; cursor:pointer">
														<div class="col-12 p-0">
															<strong style="color: #0f172a;"><?php echo $item->service_number; ?></strong>
															<p class="m-0 weight-500 text-truncate" style="max-width: 170px;"><?php echo $item->title ?></p>
															<p class="m-0 text-muted font-12">
																<?php
																$authors = explode('|', $item->pauthors);
																foreach ($authors as $key => $author) {
																	if ($key > 0) {
																		echo ', ';
																	}
																	echo getUsername($author);
																}
																?>
															</p>
															<div class="mt-2 text-truncate weight-600" style="max-width: 170px; color: #475569; font-size: 12px;">
																<?php echo shorted($item->firma_adi  , 35); ?>
															</div>
														</div>
													</div>
												</a>
											</li>
											<?php
										}
										echo '</ul>';
										echo '</td>';
									}
									?>
								</tr>
							</tbody>
						</table>
					</div>

				</div>
			</div>
		</div>


		<?php
		if (permtrue('offerview')) {
			?>
			<div class="row m-0 clearfix">
				<div class="main-card col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-5 pt-0">
					<div class="bg-white premium-section-card box-shadow height-100-p">
						<h4 class="mb-30 weight-600"><a data-tooltip="Tüm Teklifleri Gör" href="index.php?p=offers" class="text-blue">Son 3 Teklif</a></h4>
						
						<?php
						$sql = $ac->prepare('SELECT * FROM offers ORDER BY id DESC LIMIT 3');
						$sql->execute();
						while ($result = $sql->fetch(PDO::FETCH_ASSOC)) {
							$findc = $ac->prepare('SELECT * FROM customers WHERE id = ?');
							$findc->execute(array($result['cid']));
							$ffc = $findc->fetch(PDO::FETCH_ASSOC);
							?>
							<a href="index.php?p=offers/offer-manage&id=<?php echo $result['id'] ?>" class="custom-list-group-item" style="border-left-color: #10b981;">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<h6 class="mb-1 font-16"><?php echo $ffc['company'] ?></h6>
										<small class="d-block mb-1 text-muted"><?php echo $result['company_authors'] ?></small>
										<span class="text-muted font-12"><i class="fa fa-calendar mr-1"></i> <?php echo $result['reg_date'] ?></span>
									</div>
									<div class="text-right">
										<span class="d-block weight-700 font-16 text-success"><?php echo tlFormat($result['total_price']) ?></span>
										<small class="text-muted"><i class="fa fa-user mr-1"></i> <?php echo getUsername($result['creativer']) ?></small>
									</div>
								</div>
							</a>
						<?php } ?>
					</div>
				</div>

				<div class="main-card col-xl-6 col-lg-12 col-md-12 col-sm-12 mb-5 pt-0">
					<div class="bg-white premium-section-card box-shadow height-100-p">
						<h4 class="mb-30 weight-600"><a data-tooltip="Tüm Servisleri Gör" href="index.php?p=services" class="text-blue">Son Eklenen Servisler</a></h4>
						
						<?php
						$sql = $ac->prepare('SELECT * FROM projects ORDER BY id DESC LIMIT 4');
						$sql->execute();
						while ($result = $sql->fetch(PDO::FETCH_ASSOC)) {
							$findc = $ac->prepare('SELECT * FROM customers WHERE id = ?');
							$findc->execute(array($result['pcid']));
							$ffc = $findc->fetch(PDO::FETCH_ASSOC);

							$st = $ac->prepare('SELECT * FROM units WHERE id = ?');
							$st->execute(array($result['servicestype']));
							$servicetype = $st->fetch(PDO::FETCH_ASSOC);
							?>
							<a href="index.php?p=services&sid=<?php echo $result['id'] ?>" class="custom-list-group-item" style="border-left-color: #3b82f6;">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<h6 class="mb-1 font-16"><?php echo $ffc['company'] . ' / ' . $servicetype['title'] ?></h6>
										<small class="d-block mb-1 text-muted"><?php echo $servicetype['title'] ?></small>
										<span class="text-muted font-12"><i class="fa fa-calendar mr-1"></i> <?php echo $result['pstart_date'] ?></span>
									</div>
									<div class="text-right">
										<small class="d-block text-muted"><i class="fa fa-user mr-1"></i> <?php echo getUsername($result['pcreativer']) ?></small>
									</div>
								</div>
							</a>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php
		}
		?>



		<div class="row m-0">
			<!-- Ekip -->
			<div class="main-card col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-5 pt-0">
				<div class="bg-white premium-section-card box-shadow height-100-p">
					<h4 class="mb-30 weight-600"><a href="index.php?p=all-users" class="text-blue">Ekip Üyeleri</a></h4>
					<div class="table-responsive">
						<table class="table table-hover table-borderless align-middle">
							<thead>
								<tr class="border-bottom" style="color: #64748b; font-size: 13px;">
									<th class="pb-3 pl-0">Kullanıcı</th>
									<th class="pb-3 text-right pr-0">Görev (Aktif/Toplam)</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$pqr = $ac->prepare('SELECT * FROM perms');
								$pqr->execute();
								while ($pp = $pqr->fetch(PDO::FETCH_ASSOC)) {
									?>
									<tr class="table-light">
										<td colspan="2" class="pl-2 py-2 font-13 weight-600 text-secondary" style="background: #f8fafc;">
											<i class="fa fa-folder-open-o mr-2"></i> <?php echo $pp['p_title']; ?>
										</td>
									</tr>

									<?php
									$upq = $ac->prepare('SELECT * FROM users WHERE permission = ?');
									$upq->execute(array($pp['id']));
									while ($uu = $upq->fetch(PDO::FETCH_ASSOC)) {
										$gorevq = $ac->prepare('SELECT COUNT(*) FROM missions WHERE authors = ? AND statu = ?');
										$gorevq->execute(array($uu['id'], 1));
										$sg1 = $gorevq->fetchColumn();

										$gorevq2 = $ac->prepare('SELECT COUNT(*) FROM missions WHERE authors = ? ');
										$gorevq2->execute(array($uu['id']));
										$sg2 = $gorevq2->fetchColumn();
										?>
										<tr class="border-bottom">
											<td class="pl-4 py-3">
												<div class="d-flex align-items-center">
													<div class="avatar-sm mr-3 bg-light-green text-success d-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; font-weight: 600;">
														<?php echo strtoupper(substr($uu['username'], 0, 2)); ?>
													</div>
													<span class="weight-500 text-dark"><?php echo $uu['username']; ?></span>
												</div>
											</td>
											<td class="text-right py-3 pr-4 font-14 weight-600 text-secondary">
												<span class="badge badge-pill badge-primary px-3 py-1"><?php echo $sg1 . ' / ' . $sg2; ?></span>
											</td>
										</tr>
										<?php
									}
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- Yapılacaklar Listesi -->
			<div class="main-card col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-10 pt-0">
				<div class="bg-white premium-section-card box-shadow height-100-p">
					<h4 id="logs" class="mb-30 weight-600"><a href="#logs" class="text-blue">Yapılacaklar Listesi</a></h4>

					<?php
					$toq = $ac->prepare('SELECT * FROM todolist WHERE okey = ?');
					$toq->execute(array(0));
					while ($tto = $toq->fetch(PDO::FETCH_ASSOC)) {
						?>
						<a href="index.php?p=task-edit&reg=true&id=<?php echo $tto['id']; ?>" class="custom-list-group-item" style="border-left-color: #8b5cf6;">
							<div class="d-flex justify-content-between align-items-center">
								<div>
									<h6 class="mb-1 font-16"><?php echo shorted($tto['title'], 50); ?></h6>
									<span class="text-muted font-12"><i class="fa fa-user-circle mr-1"></i> Oluşturan: <?php echo uset($tto['creativer'], 'username'); ?></span>
								</div>
								<div class="text-right">
									<span class="badge badge-pill badge-light text-secondary font-12"><i class="fa fa-clock-o mr-1"></i> <?php echo $tto['last_date']; ?></span>
								</div>
							</div>
						</a>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>