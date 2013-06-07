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
	$("#loadingDiv").ajaxStart(function(){$(this).show();});$("#loadingDiv").ajaxStop(function(){$(this).hide();});
	$('#book_bus').submit(function(e) {
		e.preventDefault();
		var bln_validate = true;
		var data = $(this).serialize();
		var status = false;
		var route_code = $('select[name=route_code] option:selected').data('route_code');
		
		// Validate form inputs
		if ($('#bus_no').val().length == 0) {
			$('.alert').fadeIn();
			$('#bus_no').focus();
			return false;
		}
		
		if ($("#register_bus").css('display') == "block" || $("#book_available_bus").css('display') == "block")
		{
			$.each($(this).serializeArray(), function(i, val) {
				if (val.value.length == 0) {
					$('.alert').fadeIn();
					$('[name=' + val.name + ']').focus();
					bln_validate = false;
					return false;
				}
			});
			
			if (bln_validate === false) return false;
			
			$.post('ajax.php', data + '&op=book_bus&route_code=' + route_code, function(d) {
			if (d.trim() == "done") {
				$('#b_side > .alert').removeClass('alert-error').addClass('alert-success')
				.html('Bus booking successful.').fadeIn('fast').fadeOut(8000);
				$('button[type=reset]').click();
			} else if (d.trim() == "05") { // Invalid route fare
				var err = "This route's fare is invalid, please check it now";
				$('.alert').removeClass('alert-success').addClass('alert-error')
				.html('<button type="button" class="close" data-dismiss="alert">X</button>' + err).fadeIn('fast');
			}
			else
				$('.alert').removeClass('alert-success').addClass('alert-error').html('<button type="button" class="close" data-dismiss="alert">X</button>' + d).fadeIn('fast');
			});
			$("#register_bus_holder").empty();
			$("#register_bus_holder").css('display', 'none');
		}
		else
		{
			$.post('ajax.php', data + '&op=bus_no_verify', function(r){
			if (r == "true") {
				$("#register_bus_holder").load('register_bus.php #book_available_bus');//return false;
			}
			else {
				$("#register_bus_holder").load('register_bus.php #register_bus'); //return false;
			}
		});
		}
		
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
	
	var calendar = new dhtmlXCalendarObject(["t_date", "tdate", "travel_date"]);
	
	/*** Edit booked bus ***/
	$('#report').on('click', '.edit-bookedBus', function() {
		var bd_id = $(this).data('bd_id');
		var busInfo_id = $(this).data('bi_id');
		$.getJSON ('ajax.php', {'op':'get-booked-bus-details', 'bd_id':bd_id, 'bus_info_id':busInfo_id}, function(d) {
			$("#bus_no1").val(d.bus_no);
			$("#bus_order").val(d.departure_order);
			$("#travel_date").val(d.travel_date);
			switch (d.route_code)
			{
				case 'LagEnu': dest = 'Enugu'; break;
				case 'LagNsu': dest = 'Nsukka'; break;
				case 'LagNsu/Enu': dest = 'Nsukka/Enugu'; break;
				case 'LagKad': dest = 'Kaduna'; break;
				case 'LagOni': dest = 'Onitsha'; break;
				case 'LagAba/Owe': dest = 'Aba/Owerri'; break;
				case 'LagAbak': dest = 'Abakaliki'; break;
				case 'LagOni/Asa': dest = 'Onitsha/Asaba'; break;
				case 'LagOko': dest = 'Okoja'; break;
				case 'LagAna': dest = 'Anambra'; break;
				case 'LagAbu': dest = 'Abuja'; break;
				case 'LagEnu/Nin': dest = 'Enugu/Nineth mile'; break;
				case 'LagPor': dest = 'Porthacourt'; break;
				case 'LagAsa': dest = 'Asaba'; break;
				default: dest = 'Lagos';
			}
			$('select#routecode option:selected').text(dest);
			$("#bd_id").val(bd_id);
			$("#busInfo_id").val(busInfo_id);
			$("#bus_name").val(d.bus_name);
			$("#no_of_seats").val(d.num_of_seats);
			$("#drivers_name").val(d.driver_name);
			$("#drivers_phone_no").val(d.drivers_phone_no);
		});
	});
	
	$('#report').on('click', '.up-bus', function() {
		
		var rid = $(this).data('bus_id');
		var rcode = $(this).data('routecode');
		var d_order = $(this).data('departure_order');
		var date = $(this).data('date');
		if(d_order == 1){return false}
		else
		{
			$.post('ajax.php', {'op':'getUpId', 'row_id':rid, 'd_order':d_order, 'date':date, 'rcode':rcode},
			       function(d) {
				  $.post('ajax.php', {'op':'up-bus', 'row_id':rid, 'd_order':d_order, 'date':date, 'rcode':rcode, 'id': d}, 				         function(d) {
						//alert(d); return false;
						$("tbody#tb").html(d);
					});
								
			});
		}
	});
	
	
	/*** Save edited customer's information ***/
	$('#bookedBus_details').submit( function(e) {
		e.preventDefault();
		var bln_validate = true;
		var routeCode = $('select[name=routecode] option:selected').data('route_code');
		//var routeCode = $('select#routecode option:selected').text();
		//alert(routeCode); return
		// Validate form inputs
		$.each($(this).serializeArray(), function(i, val) {
			if (val.value.length == 0) {
				//alert(val.name)
				bln_validate = false;
				$('#bookedBusModal .alert').html("Form not completely filled out").fadeIn().fadeOut(10000);
				$("[name=' + val.name + ']").focus();
				return false;
			}
		});
		
		if (bln_validate === false) return false;
		
		$.post('ajax.php', $(this).serialize() + '&op=save_booked_bus_edited_info&routeCode='+routeCode, function(d) {
			$('#bookedBusModal .alert').addClass('alert-success').html("Operation successful...").fadeIn().fadeOut(9000);
		});
		
	});
	
	// Clean Database
	$('#clean-db').click(function(e) {
		e.preventDefault();
		$.get('util/clean_db.php', function(d) {
			alert(d);
		});
	});
});
</script>
<script src="../javascript/jquery.autocomplete.pack.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../css/jquery.autocomplete.css" media="all" />
<script type="text/javascript" src="js/fetch_registered_buses.js"></script>
<div class='container'>
	<div class="small_head">Book Bus For Tomorrow
	<hr />
	<div class='side' id="b_side" style='width:380px;'>
		<div class="alert alert-error hide">
			<button type="button" class="close" data-dismiss="alert">X</button>
			Form not completely filled out.
		</div>
		<form action="" method="post" id="book_bus" class='form-horizontal'>
			<div class="control-group">
				<label class="control-label">Enter Bus Number:</label>
				<div class="controls">
					<div style='float:left; width:250px'>
						<input type="text" id="bus_no" name="bus_no" placeholder="Bus Number" style='width:160px' />
						<span id='error'></span>
					</div>
				</div>
			</div>
									
			<div class="control-group">
				<label class="control-label">Date of travel:</label>
				<div class="controls"><input name="travel_date" id="t_date" type="text" value="<?php echo date('Y-m-d'); ?>" style='width:160px' /></div>
			</div>
		<div id="register_bus_holder"><div id="loadingDiv" style=" float: left; display: none; margin-left:40%"><img src="<?php echo BASE_URL;?>/images/ajax_loader.gif" alt="loading" width="20px" height="20px"></div></div>
			<div class="control-group">
				<div class="controls">
				<button type="submit" class="btn btn-info">Book</button>&nbsp; &nbsp;
				<button type="reset" onclick="javascript:location.href = location.href" class="btn btn-inverse">Cancel</button></div>
			</div>
		</form>
	</div>
	
	<div class='side' id="d_side" style='width:520px; float:right'>
		<form action="" method="post">
			<label style='float:left; display:inline'>Date of travel</label>
			<input name="travel_date" id="tdate" type="text" value="<?php echo @$_POST['travel_date']; ?>" style='width:110px; margin-top:-5px; margin-left:5px' />
			<input type="submit" class="btn btn-primary" name="submit" value="Show" style="margin-top:-14px;" />
			<!-- Clean table -->
			<button class="btn btn-danger btn-large" id="clean-db" style="float:right"><i class="icon-exclamation-sign icon-white"></i> Clean Database</button>
		</form>
		
		<div id="report" style='clear:both'>
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
						<th style='text-align:center' colspan="5">Option</th>
					</tr>
				</thead>
				<tbody id='tb'>
		<?php
			if (isset($_POST['submit'])) $date = $_POST['travel_date'];
			else $date = date('Y-m-d');
			$sql = "SELECT bb.id, bi.id AS busId, bb.departure_order, bi.num_of_seats, bi.driver_name, bi.drivers_phone_no, bi.bus_no,  bi.bus_name, bb.seat_status, booked_id, bb.route_code, route
			FROM booked_buses AS bb
			LEFT JOIN bus_info AS bi ON bb.bus_id = bi.id
			LEFT JOIN seat_booking AS sb ON bb.id = sb.bb_id
			JOIN routes ON bb.route_code = routes.route_code
			WHERE bb.travel_date = '$date' ORDER BY bb.route_code, bb.departure_order";
			$result = $DB_CONNECTION->query($sql);
			
			//$sql = "SELECT bb.id, bi.id AS busId, bb.departure_order, bi.num_of_seats, bi.driver_name, bi.drivers_phone_no, bi.bus_no, bi.bus_name, bb.seat_status, booked_id
			//FROM booked_buses AS bb
			//LEFT JOIN bus_info AS bi ON bb.bus_id = bi.id
			//
			//WHERE bb.travel_date = '$date' ORDER BY bb.route_code, bb.departure_order";
			//$result = $DB_CONNECTION->query($sql) or die (mysqli_error($DB_CONNECTION));
			//echo $result->num_rows;
			//die (var_dump($result->fetch_assoc()));
			 
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					// Check if tickets has been printed
					$ids[] = $row['id'];
					$result1 = $DB_CONNECTION->query("SELECT seat_status FROM seat_booking WHERE booked_id = '{$row['booked_id']}'");
					//$seat_status = $result1->fetch_object()->seat_status;
					//echo $row['driver_name'];
					echo "<tr id='{$row['id']}' data-departure_order='{$row['departure_order']}' data-routecode='{$row['route_code']}'><td>{$row['route']}</td>
							<td>{$row['num_of_seats']} seater</td>
							<td>{$row['driver_name']}</td>
							<td>{$row['drivers_phone_no']}</td>
							<td>{$row['bus_no']}</td>
							<td>{$row['seat_status']}</td>
							<td style='text-align:center; width:19px'><a href='#' title='Edit' data-target='#bookedBusModal' data-toggle='modal' data-bd_id='{$row['id']}' data-bi_id='{$row['busId']}' class='edit-bookedBus'><img src='../images/pencil.png' /></a></td>
							<td style='width:15px'><a href='' class='remove-bus' data-seating_arrangement='{$row['num_of_seats']}' data-bus_id='{$row['id']}' data-bi_id='{$row['busId']}' ><img src='../images/cross.png' /></a></td>
							<td style='text-align:center; width:19px'><a href='#' class='up-bus' data-bus_id='{$row['id']}' data-departure_order='{$row['departure_order']}' data-date=$date data-routecode='{$row['route_code']}' ><img src='../images/arrow_up.png' /></a></td></tr>";
							
							#if ($seat_status == "Not full") {
							#	//echo "<td style='text-align:center'><a href='#' title='Print tickets' class='print-tickets' id='{$row['id']}' data-booked_id='{$row['booked_id']}'>Tickets</a></td>";
							#} else echo "<td></td>";
							#echo "<td style='text-align:center'><a href='quick_manifest.php?id={$row['id']}' title='Print manifest' class='write-manifest'>Manifest</a></td>
					
				}
				
			}
		?>
			</tbody>
		</table>
		</div>
	</div>
