<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

$user_store_ids = $_SESSION['logic_erp']['store_location_id'];
$user_supplier_ids = $_SESSION['logic_erp']['supplier_id'];
$user_comp_location_ids = $_SESSION['logic_erp']['company_location_id'];

//======================= Load Buyer ===================


if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
	exit();
}

if ($action == "report_setting_button") {
	$buttonIdArr = ['78#Print1', '66#print2'];
	$print_report_format_arr = get_report_button_array($data, 6, 313, $user_id, $buttonIdArr);
}

// ==============Start Floor Room Rack Shelf Bin upto variable Settings============
if ($action == "upto_variable_settings") {
	extract($_REQUEST);
	echo $variable_inventory = return_field_value("store_method", "variable_settings_inventory", "company_name='$cbo_company_id' and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	exit();
}
// ==============End Floor Room Rack Shelf Bin upto variable Settings==============

if ($action == "varriable_setting_auto_receive") {
	extract($_REQUEST);
	echo $variable_auto_transfer = return_field_value("auto_transfer_rcv", "variable_settings_inventory", "company_name='$cbo_company_id_to' and variable_list=27 and item_category_id=1 and status_active=1 and is_deleted=0");
	exit();
}

if ($action == "load_room_rack_self_bin") {
	$explodeData = explode('*', $data);
	$explodeData[11] = 'storeUpdateUptoDisable()';
	$data = implode('*', $explodeData);
	load_room_rack_self_bin("requires/yarn_transfer_controller", $data);
}

if ($action == "itemDescription_popup") {
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>

	<script>
		/*$(document).ready(function(e) {
		setFilterGrid('tbl_list_search',-1);
	});*/

		function js_set_value(data) {
			$('#product_id').val(data);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:920px;">
			<form name="searchdescfrm" id="searchdescfrm">
				<fieldset style="width:910px;margin-left:10px">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="600" class="rpt_table">
						<thead>
							<th>Search By</th>
							<th width="280" id="search_by_td_up">Please Enter Item Details</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
								<input type="hidden" name="product_id" id="product_id" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								$search_by_arr = array(1 => "Item Details", 2 => "Lot No.");
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../../') ";
								echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "2", $dd, 0);
								?>
							</td>
							<td id="search_by_td">
								<input type="text" style="width:130px;" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_store_name; ?>, 'create_product_search_list_view', 'search_div', 'yarn_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
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

if ($action == 'create_product_search_list_view') {
	$data = explode("_", $data);
	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$store_id = $data[3];

	if ($search_by == 1)
		$search_field = "a.product_name_details";
	else
		$search_field = "a.lot";

	$rack_variable = return_field_value("store_method", "variable_settings_inventory", "company_name='$company_id' and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");

	//$store_method_upto = ( $rack_variable=="" || $rack_variable<2 )?"":", b.floor_id, b.room, b.rack, b.self, b.bin_box";

	if ($rack_variable == "" || $rack_variable < 2) {
		$store_method_upto = ", b.store_id";
	} else {
		if ($rack_variable == 2) {
			$store_method_upto = ", b.store_id,b.floor_id";
		}
		if ($rack_variable == 3) {
			$store_method_upto = " , b.store_id,b.floor_id,b.room";
		}
		if ($rack_variable == 4) {
			$store_method_upto = " , b.store_id,b.floor_id,b.room,b.rack";
		}
		if ($rack_variable == 5) {
			$store_method_upto = " , b.store_id,b.floor_id,b.room,b.rack,b.self";
		}
		if ($rack_variable == 6) {
			$store_method_upto = " , b.store_id,b.floor_id,b.room,b.rack,b.self,b.bin_box";
		}
	}

	$sql = "SELECT a.id, a.company_id, a.supplier_id, a.product_name_details, a.lot, a.current_stock, a.brand, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as balance_quantity $store_method_upto from product_details_master a, inv_transaction b where a.id=b.prod_id and a.item_category_id=1 and a.company_id=$company_id and b.store_id=$store_id and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.company_id, a.supplier_id, a.product_name_details, a.lot, a.current_stock, a.brand $store_method_upto";

	//echo $sql;

	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$store_name_arr = return_library_array("select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company_id and b.category_type in(1) and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name", "id", "store_name");
	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where company_id=$company_id and status_active=1 and is_deleted=0 group by floor_room_rack_id, floor_room_rack_name order by floor_room_rack_name", "floor_room_rack_id", "floor_room_rack_name");

	//$arr=array(1=>$company_arr,2=>$supplier_arr,5=>$brand_arr,6=>$store_name_arr,7=>$floor_room_rack_arr,8=>$floor_room_rack_arr,9=>$floor_room_rack_arr,10=>$floor_room_rack_arr,11=>$floor_room_rack_arr);

	//echo create_list_view("tbl_list_search", "Item ID,Company,Supplier,Item Details,Lot No,Brand,Store,Floor,Room,Rack,Self,Bin/Box,Stock", "80,120,130,180,90,80,80,80,80,80,80,100","1380","250",0, $sql, "js_set_value", "id,store_id,floor_id,room,rack,self,bin_box", "", 1, "0,company_id,supplier_id,0,0,brand,store_id,floor_id,room,rack,self,bin_box", $arr, "id,company_id,supplier_id,product_name_details,lot,brand,store_id,floor_id,room,rack,self,bin_box,balance_quantity", '','','0,0,0,0,0,0,0,0,0,0,0,0,2');

?>
	<div>
		<table class="rpt_table" id="rpt_tabletbl_list_search" rules="all" width="1390" cellspacing="0" cellpadding="0" border="0">
			<thead>
				<tr>
					<th width="50">SL No</th>
					<th width="80">Item ID</th>
					<th width="120">Company</th>
					<th width="130">Supplier</th>
					<th width="180">Item Details</th>
					<th width="90">Lot No</th>
					<th width="80">Brand</th>
					<th width="80">Store</th>
					<th width="80">Floor</th>
					<th width="80">Room</th>
					<th width="80">Rack</th>
					<th width="80">Self</th>
					<th width="100">Bin/Box</th>
					<th>Stock</th>
				</tr>
			</thead>
		</table>
		<div style="max-height:250px; width:1388px; overflow-y:scroll" id="">
			<table class="rpt_table" id="tbl_list_search" rules="all" width="1368" height="" cellspacing="0" cellpadding="0" border="0">
				<tbody>
					<?php

					$result = sql_select($sql);
					$i = 1;
					foreach ($result as $row) {
						if ($row[csf('balance_quantity')] > 0) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$jset_value = "'" . $row[csf('id')] . "_" . $row[csf('store_id')] . "_" . $row[csf('floor_id')] . "_" . $row[csf('room')] . "_" . $row[csf('rack')] . "_" . $row[csf('self')] . "_" . $row[csf('bin_box')] . "'";

					?>
							<tr onClick="js_set_value(<? echo $jset_value; ?>)" style="cursor:pointer" id="tr_1" height="20" bgcolor="<? echo $bgcolor; ?>">
								<td width="50"><? echo $i; ?></td>
								<td width="80" align="left">
									<p><? echo $row[csf('id')]; ?></p>
								</td>
								<td width="120" align="left">
									<p><? echo $company_arr[$row[csf('company_id')]]; ?></p>
								</td>
								<td width="130" align="left">
									<p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p>
								</td>
								<td width="180" align="left">
									<p><? echo $row[csf('product_name_details')]; ?></p>
								</td>
								<td width="90" align="left">
									<p><? echo $row[csf('lot')]; ?></p>
								</td>
								<td width="80" align="left">
									<p><? echo $brand_arr[$row[csf('brand')]]; ?></p>
								</td>
								<td width="80" align="left">
									<p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p>
								</td>
								<td width="80" align="left">
									<p><? echo $floor_room_rack_arr[$row[csf('floor_id')]]; ?></p>
								</td>
								<td width="80" align="left">
									<p><? echo $floor_room_rack_arr[$row[csf('room')]]; ?></p>
								</td>
								<td width="80" align="left">
									<p><? echo $floor_room_rack_arr[$row[csf('rack')]]; ?></p>
								</td>
								<td width="80" align="left">
									<p><? echo $floor_room_rack_arr[$row[csf('self')]]; ?></p>
								</td>
								<td width="100" align="left">
									<p><? echo $floor_room_rack_arr[$row[csf('bin_box')]]; ?></p>
								</td>
								<td align="right">
									<p><? echo number_format($row[csf('balance_quantity')], 2, ".", ""); ?></p>
								</td>
							</tr>
					<?
							$i++;
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
<?
	exit();
}

if ($action == 'populate_data_from_product_master') {
	$data_pro_ref = explode("_", $data);
	$prod_id = $data_pro_ref[0];
	$store_id = $data_pro_ref[1];
	$floor_id = $data_pro_ref[2];
	$room = $data_pro_ref[3];
	$rack = $data_pro_ref[4];
	$self = $data_pro_ref[5];
	$bin = $data_pro_ref[6];
	$company_id = $data_pro_ref[7];

	//echo "<pre>";
	//print_r($data_pro_ref);die;

	$sqlCon = "";
	if ($floor_id != "") {
		$sqlCon = " and b.floor_id=$floor_id";
	}
	if ($room != "") {
		$sqlCon .= " and b.room=$room";
	}
	if ($rack != "") {
		$sqlCon .= " and b.rack=$rack";
	}
	if ($self != "") {
		$sqlCon .= " and b.self=$self";
	}
	if ($bin != "") {
		$sqlCon .= " and b.bin_box=$bin";
	}

	// echo $sqlCon;die;
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

	$rack_variable = return_field_value("store_method", "variable_settings_inventory", "company_name='$company_id' and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");

	if ($rack_variable == "" || $rack_variable < 2) {
		$store_method_upto = ", b.store_id";
	} else {
		if ($rack_variable == 2) {
			$store_method_upto = ", b.store_id,b.floor_id";
		}
		if ($rack_variable == 3) {
			$store_method_upto = " , b.store_id,b.floor_id,b.room";
		}
		if ($rack_variable == 4) {
			$store_method_upto = " , b.store_id,b.floor_id,b.room,b.rack";
		}
		if ($rack_variable == 5) {
			$store_method_upto = " , b.store_id,b.floor_id,b.room,b.rack,b.self";
		}
		if ($rack_variable == 6) {
			$store_method_upto = " , b.store_id,b.floor_id,b.room,b.rack,b.self,b.bin_box";
		}
	}

	$sql = "SELECT a.product_name_details, a.lot, a.current_stock, a.avg_rate_per_unit, a.brand, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as balance_quantity, b.company_id $store_method_upto from product_details_master a, inv_transaction b where a.id=b.prod_id and a.item_category_id=1 and a.company_id=$company_id and a.id=$prod_id and b.store_id=$store_id and a.status_active=1 and b.status_active=1 $sqlCon group by a.product_name_details, a.lot, a.current_stock, a.avg_rate_per_unit, a.brand, b.company_id $store_method_upto";

	$data_array = sql_select($sql);

	foreach ($data_array as $row) {
		echo "document.getElementById('hidden_product_id').value 			= '" . $prod_id . "';\n";
		echo "document.getElementById('txt_item_desc').value 				= '" . $row[csf("product_name_details")] . "';\n";
		echo "document.getElementById('txt_yarn_lot').value 				= '" . $row[csf("lot")] . "';\n";
		echo "document.getElementById('txt_current_stock').value 			= '" . number_format($row[csf("current_stock")], 2, ".", "") . "';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '" . number_format($row[csf("current_stock")], 2, ".", "") . "';\n";
		echo "document.getElementById('txt_rate').value 					= '" . $row[csf("avg_rate_per_unit")] . "';\n";
		echo "document.getElementById('hide_brand_id').value 				= '" . $row[csf("brand")] . "';\n";
		echo "document.getElementById('txt_yarn_brand').value 				= '" . $brand_arr[$row[csf("brand")]] . "';\n";

		/*echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'store','from_store_td', '".$row[csf("company_id")]."');\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";*/

		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'floor','floor_td', '" . $row[csf("company_id")] . "','" . "','" . $row[csf("store_id")] . "',this.value);\n";
		echo "document.getElementById('cbo_floor').value 					= '" . $row[csf("floor_id")] . "';\n";

		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'room','room_td', '" . $row[csf("company_id")] . "','" . "','" . $row[csf("store_id")] . "','" . $row[csf("floor_id")] . "',this.value);\n";
		echo "document.getElementById('cbo_room').value 					= '" . $row[csf("room")] . "';\n";

		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'rack','rack_td', '" . $row[csf("company_id")] . "','" . "','" . $row[csf("store_id")] . "','" . $row[csf("floor_id")] . "','" . $row[csf("room")] . "',this.value);\n";
		echo "document.getElementById('txt_rack').value 					= '" . $row[csf("rack")] . "';\n";

		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'shelf','shelf_td', '" . $row[csf("company_id")] . "','" . "','" . $row[csf("store_id")] . "','" . $row[csf("floor_id")] . "','" . $row[csf("room")] . "','" . $row[csf("rack")] . "',this.value);\n";
		echo "document.getElementById('txt_shelf').value 					= '" . $row[csf("self")] . "';\n";

		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'bin','bin_td', '" . $row[csf("company_id")] . "','" . "','" . $row[csf("store_id")] . "','" . $row[csf("floor_id")] . "','" . $row[csf("room")] . "','" . $row[csf("rack")] . "','" . $row[csf("self")] . "',this.value);\n";
		echo "document.getElementById('cbo_bin').value 						= '" . $row[csf("bin_box")] . "';\n";


		exit();
	}
}

