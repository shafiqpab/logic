<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="search_list_view")
{
		$arr = array(1=>$accounts_main_group,3=>$accounts_statement_type,4=>$accounts_account_type,5=>$accounts_cash_flow_group,6=>$row_status);	
		echo  create_list_view ( "list_view", "Sub Group Code,Main Group,Sub Group,Statement Type,Balance Type,Cash Flow Group,Status", "100,150,150,110,60,120,60","800","250",0, "select  sub_group_code,main_group,sub_group,statement_type,account_type,cash_flow_group,status_active,id from lib_account_group  where status_active=1 and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 0,"0,main_group,0,statement_type,account_type,cash_flow_group,status_active", $arr ,"sub_group_code,main_group,sub_group,statement_type,account_type,cash_flow_group,status_active", "../accounts/requires/account_group_controller", 'setFilterGrid("list_view",-1);' );
}


if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select  sub_group_code,main_group,sub_group,statement_type,account_type,cash_flow_group,status_active,id from lib_account_group where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_main_accounts_group').value = '".($inf[csf("main_group")])."';\n";    
		echo "document.getElementById('text_sub_group').value  = '".($inf[csf("sub_group")])."';\n"; 
		echo "document.getElementById('txt_sub_group_code').value = '".($inf[csf("sub_group_code")])."';\n";    
		echo "document.getElementById('cbo_statement_type').value  = '".($inf[csf("statement_type")])."';\n"; 
		echo "document.getElementById('cbo_account_type').value = '".($inf[csf("account_type")])."';\n";    
		echo "document.getElementById('cbo_cash_flow_group').value  = '".($inf[csf("cash_flow_group")])."';\n"; 
		echo "document.getElementById('cbo_retained_earning').value = '".($inf[csf("retained_earnings")])."';\n"; 
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_accounts_group',1);\n";  
	}
}


if ($action=="save_update_delete")
{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here==================================================
	{
		if (is_duplicate_field( "sub_group_code", "lib_account_group", "sub_group_code=$txt_sub_group_code and sub_group=$text_sub_group" ) == 1)
		{
			echo "12**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", " lib_account_group", 1 ) ; 
			$field_array="id,sub_group_code,main_group,sub_group,statement_type,account_type,cash_flow_group,retained_earnings,inserted_by,insert_date,status_active,is_deleted";
			
			$data_array="(".$id.",".$txt_sub_group_code.",".$cbo_main_accounts_group.",".$text_sub_group.",".$cbo_statement_type.",".$cbo_account_type.",".$cbo_cash_flow_group.",".$cbo_retained_earning.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
			$rID=sql_insert("lib_account_group",$field_array,$data_array,1);
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
			 if($rID )
			    {
					oci_commit($con);   
					echo "0**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
	}
	
	else if ($operation==1)   // Update Here====================================================
	{
		if (is_duplicate_field( "sub_group_code", "lib_account_group", "sub_group_code=$txt_sub_group_code and sub_group=$text_sub_group and id!=$update_id" ) == 1)
		{
			echo "12**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$field_array="sub_group_code*main_group*sub_group*statement_type*account_type*cash_flow_group*retained_earnings*updated_by*update_date*status_active*is_deleted";
			
			$data_array="".$txt_sub_group_code."*".$cbo_main_accounts_group."*".$text_sub_group."*".$cbo_statement_type."*".$cbo_account_type."*".$cbo_cash_flow_group."*".$cbo_retained_earning."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";
			
			$rID=sql_update("lib_account_group",$field_array,$data_array,"id","".$update_id."",1);
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "1**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
			 if($rID )
			    {
					oci_commit($con);   
					echo "1**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
		  disconnect($con);
		  die;
		}
	}
	
	else if ($operation==2)   // Delete Here===================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("lib_account_group",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		if($db_type==2 || $db_type==1 )
			{
			 if($rID )
			    {
					oci_commit($con);   
					echo "2**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
		disconnect($con);
		die;
	}
}


?>