<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="country_list_view")
{
	$arr=array (2=>$cut_up_array);
	 echo  create_list_view ( "list_view", "Country Name,Short Name,Zone", "100,100,70,80","450","220",0, "select id, country_name,region,short_name,cut_off,zone from lib_country where status_active=1 and is_deleted=0  order by country_name Asc", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,cut_off", "", "country_name,short_name,region,zone", "requires/country_entry_controller", 'setFilterGrid("list_view",-1);','0,0');
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, country_name, short_name,region,zone from lib_country where id='$data'  and status_active=1 and is_deleted=0" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_country_name').value = '".trim($inf[csf("country_name")])."';\n";  
		echo "document.getElementById('txt_short_name').value = '".trim($inf[csf("short_name")])."';\n";
		echo "document.getElementById('txt_region').value = '".trim($inf[csf("region")])."';\n";
		echo "document.getElementById('update_id').value			= '".($inf[csf("id")])."';\n"; 
		echo "document.getElementById('cbo_zone_name').value			= '".($inf[csf("zone")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_cut_off',1);\n";  
	}
}

if ($action=="save_update_delete")
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
		if(is_duplicate_field( "country_name", " lib_country", "LOWER(COUNTRY_NAME)=LOWER($txt_country_name) and is_deleted=0 " ) == 1)
		{
			echo "11**0"; die;
		}
				
 		$field_array="id,country_name,short_name,region,entry_form,status_active,inserted_by,insert_date,zone";
		$id = return_next_id( "id", "lib_country", 1 );
		$data_array="(".$id.",".$txt_country_name.",".$txt_short_name.",".$txt_region.",454,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_zone_name.")";
			
		
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
		if(is_duplicate_field( "country_name", " lib_country", "id!=$update_id and LOWER(country_name)=LOWER($txt_country_name) and is_deleted=0 " ) == 1)
		{
			echo "11**0"; die;
		}		 
		 //update code here
		$field_array_update="country_name*short_name*region*updated_by*update_date*zone";

				
	  	$data_array_update="".$txt_country_name."*".$txt_short_name."*".$txt_region."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_zone_name."";

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
		echo "19**";
		die;
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