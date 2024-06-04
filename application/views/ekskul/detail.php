<div id="url" data-urlPic="<?= $ajax; ?>"></div>
<div class="mt-5">
	<div class="row justify-content-center">
		<div class="col-sm-8">
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
	</div>
</div>

<script>
	const url = $("#url").data("urlpic");

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
	})
</script>
