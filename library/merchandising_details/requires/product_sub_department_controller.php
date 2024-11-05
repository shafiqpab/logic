<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="sub_department_list_view")
{
	$arr=array (1=>$product_dept);
							echo  create_list_view ( "list_view", "Sub Department Name,Department, Buyer", "200,200,150","620","220",1, "
SELECT a.sub_department_name, a.department_id, b.buyer_name, a.id FROM lib_pro_sub_deparatment a, lib_buyer b WHERE a.buyer_id = b.id AND a.status_active =1 AND a.is_deleted =0", "get_php_form_data", "id", "'load_php_data_to_form'", 1,"0,department_id,0", $arr, "sub_department_name,department_id,buyer_name", "../merchandising_details/requires/product_sub_department_controller", 'setFilterGrid("list_view",-1);',''); 
}


if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "SELECT sub_department_name, department_id,buyer_id, id FROM lib_pro_sub_deparatment WHERE id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_sub_dep_name').value = '".($inf[csf("sub_department_name")])."';\n";    
		echo "document.getElementById('cbo_department_id').value  = '".($inf[csf("department_id")])."';\n";
		echo "document.getElementById('cbo_buyer_id').value = ".($inf[csf("buyer_id")]).";\n";    
		echo "document.getElementById('update_id').value = ".($inf[csf("id")]).";\n";    
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_product_department',1);\n";  
	}
}


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here============================================
	{
		if (is_duplicate_field( "sub_department_name", "lib_pro_sub_deparatment", "sub_department_name=$txt_sub_dep_name and buyer_id=$cbo_buyer_id and department_id=$cbo_department_id  and is_deleted=0" ) == 1)
		{
			echo 11; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
	
	$id=return_next_id( "id", "lib_pro_sub_deparatment", 1 ) ;
	$field_array="id,sub_department_name,department_id,buyer_id,inserted_by,insert_date,status_active,is_deleted";
	$data_array="(".$id.",".$txt_sub_dep_name.",".$cbo_department_id.",".$cbo_buyer_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
	
	$rID=sql_insert("lib_pro_sub_deparatment",$field_array,$data_array,1);
			
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
	
	else if ($operation==1)   // Update Here=========================================================
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			if (is_duplicate_field( "sub_department_name", "lib_pro_sub_deparatment", "sub_department_name=$txt_sub_dep_name and buyer_id=$cbo_buyer_id and department_id=$cbo_department_id  and is_deleted=0 and id <> $update_id " ) == 1)
			{
				echo 11; disconnect($con); die;
			}
			
			
			
			
			$field_array="sub_department_name*department_id*buyer_id*update_by*update_date*status_active*is_deleted";
			$data_array="".$txt_sub_dep_name."*".$cbo_department_id."*".$cbo_buyer_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'*'0'";	
			$rID=sql_update("lib_pro_sub_deparatment",$field_array,$data_array,"id","".$update_id."",1);
		
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
	
	
	else if ($operation==2)   // Delete Here===================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="update_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("lib_pro_sub_deparatment",$field_array,$data_array,"id","".$update_id."",1);
		
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