<?php
ini_set('display_errors',1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<LINK REL="SHORTCUT ICON" HREF="images/logic_logo.png">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Logic ERP Solution</title>

	<?php
    /*IIS User Authen
    $_SERVER['LOGON_USER']
    $_SERVER['AUTH_USER']
    $_SERVER['REDIRECT_LOGON_USER']
    $_SERVER['REDIRECT_AUTH_USER']
    */
	//oci_connect('metroerp', 'metroerp', 'PERP:1521/XE');
	//https://jaswanttak.wordpress.com/2010/08/31/activate-oracle-on-xampp-for-windows-oci8/
	//print_r( get_loaded_extensions() );
	//D:\oraclexe\app\oracle\product\11.2.0\server\bin;;C:\Program Files (x86)\Intel\iCLS Client\;C:\Program Files\Intel\iCLS Client\;C:\Windows\system32;C:\Windows;C:\Windows\System32\Wbem;C:\Windows\System32\WindowsPowerShell\v1.0\;C:\Program Files\Intel\Intel(R) Management Engine Components\DAL;C:\Program Files (x86)\Intel\Intel(R) Management Engine Components\DAL;C:\Program Files\Intel\Intel(R) Management Engine Components\IPT;C:\Program Files (x86)\Intel\Intel(R) Management Engine Components\IPT;C:\Program Files (x86)\NVIDIA Corporation\PhysX\Common
	//7867  172.16.16.53
	//cscript /nologo configure.js "--enable-snapshot-build" "--disable-isapi" "--enable-debug-pack" "--disable-isapi" "--without-mssql" "--without-pdo-mssql" "--without-pi3web" "--with-pdo-oci=D:\php-sdk\oracle\instantclient10\sdk,shared" "--with-oci8=D:\php-sdk\oracle\instantclient10\sdk,shared" "--with-oci8-11g=D:\php-sdk\oracle\instantclient11\sdk,shared" "--enable-object-out-dir=../obj/" "--enable-com-dotnet" "--with-mcrypt=static"

    session_start();
		
    include('includes/common.php'); 
	//echo "2dXj1tc="; die;
  //// echo decrypt("np2nn56V"); die;

    extract($_REQUEST);
	//$pc_time= add_time(date("H:i:s",time()),360);
	//$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));
	// $_SESSION['logic_erp']["data"]='';
    //echo $_SESSION['logic_erp']["data"];
 	//$pc_time= date("H:i:s",time());


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
	//echo  strtoupper($macAddr)."__".$ipAddress."__".$proxy_address;die;
    //print_r($_SERVER);
    echo load_html_head_contents("Logic ERP Solution","", $filter, '', $unicode);

    $time= date('d-m-Y');
    $day = date("D", strtotime($time));
    if($day == 'Sat' || $day == 'Mon' || $day == 'Wed')
    {
    ?>
    	<link rel="stylesheet" href="css/login_pg.css" type="text/css">
    <? }else{ ?>
    	<link rel="stylesheet" href="css/login_pg_red.css" type="text/css">
    <? } ?>
    
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
	<?php  
	
	if( isset($_POST["reset_user"]) )
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
							header('location: login.php?m=7');
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID0;
						}
					}
					if($db_type==2 || $db_type==1 )
					{
						if($rID0==1)
						{
							oci_commit($con);
							header('location: login.php?m=7');
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID0;
						}
					}
					disconnect($con);
					die;
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
		//  echo $ddd="SELECT password,user_level,id,access_ip,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE user_name = '$userid' AND valid = 1";die;

			$log_sql = sql_select("SELECT password, user_level, id, access_ip, buyer_id, brand_id,unit_id, is_data_level_secured, graph_id, store_location_id, supplier_id, company_location_id, mac_addr, user_name,single_user_id,user_code,tna_task_id FROM user_passwd WHERE user_name = '$userid' AND valid = 1");
			
			 //print_r($log_sql);die;
			if(sizeof($log_sql) !=0){
				foreach($log_sql as $r_log)
				{  //echo $password.'='.$_POST["txt_password"].'='.$r_log[csf('PASSWORD')];die;
					
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
						
						// echo "DSD";die;
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
						//echo "insert into login_history (".$field_array.") values ".$data_array;die;
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
								echo "10****".$rID;
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
								echo "10****".$rID;
							}
						}

						$image_location=return_field_value("image_location","common_photo_library","master_tble_id = '".$r_log[csf('ID')]."' and form_name = 'user_info'","image_location");
						$group_logo=return_field_value("image_location","common_photo_library","is_deleted= 0 and form_name='group_logo' order by id desc","image_location");
						
						$_SESSION['logic_erp']["history_id"]			= $id;
						$_SESSION['logic_erp']["photo"]					= $image_location;
						$_SESSION['logic_erp']["group_logo"]			= $group_logo;
						$_SESSION['logic_erp']["user_id"]				= $r_log[csf('ID')];
						$_SESSION['logic_erp']["user_code"]				= $r_log[csf('USER_CODE')];
						$_SESSION['logic_erp']["user_name"]				= $userid;
						$_SESSION['logic_erp']["user_level"]			= $r_log[csf('USER_LEVEL')];
						$_SESSION['logic_erp']["user_ip"]				= $r_log[csf('ACCESS_IP')];
						$_SESSION['logic_erp']["buyer_id"]				= $r_log[csf('BUYER_ID')];
						$_SESSION['logic_erp']["brand_id"]				= $r_log[csf('BRAND_ID')];
						$_SESSION['logic_erp']["company_id"]			= $r_log[csf('UNIT_ID')];
						$_SESSION['logic_erp']["data_level_secured"]	= $r_log[csf('IS_DATA_LEVEL_SECURED')];
						$_SESSION['logic_erp']["graph_id"]				= $r_log[csf('graph_id')];
						$_SESSION['logic_erp']['user_menu']				= array();
						$_SESSION['logic_erp']['store_location_id']		= $r_log[csf('store_location_id')];
						$_SESSION['logic_erp']['supplier_id']			= $r_log[csf('supplier_id')];
						$_SESSION['logic_erp']['company_location_id']	= $r_log[csf('company_location_id')];
						$_SESSION['logic_erp']['single_user']			= $r_log[csf('single_user_id')];
						$_SESSION['logic_erp']['tna_task_id']			= $r_log[csf('tna_task_id')];

						$sql="select page_id, company_id, user_id, field_id, field_name, is_disable, defalt_value from field_level_access where  user_id='".$r_log[csf('ID')]."' and status_active=1 and is_deleted=0";
						$sql_exe=sql_select($sql);
						foreach($sql_exe as $row)
						{
							$_SESSION['logic_erp']['data_arr'][$row[csf("page_id")]][$row[csf("company_id")]][$row[csf("field_name")]]['is_disable']=$row[csf("is_disable")];
							$_SESSION['logic_erp']['data_arr'][$row[csf("page_id")]][$row[csf("company_id")]][$row[csf("field_name")]]['defalt_value']=$row[csf("defalt_value")];
						}


						$sql="select id, page_id, field_id, field_name,field_message, is_mandatory from mandatory_field where status_active=1 and is_deleted=0 and is_mandatory=1 and field_name is not null order by id";
						$sql_exe=sql_select($sql);
						foreach($sql_exe as $row)
						{
							$_SESSION['logic_erp']['mandatory_field'][$row[csf("page_id")]][$row[csf("field_id")]]=$row[csf("field_name")];
							$_SESSION['logic_erp']['mandatory_message'][$row[csf("page_id")]][$row[csf("field_id")]]=$row[csf("field_message")];
						}

						$result = sql_select("SELECT a.m_mod_id , a.main_module from main_module a, user_priv_module b where  a.m_mod_id = b.module_id and b.user_id = '".$r_log[csf('id')]."' AND b.valid = '1' AND a.status = '1'	ORDER BY a.mod_slno ASC");

						$all_module = array();
						foreach($result as $row)
						{
							$_SESSION['logic_erp']['user_menu'][] = array(
								'module_id'		=> $row[csf('M_MOD_ID')],
								'module_name'	=> $row[csf('MAIN_MODULE')]
							);
							disconnect($con);

						}
						
						list($host,$serverurl)=explode("/",$_SERVER['REQUEST_URI']);
						$_SESSION['project_url']= $serverurl;
						$_SESSION['app_notification'] = 1;
						$_SESSION['socket_url'] = '';
						//$_SESSION['socket_url'] = 'localhost:9418';
						$_SESSION['socket_url'] = '182.160.107.70:9418';
						
						
						header('location:index.php');
					}
					else 
					{
						header('location: login.php?m=9');
					}
				}
			}else{
						header('location: login.php?m=10');
			}
	}

	
		
