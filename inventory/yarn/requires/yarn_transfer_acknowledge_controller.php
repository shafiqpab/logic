<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/yarn_transfer_acknowledge_controller",$data);
}

if ($action=="load_drop_down_location_to__________")
{ 
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location_to", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down( 'requires/yarn_transfer_acknowledge_controller',this.value+'_'+$data[0], 'load_drop_down_store_to', 'to_store_td' );" );	
	exit();
}

if ($action=="load_drop_down_location_to_up")
{
	$data=explode("_",$data);
		echo create_drop_down( "cbo_location_to", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "
		load_room_rack_self_bin('yarn_transfer_acknowledge_controller*4*cbo_store_name_to', 'store','to_store_td', $data[0],this.value);" );
	exit();
}

if ($action=="varriable_setting_auto_receive")
{
	extract($_REQUEST);
	echo $variable_auto_transfer=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name='$cbo_company_id_to' and variable_list=27 and status_active=1 and is_deleted=0");
	exit();
}	

if ($action=="load_drop_down_store_to")
{
	$data=explode("_",$data);
	$company=$data[0];

	echo create_drop_down( "cbo_store_name_to", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.company_id=$company and a.status_active=1 and a.is_deleted=0 and b.category_type in(1)  group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "fnc_floor_load(this.value+'_'+$company);reset_room_rack_shelf('','cbo_store_name_to');" );
	exit();
}

if ($action=="load_drop_down_floor_to")
{
	$data=explode("_",$data);
	$store=$data[0];
	$company=$data[1];
	$incrementId=$data[2];
	echo create_drop_down( "cbo_floor_to_$incrementId", 100, "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store' and a.company_id='$company' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name", "floor_id,floor_room_rack_name", 1, "--Select--", 0, "load_drop_down( 'requires/yarn_transfer_acknowledge_controller',this.value+'_'+$incrementId+'_'+$company+'_'+$store, 'load_drop_down_room_to', 'room_td_$incrementId' );reset_room_rack_shelf($incrementId,'cbo_floor_to');",0,"","","","","","","cboFloorTo[]" );
	exit();
}
if ($action=="load_drop_down_room_to")
{
	$data=explode("_",$data);
	$floorId=$data[0];
	$incrementId=$data[1];
	$company=$data[2];
	$store=$data[3];
	echo create_drop_down( "cbo_room_to_$incrementId", 100, "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and b.store_id=$store and a.company_id='$company' and b.floor_id=$floorId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select--", 0, "load_drop_down( 'requires/yarn_transfer_acknowledge_controller',this.value+'_'+$incrementId+'_'+$company+'_'+$store+'_'+$floorId, 'load_drop_down_rack_to', 'rack_td_$incrementId' );reset_room_rack_shelf($incrementId,'cbo_room_to');",0,"","","","","","","cboRoomTo[]" );	
	exit();
}
if ($action=="load_drop_down_rack_to")
{
	$data=explode("_",$data);
	$roomId=$data[0];
	$incrementId=$data[1];
	$company=$data[2];
	$store=$data[3];
	$floorId=$data[4];
	echo create_drop_down( "txt_rack_to_$incrementId", 100, "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and b.store_id=$store and a.company_id='$company' and b.floor_id=$floorId and room_id=$roomId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select--", 0, "load_drop_down( 'requires/yarn_transfer_acknowledge_controller',this.value+'_'+$incrementId+'_'+$company+'_'+$store+'_'+$floorId+'_'+$roomId, 'load_drop_down_shelf_to', 'shelf_td_$incrementId' );reset_room_rack_shelf($incrementId,'txt_rack_to');",0,"","","","","","","txtRackTo[]" );	
	exit();
}
if ($action=="load_drop_down_shelf_to")
{
	$data=explode("_",$data);
	$rackId=$data[0];
	$incrementId=$data[1];
	$company=$data[2];
	$store=$data[3];
	$floorId=$data[4];
	$roomId=$data[5];
	echo create_drop_down( "txt_shelf_to_$incrementId", 100, "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and b.store_id=$store and a.company_id='$company' and b.floor_id=$floorId and room_id=$roomId and rack_id=$rackId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select--", 0, "load_drop_down( 'requires/yarn_transfer_acknowledge_controller',this.value+'_'+$incrementId+'_'+$company+'_'+$store+'_'+$floorId+'_'+$roomId+'_'+$rackId, 'load_drop_down_bin_to', 'bin_td_$incrementId' );reset_room_rack_shelf($incrementId,'txt_shelf_to');",0,"","","","","","","txtShelfTo[]" );	
	exit();
}

if ($action=="load_drop_down_bin_to")
{
	$data=explode("_",$data);
	$shelfId=$data[0];
	$incrementId=$data[1];
	$company=$data[2];
	$store=$data[3];
	$floorId=$data[4];
	$roomId=$data[5];
	$rackId=$data[6];
	echo create_drop_down( "cbo_bin_to_$incrementId", 100, "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and b.store_id=$store and a.company_id='$company' and b.floor_id=$floorId and room_id=$roomId and rack_id=$rackId and shelf_id=$shelfId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name","bin_id,floor_room_rack_name", 1, "--Select--", 0, "",0,"","","","","","","txtBinTo[]" );	
	exit();
}

if($action=='itemTransfer_popup')
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		function js_set_value(data)
		{
			$('#transfer_data_str').val(data);
			parent.emailwindow.hide();
		}
    </script>
	<?
	$company=str_replace("'","",$company);

	$transfer_criteria=str_replace("'","",$transfer_criteria);
	$store=str_replace("'","",$store);
	if($transfer_criteria!=0) $transfer_criteria_cond	=" and a.transfer_criteria = '$transfer_criteria'";
	if($store!=0) $store_cond	=" and b.to_store = '$store'";

	if ($transfer_criteria==2) {
		if($company!=0) $company_cond	=" and a.company_id = '$company'";
	}
	else{
		if($company!=0) $company_cond	=" and a.to_company = '$company'";
	}
	if($db_type==0) $year_field="YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY')";
	else $year_field="";//defined Later
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

 	$sql="SELECT a.id, $year_field as year, a.transfer_prefix_number, a.transfer_system_id, a.challan_no, a.company_id, a.transfer_date, a.transfer_criteria, a.item_category, a.to_location_id, b.to_store 
	from inv_item_transfer_mst a ,inv_item_transfer_dtls_ac b
	where b.item_category=1 $company_cond $transfer_criteria_cond $store_cond and a.id=b.mst_id and a.entry_form =10 and a.transfer_criteria in(1,2,4) and b.is_acknowledge=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.id,a.insert_date, a.transfer_prefix_number, a.transfer_system_id, a.challan_no, a.company_id, a.transfer_date, a.transfer_criteria, a.item_category,a.to_location_id, b.to_store order by a.transfer_system_id";
	//echo $sql;//die;
	$arr=array(2=>$company_arr,3=>$store_arr,5=>$item_transfer_criteria,6=>$item_category);
	echo  create_list_view("tbl_list_search", "Challan No,Year,Company,Store,Transfer Date,Transfer Criteria,Item Category", "120,40,120,120,70,120","760","250",0, $sql, "js_set_value", "id,to_store", "", 1, "0,0,company_id,to_store,0,transfer_criteria,item_category", $arr, "transfer_system_id,year,company_id,to_store,transfer_date,transfer_criteria,item_category", '','setFilterGrid("tbl_list_search",-1);','0,0,0,0,3,0,0');
	
	echo "<input type='hidden' id='transfer_data_str' />";
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data = explode("_", $data);
	$transfer_id = $data[0];
	$store_id = $data[1];
	// $transfer_criteria = $data[2];
	$data_array=sql_select("SELECT a.transfer_system_id, a.challan_no, a.company_id, a.location_id, a.transfer_date, a.transfer_criteria, b.item_category, a.to_company, a.to_location_id,b.to_store 
	from inv_item_transfer_mst a,inv_item_transfer_dtls_ac b 
	where a.id=b.mst_id and a.id='$transfer_id' 
	group by a.transfer_system_id, a.challan_no, a.company_id, a.location_id, a.transfer_date, a.transfer_criteria, b.item_category, a.to_company, a.to_location_id,b.to_store");

	// $data_array1=sql_select("select transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category, to_company, remarks, purpose from inv_item_transfer_mst where id='$transfer_id'");

	$company_id=$data_array[0][csf("company_id")];
	// echo "select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1";
	$variable_inventory_sql=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method=$variable_inventory_sql[0][csf("store_method")];

	$to_company=$data_array[0][csf("to_company")];
	if($transfer_criteria=$data_array[0][csf("transfer_criteria")] != 1)
	{
		$to_company=$data_array[0][csf("company_id")];
	}	
	// echo "select store_method, rack_balance from variable_settings_inventory  where company_name=$to_company and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1";die;
	$variable_inventory_sql_to=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$to_company and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method_to=$variable_inventory_sql_to[0][csf("store_method")];

	foreach ($data_array as $row)
	{
		$comCond = ($row[csf("transfer_criteria")]==2) ? $row[csf("company_id")] : $row[csf("to_company")] ;

		if($row[csf("to_store")]>0)
		{
			echo "load_drop_down('requires/yarn_transfer_acknowledge_controller','".$comCond."', 'load_drop_down_store_to', 'to_store_td' );\n";
			echo "document.getElementById('cbo_store_name_to').value 		= '".$row[csf("to_store")]."';\n";
		}
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$comCond."';\n";
		echo "document.getElementById('store_update_upto').value 			= '".$store_method."';\n";
		echo "document.getElementById('store_update_upto_to').value 		= '".$store_method_to."';\n";
		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";
		echo "$('#cbo_store_name_to').attr('disabled','disabled');\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_transfer_acknowledgement',1,1);\n"; 
		exit();
		
	}
}

if($action=="show_dtls_list_view")
{
	$data_array=sql_select("SELECT a.transfer_criteria, a.company_id, a.to_company, a.location_id, a.to_location_id, a.from_order_id, a.to_order_id, a.transfer_date, a.remarks, b.id, b.mst_id, b.dtls_id,  b.from_store, b.to_store, b.floor_id, b.room, b.rack, b.shelf, b.bin_box, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.rate, b.transfer_value, b.color_id, b.uom, b.store_rate, b.store_amount
	from inv_item_transfer_mst a, inv_item_transfer_dtls_ac b 
	where a.id='$data' and a.id=b.mst_id and b.is_acknowledge=0");

	$transfer_criteria=$data_array[0][csf('transfer_criteria')];
	if ($transfer_criteria==2) 
	{
		$company_id=$data_array[0][csf('company_id')];
	}
	else
	{		
		$company_id=$data_array[0][csf('to_company')];
	}
	$store=$data_array[0][csf('to_store')];

	//===============
	$lib_room_rack_shelf_sql = "SELECT b.company_id, b.location_id, b.store_id, b.floor_id, b.room_id, b.rack_id, b.shelf_id, b.bin_id,
	a.floor_room_rack_name floor_name,
	c.floor_room_rack_name room_name,
	d.floor_room_rack_name rack_name,
	e.floor_room_rack_name shelf_name,
	f.floor_room_rack_name bin_name 
	from lib_floor_room_rack_dtls b 
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0 
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0 
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0 
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0 
	left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0 
	where b.status_active=1 and b.is_deleted=0 and b.company_id = $company_id and b.store_id=$store";
	// echo $lib_room_rack_shelf_sql;die;

	$lib_floor_arr_res=sql_select($lib_room_rack_shelf_sql); 
	foreach ($lib_floor_arr_res as $room_rack_shelf_row) 
	{
		$company  = $room_rack_shelf_row[csf("company_id")];
		$floor_id = $room_rack_shelf_row[csf("floor_id")];
		$room_id  = $room_rack_shelf_row[csf("room_id")];
		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

		if($floor_id!="" ){
			$lib_floor_arr[$company][$floor_id] = $room_rack_shelf_row[csf("floor_name")];
		}
		if($floor_id!="" && $room_id!="" ){
			$lib_room_arr[$company][$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" ){
			$lib_rack_arr[$company][$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" ){
			$lib_shelf_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!="" ){
			$lib_bin_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id][$bin_id] = $room_rack_shelf_row[csf("bin_name")];
		}
	}
	//=================
	//print_r($lib_floor_arr);die;

	$i=0;  //$company_id=$data_array[0][csf('to_company')];
	foreach($data_array as $row)
	{
		$i++;
		$prod_id=$row[csf('to_prod_id')];
		$item_desc=return_field_value("product_name_details","product_details_master","id=$prod_id");
		
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";


		$floor=$row[csf('to_floor_id')];
		$room=$row[csf('to_room')];
		$rack=$row[csf('to_rack')];
		$shelf =$row[csf('to_shelf')];
		$bin =$row[csf('to_bin_box')];

		if(empty($floor)){ $fdis_enable=1;}else{$fdis_enable=0;}
		if(empty($room)){$rdis_enable=1;}else{$rdis_enable=0;}
		if(empty($rack)){$radis_enable=1;}else{$radis_enable=0;}
		if(empty($shelf)){$sdis_enable=1;}else{$sdis_enable=0;}
		if(empty($bin)){$bdis_enable=1;}else{$bdis_enable=0;}


		$floor_arr = $lib_floor_arr[$company_id];
		$room_arr = $lib_room_arr[$company_id][$floor];
		$rack_arr = $lib_rack_arr[$company_id][$floor][$room];
		$self_arr = $lib_shelf_arr[$company_id][$floor][$room][$rack];
		$bin_arr = $lib_bin_arr[$company_id][$floor][$room][$rack][$shelf];
			

		if(empty($room_arr)){
			$room_arr = $blank_array;
		}

		if(empty($rack_arr)){
			$rack_arr = $blank_array;
		}
		if(empty($self_arr)){
			$self_arr = $blank_array;
		}
		if(empty($bin_arr)){
			$bin_arr = $blank_array;
		}

		?>
        <tr id="dtlsTbodyTr_<? echo $i; ?>">
            <td><input type="text" id="sl_<? echo $i; ?>" name="sl[]" class="text_boxes"  style="width:20px" value="<? echo $i; ?>"/></td>
            <td>
            	<input type="text" id="txtTransDate_<? echo $i; ?>" name="txtTransDate[]" class="text_boxes"  style="width:56px"  value="<? echo change_date_format($row[csf('transfer_date')]); ?>" readonly/>
            	
            	<input type="hidden" id="txtDtlsID_<? echo $i; ?>" name="txtDtlsID[]" class="text_boxes" value="<? echo $row[csf('dtls_id')]; ?>" />
            	<input type="hidden" id="txtDtlsAcID_<? echo $i; ?>" name="txtDtlsAcID[]" class="text_boxes" value="<? echo $row[csf('id')]; ?>" />
            	<input type="hidden" id="txtTransID_<? echo $i; ?>" name="txtTransID[]" class="text_boxes" value="" />
            </td>
            <td>
            	<input type="text" id="productID_<? echo $i; ?>" name="productID[]" class="text_boxes"  style="width:56px" value="<? echo $row[csf('to_prod_id')]; ?>" readonly />
            </td>
            <td>
            	<input type="text" id="txtItemDesc_<? echo $i; ?>" name="txtItemDesc[]" class="text_boxes" style="width:120px;" value="<? echo $item_desc; ?>" readonly />
				<input type="hidden" id="fromProductID_<? echo $i; ?>" name="fromProductID[]" value="<? echo $row[csf('from_prod_id')]; ?>"  readonly />
            </td>
            <td>
                <input type="text"  id="txtTransQnty_<? echo $i; ?>" name="txtTransQnty[]" class="text_boxes_numeric" style="width:70px;" readonly value="<? echo $row[csf('transfer_qnty')]; ?>"  />
                <input type="hidden" name="hiddenTranQnty[]" id="hiddenTranQnty_<? echo $i; ?>" value="<? echo $row[csf('transfer_qnty')]; ?>" readonly />
                <input type="hidden" name="hiddenTranRate[]" id="hiddenTranRate_<? echo $i; ?>" value="<? echo $row[csf('rate')]; ?>" readonly />
                <input type="hidden" name="hiddenTranValue[]" id="hiddenTranValue_<? echo $i; ?>" value="<? echo $row[csf('transfer_value')]; ?>" readonly />
                <input type="hidden" name="hiddenStoreRate[]" id="hiddenStoreRate_<? echo $i; ?>" value="<? echo $row[csf('store_rate')]; ?>" readonly />
                <input type="hidden" name="hiddenStoreValue[]" id="hiddenStoreValue_<? echo $i; ?>" value="<? echo $row[csf('store_amount')]; ?>" readonly />
            </td>
            <td>
                <? echo create_drop_down( "cbo_uom", 50, $unit_of_measurement,'', 0, "", $row[csf('uom')], "",1,'',"","","","","","cboUom[]" );?>
            </td>
            <td>
                <? echo create_drop_down( "cbo_from_company_id_$i", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 0, "--Select--", $row[csf('company_id')], "",1,'',"","","","","","fromCompanyId[]" );?>
            </td>
            <td>
                <? $company=$row[csf('company_id')];
                echo create_drop_down( "cbo_from_store_id_$i", 100, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.company_id=$company and a.status_active=1 and a.is_deleted=0 and b.category_type in(1)  group by a.id, a.store_name order by a.store_name",'id,store_name', 0, "--Select--", $row[csf('from_store')], "",1,'',"","","","","","fromStoreId[]" );?>
            </td>
            <td id="floor_td_to_<? echo $i;?>">
                <? 
                	echo create_drop_down( "cbo_floor_to_$i", 100, $floor_arr,"", 1, "--Select--",$row[csf('to_floor_id')], "load_drop_down( 'requires/yarn_transfer_acknowledge_controller',this.value+'_'+$i+'_'+$company_id+'_'+$store, 'load_drop_down_room_to', 'room_td_$i' );reset_room_rack_shelf($i,'cbo_floor_to');",$fdis_enable,"","","","","","","cboFloorTo[]" );
                ?>
            </td>
            <td id="room_td_<? echo $i;?>">
            	<? 
            		echo create_drop_down( "cbo_room_to_$i", 100, $room_arr,"", 1, "--Select--",$row[csf('to_room')], "",$rdis_enable,"","","","","","","cboRoomTo[]" ); ?>
            </td>
            <td id="rack_td_<? echo $i;?>">
            	<? echo create_drop_down( "txt_rack_to_$i", 100, $rack_arr,"", 1, "--Select--",$row[csf('to_rack')], "",$radis_enable,"","","","","","","txtRackTo[]" ); ?>
            </td>
            <td id="shelf_td_<? echo $i;?>">
            	<? echo create_drop_down( "txt_shelf_to_$i", 100, $self_arr,"", 1, "--Select--",$row[csf('to_shelf')], "",$sdis_enable,"","","","","","","txtShelfTo[]" ); ?>
            </td>
            <td id="bin_td_<? echo $i;?>">
            	<? echo create_drop_down( "cbo_bin_to_$i", 100, $bin_arr,"", 1, "--Select--",$row[csf('to_bin_box')], "",$bdis_enable,"","","","","","","txtBinTo[]" ); ?>
            </td>
            <td>
            	<input type="text" id="txtRemarks_<? echo $i; ?>" name="txtRemarks[]" class="text_boxes" style="width:80px;" value="<? echo $row[csf('remarks')]; ?>" readonly />
            </td>
        </tr>
    	<?
	}
	exit();	
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	
	
	$all_prod_id_arr=array();
	for($i=1;$i<=$numRow;$i++)
	{
		$productID 	 	="productID_".$i;
		$all_prod_id_arr[str_replace("'","",$$productID)]=str_replace("'","",$$productID);
	}
	
	$data_prod=sql_select("select ID, SUPPLIER_ID, CURRENT_STOCK, AVG_RATE_PER_UNIT, STOCK_VALUE, AVAILABLE_QNTY from product_details_master where id in(".implode(",",$all_prod_id_arr).")");
	$prod_data_arr=array();
	foreach($data_prod as $val)
	{
		$prod_data_arr[$val["ID"]]["SUPPLIER_ID"]=$val["SUPPLIER_ID"];
		$prod_data_arr[$val["ID"]]["CURRENT_STOCK"]=$val["CURRENT_STOCK"];
		$prod_data_arr[$val["ID"]]["AVG_RATE_PER_UNIT"]=$val["AVG_RATE_PER_UNIT"];
		$prod_data_arr[$val["ID"]]["STOCK_VALUE"]=$val["STOCK_VALUE"];
		$prod_data_arr[$val["ID"]]["AVAILABLE_QNTY"]=$val["AVAILABLE_QNTY"];
	}
	//echo "10**select ID, RATE, CONS_QTY AS CURRENT_STOCK, AMOUNT AS STOCK_VALUE, CATEGORY_ID, PROD_ID from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id in(".implode(",",$all_prod_id_arr).") and store_id=$cbo_store_name_to and company_id=$cbo_company_id_to";die;
	$sql_store_rcv = sql_select("select ID, RATE, CONS_QTY AS CURRENT_STOCK, AMOUNT AS STOCK_VALUE, CATEGORY_ID, PROD_ID from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id in(".implode(",",$all_prod_id_arr).") and store_id=$cbo_store_name_to and company_id=$cbo_company_id_to");
	$store_data_all=array();
	foreach($sql_store_rcv as $val)
	{
		$store_data_all[$val["CATEGORY_ID"]][$val["PROD_ID"]]["ID"]=$val["ID"];
		$store_data_all[$val["CATEGORY_ID"]][$val["PROD_ID"]]["RATE"]=$val["RATE"];
		$store_data_all[$val["CATEGORY_ID"]][$val["PROD_ID"]]["CURRENT_STOCK"]=$val["CURRENT_STOCK"];
		$store_data_all[$val["CATEGORY_ID"]][$val["PROD_ID"]]["STOCK_VALUE"]=$val["STOCK_VALUE"];
	}
	
	
	$variable_store_wise_rate=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$cbo_company_id_to and variable_list=47 and item_category_id=1 and status_active=1 and is_deleted=0","auto_transfer_rcv");
	if($variable_store_wise_rate != 1) $variable_store_wise_rate=2; 

	if ($operation==0)  // Insert Here
	{		
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$txt_transfer_date="'".change_date_format($txt_transfer_date,'','',1)."'";
		$id = return_next_id_by_sequence("INV_ITEM_TRANS_MST_AC_PK_SEQ", "inv_item_trans_acknowledgement", $con);
		$field_array="id, entry_form, challan_id, company_id, store_id, transfer_criteria, item_category, acknowledg_date, remarks, inserted_by, insert_date";
		
		$data_array="(".$id.",419,".$challan_id.",".$cbo_company_id_to.",".$cbo_store_name_to.",".$cbo_transfer_criteria.",1,".$txt_transfer_date.",'".$txt_remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		$field_array_trans="id, mst_id, transaction_criteria, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, floor_id, room, rack, self, bin_box, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, inserted_by, insert_date, store_rate, store_amount"; //$field_array_trans="origin_prod_id, brand_id, btb_lc_id";

		$field_array_dtls_update="to_store*to_floor_id*to_room*to_rack*to_shelf*to_bin_box*to_trans_id*updated_by*update_date";
		$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*available_qnty*updated_by*update_date";
		$field_array_store="rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date";
		$field_array_store_rcv="id,company_id,store_id,category_id,prod_id,cons_qty,rate,amount,last_purchased_qnty,inserted_by,insert_date,lot,first_receive_date,last_receive_date";
		
		$sdtlsid = return_next_id( "id", "inv_store_wise_yarn_qty_dtls", 1 );
		
		$data_array_store_rcv="";$prod_id_arr=$store_id_arr=array();
		$dtls_ac_ids='';
		for($i=1;$i<=$numRow;$i++)
		{
			$txtTransDate 	="txtTransDate_".$i;
			$fromCompanyId 	="fromCompanyId_".$i;
			$fromStoreId 	="fromStoreId_".$i;
			$txtDtlsID 		="txtDtlsID_".$i;
			$txtDtlsAcID 	="txtDtlsAcID_".$i;
			$txtItemDesc 	="txtItemDesc_".$i;
			$productID 	 	="productID_".$i; // to_product_id
			$fromProductID 	="fromProductID_".$i;
			$cboFloorTo 	="cboFloorTo_".$i;
			$cboRoomTo 	 	="cboRoomTo_".$i;
			$txtRackTo 		="txtRackTo_".$i;
			$txtShelfTo 	="txtShelfTo_".$i;
			$txtBinTo 		="txtBinTo_".$i;
			$hiddenTranQnty ="hiddenTranQnty_".$i;
			$hiddenTranRate ="hiddenTranRate_".$i;
			$hiddenTranValue="hiddenTranValue_".$i;
			$cbo_uom 		="cboUom_".$i;
			$txtRemarks 	="txtRemarks_".$i;
			
			$hiddenStoreRate ="hiddenStoreRate_".$i;
			$hiddenStoreValue="hiddenStoreValue_".$i;
			
			$dtls_ac_ids.= str_replace("'","",$$txtDtlsAcID).",";

			$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			if($data_array_trans!="") $data_array_trans.=",";
			$data_array_trans .="(".$recv_trans_id.",".$challan_id.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",'".str_replace("'","",$$productID)."',1,5,".$txt_transfer_date.",".$cbo_store_name_to.",'".str_replace("'","",$$cboFloorTo)."','".str_replace("'","",$$cboRoomTo)."','".str_replace("'","",$$txtRackTo)."','".str_replace("'","",$$txtShelfTo)."','".str_replace("'","",$$txtBinTo)."','".str_replace("'","",$$cbo_uom)."','".str_replace("'","",$$hiddenTranQnty)."','".number_format(str_replace("'","",$$hiddenTranRate),10,'.','')."','".number_format(str_replace("'","",$$hiddenTranValue),8,'.','')."','".str_replace("'","",$$hiddenTranQnty)."','".str_replace("'","",$$hiddenTranValue)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".number_format(str_replace("'","",$$hiddenStoreRate),10,'.','')."','".number_format(str_replace("'","",$$hiddenStoreValue),8,'.','')."')";

			if($$txtDtlsID !='')
			{
				$id_arr[]=str_replace("'","",$$txtDtlsID);
				$data_array_dtls_update[str_replace("'","",$$txtDtlsID)] = explode("*",("'".$cbo_store_name_to."'*".str_replace("'","",$$cboFloorTo)."*".str_replace("'","",$$cboRoomTo)."*".str_replace("'","",$$txtRackTo)."*".str_replace("'","",$$txtShelfTo)."*".str_replace("'","",$$txtBinTo)."*".$recv_trans_id."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			}//."'*'".$recv_trans_id
			
			if( str_replace("'","",$cbo_company_id_to) == str_replace("'","",$$fromCompanyId) && str_replace("'","",$cbo_transfer_criteria) == 2 )
			{
				//same company and store to store transfer
				//no need to update product_details_master table
				
				$store_up_id_to=0;
				//echo "10**".$variable_store_wise_rate."=".$store_data_all[1][str_replace("'","",$$productID)]["ID"];oci_rollback($con);disconnect($con);die;
				if($variable_store_wise_rate==1)
				{
					if($store_data_all[1][str_replace("'","",$$productID)]["ID"]>0)
					{
						$store_up_id_to=$store_data_all[1][str_replace("'","",$$productID)]["ID"];
						$store_presentStock	=$store_data_all[1][str_replace("'","",$$productID)]["CURRENT_STOCK"];
						$store_presentStockValue =$store_data_all[1][str_replace("'","",$$productID)]["STOCK_VALUE"];
						
						$currentStock_store		=$store_presentStock+str_replace("'", '',$$hiddenTranQnty);
						$currentValue_store		=$store_presentStockValue+str_replace("'","",$$hiddenStoreValue);
						$store_presentAvgRate	=0;
						if($currentStock_store != 0 && $currentValue_store != 0) $store_presentAvgRate=$currentValue_store/$currentStock_store;	
						
						$store_id_arr[]=$store_up_id_to;
						$data_array_store[$store_up_id_to]= explode("*",("'".number_format($store_presentAvgRate,10,'.','')."'*".str_replace("'", '',$$hiddenTranQnty)."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
					else
					{
						if($data_array_store_rcv!="") $data_array_store_rcv.=",";
						$data_array_store_rcv.= "(".$sdtlsid.",".$cbo_company_id_to.",".$cbo_store_name_to.",1,".$$productID.",".$$hiddenTranQnty.",'".number_format(str_replace("'","",$$hiddenStoreRate),10,'.','')."','".number_format(str_replace("'","",$$hiddenStoreValue),8,'.','')."',".$$hiddenTranQnty.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$txt_lot."',".$txt_transfer_date.",".$txt_transfer_date.")";
						$sdtlsid++;
					}
				}
			}
			else
			{
				//$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value, available_qnty from product_details_master where id=".str_replace("'","",$$productID));
				$prod_data_arr[$val["ID"]]["SUPPLIER_ID"]=$val["SUPPLIER_ID"];
				$prod_data_arr[$val["ID"]]["CURRENT_STOCK"]=$val["CURRENT_STOCK"];
				$prod_data_arr[$val["ID"]]["AVG_RATE_PER_UNIT"]=$val["AVG_RATE_PER_UNIT"];
				$prod_data_arr[$val["ID"]]["STOCK_VALUE"]=$val["STOCK_VALUE"];
				
				$stock_qnty=$prod_data_arr[str_replace("'","",$$productID)]["CURRENT_STOCK"];
				$avg_rate_per_unit=$prod_data_arr[str_replace("'","",$$productID)]["AVG_RATE_PER_UNIT"];

				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$$hiddenTranQnty);
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				$curr_availlable_qty=$prod_data_arr[str_replace("'","",$$productID)]["AVAILABLE_QNTY"]+str_replace("'", '',$$hiddenTranQnty);

				
				// last_purchased_qnty, current_stock, stock_value
				$prod_id_arr[]=str_replace("'","",$$productID);
				$data_array_prod_update[str_replace("'","",$$productID)] = explode("*",("'".number_format($avg_rate_per_unit,10,'.','')."'*".$$hiddenTranQnty."*".$curr_stock_qnty."*".number_format($stock_value,8,'.','')."*".$curr_availlable_qty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$store_up_id_to=0;
				//echo "10**".$variable_store_wise_rate."=".$store_data_all[1][str_replace("'","",$$productID)]["ID"];oci_rollback($con);disconnect($con);die;
				if($variable_store_wise_rate==1)
				{
					if($store_data_all[1][str_replace("'","",$$productID)]["ID"]>0)
					{
						$store_up_id_to=$store_data_all[1][str_replace("'","",$$productID)]["ID"];
						$store_presentStock	=$store_data_all[1][str_replace("'","",$$productID)]["CURRENT_STOCK"];
						$store_presentStockValue =$store_data_all[1][str_replace("'","",$$productID)]["STOCK_VALUE"];
						
						$currentStock_store		=$store_presentStock+str_replace("'", '',$$hiddenTranQnty);
						$currentValue_store		=$store_presentStockValue+str_replace("'","",$$hiddenStoreValue);
						$store_presentAvgRate	=0;
						if($currentStock_store != 0 && $currentValue_store != 0) $store_presentAvgRate=$currentValue_store/$currentStock_store;	
						
						$store_id_arr[]=$store_up_id_to;
						$data_array_store[$store_up_id_to]= explode("*",("'".number_format($store_presentAvgRate,10,'.','')."'*".str_replace("'", '',$$hiddenTranQnty)."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
					else
					{
						if($data_array_store_rcv!="") $data_array_store_rcv.=",";
						$data_array_store_rcv.= "(".$sdtlsid.",".$cbo_company_id_to.",".$cbo_store_name_to.",1,".$$productID.",".$$hiddenTranQnty.",'".number_format(str_replace("'","",$$hiddenStoreRate),10,'.','')."','".number_format(str_replace("'","",$$hiddenStoreValue),8,'.','')."',".$$hiddenTranQnty.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$txt_lot."',".$txt_transfer_date.",".$txt_transfer_date.")";
						$sdtlsid++;
					}
				}
				
				
			}
		}
		
		$field_array_mst_update = "is_acknowledge";
		$data_array_mst_update  = "1";
		$field_array_status="updated_by*update_date*is_acknowledge";
		$data_array_status=$user_id."*'".$pc_date_time."'*1";
		
		$rID=$rID2=$rID3=$rID4=$rID5=$rID6=$storeInsID=$rID7=true;
		//echo "10**insert into inv_item_trans_acknowledgement (".$field_array.") values ".$data_array;die;
		$rID=sql_insert("inv_item_trans_acknowledgement",$field_array,$data_array,0);
		$rID4=sql_update("inv_item_transfer_mst",$field_array_mst_update,$data_array_mst_update,"id",$challan_id,0);
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if(count($data_array_dtls_update)>0) // transfer_dtls table update
		{
			$rID3=execute_query(bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ));
		} 
		//$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$$productID,0);
		
		if(count($prod_id_arr)>0) // product table update
		{
			$rID5=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_arr ));
		}
		
		if(count($store_id_arr)>0 && $variable_store_wise_rate==1) // store table update
		{
			$rID6=execute_query(bulk_update_sql_statement( "inv_store_wise_yarn_qty_dtls", "id", $field_array_store, $data_array_store, $store_id_arr ));
		}
		
		if($data_array_store_rcv!="" && $variable_store_wise_rate==1)
		{
			//echo "10**insert into inv_store_wise_yarn_qty_dtls (".$field_array_store_rcv.") values ".$data_array_store_rcv;oci_rollback($con);disconnect($con);die;
			$storeInsID=sql_insert("inv_store_wise_yarn_qty_dtls",$field_array_store_rcv,$data_array_store_rcv,1); 
		}

		$dtls_ac_ids=chop($dtls_ac_ids,",");
		if($dtls_ac_ids!="")					// transfer_dtls_ac is_acknowledge update
		{
			$rID7=sql_multirow_update("inv_item_transfer_dtls_ac",$field_array_status,$data_array_status,"id",$dtls_ac_ids,0);
		}
		
		//echo "10** $rID=$rID2=$rID3=$rID4=$rID5=$rID6=$storeInsID=$rID7";oci_rollback($con);disconnect($con);die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $storeInsID && $rID7)
			{
				mysql_query("COMMIT");  
				echo "0**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $storeInsID && $rID7)
			{
				oci_commit($con);
				echo "0**".$id;
			}
			else
			{
				oci_rollback($con); 
				echo "5**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if($db_type==0)
		{
			$txt_transfer_date= "'".change_date_format($txt_transfer_date,'yyyy-mm-dd')."'";
		}
		else if($db_type==2)
		{
			$txt_transfer_date="'".change_date_format($txt_transfer_date,'','',1)."'";
		}

		//echo "10**".$txt_transfer_date; die;
		$field_array_trans_update="transaction_date*floor_id*room*rack*self*bin_box*updated_by*update_date";
		$field_array_dtls_update="to_floor_id*to_room*to_rack*to_shelf*to_bin_box*updated_by*update_date";
		$flag=1;
		for($i=1;$i<=$numRow;$i++)
		{
			$txtDtlsID 		="txtDtlsID_".$i;
			$txtTransID 	="txtTransID_".$i;
			$cboFloorTo 	="cboFloorTo_".$i;
			$cboRoomTo 	 	="cboRoomTo_".$i;
			$txtRackTo 		="txtRackTo_".$i;
			$txtShelfTo 	="txtShelfTo_".$i;
			$txtBinTo 		="txtBinTo_".$i;
			
			if($$txtTransID !='')
			{
				$trans_id_arr[]=str_replace("'","",$$txtTransID);

				$data_array_trans_update[str_replace("'","",$$txtTransID)] = explode("*",("$txt_transfer_date*".str_replace("'","",$$cboFloorTo)."*".str_replace("'","",$$cboRoomTo)."*".str_replace("'","",$$txtRackTo)."*".str_replace("'","",$$txtShelfTo)."*".str_replace("'","",$$txtBinTo)."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			}

			if($$txtDtlsID !='')
			{
				$id_arr[]=str_replace("'","",$$txtDtlsID);
				$data_array_dtls_update[str_replace("'","",$$txtDtlsID)] = explode("*",("".str_replace("'","",$$cboFloorTo)."*".str_replace("'","",$$cboRoomTo)."*".str_replace("'","",$$txtRackTo)."*".str_replace("'","",$$txtShelfTo)."*".str_replace("'","",$$txtBinTo)."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			}
		}

		$field_array_update="acknowledg_date*remarks*updated_by*update_date";
		$data_array_update="$txt_transfer_date*'".$txt_remarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("inv_item_trans_acknowledgement",$field_array_update,$data_array_update,"id",$update_id,1);

		if($rID==1)
		{
			if($rID) $flag=1; else $flag=0; 
		} 

		if(count($data_array_trans_update)>0)
		{
			$rID2=execute_query(bulk_update_sql_statement( "inv_transaction", "id", $field_array_trans_update, $data_array_trans_update, $trans_id_arr ));
			if($rID2) $flag=1; else $flag=0; 
		}

		if(count($data_array_dtls_update)>0)
		{
			$rID3=execute_query(bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ));
			if($rID3) $flag=1; else $flag=0; 
		}

		//echo "5**".$rID."**".$rID2."**".$rID3."**".$flag;die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con); 
				echo "5**0";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="show_dtls_list_view_update")
{
	$data_array=sql_select("SELECT a.id, a.challan_id, a.acknowledg_date, d.remarks, b.id as dtls_id, b.mst_id, b.from_store, b.to_store, b.floor_id, b.room, b.rack, b.shelf, b.bin_box, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.rate, b.transfer_value, b.color_id, b.uom, d.from_order_id, d.to_order_id, d.transfer_date, B.to_trans_id as trans_id, b.batch_id ,a.company_id as company_id, b.store_rate, b.store_amount
	from inv_item_trans_acknowledgement a, inv_item_transfer_dtls b, inv_item_transfer_mst d
	where a.id='$data' and a.challan_id=b.mst_id and d.id=b.mst_id and a.entry_form=419
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

	$store=$data_array[0][csf('to_store')];
	$company_id=$data_array[0][csf('company_id')];

	$lib_room_rack_shelf_sql = "SELECT b.company_id, b.location_id, b.store_id, b.floor_id, b.room_id, b.rack_id, b.shelf_id, b.bin_id,
	a.floor_room_rack_name floor_name,
	c.floor_room_rack_name room_name,
	d.floor_room_rack_name rack_name,
	e.floor_room_rack_name shelf_name,
	f.floor_room_rack_name bin_name 
	from lib_floor_room_rack_dtls b 
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0 
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0 
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0 
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0 
	left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0
	where b.status_active=1 and b.is_deleted=0 and b.company_id = $company_id and b.store_id=$store";
	$lib_floor_arr_res=sql_select($lib_room_rack_shelf_sql); 
	foreach ($lib_floor_arr_res as $room_rack_shelf_row) 
	{
		$company  = $room_rack_shelf_row[csf("company_id")];
		$floor_id = $room_rack_shelf_row[csf("floor_id")];
		$room_id  = $room_rack_shelf_row[csf("room_id")];
		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

		if($floor_id!="" ){
			$lib_floor_arr[$company][$floor_id] = $room_rack_shelf_row[csf("floor_name")];
		}
		if($floor_id!="" && $room_id!="" ){
			$lib_room_arr[$company][$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" ){
			$lib_rack_arr[$company][$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" ){
			$lib_shelf_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!="" ){
			$lib_bin_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id][$bin_id] = $room_rack_shelf_row[csf("bin_name")];
		}
	}

	$i=0;  $company_id=$data_array[0][csf('company_id')];
	foreach($data_array as $row)
	{
		
		$i++;
		$prod_id=$row[csf('to_prod_id')];
		$item_desc=return_field_value("product_name_details","product_details_master","id=$prod_id");
		
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";


		$floor=$row[csf('to_floor_id')];
		$room=$row[csf('to_room')];
		$rack=$row[csf('to_rack')];
		$shelf =$row[csf('to_shelf')];
		$bin =$row[csf('to_bin_box')];

		
		if(empty($floor)){ $fdis_enable=1;}else{$fdis_enable=0;}
		if(empty($room)){$rdis_enable=1;}else{$rdis_enable=0;}
		if(empty($rack)){$radis_enable=1;}else{$radis_enable=0;}
		if(empty($shelf)){$sdis_enable=1;}else{$sdis_enable=0;}
		if(empty($bin)){$bdis_enable=1;}else{$bdis_enable=0;}

		$floor_arr = $lib_floor_arr[$company];
		$room_arr = $lib_room_arr[$company][$floor];
		$rack_arr = $lib_rack_arr[$company][$floor][$room];
		$self_arr = $lib_shelf_arr[$company][$floor][$room][$rack];
		$bin_arr = $lib_bin_arr[$company][$floor][$room][$rack][$shelf];

		if(empty($room_arr)){
			$room_arr = $blank_array;
		}

		if(empty($rack_arr)){
			$rack_arr = $blank_array;
		}
		if(empty($self_arr)){
			$self_arr = $blank_array;
		}
		if(empty($bin_arr)){
			$bin_arr = $blank_array;
			
		}

		?>
        <tr id="dtlsTbodyTr_<? echo $i; ?>">
            <td><input type="text" id="sl_<? echo $i; ?>" name="sl[]" class="text_boxes"  style="width:20px" value="<? echo $i; ?>"/></td>
            <td>
            	<input type="text" id="txtTransDate_<? echo $i; ?>" name="txtTransDate[]" class="text_boxes"  style="width:80px" value="<? echo change_date_format($row[csf('transfer_date')]); ?>" readonly />
            	<input type="hidden" id="txtDtlsID_<? echo $i; ?>" name="txtDtlsID[]" class="text_boxes" value="<? echo $row[csf('dtls_id')]; ?>" />
            	<input type="hidden" id="txtDtlsAcID_<? echo $i; ?>" name="txtDtlsAcID[]" class="text_boxes" value="" />
            	<input type="hidden" id="txtTransID_<? echo $i; ?>" name="txtTransID[]" class="text_boxes" value="<? echo $row[csf('trans_id')]; ?>" />
            </td>
            <td>
            	<input type="text" id="productID_<? echo $i; ?>" name="productID[]" class="text_boxes"  style="width:56px" value="<? echo $row[csf('to_prod_id')]; ?>" readonly />
            </td>
            <td>
            	<input type="text" id="txtItemDesc_<? echo $i; ?>" name="txtItemDesc[]" class="text_boxes" style="width:120px;" value="<? echo $item_desc; ?>" readonly />
				<input type="hidden" id="fromProductID_<? echo $i; ?>" name="fromProductID[]" value="<? echo $row[csf('from_prod_id')]; ?>"  readonly />
            </td>
            <td>
                <input type="text"  id="txtTransQnty_<? echo $i; ?>" name="txtTransQnty[]" class="text_boxes_numeric" style="width:70px;" readonly value="<? echo $row[csf('transfer_qnty')]; ?>"  />
                <input type="hidden" name="hiddenTranQnty[]" id="hiddenTranQnty_<? echo $i; ?>" value="<? echo $row[csf('transfer_qnty')]; ?>" readonly />
                <input type="hidden" name="hiddenTranRate[]" id="hiddenTranRate_<? echo $i; ?>" value="<? echo $row[csf('rate')]; ?>" readonly />
                <input type="hidden" name="hiddenTranValue[]" id="hiddenTranValue_<? echo $i; ?>" value="<? echo $row[csf('transfer_value')]; ?>" readonly />
                <input type="hidden" name="hiddenStoreRate[]" id="hiddenStoreRate_<? echo $i; ?>" value="<? echo $row[csf('store_rate')]; ?>" readonly />
                <input type="hidden" name="hiddenStoreValue[]" id="hiddenStoreValue_<? echo $i; ?>" value="<? echo $row[csf('store_amount')]; ?>" readonly />
            </td>
            <td>
                <? echo create_drop_down( "cbo_uom", 50, $unit_of_measurement,'', 0, "", $row[csf('uom')], "",1,"","","","","","","cboUom[]" );?>
            </td>
            <td>
                <? echo create_drop_down( "cbo_from_company_id_$i", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 0, "--Select--", $row[csf('company_id')], "",1,'',"","","","","","fromCompanyId_[]" );?>
            </td>
            <td>
                <? $company=$row[csf('company_id')];
                echo create_drop_down( "cbo_from_store_id_$i", 100, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.company_id=$company and a.status_active=1 and a.is_deleted=0 and b.category_type in(1)  group by a.id, a.store_name order by a.store_name",'id,store_name', 0, "--Select--", $row[csf('from_store')], "",1,'',"","","","","","fromStoreId[]" );?>
            </td>
            <td id="floor_td_to_<? echo $i;?>">
                <? 
                	echo create_drop_down( "cbo_floor_to_$i", 100,$floor_arr,"", 1, "--Select--",$row[csf('to_floor_id')], "load_drop_down( 'requires/yarn_transfer_acknowledge_controller',this.value+'_'+$i+'_'+$company+'_'+$store, 'load_drop_down_room_to', 'room_td_$i' );reset_room_rack_shelf($i,'cbo_floor_to');",$fdis_enable,"","","","","","","cboFloorTo[]" );
                ?>
            </td>
            <td id="room_td_<? echo $i;?>">
            	<? 
            		echo create_drop_down( "cbo_room_to_$i", 100,$room_arr,"", 1, "--Select--",$row[csf('to_room')], "",$rdis_enable,"","","","","","","cboRoomTo[]" ); ?>
            </td>
            <td id="rack_td_<? echo $i;?>">
            	<? echo create_drop_down( "txt_rack_to_$i", 100,$rack_arr,"", 1, "--Select--",$row[csf('to_rack')], "",$radis_enable,"","","","","","","txtRackTo[]" ); ?>
            </td>
            <td id="shelf_td_<? echo $i;?>">
            	<? echo create_drop_down( "txt_shelf_to_$i", 100,$self_arr,"", 1, "--Select--",$row[csf('to_shelf')], "",$sdis_enable,"","","","","","","txtShelfTo[]" ); ?>
            </td>
            <td id="bin_td_<? echo $i;?>">
            	<? echo create_drop_down( "cbo_bin_to_$i", 100,$bin_arr,"", 1, "--Select--",$row[csf('to_bin_box')], "",$bdis_enable,"","","","","","","txtBinTo[]" ); ?>
            </td>
            <td>
            	<input type="text" id="txtRemarks_<? echo $i; ?>" name="txtRemarks[]" class="text_boxes" style="width:80px;" value="<? echo $row[csf('remarks')]; ?>" readonly />
            </td>
        </tr>
    	<?
		
	}
	
	exit();	
}

if ($action=="itemAcknowle_popup")
{
	echo load_html_head_contents("Item Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
		<script>
			function js_set_value(data)
			{
				$('#transfer_id').val(data);
				parent.emailwindow.hide();
			}

			function load_store()
			{
				var cbo_company_id_to='<? echo $cbo_company_id_to; ?>';
				load_drop_down('yarn_transfer_acknowledge_controller',cbo_company_id_to, 'load_drop_down_store_to', 'to_store_td' );
			}
    	</script>
	</head>
	<body onLoad="load_store();">
	<div align="center" style="width:790px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:770px;margin-left:10px">
	        <legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="740" class="rpt_table">
	                <thead>
	                    <th>Transfer Criteria</th>
	                    <th>Store</th>
	                    <th>System ID</th>
	                    <th>Date Range</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td>
							<?
                                echo create_drop_down("cbo_transfer_criteria", 120,$item_transfer_criteria,"", 1,"-- Select --",'0',"",'','1,2');
	                        ?>
	                    </td>
	                    <td id="to_store_td">
                            <?
                               echo create_drop_down( "cbo_store_name_to", 160, $blank_array,"", 1, "--Select Store--", 0, "",1 );
                            ?>	
	                    </td>
                        <td>
	                        <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes_numeric" style="width:70px;" />
                        </td>
	                    <td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
						</td>
						<td>
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_transfer_criteria').value+'_'+document.getElementById('cbo_store_name_to').value+'_'+document.getElementById('txt_system_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id_to; ?>, 'create_transfer_search_list_view', 'search_div', 'yarn_transfer_acknowledge_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	                <tr>
						<td colspan="6" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
	            </table>
	        	<div style="margin-top:10px" id="search_div"></div> 
			</fieldset>
		</form>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=='create_transfer_search_list_view')
{
	$data = explode("_",$data);
	$transfer_criteria=$data[0]; 
	$store=$data[1];
	$system_id=$data[2];
	$txt_date_from=$data[3];
	$txt_date_to=$data[4];
	$company=$data[5];

	if($company==0)$company_cond=""; else $company_cond=" and a.company_id=$company";
	if($transfer_criteria==0)$criteria_cond=""; else $criteria_cond=" and a.transfer_criteria=$transfer_criteria";
	if($store==0)$store_cond=""; else $store_cond=" and a.store_id=$store";
	if($system_id=="")$system_id_cond=""; else $system_id_cond=" and a.id=$system_id";
	if ($txt_date_from!="" &&  $txt_date_to!="")
	{
		if($db_type==0)
		{
			$acknowledg_date_cond = "and a.acknowledg_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'"; 
		}
		else
		{
			$acknowledg_date_cond = "and a.acknowledg_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'"; 
		}
	}
	else
	{
		$acknowledg_date_cond ="";
	}
	if($db_type==0) $year_field="YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY')";
	else $year_field="";
 	
 	$sql="select a.id, $year_field as year, a.entry_form, a.challan_id, a.company_id, a.store_id, a.location_id, a.transfer_criteria, a.item_category, a.acknowledg_date, a.remarks, b.transfer_system_id from inv_item_trans_acknowledgement a, inv_item_transfer_mst b where a.challan_id=b.id and a.entry_form=419 and a.transfer_criteria in(1,2,4) $company_cond $criteria_cond $store_cond $system_id_cond $acknowledg_date_cond and a.status_active=1 and a.is_deleted=0";
	
	$store_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$location_arr=return_library_array( "select id, location_name from  lib_location", "id", "location_name"  );
	//print_r($location_arr);
	$arr=array(2=>$store_arr,5=>$item_transfer_criteria);
	//print_r($item_transfer_criteria);

	echo  create_list_view("tbl_list_search", "System ID, Year, Store, Challan No, Acknowledg Date, Transfer Criteria", "70,60,150,150,80","730","250",0, $sql, "js_set_value", "id", "", 1, "0,0,store_id,0,0,transfer_criteria", $arr, "id,year,store_id,transfer_system_id,acknowledg_date,transfer_criteria", '','','0,0,0,0,3,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master_update')
{
	$data_array=sql_select("SELECT a.id, a.entry_form, a.challan_id, a.company_id, a.store_id, a.location_id, a.transfer_criteria, a.item_category, a.acknowledg_date, a.remarks, b.transfer_system_id from inv_item_trans_acknowledgement a, inv_item_transfer_mst b where a.id='$data' and a.challan_id=b.id and a.entry_form=419 and a.transfer_criteria in(1,2,4) and a.status_active=1 and a.is_deleted=0");

	// $data_array1=sql_select("select transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category, to_company, remarks, purpose from inv_item_transfer_mst where id='$data'");

	$company_id=$data_array[0][csf("company_id")];
	// echo "select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1";
	$variable_inventory_sql=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method=$variable_inventory_sql[0][csf("store_method")];

	$to_company=$data_array[0][csf("to_company")];
	if($transfer_criteria=$data_array[0][csf("transfer_criteria")] != 1)
	{
		$to_company=$data_array[0][csf("company_id")];
	}	
	// echo "select store_method, rack_balance from variable_settings_inventory  where company_name=$to_company and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1";die;
	$variable_inventory_sql_to=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$to_company and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method_to=$variable_inventory_sql_to[0][csf("store_method")];
	
	foreach ($data_array as $row)
	{
		if($row[csf("store_id")]>0){
			echo "load_room_rack_self_bin('requires/yarn_transfer_acknowledge_controller*1*cbo_store_name_to', 'store','to_store_td', '".$row[csf("company_id")]."','"."0"."',this.value);\n";
			echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("store_id")]."';\n";
		}
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('challan_id').value 					= '".$row[csf("challan_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("acknowledg_date")])."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		// echo "document.getElementById('store_update_upto').value 			= '".$store_method."';\n";
		// echo "document.getElementById('store_update_upto_to').value 		= '".$store_method_to."';\n";
		echo "document.getElementById('store_update_upto').value 		    = '".$store_method_to."';\n";
		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";
		echo "$('#cbo_store_name_to').attr('disabled','disabled');\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		echo "$('#txt_challan_no').attr('disabled','disabled');\n";
		
		
		//echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_transfer_acknowledgement',1,1);\n"; 
		 
		exit();
		
	}
}

?>