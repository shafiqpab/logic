<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
 
if ($action=="wages_rate_list_view")
{
	 $arr=array(1=>$wages_rate_var_for,2=>$row_status);
     echo  create_list_view ( "list_view", "Variable Name,Applciable For,Status", "150,200","500","220",1, "select wages_rate_variable_name,wages_rate_variable_for,id,status_active from  lib_wages_rate_variable where is_deleted=0", "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,wages_rate_variable_for,status_active", $arr , "wages_rate_variable_name,wages_rate_variable_for,status_active", "../production/requires/wages_rate_controller", 'setFilterGrid("list_view",-1);' ) ;

}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select wages_rate_variable_name,wages_rate_variable_for,id,status_active from  lib_wages_rate_variable where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_variable_name').value = '".($inf[csf("wages_rate_variable_name")])."';\n";    
		echo "document.getElementById('cbo_variable_for').value  = '".($inf[csf("wages_rate_variable_for")])."';\n"; 
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_wages_rate_var',1);\n";  

	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "wages_rate_variable_name", "lib_wages_rate_variable", " wages_rate_variable_name=$txt_variable_name and wages_rate_variable_for=$cbo_variable_for and is_deleted=0" ) == 1)
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
			//cbo_company_name,cbo_location_name,cbo_floor_name,txt_sewing_line_serial,txt_line_name,cbo_status,update_id
			//company_name,location_name,floor_name,sewing_line_serial,line_name,status_active,id
			
			$id=return_next_id( "id", " lib_wages_rate_variable", 1 ) ;
			$field_array="id,wages_rate_variable_name,wages_rate_variable_for,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$txt_variable_name.",".$cbo_variable_for.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
			$rID=sql_insert("lib_wages_rate_variable",$field_array,$data_array,1);
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
		if (is_duplicate_field( "wages_rate_variable_name", "lib_wages_rate_variable", " wages_rate_variable_name=$txt_variable_name and wages_rate_variable_for=$cbo_variable_for and  id!=$update_id and is_deleted=0" ) == 1)
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
			
			$field_array="wages_rate_variable_name*wages_rate_variable_for*updated_by*update_date*status_active*is_deleted";
			$data_array="".$txt_variable_name."*".$cbo_variable_for."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";
			
			 
			$rID=sql_update("lib_wages_rate_variable",$field_array,$data_array,"id","".$update_id."",1);
			 
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
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("lib_wages_rate_variable",$field_array,$data_array,"id","".$update_id."",1);
		
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