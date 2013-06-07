<?php
//session_start();
require_once("general_functions.php");

$q = filter($_GET['q']);
if ($_GET['op'] == 'get_states') {
	$sql = "SELECT name FROM states_towns WHERE name LIKE '%$q%'";
	$result = $DB_CONNECTION->query($sql);
	while ($row = $result->fetch_array()) {
		echo $row['name']."\n";
	}
	
} elseif ($_GET['op'] == 'destination') {
	$opt = isset($_GET['opt']) ? $_GET['opt'] : '';
	$sql = "SELECT route_code FROM travels WHERE id = '$opt'";
	$result = $DB_CONNECTION->query($sql);
	
	$route = $result->fetch_assoc();
	$routes = explode(" ", $route['route_code']);
	array_pop($routes); // Remove the extra space
	
	/*** Get the route maps for the route-codes, extract the destination and display ***/
	foreach ($routes AS $route) {
		$result = $DB_CONNECTION->query("SELECT route, id from routes WHERE route_code = '$route'");
		$route_map = $result->fetch_assoc();
		$destination = substr($route_map['route'], strlen("Lagos - "));
		echo $destination . "|%&*" . $route_map['id']."\n";
	}
	
} elseif ($_GET['op'] == 'travel') {
	$sql = "SELECT id, company_name FROM travels WHERE company_name LIKE '%$q%'";
	$result = $DB_CONNECTION->query($sql);
	while ($row = $result->fetch_array()) {
		echo $row['company_name']."|%&*".$row['id']."\n";
	}
}
?>	