<div id="uri" data-eventUri="<?= $event ?>"></div>
<div class="row justify-content-center">
	<div class="col-sm-11">
		<div class="card border-0 shadow p-5" style="border-radius: 20px;">
			<div class="card-body">
				<div id='calendar'></div>
			</div>
		</div>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const eventUri = $("#uri").data('eventuri');

		var calendarEl = document.getElementById('calendar');
		var calendar = new FullCalendar.Calendar(calendarEl, {
			initialView: 'listWeek',
			events: eventUri,
			displayEventTime: true,
			headerToolbar: {
				left: 'prev,next today',
				center: 'title',
				right: 'dayGridMonth,timeGridWeek,listWeek'
			}
		});
		calendar.render();
	});

	// $(document).ready(function() {
	// 	var calendar = $('#calendar').fullCalendar({
	// 		editable:true,
	// 		header:{
	// 			left:'prev,next today',
	// 			center:'title',
	// 			right:'month,agendaWeek,agendaDay'
	// 		},
	// 		// events: 'load.php',
	// 		// selectable:true,
	// 		// selectHelper:true
	// 	});

	// 	// calendar.render()
	// });

</script>
