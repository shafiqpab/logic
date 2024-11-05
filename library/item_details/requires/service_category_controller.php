<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
 
include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="search_list_view")
{
		$arr=array (4=>$row_status);
		echo  create_list_view ( "list_view,tbl_scroll_body","Service Code,Service Group,Service Category,Service Name,Status", "100,150,100,100,50","600","220",0,  "select id,service_code,service_group,service_category,service_name,status_active from lib_service_category where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,0,status_active", $arr,"service_code,service_group,service_category,service_name,status_active", "../item_details/requires/service_category_controller", 'setFilterGrid("list_view",-1);') ;
	 
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id,service_code,service_group,service_category,service_name,status_active from lib_service_category  where id='$data'" );
	

	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_service_code').value = '".($inf[csf("service_code")])."';\n";    
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('txt_service_group').value  = '".($inf[csf("service_group")])."';\n";
		echo "document.getElementById('txt_service_category').value  = '".($inf[csf("service_category")])."';\n";
		echo "document.getElementById('txt_service_name').value  = '".($inf[csf("service_name")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_service_cat_info',1);\n";  
	}
}

if ($action=="save_update_delete")
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$txt_service_code=str_replace("'", "", $txt_service_code);
	$txt_service_group=str_replace("'", "", $txt_service_group);
	$txt_service_category=str_replace("'", "", $txt_service_category);
	$txt_service_name=str_replace("'", "", $txt_service_name);
	
	if ($operation==0)  // Insert Here
	{
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		if (is_duplicate_field( "service_code", "*lib_service_category", "service_code=$txt_service_code and service_group=$txt_service_group and service_category=$txt_service_category and service_name=$txt_service_name and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			check_table_status( $_SESSION['menu_id'],1);
			$id=return_next_id( "id", "lib_service_category", 0 ) ;
			$field_array="id,service_code,service_group,service_category,service_name,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",'".$txt_service_code."','".$txt_service_group."','".$txt_service_category."','".$txt_service_name."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)"; 
			$rID=sql_insert("lib_service_category",$field_array,$data_array,1);

			check_table_status( $_SESSION['menu_id'],0);
			if($db_type==2 || $db_type==1 )
			{
				if($rID )
				{
					oci_commit($con);  
					echo "0**".$rID;
				}
			else
				{
					oci_rollback($con);
					echo "10**".$rID."**insert into lib_service_category($field_array)values".$data_array;
				}
			}
			disconnect($con);
			die;
		}
	}
	
	else if ($operation==1)   // Update Here
	{
		if (is_duplicate_field( "service_code","lib_service_category", "service_code=$txt_service_code and id!=$update_id and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
		
			$field_array="service_code*service_group*service_category*service_name*updated_by*update_date*status_active*is_deleted";
			$is_deleted=0;
			$data_array="'".$txt_service_code."'*'".$txt_service_group."'*'".$txt_service_category."'*'".$txt_service_name."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".str_replace("'","",$cbo_status)."*".$is_deleted."";
			$rID=sql_update("lib_service_category",$field_array,$data_array,"id","$update_id",0);
			//echo $rID ;
		
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
	
	
	else if ($operation==2)  
	{
		
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("lib_service_category",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".$rID;
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



?>