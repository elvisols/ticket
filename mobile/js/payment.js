$(document).on('pageinit', '#payment', function() {
	$("#ticket_details").click(function() {
		if ($("#ticket-details").not(":visible")) {
			$("#payment-opt").fadeOut(function() {
				$("#ticket-details").fadeIn();
			});
		} 
	});
	
	$("#payment_opt").click(function() {
		if ($("#payment-opt").not(":visible")) {
			$("#ticket-details").fadeOut(function() {
				$("#payment-opt").fadeIn();
			});
		} 
	});
	
	$("#personal_details").click(function() {
		$("#customer-details").slideToggle();
	});
	
	/*** Select payment option ***/
	$("#pay-on-delivery").css("display", "none");
	$("input[name='payment']:radio").click(function() {
		$('#bank-payment, #pay-on-delivery').fadeOut('fast');
		
		if ($(this).attr('id') == "Home delivery") {
			$('#pay-on-delivery').fadeIn('slow');
		} else if ($(this).attr('id') == "Bank payment") {
			$('#bank-payment').fadeIn('slow');
		}
	});
	
	/*** Submit customers form and sent other data to the server ***/
	$("#customer_info").submit(function(e) {
		e.preventDefault();
		/*** validate customer's form ***/
		var bln_validate = true;
		$.each($('#customer_info').serializeArray(), function(i, val) { 
			if (val.value.length == 0) {
				bln_validate = false;
				$("input[name='" + val.name + "']").focus();
				$('#c_info').html("Form not completely filled out").fadeIn().fadeOut(11000);
				return false;
			}
		});
		
		if (bln_validate == false) return false;
		
		var payment_opt = $('input[name=payment]:checked').attr('id');
		var address = '';
		if (payment_opt == "Home delivery") {
			address = $('#delivery_addr').val();
		}
		
		var seat_no          = $("#data").data("seat_no");
		var route            = $("#data").data("route");
		var seat_arrangement = $("#data").data("seat_arrangement");
		var booked_id        = $("#data").data("booked_id");
		var bus_type_id      = $("#data").data("bus_type_id");
		var travel_id        = $("#data").data("travel_id");
		var travel_date      = $("#data").data("travel_date");
		//alert(travel_date);
		
		$.post('../ajax.php', {
			'op'             : 'handle_customer_info',
			'seat_no'        : seat_no,
			'route'          : route,
			'bus_type'       : seat_arrangement,
			'booked_id'      : booked_id,
			'bus_type_id'    : bus_type_id,
			'travel_id'      : travel_id,
			'travel_date'    : travel_date,
			'payment_opt'    : payment_opt,
			'address'        : address,
			'customer_name'  : $('#customer_name').val(),
			'customer_phone' : $('#customer_phone').val(),
			'email'          : $('#email').val(),
			'next_of_kin_phone': $('#next_of_kin_num').val() },
			function(d) {
				if ($.trim(d) == "02") {
					$("#seat-error").prepend("Sorry, <b>seat " + seat_no + "</b> is no longer available, please go back and select a different seat.<br />Thank you");
					$("#seat-error").popup("open");
				} else if ($.trim(d) == "03") {
					$("#seat-error").prepend("We are sorry, your seat booking wasn't successful, please try again later");
					$("#seat-error").popup("open");
				} else {
					location.href = "checkout.php?payment_opt=" + payment_opt + "&c_name=" + $('#customer_name').val() + "&ticket_no=" + d;
				}
			}
		);
	});
});