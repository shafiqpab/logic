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
			 
			$id=return_next_id( "id", "company_loc_flr_line_count", 1 ) ;
			$duplicate_chk=sql_select("SELECT id from company_loc_flr_line_count where  status_active=1 ");			 
			if(count($duplicate_chk)>0){echo "420**420";disconnect($con);die;}
			$field_array="id,company_count, location_count, floor_count, line_count,inserted_by,insert_date, status_active, is_deleted";
			$data_array="(".$id.",".$txt_company_count.",".$txt_location_count.",".$txt_floor_count.",".$txt_line_count.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$rID=sql_insert("company_loc_flr_line_count",$field_array,$data_array,1);
			if($db_type==0)
			{
				if($rID  ){
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
				 if($rID  )
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
			 
			$field_array="company_count*location_count*floor_count*line_count*updated_by*update_date";
			$data_array="".$txt_company_count."*".$txt_location_count."*".$txt_floor_count."*".$txt_line_count."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			 
			$rID=sql_update("company_loc_flr_line_count",$field_array,$data_array,"id","".$update_id."",1);
			 
			if($db_type==0)
			{
				if($rID  ){
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
			 if($rID  )
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
	else if ($operation==2)   // Update Here
	{
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("company_loc_flr_line_count",$field_array,$data_array,"id","".$update_id."",1);
		 
		
		if($db_type==0)
		{
			if($rID  ){
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
			 if($rID  )
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

if ($action=="details_list_view")
{ 
	 
	//$arr=array(0=>$buyer_arr, 3=>$fabric_shade);
	$arr=array();
	echo  create_list_view ( "list_view", "Company Count,Location Count,Floor Count,Line Count", "150,100,100,50","600","220",1, "SELECT id,company_count, location_count, floor_count, line_count  from company_loc_flr_line_count where  status_active=1 and is_deleted=0 order by id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,0,0,0", $arr , "company_count,location_count,floor_count,line_count", "../production/requires/company_location_count_controller", 'setFilterGrid("list_view",-1);' ) ;
                	 	
}

if ($action=="load_php_data_to_form")//load list view data to the form
{
	$nameArray=sql_select( "SELECT id,company_count, location_count, floor_count, line_count  from company_loc_flr_line_count where id='$data'" ); 
	foreach ($nameArray as $inf)
	{ 
		 
		echo "document.getElementById('txt_company_count').value = '".($inf[csf("company_count")])."';\n";    
		echo "document.getElementById('txt_location_count').value  = '".($inf[csf("location_count")])."';\n"; 
 		echo "document.getElementById('txt_floor_count').value  = '".($inf[csf("floor_count")])."';\n";
		echo "document.getElementById('txt_line_count').value  = '".($inf[csf("line_count")])."';\n";		 
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_count_entry',1);\n";  
	}
}


?>