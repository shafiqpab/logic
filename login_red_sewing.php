<?
 error_reporting(0);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <!-- <LINK REL="SHORTCUT ICON" HREF="images/logic_logo.png"> -->
        <link rel="stylesheet" type="text/css" href="css/login_pg_red.css">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Logic ERP Solution</title>
    </head>
	<?php
   
 	session_start();
    include('includes/common.php');
    extract($_REQUEST);
 
	if($db_type==0) $pc_time = date("Y-m-d H:i:s",time()); 
	else $pc_time = date("d-M-Y h:i:s A",time()); 
	if($db_type==0) $pc_date = date("Y-m-d",time());
	else $pc_date = date("d-M-Y",time());
 	$_SESSION['logic_erp']["pc_local_ip"]=$_SERVER['REMOTE_ADDR'];
    ob_start();
	system('arp -a '.$_SERVER['REMOTE_ADDR']);
	$arp=ob_get_contents();
	ob_clean();
	$lines=explode("\n", $arp);
	
	#look for the output line describing our IP address
	foreach($lines as $line)
	{
	   $cols=preg_split('/\s+/', trim($line));
	   if ($cols[0]==$_SERVER['REMOTE_ADDR'])
	   {
		   $macAddr=$cols[1];
	   }
	}
	//echo  strtoupper($macAddr)."__".$ipAddress."__".$proxy_address; 
    //print_r($_SERVER);
    //echo load_html_head_contents_new_login_white_spin("Logic ERP Solution","", $filter, '', $unicode);
 	// echo decrypt("2dXj1tc="); die;
 ?>

    <script language="Javascript" type="text/javascript" src="js/jquery_easy_ip.js"></script>
    <script>
     
	function my_callback(json) {
		$.ajax({
		  url: "includes/common_functions_for_js.php?action=add_ip_session&ip_address="+json.IP,
		  async: false
		}).responseText 
    }



 </script>
</head>
<!--EasyjQuery_Get_IP("my_callback")-->
<body class="login_body">
    <input type="hidden" id="ipp" value="<? echo $_SESSION['logic_erp']["pc_local_ip"]; ?>" />
	<div class="container">
		
		<div class="login_form_block">
            <div class="logic_logo_block">
                <img src="images/logic/logic_logo_new.png" alt="Logic Software BD Ltd." class="logic_logo">
            </div>
            <div class="login_form">
                <form id="login_form" name="login_form" action="" method="">
                    <div class="user_block">
                        <div class="user_lable">
                            <label for="user_name" class="lable">User Name</label>
                        </div>
                        <div class="user_input">
                            <input type="text" class="user_name" id="user_name" name="user_name" /> 
                        </div>
                    </div>
                    <div class="password_block">
                        <div class="password_lable">
                            <label for="password" class="lable"> Password </label>
                        </div>
                        <div class="password_input">
                            <input type="password" class="password" id="password" name="password" /> 
                        </div>
                    </div>
                    <div class="button_block">
                        <div class="login_button">
                            <input type="submit" class="login" id="login" name="login"  value="Login"/>
                        </div>
                        <div class="forgot_link">
                            <a class="forgot">Forgot_password? <br/> Reset Password. </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="product_details_block">
            <span class="left_arrow"></span>
            <span class="prod_details_heading">Product Details</span>
            <span class="right_arrow"></span>
        </div>
        <div class="product_list_box">
            <ul class="prod_list_ul">
                <li class="prod_list_li"><span>Knitting ERP</span></li>
                <li class="prod_list_li"> <span>Woven ERP</span></li>
                <!-- <li class="prod_list_li"></li> -->
            </ul>
        </div>
        <div class="product_list_box">
            <ul class="prod_list_ul">
                <li class="prod_list_li"><span>Spinning ERP</span></li>
                <li class="prod_list_li"><span>Sweater ERP</span></li>
                <!-- <li class="prod_list_li"></li> -->
            </ul>
        </div>
        <div class="product_list_box">
            <ul class="prod_list_ul">
                <li class="prod_list_li"><span>Planning ERP</span></li>
                <li class="prod_list_li"><span>HR & Accounts</span></li>
                <!-- <li class="prod_list_li"></li> -->
            </ul>
        </div>
        <footer class="login_footer">
            <div class="phone_no">+8801729254876</div>
            <div class="email_address">info@logicsoftbd.com</div>
            <div class="website">www.logicsoftbd.com</div>
            <div class="location">House#83, Road#4, Block- A Post: Banani, Dhaka</div>
        </footer>
	</div>
</body>
    <script>
        function resetuser()
        {
            document.getElementById('tr_reset_user').style.visibility="visible";
        }

        // if (document.getElementById('ipp').value=="") setTimeout('window.location.reload()',1000);

    </script>
</html>
<?php ob_flush(); ?>


