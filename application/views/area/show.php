<div class="row justify-content-center">

	<?php $this->load->view('flashdata'); ?>

	<?php foreach ($areas as $area) :
	?>
	<div class="col-sm-10">

		<button class="btn btn-primary mb-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
			Cetak Laporan
		</button>
		<div class="collapse mb-3" id="collapseExample">
			<form method="post" action="<?= base_url('area/print_report') ?>">
				<div class="row align-items-end">
					<div class="col-sm-3">
						<div class="mb-3">
							<label for="start">Dari Tanggal</label>
							<input type="date" name="start" id="start" class="form-control">
						</div>
					</div>
					<div class="col-sm-3">
						<div class="mb-3">
							<label for="end">Sampai Tanggal</label>
							<input type="date" name="end" id="end" class="form-control">
						</div>
					</div>
					<div class="col-sm-2">
						<div class="mb-3">
							<button type="submit" class="btn btn-primary">Cetak	</button>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="card border-0 shadow" style="border-radius: 16px;">
			<div class="card-body">
				<div class="row justify-content-center align-items-center mt-3">
					<div class="col-sm-8">
						<?php $this->load->view('area/carousel', $area); ?>
					</div>
					<div class="col-sm-4 mt-3">
						<h2><?= $area->area_name ?></h2>
						<p>#<?= strtoupper($area->area_code) ?></p>
						<h4>Open: <?= $area->open_hours . ' - ' . $area->close_hours ?></h4>
						<h5>PIC Area: <?= $area->pic_area ?? 'Unknown' ?></h5>
						<small style="font-size: 8pt;"><i><b>Created By: <?= $area->user_input ?></b></i></small>
						<br>
						<small style="font-size: 8pt;"><i><b>Created At: <?= date('d F Y', strtotime($area->created_at)) ?></b></i></small>

						<?php if($this->session->user->role == 1){ ?>
						<div class="row mt-3">
							<div class="col-sm-2 d-grid mt-2">
								<a href="<?= base_url('area/edit/' . $area->area_code) ?>" class="btn btn-primary rounded-pill btn-sm d-block">Edit</a>
							</div>
							<div class="col-sm-2 d-grid mt-2">
								<?php $area->idUpdate = $area->area_code ?>
								<button data-bs-toggle="modal" data-bs-target="#alert<?= $area->idUpdate ?>" class="btn btn-danger rounded-pill btn-sm d-block">Delete</button>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
				<?php
					$area->action = 'Hapus';
					$area->dataAlert = $area->area_code;
					$area->type = 'delete';
					$area->url = base_url('area/delete/' . $area->area_code);
				?>
				<?php $this->load->view('alert', $area); ?>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>


<script>
	$("#start").on("change", function () {
		const startDate = $(this).val();

		$("#end").attr("min", startDate);
	});

	$("#end").on("change", function () {
		const endDate = $(this).val();

		$("#start").attr("max", endDate);
	})
</script>
