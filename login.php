<? error_reporting(0);?>
<!DOCTYPE html>
<html lang="en" >
<head>
	<link rel="icon" type="image/x-icon" href="images/favicon.ico">
  	<meta charset="UTF-8">
  	<title>Signin To Your Account</title>
  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
	<style>
		body {
			background-image: url("images/header/property-bg.jpg");
 			background-color: #cccccc;
			 background-repeat: no-repeat;
			 background-size: cover;
			 background-position: center;
			/* background: #f2f2f2; */
			}
			.animate {
			-webkit-transition: all 0.3s linear;
			transition: all 0.3s linear;
			}
			.text-center {
			text-align: center;
			}
			.pull-left {
			float: left;
			}
			.pull-right {
			float: right;
			}
			.clearfix:after {
			visibility: hidden;
			display: block;
			font-size: 0;
			content: " ";
			clear: both;
			height: 0;
			}
			.clearfix {
			display: inline-block;
			}
			/* start commented backslash hack \*/
			* html .clearfix {
			height: 1%;
			}
			.clearfix {
			display: block;
			}
			/* close commented backslash hack */
			a {
			color: #6db000;
			}
			a:hover,
			a:focus {
			color: #00755d;
			-webkit-transition: all 0.3s linear;
			transition: all 0.3s linear;
			}
			.text-primary {
			color: #6db000;
			}
			input:-webkit-autofill {
			-webkit-box-shadow: 0 0 0 1000px white inset !important;
			}
			.logo h1 {
			color: #6db000;
			margin-bottom: -12px;
			}
			input[type="checkbox"] {
			width: auto;
			}
			button {
			cursor: pointer;
			background: #6db000;
			width: 100%;
			border: 0;
			padding: 10px 15px;
			color: #fff;
			font-size: 20px;
			-webkit-transition: 0.3s linear;
			transition: 0.3s linear;
			}
			span.validate-tooltip {
			background: #D91717;
			width: 100%;
			display: block;
			padding: 5px;
			color: #fff;
			box-sizing: border-box;
			font-size: 14px;
			margin-top: -28px;
			-webkit-transition: all 0.3s ease-in-out;
			transition: all 0.3s ease-in-out;
			-webkit-animation: tooltipanimation 0.3s 1;
			animation: tooltipanimation 0.3s 1;
			}
			.input-group {
			position: relative;
			margin-bottom: 20px;
			}
			.input-group label {
			position: absolute;
			top: 9px;
			left: 10px;
			font-size: 16px;
			color: #cdcdcd;
			font-weight: normal;
			padding: 2px 5px;
			z-index: 5;
			-webkit-transition: all 0.3s linear;
			transition: all 0.3s linear;
			}
			.input-group input {
			outline: none;
			display: block;
			width: 100%;
			height: 40px;
			position: relative;
			z-index: 3;
			border: 1px solid #d9d9d9;
			padding: 10px 10px;
			background: #ffffff;
			box-sizing: border-box;
			font-wieght: 400;
			-webkit-transition: 0.3s ease;
			transition: 0.3s ease;
			}
			.input-group .lighting {
			/* background: #6db000; */
			width: 0;
			height: 2px;
			display: inline-block;
			position: absolute;
			top: 40px;
			left: 0;
			-webkit-transition: all 0.3s linear;
			transition: all 0.3s linear;
			}
			.input-group.focused .lighting {
			width: 100%;
			}
			.input-group.focused label {
			background: #fff;
			font-size: 12px;
			top: -8px;
			left: 5px;
			color: #6db000;
			}
			.input-group span.validate-tooltip {
			margin-top: 0;
			}
			.wrapper {
			width: 320px;
			background: #fff;
			margin: 20px auto;
			min-height: 200px;
			border: 1px solid #f3f3f3;
			}
			.wrapper .inner-warpper {
			padding: 50px 30px 60px;
			box-shadow: 10px 1.732px 20px 0px rgba(0, 0, 0, 0.063);
			border: 1px dashed #6db000;
			}
			.wrapper .title {
			margin-top: 0;
			}
			.wrapper .supporter {
			margin-top: 10px;
			font-size: 14px;
			color: #8E8E8E;
			cursor: pointer;
			}
			.wrapper .remember-me {
			cursor: pointer;
			}
			.wrapper input[type="checkbox"] {
			float: left;
			margin-right: 5px;
			margin-top: 2px;
			cursor: pointer;
			}
			.wrapper label[for="rememberMe"] {
			cursor: pointer;
			}
			.wrapper .signup-wrapper {
			padding: 10px;
			font-size: 14px;
			background: #EBEAEA;
			}
			.wrapper .signup-wrapper a {
			text-decoration: none;
			color: #7F7F7F;
			}
			.wrapper .signup-wrapper a:hover {
			text-decoration: underline;
			}
			@-webkit-keyframes tooltipanimation {
			from {
				margin-top: -28px;
			}
			to {
				margin-top: 0;
			}
			}
			@keyframes tooltipanimation {
			from {
				margin-top: -28px;
			}
			to {
				margin-top: 0;
			}
			}
			.direction {
			width: 200px;
			position: fixed;
			top: 120px;
			left: 20px;
			font-size: 14px;
			line-height: 1.2;
			text-align: center;
			background: #9365B8;
			padding: 10px;
			color: #fff;
			}
			@media (max-width: 480px) {
			.direction {
				position: static;
			}
			}
	</style>
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
    // echo load_html_head_contents("RTML ERP Solution","", $filter, '', $unicode);
	?>
    <script language="Javascript" type="text/javascript" src="js/jquery_easy_ip.js"></script>
    <script>
    	function my_callback(json) {
    		$.ajax({
    			url: "includes/common_functions_for_js.php?action=add_ip_session&ip_address="+json.IP,
    			async: false
    		}).responseText
    	}

       //01714899174 khademul, print factory -- mannan

   </script>
