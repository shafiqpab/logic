<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']["user_id"];

if ($action=="cbo_root_menu")
{
	echo create_drop_down( "cbo_root_menu", 250, "select m_menu_id,menu_name from main_menu where position='1' and status_active=1 and is_deleted=0 and m_module_id='$data' and is_deleted=0 and status_active=1 and STATUS=1 order by menu_name","m_menu_id,menu_name", 1, "-- Select Menu Name --", $selected, "load_drop_down( 'requires/menu_create_controller', this.value, 'cbo_root_menu_under', 'subrootdiv' )" );
	exit();
}

if ($action=="cbo_root_menu_under")
{
	echo create_drop_down( "cbo_root_menu_under", 250, "select m_menu_id,menu_name from main_menu where position='2' and root_menu ='$data' order by menu_name","m_menu_id,menu_name", 1, "-- Select Menu Name --", $selected, "" );
	exit();
}

if ($action=="create_menu_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!='') $menu_name="$data[0]%"; else $menu_name="%%";
	if ($data[1]!=0) $m_module_id="$data[1]"; else $m_module_id="";
	if($data[1]==0)
	{
		echo "<b>Select Main Module Name then Enter Menu Name </b>";
		die;
	}
	else
	{
		$sql= "select m_menu_id,m_module_id,menu_name,root_menu,sub_root_menu,position,fabric_nature,slno from main_menu  where menu_name like '$menu_name' and m_module_id='$m_module_id' and status !=0 and status_active=1 and is_deleted=0 order by root_menu,sub_root_menu,slno ASC";
		$m_module_id=return_library_array( "select m_mod_id, main_module from main_module",'m_mod_id','main_module');
	    $arr=array (1=>$m_module_id,5=>$item_category);
	    echo  create_list_view ( "list_view", "ID,Module Name,Menu Name,Root Menu,Sub Root Menu,Fabric Nature,Position,Seq.", "60,120,200,50,50,75,50,50","720","300",1, $sql, "get_php_form_data", "m_menu_id","'load_php_data_to_form'", 1, "0,m_module_id,0,0,0,fabric_nature,0,0", $arr , "m_menu_id,m_module_id,menu_name,root_menu,sub_root_menu,fabric_nature,position,slno", "requires/menu_create_controller", 'setFilterGrid("list_view",-1);',"0,0,0,0,1,0,1,1" ) ;	
	}
	exit();
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select m_menu_id,m_module_id,menu_name,f_location,mobile_menu_link,root_menu,sub_root_menu,position,slno,report_menu,status,fabric_nature,IS_MOBILE_MENU from main_menu where m_menu_id='$data'" );
	foreach ($nameArray as $inf)
	{
		 
		if($inf[csf("report_menu")]==1) echo "$('#chk_report_menu').attr('checked',true);\n"; else  echo "$('#chk_report_menu').attr('checked',false);\n";

		if($inf[csf("IS_MOBILE_MENU")]==1){echo "$('#chk_mobile_menu').attr('checked',true);\n";} 
		else{echo "$('#chk_mobile_menu').attr('checked',false);\n";}

		echo "document.getElementById('chk_mobile_menu').value  = '".($inf[csf("menu_name")])."';\n"; 
		echo "document.getElementById('txt_menu_name').value  = '".($inf[csf("menu_name")])."';\n"; 
		echo "document.getElementById('txt_menu_link').value  = '".($inf[csf("f_location")])."';\n";
		echo "document.getElementById('txt_mobile_menu_link').value  = '".($inf[csf("mobile_menu_link")])."';\n";
		echo "document.getElementById('cbo_root_menu').value  = '".($inf[csf("root_menu")])."';\n"; 
		echo "document.getElementById('cbo_root_menu_under').value  = '".($inf[csf("sub_root_menu")])."';\n"; 
		echo "document.getElementById('txt_menu_seq').value  = '".($inf[csf("slno")])."';\n"; 
		echo "document.getElementById('cbo_menu_sts').value  = '".($inf[csf("status")])."';\n"; 
		echo "document.getElementById('update_id').value  = '".($inf[csf("m_menu_id")])."';\n";
		echo "document.getElementById('cbo_fabric_nature').value  = '".($inf[csf("fabric_nature")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_menu_create',1);\n"; 
 	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if( str_replace("'","",$cbo_root_menu) == 0 ) {
		$position = 1;
	}
	else {
		if( str_replace("'","",$cbo_root_menu_under) == 0 ) $position = 2;
		else $position = 3;
	}
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "m_menu_id", " main_menu", 1 ) ;
		$field_array="m_menu_id,m_module_id,root_menu,sub_root_menu,menu_name,f_location,mobile_menu_link,position,status,slno,report_menu,fabric_nature,is_mobile_menu,m_page_name,m_page_short_name,inserted_by,insert_date,status_active,is_deleted";
		$data_array="(".$id.",".$cbo_module_name.",".$cbo_root_menu.",".$cbo_root_menu_under.",".$txt_menu_name.",".$txt_menu_link.",".$txt_mobile_menu_link.",'".$position."',".$cbo_menu_sts.",".$txt_menu_seq.",".$chk_report_menu.",".$cbo_fabric_nature.",".$chk_mobile_menu.",".$txt_page_link.",".$txt_short_name.",".$user_id.",'".$pc_date_time."','1',0)";
		// echo "insert into main_menu ($field_array) values $data_array";die;
		$rID=sql_insert("main_menu",$field_array,$data_array,1);
		
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
		else if($db_type==2 || $db_type==1 )
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
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		 
		
		$field_array="m_module_id*root_menu*sub_root_menu*menu_name*f_location*mobile_menu_link*position*status*slno*report_menu*fabric_nature*is_mobile_menu*m_page_name*m_page_short_name*updated_by*update_date*status_active*is_deleted";
	    $data_array="".$cbo_module_name."*".$cbo_root_menu."*".$cbo_root_menu_under."*".$txt_menu_name."*".$txt_menu_link."*".$txt_mobile_menu_link."*'".$position."'*".$cbo_menu_sts."*".$txt_menu_seq."*".$chk_report_menu."*".$cbo_fabric_nature."*".$chk_mobile_menu."*".$txt_page_link."*".$txt_short_name."*".$user_id."*'".$pc_date_time."'*1*0";

		// echo $data_array;die; 

		$rID=sql_update("main_menu",$field_array,$data_array,"m_menu_id","".$update_id."",1);
		
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
		else if($db_type==2 || $db_type==1 )
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
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//$field_array="status*updated_by*update_date*status_active*is_deleted";
		//$data_array="'0'";
		$field_array="status*updated_by*update_date*status_active*is_deleted";
		$data_array="'0'*'".$user_id."'*'".$pc_date_time."'*0*1";

		$rID=sql_update("main_menu",$field_array,$data_array,"m_menu_id","".$update_id."",1);
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
		else if($db_type==2 || $db_type==1 )
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
?>