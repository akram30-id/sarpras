<div id="urlSearch" data-user_url="<?= $findUser ?>"></div>
<div class="row justify-content-center">
	<div class="col-sm-6">
		<?php $this->load->view('flashdata'); ?>
		<div class="card boder-0 shadow p-3" style="border-radius: 16px;">
			<div class="card-body">
				<h3 class="text-center mt-4 mb-5"><?= $title ?></h3>
				<form class="needs-validation" novalidate method="POST" action="<?= base_url('ekskul/do_add') ?>">
					<div class="mb-1">
						<label for="find-pic" class="form-label">PIC</label>
						<input type="text" class="form-control" id="find-pic" name="find-pic">
					</div>
					<div class="mb-3">
						<input type="text" class="form-control" id="pic" disabled>
						<input type="hidden" class="form-control" id="send-pic" name="pic">
					</div>
					<div class="mb-3">
						<label for="ekskul_name" class="form-label">Nama Ekskul</label>
						<input type="text" class="form-control" id="ekskul_name" name="ekskul_name" required>
						<div class="invalid-feedback">
							Nama Ekskul wajib diisi.
						</div>
					</div>
					<div id="image-preview" class="row my-3"></div>
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
	const userURL = $("#urlSearch").data('user_url');
	
	$("#find-pic").autocomplete({
		source: function(request, response) {
			// Replace 'your_data_url' with the actual URL of your data source
			$.ajax({
				url: userURL,
				dataType: 'json',
				type: "GET",
				data: {
					search: $("#find-pic").val()
				},
				success: function(data) {
					response(data);
				}
			});
		},
		minLength: 0, // Show all data on focus
		select: function(event, ui) {
			$("#pic").val(ui.item.value); // Append selected value
			$("#send-pic").val(ui.item.value); // Append selected value
		}
	}).focus(function () {
		$(this).autocomplete('search', '')
	});
</script>
