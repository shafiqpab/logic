<?php

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


//print_r($com_arr);die;
if ($action=="batcher_entry_list_view")
{
	 $style_type=array(1=>"Solid",2=>"Stripe",3=>"Printed",4=>"Embroidery",5=>"Print & Embroidery",6=>"Lay Wash",7=>"Stripe & Printed"); 

	 $cutting_no=array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5",6=>"6",7=>"7",8=>"8",9=>"9",10=>"10",11=>"11",12=>"12",13=>"13",14=>"14",15=>"15",16=>"16",17=>"17",18=>"18",19=>"19",20=>"20"); 

	$arr = array(0 => $style_type,1 => $cutting_no);
	echo  create_list_view ( "list_view", "Style Types,Cutting Will Start Before,Cutting Target Over Plan", "180,120,200","500","220", 0, "select style_id,cutting_id,cutting_target,id from lib_fabric_cutting_planning where status_active=1 and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "style_id,cutting_id, 0", $arr, "style_id,cutting_id,cutting_target", "../planning/requires/fabric_cutting_planning_controller", 'setFilterGrid("list_view",-1);' );
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select  id, style_id, cutting_id,cutting_target from lib_fabric_cutting_planning where status_active=1 and is_deleted=0 and id='$data' ");
//  echo "select  id, style_id, cutting_id,cutting_target from lib_fabric_cutting_planning where status_active=1 and is_deleted=0 and id='$data'";die();
    
	foreach ($nameArray as $inf)
	{		
		echo "document.getElementById('cbo_style_type').value = '".$inf[csf("style_id")]."';\n";
		echo "document.getElementById('cbo_cutting_no').value = '".$inf[csf("cutting_id")]."';\n";
		echo "document.getElementById('txt_cutting_target').value  = '".($inf[csf("cutting_target")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_batcher_entry',1);\n";
	}
}

// if ($action=="load_drop_down_location")
// {
// 	 echo create_drop_down( "cbo_location_id", 160, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );		 
// }



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

			$duplicate = is_duplicate_field("id","lib_fabric_cutting_planning","style_id=$cbo_style_type and status_active=1");
			if($duplicate==1)
			{
				echo "11**duplicate";
				disconnect($con);
				die;
			}

	 
			// if(is_duplicate_field("id","lib_fabric_cutting_planning","style_id=$cbo_style_type and cutting_id='$cbo_cutting_no' and status_active=1 ") == 1)
			// {
			// 	echo "11**0";die;
			// }


			 
			$id=return_next_id( "id", "lib_fabric_cutting_planning", 1 ) ;
			$field_array="id,style_id,cutting_id,cutting_target,inserted_by,insert_date";

			$data_array="(".$id.",".$cbo_style_type.",".$cbo_cutting_no.",".$txt_cutting_target.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//Insert Data in lib_batcher Table----------------------------------------
			//echo "10**INSERT INTO lib_fabric_cutting_planning (".$field_array.") VALUES ".$data_array."";die;
			
			$rID=sql_insert("lib_fabric_cutting_planning",$field_array,$data_array,1);

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

			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$field_array="style_id*cutting_id*cutting_target*updated_by*update_date";
           
			$data_array="".$cbo_style_type."*".$cbo_cutting_no."*".$txt_cutting_target."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			$rID=sql_update("lib_fabric_cutting_planning", $field_array, $data_array, "id", $update_id, 1);
		 
	 
		
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
    else if ($operation==2)   // Delete Here
	{

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";

		$rID=sql_delete("lib_fabric_cutting_planning",$field_array,$data_array,"id","".$update_id."",1);

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
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
		   }
		disconnect($con);
		die;
	}

}
?>