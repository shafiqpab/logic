<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="search_list_view")
{
	$company_arr = return_library_array("select id, company_name from lib_company",'id','company_name'); 
	$arr = array(0=>$company_arr,1=>$integrated_project_list);	
	echo  create_list_view ( "list_view", "Company Name,Project Name,Server Name,Database Name,User Name", "130,120,130,130,110","600","250",0, "select  company_id,project_name,server_name,database_name,login_name,id from lib_db_integration_variables", "get_php_form_data", "id", "'load_php_data_to_form'", 0,"company_id,project_name", $arr ,"company_id,project_name,server_name,database_name,login_name", "requires/integration_variables_intercompany_controller", 'setFilterGrid("list_view",-1);' );
}

if ($action=="load_php_data_to_form")
{
	
	$nameArray=sql_select( "select  id,company_id,project_name,database_name,server_name,ip_address,login_name,login_password,admin_mail,server_id,port_no from lib_db_integration_variables where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_company_id').value 		= '".($inf[csf("company_id")])."';\n"; 
		echo "document.getElementById('cbo_project_name').value 	= '".($inf[csf("project_name")])."';\n";    
		echo "document.getElementById('txt_database_name').value  	= '".($inf[csf("database_name")])."';\n"; 
		echo "document.getElementById('txt_server_name').value 		= '".($inf[csf("server_name")])."';\n";    
		echo "document.getElementById('txt_ip_address').value  		= '".($inf[csf("ip_address")])."';\n"; 
		echo "document.getElementById('txt_login_name').value 		= '".($inf[csf("login_name")])."';\n";    
		echo "document.getElementById('txt_login_password').value  	= '".($inf[csf("login_password")])."';\n"; 
		echo "document.getElementById('txt_admin_mail').value 		= '".($inf[csf("admin_mail")])."';\n"; 
		echo "document.getElementById('txt_server_id').value 		= '".($inf[csf("server_id")])."';\n"; 
		echo "document.getElementById('txt_port').value 			= '".($inf[csf("port_no")])."';\n";

		echo "document.getElementById('update_id').value 			= '".($inf[csf("id")])."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_integration_variable',1);\n"; 
	}
}

if ($action=="save_update_delete")
{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here==================================================
	{
		if (is_duplicate_field( "project_name", "lib_integration_variables", "project_name=$cbo_project_name and company_id=$cbo_company_id" ) == 1)
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
			$id=return_next_id( "id", " lib_db_integration_variables", 1 ) ; 
			$field_array="id,company_id,project_name,database_name,server_name,ip_address,login_name,login_password,admin_mail,server_id,port_no";
			//'cbo_project_name*txt_database_name*txt_server_name*txt_ip_address*txt_login_name*txt_login_password*txt_admin_mail*txt_server_id*txt_port*update_id'
			$data_array="(".$id.",".$cbo_company_id.",".$cbo_project_name.",".$txt_database_name.",".$txt_server_name.",".$txt_ip_address.",".$txt_login_name.",".$txt_login_password.",".$txt_admin_mail.",".$txt_server_id.",".$txt_port.")";
			//echo "10***insert into lib_integration_variables(4field_array)values".$data_array;
			$rID=sql_insert("lib_db_integration_variables",$field_array,$data_array,1);
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
	
	else if ($operation==1)   // Update Here====================================================
	{
		if (is_duplicate_field( "project_name", "lib_integration_variables", "project_name=$cbo_project_name and company_id=$cbo_company_id and  id!=$update_id" ) == 1)
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
			$field_array="company_id*project_name*database_name*server_name*ip_address*login_name*login_password*admin_mail*server_id*port_no";
			
			$data_array="".$cbo_company_id."*".$cbo_project_name."*".$txt_database_name."*".$txt_server_name."*".$txt_ip_address."*".$txt_login_name."*".$txt_login_password."*".$txt_admin_mail."*".$txt_server_id."*".$txt_port."";
			
			$rID=sql_update("lib_db_integration_variables",$field_array,$data_array,"id","".$update_id."",1);
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
		$rID=execute_query( "delete from lib_db_integration_variables where id=$update_id",1 );
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