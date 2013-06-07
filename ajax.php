<?php
session_start();
require_once("includes/general_functions.php");

if (isset($_REQUEST['op'])) {
	if     ($_REQUEST['op'] == 'get_seating') 	       getSeats();
	elseif ($_REQUEST['op'] == 'handle_customer_info') handleCustomerInfo();
	elseif ($_REQUEST['op'] == 'cancel-ticket')        cancelTicket();
	elseif ($_REQUEST['op'] == 'get-customer-details') echo json_encode(getCustomersDetails($_GET['bd_id']));
}

/*************************************************************
 *	@Inputs
 *		- origin, destination,
 *		  travel_date, bus type/number of seater
 *
 *		- Fare and travel_id must be present for online booking
 *		- Bus position is optional
 *
 *	@Ouput
 *		Returns seating arrangement
**/		
function getSeats() {
	global $DB_CONNECTION;
	$bus_id = '';
	
	$_SESSION['origin']      = $_REQUEST['origin'];
	$_SESSION['destination'] = $_REQUEST['destination'];
	$_SESSION['travel_date'] = $_REQUEST['travel_date'];
	
	$route_code = getRouteCode($_REQUEST['origin'], $_REQUEST['destination']);
	$route = validateRouteCode($route_code);
	if ($route === true) {
		$_SESSION['route_code'] = $route_code;
	} else {
		echo $route;
		return false;
	}
	
/*** Get seat details for the loading bus on the selected route ***/
	$num_of_seats = $_REQUEST['num_of_seats'];
	$booked_seats = array();
	$seat_details = array();
	
	# Determine which seating arrangement and fare to select [ for seat_booking table ]
		if ($_REQUEST['num_of_seats'] == 11) {
			$fare_field = "executive_fare";
			$seating_criterial = "seating_arrangement = '11'";
			//$seating_criterial_booked_buses = "num_of_seats = '11'";
		} elseif ($_REQUEST['num_of_seats'] == 14 || $_REQUEST['num_of_seats'] == 15) {
			$fare_field = 'hiace_fare';
			$seating_criterial = "(seating_arrangement = '14' OR seating_arrangement = '15')"; // For seat_booking table
			//$seating_criterial_booked_buses = "num_of_seats = '{$_REQUEST['num_of_seats']}'"; // For booked bus table
		} elseif ($_REQUEST['num_of_seats'] > 20) {
			$fare_field = 'luxury_fare';
			$seating_criterial = "seating_arrangement = '{$_REQUEST['num_of_seats']}'"; // For seat_booking table
		}
		$seating_criterial_booked_buses = "(num_of_seats = '{$_REQUEST['num_of_seats']}'
			OR num_of_seats = '{$_REQUEST['num_of_seats']}' + '1')"; // For booked bus table
	
	# Get the travel's route fare [ For online booking ]
		if (isset($_REQUEST['fare'])) {
			$fare = $_REQUEST['fare'];
			$travel_id = $_REQUEST['travel_id'];
		} else {
		# Get route fare [ For offline ]
			require_once('terminal/includes/settings.php');
			$travel_id = TRAVEL_ID;
			$sql = "SELECT {$fare_field} FROM fares WHERE travel_id = '$travel_id' AND route_code = '{$_SESSION['route_code']}'";
			$result = $DB_CONNECTION->query($sql);
			$fare = $result->fetch_object();
			$fare = $fare->$fare_field;
		}
			
	# Check if this route is merged and get the merged bus
		$merged_id = 0;
		$merge_details = getMergedDetails($_REQUEST['travel_date'], $_REQUEST['destination']);
		if (@$merge_details['merged_route'] == $_REQUEST['destination']) {
			$merge_query = "SELECT * FROM seat_booking WHERE booked_id = '{$merge_details['going_booked_id']}' AND seat_status = 'Not Full'";
			$merged_id = $merge_details['merge_id'];
		}
		
	$where = "WHERE route_code = '{$_SESSION['route_code']}'
			  AND travel_date = '{$_REQUEST['travel_date']}' 
			  AND seat_status = 'Not full'";
			  