if ($action == "itemTransfer_popup") {
	echo load_html_head_contents("Item Transfer Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>

	<script>
		function js_set_value(data) {
			var data_arr = data.split("_");
			$('#transfer_id').val(data_arr[0]);
			$('#is_posted_account').val(data_arr[1]);
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:770px;">
			<form name="searchdescfrm" id="searchdescfrm">
				<fieldset style="width:760px;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
						<thead>
							<th width="200">Search By</th>
							<th width="200" id="search_by_td_up">Please Enter Transfer ID</th>
							<th width="220">Transfer Date Range</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
								<input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
								<input type="hidden" name="is_posted_account" id="is_posted_account" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td align="center">
								<?
								$search_by_arr = array(1 => "Transfer ID", 2 => "Challan No.");
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../../') ";
								echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td id="search_by_td" align="center">
								<input type="text" style="width:130px;" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;" placeholder="From Date" readonly />&nbsp; TO &nbsp;
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:80px;" readonly />
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_transfer_search_list_view', 'search_div', 'yarn_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="4" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
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

if ($action == 'create_transfer_search_list_view') {
	$data = explode("_", $data);
	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$date_form = $data[3];
	$date_to = $data[4];
	$year_selection = $data[5];
	$transfer_criteria = $data[6];
	if ($db_type == 0) {
		$date_form = change_date_format($date_form, 'yyyy-mm-dd');
		$date_to = change_date_format($date_to, 'yyyy-mm-dd');
	} else {
		$date_form = change_date_format($date_form, '', '', 1);
		$date_to = change_date_format($date_to, '', '', 1);
	}
	//echo $date_form."==".$date_to;die;

	if ($search_by == 1)
		$search_field = "transfer_system_id";
	else
		$search_field = "challan_no";

	if ($db_type == 0) $year_field = "YEAR(insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(insert_date,'YYYY') as year,";
	else $year_field = ""; //defined Later
	$date_cond = "";
	if ($date_form != "" && $date_to) {
		$date_cond = " and transfer_date between '$date_form' and '$date_to' ";
	}
	if ($year != "") {
		$year_cond = "and to_char(transfer_date,'YYYY')=$year_selection"; //data show only year wise, just apply query condition
	}

	$sql = "select id, $year_field transfer_prefix_number, transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category, is_posted_account from inv_item_transfer_mst where item_category=1 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria in($transfer_criteria) and status_active=1 and is_deleted=0 $date_cond order by id";

	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$arr = array(3 => $company_arr, 5 => $item_transfer_criteria, 6 => $item_category);

	echo create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "70,60,100,120,90,140", "750", "250", 0, $sql, "js_set_value", "id,is_posted_account", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '', '', '0,0,0,0,3,0,0');

	exit();
}

if ($action == 'populate_data_from_transfer_master') {
	$data_array = sql_select("select transfer_system_id,ready_to_approved, challan_no, company_id, transfer_date, transfer_criteria, item_category, to_company, remarks, purpose from inv_item_transfer_mst where id='$data'");

	$company_id = $data_array[0][csf("company_id")];
	$variable_inventory_sql = sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method = $variable_inventory_sql[0][csf("store_method")];

	$to_company = $data_array[0][csf("to_company")];
	if ($transfer_criteria = $data_array[0][csf("transfer_criteria")] != 1) {
		$to_company = $data_array[0][csf("company_id")];
	}

	$variable_inventory_sql_to = sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$to_company and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method_to = $variable_inventory_sql_to[0][csf("store_method")];

	foreach ($data_array as $row) {
		echo "document.getElementById('update_id').value 					= '" . $data . "';\n";
		echo "document.getElementById('txt_system_id').value 				= '" . $row[csf("transfer_system_id")] . "';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '" . $row[csf("transfer_criteria")] . "';\n";

		echo "active_inactive(" . $row[csf("transfer_criteria")] . ");\n";

		echo "document.getElementById('cbo_company_id').value 				= '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_approved').value 				= '" . $row[csf("ready_to_approved")] . "';\n";
		echo "document.getElementById('txt_challan_no').value 				= '" . $row[csf("challan_no")] . "';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '" . change_date_format($row[csf("transfer_date")]) . "';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '" . $row[csf("to_company")] . "';\n";
		echo "document.getElementById('store_update_upto').value 			= '" . $store_method . "';\n";
		echo "document.getElementById('store_update_upto_to').value 		= '" . $store_method_to . "';\n";
		echo "document.getElementById('cbo_item_category').value 			= '" . $row[csf("item_category")] . "';\n";
		echo "document.getElementById('txt_remarks').value 					= '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('cbo_purpose').value 					= '" . $row[csf("purpose")] . "';\n";

		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";
		echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_yarn_transfer_entry',1,1);\n";

		exit();
	}
}

if ($action == "show_transfer_listview") {
	$product_des_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=1", "id", "product_name_details");
	$product_arr = return_library_array("select id, lot from product_details_master where item_category_id=1", "id", "product_name_details");
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$sql = "select id, from_store, to_store, from_prod_id, transfer_qnty, yarn_lot, brand_id from inv_item_transfer_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	$arr = array(0 => $store_arr, 1 => $store_arr, 2 => $product_des_arr, 3 => $product_arr, 5 => $brand_arr);

	echo create_list_view("list_view", "From Store,To Store,Item Description,Yarn Lot,Transfered Qnty,Yarn Brand", "130,130,220,100,110", "880", "200", 0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "from_store,to_store,from_prod_id,from_prod_id,0,brand_id", $arr, "from_store,to_store,from_prod_id,yarn_lot,transfer_qnty,brand_id", "requires/yarn_transfer_controller", '', '0,0,0,0,2,0');

	exit();
}

if ($action == 'populate_transfer_details_form_data') {
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$data_array = sql_select("select a.transfer_criteria, a.company_id,a.to_company,b.id, b.mst_id, b.from_store, b.to_store, b.from_prod_id,b.floor_id,b.room,b.rack,b.shelf,bin_box,b.to_floor_id,b.to_room,b.to_rack,b.to_shelf,b.to_bin_box, b.to_prod_id, b.transfer_qnty, b.rate, b.transfer_value, b.yarn_lot, b.brand_id, b.no_of_bag, b.no_of_cone, b.weight_per_bag, b.fso_no, b.buyer_id from inv_item_transfer_mst a,inv_item_transfer_dtls b where b.id='$data' and a.id=b.mst_id");
	foreach ($data_array as $row) {
		if ($row[csf("transfer_criteria")] == 1) {
			$company_id = $row[csf("to_company")];
		} else {
			$company_id = $row[csf("company_id")];
		}
		//echo $row[csf("from_store")].'===';
		$from_bin_box = (str_replace("'", "", $row[csf("bin_box")]) == "") ? 0 : $row[csf("bin_box")];
		$to_bin_box = (str_replace("'", "", $row[csf("to_bin_box")]) == "") ? 0 : $row[csf("to_bin_box")];

		echo "reset_form('','','cbo_store_name*cbo_store_name_to*txt_yarn_lot*txt_transfer_qnty*txt_rate*txt_transfer_value*hide_brand_id*txt_yarn_brand*hidden_transfer_qnty*hidden_product_id*previous_from_prod_id*previous_to_prod_id*txt_item_desc*txt_current_stock*txt_btb_selection*txt_btb_lc_id*hidden_current_stock*origin_product_id*update_trans_issue_id*update_trans_recv_id','');\n";

		echo "document.getElementById('update_dtls_id').value 				= '" . $row[csf("id")] . "';\n";

		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'store','from_store_td', $('#cbo_company_id').val(),'" . "',this.value);\n";
		echo "document.getElementById('cbo_store_name').value 				= '" . $row[csf("from_store")] . "';\n";
		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'floor','floor_td', $('#cbo_company_id').val(),'" . "','" . $row[csf('from_store')] . "',this.value);\n";
		echo "document.getElementById('cbo_floor').value 				= '" . $row[csf("floor_id")] . "';\n";
		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'room','room_td', $('#cbo_company_id').val(),'" . "','" . $row[csf('from_store')] . "','" . $row[csf('floor_id')] . "',this.value);\n";
		echo "document.getElementById('cbo_room').value 				= '" . $row[csf("room")] . "';\n";
		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'rack','rack_td', $('#cbo_company_id').val(),'" . "','" . $row[csf('from_store')] . "','" . $row[csf('floor_id')] . "','" . $row[csf('room')] . "',this.value);\n";
		echo "document.getElementById('txt_rack').value 				= '" . $row[csf("rack")] . "';\n";
		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'shelf','shelf_td', $('#cbo_company_id').val(),'" . "','" . $row[csf('from_store')] . "','" . $row[csf('floor_id')] . "','" . $row[csf('room')] . "','" . $row[csf('rack')] . "',this.value);\n";
		echo "document.getElementById('txt_shelf').value 				= '" . $row[csf("shelf")] . "';\n";
		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'bin','bin_td', $('#cbo_company_id').val(),'" . "','" . $row[csf('from_store')] . "','" . $row[csf('floor_id')] . "','" . $row[csf('room')] . "','" . $row[csf('rack')] . "','" . $row[csf('shelf')] . "',this.value);\n";
		echo "document.getElementById('cbo_bin').value 					= '" . $from_bin_box . "';\n";


		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1*cbo_store_name_to', 'store','to_store_td', '" . $company_id . "','" . "',this.value);\n";
		echo "document.getElementById('cbo_store_name_to').value 			= '" . $row[csf("to_store")] . "';\n";
		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1*cbo_floor_to', 'floor','floor_td_to', '" . $company_id . "','" . "','" . $row[csf('to_store')] . "',this.value);\n";
		echo "document.getElementById('cbo_floor_to').value 			= '" . $row[csf("to_floor_id")] . "';\n";
		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1*cbo_room_to', 'room','room_td_to', '" . $company_id . "','" . "','" . $row[csf('to_store')] . "','" . $row[csf('to_floor_id')] . "',this.value);\n";
		echo "document.getElementById('cbo_room_to').value 				= '" . $row[csf("to_room")] . "';\n";
		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1*txt_rack_to', 'rack','rack_td_to','" . $company_id . "','" . "','" . $row[csf('to_store')] . "','" . $row[csf('to_floor_id')] . "','" . $row[csf('to_room')] . "',this.value);\n";
		echo "document.getElementById('txt_rack_to').value 				= '" . $row[csf("to_rack")] . "';\n";
		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1*txt_shelf_to', 'shelf','shelf_td_to','" . $company_id . "','" . "','" . $row[csf('to_store')] . "','" . $row[csf('to_floor_id')] . "','" . $row[csf('to_room')] . "','" . $row[csf('to_rack')] . "',this.value);\n";
		echo "document.getElementById('txt_shelf_to').value 			= '" . $row[csf("to_shelf")] . "';\n";

		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'bin','bin_td', '" . $company_id . "','" . "','" . $row[csf('to_store')] . "','" . $row[csf('to_floor_id')] . "','" . $row[csf('to_room')] . "','" . $row[csf('to_rack')] . "','" . $row[csf('to_shelf')] . "',this.value);\n";
		echo "document.getElementById('cbo_bin_to').value 					= '" . $to_bin_box . "';\n";

		echo "storeUpdateUptoDisable();\n";
		echo "disable_enable_fields('cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin',1);\n";

		echo "document.getElementById('txt_yarn_lot').value 				= '" . $row[csf("yarn_lot")] . "';\n";
		echo "document.getElementById('txt_transfer_qnty').value 			= '" . $row[csf("transfer_qnty")] . "';\n";
		echo "document.getElementById('txt_rate').value 					= '" . $row[csf("rate")] . "';\n";
		echo "document.getElementById('txt_transfer_value').value 			= '" . $row[csf("transfer_value")] . "';\n";
		echo "document.getElementById('hide_brand_id').value 				= '" . $row[csf("brand_id")] . "';\n";
		echo "document.getElementById('txt_yarn_brand').value 				= '" . $brand_arr[$row[csf("brand_id")]] . "';\n";
		echo "document.getElementById('hidden_transfer_qnty').value 		= '" . $row[csf("transfer_qnty")] . "';\n";
		echo "document.getElementById('hidden_product_id').value 			= '" . $row[csf("from_prod_id")] . "';\n";
		echo "document.getElementById('previous_from_prod_id').value 		= '" . $row[csf("from_prod_id")] . "';\n";
		echo "document.getElementById('previous_to_prod_id').value 			= '" . $row[csf("to_prod_id")] . "';\n";
		echo "document.getElementById('txt_no_of_bag').value 				= '" . $row[csf("no_of_bag")] . "';\n";
		echo "document.getElementById('txt_no_of_cone').value 				= '" . $row[csf("no_of_cone")] . "';\n";
		echo "document.getElementById('txt_weight_per_bag').value 			= '" . $row[csf("weight_per_bag")] . "';\n";
		echo "document.getElementById('txt_fso_no').value 					= '" . $row[csf("fso_no")] . "';\n";
		echo "document.getElementById('cbo_buyer_name').value 				= '" . $row[csf("buyer_id")] . "';\n";

		$from_prod_id = $row[csf('from_prod_id')];
		$from_store_id = $row[csf('from_store')];

		$comp_id = $row[csf("company_id")];
		$floor_id = $row[csf("floor_id")];
		$room = $row[csf("room")];
		$rack = $row[csf("rack")];
		$self = $row[csf("self")];
		$bin = $row[csf("bin_box")];

		$sqlCon = "";
		if ($floor_id != "") {
			$sqlCon = " and b.floor_id=$floor_id";
		}
		if ($room != "") {
			$sqlCon .= " and b.room=$room";
		}
		if ($rack != "") {
			$sqlCon .= " and b.rack=$rack";
		}
		if ($self != "") {
			$sqlCon .= " and b.self=$self";
		}
		if ($bin != "") {
			$sqlCon .= " and b.bin_box=$bin";
		}

		$rack_variable = return_field_value("store_method", "variable_settings_inventory", "company_name=$comp_id and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");

		if ($rack_variable == "" || $rack_variable < 2) {
			$store_method_upto = ", b.store_id";
		} else {
			if ($rack_variable == 2) {
				$store_method_upto = ", b.store_id,b.floor_id";
			}
			if ($rack_variable == 3) {
				$store_method_upto = " , b.store_id,b.floor_id,b.room";
			}
			if ($rack_variable == 4) {
				$store_method_upto = " , b.store_id,b.floor_id,b.room,b.rack";
			}
			if ($rack_variable == 5) {
				$store_method_upto = " , b.store_id,b.floor_id,b.room,b.rack,b.self";
			}
			if ($rack_variable == 6) {
				$store_method_upto = " , b.store_id,b.floor_id,b.room,b.rack,b.self,b.bin_box";
			}
		}

		$sql = sql_select("select a.product_name_details, a.current_stock, a.avg_rate_per_unit, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as balance_qnty,b.btb_lc_id $store_method_upto
			from product_details_master a, inv_transaction b
			where a.id=b.prod_id and a.id=$from_prod_id and b.store_id=$from_store_id and b.status_active=1 and b.is_deleted=0 $sqlCon group by a.product_name_details, a.current_stock, a.avg_rate_per_unit, b.btb_lc_id $store_method_upto");

		$stock = $sql[0][csf("balance_qnty")] + $row[csf("transfer_qnty")];

		echo "document.getElementById('txt_item_desc').value 				= '" . $sql[0][csf("product_name_details")] . "';\n";
		echo "document.getElementById('txt_current_stock').value 			= '" . $sql[0][csf("balance_qnty")] . "';\n";
		if ($sql[0][csf("btb_lc_id")] > 0) {
			$btb_lc_num = return_field_value("lc_number", "com_btb_lc_master_details", "id='" . $sql[0][csf("btb_lc_id")] . "'", "lc_number");
			echo "document.getElementById('txt_btb_selection').value = '" . $btb_lc_num . "';\n";
			echo "document.getElementById('txt_btb_lc_id').value = '" . $sql[0][csf("btb_lc_id")] . "';\n";
		}
		echo "document.getElementById('hidden_current_stock').value 		= '" . $stock . "';\n";

		$prod_id_all = $row[csf("from_prod_id")] . "," . $row[csf("to_prod_id")];

		$sql_trans = sql_select("select id, prod_id, transaction_type, origin_prod_id from inv_transaction where mst_id=" . $row[csf('mst_id')] . " and item_category=1 and transaction_type in(5,6) and prod_id in($prod_id_all) order by id asc");
		$trans_issue_id = '';
		$trans_recv_id = '';
		foreach ($sql_trans as $row_trans) {
			if ($row_trans[csf('transaction_type')] == 6 && $row_trans[csf('prod_id')] == $row[csf("from_prod_id")]) {
				$trans_issue_id = $row_trans[csf('id')];
				$orgin_prod_id = $row_trans[csf('origin_prod_id')];
			}

			if ($row_trans[csf('transaction_type')] == 5 && $row_trans[csf('prod_id')] == $row[csf("to_prod_id")]) {
				$trans_recv_id = $row_trans[csf('id')];
			}
		}

		echo "document.getElementById('origin_product_id').value 			= '" . $orgin_prod_id . "';\n";
		echo "document.getElementById('update_trans_issue_id').value 		= '" . $trans_issue_id . "';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '" . $trans_recv_id . "';\n";
		echo "$('#cbo_store_name').attr('disabled',true);\n";
		echo "$('#cbo_store_name_to').attr('disabled',true);\n";
		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_yarn_transfer_entry',1,1);\n";

		exit();
	}
}

