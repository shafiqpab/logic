<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');

if($action == "is_used_department"){
	$division=sql_select("SELECT department_id from lib_style_ref where department_id='$data' and status_active=1 and is_deleted=0");
	

	if(count($division)>0 ){
		echo 1;
		exit();
	}
	else{
		echo 0;
		exit();
	}
}
if ($action=="load_drop_down_division")
{
	echo create_drop_down( "cbo_division_name", 150, "select id,division_name from lib_division_name where buyer_id='$data' and status_active =1 and is_deleted=0  order by division_name","id,division_name", 1, "-- Select --", $selected, "" );
	exit();
}

if ($action=="division_list_view")
{
		
		$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
		$division_arr=return_library_array( "select id,division_name from lib_division_name",'id','division_name');
		$arr=array (1=>$buyer_arr,2=>$division_arr);
		echo  create_list_view ( "list_view", "Department Name,Buyer,Division", "170,150,150","550","220",0, "select  department_name,buyer_id,id,division_id from  lib_department_name where is_deleted=0 order by department_name", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,buyer_id,division_id", $arr , "department_name,buyer_id,division_id", "../merchandising_details/requires/department_entry_controller", 'setFilterGrid("list_view",-1);' ) ;
}
if ($action=="load_php_data_to_form")
{

	$nameArray=sql_select( "select  department_name,buyer_id,id,division_id from  lib_department_name where is_deleted=0 and id='$data'" );
	foreach ($nameArray as $inf)
	{
		
	$division=sql_select("SELECT division from lib_style_ref where division='$data' and status_active=1 and is_deleted=0");

	if(count($division)>0 ){
		echo "$('#txt_department_name').attr('disabled','disabled');\n";
		echo "$('#cbo_buyer_name').attr('disabled','disabled');\n";
		echo "$('#cbo_division_name').attr('disabled','disabled');\n";
	}
	else
	{
		echo "$('#txt_department_name').removeAttr('disabled','disabled');\n";
		echo "$('#cbo_buyer_name').removeAttr('disabled','disabled');\n";
		echo "$('#cbo_division_name').removeAttr('disabled','disabled');\n";
	}
	
	
		echo "document.getElementById('txt_department_name').value = '".($inf[csf("department_name")])."';\n";
		echo "document.getElementById('cbo_buyer_name').value  = '".($inf[csf("buyer_id")])."';\n";
		echo "load_drop_down( 'requires/department_entry_controller','".$inf[csf("buyer_id")]."', 'load_drop_down_division', 'division_td' )\n";
		echo "document.getElementById('cbo_division_name').value  = '".($inf[csf("division_id")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_color_info',1);\n";
	}
}

if ($action=="save_update_delete")
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		if(is_duplicate_field( "department_name", " lib_department_name", "LOWER(department_name)=LOWER($txt_department_name) and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "lib_department_name", 1 ) ;
			$field_array="id,department_name,buyer_id,division_id,inserted_by,insert_date,status_active,is_deleted";
			$str_rep=array("_","/", "&", "*", "(", ")", "=","'",",","\r", "\n",'"','#');
			$txt_department_name=str_replace($str_rep,' ',$txt_department_name);
			$data_array="(".$id.",'".trim($txt_department_name)."',".$cbo_buyer_name.",".$cbo_division_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

			//Insert Data in lib_department_name_tag_buyer Table----------------------------------------

			
			
			$rID=sql_insert("lib_department_name",$field_array,$data_array,0);
			
			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}
			else if($db_type==2 || $db_type==1 )
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
		if(is_duplicate_field( "department_name", " lib_department_name", "id!=$update_id and LOWER(department_name)=LOWER($txt_department_name) and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
	
			$field_array="department_name*buyer_id*division_id*updated_by*update_date*status_active";
			$str_rep=array("_","/", "&", "*", "(", ")", "=","'",",","\r", "\n",'"','#');
			$txt_department_name=str_replace($str_rep,' ',$txt_department_name);
			$data_array="'".trim($txt_department_name)."'*".$cbo_buyer_name."*".$cbo_division_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			
			$rID=sql_update("lib_department_name",$field_array,$data_array,"id","".$update_id."",0);
			
			if($db_type==0)
			{
				if($rID )
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
			else if($db_type==2 || $db_type==1 )
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
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";

		$rID=sql_delete("lib_department_name",$field_array,$data_array,"id","".$update_id."",1);
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
		else if($db_type==2 || $db_type==1 )
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