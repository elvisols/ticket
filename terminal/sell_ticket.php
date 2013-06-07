<?php
session_start();
require_once("includes/fns.php");

if (!isset($_SESSION['worker'])) header("Location: index.php");

docType();
printBanner();
?>
<script type="text/javascript" src="js/sell_ticket.js"></script>
<link rel="stylesheet" type="text/css" href='../css/seats.css' />
<style>
#main_bus_search {
	float:left;
	width:400px;
	position:relative;
	top:-50px;
	padding:30px;
}

#details {float:right; width:300px; display:none; border-left:#e8e8e8 solid thin; padding:15px}
#pick_seat {clear:left; position:relative; top:-80px; margin-left:30px}
#bus_details {display:none}
iframe#receipt {clear:both; width:280px; display:none; height:300px; border:#ccc solid; }
label {font: bold 11px Verdana}
nput.small {padding:1px;}
select {paddng:0px; heght:23px; width:173px}
.close-image {display:none}
</style>
<script type="text/javascript">
$(document).ready(function() {
	var calendar = new dhtmlXCalendarObject(["t_date"]);
});

/*** Prevent backspace button from navigating the page backwards ***/
$(function(){
    /*
     * this swallows backspace keys on any non-input element.
     * stops backspace -> back
     */
    var rx = /INPUT|SELECT|TEXTAREA/i;

    $(document).bind("keydown keypress", function(e){
        if( e.which == 8 ){ // 8 == backspace
            if(!rx.test(e.target.tagName) || e.target.disabled || e.target.readOnly ){
                e.preventDefault();
            }
        }
    });
});
</script>

<script src="javascript/indejs" type="text/javascript"></script>
<script src="../javascript/jquery.autocomplete.pack.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../css/jquery.autocomplete.css" media="all" />

    <div id='content'>
		
        <div id='main_bus_search'>
        	<!--<span class='head' style="position:relative; top:-20px; font-size:20px">Sell Ticket</span><hr style='margin-top:-12px' />-->
			<div class="alert alert-error hide">
			</div>
            <form action="" method="get" id="book">
				<div style='float:left; width:200px;'>
					<label>Select Bus Type</label>
					<select name="num_of_seats">
						<option value="">-- Select bus type --</option>
						<option value="14">Small bus</option>
						<option value="11">11 Seaters</option>
						<option value="59">Luxury bus</option>
					</select>
				</div>
				
				<div style='float:left; width:200px;'>
					<label>Pick Bus</label>
					<select name="bus">
						<option value="">-- Auto select --</option>
						<option value="1">First bus</option>
						<option value="2">Second bus</option>
						<option value="3">Third bus</option>
						<option value="4">Fourth bus</option>
						<option value="5">Fifth bus</option>
						<option value="6">Sixth bus</option>
						<option value="7">Seventh bus</option>
						<option value="8">Eight bus</option>
						<option value="9">Ninth bus</option>
						<option value="10">Tenth bus</option>
					</select>
				</div>
						
            	<div style='float:left; width:200px;'><label>From</label>
    			<input type="text" id="origin" name="origin" value="Lagos" style='width:160px' class="small" /></div>
                    
				<div style='float:left; width:200px'><label>To</label>
				<input type="text" id="destination" name="destination" style='width:160px' class="small" /><br />
				</div>
				
				<p id="alert-show"><div id='loading_bus_details' class='alert hide'></div></p>
				
				<p style='clear:both; position:relative; top:10px'>
					<label>Date of travel</label>
					<input name="travel_date" id="t_date" type="text" value="<?php echo date('Y-m-d'); ?>" style='width:160px' class="small" />
					<button type="submit" name="search" type="submit" style="margin-top:-10px" class="btn btn-primary btn-small"><i class="icon-th-list icon-white"></i> Show seats</button>
					<button type='button' style="margin-top:-10px" id='reset-form' class='btn btninverse btn-small'><i class="icon-remove"></i> Cancel</button>
				</p>
            </form>
        </div>
		
		<div id="details">
			<form method="post" id="customer_info" class="form-horizontal">
				<p><input type="text" name="customer_name" placeholder="Customer's name" /></p>
				<p><input type="text" name="address" placeholder="Customer's address" /></p>
				<p><input type="text" name="next_of_kin_phone" placeholder="Next of kin's phone number" /></p>
				<p><button type='submit' class='btn btn-primary'><i class='icon-print icon-white'></i> Print ticket</button></p>
				<input type="reset" class="hide" />
				<div class="alert hide alert-error">Please fill in all the required details</div>
			</form>
		</div>
		
		<div id="pick_seat">
		</div>
		
		<iframe id='receipt' name='receipt' src='<?php echo BASE_URL; ?>receipt.htm'></iframe>
		
    </div>