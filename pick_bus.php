<?php
session_start();
require_once("includes/general_functions.php");

if (empty($_GET['travel_date'])) {
	header("Location: index.php?msg=m_date");
} elseif (empty($_GET['destination'])) {
	header("Location: index.php?msg=destination");
} else {
	/*** Get the route_code of the selected route ***/
	$origin      = filter($_GET['origin']);
	$destination = filter($_GET['destination']);
	
	$route_code = getRouteCode($origin, $destination);
	
	/*** Get buses [ types and amenities ] that run the selected route ***/ 
	$sql = "SELECT b.id as bus_id, b.bus_type, b.amenities, b.seats, t.company_name, dt.departure_time, f.* FROM buses AS b
			JOIN travels AS t ON b.travel_id = t.id
			JOIN fares as f ON t.id = f.travel_id AND f.route_code = '$route_code'
			LEFT JOIN departure_time AS dt ON dt.departure_order = 'Bus 1' AND b.travel_id = dt.travel_id AND dt.route_code = '$route_code'
			WHERE b.route_code = '$route_code'";
	$result = $DB_CONNECTION->query($sql) or die (mysqli_error($DB_CONNECTION));
} 

docType();
printBanner();

?>
<style>
td {text-align:center}
th > a {color:#333; font-size:12px}
.close-image {position:absolute; top:3px; right:3px; cursor:pointer}
</style>
<link rel="stylesheet" type="text/css" href='css/seats.css' />
<div id='content'>
	<div id="route_details">
		<?php echo "{$_GET['origin']} - {$_GET['destination']} | {$_GET['travel_date']} "; ?>
	</div>
	<table cellpadding="15" class='sortable table' border='0' style="width:100%; border-collapse:collapse; font:11px Verdana; color:#666">
	<thead><tr style='border-bottom:#ccc solid thin; border-top:#ccc solid;'>
		<th><a href="#" class='sortableTableheader'>Travels</a></th>
		<th><a href="#" class='sortableTableheader'>Bus Type</a></th>
		<th><a href="#" class='sortableTableheader'>Amenities</a></th>
		<th style='text-align:center'><a href="#" class='sortableTableheader'>Seats</a></th>
		<th><a href="#" class='sortableTableheader'>Departure</a></th>
		<th><a href="#" class='sortableTableheader'>Fare(N)</a></th>
	</tr></thead>
    
	<tbody>
		<?php
			$n = 0;
			while ($info = $result->fetch_assoc()) {
				/*** Determine bus type and set appropriate fields ***/
				if ($info['seats'] == 11) {
					$fare = $info['executive_fare'];
				} elseif ($info['seats'] < 20) {
					$fare = $info['hiace_fare'];
				} else {
					$fare = $info['luxury_fare'];
				}
				
				if (($n % 2) == 0) $bg = "bgcolor='#f8f8ff'";
				else $bg = "bgcolor='#ffffff'";
				echo "<tr style='border-bottom:#ccc solid thin' id='{$info['bus_id']}' $bg>
					<td><b>{$info['company_name']}</b></td>
					<td>{$info['bus_type']}</td>
					<td>{$info['amenities']}</td>
					<td style='text-align:center'>{$info['seats']}<br /><br />
						<a class='seats btn btn-inverse btn-small' href='#' data-fare='{$fare}' data-destination='{$_GET['destination']}' data-travel_date='{$_GET['travel_date']}' data-origin='{$_GET['origin']}' data-num_of_seats='{$info['seats']}' data-bus_id='{$info['bus_id']}' data-travel_id='{$info['travel_id']}' data-departure_time='{$info['departure_time']}'><i class='icon-th-list icon-white'></i> Pick a Seat</a>
					</td>
					<td>{$info['departure_time']}</td>
					<td>{$fare} NGN</td>
				</tr>
				<tr style='border-top-color:#ff000' data-bus_id='{$info['bus_id']}'><td colspan='6' class='show-seat' id='show-seat_{$info['bus_id']}' style='display:block; padding:0px'></td></tr>";
				++$n;
			}
		?>
	</tbody>
</table>
	<div class='hide' id='pickedseat'></div>
</div>
<script type="text/javascript" src="javascript/pickbus.js"></script>
<?php printFooter(); ?>