#	When auto pick bus is not used, used the bus' position to select a bus instead
	if (!empty($_REQUEST['bus'])) {
		$departure_order = $_REQUEST['bus'];
		$where .= " AND departure_order = '$departure_order'";
		//return;
	}
	
	# Check if a bus is already loading; or use merged routes booked_id if "$merge_query" is set
	$sql = "SELECT * FROM seat_booking {$where} AND {$seating_criterial}";
	if (isset($merge_query)) $sql = $merge_query;
	
	$result = $DB_CONNECTION->query($sql);
	
	if ($result->num_rows == 0) { // No bus is loading
		$bus_order = 0;
		# Check for the last filled bus, if any, and get the departure order/position
			$where1 = "WHERE route_code = '{$_SESSION['route_code']}'
					   AND travel_date = '{$_REQUEST['travel_date']}'
					   AND {$seating_criterial}
					   ORDER BY departure_order DESC LIMIT 0, 1";
			$result = $DB_CONNECTION->query("SELECT departure_order FROM seat_booking {$where1}");
			if ($result->num_rows > 0) {
				$order = $result->fetch_object();
				$bus_order = $order->departure_order;
			}

		if (isset($departure_order) && $departure_order > 0) {
			$where1 = "WHERE route_code = '{$_SESSION['route_code']}'
					   AND travel_date = '{$_REQUEST['travel_date']}'
					   AND {$seating_criterial} 
					   AND seat_status = 'Full' AND departure_order = '$departure_order'";
			$result = $DB_CONNECTION->query("SELECT seat_status FROM seat_booking {$where1}");
			if ($result->num_rows == 1) {
				echo "The selected bus is full";
				return;
			}
			//echo $bus_order;
			for ($i = 1; $i < $departure_order + 1; $i++) {
				if ($i <= $bus_order) continue;
				$where1 = "WHERE route_code = '{$_SESSION['route_code']}'
						   AND travel_date = '{$_REQUEST['travel_date']}'
						   AND departure_order = '$i'";
				
				$bus_info = insertBusForLoading($where1, $seating_criterial_booked_buses, $i, $fare, $travel_id);
			}
			
			$num_of_seats = $bus_info['num_of_seats'];
			$booked_id    = $bus_info['booked_id'];
		} else {
		# Check for the last filled bus, if any, and get the departure order/position, else set it as first bus
			if ($bus_order > 0) {
				$departure_order = $bus_order + 1; // For next bus
			} else {
				$departure_order = 1;
			}
			$where .= " AND departure_order = '$departure_order'";
			
			$bus_info = insertBusForLoading($where, $seating_criterial_booked_buses, $departure_order, $fare, $travel_id);
			$num_of_seats = $bus_info['num_of_seats'];
			$booked_id    = $bus_info['booked_id'];
		}
		
	} else { // A bus is loading
		$seat_details = $result->fetch_assoc();
		$num_of_seats = $seat_details['seating_arrangement'];
		$booked_seats = explode(",", $seat_details['booked_seats']);
		$booked_id    = $seat_details['booked_id'];
	}
	
	/*** build seats ***/
	if ($_REQUEST['num_of_seats'] > 20) {
		doLuxuryBusSeatingArrangement($fare, $booked_id, $booked_seats, $bus_id, $merged_id, $num_of_seats);
	} elseif ($_REQUEST['num_of_seats'] > 12) {
		doFifteenSeaterArrangement($fare, $booked_id, $booked_seats, $bus_id, $merged_id, $num_of_seats);
	} elseif ($_REQUEST['num_of_seats'] == 11) {
		doElevenSeaterArrangement($fare, $booked_id, $booked_seats, $bus_id, $merged_id, $num_of_seats);
	}
	return true;
}

