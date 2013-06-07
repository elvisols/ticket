<?php
session_start();
require_once("../includes/DB_CONNECT.php");
$DB_CONNECTION = db_connect();

$sql = "SELECT id, bb.bus_no, bb.route_code, no_of_seats, bb.departure_order, bb.travel_date, booked_id
		FROM booked_buses AS bb
		JOIN seat_booking AS sb ON sb.bus_id = bb.id
		WHERE id = '{$_REQUEST['bb_id']}'";
$result = $DB_CONNECTION->query($sql) or die (mysqli_error($DB_CONNECTION));
$bus = $result->fetch_object();

if ($bus->no_of_seats == 11) $fare_field = 'executive_fare';
elseif ($bus->no_of_seats < 20) $fare_field = 'hiace_fare';
else $fare_field = 'luxury_fare';

$fare  = getFare('1', $bus->route_code, $fare_field);
$state = splitRouteMap($bus->route_code);
$route = "{$state['origin']} to {$state['destination']}";

$date = date('Y-m-d');
$seats = '';
for ($i = 1; $i < $bus->no_of_seats + 1; $i++) {
	// Check if tickets has been printed
	$result1 = $DB_CONNECTION->query("SELECT seat_status FROM seat_booking WHERE booked_id = '$bus->booked_id'");
	$seat_status = $result1->fetch_object()->seat_status;
	if ($seat_status == "Full") break;
	
	/*** Generate ticket ref number ***/
	$find      = array('/', '+', '=', "\\", '|', '-');
	$replace   = array('X', 'Y', 'Z', 'U', 'O', '4');
	$ticket_no = strtoupper(str_replace($find, $replace, base64_encode(mcrypt_create_iv(6, MCRYPT_RAND))));
	
	$sql = "INSERT INTO booking_details (ticket_no, route_code, booked_id, fare, address, bus_no, date_booked, travel_date, staff_id, travel_id) VALUES
	('$ticket_no', '$bus->route_code', '{$_REQUEST['booked_id']}', '$fare', '{$state['destination']}', '$bus->bus_no', '$date', '$bus->travel_date', '{$_SESSION['staff_id']}', '1')";
	$DB_CONNECTION->query($sql);
	
	if (empty($seats))
		$seats .= $i; // The first seat;
	else
		$seats .= "," . $i;
	
	#$tickets['ticket'] = getTicket($ticket_no, '......................', '....................', $route, $i, $bus->bus_no, $bus->travel_date, $fare);
	#echo json_encode($tickets['ticket']);
}

$sql = "UPDATE seat_booking SET booked_seats = '$seats', seat_status = 'Full' WHERE booked_id = '$bus->booked_id'";
$DB_CONNECTION->query($sql);

$DB_CONNECTION->query("UPDATE booked_buses SET seat_status = 'Full' WHERE id = '$bus->id'");

//echo json_encode($tickets);

function getTicket($ticket_no, $name, $next_of_kin, $route, $seat_no, $bus_no, $travel_date, $fare) {
	return "<div class='line'><label class='ticket'>Ticket:</label> $ticket_no</div>
		<div class='line'><label class='ticket'>Customer name:</label> $name</div>
		<div class='line'><label class='ticket'>Next of kin no:</label> $next_of_kin</div>
		<div class='line'><label class='ticket'>Route:</label> $route</div>
		<div class='line'><label class='ticket'>Seat number:</label> $seat_no</div>
		<div class='line'><label class='ticket'>Bus number:</label>$bus_no</div>
		<div class='line'><label class='ticket'>Date of Travel:</label>$travel_date</div>
		<div class='line'><label class='ticket'>Amount:</label> $fare NGN</div>
		<br /><p><div class='line'><br />&nbsp;</div></p>";
}

function splitRouteMap($route_code) {
	global $DB_CONNECTION;
	$result = $DB_CONNECTION->query("SELECT route from routes WHERE route_code = '$route_code' ORDER BY route");
	$route_map = $result->fetch_assoc();
	$route = explode(" - ", $route_map['route']);
	return array('origin' => $route[0], 'destination' => $route[1]);
}

function getFare($travel_id, $route_code, $fare_field) {
	global $DB_CONNECTION;
	
	$result = $DB_CONNECTION->query("SELECT {$fare_field} FROM fares WHERE travel_id = '$travel_id' AND route_code = '$route_code'");
	return $result->fetch_object()->$fare_field;
}
?>