<div id="carouselExample<?= $area_code ?>" class="carousel carousel-dark slide" data-bs-ride="carousel">
	<div class="carousel-inner">
		<?php 
			if ($photos) {
				foreach ($photos as $src) : ?>
				<div class="carousel-item active">
					<img src="<?= $src ?>" class="d-block img-fluid" style="width: 720px; border-radius: 20px;" alt="...">
				</div>
				<?php endforeach;
			} else { ?>
				<div class="carousel-item active">
					<img src="<?= base_url('assets/img/apple-touch-icon.png') ?>" style="width: 720px; border-radius: 20px;" class="d-block img-fluid" alt="...">
				</div>
		<?php } ?>
	</div>
</div>
