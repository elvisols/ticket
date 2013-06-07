<?php 
session_start();
require_once("includes/fns.php");

if (isset($_POST['submit'])) {
	$uname = $_POST['uname'];
	$pswd  = md5($_POST['pswd']);
	
	$result = $DB_CONNECTION->query("SELECT id FROM workers WHERE username = '$uname' AND password = '$pswd'");
	if ($result->num_rows > 0) {
		$_SESSION['worker'] = true;
		$_SESSION['uname'] = $uname;
		$staff = $result->fetch_assoc();
		$_SESSION['staff_id'] = $staff['id'];
		header("Location: sell_ticket.php");
		exit;
	} else echo "Please leave this place!";
	
} else {
	docType("Admin Page");
	//printBanner();
	?>
	<div id='left' style='margin-top:20px; width:94%; min-height:480px'>
		<br /><br />
		<div style='margin-top:70px; border:#ccc solid thin; margin-left:auto; margin-right:auto; padding:0px 30px; width:470px'>
			<div class="head" style="padding:5px">Login</div><hr style="margin:2px" /><br />
			<div style="text-align:center">
				<form action="" method="post">
					<p>Username&nbsp;&nbsp;<input type="text" name="uname" class="text" /></p>
					<p>Password&nbsp;&nbsp; <input type="password" name="pswd" class="text" /></p>
					<input type="submit" class="btn btn-primary" name="submit" value="  Login  " /><br /><br />
				</form>
				<a href="create.php">Create Account</a>
			</div>
		</div>
	</div>
	<?php
}
?>