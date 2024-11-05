<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

 
if ($action=="save_update_delete")
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	
	//===============Insert====================
	if($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		//return_next_id( $field_name, $table_name, $max_row=1, $new_conn );
		$id = return_next_id( "id", "tbl_test_employee_reaz", 1 ) ;
		
		//id full_name emp_code company designation email address joining_date sex education status_active 
		$field_array = "id, full_name, emp_code, company, designation, email, address, joining_date, sex, education, status_active";
		$data_array="(".$id.",".$txt_full_name.",".$txt_emp_code.",".$txt_company.",".$cbo_designation.",".$txt_email.",".$txt_address.",".$txt_joining_date.",".$sex.",".$education.",".$status_active.")";
		//$data_array="(".$id.",".$txt_employee_id.",".$txt_full_name.",".$designation_level.",".$address.",".$txt_exp_date.")"; 
		//echo "insert into tbl_test_employee_reaz(".$field_array.")values".$data_array;die;
		$rID = sql_insert("tbl_test_employee_reaz",$field_array,$data_array,1);
		
		//echo $rID; die;
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
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
	
	//==================Update==================================
	else if($operation==1)
	{

			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
//			$field_array = "txt_employee_id*txt_full_name*designation_level*address*txt_exp_date";
//			$data_array="".$txt_employee_id."*".$txt_full_name."*".$designation_level."*".$address."*".$txt_exp_date."";
			
			$field_array = "full_name*emp_code*company*designation*email*address*joining_date*sex*education*status_active";
			$data_array=$txt_full_name."*".$txt_emp_code."*".$txt_company."*".$cbo_designation."*".$txt_email."*".$txt_address."*".$txt_joining_date."*".$sex."*".$education."*".$status_active;
		
		
		
			//$field_array="txt_employee_id*txt_full_name*designation_level*address*txt_exp_date";
			//$data_array="(".$txt_employee_id."*".$txt_full_name."*".$designation_level."*".$address."*".$txt_exp_date.")";
			
			$rID=sql_update("tbl_test_employee_reaz",$field_array,$data_array,"id","".$update_id."",1);
			
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "1**";
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**";
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
	
		//====================== Delete ===========================================================
	else if($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$rID=execute_query("delete from tbl_test_employee_reaz where id=$data",0);	
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
 
		$rID=sql_delete("tbl_test_employee_reaz",$field_array,$data_array,"company_id*form_id",$cbo_company_id."*".$cbo_form_id,1);	
		if($db_type==0)
		{
			if($rID)
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
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
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
	exit();

}
 
 
 
 
if ($action=="load_php_data_to_form")
{
 
	$nameArray=sql_select( "select id,full_name,emp_code,company,designation,email,address,joining_date,sex,education,status_active  from tbl_test_employee_reaz  where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('update_id').value = '".$inf[csf("id")]."';\n";
		echo "document.getElementById('txt_full_name').value = '".$inf[csf("full_name")]."';\n";    
		echo "document.getElementById('txt_emp_code').value  = '".($inf[csf("emp_code")])."';\n"; 
		echo "document.getElementById('txt_company').value  = '".($inf[csf("company")])."';\n";  
		
		echo "document.getElementById('cbo_designation').value  = '".($inf[csf("designation")])."';\n";
		echo "document.getElementById('txt_email').value  = '".($inf[csf("email")])."';\n";  
		echo "document.getElementById('txt_address').value  = '".($inf[csf("address")])."';\n";  
		echo "document.getElementById('txt_joining_date').value  = '".change_date_format($inf[csf("joining_date")])."';\n";  
		echo "document.getElementById('sex').value  = '".($inf[csf("sex")])."';\n";  
		echo "document.getElementById('education').value  = '".($inf[csf("education")])."';\n";  
		echo "document.getElementById('status_active').value  = '".($inf[csf("status_active")])."';\n"; 
		
		  
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_test_employee',1);\n";  
		//echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_user_creation',1);\n";  
	}
}
		   $designation_arr = return_library_array("select id, system_designation from lib_designation","id","system_designation");
				   $SexStatus_arr= array(1=>'Male',2=>'Female');
				   $Status_arr= array(0=>'In Active',1=>'Active');
					$arr=array (3=>$designation_arr,7=>$SexStatus_arr, 9=>$Status_arr);
					echo  create_list_view ( "list_view", "Full Name,Employee Code,Company,Designation,Email,Address,Joining Date,Sex,Education,Status", "100,100,80,100,100,100,100,50,80","990","220",1, "select id,full_name,emp_code,company,designation,email,address,joining_date,sex,education,status_active from tbl_test_employee_reaz", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,designation,0,0,0,sex,0,status_active", $arr , "full_name,emp_code,company,designation,email,address,joining_date,sex,education,status_active", "requires/test_employee_reaz_controller", 'setFilterGrid("list_view",-1);' );
                ?>