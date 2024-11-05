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
	load_room_rack_self_bin("requires/raw_material_transfer_acknowledgement_controller",$data);
}

if ($action=="load_drop_down_location_to")
{ 
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location_to", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down( 'requires/raw_material_transfer_acknowledgement_controller',this.value+'_'+$data[0], 'load_drop_down_store_to', 'to_store_td' );" );	
	exit();
}


if ($action=="load_drop_down_location_to_up")
{
	$data=explode("_",$data);
		echo create_drop_down( "cbo_location_to", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "
		load_room_rack_self_bin('raw_material_transfer_acknowledgement_controller*101*cbo_store_name_to', 'store','to_store_td', $data[0],this.value);" );
	exit();
}


if ($action=="load_drop_down_store_to")
{
	$data=explode("_",$data);
	$company=$data[1];
	$location=$data[0];

	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id='$user_id'");
		$store_location_id = $userCredential[0][csf('store_location_id')];
		if ($store_location_id != '') {$store_location_credential_cond = "and a.id in($store_location_id)";} else { $store_location_credential_cond = "";}

	echo create_drop_down( "cbo_store_name_to", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.company_id=$company and a.location_id=$location and a.status_active=1 and a.is_deleted=0 and b.category_type in(101) $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "fnc_floor_load(this.value+'_'+$company+'_'+$location);reset_room_rack_shelf('','cbo_store_name_to');" );
	exit();
}

if ($action=="load_drop_down_floor_to")
{
	$data=explode("_",$data);
	$store=$data[0];
	$company=$data[1];
	$location=$data[2];
	$incrementId=$data[3];
	echo create_drop_down( "cbo_floor_to_$incrementId", 152, "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store' and a.company_id='$company' and b.location_id=$location and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name", "floor_id,floor_room_rack_name", 1, "-- Select Floor --", 0, "load_drop_down( 'requires/raw_material_transfer_acknowledgement_controller',this.value+'_'+$incrementId+'_'+$company+'_'+$location+'_'+$store, 'load_drop_down_room_to', 'room_td_$incrementId' );reset_room_rack_shelf($incrementId,'cbo_floor_to');",0,"","","","","","","cboFloorTo[]" );
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
	echo create_drop_down( "cbo_room_to_$incrementId", 152, "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and b.store_id=$store and a.company_id='$company'  and b.location_id=$location and b.floor_id=$floorId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "-- Select Room --", 0, "load_drop_down( 'requires/raw_material_transfer_acknowledgement_controller',this.value+'_'+$incrementId+'_'+$company+'_'+$location+'_'+$store+'_'+$floorId, 'load_drop_down_rack_to', 'rack_td_$incrementId' );reset_room_rack_shelf($incrementId,'cbo_room_to');",0,"","","","","","","cboRoomTo[]" );	
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
	echo create_drop_down( "txt_rack_to_$incrementId", 152, "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and b.store_id=$store and a.company_id='$company'  and b.location_id=$location and b.floor_id=$floorId and room_id=$roomId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "-- Select Rack --", 0, "load_drop_down( 'requires/raw_material_transfer_acknowledgement_controller',this.value+'_'+$incrementId+'_'+$company+'_'+$location+'_'+$store+'_'+$floorId+'_'+$roomId, 'load_drop_down_shelf_to', 'shelf_td_$incrementId' );reset_room_rack_shelf($incrementId,'txt_rack_to');",0,"","","","","","","txtRackTo[]" );	
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
	echo create_drop_down( "txt_shelf_to_$incrementId", 152, "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and b.store_id=$store and a.company_id='$company'  and b.location_id=$location and b.floor_id=$floorId and room_id=$roomId and rack_id=$rackId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "-- Select Shelf --", 0, "load_drop_down( 'requires/raw_material_transfer_acknowledgement_controller',this.value+'_'+$incrementId+'_'+$company+'_'+$location+'_'+$store+'_'+$floorId+'_'+$roomId+'_'+$rackId, 'load_drop_down_bin_to', 'bin_td_$incrementId' );reset_room_rack_shelf($incrementId,'txt_shelf_to');",0,"","","","","","","txtShelfTo[]" );	
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
	echo create_drop_down( "cbo_bin_to_$incrementId", 152, "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and b.store_id=$store and a.company_id='$company'  and b.location_id=$location and b.floor_id=$floorId and room_id=$roomId and rack_id=$rackId and shelf_id=$shelfId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name","bin_id,floor_room_rack_name", 1, "-- Select Bin --", 0, "",0,"","","","","","","txtBinTo[]" );	
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
	if($location!=0) $location_cond	=" and a.to_location_id = '$location'";
	if($store!=0) $store_cond	=" and b.to_store = '$store'";
	if($db_type==0) $year_field="YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY')";
	else $year_field="";//defined Later
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	//,b.dtls_id, b.to_prod_id, b.from_prod_id, b.to_store, b.transfer_qnty, b.transfer_value, b.rate, b.uom, b.batch_id, b.to_floor_id
 	$sql="SELECT a.id, $year_field as year, a.transfer_prefix_number, a.transfer_system_id, a.challan_no, a.company_id, a.transfer_date, a.transfer_criteria, a.item_category,a.to_location_id, b.to_store 
	from inv_item_transfer_mst a ,inv_item_transfer_dtls_ac b 
	where b.item_category=101 and a.company_id=$company $location_cond $store_cond and a.id=b.mst_id and a.entry_form in(487) and a.transfer_criteria in(1,2,4) and b.is_acknowledge=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.id,a.insert_date, a.transfer_prefix_number, a.transfer_system_id, a.challan_no, a.company_id, a.transfer_date, a.transfer_criteria, a.item_category,a.to_location_id, b.to_store order by a.transfer_system_id";
	//echo $sql;//die;
	// setFilterGrid(\'tbl_list_search\',-1);
	?>
	<table cellspacing="0" cellpadding="0" width="870" class="rpt_table" rules="all" border="1">
	<thead>
		<tr>
			<th align="canter" ><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
		</tr>
	</head>
	</table>
	<?
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
	$data_array=sql_select("SELECT a.transfer_system_id, a.challan_no, a.company_id, a.location_id, a.transfer_date, a.transfer_criteria, b.item_category, a.to_company, a.to_location_id,b.to_store 
	from inv_item_transfer_mst a,inv_item_transfer_dtls_ac b 
	where a.id=b.mst_id and a.id='$transfer_id' 
	group by a.transfer_system_id, a.challan_no, a.company_id, a.location_id, a.transfer_date, a.transfer_criteria, b.item_category, a.to_company, a.to_location_id,b.to_store");
	foreach ($data_array as $row)
	{ 
		echo "load_drop_down('requires/raw_material_transfer_acknowledgement_controller','".$row[csf("to_company")]."', 'load_drop_down_location_to', 'to_location_td' );\n";
		echo "document.getElementById('cbo_location_to').value 				= '".$row[csf("to_location_id")]."';\n";

		if($row[csf("to_store")]>0)
		{
			$loc_com=$row[csf("to_location_id")]."_".$row[csf("to_company")];
			echo "load_drop_down('requires/raw_material_transfer_acknowledgement_controller','".$loc_com."', 'load_drop_down_store_to', 'to_store_td' );\n";
			echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store")]."';\n";

			//echo "load_room_rack_self_bin('requires/raw_material_transfer_acknowledgement_controller*3*cbo_store_name_to', 'store','to_store_td', '".$row[csf("to_company")]."','".$row[csf("to_location_id")]."',this.value);\n";
			//echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store")]."';\n";
		}

		//echo "document.getElementById('mst_id').value 						= '".$data."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		
		//echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("to_company")]."';\n";
		//echo "document.getElementById('cbo_location_to').value 				= '".$row[csf("to_location_id")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		//echo "fnc_floor_load(".$row[csf("to_store")].'_'.$row[csf("to_company")].'_'.$row[csf("to_location_id")].");\n";
		//reset_room_rack_shelf('','cbo_store_name_to');
		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";
		echo "$('#cbo_location_to').attr('disabled','disabled');\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_transfer_acknowledgement',1,1);\n"; 
		exit();
		
	}
}


if($action=="show_dtls_list_view")
{
	$data_array=sql_select("SELECT a.transfer_criteria, a.company_id, a.to_company, a.location_id, a.to_location_id, a.from_order_id, a.to_order_id, b.id, b.mst_id, b.dtls_id,  b.from_store, b.to_store, b.floor_id, b.room, b.rack, b.shelf, b.bin_box, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.rate, b.transfer_value, b.color_id, b.uom, b.batch_id 
	from inv_item_transfer_mst a, inv_item_transfer_dtls_ac b 
	where a.id='$data' and a.id=b.mst_id and b.is_acknowledge=0");
	
	foreach($data_array as $row)
	{
		$order_ids.=$row[csf('to_order_id')].",";
		$order_id=chop($order_ids,",");
	}
	$order_name_arr=return_library_array( "select id ,po_number from wo_po_break_down where status_active=1 and is_deleted =0 and id in(".$order_id.")",'id','po_number');

	$store=$data_array[0][csf('to_store')];
	$company_id=$data_array[0][csf('to_company')];
	$location=$data_array[0][csf('to_location_id')];

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
	where b.status_active=1 and b.is_deleted=0 and b.company_id = $company_id and b.location_id=$location and b.store_id=$store";
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
		$item_desc=return_field_value("product_name_details","product_details_master","id=$prod_id");
		
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
        <tr id="dtlsTbodyTr_<?php echo $i; ?>">
            <td><input type="text" id="sl_<?php echo $i; ?>" name="sl[]" class="text_boxes"  style="width:20px" value="<?php echo $i; ?>"/></td>
            <td style="display: none;">
            	<input type="text" id="txtOrderNo_<?php echo $i; ?>" name="txtOrderNo[]" class="text_boxes"  style="width:80px"  value="<? echo $order_name_arr[$row[csf('to_order_id')]]; ?>" readonly/>
            	<input type="hidden" id="txtOrderID_<?php echo $i; ?>" name="txtOrderID[]" class="text_boxes" value="<? echo $row[csf('to_order_id')]; ?>" />
            	<input type="hidden" id="fromOrderID_<?php echo $i; ?>" name="fromOrderID[]" class="text_boxes" value="<? echo $row[csf('from_order_id')]; ?>" />
            </td>
            <td>
            	<input type="text" id="txtItemDesc_<?php echo $i; ?>" name="txtItemDesc[]" class="text_boxes" style="width:120px;" value="<? echo $item_desc; ?>" readonly />
				<input type="hidden" id="productID_<?php echo $i; ?>" name="productID[]" value="<? echo $row[csf('to_prod_id')]; ?>"  readonly />
				<input type="hidden" id="fromProductID_<?php echo $i; ?>" name="fromProductID[]" value="<? echo $row[csf('from_prod_id')]; ?>"  readonly />
				<input type="hidden" id="colorID_<?php echo $i; ?>" name="colorID[]" value="<? echo $row[csf('color_id')]; ?>"  readonly />
				<input type="hidden" id="txtDtlsID_<?php echo $i; ?>" name="txtDtlsID[]" class="text_boxes" value="<? echo $row[csf('dtls_id')]; ?>" />
            	<input type="hidden" id="txtDtlsAcID_<?php echo $i; ?>" name="txtDtlsAcID[]" class="text_boxes" value="<? echo $row[csf('id')]; ?>" />
                <input type="hidden" id="txtBatchId_<?php echo $i; ?>" name="txtBatchId[]" class="text_boxes" value="<? echo $row[csf('batch_id')]; ?>" />
            	<input type="hidden" id="txtTransID_<?php echo $i; ?>" name="txtTransID[]" class="text_boxes" value="" />
            </td>
            <td id="floor_td_to_<? echo $i;?>">
                <? 
                	echo create_drop_down( "cbo_floor_to_$i", 152,$floor_arr,"", 1, "--Select--",$row[csf('to_floor_id')], "load_drop_down( 'requires/raw_material_transfer_acknowledgement_controller',this.value+'_'+$i+'_'+$company+'_'+$location+'_'+$store, 'load_drop_down_room_to', 'room_td_$i' );reset_room_rack_shelf($i,'cbo_floor_to');",0,"","","","","","","cboFloorTo[]" );
                ?>
            </td>
            <td id="room_td_<? echo $i;?>">
            	<? 
            		echo create_drop_down( "cbo_room_to_$i", 152,$room_arr,"", 1, "--Select--",$row[csf('to_room')], "",0,"","","","","","","cboRoomTo[]" ); ?>
            </td>
            <td id="rack_td_<? echo $i;?>">
            	<? echo create_drop_down( "txt_rack_to_$i", 152,$rack_arr,"", 1, "--Select--",$row[csf('to_rack')], "",0,"","","","","","","txtRackTo[]" ); ?>
            </td>
            <td id="shelf_td_<? echo $i;?>">
            	<? echo create_drop_down( "txt_shelf_to_$i", 152,$self_arr,"", 1, "--Select--",$row[csf('to_shelf')], "",0,"","","","","","","txtShelfTo[]" ); ?>
            </td>
            <td id="bin_td_<? echo $i;?>">
            	<? echo create_drop_down( "cbo_bin_to_$i", 152,$bin_arr,"", 1, "--Select--",$row[csf('to_bin_box')], "",0,"","","","","","","txtBinTo[]" ); ?>
            </td>
            <td>
                <input type="text"  id="txtTransQnty_<?php echo $i; ?>" name="txtTransQnty[]" class="text_boxes_numeric" style="width:70px;" readonly value="<? echo $row[csf('transfer_qnty')]; ?>"  />
                <input type="hidden" name="hiddenTranQnty[]" id="hiddenTranQnty_<?php echo $i; ?>" value="<? echo $row[csf('transfer_qnty')]; ?>" readonly />
                <input type="hidden" name="hiddenTranRate[]" id="hiddenTranRate_<?php echo $i; ?>" value="<? echo $row[csf('rate')]; ?>" readonly />
                <input type="hidden" name="hiddenTranValue[]" id="hiddenTranValue_<?php echo $i; ?>" value="<? echo $row[csf('transfer_value')]; ?>" readonly />
            </td>
            <td>
                <? echo create_drop_down( "cbo_uom", 50, $unit_of_measurement,'', 0, "", $row[csf('uom')], "",1,'',"","","","","","cboUom[]" );?>
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
	/*if(str_replace("'", "", $cbo_transfer_criteria) == 1 || str_replace("'", "", $cbo_transfer_criteria) == 2) $entry_form=112;
	else */
	$entry_form=487;
	// echo "10**".$entry_form;die;
	if ($operation==0)  // Insert Here
	{
		
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$txt_transfer_date="'".change_date_format($txt_transfer_date,'','',1)."'";
		$id = return_next_id_by_sequence("INV_ITEM_TRANS_MST_AC_PK_SEQ", "inv_item_trans_acknowledgement", $con);
		$field_array="id, entry_form, challan_id, company_id, store_id, location_id, transfer_criteria, item_category, acknowledg_date, remarks, inserted_by, insert_date";
		
		$data_array="(".$id.",488,".$challan_id.",".$cbo_company_id_to.",".$cbo_store_name_to.",".$cbo_location_to.",".$cbo_transfer_criteria.",".$cbo_item_category.",".$txt_transfer_date.",'".$txt_remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		$field_array_trans="id, mst_id, transaction_criteria, company_id, prod_id, pi_wo_batch_no, item_category, transaction_type, transaction_date, store_id, floor_id, room, rack, self, bin_box, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, inserted_by, insert_date";

		$field_array_dtls_update="to_store*to_floor_id*to_room*to_rack*to_shelf*to_bin_box*updated_by*update_date";
		//*to_trans_id

		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, order_rate, order_amount, inserted_by, insert_date";
		$field_array_ord_prod="id, company_id, category_id, prod_id, po_breakdown_id, stock_quantity, last_rcv_qnty, avg_rate, stock_amount, inserted_by, insert_date";
		$field_array_ord_prod_update_to="avg_rate*last_rcv_qnty*stock_quantity*stock_amount*updated_by*update_date";

		//$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, order_rate, order_amount, inserted_by, insert_date";
  
		if (is_duplicate_field( "challan_id", "inv_item_trans_acknowledgement", "challan_id=$challan_id" )==1)
		{
			echo "20**Duplicate Challan No is Not Allow";
			disconnect($con);
			die;
		}
 
		
		$dtls_ac_ids=''; $flag=1;
		for($i=1;$i<=$numRow;$i++)
		{
			$txtOrderNo 	="txtOrderNo_".$i;
			$txtOrderID 	="txtOrderID_".$i;
			$fromOrderID 	="fromOrderID_".$i;
			$txtDtlsID 		="txtDtlsID_".$i;
			$txtDtlsAcID 	="txtDtlsAcID_".$i;
			$txtItemDesc 	="txtItemDesc_".$i;
			$productID 	 	="productID_".$i;
			$fromProductID 	="fromProductID_".$i;
			$colorID 		="colorID_".$i;
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
			$dtls_ac_ids.= str_replace("'","",$$txtDtlsAcID).",";

			$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			if($data_array_trans!="") $data_array_trans.=",";

			$data_array_trans .="(".$recv_trans_id.",".$challan_id.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",'".str_replace("'","",$$productID)."','".str_replace("'","",$$txtBatchId)."',".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",'".str_replace("'","",$$cboFloorTo)."','".str_replace("'","",$$cboRoomTo)."','".str_replace("'","",$$txtRackTo)."','".str_replace("'","",$$txtShelfTo)."','".str_replace("'","",$$txtBinTo)."','".str_replace("'","",$$txtOrderID)."','".str_replace("'","",$$cbo_uom)."','".str_replace("'","",$$hiddenTranQnty)."','".str_replace("'","",$$hiddenTranRate)."','".str_replace("'","",$$hiddenTranValue)."','".str_replace("'","",$$hiddenTranQnty)."','".str_replace("'","",$$hiddenTranValue)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$conversion_factor=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b","a.item_group_id=b.id and a.item_category_id=101 and a.entry_form=334 and a.id='".str_replace("'","",$$productID)."' ","conversion_factor");
			// ========================================================================================
			// Proportion and order wise stock end
			$order_trans_qnty=(str_replace("'","",$$hiddenTranQnty)/$conversion_factor);
			if($order_trans_qnty=="") $order_trans_qnty=0;
			
			$row_prod_order=sql_select( "select id, stock_quantity, avg_rate, stock_amount from order_wise_stock where prod_id='".str_replace("'","",$$fromProductID)."' and po_breakdown_id='".str_replace("'","",$$fromOrderID)."' and status_active=1 and is_deleted=0" );		
			$avg_rate=$row_prod_order[0][csf("avg_rate")];
			
			unset($row_prod_order);
			

			// $$txtOrderID == to order id
			$id_prop=return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$row_prod_order_to=sql_select( "select id, stock_quantity, avg_rate, stock_amount from order_wise_stock where prod_id='".str_replace("'","",$$productID)."' and po_breakdown_id='".str_replace("'","",$$txtOrderID)."' and status_active=1 and is_deleted=0" );
			/*$ord_prod_id_arr_to=array();
			if(count($row_prod_order_to)>0)
			{
				$item_order_id_to=$row_prod_order_to[0][csf("id")];
				$avg_rate_to=$row_prod_order_to[0][csf("avg_rate")];
				$order_amount_to=str_replace("'","",$order_trans_qnty)*$avg_rate_to;
				$curr_ord_stock_qnty=$row_prod_order_to[0][csf('stock_quantity')]+str_replace("'","",$order_trans_qnty);
				$curr_ord_stock_value=$row_prod_order_to[0][csf('stock_amount')]+$order_amount_to;
				$avg_ord_rate=number_format($curr_ord_stock_value/$curr_ord_stock_qnty,$dec_place[3],'.','');

				if($data_array_prop!="") $data_array_prop.=",";
				$data_array_prop.="(".$id_prop.",".$recv_trans_id.",5,$entry_form,'".str_replace("'","",$$txtDtlsID)."','".str_replace("'","",$$txtOrderID)."','".str_replace("'","",$$productID)."','".str_replace("'","",$$colorID)."','".str_replace("'","",$order_trans_qnty)."','".$avg_rate_to."','".$order_amount_to."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
										
				$ord_prod_id_arr_to[]=$item_order_id_to; // update order wise stock table
				$data_array_ord_prod_update_to[$item_order_id_to]=explode("*",("".$avg_ord_rate."*'".str_replace("'","",$order_trans_qnty)."'*".$curr_ord_stock_qnty."*".$curr_ord_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
			}
			else
			{
				$avg_rate_to=$avg_rate;
				$order_amount_to=str_replace("'","",$order_trans_qnty)*$avg_rate_to;

				if($data_array_prop!="") $data_array_prop.=",";
				$data_array_prop.="(".$id_prop.",".$recv_trans_id.",5,$entry_form,'".str_replace("'","",$$txtDtlsID)."','".str_replace("'","",$$txtOrderID)."','".str_replace("'","",$$productID)."','".str_replace("'","",$$colorID)."','".str_replace("'","",$order_trans_qnty)."','".$avg_rate_to."','".$order_amount_to."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";			
				
				$ord_prod_id = return_next_id_by_sequence("ORDER_WISE_STOCK_PK_SEQ", "order_wise_stock", $con);
				if($data_array_ord_prod!="") $data_array_ord_prod.=","; // insert order wise stock table
				$data_array_ord_prod.="(".$ord_prod_id.",".$cbo_company_id_to.",101,'".str_replace("'","",$$productID)."','".str_replace("'","",$$txtOrderID)."','".str_replace("'","",$order_trans_qnty)."','".str_replace("'","",$order_trans_qnty)."','".$avg_rate_to."','".$order_amount_to."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}*/
			// Proportion and order wise stock end
			// =================================================================== End 
			//$id_arr=array();

			if($$txtDtlsID !='')
			{
				$id_arr[]=str_replace("'","",$$txtDtlsID);
				$data_array_dtls_update[str_replace("'","",$$txtDtlsID)] = explode("*",("'".$cbo_store_name_to."'*".str_replace("'","",$$cboFloorTo)."*".str_replace("'","",$$cboRoomTo)."*".str_replace("'","",$$txtRackTo)."*".str_replace("'","",$$txtShelfTo)."*".str_replace("'","",$$txtBinTo)."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			}//."'*'".$recv_trans_id

			// print_r($id_arr);die;
		
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id=".str_replace("'","",$$productID));
			if(count($data_prod)>0)
			{
				$stock_qnty=$data_prod[0][csf('current_stock')];
				$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];

				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$$hiddenTranQnty);
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;

				$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
				// last_purchased_qnty, current_stock, stock_value
				$data_array_prod_update="'".$avg_rate_per_unit."'*".$$hiddenTranQnty."*".$curr_stock_qnty."*".$stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
				$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$$productID,0);
				if($flag==1)
				{
					if($prod) $flag=1; else $flag=0;
				}
			}
		}
		//echo "10**insert into inv_item_trans_acknowledgement (".$field_array.") values ".$data_array;die;
		$rID = sql_insert("inv_item_trans_acknowledgement", $field_array, $data_array, 0);
		if($rID) $flag=1; else $flag=0;

		$field_array_mst_update = "is_acknowledge";
		$data_array_mst_update  = "1";
		$rID8 = sql_update("inv_item_transfer_mst",$field_array_mst_update, $data_array_mst_update, "id", $challan_id, 0);
		// echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$rID2 = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 0);
		
		if($flag == 1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}

		if(count($data_array_dtls_update)>0) // transfer_dtls table update
		{
			//print_r($id_arr);die;
			//echo "10**".bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ); die;
			$rID3 = execute_query(bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ));
			//echo $rID3;die;
			if($rID3) $flag=1; else $flag=0; 
		} 

		$dtls_ac_ids = chop($dtls_ac_ids,",");
		if($dtls_ac_ids!="")					// transfer_dtls_ac is_acknowledge update
		{
			$field_array_status="updated_by*update_date*is_acknowledge";
			$data_array_status=$user_id."*'".$pc_date_time."'*1";

			$rID4 = sql_multirow_update("inv_item_transfer_dtls_ac",$field_array_status,$data_array_status,"id",$dtls_ac_ids,0);
			
			if($flag == 1)
			{
				if($rID4) $flag=1; else $flag=0; 
			} 
		}
		
		// echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		/*$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1); 
		if($flag==1) 
		{
			if($rID5) $flag=1; else $flag=0;
		}

		if(count($ord_prod_id_arr_to)>0) // order_wise_stock table update
		{
			//echo bulk_update_sql_statement("order_wise_stock","id",$field_array_ord_prod_update_to,$data_array_ord_prod_update_to,$ord_prod_id_arr_to);die;
			$ordProdUpdateTo=execute_query(bulk_update_sql_statement("order_wise_stock","id",$field_array_ord_prod_update_to,$data_array_ord_prod_update_to,$ord_prod_id_arr_to));
			if($flag==1) 
			{
				if($ordProdUpdateTo) $flag=1; else $flag=0; 
			}
		}

		if($data_array_ord_prod!="") // order_wise_stock Insert
		{
			//echo "10**insert into order_wise_stock (".$field_array_ord_prod.") values ".$data_array_ord_prod;die;
			$ordProdInsert=sql_insert("order_wise_stock",$field_array_ord_prod,$data_array_ord_prod,0);
			if($flag==1)
			{
				if($ordProdInsert) $flag=1; else $flag=0; 
			}
		}
        */
		//echo "10**".$rID."**".$rID2."**".$rID3."**".$rID4."**";die;

		if($flag==1)
		{
			oci_commit($con);
			echo "0**".$id;
		}
		else
		{
			oci_rollback($con); 
			echo "5**0";
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

		// $duplicate = is_duplicate_field("b.id","inv_receive_master a, inv_transaction b","a.id=b.mst_id and a.id=$hidden_mrr_id and b.prod_id=$current_prod_id and b.transaction_type=1  and a.status_active=1 and b.status_active=1");
		// if($duplicate==1)
		// {
		// 	echo "20**Duplicate Product is Not Allow in Same MRR Number.";
		// 	// check_table_status( $_SESSION['menu_id'],0);
		// 	disconnect($con);
		// 	die;
		// }

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
	/*$data_array=sql_select("SELECT a.id, a.challan_id, a.acknowledg_date, a.remarks, b.id as dtls_id, b.mst_id, b.from_store, b.to_store, b.floor_id, b.room, b.rack, b.shelf, b.bin_box, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.rate, b.transfer_value, b.color_id, b.uom, d.from_order_id, d.to_order_id, c.trans_id, b.batch_id ,a.company_id as company_id, a.location_id as to_location_id
	from inv_item_trans_acknowledgement a, inv_item_transfer_dtls b, order_wise_pro_details c, inv_item_transfer_mst d 
	where a.id='$data' and b.id=c.dtls_id and a.challan_id=b.mst_id and d.id=b.mst_id and a.entry_form=488 and c.trans_type=5 and c.entry_form in(487) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");*/
	$data_array=sql_select("SELECT a.id, a.challan_id, a.acknowledg_date, a.remarks, b.id as dtls_id, b.mst_id, b.from_store, b.to_store, b.floor_id, b.room, b.rack, b.shelf, b.bin_box, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.rate, b.transfer_value, b.color_id, b.uom, d.from_order_id, d.to_order_id, b.batch_id ,a.company_id as company_id, a.location_id as to_location_id
	from inv_item_trans_acknowledgement a, inv_item_transfer_dtls b, inv_item_transfer_mst d 
	where a.id='$data' and a.challan_id=b.mst_id and d.id=b.mst_id and a.entry_form=488 and d.entry_form=487 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
	
	/*foreach($data_array as $row)
	{
		$order_ids.=$row[csf('to_order_id')].",";
		$order_id=chop($order_ids,",");
	}

	$order_name_arr=return_library_array( "select id ,po_number from wo_po_break_down where status_active=1 and is_deleted =0 and id in(".$order_id.")",'id','po_number');
*/
	$store=$data_array[0][csf('to_store')];
	$company_id=$data_array[0][csf('company_id')];
	$location=$data_array[0][csf('to_location_id')];


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
	where b.status_active=1 and b.is_deleted=0 and b.company_id = $company_id and b.location_id=$location and b.store_id=$store";
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
        <tr id="dtlsTbodyTr_<?php echo $i; ?>">
            <td><input type="text" id="sl_<?php echo $i; ?>" name="sl[]" class="text_boxes"  style="width:20px" value="<?php echo $i; ?>"/></td>
            <td style="display: none;">
            	<input type="text" id="txtOrderNo_<?php echo $i; ?>" name="txtOrderNo[]" class="text_boxes"  style="width:80px" value="<? echo $order_name_arr[$row[csf('to_order_id')]]; ?>" readonly />
            	<input type="hidden" id="txtOrderID_<?php echo $i; ?>" name="txtOrderID[]" class="text_boxes" value="<? echo $row[csf('to_order_id')]; ?>" />
            	<input type="hidden" id="fromOrderID_<?php echo $i; ?>" name="fromOrderID[]" class="text_boxes" value="<? echo $row[csf('from_order_id')]; ?>" />
            	
            </td>
            <td>
            	<input type="text" id="txtItemDesc_<?php echo $i; ?>" name="txtItemDesc[]" class="text_boxes" style="width:120px;" value="<? echo $item_desc; ?>" readonly />
				<input type="hidden" id="productID_<?php echo $i; ?>" name="productID[]" value="<? echo $row[csf('to_prod_id')]; ?>"  readonly />
				<input type="hidden" id="fromProductID_<?php echo $i; ?>" name="fromProductID[]" value="<? echo $row[csf('from_prod_id')]; ?>"  readonly />
				<input type="hidden" id="colorID_<?php echo $i; ?>" name="colorID[]" value=""  readonly />
				<input type="hidden" id="txtDtlsID_<?php echo $i; ?>" name="txtDtlsID[]" class="text_boxes" value="<? echo $row[csf('dtls_id')]; ?>" />
            	<input type="hidden" id="txtDtlsAcID_<?php echo $i; ?>" name="txtDtlsAcID[]" class="text_boxes" value="" />
            	<input type="hidden" id="txtTransID_<?php echo $i; ?>" name="txtTransID[]" class="text_boxes" value="<? echo $row[csf('trans_id')]; ?>" />
                <input type="hidden" id="txtBatchId_<?php echo $i; ?>" name="txtBatchId[]" class="text_boxes" value="<? echo $row[csf('batch_id')]; ?>" />
            </td>
            <td id="floor_td_to_<? echo $i;?>">
                <? 
                	echo create_drop_down( "cbo_floor_to_$i", 152,$floor_arr,"", 1, "--Select--",$row[csf('to_floor_id')], "load_drop_down( 'requires/raw_material_transfer_acknowledgement_controller',this.value+'_'+$i+'_'+$company+'_'+$location+'_'+$store, 'load_drop_down_room_to', 'room_td_$i' );reset_room_rack_shelf($i,'cbo_floor_to');",0,"","","","","","","cboFloorTo[]" );
                ?>
            </td>
            <td id="room_td_<? echo $i;?>">
            	<? 
            		echo create_drop_down( "cbo_room_to_$i", 152,$room_arr,"", 1, "--Select--",$row[csf('to_room')], "",0,"","","","","","","cboRoomTo[]" ); ?>
            </td>
            <td id="rack_td_<? echo $i;?>">
            	<? echo create_drop_down( "txt_rack_to_$i", 152,$rack_arr,"", 1, "--Select--",$row[csf('to_rack')], "",0,"","","","","","","txtRackTo[]" ); ?>
            </td>
            <td id="shelf_td_<? echo $i;?>">
            	<? echo create_drop_down( "txt_shelf_to_$i", 152,$self_arr,"", 1, "--Select--",$row[csf('to_shelf')], "",0,"","","","","","","txtShelfTo[]" ); ?>
            </td>
            <td id="bin_td_<? echo $i;?>">
            	<? echo create_drop_down( "cbo_bin_to_$i", 152,$bin_arr,"", 1, "--Select--",$row[csf('to_bin_box')], "",0,"","","","","","","txtBinTo[]" ); ?>
            </td>
            <td>
                <input type="text"  id="txtTransQnty_<?php echo $i; ?>" name="txtTransQnty[]" class="text_boxes_numeric" style="width:70px;" readonly value="<? echo $row[csf('transfer_qnty')]; ?>"  />
                <input type="hidden" name="hiddenTranQnty[]" id="hiddenTranQnty_<?php echo $i; ?>" value="<? echo $row[csf('transfer_qnty')]; ?>" readonly />
                <input type="hidden" name="hiddenTranRate[]" id="hiddenTranRate_<?php echo $i; ?>" value="<? echo $row[csf('rate')]; ?>" readonly />
                <input type="hidden" name="hiddenTranValue[]" id="hiddenTranValue_<?php echo $i; ?>" value="<? echo $row[csf('transfer_value')]; ?>" readonly />
            </td>
            <td>
                <? echo create_drop_down( "cbo_uom", 50, $unit_of_measurement,'', 0, "", $row[csf('uom')], "",1,"","","","","","","cboUom[]" );?>
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
				load_drop_down('raw_material_transfer_acknowledgement_controller',cbo_company_id_to, 'load_drop_down_location_to_up', 'to_location_td' );
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
                                echo create_drop_down("cbo_transfer_criteria", 120,$item_transfer_criteria,"", 1,"-- Select --",'2',1,'','1,2,4');
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
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_transfer_criteria').value+'_'+document.getElementById('cbo_location_to').value+'_'+document.getElementById('cbo_store_name_to').value+'_'+document.getElementById('txt_system_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id_to; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_transfer_search_list_view', 'search_div', 'raw_material_transfer_acknowledgement_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$year=$data[7];

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

	if($db_type==0){$sql_cond .=" and year(a.insert_date)=".$year."";}
	else{$sql_cond .=" and to_char(a.insert_date,'YYYY')=".$year."";}
 	
 	$sql="select a.id, $year_field as year, a.entry_form, a.challan_id, a.company_id, a.store_id, a.location_id, a.transfer_criteria, a.item_category, a.acknowledg_date, a.remarks, b.transfer_system_id from inv_item_trans_acknowledgement a, inv_item_transfer_mst b where a.challan_id=b.id and a.entry_form=488 and a.transfer_criteria in(1,2,4) $company_cond $criteria_cond $location_cond $store_cond $system_id_cond $acknowledg_date_cond $sql_cond and a.status_active=1 and a.is_deleted=0";
	
	$store_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$location_arr=return_library_array( "select id, location_name from  lib_location", "id", "location_name"  );
	//print_r($location_arr);
	$arr=array(2=>$location_arr,3=>$store_arr,6=>$item_transfer_criteria);
	//print_r($item_transfer_criteria);
	?>
	<table cellspacing="0" cellpadding="0" width="870" class="rpt_table" rules="all" border="1">
	<thead>
		<tr>
			<th align="canter" ><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
		</tr>
	</head>
	</table>
	<?
	echo  create_list_view("tbl_list_search", "System ID, Year, Location, Store, Challan No, Transfer Date, Transfer Criteria", "70,60,130,150,150,80","860","250",0, $sql, "js_set_value", "id", "", 1, "0,0,location_id,store_id,0,0,transfer_criteria", $arr, "id,year,location_id,store_id,transfer_system_id,acknowledg_date,transfer_criteria", '','','0,0,0,0,0,3,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master_update')
{
	$data_array=sql_select("SELECT a.id, a.entry_form, a.challan_id, a.company_id, a.store_id, a.location_id, a.transfer_criteria, a.item_category, a.acknowledg_date, a.remarks, b.transfer_system_id from inv_item_trans_acknowledgement a, inv_item_transfer_mst b where a.id='$data' and a.challan_id=b.id and a.entry_form=488 and a.transfer_criteria in(1,2,4) and a.status_active=1 and a.is_deleted=0");
	foreach ($data_array as $row)
	{ 
		echo "load_drop_down('requires/raw_material_transfer_acknowledgement_controller','".$row[csf("company_id")]."', 'load_drop_down_location_to', 'to_location_td' );\n";
		echo "document.getElementById('cbo_location_to').value 				= '".$row[csf("location_id")]."';\n";

		if($row[csf("store_id")]>0){
			$loc_com=$row[csf("location_id")]."_".$row[csf("company_id")];
			echo "load_drop_down('requires/raw_material_transfer_acknowledgement_controller','".$loc_com."', 'load_drop_down_store_to', 'to_store_td' );\n";
			// echo "load_room_rack_self_bin('requires/raw_material_transfer_acknowledgement_controller*4*cbo_store_name_to', 'store','to_store_td', '".$row[csf("company_id")]."','".$row[csf("location_id")]."',this.value);\n";
			echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("store_id")]."';\n";
		}
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('challan_id').value 					= '".$row[csf("challan_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("acknowledg_date")])."';\n";
		//echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("company_id")]."';\n";		
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";
		echo "$('#cbo_location_to').attr('disabled','disabled');\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		
		//echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_transfer_acknowledgement',1,1);\n"; 
		 
		exit();
		
	}
}

?>