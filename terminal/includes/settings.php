<?php
// Determine if the software is installed, and who the hell runs it
if(!isset($DB_CONNECTION)) $DB_CONNECTION = db_connect();

$sql = "SELECT company_name, t.id AS travel_id, terminal_name, state_id, st.name AS state_name FROM terminal AS tn
		JOIN travels AS t ON tn.travel_id = t.id
		JOIN states_towns AS st ON tn.state_id = st.id";
$result = $DB_CONNECTION->query($sql);
extract($result->fetch_assoc());

define ("TRAVEL_ID",     $travel_id);
define ("TRAVEL_NAME",   $company_name);
define ("TERMINAL_NAME", $terminal_name);
define ("SOURCE_ID",     $state_id);
define ("SOURCE",        $state_name);
?>