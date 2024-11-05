<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="search_list_view")
{
		$arr = array(0=>$accounts_journal_type,3=>$row_status);	
		echo  create_list_view ( "list_view", "Journal Type,Pre-Fix,Starting Number,Status", "200,100,100,100","600","250",0, "select  journal_type,pre_fix,starting_number,status_active,id from lib_account_journal  where status_active=1 and is_deleted=0", "get_php_form_data", "id","'load_php_data_to_form'", 1,"journal_type,0,0,status_active", $arr ,"journal_type,pre_fix,starting_number,status_active", "../accounts/requires/journal_prefix_controller", 'setFilterGrid("list_view",-1);' ) ;
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select journal_type,pre_fix,starting_number,status_active,id from lib_account_journal where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_accounts_journal_type').value = '".($inf[csf("journal_type")])."';\n";    
		echo "document.getElementById('text_pre_fix').value  = '".($inf[csf("pre_fix")])."';\n"; 
		echo "document.getElementById('text_starting_number').value = '".($inf[csf("starting_number")])."';\n";    
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_journal_prefix',1);\n";  
	}
}

if ($action=="save_update_delete")
{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here===========================================
	{
		if (is_duplicate_field( "pre_fix", "lib_account_journal", "pre_fix=$text_pre_fix" ) == 1)
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
			 
			$id=return_next_id( "id", "lib_account_journal", 1 ) ; 
			$field_array="id,journal_type,pre_fix,starting_number,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$cbo_accounts_journal_type.",".$text_pre_fix.",".$text_starting_number.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
			$rID=sql_insert("lib_account_journal",$field_array,$data_array,1);
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
	
	else if ($operation==1)   // Update Here=======================================================
	{
		if (is_duplicate_field( "pre_fix", "lib_account_journal", "pre_fix=$text_pre_fix and id!=$update_id" ) == 1)
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
			
			$field_array="journal_type*pre_fix*starting_number*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_accounts_journal_type."*".$text_pre_fix."*".$text_starting_number."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";
			$rID=sql_update("lib_account_journal",$field_array,$data_array,"id","".$update_id."",1);
			
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
	
	else if ($operation==2)   // Update Here===============================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("lib_account_journal",$field_array,$data_array,"id","".$update_id."",1);
		
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


?>