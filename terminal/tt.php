<?php
//require_once("../includes/DB_CONNECT.php");
//require_once("includes/fns.php");

$param = array(
	'username'   => 'Chibuzo',
	'first_name' => 'Henry',
	'School'     => 'UNN',
	'department' => 'Computer Science'
);

$param = array(
	array('name' => 'Uzo', 'Sex' => 'Male'),
	array('name' => 'Elvis', 'Sex' => 'Female')
);

foreach ($param AS &$row) {
	var_dump($row);
	//echo "<br />";
}
?> 