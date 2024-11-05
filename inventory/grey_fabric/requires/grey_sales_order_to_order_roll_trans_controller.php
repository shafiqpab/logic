<?

header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

// Linking selected report buttons with this page
if ($action == "company_wise_report_button_setting") {
	extract($_REQUEST);

	$print_report_format = return_field_value("format_id", " lib_report_template", "template_name ='" . $data . "'  and module_id=6 and report_id=283 and is_deleted=0 and status_active=1");
	$print_report_format_arr = explode(",", $print_report_format);

	echo "$('#print2').hide();\n";
	echo "$('#print3').hide();\n";
	echo "$('#print4').hide();\n";


	if ($print_report_format != "") {
		foreach ($print_report_format_arr as $id) {
			if ($id == 66) {
				echo "$('#print2').show();\n";
			}
			else if($id == 137) {
				echo "$('#print4').show();\n";
			}
			else if($id == 85) {
				echo "$('#print3').show();\n";
			}

		}
	} else {
		echo "$('#print2').show();\n";
		echo "$('#print3').show();\n";
		echo "$('#print4').show();\n";

	}

	exit();
}

if ($action == "load_drop_store_from") {
	$data = explode("_", $data);
	$category_id = 13;
	echo create_drop_down("cbo_from_store_name", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$data[1] and b.category_type=$category_id and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "--Select store--", 0, "");
}

if ($action == "load_drop_store_to") {
	$data = explode("_", $data);
	$category_id = 13;
	echo create_drop_down("cbo_store_name", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$data[1] and b.category_type=$category_id and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "--Select store--", 0, "fn_load_floor(this.value);reset_room_rack_shelf('','cbo_store_name');");
}

if ($action == "load_drop_from_store_balnk") {
	echo create_drop_down("cbo_from_store_name", 160, $blank_array, "", 1, "--Select store--", 0, "");
}

if ($action == "load_drop_store_balnk") {
	echo create_drop_down("cbo_store_name", 160, $blank_array, "", 1, "--Select store--", 0, "");
}

if ($action == "floor_list") {
	$data_ref = explode("__", $data);
	$floor_arr = array();
	$floor_data = sql_select("select b.floor_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and b.store_id='$data_ref[1]' and a.company_id='$data_ref[0]' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach ($floor_data as $row) {
		$floor_arr[$row[csf('floor_id')]] = $row[csf('floor_room_rack_name')];
	}
	$jsFloor_arr = json_encode($floor_arr);
	echo $jsFloor_arr;
	die();
}

if ($action == "room_list") {
	$data_ref = explode("__", $data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$room_arr = array();
	$room_data = sql_select("select b.room_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and b.store_id='$data_ref[1]' and a.company_id='$data_ref[0]' and b.floor_id in($data_ref[2]) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach ($room_data as $row) {
		$room_arr[$row[csf('room_id')]] = $row[csf('floor_room_rack_name')];
	}
	$jsRoom_arr = json_encode($room_arr);
	echo $jsRoom_arr;
	die();
}

if ($action == "rack_list") {
	$data_ref = explode("__", $data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$rack_arr = array();
	$rack_data = sql_select("select b.rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and b.store_id='$data_ref[1]' and a.company_id='$data_ref[0]' and b.room_id in($data_ref[2]) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach ($rack_data as $row) {
		$rack_arr[$row[csf('rack_id')]] = $row[csf('floor_room_rack_name')];
	}
	$jsRack_arr = json_encode($rack_arr);
	echo $jsRack_arr;
	die();
}

if ($action == "shelf_list") {
	$data_ref = explode("__", $data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$shelf_arr = array();
	$shelf_data = sql_select("select b.shelf_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and b.store_id='$data_ref[1]' and a.company_id='$data_ref[0]' and b.rack_id in($data_ref[2]) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach ($shelf_data as $row) {
		$shelf_arr[$row[csf('shelf_id')]] = $row[csf('floor_room_rack_name')];
	}
	$jsShelf_arr = json_encode($shelf_arr);
	echo $jsShelf_arr;
	die();
}

if ($action == "bin_list") {
	$data_ref = explode("__", $data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$bin_arr = array();
	$bin_data = sql_select("select b.bin_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and b.store_id='$data_ref[1]' and a.company_id='$data_ref[0]' and b.shelf_id in($data_ref[2]) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach ($bin_data as $row) {
		$bin_arr[$row[csf('bin_id')]] = $row[csf('floor_room_rack_name')];
	}
	$jsBin_arr = json_encode($bin_arr);
	echo $jsBin_arr;
	die();
}

if ($action == "bodypart_list") {
	$data_ref = explode("__", $data);

	$bodyPart_arr = array();

	// echo "SELECT b.id, b.body_part_id from fabric_sales_order_mst a, fabric_sales_order_dtls b  where a.id=b.mst_id and a.company_id= '$data_ref[0]' and b.mst_id='$data_ref[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, b.body_part_id order by b.id";
	$body_part_sql = sql_select("SELECT b.id, b.body_part_id from fabric_sales_order_mst a, fabric_sales_order_dtls b  where a.id=b.mst_id and a.company_id= '$data_ref[0]' and b.mst_id='$data_ref[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, b.body_part_id order by b.id");


	foreach ($body_part_sql as $row) {
		$bodyPart_arr[$row[csf('body_part_id')]] = $body_part[$row[csf('body_part_id')]];
	}

	$jsBodyPart_arr = json_encode($bodyPart_arr);
	echo $jsBodyPart_arr;
	die();
}

if ($action == "load_drop_down_buyer") {
	$data = explode("_", $data);
	$with_in_group = $data[0];
	$company_id = $data[1];

	if ($company_id == 0) {
		echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "--Select Buyer--", 0, "");
	} else {
		if ($with_in_group == 1) {
			echo create_drop_down("cbo_buyer_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Buyer--", "0", "", "");
		} else if ($with_in_group == 2) {
			echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "--Select Buyer--", $selected, "", 0);
		} else {
			echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "--Select Buyer--", 0, "");
		}
	}

	exit();
}

if ($action == "requ_variable_settings") {
	extract($_REQUEST);
	$requisition_type = return_field_value("user_given_code_status", "variable_settings_inventory", "company_name='$cbo_company_id' and variable_list=30 and item_category_id=13 and status_active=1 and is_deleted=0");

	$variable_inventory = return_field_value("store_method", "variable_settings_inventory", "company_name='$cbo_company_id' and item_category_id=13 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$requisition_and_order_basis = 1; // 1 (Yes) use for Urmi
	echo $requisition_type . '**' . $variable_inventory . '**' . $requisition_and_order_basis;
	exit();
}

if ($action == "orderToorderTransferRequisition_popup") {
	echo load_html_head_contents("Order To Order Transfer Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(data) {
			$('#transfer_id').val(data);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:780px;">
			<form name="searchdescfrm" id="searchdescfrm">
				<fieldset style="width:760px;margin-left:10px">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="550" class="rpt_table">
						<thead>
							<th>Search By</th>
							<th width="240" id="search_by_td_up">Please Enter Transfer ID</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
								<input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								$search_by_arr = array(1 => "Requisition ID", 2 => "Challan No.");
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../../') ";
								echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td id="search_by_td">
								<input type="text" style="width:130px;" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $requisition_order_basis; ?>, 'create_transfer_requisition_search_list_view', 'search_div', 'grey_sales_order_to_order_roll_trans_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

if ($action == 'create_transfer_requisition_search_list_view') {
	$data = explode("_", $data);
	$search_string = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$requisition_order_basis = $data[3];

	if ($search_by == 1)
		$search_field = "transfer_prefix_number";
	else
		$search_field = "challan_no";

	if ($db_type == 0) $year_field = "YEAR(insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(insert_date,'YYYY') as year,";
	else $year_field = ""; //defined Later

	$sql_trans_requ = sql_select("SELECT a.transfer_requ_id from inv_item_transfer_mst a, inv_item_transfer_dtls b
	where a.id=b.mst_id and a.item_category=13 and a.company_id=$company_id and a.transfer_criteria=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=133 and a.transfer_requ_id IS NOT NULL
	group by a.transfer_requ_id");

	$requ_id = "";
	foreach ($sql_trans_requ as $row) {
		if ($requ_id == "") {
			$requ_id .= $row[csf('transfer_requ_id')];
		} else {
			$requ_id .= ', ' . $row[csf('transfer_requ_id')];
		}
	}
	//echo $requ_id;
	if ($requ_id != "") {
		$requ_id_cond = "and id not in($requ_id)";
	}

	$sql = "SELECT id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_requ_mst where item_category=13 and company_id=$company_id and $search_field='$search_string' and transfer_criteria=4 and entry_form=352 and requisition_status=1 and status_active=1 and is_deleted=0 order by id desc"; // $requ_id_cond

	//echo $sql;

	$arr = array(3 => $company_arr, 5 => $item_transfer_criteria, 6 => $item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,80,90,130", "750", "250", 0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '', '', '0,0,0,0,3,0,0');

	exit();
}

if ($action == 'populate_data_from_transfer_requi_master') {
	$data_array = sql_select("SELECT a.transfer_system_id, a.challan_no, a.company_id, a.transfer_date, a.item_category, a.from_order_id, a.to_order_id, a.ready_to_approve, a.is_approved, sum(b.transfer_qnty) as transfer_qnty from inv_item_transfer_requ_mst a,  inv_item_transfer_requ_dtls b
		where a.id=b.mst_id and a.id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.transfer_system_id, a.challan_no, a.company_id, a.transfer_date, a.item_category, a.from_order_id, a.to_order_id, a.ready_to_approve, a.is_approved");
	foreach ($data_array as $row) {
		echo "document.getElementById('txt_requisition_id').value 			= '" . $data . "';\n";
		echo "document.getElementById('txt_requisition_no').value 			= '" . $row[csf("transfer_system_id")] . "';\n";
		echo "document.getElementById('cbo_company_id').value 				= '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('txt_challan_no').value 				= '" . $row[csf("challan_no")] . "';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '" . change_date_format($row[csf("transfer_date")]) . "';\n";
		echo "document.getElementById('hidd_requi_qty').value 				= '" . $row[csf("transfer_qnty")] . "';\n";
		echo "get_php_form_data('" . $row[csf("from_order_id")] . "**from'" . ",'populate_data_from_order','requires/grey_sales_order_to_order_roll_trans_controller');\n";
		echo "get_php_form_data('" . $row[csf("to_order_id")] . "**to'" . ",'populate_data_from_order','requires/grey_sales_order_to_order_roll_trans_controller');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		//echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_grey_transfer_entry',1,1);\n";

		exit();
	}
}

if ($action == "barcode_popup") //  Roll Scan/Browse Pupup
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1, $unicode, 1, 1);
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array();

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(str) {
			toggle(document.getElementById('search' + str), '#FFFFCC');

			var total_selected_val = $('#hidden_selected_row_total').val() * 1; // txt_individual_qty
			var total_selected_pcs = $('#hidden_selected_pcs_total').val() * 1; // txt_individual_pcs


			if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_individual_id' + str).val());
				total_selected_val = total_selected_val + $('#txt_individual_qty' + str).val() * 1;
				total_selected_pcs = total_selected_pcs + $('#txt_individual_pcs' + str).val() * 1;

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
				total_selected_val = total_selected_val - $('#txt_individual_qty' + str).val() * 1;
				total_selected_pcs = total_selected_pcs - $('#txt_individual_pcs' + str).val() * 1;

			}
			var id = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
			}
			id = id.substr(0, id.length - 1);

			$('#hidden_barcode_nos').val(id);
			$('#hidden_selected_row_total').val(total_selected_val.toFixed(2));
			$('#hidden_selected_pcs_total').val(total_selected_pcs.toFixed(2));


			if (id != "") {
				var no_of_roll = id.split(',').length;
			} else {
				var no_of_roll = "0";
			}
			$('#hidden_selected_row_count').val(no_of_roll);
		}

		function fnc_close() {
			parent.emailwindow.hide();
		}

		function reset_hide_field() {
			$('#hidden_barcode_nos').val('');
			selected_id = new Array();
		}

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;

			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				if ($("#search" + i).css("display") != "none") {
					js_set_value(i);
				}
			}
		}
	</script>



	<script>
		var tableFilters = {

			col_operation: {

				id: ["value_total_selected_value_td", "value_total_selected_pcs_td"],
				col: [18, 19],
				operation: ["sum", "sum"],
				write_method: ["innerHTML", "innerHTML"]
			}

		}
		setFilterGrid("tbl_list_search", -1, tableFilters);
	</script>

	</head>

	<body>
		<div align="center" style="width:1200px;">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:1200px; margin-left:2px;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="900" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Within Group</th>
							<th>Buyer Name</th>
							<th>Barcode No.</th>
							<th>IR/IB</th>
							<th>Sales Order No</th>
							<th>Sales/Booking No</th>
							<th width="170">Delivery Date Range</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:80px;" class="formbutton" />
								<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down("cbo_within_group", 70, $yes_no, "", 1, "--Select--", 0, "load_drop_down( 'grey_sales_order_to_order_roll_trans_controller', this.value+'_'+" . $company_id . ", 'load_drop_down_buyer', 'buyer_td' );");
								?>
							</td>
							<td id="buyer_td"><? echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "--Select Buyer--", 0, ""); ?></td>
							<td>
								<input type="text" style="width:80px;" class="text_boxes" name="barcode_no" id="barcode_no" placeholder="Barcode No." />
							</td>
							<td>
								<input type="text" style="width:80px;" class="text_boxes" name="txt_ref_no" id="txt_ref_no" placeholder="Int Ref." />
							</td>
							<td>
								<input type="text" style="width:80px;" class="text_boxes" name="txt_order_no" id="txt_order_no" placeholder="Enter Order No" />
							</td>
							<td>
								<input type="text" style="width:80px;" class="text_boxes" name="txt_sales_booking_no" id="txt_sales_booking_no" placeholder="Enter Sales/Booking No" />
							</td>
							<td>
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" readonly>
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" readonly>
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('txt_sales_booking_no').value+'_'+<? echo $cbo_store_name; ?>+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+document.getElementById('txt_ref_no').value+'_'+<? echo $requisition_id; ?>+'_'+document.getElementById('barcode_no').value, 'create_barcode_search_list_view', 'search_div', 'grey_sales_order_to_order_roll_trans_controller', 'setFilterGrid(\'tbl_list_search\',-1,tableFilters);')" style="width:80px;" />
							</td>
						</tr>
						<tr>
							<td colspan="7" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>





	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
	<?
	exit();
}




