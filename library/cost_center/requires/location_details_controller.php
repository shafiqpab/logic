<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="location_list_view")
{
		//$arr=array (1=>$yes_no);
		$lib_company_arr=return_library_array( "select id,company_name from lib_company where status_active=1 and is_deleted=0", "id","company_name" );
		$arr=array (1=>$lib_company_arr);
		echo  create_list_view ( "list_view", "Location Name,Company Name,Contact Person,Contact Number,Email.", "200,150,150,150","900","200",0, "select  location_name,company_id,contact_person,contact_no,email,id from lib_location where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,company_id,0,0,0", $arr , "location_name,company_id,contact_person,contact_no,email", "../cost_center/requires/location_details_controller", 'setFilterGrid("list_view",-1);' ) ;
		// echo  create_list_view ( "list_view", "Location Name,Contact Person,Contact Number,Email.", "200,150,150","800","200",0, "select  location_name,contact_person,contact_no,email,id from lib_location where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,subcontract_party,0,0", $arr , "location_name,contact_person,contact_no,email", "../cost_center/requires/location_details_controller", 'setFilterGrid("list_view",-1);' ) ;
		}


if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id,location_name,contact_person,contact_no,company_id,website,address,email,country_id,remark,status_active from  lib_location where id='$data'" );
	// print_r( $data);die;

	foreach ($nameArray as $inf)
	{
		$location_name=str_replace(array("&", "*", "(", ")", "=","'","_","\r", "\n",'"','#'), "", $inf[csf("location_name")]);
		$contact_person=str_replace(array("&", "*", "(", ")", "=","'","_","\r", "\n",'"','#'), "", $inf[csf("contact_person")]);
		$contact_no=str_replace(array("&", "*", "(", ")", "=","'","_","\r", "\n",'"','#'), "", $inf[csf("contact_no")]);
		$country_id=str_replace(array("&", "*", "(", ")", "=","'","_","\r", "\n",'"','#'), "",$inf[csf("country_id")]);
		$website=str_replace(array("&", "*", "(", ")", "=","'","_","\r", "\n",'"','#'), "", $inf[csf("website")]);
		$email=str_replace(array("&", "*", "(", ")", "=","'","_","\r", "\n",'"','#'), "", $inf[csf("email")]);
		$company_id=str_replace(array("&", "*", "(", ")", "=","'","_","\r", "\n",'"','#'), "", $inf[csf("company_id")]);
		$address=str_replace(array("&", "*", "(", ")", "=","'","_","\r", "\n",'"','#'), "", $inf[csf("address")]);
		$remark=str_replace(array("&", "*", "(", ")", "=","'","_","\r", "\n",'"','#'), "", $inf[csf("remark")]);		
		echo "document.getElementById('txt_location_name').value = '".$location_name."';\n";    
		echo "document.getElementById('txt_contact_person').value  = '".$contact_person."';\n";
		echo "document.getElementById('txt_contact_no').value = '".$contact_no."';\n";    
		echo "document.getElementById('cbo_country_id').value  = '".$country_id."';\n";
		echo "document.getElementById('txt_website').value  = '".$website."';\n";
		echo "document.getElementById('txt_email').value = '".$email."';\n";
		echo "document.getElementById('cbo_company_id').value = '".$company_id."';\n";    
    	echo "document.getElementById('txt_address').value = '".$address."';\n"; 
		echo "document.getElementById('txt_remark').value = '".$remark."';\n";
		echo "document.getElementById('cbo_status').value = '".($inf[csf("status_active")])."';\n";    
		echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_location_info',1);\n";  
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here=======================================================
	{
		if (is_duplicate_field( "location_name", "lib_location", "location_name=$txt_location_name and company_id=$cbo_country_id and is_deleted=0" ) == 1)
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
	
			$id=return_next_id( "id", "lib_location", 1 ) ;
			$field_array="id,location_name,contact_person,contact_no,company_id,website,address,email,country_id,remark,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$txt_location_name.",".$txt_contact_person.",".$txt_contact_no.",".$cbo_company_id.",".$txt_website.",".$txt_address.",".$txt_email.",".$cbo_country_id.",".$txt_remark.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0')";
			$rID=sql_insert("lib_location",$field_array,$data_array,1);
			//echo $rID; die;
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
	
	else if ($operation==1)   // Update Here=====================================================
	{
		if (is_duplicate_field( "location_name", "lib_location", "location_name=$txt_location_name and company_id=$cbo_country_id and id!=$update_id and is_deleted=0" ) == 1)
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
			
			$field_array="location_name*contact_person*contact_no*company_id*website*address*email*country_id*remark*updated_by*update_date*status_active*is_deleted";
			$data_array="".$txt_location_name."*".$txt_contact_person."*".$txt_contact_no."*".$cbo_company_id."*".$txt_website."*".$txt_address."*".$txt_email."*".$cbo_country_id."*".$txt_remark."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'";
			
			$rID=sql_update("lib_location",$field_array,$data_array,"id","".$update_id."",1);
			
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
	
	else if ($operation==2)   // Update Here==================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("lib_location",$field_array,$data_array,"id","".$update_id."",1);
		
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