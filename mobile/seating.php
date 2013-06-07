<?php
include_once("includes/header.html");
require_once("../ajax.php");

require_once("../includes/DB_CONNECT.php");
$DB_CONNECTION = db_connect();
?>

<div data-role="page" id="seating_arrangement">
	<div data-role="header">
		<h1>Seating arrangement</h1>
		<a href="index.php" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
	</div>

	<div data-role="content">
		<?php
			getSeats();
		?>
		
		<span id="data" data-bus_type_id="<?php echo $_GET['bus_type_id']; ?>" data-departure_time="<?php echo $_GET['departure_time']; ?>" />
		
		<div data-role="popup" id="seat-popup" class="ui-content" data-transition="flip" data-theme="e" data-overlay-theme="a">
			<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
			<p>You have not picked a seat yet...</p>
		</div>
	</div>
	
</div>

</body>
</html>