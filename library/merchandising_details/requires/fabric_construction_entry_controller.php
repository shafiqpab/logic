<?php
/******************************************************************
|	Purpose			:	This form will create Fabric Construction Entry
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md Mamun Ahmed Sagor
|	Creation date 	:	26.05.2021
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
********************************************************************/
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
		
		$str_rep=array("<?","?>","_", "&", "*", "(", ")", "=","'","\r", "\n",'"','#');
		
		$txtfabconst=str_replace($str_rep,' ',$txt_fabric_construction_name);
		
		if (is_duplicate_field( "fabric_construction_name", "lib_fabric_construction", "fabric_construction_name='$txtfabconst' and status_active=1 and is_deleted=0" ) == 1)
		{
			echo "11**0";disconnect($con); die;
		}
		else
		{
			
			$id = return_next_id( "id", "lib_fabric_construction", 1 );
			$field_array="id,fabric_construction_name,status_active,inserted_by,insert_date";
			$data_array="(".$id.",'".$txtfabconst."',".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$rID=sql_insert("lib_fabric_construction",$field_array,$data_array,1);
		}
		
		//array data inserting here
		/*
		$field_array="id,fabric_construction_name,inserted_by,insert_date";
		$data_array="";
		$i=0;
		foreach($composition as $id=>$val)
		{
			$string = trim(preg_replace('/\s+/', ' ', $val));
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array.="$add_comma(".$id.",'".$string."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$i++;
		}
		$rID=sql_insert("lib_fabric_construction",$field_array,$data_array,1);
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
		
		$str_rep=array("<?","?>","_", "&", "*", "(", ")", "=","'","\r", "\n",'"','#');
		
		$txtfabconst=str_replace($str_rep,' ',$txt_fabric_construction_name);
		
		if (is_duplicate_field( "fabric_construction_name", "lib_fabric_construction", "fabric_construction_name='$txtfabconst' and status_active=1 and is_deleted=0 and id<>$update_id" ) == 1)
		{
			echo "11**0"; disconnect($con); die;
		}
		else
		{
			$field_array="fabric_construction_name*status_active*updated_by*update_date";
			$data_array="'".$txtfabconst."'*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("lib_fabric_construction",$field_array,$data_array,"id","".$update_id."",1);
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
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("lib_fabric_construction",$field_array,$data_array,"id","".$update_id."",1);

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
                echo  create_list_view ( "list_view", "Fabric Construction Name,Status", "400,100","550","220",0, "select id, fabric_construction_name, status_active from lib_fabric_construction where status_active in(1,2) and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,status_active", $arr, "fabric_construction_name,status_active", "requires/fabric_construction_entry_controller", 'setFilterGrid("list_view",-1);','0,0');
}

if($action == "is_used_fabric_construction"){
	$division=sql_select("SELECT fabric_construction_id from lib_yarn_count_determina_mst where fabric_construction_id='$data' and status_active=1 and is_deleted=0");
	

	if(count($division)>0 ){
		echo 1;
		exit();
	}
	else{
		echo 0;
		exit();
	}
}


if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, fabric_construction_name, status_active from lib_fabric_construction where id='$data'" );
	foreach ($nameArray as $inf)
	{
		$division=sql_select("SELECT fabric_construction_id from lib_yarn_count_determina_mst where fabric_construction_id='$data' and status_active=1 and is_deleted=0");

		if(count($division)>0 ){
			echo "$('#txt_fabric_construction_name').attr('disabled','disabled');\n";
			echo "$('#cbo_status').attr('disabled','disabled');\n";
		}
		else
		{
			echo "$('#txt_fabric_construction_name').removeAttr('disabled','disabled');\n";
			echo "$('#cbo_status').removeAttr('disabled','disabled');\n";
		}

		echo "document.getElementById('txt_fabric_construction_name').value = '".($inf[csf("fabric_construction_name")])."';\n";    
		echo "document.getElementById('cbo_status').value			= '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value			= '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_fabric_construction_entry',1);\n";  
	}
}

// if($action=="check_fabric_construction")
// {
// 	//$data=explode("**",$data);
	
// 	 $sql="select a.fabric_construction_id as fabric_construction_id from lib_yarn_count_determina_mst a where a.fabric_construction_id=$data and a.is_deleted=0 and a.status_active=1"; 
	
// 	$data_array=sql_select($sql);
// 	if(count($data_array)>0)
// 	{
// 		echo "1_";
// 	}
// 	else
// 	{
// 		echo "0_";
// 	}
// 	exit();	
// }

?>