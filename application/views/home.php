<section class="section dashboard">
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

			<!-- Reports -->
			<div class="col-12">
				<div class="card">

				<div class="card-body">
					<h5 class="card-title">Reports <span>/Today</span></h5>

					<!-- Line Chart -->
					<div id="reportsChart"></div>

					<script>
					document.addEventListener("DOMContentLoaded", () => {
						new ApexCharts(document.querySelector("#reportsChart"), {
						series: [{
							name: 'Sales',
							data: [31, 40, 28, 51, 42, 82, 56],
						}, {
							name: 'Revenue',
							data: [11, 32, 45, 32, 34, 52, 41]
						}, {
							name: 'Customers',
							data: [15, 11, 32, 18, 9, 24, 11]
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
							type: 'datetime',
							categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
						},
						tooltip: {
							x: {
							format: 'dd/MM/yy HH:mm'
							},
						}
						}).render();
					});
					</script>
					<!-- End Line Chart -->

				</div>

				</div>
			</div><!-- End Reports -->

			<!-- Recent Sales -->
			<div class="col-12">
				<div class="card recent-sales overflow-auto">

				<div class="card-body">
					<h5 class="card-title">Recent Sales <span>| Today</span></h5>

					<table class="table table-borderless datatable">
					<thead>
						<tr>
						<th scope="col">#</th>
						<th scope="col">Customer</th>
						<th scope="col">Product</th>
						<th scope="col">Price</th>
						<th scope="col">Status</th>
						</tr>
					</thead>
					<tbody>
						<tr>
						<th scope="row"><a href="#">#2457</a></th>
						<td>Brandon Jacob</td>
						<td><a href="#" class="text-primary">At praesentium minu</a></td>
						<td>$64</td>
						<td><span class="badge bg-success">Approved</span></td>
						</tr>
						<tr>
						<th scope="row"><a href="#">#2147</a></th>
						<td>Bridie Kessler</td>
						<td><a href="#" class="text-primary">Blanditiis dolor omnis similique</a></td>
						<td>$47</td>
						<td><span class="badge bg-warning">Pending</span></td>
						</tr>
						<tr>
						<th scope="row"><a href="#">#2049</a></th>
						<td>Ashleigh Langosh</td>
						<td><a href="#" class="text-primary">At recusandae consectetur</a></td>
						<td>$147</td>
						<td><span class="badge bg-success">Approved</span></td>
						</tr>
						<tr>
						<th scope="row"><a href="#">#2644</a></th>
						<td>Angus Grady</td>
						<td><a href="#" class="text-primar">Ut voluptatem id earum et</a></td>
						<td>$67</td>
						<td><span class="badge bg-danger">Rejected</span></td>
						</tr>
						<tr>
						<th scope="row"><a href="#">#2644</a></th>
						<td>Raheem Lehner</td>
						<td><a href="#" class="text-primary">Sunt similique distinctio</a></td>
						<td>$165</td>
						<td><span class="badge bg-success">Approved</span></td>
						</tr>
					</tbody>
					</table>

				</div>

				</div>
			</div><!-- End Recent Sales -->

			<!-- Top Selling -->
			<div class="col-12">
				<div class="card top-selling overflow-auto">

				<div class="card-body pb-0">
					<h5 class="card-title">Top Selling <span>| Today</span></h5>

					<table class="table table-borderless">
					<thead>
						<tr>
						<th scope="col">Preview</th>
						<th scope="col">Product</th>
						<th scope="col">Price</th>
						<th scope="col">Sold</th>
						<th scope="col">Revenue</th>
						</tr>
					</thead>
					<tbody>
						<tr>
						<th scope="row"><a href="#"><img src="assets/img/product-1.jpg" alt=""></a></th>
						<td><a href="#" class="text-primary fw-bold">Ut inventore ipsa voluptas nulla</a></td>
						<td>$64</td>
						<td class="fw-bold">124</td>
						<td>$5,828</td>
						</tr>
						<tr>
						<th scope="row"><a href="#"><img src="assets/img/product-2.jpg" alt=""></a></th>
						<td><a href="#" class="text-primary fw-bold">Exercitationem similique doloremque</a></td>
						<td>$46</td>
						<td class="fw-bold">98</td>
						<td>$4,508</td>
						</tr>
						<tr>
						<th scope="row"><a href="#"><img src="assets/img/product-3.jpg" alt=""></a></th>
						<td><a href="#" class="text-primary fw-bold">Doloribus nisi exercitationem</a></td>
						<td>$59</td>
						<td class="fw-bold">74</td>
						<td>$4,366</td>
						</tr>
						<tr>
						<th scope="row"><a href="#"><img src="assets/img/product-4.jpg" alt=""></a></th>
						<td><a href="#" class="text-primary fw-bold">Officiis quaerat sint rerum error</a></td>
						<td>$32</td>
						<td class="fw-bold">63</td>
						<td>$2,016</td>
						</tr>
						<tr>
						<th scope="row"><a href="#"><img src="assets/img/product-5.jpg" alt=""></a></th>
						<td><a href="#" class="text-primary fw-bold">Sit unde debitis delectus repellendus</a></td>
						<td>$79</td>
						<td class="fw-bold">41</td>
						<td>$3,239</td>
						</tr>
					</tbody>
					</table>

				</div>

				</div>
			</div><!-- End Top Selling -->

			</div>
		</div><!-- End Left side columns -->

	</div>
</section>
