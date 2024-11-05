<?php

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


//print_r($com_arr);die;
if ($action=="body_part_title_list_view")
{
	$com_arr=return_library_array( "select id, company_name from lib_company", 'id', 'company_name');
	$arr = array(0 => $com_arr);
	echo  create_list_view ( "list_view", "Company Name,Body Part Title", "225,225","500","220", 0, "select company_id,bundle_use_for,id from ppl_bundle_title where status_active=1 and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id, 0", $arr, "company_id,bundle_use_for", "../merchandising_details/requires/body_part_title_entry_controller", 'setFilterGrid("list_view",-1);' );
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select  id, company_id, bundle_use_for from ppl_bundle_title where status_active=1 and is_deleted=0 and id='$data'" );
	foreach ($nameArray as $inf)
	{		
		echo "document.getElementById('cbo_company_name').value = '".$inf[csf("company_id")]."';\n";
		echo "document.getElementById('txt_body_part_title_name').value  = '".($inf[csf("bundle_use_for")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_body_part_title',1);\n";
	}
}

if ($action=="save_update_delete")
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{	
		//$duplicate = is_duplicate_field("id","ppl_bundle_title","company_id=$cbo_company_name and bundle_use_for=$txt_body_part_title_name and is_deleted=0 ");

		if(is_duplicate_field("id","ppl_bundle_title","company_id=$cbo_company_name and bundle_use_for=$txt_body_part_title_name and is_deleted=0 ") == 1)
		{
			echo "11**0";die;
		}


		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id( "id", "ppl_bundle_title", 1 ) ;
		$field_array="id,company_id,bundle_use_for,inserted_by,insert_date,status_active,is_deleted";

		$data_array="(".$id.",".$cbo_company_name.",".$txt_body_part_title_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."', 1, 0)";

		//Insert Data in ppl_bundle_title Table----------------------------------------
		//echo "10**INSERT INTO ppl_bundle_title (".$field_array.") VALUES ".$data_array."";die;
		
		$rID=sql_insert("ppl_bundle_title",$field_array,$data_array,1);

		//----------------------------------------------------------------------------------

		if($db_type==0)
		{
			if($rID)
			{
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
			if($rID)
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

		if(is_duplicate_field("id","ppl_bundle_title","company_id=$cbo_company_name and bundle_use_for=$txt_body_part_title_name and is_deleted=0 and id !=$update_id") == 1)
		{
			echo "11**0";die;
		}

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="company_id*bundle_use_for*updated_by*update_date";

		$data_array="".$cbo_company_name."*".$txt_body_part_title_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo $data_array;die;

		$rID=sql_update("ppl_bundle_title", $field_array, $data_array, "id", $update_id, 1);
		//Update Data in ppl_bundle_title Table----------------------------------------

		
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
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
		   }
		disconnect($con);
		die;
	}
 //    else if ($operation==2)   // Delete Here
	// {

	// 	$con = connect();
	// 	if($db_type==0)
	// 	{
	// 		mysql_query("BEGIN");
	// 	}

	// 	$field_array="updated_by*update_date*status_active*is_deleted";
	//     $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";

	// 	$rID=sql_delete("ppl_bundle_title",$field_array,$data_array,"id","".$update_id."",1);

	// 	if($db_type==0)
	// 	{
	// 		if($rID )
	// 		  {
	// 			mysql_query("COMMIT");
	// 			echo "2**".$rID;
	// 		   }
	// 		else
	// 		  {
	// 			mysql_query("ROLLBACK");
	// 			echo "10**".$rID;
	// 		  }
	// 	}
	// 	if($db_type==2 || $db_type==1 )
	// 	   {
	//           if($rID )
	// 		    {
	// 				oci_commit($con);
	// 				echo "2**".$rID;
	// 			}
	// 			else{
	// 				oci_rollback($con);
	// 				echo "10**".$rID;
	// 			}
	// 	   }
	// 	disconnect($con);
	// 	die;
	// }

}
?>