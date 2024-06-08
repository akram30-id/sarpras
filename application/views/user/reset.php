<div id="urlSearch" data-user_url="<?= $findUser ?>"></div>
<div class="row justify-content-center">
	<div class="col-sm-6">
		<?php $this->load->view('flashdata'); ?>
		<div class="card boder-0 shadow p-3" style="border-radius: 16px;">
			<div class="card-body">
				<h3 class="text-center mt-4 mb-5"><?= $title ?></h3>
				<form class="needs-validation" novalidate method="POST" action="<?= base_url('user/do_reset') ?>" enctype="multipart/form-data">
					<div class="mb-1">
						<label for="find-user" class="form-label">User</label>
						<input type="text" class="form-control" id="find-user" name="find-user" required>
						<div class="invalid-feedback">
							Wajib pilih user.
						</div>
					</div>
					<div class="mb-3">
						<input type="text" class="form-control" id="user" disabled>
						<input type="hidden" class="form-control" id="send-user" name="user">
					</div>
					<small><i>Password yang telah direset akan berubah menjadi "default"</i></small>
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

<script>
	const userURL = $("#urlSearch").data('user_url');

	$("#find-user").autocomplete({
		source: function(request, response) {
			// Replace 'your_data_url' with the actual URL of your data source
			$.ajax({
				url: userURL,
				dataType: 'json',
				type: "GET",
				data: {
					search: $("#find-user").val()
				},
				success: function(data) {
					response(data);
				}
			});
		},
		minLength: 0, // Show all data on focus
		select: function(event, ui) {
			$("#user").val(ui.item.value); // Append selected value
			$("#send-user").val(ui.item.value); // Append selected value
		}
	}).focus(function () {
		$(this).autocomplete('search', '')
	});

</script>