</div>
	<!-- Edit customer details modal -->
	<div id="bookedBusModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
		<h3 id="myModalLabel">Edit Booked Bus Details</h3>
	</div>
		<div class="modal-body">
			<div class="alert hide"></div>
			<form method="post" action="" id='bookedBus_details' class='form-horizontal'>
				<p>
				<div class="control-group">
					<label class="control-label" for="bus_no">Bus's number:</label>
					<div class="controls">
						<input type="text" name="bus_no1" id="bus_no1" />
					</div>
				</div>
										
				<div class="control-group">
					<label class="control-label" for="bus_name">Bus's name:</label>
					<div class="controls">
						<input type="text" name="bus_name" id="bus_name" />
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="no_of_seats">Number of Seats:</label>
					<div class="controls">
						<input type="text" name="no_of_seats" id="no_of_seats" />
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="drivers_name">Driver's name:</label>
					<div class="controls">
						<input type="text" name="drivers_name" id="drivers_name" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="drivers_phone_no">Driver's Phone Number:</label>
					<div class="controls">
						<input type="text" name="drivers_phone_no" id="drivers_phone_no" />
					</div>
				</div>
				<!-- Get the Id's of booked_buses and bus_info table -->
				<input type="hidden" name="bd_id" id="bd_id" />
				<input type="hidden" name="busInfo_id" id="busInfo_id" />
				
				</p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
			<input type="submit" id='s' class="btn btn-primary" value="Save" />
		</div>
		</form>
	</div>
	
</div>
<script type="text/javascript" src="../javascript/bootstrapmodal.js"></script>
<script type="text/javascript">
