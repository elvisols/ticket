<?php
session_start();
require_once("general_func.inc");

$q = filter($_GET['q']);
if ($_GET['op'] == 'friends') {
	$sql = "SELECT friend FROM friends_table WHERE friend LIKE '%$q%' AND user_id = '{$_SESSION['user_id']}'";
	foreach ($sd_db->query($sql) AS $f) {
		echo $f['friend']."\n";
	}
} elseif ($_GET['op'] == 'networks') {
	$sql = "SELECT network_name FROM networks WHERE network_name LIKE '%$q%'";
	foreach ($sd_db->query($sql) AS $n) {
		echo $n['network_name']."\n";
	}
}
?>	