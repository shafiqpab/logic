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

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];

if ($company_id != '') {
	$company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id != '') {
	$store_location_credential_cond = "and a.id in($store_location_id)";
}
if ($item_cate_id != '') {
	$item_cate_credential_cond = $item_cate_id;
} else {
	$item_cate_credential_cond = "" . implode(",", array_flip($general_item_category)) . "";
}
if ($supplier_id != '') {
	$supplier_credential_cond = "and a.id in($supplier_id)";
}

// ==============Start Floor Room Rack Shelf Bin upto variable Settings============
if ($action == "upto_variable_settings") {
	extract($_REQUEST);
	/*echo "select store_method from variable_settings_inventory where company_name='$cbo_company_id' and item_category_id=13 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1";die;*/
	echo $variable_inventory = return_field_value("store_method", "variable_settings_inventory", "company_name='$cbo_company_id' and item_category_id=8 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	exit();
}
// ==============End Floor Room Rack Shelf Bin upto variable Settings==============

//========== user credential end ==========

if ($db_type == 2 || $db_type == 1) {
	$mrr_date_check = "and to_char(insert_date,'YYYY')=" . date('Y', time()) . "";
	$group_concat = "wm_concat";
} else if ($db_type == 0) {
	$mrr_date_check = "and year(insert_date)=" . date('Y', time()) . "";
	$group_concat = "group_concat";
}


//$result = array_intersect($array1, $array2);
//--------------------------------------------------------------------------------------------
//$trim_group_arr = return_library_array("select id, order_uom from lib_item_group","id","order_uom");

if ($action == "report_formate_setting") {
	$print_report_format = return_field_value("format_id", "lib_report_template", "template_name ='" . $data . "' and module_id=6 and report_id=194 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n";
	exit();
}

//load drop down supplier
if ($action == "load_drop_down_supplier") {
	//echo "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(1,5,6,7,8,30,36,37,39,92) $supplier_credential_cond and c.tag_company in($data) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name";
	echo create_drop_down("cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(1,5,6,7,8,30,36,37,39,92) $supplier_credential_cond and c.tag_company in($data) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}
if($action=="load_drop_down_supplier_new")
{
	extract($data);
	$exdata = explode("*",$data);
	
	$comId = $exdata[0];
	$suppId = $exdata[1];
	$user_supplier_cond = $suppId ? "and c.id in ($suppId)" :"";

	echo create_drop_down("cbo_supplier", 170, "SELECT DISTINCT c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$comId' and b.party_type IN(1,5,6,7,8,30,36,37,39,92) and c.status_active IN(1) and c.is_deleted=0 group by c.id, c.supplier_name   UNION ALL  SELECT DISTINCT c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c, inv_receive_master d where c.id=b.supplier_id and a.supplier_id = b.supplier_id and c.id=d.supplier_id and a.tag_company='$comId' $user_supplier_cond and b.party_type IN(1,5,6,7,8,30,36,37,39,92) and c.status_active IN(1,3) and c.is_deleted=0 group by c.id, c.supplier_name    order by supplier_name    ", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}
if ($action == "load_drop_down_loan_party") {
	echo create_drop_down("cbo_loan_party", 170, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b 
	where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name", "id,supplier_name", 1, "- Select Loan Party -", $selected, "", "", "");
	exit();
}

/*if ($action=="load_room_rack_self_bin")
{
	$explodeData = explode('*', $data);
	$explodeData[11] = 'storeUpdateUptoDisable()';
	$data=implode('*', $explodeData);
	load_room_rack_self_bin("requires/general_item_receive_controller",$data);
}*/

if ($action == "load_drop_down_store") {
	echo create_drop_down("cbo_store_name", 170, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type in(4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99) and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "--Select store--", 0, "load_drop_down('requires/general_item_receive_controller', this.value+'_'+$data, 'load_drop_floor','floor_td');storeUpdateUptoDisable();store_wise_stock(this.value);");
	exit();
}

if ($action == "store_wise_stock") {
	$data_ref = explode("__", $data);
	$product_id = $data_ref[0];
	$store_id   = $data_ref[1];
	$store_wise_qty = return_field_value("sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as store_stock", "inv_transaction", "prod_id=$product_id and store_id=$store_id and status_active=1 and is_deleted=0", "store_stock");
	echo $store_wise_qty;
	exit();
}

if ($action == "load_drop_floor") {
	$data = explode("_", $data);
	$store_id = $data[0];
	$company_id = $data[1];
	echo create_drop_down("cbo_floor", "152", "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name", "floor_id,floor_room_rack_name", 1, "--Select Floor--", 0, "load_drop_down('requires/general_item_receive_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_room','room_td');storeUpdateUptoDisable();", 0);
}

if ($action == "load_drop_room") {
	$data = explode("_", $data);
	$floor_id = $data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	$room_id = $data[3];

	echo create_drop_down("cbo_room", 152, "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.floor_id='$floor_id' and b.store_id=$store_id and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name", "room_id,floor_room_rack_name", 1, "--Select Room--", $room_id, "load_drop_down('requires/general_item_receive_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_rack','rack_td');storeUpdateUptoDisable();", 0);
}

if ($action == "load_drop_rack") {
	$data = explode("_", $data);
	$room_id = $data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	$rack_id = $data[3];
	echo create_drop_down("txt_rack", 152, "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.room_id='$room_id' and b.store_id=$store_id and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name", "rack_id,floor_room_rack_name", 1, "--Select Rack--", $rack_id, "load_drop_down('requires/general_item_receive_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_shelf','shelf_td');storeUpdateUptoDisable();", 0);
}

if ($action == "load_drop_shelf") {
	$data = explode("_", $data);
	$rack = $data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	$shelf_id = $data[3];
	echo create_drop_down("txt_shelf", 152, "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.rack_id='$rack' and b.store_id=$store_id and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name", "shelf_id,floor_room_rack_name", 1, "--Select Shelf--", $shelf_id, "load_drop_down('requires/general_item_receive_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_bin','bin_td');storeUpdateUptoDisable();", 0);
}

if ($action == "load_drop_bin") {
	$data = explode("_", $data);
	$shelf = $data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	$bin_id = $data[3];
	echo create_drop_down("cbo_bin", 152, "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.bin_id and b.shelf_id=$shelf and b.store_id='$store_id' and a.company_id='$company_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name order by a.floor_room_rack_name", "bin_id,floor_room_rack_name", 1, "--Select Bin--", $bin_id, "", 0);
}

//load drop down store
/*if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 170, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type in ($item_cate_credential_cond) $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 and a.company_id in($data)  group by a.id ,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", "", "","" );  	 
	exit();
}*/
if ($action == "check_conversion_rate") //Conversion Exchange Rate
{
	$data = explode("**", $data);
	if ($db_type == 0) {
		$conversion_date = change_date_format($data[1], "Y-m-d", "-", 1);
	} else {
		$conversion_date = change_date_format($data[1], "d-M-y", "-", 1);
	}
	$currency_rate = set_conversion_rate($data[0], $conversion_date, $data[2]);
	echo "1" . "_" . $currency_rate;
	exit();
}

if ($action == "varible_inventory") {
	$sql_variable_inventory = sql_select("select id, independent_controll, rate_optional, is_editable, rate_edit  from variable_settings_inventory where company_name=$data and variable_list=20 and status_active=1 and menu_page_id=20");
	if (count($sql_variable_inventory) > 0) {
		echo "1**" . $sql_variable_inventory[0][csf("independent_controll")] . "**" . $sql_variable_inventory[0][csf("rate_optional")] . "**" . $sql_variable_inventory[0][csf("is_editable")] . "**" . $sql_variable_inventory[0][csf("rate_edit")];
	} else {
		echo "0**" . $sql_variable_inventory[0][csf("independent_controll")] . "**" . $sql_variable_inventory[0][csf("rate_optional")] . "**" . $sql_variable_inventory[0][csf("is_editable")] . "**" . $sql_variable_inventory[0][csf("rate_edit")];
	}
	$variable_inventory = return_field_value("store_method", "variable_settings_inventory", "company_name=$data and item_category_id=8 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	echo "**" . $variable_inventory;
	$variable_lot = return_field_value("auto_transfer_rcv", "variable_settings_inventory", "company_name=$data and variable_list=32 and status_active=1 and is_deleted=0");
	echo "**" . $variable_lot;
	die;
}
//load drop down item group
if ($action == "load_drop_down_itemgroup") {
	$data = explode('_', $data);
	if ($data[1] > 0) $com_cond = " and b.company_id=$data[1]";
	echo create_drop_down("cbo_item_group", 130, "select id,item_name from lib_item_group where item_category=$data[0] and status_active=1 and is_deleted=0 order by item_name", "id,item_name", 1, "-- Select --", 0, "", "");
	exit();
}

//load drop down uom
if ($action == "load_drop_down_uom") {
	if ($data == 0) $uom = 0;
	else $uom = $trim_group_arr[$data];
	//echo $data;die;
	echo create_drop_down("cbo_uom", 130, $unit_of_measurement, "", 1, "-- Select --", $uom, "", 1);
	exit();
}

// wo/pi popup here----------------------// 
if ($action == "wopi_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
		function js_set_value(str) {
			var splitData = str.split("_");

			if (splitData[2] == "No") {
				alert("Goods receive not allowed against Un-Approved P.O. Please ensure the P.O is approved before receiving the goods");
				return;
			}

			$("#hidden_tbl_id").val(splitData[0]); // wo/pi id
			$("#hidden_wopi_number").val(splitData[1]); // wo/pi number
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
					<thead>
						<th width="150"> </th>
						<th>
							<?
							echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --");
							?>
						</th>
						<th width="150" colspan="3"> </th>
					</thead>
					<thead>
						<th width="150">Search By</th>
						<th width="150" align="center" id="search_by_th_up">Enter WO/PI/Req Number</th>
						<th width="150">Item Category</th>
						<th width="200">Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /></th>
					</thead>
					<tbody>
						<tr>
							<td>
								<?
								echo create_drop_down("cbo_search_by", 150, $receive_basis_arr, "", 1, "--Select--", $receive_basis, "", 1);
								?>
							</td>
							<td width="180" align="center" id="search_by_td">
								<input type="text" style="width:150px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<? ($receive_basis == 1) ? $category_disable = "" : $category_disable = 1; ?>
							<td>
								<?
								//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index )
								echo create_drop_down("cbo_item_category", 170, $general_item_category, "", 1, "-- Select --", "", "", "$category_disable", "$item_cate_credential_cond", "", "", "");
								// 4,8,9,10,11,15,16,17,18,19,20,21,22,32
								?>
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_item_category').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+'<? echo $cbo_store_name ?>'+'_'+document.getElementById('cbo_year_selection').value, 'create_wopi_search_list_view', 'search_div', 'general_item_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1);  ?>
								<!-- Hidden field here-->
								<input type="hidden" id="hidden_tbl_id" value="" />
								<input type="hidden" id="hidden_wopi_number" value="hidden_wopi_number" />
								<!-- END -->
							</td>
						</tr>
					</tbody>
				</table>
				<br>
				<div align="center" valign="top" id="search_div"> </div>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "create_wopi_search_list_view") {
	$ex_data = explode("_", $data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$item_cat_ref = $ex_data[5];
	$cbo_string_search_type = $ex_data[6];
	$cbo_year = $ex_data[8];

	if ($txt_search_common == "" && ($txt_date_from == "" && $txt_date_to == "")) {
		echo "<p style='color:red; font-size:16px;'>Please select date range or WO/PI/Req Number</p>";
		die;
	}

	$sql_variable_setup = sql_select("select CATEGORY, OVER_RCV_PERCENT, OVER_RCV_PAYMENT from variable_inv_ile_standard where company_name = $company and variable_list = 23 and status_active=1 and is_deleted=0");
	$variable_setup_data = array();
	foreach ($sql_variable_setup as $val) {
		$variable_setup_data[$val["CATEGORY"]]["OVER_RCV_PERCENT"] = $val["OVER_RCV_PERCENT"];
		$variable_setup_data[$val["CATEGORY"]]["OVER_RCV_PAYMENT"] = $val["OVER_RCV_PAYMENT"];
	}

	$appr_status = array();
	if ($db_type == 0) {
		/*$year_field = "YEAR(a.insert_date) as year";*/
		$year_cond = " and YEAR(a.insert_date) = $cbo_year ";
	} else if ($db_type == 2) {
		/*$year_field = "to_char(a.insert_date,'YYYY') as year";*/
		$year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
	}

	if ($txt_date_from != "" && $txt_date_to != "") {
		if ($db_type == 0) {
			$txt_date_from = change_date_format($txt_date_from, 'yyyy-mm-dd');
			$txt_date_to = change_date_format($txt_date_to, 'yyyy-mm-dd');
		} else if ($db_type == 2) {
			$txt_date_from = change_date_format($txt_date_from, '', '', 1);
			$txt_date_to = change_date_format($txt_date_to, '', '', 1);
		}
	}

	$sql_cond = "";
	if (trim($txt_search_by) == 1) // for pi
	{
		if ($txt_date_from != "" && $txt_date_to != "") {
			$sql_cond .= " and a.pi_date  between '" . $txt_date_from . "' and '" . $txt_date_to . "'";
		} else {
			if ($db_type == 0) {
				$sql_cond .= " and YEAR(a.pi_date) = $cbo_year ";
			} else {
				$sql_cond .= " and to_char(a.pi_date,'YYYY') = $cbo_year ";
			}
		}
		if ($item_cat_ref > 0) {
			$sql_cond .= " and a.item_category_id=$item_cat_ref";
		} else {
			$sql_cond .= " and a.item_category_id in ($item_cate_credential_cond)";
		}
	} else if (trim($txt_search_by) == 2) // for wo
	{
		if ($txt_date_from != "" && $txt_date_to != "") {
			$sql_cond .= " and a.wo_date  between '" . $txt_date_from . "' and '" . $txt_date_to . "'";
		} else {
			if ($db_type == 0) {
				$sql_cond .= " and YEAR(a.wo_date) = $cbo_year ";
			} else {
				$sql_cond .= " and to_char(a.wo_date,'YYYY') = $cbo_year ";
			}
		}
	} else if (trim($txt_search_by) == 7) // for requisition
	{
		if ($txt_date_from != "" && $txt_date_to != "") {
			$sql_cond .= " and a.requisition_date  between '" . $txt_date_from . "' and '" . $txt_date_to . "'";
		} else {
			if ($db_type == 0) {
				$sql_cond .= " and YEAR(a.requisition_date) = $cbo_year ";
			} else {
				$sql_cond .= " and to_char(a.requisition_date,'YYYY') = $cbo_year ";
			}
		}
		if ($item_cat_ref > 0) $sql_cond .= " and b.item_category=$item_cat_ref";
	}
	if (trim($txt_search_common) != "") {
		if (trim($txt_search_by) == 1) // for pi
		{
			if ($cbo_string_search_type == 1) {
				$sql_cond .= " and a.pi_number='$txt_search_common'";
			} else if ($cbo_string_search_type == 2) {
				$sql_cond .= " and a.pi_number LIKE '$txt_search_common%'";
			} else if ($cbo_string_search_type == 3) {
				$sql_cond .= " and a.pi_number LIKE '%$txt_search_common'";
			} else {
				$sql_cond .= " and a.pi_number LIKE '%$txt_search_common%'";
			}


			if (trim($company) != "") $sql_cond .= " and a.importer_id='$company'";
		} else if (trim($txt_search_by) == 2) // for wo
		{
			if ($cbo_string_search_type == 1) {
				$sql_cond .= " and wo_number_prefix_num='$txt_search_common'";
			} else if ($cbo_string_search_type == 2) {
				$sql_cond .= " and wo_number_prefix_num LIKE '$txt_search_common%'";
			} else if ($cbo_string_search_type == 3) {
				$sql_cond .= " and wo_number_prefix_num LIKE '%$txt_search_common'";
			} else if ($cbo_string_search_type == 4 || $cbo_string_search_type == 0) {
				$sql_cond .= " and wo_number_prefix_num LIKE '%$txt_search_common%'";
			}

			if (trim($company) != "") $sql_cond .= " and company_name='$company'";
		} else if (trim($txt_search_by) == 7) // for requisition
		{
			if ($cbo_string_search_type == 1) {
				$sql_cond .= " and a.requ_prefix_num='$txt_search_common'";
			} else if ($cbo_string_search_type == 2) {
				$sql_cond .= " and a.requ_prefix_num LIKE '$txt_search_common%'";
			} else if ($cbo_string_search_type == 3) {
				$sql_cond .= " and a.requ_prefix_num LIKE '%$txt_search_common'";
			} else if ($cbo_string_search_type == 4 || $cbo_string_search_type == 0) {
				$sql_cond .= " and a.requ_prefix_num LIKE '%$txt_search_common%'";
			}

			if (trim($company) != "") $sql_cond .= " and a.company_id='$company'";
		}
	}

	//echo $sql_cond;die; 

	if ($txt_search_by == 1) //pi base
	{
		$approval_status_cond = "";
		if ($db_type == 0) {
			$approval_status = "select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '" . change_date_format(date('d-m-Y'), 'yyyy-mm-dd') . "' and company_id='$company')) and page_id=18 and status_active=1 and is_deleted=0";
		} else {
			$approval_status = "select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '" . change_date_format(date('d-m-Y'), "", "", 1) . "' and company_id='$company')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		$approval_status = sql_select($approval_status);
		if ($approval_status[0][csf('approval_need')] == 1) {
			$approval_status_cond = "and a.approved = 1";
		}

		if ($db_type == 0) {
			$sql = "select a.id as id, a.pi_number as wopi_number, b.lc_number as lc_number, a.pi_date as wopi_date, a.supplier_id as supplier_id, a.currency_id as currency_id, a.source as source, a.item_category_id as item_category,
			from com_pi_master_details a left join com_btb_lc_master_details b on FIND_IN_SET(a.id,b.pi_id)
			where a.item_category_id not in (1,2,3,5,6,7,12,13,14) and a.status_active=1 and a.is_deleted=0 and a.goods_rcv_status<>1 and a.importer_id=$company 
			$sql_cond $approval_status_cond
			order by a.pi_date desc"; //a.supplier_id in (select id from lib_supplier where FIND_IN_SET($company,tag_company) )
		}

		if ($db_type == 1 || $db_type == 2) {
			$sql = "SELECT a.id as id,a.pi_number as wopi_number,d.lc_number as lc_number,a.pi_date as wopi_date,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source,a.item_category_id as item_category,sum(b.quantity) as quantity  
			from com_pi_master_details a,com_pi_item_details b 
			left join com_btb_lc_pi c on b.pi_id=c.pi_id 
			left join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id
			where a.id=b.pi_id and a.item_category_id not in (1,2,3,5,6,7,12,13,14) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.goods_rcv_status<>1 and a.importer_id=$company 
			$sql_cond $approval_status_cond
			group by a.id,a.pi_number,d.lc_number,a.pi_date ,a.supplier_id ,a.currency_id ,a.source ,a.item_category_id 
			order by a.pi_date desc";
		}
	} else if ($txt_search_by == 2) // wo base
	{
		$sql = "SELECT a.id, a.wo_number_prefix_num as wopi_number, ' ' as lc_number, a.wo_date as wopi_date, a.supplier_id as supplier_id, a.currency_id as currency_id, a.source as source, a.location_id, a.is_approved, a.entry_form, b.item_category_id as item_category , sum(b.supplier_order_quantity) as quantity
		from wo_non_order_info_mst a, wo_non_order_info_dtls b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(146,147) and a.pay_mode in (1,4) and a.company_name=$company $sql_cond
		group by a.id, a.wo_number_prefix_num, a.wo_date, a.supplier_id, a.currency_id, a.source, a.location_id, a.is_approved, a.entry_form, b.item_category_id
		order by a.id DESC"; //supplier_id in (select id from lib_supplier where FIND_IN_SET($company,tag_company) )

		//checking approval status start for stationary
		$current_date = date('m/d/Y');
		if ($db_type == 0) {
			$approval_status = "select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '" . change_date_format($current_date, 'yyyy-mm-dd') . "' and company_id='$company')) and page_id=16 and status_active=1 and is_deleted=0";
		} else {
			$approval_status = "select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '" . change_date_format($current_date, "", "", 1) . "' and company_id='$company')) and page_id=16 and status_active=1 and is_deleted=0";
		}
		$approval_status = sql_select($approval_status);
		$approve_status[146]['status'] = $approval_status[0][csf('approval_need')];
		$approve_status[146]['allow_partial'] = $approval_status[0][csf('allow_partial')];

		//checking approval status start for other Purchase
		if ($db_type == 0) {
			$approval_status = "select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '" . change_date_format($current_date, 'yyyy-mm-dd') . "' and company_id='$company')) and page_id=22 and status_active=1 and is_deleted=0";
		} else {
			$approval_status = "select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '" . change_date_format($current_date, "", "", 1) . "' and company_id='$company')) and page_id=22 and status_active=1 and is_deleted=0";
		}
		$approval_status = sql_select($approval_status);
		$approve_status[147]['status'] = $approval_status[0][csf('approval_need')];

		$sql_data = sql_select($sql);
		foreach ($sql_data as $val) {
			if ($approve_status[$val[csf('entry_form')]]['status'] == 1) {
				if ($approve_status[$val[csf('entry_form')]]['allow_partial'] == 1) {
					if ($val[csf('is_approved')] == 1 || $val[csf('is_approved')] == 3) {
						$appr_status[$val[csf('id')]] = "Yes";
					} else {
						$appr_status[$val[csf('id')]] = "No";
					}
				} else {
					if ($val[csf('is_approved')] == 1) {
						$appr_status[$val[csf('id')]] = "Yes";
					} else {
						$appr_status[$val[csf('id')]] = "No";
					}
				}
			} else {
				$appr_status[$val[csf('id')]] = "N/A";
			}
		}
	} else if ($txt_search_by == 7) // requisition base
	{
		/*$approval_need=return_field_value("approval_need","approval_setup_mst a, approval_setup_dtls b","a.id = b.mst_id
		and b.page_id = 13 and a.company_id = $company
		and a.setup_date = ( select max(c.setup_date) from approval_setup_mst c where c.company_id = $company )");
		if($approval_need==1)
		{
			$approval_cond = " and a.is_approved = '1'";
		}else{
			$approval_cond="";
		}*/
		$current_date = date('m/d/Y');
		$approval_status = "select approval_need,allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '" . change_date_format($current_date, "", "", 1) . "' and company_id='$company')) and page_id=13 and status_active=1 and is_deleted=0";
		$approval_status = sql_select($approval_status);
		if (count($approval_status) > 0) {
			if ($approval_status[0][csf('approval_need')] == 1) {
				if ($approval_status[0][csf('allow_partial')] == 1) {
					$approval_cond = " and a.is_approved in (1,3)";
				} else {
					$approval_cond = " and a.is_approved in (1)";
				}
			}
		} else {
			$approval_cond = "";
		}

		$sql = "SELECT a.id, a.requ_prefix_num as wopi_number,' ' as lc_number,a.requisition_date as wopi_date,'' as supplier_id, a.cbo_currency as currency_id,a.source as source , a.item_category_id , b.item_category,a.location_id ,sum(b.quantity) as quantity
		from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b ,lib_store_location c
		where a.id = b.mst_id  and c.id = $ex_data[7] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category in (" . implode(",", array_flip($general_item_category)) . ") and a.pay_mode=4 and a.company_id=$company $approval_cond
		$sql_cond $year_cond
		group by a.id,a.requ_prefix_num,a.requisition_date,a.cbo_currency,a.source,a.item_category_id,b.item_category,a.location_id
		order by a.id desc";
	}
	//echo $sql;
	$result = sql_select($sql);

	$booking_id_all = array();
	foreach ($result as $row) {
		$booking_id_all[$row[csf('id')]] = $row[csf('id')];
	}
	//$booking_id_in=where_con_using_array($booking_id_all,0,'a.booking_id');
	$con = connect();
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=53 and ref_from in(1)");
	oci_commit($con);
	fnc_tempengine("gbl_temp_engine", $user_id, 53, 1, $booking_id_all, $empty_arr);

	$receive_sql = sql_select("SELECT a.id, a.booking_id, b.prod_id, sum(b.order_qnty) as receive_qnty,b.item_category 
	from gbl_temp_engine g, inv_receive_master a, inv_transaction b 
	where a.booking_id=g.ref_val and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=20 and a.receive_basis=$txt_search_by and b.transaction_type=1 and g.user_id=$user_id and g.entry_form=53 and g.ref_from=1
	group by a.id, a.booking_id, b.prod_id,b.item_category ");
	foreach ($receive_sql as $row) {
		$receive_arr[$row[csf("booking_id")]][$row[csf("item_category")]] += $row[csf("receive_qnty")];
	}

	$receive_return_sql = sql_select("SELECT a.booking_id, b.prod_id, c.conversion_factor, sum(b.cons_quantity) as issue_qnty,b.item_category 
	from gbl_temp_engine g, inv_issue_master a, inv_transaction b, product_details_master c 
	where a.booking_id=g.ref_val and a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and b.transaction_type=3 and a.received_id>0 and a.entry_form=26 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and g.user_id=$user_id and g.entry_form=53 and g.ref_from=1
	group by a.booking_id, b.prod_id,b.item_category , c.conversion_factor");
	foreach ($receive_return_sql as $row) {
		$receive_rtn_arr[$row[csf("booking_id")]][$row[csf("item_category")]] += $row[csf("issue_qnty")] / $row[csf("conversion_factor")];
	}

	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=53 and ref_from in(1)");
	oci_commit($con);
	disconnect($con);

	$location_lib_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$arr = array(1 => $location_lib_arr, 4 => $supplier_arr, 5 => $currency, 6 => $source, 7 => $item_category, 8 => $appr_status);
?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
		<thead>
			<th width="40">SL No</th>
			<th width="70">WO/PI No</th>
			<th width="120">Location</th>
			<th width="80">LC</th>
			<th width="90">Date</th>
			<th width="150">Supplier</th>
			<th width="60">Currency</th>
			<th width="100">Source</th>
			<th width="100"> Item Category</th>
			<th> Approval Status</th>
		</thead>
	</table>
	<div style="width:900px; max-height:250px; overflow-y:scroll">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" id="list_view">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
				//$variable_setup_data[$val["CATEGORY"]]["OVER_RCV_PERCENT"]=$val["OVER_RCV_PERCENT"];
				//$variable_setup_data[$val["CATEGORY"]]["OVER_RCV_PAYMENT"]=$val["OVER_RCV_PAYMENT"];
				$woPiBlance = 1;
				if ($variable_setup_data[$row[csf('item_category')]]["OVER_RCV_PAYMENT"] > 0) {
					$allow_qnty = ($row[csf('quantity')] + (($row[csf('quantity')] / 100) * $variable_setup_data[$row[csf('item_category')]]["OVER_RCV_PERCENT"]));
					$woPiBlance = $allow_qnty - $receive_arr[$row[csf("id")]][$row[csf('item_category')]] + $receive_rtn_arr[$row[csf("id")]][$row[csf('item_category')]];
				}

				if ($woPiBlance > 0) {
			?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $row[csf('id')] . "_" . $row[csf('wopi_number')] . "_" . $appr_status[$row[csf('id')]]; ?>')">

						<td width="40"><? echo "$i"; ?></td>
						<td width="70">
							<p><? echo $row[csf('wopi_number')]; ?></p>
						</td>
						<td width="120">
							<p><? echo $location_lib_arr[$row[csf('location_id')]]; ?></p>
						</td>
						<td width="80">
							<p><? echo $row[csf('lc_number')]; ?></p>
						</td>
						<td width="90">
							<p><? echo change_date_format($row[csf('wopi_date')]); ?> </p>
						</td>
						<td width="150" align="center">
							<p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p>
						</td>
						<td width="60">
							<p><? echo $currency[$row[csf('currency_id')]]; ?></p>
						</td>
						<td width="100">
							<p><? echo $source[$row[csf('source')]]; ?></p>
						</td>
						<td width="100">
							<p><? echo $item_category[$row[csf('item_category')]]; ?>&nbsp;</p>
						</td>
						<td align="center">
							<p><? echo $appr_status[$row[csf('id')]]; ?></p>
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

//after select wo/pi number get form data here---------------------------//
if ($action == "populate_data_from_wopi_popup") {
	$ex_data = explode("**", $data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID = $ex_data[1];

	if ($receive_basis == 1) //PI
	{
		if ($db_type == 0) {
			$sql = "select b.id as id, a.pi_number as wopi_number, b.lc_number as lc_number, a.supplier_id as supplier_id, a.currency_id as currency_id, a.source as source, '' as pay_mode, a.id as pi_id, null as reference  
				from com_pi_master_details a left join com_btb_lc_master_details b on FIND_IN_SET(a.id,b.pi_id)
				where a.id=$wo_pi_ID and a.item_category_id not in (1,2,3,5,6,7,12,13,14) and
				a.status_active=1 and a.is_deleted=0";
		}
		if ($db_type == 1 || $db_type == 2) {
			$sql = "select b.id as id, a.pi_number as wopi_number, b.lc_number as lc_number, a.supplier_id as supplier_id, a.currency_id as currency_id, a.source as source, '' as pay_mode , a.id as pi_id, null as reference
				from com_pi_master_details a left join com_btb_lc_pi c on a.id=c.pi_id left join com_btb_lc_master_details b on c.com_btb_lc_master_details_id=b.id
				where  a.id=$wo_pi_ID and a.item_category_id not in (1,2,3,5,6,7,12,13,14) and
				a.status_active=1 and a.is_deleted=0";
		}
	} else if ($receive_basis == 2) //WO
	{
		$sql = "select id, wo_number as wopi_number, '' as lc_number, supplier_id as supplier_id, currency_id as currency_id, source as source, pay_mode, 0 as pi_id, reference
				from wo_non_order_info_mst
				where id=$wo_pi_ID and status_active=1 and is_deleted=0 and
				entry_form in(146,147)";
	} else if ($receive_basis == 7) //Requisition
	{
		$sql = "select id, requ_no as wopi_number,'' as lc_number, requisition_date as wopi_date, '' as supplier_id, cbo_currency as currency_id, source as source, pay_mode as pay_mode, 0 as pi_id, reference 
				from inv_purchase_requisition_mst
				where id=$wo_pi_ID and status_active=1 and is_deleted=0 and pay_mode=4";
	}
	// echo $sql;die;
	$result = sql_select($sql);
	foreach ($result as $row) {
		echo "$('#txt_wo_pi_req').val('" . $row[csf("wopi_number")] . "');\n";
		echo "$('#cbo_supplier').val(" . $row[csf("supplier_id")] . ");\n";
		echo "$('#cbo_currency').val(" . $row[csf("currency_id")] . ");\n";
		echo "$('#txt_ref_no').val('" . $row[csf("reference")] . "');\n";
		echo "check_exchange_rate();\n";

		/*if($row[csf("currency_id")]==1)
		{
			echo "$('#txt_exchange_rate').val(1);\n";
		}*/

		echo "$('#cbo_source').val(" . $row[csf("source")] . ");\n";
		if ($receive_basis == 1) {
			//$pay_mode=return_field_value("b.pay_mode as pay_mode","com_pi_item_details a, wo_non_order_info_mst b"," a.work_order_id=b.id and a.pi_id=".$row[csf("pi_id")],"pay_mode");
			echo "$('#cbo_pay_mode').val(2);\n";
		} else {
			echo "$('#cbo_pay_mode').val(" . $row[csf("pay_mode")] . ");\n";
		}

		echo "$('#txt_lc_no').val('" . $row[csf("lc_number")] . "');\n";
		if ($row[csf("lc_number")] != "") {
			echo "$('#hidden_lc_id').val(" . $row[csf("id")] . ");\n";
		}
	}
	exit();
}

//right side product list create here--------------------//
if ($action == "show_product_listview") {
	$ex_data = explode("**", $data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID      = $ex_data[1];
	$company_id    = $ex_data[2];
	$store_id      = $ex_data[3];

	//$dec_place_arr=return_item_dec_place_array(0,$company_id);
	//$item_group_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");
	$item_grp_sql = sql_select("select id, item_name, conversion_factor from lib_item_group where status_active=1 and is_deleted=0");
	$item_group_arr = array();
	foreach ($item_grp_sql as $row) {
		$item_group_arr[$row[csf("id")]]['item_name'] = $row[csf("item_name")];
		$item_group_arr[$row[csf("id")]]['conversion_factor'] = $row[csf("conversion_factor")];
	}
	/*$receive_return_sql = sql_select("select a.booking_id, c.prod_id, d.item_group_id, sum(c.cons_quantity) as issue_qnty 
	from inv_receive_master a, inv_issue_master b, inv_transaction c, product_details_master d
	where a.id=b.received_id and b.id=c.mst_id and c.prod_id=d.id and a.booking_id=".$wo_pi_ID." and a.receive_basis=$receive_basis and b.entry_form=26 and c.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
	group by a.booking_id, c.prod_id, d.item_group_id");*/
	$receive_return_sql = sql_select("select a.booking_id, b.prod_id, c.item_group_id, c.conversion_factor, sum(b.cons_quantity) as issue_qnty 
	from inv_issue_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=3 and a.received_id>0 and a.entry_form=26 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id 
	group by a.booking_id, b.prod_id, c.item_group_id, c.conversion_factor");
	foreach ($receive_return_sql as $row) {
		//$receive_rtn_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]+=$row[csf("issue_qnty")]/$item_conversion_factor[$row[csf("conversion_factor")]];
		$receive_rtn_arr[$row[csf("booking_id")]][$row[csf("prod_id")]] += $row[csf("issue_qnty")] / $row[csf("conversion_factor")];
	}

	$receive_sql = sql_select("select a.id, a.booking_id, b.prod_id, sum(b.order_qnty) as receive_qnty 
	from inv_receive_master a, inv_transaction b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=20 and a.receive_basis=$receive_basis and b.transaction_type=1 and a.booking_id=$wo_pi_ID
	group by a.id, a.booking_id, b.prod_id");
	foreach ($receive_sql as $row) {
		$receive_arr[$row[csf("booking_id")]][$row[csf("prod_id")]] += $row[csf("receive_qnty")];
	}
	//echo '<pre>';print_r($receive_arr);

	if ($receive_basis == 1) // pi basis
	{
		$sql = "select a.id,c.id as prod_id, a.importer_id as company_id, a.supplier_id, c.item_description as product_name_details, c.item_group_id, c.sub_group_name, sum(b.quantity) as quantity, 0 as requisition_dtls_id, c.item_size, c.item_number
		from com_pi_master_details a, com_pi_item_details b, product_details_master c
		where a.id=b.pi_id and b.item_prod_id=c.id and a.id=$wo_pi_ID and a.status_active=1 and b.status_active=1 and c.status_active in(1,3) 
		group by a.id,a.importer_id,a.supplier_id,c.id,c.item_description,c.item_group_id, c.sub_group_name, c.item_size, c.item_number";
	} 
	else if ($receive_basis == 2) // wo basis
	{
		//,b.requisition_dtls_id  group by , b.id order by b.id asc
		$sql = "select  a.id ,c.id as prod_id,a.company_name as company_id,a.supplier_id, c.item_description as product_name_details, c.item_group_id, c.sub_group_name, 0 as requisition_dtls_id, sum(b.supplier_order_quantity) as quantity, c.item_size, c.item_number
		from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master c
		where a.id=b.mst_id and a.id=$wo_pi_ID and a.pay_mode in (1,4) and b.item_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,3) 
		group by a.id,a.company_name,a.supplier_id, c.id, c.item_description,c.item_group_id, c.sub_group_name, c.item_size, c.item_number";
	} 
	else if ($receive_basis == 7) // requisition basis
	{
		$sql = "select  a.id,c.id as prod_id,a.company_id, '' as supplier_id, c.item_description as product_name_details, c.item_group_id, c.sub_group_name, sum(b.quantity) as quantity,0 as requisition_dtls_id, c.item_size, c.item_number
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c 
		where a.id=b.mst_id and b.product_id=c.id and a.id=$wo_pi_ID and b.item_category in (" . implode(",", array_flip($general_item_category)) . ") and a.pay_mode=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,3) 
		group by a.id,a.company_id,c.id,c.item_description,c.item_group_id, c.sub_group_name, c.item_size, c.item_number";
	}
	//echo "test9";
	//echo $sql; die;
	$result = sql_select($sql);
	$i = 1;
?>
	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" width="510" rules="all">
		<thead>
			<tr>
				<th width="15">SL</th>
				<th width="130">Product Name</th>
				<th width="60">Item Size</th>
				<th width="60">Item Number</th>
				<th width="60">Item Group</th>
				<th width="60">Item Sub Group</th>
				<th width="40">Wo/RQ /PI Qnty</th>
				<th width="40">Receive Qnty</th>
				<th>Balance</th>
			</tr>
		</thead>
		<tbody id="list_view">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
				$productName = $row[csf("product_name_details")];
				$receive_qty = $receive_arr[$row[csf("id")]][$row[csf("prod_id")]] - $receive_rtn_arr[$row[csf("id")]][$row[csf("prod_id")]];
				$balance_quantity = $row[csf("quantity")] - $receive_qty;
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="get_php_form_data('<? echo $receive_basis . "**" . $row[csf("id")] . "**" . $row[csf("company_id")] . "**" . $row[csf("supplier_id")] . "**" . $row[csf("quantity")] . "**" . $row[csf("prod_id")] . "**" . $balance_quantity . "**" . $row[csf("requisition_dtls_id")] . "**" . $store_id; ?>','wo_pi_product_form_input','requires/general_item_receive_controller'); change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
					<td><? echo $i; ?></td>
					<td>
						<p><? echo $productName; ?>&nbsp;</p>
					</td>
					<td>
						<p><? echo $row[csf("item_size")]; ?>&nbsp;</p>
					</td>
					<td>
						<p><? echo $row["ITEM_NUMBER"]; ?>&nbsp;</p>
					</td>
					<td>
						<p><? echo $item_group_arr[$row[csf("item_group_id")]]['item_name']; ?>&nbsp;</p>
					</td>
					<td>
						<p><? echo $row[csf("sub_group_name")]; ?>&nbsp;</p>
					</td>
					<td align="right"><? echo number_format($row[csf("quantity")], 0);  ?></td>
					<td align="right"><? echo number_format($receive_qty, 0); ?></td>
					<td align="right"><? echo number_format($balance_quantity, 0); ?></td>
				</tr>
			<?
				$i++;
			}
			?>
		</tbody>
	</table>
	</fieldset>
<?
	exit();
}

// get form data from product click in right side
if ($action == "wo_pi_product_form_input") {
	$ex_data = explode("**", $data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID = $ex_data[1];
	$company_id = $ex_data[2];
	$supplier_id = $ex_data[3];
	$wo_po_qnty = $ex_data[4];
	$product_id = $ex_data[5];
	$balance_quantity = $ex_data[6];
	$requisition_dtls_id = $ex_data[7];
	$store_id = $ex_data[8];

	$store_wise_qty = return_field_value("sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as store_stock", "inv_transaction", "prod_id=$product_id and store_id=$store_id and status_active=1 and is_deleted=0", "store_stock");
	//echo $store_wise_qty.'bcghvsgh';die;

	if ($receive_basis == 1) // pi basis
	{
		$sql = "select a.importer_id as company_id, a.supplier_id, c.id, c.item_category_id as item_category, c.item_group_id as item_group, c.item_description as item_description, b.uom as cons_uom, (sum(net_pi_amount)/sum(b.quantity)) as rate, sum(b.quantity) as quantity, c.current_stock as global_stock, c.brand_name, c.origin, c.model, c.re_order_label
		from com_pi_master_details a, com_pi_item_details b, product_details_master c
		where a.id=$wo_pi_ID and c.id=$product_id and a.id=b.pi_id and b.item_prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by c.id,c.item_category_id, c.item_group_id,c.item_description,a.importer_id, a.supplier_id,b.uom,c.brand_name,c.origin,c.model,c.current_stock, c.re_order_label";
	} else if ($receive_basis == 2) // wo basis 
	{
		//if($requisition_dtls_id!=0) $requisition_cond=" and b.requisition_dtls_id =$requisition_dtls_id"; , avg(b.rate) as rate,
		$sql = "select a.company_name as company_id, a.supplier_id, c.id, c.item_category_id as item_category, c.item_group_id as item_group, c.item_description as item_description, b.uom as cons_uom, sum(b.supplier_order_quantity) as quantity, sum(b.amount) as amount, (sum(b.amount)/sum(b.supplier_order_quantity)) as rate, c.current_stock as global_stock, c.brand_name, c.origin, c.model, c.re_order_label
		from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master c
		where a.id=b.mst_id and a.id=$wo_pi_ID and c.id=$product_id and a.pay_mode in (1,4) and b.item_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by c.id,c.item_category_id, c.item_group_id,c.item_description,a.company_name,a.supplier_id,b.uom,c.brand_name,c.origin,c.model, c.current_stock, c.re_order_label";
	} else if ($receive_basis == 7) //  requisition basis
	{
		$sql = "select a.company_id, '' as supplier_id, c.id, b.item_category as item_category, c.item_group_id as item_group, c.item_description as item_description, b.cons_uom, (sum(amount)/sum(b.quantity)) as rate, sum(b.quantity) as quantity, c.current_stock as global_stock,c.brand_name,c.origin,c.model, c.re_order_label
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c 
		where a.id=b.mst_id and b.product_id=c.id and a.id=$wo_pi_ID and c.id=$product_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by c.id,b.item_category, c.item_group_id,c.item_description,a.company_id,a.item_category_id,b.cons_uom,c.brand_name,c.origin,c.model, c.current_stock, c.re_order_label";
	}

	//echo $sql;

	$result = sql_select($sql);
	foreach ($result as $row) {
		$rate = number_format($row[csf("rate")], 8, ".", "");
		echo "$('#cbo_supplier').val(" . $row[csf("supplier_id")] . ");\n";
		echo "$('#cbo_item_category').val(" . $row[csf("item_category")] . ");\n";
		//'".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."'
		echo "load_drop_down( 'requires/general_item_receive_controller','" . $row[csf("item_category")] . "_" . $row[csf("company_id")] . "', 'load_drop_down_itemgroup', 'item_group_td' );";
		echo "$('#cbo_item_group').val(" . $row[csf("item_group")] . ");\n";
		echo "$('#txt_item_desc').val('" . $row[csf("item_description")] . "');\n";
		echo "$('#current_prod_id').val('" . $row[csf("id")] . "');\n";
		echo "$('#txt_glob_stock').val('" . $store_wise_qty . "');\n";
		//echo "$('#txt_serial_no').val(".$row[csf("")].");\n";
		echo "$('#cbo_uom').val(" . $row[csf("cons_uom")] . ");\n";
		echo "$('#txt_rate').val('" . $rate . "');\n";
		echo "$('#txt_brand').val('" . $row[csf("brand_name")] . "');\n";
		echo "$('#cbo_origin').val('" . $row[csf("origin")] . "');\n";
		echo "$('#txt_model').val('" . $row[csf("model")] . "');\n"; //new dev
		echo "$('#txt_re_order_level').val('" . $row[csf("re_order_label")] . "');\n"; //new dev
		echo "$('#hid_req_dtls_id').val('" . $requisition_dtls_id . "');\n"; //new dev for WO
		echo "$('#txt_order_qty').val('" . $balance_quantity . "');\n";

		echo "$('#cbo_item_category').attr('disabled',true);\n";
		echo "$('#cbo_item_group').attr('disabled',true);\n";
		echo "$('#txt_item_desc').attr('disabled',true);\n";
		echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_general_item_receive_entry',1);\n";

		echo "fn_calile();\n";
	}
	exit();
}
// LC popup here----------------------// 
if ($action == "lc_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
		function js_set_value(str) {
			var splitData = str.split("_");
			$("#hidden_tbl_id").val(splitData[0]); // wo/pi id
			$("#hidden_wopi_number").val(splitData[1]); // wo/pi number
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
				<table width="600" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="150">Search By</th>
							<th width="150" align="center" id="search_by_td_up">Enter WO/PI Number</th>
							<th>
								<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" />
								<!-- Hidden field here-->
								<input type="hidden" id="hidden_tbl_id" value="" />
								<input type="hidden" id="hidden_wopi_number" value="hidden_wopi_number" />
								<!--END-->
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?
								$search_by_arr = array(0 => 'LC Number', 1 => 'Supplier Name');
								$dd = "change_search_event(this.value, '0*1', '0*select id, supplier_name from lib_supplier', '../../../') ";
								echo create_drop_down("cbo_search_by", 170, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td width="180" align="center" id="search_by_td">
								<input type="text" style="width:230px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>, 'create_lc_search_list_view', 'search_div', 'general_item_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
					</tbody>
				</table>
				<div align="center" valign="top" id="search_div"> </div>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "create_lc_search_list_view") {
	$ex_data = explode("_", $data);
	$cbo_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$company = $ex_data[2];

	if ($cbo_search_by == 1 && $txt_search_common != "") // lc number
	{
		$sql = "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where lc_number LIKE '%$search_string%' and importer_id=$company and item_category_id=1 and is_deleted=0 and status_active=1";
	} else if ($cbo_search_by == 1 && $txt_search_common != "") //supplier
	{
		$sql = "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where supplier_id='$search_string' and importer_id=$company and item_category_id=1 and is_deleted=0 and status_active=1";
	} else {
		$sql = "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where importer_id=$company and item_category_id=1 and is_deleted=0 and status_active=1";
	}

	$company_arr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$arr = array(1 => $company_arr, 2 => $supplier_arr, 3 => $item_category);
	echo  create_list_view("list_view", "LC No,Importer,Supplier Name,Item Category,Value", "120,150,150,120,120", "750", "260", 0, $sql, "js_set_value", "id,lc_number", "", 1, "0,importer_id,supplier_id,item_category_id,0", $arr, "lc_number,importer_id,supplier_id,item_category_id,lc_value", "", '', '0,0,0,0,0,1');
	exit();
}

if ($action == "show_ile") {
	$ex_data = explode("**", $data);
	$company = $ex_data[0];
	$source = $ex_data[1];
	$rate = $ex_data[2];
	$category = $ex_data[3];
	$group = $ex_data[4];

	if ($db_type == 0) {
		$sql = "select standard from variable_inv_ile_standard where source='$source' and company_name='$company' and category=$category and status_active=1 and is_deleted=0 order by id limit 1";
	} else {
		$sql = "select standard from variable_inv_ile_standard where source='$source' and company_name='$company' and category=$category and status_active=1 and is_deleted=0 and rownum <= 2 order by id desc";
	}

	//echo $sql;die;
	$result = sql_select($sql);
	foreach ($result as $row) {
		// NOTE :- ILE=standard, ILE% = standard/100*rate
		$ile = $row[csf("standard")];
		$ile_percentage = ($row[csf("standard")] / 100) * $rate;
		echo $ile . "**" . number_format($ile_percentage, $dec_place[3], ".", "");
		exit();
	}
	exit();
}

if ($action == "serial_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	if (str_replace("'", "", $serialString) != "") {
		$mainEx = explode("**", str_replace("'", "", $serialString));
		$serialArr = explode(",", $mainEx[0]);
		$qntyArr = explode(",", $mainEx[1]);
	}

?>
	<script>
		function add_break_down_tr(i) {
			var row_num = $('#tbl_serial tr').length - 1;
			if (row_num != i) {
				return false;
			} else {
				i++;
				$("#tbl_serial tr:last").clone().find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) {
							var id = id.split("_");
							return id[0] + "_" + i
						},
						'name': function(_, name) {
							return name + i
						},
						'value': function(_, value) {
							return value
						}
					});
				}).end().appendTo("#tbl_serial");
				$('#txtSerialNo_' + i).val('');
				$('#txtQuantity_' + i).removeClass("class").addClass("class", "text_boxes_numeric");
				$('#btnIncrease_' + i).removeAttr("onClick").attr("onClick", "add_break_down_tr(" + i + ");");
				$('#btnDecrease_' + i).removeAttr("onClick").attr("onClick", "fn_deletebreak_down_tr(" + i + ")");
				$('#txtSerialNo_' + i).removeAttr("onBlur").attr("onBlur", "fn_check_serial(" + i + ")");
			}
		}

		function fn_deletebreak_down_tr(rowNo) {
			var numRow = $('table#tbl_serial tbody tr').length;
			if (numRow == rowNo && rowNo != 1) {
				$('#tbl_serial tbody tr:last').remove();
			}
		}

		function fnClosed() {
			var numRow = $('table#tbl_serial tr').length;
			var serialS = "";
			var qntyS = "";
			for (var i = 1; i < numRow; i++) {
				if (i * 1 > 1) {
					serialS += ",";
					qntyS += ",";
				}
				serialS += $("#txtSerialNo_" + i).val();
				qntyS += $("#txtQuantity_" + i).val();
				if (form_validation('txtSerialNo_' + i, 'Serial') == false) {
					return;
				}
			}
			var txtString = serialS; //+"**"+qntyS;
			$("#txt_string").val(txtString);
			$("#txt_qty").val(qntyS);
			parent.emailwindow.hide();
		}

		function fn_check_serial(rowNo) {
			if (rowNo != 1) {
				var table_length = $('#tbl_serial tr').length;
				for (var i = 1; i <= rowNo - 1; i++) {
					if (($('#txtSerialNo_' + i).val() * 1) == ($('#txtSerialNo_' + rowNo).val() * 1)) {
						$('#txtSerialNo_' + rowNo).val("");
					}
				}
			}
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
				<table width="450" cellspacing="0" cellpadding="0" border="0" class="rpt_table" id="tbl_serial">
					<thead>
						<tr>
							<th width="260" class="must_entry_caption">Serial No</th>
							<th width="80">Quantity</th>
							<th width="120">Action</th>
						</tr>
					</thead>
					<tbody>
						<?
						$chkNo = sizeof($serialArr);
						if (!empty($serialArr[0])) {
							for ($j = 1; $j <= $chkNo; $j++) {
						?>
								<tr>
									<td>
										<input type="text" id="txtSerialNo_<? echo $j; ?>" name="txtSerialNo_<? echo $j; ?>" style="width:250px" class="text_boxes" value="<? echo $serialArr[$j - 1]; ?>" onBlur="fn_check_serial(<? echo $j; ?>)" />
									</td>
									<td>
										<input type="text" id="txtQuantity_<? echo $j; ?>" name="txtQuantity_<? echo $j; ?>" style="width:70px" class="text_boxes_numeric" value="<? echo $qntyArr[$j - 1]; ?>" disabled />
									</td>
									<td>
										<input type="button" id="btnIncrease_<? echo $j; ?>" name="btnIncrease_<? echo $j; ?>" class="formbutton" style="width:40px" onClick="add_break_down_tr(<? echo $j; ?>)" value="+" />
										<input type="button" id="btnDecrease_<? echo $j; ?>" name="btnDecrease_<? echo $j; ?>" class="formbutton" style="width:40px" onClick="fn_deletebreak_down_tr(<? echo $j; ?>)" value="-" />
									</td>
								</tr>
							<?
							}
						} else {
							?>
							<tr>
								<td>
									<input type="text" id="txtSerialNo_1" name="txtSerialNo_1" style="width:250px" class="text_boxes" value="" onBlur="fn_check_serial(1)" />
								</td>
								<td>
									<input type="text" id="txtQuantity_1" name="txtQuantity_1" style="width:70px" class="text_boxes_numeric" value="1" disabled />
								</td>
								<td>
									<input type="button" id="btnIncrease_1" name="btnIncrease_1" class="formbutton" style="width:40px" onClick="add_break_down_tr(1)" value="+" />
									<input type="button" id="btnDecrease_1" name="btnDecrease_1" class="formbutton" style="width:40px" onClick="fn_deletebreak_down_tr(1)" value="-" />
								</td>
							</tr>
						<? } ?>
					</tbody>
				</table>
				<div><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fnClosed()" /></div>
				<!-- Hidden field here -->
				<input type="hidden" id="txt_string" value="" />
				<input type="hidden" id="txt_qty" value="" />
				<!-- END -->
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "item_description_popup") {
	echo load_html_head_contents("Item popup", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(item_description) {
			var splitArr = item_description.split("_");
			$("#product_id_td").val(splitArr[0]);
			$("#item_description_td").val(splitArr[1]);
			$("#current_stock").val(splitArr[2]);
			$("#brand_name").val(splitArr[3]);
			$("#origin").val(splitArr[4]);
			$("#model").val(splitArr[5]);
			$("#order_uom").val(splitArr[6]);
			$("#re_order_level").val(splitArr[7]);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%">
			<form name="item_popup_1" id="item_popup_1">
				<table>
					<thead>
						<tr>
							<th colspan="8"><? echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --"); ?></th>
						</tr>
					</thead>
				</table>
				<?
				$entry_cond = "";
				if (str_replace("'", "", $item_category) == 4) $entry_cond = "and a.entry_form=20";
				//$sql="select a.id, a.item_code, a.item_description, a.item_size, a.current_stock, a.brand_name, a.origin, a.item_number, a.model, a.sub_group_name, a.order_uom, a.re_order_label, sum((case when b.transaction_type in in(1,4,5) then cons_quantity else 0 end)-(case when b.transaction_type in in(2,3,6) then cons_quantity else 0 end)) as store_current_stock
				//	from product_details_master a left join inv_transaction b on a.id=b.prod_id and b.store_id=$cbo_store_name 
				//	where status_active=1 and is_deleted=0 and company_id=$company_id and item_category_id=$item_category and item_group_id=$item_group $entry_cond";

				$sql = "select a.id, a.item_code, a.item_description, a.item_size, a.current_stock, a.brand_name, a.origin, a.item_number, a.model, a.sub_group_name, a.order_uom, a.re_order_label, 
sum((case when b.transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then cons_quantity else 0 end)) as store_current_stock 
	from product_details_master a 
	left join inv_transaction b on a.id=b.prod_id and b.store_id=$cbo_store_name and b.status_active=1 and b.is_deleted=0 
	where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.item_category_id=$item_category and a.item_group_id=$item_group 
	group by a.id, a.item_code, a.item_description, a.item_size, a.current_stock, a.brand_name, a.origin, a.item_number, a.model, a.sub_group_name, a.order_uom, a.re_order_label";
				//echo $sql;//die;
				$arr = array(4 => $unit_of_measurement);
				echo  create_list_view("list_view", "Product Id,Item Account,Item Code,Item Description,UOM,Re-Order Level,Item Size,Item Number,Model,Sub Group", "50,80,70,190,70,70,80,80,70", "930", "250", 0, $sql, "js_set_value", "id,item_description,store_current_stock,brand_name,origin,model,order_uom,re_order_label", "", 1, "0,0,0,0,order_uom,0,0,0,0", $arr, "id,item_account,item_code,item_description,order_uom,re_order_label,item_size,item_number,model,sub_group_name", "", 'setFilterGrid("list_view",-1);');

				/*$arr=array(1=>$supplier_arr,5=>$receive_basis_arr);
	echo create_list_view("list_view", "MRR No, Supplier Name, Challan No, LC No, Receive Date, Receive Basis,WO Number,PI Number, Receive Qnty","120,120,120,120,120,100,120,80,80","1050","260",0, $sql , "js_set_value", "rcv_id", "", 1, "0,supplier_id,0,0,0,receive_basis,0,0,0", $arr, "recv_number,supplier_id,challan_no,lc_number,receive_date,receive_basis,wo_number,pi_number,receive_qnty", "",'','0,0,0,0,0,0,0,0,2') ;*/
				?>
				<input type="hidden" id="item_description_td" />
				<input type="hidden" id="product_id_td" />
				<input type="hidden" id="current_stock" />
				<input type="hidden" id="brand_name" />
				<input type="hidden" id="origin" />
				<input type="hidden" id="model" />
				<input type="hidden" id="order_uom" />
				<input type="hidden" id="re_order_level" />
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "addi_info_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$user_info_arr = return_library_array("SELECT a.id, a.user_full_name, b.custom_designation from user_passwd a, lib_designation b where a.designation = b.id and a.valid = 1 order by a.user_full_name", "id", "user_full_name");


	if (str_replace("'", "", $pre_addi_info)) {
		$pre_addi_info_arr = explode("_", str_replace("'", "", $pre_addi_info));
		$pre_txt_book_no = $pre_addi_info_arr[0];
		$txt_challan_date = $pre_addi_info_arr[1];
		$txt_bill_no = $pre_addi_info_arr[2];
		$txt_bill_date = $pre_addi_info_arr[3];
		$cbo_purchaser_name = $pre_addi_info_arr[4];
		$cbo_carried_by = $pre_addi_info_arr[5];
		$cbo_qc_check_by = $pre_addi_info_arr[6];
		$cbo_receive_by = $pre_addi_info_arr[7];
		$cbo_gate_entry_by = $pre_addi_info_arr[8];

		$cbo_purchaser_name_show = $user_info_arr[$pre_addi_info_arr[4]];
		$cbo_carried_by_show = $user_info_arr[$pre_addi_info_arr[5]];
		$cbo_qc_check_by_show = $user_info_arr[$pre_addi_info_arr[6]];
		$cbo_receive_by_show = $user_info_arr[$pre_addi_info_arr[7]];
		$cbo_gate_entry_by_show = $user_info_arr[$pre_addi_info_arr[8]];

		$txt_gate_entry_date = $pre_addi_info_arr[9];
		$txt_addi_receive_date = $pre_addi_info_arr[10];
		$txt_gate_entry_no = $pre_addi_info_arr[11];
		$txt_store_sl_no = $pre_addi_info_arr[12];
	}

?>
	<script>
		function fnClosed() {
			var txtString = "";
			txtString = $("#txt_book_no").val() + '_' + $("#txt_challan_date").val() + '_' + $("#txt_bill_no").val() + '_' + $("#txt_bill_date").val() + '_' + $("#cbo_purchaser_name").val() + '_' + $("#cbo_carried_by").val() + '_' + $("#cbo_qc_check_by").val() + '_' + $("#cbo_receive_by").val() + '_' + $("#cbo_gate_entry_by").val() + '_' + $("#txt_gate_entry_date").val() + '_' + $("#txt_addi_receive_date").val() + '_' + $("#txt_gate_entry_no").val() + '_' + $("#txt_store_sl_no").val();
			$("#txt_string").val(txtString);
			parent.emailwindow.hide();
		}

		function openmypage_user_info(field_id) {
			var title = "User Info Popup";
			var pre_addi_info = $('#txt_addi_info').val();
			page_link = 'general_item_receive_controller.php?action=user_info_popup';
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=410px, height=250px, center=1, resize=0, scrolling=0', '../../')
			emailwindow.onclose = function() {
				var theform = this.contentDoc.forms[0];
				var user_id = this.contentDoc.getElementById("user_id").value;
				var txt_name = this.contentDoc.getElementById("txt_name").value;
				$('#' + field_id).val(user_id);
				$('#' + field_id + '_show').val(txt_name);
			}
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<br>
			<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
				<fieldset style="width:650px;">
					<table width="650" cellspacing="2" cellpadding="0" border="0">
						<tr>
							<td width="100">
								<b>Rcvd/Book No.</b>
							</td>
							<td>
								<input type="text" id="txt_book_no" name="txt_book_no" style="width:150px" class="text_boxes" value="<? echo $pre_txt_book_no; ?>" />
							</td>

							<td width="100">
								<b>Receive Date</b>
							</td>
							<td>
								<input type="text" id="txt_addi_receive_date" name="txt_addi_receive_date" style="width:150px" class="datepicker" value="<? echo $txt_addi_receive_date; ?>" readonly />
							</td>
						</tr>
						<tr>
							<td width="100">
								<b>Challan Date</b>
							</td>
							<td>
								<input type="text" id="txt_challan_date" name="txt_challan_date" style="width:150px" class="datepicker" value="<? echo $txt_challan_date; ?>" readonly />
							</td>
							<td width="100">
								<b>Bill No.</b>
							</td>
							<td>
								<input type="text" id="txt_bill_no" name="txt_bill_no" style="width:150px" class="text_boxes" value="<? echo $txt_bill_no; ?>" />
							</td>
						</tr>
						<tr>
							<td width="100">
								<b>Bill Date</b>
							</td>
							<td>
								<input type="text" id="txt_bill_date" name="txt_bill_date" style="width:150px" class="datepicker" value="<? echo $txt_bill_date; ?>" readonly />
							</td>
							<td width="100">
								<b>Purchaser Name</b>
							</td>
							<td>
								<?
								//echo create_drop_down( "cbo_purchaser_name", 160, "select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name","id,user_full_name", 1, "-- Select --", $cbo_purchaser_name, "" );
								?>
								<input type="text" class="text_boxes" id="cbo_purchaser_name_show" value="<? echo $cbo_purchaser_name_show; ?>" onDblClick="openmypage_user_info('cbo_purchaser_name')" style="width: 150px" placeholder="Browse" readonly>
								<input type="hidden" class="text_boxes" id="cbo_purchaser_name" value="<? echo $cbo_purchaser_name; ?>">
							</td>
						</tr>
						<tr>
							<td width="100">
								<p><b>Carried By</b>(Deliveried By)</p>
							</td>
							<td>
								<?
								//echo create_drop_down( "cbo_carried_by", 160, "select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name","id,user_full_name", 1, "-- Select --", $cbo_carried_by, "" );
								?>
								<input type="text" class="text_boxes" id="cbo_carried_by_show" value="<? echo $cbo_carried_by_show; ?>" onDblClick="openmypage_user_info('cbo_carried_by')" style="width: 150px" placeholder="Browse" readonly>
								<input type="hidden" class="text_boxes" id="cbo_carried_by" value="<? echo $cbo_carried_by; ?>">
							</td>
							<td width="100">
								<b>QC Check By</b>
							</td>
							<td>
								<?
								//echo create_drop_down( "cbo_qc_check_by", 160, "select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name","id,user_full_name", 1, "-- Select --", $cbo_qc_check_by, "" );
								?>
								<input type="text" class="text_boxes" id="cbo_qc_check_by_show" value="<? echo $cbo_qc_check_by_show; ?>" onDblClick="openmypage_user_info('cbo_qc_check_by')" style="width: 150px" placeholder="Browse" readonly>
								<input type="hidden" class="text_boxes" id="cbo_qc_check_by" value="<? echo $cbo_qc_check_by; ?>">
							</td>

						</tr>
						<tr>
							<td width="100">
								<b>Received By</b>
							</td>
							<td>
								<?
								//echo create_drop_down( "cbo_receive_by", 160, "select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name","id,user_full_name", 1, "-- Select --", $cbo_receive_by, "" );
								?>
								<input type="text" class="text_boxes" id="cbo_receive_by_show" value="<? echo $cbo_receive_by_show; ?>" onDblClick="openmypage_user_info('cbo_receive_by')" style="width: 150px" placeholder="Browse" readonly>
								<input type="hidden" class="text_boxes" id="cbo_receive_by" value="<? echo $cbo_receive_by; ?>">
							</td>
							<td width="100">
								<b>Gate Entry No</b>
							</td>
							<td>
								<input type="text" id="txt_gate_entry_no" name="txt_gate_entry_no" class="text_boxes" style="width:150px" value="<? echo $txt_gate_entry_no; ?>" />
							</td>
						</tr>
						<tr>
							<td width="100">
								<b>Gate Entry By</b>
							</td>
							<td>
								<?
								//echo create_drop_down( "cbo_gate_entry_by", 160, "select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name","id,user_full_name", 1, "-- Select --", $cbo_gate_entry_by, "" );
								?>
								<input type="text" class="text_boxes" id="cbo_gate_entry_by_show" value="<? echo $cbo_gate_entry_by_show; ?>" onDblClick="openmypage_user_info('cbo_gate_entry_by')" style="width: 150px" readonly placeholder="Browse">
								<input type="hidden" class="text_boxes" id="cbo_gate_entry_by" value="<? echo $cbo_gate_entry_by; ?>">
							</td>

							<td width="100">
								<b>Gate Entry Date</b>
							</td>
							<td>
								<input type="text" id="txt_gate_entry_date" name="txt_gate_entry_date" style="width:150px" class="datepicker" value="<? echo $txt_gate_entry_date; ?>" readonly />
							</td>

						</tr>
						<tr>
							<td width="100">
								<b>Store Sl No.</b>
							</td>
							<td>
								<input type="text" class="text_boxes" id="txt_store_sl_no" value="<? echo $txt_store_sl_no; ?>" style="width: 150px">
							</td>
						</tr>

					</table>
					<br>
					<div><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fnClosed()" /></div>

					<input type="hidden" id="txt_string" value="" />
					<br>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "user_info_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
		function js_set_value(str) {
			var splitArr = str.split("_");
			$("#user_id").val(splitArr[0]);
			$("#txt_name").val(splitArr[1]);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<br>
			<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
				<?
				$sql = "SELECT a.id,a.user_name, a.user_full_name, b.custom_designation from user_passwd a, lib_designation b where a.designation = b.id and a.valid = 1 order by a.user_full_name";
				echo  create_list_view("list_view", "User Id, User Full Name,Designation", "70,130,140", "370", "240", 0, $sql, "js_set_value", "id,user_full_name", "", 1, "0,0,0", $arr, "user_name,user_full_name,custom_designation", "", 'setFilterGrid("list_view",-1);');
				?>
				<input type="hidden" id="user_id" name="user_id">
				<input type="hidden" id="txt_name" name="txt_name">
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

//data save update delete here------------------------------//
if ($action == "save_update_delete") 
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$variable_set_invent = return_field_value("user_given_code_status", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=19 and item_category_id=$cbo_item_category", "user_given_code_status");

	$variable_store_wise_rate = return_field_value("auto_transfer_rcv", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=47 and item_category_id=8 and status_active=1 and is_deleted=0", "auto_transfer_rcv");
	if ($variable_store_wise_rate != 1) $variable_store_wise_rate = 2;
	//echo "10**$variable_store_wise_rate";die;
	$txt_rate = str_replace("'", "", $txt_rate);
	$txt_amount = str_replace("'", "", $txt_amount);
	$rate = str_replace("'", "", $txt_rate);
	$store_update_upto = str_replace("'", "", $store_update_upto);
	
	$cbo_floor = str_replace("'", "", $cbo_floor);
	$cbo_room = str_replace("'", "", $cbo_room);
	$txt_rack = str_replace("'", "", $txt_rack);
	$txt_shelf = str_replace("'", "", $txt_shelf);
	$cbo_bin = str_replace("'", "", $cbo_bin);
	
	if($store_update_upto > 1)
	{
		if($store_update_upto==6)
		{
			if($cbo_floor==0 || $cbo_room==0 || $txt_rack==0 || $txt_shelf==0 || $cbo_bin==0)
			{
				echo "30**Up To Bin Value Full Fill Required For Inventory";die;
			}
		}
		else if($store_update_upto==5)
		{
			if($cbo_floor==0 || $cbo_room==0 || $txt_rack==0 || $txt_shelf==0)
			{
				echo "30**Up To Shelf Value Full Fill Required For Inventory";die;
			}
			else
			{
				$cbo_bin=0;
			}
		}
		else if($store_update_upto==4 )
		{
			if($cbo_floor==0 || $cbo_room==0 || $txt_rack==0)
			{
				echo "30**Up To Rack Value Full Fill Required For Inventory";die;
			}
			else
			{
				$cbo_bin=0;$txt_shelf=0;
			}
		}
		else if($store_update_upto==3)
		{
			if($cbo_floor==0 || $cbo_room==0)
			{
				echo "30**Up To Room Value Full Fill Required For Inventory";die;
			}
			else
			{
				$cbo_bin=0;$txt_shelf=0;$txt_rack=0;
			}
		}
		else if($store_update_upto==2)
		{
			if($cbo_floor==0)
			{
				echo "30**Up To Floor Value Full Fill Required For Inventory";die;
			}
			else
			{
				$cbo_bin=0;$txt_shelf=0;$txt_rack=0;$cbo_room=0;
			}
		}
	}
	else
	{
		$cbo_bin=0;$txt_shelf=0;$txt_rack=0;$cbo_room=0;$cbo_floor=0;
	}
	
	// check MRR Auditing Report is Audited or Not
	if (str_replace("'", '', $txt_mrr_no) != '') {
		$is_audited = return_field_value("is_audited", "inv_receive_master", "id=" . str_replace("'", '', $hidden_mrr_id) . " and status_active=1 and is_deleted=0", "is_audited");
		//echo "10**$is_audited".'rakib';die;
		if ($is_audited == 1) {
			echo "50**This MRR is Audited. Save, Update and Delete Not Allowed..";
			die;
		}
	}

	if (str_replace("'", "", $txt_amount) * 1 <= 0) {
		echo "20**Receive Amount Not Allow Less Than Or Equal Zero";
		die;
	}

	//---------------Check Receive date with Last Transaction date-------------//
	$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$current_prod_id and transaction_type in (2,3,6) and status_active = 1", "max_date");
	if ($max_transaction_date != "") {
		$receive_date = strtotime(str_replace("'", "", $txt_receive_date));
		$max_transaction_date = strtotime($max_transaction_date);
		if ($receive_date < $max_transaction_date) {
			echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Item";
			//check_table_status($_SESSION['menu_id'], 0);
			disconnect($con);
			die;
		}
	}

	$sql_variable_setup = "select over_rcv_percent, over_rcv_payment from variable_inv_ile_standard where company_name = $cbo_company_id and category=$cbo_item_category and variable_list = 23 and status_active=1 and is_deleted=0";
	//echo "10**".$sql_variable_setup;die;
	$sql_variable_setup_result = sql_select($sql_variable_setup);
	$variable_over_rcv_percent = $sql_variable_setup_result[0][csf("over_rcv_percent")];
	$variable_over_rcv_payment = $sql_variable_setup_result[0][csf("over_rcv_payment")];

	$sql_variable_challan = sql_select("select auto_transfer_rcv from variable_settings_inventory where company_name = $cbo_company_id and variable_list = 38 and status_active=1 and is_deleted=0");
	$variale_challan_dup = $sql_variable_challan[0][csf("auto_transfer_rcv")];

	if ($operation == 0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0**0"; die;}

		//---------------Check Receive control on Gate Entry according to variable settings inventory---------------------------//
		$challan_no = str_replace("'", "", $txt_challan_no);
		$supplier_id = str_replace("'", "", $cbo_supplier);
		if ($challan_no != "") {
			if ($variable_set_invent == 1) {
				$variable_set_invent = return_field_value("a.id as id", " inv_gate_in_mst a,  inv_gate_in_dtl b", "a.id=b.mst_id and a.company_id=$cbo_company_id and a.challan_no='$challan_no' and b.item_category_id=$cbo_item_category  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "id");
				if (empty($variable_set_invent)) {
					echo "30** This Item Not Found In Gate Entry. \n Please Gate Entry First.";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}

			if ($variale_challan_dup == 2) {
				$hidden_mrr_id = str_replace("'", "", $hidden_mrr_id);
				if ($hidden_mrr_id != "") $mrr_cond = " and id<>$hidden_mrr_id";
				$duplicate_chalan = is_duplicate_field("id", "inv_receive_master ", "status_active=1 and is_deleted=0 and supplier_id=$supplier_id and challan_no='$challan_no' $mrr_cond");
				if ($duplicate_chalan == 1) {
					echo "30**Duplicate Challan No. Not Allow.";
					disconnect($con);
					die;
				}
			}
		}

		//---------------End Check Receive control on Gate Entry---------------------------//
		if ($db_type == 0) {
			$concattS = explode(",", return_field_value(" concat(unit_of_measure,',',conversion_factor) as cons_uom", "product_details_master", "id=$current_prod_id", "cons_uom"));
		}
		if ($db_type == 2) {
			$concattS = explode(",", return_field_value("(unit_of_measure || ',' ||conversion_factor) as cons_uom", "product_details_master", "id=$current_prod_id", "cons_uom"));
		}
		$cons_uom = $concattS[0];
		$conversion_factor = $concattS[1];

		//echo "10**$conversion_factor";die; 

		//---------------Check Meterial Over Receive control Start---------------------------//

		$rcv_basis = str_replace("'", "", $cbo_receive_basis);
		$wo_pi_req_id = str_replace("'", "", $txt_wo_pi_req_id);
		$cr_prod_id = str_replace("'", "", $current_prod_id);
		if ($variable_over_rcv_payment > 0 && $rcv_basis != 4 && $rcv_basis != 6) {
			$totalRtnQnty = return_field_value("sum(c.cons_quantity) as bal", "inv_issue_master b, inv_transaction c", "c.mst_id=b.id and b.booking_id=" . $wo_pi_req_id . " and c.prod_id=" . $cr_prod_id . " and b.entry_form=26 and c.transaction_type=3 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "bal");
			$totalRtnQnty = $totalRtnQnty / $conversion_factor;

			//txt_receive_qty current_prod_id
			$prev_rcv_sql = sql_select(" select sum(b.order_qnty) as qnty from inv_receive_master a, inv_transaction b where a.id = b.mst_id and a.entry_form = 20 and b.transaction_type=1 and a.receive_basis = $rcv_basis and a.booking_id = $wo_pi_req_id and b.item_category=$cbo_item_category and b.prod_id=$cr_prod_id and a.status_active=1 and b.status_active=1");

			$prev_rcv_qnty = $prev_rcv_sql[0][csf("qnty")] - $totalRtnQnty;
			$current_receive_qty = str_replace("'", "", $txt_receive_qty);
			$tot_qnty = $prev_rcv_qnty + $current_receive_qty;

			if ($rcv_basis == 1) {
				$wo_pi_req_sql = sql_select(" select sum(b.quantity) as quantity from com_pi_item_details b where b.status_active=1 and b.pi_id=$wo_pi_req_id and b.item_prod_id=$cr_prod_id");
				$wo_pi_req_qnty = $wo_pi_req_sql[0][csf("quantity")];
			} else if ($rcv_basis == 2) {
				$wo_pi_req_sql = sql_select(" select sum(b.supplier_order_quantity) as quantity from wo_non_order_info_dtls b where b.status_active=1 and b.mst_id=$wo_pi_req_id and b.item_id=$cr_prod_id");
				$wo_pi_req_qnty = $wo_pi_req_sql[0][csf("quantity")];
			} else {
				$wo_pi_req_sql = sql_select(" select sum(b.quantity) as quantity from inv_purchase_requisition_dtls b where b.status_active=1 and b.mst_id=$wo_pi_req_id and b.product_id=$cr_prod_id");
				$wo_pi_req_qnty = $wo_pi_req_sql[0][csf("quantity")];
			}

			$allow_qnty = ($wo_pi_req_qnty + (($wo_pi_req_qnty / 100) * $variable_over_rcv_percent));
			if ($tot_qnty > $allow_qnty) {
				echo "30** MRR Quantity Not Allow More Then PI/Wo/Req Allowed Quantity.";
				disconnect($con);
				die;
			}
		}
		//echo "10**done"; die;


		//---------------Check Meterial Over Receive control End---------------------------//



		//---------------Check Last Transaction date End -------------//


		//---------------Check Duplicate product in Same return number ------------------------//
		$duplicate = is_duplicate_field("b.id", "inv_receive_master a, inv_transaction b", "a.id=b.mst_id and a.id=$hidden_mrr_id and b.prod_id=$current_prod_id and b.transaction_type=1 and a.status_active=1 and b.status_active=1");
		if ($duplicate == 1) {
			echo "20**Duplicate Product is Not Allow in Same MRR Number.";
			disconnect($con);
			die;
		}


		//------------------------------Check product END---------------------------------------//

		$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value,available_qnty from product_details_master where id=$current_prod_id");
		$presentStock = $presentStockValue = $presentAvgRate = 0;
		$product_name_details = "";
		foreach ($sql as $result) {
			$presentStock		= $result[csf("current_stock")];
			$presentStockValue	= $result[csf("stock_value")];
			$presentAvgRate		= $result[csf("avg_rate_per_unit")];
			$product_name_details 	= $result[csf("product_name_details")];
			$available_qnty			= $result[csf("available_qnty")];
		}
		//----------------Check Product ID END---------------------//

		if ($txt_store_sl_no == "") $txt_store_sl_no = "''";

		$txt_challan_date = $txt_bill_date = $txt_gate_entry_date = $txt_addi_rcvd_date = "";
		$addi_info_arr = explode("_", str_replace("'", "", $txt_addi_info));

		$txt_book_no = $addi_info_arr[0];
		$txt_challan_date = $addi_info_arr[1];
		$txt_bill_no = $addi_info_arr[2];
		$txt_bill_date = $addi_info_arr[3];
		$cbo_purchaser_name = $addi_info_arr[4];
		$cbo_carried_by = $addi_info_arr[5];
		$cbo_qc_check_by = $addi_info_arr[6];
		$cbo_receive_by = $addi_info_arr[7];
		$cbo_gate_entry_by = $addi_info_arr[8];
		$txt_gate_entry_date = $addi_info_arr[9];
		$txt_addi_rcvd_date = $addi_info_arr[10];
		$txt_gate_entry_no = $addi_info_arr[11];
		$txt_store_sl_no = "'" . $addi_info_arr[12] . "'";

		if ($db_type == 0) {
			$txt_challan_date = change_date_format($txt_challan_date, 'yyyy-mm-dd');
			$txt_bill_date = change_date_format($txt_bill_date, 'yyyy-mm-dd');
			$txt_gate_entry_date = change_date_format($txt_gate_entry_date, 'yyyy-mm-dd');
			$txt_addi_rcvd_date = change_date_format($txt_addi_rcvd_date, 'yyyy-mm-dd');
		} else {
			$txt_challan_date = change_date_format($txt_challan_date, '', '', 1);
			$txt_bill_date = change_date_format($txt_bill_date, '', '', 1);
			$txt_gate_entry_date = change_date_format($txt_gate_entry_date, '', '', 1);
			$txt_addi_rcvd_date = change_date_format($txt_addi_rcvd_date, '', '', 1);
		}

		//details table entry here START-----------------------------------//
		//echo $cbo_item_group;die;		
		$txt_ile = str_replace("'", "", $txt_ile);
		$txt_receive_qty = str_replace("'", "", $txt_receive_qty);
		$ile = ($txt_ile / $rate) * 100; // ile cost to ile
		$ile_cost = str_replace("'", "", $txt_ile); //ile cost = (ile/100)*rate
		$exchange_rate = str_replace("'", "", $txt_exchange_rate);
		$domestic_rate = return_domestic_rate($rate, $ile_cost, $exchange_rate, $conversion_factor);
		$cons_rate = number_format($domestic_rate, 10, ".", ""); //number_format($rate*$exchange_rate,$dec_place[3],".","");
		$con_quantity = $conversion_factor * $txt_receive_qty;
		$con_amount = $cons_rate * $con_quantity;
		$con_ile = $ile / $conversion_factor; //($ile/$domestic_rate)*100;
		//$con_ile_cost = ($con_ile/100)*$cons_rate; //before.......

		if ($ile_cost == "") $ile_cost = 0;
		if ($cons_uom == "") $cons_uom = 0;
		if ($con_ile == "") $con_ile = 0;

		$con_ile_cost = ($ile_cost * $exchange_rate) / $conversion_factor; //current calculation changed by fm rasel
		if ($con_ile_cost == "") $con_ile_cost = 0;

		if (number_format($cons_rate, 10, ".", "") == 0) {
			echo "20**Rate Not Found.";
			disconnect($con);
			die;
		}

		if (str_replace("'", "", $txt_mrr_no) != "") {
			$new_recv_number[0] = str_replace("'", "", $txt_mrr_no);
			$id = str_replace("'", "", $hidden_mrr_id);
			//master table UPDATE here START----------------------//		
			$field_array1 = "receive_basis*receive_date*booking_id*booking_no*challan_no*challan_date*store_id*exchange_rate*currency_id*supplier_id*lc_no*pay_mode*source*supplier_referance*receive_purpose*loan_party*boe_mushak_challan_no*boe_mushak_challan_date*remarks*store_sl_no*rcvd_book_no*addi_challan_date*bill_no*bill_date*purchaser_name*carried_by*qc_check_by*receive_by*gate_entry_by*gate_entry_date*addi_rcvd_date*gate_entry_no*updated_by*update_date*ref_no*bill_no_mst*bill_no_mst_date";
			$data_array1 = "" . $cbo_receive_basis . "*" . $txt_receive_date . "*" . $txt_wo_pi_req_id . "*" . $txt_wo_pi_req . "*" . $txt_challan_no . "*" . $txt_challan_date_mst . "*" . $cbo_store_name . "*" . $txt_exchange_rate . "*" . $cbo_currency . "*" . $cbo_supplier . "*" . $hidden_lc_id . "*" . $cbo_pay_mode . "*" . $cbo_source . "*" . $txt_sup_ref . "*" . $cbo_receive_purpose . "*" . $cbo_loan_party . "*" . $txt_boe_mushak_challan_no . "*" . $txt_boe_mushak_challan_date . "*" . $txt_remarks . "*" . $txt_store_sl_no . "*'" . $txt_book_no . "'*'" . $txt_challan_date . "'*'" . $txt_bill_no . "'*'" . $txt_bill_date . "'*'" . $cbo_purchaser_name . "'*'" . $cbo_carried_by . "'*'" . $cbo_qc_check_by . "'*'" . $cbo_receive_by . "'*'" . $cbo_gate_entry_by . "'*'" . $txt_gate_entry_date . "'*'" . $txt_addi_rcvd_date . "'*'" . $txt_gate_entry_no . "'*'" . $user_id . "'*'" . $pc_date_time . "'*" . $txt_ref_no . "*" . $txt_bill_no_mst . "*" . $txt_bill_date_mst . "";
			//master table UPDATE here END---------------------------------------// 
		} else {
			//master table entry here START---------------------------------------//txt_remarks		

			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			$new_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con, 1, str_replace("'", "", $cbo_company_id), 'GIR', 20, date("Y", time())));

			$field_array1 = "id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, company_id, receive_basis, receive_date, booking_id, booking_no, challan_no, challan_date, store_id, exchange_rate, currency_id, supplier_id, lc_no, pay_mode, source, supplier_referance,receive_purpose,loan_party, boe_mushak_challan_no, boe_mushak_challan_date, remarks, variable_setting, store_sl_no, rcvd_book_no,addi_challan_date,bill_no,bill_date,purchaser_name,carried_by,qc_check_by,receive_by,gate_entry_by,gate_entry_date,addi_rcvd_date,gate_entry_no, inserted_by, insert_date,ref_no,bill_no_mst,bill_no_mst_date";
			$data_array1 = "(" . $id . ",'" . $new_recv_number[1] . "','" . $new_recv_number[2] . "','" . $new_recv_number[0] . "',20," . $cbo_company_id . "," . $cbo_receive_basis . "," . $txt_receive_date . "," . $txt_wo_pi_req_id . "," . $txt_wo_pi_req . "," . $txt_challan_no . "," . $txt_challan_date_mst . "," . $cbo_store_name . "," . $txt_exchange_rate . "," . $cbo_currency . "," . $cbo_supplier . "," . $hidden_lc_id . "," . $cbo_pay_mode . "," . $cbo_source . "," . $txt_sup_ref . "," . $cbo_receive_purpose . "," . $cbo_loan_party . "," . $txt_boe_mushak_challan_no . "," . $txt_boe_mushak_challan_date . "," . $txt_remarks . "," . $variable_string_inventory . "," . $txt_store_sl_no . ",'" . $txt_book_no . "','" . $txt_challan_date . "','" . $txt_bill_no . "','" . $txt_bill_date . "','" . $cbo_purchaser_name . "','" . $cbo_carried_by . "','" . $cbo_qc_check_by . "','" . $cbo_receive_by . "','" . $cbo_gate_entry_by . "','" . $txt_gate_entry_date . "','" . $txt_addi_rcvd_date . "','" . $txt_gate_entry_no . "','" . $user_id . "','" . $pc_date_time . "'," . $txt_ref_no . "," . $txt_bill_no_mst . "," . $txt_bill_date_mst . ")";
			//master table entry here END---------------------------------------// 
		}


		$dtlsid = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		$field_array_trams = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,supplier_id,prod_id,item_category,transaction_type,transaction_date,store_id,order_uom,order_qnty,order_rate,order_ile,order_ile_cost,order_amount,cons_uom,cons_quantity,cons_rate,cons_ile,cons_ile_cost,cons_amount,balance_qnty,balance_amount,floor_id,room,rack,self,bin_box,expire_date,remarks,inserted_by,insert_date,batch_lot,store_rate,store_amount";
		$data_array_trans = "(" . $dtlsid . "," . $id . "," . $cbo_receive_basis . "," . $txt_wo_pi_req_id . "," . $cbo_company_id . "," . $cbo_supplier . "," . $current_prod_id . "," . $cbo_item_category . ",1," . $txt_receive_date . "," . $cbo_store_name . "," . $cbo_uom . "," . $txt_receive_qty . "," . number_format($txt_rate, 10, ".", "") . "," . $ile . "," . $ile_cost . "," . number_format($txt_amount, 8, ".", "") . "," . $cons_uom . "," . $con_quantity . "," . number_format($cons_rate, 10, ".", "") . "," . $con_ile . "," . $con_ile_cost . "," . number_format($con_amount, 8, ".", "") . "," . $con_quantity . "," . number_format($con_amount, 8, ".", "") . "," . $cbo_floor . "," . $cbo_room . "," . $txt_rack . "," . $txt_shelf . "," . $cbo_bin . "," . $txt_warranty_date . "," . $txt_referance . ",'" . $user_id . "','" . $pc_date_time . "'," . $txt_lot . "," . number_format($cons_rate, 10, ".", "") . "," . number_format($con_amount, 8, ".", "") . ")";

		//yarn details table entry here END-----------------------------------//

		//product master table data UPDATE START----------------------------------------------------------//	
		$stock_value 	= $domestic_rate * $con_quantity;
		$currentStock   = $presentStock + $con_quantity;
		$available_qnty = $available_qnty + $con_quantity;
		$StockValue = 0;
		$avgRate = $presentAvgRate;
		if ($currentStock != 0) {
			$StockValue	 = $presentStockValue + $stock_value;
			$avgRate	 = $StockValue / $currentStock;
		}

		$field_array3 = "avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*available_qnty*updated_by*update_date";
		$data_array3 = "" . number_format($avgRate, 10, ".", "") . "*" . $con_quantity . "*" . $currentStock . "*" . number_format($StockValue, 8, ".", "") . "*" . $available_qnty . "*'" . $user_id . "'*'" . $pc_date_time . "'";

		//------------------ product_details_master END---------------------------------------------------//


		$store_up_id = 0;
		if ($variable_store_wise_rate == 1) {
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$current_prod_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id");
			if (count($sql_store) < 1) {
				$field_array_store = "id,company_id,store_id,category_id,prod_id,cons_qty,rate,amount,last_purchased_qnty,inserted_by,insert_date,lot,first_receive_date,last_receive_date";

				$sdtlsid = return_next_id("id", "inv_store_wise_gen_qty_dtls", 1);
				$data_array_store = "(" . $sdtlsid . "," . $cbo_company_id . "," . $cbo_store_name . "," . $cbo_item_category . "," . $current_prod_id . "," . $con_quantity . "," . number_format($cons_rate, 10, ".", "") . "," . number_format($con_amount, 8, ".", "") . "," . $con_quantity . ",'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "'," . $txt_lot . "," . $txt_receive_date . "," . $txt_receive_date . ")";
			} else {
				if (count($sql_store) > 1) {
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
					$store_stock_value 	= $cons_rate * $con_quantity;
					$store_currentStock = $store_presentStock + $con_quantity;
					$store_StockValue	= $store_presentStockValue + $store_stock_value;
					$store_avgRate = 0;
					if ($store_StockValue != 0 && $store_currentStock != 0) $store_avgRate = abs($store_StockValue / $store_currentStock);

					$field_array_store = "rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date*last_receive_date";
					$data_array_store = "" . number_format($store_avgRate, 10, ".", "") . "*" . $txt_receive_qty . "*" . $store_currentStock . "*" . number_format($store_StockValue, 8, ".", "") . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'*" . $txt_receive_date . "";
				}
			}
		}




		//serial no save---------------
		//$serialID = return_next_id("id", "inv_serial_no_details", 1);

		$serial_field_array = "id,recv_trans_id,prod_id,serial_no,is_issued,inserted_by,insert_date,serial_qty";
		$expSerial = explode(",", str_replace("'", "", $txt_serial_no));
		$expSerialqty = explode(",", str_replace("'", "", $txt_serial_qty));
		//print_r($current_prod_id);die;
		$serial_data_array == "";
		for ($i = 0; $i < count($expSerial); $i++) {
			$serialID = return_next_id_by_sequence("INV_SERIAL_NO_DETAILS_PK_SEQ", "inv_serial_no_details", $con);
			if ($i > 0) {
				$serial_data_array .= ",";
			}
			$serial_data_array .= "(" . $serialID . "," . $dtlsid . "," . $current_prod_id . ",'" . $expSerial[$i] . "',0,'" . $user_id . "','" . $pc_date_time . "','" . $expSerialqty[$i] . "')";
			//$serialID++;
		}


		$rID = $dtlsrID = $prodUpdate = $serial_dtlsrID = $storeRID = 1;
		if (str_replace("'", "", $txt_mrr_no) != "") {
			$rID = sql_update("inv_receive_master", $field_array1, $data_array1, "id", $id, 1);
		} else {
			$rID = sql_insert("inv_receive_master", $field_array1, $data_array1, 1);
		}
		//echo "10**INSERT INTO inv_transaction (".$field_array_trams.") VALUES ".$data_array_trans; oci_rollback($con);disconnect($con);die;
		$dtlsrID = sql_insert("inv_transaction", $field_array_trams, $data_array_trans, 1);
		$prodUpdate = sql_update("product_details_master", $field_array3, $data_array3, "id", $current_prod_id, 1);

		if (str_replace("'", "", $txt_serial_no) != "") {
			$serial_dtlsrID = sql_insert("inv_serial_no_details", $serial_field_array, $serial_data_array, 1);
		}

		if ($variable_store_wise_rate == 1) {
			if ($store_up_id > 0) {
				$storeRID = sql_update("inv_store_wise_gen_qty_dtls", $field_array_store, $data_array_store, "id", $store_up_id, 1);
			} else {
				$storeRID = sql_insert("inv_store_wise_gen_qty_dtls", $field_array_store, $data_array_store, 1);
			}
		}
		//echo "10**".$new_recv_number[0]."**".$id;die;
		//echo "10**$rID && $dtlsrID && $prodUpdate && $serial_dtlsrID && $storeRID".jahid;oci_rollback($con);disconnect($con);die;

		if ($db_type == 0) {
			if ($rID && $dtlsrID && $prodUpdate && $serial_dtlsrID && $storeRID) {
				mysql_query("COMMIT");
				echo "0**" . $new_recv_number[0] . "**" . $id;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $new_recv_number[0] . "**" . $id;
			}
		}
		if ($db_type == 2 || $db_type == 1) {
			if ($rID && $dtlsrID && $prodUpdate && $serial_dtlsrID && $storeRID) {
				oci_commit($con);
				echo "0**" . $new_recv_number[0] . "**" . $id;
			} else {
				oci_rollback($con);
				echo "10**" . $new_recv_number[0] . "**" . $id;
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
		$hidden_mrr_id = str_replace("'", "", $hidden_mrr_id);
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0**0"; die;}
		//table lock here 
		if (str_replace("'", "", $update_id) == "") {
			echo "15";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			exit();
		}

		$max_transaction_id = return_field_value("max(id) as max_trans_id", "inv_transaction", "prod_id=$current_prod_id and transaction_type in(2,3,6) and status_active = 1", "max_trans_id");
		if ($max_transaction_id > str_replace("'", "", $update_id)) {
			echo "20**Next Transaction Found, Update Not Allow";
			disconnect($con);
			die;
		}

		//---------------Check Receive control on Gate Entry according to variable settings inventory---------------------------//
		$challan_no = str_replace("'", "", $txt_challan_no);
		$supplier_id = str_replace("'", "", $cbo_supplier);
		if ($challan_no != "") {
			if ($variable_set_invent == 1) {
				$variable_set_invent = return_field_value("a.id as id", " inv_gate_in_mst a,  inv_gate_in_dtl b", "a.id=b.mst_id and a.company_id=$cbo_company_id and a.challan_no='$challan_no' and b.item_category_id=$cbo_item_category and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "id");
				if (empty($variable_set_invent)) {
					echo "30** This Item Not Found In Gate Entry. \n Please Gate Entry First.";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}

			if ($variale_challan_dup == 2) {
				$duplicate_chalan = is_duplicate_field("id", "inv_receive_master ", "status_active=1 and is_deleted=0 and supplier_id = $supplier_id and challan_no='$challan_no' and id<>$hidden_mrr_id");
				if ($duplicate_chalan == 1) {
					echo "30**Duplicate Challan No. Not Allow.";
					disconnect($con);
					die;
				}
			}
		}
		//---------------End Check Receive control on Gate Entry---------------------------//


		//---------------Check Meterial Over Receive control Start---------------------------//
		$rcv_basis = str_replace("'", "", $cbo_receive_basis);

		if ($db_type == 0) {
			$concattS = explode(",", return_field_value(" concat(unit_of_measure,',',conversion_factor) as cons_uom", "product_details_master", "id=$current_prod_id", "cons_uom"));
		}
		if ($db_type == 2) {
			$concattS = explode(",", return_field_value("(unit_of_measure || ',' ||conversion_factor) as cons_uom", "product_details_master", "id=$current_prod_id", "cons_uom"));
		}
		$cons_uom = $concattS[0];
		$conversion_factor = $concattS[1];

		if ($variable_over_rcv_payment > 0 && $rcv_basis != 4 && $rcv_basis != 6) {
			//txt_receive_qty current_prod_id
			$wo_pi_req_id = str_replace("'", "", $txt_wo_pi_req_id);
			$cr_prod_id = str_replace("'", "", $current_prod_id);

			$totalRtnQnty = return_field_value("sum(c.cons_quantity) as bal", "inv_issue_master b, inv_transaction c", "c.mst_id=b.id and b.booking_id=" . $wo_pi_req_id . " and c.prod_id=" . $cr_prod_id . " and b.entry_form=26 and c.transaction_type=3 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "bal");
			$totalRtnQnty = $totalRtnQnty / $conversion_factor;

			$prev_rcv_sql = sql_select(" select sum(b.order_qnty) as qnty from inv_receive_master a, inv_transaction b where a.id = b.mst_id and a.entry_form = 20 and b.transaction_type=1 and a.receive_basis = $rcv_basis and a.booking_id = $wo_pi_req_id and b.item_category=$cbo_item_category and b.prod_id=$cr_prod_id and a.status_active=1 and b.status_active=1 and b.id<>$update_id");
			$prev_rcv_qnty = $prev_rcv_sql[0][csf("qnty")] - $totalRtnQnty;
			$current_receive_qty = str_replace("'", "", $txt_receive_qty);
			$tot_qnty = $prev_rcv_qnty + $current_receive_qty;

			if ($rcv_basis == 1) {
				$wo_pi_req_sql = sql_select(" select sum(b.quantity) as quantity from com_pi_item_details b where b.status_active=1 and b.pi_id=$wo_pi_req_id and b.item_prod_id=$cr_prod_id");
				$wo_pi_req_qnty = $wo_pi_req_sql[0][csf("quantity")];
			} else if ($rcv_basis == 2) {
				$wo_pi_req_sql = sql_select(" select sum(b.supplier_order_quantity) as quantity from wo_non_order_info_dtls b where b.status_active=1 and b.mst_id=$wo_pi_req_id and b.item_id=$cr_prod_id");
				$wo_pi_req_qnty = $wo_pi_req_sql[0][csf("quantity")];
			} else {
				$wo_pi_req_sql = sql_select(" select sum(b.quantity) as quantity from inv_purchase_requisition_dtls b where b.status_active=1 and b.mst_id=$wo_pi_req_id and b.product_id=$cr_prod_id");
				$wo_pi_req_qnty = $wo_pi_req_sql[0][csf("quantity")];
			}

			$allow_qnty = ($wo_pi_req_qnty + (($wo_pi_req_qnty / 100) * $variable_over_rcv_percent));
			if ($tot_qnty > $allow_qnty) {
				echo "30** MRR Quantity Not Allow More Then PI/Wo/Req Allowed Quantity.";
				disconnect($con);
				die;
			}
		}

		//---------------Check Meterial Over Receive control End---------------------------//


		$issue_check = return_field_value("id", "inv_mrr_wise_issue_details", "recv_trans_id=$update_id and status_active=1", "id");
		if ($issue_check > 0) {
			echo "20**This Product Already Issue. Update Not Allow.";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		} else {
			//check update id
			//---------------Check Receive date with Last Transaction date-------------//
			$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$current_prod_id and store_id=$cbo_store_name and id <> $update_id and status_active = 1", "max_date");
			if ($max_transaction_date != "") {
				$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
				$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
				if ($receive_date < $max_transaction_date) {
					echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Lot";
					//check_table_status($_SESSION['menu_id'], 0);
					disconnect($con);
					die;
				}
			}

			//---------------Check Last Transaction date End -------------//

			//previous product stock adjust here--------------------------//
			//product master table UPDATE here START ---------------------//
			$sql = sql_select("select a.prod_id,a.cons_quantity,a.cons_rate,a.cons_amount, a.store_amount,b.avg_rate_per_unit,b.current_stock,b.stock_value from inv_transaction a, product_details_master b  where a.status_active=1 and a.id=$update_id and a.prod_id=b.id");

			$before_prod_id = $before_receive_qnty = $before_rate = $beforeAmount = $before_brand = "";
			$beforeStock = $beforeStockValue = $beforeAvgRate = 0;
			foreach ($sql as $row) {
				$before_prod_id 		= $row[csf("prod_id")];
				$before_receive_qnty 	= $row[csf("cons_quantity")]; //stock qnty
				$before_rate 			= $row[csf("cons_rate")];
				$beforeAmount			= $row[csf("cons_amount")]; //stock value
				$beforeStoreAmount		= $row[csf("store_amount")];
				$beforeStock			= $row[csf("current_stock")];
				$beforeStockValue		= $row[csf("stock_value")];
				$beforeAvgRate			= $row[csf("avg_rate_per_unit")];
			}




			//stock value minus here---------------------------//
			$adj_beforeStock			= $beforeStock - $before_receive_qnty;
			$adj_beforeStockValue		= $beforeStockValue - $beforeAmount;
			if ($adj_beforeStock != 0)
				$adj_beforeAvgRate	= number_format(($adj_beforeStockValue / $adj_beforeStock), 10, '.', '');
			else $adj_beforeAvgRate = 0;

			//current product stock-------------------------//
			$current_prod_id = str_replace("'", "", $current_prod_id);
			$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value,available_qnty from product_details_master where id=$current_prod_id");
			$presentStock = $presentStockValue = $presentAvgRate = 0;
			$product_name_details = "";
			foreach ($sql as $result) {
				$presentStock		  = $result[csf("current_stock")];
				$presentStockValue	 = $result[csf("stock_value")];
				$presentAvgRate		= $result[csf("avg_rate_per_unit")];
				$product_name_details  = $result[csf("product_name_details")];
				$available_qnty		= $result[csf("available_qnty")];
			}
			//----------------Check Product ID END---------------------//

			//yarn master table UPDATE here START----------------------//booking_id$txt_wo_pi_req_id
			if ($update_id != "") {
				$txt_challan_date = $txt_bill_date = $txt_gate_entry_date = $txt_addi_rcvd_date = "";
				$addi_info_arr = explode("_", str_replace("'", "", $txt_addi_info));

				$txt_book_no = $addi_info_arr[0];
				$txt_challan_date = $addi_info_arr[1];
				$txt_bill_no = $addi_info_arr[2];
				$txt_bill_date = $addi_info_arr[3];
				$cbo_purchaser_name = $addi_info_arr[4];
				$cbo_carried_by = $addi_info_arr[5];
				$cbo_qc_check_by = $addi_info_arr[6];
				$cbo_receive_by = $addi_info_arr[7];
				$cbo_gate_entry_by = $addi_info_arr[8];
				$txt_gate_entry_date = $addi_info_arr[9];
				$txt_addi_rcvd_date = $addi_info_arr[10];
				$txt_gate_entry_no = $addi_info_arr[11];
				$txt_store_sl_no = "'" . $addi_info_arr[12] . "'";
				if ($db_type == 0) {
					$txt_challan_date = change_date_format($txt_challan_date, 'yyyy-mm-dd');
					$txt_bill_date = change_date_format($txt_bill_date, 'yyyy-mm-dd');
					$txt_gate_entry_date = change_date_format($txt_gate_entry_date, 'yyyy-mm-dd');
					$txt_addi_rcvd_date = change_date_format($txt_addi_rcvd_date, 'yyyy-mm-dd');
				} else {
					$txt_challan_date = change_date_format($txt_challan_date, '', '', 1);
					$txt_bill_date = change_date_format($txt_bill_date, '', '', 1);
					$txt_gate_entry_date = change_date_format($txt_gate_entry_date, '', '', 1);
					$txt_addi_rcvd_date = change_date_format($txt_addi_rcvd_date, '', '', 1);
				}

				$rate = str_replace("'", "", $txt_rate);
				$txt_ile = str_replace("'", "", $txt_ile);
				$txt_receive_qty = str_replace("'", "", $txt_receive_qty);
				$ile = ($txt_ile / $rate) * 100; // ile cost to ile

				$ile_cost = str_replace("'", "", $txt_ile); //ile cost = (ile/100)*rate
				$exchange_rate = str_replace("'", "", $txt_exchange_rate);


				$domestic_rate = return_domestic_rate($rate, $ile_cost, $exchange_rate, $conversion_factor);
				$cons_rate = number_format($domestic_rate, 10, ".", ""); //number_format($rate*$exchange_rate,$dec_place[3],".","");

				$con_quantity = $conversion_factor * $txt_receive_qty;
				$con_amount = $cons_rate * $con_quantity;
				$con_ile = $ile / $conversion_factor;
				//$con_ile_cost = ($con_ile/100)*($cons_rate);//before .........
				$con_ile_cost = ($ile_cost * $exchange_rate) / $conversion_factor; //current calculation changed by fm rasel
				if ($con_ile_cost == "") $con_ile_cost = 0;
				//echo "20**".$con_ile.'/100='.$cons_rate; oci_rollback($con); die; 

				if ($ile_cost == "") $ile_cost = 0;
				if ($con_ile == "") $con_ile = 0;
				if ($cons_uom == "") $cons_uom = 0;
				if (number_format($cons_rate, 10, ".", "") == 0) {
					echo "20**Rate Not Found.";
					disconnect($con);
					die;
				}

				$field_array_receive = "receive_basis*receive_date*booking_id*booking_no*challan_no*challan_date*receive_purpose*loan_party*exchange_rate*currency_id*supplier_id*lc_no*pay_mode*source*supplier_referance*boe_mushak_challan_no*boe_mushak_challan_date*remarks*store_sl_no*rcvd_book_no*addi_challan_date*bill_no*bill_date*purchaser_name*carried_by*qc_check_by*receive_by*gate_entry_by*gate_entry_date*addi_rcvd_date*gate_entry_no*updated_by*update_date*bill_no_mst*bill_no_mst_date";
				$data_array_receive = "" . $cbo_receive_basis . "*" . $txt_receive_date . "*" . $txt_wo_pi_req_id . "*" . $txt_wo_pi_req . "*" . $txt_challan_no . "*" . $txt_challan_date_mst . "*" . $cbo_receive_purpose . "*" . $cbo_loan_party . "*" . $txt_exchange_rate . "*" . $cbo_currency . "*" . $cbo_supplier . "*" . $hidden_lc_id . "*" . $cbo_pay_mode . "*" . $cbo_source . "*" . $txt_sup_ref . "*" . $txt_boe_mushak_challan_no . "*" . $txt_boe_mushak_challan_date . "*" . $txt_remarks . "*" . $txt_store_sl_no . "*'" . $txt_book_no . "'*'" . $txt_challan_date . "'*'" . $txt_bill_no . "'*'" . $txt_bill_date . "'*'" . $cbo_purchaser_name . "'*'" . $cbo_carried_by . "'*'" . $cbo_qc_check_by . "'*'" . $cbo_receive_by . "'*'" . $cbo_gate_entry_by . "'*'" . $txt_gate_entry_date . "'*'" . $txt_addi_rcvd_date . "'*'" . $txt_gate_entry_no . "'*'" . $user_id . "'*'" . $pc_date_time . "'*" . $txt_bill_no_mst . "*" . $txt_bill_date_mst . "";

				//yarn master table UPDATE here END---------------------------------------// 

				// yarn details table UPDATE here START-----------------------------------//		


				$field_array_trans = "receive_basis*pi_wo_batch_no*company_id*supplier_id*prod_id*item_category*transaction_type*transaction_date*order_uom*order_qnty*order_rate*order_ile*order_ile_cost*order_amount*cons_uom*cons_quantity*cons_rate*cons_ile*cons_ile_cost*cons_amount*balance_qnty*balance_amount*floor_id*room*rack*self*bin_box*expire_date*remarks*updated_by*update_date*batch_lot*store_rate*store_amount";
				$data_array_trans = "" . $cbo_receive_basis . "*" . $txt_wo_pi_req_id . "*" . $cbo_company_id . "*" . $cbo_supplier . "*" . $current_prod_id . "*" . $cbo_item_category . "*1*" . $txt_receive_date . "*" . $cbo_uom . "*" . $txt_receive_qty . "*" . number_format($txt_rate, 10, ".", "") . "*" . $ile . "*" . $ile_cost . "*" . number_format($txt_amount, 8, ".", "") . "*" . $cons_uom . "*" . $con_quantity . "*" . number_format($cons_rate, 10, ".", "") . "*" . $con_ile . "*" . $con_ile_cost . "*" . number_format($con_amount, 8, ".", "") . "*" . $con_quantity . "*" . number_format($con_amount, 8, ".", "") . "*" . $cbo_floor . "*" . $cbo_room . "*" . $txt_rack . "*" . $txt_shelf . "*" . $cbo_bin . "*" . $txt_warranty_date . "*" . $txt_referance . "*'" . $user_id . "'*'" . $pc_date_time . "'*" . $txt_lot . "*" . number_format($cons_rate, 10, ".", "") . "*" . number_format($con_amount, 8, ".", "") . "";
				//echo "**".$field_array."<br>".$data_array;die;
				//$dtlsrID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
			}

			$stock_without_current_trans = return_field_value("sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as balance_stock", "inv_transaction", "status_active=1 and prod_id=$before_prod_id and store_id=$cbo_store_name and id <> $update_id", "balance_stock");
			//$current_store_stock=(($stock_without_current_trans+$con_quantity)-$before_receive_qnty);
			$current_store_stock = ($stock_without_current_trans + $con_quantity);
			if ($current_store_stock < 0) {
				echo "30**Store Wise Stock Less Then Zero Not Allow";
				disconnect($con);
				die;
			}

			//yarn details table UPDATE here END-----------------------------------//
			//product master table data UPDATE START----------------------------------------------------------// 
			$field_array_product = "avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*available_qnty*updated_by*update_date";
			if ($before_prod_id == $current_prod_id) {
				$currentStock	= $adj_beforeStock + $con_quantity;
				$available_qnty  = $available_qnty + $con_quantity;

				if ($currentStock < 0) //Aziz
				{
					echo "30**Stock cannot be less than zero.";
					disconnect($con);
					die;
				}

				$avgRate = $StockValue = 0;
				if ($currentStock != 0) {
					$StockValue	  = $adj_beforeStockValue + $con_amount;
					$avgRate      = number_format($StockValue / $currentStock, 10, '.', '');
				}

				$data_array_product = "" . number_format($avgRate, 10, '.', '') . "*" . $con_quantity . "*" . $currentStock . "*" . number_format($StockValue, 8, '.', '') . "*" . $available_qnty . "*'" . $user_id . "'*'" . $pc_date_time . "'";
				//$prodUpdate = sql_update("product_details_master",$field_array_product,$data_array_product,"id",$current_prod_id,1);
			} else {
				//before
				$updateID_array = $update_data = array();

				if ($adj_beforeStock < 0) //Aziz
				{
					echo "30**Stock cannot be less than zero.";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}


				if ($adj_beforeStock != 0) {
					$adj_beforeStockValue  = $adj_beforeStockValue;
					//$adj_beforeAvgRate = number_format($adj_beforeAvgRate,$dec_place[3],'.','');
				} else {
					$adj_beforeStockValue = 0;
					//$adj_beforeAvgRate = 0;
				}

				$updateID_array[] = $before_prod_id;
				$update_data[$before_prod_id] = explode("*", ("" . number_format($adj_beforeAvgRate, 10, '.', '') . "*0*" . $adj_beforeStock . "*" . number_format($adj_beforeStockValue, 8, '.', '') . "*" . $available_qnty . "*'" . $user_id . "'*'" . $pc_date_time . "'"));
				//current			 
				$presentStock 		= $presentStock + $con_quantity;
				$available_qnty  	= $available_qnty + $con_quantity;

				if ($presentStock != 0) {
					$presentStockValue  = $presentStockValue + $con_amount;
					$presentAvgRate		= number_format($presentStockValue / $presentStock, 10, '.', '');
				} else {
					$presentStockValue = 0;
					//$presentAvgRate = 0;
				}

				$updateID_array[] = $current_prod_id;
				if ($presentAvgRate < 0 || $presentAvgRate == "" || $presentAvgRate == "nan") $presentAvgRate = 0;
				$update_data[$current_prod_id] = explode("*", ("" . number_format($presentAvgRate, 10, '.', '') . "*0*" . $presentStock . "*" . number_format($presentStockValue, 8, '.', '') . "*" . $available_qnty . "*'" . $user_id . "'*'" . $pc_date_time . "'"));
				//$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_product,$update_data,$updateID_array),1);
			}

			//------------------ product_details_master END---------------------------------------------------//
			$store_up_id = 0;
			if ($variable_store_wise_rate == 1) {
				$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$current_prod_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id");

				if (count($sql_store) < 1) {
					echo "20**No Data Found.";
					disconnect($con);
					die;
				} elseif (count($sql_store) > 1) {
					echo "20**Duplicate Product is Not Allow in Same REF Number.";
					disconnect($con);
					die;
				} else {
					$store_presentStock = $store_presentStockValue = $store_presentAvgRate = $store_before_receive_qnty = 0;
					foreach ($sql_store as $result) {
						$store_up_id = $result[csf("id")];
						$store_presentStock	= $result[csf("current_stock")];
						$store_presentStockValue = $result[csf("stock_value")];
						$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
					}
					$adj_beforeStock_store			= $store_presentStock - $before_receive_qnty;
					$adj_beforeStockValue_store		= $store_presentStockValue - $beforeStoreAmount;

					$field_array_store = "rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date*last_receive_date";

					$currentStock_store		= $adj_beforeStock_store + $con_quantity;
					$currentValue_store		= $adj_beforeStockValue_store + $con_amount;
					$store_avgRate = 0;
					if ($currentValue_store != 0 && $currentStock_store != 0) $store_avgRate = abs($currentValue_store / $currentStock_store);
					//echo "20**$store_avgRate.";disconnect($con);die;
					$data_array_store = "" . number_format($store_avgRate, 10, '.', '') . "*" . $con_quantity . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'*" . $txt_receive_date . "";
				}
			}




			//serial no save---------------
			$deleteSerial = execute_query("delete from inv_serial_no_details where recv_trans_id=" . $update_id, 0);
			//$serialID = return_next_id("id", "inv_serial_no_details", 1);	

			$serial_field_array = "id,recv_trans_id,prod_id,serial_no,inserted_by,insert_date,serial_qty";
			$expSerial = explode(",", str_replace("'", "", $txt_serial_no));
			$expSerialqty = explode(",", str_replace("'", "", $txt_serial_qty));
			$serial_data_array == "";
			for ($i = 0; $i < count($expSerial); $i++) {
				$serialID = return_next_id_by_sequence("INV_SERIAL_NO_DETAILS_PK_SEQ", "inv_serial_no_details", $con);
				if ($i > 0) {
					$serial_data_array .= ",";
				}
				$serial_data_array .= "(" . $serialID . "," . $update_id . "," . $current_prod_id . ",'" . $expSerial[$i] . "','" . $user_id . "','" . $pc_date_time . "','" . $expSerialqty[$i] . "')";
				//$serialID++;
			}

			//all query execute here
			$rID = $dtlsrID = $prodUpdate = $serial_dtlsrID = $storeRID = true;
			if ($update_id != "") {
				$rID = sql_update("inv_receive_master", $field_array_receive, $data_array_receive, "id", $hidden_mrr_id, 1);
				$dtlsrID = sql_update("inv_transaction", $field_array_trans, $data_array_trans, "id", $update_id, 1);
			}
			if ($before_prod_id == $current_prod_id) {
				$prodUpdate = sql_update("product_details_master", $field_array_product, $data_array_product, "id", $current_prod_id, 1);
			} else {
				//execute_query execute_query
				$prodUpdate = execute_query(bulk_update_sql_statement("product_details_master", "id", $field_array_product, $update_data, $updateID_array));
			}

			if (str_replace("'", "", $txt_serial_no) != "") {
				$serial_dtlsrID = sql_insert("inv_serial_no_details", $serial_field_array, $serial_data_array, 1);
			}

			if ($store_up_id > 0 && $variable_store_wise_rate == 1) {
				$storeRID = sql_update("inv_store_wise_gen_qty_dtls", $field_array_store, $data_array_store, "id", $store_up_id, 1);
			}

			//echo "10**$rID && $dtlsrID && $prodUpdate && $deleteSerial && $serial_dtlsrID && $storeRID";oci_rollback($con);disconnect($con);die;

			if ($db_type == 0) {
				if ($rID && $dtlsrID && $prodUpdate && $deleteSerial && $serial_dtlsrID && $storeRID) {
					mysql_query("COMMIT");
					echo "1**" . str_replace("'", "", $txt_mrr_no) . "**" . str_replace("'", "", $hidden_mrr_id);
				} else {
					mysql_query("ROLLBACK");
					echo "10**" . str_replace("'", "", $txt_mrr_no) . "**" . str_replace("'", "", $hidden_mrr_id);
				}
			}
			if ($db_type == 2 || $db_type == 1) {
				if ($rID && $dtlsrID && $prodUpdate && $deleteSerial && $serial_dtlsrID && $storeRID) {
					oci_commit($con);
					echo "1**" . str_replace("'", "", $txt_mrr_no) . "**" . str_replace("'", "", $hidden_mrr_id);
				} else {
					oci_rollback($con);
					echo "10**" . str_replace("'", "", $txt_mrr_no) . "**" . str_replace("'", "", $hidden_mrr_id);
				}
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	} else if ($operation == 2) // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		// master table delete here---------------------------------------
		$mst_id = str_replace("'", "", $hidden_mrr_id);

		if ($mst_id == "" || $mst_id == 0) {
			echo "16**Delete not allowed. Problem occurred";
			disconnect($con);
			die;
		} else {
			$update_id = str_replace("'", "", $update_id);
			$product_id = str_replace("'", "", $current_prod_id);
			if (str_replace("'", "", $update_id) == "") {
				echo "16**Delete not allowed. Problem occurred";
				disconnect($con);
				die;
			}

			//echo "10**select id from inv_transaction where transaction_type in(2,3,6) and prod_id=$product_id and status_active=1 and is_deleted=0 and id >$update_id"; die;
			$chk_next_transaction = return_field_value("id", "inv_transaction", "transaction_type in(2,3,6) and prod_id=$product_id and status_active=1 and is_deleted=0 and id >$update_id ", "id");
			if ($chk_next_transaction != "") {
				echo "18**Delete not allowed.This item is used in another transaction";
				disconnect($con);
				die;
			} else {
				$sql = sql_select("select a.prod_id, a.cons_quantity, a.cons_rate, a.cons_amount, a.store_amount, b.avg_rate_per_unit, b.current_stock, b.stock_value from inv_transaction a, product_details_master b where a.status_active=1 and a.id=$update_id and a.prod_id=b.id");

				$before_prod_id = $before_receive_qnty = $before_rate = $beforeAmount = $before_brand = "";
				$beforeStock = $beforeStockValue = $beforeAvgRate = 0;
				foreach ($sql as $row) {
					$before_prod_id 		= $row[csf("prod_id")];
					$before_receive_qnty 	= $row[csf("cons_quantity")]; //stock qnty
					$before_rate 			= $row[csf("cons_rate")];
					$beforeAmount			= $row[csf("cons_amount")]; //stock value
					$beforeStoreAmount		= $row[csf("store_amount")];
					$beforeStock			= $row[csf("current_stock")];
					$beforeStockValue		= $row[csf("stock_value")];
					$beforeAvgRate			= $row[csf("avg_rate_per_unit")];
				}
				//stock value minus here---------------------------//
				$adj_beforeStock = $beforeStock - $before_receive_qnty;
				$adj_beforeAvgRate = $adj_beforeStockValue = 0;
				if ($adj_beforeStock != 0) {
					$adj_beforeStockValue  = $beforeStockValue - $beforeAmount;
					$adj_beforeAvgRate = number_format(($adj_beforeStockValue / $adj_beforeStock), 10, '.', '');
				}

				$field_array_product = "avg_rate_per_unit*current_stock*stock_value*updated_by*update_date";
				$data_array_product = "" . number_format($adj_beforeAvgRate, 10, '.', '') . "*" . $adj_beforeStock . "*" . number_format($adj_beforeStockValue, 8, '.', '') . "*'" . $user_id . "'*'" . $pc_date_time . "'";

				if ($variable_store_wise_rate == 1) {
					$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$before_prod_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id");
					$store_up_id = 0;
					if (count($sql_store) < 1) {
						echo "20**No Data Found.";
						disconnect($con);
						die;
					} elseif (count($sql_store) > 1) {
						echo "20**Duplicate Product is Not Allow in Same REF Number.";
						disconnect($con);
						die;
					} else {
						$store_presentStock = $store_presentStockValue = $store_presentAvgRate = $store_before_receive_qnty = 0;
						foreach ($sql_store as $result) {
							$store_up_id = $result[csf("id")];
							$store_presentStock	= $result[csf("current_stock")];
							$store_presentStockValue = $result[csf("stock_value")];
							$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
						}
						$currentStock_store		= $store_presentStock - $before_receive_qnty;
						$currentValue_store		= $store_presentStockValue - $beforeStoreAmount;

						$store_avgRate = 0;
						if ($currentValue_store != 0 && $currentStock_store != 0) $store_avgRate = abs($currentValue_store / $currentStock_store);

						$field_array_store = "rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date*last_receive_date";
						$data_array_store = "" . number_format($store_avgRate, 10, '.', '') . "*" . $before_receive_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'*" . $txt_receive_date . "";
					}
				}


				$sql_mst = sql_select("select id from inv_transaction where status_active=1 and is_deleted=0 and transaction_type=1 and mst_id=$mst_id");

				if (count($sql_mst) == 1) {
					$field_array_mst = "updated_by*update_date*status_active*is_deleted";
					$data_array_mst = "" . $user_id . "*'" . $pc_date_time . "'*0*1";

					$rID = sql_update("inv_receive_master", $field_array_mst, $data_array_mst, "id", $mst_id, 1);
					$resetLoad = 1;
				} else {
					$rID = 1;
					$resetLoad = 2;
				}

				$field_array_trans = "updated_by*update_date*status_active*is_deleted";
				$data_array_trans = "" . $user_id . "*'" . $pc_date_time . "'*0*1";

				$rID2 = sql_update("inv_transaction", $field_array_trans, $data_array_trans, "id", $update_id, 1);
				$rID3 = sql_update("product_details_master", $field_array_product, $data_array_product, "id", $product_id, 1);
				$storeRID = true;
				if ($store_up_id > 0 && $variable_store_wise_rate == 1) {
					$storeRID = sql_update("inv_store_wise_gen_qty_dtls", $field_array_store, $data_array_store, "id", $store_up_id, 1);
				}
			}
			/*$rID = sql_update("inv_receive_master",'status_active*is_deleted','0*1',"id*item_category","$mst_id*$cbo_item_category",0);
			$dtlsrID = sql_update("inv_transaction",'status_active*is_deleted','0*1',"mst_id*item_category","$mst_id*$cbo_item_category",0);
			$srID = sql_update("inv_serial_no_details",'status_active*is_deleted','0*1',"mst_id*entry_form","$mst_id*20",1);*/
		}
		//echo "10**".$rID."**".$rID2."**".$rID3; die;
		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3 && $storeRID) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $txt_mrr_no) . "**" . str_replace("'", "", $hidden_mrr_id) . "**" . $resetLoad;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_mrr_no) . "**" . str_replace("'", "", $hidden_mrr_id) . "**" . $resetLoad;
			}
		}
		if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3 && $storeRID) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $txt_mrr_no) . "**" . str_replace("'", "", $hidden_mrr_id) . "**" . $resetLoad;
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_mrr_no) . "**" . str_replace("'", "", $hidden_mrr_id) . "**" . $resetLoad;
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "mrr_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

	<script>
		function js_set_value(mrr) {
			$("#hidden_recv_number").val(mrr); // mrr number
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="1">
					<thead>
						<tr>
							<th width="150">Supplier</th>
							<th width="150">Search By</th>
							<th width="250" align="center" id="search_by_td_up">Enter MRR Number</th>
							<th width="200">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("cbo_supplier", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and  b.party_type in(1,5,6,7,8,30,36,37,39,92) $supplier_credential_cond  and a.status_active=1 and a.is_deleted=0  group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
								?>
							</td>
							<td align="center">
								<?
								$search_by = array(1 => 'MRR No', 2 => 'Challan No', 3 => 'WO', 4 => 'PI');
								$dd = "change_search_event(this.value, '0*0*0*0', '0*0*0)', '../../../') ";
								echo create_drop_down("cbo_search_by", 120, $search_by, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td width="" align="center" id="search_by_td">
								<input type="text" style="width:230px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_mrr_search_list_view', 'search_div', 'general_item_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1);  ?>
								<!- Hidden field here-->
									<input type="hidden" id="hidden_recv_number" value="hidden_recv_number" />

							</td>
						</tr>
					</tbody>
					</tr>
				</table>
				<br>
				<div align="center" valign="top" id="search_div"> </div>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "create_mrr_search_list_view") {
	$ex_data = explode("_", $data);
	// echo "<pre>";
	// print_r($ex_data);
	// echo "</pre>";
	$supplier = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = trim($ex_data[2]);
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];
	$company = $ex_data[5];
	$year = $ex_data[6];

	if ($txt_search_common == "" && ($fromDate == "" && $toDate == "")) {
		echo "<p style='color:red; font-size:16px;'>Please select date range or MRR Number</p>";
		die;
	}

	$sql_cond = "";
	$wo_pi_sql = "";
	$basis_cond = '';
	if ($txt_search_common != "") {
		if (trim($txt_search_by) == 1) // for mrr
		{
			$sql_cond .= " and a.recv_number_prefix_num LIKE '$txt_search_common'";
		} else if (trim($txt_search_by) == 2) // for chllan no
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";
		} else if (trim($txt_search_by) == 3) {
			$year_cond = "";
			if ($db_type == 0) {
				if ($year != "") {

					$year_cond = " and YEAR(wo_date) =$year ";
				}
			} else {
				if ($year != "") {
					$year_cond = " and to_char(wo_date,'YYYY') =$year ";
				}
			}
			$wo_pi_sql = "select id from wo_non_order_info_mst where wo_number LIKE '%$txt_search_common%' $year_cond";
			$basis_cond = " and a.receive_basis=2";
		} else {
			$year_cond = "";
			if ($db_type == 0) {
				if ($year != "") {
					$year_cond = " and YEAR(pi_date) =$year ";
				}
			} else {
				if ($year != "") {
					$year_cond = " and to_char(pi_date,'YYYY') =$year ";
				}
			}
			$wo_pi_sql = "select id from com_pi_master_details where pi_number='$txt_search_common' $year_cond";
			$basis_cond = " and a.receive_basis=1";
		}
	}
	//echo $wo_pi_sql;
	$booking_cond = '';
	if (!empty($wo_pi_sql)) {

		$cond_res = sql_select($wo_pi_sql);
		$booking_ids = array();
		foreach ($cond_res as $row) {
			array_push($booking_ids, $row[csf('id')]);
		}
		array_unique($booking_ids);

		$booking_cond = " and a.booking_id in(" . implode(',', $booking_ids) . ")";
	}
	//echo "<br>".$booking_cond;die;
	$year_cond = '';
	if ($db_type == 0) {
		if ($fromDate != "" || $toDate != "") $sql_cond .= " and a.receive_date  between '" . change_date_format($fromDate, 'yyyy-mm-dd') . "' and '" . change_date_format($toDate, 'yyyy-mm-dd') . "'";



		if ($year != "") {
			if (trim($txt_search_by) == 1) {

				$year_cond = " and YEAR(a.receive_date) =$year ";
			} else if (trim($txt_search_by) == 2) {
				$year_cond = " and YEAR(a.receive_date) =$year ";
			}
		}
	} else {
		if ($fromDate != "" || $toDate != "") $sql_cond .= " and a.receive_date  between '" . change_date_format($fromDate, 'yyyy-mm-dd', '', -1) . "' and '" . change_date_format($toDate, 'yyyy-mm-dd', '', -1) . "'";

		if ($year != "") {
			if (trim($txt_search_by) == 1) {

				$year_cond = " and to_char(a.receive_date,'YYYY') =$year ";
			} else if (trim($txt_search_by) == 2) {
				$year_cond = " and to_char(a.receive_date,'YYYY') =$year ";
			}
		}
	}

	if (trim($company) != "") $sql_cond .= " and a.company_id='$company'";
	if (trim($supplier) != 0) $sql_cond .= " and a.supplier_id='$supplier'";

	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
	$cre_company_id = $userCredential[0][csf('company_id')];
	$cre_supplier_id = $userCredential[0][csf('supplier_id')];
	$cre_store_location_id = $userCredential[0][csf('store_location_id')];
	$cre_item_cate_id = $userCredential[0][csf('item_cate_id')];

	$credientian_cond = "";
	if ($cre_company_id != "") $credientian_cond = " and a.company_id in($cre_company_id)";
	if ($cre_supplier_id != "") $credientian_cond .= " and a.supplier_id in($cre_supplier_id)";
	if ($cre_store_location_id != "") $credientian_cond .= " and b.store_id in($cre_store_location_id)";
	if ($cre_item_cate_id != "") $credientian_cond .= " and b.item_category in($cre_item_cate_id)";

	//echo $credientian_cond;die;

	$sql = "SELECT a.id as rcv_id, a.recv_number,a.recv_number_prefix_num, a.supplier_id, a.challan_no, c.lc_number, a.receive_date, a.receive_basis, a.booking_id, sum(b.order_qnty) as receive_qnty, b.mst_id
	from inv_transaction b, inv_receive_master a left join com_btb_lc_master_details c on a.lc_no=c.id where a.id=b.mst_id and a.entry_form=20 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.transaction_type=1 $sql_cond  $credientian_cond $booking_cond $basis_cond $year_cond
	group by a.id, a.recv_number, a.recv_number_prefix_num, a.supplier_id, a.challan_no, c.lc_number, a.receive_date, a.receive_basis, a.booking_id, b.mst_id 
	order by b.mst_id desc";
	//echo $sql;

	$sql_res = sql_select($sql);

	$booking_ids_arr = array();
	$pi_ids_arr = array();
	foreach ($sql_res as $row) {
		if ($row[csf('booking_id')] > 0) {
			if ($row[csf('receive_basis')] == 1) $pi_ids_arr[$row[csf('booking_id')]] = $row[csf('booking_id')];
			else if ($row[csf('receive_basis')] == 2) $booking_ids_arr[$row[csf('booking_id')]] = $row[csf('booking_id')];
		}
	}

	$con = connect();
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=53 and ref_from in(2,3)");
	oci_commit($con);

	$pi_number_arr = array();
	if (count($pi_ids_arr) > 0) {
		fnc_tempengine("gbl_temp_engine", $user_id, 53, 2, $pi_ids_arr, $empty_arr);
		$sql_pi_res = sql_select("SELECT a.ID, a.PI_NUMBER from gbl_temp_engine g, com_pi_master_details a where g.ref_val=a.id and g.user_id=$user_id and g.entry_form=53 and g.ref_from=2 and a.status_active=1");
		foreach ($sql_pi_res as $row) {
			$pi_number_arr[$row["ID"]] = $row["PI_NUMBER"];
		}
		unset($sql_pi_res);
	}

	$wo_number_arr = array();
	if (count($booking_ids_arr) > 0) {
		fnc_tempengine("gbl_temp_engine", $user_id, 53, 3, $booking_ids_arr, $empty_arr);
		$sql_wo_res = sql_select("SELECT a.ID, a.WO_NUMBER from gbl_temp_engine g, wo_non_order_info_mst a where g.ref_val=a.id and g.user_id=$user_id and g.entry_form=53 and g.ref_from=3 and a.status_active=1");
		foreach ($sql_wo_res as $row) {
			$wo_number_arr[$row["ID"]] = $row["WO_NUMBER"];
		}
		unset($sql_wo_res);
	}

	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=53 and ref_from in(2,3)");
	oci_commit($con);
	disconnect($con);

	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
?>
	<style>
		.wrd_brk {
			word-break: break-all;
		}
	</style>
	<div>
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1030">
			<thead>
				<th width="30">SL</th>
				<th width="120">MRR No</th>
				<th width="120">Supplier Name</th>
				<th width="120">Challan No</th>
				<th width="120">LC No</th>
				<th width="80">Receive Date</th>
				<th width="100">Receive Basis</th>
				<th width="100">WO Number</th>
				<th width="100">PI Number</th>
				<th>Receive Qnty</th>
			</thead>
		</table>
		<div style="width:1050px; max-height:250px;overflow-y:scroll;">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table" id="list_view">
				<tbody>
					<?
					$i = 1;
					foreach ($sql_res as $row) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf("rcv_id")] ."_". $row['SUPPLIER_ID'];   ?>")' style="cursor:pointer">
							<td width="30"><? echo $i; ?></td>
							<td width="120" class="wrd_brk"><? echo $row[csf('recv_number')]; ?></td>
							<td width="120" class="wrd_brk"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></td>
							<td width="120" class="wrd_brk"><? echo $row[csf('challan_no')]; ?></td>
							<td width="120" class="wrd_brk"><? echo $row[csf('lc_number')]; ?></td>
							<td width="80"><? echo change_date_format($row[csf('receive_date')]); ?></td>
							<td width="100" class="wrd_brk"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
							<td width="100" class="wrd_brk"><? echo $wo_number_arr[$row[csf('booking_id')]]; ?></td>
							<td width="100" class="wrd_brk"><? echo $pi_number_arr[$row[csf('booking_id')]]; ?></td>
							<td align="right"><? echo number_format($row[csf('receive_qnty')], 2); ?>&nbsp;</td>
						</tr>
					<?
						$i++;
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
<?
	exit();
}

if ($action == "populate_data_from_data") {
	$sql = "SELECT id, recv_number, company_id, receive_basis, receive_purpose, loan_party, receive_date, booking_id, challan_no, challan_date, store_id, lc_no, supplier_id, exchange_rate, currency_id, lc_no, pay_mode, source, boe_mushak_challan_no, boe_mushak_challan_date, remarks, supplier_referance, is_posted_account, variable_setting, store_sl_no, rcvd_book_no,addi_challan_date,bill_no,bill_date,purchaser_name,carried_by,qc_check_by,receive_by,gate_entry_by,gate_entry_date,addi_rcvd_date,gate_entry_no,is_audited, bill_no_mst, bill_no_mst_date from inv_receive_master where id='$data' and entry_form=20";
	$res = sql_select($sql);
	foreach ($res as $row) {
		echo "$('#hidden_mrr_id').val(" . $row[csf("id")] . ");\n";
		echo "$('#txt_mrr_no').val('" . $row[csf("recv_number")] . "');\n";
		echo "$('#cbo_company_id').val(" . $row[csf("company_id")] . ");\n";
		 echo "load_drop_down( 'requires/general_item_receive_controller', " . $row[csf("company_id")] . ", 'load_drop_down_supplier_new', 'supplier' );\n";
		echo "$('#cbo_receive_basis').val(" . $row[csf("receive_basis")] . ");\n";
		echo "$('#cbo_receive_purpose').val(" . $row[csf("receive_purpose")] . ");\n";
		echo "$('#cbo_loan_party').val(" . $row[csf("loan_party")] . ");\n";
		echo "$('#txt_receive_date').val('" . change_date_format($row[csf("receive_date")]) . "');\n";
		echo "$('#txt_challan_no').val('" . $row[csf("challan_no")] . "');\n";
		echo "$('#txt_challan_date_mst').val('" . change_date_format($row[csf("challan_date")]) . "');\n";
		echo "$('#txt_bill_no_mst').val('" . $row[csf("bill_no_mst")] . "');\n";
		echo "$('#txt_bill_date_mst').val('" . change_date_format($row[csf("bill_no_mst_date")]) . "');\n";
		//echo "load_room_rack_self_bin('requires/general_item_receive_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_99', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		//load_drop_down('requires/general_item_receive_controller', this.value+'_'+$data, 'load_drop_floor','floor_td');storeUpdateUptoDisable();
		echo "load_drop_down('requires/general_item_receive_controller', '" . $row[csf('company_id')] . "', 'load_drop_down_store','store_td');\n";
		echo "$('#cbo_store_name').val(" . $row[csf("store_id")] . ");\n";
		echo "load_drop_down('requires/general_item_receive_controller', '" . $row[csf('store_id')] . "_" . $row[csf('company_id')] . "', 'load_drop_floor','floor_td');\n";
		echo "$('#cbo_supplier').val(" . $row[csf("supplier_id")] . ");\n";
		echo "$('#cbo_currency').val(" . $row[csf("currency_id")] . ");\n";
		echo "$('#txt_sup_ref').val('" . $row[csf("supplier_referance")] . "');\n";
		echo "$('#hidden_posted_in_account').val('" . $row[csf("is_posted_account")] . "');\n";

		$addi_info_str = $row[csf("rcvd_book_no")] . "_" . change_date_format($row[csf("addi_challan_date")]) . "_" . $row[csf("bill_no")] . "_" . change_date_format($row[csf("bill_date")]) . "_" . $row[csf("purchaser_name")] . "_" . $row[csf("carried_by")] . "_" . $row[csf("qc_check_by")] . "_" . $row[csf("receive_by")] . "_" . $row[csf("gate_entry_by")] . "_" . change_date_format($row[csf("gate_entry_date")]) . "_" . change_date_format($row[csf("addi_rcvd_date")]) . "_" . $row[csf("gate_entry_no")] . "_" . $row[csf("store_sl_no")];
		echo "$('#txt_addi_info').val('" . $addi_info_str . "');\n";

		echo "$('#variable_string_inventory').val('" . $row[csf("variable_setting")] . "');\n";
		$variable_ref = explode("**", $row[csf("variable_setting")]);
		//echo "$('#variable_string_inventory').val('".$variable_ref[1]."');\n";
		if ($variable_ref[2] == 1) {
			echo "$('#rate_td').css('display', 'none');\n";
			echo "$('#amount_td').css('display', 'none');\n";
			echo "$('#book_currency_td').css('display', 'none');\n";
		} else {
			echo "$('#rate_td').css('display', '');\n";
			echo "$('#amount_td').css('display', '');\n";
			echo "$('#book_currency_td').css('display', '');\n";
		}
		if ($variable_ref[3] == 2) {
			echo "$('#txt_rate').attr('readonly',true);\n";
		} else {
			echo "$('#txt_rate').attr('readonly',false);\n";
		}

		if ($row[csf("currency_id")] == 1) {
			echo "$('#txt_exchange_rate').val(1);\n";
			echo "$('#txt_exchange_rate').attr('disabled',true);\n";
		} else {
			echo "$('#txt_exchange_rate').attr('disabled',false);\n";
		}
		echo "$('#txt_exchange_rate').val(" . $row[csf("exchange_rate")] . ");\n";
		echo "$('#cbo_pay_mode').val(" . $row[csf("pay_mode")] . ");\n";
		echo "$('#cbo_source').val(" . $row[csf("source")] . ");\n";
		echo "$('#txt_boe_mushak_challan_no').val('" . $row[csf("boe_mushak_challan_no")] . "');\n";
		echo "$('#txt_boe_mushak_challan_date').val('" . change_date_format($row[csf("boe_mushak_challan_date")]) . "');\n";
		echo "$('#txt_remarks').val('" . $row[csf("remarks")] . "');\n";

		if ($row[csf("receive_basis")] == 1)
			$wopireq = return_field_value("pi_number", "com_pi_master_details", "id=" . $row[csf("booking_id")] . "");
		else if ($row[csf("receive_basis")] == 2)
			$wopireq = return_field_value("wo_number", "wo_non_order_info_mst", "id=" . $row[csf("booking_id")] . "");
		else if ($row[csf("receive_basis")] == 7)
			$wopireq = return_field_value("requ_no", "inv_purchase_requisition_mst", "id=" . $row[csf("booking_id")] . "");
		echo "$('#txt_wo_pi_req').val('" . $wopireq . "');\n";
		echo "$('#txt_wo_pi_req_id').val(" . $row[csf("booking_id")] . ");\n";

		echo "$('#hidden_lc_id').val(" . $row[csf("lc_no")] . ");\n";
		if ($row[csf("lc_no")] > 0) {
			$lcNumber = return_field_value("lc_number", "com_btb_lc_master_details", "id='" . $row[csf("lc_no")] . "'");
		}
		echo "$('#txt_lc_no').val('" . $lcNumber . "');\n";
		echo "storeUpdateUptoDisable();\n";
		//right side list view
		echo "show_list_view('" . $row[csf("id")] . "__" . $row[csf("variable_setting")] . "','show_dtls_list_view','list_container','requires/general_item_receive_controller','');\n";
		// Check Audited
		if ($row[csf("is_audited")] == 1) echo "$('#audited').text('Audited');\n";
		else echo "$('#audited').text('');\n";

		echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_general_item_receive_entry',1);\n";
	}
	exit();
}

if ($action == "show_dtls_list_view") {
	$data_ref = explode("__", $data);
	$mst_id = $data_ref[0];
	$variable_string_inventory = $data_ref[1];
	$variable_string_inventory_ref = explode("**", $variable_string_inventory);
	$rate_hide_inventory = $variable_string_inventory_ref[2];
	$item_group_arr = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
	$sql = "select a.recv_number,a.recv_number_prefix_num, b.id, b.receive_basis,b.pi_wo_batch_no,c.product_name_details,c.lot,b.order_uom,b.order_qnty,b.order_rate,b.order_ile_cost,b.order_amount,b.cons_amount, c.item_group_id , c.item_description , c.item_size 
	from inv_receive_master a, inv_transaction b,  product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and a.status_active=1 and b.status_active=1 and c.status_active in(1,3) and a.entry_form=20 and a.id=$mst_id
	order by b.id";
	//echo $sql;//die;		
	$result = sql_select($sql);
	$i = 1;
	$totalQnty = 0;
	$totalAmount = 0;
	$totalbookCurr = 0;
?>
	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" width="950" rules="all">
		<thead>
			<tr>
				<th>SL</th>
				<th>Item Group</th>
				<th>Item Description</th>
				<th>UOM</th>
				<th>Receive Qty</th>
				<?
				if ($rate_hide_inventory != 1) {
				?>
					<th>Rate</th>
				<?
				}
				?>

				<th>ILE Cost</th>
				<?
				if ($rate_hide_inventory != 1) {
				?>
					<th>Amount</th>
					<th>Book Currency</th>
				<?
				}
				?>
			</tr>
		</thead>
		<tbody>
			<? foreach ($result as $row) {

				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";

				$wopireq = "";
				if ($row[csf("receive_basis")] == 1)
					$wopireq = return_field_value("pi_number", "com_pi_master_details", "id=" . $row[csf("pi_wo_batch_no")] . "");
				else if ($row[csf("receive_basis")] == 2)
					$wopireq = return_field_value("wo_number", "wo_non_order_info_mst", "id=" . $row[csf("pi_wo_batch_no")] . "");
				else if ($row[csf("receive_basis")] == 7)
					$wopireq = return_field_value("requ_no", "inv_purchase_requisition_mst", "id=" . $row[csf("pi_wo_batch_no")] . "");

				$totalQnty += $row[csf("order_qnty")];
				$totalAmount += $row[csf("order_amount")];
				$totalbookCurr += $row[csf("cons_amount")];

				$item_description = $item_group_arr[$row[csf("item_group_id")]] . " " . $row[csf("item_description")] . " " . $row[csf("item_size")];

			?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")]; ?>","child_form_input_data","requires/general_item_receive_controller")' style="cursor:pointer">
					<td width="50"><?php echo $i; ?></td>
					<td width="120">
						<p><?php echo $item_group_arr[$row[csf("item_group_id")]]; ?></p>
					</td>
					<td width="180">
						<p><?php echo $row[csf("item_description")]; ?></p>
					</td>
					<td width="100">
						<p><?php echo $unit_of_measurement[$row[csf("order_uom")]]; ?></p>
					</td>
					<td width="100" align="right">
						<p><?php echo $row[csf("order_qnty")]; ?></p>
					</td>
					<?
					if ($rate_hide_inventory != 1) {
					?>
						<td width="100" align="right">
							<p><?php echo $row[csf("order_rate")]; ?></p>
						</td>
					<?
					}
					?>
					<td width="100" align="right">
						<p><?php echo $row[csf("order_ile_cost")]; ?></p>
					</td>
					<?
					if ($rate_hide_inventory != 1) {
					?>
						<td width="100" align="right">
							<p><?php echo $row[csf("order_amount")]; ?></p>
						</td>
						<td width="" align="right">
							<p><?php echo number_format($row[csf("cons_amount")], 4); ?></p>
						</td>
					<?
					}
					?>


				</tr>
			<? $i++;
			} ?>
		<tfoot>
			<th colspan="4">Total</th>
			<th><?php echo number_format($totalQnty, 2); ?></th>
			<?
			if ($rate_hide_inventory != 1) {
			?>
				<th></th>
			<?
			}
			?>

			<th></th>
			<?
			if ($rate_hide_inventory != 1) {
			?>
				<th><?php echo number_format($totalAmount, 4); ?></th>
				<th><?php echo number_format($totalbookCurr, 4); ?></th>
			<?
			}
			?>
		</tfoot>
		</tbody>
	</table>
<?
	exit();
}

if ($action == "child_form_input_data") {
	$rcv_dtls_id = $data;

	/*$sql = "select a.currency_id, a.booking_id, a.receive_basis, a.exchange_rate, b.id, b.pi_wo_batch_no, b.prod_id, b.brand_id, c.lot, b.order_uom, b.order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount, b.expire_date, b.room,b.rack, b.self,b.bin_box,c.item_category_id,c.item_group_id,c.item_description,c.current_stock as global_stock,b.remarks,c.brand_name,c.origin
	from inv_receive_master a, inv_transaction b, product_details_master c  
	where a.id=b.mst_id and b.prod_id=c.id and b.id='$rcv_dtls_id'  and a.status_active=1 and b.status_active=1";*/
	/*new dev*/

	$sql = "select a.company_id, a.currency_id, a.booking_id, a.receive_basis, a.receive_purpose, a.loan_party, a.exchange_rate, b.id, b.pi_wo_batch_no, b.prod_id, b.brand_id, c.lot, b.order_uom, b.order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount, b.expire_date, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, c.item_category_id, c.item_group_id, c.item_description as item_description, c.current_stock as global_stock, b.remarks, c.brand_name, c.origin, c.model, c.conversion_factor, b.batch_lot, c.re_order_label
	from inv_receive_master a, inv_transaction b, product_details_master c  
	where a.id=b.mst_id and b.prod_id=c.id and b.id='$rcv_dtls_id'  and a.status_active=1 and b.status_active=1";
	//echo $sql;
	$result = sql_select($sql);

	foreach ($result as $row) {
		echo "$('#cbo_item_category').val(" . $row[csf("item_category_id")] . ");\n";
		echo "load_drop_down( 'requires/general_item_receive_controller', '" . $row[csf("item_category_id")] . "_" . $row[csf("company_id")] . "', 'load_drop_down_itemgroup', 'item_group_td' );\n";
		echo "$('#cbo_item_group').val(" . $row[csf("item_group_id")] . ");\n";
		echo "$('#txt_item_desc').val('" . $row[csf("item_description")] . "');\n";
		echo "$('#txt_warranty_date').val('" . change_date_format($row[csf("expire_date")]) . "');\n";
		if ($db_type == 0) {
			$serialString = return_field_value("$group_concat(serial_no)", "inv_serial_no_details", "recv_trans_id=" . $row[csf("id")] . " group by recv_trans_id");
			$serialqty = return_field_value("$group_concat(serial_qty)", "inv_serial_no_details", "recv_trans_id=" . $row[csf("id")] . " group by recv_trans_id");
		} else {
			$serialString = return_field_value("LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as serial_no", "inv_serial_no_details", "recv_trans_id=" . $row[csf("id")] . " group by recv_trans_id", "serial_no");
			$serialqty = return_field_value("LISTAGG(CAST(serial_qty AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_qty) as serial_qty", "inv_serial_no_details", "recv_trans_id=" . $row[csf("id")] . " group by recv_trans_id", "serial_qty");
		}


		echo "$('#txt_serial_no').val('" . $serialString . "');\n";
		echo "$('#txt_serial_qty').val('" . $serialqty . "');\n";
		echo "$('#cbo_currency').val(" . $row[csf("currency_id")] . ");\n";
		if ($row[csf("receive_basis")] == 1)
			$wopireq = return_field_value("pi_number", "com_pi_master_details", "id=" . $row[csf("booking_id")] . "");
		else if ($row[csf("receive_basis")] == 2)
			$wopireq = return_field_value("wo_number", "wo_non_order_info_mst", "id=" . $row[csf("booking_id")] . "");
		else if ($row[csf("receive_basis")] == 7)
			$wopireq = return_field_value("requ_no", "inv_purchase_requisition_mst", "id=" . $row[csf("booking_id")] . "");
		echo "$('#txt_wo_pi').val('" . $wopireq . "');\n";
		echo "$('#txt_wo_pi_id').val(" . $row[csf("booking_id")] . ");\n";
		echo "$('#txt_receive_qty').val(" . $row[csf("order_qnty")] . ");\n";
		echo "$('#txt_rate').val(" . $row[csf("order_rate")] . ");\n";
		echo "$('#txt_ile').val(" . $row[csf("order_ile_cost")] . ");\n";
		echo "$('#cbo_uom').val(" . $row[csf("order_uom")] . ");\n";
		echo "$('#txt_amount').val(" . $row[csf("order_amount")] . ");\n";
		echo "$('#txt_book_currency').val(" . $row[csf("cons_amount")] . ");\n";

		echo "$('#txt_referance').val('" . $row[csf("remarks")] . "');\n";
		echo "$('#txt_brand').val('" . $row[csf("brand_name")] . "');\n";
		echo "$('#cbo_origin').val('" . $row[csf("origin")] . "');\n";
		echo "$('#txt_model').val('" . $row[csf("model")] . "');\n"; //new dev
		echo "$('#txt_re_order_level').val('" . $row[csf("re_order_label")] . "');\n"; //new dev
		echo "$('#txt_lot').val('" . $row[csf("batch_lot")] . "');\n";
		$conversion_factor = $row[csf("conversion_factor")];
		$totalRtnQnty = return_field_value("sum(c.cons_quantity) as bal", "inv_issue_master b, inv_transaction c", "c.mst_id=b.id and b.booking_id=" . $row[csf("booking_id")] . " and c.prod_id=" . $row[csf("prod_id")] . " and b.entry_form=26 and c.transaction_type=3 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "bal");

		$store_current_stock = return_field_value("sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)-(case when c.transaction_type in(2,3,6) then c.cons_quantity else 0 end)) as store_current_stock", "inv_transaction c", "c.store_id=" . $row[csf("store_id")] . " and c.prod_id=" . $row[csf("prod_id")] . " and c.status_active=1 and c.is_deleted=0", "store_current_stock");
		echo "$('#txt_glob_stock').val(" . $store_current_stock . ");\n";
		if ($row[csf("receive_basis")] == 1) // pi
		{
			$pi_wo_req_qty = return_field_value("sum(b.quantity) as pi_qnty", "com_pi_master_details a, com_pi_item_details b", "a.id=b.pi_id and a.id=" . $row[csf("booking_id")] . "  and b.item_prod_id=" . $row[csf("prod_id")] . " and b.status_active=1 and b.is_deleted=0  group by a.id", "pi_qnty");
			$totalRcvQnty = return_field_value("sum(a.order_qnty) as bal", "inv_transaction a, inv_receive_master b ", "a.mst_id=b.id and b.booking_id=" . $row[csf("booking_id")] . " and a.prod_id=" . $row[csf("prod_id")] . " and b.receive_basis=1 and b.entry_form=20 and a.transaction_type=1 and b.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "bal");

			$totalRcvQnty = $totalRcvQnty - ($totalRtnQnty / $conversion_factor);
			$wo_pi_re_bal = (($pi_wo_req_qty + $row[csf("order_qnty")]) - $totalRcvQnty);
			$wo_pi_re_bal = number_format($wo_pi_re_bal, 2, ".", "");
		} else if ($row[csf("receive_basis")] == 2) // wo
		{

			$pi_wo_req_qty = return_field_value("sum(b.supplier_order_quantity) as wo_quantity", "wo_non_order_info_mst a, wo_non_order_info_dtls b", "a.id=b.mst_id and a.id=" . $row[csf("booking_id")] . " and b.item_id=" . $row[csf("prod_id")] . " and b.status_active=1 and b.is_deleted=0 group by a.id,b.item_id", "wo_quantity");
			$totalRcvQnty = return_field_value("sum(a.order_qnty) as bal", "inv_transaction a, inv_receive_master b ", "a.mst_id=b.id and b.booking_id=" . $row[csf("booking_id")] . " and a.prod_id=" . $row[csf("prod_id")] . " and b.receive_basis=2 and b.entry_form=20 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "bal");
			$totalRcvQnty = $totalRcvQnty - ($totalRtnQnty / $conversion_factor);
			$wo_pi_re_bal = (($pi_wo_req_qty + $row[csf("order_qnty")]) - $totalRcvQnty);
			$wo_pi_re_bal = number_format($wo_pi_re_bal, 2, ".", "");
		} else if ($row[csf("receive_basis")] == 7) // Req
		{
			$pi_wo_req_qty = return_field_value("sum(b.quantity) as req_quantity", "inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b", "a.id=b.mst_id and a.id=" . $row[csf("booking_id")] . " and b.product_id=" . $row[csf("prod_id")] . " and b.status_active=1 and b.is_deleted=0 group by a.id", "req_quantity");
			$totalRcvQnty = return_field_value("sum(a.order_qnty) as bal", "inv_transaction a, inv_receive_master b ", "a.mst_id=b.id and b.booking_id=" . $row[csf("booking_id")] . " and a.prod_id=" . $row[csf("prod_id")] . " and b.receive_basis=7 and b.entry_form=20 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "bal");
			$totalRcvQnty = $totalRcvQnty - ($totalRtnQnty / $conversion_factor);
			//echo $totalRcvQnty."=".$totalRtnQnty."=".$conversion_factor."=".$pi_wo_req_qty."=".$row[csf("order_qnty")]."=".$totalRcvQnty;die;
			$wo_pi_re_bal = (($pi_wo_req_qty + $row[csf("order_qnty")]) - $totalRcvQnty);
			$wo_pi_re_bal = number_format($wo_pi_re_bal, 2, ".", "");
		}
		echo "$('#txt_order_qty').val('" . $wo_pi_re_bal . "');\n";
		echo "load_drop_down('requires/general_item_receive_controller', " . $row[csf("store_id")] . "+'_'+" . $row[csf('company_id')] . ", 'load_drop_floor','floor_td');\n";
		echo "$('#cbo_floor').val('" . $row[csf("floor_id")] . "');\n";
		if ($row[csf("floor_id")]) {
			echo "load_drop_down('requires/general_item_receive_controller', " . $row[csf("floor_id")] . "+'_'+" . $row[csf('company_id')] . "+'_'+" . $row[csf("store_id")] . "+'_'+" . $row[csf("room")] . ", 'load_drop_room','room_td');\n";
		}
		echo "$('#cbo_room').val('" . $row[csf("room")] . "');\n";
		if ($row[csf("room")]) {
			echo "load_drop_down('requires/general_item_receive_controller', " . $row[csf("room")] . "+'_'+" . $row[csf('company_id')] . "+'_'+" . $row[csf("store_id")] . "+'_'+" . $row[csf("rack")] . ", 'load_drop_rack','rack_td');\n";
		}

		echo "$('#txt_rack').val('" . $row[csf("rack")] . "');\n";
		if ($row[csf("rack")]) {
			echo "load_drop_down('requires/general_item_receive_controller', " . $row[csf("rack")] . "+'_'+" . $row[csf('company_id')] . "+'_'+" . $row[csf("store_id")] . "+'_'+" . $row[csf("self")] . ", 'load_drop_shelf','shelf_td');\n";
		}

		echo "$('#txt_shelf').val('" . $row[csf("self")] . "');\n";
		if ($row[csf("self")]) {
			echo "load_drop_down('requires/general_item_receive_controller', " . $row[csf("self")] . "+'_'+" . $row[csf('company_id')] . "+'_'+" . $row[csf("store_id")] . "+'_'+" . $row[csf("bin_box")] . ", 'load_drop_bin','bin_td');\n";
		}

		echo "$('#cbo_bin').val('" . $row[csf("bin_box")] . "');\n";
		echo "$('#update_id').val('" . $row[csf("id")] . "');\n";
		echo "$('#current_prod_id').val(" . $row[csf("prod_id")] . ");\n";
		echo "set_button_status(1, permission, 'fnc_general_item_receive_entry',1);\n";
		echo "disable_enable_fields( 'txt_challan_no*txt_exchange_rate', 0, '', '');\n";
		echo "disable_enable_fields( 'txt_item_desc*cbo_item_group*cbo_item_category', 1, '', '');\n";
		echo "fn_calile();\n";
		echo "storeUpdateUptoDisable();\n";
	}
	exit();
}


if ($action == "load_exchange_rate") {
	if ($data == 1) {
		echo "$('#txt_exchange_rate').val(1);\n";
		echo "$('#txt_exchange_rate').attr('disabled',true);\n";
	} else {
		$last_exchange_rate = return_field_value("exchange_rate", "inv_receive_master", "currency_id=$data order by id limit 0,1");

		echo "$('#txt_exchange_rate').val(" . $last_exchange_rate . ");\n";
		echo "$('#txt_exchange_rate').attr('disabled',false);\n";
	}
	exit();
}




//################################################# function Here #########################################//
//function for domestic rate find--------------//
//parameters rate,ile cost,exchange rate,conversion factor
function return_domestic_rate($rate, $ile_cost, $exchange_rate, $conversion_factor)
{
	$rate_ile = $rate + $ile_cost;
	$rate_ile_exchange = $rate_ile * $exchange_rate;
	$doemstic_rate = $rate_ile_exchange / $conversion_factor;
	return $doemstic_rate;
}



if ($action == "general_item_receive_print") {
	extract($_REQUEST);
	$data = explode('__', $data);
	$variable_inventory_ref = explode("**", $data[3]);
	$rate_hide_inventory = $variable_inventory_ref[2];
	$user_name_arr = return_library_array("select id, user_full_name from user_passwd", "id", "user_full_name");
	//echo $rate_hide_inventory;die;
	//print_r ($data);

	$sql = " select id, recv_number,receive_basis,receive_date, challan_no, challan_date, lc_no, store_id, supplier_id, currency_id, exchange_rate, pay_mode,source,booking_id,location_id,ref_no,inserted_by from inv_receive_master where id='$data[1]'";
	//echo $sql;
	$dataArray = sql_select($sql);
	$rcv_basis = $dataArray[0][csf('receive_basis')];
	$inserted_by = $dataArray[0][csf('inserted_by')];
	$lc_no = $dataArray[0][csf('lc_no')];
	if ($dataArray[0][csf('receive_basis')] == 2 || $dataArray[0][csf('receive_basis')] == 1 || $dataArray[0][csf('receive_basis')] == 7) {

		if ($dataArray[0][csf('receive_basis')] == 2) // Wo Basis
		{
			$wo_sql = sql_select("select a.id,a.wo_number,a.requisition_no as requ_id ,	b.item_id,sum(b.supplier_order_quantity) as wo_qnty from  wo_non_order_info_mst  a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id='" . $dataArray[0][csf('booking_id')] . "' and b.status_active=1 and b.is_deleted=0 group by a.id,a.wo_number,a.requisition_no,b.item_id");
			foreach ($wo_sql as $row) {
				$wo_library[$row[csf("id")]] = $row[csf("wo_number")];
				$wo_library_prod[$row[csf("id")]][$row[csf("item_id")]] = $row[csf("wo_qnty")];

				$requsition_id_arr[$row[csf("wo_number")]] = $row[csf("requ_id")];
				$wo_arr[$row[csf("id")]] = $row[csf("wo_number")];
			}
		} else if ($dataArray[0][csf('receive_basis')] == 1) //Pi Basis
		{
			$sql_pi = sql_select("select a.id as pi_id, a.pi_number,b.work_order_no, b.item_prod_id as item_id , sum(b.quantity) as quantity from com_pi_master_details a , com_pi_item_details b where a.id=b.pi_id  and a.id='" . $dataArray[0][csf('booking_id')] . "' and b.status_active=1 and b.is_deleted=0 group by a.id, a.pi_number,b.work_order_no,b.item_prod_id");
			foreach ($sql_pi as $row) {
				$pi_library[$row[csf("pi_id")]] = $row[csf("pi_number")];
				$wo_library_prod[$row[csf("pi_id")]][$row[csf("item_id")]] = $row[csf("quantity")];

				$pi_wo_no_library[$row[csf("pi_number")]] = $row[csf("work_order_no")];
				$pi_arr[$row[csf("pi_id")]] = $row[csf("pi_number")];
			}
		} else {
			$sql_req = sql_select("select a.id as req_id, a.requ_no,a.division_id,a.department_id, b.product_id as item_id , sum(b.quantity) as quantity from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id  and a.id='" . $dataArray[0][csf('booking_id')] . "' and b.status_active=1 and b.is_deleted=0 group by a.id,a.requ_no,a.division_id,a.department_id,b.product_id");
			foreach ($sql_req as $row) {
				$requisition_library[$row[csf("req_id")]] = $row[csf("requ_no")];
				$wo_library_prod[$row[csf("req_id")]][$row[csf("item_id")]] = $row[csf("quantity")];

				$division_library[$row[csf("requ_no")]] = $row[csf("division_id")];
				$department_library[$row[csf("requ_no")]] = $row[csf("department_id")];
				$req_arr[$row[csf("requ_no")]] = $row[csf("requ_no")];
			}
		}


		$order_prev_sql = sql_select("select a.booking_id,b.prod_id,sum(b.order_qnty) as wo_prev_qnty from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.booking_id='" . $dataArray[0][csf('booking_id')] . "' and a.id !='" . $dataArray[0][csf('id')] . "'  and a.status_active=1 and b.status_active=1 group by a.booking_id,b.prod_id");
		foreach ($order_prev_sql as $row) {
			$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]] = $row[csf("wo_prev_qnty")];
		}
	}

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$lc_arr = return_library_array("select id, lc_number from  com_btb_lc_master_details where item_category_id in(" . implode(",", array_flip($general_item_category)) . ")", "id", "lc_number");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$user_id 		= return_library_array("select id,user_name from user_passwd", "id", "user_name");
	$user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");
	$sql_location = sql_select("select store_location,location_id,id from lib_store_location where id=" . $dataArray[0][csf('store_id')]);
	//echo "select store_location,id from lib_store_location where id=".$dataArray[0][csf('store_id')];
	$lcNumber = return_field_value("lc_number", "com_btb_lc_master_details", "id='" . $lc_no . "'");

	$address_sql = sql_select("select a.address from lib_location a where a.company_id=" . $data[0] . " and a.id=" . $sql_location[0][csf('location_id')]);

?>
	<div style="width:1000px;">
		<table width="1000" cellspacing="0" align="right">
			<tr>
				<td width="100" style="font-size:xx-large" rowspan="2">
					<?
					$cbo_company_name = $data[0];
					$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$cbo_company_name'", "image_location");
					?>
					<img src='../../<? echo $image_location; ?>' height='40' align="left" style="width: 100px;" />
				</td>
				<td colspan="4" style="font-size:xx-large; justify-content: center;text-align: center;">


					<strong><? echo $company_library[$data[0]]; ?></strong><br>


				</td>
				<td width="200">

				</td>
			</tr>
			<tr>
				<td style="font-size:xx-large;justify-content: center;text-align: center;" colspan="4">
					<span style="font-size:18px;justify-content: center;text-align: center;">
						<?
						$address = "";
						if (count($address_sql)) {
							$address = $address_sql[0][csf('address')];
						}
						echo $address;
						?>
					</span>
				</td>
				<td width="200">
					<p style="font-size: 22px;">
						<?php
						$location = '';
						if (count($sql_location)) {
							$location = $location_arr[$sql_location[0][csf('location_id')]];
						} ?>
						Location : <?php echo $location; ?>
					</p>
				</td>
			</tr>

			<tr>
				<td width="100"></td>
				<td colspan="4" style="font-size:x-large;justify-content: center;text-align: center;"><strong><u>Material Receiving & Inspection Report</u></strong></td>
				<td width="200"></td>
			</tr>
			<tr>
				<td colspan="6">&nbsp;</td>
			</tr>
			<tr>
				<td width="120"><strong>MRIR Number:</strong></td>
				<td width="175"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="130"><strong>Receive Basis :</strong></td>
				<td width="175"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<td width="125"><strong>Receive Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Challan No:</strong></td>
				<td><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Challan Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('challan_date')]); ?></td>
				<td><strong>L/C No:</strong></td>
				<td><? echo $lcNumber; //$lc_arr[$dataArray[0][csf('lc_no')]]; 
					?></td>
			</tr>
			<tr>
				<td><strong>Supplier:</strong></td>
				<td><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
				<td><strong>Currency:</strong></td>
				<td><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
				<td><strong>Exchange Rate:</strong></td>
				<td><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
			</tr>
			<tr>
				<td><strong>Pay Mode:</strong></td>
				<td><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
				<td><strong>Source:</strong></td>
				<td><? echo $source[$dataArray[0][csf('source')]]; ?></td>
				<td><strong>Store Name:</strong></td>
				<td><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>WO/PI/Req.No:</strong></td>
				<td>
					<?
					if ($dataArray[0][csf('receive_basis')] == 1) {
						echo $pi_arr[$dataArray[0][csf('booking_id')]];
					} else if ($dataArray[0][csf('receive_basis')] == 2) {
						echo $wo_arr[$dataArray[0][csf('booking_id')]];
					} else if ($dataArray[0][csf('receive_basis')] == 7) {
						if ($req_arr[$dataArray[0][csf('booking_id')]] != "") {

							echo $req_arr[$dataArray[0][csf('booking_id')]];
						} else {

							echo $requisition_library[$dataArray[0][csf('booking_id')]];
						}
					} else {
						echo "";
					}
					?>
				</td>
				<td></td>
				<td></td>
				<td><strong>Ref. No:</strong></td>
				<td><? echo $dataArray[0][csf('ref_no')]; ?></td>
			</tr>
		</table>
		<br>

		<table align="right" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table" style="margin-bottom:15px;">
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<th width="40">SL</th>
					<th width="80" align="center">Item Category</th>
					<th width="100" align="center">Item Group</th>
					<th width="150" align="center">Item Description</th>
					<th width="50" align="center">Lot</th>
					<th width="50" align="center">UOM</th>
					<th width="80" align="center">Recv. Qnty.</th>
					<?
					if ($rate_hide_inventory != 1) {
					?>
						<th width="50" align="center">Rate</th>
						<th width="70" align="center">Amount</th>
						<th width="70" align="center">BDT Amount</th>
					<?
					}
					?>
					<th width="80" align="center">PI/Ord/Req Qnty Bal.</th>
					<th width="80" align="center">Warranty Exp. Date</th>
					<th>Serial No</th>
				</tr>
			</thead>
			<?
			$mrr_no = $dataArray[0][csf('recv_number')];;
			$up_id = $data[1];
			$cond = "";
			if ($mrr_no != "") $cond .= " and a.recv_number='$mrr_no'";
			if ($up_id != "") $cond .= " and a.id='$up_id'";
			$i = 1;
			$item_name_arr = return_library_array("select id, item_name from  lib_item_group", "id", "item_name");


			//echo "select a.id, a.receive_basis, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, (b.order_amount*a.exchange_rate) as amount_bdt , c.item_category_id, c.item_group_id, c.item_description, c.product_name_details, c.lot, c.item_size from inv_receive_master a, inv_transaction b,  product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category not in (1,2,3,5,6,7,12,13,14) and a.entry_form=20  and a.status_active=1 and b.status_active=1 $cond";
			// $sql_result= sql_select("select a.receive_basis, b.id, b.order_uom, b.order_qnty, b.order_rate, a.audit_by, a.audit_date, a.is_audited, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, (b.order_amount*a.exchange_rate) as amount_bdt , c.item_category_id, c.item_group_id, c.item_description, c.product_name_details, c.lot, c.item_size, a.booking_id, b.prod_id, b.batch_lot  from inv_receive_master a, inv_transaction b,  product_details_master c  where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category not in (1,2,3,5,6,7,12,13,14) and a.entry_form=20  and a.status_active=1 and b.status_active=1 $cond order by b.id");
			//query condition changed for Date Wise Item Receive and Issue Multi Category Report page (trnas ref) hyperlink report.
			$sql_result = sql_select("select a.receive_basis, b.id, b.order_uom, b.order_qnty, b.order_rate, a.audit_by, a.audit_date, a.is_audited, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, (b.order_amount*a.exchange_rate) as amount_bdt, c.item_category_id, c.item_group_id, c.item_description, c.product_name_details, c.lot, c.item_size, a.booking_id, b.prod_id, b.batch_lot from inv_receive_master a, inv_transaction b,  product_details_master c  where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and a.status_active=1 and b.status_active=1 $cond order by b.id");



			foreach ($sql_result as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				//$order_qnty=$row[csf('order_qnty')];
				$order_qnty_sum += $row[csf('order_qnty')];

				//$order_amount=$row[csf('order_amount')];
				$order_amount_sum += $row[csf('order_amount')];
				$amount_bdt_sum += $row[csf('amount_bdt')];

				//$balance_qnty=($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]-$row[csf('order_qnty')]);
				$balance_qnty = ($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]] - ($row[csf('order_qnty')] + $order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]));
				$balance_qnty_sum += $balance_qnty;

				$desc = $row[csf('item_description')];

				if ($row[csf('item_size')] != "") {
					$desc .= ", " . $row[csf('item_size')];
				}
				if ($db_type == 0) {
					$serialString = return_field_value("$group_concat(serial_no)", "inv_serial_no_details", "recv_trans_id=" . $row[csf("id")] . " group by recv_trans_id");
				} else {
					$serialString = return_field_value("LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as serial_no", "inv_serial_no_details", "recv_trans_id=" . $row[csf("id")] . " group by recv_trans_id", "serial_no");
				}
			?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td><? echo $i; ?></td>
					<td title="<? echo $row[csf("booking_id")] . "==" . $row[csf("prod_id")]; ?>"><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
					<td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
					<td><? echo $desc; ?></td>
					<td><? echo $row[csf('batch_lot')]; ?></td>
					<td><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
					<td align="right"><? echo number_format($row[csf('order_qnty')], 2); ?></td>
					<?
					if ($rate_hide_inventory != 1) {
					?>
						<td align="right"><? echo $row[csf('order_rate')]; ?></td>
						<td align="right"><? echo number_format($row[csf('order_amount')], 2); ?></td>
						<td align="right"><? echo number_format($row[csf('amount_bdt')], 2); ?></td>
					<?
					}
					?>
					<td align="right"><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7) echo number_format($balance_qnty, 0);
										else echo "0.00"; ?></td>
					<td><? echo change_date_format($row[csf('expire_date')]); ?></td>
					<td style="word-break: break-word;"><? echo $serialString; ?></td>
				</tr>
			<?
				$i++;
			}
			?>
			<tr>
				<td><strong>Total:</strong></td>
				<td>&nbsp;&nbsp;</td>
				<td>&nbsp;&nbsp;</td>
				<td>&nbsp;&nbsp;</td>
				<td>&nbsp;&nbsp;</td>
				<td>&nbsp;&nbsp;</td>
				<td align="right"><strong><? echo number_format($order_qnty_sum, 2); ?></strong></td>
				<?
				if ($rate_hide_inventory != 1) {
				?>
					<td>&nbsp;&nbsp;</td>
					<td align="right"><strong><? echo number_format($order_amount_sum, 2); ?></strong></td>
					<td align="right"><strong><? echo number_format($amount_bdt_sum, 2); ?></strong></td>
				<?
				}
				?>
				<td align="right"><strong><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7) echo number_format($balance_qnty_sum, 2);
											else echo "0.00"; ?></strong></td>
				<td>&nbsp;&nbsp;</td>
				<td>&nbsp;&nbsp;</td>

			</tr>
		</table>
		<table>
			<tr>
				<?php

				if ($sql_result[0][csf("is_audited")] == 1) {
				?>
					<td><strong><? echo 'Audited By &nbsp;' . $user_name[$sql_result[0][csf("audit_by")]] . '&nbsp;' . $sql_result[0][csf("audit_date")]; ?></strong></td>
				<?php
				}
				?>


			</tr>
		</table>
		<div style="margin-left:27px;">
			<?
			$remarks = return_field_value("remarks", "inv_receive_master", "company_id=$data[0] and id='$data[1]'");
			echo "Remarks : " . $remarks;
			?>
		</div>

		<?
		echo signature_table(11, $data[0], "950px", '', 0, $inserted_by);
		?>
	</div>
	</div>
<?
	exit();
}

if ($action == "general_item_receive_print_4") {
	extract($_REQUEST);
	$data = explode('__', $data);
	$variable_inventory_ref = explode("**", $data[3]);
	$rate_hide_inventory = $variable_inventory_ref[2];
	//echo $rate_hide_inventory;die;
	//print_r ($data);

	$sql = " select id, recv_number,receive_basis,receive_date, challan_no, challan_date, lc_no, store_id, supplier_id, currency_id, exchange_rate, pay_mode,source,booking_id,location_id from inv_receive_master where id='$data[1]'";
	//echo $sql;
	$dataArray = sql_select($sql);
	$rcv_basis = $dataArray[0][csf('receive_basis')];
	if ($dataArray[0][csf('receive_basis')] == 2 || $dataArray[0][csf('receive_basis')] == 1 || $dataArray[0][csf('receive_basis')] == 7) {

		if ($dataArray[0][csf('receive_basis')] == 2) // Wo Basis
		{
			$wo_sql = sql_select("select a.id,a.wo_number,a.requisition_no as requ_id ,	b.item_id,sum(b.supplier_order_quantity) as wo_qnty from  wo_non_order_info_mst  a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id='" . $dataArray[0][csf('booking_id')] . "' and b.status_active=1 and b.is_deleted=0 group by a.id,a.wo_number,a.requisition_no,b.item_id");
			foreach ($wo_sql as $row) {
				$wo_library[$row[csf("id")]] = $row[csf("wo_number")];
				$wo_library_prod[$row[csf("id")]][$row[csf("item_id")]] = $row[csf("wo_qnty")];

				$requsition_id_arr[$row[csf("wo_number")]] = $row[csf("requ_id")];
				$wo_arr[$row[csf("id")]] = $row[csf("wo_number")];
			}
		} else if ($dataArray[0][csf('receive_basis')] == 1) //Pi Basis
		{
			$sql_pi = sql_select("select a.id as pi_id, a.pi_number,b.work_order_no, b.item_prod_id as item_id , sum(b.quantity) as quantity from com_pi_master_details a , com_pi_item_details b where a.id=b.pi_id  and a.id='" . $dataArray[0][csf('booking_id')] . "' and b.status_active=1 and b.is_deleted=0 group by a.id, a.pi_number,b.work_order_no,b.item_prod_id");
			foreach ($sql_pi as $row) {
				$pi_library[$row[csf("pi_id")]] = $row[csf("pi_number")];
				$wo_library_prod[$row[csf("pi_id")]][$row[csf("item_id")]] = $row[csf("quantity")];

				$pi_wo_no_library[$row[csf("pi_number")]] = $row[csf("work_order_no")];
				$pi_arr[$row[csf("pi_id")]] = $row[csf("pi_number")];
			}
		} else {
			$sql_req = sql_select("select a.id as req_id, a.requ_no,a.division_id,a.department_id, b.product_id as item_id , sum(b.quantity) as quantity from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id  and a.id='" . $dataArray[0][csf('booking_id')] . "' and b.status_active=1 and b.is_deleted=0 group by a.id,a.requ_no,a.division_id,a.department_id,b.product_id");
			foreach ($sql_req as $row) {
				$requisition_library[$row[csf("req_id")]] = $row[csf("requ_no")];
				$wo_library_prod[$row[csf("req_id")]][$row[csf("item_id")]] = $row[csf("quantity")];

				$division_library[$row[csf("requ_no")]] = $row[csf("division_id")];
				$department_library[$row[csf("requ_no")]] = $row[csf("department_id")];
				$req_arr[$row[csf("requ_no")]] = $row[csf("requ_no")];
			}
		}


		$order_prev_sql = sql_select("select a.booking_id,b.prod_id,sum(b.order_qnty) as wo_prev_qnty from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.booking_id='" . $dataArray[0][csf('booking_id')] . "' and a.id !='" . $dataArray[0][csf('id')] . "'  and a.status_active=1 and b.status_active=1 group by a.booking_id,b.prod_id");
		foreach ($order_prev_sql as $row) {
			$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]] = $row[csf("wo_prev_qnty")];
		}
	}

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$lc_arr = return_library_array("select id, lc_number from  com_btb_lc_master_details where item_category_id in(" . implode(",", array_flip($general_item_category)) . ")", "id", "lc_number");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$user_id 		= return_library_array("select id,user_name from user_passwd", "id", "user_name");
	$user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");

	$sql_location = sql_select("select store_location,location_id,id from lib_store_location where id=" . $dataArray[0][csf('store_id')]);
	//echo "select store_location,id from lib_store_location where id=".$dataArray[0][csf('store_id')];
	$address_sql = sql_select("select a.address from lib_location a where a.company_id=" . $data[0] . " and a.id=" . $sql_location[0][csf('location_id')]);
	$cbo_company_name = $data[0];
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$cbo_company_name'", "image_location");
	$address = "";
	if (count($address_sql)) {
		$address = $address_sql[0][csf('address')];
	};
	$location = '';
	if (count($sql_location)) {
		$location = $location_arr[$sql_location[0][csf('location_id')]];
	};
	$item_name_arr = return_library_array("select id, item_name from  lib_item_group", "id", "item_name");
	$mrr_no = $dataArray[0][csf('recv_number')];;
	$up_id = $data[1];
	$cond = "";
	if ($mrr_no != "") $cond .= " and a.recv_number='$mrr_no'";
	if ($up_id != "") $cond .= " and a.id='$up_id'";
	//echo "select a.id, a.receive_basis, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, (b.order_amount*a.exchange_rate) as amount_bdt , c.item_category_id, c.item_group_id, c.item_description, c.product_name_details, c.lot, c.item_size from inv_receive_master a, inv_transaction b,  product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category not in (1,2,3,5,6,7,12,13,14) and a.entry_form=20  and a.status_active=1 and b.status_active=1 $cond";
	$sql_result = sql_select("select a.id, a.receive_basis, b.order_uom, b.order_qnty, b.order_rate, a.audit_by, a.audit_date, a.is_audited, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, (b.order_amount*a.exchange_rate) as amount_bdt ,b.remarks, c.item_category_id, c.item_group_id, c.item_description, c.product_name_details, c.lot, c.item_size, a.booking_id, b.prod_id, b.batch_lot 
	from inv_receive_master a, inv_transaction b,  product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category not in (1,2,3,5,6,7,12,13,14) and a.entry_form=20  and a.status_active=1 and b.status_active=1 $cond
	order by b.id");
	$remarks = return_field_value("remarks", "inv_receive_master", "company_id=$data[0] and id='$data[1]'");
?>
	<style>
		@media print {
			#page_break {
				page-break-after: always;
			}
		}
	</style>
	<div style="width:960px;">
		<table width="950" cellspacing="0" align="right">
			<tr>
				<td width="100" style="font-size:xx-large" rowspan="2">
					<img src='../../<? echo $image_location; ?>' height='40' align="left" style="width: 100px;" />
				</td>
				<td colspan="4" style="font-size:xx-large; justify-content: center;text-align: center;">
					<strong><? echo $company_library[$data[0]]; ?></strong><br>
				</td>
				<td width="200" align="center">Main Copy
				</td>
			</tr>
			<tr>
				<td style="font-size:xx-large;justify-content: center;text-align: center;" colspan="4">
					<span style="font-size:18px;justify-content: center;text-align: center;">
						<? echo $address; ?>
					</span>
				</td>
				<td width="200">
					<p style="font-size: 22px;">
						Location : <?php echo $location; ?>
					</p>
				</td>
			</tr>
			<tr>
				<td width="100"></td>
				<td colspan="4" style="font-size:x-large;justify-content: center;text-align: center;"><strong><u>Material Receiving & Inspection Report</u></strong></td>
				<td width="200"></td>
			</tr>
			<tr>
				<td colspan="6">&nbsp;</td>
			</tr>
			<tr>
				<td width="120"><strong>MRIR Number:</strong></td>
				<td width="175"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="130"><strong>Receive Basis :</strong></td>
				<td width="175"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<td width="125"><strong>Receive Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Challan No:</strong></td>
				<td><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Challan Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('challan_date')]); ?></td>
				<td><strong>L/C No:</strong></td>
				<td><? echo $lc_arr[$dataArray[0][csf('lc_no')]]; ?></td>

			</tr>
			<tr>
				<td><strong>Supplier:</strong></td>
				<td><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
				<td><strong>Currency:</strong></td>
				<td><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
				<td><strong>Exchange Rate:</strong></td>
				<td><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
			</tr>
			<tr>
				<td><strong>Pay Mode:</strong></td>
				<td><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
				<td><strong>Source:</strong></td>
				<td><? echo $source[$dataArray[0][csf('source')]]; ?></td>
				<td><strong>Store Name:</strong></td>
				<td><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>WO/PI/Req.No:</strong></td>
				<td><?
					if ($dataArray[0][csf('receive_basis')] == 1) {
						echo $pi_arr[$dataArray[0][csf('booking_id')]];
					} else if ($dataArray[0][csf('receive_basis')] == 2) {
						echo $wo_arr[$dataArray[0][csf('booking_id')]];
					} else if ($dataArray[0][csf('receive_basis')] == 7) {
						if ($req_arr[$dataArray[0][csf('booking_id')]] != "") {

							echo $req_arr[$dataArray[0][csf('booking_id')]];
						} else {

							echo $requisition_library[$dataArray[0][csf('booking_id')]];
						}
					} else {
						echo "";
					}
					?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table" style="margin-bottom:15px;">
				<thead bgcolor="#dddddd" align="center">
					<th width="40">SL</th>
					<th width="80" align="center">Item Category</th>
					<th width="150" align="center">Item Group</th>
					<th width="200" align="center">Item Description</th>
					<th width="50" align="center">Lot</th>
					<th width="50" align="center">UOM</th>
					<th width="80" align="center">Recv. Qnty.</th>
					<?
					if ($rate_hide_inventory != 1) {
					?>
						<th width="50" align="center">Rate</th>
						<th width="70" align="center">Amount</th>
						<th width="70" align="center">BDT Amount</th>
					<?
					}
					?>
					<th width="80" align="center">PI/Ord/Req Qnty Bal.</th>
					<th align="center">Warranty Exp. Date</th>
					<th align="center">Comments</th>
				</thead>
				<?
				$i = 1;
				foreach ($sql_result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					//$order_qnty=$row[csf('order_qnty')];
					$order_qnty_sum += $row[csf('order_qnty')];

					//$order_amount=$row[csf('order_amount')];
					$order_amount_sum += $row[csf('order_amount')];
					$amount_bdt_sum += $row[csf('amount_bdt')];

					//$balance_qnty=($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]-$row[csf('order_qnty')]);
					$balance_qnty = ($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]] - ($row[csf('order_qnty')] + $order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]));
					$balance_qnty_sum += $balance_qnty;

					$desc = $row[csf('item_description')];

					if ($row[csf('item_size')] != "") {
						$desc .= ", " . $row[csf('item_size')];
					}
				?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $i; ?></td>
						<td title="<? echo $row[csf("booking_id")] . "==" . $row[csf("prod_id")]; ?>"><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
						<td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
						<td><? echo $desc; ?></td>
						<td><? echo $row[csf('batch_lot')]; ?></td>
						<td><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
						<td align="right"><? echo number_format($row[csf('order_qnty')], 0); ?></td>
						<?
						if ($rate_hide_inventory != 1) {
						?>
							<td align="right"><? echo $row[csf('order_rate')]; ?></td>
							<td align="right"><? echo number_format($row[csf('order_amount')], 2); ?></td>
							<td align="right"><? echo number_format($row[csf('amount_bdt')], 2); ?></td>
						<?
						}
						?>
						<td align="right"><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7) echo number_format($balance_qnty, 0); ?></td>
						<td><? echo change_date_format($row[csf('expire_date')]); ?></td>
						<td><? echo $row[csf('remarks')]; ?></td>
					</tr>
				<?
					$i++;
				}
				?>
				<tr>
					<td><strong>Total:</strong></td>
					<td>&nbsp;&nbsp;</td>
					<td>&nbsp;&nbsp;</td>
					<td>&nbsp;&nbsp;</td>
					<td>&nbsp;&nbsp;</td>
					<td>&nbsp;&nbsp;</td>
					<td align="right"><strong><? echo number_format($order_qnty_sum, 2); ?></strong></td>
					<?
					if ($rate_hide_inventory != 1) {
					?>
						<td>&nbsp;&nbsp;</td>
						<td align="right"><strong><? echo number_format($order_amount_sum, 2); ?></strong></td>
						<td align="right"><strong><? echo number_format($amount_bdt_sum, 2); ?></strong></td>
					<?
					}
					?>
					<td align="right"><strong><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7)  echo number_format($balance_qnty_sum, 2); ?></strong></td>
					<td>&nbsp;&nbsp;</td>

				</tr>
			</table>
			<table>
				<tr>
					<td><strong><? echo 'In Word: ' . number_to_words(number_format($order_amount_sum, 2)); ?></strong></td>
				</tr>
				<tr>
					<?php
					if ($sql_result[0][csf("is_audited")] == 1) {
					?>
						<td><strong><? echo 'Audited By &nbsp;' . $user_name[$sql_result[0][csf("audit_by")]] . '&nbsp;' . $sql_result[0][csf("audit_date")]; ?></strong></td>
					<?php
					}
					?>
				</tr>
			</table>
			<br>
			<div style="margin-left:27px;">
				<? echo "Remarks : " . $remarks; ?>
			</div>
			<?
			echo signature_table(11, $data[0], "1000px", '1', 10, $inserted_by);
			?>
		</div>
	</div>
	<div id="page_break"></div>
	<div style="width:960px;">
		<table width="950" cellspacing="0" align="left">
			<tr>
				<td width="100" style="font-size:xx-large" rowspan="2">
					<img src='../../<? echo $image_location; ?>' height='40' align="left" style="width: 100px;" />
				</td>
				<td colspan="4" style="font-size:xx-large; justify-content: center;text-align: center;">
					<strong><? echo $company_library[$data[0]]; ?></strong><br>
				</td>
				<td width="200" align="center">Duplicate Copy
				</td>
			</tr>
			<tr>
				<td style="font-size:xx-large;justify-content: center;text-align: center;" colspan="4">
					<span style="font-size:18px;justify-content: center;text-align: center;">
						<? echo $address; ?>
					</span>
				</td>
				<td width="200">
					<p style="font-size: 22px;">
						Location : <?php echo $location; ?>
					</p>
				</td>
			</tr>
			<tr>
				<td width="100"></td>
				<td colspan="4" style="font-size:x-large;justify-content: center;text-align: center;"><strong><u>Material Receiving & Inspection Report</u></strong></td>
				<td width="200"></td>
			</tr>
			<tr>
				<td colspan="6">&nbsp;</td>
			</tr>
			<tr>
				<td width="120"><strong>MRIR Number:</strong></td>
				<td width="175"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="130"><strong>Receive Basis :</strong></td>
				<td width="175"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<td width="125"><strong>Receive Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Challan No:</strong></td>
				<td><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>L/C No:</strong></td>
				<td><? echo $lc_arr[$dataArray[0][csf('lc_no')]]; ?></td>
				<td><strong>Store Name:</strong></td>
				<td><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Supplier:</strong></td>
				<td><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
				<td><strong>Currency:</strong></td>
				<td><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
				<td><strong>Exchange Rate:</strong></td>
				<td><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
			</tr>
			<tr>
				<td><strong>Pay Mode:</strong></td>
				<td><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
				<td><strong>Source:</strong></td>
				<td><? echo $source[$dataArray[0][csf('source')]]; ?></td>
				<td><strong>WO/PI/Req.No:</strong></td>
				<td><?
					if ($dataArray[0][csf('receive_basis')] == 1) {
						echo $pi_arr[$dataArray[0][csf('booking_id')]];
					} else if ($dataArray[0][csf('receive_basis')] == 2) {
						echo $wo_arr[$dataArray[0][csf('booking_id')]];
					} else if ($dataArray[0][csf('receive_basis')] == 7) {
						if ($req_arr[$dataArray[0][csf('booking_id')]] != "") {

							echo $req_arr[$dataArray[0][csf('booking_id')]];
						} else {

							echo $requisition_library[$dataArray[0][csf('booking_id')]];
						}
					} else {
						echo "";
					}
					?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table" style="margin-bottom:15px;">
				<thead bgcolor="#dddddd" align="left">
					<th width="40">SL</th>
					<th width="80" align="center">Item Category</th>
					<th width="150" align="center">Item Group</th>
					<th width="200" align="center">Item Description</th>
					<th width="50" align="center">Lot</th>
					<th width="50" align="center">UOM</th>
					<th width="80" align="center">Recv. Qnty.</th>
					<?
					if ($rate_hide_inventory != 1) {
					?>
						<th width="50" align="center">Rate</th>
						<th width="70" align="center">Amount</th>
						<th width="70" align="center">BDT Amount</th>
					<?
					}
					?>
					<th width="80" align="center">PI/Ord/Req Qnty Bal.</th>
					<th align="center">Warranty Exp. Date</th>
					<th align="center">Comments</th>
				</thead>
				<?
				$i = 1;
				$order_qnty_sum = $order_amount_sum = $amount_bdt_sum = $balance_qnty_sum = 0;
				foreach ($sql_result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					//$order_qnty=$row[csf('order_qnty')];
					$order_qnty_sum += $row[csf('order_qnty')];

					//$order_amount=$row[csf('order_amount')];
					$order_amount_sum += $row[csf('order_amount')];
					$amount_bdt_sum += $row[csf('amount_bdt')];

					//$balance_qnty=($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]-$row[csf('order_qnty')]);
					$balance_qnty = ($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]] - ($row[csf('order_qnty')] + $order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]));
					$balance_qnty_sum += $balance_qnty;

					$desc = $row[csf('item_description')];

					if ($row[csf('item_size')] != "") {
						$desc .= ", " . $row[csf('item_size')];
					}
				?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $i; ?></td>
						<td title="<? echo $row[csf("booking_id")] . "==" . $row[csf("prod_id")]; ?>"><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
						<td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
						<td><? echo $desc; ?></td>
						<td><? echo $row[csf('batch_lot')]; ?></td>
						<td><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
						<td align="right"><? echo number_format($row[csf('order_qnty')], 0); ?></td>
						<?
						if ($rate_hide_inventory != 1) {
						?>
							<td align="right"><? echo $row[csf('order_rate')]; ?></td>
							<td align="right"><? echo number_format($row[csf('order_amount')], 2); ?></td>
							<td align="right"><? echo number_format($row[csf('amount_bdt')], 2); ?></td>
						<?
						}
						?>
						<td align="right"><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7) echo number_format($balance_qnty, 0); ?></td>
						<td><? echo change_date_format($row[csf('expire_date')]); ?></td>
						<td><? echo $row[csf('remarks')]; ?></td>
					</tr>
				<?
					$i++;
				}
				?>
				<tr>
					<td><strong>Total:</strong></td>
					<td>&nbsp;&nbsp;</td>
					<td>&nbsp;&nbsp;</td>
					<td>&nbsp;&nbsp;</td>
					<td>&nbsp;&nbsp;</td>
					<td align="right"><strong><? echo number_format($order_qnty_sum, 2); ?></strong></td>
					<?
					if ($rate_hide_inventory != 1) {
					?>
						<td>&nbsp;&nbsp;</td>
						<td align="right"><strong><? echo number_format($order_amount_sum, 2); ?></strong></td>
						<td align="right"><strong><? echo number_format($amount_bdt_sum, 2); ?></strong></td>
					<?
					}
					?>
					<td align="right"><strong><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7)  echo number_format($balance_qnty_sum, 2); ?></strong></td>
					<td>&nbsp;&nbsp;</td>

				</tr>
			</table>
			<table>
				<tr>
					<td><strong><? echo 'In Word: ' . number_to_words(number_format($order_amount_sum, 2)); ?></strong></td>
				</tr>
				<tr>
					<?php
					if ($sql_result[0][csf("is_audited")] == 1) {
					?>
						<td><strong><? echo 'Audited By &nbsp;' . $user_name[$sql_result[0][csf("audit_by")]] . '&nbsp;' . $sql_result[0][csf("audit_date")]; ?></strong></td>
					<?php
					}
					?>
				</tr>
			</table>
			<br>
			<div style="margin-left:27px;">
				<? echo "Remarks : " . $remarks; ?>
			</div>
			<?
			echo signature_table(11, $data[0], "1000px", '1', 10, $inserted_by);
			?>
		</div>
	</div>
<?
	exit();
}

if ($action == "general_item_receive_print_3") 
{
	extract($_REQUEST);
	$data = explode('__', $data);
	echo load_html_head_contents("General Item Receive Info", "../../../", 1, 1, $unicode);

	$countryShortNameArr = return_library_array("select id,short_name from lib_country where status_active=1 and is_deleted=0", 'id', 'short_name');
	$division_name_arr = return_library_array("select id, division_name from   lib_division", "id", "division_name");
	$department_name_arr = return_library_array("select id, department_name from   lib_department", "id", "department_name");

	$req_div_name_arr = return_library_array("select id, division_id from   inv_purchase_requisition_mst", "id", "division_id");
	$req_department_name_arr = return_library_array("select id, department_id from inv_purchase_requisition_mst", "id", "department_id");
	$req_no_arr = return_library_array("select wo_number, requisition_no from wo_non_order_info_mst where entry_form in(146,147)", "wo_number", "requisition_no");

	$user_name_arr = return_library_array("select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name", "id", "user_full_name");
	$lc_arr = return_library_array("select id, lc_number from  com_btb_lc_master_details", "id", "lc_number");

	$sql = " select id, recv_number, receive_basis, receive_purpose, booking_id, loan_party, gate_entry_no, receive_date, challan_no, challan_date, location_id, store_id, supplier_id, lc_no, currency_id, exchange_rate, source,supplier_referance,pay_mode,store_sl_no,rcvd_book_no, addi_challan_date,bill_no, bill_date, purchaser_name, carried_by, qc_check_by, receive_by,gate_entry_by,gate_entry_date,addi_rcvd_date from inv_receive_master where id='$data[1]'";
	//echo $sql;die;
	$dataArray = sql_select($sql);
	$rcv_basis = $dataArray[0][csf('receive_basis')];
	if ($dataArray[0][csf('receive_basis')] == 2 || $dataArray[0][csf('receive_basis')] == 1 || $dataArray[0][csf('receive_basis')] == 7) {

		if ($dataArray[0][csf('receive_basis')] == 2) // Wo
		{
			$wo_sql = sql_select("select a.id,a.wo_number,a.wo_date,a.requisition_no as requ_id,b.item_id,sum(b.supplier_order_quantity) as wo_qnty from  wo_non_order_info_mst  a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id='" . $dataArray[0][csf('booking_id')] . "' and a.status_active=1 and b.status_active=1 group by a.id,a.wo_number,a.requisition_no ,b.item_id,a.wo_date");
			foreach ($wo_sql as $row) {
				$wo_library[$row[csf("id")]]["wo_number"] = $row[csf("wo_number")];
				$wo_library[$row[csf("id")]]["wo_date"] = $row[csf("wo_date")];
				$wo_library_prod[$row[csf("id")]][$row[csf("item_id")]] = $row[csf("wo_qnty")];
				$requsition_id_arr[$row[csf("wo_number")]] = $row[csf("requ_id")];
			}
		} else if ($dataArray[0][csf('receive_basis')] == 1) // Pi
		{
			$sql_pi = sql_select("select a.id as pi_id, a.pi_number,b.work_order_no, b.item_prod_id as item_id , sum(b.quantity) as quantity from com_pi_master_details a , com_pi_item_details b where a.id=b.pi_id  and a.id='" . $dataArray[0][csf('booking_id')] . "' group by a.id, a.pi_number,b.work_order_no,b.item_prod_id");
			foreach ($sql_pi as $row) {
				$pi_library[$row[csf("pi_id")]] = $row[csf("pi_number")];
				$wo_library_prod[$row[csf("pi_id")]][$row[csf("item_id")]] += $row[csf("quantity")];

				$pi_wo_no_library[$row[csf("pi_number")]] = $row[csf("work_order_no")];
			}
		} else // Req.
		{
			$sql_req = sql_select("select a.id as req_id, a.requ_no,a.requisition_date,a.division_id,a.department_id, b.product_id as item_id , sum(b.quantity) as quantity from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id  and a.id='" . $dataArray[0][csf('booking_id')] . "' group by a.id,a.requ_no,a.division_id,a.department_id,b.product_id,a.requisition_date");
			foreach ($sql_req as $row) {
				$requisition_library[$row[csf("req_id")]]["requ_no"] = $row[csf("requ_no")];
				$requisition_library[$row[csf("req_id")]]["requisition_date"] = $row[csf("requisition_date")];
				$wo_library_prod[$row[csf("req_id")]][$row[csf("item_id")]] = $row[csf("quantity")];

				$division_library[$row[csf("requ_no")]] = $row[csf("division_id")];
				$department_library[$row[csf("requ_no")]] = $row[csf("department_id")];
			}
		}


		$order_prev_sql = sql_select("select a.booking_id,b.prod_id,sum(b.order_qnty) as wo_prev_qnty from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.booking_id='" . $dataArray[0][csf('booking_id')] . "' and a.id !='" . $dataArray[0][csf('id')] . "'  and a.status_active=1 and b.status_active=1 group by a.booking_id,b.prod_id");
		foreach ($order_prev_sql as $row) {
			$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]] = $row[csf("wo_prev_qnty")];
		}
	}

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

	$supplier_res = sql_select("select id,supplier_name,address_1 from  lib_supplier");
	foreach ($supplier_res as $val) {
		$supplier_library[$val[csf("id")]]["supplier_name"] = $val[csf("supplier_name")];
		$supplier_library[$val[csf("id")]]["supplier_address"] = $val[csf("address_1")];
	}


	$prod_min_max_rate_res = sql_select("select prod_id, max(order_rate) max_order_rate,max(transaction_date) keep (dense_rank first order by order_rate desc) max_order_date,
	min(order_rate)min_order_rate,min(transaction_date) keep (dense_rank first order by order_rate asc) min_order_date
	from inv_transaction where transaction_type=1 and status_active = 1
	group by prod_id");

	foreach ($prod_min_max_rate_res as $val) {
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["max_order_rate"] = $val[csf("max_order_rate")];
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["max_order_date"] = $val[csf("max_order_date")];
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["min_order_rate"] = $val[csf("min_order_rate")];
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["min_order_date"] = $val[csf("min_order_date")];
	}

	//=================Last Rate============>>>>>================

	//$last_rate_res = sql_select("select prod_id,order_rate,transaction_date, mst_id from inv_transaction where transaction_type=1 and status_active = 1 and transaction_date <= '".$dataArray[0][csf('receive_date')]."' and mst_id < ".$dataArray[0][csf('id')]." order by mst_id desc");

	$last_rate_res = sql_select("select a.prod_id,a.order_rate,a.transaction_date, a.mst_id, b.recv_number
		from inv_transaction a, inv_receive_master b
		where a.mst_id = b.id and a.transaction_type=1 and a.status_active = 1 
		and a.transaction_date <= '" . $dataArray[0][csf('receive_date')] . "' and a.mst_id < " . $dataArray[0][csf('id')] . " 
		 order by a.mst_id desc");


	$prodDupliChkArr = array();
	foreach ($last_rate_res as $value) {
		if ($prodDupliChkArr[$value[csf("prod_id")]] == "") {
			$prodDupliChkArr[$value[csf("prod_id")]] = $value[csf("prod_id")];
			$last_rate_arr[$value[csf("prod_id")]]["last_rate"] = $value[csf("order_rate")];
			$last_rate_arr[$value[csf("prod_id")]]["trans_date"] = $value[csf("transaction_date")];
			$last_rate_arr[$value[csf("prod_id")]]["recv_number"] = $value[csf("recv_number")];
		}
	}
	unset($last_rate_res);

	//=======================================<<<<================

	$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$item_name_arr = return_library_array("select id, item_name from  lib_item_group", "id", "item_name");

	?>
	<style type="text/css">
		#top_table tr td {
			vertical-align: top;
		}
	</style>
	<div style="width:1170px;">
		<table width="1150" cellspacing="1" align="right" id="top_table">
			<tr>
				<td width="100" style="font-size:xx-large" rowspan="2">
					<?
					$cbo_company_name = $data[0];
					$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$cbo_company_name'", "image_location");
					?>
					<img src='../../../<? echo $image_location; ?>' height='40' align="left" style="width: 100px;" />
				</td>
				<td colspan="8" style="font-size:xx-large; justify-content: center;text-align: center;">
					<strong><? echo $company_library[$data[0]]; ?></strong><br>
					<span style="font-size:14px">
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
							Province No: <? echo $result['province']; ?>
							Country: <? echo $country_arr[$result['country_id']]; ?><br>
							Email Address: <? echo $result['email']; ?>
							Website No: <? echo $result['website'];
									}
										?>
					</span>
				</td>
				<td width="200">

				</td>
			</tr>
			<tr>
				<td colspan="8" align="center" style="font-size:x-large"><strong><u>Material Receiving Report</u></strong></td>
			</tr>
			<tr>
				<td width="150"><strong>MRR Number</strong></td>
				<td width="180">:<? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="100"><strong>Receive No</strong></td>
				<td width="130">:<? echo $dataArray[0][csf('rcvd_book_no')]; ?></td>
				<td width="100"><strong>Receive Basis</strong></td>
				<td width="220">:<? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?>&nbsp;&nbsp;</td>
				<td width="120"><strong>Purchase By</strong></td>
				<td width="180">:<? echo $user_name_arr[$dataArray[0][csf('purchaser_name')]]; ?></td>

			</tr>
			<tr>
				<td><strong>Mrr Date</strong></td>
				<td>:<? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
				<td><strong>Receive Date</strong></td>
				<td>:<? echo change_date_format($dataArray[0][csf('addi_rcvd_date')]); ?></td>
				<td><strong>L/C No</strong></td>
				<td>:<? echo $lc_arr[$dataArray[0][csf('lc_no')]]; ?></td>
				<td><strong>Delivered By</strong></td>
				<td>:<? echo $user_name_arr[$dataArray[0][csf('carried_by')]]; ?></td>
			</tr>
			<tr>
				<td><strong>WO/PI/Req.No</strong></td>
				<td>:
					<?
					if ($dataArray[0][csf('receive_basis')] == 1) // PI
					{
						echo $pi_library[$dataArray[0][csf('booking_id')]];
					} else if ($dataArray[0][csf('receive_basis')] == 2) // WO
					{
						echo $wo_library[$dataArray[0][csf('booking_id')]]["wo_number"];
					} else  if ($dataArray[0][csf('receive_basis')] == 7) // Req.
					{
						echo $requisition_library[$dataArray[0][csf('booking_id')]]["requ_no"];
					} else {
						echo "Independent";
					}
					?></td>
				<td><strong>Challan No</strong></td>
				<td>:<? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Challan Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('challan_date')]); ?></td>
				<td><strong>Supplier</strong></td>
				<td>:<? echo $supplier_library[$dataArray[0][csf('supplier_id')]]["supplier_name"]; ?></td>

			</tr>
			<tr>
				<td><strong>WO/PI/Req.No Date</strong></td>
				<td>:
					<?
					if ($dataArray[0][csf('receive_basis')] == 1) // PI
					{
						echo $pi_library[$dataArray[0][csf('booking_id')]];
					} else if ($dataArray[0][csf('receive_basis')] == 2) // WO
					{
						echo $wo_library[$dataArray[0][csf('booking_id')]]["wo_date"];
					} else  if ($dataArray[0][csf('receive_basis')] == 7) // Req.
					{
						echo $requisition_library[$dataArray[0][csf('booking_id')]]["requisition_date"];
					} else {
						echo "";
					}
					?>
				</td>
				<td><strong>Challan Date</strong></td>
				<td>:<? echo change_date_format($dataArray[0][csf('addi_challan_date')]); ?></td>
				<td><strong>Supplier Address</strong></td>
				<td>
					<p>:<? echo $supplier_library[$dataArray[0][csf('supplier_id')]]["supplier_address"]; ?></p>
				</td>
				<td><strong>Received By </strong></td>
				<td>:<? echo $user_name_arr[$dataArray[0][csf('receive_by')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Gate Entry No</strong></td>
				<td>:<? echo $dataArray[0][csf('gate_entry_no')]; ?></td>
				<td><strong>Bill No</strong></td>
				<td>:<? echo $dataArray[0][csf('bill_no')]; ?></td>
				<td><strong>Store Name</strong></td>
				<td>:<strong><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></strong></td>
				<td><strong>Gate Entry By </strong></td>
				<td>:<? echo $user_name_arr[$dataArray[0][csf('gate_entry_by')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Gate Entry Date</strong></td>
				<td>:<? echo change_date_format($dataArray[0][csf('gate_entry_date')]); ?></td>
				<td><strong>Bill Date</strong></td>
				<td>:<? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
				<td><strong>Store Sl No</strong></td>
				<td>:<strong><? echo $dataArray[0][csf('store_sl_no')]; ?></strong></td>
				<td><strong>Prepared By</strong></td>
				<td>:<? echo $user_name_arr[$user_id]; ?></td>
			</tr>
			<tr>
				<td><strong>Pay Mode</strong></td>
				<td>:<?
						if ($dataArray[0][csf('receive_basis')] == 1 && $dataArray[0][csf('pay_mode')] == 0) {
							echo $pay_mode[2]; //PI basis pay mode will be import
						} else {
							echo $pay_mode[$dataArray[0][csf('pay_mode')]];
						}

						?>
				</td>
				<td><strong>Currency</strong></td>
				<td>:<? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
				<td><strong>Source</strong></td>
				<td>:<? echo $source[$dataArray[0][csf('source')]]; ?></td>
				<td><strong>QC Checked By</strong></td>
				<td>:<? echo $user_name_arr[$dataArray[0][csf('qc_check_by')]]; ?></td>
			</tr>
			<tr>
				<td rowspan="2" valign="top"><strong>Barcode</strong></td>:
				<td rowspan="2" colspan="3" valign="top" id="bar_code"></td>
				<td><strong>Exchange Rate</strong></td>
				<td>:<? echo $dataArray[0][csf('exchange_rate')]; ?></td>
				<td colspan="2"></td>
			</tr>
			<tr>
				<td colspan="6"></td>
			</tr>
		</table>
		<?
		if ($db_type == 2) {
			$sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category,c.id as product_id,
	  b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks, b.batch_lot,
	  (c.sub_group_name||' '|| c.item_description || ' '|| c.item_size) as product_name_details,c.item_number,c.item_group_id, c.item_description,c.item_code, c.brand_name, c.origin, b.transaction_date
	  from inv_receive_master a, inv_transaction b, product_details_master c 
	  where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1 
	  order by b.id";
		} else {
			$sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category,c.id as product_id,
	  b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks, b.batch_lot,
	 concat(c.sub_group_name,c.item_description, c.item_size) as product_name_details, c.item_number, c.item_group_id, c.item_description,c.item_code, c.brand_name, c.origin, b.transaction_date
	  from inv_receive_master a, inv_transaction b, product_details_master c 
	  where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1
	  order by b.id";
		}

		$sql_result = sql_select($sql_dtls);
		$i = 1;
		?>
		<br>
		<br>
		<div style="width:100%;margin-top:20px;">
			<table align="left" cellspacing="0" width="1300" border="1" rules="all" class="rpt_table" style="margin-left:20px;">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="40" rowspan="2">Item Code</th>
						<th width="40" rowspan="2">Item Number</th>
						<th width="70" rowspan="2">Item Category</th>
						<th width="110" rowspan="2">Item Group</th>
						<th width="160" rowspan="2">Item Description</th>
						<th width="70" rowspan="2">Brand</th>
						<th width="50" rowspan="2">Origin</th>
						<th width="50" rowspan="2">Lot</th>
						<th width="40" rowspan="2">UOM</th>
						<th width="70" rowspan="2">WO/PI Qnty.</th>
						<th width="70" rowspan="2">Previous Recv Qnty</th>
						<th width="70" rowspan="2">Today Recv. Qnty.</th>
						<th width="80" rowspan="2">WO/PI Qnty Bal.</th>
						<th width="240" colspan="3">Unit Price and Date</th>
						<th width="100" rowspan="2">Rate</th>
						<th rowspan="2">Amount</th>
					</tr>
					<tr>
						<th width="80">Maximum</th>
						<th width="80">Minimum</th>
						<th width="80">Last</th>
					</tr>
				</thead>
				<tbody>
					<?
					foreach ($sql_result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$order_qnty = $row[csf('order_qnty')];
						$order_qnty_sum += $order_qnty;

						$order_amount = $row[csf('order_amount')];
						$order_amount_sum += $order_amount;

						$balance_qnty = ($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]] - ($row[csf('order_qnty')] + $order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]));
						$balance_qnty_sum += $balance_qnty;

						$desc = $row[csf('item_description')];

						if ($row[csf('item_size')] != "") {
							$desc .= ", " . $row[csf('item_size')];
						}
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td><? echo $i; ?></td>
							<td><? echo $row[csf('item_code')]; ?></td>
							<td><? echo $row[csf('item_number')]; ?></td>
							<td><? echo $item_category[$row[csf('item_category')]]; ?></td>
							<td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
							<td align="center"><? echo $row[csf('product_name_details')]; ?></td>
							<td align="center"><? echo $row[csf('brand_name')]; ?></td>
							<td align="center"><? echo $countryShortNameArr[$row[csf('origin')]]; ?></td>
							<td align="center"><? echo $row[csf('batch_lot')]; ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
							<td align="right"><? echo number_format($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]], 2);
												$tot_ord_qnty += $wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]; ?></td>
							<td align="right"><? echo number_format($order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]], 2);
												$tot_prev_qnty += $order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]; ?></td>
							<td align="right"><? echo number_format($row[csf('order_qnty')], 2, '.', ','); ?></td>
							<td align="right"><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7) echo number_format($balance_qnty, 2, '.', ','); ?></td>

							<td align="right">
								<? echo  number_format($prod_min_max_rate_arr[$row[csf("prod_id")]]["max_order_rate"], 2) . "<hr/ style='border:1px solid black'>" . $prod_min_max_rate_arr[$row[csf("prod_id")]]["max_order_date"]; ?>
							</td>
							<td align="right">
								<? echo  number_format($prod_min_max_rate_arr[$row[csf("prod_id")]]["min_order_rate"], 2) . "<hr/ style='border:1px solid black'>" . $prod_min_max_rate_arr[$row[csf("prod_id")]]["min_order_date"]; ?>
							</td>
							<td align="right" title="mrr no = <? echo $last_rate_arr[$row[csf("prod_id")]]["recv_number"]; ?>">
								<?
								//echo  number_format($row[csf("order_rate")],2)."<hr/ style='border:1px solid black'><span>".$row[csf("transaction_date")]."</span>"; 
								echo  number_format($last_rate_arr[$row[csf("prod_id")]]["last_rate"], 2) . "<hr/ style='border:1px solid black'><span>" . $last_rate_arr[$row[csf("prod_id")]]["trans_date"] . "</span>";
								?>
							</td>

							<td align="right"><? echo number_format($row[csf('order_rate')], 4, '.', ','); ?></td>
							<td align="right"><? echo number_format($row[csf('order_amount')], 2, '.', ',');  ?></td>
						</tr>
					<?
						$i++;
					}
					?>
					<tr bgcolor="#CCCCCC">
						<td align="right" colspan="10">Total</td>
						<td align="right"><? echo number_format($tot_ord_qnty, 2, '.', ','); ?></td>
						<td align="right"><? echo number_format($tot_prev_qnty, 2, '.', ','); ?></td>
						<td align="right"><? echo number_format($order_qnty_sum, 2, '.', ','); ?></td>
						<td align="right"><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7) echo number_format($balance_qnty_sum, 2, '.', ','); ?></td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right" colspan="3"><? echo number_format($order_amount_sum, 2, '.', ','); ?></td>

					</tr>
				</tbody>
			</table>
			<table>
				<tr>
					<td colspan="15">
						<h3 align="center" style="margin-left:150px;"> In Words : &nbsp;<? echo number_to_words(number_format($order_amount_sum, 2, '.', ',')) . "( " . $currency[$dataArray[0][csf('currency_id')]] . " )"; ?></h3>
					</td>
				</tr>
			</table>
			<br>
			<?
			echo signature_table(11, $data[0], "1300px", '1', 40, $inserted_by);
			?>
		</div>
	</div>

	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			// alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

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
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#bar_code").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $dataArray[0][csf('recv_number')]; ?>');
	</script>
	<?
	exit();
}


if ($action == "general_item_receive_print_6") 
{
	extract($_REQUEST);
	$data = explode('__', $data);
	echo load_html_head_contents("General Item Receive Info", "../../../", 1, 1, $unicode);

	$countryShortNameArr = return_library_array("select id,short_name from lib_country where status_active=1 and is_deleted=0", 'id', 'short_name');
	$division_name_arr = return_library_array("select id, division_name from   lib_division", "id", "division_name");
	$department_name_arr = return_library_array("select id, department_name from   lib_department", "id", "department_name");

	$req_div_name_arr = return_library_array("select id, division_id from   inv_purchase_requisition_mst", "id", "division_id");
	$req_department_name_arr = return_library_array("select id, department_id from inv_purchase_requisition_mst", "id", "department_id");
	$req_no_arr = return_library_array("select wo_number, requisition_no from wo_non_order_info_mst where entry_form in(146,147)", "wo_number", "requisition_no");

	$user_name_arr = return_library_array("select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name", "id", "user_full_name");
	$lc_arr = return_library_array("select id, lc_number from  com_btb_lc_master_details", "id", "lc_number");

	$sql = " select id, recv_number, receive_basis, receive_purpose, booking_id, loan_party, gate_entry_no, receive_date, challan_no, challan_date, location_id, store_id, supplier_id, lc_no, currency_id, exchange_rate, source,supplier_referance,pay_mode,store_sl_no,rcvd_book_no, addi_challan_date,bill_no, bill_date, purchaser_name, carried_by, qc_check_by, receive_by,gate_entry_by,gate_entry_date,addi_rcvd_date, remarks from inv_receive_master where id='$data[1]'";
	//echo $sql;die;
	$dataArray = sql_select($sql);
	$rcv_basis = $dataArray[0][csf('receive_basis')];
	if ($dataArray[0][csf('receive_basis')] == 2 || $dataArray[0][csf('receive_basis')] == 1 || $dataArray[0][csf('receive_basis')] == 7) {

		if ($dataArray[0][csf('receive_basis')] == 2) // Wo
		{
			$wo_sql = sql_select("select a.id,a.wo_number,a.wo_date,a.requisition_no as requ_id,b.item_id,sum(b.supplier_order_quantity) as wo_qnty from  wo_non_order_info_mst  a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id='" . $dataArray[0][csf('booking_id')] . "' and a.status_active=1 and b.status_active=1 group by a.id,a.wo_number,a.requisition_no ,b.item_id,a.wo_date");
			foreach ($wo_sql as $row) {
				$wo_library[$row[csf("id")]]["wo_number"] = $row[csf("wo_number")];
				$wo_library[$row[csf("id")]]["wo_date"] = $row[csf("wo_date")];
				$wo_library_prod[$row[csf("id")]][$row[csf("item_id")]] = $row[csf("wo_qnty")];
				$requsition_id_arr[$row[csf("wo_number")]] = $row[csf("requ_id")];
			}
		} else if ($dataArray[0][csf('receive_basis')] == 1) // Pi
		{
			$sql_pi = sql_select("select a.id as pi_id, a.pi_number,b.work_order_no, b.item_prod_id as item_id , sum(b.quantity) as quantity from com_pi_master_details a , com_pi_item_details b where a.id=b.pi_id  and a.id='" . $dataArray[0][csf('booking_id')] . "' group by a.id, a.pi_number,b.work_order_no,b.item_prod_id");
			foreach ($sql_pi as $row) {
				$pi_library[$row[csf("pi_id")]] = $row[csf("pi_number")];
				$wo_library_prod[$row[csf("pi_id")]][$row[csf("item_id")]] += $row[csf("quantity")];

				$pi_wo_no_library[$row[csf("pi_number")]] = $row[csf("work_order_no")];
			}
		} else // Req.
		{
			$sql_req = sql_select("select a.id as req_id, a.requ_no,a.requisition_date,a.division_id,a.department_id, b.product_id as item_id , sum(b.quantity) as quantity from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id  and a.id='" . $dataArray[0][csf('booking_id')] . "' group by a.id,a.requ_no,a.division_id,a.department_id,b.product_id,a.requisition_date");
			foreach ($sql_req as $row) {
				$requisition_library[$row[csf("req_id")]]["requ_no"] = $row[csf("requ_no")];
				$requisition_library[$row[csf("req_id")]]["requisition_date"] = $row[csf("requisition_date")];
				$wo_library_prod[$row[csf("req_id")]][$row[csf("item_id")]] = $row[csf("quantity")];

				$division_library[$row[csf("requ_no")]] = $row[csf("division_id")];
				$department_library[$row[csf("requ_no")]] = $row[csf("department_id")];
			}
		}


		$order_prev_sql = sql_select("select a.booking_id,b.prod_id,sum(b.order_qnty) as wo_prev_qnty from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.booking_id='" . $dataArray[0][csf('booking_id')] . "' and a.id !='" . $dataArray[0][csf('id')] . "'  and a.status_active=1 and b.status_active=1 group by a.booking_id,b.prod_id");
		foreach ($order_prev_sql as $row) {
			$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]] = $row[csf("wo_prev_qnty")];
		}
	}

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

	$supplier_res = sql_select("select id,supplier_name,address_1 from  lib_supplier");
	foreach ($supplier_res as $val) {
		$supplier_library[$val[csf("id")]]["supplier_name"] = $val[csf("supplier_name")];
		$supplier_library[$val[csf("id")]]["supplier_address"] = $val[csf("address_1")];
	}


	$prod_min_max_rate_res = sql_select("select prod_id, max(order_rate) max_order_rate,max(transaction_date) keep (dense_rank first order by order_rate desc) max_order_date,
	min(order_rate)min_order_rate,min(transaction_date) keep (dense_rank first order by order_rate asc) min_order_date
	from inv_transaction where transaction_type=1 and status_active = 1
	group by prod_id");

	foreach ($prod_min_max_rate_res as $val) {
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["max_order_rate"] = $val[csf("max_order_rate")];
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["max_order_date"] = $val[csf("max_order_date")];
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["min_order_rate"] = $val[csf("min_order_rate")];
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["min_order_date"] = $val[csf("min_order_date")];
	}

	//=================Last Rate============>>>>>================

	//$last_rate_res = sql_select("select prod_id,order_rate,transaction_date, mst_id from inv_transaction where transaction_type=1 and status_active = 1 and transaction_date <= '".$dataArray[0][csf('receive_date')]."' and mst_id < ".$dataArray[0][csf('id')]." order by mst_id desc");

	$last_rate_res = sql_select("select a.prod_id,a.order_rate,a.transaction_date, a.mst_id, b.recv_number
		from inv_transaction a, inv_receive_master b
		where a.mst_id = b.id and a.transaction_type=1 and a.status_active = 1 
		and a.transaction_date <= '" . $dataArray[0][csf('receive_date')] . "' and a.mst_id < " . $dataArray[0][csf('id')] . " 
		 order by a.mst_id desc");


	$prodDupliChkArr = array();
	foreach ($last_rate_res as $value) {
		if ($prodDupliChkArr[$value[csf("prod_id")]] == "") {
			$prodDupliChkArr[$value[csf("prod_id")]] = $value[csf("prod_id")];
			$last_rate_arr[$value[csf("prod_id")]]["last_rate"] = $value[csf("order_rate")];
			$last_rate_arr[$value[csf("prod_id")]]["trans_date"] = $value[csf("transaction_date")];
			$last_rate_arr[$value[csf("prod_id")]]["recv_number"] = $value[csf("recv_number")];
		}
	}
	unset($last_rate_res);

	//=======================================<<<<================

	$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$item_name_arr = return_library_array("select id, item_name from  lib_item_group", "id", "item_name");

	?>
	<style type="text/css">
		#top_table tr td {
			vertical-align: top;
		}
	</style>
	<div style="width:1170px;">
		<table width="1150" cellspacing="1" align="right" id="top_table">
			<tr>
				<td width="100" style="font-size:xx-large" rowspan="2">
					<?
					$cbo_company_name = $data[0];
					$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$cbo_company_name'", "image_location");
					?>
					<img src='../../../<? echo $image_location; ?>' height='40' align="left" style="width: 100px;" />
				</td>
				<td colspan="8" style="font-size:xx-large; justify-content: center;text-align: center;">
					<strong><? echo $company_library[$data[0]]; ?></strong><br>
					<span style="font-size:14px">
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
							Province No: <? echo $result['province']; ?>
							Country: <? echo $country_arr[$result['country_id']]; ?><br>
							Email Address: <? echo $result['email']; ?>
							Website No: <? echo $result['website'];
									}
										?>
					</span>
				</td>
				<td width="200">

				</td>
			</tr>
			<tr>
				<td colspan="8" align="center" style="font-size:x-large"><strong><u>Material Receiving Report</u></strong></td>
			</tr>
			<tr>
				<td width="150"><strong>MRR Number</strong></td>
				<td width="180">:<? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="100"><strong>Receive No</strong></td>
				<td width="130">:<? echo $dataArray[0][csf('rcvd_book_no')]; ?></td>
				<td width="100"><strong>Receive Basis</strong></td>
				<td width="220">:<? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?>&nbsp;&nbsp;</td>
				<td width="120"><strong>Purchase By</strong></td>
				<td width="180">:<? echo $user_name_arr[$dataArray[0][csf('purchaser_name')]]; ?></td>

			</tr>
			<tr>
				<td><strong>Mrr Date</strong></td>
				<td>:<? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
				<td><strong>Receive Date</strong></td>
				<td>:<? echo change_date_format($dataArray[0][csf('addi_rcvd_date')]); ?></td>
				<td><strong>L/C No</strong></td>
				<td>:<? echo $lc_arr[$dataArray[0][csf('lc_no')]]; ?></td>
				<td><strong>Delivered By</strong></td>
				<td>:<? echo $user_name_arr[$dataArray[0][csf('carried_by')]]; ?></td>
			</tr>
			<tr>
				<td><strong>WO/PI/Req.No</strong></td>
				<td>:
					<?
					if ($dataArray[0][csf('receive_basis')] == 1) // PI
					{
						echo $pi_library[$dataArray[0][csf('booking_id')]];
					} else if ($dataArray[0][csf('receive_basis')] == 2) // WO
					{
						echo $wo_library[$dataArray[0][csf('booking_id')]]["wo_number"];
					} else  if ($dataArray[0][csf('receive_basis')] == 7) // Req.
					{
						echo $requisition_library[$dataArray[0][csf('booking_id')]]["requ_no"];
					} else {
						echo "Independent";
					}
					?></td>
				<td><strong>Challan No</strong></td>
				<td>:<? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Challan Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('challan_date')]); ?></td>
				<td><strong>Supplier</strong></td>
				<td>:<? echo $supplier_library[$dataArray[0][csf('supplier_id')]]["supplier_name"]; ?></td>

			</tr>
			<tr>
				<td><strong>WO/PI/Req.No Date</strong></td>
				<td>:
					<?
					if ($dataArray[0][csf('receive_basis')] == 1) // PI
					{
						echo $pi_library[$dataArray[0][csf('booking_id')]];
					} else if ($dataArray[0][csf('receive_basis')] == 2) // WO
					{
						echo $wo_library[$dataArray[0][csf('booking_id')]]["wo_date"];
					} else  if ($dataArray[0][csf('receive_basis')] == 7) // Req.
					{
						echo $requisition_library[$dataArray[0][csf('booking_id')]]["requisition_date"];
					} else {
						echo "";
					}
					?>
				</td>
				<td><strong>Challan Date</strong></td>
				<td>:<? echo change_date_format($dataArray[0][csf('addi_challan_date')]); ?></td>
				<td><strong>Supplier Address</strong></td>
				<td>
					<p>:<? echo $supplier_library[$dataArray[0][csf('supplier_id')]]["supplier_address"]; ?></p>
				</td>
				<td><strong>Received By </strong></td>
				<td>:<? echo $user_name_arr[$dataArray[0][csf('receive_by')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Gate Entry No</strong></td>
				<td>:<? echo $dataArray[0][csf('gate_entry_no')]; ?></td>
				<td><strong>Bill No</strong></td>
				<td>:<? echo $dataArray[0][csf('bill_no')]; ?></td>
				<td><strong>Store Name</strong></td>
				<td>:<strong><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></strong></td>
				<td><strong>Gate Entry By </strong></td>
				<td>:<? echo $user_name_arr[$dataArray[0][csf('gate_entry_by')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Gate Entry Date</strong></td>
				<td>:<? echo change_date_format($dataArray[0][csf('gate_entry_date')]); ?></td>
				<td><strong>Bill Date</strong></td>
				<td>:<? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
				<td><strong>Store Sl No</strong></td>
				<td>:<strong><? echo $dataArray[0][csf('store_sl_no')]; ?></strong></td>
				<td><strong>Prepared By</strong></td>
				<td>:<? echo $user_name_arr[$user_id]; ?></td>
			</tr>
			<tr>
				<td><strong>Pay Mode</strong></td>
				<td>:<?
						if ($dataArray[0][csf('receive_basis')] == 1 && $dataArray[0][csf('pay_mode')] == 0) {
							echo $pay_mode[2]; //PI basis pay mode will be import
						} else {
							echo $pay_mode[$dataArray[0][csf('pay_mode')]];
						}

						?>
				</td>
				<td><strong>Currency</strong></td>
				<td>:<? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
				<td><strong>Source</strong></td>
				<td>:<? echo $source[$dataArray[0][csf('source')]]; ?></td>
				<td><strong>QC Checked By</strong></td>
				<td>:<? echo $user_name_arr[$dataArray[0][csf('qc_check_by')]]; ?></td>
			</tr>
			<tr>
				<td rowspan="2" valign="top"><strong>Barcode</strong></td>:
				<td rowspan="2" colspan="3" valign="top" id="bar_code"></td>
				<td><strong>Exchange Rate</strong></td>
				<td>:<? echo $dataArray[0][csf('exchange_rate')]; ?></td>
				<td><strong>Remark</strong></td>
				<td>:<? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
			<tr>
				<td colspan="6"></td>
			</tr>
		</table>
		<?
		if ($db_type == 2) {
			//   $sql_dtls = "SELECT a.id as recv_id, a.booking_id, b.item_category,c.id as product_id,
			//   b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks, b.batch_lot,
			//   (c.sub_group_name||' '|| c.item_description || ' '|| c.item_size) as product_name_details,c.item_number,c.item_group_id, c.item_description,c.item_code, c.brand_name, c.origin, b.transaction_date
			//   from inv_receive_master a,  product_details_master c, inv_transaction b
			//   left join wo_non_order_info_dtls d on  b.receive_basis=2 and b.pi_wo_batch_no= d.mst_id and b.prod_id= d.item_id
			//   where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1 
			//   order by b.id";

			$sql_dtls = "SELECT a.id as recv_id, a.booking_id, b.item_category,c.id as product_id,
		b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, 
		sum(b.order_qnty) as order_qnty, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty,
		b.expire_date, b.batch_lot, b.prod_id, b.remarks, b.batch_lot, c.item_description as product_name_details,c.item_number,c.item_group_id,c.item_code, c.brand_name, c.origin, b.transaction_date
		from inv_receive_master a,  product_details_master c, inv_transaction b
		left join wo_non_order_info_dtls d on  b.receive_basis=2 and b.pi_wo_batch_no= d.mst_id and b.prod_id= d.item_id
		where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1 
		group by a.id , a.booking_id, b.item_category, c.id, b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, c.item_number, c.item_group_id, c.item_description, c.item_code, c.brand_name, c.origin, b.transaction_date,
		b.expire_date, b.batch_lot, b.prod_id, b.remarks, b.batch_lot
		order by b.id";
		} else {
			$sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category,c.id as product_id,
	  b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks, b.batch_lot,
	 concat(c.sub_group_name,c.item_description, c.item_size) as product_name_details, c.item_number, c.item_group_id, c.item_description,c.item_code, c.brand_name, c.origin, b.transaction_date
	  from inv_receive_master a, inv_transaction b, product_details_master c ,wo_non_order_info_dtls d
	  where d.item_id=c.id and a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1
	  order by b.id";
		}

		//echo $sql_dtls; die;

		$sql_result = sql_select($sql_dtls);
		$i = 1;
		?>
		<br>
		<br>
		<div style="width:100%;margin-top:20px;">
			<table align="left" cellspacing="0" width="1300" border="1" rules="all" class="rpt_table" style="margin-left:20px;">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="70" rowspan="2">Item Category</th>
						<th width="110" rowspan="2">Item Group</th>
						<th width="160" rowspan="2">Item Description</th>
						<th width="100" rowspan="2">Brand</th>
						<th width="50" rowspan="2">Origin</th>
						<th width="50" rowspan="2">Lot</th>
						<th width="40" rowspan="2">UOM</th>
						<th width="70" rowspan="2">WO/PI Qnty.</th>
						<th width="70" rowspan="2">Previous Recv Qnty</th>
						<th width="70" rowspan="2">Today Recv. Qnty.</th>
						<th width="80" rowspan="2">WO/PI Qnty Bal.</th>
						<th width="240" colspan="3">Unit Price and Date</th>
						<th width="100" rowspan="2">Rate</th>
						<th width="100" rowspan="2">Amount</th>
						<th width="200" rowspan="2">Comments</th>
					</tr>
					<tr>
						<th width="80">Maximum</th>
						<th width="80">Minimum</th>
						<th width="80">Last</th>
					</tr>
				</thead>
				<tbody>
					<?
					foreach ($sql_result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$order_qnty = $row[csf('order_qnty')];
						$order_qnty_sum += $order_qnty;

						$order_amount = $row[csf('order_amount')];
						$order_amount_sum += $order_amount;

						$balance_qnty = ($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]] - ($row[csf('order_qnty')] + $order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]));
						$balance_qnty_sum += $balance_qnty;

						$desc = $row[csf('item_description')];

						if ($row[csf('item_size')] != "") {
							$desc .= ", " . $row[csf('item_size')];
						}
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td><? echo $i; ?></td>
							<td><? echo $item_category[$row[csf('item_category')]]; ?></td>
							<td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
							<td align="center"><? echo $row[csf('product_name_details')]; ?></td>
							<td align="center"><? echo $row[csf('brand_name')]; ?></td>
							<td align="center"><? echo $countryShortNameArr[$row[csf('origin')]]; ?></td>
							<td align="center"><? echo $row[csf('batch_lot')]; ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
							<td align="right"><? echo number_format($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]], 2);
												$tot_ord_qnty += $wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]; ?></td>
							<td align="right"><? echo number_format($order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]], 2);
												$tot_prev_qnty += $order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]; ?></td>
							<td align="right"><? echo number_format($row[csf('order_qnty')], 2, '.', ','); ?></td>
							<td align="right"><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7) echo number_format($balance_qnty, 2, '.', ','); ?></td>

							<td align="right">
								<? echo  number_format($prod_min_max_rate_arr[$row[csf("prod_id")]]["max_order_rate"], 2) . "<hr/ style='border:1px solid black'>" . $prod_min_max_rate_arr[$row[csf("prod_id")]]["max_order_date"]; ?>
							</td>
							<td align="right">
								<? echo  number_format($prod_min_max_rate_arr[$row[csf("prod_id")]]["min_order_rate"], 2) . "<hr/ style='border:1px solid black'>" . $prod_min_max_rate_arr[$row[csf("prod_id")]]["min_order_date"]; ?>
							</td>
							<td align="right" title="mrr no = <? echo $last_rate_arr[$row[csf("prod_id")]]["recv_number"]; ?>">
								<?
								//echo  number_format($row[csf("order_rate")],2)."<hr/ style='border:1px solid black'><span>".$row[csf("transaction_date")]."</span>"; 
								echo  number_format($last_rate_arr[$row[csf("prod_id")]]["last_rate"], 2) . "<hr/ style='border:1px solid black'><span>" . $last_rate_arr[$row[csf("prod_id")]]["trans_date"] . "</span>";
								?>
							</td>

							<td align="right"><? echo number_format($row[csf('order_rate')], 4, '.', ','); ?></td>
							<td align="right"><? echo number_format($row[csf('order_amount')], 2, '.', ',');  ?></td>
							<td align="left"><? echo $row[csf('remarks')];  ?></td>
						</tr>
					<?
						$i++;
					}
					?>
					<tr bgcolor="#CCCCCC">
						<td align="right" colspan="8">Total</td>
						<td align="right"><? echo number_format($tot_ord_qnty, 2, '.', ','); ?></td>
						<td align="right"><? echo number_format($tot_prev_qnty, 2, '.', ','); ?></td>
						<td align="right"><? echo number_format($order_qnty_sum, 2, '.', ','); ?></td>
						<td align="right"><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7) echo number_format($balance_qnty_sum, 2, '.', ','); ?></td>
						<!-- <td align="right">&nbsp;</td> -->
						<td align="right" colspan="3">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right"><? echo number_format($order_amount_sum, 2, '.', ','); ?></td>
						<td align="right">&nbsp;</td>
					</tr>
				</tbody>
			</table>
			<table>
				<tr>
					<td colspan="15">
						<h3 align="center" style="margin-left:150px;"> In Words : &nbsp;<? echo number_to_words(number_format($order_amount_sum, 2, '.', ',')) . "( " . $currency[$dataArray[0][csf('currency_id')]] . " )"; ?></h3>
					</td>
				</tr>
			</table>
			<br>
			<?
			echo signature_table(11, $data[0], "1300px", '1', 40, $inserted_by);
			?>
		</div>
	</div>

	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			// alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

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
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#bar_code").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $dataArray[0][csf('recv_number')]; ?>');
	</script>
<?
	exit();
}


if ($action == "general_item_receive_print_new") 
{
	extract($_REQUEST);
	$data = explode('__', $data);
	$variable_inventory_ref = explode("**", $data[4]);
	$rate_hide_inventory = $variable_inventory_ref[2];
	echo load_html_head_contents("General Item Receive Info", "../../../", 1, 1, $unicode);
	$division_name_arr = return_library_array("select id, division_name from   lib_division", "id", "division_name");
	$department_name_arr = return_library_array("select id, department_name from   lib_department", "id", "department_name");

	$req_div_name_arr = return_library_array("select id, division_id from inv_purchase_requisition_mst", "id", "division_id");
	$req_department_name_arr = return_library_array("select id, department_id from inv_purchase_requisition_mst", "id", "department_id");
	$req_no_arr = return_library_array("select wo_number, requisition_no from wo_non_order_info_mst where entry_form in(146,147)", "wo_number", "requisition_no");

	$sql = " select id, recv_number, receive_basis, receive_purpose, booking_id, loan_party, gate_entry_no, receive_date, challan_no, challan_date, location_id, store_id, supplier_id, lc_no, currency_id, boe_mushak_challan_no, boe_mushak_challan_date, exchange_rate, source,supplier_referance,pay_mode,INSERTED_BY from inv_receive_master where id='$data[1]'";
	//echo $sql;die;
	$dataArray = sql_select($sql);
	$rcv_basis = $dataArray[0][csf('receive_basis')];
	$inserted_by = $dataArray[0]['INSERTED_BY'];
	$lc_no = $dataArray[0]['LC_NO'];
	$mrr_no =  $dataArray[0][csf('recv_number')];


	if ($dataArray[0][csf('receive_basis')] == 2 || $dataArray[0][csf('receive_basis')] == 1 || $dataArray[0][csf('receive_basis')] == 7) {

		if ($dataArray[0][csf('receive_basis')] == 2) // Wo Basis
		{
			$wo_sql = sql_select("select a.id, a.wo_number, a.requisition_no as requ_id, b.item_id, sum(b.supplier_order_quantity) as wo_qnty 
			from wo_non_order_info_mst a, wo_non_order_info_dtls b 
			where a.id=b.mst_id and a.id='" . $dataArray[0][csf('booking_id')] . "' and b.status_active=1 and b.is_deleted=0 
			group by a.id, a.wo_number, a.requisition_no, b.item_id");
			foreach ($wo_sql as $row) {
				$wo_library[$row[csf("id")]] = $row[csf("wo_number")];
				$wo_library_prod[$row[csf("id")]][$row[csf("item_id")]] = $row[csf("wo_qnty")];

				$requsition_id_arr[$row[csf("wo_number")]] = $row[csf("requ_id")];
			}
		} else if ($dataArray[0][csf('receive_basis')] == 1) //Pi Basis
		{
			$sql_pi = sql_select("select a.id as pi_id, a.pi_number, b.work_order_no, b.item_prod_id as item_id, sum(b.quantity) as quantity 
			from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id  and a.id='" . $dataArray[0][csf('booking_id')] . "' and b.status_active=1 and b.is_deleted=0 
			group by a.id, a.pi_number, b.work_order_no, b.item_prod_id");
			foreach ($sql_pi as $row) {
				$pi_library[$row[csf("pi_id")]] = $row[csf("pi_number")];
				$wo_library_prod[$row[csf("pi_id")]][$row[csf("item_id")]] = $row[csf("quantity")];

				$pi_wo_no_library[$row[csf("pi_number")]] = $row[csf("work_order_no")];
			}
		} else {
			$sql_req = sql_select("select a.id as req_id, a.requ_no,a.division_id,a.department_id, b.product_id as item_id , sum(b.quantity) as quantity 
			from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
			where a.id=b.mst_id  and a.id='" . $dataArray[0][csf('booking_id')] . "' and b.status_active=1 and b.is_deleted=0 
			group by a.id, a.requ_no, a.division_id, a.department_id, b.product_id");
			foreach ($sql_req as $row) {
				$requisition_library[$row[csf("req_id")]] = $row[csf("requ_no")];
				$wo_library_prod[$row[csf("req_id")]][$row[csf("item_id")]] = $row[csf("quantity")];

				$division_library[$row[csf("requ_no")]] = $row[csf("division_id")];
				$department_library[$row[csf("requ_no")]] = $row[csf("department_id")];
			}
		}


		$order_prev_sql = sql_select("select a.booking_id,b.prod_id,sum(b.order_qnty) as wo_prev_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.booking_id='" . $dataArray[0][csf('booking_id')] . "' and a.id !='" . $dataArray[0][csf('id')] . "'  and a.status_active=1 and b.status_active=1 and b.status_active=1 and b.status_active=1 group by a.booking_id,b.prod_id");
		foreach ($order_prev_sql as $row) {
			$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]] = $row[csf("wo_prev_qnty")];
		}
	}

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$item_name_arr = return_library_array("select id, item_name from  lib_item_group", "id", "item_name");
	$loan_type = array('1' => "Loan");
	$lcNumber = return_field_value("lc_number", "com_btb_lc_master_details", "id='" . $lc_no . "'");
	// echo "SELECT  a.BILL_NO, a.BILL_DATE from inv_bill_processing_mst a, inv_bill_processing_dtls b
	// where a.id=b.mst_id and b.receive_id='$data[1]' group by a.BILL_NO, a.BILL_DATE";

	$sql_bill_arr = sql_select("SELECT a.BILL_NO, a.BILL_DATE from inv_bill_processing_mst a, inv_bill_processing_dtls b
	where a.id=b.mst_id and b.receive_id='$data[1]' group by a.BILL_NO, a.BILL_DATE");



	

	//print2 button qr code of mrir number
	
	$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR;
    $PNG_WEB_DIR = 'qrcode_image/';

	foreach (glob($PNG_WEB_DIR."*.png") as $filename) {
		@unlink($filename);
	}

	if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);

	$filename = $PNG_TEMP_DIR.'test.png';
	$errorCorrectionLevel = 'L';
	$matrixPointSize = 4;

    include "../../../ext_resource/phpqrcode/qrlib.php";

	$filename = $PNG_TEMP_DIR.md5($mrr_no).'.png';
	QRcode::png($mrr_no, $filename, $errorCorrectionLevel, $matrixPointSize, 2);


	?>
	<div style="width:970px;">
		<table width="970" cellspacing="0" align="right">
			<tr>
				
				<td width="100" style="font-size:xx-large" rowspan="2">
					<?
					$cbo_company_name = $data[0];
					$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$cbo_company_name'", "image_location");
					?>
					<img src='../../../<? echo $image_location; ?>' height='40' align="left" style="width: 100px;" />
				</td>
				

				</td>
			<td colspan="1" align="center" style="font-size:22px"><img src="<? echo $PNG_WEB_DIR . basename($filename); ?>" height="100" width="">
			</td>



				<td colspan="6" style="font-size:xx-large; justify-content: center;text-align: center;padding-left: 90px;">
					<strong><? echo $company_library[$data[0]]; ?></strong><br>
					<span style="font-size:14px">
						<?
						$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						foreach ($nameArray as $result) {
						?>
							
							 <? 
							 echo $result['PLOT_NO']? "Plot: ".$result['PLOT_NO'].",":""; ?>
							 <? echo $result['LEVEL_NO']? $result['LEVEL_NO'].",":""; ?>
							 <? echo $result['ROAD_NO']? $result['ROAD_NO'].",":""; ?>
							 <? echo $result['BLOCK_NO']?$result['BLOCK_NO'].",":""; ?>
							 <? echo $result['CITY']?"City: ".$result['CITY'].",":""; ?>
							 <? echo $result['ZIP_CODE']?$result['ZIP_CODE'].",":""; ?>
							 <? echo $result['PROVINCE']?$result['PROVINCE'].",":""; ?>
							 <? echo $country_arr[$result['COUNTRY_ID']];
							 
									}
										?>
					</span>
				</td>
				<td width="200">

				</td>
				
			<tr>
				<td style="font-size:xx-large;justify-content: center;text-align: center;" colspan="6">
					<span style="font-size:18px;justify-content: center;text-align: center;">
						<?
						$address = "";
						if (count($address_sql)) {
							$address = $address_sql[0][csf('address')];
						}
						echo $address;
						?>
					</span>
			
				<td width="200">
					<p style="font-size: 22px;">
						<?php
						$location = '';
						if (count($sql_location)) {
							$location = $location_arr[$sql_location[0][csf('location_id')]];
						} ?>
						Location : <?php echo $location; ?>
					</p>
				</td>








			</tr>
			</tr>
			<tr>
				<td colspan="8" align="center" style="font-size:x-large"><strong><u>Material Receiving & Inspection Report</u></strong></td>
			</tr>
			<tr>
				<td width="100"><strong>MRIR Number:</strong></td>
				<td width="220"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="100"><strong>Receive Basis :</strong></td>
				<td width="180"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<td width="100"><strong>Receive Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Challan No:</strong></td>
				<td><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Challan Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('challan_date')]); ?></td>
				<td><strong>L/C No:</strong></td>
				<td><? echo  $lcNumber; //$dataArray[0][csf('lc_no')]; 
					?></td>

			</tr>
			<tr>
				<td><strong>Supplier:</strong></td>
				<td><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
				<td><strong>Currency:</strong></td>
				<td><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
				<td><strong>Exchange Rate:</strong></td>
				<td><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
			</tr>
			<tr>
				<td><strong>Ref:</strong></td>
				<td><? echo $dataArray[0][csf('supplier_referance')]; ?></td>
				<td><strong>Source:</strong></td>
				<td><? echo $source[$dataArray[0][csf('source')]]; ?></td>
				<td><strong>Pay Mode:</strong></td>
				<td><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>

			</tr>
			<tr>
				<td><strong>WO/PI:</strong></td>
				<td>
					<?
					if ($dataArray[0][csf('receive_basis')] == 1) //Pi Basis
					{
						echo $pi_library[$dataArray[0][csf('booking_id')]];
					} else if ($dataArray[0][csf('receive_basis')] == 2) //Wo Basis
					{
						echo $wo_library[$dataArray[0][csf('booking_id')]];
					} else  if ($dataArray[0][csf('receive_basis')] == 7) // Req. Basis
					{
						echo $requisition_library[$dataArray[0][csf('booking_id')]];
					} else {
						echo "";
					}
					?></td>
				<td><strong>Division: </strong></td>
				<td>

					<?
					if ($dataArray[0][csf('receive_basis')] == 1) //PI
					{
						echo $division_name_arr[$req_div_name_arr[$req_no_arr[$pi_wo_no_library[$pi_library[$dataArray[0][csf('booking_id')]]]]]];
					} else if ($dataArray[0][csf('receive_basis')] == 2) //Wo
					{
						echo $division_name_arr[$req_div_name_arr[$requsition_id_arr[$wo_library[$dataArray[0][csf('booking_id')]]]]];
						//echo $division_name_arr[$division_library[$requisition_library[$dataArray[0][csf('booking_id')]]]];
					} else if ($dataArray[0][csf('receive_basis')] == 7) // Req..
					{
						echo $division_name_arr[$division_library[$requisition_library[$dataArray[0][csf('booking_id')]]]];
					} else {
						echo "";
					}
					?>

				</td>
				<td><strong>Department:</strong></td>
				<td><?
					if ($dataArray[0][csf('receive_basis')] == 1) //PI
					{
						echo $department_name_arr[$req_department_name_arr[$req_no_arr[$pi_wo_no_library[$pi_library[$dataArray[0][csf('booking_id')]]]]]];
					} else  if ($dataArray[0][csf('receive_basis')] == 2) //WO
					{
						echo $department_name_arr[$req_department_name_arr[$requsition_id_arr[$wo_library[$dataArray[0][csf('booking_id')]]]]];
					} else  if ($dataArray[0][csf('receive_basis')] == 7) // Req..
					{
						echo $department_name_arr[$department_library[$requisition_library[$dataArray[0][csf('booking_id')]]]];
					} else {
						echo "";
					}
					?>
				<td><strong>Receive Purpose: </strong></td>
				<td><? echo $loan_type[$dataArray[0][csf('receive_purpose')]]; ?></td>
				</td>
			</tr>
			<tr>
				<td><strong>Store Name:</strong></td>
				<td><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
				<td><strong>BOE/Mushak Challan No:</strong></td>
				<td><? echo $dataArray[0][csf('boe_mushak_challan_no')]; ?></td>
				<td><strong>BOE/Mushak Challan Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('boe_mushak_challan_date')]); ?></td>
			</tr>
			<tr>
				<td><strong> Bill No:</strong></td>
				<td><? echo $sql_bill_arr[0]['BILL_NO']; ?></td>
				<td><strong> Bill Date:</strong></td>
				<td><? echo change_date_format($sql_bill_arr[0]['BILL_DATE']); ?></td>
			</tr>
		</table>
		<?
		if ($db_type == 2) {
			$sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category,
		  b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks, b.batch_lot,
		  (c.sub_group_name||' '|| c.item_description || ' '|| c.item_size) as product_name_details, c.item_group_id, c.item_description,c.item_code
		  from inv_receive_master a, inv_transaction b, product_details_master c 
		  where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1
		  order by b.id";
		} else {
			$sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category,
		  b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks, b.batch_lot,
		 concat(c.sub_group_name,c.item_description, c.item_size) as product_name_details, c.item_group_id, c.item_description,c.item_code
		  from inv_receive_master a, inv_transaction b, product_details_master c 
		  where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1";
		}

		$sql_result = sql_select($sql_dtls);
		$i = 1;

		?>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="1010" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="70">Item Category</th>
					<th width="110">Item Group</th>
					<th width="40">Item Code</th>
					<th width="160">Item Description</th>
					<th width="50">Lot</th>
					<th width="40">UOM</th>
					<th width="70">PI/WO Qnty.</th>
					<th width="70">Previous Recv Qnty</th>
					<th width="70">Today Recv. Qnty.</th>
					<?
					if ($rate_hide_inventory != 1) {
					?>
						<th width="50">Rate</th>
						<th width="80">Amount</th>
					<?
					}
					?>
					<th width="80">PI/WO Qnty Bal.</th>
					<th>Comments</th>
				</thead>
				<?
				foreach ($sql_result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					$order_qnty = $row[csf('order_qnty')];
					$order_qnty_sum += $order_qnty;

					$order_amount = $row[csf('order_amount')];
					$order_amount_sum += $order_amount;

					$balance_qnty = ($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]] - ($row[csf('order_qnty')] + $order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]));
					$balance_qnty_sum += $balance_qnty;

					$desc = $row[csf('item_description')];

					if ($row[csf('item_size')] != "") {
						$desc .= ", " . $row[csf('item_size')];
					}
				?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $i; ?></td>
						<td><? echo $item_category[$row[csf('item_category')]]; ?></td>
						<td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
						<td><? echo $row[csf('item_code')]; ?></td>
						<td align="center"><? echo $row[csf('product_name_details')]; ?></td>
						<td align="center"><? echo $row[csf('batch_lot')]; ?></td>
						<td align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
						<td align="right"><? echo number_format($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]], 2);
											$tot_ord_qnty += $wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]; ?></td>
						<td align="right"><? echo number_format($order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]], 2);
											$tot_prev_qnty += $order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]; ?></td>
						<td align="right"><? echo number_format($row[csf('order_qnty')], 2); ?></td>
						<?
						if ($rate_hide_inventory != 1) {
						?>
							<td align="right"><? echo number_format($row[csf('order_rate')], 2); ?></td>
							<td align="right"><? echo number_format($row[csf('order_amount')], 2);  ?></td>
						<?
						}
						?>
						<td align="right"><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7)  echo  number_format($balance_qnty, 2); ?></td>
						<td><? echo $row[csf('remarks')]; ?></td>
					</tr>
				<?
					$i++;
				}
				?>
				<tr>

					<td><strong>Total:</strong></td>
					<td><strong>&nbsp;&nbsp;</strong></td>
					<td><strong>&nbsp;&nbsp;</strong></td>
					<td><strong>&nbsp;&nbsp;</strong></td>
					<td><strong>&nbsp;&nbsp;</strong></td>
					<td colspan="2" align="center"><strong>Total:</strong>
					</td>
					<td><strong>&nbsp;&nbsp;</strong></td>
					<td align="right"><strong><? echo number_format($tot_ord_qnty, 2); ?></strong></td>
					<td align="right"><strong><? echo number_format($tot_prev_qnty, 2); ?></strong></td>
					<td align="right"><strong><? echo number_format($order_qnty_sum, 2); ?></strong></td>
					<?
					if ($rate_hide_inventory != 1) {
					?>
						<td><strong>&nbsp;&nbsp;</strong></td>
						<td align="right"><strong><? echo number_format($order_amount_sum, 2); ?></strong></td>
					<?
					}
					?>
					<td align="right"><strong><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7) echo number_format($balance_qnty_sum, 2); ?></strong></td>
					<td><strong>&nbsp;&nbsp;</strong></td>
				</tr>
			</table>

			<table width="1010">
				<tr>
					<td align="center" style="padding-top: 30px;">
						<strong>In Words :<? echo number_to_words($order_amount_sum); ?></strong>
					</td>
				</tr>
			</table>



			<br>
			<?
			echo signature_table(11, $data[0], "1010px", '1', 40, $inserted_by);
			?>
		</div>
	<?
	exit();
}

if ($action == "general_item_receive_print_5") 
{
	extract($_REQUEST);
	$data = explode('__', $data);
	$company_id = $data[0];
	$mrr_id = $data[1];
	$report_title = $data[2];
	$receive_basis = $data[3];
	$variable_string_inventory = $data[4];

	//echo load_html_head_contents("General Item Receive Info","../../../", 1, 1, $unicode);

	$sql_supplier = sql_select("select id, supplier_name, address_1, supplier_ref from lib_supplier where status_active=1 and is_deleted=0");
	foreach ($sql_supplier as $val) {
		$supplier_library[$val[csf("id")]]["supplier_name"] = $val[csf("supplier_name")];
		$supplier_library[$val[csf("id")]]["supplier_address"] = $val[csf("address_1")];
		$supplier_library[$val[csf("id")]]["supplier_ref"] = $val[csf("supplier_ref")];
	}

	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$item_name_arr = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
	$division_name_arr = return_library_array("select id, division_name from lib_division", "id", "division_name");
	$department_name_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");
	$user_name_arr = return_library_array("select id, user_full_name from user_passwd", "id", "user_full_name");

	$sql_store_location = sql_select("select id, store_name, location_id from lib_store_location where status_active=1 and is_deleted=0");
	foreach ($sql_store_location as $val) {
		$store_location_arr[$val[csf("id")]]['store_name'] = $val[csf("store_name")];
		$store_location_arr[$val[csf("id")]]['location_id'] = $val[csf("location_id")];
	}
	$store_library = return_library_array("select id, store_name, location_id from lib_store_location where status_active=1 and is_deleted=0", "id", "store_name");

	$lc_arr = return_library_array("select id, lc_number from  com_btb_lc_master_details", "id", "lc_number");

	$sql = " select id, recv_number, receive_basis, receive_purpose, booking_id, loan_party, gate_entry_no, receive_date, challan_no, challan_date, location_id, store_id, supplier_id, lc_no, currency_id, exchange_rate, source, supplier_referance, pay_mode, store_sl_no, rcvd_book_no,  bill_no, bill_date, purchaser_name, carried_by, qc_check_by, receive_by, gate_entry_by, gate_entry_date, addi_rcvd_date, remarks, supplier_referance, challan_date, store_id, inserted_by from inv_receive_master where id='$mrr_id'";
	//echo $sql;die;
	$dataArray = sql_select($sql);
	$rcv_basis = $dataArray[0][csf('receive_basis')];
	$booking_id = $dataArray[0][csf('booking_id')];
	$receive_date = $dataArray[0][csf('receive_date')];
	$inserted_by = $dataArray[0][csf('inserted_by')];

	if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7) {
		if ($rcv_basis == 2) // Wo 
		{
			$wo_sql = sql_select("select a.id, a.wo_number, a.wo_date, a.requisition_no as requ_id, b.item_id, sum(b.supplier_order_quantity) as wo_qnty, sum(b.gross_amount) as wo_gross_amount
			from wo_non_order_info_mst a, wo_non_order_info_dtls b 
			where a.id=b.mst_id and a.id=$booking_id and a.status_active=1 and b.status_active=1 
			group by a.id, a.wo_number, a.requisition_no, b.item_id, a.wo_date");
			foreach ($wo_sql as $row) {
				$wo_library[$row[csf("id")]]["wo_number"] = $row[csf("wo_number")];
				$wo_library[$row[csf("id")]]["wo_date"] = $row[csf("wo_date")];
				$wo_library_prod[$row[csf("id")]][$row[csf("item_id")]]["pi_wo_qnty"] = $row[csf("wo_qnty")];
				$wo_library_prod[$row[csf("id")]][$row[csf("item_id")]]["pi_wo_gross_rate"] = $row[csf("wo_gross_amount")] / $row[csf("wo_qnty")];
				$requsition_id .= $row[csf("requ_id")] . ',';
			}
			$requsition_ids = implode(',', array_unique(explode(',', rtrim($requsition_id, ','))));
			if ($requsition_ids != "") {
				$sql_req = sql_select("select id, requ_no from inv_purchase_requisition_mst where id in($requsition_ids) and status_active=1 and is_deleted=0");
				foreach ($sql_req as $row) {
					$requisition_no .= $row[csf("requ_no")] . ',';
				}
				$requisition_nos = rtrim($requisition_no, ',');
			}
		} else if ($rcv_basis == 1) // Pi
		{
			$sql_pi = sql_select("select a.id as pi_id, a.pi_number, b.work_order_no, b.item_prod_id as item_id, sum(b.quantity) as quantity, sum(b.amount) as gross_amount
			from com_pi_master_details a, com_pi_item_details b 
			where a.id=b.pi_id and a.id=$booking_id and b.status_active=1
			group by a.id, a.pi_number, b.work_order_no, b.item_prod_id");
			foreach ($sql_pi as $row) {
				$pi_library[$row[csf("pi_id")]] = $row[csf("pi_number")];
				$pi_wo_no_library[$row[csf("pi_number")]] = $row[csf("work_order_no")];
				$wo_library_prod[$row[csf("pi_id")]][$row[csf("item_id")]]["pi_wo_qnty"] = $row[csf("quantity")];
				$wo_library_prod[$row[csf("pi_id")]][$row[csf("item_id")]]["pi_wo_gross_rate"] = $row[csf("gross_amount")] / $row[csf("quantity")];
			}
		} else // Req.
		{
			$sql_req = sql_select("select a.id as req_id, a.requ_no, a.requisition_date, a.division_id, a.department_id, b.product_id as item_id, sum(b.quantity) as quantity, sum(b.amount) as gross_amount 
			from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
			where a.id=b.mst_id  and a.id=$booking_id and b.status_active=1
			group by a.id, a.requ_no, a.division_id, a.department_id, b.product_id, a.requisition_date");
			foreach ($sql_req as $row) {
				$requisition_library[$row[csf("req_id")]]["requ_no"] = $row[csf("requ_no")];
				$requisition_library[$row[csf("req_id")]]["requisition_date"] = $row[csf("requisition_date")];
				$division_library[$row[csf("requ_no")]] = $row[csf("division_id")];
				$department_library[$row[csf("requ_no")]] = $row[csf("department_id")];

				$wo_library_prod[$row[csf("req_id")]][$row[csf("item_id")]]["pi_wo_qnty"] = $row[csf("quantity")];
				$wo_library_prod[$row[csf("req_id")]][$row[csf("item_id")]]["pi_wo_gross_rate"] = $row[csf("gross_amount")] / $row[csf("quantity")];
			}
		}

		$order_prev_sql = sql_select("select a.booking_id, b.prod_id,sum(b.order_qnty) as wo_prev_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.booking_id=$booking_id and a.id !=$mrr_id  and a.status_active=1 and b.status_active=1 group by a.booking_id,b.prod_id");
		foreach ($order_prev_sql as $row) {
			$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]] = $row[csf("wo_prev_qnty")];
		}
	}

	$prod_min_max_rate_res = sql_select("select prod_id, max(cons_rate) max_order_rate, max(transaction_date) keep (dense_rank first order by cons_rate desc) max_order_date, min(cons_rate) min_order_rate, min(transaction_date) keep (dense_rank first order by cons_rate asc) min_order_date
	from inv_transaction where transaction_type=1 and status_active = 1 
	group by prod_id");

	foreach ($prod_min_max_rate_res as $val) {
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["max_order_rate"] = $val[csf("max_order_rate")];
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["max_order_date"] = $val[csf("max_order_date")];
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["min_order_rate"] = $val[csf("min_order_rate")];
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["min_order_date"] = $val[csf("min_order_date")];
	}


	$last_rate_res = sql_select("select a.prod_id,a.cons_rate as order_rate,a.transaction_date, a.mst_id, b.recv_number
	from inv_transaction a, inv_receive_master b
	where a.mst_id = b.id and a.transaction_type=1 and a.status_active = 1 
	and a.transaction_date <= '$receive_date' and a.mst_id < $mrr_id order by a.mst_id desc");

	$prodDupliChkArr = array();
	foreach ($last_rate_res as $value) {
		if ($prodDupliChkArr[$value[csf("prod_id")]] == "") {
			$prodDupliChkArr[$value[csf("prod_id")]] = $value[csf("prod_id")];
			$last_rate_arr[$value[csf("prod_id")]]["last_rate"] = $value[csf("order_rate")];
			$last_rate_arr[$value[csf("prod_id")]]["trans_date"] = $value[csf("transaction_date")];
			$last_rate_arr[$value[csf("prod_id")]]["recv_number"] = $value[csf("recv_number")];
		}
	}
	unset($last_rate_res);
	//echo $rcv_basis.ghh;die;
	//=======================================<<<<================
	?>
		<style type="text/css">
			#top_table tr td {
				vertical-align: top;
			}
		</style>
		<div style="width:1170px;">
			<table width="1150" cellspacing="1" align="right" id="top_table">
				<tr>
					<?
					$sql_company = sql_select("select id, company_name, plot_no, level_no, road_no, block_no, city, zip_code, country_id, email, website from lib_company where id=$company_id and is_deleted=0 and status_active=1");
					foreach ($sql_company as $row) {
						if ($row[csf('plot_no')] != '') $plot_no = $row[csf('plot_no')] . ', ';
						if ($row[csf('level_no')] != '') $level_no = $row[csf('level_no')] . ', ';
						if ($row[csf('road_no')] != '') $road_no = $row[csf('road_no')] . ', ';
						if ($row[csf('block_no')] != '') $block_no = $row[csf('block_no')] . ', ';
						if ($row[csf('city')] != '') $city = $row[csf('city')] . ', ';
						if ($row[csf('zip_code')] != '') $zip_code = $row[csf('zip_code')] . ', ';
						if ($row[csf('country_id')] != 0) $country = $country_arr[$row[csf('country_id')]];
						if ($row[csf('email')] != '') $company_email = "Email Address:&nbsp;" . $row[csf('email')];
						if ($row[csf('website')] != '') $company_website = "Website No:&nbsp;" . $row[csf('website')];

						$company_address = $plot_no . $level_no . $road_no . $block_no . $city . $zip_code . $country;
						$company_arr[$row[csf('id')]] = $row[csf('company_name')];
					}
					?>
					<td width="100" style="font-size:xx-large" rowspan="4">
						<?
						$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company_id'", "image_location");
						?>
						<img src='../../../<? echo $image_location; ?>' align="left" style="width: 100px;" />
					</td>
					<td colspan="8" style="font-size:xx-large; justify-content: center;text-align: center;">
						<strong><? echo $company_arr[$company_id]; ?></strong><br>
					</td>
				<tr>
					<td colspan="8" style="font-size:16px; justify-content: center;text-align: center;">
						<? echo $company_address; ?>
					</td>
				</tr>
				<tr>
					<td colspan="8" style="font-size:16px; justify-content: center;text-align: center;">
						<? echo $company_email . '&nbsp;&nbsp;' . $company_website; ?>
					</td>
				</tr>
				<tr>
					<td colspan="8" align="center" style="font-size:x-large"><strong><u>Material Receiving Report</u></strong></td>
				</tr>
				<tr>
					<td width="150"><strong>MRR Number</strong></td>
					<td width="180">:&nbsp;<? echo $dataArray[0][csf('recv_number')]; ?></td>
					<td width="100"><strong>MRR Date</strong></td>
					<td width="130">:&nbsp;<? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
					<td width="100"><strong>Location</strong></td>
					<td width="220">:&nbsp;<? echo $location_arr[$store_location_arr[$dataArray[0][csf('store_id')]]['location_id']]; ?></td>
					<td width="120"><strong>Receive Basis</strong></td>
					<td width="180">:&nbsp;<? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>

				</tr>
				<tr>
					<td><strong>WO/PI/Req.No</strong></td>
					<td>:
						<?
						if ($rcv_basis == 1) echo $pi_library[$booking_id]; // PI
						else if ($rcv_basis == 2) echo $wo_library[$booking_id]["wo_number"]; // WO
						else if ($rcv_basis == 7) echo $requisition_library[$booking_id]["requ_no"]; // Req.
						else echo "Independent";
						?></td>
					<td><strong>WO/PI/Req.No Date</strong></td>
					<td>:
						<?
						if ($rcv_basis == 1) echo change_date_format($pi_library[$booking_id]); // PI
						else if ($rcv_basis == 2) echo change_date_format($wo_library[$booking_id]["wo_date"]); // WO
						else if ($rcv_basis == 7) echo change_date_format($requisition_library[$booking_id]["requisition_date"]); // Req.
						else echo "";
						?>
					</td>
					<td><strong>L/C No</strong></td>
					<td>:&nbsp;<? echo $lc_arr[$dataArray[0][csf('lc_no')]]; ?></td>
					<td><strong>Source</strong></td>
					<td>:&nbsp;<? echo $source[$dataArray[0][csf('source')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Challan No</strong></td>
					<td>:&nbsp;<? echo $dataArray[0][csf('challan_no')]; ?></td>
					<td><strong>Pay Mode</strong></td>
					<td>:
						<?
						if ($rcv_basis == 1 && $dataArray[0][csf('pay_mode')] == 0) echo $pay_mode[2]; //PI basis pay mode will be import
						else echo $pay_mode[$dataArray[0][csf('pay_mode')]];
						?>
					</td>
					<td><strong>Store Name</strong></td>
					<td>:&nbsp;<? echo $store_location_arr[$dataArray[0][csf('store_id')]]['store_name']; ?></td>
					<td><strong>Currency</strong></td>
					<td>:&nbsp;<? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Challan Date</strong></td>
					<td>:&nbsp;<? echo change_date_format($dataArray[0][csf('challan_date')]); ?></td>
					<td><strong>Exchange Rate</strong></td>
					<td>:&nbsp;<? echo $dataArray[0][csf('exchange_rate')]; ?></td>
					<td colspan="4"></td>
				</tr>
				<tr>
					<td><strong>Supplier</strong></td>
					<td colspan="3">:&nbsp;<? echo $supplier_library[$dataArray[0][csf('supplier_id')]]["supplier_name"]; ?></td>
					<td><strong>Supplier Address</strong></td>
					<td colspan="3">
						<p>:&nbsp;<? echo $supplier_library[$dataArray[0][csf('supplier_id')]]["supplier_address"]; ?></p>
					</td>
				</tr>
				<tr>
					<td><strong>Remarks</strong></td>
					<td colspan="3">:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
					<td><strong>Supplier Ref</strong></td>
					<td>:&nbsp;<? echo $dataArray[0][csf('supplier_referance')]; ?></td>
					<td><strong>Received By</strong></td>
					<td>:&nbsp;<? echo $user_name_arr[$inserted_by]; ?></td>
				</tr>
				<tr>
					<td><strong>Req No</strong></td>
					<td colspan="7">:&nbsp;<? echo $requisition_nos; ?></td>
				</tr>
			</table>
			<?
			if ($db_type == 2) {
				$sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category, c.id as product_id, b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks, (c.sub_group_name||' '|| c.item_description || ' '|| c.item_size) as product_name_details, c.item_number, c.item_group_id, c.item_description, c.item_code, c.brand_name, c.origin, b.transaction_date, b.batch_lot
		from inv_receive_master a, inv_transaction b, product_details_master c 
		where a.company_id=$company_id and a.id=$mrr_id and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and a.status_active=1 and b.status_active=1 
		order by b.id";
			} else {
				$sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category, c.id as product_id, b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks, concat(c.sub_group_name,c.item_description, c.item_size) as product_name_details, c.item_number, c.item_group_id, c.item_description,c.item_code, c.brand_name, c.origin, b.transaction_date, b.batch_lot
		from inv_receive_master a, inv_transaction b, product_details_master c 
		where a.company_id=$company_id and a.id=$mrr_id and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and a.status_active=1 and b.status_active=1
		order by b.id";
			}
			//echo $sql_dtls;
			$sql_result = sql_select($sql_dtls);

			?>
			<br>
			<div style="width:100%;margin-top:20px;">
				<table align="left" cellspacing="0" width="1330" border="1" rules="all" class="rpt_table" style="margin-left:20px;">
					<thead bgcolor="#dddddd" align="center">
						<tr>
							<th width="30" rowspan="2">SL</th>
							<th width="50" rowspan="2">Item Code</th>
							<th width="100" rowspan="2">Item Category</th>
							<th width="100" rowspan="2">Item Group</th>
							<th width="150" rowspan="2">Item Description</th>
							<th width="50" rowspan="2">Brand</th>
							<th width="50" rowspan="2">Origin</th>
							<th width="50" rowspan="2">Lot</th>
							<th width="40" rowspan="2">UOM</th>
							<th width="70" rowspan="2">WO/PI Qnty.</th>
							<th width="60" rowspan="2">Previous Recv Qnty</th>
							<th width="60" rowspan="2">Today Recv. Qnty.</th>
							<th width="70" rowspan="2">WO/PI Qnty Bal.</th>
							<th width="180" colspan="3">Unit Price and Date</th>
							<th width="50" rowspan="2">WO Rate</th>
							<th width="70" rowspan="2">WO Amount</th>
							<th width="50" rowspan="2">MRR Rate</th>
							<th rowspan="2">MRR Amount</th>
						</tr>
						<tr>
							<th width="60">Maximum</th>
							<th width="60">Minimum</th>
							<th width="60">Last</th>
						</tr>
					</thead>
					<tbody>
						<?
						$i = 1;
						//echo "<pre>";print_r($wo_library_prod);die;
						foreach ($sql_result as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							$order_qnty = $row[csf('order_qnty')];
							$order_qnty_sum += $order_qnty;

							$order_amount = $row[csf('order_amount')];
							$order_amount_sum += $order_amount;
							$balance_qnty = $wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]["pi_wo_qnty"] - ($row[csf('order_qnty')] + $order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]);
							//$balance_qnty=($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]["pi_wo_qnty"]-($row[csf('order_qnty')]+$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]));
							$balance_qnty_sum += $balance_qnty;

							$desc = $row[csf('item_description')];

							if ($row[csf('item_size')] != "") {
								$desc .= ", " . $row[csf('item_size')];
							}
						?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td><? echo $i; ?></td>
								<td><? echo $row[csf('item_code')]; ?></td>
								<td><? echo $item_category[$row[csf('item_category')]]; ?></td>
								<td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
								<td><? echo $row[csf('product_name_details')]; ?></td>
								<td><? echo $row[csf('brand_name')]; ?></td>
								<td><? echo $countryShortNameArr[$row[csf('origin')]]; ?></td>
								<td align="center"><? echo $row[csf('batch_lot')]; ?></td>
								<td align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
								<td align="right"><? echo number_format($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]["pi_wo_qnty"], 2);
													$tot_ord_qnty += $wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]["pi_wo_qnty"]; ?></td>
								<td align="right"><? echo number_format($order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]], 2);
													$tot_prev_qnty += $order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]; ?></td>
								<td align="right"><? echo number_format($row[csf('order_qnty')], 2, '.', ','); ?></td>
								<td align="right"><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7) echo number_format($balance_qnty, 2, '.', ','); ?></td>

								<td align="right">
									<? echo  number_format($prod_min_max_rate_arr[$row[csf("prod_id")]]["max_order_rate"], 2) . "<hr/ style='border:1px solid black'>" . change_date_format($prod_min_max_rate_arr[$row[csf("prod_id")]]["max_order_date"]); ?>
								</td>
								<td align="right">
									<? echo  number_format($prod_min_max_rate_arr[$row[csf("prod_id")]]["min_order_rate"], 2) . "<hr/ style='border:1px solid black'>" . change_date_format($prod_min_max_rate_arr[$row[csf("prod_id")]]["min_order_date"]); ?>
								</td>
								<td align="right" title="mrr no = <? echo $last_rate_arr[$row[csf("prod_id")]]["recv_number"]; ?>">
									<?
									//echo  number_format($row[csf("order_rate")],2)."<hr/ style='border:1px solid black'><span>".$row[csf("transaction_date")]."</span>"; 
									echo  number_format($last_rate_arr[$row[csf("prod_id")]]["last_rate"], 2) . "<hr/ style='border:1px solid black'><span>" . change_date_format($last_rate_arr[$row[csf("prod_id")]]["trans_date"]) . "</span>";
									?>
								</td>

								<td align="right"><? echo number_format($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]["pi_wo_gross_rate"], 4, '.', ','); ?></td>
								<td align="right"><? $pi_wo_amt = $row[csf('order_qnty')] * $wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]["pi_wo_gross_rate"];
													echo number_format($pi_wo_amt, 2, '.', ',');  ?></td>
								<td align="right"><? echo number_format($row[csf('order_rate')], 4, '.', ','); ?></td>
								<td align="right"><? echo number_format($row[csf('order_amount')], 2, '.', ',');  ?></td>
							</tr>
						<?
							$i++;
							$pi_wo_amt_sum += $pi_wo_amt;
						}
						?>
						<tr bgcolor="#CCCCCC">
							<td align="right" colspan="9">Total</td>
							<td align="right"><? echo number_format($tot_ord_qnty, 2, '.', ','); ?></td>
							<td align="right"><? echo number_format($tot_prev_qnty, 2, '.', ','); ?></td>
							<td align="right"><? echo number_format($order_qnty_sum, 2, '.', ','); ?></td>
							<td align="right"><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7) echo number_format($balance_qnty_sum, 2, '.', ','); ?></td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right" colspan="2"><? echo number_format($pi_wo_amt_sum, 2, '.', ','); ?></td>
							<td align="right" colspan="2"><? echo number_format($order_amount_sum, 2, '.', ','); ?></td>
						</tr>
						<tr bgcolor="#CCCCCC">
							<td align="right" colspan="16">Upcharge/Discount</td>
							<td align="right" colspan="2"><? $upcharge_discount = $order_amount_sum - $pi_wo_amt_sum;
															if ($upcharge_discount > 0) echo number_format($upcharge_discount, 2, '.', ',');
															else echo "(" . number_format(abs($upcharge_discount), 2, '.', ',') . ")"; ?></td>
							<td align="right" colspan="2">&nbsp;</td>
						</tr>
						<tr bgcolor="#CCCCCC">
							<td align="right" colspan="16">Total</td>
							<td align="right" colspan="2"><? echo number_format($order_amount_sum, 2, '.', ','); ?></td>
							<td align="right" colspan="2">&nbsp;</td>
						</tr>
					</tbody>
				</table>
				<table>
					<tr>
						<td colspan="14">
							<h3 align="center" style="margin-left:150px;"> In Words : &nbsp;<? echo number_to_words(number_format($order_amount_sum, 2, '.', ',')) . "( " . $currency[$dataArray[0][csf('currency_id')]] . " )"; ?></h3>
						</td>
					</tr>
				</table>
				<?
				// Return
				if ($booking_id != "") {
					if ($db_type == 2) {
						$sql_return = "SELECT a.id, a.issue_number as return_id, a.issue_date as return_date, a.challan_no, a.remarks, a.booking_id, b.item_category, b.receive_basis, b.pi_wo_batch_no, b.cons_quantity as return_qty, b.cons_rate as rate, b.cons_amount as return_value, b.prod_id, c.id as product_id, (c.sub_group_name||' '|| c.item_description || ' '|| c.item_size) as product_name_details, c.item_number, c.item_group_id, c.item_description, c.item_code, c.brand_name, c.origin, b.transaction_date
						from inv_issue_master a, inv_transaction b, product_details_master c 
						where a.company_id=$company_id and a.RECEIVED_ID = $mrr_id and  a.booking_id=$booking_id and a.id=b.mst_id and b.prod_id=c.id and a.entry_form=26 and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
					} else {
						$sql_return = "SELECT a.id, a.issue_number as return_id, a.issue_date as return_date, a.challan_no, a.remarks, a.booking_id, b.item_category, b.receive_basis, b.pi_wo_batch_no, b.cons_quantity as return_qty, b.cons_rate as rate, b.cons_amount as return_value, b.prod_id, c.id as product_id, concat(c.sub_group_name,c.item_description, c.item_size) as product_name_details, c.item_number, c.item_group_id, c.item_description, c.item_code, c.brand_name, c.origin, b.transaction_date
		   				from inv_issue_master a, inv_transaction b, product_details_master c 
						where a.company_id=$company_id and a.RECEIVED_ID = $mrr_id and a.booking_id=$booking_id and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
					}
				}
				// echo $sql_return ;
				$sql_return_res = sql_select($sql_return);
				if (count($sql_return_res) > 0) {
				?>
					<table cellspacing="0" width="1260" border="1" rules="all" class="rpt_table" style="margin-left:20px;">
						<tr>
							<td colspan="12" align="center">
								<h2>Receive Return Information</h2>
							</td>
						</tr>
					</table>
					<table align="left" cellspacing="0" width="1260" border="1" rules="all" class="rpt_table" style="margin-left:20px;">
						<thead bgcolor="#dddddd" align="center">
							<tr>
								<th width="30">SL</th>
								<th width="100">Item Code</th>
								<th width="120">Item Category</th>
								<th width="120">Item Group</th>
								<th width="180">Item Description</th>
								<th width="100">Return ID</th>
								<th width="80">Return Date</th>
								<th width="100">Challan No</th>
								<th width="250">Remarks</th>
								<th width="90">Returned Qty</th>
								<th width="80">Rate</th>
								<th width="100">Return Value</th>
							</tr>
						</thead>
						<tbody>
							<?
							$j = 1;
							$tot_terurn_value = 0;
							foreach ($sql_return_res as $row) {
								if ($j % 2 == 0) $bgcolor = "#E9F3FF";
								else $bgcolor = "#FFFFFF";
							?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td><? echo $j; ?></td>
									<td><? echo $row[csf('item_code')]; ?></td>
									<td><? echo $item_category[$row[csf('item_category')]]; ?></td>
									<td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
									<td><? echo $row[csf('product_name_details')]; ?></td>
									<td><? echo $row[csf('return_id')]; ?></td>
									<td><? echo change_date_format($row[csf('return_date')]); ?></td>
									<td><? echo $row[csf('challan_no')]; ?></td>
									<td><? echo $row[csf('remarks')]; ?></td>
									<td><? echo number_format($row[csf('return_qty')], 2, '.', ','); ?></td>
									<td><? echo number_format($row[csf('rate')], 2, '.', ','); ?></td>
									<td><? echo number_format($row[csf('return_value')], 2, '.', ','); ?></td>
								</tr>
							<?
								$tot_terurn_value += $row[csf('return_value')];
								$j++;
							}
							?>
							<tr bgcolor="#CCCCCC">
								<td align="right" colspan="11">Net Return Value</td>
								<td align="right"><? echo number_format($tot_terurn_value, 2, '.', ','); ?></td>
							</tr>
							<tr bgcolor="#CCCCCC">
								<td align="right" colspan="11">Net MRR Value After Return</td>
								<td align="right"><? echo number_format($order_amount_sum - $tot_terurn_value, 2, '.', ','); ?></td>
							</tr>
						</tbody>
					</table>
				<?
				}
				?>
				<div align="center">
					<? echo signature_table(11, $data[0], "1300px", '1', 10, $inserted_by); ?>
				</div>

			</div>
		</div>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
	<?
	exit();
}



/*if ($action = "get_receive_basis") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$variable_set_invent = return_field_value("independent_controll", "variable_settings_inventory", "company_name='$company_id' and variable_list=20 and menu_page_id=621 and status_active=1 and is_deleted=0", "independent_controll");
	$is_independent_controlled = ($variable_set_invent == 1) ? "1,2,4,6,7" : "1,2,6,7";
	echo create_drop_down("cbo_receive_basis", 170, $receive_basis_arr, "", 1, "- Select Receive Basis -", $selected, "fn_onCheckBasis(this.value)", "", $is_independent_controlled);
	exit();
}*/

if ($action == "general_item_receive_print_7") 
{
	extract($_REQUEST);
	$data = explode('__', $data);
	echo load_html_head_contents("General Item Receive Print 7", "../../../", 1, 1, $unicode);

	$company_library = return_library_array("SELECT id, company_name from lib_company", "id", "company_name");
	$country_arr = return_library_array("SELECT id, country_name from  lib_country", "id", "country_name");
	$countryShortNameArr = return_library_array("SELECT id,short_name from lib_country where status_active=1", 'id', 'short_name');
	$user_name_arr = return_library_array("SELECT id, user_full_name from user_passwd ", "id", "user_full_name");
	$item_name_arr = return_library_array("SELECT id, item_name from  lib_item_group", "id", "item_name");

	$sql = " SELECT id, recv_number, receive_basis, booking_id, receive_date, challan_no, challan_date, location_id, store_id, supplier_id, currency_id, exchange_rate, source, pay_mode, rcvd_book_no, addi_challan_date, bill_no, bill_date, purchaser_name, receive_by, remarks,inserted_by from inv_receive_master where id='$data[1]'";
	//echo $sql;die;
	$dataArray = sql_select($sql);
	$rcv_basis = $dataArray[0]['RECEIVE_BASIS'];
	$store_id = $dataArray[0]['STORE_ID'];
	$supplier_id = $dataArray[0]['SUPPLIER_ID'];
	$inserted_by = $dataArray[0]['INSERTED_BY'];
	if ($dataArray[0]['RECEIVE_BASIS'] == 2 || $dataArray[0]['RECEIVE_BASIS'] == 1 || $dataArray[0]['RECEIVE_BASIS'] == 7) {

		if ($dataArray[0]['RECEIVE_BASIS'] == 2) // Wo
		{
			$wo_sql = sql_select("SELECT a.id,a.wo_number,a.wo_date,a.requisition_no as requ_id,b.item_id,sum(b.supplier_order_quantity) as wo_qnty from  wo_non_order_info_mst  a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id='" . $dataArray[0]['BOOKING_ID'] . "' and a.status_active=1 and b.status_active=1 group by a.id,a.wo_number,a.requisition_no ,b.item_id,a.wo_date");
			foreach ($wo_sql as $row) {
				$wo_library[$row["ID"]]["wo_number"] = $row["WO_NUMBER"];
				$wo_library[$row["ID"]]["wo_date"] = $row["WO_DATE"];
				$wo_library_prod[$row["ID"]][$row["ITEM_ID"]] = $row["WO_QNTY"];
				$requsition_id_arr[$row["WO_NUMBER"]] = $row["REQU_ID"];
			}
		} else if ($dataArray[0]['RECEIVE_BASIS'] == 1) // Pi
		{
			$sql_pi = sql_select("SELECT a.id as pi_id, a.pi_number,b.work_order_no, b.item_prod_id as item_id , sum(b.quantity) as quantity from com_pi_master_details a , com_pi_item_details b where a.id=b.pi_id  and a.id='" . $dataArray[0]['BOOKING_ID'] . "' group by a.id, a.pi_number,b.work_order_no,b.item_prod_id");
			foreach ($sql_pi as $row) {
				$pi_library[$row["PI_ID"]] = $row["PI_NUMBER"];
				$wo_library_prod[$row["PI_ID"]][$row["ITEM_ID"]] += $row["QUANTITY"];

				$pi_wo_no_library[$row["PI_NUMBER"]] = $row["WORK_ORDER_NO"];
			}
		} else // Req.
		{
			$sql_req = sql_select("SELECT a.id as req_id, a.requ_no,a.requisition_date,a.division_id,a.department_id, b.product_id as item_id , sum(b.quantity) as quantity from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id  and a.id='" . $dataArray[0]['BOOKING_ID'] . "' group by a.id,a.requ_no,a.division_id,a.department_id,b.product_id,a.requisition_date");
			foreach ($sql_req as $row) {
				$requisition_library[$row["REQ_ID"]]["requ_no"] = $row["REQU_NO"];
				$requisition_library[$row["REQ_ID"]]["requisition_date"] = $row["REQUISITION_DATE"];
				$wo_library_prod[$row["REQ_ID"]][$row["ITEM_ID"]] = $row["QUANTITY"];

				$division_library[$row["REQU_NO"]] = $row["DIVISION_ID"];
				$department_library[$row["REQU_NO"]] = $row["DEPARTMENT_ID"];
			}
		}


		$order_prev_sql = sql_select("SELECT a.booking_id,b.prod_id,sum(b.order_qnty) as wo_prev_qnty from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.booking_id='" . $dataArray[0]['BOOKING_ID'] . "' and a.id !='" . $dataArray[0]['ID'] . "'  and a.status_active=1 and b.status_active=1 group by a.booking_id,b.prod_id");
		foreach ($order_prev_sql as $row) {
			$order_prev_qnty_arr[$row["BOOKING_ID"]][$row["PROD_ID"]] = $row["WO_PREV_QNTY"];
		}
	}

	//=======================================<<<<================
	$store_info = sql_select("SELECT id, store_name,store_location from lib_store_location where id=$store_id");
	$supplier_info = sql_select("SELECT id,supplier_name,address_1 from lib_supplier where id=$supplier_id");

	?>
		<style type="text/css">
			#top_table tr td {
				vertical-align: top;
			}

			.rpt_table td,
			.rpt_table thead th {
				border: 1px solid black;
				font-size: 14px;
			}
		</style>
		<div style="width:1220px;">
			<table width="1200" cellspacing="1" align="right" id="top_table">
				<tr>
					<td width="100" style="font-size:xx-large" rowspan="3">
						<?
						$cbo_company_name = $data[0];
						$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$cbo_company_name'", "image_location");
						?>
						<img src='<?= base_url($image_location); ?>' height='70' align="left" style="width: 80px;" />
					</td>
					<td colspan="8" valign="top" style="font-size:xx-large; text-align: center;">
						<strong><? echo $company_library[$data[0]]; ?></strong><br>
					</td>
				</tr>
				<tr>
					<td colspan="8" align="center" style="font-size:x-large">
						<span style="font-size:14px">
							<?
							$nameArray = sql_select("SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] ");
							foreach ($nameArray as $row) {
								if ($row["LEVEL_NO"])		$level_no	= $row["LEVEL_NO"] . ', ';
								if ($row["PLOT_NO"])		$plot_no 	= $row["PLOT_NO"] . ', ';
								if ($row["ROAD_NO"]) 		$road_no 	= $row["ROAD_NO"] . ', ';
								if ($row["BLOCK_NO"] != '')	$block_no 	= $row["BLOCK_NO"] . ', ';
								if ($row["ZIP_CODE"] != '')	$zip_code 	= ' -' . $row["ZIP_CODE"] . ', ';
								if ($row["CITY"] != '') 		$city 		= ($zip_code != "") ? $row["CITY"] : $row["CITY"] . " ,";
								if ($row["COUNTRY_ID"] != '')	$country 	= $country_full_name[$row["COUNTRY_ID"]] . '.';
								if ($row["EMAIL"] != '')		$email 		= "Email Address: " . $row["EMAIL"] . ', ';
								if ($row["WEBSITE"] != '')	$website 	= "Website No: " . $row["WEBSITE"];
							}
							echo rtrim($level_no . $plot_no . $road_no . $block_no . $city . $zip_code . $country, ", ") . '</br>' . $email . $website;
							?>
						</span>
					</td>
				</tr>
				<tr>
					<td colspan="8" align="center" style="font-size:x-large"><strong><u>Material Receiving Report</u></strong></td>
				</tr>
			</table>
			<hr width="1200" style="border: 1px solid black;">
			<table width="1200" cellspacing="1" align="right" id="top_table" style="margin: 5px;">
				<table cellspacing="0" width="360" border="1" rules="all" class="rpt_table" style="float: left;border: 2px solid black; margin-right: 20px; margin-left:20px;">
					<tr>
						<td width="120">MRR Number</td>
						<td>:&nbsp;<? echo $dataArray[0]['RECV_NUMBER']; ?></td>
					</tr>
					<tr>
						<td>MRR Date</td>
						<td>:&nbsp;<? echo change_date_format($dataArray[0]['RECEIVE_DATE']); ?></td>
					</tr>
					<tr>
						<td>Supplier Name</td>
						<td>:&nbsp;<? echo $supplier_info[0]["SUPPLIER_NAME"]; ?></td>
					</tr>
					<tr>
						<td>Supplier Address</td>
						<td>:&nbsp;<? echo $supplier_info[0]["ADDRESS_1"]; ?></td>
					</tr>
					<tr>
						<td>Source</td>
						<td>:&nbsp;<? echo $source[$dataArray[0]['SOURCE']]; ?></td>
					</tr>
					<tr>
						<td>Pay Mode</td>
						<td>:&nbsp;<?
									if ($dataArray[0]['RECEIVE_BASIS'] == 1 && $dataArray[0]['PAY_MODE'] == 0) {
										echo $pay_mode[2];
									} else {
										echo $pay_mode[$dataArray[0]['PAY_MODE']];
									}
									?>
						</td>
					</tr>
					<tr>
						<td>Exchange Rate</td>
						<td>:&nbsp;<? echo $dataArray[0]['EXCHANGE_RATE']; ?></td>
					</tr>
				</table>
				<table cellspacing="0" width="360" border="1" rules="all" class="rpt_table" style="float: left;border: 2px solid black; margin-right: 20px;margin-left:20px;">
					<tr>
						<td width="100">Receive Basis</td>
						<td>:&nbsp;<? echo $receive_basis_arr[$dataArray[0]['RECEIVE_BASIS']]; ?></td>
					</tr>
					<tr>
						<td>WO/PI/Req.No</td>
						<td>:&nbsp;<?
									if ($dataArray[0]['RECEIVE_BASIS'] == 1) // PI
									{
										echo $pi_library[$dataArray[0]['BOOKING_ID']];
									} else if ($dataArray[0]['RECEIVE_BASIS'] == 2) // WO
									{
										echo $wo_library[$dataArray[0]['BOOKING_ID']]["wo_number"];
									} else  if ($dataArray[0]['RECEIVE_BASIS'] == 7) // Req.
									{
										echo $requisition_library[$dataArray[0]['BOOKING_ID']]["requ_no"];
									} else {
										echo "Independent";
									}
									?>
						</td>
					</tr>
					<tr>
						<td>WO/PI/Req.No Date</td>
						<td>:&nbsp;<?
									if ($dataArray[0]['RECEIVE_BASIS'] == 1) // PI
									{
										echo $pi_library[$dataArray[0]['BOOKING_ID']];
									} else if ($dataArray[0]['RECEIVE_BASIS'] == 2) // WO
									{
										echo $wo_library[$dataArray[0]['BOOKING_ID']]["wo_date"];
									} else  if ($dataArray[0]['RECEIVE_BASIS'] == 7) // Req.
									{
										echo $requisition_library[$dataArray[0]['BOOKING_ID']]["requisition_date"];
									} else {
										echo "";
									}
									?>
						</td>
					</tr>
					<tr>
						<td>Challan No</td>
						<td>:&nbsp;<? echo $dataArray[0]['CHALLAN_NO']; ?></td>
					</tr>
					<tr>
						<td>Challan Date</td>
						<td>:&nbsp;<? echo change_date_format($dataArray[0]['CHALLAN_DATE']); ?></td>
					</tr>
					<tr>
						<td>Currency</td>
						<td>:&nbsp;<?= $currency[$dataArray[0]['CURRENCY_ID']] ?></td>
					</tr>
					<tr>
						<td>Purchaser Name</td>
						<td>:&nbsp;<?= $user_name_arr[$dataArray[0]['PURCHASER_NAME']]; ?></td>
					</tr>
				</table>
				<table cellspacing="0" width="380" border="1" rules="all" class="rpt_table" style="border: 2px solid black; margin-right: 50px;">
					<tr>
						<td width="100">Receive By</td>
						<td>:&nbsp;<?= $user_name_arr[$dataArray[0]['RECEIVE_BY']]; ?></td>
					</tr>
					<tr>
						<td>Store Name</td>
						<td>:&nbsp;<?= $store_info[0]["STORE_NAME"]; ?></td>
					</tr>
					<tr>
						<td>Store Location</td>
						<td>:&nbsp;<?= $store_info[0]["STORE_LOCATION"]; ?></td>
					</tr>
					<tr>
						<td>Book No</td>
						<td>:&nbsp;<? echo $dataArray[0]['RCVD_BOOK_NO']; ?></td>
					</tr>
					<tr>
						<td>Bill No</td>
						<td>:&nbsp;<? echo $dataArray[0]['BILL_NO']; ?></td>
					</tr>
					<tr>
						<td>Bill Date</td>
						<td>:&nbsp;<? echo change_date_format($dataArray[0]['BILL_DATE']); ?></td>
					</tr>
					<tr>
						<td>Remark</td>
						<td>:&nbsp;<? echo $dataArray[0]['REMARKS']; ?></td>
					</tr>
				</table>
			</table>
			<br>
			<hr width="1200" style="border: 1px solid black;">
			<?
			if ($db_type == 2) {
				$sql_dtls = "SELECT a.id as recv_id, a.booking_id, b.item_category,c.id as product_id,
	  b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.prod_id, b.remarks, (c.sub_group_name||' '|| c.item_description || ' '|| c.item_size) as product_name_details,c.item_number,c.item_group_id, c.item_description,c.item_code, c.brand_name, c.origin, b.transaction_date
	  from inv_receive_master a,  product_details_master c, inv_transaction b
	  left join wo_non_order_info_dtls d on  b.receive_basis=2 and b.pi_wo_batch_no= d.mst_id and b.prod_id= d.item_id
	  where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1 
	  group by a.id , a.booking_id, b.item_category,c.id, b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, 
	  b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.prod_id, b.remarks, (c.sub_group_name||' '|| c.item_description || ' '|| c.item_size) ,c.item_number,c.item_group_id, c.item_description,c.item_code, c.brand_name, c.origin, b.transaction_date 
	  order by b.id";
			} else {
				$sql_dtls = "SELECT a.id as recv_id, a.booking_id, b.item_category,c.id as product_id,
	  b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.prod_id, b.remarks, concat(c.sub_group_name,c.item_description, c.item_size) as product_name_details, c.item_number, c.item_group_id, c.item_description,c.item_code, c.brand_name, c.origin, b.transaction_date
	  from inv_receive_master a, inv_transaction b, product_details_master c ,wo_non_order_info_dtls d
	  where d.item_id=c.id and a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1
	  order by b.id";
			}

			//echo $sql_dtls; die;

			$sql_result = sql_select($sql_dtls);
			$i = 1;
			?>
			<br>
			<table align="left" cellspacing="0" width="1200" border="1" rules="all" class="rpt_table" style="margin-left:20px;">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="120" rowspan="2">Item Category</th>
						<th width="110" rowspan="2">Item Group</th>
						<th width="200" rowspan="2">Item Description</th>
						<th width="100" rowspan="2">Brand</th>
						<th width="50" rowspan="2">Origin</th>
						<th width="40" rowspan="2">UOM</th>
						<th width="70" rowspan="2">WO/PI Qnty.</th>
						<th width="70" rowspan="2">Previous Recv Qnty</th>
						<th width="70" rowspan="2">Today Recv. Qnty.</th>
						<th colspan="2">Visual Insp.</th>
						<th width="70" rowspan="2">Rate</th>
						<th rowspan="2">Amount</th>
					</tr>
					<tr>
						<th width="80">Good</th>
						<th width="80">Damage</th>
					</tr>
				</thead>
				<tbody>
					<?
					foreach ($sql_result as $row) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td><? echo $i; ?></td>
							<td><? echo $item_category[$row['ITEM_CATEGORY']]; ?></td>
							<td><? echo $item_name_arr[$row['ITEM_GROUP_ID']]; ?></td>
							<td><? echo $row['PRODUCT_NAME_DETAILS']; ?></td>
							<td align="center"><? echo $row['BRAND_NAME']; ?></td>
							<td align="center"><? echo $countryShortNameArr[$row['ORIGIN']]; ?></td>
							<td align="center"><? echo $unit_of_measurement[$row['ORDER_UOM']]; ?></td>
							<td align="right"><? echo number_format($wo_library_prod[$row["BOOKING_ID"]][$row["PROD_ID"]], 2);  ?></td>
							<td align="right"><? echo number_format($order_prev_qnty_arr[$row["BOOKING_ID"]][$row["PROD_ID"]], 2); ?></td>
							<td align="right"><? echo number_format($row['ORDER_QNTY'], 2, '.', ','); ?></td>
							<td align="right"><? echo number_format($row['ORDER_QNTY'], 2, '.', ',');  ?></td>
							<td align="right"><? echo number_format($row['REMARKS'], 2, '.', ','); ?></td>
							<td align="right"><? echo number_format($row['ORDER_RATE'], 4, '.', ','); ?></td>
							<td align="right"><? echo number_format($row['ORDER_AMOUNT'], 2, '.', ',');  ?></td>
						</tr>
					<?
						$order_qnty_sum += $row['ORDER_QNTY'];
						$damage_qnty_sum += $row['REMARKS'];
						$order_amount_sum += $row['ORDER_AMOUNT'];
						$tot_ord_qnty += $wo_library_prod[$row["BOOKING_ID"]][$row["PROD_ID"]];
						$tot_prev_qnty += $order_prev_qnty_arr[$row["BOOKING_ID"]][$row["PROD_ID"]];
						$i++;
					}
					?>
					<tr bgcolor="#CCCCCC">
						<td align="right" colspan="7">Total&nbsp;</td>
						<td align="right"><? echo number_format($tot_ord_qnty, 2, '.', ','); ?></td>
						<td align="right"><? echo number_format($tot_prev_qnty, 2, '.', ','); ?></td>
						<td align="right"><? echo number_format($order_qnty_sum, 2, '.', ','); ?></td>
						<td align="right"><? echo number_format($order_qnty_sum, 2, '.', ','); ?></td>
						<td align="right"><? echo number_format($damage_qnty_sum, 2, '.', ','); ?></td>
						<td align="right">&nbsp;</td>
						<td align="right"><? echo number_format($order_amount_sum, 2, '.', ','); ?></td>
					</tr>
				</tbody>
			</table>
			<table>
				<tr>
					<td>
						<h3 align="center" style="margin-left:50px;"> In Words : &nbsp;<? echo number_to_words(number_format($order_amount_sum, 2, '.', ',')) . "( " . $currency[$dataArray[0][csf('currency_id')]] . " )"; ?></h3>
					</td>
				</tr>
			</table>
			<br>
			<?
			$insert_signature_arr = sql_select("SELECT MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature' and master_tble_id=$inserted_by ");

			if (count($insert_signature_arr[$inserted_by])) {
				$userSignatureArr[$inserted_by] = base_url($insert_signature_arr[0]["IMAGE_LOCATION"]);
			}

			$signature_arr = sql_select("SELECT a.user_id,b.IMAGE_LOCATION from variable_settings_signature a, common_photo_library b where a.report_id=11 and a.company_id=$data[0] and a.status_active=1 and a.template_id=1 and a.user_id=b.master_tble_id and b.form_name='user_signature'");

			$signature_arr_res = sql_select($signature_arr);
			foreach ($signature_arr_res as $row) {
				$userSignatureArr[$row['USER_ID']] = base_url($row['IMAGE_LOCATION']);
			}

			echo signature_table(11, $data[0], "1200px", '1', 40, $inserted_by, $userSignatureArr);
			?>
		</div>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
	<?
	exit();
}

if ($action == "general_item_receive_print_8") 
{
	extract($_REQUEST);
	$data = explode('__', $data);
	echo load_html_head_contents("General Item Receive Info", "../../../", 1, 1, $unicode);

	$countryShortNameArr = return_library_array("select id,short_name from lib_country where status_active=1 and is_deleted=0", 'id', 'short_name');
	$division_name_arr = return_library_array("select id, division_name from   lib_division", "id", "division_name");
	$department_name_arr = return_library_array("select id, department_name from   lib_department", "id", "department_name");

	$req_div_name_arr = return_library_array("select id, division_id from   inv_purchase_requisition_mst", "id", "division_id");
	$req_department_name_arr = return_library_array("select id, department_id from inv_purchase_requisition_mst", "id", "department_id");
	$req_no_arr = return_library_array("select wo_number, requisition_no from wo_non_order_info_mst where entry_form in(146,147)", "wo_number", "requisition_no");

	$user_name_arr = return_library_array("select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name", "id", "user_full_name");
	$lc_arr = return_library_array("select id, lc_number from  com_btb_lc_master_details", "id", "lc_number");
	$approval_arr = return_library_array("SELECT MST_ID,  max(APPROVED_DATE) as APPROVED_DATE from  approval_history WHERE CURRENT_APPROVAL_STATUS=1 group by MST_ID", "MST_ID", "APPROVED_DATE");
	// echo $approval_arr[$data[1]]."hhh"."___".$data[1];
	$sql = " SELECT id, recv_number, receive_basis, receive_purpose, booking_id, loan_party, gate_entry_no, receive_date, challan_no, challan_date, location_id, store_id, supplier_id, lc_no, currency_id, exchange_rate, source,supplier_referance,pay_mode,store_sl_no,rcvd_book_no, addi_challan_date,bill_no, bill_date, purchaser_name, carried_by, qc_check_by, receive_by,gate_entry_by,gate_entry_date,addi_rcvd_date, remarks, inserted_by from inv_receive_master where id='$data[1]'";
	// echo $sql;die;
	$dataArray = sql_select($sql);
	$rcv_basis = $dataArray[0][csf('receive_basis')];
	if ($dataArray[0][csf('receive_basis')] == 2 || $dataArray[0][csf('receive_basis')] == 1 || $dataArray[0][csf('receive_basis')] == 7) {

		if ($dataArray[0][csf('receive_basis')] == 2) // Wo
		{
			$wo_sql = sql_select("select a.id,a.wo_number,a.wo_date,a.requisition_no as requ_id,b.item_id,sum(b.supplier_order_quantity) as wo_qnty from  wo_non_order_info_mst  a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id='" . $dataArray[0][csf('booking_id')] . "' and a.status_active=1 and b.status_active=1 group by a.id,a.wo_number,a.requisition_no ,b.item_id,a.wo_date");

			$all_requ_no = '';
			foreach ($wo_sql as $row) {
				$wo_library[$row[csf("id")]]["wo_number"] = $row[csf("wo_number")];
				$wo_library[$row[csf("id")]]["wo_date"] = $row[csf("wo_date")];
				$wo_library_prod[$row[csf("id")]][$row[csf("item_id")]] = $row[csf("wo_qnty")];
				$requsition_id_arr[$row[csf("wo_number")]] = $row[csf("requ_id")];

				if ($row[csf("requ_id")] != '') {
					if ($all_requ_no != '') {
						$all_requ_no .= ",";
					}
					$all_requ_no .= $row[csf("requ_id")];
				}
			}

			$requsition_arr = array();

			$requisition_sql = sql_select("select a.id as req_id, a.requ_no from inv_purchase_requisition_mst a where a.id in ($all_requ_no) group by a.id,a.requ_no");

			foreach ($requisition_sql as $req_val) {
				$requsition_arr[$req_val[csf('req_id')]] = $req_val[csf('requ_no')];
			}
		} else if ($dataArray[0][csf('receive_basis')] == 1) // Pi
		{
			$sql_pi = sql_select("select a.id as pi_id, a.pi_number,b.work_order_no, b.item_prod_id as item_id , sum(b.quantity) as quantity from com_pi_master_details a , com_pi_item_details b where a.id=b.pi_id  and a.id='" . $dataArray[0][csf('booking_id')] . "' group by a.id, a.pi_number,b.work_order_no,b.item_prod_id");
			foreach ($sql_pi as $row) {
				$pi_library[$row[csf("pi_id")]] = $row[csf("pi_number")];
				$wo_library_prod[$row[csf("pi_id")]][$row[csf("item_id")]] += $row[csf("quantity")];

				$pi_wo_no_library[$row[csf("pi_number")]] = $row[csf("work_order_no")];
			}
		} else // Req.
		{
			$sql_req = sql_select("select a.id as req_id, a.requ_no,a.requisition_date,a.division_id,a.department_id, b.product_id as item_id , sum(b.quantity) as quantity from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id  and a.id='" . $dataArray[0][csf('booking_id')] . "' group by a.id,a.requ_no,a.division_id,a.department_id,b.product_id,a.requisition_date");
			foreach ($sql_req as $row) {
				$requisition_library[$row[csf("req_id")]]["requ_no"] = $row[csf("requ_no")];
				$requisition_library[$row[csf("req_id")]]["requisition_date"] = $row[csf("requisition_date")];
				$wo_library_prod[$row[csf("req_id")]][$row[csf("item_id")]] = $row[csf("quantity")];

				$division_library[$row[csf("requ_no")]] = $row[csf("division_id")];
				$department_library[$row[csf("requ_no")]] = $row[csf("department_id")];
			}
		}


		$order_prev_sql = sql_select("select a.booking_id,b.prod_id,sum(b.order_qnty) as wo_prev_qnty from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.booking_id='" . $dataArray[0][csf('booking_id')] . "' and a.id !='" . $dataArray[0][csf('id')] . "' and a.id<'" . $dataArray[0][csf('id')] . "' and a.receive_date<='" . $dataArray[0][csf('receive_date')] . "'  and a.status_active=1 and b.status_active=1 group by a.booking_id,b.prod_id");

		foreach ($order_prev_sql as $row) {
			$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]] = $row[csf("wo_prev_qnty")];
		}
	}

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

	$supplier_res = sql_select("select id,supplier_name,address_1 from  lib_supplier");
	foreach ($supplier_res as $val) {
		$supplier_library[$val[csf("id")]]["supplier_name"] = $val[csf("supplier_name")];
		$supplier_library[$val[csf("id")]]["supplier_address"] = $val[csf("address_1")];
	}


	$prod_min_max_rate_res = sql_select("select prod_id, max(order_rate) max_order_rate,max(transaction_date) keep (dense_rank first order by order_rate desc) max_order_date,
	min(order_rate)min_order_rate,min(transaction_date) keep (dense_rank first order by order_rate asc) min_order_date
	from inv_transaction where transaction_type=1 and status_active = 1
	group by prod_id");

	foreach ($prod_min_max_rate_res as $val) {
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["max_order_rate"] = $val[csf("max_order_rate")];
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["max_order_date"] = $val[csf("max_order_date")];
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["min_order_rate"] = $val[csf("min_order_rate")];
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["min_order_date"] = $val[csf("min_order_date")];
	}

	//=================Last Rate============>>>>>================

	//$last_rate_res = sql_select("select prod_id,order_rate,transaction_date, mst_id from inv_transaction where transaction_type=1 and status_active = 1 and transaction_date <= '".$dataArray[0][csf('receive_date')]."' and mst_id < ".$dataArray[0][csf('id')]." order by mst_id desc");

	$last_rate_res = sql_select("select a.prod_id,a.order_rate,a.transaction_date, a.mst_id, b.recv_number
		from inv_transaction a, inv_receive_master b
		where a.mst_id = b.id and a.transaction_type=1 and a.status_active = 1 
		and a.transaction_date <= '" . $dataArray[0][csf('receive_date')] . "' and a.mst_id < " . $dataArray[0][csf('id')] . " 
		 order by a.mst_id desc");


	$prodDupliChkArr = array();
	foreach ($last_rate_res as $value) {
		if ($prodDupliChkArr[$value[csf("prod_id")]] == "") {
			$prodDupliChkArr[$value[csf("prod_id")]] = $value[csf("prod_id")];
			$last_rate_arr[$value[csf("prod_id")]]["last_rate"] = $value[csf("order_rate")];
			$last_rate_arr[$value[csf("prod_id")]]["trans_date"] = $value[csf("transaction_date")];
			$last_rate_arr[$value[csf("prod_id")]]["recv_number"] = $value[csf("recv_number")];
		}
	}
	unset($last_rate_res);

	//=======================================<<<<================

	$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$item_name_arr = return_library_array("select id, item_name from  lib_item_group", "id", "item_name");
	?>
		<style type="text/css">
			#top_table tr td {
				vertical-align: top;
			}
		</style>
		<div style="width:1300px;">
			<table width="1270" cellspacing="1" align="right" id="top_table" style="margin-left:20px;">
				<tr>
					<td width="100" style="font-size:xx-large" rowspan="2">
						<?
						$cbo_company_name = $data[0];
						$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$cbo_company_name'", "image_location");
						?>
						<img src='../../../<? echo $image_location; ?>' height='40' align="left" style="width: 100px;" />
					</td>
					<td colspan="8" style="font-size:xx-large; justify-content: center;text-align: center;">
						<strong><? echo $company_library[$data[0]]; ?></strong><br>
						<span style="font-size:14px">
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
								Province No: <? echo $result['province']; ?>
								Country: <? echo $country_arr[$result['country_id']]; ?><br>
								Email Address: <? echo $result['email']; ?>
								Website No: <? echo $result['website'];
										}
											?>
						</span>
					</td>
					<td width="200">

					</td>
				</tr>
				<tr>
					<td colspan="8" align="center" style="font-size:x-large"><strong><u>Material Receiving Report</u></strong></td>
				</tr>
				<tr>
					<td width="150"><strong style="font-size: 16px;">Requisition No</strong></td>
					<td width="290" style="font-size: 16px;"><strong>:
							<?
							if ($dataArray[0][csf('receive_basis')] == 2) // WO
							{
								echo $requsition_arr[$requsition_id_arr[$wo_library[$dataArray[0][csf('booking_id')]]["wo_number"]]];
							} else  if ($dataArray[0][csf('receive_basis')] == 7) // Req.
							{
								echo $requisition_library[$dataArray[0][csf('booking_id')]]["requ_no"];
							}
							?>
						</strong></td>
					<td width="100"><strong style="font-size: 16px;">Receive No</strong></td>
					<td style="font-size: 16px;" width="290"><strong>:<? echo $dataArray[0][csf('rcvd_book_no')]; ?></strong></td>
					<td width="100"><strong style="font-size: 16px;">Receive Basis</strong></td>
					<td style="font-size: 16px;" width="290"><strong>:<? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></strong>&nbsp;&nbsp;</td>
					<td width="120"><strong style="font-size: 16px;">Purchase By</strong></td>
					<td style="font-size: 16px;" width="290"><strong>:<? echo $user_name_arr[$dataArray[0][csf('purchaser_name')]]; ?></strong></td>

				</tr>
				<tr>
					<td width="150"><strong style="font-size: 16px;">MRR Number</strong></td>
					<td style="font-size: 16px;" width="180"><strong>:<? echo $dataArray[0][csf('recv_number')]; ?></strong></td>
					<td><strong style="font-size: 16px;">Receive Date</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo change_date_format($dataArray[0][csf('addi_rcvd_date')]); ?></strong></td>
					<td><strong style="font-size: 16px;">L/C No</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo $lc_arr[$dataArray[0][csf('lc_no')]]; ?></strong></td>
					<td><strong style="font-size: 16px;">Delivered By</strong></td><strong>
						<td style="font-size: 16px;">:<? echo $user_name_arr[$dataArray[0][csf('carried_by')]]; ?></td>
					</strong>
				</tr>
				<tr>
					<td><strong style="font-size: 16px;">Mrr Date</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo change_date_format($dataArray[0][csf('receive_date')]); ?></strong></td>
					<td><strong style="font-size: 16px;">Challan No</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo $dataArray[0][csf('challan_no')]; ?></strong></td>
					<td><strong style="font-size: 16px;">Challan Date</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo change_date_format($dataArray[0][csf('challan_date')]); ?></strong></td>
					<td><strong style="font-size: 16px;">Supplier</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo $supplier_library[$dataArray[0][csf('supplier_id')]]["supplier_name"]; ?></strong></td>

				</tr>
				<tr>
					<td><strong style="font-size: 16px;">WO/PI/Req.No</strong></td>
					<td style="font-size: 16px;"><strong>:
							<?
							if ($dataArray[0][csf('receive_basis')] == 1) // PI
							{
								echo $pi_library[$dataArray[0][csf('booking_id')]];
							} else if ($dataArray[0][csf('receive_basis')] == 2) // WO
							{
								echo $wo_library[$dataArray[0][csf('booking_id')]]["wo_number"];
							} else  if ($dataArray[0][csf('receive_basis')] == 7) // Req.
							{
								echo $requisition_library[$dataArray[0][csf('booking_id')]]["requ_no"];
							} else {
								echo "Independent";
							}
							?></strong></td>
					<td><strong style="font-size: 16px;">Challan Date</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo change_date_format($dataArray[0][csf('addi_challan_date')]); ?></strong></td>
					<td><strong style="font-size: 16px;">Supplier Address</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo $supplier_library[$dataArray[0][csf('supplier_id')]]["supplier_address"]; ?></strong></td>
					<td><strong style="font-size: 16px;">Received By </strong></td>
					<td style="font-size: 16px;"><strong>:<? echo $user_name_arr[$dataArray[0][csf('receive_by')]]; ?></strong></td>
				</tr>
				<tr>
					<td><strong style="font-size: 16px;">WO/PI/Req.No Date</strong></td>
					<td style="font-size: 16px;"><strong>:
							<?
							if ($dataArray[0][csf('receive_basis')] == 1) // PI
							{
								echo $pi_library[$dataArray[0][csf('booking_id')]];
							} else if ($dataArray[0][csf('receive_basis')] == 2) // WO
							{
								echo $wo_library[$dataArray[0][csf('booking_id')]]["wo_date"];
							} else  if ($dataArray[0][csf('receive_basis')] == 7) // Req.
							{
								echo $requisition_library[$dataArray[0][csf('booking_id')]]["requisition_date"];
							} else {
								echo "";
							}
							?>
						</strong></td>
					<td><strong style="font-size: 16px;">Bill No</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo $dataArray[0][csf('bill_no')]; ?></strong></td>
					<td><strong style="font-size: 16px;">Store Name</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo $store_library[$dataArray[0][csf('store_id')]]; ?></strong></td>
					<td><strong style="font-size: 16px;">Gate Entry By </strong></td>
					<td style="font-size: 16px;"><strong>:<? echo $user_name_arr[$dataArray[0][csf('gate_entry_by')]]; ?></strong></td>
				</tr>
				<tr>
					<td><strong style="font-size: 16px;">Gate Entry No</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo $dataArray[0][csf('gate_entry_no')]; ?></strong></td>
					<td><strong style="font-size: 16px;">Bill Date</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo change_date_format($dataArray[0][csf('bill_date')]); ?></strong></td>
					<td><strong style="font-size: 16px;">Store Sl No</strong></td>
					<td style="font-size: 16px;">:<strong><? echo $dataArray[0][csf('store_sl_no')]; ?></strong></td>
					<td><strong style="font-size: 16px;">Prepared By</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo $user_name_arr[$dataArray[0][csf('inserted_by')]]; ?></strong></td>
				</tr>
				<tr>
					<td><strong style="font-size: 16px;">Gate Entry Date</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo change_date_format($dataArray[0][csf('gate_entry_date')]); ?></strong></td>
					<td><strong style="font-size: 16px;">Currency</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo $currency[$dataArray[0][csf('currency_id')]]; ?></strong></td>
					<td><strong style="font-size: 16px;">Source</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo $source[$dataArray[0][csf('source')]]; ?></strong></td>
					<td><strong style="font-size: 16px;">QC Checked By</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo $user_name_arr[$dataArray[0][csf('qc_check_by')]]; ?></strong></td>
				</tr>
				<tr>
					<td><strong style="font-size: 16px;">Pay Mode</strong></td>
					<td style="font-size: 16px;"><strong>:<?
						if ($dataArray[0][csf('receive_basis')] == 1 && $dataArray[0][csf('pay_mode')] == 0) {
							echo $pay_mode[2]; //PI basis pay mode will be import
						} else {
							echo $pay_mode[$dataArray[0][csf('pay_mode')]];
						}

						?>
						</strong></td>
					<td rowspan="2" valign="top"><strong style="font-size: 16px;">Barcode</strong></td>
					<td rowspan="2" colspan="3" valign="top" id="bar_code"></td>
					<td><strong style="font-size: 16px;">Exchange Rate</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo $dataArray[0][csf('exchange_rate')]; ?></strong></td>
				</tr>
				<tr>
					<td><strong style="font-size: 16px;">Remark</strong></td>
					<td style="font-size: 16px;"><strong>:<? echo $dataArray[0][csf('remarks')]; ?></strong></td>
					<td style="font-size: 16px;text-align:left" colspan="3"><strong>WO Approval Date:<? echo change_date_format($approval_arr[$dataArray[0][csf('booking_id')]]); ?></strong></td>
				</tr>
				
				<tr>
					<td colspan="6"></td>
				</tr>
			</table>
			<?
			if ($db_type == 2) {
				//   $sql_dtls = "SELECT a.id as recv_id, a.booking_id, b.item_category,c.id as product_id,
				//   b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks, b.batch_lot,
				//   (c.sub_group_name||' '|| c.item_description || ' '|| c.item_size) as product_name_details,c.item_number,c.item_group_id, c.item_description,c.item_code, c.brand_name, c.origin, b.transaction_date
				//   from inv_receive_master a,  product_details_master c, inv_transaction b
				//   left join wo_non_order_info_dtls d on  b.receive_basis=2 and b.pi_wo_batch_no= d.mst_id and b.prod_id= d.item_id
				//   where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1 
				//   order by b.id";
				$sql_dtls = "SELECT a.id as recv_id, a.booking_id, b.item_category,c.id as product_id,
	b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks, b.batch_lot,
	(c.sub_group_name||' '|| c.item_description || ' '|| c.item_size) as product_name_details,c.item_number,c.item_group_id, c.item_description,c.item_code, c.brand_name, c.origin, b.transaction_date
	from inv_receive_master a,  product_details_master c, inv_transaction b
	left join wo_non_order_info_dtls d on  b.receive_basis=2 and b.pi_wo_batch_no= d.mst_id and b.prod_id= d.item_id
	where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1 
	group by a.id , a.booking_id, b.item_category,c.id , b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks, b.batch_lot,
	(c.sub_group_name||' '|| c.item_description || ' '|| c.item_size),c.item_number,c.item_group_id, c.item_description,c.item_code, c.brand_name, c.origin, b.transaction_date
	order by b.id";
			} else {
				$sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category,c.id as product_id,
	  b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks, b.batch_lot,
	 concat(c.sub_group_name,c.item_description, c.item_size) as product_name_details, c.item_number, c.item_group_id, c.item_description,c.item_code, c.brand_name, c.origin, b.transaction_date
	  from inv_receive_master a, inv_transaction b, product_details_master c ,wo_non_order_info_dtls d
	  where d.item_id=c.id and a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1
	  order by b.id";
			}

			//echo $sql_dtls; die;

			$sql_result = sql_select($sql_dtls);
			$i = 1;
			?>
			<br>
			<br>
			<div style="width:100%;margin-top:20px;">
				<table align="left" cellspacing="0" width="1300" border="1" rules="all" class="rpt_table" style="margin-left:20px;">
					<thead bgcolor="#dddddd" align="center">
						<tr>
							<th width="30" rowspan="2">SL</th>
							<th width="70" rowspan="2">Item Category</th>
							<th width="110" rowspan="2">Item Group</th>
							<th width="160" rowspan="2">Item Description</th>
							<th width="100" rowspan="2">Brand</th>
							<th width="50" rowspan="2">Origin</th>
							<th width="50" rowspan="2">Lot</th>
							<th width="40" rowspan="2">UOM</th>
							<th width="70" rowspan="2">WO/PI Qnty.</th>
							<th width="70" rowspan="2">Previous Recv Qnty</th>
							<th width="70" rowspan="2">Today Recv. Qnty.</th>
							<th width="80" rowspan="2">WO/PI Qnty Bal.</th>
							<th width="240" colspan="3">Unit Price and Date</th>
							<th width="100" rowspan="2">Rate</th>
							<th width="100" rowspan="2">Amount</th>
							<th width="200" rowspan="2">Comments</th>
						</tr>
						<tr>
							<th width="80">Maximum</th>
							<th width="80">Minimum</th>
							<th width="80">Last</th>
						</tr>
					</thead>
					<tbody>
						<?
						foreach ($sql_result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							$order_qnty = $row[csf('order_qnty')];
							$order_qnty_sum += $order_qnty;

							$order_amount = $row[csf('order_amount')];
							$order_amount_sum += $order_amount;

							$balance_qnty = ($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]] - ($row[csf('order_qnty')] + $order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]));
							$balance_qnty_sum += $balance_qnty;

							$desc = $row[csf('item_description')];

							if ($row[csf('item_size')] != "") {
								$desc .= ", " . $row[csf('item_size')];
							}
						?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td><? echo $i; ?></td>
								<td><? echo $item_category[$row[csf('item_category')]]; ?></td>
								<td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
								<td align="center"><? echo $row[csf('product_name_details')]; ?></td>
								<td align="center"><? echo $row[csf('brand_name')]; ?></td>
								<td align="center"><? echo $countryShortNameArr[$row[csf('origin')]]; ?></td>
								<td align="center"><? echo $row[csf('batch_lot')]; ?></td>
								<td align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
								<td align="right"><? echo number_format($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]], 2);
													$tot_ord_qnty += $wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]; ?></td>
								<td align="right"><? echo number_format($order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]], 2);
													$tot_prev_qnty += $order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]; ?></td>
								<td align="right"><? echo number_format($row[csf('order_qnty')], 2, '.', ','); ?></td>
								<td align="right"><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7) echo number_format($balance_qnty, 2, '.', ','); ?></td>

								<td align="right">
									<? echo  number_format($prod_min_max_rate_arr[$row[csf("prod_id")]]["max_order_rate"], 2) . "<hr/ style='border:1px solid black'>" . $prod_min_max_rate_arr[$row[csf("prod_id")]]["max_order_date"]; ?>
								</td>
								<td align="right">
									<? echo  number_format($prod_min_max_rate_arr[$row[csf("prod_id")]]["min_order_rate"], 2) . "<hr/ style='border:1px solid black'>" . $prod_min_max_rate_arr[$row[csf("prod_id")]]["min_order_date"]; ?>
								</td>
								<td align="right" title="mrr no = <? echo $last_rate_arr[$row[csf("prod_id")]]["recv_number"]; ?>">
									<?
									//echo  number_format($row[csf("order_rate")],2)."<hr/ style='border:1px solid black'><span>".$row[csf("transaction_date")]."</span>"; 
									echo  number_format($last_rate_arr[$row[csf("prod_id")]]["last_rate"], 2) . "<hr/ style='border:1px solid black'><span>" . $last_rate_arr[$row[csf("prod_id")]]["trans_date"] . "</span>";
									?>
								</td>

								<td align="right"><? echo number_format($row[csf('order_rate')], 4, '.', ','); ?></td>
								<td align="right"><? echo number_format($row[csf('order_amount')], 2, '.', ',');  ?></td>
								<td align="left"><? echo $row[csf('remarks')];  ?></td>
							</tr>
						<?
							$i++;
						}
						?>
						<tr bgcolor="#CCCCCC">
							<td align="right" colspan="8">Total</td>
							<td align="right"><? echo number_format($tot_ord_qnty, 2, '.', ','); ?></td>
							<td align="right"><? echo number_format($tot_prev_qnty, 2, '.', ','); ?></td>
							<td align="right"><? echo number_format($order_qnty_sum, 2, '.', ','); ?></td>
							<td align="right"><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7) echo number_format($balance_qnty_sum, 2, '.', ','); ?></td>
							<!-- <td align="right">&nbsp;</td> -->
							<td align="right" colspan="3">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right"><? echo number_format($order_amount_sum, 2, '.', ','); ?></td>
							<td align="right">&nbsp;</td>
						</tr>
					</tbody>
				</table>
				<table>
					<tr>
						<td colspan="15">
							<h3 align="center" style="margin-left:150px;"> In Words : &nbsp;<? echo number_to_words(number_format($order_amount_sum, 2, '.', ',')) . "( " . $currency[$dataArray[0][csf('currency_id')]] . " )"; ?></h3>
						</td>
					</tr>
				</table>
				<br>
				<?
				echo signature_table(11, $data[0], "1300px", '1', 40, $inserted_by);
				?>
			</div>
		</div>

		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {
				var value = valuess; //$("#barcodeValue").val();
				// alert(value)
				var btype = 'code39'; //$("input[name=btype]:checked").val();
				var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

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
				//$("#barcode_img_id").html('11');
				value = {
					code: value,
					rect: false
				};

				$("#bar_code").show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $dataArray[0][csf('recv_number')]; ?>');
		</script>
	<?
	exit();
}

if ($action == "general_item_receive_print_9") 
{
	extract($_REQUEST);
	$data = explode('__', $data);
	echo load_html_head_contents("General Item Receive Info", "../../../", 1, 1, $unicode);

	$countryShortNameArr = return_library_array("select id,short_name from lib_country where status_active=1 and is_deleted=0", 'id', 'short_name');
	$division_name_arr = return_library_array("select id, division_name from   lib_division", "id", "division_name");
	$department_name_arr = return_library_array("select id, department_name from   lib_department", "id", "department_name");

	$req_div_name_arr = return_library_array("select id, division_id from   inv_purchase_requisition_mst", "id", "division_id");
	$req_department_name_arr = return_library_array("select id, department_id from inv_purchase_requisition_mst", "id", "department_id");
	$req_no_arr = return_library_array("select wo_number, requisition_no from wo_non_order_info_mst where entry_form in(146,147)", "wo_number", "requisition_no");

	$user_name_arr = return_library_array("select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name", "id", "user_full_name");
	$lc_arr = return_library_array("select id, lc_number from  com_btb_lc_master_details", "id", "lc_number");

	$sql = " select id, recv_number, receive_basis, receive_purpose, booking_id, loan_party, gate_entry_no, receive_date, challan_no, challan_date, location_id, store_id, supplier_id, lc_no, currency_id, exchange_rate, source,supplier_referance,pay_mode,store_sl_no,rcvd_book_no, addi_challan_date,bill_no, bill_date, purchaser_name, carried_by, qc_check_by, receive_by,gate_entry_by,gate_entry_date,addi_rcvd_date,remarks from inv_receive_master where id='$data[1]'";
	//echo $sql;die;
	$dataArray = sql_select($sql);
	$rcv_basis = $dataArray[0][csf('receive_basis')];
	
	if ($dataArray[0][csf('receive_basis')] == 2 || $dataArray[0][csf('receive_basis')] == 1 || $dataArray[0][csf('receive_basis')] == 7) {

		if ($dataArray[0][csf('receive_basis')] == 2) // Wo
		{
			$wo_sql = sql_select("select a.id,a.wo_number,a.wo_date,a.requisition_no as requ_id,b.item_id,sum(b.supplier_order_quantity) as wo_qnty from  wo_non_order_info_mst  a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id='" . $dataArray[0][csf('booking_id')] . "' and a.status_active=1 and b.status_active=1 group by a.id,a.wo_number,a.requisition_no ,b.item_id,a.wo_date");
			foreach ($wo_sql as $row) {
				$wo_library[$row[csf("id")]]["wo_number"] = $row[csf("wo_number")];
				$wo_library[$row[csf("id")]]["wo_date"] = $row[csf("wo_date")];
				$wo_library_prod[$row[csf("id")]][$row[csf("item_id")]] = $row[csf("wo_qnty")];
				$requsition_id_arr[$row[csf("wo_number")]] = $row[csf("requ_id")];
			}
		} else if ($dataArray[0][csf('receive_basis')] == 1) // Pi
		{
			$sql_pi = sql_select("select a.id as pi_id, a.pi_number,b.work_order_no, b.item_prod_id as item_id , sum(b.quantity) as quantity from com_pi_master_details a , com_pi_item_details b where a.id=b.pi_id  and a.id='" . $dataArray[0][csf('booking_id')] . "' group by a.id, a.pi_number,b.work_order_no,b.item_prod_id");
			foreach ($sql_pi as $row) {
				$pi_library[$row[csf("pi_id")]] = $row[csf("pi_number")];
				$wo_library_prod[$row[csf("pi_id")]][$row[csf("item_id")]] += $row[csf("quantity")];

				$pi_wo_no_library[$row[csf("pi_number")]] = $row[csf("work_order_no")];
			}
		} else // Req.
		{
			$sql_req = sql_select("select a.id as req_id, a.requ_no,a.requisition_date,a.division_id,a.department_id, b.product_id as item_id , sum(b.quantity) as quantity from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id  and a.id='" . $dataArray[0][csf('booking_id')] . "' group by a.id,a.requ_no,a.division_id,a.department_id,b.product_id,a.requisition_date");
			foreach ($sql_req as $row) {
				$requisition_library[$row[csf("req_id")]]["requ_no"] = $row[csf("requ_no")];
				$requisition_library[$row[csf("req_id")]]["requisition_date"] = $row[csf("requisition_date")];
				$wo_library_prod[$row[csf("req_id")]][$row[csf("item_id")]] = $row[csf("quantity")];

				$division_library[$row[csf("requ_no")]] = $row[csf("division_id")];
				$department_library[$row[csf("requ_no")]] = $row[csf("department_id")];
			}
		}


		$order_prev_sql = sql_select("select a.booking_id,b.prod_id,sum(b.order_qnty) as wo_prev_qnty from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.booking_id='" . $dataArray[0][csf('booking_id')] . "' and a.id !='" . $dataArray[0][csf('id')] . "'  and a.status_active=1 and b.status_active=1 group by a.booking_id,b.prod_id");
		foreach ($order_prev_sql as $row) {
			$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]] = $row[csf("wo_prev_qnty")];
		}
	}

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

	$supplier_res = sql_select("select id,supplier_name,address_1 from  lib_supplier");
	foreach ($supplier_res as $val) {
		$supplier_library[$val[csf("id")]]["supplier_name"] = $val[csf("supplier_name")];
		$supplier_library[$val[csf("id")]]["supplier_address"] = $val[csf("address_1")];
	}


	$prod_min_max_rate_res = sql_select("select prod_id, max(order_rate) max_order_rate,max(transaction_date) keep (dense_rank first order by order_rate desc) max_order_date,
	min(order_rate)min_order_rate,min(transaction_date) keep (dense_rank first order by order_rate asc) min_order_date
	from inv_transaction where transaction_type=1 and status_active = 1
	group by prod_id");

	foreach ($prod_min_max_rate_res as $val) {
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["max_order_rate"] = $val[csf("max_order_rate")];
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["max_order_date"] = $val[csf("max_order_date")];
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["min_order_rate"] = $val[csf("min_order_rate")];
		$prod_min_max_rate_arr[$val[csf("prod_id")]]["min_order_date"] = $val[csf("min_order_date")];
	}

	//=================Last Rate============>>>>>================

	//$last_rate_res = sql_select("select prod_id,order_rate,transaction_date, mst_id from inv_transaction where transaction_type=1 and status_active = 1 and transaction_date <= '".$dataArray[0][csf('receive_date')]."' and mst_id < ".$dataArray[0][csf('id')]." order by mst_id desc");

	$last_rate_res = sql_select("select a.prod_id,a.order_rate,a.transaction_date, a.mst_id, b.recv_number
		from inv_transaction a, inv_receive_master b
		where a.mst_id = b.id and a.transaction_type=1 and a.status_active = 1 
		and a.transaction_date <= '" . $dataArray[0][csf('receive_date')] . "' and a.mst_id < " . $dataArray[0][csf('id')] . " 
		 order by a.mst_id desc");


	$prodDupliChkArr = array();
	foreach ($last_rate_res as $value) {
		if ($prodDupliChkArr[$value[csf("prod_id")]] == "") {
			$prodDupliChkArr[$value[csf("prod_id")]] = $value[csf("prod_id")];
			$last_rate_arr[$value[csf("prod_id")]]["last_rate"] = $value[csf("order_rate")];
			$last_rate_arr[$value[csf("prod_id")]]["trans_date"] = $value[csf("transaction_date")];
			$last_rate_arr[$value[csf("prod_id")]]["recv_number"] = $value[csf("recv_number")];
		}
	}
	unset($last_rate_res);

	//=======================================<<<<================

	$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$item_name_arr = return_library_array("select id, item_name from  lib_item_group", "id", "item_name");

	?>
	<style type="text/css">
		#top_table tr td {
			vertical-align: top;
		}
	</style>
	<div style="width:1170px;">
		<table width="1150" cellspacing="1" align="right" id="top_table">
			<tr>
				<td width="100" style="font-size:xx-large" rowspan="2">
					<?
					$cbo_company_name = $data[0];
					$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$cbo_company_name'", "image_location");
					?>
					<img src='../../../<? echo $image_location; ?>' height='40' align="left" style="width: 100px;" />
				</td>
				<td colspan="8" style="font-size:xx-large; justify-content: center;text-align: center;">
					<strong><? echo $company_library[$data[0]]; ?></strong><br>
					<span style="font-size:14px">
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
							Province No: <? echo $result['province']; ?>
							Country: <? echo $country_arr[$result['country_id']]; ?><br>
							Email Address: <? echo $result['email']; ?>
							Website No: <? echo $result['website'];
									}
										?>
					</span>
				</td>
				<td width="200">

				</td>
			</tr>
			<tr>
				<td colspan="8" align="center" style="font-size:x-large;"><strong><u>Material Receiving Report</u></strong></td>
			</tr>
			</table>

			<table width="1150" cellspacing="1" align="right" id="top_table" style="margin-top:15px;">
			<tr>
				<td width="150"><strong>MRR Number</strong></td>
				<td width="180">:<? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="100"><strong>Receive No</strong></td>
				<td width="130">:<? echo $dataArray[0][csf('rcvd_book_no')]; ?></td>
				<td width="100"><strong>Receive Basis</strong></td>
				<td width="220">:<? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?>&nbsp;&nbsp;</td>
				<td width="120"><strong>Purchase By</strong></td>
				<td width="180">:<? echo $user_name_arr[$dataArray[0][csf('purchaser_name')]]; ?></td>

			</tr>
			<tr>
				<td><strong>Mrr Date</strong></td>
				<td>:<? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
				<td><strong>Receive Date</strong></td>
				<td>:<? echo change_date_format($dataArray[0][csf('addi_rcvd_date')]); ?></td>
				<td><strong>L/C No</strong></td>
				<td>:<? echo $lc_arr[$dataArray[0][csf('lc_no')]]; ?></td>
				<td><strong>Delivered By</strong></td>
				<td>:<? echo $user_name_arr[$dataArray[0][csf('carried_by')]]; ?></td>
			</tr>
			<tr>
				<td><strong>WO/PI/Req.No</strong></td>
				<td>:
					<?
					if ($dataArray[0][csf('receive_basis')] == 1) // PI
					{
						echo $pi_library[$dataArray[0][csf('booking_id')]];
					} else if ($dataArray[0][csf('receive_basis')] == 2) // WO
					{
						echo $wo_library[$dataArray[0][csf('booking_id')]]["wo_number"];
					} else  if ($dataArray[0][csf('receive_basis')] == 7) // Req.
					{
						echo $requisition_library[$dataArray[0][csf('booking_id')]]["requ_no"];
					} else {
						echo "Independent";
					}
					?></td>
				<td><strong>Challan No</strong></td>
				<td>:<? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Challan Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('challan_date')]); ?></td>
				<td><strong>Supplier</strong></td>
				<td>:<? echo $supplier_library[$dataArray[0][csf('supplier_id')]]["supplier_name"]; ?></td>

			</tr>
			<tr>
				<td><strong>WO/PI/Req.No Date</strong></td>
				<td>:
					<?
					if ($dataArray[0][csf('receive_basis')] == 1) // PI
					{
						echo $pi_library[$dataArray[0][csf('booking_id')]];
					} else if ($dataArray[0][csf('receive_basis')] == 2) // WO
					{
						echo $wo_library[$dataArray[0][csf('booking_id')]]["wo_date"];
					} else  if ($dataArray[0][csf('receive_basis')] == 7) // Req.
					{
						echo $requisition_library[$dataArray[0][csf('booking_id')]]["requisition_date"];
					} else {
						echo "";
					}
					?>
				</td>
				<td><strong>Challan Date</strong></td>
				<td>:<? echo change_date_format($dataArray[0][csf('addi_challan_date')]); ?></td>
				<td><strong>Supplier Address</strong></td>
				<td>
					<p>:<? echo $supplier_library[$dataArray[0][csf('supplier_id')]]["supplier_address"]; ?></p>
				</td>
				<td><strong>Received By </strong></td>
				<td>:<? echo $user_name_arr[$dataArray[0][csf('receive_by')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Gate Entry No</strong></td>
				<td>:<? echo $dataArray[0][csf('gate_entry_no')]; ?></td>
				<td><strong>Bill No</strong></td>
				<td>:<? echo $dataArray[0][csf('bill_no')]; ?></td>
				<td><strong>Store Name</strong></td>
				<td>:<strong><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></strong></td>
				<td><strong>Gate Entry By </strong></td>
				<td>:<? echo $user_name_arr[$dataArray[0][csf('gate_entry_by')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Gate Entry Date</strong></td>
				<td>:<? echo change_date_format($dataArray[0][csf('gate_entry_date')]); ?></td>
				<td><strong>Bill Date</strong></td>
				<td>:<? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
				<td><strong>Store Sl No</strong></td>
				<td>:<strong><? echo $dataArray[0][csf('store_sl_no')]; ?></strong></td>
				<td><strong>Prepared By</strong></td>
				<td>:<? echo $user_name_arr[$user_id]; ?></td>
			</tr>
			<tr>
				<td><strong>Pay Mode</strong></td>
				<td>:<?
						if ($dataArray[0][csf('receive_basis')] == 1 && $dataArray[0][csf('pay_mode')] == 0) {
							echo $pay_mode[2]; //PI basis pay mode will be import
						} else {
							echo $pay_mode[$dataArray[0][csf('pay_mode')]];
						}

						?>
				</td>
				<td><strong>Currency</strong></td>
				<td>:<? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
				<td><strong>Source</strong></td>
				<td>:<? echo $source[$dataArray[0][csf('source')]]; ?></td>
				<td><strong>QC Checked By</strong></td>
				<td>:<? echo $user_name_arr[$dataArray[0][csf('qc_check_by')]]; ?></td>
			</tr>
			<tr>
				<td rowspan="2" valign="top"><strong>Barcode</strong></td>:
				<td rowspan="2" colspan="3" valign="top" id="bar_code"></td>
				<td><strong>Exchange Rate</strong></td>
				<td>:<? echo $dataArray[0][csf('exchange_rate')]; ?></td>
				<!-- <td colspan="2"></td> -->
				<td><strong>Remark</strong></td>
				<td>:<? echo $dataArray[0][csf('remarks')];?></td>
			</tr>
			<tr>
				<td colspan="6"></td>
			</tr>
		</table>
		<?
		if ($db_type == 2) {
			$sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category,c.id as product_id,
			b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks, b.batch_lot,
			(c.sub_group_name||' '|| c.item_description || ' '|| c.item_size) as product_name_details,c.item_number,c.item_group_id, c.item_description,c.item_code, c.brand_name, c.origin, b.transaction_date
			from inv_receive_master a, inv_transaction b, product_details_master c 
			where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1 
			order by b.id";
		} else {
			$sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category,c.id as product_id,
			b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks, b.batch_lot,
			concat(c.sub_group_name,c.item_description, c.item_size) as product_name_details, c.item_number, c.item_group_id, c.item_description,c.item_code, c.brand_name, c.origin, b.transaction_date
			from inv_receive_master a, inv_transaction b, product_details_master c 
			where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1
			order by b.id";
		}

		$sql_result = sql_select($sql_dtls);
		$i = 1;
		?>
		<br>
		<br>
		<br>
		<div style="width:100%;margin-top:20px;">
		<br>
			<table align="left" cellspacing="0" width="1150" border="1" rules="all" class="rpt_table" style="margin-left:20px;margin-top:20px;">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30">SL</th>
						<!-- <th width="40" rowspan="2">Item Code</th> -->
						<!-- <th width="40" rowspan="2">Item Number</th> -->
						<th width="70">Item Category</th>
						<th width="150">Item Group</th>
						<th width="200">Item Description</th>
						<!-- <th width="70" rowspan="2">Brand</th> -->
						<!-- <th width="50" rowspan="2">Origin</th> -->
						<!-- <th width="50" rowspan="2">Lot</th> -->
						<th width="50">UOM</th>
						<th width="100">WO/PI Qnty.</th>
						<th width="100">Previous Recv Qnty</th>
						<th width="100">Today Recv. Qnty.</th>
						<th width="100">WO/PI Qnty Bal.</th>
						<th width="100">Comments</th>
						<!-- <th width="240" colspan="3">Unit Price and Date</th> -->
						<!-- <th width="100" rowspan="2">Rate</th> -->
						<!-- <th rowspan="2">Amount</th> -->
					</tr>
					<!-- <tr>
						<th width="80">Maximum</th>
						<th width="80">Minimum</th>
						<th width="80">Last</th>
					</tr> -->
				</thead>
				<tbody>
					<?
					foreach ($sql_result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$order_qnty = $row[csf('order_qnty')];
						$order_qnty_sum += $order_qnty;

						$order_amount = $row[csf('order_amount')];
						$order_amount_sum += $order_amount;

						$balance_qnty = ($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]] - ($row[csf('order_qnty')] + $order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]));
						$balance_qnty_sum += $balance_qnty;

						$desc = $row[csf('item_description')];

						if ($row[csf('item_size')] != "") {
							$desc .= ", " . $row[csf('item_size')];
						}
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td><? echo $i; ?></td>
							<!-- <td><?// echo $row[csf('item_code')]; ?></td> -->
							<!-- <td><?//echo $row[csf('item_number')]; ?></td> -->
							<td><? echo $item_category[$row[csf('item_category')]]; ?></td>
							<td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
							<td align="center"><? echo $row[csf('product_name_details')]; ?></td>
							<!-- <td align="center"><?// echo $row[csf('brand_name')]; ?></td> -->
							<!-- <td align="center"><?// echo $countryShortNameArr[$row[csf('origin')]]; ?></td> -->
							<!-- <td align="center"><?// echo $row[csf('batch_lot')]; ?></td> -->
							<td align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
							<td align="right"><? echo number_format($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]], 2);
												$tot_ord_qnty += $wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]; ?></td>
							<td align="right"><? echo number_format($order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]], 2);
												$tot_prev_qnty += $order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]; ?></td>
							<td align="right"><? echo number_format($row[csf('order_qnty')], 2, '.', ','); ?></td>
							<td align="right"><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7) echo number_format($balance_qnty, 2, '.', ','); 
							$balance_qnty_sum += $balance_qnty;
							?></td>
							<td><? echo $row[csf('remarks')];?> </td>
							<!-- <td align="right">
								<? //echo  number_format($prod_min_max_rate_arr[$row[csf("prod_id")]]["max_order_rate"], 2) . "<hr/ style='border:1px solid black'>" . $prod_min_max_rate_arr[$row[csf("prod_id")]]["max_order_date"]; ?>
							</td>
							<td align="right">
								<?// echo  number_format($prod_min_max_rate_arr[$row[csf("prod_id")]]["min_order_rate"], 2) . "<hr/ style='border:1px solid black'>" . $prod_min_max_rate_arr[$row[csf("prod_id")]]["min_order_date"]; ?>
							</td>
							<td align="right" title="mrr no = <?// echo $last_rate_arr[$row[csf("prod_id")]]["recv_number"]; ?>">
								<?
								//echo  number_format($row[csf("order_rate")],2)."<hr/ style='border:1px solid black'><span>".$row[csf("transaction_date")]."</span>"; 
								//echo  number_format($last_rate_arr[$row[csf("prod_id")]]["last_rate"], 2) . "<hr/ style='border:1px solid black'><span>" . $last_rate_arr[$row[csf("prod_id")]]["trans_date"] . "</span>";
								?>
							</td>
							<td align="right"><?// echo number_format($row[csf('order_rate')], 4, '.', ','); ?></td>
							<td align="right"><?// echo number_format($row[csf('order_amount')], 2, '.', ',');  ?></td> -->
						</tr>
					<?
						$i++;
					}
					?>
					<tr bgcolor="#CCCCCC">
						<td align="right" colspan="8">Total</td>
						<td align="right"><? if ($rcv_basis == 2 || $rcv_basis == 1 || $rcv_basis == 7) 
						echo number_format($balance_qnty_sum, 2, '.', ','); ?></td>
						<td align="right">&nbsp;</td>
						<!-- <td align="right">&nbsp;</td> -->
						<!-- <td align="right">&nbsp;</td>
						<td align="right" colspan="3"><?// echo number_format($order_amount_sum, 2, '.', ','); ?></td> -->

					</tr>
					
				</tbody>
			</table>
			<table align="left" cellspacing="0" width="1150" border="1" rules="all">
				<br>
				<tr>
					<td colspan="15">
						<h3 align="left" style="margin-left:20px;"> In Words : &nbsp;<? echo number_to_words(number_format($balance_qnty_sum, 2, '.', ',')) . "( " . $currency[$dataArray[0][csf('currency_id')]] . " )"; ?></h3>
					</td>
				</tr>
			</table>
			 
				<?
				echo signature_table(11, $data[0], "1300px", '1', 10, $inserted_by);
				?>
			 
		</div>
	</div>

	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			// alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

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
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#bar_code").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $dataArray[0][csf('recv_number')]; ?>');
	</script>
	<?
	exit();
}


if ($action == "file_upload") {
	header("Content-Type: application/json");
	$filename = time() . $_FILES['file']['name'];
	$location = "../../../file_upload/" . $filename;
	//echo "0**".$filename; die;
	$uploadOk = 1;
	if (empty($hidden_mrr_id)) {
		$hidden_mrr_id = $_GET['hidden_mrr_id'];
	}

	if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
		$uploadOk = 1;
	} else {
		$uploadOk = 0;
	}
	// echo "0**".$uploadOk; die;
	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}

	$id = return_next_id("id", "COMMON_PHOTO_LIBRARY", 1);
	$data_array .= "(" . $id . "," . $hidden_mrr_id . ",'general_item_receive','file_upload/" . $filename . "','2','" . $filename . "','" . $pc_date_time . "')";
	$field_array = "id,master_tble_id,form_name,image_location,file_type,real_file_name,insert_date";
	$rID = sql_insert("COMMON_PHOTO_LIBRARY", $field_array, $data_array, 1);

	if ($db_type == 0) {
		if ($rID == 1 && $uploadOk == 1) {
			mysql_query("COMMIT");
			echo "0**" . $new_system_id[0] . "**" . $hidden_mrr_id;
		} else {
			mysql_query("ROLLBACK");
			echo "10**" . $hidden_mrr_id;
		}
	} else if ($db_type == 2 || $db_type == 1) {
		if ($rID == 1 && $uploadOk == 1) {
			oci_commit($con);
			echo "0**" . $new_system_id[0] . "**" . $hidden_mrr_id;
		} else {
			oci_rollback($con);
			echo "10**" . $rID . "**" . $uploadOk . "**INSERT INTO COMMON_PHOTO_LIBRARY(" . $field_array . ") VALUES " . $data_array;
		}
	}
	disconnect($con);
	die;
}

	?>