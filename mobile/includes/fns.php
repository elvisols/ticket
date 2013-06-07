<?php

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
		$destination = substr($_REQUEST['destination'], 0, $d_num);
		$route_code = $origin . $destination;
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
?>