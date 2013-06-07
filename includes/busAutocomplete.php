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
	
} elseif ($_GET['op'] == 'bus_no') {
	
	$sql = "SELECT bus_no FROM bus_info WHERE bus_no LIKE '%$q%'";
	$result = $DB_CONNECTION->query($sql);
	while ($row = $result->fetch_array())
	{
		echo $row['bus_no']."\n";
	}
	
} elseif ($_GET['op'] == 'travel') {
	$sql = "SELECT id, company_name FROM travels WHERE company_name LIKE '%$q%'";
	$result = $DB_CONNECTION->query($sql);
	while ($row = $result->fetch_array()) {
		echo $row['company_name']."|%&*".$row['id']."\n";
	}
}
?>	