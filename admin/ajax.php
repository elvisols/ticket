<?php
require_once("../includes/general_functions.php");

if ($_REQUEST['op'] == 'mapped') {
	$state = filter($_GET['state']);
	$sql = "SELECT id, route_code FROM routes WHERE route LIKE '$state%'";
	$result = $DB_CONNECTION->query($sql);
	if (!$result) die ($result->error());
	$html = "";
	while ($row = $result->fetch_array()) {
		$route = splitRouteMap($row['route_code']);
				
		# Continue...
		$html .= "<div id='{$row['route_code']}'> >> {$route['destination']}<span>
		<input type='text' id='travel_{$row['id']}' class='add_route' style='margin-top:-5px' /> <input type='button' class='add_travel btn btn-small' value='Ok' style='margin-top:-12px' />
		<a class='remove_state' id='{$row['id']}' href='#'>X</a></span></div>\n";
	}
	echo $html;
}
elseif ($_REQUEST['op'] == 'add_route') echo addRoute();

elseif ($_REQUEST['op'] == 'remove_map')
{
	$id = filter($_REQUEST['map_id']);
	$DB_CONNECTION->query("DELETE FROM routes WHERE id = '$id'");
}
elseif ($_REQUEST['op'] == "add_route_to_travel")
{
	$route = filter($_POST['route_code']);
	$travel = filter($_POST['travel_id']);
	$route = $route . " ";
	$sql = "UPDATE travels SET route_code = CONCAT(route_code, '$route') WHERE id = '$travel'";
	$DB_CONNECTION->query($sql);
}
elseif ($_REQUEST['op'] == 'add_bus')
{
	$sql = "INSERT INTO buses (travel_id, bus_type, amenities, route_code, seats, departure_time, fare) VALUES
	('{$_POST['travel']}', '{$_POST['bus_type']}', '{$_POST['amenities']}', '{$_POST['route']}', '{$_POST['seats']}', '{$_POST['departure_time']}', '{$_POST['fare']}')";
	$DB_CONNECTION->query($sql);
}
?>