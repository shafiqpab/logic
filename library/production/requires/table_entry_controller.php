<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=='load_drop_down_location') {
	//echo $data; die;
	//echo create_drop_down( "cbo_location_name", 262, "select location_name,id from lib_location where  company_id='$data' and is_deleted=0  and status_active=1  order by location_name",'id,location_name', 1, '--- Select Location ---', 0, '' );
	 
echo create_drop_down('cbo_location_name', 262, "select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1  order by location_name", 'id,location_name', 1, '--- Select Location ---', 0, "load_drop_down( 'requires/table_entry_controller', this.value, 'load_drop_down_floor', 'floor' )"  );
}

if ($action=='load_drop_down_floor') {
	//echo $data; die;
	echo create_drop_down( 'cbo_floor_name', 262, "select floor_name,id from  lib_prod_floor where location_id='$data' and is_deleted=0  and status_active=1  order by floor_name", 'id,floor_name', 1, '--- Select Floor ---', 0, '' );
}

if ($action=='table_entry_list_view') {
	// $floor=return_library_array( "select floor_name,id from  lib_prod_floor where is_deleted=0", "id", "floor_name"  );
	// $arr=array(2=>$floor);
	// echo  create_list_view ( "list_view", "Company,Location,Floor,Sewing Line,Sewing Group,Line Serial,Man Power", "120,120,80,80,70,70","650","220",1, "SELECT c.company_name,l.location_name,a.floor_name, a.sewing_line_serial, a.sewing_group, a.line_name,a.id,a.man_power from lib_table_entry a, lib_company c, lib_location l  where a.company_name=c.id and a.location_name=l.id and a.is_deleted=0  order by a.id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,0,floor_name", $arr , "company_name,location_name,floor_name,line_name,sewing_group,sewing_line_serial,man_power", "../production/requires/table_entry_controller", 'setFilterGrid("list_view",-1);' ) ;		

	$floor=return_library_array( 'select floor_name,id from lib_prod_floor where is_deleted=0', 'id', 'floor_name' );
    $arr=array(2=>$floor);
    echo create_list_view( 'list_view', 'Company,Location,Floor,Table Name,Table Group,Table Serial,Man Power', '150,120,80,80,120,100', '750', '220', 1, "select c.company_name, l.location_name, a.floor_name, a.table_name, a.table_group, a.table_sequence, a.id,a.man_power from lib_table_entry a, lib_company c, lib_location l where a.company_name=c.id and a.location_name=l.id and a.is_deleted=0 order by a.id desc", 'get_php_form_data', 'id', "'load_php_data_to_form'", 1, '0,0,floor_name', $arr , 'company_name,location_name,floor_name,table_name,table_group,table_sequence,man_power', '../production/requires/table_entry_controller', 'setFilterGrid("list_view",-1);' );
}

if ($action=='load_php_data_to_form') {
	$nameArray=sql_select( "select company_name, location_name, floor_name, table_sequence, table_type, table_name, table_group, table_status, id, man_power from lib_table_entry where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "load_drop_down( 'requires/table_entry_controller', '".($inf[csf("company_name")])."', 'load_drop_down_location', 'location' );\n";
		echo "load_drop_down( 'requires/table_entry_controller', '".($inf[csf("location_name")])."', 'load_drop_down_floor', 'floor' );\n";
		echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_name")])."';\n";    
		echo "document.getElementById('cbo_location_name').value  = '".($inf[csf("location_name")])."';\n"; 
		echo "document.getElementById('cbo_floor_name').value  = '".($inf[csf("floor_name")])."';\n";
		echo "document.getElementById('cbo_table_type').value  = '".($inf[csf("table_type")])."';\n";
		echo "document.getElementById('txt_table_sequence').value  = '".($inf[csf("table_sequence")])."';\n";
		echo "document.getElementById('txt_table_name').value  = '".($inf[csf("table_name")])."';\n";
		echo "document.getElementById('txt_table_group').value  = '".($inf[csf("table_group")])."';\n";
		echo "document.getElementById('cbo_table_status').value  = '".($inf[csf("table_status")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "document.getElementById('txt_man_power').value  = '".($inf[csf("man_power")])."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_saveUpdateDelete',1);\n";
	}
}

if ($action=='save_update_delete') {
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( 'table_name', 'lib_table_entry', "table_name=$txt_table_name and company_name=$cbo_company_name and location_name=$cbo_location_name and floor_name=$cbo_floor_name and is_deleted=0" ) == 1) {
			echo "11**0"; die;
		}

		$con = connect();
		if($db_type==0) {
			mysql_query("BEGIN");
		}
		//cbo_company_name,cbo_location_name,cbo_floor_name,txt_sewing_line_serial,txt_line_name,cbo_table_status,update_id
		//company_name,location_name,floor_name,sewing_line_serial,line_name,status_active,id
		
		$mstId=return_next_id('id', 'lib_table_entry', 1);
		$field_array="id,company_name,location_name,floor_name,table_type,table_sequence,table_name,table_group,man_power,table_status,inserted_by,insert_date";
		$data_array='('.$mstId.','.$cbo_company_name.','.$cbo_location_name.','.$cbo_floor_name.','.$cbo_table_type.','.$txt_table_sequence.','.$txt_table_name.','.$txt_table_group.','.$txt_man_power.','.$cbo_table_status.','.$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$rID=sql_insert('lib_table_entry', $field_array, $data_array, 1);
		if($db_type==0) {
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$mstId;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1) {
			 if($rID) {
				oci_commit($con);   
				echo "0**".$mstId;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	
	else if ($operation==1)   // Update Here
	{
		/*if (is_duplicate_field( 'table_name', 'lib_table_entry', "table_name=$txt_table_name and company_name=$cbo_company_name and location_name=$cbo_location_name and floor_name=$cbo_floor_name and is_deleted=0" ) == 1) {
			echo "11**0"; die;
		}*/

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		/*$field_array="id,company_name,location_name,floor_name,table_type,table_sequence,table_name,table_group,man_power,table_status,inserted_by,insert_date";
		$data_array='('.$mstId.','.$cbo_company_name.','.$cbo_location_name.','.$cbo_floor_name.','.$cbo_table_type.','.$txt_table_sequence.','.$txt_table_name.','.$txt_table_group.','.$txt_man_power.','.$cbo_table_status.','.$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";*/

		$field_array="company_name*location_name*floor_name*table_type*table_sequence*table_name*table_group*man_power*table_status*updated_by*update_date";
		$data_array="".$cbo_company_name.'*'.$cbo_location_name.'*'.$cbo_floor_name.'*'.$cbo_table_type.'*'.$txt_table_sequence.'*'.$txt_table_name.'*'.$txt_table_group.'*'.$txt_man_power.'*'.$cbo_table_status.'*'.$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update('lib_table_entry', $field_array, $data_array, 'id', ''.$update_id.'', 1);
		 
		if($db_type==0) {
			if($rID ){
				mysql_query("COMMIT");
				echo "1**".$update_id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$update_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
		 if($rID )
		    {
				oci_commit($con);   
				echo "1**".$update_id;
			}
			else{
				oci_rollback($con);
				echo "10**".$update_id;
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
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete('lib_table_entry', $field_array, $data_array, 'id', ''.$update_id.'', 1);
		
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