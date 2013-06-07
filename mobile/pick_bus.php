<?php
include_once("includes/header.html");
require_once("../includes/general_functions.php");

/*** Get the route_code of the selected route ***/
$origin      = filter($_GET['origin']);
$destination = filter($_GET['destination']);
$travel_date = filter($_GET['travel_date']);

$route_code = getRouteCode($origin, $destination);

/*** Get buses [ types and amenities ] that run the selected route ***/ 
$sql = "SELECT b.id as bus_id, b.bus_type, b.amenities, b.seats, t.company_name, dt.departure_time, f.* FROM buses AS b
		JOIN travels AS t ON b.travel_id = t.id
		JOIN fares as f ON t.id = f.travel_id AND f.route_code = '$route_code'
		LEFT JOIN departure_time AS dt ON dt.departure_order = 'Bus 1' AND b.travel_id = dt.travel_id AND dt.route_code = '$route_code'
		WHERE b.route_code = '$route_code'";
$result = $DB_CONNECTION->query($sql);
	
?>
<div data-role="page" id="pick_bus">
	<div data-role="header">
		<h1>Pick bus</h1>
		<a href="index.php" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
	</div>

	<div data-role="content">
		<?php
			$n = 0;
			while ($info = $result->fetch_assoc()) {
				extract($info);
				/*** Determine bus type and set appropriate fields ***/
				if ($seats == 11) {
					$fare = $executive_fare;
				} elseif ($seats < 20) {
					$fare = $hiace_fare;
				} else {
					$fare = $luxury_fare;
				}
				
				if (($n % 2) == 0) $bg = "background-color:#f8f8ff; border-bottom:#ccc solid thin; margin:6px 0px";
				else $bg = "background-color=#fff; border-bottom:#ccc solid thin; margin:6px 0px";
				$query_string = "?origin=$origin&destination=$destination&fare=$fare&num_of_seats=$seats&travel_id=$travel_id&travel_date=$travel_date&bus_type_id=$bus_id&departure_time=$departure_time";
				echo "<a href='seating.php$query_string' style='color:#333'>
					<div class='ui-grid-a' style='font:normal 11px verdana;$bg'>
					<div class='ui-block-a'>
						<p><b>{$company_name}</b></p>
						{$bus_type}<br />
						{$amenities}
					</div>
					<div class='ui-block-b' style='text-align:right; padding-right:3%'>
						<p>{$departure_time}</p>
						<b>₦{$fare}</b>
					</div>
				</div></a>";
				$n++;
			}
		?>
	</div>
	
</div>

</body>
</html>