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
	load_room_rack_self_bin("requires/general_item_transfer_acknowledgement_controller",$data);
}

if ($action=="load_drop_down_location_to")
{ 
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location_to", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down( 'requires/general_item_transfer_acknowledgement_controller',$data[0]+'_'+this.value, 'load_drop_down_store_to', 'to_store_td' );" );	
	exit();
}


if ($action=="load_drop_down_location_to_up")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location_to", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down( 'general_item_transfer_acknowledgement_controller',$data[0]+'_'+this.value, 'load_drop_down_store_to', 'to_store_td' );" );
	exit();
}


if ($action=="load_drop_down_store_to")
{
	//echo $data;die;
	$data=explode("_",$data);
	//echo $data;die;
	$company=$data[0];
	$location=$data[1];

	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
	$store_location_id = $userCredential[0][csf('store_location_id')];
	if ($store_location_id != '') {$store_location_credential_cond = "and a.id in($store_location_id)";} else { $store_location_credential_cond = "";}
	echo create_drop_down( "cbo_store_name_to", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.company_id=$company and a.location_id=$location and a.status_active=1 and a.is_deleted=0 and b.category_type in(".implode(",",array_flip($general_item_category)).") $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "fnc_floor_load(this.value+'_'+$company+'_'+$location);reset_room_rack_shelf('','cbo_store_name_to');" );
	exit();
}

