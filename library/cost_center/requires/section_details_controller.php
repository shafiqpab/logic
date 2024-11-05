<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="section_list_view")
{
	if($db_type==2) $div_com="a.department_name || '-' || c.company_short_name as dept_name" ;
	else $div_com="concat(a.department_name,'-',b.company_short_name) as dept_name" ;
	$sql = "select $div_com ,a.id from lib_department a, lib_division b, lib_company c where a.division_id = b.id and b.company_id = c.id and a.is_deleted=0 and a.status_active=1";//die;
	//$lib_dept_result = sql_select($sql);
	$lib_department_arr=return_library_array( "$sql", "id","dept_name"  );
	//print_r($lib_department_arr);die;
	$arr=array (1=>$lib_department_arr);

	
    echo  create_list_view ( "list_view", "Section Name,department,Contact Person,Contact Number,Email.", "150,150,150,150","800","200",0, "select section_name,department_id,contact_person,contact_no,email,id from lib_section where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,department_id,0,0,0", $arr , "section_name,department_id,contact_person,contact_no,email", "../cost_center/requires/section_details_controller", 'setFilterGrid("list_view",-1);' ) ;
}


if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id,section_name,department_id,contact_person,contact_no,country_id,website,email,short_name,address,remark,status_active from lib_section where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_section_name').value = '".($inf[csf("section_name")])."';\n";    
		echo "document.getElementById('cbo_department_id').value  = '".($inf[csf("department_id")])."';\n";
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
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_section_details',1);\n";  
	}
}



if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$integrated_point=return_field_value("project_name","lib_integration_variables","project_name=2","project_name");
	if($integrated_point==2)
	{
		echo "50"; die;
	}
	else
	{
		$department_id = str_replace("'","",$cbo_department_id);
		if ($operation==0)  // Insert Here============================================
		{

			// if (is_duplicate_field( "section_name", "lib_section", "section_name=$txt_section_name and is_deleted=0" ) == 1)
			// {
			// 	echo "11**0"; die;
			// }
			// $department_id = str_replace("'","",$cbo_department_id);
			
			// $sql ="select a.id,a.section_name,a.department_id,b.division_id,c.company_id  from lib_section a, lib_department b, lib_division c where a.department_id = b.id and b.division_id=c.id and  a.section_name = $txt_section_name and a.is_deleted = 0 and a.status_active=1";
			// $sql_result = sql_select($sql);
			// $company_wise_department_array=array();
	
			// foreach ($sql_result as $row){
			// 	$company_wise_department_array[$row[csf('company_id')]][$row[csf('division_id')]][$row[csf('department_id')]]= $row[csf('section_name')];
			// }
			
	
			// if (!empty($company_wise_department_array))
			// {
			// 	echo "11**0"; die();
			// }
			 if (is_duplicate_field( "section_name", "lib_section", "section_name=$txt_section_name and is_deleted=0 and department_id='$department_id' " ) == 1)
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
		
				$id=return_next_id( "id", "lib_section", 1 ) ;
				$field_array="id,section_name,department_id,contact_person,contact_no,country_id,website,email,short_name,address,remark,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id.",".$txt_section_name.",".$cbo_department_id.",".$txt_contact_person.",".$txt_contact_no.",".$cbo_country_id.",".$txt_website.",".$txt_email.",".$txt_short_name.",".$txt_address.",".$txt_remark.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0')";
				$rID=sql_insert("lib_section",$field_array,$data_array,1);
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
		
		else if ($operation==1)   // Update Here=========================================================
		{
			if (is_duplicate_field( "section_name", "lib_section", "section_name=$txt_section_name and is_deleted=0 and department_id='$department_id' and id!=$update_id" ) == 1)
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
				$field_array="section_name*department_id*contact_person*contact_no*country_id*website*email*short_name*address*remark*updated_by*update_date*status_active*is_deleted";
				$data_array="".$txt_section_name."*".$cbo_department_id."*".$txt_contact_person."*".$txt_contact_no."*".$cbo_country_id."*".$txt_website."*".$txt_email."*".$txt_short_name."*".$txt_address."*".$txt_remark."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'";
				
				$rID=sql_update("lib_section",$field_array,$data_array,"id","".$update_id."",1);
				
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
		
		
			
		else if ($operation==2)   // Delete Here===================================================
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			
			$rID=sql_delete("lib_section",$field_array,$data_array,"id","".$update_id."",1);
			
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