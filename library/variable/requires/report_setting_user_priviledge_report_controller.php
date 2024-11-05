<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$report_format=array(1=>"Fabric Booking Gr",2=>"Print Booking F 1",3=>"Print Booking F 2",4=>"Print For Cut 1",5=>"Print For Cut 2",6=>"Fabric Booking F 1",7=>"Fabric Booking F 2",8=>"Print Booking",9=>"Print Booking 2",10=>"Fabric Booking",11=>"Fabric Booking",12=>"Print Booking 1");
$report_name=array(1=>"Main Fabric Booking",2=>"Short Fabric Booking",3=>"Sample Fabric Booking -With order",4=>"Sample Fabric Booking -Without order",5=>"Multiple Order Wise Trims Booking",6=>"Service Booking For Knitting",7=>"Yarn Dyeing Work Order",8=>"Yarn Dyeing Work Order Without Order",9=>"Embellishment Work Order",10=>"Service Booking For AOP",11=>"Fabric Service Booking",12=>"Service Booking For Knitting",13=>"Yarn Dyeing Work Order",14=>"Yarn Dyeing Work Order Without Order");


 


if ($action=="load_priv_list_view")
{
		$data=explode('_',$data);
	  
		$sql= "SELECT a.menu_name,a.m_menu_id, b.show_priv,b.save_priv,b.edit_priv,b.delete_priv,b.approve_priv,b.id FROM main_menu a, user_priv_mst b WHERE b.user_id = '$data[0]' AND a.m_module_id = '$data[1]' AND a.m_menu_id = b.main_menu_id and a.status=1 ORDER BY main_menu_id ASC";
		 
		$arr=array (1=>$form_permission_type,2=>$form_permission_type,3=>$form_permission_type,4=>$form_permission_type,5=>$form_permission_type);
	    echo  create_list_view ( "list_view", "Menu Name,Visibility,Insert,Update ,Delete,Approve", "520,80,80,80,80,80","1050","320",1, $sql, "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,show_priv,save_priv,edit_priv,delete_priv,approve_priv", $arr , "menu_name,show_priv,save_priv,edit_priv,delete_priv,approve_priv", "/requires/user_priviledge_controller", '' ) ;	
	 
}


