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

<!-- <div class="" > -->
<div class="main-container" id="content">
	<div id="maincontainer" class="content pd-10 pd-ltr-10 xs-pd-10-10">
		<div class="row m-0">
			<div class="main-card col-lg-3 col-md-6 col-sm-12 mb-5">
				<div
					class="bg-white pd-20 d-flex align-content-between flex-wrap box-shadow border-radius-5 height-100-p">
					<div class="project-info clearfix">
						<div class="project-info-left">
							<div class="icon box-shadow bg-blue text-white">
								<i class="fa fa-briefcase"></i>
							</div>
						</div>
						<div class="project-info-right">
							<span class="no text-blue weight-500 font-24">
								<?php echo $contproject != 0 ? $contproject : '0'; ?>
							</span>
							<p class="weight-400 font-18">Devam eden Servisler branch kontrol edildi </p>
						</div>
					</div>
					<div class="project-info-progress">
						<div class="row clearfix">
							<div class="col-sm-6 text-muted weight-500">0</div>
							<div class="col-sm-6 text-right weight-500 font-14 text-muted">
								<?php echo $contokproject; ?>
							</div>
						</div>
						<div class="progress" style="height: 10px;">
							<div class="progress-bar bg-blue progress-bar-striped progress-bar-animated"
								role="progressbar" style="width: <?php echo $prgs; ?>%;" aria-valuenow="25"
								aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="main-card col-lg-3 col-md-6 col-sm-12 mb-5">
				<div
					class="bg-white pd-20 d-flex align-content-between flex-wrap box-shadow border-radius-5 height-100-p">
					<div class="project-info clearfix">
						<div class="project-info-left">
							<div class="icon box-shadow bg-light-green text-white">
								<i class="fa fa-handshake-o"></i>
							</div>
						</div>
						<div class="project-info-right">
							<span class="no text-light-green weight-500 font-24">
								<?php echo $contoffer > 0 ? $contoffer : '0'; ?>
							</span>
							<p class="weight-400 font-18">Bekleyen Teklif Sayısı</p>
						</div>
					</div>
					<div class="project-info-progress">
						<div class="row clearfix">
							<div class="col-sm-6 text-muted weight-500">
								<?php echo '0'; ?>
							</div>
							<div class="col-sm-6 text-right weight-500 font-14 text-muted">
								<?php echo $ofacs; ?>
							</div>
						</div>
						<div class="progress" style="height: 10px;">
							<div class="progress-bar bg-light-green progress-bar-striped progress-bar-animated"
								role="progressbar" style="width: <?php echo $of1rep; ?>%;" aria-valuenow="25"
								aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="main-card col-lg-3 col-md-6 col-sm-12 mb-5">
				<div
					class="bg-white pd-20 d-flex align-content-between flex-wrap box-shadow border-radius-5 height-100-p">
					<div class="project-info clearfix">
						<div class="project-info-left">
							<div class="icon box-shadow bg-light-orange text-white">
								<i class="fa fa-list-alt"></i>
							</div>
						</div>
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
						<div class="project-info-right">
							<span class="no text-light-orange weight-500 font-24">
								<?php echo $ab; ?>
							</span>
							<p class="weight-400 font-18">Tamamlanmayan Görev<br></p>
						</div>
					</div>
					<div class="project-info-progress">
						<div class="row clearfix">
							<div class="col-sm-6 text-muted weight-500">0</div>
							<div class="col-sm-6 text-right weight-500 font-14 text-muted">
								<?php echo $ab2; ?>
							</div>
						</div>
						<div class="progress" style="height: 10px;">
							<div class="progress-bar bg-light-orange progress-bar-striped progress-bar-animated"
								role="progressbar" style="width: <?php echo $oran; ?>%;" aria-valuenow="25"
								aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="main-card col-lg-3 col-md-6 col-sm-12 mb-5">
				<div
					class="bg-white pd-20 d-flex align-content-between flex-wrap box-shadow border-radius-5 m-0 height-100-p">
					<div class="project-info clearfix">
						<div class="project-info-left">
							<div class="icon box-shadow bg-light-purple text-white">
								<i class="fa fa-podcast"></i>
							</div>
						</div>
						<div class="project-info-right">
							<span class="no text-light-purple weight-500 font-24">
								<?php echo $tdq ?? 0; ?>
							</span>
							<p class="weight-400 font-18">Yapılacaklar Listesi</p>
						</div>
					</div>
					<div class="project-info-progress">
						<div class="row clearfix">
							<div class="col-sm-6 text-muted weight-500">
								<?php echo 'Kalan'; ?>
							</div>
							<div style="font-size:15px;" class="col-sm-6 text-right weight-500 font-14 text-muted">
								<?php echo $tdxs ?? 0; ?>
							</div>
						</div>
						<div class="progress" style="height: 10px;">
							<div class="progress-bar bg-light-purple progress-bar-striped progress-bar-animated"
								role="progressbar" style="width: 100%;" aria-valuenow="25" aria-valuemin="0"
								aria-valuemax="100"></div>
						</div>
					</div>
				</div>
			</div>

		</div>


		<style>
			.list-item {
				display: flex;
				justify-content: space-between;
				border-radius: 6px;
				background-color: cadetblue;
				margin: 4px;
				padding: 4px;
			}

			.list-item:hover,
			.list-item>label:hover {
				cursor: pointer !important;
			}

			.table td {
				vertical-align: top;
				min-width: 200px;

			}

			.table-container {
				display: block;
				overflow-x: auto;
			}

			table {
				width: 100%;
				border-collapse: collapse;
			}
		</style>



		<div class="row m-0 clearfix d-block">
			<div class="main-card col-sm-12 mb-5 pt-0">
				<div class="bg-white pd-20 box-shadow border-radius-5 height-100-p ">
					<h4 class="mb-4 text-blue">15 günlük Servis Listesi</h4>
					<?php
					$bekliyor = $services->getServiceBackColour(15)->colour;
					$calisiyor = $services->getServiceBackColour(16)->colour;
					$tamamlandi = $services->getServiceBackColour(17)->colour;

					?>
					<span class="badge p-2 mb-2" style="color:black;background-color:<?php echo $bekliyor ?>;">Bekliyor</span>
					<span class="badge p-2 mb-2" style="color:black;background-color:<?php echo $calisiyor ?>;">Çalışıyor</span>
					<span class="badge p-2 mb-2" style="color:black;background-color:<?php echo $tamamlandi ?>;">Tamamlandı</span>

					<div class="table-container" id="container">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
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
										echo '<th style="width:14.28%">' . Date::getDayNames($day) . '</th>';
									}
									?>
								</tr>
								<tr>
									<?php
									foreach ($dates as $date) {
										echo '<th style="width:14.28%">' . $date . '</th>';
									}
									?>
								</tr>
							</thead>
							<tbody>
								<tr>
									<?php
									foreach ($dates as $date) {
										$service = $services->getDailyServiceList($date);
										echo '<td style="width:14.28%">';
										echo '<ul>';
										foreach ($service as $item) {
											$item->psecond_date = (new DateTime($item->psecond_date))->format('Y-m-d');
											if ($item->psecond_date != null && $item->psecond_date == $date) {
												$border = 'border: 3px solid #8062D6;';
											} else {
												$border = '';
											}
											$service_back_colour = $services->getServiceBackColour($item->pstatu);
											?>

											<li class="list-item"
												style="background:<?php echo $service_back_colour->colour . ";" . $border ?>"
												data-tooltip="SERVİSİ GÖRÜNTÜLE">
												<a href="index.php?p=services&id=<?php echo $item->id ?>">
													<div class="row m-0" style="font-size: 14px;cursor:pointer">
														<div class="col-6 p-0">
															<?php echo $item->service_number; ?>
															<p><?php echo $item->title ?></p>
															<p>
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
														</div>
														<div class="col-6 p-0" style="overflow:hidden">
															<label for="" class="align-right"
																style="font-size: 14px;cursor:pointer">
																<?php echo shorted($item->firma_adi  , 40); ?>
															</label>
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
					<div class="bg-white pd-20 box-shadow border-radius-5 height-100-p ">
						<h4 class="mb-30 "><a data-tooltip="Tüm Teklifleri Gör" href="index.php?p=offers">Son 3 Teklif</a>
						</h4>
						<?php
						$sql = $ac->prepare('SELECT * FROM offers ORDER BY id DESC LIMIT 3');
						$sql->execute();
						while ($result = $sql->fetch(PDO::FETCH_ASSOC)) {
							?>

							<div class="list-group">
								<a href="index.php?p=offers/offer-manage&id=<?php echo $result['id'] ?>"
									class="list-group-item m-1 list-group-item-action flex-column align-items-start">
									<div class="row w-100 justify-content-between ml-0 pl-0">

										<div class="col-9">
											<h6 class="text-white">
												<?php
												$findc = $ac->prepare('SELECT * FROM customers WHERE id = ?');
												$findc->execute(array($result['cid']));
												$ffc = $findc->fetch(PDO::FETCH_ASSOC);
												?>
												<?php echo $ffc['company'] ?>
											</h6>
											<small class="mb-1">
												<?php echo $result['company_authors'] ?>
											</small>
											<p>
												<?php echo $result['reg_date'] ?>
											</p>


										</div>
										<div class="col-3 text-right m-0 p-0">
											<p class="mb-1">
												<?php echo tlFormat($result['total_price']) ?>
											</p>
											<p>
												<?php echo getUsername($result['creativer']) ?>
											</p>
										</div>
									</div>


								</a>

							</div>
						<?php } ?>


					</div>
				</div>
				<div class="main-card col-xl-6 col-lg-12 col-md-12 col-sm-12 mb-5 pt-0">
					<div class="bg-white pd-20 box-shadow border-radius-5 height-100-p">

						<h4><a data-tooltip="Tüm Servisleri Gör" href="index.php?p=services">Son Eklenen Servisler</a></h4>
						<br>
						<?php

						$sql = $ac->prepare('SELECT * FROM projects ORDER BY id DESC LIMIT 4');
						$sql->execute();
						while ($result = $sql->fetch(PDO::FETCH_ASSOC)) {
							// FİRMA ADI
							$findc = $ac->prepare('SELECT * FROM customers WHERE id = ?');
							$findc->execute(array($result['pcid']));
							$ffc = $findc->fetch(PDO::FETCH_ASSOC);

							// SERVİS TİPİ
							$st = $ac->prepare('SELECT * FROM units WHERE id = ?');
							$st->execute(array($result['servicestype']));
							$servicetype = $st->fetch(PDO::FETCH_ASSOC);

							?>


							<div class="list-group">
								<a href="index.php?p=services&sid=<?php echo $result['id'] ?>"
									class="list-group-item m-1 list-group-item-action flex-column align-items-start">
									<div class="w-100 justify-content-between">
										<h6 class="mb-1 text-white">
											<?php echo $ffc['company'] . ' / ' . $servicetype['title'] ?>
										</h6>
										<small>
											<?php echo $result['pstart_date'] ?>
										</small>

									</div>
									<p class="mb-1">
										<?php echo $servicetype['title'] ?>
									</p>
									<p>
										<?php echo getUsername($result['pcreativer']) ?>
									</p>

								</a>

							</div>
						<?php } ?>

						<!-- <div class="device-manage-progress-chart">

						<ul>
								<?php
								$ofq = $ac->prepare('SELECT * FROM inexps ORDER BY id DESC LIMIT 6');
								$ofq->execute();
								while ($offs = $ofq->fetch(PDO::FETCH_ASSOC)) {
									$pmxa = $ac->prepare('SELECT * FROM pay_methods WHERE id = ? ');
									$pmxa->execute(array($offs['pay_method']));
									$xxp = $pmxa->fetch(PDO::FETCH_ASSOC);
									if ($xxp['currency'] == 'tl') {
										$prx = '₺';
									} elseif ($xxp['currency'] == 'dollar') {
										$prx = '$';
									} elseif ($xxp['currency'] == 'euro') {
										$prx = '€';
									} else {
										$prx = '';
									}

									if ($offs['type'] == 'in') {
										?>
										<li class="clearfix">
											<div title="<?php echo $offs['descs']; ?>" class="device-name">
												<?php echo $offs['title']; ?>
											</div>
											<div class="device-progress">
												<div class="progress">
													<div title="<?php echo $offs['descs']; ?>"
														class="progress-bar bg-success border-radius-8" role="progressbar"
														aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"
														style="width: 100%;">
													</div>
												</div>
											</div>
											<div class="device-total">
												<?php echo '+' . $offs['pay'] . $prx; ?>
											</div>
										</li>
										<?php
									} else {
										?>
										<li class="clearfix">
											<div title="<?php echo $offs['descs']; ?>" class="device-name">
												<?php echo $offs['title']; ?>
											</div>
											<div class="device-progress">
												<div class="progress">
													<div title="<?php echo $offs['descs']; ?>"
														class="progress-bar bg-danger border-radius-8" role="progressbar"
														aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"
														style="width: 100%;">
													</div>
												</div>
											</div>
											<div class="device-total">
												<?php echo '-' . $offs['pay'] . $prx; ?>
											</div>
										</li>

										<?php
									}
								}
								?>

							</ul>
						</div> -->
					</div>
				</div>


			</div>
			<?php
		}
		?>



		<div class="row m-0">

			<div class="main-card col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-5 pt-0">
				<div class="bg-white pd-20 box-shadow border-radius-5 height-100-p">
					<h4 class="mb-30"><a href="index.php?p=all-users">Ekip</a></h4>
					<div class="clearfix device-usage-chart">

						<div class="width-20-p pull-left">
							<table style="width: 100%;">
								<thead>
									<tr>
										<th class="weight-700">
											<p>Kullanıcı</p>
										</th>
										<th class="text-right weight-700">
											<p>Görev</p>
										</th>



									</tr>
								</thead>
								<tbody>
									<?php
									$pqr = $ac->prepare('SELECT * FROM perms');
									$pqr->execute();
									while ($pp = $pqr->fetch(PDO::FETCH_ASSOC)) {
										?>
										<tr>
											<td width="70%">
												<p title="" class="weight-500 mb-5"><i class="fa fa-square text-black"></i>
													<?php echo $pp['p_title']; ?>
												</p>
											</td>
											<td class="text-right weight-400">&nbsp;</td>
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
											<tr>
												<td width="70%">
													<p class="weight-500 mb-5"><i style="margin-left:18px"
															class="fa fa-square text-green"></i>
														<?php echo $uu['username']; ?>
													</p>
												</td>
												<td class="text-right weight-400">
													<?php echo $sg1 . '/' . $sg2; ?>
												</td>
												<td class="text-right weight-400"></td>
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
			</div>


			<div class="main-card col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-10 pt-0">
				<div class="bg-white pd-20 box-shadow border-radius-5 height-100-p">
					<h4 id="logs" class="mb-20 text-blue">Yapılacaklar Listesi </h4>


					<?php
					$toq = $ac->prepare('SELECT * FROM todolist WHERE okey = ?');
					$toq->execute(array(0));
					while ($tto = $toq->fetch(PDO::FETCH_ASSOC)) {
						?>

						<div class="list-group">
							<a href="index.php?p=task-edit&reg=true&id=<?php echo $tto['id']; ?>"
								class="list-group-item m-1 list-group-item-action flex-column align-items-start">
								<div class="d-flex w-100 justify-content-between">
									<h6 class="mb-1 text-white">
										<?php echo uset($tto['creativer'], 'username'); ?>
									</h6>
									<small>
										<?php echo $tto['last_date']; ?>
									</small>
								</div>
								<p class="mb-1">
									<?php echo shorted($tto['title']); ?>
								</p>

							</a>
						</div>
					<?php } ?>

				</div>

			</div>
		</div>
	</div>
</div>