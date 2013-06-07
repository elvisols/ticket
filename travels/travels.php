<?php
session_start();
require_once('fns.php');
require_once('../terminal/includes/fns.php');

docType();
print_banner();
?>
<style>
._menu {float:left; width:45%}
th, td {font: 11px verdana}
#details {float:right; width:230px; border:#ccc solid thin; padding:9px; font:11px Verdana; line-height:19px; background-color:#f8f8f8}
</style>
<script>
$(document).ready(function() {
	/*** Get today's report and display on page load ***/
		currentDate = (new Date().getFullYear() + '-' + (new Date().getMonth() + 1) + '-' + (Number(new Date().getDate()))).toString();
		getDailyReport(currentDate);
	
	var calendar = new dhtmlXCalendarObject(["t_date"]);
	
	/*** Get tabular report ***/
		$('#view').click(function() {
			getDailyReport($('#t_date').val());
		});
});

function getDailyReport(date) { 
	$.post('ajax.php', {'op':'travels_report', 'date':date}, function(d) {
		$('#tbody').css('display', 'none').html(d).slideDown();
	});
}
</script>
<div id='content'>
	<div class="_menu">
		<input type="text" value="<?php echo date('Y-m-d'); ?>" class="input-medium" id="t_date" />
		<button class='btn btn-primary' id="view" style='margin-top:-9px'>Display</button>
	</div>
	
	<div class="_menu" style='text-align:right'>
		<a href=''>Report</a>&nbsp; .&nbsp;
		<a href='route_fare.php'>Add route/Edit fare</a>&nbsp; .&nbsp;
		<a href=''>Add bus</a>
	</div>
	<table class='table table-striped table-bordered' stle='width:65%; float:left'>
		<thead>
			<tr>
				<th>S/no</th>
				<th>Bus No</th>
				<th>Route</th>
				<th>Bus Type</th>
				<th>Tickets sold</th>
				<th>Offline</th>
				<th>Online</th>
				<th>Fare</th>
				<th>Income</th>
				<th>Driver's allowance</th>
				<th>Service charge</th>
				<th>Balance</th>
			</tr>
		</thead>
		<tbody id="tbody">
		
		</tbody>
	</table>
	
	<!--<div id='details'>Summary</div>-->
</div>