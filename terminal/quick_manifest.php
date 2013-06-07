<?php
require_once("../includes/DB_CONNECT.php");
require_once("includes/fns.php");
$DB_CONNECTION = db_connect();

$sql = "UPDATE booking_details SET c_name = '', next_of_kin_phone = '', seat_no = '', address = '', fare = '' WHERE id = ''";


/*** Fetch bus details ***/
$sql = "SELECT bb.bus_no, sb.route_code, bb.travel_date, booked_id, route
		FROM   booked_buses AS bb
		JOIN   seat_booking AS sb ON bb.id = sb.bus_id
		JOIN   routes ON bb.route_code = routes.route_code
		WHERE  bb.id = '{$_REQUEST['id']}'";
$result = $DB_CONNECTION->query($sql);
$bus = $result->fetch_object();

/*** Fetch all the tickets ***/
$result = $DB_CONNECTION->query("SELECT id, ticket_no, c_name, next_of_kin_phone, seat_no FROM booking_details WHERE booked_id = '$bus->booked_id'");

docType();
printBanner();
?>
<style>
td, input[type=text] {font: 11px verdana}
input[type=text] {margin:0}
</style>
<script type="text/javascript" src="js/quick.js"></script>
<div class="container">
	<blockquote>
		Bus number: <?php echo $bus->bus_no;?><br />
		Route: <?php echo $bus->route; ?>
	</blockquote>
	<table class='table table-striped table-bordered' style='width:75%;'>
		<thead><tr><th>Ticket no</th><th>Customer's name</th><th>Next of kin phone</th><th>Seat no</th></tr></thead>
		<tbody>
			<form action="" method="post" id="manifest">
				<?php while ($ticket = $result->fetch_object()) {
					$seat_no = $ticket->seat_no == '0' ? '' : $ticket->seat_no; 
					echo "<tr id='{$ticket->id}' class='ticket-row'>
					<td>{$ticket->ticket_no}</td>
					<td><input type='text' id='c_name' value='$ticket->c_name' /></td>
					<td><input type='text' id='next_of_kin' value='$ticket->next_of_kin_phone' style='width:120px' /></td>
					<td><input type='text' id='seat_no' value='$seat_no' style='width:20px' /></td></tr>";
				} ?>
				<p><input type="submit" value=" Save " class="btn btn-primary" /></p>
			</form>
		</tbody>
	</table>
</div>
</body>
</html>