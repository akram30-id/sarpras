<?php $status = ['ACTIVE', 'INACTIVE'] ?>

<div class="row justify-content-center">
	<div class="col-sm-6">
		<?php $this->load->view('flashdata'); ?>
		<div class="card boder-0 shadow p-3" style="border-radius: 16px;">
			<div class="card-body">
				<h3 class="text-center mt-4 mb-5">Update Area #<?= $area_edit->area_code ?></h3>
				<form class="needs-validation" novalidate method="POST" action="<?= base_url('area/update/' . $area_edit->area_code) ?>">
					<div class="mb-3">
						<label for="area-name" class="form-label">Nama Area</label>
						<input type="text" class="form-control" id="area-name" name="area_name" value="<?= $area_edit->area_name ?>" required>
						<div class="invalid-feedback">
							Nama Area wajib diisi.
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="mb-3">
								<label for="open-hours" class="form-label">Jam Buka</label>
								<input type="time" class="form-control" id="open-hours" name="open_hours" value="<?= date('H:i', strtotime($area_edit->open_hours)) ?>" required>
								<div class="invalid-feedback">
									Jam Buka wajib diisi.
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="mb-3">
								<label for="close-hours" class="form-label">Jam Tutup</label>
								<input type="time" class="form-control" id="close-hours" name="close_hours" value="<?= date('H:i', strtotime($area_edit->close_hours)) ?>" required>
								<div class="invalid-feedback">
									Jam Tutup wajib diisi.
								</div>
							</div>
						</div>
					</div>
					<div class="mb-3">
						<label for="status" class="form-label">Status</label>
						<select class="form-select" name="status" required>
							<option selected value="" disabled>- PILIH STATUS -</option>
							<?php foreach($status as $s): ?>
								<option value="<?= $s ?>" <?php if($s == $area_edit->status) echo 'selected="selected"' ?>><?= $s ?></option>
							<?php endforeach; ?>
						</select>
						<div class="invalid-feedback">
							Status wajib diisi.
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