</head>
<body>
<input type="hidden" id="ipp" value="<? echo $_SESSION['logic_erp']["pc_local_ip"]; ?>" />
	<?php
	if(isset($_POST["reset_user"]))
	{
		$reset_user  = trim($_POST['txt_reset_user']);
		$reset_email = trim($_POST['txt_reset_email']);
		if( empty( $txt_reset_user ) ) {
			header('location: login.php?m=5');
			exit();
		}
		if( empty( $txt_reset_email ) ) {
			header('location: login.php?m=6');
			exit();
		}
		$rand=mt_rand(1011,9999);
		$nameArray=sql_select( "select user_name, user_email from user_passwd where user_name='".$txt_reset_user."' and user_email='".$reset_email."'" );
			/*echo $nameArray[0][csf('user_name')];
			echo $nameArray[0][csf('user_email')];
			var_dump( $nameArray);
			exit;*/

			$subject  = "Password Reset Request for PLATFORM User";
			$headers  = "From: " ."support@logicsoftbd.com" . "\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			$message  = '<html><body>';
			$message .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
			$message .= "<tr style='background: #0166B3; color:#fff; text-align:center;'><td ><strong>Platform 3.1</strong> </td></tr>";
			$message .= "<tr style='background: #eee;'><td ><strong>Your secrate code is here</strong> </td></tr>";
			$message .= "<tr><td><strong>Code: " . $rand . "</strong> </td></tr>";
			$message .= "<tr style='background: #ED1D3B; color:#fff;'><td><strong>Developed by <a style='color:#fff;'  href='http://www.logicsoftbd.com/'> Logic software Ltd</a></strong> </td></tr>";
			$message .= "</table>";
			$message .= "</body></html>";

			if($nameArray[0][csf('user_name')] == $reset_user && $nameArray[0][csf('user_email')] == $reset_email)
			{
				$mail_to = mail($reset_email,$subject,$message,$headers);
				if($mail_to==1)
				{
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}
					$rID0=execute_query( "update user_passwd set is_fst_time=1, reset_code=$rand where user_name='".$txt_reset_user."' and user_email='".$reset_email."'", 1 );
					if($db_type==0)
					{
						if($rID0==1 )
						{
							mysql_query("COMMIT");
							//echo "0**".$rID0;
							//echo "Email has been send and Data Table has been updated";
							header('location: login.php?m=7');
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "15**".$rID0;
						}
					}
					if($db_type==2 || $db_type==1 )
					{
						if($rID0==1)
						{
							oci_commit($con);
							//echo "1**".$rID0;
							header('location: login.php?m=7');
						}
						else
						{
							oci_rollback($con);
							echo "15**".$rID0;
						}
					}
					disconnect($con);
					die;
					//header('location: login.php?m=2');
					//exit();
				}
			}
			else
			{
				header('location: login.php?m=8');
				exit();
			}
		    //txt_reset_user
		}

		if( isset( $_POST["submit"] ) )
		{
			$userid   = $_POST["txt_userid"];
			$password = trim(encrypt(($_POST["txt_password"])));
			$usermac  = trim($_POST["hiddenUserMAC"]);
			if( empty( $userid ) ) {
				header('location: login.php?m=1');
				exit();
			}
			if( empty( $password ) ) {
				header('location: login.php?m=2');
				exit();
			}
			$e_date = date('Y-m-d');
		    /// $ddd="SELECT password,user_level,id,access_ip,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE user_name = '$userid' AND valid = 1";

			$log_sql = sql_select("SELECT password, user_level, id, access_ip, buyer_id, unit_id, is_data_level_secured, graph_id, store_location_id, supplier_id, company_location_id, mac_addr, tna_task_id FROM user_passwd WHERE user_name = '$userid' AND valid = 1");

			foreach($log_sql as $r_log)
			{
				if( $password == trim($r_log[csf('PASSWORD')]) )
				{
					$ip_details=explode(",",$r_log[csf('access_ip')]);
					$output = `uname -a`;

					if (trim($ip_details[1]) != '')
					{
						if (preg_match("#Linux#i",$output))
							$proxy_ip=get_ip_mac("traceroute");
						else
							$proxy_ip=get_ip_mac("tracert");
					}
					$con = connect();  // connect to DB
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}
					if (trim($ip_details[1]) != '')
					{
						if(trim($ip_details[1]) != trim($proxy_ip))
						{
							$id=return_next_id( "id", "login_history", 1 );
							$field_array="id, user_id, lan_ip, lan_mac, wan_ip, login_time, login_date, login_status";
							$data_array="('".$id."','".$r_log[csf('id')]."','".trim($_SESSION['logic_erp']["pc_local_ip"])."','".$ip_details[2]."','".$proxy_ip."','".$pc_time."','".$pc_date."','1')";
							$rID=sql_insert("login_history",$field_array,$data_array,1);
							header('location: login.php?m=4');
							exit();
						}
					}

					$mac_addr = $r_log[csf('mac_addr')];
					if (trim($mac_addr) != '')
					{
						//echo $usermac;die;
						if($usermac != $mac_addr)
						{
							$id=return_next_id( "id", "login_history", 1 );
							$field_array="id, user_id, lan_ip, lan_mac, wan_ip, login_time, login_date, login_status";
							$data_array="('".$id."','".$r_log[csf('id')]."','".trim($_SESSION['logic_erp']["pc_local_ip"])."','".$mac_addr."','".$proxy_ip."','".$pc_time."','".$pc_date."','1')";
							$rID=sql_insert("login_history",$field_array,$data_array,1);
							header('location: login.php?m=4');
							exit();
						}
					}



					if (trim($ip_details[0]) != '')
					{
						if(trim($ip_details[0]) != trim($_SESSION['logic_erp']["pc_local_ip"]))
						{
							$id=return_next_id( "id", "login_history", 1 ) ;
							$field_array="id, user_id, lan_ip, lan_mac, wan_ip, login_time, login_date, login_status";
							$data_array="('".$id."','".$r_log[csf('id')]."','".trim($_SESSION['logic_erp']["pc_local_ip"])."','".$ip_details[2]."','".$proxy_ip."','".$pc_time."','".$pc_date."','1')";
							$rID=sql_insert("login_history",$field_array,$data_array,1);
							header('location: login.php?m=3');
							exit();
						}
					}

					$id=return_next_id( "id", "login_history", 1 ) ;
					$field_array="id, user_id, lan_ip, lan_mac, wan_ip, login_time, login_date, login_status";
					$data_array="('".$id."','".$r_log[csf('id')]."','".trim($_SESSION['logic_erp']["pc_local_ip"])."','".$ip_details[2]."','".$proxy_ip."','".$pc_time."','".$pc_date."','0')";
					$rID=sql_insert("login_history",$field_array,$data_array,1);

					//header('location: login.php?m='.$rID);
					//exit();
					if($db_type==0)
					{
						if($rID ){
							mysql_query("COMMIT");
							echo "0****".$rID;
						}
						else{
							mysql_query("ROLLBACK");
							echo "15****".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "0****".$rID;
						}
						else
						{
							oci_rollback($con);
							echo "15****".$rID;
						}
					}

					$image_location=return_field_value("image_location","common_photo_library","master_tble_id = '".$r_log[csf('ID')]."' and form_name = 'user_info'","image_location");

					$_SESSION['logic_erp']["history_id"]=$id;
					$_SESSION['logic_erp']["photo"]=$image_location;

					$_SESSION['logic_erp']["user_id"]		= $r_log[csf('ID')];
					$_SESSION['logic_erp']["user_name"]		= $userid;
					$_SESSION['logic_erp']["user_level"]	= $r_log[csf('USER_LEVEL')];
					$_SESSION['logic_erp']["user_ip"]		= $r_log[csf('ACCESS_IP')];
					$_SESSION['logic_erp']["buyer_id"]	= $r_log[csf('BUYER_ID')];
					$_SESSION['logic_erp']["company_id"]	= $r_log[csf('UNIT_ID')];
					$_SESSION['logic_erp']["data_level_secured"]	= $r_log[csf('IS_DATA_LEVEL_SECURED')];
					$_SESSION['logic_erp']["graph_id"]	= $r_log[csf('graph_id')];
					$_SESSION['logic_erp']['user_menu']		= array();
					$_SESSION['logic_erp']['store_location_id']		= $r_log[csf('store_location_id')];
					$_SESSION['logic_erp']['supplier_id']		= $r_log[csf('supplier_id')];
					$_SESSION['logic_erp']['company_location_id']	= $r_log[csf('company_location_id')];
					$_SESSION['logic_erp']['tna_task_id']	= $r_log[csf('tna_task_id')];
					 /*$result = sql_select("SELECT t1.m_mod_id,t1.main_module FROM main_module AS t1
							LEFT JOIN user_priv_module AS t2 ON t1.m_mod_id = t2.module_id
							WHERE t2.user_id = '".$r_log[csf('id')]."' AND t2.valid = '1' AND t1.status = '1'
							ORDER BY t1.mod_slno ASC, main_module ASC");*/

							$sql="select page_id, company_id, user_id, field_id, field_name, is_disable, defalt_value from field_level_access where  user_id='".$r_log[csf('ID')]."' and status_active=1 and is_deleted=0";
							$sql_exe=sql_select($sql);
							foreach($sql_exe as $row)
							{
								$_SESSION['logic_erp']['data_arr'][$row[csf("page_id")]][$row[csf("company_id")]][$row[csf("field_name")]]['is_disable']=$row[csf("is_disable")];
								$_SESSION['logic_erp']['data_arr'][$row[csf("page_id")]][$row[csf("company_id")]][$row[csf("field_name")]]['defalt_value']=$row[csf("defalt_value")];
							}


							$sql="select id, page_id, field_id, field_name,field_message, is_mandatory from mandatory_field where status_active=1 and is_deleted=0 and is_mandatory=1";
							$sql_exe=sql_select($sql);
							foreach($sql_exe as $row)
							{
								$_SESSION['logic_erp']['mandatory_field'][$row[csf("page_id")]][$row[csf("field_id")]]=$row[csf("field_name")];
								$_SESSION['logic_erp']['mandatory_message'][$row[csf("page_id")]][$row[csf("field_id")]]=$row[csf("field_message")];
							}

							$result = sql_select("SELECT a.m_mod_id , a.main_module from main_module a, user_priv_module b where  a.m_mod_id = b.module_id
								and b.user_id = '".$r_log[csf('id')]."' AND b.valid = '1' AND a.status = '1'
								ORDER BY a.mod_slno ASC");

							$all_module = array();
							foreach($result as $row)
							{
								$_SESSION['logic_erp']['user_menu'][] = array(
									'module_id'		=> $row[csf('M_MOD_ID')],
									'module_name'	=> $row[csf('MAIN_MODULE')]
								);
								disconnect($con);

							}
							header('location:index.php');
						}
						else header('location: login.php?m=2');
					}
				}

				?>
