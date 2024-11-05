<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="group_list_view")
{
	$sql="select  fabric_group_name,status_active,id from   lib_fabric_group_entry where is_deleted=0 order by id desc";
	$arr=array (1=>$row_status);
	echo  create_list_view ( "list_view", "Fabric Group Name,Status", "230,120","350","220",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,status_active", $arr , "fabric_group_name,status_active", "../merchandising_details/requires/fabric_group_entry_controller", 'setFilterGrid("list_view",-1);' ) ;
}
if ($action=="load_php_data_to_form")
{
	
	$nameArray=sql_select( "select  fabric_group_name,status_active,id from   lib_fabric_group_entry where is_deleted=0 and id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_fabric_group_name').value = '".($inf[csf("fabric_group_name")])."';\n";     
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_fabric_group_info',1);\n";  
	}
}

if ($action=="save_update_delete")
{  

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
			$con = connect();
			$id=return_next_id( "id", "lib_fabric_group_entry",1);
			$field_array="id,fabric_group_name,inserted_by,insert_date,status_active,is_deleted";	
			$data_array="(".$id.",".$txt_fabric_group_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";



			$rID=sql_insert("lib_fabric_group_entry",$field_array,$data_array,0);	
			if($db_type==2 || $db_type==1 )
			{
				if($rID)
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
	else if ($operation==1)   // Update Here
	{
			$con = connect();
			//txt_weave
			$field_array="fabric_group_name*updated_by*update_date*status_active";
		    $data_array="".$txt_fabric_group_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."";
			$rID=sql_update("lib_fabric_group_entry",$field_array,$data_array,"id","".$update_id."",0);

			if($db_type==2 || $db_type==1 )
			   {
		        if($rID)
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
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("lib_fabric_group_entry",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==2 || $db_type==1 )
	   	{
            if($rID )
		    {
				oci_commit($con);   
				echo "2**".$rID;
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