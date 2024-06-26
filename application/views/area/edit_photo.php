<div class="row justify-content-center">
	<div class="col-sm-6">
		<?php $this->load->view('flashdata'); ?>
		<div class="card boder-0 shadow p-3" style="border-radius: 16px;">
			<div class="card-body">
				<h3 class="text-center mt-4 mb-5">Update Foto Area</h3>
				<form method="POST" class="needs-validation" action="<?= base_url('area/update_photos/' . $areaCode) ?>" enctype="multipart/form-data" novalidate>
					<div class="mb-3">
						<label for="existing-photos">
							Existing Photos
						</label>
						<div class="row align-items-center">
							<?php foreach($photos as $photo): ?>
								<div class="col-sm-3 img-current" data-id="<?= $photo->id_photo_area ?>">
									<img src="<?= $photo->photo_url ?>" style="width: 100%; border-radius: 16px;" alt="...">
								</div>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="mb-3">
						<label for="area-name" class="form-label">Upload Gambar Area</label>
						<input type="file" name="images[]" class="form-control" multiple accept="image/*">
						<div class="invalid-feedback">
						<div class="invalid-feedback">
							Gambar wajib upload minimal 1.
						</div>
					</div>

					<div id="deleteImages">
					</div>

					<div id="image-preview" class="row mb-3 mt-5"></div>

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
	$('input[name="images[]"]').on('change', function() {
		const files = $(this)[0].files;
		for (var i = 0; i < files.length; i++) {
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

	$(".img-current").on("click", function() {
		const idImage = $(this).data('id');

		
		$("#deleteImages").append(
			`<input type="hidden" name="delete_images[]" value="${idImage}" class="form-control" required>`
		);

		$('.img-current[data-id="' + idImage + '"]').remove();
	});

</script>
