<?php if ($this->session->flashdata('failed')) { ?>
					<div class="pt-2 pb-2 text-center">
						<div class="alert alert-danger" role="alert">
							<?= $this->session->flashdata('failed'); ?>
						</div>
					</div>
				<?php 
		} ?>

				<?php if ($this->session->flashdata('success')) { ?>
					<div class="pt-2 pb-2 text-center">
						<div class="alert alert-success" role="success">
							<?= $this->session->flashdata('success'); ?>
						</div>
					</div>
				<?php 
		} ?>
