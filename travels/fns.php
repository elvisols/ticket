<?php

require_once("../includes/DB_CONNECT.php");
if (!isset($DB_CONNECTION)) $DB_CONNECTION = db_connect();
require_once("../terminal/includes/settings.php");


function print_banner() {
?>
<body>
	<div id="modal-content"></div>
<header>	
	<!--- top menu starts here-->
	<div class="navbar navbar-fixed-top" >
		<div class='navbar-inner'>
			<div class='container'>
				<div id='top_menu'>
					<div class="pull-left">
						<span class="head" style="position:relative; top:15px">Ifesinachi Transport Limited</span>
					</div>
					
					<div class="pull-right">
						<ul class="nav">
							<!--<li><a href="sell_ticket.php">Report</a></li>-->
							<?php if (isset($_SESSION['uname'])) echo "<li><a href='logout.php'>[ Logout ]</a></li>"; ?>
						</ul>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</header>
<?php
}
?>