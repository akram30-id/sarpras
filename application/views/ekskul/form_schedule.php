<?php
$days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];

?>

<div id="urlSearch" data-area_url="<?= $findArea ?>"></div>
<div class="row justify-content-center">
	<div class="col-sm-8">
		<?php $this->load->view('flashdata'); ?>
		<div class="card boder-0 shadow p-3" style="border-radius: 16px;">
			<div class="card-body">
				<h3 class="text-center mt-4 mb-5"><?= $title ?></h3>
				<form class="form-validation" method="POST" action="<?= base_url('ekskul/do_insert_schedule/' . $ekskul->ekskul_code) ?>" novalidate>
					<div class="mb-3">
						<label for="ekskul" class="form-label">Eksul</label>
						<input type="text" class="form-control" id="ekskul" name="ekskul" value="<?= $ekskul->ekskul_code . ' - ' . $ekskul->ekskul_name ?>" disabled required>
					</div>
					<div class="mb-3">
						<label for="pic" class="form-label">PIC Ekskul</label>
						<input type="text" class="form-control" id="pic" name="pic" value="<?= $ekskul->pic . ' - ' . $ekskul->name ?>" disabled required>
					</div>
					<div class="mb-1">
						<label for="find-area" class="form-label">Ruangan</label>
						<input type="text" class="form-control" id="find-area" name="find-area">
					</div>
					<div class="mb-3">
						<input type="text" class="form-control" id="area" disabled>
						<input type="hidden" class="form-control" id="send-area" name="area">
					</div>
					<div class="mb-3">
						<label for="days" class="form-label">Pilih Hari</label>
						<select class="form-select" id="day" name="day" aria-label="Default select example">
							<option disabled>- PILIH HARI -</option>
							<?php for ($i = 0; $i <= 6; $i++) { ?>
								<option value="<?= $i ?>"><?= $days[$i] ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="row">
						<div class="col-sm-4">
							<div class="mb-3">
								<label for="start_clock" class="form-label">Dari Jam</label>
								<input type="time" class="form-control" id="start_clock" name="start_clock" required>
								<div class="invalid-feedback">
									Jam Mulai wajib diisi.
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="mb-3">
								<label for="end_clock" class="form-label">Sampai Jam</label>
								<input type="time" class="form-control" id="end_clock" name="end_clock" required>
								<div class="invalid-feedback">
									Jam Selesai wajib diisi.
								</div>
							</div>
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
	}).focus(function () {
		$(this).autocomplete('search', '')
	});
</script>