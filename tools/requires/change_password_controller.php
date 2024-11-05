<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

extract($_REQUEST);
 
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	 
	if ($operation==1)   // Update Here
	{
		$con = connect();
		
		$txt_old_passwd=trim(encrypt(str_replace("'","",$txt_old_passwd)));
		$txt_new_passwd=trim(encrypt(str_replace("'","",$txt_new_passwd)));
		 
		if (return_field_value("password", "user_passwd", "user_name=".$txt_user_id) != $txt_old_passwd)
		{
			echo "12**0"; die;
		}
		//$con = connect();
		$field_array="password"; 
		//$field_array="id,user_name,password,created_on,created_by,access_ip,expire_on,user_level,buyer_id,unit_id,is_data_level_secured,valid";
		
		$data_array=" '".$txt_new_passwd."' ";
		
		$rID=sql_update("user_passwd",$field_array,$data_array,"user_name","".$txt_user_id."",1);
		 
		
		//echo "1****".$rID;
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);   
				echo "1**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		
		disconnect($con);
		die;
	}
}
 

?>