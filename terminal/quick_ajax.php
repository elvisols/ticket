<?php
require_once("../includes/DB_CONNECT.php");
$DB_CONNECTION = db_connect();

$data = json_decode($_POST['posted_data']);

$stmt = $DB_CONNECTION->prepare("UPDATE booking_details SET c_name = ?, next_of_kin_phone = ?, seat_no = ? WHERE id = ?");
$stmt->bind_param('ssss', $c_name, $next_of_kin_phone, $seat_no, $id);

for ($i = 0; $i < count($data); $i++) {
	$c_name = $data[$i]->c_name;
	$next_of_kin_phone = $data[$i]->next_of_kin;
	$seat_no = $data[$i]->seat_no;
	$id = $data[$i]->id;
	$stmt->execute();
}

?>