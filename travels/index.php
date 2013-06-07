<?php
session_start();
require_once('fns.php');
require_once('../terminal/includes/fns.php');

if (isset($_POST['username']) && isset($_POST['password'])) {
	$uname = filter($_POST['username']);
	$pswd  = filter($_POST['password']);
	
	$result = $DB_CONNECTION->query("SELECT salt FROM travels_account WHERE username = '$uname'");
	if ($result->num_rows > 0) {
		$salt = $result->fetch_object();
	} else {
		$msg = "Your username was not found in our database";
		return;
	}
	
	$pswd = hash('sha256', $pswd . $salt->salt);
	$result = $DB_CONNECTION->query("SELECT travel_id, username, tc.id FROM travels_account AS tc JOIN travels AS t ON t.id = travel_id WHERE username = '$uname' AND password = '$pswd'");
	if ($result->num_rows == 1) {
		$info = $result->fetch_object();
		$_SESSION['id']             = $info->id;
		$_SESSION['username']       = $info->username;
		//$_SESSION['oya_percentage'] = $info->oya_percentage;
		$_SESSION['travel_id']      = $info->travel_id;
		
		header("Location: travels.php");
	} else {
		$msg = "Invalid account";
	}
}
	

docType();
print_banner();
?>
<?php echo isset($msg) ? "<div class='msg'>$msg</div>" : '';?>
<div style="width:500px; border:#ccc solid thin; margin:auto; margin-top:30px; padding:5px; box-shadow:0px 1px 5px 0px #ccc; border-radius:5px">
	<div class="small_head">&nbsp;Company Login<br /></div>
	<hr style='margin-top:8px' />
	<img src="../images/security.png" style="float:left; margin-top:0px; margin-right:30px" />
	<form action="index.php" method="post"><br />
		
		<div class="control-group">
			<div class='controls'>
				<input type='text' name='username' placeholder='Username' />
			</div>
		</div>
		
		<div class="control-group">
			<div class='controls'>
				<input type='password' name='password' placeholder='Password' />
			</div>
		</div>
		
		<input type='submit' name='login' style='margin-left:67px; padding:6px 15px' value=' Login ' class="btn btn-info" /> &nbsp; &nbsp;
		<button type='reset' style='font-size:14px' class="btn">Reset</button><br /><br />
		
		<p><a href='' style="float:right; margin-right:123px">Forgot your password</a></p><br />
	</form>
</div>	


<?php
//printFooter();
?>