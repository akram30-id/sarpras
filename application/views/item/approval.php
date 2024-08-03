<div class="row">
	<div class="col-sm-10">
		<button class="btn btn-primary mb-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
			Cetak Laporan
		</button>
		<div class="collapse mb-3" id="collapseExample">
			<form method="post" action="<?= base_url('item/print_approval') ?>">
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
				<h3 class="text-center mt-4 mb-3">Approval Request Item</h3>
				<div class="row justify-content-center mb-2">
					<div class="col-sm-2 mb-1">
						<button class="btn btn-sm btn-secondary rounded-pill" id="btn-return">
							Pengembalian
						</button>
					</div>	
					<div class="col-sm-2 mb-1">
						<button class="btn btn-sm btn-primary rounded-pill" id="btn-request">
							Peminjaman
						</button>
					</div>
				</div>
				<table id="datatables" data-url="<?= $datatables ?>" data-urlRequest="<?= $datatablesRequest ?>" class="display" style="width:100%; font-size: 10pt;">
					<thead>
						<tr>
							<th>NO</th>
							<th>KODE REQUEST</th>
							<th>NAMA BARANG</th>
							<th>TIPE REQUEST</th>
							<th>STATUS APPROVAL</th>
							<th>QTY</th>
							<th>DESKRIPSI USER</th>
							<th>TGL REQUEST</th>
							<th>USER REQUEST</th>
							<th>TANDA TANGAN</th>
							<th>USER APPROVAL</th>
							<th>#####</th>
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

		$("#btn-return").click(function (e) { 
			// Check if the DataTable is already initialized and destroy it if necessary
			if ($.fn.DataTable.isDataTable('#datatables')) {
				$('#datatables').DataTable().clear().destroy();
			}

			const url = $("table#datatables").data('url');

			const tableReturn = new DataTable("table#datatables", {
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

			$(this).attr("class", "btn btn-sm btn-secondary rounded-pill");
			$("#btn-request").attr("class", "btn btn-sm btn-primary rounded-pill");

			tableReturn.draw();
		});

		$("#btn-request").on("click", function () { 
			// Check if the DataTable is already initialized and destroy it if necessary
			if ($.fn.DataTable.isDataTable('#datatables')) {
				$('#datatables').DataTable().clear().destroy();
			}

			const urlRequest = $("table#datatables").data('urlrequest');

			const tableRequest = new DataTable("table#datatables", {
				processing: true,
				serverSide: true,
				searchable: true,
				responsive: true,
				scrollX: true,
				ajax: {
					url: urlRequest,
					type: 'POST'
				}
			});

			$(this).attr("class", "btn btn-sm btn-secondary rounded-pill");
			$("#btn-return").attr("class", "btn btn-sm btn-primary rounded-pill");

			tableRequest.draw();
		});
	})
</script>
