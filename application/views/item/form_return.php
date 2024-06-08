<div id="urlSearch" data-item_url="<?= $urlRequest ?>" data-item_qty="<?= $urlQTY ?>"></div>
<div class="row justify-content-center">
	<div class="col-sm-6">
		<?php $this->load->view('flashdata'); ?>
		<div class="card boder-0 shadow p-3" style="border-radius: 16px;">
			<div class="card-body">
				<h3 class="text-center mt-4 mb-5">Kembalikan Barang</h3>
				<form class="needs-validation" novalidate method="POST" action="<?= base_url('item/do_return') ?>" enctype="multipart/form-data">
					<div class="mb-1">
						<label for="find-req" class="form-label">Request Item</label>
						<input type="text" class="form-control" id="find-req" name="find-req" required>
						<div class="invalid-feedback">
							Wajib pilih request item.
						</div>
					</div>
					<div class="mb-3">
						<input type="text" class="form-control" id="request" disabled>
						<input type="hidden" class="form-control" id="send-request" name="request">
					</div>
					<div class="mb-3" id="qty-section">
						<label for="qty" class="form-label">Quantity</label>
						<input type="text" class="form-control" id="qty" name="qty" disabled>
						<input type="hidden" class="form-control" id="qtyActual" name="qtyActual">
					</div>
					<div class="mb-3">
						<label for="signature" class="form-label">Tanda Tangan</label>
						<div class="row justify-content-center mb-1">
							<div class="col-sm-10">
								<canvas id="signatureCanvas" height="200" max-width="460" style="border: 1px solid #000; cursor: crosshair;"></canvas>
								<br>
								<a href="#" class="btn btn-sm btn-secondary" onclick="clearCanvas()">Clear</a>
								<a href="#" class="btn btn-sm btn-secondary" onclick="saveSignature()">Save</a>
							</div>
						</div>
						<div class="mb-3">
							<input type="hidden" id="signature" name="signature">
						</div>
					</div>
					<div class="mb-3">
						<label for="user_notes" class="form-label">Catatan</label>
						<textarea type="text" class="form-control" id="user_notes" rows="5" name="user_notes"></textarea>
					</div>
					<div class="row justify-content-end mt-5" style="margin-right: 8px;">
						<div class="col-sm-2">
							<div class="d-grid">
								<button type="submit" id="submit" class="btn btn-primary btn-block">Submit</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	const itemURL = $("#urlSearch").data('item_url');
	const itemQTY = $("#urlSearch").data('item_qty');
	
	$("#find-req").autocomplete({
		source: function(request, response) {
			// Replace 'your_data_url' with the actual URL of your data source
			$.ajax({
				url: itemURL,
				dataType: 'json',
				type: "GET",
				data: {
					search: $("#find-req").val()
				},
				success: function(data) {
					response(data);
				}
			});
		},
		minLength: 0, // Show all data on focus
		select: function(event, ui) {
			$("#request").val(ui.item.value); // Append selected value
			$("#send-request").val(ui.item.value); // Append selected value

			const selected = ui.item.value;
			$.ajax({
				type: "POST",
				url: itemQTY,
				data: {
					search: selected
				},
				dataType: "json",
				success: function (response) {
					const qty = response.qty;
					const unit = response.unit_qty;

					$("#qty").val(`${qty} ${unit}`);
					$("#qtyActual").val(qty);
				}
			});
		}
	}).focus(function () {
		$(this).autocomplete('search', '')
	});


	var canvas = document.getElementById('signatureCanvas');
	var ctx = canvas.getContext('2d');

	ctx.lineWidth = 2;
	ctx.strokeStyle = 'black';

	var isDrawing = false;

	function getTouchPos(canvasDom, touchEvent) {
		var rect = canvasDom.getBoundingClientRect();
		return {
			x: touchEvent.touches[0].clientX - rect.left,
			y: touchEvent.touches[0].clientY - rect.top
		};
	}

	function drawLine(x1, y1, x2, y2) {
		ctx.beginPath();
		ctx.moveTo(x1, y1);
		ctx.lineTo(x2, y2);
		ctx.stroke();
		ctx.closePath();
	}

	canvas.addEventListener('touchstart', function(e) {
		e.preventDefault();
		var touch = getTouchPos(canvas, e);
		isDrawing = true;
		ctx.beginPath();
		ctx.moveTo(touch.x, touch.y);
	}, false);

	canvas.addEventListener('touchmove', function(e) {
		e.preventDefault();
		if (isDrawing) {
			var touch = getTouchPos(canvas, e);
			drawLine(lastX, lastY, touch.x, touch.y);
			lastX = touch.x;
			lastY = touch.y;
		}
	}, false);

	canvas.addEventListener('touchend', function(e) {
		e.preventDefault();
		if (isDrawing) {
			isDrawing = false;
		}
	}, false);

	// Inisialisasi lastX dan lastY
	var lastX, lastY;

	// Event listeners untuk mouse
	canvas.addEventListener('mousedown', function(e) {
		isDrawing = true;
		lastX = e.offsetX;
		lastY = e.offsetY;
	});

	canvas.addEventListener('mousemove', function(e) {
		if (isDrawing) {
			drawLine(lastX, lastY, e.offsetX, e.offsetY);
			lastX = e.offsetX;
			lastY = e.offsetY;
		}
	});

	canvas.addEventListener('mouseup', function() {
		isDrawing = false;
	});

	canvas.addEventListener('mouseout', function() {
		isDrawing = false;
	});

	function clearCanvas() {
		ctx.clearRect(0, 0, canvas.width, canvas.height);
		$("#signature").val('');
	}

	function saveSignature() {
		var dataURL = canvas.toDataURL(); // Convert canvas to base64 data URL
		// console.log(dataURL); // You can send this dataURL to your server to save the signature as an image
		// Alternatively, you can use this dataURL to display the signature elsewhere in your application
		$("#signature").val(dataURL);
	}
</script>
