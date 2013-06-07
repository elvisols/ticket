<?php
ini_set('max_execution_time', 300);
define("TRAVEL_ID", "1");

// Get service charge for this travel

if (strtotime('now') > strtotime('8:00 PM')) {
	# Connect to online database server
	$dbh = connectToDBOnline();
		
	// Cancel all unpaid reservations, thank you
	
	$tomorrow  = mktime(0, 0, 0, date("m")  , date("d") + 1, date("Y"));
	//$travel_date = date('Y-m-d', $tomorrow);
	$travel_date = "2013-03-12";
	
	# Connect to offline database
	require_once("../includes/DB_CONNECT.php");
	$DB_CONNECTION = db_connect();
	
	# Lets see if the data we want has already been pulled down
	$result = $DB_CONNECTION->query("SELECT id FROM sync_record WHERE travel_date = '$travel_date' AND offline = 'Synched'");
	if ($result->num_rows > 0) {
		die ("This has already been done.");
	}
	
	$query_check = true;
	$sql = "SELECT * FROM seat_booking WHERE travel_date = '$travel_date' AND travel_id = '" . TRAVEL_ID . "'";
	$stmt = $dbh->query($sql);
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		// Insert loading bus
		extract($row);
		$DB_CONNECTION->query("START TRANSACTION");
		$sql1 = "INSERT INTO seat_booking
		(route_code, booked_seats, seating_arrangement, departure_order, travel_id, seat_status, fare, offline_charge, online_charge, travel_date, time_stamp) VALUES
		('$route_code', '$booked_seats', '$seating_arrangement', '$departure_order', '$travel_id', '$seat_status', '$fare', '2', '6', '$travel_date', '$time_stamp')";
		$DB_CONNECTION->query($sql1) ? null : $query_check = false;
		if ($query_check == false) die("First insert failed"); // Break
		else echo "First insert suceeded<br />";
		$new_booked_id = $DB_CONNECTION->insert_id;
		
	/*** Handle ticket records ***/
		$query = "SELECT * FROM booking_details WHERE booked_id = '{$row['booked_id']}'";
		$stmt1 = $dbh->query($query);
		while ($record = $stmt1->fetch(PDO::FETCH_ASSOC))
		{
			extract($record);
			echo "Inside second online query - {$ticket_no}<br />";
		#/*** Handle customer's/traveler's data ***/
		#	$stmt2 = $dbh->query("SELECT * FROM customers WHERE cid = '{$record['cid']}'"); // Online
		#	$customers = $stmt2->fetch(PDO::FETCH_ASSOC);
		#	
		#	# Search for customer offline
		#	$result = $DB_CONNECTION->query("SELECT cid FROM customers WHERE next_of_kin_phone = '{$customers['next_of_kin_phone']}' AND c_name = '{$customers['c_name']}'"); // Add email later
		#	$info = $result->fetch_assoc();
		#	
		#	/*** If customer doesn't exists offline, insert the new customer, else use the details of the existing customer ***/
		#	if (!isset($info['cid'])) {
		#		$customer_sql = "INSERT INTO customers (c_name, next_of_kin_phone, email, phone_no) VALUES
		#		('{$customers['c_name']}', '{$customers['next_of_kin_phone']}', '{$customers['email']}', '{$customers['phone_no']}')";
		#		$DB_CONNECTION->query($customer_sql) ? null : $query_check = false;
		#		if ($query_check == false) print("Second insert failed<br />");  // Break
		#		else echo "Second insert suceeded<br />";
		#		$cid = $DB_CONNECTION->insert_id;
		#	} else {
		#		$cid = $info['cid'];
		#	}
			
			// Insert booking details offline
			$sql2 = "INSERT INTO booking_details
				(ticket_no, booked_id, route_code, seat_no, fare, online, date_booked, travel_date, travel_id, c_name, next_of_kin_phone, address) VALUES
			('$ticket_no', '$new_booked_id', '$route_code', '$seat_no', '$fare', 'Yes', '$date_booked', '$travel_date', '$travel_id', '$c_name', '$next_of_kin_phone', '$address')";
			
			try {
				$DB_CONNECTION->query($sql2) ? null : $query_check = false;
			} catch (PDOException $e) {
				echo $e->getMessage();
			}
			
			if ($query_check == false) print("Third insert failed<br />");  // Break
			else echo "Third insert suceeded<br />";
		}
	}
	if ($query_check == true) {
		$DB_CONNECTION->query("COMMIT");
		echo "Changes committed";
		// Record the synching
		$sql = "INSERT INTO sync_record (travel_date, travel_id, offline) VALUES ('$travel_date', '" . TRAVEL_ID . "', 'Synched')";
		$dbh->exec($sql);
		$DB_CONNECTION->query($sql);
	} else {
		$DB_CONNECTION->query("ROLLBACK");
	}
}
/*** Upload today's details online ***/
elseif (strtotime('now') > strtotime('12:00 PM'))
{
	# Connect to offline database
	require_once("../includes/DB_CONNECT.php");
	$DB_CONNECTION = db_connect();
	
	$today = date('Y-m-d'); // Get today's date
	
	# Lets see if the records have been synched already
	$result = $DB_CONNECTION->query("SELECT id FROM sync_record WHERE travel_date = '$today' AND online = 'Synched' AND travel_id = '" . TRAVEL_ID . "'");
	if ($result->num_rows > 0) {
		die ("Todays offline record has been synched online, already");
	}
	
	# Get from offline, all seat booking records for today
	$seat_bookings_result = $DB_CONNECTION->query("SELECT * FROM seat_booking WHERE travel_date = '$today'");
	
	# Connect to online database server
	$dbh = connectToDBOnline();
	$dbh->beginTransaction();
	$query_check = true;
	
	# Delete initial booking details online
	$dbh->exec("DELETE FROM booking_details WHERE travel_date = '$today'");
	echo "Deleted booking_details<br />";
	
	# Delete seat booking records online
	$dbh->exec("DELETE FROM seat_booking WHERE travel_date = '$today'");
	echo "Deleted seat_booking<br />";
	
	# Upload complete booking details records for today, online
	while ($row = $seat_bookings_result->fetch_assoc()) {
		extract($row);
		$sql = "INSERT INTO seat_booking
		(bus_no, travel_id, route_code, booked_seats, seating_arrangement, departure_order, seat_status, offline_charge, online_charge, fare, travel_date) VALUES
		('$bus_no', '" . TRAVEL_ID . "', '$route_code', '$booked_seats', '$seating_arrangement', '$departure_order', '$seat_status', '$offline_charge', '$online_charge', '$fare', '$today')";
		try {
			$dbh->exec($sql);
			$new_booked_id = $dbh->lastInsertId();
		} catch (PDOException $e) {
			$query_check = false;
		}
		
		# Get manifest audit for this bus offline
		$m_sql = "SELECT * FROM manifest audit WHERE booked_id = '$booked_id'";
		$m_result = $DB_CONNECTION->query($m_sql);
		$audit = $m_result->fetch_assco();
		
		# Insert into manifest audit, online
		$m_sql = "INSERT INTO manifest_audit (booked_id, load_cost, drivers_expenses, audit, travel_id)
				  VALUES ('$new_booked_id', '$load_cost', '$drivers_expenses', '$audit', '$travel_id')";
		try {
			$dbh->exec($m_sql);
		} catch (PDOException $e) {
			$query_check = false;
		}
		
		$booking_details_result = $DB_CONNECTION->query("SELECT * FROM booking_details WHERE booked_id = '$booked_id'");
		while ($row1 = $booking_details_result->fetch_assoc()) {
			extract ($row1);
			
			// Insert booking details offline
			$sql2 = "INSERT INTO booking_details
			(ticket_no, booked_id, route_code, bus_no, seat_no, fare, online, date_booked, travel_date, travel_id, c_name, next_of_kin_phone, address) VALUES
			('$ticket_no', '$new_booked_id', '$route_code', '$bus_no', '$seat_no', '$fare', '$online', '$date_booked', '$travel_date', '" . TRAVEL_ID . "', '$c_name', '$next_of_kin_phone', '$address')";
			try {
				$dbh->exec($sql2);
			} catch (PDOException $e) {
				$query_check = false;
			}
		}
	}
	if ($query_check) { 
		$sql = "UPDATE sync_record SET online = 'Synched' WHERE travel_id = '" . TRAVEL_ID . "' AND travel_date = '$travel_date'";
		$dbh->exec($sql);
		$DB_CONNECTION->query($sql);
		$dbh->commit();
		echo "Done well";
	} else {
		$dbh->rollBack();
		echo "There was an error";
	}
} else {
	echo "What are you thinking?";	
}


function connectToDBOnline() {
	$dsn = 'mysql:host=184.173.209.193;dbname=oya_bus_ticket';
	$options = array(
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
	); 
	
	try {
		$dbh = new PDO($dsn, 'oya', 'g!K,#ypmBJ)4', $options);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $dbh;
	} catch (PDOException $e) {
		echo $e->getMessage();
		exit;
	}
}

?>