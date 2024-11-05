<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');


//----------------------------------------------------
$session_time_out=(3600*60*24); // exprie after Half hour (3600 seconds = 1 minute)
if ($_SESSION['enterTime'] != '') {
	$timeDiffernce = time() - $_SESSION['enterTime'];
	if ($timeDiffernce > $session_time_out) {
		unset($_SESSION['enterTime']);
		//header("location: logout.php");
		echo 0;exit();
	} else {
		$_SESSION['enterTime'] = time();
	}
} else {
	$_SESSION['enterTime'] = time();
	ini_set( "session.gc_maxlifetime", $session_time_out );
	ini_set( "session.cookie_lifetime", $session_time_out );
}
//--------------------------------------------------



$user_id = $_SESSION['logic_erp']["user_id"];
extract($_REQUEST);
	if($menuid>0){$wherCon=" and M_MENU_ID = $menuid";}
	else{$wherCon=" and a.F_LOCATION = 'page_container.php?".trim($data)."'";}
	
	$trans_in_row = sql_select("select a.m_menu_id from main_menu a, user_priv_mst b where a.m_menu_id = b.main_menu_id AND b.valid = 1 AND b.user_id = $user_id AND b.show_priv = 1 $wherCon");
	
	echo ($trans_in_row[0]['M_MENU_ID'])?1:0;
	
	
	
	
//----------------------------------------------------------
// if($_SESSION['logic_erp']['user_id']==165000000000){
	
// 	require_once('../mailer/class.phpmailer.php');
// 	require_once('../auto_mail/setting/mail_setting.php');
	
	
// 	$subject="User Login Check";
// 	$message="User Id:".$_SESSION['logic_erp']['user_id'].", User IP:".$_SERVER['REMOTE_ADDR'].", Login Time:".date("Y-m-d H:i:s",time()).",M_MENU_ID:".$trans_in_row[0][M_MENU_ID];
    
	
// 	$file = 'user_log.txt';
// 	$current = file_get_contents($file);
// 	$current .= $message."\n";
// 	file_put_contents($file, $current);


// 	$to="fakhrul@urmigroup.net,reza@logicsoftbd.com";
// 	$header=mailHeader();
// 	sendMailMailer( $to, $subject, $message, $from_mail );
	
// }
//-------------------------------------------	
	
	
	
	
	
	
	
	
	
exit();
?>