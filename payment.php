<?php
session_start();
require_once("includes/general_functions.php");

docType();
printBanner();
?>
<style>
#details, #payment_opt {float:left; width:30%}
#paymentopt{margin-left:60px; width:50%; display:none}
#payment_opt {width:290px; font: 12px Arial; line-height:17px; padding:10px; margin-left:50px; margin-top:-9px}
#ticket_details {float:right; width:230px; border:#ccc solid thin; padding:9px; font:11px Verdana; line-height:19px; margin-top:20px; background-color:#f8f8f8}
#pay > div {float:left}
#pay > a {display:inline-block; padding:10px;}
#pay > div > a:hover {display:inline-block; padding:1px; border:#ccc solid thin}
i {margin-left:5px; position:relative; top:-1px}
</style>
<script type="text/javascript" src="bootstrap-tab.js"></script>
<div id='content'>
	<div id="details">
		<div class="head">Personal Details</div><hr style='margin-top:2px' />
		<form method="post" id="customer_info" class="form-horizontal">
			<p><input type="text" name="customer_name" id="customer_name" placeholder="Customer's name" /></p>
			<p><input type="text" name="customer_phone" id="customer_phone" placeholder="Customer's phone number" /></p>
			<p><input type="text" name="next_of_kin_num" id="next_of_kin_num" placeholder="Next of kin phone number" /></p>
			<p><input type="text" name="email" id="email" placeholder="Email address" /></p>
			<!--<p><button type='submit' class='btn btn-primary'><i class='icon-user icon-white'></i> Add Details</button></p>-->
		</form>
		<div id="c_info" class="alert alert-error hide"></div>
	</div>
	
	<div id="payment_opt">
		<div class='head'>Payment Options</div><hr style='margin:2px' />
		<p><input type="radio" name="payment" id="Bank payment" value="Bank payment" checked="checked" /> <span style="margin-left: 8px; position:relative; top:4px">Bank deposit</span></p>
		<p><input type="radio" name="payment" id="Home delivery" value="Home delivery" /> <span style="margin-left: 8px; position:relative; top:4px">Pay cash on delivery</span></p>
		<p><input type="radio" name="payment" id="Home delivery" value="Home delivery" /> <span style="margin-left: 8px; position:relative; top:4px">Pay with Stanbic Mobile money</span></p><br />
		
		<div id="bank-payment-opt" class="payment-opt"><br />
			After checkout, please deposit the total amount into the following account with your ticket number as a reference.
			Your mobile ticket will be sent through SMS, as soon as your transaction reflects in our account. 
			<br /><br />
			<p>
			<b>Bank:</b> Access Bank<br />
			<b>Account Name:</b> 4Forty Bus Limited<br />
			<b>Account Number:</b> 0059990833<br />
			<b>Type of Account:</b> Current Account
			</p>
		</div>
		
	
		<div id="pay-on-delivery" class='hide payment-opt'>
			<form method="post">
				<label>Enter delivery address</label>
				<textarea id="delivery_addr" row="4" style="width:95%"></textarea>
				
				<div class="controls">
					<input type="submit" class="btn btn-primary" name="submit" value="  Send  " style="visibility:hidden" />
				</div>
			</form>
		</div>
		
	</div>
	
	<?php
		/*** Get ticket booking details ***/
		$booked_id      = filter($_GET['booked_id']);
		$bus_type_id    = filter($_GET['bus_id']);
		$seat_no        = filter($_GET['seat_no']);
		$departure_time = filter($_GET['departure_time']);
		
		$result = $DB_CONNECTION->query("SELECT route_code, fare, travel_date FROM seat_booking WHERE booked_id = '$booked_id'");
		$details = $result->fetch_object();
		
		$route = splitRouteMap($details->route_code);
		$travel_date = date('D d M Y', strtotime($details->travel_date));
		
		$result = $DB_CONNECTION->query("SELECT bus_type, fare, company_name, travel_id, departure_time FROM buses AS b JOIN travels AS t WHERE travel_id = t.id AND b.id = '$bus_type_id'");
		$bus = $result->fetch_object();
	?>
	
	<div id="ticket_details">
		<div><b>Ticket Booking Details</b></div><hr style='margin:5px 0px' />
		<i class="icon-hand-right"></i> <?php echo "{$route['origin']} to {$route['destination']}<br />
		<i class='icon-hand-right'></i> {$travel_date}<br />
		<i class='icon-hand-right'></i> $bus->company_name<br />
		<i class='icon-hand-right'></i> $bus->bus_type<br />
		<i class='icon-hand-right'></i> Seat no: {$seat_no}<br />
		<i class='icon-hand-right'></i> Fare: {$details->fare} NGN<br />
		<i class='icon-hand-right'></i> Departure: $departure_time	
	</div>"
	?>
	<div style='float:right; width:250px; margin-top:15px'>
		<a href='#' class='btn btn-large btn-block btn-warning' id="checkout" stle='margin-top:15px'>Check out</a>
	</div>
	<div style='float:right; width:200px; margin-top:15px' id="error" class="alert alert-error hide"></div>
</div>

</div>
<script type="text/javascript">
$(document).ready(function() {
	$("input[name='payment']:radio").click(function() {
		$('.payment-opt').fadeOut('fast');
		
		if ($(this).attr('id') == "Home delivery") {
			$('#pay-on-delivery').fadeIn('slow');
		} else if ($(this).attr('id') == "Bank payment") {
			$('#bank-payment-opt').fadeIn('slow');
		}
	});
	
/*** Check out, get traveller's personal details and payment option ***/	
	$('a#checkout').click(function(e) {
		e.preventDefault();
		$('#customer_info').submit(e);
	
	
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
		
		var seat_no 	= <?php echo $seat_no; ?>;
		var route   	= "<?php echo "{$route['origin']} to {$route['destination']}"; ?>";
		var travel_id   = <?php echo $bus->travel_id; ?>;
		var seat_arrangement = <?php echo $_GET['seating_arrangement']; ?>;
		var booked_id   = <?php echo $booked_id; ?>;
		var bus_type_id = <?php echo $bus_type_id; ?>;
		var travel_date = "<?php echo $details->travel_date; ?>";
		
		$.post('ajax.php', {
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
					$("#error").html("Sorry, <b>seat " + seat_no + "</b> is no longer available, please go back and select a different seat.<br />Thank you").fadeIn();
				} else if ($.trim(d) == "03") {
					$("#error").html("We are sorry, your seat booking wasn't successful, please try again later").fadeIn();
				} else {
					location.href = "checkout.php?payment_opt=" + payment_opt + "&c_name=" + $('#customer_name').val() + "&ticket_no=" + d;
				}
			}
		);
	});
});
</script>
<?php printFooter(); ?>