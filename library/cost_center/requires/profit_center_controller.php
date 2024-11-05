<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="prof_center_list_view")
{
	$company_name=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
    $arr=array (1=>$company_name);
    echo  create_list_view ( "list_view", "Profit Center Name,Company Name,Contact Person,Contact Number,Email", "130,150,200,100","800","220",0, " select  	profit_center_name,company_id,contact_person,contact_no,email,id from lib_profit_center where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,company_id,0,0,0", $arr , "profit_center_name,company_id,contact_person,contact_no,email", "../cost_center/requires/profit_center_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0' ) ;


}




if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id,profit_center_name,contact_person,contact_no,company_id,website,address,email,country_id,remark from  lib_profit_center where id='$data'" );
 //txt_prof_cntr_name*cbo_company*txt_area_address*txt_contact_no*txt_area_remark*cbo_country*cbo_status*txt_website*txt_email*update_id
foreach ($nameArray as $inf)
	{	echo "document.getElementById('txt_prof_cntr_name').value = '".($inf[csf("profit_center_name")])."';\n";    
		echo "document.getElementById('cbo_company').value  = '".($inf[csf("company_id")])."';\n";
		echo "document.getElementById('txt_area_address').value = '".($inf[csf("address")])."';\n";   
		echo "document.getElementById('txt_contact_person').value  = '".($inf[csf("contact_person")])."';\n";
		echo "document.getElementById('txt_contact_no').value  = '".($inf[csf("contact_no")])."';\n";
		echo "document.getElementById('txt_area_remark').value  = '".($inf[csf("remark")])."';\n";
		echo "document.getElementById('cbo_country').value = '".($inf[csf("country_id")])."';\n";
		echo "document.getElementById('cbo_status').value = '".($inf[csf("status_active")])."';\n";    
    	echo "document.getElementById('txt_website').value = '".($inf[csf("website")])."';\n"; 
		echo "document.getElementById('txt_email').value = '".($inf[csf("email")])."';\n";
 		echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";

		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_profit_center',1);\n";  
	}
}




if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "profit_center_name", "lib_profit_center", "profit_center_name=$txt_prof_cntr_name and is_deleted=0" ) == 1)
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
			
			$id=return_next_id( "id", "lib_profit_center", 1 );
			$field_array="id,profit_center_name,contact_person,contact_no,company_id,website,address,email,country_id,remark,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$txt_prof_cntr_name.",".$txt_contact_person.",".$txt_contact_no.",".$cbo_company.",".$txt_website.",".$txt_area_address.",".$txt_email.",".
			$cbo_country.",".$txt_area_remark.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0')";
			$rID=sql_insert("lib_profit_center",$field_array,$data_array,1);
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
	
	else if ($operation==1)   // Update Here
	{
		//if (is_duplicate_field( "company_name", "lib_profit_center", "company_name=$txt_company_name and id!=$update_id and is_deleted=0" ) == 1)
	if (is_duplicate_field( "profit_center_name", "lib_profit_center", "profit_center_name=$txt_prof_cntr_name and id!=$update_id and is_deleted=0" ) == 1)

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
			
			$field_array="profit_center_name*contact_person*contact_no*company_id*website*address*email*country_id*remark*updated_by*update_date*status_active*is_deleted";
			$data_array="".$txt_prof_cntr_name."*".$txt_contact_person."*".$txt_contact_no."*".$cbo_company."*".$txt_website."*".$txt_area_address."*".$txt_email."*".
			$cbo_country."*".$txt_area_remark."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'";
			$rID=sql_update("lib_profit_center",$field_array,$data_array,"id","".$update_id."",1);
			
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
		
		$rID=sql_delete("lib_profit_center",$field_array,$data_array,"id","".$update_id."",1);
		
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