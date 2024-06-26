<!-- HANYA BOLEH DIAKSES OLEH PIC ATAU ADMIN -->
<?php if($this->session->user->role == 1 || $this->session->user->username == $ekskul->pic){ ?>
	<div class="col-sm-8">
		<div class="card boder-0 shadow p-3" style="border-radius: 16px;">
			<div class="card-body">
				<h3 class="text-center mt-4 mb-5">EKSKUL DETAIL</h3>
				<form class="needs-validation" novalidate method="POST" action="<?= base_url('ekskul/do_update/' . $ekskul_code) ?>">
					<div class="mb-1">
						<label for="find-pic" class="form-label">PIC</label>
						<input type="text" class="form-control" id="find-pic" name="find-pic" value="<?= $ekskul->pic . 
						' - ' . $ekskul->name ?>" <?php if($this->session->user->role != 1) echo 'disabled'; ?> required>
						<div class="invalid-feedback">
							PIC wajib diisi.
						</div>
					</div>
					<div class="mb-3">
						<input type="text" class="form-control" id="pic" value="<?= $ekskul->pic . 
						' - ' . $ekskul->name ?>" disabled>
						<input type="hidden" class="form-control" id="send-pic" value="<?= $ekskul->pic . 
						' - ' . $ekskul->name ?>" name="pic">
					</div>
					<div class="mb-3">
						<label for="ekskul_name" class="form-label">Nama Ekskul</label>
						<input type="text" class="form-control" id="ekskul_name" name="ekskul_name" value="<?= $ekskul->ekskul_name ?>" required>
						<div class="invalid-feedback">
							Nama Ekskul wajib diisi.
						</div>
					</div>
					<div id="image-preview" class="row my-3"></div>
					<div class="row justify-content-end mt-5" style="margin-right: 8px;">
						<div class="col-sm-2">
							<div class="d-grid">
								<button type="submit" class="btn btn-primary btn-block">Update</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>
