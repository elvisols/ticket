<?php
require_once("../../includes/DB_CONNECT.php");
if (!isset($DB_CONNECTION)) $DB_CONNECTION = db_connect();

$sql = "DELETE FROM seat_booking WHERE booked_seats = ''";
try {
	$result = $DB_CONNECTION->query($sql);
	echo "Database cleaned";	
} catch (Exception $e) {
	echo $e->getMessage();
}

// Do some check on executive bus (11 seaters)
//$sql = "UPDATE seat_booking
//		SET departure_order = '1' 
//		WHERE seating_arrangement = 11 AND departure_order <> '1'";
//$DB_CONNECTION->query($sql);
?>