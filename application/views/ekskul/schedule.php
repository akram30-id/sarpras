<?php 

$days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];

?>

<div class="container mt-5">
	<div class="row justify-content-center">
		<?php foreach ($days as $index => $day) { ?>
			<div class="col-sm-4">
				<div class="card border-0 shadow" style="border-radius: 16px;">
					<div class="card-body">
						<h5 class="text-center mt-3"><?= $day ?></h5>
						<hr>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