if ($action=="load_drop_down_floor_to")
{
	$data=explode("_",$data);
	$store=$data[0];
	$company=$data[1];
	$location=$data[2];
	$incrementId=$data[3];
	echo create_drop_down( "cbo_floor_to_$incrementId", 152, "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store' and a.company_id='$company' and b.location_id=$location and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name", "floor_id,floor_room_rack_name", 1, "-- Select Floor --", 0, "load_drop_down( 'requires/general_item_transfer_acknowledgement_controller',this.value+'_'+$incrementId+'_'+$company+'_'+$location+'_'+$store, 'load_drop_down_room_to', 'room_td_$incrementId' );reset_room_rack_shelf($incrementId,'cbo_floor_to');",0,"","","","","","","cboFloorTo[]" );
	exit();
}
if ($action=="load_drop_down_room_to")
{
	$data=explode("_",$data);
	$floorId=$data[0];
	$incrementId=$data[1];
	$company=$data[2];
	$location=$data[3];
	$store=$data[4];
	
	$location_cond="";
	if(str_replace("'","",$location)>0) $location_cond=" and b.location_id=$location";
	echo create_drop_down( "cbo_room_to_$incrementId", 152, "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and b.store_id=$store and a.company_id='$company' $location_cond and b.floor_id=$floorId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "-- Select Room --", 0, "load_drop_down( 'requires/general_item_transfer_acknowledgement_controller',this.value+'_'+$incrementId+'_'+$company+'_'+$location+'_'+$store+'_'+$floorId, 'load_drop_down_rack_to', 'rack_td_$incrementId' );reset_room_rack_shelf($incrementId,'cbo_room_to');",0,"","","","","","","cboRoomTo[]" );	
	exit();
}
if ($action=="load_drop_down_rack_to")
{
	$data=explode("_",$data);
	$roomId=$data[0];
	$incrementId=$data[1];
	$company=$data[2];
	$location=$data[3];
	$store=$data[4];
	$floorId=$data[5];
	$location_cond="";
	if(str_replace("'","",$location)>0) $location_cond=" and b.location_id=$location";
	
	echo create_drop_down( "txt_rack_to_$incrementId", 152, "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and b.store_id=$store and a.company_id='$company'  $location_cond and b.floor_id=$floorId and room_id=$roomId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "-- Select Rack --", 0, "load_drop_down( 'requires/general_item_transfer_acknowledgement_controller',this.value+'_'+$incrementId+'_'+$company+'_'+$location+'_'+$store+'_'+$floorId+'_'+$roomId, 'load_drop_down_shelf_to', 'shelf_td_$incrementId' );reset_room_rack_shelf($incrementId,'txt_rack_to');",0,"","","","","","","txtRackTo[]" );	
	exit();
}
if ($action=="load_drop_down_shelf_to")
{
	$data=explode("_",$data);
	$rackId=$data[0];
	$incrementId=$data[1];
	$company=$data[2];
	$location=$data[3];
	$store=$data[4];
	$floorId=$data[5];
	$roomId=$data[6];
	
	$location_cond="";
	if(str_replace("'","",$location)>0) $location_cond=" and b.location_id=$location";
	
	echo create_drop_down( "txt_shelf_to_$incrementId", 152, "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and b.store_id=$store and a.company_id='$company'  $location_cond and b.floor_id=$floorId and room_id=$roomId and rack_id=$rackId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "-- Select Shelf --", 0, "load_drop_down( 'requires/general_item_transfer_acknowledgement_controller',this.value+'_'+$incrementId+'_'+$company+'_'+$location+'_'+$store+'_'+$floorId+'_'+$roomId+'_'+$rackId, 'load_drop_down_bin_to', 'bin_td_$incrementId' );reset_room_rack_shelf($incrementId,'txt_shelf_to');",0,"","","","","","","txtShelfTo[]" );	
	exit();
}

if ($action=="load_drop_down_bin_to")
{
	$data=explode("_",$data);
	$shelfId=$data[0];
	$incrementId=$data[1];
	$company=$data[2];
	$location=$data[3];
	$store=$data[4];
	$floorId=$data[5];
	$roomId=$data[6];
	$rackId=$data[7];
	
	$location_cond="";
	if(str_replace("'","",$location)>0) $location_cond=" and b.location_id=$location";
	
	echo create_drop_down( "cbo_bin_to_$incrementId", 152, "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and b.store_id=$store and a.company_id='$company'  $location_cond and b.floor_id=$floorId and room_id=$roomId and rack_id=$rackId and shelf_id=$shelfId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name","bin_id,floor_room_rack_name", 1, "-- Select Bin --", 0, "",0,"","","","","","","txtBinTo[]" );	
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
	$location=str_replace("'","",$location);
	$store=str_replace("'","",$store);

	if ($location!=0) $location_cond	=" and a.to_location_id = '$location'";
	if ($store!=0) $store_cond	=" and b.to_store = '$store'";

	if ($db_type==0) $year_field="YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY')";
	else $year_field="";//defined Later

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	//,b.dtls_id, b.to_prod_id, b.from_prod_id, b.to_store, b.transfer_qnty, b.transfer_value, b.rate, b.uom, b.batch_id, b.to_floor_id
 	$sql="SELECT a.id, $year_field as year, a.transfer_prefix_number, a.transfer_system_id, a.challan_no, a.company_id, a.transfer_date, a.transfer_criteria, b.item_category, a.to_location_id, b.to_store 
	from inv_item_transfer_mst a ,inv_item_transfer_dtls_ac b 
	where b.item_category in(".implode(",",array_flip($general_item_category)).") and a.to_company=$company $store_cond and a.id=b.mst_id and a.entry_form=57 and a.transfer_criteria in(1,2) and b.is_acknowledge=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.id,a.insert_date, a.transfer_prefix_number, a.transfer_system_id, a.challan_no, a.company_id, a.transfer_date, a.transfer_criteria, b.item_category,a.to_location_id, b.to_store order by a.transfer_system_id";
	//echo $sql;//die;
	$arr=array(2=>$company_arr,3=>$store_arr,5=>$item_transfer_criteria,6=>$item_category);
	echo  create_list_view("tbl_list_search", "Challan No,Year,Company,Store,Transfer Date,Transfer Criteria,Item Category", "120,40,120,120,70,120","760","250",0, $sql, "js_set_value", "id,to_store", "", 1, "0,0,company_id,to_store,0,transfer_criteria,item_category", $arr, "transfer_system_id,year,company_id,to_store,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	echo "<input type='hidden' id='transfer_data_str' />";
	exit();
}


if($action=='populate_data_from_transfer_master')
{
	$data = explode("_", $data);
	$transfer_id = $data[0];
	$store_id = $data[1];
	$data_array=sql_select("SELECT a.transfer_system_id, a.challan_no, a.company_id, a.location_id, a.transfer_date, a.transfer_criteria, b.item_category, a.to_company, a.to_location_id,b.to_store 
	from inv_item_transfer_mst a,inv_item_transfer_dtls_ac b 
	where a.id=b.mst_id and a.id='$transfer_id' 
	group by a.transfer_system_id, a.challan_no, a.company_id, a.location_id, a.transfer_date, a.transfer_criteria, b.item_category, a.to_company, a.to_location_id,b.to_store");
	foreach ($data_array as $row)
	{ 
		//echo "load_drop_down('requires/general_item_transfer_acknowledgement_controller','".$row[csf("to_company")]."', 'load_drop_down_location_to', 'to_location_td' );\n";
		//echo "document.getElementById('cbo_location_to').value 				= '".$row[csf("to_location_id")]."';\n";

		/*if($row[csf("to_store")]>0)
		{
			$loc_com=$row[csf("to_location_id")]."_".$row[csf("to_company")];
			echo "load_drop_down('requires/general_item_transfer_acknowledgement_controller','".$loc_com."', 'load_drop_down_store_to', 'to_store_td' );\n";
			echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store")]."';\n";

			//echo "load_room_rack_self_bin('requires/general_item_transfer_acknowledgement_controller*3*cbo_store_name_to', 'store','to_store_td', '".$row[csf("to_company")]."','".$row[csf("to_location_id")]."',this.value);\n";
			//echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store")]."';\n";
		}*/

		//echo "document.getElementById('mst_id').value 						= '".$data."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		
		//echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("to_company")]."';\n";
		//echo "document.getElementById('cbo_location_to').value 				= '".$row[csf("to_location_id")]."';\n";
		//echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		//echo "fnc_floor_load(".$row[csf("to_store")].'_'.$row[csf("to_company")].'_'.$row[csf("to_location_id")].");\n";
		//reset_room_rack_shelf('','cbo_store_name_to');
		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";
		echo "$('#cbo_store_name_to').attr('disabled','disabled');\n";
		echo "$('#cbo_location_to').attr('disabled','disabled');\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_transfer_acknowledgement',1,1);\n"; 
		exit();
		
	}
}


if($action=="show_dtls_list_view")
{
	$data_array=sql_select("SELECT a.transfer_criteria, a.company_id, a.to_company, a.location_id, a.to_location_id, a.from_order_id, a.to_order_id, b.id, b.mst_id, b.dtls_id,  b.item_category, b.from_store, b.to_store, b.floor_id, b.room, b.rack, b.shelf, b.bin_box, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.rate, b.transfer_value, b.color_id, b.uom, b.batch_id, b.yarn_lot, b.store_rate, b.store_amount 
	from inv_item_transfer_mst a, inv_item_transfer_dtls_ac b 
	where a.id='$data' and a.id=b.mst_id and b.is_acknowledge=0");
	
	foreach($data_array as $row)
	{
		$order_ids.=$row[csf('to_order_id')].",";
		$order_id=chop($order_ids,",");
		$prod_id.=$row[csf('to_prod_id')].',';
	}
	$prod_ids=rtrim($prod_id,',');
	$order_name_arr=return_library_array( "select id ,po_number from wo_po_break_down where status_active=1 and is_deleted =0 and id in(".$order_id.")",'id','po_number');

	$store=$data_array[0][csf('to_store')];
	$company_id=$data_array[0][csf('to_company')];
	$location=$data_array[0][csf('to_location_id')];

	$lib_group_arr=return_library_array("select id, item_name from lib_item_group where status_active=1 and is_deleted =0",'id','item_name');

	$sql_prod=sql_select("select id, product_name_details, lot, item_group_id from product_details_master where id in($prod_ids) and status_active=1 and is_deleted=0");
	foreach ($sql_prod as $row) {
		$product_arr[$row[csf('id')]]['lot']=$row[csf('lot')];
		$product_arr[$row[csf('id')]]['item_group_id']=$row[csf('item_group_id')];
		$product_arr[$row[csf('id')]]['product_name_details']=$row[csf('product_name_details')];
	}

	//===============
	////and b.location_id=$location
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
	//echo $lib_room_rack_shelf_sql;die;

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
	//print_r($lib_floor_arr[1]);die;

	$i=0;  $company_id=$data_array[0][csf('to_company')];
	foreach($data_array as $row)
	{
		$i++;
		$prod_id=$row[csf('to_prod_id')];
		$item_desc=$product_arr[$prod_id]['product_name_details'];
		
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";


		$floor=$row[csf('to_floor_id')];
		$room=$row[csf('to_room')];
		$rack=$row[csf('to_rack')];
		$shelf =$row[csf('to_shelf')];
		$bin =$row[csf('to_bin_box')];


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
                <? echo create_drop_down( "cbo_item_category_$i", 120, $general_item_category,'', 0, "", $row[csf('item_category')], "",1,"","","","","","","cboItemCategory[]" ); ?>
            	<input type="hidden" id="txtDtlsID_<? echo $i; ?>" name="txtDtlsID[]" class="text_boxes" value="<? echo $row[csf('dtls_id')]; ?>" />
            	<input type="hidden" id="txtDtlsAcID_<? echo $i; ?>" name="txtDtlsAcID[]" class="text_boxes" value="<? echo $row[csf('id')]; ?>" />
                <input type="hidden" id="txtBatchId_<? echo $i; ?>" name="txtBatchId[]" class="text_boxes" value="<? echo $row[csf('batch_id')]; ?>" />
            	<input type="hidden" id="txtTransID_<? echo $i; ?>" name="txtTransID[]" class="text_boxes" value="" />
            </td>
            <td>
            	<input type="text" id="txtItemGroup_<? echo $i; ?>" name="txtItemGroup[]" class="text_boxes" style="width:80px;" value="<? echo $lib_group_arr[$product_arr[$prod_id]['item_group_id']]; ?>" readonly />
            	<input type="hidden" id="txtItemGroupID_<? echo $i; ?>" name="txtItemGroupID[]" class="text_boxes" value="<? echo $product_arr[$prod_id]['item_group_id']; ?>" />
            </td>
            <td>
            	<input type="text" id="txtItemDesc_<? echo $i; ?>" name="txtItemDesc[]" class="text_boxes" style="width:120px;" value="<? echo $item_desc; ?>" readonly />
				<input type="hidden" id="productID_<? echo $i; ?>" name="productID[]" value="<? echo $row[csf('to_prod_id')]; ?>"  readonly />
				<input type="hidden" id="fromProductID_<? echo $i; ?>" name="fromProductID[]" value="<? echo $row[csf('from_prod_id')]; ?>"  readonly />
				<input type="hidden" id="colorID_<? echo $i; ?>" name="colorID[]" value="<? echo $row[csf('color_id')]; ?>"  readonly />
            </td>
            <td>
            	<input type="text" id="txtLot_<? echo $i; ?>" name="txtLot[]" class="text_boxes" style="width:70px;" value="<? echo $row[csf('yarn_lot')];//$product_arr[$prod_id]['lot']; ?>" readonly />
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
                <? echo create_drop_down( "cbo_uom_$i", 50, $unit_of_measurement,'', 0, "", $row[csf('uom')], "",1,'',"","","","","","cboUom[]" );?>
            </td>
            <td id="floor_td_to_<? echo $i;?>">
                <? 
					//reset_room_rack_shelf($i,'cbo_floor_to');
                	echo create_drop_down( "cbo_floor_to_$i", 120,$floor_arr,"", 1, "--Select--",$row[csf('to_floor_id')], "load_drop_down( 'requires/general_item_transfer_acknowledgement_controller',this.value+'_'+$i+'_'+$company+'_'+$location+'_'+$store, 'load_drop_down_room_to', 'room_td_$i' );",0,"","","","","","","cboFloorTo[]" );
                ?>
            </td>
            <td id="room_td_<? echo $i;?>">
            	<? 
            		echo create_drop_down( "cbo_room_to_$i", 120,$room_arr,"", 1, "--Select--",$row[csf('to_room')], "",0,"","","","","","","cboRoomTo[]" ); ?>
            </td>
            <td id="rack_td_<? echo $i;?>">
            	<? echo create_drop_down( "txt_rack_to_$i", 120,$rack_arr,"", 1, "--Select--",$row[csf('to_rack')], "",0,"","","","","","","txtRackTo[]" ); ?>
            </td>
            <td id="shelf_td_<? echo $i;?>">
            	<? echo create_drop_down( "txt_shelf_to_$i", 120,$self_arr,"", 1, "--Select--",$row[csf('to_shelf')], "",0,"","","","","","","txtShelfTo[]" ); ?>
            </td>
            <td id="bin_td_<? echo $i;?>">
            	<? echo create_drop_down( "cbo_bin_to_$i", 120,$bin_arr,"", 1, "--Select--",$row[csf('to_bin_box')], "",0,"","","","","","","txtBinTo[]" ); ?>
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
	//if(str_replace("'", "", $cbo_transfer_criteria) == 1 || str_replace("'", "", $cbo_transfer_criteria) == 2) $entry_form=112;
	//else $entry_form=78;
	// echo "10**".$entry_form;die;
	$all_prod_id_arr=array();
	for($i=1;$i<=$numRow;$i++)
	{
		$productID 	 	="productID_".$i;
		$all_prod_id_arr[str_replace("'","",$$productID)]=str_replace("'","",$$productID);
	}
	
	$data_prod=sql_select("select ID, SUPPLIER_ID, CURRENT_STOCK, AVG_RATE_PER_UNIT, STOCK_VALUE from product_details_master where id in(".implode(",",$all_prod_id_arr).")");
	$prod_data_arr=array();
	foreach($data_prod as $val)
	{
		$prod_data_arr[$val["ID"]]["SUPPLIER_ID"]=$val["SUPPLIER_ID"];
		$prod_data_arr[$val["ID"]]["CURRENT_STOCK"]=$val["CURRENT_STOCK"];
		$prod_data_arr[$val["ID"]]["AVG_RATE_PER_UNIT"]=$val["AVG_RATE_PER_UNIT"];
		$prod_data_arr[$val["ID"]]["STOCK_VALUE"]=$val["STOCK_VALUE"];
	}
	//echo "10**select ID, RATE, CONS_QTY AS CURRENT_STOCK, AMOUNT AS STOCK_VALUE, CATEGORY_ID, PROD_ID from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id in(".implode(",",$all_prod_id_arr).") and store_id=$cbo_store_name_to and company_id=$cbo_company_id_to";die;
	$sql_store_rcv = sql_select("select ID, RATE, CONS_QTY AS CURRENT_STOCK, AMOUNT AS STOCK_VALUE, CATEGORY_ID, PROD_ID from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id in(".implode(",",$all_prod_id_arr).") and store_id=$cbo_store_name_to and company_id=$cbo_company_id_to");
	$store_data_all=array();
	foreach($sql_store_rcv as $val)
	{
		$store_data_all[$val["CATEGORY_ID"]][$val["PROD_ID"]]["ID"]=$val["ID"];
		$store_data_all[$val["CATEGORY_ID"]][$val["PROD_ID"]]["RATE"]=$val["RATE"];
		$store_data_all[$val["CATEGORY_ID"]][$val["PROD_ID"]]["CURRENT_STOCK"]=$val["CURRENT_STOCK"];
		$store_data_all[$val["CATEGORY_ID"]][$val["PROD_ID"]]["STOCK_VALUE"]=$val["STOCK_VALUE"];
	}
	
	//echo "10**".$entry_form;print_r($store_data_all);die;
	
	$variable_store_wise_rate=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$cbo_company_id_to and variable_list=47 and item_category_id=8 and status_active=1 and is_deleted=0","auto_transfer_rcv");
	if($variable_store_wise_rate != 1) $variable_store_wise_rate=2;
	
	if ($operation==0)  // Insert Here
	{
		
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$txt_transfer_date="'".change_date_format($txt_transfer_date,'','',1)."'";
		//echo "10**$txt_transfer_date";die;
		$id = return_next_id_by_sequence("INV_ITEM_TRANS_MST_AC_PK_SEQ", "inv_item_trans_acknowledgement", $con);
		$field_array="id, entry_form, challan_id, company_id, store_id, location_id, transfer_criteria, acknowledg_date, remarks, inserted_by, insert_date";
		
		$data_array="(".$id.",477,".$challan_id.",".$cbo_company_id_to.",".$cbo_store_name_to.",".$cbo_location_to.",".$cbo_transfer_criteria.",".$txt_transfer_date.",'".$txt_remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		$field_array_trans="id, mst_id, transaction_criteria, company_id, prod_id, pi_wo_batch_no, item_category, transaction_type, transaction_date, store_id, floor_id, room, rack, self, bin_box, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, inserted_by, insert_date, batch_lot, store_rate, store_amount";

		$field_array_dtls_update="to_store*to_floor_id*to_room*to_rack*to_shelf*to_bin_box*updated_by*update_date";
		$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
		$field_array_store="rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date";
		$dtls_ac_ids='';
		$field_array_store_rcv="id,company_id,store_id,category_id,prod_id,cons_qty,rate,amount,last_purchased_qnty,inserted_by,insert_date,lot,first_receive_date,last_receive_date";
		$prod_id_arr=$store_id_arr=array();
		$sdtlsid = return_next_id( "id", "inv_store_wise_gen_qty_dtls", 1 );
		$data_array_store_rcv="";
		for($i=1;$i<=$numRow;$i++)
		{
			$cboItemCategory="cbo_item_category_".$i;
			$txtDtlsID 		="txtDtlsID_".$i;
			$txtDtlsAcID 	="txtDtlsAcID_".$i;
			$txtItemDesc 	="txtItemDesc_".$i;
			$productID 	 	="productID_".$i;
			$fromProductID 	="fromProductID_".$i;
			$txtItemGroupID	="txtItemGroupID_".$i;
			$cboFloorTo 	="cboFloorTo_".$i;
			$cboRoomTo 	 	="cboRoomTo_".$i;
			$txtRackTo 		="txtRackTo_".$i;
			$txtShelfTo 	="txtShelfTo_".$i;
			$txtBinTo 		="txtBinTo_".$i;
			$hiddenTranQnty ="hiddenTranQnty_".$i;
			$hiddenTranRate ="hiddenTranRate_".$i;
			$hiddenTranValue="hiddenTranValue_".$i;
			$cbo_uom 		="cboUom_".$i;
			$txtBatchId 	="txtBatchId_".$i;
			$txtLot 		="txtLot_".$i;
			
			$hiddenStoreRate ="hiddenStoreRate_".$i;
			$hiddenStoreValue="hiddenStoreValue_".$i;
			
			$dtls_ac_ids.= str_replace("'","",$$txtDtlsAcID).",";

			$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			if($data_array_trans!="") $data_array_trans.=",";

			$data_array_trans .="(".$recv_trans_id.",".$challan_id.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",'".str_replace("'","",$$productID)."','".str_replace("'","",$$txtBatchId)."','".str_replace("'","",$$cboItemCategory)."',5,".$txt_transfer_date.",".$cbo_store_name_to.",'".str_replace("'","",$$cboFloorTo)."','".str_replace("'","",$$cboRoomTo)."','".str_replace("'","",$$txtRackTo)."','".str_replace("'","",$$txtShelfTo)."','".str_replace("'","",$$txtBinTo)."','".str_replace("'","",$$cbo_uom)."','".str_replace("'","",$$hiddenTranQnty)."','".number_format(str_replace("'","",$$hiddenTranRate),10,'.','')."','".number_format(str_replace("'","",$$hiddenTranValue),8,'.','')."','".str_replace("'","",$$hiddenTranQnty)."','".number_format(str_replace("'","",$$hiddenTranValue),8,'.','')."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".str_replace("'","",$$txtLot)."','".number_format(str_replace("'","",$$hiddenStoreRate),10,'.','')."','".number_format(str_replace("'","",$$hiddenStoreValue),8,'.','')."')";

			
			if($$txtDtlsID !='')
			{
				$id_arr[]=str_replace("'","",$$txtDtlsID);
				$data_array_dtls_update[str_replace("'","",$$txtDtlsID)] = explode("*",("".$cbo_store_name_to."*".str_replace("'","",$$cboFloorTo)."*".str_replace("'","",$$cboRoomTo)."*".str_replace("'","",$$txtRackTo)."*".str_replace("'","",$$txtShelfTo)."*".str_replace("'","",$$txtBinTo)."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			}//."'*'".$recv_trans_id
		
			
			$curr_stock_qnty=$prod_data_arr[str_replace("'","",$$productID)]["CURRENT_STOCK"]+str_replace("'", '',$$hiddenTranQnty);
			$curr_stock_value=$prod_data_arr[str_replace("'","",$$productID)]["STOCK_VALUE"]+str_replace("'","",$$hiddenTranValue);
			$avg_rate_per_unit=0;
			if($curr_stock_qnty != 0 && $curr_stock_value != 0) $avg_rate_per_unit=$curr_stock_value/$curr_stock_qnty;
			if($avg_rate_per_unit==0)
			{
				echo "20**Rate Not Found";oci_rollback($con);disconnect($con); die;
			}
			
			$prod_id_arr[]=str_replace("'","",$$productID);
			$data_array_prod_update[str_replace("'","",$$productID)] = explode("*",("'".number_format($avg_rate_per_unit,10,'.','')."'*".str_replace("'","",$$hiddenTranQnty)."*".$curr_stock_qnty."*".number_format($curr_stock_value,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			
			
			$store_up_id_to=0;
			if($variable_store_wise_rate==1)
			{
				if($store_data_all[str_replace("'","",$$cboItemCategory)][str_replace("'","",$$productID)]["ID"]>0)
				{
					$store_up_id_to=$store_data_all[str_replace("'","",$$cboItemCategory)][str_replace("'","",$$productID)]["ID"];
					$store_presentStock	=$store_data_all[str_replace("'","",$$cboItemCategory)][str_replace("'","",$$productID)]["CURRENT_STOCK"];
					$store_presentStockValue =$store_data_all[str_replace("'","",$$cboItemCategory)][str_replace("'","",$$productID)]["STOCK_VALUE"];
					
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
					$data_array_store_rcv.= "(".$sdtlsid.",".$cbo_company_id_to.",".$cbo_store_name_to.",".$$cboItemCategory.",".$$productID.",".$$hiddenTranQnty.",'".number_format(str_replace("'","",$$hiddenStoreRate),10,'.','')."','".number_format(str_replace("'","",$$hiddenStoreValue),8,'.','')."',".$$hiddenTranQnty.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$txt_lot."',".$txt_transfer_date.",".$txt_transfer_date.")";
					$sdtlsid++;
				}
			}
		}
		
		$field_array_mst_update = "is_acknowledge";
		$data_array_mst_update  = "1";
		
		$rID=$rID8=$rID2=$rID3=$rID4=$rID5=$rID6=$storeInsID=true;
		$rID=sql_insert("inv_item_trans_acknowledgement",$field_array,$data_array,0);
		$rID8=sql_update("inv_item_transfer_mst",$field_array_mst_update,$data_array_mst_update,"id",$challan_id,0);
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if(count($id_arr)>0) // transfer_dtls table update
		{
			//echo "10**".bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr );die;
			$rID3=execute_query(bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ));
		} 
		//echo "10**";die;

		$dtls_ac_ids=chop($dtls_ac_ids,",");
		if($dtls_ac_ids!="")					// transfer_dtls_ac is_acknowledge update
		{
			$field_array_status="updated_by*update_date*is_acknowledge";
			$data_array_status=$user_id."*'".$pc_date_time."'*1";
			$rID4=sql_multirow_update("inv_item_transfer_dtls_ac",$field_array_status,$data_array_status,"id",$dtls_ac_ids,0);
		}
		
		if(count($prod_id_arr)>0) // product table update
		{
			$rID5=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_arr ));
		}
		
		if(count($store_id_arr)>0 && $variable_store_wise_rate==1) // store table update
		{
			$rID6=execute_query(bulk_update_sql_statement( "inv_store_wise_gen_qty_dtls", "id", $field_array_store, $data_array_store, $store_id_arr ));
		}
		
		if($data_array_store_rcv!="" && $variable_store_wise_rate==1)
		{
			//echo "10**insert into inv_store_wise_gen_qty_dtls (".$field_array_store_rcv.") values ".$data_array_store_rcv;oci_rollback($con);disconnect($con);die;
			$storeInsID=sql_insert("inv_store_wise_gen_qty_dtls",$field_array_store_rcv,$data_array_store_rcv,1); 
		}
		
		//echo "10**$rID=$rID8=$rID2=$rID3=$rID4=$rID5=$rID6=$storeInsID";oci_rollback($con);disconnect($con);die;

		if($db_type==0)
		{
			if($rID && $rID8 && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $storeInsID)
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
			if($rID && $rID8 && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $storeInsID)
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

if ($action == "transfer_acknowledgement_print_1") //print button
{
    extract($_REQUEST);
    $data = explode('*', $data);
	// echo "<pre>"; print_r($data); die;

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$store_arr = return_library_array("select id, store_name from lib_store_location", "id", "store_name");
	$item_category_arr = return_library_array("select category_id, actual_category_name from lib_item_category_list", "category_id", "actual_category_name");
	$item_group_arr = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$user_arr = return_library_array("SELECT ID, USER_FULL_NAME from USER_PASSWD", "ID", "USER_FULL_NAME");

	// $mst_data_array=sql_select("SELECT a.id, a.remarks, d.transfer_system_id, d.challan_no, d.transfer_date, d.transfer_criteria, d.company_id, d.requisition_no, a.inserted_by, 
	// from inv_item_trans_acknowledgement a, inv_item_transfer_mst d
	// where a.id='$data[0]' and d.id=a.challan_id and a.entry_form=477 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0");

	$mst_data_array=sql_select("SELECT a.id, a.remarks, d.transfer_system_id, d.challan_no, d.transfer_date, d.transfer_criteria, d.company_id, d.requisition_no, a.inserted_by, b.from_store
	from inv_item_trans_acknowledgement a, inv_item_transfer_mst d, inv_item_transfer_dtls b
	where a.id='$data[0]' and d.id=b.mst_id and d.id=a.challan_id and a.entry_form=477 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0");

	// echo "<pre>"; print_r($mst_data_array); die;
	$store = $mst_data_array[0]['FROM_STORE'];
	$from_company = $mst_data_array[0]['COMPANY_ID'];
	// echo $store . " " . $from_company; die;
	?>
    <div style="width:810px;">
        <table cellspacing="0" style="font: 12px tahoma; width: 100%;">
            <tr>
                <td colspan="6" align="center" style="font-size:24px">
                    <strong><? echo $company_library[$data[1]]; ?></strong>
				</td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">
                    <?
                    $nameArray = sql_select("SELECT location_name,email,website from lib_location where id=$data[5] and company_id=$data[1] and status_active=1 and is_deleted=0");
                    foreach ($nameArray as $result) {
                        ?>
                        Factory Location: <? echo $result[csf('location_name')]; ?><br>
                        Email Address: <? echo $result[csf('email')]; ?><br>
                        Website No: <? echo $result[csf('website')];
                    }
                    ?>
                </td>
            </tr>
			<tr>
                <td colspan="6" align="center" style="font-size:16px;">
                    <strong><u> General Item Transfer Acknowledgement Letter</u></strong>
				</td>
            </tr>
            <tr>
                <td width="150"><strong>Transfer Ack. ID:</strong></td>
                <td width="150"><?=$data[0]; ?></td>
                <td width="105">&nbsp;</td>
                <td width="105">&nbsp;</td>
                <td width="150"><strong>Transfer ID:</strong></td>
                <td width="150"><? echo $mst_data_array[0]['TRANSFER_SYSTEM_ID']; ?></td>
            </tr>
            <tr>
                <td width="150"><strong>Ack Date:</strong></td>
                <td width="150"><? echo change_date_format($data[4]); ?></td>
                <td width="105">&nbsp;</td>
                <td width="105">&nbsp;</td>
                <td width="150"><strong>Transfer Criteria:</strong></td>
                <td width="150"><? echo $item_transfer_criteria[$mst_data_array[0]['TRANSFER_CRITERIA']]; ?></td>
            </tr>
            <tr>
                <td width="150"><strong>Challan No:</strong></td>
                <td width="150"><?=$data[3]; ?></td>
                <td width="105">&nbsp;</td>
                <td width="105">&nbsp;</td>
                <td width="150"><strong>Transfer Date:</strong></td>
                <td width="150"><? echo change_date_format($mst_data_array[0]['TRANSFER_DATE']); ?></td>
            </tr>
            <tr>
                <td width="150"><strong>Requisition No:</strong></td>
                <td width="150"><? echo $mst_data_array[0]['REQUISITION_NO']; ?></td>
                <td width="105">&nbsp;</td>
                <td width="105">&nbsp;</td>
                <td width="150"><strong>From Company:</strong></td>
                <td width="150"><? echo $company_library[$mst_data_array[0]['COMPANY_ID']]; ?></td>
            </tr>
            <tr>
                <td width="150"><strong>Store:</strong></td>
                <td width="150"><?=$store_arr[$data[2]]; ?></td>
                <td width="105">&nbsp;</td>
                <td width="105">&nbsp;</td>
                <td width="150"><strong>From Location:</strong></td>
                <td width="150">
					<?
					$store_location = sql_select("SELECT ID,STORE_LOCATION FROM LIB_STORE_LOCATION WHERE ID=$store and COMPANY_ID=$from_company AND STATUS_ACTIVE=1 AND IS_DELETED=0");
					echo $store_location[0]['STORE_LOCATION'];
					?>
				</td>
            </tr>
            <tr>
                <td width="150"><strong>Remarks:</strong></td>
                <td width="150"><? echo $mst_data_array[0]['REMARKS']; ?></td>
                <td width="105">&nbsp;</td>
                <td width="105">&nbsp;</td>
                <td width="150">&nbsp;</td>
                <td width="150">&nbsp;</td>
            </tr>
        </table>
        <div style="width:100%;">
            <table cellspacing="0" width="810" border="1" rules="all" class="rpt_table" style=" margin-top:20px; font: 12px tahoma;">
                <thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="100" align="center">From Store</th>
					<th width="100" align="center">Item Category</th>
					<th width="100" align="center">Item Group</th>
					<th width="140" align="center">Item Description</th>
					<th width="80" align="center">Brand</th>
					<th width="80" align="center">UOM</th>
					<th width="100" align="center">Item Code</th>
					<th width="80" align="center">Acl. Qnty.</th>
                </thead>
                <tbody>
                <?
				$data_array=sql_select("SELECT a.id, b.from_store,b.item_category, b.brand_id,b.uom,b.transfer_qnty, b.to_prod_id
				from inv_item_trans_acknowledgement a, inv_item_transfer_dtls b, inv_item_transfer_mst d 
				where a.id='$data[0]' and a.challan_id=b.mst_id and d.id=b.mst_id and a.entry_form=477 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 
				and b.is_deleted=0");
				// echo "<pre>";print_r($data_array);die;

				foreach($data_array as $row)
				{
					$prod_id.=$row[csf('to_prod_id')].',';
				}
				$prod_ids=rtrim($prod_id,',');
				
				$sql_prod=sql_select("select id, product_name_details, item_code, item_group_id from product_details_master where id in($prod_ids) and status_active=1 and is_deleted=0");
				foreach ($sql_prod as $row) {
					$product_arr[$row[csf('id')]]['item_group_id']=$row[csf('item_group_id')];
					$product_arr[$row[csf('id')]]['product_name_details']=$row[csf('product_name_details')];
					$product_arr[$row[csf('id')]]['item_code']=$row[csf('item_code')];
				}
				// echo "<pre>";print_r($product_arr);die;

                $i = 1;
                $tot_qnty = array();
                foreach ($data_array as $val) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
						<td align="center"><? echo $store_arr[$val[csf('from_store')]]; ?></td>
						<td align="center"><? echo $item_category_arr[$val[csf('item_category')]]; ?></td>
						<td><? echo $item_group_arr[$product_arr[$val[csf('to_prod_id')]]['item_group_id']]; ?></td>
						<td><? echo $product_arr[$val[csf('to_prod_id')]]['product_name_details']; ?></td>
						<td><? echo $brand_arr[$val[csf('brand_id')]]; ?></td>
						<td align="center"><? echo $unit_of_measurement[$val[csf('uom')]]; ?></td>
						<td align="center"><? echo $product_arr[$val[csf('to_prod_id')]]['item_code']; ?></td>
						<td align="right"><? echo $val[csf('transfer_qnty')]; ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                </tbody>
            </table>
            <br><br>
			<p>Dear Sir/Medam,</p>
			<p>We have received these item(s) which has/have been given above on <strong><? echo change_date_format($data[4]); ?></strong> from <strong><? echo $company_library[$mst_data_array[0]['COMPANY_ID']]; ?></strong>. Now these item(s) is/are in our stock.</p>
			<br><br><br>
			<p>Thanks & Regards</p>
			<p><? echo $user_arr[$mst_data_array[0]['INSERTED_BY']]; ?></p>
            <?
            //echo signature_table(28, $data[0], "900px");
            ?>
        </div>
    </div>
	
    <?
    exit();
}

if($action=="show_dtls_list_view_update")
{

	$data_array=sql_select("SELECT a.id, a.challan_id, a.acknowledg_date, d.remarks, b.id as dtls_id, b.mst_id, b.item_category, b.from_store, b.to_store, b.floor_id, b.room, b.rack, b.shelf, b.bin_box, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.rate, b.transfer_value, b.color_id, b.uom, d.from_order_id, d.to_order_id, d.transfer_date, B.to_trans_id as trans_id, b.batch_id ,a.company_id as company_id, b.yarn_lot, b.store_rate, b.store_amount
	from inv_item_trans_acknowledgement a, inv_item_transfer_dtls b, inv_item_transfer_mst d
	where a.id='$data' and a.challan_id=b.mst_id and d.id=b.mst_id and a.entry_form=477 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");	

	foreach($data_array as $row)
	{
		$prod_id_arr[$row[csf('to_prod_id')]]=$row[csf('to_prod_id')];
	}

	$store=$data_array[0][csf('to_store')];
	$company_id=$data_array[0][csf('company_id')];
	$location=$data_array[0][csf('to_location_id')];

	$lib_group_arr=return_library_array("select id, item_name from lib_item_group where status_active=1 and is_deleted =0",'id','item_name');
	//=$prod_id
	$sql_prod=sql_select("select id, product_name_details, lot, item_group_id from product_details_master where id in(".implode(",",$prod_id_arr).") and status_active=1 and is_deleted=0");
	foreach ($sql_prod as $row) {
		$product_arr[$row[csf('id')]]['lot']=$row[csf('lot')];
		$product_arr[$row[csf('id')]]['item_group_id']=$row[csf('item_group_id')];
		$product_arr[$row[csf('id')]]['product_name_details']=$row[csf('product_name_details')];
	}

	$location_cond="";
	if($location>0) $location_cond=" and b.location_id=$location";
	$store_cond="";
	if($store>0) $store_cond=" and b.store_id=$store";
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
	where b.status_active=1 and b.is_deleted=0 and b.company_id = $company_id $location_cond $store_cond";
	//echo "10**".$lib_room_rack_shelf_sql;die;
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

	$i=0; 
	
	$company_id=$data_array[0][csf('company_id')];
	foreach($data_array as $row)
	{
		$i++;
		$prod_id=$row[csf('to_prod_id')];
		$item_desc=$product_arr[$prod_id]['product_name_details'];
		
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";


		$floor=$row[csf('to_floor_id')];
		$room=$row[csf('to_room')];
		$rack=$row[csf('to_rack')];
		$shelf =$row[csf('to_shelf')];
		$bin =$row[csf('to_bin_box')];


		$floor_arr = $lib_floor_arr[$company];
		$room_arr = $lib_room_arr[$company][$floor];
		$rack_arr = $lib_rack_arr[$company][$floor][$room];
		$self_arr = $lib_shelf_arr[$company][$floor][$room][$rack];
		$bin_arr = $lib_bin_arr[$company][$floor][$room][$rack][$shelf];
		
		//print_r($floor_arr);print_r($room_arr);print_r($rack_arr);print_r($self_arr);die;

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
                <? echo create_drop_down( "cbo_item_category_$i", 120, $general_item_category,'', 0, "", $row[csf('item_category')], "",1,"","","","","","","cboItemCategory[]" ); ?>
            	<input type="hidden" id="txtDtlsID_<? echo $i; ?>" name="txtDtlsID[]" class="text_boxes" value="<? echo $row[csf('dtls_id')]; ?>" />
            	<input type="hidden" id="txtDtlsAcID_<? echo $i; ?>" name="txtDtlsAcID[]" class="text_boxes" value="<? echo $row[csf('id')]; ?>" />
                <input type="hidden" id="txtBatchId_<? echo $i; ?>" name="txtBatchId[]" class="text_boxes" value="<? echo $row[csf('batch_id')]; ?>" />
            	<input type="hidden" id="txtTransID_<? echo $i; ?>" name="txtTransID[]" class="text_boxes" value="" />
            </td>
            <td>
            	<input type="text" id="txtItemGroup_<? echo $i; ?>" name="txtItemGroup[]" class="text_boxes" style="width:80px;" value="<? echo $lib_group_arr[$product_arr[$prod_id]['item_group_id']]; ?>" readonly />
            	<input type="hidden" id="txtItemGroupID_<? echo $i; ?>" name="txtItemGroupID[]" class="text_boxes" value="<? echo $product_arr[$prod_id]['item_group_id']; ?>" />
            </td>
            <td>
            	<input type="text" id="txtItemDesc_<? echo $i; ?>" name="txtItemDesc[]" class="text_boxes" style="width:120px;" value="<? echo $item_desc; ?>" readonly />
				<input type="hidden" id="productID_<? echo $i; ?>" name="productID[]" value="<? echo $row[csf('to_prod_id')]; ?>"  readonly />
				<input type="hidden" id="fromProductID_<? echo $i; ?>" name="fromProductID[]" value="<? echo $row[csf('from_prod_id')]; ?>"  readonly />
				<input type="hidden" id="colorID_<? echo $i; ?>" name="colorID[]" value="<? echo $row[csf('color_id')]; ?>"  readonly />
            </td>
            <td>
            	<input type="text" id="txtLot_<? echo $i; ?>" name="txtLot[]" class="text_boxes" style="width:70px;" value="<? echo $row[csf('yarn_lot')]; ?>" readonly />
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
                <? echo create_drop_down( "cbo_uom_$i", 50, $unit_of_measurement,'', 0, "", $row[csf('uom')], "",1,'',"","","","","","cboUom[]" );?>
            </td>
            <td id="floor_td_to_<? echo $i;?>">
                <? 
                	echo create_drop_down( "cbo_floor_to_$i", 120,$floor_arr,"", 1, "--Select--",$row[csf('to_floor_id')], "load_drop_down( 'requires/general_item_transfer_acknowledgement_controller',this.value+'_'+$i+'_'+$company+'_'+$location+'_'+$store, 'load_drop_down_room_to', 'room_td_$i' );reset_room_rack_shelf($i,'cbo_floor_to');",0,"","","","","","","cboFloorTo[]" );
                ?>
            </td>
            <td id="room_td_<? echo $i;?>">
            	<? 
            		echo create_drop_down( "cbo_room_to_$i", 120,$room_arr,"", 1, "--Select--",$row[csf('to_room')], "",0,"","","","","","","cboRoomTo[]" ); ?>
            </td>
            <td id="rack_td_<? echo $i;?>">
            	<? echo create_drop_down( "txt_rack_to_$i", 120,$rack_arr,"", 1, "--Select--",$row[csf('to_rack')], "",0,"","","","","","","txtRackTo[]" ); ?>
            </td>
            <td id="shelf_td_<? echo $i;?>">
            	<? echo create_drop_down( "txt_shelf_to_$i", 120,$self_arr,"", 1, "--Select--",$row[csf('to_shelf')], "",0,"","","","","","","txtShelfTo[]" ); ?>
            </td>
            <td id="bin_td_<? echo $i;?>">
            	<? echo create_drop_down( "cbo_bin_to_$i", 120,$bin_arr,"", 1, "--Select--",$row[csf('to_bin_box')], "",0,"","","","","","","txtBinTo[]" ); ?>
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

			function load_location()
			{
				var cbo_company_id_to='<? echo $cbo_company_id_to; ?>';
				load_drop_down('general_item_transfer_acknowledgement_controller',cbo_company_id_to, 'load_drop_down_location_to_up', 'to_location_td' );
			}
    	</script>
	</head>
	<body onLoad="load_location();">
	<div align="center" style="width:880px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:860px;margin-left:10px">
	        <legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="830" class="rpt_table">
	                <thead>
	                    <th>Transfer Criteria</th>
	                    <th>Location</th>
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
	                    <td id="to_location_td">
                            <?
                               echo create_drop_down( "cbo_location_to", 160, $blank_array,"", 1, "--Select Location--", 0, "",1 );
                            ?>	
	                    </td>
	                    <td id="to_store_td">
	                        <?
	                        	echo create_drop_down( "cbo_store_name_to", 160, $blank_array,"", 1, "--Select store--", 0, "",1);
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
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_transfer_criteria').value+'_'+document.getElementById('cbo_location_to').value+'_'+document.getElementById('cbo_store_name_to').value+'_'+document.getElementById('txt_system_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id_to; ?>, 'create_transfer_search_list_view', 'search_div', 'general_item_transfer_acknowledgement_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$location=$data[1];
	$store=$data[2];
	$system_id=$data[3];
	$txt_date_from=$data[4];
	$txt_date_to=$data[5];
	$company=$data[6];

	if($company==0)$company_cond=""; else $company_cond=" and a.company_id=$company";
	if($transfer_criteria==0)$criteria_cond=""; else $criteria_cond=" and a.transfer_criteria=$transfer_criteria";
	if($location==0)$location_cond=""; else $location_cond=" and a.location_id=$location";
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
 	
 	$sql="select a.id, $year_field as year, a.entry_form, a.challan_id, a.company_id, a.store_id, a.location_id, a.transfer_criteria, a.item_category, a.acknowledg_date, a.remarks, b.transfer_system_id from inv_item_trans_acknowledgement a, inv_item_transfer_mst b where a.challan_id=b.id and a.entry_form=477 and a.transfer_criteria in(1,2) $company_cond $criteria_cond $location_cond $store_cond $system_id_cond $acknowledg_date_cond and a.status_active=1 and a.is_deleted=0";
	
	$store_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$location_arr=return_library_array( "select id, location_name from  lib_location", "id", "location_name"  );
	//print_r($location_arr);
	$arr=array(2=>$location_arr,3=>$store_arr,6=>$item_transfer_criteria);
	//print_r($item_transfer_criteria);

	echo  create_list_view("tbl_list_search", "System ID, Year, Location, Store, Challan No, Transfer Date, Transfer Criteria", "70,60,130,150,150,80","860","250",0, $sql, "js_set_value", "id", "", 1, "0,0,location_id,store_id,0,0,transfer_criteria", $arr, "id,year,location_id,store_id,transfer_system_id,acknowledg_date,transfer_criteria", '','','0,0,0,0,0,3,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master_update')
{
	$data_array=sql_select("SELECT a.id, a.entry_form, a.challan_id, a.company_id, a.store_id, a.location_id, a.transfer_criteria, a.item_category, a.acknowledg_date, a.remarks, b.transfer_system_id from inv_item_trans_acknowledgement a, inv_item_transfer_mst b where a.id='$data' and a.challan_id=b.id and a.entry_form=477 and a.transfer_criteria in(1,2) and a.status_active=1 and a.is_deleted=0");
	foreach ($data_array as $row)
	{ 
		echo "load_drop_down('requires/general_item_transfer_acknowledgement_controller','".$row[csf("company_id")]."', 'load_drop_down_location_to', 'to_location_td' );\n";
		echo "document.getElementById('cbo_location_to').value 				= '".$row[csf("location_id")]."';\n";
		$item_category=$row[csf("item_category")];
		if($row[csf("store_id")]>0)
		{
			echo "load_drop_down('requires/general_item_transfer_acknowledgement_controller','".$row[csf("company_id")]."_".$row[csf("location_id")]."', 'load_drop_down_store_to', 'to_store_td' );\n";
			echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("store_id")]."';\n";
			echo "$('#cbo_store_name_to').attr('disabled','disabled');\n";
		}
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('challan_id').value 					= '".$row[csf("challan_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("acknowledg_date")])."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("company_id")]."';\n";		
		//echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		
		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";
		echo "$('#cbo_location_to').attr('disabled','disabled');\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		exit();
		
	}
}

?>