<!-- partial:index.partial.html -->
<div class="logo text-center">
<div class="logic_logo_block">
	<img src="images/header/p-logo.png" alt="RTML Software BD Ltd." class="logic_logo" style="max-width: 100%;">
</div>
</div>
<div class="wrapper">
  <div class="inner-warpper text-center">
    <h2 class="title">Login to your account</h2>
    <form id="login_form" name="login_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
      <div class="input-group focused">
        <label class="palceholder" for="txt_userid">User Name</label>
        <input class="form-control" name="txt_userid" id="txt_userid" type="text" placeholder="" />
        <span class="lighting"></span>
      </div>
      <div class="input-group focused">
        <label class="palceholder" for="txt_password">Password</label>
        <input class="form-control" name="txt_password" id="txt_password" type="password" placeholder="" />
        <span class="lighting"></span>
      </div>

      <button type="submit" id="submit" name="submit">Login</button>
	  <!-- <input type="submit" class="login" id="submit" name="submit"  value="Login"/> -->
      <div class="clearfix supporter">
        <a class="forgot pull-right" onclick="resetuser()" href="javascript:void(0);">Forgot Password?</a>
      </div>
	  <?  //echo get_ip_mac();
		if ($m==1) echo "Please Enter User  Name.";
		else if ($m==2) echo "Please Enter Password.";
		else if ($m==3) echo "Your IP address Does not Match, You are not Allowed to Login From This Network.";
		else if ($m==4) echo "You are not Belongs to this Domain, Please Dont Try from Here again.";
		else if ($m==5) echo "Please Enter User Name.";
		else if ($m==6) echo "Please Enter Email. ";
		else if ($m==7) echo "Email has been send and Data Table has been updated. ";
		else if ($m==8) echo "Your User Name and Email is not mathced.";
		?>
    </form>

	
	<div id="tr_reset_user"  style="display:none">
		<lable class="reset_lable">Reset User</lable>
		<input type="text" name="txt_reset_user" class="reset_user_name"  placeholder="Reset User"/>
		<lable class="reset_lable">Email Address</lable>
		<input type="text" name="txt_reset_email"  class="reset_user_name"  placeholder="Email Address"/>
		<input type="submit" name="reset_user" class="login" style="width:100px" value="Reset" />
	</div>
  </div>
</div>

<script>
	function resetuser()
	{
		if(document.getElementById('tr_reset_user').style.visibility=="visible"){
			document.getElementById('tr_reset_user').style.visibility="collapse";
		}else{
			document.getElementById('tr_reset_user').style.visibility="visible";
		}
	}

	var user_ip = '';
	var user_mac = '';
	if (localStorage.getItem('user_ip') && localStorage.getItem('user_mac')) {
		user_ip  = localStorage.getItem('user_ip');
		user_mac = localStorage.getItem('user_mac');
	}

	document.getElementById('hiddenUserIP').value  = user_ip;
	document.getElementById('hiddenUserMAC').value = user_mac;


</script>
</body>
</html>
<?php ob_flush(); ?>
