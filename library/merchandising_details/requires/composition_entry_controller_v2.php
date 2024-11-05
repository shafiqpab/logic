<?php
/*******************************************************************
|	Purpose			:	This form will create Composition Entry
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Helal Uddin
|	Creation date 	:	23-02-2021
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
		
		if (is_duplicate_field( "composition_name", "lib_composition_array", "composition_name=$txt_composition_name and status_active=1 and is_deleted=0" ) == 1)
		{
			echo "11**0";disconnect($con); die;
		}
		else
		{
			//cbo_yarn_fibre_type*cbo_yarn_fibre*cbo_yarn_type*cbo_yarn_spinning_system
			
			$id = return_next_id( "id", "lib_composition_array", 1 );
			$field_array="id,composition_name,yarn_fibre,yarn_fibre_type,entry_form,status_active,inserted_by,insert_date";
			$data_array="(".$id.",".$txt_composition_name.",".$cbo_yarn_fibre.",".$cbo_yarn_fibre_type.",709,".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$rID=sql_insert("lib_composition_array",$field_array,$data_array,1);
		}
		
		
		
		//array data inserting here
		/*
		$field_array="id,composition_name,inserted_by,insert_date";
		$data_array="";
		$i=0;
		foreach($composition as $id=>$val)
		{
			$string = trim(preg_replace('/\s+/', ' ', $val));
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array.="$add_comma(".$id.",'".$string."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$i++;
		}
		$rID=sql_insert("lib_composition_array",$field_array,$data_array,1);
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
		
		if (is_duplicate_field( "composition_name", "lib_composition_array", "composition_name=$txt_composition_name and status_active=1 and is_deleted=0 and id<>$update_id" ) == 1)
		{
			echo "11**0"; disconnect($con); die;
		}
		else
		{
			

			$field_array="composition_name*yarn_fibre*yarn_fibre_type*status_active*updated_by*update_date";
			$data_array="".$txt_composition_name."*".$cbo_yarn_fibre."*".$cbo_yarn_fibre_type."*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("lib_composition_array",$field_array,$data_array,"id","".$update_id."",1);
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
		$rID=sql_delete("lib_composition_array",$field_array,$data_array,"id","".$update_id."",1);

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

if ($action=="composition_list_view")
{
	$arr=array (1=>$yarn_fibre_arr,2=>$yarn_fibre_type_arr,3=>$row_status);
    echo  create_list_view ( "list_view", "Composition Name,Yarn Fibre,Fibre Type,Status", "350,100,100,100","750","220",0, "select id, composition_name, status_active , yarn_fibre,yarn_fibre_type from lib_composition_array where status_active in(1,2) and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,yarn_fibre,yarn_fibre_type,status_active", $arr, "composition_name,yarn_fibre,yarn_fibre_type,status_active", "requires/composition_entry_controller_v2", 'setFilterGrid("list_view",-1);','0,0');
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, composition_name, status_active,yarn_spinning_system ,yarn_type, yarn_fibre,yarn_fibre_type from lib_composition_array where id='$data'" );
	
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_composition_name').value = '".($inf[csf("composition_name")])."';\n";    
		echo "document.getElementById('cbo_status').value			= '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('cbo_yarn_fibre_type').value			= '".($inf[csf("yarn_fibre_type")])."';\n";
		echo "document.getElementById('cbo_yarn_fibre').value			= '".($inf[csf("yarn_fibre")])."';\n";
		echo "document.getElementById('update_id').value			= '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_composition_entry',1);\n";  
	}
}

if($action=="check_composition")
{
	//$data=explode("**",$data);
	
	 $sql="select a.copmposition_id as copmposition_id from lib_yarn_count_determina_dtls a where a.copmposition_id=$data and a.is_deleted=0 and a.status_active=1"; 
	
	$data_array=sql_select($sql);
	if(count($data_array)>0)
	{
		echo "1_";
	}
	else
	{
		echo "0_";
	}
	exit();	
}
?>