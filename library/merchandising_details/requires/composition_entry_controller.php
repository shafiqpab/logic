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
		//echo "Check Fabric".$chk_fabric;   // is_fabric = 1; not checked =2
		//die();
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
			$id = return_next_id( "id", "lib_composition_array", 1 );
			$field_array="id,composition_name,comp_short_name,yarn_category_type,entry_form,status_active,is_fabric,inserted_by,insert_date";
			$data_array="(".$id.",".$txt_composition_name.",".$txt_composition_short_name.",".$cbo_yarn_type.",708,".$cbo_status.",".$chk_fabric.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
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
		 //echo "10**insert into lib_composition_array (".$field_array.") values ".$data_array."<br>";die;
		// disconnect($con);
		// die;

		//echo "10**".$rID;die;

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
			$field_array="composition_name*comp_short_name*yarn_category_type*status_active*is_fabric*updated_by*update_date";
			$data_array="".$txt_composition_name."*".$txt_composition_short_name."*".$cbo_yarn_type."*".$cbo_status."*".$chk_fabric."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("lib_composition_array",$field_array,$data_array,"id","".$update_id."",1);
		}
		//echo "10**".$rID;die;
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
//disable for issue id==17146 nz requirement
/* if($action == "is_used_composition"){
	$division=sql_select("SELECT copmposition_id from lib_yarn_count_determina_dtls where copmposition_id='$data' and status_active=1 and is_deleted=0");
	

	if(count($division)>0 ){
		echo 1;
		exit();
	}
	else{
		echo 0;
		exit();
	}
} */

if ($action=="composition_list_view")
{
	$arr=array (1=>$yarn_type_for_entry,2=>$row_status,3=>$yes_no);
	echo  create_list_view ( "list_view", "Composition Name,Category,Status,Fabric/Yarn Status", "400,100,100,100","750","220",0, "select id, composition_name,yarn_category_type, status_active,is_fabric from lib_composition_array where status_active in(1,2) and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,yarn_category_type,status_active,is_fabric", $arr, "composition_name,yarn_category_type,status_active,is_fabric", "requires/composition_entry_controller", 'setFilterGrid("list_view",-1);','0,0,0');
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, composition_name,comp_short_name, yarn_category_type, status_active,is_fabric from lib_composition_array where id='$data'" );
	foreach ($nameArray as $inf)
	{
		$division=sql_select("SELECT copmposition_id from lib_yarn_count_determina_dtls where copmposition_id='$data' and status_active=1 and is_deleted=0");

	if(count($division)>0 ){
		echo "$('#txt_composition_name').attr('disabled','disabled');\n";
		//echo "$('#txt_composition_short_name').attr('disabled','disabled');\n";
		echo "$('#cbo_yarn_type').attr('disabled','disabled');\n";
        echo "$('#cbo_status').attr('disabled','disabled');\n";
	}
	else
	{
		echo "$('#txt_composition_name').removeAttr('disabled','disabled');\n";
		echo "$('#txt_composition_short_name').removeAttr('disabled','disabled');\n";
		echo "$('#cbo_yarn_type').removeAttr('disabled','disabled');\n";
        echo "$('#cbo_status').removeAttr('disabled','disabled');\n";
	}

	if($inf[csf("is_fabric")]==1)
	{
		echo "$('#chk_fabric').attr('checked',true);\n";
	}
	else
	{
		echo "$('#chk_fabric').attr('checked',false);\n";
	}
	
		echo "document.getElementById('txt_composition_name').value = '".($inf[csf("composition_name")])."';\n";    
		echo "document.getElementById('txt_composition_short_name').value = '".($inf[csf("comp_short_name")])."';\n";    
		echo "document.getElementById('cbo_yarn_type').value = '".($inf[csf("yarn_category_type")])."';\n";    
		echo "document.getElementById('cbo_status').value			= '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('chk_fabric').value			= '".($inf[csf("is_fabric")])."';\n";
		echo "document.getElementById('update_id').value			= '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_composition_entry',1);\n";  
	}
}

// if($action=="check_composition")
// {
// 	//$data=explode("**",$data);
	
// 	 $sql="select a.copmposition_id as copmposition_id from lib_yarn_count_determina_dtls a where a.copmposition_id=$data and a.is_deleted=0 and a.status_active=1"; 
	
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