$(document).on('pageinit', '#seating_arrangement', function(evt) {
	// Hide unecessary details on mobile
	$('#seat_tips, #seat_arrangement p, #seat_details').hide();
	$(".close-image").css("display", "none");
	
	// resize seat arrangement
	$('#seat_arrangement').css('width', '80%').prepend("&nbsp;Seat no: <span id='picked_seat'></span><br />");
	$('#seat_wrap').css('margin', '2px').after("<div style='clear:left'>&nbsp;Fare: <span id='show_fare'></span></div>");
	
	// Change "continue" link to a moblie button
	$('#continue').data("data-role", "button").button();
	$('#continue').css("margin-top", "10px");
	
	$(this).trigger('create');
	
	/*** Select/book and unselect seat [ Toggle ] ***/
	$('.seat').click(function() {
		var seat_no = $(this).attr('id');
		//var bus_id  = $(this).data('bus_id');
		var fare = Number($('#seat_arrangement').attr('class'));
		
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
			$('#show_fare').text("₦" + fare);
		} else {
			$(this).css('background-image', 'url("../images/seat.gif")');
			$('#picked_seat').text('');
			$(this).data('hidden', 'no');
		}
		//$('#picked_seat').data('bus_id', bus_id);
	});
	
	
	$("#continue").click(function() {		
		/*** Collect some sweet data ***/
		var seat_no   = $('#picked_seat').text();
		if (seat_no == '') {
			//$(this).data("rel", "popup");
			//$(this).attr("href", "#seat-popup");
			$("#seat-popup").popup("open");
			return false;
		}
		var booked_id    = $('#seat_arrangement').data("booked_id");
		var bus_type_id  = $("#data").data("bus_type_id");
		var departure_time = $("#data").data("departure_time");
		var query_string = "booked_id=" + booked_id + "&seat_no=" + seat_no + "&bus_type_id=" + bus_type_id + "&departure_time=" + departure_time;
		$.mobile.changePage("payment.php", {
				type: "get",
				data: query_string
		});
	});
	
});

$(function() {
	//$('.ui-page-active :jqmData(role=content)').trigger('create');
	//$.mobile.initializePage();
});