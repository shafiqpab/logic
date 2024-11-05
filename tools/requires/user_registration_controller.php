<?php

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="save_update_delete")
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$user_ip                 = trim($txt_user_ip);
	$user_mac                = trim($txt_user_mac);
	$executive_user_id       = trim($txt_executive_user_id);
	$executive_user_password = trim(encrypt(str_replace("'","",$txt_executive_user_password)));

	if ($operation==0)  // Insert Here
	{
		$query = "select user_name, password from user_passwd where user_name=$executive_user_id and password='$executive_user_password' and valid=1";
		$result_query = sql_select($query);
		if (count($result_query) == 1)
		{
			echo "0**OK";
		}
		else
		{
			echo "0**Not OK";
		}
	}
	elseif ($operation==1)  // Update Here
	{
	 	$query = "select user_name, password from user_passwd where user_name=$executive_user_id and password='$executive_user_password' and valid=1";
		$result_query = sql_select($query);
		if (count($result_query) == 1)
		{
			echo "1**OK";
		}
		else
		{
			echo "1**Not OK";
		}
	}	
}
