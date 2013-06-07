<?php
require_once("includes/header.html");

$payment_opt   = "Bank payment"; //$_GET['payment_opt'];
$customer_name = $_GET['c_name'];
$ticket_no     = $_GET['ticket_no'];

if (!isset($_GET['payment_opt'])) exit;

if ($payment_opt == 'Bank payment') {
	$msg = "<p><b>Thank you {$customer_name}.</b></p>Your ticket number is {$ticket_no}.<br />
	We will send your mTicket to your mobile phone once your bank payment is confirmed.<br />
	You can also use your ticket number to SMS our mTicket to your mobile number.";
}
?>

<div data-role="page" style="font:11px Verdana; line-height:16px;">
	<div data-role="header">
		<h1>Payment</h1>
		<a href="index.php" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
	</div>
	
	<div data-role="content">
		<?php echo $msg; ?>
	</div>
</div>
</body>
</html>