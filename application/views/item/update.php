<div id="urlSearch" data-area_url="<?= $findArea ?>"></div>
<div class="row justify-content-center">
	<div class="col-sm-8">
		<?php $this->load->view('flashdata'); ?>
		<div class="card boder-0 shadow p-3" style="border-radius: 16px;">
			<div class="card-body">
				<h3 class="text-center mt-4 mb-5"><?= $title ?></h3>
				<form class="needs-validation" novalidate method="POST" action="<?= base_url('item/do_update/' . $item->item_code) ?>" enctype="multipart/form-data">
					<div class="mb-1">
						<label for="find-area" class="form-label">Area</label>
						<input type="text" class="form-control" id="find-area" name="find-area" value="<?= ($item->area_code == null) ? '' : $item->area_code . ' - ' . $area_name ?>">
					</div>
					<div class="mb-3">
						<input type="text" class="form-control" id="area" value="<?= ($item->area_code == null) ? '' : $item->area_code . ' - ' . $area_name ?>" disabled>
						<small style="font-size: 9pt; color: red;"><i>Jika tidak diisi, berarti bukan item milik area.</i></small>
						<input type="hidden" class="form-control" id="send-area" value="<?= ($item->area_code == null) ? '' : $item->area_code . ' - ' . $area_name ?>" name="area">
					</div>
					<div class="mb-3">
						<label for="item_name" class="form-label">Nama Item</label>
						<input type="text" class="form-control" id="item_name" name="item_name" value="<?= $item->inventory_name ?>" required>
						<div class="invalid-feedback">
							Nama Item wajib diisi.
						</div>
					</div>
					<div class="row mb-3">
						<div class="col-sm-6">
							<label for="qty" class="form-label">Quantity Item</label>
							<input type="number" class="form-control" id="qty" name="qty" value="<?= $item->qty ?>" required>
							<div class="invalid-feedback">
								Quantity Item wajib diisi.
							</div>
						</div>
						<div class="col-sm-4">
							<label for="unit_qty" class="form-label">Satuan</label>
							<input type="text" class="form-control" id="unit_qty" name="unit_qty" value="<?= $item->unit_qty ?>" required>
							<div class="invalid-feedback">
								Satuan wajib diisi.
							</div>
						</div>
					</div>
					<div class="mb-3">
						<label for="description" class="form-label">Deskripsi Item</label>
						<textarea type="text" class="form-control" id="description" rows="5" name="description" required><?= $item->description ?></textarea>
						<div class="invalid-feedback">
							Deskripsi Item wajib diisi.
						</div>
					</div>
					<div class="mb-3">
						<label for="thumbnail" class="form-label">Thumbnail</label>
						<input type="file" name="thumbnail" class="form-control">
					</div>
					<div id="image-preview" class="row my-3">
						<div class="col-md-3">
							<img src="<?= $item->thumbnail ?>" height="120px;" alt="Preview Image">
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
	});

	$('input[name="thumbnail"]').on('change', function() {
		const files = $(this)[0].files;
		const file = files[0];
		var reader = new FileReader();
			reader.onload = function(e) {
				$('#image-preview').html(`
					<div class="col-md-3">
						<img src="${e.target.result}" height="120px;" alt="Preview Image">
					</div>
				`);
			}
		reader.readAsDataURL(file);
	});
</script>
