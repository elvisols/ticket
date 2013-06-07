<?php 
session_start();
require_once("includes/fns.php");

if (isset($_POST['submit'])) {
	$uname = $_POST['uname'];
	$pswd  = md5($_POST['pswd']);
	
	$result = $DB_CONNECTION->query("SELECT username FROM workers WHERE username = '$uname'");
	if (($result->num_rows) > 0) {
		$_SESSION['admin'] = true;
		$_SESSION['uname'] = $uname;
		header("Location: terminal/index.php");
		exit;
	} else {
		$pswd = md5($_POST['pswd']);
		$DB_CONNECTION->query("INSERT INTO workers (fullname, username, password, date_created) VALUES ('{$_POST['fullname']}', '{$_POST['uname']}', '$pswd', NOW())");
		echo "Account created";
	}
	
} else {
	docType("Admin Page");
	//printBanner();
	?>
	<div id='left' style='margin-top:20px; width:94%; min-height:480px'>
		<br /><br />
		<div style='margin-top:70px; border:#ccc solid thin; margin-left:auto; margin-right:auto; padding:0px 30px; width:470px'>
			<div class="head" style="padding:5px">Create Account</div><hr style="margin:2px" /><br />
			<div style="text-align:center">
				<form action="" method="post">
					<p>Full name&nbsp;&nbsp;<input type="text" name="fullname" class="text" /></p>
					<p>Username&nbsp;&nbsp;<input type="text" name="uname" class="text" /></p>
					<p>Password&nbsp;&nbsp; <input type="password" name="pswd" class="text" /></p>
					<input type="submit" class="btn btn-primary" name="submit" value="  Create  " /><br /><br />
				</form>
			</div>
		</div>
	</div>
	<?php
}
?>