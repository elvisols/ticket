<?php
 
//check if this file is being accessed directly
if (stristr(htmlentities($_SERVER['PHP_SELF']), "DB_CONNECT.php")) {
	Header("Location: ../index.php");
    die();
}

function db_connect(){
	return $DB_CONNECTION = new mysqli("localhost", "elvis", "solsadmin", "ticket");
}
?>