if ($action == "create_barcode_search_list_view") //  Roll Scan/Browse Pupup List view
{
	$data = explode('_', $data);
	//print_r($data);die;
	$company_id = $data[2];
	$with_in_group = $data[5];
	$sales_booking_no = $data[6];
	$store_id = $data[7];
	$transfer_cateria = $data[8];
	$int_ref = $data[9];
	$requisition_id = $data[10];
	$barcodeNo = $data[11];

	if ($int_ref != "") {
		$po_sql = "SELECT a.id, a.po_number, a.grouping, b.booking_no from wo_po_break_down a, wo_booking_dtls b
		where a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and a.grouping='$int_ref' and b.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0";
		// echo $po_sql;
		$po_sql_result = sql_select($po_sql);
		$refBooking_cond = "";
		foreach ($po_sql_result as $key => $row) {
			//$po_id_arr[$row[csf('id')]]=$row[csf('id')];
			$bookingNo_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
		}
		//$poIds=implode(",",array_unique(explode(",",$po_id_arr)));
		$refBooking_cond = " and e.sales_booking_no in('" . implode("','", $bookingNo_arr) . "') ";
		//echo $refBooking_cond;die;
	}

	if ($data[3] != "" &&  $data[4] != "") {
		if ($db_type == 0) {
			$delivery_date_cond = "and e.delivery_date between '" . change_date_format($data[3], "yyyy-mm-dd", "-") . "' and '" . change_date_format($data[4], "yyyy-mm-dd", "-") . "'";
		} else {
			$delivery_date_cond = "and e.delivery_date between '" . change_date_format($data[3], '', '', 1) . "' and '" . change_date_format($data[4], '', '', 1) . "'";
		}
	} else
		$delivery_date_cond = "";

	$arr = array(2 => $company_arr); //2=>$company_arr,
	if ($data[0] == 0) $buyer_cond = "";
	else $buyer_cond = "and e.buyer_id='$data[0]'";
	if ($data[1] != "") $po_cond = "and e.job_no_prefix_num='$data[1]'";
	else $po_cond = "";
	if ($with_in_group != 0) $within_group_cond = "and e.within_group='$with_in_group'";
	else $within_group_cond = "";
	if ($sales_booking_no != "") $sales_booking_no_cond = "and e.sales_booking_no='$sales_booking_no'";
	else $sales_booking_no_cond = "";

	if ($store_id > 0) {
		$store_cond = " and a.store_id=$store_id";
		$store_cond2 = " and b.to_store=$store_id";
	} else {
		$store_cond = "";
		$store_cond2 = "";
	}

	if(!empty($barcodeNo)){
		$barcodeCond = " and c.barcode_no='$barcodeNo'";
	}else{
		$barcodeCond = " ";
	}
	// =======================================
	$fso_sql = "SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.color_id as color_names, b.yarn_count, b.stitch_length, b.brand_id, b.body_part_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, c.rate,c.amount, 1 as type,c.booking_no,a.store_id, d.detarmination_id, d.gsm, d.dia_width, e.id, e.job_no, e.job_no_prefix_num, to_char(e.insert_date,'YYYY') as year, e.delivery_date, e.style_ref_no, e.buyer_id, e.booking_id, e.sales_booking_no,e.po_buyer, e.within_group
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, fabric_sales_order_mst e, product_details_master d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and c.po_breakdown_id=e.id  and b.trans_id<>0 and a.entry_form in(2,22,58,84) and c.entry_form in(2,22,58,84) and c.is_transfer!=6 and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_service=0 and a.company_id=$company_id $buyer_cond $po_cond $delivery_date_cond $within_group_cond $sales_booking_no_cond $store_cond $refBooking_cond $barcodeCond 
	union all
	select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.color_names, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_body_part as body_part_id, b.to_floor_id as floor_id, b.to_room as room, b.to_rack as rack,  b.to_shelf as self, b.to_bin_box as bin_box, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, c.rate,c.amount, 2 as type, c.booking_no, b.to_store as store_id, d.detarmination_id, d.gsm, d.dia_width, e.id, e.job_no, e.job_no_prefix_num, to_char(e.insert_date,'YYYY') as year, e.delivery_date, e.style_ref_no, e.buyer_id, e.booking_id, e.sales_booking_no,e.po_buyer, e.within_group
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, fabric_sales_order_mst e, product_details_master d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.from_prod_id=d.id and c.po_breakdown_id=e.id and a.entry_form in(133) and c.entry_form in(133) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_service=0 and a.company_id=$company_id $buyer_cond $po_cond $delivery_date_cond $within_group_cond $sales_booking_no_cond $store_cond2 $refBooking_cond $barcodeCond  order by barcode_no";
	 //echo $fso_sql;die;
	$result = sql_select($fso_sql);

	foreach ($result as $row) {
		$barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		$all_prod_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
		$all_fso_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
	}
	$barcode_arr = array_filter(array_unique($barcode_arr));
	$fso_id_arr = array_filter(array_unique($all_fso_id_arr));

	if (count($barcode_arr) > 0) {
		$all_barcode_nos = implode(",", $barcode_arr);
		$BarCond = $BarCond2 = $all_barcode_cond = $all_barcode_cond2 = "";

		if ($db_type == 2 && count($barcode_arr) > 999) {
			$barcode_arr_chunk = array_chunk($barcode_arr, 999);
			foreach ($barcode_arr_chunk as $chunk_arr) {
				$BarCond .= " a.barcode_no in(" . implode(",", $chunk_arr) . ") or ";
				$BarCond2 .= " b.barcode_no in(" . implode(",", $chunk_arr) . ") or ";
			}

			$all_barcode_cond .= " and (" . chop($BarCond, 'or ') . ")";
			$all_barcode_cond2 .= " and (" . chop($BarCond, 'or ') . ")";
		} else {
			$all_barcode_cond = " and a.barcode_no in($all_barcode_nos)";
			$all_barcode_cond2 = " and b.barcode_no in($all_barcode_nos)";
		}



		$all_prod_ids = implode(",", $all_prod_arr);
		$prodCond = $all_prod_id_cond = "";

		if ($db_type == 2 && count($all_prod_arr) > 999) {
			$all_prod_arr_chunk = array_chunk($all_prod_arr, 999);
			foreach ($all_prod_arr_chunk as $chunk_arr) {
				$prodCond .= " id in(" . implode(",", $chunk_arr) . ") or ";
			}
			$all_prod_id_cond .= " and (" . chop($prodCond, 'or ') . ")";
		} else {
			$all_prod_id_cond = " and id in($all_prod_ids)";
		}
	}

	if (!empty($barcode_arr)) {
		$scanned_barcode_arr = array();
		$barcodeData = sql_select("select a.barcode_no from pro_roll_details a where a.entry_form in(61) and a.is_returned=0 and a.status_active=1 and a.is_deleted=0 $all_barcode_cond");
		foreach ($barcodeData as $row) {
			$scanned_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		}

		$stitch_lot_sql = sql_select("SELECT a.barcode_no, a.receive_basis, a.booking_no, a.qc_pass_qnty_pcs, a.coller_cuff_size, b.stitch_length, b.yarn_lot, b.yarn_count, b.width, b.body_part_id, b.color_id, b.color_range_id, b.gsm, c.knitting_source, c.knitting_company from pro_roll_details a,pro_grey_prod_entry_dtls b, inv_receive_master c where a.dtls_id = b.id and b.mst_id = c.id and a.entry_form = 2 and a.status_active=1 and b.status_active =1 and b.is_deleted =0 and a.is_deleted =0 $all_barcode_cond");
		foreach ($stitch_lot_sql as $row) {
			$production_ref_arr[$row[csf("barcode_no")]]['stitch_length'] = $row[csf("stitch_length")];
			$production_ref_arr[$row[csf("barcode_no")]]['yarn_lot'] = $row[csf("yarn_lot")];
			$production_ref_arr[$row[csf("barcode_no")]]['yarn_count'] = $row[csf("yarn_count")];
			$production_ref_arr[$row[csf("barcode_no")]]['width'] = $row[csf("width")];
			$production_ref_arr[$row[csf("barcode_no")]]['gsm'] = $row[csf("gsm")];
			$production_ref_arr[$row[csf("barcode_no")]]['body_part_id'] = $row[csf("body_part_id")];
			$production_ref_arr[$row[csf("barcode_no")]]['receive_basis'] = $row[csf("receive_basis")];
			$production_ref_arr[$row[csf("barcode_no")]]['booking_no'] = $row[csf("booking_no")];
			$production_ref_arr[$row[csf("barcode_no")]]['color_id'] = $row[csf("color_id")];
			$production_ref_arr[$row[csf("barcode_no")]]['color_range_id'] = $color_range[$row[csf("color_range_id")]];
			$production_ref_arr[$row[csf("barcode_no")]]['qty_in_pcs'] = $row[csf("qc_pass_qnty_pcs")];
			$production_ref_arr[$row[csf("barcode_no")]]['size'] = $row[csf("coller_cuff_size")];
			$production_ref_arr[$row[csf("barcode_no")]]['knitting_source'] = $row[csf("knitting_source")];
			$production_ref_arr[$row[csf("barcode_no")]]['knitting_company'] = $row[csf("knitting_company")];
		}

		$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13 $all_prod_id_cond", 'id', 'product_name_details');
	}

	if (count($all_fso_id_arr) > 0) {
		$all_fso_id = implode(",", $all_fso_id_arr);
		$fso_idCond = $all_fso_id_cond = "";

		if ($db_type == 2 && count($all_fso_id_arr) > 999) {
			$barcode_arr_chunk = array_chunk($all_fso_id_arr, 999);
			foreach ($barcode_arr_chunk as $chunk_arr) {
				$fso_idCond .= " c.id in(" . implode(",", $chunk_arr) . ") or ";
			}

			$all_fso_id_cond .= " and (" . chop($fso_idCond, 'or ') . ")";
		} else {
			$all_fso_id_cond = " and c.id in($all_fso_id)";
		}

		$int_ref_sql = "SELECT a.id, a.po_number, a.grouping, b.booking_no, b.booking_mst_id
		from fabric_sales_order_mst c, wo_booking_dtls b, wo_po_break_down a
		where c.BOOKING_ID=b.BOOKING_MST_ID and b.po_break_down_id=a.id and c.within_group=1 and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $all_fso_id_cond";
		// echo $int_ref_sql;die;
		$int_ref_sql_result = sql_select($int_ref_sql);
		$int_ref_arr = array();
		foreach ($int_ref_sql_result as $key => $row) {
			$int_ref_arr[$row[csf('booking_no')]] = $row[csf('grouping')];
		}
	}

	$sql_requ = "SELECT b.barcode_no from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
	WHERE a.id=b.mst_id and a.entry_form in(352) and b.mst_id=$requisition_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	// echo $sql_requ;
	$sql_requ_result = sql_select($sql_requ);
	foreach ($sql_requ_result as $row) {
		$requisition_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}

	$floorRoomRackShelf_array = return_library_array("SELECT floor_room_rack_id, floor_room_rack_name FROM lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");
	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
	$colorArr = return_library_array("select id, color_name from lib_color where status_active=1", "id", "color_name");
	$yarnCountArr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1", "id", "yarn_count");
	$companyArr = return_library_array("select id, company_name from lib_company where status_active=1", "id", "company_name");
	$supplierArr = return_library_array("select id, supplier_name from lib_supplier where status_active=1", "id", "supplier_name");
	?>
	<style>
        .wrd_brk{word-break: break-all;word-wrap: break-word;}          
    </style>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1860" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="50">Sales Order No</th>
			<th width="50">Year</th>
			<th width="60">With in Group</th>
			<th width="110">Sales Order Buyer</th>
			<th width="110">Sales/Booking No</th>
			<th width="100">knitting Party</th>
			<th width="100">PO Buyer</th>
			<th width="50">IR/IB</th>
			<th width="100">Style Ref.</th>
			<th width="150">Fabric Description</th>
			<th width="90">Yarn Lot</th>
			<th width="60">Yarn Count</th>
			<th width="100">Stitch Length</th>

			<th width="40">Gsm</th>
			<th width="40">Dia</th>
			<th width="100">Color</th>
			<th width="90">Barcode No</th>
			<th width="50">Roll Qty.</th>
			<th width="50">Qty.In Pcs</th>
			<th width="50">Size</th>
			<th width="50">Floor</th>
			<th width="50">Room</th>
			<th width="50">Rack</th>
			<th width="50">Shelf</th>
			<th>Delivery Date</th>
		</thead>
	</table>
	<div style="width:1880px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1860" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			$total_grey_qnty = 0;
			$total_grey_pcs = 0;
			foreach ($result as $row) {
				if ($requisition_id > 0) // For Requisition Basis
				{
					if ($scanned_barcode_arr[$row[csf('barcode_no')]] == "" && $requisition_barcode_arr[$row[csf('barcode_no')]] != "") {
						$trans_flag = "";
						if ($row[csf('entry_form')] == 133) {
							$trans_flag = " (T)";
						}

						$stitch_length = $production_ref_arr[$row[csf("barcode_no")]]['stitch_length'];
						$yarn_lot = $production_ref_arr[$row[csf("barcode_no")]]['yarn_lot'];
						$yarnCount = $production_ref_arr[$row[csf("barcode_no")]]['yarn_count'];
						$dia_width = $production_ref_arr[$row[csf("barcode_no")]]['width'];
						$gsm = $production_ref_arr[$row[csf("barcode_no")]]['gsm'];
						$body_part_id = $production_ref_arr[$row[csf("barcode_no")]]['body_part_id'];
						$receive_basis = $production_ref_arr[$row[csf("barcode_no")]]['receive_basis'];
						$colorName = $production_ref_arr[$row[csf("barcode_no")]]['color_id'];
						$colorRange = $production_ref_arr[$row[csf("barcode_no")]]['color_range_id'];
						$qty_in_pcs = $production_ref_arr[$row[csf("barcode_no")]]['qty_in_pcs'];
						$size = $production_ref_arr[$row[csf("barcode_no")]]['size'];
						$knitting_source = $production_ref_arr[$row[csf("barcode_no")]]['knitting_source'];
						$knitting_company = $production_ref_arr[$row[csf("barcode_no")]]['knitting_company'];
						
						$knitting_party = '';
						if($knitting_source == 1)
						{
							$knitting_party = $companyArr[$knitting_company];
						}
						else
						{
							$knitting_party = $supplierArr[$knitting_company];
						}

						if ($receive_basis == 2) {
							$program_no = $production_ref_arr[$row[csf("barcode_no")]]['booking_no'];
						} else {
							$program_no = "";
						}

						//$yarn_count_array = array_unique(explode(",", $yarnCount));
						$yarn_count_array = array_unique(explode(",",chop($row[csf('yarn_count')],",")));
						$all_count = "";
						foreach ($yarn_count_array as $count_id) {
							$all_count .= $yarnCountArr[$count_id] . ",";
						}
						$all_count = chop($all_count, ",");

						$colorName_array = array_unique(explode(",", $colorName));
						$all_color = "";
						foreach ($colorName_array as $color_id) {
							$all_color .= $colorArr[$color_id] . ",";
						}
						$all_color = chop($all_color, ",");
						$construction_composition = $composition_arr[$row[csf('detarmination_id')]];
						$int_ref = $int_ref_arr[$row[csf('sales_booking_no')]];

						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
			?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
							<td width="30" align="center"><? echo $i; ?>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>" />
								<input type="hidden" name="txt_individual_qty" id="txt_individual_qty<?php echo $i; ?>" value="<?php echo $row[csf('qnty')]; ?>" />

								<input type="hidden" name="txt_individual_pcs" id="txt_individual_pcs<?php echo $i; ?>" value="<?php echo $production_ref_arr[$row[csf("barcode_no")]]['qty_in_pcs']; ?>" />
							</td>
							<td width="50">
								<p><? echo $row[csf('job_no_prefix_num')]; ?></p>
							</td>
							<td width="50">
								<p><? echo $row[csf('year')]; ?></p>
							</td>
							<td width="60">
								<p><? echo $yes_no[$row[csf('within_group')]]; ?></p>
							</td>
							<td width="110">
								<p><? echo $buyer_name; ?></p>
							</td>
							<td width="110">
								<p><? echo $row[csf('sales_booking_no')]; ?></p>
							</td>
							<td width="100">
								<p><? echo $knitting_party; ?></p>
							</td>
							<td width="100">
								<p><? echo $buyer_arr[$row[csf('po_buyer')]]; ?></p>
							</td>
							<td width="50">
								<p><? echo $int_ref; ?></p>
							</td>
							<td width="100">
								<p><? echo $row[csf('style_ref_no')]; ?></p>
							</td>
							<td width="150">
								<p><? echo $construction_composition; ?></p>
							</td>
							<td width="90">
								<p><? echo $row[csf('yarn_lot')]; ?></p>
							</td>
							<td width="60">
								<p><? echo $all_count; ?></p>
							</td>
							<td width="100">
								<p><? echo $row[csf('stitch_length')]; ?></p>
							</td>

							<td width="40">
								<p><? echo $row[csf('gsm')]; ?></p>
							</td>
							<td width="40">
								<p><? echo $row[csf('dia_width')]; ?></p>
							</td>
							<td width="100">
								<p><? echo $all_color; ?></p>
							</td>
							<td width="90">
								<p><? echo $row[csf('barcode_no')] . $trans_flag; ?>&nbsp;</p>
							</td>
							<td width="50" align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
							<td width="50" align="right"><? echo $qty_in_pcs; ?></td>
							<td width="50" align="right"><? echo $size; ?></td>
							<td width="50">
								<p><? echo $floorRoomRackShelf_array[$row[csf('floor_id')]]; ?>&nbsp;</p>
							</td>
							<td width="50">
								<p><? echo $floorRoomRackShelf_array[$row[csf('room')]]; ?>&nbsp;</p>
							</td>
							<td width="50">
								<p><? echo $floorRoomRackShelf_array[$row[csf('rack')]]; ?>&nbsp;</p>
							</td>
							<td width="50">
								<p><? echo $floorRoomRackShelf_array[$row[csf('self')]]; ?>&nbsp;</p>
							</td>
							<td><? echo change_date_format($row[csf('delivery_date')]); ?></td>
						</tr>
					<?
						$i++;
						$total_grey_qnty += $row[csf('qnty')];
						$total_grey_pcs += $production_ref_arr[$row[csf("barcode_no")]]['qty_in_pcs'];
					}
				} else {
					if ($scanned_barcode_arr[$row[csf('barcode_no')]] == "") {
						$trans_flag = "";
						if ($row[csf('entry_form')] == 133) {
							$trans_flag = " (T)";
						}

						$stitch_length = $production_ref_arr[$row[csf("barcode_no")]]['stitch_length'];
						$yarn_lot = $production_ref_arr[$row[csf("barcode_no")]]['yarn_lot'];
						$yarnCount = $production_ref_arr[$row[csf("barcode_no")]]['yarn_count'];
						$dia_width = $production_ref_arr[$row[csf("barcode_no")]]['width'];
						$gsm = $production_ref_arr[$row[csf("barcode_no")]]['gsm'];
						$body_part_id = $production_ref_arr[$row[csf("barcode_no")]]['body_part_id'];
						$receive_basis = $production_ref_arr[$row[csf("barcode_no")]]['receive_basis'];
						$colorName = $production_ref_arr[$row[csf("barcode_no")]]['color_id'];
						$colorRange = $production_ref_arr[$row[csf("barcode_no")]]['color_range_id'];
						$qty_in_pcs = $production_ref_arr[$row[csf("barcode_no")]]['qty_in_pcs'];
						$size = $production_ref_arr[$row[csf("barcode_no")]]['size'];
						$knitting_source = $production_ref_arr[$row[csf("barcode_no")]]['knitting_source'];
						$knitting_company = $production_ref_arr[$row[csf("barcode_no")]]['knitting_company'];
						
						$knitting_party = '';
						if($knitting_source == 1)
						{
							$knitting_party = $companyArr[$knitting_company];
						}
						else
						{
							$knitting_party = $supplierArr[$knitting_company];
						}

						if ($receive_basis == 2) {
							$program_no = $production_ref_arr[$row[csf("barcode_no")]]['booking_no'];
						} else {
							$program_no = "";
						}

						//$yarn_count_array = array_unique(explode(",", $yarnCount));
						$yarn_count_array = array_unique(explode(",",chop($row[csf('yarn_count')],",")));
						$all_count = "";
						foreach ($yarn_count_array as $count_id) {
							$all_count .= $yarnCountArr[$count_id] . ",";
						}
						$all_count = chop($all_count, ",");

						$colorName_array = array_unique(explode(",", $colorName));
						$all_color = "";
						foreach ($colorName_array as $color_id) {
							$all_color .= $colorArr[$color_id] . ",";
						}
						$all_color = chop($all_color, ",");
						$construction_composition = $composition_arr[$row[csf('detarmination_id')]];
						$int_ref = $int_ref_arr[$row[csf('sales_booking_no')]];

						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
							<td width="30" align="center"><? echo $i; ?>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>" />
								<input type="hidden" name="txt_individual_qty" id="txt_individual_qty<?php echo $i; ?>" value="<?php echo $row[csf('qnty')]; ?>" />

								<input type="hidden" name="txt_individual_pcs" id="txt_individual_pcs<?php echo $i; ?>" value="<?php echo $production_ref_arr[$row[csf("barcode_no")]]['qty_in_pcs']; ?>" />
							</td>
							<td width="50">
								<p><? echo $row[csf('job_no_prefix_num')]; ?></p>
							</td>
							<td width="50">
								<p><? echo $row[csf('year')]; ?></p>
							</td>
							<td width="60">
								<p><? echo $yes_no[$row[csf('within_group')]]; ?></p>
							</td>
							<td width="110">
								<p><? echo $buyer_name; ?></p>
							</td>
							<td width="110">
								<p><? echo $row[csf('sales_booking_no')]; ?></p>
							</td>
							<td width="100">
								<p><? echo $knitting_party; ?></p>
							</td>
							<td width="100">
								<p><? echo $buyer_arr[$row[csf('po_buyer')]]; ?></p>
							</td>
							<td width="50">
								<p><? echo $int_ref; ?></p>
							</td>
							<td width="100">
								<p><? echo $row[csf('style_ref_no')]; ?></p>
							</td>
							<td width="150">
								<p><? echo $construction_composition; ?></p>
							</td>
							<td width="90">
								<p><? echo $row[csf('yarn_lot')]; ?></p>
							</td>
							<td width="60">
								<p><? echo $all_count; ?></p>
							</td>
							<td width="100">
								<p><? echo $row[csf('stitch_length')]; ?></p>
							</td>

							<td width="40">
								<p><? echo $row[csf('gsm')]; ?></p>
							</td>
							<td width="40">
								<p><? echo $row[csf('dia_width')]; ?></p>
							</td>
							<td width="100">
								<p><? echo $all_color; ?></p>
							</td>
							<td width="90">
								<p><? echo $row[csf('barcode_no')] . $trans_flag; ?>&nbsp;</p>
							</td>
							<td width="50" align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
							<td width="50" align="right"><? echo $qty_in_pcs; ?></td>
							<td width="50" align="right"><? echo $size; ?></td>
							<td width="50">
								<p><? echo $floorRoomRackShelf_array[$row[csf('floor_id')]]; ?>&nbsp;</p>
							</td>
							<td width="50">
								<p><? echo $floorRoomRackShelf_array[$row[csf('room')]]; ?>&nbsp;</p>
							</td>
							<td width="50">
								<p><? echo $floorRoomRackShelf_array[$row[csf('rack')]]; ?>&nbsp;</p>
							</td>
							<td width="50">
								<p><? echo $floorRoomRackShelf_array[$row[csf('self')]]; ?>&nbsp;</p>
							</td>
							<td><? echo change_date_format($row[csf('delivery_date')]); ?></td>
						</tr>
			<?
						$i++;
						$total_grey_qnty += $row[csf('qnty')];
						$total_grey_pcs += $production_ref_arr[$row[csf("barcode_no")]]['qty_in_pcs'];
					}
				}
			}
			?>
		</table>
	</div>
	<table width="1860" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
		<tr class="tbl_bottom">
		<td width="30"></td>
			<td width="50"></td>
			<td width="50"></td>
			<td width="60"></td>
			<td width="110"></td>
			<td width="110"></td>
			<td width="100"></td>
			<td width="100"></td>
			<td width="50"></td>
			<td width="100"></td>
			<td width="150"></td>
			<td width="90"></td>
			<td width="60"></td>
			<td width="100"></td>

			<td width="40"></td>
			<td width="40"></td>
			<td width="100"></td>
			<td width="90" align="right">Total</td>
			<td width="50" id="value_total_selected_value_td" align="right" class="wrd_brk"><?php echo number_format($total_grey_qnty, 2); ?></td>
			<td width="50" id="value_total_selected_pcs_td" align="right" class="wrd_brk"><?php echo number_format($total_grey_pcs, 2); ?></td>
			<td width="50"></td>
			<td width="50"></td>
			<td width="50"></td>
			<td width="50"></td>
			<td width="50"></td>
			<td width=""></td>
		</tr>

		<tr class="tbl_bottom">
		<td width="30"></td>
			<td width="50"></td>
			<td width="50"></td>
			<td width="60"></td>
			<td width="110"></td>
			<td width="110"></td>
			<td width="100"></td>
			<td width="100"></td>
			<td width="50"></td>
			<td width="100"></td>
			<td width="150"></td>
			<td width="90"></td>
			<td width="60"></td>

			<td width="140" colspan="2">Count of Selected Row</td>
			<td width="40">
				<input type="text" style="width:27px" class="text_boxes_numeric" name="hidden_selected_row_count" id="hidden_selected_row_count" readonly value="0">
			</td>
			<td width="190" colspan="2">Selected Row Total</td>
			<td width="50">
				<input type="text" class="text_boxes_numeric" name="hidden_selected_row_total" id="hidden_selected_row_total" readonly="" value="0" style="width:37px">
			</td>
			<td width="50">
				<input type="text" class="text_boxes_numeric" name="hidden_selected_pcs_total" id="hidden_selected_pcs_total" readonly="" value="0" style="width:37px">
			</td>
			<td width="50"></td>
			<td width="50"></td>
			<td width="50"></td>
			<td width="50"></td>
			<td width="50"></td>
			<td width=""></td>
		</tr>

		<tr>
			<td align="left" colspan="2">
				<input type="checkbox" name="close" class="formbutton" onClick="check_all_data()" /> Check all
			</td>
			<td align="center" colspan="26">
				<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
			</td>
		</tr>
	</table>
	<!-- <script>
		var tableFilters = {
			
			col_operation: {
				
				id: ["value_total_selected_value_td", "value_total_selected_pcs_td"],
				//col: [7,14,16,17,18,19,20,21,22,24,25,26],
				col: [15, 16],
				operation: ["sum", "sum"],
				write_method: ["innerHTML", "innerHTML"]
			}
			
		}
		setFilterGrid("tbl_list_search", -1, tableFilters);
	</script> -->
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<?

	exit();
}

