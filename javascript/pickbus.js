$(document).ready(function() {
	/*** Show seating arrangement ***/
	$('.sortable').on('click', '.seats', function(e) {
		e.preventDefault();
		var bus_id       = $(this).data('bus_id');
		var num_of_seats = $(this).data('num_of_seats');
		var origin       = $(this).data('origin');
		var destination  = $(this).data('destination');
		var travel_date  = $(this).data('travel_date');
		var fare         = $(this).data('fare');
		var travel_id    = $(this).data('travel_id');
		var departure_time = $(this).data('departure_time');
		
		$.post('ajax.php', {
			'bus_id'      :bus_id,
			'num_of_seats':num_of_seats,
			'origin'      :origin,
			'destination' :destination,
			'travel_date' :travel_date,
			'fare'        :fare,
			'travel_id'   :travel_id,
			'departure_time':departure_time,
			'op'          :'get_seating'
		},
			function(d) {
				$('tr#' + bus_id).css('border-bottom', 'none');
				var height = '';
				if (num_of_seats > 15) height = "280px";
				else height = "220px";
				$('td#show-seat_' + bus_id).css('display', 'block').animate({height: height}, function() {
					$(this).html(d);
					$(this).parent('tr').css('border-bottom', '#ccc solid thin');
				});
			}
		);
	});
	
	
	/*** Select/book and unselect seat [ Toggle ] ***/
	$('td.show-seat').on('click', '.seat', function() {
		var seat_no = $(this).attr('id');
		//var bus_id  = $(this).data('bus_id');
		var bus_id  = '1'; 	// Just keep this alive in case...
		var fare    = Number($('#seat_arrangement').attr('class'));
		
		if ($(this).data('hidden') == 'no') {
			
			if ($('#picked_seat').text().length == 0) {
				$('#picked_seat').text(seat_no);
			} else if ($('#picked_seat').text() != seat_no) { // Check if there's an already selected seat
				$('div.seat').css('background-image', 'url("images/seat.gif")').data('hidden', 'no');
				$('#picked_seat').text(seat_no);
			}
			$(this).css('background-image', 'url("images/selected_seat.gif")');
			$('#show_fare').text(fare);
			$(this).data('hidden', 'Yes');
			
			//if ($('#picked_seat').text().length == 0) {
			//	$('#picked_seat').text(seat_no);
			//	
			//} else {
			//	var picked_seat = $('#picked_seat').text();
			//	var old_cost  = Number($('#show_fare').text());
			//	$('#picked_seat').text(picked_seat + ', ' + seat_no);
			//	$('#show_fare').text(fare + old_cost);
			//}
		} else {
			$(this).css('background-image', 'url("images/seat.gif")');
			$('#picked_seat').text('');
			$(this).removeData('hidden').data('hidden', 'no');
		}
	});
	
 /*** Proceed to payment page ***/
	$('.sortable').on('click', '#continue', function(e) {
		e.preventDefault();
		var seat_no             = $('#picked_seat').text();
		var booked_id           = $('#seat_arrangement').data('booked_id');
		var seating_arrangement = $('#seat_arrangement').data('seating_arrangement');
		var departure_time      = $("#seat_arrangement").data("departure_time");
		var bus_type_id         = $(this).parents('tr').data('bus_id');
	
		if (seat_no.length < 1) {
			alert("Pick a seat before you continue");
			return false;
		}
		
		location.href = "payment.php?booked_id=" + booked_id
					  + "&bus_id=" + bus_type_id
					  + "&seat_no=" + seat_no
					  + "&seating_arrangement=" + seating_arrangement
					  + "&departure_time=" + departure_time;
		
		//$.post('ajax.php', {
		//	'seat_nos'        : seat_no,
		//	'bus_type_id'	  : bus_type_id,
		//	'booked_id'       : booked_id,
		//	'seat_arrangement': seating_arrangement,
		//	'op':'reserve_seat'},
		//	function(d) {
		//		if (d.trim() == 'done') {
		//			location.href = "payment.php?booked_id=" + booked_id + "&bus_id=" + bus_type_id + "&seat_no=" + seat_no;
		//		} else {
		//			alert(d);
		//		}
		//	}
		//);
	});
	
	
	$('td.show-seat').on('click', '.close-image', function() {
		$(this).closest('td', '.sortable').fadeOut();
	});
});