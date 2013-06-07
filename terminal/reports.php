<?php
session_start();
require_once("includes/fns.php");

if (!isset($_SESSION['worker'])) header("Location: index.php");

docType();
printBanner();
?>
<style>
td, th {font: 10px Verdana; color:#666;}
th {font-weight:bold}
#receipt {clear:both; width:280px; display:none; height:300px; border:#ccc solid; }
.audit_pane {width:20%; float:right; border:#ccc solid thin; font: 11px verdana; padding:8px; line-height:17px}
#ticket_search {clear:both; position:relative; top:10px}
</style>
<div id="content">
<div id='forms' style="margin-top:-30px">
<form>
	<div style='float:left;'>
		<label>Date of travel</label>
		<input name="travel_date" id="t_date" type="text" value="<?php echo date('Y-m-d'); ?>" style='width:160px' />
	</div>
	
	<div style='float:left; margin-left:20px'>
		<label>Choose destination</label>
		<select name="state_id">
			<option value="">-- Select Destination --</option>
			<?php echo getDestination(); ?>
		</select>
	</div>
	
	<div style='float:left; margin-left:20px'>
		<label>Select Bus</label>
		<select name='bus'>
			<option value="">-- Select bus --</option>
		</select>
	</div>
	<button type='button' class="btn btn-primary" id='view' style='float:left; margin-top:25px; margin-left:5px'>View</button>
</form>
<button type='button' class='btn btn-large btn-primary hide' id='print' style='margin-left:20px; margin-top:18px'><i class='icon-print icon-white'></i> Print Manifest</button>
</div>

<div id="ticket_search">
	<input type="text" id="ticket_no" placeholder="Enter ticket ref number" style='width:160px' /><br />
	<button class="btn" id="ticket-search"><i class="icon-search"></i> Search</button>
</div>

<div id="report" style='clear:both'>
</div>
	
	<iframe id='manifest' name='manifest' style='width:100%; display:none' src='manifest.htm'></iframe>
	<iframe id='receipt' name='receipt' src='../receipt.htm'></iframe>
	
	<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
			<h3 id="myModalLabel">Balance Sheet</h3>
		  </div>
		  <div class="modal-body">
			<div class="alert alert-success hide"></div>
			<form method="post" id='audit'>
				<p>
					<div class="input-append">
						<!--<div style='float:left; width:45%'>
							<label style='display:inline'>Insurance</label><br />
							<input type="text" name="audit" class="input-medium" placeholder='0.00' /><span class="add-on">NGN</span>
						</div>-->
						<label style='marginleft:20px; display:inline'>Driver's expenses, allowance/commision</label><br />
						<input type="text" name="drivers_expenses" class="input-medium" placeholder="0.00" /><span class="add-on">NGN</span>
						
						<div style='float:left; width:45%; margin-top:10px'>
							<label style='display:inline'>Load</label><br />
							<input type="text" name="load" class="input-medium" placeholder="0.00" /><span class="add-on">NGN</span>
						</div>					
						</div>
					<input type="hidden" name="booked_id" />
				</p>
		  </div>
		  <div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
			<button type="submit" class="btn btn-primary"> Save </button>
		  </div>
		  </form>
		</div>
	
	<!-- Free ticket modal -->
	<div id="myTicketModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
			<h3 id="myModalLabel">Give free ticket</h3>
		  </div>
		  <div class="modal-body">
			<div class="alert alert-success hide"></div>
			<form method="post" action="" id='free-ticket' class='form-horizontal'>
				<p>
					<div class="control-group">
						<label class="control-label" for="inputEmail">Give free ticket</label>
						<div class="controls">
							<select name='free_ticket'>
								
							</select>
						</div>
					</div>
					<input type="hidden" name="booking_details_id" />
				</p>
		  </div>
		  <div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
			<button type="submit" class="btn btn-primary"> Save </button>
		  </div>
		  </form>
		</div>
	
	<!-- Edit customer details modal -->
	<div id="customerModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
		<h3 id="myModalLabel">Edit customer details</h3>
	</div>
		<div class="modal-body">
			<div class="alert hide"></div>
			<form method="post" action="" id='customer_details' class='form-horizontal'>
				<p>
					<div class="control-group">
						<label class="control-label" for="c_name">Customer's name</label>
						<div class="controls">
							<input type="text" name="c_name" id="c_name" />
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label" for="next_of_kin">Next of kin number</label>
						<div class="controls">
							<input type="text" name="next_of_kin_no" id="next_of_kin" />
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label" for="address">Customer's address</label>
						<div class="controls">
							<input type="text" name="address" id="address" />
						</div>
					</div>
					
					<input type="hidden" name="bd_id" id="bd_id" />
				</p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
			<input type="submit" class="btn btn-primary" value="Save" />
		</div>
		</form>
	</div>
	
</div>
<script type="text/javascript" src="../javascript/bootstrapmodal.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	
	$('select[name=state_id]').change(function() {
		getBookedBuses($('#t_date').val());
	});
	
	/*** Get manifest ***/
	$('#view').click(function() {
		var bus_id    = $('select[name=bus] option:selected').val();
		if ($('select[name=bus] option:selected').text() == "") {
			alert("Choose a bus to continue...");
			return false;
		}
		
		var booked_id = $('select[name=bus] option:selected').data('booked_id');
		var state_id  = $('select[name=state_id] option:selected').val();
		
		if (booked_id == null) {
			alert("You must select a bus to continue");
			return false;
		}
		
		$("#ticket_search").hide();
	
		$.post('ajax.php', {'op':'reports', 'bus_id':bus_id, 'state_id':state_id, 'booked_id':booked_id}, function(d) {
			$('#report').html(d);
			$('button#print').data('bus_id', bus_id);
			$('button#print').data('booked_id', booked_id);
			$('button#print').data('state_id', state_id).show();
		});
		$("input[name='booked_id']").val(booked_id);
	});
	
	/*** Cancel sold ticket ***/
	$('#report').on('click', '.cancel-ticket', function() {
		var $this = $(this);
		var ticket_id = $this.attr('id');
		if (confirm("Are you sure you want to cancel this ticket?")) {
			$.post('../ajax.php', {'op':'cancel-ticket', 'ticket_id':ticket_id}, function() {
				$('tr#row_' + ticket_id).fadeOut('fast');
			});
		}
	});
	
	// Print manifest
	$('button#print').click(function() {
		var bus_id    = $(this).data('bus_id');
		var state_id  = $(this).data('state_id');
		var booked_id = $(this).data('booked_id');
		
		$.get('ajax.php', {'op':'print_manifest', 'bus_id':bus_id, 'state_id':state_id, 'booked_id':booked_id}, function(d) {
			
			/*** Print manifest ***/
			var iframe_body = $("#manifest").contents().find("body");
			iframe_body.html(d);
			window.frames['manifest'].print();
			iframe_body.html("");
		});
	});
	
	
	/*** Submit manifest balance sheet ***/
	$('.modal-body').on('submit', '#audit', function(e) {
		e.preventDefault();
		
		$.post('ajax.php', $(this).serialize() + '&op=balance-sheet', function(d) {
			$('.modal-body .alert-success').html("Action successful...").show().fadeOut(6000);
		});
	});
	
	// Reprint ticket
	$('#report').on('click', '.print-ticket', function() {
		var ticket_id = $(this).attr('id');
		$.post('ajax.php', {'ticket_id':ticket_id, 'op':'re_print_ticket'}, function(d) {
			
			/*** Print ticket ***/
			var iframe_body = $("#receipt").contents().find("#details");
			iframe_body.html(d);
			window.frames['receipt'].print();
			iframe_body.html("");
		});
	});
	
	// Get Free ticket amount
	$('#report').on('click', '.free-ticket', function() {
		var amt = $(this).text();
		var id = $(this).attr('id');
		if (!IsNumeric(amt)) {
			var route_code = $(this).data('route');
			var seating    = $(this).data('seating_arrangement');
			if (seating < 20) seating = 'hiace_fare';
			else seating = 'luxury_fare';
			$.get('ajax.php', {'op':'get-fare', 'route':route_code, 'seating':seating}, function(d) {
				$('select[name=free_ticket]').html("<option value='" + d + "'>" + d + "</option><option value='0'>Free</option>");
			});
		} else {
			$('select[name=free_ticket]').html("<option value='" + amt + "'>" + amt + "</option><option value='0'>Free</option>");
		}
		$("input[name='booking_details_id']").val(id);
	});
	
	
	// Submit form free ticket
	$('form#free-ticket').submit(function(e) {
		e.preventDefault();
		
		$.post('ajax.php', $(this).serialize() + '&op=make-free-ticket', function() {
			$('#myTicketModal .alert').html("Operation successful...").show().fadeOut(6000);
		});
	});
	
/*** Reopen the current bus ***/
	$('#report').on('click', '#reopen', function() {
		var booked_id = $(this).data('booked_id');
		$.post('ajax.php', {'op':'reopen-bus', 'booked_id':booked_id}, function(d) {
			if (d.trim() == "Done")
				alert("The bus has been reopen");
			else
				alert(d);
		});
	});
	
/*** Edit customer information ***/
	$('#report').on('click', '.edit-ticket', function() {
		var bd_id = $(this).data('bd_id');
		$.getJSON ('../ajax.php', {'op':'get-customer-details', 'bd_id':bd_id}, function(d) {
			$("#c_name").val(d.c_name);
			$("#next_of_kin").val(d.next_of_kin_phone);
			$("#address").val(d.address);
			$("#bd_id").val(bd_id);
		});
	});
	
	
/*** Save edited customer's information ***/
	$('#customer_details').on('submit', function(e) {
		e.preventDefault();
		var bln_validate = true;
		
		// Validate form inputs
		$.each($(this).serializeArray(), function(i, val) {
			if (val.value.length == 0) {
				alert(val.name)
				bln_validate = false;
				$('#customerModal .alert').html("Form not completely filled out").fadeIn().fadeOut(10000);
				$("[name=' + val.name + ']").focus();
				return false;
			}
		});
		
		if (bln_validate === false) return false;
		
		$.post('ajax.php', $(this).serialize() + '&op=save_customer_edited_info', function() {
			$('#customerModal .alert').addClass('alert-success').html("Operation successful...").fadeIn().fadeOut(9000);
		});
	});
	
	var calendar = new dhtmlXCalendarObject(["t_date"]);
});

function getBookedBuses(_date) {
	var route_code = $('select[name=state_id] option:selected').data('route_code'); 
	
	$.get('ajax.php', {'op':'get_booked_buses', 'date':_date, 'route_code':route_code}, function(d) {
		$('select[name=bus]').html(d);
	});
}

function IsNumeric(num) {
	var num_exp = "/^[0-9]+$/";
	if (num.match(num_exp)) {
		return true;
	}
}  
</script>