if ($action == "order_popup") {
	echo load_html_head_contents("Order Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(data) {
			$('#order_id').val(data);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:750px;">
			<form name="searchdescfrm" id="searchdescfrm">
				<fieldset style="width:750px;">
					<table cellpadding="0" cellspacing="0" width="750" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Within Group</th>
							<th>Buyer Name</th>
							<th>IR/IB</th>
							<th>Sales Order No</th>
							<th>Sales/Booking No</th>
							<th width="170">Delivery Date Range</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:80px;" class="formbutton" />
								<input type="hidden" name="order_id" id="order_id" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down("cbo_within_group", 70, $yes_no, "", 1, "--Select--", 0, "load_drop_down( 'grey_sales_order_to_order_roll_trans_controller', this.value+'_'+" . $cbo_company_id . ", 'load_drop_down_buyer', 'buyer_td' );");
								?>
							</td>
							<td id="buyer_td"><? echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "--Select Buyer--", 0, ""); ?></td>
							<td>
								<input type="text" style="width:80px;" class="text_boxes" name="txt_ref_no" id="txt_ref_no" placeholder="Int Ref." />
							</td>
							<td>
								<input type="text" style="width:80px;" class="text_boxes" name="txt_order_no" id="txt_order_no" placeholder="Enter Order No" />
							</td>
							<td>
								<input type="text" style="width:80px;" class="text_boxes" name="txt_sales_booking_no" id="txt_sales_booking_no" placeholder="Enter Sales/Booking No" />
							</td>
							<td>
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" readonly>
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" readonly>
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>'+'_'+document.getElementById('cbo_within_group').value+'_'+'<? echo $txt_from_order_id; ?>'+'_'+document.getElementById('txt_sales_booking_no').value+'_'+document.getElementById('txt_ref_no').value, 'create_po_search_list_view', 'search_div', 'grey_sales_order_to_order_roll_trans_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;" />
							</td>
						</tr>
						<tr>
							<td colspan="7" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if ($action == 'create_po_search_list_view') {
	$data = explode('_', $data);
	// print_r($data);
	$company_id = $data[2];
	$fromOrderId = $data[7];
	$sales_booking_no = $data[8];
	$int_ref = $data[9];

	//$po_buyer_arr=return_library_array( "select id, buyer_id from wo_booking_mst",'id','buyer_id');
	//$sales_buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if ($int_ref != "") {
		$po_sql = "SELECT a.id, a.po_number, a.grouping, b.booking_no from wo_po_break_down a, wo_booking_dtls b
		where a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and a.grouping='$int_ref' and b.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0";
		// echo $po_sql;
		$po_sql_result = sql_select($po_sql);
		$refBooking_cond = "";
		foreach ($po_sql_result as $key => $row) {
			//$po_id_arr[$row[csf('id')]]=$row[csf('id')];
			$bookingNo_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
		}
		//$poIds=implode(",",array_unique(explode(",",$po_id_arr)));
		$refBooking_cond = " and a.sales_booking_no in('" . implode("','", $bookingNo_arr) . "') ";
		//echo $refBooking_cond;die;
	}

	if ($data[3] != "" &&  $data[4] != "") {
		if ($db_type == 0) {
			$delivery_date_cond = "and a.delivery_date between '" . change_date_format($data[3], "yyyy-mm-dd", "-") . "' and '" . change_date_format($data[4], "yyyy-mm-dd", "-") . "'";
		} else {
			$delivery_date_cond = "and a.delivery_date between '" . change_date_format($data[3], '', '', 1) . "' and '" . change_date_format($data[4], '', '', 1) . "'";
		}
	} else
		$delivery_date_cond = "";

	$type = $data[5];
	$arr = array(2 => $company_arr); //2=>$company_arr,

	$with_in_group = $data[6];
	if ($type == "to") {
		$orderIdOmitCond = "and a.id not in($fromOrderId)";
	}
	if ($data[0] == 0) $buyer_cond = "";
	else $buyer_cond = "and a.buyer_id='$data[0]'";
	if ($data[1] != "") $po_cond = "and a.job_no_prefix_num='$data[1]'";
	else $po_cond = "";
	if ($with_in_group != 0) $within_group_cond = "and a.within_group='$with_in_group'";
	else $within_group_cond = "";

	if ($type == "from") $status_cond = " and b.status_active in(1,3)";
	else $status_cond = " and b.status_active=1";
	if ($sales_booking_no != "") $sales_booking_no_cond = "and a.sales_booking_no='$sales_booking_no'";
	else $sales_booking_no_cond = "";
?>
	<div style="width:100%;">
		<table cellspacing="0" border="1" cellpadding="0" rules="all" width="780" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="50">Sales Order No</th>
				<th width="50">Year</th>
				<th width="60">With in Group</th>
				<th width="110">Sales Order Buyer</th>
				<th width="110">Sales/Booking No</th>
				<th width="100">PO Buyer</th>
				<th width="100">Style Ref.</th>
				<th width="70">PO Qty</th>
				<th>Delivery Date</th>
			</thead>
		</table>
	</div>
	<div style="width:780;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="760" class="rpt_table" id="tbl_list_search">
			<?
			/*if($db_type==0)
			{
				$sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, YEAR(a.insert_date) as year, a.delivery_date, a.style_ref_no, a.buyer_id, a.booking_id, a.sales_booking_no,a.po_buyer, a.within_group, group_concat(b.item_number_id) as item_number_id, sum(b.grey_qty) as order_qty
				from fabric_sales_order_mst a, fabric_sales_order_dtls b
				where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $buyer_cond $po_cond $delivery_date_cond $orderIdOmitCond $within_group_cond $sales_booking_no_cond
				group by a.id, a.job_no, a.job_no_prefix_num, a.insert_date, a.delivery_date, a.style_ref_no, a.buyer_id, a.booking_id, a.sales_booking_no,a.po_buyer, a.within_group order by a.id DESC ";
			}
			else if($db_type==2)
			{
				$sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, to_char(a.insert_date,'YYYY') as year, a.delivery_date, a.style_ref_no, a.buyer_id, a.booking_id, a.sales_booking_no,a.po_buyer, a.within_group, listagg(b.item_number_id,',') within group (order by b.item_number_id) as item_number_id,  listagg(b.fabric_desc,',') within group (order by b.fabric_desc) as fabric_desc, sum(b.grey_qty) as order_qty
				from fabric_sales_order_mst a, fabric_sales_order_dtls b
				where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $buyer_cond $po_cond $delivery_date_cond $orderIdOmitCond $within_group_cond $sales_booking_no_cond
				group by a.id, a.job_no, a.job_no_prefix_num, a.insert_date, a.delivery_date, a.style_ref_no, a.buyer_id, a.booking_id, a.sales_booking_no,a.po_buyer, a.within_group order by a.id DESC ";
			}*/
			$sql = "SELECT a.id, a.job_no, a.job_no_prefix_num, to_char(a.insert_date,'YYYY') as year, a.delivery_date, a.style_ref_no, a.buyer_id, a.booking_id, a.sales_booking_no,a.po_buyer, a.within_group, b.grey_qty as order_qty, b.determination_id, b.gsm_weight, b.dia
			from fabric_sales_order_mst a, fabric_sales_order_dtls b
			where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $buyer_cond $po_cond $delivery_date_cond $orderIdOmitCond $within_group_cond $sales_booking_no_cond
			$refBooking_cond order by a.id DESC ";

			//echo  $sql; //die;
			$sql_result = sql_select($sql);
			foreach ($sql_result as $row) {
				$data_array[$row[csf('job_no')]]['fso_id'] = $row[csf('id')];
				$data_array[$row[csf('job_no')]]['job_no_prefix_num'] = $row[csf('job_no_prefix_num')];
				$data_array[$row[csf('job_no')]]['year'] = $row[csf('year')];
				$data_array[$row[csf('job_no')]]['delivery_date'] = $row[csf('delivery_date')];
				$data_array[$row[csf('job_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
				$data_array[$row[csf('job_no')]]['buyer_id'] = $row[csf('buyer_id')];
				$data_array[$row[csf('job_no')]]['booking_id'] = $row[csf('booking_id')];
				$data_array[$row[csf('job_no')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
				$data_array[$row[csf('job_no')]]['po_buyer'] = $row[csf('po_buyer')];
				$data_array[$row[csf('job_no')]]['within_group'] = $row[csf('within_group')];
				$data_array[$row[csf('job_no')]]['order_qty'] += $row[csf('order_qty')];
				$data_array[$row[csf('job_no')]]['desc'] .= $row[csf('determination_id')] . '*' . $row[csf('gsm_weight')] . '*' . $row[csf('dia')] . ',';
			}
			// echo "<pre>"; print_r($data_array);

			$i = 1;
			foreach ($data_array as $row) {
				$desc = implode(",", array_unique(explode(",", chop($row['desc'], ","))));
				// echo $desc.'<br>';
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
				if ($row['within_group'] == 1) $buyer_name = $company_arr[$row['buyer_id']];
				else if ($row['within_group'] == 2) $buyer_name = $buyer_arr[$row['buyer_id']];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row['fso_id']; ?>_<? echo $desc; ?>');">
					<td width="30"><? echo $i; ?></td>
					<td width="50"><? echo $row['job_no_prefix_num']; ?></td>
					<td width="50"><? echo $row['year']; ?></td>
					<td width="60"><? echo $yes_no[$row['within_group']]; ?></td>
					<td width="110"><? echo $buyer_name; ?></td>
					<td width="110"><? echo $row['sales_booking_no']; ?></td>
					<td width="100"><? echo $buyer_arr[$row['po_buyer']]; ?></td>
					<td width="100"><?
					 // $style_ref_no= explode(",",$row['style_ref_no']);
					 $style_ref_no= str_split($row['style_ref_no'], 12);
					 foreach($style_ref_no as $value){
					  echo $value."<br>"; 
					 }
					 
					 ?></td>
					<td width="70" align="right"><? echo number_format($row['order_qty'], 2); ?></td>
					<td><? echo change_date_format($row['delivery_date']); ?></td>
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

if ($action == "to_color_popup") {
	echo load_html_head_contents("Order Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(data) {
			// alert(data);
			$('#color_str_ref').val(data);
			parent.emailwindow.hide();
		}
	</script>

	<div style="width:100%;">
		<table cellspacing="0" border="1" cellpadding="0" rules="all" width="290" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th>Color</th>
				<input type="hidden" name="color_str_ref" id="color_str_ref" class="text_boxes" value="">
			</thead>
		</table>
	</div>
	<div style="width:290;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="270" class="rpt_table" id="tbl_list_search">
			<?
			$sql = "SELECT b.color_id
			from fabric_sales_order_mst a, fabric_sales_order_dtls b
			where a.id=b.mst_id and a.company_id=$cbo_to_company_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=$txt_to_order_id";

			// echo  $sql; die;
			$sql_result = sql_select($sql);
			foreach ($sql_result as $row) {
				$data_array[$row[csf('color_id')]] = $row[csf('color_id')];
			}
			// echo "<pre>"; print_r($data_array);
			$i = 1;
			foreach ($data_array as $color_id => $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $color_id; ?>_<? echo $color_library[$color_id]; ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width=""><? echo $color_library[$color_id]; ?></td>
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

if ($action == 'populate_data_from_order') {
	$data = explode("**", $data);
	$po_id = $data[0];
	$which_order = $data[1];
	//$po_buyer_arr=return_library_array( "select id, buyer_id from wo_booking_mst",'id','buyer_id');
	//$po_comp_arr=return_library_array( "select id, company_id from wo_booking_mst",'id','company_id');
	//$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");

	/*$data_array= sql_select("SELECT a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.within_group, a.sales_booking_no,a.po_company_id,a.po_buyer, a.buyer_id, a.customer_buyer, a.booking_id, listagg(b.item_number_id,',') within group (order by b.item_number_id) as item_number_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id='$po_id'
	group by a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.within_group, a.sales_booking_no,a.po_company_id,a.po_buyer, a.buyer_id, a.customer_buyer, a.booking_id");*/

	$sql = "SELECT a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.within_group, a.sales_booking_no,a.po_company_id,a.po_buyer, a.buyer_id, a.customer_buyer, a.booking_id, b.item_number_id, b.determination_id, b.gsm_weight, b.dia
	from fabric_sales_order_mst a, fabric_sales_order_dtls b
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id='$po_id'";

	// echo  $sql; die;
	$sql_result = sql_select($sql);
	foreach ($sql_result as $row) {
		$data_array[$row[csf('job_no')]]['job_no_prefix_num'] = $row[csf('job_no_prefix_num')];
		$data_array[$row[csf('job_no')]]['job_no'] = $row[csf('job_no')];
		$data_array[$row[csf('job_no')]]['company_id'] = $row[csf('company_id')];
		$data_array[$row[csf('job_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$data_array[$row[csf('job_no')]]['within_group'] = $row[csf('within_group')];
		$data_array[$row[csf('job_no')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
		$data_array[$row[csf('job_no')]]['po_company_id'] = $row[csf('po_company_id')];
		$data_array[$row[csf('job_no')]]['po_buyer'] = $row[csf('po_buyer')];
		$data_array[$row[csf('job_no')]]['buyer_id'] = $row[csf('buyer_id')];
		$data_array[$row[csf('job_no')]]['customer_buyer'] = $row[csf('customer_buyer')];
		$data_array[$row[csf('job_no')]]['booking_id'] = $row[csf('booking_id')];
		$data_array[$row[csf('job_no')]]['item_number_id'] = $row[csf('item_number_id')];

		if ($user_level == 2) // User managment > User Level = Admin User
		{
			$data_array[$row[csf('job_no')]]['desc'] .= $row[csf('determination_id')] . ',';
		} else {
			$data_array[$row[csf('job_no')]]['desc'] .= $row[csf('determination_id')] . '*' . $row[csf('gsm_weight')] . '*' . $row[csf('dia')] . ',';
		}
		$booking_idArr[$row[csf('booking_id')]] = $row[csf('booking_id')];
	}
	// echo "<pre>"; print_r($data_array);die;

	if (!empty($booking_idArr)) {
		$int_ref_sql = "SELECT a.id, a.po_number, a.grouping, b.booking_no, b.booking_mst_id
		from fabric_sales_order_mst c, wo_booking_dtls b, wo_po_break_down a
		where c.booking_id=b.booking_mst_id and b.po_break_down_id=a.id and c.id=$po_id and c.within_group=1 and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		//echo $int_ref_sql;die;
		$int_ref_sql_result = sql_select($int_ref_sql);
		$int_ref_arr = array();
		foreach ($int_ref_sql_result as $key => $row) {
			$int_ref_arr[$row[csf('booking_no')]] = $row[csf('grouping')];
		}
	}


	foreach ($data_array as $row) {
		$int_ref = $int_ref_arr[$row['sales_booking_no']];

		$desc = implode(",", array_unique(explode(",", chop($row['desc'], ","))));
		$gmts_item_id = array_unique(explode(",", $row['item_number_id']));
		foreach ($gmts_item_id as $item_id) {
			if ($gmts_item == "") $gmts_item = $garments_item[$item_id];
			else $gmts_item .= "," . $garments_item[$item_id];
		}

		if ($row["within_group"] == 1) {
			$buyer = $row["po_buyer"];
		} else {
			$buyer = $row["buyer_id"];
		}

		echo "document.getElementById('txt_" . $which_order . "_order_id').value 			= '" . $po_id . "';\n";
		echo "document.getElementById('txt_" . $which_order . "_order_no').value 			= '" . $row["job_no"] . "';\n";
		echo "document.getElementById('txt_" . $which_order . "_booking_no').value 			= '" . $row["sales_booking_no"] . "';\n";
		echo "document.getElementById('cbo_" . $which_order . "_company').value 			= '" . $row["po_company_id"] . "';\n";
		echo "document.getElementById('cbo_" . $which_order . "_buyer_name').value 			= '" . $buyer . "';\n";
		echo "document.getElementById('cbo_" . $which_order . "_cust_buyer_name').value 	= '" . $row["customer_buyer"] . "';\n";
		echo "document.getElementById('txt_" . $which_order . "_style_ref').value 			= '" . $row["style_ref_no"] . "';\n";
		echo "document.getElementById('txt_" . $which_order . "_int_ref').value 			= '" . $int_ref . "';\n";
		//echo "document.getElementById('txt_".$which_order."_job_no').value 				= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_" . $which_order . "_gmts_item').value 			= '" . $gmts_item . "';\n";
		//echo "document.getElementById('txt_".$which_order."_shipment_date').value 		= '".change_date_format($row[csf("delivery_date")])."';\n";
		// echo $which_order;

		if ($which_order == 'to') {
			echo "document.getElementById('desc_str').value 			= '" . $desc . "';\n";
			echo "load_bodypart_list();\n";
		}

		exit();
	}
}

if ($action == "show_dtls_list_view") {
	$data = explode("**", $data);
	$barcode_no = $data[0];
	$requisition_id = $data[1];

	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');

	if ($requisition_id > 0) {
		$sql_requ = "SELECT b.id as requ_dtls_id, b.barcode_no, b.remarks from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
		WHERE a.id=b.mst_id and a.entry_form in(352) and b.mst_id=$requisition_id and b.barcode_no in($barcode_no) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		// echo $sql_requ;
		$sql_requ_result = sql_select($sql_requ);
		if (empty($sql_requ_result)) {
			echo "999";
			die;
		}
		foreach ($sql_requ_result as $row) {
			$requisition_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
			$requ_dtls_arr[$row[csf('barcode_no')]]['requ_dtls_id'] = $row[csf('requ_dtls_id')];
			$requ_dtls_arr[$row[csf('barcode_no')]]['remarks'] = $row[csf('remarks')];
		}
	}

	$programArr = return_library_array("SELECT b.barcode_no, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and b.entry_form=2 and b.barcode_no in($barcode_no) and b.status_active=1 and b.is_deleted=0", "barcode_no", "booking_id");

	$sql = "SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.color_id as color_names, b.yarn_count, b.stitch_length, b.brand_id, b.body_part_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, c.rate,c.amount, 1 as type,c.booking_no,a.store_id, d.detarmination_id, d.gsm, d.dia_width
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and b.trans_id<>0 and a.entry_form in(2,22,58,84) and c.entry_form in(2,22,58,84) and c.is_transfer!=6 and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_no) and c.is_service=0 and c.is_sales=1
	union all
	select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.color_names, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_body_part as body_part_id, b.to_floor_id as floor_id, b.to_room as room, b.to_rack as rack,  b.to_shelf as self, b.to_bin_box as bin_box, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, c.rate,c.amount, 2 as type, c.booking_no, b.to_store as store_id, d.detarmination_id, d.gsm, d.dia_width
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, product_details_master d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.from_prod_id=d.id and a.entry_form in(133) and c.entry_form in(133) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_no) and c.is_service=0 and c.is_sales=1
	order by barcode_no";
	// echo $sql;
	$data_array = sql_select($sql);

	foreach ($data_array as $row) {
		$ref_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}

	$ref_barcode_arr = array_filter($ref_barcode_arr);
	if (count($ref_barcode_arr) > 0) {
		$all_ref_barcode_nos = implode(",", $ref_barcode_arr);
		$all_ref_barcode_no = "";
		$barCond = "";
		$all_ref_barcode_no_1 = "";
		$barCond_1 = "";
		if ($db_type == 2 && count($ref_barcode_arr) > 999) {
			$ref_barcode_arr_chunk = array_chunk($ref_barcode_arr, 999);
			foreach ($ref_barcode_arr_chunk as $chunk_arr) {
				$chunk_arr_value = implode(",", $chunk_arr);
				$barCond .= "  barcode_no in($chunk_arr_value) or ";
				$barCond_1 .= "  barcode_num in($chunk_arr_value) or ";
			}

			$all_ref_barcode_no .= " and (" . chop($barCond, 'or ') . ")";
			$all_ref_barcode_no_1 .= " and (" . chop($barCond_1, 'or ') . ")";
		} else {
			$all_ref_barcode_no = " and barcode_no in($all_ref_barcode_nos)";
			$all_ref_barcode_no_1 = " and barcode_num in($all_ref_barcode_nos)";
		}

		$issued_barcode_arr = return_library_array("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0 $all_ref_barcode_no and is_returned !=1 ", "barcode_no", "barcode_no");

		$delv_arr = return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 $all_ref_barcode_no_1", "barcode_num", "grey_sys_id");
	}

	if (count($ref_barcode_arr) > 0) // production
	{
		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
        from pro_grey_prod_entry_dtls a, pro_roll_details b
        where a.id=b.dtls_id and a.mst_id=b.mst_id and b.entry_form in(2)  and a.status_active=1 and b.status_active=1 $all_ref_barcode_no ");
		$prodBarcodeData = array();
		foreach ($production_sql as $row) {
			$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] = $row[csf("stitch_length")];
			$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
			$prodBarcodeData[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
		}
	}


	$i = 1;
	if (count($data_array) > 0) {
		foreach ($data_array as $row) {
			if ($issued_barcode_arr[$row[csf('barcode_no')]] == "") {
				$ycount = '';
				$count_id = explode(',', $row[csf('yarn_count')]);
				foreach ($count_id as $count) {
					if ($ycount == '') $ycount = $count_arr[$count];
					else $ycount .= "," . $count_arr[$count];
				}

				$transRollId = $row[csf('roll_id')];
				$program_no = '';
				if ($row[csf('entry_form')] == 2) {
					if ($row[csf('receive_basis')] == 2) $program_no = $row[csf('booking_id')];
				} else if ($row[csf('entry_form')] == 58 || $row[csf('entry_form')] == 84) {
					$program_no = $programArr[$row[csf('barcode_no')]];
					$row[csf('roll_id')] = $row[csf('roll_id_prev')];
				} else if ($row[csf('entry_form')] == 133) {
					$program_no = $row[csf('booking_no')];
					$row[csf('roll_id')] = $row[csf('roll_id_prev')];
				} else if ($row[csf('entry_form')] == 22) {
					$program_no = $row[csf('booking_no')];
				}
				$itemDesc = $row[csf('detarmination_id')] . '*' . $row[csf('gsm')] . '*' . $row[csf('dia_width')];
				$roll_rate = $row[csf("rate")];
				if ($roll_rate == "") $roll_rate = 0;

				$color_names = "";
				foreach (explode(",", $row[csf('color_names')]) as  $val) {
					$color_names .= $color_library[$val] . ",";
				}
				$color_names = chop($color_names, ",");

				$barcodeData .= $row[csf('barcode_no')] . "**" . $row[csf('po_breakdown_id')] . "**" . $row[csf('roll_no')] . "**" . $program_no . "**" . $row[csf('prod_id')] . "**" . $product_arr[$row[csf('prod_id')]] . "**" . $ycount . "**" . $brand_arr[$row[csf('brand_id')]] . "**" . $row[csf('yarn_lot')] . "**" . $color_names . "**" . $row[csf('stitch_length')] . "**" . $itemDesc . "**" . $row[csf('amount')] . "**" . $roll_rate . "**" . $row[csf('roll_id')] . "**" . $row[csf('qnty')] . "**" . $row[csf('color_names')] . "**" . $row[csf('yarn_count')] . "**" . $row[csf('brand_id')] . "**" . $row[csf('body_part_id')] . "**" . $row[csf('floor_id')] . "**" . $row[csf('room')] . "**" . $row[csf('rack')] . "**" . $row[csf('self')] . "**" . $row[csf('bin_box')] . "**" . $transRollId . "**" . $row[csf('store_id')] . "**" . $requ_dtls_arr[$row[csf("barcode_no")]]["remarks"] . "**" . $prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"] . "**" . $requ_dtls_arr[$row[csf("barcode_no")]]["requ_dtls_id"] . "__";
			}
		}
		echo chop($barcodeData, "__");
	} else {
		echo "0";
	}
	exit();
}

if ($action == "show_transfer_listview") {
	$data = explode("**", $data);

	$mst_id = $data[0];
	$order_id = $data[1];
	$cbo_transfer_criteria = $data[5];

	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
	//$delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 and order_id=$order_id", "barcode_num", "grey_sys_id");
	//$trans_arr=return_library_array("select barcode_no, mst_id from pro_roll_details where entry_form=2", "barcode_no", "mst_id");
	$re_trans_arr = return_library_array("select barcode_no from pro_roll_details where entry_form=133 and re_transfer=1 and status_active=1 and is_deleted=0 and mst_id=$mst_id ", "barcode_no", "barcode_no");

	$transfer_arr = array();
	$transfer_dataArray = sql_select("SELECT a.id, a.trans_id, a.to_trans_id, b.id as roll_id, b.barcode_no from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=$mst_id and b.mst_id=$mst_id and b.entry_form=133 and b.transfer_criteria=$cbo_transfer_criteria and b.status_active=1 and b.is_deleted=0");
	foreach ($transfer_dataArray as $row) {
		$transfer_arr[$row[csf('barcode_no')]]['dtls_id'] = $row[csf('id')];
		$transfer_arr[$row[csf('barcode_no')]]['from_trans_id'] = $row[csf('trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['to_trans_id'] = $row[csf('to_trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['rolltableId'] = $row[csf('roll_id')];
	}

	$programArr = return_library_array("SELECT a.id, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and b.entry_form=2 and b.po_breakdown_id=$order_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.booking_id", "id", "booking_id");

	$sql_qry = "SELECT a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.to_prod_id, b.yarn_lot, b.color_names, b.y_count as yarn_count, b.stitch_length, b.brand_id,b.body_part_id,b.floor_id,b.room,b.rack,b.shelf,b.bin_box,b.to_body_part,b.to_floor_id,b.to_room,b.to_rack,b.to_shelf,b.to_bin_box, b.transfer_requ_dtls_id, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.booking_no, c.roll_id as roll_id_prev, c.rate, c.amount, 3 as type , b.from_store as store_id, b.remarks, d.detarmination_id, d.gsm, d.dia_width, c.roll_split_from
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, product_details_master d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.from_prod_id=d.id and a.entry_form in(133) and c.entry_form in(133) and c.status_active=1 and c.is_deleted=0 and c.mst_id=$mst_id
	order by barcode_no";
	// echo $sql_qry;die;

	$data_arr = sql_select($sql_qry);
	//echo "kausar";  print_r ( $data_arr );	die;
	$barcodeNos = "";
	$ref_barcode_arr = array();
	foreach ($data_arr as $vals) {
		$barcodeNos .= $vals[csf('barcode_no')] . ",";
		$ref_barcode_arr[$vals[csf('barcode_no')]] = $vals[csf('barcode_no')];
	}

	$barcodeNos = chop($barcodeNos, ",");
	$barcodeNos;

	if (count($ref_barcode_arr) > 0) // production
	{
		$all_ref_barcode_nos = implode(",", $ref_barcode_arr);
		$all_ref_barcode_no = "";
		$barCond = "";
		if ($db_type == 2 && count($ref_barcode_arr) > 999) {
			$ref_barcode_arr_chunk = array_chunk($ref_barcode_arr, 999);
			foreach ($ref_barcode_arr_chunk as $chunk_arr) {
				$chunk_arr_value = implode(",", $chunk_arr);
				$barCond .= "  b.barcode_no in($chunk_arr_value) or ";
			}
			$all_ref_barcode_no .= " and (" . chop($barCond, 'or ') . ")";
		} else {
			$all_ref_barcode_no = " and b.barcode_no in($all_ref_barcode_nos)";
		}

		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
        from pro_grey_prod_entry_dtls a, pro_roll_details b
        where a.id=b.dtls_id and a.mst_id=b.mst_id and b.entry_form in(2)  and a.status_active=1 and b.status_active=1 $all_ref_barcode_no ");
		$prodBarcodeData = array();
		foreach ($production_sql as $row) {
			$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] = $row[csf("stitch_length")];
			$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
			$prodBarcodeData[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
		}
	}

	$sql_issue_barcode = sql_select("select barcode_no from  pro_roll_details  where  entry_form in(61) and status_active=1 and is_deleted=0 and barcode_no in($barcodeNos) and is_returned !=1 order by barcode_no");
	foreach ($sql_issue_barcode as $barcodeNO) {
		$barcode_arr[$barcodeNO[csf('barcode_no')]]['barcode'] = $barcodeNO[csf('barcode_no')];
	}

	$splited_roll_sql = sql_select("select barcode_no,split_from_id from pro_roll_split where status_active =1 and barcode_no in ($barcodeNos) ");

	foreach ($splited_roll_sql as $bar) {
		$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]] = $bar[csf('barcode_no')];
	}

	$child_roll_sql = sql_select("select barcode_no,roll_split_from from pro_roll_details where status_active =1 and barcode_no in ($barcodeNos)  and roll_split_from !=0");

	foreach ($child_roll_sql as $bar) {
		$child_roll_ref[$bar[csf('barcode_no')]][$bar[csf('roll_split_from')]] = $bar[csf('barcode_no')];
	}
	// echo "<pre>";print_r($child_roll_ref);die;

	$body_part_sql = sql_select("SELECT b.id, b.body_part_id from fabric_sales_order_mst a, fabric_sales_order_dtls b  where a.id=b.mst_id and a.company_id= '$data[2]' and b.mst_id='$data[4]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, b.body_part_id order by b.id");
	$body_part_id_arr = array();
	foreach ($body_part_sql as $row) {
		$body_part_id = $row[csf("body_part_id")];

		if ($body_part_id != "") {
			$body_part_id_arr[$body_part_id] = $body_part[$row[csf("body_part_id")]];
		}
	}


	$lib_room_rack_shelf_sql = "SELECT b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,
	a.floor_room_rack_name floor_name, c.floor_room_rack_name room_name, d.floor_room_rack_name rack_name,
	e.floor_room_rack_name shelf_name, f.floor_room_rack_name bin_name
	from lib_floor_room_rack_dtls b
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
	left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0
	where b.status_active=1 and b.is_deleted=0 and b.company_id=$data[2] and b.store_id=$data[3]
	order by a.floor_room_rack_name , c.floor_room_rack_name , d.floor_room_rack_name , e.floor_room_rack_name , f.floor_room_rack_name";
	// echo $lib_room_rack_shelf_sql;die;
	$lib_rrsb_arr = sql_select($lib_room_rack_shelf_sql);
	if (!empty($lib_rrsb_arr)) {
		foreach ($lib_rrsb_arr as $room_rack_shelf_row) {
			$company  = $room_rack_shelf_row[csf("company_id")];
			$floor_id = $room_rack_shelf_row[csf("floor_id")];
			$room_id  = $room_rack_shelf_row[csf("room_id")];
			$rack_id  = $room_rack_shelf_row[csf("rack_id")];
			$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
			$bin_id   = $room_rack_shelf_row[csf("bin_id")];

			if ($floor_id != "") {
				$lib_floor_arr[$floor_id] = $room_rack_shelf_row[csf("floor_name")];
			}

			if ($floor_id != "" && $room_id != "") {
				$lib_room_arr[$room_id] = $room_rack_shelf_row[csf("room_name")];
			}

			if ($floor_id != "" && $room_id != "" && $rack_id != "") {
				$lib_rack_arr[$rack_id] = $room_rack_shelf_row[csf("rack_name")];
			}

			if ($floor_id != "" && $room_id != "" && $rack_id != "" && $shelf_id != "") {
				$lib_shelf_arr[$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
			}
			if ($floor_id != "" && $room_id != "" && $rack_id != "" && $shelf_id != "" && $bin_id != "") {
				$lib_bin_arr[$bin_id] = $room_rack_shelf_row[csf("bin_name")];
			}
		}
	} else {
		$lib_floor_arr[0] = "";
		$lib_room_arr[0] = "";
		$lib_rack_arr[0] = "";
		$lib_shelf_arr[0] = "";
		$lib_bin_arr[0] = "";
	}

	if (count($data_arr) > 0) {
		foreach ($data_arr as $rows) {
			$ycount = '';
			$count_id = explode(',', $rows[csf('yarn_count')]);
			foreach ($count_id as $count) {
				if ($ycount == '') $ycount = $count_arr[$count];
				else $ycount .= "," . $count_arr[$count];
			}

			$transRollId = $rows[csf('roll_id')];
			$program_no = '';

			$program_no = $rows[csf('booking_no')];
			$rows[csf('roll_id')] = $rows[csf('roll_id_prev')];

			if ($transfer_arr[$rows[csf('barcode_no')]]['dtls_id'] == "") {
				$checked = 0;
			} else $checked = 1;

			if ($re_trans_arr[$rows[csf('barcode_no')]] == "") {
				$disabled = 0;
			} else //when ack
			{
				$disabled = 1;
			}
			//check issued barcode
			if ($barcode_arr[$rows[csf('barcode_no')]]['barcode'] == $rows[csf('barcode_no')]) {
				$disabled = 1;
			}

			if ($splited_roll_ref[$rows[csf('barcode_no')]][$transRollId] != "") {
				$disabled = 1;
			}
			if ($child_roll_ref[$rows[csf('barcode_no')]][$rows[csf('roll_split_from')]]) {
				// echo $rows[csf('barcode_no')].']['.$rows[csf('roll_split_from')].'<br>';
				$disabled = 1;
			}
			if ($rows[csf('transfer_requ_dtls_id')] != "") {
				$disabled = 1;
			}

			$color_names = "";
			foreach (explode(",", $rows[csf('color_names')]) as  $val) {
				$color_names .= $color_library[$val] . ",";
			}
			$color_names = chop($color_names, ",");

			$dtls_id = $transfer_arr[$rows[csf('barcode_no')]]['dtls_id'];
			$from_trans_id = $transfer_arr[$rows[csf('barcode_no')]]['from_trans_id'];
			$to_trans_id = $transfer_arr[$rows[csf('barcode_no')]]['to_trans_id'];
			$rolltableId = $transfer_arr[$rows[csf('barcode_no')]]['rolltableId'];
			$itemDesc = $rows[csf('detarmination_id')] . '*' . $rows[csf('gsm')] . '*' . $rows[csf('dia_width')];
			$roll_rate = $rows[csf("rate")];
			if ($roll_rate == "") $roll_rate = 0;

			$barcodeData .= $rows[csf('barcode_no')] . "**" . $rows[csf('po_breakdown_id')] . "**" . $rows[csf('roll_no')] . "**" . $program_no . "**" . $rows[csf('prod_id')] . "**" . $product_arr[$rows[csf('prod_id')]] . "**" . $ycount . "**" . $brand_arr[$rows[csf('brand_id')]] . "**" . $rows[csf('yarn_lot')] . "**" . $color_names . "**" . $rows[csf('stitch_length')] . "**" . $itemDesc . "**" . $rows[csf('amount')] . "**" . $roll_rate . "**" . $rows[csf('roll_id')] . "**" . $rows[csf('qnty')] . "**" . $rows[csf('color_names')] . "**" . $rows[csf('yarn_count')] . "**" . $rows[csf('brand_id')] . "**" . $rows[csf('body_part_id')] . "**" . $rows[csf('floor_id')] . "**" . $rows[csf('room')] . "**" . $rows[csf('rack')] . "**" . $rows[csf('self')] . "**" . $rows[csf('bin_box')] . "**" . $transRollId . "**" . $rows[csf('store_id')] . "**" . $rows[csf('remarks')] . "**" . $rows[csf('to_prod_id')] . "**" . $dtls_id . "**" . $rows[csf('transfer_requ_dtls_id')] . "**" . $from_trans_id . "**" . $to_trans_id . "**" . $rolltableId . "**" . $rows[csf('to_body_part')] . "**" . $rows[csf('to_floor_id')] . "**" . $rows[csf('to_room')] . "**" . $rows[csf('to_rack')] . "**" . $rows[csf('to_shelf')] . "**" . $rows[csf('to_bin_box')] . "**" . $checked . "**" . $disabled . "**" . $prodBarcodeData[$rows[csf("barcode_no")]]["machine_dia"] . "__";
		}
		echo chop($barcodeData, "__");
	} else {
		echo "0";
	}
	exit();
}

if ($action == "populate_data_about_order") {
	$data = explode("**", $data);
	$order_id = $data[0];
	$prod_id = $data[1];

	$sql = sql_select("select
		sum(case when entry_form in(2,22) then quantity end) as grey_fabric_recv,
		sum(case when entry_form in(16) then quantity end) as grey_fabric_issued,
		sum(case when entry_form=45 then quantity end) as grey_fabric_recv_return,
		sum(case when entry_form=51 then quantity end) as grey_fabric_issue_return,
		sum(case when entry_form in(13,81) and trans_type=5 then quantity end) as grey_fabric_trans_recv,
		sum(case when entry_form in(13,80) and trans_type=6 then quantity end) as grey_fabric_trans_issued
		from order_wise_pro_details where trans_id<>0 and prod_id=$prod_id and po_breakdown_id=$order_id and is_deleted=0 and status_active=1");

	$grey_fabric_recv = $sql[0][csf('grey_fabric_recv')] + $sql[0][csf('grey_fabric_trans_recv')] + $sql[0][csf('grey_fabric_issue_return')];
	$grey_fabric_issued = $sql[0][csf('grey_fabric_issued')] + $sql[0][csf('grey_fabric_trans_issued')] + $sql[0][csf('grey_fabric_recv_return')];
	$yet_issue = $grey_fabric_recv - $grey_fabric_issued;

	echo "$('#txt_stock').val('" . $yet_issue . "');\n";

	exit();
}

if ($action == "orderToorderTransfer_popup") {
	echo load_html_head_contents("Order To Order Transfer Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data) {
			$('#transfer_id').val(data);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:780px;">
			<form name="searchdescfrm" id="searchdescfrm">
				<fieldset style="width:760px;margin-left:10px">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="550" class="rpt_table">
						<thead>
							<th>Search By</th>
							<th width="240" id="search_by_td_up">Please Enter Transfer ID</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
								<input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								$search_by_arr = array(1 => "Transfer ID", 2 => "Challan No.", 3 => "Barcode No.");
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../../') ";
								echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td id="search_by_td">
								<input type="text" style="width:130px;" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_transfer_search_list_view', 'search_div', 'grey_sales_order_to_order_roll_trans_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

if ($action == 'create_transfer_search_list_view') {
	$data = explode("_", $data);
	$search_string = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$transfer_criteria = trim($data[3]);
	
	if(!empty($search_string)){
		if ($search_by == 1){
			$search_field = " and a.transfer_prefix_number=$search_string";
		}else if($search_by == 2){
			$search_field = " and a.challan_no=$search_string";
		}else if($search_by == 3){
			$search_field = " and b.barcode_no=$search_string";
		}else{
			$search_field = " ";
		}
	}
		

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
	else $year_field = ""; //defined Later

	$sql = "SELECT a.id, a.transfer_prefix_number, $year_field a.transfer_system_id, a.challan_no, a.company_id, a.transfer_date, a.transfer_criteria, a.item_category, b.barcode_no from inv_item_transfer_mst a, pro_roll_details b where a.id=b.mst_id and  a.company_id=$company_id and b.transfer_criteria=$transfer_criteria $search_field  and a.entry_form=133 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	 order by a.id";

	//echo $sql; exit();

	$arr = array(3 => $company_arr, 5 => $item_transfer_criteria, 6 => $item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category, Barcode No.", "80,70,100,80,90,130,100", "850", "250", 0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category,barcode_no", '', '', '0,0,0,0,3,0,0');

	exit();
}

if ($action == 'populate_data_from_transfer_master') {

	$data_array = sql_select("SELECT a.transfer_system_id,a.challan_no, a.company_id, a.transfer_date, a.item_category, a.from_order_id, a.to_order_id, a.transfer_requ_no, a.transfer_requ_id,max(b.from_store) as from_store,max(b.to_store) as to_store, a.transfer_criteria,a.purpose, a.to_company, a.to_color_id, a.driver_name,a.remarks, a.mobile_no, a.vehicle_no from inv_item_transfer_mst a,inv_item_transfer_dtls b where a.id=b.mst_id and a.id='$data' group by a.purpose,a.transfer_system_id,a.challan_no, a.company_id, a.transfer_date, a.item_category, a.from_order_id, a.to_order_id, a.transfer_requ_no, a.transfer_requ_id, a.transfer_criteria, a.to_company, a.to_color_id, a.driver_name, a.remarks, a.mobile_no, a.vehicle_no");

	$transfer_requ_id = $data_array[0][csf("transfer_requ_id")];
	if ($transfer_requ_id != "") {
		$requi_qty_status = sql_select("SELECT a.id, a.requisition_status, sum(b.transfer_qnty) as transfer_qnty from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
		where a.id=b.mst_id and a.id=$transfer_requ_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.requisition_status");
		foreach ($requi_qty_status as $value) {
			$requi_qty_status_arr[$value[csf("id")]]['requisition_status'] = $value[csf("requisition_status")];
			$requi_qty_status_arr[$value[csf("id")]]['transfer_qnty'] += $value[csf("transfer_qnty")];
		}
	}
	foreach ($data_array as $row) {
		if ($row[csf("transfer_criteria")] == 4) {
			$to_company = $row[csf("company_id")];
		} else {
			$to_company = $row[csf("to_company")];
		}
		echo "load_drop_down( 'requires/grey_sales_order_to_order_roll_trans_controller','" . $row[csf("transfer_criteria")] . '_' . $row[csf("company_id")] . "', 'load_drop_store_from', 'from_store_td' );\n";
		echo "load_drop_down( 'requires/grey_sales_order_to_order_roll_trans_controller','" . $row[csf("transfer_criteria")] . '_' . $to_company . "', 'load_drop_store_to', 'store_td' );\n";

		echo "document.getElementById('update_id').value 				= '" . $data . "';\n";
		echo "document.getElementById('txt_system_id').value 			= '" . $row[csf("transfer_system_id")] . "';\n";
		echo "document.getElementById('cbo_company_id').value 			= '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('txt_challan_no').value 			= '" . $row[csf("challan_no")] . "';\n";
		echo "document.getElementById('cbo_from_store_name').value 		= '" . $row[csf("from_store")] . "';\n";
		echo "document.getElementById('cbo_store_name').value 			= '" . $row[csf("to_store")] . "';\n";
		echo "document.getElementById('cbo_purpose_id').value 			= '" . $row[csf("purpose")] . "';\n";
		echo "document.getElementById('txt_transfer_date').value 		= '" . change_date_format($row[csf("transfer_date")]) . "';\n";


		echo "document.getElementById('txt_driver_name').value 			= '" . $row[csf("driver_name")] . "';\n";
		echo "document.getElementById('txt_remark').value 			= '" . $row[csf("driver_name")] . "';\n";
		echo "document.getElementById('txt_mobile_no').value 			= '" . $row[csf("mobile_no")] . "';\n";
		echo "document.getElementById('txt_vehicle_no').value 			= '" . $row[csf("vehicle_no")] . "';\n";



		

		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		echo "$('#cbo_to_company_id').attr('disabled','disabled');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "document.getElementById('cbo_transfer_criteria').value 	= '" . $row[csf("transfer_criteria")] . "';\n";
		echo "document.getElementById('cbo_to_company_id').value 		= '" . $to_company . "';\n";

		echo "get_php_form_data('" . $row[csf("from_order_id")] . "**from'" . ",'populate_data_from_order','requires/grey_sales_order_to_order_roll_trans_controller');\n";
		echo "get_php_form_data('" . $row[csf("to_order_id")] . "**to'" . ",'populate_data_from_order','requires/grey_sales_order_to_order_roll_trans_controller');\n";
		//echo "get_php_form_data('".$row[csf("to_order_id")]."**to'".",'bodypart_list','requires/grey_sales_order_to_order_roll_trans_controller');\n";




		echo "document.getElementById('txt_requisition_no').value 		= '" . $row[csf("transfer_requ_no")] . "';\n";
		echo "document.getElementById('txt_requisition_no').value 		= '" . $row[csf("transfer_requ_no")] . "';\n";
		echo "document.getElementById('txt_requisition_id').value 		= '" . $row[csf("transfer_requ_id")] . "';\n";
		echo "document.getElementById('hidd_requi_qty').value 			= '" . $requi_qty_status_arr[$row[csf("transfer_requ_id")]]['transfer_qnty'] . "';\n";
		echo "document.getElementById('cbo_complete_status').value 		= '" . $requi_qty_status_arr[$row[csf("transfer_requ_id")]]['requisition_status'] . "';\n";
		echo "$('#txt_requisition_no').attr('disabled','disabled');\n";
		echo "document.getElementById('hid_to_color_id').value 			= '" . $row[csf("to_color_id")] . "';\n";
		echo "document.getElementById('txt_to_color_no').value 			= '" . $color_library[$row[csf("to_color_id")]] . "';\n";

		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_grey_transfer_entry',1,1);\n";

		exit();
	}
}

if ($action == "orderInfo_popup") {
	echo load_html_head_contents("Order Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>

	</head>

	<body>
		<div align="center" style="width:770px;">
			<form name="searchdescfrm" id="searchdescfrm">
				<fieldset style="width:760px;margin-left:15px">
					<legend><? echo ucfirst($type); ?> Order Info</legend>
					<br>
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr bgcolor="#FFFFFF">
							<td align="center"><? echo ucfirst($type); ?> Order No: <b><? echo $txt_order_no; ?></b></td>
						</tr>
					</table>
					<br>
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="750" align="center">
						<thead>
							<th width="40">SL</th>
							<th width="100">Required</th>
							<?
							if ($type == "from") {
							?>
								<th width="100">Knitted</th>
								<th width="100">Issue to dye</th>
								<th width="100">Issue Return</th>
								<th width="100">Transfer Out</th>
								<th width="100">Transfer In</th>
								<th>Remaining</th>
							<?
							} else {
							?>
								<th width="80">Yrn. Issued</th>
								<th width="80">Yrn. Issue Rtn</th>
								<th width="80">Knitted</th>
								<th width="90">Issue Rtn.</th>
								<th width="100">Transf. Out</th>
								<th width="100">Transf. In</th>
								<th>Shortage</th>
							<?
							}
							?>

						</thead>
						<?
						$req_qty = return_field_value("sum(b.grey_fab_qnty) as grey_req_qnty", "wo_booking_mst a, wo_booking_dtls b", "a.booking_no=b.booking_no and a.item_category in(2,13) and b.po_break_down_id=$txt_order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1", "grey_req_qnty");

						$sql = "select
					sum(CASE WHEN entry_form ='3' THEN quantity ELSE 0 END) AS issue_qnty,
					sum(CASE WHEN entry_form ='5' THEN quantity ELSE 0 END) AS dye_issue_qnty,
					sum(CASE WHEN entry_form ='9' THEN quantity ELSE 0 END) AS return_qnty,
					sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_out_qnty,
					sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_in_qnty,
					sum(CASE WHEN trans_id<>0 and entry_form in(2,22) THEN quantity ELSE 0 END) AS knit_qnty
					from order_wise_pro_details where po_breakdown_id=$txt_order_id and status_active=1 and is_deleted=0";
						$dataArray = sql_select($sql);
						$remaining = 0;
						$shoratge = 0;
						?>
						<tr bgcolor="#EFEFEF">
							<td>1</td>
							<td align="right"><? echo number_format($req_qty, 2); ?>&nbsp;</td>
							<?
							if ($type == "from") {
								$remaining = $dataArray[0][csf('issue_qnty')] - $dataArray[0][csf('return_qnty')] - $dataArray[0][csf('transfer_out_qnty')] + $dataArray[0][csf('transfer_in_qnty')] - $dataArray[0][csf('knit_qnty')];
							?>
								<td align="right"><? echo number_format($dataArray[0][csf('knit_qnty')], 2); ?>&nbsp;</td>
								<td align="right"><? echo number_format($dataArray[0][csf('dye_issue_qnty')], 2); ?></td>
								<td align="right"><? echo number_format($dataArray[0][csf('return_qnty')], 2); ?>&nbsp;</td>
								<td align="right"><? echo number_format($dataArray[0][csf('transfer_in_qnty')], 2); ?></td>
								<td align="right"><? echo number_format($dataArray[0][csf('transfer_out_qnty')], 2); ?>&nbsp;</td>
								<td align="right"><? echo number_format($remaining, 2); ?>&nbsp;</td>
							<?
							} else {
								$shoratge = $req_qty - $dataArray[0][csf('issue_qnty')] - $dataArray[0][csf('return_qnty')] + $dataArray[0][csf('transfer_out_qnty')] - $dataArray[0][csf('transfer_in_qnty')];
							?>
								<td align="right"><? echo number_format($dataArray[0][csf('issue_qnty')], 2); ?>&nbsp;</td>
								<td align="right"><? echo number_format($dataArray[0][csf('return_qnty')], 2); ?></td>
								<td align="right"><? echo number_format($dataArray[0][csf('knit_qnty')], 2); ?>&nbsp;</td>
								<td align="right"><? echo number_format($dataArray[0][csf('return_qnty')], 2); ?></td>
								<td align="right"><? echo number_format($dataArray[0][csf('transfer_in_qnty')], 2); ?>&nbsp;</td>
								<td align="right"><? echo number_format($dataArray[0][csf('transfer_out_qnty')], 2); ?>&nbsp;</td>
								<td align="right"><? echo number_format($shoratge, 2); ?>&nbsp;</td>
							<?
							}

							?>
						</tr>
					</table>
					<table>
						<tr>
							<td align="center">
								<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</body>

	</html>
<?
	exit();
}

//data save update delete here------------------------------//
if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	// Lib -> Variable Settings -> Inventory -> Variable List -> Auto Transfer Receive
	// if Auto Transfer Receive yes, then no need to acknowledgement
	$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and item_category_id=13 and status_active=1 and variable_list= 27", "auto_transfer_rcv");
	if ($variable_auto_rcv == "") {
		$variable_auto_rcv = 1; // if auto receive 1 No, then no need to acknowledgement
	}

	$store_update_upto=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=13 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");

	// echo "20**$variable_auto_rcv".'====='; die;

	for ($k = 1; $k <= $total_row; $k++) {
		$productId = "productId_" . $k;
		$prod_ids .= $$productId . ",";
	}
	$prod_ids = implode(",", array_unique(explode(",", chop($prod_ids, ','))));
	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in ($prod_ids) and transaction_type in (1,4,5)", "max_date");
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$trans_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
	if ($trans_date < $max_recv_date) {
		echo "20**Transfer Date Can not Be Less Than Last Receive Date Of These Lot";
		die;
	}

	if (str_replace("'", "", $update_id) != "") {
		$is_acknowledge = return_field_value("b.id id", "inv_item_transfer_mst a,inv_item_trans_acknowledgement b", "a.id=b.challan_id and  a.id=$update_id and a.status_active=1 and a.is_acknowledge=1 and b.entry_form=133", "id");
		if ($is_acknowledge != "") {
			echo "20**Update not allowed. This Transfer Challan is already Acknowledged.\nAcknowledge System ID = $is_acknowledge";
			die;
		}
	}

	if (str_replace("'", "", $cbo_transfer_criteria) == 4) {
		$cbo_to_company_id = $cbo_company_id;
	}

	if ($operation == 0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$transfer_recv_num = '';
		$transfer_update_id = '';

		if (str_replace("'", "", $update_id) == "") {
			if ($db_type == 0) $year_cond = "YEAR(insert_date)";
			else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
			else $year_cond = ""; //defined Later

			//$new_transfer_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GSTST', date("Y",time()), 5, "select transfer_prefix, transfer_prefix_number from inv_item_transfer_mst where company_id=$cbo_company_id and entry_form=133 and transfer_criteria=4 and item_category=13 and $year_cond=".date('Y',time())." order by id desc ", "transfer_prefix", "transfer_prefix_number" ));

			//$id=return_next_id( "id", "inv_item_transfer_mst", 1 ) ;

			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			//print_r($id); die;
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con, 1, $cbo_company_id, 'GSTST', 133, date("Y", time()), 13));

			$field_array = "id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria, to_company, entry_form, from_order_id, to_order_id, transfer_requ_no, transfer_requ_id, item_category,purpose,to_color_id,driver_name,mobile_no,vehicle_no,remarks, inserted_by, insert_date";

			$data_array = "(" . $id . ",'" . $new_transfer_system_id[1] . "'," . $new_transfer_system_id[2] . ",'" . $new_transfer_system_id[0] . "'," . $cbo_company_id . "," . $txt_challan_no . "," . $txt_transfer_date . "," . $cbo_transfer_criteria . "," . $cbo_to_company_id . ",133," . $txt_from_order_id . "," . $txt_to_order_id . "," . $txt_requisition_no . "," . $txt_requisition_id . ",13," . $cbo_purpose_id . "," . $hid_to_color_id . " ," . $txt_driver_name . " ," . $txt_mobile_no . " ," . $txt_vehicle_no . " ," . $txt_remark . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			//  echo "insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			/*$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;*/

			$transfer_recv_num = $new_transfer_system_id[0];
			$transfer_update_id = $id;
		} else {
			$field_array_update = "challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
			$data_array_update = $txt_challan_no . "*" . $txt_transfer_date . "*" . $txt_from_order_id . "*" . $txt_to_order_id . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; */

			$transfer_recv_num = str_replace("'", "", $txt_system_id);
			$transfer_update_id = str_replace("'", "", $update_id);
		}

		$rate = 0;
		$amount = 0;
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;

		$field_array_prod_insert = "id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, gsm, dia_width, inserted_by, insert_date";

		$field_array_prod_update = "current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";

		$field_array_trans = "id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, floor_id, room, rack, self, bin_box, program_no, stitch_length,store_id, inserted_by, insert_date,body_part_id";

		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		$field_array_dtls = "id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, y_count, brand_id, yarn_lot, color_names, floor_id, room, rack, shelf,bin_box, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, from_program, to_program, stitch_length,from_store,to_store, transfer_requ_dtls_id, inserted_by, insert_date, body_part_id, to_body_part,remarks";

		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_roll = "id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, rate, amount, booking_no, roll_no, roll_id, from_roll_id, is_transfer, transfer_criteria,is_sales, re_transfer, inserted_by, insert_date";

		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$field_array_proportionate = "id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity,is_sales, inserted_by, insert_date";

		if (str_replace("'", "", $cbo_transfer_criteria) == 1) // Company to Company
		{
			$rollIds = '';
			for ($j = 1; $j <= $total_row; $j++) {
				$barcodeNo = "barcodeNo_" . $j;
				$rollNo = "rollNo_" . $j;
				$progId = "progId_" . $j;
				$productId = "productId_" . $j;
				$rollId = "rollId_" . $j;
				$rollWgt = "rollWgt_" . $j;
				$yarnLot = "yarnLot_" . $j;
				$colorName = "colorName_" . $j;
				$yarnCount = "yarnCount_" . $j;
				$stichLn = "stichLn_" . $j;
				$brandId = "brandId_" . $j;
				$floor = "floor_" . $j;
				$room = "room_" . $j;
				$rack = "rack_" . $j;
				$shelf = "shelf_" . $j;
				$binbox = "binbox_" . $j;
				$transRollId = "transRollId_" . $j;
				$storeId = "storeId_" . $j;
				$requiDtlsId = "requiDtlsId_" . $j;
				$cbo_floor_to = "cbo_floor_to_" . $j;
				$cbo_room_to = "cbo_room_to_" . $j;
				$txt_rack_to = "txt_rack_to_" . $j;
				$txt_shelf_to = "txt_shelf_to_" . $j;
				$txt_bin_to = "txt_bin_to_" . $j;
				$frombodypartId = "frombodypartId_" . $j;
				$cboToBodyPart = "cbo_To_BodyPart_" . $j;
				$txtRemarks = "txtRemarks_" . $j;
				$constructCompo = "ItemDtls_" . $j;
				$ItemDesc = "ItemDesc_" . $j;
				$rollRate = "rollRate_" . $j;
				$rollAmount = "rollAmount_" . $j;
				//echo "10**txt_bin_to : ".$$txt_bin_to;die;
				$data_item = explode("*", str_replace("'", "", $$ItemDesc));
				$detarmination_id = $data_item[0];
				$gsm = $data_item[1];
				$diaWidth = strtoupper($data_item[2]);

				$rollIds .= $$transRollId . ",";

				$cons_rate = str_replace("'", "", $$rollRate);
				$cons_amount = str_replace("'", "", $$rollWgt) * $cons_rate;

				$barcode_ref_arr[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$rollWgt);

				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

				if ($diaWidth != "") {
					$dia_cond = " and upper(dia_width)='$diaWidth'";
				} else {

					if ($db_type == 0) {
						$dia_cond = " and upper(dia_width)='$diaWidth'";
					} else {
						$dia_cond = " and upper(dia_width) is null ";
					}
				}
				$row_prod = sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_to_company_id and item_category_id=13 and detarmination_id=$detarmination_id and gsm='$gsm' $dia_cond and status_active=1 and is_deleted=0");
				if (count($row_prod) > 0 || $new_prod_ref_arr[$cbo_to_company_id . "**" . $detarmination_id . "**" . $gsm . "**" . $diaWidth . "**13"] != "") {
					if (count($row_prod) > 0) {
						$new_prod_id = $row_prod[0][csf('id')];
						$product_id_update_parameter[$new_prod_id]['qnty'] += str_replace("'", "", $$rollWgt);
						$product_id_update_parameter[$new_prod_id]['amount'] += $cons_amount;
						$update_to_prod_id[$new_prod_id] = $new_prod_id;
					} else {
						$new_prod_id = $new_prod_ref_arr[$cbo_to_company_id . "**" . $detarmination_id . "**" . $gsm . "**" . $diaWidth . "**13"];
						$product_id_insert_parameter[$new_prod_id . "**" . $detarmination_id . "**" . $gsm . "**" . $diaWidth . "**" . $$constructCompo . "**13"] += str_replace("'", "", $$rollWgt);
						$product_id_insert_amount[$new_prod_id . "**" . $detarmination_id . "**" . $gsm . "**" . $diaWidth . "**" . $$constructCompo . "**13"] += $cons_amount;
					}
				} else {
					$new_prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
					$new_prod_ref_arr[$cbo_to_company_id . "**" . $detarmination_id . "**" . $gsm . "**" . $diaWidth . "**13"] = $new_prod_id;
					$product_id_insert_parameter[$new_prod_id . "**" . $detarmination_id . "**" . $gsm . "**" . $diaWidth . "**" . $$constructCompo . "**13"] += str_replace("'", "", $$rollWgt);
					$product_id_insert_amount[$new_prod_id . "**" . $detarmination_id . "**" . $gsm . "**" . $diaWidth . "**" . $$constructCompo . "**13"] += $cons_amount;
				}

				if ($data_array_trans != "") $data_array_trans .= ",";
				$data_array_trans .= "(" . $id_trans . "," . $transfer_update_id . "," . $cbo_company_id . "," . $$productId . ",13,6," . $txt_transfer_date . "," . $txt_from_order_id . ",12," . $$rollWgt . "," . $cons_rate . "," . $cons_amount . "," . $$floor . "," . $$room . "," . $$rack . "," . $$shelf . "," . $$binbox . "," . $$progId . "," . $$stichLn . "," . $$storeId . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$frombodypartId . ")";

				$from_trans_id = $id_trans;

				if ($data_array_prop != "") $data_array_prop .= ",";
				$data_array_prop .= "(" . $id_prop . "," . $from_trans_id . ",6,133," . $id_dtls . "," . $txt_from_order_id . "," . $$productId . "," . $$rollWgt . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$to_trans_id = 0;
				if ($variable_auto_rcv == 1) // if auto receive 1 No, then no need to acknowledgement
				{
					$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$to_trans_id = $id_trans;
					$data_array_trans .= ",(" . $id_trans . "," . $transfer_update_id . "," . $cbo_to_company_id . "," . $new_prod_id . ",13,5," . $txt_transfer_date . "," . $txt_to_order_id . ",12," . $$rollWgt . "," . $cons_rate . "," . $cons_amount . "," . $$cbo_floor_to . "," . $$cbo_room_to . "," . $$txt_rack_to . "," . $$txt_shelf_to . "," . $$txt_bin_to . "," . $$progId . "," . $$stichLn . "," . $cbo_store_name . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$cboToBodyPart . ")";

					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					$data_array_prop .= ",(" . $id_prop . "," . $id_trans . ",5,133," . $id_dtls . "," . $txt_to_order_id . "," . $new_prod_id . "," . $$rollWgt . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				}

				if ($data_array_dtls != "") $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $id_dtls . "," . $transfer_update_id . "," . $from_trans_id . "," . $to_trans_id . "," . $$productId . "," . $new_prod_id . ",13," . $$rollWgt . "," . $$rollNo . "," . $cons_rate . "," . $cons_amount . ",12," . $$yarnCount . "," . $$brandId . "," . $$yarnLot . "," . $$colorName . "," . $$floor . "," . $$room . "," . $$rack . "," . $$shelf . "," . $$binbox . "," . $$cbo_floor_to . "," . $$cbo_room_to . "," . $$txt_rack_to . "," . $$txt_shelf_to . "," . $$txt_bin_to . "," . $$progId . "," . $$progId . "," . $$stichLn . "," . $$storeId . "," . $cbo_store_name . "," . $$requiDtlsId . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$frombodypartId . "," . $$cboToBodyPart . "," . $$txtRemarks . ")";

				if ($variable_auto_rcv == 1) // if Auto recv No 1
				{
					$re_transfer = 0;
				} else {
					$re_transfer = 1;
				}

				if ($data_array_roll != "") $data_array_roll .= ",";
				$data_array_roll .= "(" . $id_roll . "," . $$barcodeNo . "," . $transfer_update_id . "," . $id_dtls . "," . $txt_to_order_id . ",133," . $$rollWgt . "," . $cons_rate . "," . $cons_amount . "," . $$progId . "," . $$rollNo . "," . $$rollId . "," . $$transRollId . ",5,1,1," . $re_transfer . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$inserted_roll_id_arr[$id_roll] =  $id_roll;
				$barcode_id[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);

				// echo str_replace("'", "", $$productId).'++++++++';
				$prodData_array[str_replace("'", "", $$productId)] += str_replace("'", "", $$rollWgt);
				$prodData_amount_array[str_replace("'", "", $$productId)] += str_replace("'", "", $$rollAmount);
				$all_prod_id .= $$productId . ",";

				// echo '<pre>';print_r($prodData_array);
			}

			// echo '<pre>';print_r($product_id_insert_parameter);
			if (!empty($product_id_insert_parameter)) {
				foreach ($product_id_insert_parameter as $key => $val) {
					$prod_description_arr = explode("**", $key);
					$prod_id = $prod_description_arr[0];
					$fabric_desc_id = $prod_description_arr[1];
					$txt_gsm = $prod_description_arr[2];
					$txt_width = $prod_description_arr[3];
					$cons_compo = $prod_description_arr[4];

					$roll_amount = $product_id_insert_amount[$key];

					$avg_rate_per_unit = $roll_amount / $val;

					$prod_name_dtls = trim($cons_compo);

					if ($variable_auto_rcv == 2) // if Auto recv Yes 2 need to ack
					{
						$avg_rate_per_unit = 0;
						$val = 0;
						$roll_amount = 0;
					}
					// if Qty is zero then rate & value will be zero
					if ($val <= 0) {
						$roll_amount = 0;
						$avg_rate_per_unit = 0;
					}

					if ($data_array_prod_insert != "") $data_array_prod_insert .= ",";
					$data_array_prod_insert .= "(" . $prod_id . "," . $cbo_to_company_id . "," . $cbo_store_name . ",13," . $fabric_desc_id . "," . $cons_compo . "," . $prod_name_dtls . "," . "12" . "," . $avg_rate_per_unit . "," . $val . "," . $val . "," . $roll_amount . "," . $txt_gsm . ",'" . $txt_width . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				}
			}

			if (!empty($update_to_prod_id)) {
				$prod_id_array = array();
				$up_to_prod_ids = implode(",", array_unique($update_to_prod_id));
				//echo "10**select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($up_to_prod_ids) ";die;
				$toProdIssueResult = sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($up_to_prod_ids) ");
				foreach ($toProdIssueResult as $row) {
					if ($variable_auto_rcv == 1) // if auto receive 1 No, then no need to acknowledgement
					{
						$stock_qnty = $product_id_update_parameter[$row[csf("id")]]['qnty'] + $row[csf("current_stock")];
						$stock_value = $product_id_update_parameter[$row[csf("id")]]['amount'] + $row[csf("stock_value")];
					} else // if auto receive 2 Yes need to ack
					{
						$stock_qnty =  $row[csf("current_stock")];
						$stock_value =  $row[csf("stock_value")];
					}
					if ($stock_qnty > 0) {
						$avg_rate_per_unit = $stock_value / $stock_qnty;
						$stock_value = $avg_rate_per_unit * $stock_qnty;
					} else {
						$avg_rate_per_unit = 0;
						$stock_value = 0;
					}
					// if Qty is zero then rate & value will be zero
					if ($stock_qnty <= 0) {
						$stock_value = 0;
						$avg_rate_per_unit = 0;
					}

					// echo "10**".$avg_rate_per_unit.'==='.$stock_value.'############';
					$prod_id_array[] = $row[csf('id')];
					$data_array_prod_update[$row[csf('id')]] = explode("*", ("'" . $stock_qnty . "'*'" . $avg_rate_per_unit . "'*'" . $stock_value . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
				}
				unset($toProdIssueResult);
			}


			$all_prod_id_arr = implode(",", array_unique(explode(",", chop($all_prod_id, ','))));
			$fromProdIssueResult = sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($all_prod_id_arr) and company_id=$cbo_company_id");
			foreach ($fromProdIssueResult as $row) {
				// echo $row[csf('id')].'@@';
				$issue_qty = $prodData_array[$row[csf('id')]];
				$issue_amount = $prodData_amount_array[$row[csf('id')]];

				$current_stock = $row[csf('current_stock')] - $issue_qty;
				$current_amount = $row[csf('stock_value')] - $issue_amount;
				$current_avg_rate = $row[csf('stock_value')] - $issue_amount;

				// if Qty is zero then rate & value will be zero
				if ($current_stock <= 0) {
					$current_amount = 0;
					$current_avg_rate = 0;
				}

				$prod_id_array[] = $row[csf('id')];
				$data_array_prod_update[$row[csf('id')]] = explode("*", ("'" . $current_stock . "'*'" . $current_avg_rate . "'*'" . $current_amount . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
			}
		} else // order to order and store to store
		{
			$rollIds = '';
			for ($j = 1; $j <= $total_row; $j++) {
				$barcodeNo = "barcodeNo_" . $j;
				$rollNo = "rollNo_" . $j;
				$progId = "progId_" . $j;
				$productId = "productId_" . $j;
				$rollId = "rollId_" . $j;
				$rollWgt = "rollWgt_" . $j;
				$yarnLot = "yarnLot_" . $j;
				$colorName = "colorName_" . $j;
				$yarnCount = "yarnCount_" . $j;
				$stichLn = "stichLn_" . $j;
				$brandId = "brandId_" . $j;
				$floor = "floor_" . $j;
				$room = "room_" . $j;
				$rack = "rack_" . $j;
				$shelf = "shelf_" . $j;
				$binbox = "binbox_" . $j;
				$transRollId = "transRollId_" . $j;
				$storeId = "storeId_" . $j;
				$requiDtlsId = "requiDtlsId_" . $j;
				$cbo_floor_to = "cbo_floor_to_" . $j;
				$cbo_room_to = "cbo_room_to_" . $j;
				$txt_rack_to = "txt_rack_to_" . $j;
				$txt_shelf_to = "txt_shelf_to_" . $j;
				$txt_bin_to = "txt_bin_to_" . $j;
				$frombodypartId = "frombodypartId_" . $j;
				$cboToBodyPart = "cbo_To_BodyPart_" . $j;
				$txtRemarks = "txtRemarks_" . $j;
				$constructCompo = "ItemDtls_" . $j;
				$ItemDesc = "ItemDesc_" . $j;
				$rollRate = "rollRate_" . $j;
				$rollAmount = "rollAmount_" . $j;
				//echo "10**txt_bin_to : ".$$txt_bin_to;die;

				$rollIds .= $$transRollId . ",";

				$cons_rate = str_replace("'", "", $$rollRate);
				$cons_amount = str_replace("'", "", $$rollWgt) * $cons_rate;

				$barcode_ref_arr[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$rollWgt);

				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);


				if ($data_array_trans != "") $data_array_trans .= ",";
				$data_array_trans .= "(" . $id_trans . "," . $transfer_update_id . "," . $cbo_company_id . "," . $$productId . ",13,6," . $txt_transfer_date . "," . $txt_from_order_id . ",12," . $$rollWgt . "," . $cons_rate . "," . $cons_amount . "," . $$floor . "," . $$room . "," . $$rack . "," . $$shelf . "," . $$binbox . "," . $$progId . "," . $$stichLn . "," . $$storeId . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$frombodypartId . ")";

				$from_trans_id = $id_trans;

				if ($data_array_prop != "") $data_array_prop .= ",";
				$data_array_prop .= "(" . $id_prop . "," . $from_trans_id . ",6,133," . $id_dtls . "," . $txt_from_order_id . "," . $$productId . "," . $$rollWgt . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$to_trans_id = 0;
				if ($variable_auto_rcv == 1) // if auto receive 1 No, then no need to acknowledgement
				{
					$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$to_trans_id = $id_trans;

					$data_array_trans .= ",(" . $id_trans . "," . $transfer_update_id . "," . $cbo_to_company_id . "," . $$productId . ",13,5," . $txt_transfer_date . "," . $txt_to_order_id . ",12," . $$rollWgt . "," . $cons_rate . "," . $cons_amount . "," . $$cbo_floor_to . "," . $$cbo_room_to . "," . $$txt_rack_to . "," . $$txt_shelf_to . "," . $$txt_bin_to . "," . $$progId . "," . $$stichLn . "," . $cbo_store_name . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$cboToBodyPart . ")";

					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					$data_array_prop .= ",(" . $id_prop . "," . $id_trans . ",5,133," . $id_dtls . "," . $txt_to_order_id . "," . $$productId . "," . $$rollWgt . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				}

				if ($data_array_dtls != "") $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $id_dtls . "," . $transfer_update_id . "," . $from_trans_id . "," . $to_trans_id . "," . $$productId . "," . $$productId . ",13," . $$rollWgt . "," . $$rollNo . "," . $cons_rate . "," . $cons_amount . ",12," . $$yarnCount . "," . $$brandId . "," . $$yarnLot . "," . $$colorName . "," . $$floor . "," . $$room . "," . $$rack . "," . $$shelf . "," . $$binbox . "," . $$cbo_floor_to . "," . $$cbo_room_to . "," . $$txt_rack_to . "," . $$txt_shelf_to . "," . $$txt_bin_to . "," . $$progId . "," . $$progId . "," . $$stichLn . "," . $$storeId . "," . $cbo_store_name . "," . $$requiDtlsId . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$frombodypartId . "," . $$cboToBodyPart . "," . $$txtRemarks . ")";

				if ($variable_auto_rcv == 1) // if Auto recv No 1
				{
					$re_transfer = 0;
				} else {
					$re_transfer = 1;
				}

				if ($data_array_roll != "") $data_array_roll .= ",";
				$data_array_roll .= "(" . $id_roll . "," . $$barcodeNo . "," . $transfer_update_id . "," . $id_dtls . "," . $txt_to_order_id . ",133," . $$rollWgt . "," . $cons_rate . "," . $cons_amount . "," . $$progId . "," . $$rollNo . "," . $$rollId . "," . $$transRollId . ",5," . $cbo_transfer_criteria . ",1," . $re_transfer . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$inserted_roll_id_arr[$id_roll] =  $id_roll;
				$barcode_id[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);
			}
		}

		$check_if_already_scanned = sql_select("select a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and b.entry_form = 61 and  a.entry_form=61 and a.is_returned!=1 and a.barcode_no in (" . implode(",", array_filter($barcode_id)) . ") and a.status_active = 1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0");

		foreach ($check_if_already_scanned as $val) {
			if ($val[csf("barcode_no")]) {
				echo "20**Sorry! Barcode already Scanned. Challan No: " . $val[csf("issue_number")] . " Barcode No : " . $val[csf("barcode_no")];
				die;
			}
		}

		$trans_check_sql = sql_select("select barcode_no, entry_form, po_breakdown_id, qnty from pro_roll_details where barcode_no in (" . implode(",", array_filter($barcode_id)) . ") and entry_form in ( 58,84,133) and re_transfer =0 and status_active = 1 and is_deleted = 0");

		foreach ($trans_check_sql as $val) {
			if ($val[csf("po_breakdown_id")]  !=  str_replace("'", "", $txt_from_order_id)) {
				echo "20**Sorry! This barcode " . str_replace("'", "", $$barcodeNo) . " doesn't belong to this sales order " . $txt_from_order_no . "";
				die;
			}

			if ($val[csf("qnty")]  !=  $barcode_ref_arr[$val[csf("barcode_no")]]) {
				echo "20**Sorry! current quantity does not match with original qnty. Barcode no: " . $val[csf("barcode_no")] . "";
				die;
			}
		}


		if (str_replace("'", "", $update_id) == "") {
			$rID = sql_insert("inv_item_transfer_mst", $field_array, $data_array, 0);
			if ($rID) $flag = 1;
			else $flag = 0;
		} else {
			$rID = sql_update("inv_item_transfer_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
			if ($rID) $flag = 1;
			else $flag = 0;
		}

		if (str_replace("'", "", $cbo_transfer_criteria) == 1) {
			if ($data_array_prod_insert != "") {
				// echo "10**insert into product_details_master (".$field_array_prod_insert.") values ".$data_array_prod_insert;die;
				$rID7 = sql_insert("product_details_master", $field_array_prod_insert, $data_array_prod_insert, 0);
				if ($rID7) $flag = 1;
				else $flag = 0;
			}
			// echo "10**".bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );die;
			$prodUpdate = execute_query(bulk_update_sql_statement("product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array));
		}

		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$rID2 = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 0);
		if ($flag == 1) {
			if ($rID2) $flag = 1;
			else $flag = 0;
		}

		// echo "10**insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID3 = sql_insert("inv_item_transfer_dtls", $field_array_dtls, $data_array_dtls, 0);
		if ($flag == 1) {
			if ($rID3) $flag = 1;
			else $flag = 0;
		}

		$rollIds = chop($rollIds, ',');
		$rID4 = sql_multirow_update("pro_roll_details", "is_transfer*transfer_criteria*re_transfer", "6*$cbo_transfer_criteria*1", "id", $rollIds, 0);
		if ($flag == 1) {
			if ($rID4) $flag = 1;
			else $flag = 0;
		}


		// echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
		$rID5 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 1);
		if ($flag == 1) {
			if ($rID5) $flag = 1;
			else $flag = 0;
		}

		// echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID6 = sql_insert("order_wise_pro_details", $field_array_proportionate, $data_array_prop, 1);
		if ($flag == 1) {
			if ($rID6) $flag = 1;
			else $flag = 0;
		}

		if (str_replace("'", "", $txt_requisition_id) != "") {
			if (str_replace("'", "", $requisition_and_order_basis) != 1) // 1 (Yes) for urmi variable setting
			{
				$cbo_complete_status = 2;
			}
			$requi_field_array_update = "requisition_status*updated_by*update_date";
			$requi_data_array_update = $cbo_complete_status . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
			$rID7 = sql_update("inv_item_transfer_requ_mst", $requi_field_array_update, $requi_data_array_update, "id", $txt_requisition_id, 1);
			if ($rID7) $flag = 1;
			else $flag = 0;
		}

		$rID8 = execute_query("update pro_roll_details set is_returned=1 where barcode_no in (" . implode(',', $barcode_id) . ") and id not in (" . implode(',', $inserted_roll_id_arr) . ")");
		if ($flag == 1) {
			if ($rID8)
				$flag = 1;
			else
				$flag = 0;
		}

		// echo "10**$rID##$rID2##$rID3##$rID4##$rID5##$rID6##$rID7";oci_rollback($con);die;
		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "0**" . $transfer_update_id . "**" . $transfer_recv_num . "**0";
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**" . "&nbsp;" . "**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "0**" . $transfer_update_id . "**" . $transfer_recv_num . "**0";
			} else {
				oci_rollback($con);
				echo "5**0**" . "&nbsp;" . "**0";
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		/**
		 * list of fields that will not change/update on update button event
		 * fields=> from_order_id*to_order_id*
		 * data => $txt_from_order_id."*".$txt_to_order_id."*".
		 */
		$field_array_update = "challan_no*transfer_date*to_color_id*driver_name*mobile_no*vehicle_no*remarks*updated_by*update_date";
		$data_array_update = $txt_challan_no . "*" . $txt_transfer_date . "*" . $hid_to_color_id . "*" .$txt_driver_name. "*" .$txt_mobile_no. "*" .$txt_vehicle_no."*".$txt_remark."*". $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$rate = 0;
		$amount = 0;
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;

		$all_prod_id = "";
		$field_array_prod_insert = "id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, gsm, dia_width, inserted_by, insert_date";

		$field_array_trans = "id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, floor_id, room, rack, self, bin_box,program_no, stitch_length,store_id, inserted_by, insert_date,body_part_id";
		$field_array_trans_update = "prod_id*transaction_date*order_id*cons_quantity*cons_rate*cons_amount*store_id*floor_id*room*rack*self*bin_box*program_no*stitch_length*body_part_id*updated_by*update_date";

		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		$field_array_dtls = "id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, y_count, brand_id, yarn_lot, color_names, floor_id, room, rack, shelf, bin_box, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, from_program, to_program, stitch_length,from_store,to_store, transfer_requ_dtls_id, inserted_by, insert_date, body_part_id, to_body_part,remarks";

		$field_array_dtls_update = "from_prod_id*to_prod_id*transfer_qnty*roll*rate*transfer_value*y_count*brand_id*yarn_lot*from_store*to_store*floor_id*room*rack*shelf*bin_box*to_floor_id*to_room*to_rack*to_shelf*to_bin_box*from_program*to_program*stitch_length*to_body_part*updated_by*update_date";

		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_roll = "id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, rate, amount, booking_no, roll_no, roll_id, from_roll_id, is_transfer, transfer_criteria, is_sales, re_transfer, inserted_by, insert_date";
		$field_array_updateroll = "qnty*booking_no*roll_no*updated_by*update_date";

		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$field_array_proportionate = "id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, is_sales, inserted_by, insert_date";

		$field_array_prod_update = "current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";

		$rollIds = '';
		$update_dtls_id = '';
		$update_to_prod_id = array();
		$deleted_prod_id_arr = array();
		$update_from_prod_id_arr = array();
		if (str_replace("'", "", $cbo_transfer_criteria) == 1) // update Company to company
		{
			for ($j = 1; $j <= $total_row; $j++) {
				$barcodeNo = "barcodeNo_" . $j;
				$all_barcodeNo .= $$barcodeNo . ",";
				$rollNo = "rollNo_" . $j;
				$progId = "progId_" . $j;
				$productId = "productId_" . $j;
				$rollId = "rollId_" . $j;
				$rollWgt = "rollWgt_" . $j;
				$yarnLot = "yarnLot_" . $j;
				$colorName = "colorName_" . $j;
				$yarnCount = "yarnCount_" . $j;
				$stichLn = "stichLn_" . $j;
				$brandId = "brandId_" . $j;
				$floor = "floor_" . $j;
				$room = "room_" . $j;
				$rack = "rack_" . $j;
				$shelf = "shelf_" . $j;
				$binbox = "binbox_" . $j;
				$dtlsId = "dtlsId_" . $j;
				$transIdFrom = "transIdFrom_" . $j;
				$transIdTo = "transIdTo_" . $j;
				$rolltableId = "rolltableId_" . $j;
				$transRollId = "transRollId_" . $j; //rollMstId
				$storeId = "storeId_" . $j;
				$requiDtlsId = "requiDtlsId_" . $j;
				$cbo_floor_to = "cbo_floor_to_" . $j;
				$cbo_room_to = "cbo_room_to_" . $j;
				$txt_rack_to = "txt_rack_to_" . $j;
				$txt_shelf_to = "txt_shelf_to_" . $j;
				$txt_bin_to = "txt_bin_to_" . $j;
				$frombodypartId = "frombodypartId_" . $j;
				$cboToBodyPart = "cbo_To_BodyPart_" . $j;
				$txtRemarks = "txtRemarks_" . $j;
				$constructCompo = "ItemDtls_" . $j;
				$ItemDesc = "ItemDesc_" . $j;
				$rollRate = "rollRate_" . $j;
				$rollAmount = "rollAmount_" . $j;
				$toProductUp = "toProductUp_" . $j;
				$data_item = explode("*", str_replace("'", "", $$ItemDesc));
				$detarmination_id = $data_item[0];
				$gsm = $data_item[1];
				$diaWidth = strtoupper($data_item[2]);

				$cons_rate = str_replace("'", "", $$rollRate);
				$cons_amount = str_replace("'", "", $$rollWgt) * $cons_rate;

				if ($$rolltableId != "") {
					$saved_roll_arr[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$rolltableId);
				} else {
					$new_roll_arr[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$transRollId);
				}

				if (str_replace("'", "", $$rolltableId) > 0) // Company to company update
				{
					$update_dtls_id .= str_replace("'", "", $$dtlsId) . ",";


					$transId_arr[] = str_replace("'", "", $$transIdFrom);
					$data_array_update_trans[str_replace("'", "", $$transIdFrom)] = explode("*", ($$productId . "*" . $txt_transfer_date . "*" . $txt_from_order_id . "*" . $$rollWgt . "*" . $cons_rate . "*" . $cons_amount . "*" . $$storeId . "*" . $$floor . "*" . $$room . "*" . $$rack . "*" . $$shelf . "*" . $$binbox . "*" . $$progId . "*" . $$stichLn . "*" . $$frombodypartId . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));

					if ($variable_auto_rcv == 1) {
						if (str_replace("'", "", $$transIdTo)  != "" && str_replace("'", "", $$transIdTo) != 0) {
							$transId_arr[] = str_replace("'", "", $$transIdTo);
							$data_array_update_trans[str_replace("'", "", $$transIdTo)] = explode("*", ($$toProductUp . "*" . $txt_transfer_date . "*" . $txt_to_order_id . "*" . $$rollWgt . "*" . $cons_rate . "*" . $cons_amount . "*" . $cbo_store_name . "*" . $$cbo_floor_to . "*" . $$cbo_room_to . "*" . $$txt_rack_to . "*" . $$txt_shelf_to . "*" . $$txt_bin_to . "*" . $$progId . "*" . $$stichLn . "*" . $$cboToBodyPart . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
						}
					}

					$dtlsId_arr[] = str_replace("'", "", $$dtlsId);
					$data_array_update_dtls[str_replace("'", "", $$dtlsId)] = explode("*", ($$productId . "*" . $$toProductUp . "*" . $$rollWgt . "*" . $$rollNo . "*" . $cons_rate . "*" . $cons_amount . "*" . $$yarnCount . "*" . $$brandId . "*" . $$yarnLot . "*" . $$storeId . "*" . $cbo_store_name . "*" . $$floor . "*" . $$room . "*" . $$rack . "*" . $$shelf . "*" . $$binbox . "*" . $$cbo_floor_to . "*" . $$cbo_room_to . "*" . $$txt_rack_to . "*" . $$txt_shelf_to . "*" . $$txt_bin_to . "*" . $$progId . "*" . $$progId . "*" . $$stichLn . "*" . $$cboToBodyPart . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));

					$rollId_arr[] = str_replace("'", "", $$rolltableId);
					$data_array_update_roll[str_replace("'", "", $$rolltableId)] = explode("*", ($$rollWgt . "*" . $$progId . "*" . $$rollNo . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));

					$dtlsIdProp = str_replace("'", "", $$dtlsId);
					$transIdfromProp = str_replace("'", "", $$transIdFrom);
					$transIdtoProp = str_replace("'", "", $$transIdTo);

					$new_prod_id = $$toProductUp;
				} else // Company to company New insert
				{
					$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
					$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

					if ($diaWidth != "") {
						$dia_cond = " and upper(dia_width)='$diaWidth'";
					} else {

						if ($db_type == 0) {
							$dia_cond = " and upper(dia_width)='$diaWidth'";
						} else {
							$dia_cond = " and upper(dia_width) is null ";
						}
					}

					$row_prod = sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_to_company_id and item_category_id=13 and detarmination_id=$detarmination_id and gsm='$gsm' $dia_cond and status_active=1 and is_deleted=0");
					if (count($row_prod) > 0 || $new_prod_ref_arr[$cbo_to_company_id . "**" . $detarmination_id . "**" . $gsm . "**" . $diaWidth . "**13"] != "") {
						if (count($row_prod) > 0) {
							$new_prod_id = $row_prod[0][csf('id')];
							$product_id_update_parameter[$new_prod_id]['qnty'] += str_replace("'", "", $$rollWgt);
							$product_id_update_parameter[$new_prod_id]['amount'] += $cons_amount;
							$update_to_prod_id[$new_prod_id] = $new_prod_id;
						} else {
							$new_prod_id = $new_prod_ref_arr[$cbo_to_company_id . "**" . $detarmination_id . "**" . $gsm . "**" . $diaWidth . "**13"];
							$product_id_insert_parameter[$new_prod_id . "**" . $detarmination_id . "**" . $gsm . "**" . $diaWidth . "**" . $$constructCompo . "**13"] += str_replace("'", "", $$rollWgt);
							$product_id_insert_amount[$new_prod_id . "**" . $detarmination_id . "**" . $gsm . "**" . $diaWidth . "**" . $$constructCompo . "**13"] += $cons_amount;
						}
					} else {
						$new_prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
						$new_prod_ref_arr[$cbo_to_company_id . "**" . $detarmination_id . "**" . $gsm . "**" . $diaWidth . "**13"] = $new_prod_id;
						$product_id_insert_parameter[$new_prod_id . "**" . $detarmination_id . "**" . $gsm . "**" . $diaWidth . "**" . $$constructCompo . "**13"] += str_replace("'", "", $$rollWgt);
						$product_id_insert_amount[$new_prod_id . "**" . $detarmination_id . "**" . $gsm . "**" . $diaWidth . "**" . $$constructCompo . "**13"] += $cons_amount;
					}

					$rollIds .= $$transRollId . ",";
					if ($data_array_trans != "") $data_array_trans .= ",";
					$data_array_trans .= "(" . $id_trans . "," . $update_id . "," . $cbo_company_id . "," . $$productId . ",13,6," . $txt_transfer_date . "," . $txt_from_order_id . ",12," . $$rollWgt . "," . $cons_rate . "," . $cons_amount . "," . $$floor . "," . $$room . "," . $$rack . "," . $$shelf . "," . $$binbox . "," . $$progId . "," . $$stichLn . "," . $$storeId . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$frombodypartId . ")";

					$transIdfromProp = $id_trans;

					$transIdtoProp = 0;
					if ($variable_auto_rcv == 1) // if auto receive 1 No, then no need to acknowledgement
					{
						$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
						$transIdtoProp = $id_trans;
						$data_array_trans .= ",(" . $id_trans . "," . $update_id . "," . $cbo_to_company_id . "," . $new_prod_id . ",13,5," . $txt_transfer_date . "," . $txt_to_order_id . ",12," . $$rollWgt . "," . $cons_rate . "," . $cons_amount . "," . $$cbo_floor_to . "," . $$cbo_room_to . "," . $$txt_rack_to . "," . $$txt_shelf_to . "," . $$txt_bin_to . "," . $$progId . "," . $$stichLn . "," . $cbo_store_name . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$cboToBodyPart . ")";
					}

					if ($data_array_dtls != "") $data_array_dtls .= ",";
					$data_array_dtls .= "(" . $id_dtls . "," . $update_id . "," . $transIdfromProp . "," . $transIdtoProp . "," . $$productId . "," . $new_prod_id . ",13," . $$rollWgt . "," . $$rollNo . "," . $cons_rate . "," . $cons_amount . ",12," . $$yarnCount . "," . $$brandId . "," . $$yarnLot . "," . $$colorName . "," . $$floor . "," . $$room . "," . $$rack . "," . $$shelf . "," . $$binbox . "," . $$cbo_floor_to . "," . $$cbo_room_to . "," . $$txt_rack_to . "," . $$txt_shelf_to . "," . $$txt_bin_to . "," . $$progId . "," . $$progId . "," . $$stichLn . "," . $$storeId . "," . $cbo_store_name . "," . $$requiDtlsId . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$frombodypartId . "," . $$cboToBodyPart . "," . $$txtRemarks . ")";

					if ($variable_auto_rcv == 1) // if Auto recv No 1
					{
						$re_transfer = 0;
					} else {
						$re_transfer = 1;
					}

					if ($data_array_roll != "") $data_array_roll .= ",";
					$data_array_roll .= "(" . $id_roll . "," . $$barcodeNo . "," . $update_id . "," . $id_dtls . "," . $txt_to_order_id . ",133," . $$rollWgt . "," . $cons_rate . "," . $cons_amount . "," . $$progId . "," . $$rollNo . "," . $$rollId . "," . $$transRollId . ",5,1,1," . $re_transfer . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$dtlsIdProp = $id_dtls;
					$all_trans_roll_id .= $$transRollId . ",";

					$barcode_ref_arr[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$rollWgt);
					$inserted_roll_id_arr[$id_roll] =  $id_roll;
					$new_inserted[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);
					$all_prod_id .= $$productId . ","; // if new insert $$productId is from product id

					// echo str_replace("'", "", $$productId).'++++++++';
					$prodData_array[str_replace("'", "", $$productId)] += str_replace("'", "", $$rollWgt);
					$prodData_amount_array[str_replace("'", "", $$productId)] += str_replace("'", "", $$rollAmount);
				}
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				if ($data_array_prop != "") $data_array_prop .= ",";
				$data_array_prop .= "(" . $id_prop . "," . $transIdfromProp . ",6,133," . $dtlsIdProp . "," . $txt_from_order_id . "," . $$productId . "," . $$rollWgt . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				if ($variable_auto_rcv == 1) // if auto receive 1 No, then no need to acknowledgement
				{
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					$data_array_prop .= ",(" . $id_prop . "," . $transIdtoProp . ",5,133," . $dtlsIdProp . "," . $txt_to_order_id . "," . $new_prod_id . "," . $$rollWgt . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				}

				// echo str_replace("'", "", $$productId).'++++++++';
				//$prodData_array[str_replace("'", "", $$productId)]+=str_replace("'", "", $$rollWgt);
				//$prodData_amount_array[str_replace("'", "", $$productId)]+=str_replace("'", "", $$rollAmount);


			}
		} else // update Order to Order and Store to Store
		{
			for ($j = 1; $j <= $total_row; $j++) {
				$barcodeNo = "barcodeNo_" . $j;
				$all_barcodeNo .= $$barcodeNo . ",";
				$rollNo = "rollNo_" . $j;
				$progId = "progId_" . $j;
				$productId = "productId_" . $j;
				$rollId = "rollId_" . $j;
				$rollWgt = "rollWgt_" . $j;
				$yarnLot = "yarnLot_" . $j;
				$colorName = "colorName_" . $j;
				$yarnCount = "yarnCount_" . $j;
				$stichLn = "stichLn_" . $j;
				$brandId = "brandId_" . $j;
				$floor = "floor_" . $j;
				$room = "room_" . $j;
				$rack = "rack_" . $j;
				$shelf = "shelf_" . $j;
				$binbox = "binbox_" . $j;
				$dtlsId = "dtlsId_" . $j;
				$transIdFrom = "transIdFrom_" . $j;
				$transIdTo = "transIdTo_" . $j;
				$rolltableId = "rolltableId_" . $j;
				$transRollId = "transRollId_" . $j; //source_roll_Id
				$storeId = "storeId_" . $j;
				$requiDtlsId = "requiDtlsId_" . $j;
				$cbo_floor_to = "cbo_floor_to_" . $j;
				$cbo_room_to = "cbo_room_to_" . $j;
				$txt_rack_to = "txt_rack_to_" . $j;
				$txt_shelf_to = "txt_shelf_to_" . $j;
				$txt_bin_to = "txt_bin_to_" . $j;
				$frombodypartId = "frombodypartId_" . $j;
				$cboToBodyPart = "cbo_To_BodyPart_" . $j;
				$txtRemarks = "txtRemarks_" . $j;
				$constructCompo = "ItemDtls_" . $j;
				$ItemDesc = "ItemDesc_" . $j;
				$rollRate = "rollRate_" . $j;
				$rollAmount = "rollAmount_" . $j;
				$toProductUp = "toProductUp_" . $j;

				$cons_rate = str_replace("'", "", $$rollRate);
				$cons_amount = str_replace("'", "", $$rollWgt) * $cons_rate;

				if (str_replace("'", "", $$rolltableId) != "") {
					$saved_roll_arr[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$rolltableId);
				} else {
					$new_roll_arr[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$transRollId);
				}
				$barcode_wgt_arr[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$rollWgt);

				if (str_replace("'", "", $$rolltableId) > 0) // order to order update
				{
					$update_dtls_id .= str_replace("'", "", $$dtlsId) . ",";


					$transId_arr[] = str_replace("'", "", $$transIdFrom);
					$data_array_update_trans[str_replace("'", "", $$transIdFrom)] = explode("*", ($$productId . "*" . $txt_transfer_date . "*" . $txt_from_order_id . "*" . $$rollWgt . "*" . $cons_rate . "*" . $cons_amount . "*" . $$storeId . "*" . $$floor . "*" . $$room . "*" . $$rack . "*" . $$shelf . "*" . $$binbox . "*" . $$progId . "*" . $$stichLn . "*" . $$frombodypartId . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));

					if ($variable_auto_rcv == 1) // if auto receive 1 No, then no need to acknowledgement
					{
						if (str_replace("'", "", $$transIdTo)  != "" && str_replace("'", "", $$transIdTo) != 0) {
							$transId_arr[] = str_replace("'", "", $$transIdTo);
							$data_array_update_trans[str_replace("'", "", $$transIdTo)] = explode("*", ($$productId . "*" . $txt_transfer_date . "*" . $txt_to_order_id . "*" . $$rollWgt . "*" . $cons_rate . "*" . $cons_amount . "*" . $cbo_store_name . "*" . $$cbo_floor_to . "*" . $$cbo_room_to . "*" . $$txt_rack_to . "*" . $$txt_shelf_to . "*" . $$txt_bin_to . "*" . $$progId . "*" . $$stichLn . "*" . $$cboToBodyPart . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
						}
					}

					$dtlsId_arr[] = str_replace("'", "", $$dtlsId);
					$data_array_update_dtls[str_replace("'", "", $$dtlsId)] = explode("*", ($$productId . "*" . $$toProductUp . "*" . $$rollWgt . "*" . $$rollNo . "*" . $cons_rate . "*" . $cons_amount . "*" . $$yarnCount . "*" . $$brandId . "*" . $$yarnLot . "*" . $$storeId . "*" . $cbo_store_name . "*" . $$floor . "*" . $$room . "*" . $$rack . "*" . $$shelf . "*" . $$binbox . "*" . $$cbo_floor_to . "*" . $$cbo_room_to . "*" . $$txt_rack_to . "*" . $$txt_shelf_to . "*" . $$txt_bin_to . "*" . $$progId . "*" . $$progId . "*" . $$stichLn . "*" . $$cboToBodyPart . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));

					$rollId_arr[] = str_replace("'", "", $$rolltableId);
					$data_array_update_roll[str_replace("'", "", $$rolltableId)] = explode("*", ($$rollWgt . "*" . $$progId . "*" . $$rollNo . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));

					$dtlsIdProp = str_replace("'", "", $$dtlsId);
					$transIdfromProp = str_replace("'", "", $$transIdFrom);
					$transIdtoProp = str_replace("'", "", $$transIdTo);
				} else // order to order New insert
				{
					$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
					$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);


					$rollIds .= $$transRollId . ",";
					if ($data_array_trans != "") $data_array_trans .= ",";
					$data_array_trans .= "(" . $id_trans . "," . $update_id . "," . $cbo_company_id . "," . $$productId . ",13,6," . $txt_transfer_date . "," . $txt_from_order_id . ",12," . $$rollWgt . "," . $cons_rate . "," . $cons_amount . "," . $$floor . "," . $$room . "," . $$rack . "," . $$shelf . "," . $$binbox . "," . $$progId . "," . $$stichLn . "," . $$storeId . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$frombodypartId . ")";

					$transIdfromProp = $id_trans;
					$transIdtoProp = 0;
					if ($variable_auto_rcv == 1) // if auto receive 1 No, then no need to acknowledgement
					{
						$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
						$transIdtoProp = $id_trans;
						$data_array_trans .= ",(" . $id_trans . "," . $update_id . "," . $cbo_to_company_id . "," . $$productId . ",13,5," . $txt_transfer_date . "," . $txt_to_order_id . ",12," . $$rollWgt . "," . $cons_rate . "," . $cons_amount . "," . $$cbo_floor_to . "," . $$cbo_room_to . "," . $$txt_rack_to . "," . $$txt_shelf_to . "," . $$txt_bin_to . "," . $$progId . "," . $$stichLn . "," . $cbo_store_name . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$cboToBodyPart . ")";
					}

					if ($data_array_dtls != "") $data_array_dtls .= ",";
					$data_array_dtls .= "(" . $id_dtls . "," . $update_id . "," . $transIdfromProp . "," . $transIdtoProp . "," . $$productId . "," . $$productId . ",13," . $$rollWgt . "," . $$rollNo . "," . $cons_rate . "," . $cons_amount . ",12," . $$yarnCount . "," . $$brandId . "," . $$yarnLot . "," . $$colorName . "," . $$floor . "," . $$room . "," . $$rack . "," . $$shelf . "," . $$binbox . "," . $$cbo_floor_to . "," . $$cbo_room_to . "," . $$txt_rack_to . "," . $$txt_shelf_to . "," . $$txt_bin_to . "," . $$progId . "," . $$progId . "," . $$stichLn . "," . $$storeId . "," . $cbo_store_name . "," . $$requiDtlsId . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$frombodypartId . "," . $$cboToBodyPart . "," . $$txtRemarks . ")";

					if ($variable_auto_rcv == 1) // if Auto recv No 1
					{
						$re_transfer = 0;
					} else {
						$re_transfer = 1;
					}

					if ($data_array_roll != "") $data_array_roll .= ",";
					$data_array_roll .= "(" . $id_roll . "," . $$barcodeNo . "," . $update_id . "," . $id_dtls . "," . $txt_to_order_id . ",133," . $$rollWgt . "," . $cons_rate . "," . $cons_amount . "," . $$progId . "," . $$rollNo . "," . $$rollId . "," . $$transRollId . ",5," . $cbo_transfer_criteria . ",1," . $re_transfer . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$dtlsIdProp = $id_dtls;
					$all_trans_roll_id .= $$transRollId . ",";

					$barcode_ref_arr[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$rollWgt);
					$inserted_roll_id_arr[$id_roll] =  $id_roll;
					$new_inserted[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);
				}
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				if ($data_array_prop != "") $data_array_prop .= ",";
				$data_array_prop .= "(" . $id_prop . "," . $transIdfromProp . ",6,133," . $dtlsIdProp . "," . $txt_from_order_id . "," . $$productId . "," . $$rollWgt . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				if ($variable_auto_rcv == 1) // if auto receive 1 No, then no need to acknowledgement
				{
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					$data_array_prop .= ",(" . $id_prop . "," . $transIdtoProp . ",5,133," . $dtlsIdProp . "," . $txt_to_order_id . "," . $$productId . "," . $$rollWgt . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				}
			}
		}

		$all_barcodeNo = chop($all_barcodeNo, ',');
		$all_barcodeNo_arr = explode(",", $all_barcodeNo);

		if ($all_barcodeNo != "") {
			/*$next_transfer_sql = sql_select("SELECT max(a.id) as max_id,  a.barcode_no from pro_roll_details a
			where  a.barcode_no in ($all_barcodeNo) and a.status_active =1 and a.is_deleted=0 group by  a.barcode_no");*/

			$re_transfer_cond = ' and a.re_transfer=0';
			if ($variable_auto_rcv == 1) // if auto receive 1 No, then no need to acknowledgement
			{
				// $re_transfer=0;
				$re_transfer_cond = ' and a.re_transfer=0';
			} else {
				// $re_transfer=1;
				$re_transfer_cond = ' and a.re_transfer in(1,0)';
			}

			// Split, Mother barcode transfer after, child barcode new insert current transfer id.
			// when acknowledge found re_transfer=1, new barcode scan in update event re_transfer=0.
			// After acknowledge > re_transfer=0 in acknowledge page.
			$next_transfer_sql = sql_select("SELECT max(a.id) as max_id,  a.barcode_no from pro_roll_details a, PRO_GREY_PROD_ENTRY_DTLS b
			where a.DTLS_ID=b.id and a.barcode_no in ($all_barcodeNo) and a.status_active =1 and a.is_deleted=0 and entry_form in(2) $re_transfer_cond and b.trans_id>0 group by  a.barcode_no
			union all
			SELECT max(a.id) as max_id,  a.barcode_no from pro_roll_details a
			where  a.barcode_no in ($all_barcodeNo) and a.status_active =1 and a.is_deleted=0 and entry_form in(22,58,84,133) $re_transfer_cond group by  a.barcode_no");
			// echo "10**".$next_transfer_sql;die;
			foreach ($next_transfer_sql as $next_trans) {
				$next_transfer_arr[$next_trans[csf('barcode_no')]] = $next_trans[csf('max_id')];
			}

			$current_transfer_sql = sql_select("SELECT a.barcode_no, b.transfer_system_id as system_id, a.roll_split_from, a.qnty from pro_roll_details a, inv_item_transfer_mst b where a.mst_id=b.id and a.entry_form in (22,58,133) and a.barcode_no in ($all_barcodeNo) and a.status_active=1 and a.is_deleted=0 $re_transfer_cond
			union all
			select a.barcode_no, b.recv_number as system_id, a.roll_split_from, a.qnty from pro_roll_details a, inv_receive_master b where a.mst_id=b.id and a.entry_form in (84) and a.barcode_no in ($all_barcodeNo) and a.status_active=1 and a.is_deleted=0 $re_transfer_cond");

			foreach ($current_transfer_sql as $current_trans) {
				$next_transfer_ref[$current_trans[csf('barcode_no')]]["transfer_no"] = $current_trans[csf('system_id')];
				$current_barcode_split[$current_trans[csf('barcode_no')]] = $current_trans[csf('roll_split_from')];
				$current_barcode_qnty[$current_trans[csf('barcode_no')]] = $current_trans[csf('qnty')];
			}

			if (!empty($saved_roll_arr)) // Saved barcode to next transaction found
			{
				foreach ($saved_roll_arr as $barcode => $saved_roll_id) {
					if ($saved_roll_id != $next_transfer_arr[$barcode]) {
						if ($current_barcode_split[$barcode]) {
							echo "20**Sorry Split Found Update/Delete Not allowed, \nBarcode No :-  " . $barcode;
							disconnect($con);
							die;
						}
					}
					//echo $current_barcode_qnty[$barcode] .'!='. $barcode_wgt_arr[$barcode].'<br>';
					if ($current_barcode_qnty[$barcode] != $barcode_wgt_arr[$barcode]) {
						echo "20**Sorry Split Found Update/Delete Not allowed, \nBarcode No :  " . $barcode;
						disconnect($con);
						die;
					}
				}
			}

			$issue_data_refer = sql_select("SELECT a.id, a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 61 and a.barcode_no in ($all_barcodeNo) and a.status_active = 1 and a.is_deleted = 0 and a.is_returned=0");
			if ($issue_data_refer[0][csf("barcode_no")] != "") {
				echo "20**Sorry Barcode No : " . $issue_data_refer[0][csf("barcode_no")] . "\nFound in Issue No " . $issue_data_refer[0][csf("issue_number")];
				disconnect($con);
				die;
			}

			$current_transfer_sql = sql_select("SELECT a.barcode_no, b.transfer_system_id as system_id from pro_roll_details a, inv_item_transfer_mst b where a.mst_id=b.id and a.entry_form in (133) and a.barcode_no in ($all_barcodeNo) and a.status_active=1 and a.is_deleted=0  $re_transfer_cond
			union all
			select a.barcode_no, b.recv_number as system_id from pro_roll_details a, inv_receive_master b where a.mst_id=b.id and a.entry_form in (84) and a.barcode_no in ($all_barcodeNo) and a.status_active=1 and a.is_deleted=0  $re_transfer_cond");

			foreach ($current_transfer_sql as $current_trans) {
				$next_transfer_ref[$current_trans[csf('barcode_no')]]["transfer_no"] = $current_trans[csf('system_id')];
			}
			// echo '<pre>';print_r($saved_roll_arr);
			if (!empty($saved_roll_arr)) // Saved barcode to next transaction found
			{
				foreach ($saved_roll_arr as $barcode => $saved_roll_id) {
					// echo $barcode.'<br>';
					// echo $saved_roll_id.'='.$next_transfer_arr[$barcode].'<br>';
					if ($saved_roll_id != $next_transfer_arr[$barcode]) {
						echo "20**Sorry Barcode No : " . $barcode . " \nFound in Transfer/Return No : " . $next_transfer_ref[$barcode]["transfer_no"];
						disconnect($con);
						die;
					}
				}
			}
			// echo '<pre>';print_r($new_roll_arr);
			if (!empty($new_roll_arr)) // new barcode show in current transfer but this barcode saved to another tab
			{
				foreach ($new_roll_arr as $barcode => $new_roll_id) {
					// echo $new_roll_id .'!='. $next_transfer_arr[$barcode].'<br>';
					if ($new_roll_id != $next_transfer_arr[$barcode]) {
						echo "20**Sorry Barcode No : " . $barcode . " \nFound in Transfer/Return No : " . $next_transfer_ref[$barcode]["transfer_no"];
						disconnect($con);
						die;
					}
				}
			}
		}
		// echo "10**string";die;

		$new_inserted_zs = array_filter($new_inserted);
		if (!empty($new_inserted_zs)) {
			$check_if_already_scanned = sql_select("select a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and b.entry_form = 61 and  a.entry_form=61 and a.is_returned!=1 and a.barcode_no in (" . implode(",", array_filter($new_inserted)) . ") and a.status_active = 1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0");

			foreach ($check_if_already_scanned as $val) {
				if ($val[csf("barcode_no")]) {
					echo "20**Sorry! Barcode already Scanned. Challan No: " . $val[csf("issue_number")] . " Barcode No : " . $val[csf("barcode_no")];
					die;
				}
			}

			$trans_check_sql = sql_select("select barcode_no, entry_form, po_breakdown_id, qnty from pro_roll_details where barcode_no in (" . implode(",", array_filter($new_inserted)) . ") and entry_form in ( 58,84,133) and re_transfer =0 and status_active = 1 and is_deleted = 0");

			foreach ($trans_check_sql as $val) {
				if ($val[csf("po_breakdown_id")]  !=  str_replace("'", "", $txt_from_order_id)) {
					echo "20**Sorry! This barcode " . str_replace("'", "", $$barcodeNo) . " doesn't belong to this sales order " . $txt_from_order_no . "";
					die;
				}

				if ($val[csf("qnty")]  !=  $barcode_ref_arr[$val[csf("barcode_no")]]) {
					echo "20**Sorry! current quantity does not match with original qnty. Barcode no: " . $val[csf("barcode_no")] . "";
					die;
				}
			}
		}

		if ($txt_deleted_id != "") {
			//echo "10**5**jahid.$txt_deleted_id";die;
			$deletedIds = explode(",", $txt_deleted_id);
			$dtlsIDDel = '';
			$transIDDel = '';
			$rollIDDel = '';
			$rollIDactive = '';
			$delBarcodeNo = '';
			foreach ($deletedIds as $delIds) {
				$delIds = explode("_", $delIds);
				if ($dtlsIDDel == "") {
					$dtlsIDDel = $delIds[0];
					$transIDDel = $delIds[1] . "," . $delIds[2];
					$rollIDDel = $delIds[3];
					$rollIDactive = $delIds[4];
					$delBarcodeNo = $delIds[5];
				} else {
					$dtlsIDDel .= "," . $delIds[0];
					$transIDDel .= "," . $delIds[1] . "," . $delIds[2];
					$rollIDDel .= "," . $delIds[3];
					$rollIDactive .= "," . $delIds[4];
					$delBarcodeNo .= "," . $delIds[5];
				}
			}

			$txt_deleted_prod_qty = trim($txt_deleted_prod_qty, "'");
			$txt_deleted_prod_qty = explode(",", $txt_deleted_prod_qty);
			// echo '<pre>';print_r($txt_deleted_prod_qty);
			foreach ($txt_deleted_prod_qty as $val) {
				$qty_production = explode("_", $val);

				$up_del_prod_id_data[$qty_production[0]]['qnty'] += $qty_production[1];
				$up_del_prod_id_data[$qty_production[0]]['amount'] += $qty_production[3];
				$deleted_prod_id_arr[$qty_production[0]] = $qty_production[0];

				$up_del_from_prod_id_data[$qty_production[2]]['qnty'] += $qty_production[1];
				$up_del_from_prod_id_data[$qty_production[2]]['amount'] += $qty_production[3];
				$update_from_prod_id_arr[$qty_production[2]] = $qty_production[2];
			}

			$update_from_prod_id_arr = array_filter(array_unique($update_from_prod_id_arr));
			$deleted_prod_id_arr = array_filter(array_unique($deleted_prod_id_arr));

			if ($delBarcodeNo != "") {
				$check_sql = sql_select("SELECT a.barcode_no , b.issue_number as system_no, a.entry_form, 'Issue' as msg_source from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 61 and b.entry_form = 61 and a.is_returned != 1 and  a.status_active=1 and  b.status_active=1 and a.barcode_no in ($delBarcodeNo)
				union all
				select a.barcode_no , b.transfer_system_id as system_no, a.entry_form, 'Transfer' as msg_source from pro_roll_details a, inv_item_transfer_mst b where a.mst_id = b.id and a.entry_form = 133 and b.entry_form = 133 and a.re_transfer = 0 and  a.status_active=1 and  b.status_active=1 and a.barcode_no in ($delBarcodeNo) and a.id not in ($rollIDDel) ");

				$msg = "";
				foreach ($check_sql as $val) {
					$msg .= $val[csf("msg_source")] . " Found. Barcode :" . $val[csf("barcode_no")] . " chalan no: " . $val[csf("system_no")] . "\n";
				}

				if ($msg) {
					echo "20**" . $msg;
					disconnect($con);
					die;
				}


				$splited_roll_sql = sql_select("select barcode_no,split_from_id from pro_roll_split where status_active =1 and barcode_no in ($delBarcodeNo)");

				foreach ($splited_roll_sql as $bar) {
					$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]] = $bar[csf('barcode_no')];
				}

				$child_split_sql = sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 and barcode_no in ($delBarcodeNo) and entry_form = 133 order by barcode_no");
				foreach ($child_split_sql as $bar) {
					$child_split_arr[$bar[csf('barcode_no')]][$bar[csf('id')]] = $bar[csf('barcode_no')];
				}

				foreach ($deletedIds as $delIds) {
					$delIds = explode("_", $delIds);
					if ($splited_roll_ref[$delIds[5]][$delIds[3]] != "" || $child_split_arr[$delIds[5]][$delIds[3]] != "") {
						echo "20**" . "Split Found. barcode no: " . $delIds[5];
						disconnect($con);
						die;
					}
				}
			}

			$prev_rol_id_sql = sql_select("select from_roll_id from pro_roll_details where id in($rollIDDel) and status_active=1");
			$prev_rol_id = "";
			foreach ($prev_rol_id_sql as $row) {
				$prev_rol_id .= $row[csf("from_roll_id")] . ",";
			}
			$prev_rol_id = implode(",", array_unique(explode(",", chop($prev_rol_id, ","))));
			//echo "10**5##select from_roll_id from pro_roll_details where id in($rollIDDel)";die;
			$field_array_status = "updated_by*update_date*status_active*is_deleted";
			$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
			/*$statusChangeTrans=sql_multirow_update("inv_transaction",$field_array_status,$data_array_status,"id",$transIDDel,0);
			$statusChangeDtls=sql_multirow_update("inv_item_transfer_dtls",$field_array_status,$data_array_status,"id",$dtlsIDDel,0);
			$statusChangeRoll=sql_multirow_update("pro_roll_details",$field_array_status,$data_array_status,"id",$rollIDDel,0);
			$activeRoll=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","0*0*0","id",$rollIDactive,0);
			$active_prev_roll=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","0*0*0","id",$prev_rol_id,0);

			if($flag==1)
			{
				if($statusChangeTrans && $statusChangeDtls && $statusChangeRoll && $activeRoll && $active_prev_roll) $flag=1; else $flag=0;
			}*/
		}
		// echo '<pre>';print_r($update_from_prod_id_arr);
		// echo "10**fail";die;

		if (!empty($product_id_insert_parameter)) {
			foreach ($product_id_insert_parameter as $key => $val) {
				$prod_description_arr = explode("**", $key);
				$prod_id = $prod_description_arr[0];
				$fabric_desc_id = $prod_description_arr[1];
				$txt_gsm = $prod_description_arr[2];
				$txt_width = $prod_description_arr[3];
				$cons_compo = $prod_description_arr[4];

				$roll_amount = $product_id_insert_amount[$key];

				$avg_rate_per_unit = $roll_amount / $val;

				$prod_name_dtls = trim($cons_compo);

				if ($variable_auto_rcv == 2) // if Auto recv Yes 2 need to ack
				{
					$avg_rate_per_unit = 0;
					$val = 0;
					$roll_amount = 0;
				}
				// if Qty is zero then rate & value will be zero
				if ($val <= 0) {
					$roll_amount = 0;
					$avg_rate_per_unit = 0;
				}

				if ($data_array_prod_insert != "") $data_array_prod_insert .= ",";
				$data_array_prod_insert .= "(" . $prod_id . "," . $cbo_to_company_id . "," . $cbo_store_name . ",13," . $fabric_desc_id . "," . $cons_compo . "," . $prod_name_dtls . "," . "12" . "," . $avg_rate_per_unit . "," . $val . "," . $val . "," . $roll_amount . "," . $txt_gsm . ",'" . $txt_width . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			}
		}

		// =============
		$all_prod_id_arr = array_unique(explode(",", chop($all_prod_id, ',')));
		// echo "10**";print_r($update_to_prod_id);die;
		$all_up_del_prod_id = array_merge($update_to_prod_id, $deleted_prod_id_arr, $update_from_prod_id_arr, $all_prod_id_arr); // New Roll, Deleted Roll, Deleted From roll product id Mearged to update
		// echo "10**";print_r($all_prod_id_arr);die;
		if (!empty($all_up_del_prod_id)) {
			$prod_id_array = array();
			$all_up_del_prod_id = chop(implode(",", array_unique($all_up_del_prod_id)), ",");
			$toProdIssueResult = sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($all_up_del_prod_id) ");

			// echo "10**"."select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($all_up_del_prod_id) ";die;

			if ($variable_auto_rcv == 2) // need to ack
			{
				foreach ($toProdIssueResult as $row) {
					//New Roll (+) and Deleted roll (-) and Deleted from roll (+)
					// $product_id_update_parameter > new roll already found to product

					$new_added_from_prod_qnty = $prodData_array[$row[csf("id")]];
					$new_added_from_prod_amount = $prodData_array_amount[$row[csf("id")]];

					//$stock_qnty = $product_id_update_parameter[$row[csf("id")]]['qnty'] + $row[csf("current_stock")] - $up_del_prod_id_data[$row[csf("id")]]['qnty'] + $up_del_from_prod_id_data[$row[csf("id")]]['qnty'] - $new_added_from_prod_qnty;
					$stock_qnty = $row[csf("current_stock")] + $up_del_from_prod_id_data[$row[csf("id")]]['qnty'] - $new_added_from_prod_qnty;

					//$stock_value = $product_id_update_parameter[$row[csf("id")]]['amount'] + $row[csf("stock_value")] - $up_del_prod_id_data[$row[csf("id")]]['amount'] + $up_del_from_prod_id_data[$row[csf("id")]]['amount'] - $new_added_from_prod_amount;
					$stock_value = $row[csf("stock_value")] + $up_del_from_prod_id_data[$row[csf("id")]]['amount'] - $new_added_from_prod_amount;

					//$avg_rate_per_unit = $stock_value/$stock_qnty;
					if ($stock_qnty > 0) {
						$avg_rate_per_unit = $stock_value / $stock_qnty;
					} else {
						$avg_rate_per_unit = 0;
					}
					// if Qty is zero then rate & value will be zero
					if ($stock_qnty <= 0) {
						$stock_value = 0;
						$avg_rate_per_unit = 0;
					}
					$prod_id_array[] = $row[csf('id')];
					$data_array_prod_update[$row[csf('id')]] = explode("*", ("'" . $stock_qnty . "'*'" . $avg_rate_per_unit . "'*'" . $stock_value . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
				}
				unset($toProdIssueResult);
			} else {
				foreach ($toProdIssueResult as $row) {
					//New Roll (+) and Deleted roll (-) and Deleted from roll (+)

					$new_added_from_prod_qnty = $prodData_array[$row[csf("id")]];
					$new_added_from_prod_amount = $prodData_array_amount[$row[csf("id")]];
					// echo $product_id_update_parameter[$row[csf("id")]]['qnty'] .'+'. $row[csf("current_stock")] .'-'. $up_del_prod_id_data[$row[csf("id")]]['qnty'] .'+'. $up_del_from_prod_id_data[$row[csf("id")]]['qnty'] .'-'. $new_added_from_prod_qnty.'<br>'.$row[csf("id")].'<br>';


					$stock_qnty = $product_id_update_parameter[$row[csf("id")]]['qnty'] + $row[csf("current_stock")] - $up_del_prod_id_data[$row[csf("id")]]['qnty'] + $up_del_from_prod_id_data[$row[csf("id")]]['qnty'] - $new_added_from_prod_qnty;

					$stock_value = $product_id_update_parameter[$row[csf("id")]]['amount'] + $row[csf("stock_value")] - $up_del_prod_id_data[$row[csf("id")]]['amount'] + $up_del_from_prod_id_data[$row[csf("id")]]['amount'] - $new_added_from_prod_amount;

					$avg_rate_per_unit = $stock_value / $stock_qnty;
					// if Qty is zero then rate & value will be zero
					if ($stock_qnty <= 0) {
						$stock_value = 0;
						$avg_rate_per_unit = 0;
					}
					$prod_id_array[] = $row[csf('id')];
					$data_array_prod_update[$row[csf('id')]] = explode("*", ("'" . $stock_qnty . "'*'" . $avg_rate_per_unit . "'*'" . $stock_value . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
				}
				unset($toProdIssueResult);
			}
		}
		// ===========


		if ($txt_deleted_id != "") {
			$statusChangeTrans = sql_multirow_update("inv_transaction", $field_array_status, $data_array_status, "id", $transIDDel, 0);
			$statusChangeDtls = sql_multirow_update("inv_item_transfer_dtls", $field_array_status, $data_array_status, "id", $dtlsIDDel, 0);
			$statusChangeRoll = sql_multirow_update("pro_roll_details", $field_array_status, $data_array_status, "id", $rollIDDel, 0);
			$activeRoll = sql_multirow_update("pro_roll_details", "is_transfer*transfer_criteria*re_transfer", "0*0*0", "id", $rollIDactive, 0);
			$active_prev_roll = sql_multirow_update("pro_roll_details", "is_transfer*transfer_criteria*re_transfer", "0*0*0", "id", $prev_rol_id, 0);

			if ($flag == 1) {
				if ($statusChangeTrans && $statusChangeDtls && $statusChangeRoll && $activeRoll && $active_prev_roll) $flag = 1;
				else $flag = 0;
			}
		}

		if ($dtlsIDDel == "") {
			$update_dtls_id = chop($update_dtls_id, ',');
		} else {
			$update_dtls_id = $update_dtls_id . $dtlsIDDel;
		}

		if ($update_dtls_id != "") {
			$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id in(" . $update_dtls_id . ") and entry_form=133");
			if ($flag == 1) {
				if ($query) $flag = 1;
				else $flag = 0;
			}
		}

		$rID = sql_update("inv_item_transfer_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
		if ($rID) $flag = 1;
		else $flag = 0;

		if (count($data_array_update_roll) > 0) {
			$rID2 = execute_query(bulk_update_sql_statement("inv_transaction", "id", $field_array_trans_update, $data_array_update_trans, $transId_arr));
			if ($flag == 1) {
				if ($rID2) $flag = 1;
				else $flag = 0;
			}
			// echo "**10".bulk_update_sql_statement("inv_item_transfer_dtls","id",$field_array_dtls_update,$data_array_update_dtls,$dtlsId_arr);die;
			$rID3 = execute_query(bulk_update_sql_statement("inv_item_transfer_dtls", "id", $field_array_dtls_update, $data_array_update_dtls, $dtlsId_arr));
			if ($flag == 1) {
				if ($rID3) $flag = 1;
				else $flag = 0;
			}

			$rID4 = execute_query(bulk_update_sql_statement("pro_roll_details", "id", $field_array_updateroll, $data_array_update_roll, $rollId_arr));
			if ($flag == 1) {
				if ($rID4) $flag = 1;
				else $flag = 0;
			}
		}

		if (str_replace("'", "", $cbo_transfer_criteria) == 1) // insert/update product info for company to company
		{
			if ($data_array_prod_insert != "") {
				// echo "10**insert into product_details_master (".$field_array_prod_insert.") values ".$data_array_prod_insert;die;
				$rID9 = sql_insert("product_details_master", $field_array_prod_insert, $data_array_prod_insert, 0);
				if ($rID9) $flag = 1;
				else $flag = 0;
			}

			if (!empty($data_array_prod_update)) {
				// echo bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );die;
				$prodUpdate = execute_query(bulk_update_sql_statement("product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array));

				if ($flag == 1) {
					if ($prodUpdate)
						$flag = 1;
					else
						$flag = 0;
				}
			}
		}

		if ($data_array_dtls != "") {
			// echo "insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
			$rIDinv = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 0);
			if ($flag == 1) {
				if ($rIDinv) $flag = 1;
				else $flag = 0;
			}
			// echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
			$rIDDtls = sql_insert("inv_item_transfer_dtls", $field_array_dtls, $data_array_dtls, 0);
			if ($flag == 1) {
				if ($rIDDtls) $flag = 1;
				else $flag = 0;
			}

			//echo $flag;die;
			//echo "0**";
			//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
			$rIDRoll = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 1);
			if ($flag == 1) {
				if ($rIDRoll) $flag = 1;
				else $flag = 0;
			}
		}

		$rollIds = chop($rollIds, ',');
		if ($rollIds != "") {
			$rID5 = sql_multirow_update("pro_roll_details", "is_transfer*transfer_criteria*re_transfer", "6*$cbo_transfer_criteria*1", "id", $rollIds, 0);
			if ($flag == 1) {
				if ($rID5) $flag = 1;
				else $flag = 0;
			}
		}



		// echo "10**5**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		if ($data_array_prop != "") {
			$rIDProp = sql_insert("order_wise_pro_details", $field_array_proportionate, $data_array_prop, 1);
			if ($flag == 1) {
				if ($rIDProp) $flag = 1;
				else $flag = 0;
			}
		}

		if (str_replace("'", "", $txt_requisition_id) != "") {
			if (str_replace("'", "", $requisition_and_order_basis) != 1) // 1 (Yes) for urmi
			{
				$cbo_complete_status = 2;
			}
			$requi_field_array_update = "requisition_status*updated_by*update_date";
			$requi_data_array_update = $cbo_complete_status . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
			$rID7 = sql_update("inv_item_transfer_requ_mst", $requi_field_array_update, $requi_data_array_update, "id", $txt_requisition_id, 1);
			if ($rID7) $flag = 1;
			else $flag = 0;
		}

		if (!empty($new_inserted)) {
			$rID8 = execute_query("update pro_roll_details set is_returned=1 where barcode_no in (" . implode(',', $new_inserted) . ") and id not in (" . implode(',', $inserted_roll_id_arr) . ")");
			if ($flag == 1) {
				if ($rID8)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		// echo "10**5**$flag";die;
		// echo "10**5**$flag**$rID2**$rID3**$rID4**$rID9**$prodUpdate**$rIDinv**$rIDDtls**$rIDRoll**$rID5**$rIDProp**$rID7**$rID8";die;

		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $update_id) . "**" . str_replace("'", "", $txt_system_id) . "**0";
			} else {
				mysql_query("ROLLBACK");
				echo "6**0**" . "&nbsp;" . "**1";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "1**" . str_replace("'", "", $update_id) . "**" . str_replace("'", "", $txt_system_id) . "**0";
			} else {
				oci_rollback($con);
				echo "6**0**" . "&nbsp;" . "**1";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "grey_fabric_order_to_order_transfer_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);



	$sql = "select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category, to_color_id from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;

	$dataArray = sql_select($sql);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13", "id", "product_name_details");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

	$po_buyer_arr = return_library_array("select id, buyer_id from wo_booking_mst", 'id', 'buyer_id');
	$po_comp_arr = return_library_array("select id, company_id from wo_booking_mst", 'id', 'company_id');

	$poDataArray = sql_select("SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.sales_booking_no, a.buyer_id, a.booking_id, sum(b.grey_qty) as qty
	from fabric_sales_order_mst a, fabric_sales_order_dtls b
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.sales_booking_no, a.buyer_id, a.booking_id");
	$job_array = array(); //$all_job_id='';
	foreach ($poDataArray as $row) {
		$job_array[$row[csf('id')]]['no'] = $row[csf('job_no')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_id')];
		$job_array[$row[csf('id')]]['style'] = $row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['qty'] = $row[csf('qty')];
		$job_array[$row[csf('id')]]['company'] = $row[csf('company_id')];
		$job_array[$row[csf('id')]]['booking'] = $row[csf('sales_booking_no')];
		$job_array[$row[csf('id')]]['booking_id'] = $row[csf('booking_id')];
	}
	unset($poDataArray);

	$from_booking = $job_array[$dataArray[0][csf('from_order_id')]]['booking'];
	$to_booking = $job_array[$dataArray[0][csf('to_order_id')]]['booking'];
	if ($from_booking != "" || $to_booking != "") {
		$po_sql = "SELECT a.id, a.po_number, a.grouping, b.booking_no from wo_po_break_down a, wo_booking_dtls b
		where a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.booking_no in('$from_booking','$to_booking') and b.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number, a.grouping, b.booking_no order by a.grouping";
		// echo $po_sql;die;
		$po_sql_result = sql_select($po_sql);
		$refBooking_cond = "";
		foreach ($po_sql_result as $key => $row) {
			$int_ref_arr[$row[csf('booking_no')]] .= $row[csf('grouping')] . ',';
		}
		// echo "<pre>";print_r($int_ref_arr);die;

		/*$po_sql = sql_select("select po_break_down_id from wo_booking_dtls where booking_no = '".$from_booking."'");
		foreach ($po_sql as $val)
		{
			$po_arr[$val[csf("po_break_down_id")]] =$val[csf("po_break_down_id")];
		}
		$po_arr = array_filter($po_arr);
		$po_ids = implode(",", $po_arr);

		$po_sql="select id, po_number, grouping from wo_po_break_down where status_active=1 and is_deleted=0 and id in($po_ids)";
		$result_po_sql = sql_select($po_sql);
		foreach ($result_po_sql as $key => $row)
		{
			$po_int_ref_arr[$row[csf('grouping')]]=$row[csf('grouping')];
		}
		$po_int_ref_arr = array_filter($po_int_ref_arr);
		$from_int_ref = implode(",", $po_int_ref_arr);
		unset($po_sql);*/
	}

	$sql_dtls = "SELECT a.from_prod_id, a.transfer_qnty, a.uom, a.y_count, a.brand_id, a.yarn_lot, a.to_rack, a.to_shelf, a.stitch_length, b.barcode_no, b.roll_no, b.booking_no, a.color_names
	from inv_item_transfer_dtls a, pro_roll_details b
	where a.id=b.dtls_id and b.entry_form=133 and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.barcode_no";

	$sql_result = sql_select($sql_dtls);
	$from_color_arr = array();
	foreach ($sql_result as $key => $row) {
		$from_color_arr[$row[csf('color_names')]] .= $row[csf('color_names')] . ',';
	}
	$from_color = '';
	$color_id_arr = array_unique(explode(",", implode(",", $from_color_arr)));
	foreach ($color_id_arr as $val) {
		if ($val > 0) $from_color .= $color_library[$val] . ",";
	}
	$from_color = chop($from_color, ',');

?>
	<div style="width:930px;">
		<table width="900" cellspacing="0" align="right">
			<tr>
				<td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<?
					$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result) {
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')] ?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')]; ?>
						City No: <? echo $result[csf('city')]; ?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Email Address: <? echo $result[csf('email')]; ?>
						Website No: <? echo $result[csf('website')];
								}
									?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
			</tr>
			<tr>
				<td width="125"><strong>Transfer ID :</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
				<td width="125"><strong>Transfer Date:</strong></td>
				<td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
				<td width="125"><strong>Challan No.:</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
			</tr>
		</table>
		<table width="900" cellspacing="0" align="right" style="margin-top:5px;">
			<tr>
				<td width="450">
					<table width="100%" cellspacing="0" align="right" style="font-size:12px">
						<tr>
							<td colspan="4" align="center" style="font-weight:bold; font-size:14px;"><u>From Order</u></td>
						</tr>
						<tr>
							<td width="100">Sales Order No:</td>
							<td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['no']; ?></td>
							<td width="100">Quantity:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['qty']; ?></td>
						</tr>
						<tr>
							<td>Po Buyer:</td>
							<td>&nbsp;<? echo $buyer_arr[$po_buyer_arr[$job_array[$dataArray[0][csf('from_order_id')]]['booking_id']]]; ?></td>
							<td>Po Company:</td>
							<td>&nbsp;<? echo $company_library[$po_comp_arr[$job_array[$dataArray[0][csf('from_order_id')]]['booking_id']]]; ?></td>
						</tr>
						<tr>
							<td>Style Ref:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['style']; ?></td>
							<td>Booking No:</td>
							<td>&nbsp;<? echo $from_booking; ?></td>
						</tr>
						<tr>
							<td>Int. Ref. No:</td>
							<td>&nbsp;<? echo chop($int_ref_arr[$from_booking], ","); ?></td>
							<td>Color:</td>
							<td>&nbsp;<? echo $from_color; ?></td>
						</tr>
					</table>
				</td>
				<td>
					<table width="100%" cellspacing="0" align="right" style="font-size:12px">
						<tr>
							<td colspan="4" align="center" style="font-weight:bold; font-size:14px;"><u>To Order</u></td>
						</tr>
						<tr>
							<td width="100">Sales Order No:</td>
							<td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['no']; ?></td>
							<td width="100">Quantity:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['qty']; ?></td>
						</tr>
						<tr>
							<td>Po Buyer:</td>
							<td>&nbsp;<? echo $buyer_arr[$po_buyer_arr[$job_array[$dataArray[0][csf('to_order_id')]]['booking_id']]]; ?></td>
							<td>Po Company:</td>
							<td>&nbsp;<? echo $company_library[$po_comp_arr[$job_array[$dataArray[0][csf('to_order_id')]]['booking_id']]]; ?></td>
						</tr>
						<tr>
							<td>Style Ref:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['style']; ?></td>
							<td>Booking No:</td>
							<td>&nbsp;<? echo $to_booking; ?></td>
						</tr>
						<tr>
							<td>Int. Ref. No:</td>
							<td>&nbsp;<? echo chop($int_ref_arr[$to_booking], ","); ?></td>
							<td>Color:</td>
							<td>&nbsp;<? echo $color_library[$dataArray[0][csf('to_color_id')]]; ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="900" border="1" rules="all" class="rpt_table" style="font-size:13px">
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="80">Barcode No</th>
					<th width="60">Roll No</th>
					<th width="60">Prog. No</th>
					<th width="160">Fabric Description</th>
					<th width="80">Y/Count</th>
					<th width="70">Y/Brand</th>
					<th width="80">Y/Lot</th>
					<th width="50">Rack</th>
					<th width="50">Shelf</th>
					<th width="70">Stitch Length</th>
					<th width="50">UOM</th>
					<th>Transfered Qty</th>
				</thead>
				<tbody>
					<?

					$i = 1;
					foreach ($sql_result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$transfer_qnty = $row[csf('transfer_qnty')];
						$transfer_qnty_sum += $transfer_qnty;

						$ycount = '';
						$count_id = explode(',', $row[csf('y_count')]);
						foreach ($count_id as $count) {
							if ($ycount == '') $ycount = $count_arr[$count];
							else $ycount .= "," . $count_arr[$count];
						}
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td><? echo $row[csf("barcode_no")]; ?></td>
							<td><? echo $row[csf("roll_no")]; ?></td>
							<td><? echo $row[csf("booking_no")]; ?></td>
							<td><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
							<td><? echo $ycount; ?></td>
							<td><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
							<td><? echo $row[csf("yarn_lot")]; ?></td>
							<td><? echo $row[csf("to_rack")]; ?></td>
							<td><? echo $row[csf("to_shelf")]; ?></td>
							<td><? echo $row[csf("stitch_length")]; ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
							<td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
						</tr>
					<?
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="12" align="right"><strong>Total </strong></td>
						<td align="right"><?php echo $transfer_qnty_sum; ?></td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(111, $data[0], "900px");
			?>
		</div>
	</div>
<?
	exit();
}

if ($action == "grey_fabric_order_to_order_transfer_print_2") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$path = $data[3];
	//print_r ($data);



	$sql = "select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category, to_color_id, transfer_criteria from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;

	$dataArray = sql_select($sql);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13", "id", "product_name_details");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

	$po_buyer_arr = return_library_array("select id, buyer_id from wo_booking_mst", 'id', 'buyer_id');
	$po_comp_arr = return_library_array("select id, company_id from wo_booking_mst", 'id', 'company_id');

	$poDataArray = sql_select("SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.sales_booking_no, a.buyer_id, a.booking_id, sum(b.grey_qty) as qty
	from fabric_sales_order_mst a, fabric_sales_order_dtls b
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.sales_booking_no, a.buyer_id, a.booking_id");
	$job_array = array(); //$all_job_id='';
	foreach ($poDataArray as $row) {
		$job_array[$row[csf('id')]]['no'] = $row[csf('job_no')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_id')];
		$job_array[$row[csf('id')]]['style'] = $row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['qty'] = $row[csf('qty')];
		$job_array[$row[csf('id')]]['company'] = $row[csf('company_id')];
		$job_array[$row[csf('id')]]['booking'] = $row[csf('sales_booking_no')];
		$job_array[$row[csf('id')]]['booking_id'] = $row[csf('booking_id')];
	}
	unset($poDataArray);

	$from_booking = $job_array[$dataArray[0][csf('from_order_id')]]['booking'];
	$to_booking = $job_array[$dataArray[0][csf('to_order_id')]]['booking'];
	if ($from_booking != "" || $to_booking != "") {
		$po_sql = "SELECT a.id, a.po_number, a.grouping, b.booking_no from wo_po_break_down a, wo_booking_dtls b
		where a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.booking_no in('$from_booking','$to_booking') and b.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number, a.grouping, b.booking_no order by a.grouping";
		// echo $po_sql;die;
		$po_sql_result = sql_select($po_sql);
		$refBooking_cond = "";
		foreach ($po_sql_result as $key => $row) {
			$int_ref_arr[$row[csf('booking_no')]] .= $row[csf('grouping')] . ',';
		}
		// echo "<pre>";print_r($int_ref_arr);die;

		/*$po_sql = sql_select("select po_break_down_id from wo_booking_dtls where booking_no = '".$from_booking."'");
		foreach ($po_sql as $val)
		{
			$po_arr[$val[csf("po_break_down_id")]] =$val[csf("po_break_down_id")];
		}
		$po_arr = array_filter($po_arr);
		$po_ids = implode(",", $po_arr);

		$po_sql="select id, po_number, grouping from wo_po_break_down where status_active=1 and is_deleted=0 and id in($po_ids)";
		$result_po_sql = sql_select($po_sql);
		foreach ($result_po_sql as $key => $row)
		{
			$po_int_ref_arr[$row[csf('grouping')]]=$row[csf('grouping')];
		}
		$po_int_ref_arr = array_filter($po_int_ref_arr);
		$from_int_ref = implode(",", $po_int_ref_arr);
		unset($po_sql);*/
	}

	$sql_dtls = "SELECT a.from_prod_id, sum(a.transfer_qnty) as transfer_qnty, a.uom, a.y_count, a.brand_id, a.yarn_lot, a.to_rack, a.to_shelf, a.stitch_length, b.booking_no, a.color_names,count(b.roll_id) as no_of_roll
	from inv_item_transfer_dtls a, pro_roll_details b
	where a.id=b.dtls_id and b.entry_form=133 and a.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.from_prod_id,a.uom,a.y_count,a.brand_id,a.yarn_lot,a.to_rack,a.to_shelf,a.stitch_length,b.booking_no,a.color_names";
	//echo $sql_dtls;
	$sql_result = sql_select($sql_dtls);
	$from_color_arr = array();
	$bookingNoChk = array();
	$bookingNoArr = array();
	foreach ($sql_result as $key => $row) {
		$from_color_arr[$row[csf('color_names')]] .= $row[csf('color_names')] . ',';

		if ($bookingNoChk[$row[csf('booking_no')]] == "") {
			$bookingNoChk[$row[csf('booking_no')]] = $row[csf('booking_no')];
			array_push($bookingNoArr, $row[csf('booking_no')]);
		}
	}
	$from_color = '';
	$color_id_arr = array_unique(explode(",", implode(",", $from_color_arr)));
	foreach ($color_id_arr as $val) {
		if ($val > 0) $from_color .= $color_library[$val] . ",";
	}
	$from_color = chop($from_color, ',');

	$prog_arr = "SELECT a.id as program_no,a.color_range,a.machine_dia,a.machine_gg
	FROM ppl_planning_info_entry_dtls a, PPL_PLANNING_ENTRY_PLAN_DTLS b
	WHERE a.mst_id = b.mst_id AND a.id = b.dtls_id AND a.status_active = 1 AND a.is_deleted = 0 " . where_con_using_array($bookingNoArr, 0, 'a.id') . " ";
	//echo $prog_arr;
	$prog_arr_result = sql_select($prog_arr);
	$progInfoArr = array();
	foreach ($prog_arr_result as $row) {
		$progInfoArr[$row[csf('program_no')]]['color_range'] = $row[csf('color_range')];
		$progInfoArr[$row[csf('program_no')]]['machine_gg'] = $row[csf('machine_gg')];
		$progInfoArr[$row[csf('program_no')]]['machine_dia'] = $row[csf('machine_dia')];
	}

	?>
		<div style="width:1050px;">
			<table width="1020" cellspacing="0" align="right">
				<tr>
					<td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="6" align="center" style="font-size:14px">
						<!-- <?
								$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
								foreach ($nameArray as $result) {
								?>
							Plot No: <? echo $result[csf('plot_no')]; ?>
							Level No: <? echo $result[csf('level_no')] ?>
							Road No: <? echo $result[csf('road_no')]; ?>
							Block No: <? echo $result[csf('block_no')]; ?>
							City No: <? echo $result[csf('city')]; ?>
							Zip Code: <? echo $result[csf('zip_code')]; ?>
							Email Address: <? echo $result[csf('email')]; ?>
							Website No: <? echo $result[csf('website')];
									}
										?> -->
					</td>
				</tr>
				<tr>
					<td colspan="4" align="right" style="font-size:18px"><strong><u>Roll Wise Grey <? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?> Transfer Report</u></strong></td>
					<td colspan="5" id="barcode_img_id" align="right"></td>

				</tr>
				<tr>
					<td width="125"><strong>Transfer ID :</strong></td>
					<td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
					<td width="125"><strong>Transfer Date:</strong></td>
					<td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
					<td width="125"><strong>Challan No.:</strong></td>
					<td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
				</tr>

			</table>
			<table width="1020" cellspacing="0" align="right" style="margin-top:5px;">
				<tr>
					<td width="450">
						<table width="100%" cellspacing="0" align="right" style="font-size:12px">
							<tr>
								<td colspan="4" align="center" style="font-weight:bold; font-size:14px;"><u>From Order</u></td>
							</tr>
							<tr>
								<td width="100">Sales Order No:</td>
								<td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['no']; ?></td>
								<td width="100">Quantity:</td>
								<td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['qty']; ?></td>
							</tr>
							<tr>
								<td>Po Buyer:</td>
								<td>&nbsp;<? echo $buyer_arr[$po_buyer_arr[$job_array[$dataArray[0][csf('from_order_id')]]['booking_id']]]; ?></td>
								<td>Po Company:</td>
								<td>&nbsp;<? echo $company_library[$po_comp_arr[$job_array[$dataArray[0][csf('from_order_id')]]['booking_id']]]; ?></td>
							</tr>
							<tr>
								<td>Style Ref:</td>
								<td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['style']; ?></td>
								<td>Booking No:</td>
								<td>&nbsp;<? echo $from_booking; ?></td>
							</tr>
							<tr>
								<td>Int. Ref. No:</td>
								<td>&nbsp;<? echo chop($int_ref_arr[$from_booking], ","); ?></td>
								<td>Color:</td>
								<td>&nbsp;<? echo $from_color; ?></td>
							</tr>
						</table>
					</td>
					<td>
						<table width="100%" cellspacing="0" align="right" style="font-size:12px">
							<tr>
								<td colspan="4" align="center" style="font-weight:bold; font-size:14px;"><u>To Order</u></td>
							</tr>
							<tr>
								<td width="100">Sales Order No:</td>
								<td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['no']; ?></td>
								<td width="100">Quantity:</td>
								<td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['qty']; ?></td>
							</tr>
							<tr>
								<td>Po Buyer:</td>
								<td>&nbsp;<? echo $buyer_arr[$po_buyer_arr[$job_array[$dataArray[0][csf('to_order_id')]]['booking_id']]]; ?></td>
								<td>Po Company:</td>
								<td>&nbsp;<? echo $company_library[$po_comp_arr[$job_array[$dataArray[0][csf('to_order_id')]]['booking_id']]]; ?></td>
							</tr>
							<tr>
								<td>Style Ref:</td>
								<td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['style']; ?></td>
								<td>Booking No:</td>
								<td>&nbsp;<? echo $to_booking; ?></td>
							</tr>
							<tr>
								<td>Int. Ref. No:</td>
								<td>&nbsp;<? echo chop($int_ref_arr[$to_booking], ","); ?></td>
								<td>Color:</td>
								<td>&nbsp;<? echo $color_library[$dataArray[0][csf('to_color_id')]]; ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<br>
			<div style="width:100%;">
				<table align="right" cellspacing="0" width="1020" border="1" rules="all" class="rpt_table" style="font-size:13px">
					<thead bgcolor="#dddddd" align="center">
						<th width="30">SL</th>
						<th width="60">Prog. No</th>
						<th width="160">Fabric Description</th>
						<th width="100">Color Range</th>
						<th width="70">Stich Length</th>
						<th width="100">Machine<br>DiaxGG</th>
						<th width="70">Y/Brand</th>
						<th width="80">Y/Count</th>
						<th width="80">Y/Lot</th>
						<th width="50">Rack</th>
						<th width="50">Shelf</th>
						<th width="60">No Of Roll</th>
						<th width="50">UOM</th>
						<th>Transfered Qty</th>
					</thead>
					<tbody>
						<?

						$i = 1;
						foreach ($sql_result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$transfer_qnty = $row[csf('transfer_qnty')];
							$transfer_qnty_sum += $transfer_qnty;

							$ycount = '';
							$count_id = explode(',', $row[csf('y_count')]);
							foreach ($count_id as $count) {
								if ($ycount == '') $ycount = $count_arr[$count];
								else $ycount .= "," . $count_arr[$count];
							}
						?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $i; ?></td>
								<td align="center"><? echo $row[csf("booking_no")]; ?></td>
								<td align="center"><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
								<td align="center"><? echo $color_range[$progInfoArr[$row[csf('booking_no')]]['color_range']]; ?></td>
								<td align="center"><? echo $row[csf("stitch_length")]; ?></td>
								<td align="center">
									<?
									echo $progInfoArr[$row[csf('booking_no')]]['machine_dia'] . 'x' . $progInfoArr[$row[csf('booking_no')]]['machine_gg'];
									?>
								</td>
								<td align="center"><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
								<td align="center"><? echo $ycount; ?></td>
								<td align="center"><? echo $row[csf("yarn_lot")]; ?></td>
								<td align="center"><? echo $row[csf("to_rack")]; ?></td>
								<td align="center"><? echo $row[csf("to_shelf")]; ?></td>
								<td align="center"><? echo $row[csf("no_of_roll")]; ?></td>
								<td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
								<td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
							</tr>
						<?
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="13" align="right"><strong>Total </strong></td>
							<td align="right"><?php echo $transfer_qnty_sum; ?></td>
						</tr>
					</tfoot>
				</table>
				<br>
				<?
				echo signature_table(111, $data[0], "900px");
				?>

			</div>
		</div>
		<?if($path){?>
			<script type="text/javascript" src="<?=$path?>../../js/jquery.js"></script>
			<script type="text/javascript" src="<?=$path?>../../js/jquerybarcode.js"></script>
		<?}else{?>
			<script type="text/javascript" src="../../js/jquery.js"></script>
			<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<?}?>
		<script>
			function generateBarcode(valuess) {
				var value = valuess; //$("#barcodeValue").val();
				//alert(value)
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

				$("#barcode_img_id").show().barcode(value, btype, settings);
			}
			generateBarcode("<? echo $dataArray[0][csf('transfer_system_id')]; ?>");
		</script>

	<?
		exit();
}

if ($action == "grey_fabric_order_to_order_transfer_print_3") {
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);
	$path = $data[3];



	$sql = "select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category, to_color_id, transfer_criteria from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;

	$dataArray = sql_select($sql);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13", "id", "product_name_details");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

	$po_buyer_arr = return_library_array("select id, buyer_id from wo_booking_mst", 'id', 'buyer_id');
	$po_comp_arr = return_library_array("select id, company_id from wo_booking_mst", 'id', 'company_id');

	$poDataArray = sql_select("SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.sales_booking_no, a.buyer_id, a.booking_id, sum(b.grey_qty) as qty
	from fabric_sales_order_mst a, fabric_sales_order_dtls b
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.sales_booking_no, a.buyer_id, a.booking_id");
	$job_array = array(); //$all_job_id='';
	foreach ($poDataArray as $row) {
		$job_array[$row[csf('id')]]['no'] = $row[csf('job_no')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_id')];
		$job_array[$row[csf('id')]]['style'] = $row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['qty'] = $row[csf('qty')];
		$job_array[$row[csf('id')]]['company'] = $row[csf('company_id')];
		$job_array[$row[csf('id')]]['booking'] = $row[csf('sales_booking_no')];
		$job_array[$row[csf('id')]]['booking_id'] = $row[csf('booking_id')];
	}
	unset($poDataArray);

	$from_booking = $job_array[$dataArray[0][csf('from_order_id')]]['booking'];
	$to_booking = $job_array[$dataArray[0][csf('to_order_id')]]['booking'];
	if ($from_booking != "" || $to_booking != "") {
		$po_sql = "SELECT a.id, a.po_number, a.grouping, b.booking_no from wo_po_break_down a, wo_booking_dtls b
		where a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.booking_no in('$from_booking','$to_booking') and b.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number, a.grouping, b.booking_no order by a.grouping";
		// echo $po_sql;die;
		$po_sql_result = sql_select($po_sql);
		$refBooking_cond = "";
		foreach ($po_sql_result as $key => $row) {
			$int_ref_arr[$row[csf('booking_no')]] .= $row[csf('grouping')] . ',';
		}
		// echo "<pre>";print_r($int_ref_arr);die;

		/*$po_sql = sql_select("select po_break_down_id from wo_booking_dtls where booking_no = '".$from_booking."'");
		foreach ($po_sql as $val)
		{
			$po_arr[$val[csf("po_break_down_id")]] =$val[csf("po_break_down_id")];
		}
		$po_arr = array_filter($po_arr);
		$po_ids = implode(",", $po_arr);

		$po_sql="select id, po_number, grouping from wo_po_break_down where status_active=1 and is_deleted=0 and id in($po_ids)";
		$result_po_sql = sql_select($po_sql);
		foreach ($result_po_sql as $key => $row)
		{
			$po_int_ref_arr[$row[csf('grouping')]]=$row[csf('grouping')];
		}
		$po_int_ref_arr = array_filter($po_int_ref_arr);
		$from_int_ref = implode(",", $po_int_ref_arr);
		unset($po_sql);*/
	}

	$sql_dtls = "SELECT a.from_prod_id, sum(a.transfer_qnty) as transfer_qnty, a.uom, a.y_count, a.brand_id, a.yarn_lot, a.to_rack, a.to_shelf, a.stitch_length, b.booking_no, a.color_names,count(b.roll_id) as no_of_roll
	from inv_item_transfer_dtls a, pro_roll_details b
	where a.id=b.dtls_id and b.entry_form=133 and a.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.from_prod_id,a.uom,a.y_count,a.brand_id,a.yarn_lot,a.to_rack,a.to_shelf,a.stitch_length,b.booking_no,a.color_names";
	//echo $sql_dtls;
	$sql_result = sql_select($sql_dtls);
	$from_color_arr = array();
	$bookingNoChk = array();
	$bookingNoArr = array();
	foreach ($sql_result as $key => $row) {
		$from_color_arr[$row[csf('color_names')]] .= $row[csf('color_names')] . ',';

		if ($bookingNoChk[$row[csf('booking_no')]] == "") {
			$bookingNoChk[$row[csf('booking_no')]] = $row[csf('booking_no')];
			array_push($bookingNoArr, $row[csf('booking_no')]);
		}
	}
	$from_color = '';
	$color_id_arr = array_unique(explode(",", implode(",", $from_color_arr)));
	foreach ($color_id_arr as $val) {
		if ($val > 0) $from_color .= $color_library[$val] . ",";
	}
	$from_color = chop($from_color, ',');

	$prog_arr = "SELECT a.id as program_no,a.color_range,a.machine_dia,a.machine_gg
	FROM ppl_planning_info_entry_dtls a, PPL_PLANNING_ENTRY_PLAN_DTLS b
	WHERE a.mst_id = b.mst_id AND a.id = b.dtls_id AND a.status_active = 1 AND a.is_deleted = 0 " . where_con_using_array($bookingNoArr, 0, 'a.id') . " ";
	//echo $prog_arr;
	$prog_arr_result = sql_select($prog_arr);
	$progInfoArr = array();
	foreach ($prog_arr_result as $row) {
		$progInfoArr[$row[csf('program_no')]]['color_range'] = $row[csf('color_range')];
		$progInfoArr[$row[csf('program_no')]]['machine_gg'] = $row[csf('machine_gg')];
		$progInfoArr[$row[csf('program_no')]]['machine_dia'] = $row[csf('machine_dia')];
	}

	?>
		<div style="width:1050px;">
			<table width="1020" cellspacing="0" align="right">
				<tr>
					<td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="6" align="center" style="font-size:14px">
						<!-- <?
								$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
								foreach ($nameArray as $result) {
								?>
							Plot No: <? echo $result[csf('plot_no')]; ?>
							Level No: <? echo $result[csf('level_no')] ?>
							Road No: <? echo $result[csf('road_no')]; ?>
							Block No: <? echo $result[csf('block_no')]; ?>
							City No: <? echo $result[csf('city')]; ?>
							Zip Code: <? echo $result[csf('zip_code')]; ?>
							Email Address: <? echo $result[csf('email')]; ?>
							Website No: <? echo $result[csf('website')];
									}
										?> -->
					</td>
				</tr>
				<tr>
					<td colspan="4" align="right" style="font-size:18px"><strong><u>Roll Wise Grey <? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?> Transfer Report</u></strong></td>
					<td colspan="5" id="barcode_img_id" align="right"></td>

				</tr>
				<tr>
					<td width="125"><strong>Transfer ID :</strong></td>
					<td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
					<td width="125"><strong>Transfer Date:</strong></td>
					<td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
					<td width="125"><strong>Challan No.:</strong></td>
					<td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
				</tr>

			</table>
			<table width="1020" cellspacing="0" align="right" style="margin-top:5px;">
				<tr>
					<td width="450">
						<table width="100%" cellspacing="0" align="right" style="font-size:12px">
							<tr>
								<td colspan="4" align="center" style="font-weight:bold; font-size:14px;"><u>From Order</u></td>
							</tr>
							<tr>
								<td width="100">Sales Order No:</td>
								<td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['no']; ?></td>
								<td width="100">Quantity:</td>
								<td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['qty']; ?></td>
							</tr>
							<tr>
								<td>Po Company:</td>
								<td>&nbsp;<? echo $company_library[$po_comp_arr[$job_array[$dataArray[0][csf('from_order_id')]]['booking_id']]]; ?></td>
								<td>Style Ref:</td>
								<td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['style']; ?></td>
							</tr>
							<tr>
								<td>Booking No:</td>
								<td>&nbsp;<? echo $from_booking; ?></td>
							</tr>
							<tr>
								<td>Int. Ref. No:</td>
								<td>&nbsp;<? echo chop($int_ref_arr[$from_booking], ","); ?></td>
								<td>Color:</td>
								<td>&nbsp;<? echo $from_color; ?></td>
							</tr>
						</table>
					</td>
					<td>
						<table width="100%" cellspacing="0" align="right" style="font-size:12px">
							<tr>
								<td colspan="4" align="center" style="font-weight:bold; font-size:14px;"><u>To Order</u></td>
							</tr>
							<tr>
								<td width="100">Sales Order No:</td>
								<td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['no']; ?></td>
								<td width="100">Quantity:</td>
								<td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['qty']; ?></td>
							</tr>
							<tr>
								<td>Po Company:</td>
								<td>&nbsp;<? echo $company_library[$po_comp_arr[$job_array[$dataArray[0][csf('to_order_id')]]['booking_id']]]; ?></td>
								<td>Style Ref:</td>
								<td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['style']; ?></td>
							</tr>
							<tr>
								<td>Booking No:</td>
								<td>&nbsp;<? echo $to_booking; ?></td>
							</tr>
							<tr>
								<td>Int. Ref. No:</td>
								<td>&nbsp;<? echo chop($int_ref_arr[$to_booking], ","); ?></td>
								<td>Color:</td>
								<td>&nbsp;<? echo $color_library[$dataArray[0][csf('to_color_id')]]; ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<br>
			<div style="width:100%;">
				<table align="right" cellspacing="0" width="1020" border="1" rules="all" class="rpt_table" style="font-size:13px">
					<thead bgcolor="#dddddd" align="center">
						<th width="30">SL</th>
						<th width="60">Prog. No</th>
						<th width="160">Fabric Description</th>
						<th width="100">Color Range</th>
						<th width="70">Stich Length</th>
						<th width="100">Machine<br>DiaxGG</th>
						<th width="80">Y/Count</th>
						<th width="80">Y/Lot</th>
						<th width="50">Rack</th>
						<th width="50">Shelf</th>
						<th width="60">No Of Roll</th>
						<th width="50">UOM</th>
						<th>Transfered Qty</th>
					</thead>
					<tbody>
						<?

						$i = 1;
						foreach ($sql_result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$transfer_qnty = $row[csf('transfer_qnty')];
							$transfer_qnty_sum += $transfer_qnty;

							$ycount = '';
							$count_id = explode(',', $row[csf('y_count')]);
							foreach ($count_id as $count) {
								if ($ycount == '') $ycount = $count_arr[$count];
								else $ycount .= "," . $count_arr[$count];
							}
						?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $i; ?></td>
								<td align="center"><? echo $row[csf("booking_no")]; ?></td>
								<td align="center"><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
								<td align="center"><? echo $color_range[$progInfoArr[$row[csf('booking_no')]]['color_range']]; ?></td>
								<td align="center"><? echo $row[csf("stitch_length")]; ?></td>
								<td align="center">
									<?
									echo $progInfoArr[$row[csf('booking_no')]]['machine_dia'] . 'x' . $progInfoArr[$row[csf('booking_no')]]['machine_gg'];
									?>
								</td>
								
								<td align="center"><? echo $ycount; ?></td>
								<td align="center"><? echo $row[csf("yarn_lot")]; ?></td>
								<td align="center"><? echo $row[csf("to_rack")]; ?></td>
								<td align="center"><? echo $row[csf("to_shelf")]; ?></td>
								<td align="center"><? echo $row[csf("no_of_roll")]; ?></td>
								<td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
								<td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
							</tr>
						<?
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="12" align="right"><strong>Total </strong></td>
							<td align="right"><?php echo $transfer_qnty_sum; ?></td>
						</tr>
					</tfoot>
				</table>
				<br>
				<?
				echo signature_table(111, $data[0], "900px");
				?>

			</div>
		</div>
		<?if($path){?>
			<script type="text/javascript" src="<?=$path?>../../js/jquery.js"></script>
			<script type="text/javascript" src="<?=$path?>../../js/jquerybarcode.js"></script>
		<?}else{?>
			<script type="text/javascript" src="../../js/jquery.js"></script>
			<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<?}?>	
		<script>
			function generateBarcode(valuess) {
				var value = valuess; //$("#barcodeValue").val();
				//alert(value)
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

				$("#barcode_img_id").show().barcode(value, btype, settings);
			}
			generateBarcode("<? echo $dataArray[0][csf('transfer_system_id')]; ?>");
		</script>

	<?
		exit();
}


if ($action == "grey_fabric_order_to_order_transfer_print_4") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$path = $data[3];
	//print_r ($data);

	// $sql = "select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category, to_color_id, transfer_criteria, remarks, driver_name, mobile_no, vehicle_no from inv_item_transfer_mst  where id='$data[1]' and company_id='$data[0]'";

	$sql = "select a.id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, a.to_order_id, a.item_category, a.to_color_id, a.transfer_criteria, a.remarks, a.driver_name, a.mobile_no, a.vehicle_no ,max(b.from_store) as from_store,max(b.to_store) as to_store from inv_item_transfer_mst a , inv_item_transfer_dtls b where a.id='$data[1]' and a.company_id='$data[0]' and b.mst_id= a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, a.to_order_id, a.item_category, a.to_color_id, a.transfer_criteria, 
	a.remarks, a.driver_name, a.mobile_no, a.vehicle_no";
	// echo $sql;die;  

	$dataArray = sql_select($sql);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13", "id", "product_name_details");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$store_location_library = return_library_array("select id, store_name from lib_store_location", "id", "store_name");

	$po_buyer_arr = return_library_array("select id, buyer_id from wo_booking_mst", 'id', 'buyer_id');
	$po_comp_arr = return_library_array("select id, company_id from wo_booking_mst", 'id', 'company_id');

	$poDataArray = sql_select("SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.sales_booking_no, a.buyer_id, a.booking_id, sum(b.grey_qty) as qty
	from fabric_sales_order_mst a, fabric_sales_order_dtls b
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.sales_booking_no, a.buyer_id, a.booking_id");
	$job_array = array(); //$all_job_id='';
	foreach ($poDataArray as $row) {
		$job_array[$row[csf('id')]]['no'] = $row[csf('job_no')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_id')];
		$job_array[$row[csf('id')]]['style'] = $row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['qty'] = $row[csf('qty')];
		$job_array[$row[csf('id')]]['company'] = $row[csf('company_id')];
		$job_array[$row[csf('id')]]['booking'] = $row[csf('sales_booking_no')];
		$job_array[$row[csf('id')]]['booking_id'] = $row[csf('booking_id')];
	}
	unset($poDataArray);

	$from_booking = $job_array[$dataArray[0][csf('from_order_id')]]['booking'];
	$to_booking = $job_array[$dataArray[0][csf('to_order_id')]]['booking'];
	if ($from_booking != "" || $to_booking != "") {
		$po_sql = "SELECT a.id, a.po_number, a.grouping, b.booking_no from wo_po_break_down a, wo_booking_dtls b
		where a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.booking_no in('$from_booking','$to_booking') and b.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number, a.grouping, b.booking_no order by a.grouping";
		// echo $po_sql;die;
		$po_sql_result = sql_select($po_sql);
		$refBooking_cond = "";
		$int_ref_arr=array();
		foreach ($po_sql_result as $key => $row) {
			if($row[csf('grouping')]!=null)
			$int_ref_arr[$row[csf('booking_no')]] = $row[csf('grouping')] ;
		}
		// echo "<pre>";print_r($int_ref_arr);die;

		/*$po_sql = sql_select("select po_break_down_id from wo_booking_dtls where booking_no = '".$from_booking."'");
		foreach ($po_sql as $val)
		{
			$po_arr[$val[csf("po_break_down_id")]] =$val[csf("po_break_down_id")];
		}
		$po_arr = array_filter($po_arr);
		$po_ids = implode(",", $po_arr);

		$po_sql="select id, po_number, grouping from wo_po_break_down where status_active=1 and is_deleted=0 and id in($po_ids)";
		$result_po_sql = sql_select($po_sql);
		foreach ($result_po_sql as $key => $row)
		{
			$po_int_ref_arr[$row[csf('grouping')]]=$row[csf('grouping')];
		}
		$po_int_ref_arr = array_filter($po_int_ref_arr);
		$from_int_ref = implode(",", $po_int_ref_arr);
		unset($po_sql);*/
	}

	$sql_dtls = "SELECT a.from_prod_id, sum(a.transfer_qnty) as transfer_qnty, a.uom, a.y_count, a.brand_id, a.yarn_lot, a.to_rack, a.to_shelf, a.stitch_length, b.booking_no, a.color_names,count(b.roll_id) as no_of_roll
	from inv_item_transfer_dtls a, pro_roll_details b
	where a.id=b.dtls_id and b.entry_form=133 and a.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.from_prod_id,a.uom,a.y_count,a.brand_id,a.yarn_lot,a.to_rack,a.to_shelf,a.stitch_length,b.booking_no,a.color_names ";
	// echo $sql_dtls;
	$sql_result = sql_select($sql_dtls);
	$from_color_arr = array();
	$bookingNoChk = array();
	$bookingNoArr = array();
	foreach ($sql_result as $key => $row) {
		$from_color_arr[$row[csf('color_names')]] .= $row[csf('color_names')] . ',';

		if ($bookingNoChk[$row[csf('booking_no')]] == "") {
			$bookingNoChk[$row[csf('booking_no')]] = $row[csf('booking_no')];
			array_push($bookingNoArr, $row[csf('booking_no')]);
		}
	}
	$from_color = '';
	$color_id_arr = array_unique(explode(",", implode(",", $from_color_arr)));
	foreach ($color_id_arr as $val) {
		if ($val > 0) $from_color .= $color_library[$val] . ",";
	}
	$from_color = chop($from_color, ',');

	$prog_arr = "SELECT a.id as program_no,a.color_range,a.machine_dia,a.machine_gg,b.color_type_id
	FROM ppl_planning_info_entry_dtls a, PPL_PLANNING_ENTRY_PLAN_DTLS b
	WHERE a.mst_id = b.mst_id AND a.id = b.dtls_id AND a.status_active = 1 AND a.is_deleted = 0 " . where_con_using_array($bookingNoArr, 0, 'a.id') . " ";
	//echo $prog_arr;
	$prog_arr_result = sql_select($prog_arr);
	$progInfoArr = array();
	foreach ($prog_arr_result as $row) {
		$progInfoArr[$row[csf('program_no')]]['color_range'] = $row[csf('color_range')];
		$progInfoArr[$row[csf('program_no')]]['machine_gg'] = $row[csf('machine_gg')];
		$progInfoArr[$row[csf('program_no')]]['machine_dia'] = $row[csf('machine_dia')];
		$progInfoArr[$row[csf('program_no')]]['color_type_id'] = $row[csf('color_type_id')];
	}

	?>
		<div style="width:1050px;">
			<table width="1040" cellspacing="0" align="right">
				<tr>
					<td colspan="8" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="8" align="center" style="font-size:14px">
						<!-- <?
								$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
								foreach ($nameArray as $result) {
								?>
							Plot No: <? echo $result[csf('plot_no')]; ?>
							Level No: <? echo $result[csf('level_no')] ?>
							Road No: <? echo $result[csf('road_no')]; ?>
							Block No: <? echo $result[csf('block_no')]; ?>
							City No: <? echo $result[csf('city')]; ?>
							Zip Code: <? echo $result[csf('zip_code')]; ?>
							Email Address: <? echo $result[csf('email')]; ?>
							Website No: <? echo $result[csf('website')];
									}
										?> -->
					</td>
				</tr>
				<tr>
					<td colspan="5" align="right" style="font-size:18px"><strong><u>Roll Wise Grey Transfer Challan</u></strong>&nbsp;</td>
					<td colspan="5" id="barcode_img_id" align="right"></td>

				</tr>
				<tr>
					<td width="100px"><strong>Transfer ID:</strong></td>
					<td width="200px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
					<td width="125px"><strong>Transfer Date:</strong></td>
					<td width="100px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
					<td width="100px"><strong>Challan No:</strong></td>
					<td width="150px"><? echo $dataArray[0][csf('challan_no')]; ?></td> 
					<td width="135px"><strong>Transfer Criteria:</strong></td>
					<td width="100px"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
				</tr>

			</table>
			<table width="1040" cellspacing="0" align="right" style="margin-top:5px;">
				<tr>
					<td width="460">
						<table width="100%" cellspacing="0" align="right" style="font-size:12px">
							<tr>
								<td colspan="4" align="center" style="font-weight:bold; font-size:14px;"><u>From</u></td>
							</tr>
							<tr>
								<td colspan="1" width="100">Store Name:</td>
								<td colspan="3" width="120">&nbsp;<? echo $store_location_library[$dataArray[0][csf('from_store')]]; ?></td>
								
							</tr>
							<tr>
								<td colspan="1" width="100">Sales Order No:</td>
								<td colspan="3" width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['no']; ?></td>
								
							</tr>
							
							<tr>
								<td colspan="1">Style Ref:</td>
								<td colspan="3">&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['style']; ?></td>
								
							</tr>
							<tr>
								<td colspan="1">Int. Ref. No:</td>
								<td colspan="3">&nbsp;<? echo '1' . chop($int_ref_arr[$from_booking], ","); ?></td>
								
							</tr>
						</table>
					</td>
					<td>
						<table width="100%" cellspacing="0" align="right" style="font-size:12px">
							<tr>
								<td colspan="4" align="center" style="font-weight:bold; font-size:14px;"><u>To</u></td>
							</tr>
							<tr>
								<td colspan="1" width="100">Store Name:</td>
								<td colspan="3" width="120">&nbsp;<? echo $store_location_library[$dataArray[0][csf('to_store')]]; ?></td>
								
							</tr>
							<tr>
								<td colspan="1" width="100">Sales Order No:</td>
								<td colspan="3" width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['no']; ?></td>
								
							</tr>
							
							<tr>
								<td colspan="1">Style Ref:</td>
								<td colspan="3">&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['style']; ?></td>
								
							</tr>
							<tr>
								<td colspan="1">Int. Ref. No:</td>
								<td colspan="3">&nbsp;<? echo '1' . chop($int_ref_arr[$to_booking], ","); ?></td>
								
							</tr>
						</table>
					</td>
				</tr>
			</table>

			<table width="1040" cellspacing="0" align="right" style="margin-top:5px;" style="font-size:12px">
						<tr  >
							<td width="1040" colspan="8" align="center" style="font-weight:bold; font-size:14px;"><u>Driver Information</u></td>
						</tr>
						
						<tr align="left" style="font-size:12px;">
							<td width="100" align=""  >Driver Name:</td>
							<td width="150" align=""><? echo $dataArray[0][csf('driver_name')]; ?></td>
							<td width="100" align="">Mobile No</td>
							<td width="150" align=""><? echo $dataArray[0][csf('mobile_no')]; ?></td>
							<td width="100" align="">Vehicle No:</td>
							<td width="150" align=""><? echo $dataArray[0][csf('vehicle_no')]; ?></td>
							<td width="100" align="">Remark:</td>
							<td width="190" align=""><? echo $dataArray[0][csf('remarks')]; ?></td>
						</tr>
						<tr>
							<td colspan="8" align="center">&nbsp;</td>
						</tr>
			</table>


			<br>
			<div style="width:100%;">
				<table align="right" cellspacing="0" width="1040" border="1" rules="all" class="rpt_table" style="font-size:13px" >
					<thead bgcolor="#dddddd" align="center">
						<th width="30">SL</th>
						<th width="60">Prog. No</th>
						<th width="180">Fabric Description</th>
						<th width="100">Color Range</th>
						<th width="80">Color Type</th>
						<th width="70">Stich Length</th>
						<th width="100">Machine<br>DiaxGG</th>
						<th width="90">Y/Count</th>
						<th width="80">Y/Lot</th>
						<th width="150">Yarn Compositon</th>
						<th width="60">No Of Roll</th>
						<th width="50">UOM</th>
						<th>Transfered Qty</th>
					</thead>

					<tbody style="font-weight: normal;">
						
						<?
						$i = 1;
						$result_data_arr=array();
						$result_data_arr2=array();
						$result_data_arr3=array();


						foreach ($sql_result as $row) 
						{
							
							$transfer_qnty = $row[csf('transfer_qnty')];
							$transfer_qnty_sum += $transfer_qnty;
							$roll_count = $row[csf("no_of_roll")];
							$roll_sum += $roll_count;


							$ycount = '';
							$count_id = explode(',', $row[csf('y_count')]);
							foreach ($count_id as $count) {
								if ($ycount == '') $ycount = $count_arr[$count];
								else $ycount .= "," . $count_arr[$count];
							}

							
							$result_data_arr[$row[csf("booking_no")]][$product_arr[$row[csf("from_prod_id")]]][$color_range[$progInfoArr[$row[csf('booking_no')]]['color_range']]][$color_type[$progInfoArr[$row[csf('booking_no')]]['color_type_id']]][$row[csf("stitch_length")]][$progInfoArr[$row[csf('booking_no')]]['machine_dia'] . 'x' . $progInfoArr[$row[csf('booking_no')]]['machine_gg']][$ycount][$row[csf("yarn_lot")]][$product_arr[$row[csf("from_prod_id")]]] +=  $row[csf("no_of_roll")];

							$result_data_arr2[$row[csf("booking_no")]][$product_arr[$row[csf("from_prod_id")]]][$color_range[$progInfoArr[$row[csf('booking_no')]]['color_range']]][$color_type[$progInfoArr[$row[csf('booking_no')]]['color_type_id']]][$row[csf("stitch_length")]][$progInfoArr[$row[csf('booking_no')]]['machine_dia'] . 'x' . $progInfoArr[$row[csf('booking_no')]]['machine_gg']][$ycount][$row[csf("yarn_lot")]][$product_arr[$row[csf("from_prod_id")]]] =  $unit_of_measurement[$row[csf("uom")]];

							$result_data_arr3[$row[csf("booking_no")]][$product_arr[$row[csf("from_prod_id")]]][$color_range[$progInfoArr[$row[csf('booking_no')]]['color_range']]][$color_type[$progInfoArr[$row[csf('booking_no')]]['color_type_id']]][$row[csf("stitch_length")]][$progInfoArr[$row[csf('booking_no')]]['machine_dia'] . 'x' . $progInfoArr[$row[csf('booking_no')]]['machine_gg']][$ycount][$row[csf("yarn_lot")]][$product_arr[$row[csf("from_prod_id")]]] +=  $row[csf("transfer_qnty")];

						}

						foreach($result_data_arr as $boking => $row){
							foreach($row as $prog => $row2){
								foreach($row2 as $colorRange => $row3){
									foreach($row3 as $colorType => $row4){
										foreach($row4 as $stitchLength => $row5){
											foreach($row5 as $dia_gg => $row6){
												foreach($row6 as $yarnCount => $row7){
													foreach($row7 as $yarnLot => $row8){
														if ($i % 2 == 0)
														{
															$bgcolor = "#E9F3FF";
														}
														else
														{
															$bgcolor = "#FFFFFF";
														}
														?>
														<tr bgcolor="<? echo $bgcolor; ?>" style="font-weight: normal;">
														<?php
														foreach($row8 as $prodId => $row9){
															?>
															<th align="center" style="font-weight: normal;"> <?php echo $i++; ?></th>
															<th align="center" style="font-weight: normal;"> <?php echo $boking; ?></th>
															<th align="center" style="font-weight: normal;"> <?php echo $prog; ?></th>
															<th align="center" style="font-weight: normal;"> <?php echo $colorRange; ?></th>
															<th align="center" style="font-weight: normal;"> <?php echo $colorType; ?></th>
															<th align="center" style="font-weight: normal;"> <?php echo $stitchLength; ?></th>
															<th align="center" style="font-weight: normal;"> <?php echo $dia_gg; ?></th>
															<th align="center" style="font-weight: normal;"> <?php echo $yarnCount; ?></th>
															<th align="center" style="font-weight: normal;"> <?php echo $yarnLot; ?></th>
															<th align="center" style="font-weight: normal;"> <?php echo $prodId; ?></th>
															<th align="center" style="font-weight: normal;"> <?php echo $row9; ?></th>
															<th align="center" style="font-weight: normal;"> <?php 
																echo $uom= $result_data_arr2[$boking][$prog][$colorRange][$colorType][$stitchLength][$dia_gg][$yarnCount][$yarnLot][$prodId]; ?>
																
															</th>
															<th align="center" style="font-weight: normal;"><?php 
																echo $result_data_arr3[$boking][$prog][$colorRange][$colorType][$stitchLength][$dia_gg][$yarnCount][$yarnLot][$prodId]; ?>
															</th>

															<?
															
														}
														?>
															</tr>
														<?
													}
												}
											}
										}
									}
								}
							}
						}

						?>

					</tbody>
					<tfoot>
						<tr>
							<td colspan="10" align="right"><strong>Total </strong></td>
							<td align="center"><?php echo $roll_sum; ?></td>
							<td align="center"><?php echo $uom; ?></td>
							<td align="center"><?php echo $transfer_qnty_sum; ?></td>
						</tr>
					</tfoot>
				</table>
				<br>
				<?
				echo signature_table(111, $data[0], "900px");
				?>

			</div>
		</div>
		<?if($path){?>
			<script type="text/javascript" src="<?=$path?>../../js/jquery.js"></script>
			<script type="text/javascript" src="<?=$path?>../../js/jquerybarcode.js"></script>
		<?}else{?>
			<script type="text/javascript" src="../../js/jquery.js"></script>
			<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<?}?>
		<script>
			function generateBarcode(valuess) {
				var value = valuess; //$("#barcodeValue").val();
				//alert(value)
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

				$("#barcode_img_id").show().barcode(value, btype, settings);
			}
			generateBarcode("<? echo $dataArray[0][csf('transfer_system_id')]; ?>");
		</script>

	<?
		exit();
}



?>