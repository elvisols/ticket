<?php
session_start();
require_once("includes/fns.php");

docType();
printBanner();
?>
<style>
#details {float:right; width:240px; border:#ccc solid thin; padding:9px; font:11px Verdana; line-height:19px; margin-top:15px; background-color:#f8f8f8; dislay:none}
#merged-routes-details {float:left; padding:9px; font:11px Verdana; wdth:240px; border:#ccc solid thin; background-color:#f8f8f8; margin-top:5px; display:none}
</style>

<script type="text/javascript">
	$(document).ready(function () {
		
		var calendar = new dhtmlXCalendarObject(["t_date"]);
		
		
		/*** Get chart for the selected date ***/
		$('#get-date').click(function() {
			var _date = $('#t_date').val();
			$('#details').html("<b>Routes to merge</b><hr style='margin:4px 0px' />")
			$.post('ajax.php', {'op':'get-chart', '_date':_date}, function(d) { //alert(d);
				$('#chart-body').html(d);
				$.get('ajax.php', {'_date':_date, 'op':'get-merged-routes'}, function(d) {
					$('#merged-routes-details').html(d).show();
				});
			});
		});
	
			
		/*** Select routes to merge ***/
		$('#chart-body').on('click', ':checked.merge', function() {
			if ($(this).is(':checked')) {
				var booked_id = $(this).val();
				var destination = $(this).data('destination');
				var bus_no = $(this).data('bus_no');
				var seating_arrangement = $(this).data('seating_arrangement');

				$('#details').append(destination + "<br />");
				$('#booked_ids').append(booked_id + '-');
				$('#bus_nos').append(bus_no + '-');
				$('#seating_arrangement').append(seating_arrangement + '-');
				$('#destination').append(destination + '-');
			}
			
		});
		
		
		/*** Merge the selected routes ***/
		$('#merge-routes').click(function() {
			var booked_ids          = $('#booked_ids').text();
			var bus_nos             = $('#bus_nos').text();
			var seating_arrangement = $('#seating_arrangement').text();
			var destination         = $('#destination').text();
			
			var booked = booked_ids.split('-');
			var bus = bus_nos.split('-');
			var seating = seating_arrangement.split('-');
			var destination = destination.split('-');
			booked.pop();
			bus.pop();
			seating.pop();
			destination.pop();
			
			
			var going_bus_no, going_booked_id, merge_booked_id, going_index = 0, merge_index = 0;
			$.each(booked, function(i, val) {
				//var max_seat = Math.max.apply(this, num_of_seats);
				
				if (bus[i] != 0) {
					going_bus_no = bus[i];
					going_booked_id = val;
					going_index = i;
				} else {
					merge_booked_id = val;
					merge_index = i;
				}
			});
			
			if (!going_bus_no) {
				alert("You must book a bus for either of the merging routes");
				return false;
			}
			
			$.post('ajax.php', {
				'op'				:'merge-route',
				'bus_no'		    : going_bus_no,
				'going_booked_id'   : going_booked_id,
				'merging_booked_id' : merge_booked_id,
				'seating_arrangement': seating[going_index],
				'going_route'       :destination[going_index],
				'merged_route'      : destination[merge_index] 
			}, function(d) {
				if (d.trim() == "done") alert("Merging operation successfull");
				else alert(d);
			});
		});
	});
</script>

	<div class='container'>
		<div style='margin-bottom:8px; float:left;'>
			<input type="text" id="t_date" value="<?php echo date('Y-m-d'); ?>" style='width:160px' />
		</div>
		<button type="button" class="btn btn-primary" id="get-date" style="margin-left:15px">Show chart</button>

		<!--<div class='small_head'>Today's bookings</div>-->
		<table class='table table-striped table-bordered' id="chart" style='font: 11px Verdana; clear:both; width:70%; float:left; margin-top:5px; margin-right:15px'>
			<thead>
				<tr>
					<th>Destination</th>
					<th>Bus type</th>
					<th>Bus order</th>
					<th stle='width:200px'>Seats booked</th>
					<th stle='width:200px'>Seats available</th>
					<th>Merge route</th>
				</tr>
			</thead>
			<tbody id="chart-body">
			</tbody>
			<tr><td colspan="6"><div id='merged-routes-details'></div></td></tr>
		</table>
		<div id="details"><b>Routes to merge</b><hr style='margin:4px 0px' /></div>
		
		
		<div id="booked_ids" style="display:none"></div>
		<div id="bus_nos" style="display:none"></div>
		<div id="destination" style="display:none"></div>
		<div id="seating_arrangement" style="display:none"></div>
		<div id="available_seats" style="display:none"></div>
		<button type='button' class="btn btn-primary" id="merge-routes" style='clear:both; float:right'>Merge routes</button>
	</div>

