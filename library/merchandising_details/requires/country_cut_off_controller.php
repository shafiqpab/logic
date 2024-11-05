<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="cutoff_list_view")
{
	$arr=array (2=>$cut_up_array);
	 echo  create_list_view ( "list_view", "Country Name,Short Name,Cut Off", "150,150","550","220",0, "select id, country_name,short_name,cut_off from lib_country where status_active=1 and is_deleted=0 order by country_name Asc", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,cut_off", $arr, "country_name,short_name,cut_off", "requires/country_cut_off_controller", 'setFilterGrid("list_view",-1);','0,0');
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, country_name, cut_off from lib_country where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_Country_id').value = '".($inf[csf("country_name")])."';\n";  
		echo "document.getElementById('cbo_cutOff_id').value = '".($inf[csf("cut_off")])."';\n";   
		//echo "document.getElementById('cbo_status').value			= '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value			= '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_cut_off',1);\n";  
	}
}

if ($action=="save_update_delete_cutoff")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	 
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
				
 		$field_array="id,cut_off,status_active,inserted_by,insert_date";
		$id = return_next_id( "id", "lib_country", 1 );
		$data_array="(".$id.",".$cbo_cutOff_id.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
		
		$rID=sql_insert("lib_country",$field_array,$data_array,1);
		//echo "5**0**insert into lib_country (".$field_array.") values ".$data_array;die;
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
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);  
				echo "0**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
				 
		 //update code here
		$field_array_update="cut_off*updated_by*update_date";

				
	  	$data_array_update="".$cbo_cutOff_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("lib_country",$field_array_update,$data_array_update,"id","".$update_id."",1);
			
		
		if($db_type==0)
		{
			if($rID==1)
			{
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $txt_system_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
	
	
		else if ($operation==2)   // Delete Here
	{
		
	  		$con = connect();
	  		if($db_type==0)
	  		{
	  			mysql_query("BEGIN");
	  		}
	  		
	  		$field_array="updated_by*update_date*status_active*is_deleted";
	  	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
	  		$rID=sql_delete("lib_country",$field_array,$data_array,"id","".$id."",1);

		  		if($db_type==0)
			{
				if( $rID)
				{
					mysql_query("COMMIT");  
					echo "2**".$rID;
				}
				
				else
				{
					mysql_query("ROLLBACK"); 
					echo "6**".$rID;
				}
			}
			if($db_type==2 || $db_type==1)
			{
				if( $rID)
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