function sql_selectd($strQuery, $is_single_row, $new_conn, $un_buffered, $connection)
{
	if ( $new_conn!="" )
	{
		$new_conn=explode("*",$new_conn);
		$con_select = oci_connect($new_conn[1], $new_conn[2], $new_conn[0]);
	}
	else
	{
		if($connection==""){
			$con_select = connect();
		}else{
			$con_select = $connection;
		}
	}
	//echo  $strQuery;die;
	echo $con_select;
	$result = oci_parse($con_select, $strQuery);
	oci_execute($result);
	print_r(oci_fetch_assoc($result));
	$rows = array();
	
	 while($summ=oci_fetch_assoc($result))
	 {
		if($is_single_row==1)
		{
			$rows[] = $summ;
			if($connection=="") disconnect($con_select);
			return $rows;

			die;
		}
		else
		{
		$rows[] = $summ;
		}
	 }
	if($connection=="")  disconnect($con_select);
	return $rows;
	 //echo $row['mychars']->load(); for clob data type, mychars is clob
	die;
}

				?>
				<div style="width:100%; height:150px;"></div>
				<div class="container">
					<div class="login_fieldset_block">
						<div class="login_form_block">
							<div class="logic_logo_block">
								<img src="images/logic/logic_logo_new.png" alt="Logic Software BD Ltd." class="logic_logo">
							</div>
							<div class="login_form">
								<form id="login_form" name="login_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
									<input type="hidden" id="hiddenUserIP" name="hiddenUserIP" value="">
									<input type="hidden" id="hiddenUserMAC" name="hiddenUserMAC" value="">
									<div class="user_block">
										<div class="user_lable">
											<label for="user_name" class="lable">User Name</label>
										</div>
										<div class="user_input">
											<input type="text" class="user_name" id="txt_userid" name="txt_userid" placeholder="User Name"/>
										</div>
									</div>
									<div class="password_block">
										<div class="password_lable">
											<label for="password" class="lable"> Password </label>
										</div>
										<div class="password_input">
											<input type="password" class="password" id="txt_password" name="txt_password"  placeholder="Password"/>
										</div>
									</div>
									<div class="button_block">
										<div class="login_button">
											<input type="submit" class="login" id="submit" name="submit"  value="Login"/>
										</div>
										<div class="forgot_link" style="display:none;">
											<a class="forgot" onclick="resetuser()" >Forgot Password? <br/> Reset Password</a>
										</div>
									</div>
									<div id="tr_reset_user"  style="visibility:collapse">
										<lable class="reset_lable">Reset User</lable>
										<input type="text" name="txt_reset_user" class="reset_user_name"  placeholder="Reset User"/>
										<lable class="reset_lable">Email Address</lable>
										<input type="text" name="txt_reset_email"  class="reset_user_name"  placeholder="Email Address"/>
										<input type="submit" name="reset_user" class="login" style="width:100px" value="Reset" />
									</div>
									<div id="footer" style="height: 40px; position: relative; top: -90px; color: white; visibility:visible;">
										<strong>
										<?  //echo get_ip_mac();
										if ($m==1) echo "Please Enter User  Name.";
										else if ($m==2) echo "Please Enter Password.";
										else if ($m==3) echo "Your IP address Does not Match, You are not Allowed to Login From This Network.";
										else if ($m==4) echo "You are not Belongs to this Domain, Please Dont Try from Here again.";
										else if ($m==5) echo "Please Enter User Name.";
										else if ($m==6) echo "Please Enter Email. ";
										else if ($m==7) echo "Email has been send and Data Table has been updated. ";
										else if ($m==8) echo "Your User Name and Email is not mathced.";
										else if ($m==9) echo "Your user name or password is incorrect. Please try again.";
										else if ($m==10) echo "Your User Name is not mathced.";
										?>
										</strong>
									</div>
								</form>
							</div>
						</div>
					</div>
		<div class="produc_block">
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
		</div>
		<footer class="login_footer">
			<div class="phone_no">+880 1755 643089</div>
			<div class="email_address"><a href="mailto:all@logicsoftbd.com">all@logicsoftbd.com</a></div>
			<div class="website"><a href="http://logicsoftbd.com/" target="_blank">www.logicsoftbd.com</a></div>
			<div class="location">House#85(Apt-B5), Road#4, Block- A, Post: Banani, Dhaka-1213</div>
		</footer>
	</div>

</body>
<script>
	function resetuser()
	{
		if(document.getElementById('footer').style.visibility=="visible"){
			document.getElementById('footer').style.visibility="collapse";
		}
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
</html>
<?php ob_flush(); ?>
