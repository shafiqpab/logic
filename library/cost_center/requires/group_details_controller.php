<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="group_details_view")
{
	$arr=array (5=>$row_status);
	echo  create_list_view ( "list_view", "Group Name,Contact Person,Contact No,Website,Address,Status", "170,90,100,130,80","700","220",1, "
	select  id,group_name,contact_person,contact_no,website,address,status_active from lib_group where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, 	"0,0,0,0,0,status_active", $arr, "group_name,contact_person,contact_no,website,address,status_active", "../cost_center/requires/group_details_controller", 'setFilterGrid("list_view",-1);',''); 
}



if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id,group_name,group_short_name,contact_person,contact_no,country_id,website,email,address,remark,status_active from  lib_group where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_group_name').value = '".($inf[csf("group_name")])."';\n"; 
		echo "document.getElementById('txt_group_short').value = '".($inf[csf("group_short_name")])."';\n";    
		echo "document.getElementById('txt_contact_person').value  = '".($inf[csf("contact_person")])."';\n";
		echo "document.getElementById('txt_contact_no').value = '".($inf[csf("contact_no")])."';\n";    
		echo "document.getElementById('cbo_country_id').value  = '".($inf[csf("country_id")])."';\n";
		echo "document.getElementById('txt_website').value  = '".($inf[csf("website")])."';\n";
		echo "document.getElementById('txt_email').value  = '".($inf[csf("email")])."';\n";
		echo "document.getElementById('txt_address').value = '".($inf[csf("address")])."';\n";
		echo "document.getElementById('txt_remark').value = '".($inf[csf("remark")])."';\n";
		echo "document.getElementById('cbo_status').value = '".($inf[csf("status_active")])."';\n";    
    	echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
	    echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_group_details',1);\n";  
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "group_name", "lib_group", "group_name=$txt_group_name and is_deleted=0" ) == 1)
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
			$id=return_next_id( "id", "lib_group", 1 ) ;
			$field_array="id,group_name,group_short_name,contact_person,contact_no,country_id,website,email,address,remark,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$txt_group_name.",".$txt_group_short.",".$txt_contact_person.",".$txt_contact_no.",".$cbo_country_id.",".$txt_website.",".$txt_email.",".$txt_address.",".$txt_remark.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0')";
			$rID=sql_insert("lib_group",$field_array,$data_array,1);
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
		if (is_duplicate_field( "group_name", "lib_group", "group_name=$txt_group_name and id!=$update_id and is_deleted=0" ) == 1)
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
			$field_array="group_name*group_short_name*contact_person*contact_no*country_id*website*email*address*remark*updated_by*update_date*status_active*is_deleted";
			$data_array="".$txt_group_name."*".$txt_group_short."*".$txt_contact_person."*".$txt_contact_no."*".$cbo_country_id."*".$txt_website."*".$txt_email."*".$txt_address."*".$txt_remark."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'";
			$rID=sql_update("lib_group",$field_array,$data_array,"id","".$update_id."",1);
			
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
		if (is_duplicate_field( "id", "lib_company", "group_id=$update_id and is_deleted=0" ) == 1)
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
			
			$rID=sql_delete("lib_group",$field_array,$data_array,"id","".$update_id."",1);
			
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