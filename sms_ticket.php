<?php
session_start();
require_once("includes/general_functions.php");

docType();
printBanner();
?>
<style>
label {display:inline; position:relative; top:4px; margin-left:7px}
</style>
<div id="content">
	<div id='pane'>
		<div class='head'>SMS Ticket</div><hr style='margin:6px 0px' /><br />
		
		<div class='alert'>This service is not currently available</div>
		<form action="" method="get">
			<div style='width:200px;'><span>Ticket Number</span>
			<input type="text" id="origin" name="origin" style='width:160px' /></div>
			<!--<p><input type="radio" value="print" /> <label>Print ticket</label></p>
			<p><input type="radio" value="sms" /> <label>Get mTicket by SMS</label></p>-->
			<br />
			<p style="clear:both"><button type="button" class="btn btn-info btn-large">Submit</button></p>
		</form>
	</div>
</div>

<?php printFooter(); ?>