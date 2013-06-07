<?php
session_start();
require_once("../includes/general_functions.php");
//require_once("fns.php");

docType();
printBanner();

// Add routes
if (isset($_POST['add'])) {
	foreach ($_POST['s_name'] AS $name) {
		if (!empty($name)) {
			$s_name = filter($name);
			$DB_CONNECTION->query("INSERT INTO states_towns (name) VALUES ('$s_name')");
		}
	}
}
?>
<script src="../javascript/jquery.autocomplete.pack.js" type="text/javascript"></script>
<script src="../javascript/bootstrap-tab.js" type="text/javascript"></script>
<script type="text/javascript" src="mapping.js"></script>
<link rel="stylesheet" type="text/css" href="../css/jquery.autocomplete.css" media="all" />

<style>
.left, .right {width:44%; float:left; margin:15px; border:#ccc solid thin; padding:10px}
#show > div {padding:8px; border:#ccc solid thin;}
#show > div > span {width:65%; float:right; margintop:-3px; border:#fff solid thin}
#show {width:75%; float:left}
.remove_state {float:right; padding-right:5px; font-family:Verdana}
#optpane {float:left; width:20%; border-right:#ccc solid thin; height:70px}
#contentpane {float:left; width:70%; border:#ccc solid thin}
</style>
<div id='content'>
	<div class="tabbable tabs-left">
		<ul class="nav nav-tabs" style="width:22%">
			<li><a href='#state' data-toggle="tab">Add State</a></li>
			<li class="active"><a href='#route' data-toggle="tab">Manage route</a></li>
			<li><a href='#bus' data-toggle="tab">Add bus</a></li>
		</ul>
		
		<div class="tab-content">
			<div class='tab-pane' id="state">
				<div class='head'>Add state/Town</div><hr style='margin-top:3px' />
				<form action='' method="post" class="form-horizontal">
					<p style="margin-top:20px">
					<div class="control-group">
						<label class="control-label">State</label>
						<div class="controls">	
							<input type="text" name="s_name[]" />
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">State</label>
						<div class="controls">	
							<input type="text" name="s_name[]" />
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">State</label>
						<div class="controls">	
							<input type="text" name="s_name[]" />
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">State</label>
						<div class="controls">	
							<input type="text" name="s_name[]" />
						</div>
					</div>
					</p>
					<div class="controls"><input type="submit" class="btn btn-primary" value=" Add State(s)" name="add" /> &nbsp;
					<input type="reset" class="btn" value="Clear" />
					</div>
				</form>
			</div>
			
			<div class='tab-pane active' id="route">
				<div class='head'>Mapping</div><hr style='margin-top:3px' />
				<p>Pick State<br /><input type="text" id="origin" name="origin" style="width:150px" />
				<input type="text" id="destination" name="destination" style="width:150px" />
				<input type="button" class="btn btn-small" style='margin-top:-10px' value="Add" id="add" />
				<p><div id="show"></div></p>
				<p style="clear:both"><input type="submit" value=" Done " class="btn btn-primary" id="done" /></p>
			</div>
			
			<div class='tab-pane' id="bus">
				<div class='head'>Add bus</div><hr style='margin-top:3px' />
				<form id="add_bus" method="post" class="form-horizontal">
					<div class="control-group">
						<label class="control-label">Travel</label>
						<div class="controls">
							<select name="travel">
					<?php
						$result = $DB_CONNECTION->query("SELECT id, company_name FROM travels");
						while($row = $result->fetch_assoc()) {
							echo "<option value='{$row['id']}'>{$row['company_name']}</option>\n";
						}
					?>
							</select>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Bus type</label>
						<div class="controls">
							<select name="bus_type">
								<option value="">-- Select bus type --</option>
							<?php
								/*** Auto select bus position onloading ***/
								//$sql = "SELECT id, num_of_seats, destination";
							
								$result = $DB_CONNECTION->query("SELECT name, number_of_seats FROM bus_types");
								while ($type = $result->fetch_assoc()) {
									echo "\t<option value='{$type['name']}'>{$type['name']}</option>\n";
								}
								echo "</select>";
							?>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Amenities</label>
						<div class="controls">
							<input type="text" name="amenities" />
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Route</label>
						<div class="controls">
							<select name="route">
								<?php
									$result = $DB_CONNECTION->query("SELECT route_code, route FROM routes");
									while($row = $result->fetch_assoc()) {
										echo "<option value='{$row['route_code']}'>{$row['route']}</option>\n";
									}
								?>
							</select>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Seats</label>
						<div class="controls">
							<input type="text" name="seats" />
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Departure time</label>
						<div class="controls">
							<input type="text" name="departure_time" />
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Fare</label>
						<div class="controls">
							<input type="text" name="fare" />
						</div>
					</div>
					
					<div class="controls">
						<input type="submit" name="bus" class="btn  btn-primary" value="Add Bus" /> &nbsp;
						<input type="reset" class="btn" value="Clear" />
					</div>
				</form>
			</div>
		</div>
	</div>
	
</div>