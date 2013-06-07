<?php

/***************************************************************************
 *                               general_functions.php
 *                              -----------------------
 *   
 *	@By Okolo Chibuzo << On Monday 13th August, 2012 >>
 *  
 *
 ***************************************************************************/
 
require_once("DB_CONNECT.php");
if (!isset($DB_CONNECTION)) $DB_CONNECTION = db_connect();
define ("BASE_URL", "http://localhost/ticket/");

function docType() {
?>
	<!DOCTYPE html>
	<head>
	<meta charset="utf-8">
	<meta name="DESCRIPTION" content="Get the Best Online Bus Tickets Reservation Services with 4forty. Your Online Bus Tickets Booking is just a click away!">
	<meta name="KEYWORDS" content="Bus, Tickets, Booking, Reservation, Travels, Nigeria">
	<title>Online Bus Tickets Booking</title>
	<script src="<?php echo BASE_URL; ?>javascript/plugins/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="<?php echo BASE_URL; ?>javascript/universal.js" type="text/javascript"></script>
	<script src="<?php echo BASE_URL; ?>javascript/bootstrap.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="<?php echo BASE_URL; ?>javascript/dhtmlxcalendar.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>css/universal.css" media="all" />
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>css/datePicker.css" media="all" />
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
	<div class="navbar navbar-fixed-top">
		<div class='navbar-inner'>
			<div class='container'  style="height:55px !important;">
				<div class="pull-left">
					<a class="head" href="index.php"><img src="<?php echo BASE_URL; ?>images/logo.gif" id="logo" /></a>
				</div>
				
				<div class="pull-right" style="margin-top:8px">
					<ul class="nav">
						<li><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
						<li><a href="<?php echo BASE_URL; ?>sms_ticket.php">SMS Ticket</a></li>
						<li><a href="<?php echo BASE_URL; ?>check_fare.php">Check Fare</a></li>
						<li><a href="<?php echo BASE_URL; ?>cancel_ticket.php">Cancel Ticket</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</header>
<?php
}

function printFooter() {
?>
<div class="navbar navbar-fixed">
	<div class="navbar-inner">
		<div class="container">
			<div class="pull-right">	
				<ul class="nav" style="padding-top:8px">
					<li><a href='#'>FAQ</a></li>
					<li><a href='#'>Terms And Conditions</a></li>
					<li><a href='#'>Contact Us</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>

</body>
	</html>
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

function getStateName($state_id) {
	global $DB_CONNECTION;
	$result = $DB_CONNECTION->query("SELECT name FROM states_towns WHERE id = '$state_id'");
	$name = $result->fetch_assoc();
	return $name['name'];
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
		$destination = substr(filter($_REQUEST['destination']), 0, $d_num);
		$route_code  = $origin . $destination;
	}
	return $route_code;
}

function splitRouteMap($route_code) {
	global $DB_CONNECTION;
	$result = $DB_CONNECTION->query("SELECT route from routes WHERE route_code = '$route_code' ORDER BY route");
	$route_map = $result->fetch_assoc();
	$route = explode(" - ", $route_map['route']);
	return array('origin' => $route[0], 'destination' => $route[1]);
}

function addRoute() {
	global $DB_CONNECTION;
	
	$route_code = getRouteCode($_POST['origin'], $_POST['destination']);
	$route      = "{$_POST['origin']} - {$_POST['destination']}";
	$DB_CONNECTION->query("INSERT INTO routes (route_code, route) VALUES ('$route_code', '$route')");
	return $route_code;
}

function getTravelName($id) {
	global $DB_CONNECTION;
	$result = $DB_CONNECTION->query("SELECT company_name FROM travels WHERE id = '$id'");
	$name = $result->fetch_assoc();
	return $name['company_name'];
}
?>