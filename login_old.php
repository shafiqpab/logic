<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<LINK REL="SHORTCUT ICON" HREF="images/logic_logo.png">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Logic ERP Solution</title>
    
    </head>


<?php

 


 	session_start();
    include('includes/common.php');
	 
	extract($_REQUEST);
 
$pc_time= add_time(date("H:i:s",time()),360);  
$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));
echo load_html_head_contents("Logic ERP Solution","", $filter, '', $unicode);
//echo decrypt("oJ+nnps="); 


 
?>
 
</head>

<body class="login_body">
	<center>
    <input type="hidden" id="ipp" value="<? echo $_SESSION['logic_erp']["pc_local_ip"]; ?>" />
		<?php
		 
		if( isset( $_POST["submit"] ) ) {
			$con = connect();  // connect to DB
			$userid = trim($txt_userid);
			$password = encrypt(trim($txt_password));
			if(  trim($userid)=="" ) {
				header('location: login.php?m=1');
				exit();
			}
			if( empty( $password ) ) {
				header('location: login.php?m=2');
				exit();
			} 
			$e_date = date('Y-m-d');	
		/// $ddd="SELECT password,user_level,id,access_ip,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE user_name = '$userid' AND valid = 1";
		 
			$log_sql = sql_select("SELECT password,user_level,id,access_ip,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE user_name = '$userid' AND valid = 1"); 
		 	
			foreach($log_sql as $r_log){ 
			
				if( $password == trim($r_log[csf('PASSWORD')]) ) {
				 
					   
					
					
					$_SESSION['logic_erp']["history_id"]=$id;
					
					$_SESSION['logic_erp']["user_id"]		= $r_log[csf('ID')];
					$_SESSION['logic_erp']["user_name"]		= $userid;
					$_SESSION['logic_erp']["user_level"]	= $r_log[csf('USER_LEVEL')];
					$_SESSION['logic_erp']["user_ip"]		=  $_SERVER['REMOTE_ADDR'];//$r_log[csf('ACCESS_IP')];
					$_SESSION['logic_erp']["buyer_id"]	= $r_log[csf('BUYER_ID')];
					$_SESSION['logic_erp']["company_id"]	= $r_log[csf('UNIT_ID')];
					$_SESSION['logic_erp']["data_level_secured"]	= $r_log[csf('IS_DATA_LEVEL_SECURED')];
					
					$_SESSION['logic_erp']['user_menu']		= array();
					 
					$result = sql_select("SELECT a.m_mod_id , a.main_module from main_module a, user_priv_module b where  a.m_mod_id = b.module_id
							and b.user_id = '".$r_log[csf('id')]."' AND b.valid = '1' AND a.status = '1'
							ORDER BY a.mod_slno ASC"); 
							
					$all_module = array();
					 
							foreach($result as $row){ 
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
		<div style="width:100%; height:150px;"></div>
		<table width="100%"  align="center" border="0">
			<tr><td align="center"><div id="footer" style="width:50%; background-color:#FFFFFF">
					<?  //echo get_ip_mac();
						if ($m==1) echo "Please Enter User  Name."; 
						else if ($m==2) echo "Please Enter Password."; 
						else if ($m==3) echo "Your IP address Does not Match, You are not Allowed to Login From This Network."; 
						else if ($m==4) echo "You are not Belongs to this Domain, Please Dont Try from Here again."; 

					?></div></td></tr>
			<tr>
				<td align="center">
					 
						<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
							<table width="300" border="0" align="center" background="images/body3.jpg">
								<tr><td colspan="2" align="center" height="10" bgcolor=""></td></tr>
								<tr><td colspan="2" align="center" bgcolor=""><h3>User Login</h3></td></tr>
								<tr>
									<td align="right">UserID:</td>
									<td><input type="text" name="txt_userid" /></td>
								</tr>
								<tr>
									<td align="right">Password:</td>
									<td><input type="password" name="txt_password" /></td>
								</tr>
								<tr>
									 
									<td valign="middle" height="60" align="center" colspan="2">
										<input type="submit" name="submit" class="formbutton" style="width:100px" value="Login" />&nbsp;&nbsp;
										<input type="reset" name="reset" class="formbutton" style="width:100px" value="Reset" />
									</td>
								</tr>
							</table>
						</form>
					 
				</td>
			</tr>
			<tr>
				<td align="center">
					<div id="footer" style="width:50%;">
						<img src="images/copyright.jpg" height="20" width="20" /> 2011 <span>Design &amp; Developed By <a href="http://www.logicsoftbd.com" target="_blank">Logic Software Limited.</a></span>
					</div>
				</td>
			</tr>
		</table>
	</center>
</body>
<script>

// if (document.getElementById('ipp').value=="") setTimeout('window.location.reload()',1000);

</script>
</html>
<?php ob_flush(); ?>


