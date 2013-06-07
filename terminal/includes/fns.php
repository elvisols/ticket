<?php
define ("BASE_URL", "http://localhost/ticket/");
require_once("../includes/DB_CONNECT.php");
if (!isset($DB_CONNECTION)) $DB_CONNECTION = db_connect();
require_once('settings.php');

function docType() {
?>
	<!DOCTYPE html>
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta charset="utf-8">
	<meta name="DESCRIPTION" content="Get the Best Online Bus Tickets Reservation Services with 4forty. Your Online Bus Tickets Booking is just a click away!">
	<meta name="KEYWORDS" content="Bus, Tickets, Booking, Reservation, Travels, Nigeria">
	<title>Online Bus Tickets Booking</title>
	<script src="<?php echo BASE_URL; ?>javascript/plugins/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="<?php echo BASE_URL; ?>javascript/bootstrap.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="<?php echo BASE_URL; ?>javascript/dhtmlxcalendar.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>css/universal.css" media="all" />
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>css/bootstrap.min.css" media="all" />
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>css/dhtmlxcalendar.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>css/dhtmlxcalendar_dhx_skyblue.css" />
	</head>
<?php	
}

function printBanner() {
?>
<body>
	<div id="modal-content"></div>
<header>	
	<!--- top menu starts here-->
	<div class="navbar navbar-fixed-top" >
		<div class='navbar-inner'>
			<div class='container'>
				<div id='top_menu'>
					<div class="pull-left">
						<span class="head" style="position:relative; top:15px"><?php echo TRAVEL_NAME; ?></span>
					</div>
					
					<div class="pull-right">
						<ul class="nav">
							<li><a href="sell_ticket.php">Sell Ticket</a></li>
							<li><a href="charts.php">Chart</a></li>
							<li><a href="reports.php">Manifest</a></li>
							<?php if ($_SESSION['uname'] == '4forty' || $_SESSION['uname'] == 'ossai') {
								echo "<li><a href='book_bus.php'>Book Bus</a></li>
									  <li><a href='backend.php'>Add Fare/Route</a></li>";
									}
								if (isset($_SESSION['uname'])) echo "<li><a href='logout.php'>[ Logout ]</a></li>";
							?>
						</ul>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</header>
<?php
}

function filter($value) {
	global $DB_CONNECTION;
	if (get_magic_quotes_gpc())
		$value = stripslashes($value);
	if (!is_numeric($value))
		$value = mysqli_real_escape_string($DB_CONNECTION, $value);
	return htmlspecialchars(trim($value));
}	

function getStateId($state_name) {
	global $DB_CONNECTION;
	$result = $DB_CONNECTION->query("SELECT id FROM states_towns WHERE name = '$state_name'");
	$id = $result->fetch_assoc();
	return $id['id'];
}

function getRouteCode($origin, $destination) {
	if ($destination == "Abakeliki") {
		$d_num = 4;
		$o_num = 3;
	} elseif ($origin == "Abakeliki") {
		$o_num = 4;
		$d_num = 3;
	} else {
		$d_num = 3;
		$o_num = 3;
	}
	$origin = substr($origin, 0, $o_num);
	if (strstr($destination, "/")) {
		$dual_routes   = explode("/", $destination);
		$merged_routes = implode("/", array(substr($dual_routes[0], 0, $d_num), substr($dual_routes[1], 0, $d_num)));
		$route_code    = $origin . $merged_routes;
	} else {
		$destination = substr($destination, 0, $d_num);
		$route_code  = $origin . $destination;
	}
	return $route_code;
}

function getDestination() {
	global $DB_CONNECTION;
	
	/*** Get route-codes active for this travel ***/
	$result = $DB_CONNECTION->query("SELECT route_code FROM travels WHERE id = '" . TRAVEL_ID . "'");
	$route = $result->fetch_assoc();
	$routes = explode(" ", $route['route_code']);
	/* array_pop($routes); */
	array_shift($routes);
	
	/*** Get the route maps for the route-codes, extract the destination and display ***/
	foreach ($routes AS $route) {
		$state = splitRouteMap($route);
		$state_id = getStateId($state['destination']);
		$destinations['states'][] = $state['destination'];
		echo "\t<option value='{$state_id}' data-route_code='{$route}'>{$state['destination']}</option>\n"; 
	}
	//return $destinations;
}

/**
function splitRouteMap($route_code) {
	global $DB_CONNECTION;
	$result = $DB_CONNECTION->query("SELECT route from routes WHERE route_code = '$route_code'");
	$route_map = $result->fetch_assoc();
	return substr($route_map['route'], strlen(SOURCE_NAME . " - "));
}*/
	
function splitRouteMap($route_code) {
	global $DB_CONNECTION;
	$result = $DB_CONNECTION->query("SELECT route from routes WHERE route_code = '$route_code' ORDER BY route");
	$route_map = $result->fetch_assoc();
	$route = explode(" - ", $route_map['route']);
	return array('origin' => $route[0], 'destination' => $route[1]);
}	

function getStateName($state_id) {
	global $DB_CONNECTION;
	$result = $DB_CONNECTION->query("SELECT name FROM states_towns WHERE id = '$state_id'");
	$name = $result->fetch_assoc();
	return $name['name'];
}

function getStaffUsername($staff_id) {
	global $DB_CONNECTION;
	
	$result = $DB_CONNECTION->query("SELECT username FROM workers WHERE id = '$staff_id'");
	$staff = $result->fetch_assoc();
	return $staff['username'];
}
?>