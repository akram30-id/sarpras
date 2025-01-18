<div id="url" data-urlPic="<?= $ajax; ?>"></div>
<div id="urlSearch" data-user_url="<?= $findUser ?>"></div>
<div class="mt-5">
	<div class="row justify-content-center">
		<div class="col-sm-8">
			<?php $this->load->view('flashdata'); ?>
			<div class="card border-0 shadow" style="border-radius: 32px;">
				<div class="card-body">
				<h3 class="text-center fw-semibold mt-5 mb-5">
					PIC INFORMATION
				</h3>
					<div class="row align-items-center px-3 pb-3">
						<div class="col-sm-4">
							<img id="avatar" height="256" class="img-fluid" src="<?= base_url('assets/img/loading.gif') ?>" alt="" srcset="">
						</div>
						<div class="col-sm-8">
							<div class="row">
								<div class="col-sm-3">
									<p>Nama</p>
								</div>
								<div class="col-sm-9">
									<p id="name">LOADING . . . </p>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<p>Tangal Lahir</p>
								</div>
								<div class="col-sm-9">
									<p id="born-date">Loading . . .</p>
								</div>
							</div>
							<div class="row align-items-center">
								<div class="col-sm-3">
									<p>Tempat Lahir</p>
								</div>
								<div class="col-sm-9">
									<p id="born-at">Loading . . .</p>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<p>Updated By</p>
								</div>
								<div class="col-sm-9">
									<p id="user-input">Loading . . .</p>
								</div>
							</div>
						</div>
					</div>
					<div class="d-flex align-items-center justify-content-end mt-3">
						<a href="<?= base_url('ekskul/schedule/' . $ekskul_code) ?>" class="btn btn-sm btn-primary rounded-pill">Show Schedule >>></a>
					</div>
				</div>
			</div>
		</div>

		<?php $this->load->view('ekskul/update_master', ['ekskul' => $ekskul]); ?>
		<?php $this->load->view('ekskul/update_schedule', ['ekskul' => $ekskul, 'schedule' => $schedule]); ?>
	</div>
</div>

<script>
	const url = $("#url").data("urlpic");
	const userURL = $("#urlSearch").data('user_url');

	$.ajax({
		url: url,
		type: 'GET',
		dataType: 'json',
		success: function (result) {
			if (result.success) {
				const response = result.data;

				$("#avatar").attr('src', response.photo);
				$("#name").text(response.name);
				$("#born-date").text(response.birth_date);
				$("#born-at").text(response.born_at);
				$("#user-input").text(response.user_input + ' (' + response.created_at + ')');
			}
		}
	});

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
