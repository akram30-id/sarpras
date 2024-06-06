<?php 

$days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];

// echo '<pre>';
// print_r($schedule);
// return;

?>

<div class="container mt-5">
	<div class="row justify-content-center">
		<?php foreach ($days as $index => $day) { ?>
			<div class="col-sm-4">
				<div class="card border-0 shadow" style="border-radius: 16px;">
					<div class="card-body">
						<h5 class="text-center mt-3"><?= $day ?></h5>
						<hr>
						<ul>
							<?php foreach ($schedule as $value) {
								if($value->day == $index){ ?>
									<li>
										<a href="<?= base_url('ekskul/detail/' . $value->ekskul_code) ?>">
											<?= $value->ekskul_name . ' #' . $value->ekskul_code ?>
										</a>
										<br>
										<small>
											<?= $value->start_clock . ' - ' . $value->end_clock; ?>
										</small>
									</li>
								<?php }
							} ?>
						</ul>
					</div>
				</div>
			</div>
			<?php } ?>
	</div>
</div>
