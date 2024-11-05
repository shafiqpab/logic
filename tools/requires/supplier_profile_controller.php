<?
session_start();
//Checking the user access
if( $_SESSION['logic_erp']['user_id'] == "" ) header("../../../location:login.php");
//include common php function
require_once('../../includes/common.php');
//Extract all request and stored into an‍ array index
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
if ($action=="supplier_details_view")
{
	$arr=array (8=>$row_status);
	echo  create_list_view ( "list_view", "Suppier Name,Contact Person,Address,Country,Email,Phone,Web,Status,Remarks", "130,80,100,100,70,70,70,90","850","auto",1, "select supplier_name,contact_person,address,country_id,email,phone,web,status_active,remarks,id from supplier_tbl_imran where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1,"0,0,0,0,0,0,0,0,status_active", $arr, "supplier_name,contact_person,address,country_id,email,phone,web,status_active,remarks", "requires/supplier_profile_controller", 'setFilterGrid("list_view",-1);',''); 
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select supplier_name,contact_person,address,country_id,email,phone,web,status_active,remarks,id from supplier_tbl_imran where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_supplier_name').value = '".($inf[csf("supplier_name")])."';\n";    
		echo "document.getElementById('txt_contact_person').value  = '".($inf[csf("contact_person")])."';\n";
		echo "document.getElementById('txt_address').value = '".($inf[csf("address")])."';\n";    
		echo "document.getElementById('cbo_country_id').value  = '".($inf[csf("country_id")])."';\n";
		echo "document.getElementById('txt_email').value  = '".($inf[csf("email")])."';\n";
		echo "document.getElementById('txt_phone').value  = '".($inf[csf("phone")])."';\n";
		echo "document.getElementById('txt_web').value = '".($inf[csf("web")])."';\n";
		echo "document.getElementById('cbo_status').value = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('txt_remarks').value = '".($inf[csf("remarks")])."';\n";    
    	echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
	    echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_supplier_details',1);\n";  
	}
}


if ($action=="save_update_delete")
{//echo  $action; die;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here
	{
		
		if (is_duplicate_field( "supplier_name", "supplier_tbl_imran", "supplier_name=$txt_supplier_name and is_deleted=0" ) == 1)
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
			$id=return_next_id( "id", "supplier_tbl_imran", 1 ) ;
			
			$field_array="id,supplier_name,contact_person,address,country_id,email,phone,web,status_active,remarks,inserted_by,insert_date,is_deleted";
			$data_array="(".$id.",".$txt_supplier_name.",".$txt_contact_person.",".$txt_address.",".$cbo_country_id.",".$txt_email.",".$txt_phone.",".$txt_web.",".$cbo_status.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0')";
			//echo "INSERT INTO supplier_tbl_imran(".$field_array.") VALUES ".$data_array; die;
			$rID=sql_insert("supplier_tbl_imran",$field_array,$data_array,1);
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
		if (is_duplicate_field( "supplier_name", "supplier_tbl_imran", "supplier_name=$txt_supplier_name and id!=$update_id and is_deleted=0" ) == 1)
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
			
			$field_array="supplier_name*contact_person*address*country_id*email*phone*web*status_active*remarks";
			$data_array="".$txt_supplier_name."*".$txt_contact_person."*".$txt_address."*".$cbo_country_id."*".$txt_email."*".$txt_phone."*".$txt_web."*".$cbo_status."*".$txt_remarks."";
			
			 //echo "update supplier_tbl_imran set(".$field_array."=".$data_array[0]; die;
$rID=sql_update("supplier_tbl_imran", $field_array, $data_array,"id",$update_id,1);
			
			
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
	
	else if ($operation==2) // Delete Here
	{
		if (is_duplicate_field( "id", "supplier_tbl_imran", "supplier_id=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			
			$rID=sql_delete("supplier_tbl_imran",$field_array,$data_array,"id","".$update_id."",1);
			
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
}
?>
