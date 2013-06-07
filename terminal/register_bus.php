<?php
require_once("includes/fns.php");
?>

<div id="register_bus" style='display: block'>

			<div class="control-group">
				<font color="red" size="-1">Oops! Above Bus Number UNAVAILABLE!</font> <font size="-1"><b>Register it below:</b></font>
			</div>
				<!--8310427-->
				<?php
					/*** Get route-codes active for this travel ***/
					$result = $DB_CONNECTION->query("SELECT route_code FROM travels WHERE id = '1'");
					$route = $result->fetch_assoc();
					$routes = explode(" ", $route['route_code']);
					
					echo "<div class='control-group'><label class='control-label'>Going to...[ tomorrow ]</label>
						<div class='controls'><select name='route_code'>\n";
						echo getDestination();
				?>
				</select></div></div>
		
			<div class="control-group">
				<label class="control-label">Bus Name:</label>
				<div class="controls"><input type="text" name="bus_name" /></div>
			</div>
			
			<div class="control-group">
				<label class="control-label">Bus Seaters:</label>
				<div class="controls"><input type="text" name="num_of_seats" /></div>
			</div>
					
			<div class="control-group">
				<label class="control-label">Driver's Name:</label>
				<div class="controls"><input type="text" name="driver_name" /></div>
			</div>
			
			<div class="control-group">
				<label class="control-label">Driver's Phone Number:</label>
				<div class="controls"><input type="text" name="driver_phone_no" /></div>
			</div>
</div>
<div class="control-group" id="book_available_bus" style='display: block'>
				<!--8310427-->
				<?php
					/*** Get route-codes active for this travel ***/
					$result = $DB_CONNECTION->query("SELECT route_code FROM travels WHERE id = '1'");
					$route = $result->fetch_assoc();
					$routes = explode(" ", $route['route_code']);
					
					echo "<div class='control-group'><label class='control-label'>Going to...[ tomorrow ]</label>
						<div class='controls'><select name='route_code'>\n";
						echo getDestination();
				?>
				</select></div></div>
					
</div>
