<div class="row">
	<div class="col-sm-10">
		<div class="d-flex align-items-center">
			<a href="<?= base_url('area/form_book') ?>" class="btn btn-primary mb-4" style="margin-right: 8px;">
				Booking Ruangan
			</a>
			<button class="btn btn-primary mb-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
				Cetak Laporan
			</button>
		</div>
		<div class="collapse mb-3" id="collapseExample">
			<form method="post" action="<?= base_url('area/print_booking') ?>">
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
					<div class="col-sm-3">
						<div class="mb-3">
							<label for="status">Status Booking</label>
							<select class="form-select" name="status" aria-label="Default select example">
								<option disabled>- PILIH STATUS -</option>
								<?php $status = ['APPROVED', 'PENDING', 'REJECTED'] ?>
								<?php foreach ($status as $s){ ?>
									<option value="<?= $s ?>"><?= $s ?></option>
								<?php } ?>
								<option value="ALL">ALL</option>
							</select>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="mb-3">
							<button type="submit" class="btn btn-primary">Cetak	</button>
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
				<h3 class="text-center mt-4 mb-5">Data Ruangan Booking</h3>
				<table id="datatables" data-url="<?= $datatables ?>" class="display" style="width:100%; font-size: 10pt;">
					<thead>
						<tr>
							<th>NO</th>
							<th>SUBMISSION CODE</th>
							<th>STATUS</th>
							<th>AREA</th>
							<th>PEMOHON</th>
							<th>DARI TANGGAL</th>
							<th>SAMPAI TANGGAL</th>
							<th>NOTES</th>
							<th>PIC AREA</th>
							<th>TGL DIBUAT</th>
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
