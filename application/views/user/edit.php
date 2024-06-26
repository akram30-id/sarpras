<div class="row justify-content-center">
	<div class="col-sm-6">
		<?php $this->load->view('flashdata'); ?>
		<div class="card boder-0 shadow p-3" style="border-radius: 16px;">
			<div class="card-body">
				<h3 class="text-center mt-4 mb-5"><?= $title ?></h3>
				<form class="needs-validation" novalidate method="POST" action="<?= base_url('user/do_update/' . $user->username) ?>" enctype="multipart/form-data">
					<div class="mb-3">
						<label for="name" class="form-label">Nama</label>
						<input type="text" class="form-control" id="name" name="name" value="<?= $user->name ?>" required>
						<div class="invalid-feedback">
							Nama User wajib diisi.
						</div>
					</div>
					<div class="mb-3">
						<label for="user_code" class="form-label">NIP/NIS/NIK</label>
						<input type="text" class="form-control" id="user_code" name="user_code" value="<?= $user->username ?>" required disabled>
						<div class="invalid-feedback">
							Nomor Identitas User wajib diisi.
						</div>
					</div>
					<?php if(!isset($edit_my_self)) { ?>
						<div class="mb-3">
							<label for="role" class="form-label">Role User</label>
							<select class="form-select" name="role" required>
								<option selected value="" disabled>- PILIH ROLE -</option>
								<?php foreach ($roles as $s) : ?>
									<option value="<?= $s->role ?>" <?php if($s->role == $user->role) echo 'selected="selected"' ?>><?= strtoupper($s->role_name) ?></option>
								<?php endforeach; ?>
							</select>
							<div class="invalid-feedback">
								Role User wajib diisi.
							</div>
						</div>
					<?php } ?>
					<div class="mb-3">
						<label for="birth_date" class="form-label">Tanggal Lahir</label>
						<input type="date" class="form-control" id="birth_date" name="birth_date" value="<?= $user->birth_date ?>" required>
						<div class="invalid-feedback">
							Tanggal Lahir wajib diisi.
						</div>
					</div>
					<div class="mb-3">
						<label for="born_at" class="form-label">Tempat Lahir</label>
						<input type="text" class="form-control" id="born_at" name="born_at" value="<?= $user->born_at ?>" required>
						<div class="invalid-feedback">
							Tempat Lahir wajib diisi.
						</div>
					</div>
					<div class="mb-3">
						<label for="avatar" class="form-label">Avatar</label>
						<input type="file" name="avatar" class="form-control">
					</div>
					<div id="image-preview" class="row my-3">
						<label for="current_image" class="form-label">Current Image</label>
						<div class="col-md-3">
							<img src="<?= $user->photo ?? base_url('assets/img/apple-touch-icon.png') ?>" class="rounded-circle" width="120px" height="120px;" alt="Preview Image">
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
	$('input[name="avatar"]').on('change', function() {
		const files = $(this)[0].files;
		const file = files[0];
		var reader = new FileReader();
			reader.onload = function(e) {
				$('#image-preview').html(`
					<div class="col-md-3">
						<img src="${e.target.result}" class="rounded-circle" width="120px" height="120px;" alt="Preview Image">
					</div>
				`);
			}
		reader.readAsDataURL(file);
	});

</script>
