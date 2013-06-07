<?php
include_once("includes/header.html");

if (isset($_POST['submit'])) {
	/*** Get the route_code of the selected route ***/
	$destination = $_POST['destination'];
	$origin      = $_POST['origin'];
	
	require_once("../includes/DB_CONNECT.php");
	if (!isset($DB_CONNECTION)) $DB_CONNECTION = db_connect();
	require_once("includes/fns.php");
	$route_code = getRouteCode($origin, $destination);
	
	/*** Select the the fare for the route_code ***/
	$sql = "SELECT f.*, bus_type, seats, company_name FROM buses AS b
			JOIN fares as f ON b.travel_id = f.travel_id AND f.route_code = '$route_code'
			JOIN travels AS t ON b.travel_id = t.id
			WHERE b.route_code = '$route_code'";
	$result = $DB_CONNECTION->query($sql) or die (mysqli_error($DB_CONNECTION));
	if ($result->num_rows > 0) { echo "yeah";
		$amount = "<div><b>From {$origin} to {$destination}:</b></div>";
		$temp_company_name = '';
		while ($row = $result->fetch_assoc()) {
			extract($row);
			echo "here $company_name";
			if ($company_name != $temp_company_name) $amount .= "<div style='font: 11px verdana'><b>{$company_name}</b></div>";
			if ($seats == 11) {
					$fare = $executive_fare;
			} elseif ($seats < 20) {
				$fare = $hiace_fare;
			} else {
				$fare = $luxury_fare;
			}
			$amount .= "<div style='font: 11px verdana; padding:3px'>{$bus_type} - ₦{$fare}</div>";
			$temp_company_name = $company_name;
		}
	}
	
	if (!isset($amount)) {
		$amount = "Not available at this moment";
	}
	//$amount .= "</dl>";
}
?>

<div data-role="page">
	<div data-role="header">
		<h1>Check transport fare</h1>
		<a href="index.php" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
	</div>

	<div data-role="content">
		<?php echo isset($amount) ? "<div class='ui-body ui-body-e'>$amount</div>" : ''; ?>
		<form action="check_fare.php" method="post" dataajax="false">
			<label for="origin"> &nbsp;From</label>
			<select type="text" name="origin" id="origin">
				<option value="Lagos">Lagos</option>
			</select>
			
			<label for="destination"> &nbsp;To</label>
			<select name="destination" id="destination">
				<?php
					require_once("../includes/DB_CONNECT.php");
					if (!isset($DB_CONNECTION)) $DB_CONNECTION = db_connect();
					
					$result = $DB_CONNECTION->query("SELECT route_code FROM travels WHERE id = '1'");
	
					$route = $result->fetch_assoc();
					$routes = explode(" ", $route['route_code']);
					array_pop($routes); // Remove the extra space
					
					/*** Get the route maps for the route-codes, extract the destination and display ***/
					foreach ($routes AS $route) {
						$result = $DB_CONNECTION->query("SELECT route, id from routes WHERE route_code = '$route'");
						$route_map = $result->fetch_assoc();
						$destination = substr($route_map['route'], strlen("Lagos - "));
						if ($destination != "Lagos")
						echo "<option value='{$destination}'>{$destination}</option>";
					}
				?>
			</select>
			
			<p><br /><br /><input type="submit" name="submit" value="Check fare" data-theme="a" /></p>
	</div>
</div>

</body>
</html>