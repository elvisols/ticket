<?php
session_start();
require_once("includes/general_functions.php");
require_once("includes/booking_functions.php");

docType();
printBanner();
?>
<script src="javascript/pick_bus.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="css/seating_preference.css" media="all" />
    
<div id='content'>
	<div id="left_bus_options">
		<h3>Your Selection</h3>
		<table width="200" border="0" cellspacing="0" cellpadding="0">
			<tr><td>Ikorodu-Nsukka</td></tr>
			<tr><td>Ifesinachi</td></tr>
			<tr><td>09/11/2011</td></tr>
			<tr><td>9:30 PM</td></tr>
			<tr><td>Fare(N) 600</td></tr>
		</table>
	</div>
	<div id="select_bus">
		<h3>Seating Preference</h3>
		<form action="passenger_details.php">
		<table width="500" border="0" cellspacing="1" cellpadding="1">
			<tr>
				<td>Number of seats</td>
				<td><input name="number_of_seats" type="number" value="1" /></td>
			</tr>
			<tr>
				<td>Type of seat</td>
				<td><select name="seat_type" size="1">
				  <option>Any</option>
				  <option value="window">Window</option>
				  <option value="sidewalk">Sidewalk</option>
				</select></td>
			</tr>
			<tr>
				<td>Position</td>
				<td><select name="seat_poition" size="1">
				  <option>Any</option>
				  <option value="window">Front</option>
				  <option value="sidewalk">Middle</option>
				  <option value="sidewalk">Back</option>
				  <option value="sidewalk">Backrow</option>
				  <option value="sidewalk">Beside Driver</option>
				</select></td>
			</tr>
		</table>
		<div id="continue"><a href="javascript:history.go(-1)">Reselect bus</a> <input name="search" type="submit" value="Continue" class="button" /></div>
		</form>
	</div>
</div>
<?php printFooter(); ?>