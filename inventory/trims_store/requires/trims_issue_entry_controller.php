<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

/*################ This variable define for accessories controller approval and apply blue planet fashion.    ##################################*/
$accessories_con_app=0;
/*################ value 1 for controll and 0 not controll.  accessories controller  variable end.  ##################################*/


$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, supplier_id, company_location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$company_location_id = $userCredential[0][csf('company_location_id')];

if ($company_id !='') {
    $company_credential_cond = " and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = " and a.id in($store_location_id)"; 
}
if ($supplier_id !='') {
    $supplier_credential_cond = " and a.id in($supplier_id)";
}

if ($company_location_id !='') {
    $location_credential_cond = " and id in($company_location_id)";
}

if ($action=="com_wise_all_data")
{
	extract($_REQUEST);
	$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$data' and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	
	$location_data=sql_select("select ID, LOCATION_NAME from lib_location where company_id=$data $location_credential_cond and status_active =1 and is_deleted=0 order by location_name");
	$location_arr=array();
	foreach($location_data as $row)
	{
		$location_arr[$row["ID"]]=$row["LOCATION_NAME"];
	}
	unset($location_data);
	$js_location_arr= json_encode($location_arr);
	
	$print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=181 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);
	$js_printButton= json_encode($printButton);
	
	echo $variable_inventory."**".$js_location_arr."**".$js_printButton;
	exit();
}

if ($action=="stoe_wise_all_data")
{
	extract($_REQUEST);
	$data_ref=explode("_",$data);
	$store_id=$data_ref[0];
	$company_id=$data_ref[1];
	
	$floor_data=sql_select("select b.FLOOR_ID, a.FLOOR_ROOM_RACK_NAME from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name");
	$floor_arr=array();
	foreach($floor_data as $row)
	{
		$floor_arr[$row["FLOOR_ID"]]=$row["FLOOR_ROOM_RACK_NAME"];
	}
	unset($floor_data);
	$js_floor_arr= json_encode($floor_arr);
	
	$room_data=sql_select("select b.ROOM_ID, a.FLOOR_ROOM_RACK_NAME from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name");
	$room_arr=array();
	foreach($room_data as $row)
	{
		$room_arr[$row["ROOM_ID"]]=$row["FLOOR_ROOM_RACK_NAME"];
	}
	unset($room_data);
	$js_room_arr= json_encode($room_arr);
	
	$rack_data=sql_select("select b.RACK_ID, a.FLOOR_ROOM_RACK_NAME from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name");
	$rack_arr=array();
	foreach($rack_data as $row)
	{
		$rack_arr[$row["RACK_ID"]]=$row["FLOOR_ROOM_RACK_NAME"];
	}
	unset($rack_data);
	$js_rack_arr= json_encode($rack_arr);
	
	$self_data=sql_select("select b.SHELF_ID, a.FLOOR_ROOM_RACK_NAME from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
	$self_arr=array();
	foreach($self_data as $row)
	{
		$self_arr[$row["SHELF_ID"]]=$row["FLOOR_ROOM_RACK_NAME"];
	}
	unset($self_data);
	$js_self_arr= json_encode($self_arr);
	
	
	$bin_data=sql_select("select b.BIN_ID, a.FLOOR_ROOM_RACK_NAME from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.bin_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name order by a.floor_room_rack_name");
	$bin_arr=array();
	foreach($bin_data as $row)
	{
		$bin_arr[$row["BIN_ID"]]=$row["FLOOR_ROOM_RACK_NAME"];
	}
	unset($bin_data);
	$js_bin_arr= json_encode($bin_arr);
	
	
	
	echo $js_floor_arr."**".$js_room_arr."**".$js_rack_arr."**".$js_self_arr."**".$js_bin_arr;
	exit();
}

/*if ($action=="load_drop_down_location_by_swing")
{
	echo create_drop_down( "cbo_location_swing", 132, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}*/
/*if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 132, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/trims_issue_entry_controller*4', 'store','store_td', $('#cbo_company_id').val(), this.value);" );//load_drop_down( 'requires/grey_fabric_receive_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'cbo_floor' ); // load_floor();
	exit();
}*/

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 132, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", 0, "reset_on_change(this.id);load_drop_down('requires/trims_issue_entry_controller', this.value+'_'+$data[0], 'load_drop_down_store','store_td');" );
	exit();
}

if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_store_name", 132, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[1]' and a.location_id = $data[0] and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"reset_on_change(this.id);floor_room_rack(this.value)");
	exit();
}

if($action == "load_drop_floor")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "cbo_floor", "132", "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_id,floor_room_rack_name", 1, "--Select Floor--", 0, "" );
}

if($action == "load_drop_room")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];

	echo create_drop_down( "cbo_room", "132", "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select Room--", 0, "" );
}

if($action == "load_drop_rack")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "txt_rack", '132', "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select Rack--", 0, "" );
}

if($action == "load_drop_shelf")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "txt_shelf", '132', "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select Shelf--", 0, "" );
}

if($action == "load_drop_bin")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "cbo_bin", '132', "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.bin_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name order by a.floor_room_rack_name","bin_id,floor_room_rack_name", 1, "--Select Bin--", 0, "" );
}


/*if ($action=="load_room_rack_self_bin")
{
	$explodeData = explode('*', $data);
	$explodeData[11] = 'get_trim_stock();';
	$data=implode('*', $explodeData);
	load_room_rack_self_bin("requires/trims_issue_entry_controller",$data);
}*/

