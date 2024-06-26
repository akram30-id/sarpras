<div class="row justify-content-center">
	<div class="col-sm-6">
		<?php $this->load->view('flashdata'); ?>
		<div class="card boder-0 shadow p-3" style="border-radius: 16px;">
			<div class="card-body">
				<h3 class="text-center mt-4 mb-5"><?= $title ?></h3>
				<form class="needs-validation" novalidate method="POST" action="<?= base_url('user/do_change') ?>" enctype="multipart/form-data">
					<div class="mb-3">
						<label for="username">Username</label>
						<input type="text" class="form-control" id="username" value="<?= $user ?>" name="username" disabled>
						<input type="hidden" class="form-control" id="user" value="<?= $user ?>" name="user">
					</div>
					<div class="mb-3">
						<label for="old-password">Password Lama</label>
						<input type="password" class="form-control" id="old-password" name="old-password" required>
						<div class="invalid-feedback">
							Password Lama wajib diisi
						</div>
					</div>
					<div class="mb-3">
						<label for="old-password">Password Baru</label>
						<input type="password" class="form-control" id="new-password" name="new-password" required>
						<div class="invalid-feedback">
							Password Baru wajib diisi
						</div>
					</div>
					<div class="mb-3">
						<label for="old-password">Konfirmasi Password</label>
						<input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
						<div class="invalid-feedback">
							Konfirmasi Password wajib diisi
						</div>
					</div>
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
</div>
