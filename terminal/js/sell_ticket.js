$(document).ready(function() {
	
	/*** Submit travel details and load seating arrangement ***/
	$("#book").submit(function(e) {
		e.preventDefault();
		if ($('#destination').val().length == '' || $('select[name=num_of_seats] option:selected').val().length == '') {
			$('#main_bus_search .alert-error').html("Fill out all the form fields to continue.").show().fadeOut(6000);
			return false;
		}
		var data = $(this).serialize();
		//var bus_id = $('#bus_details').data('bus_id');
		//var num_of_seats = $('#bus_details').data('num_of_seats');
		
		// Load seating arrangement
		$.post('../ajax.php', data + '&op=get_seating', function(d) {
			if (d.trim() == "04") {
				$('#main_bus_search .alert-error').html("The selected route is invalid").show().fadeOut(6000);
			} else {
				$('#pick_seat').html(d);
			}
		});
	});
	
	
	/*** Select/book and unselect seat [ Toggle ] ***/
	$('#pick_seat').on('click', '.seat', function() {
		var seat_no = $(this).attr('id');
		//var bus_id  = $(this).data('bus_id');
		//var fare    = Number($('#seat_arrangement').attr('class'));
		
		if ($(this).data('hidden') == 'no') {
			$(this).css('background-image', 'url("../images/selected_seat.gif")');
			
			if ($('#picked_seat').text().length == 0) {
				$('#picked_seat').text(seat_no);
			} else
			// Check if there's an already selected seat
			if ($('#picked_seat').text() != seat_no) {
				$('div#' + $('#picked_seat').text()).css('background-image', 'url("../images/seat.gif")').data('hidden', 'no');
				$('#picked_seat').text(seat_no);
			}
			$(this).data('hidden', 'yes');
		} else {
			$(this).css('background-image', 'url("../images/seat.gif")');
			$('#picked_seat').text('');
			$(this).data('hidden', 'no');
		}
		//$('#picked_seat').data('bus_id', bus_id);
	});
	
	
	/*** Put the selected seat in a placeholder, and display inputs for customer's details ***/
	$('#pick_seat').on('click', '#continue', function(e) {
		e.preventDefault();
		var picked_seats = $('#picked_seat').text();
		if (picked_seats.length < 1) {
			alert("Pick a seat before you continue");
			return false;
		}
		$('#details').fadeIn();
		$("input[name='customer_name']").focus();
	});
	
	
	/*** Store user info and booking details, then print ticket ***/
	$('#details').on('submit', '#customer_info', function(e) {
		e.preventDefault();
		var bln_validate = true;
		var to = $("#destination").val();
		var travel_date = $('#t_date').val();
		//var bus_id = $('#picked_seat').data('bus_id');
		var seat_no   = $('#picked_seat').text();
		var bus_type  = $('#seat_arrangement').data('seating_arrangement');
		var booked_id = $('#seat_arrangement').data('booked_id');
		var merged    = $('#seat_arrangement').data('merged');
		
		// Validate form inputs
		$.each($(this).serializeArray(), function(i, val) {
			if (val.value.length == 0 && val.name != 'address') {
				bln_validate = false;
				$('#details .alert').html("Form not completely filled out").fadeIn();
				$("[name='" + val.name + "']").focus();
				return false;
			}
		});
		
		if (bln_validate === false) return false;
				
		$.post('../ajax.php',
			$(this).serialize()
			+ '&to=' + to
			+ '&op=handle_customer_info&booked_id=' + booked_id
			+ '&seat_no=' + seat_no
			+ '&bus_type=' + bus_type
			+ '&travel_date=' + travel_date
			+ '&merged=' + merged,
			function(d) {
				if (d.trim() == "01") {
					alert("Please select a seat to continue");
				} else if (d.trim() == "02") {
					alert("Seat " + seat_no + " is no longer available, pick a different seat");
				} else if (d.trim() == "03") {
					alert("This operation failed, please refresh the browser and start again");
				} else {
					$('input[type=reset]').click();
				
					/*** Print ticket ***/
					var iframe_body = $("#receipt").contents().find("#details");
					iframe_body.html(d);
					window.frames['receipt'].print();
					iframe_body.html("");
					
					/*** Reset for the next customer ***/
					$('#pick_seat').html('');
					$('#details').fadeOut();
				}
			}
		);
	});
	
	
	/*** Autocomplete for selecting destination ***/
	$("#destination").focus(function() {
		$(this).autocomplete('../includes/autocomplete.php?op=destination&opt=1').result(function(event, data, formatted) {
			data = data.toString();
		});
		return false;
	});
	
	
	///*** Search for ticket details usign the ticket ref number, for online customers ***/
	//$("#ticket-search").click(function() {
	//	if ($("#ticket_no").val().length == 8) {
	//		var ticket_no = $("#ticket_no").val();
	//	} else {
	//		alert("Ticket ref number must be eight (8) digits");
	//		return false;
	//	}
	//	
	//	$.get('ajax.php', {"op":"get-ticket-details", "ticket_no":ticket_no}, function(d) {
	//		$("#ticket-details").html(d);
	//	});
	//});
	
	/*** Cancel/Reset for a different route ***/
	$('#reset-form').click(function() {
		location.href = 'sell_ticket.php';
	});
});