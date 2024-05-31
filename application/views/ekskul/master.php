<a href="<?= base_url('ekskul/add') ?>" class="btn btn-primary rounded-pill">+ Tambah Ekskul</a>
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
