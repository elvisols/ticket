<?php
session_start();
include_once('includes/general_functions.php');

docType();
printBanner();

echo "<div class='head' style='text-align:center'>Sign Up</div>";


function showRegForm() {	
?>
<script type="text/javascript">
function verifyPswd() {
	if(document.getElementById("pswd").value != document.getElementById("pswd1").value) {
		alert("Incorrect password");
		document.regForm.pswd1.value = "";
		document.regForm.pswd1.focus();
		return false;
	}
	return;
}
</script>
<?php $security = addSpamSecurity(); ?>
<style>
label {width:110px; display:block; float:left; text-align:right; margin-right:30px; padding-top:6px}
</style>
<br />
<div style='width:450px; border:dotted thin #cccccc; text-align:left; padding-left:20px; margin:auto'>
	<br />
	<form action="" name="regForm" method='post' onsubmit='validate()'>
		<label>Full Name</label><input type='text' name='name' value='<?php echo @$_POST['name']; ?>' size="35" class="text" />
		<br /><br />
		<label>Email</label><input type='text' name='email' value='<?php echo @$_POST['email']; ?>' size="35" class="text" />
		<br /><br />
		<label>Username</label><input type='text' name='username' value='<?php echo @$_POST['username']; ?>' size="35" class="text" />
		<br /><br />
		<label>Phone no</label><input type='text' name='phone' value='<?php echo @$_POST['phone']; ?>' size="35" class="text" />
		<br /><br />
		<label>Password</label><input type='password' name='pswd' id="pswd" size="35" class="text" />
		<br /><br />
		<label>Verify Password</label>
		<input type='password' name="pswd1" id="pswd1" class="text" size="35" />
		<br /><br />
		<span style="font-size:83%;color:red">&nbsp;[ <b>*</b> Please answer this question to prove that you are human ]</span><br/>
		<label><?php echo "{$security['first']} {$security['op']} {$security['second']}"; ?> </label>
		<input type="hidden" name='secu' value="<?php echo $security['secu']; ?>" />
		<input id="security_code" name="security_code" type="text" size="35" class="text" onfocus="verifyPswd()" /><br /><br /><br />
		<input class='button' type='submit' name='submit' value='Sign up' style='margin-left:200px' /> &nbsp; &nbsp; &nbsp; 
		<input class='button' type='reset' value='Reset' style='font-size:14px' />
	</form><br /><br />
</div>
				
<?php
} ///:~
$hashstr;
$bln_show_form = false;
if (!isset($_POST['submit'])) {
	showRegForm();
	printFooter();
} else {
	if (strlen($_POST['username']) <= 25 && strlen($_POST['pswd']) <= 25 && strlen($_POST['email']) <= 50)
		if (validate_email($_POST['email']) && validate($_POST['username'])) {
			if (!empty($_POST['name'])) {
				$name = $_POST['name'];
				$username = filter(strtolower($_POST['username']));
				$email = filter($_POST['email']);
				if ((int)$_POST['security_code'] === 6) {
					$query = "SELECT user_id FROM users WHERE email = '$email' OR userName = '$username'";
					$stmt = $dbh->query($query);
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
					if ($result !== false) {
						$msg = "The username {$username} or email address {$email} already exist, use something unique.";
						$bln_show_form = true;
					} else {
						$phone = filter($_POST['phone']);
						$pswd = md5(filter($_POST['pswd']));
						$hashstr = "I will add this string in case your email is too short.";
						$hash = md5($email.$hashstr);
						$query = "INSERT INTO users (username, email, password, phone_no, hash, status, remote_addr, name, date_joined) 
							VALUES ('$username', '$email', '$pswd', '$phone', '$hash', '0', '{$_SERVER['REMOTE_ADDR']}', '$name', NOW())";
						$dbh->exec($query);
						
						/*$encoded_email = urlencode($email);
						$mail = "Thank you for registering with Foster vila. Click the link below to confirm and activate your membership.\n"
								."<?php echo BASE_URL; ?>activate_membership.php?hash=$hash&email=$encoded_email";
						$_msg = "Congratulations! An email has been sent to you to confirm your registration.<br /> Please check your spam folder if it's not in your inbox folder.";		
						sendMail($username, $email, 'Foster vila Registration Confirmation', $mail, $_msg);	*/
					}		
				} else {
					$msg = "Your answer to the security question is incorrect!";
					$bln_show_form = true;
				}
			} else {
				$msg = "You must select the account type to create";
				$bln_show_form = true;
			}
		} else {
			$msg = "Invalid email address or username";
			$bln_show_form = true;
		} 
	else {
		$msg = "Username and password must not exceed 25 characters and email must be less than 50 characters";
		$bln_show_form = true;
	}
	echo isset($msg) ? "<div class='msg'>$msg</div>" : '';
	if ($bln_show_form) {
		showRegForm();
		printFooter();
	}	
}

function validate($input) {
	if (isset($input)) {
		if (strlen($input) <= 3 || strlen($input) > 25) {
			#	Your username and password must not be less than three(3) and greater than twenty five(25)
			return false;
		} else {
			return true;
		}	
	} else
		return false;
}

?>	