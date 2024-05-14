<div class="row justify-content-center">
	<div class="col-sm-5">
		<?php $this->load->view('flashdata'); ?>
		<div class="card boder-0 shadow p-3" style="border-radius: 16px;">
			<div class="card-body">
				<h3 class="text-center mt-4 mb-5">Tanda Tangan Checkout</h3>
				<form method="POST" action="<?= base_url('area/do_checkout/' . $bookingCode) ?>">
					<div class="row justify-content-center mb-1">
						<div class="col-sm-10">
							<canvas id="signatureCanvas" height="200" max-width="460" style="border: 1px solid #000; cursor: crosshair;"></canvas>
							<br>
							<a href="#" class="btn btn-sm btn-secondary" onclick="clearCanvas()">Clear</a>
							<a href="#" class="btn btn-sm btn-secondary" onclick="saveSignature()">Save</a>
						</div>
					</div>
					<div class="mb-3">
						<input type="hidden" id="signaturePhoto" name="signaturePhoto">
					</div>
					<div class="row justify-content-end mt-5" style="margin-right: 8px;">
						<div class="col-sm-2">
							<div class="d-grid">
								<button type="submit" class="btn btn-primary btn-block">Submit</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
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
		$("#signaturePhoto").val('');
	}

	function saveSignature() {
		var dataURL = canvas.toDataURL(); // Convert canvas to base64 data URL
		// console.log(dataURL); // You can send this dataURL to your server to save the signature as an image
		// Alternatively, you can use this dataURL to display the signature elsewhere in your application
		$("#signaturePhoto").val(dataURL);
	}
</script>
