<?php
require_once("includes/general_functions.php");

docType();
printBanner();

if (isset($_GET['cancel'])) {
	$ticket_no = filter($_GET['ticket_no']);
	$result = $DB_CONNECTION->query("SELECT id FROM booking_details WHERE ticket_no = '$ticket_no'");
	if ($result->num_rows == 1) {
		$id = $result->fetch_object();
		$_POST['ticket_id'] = $id->id;
		require_once("ajax.php");
		if ( cancelTicket() ) {
			$msg = "The ticket with ref number {$ticket_no} has been cancelled";
		} else {
			$msg = "This ticket couldn't be cancelled, please try again";
		}
	} else {
		$msg = "This ticket ref number is not valid.";
	}
	
	
}
?>
<div id="content">
	<div id='pane'>
		<div class='head'>Cancel Ticket</div><hr style='margin:6px 0px' /><br />
		<?php echo isset($msg) ? "<div class='alert'>$msg</div>" : ''; ?>
		<form action="" method="get">
			<div style='float:left; width:200px;'><label>Ticket Number</label>
			<input type="text" name="ticket_no" /></div>
			<p style="clear:both"><input type="submit" name="cancel" class="btn btn-info btn-large" value=" Cancel Ticket " /></p>
		</form>
	</div>
</div>

<?php printFooter(); ?>