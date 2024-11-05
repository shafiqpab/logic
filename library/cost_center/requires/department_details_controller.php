<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="department_list_view")
{
	if($db_type==2) $div_com="a.division_name || '-' || b.company_short_name as division_name" ;
	else $div_com="concat(a.division_name,'-',b.company_short_name) as division_name" ;
	$lib_division_arr=return_library_array( "select $div_com,a.id from lib_division a,lib_company b where a.company_id=b.id", "id","division_name"  );
	$arr=array (1=>$lib_division_arr);
	
    echo  create_list_view ( "list_view", "Department Name,Division,Contact Person,Contact Number,Email.", "150,150,150,150","800","200",0, "select  department_name,division_id,contact_person,contact_no,email,id from lib_department where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,division_id,0,0,0", $arr , "department_name,division_id,contact_person,contact_no,email", "../cost_center/requires/department_details_controller", 'setFilterGrid("list_view",-1);' ) ;
}


if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id,department_name,division_id,contact_person,contact_no,country_id,website,email,short_name,address,remark,status_active from lib_department where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_department_name').value = '".($inf[csf("department_name")])."';\n";    
		echo "document.getElementById('cbo_division_id').value  = '".($inf[csf("division_id")])."';\n";
		echo "document.getElementById('txt_contact_person').value = '".($inf[csf("contact_person")])."';\n";    
		echo "document.getElementById('txt_contact_no').value  = '".($inf[csf("contact_no")])."';\n";
		echo "document.getElementById('cbo_country_id').value  = '".($inf[csf("country_id")])."';\n";
		echo "document.getElementById('txt_website').value = '".($inf[csf("website")])."';\n";
		echo "document.getElementById('txt_email').value = '".($inf[csf("email")])."';\n"; 
		echo "document.getElementById('txt_short_name').value = '".($inf[csf("short_name")])."';\n";
    	echo "document.getElementById('txt_address').value = '".($inf[csf("address")])."';\n"; 
		echo "document.getElementById('txt_remark').value = '".($inf[csf("remark")])."';\n";
		echo "document.getElementById('cbo_status').value = '".($inf[csf("status_active")])."';\n";    
		echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_department_details',1);\n";  
	}
}


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $txt_department_name;die;
	
	$integrated_point=return_field_value("project_name","lib_integration_variables","project_name=2","project_name");
	if($integrated_point==2)
	{
		echo "50"; die;
	}
	else
	{
		if ($operation==0)  // Insert Here============================================
		{
			$division_id = str_replace("'","",$cbo_division_id);
			
			$sql ="select a.department_name,a.division_id,b.company_id  from lib_department a, lib_division b where a.division_id = b.id and  a.department_name = $txt_department_name and a.is_deleted = 0";
			$sql_result = sql_select($sql);
			$company_wise_division_array=array();
	
			foreach ($sql_result as $row){
				$company_wise_division_array[$row[csf('company_id')]][$row[csf('division_id')]]= $row[csf('department_name')];
			}
			
			$company_id_array = return_library_array("select id,company_id from lib_division where id=$cbo_division_id and is_deleted=0","id","company_id");
	
			 $depart_ment = "'".strtolower($company_wise_division_array[$company_id_array[$division_id]][$division_id])."'";//die("with kkk");
			
	
			if ( $depart_ment == strtolower($txt_department_name))
			{
				echo "11**0"; die();
			}
			else
			{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
		
		$id=return_next_id( "id", "lib_department", 1 ) ;
		$field_array="id,department_name,division_id,contact_person,contact_no,country_id,website,email,short_name,address,remark,inserted_by,insert_date,status_active,is_deleted";
		$data_array="(".$id.",".$txt_department_name.",".$cbo_division_id.",".$txt_contact_person.",".$txt_contact_no.",".$cbo_country_id.",".$txt_website.",".$txt_email.",".$txt_short_name.",".$txt_address.",".$txt_remark.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0')";
		
		$rID=sql_insert("lib_department",$field_array,$data_array,1);
				//echo $data_array; die;
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
				$field_array="department_name*division_id*contact_person*contact_no*country_id*website*email*short_name*address*remark*updated_by*update_date*status_active*is_deleted";
				$data_array="".$txt_department_name."*".$cbo_division_id."*".$txt_contact_person."*".$txt_contact_no."*".$cbo_country_id."*".$txt_website."*".$txt_email."*".$txt_short_name."*".$txt_address."*".$txt_remark."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'";
				
				$rID=sql_update("lib_department",$field_array,$data_array,"id","".$update_id."",1);
				
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
				disconnect($con);
				die;
				}
		}
		
		
		else if ($operation==2)   // Delete Here===================================================
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			
			$rID=sql_delete("lib_department",$field_array,$data_array,"id","".$update_id."",1);
			
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
				disconnect($con);
				die;
			 }
		}
	}
}


?>