/*** This function inserts either a virtual bus or an actual bus for loading ***/
function insertBusForLoading($where, $seating, $departure_order, $fare, $travel_id) {
	global $DB_CONNECTION;
	
	# Get the service charges for both online and offline bookings
	$charge = $DB_CONNECTION->query("SELECT offline_charge, online_charge FROM travels WHERE id = '$travel_id'");
	$charges = $charge->fetch_object();
	$offline_charge = $charges->offline_charge;
	$online_charge  = $charges->online_charge;
	
	# Check if there's an already booked bus for this route, get it if there is (thank me later)
	$sql = "SELECT bus_no, bb.id AS bb_id, num_of_seats FROM booked_buses AS bb
			JOIN bus_info AS bi ON bb.bus_id = bi.id {$where} AND {$seating}";
	$result = $DB_CONNECTION->query($sql) or die (mysqli_error($DB_CONNECTION));
	
	# If there's a booked bus, use it
	if ($result->num_rows == 1) {
		$buss = $result->fetch_object();
		$sql = "INSERT INTO seat_booking (route_code, seating_arrangement, departure_order, bb_id, bus_no, fare, offline_charge, online_charge, travel_date, travel_id) VALUES
			('{$_SESSION['route_code']}', '$buss->num_of_seats', '$departure_order', '$buss->bb_id', '$buss->bus_no', '$fare', '$offline_charge', '$online_charge', '{$_REQUEST['travel_date']}', '$travel_id')";
		$bus['num_of_seats'] = $buss->num_of_seats;
	} else {
	# There's no booked bus, add a virtual bus
		$sql = "INSERT INTO seat_booking (route_code, seating_arrangement, departure_order, fare, offline_charge, online_charge, travel_date, travel_id) VALUES
			('{$_SESSION['route_code']}', '{$_REQUEST['num_of_seats']}', '$departure_order', '$fare',  '$offline_charge', '$online_charge', '{$_REQUEST['travel_date']}', '$travel_id')";
		$bus['num_of_seats'] = $_REQUEST['num_of_seats'];
	}
	$DB_CONNECTION->query($sql);
	$bus['booked_id'] = $DB_CONNECTION->insert_id;
	return $bus;
}

