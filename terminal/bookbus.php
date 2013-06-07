<?php
session_start();
require_once("includes/fns.php");

docType();
printBanner();
?>
<style>
.side {float:left}
iframe#receipt {clear:both; width:280px; dsplay:none; height:300px; border:#ccc solid; }
</style>
<script type="text/javascript">
$(document).ready(function() {
	/*** Book bus ***/
	$('#book_bus').submit(function(e) {
		e.preventDefault();
		var bln_validate = true;
		var data = $(this).serialize();
		var route_code = $('select[name=route_code] option:selected').data('route_code');
		
		// Validate form inputs
		$.each($(this).serializeArray(), function(i, val) {
			if (val.value.length == 0) {
				$('.alert').fadeIn();
				$('[name=' + val.name + ']').focus();
				bln_validate = false;
				return false;
			}
		});
		
		if (bln_validate === false) return false;
		
		$.post('ajax.php', data + '&op=book_bus&route_code=' + route_code, function(d) { //alert(d);
			if (d.trim() == "done") {
				$('#b_side > .alert').removeClass('alert-error').addClass('alert-success')
				.html('Bus booking successful.').fadeIn('fast').fadeOut(5000);
				$('button[type=reset]').click();
			}	
			else
				$('.alert').removeClass('alert-success').addClass('alert-error')
				.html('<button type="button" class="close" data-dismiss="alert">X</button>' + d).fadeIn('fast');
		});
	});
	
	/*** Remove a booked bus ***/
	$('.remove-bus').click(function(e) {
		e.preventDefault();
		var bus_id              = $(this).data('bus_id');
		var seating_arrangement = $(this).data('seating_arrangement');
		var seat_status = '';
		if (seating_arrangement == 15 || seating_arrangement == 60) {
			--seating_arrangement;
			seat_status = "Not full";
		} else seat_status = "Full";
		if (confirm("Are you sure you want to remove this bus?")) {
			$.post('ajax.php', {'op':'remove_bus', 'bus_id':bus_id, 'seating_arrangement':seating_arrangement, 'seat_status':seat_status}, function(d) { 
				location.href = location.href;
			});
		}
	});
	
	/*** Fast print: Print all the tickets for a booked bus ***/
	$('.print-tickets').click(function() {
		var bb_id = $(this).attr('id');
		var booked_id = $(this).data('booked_id');
		var $this = $(this);
		$.get('quick_print.php', {'bb_id':bb_id, 'booked_id':booked_id}, function(d) {
			$('#d_side > .alert-success').fadeIn().fadeOut(9000);
			/*** Print ticket ***/
			$this.fadeOut();
		});
	});
	
	var calendar = new dhtmlXCalendarObject(["t_date", "tdate"]);
});
</script>
<div class='container'>
	<div class="small_head">Book Bus For Tomorrow
	<hr />
	<div class='side' id="b_side" style='width:380px;'>
		<div class="alert alert-error hide">
			<button type="button" class="close" data-dismiss="alert">X</button>
			Form not completely filled out.
		</div>
		<form action="" method="get" id="book_bus" class='form-horizontal'>
			<div class="control-group">
				<label class="control-label">Select Bus Type</label>
				<div class="controls"><select name="num_of_seats">
					<option value="">-- Select bus type --</option>
				<?php
					/*** Auto select bus position onloading ***/
					//$sql = "SELECT id, num_of_seats, destination";
				
					$result = $DB_CONNECTION->query("SELECT name, number_of_seats FROM bus_types");
					while ($type = $result->fetch_assoc()) {
						echo "\t<option value='{$type['number_of_seats']}'>{$type['name']} - {$type['number_of_seats']} Seaters</option>\n";
					}
					echo "</select></div></div>";
					
					/*** Get route-codes active for this travel ***/
					$result = $DB_CONNECTION->query("SELECT route_code FROM travels WHERE id = '1'");
					$route = $result->fetch_assoc();
					$routes = explode(" ", $route['route_code']);
					
					echo "<div class='control-group'><label class='control-label'>Going to...[ tomorrow ]</label>
						<div class='controls'><select name='route_code'>\n
						\t<option value=''>-- Going to --</option>\n";
					
					echo getDestination();
				?>
			</select></div></div>
			<!--
			<div class="control-group">
				<label class="control-label">Choose Bus</label>
				<div class="controls">
					<select name="bus_order">
						<option value=''>-- Choose bus position --</option>
						<option value="1">First bus</option>
						<option value="2">Second bus</option>
						<option value="3">Third bus</option>
						<option value="4">Fourth bus</option>
						<option value="5">Fifth bus</option>
						<option value="6">Sixth bus</option>
						<option value="more">More</option>
					</select>
				</div>
			</div>
			-->
			<div class="control-group">
				<label class="control-label">Date of travel</label>
				<div class="controls"><input name="travel_date" id="t_date" type="text" value="<?php echo date('Y-m-d'); ?>" style='width:160px' /></div>
			</div>
			
			<div class="control-group">
				<label class="control-label">Bus Number</label>
				<div class="controls"><input type="text" name="bus_no" style='width:100px' /></div>
			</div>
					
			<div class="control-group">
				<label class="control-label">Drivers Name</label>
				<div class="controls"><input type="text" name="driver_name" /></div>
			</div>
			
			<div class="control-group">
				<label class="control-label">Drivers Phone Number</label>
				<div class="controls"><input type="text" name="phone_no" /></div>
			</div>
			
			<div class="control-group">
				<div class="controls"><button type="submit" class="btn btn-info">Submit</button>&nbsp; &nbsp;
				<button type="reset" class="btn btn-inverse">Cancel</button></div>
			</div>
		</form>
	</div>
	
	<div class='side' id="d_side" style='width:520px; float:right'>
		<form action="" method="post">
			<label style='float:left; display:inline'>Date of travel</label>
			<input name="travel_date" id="tdate" type="text" value="<?php echo @$_POST['travel_date']; ?>" style='width:110px; margin-top:-5px; margin-left:5px' />
			<input type="submit" class="btn btn-primary" name="submit" value="Show" style="margin-top:-14px" />
		</form>
		<div class="alert alert-success hide">Tickets successfully generated</div>
		<table class='table table-striped table-bordered' style="font: 11px Verdana">
			<thead>
				<tr>
					<th style="text-align:center; width:40px">Route</th>
					<th style="text-align:center">Bus type</th>
					<th style="text-align:center">Driver's name</th>
					<th style="text-align:center">Driver's no</th>
					<th style="text-align:center">Bus no</th>
					<th style="text-align:center">Status</th>
					<th style='text-align:center' colspan="3">Option</th>
				</tr>
			</thead>
			<tbody>
		<?php
			if (isset($_POST['submit'])) $date = $_POST['travel_date'];
			else $date = date('Y-m-d');
			$result = $DB_CONNECTION->query("SELECT bb.id, bb.no_of_seats, bb.drivers_name, bb.drivers_phone_no, bb.bus_no, bb.seat_status, booked_id, route FROM booked_buses AS bb JOIN seat_booking AS sb ON bb.id = sb.bus_id JOIN routes ON bb.route_code = routes.route_code WHERE bb.travel_date = '$date'");
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					// Check if tickets has been printed
					$result1 = $DB_CONNECTION->query("SELECT seat_status FROM seat_booking WHERE booked_id = '{$row['booked_id']}'");
					$seat_status = $result1->fetch_object()->seat_status;
					echo "<tr><td>{$row['route']}</td>
							<td>{$row['no_of_seats']} seater</td>
							<td>{$row['drivers_name']}</td>
							<td>{$row['drivers_phone_no']}</td>
							<td>{$row['bus_no']}</td>
							<td>{$row['seat_status']}</td>
							<td style='width:15px'><a href='' class='remove-bus' data-seating_arrangement='{$row['no_of_seats']}' data-bus_id='{$row['id']}'><img src='../images/cross.png' /></a></td>";
							if ($seat_status == "Not full") {
								echo "<td style='text-align:center'><a href='#' title='Print tickets' class='print-tickets' id='{$row['id']}' data-booked_id='{$row['booked_id']}'>Tickets</a></td>";
							} else echo "<td></td>";
							echo "<td style='text-align:center'><a href='quick_manifest.php?id={$row['id']}' title='Print manifest' class='write-manifest'>Manifest</a></td>
						</tr>";
				}
			}
		?>
			</tbody>
		</table>
	</div>
</div>