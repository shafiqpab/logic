<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_report_module")
{
	echo create_drop_down( "cbo_menu_name", 212, "SELECT a.m_menu_id, a.menu_name FROM main_menu a, user_priv_mst b where a.m_menu_id=b.main_menu_id and a.m_module_id='$data' and b.user_id=".$user_id." and a.status='1' and b.valid=1 and a.is_mobile_menu not in (1) and b.show_priv=1 and a.f_location is not null order by a.menu_name","m_menu_id,menu_name", 1, "--- Select Page ---", $selected, "","","" );
	exit();
}

if ($action=="iso_list_view")
{
	$companyarr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$moduleArr=return_library_array( "select m_mod_id, main_module from main_module where status=1",'m_mod_id','main_module');
	$menuArr=return_library_array( "SELECT m_menu_id, menu_name FROM main_menu where status=1 and is_mobile_menu not in (1) and f_location is not null",'m_menu_id','menu_name');
	$arr=array (0=>$companyarr,1=>$moduleArr,2=>$menuArr,4=>$row_status);
	echo  create_list_view ( "list_view", "Company Name,Module Name,Page Name,ISO No,Status", "170,170,250,120","880","220",0, "select id, company_id, module_id, menu_id, iso_no, status_active from lib_iso where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id,module_id,menu_id,0,status_active", $arr , "company_id,module_id,menu_id,iso_no,status_active", "requires/iso_library_entry_controller", 'setFilterGrid("list_view",-1);' ) ; 
	exit();
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, company_id, module_id, menu_id, iso_no, status_active from lib_iso where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "load_drop_down( 'requires/iso_library_entry_controller', '".($inf[csf("module_id")])."', 'load_drop_down_report_module', 'report_name_td' );\n";
		//cbo_company_id*cbo_module_name*cbo_menu_name*txt_iso_no*cbo_status*update_id
		echo "document.getElementById('cbo_company_id').value = '".($inf[csf("company_id")])."';\n";    
		echo "document.getElementById('cbo_module_name').value  = '".($inf[csf("module_id")])."';\n";  
		echo "document.getElementById('cbo_menu_name').value  = '".($inf[csf("menu_id")])."';\n"; 
		echo "document.getElementById('txt_iso_no').value = '".($inf[csf("iso_no")])."';\n"; 
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "disable_enable_fields('cbo_company_id*cbo_module_name*cbo_menu_name',1);\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_store_location',1);\n"; 
		
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//and iso_no='".str_replace("'","",$txt_iso_no)."'
		
		if (is_duplicate_field( "iso_no", "lib_iso", "company_id=$cbo_company_id and module_id=$cbo_module_name and menu_id=$cbo_menu_name  and status_active=1 " ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$id=return_next_id( "id", "lib_iso", 1 );
			$field_array="id,company_id,module_id,menu_id,iso_no,inserted_by,insert_date,is_deleted,status_active";
			$data_array="(".$id.",".$cbo_company_id.",".$cbo_module_name.",".$cbo_menu_name.",".$txt_iso_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,".$cbo_status.")";
			//echo "insert into lib_iso ($field_array) values $data_array";oci_rollback($con);disconnect($con);die;
			$rID=sql_insert("lib_iso",$field_array,$data_array,0);
	//=================================================================================
			if($db_type==0)
			{
				if($rID==1)
				{
					mysql_query("COMMIT");  
					echo "0**".$rID."**".$id;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
				if($rID==1)
				{  
					oci_commit($con);  
					echo "0**".$rID."**".$id;
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
	
	if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//and iso_no='".str_replace("'","",$txt_iso_no)."'
		
		if (is_duplicate_field( "iso_no", "lib_iso", "company_id=$cbo_company_id and module_id=$cbo_module_name and menu_id=$cbo_menu_name  and id<>$update_id and status_active=1 " ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$field_array="iso_no*updated_by*update_date*status_active";
			$data_array="".$txt_iso_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."";
			$rID=sql_update("lib_iso",$field_array,$data_array,"id",$update_id,1);
			
			//echo "10** $rID";disconnect($con);oci_rollback($con);die;
			//==================================================================================================================
			if($db_type==0)
			{
				if($rID==1 )
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
			  if($rID==1 )
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
	
	if ($operation==2)  // Delete Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			// and company_id=$cbo_company_name  and location_id=$cbo_store_location
			if (is_duplicate_field( "store_id", "inv_transaction", "status_active=1 and is_deleted=0 and store_id=$update_id" ) == 1){
				echo "13**0"; disconnect($con);die;
			}else{
				$field_array="updated_by*update_date*status_active*is_deleted";
				$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
				$rID=sql_update("lib_iso",$field_array,$data_array,"id","".$update_id."",1);
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
				{	if($rID )
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
			}
			disconnect($con);
			die;
	}
}




?>