if ($action=="save_update_delete")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if($operation==0)  // Insert Here
	{ 
	
/*	 echo   "select template_id from lib_report_template where template_name=".trim($txt_template_name)." and status_active=1";die;
		$templateArray=sql_select( "select template_id from lib_report_template where template_name=".trim($txt_template_name)." and status_active=1" );
		
		if(count($templateArray)>0)
		{
			$template_id=$templateArray[0];
		}
		else
		{
			$template_id=return_next_id("template_id", "lib_report_template",1);
		}*/
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		
		
		
		$sql_data=sql_select("select id as mst_id,template_name as conmpany_id from lib_report_template where  report_id=$cbo_report_name  and template_name=$cbo_company_id and module_id=$cbo_main_module and  status_active=1 and is_deleted=0 ");
		$company_id=$cbo_company_id; //$sql_data[0][csf('conmpany_id')];
		
		if (is_duplicate_field( "user_id", "user_priviledge_report_setting", "user_id=$cbo_user_name and company_id=$cbo_company_id   and module_id=$cbo_main_module and report_id=$cbo_report_name  and is_deleted=0" ) == 1)
		{
			echo "11**0"; disconnect($con); die;
		}
		$mst_id=$sql_data[0][csf('mst_id')];
		$report_id=$sql_data[0][csf('report_id')];
		$cbo_format_name=str_replace("'","",$cbo_format_name);
		
		$field_array="id,mst_id,user_id,company_id,module_id,report_id, format_id,inserted_by, insert_date, status_active, is_deleted";
		
		$id=return_next_id( "id", "user_priviledge_report_setting", 1 );
		$data_array="(".$id.",".$mst_id.",".$cbo_user_name.",".$company_id.",".$cbo_main_module.",".$cbo_report_name.",'".$cbo_format_name."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";     
		
		//echo "insert into lib_report_template($field_array) values".$data_array;die;
		$rID=sql_insert("user_priviledge_report_setting",$field_array,$data_array,1);

		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "0**".$id."**".str_replace("'","",$cbo_report_name)."**".str_replace("'","",$cbo_main_module)."";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**".$id."**".str_replace("'","",$cbo_report_name)."**".str_replace("'","",$cbo_main_module)."";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			    {
					oci_commit($con);   
					echo "0**".$id."**".str_replace("'","",$cbo_report_name)."**".str_replace("'","",$cbo_main_module)."";
				}
				else{
					oci_rollback($con);
				    echo "5**".$id."**".str_replace("'","",$cbo_report_name)."**".str_replace("'","",$cbo_main_module)."";
				}
		}
		disconnect($con);
		die;
	}
	else if($operation==1)   // Update Here
	{
       $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$sql_data=sql_select("select id as mst_id,template_name as conmpany_id from lib_report_template where  report_id=$cbo_report_name and  template_name=$cbo_company_id  and module_id=$cbo_main_module and  status_active=1 and is_deleted=0 ");
		$company_id=$cbo_company_id;//$sql_data[0][csf('conmpany_id')];
		$mst_id=$sql_data[0][csf('mst_id')];
		$report_id=$sql_data[0][csf('report_id')];
		$cbo_format_name=str_replace("'","",$cbo_format_name);
		if($update_id!="")
		{
			if (is_duplicate_field( "user_id", "user_priviledge_report_setting", " id!=$update_id and user_id=$cbo_user_name and company_id=$cbo_company_id   and module_id=$cbo_main_module and report_id=$cbo_report_name  and is_deleted=0" ) == 1)
			{
				echo "11**0"; disconnect($con); die;
			}
			$data_array_update="".$mst_id."*".$cbo_user_name."*".$company_id."*".$cbo_main_module."*".$cbo_report_name."*'".$cbo_format_name."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0"; 
		}
		
		if($update_id=="")
		{
			if (is_duplicate_field( "user_id", "user_priviledge_report_setting", "  user_id=$cbo_user_name and company_id=$cbo_company_id   and module_id=$cbo_main_module and report_id=$cbo_report_name  and is_deleted=0" ) == 1)
			{
				echo "11**0"; disconnect($con); die;
			}
			$data_array_update="".$mst_id."*".$cbo_user_name."*".$company_id."*".$cbo_main_module."*".$cbo_report_name."*'".$cbo_format_name."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0"; 
		}
		$field_array_update="mst_id*user_id*company_id*module_id*report_id*format_id*updated_by*update_date*status_active*is_deleted";
		
		//print_r($data_array_update);die;
		$rID=sql_update("user_priviledge_report_setting",$field_array_update,$data_array_update,"id",$update_id,1);
		//echo $rID;die;
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				//echo "1**".str_replace("'","",$update_id)."**0";
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$cbo_report_name)."**".str_replace("'","",$cbo_main_module)."";

			}
			else
			{
				mysql_query("ROLLBACK"); 
			//	echo "6**".str_replace("'","",$update_id)."**1";
				echo "6**".str_replace("'","",$update_id)."**".str_replace("'","",$cbo_report_name)."**".str_replace("'","",$cbo_main_module)."";

			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			    {
					oci_commit($con);   
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$cbo_report_name)."**".str_replace("'","",$cbo_main_module)."";
				}
				else{
					oci_rollback($con);
				echo "6**".str_replace("'","",$update_id)."**".str_replace("'","",$cbo_report_name)."**".str_replace("'","",$cbo_main_module)."";
				}
		}
		disconnect($con);
		die;
	}
	else if($operation==2)//Delete here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$rID=sql_delete("lib_report_template",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$cbo_company_id)."";  
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "7**".str_replace("'","",$update_id)."**".str_replace("'","",$cbo_company_id)."";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con); 
				echo "2**".str_replace("'","",$update_id)."**0";
			}
			else
			{ 
			    oci_rollback($con);
				echo "7**".str_replace("'","",$update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
		
}


 
if ($action=="load_php_data_to_form2")
{
 
	$nameArray=sql_select( "SELECT a.menu_name,a.root_menu,a.sub_root_menu,a.m_menu_id, b.show_priv,b.save_priv,b.edit_priv,b.delete_priv,b.approve_priv FROM main_menu a, user_priv_mst b WHERE b.id = '$data' AND a.m_menu_id = b.main_menu_id and a.status=1 ORDER BY main_menu_id ASC" );
	foreach ($nameArray as $inf)
	{
		echo "load_drop_down( 'requires/user_priviledge_controller', '".($inf[csf("root_menu")])."', 'cbo_root_menu_under', 'subrootdiv' );\n";
		echo "load_drop_down( 'requires/user_priviledge_controller', '".($inf[csf("sub_root_menu")])."', 'cbo_sub_root_menu_under', 'sub_subrootdiv' );\n";
		
		//echo "document.getElementById('cbo_main_menu_name').value = '".trim(($inf[csf("m_menu_id")]))."';\n";    
		//echo "document.getElementById('cbo_sub_main_menu_name').value  = '".($inf[csf("m_menu_id")])."';\n"; 
		//echo "document.getElementById('cbo_sub_menu_name').value  = '".($inf[csf("m_menu_id")])."';\n";  
		
		
		echo "document.getElementById('cbo_main_menu_name').value = '".trim(($inf[csf("root_menu")]))."';\n";    
		echo "document.getElementById('cbo_sub_main_menu_name').value  = '".($inf[csf("sub_root_menu")])."';\n"; 
		echo "document.getElementById('cbo_sub_menu_name').value  = '".($inf[csf("m_menu_id")])."';\n";  
		
		echo "document.getElementById('cbo_visibility').value  = '".($inf[csf("show_priv")])."';\n";
		echo "document.getElementById('cbo_insert').value  = '".($inf[csf("save_priv")])."';\n";  
		echo "document.getElementById('cbo_edit').value  = '".($inf[csf("edit_priv")])."';\n";  
		echo "document.getElementById('cbo_delete').value  = '".($inf[csf("delete_priv")])."';\n";  
		echo "document.getElementById('cbo_approve').value  = '".($inf[csf("approve_priv")])."';\n";  
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";     
		//echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_user_creation',1);\n";  
	}
}

if ($action=="load_drop_down_report_module")
{
	
	
	if($data==2)
	{
	$report_format_name="1,2,3,4,5,6,7,8,9,10,11,12";	
	}
	else if($data==6)
	{
	$report_format_name="11";	
	}
	else if($data==11)
	{
	$report_format_name="11,12";	
	}
	else
	{
		$report_format_name="0";	
	}
	
	
	echo create_drop_down( "cbo_report_name", 200, $report_name,"", 1, "--- Select Report ---", $selected, "load_drop_down( 'requires/report_setting_user_priviledge_report_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_report_name', 'report_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/report_setting_user_priviledge_report_controller' );","",$report_format_name );
}

if ($action=="load_drop_down_report_name")
{
	$data_ex=explode("_",$data);
	//echo $data_ex[0].'='.$data_ex[1];
	$sql_data=sql_select("select id as mst_id,template_name as company_id,module_id,report_id,format_id from  lib_report_template where report_id='$data_ex[0]' and template_name ='$data_ex[1]' and status_active=1 and is_deleted=0");  
	//print_r($data);
	$format_ids=$sql_data[0][csf('format_id')];
	$mst_ids=$sql_data[0][csf('mst_id')];
	$company_id=$sql_data[0][csf('company_id')];
	$report_format_id="$format_ids";
	//echo "document.getElementById('company_id').value  = '".$row[csf('company_id')]."';\n";
	//echo "document.getElementById('mst_update_id').value  = '".$row[csf('mst_id')]."';\n";
	echo create_drop_down( "cbo_format_name", 200, $report_format,"", 0, "--- Select Report ---", 1, "","",$report_format_id );
	
	
	
	
}
if ($action=="eval_multi_select")
{
 	echo "set_multiselect('cbo_format_name','0','0','','0');\n";
	exit();
}

if ($action=="load_priviledge_list")
{
  $data=explode("_", $data);
  //print_r($data);
  
	 ?>
    <table width="824"   border="0" cellpadding="0" cellspacing="2">
		
		<tr>
			<td width="150">Report Name</td>
            <td width="200" id="report_name_td">
					<?
                  echo create_drop_down( "cbo_main_menu_name", 200, $blank_array, 1, "-- Select Menu --", $selected, "load_drop_down( 'requires/user_priviledge_report_controller', this.value, 'load_drop_down_report_name', 'report_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/user_priviledge_report_controller' );" );
                    ?>
			</td>
           
			<td width="150">Print Format</td>
            <td width="200" id="report_td">
					<?
                    echo create_drop_down( "cbo_format_name", 200, $blank_array, 1, "-- Select Menu --", $selected, "" );
                    ?>
			</td>
		</tr>
		<tr>
			<td colspan="6" align="center"><input type="hidden" id="update_id" /> <input type="button" name="save" id="save" tabindex="3" class="formbutton" onclick="fnc_set_priviledge()" value="Set Priviledge" /> </td>
		</tr>
		<tr><td colspan="6" style="padding-top:10px;" id="load_list_priv"></td></tr>
        <script>
	set_multiselect('cbo_format_name','0','0','','0');
		 </script>
	</table>
    
     <?
}

if($action=="load_drop_down_report_list_view")
{
	
	$data=explode("_",$data);
	$user_id=$data[0];
	$report_id=$data[1];
	$module_id=$data[2];
	$company_id=$data[3];
	if($report_id==0) $report_id_cond=""; else $report_id_cond=" and report_id='$report_id'  "; 
	if($module_id==0) $module_id_cond=""; else $module_id_cond=" and module_id='$module_id'  "; 
	 $sql="select id, company_id, module_id, report_id,status_active, format_id from user_priviledge_report_setting where   user_id='$user_id' and company_id=$company_id   $module_id_cond and is_deleted=0 and status_active=1";
	$module_lib=return_library_array("select m_mod_id, main_module from main_module",'m_mod_id','main_module');

	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array(0=>$company_id,1=>$module_lib,2=>$report_name,3=>$row_status);
				
	echo create_list_view("list_view", "Company Name,Module Name,Report Name,Status", "100,100,100,60","550","240",0, $sql , "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id,module_id,report_id,status_active",$arr, "company_id,module_id,report_id,status_active", "requires/report_setting_user_priviledge_report_controller",'','0,0,0,0') ;
}


if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, company_id, module_id,report_id, format_id, status_active from user_priviledge_report_setting where id='$data' and status_active=1 ");
	foreach ($nameArray as $inf)
	{
	
		echo "document.getElementById('cbo_main_module').value = '".($inf[csf("module_id")])."';\n"; 
		echo "document.getElementById('cbo_report_name').value  = '".($inf[csf("report_id")])."';\n"; 
		//echo "load_drop_down( 'requires/report_settings_controller', document.getElementById('cbo_module_name').value, 'load_drop_down_report_name', 'report_td');\n";   
		echo "load_drop_down( 'requires/report_setting_user_priviledge_report_controller', document.getElementById('cbo_report_name').value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_report_name', 'report_td');get_php_form_data(this.value, 'eval_multi_select', 'requires/report_setting_user_priviledge_report_controller' );\n";   
	
		
		
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		
		echo "document.getElementById('cbo_format_name').value  = '".($inf[csf("format_id")])."';\n"; 
		//echo "set_multiselect('cbo_format_name','','1','".$inf[csf("format_id")]."','0');\n";
		echo "set_multiselect('cbo_format_name','0','1','".$inf[csf('format_id')]."','0');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_set_priviledge',1);\n"; 
		
		//get_php_form_data( this.value, 'eval_multi_select', 'requires/report_settings_controller' );
		  
 
	}
}
?>