function reserveSeat() {
	global $DB_CONNECTION;
	//$bus_id  = filter($_POST['bus_id']);
	$seat_no = !empty($_POST['seat_no']) ? filter($_POST['seat_no']) : die ("01"); // Error code 01 - No seat selected
	
	# If a bus (virtual/real) is already loading, get the booked seats
	$result = $DB_CONNECTION->query("SELECT bus_id, booked_seats, seating_arrangement FROM seat_booking WHERE booked_id = '{$_POST['booked_id']}'");
	
	$seat_details = $result->fetch_assoc();
	if (!empty($seat_details['booked_seats'])) {
		$booked_seats = explode(",", $seat_details['booked_seats']);
		
		# If there is any empty seat/array, remove it
		foreach ($booked_seats AS $key => $value) if (empty($value)) unset($booked_seats[$key]);
		$booked_seats = array_values($booked_seats);
		$num_of_seats_booked = count($booked_seats);
	
		/*** Make sure no seat number repeats itself, and that the selected seat ($seat_no) has not already been booked ***/
		$booked_seats = array_unique($booked_seats);
		if (in_array($seat_no, $booked_seats)) {
			return "02"; // Error code 02 - Seat number no longer available;
		}
		
		$booked_seats = implode(",", $booked_seats);
		$booked_seats .= ',' . $seat_no;
	} else {
		$booked_seats = $seat_no;
		$num_of_seats_booked = 1;
	}

	# START TRANSACTION
	$DB_CONNECTION->query("START TRANSACTION");
	$query_check = true;
/*** Check if the seats are filled ***/
	if ($num_of_seats_booked + 1 == $seat_details['seating_arrangement']) {
		$status = 'Full';
		$DB_CONNECTION->query("UPDATE booked_buses SET seat_status = '$status' WHERE id = '{$seat_details['bus_id']}'") ? null : $query_check = false;
		
		# UPDATE merged routes table if the route is merged
		$DB_CONNECTION->query("UPDATE merged_routes SET seat_status = '$status' WHERE id = '{$_POST['merged']}'") ? null : $query_check = false;
	} else {
		$status = 'Not full';
	}
	
	$DB_CONNECTION->query("UPDATE seat_booking SET booked_seats = '$booked_seats', seat_status = '$status'
						 WHERE booked_id = '{$_POST['booked_id']}'") ? null : $query_check = false;
	
	if ($query_check == false) $DB_CONNECTION->query("ROLLBACK");
	return true;
}


/*** Seating arrangement for 59/60 seater bus ***/
function doLuxuryBusSeatingArrangement($fare, $booked_id, $booked_seats, $bus_id, $merged_id, $num_of_seats) {
	$width = '600px';
	$style = "position:absolute; top:180px; width:150px; right:6px";
	$departure_time = isset($_REQUEST['departure_time']) ? $_REQUEST['departure_time'] : '00:00';
	$seat_arrangement = "<div id='seat_arrangement' style='width:{$width}' class='{$fare}'
		data-seating_arrangement='{$_REQUEST['num_of_seats']}' data-booked_id='$booked_id' data-departure_time='$departure_time'>
		<img src='images/x.png' class='close-image' />
		<p>Click on an available seat to select it. Click again to de-select it.</p><div id='seat_wrap' style='margin-left:10px'>
		<div id='right_seats'>\n<div class='cols steering'></div>\n";
	$counter = 0; $counter2 = 0;
	
	for ($i = 1; $i <= $num_of_seats; $i++) {
		$class = "class='seat'";
		
		if ($counter == 0) $seat_arrangement .= "<div class='cols'>";
		if ($counter < 2) {
			/*** exchange arrays to match seating arrangement ***/
			if ($i % 2 == 1) $seat = $i + 1;
			else $seat = $i - 1;
			if (in_array($seat, $booked_seats)) $class = "class='booked_seat'";
			$seat_arrangement .= "\t<div {$class} data-bus_id='{$bus_id}' data-hidden='no' title='Seat {$seat}' id='{$seat}'></div>";
			++$counter;
			if ($counter == 2) $seat_arrangement .= "</div>"; // Close cols
			if ($i != $num_of_seats) { continue; }
		} else {
			if ($counter2 < 2) $down_seats[] = $i;
			++$counter2;
			if ($counter2 == 2) $counter2 = $counter = 0;
			if ($i != $num_of_seats) { continue; }
		}
		
		$counter = 0;
		$seat_arrangement .= "\n</div>\n<div id='left_seats' style='margin-top:10px'><div class='cols'></div>\n";
		
		foreach ($down_seats AS $seat) {
			$class = "class='seat'";
			
			if ($seat % 2 == 1) ++$seat;
			else --$seat;
			if ($num_of_seats == 59 && $seat == 60) $seat = 59;		// Fixes a bug that makes the last seat 60 instead of 59 due to the rearrangement
			if ($counter == 0) $seat_arrangement .= "<div class='cols'>";
			if (in_array($seat, $booked_seats)) $class = "class='booked_seat'";
			$seat_arrangement .= "\t<div {$class} data-bus_id='{$bus_id}' data-hidden='no' title='Seat {$seat}' id='{$seat}'></div>";
			++$counter;
			if ($counter == 2) {
				// Close cols
				$seat_arrangement .= "</div>";
				$counter = 0;
			}
		}
		if ($counter == 1) $seat_arrangement .= "</div>";
		$seat_arrangement .= "\n</div>\n</div>";
	}
	
	echo $seat_arrangement .= doSeatingDetails($style);
}


/*** Do 14/15 seating arrangement ***/
function doFifteenSeaterArrangement($fare, $booked_id, $booked_seats, $bus_id, $merged_id, $num_of_seats) {
	$width = '390px';
	$style = "position:absolute; top:135px; width:150px;float:left";
	$steering_pos = ($num_of_seats > 14) ? "height:92px" : "height:71px";
	$counter = 0;
	$departure_time = isset($_REQUEST['departure_time']) ? $_REQUEST['departure_time'] : '00:00';
	$seat_arrangement = "<div id='seat_arrangement' style='width:{$width}' class='{$fare}' data-seating_arrangement='{$_REQUEST['num_of_seats']}' data-booked_id='$booked_id' data-merged='{$merged_id}' data-departure_time='{$departure_time}'>
		<img src='images/x.png' class='close-image' />
		<p>Click on an available seat to select it. Click again to de-select it.</p><div id='seat_wrap'>
		<div class='cols steering' style='{$steering_pos}; left:9px'></div>\n";
	$sign = '';
	for ($i = 1; $i <= $num_of_seats; $i++) {
		$class = "class='seat'";
		
		if ($i == 1 || $i == 2) {
			if ($i % 2 == 1) $seat = $i + 1;
			else $seat = $i - 1;
			if (in_array($seat, $booked_seats)) $class = "class='booked_seat'";
		} else
		/*** exchange arrays to match seating arrangement for second row ***/
		if ($i <= 11) {
			if ($i % 3 == 0) {
				$seat = $i + 2;
			} elseif ($i % 2 == 1 && $i != 7 || $i == 8) {
				$seat = $i - 2;
			} else $seat = $i;
			if (in_array($seat, $booked_seats)) $class = "class='booked_seat'";
		} else {
			if ($i == 12) {
				if ($num_of_seats == 15) $seat = $i + 3;
				else $seat = $i + 2;
			} elseif ($i == 13) {
				if ($num_of_seats == 15) $seat = $i + 1;
				else $seat = $i;
			} elseif ($i == 14) {
				if ($num_of_seats == 15) $seat = $i - 1;
				else $seat = $i - 2;
			} elseif ($i == 15) $seat = $i - 3;
			if (in_array($seat, $booked_seats)) $class = "class='booked_seat'";
		}
		
		if ($counter == 0 && $num_of_seats > 14 && $i != 12) $seat_arrangement .= "<div class='cols'><div style='width:20px; height:18px; margin:6px;'></div>";
		elseif ($counter == 0) $seat_arrangement .= "<div class='cols'>";
		$seat_arrangement .= "\t<div {$class} data-hidden='no' title='Seat {$seat}' id='{$seat}'></div>";
		++$counter;
		
		if ($i == 2) { # For two seats at the front
			$seat_arrangement .= "</div>"; // Close cols for the two seats at the front
			$counter = 0;
		}
		
		if ($counter == 3) {
			if ($num_of_seats == 15 && $i == 14) { $counter = 1; continue; }
			$counter = 0;
			$seat_arrangement .= "</div>"; // Close cols
		}
	}
	if ($counter == 1 || $counter == 2) $seat_arrangement .= "</div>";
	$seat_arrangement .= "\n</div>\n";
	
	echo $seat_arrangement .= doSeatingDetails($style);
}


/*** Do 11 seating arrangement ***/
function doElevenSeaterArrangement($fare, $booked_id, $booked_seats, $bus_id, $merged_id, $num_of_seats) {
	$width = '390px';
	$style = "position:absolute; top:135px; width:150px;float:left";
	$steering_pos = "height:95px";
	$counter = 0;
	$departure_time = isset($_REQUEST['departure_time']) ? $_REQUEST['departure_time'] : '00:00';
	$seat_arrangement = "<div id='seat_arrangement' style='width:{$width}' class='{$fare}' data-seating_arrangement='{$_REQUEST['num_of_seats']}' data-booked_id='$booked_id' data-merged='{$merged_id}' data-departure_time='{$departure_time}'>
		<img src='images/x.png' class='close-image' />
		<p>Click on an available seat to select it. Click again to de-select it.</p><div id='seat_wrap'>
		<div class='cols steering' style='{$steering_pos}; left:9px'></div>\n";
	$sign = '';
	for ($i = 1; $i <= $num_of_seats; $i++) {
		$class = "class='seat'";
		
		if ($i == 1) {
			$seat = $i;
			if (in_array($seat, $booked_seats)) $class = "class='booked_seat'";
		} elseif ($i < 8) {
			if ($i % 3 == 1) $seat = $i - 2;
			elseif ($i % 3 == 0) $seat = $i;
			else $seat = $i + 2;
			if (in_array($seat, $booked_seats)) $class = "class='booked_seat'";
		} else {
			if ($i == 8) $seat = $i + 3;
			elseif ($i == 9) $seat = $i + 1;
			elseif ($i == 10) $seat = $i - 1;
			elseif ($i == 11) $seat = $i - 3;
			if (in_array($seat, $booked_seats)) $class = "class='booked_seat'";
		}
		
		if ($counter == 0 && $seat != 11) $seat_arrangement .= "<div class='cols'><div style='width:20px; height:18px; margin:6px;'></div>";
		elseif ($counter == 0) $seat_arrangement .= "<div class='cols'>";
		$seat_arrangement .= "\t<div {$class} data-hidden='no' title='Seat {$seat}' id='{$seat}'></div>";
		++$counter;
		
		if ($i == 1) { # For the one seat at the front
			$seat_arrangement .= "</div>"; // Close cols for the two seats at the front
			$counter = 0;
		}
		
		if ($counter == 3) {
			if ($i == 10) { $counter = 1; continue; }
			$counter = 0;
			$seat_arrangement .= "</div>"; // Close cols
		}
	}
	//$seat_arrangement .= "</div>";
	$seat_arrangement .= "</div>\n</div>\n";
	
	echo $seat_arrangement .= doSeatingDetails($style);
}


function doSeatingDetails($style) {
	return "\n<div id='seat_tips'>\n<ul>\n
	\t<p><li id='available_seat'>Available Seat</li></p>\n
	\t<p><li id='selected_seat'>Selected Seat</li></p>\n
	\t<p><li id='booked_seat'>Booked Seat</li></p>\n
	</ul>\n</div>
	<div style='{$style}' id='seat_details'>
		Seat(s): <span id='picked_seat'></span><br />
		Amount: <span id='show_fare'></span>
	</div>
	
	<div style='clear:both; padding-top:15px'>
		<select name='boarding_point' data-mini='true'>
			<option value=''>Jibowu</option>
		</select>
		<a href='' style='margin-top:-10px' id='continue' data-theme='a' class='btn btn-inverse'>Continue</a>
	</div></div>";
}

/*** Sell ticket and save relevant details ***/
function handleCustomerInfo() {
	global $DB_CONNECTION;

	$name            = filter($_POST['customer_name']);
	$address         = isset($_POST['address']) ? filter($_POST['address']) : '';
	$next_of_kin     = filter($_POST['next_of_kin_phone']);
	$customer_phone = isset($_POST['customer_phone']) ? filter($_POST['customer_phone']) : '';
	$email           = isset($_POST['email']) ? filter($_POST['email']) : ''; // for online booking only
	
/*** Check if customer already exist ***/
	#$result = $DB_CONNECTION->query("SELECT cid FROM customers WHERE next_of_kin_phone = '$next_of_kin' AND c_name = '$name'"); // Add email later
	#$info = $result->fetch_assoc();
	#
	#if (!isset($info['cid'])) {
	#	$sql = "INSERT INTO customers (c_name, next_of_kin_phone, phone_no, email) VALUES ('$name', '$next_of_kin', '$customers_phone', '$email')";
	#	$DB_CONNECTION->query($sql);
	#	$cid = $DB_CONNECTION->insert_id;
	#} else {
	#	$cid = $info['cid'];
	#}
	
/*** Generate ticket ref number ***/
	$find      = array('/', '+', '=', "\\", '|', '-');
	$replace   = array('X', 'Y', 'Z', 'U', 'O', '4');
	$ticket_no = strtoupper(str_replace($find, $replace, base64_encode(mcrypt_create_iv(6, MCRYPT_RAND))));
	
/*** Get travel fare ***/
	if ($_POST['bus_type'] == 11) $fare_field = 'executive_fare';
	elseif ($_POST['bus_type'] < 20) $fare_field = 'hiace_fare';
	else $fare_field = 'luxury_fare';
	$stmt = $DB_CONNECTION->query("SELECT {$fare_field} FROM fares WHERE route_code = '{$_SESSION['route_code']}'") or die (mysqli_error($DB_CONNECTION));
	$amount = $stmt->fetch_object()->$fare_field;
	if ($next_of_kin === "0000") $amount = 3500; // Ifex ticket cost
	
/*** Get bus no ***/
	$bus_no = '';
	$sql = "SELECT bus_no FROM seat_booking WHERE booked_id = '{$_POST['booked_id']}'";
	$result = $DB_CONNECTION->query($sql);
	$bus = $result->fetch_assoc();
	if ($bus['bus_no'] == '') $bus_no = '-';
	else $bus_no = $bus['bus_no'];

	# Reserve the seat number selected
	$seat_status = reserveSeat();
	if ($seat_status !== true) {
		echo $seat_status;
		return false;
	}
	
/*** Add booking details ***/
	$query_check = true;
	if (isset($_POST['payment_opt'])) { // Online booking
		if     ($_POST['payment_opt'] == 'Bank payment') $duration = "01:00:00";
		elseif ($_POST['payment_opt'] == 'Home delivery') $duration = "02:45:00";
		$travel = $_POST['travel_id'];
		$staff  = '0';
		$bus_type_id = $_POST['bus_type_id'];
		$online = 'Yes';
		$bln_online = true;
	} else { // Offline booking
		require_once('terminal/includes/settings.php');
		$travel = TRAVEL_ID;
		$staff  = $_SESSION['staff_id'];
		$bus_type_id = '0';
		$online = "No";
		$bln_online = false;
	}
	
	$sql = "INSERT INTO booking_details (ticket_no, booked_id, route_code, fare, seat_no, c_name, next_of_kin_phone, phone_no, address, bus_no, bus_type_id, date_booked, travel_date, online, staff_id, travel_id) VALUES
	('$ticket_no', '{$_POST['booked_id']}', '{$_SESSION['route_code']}', '$amount', '{$_POST['seat_no']}', '$name', '$next_of_kin', '$customer_phone', '$address', '$bus_no', '$bus_type_id', NOW(), '{$_REQUEST['travel_date']}', '$online', '$staff', '$travel')";
	$DB_CONNECTION->query($sql) ? null : $query_check = false;
	$bd_id = $DB_CONNECTION->insert_id;
	
	# Record online booking for payment management
	if ($bln_online == true) {
		$sql = "INSERT INTO online_booking (route, payment_opt, payment_status, grace_duration, booking_details_id, email, ticket_no, travel_date, date_booked) VALUES
			('{$_POST['route']}', '{$_POST['payment_opt']}', 'Pending', '$duration', '$bd_id', '$email', '$ticket_no', '{$_REQUEST['travel_date']}', NOW())";
		$DB_CONNECTION->query($sql) ? null : $query_check = false;
	}
	
	if ($query_check == true) {
		$DB_CONNECTION->query("COMMIT");
		if ($bln_online == true) echo $ticket_no;
	} else {
		$DB_CONNECTION->query("ROLLBACK");
		echo "03"; // Transaction failed
		return false;
	}
	
	if ($bln_online == true) return true;
	
	$ticket = getTicket($ticket_no, $name, $next_of_kin, "{$_SESSION['origin']} to {$_SESSION['destination']}", $_POST['seat_no'], $bus_no, $_REQUEST['travel_date'], $amount);
	echo $ticket;
	return true;
}


function getTicket($ticket_no, $name, $next_of_kin, $route, $seat_no, $bus_no, $travel_date, $fare) {
	return "<div class='line'><label class='ticket'>Ticket:</label> $ticket_no</div>
		<div class='line'><label class='ticket'>Customer name:</label> $name</div>
		<div class='line'><label class='ticket'>Next of kin no:</label> $next_of_kin</div>
		<div class='line'><label class='ticket'>Route:</label> $route</div>
		<div class='line'><label class='ticket'>Seat number:</label> $seat_no</div>
		<div class='line'><label class='ticket'>Bus number:</label>$bus_no</div>
		<div class='line'><label class='ticket'>Date of Travel:</label>$travel_date</div>
		<div class='line'><label class='ticket'>Amount:</label> $fare NGN</div>
		<div style='text-align:center; font-style:italic'>No refund of money after payment</div>";
}


function getMergedDetails($date, $route) {
	global $DB_CONNECTION;
	
	$sql = "SELECT id, booked_ids, going_route, merged_route FROM merged_routes
			WHERE travel_date = '$date' AND seat_status = 'Not Full' AND merged_route = '$route'";
	$merged = $DB_CONNECTION->query($sql);

	$merge_details = array();
	
	if ($merged->num_rows > 0) {
		$merge = $merged->fetch_object();
		$booked_ids                       = explode(',', $merge->booked_ids);
		$merge_details['merge_id']        = $merge->id;
		$merge_details['merged_route']    = $merge->merged_route;
		$merge_details['going_route']     = $merge->going_route;
		$merge_details['going_booked_id'] = $booked_ids[0];
	}
	return $merge_details;
}


function getCustomersDetails($bd_id) {
	global $DB_CONNECTION;
	
	$result = $DB_CONNECTION->query("SELECT c_name, address, next_of_kin_phone FROM booking_details WHERE id = '$bd_id'");
	$customers = $result->fetch_assoc();
	
	return $customers;
}


function cancelTicket() {
	global $DB_CONNECTION;
	
	$sql = "SELECT bs.booked_seats, bs.booked_id, bs.bus_id, bd.seat_no FROM booking_details AS bd
			JOIN seat_booking AS bs ON bd.booked_id = bs.booked_id WHERE bd.id = '{$_POST['ticket_id']}'";
	$result = $DB_CONNECTION->query($sql);
	if ($result->num_rows == 1) {
		$data = $result->fetch_object();
	} else {
		// Print error
		return false;
	}
	
/*** Remove seat number from the booked seat ***/
	$seats = explode(",", $data->booked_seats);
	$seat_no = $data->seat_no;

	foreach ($seats AS $key => $value) if ($seats[$key] == $seat_no) unset($seats[$key]);
	//$filtered_seats = array_filter($seats, function ($seat) use ($seat_no) { return ($seat != $seat_no); } );
	$num_of_remaining_seats = count($seats);
	$remaining_seats = implode(',', $seats);
	
/*** Cancel ticket, using transaction ***/
	$query_check = true;
	$DB_CONNECTION->query("START TRANSACTION");
	# Update seat_booking
	$sql = "UPDATE seat_booking SET booked_seats = '$remaining_seats', seat_status = 'Not full'
			WHERE booked_id = '$data->booked_id'";
	$DB_CONNECTION->query($sql) ? null : $query_check = false;
	
	# Update booked bus seat status and remaining seat number [ If a bus has been assigned to the booking ]
	$sql = "UPDATE booked_buses SET seat_status = 'Not full' WHERE id = '$data->bus_id'";
	$DB_CONNECTION->query($sql) ? null : $query_check = false;
	
	# If this bus was merged, then mark it as not full in the merge table
	$DB_CONNECTION->query("UPDATE merged_routes SET seat_status = 'Not full' WHERE going_booked_id = '$data->booked_id'");
	
	# Delete the ticket
	$DB_CONNECTION->query("DELETE FROM booking_details WHERE id = '{$_POST['ticket_id']}'") ? null : $query_check = false;
	
	if ($query_check == true) {
		$DB_CONNECTION->query("COMMIT");
	} else {
		$DB_CONNECTION->query("ROLLBACK");
	}
	return true;
}

function validateRouteCode($route_code) {
	global $DB_CONNECTION;
	
	$result = $DB_CONNECTION->query("SELECT id FROM routes WHERE route_code = '$route_code'");
	if ($result->num_rows == 0) return "04"; // Invalid route code
	return true;
}
?>