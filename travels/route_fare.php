<?php
session_start();
require_once("../terminal/includes/fns.php");

if (!isset($_SESSION['worker'])) header("Location: index.php");

// Effect price modification
if (isset($_POST['route_id'])) {
	$sql = "UPDATE fares SET hiace_fare = '{$_POST['small_bus']}', luxury_fare = '{$_POST['luxury_bus']}', executive_fare = '{$_POST['executive_bus']}' 
			WHERE id = '{$_POST['route_id']}'";
	$result = $DB_CONNECTION->query($sql);
} elseif (!empty($_POST['route'])) {
	$route_code = getRouteCode(SOURCE_NAME, $_POST['route']);
	$route = SOURCE_NAME . " - " . $_POST['route'];
	
	# Check if the route exist, then use update the travel table
	$result = $DB_CONNECTION->query("SELECT id FROM routes WHERE route_code = '$route_code'");
	if ($result->num_rows < 1) {
		$DB_CONNECTION->query("INSERT INTO routes (route_code, route) VALUES ('$route_code', '$route')");
	}
	$route_code = $route_code . " ";
	$DB_CONNECTION->query("UPDATE travels SET route_code = CONCAT(route_code, '$route_code') WHERE id = '" . TRAVEL_ID . "'");
	$DB_CONNECTION->query("INSERT INTO fares (route_code, travel_id) VALUES ('$route_code', '" . TRAVEL_ID . "')");
}

docType();
printBanner();
?>
<style>
.float {padding:10px}
td {font:11px Verdana; color:#666}
</style>
<div id="content">
	<div class='float'>
		Add/Edit fare from here.
			
		<table class='table table-striped table-bordered'>
			<thead>
				<tr>
					<th>S/no</th>
					<th>Route</th>
					<th>Small Bus Fare</th>
					<th>Luxury Bus Fare</th>
					<th>Executive Bus fare</th>
					<th colspan="2" style='text-align:center'>Action</th>
				</tr>
			</thead>
			<tbody>
		<?php
			$sql = "SELECT f.*, r.route FROM fares AS f INNER JOIN routes AS r USING (route_code) WHERE travel_id = '" . TRAVEL_ID . "'";
			$result = $DB_CONNECTION->query($sql);
			$n = 1;
			while ($row = $result->fetch_assoc()) {
				echo "<tr><td>$n</td>
						<td>{$row['route']}</td>
						<td class='small'>NGN {$row['hiace_fare']}</td>
						<td>NGN {$row['luxury_fare']}</td>
						<td>NGN {$row['executive_fare']}</td>
						<td style='text-align:center' data-route='{$row['route']}' data-small_bus='{$row['hiace_fare']}' data-luxury_bus='{$row['luxury_fare']}' data-executive_bus='{$row['executive_fare']}'>
							<a href='#myModal' class='edit-route-info' data-toggle='modal' data-fare_id='{$row['id']}' title='Edit route info'>
							<img src='../images/pencil.png' /></a>
						</td>
						<td style='text-align:center'>
							<a href='' class='remove-route' data-fare_id='{$row['id']}' data-route_code='{$row['route_code']}' title='Remove route'>
								<img src='../images/cross.png' />
							</a>
						</td>
					</tr>";
				$n++;	
			}
		?>
			</tbody>
		</table>
		<div style="float:left; width:45%">
			<form action="" method="post">
				<select name="route">
					<option value="">-- Select new route --</option>
				<?php
					$result = $DB_CONNECTION->query("SELECT * FROM states_towns");
					while ($routes = $result->fetch_assoc()) {
						echo "<option value='{$routes['name']}'>{$routes['name']}</option>";
					}
				?>
				</select><br />
				<input type="submit" name="add_route" value=" Add New Route " class="btn btn-primary" />
			</form>
		</div>
		<!--<a href="sync.php" class="btn btn-primary btn-large" style="float:right">Sync Database</a>-->
		 
		<!-- Modal -->
		<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
			<h3 id="myModalLabel">Edit Route Details</h3>
		  </div>
		  <div class="modal-body">
			<div id='route' class='small_head'></div>
			<form method="post" action="">
				<br />
				<p>
					<div class="input-append">
						<div style='float:left; width:45%'>
							<label style='display:inline'>Small bus fare</label><br />
							<input type="text" name="small_bus" class="input-medium" placeholder='Small bus fare' /><span class="add-on">NGN</span>
						</div>
						<label style='margin-left:20px; display:inline'>Luxury bus fare</label><br />
						<input type="text" name="luxury_bus" class="input-medium" placeholder='Luxury bus fare' style='margin-left:20px' /><span class="add-on">NGN</span>
					
						<div style='float:left; width:45%'>
							<label style='display:inline'>Executive bus fare</label><br />
							<input type="text" name="executive_bus" class="input-medium" placeholder='Executive bus fare' /><span class="add-on">NGN</span>
						</div>
					</div>
					<input type="hidden" name="route_id" />
				</p>
		  </div>
		  <div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
			<button type="submit" class="btn btn-primary">Save changes</button>
		  </div>
		  </form>
		</div>
	</div>
</div>

<script type="text/javascript" src="../javascript/bootstrapmodal.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('.edit-route-info').click(function() {
		var id        = $(this).data('fare_id');
		var small     = $(this).parent('td').data('small_bus');
		var luxury    = $(this).parent('td').data('luxury_bus');
		var executive = $(this).parent('td').data('executive_bus');
		$('input[name=small_bus]').val(small);
		$('input[name=luxury_bus]').val(luxury);
		$('input[name=executive_bus]').val(executive);
		
		$('#route').text($(this).parent('td').data('route'));
		$('input[name=route_id]').val(id);
	});
	
	/*** Remove the selected route ***/
	$('.remove-route').click(function(e) {
		e.preventDefault();
		var fare_id  = $(this).data('fare_id');
		var route_code = $(this).data('route_code');
		if (confirm("Are you sure you want to remove this route?")) {
			$.post('ajax.php', {'op':'remove_route', 'fare_id':fare_id, 'route_code':route_code}, function(d) {
				location.href = 'backend.php';
			});
		} 
	});
});
</script>