<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="search_list_view")
{
	$arr=array (0=>$conversion_cost_head_array,2=>$row_status);
	echo  create_list_view ( "list_view", "Cost Component,Rate,Status", "200,80,120","470","220",0, "select  cost_component_name,rate,status_active,id from  lib_cost_component where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "cost_component_name,0,status_active", $arr , "cost_component_name,rate,status_active", "../merchandising_details/requires/cost_component_rate_controller", 'setFilterGrid("list_view",-1);' ) ;

}


//cbo_cost_component,txt_component_rate,cbo_status
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select cost_component_name,rate,status_active,id from lib_cost_component where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_cost_component').value  = '".($inf[csf("cost_component_name")])."';\n";    
		echo "document.getElementById('txt_component_rate').value  = '".($inf[csf("rate")])."';\n"; 
		echo "document.getElementById('cbo_status').value 		   = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value  		   = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_costcomponent_info',1);\n";  

	}
}



if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "cost_component_name", "lib_cost_component", "cost_component_name=$cbo_cost_component and is_deleted=0" ) == 1)
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
			$id=return_next_id( "id", "lib_cost_component", 1 );
			$field_array="id,cost_component_name,rate,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$cbo_cost_component.",".$txt_component_rate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
			$rID=sql_insert("lib_cost_component",$field_array,$data_array,1);
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
		
		if (is_duplicate_field( "cost_component_name", "lib_cost_component", "cost_component_name=$cbo_cost_component and id!=$update_id and is_deleted=0" ) == 1)
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
			
			$field_array="cost_component_name*rate*updated_by*update_date*status_active";
			$data_array="".$cbo_cost_component."*".$txt_component_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."";
			
			$rID=sql_update("lib_cost_component",$field_array,$data_array,"id","".$update_id."",1);
			//echo $rID; die;
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
		
		$rID=sql_delete("lib_cost_component",$field_array,$data_array,"id","".$update_id."",1);
		
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

?>
