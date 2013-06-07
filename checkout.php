<?php
session_start();
require_once("includes/general_functions.php");

docType();
printBanner();
$payment_opt   = "Bank payment"; //$_GET['payment_opt'];
$customer_name = $_GET['c_name'];
$ticket_no     = $_GET['ticket_no'];

if (!isset($_GET['payment_opt'])) exit;

if ($payment_opt == 'Bank payment') {
	$msg = "Thank you {$customer_name}. <br />Your ticket number is {$ticket_no}.<br />
	We will send your mTicket to your mobile phone once your bank payment is confirmed.<br />
	You can also use your ticket number to SMS our mTicket to your mobile number.";
} elseif ($payment_opt == 'Home delivery') {
	$msg = "Thank you {$customer_name}. <br />Your ticket will be delivered to the address you gave us, soonest.";
}

echo "<div id='content'>
	<div id='pane'>
		<div class='head'>Thank you</div><hr style='margin:6px 0px' /><br />
		<blockquote>
			<p>$msg</p>
		</blockquote>
	</div>	
</div>";

printFooter();
?>