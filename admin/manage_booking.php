<?php
session_start();
require_once("../includes/general_functions.php");

docType();
printBanner();

// Check for authenticated user

if (isset($_GET['op'], $_GET['id'])) {
	$id = filter($_GET['id']);
	$op = urldecode($_GET['op']);
	$DB_CONNECTION->query("UPDATE online_booking SET payment_status = 'Paid' WHERE id = '$id'");
	if ($DB_CONNECTION->affected_rows == 1 && $_GET['op'] == 'Bank payment') {
		// Send sms
	} 
}

echo "<div id='content'><table class='table table-striped table-bordered'>
<thead>
<tr><th>Route</th><th>Customer's name</th><th>Delivery Address</th><th>Payment opt</th><th>Customer's Phone</th><th>Action</th>
</thead><tbody>";
$date = date('Y-m-d');
$sql = "SELECT ob.id, c_name, phone_no, address, payment_opt, route
		FROM online_booking AS ob JOIN booking_details AS bd ON ob.booking_details_id = bd.id
		WHERE ob.travel_date >= '$date'";
$result = $DB_CONNECTION->query($sql);
if ($result->num_rows > 0) {
	while ($row = $result->fetch_object()) {
		echo "<tr><td>{$row->route}</td>
				  <td>{$row->c_name}</td>
				  <td>{$row->address}</td>
				  <td>{$row->payment_opt}</td>
				  <td>{$row->phone_no}</td>
				  <td><a href='?op=" . urlencode($row->payment_opt) . "&id={$row->id}'>Activate</a></td>
			  </tr>";
	}
}
echo "</tbody></table></div>";
?>