<?php
/*******************************************************************
|	Purpose			:	This controller is for Composition Entry
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Helal Uddin
|	Creation date 	:	09-08-2021
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
*********************************************************************/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="save_update_delete")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)//Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if (is_duplicate_field( "fabric_type_name", "lib_fabric_type", "fabric_type_name=$txt_fabric_type_name and status_active=1 and is_deleted=0" ) == 1)
		{
			echo "11**0";disconnect($con); die;
		}
		else
		{
			$id = return_next_id( "id", "lib_fabric_type", 1 );
			$field_array="id,fabric_type_name,status_active,inserted_by,insert_date";
			$data_array="(".$id.",".$txt_fabric_type_name.",".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$rID=sql_insert("lib_fabric_type",$field_array,$data_array,1);
		}
		
		
		
		//array data inserting here
		/*
		$field_array="id,fabric_type_name,inserted_by,insert_date";
		$data_array="";
		$i=0;
		foreach($composition as $id=>$val)
		{
			$string = trim(preg_replace('/\s+/', ' ', $val));
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array.="$add_comma(".$id.",'".$string."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$i++;
		}
		$rID=sql_insert("lib_fabric_type",$field_array,$data_array,1);
		*/
		
		//----------------------------------------------------------------------------------
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "0**".$rID;
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
		
	else if ($operation==1)//Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if (is_duplicate_field( "fabric_type_name", "lib_fabric_type", "fabric_type_name=$txt_fabric_type_name and status_active=1 and is_deleted=0 and id<>$update_id" ) == 1)
		{
			echo "11**0"; disconnect($con); die;
		}
		else
		{
			$field_array="fabric_type_name*status_active*updated_by*update_date";
			$data_array="".$txt_fabric_type_name."*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("lib_fabric_type",$field_array,$data_array,"id","".$update_id."",1);
		}
	
		//----------------------------------------------------------------------------------
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
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

	else if ($operation==2)//Delete Here
	{
		$sql=sql_select("select * from lib_yarn_count_determina_mst where construction_type=$update_id");
		if(count($sql))
		{
			echo "121**$update_id";
			die;
		}
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("lib_fabric_type",$field_array,$data_array,"id","".$update_id."",1);

		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else
			{
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

if ($action=="fabric_construction_list_view")
{
	$arr=array (1=>$row_status);
                echo  create_list_view ( "list_view", "Fabric construction Name,Status", "400,100","550","220",0, "select id, fabric_type_name, status_active from lib_fabric_type where status_active in(1,2) and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,status_active", $arr, "fabric_type_name,status_active", "requires/fabric_type_entry_controller", 'setFilterGrid("list_view",-1);','0,0');
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, fabric_type_name, status_active from lib_fabric_type where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_fabric_type_name').value = '".($inf[csf("fabric_type_name")])."';\n";    
		echo "document.getElementById('cbo_status').value			= '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value			= '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_fabric_type_entry',1);\n";  
	}
}


?>