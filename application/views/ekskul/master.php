<div class="row">
	<div class="col-sm-2">
		<a href="<?= base_url('ekskul/add') ?>" class="btn btn-primary rounded-pill">+ Tambah Ekskul</a>
	</div>
	<div class="col-sm-2">
		<button class="btn btn-primary mb-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
			Cetak Laporan
		</button>
	</div>
	<div class="col-sm-10">
		<div class="collapse mb-3" id="collapseExample">
			<form method="post" action="<?= base_url('ekskul/print_ekskul') ?>">
				<div class="row align-items-end">
					<div class="col-sm-3">
						<div class="mb-3">
							<label for="start">Dari Tanggal</label>
							<input type="date" name="start" id="start" class="form-control">
						</div>
					</div>
					<div class="col-sm-3">
						<div class="mb-3">
							<label for="end">Sampai Tanggal</label>
							<input type="date" name="end" id="end" class="form-control">
						</div>
					</div>
					<div class="col-sm-2">
						<div class="mb-3">
							<button type="submit" class="btn btn-primary">Cetak</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="row justify-content-center">
	<div class="col-sm-8">
		<?php $this->load->view('flashdata'); ?>
		<div class="card boder-0 shadow p-3" style="border-radius: 16px;">
			<div class="card-body">
				<h3 class="text-center mt-4 mb-5">Master Ekskul</h3>
				<table id="datatables" data-url="<?= $datatables ?>" class="display" style="width:100%; font-size: 10pt;">
					<thead>
						<tr>
							<th>###</th>
							<th>KODE EKSKUL</th>
							<th>NAMA EKSKUL</th>
							<th>PIC</th>
							<th>USER INPUT</th>
							<th>TGL DIBUAT</th>
							<th>ACTION</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		const url = $("table#datatables").data('url');

		const table = new DataTable("table#datatables", {
			processing: true,
			serverSide: true,
			searchable: true,
			responsive: true,
			scrollX: true,
			ajax: {
				url: url,
				type: 'POST'
			}
		});

		table.draw();
	})
</script>
