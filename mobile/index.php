<?php require_once("includes/header.html"); ?>

<div data-role="page" id="home-page">
	<div data-role="header">
		<h1>Pick your route</h1>
		<a href="index.php" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
	</div>

	<div data-role="content">
		<div style="width:65%; margin:auto; text-align:center"><img src="images/logo.gif" /></div>
		<form action="pick_bus.php" method="get">
			<label for="origin"> &nbsp;From</label>
			<select type="text" name="origin" id="origin">
				<option value="Lagos">Lagos</option>
			</select>
			
			<label for="destination"> &nbsp;To</label>
			<select name="destination" id="destination">
				<option value="">Pick a city</option>
				<?php
					require_once("../includes/DB_CONNECT.php");
					$DB_CONNECTION = db_connect();
					
					$result = $DB_CONNECTION->query("SELECT route_code FROM travels WHERE id = '1'");
	
					$route = $result->fetch_assoc();
					$routes = explode(" ", $route['route_code']);
					array_pop($routes); // Remove the extra space
					
					/*** Get the route maps for the route-codes, extract the destination and display ***/
					foreach ($routes AS $route) {
						$result = $DB_CONNECTION->query("SELECT route, id from routes WHERE route_code = '$route'");
						$route_map = $result->fetch_assoc();
						$destination = substr($route_map['route'], strlen("Lagos - "));
						if ($destination != "Lagos")
						echo "<option value='{$destination}'>{$destination}</option>";
					}
				?>
			</select>
			
			<label for="travel_date"> &nbsp;Travel date</label>
			<select name="travel_date">
			<?php
				$tomorrow      = date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
				$next_tomorrow = date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")+2, date("Y")));
				
				echo "<option value='{$tomorrow}'>{$tomorrow}</option>
					  <option value='{$next_tomorrow}'>{$next_tomorrow}</option>";
			?>
			</select>
			
			<p><br /><br /><input type="submit" name="submit" value="Find bus" data-icon="search" data-theme="a" /></p>
		</form>
	</div>
</div>

<link rel="stylesheet" type="text/css" href='../css/seats.css' />
<script type="text/javascript" src="js/seating.js"></script>
<script type="text/javascript" src="js/payment.js"></script>
<style>
#ticket-details, #customer-details {margin-top: 8px; line-height:16px; padding:5px; display:none}
#payment-opt {padding:5px;}
</style>

</body>
</html>