//data save update delete here------------------------------//
if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$cbo_floor = (str_replace("'", "", $cbo_floor) == "") ? $cbo_floor = 0 : $cbo_floor;
	$cbo_room = (str_replace("'", "", $cbo_room) == "") ? $cbo_room = 0 : $cbo_room;
	$txt_rack = (str_replace("'", "", $txt_rack) == "") ? $txt_rack = 0 : $txt_rack;
	$txt_shelf = (str_replace("'", "", $txt_shelf) == "") ? $txt_shelf = 0 : $txt_shelf;
	$cbo_bin = (str_replace("'", "", $cbo_bin) == "") ? $cbo_bin = 0 : $cbo_bin;

	$cbo_floor_to = (str_replace("'", "", $cbo_floor_to) == "") ? $cbo_floor_to = '0' : $cbo_floor_to;
	$cbo_room_to = (str_replace("'", "", $cbo_room_to) == "") ? $cbo_room_to = 0 : $cbo_room_to;
	$txt_rack_to = (str_replace("'", "", $txt_rack_to) == "") ? $txt_rack_to = 0 : $txt_rack_to;
	$txt_shelf_to = (str_replace("'", "", $txt_shelf_to) == "") ? $txt_shelf_to = 0 : $txt_shelf_to;
	$cbo_bin_to = (str_replace("'", "", $cbo_bin_to) == "") ? $cbo_bin_to = 0 : $cbo_bin_to;

	//	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (1,4,5)", "max_date");
	//	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	//	$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
	//	if ($transfer_date < $max_recv_date)
	//  {
	//       echo "20**Transfer Date Can not Be Less Than Last Receive Date Of This Lot";
	//       die;
	//	}

	$txt_transfer_value = str_replace("'", "", $txt_transfer_value);
	$txt_transfer_qnty	= str_replace("'", "", $txt_transfer_qnty);
	$update_trans_issue_id = str_replace("'", "", $update_trans_issue_id);
	$update_trans_recv_id	= str_replace("'", "", $update_trans_recv_id);
	$txt_rate	= str_replace("'", "", $txt_rate);

	$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id_to and item_category_id=1 and variable_list= 27", "auto_transfer_rcv");
	if ($variable_auto_rcv == "") {
		$variable_auto_rcv = 1; // if auto receive yes(1), then no need to acknowledgement
	}


	$variable_store_wise_rate = return_field_value("auto_transfer_rcv", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=47 and item_category_id=1 and status_active=1 and is_deleted=0", "auto_transfer_rcv");
	if ($variable_store_wise_rate != 1) $variable_store_wise_rate = 2;

	$variable_store_wise_rate_to = return_field_value("auto_transfer_rcv", "variable_settings_inventory", "company_name=$cbo_company_id_to and variable_list=47 and item_category_id=8 and status_active=1 and is_deleted=0", "auto_transfer_rcv");
	if ($variable_store_wise_rate_to != 1) $variable_store_wise_rate_to = 2;

	//for back wise balance show
	$sql_vs = "SELECT RACK_BALANCE, STORE_METHOD FROM VARIABLE_SETTINGS_INVENTORY WHERE ITEM_CATEGORY_ID=1 AND VARIABLE_LIST= 21 AND COMPANY_NAME = " . $cbo_company_id;
	$sql_vs_rslt = sql_select($sql_vs);
	$vs_data_arr = array();
	if (!empty($sql_vs_rslt)) {
		foreach ($sql_vs_rslt as $row) {
			$vs_data_arr['rack_balance'] = $row['RACK_BALANCE'];
			$vs_data_arr['store_method'] = ($row['STORE_METHOD'] == 0 ? 1 : $row['STORE_METHOD']);
		}
	} else {
		$vs_data_arr['rack_balance'] = 0;
		$vs_data_arr['store_method'] = 1;
	}
	//end for back wise balance show

	if (str_replace("'", "", $update_id) != "") {
		$is_acknowledge = return_field_value("b.id id", "inv_item_transfer_mst a,inv_item_trans_acknowledgement b", "a.id=b.challan_id and  a.id=$update_id and a.status_active=1 and a.is_acknowledge=1", "id");
		if ($is_acknowledge != "") {
			echo "20**Update not allowed. This Transfer Challan is already Acknowledged.\nAcknowledge System ID = $is_acknowledge";
			disconnect($con);
			die;
		}
	}

	//######### this stock item store level and calculate rate ########//
	$store_up_conds = "";
	if ($update_trans_issue_id > 0) {
		if ($update_trans_recv_id > 0) $store_up_conds = " and id not in($update_trans_issue_id,$update_trans_recv_id)";
		else $store_up_conds = " and id not in($update_trans_issue_id)";
	}
	$store_stock_sql = "select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as BALANCE_STOCK, sum((case when transaction_type in(1,4,5) then store_amount else 0 end)-(case when transaction_type in(2,3,6) then store_amount else 0 end)) as BALANCE_AMT 
	from inv_transaction 
	where status_active=1 and prod_id=$hidden_product_id and store_id=$cbo_store_name $store_up_conds";
	//echo "20**$store_stock_sql";disconnect($con);die;
	$store_stock_sql_result = sql_select($store_stock_sql);
	$store_item_rate = 0;
	if ($store_stock_sql_result[0]["BALANCE_AMT"] != 0 && $store_stock_sql_result[0]["BALANCE_STOCK"] != 0) {
		$store_item_rate = $store_stock_sql_result[0]["BALANCE_AMT"] / $store_stock_sql_result[0]["BALANCE_STOCK"];
	}
	$issue_store_value = $txt_transfer_qnty * $store_item_rate;


	// Insert Operation Here
	if ($operation == 0) {
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$transfer_recv_num = '';
		$transfer_update_id = '';
		$order_rate = 0;
		$order_amount = 0;

		$sqlCon = "";
		if ($cbo_floor != "") {
			$sqlCon = " and b.floor_id=$cbo_floor";
		}
		if ($cbo_room != "") {
			$sqlCon .= " and b.room=$cbo_room";
		}
		if ($txt_rack != "") {
			$sqlCon .= " and b.rack=$txt_rack";
		}
		if ($txt_shelf != "") {
			$sqlCon .= " and b.self=$txt_shelf";
		}
		if ($cbo_bin != "") {
			$sqlCon .= " and b.bin_box=$cbo_bin";
		}

		$trans_stock = sql_select("select sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as cons_quantity from inv_transaction b where b.prod_id=$hidden_product_id and b.store_id=$cbo_store_name  and b.status_active=1 and b.is_deleted=0  $sqlCon");

		$current_trans_stock = $trans_stock[0][csf("cons_quantity")];
		if ($txt_transfer_qnty > $current_trans_stock) {
			echo "30**Transfer Quantity Not Allow More Then Stock Quantity.";
			disconnect($con);
			die;
		}

		if (str_replace("'", "", $cbo_transfer_criteria) == 1) {
			$product_info = sql_select("select current_stock,available_qnty,avg_rate_per_unit from product_details_master where id=$hidden_product_id");
			if ($txt_transfer_qnty > $product_info[0][csf("available_qnty")] * 1) {
				echo "21**Transfer quantity can not be greater than available quantity. Available quantity= " . $product_info[0][csf('available_qnty')];
				disconnect($con);
				die;
			}
		}

		if (str_replace("'", "", $update_id) != "") {
			$duplicate = is_duplicate_field("b.id", " inv_item_transfer_mst a, inv_transaction b", "a.id=b.mst_id and a.id=$update_id and b.prod_id=$hidden_product_id and b.transaction_type in(5,6)");
			if ($duplicate == 1) {
				echo "20**Duplicate Product is Not Allow in Same Transfer Number.";
				disconnect($con);
				die;
			}
		}

		// echo "10**".$update_id;die;
		if (str_replace("'", "", $update_id) == "") {
			if ($db_type == 0) $year_cond = "YEAR(insert_date)";
			else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
			else $year_cond = "";

			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_item_transfer_mst", $con, 1, $cbo_company_id, 'YTE', 10, date("Y", time()), 1));

			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			$field_array = "id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria,ready_to_approved, to_company, from_order_id, to_order_id, item_category, remarks, inserted_by, insert_date, entry_form, purpose";

			$data_array = "(" . $id . ",'" . $new_transfer_system_id[1] . "'," . $new_transfer_system_id[2] . ",'" . $new_transfer_system_id[0] . "'," . $cbo_company_id . "," . $txt_challan_no . "," . $txt_transfer_date . "," . $cbo_transfer_criteria . "," . $cbo_approved . "," . $cbo_company_id_to . ",0,0," . $cbo_item_category . "," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',10," . $cbo_purpose . ")";

			$transfer_recv_num = $new_transfer_system_id[0];
			$transfer_update_id = $id;
		} else {
			$field_array_update = "challan_no*transfer_date*ready_to_approved*to_company*remarks*updated_by*update_date*purpose";
			$data_array_update = $txt_challan_no . "*" . $txt_transfer_date . "*" . $cbo_approved . "*" . $cbo_company_id_to . "*" . $txt_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $cbo_purpose . "";

			$transfer_recv_num = str_replace("'", "", $txt_system_id);
			$transfer_update_id = str_replace("'", "", $update_id);

			if ($variable_auto_rcv == 2) // if auto receive yes(1), then no need to acknowledgement
			{
				//echo "10**fail=2";die;
				$pre_saved_store = sql_select("select a.id, b.from_store, b.to_store from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id = b.mst_id and a.entry_form=10 and a.id = $transfer_update_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 group by a.id, b.from_store, b.to_store");

				if (($pre_saved_store[0][csf("from_store")]  != str_replace("'", "", $cbo_store_name)) || ($pre_saved_store[0][csf("to_store")]  != str_replace("'", "", $cbo_store_name_to))) {
					echo "20**Duplicate From Store and To Store is not allowed in same MRR";
					disconnect($con);
					die;
				}
			}
		}

		$field_array_trans = "id, mst_id, company_id, supplier_id, prod_id, origin_prod_id, item_category, transaction_type, transaction_date, store_id,floor_id,room,rack,self,bin_box, brand_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, inserted_by, insert_date, btb_lc_id,buyer_id,store_rate,store_amount";

		$field_array_dtls = "id, mst_id, from_prod_id, to_prod_id, yarn_lot, brand_id, item_group, from_store,floor_id,room,rack,shelf,bin_box, to_store,to_floor_id,to_room,to_rack,to_shelf,to_bin_box, item_category, transfer_qnty, rate, transfer_value, rate_in_usd, transfer_value_in_usd, uom, no_of_bag, no_of_cone, weight_per_bag, fso_no, buyer_id, inserted_by, insert_date,store_rate,store_amount";

		$field_array_dtls_ac = "id, mst_id, dtls_id, is_acknowledge, from_prod_id, to_prod_id, yarn_lot, brand_id, item_group, from_store, floor_id, room, rack, shelf, bin_box, to_store, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, item_category, transfer_qnty, rate, transfer_value, rate_in_usd, transfer_value_in_usd, uom, no_of_bag, no_of_cone, weight_per_bag, fso_no, buyer_id, inserted_by, insert_date,store_rate,store_amount";
		$field_array_store = "last_issued_qnty*cons_qty*amount*updated_by*update_date";
		$data_array_store_rcv = "";
		if (str_replace("'", "", $cbo_transfer_criteria) == 1) // Company to Company transfer
		{
			$data_prod = sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value, available_qnty from product_details_master where id=$hidden_product_id");
			$presentStock = $data_prod[0][csf('current_stock')] - $txt_transfer_qnty;
			$presentAvgRate = $data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue = $presentStock * $presentAvgRate;
			$presentAvaillableQty = $data_prod[0][csf('available_qnty')] - $txt_transfer_qnty;

			$field_array_prodUpdate = "avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*available_qnty*updated_by*update_date";
			$data_array_prodUpdate = number_format($presentAvgRate, 10, '.', '') . "*" . $txt_transfer_qnty . "*" . $presentStock . "*" . number_format($presentStockValue, 8, '.', '') . "*" . $presentAvaillableQty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			$supplier_id = $data_prod[0][csf('supplier_id')];

			$row_prod = sql_select("select id, current_stock, stock_value, avg_rate_per_unit, available_qnty, dyed_type from product_details_master where company_id=$cbo_company_id_to and item_category_id=1 and supplier_id='$supplier_id' and product_name_details=$txt_item_desc and lot=$txt_yarn_lot and status_active=1 and is_deleted=0");

			if (count($row_prod) > 0) // $product_id // Already found Product
			{
				$product_id = $row_prod[0][csf('id')];
				$current_stock_qnty = ($row_prod[0][csf('current_stock')] + $txt_transfer_qnty);
				$current_stock_value = ($row_prod[0][csf('stock_value')] + $txt_transfer_value);
				$current_avg_rate = ($current_stock_value / $current_stock_qnty);
				$curr_availlable_qty = $row_prod[0][csf('available_qnty')] + $txt_transfer_qnty;

				if ($variable_auto_rcv == 1) // if auto receive yes(1), then no need to acknowledgement
				{
					$field_array_prod_update = "avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*available_qnty*updated_by*update_date";
					$data_array_prod_update = number_format($current_avg_rate, 10, '.', '') . "*" . $txt_transfer_qnty . "*" . $current_stock_qnty . "*" . number_format($current_stock_value, 8, '.', '') . "*" . $curr_availlable_qty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
				}
			} else // Create new product_id here
			{
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$curr_stock_qnty = $txt_transfer_qnty;
				$avg_rate_per_unit = $data_prod[0][csf('avg_rate_per_unit')];
				$stock_value = $curr_stock_qnty * $avg_rate_per_unit;
				$curr_availlable_qty = $txt_transfer_qnty;

				if ($variable_auto_rcv == 1) // if auto receive yes(1), then no need to acknowledgement
				{
					$sql_prod_insert = "insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, available_qnty, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date, dyed_type,is_within_group)
					select
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, $curr_availlable_qty, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand,'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "', dyed_type, is_within_group from product_details_master where id=$hidden_product_id";
				} else {
					$sql_prod_insert = "insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, available_qnty, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date, dyed_type, is_within_group)
					select
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand,'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "', dyed_type, is_within_group from product_details_master where id=$hidden_product_id";
				}
			}

			//----------------Check Last Receive Date for Transfer Out---------------->>>>>>>
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (1,4,5) and store_id = $cbo_store_name  and status_active = 1", "max_date");
			if ($max_recv_date != "") {
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_recv_date) {
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
					disconnect($con);
					die;
				}
			}

			//----------------Check Last Issue Date for Transfer In---------------->>>>>>>
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id = $cbo_store_name_to  and status_active = 1", "max_date");
			if ($max_issue_date != "") {
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_issue_date) {
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
					disconnect($con);
					die;
				}
			}

			$recv_qty = 0;
			$recv_amnt = 0;
			$sql_receive = "select a.receive_date, a.currency_id, sum(b.order_qnty) as qty, sum(b.order_qnty*b.order_rate) as amnt from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=1 and a.item_category=1 and b.transaction_type=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id=$hidden_product_id group by a.receive_date, a.currency_id";
			// echo $sql_receive;die;
			$resultReceive = sql_select($sql_receive);
			foreach ($resultReceive as $row) {
				$recv_qty += $row[csf('qty')];
				if ($row[csf('currency_id')] == 1) {
					$exchange_rate = set_conversion_rate(2, $row[csf('receive_date')]);
					if ($exchange_rate <= 0) {
						$exchange_rate = 76;
					}
					$recv_amnt += $row[csf('amnt')] / $exchange_rate;
				} else {
					$recv_amnt += $row[csf('amnt')];
				}
			}

			$sql_trans = "select sum(order_qnty) as qty, sum(order_amount) as amnt from inv_transaction where transaction_type=5 and item_category=1 and status_active=1 and is_deleted=0 and prod_id=$hidden_product_id";
			// echo $sql_trans;die;
			$resultTrans = sql_select($sql_trans);
			$trnas_recv_qty = $resultTrans[0][csf('qty')];
			$trnas_recv_amnt = $resultTrans[0][csf('amnt')];

			$tot_recv_qty = $recv_qty + $trnas_recv_qty;
			$tot_recv_amnt = $recv_amnt + $trnas_recv_amnt;

			$order_rate = $tot_recv_amnt / $tot_recv_qty;
			$order_rate = is_nan($order_rate) ? 0 : $order_rate;
			$order_amount = $order_rate * $txt_transfer_qnty;

			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$data_array_trans = "(" . $id_trans . "," . $transfer_update_id . "," . $cbo_company_id . ",'" . $supplier_id . "'," . $hidden_product_id . "," . $origin_product_id . "," . $cbo_item_category . ",6," . $txt_transfer_date . "," . $cbo_store_name . "," . $cbo_floor . "," . $cbo_room . "," . $txt_rack . "," . $txt_shelf . "," . $cbo_bin . "," . $hide_brand_id . ",0,0,0,0," . $cbo_uom . "," . $txt_transfer_qnty . "," . number_format($txt_rate, 10, '.', '') . "," . number_format($txt_transfer_value, 8, '.', '') . ",0,0," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $txt_btb_lc_id . "," . $cbo_buyer_name . "," . number_format($store_item_rate, 10, '.', '') . "," . number_format($issue_store_value, 8, '.', '') . ")";

			$recv_trans_id = 0;
			if ($variable_auto_rcv == 1) // if auto receive yes(1), then no need to acknowledgement
			{
				$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans .= ",(" . $recv_trans_id . "," . $transfer_update_id . "," . $cbo_company_id_to . ",'" . $supplier_id . "'," . $product_id . "," . $origin_product_id . "," . $cbo_item_category . ",5," . $txt_transfer_date . "," . $cbo_store_name_to . "," . $cbo_floor_to . "," . $cbo_room_to . "," . $txt_rack_to . "," . $txt_shelf_to . "," . $cbo_bin_to . "," . $hide_brand_id . "," . $cbo_uom . "," . $txt_transfer_qnty . ",'" . number_format($order_rate, 10, '.', '') . "','" . number_format($order_amount, 8, '.', '') . "'," . $cbo_uom . "," . $txt_transfer_qnty . "," . number_format($txt_rate, 10, '.', '') . "," . number_format($txt_transfer_value, 8, '.', '') . "," . $txt_transfer_qnty . "," . $txt_transfer_value . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $txt_btb_lc_id . "," . $cbo_buyer_name . "," . number_format($store_item_rate, 10, '.', '') . "," . number_format($issue_store_value, 8, '.', '') . ")";
			}

			$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
			$data_array_dtls = "(" . $id_dtls . "," . $transfer_update_id . "," . $hidden_product_id . "," . $product_id . "," . $txt_yarn_lot . "," . $hide_brand_id . ",0," . $cbo_store_name . "," . $cbo_floor . "," . $cbo_room . "," . $txt_rack . "," . $txt_shelf . "," . $cbo_bin . "," . $cbo_store_name_to . "," . $cbo_floor_to . "," . $cbo_room_to . "," . $txt_rack_to . "," . $txt_shelf_to . "," . $cbo_bin_to . "," . $cbo_item_category . "," . $txt_transfer_qnty . "," . number_format($txt_rate, 10, '.', '') . "," . number_format($txt_transfer_value, 8, '.', '') . ",'" . number_format($order_rate, 10, '.', '') . "','" . number_format($order_amount, 8, '.', '') . "'," . $cbo_uom . "," . $txt_no_of_bag . "," . $txt_no_of_cone . "," . $txt_weight_per_bag . "," . $txt_fso_no . "," . $cbo_buyer_name . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . number_format($store_item_rate, 10, '.', '') . "," . number_format($issue_store_value, 8, '.', '') . ")";

			if ($variable_auto_rcv == 2) // acknowledgement_dtls_table
			{
				$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
				$data_array_dtls_ac = "(" . $id_dtls_ac . "," . $transfer_update_id . "," . $id_dtls . ",0," . $hidden_product_id . "," . $product_id . "," . $txt_yarn_lot . "," . $hide_brand_id . ",0," . $cbo_store_name . "," . $cbo_floor . "," . $cbo_room . "," . $txt_rack . "," . $txt_shelf . "," . $cbo_bin . "," . $cbo_store_name_to . "," . $cbo_floor_to . "," . $cbo_room_to . "," . $txt_rack_to . "," . $txt_shelf_to . "," . $cbo_bin_to . "," . $cbo_item_category . "," . $txt_transfer_qnty . "," . number_format($txt_rate, 10, '.', '') . "," . number_format($txt_transfer_value, 8, '.', '') . ",'" . number_format($order_rate, 10, '.', '') . "','" . number_format($order_amount, 8, '.', '') . "'," . $cbo_uom . "," . $txt_no_of_bag . "," . $txt_no_of_cone . "," . $txt_weight_per_bag . "," . $txt_fso_no . "," . $cbo_buyer_name . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . number_format($store_item_rate, 10, '.', '') . "," . number_format($issue_store_value, 8, '.', '') . ")";
			}

			$store_up_id = 0;
			if ($variable_store_wise_rate == 1) {
				$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$hidden_product_id and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");

				if (count($sql_store) < 1) {
					echo "20**No Data Found.";
					disconnect($con);
					die;
				} elseif (count($sql_store) > 1) {
					echo "20**Duplicate Product is Not Allow in Same REF Number.";
					disconnect($con);
					die;
				} else {
					$store_presentStock = $store_presentStockValue = $store_presentAvgRate = 0;
					foreach ($sql_store as $result) {
						$store_up_id = $result[csf("id")];
						$store_presentStock	= $result[csf("current_stock")];
						$store_presentStockValue = $result[csf("stock_value")];
						$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
					}

					$currentStock_store		= $store_presentStock - $txt_transfer_qnty;
					$currentValue_store		= $store_presentStockValue - $issue_store_value;
					if ($store_up_id) {
						$store_id_arr[] = $store_up_id;
						$data_array_store[$store_up_id] = explode("*", ("" . $txt_transfer_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					}
					//"".$txt_transfer_qnty."*".$currentStock_store."*".$currentValue_store."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
				}
			}

			if ($variable_auto_rcv == 1 && $variable_store_wise_rate == 1 && $variable_store_wise_rate_to = 1) {
				$sql_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$product_id and category_id=1 and store_id=$cbo_store_name_to and company_id=$cbo_company_id_to");
				$store_up_id_to = 0;
				if (count($sql_store_rcv) < 1) {
					$field_array_store_rcv = "id,company_id,store_id,category_id,prod_id,cons_qty,rate,amount,last_purchased_qnty,inserted_by,insert_date,lot,first_receive_date,last_receive_date";

					$sdtlsid = return_next_id("id", "inv_store_wise_yarn_qty_dtls", 1);
					$data_array_store_rcv = "(" . $sdtlsid . "," . $cbo_company_id_to . "," . $cbo_store_name_to . ",1," . $product_id . "," . $txt_transfer_qnty . "," . number_format($store_item_rate, 10, '.', '') . "," . number_format($issue_store_value, 8, '.', '') . "," . $txt_transfer_qnty . ",'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "'," . $txt_yarn_lot . "," . $txt_transfer_date . "," . $txt_transfer_date . ")";
				} elseif (count($sql_store_rcv) > 1) {
					echo "20**Duplicate Product is Not Allow in Same REF Number.";
					disconnect($con);
					die;
				} else {
					$store_presentStock = $store_presentStockValue = $store_presentAvgRate = 0;
					foreach ($sql_store_rcv as $result) {
						$store_up_id_to = $result[csf("id")];
						$store_presentStock	= $result[csf("current_stock")];
						$store_presentStockValue = $result[csf("stock_value")];
						$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
					}
					$currentStock_store		= $store_presentStock + $txt_transfer_qnty;
					$currentValue_store		= $store_presentStockValue + $issue_store_value;
					if ($store_up_id_to) {
						$store_id_arr[] = $store_up_id_to;
						$data_array_store[$store_up_id_to] = explode("*", ("" . $txt_transfer_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					}
				}
			}

			//LIFO/FIFO Start-----------------------------------------------//
			$isLIFOfifo = return_field_value("store_method", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=17 and item_category_id=1 and status_active=1 and is_deleted=0");
			if ($isLIFOfifo == 2) $cond_lifofifo = " DESC";
			else $cond_lifofifo = " ASC";

			$transfer_qnty = str_replace("'", "", $txt_transfer_qnty);
			$transfer_value = str_replace("'", "", $txt_transfer_value);
			$field_array_mrr = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";

			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$updateID_array = array();
			$update_data = array();

			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and store_id=$cbo_store_name and balance_qnty>0 and transaction_type in (1,4,5) and item_category=1 order by transaction_date,id $cond_lifofifo");
			foreach ($sql as $result) {
				$recv_trans_id = $result[csf("id")]; // this row will be updated
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
				$cons_rate = $result[csf("cons_rate")];
				if ($cons_rate == "") {
					$cons_rate = str_replace("'", "", $txt_rate);
				}

				$transferQntyBalance = $balance_qnty - $transfer_qnty; // minus issue qnty
				$transferStockBalance = $balance_amount - ($transfer_qnty * $cons_rate);

				if ($transferQntyBalance >= 0) {
					$amount = $transfer_qnty * $cons_rate;
					//for insert
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					if ($data_array_mrr != "") $data_array_mrr .= ",";
					$data_array_mrr .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $id_trans . ",10," . $hidden_product_id . "," . $transfer_qnty . "," . $cons_rate . "," . $amount . ",'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "')";
					//for update
					$updateID_array[] = $recv_trans_id;
					$update_data[$recv_trans_id] = explode("*", ("" . $transferQntyBalance . "*" . $transferStockBalance . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					break;
				} else if ($transferQntyBalance < 0) {


					$transferQntyBalance = $transfer_qnty - $balance_qnty;
					$transfer_qnty = $balance_qnty;
					$amount = $transfer_qnty * $cons_rate;

					//for insert
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					if ($data_array_mrr != "") $data_array_mrr .= ",";
					$data_array_mrr .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $id_trans . ",10," . $hidden_product_id . "," . $balance_qnty . "," . $cons_rate . "," . $amount . ",'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "')";
					//for update
					$updateID_array[] = $recv_trans_id;
					$update_data[$recv_trans_id] = explode("*", ("0*0*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					$transfer_qnty = $transferQntyBalance;
				}
			} //end foreach
			// LIFO/FIFO END-----------------------------------------------//

			/*
			|--------------------------------------------------------------------------
			| inv_yarn_test_mst
			|--------------------------------------------------------------------------
			|
			*/
			$sql_test_mst_insert = '';
			$data_array_test_dtls = '';
			$data_array_test_comments = '';
			$sql_test_mst = sql_select("select id from inv_yarn_test_mst where company_id = " . $cbo_company_id . " and prod_id = " . $hidden_product_id . " and lot_number = " . $txt_yarn_lot . "");
			if (!empty($sql_test_mst)) {
				$testMstId = return_next_id("id", "inv_yarn_test_mst", 1);
				$sql_test_mst_insert = "insert into inv_yarn_test_mst(id, company_id, prod_id, lot_number, test_date, test_for, specimen_wgt, specimen_length, color, receive_qty, lc_number, lc_qty, actual_yarn_count, actual_yarn_count_phy, yarn_apperance_grad, yarn_apperance_phy, twist_per_inc, twist_per_inc_phy, moisture_content, moisture_content_phy, ipi_value, ipi_value_phy, csp_minimum, csp_minimum_phy, csp_actual, csp_actual_phy, thin_yarn, thin_yarn_phy, thick, thick_phy, u, u_phy, cv, cv_phy, neps_per_km, neps_per_km_phy, heariness, heariness_phy, counts_cv, counts_cv_phy, system_result, grey_gsm, grey_wash_gsm, required_gsm, required_dia, machine_dia, stich_length, grey_gsm_dye, batch, finish_gsm, finish_dia, length, width, inserted_by, insert_date)
				select
				'" . $testMstId . "', " . $cbo_company_id_to . ", " . $product_id . ", lot_number, test_date, test_for, specimen_wgt, specimen_length, color, receive_qty, lc_number, lc_qty, actual_yarn_count, actual_yarn_count_phy, yarn_apperance_grad, yarn_apperance_phy, twist_per_inc, twist_per_inc_phy, moisture_content, moisture_content_phy, ipi_value, ipi_value_phy, csp_minimum, csp_minimum_phy, csp_actual, csp_actual_phy, thin_yarn, thin_yarn_phy, thick, thick_phy, u, u_phy, cv, cv_phy, neps_per_km, neps_per_km_phy, heariness, heariness_phy, counts_cv, counts_cv_phy, system_result, grey_gsm, grey_wash_gsm, required_gsm, required_dia, machine_dia, stich_length, grey_gsm_dye, batch, finish_gsm, finish_dia, length, width,'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "' from inv_yarn_test_mst where company_id = " . $cbo_company_id . " and prod_id = " . $hidden_product_id . " and lot_number = " . $txt_yarn_lot . " group by '" . $testMstId . "', " . $cbo_company_id_to . ", " . $product_id . ", lot_number, test_date, test_for, specimen_wgt, specimen_length, color, receive_qty, lc_number, lc_qty, actual_yarn_count, actual_yarn_count_phy, yarn_apperance_grad, yarn_apperance_phy, twist_per_inc, twist_per_inc_phy, moisture_content, moisture_content_phy, ipi_value, ipi_value_phy, csp_minimum, csp_minimum_phy, csp_actual, csp_actual_phy, thin_yarn, thin_yarn_phy, thick, thick_phy, u, u_phy, cv, cv_phy, neps_per_km, neps_per_km_phy, heariness, heariness_phy, counts_cv, counts_cv_phy, system_result, grey_gsm, grey_wash_gsm, required_gsm, required_dia, machine_dia, stich_length, grey_gsm_dye, batch, finish_gsm, finish_dia, length, width,'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "'";

				/*
				|--------------------------------------------------------------------------
				| inv_yarn_test_dtls
				|--------------------------------------------------------------------------
				|
				*/
				$field_array_test_dtls = "id, mst_id, testing_parameters_id, fab_type, testing_parameters, fabric_point, result, acceptance, fabric_class, remarks, inserted_by, insert_date";
				$testDtlsId = return_next_id("id", "inv_yarn_test_dtls", 1);
				$sql_test_dtls = "select id, mst_id, testing_parameters_id, fab_type, testing_parameters, fabric_point, result, acceptance, fabric_class, remarks from inv_yarn_test_dtls where mst_id in(select id from inv_yarn_test_mst where company_id = " . $cbo_company_id . " and prod_id = " . $hidden_product_id . " and lot_number = " . $txt_yarn_lot . ")";
				$rslt_sql_test_dtls = sql_select($sql_test_dtls);
				foreach ($rslt_sql_test_dtls as $row) {
					$from_mst_id = $row[csf('mst_id')];
					//$from_dtls_id = $row[csf('id')];

					$testing_parameters_id = $row[csf('testing_parameters_id')];
					$fab_type = $row[csf('fab_type')];
					$testing_parameters = $row[csf('testing_parameters')];
					$fabric_point = $row[csf('fabric_point')];
					$result = $row[csf('result')];
					$acceptance = $row[csf('acceptance')];
					$fabric_class = $row[csf('fabric_class')];
					$remarks = $row[csf('remarks')];

					if ($data_array_test_dtls != '')
						$data_array_test_dtls .= ",";

					$data_array_test_dtls .= "(" . $testDtlsId . "," . $testMstId . ",'" . $testing_parameters_id . "','" . $fab_type . "','" . $testing_parameters . "','" . $fabric_point . "','" . $result . "','" . $acceptance . "','" . $fabric_class . "','" . $remarks . "','" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "')";
					$testDtlsId = $testDtlsId + 1;
				}

				/*
				|--------------------------------------------------------------------------
				| inv_yarn_test_comments
				|--------------------------------------------------------------------------
				|
				*/
				$testDtlsId = $testDtlsId - 1;
				$from_company_name = return_field_value("company_short_name", "lib_company", "id=$cbo_company_id", "company_short_name");
				$field_array_test_comments = "id, mst_table_id, dtls_id, comments_knit_acceptance, comments_knit, comments_dye_acceptance, comments_dye, comments_author_acceptance, comments_author, inserted_by, insert_date";
				$testCommentsId = return_next_id("id", "inv_yarn_test_comments", 1);
				$sql_test_comments = "select comments_knit_acceptance, comments_knit, comments_dye_acceptance, comments_dye, comments_author_acceptance, comments_author from inv_yarn_test_comments where mst_table_id = " . $from_mst_id . "";
				$rslt_sql_test_comments = sql_select($sql_test_comments);
				foreach ($rslt_sql_test_comments as $row) {
					$comments_knit_acceptance = $row[csf('comments_knit_acceptance')];
					$comments_knit = $row[csf('comments_knit')];
					$comments_dye_acceptance = $row[csf('comments_dye_acceptance')];
					$comments_dye = $row[csf('comments_dye')];
					$comments_author_acceptance = $row[csf('comments_author_acceptance')];
					$comments_author = $row[csf('comments_author')] . " [" . $from_company_name . "]";

					if ($data_array_test_comments != '')
						$data_array_test_comments .= ",";

					$data_array_test_comments .= "(" . $testCommentsId . "," . $testMstId . ",'" . $testDtlsId . "','" . $comments_knit_acceptance . "','" . $comments_knit . "','" . $comments_dye_acceptance . "','" . $comments_dye . "','" . $comments_author_acceptance . "','" . $comments_author . "','" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "')";
					$testCommentsId = $testCommentsId + 1;
				}
			}
		} else // store to store transfer
		{
			//----------------Check Last Receive Date for Transfer Out---------------->>>>>>>
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (1,4,5) and store_id = $cbo_store_name  and status_active = 1", "max_date");
			if ($max_recv_date != "") {
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_recv_date) {
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
					disconnect($con);
					die;
				}
			}

			//----------------Check Last Issue Date for Transfer In---------------->>>>>>>
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and store_id = $cbo_store_name_to  and status_active = 1", "max_date");
			if ($max_issue_date != "") {
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_issue_date) {
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
					disconnect($con);
					die;
				}
			}

			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$data_array_trans = "(" . $id_trans . "," . $transfer_update_id . "," . $cbo_company_id . ",0," . $hidden_product_id . "," . $origin_product_id . "," . $cbo_item_category . ",6," . $txt_transfer_date . "," . $cbo_store_name . "," . $cbo_floor . "," . $cbo_room . "," . $txt_rack . "," . $txt_shelf . "," . $cbo_bin . "," . $hide_brand_id . ",0,0,0,0," . $cbo_uom . "," . $txt_transfer_qnty . "," . number_format($txt_rate, 10, '.', '') . "," . number_format($txt_transfer_value, 8, '.', '') . ",0,0," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $txt_btb_lc_id . "," . $cbo_buyer_name . "," . number_format($store_item_rate, 10, '.', '') . "," . number_format($issue_store_value, 8, '.', '') . ")";

			$recv_trans_id = 0;
			if ($variable_auto_rcv == 1) {
				$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans .= ",(" . $recv_trans_id . "," . $transfer_update_id . "," . $cbo_company_id . ",0," . $hidden_product_id . "," . $origin_product_id . "," . $cbo_item_category . ",5," . $txt_transfer_date . "," . $cbo_store_name_to . "," . $cbo_floor_to . "," . $cbo_room_to . "," . $txt_rack_to . "," . $txt_shelf_to . "," . $cbo_bin_to . "," . $hide_brand_id . ",0,0,0,0," . $cbo_uom . "," . $txt_transfer_qnty . "," . number_format($txt_rate, 10, '.', '') . "," . number_format($txt_transfer_value, 8, '.', '') . "," . $txt_transfer_qnty . "," . number_format($txt_transfer_value, 10, '.', '') . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $txt_btb_lc_id . "," . $cbo_buyer_name . "," . number_format($store_item_rate, 10, '.', '') . "," . number_format($issue_store_value, 8, '.', '') . ")";
			}

			$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
			$data_array_dtls = "(" . $id_dtls . "," . $transfer_update_id . "," . $hidden_product_id . "," . $hidden_product_id . "," . $txt_yarn_lot . "," . $hide_brand_id . ",0," . $cbo_store_name . "," . $cbo_floor . "," . $cbo_room . "," . $txt_rack . "," . $txt_shelf . "," . $cbo_bin . "," . $cbo_store_name_to . "," . $cbo_floor_to . "," . $cbo_room_to . "," . $txt_rack_to . "," . $txt_shelf_to . "," . $cbo_bin_to . "," . $cbo_item_category . "," . $txt_transfer_qnty . "," . number_format($txt_rate, 10, '.', '') . "," . number_format($txt_transfer_value, 8, '.', '') . ",0,0," . $cbo_uom . "," . $txt_no_of_bag . "," . $txt_no_of_cone . "," . $txt_weight_per_bag . "," . $txt_fso_no . "," . $cbo_buyer_name . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . number_format($store_item_rate, 10, '.', '') . "," . number_format($issue_store_value, 8, '.', '') . ")";

			if ($variable_auto_rcv == 2) // acknowledgement_dtls_table
			{
				$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
				$data_array_dtls_ac = "(" . $id_dtls_ac . "," . $transfer_update_id . "," . $id_dtls . ",0," . $hidden_product_id . "," . $hidden_product_id . "," . $txt_yarn_lot . "," . $hide_brand_id . ",0," . $cbo_store_name . "," . $cbo_floor . "," . $cbo_room . "," . $txt_rack . "," . $txt_shelf . "," . $cbo_bin . "," . $cbo_store_name_to . "," . $cbo_floor_to . "," . $cbo_room_to . "," . $txt_rack_to . "," . $txt_shelf_to . "," . $cbo_bin_to . "," . $cbo_item_category . "," . $txt_transfer_qnty . "," . number_format($txt_rate, 10, '.', '') . "," . number_format($txt_transfer_value, 8, '.', '') . ",'" . $order_rate . "','" . $order_amount . "'," . $cbo_uom . "," . $txt_no_of_bag . "," . $txt_no_of_cone . "," . $txt_weight_per_bag . "," . $txt_fso_no . "," . $cbo_buyer_name . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . number_format($store_item_rate, 10, '.', '') . "," . number_format($issue_store_value, 8, '.', '') . ")";
			}

			$store_up_id = 0;
			if ($variable_store_wise_rate == 1) {
				$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$hidden_product_id and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");

				if (count($sql_store) < 1) {
					echo "20**No Data Found.";
					disconnect($con);
					die;
				} elseif (count($sql_store) > 1) {
					echo "20**Duplicate Product is Not Allow in Same REF Number.";
					disconnect($con);
					die;
				} else {
					$store_presentStock = $store_presentStockValue = $store_presentAvgRate = 0;
					foreach ($sql_store as $result) {
						$store_up_id = $result[csf("id")];
						$store_presentStock	= $result[csf("current_stock")];
						$store_presentStockValue = $result[csf("stock_value")];
						$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
					}

					$currentStock_store		= $store_presentStock - $txt_transfer_qnty;
					$currentValue_store		= $store_presentStockValue - $issue_store_value;
					if ($store_up_id) {
						$store_id_arr[] = $store_up_id;
						$data_array_store[$store_up_id] = explode("*", ("" . $txt_transfer_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					}
					//"".$txt_transfer_qnty."*".$currentStock_store."*".$currentValue_store."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
				}
			}

			$store_up_id_to = 0;
			if ($variable_auto_rcv == 1 && $variable_store_wise_rate == 1 && $variable_store_wise_rate_to == 1) {
				$sql_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$hidden_product_id and category_id=1 and store_id=$cbo_store_name_to and company_id=$cbo_company_id");

				if (count($sql_store_rcv) < 1) {
					$field_array_store_rcv = "id,company_id,store_id,category_id,prod_id,cons_qty,rate,amount,last_purchased_qnty,inserted_by,insert_date,lot,first_receive_date,last_receive_date";

					$sdtlsid = return_next_id("id", "inv_store_wise_yarn_qty_dtls", 1);
					$data_array_store_rcv = "(" . $sdtlsid . "," . $cbo_company_id . "," . $cbo_store_name_to . ",1," . $hidden_product_id . "," . $txt_transfer_qnty . "," . number_format($store_item_rate, 10, '.', '') . "," . number_format($issue_store_value, 8, '.', '') . "," . $txt_transfer_qnty . ",'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "'," . $txt_yarn_lot . "," . $txt_transfer_date . "," . $txt_transfer_date . ")";
				} elseif (count($sql_store_rcv) > 1) {
					echo "20**Duplicate Product is Not Allow in Same REF Number.";
					disconnect($con);
					die;
				} else {
					$store_presentStock = $store_presentStockValue = $store_presentAvgRate = 0;
					foreach ($sql_store_rcv as $result) {
						$store_up_id_to = $result[csf("id")];
						$store_presentStock	= $result[csf("current_stock")];
						$store_presentStockValue = $result[csf("stock_value")];
						$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
					}
					$currentStock_store		= $store_presentStock + $txt_transfer_qnty;
					$currentValue_store		= $store_presentStockValue + $issue_store_value;
					if ($store_up_id_to) {
						$store_id_arr[] = $store_up_id_to;
						$data_array_store[$store_up_id_to] = explode("*", ("" . $txt_transfer_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					}
				}
			}

			//LIFO/FIFO Start-----------------------------------------------//
			$isLIFOfifo = return_field_value("store_method", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=17 and item_category_id=1 and status_active=1 and is_deleted=0");
			if ($isLIFOfifo == 2) $cond_lifofifo = " DESC";
			else $cond_lifofifo = " ASC";

			$transfer_qnty = str_replace("'", "", $txt_transfer_qnty);
			$transfer_value = str_replace("'", "", $txt_transfer_value);
			$field_array_mrr = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			//$field_array_mrr2= "id,recv_trans_id,entry_form,prod_id,item_return_qty,amount,transfer_criteria,inserted_by,insert_date";
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$updateID_array = array();
			$update_data = array();

			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and store_id=$cbo_store_name and balance_qnty>0 and transaction_type in (1,4,5) and item_category=1 order by transaction_date,id $cond_lifofifo");

			foreach ($sql as $result) {
				$recv_trans_id = $result[csf("id")]; // this row will be updated
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
				$cons_rate = $result[csf("cons_rate")];
				if ($cons_rate == "") {
					$cons_rate = str_replace("'", "", $txt_rate);
				}

				$transferQntyBalance = $balance_qnty - $transfer_qnty; // minus issue qnty

				$transferStockBalance = $balance_amount - ($transfer_qnty * $cons_rate);
				if ($transferQntyBalance >= 0) {
					$amount = $transfer_qnty * $cons_rate;
					//for insert
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					if ($data_array_mrr != "") $data_array_mrr .= ",";
					$data_array_mrr .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $id_trans . ",10," . $hidden_product_id . "," . $transfer_qnty . "," . $cons_rate . "," . $amount . ",'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "')";
					//for update
					$updateID_array[] = $recv_trans_id;
					$update_data[$recv_trans_id] = explode("*", ("" . $transferQntyBalance . "*" . $transferStockBalance . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					break;
				} else if ($transferQntyBalance < 0) {
					$transferQntyBalance = $transfer_qnty - $balance_qnty;
					$transfer_qnty = $balance_qnty;
					$amount = $transfer_qnty * $cons_rate;

					//for insert
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					if ($data_array_mrr != "") $data_array_mrr .= ",";
					$data_array_mrr .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $id_trans . ",10," . $hidden_product_id . "," . $balance_qnty . "," . $cons_rate . "," . $amount . ",'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "')";
					//for update
					$updateID_array[] = $recv_trans_id;
					$update_data[$recv_trans_id] = explode("*", ("0*0*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					$transfer_qnty = $transferQntyBalance;
				}
			} //end foreach
			// LIFO/FIFO END-----------------------------------------------//
		}

		$rID = $rID2 = $rID3 = $rID4 = $mrrWiseIssueID = $upTrID = $prodUpdate = $prod = $rsltTestMst = $rsltTestDtls = $rsltTestComments = $storeUpID = $storeInsID = true;
		if (str_replace("'", "", $update_id) == "") {
			$rID = sql_insert("inv_item_transfer_mst", $field_array, $data_array, 0);
		} else {
			$rID = sql_update("inv_item_transfer_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
		}
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$rID2 = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 0);
		//echo "10**insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID3 = sql_insert("inv_item_transfer_dtls", $field_array_dtls, $data_array_dtls, 0);
		if ($variable_auto_rcv == 2) // inv_item_transfer_dtls_ac
		{
			$rID4 = sql_insert("inv_item_transfer_dtls_ac", $field_array_dtls_ac, $data_array_dtls_ac, 0);
		}
		$mrrWiseIssueID = sql_insert("inv_mrr_wise_issue_details", $field_array_mrr, $data_array_mrr, 0);
		$upTrID = execute_query(bulk_update_sql_statement("inv_transaction", "id", $update_array, $update_data, $updateID_array), 0);

		if (str_replace("'", "", $cbo_transfer_criteria) == 1) {
			$prodUpdate = sql_update("product_details_master", $field_array_prodUpdate, $data_array_prodUpdate, "id", $hidden_product_id, 0);
			if (count($row_prod) > 0) {
				if ($variable_auto_rcv == 1) {
					$prod = sql_update("product_details_master", $field_array_prod_update, $data_array_prod_update, "id", $product_id, 0);
				}
			} else {
				$prod = execute_query($sql_prod_insert, 0);
			}


			if ($sql_test_mst_insert != '') {
				$rsltTestMst = execute_query($sql_test_mst_insert, 0);
			}
			if ($data_array_test_dtls != '') {
				$rsltTestDtls = sql_insert("inv_yarn_test_dtls", $field_array_test_dtls, $data_array_test_dtls, 0);
			}
			if ($data_array_test_comments != '') {
				$rsltTestComments = sql_insert("inv_yarn_test_comments", $field_array_test_comments, $data_array_test_comments, 0);
			}
		}


		if (count($store_id_arr) > 0 && $variable_store_wise_rate == 1) {
			$storeUpID = execute_query(bulk_update_sql_statement("inv_store_wise_yarn_qty_dtls", "id", $field_array_store, $data_array_store, $store_id_arr));
		}
		if ($data_array_store_rcv != "" && $variable_store_wise_rate == 1 && $variable_store_wise_rate_to == 1) {
			$storeInsID = sql_insert("inv_store_wise_yarn_qty_dtls", $field_array_store_rcv, $data_array_store_rcv, 1);
		}

		//echo "10** $rID=$rID2=$rID3=$rID4=$mrrWiseIssueID=$upTrID=$prodUpdate=$prod=$rsltTestMst=$rsltTestDtls=$rsltTestComments=$storeUpID=$storeInsID";oci_rollback($con);disconnect($con);die();

		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3 && $rID4 && $mrrWiseIssueID && $upTrID && $prodUpdate && $prod && $rsltTestMst && $rsltTestDtls && $rsltTestComments && $storeUpID && $storeInsID) {
				mysql_query("COMMIT");
				echo "0**" . $transfer_update_id . "**" . $transfer_recv_num . "**0";
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**" . "&nbsp;" . "**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3 && $rID4 && $mrrWiseIssueID && $upTrID && $prodUpdate && $prod && $rsltTestMst && $rsltTestDtls && $rsltTestComments && $storeUpID && $storeInsID) {
				oci_commit($con);
				echo "0**" . $transfer_update_id . "**" . $transfer_recv_num . "**0";
			} else {
				oci_rollback($con);
				echo "5**0**" . "&nbsp;" . "**0";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	} else if ($operation == 1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$sqlCon = "";
		if ($cbo_floor != "") {
			$sqlCon = " and b.floor_id=$cbo_floor";
		}
		if ($cbo_room != "") {
			$sqlCon .= " and b.room=$cbo_room";
		}
		if ($txt_rack != "") {
			$sqlCon .= " and b.rack=$txt_rack";
		}
		if ($txt_shelf != "") {
			$sqlCon .= " and b.self=$txt_shelf";
		}
		if ($cbo_bin != "") {
			$sqlCon .= " and b.bin_box=$cbo_bin";
		}

		$trans_stock = sql_select("select sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as cons_quantity from inv_transaction b where b.prod_id=$hidden_product_id and b.store_id=$cbo_store_name and b.status_active=1 and b.is_deleted=0 $sqlCon");

		$current_trans_stock = $trans_stock[0][csf("cons_quantity")] + str_replace("'", "", $hidden_transfer_qnty);
		if (str_replace("'", "", $txt_transfer_qnty) > $current_trans_stock) {
			echo "30**Transfer Quantity Not Allow More Then Stock Quantity.";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}

		if (str_replace("'", "", $cbo_transfer_criteria) == 2) {
			$sfrrsb_cond = '';
			if ($vs_data_arr['rack_balance'] == 1) {
				if ($vs_data_arr['store_method'] == 1) {
					$sfrrsb_cond = " and store_id = " . $cbo_store_name_to;
				} else if ($vs_data_arr['store_method'] == 2) {
					$sfrrsb_cond = " and store_id = " . $cbo_store_name_to . " and floor_id = " . $cbo_floor_to;
				} else if ($vs_data_arr['store_method'] == 3) {
					$sfrrsb_cond = " and store_id = " . $cbo_store_name_to . " and floor_id = " . $cbo_floor_to . " and room = " . $cbo_room_to;
				} else if ($vs_data_arr['store_method'] == 4) {
					$sfrrsb_cond = " and store_id = " . $cbo_store_name_to . " and floor_id = " . $cbo_floor_to . " and room = " . $cbo_room_to . " and rack = " . $txt_rack_to;
				} else if ($vs_data_arr['store_method'] == 5) {
					$sfrrsb_cond = " and store_id = " . $cbo_store_name_to . " and floor_id = " . $cbo_floor_to . " and room = " . $cbo_room_to . " and rack = " . $txt_rack_to . " and self = " . $txt_shelf_to;
				} else if ($vs_data_arr['store_method'] == 6) {
					$sfrrsb_cond = " and store_id = " . $cbo_store_name_to . " and floor_id = " . $cbo_floor_to . " and room = " . $cbo_room_to . " and rack = " . $txt_rack_to . " and self = " . $txt_shelf_to . " and bin_box = " . $cbo_bin_to;
				}
			} else {
				$sfrrsb_cond = " and store_id = " . $cbo_store_name_to;
			}

			$actual_issue_sql = "select sum((case when transaction_type in(2) then cons_quantity else 0 end)-(case when transaction_type in(4) then cons_quantity else 0 end)) as actual_issue_qty from inv_transaction where prod_id=" . $hidden_product_id . $sfrrsb_cond . "  and id>$update_trans_recv_id and item_category=1 and status_active=1 and is_deleted=0";
			$after_transfer_issueqty = sql_select($actual_issue_sql);
			foreach ($after_transfer_issueqty as $row) {
				$total_actual_issu_qty += $row[csf("actual_issue_qty")];
			}

			//echo "30**".$txt_transfer_qnty."<".$total_actual_issu_qty; die();
			if ($txt_transfer_qnty < $total_actual_issu_qty) { 	//txt_transfer_qnty, hidden_transfer_qnty
				echo "30**Issue found.\nIssue quantity = $total_actual_issu_qty\nCan not update transfer quantity less than issue quantity";
				disconnect($con);
				die;
			}
		}

		if (str_replace("'", "", $cbo_transfer_criteria) == 1) {
			$product_info = sql_select("select available_qnty,avg_rate_per_unit from product_details_master where id=$hidden_product_id");
			//$rate=return_field_value("avg_rate_per_unit","product_details_master","id=$cbo_item_desc");
			if (str_replace("'", "", $txt_transfer_qnty) > ($product_info[0][csf("available_qnty")] + str_replace("'", "", $hidden_transfer_qnty))) {
				echo "21**Transfer quantity can not be greater than available quantity. available quantity= " . ($product_info[0][csf("available_qnty")] + str_replace("'", "", $hidden_transfer_qnty));
				disconnect($con);
				die;
			}
		}

		if (str_replace("'", "", $update_id) != "") {
			$rcv_trans_id = str_replace("'", "", $update_trans_recv_id);
			$issue_trans_id = str_replace("'", "", $update_trans_issue_id);

			if ($variable_auto_rcv == 1) {
				$all_trans_id = $issue_trans_id . "," . $rcv_trans_id;
			} else {
				$all_trans_id = $issue_trans_id;
			}

			$duplicate = is_duplicate_field("b.id", " inv_item_transfer_mst a, inv_transaction b", "a.id=b.mst_id and a.id=$update_id and b.prod_id=$hidden_product_id and b.id not in($all_trans_id) and b.transaction_type in(5,6)");
			if ($duplicate == 1) {
				echo "20**Duplicate Product is Not Allow in Same Transfer Number.";
				disconnect($con);
				die;
			}
		}

		// echo "10**".$update_id;die;
		/*#### Stop not eligible field from update operation start ####*/
		// to_company*
		// $cbo_company_id_to."*".
		/*#### Stop not eligible field from update operation end ####*/

		$field_array_update = "challan_no*transfer_date*ready_to_approved*remarks*updated_by*update_date*purpose";
		$data_array_update = $txt_challan_no . "*" . $txt_transfer_date . "*" . $cbo_approved . "*" . $txt_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $cbo_purpose . "";

		$field_array_trans = "supplier_id*prod_id*origin_prod_id*transaction_date*store_id*floor_id*room*rack*self*bin_box*brand_id*order_qnty*order_rate*order_amount*cons_uom*cons_quantity*cons_rate*cons_amount*balance_qnty*balance_amount*updated_by*update_date*btb_lc_id*buyer_id*store_rate*store_amount";

		$field_array_dtls = "from_prod_id*to_prod_id*yarn_lot*brand_id*from_store*floor_id*room*rack*shelf*bin_box*to_store*to_floor_id*to_room*to_rack*to_shelf*to_bin_box*transfer_qnty*rate*transfer_value*rate_in_usd*transfer_value_in_usd*uom*no_of_bag*no_of_cone*weight_per_bag*fso_no*buyer_id*updated_by*update_date*store_rate*store_amount";

		$field_array_dtls_ac = "from_prod_id*to_prod_id*yarn_lot*brand_id*from_store*floor_id*room*rack*shelf*bin_box*to_store*to_floor_id*to_room*to_rack*to_shelf*to_bin_box*transfer_qnty*rate*transfer_value*rate_in_usd*transfer_value_in_usd*uom*no_of_bag*no_of_cone*weight_per_bag*fso_no*buyer_id*updated_by*update_date*store_rate*store_amount";
		$field_array_store = "last_issued_qnty*cons_qty*amount*updated_by*update_date";
		$data_array_store_rcv = "";
		$updateTransID_array = array();
		$update_trans_issue_id = str_replace("'", "", $update_trans_issue_id);
		$update_trans_recv_id = str_replace("'", "", $update_trans_recv_id);
		$hidden_product_id = str_replace("'", "", $hidden_product_id);
		$previous_from_prod_id = str_replace("'", "", $previous_from_prod_id);
		$previous_to_prod_id = str_replace("'", "", $previous_to_prod_id);

		$cbo_floor = str_replace("'", "", $cbo_floor);
		$cbo_room = str_replace("'", "", $cbo_room);
		$txt_rack = str_replace("'", "", $txt_rack);
		$txt_shelf = str_replace("'", "", $txt_shelf);
		$cbo_bin = str_replace("'", "", $cbo_bin);

		$cbo_floor_to = str_replace("'", "", $cbo_floor_to);
		$cbo_room_to = str_replace("'", "", $cbo_room_to);
		$txt_rack_to = str_replace("'", "", $txt_rack_to);
		$txt_shelf_to = str_replace("'", "", $txt_shelf_to);
		$cbo_bin_to = str_replace("'", "", $cbo_bin_to);

		$all_prod_id = $hidden_product_id . "," . $previous_from_prod_id . "," . $previous_to_prod_id;
		$prod_arr = array();
		$order_rate = 0;
		$order_amount = 0;
		$prodData = sql_select("select id, current_stock, avg_rate_per_unit, supplier_id, available_qnty from product_details_master where id in ($all_prod_id)");
		foreach ($prodData as $row) {
			$prod_arr[$row[csf('id')]]['st'] = $row[csf('current_stock')];
			$prod_arr[$row[csf('id')]]['rate'] = $row[csf('avg_rate_per_unit')];
			$prod_arr[$row[csf('id')]]['sid'] = $row[csf('supplier_id')];
			$prod_arr[$row[csf('id')]]['aq'] = $row[csf('available_qnty')];
		}

		$prev_data = sql_select("select b.transfer_qnty, b.transfer_value, b.store_rate, b.store_amount from inv_item_transfer_dtls b where b.id=$update_dtls_id and b.status_active=1");
		$prev_transfer_qnty = $prev_data[0][csf('transfer_qnty')];
		$prev_transfer_value = $prev_data[0][csf('transfer_value')];
		$prev_store_rate = $prev_data[0][csf('store_rate')];
		$prev_store_amount = $prev_data[0][csf('store_amount')];

		if (str_replace("'", "", $cbo_transfer_criteria) == 1) {
			$updateProdID_array = array();
			$field_array_adjust = "current_stock*avg_rate_per_unit*stock_value*available_qnty";

			if ($hidden_product_id == $previous_from_prod_id) {
				$adjust_curr_stock_from = $prod_arr[$previous_from_prod_id]['st'] + $prev_transfer_qnty - $txt_transfer_qnty;
				$adjust_curr_availlable_from = $prod_arr[$previous_from_prod_id]['aq'] + $prev_transfer_qnty - $txt_transfer_qnty;
				$cur_st_rate_from = $prod_arr[$previous_from_prod_id]['rate'];
				$cur_st_value_from = $adjust_curr_stock_from * $cur_st_rate_from;
				$updateProdID_array[] = $previous_from_prod_id;
				$data_array_adjust[$previous_from_prod_id] = explode("*", ("" . $adjust_curr_stock_from . "*" . number_format($cur_st_rate_from, 10, '.', '') . "*" . number_format($cur_st_value_from, 8, '.', '') . "*" . $adjust_curr_availlable_from));
			} else {
				$adjust_curr_stock_from = $prod_arr[$previous_from_prod_id]['st'] + $prev_transfer_qnty;
				$adjust_curr_availlable_from = $prod_arr[$previous_from_prod_id]['aq'] + $prev_transfer_qnty;
				$cur_st_rate_from = $prod_arr[$previous_from_prod_id]['rate'];
				$cur_st_value_from = $adjust_curr_stock_from * $cur_st_rate_from;
				$updateProdID_array[] = $previous_from_prod_id;
				$data_array_adjust[$previous_from_prod_id] = explode("*", ("" . $adjust_curr_stock_from . "*" . number_format($cur_st_rate_from, 10, '.', '') . "*" . number_format($cur_st_value_from, 8, '.', '') . "*" . $adjust_curr_availlable_from));

				$presentStock = $prod_arr[$hidden_product_id]['st'] - $txt_transfer_qnty;
				$presentAvaillable = $prod_arr[$hidden_product_id]['aq'] - $txt_transfer_qnty;
				$presentAvgRate = $prod_arr[$hidden_product_id]['rate'];
				$presentStockValue = $presentStock * $presentAvgRate;
				$updateProdID_array[] = $hidden_product_id;
				$data_array_adjust[$hidden_product_id] = explode("*", ("" . $presentStock . "*" . number_format($presentAvgRate, 10, '.', '') . "*" . number_format($presentStockValue, 8, '.', '') . "*" . $presentAvaillable));
			}

			$supplier_id = $prod_arr[$hidden_product_id]['sid'];
			$row_prod = sql_select("select id, current_stock, avg_rate_per_unit, available_qnty, dyed_type from product_details_master where company_id=$cbo_company_id_to and item_category_id=1 and supplier_id='$supplier_id' and product_name_details=$txt_item_desc and lot=$txt_yarn_lot and status_active=1 and is_deleted=0");

			if (count($row_prod) > 0) // Found previous product
			{
				$product_id = $row_prod[0][csf('id')];
				if ($product_id == $previous_to_prod_id) {
					$stock_qnty = $row_prod[0][csf('current_stock')];
					//$stock_qnty=$row_prod[0][csf('current_stock')]+str_replace("'", '',$hidden_transfer_qnty);
					$avg_rate_per_unit = $row_prod[0][csf('avg_rate_per_unit')];
					$curr_stock_qnty = $stock_qnty + $txt_transfer_qnty - $prev_transfer_qnty;
					//$curr_stock_qnty=$stock_qnty-str_replace("'", '',$txt_transfer_qnty);
					$stock_value = $curr_stock_qnty * $avg_rate_per_unit;
					$curr_available_qnty = $row_prod[0][csf('available_qnty')] + $txt_transfer_qnty - $prev_transfer_qnty;

					if ($curr_stock_qnty < 0) {
						echo "30**Stock cannot be less than zero.";
						//check_table_status( $_SESSION['menu_id'],0);
						disconnect($con);
						die;
					}

					if ($variable_auto_rcv == 1) // if auto receive yes(1), then no need to acknowledgement
					{
						$updateProdID_array[] = $product_id;
						$data_array_adjust[$product_id] = explode("*", ("" . $curr_stock_qnty . "*" . number_format($avg_rate_per_unit, 10, '.', '') . "*" . number_format($stock_value, 8, '.', '') . "*" . $curr_available_qnty));
					}
				} else {
					$stock_qnty = $row_prod[0][csf('current_stock')];
					$avg_rate_per_unit = $row_prod[0][csf('avg_rate_per_unit')];
					$curr_stock_qnty = $stock_qnty + $txt_transfer_qnty;
					$stock_value = $curr_stock_qnty * $avg_rate_per_unit;
					$curr_availlable_qnty = $row_prod[0][csf('available_qnty')] + $txt_transfer_qnty;

					$adjust_curr_stock_to = $stock_qnty - $prev_transfer_qnty;
					$adjust_curr_availlable_to = $row_prod[0][csf('available_qnty')] - $prev_transfer_qnty;
					$cur_st_value_to = $adjust_curr_stock_to * $avg_rate_per_unit;

					if ($variable_auto_rcv == 1) // 
					{
						$updateProdID_array[] = $previous_to_prod_id;
						$data_array_adjust[$previous_to_prod_id] = explode("*", ("" . $adjust_curr_stock_to . "*" . number_format($avg_rate_per_unit, 10, '.', '') . "*" . number_format($cur_st_value_to, 8, '.', '') . "*" . $adjust_curr_availlable_to));
					}

					if ($adjust_curr_stock_to < 0) {
						echo "30**Stock cannot be less than zero.";
						//check_table_status( $_SESSION['menu_id'],0);
						disconnect($con);
						die;
					}
					if ($variable_auto_rcv == 1) {
						$field_array_prod_update = "avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*available_qnty*updated_by*update_date";
						$data_array_prod_update = number_format($avg_rate_per_unit, 10, '.', '') . "*" . $txt_transfer_qnty . "*" . $curr_stock_qnty . "*" . number_format($stock_value, 8, '.', '') . "*" . $curr_availlable_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
					}
				}
			} else // Create new product
			{
				$adjust_curr_stock_to = $prod_arr[$previous_to_prod_id]['st'] - str_replace("'", '', $hidden_transfer_qnty);
				$adjust_curr_availlable_to = $prod_arr[$previous_to_prod_id]['aq'] - str_replace("'", '', $hidden_transfer_qnty);
				$avg_rate_per_unit = $prod_arr[$previous_to_prod_id]['rate'];
				$cur_st_value_to = $adjust_curr_stock_to * $avg_rate_per_unit;

				if ($variable_auto_rcv == 1) // 
				{
					$updateProdID_array[] = $previous_to_prod_id;
					$data_array_adjust[$previous_to_prod_id] = explode("*", ("" . $adjust_curr_stock_to . "*" . $avg_rate_per_unit . "*" . $cur_st_value_to . "*" . $adjust_curr_availlable_to));
				}

				if ($adjust_curr_stock_to < 0) {
					echo "30**Stock cannot be less than zero.";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}

				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$curr_stock_qnty = str_replace("'", "", $txt_transfer_qnty);
				//$avg_rate_per_unit=$prod_arr[$hidden_product_id]['rate'];
				$avg_rate_per_unit = str_replace("'", "", $txt_rate);
				$stock_value = $curr_stock_qnty * $avg_rate_per_unit;
				$available_qnty = str_replace("'", "", $txt_transfer_qnty);

				if ($variable_auto_rcv == 1) // if auto receive Yes(1), then no need to acknowledgement
				{
					$sql_prod_insert = "insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, available_qnty, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date, dyed_type, is_within_group)
					select
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, $available_qnty, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand,'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "', dyed_type, is_within_group from product_details_master where id=$hidden_product_id";
				} else {
					$sql_prod_insert = "insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, available_qnty, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date, dyed_type, is_within_group)
					select
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand,'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "', dyed_type, is_within_group from product_details_master where id=$hidden_product_id";
				}
			}

			$store_up_id = 0;
			if ($variable_store_wise_rate == 1) {
				$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$previous_from_prod_id and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");
				if (count($sql_store) < 1) {
					echo "20**No Data Found.";
					disconnect($con);
					die;
				} elseif (count($sql_store) > 1) {
					echo "20**Duplicate Product is Not Allow in Same REF Number.";
					disconnect($con);
					die;
				} else {
					$store_presentStock = $store_presentStockValue = $store_presentAvgRate = 0;
					foreach ($sql_store as $result) {
						$store_up_id = $result[csf("id")];
						$store_presentStock	= $result[csf("current_stock")];
						$store_presentStockValue = $result[csf("stock_value")];
						$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
					}

					$currentStock_store		= $store_presentStock + $prev_transfer_qnty - $txt_transfer_qnty;
					$currentValue_store		= $store_presentStockValue + $prev_store_amount - $issue_store_value;
					if ($store_up_id) {
						$store_id_arr[] = $store_up_id;
						$data_array_store[$store_up_id] = explode("*", ("" . $txt_transfer_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					}
				}
			}

			if ($variable_auto_rcv == 1 && $variable_store_wise_rate == 1 && $variable_store_wise_rate_to == 1) {
				if ($product_id == $previous_to_prod_id) {
					$sql_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$previous_to_prod_id and category_id=1 and store_id=$cbo_store_name_to and company_id=$cbo_company_id_to");
					$store_up_id_to = 0;
					if (count($sql_store_rcv) < 1) {
						echo "20**No Data Found.";
						disconnect($con);
						die;
					} elseif (count($sql_store_rcv) > 1) {
						echo "20**Duplicate Product is Not Allow in Same REF Number.";
						disconnect($con);
						die;
					} else {
						$store_presentStock = $store_presentStockValue = $store_presentAvgRate = 0;
						foreach ($sql_store_rcv as $result) {
							$store_up_id_to = $result[csf("id")];
							$store_presentStock	= $result[csf("current_stock")];
							$store_presentStockValue = $result[csf("stock_value")];
							$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
						}
						$currentStock_store		= ($store_presentStock - $prev_transfer_qnty) + $txt_transfer_qnty;
						$currentValue_store		= ($store_presentStockValue - $prev_store_amount) + $issue_store_value;
						if ($store_up_id_to) {
							$store_id_arr[] = $store_up_id_to;
							$data_array_store[$store_up_id_to] = explode("*", ("" . $txt_transfer_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
						}
					}
				} else {
					$sql_store_rcv_prev = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$previous_to_prod_id and category_id=1 and store_id=$cbo_store_name_to and company_id=$cbo_company_id_to");
					$store_up_id_to = 0;
					$store_presentStock = $store_presentStockValue = $store_presentAvgRate = 0;
					foreach ($sql_store_rcv_prev as $result) {
						$store_up_id_to = $result[csf("id")];
						$store_presentStock	= $result[csf("current_stock")];
						$store_presentStockValue = $result[csf("stock_value")];
						$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
					}
					$currentStock_store		= ($store_presentStock - $prev_transfer_qnty);
					$currentValue_store		= ($store_presentStockValue - $prev_store_amount);
					if ($store_up_id_to) {
						$store_id_arr[] = $store_up_id_to;
						$data_array_store[$store_up_id_to] = explode("*", ("" . $txt_transfer_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					}

					$sql_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$product_id and category_id=1 and store_id=$cbo_store_name_to and company_id=$cbo_company_id_to");
					$store_up_id_to = 0;
					if (count($sql_store_rcv) < 1) {
						$field_array_store_rcv = "id,company_id,store_id,category_id,prod_id,cons_qty,rate,amount,last_purchased_qnty,inserted_by,insert_date,lot,first_receive_date,last_receive_date";

						$sdtlsid = return_next_id("id", "inv_store_wise_yarn_qty_dtls", 1);
						$data_array_store_rcv = "(" . $sdtlsid . "," . $cbo_company_id_to . "," . $cbo_store_name_to . ",1," . $product_id . "," . $txt_transfer_qnty . "," . number_format($store_item_rate, 10, '.', '') . "," . number_format($issue_store_value, 8, '.', '') . "," . $txt_transfer_qnty . ",'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "'," . $txt_lot . "," . $txt_transfer_date . "," . $txt_transfer_date . ")";
					} elseif (count($sql_store_rcv) > 1) {
						echo "20**Duplicate Product is Not Allow in Same REF Number.";
						disconnect($con);
						die;
					} else {
						$store_presentStock = $store_presentStockValue = $store_presentAvgRate = 0;
						foreach ($sql_store_rcv as $result) {
							$store_up_id_to = $result[csf("id")];
							$store_presentStock	= $result[csf("current_stock")];
							$store_presentStockValue = $result[csf("stock_value")];
							$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
						}
						$currentStock_store		= $store_presentStock + $txt_transfer_qnty;
						$currentValue_store		= $store_presentStockValue + $issue_store_value;
						if ($store_up_id_to) {
							$store_id_arr[] = $store_up_id_to;
							$data_array_store[$store_up_id_to] = explode("*", ("" . $txt_transfer_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
						}
					}
				}
			}

			//----------------Check Last Receive Date for Transfer Out---------------->>>>>>>
			if ($update_trans_recv_id != "") {
				$update_trans_recv_id_cond = " and id <> $update_trans_recv_id ";
				$update_trans_recv_id_cond2 = " and id not in ($update_trans_recv_id , $update_trans_issue_id) ";
			} else {
				$update_trans_recv_id_cond2 = " and id not in ($update_trans_issue_id) ";
			}
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and store_id = $cbo_store_name $update_trans_recv_id_cond  and status_active = 1", "max_date");
			if ($max_recv_date != "") {
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
				if ($transfer_date < $max_recv_date) {
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
					disconnect($con);
					die;
				}
			}

			//----------------Check Last Issue Date for Transfer In---------------->>>>>>>
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id = $cbo_store_name_to $update_trans_recv_id_cond2 and status_active = 1", "max_date");
			if ($max_issue_date != "") {
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
				/*
				if ($transfer_date < $max_issue_date)
				{
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}*/
			}

			$recv_qty = 0;
			$recv_amnt = 0;
			$sql_receive = "select a.receive_date, a.currency_id, sum(b.order_qnty) as qty, sum(b.order_qnty*b.order_rate) as amnt from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=1 and a.item_category=1 and b.transaction_type=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id=$hidden_product_id group by a.receive_date, a.currency_id";
			$resultReceive = sql_select($sql_receive);
			foreach ($resultReceive as $row) {
				$recv_qty += $row[csf('qty')];
				if ($row[csf('currency_id')] == 1) {
					$exchange_rate = set_conversion_rate(2, $row[csf('receive_date')]);
					//echo "10**".$exchange_rate;die;
					if ($exchange_rate <= 0) {
						$exchange_rate = 76;
					}
					$recv_amnt += $row[csf('amnt')] / $exchange_rate;
				} else {
					$recv_amnt += $row[csf('amnt')];
				}
			}

			$sql_trans = "select sum(order_qnty) as qty, sum(order_amount) as amnt from inv_transaction where transaction_type=5 and item_category=1 and status_active=1 and is_deleted=0 and prod_id=$hidden_product_id";
			$resultTrans = sql_select($sql_trans);
			$trnas_recv_qty = $resultTrans[0][csf('qty')];
			$trnas_recv_amnt = $resultTrans[0][csf('amnt')];

			$tot_recv_qty = $recv_qty + $trnas_recv_qty;
			$tot_recv_amnt = $recv_amnt + $trnas_recv_amnt;

			$order_rate = $tot_recv_amnt / $tot_recv_qty;
			$order_amount = $order_rate * str_replace("'", "", $txt_transfer_qnty);

			$updateTransID_array[] = $update_trans_issue_id;
			$updateTransID_data[$update_trans_issue_id] = explode("*", ("'" . $supplier_id . "'*'" . $hidden_product_id . "'*" . $origin_product_id . "*" . $txt_transfer_date . "*" . $cbo_store_name . "*" . $cbo_floor . "*" . $cbo_room . "*" . $txt_rack . "*" . $txt_shelf . "*" . $cbo_bin . "*" . $hide_brand_id . "*'0'*'0'*'0'*" . $cbo_uom . "*" . $txt_transfer_qnty . "*" . number_format($txt_rate, 10, '.', '') . "*" . number_format($txt_transfer_value, 8, '.', '') . "*'0'*'0'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $txt_btb_lc_id . "*" . $cbo_buyer_name . "*" . number_format($store_item_rate, 10, '.', '') . "*" . number_format($issue_store_value, 8, '.', '') . ""));

			if ($variable_auto_rcv == 1) // if auto receive Yes(1), then no need to acknowledgement
			{
				$updateTransID_array[] = $update_trans_recv_id;
				$updateTransID_data[$update_trans_recv_id] = explode("*", ("'" . $supplier_id . "'*'" . $product_id . "'*" . $origin_product_id . "*" . $txt_transfer_date . "*" . $cbo_store_name_to . "*" . $cbo_floor_to . "*" . $cbo_room_to . "*" . $txt_rack_to . "*" . $txt_shelf_to . "*" . $cbo_bin_to . "*" . $hide_brand_id . "*'" . $txt_transfer_qnty . "'*'" . number_format($order_rate, 10, '.', '') . "'*'" . number_format($order_amount, 8, '.', '') . "'*" . $cbo_uom . "*" . $txt_transfer_qnty . "*" . number_format($txt_rate, 10, '.', '') . "*" . number_format($txt_transfer_value, 8, '.', '') . "*'" . $txt_transfer_qnty . "'*'" . number_format($txt_transfer_value, 8, '.', '') . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $txt_btb_lc_id . "*" . $cbo_buyer_name . "*" . number_format($store_item_rate, 10, '.', '') . "*" . number_format($issue_store_value, 8, '.', '') . ""));
			}
			// print_r($updateTransID_data);die;

			$data_array_dtls = $hidden_product_id . "*'" . $product_id . "'*" . $txt_yarn_lot . "*" . $hide_brand_id . "*" . $cbo_store_name . "*" . $cbo_floor . "*" . $cbo_room . "*" . $txt_rack . "*" . $txt_shelf . "*" . $cbo_bin . "*" . $cbo_store_name_to . "*" . $cbo_floor_to . "*" . $cbo_room_to . "*" . $txt_rack_to . "*" . $txt_shelf_to . "*" . $cbo_bin_to . "*'" . $txt_transfer_qnty . "'*" . number_format($txt_rate, 10, '.', '') . "*" . number_format($txt_transfer_value, 8, '.', '') . "*'" . number_format($order_rate, 10, '.', '') . "'*'" . number_format($order_amount, 8, '.', '') . "'*" . $cbo_uom . "*" . $txt_no_of_bag . "*" . $txt_no_of_cone . "*" . $txt_weight_per_bag . "*" . $txt_fso_no . "*" . $cbo_buyer_name . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . number_format($store_item_rate, 10, '.', '') . "*" . number_format($issue_store_value, 8, '.', '') . "";

			if ($variable_auto_rcv == 2) // acknowledgement_dtls_table
			{
				$data_array_dtls_ac = $hidden_product_id . "*'" . $product_id . "'*" . $txt_yarn_lot . "*" . $hide_brand_id . "*" . $cbo_store_name . "*" . $cbo_floor . "*" . $cbo_room . "*" . $txt_rack . "*" . $txt_shelf . "*" . $cbo_bin . "*" . $cbo_store_name_to . "*" . $cbo_floor_to . "*" . $cbo_room_to . "*" . $txt_rack_to . "*" . $txt_shelf_to . "*" . $cbo_bin_to . "*'" . $txt_transfer_qnty . "'*" . number_format($txt_rate, 10, '.', '') . "*" . number_format($txt_transfer_value, 8, '.', '') . "*'" . number_format($order_rate, 10, '.', '') . "'*'" . number_format($order_amount, 8, '.', '') . "'*" . $cbo_uom . "*" . $txt_no_of_bag . "*" . $txt_no_of_cone . "*" . $txt_weight_per_bag . "*" . $txt_fso_no . "*" . $cbo_buyer_name . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . number_format($store_item_rate, 10, '.', '') . "*" . number_format($issue_store_value, 8, '.', '') . "";
			}

			//transaction table START--------------------------//
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_issue_id and b.entry_form=10 and a.item_category=1");
			$updateID_array = array();
			$update_data = array();
			foreach ($sql as $result) {
				$adjBalance = $result[csf("balance_qnty")] + $result[csf("issue_qnty")];
				$adjAmount = $result[csf("balance_amount")] + $result[csf("amount")];
				$updateID_array[] = $result[csf("id")];
				$update_data[$result[csf("id")]] = explode("*", ("" . $adjBalance . "*" . $adjAmount . "*'" . $user_id . "'*'" . $pc_date_time . "'"));

				$trans_data_array[$result[csf("id")]]['qnty'] = $adjBalance;
				$trans_data_array[$result[csf("id")]]['amnt'] = $adjAmount;
			}


			//print_r($update_data);
			//LIFO/FIFO Start-----------------------------------------------//
			$isLIFOfifo = return_field_value("store_method", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=17 and item_category_id=1 and status_active=1 and is_deleted=0");
			if ($isLIFOfifo == 2) $cond_lifofifo = " DESC";
			else $cond_lifofifo = " ASC";

			$transfer_qnty = str_replace("'", "", $txt_transfer_qnty);
			$transfer_value = str_replace("'", "", $txt_transfer_value);
			$field_array_mrr = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			//$field_array_mrr2= "id,recv_trans_id,entry_form,prod_id,item_return_qty,amount,transfer_criteria,inserted_by,insert_date";

			//echo "10**select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and $balance_cond and transaction_type in (1,4,5) and item_category=1 order by transaction_date $cond_lifofifo";print_r($trans_data_array);die;

			$transId = implode(",", $updateID_array);
			if ($hidden_product_id == $previous_from_prod_id) $balance_cond = "(balance_qnty>0 or id in($transId))";
			else $balance_cond = "balance_qnty>0";

			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and store_id=$cbo_store_name and $balance_cond and transaction_type in (1,4,5) and item_category=1 order by transaction_date,id $cond_lifofifo");

			foreach ($sql as $result) {
				$recv_trans_id = $result[csf("id")]; // this row will be updated
				if ($trans_data_array[$recv_trans_id]['qnty'] == "") {
					$balance_qnty = $result[csf("balance_qnty")];
					$balance_amount = $result[csf("balance_amount")];
				} else {
					$balance_qnty = $trans_data_array[$recv_trans_id]['qnty'];
					$balance_amount = $trans_data_array[$recv_trans_id]['amnt'];
				}
				//echo $balance_qnty."**";
				$cons_rate = $result[csf("cons_rate")];
				if ($cons_rate == "") {
					$cons_rate = $txt_rate;
				}

				$transferQntyBalance = $balance_qnty - $transfer_qnty; // minus issue qnty
				$transferStockBalance = $balance_amount - ($transfer_qnty * $cons_rate);
				if ($transferQntyBalance >= 0) {
					$amount = $transfer_qnty * $cons_rate;
					//for insert
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					if ($data_array_mrr != "") $data_array_mrr .= ",";
					$data_array_mrr .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $update_trans_issue_id . ",10," . $hidden_product_id . "," . $transfer_qnty . "," . $cons_rate . "," . $amount . ",'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "')";
					//for update
					if (!in_array($recv_trans_id, $updateID_array)) {
						$updateID_array[] = $recv_trans_id;
					}

					$update_data[$recv_trans_id] = explode("*", ("" . $transferQntyBalance . "*" . $transferStockBalance . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					break;
				} else if ($transferQntyBalance < 0) {
					$transferQntyBalance = $transfer_qnty - $balance_qnty;
					$transfer_qnty = $balance_qnty;
					$amount = $transfer_qnty * $cons_rate;

					//for insert
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					if ($data_array_mrr != "") $data_array_mrr .= ",";
					$data_array_mrr .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $update_trans_issue_id . ",10," . $hidden_product_id . "," . $balance_qnty . "," . $cons_rate . "," . $amount . ",'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "')";
					//for update
					if (!in_array($recv_trans_id, $updateID_array)) {
						$updateID_array[] = $recv_trans_id;
					}

					$update_data[$recv_trans_id] = explode("*", ("0*0*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					$transfer_qnty = $transferQntyBalance;
				}
			} //end foreach
		} else // Store to store data update
		{
			//----------------Check Last Receive Date for Transfer Out---------------->>>>>>>
			if ($update_trans_recv_id != "") {
				$update_trans_recv_id_cond = " and id <> $update_trans_recv_id ";
				$update_trans_recv_id_cond2 = " and id not in ($update_trans_recv_id , $update_trans_issue_id) ";
			} else {
				$update_trans_recv_id_cond2 = " and id not in ($update_trans_issue_id) ";
			}

			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (1,4,5) and store_id = $cbo_store_name $update_trans_recv_id_cond  and status_active = 1", "max_date");
			if ($max_recv_date != "") {
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_recv_date) {
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}
			// echo "10**".$update_trans_recv_id."**";die;
			//----------------Check Last Transaction Date for Transfer In---------------->>>>>>>
			$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and store_id = $cbo_store_name_to $update_trans_recv_id_cond2  and status_active = 1", "max_date");
			if ($max_transaction_date != "") {
				$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_transaction_date) {/*
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;*/
				}
			}

			$updateTransID_array[] = $update_trans_issue_id;
			$updateTransID_data[$update_trans_issue_id] = explode("*", ("0*'" . $hidden_product_id . "'*" . $origin_product_id . "*" . $txt_transfer_date . "*" . $cbo_store_name . "*" . $cbo_floor . "*" . $cbo_room . "*" . $txt_rack . "*" . $txt_shelf . "*" . $cbo_bin . "*" . $hide_brand_id . "*0*0*0*" . $cbo_uom . "*" . $txt_transfer_qnty . "*" . number_format($txt_rate, 10, '.', '') . "*" . number_format($txt_transfer_value, 8, '.', '') . "*0*0*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $txt_btb_lc_id . "*" . $cbo_buyer_name . "*" . number_format($store_item_rate, 10, '.', '') . "*" . number_format($issue_store_value, 8, '.', '') . ""));

			if ($variable_auto_rcv == 1) // if auto receive Yes(1), then no need to acknowledgement
			{
				$updateTransID_array[] = $update_trans_recv_id;
				$updateTransID_data[$update_trans_recv_id] = explode("*", ("0*'" . $hidden_product_id . "'*" . $origin_product_id . "*" . $txt_transfer_date . "*" . $cbo_store_name_to . "*" . $cbo_floor_to . "*" . $cbo_room_to . "*" . $txt_rack_to . "*" . $txt_shelf_to . "*" . $cbo_bin_to . "*" . $hide_brand_id . "*0*0*0*" . $cbo_uom . "*" . $txt_transfer_qnty . "*" . number_format($txt_rate, 10, '.', '') . "*" . number_format($txt_transfer_value, 8, '.', '') . "*" . $txt_transfer_qnty . "*" . number_format($txt_transfer_value, 8, '.', '') . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $txt_btb_lc_id . "*" . $cbo_buyer_name . "*" . number_format($store_item_rate, 10, '.', '') . "*" . number_format($issue_store_value, 8, '.', '') . ""));
			}

			$data_array_dtls = $hidden_product_id . "*" . $hidden_product_id . "*" . $txt_yarn_lot . "*" . $hide_brand_id . "*" . $cbo_store_name . "*" . $cbo_floor . "*" . $cbo_room . "*" . $txt_rack . "*" . $txt_shelf . "*" . $cbo_bin . "*" . $cbo_store_name_to . "*" . $cbo_floor_to . "*" . $cbo_room_to . "*" . $txt_rack_to . "*" . $txt_shelf_to . "*" . $cbo_bin_to . "*" . $txt_transfer_qnty . "*" . number_format($txt_rate, 10, '.', '') . "*" . number_format($txt_transfer_value, 8, '.', '') . "*0*0*" . $cbo_uom . "*" . $txt_no_of_bag . "*" . $txt_no_of_cone . "*" . $txt_weight_per_bag . "*" . $txt_fso_no . "*" . $cbo_buyer_name . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . number_format($store_item_rate, 10, '.', '') . "*" . number_format($issue_store_value, 8, '.', '') . "";

			if ($variable_auto_rcv == 2) // acknowledgement_dtls_table
			{
				$data_array_dtls_ac = $hidden_product_id . "*" . $hidden_product_id . "*" . $txt_yarn_lot . "*" . $hide_brand_id . "*" . $cbo_store_name . "*" . $cbo_floor . "*" . $cbo_room . "*" . $txt_rack . "*" . $txt_shelf . "*" . $cbo_bin . "*" . $cbo_store_name_to . "*" . $cbo_floor_to . "*" . $cbo_room_to . "*" . $txt_rack_to . "*" . $txt_shelf_to . "*" . $cbo_bin_to . "*" . $txt_transfer_qnty . "*" . number_format($txt_rate, 10, '.', '') . "*" . number_format($txt_transfer_value, 8, '.', '') . "*0*0*" . $cbo_uom . "*" . $txt_no_of_bag . "*" . $txt_no_of_cone . "*" . $txt_weight_per_bag . "*" . $txt_fso_no . "*" . $cbo_buyer_name . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . number_format($store_item_rate, 10, '.', '') . "*" . number_format($issue_store_value, 8, '.', '') . "";
			}


			$store_up_id = 0;
			if ($variable_store_wise_rate == 1) {
				$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$hidden_product_id and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");

				if (count($sql_store) < 1) {
					echo "20**No Data Found.";
					disconnect($con);
					die;
				} elseif (count($sql_store) > 1) {
					echo "20**Duplicate Product is Not Allow in Same REF Number.";
					disconnect($con);
					die;
				} else {
					$store_presentStock = $store_presentStockValue = $store_presentAvgRate = 0;
					foreach ($sql_store as $result) {
						$store_up_id = $result[csf("id")];
						$store_presentStock	= $result[csf("current_stock")];
						$store_presentStockValue = $result[csf("stock_value")];
						$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
					}

					$currentStock_store		= $store_presentStock + $prev_transfer_qnty - $txt_transfer_qnty;
					$currentValue_store		= $store_presentStockValue + $prev_store_amount - $issue_store_value;
					if ($store_up_id) {
						$store_id_arr[] = $store_up_id;
						$data_array_store[$store_up_id] = explode("*", ("" . $txt_transfer_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					}
				}
			}

			if ($variable_auto_rcv == 1 && $variable_store_wise_rate == 1 && $variable_store_wise_rate_to == 1) {
				$sql_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$hidden_product_id and category_id=1 and store_id=$cbo_store_name_to and company_id=$cbo_company_id");
				$store_up_id_to = 0;
				if (count($sql_store_rcv) < 1) {
					echo "20**No Data Found.";
					disconnect($con);
					die;
				} elseif (count($sql_store_rcv) > 1) {
					echo "20**Duplicate Product is Not Allow in Same REF Number.";
					disconnect($con);
					die;
				} else {
					$store_presentStock = $store_presentStockValue = $store_presentAvgRate = 0;
					foreach ($sql_store_rcv as $result) {
						$store_up_id_to = $result[csf("id")];
						$store_presentStock	= $result[csf("current_stock")];
						$store_presentStockValue = $result[csf("stock_value")];
						$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
					}
					$currentStock_store		= ($store_presentStock - $prev_transfer_qnty) + $txt_transfer_qnty;
					$currentValue_store		= ($store_presentStockValue - $prev_store_amount) + $issue_store_value;
					if ($store_up_id_to) {
						$store_id_arr[] = $store_up_id_to;
						$data_array_store[$store_up_id_to] = explode("*", ("" . $txt_transfer_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					}
				}
			}

			//LIFO/FIFO Start-----------------------------------------------//
			$isLIFOfifo = return_field_value("store_method", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=17 and item_category_id=1 and status_active=1 and is_deleted=0");
			if ($isLIFOfifo == 2) $cond_lifofifo = " DESC";
			else $cond_lifofifo = " ASC";

			$transfer_qnty = str_replace("'", "", $txt_transfer_qnty);
			$transfer_value = str_replace("'", "", $txt_transfer_value);
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$field_array_mrr = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";

			if ($rcv_trans_id != "") {
				$rcv_trans_id_cond = " or id in($rcv_trans_id) ";
			}
			if ($hidden_product_id == $previous_from_prod_id) $balance_cond = "(balance_qnty>0 $rcv_trans_id_cond)";
			else $balance_cond = "balance_qnty>0";

			$sql_mrr = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_issue_id and b.entry_form=10 and a.item_category=1 order by a.transaction_date,a.id $cond_lifofifo");
			$updateID_array = array();
			$update_data = array();
			foreach ($sql_mrr as $result) {
				$adjBalance = $result[csf("balance_qnty")] + $result[csf("issue_qnty")];
				$adjAmount = $result[csf("balance_amount")] + $result[csf("amount")];

				$updateID_array[] = $result[csf("id")];
				$update_data[$result[csf("id")]] = explode("*", ("" . $adjBalance . "*" . $adjAmount . "*'" . $user_id . "'*'" . $pc_date_time . "'"));

				$trans_data_array[$result[csf("id")]]['qnty'] = $adjBalance;
				$trans_data_array[$result[csf("id")]]['amnt'] = $adjAmount;
			}

			//$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and store_id=$cbo_store_name and $balance_cond and transaction_type in (1,4,5) and item_category=1 order by transaction_date $cond_lifofifo");

			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and store_id=$cbo_store_name and transaction_type in (1,4,5) and item_category=1 order by transaction_date,id $cond_lifofifo");

			foreach ($sql as $result) {
				$recv_trans_id = $result[csf("id")]; // this row will be updated

				if ($trans_data_array[$recv_trans_id]['qnty'] == "") {
					$balance_qnty = $result[csf("balance_qnty")];
					$balance_amount = $result[csf("balance_amount")];
				} else {
					$balance_qnty = $trans_data_array[$recv_trans_id]['qnty'];
					$balance_amount = $trans_data_array[$recv_trans_id]['amnt'];
				}

				//echo $balance_qnty."**";
				$cons_rate = $result[csf("cons_rate")];
				if ($cons_rate == "") {
					$cons_rate = $txt_rate;
				}

				$transferQntyBalance = $balance_qnty - $transfer_qnty; // minus issue qnty
				$transferStockBalance = $balance_amount - ($transfer_qnty * $cons_rate);

				if ($transferQntyBalance >= 0) {
					$amount = $transfer_qnty * $cons_rate;
					//for insert
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					if ($data_array_mrr != "") $data_array_mrr .= ",";
					$data_array_mrr .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $update_trans_issue_id . ",10," . $hidden_product_id . "," . $transfer_qnty . "," . $cons_rate . "," . $amount . ",'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "')";

					//for update
					if (!in_array($recv_trans_id, $updateID_array)) {
						$updateID_array[] = $recv_trans_id;
					}

					$update_data[$recv_trans_id] = explode("*", ("" . $transferQntyBalance . "*" . $transferStockBalance . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					break;
				} else if ($transferQntyBalance < 0) {
					$transferQntyBalance = $transfer_qnty - $balance_qnty;
					$transfer_qnty = $balance_qnty;
					$amount = $transfer_qnty * $cons_rate;

					//for insert
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					if ($data_array_mrr != "") $data_array_mrr .= ",";
					$data_array_mrr .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $update_trans_issue_id . ",10," . $hidden_product_id . "," . $balance_qnty . "," . $cons_rate . "," . $amount . ",'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "')";
					//for update
					if (!in_array($recv_trans_id, $updateID_array)) {
						$updateID_array[] = $recv_trans_id;
					}

					$update_data[$recv_trans_id] = explode("*", ("0*0*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					$transfer_qnty = $transferQntyBalance;
				}
			}
			//end foreach
			//LIFO/FIFO End-----------------------------------------------//
		}

		$rID = $rID2 = $rID3 = $rID4 = $query = $query2 = $mrrWiseIssueID = $prodUpdate_adjust = $prod = $storeUpID = $storeInsID = true;
		$rID = sql_update("inv_item_transfer_mst", $field_array_update, $data_array_update, "id", $update_id, 0);
		$rID2 = execute_query(bulk_update_sql_statement("inv_transaction", "id", $field_array_trans, $updateTransID_data, $updateTransID_array), 0);
		$rID3 = sql_update("inv_item_transfer_dtls", $field_array_dtls, $data_array_dtls, "id", $update_dtls_id, 0);
		if ($variable_auto_rcv == 2) // inv_item_transfer_dtls_ac
		{
			$rID4 = sql_update("inv_item_transfer_dtls_ac", $field_array_dtls_ac, $data_array_dtls_ac, "dtls_id", $update_dtls_id, 0);
		}
		//transaction table stock update here------------------------//
		$query = execute_query(bulk_update_sql_statement("inv_transaction", "id", $update_array, $update_data, $updateID_array), 0);
		//echo "10**".bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array); die();
		$query2 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_trans_issue_id and entry_form=10", 0);
		$mrrWiseIssueID = sql_insert("inv_mrr_wise_issue_details", $field_array_mrr, $data_array_mrr, 0);

		if (str_replace("'", "", $cbo_transfer_criteria) == 1) {
			$prodUpdate_adjust = execute_query(bulk_update_sql_statement("product_details_master", "id", $field_array_adjust, $data_array_adjust, $updateProdID_array), 0);
			if (count($row_prod) > 0) {
				if ($product_id != $previous_to_prod_id) {
					$prod = sql_update("product_details_master", $field_array_prod_update, $data_array_prod_update, "id", $product_id, 0);
				}
			} else {
				$prod = execute_query($sql_prod_insert, 0);
			}
		}

		if (count($store_id_arr) > 0 && $variable_store_wise_rate == 1) {
			$storeUpID = execute_query(bulk_update_sql_statement("inv_store_wise_yarn_qty_dtls", "id", $field_array_store, $data_array_store, $store_id_arr));
		}
		if ($data_array_store_rcv != "" && $variable_store_wise_rate == 1 && $variable_store_wise_rate_to == 1) {
			$storeInsID = sql_insert("inv_store_wise_yarn_qty_dtls", $field_array_store_rcv, $data_array_store_rcv, 1);
		}

		//echo "10**$rID=$rID2=$rID3=$rID4=$query=$query2=$mrrWiseIssueID=$prodUpdate_adjust=$prod=$storeUpID=$storeInsID"; die();

		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3 && $rID4 && $query && $query2 && $mrrWiseIssueID && $prodUpdate_adjust && $prod && $storeUpID && $storeInsID) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $update_id) . "**" . str_replace("'", "", $txt_system_id) . "**0";
			} else {
				mysql_query("ROLLBACK");
				echo "6**0**" . "&nbsp;" . "**1";
			}
		} else if ($db_type == 2 || $db_type == 1) {

			if ($rID && $rID2 && $rID3 && $rID4 && $query && $query2 && $mrrWiseIssueID && $prodUpdate_adjust && $prod && $storeUpID && $storeInsID) {
				oci_commit($con);
				echo "1**" . str_replace("'", "", $update_id) . "**" . str_replace("'", "", $txt_system_id) . "**0";
			} else {
				oci_rollback($con);
				echo "6**0**" . "&nbsp;" . "**1";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
}

if ($action == "yarn_transfer_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);

	$sql = "select a.id, a.transfer_system_id, a.challan_no, a.transfer_date, a.transfer_criteria, a.item_category, a.to_company, a.from_order_id, a.to_order_id, a.item_category, a.remarks, a.inserted_by, b.fso_no from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and a.id='" . $data[1] . "' and a.company_id='" . $data[0] . "'";
	//echo $sql;die;
	$dataArray = sql_select($sql);

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=1", "id", "product_name_details");
	$brand_arr = return_library_array("select id, brand_name from   lib_brand", "id", "brand_name");
	$user_fullname_arr = return_library_array("select id, USER_FULL_NAME from USER_PASSWD", "id", "USER_FULL_NAME");
	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", 'floor_room_rack_id', 'floor_room_rack_name');


	//for sales booking no
	$sales_booking_no = '';
	if ($dataArray[0][csf('fso_no')] != '') {
		//$sales_booking_rslt = sql_select("SELECT sales_booking_no AS SALES_BOOKING_NO FROM fabric_sales_order_mst WHERE job_no='".$dataArray[0][csf('fso_no')]."' AND status_active=1 AND is_deleted=0 AND company_id=".$data[0]."");
		$sales_booking_rslt = sql_select("SELECT sales_booking_no AS SALES_BOOKING_NO FROM fabric_sales_order_mst WHERE job_no='" . $dataArray[0][csf('fso_no')] . "' AND status_active=1 AND is_deleted=0");
		foreach ($sales_booking_rslt as $row) {
			$sales_booking_no = $row['SALES_BOOKING_NO'];
		}
	}
?>
	<div style="width:1530px;">
		<table width="1500" cellspacing="0" align="right">
			<tr>
				<td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<?
					$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result) {
					?>
						Plot No: <? echo $result['plot_no']; ?>
						Level No: <? echo $result['level_no'] ?>
						Road No: <? echo $result['road_no']; ?>
						Block No: <? echo $result['block_no']; ?>
						City No: <? echo $result['city']; ?>
						Zip Code: <? echo $result['zip_code']; ?>
						Province No: <?php echo $result['province']; ?>
						Country: <? echo $country_arr[$result['country_id']]; ?><br>
						Email Address: <? echo $result['email']; ?>
						Website No: <? echo $result['website'];
								}
									?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
			</tr>
			<tr>
				<td width="100"><strong>Transfer ID</strong></td>
				<td width="250px">: <? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
				<td width="120"><strong>Transfer Criteria</strong></td>
				<td width="250px">: <? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
				<td width="110"><strong>To Company</strong></td>
				<td width="270px">: <? echo $company_library[$dataArray[0][csf('to_company')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Transfer Date</strong></td>
				<td>: <? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
				<td><strong>Challan No.</strong></td>
				<td>: <? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Item Category</strong></td>
				<td width="175px">: <? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
			</tr>
			<tr>
				<?php
				if ($dataArray[0][csf('fso_no')] != '') {
				?>
					<td><strong>Booking No</strong></td>
					<td>: <? echo $sales_booking_no; ?></td>
					<td><strong>Sales Order No</strong></td>
					<td>: <? echo $dataArray[0][csf('fso_no')]; ?></td>
				<?php
				}
				?>
				<td><strong>Remarks</strong></td>
				<td>: <? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="1500" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<th width="40">SL</th>
					<th width="100">From Store</th>
					<th width="100">From Floor</th>
					<th width="100">From Room</th>
					<th width="100">To Store</th>
					<th width="100">To Floor</th>
					<th width="100">To Room</th>
					<th width="250">Item Description</th>
					<th width="50">Yarn Lot</th>
					<th width="100">Transfered Qnty</th>
					<th width="100">No of Bag</th>
					<th width="100">No of Cone</th>
					<th width="110">Yarn Brand</th>
				</thead>
				<tbody>
					<?
					$sql_dtls = "select id, from_store, floor_id, room, to_store, to_floor_id, to_room, from_prod_id, yarn_lot, transfer_qnty, brand_id, no_of_bag, no_of_cone from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
					$sql_result = sql_select($sql_dtls);
					$i = 1;
					foreach ($sql_result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$transfer_qnty = $row[csf('transfer_qnty')];
						$no_of_bag_sum += $row[csf('no_of_bag')];
						$no_of_cone_sum += $row[csf('no_of_cone')];
						$transfer_qnty_sum += $transfer_qnty;

					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td align="center"><? echo $store_library[$row[csf("from_store")]]; ?></td>
							<td align="center"><? echo $floor_room_rack_arr[$row[csf("floor_id")]]; ?></td>
							<td align="center"><? echo $floor_room_rack_arr[$row[csf("room")]]; ?></td>
							<td align="center"><? echo $store_library[$row[csf("to_store")]]; ?></td>
							<td align="center"><? echo $floor_room_rack_arr[$row[csf("to_floor_id")]]; ?></td>
							<td align="center"><? echo $floor_room_rack_arr[$row[csf("to_room")]]; ?></td>
							<td align="center"><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
							<td align="center"><? echo $row[csf("yarn_lot")]; ?></td>
							<td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
							<td align="right"><? echo $row[csf("no_of_bag")]; ?></td>
							<td align="right"><? echo $row[csf("no_of_cone")]; ?></td>
							<td align="center"><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
						</tr>
					<? $i++;
					} ?>
				</tbody>
				<tfoot>
					<tr style="font-weight: bold;">
						<td colspan="9" align="right"><strong>Total :</strong></td>
						<td align="right"><?php echo number_format($transfer_qnty_sum, 2); ?></td>
						<td align="right"><?php echo $no_of_bag_sum; ?></td>
						<td align="right"><?php echo $no_of_cone_sum; ?></td>
						<td align="right"><?php //echo $req_qny_edit_sum; 
											?></td>
					</tr>
				</tfoot>
			</table>
			<br>

			<br>
			<?
			echo signature_table(38, $data[0], "900px");
			?>
			<b style="margin-left: 5px; margin-top: 100px">Prepared By: <?= $user_fullname_arr[$dataArray[0][csf('inserted_by')]] ?></b>
		</div>
	</div>
<?
	exit();
}

if ($action == "yarn_transfer_print2") {
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);

	$sql = "select a.id, a.transfer_system_id, a.challan_no, a.transfer_date, a.transfer_criteria, a.item_category, a.to_company, a.from_order_id, a.to_order_id, a.item_category, a.remarks, b.fso_no, b.from_store, b.to_store from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and a.id='" . $data[1] . "' and a.company_id='" . $data[0] . "'";
	//echo $sql;die;
	$dataArray = sql_select($sql);

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$s_location_library = return_library_array("select id, location_id from  lib_store_location", "id", "location_id");
	$location_library = return_library_array("select id, location_name from  lib_location", "id", "location_name");
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=1", "id", "product_name_details");
	$brand_arr = return_library_array("select id, brand_name from   lib_brand", "id", "brand_name");
	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", 'floor_room_rack_id', 'floor_room_rack_name');
	$imge_arr = return_library_array("select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');


	//for sales booking no
	$sales_booking_no = '';
	if ($dataArray[0][csf('fso_no')] != '') {
		//$sales_booking_rslt = sql_select("SELECT sales_booking_no AS SALES_BOOKING_NO FROM fabric_sales_order_mst WHERE job_no='".$dataArray[0][csf('fso_no')]."' AND status_active=1 AND is_deleted=0 AND company_id=".$data[0]."");
		$sales_booking_rslt = sql_select("SELECT sales_booking_no AS SALES_BOOKING_NO FROM fabric_sales_order_mst WHERE job_no='" . $dataArray[0][csf('fso_no')] . "' AND status_active=1 AND is_deleted=0");
		foreach ($sales_booking_rslt as $row) {
			$sales_booking_no = $row['SALES_BOOKING_NO'];
		}
	}
?>
	<div style="width:1330px;">
		<table width="1300" cellspacing="0" align="right">
			<tr>
				<td width="70" align="right">
					<img src='<? echo base_url($imge_arr[$data[0]]); ?>' height='100%' width='100%' />
				</td>
				<td width="100"></td>
				<td width="90"></td>
				<td>
					<table style="margin-top:10px">
						<tr>
							<td colspan="6" align="center" style="font-size:31px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
						<tr>
							<td colspan="6" align="center" style="font-size:x-large"><strong><u>Yarn Transfer Challan</u></strong></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width="100"><strong>Transfer ID</strong></td>
				<td width="250px">: <? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
				<td width="120"><strong>Transfer Criteria</strong></td>
				<td width="250px">: <? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
				<td width="110"><strong>From Location</strong></td>
				<td width="270px">: <? echo $location_library[$s_location_library[$dataArray[0][csf('from_store')]]]; ?></td>
			</tr>
			<tr>
				<td><strong>Transfer Date</strong></td>
				<td>: <? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
				<td><strong>Challan No.</strong></td>
				<td>: <? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>To Location </strong></td>
				<td width="175px">: <? echo $location_library[$s_location_library[$dataArray[0][csf('to_store')]]]; ?></td>
			</tr>
			<tr>
				<?php
				if ($dataArray[0][csf('fso_no')] != '') {
				?>
					<td><strong>Booking No</strong></td>
					<td>: <? echo $sales_booking_no; ?></td>
					<td><strong>Sales Order No</strong></td>
					<td>: <? echo $dataArray[0][csf('fso_no')]; ?></td>
				<?php
				}
				?>
				<td><strong>Item Category</strong></td>
				<td width="175px">: <? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
				<td><strong>Remarks</strong></td>
				<td colspan="3">: <? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="1300" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<th width="40">SL</th>
					<th width="100">From Store</th>
					<th width="100">Buyer</th>
					<th width="100">File</th>
					<th width="100">To Store</th>
					<th width="250">Item Description</th>
					<th width="50">Yarn Lot</th>
					<th width="100">Transfered Qnty</th>
					<th width="100">No of Bag</th>
					<th width="100">No of Cone</th>
					<th width="110">Yarn Brand</th>
				</thead>
				<tbody>
					<?
					$sql_dtls = "SELECT b.id, b.from_store, b.floor_id, b.room, b.to_store, b.from_prod_id, b.yarn_lot, b.transfer_qnty, b.brand_id, b.no_of_bag, b.no_of_cone,  b.buyer_id, c.btb_lc_id from  inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c where a.id=b.mst_id and a.id=c.mst_id and b.from_prod_id=c.prod_id and a.id='" . $data[1] . "' and a.company_id='" . $data[0] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, b.from_store, b.floor_id, b.room, b.to_store, b.from_prod_id, b.yarn_lot, b.transfer_qnty, b.brand_id, b.no_of_bag, 
					b.no_of_cone, b.buyer_id, c.btb_lc_id ";
					//echo $sql_dtls;
					$sql_result = sql_select($sql_dtls);

					foreach ($sql_result as $row) {
						if ($row[csf('btb_lc_id')] > 0) {
							if ($btbChk[$row[csf('btb_lc_id')]] == "") {
								$btbChk[$row[csf('btb_lc_id')]] = $row[csf('btb_lc_id')];
								$all_btb_id_arr[$row[csf("btb_lc_id")]] = $row[csf("btb_lc_id")];
							}
						}
					}

					$all_btb_id_arr = array_filter($all_btb_id_arr);
					if (!empty($all_btb_id_arr)) {
						$con = connect();
						execute_query("DELETE FROM TMP_BTB_LC_ID WHERE USERID = " . $user_id . "");
						oci_commit($con);

						$con = connect();
						foreach ($all_btb_id_arr as $btbId) {
							execute_query("INSERT INTO TMP_BTB_LC_ID(BTB_LC_ID,USERID) VALUES(" . $btbId . ", " . $user_id . ")");
							oci_commit($con);
						}
					}
					//die;


					$sql_file = "SELECT a.internal_file_no, a.export_lc_no as lc_sc_no, b.import_mst_id as btb_id FROM com_export_lc a, com_btb_export_lc_attachment b, tmp_btb_lc_id c WHERE a.id=b.lc_sc_id and b.import_mst_id=c.btb_lc_id and c.userid=$user_id and b.is_lc_sc=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name='" . $data[0] . "' 
					UNION All
					SELECT a.internal_file_no, a.contract_no as lc_sc_no, b.import_mst_id as btb_id FROM com_sales_contract a, com_btb_export_lc_attachment b, tmp_btb_lc_id c WHERE a.id=b.lc_sc_id and b.import_mst_id=c.btb_lc_id and c.userid=$user_id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name='" . $data[0] . "' ";
					//echo $sql_file;die;
					$sql_file_res = sql_select($sql_file);
					$file_data_arr = array();
					foreach ($sql_file_res as $row) {
						$file_data_arr[$row[csf('btb_id')]]['internal_file_no'] = $row[csf('internal_file_no')];
					}
					unset($sql_file_res);

					$r_id111 = execute_query("DELETE FROM TMP_BTB_LC_ID WHERE USERID=$user_id ");
					if ($r_id111) {
						oci_commit($con);
					}


					$i = 1;
					foreach ($sql_result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$transfer_qnty = $row[csf('transfer_qnty')];
						$no_of_bag_sum += $row[csf('no_of_bag')];
						$no_of_cone_sum += $row[csf('no_of_cone')];
						$transfer_qnty_sum += $transfer_qnty;

					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td align="center"><? echo $store_library[$row[csf("from_store")]]; ?></td>
							<td align="center"><? echo $buyer_library[$row[csf("buyer_id")]]; ?></td>
							<td align="center"><? echo $file_data_arr[$row[csf('btb_lc_id')]]['internal_file_no']; ?></td>
							<td align="center"><? echo $store_library[$row[csf("to_store")]]; ?></td>
							<td align="center"><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
							<td align="center"><? echo $row[csf("yarn_lot")]; ?></td>
							<td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
							<td align="right"><? echo $row[csf("no_of_bag")]; ?></td>
							<td align="right"><? echo $row[csf("no_of_cone")]; ?></td>
							<td align="center"><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
						</tr>
					<? $i++;
					} ?>
				</tbody>
				<tfoot>
					<tr style="font-weight: bold;">
						<td colspan="7" align="right"><strong>Total :</strong></td>
						<td align="right"><?php echo number_format($transfer_qnty_sum, 2); ?></td>
						<td align="right"><?php echo $no_of_bag_sum; ?></td>
						<td align="right"><?php echo $no_of_cone_sum; ?></td>
						<td align="right"><?php //echo $req_qny_edit_sum; 
											?></td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(38, $data[0], "900px");
			?>
		</div>
	</div>
<?
	exit();
}

if ($action == "btb_selection_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');
	$company_arr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$suplier_cond = "";
	//if($supplier>0) $suplier_cond=" and a.supplier_id=$supplier and b.supplier_id=$supplier";
	$sql = "select d.id, d.lc_number, d.importer_id, d.lc_date, d.last_shipment_date, a.supplier_id from product_details_master a, inv_transaction b, com_btb_lc_pi c, com_btb_lc_master_details d where a.id=b.prod_id and b.pi_wo_batch_no=c.pi_id and c.com_btb_lc_master_details_id=d.id and a.item_category_id=1 and b.item_category = 1 and b.receive_basis = 1 and b.transaction_type=1 and a.lot='$lot_no' and a.company_id=$comany_name and b.company_id=$comany_name $suplier_cond group by d.id, d.lc_number, d.importer_id, d.lc_date, d.last_shipment_date, a.supplier_id";
	//echo $sql;
?>
	<script>
		function js_set_value(data) {
			var splitSTR = data.split("**");
			var id = splitSTR[0];
			var lc_number = splitSTR[1];

			$("#hidden_btb_id").val(id);
			$("#hidden_btb_lc_no").val(lc_number);
			parent.emailwindow.hide();
		}
	</script>
	<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
		<table width="700" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			<thead>
				<tr>
					<th width="50">SL</th>
					<th width="150">LC NO.</th>
					<th width="150">Importer</th>
					<th width="150">Supplier Name</th>
					<th width="80">LC Date</th>
					<th>Last Shipment Date</th>
				</tr>
			</thead>
			<tbody>
				<? $nameArray = sql_select($sql);
				$i = 1;
				foreach ($nameArray as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('id')] . '**' . $row[csf('lc_number')]; ?>')" id="tr_<? echo $i; ?>" style=" cursor: pointer;">
						<td><? echo $i; ?></td>
						<td><? echo $row[csf('lc_number')] ?></td>
						<td><? echo $company_arr[$row[csf('importer_id')]]; ?></td>
						<td><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></td>
						<td><? echo ($row[csf('lc_date')] != "") ? date("d-m-Y", strtotime($row[csf('lc_date')])) : ""; ?></td>
						<td><? echo date("d-m-Y", strtotime($row[csf('last_shipment_date')])); ?></td>
					</tr>
				<?
					$i++;
				} ?>
			</tbody>
			<tfoot>
				<input type="hidden" id="hidden_btb_id" value="">
				<input type="hidden" id="hidden_btb_lc_no" value="">
			</tfoot>

		</table>
	</form>
<?
}

if ($action == "actn_fso_popup") {
	echo load_html_head_contents("FSO Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(booking_data) {
			document.getElementById('hidden_booking_data').value = booking_data;
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center">
			<fieldset style="width:680px;margin-left:4px;">
				<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
					<table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
						<thead>
							<th class="must_entry_caption">Company</th>
							<th>Search By</th>
							<th>Search</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
								<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down("cbo_company", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", $cbo_company_id, "");
								?>
							</td>
							<td align="center">
								<?
								$search_by_arr = array(2 => "Sales / Booking No");
								echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center">
								<input type="text" style="width:140px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('cbo_company').value, 'actn_fso_listview', 'search_div', 'yarn_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
						</tr>
					</table>
					<div id="search_div" style="margin-top:10px"></div>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "actn_fso_listview") {
	$data = explode('_', $data);
	$search_string = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];

	if ($company_id == 0) {
		echo "<h3 style=\"color:red;\">Select compnay.</h3>";
		die;
	}

	$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 2) {
			$search_field_cond = " and sales_booking_no like '%" . $search_string . "'";
		}
	}

	if ($db_type == 0)
		$year_field = "YEAR(insert_date) AS YEAR";
	else if ($db_type == 2)
		$year_field = "to_char(insert_date,'YYYY') AS YEAR";
	else $year_field = "";

	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	$sql = "SELECT id AS ID, $year_field, job_no_prefix_num AS JOB_NO_PREFIX_NUM, job_no AS JOB_NO, within_group AS WITHIN_GROUP, sales_order_type AS SALES_ORDER_TYPE, sales_booking_no AS SALES_BOOKING_NO, booking_date AS BOOKING_DATE, buyer_id AS BUYER_ID, style_ref_no AS STYLE_REF_NO, location_id AS LOCATION_ID FROM fabric_sales_order_mst WHERE entry_form=109 AND status_active=1 AND is_deleted=0 AND company_id=" . $company_id . " " . $search_field_cond . " ORDER BY id desc";
	//echo $sql;//die;
	$result = sql_select($sql);
?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="640" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="70">Sales Order No</th>
			<th width="60">Year</th>
			<th width="70">Buyer</th>
			<th width="100">Sales/ Booking No</th>
			<th width="80">Booking date</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:640px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="620" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			if (!empty($result)) {
				foreach ($result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					if ($row[csf('within_group')] == 1)
						$buyer = $company_arr[$row[csf('buyer_id')]];
					else
						$buyer = $buyer_arr[$row[csf('buyer_id')]];

					$booking_data = $row['JOB_NO'];
			?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $booking_data; ?>');">
						<td width="40"><? echo $i; ?></td>
						<td width="70">
							<p>&nbsp;<? echo $row['JOB_NO_PREFIX_NUM']; ?></p>
						</td>
						<td width="60" align="center">
							<p><? echo $row['YEAR']; ?></p>
						</td>
						<td width="70">
							<p><? echo $buyer; ?>&nbsp;</p>
						</td>
						<td width="100">
							<p><? echo $row['SALES_BOOKING_NO']; ?></p>
						</td>
						<td width="80" align="center"><? echo change_date_format($row['BOOKING_DATE']); ?></td>
						<td width="110">
							<p><? echo $row['STYLE_REF_NO']; ?></p>
						</td>
						<td>
							<p><? echo $location_arr[$row['LOCATION_ID']]; ?></p>
						</td>
					</tr>
			<?
					$i++;
				}
			}
			?>
		</table>
	</div>
<?
	exit();
}

//multiple_transfer_popup
if ($action == "multiple_transfer_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function check_all_data() {
			var tbl_row_count = document.getElementById('list_view').rows.length;
			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				var attrData = $('#tr_' + i).attr('onclick');
				var splitArr = attrData.split("'");
				js_set_value(splitArr[1]);
			}
		}

		var selected_id = Array();
		var selected_name = Array();

		function js_set_value(mrr) {
			var splitArr = mrr.split("_");
			//$("#hidden_return_number").val(splitArr[1]); // mrr number
			$("#hnd_transfer_id").val(splitArr[1]); // id
			toggle(document.getElementById('tr_' + splitArr[0]), '#FFFFCC');

			if (jQuery.inArray(splitArr[1], selected_id) == -1) {
				//selected_name.push(splitArr[1]);
				selected_id.push(splitArr[1]);
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == splitArr[1]) break;
				}
				//selected_name.splice( i, 1 );
				selected_id.splice(i, 1);
			}

			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				//name += selected_name[i] + ',';
			}

			id = id.substr(0, id.length - 1);
			//name = name.substr( 0, name.length - 1 );

			$('#hnd_transfer_id').val(id);
			//$('#hidden_return_number').val(name);
		}

		//func_active_inactive
		function func_active_inactive(str) {
			$('#cbo_comp_id').val(0);
			$('#cbo_comp_id_to').val(0);
			if (str == 1) {
				$('#cbo_comp_id_to').removeAttr('disabled', 'disabled');
			} else {
				$('#cbo_comp_id_to').attr('disabled', 'disabled');
			}
		}

		//func_onchange_comp
		function func_onchange_comp(id) {
			if ($('#cbo_trans_criteria').val() == 2) {
				$('#cbo_comp_id_to').val(id);
			}
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="120" class="must_entry_caption">Transfer Criteria</th>
							<th width="120" class="must_entry_caption">From Company</th>
							<th width="120" class="must_entry_caption">To Company</th>
							<th width="150">Transfer No</th>
							<th width="180">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?
								echo create_drop_down("cbo_trans_criteria", 120, $item_transfer_criteria, "", 1, "-- Select --", $transfer_criteria, "func_active_inactive(this.value);", '', '1,2');
								?>
							</td>
							<td>
								<?
								echo create_drop_down("cbo_comp_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", $company, "func_onchange_comp(this.value)");
								?>
							</td>
							<td>
								<?
								echo create_drop_down("cbo_comp_id_to", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", $company_to, "", 1);
								?>
							</td>
							<td>
								<input type="text" style="width:150px" class="text_boxes" name="txt_transfer_no" id="txt_transfer_no" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />&nbsp;&nbsp;&nbsp;
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_trans_criteria').value+'_'+document.getElementById('cbo_comp_id').value+'_'+document.getElementById('cbo_comp_id_to').value+'_'+document.getElementById('txt_transfer_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'multiple_transfer_listview', 'search_div', 'yarn_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="6">
								<? echo load_month_buttons(1);  ?>
							</td>
						</tr>
					</tbody>
					</tr>
				</table>
				<div align="center" style="margin-top:10px" valign="top" id="search_div"> </div>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

//for multiple_transfer_listview
if ($action == "multiple_transfer_listview") {
	echo '<input type="hidden" id="hnd_transfer_id" value="" />';
	$ex_data = explode("_", $data);
	$trans_criteria = $ex_data[0];
	$comp_id = $ex_data[1];
	$comp_id_to = $ex_data[2];
	$transfer_no = $ex_data[3];
	$txt_date_from = $ex_data[4];
	$txt_date_to = $ex_data[5];
	$cbo_year = $ex_data[6];

	//for trans_criteria
	$criteria_cond = '';
	if ($trans_criteria == 0) {
		echo "<p style='font-size:25px; color:#F00'>Please Select Transfer Criteria</p>";
		die;
	} else {
		$criteria_cond = " and transfer_criteria = " . $trans_criteria;
	}

	//for company
	$company_cond = '';
	if ($comp_id == 0) {
		echo "<p style='font-size:25px; color:#F00'>Please Select From Company.</p>";
		die;
	} else {
		$company_cond = " and company_id = " . $comp_id;
	}

	//for company to
	$company_to_cond = '';
	if ($comp_id_to != 0) {
		$company_to_cond = " and to_company = " . $comp_id_to;
	}

	//for transfer no
	$transfer_no_cond = '';
	if ($transfer_no != 0) {
		$transfer_no_cond = " and transfer_system_id like '%" . $transfer_no . "'";
	}

	//for date
	$date_cond = '';
	if ($txt_date_from != '' && $txt_date_to != '') {
		$date_cond = " and transfer_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
	} else {
		if (trim($cbo_year) != 0) {
			$date_cond = " and to_char(insert_date,'YYYY') = " . $cbo_year;
		}
	}

	$year_field = "to_char(insert_date,'YYYY') as year,";
	$sql = "select id, $year_field transfer_prefix_number, transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_mst where item_category=1 and status_active=1 and is_deleted=0" . $criteria_cond . $company_cond . $company_to_cond . $transfer_no_cond . $date_cond . " order by id";
	//echo $sql;
	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$arr = array(3 => $company_arr, 5 => $item_transfer_criteria, 6 => $item_category);
	echo create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "70,60,100,120,90,140", "750", "250", 0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '', '', '0,0,0,0,3,0,0', '', 1);
	exit();
}

//for multiple_transfer_print
if ($action == "multiple_transfer_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$transfer_id = $data[0];
	$sql = "select a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.inserted_by,
	b.from_store, b.floor_id, b.room, b.to_store, b.to_floor_id, b.to_room, b.from_prod_id, sum(b.transfer_qnty) as transfer_qnty, sum(b.no_of_bag) as no_of_bag, sum(b.no_of_cone) as no_of_cone,
	c.detarmination_id, c.lot, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_type2nd, c.yarn_comp_percent1st, c.yarn_type, c.color, c.supplier_id, c.brand
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, product_details_master c 
	where a.id=b.mst_id and b.from_prod_id = c.id and a.id in(" . $transfer_id . ")
	and a.status_active=1 and a.is_deleted=0
	and b.status_active=1 and b.is_deleted=0
	and c.status_active=1 and c.is_deleted=0
	group by a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.inserted_by,
	b.from_store, b.floor_id, b.room, b.to_store, b.to_floor_id, b.to_room, b.from_prod_id,
	c.detarmination_id, c.lot, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_type2nd, c.yarn_comp_percent1st, c.yarn_type, c.color, c.supplier_id, c.brand
	";
	//echo $sql;
	$sql_rslt = sql_select($sql);
	$pdata = array();
	$trans_id = '';
	$requisition_no = '';
	$supplier_id_arr = '';
	$yarn_count_id = '';
	$color = '';
	$company = 0;
	$knit_dye_company = 0;
	$knit_dye_source = 0;

	$store_id_arr = array();
	$supplier_id_arr = array();
	foreach ($sql_rslt as $row) {
		$store_id_arr[$row[csf('from_store')]] = $row[csf('from_store')];
		$store_id_arr[$row[csf('to_store')]] = $row[csf('to_store')];
		$supplier_id_arr[$row[csf('supplier_id')]] = $row[csf('supplier_id')];
	}

	/*echo "<pre>";
	print_r($store_id_arr);
	echo "</pre>";*/

	//for company
	$company_library = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$user_fullname_arr = return_library_array("select id, USER_FULL_NAME from USER_PASSWD", "id", "USER_FULL_NAME");

	//for store
	if (!empty($store_id_arr)) {
		$store_library = return_library_array("select id, store_name from lib_store_location where status_active=1 and is_deleted=0 and id in(" . implode(',', $store_id_arr) . ")", "id", "store_name");
	}

	//for supplier
	if (!empty($supplier_id_arr)) {
		$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and id in (" . implode(',', $supplier_id_arr) . ")", "id", "supplier_name");
	}

	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", 'floor_room_rack_id', 'floor_room_rack_name');
	$brand_dtls = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0", 'id', 'brand_name');
?>
	<div style="width:1650px;">
		<table cellspacing="0" border="0" width="100%">
			<tr class="form_caption">
				<td colspan="18" style="font-size:x-large" align="center"><strong><u>Yarn Transfer Challan</u></strong></td>
			</tr>
			<tr>
				<td colspan="18">&nbsp;</td>
			</tr>
		</table>
		<div style="width:100%;">
			<table align="right" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<th width="30" align="center">SL</th>
					<th width="120">Tranfer ID</th>
					<th width="120">Transfer Criteria</th>
					<th width="120">From Company</th>
					<th width="120">To Company</th>
					<th width="120">From Store</th>
					<th width="120">From Floor</th>
					<th width="120">From Room</th>
					<th width="120">To Store</th>
					<th width="120">To Floor</th>
					<th width="120">To Room</th>
					<th width="120">Yarn Supplier</th>
					<th width="100">Yarn Brand</th>
					<th width="120">Yarn Description</th>
					<th width="100">Yarn Lot</th>
					<th width="100">Transfered Qty</th>
					<th width="60">No of Bag</th>
					<th>No of Cone</th>
				</thead>
				<?
				$i = 1;
				foreach ($sql_rslt as $row) {
					$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
					$buyer_name = $buyer_arr[$job_alldata_arr[$row[csf("trans_id")]]['buyer_name']];

					if ($row[csf('yarn_count_id')] != "") {
						$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . " %" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
					} else {
						$compos = "";
					}

					if ($row[csf('issue_basis')] == 3) {
						$programNo = $requisition_data[$row[csf('requisition_no')]]['program_no'];
					}
				?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="120"><? echo $row[csf('transfer_system_id')]; ?></td>
						<td width="120"><? echo $item_transfer_criteria[$row[csf("transfer_criteria")]]; ?></td>
						<td width="120"><? echo $company_library[$row[csf("company_id")]]; ?></td>
						<td width="120"><? echo $company_library[$row[csf("to_company")]]; ?></td>
						<td width="120"><? echo $store_library[$row[csf("from_store")]]; ?></td>
						<td width="120"><? echo $floor_room_rack_arr[$row[csf("floor_id")]]; ?></td>
						<td width="120"><? echo $floor_room_rack_arr[$row[csf("room")]]; ?></td>
						<td width="120"><? echo $store_library[$row[csf("to_store")]]; ?></td>
						<td width="120"><? echo $floor_room_rack_arr[$row[csf("to_floor_id")]]; ?></td>
						<td width="120"><? echo $floor_room_rack_arr[$row[csf("to_room")]]; ?></td>
						<td width="120"><? echo $supplier_library[$row[csf("supplier_id")]]; ?></td>
						<td width="120"><? echo $brand_dtls[$row[csf("brand")]]; ?></td>
						<td width="120"><? echo $compos; ?></td>
						<td width="120"><? echo $row[csf("lot")]; ?></td>
						<td width="120" align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
						<td width="120" align="right"><? echo $row[csf("no_of_bag")]; ?></td>
						<td width="120" align="right"><? echo $row[csf("no_of_cone")]; ?></td>
					</tr>
				<?php
					$total_transfer_qnty += $row[csf('transfer_qnty')];
					$total_no_of_bags += $row[csf('no_of_bag')];
					$total_no_of_cone += $row[csf('no_of_cone')];
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="15">Total</td>
					<td align="right"><? echo number_format($total_transfer_qnty, 0, '', ','); ?></td>
					<td align="right"><? echo number_format($total_no_of_bags, 0, '', ','); ?></td>
					<td align="right"><? echo number_format($total_no_of_cone, 0, '', ','); ?></td>
					<td align="right">&nbsp;</td>
				</tr>
				<tfoot>
					<tr>
						<th colspan="21" align="left">In Word : <? echo number_to_words(number_format($total_transfer_qnty, 2)); ?></th>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(38, $data[1], "1200px", "", "", $user_fullname_arr[$sql_rslt[0][csf('inserted_by')]]);
			?>
		</div>
	</div>
<?
	exit();
}
?>