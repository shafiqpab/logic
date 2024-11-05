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
		
		if (is_duplicate_field( "buyer_id", "buyer_wise_penalty_point", " buyer_id=$cbo_buyer_id and defect_name=$cbo_defect_name and  inch=$cbo_inch and  status_active=1 and is_deleted=0" ) == 1)
		{
			echo "11**0"; disconnect($con); die;
		}
		else
		{
			
			$id=return_next_id( "id", "buyer_wise_penalty_point", 1 ) ;
			$field_array="id,buyer_id, defect_name,inch,penalty_point,inserted_by,insert_date, status_active, is_deleted";
			$data_array="(".$id.",".$cbo_buyer_id.",".$cbo_defect_name.",".$cbo_inch.",".$txt_penalty_point.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$rID=sql_insert("buyer_wise_penalty_point",$field_array,$data_array,1);
			//echo "10**insert into buyer_wise_penalty_point (".$field_array .") values ".$data_array ;die;

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
		if (is_duplicate_field( "buyer_id", "buyer_wise_penalty_point", " buyer_id=$cbo_buyer_id and defect_name=$cbo_defect_name and  inch=$cbo_inch and status_active=1 and is_deleted=0 and id <> $update_id " ) == 1)
		{
			echo "11**0"; disconnect($con); die;
		}
		else
		{

			 
			$field_array="buyer_id*defect_name*inch*penalty_point*updated_by*update_date";
			$data_array="".$cbo_buyer_id."*".$cbo_defect_name."*".$cbo_inch."*".$txt_penalty_point."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("buyer_wise_penalty_point",$field_array,$data_array,"id","".$update_id."",1);
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
	else if ($operation==2)   // Update Here
	{		
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("buyer_wise_penalty_point",$field_array,$data_array,"id","".$update_id."",1);
		
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

if ($action=="penalty_list_view")
{ 
	$buyer_arr = return_library_array("select id, buyer_name from  lib_buyer", "id", "buyer_name");
	$defect_arr = return_library_array("select defect_name, short_name from  lib_defect_name", "defect_name", "defect_name");
	//$defect_short_arr = return_library_array("select defect_name, short_name from  lib_defect_name", "defect_name", "short_name");

	$arr=array(0=>$buyer_arr,1=>$defect_arr,2=>$knit_defect_inchi_array);
	echo  create_list_view ( "list_view", "Buyer Name,Defect Name,Found in(Inch),Point", "150,100,100,50","600","220",1, "SELECT id, buyer_id, defect_name, inch, penalty_point from buyer_wise_penalty_point where status_active=1 and is_deleted=0 order by id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "buyer_id,defect_name,inch,0", $arr , "buyer_id,defect_name,inch,penalty_point", "../production/requires/buyer_wise_penalty_point_controller", 'setFilterGrid("list_view",-1);' ) ;
                	 	
}

if ($action=="load_php_data_to_form")//load list view data to the form
{
	$nameArray=sql_select( "SELECT  id, buyer_id, defect_name, inch, penalty_point  from buyer_wise_penalty_point where id='$data'" );
	foreach ($nameArray as $inf)
	{
		 
		echo "document.getElementById('cbo_buyer_id').value = '".($inf[csf("buyer_id")])."';\n";    
		echo "document.getElementById('cbo_defect_name').value  = '".($inf[csf("defect_name")])."';\n"; 
 		echo "document.getElementById('cbo_inch').value  = '".($inf[csf("inch")])."';\n";
		echo "document.getElementById('txt_penalty_point').value  = '".($inf[csf("penalty_point")])."';\n";		 
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_buyer_penalty_entry',1);\n";  
	}
}


?>