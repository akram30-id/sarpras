<div id="urlSearch" data-area_url="<?= $findArea ?>"></div>
<div class="row justify-content-center">
	<div class="col-sm-6">
		<?php $this->load->view('flashdata'); ?>
		<div class="card boder-0 shadow p-3" style="border-radius: 16px;">
			<div class="card-body">
				<h3 class="text-center mt-4 mb-5">Booking Area</h3>
				<form class="needs-validation" novalidate method="POST" action="<?= base_url('area/do_booking') ?>" enctype="multipart/form-data">
					<div class="mb-1">
						<label for="find-area" class="form-label">Area</label>
						<input type="text" class="form-control" id="find-area" name="find-area" required>
						<div class="invalid-feedback">
							Wajib pilih area.
						</div>
					</div>
					<div class="mb-3">
						<input type="text" class="form-control" id="area" disabled>
						<input type="hidden" class="form-control" id="send-area" name="area">
					</div>
					<div class="row mb-3">
						<div class="col-sm-6">
							<div class="mb-1">
								<label for="start_date" class="form-label">Dari Tanggal</label>
								<input type="date" class="form-control" id="start_date" name="start_date" required>
								<div class="invalid-feedback">
									Tanggal mulai wajib diisi.
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="mb-1">
								<label for="start_clock" class="form-label">Jam</label>
								<input type="time" class="form-control" id="start_clock" name="start_clock" required>
								<div class="invalid-feedback">
									Jam mulai wajib diisi.
								</div>
							</div>
						</div>
					</div>
					<div class="row mb-3">
						<div class="col-sm-6">
							<div class="mb-1">
								<label for="end_date" class="form-label">Sampai Tanggal</label>
								<input type="date" class="form-control" id="end_date" name="end_date" required>
								<div class="invalid-feedback">
									Tanggal berakhir wajib diisi.
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="mb-1">
								<label for="end_clock" class="form-label">Jam</label>
								<input type="time" class="form-control" id="end_clock" name="end_clock" required>
								<div class="invalid-feedback">
									Jam berakhir wajib diisi.
								</div>
							</div>
						</div>
					</div>
					<div class="mb-3">
						<label for="user_notes" class="form-label">Catatan</label>
						<textarea type="text" class="form-control" id="user_notes" rows="5" name="user_notes" required></textarea>
						<div class="invalid-feedback">
							Wajib pilih area.
						</div>
					</div>
					<div class="row justify-content-end mt-5" style="margin-right: 8px;">
						<div class="col-sm-2">
							<div class="d-grid">
								<button type="submit" class="btn btn-primary btn-block">Submit</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	const areaURL = $("#urlSearch").data('area_url');
	const userURL = $("#urlSearch").data('user_url');
	
	$("#find-area").autocomplete({
		source: function(request, response) {
			// Replace 'your_data_url' with the actual URL of your data source
			$.ajax({
				url: areaURL,
				dataType: 'json',
				type: "GET",
				data: {
					search: $("#find-area").val()
				},
				success: function(data) {
					response(data);
				}
			});
		},
		minLength: 0, // Show all data on focus
		select: function(event, ui) {
			$("#area").val(ui.item.value); // Append selected value
			$("#send-area").val(ui.item.value); // Append selected value
		}
	});

</script>
