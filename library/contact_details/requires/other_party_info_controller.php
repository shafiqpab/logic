<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="save_update_delete")
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "other_party_name", "lib_other_party", "other_party_name=$txt_other_party_name and is_deleted=0" ) == 1)
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
			$id=return_next_id( "id", "lib_other_party", 1 );
			$field_array="id,other_party_name,short_name,contact_person,designation,email,contact_no,web_site,address,country_id,remark,inserted_by,insert_date, status_active,is_deleted";

			$data_array="(".$id.",".$txt_other_party_name.",".$txt_short_name.",".$txt_contact_person.",".$txt_designation.",".$txt_email.",".$txt_contact_no.",".$txt_web_site.",".$txt_address.",".$cbo_country.",".$txt_remark.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0')";
			$rID=sql_insert("lib_other_party",$field_array,$data_array,1);
			
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "0**".$rID;
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
					echo "0**".$rID;
				}
				else
				{
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
		if (is_duplicate_field( "other_party_name", "lib_other_party", "other_party_name=$txt_other_party_name and id!=$update_id and is_deleted=0" ) == 1)
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

			$field_array="other_party_name*short_name*contact_person*designation*email*contact_no*web_site*address*country_id*remark*updated_by*update_date* status_active*is_deleted";
			$data_array="".$txt_other_party_name."*".$txt_short_name."*".$txt_contact_person."*".$txt_designation."*".$txt_email."*".$txt_contact_no."*".$txt_web_site."*".$txt_address."*".$cbo_country."*".$txt_remark."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'";
			$rID=sql_update("lib_other_party",$field_array,$data_array,"id","".$update_id."",1);
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
			if($db_type==2 || $db_type==1 )
			{
			if($rID )
				{
					oci_commit($con);  
					echo "1**".$rID;
				}
				else
				{
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
		
		$party_type_id=str_replace("'","",$update_id);
		$yean_issue_mrr=return_field_value("issue_number", "inv_issue_master", "other_party=$party_type_id and entry_form=3  and status_active=1 and is_deleted=0","issue_number");
		$dyes_chemical_issue_mrr=return_field_value("issue_number", "inv_issue_master", "loan_party=$party_type_id and entry_form=5 and status_active=1 and is_deleted=0","issue_number");
		$dyes_chemical_receive_mrr=return_field_value("issue_number", "inv_issue_master", "loan_party=$party_type_id and entry_form=4 and status_active=1 and is_deleted=0","issue_number");
		
		if($yean_issue_mrr!="" || $dyes_chemical_issue_mrr!="" || $dyes_chemical_receive_mrr!="" )
		{
			if($yean_issue_mrr!="") $delete_message="\n Yean Issue Number:".$yean_issue_mrr;
			if($dyes_chemical_issue_mrr!="") $delete_message.="\n Dyes & Chemical Issue:".$dyes_chemical_issue_mrr;
			if($dyes_chemical_receive_mrr!="") $delete_message.="\n Dyes & Chemical Receive :".$dyes_chemical_receive_mrr;
			
		echo "50**Some Entries Found For This Party, Deleting Not Allowed.".$delete_message;	
		
		 die;
		}
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$rID=sql_delete("lib_other_party",$field_array,$data_array,"id","".$update_id."",1);
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
			else
			{
				oci_rollback($con);  
				echo "10**".$rID;
				
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="other_party_list_view")
{
	$arr=array (5=>$row_status);
	echo  create_list_view ( "list_view", "Party Name,Contact Person,Designation,Contact NO,Email,Status", "150,150,100,150,150","900","220",0, "select  other_party_name,contact_person,designation,contact_no,email,status_active,id from lib_other_party where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,0,0,status_active", $arr , "other_party_name,contact_person,designation,contact_no,email,status_active", "../contact_details/requires/other_party_info_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0') ;
    
}
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id,other_party_name, short_name, contact_person, designation, email,contact_no, web_site,address,country_id,remark, status_active from lib_other_party  where id='$data'" );
	foreach ($nameArray as $inf)
	{
		
		echo "document.getElementById('txt_other_party_name').value = '".($inf[csf("other_party_name")])."';\n"; 
		echo "document.getElementById('txt_short_name').value = '".($inf[csf("short_name")])."';\n";    
		echo "document.getElementById('txt_contact_person').value  = '".($inf[csf("contact_person")])."';\n"; 
		echo "document.getElementById('txt_designation').value  = '".($inf[csf("designation")])."';\n"; 
		echo "document.getElementById('txt_contact_no').value  = '".($inf[csf("contact_no")])."';\n";  
		echo "document.getElementById('txt_email').value = '".($inf[csf("email")])."';\n";    
		echo "document.getElementById('txt_web_site').value  = '".($inf[csf("web_site")])."';\n";  
		echo "document.getElementById('txt_address').value = '".($inf[csf("address")])."';\n";    
		echo "document.getElementById('cbo_country').value  = '".($inf[csf("country_id")])."';\n"; 
		echo "document.getElementById('txt_remark').value  = '".($inf[csf("remark")])."';\n";  
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n"; 
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_other_party_info',1);\n"; 
 
	}
}


?>