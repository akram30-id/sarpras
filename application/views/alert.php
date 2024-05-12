<!-- Modal -->
<div class="modal fade" id="alert<?= $idUpdate ?>" tabindex="-1" aria-labelledby="alertLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <h3>Apakah Anda yakin ingin <?= $action ?> data <?= $dataAlert ?>?</h3>
      </div>
      <div class="d-flex justify-content-end align-items-center" style="margin-right: 10px; margin-bottom: 10px;">
		  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="margin-right: 8px;">Close</button>
			<?php if($type == 'delete'){ ?>
				<a href="<?= $url ?>" class="btn btn-danger">Hapus</a>
			<?php } else { ?>
				<button type="button" class="btn btn-primary"><?= $action ?></button>
			<?php } ?>
      </div>
    </div>
  </div>
</div>
