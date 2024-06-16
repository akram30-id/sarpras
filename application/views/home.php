<section class="section dashboard">
	<div id="url" data-url_statistic="<?= $statistic ?>"></div>
	<div class="row">

		<!-- Left side columns -->
		<div class="col-lg-12">
			<div class="row">

			<!-- Users Card -->
			<div class="col-xxl-3 col-md-6">
				<div class="card info-card sales-card">

				<div class="card-body">
					<h5 class="card-title">User</h5>

					<div class="d-flex align-items-center">
					<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
						<i class="bi bi-person-fill"></i>
					</div>
					<div class="ps-3">
						<h6><?= $total_user; ?></h6>
						<span class="text-success small pt-1 fw-bold">Active</span> <span class="text-muted small pt-2 ps-1">accounts</span>
					</div>
					</div>
				</div>

				</div>
			</div><!-- End Users Card -->

			<!-- Ekskul Card -->
			<div class="col-xxl-3 col-md-6">
				<div class="card info-card revenue-card">

				<div class="card-body">
					<h5 class="card-title">Ekskul</h5>

					<div class="d-flex align-items-center">
					<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
						<i class="bi bi-palette-fill"></i>
					</div>
					<div class="ps-3">
						<a href="<?= base_url('ekskul/master') ?>">
							<h6><?= $total_ekskul ?></h6>
						</a>
						<span class="text-success small pt-1 fw-bold">Active</span> <span class="text-muted small pt-2 ps-1">ekskul</span>

					</div>
					</div>
				</div>

				</div>
			</div><!-- End Ekskul Card -->

			<!-- Submission Area Card -->
			<div class="col-xxl-3 col-xl-12">

				<div class="card info-card customers-card">

				<div class="card-body">
					<h5 class="card-title">Area Approval</h5>

					<div class="d-flex align-items-center">
					<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
						<i class="bi bi-hourglass-split"></i>
					</div>
					<div class="ps-3">
						<a href="<?= base_url('area/approve') ?>">
							<h6><?= $total_pending_area ?></h6>
						</a>
						<span class="text-danger small pt-1 fw-bold">Pending</span> <span class="text-muted small pt-2 ps-1">approval</span>

					</div>
					</div>

				</div>
				</div>

			</div><!-- End Submission Area Card -->

			<!-- Submission Item Card -->
			<div class="col-xxl-3 col-xl-12">

				<div class="card info-card customers-card">

				<div class="card-body">
					<h5 class="card-title">Item Approval</h5>

					<div class="d-flex align-items-center">
					<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
						<i class="bi bi-tools"></i>
					</div>
					<div class="ps-3">
						<a href="<?= base_url('item/approve') ?>">
							<h6><?= $total_pending_item ?></h6>
						</a>
						<span class="text-danger small pt-1 fw-bold">Pending</span> <span class="text-muted small pt-2 ps-1">approval</span>

					</div>
					</div>

				</div>
				</div>

			</div><!-- End Submission Item Card -->

			<?php 
			
				// echo '<pre>';
				// print_r($statistic['user']);
				// return;
			
			?>

			<!-- Reports -->
			<div class="col-12">
				<div class="card">

				<div class="card-body">
					<h5 class="card-title">Reports <span>/Month</span></h5>

					<!-- Line Chart -->
					<div id="reportsChart"></div>

					<script>
						const url_statistic = $("#url").data("url_statistic");
						$.ajax({
							type: "GET",
							url: url_statistic,
							dataType: "json",
							success: function (response) {
								document.addEventListener("DOMContentLoaded", () => {
								new ApexCharts(document.querySelector("#reportsChart"), {
								series: [{
									name: 'Users',
									// data: [31, 40, 28, 51, 42, 82, 56],
									data: response.user,
								}, {
									name: 'Ekskul',
									// data: [11, 32, 45, 32, 34, 52, 41]
									data: response.ekskul,
								}, {
									name: 'Item Booked',
									// data: [15, 11, 32, 18, 9, 24, 11]
									data: response.submission_item,
								},
								{
									name: 'Area Booked',
									// data: [15, 11, 32, 18, 9, 24, 11]
									data: response.submission_area,
								}],
								chart: {
									height: 350,
									type: 'area',
									toolbar: {
									show: false
									},
								},
								markers: {
									size: 4
								},
								colors: ['#4154f1', '#2eca6a', '#ff771d'],
								fill: {
									type: "gradient",
									gradient: {
									shadeIntensity: 1,
									opacityFrom: 0.3,
									opacityTo: 0.4,
									stops: [0, 90, 100]
									}
								},
								dataLabels: {
									enabled: false
								},
								stroke: {
									curve: 'smooth',
									width: 2
								},
								xaxis: {
									type: 'category',
									categories: ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER']
								},
								tooltip: {
									x: {
									format: 'dd/MM/yy HH:mm'
									},
								}
								}).render();
							});	
							}
						});
					</script>
					<!-- End Line Chart -->

				</div>

				</div>
			</div><!-- End Reports -->

			</div>
		</div><!-- End Left side columns -->

	</div>
</section>
