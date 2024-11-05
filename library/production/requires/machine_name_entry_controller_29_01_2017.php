<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_location")
{
 
	echo create_drop_down( "cbo_location_name", 162, "select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1  order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "load_drop_down( 'requires/machine_name_entry_controller', this.value, 'load_drop_down_floor', 'floor' )"  );
}

if ($action=="load_drop_down_floor")
{
 
	echo create_drop_down( "cbo_floor_name", 162, "select floor_name,id from  lib_prod_floor where location_id='$data' and is_deleted=0  and status_active=1  order by floor_name",'id,floor_name', 1, '--- Select Floor ---', 0, '' );
	 

}

if ($action=="machine_entry_list_view")
{
	 $location_name=return_library_array( "select location_name,id from  lib_location where is_deleted=0", "id", "location_name"  );
	 $floor=return_library_array( "select floor_name,id from  lib_prod_floor where is_deleted=0", "id", "floor_name"  );
	 $arr=array(0=>$location_name,1=>$floor, 3=>$machine_category);  
	 if ($data=="" || $data==0) $com=""; else $com=" and company_id='$data'";
     echo  create_list_view ( "list_view", "Location Name,Floor Name,Machine No,Category,Machine Group,Dia Width,Gauge", "100,100,100,120,80,80","700","220",1, "select location_id,floor_id,machine_no,category_id,machine_group,dia_width,gauge,id from lib_machine_name where is_deleted=0 $com", "get_php_form_data", "id","'load_php_data_to_form'", 1, "location_id,floor_id,0,category_id", $arr , "location_id,floor_id,machine_no,category_id,machine_group,dia_width,gauge", "../production/requires/machine_name_entry_controller", 'setFilterGrid("list_view",-1);','') ;

}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select company_id,location_id,floor_id,machine_no,category_id,machine_group,dia_width,gauge,extra_cylinder,no_of_feeder,attachment,prod_capacity,capacity_uom_id,brand,origin,purchase_date,purchase_cost,accumulated_dep,depreciation_rate,depreciation_method_id,remark,seq_no,status_active,id from  lib_machine_name where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "load_drop_down( 'requires/machine_name_entry_controller', '".($inf[csf("company_id")])."', 'load_drop_down_location', 'location' );\n";
		echo "load_drop_down( 'requires/machine_name_entry_controller', '".($inf[csf("location_id")])."', 'load_drop_down_floor', 'floor' );\n";
		
		echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_id")])."';\n";    
		echo "document.getElementById('cbo_location_name').value  = '".($inf[csf("location_id")])."';\n"; 
		echo "document.getElementById('cbo_floor_name').value  = '".($inf[csf("floor_id")])."';\n";
		echo "document.getElementById('txt_machine_no').value  = '".($inf[csf("machine_no")])."';\n";
		echo "document.getElementById('cbo_catagory').value  = '".($inf[csf("category_id")])."';\n";
		echo "document.getElementById('txt_group').value  = '".($inf[csf("machine_group")])."';\n";
		echo "document.getElementById('txt_dia_width').value  = '".($inf[csf("dia_width")])."';\n";
		echo "document.getElementById('txt_gauge').value  = '".($inf[csf("gauge")])."';\n";
		echo "document.getElementById('txt_extra_cylinder').value  = '".($inf[csf("extra_cylinder")])."';\n"; 
		echo "document.getElementById('txt_no_of_feeder').value  = '".($inf[csf("no_of_feeder")])."';\n";
		echo "document.getElementById('txt_attachment').value  = '".($inf[csf("attachment")])."';\n";
		echo "document.getElementById('txt_prod_capacity').value  = '".($inf[csf("prod_capacity")])."';\n";
		echo "document.getElementById('cbo_capacity_uom').value  = '".($inf[csf("capacity_uom_id")])."';\n";
		echo "document.getElementById('txt_brand').value  = '".($inf[csf("brand")])."';\n";
		echo "document.getElementById('txt_origin').value  = '".($inf[csf("origin")])."';\n";
		echo "document.getElementById('txt_purchase_date').value  = '".(change_date_format($inf[csf("purchase_date")]))."';\n";
		echo "document.getElementById('txt_purchase_cost').value  = '".($inf[csf("purchase_cost")])."';\n";
		echo "document.getElementById('txt_accumulated_dep').value  = '".($inf[csf("accumulated_dep")])."';\n";
		echo "document.getElementById('txt_depreciation_rate').value  = '".($inf[csf("depreciation_rate")])."';\n";
		echo "document.getElementById('cbo_depreciation_method').value  = '".($inf[csf("depreciation_method_id")])."';\n";
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('txt_remarks').value  = '".($inf[csf("remark")])."';\n";
		echo "document.getElementById('txt_seq_no').value  = '".($inf[csf("seq_no")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";  
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_machine_name_entry',1);\n";
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "machine_no", "lib_machine_name", " machine_no=$txt_machine_no and company_id=$cbo_company_name and location_id=$cbo_location_name and floor_id=$cbo_floor_name and is_deleted=0" ) == 1)
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
			     
			$id=return_next_id( "id", " lib_machine_name", 1 ) ;
			$field_array="id,company_id,location_id,floor_id,machine_no,category_id,machine_group,dia_width,gauge,extra_cylinder,no_of_feeder,attachment,prod_capacity,capacity_uom_id,brand,origin,purchase_date,purchase_cost,accumulated_dep,depreciation_rate,depreciation_method_id,remark,seq_no,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_floor_name.",".$txt_machine_no.",".$cbo_catagory.",".$txt_group.",".$txt_dia_width.",".$txt_gauge.",".$txt_extra_cylinder.",".$txt_no_of_feeder.",".$txt_attachment.",".$txt_prod_capacity.",".$cbo_capacity_uom.",".$txt_brand.",".$txt_origin.",".$txt_purchase_date.",".$txt_purchase_cost.",".$txt_accumulated_dep.",".$txt_depreciation_rate.",".$cbo_depreciation_method.",".$txt_remarks.",".$txt_seq_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
			$rID=sql_insert("lib_machine_name",$field_array,$data_array,1);
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
		if (is_duplicate_field( "machine_no", "lib_machine_name", " machine_no=$txt_machine_no and company_id=$cbo_company_name and id!=$update_id and location_id=$cbo_location_name and floor_id=$cbo_floor_name and is_deleted=0" ) == 1)
		
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
			
			$field_array="company_id*location_id*floor_id*machine_no*category_id*machine_group*dia_width*gauge*extra_cylinder*no_of_feeder*attachment*prod_capacity*capacity_uom_id*brand*origin*purchase_date*purchase_cost*accumulated_dep*depreciation_rate*depreciation_method_id*remark*seq_no*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_company_name."*".$cbo_location_name."*".$cbo_floor_name."*".$txt_machine_no."*".$cbo_catagory."*".$txt_group."*".$txt_dia_width."*".$txt_gauge."*".$txt_extra_cylinder."*".$txt_no_of_feeder."*".$txt_attachment."*".$txt_prod_capacity."*".$cbo_capacity_uom."*".$txt_brand."*".$txt_origin."*".$txt_purchase_date."*".$txt_purchase_cost."*".$txt_accumulated_dep."*".$txt_depreciation_rate."*".$cbo_depreciation_method."*".$txt_remarks."*".$txt_seq_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";
			
			 
			$rID=sql_update("lib_machine_name",$field_array,$data_array,"id","".$update_id."",1);
			 
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
		
		$rID=sql_delete("lib_machine_name",$field_array,$data_array,"id","".$update_id."",1);
		
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