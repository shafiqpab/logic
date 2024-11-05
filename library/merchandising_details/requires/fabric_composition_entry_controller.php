<?php
/*******************************************************************
|	Purpose			:	This controller is for Composition Entry
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Nuruzzaman
|	Creation date 	:	05.07.2015
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
	 
		if (is_duplicate_field( "fabric_composition_name", "lib_fabric_composition", "fabric_composition_name=$txt_fabric_composition_name and status_active=1 and is_deleted=0" ) == 1)
		{
			echo "11**0";disconnect($con); die;
		}
		else
		{
			$id = return_next_id( "id", "lib_fabric_composition", 1 );
			
			$field_array="id,fabric_composition_name,entry_form,status_active,inserted_by,insert_date";
			$data_array="(".$id.",".$txt_fabric_composition_name.",710,".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$rID=sql_insert("lib_fabric_composition",$field_array,$data_array,1);

		}
		
		
		//array data inserting here
		/*
		$field_array="id,fabric_composition_name,inserted_by,insert_date";
		$data_array="";
		$i=0;
		foreach($composition as $id=>$val)
		{
			$string = trim(preg_replace('/\s+/', ' ', $val));
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array.="$add_comma(".$id.",'".$string."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$i++;
		}
		$rID=sql_insert("lib_fabric_composition",$field_array,$data_array,1);
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
		 
		if (is_duplicate_field( "fabric_composition_name", "lib_fabric_composition", "fabric_composition_name=$txt_fabric_composition_name and status_active=1 and is_deleted=0 and id<>$update_id" ) == 1)
		{
			echo "11**0"; disconnect($con); die;
		}
		else
		{
			$field_array="fabric_composition_name*status_active*updated_by*update_date";
			$data_array="".$txt_fabric_composition_name."*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("lib_fabric_composition",$field_array,$data_array,"id","".$update_id."",1);

			 
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
		$rID=sql_delete("lib_fabric_composition",$field_array,$data_array,"id","".$update_id."",1);


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

if($action == "is_used_fabric_composition"){
	$division=sql_select("SELECT fabric_composition_id from lib_yarn_count_determina_mst where fabric_composition_id='$data' and status_active=1 and is_deleted=0");
	

	if(count($division)>0 ){
		echo 1;
		exit();
	}
	else{
		echo 0;
		exit();
	}
}
if ($action=="fabric_composition_list_view")
{
	$arr=array (1=>$row_status);
                echo  create_list_view ( "list_view", "Fabric Composition Name,Status", "400,100","550","220",0, "select id, fabric_composition_name, status_active from lib_fabric_composition where status_active in(1,2) and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,status_active", $arr, "fabric_composition_name,status_active", "requires/fabric_composition_entry_controller", 'setFilterGrid("list_view",-1);','0,0');
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, fabric_composition_name, status_active from lib_fabric_composition where id='$data'" );
	foreach ($nameArray as $inf)
	{	
		
		$division=sql_select("SELECT fabric_composition_id from lib_yarn_count_determina_mst where fabric_composition_id='$data' and status_active=1 and is_deleted=0");

		if(count($division)>0 ){
			echo "$('#txt_fabric_composition_name').attr('disabled','disabled');\n";
			echo "$('#cbo_status').attr('disabled','disabled');\n";
		}
		else
		{
			echo "$('#txt_fabric_composition_name').removeAttr('disabled','disabled');\n";
			echo "$('#cbo_status').removeAttr('disabled','disabled');\n";
		}


		echo "document.getElementById('txt_fabric_composition_name').value = '".($inf[csf("fabric_composition_name")])."';\n";    
		echo "document.getElementById('cbo_status').value			= '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value			= '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_fabric_composition_entry',1);\n";  
	}
}

// if($action=="check_fabric_composition")
// {
// 	//$data=explode("**",$data);
	
// 	 $sql="select a.fabric_composition_id as fabric_composition_id from lib_yarn_count_determina_mst a where a.fabric_composition_id=$data and a.is_deleted=0 and a.status_active=1"; 
	
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