if ($action=="load_drop_down_location_by_swing")
{
	echo create_drop_down( "cbo_location_swing", 132, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/trims_issue_entry_controller', this.value+'_'+document.getElementById('cbo_sewing_company').value+'_'+document.getElementById('cbo_sewing_source').value+'_'+document.getElementById('cbo_issue_purpose').value, 'load_drop_down_floor', 'swing_floor_td');",0 );     	 
}

if($action=="load_drop_down_sewing_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];
	
	if($data[0]==1)
	{
		echo create_drop_down( "cbo_sewing_company", 132, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Sewing Company--", 0, "load_location(this.value);","" );
	}
	else if($data[0]==3)
	{
		echo create_drop_down( "cbo_sewing_company", 132, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company_id' and b.party_type in(22) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Sewing Company--", 0, "load_location(this.value);" );
	}
	else
	{
		echo create_drop_down( "cbo_sewing_company", 132, $blank_array,"",1, "--Select Sewing Company--", 0, "load_location(this.value);" );
	}
	exit();
}


// Floor drop down
if ($action=="load_drop_down_floor")
{
	//echo $data;die;
	$data=explode("_",$data);
	if($data[2]==1)
	{
		if($data[3]==36) $prod_source=5;
		else if($data[3]==37) $prod_source=3;
		else if($data[3]==41) $prod_source=1;
		else if($data[3]==42) $prod_source=11;
		else $prod_source='1,3,5,11';
		$location_cond="";
		if($data[0]>0) $location_cond=" and location_id=$data[0]";
		
		echo create_drop_down( "cbo_floor_swing", 100, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process in($prod_source) $location_cond  and company_id=$data[1] order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "get_php_form_data(document.getElementById('cbo_floor_swing').value,'line_disable_enable','requires/trims_issue_entry_controller'); load_drop_down( 'requires/trims_issue_entry_controller', this.value+'_'+$data[0]+'_'+$data[1], 'load_drop_down_sewing_line_floor', 'sewing_line_td' );",0 ); 
	}
	else
	{
		 echo create_drop_down( "cbo_floor_swing", 100, $blank_array,"", 1, "-- Select Floor --", 0, "load_drop_down( 'requires/trims_issue_entry_controller', this.value+'_'+$data[0]+'_'+$data[1], 'load_drop_down_sewing_line_floor', 'sewing_line_td' );" );
	}
}

//  line drop down
if($action=="load_drop_down_sewing_line_floor")
{
	$explode_data = explode("_",$data);
	$floor_cond="";
	if($explode_data[0]>0) $floor_cond=" and floor_name = $explode_data[0] ";
	echo create_drop_down( "cbo_sewing_line", 100, "select id,line_name,sewing_line_serial from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $floor_cond and company_name=$explode_data[2] and location_name=$explode_data[1] order by sewing_line_serial","id,line_name", 1, "--- Select ---", $selected, "",0,0 );	
	exit();
}

if($action=="create_itemDesc_search_list_view")
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$prod_id=$data[1];
	$store_id=$data[2];
	$store_update_upto=$data[3];
    $rack_shelf_array=array();
    $trim_group_arr =array();
	$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
		$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
	}

	$cumilite_issue_sql=sql_select("select b.prod_id, c.floor_id, c.room, c.rack, c.self, c.bin_box, b.trans_type, b.quantity, c.id as trans_id, c.cons_quantity as issue_cons_qnty, c.cons_amount as issue_cons_amt  
	from order_wise_pro_details b, inv_transaction c  
	where b.trans_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.entry_form in(25,49,78,112) and b.trans_type in (2,3,6) and c.transaction_type in (2,3,6) and b.po_breakdown_id in ($data[0]) and c.store_id=$store_id");
	$cumilite_issue_data=array();
	foreach($cumilite_issue_sql as $row)
	{
		if($row[csf("trans_type")]==2)
		{
			$cumilite_issue_data[$row[csf("prod_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["issue_qnty"]+=$row[csf("quantity")];
		}
		elseif($row[csf("trans_type")]==3)
		{
			$cumilite_issue_data[$row[csf("prod_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["rcv_rtn_qnty"]+=$row[csf("quantity")];
		}
		elseif($row[csf("trans_type")]==6)
		{
			$cumilite_issue_data[$row[csf("prod_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["trans_out_qnty"]+=$row[csf("quantity")];
		}
		
		if($trans_ids_check[$row[csf("trans_id")]]=="")
		{
			$trans_ids_check[$row[csf("trans_id")]]=$row[csf("trans_id")];
			$cumilite_issue_data[$row[csf("prod_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["issue_cons_qnty"]+=$row[csf("issue_cons_qnty")];
			$cumilite_issue_data[$row[csf("prod_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["issue_cons_amt"]+=$row[csf("issue_cons_amt")];
		}
	
	}
	
	
	
	
	$po_no_arr = return_library_array("select id, po_number from wo_po_break_down where id in($data[0])","id","po_number");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_arr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");

	$floor_room_rack_mst_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active=1 and is_deleted=0","floor_room_rack_id","floor_room_rack_name");
	
	
	if($db_type==0)
		$po_breakdown_id_cond=",group_concat(b.po_breakdown_id) as po_breakdown_id";
	else if($db_type==2)
		$po_breakdown_id_cond=",LISTAGG(b.po_breakdown_id,',') WITHIN GROUP ( ORDER BY b.po_breakdown_id) as po_breakdown_id";
	$sql = "SELECT a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.brand_supplier, a.current_stock, b.id as prop_id, b.quantity as recv_qty, c.id as trans_id, c.cons_quantity as recv_cons_qty, c.cons_amount, c.store_id, c.floor_id, c.room, c.rack, c.self, c.bin_box  $po_breakdown_id_cond
	from product_details_master a, order_wise_pro_details b, inv_transaction c
	where a.id=b.prod_id and b.trans_id=c.id and a.item_category_id='4' and a.entry_form=24 and b.trans_type in(1,4,5) and b.entry_form in(24,73,78,112) and b.po_breakdown_id in ($data[0]) and c.store_id=$store_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.current_stock>0 
	group by a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.current_stock, b.id, b.quantity, c.id, c.cons_quantity, c.cons_amount, c.store_id, c.floor_id, c.room, c.rack, c.self, c.bin_box 
	order by a.item_group_id, a.gmts_size";
	//echo $sql;
	$result = sql_select($sql);
	$item_order_data=$item_order_check=$item_tranaction_check=array();
	foreach($result as $row)
	{
		if($item_order_check[$row[csf("id")]][$row[csf("prop_id")]]=="")
		{
			$item_order_check[$row[csf("id")]][$row[csf("prop_id")]]=$row[csf("prop_id")];
			
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["id"]=$row[csf("id")];
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["item_group_id"]=$row[csf("item_group_id")];
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["product_name_details"]=$row[csf("product_name_details")];
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["brand_supplier"]=$row[csf("brand_supplier")];
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["color"]=$row[csf("color")];
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["gmts_size"]=$row[csf("gmts_size")];
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["brand_supplier"]=$row[csf("brand_supplier")];
			
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["item_color"]=$row[csf("item_color")];
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["item_size"]=$row[csf("item_size")];
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["current_stock"]=$row[csf("current_stock")];
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["prop_id"]=$row[csf("prop_id")];
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["recv_qty"]+=$row[csf("recv_qty")];

			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["store_id"]=$row[csf("store_id")];
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["floor_id"]=$row[csf("floor_id")];
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["room"]=$row[csf("room")];
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["rack"]=$row[csf("rack")];
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["self"]=$row[csf("self")];
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["bin_box"]=$row[csf("bin_box")];
		}
		
		if($item_tranaction_check[$row[csf("id")]][$row[csf("trans_id")]]=="")
		{
			$item_tranaction_check[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]][$row[csf("trans_id")]]=$row[csf("trans_id")];
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["recv_cons_qty"]+=$row[csf("recv_cons_qty")];
			$item_order_data[$row[csf("id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["cons_amount"]+=$row[csf("cons_amount")];
		}
	}	
	/*echo '<pre>';
	print_r($item_order_data);die;*/
	//echo $sql;
	if($store_update_upto>1) $table_width="900"; else $table_width="660";
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $table_width; ?>" class="rpt_table" id="table_header">
    	<thead>
			<th width="40">Prod. ID</th>
			<th width="70">Item Group</th>
			<th width="100">Item Desc.</th>               
			<th width="60">Item Color</th>
			<th width="50">Item Size</th>
            <th width="60">Gmts. Color</th>
			<th width="50">Gmts. Size</th>
            <th width="50">Brand Supplier</th>
            <?
			if($store_update_upto>1)
			{
				?>
                <th width="50">Floor</th>
                <th width="50">Room</th>
                <th width="50">Rack</th>
                <th width="50">Shelf</th>

                <th width="50">Bin</th>

                <?
			}
			?>
			<th width="60">Recv. Qty.</th>
            <th width="50">Cumulative Issue </th>
            <th>Issue Balance </th>
		</thead>
    </table>
		
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $table_width; ?>" class="rpt_table" id="tbl_list_search">  
		<?
		$i=1;
		foreach ($item_order_data as $item_id=>$item)
		{
			foreach ($item as $floor_id => $floor) 
			{
				foreach ($floor as $room_id => $room) 
				{
					foreach ($room as $rack_id => $rack) 
					{
						foreach ($rack as $self_id => $self) 
						{
							foreach ($self as $bin_id => $row) 
							{								
								
								
								$po_id_arr=array_unique(explode(",",chop($row[("po_breakdown_id")],",")));
								$all_po_no=$all_po_ids="";
								foreach($po_id_arr as $po_id)
								{
									$all_po_no.=$po_no_arr[$po_id].",";
									$all_po_ids.=$po_id.",";
								}
								$all_po_no=chop($all_po_no,",");$all_po_ids=chop($all_po_ids,",");
								
								$cu_issue=$balance=0;
								$current_stock=$row[('current_stock')];
								$cu_issue=$cumilite_issue_data[$row[('id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_id]["issue_qnty"]*$trim_group_arr[$row[('item_group_id')]]['conversion_factor'];
								$cu_cons_issue=$cumilite_issue_data[$row[('id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_id]["issue_cons_qnty"];
								if($cu_issue=="") $cu_issue=0;
								$receive_qnty=(($row[('recv_qty')]-$cumilite_issue_data[$row[('id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_id]["rcv_rtn_qnty"]-$cumilite_issue_data[$row[('id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_id]["trans_out_qnty"])*$trim_group_arr[$row[('item_group_id')]]['conversion_factor']);
								$balance=$receive_qnty-$cu_issue;
								$balance_cons_qnty=$row["recv_cons_qty"]-$cumilite_issue_data[$row[('id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_id]["issue_cons_qnty"];
								$balance_cons_amt=$row["cons_amount"]-$cumilite_issue_data[$row[('id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_id]["issue_cons_amt"];
								$cons_rate=$balance_cons_amt/$balance_cons_qnty;
								
								$data=$row[('id')]."**".$row[('item_group_id')]."**".$row[('product_name_details')]."**".$color_arr[$row[('item_color')]]."**".$row[('color')]."**".$row[('item_size')]."**".$row[('gmts_size')]."**".$row[('brand_supplier')]."**".$trim_group_arr[$row[('item_group_id')]]['uom']."**".$rack_id."**".$self_id."**".$row[('item_color')]."**".number_format($current_stock,2,'.','')."**".number_format($cu_issue,2,'.','')."**".number_format($balance,2,'.','')."**".number_format($receive_qnty,2,'.','')."**".$trim_group_arr[$row[('item_group_id')]]['conversion_factor']."**".$all_po_no."**".$all_po_ids."**".number_format($cons_rate,4,'.','')."**".$floor_id."**".$room_id."**".$bin_id;
								
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick='set_form_data("<? echo $data; ?>");change_color("tr_<? echo $i; ?>","<? echo $bgcolor;?>");' id="tr_<? echo $i; ?>" > 
									<td width="40"><? echo $row[('id')]; ?></td>
									<td width="70"><p><? echo $trim_group_arr[$row[('item_group_id')]]['name']; ?></p></td>  
									<td width="100"><p><? echo $row[('product_name_details')]; ?></p></td>             
									<td width="60"><p><? echo $color_arr[$row[('item_color')]]; ?>&nbsp;</p></td>
									<td width="50"><p><? echo $row[('item_size')]; ?>&nbsp;</p></td>
                                    <td width="60"><p><? echo $color_arr[$row[('color')]]; ?>&nbsp;</p></td>
									<td width="50"><p><? echo $size_arr[$row[('gmts_size')]]; ?>&nbsp;</p></td>
                                    <td width="50"><p><? echo $row[('brand_supplier')]; ?>&nbsp;</p></td>
                                    <?
									if($store_update_upto>1)
									{
										?>
										<td width="50"><p><? echo $floor_room_rack_mst_arr[$floor_id]; ?>&nbsp;</p></td>
                                        <td width="50"><p><? echo $floor_room_rack_mst_arr[$room_id]; ?>&nbsp;</p></td>
                                        <td width="50"><p><? echo $floor_room_rack_mst_arr[$rack_id]; ?>&nbsp;</p></td>
                                        <td width="50"><p><? echo $floor_room_rack_mst_arr[$self_id]; ?>&nbsp;</p></td>
                                        <td width="50"><p><? echo $floor_room_rack_mst_arr[$bin_id]; ?>&nbsp;</p></td>
										<?
									}
									?>
									<td align="right" width="60" title="<? echo "Total Receive Qnty + Total Transfer Qnty"; ?>"><? echo number_format($receive_qnty,2,'.',''); ?></td>
									<td align="right" width="50"><p><? echo number_format($cu_issue,2,'.',''); ?>&nbsp;</p></td>
									<td align="right"><p><? echo number_format($balance,2,'.',''); ?>&nbsp;</p></td>
								</tr>
								<?
								$i++;
							}
						}
					}
				}	
			}	
		}
		/*foreach ($item_order_data as $item_id=>$row)
		{
			$rack_shelf=explode(",",substr($rack_shelf_array[$row[('id')]],0,-1));
			foreach($rack_shelf as $value)
			{
				//print_r($rack_shelf);echo "jahid";
				$value=explode("**",$value);
				$rack=$value[0];
				$shelf=$value[1];
				
				$cons_rate=$row["cons_amount"]/$row["recv_cons_qty"];
				
				$po_id_arr=array_unique(explode(",",chop($row[("po_breakdown_id")],",")));
				$all_po_no=$all_po_ids="";
				foreach($po_id_arr as $po_id)
				{
					$all_po_no.=$po_no_arr[$po_id].",";
					$all_po_ids.=$po_id.",";
				}
				$all_po_no=chop($all_po_no,",");$all_po_ids=chop($all_po_ids,",");
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$cu_issue=$balance=0;
				$current_stock=$row[('current_stock')];
				$cu_issue=$cumilite_issue_data[$row[('id')]]["issue_qnty"]*$trim_group_arr[$row[('item_group_id')]]['conversion_factor'];
				$cu_cons_issue=$cumilite_issue_data[$row[('id')]]["issue_cons_qnty"];
				if($cu_issue=="") $cu_issue=0;
				$receive_qnty=(($row[('recv_qty')]-$cumilite_issue_data[$row[('id')]]["rcv_rtn_qnty"]-$cumilite_issue_data[$row[('id')]]["trans_out_qnty"])*$trim_group_arr[$row[('item_group_id')]]['conversion_factor']);
				$balance=$receive_qnty-$cu_issue;
				$balance_cons=$row[('recv_cons_qty')]-$cu_cons_issue;
				//$current_stock=$current_stock_arr[$row[('id')]];
				
				$data=$row[('id')]."**".$row[('item_group_id')]."**".$row[('product_name_details')]."**".$color_arr[$row[('item_color')]]."**".$row[('color')]."**".$row[('item_size')]."**".$row[('gmts_size')]."**".$row[('brand_supplier')]."**".$trim_group_arr[$row[('item_group_id')]]['uom']."**".$rack."**".$shelf."**".$row[('item_color')]."**".number_format($current_stock,2,'.','')."**".number_format($cu_issue,2,'.','')."**".number_format($balance,2,'.','')."**".number_format($receive_qnty,2,'.','')."**".$trim_group_arr[$row[('item_group_id')]]['conversion_factor']."**".$all_po_no."**".$all_po_ids."**".number_format($cons_rate,4,'.','');
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick='set_form_data("<? echo $data; ?>");change_color("tr_<? echo $i; ?>","<? echo $bgcolor;?>");' id="tr_<? echo $i; ?>" > 
					<td width="40"><? echo $row[('id')]; ?></td>
					<td width="70"><p><? echo $trim_group_arr[$row[('item_group_id')]]['name']; ?></p></td>  
					<td width="100"><p><? echo $row[('product_name_details')]; ?></p></td>             
					<td width="60"><p><? echo $color_arr[$row[('item_color')]]; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $row[('item_size')]; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $rack; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $shelf; ?>&nbsp;</p></td>
					<td align="right" width="60" title="<? echo "Total Receive Qnty + Total Transfer Qnty"; ?>"><? echo number_format($receive_qnty,2,'.',''); ?></td>
					<td align="right" width="50"><p><? echo number_format($cu_issue,2,'.',''); ?>&nbsp;</p></td>
					<td align="right"><p><? echo number_format($balance,2,'.',''); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;
			}	
		}*/
		?>
	</table>
	<?	
	exit();
}

if($action=="create_itemDesc_search_list_view_on_booking")
{
	$floor_room_rack_mst_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active=1 and is_deleted=0","floor_room_rack_id","floor_room_rack_name");

	$rack_shelf_array=array();
	$dataArray=sql_select("select a.prod_id, a.rack_no, a.self_no from inv_goods_placement a, inv_receive_master b where a.mst_id=b.id and a.entry_form=24 and b.entry_form=24 and b.booking_id=$data and b.booking_without_order=1 and b.receive_basis=2 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, a.rack_no, a.self_no");
	foreach($dataArray as $row)
	{
		$rack_shelf_array[$row[csf('prod_id')]].=$row[csf('rack_no')]."**".$row[csf('self_no')].",";
	}
	$trim_group_arr =array();
	$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
		$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
	}
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$sql = "SELECT a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.current_stock, sum(b.receive_qnty) as recv_qty, b.floor, b.room_no, b.rack_no, b.self_no, b.box_bin_no  
	from inv_receive_master r, product_details_master a, inv_trims_entry_dtls b 
	where r.id=b.mst_id and a.id=b.prod_id and a.item_category_id=4 and a.entry_form=24 and r.entry_form=24 and r.booking_id=$data and r.booking_without_order=1 and r.receive_basis=2 and r.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.current_stock>0 
	group by a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.current_stock, a.item_color, a.item_size, b.floor, b.room_no, b.rack_no, b.self_no, b.box_bin_no
	order by a.item_group_id";
	//echo $sql;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="630" class="rpt_table" id="tbl_list_search">
		<thead>
			<th width="40">Prod. ID</th>
			<th width="70">Item Group</th>
			<th width="100">Item Desc.</th>               
			<th width="60">Item Color</th>
			<th width="50">Item Size</th>
            <th width="50">Floor</th>
            <th width="50">Room</th>
            <th width="50">Rack</th>
            <th width="50">Shelf</th>
            <th width="50">Bin</th>
			<th>Recv. Qty.</th>
		</thead>
		<?
		$i=1;
		foreach ($result as $row)
		{  
			$rack_shelf=explode(",",substr($rack_shelf_array[$row[csf('id')]],0,-1));
			//print_r($rack_shelf);
			foreach($rack_shelf as $value)
			{
				$value=explode("**",$value);
				$rack=$value[0];
				$shelf=$value[1];
				
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
				$data=$row[csf('id')]."**".$row[csf('item_group_id')]."**".$row[csf('product_name_details')]."**".$color_arr[$row[csf('item_color')]]."**".$row[csf('color')]."**".$row[csf('item_size')]."**".$row[csf('gmts_size')]."**".$row[csf('brand_supplier')]."**".$trim_group_arr[$row[csf('item_group_id')]]['uom']."**".$row[csf('rack_no')]."**".$row[csf('self_no')]."**".$row[csf('item_color')]."**".$row[csf('current_stock')]."**".''."**".''."**".''."**".''."**".''."**".''."**".''."**".$row[csf('floor')]."**".$row[csf('room_no')]."**".$row[csf('box_bin_no')]; 
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick='set_form_data("<? echo $data ?>");change_color("tr_<? echo $i; ?>","<? echo $bgcolor;?>");' id="tr_<? echo $i; ?>" > 
					<td width="40"><? echo $row[csf('id')]; ?></td>
					<td width="70"><p><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></p></td>  
					<td width="100"><p><? echo $row[csf('product_name_details')]; ?></p></td>             
					<td width="60"><p><? echo $color_arr[$row[csf('item_color')]]; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $row[csf('item_size')]; ?>&nbsp;</p></td>					
					<td width="50"><p><? echo $floor_room_rack_mst_arr[$row[csf('floor')]]; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $floor_room_rack_mst_arr[$row[csf('room_no')]]; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $floor_room_rack_mst_arr[$row[csf('rack_no')]]; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $floor_room_rack_mst_arr[$row[csf('self_no')]]; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $floor_room_rack_mst_arr[$row[csf('box_bin_no')]]; ?>&nbsp;</p></td>
					<td align="right"><? echo number_format($row[csf('recv_qty')],2,'.',''); ?></td>
				</tr>
			<?
			$i++;
			}
		}
		?>
	</table>
	<?	
	exit();
}

if($action=="create_itemDesc_search_list_view_on_requisition")
{
	$rack_shelf_array=array();
	$dataArray=sql_select("select a.prod_id, a.rack_no, a.self_no from inv_goods_placement a, inv_receive_master b where a.mst_id=b.id and a.entry_form=24 and b.entry_form=24 and b.booking_id=$data and b.booking_without_order=1 and b.receive_basis=2 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, a.rack_no, a.self_no");
	foreach($dataArray as $row){
		$rack_shelf_array[$row[csf('prod_id')]].=$row[csf('rack_no')]."**".$row[csf('self_no')].",";
	}
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$trim_group_arr =array();
	$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
		$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
	}
	//$sql = "select a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.current_stock, sum(b.receive_qnty) as recv_qty from inv_receive_master r, product_details_master a, inv_trims_entry_dtls b where r.id=b.mst_id and a.id=b.prod_id and a.item_category_id=4 and a.entry_form=24 and r.entry_form=24 and r.booking_id=$data and r.booking_without_order=1 and r.receive_basis=2 and r.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.current_stock>0 group by a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.current_stock, a.item_color, a.item_size order by a.item_group_id";
	//listagg(CAST(invoice_no as VARCHAR(4000)),',') within group (order by invoice_no) as invoice_no
	//b.ID as PO_ID, b.PO_NUMBER

	$buyer_po_id_cond=",listagg(b.ID,',') within group (order by b.ID) as PO_ID";
	$buyer_po_no_cond=",listagg(CAST(b.PO_NUMBER as VARCHAR(4000)),',') within group (order by b.PO_NUMBER) as PO_NUMBER";

	$sql="SELECT d.ID, d.ITEM_GROUP_ID, d.PRODUCT_NAME_DETAILS, d.BRAND_SUPPLIER, d.COLOR, d.GMTS_SIZE, d.ITEM_COLOR, d.ITEM_SIZE, d.CURRENT_STOCK,d.UNIT_OF_MEASURE, SUM(c.REQSN_QTY) AS REQSN_QTY $buyer_po_id_cond $buyer_po_no_cond 
	from wo_po_details_master a, wo_po_break_down b, ready_to_sewing_reqsn c, product_details_master d 
	where a.job_no=b.job_no_mst and b.id=c.po_id and c.product_id=d.id and c.entry_form in(357,377) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.mst_id=$data and d.current_stock>0 
	group by d.id, d.item_group_id, d.product_name_details, d.brand_supplier, d.color, d.gmts_size, d.current_stock,d.unit_of_measure, d.item_color, d.item_size order by d.item_group_id";
	//echo $sql;
	$result = sql_select($sql);
	foreach ($result as $row)
	{ 
		$poBreakDownIds .=$row['PO_ID'].',';
	}
	$poBreakDownIds=chop($poBreakDownIds,',');
	$cumilite_issue_sql=sql_select("SELECT b.prod_id, sum(case when b.trans_type in (2) then  b.quantity else 0 end) as issue_qnty, sum(case when b.trans_type in (4) then  b.quantity else 0 end) as issue_rtn_qnty, sum(case when b.trans_type in (3) then  b.quantity else 0 end) as rcv_rtn_qnty, sum(case when b.trans_type in (6) then  b.quantity else 0 end) as trans_out_qnty, sum(case when c.transaction_type in (2) then  c.cons_quantity else 0 end) as issue_cons_qnty, sum(case when c.transaction_type in (4) then  c.cons_quantity else 0 end) as issue_rtn_cons_qnty  
	from order_wise_pro_details b, inv_transaction c  
	where b.trans_id=c.id and b.status_active=1 and b.is_deleted=0 and b.entry_form in(25,49,73,78,112) and b.trans_type in (2,3,4,6) and c.transaction_type in (2,3,4,6) and b.po_breakdown_id in ($poBreakDownIds) group by  b.prod_id");
	// and c.store_id=$store_id
	$cumilite_issue_data=array();
	foreach($cumilite_issue_sql as $row)
	{
		$cumilite_issue_data[$row[csf("prod_id")]]["issue_qnty"]=$row[csf("issue_qnty")]-$row[csf("issue_rtn_qnty")];
		$cumilite_issue_data[$row[csf("prod_id")]]["rcv_rtn_qnty"]=$row[csf("rcv_rtn_qnty")];
		$cumilite_issue_data[$row[csf("prod_id")]]["trans_out_qnty"]=$row[csf("trans_out_qnty")];
		$cumilite_issue_data[$row[csf("prod_id")]]["issue_cons_qnty"]=$row[csf("issue_cons_qnty")]-$row[csf("issue_rtn_cons_qnty")];
	}
	
	$rec_sql = sql_select("SELECT a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.current_stock, b.id as prop_id, b.po_breakdown_id, b.quantity as recv_qty, c.id as trans_id, c.cons_quantity as recv_cons_qty, c.cons_amount  
	from product_details_master a, order_wise_pro_details b, inv_transaction c
	where a.id=b.prod_id and b.trans_id=c.id and a.item_category_id='4' and a.entry_form=24 and b.trans_type in(1,5) and b.entry_form in(24,78,112) and b.po_breakdown_id in ($poBreakDownIds) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.current_stock>0 order by a.item_group_id");
	//and c.store_id=$store_id
	$rec_data=array();
	foreach($rec_sql as $row)
	{
		$rec_data[$row[csf("id")]]["recv_qty"]=$row[csf("recv_qty")];
		$rec_data[$row[csf("id")]]["recv_cons_qty"]=$row[csf("recv_cons_qty")];
		$rec_data[$row[csf("id")]]["cons_amount"]=$row[csf("cons_amount")];		
	} 
	/*echo "<pre>";
	print_r($rec_data);*/
	$conversion_arr = return_library_array("SELECT a.id, b.conversion_factor from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0",'id','conversion_factor' );

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="490" class="rpt_table" id="table_header">
		<thead>
			<th width="40">Prod. ID</th>
			<th width="70">Item Group</th>
			<th width="100">Item Desc.</th>
			<th width="60">Item Color</th>
			<th width="50">Item Size</th>
			<th width="50">Rack</th>
            <th width="50">Shelf</th>
			<th>Req. Qty.</th>
		</thead>
	</table>

	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="490" class="rpt_table" id="tbl_list_search">	
		<?
		$i=1;
		/*echo "<pre>";
		print_r($result);*/
		foreach ($result as $row)
		{  
			$rack_shelf=explode(",",substr($rack_shelf_array[$row['ID']],0,-1));
			//print_r($rack_shelf);
			foreach($rack_shelf as $value)
			{
				$value=explode("**",$value);
				$rack=$value[0];
				$shelf=$value[1];
				$cu_issue=$balance=0;
				$current_stock=$row[('CURRENT_STOCK')];
				$cu_issue=$cumilite_issue_data[$row[('ID')]]["issue_qnty"]*$conversion_arr[$row['ID']];
				$cu_cons_issue=$cumilite_issue_data[$row[('ID')]]["issue_cons_qnty"];
				if($cu_issue=="") $cu_issue=0;
				$receive_qnty=(($rec_data[$row['ID']]['recv_qty']-$cumilite_issue_data[$row[('ID')]]["rcv_rtn_qnty"]-$cumilite_issue_data[$row[('ID')]]["trans_out_qnty"])*$conversion_arr[$row['ID']]);

				$balance=$row['REQSN_QTY']-$cu_issue;
				//$cons_rate=$row["cons_amount"]/$row["recv_cons_qty"];
				$cons_rate=$rec_data[$row['ID']]['cons_amount'] / $rec_data[$row['ID']]['recv_cons_qty'] ;
				//$balance_cons=$rec_data[$row["ID"]["recv_cons_qty"]]-$cu_cons_issue;

				$po_id_arr=array_unique(explode(",",chop($row[("PO_ID")],",")));
				$po_no_arr=array_unique(explode(",",chop($row[("PO_NUMBER")],",")));
				$all_po_no=$all_po_ids="";
				foreach($po_id_arr as $po_id)
				{
					//$all_po_no.=$po_no_arr[$po_id].",";
					$all_po_ids.=$po_id.",";
				}
				foreach($po_no_arr as $po_no)
				{
					$all_po_no.=$po_no.",";
				}
				$all_po_no=chop($all_po_no,",");$all_po_ids=chop($all_po_ids,",");

				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$data=$row['ID']."**".$row['ITEM_GROUP_ID']."**".$row['PRODUCT_NAME_DETAILS']."**".$color_arr[$row['ITEM_COLOR']]."**".$row['COLOR']."**".$row['ITEM_SIZE']."**".$row['GMTS_SIZE']."**".$row['BRAND_SUPPLIER']."**".$row['UNIT_OF_MEASURE']."**".$rack."**".$shelf."**".$row['ITEM_COLOR']."**".number_format($current_stock,2,'.','')."**".number_format($cu_issue,2,'.','')."**".number_format($balance,2,'.','')."**".number_format($receive_qnty,2,'.','')."**".$conversion_arr[$row['ID']]."**".$all_po_no."**".$all_po_ids."**".number_format($cons_rate,4,'.','');
				//echo $data.'===';
				//"17455**1**Main Label, 0**Dark Cayan**0**0**0**0**1******8**36728.49**6.00($cu_issue)**20.04($balance)**15.00($receive_qnty)**3**DXKOPD**38554**10.3200"

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick='set_form_data("<? echo $data ?>");change_color("tr_<? echo $i; ?>","<? echo $bgcolor;?>");' id="tr_<? echo $i; ?>" > 
					<td width="40"><? echo $row['ID']; ?></td>
					<td width="70"><p><? echo $trim_group_arr[$row['ITEM_GROUP_ID']]['name']; ?></p></td>  
					<td width="100"><p><? echo $row['PRODUCT_NAME_DETAILS']; ?></p></td>             
					<td width="60"><p><? echo $color_arr[$row['item_color']]; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $row['ITEM_SIZE']; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $rack; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $shelf; ?>&nbsp;</p></td>
					<td align="right"><? echo number_format($row['REQSN_QTY'],2,'.',''); ?></td>
				</tr>
			<?
			$i++;
			}
		}
		?>
	</table>
	<?	
	exit();
}

if ($action=="po_search_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);  

?> 
	<script> 
		
		function fn_show_check()
		{
			/*if( form_validation('cbo_buyer_name','Buyer Name')==false )
			{
				return;
			}*/		
			show_list_view ( $('#txt_search_common').val()+'_'+$('#cbo_search_by').val()+'_'+<? echo $cbo_company_id; ?>+'_'+$('#cbo_buyer_name').val()+'_'+'<? echo $all_po_id; ?>'+'_'+$('#txt_date_from').val()+'_'+$('#txt_date_to').val()+'_'+$('#cbo_year_selection').val()+'_'+$('#cbo_string_search_type').val(), 'create_po_search_list_view', 'search_div', 'trims_issue_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
			set_all();
		}
		
		var selected_id = new Array(); var selected_name = new Array(); var buyer_id=''; var buyer_name='';
		
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				if($('#search'+i).css('display')!='none')
				{
					js_set_value( i );
				}
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function set_all()
		{
			var old=document.getElementById('txt_po_row_id').value; 
			if(old!="")
			{   
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{   
					js_set_value( old[k] ) 
				} 
			}
		}
		
		function js_set_value( str ) 
		{
			var color=document.getElementById('search' + str ).style.backgroundColor;
			var txt_buyer=$('#txt_buyer' + str).val();
			
			//if(color!='yellow' && selected_id.length>0 && $('#txt_buyer' + str).css('display') != 'none')
			if(color!='yellow' && selected_id.length>0 && $('#txt_buyer' + str).is(':visible'))
			{
				if(buyer_name=="")
				{
					buyer_name=txt_buyer;
				}
				else if(buyer_name*1!=txt_buyer*1)
				{
					alert("Buyer Mix Not Allowed");
					return;
				}
			}
			
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			var total_selected_val=$('#hidden_selected_row_total').val()*1;
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				total_selected_val=total_selected_val+$('#txt_individual_qty' + str).val()*1;				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				total_selected_val=total_selected_val-$('#txt_individual_qty' + str).val()*1;
			}
			
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			buyer_id=$('#txt_buyer' + str).val();

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hidden_order_id').val(id);
			$('#hidden_order_no').val(name);
			$('#hide_buyer').val(buyer_id);

			$('#hidden_selected_row_total').val( total_selected_val.toFixed(2));

			if(id!=""){
				var no_of_roll = id.split(',').length;
			}else{
				var no_of_roll = "0";
			}
			$('#hidden_selected_row_count').val(no_of_roll);
			
		}
		
		function hidden_field_reset()
		{
			$('#hidden_order_id').val('');
			$('#hidden_order_no').val( '' );
			$('#hide_buyer').val( '' );
			selected_id = new Array();
			selected_name = new Array();
		}
		
    </script>

</head>
<body>
	<div align="center">
        <form name="searchdescfrm" id="searchdescfrm" autocomplete=off>
            <fieldset style="width:880px; ">
                <input type="hidden" name="hidden_order_id" id="hidden_order_id" class="text_boxes" value="">
                <input type="hidden" name="hidden_order_no" id="hidden_order_no" class="text_boxes" value="">
                <input type="hidden" name="hide_buyer" id="hide_buyer" class="text_boxes" value="">
                <table cellpadding="0" cellspacing="0"  width="830" class="rpt_table" border="1" rules="all">
                    <thead>
                    	<tr>
                            <th colspan="5"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
                        </tr>
                    	<tr>
                        	<th>Buyer</th>
                            <th>Search By</th>
                            <th>Search</th>
                            <th>Shipment Date Range</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                                <input type="hidden" name="po_id" id="po_id" value="">
                            </th>
                        </tr>
                         
                    </thead>
                    <tr class="general">
                        <td align="center">
                            <?
                                echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $buyer_name, "","" ); 
                            ?>       
                        </td>
                        <td align="center">	
                            <?
                                $search_by_arr=array(1=>"PO No",2=>"Job No",3=>"Style Ref.",4=>"Internal Ref.");
                                echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                            ?>
                        </td>
						
                                      
                        <td align="center">				
                            <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                        </td> 
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
				   	    </td> 						

                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check()" style="width:100px;" />
                        </td>
                    </tr>
					<tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
                </table>
            <div id="search_div" style="margin-top:10px"></div>
            </fieldset>
        </form>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_po_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string=trim($data[0]);
	$search_by=$data[1];
	$company_id =$data[2];
	$buyer_id =$data[3];
	$all_po_id=$data[4];
	$start_date =$data[5];
	$end_date =$data[6];
	$po_year =$data[7];
	$search_string_type =$data[8];
	$sql_searce_cond="";
	if($search_string!="")
	{
		if($search_by==1)
		{
			if($search_string_type==1) $sql_searce_cond .=" and b.po_number = '".$search_string."'";
			else if ($search_string_type==2) $sql_searce_cond .=" and b.po_number like '".$search_string."%'";
			else if ($search_string_type==3) $sql_searce_cond .=" and b.po_number like '%".$search_string."'";
			else if ($search_string_type==4 || $search_string==0) $sql_searce_cond .=" and b.po_number like '%".$search_string."%'";
			
		}
		else if($search_by==2)
		{
			if($search_string_type==1) $sql_searce_cond .=" and a.job_no = '".$search_string."'";
			else if ($search_string_type==2) $sql_searce_cond .=" and a.job_no like '".$search_string."%'";
			else if ($search_string_type==3) $sql_searce_cond .=" and a.job_no like '%".$search_string."'";
			else if ($search_string_type==4 || $search_string==0) $sql_searce_cond .=" and a.job_no like '%".$search_string."%'";
		}
		else if($search_by==3)
		{
			if($search_string_type==1) $sql_searce_cond .=" and a.style_ref_no = '".$search_string."'";
			else if ($search_string_type==2) $sql_searce_cond .=" and a.style_ref_no like '".$search_string."%'";
			else if ($search_string_type==3) $sql_searce_cond .=" and a.style_ref_no like '%".$search_string."'";
			else if ($search_string_type==4 || $search_string==0) $sql_searce_cond .=" and a.style_ref_no like '%".$search_string."%'";
		}
		else
		{
			if($search_string_type==1) $sql_searce_cond .=" and b.grouping = '".$search_string."'";
			else if ($search_string_type==2) $sql_searce_cond .=" and b.grouping like '".$search_string."%'";
			else if ($search_string_type==3) $sql_searce_cond .=" and b.grouping like '%".$search_string."'";
			else if ($search_string_type==4 || $search_string==0) $sql_searce_cond .=" and b.grouping like '%".$search_string."%'";
		}
	}

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	/* else
	{
		if($db_type==0){$date_cond=" and year(b.pub_shipment_date)=".$po_year."";}
		else{$date_cond=" and to_char(b.pub_shipment_date,'YYYY')=".$po_year."";}	
	} */
	
	if($all_po_id!="")
		$po_id_cond=" or b.id in($all_po_id)";
	else 
		$po_id_cond="";
	
	$hidden_po_id=explode(",",$all_po_id);
	
	if(str_replace("'","",$buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$buyer_id";
	}
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	$buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer","id","buyer_name");
	$buyer_season_arr = return_library_array("SELECT id, season_name from lib_buyer_season","id","season_name");
	
	$sql = "SELECT a.job_no_prefix_num, a.job_no, a.style_ref_no, a.season_buyer_wise, a.buyer_name, a.order_uom, $year_field b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date, b.grouping as int_ref 
	from wo_po_details_master a, wo_po_break_down b 
	where a.job_no=b.job_no_mst and a.company_name=$company_id $sql_searce_cond $date_cond  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond
	order by b.id desc"; 
	//echo $sql;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table" style="margin-left:2px">
            <thead>
                <th width="40">SL</th>
                <th width="110">Buyer</th>
                <th width="60">Year</th>
                <th width="70">Job No</th>
                <th width="110">Style Ref.</th>
                <th width="80">Season</th>
                <th width="110">PO No</th>
                <th width="100">Internal Ref.</th>
                <th width="90">PO Quantity</th>
                <th width="60">UOM</th>
                <th>Shipment Date</th>
            </thead>
        </table>
        <div style="width:950px; overflow-y:scroll; max-height:200px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1; $po_row_id='';
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						
					if(in_array($selectResult[csf('id')],$hidden_po_id)) 
					{
						if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
					}
							
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                        <td width="40" align="center">
                            <? echo $i; ?>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
                            <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $selectResult[csf('po_number')]; ?>"/>
                            <input type="hidden" name="txt_buyer" id="txt_buyer<?php echo $i ?>" value="<? echo $selectResult[csf('buyer_name')]; ?>"/>
							<input type="hidden" name="txt_individual_qty[]" id="txt_individual_qty<?php echo $i; ?>" value="<?php echo $selectResult[csf('po_qnty_in_pcs')]; ?>"/>
                        </td>
                        <td width="110"><p><? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>	
                        <td align="center" width="60"><p><? echo $selectResult[csf('year')]; ?></p></td>
                        <td width="70"><p><? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td>
                        <td width="110"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                        <td width="80"><p><? echo $buyer_season_arr[$selectResult[csf('season_buyer_wise')]]; ?></p></td>
                        <td width="110"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                        <td width="100"><p><? echo $selectResult[csf('int_ref')]; ?></p></td>
                        <td width="90" align="right"><? echo $selectResult[csf('po_qnty_in_pcs')]; ?>&nbsp;</td> 
                        <td width="60" align="center"><p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
                        <td align="center"><? echo change_date_format($selectResult[csf('pub_shipment_date')]); ?></td>	
                    </tr>
                    <?
                    $i++;
				}
			?>
				<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<? echo $po_row_id; ?>"/>
            </table>
        </div>
         <table width="958" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr class="tbl_bottom">
				<td>
					<div style="width:100%"> 
						<div style="width:50%; float:left" align="left">&nbsp;&nbsp;
						Count of Selected Row= <input type="text"  style="width:50px" class="text_boxes_numeric" name="hidden_selected_row_count" id="hidden_selected_row_count" readonly value="0">
						</div>
						<div style="width:50%; float:left" align="left">
						Selected Row Total= <input type="text"  style="width:70px" class="text_boxes_numeric" name="hidden_selected_row_total" id="hidden_selected_row_total" readonly value="0">
						</div>
					</div>
				</td>
			</tr>
            <tr>
                <td align="center" valign="bottom">
                    <div style="width:100%"> 
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>           
<?
	
exit();
}

if ($action=="booking_search_popup")
{
    echo load_html_head_contents("Sample Trims Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);  
	?> 
	<script> 
		function js_set_value( id, name, buyer_id ) 
		{
			$('#hidden_booking_id').val(id);
			$('#hidden_booking_no').val(name);
			$('#hide_buyer').val(buyer_id);
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
	<div align="center">
        <form name="searchdescfrm" id="searchdescfrm" autocomplete=off>
            <fieldset style="width:780px;margin-left:5px">
                <input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes" value="">
                <input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
                <input type="hidden" name="hide_buyer" id="hide_buyer" class="text_boxes" value="">
                <table cellpadding="0" cellspacing="0" width="760" class="rpt_table" border="1" rules="all">
                    <thead>
                        <th>Buyer</th>
                        <th>Booking/Requisition No</th>
                        <th>Date Range</th>
                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" /></th> 
                    </thead>
                    <tr class="general">
                        <td align="center">
                            <?
                                echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $buyer_name, "","" ); 
                            ?>       
                        </td>
                      	<td align="center">				
                            <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                        </td>      
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                        </td>            
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_basis; ?>, 'create_trims_booking_search_list_view', 'search_div', 'trims_issue_entry_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
            <div id="search_div" style="margin-top:10px"></div>
            </fieldset>
        </form>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_trims_booking_search_list_view")
{
	$data = explode("_",$data);
	$buyer_id =$data[0];
	$search_string="%".trim($data[1]);
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$cbo_basis =$data[5];
	if($cbo_basis==2)
	{
		if(str_replace("'","",$buyer_id)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and buyer_id=$buyer_id";
		}
		
		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and booking_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and booking_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
			}
		}
		else
		{
			$date_cond="";
		}
	
		if($db_type==0) $year_field="YEAR(insert_date)"; 
		else if($db_type==2) $year_field="to_char(insert_date,'YYYY')";
		else $year_field="";//defined Later
		
		$sql = "select id, buyer_id, booking_no_prefix_num, booking_no, booking_date, delivery_date, currency_id, source, supplier_id, $year_field as year FROM wo_non_ord_samp_booking_mst WHERE company_id=$company_id and status_active =1 and is_deleted=0 and item_category=4 and booking_no like '$search_string' $buyer_id_cond $date_cond order by id";
		//echo $sql;
		$result = sql_select($sql);
		$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="772" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="80">Booking No</th>
				<th width="60">Year</th>
				<th width="110">Buyer</th>
				<th width="90">Booking Date</th>               
				<th width="130">Supplier</th>
				<th width="90">Delivary date</th>
				<th width="80">Source</th>
				<th>Currency</th>
			</thead>
		</table>
		<div style="width:772px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="list_view">  
			<?
				$i=1;
				foreach ($result as $row)
				{  
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('buyer_id')]; ?>');"> 
						<td width="30"><? echo $i; ?></td>
						<td width="80"><p>&nbsp;&nbsp;<? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
						<td width="60" align="center"><? echo $row[csf('year')]; ?></td>
						<td width="110"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
						<td width="90" align="center"><? echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>               
						<td width="130"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
						<td width="90" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
						<td width="80"><p><? echo $source[$row[csf('source')]]; ?></p></td>
						<td><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
					</tr>
				<?
				$i++;
				}
				?>
			</table>
		</div>
		<?
	}
	else
	{

		if(str_replace("'","",$buyer_id)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1){
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
				}else{
				$buyer_id_cond="";
			}
		}else{
			$buyer_id_cond=" and c.buyer_name=$buyer_id";
		}

		if($start_date!="" && $end_date!=""){
			if($db_type==0){
				$date_cond="and a.READY_SEWING_DATE between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
				$date_cond_req="and a.REQUISITION_DATE between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
			}else{
				$date_cond="and a.READY_SEWING_DATE between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
				$date_cond_req="and a.REQUISITION_DATE between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
				
			}
		}else{
			$date_cond="";
		}
		//$po_sql="select a.JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, b.ID as PO_ID, b.PO_NUMBER, b.PO_QUANTITY, (a.TOTAL_SET_QNTY*b.PO_QUANTITY) as PO_QNTY_IN_PCS, d.ID as PRODUCT_ID, d.ITEM_GROUP_ID, d.ITEM_DESCRIPTION, d.ITEM_COLOR, d.ITEM_SIZE, d.UNIT_OF_MEASURE, c.ID as DTLS_ID, c.CONS as RCV_QNTY, c.STOCK_QNTY as STOCK_QNTY, c.REQSN_QTY from wo_po_details_master a, wo_po_break_down b, ready_to_sewing_reqsn c, product_details_master d where a.job_no=b.job_no_mst and b.id=c.po_id and c.product_id=d.id and c.entry_form in(357) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.mst_id=$update_id";
		$req_sql="SELECT a.ID, a.SEW_NUMBER as REQ_NO, a.COMPANY_ID, a.READY_SEWING_DATE as REQUISITION_DATE, c.BUYER_NAME, 1 as TYPE
		from  READY_TO_SEWING_MST a, READY_TO_SEWING_REQSN b, WO_PO_DETAILS_MASTER c, WO_PO_BREAK_DOWN d 
		WHERE a.id=b.mst_id and c.job_no=d.job_no_mst and d.id=b.po_id and a.ID like '$search_string' and a.COMPANY_ID=$company_id $buyer_id_cond $date_cond and b.ENTRY_FORM in(357,377) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and d.STATUS_ACTIVE=1 and d.IS_DELETED=0 
		group by a.id, a.SEW_NUMBER, a.company_id, a.READY_SEWING_DATE, c.buyer_name
		union all
		select a.ID, a.REQ_NO as REQ_NO, a.COMPANY_ID, a.REQUISITION_DATE as REQUISITION_DATE, c.BUYER_NAME, 2 as TYPE
		from READY_TO_SEWING_REQSN_MST a, READY_TO_SEWING_REQSN b, WO_PO_DETAILS_MASTER c, WO_PO_BREAK_DOWN d 
		where a.id=b.mst_id and c.job_no=d.job_no_mst and d.id=b.po_id and a.req_no like '$search_string' and a.COMPANY_ID=$company_id $buyer_id_cond $date_cond_req and b.ENTRY_FORM in(357,377) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and d.STATUS_ACTIVE=1 and d.IS_DELETED=0
		group by a.id, a.REQ_NO, a.company_id, a.REQUISITION_DATE, c.buyer_name";
		//echo $req_sql;
		$result=sql_select($req_sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="772" class="rpt_table">
			<thead>
				<th width="50">SL</th>
				<th width="250">Buyer</th>
				<th width="200">Requisition No</th>
				<th>Requisition Date</th>
			</thead>
		</table>
		<div style="width:772px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="list_view">  
			<?
				$i=1;
				$buyer_arr = return_library_array("SELECT buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name",'id','buyer_name');
				//$store = return_library_array("SELECT a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0  group by a.id,a.store_name order by a.store_name", 'id', 'store_name');
				//$location = return_library_array("SELECT id, location_name from lib_location", "id", "location_name");
				foreach ($result as $row)
				{  
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row['ID']; ?>,'<? echo $row['REQ_NO']; ?>','<? echo $row['BUYER_NAME']; ?>');">
						<td width="50" align="center"><? echo $i; ?></td>
						<td width="250"><p>&nbsp;<? echo $buyer_arr[$row['BUYER_NAME']]; ?></p></td>
						<td width="200" align="center"><p>&nbsp;<? echo $row['REQ_NO']; ?></p></td>
						<td align="center"><? echo change_date_format($row[csf('REQUISITION_DATE')]); ?>&nbsp;</td>               
					</tr>
				<?
				$i++;
				}
				?>
			</table>
		</div>
		<?
	}	
	exit();
}

if($action=="po_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $prod_id;die;
	//echo $update_id;
	?> 
	<script>
		function distribute_qnty()
		{
			var tot_balance_qnty=$('#tot_balance_qnty').val()*1;
			var txt_prop_issue_qnty=$('#txt_prop_issue_qnty').val()*1;
			var tblRow = $("#tbl_list_search tbody tr").length;
			
			if(txt_prop_issue_qnty>0)
			{
				$('#txt_prop_ship_trims_qty').val("").attr('disabled',true);
			}
			else
			{
				$('#txt_prop_ship_trims_qty').attr('disabled',false);
			}
			
			
			if(txt_prop_issue_qnty>tot_balance_qnty)
			{
				alert("Issue Quantity Not Allow Over Stock Quantity.");
				$('#txt_prop_issue_qnty').val("");
				$('#txt_prop_issue_qnty').focus();
				$("#tbl_list_search tbody").find('tr').each(function()
				{
					if($(this).find('input[name="txtIssueQnty[]"]').prop('disabled') == false)
					{
						$(this).find('input[name="txtIssueQnty[]"]').val("");
					}
					
				});
				calculate_total();
				return;
			}
			
			$("#tbl_list_search tbody").find('tr').each(function()
			{
				if($(this).find('input[name="txtIssueQnty[]"]').prop('disabled') == false)
				{
					$(this).find('input[name="txtIssueQnty[]"]').val("");
				}
			});
			
			
			var balance =txt_prop_issue_qnty;
			var tblRow = $("#tbl_list_search tbody tr").length;
			if(txt_prop_issue_qnty>0)
			{
				for(var i=1;i<=tblRow;i++)
				{
					var RcvBalance=$('#txtIssueQnty_'+i).attr('placeholder')*1;
					var issue_qnty=(txt_prop_issue_qnty/tot_balance_qnty)*RcvBalance;
					if(RcvBalance>0 && txt_prop_issue_qnty>0 && $('#txtIssueQnty_'+i).prop('disabled') == false )
					{
						if(balance<RcvBalance)
						{
							$('#txtIssueQnty_'+i).val(balance.toFixed(6));
							break;
						}
						else
						{
							$('#txtIssueQnty_'+i).val(issue_qnty.toFixed(6));
							balance=(balance*1)-(issue_qnty*1).toFixed(6);
						}
					}
				}
			}
			
			/*if(txt_prop_issue_qnty>0)
			{
				$("#tbl_list_search tbody").find('tr').each(function()
				{
					if($(this).find('input[name="txtIssueQnty[]"]').prop('disabled') == false)
					{
						//alert(1);
						var txtPoQnty_placeholder=$(this).find('input[name="txtIssueQnty[]"]').attr('placeholder')*1;
						var issue_qnty=(txt_prop_issue_qnty/tot_balance_qnty)*txtPoQnty_placeholder;
						$(this).find('input[name="txtIssueQnty[]"]').val(issue_qnty.toFixed(4));
					}
				});
			}*/
			
			calculate_total();
		}
		
		
		
		function distribute_ship_qnty()
		{
			var tot_po_qnty=$('#tot_po_qnty').val()*1;
			var txt_prop_issue_qnty=$('#txt_prop_ship_trims_qty').val()*1;
			var tblRow = $("#tbl_list_search tbody tr").length;
			var len=totalIssue=totalTrims=0;
			var balance =txt_prop_issue_qnty;
			
			
			if(txt_prop_issue_qnty>0)
			{
				$('#txt_prop_issue_qnty').val("").attr('disabled',true);
			}
			else
			{
				$('#txt_prop_issue_qnty').attr('disabled',false);
			}
			
			$("#tbl_list_search tbody").find('tr').each(function()
			{
				$(this).find('input[name="txtIssueQnty[]"]').val("");
			});
			if(txt_prop_issue_qnty>0)
			{
				for(var i=1;i<=tblRow;i++)
				{
					var RcvBalance=$('#txtIssueQnty_'+i).attr('placeholder')*1;
					var trims_qnty=RcvBalance;
					if(RcvBalance>0 && txt_prop_issue_qnty>0)
					{
						if(balance<trims_qnty)
						{
							$('#txtIssueQnty_'+i).val(balance.toFixed(6));
							break;
						}
						else
						{
							$('#txtIssueQnty_'+i).val(trims_qnty.toFixed(6));
							balance=(balance*1)-(trims_qnty*1).toFixed(6);
							
						}
					}
				}
			}
			
			calculate_total();
		}
		
		function calculate_total()
		{
			var tblRow = $("#tbl_list_search tbody tr").length;
			var total_issue=0;
			var gmt_qty=0;
			for(var i=1;i<=tblRow;i++)
			{
				var issue_qnty=$('#txtIssueQnty_'+i).val()*1;
				var gmt_qty_id=$('#txtGarmentsQnty_'+i).val()*1;
				total_issue=total_issue*1+issue_qnty;
				gmt_qty=gmt_qty*1+gmt_qty_id;
			}
			$('#total_issue').html(total_issue.toFixed(6));
			 $('#gmt_qty_tot').html(gmt_qty);
		}
		
		function fn_placeholde_check(i)
		{
			if($('#txtIssueQnty_'+i).val()*1>$('#txtIssueQnty_'+i).attr('placeholder')*1)
			{
				$('#txtIssueQnty_'+i).val("");
				alert("Issue Quantity Not Allow Over Balance Quantity");
				return;
			}
		}
		
		
		
		function fnc_close()
		{
			var save_string='';	 var tot_trims_qnty=''; var po_id_array = new Array(); var po_no='';
			var garments_qty='';
			var conversion_factor=$('#conversion_factor').val()*1;
			$("#tbl_list_search").find('tbody tr').each(function()
			{
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				var txtPoName=$(this).find('input[name="txtPoName[]"]').val();
			    garments_qty=garments_qty*1+$(this).find('input[name="txtGarmentsQnty[]"]').val()*1;
				var txtIssueQnty=(($(this).find('input[name="txtIssueQnty[]"]').val()*1)/conversion_factor).toFixed(6);
				// alert(garments_qty);

				tot_trims_qnty=tot_trims_qnty*1+$(this).find('input[name="txtIssueQnty[]"]').val()*1;
				//alert($(this).find('input[name="txtIssueQnty[]"]').val()/conversion_factor);
				if(txtIssueQnty*1>0)
				{
					
					if(save_string=="")
					{
						save_string=txtPoId+"_"+txtIssueQnty;
					}
					else
					{
						save_string+=","+txtPoId+"_"+txtIssueQnty;
					}
					
					if(jQuery.inArray( txtPoId, po_id_array) == -1 ) 
					{
						po_id_array.push(txtPoId);
						if(po_no=="") po_no=txtPoName; else po_no+=","+txtPoName;
					}
				}

			});
				//  alert(garments_qty);
			
			$('#save_string').val( save_string );
			$('#tot_trims_qnty').val(tot_trims_qnty.toFixed(6));
			$('#all_po_id').val( po_id_array );
			$('#all_po_no').val( po_no );
			$('#total_garmrnts_qty').val( garments_qty);
			$('#distribution_method').val( $('#cbo_distribiution_method').val() );	

			var mandatory_gmts=$('#mandatory_qmt_qty').val();

			if(mandatory_gmts==1){
				if(garments_qty>0){
					parent.emailwindow.hide();
				}else{
					alert("Please Give Garments Qty");
					return;
				}
			}else{
				parent.emailwindow.hide();
			}

			
			
		}
		
		
    </script>

</head>

<body>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:620px;margin-left:10px">
            <input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
            <input type="hidden" name="tot_trims_qnty" id="tot_trims_qnty" class="text_boxes" value="">
            <input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
            <input type="hidden" name="total_garmrnts_qty" id="total_garmrnts_qty" class="text_boxes" value="">
            <input type="hidden" name="all_po_no" id="all_po_no" class="text_boxes" value="">
            <input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
            <input type="hidden" name="conversion_factor" id="conversion_factor" class="text_boxes" value="<? echo $conversion_factor; ?>">
            <div style="width:600px; margin-top:10px" align="center">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="550" align="center">
                	<thead>
                    	<tr>
                            <th>&nbsp;&nbsp;&nbsp;</th>
                            <th><b>Proportionately</b></th>
                            <th><b>Ship Date Wise</b></th>
                        </tr>
                    </thead>
                    <tr>
                        <td width="250" align="right"><b>Total Issue Qty : &nbsp;&nbsp;</b></td>
                        <td><input type="text" name="txt_prop_issue_qnty" id="txt_prop_issue_qnty" class="text_boxes_numeric" style="width:120px" onBlur="distribute_qnty()" /></td>
                        <td align="center"><input type="text" name="txt_prop_ship_trims_qty" id="txt_prop_ship_trims_qty" class="text_boxes_numeric" style="width:100px" onBlur="distribute_ship_qnty()" /></td>
                    </tr>
                </table>
            </div>
			<div style="margin-left:20px; margin-top:10px">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="660">
                    <thead>
                        <th width="130">Style No</th>
                        <th width="130">PO No</th>
                        <th width="80">Shipment Date</th>
                        <th width="100">PO Qnty</th>
                        <th width="80">Issue Qnty</th>
                        <th width="80"> Garments Qty</th>
                    </thead>
                </table>
                <div style="width:680px; max-height:280px; overflow-y:scroll" id="list_container" align="left">
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="660" id="tbl_list_search">
                        <tbody>
                        <?
                        $i=1; $tot_issue_qnty=''; $room_rack_cond=''; $po_qnty_array=array();
                        if($cbo_floor!="" && $cbo_floor!=0 ) $room_rack_cond.="and a.floor_id=$cbo_floor";
                        if($cbo_room!="" && $cbo_room!=0 ) $room_rack_cond.="and a.room=$cbo_room";
                        if($txt_rack!="" && $txt_rack!=0 ) $room_rack_cond.="and a.rack=$txt_rack";
                        if($txt_shelf!="" && $txt_shelf!=0 ) $room_rack_cond.="and a.self=$txt_shelf";
                        if($cbo_bin!="" && $cbo_bin!=0 ) $room_rack_cond.="and a.bin_box=$cbo_bin";
						
						//else $item_size_cond="a.item_size=0";


                        $explSaveData = explode(",",$save_data);
                        for($z=0;$z<count($explSaveData);$z++)
                        {
                            $po_wise_data = explode("_",$explSaveData[$z]);
                            $order_id=$po_wise_data[0];
                            $issue_qnty=$po_wise_data[1]*$conversion_factor;
                            $po_qnty_array[$order_id]=number_format($issue_qnty,6,".","");
                        }
						//echo "<pre>";print_r($po_qnty_array);//die;
						$acces_con_data=array();
						if($all_po_id!="")
						{
							//echo "select po_breakdown_id from order_wise_pro_details where status_active=1 and entry_form=25 and po_breakdown_id in($all_po_id) and prod_id=$prod_id group by po_breakdown_id"; 
							$first_entry_sql=sql_select("select po_breakdown_id from order_wise_pro_details where status_active=1 and entry_form=25 and po_breakdown_id in($all_po_id) and prod_id=$prod_id group by po_breakdown_id");
							//print_r( $first_entry_sql);//die;
						}
						//echo $accessories_con_app."=".$all_po_id."=".count($first_entry_sql);die;
						if($accessories_con_app==1 && $all_po_id!="" && count($first_entry_sql)>0)
						{
							//echo "select po_break_down_id, prod_id, approval_status from accessories_item_approval where po_break_down_id in($all_po_id)";die;
							$sql_acces_con=sql_select("select po_break_down_id, prod_id, approval_status from accessories_item_approval where po_break_down_id in($all_po_id)");
							
							foreach($sql_acces_con as $row)
							{
								$acces_con_data[$row[csf("po_break_down_id")]][$row[csf("prod_id")]]=$row[csf("approval_status")];
							}
							//print_r($acces_con_data);//die;
						}
						
                        if($all_po_id!="")
                        {
							$all_po_idd=explode(",",$all_po_id);
							$all_po_idd="'".implode("','", $all_po_idd)."'";
							$conversion_fac=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b "," a.item_group_id=b.id and a.id=$prod_id","conversion_factor");
							if($cbo_basis==3)
							{
								$reqsn_qty_sql=sql_select("select po_id as po_breakdown_id, sum(reqsn_qty) as reqsn_qty from ready_to_sewing_reqsn where po_id in ($all_po_idd) and product_id=$prod_id and mst_id=$txt_booking_id and is_deleted=0 and status_active=1 and ENTRY_FORM=357 group by po_id");
								foreach ($reqsn_qty_sql as $row)
								{
									$reqsnQty_array[$row[csf('po_breakdown_id')]]=$row[csf('reqsn_qty')];
								}
								
								$po_qty_sql=sql_select("SELECT b.po_breakdown_id, sum(b.quantity) as quantity  
								from inv_transaction a, order_wise_pro_details b 
								where a.id=b.trans_id and a.store_id=$cbo_store_name and a.RECEIVE_BASIS=3 and a.PI_WO_BATCH_NO=$txt_booking_id and b.po_breakdown_id in ($all_po_idd) and b.prod_id=$prod_id and b.trans_type in(2) and b.entry_form in(25) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $room_rack_cond group by b.po_breakdown_id");
								foreach ($po_qty_sql as $row)
								{
									$poQty_array[$row[csf('po_breakdown_id')]]=$row[csf('quantity')];
								}
								//$conversion_fac=1;
							}
							else
							{
								$check_rec_po=sql_select("SELECT b.po_breakdown_id, sum((case when b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.trans_type in(2,3,6) then b.quantity else 0 end)) as balance  
								from inv_transaction a, order_wise_pro_details b 
								where a.id=b.trans_id and a.store_id=$cbo_store_name and b.po_breakdown_id in ($all_po_idd) and b.prod_id=$prod_id and b.trans_type in(1,2,3,4,5,6) and b.entry_form in(24,25,49,73,78,112) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $room_rack_cond group by b.po_breakdown_id");
								foreach ($check_rec_po as $row)
								{
									$po_array[$row[csf('po_breakdown_id')]]=$row[csf('balance')]*$conversion_fac;
								}
							}		
	
                            $po_sql="SELECT b.id, a.buyer_name, a.style_ref_no, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date 
							from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c 
							where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.trans_type in(1,5) and c.entry_form in(24,78,112) and b.id in ($all_po_id)  and c.status_active=1 and c.is_deleted=0 

							group by b.id, a.buyer_name, a.style_ref_no, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date 
							order by b.pub_shipment_date";
					    }
						//echo $po_sql;
                        //echo "<pre>";print_r($po_sql);

						$sql_variable_auto = sql_select("SELECT ALLOW_FIN_FAB_RCV FROM variable_settings_production WHERE variable_list = 76 AND company_name =".$cbo_company_id." AND status_active = 1 AND is_deleted=0");
						$varibal_qty=$sql_variable_auto[0]["ALLOW_FIN_FAB_RCV"];
						//echo $update_id;
                        $nameArray=sql_select($po_sql);
                        foreach($nameArray as $row)
                        {
                            if ($i%2==0)
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                                
                            $issue_qnty=$po_qnty_array[$row[csf('id')]];
                            $tot_issue_qnty+=$issue_qnty;
							$po_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
							$tot_po_qnty+=$po_qnty_in_pcs;
							$order_idd=$po_array[$row[csf('id')]];
							//echo $order_idd;
							if($order_idd!=""){
								$bgcolorr="green";
							}else{
								$bgcolorr="red";
							}
							$acces_con_check=1;
							if($accessories_con_app==1 && $po_qnty_array[$row[csf('id')]]=="" && count($first_entry_sql)>0)
							{
								//echo "<pre>";print_r($acces_con_data);
								//echo $acces_con_data[$row[csf('id')]][$prod_id]."= $prod_id";die;
								if($acces_con_data[$row[csf('id')]][$prod_id]==1)
								{
									$acces_con_check=1;
									$disable_field="";
								}
								else
								{
									$acces_con_check=0;
									$disable_field='disabled="disabled"';
								}
							}
							//if($acces_con_check)
							//{
							?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td align="center" width="130"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                                <td width="130">
                                    <p style="color:<? echo $bgcolorr; ?>"><b><? echo $row[csf('po_number')]; ?></b></p>
                                    <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
                                    <input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
                                </td>
                                <td align="center" width="80"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                                <td width="100" align="right">
                                    <? echo $po_qnty_in_pcs; ?>&nbsp;
                                    <input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_qnty_in_pcs; ?>">
                                </td>
                                <td width="80" align="center">
                                	<?
                                	if($cbo_basis==3){
										if(str_replace("'","",$update_id)>0)
                                			$balance=number_format((($reqsnQty_array[$row[csf('id')]]+$issue_qnty)-($poQty_array[$row[csf('id')]])*$conversion_fac),6,".","") ;
										else
											$balance=number_format(($reqsnQty_array[$row[csf('id')]]-($poQty_array[$row[csf('id')]])*$conversion_fac),6,".","") ;

                                	} else {
                                		$balance=number_format(($po_array[$row[csf('id')]]+$issue_qnty),6,".","");
                                	}
                                	//$balance=number_format(($po_array[$row[csf('id')]]+$issue_qnty),2,".",""); ?>
                                    <input type="text" name="txtIssueQnty[]" id="txtIssueQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="calculate_total();" value="<? echo $issue_qnty; ?>" placeholder="<? if($acces_con_check) echo $balance; else echo "Approve Needed"; ?>" onBlur="fn_placeholde_check(<? echo $i; ?>)" <? echo $disable_field; ?> >
                                </td>
								<td width="80" align="center">
                                    <input type="text" style="width:60px" onKeyUp="calculate_total();" name="txtGarmentsQnty[]" class="text_boxes_numeric" id="txtGarmentsQnty_<? echo $i; ?>" value="">
                                </td>
                            </tr>
							<?
                            $i++;
							$total_place_holder_bal+=$balance;
							//}
                        }
                        ?>
                        	
                        </tbody>
                        <tfoot class="tbl_bottom">
                            <td colspan="4">Total</td>
                            <td id="total_issue"><? echo number_format($tot_issue_qnty,2); ?>
                            </td>
                            <td id="gmt_qty_tot"></td>
                        </tfoot>
                    </table>
                </div>
                <table width="580">
                     <tr>
                        <td align="center" >
                            <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
							<input type="hidden" id="mandatory_qmt_qty" name="" >
                            <input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo number_format($tot_po_qnty,4,".",""); ?>">
                            <input type="hidden" name="tot_balance_qnty" id="tot_balance_qnty" class="text_boxes" value="<? echo number_format($total_place_holder_bal,4,".",""); ?>">
                        </td>
                    </tr
                ></table>
            </div>
		</fieldset>
	</form>
</body>           
<script>
	var data = "<?php echo"$varibal_qty"?>";
	$("#mandatory_qmt_qty").val(data);
</script>
		
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="get_trim_cum_info")
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$prod_id=$data[1];
	
	//$current_stock=return_field_value("(sum(case when transaction_type=1 then cons_quantity else 0 end) - sum(case when transaction_type=2 then cons_quantity else 0 end)) as current_stock","inv_transaction","status_active=1 and is_deleted=0 and prod_id=$prod_id and item_category=4","current_stock");
	//$current_stock=return_field_value("current_stock","product_details_master","id=$prod_id");
	$stockData=sql_select("select a.current_stock, b.conversion_factor from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.id=$prod_id");
	$current_stock=$stockData[0][csf('current_stock')];
	$conversion_factor=$stockData[0][csf('conversion_factor')];
	if($conversion_factor<=0) $conversion_factor=1;
	
	/*$dataArray=sql_select("select sum(case when trans_type=1 and entry_form=24 then quantity end) as recv_qnty, sum(case when trans_type=2 and entry_form=25 then quantity end) as issue_qnty from order_wise_pro_details where po_breakdown_id in($po_id) and prod_id='$prod_id' and status_active=1 and is_deleted=0");*/
	
	$dataArray=sql_select("select sum(case when trans_type in(1,4,5) then quantity end) as recv_qnty, sum(case when trans_type in(2,3,6) then quantity end) as issue_qnty from order_wise_pro_details where po_breakdown_id in($po_id) and prod_id='$prod_id' and status_active=1 and is_deleted=0 and entry_form in(24,25,49,73,78,112)");
	
	$recv_qnty=$dataArray[0][csf('recv_qnty')]*$conversion_factor;
    $yet_to_issue = ($recv_qnty-($dataArray[0][csf('issue_qnty')]*$conversion_factor));
	
    echo "$('#txt_received_qnty').val(".number_format(($recv_qnty),2,".","").");\n";
    echo "$('#txt_cumulative_issued').val('".number_format(($dataArray[0][csf('issue_qnty')]*$conversion_factor),2,".","")."');\n";
    echo "$('#txt_yet_to_issue').val('".number_format(($yet_to_issue),2,".","")."');\n";
	echo "$('#txt_global_stock').val('".number_format(($current_stock),2,".","")."');\n";
	exit();
}

if ($action=="get_trim_cum_info_for_trims_booking")
{
	$data=explode("**",$data);
	$txt_booking_id=$data[0];
	$prod_id=$data[1];
	
	//$current_stock=return_field_value("current_stock","product_details_master","id=$prod_id");
	//$current_stock=return_field_value("(sum(case when transaction_type=1 then cons_quantity else 0 end) - sum(case when transaction_type=2 then cons_quantity else 0 end)) as current_stock","inv_transaction","status_active=1 and is_deleted=0 and prod_id=$prod_id and item_category=4","current_stock");
	
	$stockData=sql_select("select a.current_stock, b.conversion_factor from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.id=$prod_id");
	$current_stock=$stockData[0][csf('current_stock')];
	$conversion_factor=$stockData[0][csf('conversion_factor')];
	if($conversion_factor<=0) $conversion_factor=1;

	$recv_qnty=return_field_value("sum(b.receive_qnty) as recv_qnty","inv_receive_master a, inv_trims_entry_dtls b","a.id=b.mst_id and a.receive_basis=2 and a.entry_form=24 and a.item_category=4 and a.booking_id=$txt_booking_id and a.booking_without_order=1 and b.prod_id=$prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","recv_qnty");
	$recv_qnty=$recv_qnty*$conversion_factor;
	
	$iss_qnty=return_field_value("sum(b.issue_qnty) as iss_qnty","inv_issue_master a, inv_trims_issue_dtls b","a.id=b.mst_id and a.issue_basis=2 and a.entry_form=25 and a.booking_id=$txt_booking_id and b.prod_id=$prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","iss_qnty");
	
    $yet_to_issue = $recv_qnty-$iss_qnty;
	
    echo "$('#txt_received_qnty').val(".$recv_qnty.");\n";
    echo "$('#txt_cumulative_issued').val('".$iss_qnty."');\n";
    echo "$('#txt_yet_to_issue').val('".$yet_to_issue."');\n";
	echo "$('#txt_global_stock').val('".$current_stock."');\n";
	exit();
}

if ($action=="get_trim_trans_floor_room_rack")
{
	$data=explode("**",$data);
	$txt_booking_id=$data[0];
	$prod_id=$data[1];
	$store_id=$data[2];
	$all_po_id=$data[3];
	$all_po_no=$data[4];
	// echo $txt_booking_id.'='.$prod_id.'='.$store_id.'='.$all_po_id.'='.$all_po_no;

	$sql="SELECT A.FLOOR_ID, A.ROOM, A.RACK, A.SELF, A.BIN_BOX, A.COMPANY_ID
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id and a.store_id=$store_id and a.prod_id=$prod_id and a.transaction_type=1 and a.item_category=4 and b.entry_form=24 and b.po_breakdown_id in($all_po_id) group by a.floor_id, a.room, a.rack, a.self, a.bin_box, a.company_id";
	/*$sql="SELECT A.FLOOR_ID, A.ROOM, A.RACK, A.SELF, A.BIN_BOX, A.COMPANY_ID
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id and a.transaction_type=1 and a.item_category=4 and b.entry_form=24 group by a.floor_id, a.room, a.rack, a.self, a.bin_box, a.company_id";*/
	// echo $sql;die;
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{
		$floor_ids.= $row['FLOOR_ID'].',';
		$room_ids.= $row['ROOM'].',';
		$rack_ids.= $row['RACK'].',';
		$self_ids.= $row['SELF'].',';
		$bin_box_ids.= $row['BIN_BOX'].',';
	}
	$floor_ids=chop($floor_ids,",");
	$room_ids=chop($room_ids,",");
	$rack_ids=chop($rack_ids,",");
	$self_ids=chop($self_ids,",");
	$bin_box_ids=chop($bin_box_ids,",");

	/*$floor_ids=preg_replace("/,+/", ",",implode(",",array_unique(explode(",",$floor_ids))));
	$room_ids=preg_replace("/,+/", ",",implode(",",array_unique(explode(",",$room_ids))));
	$rack_ids=preg_replace("/,+/", ",",implode(",",array_unique(explode(",",$rack_ids))));
	$self_ids=preg_replace("/,+/", ",",implode(",",array_unique(explode(",",$self_ids))));
	$bin_box_ids=preg_replace("/,+/", ",",implode(",",array_unique(explode(",",$bin_box_ids))));*/
	//echo $room_ids.tipu;die;

	//$string = "0,,57,109,90,62,122,373,77,14,398,79,104,445,203,450,419,141,446,29,465,423,44,406";
	$all_floor_ids = array_unique(explode(",",$floor_ids));
	$all_room_ids = array_unique(explode(",",$room_ids));
	$all_rack_ids = array_unique(explode(",",$rack_ids));
	$all_self_ids = array_unique(explode(",",$self_ids));
	$all_bin_box_ids = array_unique(explode(",",$bin_box_ids));
	$floor_arr = array();$room_arr = array();$rack_arr = array();$shelf_arr = array();$bin_arr = array();
	foreach($all_floor_ids as $key=>$value)
	{
		if($value != '') { $floor_arr[]=$value; }
	}
	foreach($all_room_ids as $key=>$value)
	{
		if($value != '') { $room_arr[]=$value; }
	}
	foreach($all_rack_ids as $key=>$value)
	{
		if($value != '') { $rack_arr[]=$value; }
	}
	foreach($all_self_ids as $key=>$value)
	{
		if($value != '') { $shelf_arr[]=$value; }
	}
	foreach($all_bin_box_ids as $key=>$value)
	{
		if($value != '') { $bin_arr[]=$value; }
	}
	//echo count($floor_arr);die;
	// echo "<pre>";print_r($floor_arr);die;
	$floor_ids = implode(",",$floor_arr);
	$room_ids = implode(",",$room_arr);
	$rack_ids = implode(",",$rack_arr);
	$self_ids = implode(",",$shelf_arr);
	$bin_box_ids = implode(",",$bin_arr);
	//echo $floor_str;

	if (count($floor_arr)>0) {
		echo "load_drop_down( 'requires/trims_issue_entry_controller', '".$floor_ids."', 'load_drop_down_floor_rcv','floor_td');\n";
	}
	if (count($room_arr)>0) {
		echo "load_drop_down( 'requires/trims_issue_entry_controller', '".$room_ids."', 'load_drop_down_room_rcv','room_td');\n";
	}
	if (count($rack_arr)>0) {
		echo "load_drop_down( 'requires/trims_issue_entry_controller', '".$rack_ids."', 'load_drop_down_rack_rcv','rack_td');\n";
	}
	if (count($shelf_arr)>0) {
		echo "load_drop_down( 'requires/trims_issue_entry_controller', '".$self_ids."', 'load_drop_down_shelf_rcv','shelf_td');\n";
	}
	if (count($bin_arr)>0) {
		echo "load_drop_down( 'requires/trims_issue_entry_controller', '".$bin_box_ids."', 'load_drop_down_bin_rcv','bin_td');\n";
	}
	
	if (count($floor_arr)==1) {
		echo "get_trim_cum_stock($floor_ids,1);\n";
	}
	
	exit();
}

if($action=="load_drop_down_floor_rcv")
{
	$data = explode("_",$data);
	$floor_ids=$data[0];
	//$floor_ids=implode(",",array_unique(explode("**",$floor_ids)));
	if($floor_ids!=0)
	{
		echo create_drop_down( "cbo_floor", 132, "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.floor_id in($floor_ids) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_id,floor_room_rack_name", 1, "--Select Floor--", $floor_ids, "get_trim_cum_stock(this.value,1);" );
	}
	else
	{
		echo create_drop_down( "cbo_floor", 132, $blank_array,"",1, "--Select Floor--", 0, "" );
	}
	exit();
}
if($action=="load_drop_down_room_rcv")
{
	$data = explode("_",$data);
	$room_ids=$data[0];
	//$room_ids=implode(",",array_unique(explode("**",$room_ids)));

	if($room_ids!=0)
	{
		echo create_drop_down( "cbo_room", 132, "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.room_id in($room_ids) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select Room--", $room_ids, "get_trim_cum_stock(this.value,2);" );
	}
	else
	{
		echo create_drop_down( "cbo_room", 132, $blank_array,"",1, "--Select Room--", 0, "" );
	}
	exit();
}
if($action=="load_drop_down_rack_rcv")
{
	$data = explode("_",$data);
	$rack_ids=$data[0];
	//$rack_ids=implode(",",array_unique(explode("**",$rack_ids)));

	if($rack_ids!=0)
	{
		echo create_drop_down( "txt_rack", 132, "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.rack_id in($rack_ids) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select Rack--", $rack_ids, "get_trim_cum_stock(this.value,3);" );
	}
	else
	{
		echo create_drop_down( "txt_rack", 132, $blank_array,"",1, "--Select Rack--", 0, "" );
	}
	exit();
}
if($action=="load_drop_down_shelf_rcv")
{
	$data = explode("_",$data);
	$self_ids=$data[0];
	//$self_ids=implode(",",array_unique(explode("**",$self_ids)));

	if($self_ids!=0)
	{
		echo create_drop_down( "txt_shelf", 132, "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.shelf_id in($self_ids) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select Shelf--", $self_ids, "get_trim_cum_stock(this.value,4);" );
	}
	else
	{
		echo create_drop_down( "txt_shelf", 132, $blank_array,"",1, "--Select Shelf--", 0, "" );
	}
	exit();
}
if($action=="load_drop_down_bin_rcv")
{
	$data = explode("_",$data);
	$bin_box_ids=$data[0];
	//$bin_box_ids=implode(",",array_unique(explode("**",$bin_box_ids)));

	if($bin_box_ids!=0)
	{
		echo create_drop_down( "cbo_bin", 132, "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.bin_id and b.bin_id in($bin_box_ids) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name","bin_id,floor_room_rack_name", 1, "--Select Bin--", $bin_box_ids, "get_trim_cum_stock(this.value,5);" );
	}
	else
	{
		echo create_drop_down( "cbo_bin", 132, $blank_array,"",1, "--Select Bin--", 0, "" );
	}
	exit();
}

if ($action=="get_trim_store_wise_stock_for_requisition")
{
	$data=explode("**",$data);
	$prod_id=$data[0];
	$po_id=$data[1];
	$store_id=$data[2];
	$floor=$data[3];
	$room=$data[4];
	$rack=$data[5];
	$shelf=$data[6];
	$bin=$data[7];
	/*$room_rack_data=explode("_",$data[3]);
	$floor=$data[3];
	$room=$data[4];
	$rack=$data[5];
	$shelf=$data[6];
	$bin=$data[7];*/
	$sqlCon="";	
	if(str_replace("'","",$floor)!=0){$sqlCon= " and c.floor_id=$floor" ;}
	if(str_replace("'","",$room)!=0){$sqlCon.= " and c.room=$room" ;}
	if(str_replace("'","",$rack)!=0){$sqlCon.= " and c.rack=$rack" ;}
	if(str_replace("'","",$shelf)!=0){$sqlCon.= " and c.self=$shelf" ;}
	if(str_replace("'","",$bin)!=0){$sqlCon.= " and c.bin_box=$bin" ;}
	// echo $sqlCon;die;
	$stockData=sql_select("SELECT a.current_stock, b.conversion_factor from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.id=$prod_id");
	$current_stock=$stockData[0][csf('current_stock')];
	$conversion_factor=$stockData[0][csf('conversion_factor')];
	if($conversion_factor<=0) $conversion_factor=1;

	$sql_trim = "SELECT sum(case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end) as recv_qnty,
	sum(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end) as issue_qnty 
	from order_wise_pro_details b, inv_transaction c
	where b.trans_id=c.id and b.prod_id='$prod_id' and c.item_category=4 and c.store_id=$store_id $sqlCon and b.po_breakdown_id in($po_id) and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; // and c.company_id=$cbo_company_id
	// echo $sql_trim;die;
	$trims_rcv_issue=sql_select($sql_trim);
	$recv_qnty=$trims_rcv_issue[0][csf("recv_qnty")]*$conversion_factor;
	$yet_to_issue = ($recv_qnty-($trims_rcv_issue[0][csf('issue_qnty')]*$conversion_factor));
	$cumulative_issue =$trims_rcv_issue[0][csf('issue_qnty')]*$conversion_factor;

    echo "$('#txt_received_qnty').val(".number_format(($recv_qnty),2,".","").");\n";
    echo "$('#txt_cumulative_issued').val('".number_format(($cumulative_issue),2,".","")."');\n";
    echo "$('#txt_yet_to_issue').val('".number_format(($yet_to_issue),2,".","")."');\n";
	echo "$('#txt_global_stock').val('".number_format(($current_stock),2,".","")."');\n";
	exit();
	/*$dataArray=sql_select("SELECT sum(case when trans_type in(1,4,5) then quantity end) as recv_qnty, sum(case when trans_type in(2,3,6) then quantity end) as issue_qnty 
	from order_wise_pro_details 
	where po_breakdown_id in($po_id) and prod_id='$prod_id' and status_active=1 and is_deleted=0 and entry_form in(24,25,49,73,78,112)");
	
	$recv_qnty=$dataArray[0][csf('recv_qnty')]*$conversion_factor;
    $yet_to_issue = ($recv_qnty-($dataArray[0][csf('issue_qnty')]*$conversion_factor));*/
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	
    if($operation ==0) 
	{
		$isss_datas=strtotime(str_replace("'","",$txt_issue_date));
		$max_trans_date = return_field_value("max(transaction_date) as max_trans_id", "inv_transaction", "prod_id=$hidden_prod_id and transaction_type in (1,4,5) and status_active = 1 and is_deleted = 0", "max_trans_id");      
		if($max_trans_date != "")
		{
			$max_trans_date=strtotime($max_trans_date);
			if($max_trans_date>$isss_datas)
			{
				echo "20**Issue Date Not Allow Over Receive Date";
				die;
			}
		}
	}
	else 
	{
		//and store_id=$cbo_store_name
		$max_trans_id = return_field_value("max(id) as max_trans_id", "inv_transaction", "prod_id=$hidden_prod_id and transaction_type in (1,4,5) and status_active = 1 and is_deleted = 0", "max_trans_id");      
		if($max_trans_id != "")
		{
			if($max_trans_id>str_replace("'", "", $update_trans_id))
			{
				echo "20**Next Transaction Found, Update Or Delete Not Allow";
				die;
			}
		}
	}
	
	$store_update_upto=str_replace("'","",$store_update_upto);
	if($store_update_upto > 1)
	{
		$cbo_floor=str_replace("'","",$cbo_floor);
		$cbo_room=str_replace("'","",$cbo_room);
		$txt_rack=str_replace("'","",$txt_rack);
		$txt_shelf=str_replace("'","",$txt_shelf);
		$cbo_bin=str_replace("'","",$cbo_bin);
		if($store_update_upto==2)
		{
			$cbo_room=0;
			$txt_rack=0;
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==3)
		{
			$txt_rack=0;
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==4)
		{
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==5)
		{
			$cbo_bin=0;
		}
	}
	else
	{
		$cbo_floor=0;
		$cbo_room=0;
		$txt_rack=0;
		$txt_shelf=0;
		$cbo_bin=0;
	}
    
	$all_order_ids=str_replace("'","",$all_po_id);
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		//product master table information
		$avg_rate=$stock_qnty=$stock_value=0;
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=$hidden_prod_id");
		foreach($sql as $result)
		{
			$avg_rate = $result[csf("avg_rate_per_unit")];
			$stock_qnty = $result[csf("current_stock")];
			$stock_value = $result[csf("stock_value")];
		}
		
		if(str_replace("'","",$txt_issue_qnty)>$stock_qnty)
		{
			echo "17**Issue Quantity Exceeds The Global Current Stock Quantity"; 
			disconnect($con);die;			
		}
		
		$trims_issue_num=''; $trims_update_id='';
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			$new_system_id = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_company_id,'TIE',25,date("Y",time()) ));
		 	
			
			$field_array="id, issue_number_prefix, issue_number_prefix_num,issue_number, issue_purpose, entry_form, item_category, company_id, issue_basis, booking_id, booking_no, issue_date, challan_no, store_id, knit_dye_source, knit_dye_company,location_id, location_sewing, attention, remarks, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_issue_purpose.",25,4,".$cbo_company_id.",".$cbo_basis.",".$txt_booking_id.",".$txt_booking_no.",".$txt_issue_date.",".$txt_issue_chal_no.",".$cbo_store_name.",".$cbo_sewing_source.",".$cbo_sewing_company.",".$cbo_location.",".$cbo_location_swing.",".$txt_attention.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$trims_issue_num=$new_system_id[0];
			$trims_update_id=$id;
		}
		else
		{
			$field_array_update="issue_purpose*issue_basis*booking_id*booking_no*issue_date*challan_no*store_id*knit_dye_source*knit_dye_company*location_id*location_sewing*attention*remarks*updated_by*update_date";
			$data_array_update=$cbo_issue_purpose."*".$cbo_basis."*".$txt_booking_id."*".$txt_booking_no."*".$txt_issue_date."*".$txt_issue_chal_no."*".$cbo_store_name."*".$cbo_sewing_source."*".$cbo_sewing_company."*".$cbo_location."*".$cbo_location_swing."*".$txt_attention."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$trims_issue_num=str_replace("'","",$txt_system_id);
			$trims_update_id=str_replace("'","",$update_id);
		}
		
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$txt_issue_qnty=str_replace("'","",$txt_issue_qnty);
		$txt_garments_qty=str_replace("'","",$txt_garments_qty);
		$issue_stock_value = str_replace("'","",$txt_cons_rate)*str_replace("'","",$txt_issue_qnty);
		
		$field_array_trans="id, mst_id, company_id, receive_basis, pi_wo_batch_no, prod_id, item_category, transaction_type, transaction_date, cons_uom, cons_quantity, garments_qty, cons_rate, cons_amount, issue_challan_no, store_id,floor_id,room,rack,self,bin_box, inserted_by, insert_date";
		
		$data_array_trans="(".$id_trans.",".$trims_update_id.",".$cbo_company_id.",".$cbo_basis.",".$txt_booking_id.",".$hidden_prod_id.",4,2,".$txt_issue_date.",".$cbo_uom.",".$txt_issue_qnty.",'".$txt_garments_qty."',".$txt_cons_rate.",".$issue_stock_value.",".$txt_issue_chal_no.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$save_data=str_replace("'","",$save_data);
		$tot_string=strlen($save_data);
		$count_loop=ceil($tot_string/3900);
		$first_save_data=''; $second_save_data=''; $theRest_save_data='';$count=0; $interval=3900;
		for($i=1;$i<=$count_loop; $i++)
		{
		    if($count_loop>0 && $i==1) $first_save_data=substr($save_data, $count, $interval);
		    if($count_loop>1 && $i==2) $second_save_data=substr($save_data, $count, $interval);
		    if($count_loop>2 && $i==3) $theRest_save_data=substr($save_data, $count, $interval);
		    $count+=3900;
		}
		$id_dtls = return_next_id_by_sequence("INV_TRIMS_ISSUE_DTLS_PK_SEQ", "inv_trims_issue_dtls", $con);
		$field_array_dtls="id, mst_id, trans_id, prod_id, item_group_id, item_description, brand_supplier,floor_id,room,rack_no, shelf_no,bin, uom, issue_qnty, garments_qty, rate, amount, order_id, item_order_id, gmts_color_id, gmts_size_id, item_color_id, item_size, save_string, save_string_2, save_string_3, inserted_by, insert_date,floor_sewing,sewing_line";
		
		$data_array_dtls="(".$id_dtls.",".$trims_update_id.",".$id_trans.",".$hidden_prod_id.",".$cbo_item_group.",".$txt_item_description.",".$txt_brand_supref.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_uom.",".$txt_issue_qnty.",'".$txt_garments_qty."',".$txt_cons_rate.",".$issue_stock_value.",".$selected_po_id.",".$selected_po_id.",".$gmts_color_id.",".$gmts_size_id.",".$txt_item_color_id.",".$txt_item_size.",'".$first_save_data."','".$second_save_data."','".$theRest_save_data."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_floor_swing.",".$cbo_sewing_line.")";
		
		
		//product master table data UPDATE START----------------------//
		$avgRate=$avg_rate;	
		$currentStock   = $stock_qnty-$txt_issue_qnty;
		$StockValue=0;
		if ($currentStock != 0){
			$StockValue	 	= $currentStock*$avg_rate;
			$avgRate	 	= number_format($avg_rate,$dec_place[3],'.','');
		}	 

		$field_array_prod= "avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date"; 
		$data_array_prod=$avgRate."*".$txt_issue_qnty."*".$currentStock."*".$StockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		//------------------ product_details_master END--------------//
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, order_rate, order_amount, inserted_by, insert_date";

		$sqlCon="";	
		$store_update_upto=str_replace("'","",$store_update_upto);
		if($store_update_upto > 1)
		{
			if($store_update_upto==6)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
				if(str_replace("'","",$cbo_bin)!=0){$sqlCon.= " and c.bin_box=$cbo_bin" ;}
			}
			else if($store_update_upto==5)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
			}
			else if($store_update_upto==4)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
			}
			else if($store_update_upto==3)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
			}
			else if($store_update_upto==2)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
			}
		}

		/*$sqlCon="";	
		if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
		if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
		if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
		if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
		if(str_replace("'","",$cbo_bin)!=0){$sqlCon.= " and c.bin_box=$cbo_bin" ;}*/

		$save_data=explode(",",str_replace("'","",$save_data));
		$prod_id=str_replace("'","",$hidden_prod_id);
		$conversion_fac=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b "," a.item_group_id=b.id and a.id=$hidden_prod_id","conversion_factor");
		for($i=0;$i<count($save_data);$i++)
		{
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$order_dtls=explode("_",$save_data[$i]);
			
			$order_id=$order_dtls[0];
			$issue_qnty=$order_dtls[1];
			$trim_stock=0;
			if(str_replace("'","",$cbo_basis)==1 || str_replace("'","",$cbo_basis)==3)
			{
				$sql_trim = sql_select("select sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as balance, sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.order_amount else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.order_amount else 0 end)) as balance_amt
				from order_wise_pro_details b, inv_transaction c
				where b.trans_id=c.id and b.prod_id='$prod_id' and c.item_category=4 and c.company_id=$cbo_company_id and c.store_id=$cbo_store_name $sqlCon and b.po_breakdown_id =$order_id and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				$trim_stock=$sql_trim[0][csf("balance")]*$conversion_fac;
				$trim_stock_amt=$sql_trim[0][csf("balance_amt")];
				$trim_ord_rate=0;
				if($sql_trim[0][csf("balance")]!=0 && $trim_stock_amt!=0)$trim_ord_rate=$trim_stock_amt/$sql_trim[0][csf("balance")];
				
			}
			else
			{
				$sql_trim = sql_select("select sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)-(case when c.transaction_type in(2,3,6) then c.cons_quantity else 0 end)) as balance, sum((case when c.transaction_type in(1,4,5) then c.cons_amount else 0 end)-(case when c.transaction_type in(2,3,6) then c.cons_amount else 0 end)) as balance_amt 
				from inv_transaction c
				where c.prod_id='$prod_id' and c.item_category=4 and c.receive_basis=2 and c.company_id=$cbo_company_id and c.store_id =$cbo_store_name $sqlCon and c.pi_wo_batch_no =$txt_booking_id and c.status_active=1 and c.is_deleted=0");
				$trim_stock=$sql_trim[0][csf("balance")];
				$trim_stock_amt=$sql_trim[0][csf("balance_amt")];
				$trim_ord_rate=0;
				if($sql_trim[0][csf("balance")]!=0 && $trim_stock_amt!=0)$trim_ord_rate=$trim_stock_amt/$sql_trim[0][csf("balance")];
			}
			$order_amount=$issue_qnty*$trim_ord_rate;
			
			//echo $issue_qnty.'>>'.$trim_stock;
			$issue_qnty_order=($issue_qnty*$conversion_fac);

			if(number_format($issue_qnty_order,6,".","")>number_format($trim_stock,6,".",""))
			{
				echo "11**Issue Quantity Not Allow Over Order Stock. $issue_qnty = $issue_qnty_order = $trim_stock";
				disconnect($con);
				die;
			}
			
			
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array_prop.="$add_comma(".$id_prop.",".$id_trans.",2,25,".$id_dtls.",".$order_id.",".$hidden_prod_id.",".$issue_qnty.",'".$trim_ord_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$id_prop = $id_prop+1;
			$all_order_id.=$order_id.",";
			
		}
		
		$all_order_id=chop($all_order_id,",");
		
		$item_approved = return_library_array("select id,approval_status from accessories_item_approval where po_break_down_id in($all_order_id) and prod_id in ($prod_id) and item_group_id in ($cbo_item_group) and entry_form=25", "id", "approval_status",1);

		
		$first_entry_sql=array();
		if($accessories_con_app)
		{
			$first_entry_sql=sql_select("select po_breakdown_id from order_wise_pro_details where status_active=1 and entry_form=25 and po_breakdown_id in($all_order_id) and prod_id=$prod_id group by po_breakdown_id");
		}
		
		
		$rID=$rID2=$rID3=$prodUpdate=$rID6=$appCont_rID1=true;
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_id,1);
		}
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		//echo "10**insert into inv_trims_issue_dtls ($field_array_dtls) values $data_array_dtls";oci_rollback($con);die; 
		$rID3=sql_insert("inv_trims_issue_dtls",$field_array_dtls,$data_array_dtls,0);
		$prodUpdate= sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$hidden_prod_id,1);
		if(str_replace("'","",$cbo_basis)==1 || str_replace("'","",$cbo_basis)==3)
		{
			if($data_array_prop!="")
			{
				$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			}
		}
		//echo "10**$accessories_con_app";oci_rollback($con);die;
		$app_rID=$appCont_rID1=true;
		if($accessories_con_app && count($first_entry_sql)>0)
		{
			if($all_order_id=="") $all_order_id=0; if($prod_id=="") $prod_id=0;
			$appCont_sql="update accessories_item_approval set approval_status=0 where po_break_down_id in($all_order_id) and prod_id in($prod_id)";
			//echo "10**".$appCont_sql;oci_rollback($con);die;
			$appCont_rID1=execute_query($appCont_sql);
		}
		
		//echo "10**$rID=$rID2=$rID3=$prodUpdate=$rID6=$appCont_rID1";oci_rollback($con);die;
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $prodUpdate && $rID6 && $appCont_rID1)
			{
				mysql_query("COMMIT");  

				echo "0**".$trims_update_id."**".$trims_issue_num."**0"."**".$all_order_ids."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;"."**0"."**0";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $prodUpdate && $rID6 && $appCont_rID1)
			{
				oci_commit($con); 
				echo "0**".$trims_update_id."**".$trims_issue_num."**0"."**".$all_order_ids."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0"."**0";
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
		
		$sql_issue_rtn=sql_select("select id, issue_id, prod_id, cons_quantity from inv_transaction where status_active=1 and transaction_type=4 and issue_id=$update_id and prod_id=$previous_prod_id");
		if(str_replace("'","",$previous_prod_id)==str_replace("'","",$hidden_prod_id))
		{
			$issue_rtn_qnty=$sql_issue_rtn[0][csf("cons_quantity")];
			$issue_quantity=str_replace("'","",$txt_issue_qnty);
			if($issue_quantity<$issue_rtn_qnty)
			{
				echo "17**Issue Quantity Not Allow Less Then Issue Return Quantity"; 
				disconnect($con);die;
			}
			
		}
		else
		{
			if(count($sql_issue_rtn)>0)
			{
				echo "17**Issue Return Found, Item Change Not Allow"; 
				disconnect($con);die;
			}
		}
		
		$field_array_update="issue_purpose*issue_basis*booking_id*booking_no*issue_date*challan_no*store_id*knit_dye_source*knit_dye_company*location_id*location_sewing*attention*remarks*updated_by*update_date";
		$data_array_update=$cbo_issue_purpose."*".$cbo_basis."*".$txt_booking_id."*".$txt_booking_no."*".$txt_issue_date."*".$txt_issue_chal_no."*".$cbo_store_name."*".$cbo_sewing_source."*".$cbo_sewing_company."*".$cbo_location."*".$cbo_location_swing."*".$txt_attention."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		//product master table information
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=$hidden_prod_id");
		$avg_rate=$stock_qnty=$stock_value=0;
		foreach($sql as $result)
		{
			$avg_rate = $result[csf("avg_rate_per_unit")];
			$stock_qnty = $result[csf("current_stock")];
			$stock_value = $result[csf("stock_value")];
		}
		
		
		$txt_issue_qnty=str_replace("'","",$txt_issue_qnty);
		$issue_stock_value = str_replace("'","",$txt_cons_rate)*str_replace("'","",$txt_issue_qnty);
		
		$field_array_trans_update="receive_basis*pi_wo_batch_no*prod_id*transaction_date*cons_uom*cons_quantity*cons_rate*cons_amount*issue_challan_no*store_id*floor_id*room*rack*self*bin_box*updated_by*update_date";
		$data_array_trans_update=$cbo_basis."*".$txt_booking_id."*".$hidden_prod_id."*".$txt_issue_date."*".$cbo_uom."*".$txt_issue_qnty."*".$txt_cons_rate."*".$issue_stock_value."*".$txt_issue_chal_no."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		

		$save_data=str_replace("'","",$save_data);
		$tot_string=strlen($save_data);
		$count_loop=ceil($tot_string/3900);
		$first_save_data=''; $second_save_data=''; $theRest_save_data='';$count=0; $interval=3900;
		for($i=1;$i<=$count_loop; $i++)
		{
		    if($count_loop>0 && $i==1) $first_save_data=substr($save_data, $count, $interval);
		    if($count_loop>1 && $i==2) $second_save_data=substr($save_data, $count, $interval);
		    if($count_loop>2 && $i==3) $theRest_save_data=substr($save_data, $count, $interval);
		    $count+=3900;
		}

		$field_array_dtls_update="trans_id*prod_id*item_group_id*item_description*brand_supplier*floor_id*room*rack_no*shelf_no*bin*uom*issue_qnty*rate*amount*order_id*gmts_color_id*gmts_size_id*item_color_id*item_size*save_string*save_string_2*save_string_3*floor_sewing*sewing_line*updated_by*update_date";
		
		$data_array_dtls_update=$update_trans_id."*".$hidden_prod_id."*".$cbo_item_group."*".$txt_item_description."*".$txt_brand_supref."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$cbo_uom."*".$txt_issue_qnty."*".$txt_cons_rate."*".$issue_stock_value."*".$all_po_id."*".$gmts_color_id."*".$gmts_size_id."*".$txt_item_color_id."*".$txt_item_size."*'".$first_save_data."'*'".$second_save_data."'*'".$theRest_save_data."'*".$cbo_floor_swing."*".$cbo_sewing_line."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		if(str_replace("'","",$previous_prod_id)==str_replace("'","",$hidden_prod_id))
		{
			$avgRate=$avg_rate;
			$currentStock   = $stock_qnty-$txt_issue_qnty+str_replace("'", '',$hidden_issue_qnty);
			$StockValue=0;
			if ($currentStock != 0){
				$StockValue	 	= $currentStock*$avg_rate;
				$avgRate	 	= number_format($avg_rate,$dec_place[3],'.','');
			} 		 
			
			$latest_current_stock=$stock_qnty+str_replace("'", '',$hidden_issue_qnty);	
			
			$field_array_prod= "avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date"; 
			$data_array_prod=$avgRate."*".$txt_issue_qnty."*".$currentStock."*".$StockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		}
		else
		{
			$stock=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_prod_id");
			$adjust_curr_stock=$stock[0][csf('current_stock')]+str_replace("'", '',$hidden_issue_qnty);
			$adjust_rate=$stock[0][csf('avg_rate_per_unit')];
			$adjust_value=$adjust_curr_stock*$adjust_rate;
			//product master table data UPDATE START----------------------//
			$avgRate=$avg_rate;
			$currentStock   = $stock_qnty-$txt_issue_qnty;
			$StockValue=0;
			if ($currentStock != 0){
				$StockValue	 	= $currentStock*$avg_rate;
				$avgRate	 	= number_format($avg_rate,$dec_place[3],'.','');
			} 
			
			$latest_current_stock=$stock_qnty;
			
			$field_array_prod= "avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date"; 
			$data_array_prod=$avgRate."*".$txt_issue_qnty."*".$currentStock."*".$StockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		}
		
		if(str_replace("'","",$txt_issue_qnty)>$latest_current_stock)
		{
			echo "17**Issue Quantity Exceeds The Global Current Stock Quantity"; 
			disconnect($con);die;			
		}
		
		//------------------ product_details_master END--------------//

		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, order_rate, order_amount, inserted_by, insert_date";
		$sqlCon="";	
		$store_update_upto=str_replace("'","",$store_update_upto);
		if($store_update_upto > 1)
		{
			if($store_update_upto==6)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
				if(str_replace("'","",$cbo_bin)!=0){$sqlCon.= " and c.bin_box=$cbo_bin" ;}
			}
			else if($store_update_upto==5)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
			}
			else if($store_update_upto==4)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
			}
			else if($store_update_upto==3)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
			}
			else if($store_update_upto==2)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
			}
		}

		$save_data=explode(",",str_replace("'","",$save_data));
		$all_order_id="";
		$prod_id=str_replace("'","",$hidden_prod_id);
		$conversion_fac=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b "," a.item_group_id=b.id and a.id=$hidden_prod_id","conversion_factor");
		for($i=0;$i<count($save_data);$i++)
		{
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$order_dtls=explode("_",$save_data[$i]);
			$order_id=$order_dtls[0];
			$issue_qnty=$order_dtls[1];
			
			$trim_stock=0;
			if(str_replace("'","",$cbo_basis)==1 || str_replace("'","",$cbo_basis)==3)
			{
				$sql_trim = sql_select("select sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as balance, sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.order_amount else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.order_amount else 0 end)) as balance_amt 
				from order_wise_pro_details b, inv_transaction c
				where b.trans_id=c.id and b.prod_id='$prod_id' and c.id <> $update_trans_id and c.item_category=4 and c.company_id=$cbo_company_id and c.store_id=$cbo_store_name $sqlCon and b.po_breakdown_id =$order_id and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				$trim_stock=$sql_trim[0][csf("balance")]*$conversion_fac;
				$trim_stock_amt=$sql_trim[0][csf("balance_amt")];
				$trim_ord_rate=0;
				if($sql_trim[0][csf("balance")]!=0 && $trim_stock_amt!=0)$trim_ord_rate=$trim_stock_amt/$sql_trim[0][csf("balance")];
				
			}
			else
			{
				$sql_trim = sql_select("select sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)-(case when c.transaction_type in(2,3,6) then c.cons_quantity else 0 end)) as balance, sum((case when c.transaction_type in(1,4,5) then c.cons_amount else 0 end)-(case when c.transaction_type in(2,3,6) then c.cons_amount else 0 end)) as balance_amt 
				from inv_transaction c
				where c.prod_id='$prod_id' and c.id <> $update_trans_id and c.item_category=4 and c.receive_basis=2 and c.company_id=$cbo_company_id and c.store_id =$cbo_store_name $sqlCon and c.pi_wo_batch_no =$txt_booking_id and c.status_active=1 and c.is_deleted=0");
				$trim_stock=$sql_trim[0][csf("balance")];
				$trim_stock_amt=$sql_trim[0][csf("balance_amt")];
				$trim_ord_rate=0;
				if($sql_trim[0][csf("balance")]!=0 && $trim_stock_amt!=0)$trim_ord_rate=$trim_stock_amt/$sql_trim[0][csf("balance")];
			}
			$order_amount=$issue_qnty*$trim_ord_rate;
			$iss_qty_ord = ($issue_qnty*$conversion_fac);
			if(number_format($iss_qty_ord,6,".","")>number_format($trim_stock,6,".",""))
			{
				echo "11**Issue Quantity Not Allow Over Order Stock.";
				disconnect($con);
				die;
			}
			//$id_prop = $id_prop+1;
			$all_order_id.=$order_id.",";
			
			
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array_prop.="$add_comma(".$id_prop.",".$update_trans_id.",2,25,".$update_dtls_id.",".$order_id.",".$hidden_prod_id.",".$issue_qnty.",'".$avg_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		
		$all_order_id=chop($all_order_id,",");
		$rID=$rID2=$rID3=$adjust_prod=$prodUpdate=$delete_prop=$rID4=true;
		$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_id,1);
		$rID2=sql_update("inv_transaction",$field_array_trans_update,$data_array_trans_update,"id",$update_trans_id,0);
		$rID3=sql_update("inv_trims_issue_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$update_dtls_id,0);
		if(str_replace("'","",$previous_prod_id)==str_replace("'","",$hidden_prod_id))
		{
			$prodUpdate= sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$hidden_prod_id,1);
		}
		else
		{
			$adjust_prod=sql_update("product_details_master","current_stock*stock_value",$adjust_curr_stock."*".$adjust_value,"id",$previous_prod_id,0);
			$prodUpdate= sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$hidden_prod_id,1);
		}
		$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id=$update_dtls_id and trans_id=$update_trans_id and entry_form=25",0);
		
		
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;	
		if(str_replace("'","",$cbo_basis)==1 || str_replace("'","",$cbo_basis)==3)
		{
			if($data_array_prop!="")
			{
				$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			}
		}
		
		//echo "10**$rID=$rID2=$rID3=$adjust_prod=$prodUpdate=$delete_prop=$rID4=";oci_rollback($con);die;
		
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $adjust_prod && $prodUpdate && $delete_prop && $rID4)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0"."**".$all_order_ids."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**0**1"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $adjust_prod && $prodUpdate && $delete_prop && $rID4)
			{
				oci_commit($con); 
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0"."**".$all_order_ids."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1"."**0";
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
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$update_id=str_replace("'","",$update_id);
		$previous_prod_id=str_replace("'","",$previous_prod_id);
		$update_dtls_id=str_replace("'","",$update_dtls_id);
		$update_trans_id=str_replace("'","",$update_trans_id);
		//echo "10**$update_id=$previous_prod_id=$update_dtls_id=$update_trans_id";die;
		if($update_id>0 && $previous_prod_id>0 && $update_dtls_id>0 && $update_trans_id>0)
		{
			$previous_data_check=sql_select("select id as rcv_id, cons_quantity as rcv_qnty, cons_amount as rcv_amount  from inv_transaction where transaction_type=2 and id=$update_trans_id and prod_id=$previous_prod_id");
			$previous_check_id=$previous_data_check[0][csf("rcv_id")];
			$previous_qnty=$previous_data_check[0][csf("rcv_qnty")];
			$previous_amount=$previous_data_check[0][csf("rcv_amount")];
			
			if($db_type==0) $row_count_cond=" limit 1"; else $row_count_cond=" and rownum<2";
			$next_operation_check=sql_select("select id as next_id, mst_id as mst_id, transaction_type as transaction_type from inv_transaction where id > $previous_check_id and prod_id=$previous_prod_id and status_active=1 and transaction_type=4 and issue_id=$update_id $row_count_cond");
			if(count($next_operation_check)>0)
			{
				$next_id=$next_operation_check[0][csf("next_id")];
				$next_mst_id=$next_operation_check[0][csf("mst_id")];
				$next_transaction_type=$next_operation_check[0][csf("transaction_type")];

				if($next_transaction_type==1 || $next_transaction_type==4)
				{
					$next_mrr=return_field_value("recv_number as next_mrr_number","inv_receive_master","id=$next_mst_id","next_mrr_number");
				}
				else if($next_transaction_type==2 || $next_transaction_type==3)
				{
					$next_mrr=return_field_value("issue_number as next_mrr_number","inv_issue_master","id=$next_mst_id","next_mrr_number");
				}
				else
				{
					$next_mrr=return_field_value("transfer_system_id as next_mrr_number","inv_item_transfer_mst","id=$next_mst_id","next_mrr_number");
				}
				echo "20**Next Operation No:- $next_mrr  Found, Delete Not Allow.";
				disconnect($con);die;
				//check_table_status( $_SESSION['menu_id'],0);
			}
			
			
			$row_prod=sql_select( "select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$previous_prod_id and status_active=1 and is_deleted=0" );
			$prod_id=$row_prod[0][csf('id')];
			$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
			$curr_stock_qnty=$row_prod[0][csf('current_stock')]+$previous_qnty;
			$curr_stock_value=0;
			if ($curr_stock_qnty != 0) {
				$curr_stock_value=$row_prod[0][csf('stock_value')]+$previous_amount;
				$avg_rate_per_unit=number_format($curr_stock_value/$curr_stock_qnty,$dec_place[3],'.','');
			}

			$field_array_prod_update="avg_rate_per_unit*current_stock*stock_value*updated_by*update_date";
			$data_array_prod_update=$avg_rate_per_unit."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			
			$row_propotionate=sql_select( "select id, po_breakdown_id, quantity, order_rate, order_amount 
			from order_wise_pro_details where trans_id=$previous_check_id and status_active=1 and is_deleted=0" );
			$propotionate_data=array();
			foreach($row_propotionate as $row)
			{
				$all_order_id.=$row[csf("po_breakdown_id")].",";
				$propotionate_data[$row[csf("po_breakdown_id")]]["quantity"]+=$row[csf("quantity")];
				$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount"]+=$row[csf("order_amount")];
			}
			$all_order_id=chop($all_order_id,",");
			$field_array_prod_ord_update="avg_rate*stock_quantity*stock_amount*updated_by*update_date";
			if($all_order_id!="")
			{
				$prod_order_stock=sql_select("select id, po_breakdown_id, stock_quantity, stock_amount 
				from order_wise_stock where prod_id=$previous_prod_id and po_breakdown_id in($all_order_id) and status_active=1 and is_deleted=0 ");
				foreach($prod_order_stock as $row)
				{
					$current_stock_qnty=$row[csf('stock_quantity')]+$propotionate_data[$row[csf("po_breakdown_id")]]["quantity"];
					$current_stock_value=$row[csf('stock_amount')]+$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount"];
					if($current_stock_value>0 && $current_stock_qnty>0)
					{
						$current_avg_rate=number_format($current_stock_value/$current_stock_qnty,$dec_place[3],'.','');
					}
					else
					{
						$current_avg_rate=0;
					}
					
					if($row[csf('id')])
					{
						$ord_prod_id_arr[]=$row[csf('id')];
						$data_array_prod_ord_update[$row[csf('id')]]=explode("*",("".$current_avg_rate."*".$current_stock_qnty."*".$current_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					}
				}
			}
			
			
			$field_arr="status_active*is_deleted*updated_by*update_date";
			$data_arr="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=$rID2=$rID3=$rID4=$ordProdUpdate=true;
			$rID=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$previous_prod_id,1);
			if(count($ord_prod_id_arr)>0)
			{
				$ordProdUpdate=execute_query(bulk_update_sql_statement("order_wise_stock","id",$field_array_prod_ord_update,$data_array_prod_ord_update,$ord_prod_id_arr));
			}
			//echo "10**$update_trans_id == $update_dtls_id == $update_trans_id";oci_rollback($con);check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;
			$rID2=sql_update("inv_transaction",$field_arr,$data_arr,"id",$update_trans_id,1);
			$rID3=sql_update("inv_trims_issue_dtls",$field_arr,$data_arr,"id",$update_dtls_id,1);
			if($all_order_id!="")
			{
				$rID4=sql_update("order_wise_pro_details",$field_arr,$data_arr,"trans_id",$update_trans_id,1);
			}
			
			//echo "10** $rID && $ordProdUpdate && $rID2 && $rID3 && $rID4";oci_rollback($con);disconnect($con);die;
			if($db_type==0)
			{
				if($rID && $ordProdUpdate && $rID2 && $rID3 && $rID4)
				{
					mysql_query("COMMIT");  
					echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "7**0**0**1";
				}
			}
	
			if($db_type==2 || $db_type==1 )
			{
				if($rID && $ordProdUpdate && $rID2 && $rID3 && $rID4)
				{
					oci_commit($con);  
					echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
				}
				else
				{
					oci_rollback($con);
					echo "7**0**0**1";
				}
			}
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
	}
}

if ($action=="trims_issue_popup_search")
{
	echo load_html_head_contents("Trims Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(ids)
		{
			var id = ids.split("_");
			$('#hidden_issue_id').val(id[0]);
			$('#hidden_posted_in_account').val(id[1]);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:780px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:775px; margin-left:5px">
		<legend>Enter search words</legend>           
            <table cellpadding="2" cellspacing="0" width="770" class="rpt_table" rules="all" border="1">
                <thead>
                    <th>Store</th>
                    <th>Issue Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Enter Issue ID No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_issue_id" id="hidden_issue_id" class="text_boxes" value="">  
                        <input type="hidden" name="hidden_posted_in_account" id="hidden_posted_in_account" class="text_boxes" value="">  
                    </th> 
                </thead>
                <tr>
                    <td align="center">
                    	<?
							echo create_drop_down("cbo_store_name", 130, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$cbo_company_id' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- All store --", 0, "" );
						?>       
                    </td>
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					</td>
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Issue ID",2=>"Challan No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";							
							echo create_drop_down( "cbo_search_by", 120, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                    </td>     
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_store_name').value+'_'+document.getElementById('cbo_year_selection').value, 'create_trims_issue_search_list_view', 'search_div', 'trims_issue_entry_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="margin-top:8px;" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_trims_issue_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$store_id =$data[5];
	$year_id =$data[6];
	
	if($store_id==0) $store_name=""; else $store_name="and a.store_id=$store_id";
	
	$trims_issue_basis=array(1=>"With Order",2=>"Without Order");
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.issue_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.issue_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==1)	
			$search_field_cond="and a.issue_number_prefix_num='$data[0]'";
		else	
			$search_field_cond="and a.challan_no like '$search_string'";
	}
	else
	{
		$search_field_cond="";
	}
	
	if($db_type==0) $year_field="YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY')";
	else $year_field="";//defined Later
	$year_condition="";
	if($year_id>0)
	{
		if($db_type==0)

		{
			$year_condition=" and YEAR(a.insert_date)='$year_id'";
		}
		else
		{
			$year_condition=" and to_char(a.insert_date,'YYYY')='$year_id'";
		}
	}
	
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, supplier_id, company_location_id FROM user_passwd where id=$user_id");
	$cre_company_id = $userCredential[0][csf('company_id')];
	$cre_supplier_id = $userCredential[0][csf('supplier_id')];
	$cre_store_location_id = $userCredential[0][csf('store_location_id')];
	$cre_company_location_id = $userCredential[0][csf('company_location_id')];
	
	if ($cre_company_id !='') {
		$company_credential_cond = " and a.company_id in($cre_company_id)";
	}
	if ($cre_store_location_id !='') {
		$cre_store_location_id=$cre_store_location_id.",0";
		$store_location_credential_cond = " and a.store_id in($cre_store_location_id)"; 
	}
	
	/*if ($cre_company_location_id !='') {
		$cre_company_location_id=$cre_company_location_id.",0";
		$location_credential_cond = " and location_id in($cre_company_location_id)";
	}*/
	
	$sql = "select a.id, a.issue_number_prefix_num, $year_field as year, a.issue_number, a.challan_no, a.store_id, a.location_id, a.issue_date, a.booking_no, a.issue_basis, a.is_posted_account 
	from inv_issue_master a, inv_transaction b 
	where a.id=b.mst_id and b.transaction_type=2 and a.entry_form=25 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $store_name  $search_field_cond $date_cond $company_credential_cond $store_location_credential_cond $year_condition 
	group by a.id, a.issue_number_prefix_num, a.insert_date, a.issue_number, a.challan_no, a.store_id, a.location_id, a.issue_date, a.booking_no, a.issue_basis, a.is_posted_account
	order by a.id desc"; 
	//echo $sql;
	$store_arr = return_library_array("select id, store_name from lib_store_location","id","store_name");
	$location_arr = return_library_array("select id, location_name from lib_location","id","location_name");
	$arr=array(2=>$trims_issue_basis,5=>$store_arr,6=>$location_arr);
	
	echo create_list_view("list_view", "Issue ID,Year,Issue Basis,Booking No.,Challan No,Store,Location,Issue date", "70,60,80,110,80,100,110","770","240",0, $sql, "js_set_value", "id,is_posted_account", "", 1, "0,0,issue_basis,0,0,store_id,location_id,0", $arr, "issue_number_prefix_num,year,issue_basis,booking_no,challan_no,store_id,location_id,issue_date", "",'','0,0,0,0,0,0,0,3');
	
	exit();
}

if($action=='populate_data_from_trims_issue')
{
	
	$data_array=sql_select("select id, company_id, issue_basis,issue_purpose, booking_id, booking_no, issue_number, challan_no, store_id, issue_date, knit_dye_source, knit_dye_company, location_id,location_sewing, attention, remarks from inv_issue_master where id=$data");

	$company_id=$data_array[0][csf("company_id")];
	$variable_inventory_sql=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method=$variable_inventory_sql[0][csf("store_method")];
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("issue_number")]."';\n";
		echo "document.getElementById('cbo_issue_purpose').value 			= '".$row[csf("issue_purpose")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_basis').value 					= '".$row[csf("issue_basis")]."';\n";
		echo "document.getElementById('store_update_upto').value 			= '".$store_method."';\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "enable_disable();\n";
		
		if($row[csf("issue_basis")]==2)
		{
			echo "show_list_view('".$row[csf("booking_id")]."','create_itemDesc_search_list_view_on_booking','list_fabric_desc_container','requires/trims_issue_entry_controller','setFilterGrid(\'tbl_list_search\',-1);');\n";
		}
		
		echo "document.getElementById('cbo_sewing_source').value 			= '".$row[csf("knit_dye_source")]."';\n";
		
		echo "load_drop_down( 'requires/trims_issue_entry_controller', ".$row[csf("knit_dye_source")]."+'_'+".$row[csf("company_id")].", 'load_drop_down_sewing_com','sewing_com');\n";
		
		echo "document.getElementById('cbo_sewing_company').value 		    = '".$row[csf("knit_dye_company")]."';\n";
		echo "load_location('".$row[csf("knit_dye_company")]."');\n";
		echo "document.getElementById('cbo_location_swing').value 			= '".$row[csf("location_sewing")]."';\n";

		echo "document.getElementById('txt_issue_date').value 				= '".change_date_format($row[csf("issue_date")])."';\n";
		echo "document.getElementById('txt_booking_no').value 				= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_booking_id').value 				= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('txt_issue_chal_no').value 			= '".$row[csf("challan_no")]."';\n";
		
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		echo "load_drop_down('requires/trims_issue_entry_controller', '".$row[csf('location_id')]."_".$row[csf('company_id')]."', 'load_drop_down_store','store_td');\n";
		//echo "load_room_rack_self_bin('requires/trims_issue_entry_controller*4', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";


		echo "document.getElementById('txt_attention').value 				= '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		
		echo "load_drop_down( 'requires/trims_issue_entry_controller','".$row[csf("location_sewing")]."_".$row[csf("knit_dye_company")]."_".$row[csf("knit_dye_source")]."_".$row[csf("issue_purpose")]."', 'load_drop_down_floor', 'swing_floor_td');\n";
		
		echo "chk_purpose_condition('".$row[csf("issue_purpose")]."');\n"; 
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_trims_issue',1,1);\n";  
		exit();
	}
}

if($action=="show_trims_listview")
{
	$data_ref=explode("**",$data);
	$trim_group_arr =array();
	$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
		$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
	}
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_arr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");
	$sql="select id, item_group_id, item_description, brand_supplier, issue_qnty, item_color_id, item_size, uom, order_id, item_order_id, gmts_color_id, gmts_size_id from inv_trims_issue_dtls where mst_id=$data_ref[0] and status_active = '1' and is_deleted = '0' order by id ASC";
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table">
		<thead>
			<th width="60">Item Group</th>
			<th width="120">Item Description</th>               
			<th width="70">Item Color</th>
			<th width="50">Item Size</th>
            <th width="60">Gmts. Color</th>
			<th width="40">Gmts. Size</th>
			<th width="60">Supp Ref</th>
			<th width="30">UOM</th>
            <th width="50">Issue Qnty</th>
            <th>Buyer Order</th>
		</thead>
	</table>
	<div style="width:687px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table" id="tbl_list_search_dtls">  
		<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				
				$order_no="";
				if($row[csf("order_id")]!="")
				{
					if($db_type==0)
					{
						$order_no=return_field_value("group_concat(po_number) as po_no","wo_po_break_down","id in (".$row[csf("item_order_id")].")","po_no");	
					}
					else
					{
						$order_no=return_field_value("LISTAGG(po_number, ',') WITHIN GROUP (ORDER BY id) as po_no","wo_po_break_down","id in (".$row[csf("item_order_id")].")","po_no");		
					}
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('id')]."**".$data_ref[1]; ?>','populate_trims_details_form_data', 'requires/trims_issue_entry_controller');"> 
					<td width="60"><p><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></p></td>  
					<td width="120"><p><? echo $row[csf('item_description')]; ?></p></td>             
					<td width="70"><p><? echo $color_arr[$row[csf('item_color_id')]]; ?></p></td>
					<td width="50"><p><? echo $row[csf('item_size')]; ?></p></td>
                    <td width="60"><p><? echo $color_arr[$row[csf('gmts_color_id')]]; ?></p></td>
					<td width="40" align="center"><p><? echo $size_arr[$row[csf('gmts_size_id')]]; ?></p></td>
					<td width="60"><p><? echo $row[csf('brand_supplier')]; ?></p></td>
                    <td width="30" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
					<td align="right" width="50"><? echo number_format($row[csf('issue_qnty')],2); ?></td>
                    <td><p><? echo $order_no; ?>&nbsp;</p></td>
				</tr>
				<?
                $i++;
			}
			?>
		</table>
	</div>
<?	
	exit();
}


if($action=='populate_trims_details_form_data')
{
	$data_ref=explode("**",$data);
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_arr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");
	$issue_mst=sql_select("select a.issue_basis, a.booking_id from inv_issue_master a, inv_trims_issue_dtls b where a.id=b.mst_id and b.id=$data_ref[0]");
	$issue_basis=$issue_mst[0][csf('issue_basis')];
	$booking_id=$issue_mst[0][csf('booking_id')];
	$data_array=sql_select("select b.id, b.trans_id, b.prod_id, b.item_group_id, b.item_description, b.brand_supplier, a.company_id,a.store_id,b.floor_id,b.room,b.rack_no, b.shelf_no,b.bin, b.issue_qnty, b.gmts_color_id, b.gmts_size_id, b.uom, b.order_id, b.item_order_id, b.item_order_id, b.item_color_id, b.item_size,b.save_string,b.save_string_2, b.save_string_3,b.floor_sewing, b.sewing_line, a.location_id, a.knit_dye_source, a.knit_dye_company, a.location_sewing, a.issue_purpose, b.rate as cons_rate 
	from inv_trims_issue_dtls b, inv_issue_master a  where b.id=$data_ref[0] and a.id=b.mst_id");
	foreach ($data_array as $row)
	{ 
		$save_string_data=$row[csf("save_string")]."".$row[csf("save_string_2")]."".$row[csf("save_string_3")];	
		$conversion_fac=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b "," a.item_group_id=b.id and a.id=".$row[csf("prod_id")]."","conversion_factor");
		
		//echo "load_drop_down( 'requires/trims_issue_entry_controller', '".$row[csf("location_id")]."_".$row[csf("knit_dye_company")]."_".$row[csf("knit_dye_source")]."_".$row[csf("issue_purpose")]."', 'load_drop_down_floor', 'swing_floor_td');\n";
		//echo "load_drop_down('requires/trims_issue_entry_controller', ".$row[csf("floor_sewing")]."+'_'+".$row[csf('location_sewing')]."+'_'+".$row[csf('knit_dye_company')].", 'load_drop_down_sewing_line_floor','sewing_line_td');\n";
		echo "load_drop_down( 'requires/trims_issue_entry_controller', '".$row[csf("floor_sewing")]."_".$row[csf("location_sewing")]."_".$row[csf("knit_dye_company")]."', 'load_drop_down_sewing_line_floor', 'sewing_line_td' );\n";
		
		echo "document.getElementById('cbo_item_group').value 				= '".$row[csf("item_group_id")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_item_description').value 		= '".$row[csf("item_description")]."';\n";
		echo "document.getElementById('txt_issue_qnty').value 				= '".number_format($row[csf("issue_qnty")],2,'.','')."';\n";
		echo "document.getElementById('hidden_issue_qnty').value 			= '".number_format($row[csf("issue_qnty")],2,'.','')."';\n";
		echo "document.getElementById('txt_brand_supref').value 			= '".$row[csf("brand_supplier")]."';\n";
		
		/*echo "load_room_rack_self_bin('requires/trims_issue_entry_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 					= '".$row[csf("floor_id")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_issue_entry_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "document.getElementById('cbo_room').value 					= '".$row[csf("room")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_issue_entry_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack_no")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_issue_entry_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack_no')]."',this.value);\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf_no")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_issue_entry_controller', 'bin','bin_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack_no')]."','".$row[csf('shelf_no')]."',this.value);\n";
		echo "document.getElementById('cbo_bin').value 						= '".$row[csf("bin")]."';\n";*/

		if ($row[csf("room")]=="") { $row[csf("room")]=0; }
		if ($row[csf("rack_no")]=="") { $row[csf("rack_no")]=0; }
		if ($row[csf("shelf_no")]=="") { $row[csf("shelf_no")]=0; }
		if ($row[csf("bin")]=="") { $row[csf("bin")]=0; }

		echo "load_drop_down('requires/trims_issue_entry_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_floor','floor_td');\n";
		echo "document.getElementById('cbo_floor').value 					= '".$row[csf("floor_id")]."';\n";

		echo "load_drop_down('requires/trims_issue_entry_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_room','room_td');\n";
		echo "document.getElementById('cbo_room').value 					= '".$row[csf("room")]."';\n";

		echo "load_drop_down('requires/trims_issue_entry_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_rack','rack_td');\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack_no")]."';\n";

		echo "load_drop_down('requires/trims_issue_entry_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_shelf','shelf_td');\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf_no")]."';\n";

		echo "load_drop_down('requires/trims_issue_entry_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_bin','bin_td');\n";
		echo "document.getElementById('cbo_bin').value 						= '".$row[csf("bin")]."';\n";

		echo "$('#cbo_floor').attr('disabled','disabled');\n";
		echo "$('#cbo_room').attr('disabled','disabled');\n";
		echo "$('#txt_rack').attr('disabled','disabled');\n";
		echo "$('#txt_shelf').attr('disabled','disabled');\n";
		echo "$('#cbo_bin').attr('disabled','disabled');\n";

		echo "document.getElementById('txt_item_color').value 				= '".$color_arr[$row[csf("item_color_id")]]."';\n";
		echo "document.getElementById('txt_item_color_id').value 			= '".$row[csf("item_color_id")]."';\n";
		echo "document.getElementById('txt_gmts_color').value 				= '".$color_arr[$row[csf("gmts_color_id")]]."';\n";
		echo "document.getElementById('gmts_color_id').value 				= '".$row[csf("gmts_color_id")]."';\n";
		echo "document.getElementById('txt_item_size').value 				= '".$row[csf("item_size")]."';\n";
		echo "document.getElementById('txt_gmts_size').value 				= '".$size_arr[$row[csf("gmts_size_id")]]."';\n"; 
		echo "document.getElementById('gmts_size_id').value 				= '".$row[csf("gmts_size_id")]."';\n"; 
		echo "document.getElementById('all_po_id').value 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('selected_po_id').value 				= '".$row[csf("item_order_id")]."';\n";
		echo "document.getElementById('hidden_prod_id').value 				= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('previous_prod_id').value 			= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_trans_id').value 				= '".$row[csf("trans_id")]."';\n";
		echo "document.getElementById('save_data').value 					= '".$save_string_data."';\n";
		echo "document.getElementById('txt_conversion_faction').value 		= '".$conversion_fac."';\n";
		echo "document.getElementById('txt_cons_rate').value 				= '".number_format($row[csf("cons_rate")],4,'.','')."';\n";
		
		echo "document.getElementById('cbo_floor_swing').value 					= '".$row[csf("floor_sewing")]."';\n";
		
		//load_drop_down( 'requires/trims_issue_entry_controller', this.value+'_'+$data[0]+'_'+$data[1], 'load_drop_down_sewing_line_floor', 'sewing_line_td' );
		echo "document.getElementById('cbo_sewing_line').value 				= '".$row[csf("sewing_line")]."';\n";	
		
		if($issue_basis==2)
		{
			$order_no="";
			$buyer_name=return_field_value("buyer_id","wo_non_ord_samp_booking_mst","id='$booking_id'");
			
			echo "get_php_form_data('".$booking_id."'+'**'+".$row[csf("prod_id")].",'get_trim_cum_info_for_trims_booking','requires/trims_issue_entry_controller')".";\n";
			//echo "show_list_view('".$booking_id."','create_itemDesc_search_list_view_on_booking','list_fabric_desc_container','requires/trims_issue_entry_controller','');\n";
		}
		else
		{
			if($db_type==0)
			{
				$order_data=sql_select("select group_concat(a.po_number) as po_no, group_concat(distinct(b.buyer_name)) as buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id in(".$row[csf("item_order_id")].")");
			}
			else
			{
				$order_data=sql_select("select LISTAGG(cast(a.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as po_no, LISTAGG(b.buyer_name, ',') WITHIN GROUP (ORDER BY b.id) as buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id in(".$row[csf("item_order_id")].")");
			}
			
			$order_no=implode(",",array_unique(explode(",",$order_data[0][csf('po_no')])));//$order_data[0][csf('po_no')];
			$buyer_name=implode(",",array_unique(explode(",",$order_data[0][csf('buyer_name')])));//$order_data[0][csf('buyer_name')];
			
			echo "get_php_form_data('".$row[csf("order_id")]."'+'**'+".$row[csf("prod_id")].", 'get_trim_cum_info', 'requires/trims_issue_entry_controller')".";\n";
			echo "show_list_view('".$row[csf("order_id")]."'+'**'+".$row[csf("prod_id")]."+'**'+".$row[csf("store_id")]."+'**'+'".$data_ref[1]."', 'create_itemDesc_search_list_view','list_fabric_desc_container','requires/trims_issue_entry_controller','setFilterGrid(\'tbl_list_search\',-1);');\n";
			//echo "setFilterGrid('tbl_list_search',0);\n";
		}
		
		echo "document.getElementById('cbo_buyer_name').value 				= '".$buyer_name."';\n";
		echo "document.getElementById('txt_buyer_order').value 				= '".$order_no."';\n";

		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_trims_issue',1,1);\n";  
		exit();
	}
}

if ($action=="goods_placement_popup")
{
	echo load_html_head_contents("Goods Placement Entry Form", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$dtls_data=sql_select("select item_group_id, item_description, issue_qnty, prod_id from inv_trims_issue_dtls where id=$update_dtls_id");
	$trim_group_arr =array();
	$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
		$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
	}
	?> 
	<script>
		
		var permission='<? echo $permission; ?>';
		
		function fn_addRow( i )
		{ 
			var row_num=$('#txt_tot_row').val();
			row_num++;
			
			var clone= $("#tr_"+i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});
			
			clone.find("input,select").each(function(){
				  
			$(this).attr({ 
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
			  'name': function(_, name) { return name },
			  'value': function(_, value) { return value }              
			});
			 
			}).end();
			
			$("#tr_"+i).after(clone);
			
			$('#txtSelfNo_'+row_num).val('');
			$('#txtBoxBinNo_'+row_num).val('');
			$('#txtCtnNo_'+row_num).val('');
			$('#txtCtnQnty_'+row_num).val('');
			
			$('#increase_'+row_num).removeAttr("value").attr("value","+");
			$('#decrease_'+row_num).removeAttr("value").attr("value","-");
			$('#increase_'+row_num).removeAttr("onclick").attr("onclick","fn_addRow("+row_num+");");
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			
			$('#txt_tot_row').val(row_num);
		}
		
		function fn_deleteRow(rowNo) 
		{ 		
			var row_num=$('#tbl_list tbody tr').length;
			if(row_num!=1)
			{
				$("#tr_"+rowNo).remove();
			}
		}
		
		
		function fnc_goods_placement_entry(operation)
		{
			
			var dataString=""; var j=0;
			$("#tbl_list").find('tbody tr').each(function()
			{
				var txtRoomNo=$(this).find('select[name="txtRoomNo[]"]').val();
				var txtRackNo=$(this).find('select[name="txtRackNo[]"]').val();
				var txtSelfNo=$(this).find('select[name="txtSelfNo[]"]').val();
				var txtBoxBinNo=$(this).find('select[name="txtBoxBinNo[]"]').val();
				var txtCtnNo=$(this).find('select[name="txtCtnNo[]"]').val();
				var txtCtnQnty=$(this).find('input[name="txtCtnQnty[]"]').val();
				//alert(txtRackNo);
				if(!(txtRackNo==""))
				{
					j++;
					
					dataString+='&txtRoomNo_' + j + '=' + txtRoomNo + '&txtRackNo_' + j + '=' + txtRackNo + '&txtSelfNo_' + j + '=' + txtSelfNo + '&txtBoxBinNo_' + j + '=' + txtBoxBinNo + '&txtCtnNo_' + j + '=' + txtCtnNo + '&txtCtnQnty_' + j + '=' + txtCtnQnty;
				}
			});
			
			if(j==0)
			{
				alert("Please Insert At Least One Rack No.");
				return;	
			}
			
			var data="action=save_update_delete_goods_placement&operation="+operation+'&tot_row='+j+get_submitted_data_string('dtls_id',"../../../")+dataString;
			//alert(data);return;
			freeze_window(operation);
			
			http.open("POST","trims_issue_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_goods_placement_entry_Reply_info;
		}
		
		function fnc_goods_placement_entry_Reply_info()
		{
			if(http.readyState == 4) 
			{
				//alert(http.responseText);release_freezing();return;
				var reponse=trim(http.responseText).split('**');	
					
				show_msg(reponse[0]);
				
				if((reponse[0]==0 || reponse[0]==1 || reponse[0]==2))
				{
					reset_form('goodsPlacement_1','','','','','dtls_id');
					load_dtls_part();
				}
				
				set_button_status(reponse[2], permission, 'fnc_goods_placement_entry',1);	
				release_freezing();	
			}
		}
		
		function load_dtls_part()
		{
			var list_view_goods_placement = return_global_ajax_value( <? echo $update_dtls_id; ?>+"**"+<? echo $dtls_data[0][csf('prod_id')]; ?>, 'load_php_dtls_form', '', 'trims_issue_entry_controller');

			if(list_view_goods_placement!='')
			{
				$("#tbl_list tbody tr").remove();
				$("#tbl_list tbody").append(list_view_goods_placement);
				
				var row_num=$("#tbl_list tbody tr").length;
				$('#txt_tot_row').val(row_num);
			}
		}
    </script>

</head>

<body>
<div align="center">
	<? echo load_freeze_divs ("../../../",$permission,1); ?>
	<form name="goodsPlacement_1" id="goodsPlacement_1">
		<fieldset style="width:580px;">
        	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                <thead>
                    <th width="160">Item Group</th>
                    <th width="200">Item Description</th>
                    <th>Issue Qnty</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                    <td><p>&nbsp;<? echo $trim_group_arr[$dtls_data[0][csf('item_group_id')]]['name']; ?></p></td>
                    <td><p>&nbsp;<? echo $dtls_data[0][csf('item_description')]; ?></p></td>
                    <td align="right"><? echo number_format($dtls_data[0][csf('issue_qnty')],2); ?>&nbsp;</td>
                    <input type="hidden" name="dtls_id" id="dtls_id" class="text_boxes" value="<? echo $update_dtls_id; ?>">
                    <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="">
                </tr>
            </table>
        </fieldset> 
        <fieldset style="width:770px; margin-top:10px">
            <legend>New Entry</legend>
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="760" id="tbl_list">
            	<thead>
                    <th width="110">Room No</th>
                    <th width="110">Rack No</th>
                    <th width="110">Self No</th>
                    <th width="110">Box/Bin</th>
                    <th width="110">Ctn. No</th>
                    <th width="110">Ctn. Qnty</th>
                    <th></th>
                </thead>
                <tbody>
                </tbody>    
            </table>
			<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="760">
             	<tr>
                    <td colspan="7">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="7" align="center" class="button_container">
						<? 
							echo load_submit_buttons($permission, "fnc_goods_placement_entry", 0,0,"reset_form('goodsPlacement_1','','','','','txt_tot_row*dtls_id');$('#tbl_list tbody tr:not(:first)').remove();",1);
                        ?>
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                    </td>	  
                </tr>
			</table>
		</fieldset>
	</form>
</div>
</body>  
<script>
 	get_php_form_data(<? echo $update_dtls_id; ?>, "populate_data_goods_placement", "trims_issue_entry_controller" );
	load_dtls_part();
</script>	
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	
</html>
<?
}

if ($action=="save_update_delete_goods_placement")
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
		
		$field_array="id, entry_form, mst_id, trans_id, dtls_id, prod_id, room_no, rack_no, self_no, box_bin_no, ctn_no, ctn_qnty, inserted_by, insert_date";
		
		$dtls_data=sql_select("select mst_id, trans_id, prod_id from inv_trims_issue_dtls where id=$dtls_id");
		
		$data_array='';
		
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$txtRoomNo="txtRoomNo_".$j;
			$txtRackNo="txtRackNo_".$j;
			$txtSelfNo="txtSelfNo_".$j;
			$txtBoxBinNo="txtBoxBinNo_".$j;
			$txtCtnNo="txtCtnNo_".$j;
			$txtCtnQnty="txtCtnQnty_".$j;
			
			//if($id=="") $id=return_next_id( "id", "inv_goods_placement", 1 ) ; else $id = $id+1;
			$id=return_next_id_by_sequence("INV_GOODS_PLACEMENT_PK_SEQ", "inv_goods_placement", $con);
			
			if($data_array!="") $data_array.=",";
			
			$data_array.="(".$id.",25,".$dtls_data[0][csf('mst_id')].",".$dtls_data[0][csf('trans_id')].",".$dtls_id.",".$dtls_data[0][csf('prod_id')].",'".$$txtRoomNo."','".$$txtRackNo."','".$$txtSelfNo."','".$$txtBoxBinNo."','".$$txtCtnNo."','".$$txtCtnQnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		}
		
		if($data_array!="")
		{
			//echo $data_array;die;
			$rID=sql_insert("inv_goods_placement",$field_array,$data_array,1);
			if($rID) $flag=1; else $flag=0; 
		}
			
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'", '', $dtls_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".str_replace("'", '', $dtls_id)."**1";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
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
		
		$field_array="id, entry_form, mst_id, trans_id, dtls_id, prod_id, room_no, rack_no, self_no, box_bin_no, ctn_no, ctn_qnty, inserted_by, insert_date";
		$dtls_data=sql_select("select mst_id, trans_id, prod_id from inv_trims_issue_dtls where id=$dtls_id");
		
		$data_array='';
		
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$txtRoomNo="txtRoomNo_".$j;
			$txtRackNo="txtRackNo_".$j;
			$txtSelfNo="txtSelfNo_".$j;
			$txtBoxBinNo="txtBoxBinNo_".$j;
			$txtCtnNo="txtCtnNo_".$j;
			$txtCtnQnty="txtCtnQnty_".$j;
			
			//if($id=="") $id=return_next_id( "id", "inv_goods_placement", 1 ) ; else $id = $id+1;
			$id=return_next_id_by_sequence("INV_GOODS_PLACEMENT_PK_SEQ", "inv_goods_placement", $con);
			if($data_array!="") $data_array.=",";
			
			$data_array.="(".$id.",25,".$dtls_data[0][csf('mst_id')].",".$dtls_data[0][csf('trans_id')].",".$dtls_id.",".$dtls_data[0][csf('prod_id')].",'".$$txtRoomNo."','".$$txtRackNo."','".$$txtSelfNo."','".$$txtBoxBinNo."','".$$txtCtnNo."','".$$txtCtnQnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		}
		
		$delete=execute_query("delete from inv_goods_placement where dtls_id=$dtls_id and entry_form=25",0);
		if($delete) $flag=1; else $flag=0;
		
		if($data_array!="")
		{
			//echo $data_array;die;
			$rID=sql_insert("inv_goods_placement",$field_array,$data_array,1);
			if($flag==1)
			{
				if($rID) $flag=1; else $flag=0;
			} 
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$delete=execute_query("delete from inv_goods_placement where dtls_id=$dtls_id and entry_form=25",0);
		if($db_type==0)
		{
			if($delete)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($delete)
			{
				oci_commit($con); 
				echo "1**".str_replace("'", '', $update_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="load_php_dtls_form")
{
	$data=explode("**",$data);
	$dtls_id=$data[0];
	$prod_id=$data[1];
	
	if($db_type==0)
	{
		$recv_dataArray=sql_select("select group_concat(distinct(room_no)) as room_no, group_concat(distinct(rack_no)) as rack_no, group_concat(distinct(self_no)) as self_no, group_concat(distinct(box_bin_no)) as box_bin_no, group_concat(distinct(ctn_no)) as ctn_no from inv_goods_placement where prod_id=$prod_id and entry_form=24 and status_active=1 and is_deleted=0");
	}
	else
	{
		$recv_dataArray=sql_select("select LISTAGG(room_no, ',') WITHIN GROUP (ORDER BY id) as room_no, LISTAGG(rack_no, ',') WITHIN GROUP (ORDER BY id) as rack_no, LISTAGG(self_no, ',') WITHIN GROUP (ORDER BY id) as self_no, LISTAGG(id, ',') WITHIN GROUP (ORDER BY box_bin_no) as box_bin_no, LISTAGG(ctn_no, ',') WITHIN GROUP (ORDER BY id) as ctn_no from inv_goods_placement where prod_id=$prod_id and entry_form=24 and status_active=1 and is_deleted=0");	
	}
	
	$room_no_arr=explode(",",$recv_dataArray[0][csf('room_no')]);
	$room_no_arr=array_combine($room_no_arr,$room_no_arr);
	
	$rack_no_arr=explode(",",$recv_dataArray[0][csf('rack_no')]);
	$rack_no_arr=array_combine($rack_no_arr,$rack_no_arr);
	
	$self_no_arr=explode(",",$recv_dataArray[0][csf('self_no')]);
	$self_no_arr=array_combine($self_no_arr,$self_no_arr);
	
	$box_bin_no_arr=explode(",",$recv_dataArray[0][csf('box_bin_no')]);
	$box_bin_no_arr=array_combine($box_bin_no_arr,$box_bin_no_arr);
	
	$ctn_no_arr=explode(",",$recv_dataArray[0][csf('ctn_no')]);
	$ctn_no_arr=array_combine($ctn_no_arr,$ctn_no_arr);

	$sql="select room_no, rack_no, self_no, box_bin_no, ctn_no, ctn_qnty from inv_goods_placement where dtls_id=$dtls_id and entry_form=25 and status_active=1 and is_deleted=0";
	$result=sql_select($sql);
	$count=count($result);
	
	if($count==0 ) // New Insert
	{
	?>
        <tr id="tr_1">
            <td>
            	<select class="combo_boxes" id="txtRoomNo_1" name="txtRoomNo[]" style="width:110px">
                    <option value="">-- Select --</option>
					<?
                        foreach($room_no_arr as $key=>$value)
                        {
                        ?>
                            <option value="<? echo $key; ?>"><? echo $value; ?></option>
                        <?	
                        }
                        // echo create_drop_down( "txtRoomNo_1", 110, $room_no_arr,"",1, "-- Select --", 0, "",'','','','','','','','txtRoomNo[]' );
                    ?>
                </select>
            </td>
            <td>
            	<select class="combo_boxes" id="txtRackNo_1" name="txtRackNo[]" style="width:110px">
                    <option value="">-- Select --</option>
					<?
                        foreach($rack_no_arr as $key=>$value)
                        {
                        ?>
                            <option value="<? echo $key; ?>"><? echo $value; ?></option>
                        <?	
                        }
                        //  echo create_drop_down( "txtRackNo_1", 110, $rack_no_arr,"",1, "-- Select --", 0, "",'','','','','','','','txtRackNo[]' );
                    ?>
                </select>
            </td>
            <td>
            	<select class="combo_boxes" id="txtSelfNo_1" name="txtSelfNo[]" style="width:110px">
                    <option value="">-- Select --</option>
					<?
                        foreach($self_no_arr as $key=>$value)
                        {
                        ?>
                            <option value="<? echo $key; ?>"><? echo $value; ?></option>
                        <?	
                        }
                        // echo create_drop_down( "txtSelfNo_1", 110, $self_no_arr,"",1, "-- Select --", 0, "",'','','','','','','','txtSelfNo[]' ); 
                    ?>
                </select>
            </td>
            <td>
            	<select class="combo_boxes" id="txtBoxBinNo_1" name="txtBoxBinNo[]" style="width:110px">
                    <option value="">-- Select --</option>
					<?
                        foreach($box_bin_no_arr as $key=>$value)
                        {
                        ?>
                            <option value="<? echo $key; ?>"><? echo $value; ?></option>
                        <?	
                        }
                        // echo create_drop_down( "txtBoxBinNo_1", 110, $box_bin_no_arr,"",1, "-- Select --", 0, "",'','','','','','','','txtBoxBinNo[]' );
                    ?>
                </select>
            </td>
            <td>
            	<select class="combo_boxes" id="txtCtnNo_1" name="txtCtnNo[]" style="width:110px">
                    <option value="">-- Select --</option>
					<?
                        foreach($ctn_no_arr as $key=>$value)
                        {
                        ?>
                            <option value="<? echo $key; ?>"><? echo $value; ?></option>
                        <?	
                        }
                        // create_drop_down( "txtCtnNo_1", 110, $ctn_no_arr,"",1, "-- Select --", 0, "",'','','','','','','','txtCtnNo[]' );
                    ?>
                </select>
            </td>
            <td>
                <input type="text" name="txtCtnQnty[]" id="txtCtnQnty_1" class="text_boxes_numeric" style="width:100px;"/>
            </td>
            <td>
                <input type="button" id="increase_1" name="increase[]" style="width:35px" class="formbuttonplasminus" value="+" onClick="fn_addRow(1)" />
                <input type="button" id="decrease_1" name="decrease[]" style="width:35px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
            </td>
        </tr>
    <?
	}
	else // From Update
	{
		$i=0;
		foreach($result as $row)
		{
			$i++;
		?>
			<tr id="tr_<? echo $i; ?>">
            	<td>
                    <select class="combo_boxes" id="txtRoomNo_<? echo $i; ?>" name="txtRoomNo[]" style="width:110px">
                        <option value="">-- Select --</option>
                        <?
                            foreach($room_no_arr as $key=>$value)
                            {
                            ?>
                                <option value=<? echo $key; if($row[csf('room_no')]==$key) { ?> selected <? } ?>><? echo $value; ?></option>
                            <?	
                            }
                        ?>
                    </select>
                </td>
                <td>
                    <select class="combo_boxes" id="txtRackNo_<? echo $i; ?>" name="txtRackNo[]" style="width:110px">
                        <option value="">-- Select --</option>
                        <?
                            foreach($rack_no_arr as $key=>$value)
                            {
                            ?>
                                <option value=<? echo $key; if($row[csf('rack_no')]==$key) { ?> selected <? } ?>><? echo $value; ?></option>
                            <?	
                            }
                        ?>
                    </select>
                </td>
                <td>
                    <select class="combo_boxes" id="txtSelfNo_<? echo $i; ?>" name="txtSelfNo[]" style="width:110px">
                        <option value="">-- Select --</option>
                        <?
                            foreach($self_no_arr as $key=>$value)
                            {
                            ?>
                                <option value=<? echo $key; if($row[csf('self_no')]==$key) { ?> selected <? } ?>><? echo $value; ?></option>
                            <?	
                            }
                        ?>
                    </select>
                </td>
                <td>
                    <select class="combo_boxes" id="txtBoxBinNo_<? echo $i; ?>" name="txtBoxBinNo[]" style="width:110px">
                        <option value="">-- Select --</option>
                        <?
                            foreach($box_bin_no_arr as $key=>$value)
                            {
                            ?>
                                <option value=<? echo $key; if($row[csf('box_bin_no')]==$key) { ?> selected <? } ?>><? echo $value; ?></option>
                            <?	
                            }
                        ?>
                    </select>
                </td>
                <td>
                    <select class="combo_boxes" id="txtCtnNo_<? echo $i; ?>" name="txtCtnNo[]" style="width:110px">
                        <option value="">-- Select --</option>
                        <?
                            foreach($ctn_no_arr as $key=>$value)
                            {
                            ?>
                                <option value=<? echo $key; if($row[csf('ctn_no')]==$key) { ?> selected <? } ?>><? echo $value; ?></option>
                            <?	
                            }
                        ?>
                    </select>
                </td>
                <td>
                    <input type="text" name="txtCtnQnty[]" id="txtCtnQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:100px;" value="<? echo $row[csf('ctn_qnty')]; ?>"/>
                </td>
                <td>
                    <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:35px" class="formbuttonplasminus" value="+" onClick="fn_addRow(<? echo $i; ?>)" />
                    <input type="button" id="decrease_<? echo $i;?>" name="decrease[]" style="width:35px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);"/>
                </td>
            </tr>
		<?
		}		
	}
	
	exit();
}

if ($action=="populate_data_goods_placement")
{
	$result=sql_select("select id from inv_goods_placement where dtls_id=$data and entry_form=25 and status_active=1 and is_deleted=0");
	
	if(count($result)>0) $button_status=1; else $button_status=0;
	
	echo "set_button_status($button_status, '".$_SESSION['page_permission']."', 'fnc_goods_placement_entry',1,1);\n";  
	exit();
}

if ($action=="trims_issue_entry_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	echo load_html_head_contents($data[3], "../../", 1, 1, '', '', '');
	$trim_group_arr =array();
	$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
		$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
	}
	$buyer_short=return_library_array( "select id,short_name from lib_buyer", "id","short_name"  );
	$store_library=return_library_array( "select id,store_name from lib_store_location", "id","store_name"  );
	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor","id","floor_name");
	$sewing_line_arr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	
	$sql="select id, issue_number, issue_date, challan_no, issue_basis, issue_purpose, store_id, knit_dye_source, knit_dye_company, location_id, remarks from inv_issue_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=25 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	$issue_basis=$dataArray[0][csf("issue_basis")];
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$lib_floor_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "floor_id","floor_room_rack_name" ); 
	$lib_room_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "room_id","floor_room_rack_name" ); 
	$lib_rack_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "rack_id","floor_room_rack_name" ); 
	$lib_shelf_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "shelf_id","floor_room_rack_name" ); 
	$lib_bin_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "bin_id","floor_room_rack_name" ); 
	?>
	<div style="width:1050px;">
		<table width="1050" cellspacing="0" align="right" border="0" style="margin-bottom:10px">
			<tr>
				<td rowspan="2" colspan="">
					<img src="../../../<? echo $image_location; ?>" height="70" width="200" style="float:left;">
				</td>
				<td colspan="4" align="left" style="font-size:xx-large;">
					<strong  style="float:left; margin-left:32px;"><? echo $company_arr[$data[0]]; ?></strong>
				</td>
				<td colspan="2"  rowspan="2" align="right" id="barcode_img_id" width="260"></td>
			</tr>
			<tr class="form_caption">
				<td colspan="4" align="left" style="padding-left:65px;">
					<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						foreach ($nameArray as $result)
						{
						?>
							<? echo $result[csf('plot_no')]; ?>
							<? if($result[csf('level_no')]!="") echo ",".$result[csf('level_no')]?>
							<? if($result[csf('road_no')]!="") echo ",".$result[csf('road_no')]; ?>
							<? if($result[csf('block_no')]!="") echo ",".$result[csf('block_no')];?>
							<? if($result[csf('city')]!="") echo ",".$result[csf('city')];?>
							<? if($result[csf('zip_code')]!="") echo ",".$result[csf('zip_code')]; ?>
							<? if($result[csf('province')]!="") echo ",".$result[csf('province')];?>
							<? if($result[csf('country_id')]!="") echo ",".$country_arr[$result[csf('country_id')]]; ?><br>
							Email:<? if($result[csf('email')]!="") echo $result[csf('email')].",";?>
							Website:<? if($result[csf('website')]!="") echo $result[csf('website')];
						}
						$location='';
						if($dataArray[0][csf('knit_dye_source')]==1)
						{
							$caption="Location";
							$issueTo=$company_arr[$dataArray[0][csf('knit_dye_company')]];
							$location=return_field_value("location_name","lib_location","id='".$dataArray[0][csf('location_id')]."'");
						}
						else
						{
							$caption="Address";
							$supplierData=sql_select("select address_1, address_2, supplier_name from lib_supplier where id='".$dataArray[0][csf('knit_dye_company')]."'");
							$issueTo=$supplierData[0][csf('supplier_name')];
							$location=$supplierData[0][csf('address_1')];
							if($location=="") $location=$supplierData[0][csf('address_2')]; else $location.=", ".$supplierData[0][csf('address_2')];
						}
					?> 
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:x-large"><strong><u>Accessories Issue Challan</u></strong></center></td>
			</tr>
		</table>
			<table cellspacing="0" width="900" align="center" border="1" rules="all" class="">
			<tr>
				<td width="120"><strong>Issue No:</strong></td> <td width="150"><? echo $dataArray[0][csf('issue_number')]; ?></td>
				<td width="120"><strong>Issue Date :</strong></td><td width="150"><? echo  change_date_format($dataArray[0][csf('issue_date')]); ?></td>
				<td width="120"><strong>Issue Purpose :</strong></td><td width="150"><? echo  $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Challan No :</strong></td><td width="175px" ><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Store Name :</strong></td> <td><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
				<td><strong>Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Issue To :</strong></td><td><? echo $issueTo; ?></td>
				<td><strong><? echo $caption; ?> :</strong></td><td><? echo $location; ?></td>
				<td><strong>Remarks :</strong></td><td ><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:1100px;">
			<table align="left"  style="margin-bottom:20px;"  cellspacing="0" width="1100" border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="120" align="center">Item Group</th>
					<th width="140" align="center">Item Des.</th>
					<th width="70" align="center">Item Color</th>
					<th width="120" align="center">Job No.</th>
					<th width="120" align="center">Buyer</th>
					<th width="140" align="center">Style Ref.</th>
					<th width="150" align="center">Buyer Order</th>
					<th width="60" align="center">UOM </th>
					<th width="70" align="center">Item Size</th>
					<th width="80" align="center">Issue Qty</th>                
					<th width="70" align="center">Floor</th>
					<th width="70" align="center">Sewing Line</th>              
					<th width="70" align="center">Rack</th>
					<th width="70" align="center">Self</th>
				</thead>
				<?
					$i=1; 
					$mst_id=$dataArray[0][csf('id')];
					//$sql_dtls="select b.id, b.item_group_id, b.item_description, b.gmts_color_id, b.gmts_size_id, b.order_id, b.uom, b.issue_qnty, c.room_no, c.rack_no, c.self_no, c.box_bin_no, c.ctn_no, c.ctn_qnty from inv_trims_issue_dtls b left join inv_goods_placement c on b.id=c.dtls_id and c.entry_form=25 where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'";
					$sql_dtls="select id, item_group_id, item_description, item_color_id, item_size, order_id, item_order_id, uom, issue_qnty, rack_no, shelf_no,sewing_line,floor_id from inv_trims_issue_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'  order by id ASC";
					//echo $sql_dtls;
					$sql_result=sql_select($sql_dtls);
					
					foreach($sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$order_no=$row[csf('item_order_id')];
						if($issue_basis==1)
						{
							if($db_type==0)
							{
								$job_data=sql_select("select group_concat(DISTINCT a.job_no) as job_no, group_concat(DISTINCT a.style_ref_no) as style_ref_no, group_concat(DISTINCT a.buyer_name) as buyer_name, group_concat(b.po_number) as po_number
								from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($order_no) group by a.job_no,a.style_ref_no,a.buyer_name");
							
								$job_no=$job_data[0][csf('job_no')];
								$style_ref_no=$job_data[0][csf('style_ref_no')];
								$buyer=$job_data[0][csf('buyer_name')];
								$buyer_name='';
								foreach(explode(',',$buyer) as $buyer_id){
									if($buyer_name)$buyer_name=$buyer_short[$buyer_id].',';	else $buyer_name=$buyer_short[$buyer_id];
								}
								$po_no=$job_data[0][csf('po_no')];
							}
							else
							{
								$job_data=sql_select("select LISTAGG(cast(a.job_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as job_no,
								LISTAGG(cast(a.style_ref_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as style_ref_no,
								LISTAGG(cast(a.buyer_name as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as buyer_name,
								LISTAGG(cast(b.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_no
								from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($order_no) group by a.job_no,a.style_ref_no,a.buyer_name");
								$job_no=implode(',',array_unique(explode(',',$job_data[0][csf('job_no')])));
								$style_ref_no=implode(',',array_unique(explode(',',$job_data[0][csf('style_ref_no')])));
								$buyer=array_unique(explode(',',$job_data[0][csf('buyer_name')]));
								
								$buyer_name='';
								foreach($buyer as $buyer_id){
									if($buyer_name)$buyer_name=$buyer_short[$buyer_id].',';	else $buyer_name=$buyer_short[$buyer_id];
								}
								$po_no=implode(',',array_unique(explode(',',$job_data[0][csf('po_no')])));
							}
						}
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td><? echo $i; ?></td>
							<td style="word-break:break-all;"><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></td>
							<td style="word-break:break-all;"><? echo $row[csf('item_description')]; ?></td>
							<td style="word-break:break-all;"><? echo $color_arr[$row[csf('item_color_id')]]; ?></td>
							<td style="word-break:break-all;"><? echo $job_no; ?></td>
							<td align="center" style="word-break:break-all;"><? echo $buyer_name; ?></td>
							<td align="center" style="word-break:break-all;"><? echo $style_ref_no; ?></td><!--style="font-size:12px; overflow:hidden; text-overflow: ellipsis; white-space: nowrap;"  style="overflow:hidden; text-overflow: ellipsis; white-space: nowrap; font-weight:bold;"-->
							<td align="center" style="word-break:break-all;"><? echo $po_no; ?></td>
							<td align="center" style="word-break:break-all;"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
							<td align="center" style="word-break:break-all;"><? echo $row[csf('item_size')]; ?></td>
							<td align="right"><? echo number_format($row[csf('issue_qnty')],2,'.',''); ?></td>
							<td align="center" style="word-break:break-all;"><? echo $lib_floor_arr[$row[csf('floor_id')]]; ?></td>
							<td align="right" style="word-break:break-all;"><? echo $sewing_line_arr[$row[csf('sewing_line')]]; ?></td>                       
							<td align="center" style="word-break:break-all;"><? echo $lib_rack_arr[$row[csf('rack_no')]]; ?></td>
							<td align="center" style="word-break:break-all;"><? echo $lib_shelf_arr[$row[csf('shelf_no')]]; ?></td>
						</tr>
					<?
						$i++;
					}
					
				?>
			</table>
			
			<table  width="500" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
				<thead>
					<tr>
						<th>UOM</th>
						<th>Recv. Qty</th>
						<th>Cuml. Issue Qty</th>
						<th>Balance Qty</th>
						<th>Remarks</th>
					</tr>
				</thead>
				<? 		
					$total_uom_wise = array();
					//echo "select prod_id, issue_qnty, uom, order_id from inv_trims_issue_dtls where mst_id ='$data[1]'";
					$trims_sql = sql_select("select prod_id, issue_qnty, uom, order_id from inv_trims_issue_dtls where mst_id ='$data[1]'");
					
					foreach($trims_sql as $row)
					{
						$po_id=$row[csf('order_id')];
						$prod_id=$row[csf('prod_id')]; 
				        $qty = $row['ISSUE_QNTY'];
						$dataArray=sql_select("select sum(case when trans_type in(1,4,5) then quantity end) as recv_qnty, sum(case when trans_type in(2,3,6) then quantity end) as issue_qnty from order_wise_pro_details where po_breakdown_id in($po_id) and prod_id='$prod_id' and status_active=1 and is_deleted=0 and entry_form in(24,25,49,73,78,112)");
						
						$recv_qnty=$dataArray[0][csf('recv_qnty')];
						$issue_qnty = $dataArray[0][csf('issue_qnty')];
						$key=$prod_id;
						$total_uom_wise[$key]['po_id'] = $po_id;
						$total_uom_wise[$key]['prod_id'] = $prod_id;
						$total_uom_wise[$key]['uom'] = $row[csf('uom')];
						$total_uom_wise[$key]['recv'] = $recv_qnty;
						$total_uom_wise[$key]['issue'] = $issue_qnty;
						$total_uom_wise[$key]['balance'] = $recv_qnty-$issue_qnty;	

						//print_r($dataArray); 
					}
					//print_r($total_uom_wise); 

					$summed_uom_values = array();

					foreach ($total_uom_wise as $item) {
						$uom = $item['uom'];
						
						if (!isset($summed_uom_values[$uom])) {
							$summed_uom_values[$uom] = array('recv' => 0, 'issue' => 0, 'balance' => 0);
						}
						
						$summed_uom_values[$uom]['recv'] += $item['recv'];
						$summed_uom_values[$uom]['issue'] += $item['issue'];
						$summed_uom_values[$uom]['balance'] += $item['balance'];
					}



					// echo "<pre>";
					// print_r($summed_uom_values);
					
					?><tbody><?
					$total_rcv_qty = $total_cuml_issue_qty = $total_balance_qty = 0;
					foreach($summed_uom_values as $k=>$value)
					{
						?>	
						
							<tr>
								<td align="center">
								<? echo $unit_of_measurement[$k];?>
								</td>
								<td align="center">
								<? echo number_format($value['recv'],2,'.','');?>
								</td>
								<td align="center">
								<? 
								echo number_format($value['issue'],2,'.',''); ?>
								</td>
								<td align="center">
								<? echo number_format($value['balance'],2,'.','');  ?>
								</td>
								<td align="center">
								<?   echo ""; ?>
								</td>
							</tr>
						
						<?
						$total_rcv_qty += $value['recv'];
						$total_cuml_issue_qty += $value['issue'];
						$total_balance_qty += $value['balance'];
					}
				?>	
				</tbody>
				<tfoot>
					<tr style="background-color: #d0cccc;" class="tbl_bottom">
						<td align="center">Total</td>
						<td align="center"><?= number_format($total_rcv_qty,2,'.',''); ?></td>
						<td align="center"><?= number_format($total_cuml_issue_qty,2,'.',''); ?></td>
						<td align="center"><?= number_format($total_balance_qty,2,'.',''); ?></td>
						<td align="center"></td>
					</tr>
				</tfoot>
				</table>
				
			</table>
			<?
				echo signature_table(36, $data[0], "1000px");
			?>
		</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
			// alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			
			var settings = {
			output:renderer,
			bgColor: '#FFFFFF',
			color: '#000000',
			barWidth: 1,
			barHeight: 30,
			moduleSize:5,
			posX: 10,
			posY: 20,
			addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $data[2]; ?>');
	</script>
	<?
	exit();
}
//for Urmi
if ($action=="trims_issue_entry_print2")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	echo load_html_head_contents($data[3], "../../", 1, 1, '', '', '');
	//print_r ($data);
	$trim_group_arr =array();
	$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
		$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
	}
	$buyer_short=return_library_array( "select id,short_name from lib_buyer", "id","short_name"  );
	$store_library=return_library_array( "select id,store_name from lib_store_location", "id","store_name"  );
	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_arr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");
	$po_number_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor","id","floor_name");
	$sewing_line_arr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	
	$sql="select id, issue_number, issue_date, 	issue_basis, challan_no,issue_purpose, store_id, knit_dye_source, knit_dye_company, location_id, remarks, inserted_by from inv_issue_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=25 ";
	//echo $sql;
	
	$dataArray=sql_select($sql);
	$issue_basis=$dataArray[0][csf('issue_basis')];
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$lib_floor_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "floor_id","floor_room_rack_name" ); 
	
	$lib_sewing_floor_arr=return_library_array( "select id,floor_name from lib_prod_floor  where status_active =1 and is_deleted=0", "id","floor_name" ); 
	$lib_room_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "room_id","floor_room_rack_name" ); 
	$lib_rack_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "rack_id","floor_room_rack_name" ); 
	$lib_shelf_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "shelf_id","floor_room_rack_name" ); 
	$lib_bin_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "bin_id","floor_room_rack_name" ); 
	?>
	<div style="width:1170px;">
		<table width="1000" cellspacing="0" align="right" border="0" style="margin-bottom:10px">
			<tr>
				<td rowspan="2" colspan="" >
					<img src="../../../<? echo $image_location; ?>" height="70" width="200" style="float:left;">
				</td>
				<td colspan="4" align="center" style="font-size:xx-large;">
					<strong style="float:left; margin-left:18px;"><? echo $company_arr[$data[0]]; ?></strong>
				</td>
				<td colspan="2"  rowspan="2" align="right" id="barcode_img_id"></td>
			</tr>
			<tr class="form_caption">
				<td colspan="4" align="left" style="padding-left:50px;">
					<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						foreach ($nameArray as $result)
						{
						?>
							<? echo $result[csf('plot_no')]; ?>
							<? if($result[csf('level_no')]!="") echo ",".$result[csf('level_no')]?>
							<? if($result[csf('road_no')]!="") echo ",".$result[csf('road_no')]; ?>
							<? if($result[csf('block_no')]!="") echo ",".$result[csf('block_no')];?>
							<? if($result[csf('city')]!="") echo ",".$result[csf('city')];?>
							<? if($result[csf('zip_code')]!="") echo ",".$result[csf('zip_code')]; ?>
							<? if($result[csf('province')]!="") echo ",".$result[csf('province')];?>
							<? if($result[csf('country_id')]!="") echo ",".$country_arr[$result[csf('country_id')]]; ?><br>
							Email:<? if($result[csf('email')]!="") echo $result[csf('email')].",";?>
							Website:<? if($result[csf('website')]!="") echo $result[csf('website')];
						}
						$location='';
						if($dataArray[0][csf('knit_dye_source')]==1)
						{
							$caption="Location";
							$issueTo=$company_arr[$dataArray[0][csf('knit_dye_company')]];
							$location=return_field_value("location_name","lib_location","id='".$dataArray[0][csf('location_id')]."'");
						}
						else
						{
							$caption="Address";
							$supplierData=sql_select("select address_1, address_2, supplier_name from lib_supplier where id='".$dataArray[0][csf('knit_dye_company')]."'");
							$issueTo=$supplierData[0][csf('supplier_name')];
							$location=$supplierData[0][csf('address_1')];
							if($location=="") $location=$supplierData[0][csf('address_2')]; else $location.=", ".$supplierData[0][csf('address_2')];
						}
					?> 
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:x-large"><strong><u>Accessories Issue Challan</u></strong></center></td>
			</tr>
			</table>
			<table cellspacing="0" width="920" align="center" border="1" rules="all" class="">
			<tr>
				<td width="90"><strong>Issue No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
				<td width="90"><strong>Issue Date :</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('issue_date')]); ?></td>
				<td width="95"><strong>Issue Purpose :</strong></td><td width="175px"><? echo  $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Challan No :</strong></td><td width="175px" ><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Store Name :</strong></td> <td><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
				<td><strong>Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Issue To :</strong></td><td><? echo $issueTo; ?></td>
				<td><strong><? echo $caption; ?> :</strong></td><td><? echo $location; ?></td>
				<td><strong>Remarks :</strong></td><td ><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
			
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="1150"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="120" align="center">Item Group</th>
					<th width="140" align="center">Item Des.</th>
					<th width="70" align="center">Item Color</th>
					<th width="70" align="center">Job No.</th>
					<th width="70" align="center">Buyer</th>
					<th width="70" align="center">Style Ref.</th>
					<th width="120" align="center">Buyer Order</th>
					<th width="80" align="center">Gmts. Color </th>
                    <th width="70" align="center">Gmts. Size </th>
                    <th width="60" align="center">UOM </th>
					<th width="70" align="center">Item Size</th>
					<th width="80" align="center">Issue Qty</th>
					<th width="100" align="center">Sewing Floor</th>
					<th width="80" align="center">Sewing Line</th>
					<th width="70" align="center">Floor</th>
					<th width="70" align="center">Rack</th>
					<th width="70" align="center">Self</th>
				</thead>
				<?
					$i=1; 
					$mst_id=$dataArray[0][csf('id')];
					//$sql_dtls="select b.id, b.item_group_id, b.item_description, b.gmts_color_id, b.gmts_size_id, b.order_id, b.uom, b.issue_qnty, c.room_no, c.rack_no, c.self_no, c.box_bin_no, c.ctn_no, c.ctn_qnty from inv_trims_issue_dtls b left join inv_goods_placement c on b.id=c.dtls_id and c.entry_form=25 where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'";
					if($issue_basis==2)//Without Order
					{
						$sql_dtls="select id, item_group_id, item_description, item_color_id, item_size, order_id, uom, issue_qnty, rack_no, shelf_no,sewing_line,floor_id,floor_sewing, gmts_color_id, gmts_size_id 
						from inv_trims_issue_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0' order by id ASC";
						//echo $sql_dtls;
						
					}
					else if($issue_basis==1 || $issue_basis==3)
					{
						$sql_dtls="select b.id, b.item_group_id, b.item_description, b.item_color_id, b.item_size, b.order_id, b.uom, c.po_breakdown_id as po_id,c.quantity as issue_qnty, b.rack_no, b.shelf_no,b.sewing_line,b.floor_id,floor_sewing, b.gmts_color_id, b.gmts_size_id 
						from inv_trims_issue_dtls b, order_wise_pro_details c 
						where c.dtls_id=b.id and c.prod_id=b.prod_id and c.entry_form=25 and  b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'  order by b.id ASC";
					}
					//echo $sql_dtls;
					$sql_result=sql_select($sql_dtls);
					foreach($sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						//$order_no=$row[csf('order_id')];
						$order_no=$row[csf('po_id')];
						if($issue_basis==1 || $issue_basis==3)
						{
							if($db_type==0)
							{
								$job_data=sql_select("select group_concat(DISTINCT a.job_no) as job_no, group_concat(DISTINCT a.style_ref_no) as style_ref_no, group_concat(DISTINCT a.buyer_name) as buyer_name
								from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($order_no) group by a.job_no,a.style_ref_no,a.buyer_name");
								$job_no=$job_data[0][csf('job_no')];
								$style_ref_no=$job_data[0][csf('style_ref_no')];
								$buyer=$job_data[0][csf('buyer_name')];
								$buyer_name='';
								foreach(explode(',',$buyer) as $buyer_id){
									if($buyer_name)$buyer_name=$buyer_short[$buyer_id].',';	else $buyer_name=$buyer_short[$buyer_id];
								}
							}
							else
							{
								$job_data=sql_select("select LISTAGG(cast(a.job_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as job_no,
								LISTAGG(cast(a.style_ref_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as style_ref_no,
								LISTAGG(cast(a.buyer_name as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as buyer_name
								from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($order_no) group by a.job_no,a.style_ref_no,a.buyer_name");
								$job_no=implode(',',array_unique(explode(',',$job_data[0][csf('job_no')])));
								$style_ref_no=implode(',',array_unique(explode(',',$job_data[0][csf('style_ref_no')])));
								$buyer=array_unique(explode(',',$job_data[0][csf('buyer_name')]));
								$buyer_name='';
								foreach($buyer as $buyer_id){
									if($buyer_name)$buyer_name=$buyer_short[$buyer_id].',';	else $buyer_name=$buyer_short[$buyer_id];
								}
							}
						}
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td><? echo $i; ?></td>
							<td style="word-break:break-all"><p><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></p></td>
							<td style="word-break:break-all"><p><? echo $row[csf('item_description')]; ?></p></td>
							<td style="word-break:break-all"><p><? echo $color_arr[$row[csf('item_color_id')]]; ?></p></td>
							<td style="word-break:break-all"><p><? echo $job_no; ?></p></td>
							<td style="word-break:break-all"><p><? echo $buyer_name; ?></p></td>
							<td style="word-break:break-all"><p><? echo $style_ref_no; ?></p></td>
							<td style="word-break:break-all"><p><? echo $po_number_arr[$row[csf('po_id')]]; ?></p></td>
                            <td style="word-break:break-all"><p><? echo $color_arr[$row[csf('gmts_color_id')]]; ?></p></td>
                            <td style="word-break:break-all"><p><? echo $size_arr[$row[csf('gmts_size_id')]]; ?></p></td>
							<td align="center" style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
							<td align="center" style="word-break:break-all"><? echo $row[csf('item_size')]; ?></td>
							<td align="right"><? echo number_format($row[csf('issue_qnty')],2,'.',''); ?></td>
							<td align="center" style="word-break:break-all"><? echo $lib_sewing_floor_arr[$row[csf('floor_sewing')]]; ?></td>
							<td align="center" style="word-break:break-all"><? echo $sewing_line_arr[$row[csf('sewing_line')]]; ?></td>
							<td align="center" style="word-break:break-all"><? echo $lib_floor_arr[$row[csf('floor_id')]]; ?></td>
							<td align="center" style="word-break:break-all"><p><? echo $lib_rack_arr[$row[csf('rack_no')]]; ?></p></td>
							<td align="center" style="word-break:break-all"><p><? echo $lib_shelf_arr[$row[csf('shelf_no')]]; ?></p></td>
						</tr>
						<?
						$i++;
					}
				?>
			</table>
			<br>
			<?
				echo signature_table(36, $data[0], "1150px",'','',$dataArray[0][csf('inserted_by')]);
			?>
		</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>

		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
			// alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			
			var settings = {
			output:renderer,
			bgColor: '#FFFFFF',
			color: '#000000',
			barWidth: 1,
			barHeight: 30,
			moduleSize:5,
			posX: 10,
			posY: 20,
			addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $data[2]; ?>');
	</script>
	<?
	exit();
}

if ($action=="trims_issue_entry_print3")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	echo load_html_head_contents($data[3], "../../", 1, 1, '', '', '');
	//print_r ($data);
	$system_no = $data[2];
	$trim_group_arr =array();
	$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
		$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
	}
	$buyer_short=return_library_array( "select id,short_name from lib_buyer", "id","short_name"  );
	$store_library=return_library_array( "select id,store_name from lib_store_location", "id","store_name"  );
	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$department_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$po_number_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor","id","floor_name");
	$sewing_line_arr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$trims_issue_basis=array(1=>"With Order",2=>"Without Order",3=>"Requisition");
	
	$sql="SELECT id as ID, issue_number as ISSUE_NUMBER, issue_date as ISSUE_DATE, 	issue_basis as ISSUE_BASIS, challan_no as CHALLAN_NO,issue_purpose as ISSUE_PURPOSE, store_id as STORE_ID, knit_dye_source as KNIT_DYE_SOURCE, knit_dye_company as KNIT_DYE_COMPANY, location_id as LOCATION_ID, attention as ATTENTION, remarks as REMARKS from inv_issue_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=25 ";
	//echo $sql;
	
	$dataArray=sql_select($sql);
	$issue_basis=$dataArray[0]['ISSUE_BASIS'];
	$sewing_source=$dataArray[0]['KNIT_DYE_SOURCE'];
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");	
	$lib_sewing_floor_arr=return_library_array( "select id,floor_name from lib_prod_floor  where status_active =1 and is_deleted=0", "id","floor_name" ); 
	
	//for gate pass
	$sql_get_pass = "SELECT a.id as ID, a.sys_number as SYS_NUMBER, a.basis as BASIS, a.company_id as COMPANY_ID, a.get_pass_no as GET_PASS_NO, a.department_id as DEPARTMENT_ID, a.attention as ATTENTION, a.sent_by as SENT_BY, a.within_group as WITHIN_GROUP, a.sent_to as SENT_TO, a.challan_no as CHALLAN_NO, a.out_date as OUT_DATE, a.time_hour as TIME_HOUR, a.time_minute as TIME_MINUTE, a.returnable as RETURNABLE, a.delivery_as as DELIVERY_AS, a.est_return_date as EST_RETURN_DATE, a.inserted_by as INSERTED_BY, a.carried_by as CARRIED_BY, a.location_id as LOCATION_ID, a.com_location_id as COM_LOCATION_ID, a.vhicle_number as VHICLE_NUMBER, a.location_name as LOCATION_NAME, a.remarks as REMARKS, a.do_no as DO_NO, a.mobile_no as MOBILE_NO, a.issue_id as ISSUE_ID, a.returnable_gate_pass_reff as RETURNABLE_GATE_PASS_REFF, a.delivery_company as DELIVERY_COMPANY, a.issue_purpose as ISSUE_PURPOSE,a.security_lock_no as SECURITY_LOCK_NO,a.driver_name as DRIVER_NAME,a.driver_license_no as DRIVER_LICENSE_NO, b.quantity as QUANTITY, b.no_of_bags as NO_OF_BAGS FROM inv_gate_pass_mst a, inv_gate_pass_dtls b WHERE a.id = b.mst_id AND a.company_id ='$data[0]' AND a.basis = 7 AND a.status_active = 1 AND a.is_deleted = 0 AND a.challan_no LIKE '".$system_no."%'";
	// echo $sql_get_pass;
	$sql_get_pass_rslt = sql_select($sql_get_pass);
	$is_gate_pass = 0;
	$is_gate_out = 0;
	$gate_pass_id = '';
	$gatePassDataArr = array();
	foreach($sql_get_pass_rslt as $row)
	{
		$exp = explode(',', $row['CHALLAN_NO']);
		foreach($exp as $key=>$val)
		{
			if($val == $system_no)
			{
				$is_gate_pass = 1;
				$gate_pass_id = $row['ID'];
				
				$row['OUT_DATE'] = ($row['OUT_DATE']!=''?date('d-m-Y', strtotime($row['OUT_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				
				if($row['WITHIN_GROUP'] == 1)
				{
					//$row['SENT_TO'] = ($row['BASIS']==50?$buyer_dtls_arr[$row['SENT_TO']]:$supplier_dtls_arr[$row['SENT_TO']]);
					$row['SENT_TO'] = $company_arr[$row['SENT_TO']];
					$row['LOCATION_NAME'] = $location_arr[$row['LOCATION_ID']];
				}
				
				//for gate pass info
				$gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_id'] = $row['SYS_NUMBER'];
				$gatePassDataArr[$row['CHALLAN_NO']]['from_company'] = $company_arr[$row['COMPANY_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['from_location'] =$location_arr[ $row['COM_LOCATION_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$row['CHALLAN_NO']]['returnable'] = $yes_no[$row['RETURNABLE']];
				$gatePassDataArr[$row['CHALLAN_NO']]['est_return_date'] = $row['EST_RETURN_DATE'];
				
				$gatePassDataArr[$row['CHALLAN_NO']]['to_company'] = $row['SENT_TO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['to_location'] = $row['LOCATION_NAME'];
				$gatePassDataArr[$row['CHALLAN_NO']]['delivery_kg'] += $row['QUANTITY'];
				$gatePassDataArr[$row['CHALLAN_NO']]['delivery_bag'] += $row['NO_OF_BAGS'];
				
				$gatePassDataArr[$row['CHALLAN_NO']]['department'] = $department_arr[$row['DEPARTMENT_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['attention'] = $row['ATTENTION'];
				$gatePassDataArr[$row['CHALLAN_NO']]['issue_purpose'] = $row['ISSUE_PURPOSE'];
				$gatePassDataArr[$row['CHALLAN_NO']]['remarks'] = $row['REMARKS'];
				$gatePassDataArr[$row['CHALLAN_NO']]['carried_by'] = $row['CARRIED_BY'];
				$gatePassDataArr[$row['CHALLAN_NO']]['vhicle_number'] = $row['VHICLE_NUMBER'];
				$gatePassDataArr[$row['CHALLAN_NO']]['mobile_no'] = $row['MOBILE_NO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['security_lock_no'] = $row['SECURITY_LOCK_NO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['driver_name'] = $row['DRIVER_NAME'];
				$gatePassDataArr[$row['CHALLAN_NO']]['driver_license_no'] = $row['DRIVER_LICENSE_NO'];
			}
		}
	}
	
	//for gate out
	if($gate_pass_id != '')
	{
		$sql_gate_out="SELECT out_date as OUT_DATE, out_time as OUT_TIME from inv_gate_out_scan where status_active = 1 and is_deleted = 0 and inv_gate_pass_mst_id='".$gate_pass_id."'";
		$sql_gate_out_rslt = sql_select($sql_gate_out);
		if(!empty($sql_gate_out_rslt))
		{
			foreach($sql_gate_out_rslt as $row)
			{
				$is_gate_out = 1;
				$gatePassDataArr[$system_no]['out_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$system_no]['out_time'] = $row['OUT_TIME'];
			}
		}
	}
	?>
	<div style="width:1030px;">
		<table width="1000" cellspacing="0" border="0" style="margin-bottom:10px">
			<tr>
				<td rowspan="2" >
					<img src="../../../<? echo $image_location; ?>" height="70" width="150" style="float:left;">
				</td>
				<td colspan="4" align="center" style="font-size:xx-large;">
					<strong ><? echo $company_arr[$data[0]]; ?></strong>
				</td>
				<td colspan="2" rowspan="2" align="right" width="150"><?php echo ($is_gate_pass==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Pass Done</span>":'').($is_gate_out==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Out Done</span>":''); ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="4" align="center" >
					<?
						$nameArray=sql_select( "SELECT plot_no as PLOT_NO, level_no as LEVEL_NO, road_no as ROAD_NO, block_no as BLOCK_NO, country_id as COUNTRY_ID, province as PROVINCE, city as CITY, zip_code as ZIP_CODE, email as EMAIL, website as WEBSITE from lib_company where id=$data[0]");
						foreach ($nameArray as $result)
						{
						?>
							<? echo $result['PLOT_NO']; ?>
							<? if($result['LEVEL_NO']!="") echo ",".$result['LEVEL_NO']?>
							<? if($result['ROAD_NO']!="") echo ",".$result['ROAD_NO']; ?>
							<? if($result['BLOCK_NO']!="") echo ",".$result['BLOCK_NO'];?>
							<? if($result['CITY']!="") echo ",".$result['CITY'];?>
							<? if($result['ZIP_CODE']!="") echo ",".$result['ZIP_CODE']; ?>
							<? if($result['PROVINCE']!="") echo ",".$result['PROVINCE'];?>
							<? if($result['COUNTRY_ID']!="") echo ",".$country_arr[$result['COUNTRY_ID']]; ?><br>
							Email:<? if($result['EMAIL']!="") echo $result['EMAIL'].",";?>
							Website:<? if($result['WEBSITE']!="") echo $result['WEBSITE'];
						}
						$location='';
						if($dataArray[0]['KNIT_DYE_SOURCE']==1)
						{
							$caption="Location";
							$issueTo=$company_arr[$dataArray[0]['KNIT_DYE_COMPANY']];
							$location=return_field_value("location_name","lib_location","id='".$dataArray[0]['LOCATION_ID']."'");
						}
						else
						{
							$caption="Address";
							$supplierData=sql_select("SELECT address_1 as ADDRESS_1, address_2 as ADDRESS_2, supplier_name as SUPPLIER_NAME from lib_supplier where id='".$dataArray[0]['KNIT_DYE_COMPANY']."'");
							$issueTo=$supplierData[0]['SUPPLIER_NAME'];
							$location=$supplierData[0]['ADDRESS_1'];
							if($location=="") $location=$supplierData[0]['ADDRESS_2']; else $location.=", ".$supplierData[0]['ADDRESS_2'];
						}
					?> 
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:x-large"><strong><u>ACCESSORIES DELIVERY CHALLAN</u></strong></center></td>
			</tr>
			</table>
			<table cellspacing="0" width="920" align="center" border="1" rules="all" class="">
			<tr>
				<td width="90"><strong>Source:</strong></td> <td width="175px"><? echo $knitting_source[$dataArray[0]['KNIT_DYE_SOURCE']]; ?></td>
				<td width="90"><strong>Store Name:</strong></td><td width="175px"><? echo $store_library[$dataArray[0]['STORE_ID']];?></td>
				<td width="95"><strong>Issue No:</strong></td><td width="175px"><? echo $dataArray[0]['ISSUE_NUMBER']; ?></td>
			</tr>
			<tr>
				<td ><strong>Issue To:</strong></td> <td ><? echo $issueTo; ?></td>
				<td><strong>Issue Purpose:</strong></td><td ><? echo $yarn_issue_purpose[$dataArray[0]['ISSUE_PURPOSE']]; ?></td>
				<td ><strong>Issue Date:</strong></td><td ><? echo change_date_format($dataArray[0]['ISSUE_DATE']);?></td>
			</tr>
			<tr>
				<td ><strong>Address:</strong></td> <td><? echo $location; ?></td>
				<td><strong>Issue Basis:</strong></td><td ><? echo $trims_issue_basis[$dataArray[0]['ISSUE_BASIS']]; ?></td>
				<td ><strong>Attention:</strong></td><td ><? echo $dataArray[0]['ATTENTION'];?></td>
			</tr>
			<tr>
				<td><strong>Remarks :</strong></td><td colspan="5"><? echo $dataArray[0]['REMARKS']; ?></td>
			</tr>
			<tr>
				<td colspan="6"  rowspan="2" align="center" id="barcode_img_id" height="50" valign="middle" style="border-left:hidden;border-right:hidden;border-bottom:hidden;"></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="100" align="center">Item Group</th>
					<th width="120" align="center">Item Des.</th>
					<th width="60" align="center">Item Color</th>
					<th width="100" align="center">Job No.</th>
					<th width="70" align="center">Buyer</th>
					<th width="100" align="center">Style Ref.</th>
					<th width="100" align="center">Buyer Order</th>
					<th width="50" align="center">UOM </th>
					<th width="60" align="center">Item Size</th>
					<th width="60" align="center">Issue Qty</th>
					<th width="70" align="center">Sewing Floor</th>
					<th align="center">Sewing Line</th>
				</thead>
				<?
					$i=1; 
					$mst_id=$dataArray[0][csf('id')];
					if($issue_basis==2)//Without Order
					{
						$sql_dtls="SELECT id as ID, item_group_id as ITEM_GROUP_ID, item_description as ITEM_DESCRIPTION, item_color_id as ITEM_COLOR_ID, item_size as ITEM_SIZE, order_id as ORDER_ID, uom as UOM, issue_qnty as ISSUE_QNTY, sewing_line as SEWING_LINE, floor_sewing as FLOOR_SEWING from inv_trims_issue_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0' order by id ASC";
						//echo $sql_dtls;
						$sql_result=sql_select($sql_dtls);
					}
					else if($issue_basis==1)
					{
						$sql_dtls="SELECT b.id as ID, b.item_group_id as ITEM_GROUP_ID, b.item_description as ITEM_DESCRIPTION, b.item_color_id as ITEM_COLOR_ID, b.item_size as ITEM_SIZE, b.order_id as ORDER_ID, b.uom as UOM, c.po_breakdown_id as PO_ID,c.quantity as ISSUE_QNTY, b.sewing_line as SEWING_LINE, floor_sewing as FLOOR_SEWING from inv_trims_issue_dtls b,order_wise_pro_details c where c.dtls_id=b.id and c.prod_id=b.prod_id and c.entry_form=25 and  b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'  order by b.id ASC";
						$sql_result=sql_select($sql_dtls);
					}
					
					foreach($sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$order_no=$row['ORDER_ID'];
						if($issue_basis==1)
						{
							if($db_type==0)
							{
								$job_data=sql_select("select group_concat(DISTINCT a.job_no) as JOB_NO, group_concat(DISTINCT a.style_ref_no) as STYLE_REF_NO, group_concat(DISTINCT a.buyer_name) as BUYER_NAME
								from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($order_no) group by a.job_no,a.style_ref_no,a.buyer_name");
								$job_no=$job_data[0]['JOB_NO'];
								$style_ref_no=$job_data[0]['STYLE_REF_NO'];
								$buyer=$job_data[0]['BUYER_NAME'];
								$buyer_name='';
								foreach(explode(',',$buyer) as $buyer_id){
									if($buyer_name)$buyer_name=$buyer_short[$buyer_id].',';	else $buyer_name=$buyer_short[$buyer_id];
								}
							}
							else
							{
								$job_data=sql_select("select LISTAGG(cast(a.job_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as JOB_NO,
								LISTAGG(cast(a.style_ref_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as STYLE_REF_NO,
								LISTAGG(cast(a.buyer_name as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as BUYER_NAME
								from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($order_no) group by a.job_no,a.style_ref_no,a.buyer_name");
								$job_no=implode(',',array_unique(explode(',',$job_data[0]['JOB_NO'])));
								$style_ref_no=implode(',',array_unique(explode(',',$job_data[0]['STYLE_REF_NO'])));
								$buyer=array_unique(explode(',',$job_data[0]['BUYER_NAME']));
								$buyer_name='';
								foreach($buyer as $buyer_id){
									if($buyer_name)$buyer_name=$buyer_short[$buyer_id].',';	else $buyer_name=$buyer_short[$buyer_id];
								}
							}
						}
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td><? echo $i; ?></td>
							<td><p><? echo $trim_group_arr[$row['ITEM_GROUP_ID']]['name']; ?></p></td>
							<td><p><? echo $row['ITEM_DESCRIPTION']; ?></p></td>
							<td><p><? echo $color_arr[$row['ITEM_COLOR_ID']]; ?></p></td>
							<td><p><? echo $job_no; ?></p></td>
							<td><p><? 
								if($sewing_source==3)
								{
									echo "WHS";
								}
								else
								{
									echo $buyer_name;
								}
							?></p></td>
							<td><p><? 
								if($sewing_source==3)
								{
									echo "WHS";
								}
								else
								{
									echo $style_ref_no;
								}
							?></p></td>
							<td><p><? echo $po_number_arr[$row['PO_ID']]; ?></p></td>
							<td align="center"><? echo $unit_of_measurement[$row['UOM']]; ?></td>
							<td align="center"><? echo $row['ITEM_SIZE']; ?></td>
							<td align="right"><? echo number_format($row['ISSUE_QNTY'],2,'.',''); ?></td>
							<td align="center"><? echo $lib_sewing_floor_arr[$row['FLOOR_SEWING']]; ?></td>
							<td align="right"><? echo $sewing_line_arr[$row['SEWING_LINE']]; ?></td>
						</tr>
						<?
						$i++;
					}
				?>
			</table>
		</div>
		<br>
		<div style="width:1010px;clear:both;margin-left:30px;">
		For mishandling or other reason no claim is acceptable in any stage, once the Goods is received in good condition and quantity and out from factory premise.
		</div>
		<div style="width:100%;">
			<br>
			<table align="right" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table" >
				<tbody>
					<tr>
						<td colspan="3" align="center" valign="middle" style="font-size:25px;"><strong>&lt;&lt;Gate Pass&gt;&gt;</strong></td>
						<td colspan="3" align="center" valign="middle" id="gate_pass_barcode_img_id" height="50"></td>
					</tr>
					<tr>
						<td width="150"><strong>From Company:</strong></td>
						<td width="150"><?php echo $gatePassDataArr[$system_no]['from_company']; ?></td>
						<td width="150"><strong>To Company:</strong></td>
						<td width="150"><?php echo $gatePassDataArr[$system_no]['to_company']; ?></td>
						<td width="150"><strong>Carried By:</strong></td>
						<td width="150"><?php echo $gatePassDataArr[$system_no]['carried_by']; ?></td>
					</tr>						
					<tr>
						<td ><strong>From Location:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['from_location']; ?></td>
						<td ><strong>To Location:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['to_location']; ?></td>
						<td ><strong>Driver Name:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['driver_name']; ?></td>
					</tr>
					<tr>
						<td><strong>Gate Pass ID:</strong></td>
						<td><?php echo $gatePassDataArr[$system_no]['gate_pass_id']; ?></td>
						<td rowspan="2"><strong>Delivery Qnty</strong></td>
						<td rowspan="2"></td>
						<!-- <td align="center"><strong>Kg</strong></td>
						<td align="center"><strong>Bag</td> -->
						<td ><strong>Vehicle Number:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['vhicle_number']; ?></td>
					</tr>						
					<tr>
						<td ><strong>Gate Pass Date:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['gate_pass_date']; ?></td>
						<!-- <td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_kg']; ?></td>
						<td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_bag']; ?></td> -->
						<td ><strong>Driver License No.:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['driver_license_no']; ?></td>
					</tr>						
					<tr>
						<td ><strong>Out Date:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['out_date']; ?></td>
						<td ><strong>Dept. Name:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['department']; ?></td>
						<td ><strong>Mobile No.:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['mobile_no']; ?></td>
					</tr>						
					<tr>
						<td ><strong>Out Time:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['out_time']; ?></td>
						<td ><strong>Attention:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['attention']; ?></td>
						<td ><strong>Sequrity Lock No.:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['security_lock_no']; ?></td>
					</tr>						
					<tr>
						<td ><strong>Returnable:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['returnable']; ?></td>
						<td ><strong>Purpose:</strong></td>
						<td colspan="3"><?php echo $gatePassDataArr[$system_no]['issue_purpose']; ?></td>
					</tr>						
					<tr>
						<td ><strong>Est. Return Date:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['est_return_date']; ?></td>
						<td ><strong>Remarks:</strong></td>
						<td colspan="3"><?php echo $gatePassDataArr[$system_no]['remarks']; ?></td>
					</tr>						
				</tbody>	
			</table>
			<br>
			<?
				echo signature_table(36, $data[0], "1000px");
			?>
		</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
			// alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			output:renderer,
			bgColor: '#FFFFFF',
			color: '#000000',
			barWidth: 1,
			barHeight: 30,
			moduleSize:5,
			posX: 10,
			posY: 20,
			addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $data[2]; ?>');

		//for gate pass barcode
		function generateBarcodeGatePass(valuess)
		{
			var zs = '<?php echo $x; ?>';
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer = 'bmp';// $("input[name=renderer]:checked").val();
			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			value = {code: value, rect: false};
			$("#gate_pass_barcode_img_id").show().barcode(value, btype, settings);
		}
		if('<? echo $gatePassDataArr[$system_no]['gate_pass_id']; ?>' != '')
		{
			generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$system_no]['gate_pass_id']); ?>');
		}
	</script>
	<?
	exit();
}

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=181 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);

	foreach($printButton as $id)
	{
		if($id==86)$buttonHtml.='<input id="Print1" type="button" class="formbutton" style="width:80px" onClick="fnc_trims_issue(4)" name="print" value="Print">';
		if($id==116)$buttonHtml.='<input id="Print2" type="button" class="formbutton" style="width:80px" onClick="fnc_trims_issue(5)" name="print" value="Print 2">';
		if($id==136)$buttonHtml.='<input id="Print3" type="button" class="formbutton" style="width:80px" onClick="fnc_trims_issue(6)" name="print" value="Print 3">';
	}

   echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
   exit();
}
?>
