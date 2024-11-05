<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="search_list_view")
{
	$arr=array (3=>$row_status, 2=>$business_nature_arr, 1=>$sample_type);
	echo  create_list_view ( "list_view", "Sample Name,Sample Type,Business Nature,Status.", "200,150,100,100","570","220",0, "select  sample_name, sample_type, status_active, id, business_nature from lib_sample where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,sample_type,business_nature,status_active", $arr , "sample_name,sample_type,business_nature,status_active", "requires/sample_info_controller", 'setFilterGrid("list_view",-1);' ) ;
	exit();
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select sample_name, sample_type, invoice_mendatory, business_nature, status_active, id from lib_sample where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_sample_name').value = '".($inf[csf("sample_name")])."';\n";    
		echo "document.getElementById('cbo_business_nature').value = '".($inf[csf("business_nature")])."';\n";    
		echo "document.getElementById('cbo_sample_type').value  = '".($inf[csf("sample_type")])."';\n"; 
		echo "document.getElementById('cbo_invoice_mendatory').value  = '".($inf[csf("invoice_mendatory")])."';\n"; 
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_sample_info',1);\n";  
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "sample_name", "lib_sample", "sample_name=$txt_sample_name and business_nature=$cbo_business_nature and is_deleted=0" ) == 1)
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
			$id=return_next_id( "id", "lib_sample", 1 ) ;
			$field_array="id, sample_name, sample_type, invoice_mendatory, business_nature, inserted_by, insert_date, status_active, is_deleted";
			$data_array="(".$id.",".$txt_sample_name.",".$cbo_sample_type.",".$cbo_invoice_mendatory.",".$cbo_business_nature.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
			$rID=sql_insert("lib_sample",$field_array,$data_array,1);
			
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
		if (is_duplicate_field( "sample_name", "lib_sample", "sample_name=$txt_sample_name and business_nature=$cbo_business_nature and id!=$update_id and is_deleted=0" ) == 1)
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
			
			$field_array="sample_name*sample_type*invoice_mendatory*business_nature*updated_by*update_date*status_active";
			$data_array="".$txt_sample_name."*".$cbo_sample_type."*".$cbo_invoice_mendatory."*".$cbo_business_nature."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."";
			
			$rID=sql_update("lib_sample",$field_array,$data_array,"id","".$update_id."",1);
			 //echo $data_array; die;
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
		
		$rID=sql_delete("lib_sample",$field_array,$data_array,"id","".$update_id."",1);
		
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