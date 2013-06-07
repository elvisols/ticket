<?php
 
//check if this file isnt being accessed directly
if (stristr(htmlentities($_SERVER['PHP_SELF']), "config.php")) {
	Header("Location: index.php");
    die();
}

define('DATABASE_SERVER', 'localhost');
define('DATABASE_NAME', 'ticket');
define('DATABASE_USERNAME', 'elvis');
define('DATABASE_PASSWORD', 'solsadmin');
?>