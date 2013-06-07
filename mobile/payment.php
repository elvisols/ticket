<?php
require_once("includes/header.html");
?>

<script type="text/javascript" src="js/payment.js"></script>
<div data-role="page" id="payment" style="font:11px Verdana">
	<div data-role="header">
		<h1>Payment</h1>
		<a href="index.php" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
	</div>
	
	<div data-role="content">
		<div data-role="navbar">
			<ul>
				<li><a href="#" class="ui-btn-active" id="payment_opt">Payment options</a></li>
				<li><a href="#" id="ticket_details">Ticket details</a></li>
			</ul>
		</div>

	<?php
		/*** Get ticket booking details ***/
		$booked_id   = $_REQUEST['booked_id'];
		$seat_no     = $_REQUEST['seat_no'];
		$bus_type_id = $_REQUEST['bus_type_id'];
		$departure_time = $_REQUEST['departure_time'];
		
		require_once("../includes/DB_CONNECT.php");
		if (!isset($DB_CONNECTION)) $DB_CONNECTION = db_connect();
		
		$result = $DB_CONNECTION->query("SELECT route_code, bus_id, fare, travel_date, travel_id, seating_arrangement FROM seat_booking WHERE booked_id = '$booked_id'");
		extract($result->fetch_assoc());
		//die ($travel_date);
		include_once('includes/fns.php');
		$route = splitRouteMap($route_code);
		$t_date = date('D d M Y', strtotime($travel_date));
		
		$result = $DB_CONNECTION->query("SELECT company_name FROM travels WHERE id = '$travel_id'");
		$bus = $result->fetch_object();
	
		echo "<span id='data' data-seat_no='$seat_no' data-route='{$route['origin']} to {$route['destination']}' data-seat_arrangement='$seating_arrangement' data-bus_type_id='$bus_type_id' data-booked_id='$booked_id' data-travel_id='$travel_id' data-travel_date='$travel_date' />";
		
	?>
		
		<div id="ticket-details"><br />
			<!--<div><b>Ticket Booking Details</b></div>-->
			<?php echo "
			&nbsp;<b>$bus->company_name</b><br />
			&nbsp;{$route['origin']} to {$route['destination']}<br />
			&nbsp;{$t_date}<br />
			<!--&nbsp;bus type<br />-->
			&nbsp;Seat no: {$seat_no}<br />
			&nbsp;Fare: {$fare} NGN<br />
			&nbsp;Departure: $departure_time
		</div>";
		?>
			
		<div id="payment-opt">	
			<fieldset data-role="controlgroup" data-mini="true"><br />
				<input type="radio" name="payment" id="Bank payment" value="Bank payment" checked="checked" />
				<label for="Bank payment">Bank Deposit</label>
		
				<input type="radio" name="payment" id="Home delivery" value="Home delivery"  />
				<label for="Home delivery">Pay cash on delivery</label>
				
				<input type="radio" name="mobile_money" id="Stanbic" value="Stanbic"  />
				<label for="Stanbic">Pay with Stanbic Mobile money</label>
			</fieldset>	
			<div id="bank-payment">
				<h3>Bank payment</h3>
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
			
			<div id="pay-on-delivery">
				<form method="post">
					<br />
					<div data-role="fieldcontain">
						<label for="address">Enter delivery address</label>
						<textarea id="delivery_addr" name="address"></textarea>
					</div>
				</form>
			</div>
		</div>
		
		<br />
		<div data-role="navbar">
			<ul>
				<li><a href="#" class="ui-btn-active ui-state-persist" id="personal_details">Enter personal details</a></li>
			</ul>
		</div>
		
		<div id="customer-details">
			<h3>Personal Details</h3>
			<form method="post" id="customer_info" data-ajax="false">
				<p><input type="text" name="customer_name" id="customer_name" placeholder="Customer's name" data-mini='true' /></p>
				<p><input type="text" name="customer_phone" id="customer_phone" placeholder="Customer's phone number" data-mini='true' /></p>
				<p><input type="text" name="next_of_kin_num" id="next_of_kin_num" placeholder="Next of kin phone number" data-mini='true' /></p>
				<p><input type="text" name="email" id="email" placeholder="Email address" data-mini='true' /></p>
				<input type="submit" value="Send booking details" data-mini="true" data-theme="a" />
			</form>
		</div>
		
		<div data-role="popup" id="seat-error" class="ui-content" data-transition="flip" data-theme="e" data-overlay-theme="a">
			<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
		</div>
</div>
</body>
</html>