<div class="row justify-content-center">
	<div class="col-sm-6">
		<?php $this->load->view('flashdata'); ?>
		<div class="card boder-0 shadow p-3" style="border-radius: 16px;">
			<div class="card-body">
				<h3 class="text-center mt-4 mb-5">Report Item</h3>
				<form class="needs-validation" novalidate method="POST" action="<?= base_url('item/do_report/' . $request_code) ?>" enctype="multipart/form-data">
					<div class="mb-3">
						<label for="report_type" class="form-label">Tipe Laporan</label>
						<select class="form-select" name="type" id="type" required>
							<option disabled>- Pilih Tipe Laporan -</option>
							<option value="broken">Barang Rusak</option>
							<option value="lost">Barang Hilang</option>
						</select>
						<div class="invalid-feedback">
							Tipe Laporan wajib dipilih.
						</div>
					</div>
					<div class="mb-3">
						<label for="broken_date" class="form-label">Tanggal Kejadian</label>
						<input type="date" name="broken_date" class="form-control" required>
						<div class="invalid-feedback">
							Tanggal Kejadian wajib diisi.
						</div>
					</div>
					<div class="mb-3">
						<label for="reason" class="form-label">Alasan</label>
						<textarea type="text" class="form-control" id="reason" rows="5" name="reason" required></textarea>
						<div class="invalid-feedback">
							Alasan wajib diisi.
						</div>
					</div>
					<div class="mb-1">
						<label for="evidence" class="form-label">Bukti</label>
						<input type="file" name="evidence" class="form-control">
					</div>

					<div id="image-preview" class="row my-3"></div>

					<div class="row justify-content-end mt-5" style="margin-right: 8px;">
						<div class="col-sm-2">
							<div class="d-grid">
								<button type="submit" id="submit" class="btn btn-primary btn-block">Submit</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	
	$('input[name="evidence"]').on('change', function() {
		const files = $(this)[0].files;
		for (var i = 0; i < files.length; i++) {
			if (i >= 3) {
				continue;
			}

			var file = files[i];
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#image-preview').append(`
					<div class="col-md-3">
						<img src="${e.target.result}" width="100%" alt="Preview Image">
					</div>
				`);
			}
			reader.readAsDataURL(file);
		}
	});

</script>
