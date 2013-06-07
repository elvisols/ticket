<?php
echo 'I just modified here and one other place';
$useragent = $_SERVER['HTTP_USER_AGENT'];
if (preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
    header('Location: http://oya.com.ng/mobile/');

require_once("includes/general_functions.php");

docType();
printBanner();
?>
<style>
#main_bus_search {
	width:450px;
	display:inline-block;
	padding-left:20px;
}

#main_info {
	width:365px;
	float:right;
	position:relative;
	right:50px;
	top:20px;
	height:365px;
}

#advert {
	float:left;
	width: 321px;
	margin-left:20px;
	margin-top:50px;
}

#contact {clear:both; float:left; position:relative; top:-110px; width:350px; font-size:18px;}
</style>

<script src="javascript/index.js" type="text/javascript"></script>
<script src="javascript/jquery.autocomplete.pack.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" media="all" />
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=512451545459816";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="modal fade hide">
	
</div>

    <div id='content'>
    	<div id='main_info'>
			<div id="slider">
					<img src="images/mobile-app.jpg" alt="aaaaa" />
						<!--<span>
							Book bus ticket using your mobile phone
						</span>-->
			</div>
        </div> 
        
        <div id='main_bus_search'>
        	<p><span class='head'>Book Bus Tickets</span><br /><span class="small_head" style="font:normal 13px Tahoma;">[ No extra charge for online booking ]</span></p>
            <form action="pick_bus.php" method="get">
            	<div style='float:left; width:200px;'><label>From</label>
    			<input type="text" id="orgin" name="origin" value="Lagos" readonly="readonly" style='width:160px' /></div>
                    
				<div style='float:left; width:200px'><label>To</label>
				<input type="text" id="destination" name="destination" placeholder="Enter a city" style='width:160px' /><br />
						<span id='error'></span></div>
				
				<p style='clear:both; position:relative; top:25px'>
					<?php echo isset($_GET['msg']) ? "<span class='alert'>You must select travel date to continue.</span><br /><br />" : ''; ?>
					<label>Date of travel</label>
					<input name="travel_date" id="t_date" style='width:160px' placeholder="yyyy-mm-dd" type="text" />
					<button type="submit" name="search" style="margin-top:-10px" type="submit" class="btn btn-primary"><i class="icon-search icon-white"></i> Search</button>
				</p>
  			
            </form>
        </div>
		
		<div id='advert' class='hide'>
			<img src="images/cartoon-bus.png" />
		</div>
		<div>just modified here and one other place</div>
		<div id="contact">
			<div class="small_head"><img src="images/call.gif" /> For ticket and bus booking call</div><hr style="margin:6px" />
			<div style="width:80%; margin-left:30px; textalign:center; margin-top:10px">08181217561<span style="float:right">08068811429</span></div><br />
			<div style="margin-left:30px; textalign:center">08023276936</div><br />
			<p>
				<div class="fb-like" data-href="http://oya.com.ng" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false"></div>
				<a href="https://twitter.com/oyaNigeria" class="twitter-follow-button" data-show-count="true" data-lang="en">Follow @oyaNigeria</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</p>		
		</div>
    </div>
 	<?php printFooter(); ?>