<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
 
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	if ($operation==0)  // Insert Here
	{
		
		if (is_duplicate_field( "id", "lib_defect_name", "defect_name=$txt_defect_name and  status_active=1 and is_deleted=0" ) == 1)
		{
			echo "11**0"; disconnect($con); die;
		}
		else
		{
			
			$id=return_next_id( "id", "lib_defect_name", 1 ) ;
			$field_array="id, defect_name,short_name,type,sequence_no,inserted_by,insert_date, status_active, is_deleted";
			$data_array="(".$id.",".$cbo_defect_name.",".$txt_defect_short_name.",".$cbo_defect_type.",".$txt_sequence.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
			$rID=sql_insert("lib_defect_name",$field_array,$data_array,1);
			//echo "10**insert into lib_defect_name (".$field_array .") values ".$data_array ;die;

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
	else if ($operation==1)   // Update Here
	{
		if (is_duplicate_field( "id", "lib_defect_name", "defect_name=$cbo_defect_name and status_active=1 and is_deleted=0 and id <> $update_id " ) == 1)
		{
			echo "11**0"; disconnect($con); die;
		}
		else
		{

			 
			$field_array="defect_name*short_name*type*sequence_no*updated_by*update_date";
			$data_array="".$cbo_defect_name."*".$txt_defect_short_name."*".$cbo_defect_type."*".$txt_sequence."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("lib_defect_name",$field_array,$data_array,"id","".$update_id."",1);
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
	else if ($operation==2)   // Update for delete Here
	{

		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("lib_defect_name",$field_array,$data_array,"id","".$update_id."",1);
		
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
if ($action=="load_drop_down_defect_name")
{ 
	if ($data==3){
		echo create_drop_down( "cbo_defect_name", 153, $knit_defect_array,"", 1, '--Select--' );
		exit();
	}else{
		echo create_drop_down( "cbo_defect_name", 153, $finish_qc_defect_array,"", 1,'--Select--' );
		exit();
	}
}

if ($action=="penalty_list_view")
{ 
	$type_arr=array(1=>'Finish Fabric Defect',2=>'Finish Fabric Observation',3=>'Grey Fabric Defect');
	if($type_arr==3){
	   $arr = array(0 => $knit_defect_array,3 => $type_arr, 4=> $row_status);
	   echo  create_list_view ( "list_view", "Defect Name,Defect Short Name,Sequence,Type,Status", "120,100,80,150,100","600","150",1, "SELECT id,   sequence_no,defect_name, short_name,status_active,type  from lib_defect_name where is_deleted=0 order by id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "defect_name,0,0,type,status_active", $arr , "defect_name,short_name,sequence_no,type,status_active", "../production/requires/defect_name_entry_controller", 'setFilterGrid("list_view",-1);' ) ;
	   
	}else{
	   $arr = array(0 => $finish_qc_defect_array,3 => $type_arr, 4=> $row_status);
	   echo  create_list_view ( "list_view", "Defect Name,Defect Short Name,Sequence,Type,Status", "120,100,80,150,100","600","150",1, "SELECT id,   sequence_no,defect_name, short_name,status_active,type  from lib_defect_name where is_deleted=0 order by id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "defect_name,0,0,type,status_active", $arr , "defect_name,short_name,sequence_no,type,status_active", "../production/requires/defect_name_entry_controller", 'setFilterGrid("list_view",-1);' ) ; 
	   
	}
	
}

if ($action=="load_php_data_to_form")//load list view data to the form
{
	$nameArray=sql_select( "SELECT  id, sequence_no,defect_name,status_active,   short_name,type  from lib_defect_name where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "load_drop_down( 'requires/defect_name_entry_controller', '".$row[csf("defect_name")]."', 'load_drop_down_defect_name', 'defect_name_td');\n";
 		echo "document.getElementById('cbo_defect_name').value  = '".($inf[csf("defect_name")])."';\n"; 
		echo "document.getElementById('txt_defect_short_name').value  = '".($inf[csf("short_name")])."';\n";
		echo "document.getElementById('cbo_defect_type').value  = '".($inf[csf("type")])."';\n";
 		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_defect_entry',1);\n";  
		echo "document.getElementById('txt_sequence').value  = '".($inf[csf("sequence_no")])."';\n";
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
	}
}


?>