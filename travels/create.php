<?php 
session_start();
require_once("fns.php");
require_once('../terminal/includes/fns.php');

if (isset($_POST['submit'])) {
	$uname = $_POST['username'];
	$pswd  = md5($_POST['password']);
	
	$result = $DB_CONNECTION->query("SELECT username FROM travels_account WHERE username = '$uname'");
	if (($result->num_rows) > 0) {
		$msg = "This username is not available, choose something else";
	} else {
		$DB_CONNECTION->query("INSERT INTO travels (company_name) VALUES ('{$_POST['travel_name']}')");
		$travel_id = $DB_CONNECTION->insert_id;
		$salt = base64_encode(mcrypt_create_iv(24, MCRYPT_DEV_URANDOM));
		$pswd = hash('sha256', $_POST['password'] . $salt);
		$DB_CONNECTION->query("INSERT INTO travels_account (username, password, salt, travel_id, date_created)
							  VALUES ('{$_POST['username']}', '$pswd', '$salt', '$travel_id', NOW())");
		echo "Account created";
	}
	
} else {
	docType();
	printBanner();
	?>
	<?php echo isset($msg) ? "<div class='msg'>$msg</div>" : '';?>
<div style="width:500px; border:#ccc solid thin; margin:auto; margin-top:30px; padding:5px; box-shadow:0px 1px 5px 0px #ccc; border-radius:5px">
	<div class="small_head">&nbsp;Create Company Account<br /></div>
	<hr style='margin-top:8px' />

	<form action="" method="post" class="form-horizontal"><br />
		
		<div class="control-group">
			<label class='control-label'>Company name</label>
			<div class='controls'>
				<input type='text' name='travel_name' placeholder='Company name' />
			</div>
		</div>
		
		<div class="control-group">
			<label class='control-label'>Username</label>
			<div class='controls'>
				<input type='text' name='username' placeholder='Username' />
			</div>
		</div>
		
		<div class="control-group">
			<label class='control-label'>Password</label>
			<div class='controls'>
				<input type='password' name='password' placeholder='Password' />
			</div>
		</div>
		
		<div class="control-group">
			<label class='control-label'>Verufy password</label>
			<div class='controls'>
				<input type='password' name='verify_password' placeholder='Verify password' />
			</div>
		</div>
		
		<div class="controls">
			<input type='submit' name='submit' value=' Create account ' class="btn btn-info" /> &nbsp; &nbsp;
			<button type='reset' style='font-size:14px' class="btn">Reset</button><br /><br />
		</div>
		
	</form>
</div>	
	<?php
}
?>