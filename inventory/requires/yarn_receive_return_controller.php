<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//--------------------------------------------------------------------------------------------
if ($action == "upto_variable_settings") {
	$sql =  sql_select("select store_method from variable_settings_inventory where company_name = $data and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$return_data = "";
	if (count($sql) > 0) {
		$return_data = $sql[0][csf('store_method')];
	} else {
		$return_data = 0;
	}

	echo $return_data;
	die;
}

if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 1);
	exit();
}

if ($action == "load_drop_down_store") {
	$data = explode("_", $data);
	echo create_drop_down("cbo_store_name", 80, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' and b.category_type in(1) and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "--Select store--", 0, "reset_on_change(this.id);load_drop_down('requires/yarn_receive_return_controller', this.value+'_'+$data[0], 'load_drop_floor','floor_td');load_drop_down('requires/yarn_receive_return_controller', this.value+'_'+$data[0], 'load_drop_room','room_td');load_drop_down('requires/yarn_receive_return_controller', this.value+'_'+$data[0], 'load_drop_rack','rack_td');load_drop_down('requires/yarn_receive_return_controller', this.value+'_'+$data[0], 'load_drop_shelf','shelf_td');load_drop_down('requires/yarn_receive_return_controller', this.value+'_'+$data[0], 'load_drop_bin','bin_td');", 1);
	exit();
}

if ($action == "load_drop_floor") {
	// echo "string";die;
	$data = explode("_", $data);
	$store_id = $data[0];
	$company_id = $data[1];
	echo create_drop_down("cbo_floor", "80", "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name", "floor_id,floor_room_rack_name", 1, "--Select Floor--", 0, "", 1);
}

if ($action == "load_drop_room") {
	$data = explode("_", $data);
	$store_id = $data[0];
	$company_id = $data[1];

	echo create_drop_down("cbo_room", "80", "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name", "room_id,floor_room_rack_name", 1, "--Select Room--", 0, "", 1);
}

if ($action == "load_drop_rack") {
	$data = explode("_", $data);
	$store_id = $data[0];
	$company_id = $data[1];
	echo create_drop_down("txt_rack", '80', "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name", "rack_id,floor_room_rack_name", 1, "--Select Rack--", 0, "", 1);
}

if ($action == "load_drop_shelf") {
	$data = explode("_", $data);
	$store_id = $data[0];
	$company_id = $data[1];
	echo create_drop_down("txt_shelf", '80', "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name", "shelf_id,floor_room_rack_name", 1, "--Select Shelf--", 0, "", 1);
}

if ($action == "load_drop_bin") {
	$data = explode("_", $data);
	$store_id = $data[0];
	$company_id = $data[1];
	echo create_drop_down("cbo_bin", '80', "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.bin_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name order by a.floor_room_rack_name", "bin_id,floor_room_rack_name", 1, "--Select Bin--", 0, "", 1);
}

if ($action == "mrr_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
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
				<table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
					<thead>
						<tr>
							<th>Supplier</th>
							<th>Search By</th>
							<th align="center" id="search_by_td_up">Please Enter MRR No</th>
							<th>Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /></th>
						</tr>
					</thead>
					<tbody>
						<tr class="general">
							<td>
								<?
								echo create_drop_down("cbo_supplier", 150, "select c.supplier_name,c.id from lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
								?>
							</td>
							<td>
								<?
								$search_by = array(1 => 'MRR No', 2 => 'Challan No', 3 => 'Lot No');
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 120, $search_by, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td width="" align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_mrr_search_list_view', 'search_div', 'yarn_receive_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1);  ?>
								<!-- Hidden field here-->
								<input type="hidden" id="hidden_recv_number" value="" />

							</td>
						</tr>
					</tbody>
					</tr>
				</table>
				<div align="center" style="margin-top:10px" valign="top" id="search_div"> </div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "create_mrr_search_list_view") {
	$ex_data = explode("_", $data);
	$supplier = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = trim($ex_data[2]);
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];
	$company = $ex_data[5];

	$sql_cond = "";
	if (trim($txt_search_common) != "") {
		if (trim($txt_search_by) == 1) // for mrr
		{
			$mrr_cond .= " and a.recv_number LIKE '%$txt_search_common'";
			$trans_cond .= " and a.transfer_system_id LIKE '%$txt_search_common'";
		} else if (trim($txt_search_by) == 2) // for chllan no
		{
			$mrr_cond .= " and a.challan_no LIKE '%$txt_search_common%'";
			$trans_cond .= " and a.challan_no LIKE '%$txt_search_common%'";
		} else if (trim($txt_search_by) == 3) // for chllan no
		{
			$mrr_cond .= " and d.lot='$txt_search_common'";
			$trans_cond .= " and d.lot='$txt_search_common'";
		}
	}

	if ($fromDate != "" && $toDate != "") {
		if ($db_type == 0) {
			$mrr_cond .= " and a.receive_date  between '" . change_date_format($fromDate, 'yyyy-mm-dd') . "' and '" . change_date_format($toDate, 'yyyy-mm-dd') . "'";
			$trans_cond .= " and a.transfer_date  between '" . change_date_format($fromDate, 'yyyy-mm-dd') . "' and '" . change_date_format($toDate, 'yyyy-mm-dd') . "'";
		} else {
			$mrr_cond .= " and a.receive_date  between '" . change_date_format($fromDate, '', '', 1) . "' and '" . change_date_format($toDate, '', '', 1) . "'";
			$trans_cond .= " and a.transfer_date  between '" . change_date_format($fromDate, '', '', 1) . "' and '" . change_date_format($toDate, '', '', 1) . "'";
		}
	}
	if (trim($company) != "") {
		$mrr_cond .= " and b.company_id='$company'";
		$trans_cond .= " and b.company_id='$company'";
	}
	if (trim($supplier) != 0) {
		$mrr_cond .= " and d.supplier_id='$supplier'";
		$trans_cond .= " and d.supplier_id='$supplier'";
	}

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
	else $year_field = "";

	if ($db_type == 0) {
		$select_prod = " group_concat(d.id) as prod_id";
	} else {
		$select_prod = " listagg(cast(d.id as varchar(4000)),',') within group(order by d.id) as prod_id";
	}

	$sql = "select a.id as mst_id, a.recv_number_prefix_num,a.recv_number, $year_field d.supplier_id, a.challan_no, a.receive_date, a.receive_basis, sum(b.cons_quantity) as receive_qnty,  sum(b.balance_qnty) as balance_qnty, 1 as type ,a.lc_no, d.lot, $select_prod
	from product_details_master d, inv_transaction b, inv_receive_master a
	where d.id=b.prod_id and a.id=b.mst_id and a.entry_form in(1,9) and b.item_category=1 and b.transaction_type in(1,4) and a.status_active=1 $mrr_cond
	group by a.id, a.recv_number_prefix_num ,a.recv_number, d.supplier_id, a.challan_no, a.receive_date, a.receive_basis, a.insert_date ,a.lc_no, d.lot
	HAVING sum(b.balance_qnty)>0
	union all
	select a.id as mst_id, a.transfer_prefix_number as recv_number_prefix_num, a.transfer_system_id as recv_number, $year_field d.supplier_id, a.challan_no, a.transfer_date as receive_date, 0 as receive_basis, sum(b.cons_quantity) as receive_qnty, sum(b.balance_qnty) as balance_qnty, 2 as type ,b.btb_lc_id as lc_no, d.lot, $select_prod
	from product_details_master d, inv_transaction b,  inv_item_transfer_mst a
	where d.id=b.prod_id and a.id=b.mst_id and a.transfer_criteria in(1,2) and a.item_category='1' and b.item_category=1 and a.status_active=1 $trans_cond
	group by a.id, a.transfer_prefix_number, a.transfer_system_id, d.supplier_id, a.challan_no, a.transfer_date, a.insert_date ,b.btb_lc_id, d.lot
	HAVING sum(b.balance_qnty)>0";
	//echo $sql;
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$btb_lc_arr = return_library_array("select id, lc_number from com_btb_lc_master_details", 'id', 'lc_number');
	$arr = array(2 => $supplier_arr, 5 => $btb_lc_arr, 7 => $receive_basis_arr);
	echo create_list_view("list_view", "MRR No, Year, Supplier Name, Challan No, Lot, BTB LC, Receive Date, Receive Basis, Receive Qty., Balance Qty ", "70,60,180,120,80,90,80,140,90", "1080", "220", 0, $sql, "js_set_value", "mst_id,type,supplier_id,prod_id", "", 1, "0,0,supplier_id,0,0,lc_no,0,receive_basis,0,0", $arr, "recv_number_prefix_num,year,supplier_id,challan_no,lot,lc_no,receive_date,receive_basis,receive_qnty,balance_qnty", "", '', '0,0,0,0,0,0,3,0,2,2');
	exit();
}

if ($action == "populate_data_from_data") {
	$data_ref = explode("**", $data);

	$mst_id = $data_ref[0];
	$mrr_type = $data_ref[1];
	$supplier_id = $data_ref[2];
	$prod_id = $data_ref[3];

	//echo $mrr_type;die;

	if ($mrr_type == 1) {
		$sql = "select id, recv_number, entry_form, receive_basis, receive_purpose, receive_date, challan_no, booking_id, booking_no, issue_id, lc_no from inv_receive_master where id=$mst_id and entry_form in(1,9)";
	} else {
		$sql = "select id, transfer_system_id as recv_number, 10 as entry_form, 0 as receive_basis, transfer_date as receive_date, challan_no, 0 as booking_id, null as booking_no from inv_item_transfer_mst where id=$mst_id";
	}

	//echo $sql;die;
	$res = sql_select($sql);
	foreach ($res as $row) {
		echo "$('#txt_mrr_no').val('" . $row[csf("recv_number")] . "');\n";
		echo "$('#txt_received_id').val('" . $row[csf("id")] . "');\n";
		echo "$('#hdn_issue_id').val('" . $row[csf("issue_id")] . "');\n";
		echo "$('#cbo_return_to').val('" . $supplier_id . "');\n";


		//for order wise qty
		if ($mrr_type == 1 && $row[csf("receive_purpose")] == 2) {
			echo "$('#txt_return_qnty').attr('placeholder','browse').attr('onclick','func_return_qty()').attr('readonly','readonly');\n";
		} else {
			echo "$('#txt_return_qnty').attr('placeholder','Write').removeAttr('onclick','func_return_qty()').removeAttr('readonly','readonly');\n";
		}

		// get yarn issue return info
		if ($row[csf("entry_form")] == 9) {

			if ($all_prod_id == "") $all_prod_id = 0;
			$issue_id = $row[csf("issue_id")];

			$yarn_issue_return_sql = "select e.id as rcv_trans_id, e.pi_wo_batch_no 
			from  inv_transaction b, inv_mrr_wise_issue_details c, inv_transaction e 
			where b.id =c.issue_trans_id and c.recv_trans_id = e.id and b.item_category=1 and e.item_category=1 and b.transaction_type=2 and e.transaction_type=1 and b.prod_id =$prod_id and b.mst_id=$issue_id and c.entry_form = 3 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 and e.receive_basis=1";
			//echo $yarn_issue_return_sql;
			$yarn_issue_return_data = sql_select($yarn_issue_return_sql);
			foreach ($yarn_issue_return_data as $issue_return_row) {
				$pi_id = $issue_return_row[csf("pi_wo_batch_no")];
			}

			if ($pi_id != "") {
				$pi_no = return_field_value("pi_number", "com_pi_master_details", "id='" . $pi_id . "'");
				$ref_closing_status = return_field_value("ref_closing_status", "com_pi_master_details", "id='" . $pi_id . "'");

				echo "$('#txt_pi_no').val('" . $pi_no . "');\n";
				echo "$('#pi_id').val('" . $pi_id . "');\n";
				echo "$('#hidden_ref_closing_status').val('" . $ref_closing_status . "');\n";
				echo "$('#txt_pi_no').attr('disabled','disabled');\n";
			}
		} elseif ($row[csf("entry_form")] == 1) {
			if ($row[csf("receive_basis")] == 1) {
				$pi_no = return_field_value("pi_number", "com_pi_master_details", "id='" . $row[csf("booking_id")] . "'");
				$ref_closing_status = return_field_value("ref_closing_status", "com_pi_master_details", "id='" . $row[csf("booking_id")] . "'");
				echo "$('#txt_pi_no').val('" . $pi_no . "');\n";
				echo "$('#pi_id').val('" . $row[csf("booking_id")] . "');\n";
				echo "$('#txt_pi_no').attr('disabled','disabled');\n";
				echo "$('#hidden_ref_closing_status').val('" . $ref_closing_status . "');\n";
			}
		}

		//for lc no
		$lcNumber = return_field_value("lc_number", "com_btb_lc_master_details", "id='" . $row[csf("lc_no")] . "'");
		echo "$('#txt_lc_no').val('" . $lcNumber . "');\n";

		//right side list view
		echo "show_list_view('" . $row[csf("id")] . "**" . $mrr_type . "','show_product_listview','list_product_container','requires/yarn_receive_return_controller','');\n";
	}

	exit();
}

//right side product list create here--------------------//
if ($action == "show_product_listview") {
	$mrr_ref = explode("**", $data);
	$mrr_id = $mrr_ref[0];
	$mrr_type = $mrr_ref[1];
	if ($mrr_type == 1)
		$transaction_type_cond = " and b.transaction_type in(1,4)";
	else
		$transaction_type_cond = " and b.transaction_type in(5)";

	$sql = "select c.product_name_details, c.current_stock, b.mst_id as mrr_id, b.id as tr_id, c.id as prod_id, b.cons_quantity, b.balance_qnty, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box 
	from inv_transaction b, product_details_master c
	where b.prod_id=c.id and b.mst_id=$mrr_id and b.item_category=1 $transaction_type_cond";
	//echo $sql;
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");
	$result = sql_select($sql);
	$i = 1;
?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
		<thead>
			<tr>
				<th>SL</th>
				<th>Product Name</th>
				<th>Store</th>
				<th>Floor</th>
				<th>Room</th>
				<th>Rack</th>
				<th>Shelf</th>
				<th>Bin/Box</th>
				<th>Curr.Stock</th>
			</tr>
		</thead>
		<tbody>
			<? foreach ($result as $row) {
				if ($row[csf("balance_qnty")] > 0) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";
			?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("tr_id")]; ?>","item_details_form_input","requires/yarn_receive_return_controller")' style="cursor:pointer">
						<td><? echo $i; ?></td>
						<td><? echo $row[csf("product_name_details")]; ?></td>
						<td><? echo $store_arr[$row[csf("store_id")]]; ?></td>
						<td><? echo $floor_room_rack_arr[$row[csf("floor_id")]]; ?></td>
						<td><? echo $floor_room_rack_arr[$row[csf("room")]]; ?></td>
						<td><? echo $floor_room_rack_arr[$row[csf("rack")]]; ?></td>
						<td><? echo $floor_room_rack_arr[$row[csf("self")]]; ?></td>
						<td><? echo $floor_room_rack_arr[$row[csf("bin_box")]]; ?></td>
						<td align="right"><? echo number_format($row[csf("balance_qnty")], 2, ".", ""); ?></td>
					</tr>
			<?
					$i++;
				}
			}
			?>
		</tbody>
	</table>
	</fieldset>
<?
	exit();
}

//child form data input here-----------------------------//
if ($action == "item_details_form_input") {
	//$sql = "SELECT b.id as prod_id, b.product_name_details,b.avg_rate_per_unit,a.no_of_bags, a.cone_per_bag, a.store_id, a.floor_id, a.room, a.rack, a.self, a.bin_box, a.order_rate, a.order_ile_cost, a.cons_uom, a.cons_rate, a.cons_quantity, a.cons_amount, a.balance_qnty, a.balance_amount, a.mst_id as receive_id,a.transaction_type, a.issue_id, a.company_id,a.buyer_id from inv_transaction a, product_details_master b where a.id=$data  and a.status_active=1 and a.item_category=1 and transaction_type in(1,4,5) and a.prod_id=b.id and b.status_active=1";

	$sql = "SELECT b.id AS prod_id, b.product_name_details, b.avg_rate_per_unit, a.no_of_bags, a.cone_per_bag, a.store_id, a.floor_id, a.room, a.rack, a.self, a.bin_box, a.order_rate, a.order_ile_cost, a.cons_uom, a.cons_rate, a.cons_quantity, a.cons_amount, a.balance_qnty, a.balance_amount, a.mst_id  AS receive_id,a.id as trans_id, a.transaction_type, a.issue_id, a.company_id, a.buyer_id, listagg(c.po_breakdown_id,',') within group (order by c.po_breakdown_id) as po_id FROM inv_transaction a left join order_wise_pro_details c on a.id=c.trans_id, product_details_master b  WHERE   a.id=$data AND a.status_active = 1 AND a.item_category = 1 AND transaction_type IN (1, 4, 5) AND a.prod_id = b.id AND b.status_active = 1 group by b.id, b.product_name_details, b.avg_rate_per_unit, a.no_of_bags, a.cone_per_bag, a.store_id, a.floor_id, a.room, a.rack, a.self, a.bin_box, a.order_rate, a.order_ile_cost, a.cons_uom, a.cons_rate, a.cons_quantity, a.cons_amount, a.balance_qnty, a.balance_amount, a.mst_id,a.id,a.transaction_type, a.issue_id, a.company_id, a.buyer_id";

	//echo $sql;die;

	$result = sql_select($sql);
	foreach ($result as $row) {
		echo "$('#txt_item_description').val('" . $row[csf("product_name_details")] . "');\n";
		echo "$('#txt_prod_id').val('" . $row[csf("prod_id")] . "');\n";
		echo "$('#txt_rcv_trans_id').val('" . $row[csf("trans_id")] . "');\n";
		echo "$('#cbo_store_name').val('" . $row[csf("store_id")] . "');\n";
		echo "$('#cbo_buyer_name').val('" . $row[csf("buyer_id")] . "');\n";

		echo "load_drop_down('requires/yarn_receive_return_controller', " . $row[csf("store_id")] . "+'_'+" . $row[csf('company_id')] . ", 'load_drop_floor','floor_td');\n";
		echo "document.getElementById('cbo_floor').value 	= '" . $row[csf("floor_id")] . "';\n";

		echo "load_drop_down('requires/yarn_receive_return_controller', " . $row[csf("store_id")] . "+'_'+" . $row[csf('company_id')] . ", 'load_drop_room','room_td');\n";
		echo "document.getElementById('cbo_room').value 	= '" . $row[csf("room")] . "';\n";

		echo "load_drop_down('requires/yarn_receive_return_controller', " . $row[csf("store_id")] . "+'_'+" . $row[csf('company_id')] . ", 'load_drop_rack','rack_td');\n";
		echo "document.getElementById('txt_rack').value 	= '" . $row[csf("rack")] . "';\n";

		echo "load_drop_down('requires/yarn_receive_return_controller', " . $row[csf("store_id")] . "+'_'+" . $row[csf('company_id')] . ", 'load_drop_shelf','shelf_td');\n";
		echo "document.getElementById('txt_shelf').value 	= '" . $row[csf("self")] . "';\n";

		echo "load_drop_down('requires/yarn_receive_return_controller', " . $row[csf("store_id")] . "+'_'+" . $row[csf('company_id')] . ", 'load_drop_bin','bin_td');\n";
		echo "document.getElementById('cbo_bin').value 		= '" . $row[csf("bin_box")] . "';\n";

		echo "$('#txt_return_qnty').val('');\n";
		echo "$('#txt_receive_qnty').val('" . number_format($row[csf("balance_qnty")], 2, ".", "") . "');\n";
		echo "$('#cbo_uom').val('" . $row[csf("cons_uom")] . "');\n";

		if ($row[csf("transaction_type")] == 1) {
			$rate = $row[csf("avg_rate_per_unit")];
			echo "$('#txt_rate').val('" . number_format($rate, 4, ".", "") . "');\n";
		} else if ($row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) {
			echo "$('#txt_rate').val('" . number_format($row[csf("cons_rate")], 4, ".", "") . "');\n";
			$rate = $row[csf("cons_rate")];
		}

		if ($row[csf("transaction_type")] == 4) {
			$ord_rate_sql = "select c.order_rate from inv_transaction a, inv_mrr_wise_issue_details b, inv_transaction c where a.id=b.issue_trans_id and b.recv_trans_id=c.id and a.prod_id='" . $row[csf("prod_id")] . "' and a.mst_id='" . $row[csf("issue_id")] . "' and a.status_active = 1 and a.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0";
			//echo $ord_rate_sql;die;
			$ord_result = sql_select($ord_rate_sql);
			$order_rate = $ord_result[0][csf("order_rate")];
		} else {
			$order_rate = $row[csf("order_rate")];
		}

		echo "$('#order_rate').val('" . number_format($order_rate, 4, ".", "") . "');\n";
		echo "$('#order_ile_cost').val('" . $row[csf("order_ile_cost")] . "');\n";

		$return_value = $rate * $result[0][csf("balance_qnty")];
		echo "$('#txt_return_value').val('" . number_format($return_value, 2, ".", "") . "');\n";



		if ($row[csf("po_id")] != "") {
			echo "$('#txt_return_qnty').attr('placeholder','browse').attr('onclick','func_return_qty()').attr('readonly','readonly');\n";
		} else {
			echo "$('#txt_return_qnty').attr('placeholder','Write').removeAttr('onclick','func_return_qty()').removeAttr('readonly','readonly');\n";
		}
	}

	exit();
}

//data save update delete here------------------------------//
if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}


	$pi_id = str_replace("'", "", $pi_id);
	$hdnReturnString = str_replace("'", "", $hdnReturnString);
	$update_id = str_replace("'", "", $update_id);
	$txt_return_qnty = str_replace("'", "", $txt_return_qnty);
	$txt_issue_qnty = str_replace("'", "", $txt_return_qnty);
	$txt_return_value = str_replace("'", "", $txt_return_value);
	$order_rate = str_replace("'", "", $order_rate);
	$txt_rate = str_replace("'", "", $txt_rate);

	$variable_store_wise_rate = return_field_value("auto_transfer_rcv", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=47 and item_category_id=1 and status_active=1 and is_deleted=0", "auto_transfer_rcv");
	if ($variable_store_wise_rate != 1) $variable_store_wise_rate = 2;

	$store_wise_cond = ($variable_store_wise_rate == 1) ? " and store_id=$cbo_store_name" : "";
	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_prod_id and transaction_type in (1,4,5) $store_wise_cond and status_active = 1", "max_date");
	if ($max_recv_date != "") {
		$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
		$return_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_return_date)));
		if ($return_date < $max_recv_date) {
			echo "20**Return Date Can not Be Less Than Last Receive Date Of This Lot";
			disconnect($con);
			die;
		}
	}


	if ($pi_id != "" && $pi_id > 0) {
		$rcv_rtn_trans_id = str_replace("'", "", $update_id);
		$pi_total_rcv_value = return_field_value("sum(order_amount) as pi_total_rcv", "inv_transaction", "pi_wo_batch_no in($pi_id) and receive_basis in(1) and transaction_type in(1) and status_active=1 and is_deleted=0", "pi_total_rcv");
		$rcv_rtn_up_cond = "";
		if ($rcv_rtn_trans_id != "") $rcv_rtn_up_cond = " and id <>$rcv_rtn_trans_id";
		$pi_total_rcv_rtn_value = return_field_value("sum(cons_quantity*order_rate) as pi_total_rcv_rtn", "inv_transaction", "pi_wo_batch_no in($pi_id) and transaction_type =3 and status_active=1 and is_deleted=0 $rcv_rtn_up_cond", "pi_total_rcv_rtn");

		$actual_pi_rcv_value = ($pi_total_rcv_value - $pi_total_rcv_rtn_value);

		$accept_value = return_field_value("sum(current_acceptance_value) as accept_value",  "com_import_invoice_dtls", "status_active=1 and is_deleted=0 and pi_id in($pi_id)", "accept_value");

		$pi_value = return_field_value("sum(net_pi_amount) as pi_value",  "com_pi_item_details", "status_active=1 and is_deleted=0 and pi_id in($pi_id)", "pi_value");

		$cumu_accept_value = number_format($actual_pi_rcv_value - $accept_value, 2, ".", "");

		$return_value = number_format(($order_rate * $txt_return_qnty), 2, ".", "");

		if ($return_value > $cumu_accept_value) {
			echo "20**Total Payable Value= $actual_pi_rcv_value\nTotal Accp.Value= $accept_value\nAllowed Rtn Value= $cumu_accept_value\nSo current Return value $return_value is not allowed";
			disconnect($con);
			die;
		}
	}

	// check variable settings if allocation is available or not
	$inv_vs = sql_select("select allocation,smn_allocation,sales_allocation from variable_settings_inventory where company_name=$cbo_company_id and variable_list=18 and item_category_id = 1");
	$variable_set_allocation = $inv_vs[0][csf('allocation')];
	$smn_variable_set_allocation = $inv_vs[0][csf('smn_allocation')];
	$sales_variable_set_allocation = $inv_vs[0][csf('sales_allocation')];

	$variable_store_wise_rate = return_field_value("auto_transfer_rcv", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=47 and item_category_id=1 and status_active=1 and is_deleted=0", "auto_transfer_rcv");
	if ($variable_store_wise_rate != 1) $variable_store_wise_rate = 2;

	$update_conds = "";
	if ($update_id > 0) $update_conds = " and id<>$update_id";
	$store_stock_sql = "select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as BALANCE_STOCK, sum((case when transaction_type in(1,4,5) then store_amount else 0 end)-(case when transaction_type in(2,3,6) then store_amount else 0 end)) as BALANCE_AMT 
	from inv_transaction 
	where status_active=1 and prod_id=$txt_prod_id and store_id=$cbo_store_name $update_conds";
	//echo "30**$store_stock_sql";disconnect($con);die;
	$store_stock_sql_result = sql_select($store_stock_sql);
	$store_item_rate = 0;
	if ($store_stock_sql_result[0]["BALANCE_AMT"] != 0 && $store_stock_sql_result[0]["BALANCE_STOCK"] != 0) {
		$store_item_rate = $store_stock_sql_result[0]["BALANCE_AMT"] / $store_stock_sql_result[0]["BALANCE_STOCK"];
	}
	$issue_store_value = $store_item_rate * $txt_issue_qnty;

	if ($operation == 0) // Insert Here----------------------------------------------------------
	{
		//---------------Check Duplicate product in Same return number ------------------------//
		$duplicate = is_duplicate_field("b.id", "inv_issue_master a, inv_transaction b", "a.id=b.mst_id and a.issue_number=$txt_return_id and b.prod_id=$txt_prod_id and b.transaction_type=3");
		if ($duplicate == 1) {
			echo "20**Duplicate Product is Not Allow in Same Return Number.";
			disconnect($con);
			die;
		}
		//------------------------------Check Brand END---------------------------------------//

		/******** original product id check start ********/
		$origin_prod_id = return_field_value("origin_prod_id", "inv_transaction", "prod_id=$txt_prod_id and status_active=1 and mst_id=$txt_received_id and transaction_type in(1,4,5) and item_category=1", "origin_prod_id");
		/******** original product id check end ********/

		$receive_info = sql_select("select a.receive_purpose,a.receive_basis,a.exchange_rate,a.issue_id,a.entry_form,a.booking_id,b.order_rate from inv_receive_master a,inv_transaction b where a.id=b.mst_id and a.id=$txt_received_id and b.prod_id=$txt_prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(1,4,5)");

		$receive_purpose = $receive_info[0][csf("receive_purpose")];
		$receive_basis = $receive_info[0][csf("receive_basis")];
		$rec_entry_form = $receive_info[0][csf("entry_form")];
		$booking_id = $receive_info[0][csf("booking_id")];
		$exchange_rate = $receive_info[0][csf("exchange_rate")];
		$order_rate = $receive_info[0][csf("order_rate")];

		$is_with_order = return_field_value("entry_form", "wo_yarn_dyeing_mst", " status_active=1 and id=$booking_id", "entry_form");

		$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value,available_qnty,allocated_qnty from product_details_master where id=$txt_prod_id");

		$presentStock = $presentStockValue = $presentAvgRate = $allocated_qnty = $available_qnty = 0;
		$product_name_details = "";
		foreach ($sql as $result) {
			$presentStock			= $result[csf("current_stock")];
			$presentStockValue		= $result[csf("stock_value")];
			$presentAvgRate			= $result[csf("avg_rate_per_unit")];
			$product_name_details 	= $result[csf("product_name_details")];
			$available_qnty			= $result[csf("available_qnty")];
			$allocated_qnty 		= $result[csf("allocated_qnty")];
		}

		if ($presentAvgRate > 0) $cons_rate = $presentAvgRate;
		else $cons_rate = str_replace("'", "", $order_rate) * $exchange_rate;
		//echo "10** $presentAvgRate = $cons_rate";die;
		//adjust product master table START-------------------------------------//
		$order_amt = $txt_return_qnty * $order_rate;
		//transaction table insert here START--------------------------------//
		$avg_rate_amount = $txt_return_qnty * $cons_rate;
		$cons_amount = $txt_return_qnty * $cons_rate;

		// echo "20**".$variable_set_allocation;die;1
		// echo "20**$txt_return_qnty > $available_qnty";die;//0
		if ($variable_set_allocation == 1) {
			if ($receive_basis == 2 && $receive_purpose == 2) {
				if ($is_with_order == 41 || $is_with_order == 125 || $is_with_order == 135) {
					if ($txt_return_qnty > $allocated_qnty) {
						echo "20**Return quantity is not available.\nAvailable=" . $allocated_qnty;
						disconnect($con);
						die;
					}
				} else {
					if ($smn_variable_set_allocation == 1) {
						if ($txt_return_qnty > $allocated_qnty) {
							echo "20**Return quantity is not available.\nAvailable=" . $allocated_qnty;
							disconnect($con);
							die;
						}
					} else {
						if ($txt_return_qnty > $available_qnty) {
							echo "20**Return quantity is not available.\nAvailable=" . $available_qnty;
							disconnect($con);
							die;
						}
					}
				}
			} else {
				if ($txt_return_qnty > $available_qnty) {
					echo "20**Return quantity is not available.\nAvailable=" . $available_qnty;
					disconnect($con);
					die;
				}
			}
		} else {
			if ($txt_return_qnty > $available_qnty) {
				echo "20**Return quantity is not available.\nAvailable=" . $available_qnty;
				disconnect($con);
				die;
			}
		}

		if ($variable_set_allocation == 1) {
			if ($receive_basis == 2 && $receive_purpose == 2) {
				if ($is_with_order == 41 || $is_with_order == 125 || $is_with_order == 135) {
					$allocated_qnty = $allocated_qnty - $txt_return_qnty;
					$available_qnty = $available_qnty;
				} else {
					if ($smn_variable_set_allocation == 1) {
						$allocated_qnty = $allocated_qnty - $txt_return_qnty;
						$available_qnty = $available_qnty;
					} else {
						$allocated_qnty = $allocated_qnty;
						$available_qnty = $available_qnty - $txt_return_qnty;
					}
				}
			} else {
				$allocated_qnty = $allocated_qnty;
				$available_qnty = $available_qnty - $txt_return_qnty;
			}
		} else {
			$allocated_qnty = $allocated_qnty;
			$available_qnty = $available_qnty - $txt_return_qnty;
		}

		$nowStock 		= $presentStock - $txt_return_qnty;
		$nowStockValue 	= $presentStockValue - $cons_amount;
		$nowAvgRate = 0;
		if ($nowStockValue != 0 && $nowStock != 0) $nowAvgRate	= number_format($nowStockValue / $nowStock, 10, ".", "");

		$field_array_prod = "last_issued_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$data_array_prod = $txt_return_qnty . "*" . $nowStock . "*" . $nowStockValue . "*" . $allocated_qnty . "*" . $available_qnty . "*'" . $user_id . "'*'" . $pc_date_time . "'";

		// if receive return from issue return order rate is calculated (Issue Return -> Issue -> Receive) in receive currency
		if ($rec_entry_form == 9) {
			$issue_ids = $receive_info[0][csf("issue_id")];
			$issue_receive_ids = sql_select("select d.order_rate,e.exchange_rate 
			from inv_transaction b, inv_mrr_wise_issue_details a left join inv_transaction d on a.recv_trans_id=d.id and d.status_active=1 and d.transaction_type=1 left join inv_receive_master e on e.id=d.mst_id
			where b.id=a.issue_trans_id and b.mst_id in($issue_ids) and b.prod_id=$txt_prod_id and a.status_active=1 and b.status_active=1 and b.transaction_type=2");
			$order_rate = 0;
			foreach ($issue_receive_ids as $issue_receive_row) {
				if ($issue_receive_row[csf("order_rate")] != "" || $issue_receive_row[csf("order_rate")] > 0) {
					$order_rate = $issue_receive_row[csf("order_rate")];
					$exchange_rate = $issue_receive_row[csf("exchange_rate")];
				}
			}
		}

		//transaction table insert here END ---------------------------------//
		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		//if LIFO/FIFO then START -----------------------------------------//
		$field_array = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*grey_quantity*updated_by*update_date";
		$mrr_rate = 0;
		$data_array = "";
		$updateID_array = array();
		$update_data = array();
		$issueQnty = $txt_return_qnty;

		$isLIFOfifo = return_field_value("store_method", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=17");
		if ($isLIFOfifo == 2) $cond_lifofifo = " DESC";
		else $cond_lifofifo = " ASC";
		$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount, grey_quantity from inv_transaction where prod_id=$txt_prod_id and mst_id=$txt_received_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=1 order by transaction_date $cond_lifofifo");

		foreach ($sql as $result) {
			$issue_trans_id = $result[csf("id")]; // this row will be updated
			$balance_qnty = $result[csf("balance_qnty")];
			$balance_amount = $result[csf("balance_amount")];
			$mrr_rate = $result[csf("cons_rate")];
			$issueQntyBalance = $balance_qnty - $issueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount - ($issueQnty * $mrr_rate);

			//for grey qtn
			$grey_quantity = $result[csf("grey_quantity")];
			$proportionate_grey_qty = $grey_quantity - (($grey_quantity * $issueQnty) / $balance_qnty);
			$update_grey_qty = number_format($proportionate_grey_qty);

			if ($issueQntyBalance >= 0) {
				$amount = $issueQnty * $mrr_rate;
				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if ($data_array != "") $data_array .= ",";
				$data_array .= "(" . $mrrWiseIsID . "," . $issue_trans_id . "," . $transactionID . ",8," . $txt_prod_id . "," . $issueQnty . "," . number_format($mrr_rate, 10, '.', '') . "," . number_format($amount, 8, '.', '') . ",'" . $user_id . "','" . $pc_date_time . "')";
				//for update
				$updateID_array[] = $issue_trans_id;
				$update_data[$issue_trans_id] = explode("*", ("" . $issueQntyBalance . "*" . $issueStockBalance . "*" . $update_grey_qty . "*'" . $user_id . "'*'" . $pc_date_time . "'"));
				break;
			} else if ($issueQntyBalance < 0) {

				$issueQntyBalance  = $issueQnty - $balance_qnty;
				$issueQnty = $balance_qnty;
				$amount = $issueQnty * $mrr_rate;

				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if ($data_array != "") $data_array .= ",";
				$data_array .= "(" . $mrrWiseIsID . "," . $issue_trans_id . "," . $transactionID . ",8," . $txt_prod_id . "," . $issueQnty . "," . number_format($mrr_rate, 10, '.', '') . "," . number_format($amount, 8, '.', '') . ",'" . $user_id . "','" . $pc_date_time . "')";
				//for update
				$updateID_array[] = $issue_trans_id;
				$update_data[$issue_trans_id] = explode("*", ("0*0*0*'" . $user_id . "'*'" . $pc_date_time . "'"));
				$issueQnty = $issueQntyBalance;
			}
		}
		//end foreach

		//for order wise return qty
		$data_proportionate = '';
		if ($hdnReturnString != '') {
			/*
			|--------------------------------------------------------------------------
			| for allocation
			|--------------------------------------------------------------------------
			|
			*/
			$sqlAllocation = sql_select("SELECT a.id AS ID, a.qnty AS MST_QNTY, a.qnty_break_down AS QNTY_BREAK_DOWN, b.id AS DTLS_ID, b.qnty AS DTLS_QNTY, b.po_break_down_id AS PO_BREAK_DOWN_ID FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id INNER JOIN inv_transaction c ON b.job_no = c.job_no AND b.item_id = c.prod_id WHERE a.item_id = " . $txt_prod_id . " AND a.is_dyied_yarn = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active=1 AND b.is_deleted=0 AND c.prod_id = " . $txt_prod_id . " and c.mst_id = " . $txt_received_id . " and c.transaction_type in (1) AND c.item_category=1 AND c.status_active = 1 AND c.is_deleted = 0");
			$qnty_breakdown = '';
			$return_qnty = 0;
			$allocationDataArr = array();
			$updateDtlsIdArr = array();
			$data_allocation_dtls = array();
			foreach ($sqlAllocation as $row) {
				$allocation_id = $row['ID'];
				$allocation_qnty = $row['MST_QNTY'];
				$allocationDataArr[$row['PO_BREAK_DOWN_ID']]['dtls_id'] = $row['DTLS_ID'];
				$allocationDataArr[$row['PO_BREAK_DOWN_ID']]['qnty'] = $row['DTLS_QNTY'];

				$expQty = explode(',', $row['QNTY_BREAK_DOWN']);
				foreach ($expQty as $key => $val) {
					$expVal = explode('_', $val);
					$mst_data[$expVal[1]]['mst_qty'] = $expVal[0];
					$mst_data[$expVal[1]]['job_no'] = $expVal[2];
				}
			}

			/*
			|--------------------------------------------------------------------------
			| for order wise return
			|--------------------------------------------------------------------------
			|
			*/
			$field_proportionate = "id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,issue_purpose,inserted_by,insert_date";
			$expReturnString = explode(",", $hdnReturnString);

			foreach ($expReturnString as $returnString) {
				$id_proportionate = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$returnData = explode("_", $returnString);
				if ($data_proportionate != '') {
					$data_proportionate .= ',';
				}
				//hdnReturnString += hdnOrderNo+'_'+orderReturnQty+'_'+originalOrderRtnQty;	
				$data_proportionate .= "(" . $id_proportionate . "," . $transactionID . ",3,8," . $returnData[0] . "," . $txt_prod_id . "," . $returnData[1] . ",2," . $user_id . ",'" . $pc_date_time . "')";

				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_dtls
				| data preparing for
				| $data_allocation_dtls
				|--------------------------------------------------------------------------
				|
				*/
				if (!empty($allocationDataArr[$returnData[0]])) {
					$qnty = $allocationDataArr[$returnData[0]]['qnty'] - $returnData[1];
					$dtlsId = $allocationDataArr[$returnData[0]]['dtls_id'];
					$updateDtlsIdArr[] = $dtlsId;
					$data_allocation_dtls[$dtlsId] = explode("*", ("" . $qnty . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));

					//for inv_material_allocation_mst
					if ($qnty_breakdown != '') {
						$qnty_breakdown .= ',';
					}
					$mstQty = $mst_data[$returnData[0]]['mst_qty'] - $returnData[1];
					$qnty_breakdown .= $mstQty . '_' . $returnData[0] . '_' . $mst_data[$returnData[0]]['job_no'];
					$return_qnty += $returnData[1];
				}
			}
		}
		//for order wise return qty end


		if (str_replace("'", "", $txt_return_no) != "") {
			$new_return_number[0] = str_replace("'", "", $txt_return_no);
			$id = str_replace("'", "", $txt_return_id);
			//yarn master table UPDATE here START----------------------//
			$field_array_mst = "entry_form*company_id*supplier_id*issue_date*received_id*received_mrr_no*pi_id*remarks*updated_by*update_date";
			$data_array_mst = "8*" . $cbo_company_id . "*" . $cbo_return_to . "*" . $txt_return_date . "*" . $txt_received_id . "*" . $txt_mrr_no . "*'" . $pi_id . "'*" . $txt_remarks . "*'" . $user_id . "'*'" . $pc_date_time . "'";
			//yarn master table UPDATE here END---------------------------------------//
		} else {
			//yarn master table entry here START---------------------------------------//
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);

			if ($db_type == 0) $year_cond = "YEAR(insert_date)";
			else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
			else $year_cond = ""; //defined Later

			$new_return_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con, 1, $cbo_company_id, 'YRR', 8, date("Y", time()), 1));

			$field_array_mst = "id, issue_number_prefix, issue_number_prefix_num, issue_number, entry_form, item_category, company_id, supplier_id, issue_date, received_id, pi_id, received_mrr_no, remarks, inserted_by, insert_date";
			$data_array_mst = "(" . $id . ",'" . $new_return_number[1] . "','" . $new_return_number[2] . "','" . $new_return_number[0] . "',8,1," . $cbo_company_id . "," . $cbo_return_to . "," . $txt_return_date . "," . $txt_received_id . ",'" . $pi_id . "'," . $txt_mrr_no . "," . $txt_remarks . ",'" . $user_id . "','" . $pc_date_time . "')";
			//yarn master table entry here END---------------------------------------//
		}


		$field_array_trans = "id, mst_id, company_id, supplier_id, prod_id, origin_prod_id, item_category, transaction_type, transaction_date, no_of_bags, cone_per_bag, store_id, floor_id, room, rack, self, bin_box, order_rate, order_ile_cost, cons_uom, cons_quantity, cons_rate, cons_amount, inserted_by,insert_date,pi_wo_batch_no,rcv_rate,rcv_amount,buyer_id,store_rate,store_amount";
		$data_array_trans = "(" . $transactionID . "," . $id . "," . $cbo_company_id . "," . $cbo_return_to . "," . $txt_prod_id . ",'" . $origin_prod_id . "',1,3," . $txt_return_date . "," . $txt_no_of_bag . "," . $txt_no_of_cone . "," . $cbo_store_name . "," . $cbo_floor . "," . $cbo_room . "," . $txt_rack . "," . $txt_shelf . "," . $cbo_bin . "," . number_format($order_rate, 10, '.', '') . "," . $order_ile_cost . "," . $cbo_uom . "," . $txt_issue_qnty . ",'" . number_format($cons_rate, 10, '.', '') . "','" . number_format($cons_amount, 8, '.', '') . "','" . $user_id . "','" . $pc_date_time . "','" . $pi_id . "','" . number_format($order_rate, 10, '.', '') . "','" . number_format($order_amt, 8, '.', '') . "'," . $cbo_buyer_name . "," . number_format($store_item_rate, 10, '.', '') . "," . number_format($issue_store_value, 8, '.', '') . ")";

		$store_up_id = 0;
		if ($variable_store_wise_rate == 1) {
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$txt_prod_id and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");

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

				$field_array_store = "last_issued_qnty*cons_qty*amount*updated_by*update_date";
				$currentStock_store		= $store_presentStock - $txt_issue_qnty;
				$currentValue_store		= $store_presentStockValue - $issue_store_value;
				$data_array_store = "" . $txt_issue_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'";
			}
		}

		$mrrWiseIssueID = $storeRID = true;
		$upTrID = true;
		if (str_replace("'", "", $txt_return_no) != "") {
			$rID = sql_update("inv_issue_master", $field_array_mst, $data_array_mst, "id", $id, 1);
		} else {
			$rID = sql_insert("inv_issue_master", $field_array_mst, $data_array_mst, 1);
		}

		$transID = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 1);

		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;

		$prodUpdate = sql_update("product_details_master", $field_array_prod, $data_array_prod, "id", $txt_prod_id, 1);
		$mrrWiseIssueID = sql_insert("inv_mrr_wise_issue_details", $field_array, $data_array, 1);
		$upTrID = execute_query(bulk_update_sql_statement("inv_transaction", "id", $update_array, $update_data, $updateID_array));

		if ($store_up_id > 0 && $variable_store_wise_rate == 1) {
			$storeRID = sql_update("inv_store_wise_yarn_qty_dtls", $field_array_store, $data_array_store, "id", $store_up_id, 1);
		}

		//if LIFO/FIFO then END -----------------------------------------//

		//for order wise return qty
		$proportionateID = true;
		$allocation_mst_update = true;
		$allocation_dtls_update = true;

		if ($data_proportionate != '') {
			$proportionateID = sql_insert("order_wise_pro_details", $field_proportionate, $data_proportionate, 1);

			/*
			|--------------------------------------------------------------------------
			| inv_material_allocation_mst
			| data preparing and updating
			|--------------------------------------------------------------------------
			|
			*/
			$field_allocation = "qnty*qnty_break_down*updated_by*update_date";
			$allocationQnty = $allocation_qnty - $return_qnty;
			$data_allocation = "" . $allocationQnty . "*'" . $qnty_breakdown . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
			if (!empty($sqlAllocation)) {
				$allocation_mst_update = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", "" . $allocation_id . "", 0);
			}

			/*
			|--------------------------------------------------------------------------
			| inv_material_allocation_dtls
			| data updating
			|--------------------------------------------------------------------------
			|
			*/
			if (!empty($sqlAllocation) && $allocation_mst_update) {
				$field_allocation_dtls = "qnty*updated_by*update_date";
				$allocation_dtls_update = execute_query(bulk_update_sql_statement("inv_material_allocation_dtls", "id", $field_allocation_dtls, $data_allocation_dtls, $updateDtlsIdArr));
			}
		}

		//echo "10**insert into order_wise_pro_details (".$field_proportionate.") values ".$data_proportionate;die;

		//oci_rollback($con);
		//echo "10**".$rID." && ".$transID." && ".$prodUpdate." && ".$mrrWiseIssueID." && ".$upTrID." && ".$proportionateID." && ".$allocation_mst_update." && ".$allocation_dtls_update." && ".$storeRID;oci_rollback($con);disconnect($con);die;		

		if ($db_type == 0) {
			if ($rID && $transID && $prodUpdate && $mrrWiseIssueID && $upTrID && $proportionateID && $allocation_mst_update && $allocation_dtls_update && $storeRID) {
				mysql_query("COMMIT");
				echo "0**" . $new_return_number[0] . "**" . $id;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $new_return_number[0] . "**" . $id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $transID && $prodUpdate && $mrrWiseIssueID && $upTrID && $proportionateID && $allocation_mst_update && $allocation_dtls_update && $storeRID) {
				oci_commit($con);
				echo "0**" . $new_return_number[0] . "**" . $id;
			} else {
				oci_rollback($con);
				echo "10**" . $new_return_number[0] . "**" . $id;
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	} 
	else if ($operation == 1) // Update Here----------------------------------------------------------
	{
		//****************************************** BEFORE ENTRY ADJUST START *****************************************//
		//product master table information
		//before stock update
		$sql = sql_select("select a.id,a.avg_rate_per_unit,a.current_stock,a.stock_value, a.allocated_qnty, a.available_qnty, b.cons_quantity, b.cons_amount, b.store_amount from product_details_master a, inv_transaction b where a.id=b.prod_id and b.id=$update_id and a.item_category_id=1 and b.item_category=1 and b.transaction_type=3");
		$before_prod_id = $before_issue_qnty = $before_stock_qnty = $before_stock_value = $before_available_qnty = $before_allocated_qnty = 0;
		foreach ($sql as $result) {
			$before_prod_id 	= $result[csf("id")];
			$before_stock_qnty 	= $result[csf("current_stock")];
			$before_stock_value = $result[csf("stock_value")];
			$before_available_qnty	= $result[csf("available_qnty")];
			$before_allocated_qnty	= $result[csf("allocated_qnty")];
			//before quantity and stock value
			$before_issue_qnty	= $result[csf("cons_quantity")];
			$before_issue_value	= $result[csf("cons_amount")];
			$before_store_amount	= $result[csf("store_amount")];
		}

		//current product ID
		$txt_prod_id = str_replace("'", "", $txt_prod_id);
		$txt_return_qnty = str_replace("'", "", $txt_return_qnty);
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value,available_qnty,allocated_qnty from product_details_master where id=$txt_prod_id and item_category_id=1");
		$curr_avg_rate = $curr_stock_qnty = $curr_stock_value = 0;
		foreach ($sql as $result) {
			$curr_avg_rate 		= $result[csf("avg_rate_per_unit")];
			$curr_stock_qnty 	= $result[csf("current_stock")];

			$curr_stock_value 	= $result[csf("stock_value")];
			$available_qnty		= $result[csf("available_qnty")];
			$allocated_qnty 	= $result[csf("allocated_qnty")];
		}

		$receive_info = sql_select("select a.receive_purpose,a.receive_basis,a.issue_id,a.entry_form,a.booking_id,a.exchange_rate,b.order_rate, b.transaction_type from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.id=$txt_received_id and b.prod_id=$txt_prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(1,4,5)");

		$receive_purpose = $receive_info[0][csf("receive_purpose")];
		$receive_basis = $receive_info[0][csf("receive_basis")];
		$rec_entry_form = $receive_info[0][csf("entry_form")];
		$booking_id = $receive_info[0][csf("booking_id")];
		$exchange_rate = $receive_info[0][csf("exchange_rate")];
		$order_rate = $receive_info[0][csf("order_rate")];
		$transaction_type = $receive_info[0][csf("transaction_type")];

		$is_with_order = return_field_value("entry_form", "wo_yarn_dyeing_mst", " status_active=1 and id=$booking_id", "entry_form");

		if ($variable_set_allocation == 1) {
			if ($receive_basis == 2 && $receive_purpose == 2) {
				if ($is_with_order == 41 || $is_with_order == 125 || $is_with_order == 135) {
					if (($txt_return_qnty - $before_issue_qnty) > $allocated_qnty) {
						echo "20**Return quantity is not available.\nAvailable=" . $allocated_qnty;
						disconnect($con);
						die;
					}
				} else {
					if ($smn_variable_set_allocation == 1) {
						if (($txt_return_qnty - $before_issue_qnty) > $allocated_qnty) {
							echo "20**Return quantity is not available.\nAvailable=" . $allocated_qnty;
							disconnect($con);
							die;
						}
					} else {
						if (($txt_return_qnty - $before_issue_qnty) > $available_qnty) {
							echo "20**Return quantity is not available.\nAvailable=" . $available_qnty;
							disconnect($con);
							die;
						}
					}
				}
			} else {
				if (($txt_return_qnty - $before_issue_qnty) > $available_qnty) {
					echo "20**Return quantity is not available.\nAvailable=" . $available_qnty;
					disconnect($con);
					die;
				}
			}
		} else {
			if (($txt_return_qnty - $before_issue_qnty) > $available_qnty) {
				echo "20**Return quantity is not available.\nAvailable=" . $available_qnty;
				disconnect($con);
				die;
			}
		}

		//product master table data UPDATE START----------------------//
		$update_array_prod = "last_issued_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$field_array_store = "last_issued_qnty*cons_qty*amount*updated_by*update_date";
		if ($before_prod_id == $txt_prod_id) {
			$adj_stock_qnty = $curr_stock_qnty + $before_issue_qnty - $txt_return_qnty; // CurrentStock + Before Issue Qnty - Current Issue Qnty
			$adj_stock_val  = $curr_stock_value + $before_issue_value - ($txt_return_qnty * $curr_avg_rate); // CurrentStockValue + Before Issue Value - Current Issue Value

			if ($adj_stock_qnty == 0) {
				$adj_avgrate = number_format($curr_avg_rate, 10, '.', '');
			} else {
				$adj_avgrate = number_format($adj_stock_val / $adj_stock_qnty, 10, '.', '');
			}

			if ($adj_stock_qnty < 0) //Aziz
			{
				echo "30**Stock cannot be less than zero.";
				disconnect($con);
				die;
			}

			if ($variable_set_allocation == 1) {
				if ($receive_basis == 2 && $receive_purpose == 2) {
					if ($is_with_order == 41 || $is_with_order == 125 || $is_with_order == 135) {
						$adj_allocated_qnty = $allocated_qnty + $before_issue_qnty - $txt_return_qnty;
						$adj_beforeAvailableQnty = $available_qnty;
					} else {

						if ($smn_variable_set_allocation == 1) {
							$adj_allocated_qnty = $allocated_qnty + $before_issue_qnty - $txt_return_qnty;
							$adj_beforeAvailableQnty = $available_qnty;
						} else {
							$adj_allocated_qnty = $allocated_qnty;
							$adj_beforeAvailableQnty = $available_qnty + $before_issue_qnty - $txt_return_qnty;
						}
					}
				} else {
					$adj_allocated_qnty = $allocated_qnty;
					$adj_beforeAvailableQnty = $available_qnty + $before_issue_qnty - $txt_return_qnty;
				}
			} else {
				$adj_allocated_qnty = $allocated_qnty;
				$adj_beforeAvailableQnty = $available_qnty + $before_issue_qnty - $txt_return_qnty;
			}

			$data_array_prod = $txt_return_qnty . "*" . $adj_stock_qnty . "*" . number_format($adj_stock_val, 8, '.', '') . "*" . $adj_allocated_qnty . "*" . $adj_beforeAvailableQnty . "*'" . $user_id . "'*'" . $pc_date_time . "'";

			//now current stock
			$curr_avg_rate 		= $adj_avgrate;
			$curr_stock_qnty 	= $adj_stock_qnty;
			$curr_stock_value 	= $adj_stock_val;

			$store_up_id = 0;
			if ($variable_store_wise_rate == 1) {
				$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$txt_prod_id and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");

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

					if ($store_up_id) {
						$adj_beforeStock_store			= $store_presentStock + $before_issue_qnty;
						$adj_beforeStockValue_store		= $store_presentStockValue + $before_store_amount;

						$currentStock_store		= $adj_beforeStock_store - $txt_return_qnty;
						$currentValue_store		= $adj_beforeStockValue_store - $issue_store_value;
						$updateID_Storeprod[] = $store_up_id;
						$data_array_store[$store_up_id] = explode("*", ("" . $txt_return_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					}
				}
			}
		} else {
			if ($variable_set_allocation == 1) {
				if ($receive_purpose == 2) {
					if ($is_with_order == 41 || $is_with_order == 125 || $is_with_order == 135) {
						$adj_allocated_qnty = $before_allocated_qnty + $before_issue_qnty;
						$adj_beforeAvailableQnty = $before_available_qnty;

						$allocated_qnty = $allocated_qnty - $txt_return_qnty;
						$available_qnty = $available_qnty;
					} else {
						if ($smn_variable_set_allocation == 1) {
							$adj_allocated_qnty = $before_allocated_qnty + $before_issue_qnty;
							$adj_beforeAvailableQnty = $before_available_qnty;

							$allocated_qnty = $allocated_qnty - $txt_return_qnty;
							$available_qnty = $available_qnty;
						} else {
							$adj_allocated_qnty = $before_allocated_qnty;
							$adj_beforeAvailableQnty = $before_available_qnty + $before_issue_qnty;

							$allocated_qnty = $allocated_qnty;
							$available_qnty = $available_qnty - $txt_return_qnty;
						}
					}
				} else {
					$adj_allocated_qnty = $before_allocated_qnty;
					$adj_beforeAvailableQnty = $before_available_qnty + $before_issue_qnty;

					$allocated_qnty = $allocated_qnty;
					$available_qnty = $available_qnty - $txt_return_qnty;
				}
			} else {
				$adj_allocated_qnty = $before_allocated_qnty;
				$adj_beforeAvailableQnty = $before_available_qnty + $before_issue_qnty;

				$allocated_qnty = $allocated_qnty;
				$available_qnty = $available_qnty - $txt_return_qnty;
			}
			$updateIdprod_array = $update_dataProd = array();
			//before product adjust
			$adj_before_stock_qnty 	= $before_stock_qnty + $before_issue_qnty; // CurrentStock + Before Issue Qnty
			$adj_before_stock_val  	= $before_stock_value + $before_issue_value; // CurrentStockValue + Before Issue Value
			$adj_before_avgrate = 0;
			if ($adj_before_stock_val != 0 && $adj_before_stock_qnty != 0) $adj_before_avgrate = number_format($adj_before_stock_val / $adj_before_stock_qnty, 10, '.', '');
			if ($adj_before_stock_qnty < 0) //Aziz
			{
				echo "30**Stock cannot be less than zero.";
				disconnect($con);
				die;
			}
			$updateIdprod_array[] = $before_prod_id;
			$update_dataProd[$before_prod_id] = explode("*", ("" . $txt_return_qnty . "*" . $adj_before_stock_qnty . "*" . number_format($adj_before_stock_val, 8, '.', '') . "*" . $adj_allocated_qnty . "*" . $adj_beforeAvailableQnty . "*'" . $user_id . "'*'" . $pc_date_time . "'"));

			//current product adjust
			$adj_curr_stock_qnty = 	$curr_stock_qnty - $txt_return_qnty; // CurrentStock + Before Issue Qnty
			$adj_curr_stock_val  = 	$curr_stock_value - ($txt_return_qnty * $curr_avg_rate); // CurrentStockValue + Before Issue Value
			$adj_curr_avgrate = 0;
			if ($adj_curr_stock_val != 0 && $adj_curr_stock_qnty != 0) $adj_curr_avgrate =	number_format($adj_curr_stock_val / $adj_curr_stock_qnty, 10, '.', '');

			$updateIdprod_array[] = $txt_prod_id;
			$update_dataProd[$txt_prod_id] = explode("*", ("" . $txt_return_qnty . "*" . $adj_curr_stock_qnty . "*" . number_format($adj_curr_stock_val, 8, '.', '') . "*" . $allocated_qnty . "*" . $available_qnty . "*'" . $user_id . "'*'" . $pc_date_time . "'"));

			//now current stock
			$curr_avg_rate 		= $adj_curr_avgrate;
			$curr_stock_qnty 	= $adj_curr_stock_qnty;
			$curr_stock_value 	= $adj_curr_stock_val;

			$store_up_id = 0;
			if ($variable_store_wise_rate == 1) {
				$sql_store_before = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$before_prod_id and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");

				if (count($sql_store_before) < 1) {
					echo "20**No Data Found.";
					disconnect($con);
					die;
				} elseif (count($sql_store_before) > 1) {
					echo "20**Duplicate Product is Not Allow in Same REF Number.";
					disconnect($con);
					die;
				} else {
					$store_presentStock = $store_presentStockValue = $store_presentAvgRate = 0;
					foreach ($sql_store_before as $result) {
						$store_up_id = $result[csf("id")];
						$store_presentStock	= $result[csf("current_stock")];
						$store_presentStockValue = $result[csf("stock_value")];
						$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
					}
					if ($store_up_id) {
						$currentStock_store		= $store_presentStock + $before_issue_qnty;
						$currentValue_store		= $store_presentStockValue + $before_store_amount;
						$updateID_Storeprod[]	= $store_up_id;
						$data_array_store[$store_up_id] = explode("*", ("" . $before_issue_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					}
				}

				$sql_store_after = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$txt_prod_id and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");
				$store_up_id = 0;
				if (count($sql_store_after) < 1) {
					echo "20**No Data Found.";
					disconnect($con);
					die;
				} elseif (count($sql_store_after) > 1) {
					echo "20**Duplicate Product is Not Allow in Same REF Number.";
					disconnect($con);
					die;
				} else {
					$store_presentStock = $store_presentStockValue = $store_presentAvgRate = 0;
					foreach ($sql_store_after as $result) {
						$store_up_id = $result[csf("id")];
						$store_presentStock	= $result[csf("current_stock")];
						$store_presentStockValue = $result[csf("stock_value")];
						$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
					}

					if ($store_up_id) {
						$currentStock_store		= $store_presentStock - $txt_return_qnty;
						$currentValue_store		= $store_presentStockValue - $issue_store_value;
						$updateID_Storeprod[] = $store_up_id;
						$data_array_store[$store_up_id] = explode("*", ("" . $txt_return_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					}
				}
			}
		}
		//------------------ product_details_master END--------------//
		//weighted and average rate END here-------------------------//
		$trans_data_array = array();
		//transaction table START--------------------------//
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_id and b.entry_form=8 and a.item_category=1");
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

		$query2 = true;
		$query3 = true;
		//############## SAVE POINT START  ###################
		if ($db_type == 0) {
			$savepoint = "updatesql";
			mysql_query("SAVEPOINT $savepoint");
		}
		//############## SAVE POINT END    ###################

		$id = str_replace("'", "", $txt_return_id);
		//yarn master table UPDATE here START----------------------//
		$field_array_mst = "entry_form*issue_date*received_id*remarks*updated_by*update_date";
		$data_array_mst = "8*" . $txt_return_date . "*" . $txt_received_id . "*" . $txt_remarks . "*'" . $user_id . "'*'" . $pc_date_time . "'";
		//yarn master table UPDATE here END---------------------------------------//

		/******** original product id check start ********/
		$origin_prod_id = return_field_value("origin_prod_id", "inv_transaction", "prod_id=$txt_prod_id and status_active=1 and mst_id=$txt_received_id and transaction_type in(1,4,5) and item_category=1", "origin_prod_id");
		/******** original product id check end ********/

		// if receive return from issue return order rate is calculated (Issue Return -> Issue -> Receive) in receive currency
		if ($rec_entry_form == 9) {
			$issue_ids = $receive_info[0][csf("issue_id")];
			$issue_receive_ids = sql_select("select d.order_rate,e.exchange_rate 
			from inv_transaction b, inv_mrr_wise_issue_details a left join inv_transaction d on a.recv_trans_id=d.id and d.status_active=1 and d.transaction_type=1 left join inv_receive_master e on e.id=b.mst_id and e.item_category=1
			where b.id=a.issue_trans_id and b.mst_id in($issue_ids) and a.status_active=1 and b.status_active=1 and b.transaction_type=2");
			$order_rate = 0;
			foreach ($issue_receive_ids as $issue_receive_row) {
				if ($issue_receive_row[csf("order_rate")] != "" || $issue_receive_row[csf("order_rate")] > 0) {
					$order_rate = $issue_receive_row[csf("order_rate")];
					$exchange_rate = $issue_receive_row[csf("exchange_rate")];
				}
			}
		}

		//transaction table insert here START--------------------------------//
		$avg_rate_amount = $txt_return_qnty * $curr_avg_rate;
		if ($curr_avg_rate != 0) $cons_rate = $curr_avg_rate;
		else $cons_rate = $order_rate * $exchange_rate;
		$cons_amount = str_replace("'", "", $txt_issue_qnty) * $cons_rate;
		$order_amt = str_replace("'", "", $txt_return_qnty) * $order_rate;

		$field_array_trans = "company_id*supplier_id*prod_id*origin_prod_id*item_category*transaction_type*transaction_date*no_of_bags*cone_per_bag*store_id*floor_id*room*rack*self*bin_box*order_rate*order_ile_cost*cons_uom*cons_quantity*cons_rate*cons_amount*updated_by*update_date*pi_wo_batch_no*rcv_rate*rcv_amount*buyer_id*store_rate*store_amount";
		$data_array_trans = "" . $cbo_company_id . "*" . $cbo_return_to . "*" . $txt_prod_id . "*'" . $origin_prod_id . "'*1*3*" . $txt_return_date . "*" . $txt_no_of_bag . "*" . $txt_no_of_cone . "*" . $cbo_store_name . "*" . $cbo_floor . "*" . $cbo_room . "*" . $txt_rack . "*" . $txt_shelf . "*" . $cbo_bin . "*" . number_format($order_rate, 10, '.', '') . "*" . $order_ile_cost . "*" . $cbo_uom . "*" . $txt_return_qnty . "*'" . number_format($store_item_rate, 10, '.', '') . "'*'" . number_format($issue_store_value, 8, '.', '') . "'*'" . $user_id . "'*'" . $pc_date_time . "'*'" . $pi_id . "'*" . number_format($store_item_rate, 4, '.', '') . "*" . number_format($issue_store_value, 4, '.', '') . "*" . $cbo_buyer_name . "*" . number_format($store_item_rate, 10, '.', '') . "*" . number_format($issue_store_value, 8, '.', '') . "";
		//transaction table insert here END ---------------------------------//

		//if LIFO/FIFO then START -----------------------------------------//
		$field_array = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$updateTrans_array = "balance_qnty*balance_amount*grey_quantity*updated_by*update_date";
		$mrr_rate = 0;
		$data_array = "";
		$updateIDtrans_array = array();
		$update_dataTrans = array();
		$issueQnty = $txt_return_qnty;

		$isLIFOfifo = return_field_value("store_method", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=17");
		if ($isLIFOfifo == 2) $cond_lifofifo = " DESC";
		else $cond_lifofifo = " ASC";

		//if($before_prod_id==$txt_prod_id) $balance_cond=" and( balance_qnty>0 or id=$update_id)";
		//else $balance_cond=" and balance_qnty>0";
		$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount, grey_quantity from inv_transaction where prod_id=$txt_prod_id and mst_id=$txt_received_id $balance_cond and transaction_type in (1,4,5) and item_category=1 order by transaction_date $cond_lifofifo");

		foreach ($sql as $result) {
			$issue_trans_id = $result[csf("id")]; // this row will be updated
			if ($trans_data_array[$issue_trans_id]['qnty'] == "") {
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
			} else {
				$balance_qnty = $trans_data_array[$issue_trans_id]['qnty'];
				$balance_amount = $trans_data_array[$issue_trans_id]['amnt'];
			}

			$mrr_rate = $result[csf("cons_rate")];
			$issueQntyBalance = $balance_qnty - $issueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount - ($issueQnty * $mrr_rate);

			//for grey qtn
			$grey_quantity = $result[csf("grey_quantity")];
			$proportionate_grey_qty = $grey_quantity - (($grey_quantity * $issueQnty) / $balance_qnty);
			$update_grey_qty = number_format($proportionate_grey_qty);

			//echo "10**".$balance_qnty."-".$issueQnty; die();

			if ($issueQntyBalance >= 0) {
				$amount = $issueQnty * $mrr_rate;
				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if ($data_array != "") $data_array .= ",";
				$data_array .= "(" . $mrrWiseIsID . "," . $issue_trans_id . "," . $update_id . ",8," . $txt_prod_id . "," . $issueQnty . "," . $mrr_rate . "," . $amount . ",'" . $user_id . "','" . $pc_date_time . "')";
				//for update
				$updateIDtrans_array[] = $issue_trans_id;
				$update_dataTrans[$issue_trans_id] = explode("*", ("" . $issueQntyBalance . "*" . $issueStockBalance . "*" . $update_grey_qty . "*'" . $user_id . "'*'" . $pc_date_time . "'"));
				break;
			} else if ($issueQntyBalance < 0) {
				$issueQntyBalance  = $issueQnty - $balance_qnty;
				$issueQnty = $balance_qnty;
				$amount = $issueQnty * $mrr_rate;

				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if ($data_array != "") $data_array .= ",";
				$data_array .= "(" . $mrrWiseIsID . "," . $issue_trans_id . "," . $update_id . ",8," . $txt_prod_id . "," . $issueQnty . "," . $mrr_rate . "," . $amount . ",'" . $user_id . "','" . $pc_date_time . "')";
				//echo "20**".$data_array;die;
				//for update
				$updateIDtrans_array[] = $issue_trans_id;
				$update_dataTrans[$issue_trans_id] = explode("*", ("0*0*0*'" . $user_id . "'*'" . $pc_date_time . "'"));
				$issueQnty = $issueQntyBalance;
			}
		} //end foreach

		//for order wise return qty
		$data_proportionate = '';
		if ($hdnReturnString != '') {
			/*
			|--------------------------------------------------------------------------
			| for allocation
			|--------------------------------------------------------------------------
			|
			*/
			if($transaction_type == 4) //issue return
			{
				$actual_receive_sql = sql_select("select b.RECV_TRANS_ID from INV_TRANSACTION a, INV_MRR_WISE_ISSUE_DETAILS b where a.mst_id = $hdn_issue_id and a.transaction_type = 2 and a.prod_id = $txt_prod_id  and a.id = b.ISSUE_TRANS_ID");
				$rcv_trans_id = $actual_receive_sql[0]['RECV_TRANS_ID'];
				$mst_id_arr=return_library_array( "select id, mst_id from inv_transaction where id = $rcv_trans_id",'id','mst_id');
				$received_id = $mst_id_arr[$rcv_trans_id];
			}
			else if($transaction_type == 1) //receive
			{
				$received_id = $txt_received_id;
			}
			$sqlAllocation = sql_select("SELECT a.id AS ID, a.qnty AS MST_QNTY, a.qnty_break_down AS QNTY_BREAK_DOWN, b.id AS DTLS_ID, b.qnty AS DTLS_QNTY, b.po_break_down_id AS PO_BREAK_DOWN_ID FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id INNER JOIN inv_transaction c ON b.job_no = c.job_no AND b.item_id = c.prod_id WHERE a.item_id = " . $txt_prod_id . " AND a.is_dyied_yarn = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active=1 AND b.is_deleted=0 AND c.prod_id = " . $txt_prod_id . " and c.mst_id = " . $received_id . " and c.transaction_type in (1) AND c.item_category=1 AND c.status_active = 1 AND c.is_deleted = 0");
			$qnty_breakdown = '';
			$update_qty_less = 0;
			$update_qty_add = 0;
			$allocationDataArr = array();
			$updateDtlsIdArr = array();
			$data_allocation_dtls = array();
			foreach ($sqlAllocation as $row) {
				$allocation_id = $row['ID'];
				$allocation_qnty = $row['MST_QNTY'];
				$allocationDataArr[$row['PO_BREAK_DOWN_ID']]['dtls_id'] = $row['DTLS_ID'];
				$allocationDataArr[$row['PO_BREAK_DOWN_ID']]['qnty'] = $row['DTLS_QNTY'];

				$expQty = explode(',', $row['QNTY_BREAK_DOWN']);
				foreach ($expQty as $key => $val) {
					$expVal = explode('_', $val);
					$mst_data[$expVal[1]]['mst_qty'] = $expVal[0];
					$mst_data[$expVal[1]]['job_no'] = $expVal[2];
				}
			}

			$sqlProportionate = sql_select("SELECT po_breakdown_id AS PO_BREAKDOWN_ID, quantity AS QUANTITY FROM order_wise_pro_details WHERE trans_id = " . $update_id . " AND prod_id = " . $txt_prod_id . " AND entry_form = 8 AND trans_type = 3");
			$proportiona_data = array();
			foreach ($sqlProportionate as $row) {
				$proportiona_data[$row['PO_BREAKDOWN_ID']]['qnty'] = $row['QUANTITY'];
			}

			/*
			|--------------------------------------------------------------------------
			| for order wise return
			|--------------------------------------------------------------------------
			|
			*/
			$field_proportionate = "id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,issue_purpose,inserted_by,insert_date";
			$expReturnString = explode(",", $hdnReturnString);
			foreach ($expReturnString as $returnString) {
				$id_proportionate = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$returnData = explode("_", $returnString);
				if ($data_proportionate != '')
					$data_proportionate .= ',';
				//hdnReturnString += hdnOrderNo+'_'+orderReturnQty+'_'+originalOrderRtnQty;	
				$data_proportionate .= "(" . $id_proportionate . "," . $update_id . ",3,8," . $returnData[0] . "," . $txt_prod_id . "," . $returnData[1] . ",2," . $user_id . ",'" . $pc_date_time . "')";

				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_dtls
				| data preparing for
				| $data_allocation_dtls
				|--------------------------------------------------------------------------
				|
				*/
				if (!empty($allocationDataArr[$returnData[0]])) {
					if ($proportiona_data[$returnData[0]]['qnty'] >= $returnData[1]) {
						$qnty = $allocationDataArr[$returnData[0]]['qnty'] - $returnData[1];
						$mstQty = $mst_data[$returnData[0]]['mst_qty'] + ($proportiona_data[$returnData[0]]['qnty'] - $returnData[1]);
						$update_qty_less += $proportiona_data[$returnData[0]]['qnty'] - $returnData[1];
					} else {
						$qnty = $allocationDataArr[$returnData[0]]['qnty'] + $returnData[1];
						$mstQty = $mst_data[$returnData[0]]['mst_qty'] - ($returnData[1] - $proportiona_data[$returnData[0]]['qnty']);
						$update_qty_add += $returnData[1] - $proportiona_data[$returnData[0]]['qnty'];
					}

					$dtlsId = $allocationDataArr[$returnData[0]]['dtls_id'];
					$updateDtlsIdArr[] = $dtlsId;
					$data_allocation_dtls[$dtlsId] = explode("*", ("" . $qnty . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));

					//for inv_material_allocation_mst
					if ($qnty_breakdown != '') {
						$qnty_breakdown .= ',';
					}
					$qnty_breakdown .= $mstQty . '_' . $returnData[0] . '_' . $mst_data[$returnData[0]]['job_no'];
				}
			}
		}
		//for order wise return qty end

		$mrrWiseIssueID = true;
		$upTrID = true;

		if ($before_prod_id == $txt_prod_id) {
			$query1 = sql_update("product_details_master", $update_array_prod, $data_array_prod, "id", $before_prod_id, 0);
		} else {
			$query1 = execute_query(bulk_update_sql_statement("product_details_master", "id", $update_array_prod, $update_dataProd, $updateIdprod_array), 0);
		}

		$query2 = execute_query(bulk_update_sql_statement("inv_transaction", "id", $update_array, $update_data, $updateID_array), 0);
		$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=8", 0);

		$rID = sql_update("inv_issue_master", $field_array_mst, $data_array_mst, "id", $id, 0);
		$transID = sql_update("inv_transaction", $field_array_trans, $data_array_trans, "id", $update_id, 0);
		$mrrWiseIssueID = sql_insert("inv_mrr_wise_issue_details", $field_array, $data_array, 0);
		$upTrID = execute_query(bulk_update_sql_statement("inv_transaction", "id", $updateTrans_array, $update_dataTrans, $updateIDtrans_array), 0);
		$storeRID = true;
		if (count($updateID_Storeprod) > 0 && $variable_store_wise_rate == 1) {
			$storeRID = execute_query(bulk_update_sql_statement("inv_store_wise_yarn_qty_dtls", "id", $field_array_store, $data_array_store, $updateID_Storeprod), 0);
		}

		//for order wise return qty
		$proportionateDelete = true;
		$proportionateID = true;
		$allocation_mst_update = true;
		$allocation_dtls_update = true;
		if ($data_proportionate != '') {
			$proportionateDelete = execute_query("DELETE FROM order_wise_pro_details WHERE trans_id=" . $update_id . " and entry_form=8 and trans_type = 3");
			$proportionateID = sql_insert("order_wise_pro_details", $field_proportionate, $data_proportionate, 1);

			/*
			|--------------------------------------------------------------------------
			| inv_material_allocation_mst
			| data preparing and updating
			|--------------------------------------------------------------------------
			|
			*/
			$field_allocation = "qnty*qnty_break_down*updated_by*update_date";
			$allocationQnty = ($allocation_qnty + $update_qty_less) - $update_qty_add;
			$data_allocation = "" . $allocationQnty . "*'" . $qnty_breakdown . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
			$allocation_mst_update = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", "" . $allocation_id . "", 0);

			/*
			|--------------------------------------------------------------------------
			| inv_material_allocation_dtls
			| data updating
			|--------------------------------------------------------------------------
			|
			*/
			if ($allocation_mst_update) {
				$field_allocation_dtls = "qnty*updated_by*update_date";
				$allocation_dtls_update = execute_query(bulk_update_sql_statement("inv_material_allocation_dtls", "id", $field_allocation_dtls, $data_allocation_dtls, $updateDtlsIdArr));
			}
		}

		// echo "10**".$query1 ."&&". $query2 ."&&". $query3 ."&&". $rID ."&&". $transID ."&&". $mrrWiseIssueID ."&&". $upTrID ."&&". $proportionateDelete ."&&". $proportionateID ."&&". $allocation_mst_update ."&&". $allocation_dtls_update ."&&". $storeRID; oci_rollback($con);disconnect($con);die();

		if ($db_type == 0) {
			if ($query1 && $query2 && $query3 && $rID && $transID && $mrrWiseIssueID && $upTrID && $proportionateDelete && $proportionateID && $allocation_mst_update && $allocation_dtls_update && $storeRID) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $txt_return_no) . "**" . $id;
			} else {
				mysql_query("ROLLBACK");
				mysql_query("ROLLBACK TO $savepoint");
				echo "10**" . str_replace("'", "", $txt_return_no) . "**" . $id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($query1 && $query2 && $query3 && $rID && $transID && $mrrWiseIssueID && $upTrID && $proportionateDelete && $proportionateID && $allocation_mst_update && $allocation_dtls_update && $storeRID) {
				oci_commit($con);
				echo "1**" . str_replace("'", "", $txt_return_no) . "**" . $id;
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_return_no) . "**" . $id;
			}
		}

		disconnect($con);
		die;
	} 
	else if ($operation == 2) // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$mrr_data = sql_select("select a.id, a.is_posted_account, b.cons_quantity, b.cons_rate, b.cons_amount, b.store_amount, c.id as prod_id, c.current_stock, c.stock_value, c.allocated_qnty, c.available_qnty 
		from inv_issue_master a, inv_transaction b, product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and b.transaction_type=3 and a.status_active=1 and b.status_active=1 and b.id=$update_id");
		$master_id = $mrr_data[0][csf("id")];
		$is_posted_account = $mrr_data[0][csf("is_posted_account")] * 1;
		$cons_quantity = $mrr_data[0][csf("cons_quantity")];
		$cons_rate = $mrr_data[0][csf("cons_rate")];
		$cons_amount = $mrr_data[0][csf("cons_amount")];
		$before_store_amount = $mrr_data[0][csf("store_amount")];
		$prod_id = $mrr_data[0][csf("prod_id")];
		$current_stock = $mrr_data[0][csf("current_stock")];
		$stock_value = $mrr_data[0][csf("stock_value")];
		$allocated_qnty = $mrr_data[0][csf("allocated_qnty")];
		$available_qnty = $mrr_data[0][csf("available_qnty")];

		$cu_current_stock = $current_stock + $cons_quantity;
		$cu_stock_value = $stock_value + $cons_amount;
		if ($cu_stock_value > 0 && $cu_current_stock > 0)
			$cu_avg_rate = $cu_stock_value / $cu_current_stock;
		else
			$cu_avg_rate = 0;

		$receive_info = sql_select("select receive_purpose,receive_basis,issue_id,entry_form,booking_id from inv_receive_master where id=$txt_received_id");
		$receive_purpose = $receive_info[0][csf("receive_purpose")];
		$receive_basis = $receive_info[0][csf("receive_basis")];
		$rec_entry_form = $receive_info[0][csf("entry_form")];
		$booking_id = $receive_info[0][csf("booking_id")];
		$is_with_order = return_field_value("entry_form", "wo_yarn_dyeing_mst", " status_active=1 and id=$booking_id", "entry_form");
		if ($variable_set_allocation == 1) {
			//$cu_allocated_qnty=$allocated_qnty+$cons_quantity;
			$cu_available_qnty = $available_qnty;
			if ($receive_basis == 2 && $receive_purpose == 2) {
				if ($is_with_order == 41 || $is_with_order == 125 || $is_with_order == 135) {
					$cu_allocated_qnty = $allocated_qnty + $cons_quantity;
					$cu_available_qnty = $available_qnty;
				} else {
					if ($smn_variable_set_allocation == 1) {
						$cu_allocated_qnty = $allocated_qnty + $cons_quantity;
						$cu_available_qnty = $available_qnty;
					} else {
						$cu_allocated_qnty = $allocated_qnty;
						$cu_available_qnty = $available_qnty + $cons_quantity;
					}
				}
			} else {
				$cu_allocated_qnty = $allocated_qnty;
				$cu_available_qnty = $available_qnty + $cons_quantity;
			}
		} else {
			$cu_allocated_qnty = $allocated_qnty;
			$cu_available_qnty = $available_qnty + $cons_quantity;
		}

		if ($is_posted_account > 0) {
			echo "13**Delete restricted, This Information is used in another Table.";
			disconnect($con);
			oci_rollback($con);
			die;
		}

		$next_operation = return_field_value("max(id) as max_trans_id", "inv_transaction", "status_active=1 and item_category=1 and transaction_type<>3 and prod_id=$prod_id", "max_trans_id");
		if ($next_operation) {
			if ($next_operation > str_replace("'", "", $update_id)) {
				echo "13**Delete restricted, This Information is used in another Table.";
				disconnect($con);
				oci_rollback($con);
				die;
			}
		}

		$field_array = "updated_by*update_date*status_active*is_deleted";
		$data_array = "'" . $user_id . "'*'" . $pc_date_time . "'*0*1";

		$field_array_prod = "current_stock*avg_rate_per_unit*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$data_array_prod = "" . $cu_current_stock . "*" . number_format($cu_avg_rate, 10, '.', '') . "*" . number_format($cu_stock_value, 8, '.', '') . "*'" . $cu_allocated_qnty . "'*'" . $cu_available_qnty . "'*'" . $user_id . "'*'" . $pc_date_time . "'";
		if ($variable_store_wise_rate == 1) {
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$prod_id and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");
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
				$currentStock_store		= $store_presentStock + $cons_quantity;
				$currentValue_store		= $store_presentStockValue + $before_store_amount;

				$field_array_store = "last_issued_qnty*cons_qty*amount*updated_by*update_date";
				$data_array_store = "" . $before_receive_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'";
			}
		}

		$mrrsql = sql_select("select id, recv_trans_id, issue_trans_id, entry_form, prod_id, issue_qnty, rate, amount from inv_mrr_wise_issue_details where status_active=1 and entry_form=8 and issue_trans_id=$update_id order by recv_trans_id");

		$mrr_data = array();
		foreach ($mrrsql as $row) {

			$all_recv_trans_id .= $row[csf('recv_trans_id')] . ",";
			$all_issue_trans_id .= $row[csf('issue_trans_id')] . ",";
			$mrr_data[$row[csf('recv_trans_id')]]['issue_qnty'] = $row[csf('issue_qnty')];
			$mrr_data[$row[csf('recv_trans_id')]]['amount'] = $row[csf('amount')];
			$mrr_wise_issue_details_id[] = $row[csf('id')];
			$data_mrr_wise_issue_details[$row[csf('id')]] = explode("*", ($user_id . "*'" . $pc_date_time . "'*0*1"));
		}
		$all_recv_trans_id = chop($all_recv_trans_id, ",");

		$rcv_sql = sql_select("select id, balance_qnty, balance_amount from inv_transaction where id in($all_recv_trans_id) order by id");
		$update_trans_field = "balance_qnty*balance_amount*updated_by*update_date";
		foreach ($rcv_sql as $row) {
			$current_bal_qnty = $row[csf("balance_qnty")] + $mrr_data[$row[csf("id")]]['issue_qnty'];
			$current_bal_amt = $row[csf("balance_amount")] + $mrr_data[$row[csf("id")]]['amount'];
			$updateID_trans_array[] = $row[csf("id")];
			$update_trans_data[$row[csf("id")]] = explode("*", ("'" . $current_bal_qnty . "'*'" . $current_bal_amt . "'*'" . $user_id . "'*'" . $pc_date_time . "'"));
		}

		//for allocation
		if ($hdnReturnString != '') {
			/*
			|--------------------------------------------------------------------------
			| for allocation
			|--------------------------------------------------------------------------
			|
			*/
			$sqlAllocation = sql_select("SELECT a.id AS ID, a.qnty AS MST_QNTY, a.qnty_break_down AS QNTY_BREAK_DOWN, b.id AS DTLS_ID, b.qnty AS DTLS_QNTY, b.po_break_down_id AS PO_BREAK_DOWN_ID FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id INNER JOIN inv_transaction c ON b.job_no = c.job_no AND b.item_id = c.prod_id WHERE a.item_id = " . $txt_prod_id . " AND a.is_dyied_yarn = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active=1 AND b.is_deleted=0 AND c.prod_id = " . $txt_prod_id . " and c.mst_id = " . $txt_received_id . " and c.transaction_type in (1) AND c.item_category=1 AND c.status_active = 1 AND c.is_deleted = 0");
			$qnty_breakdown = '';
			$return_qnty = 0;
			$allocationDataArr = array();
			$updateDtlsIdArr = array();
			$data_allocation_dtls = array();
			foreach ($sqlAllocation as $row) {
				$allocation_id = $row['ID'];
				$allocation_qnty = $row['MST_QNTY'];
				$allocationDataArr[$row['PO_BREAK_DOWN_ID']]['dtls_id'] = $row['DTLS_ID'];
				$allocationDataArr[$row['PO_BREAK_DOWN_ID']]['qnty'] = $row['DTLS_QNTY'];

				$expQty = explode(',', $row['QNTY_BREAK_DOWN']);
				foreach ($expQty as $key => $val) {
					$expVal = explode('_', $val);
					$mst_data[$expVal[1]]['mst_qty'] = $expVal[0];
					$mst_data[$expVal[1]]['job_no'] = $expVal[2];
				}
			}

			/*
			|--------------------------------------------------------------------------
			| for order wise return
			|--------------------------------------------------------------------------
			|
			*/
			$expReturnString = explode(",", $hdnReturnString);
			foreach ($expReturnString as $returnString) {
				$returnData = explode("_", $returnString);
				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_dtls
				| data preparing for
				| $data_allocation_dtls
				|--------------------------------------------------------------------------
				|
				*/
				if (!empty($allocationDataArr[$returnData[0]])) {
					$qnty = $allocationDataArr[$returnData[0]]['qnty'] + $returnData[1];
					$dtlsId = $allocationDataArr[$returnData[0]]['dtls_id'];
					$updateDtlsIdArr[] = $dtlsId;
					$data_allocation_dtls[$dtlsId] = explode("*", ("" . $qnty . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));

					//for inv_material_allocation_mst
					if ($qnty_breakdown != '') {
						$qnty_breakdown .= ',';
					}
					$mstQty = $mst_data[$returnData[0]]['mst_qty'] + $returnData[1];
					$qnty_breakdown .= $mstQty . '_' . $returnData[0] . '_' . $mst_data[$returnData[0]]['job_no'];
					$return_qnty += $returnData[1];
				}
			}
		}
		//for order wise return qty end

		//$rID = sql_update("inv_issue_master",$field_array,$data_array,"issue_number","$txt_return_no",1);
		$rIDTr = sql_update("inv_transaction", $field_array, $data_array, "id", "$update_id", 1);
		$rIDprodID = sql_update("product_details_master", $field_array_prod, $data_array_prod, "id", "$prod_id", 1);
		$upTrID = execute_query(bulk_update_sql_statement("inv_transaction", "id", $update_trans_field, $update_trans_data, $updateID_trans_array));
		$upMrrTrID = execute_query(bulk_update_sql_statement("inv_mrr_wise_issue_details", "id", $field_array, $data_mrr_wise_issue_details, $mrr_wise_issue_details_id));
		$storeRID = true;
		if ($store_up_id > 0 && $variable_store_wise_rate == 1) {
			$storeRID = sql_update("inv_store_wise_yarn_qty_dtls", $field_array_store, $data_array_store, "id", $store_up_id, 1);
		}

		$updateProportionate = true;
		if ($hdnReturnString != '') {
			$updateProportionate = sql_update("order_wise_pro_details", $field_array, $data_array, "trans_id", $update_id, 1);

			/*
			|--------------------------------------------------------------------------
			| inv_material_allocation_mst
			| data preparing and updating
			|--------------------------------------------------------------------------
			|
			*/
			$field_allocation = "qnty*qnty_break_down*updated_by*update_date";
			$allocationQnty = $allocation_qnty + $return_qnty;
			$data_allocation = "" . $allocationQnty . "*'" . $qnty_breakdown . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
			$allocation_mst_update = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", "" . $allocation_id . "", 0);

			/*
			|--------------------------------------------------------------------------
			| inv_material_allocation_dtls
			| data updating
			|--------------------------------------------------------------------------
			|
			*/
			if ($allocation_mst_update) {
				$field_allocation_dtls = "qnty*updated_by*update_date";
				$allocation_dtls_update = execute_query(bulk_update_sql_statement("inv_material_allocation_dtls", "id", $field_allocation_dtls, $data_allocation_dtls, $updateDtlsIdArr));
			}
		}

		//echo "10**".$rIDTr."**".$rIDprodID."**".$upTrID."**".$upMrrTrID."**".$storeRID;oci_rollback($con);disconnect($con); die;
		if ($db_type == 0) {
			if ($rIDTr && $rIDprodID && $upTrID && $upMrrTrID && $updateProportionate && $storeRID) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $txt_mrr_no) . "**" . str_replace("'", "", $txt_return_id);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_mrr_no) . "**" . str_replace("'", "", $txt_return_id);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rIDTr && $rIDprodID && $upTrID && $upMrrTrID && $updateProportionate && $storeRID) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $txt_mrr_no) . "**" . str_replace("'", "", $txt_return_id);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_mrr_no) . "**" . str_replace("'", "", $txt_return_id);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "return_number_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

	<script>
		function js_set_value(mrr) {
			var splitArr = mrr.split("_");
			$("#hidden_return_number").val(splitArr[0]); // mrr number
			$("#hidden_posted_in_account").val(splitArr[1]); // is posted account
			$("#hidden_return_id").val(splitArr[2]);
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="180">Search By</th>
							<th width="250" align="center" id="search_by_td_up">Enter Return Number</th>
							<th width="220">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								$search_by = array(1 => 'Return Number');
								//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 140, $search_by, "", 0, "--Select--", "", 1, 0);
								?>
							</td>
							<td width="" align="center" id="search_by_td">
								<input type="text" style="width:230px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />&nbsp;&nbsp;&nbsp;
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_return_search_list_view', 'search_div', 'yarn_receive_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1);  ?>
								<!-- Hidden field here -->
								<input type="hidden" id="hidden_return_number" value="" />
								<input type="hidden" id="hidden_return_id" value="" />
								<input type="hidden" id="hidden_posted_in_account" value="" />
								<!--END -->
							</td>
						</tr>
					</tbody>
					</tr>
				</table>
				<div align="center" style="margin-top:10px" valign="top" id="search_div"> </div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "create_return_search_list_view") {
	$ex_data = explode("_", $data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$year_selection = $ex_data[5];

	$sql_cond = "";
	if ($search_by == 1) {
		if ($search_common != "") $sql_cond .= " and a.issue_number like '%$search_common'";
	}

	if ($txt_date_from != "" && $txt_date_to != "") {
		if ($db_type == 0) {
			$sql_cond .= " and a.issue_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
		} else {
			$sql_cond .= " and a.issue_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
		}
	}

	if ($db_type == 0) {
		$year_cond = " and YEAR(a.issue_date)=$year_selection";
	} else if ($db_type == 2) {
		$year_cond = " and to_char(a.issue_date,'YYYY')=$year_selection";
	} else {
		$year_cond = "";
		$year_field = "";
	}

	if (trim($company) != "") $sql_cond .= " and a.company_id='$company'";

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
	else $year_field = ""; //defined Later

	$sql = "select a.id, $year_field a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id,a.issue_date, a.item_category, a.received_id,a.received_mrr_no, sum(b.cons_quantity)as cons_quantity,a.is_posted_account
	from inv_issue_master a, inv_transaction b
	where a.id=b.mst_id and b.transaction_type=3 and a.status_active=1 and a.item_category=1 and b.item_category=1 and a.entry_form=8 $sql_cond $year_cond group by a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, a.insert_date,a.is_posted_account order by a.id";

	$company_arr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$arr = array(2 => $company_arr, 3 => $supplier_arr);
	echo create_list_view("list_view", "Return No, Year, Company Name, Returned To, Return Date,Return Qty,Receive MRR", "70,60,150,170,80,100,150", "850", "230", 0, $sql, "js_set_value", "issue_number,is_posted_account,id", "", 1, "0,0,company_id,supplier_id,0,0,0", $arr, "issue_number_prefix_num,year,company_id,supplier_id,issue_date,cons_quantity,received_mrr_no", "", "", '0,0,0,0,3,1,0');
	exit();
}

if ($action == "return_multy_number_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
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
			$("#hidden_return_number").val(splitArr[0]); // mrr number
			$("#hidden_return_id").val(splitArr[2]);

			toggle(document.getElementById('tr_' + splitArr[0]), '#FFFFCC');

			if (jQuery.inArray(splitArr[3], selected_id) == -1) {
				selected_id.push(splitArr[3]);
				selected_name.push(splitArr[1]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == splitArr[3]) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}

			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hidden_return_id').val(id);
			$('#hidden_return_number').val(name);
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="120" class="must_entry_caption">Supplier</th>
							<th width="180">Search By</th>
							<th width="250" align="center" id="search_by_td_up">Enter Return Number</th>
							<th width="220">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("cbo_return_to", 120, "select id,supplier_name from lib_supplier order by supplier_name", "id,supplier_name", 1, "-- Select --", $cbo_return_to, "", 0);
								?>
							</td>
							<td align="center">
								<?
								$search_by = array(1 => 'Return Number');
								//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 140, $search_by, "", 0, "--Select--", "", 1, 0);
								?>
							</td>
							<td width="" align="center" id="search_by_td">
								<input type="text" style="width:230px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />&nbsp;&nbsp;&nbsp;
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_return_to').value, 'create_multy_return_search_list_view', 'search_div', 'yarn_receive_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1);  ?>
								<!-- Hidden field here-->
								<!--END -->
							</td>
						</tr>
					</tbody>
					</tr>
				</table>
				<div align="center" style="margin-top:10px" valign="top" id="search_div"> </div>

			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "create_multy_return_search_list_view") {
	echo '<input type="hidden" id="hidden_return_number" value="" /><input type="hidden" id="hidden_return_id" value="" />';


	$ex_data = explode("_", $data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$return_to = $ex_data[5];


	$sql_cond = "";
	if ($search_by == 1) {
		if ($search_common != "") $sql_cond .= " and a.issue_number like '%$search_common'";
	}

	if ($txt_date_from != "" && $txt_date_to != "") {
		if ($db_type == 0) {
			$sql_cond .= " and a.issue_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
		} else {
			$sql_cond .= " and a.issue_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
		}
	}

	if (trim($company) != "") $sql_cond .= " and a.company_id='$company'";

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
	else $year_field = ""; //defined Later

	if (str_replace("'", "", $return_to == 0)) {
		echo "<p style='font-size:25px; color:#F00'>Please Select Supplier.</p>";
		die;
	} else {
		$supplier_con = " and a.supplier_id=$return_to";
	}



	$sql = "select a.id, $year_field a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id,a.issue_date, a.item_category, a.received_id,a.received_mrr_no, sum(b.cons_quantity)as cons_quantity,a.is_posted_account
	from inv_issue_master a, inv_transaction b
	where a.id=b.mst_id and b.transaction_type=3 and a.status_active=1 and a.item_category=1 and b.item_category=1 and a.entry_form=8  $supplier_con $sql_cond group by a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, a.insert_date,a.is_posted_account order by a.id";
	//echo $sql;
	$company_arr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$arr = array(2 => $company_arr, 3 => $supplier_arr);
	echo create_list_view("list_view", "Return No, Year, Company Name, Returned To, Return Date,Return Qty,Receive MRR", "70,60,150,170,80,100,150", "850", "230", 0, $sql, "js_set_value", "issue_number,is_posted_account,id", "1", 1, "0,0,company_id,supplier_id,0,0,0", $arr, "issue_number_prefix_num,year,company_id,supplier_id,issue_date,cons_quantity,received_mrr_no", "", "", "0,0,0,0,3,1,0", "", 1);
	exit();
}

if ($action == "populate_master_from_data") {
	$sql = "select id,issue_number,company_id,supplier_id,issue_date,item_category,received_id,received_mrr_no,pi_id,remarks
	from inv_issue_master
	where id='$data' and item_category=1 and entry_form=8";
	//echo $sql;
	$res = sql_select($sql);
	foreach ($res as $row) {
		echo "$('#txt_return_id').val('" . $row[csf("id")] . "');\n";
		echo "$('#txt_return_no').val('" . $row[csf("issue_number")] . "');\n";
		echo "$('#cbo_company_id').val(" . $row[csf("company_id")] . ");\n";
		echo "$('#cbo_return_to').val('" . $row[csf("supplier_id")] . "');\n";
		echo "$('#txt_return_date').val('" . change_date_format($row[csf("issue_date")]) . "');\n";
		//$('#txt_return_date').attr('disabled','disabled');
		echo "$('#txt_mrr_no').val('" . $row[csf("received_mrr_no")] . "');\n";
		echo "$('#txt_received_id').val('" . $row[csf("received_id")] . "');\n";
		echo "$('#txt_remarks').val('" . $row[csf("remarks")] . "');\n";
		$pi_no = return_field_value("pi_number", "com_pi_master_details", "id='" . $row[csf("pi_id")] . "'");
		$ref_closing_status = return_field_value("ref_closing_status", "com_pi_master_details", "id='" . $row[csf("pi_id")] . "'");
		echo "$('#txt_pi_no').val('" . $pi_no . "');\n";
		echo "$('#hidden_ref_closing_status').val('" . $ref_closing_status . "');\n";
		echo "$('#pi_id').val('" . $row[csf("pi_id")] . "');\n";
		$issue_id = return_field_value("issue_id", "inv_receive_master", "id='" . $row[csf("received_id")] . "'");
		echo "$('#hdn_issue_id').val('" . $issue_id . "');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#txt_mrr_no').attr('disabled','disabled');\n";

		//$entry_form=return_field_value("entry_form","inv_receive_master","id=".$row[csf("received_id")]);
		$sql_rcv = sql_select("select entry_form, lc_no from inv_receive_master where id=" . $row[csf("received_id")]);
		foreach ($sql_rcv as $rcv_row) {
			$entry_form = $rcv_row[csf('entry_form')];
			$lc_no = $rcv_row[csf('lc_no')];
		}

		if ($entry_form == 9) {
			//$pi_no=return_field_value("pi_number","com_pi_master_details","id='".$row[csf("pi_id")]."'");
			echo "$('#txt_pi_no').removeAttr('disabled','disabled');\n";
			//echo "$('#txt_pi_no').val('".$pi_no."');\n";
			//echo "$('#pi_id').val('".$row[csf("pi_id")]."');\n";
		} else {
			echo "$('#txt_pi_no').attr('disabled','disabled');\n";
			//echo "$('#txt_pi_no').val('');\n";
			//echo "$('#pi_id').val('');\n";
		}
		//right side list view
		$trans_type_sql = sql_select("select distinct b.transaction_type,a.receive_basis, a.receive_purpose from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type in(1,4) and a.recv_number='" . $row[csf("received_mrr_no")] . "'
			union all
			select distinct b.transaction_type,0 as receive_basis, 0 as receive_purpose from inv_item_transfer_mst a, inv_transaction b where a.id=b.mst_id and b.transaction_type in(5) and a.transfer_system_id='" . $row[csf("received_mrr_no")] . "'");

		if ($trans_type_sql[0][csf("transaction_type")] == 1 || $trans_type_sql[0][csf("transaction_type")] == 4)
			$mrr_type = 1;
		else
			$mrr_type = 2;

		//for order wise qty
		if ($mrr_type == 1 && $trans_type_sql[0][csf("receive_purpose")] == 2) {
			echo "$('#txt_return_qnty').attr('placeholder','browse').attr('onclick','func_return_qty()').attr('readonly','readonly');\n";
		} else {
			echo "$('#txt_return_qnty').attr('placeholder','Write').removeAttr('onclick','func_return_qty()').removeAttr('readonly','readonly');\n";
		}

		//for lc no
		$lcNumber = return_field_value("lc_number", "com_btb_lc_master_details", "id='" . $lc_no . "'");
		echo "$('#txt_lc_no').val('" . $lcNumber . "');\n";

		echo "show_list_view('" . $row[csf("received_id")] . "**" . $mrr_type . "' ,'show_product_listview' ,'list_product_container' ,'requires/yarn_receive_return_controller' , '');\n";
	}
	exit();
}

if ($action == "show_dtls_list_view") {
	/*$ex_data = explode("**",$data);
	$return_number = $ex_data[0];
	$ret_mst_id = $ex_data[1];

	$cond="";
	if($return_number!="") $cond .= " and a.issue_number='$return_number'";
	if($ret_mst_id!="") $cond .= " and a.id='$ret_mst_id'";
	*/
	// $sql = "select a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, b.id, b.no_of_bags, b.cone_per_bag, b.cons_quantity, b.cons_uom, round(to_number(b.rcv_rate), 4) as cons_rate, b.rcv_amount as cons_amount, c.product_name_details, c.id as prod_id
	// from inv_issue_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id
	// where a.id=b.mst_id and b.item_category=1 and b.transaction_type=3 and a.id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql = "select a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, b.id, b.no_of_bags, b.cone_per_bag, b.cons_quantity, b.cons_uom, round(to_number(b.rcv_rate), 4) as cons_rate, b.rcv_amount as cons_amount, c.product_name_details, c.id as prod_id, b.cons_rate as issue_return_cons_rate,b.cons_amount as issue_return_cons_amount from inv_issue_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id where a.id=b.mst_id and b.item_category=1 and b.transaction_type=3 and a.id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $sql; //die();
	$result = sql_select($sql);
	$i = 1;
	$rettotalQnty = 0;
	$rcvtotalQnty = 0;
	$totalAmount = 0;
?>

	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" style="width:1000px" rules="all">
		<thead>
			<tr>
				<th>SL</th>
				<th>Item Description</th>
				<th>No Of Bag</th>
				<th>No Of Cone</th>
				<th>Product ID</th>
				<th>Received No</th>
				<th>Return Qnty</th>
				<th>UOM</th>
				<th>Rate</th>
				<th>Return Value</th>
			</tr>
		</thead>
		<tbody>
			<?
			//$ref_closing_status = ("ref_closing_status","inv_receive_master","id=".$row[csf("received_id")]);
			foreach ($result as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				
				$mrr_arr = explode("-", $row['RECEIVED_MRR_NO']);
				/*echo "select b.balance_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id=".$row[csf("prod_id")]." and b.item_category=1 and b.transaction_type=1 and a.recv_number='".$row[csf("received_mrr_no")]."'";*/
				if ($row[csf("prod_id")] != "") {
					//echo "select b.balance_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id=".$row[csf("prod_id")]." and b.item_category=1 and b.transaction_type in (1,4,5) and a.id='".$row[csf("received_id")]."'";
					$sqlTr = sql_select("select b.balance_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id=" . $row[csf("prod_id")] . " and b.item_category=1 and b.transaction_type in (1,4,5) and a.id='" . $row[csf("received_id")] . "'");
				}

				//$ref_closing_status = return_field_value("ref_closing_status","inv_receive_master","id=".$row[csf("received_id")]);

				$rcvQnty = $sqlTr[0][csf('balance_qnty')];

				$rettotalQnty += $row[csf("cons_quantity")];
				//$rcvtotalQnty +=$rcvQnty;
				$totalAmount += $row[csf("cons_amount")];

				$tot_no_of_bags += $row[csf("no_of_bags")];
				$tot_cone_per_bag += $row[csf("cone_per_bag")];

			?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")] . "_"; ?>,<? echo $rcvQnty; ?>","child_form_input_data","requires/yarn_receive_return_controller");hidden_ref_closing(<?php echo $row[csf("cons_quantity")]; ?>);' style="cursor:pointer">
					<td width="30"><?php echo $i; ?></td>
					<td width="200">
						<p><?php echo $row[csf("product_name_details")]; ?></p>
					</td>
					<td width="70" align="right">
						<p><?php echo $row[csf("no_of_bags")]; ?></p>
					</td>
					<td width="70" align="right">
						<p><?php echo $row[csf("cone_per_bag")]; ?></p>
					</td>
					<td width="70" align="center">
						<p><?php echo $row[csf("prod_id")]; ?></p>
					</td>
					<td width="100">
						<p><?php echo $row[csf("received_mrr_no")]; ?></p>
					</td>
					<td width="70" align="right">
						<p><?php echo $row[csf("cons_quantity")]; ?></p>
					</td>
					<!--<td width="70" align="right"><p><!?php echo $rcvQnty; ?></p></td>-->
					<td width="70">
						<p><?php echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p>
					</td>
					<? if($mrr_arr[1] != 'YIR'){ ?>
						<td width="70" align="right"><p><?php echo $row[csf("cons_rate")]; ?></p></td>
						<td width="70" align="right"><p><?php echo $row[csf("cons_amount")]; ?></p></td>
					<? } else{ ?>
						<td width="70" align="right"><p><?php echo $row[csf("issue_return_cons_rate")]; ?></p></td>
						<td width="70" align="right"><p><?php echo $row[csf("issue_return_cons_amount")]; ?></p></td>
					<? } ?>
				</tr>
			<? $i++;
			} ?>
		<tfoot>
			<th colspan="2">Total</th>
			<th><?php echo $tot_no_of_bags; ?></th>
			<th><?php echo $tot_cone_per_bag; ?></th>
			<th colspan="2"></th>
			<th><?php echo number_format($rettotalQnty, 2, ".", ""); ?></th>
			<th colspan="2"></th>
			<th><?php echo number_format($totalAmount, 2, ".", ""); ?></th>
		</tfoot>
		</tbody>
	</table>

<?
	exit();
}

if ($action == "child_form_input_data") {
	$ex_data = explode(",", $data);
	$data = explode("_", $ex_data[0]); 	// transaction id
	$rcvQnty = $ex_data[1];
	$ref_closing_status = $data[1];
	//echo $ref_closing_status;die;
	// $sql = "select b.id as prod_id, b.product_name_details, a.id as tr_id, a.no_of_bags, a.cone_per_bag, a.company_id, a.store_id, a.floor_id, a.room, a.rack, a.self, a.bin_box, a.order_rate, a.order_ile_cost, a.cons_uom, a.rcv_rate as cons_rate, a.cons_quantity, a.rcv_amount as cons_amount,a.buyer_id
	// from inv_transaction a, product_details_master b
	// where a.id=$data[0] and a.status_active=1 and a.item_category=1 and transaction_type=3 and a.prod_id=b.id and b.status_active=1";
	$sql = "select b.id as prod_id, b.product_name_details, a.id as tr_id, a.no_of_bags, a.cone_per_bag, a.company_id, a.store_id, a.floor_id, a.room, a.rack, a.self, a.bin_box, a.order_rate, a.order_ile_cost, a.cons_uom, a.rcv_rate as cons_rate, a.cons_quantity, a.rcv_amount as cons_amount,a.buyer_id, a.cons_rate as issue_return_cons_rate, a.cons_amount as issue_return_cons_amount, c.received_mrr_no from inv_transaction a left join inv_issue_master c on c.id = a.mst_id, product_details_master b where a.id=$data[0] and a.status_active=1 and a.item_category=1 and transaction_type=3 and a.prod_id=b.id and b.status_active=1";
	//echo $sql;die;
	$result = sql_select($sql);
	foreach ($result as $row) {
		echo "$('#txt_item_description').val('" . $row[csf("product_name_details")] . "');\n";
		echo "$('#txt_prod_id').val('" . $row[csf("prod_id")] . "');\n";
		echo "$('#txt_no_of_bag').val('" . $row[csf("no_of_bags")] . "');\n";
		echo "$('#txt_no_of_cone').val('" . $row[csf("cone_per_bag")] . "');\n";
		echo "$('#before_prod_id').val('" . $row[csf("prod_id")] . "');\n";
		echo "$('#cbo_store_name').val('" . $row[csf("store_id")] . "');\n";
		echo "$('#cbo_buyer_name').val('" . $row[csf("buyer_id")] . "');\n";

		echo "load_drop_down('requires/yarn_receive_return_controller', " . $row[csf("store_id")] . "+'_'+" . $row[csf('company_id')] . ", 'load_drop_floor','floor_td');\n";
		echo "document.getElementById('cbo_floor').value 	= '" . $row[csf("floor_id")] . "';\n";

		echo "load_drop_down('requires/yarn_receive_return_controller', " . $row[csf("store_id")] . "+'_'+" . $row[csf('company_id')] . ", 'load_drop_room','room_td');\n";
		echo "document.getElementById('cbo_room').value 	= '" . $row[csf("room")] . "';\n";

		echo "load_drop_down('requires/yarn_receive_return_controller', " . $row[csf("store_id")] . "+'_'+" . $row[csf('company_id')] . ", 'load_drop_rack','rack_td');\n";
		echo "document.getElementById('txt_rack').value 	= '" . $row[csf("rack")] . "';\n";

		echo "load_drop_down('requires/yarn_receive_return_controller', " . $row[csf("store_id")] . "+'_'+" . $row[csf('company_id')] . ", 'load_drop_shelf','shelf_td');\n";
		echo "document.getElementById('txt_shelf').value 	= '" . $row[csf("self")] . "';\n";

		echo "load_drop_down('requires/yarn_receive_return_controller', " . $row[csf("store_id")] . "+'_'+" . $row[csf('company_id')] . ", 'load_drop_bin','bin_td');\n";
		echo "document.getElementById('cbo_bin').value 		= '" . $row[csf("bin_box")] . "';\n";

		echo "$('#txt_return_qnty').val('" . $row[csf("cons_quantity")] . "');\n";
		$rcvQnty = $rcvQnty + $row[csf("cons_quantity")];
		echo "$('#txt_receive_qnty').val('" . $rcvQnty . "');\n";
		echo "$('#cbo_uom').val('" . $row[csf("cons_uom")] . "');\n";

		$mrr_arr = explode("-", $row['RECEIVED_MRR_NO']);
		if($mrr_arr[1] != "YIR")
		{
			echo "$('#txt_rate').val('". number_format($row[csf("cons_rate")],4,".","")."');\n";
			echo "$('#txt_return_value').val(".$row[csf("cons_amount")].");\n";
		}
		else
		{
			echo "$('#txt_rate').val('". number_format($row[csf("issue_return_cons_rate")],4,".","")."');\n";
			echo "$('#txt_return_value').val(".$row[csf("issue_return_cons_amount")].");\n";
		}

		echo "$('#update_id').val(" . $row[csf("tr_id")] . ");\n";

		echo "$('#order_rate').val('" . $row[csf("order_rate")] . "');\n";
		echo "$('#order_ile_cost').val('" . $row[csf("order_ile_cost")] . "');\n";
		if ($ref_closing_status == 1) {
			echo "$('#txt_return_qnty').attr('disabled',true);\n";
			echo "$('#txt_return_qnty').attr('readonly',true);\n";
		}
	}

	//for order wise return qty
	$sql = "select po_breakdown_id AS PO, quantity as QUANTITY from order_wise_pro_details where trans_id = " . $data[0] . " and trans_type = 3 and entry_form = 8";
	$rslt = sql_select($sql);
	if (!empty($rslt)) {
		$return_str = '';
		foreach ($rslt as $row) {
			if ($return_str != '') {
				$return_str .= ',';
			}
			$return_str .= $row['PO'] . '_' . $row['QUANTITY'];
		}
		echo "$('#hdnReturnString').val('" . $return_str . "');\n";
	} else {
		echo "$('#hdnReturnString').val('');\n";
	}

	if ($row['PO'] != "") {
		echo "$('#txt_return_qnty').attr('placeholder','browse').attr('onclick','func_return_qty()').attr('readonly','readonly');\n";
	} else {
		echo "$('#txt_return_qnty').attr('placeholder','Write').removeAttr('onclick','func_return_qty()').removeAttr('readonly','readonly');\n";
	}

	echo "set_button_status(1, permission, 'fnc_yarn_receive_return_entry',1,1);\n";
	exit();
}

// pi popup here----------------------//
if ($action == "pi_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

	<script>
		function js_set_value(str) {
			var splitData = str.split("_");
			$("#hidden_tbl_id").val(splitData[0]); // pi id
			$("#hidden_pi_number").val(splitData[1]); // pi number
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th>Supplier</th>
							<th align="center" id="search_by_th_up">Enter PI Number</th>
							<th>Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /></th>
						</tr>
					</thead>
					<tbody>
						<tr class="general">
							<td>
								<?
								echo create_drop_down("cbo_supplier", 170, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
								?>
							</td>
							<td width="180" align="center" id="search_by_td">
								<input type="text" style="width:230px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_wopi_search_list_view', 'search_div', 'yarn_receive_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="4">
								<? echo load_month_buttons(1);  ?>
								<!-- Hidden field here-->
								<input type="hidden" id="hidden_tbl_id" value="" />
								<input type="hidden" id="hidden_pi_number" value="hidden_pi_number" />
								<!-- -END-->
							</td>
						</tr>
					</tbody>
					</tr>
				</table>
				<div align="center" style="margin-top:5px" valign="top" id="search_div"> </div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
	<?
}

if ($action == "create_wopi_search_list_view") {
	$ex_data = explode("_", $data);
	$supplier = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];

	$sql_cond = "";
	$sql_cond .= " and a.pi_number LIKE '%$txt_search_common%'";
	if (trim($company) != 0) $sql_cond .= " and a.importer_id='$company'";
	if (trim($supplier) != 0) $sql_cond .= " and a.supplier_id='$supplier'";

	if ($txt_date_from != "" && $txt_date_to != "") {
		if ($db_type == 0) {
			$sql_cond .= " and a.pi_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
		} else {
			$sql_cond .= " and a.pi_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
		}
	}

	$sql = "select a.id, a.pi_number, a.pi_date, a.supplier_id, a.currency_id, a.source, c.lc_number as lc_number
	from com_pi_master_details a
	left join com_btb_lc_pi b on a.id=b.pi_id
	left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id
	where
	a.item_category_id = 1 and
	a.status_active=1 and a.is_deleted=0
	$sql_cond order by a.id";
	//echo $sql;
	$result = sql_select($sql);
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$arr = array(3 => $supplier_arr, 4 => $currency, 5 => $source);

	echo  create_list_view("list_view", "PI No, LC ,Date, Supplier, Currency, Source", "120,130,100,200,100", "830", "230", 0, $sql, "js_set_value", "id,pi_number", "", 1, "0,0,0,supplier_id,currency_id,source", $arr, "pi_number,lc_number,pi_date,supplier_id,currency_id,source", "", '', '0,0,3,0,0,0');
	exit();
}

if ($action == "yarn_receive_return_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);
	$no_copy = $data[4];
	if (isset($data[3])) {
		$path = $data[3];
	} else {
		$path = "";
	}
	$sql = " select id, issue_number, received_id, issue_date, supplier_id, pi_id, remarks, received_mrr_no from  inv_issue_master where issue_number='$data[1]' and entry_form=8 and item_category=1 and status_active=1 and is_deleted=0";
	$dataArray = sql_select($sql);

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$country_arr = return_library_array("select id,country_name from lib_country", "id", "country_name");
	//$receive_arr = return_library_array("select id, recv_number from inv_receive_master","id","recv_number");
	if ($db_type == 0) {
		$select_prod = " group_concat(b.prod_id) as prod_id";
	} else {
		$select_prod = " listagg(cast(b.prod_id as varchar(4000)),',') within group(order by b.prod_id) as prod_id";
	}
	$sql_rcv = sql_select("select a.entry_form, a.issue_id, $select_prod from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type in(1,4) and a.item_category=1 and a.id='" . $dataArray[0][csf('received_id')] . "'");
	if ($sql_rcv[0][csf('entry_form')] == 9) {
		$all_prod_id = $sql_rcv[0][csf('prod_id')];
		if ($all_prod_id == "") $all_prod_id = 0;
		//echo "select c.mst_id from inv_transaction a, inv_mrr_wise_issue_details b, inv_transaction c where a.id=b.issue_trans_id and b.recv_trans_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.transaction_type=2 and b.entry_form=3 and a.mst_id='".$sql_rcv[0][csf('issue_id')]."' and a.prod_id in($all_prod_id) and c.prod_id in($all_prod_id)";die;
		$rcv_mst_id = sql_select("select c.mst_id from inv_transaction a, inv_mrr_wise_issue_details b, inv_transaction c where a.id=b.issue_trans_id and b.recv_trans_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.transaction_type=2 and b.entry_form=3 and a.mst_id='" . $sql_rcv[0][csf('issue_id')] . "' and a.prod_id in($all_prod_id) and c.prod_id in($all_prod_id)");
		$org_rcv_mst_no = return_field_value("recv_number", "inv_receive_master", "id='" . $rcv_mst_id[0][csf('mst_id')] . "'", "recv_number");
		//echo $org_rcv_mst_no;die;
	}

	if ($data[3] == 1) {
		$value_width = 820;
		$column = '';
	} else {
		$value_width = 1050;
		$column = '<th width="100" align="center">Rate</th><th width="100" align="center">Return Value</th>';
	}
	$copyNo = "";
	for ($x = 1; $x <= $no_copy; $x++) {
		if ($x == 1) {
			$copyNo = "<span style='font-size:x-large;'>1<sup>st</sup> Copy</span>";
		} else if ($x == 2) {
			$copyNo = "<span style='font-size:x-large;'>2<sup>nd</sup> Copy</span>";
		} else if ($x == 3) {
			$copyNo = "<span style='font-size:x-large;'>3<sup>rd</sup> Copy</span>";
		} else if ($x == 4) {
			$copyNo = "<span style='font-size:x-large;'>4<sup>th</sup> Copy</span>";
		} else if ($x == 5) {
			$copyNo = "<span style='font-size:x-large;'>5<sup>th</sup> Copy</span>";
		} else if ($x == 6) {
			$copyNo = "<span style='font-size:x-large;'>6<sup>th</sup> Copy</span>";
		} else if ($x == 7) {
			$copyNo = "<span style='font-size:x-large;'>7<sup>th</sup> Copy</span>";
		} else if ($x == 8) {
			$copyNo = "<span style='font-size:x-large;'>8<sup>th</sup> Copy</span>";
		} else if ($x == 9) {
			$copyNo = "<span style='font-size:x-large;'>9<sup>th</sup> Copy</span>";
		} else if ($x == 10) {
			$copyNo = "<span style='font-size:x-large;'10<sup>th</sup> Copy</span>";
		}

	?>
		<div style="width:930px;">
			<table width="910" cellspacing="0" align="right">
				<tr>
					<td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
				</tr>
				<tr class="form_caption">

					<?
					$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
					?>
					<td align="left" width="50">
						<?
						foreach ($data_array as $img_row) {
						?>
							<img src='../<? echo $path; ?><? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />
						<?
						}
						?>
					</td>
					<td colspan="4" align="center" style="font-size:14px">
						<?

						echo show_company($data[0], '', '');
						/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result['plot_no']; ?>
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?>
						Block No: <? echo $result['block_no'];?>
						City No: <? echo $result['city'];?>
						Zip Code: <? echo $result['zip_code']; ?>
						Province No: <?php echo $result['province'];?>
						Country: <? echo $country_arr[$result['country_id']]; ?><br>
						Email Address: <? echo $result['email'];?>
						Website No: <? echo $result['website'];
					}*/
						?>
					</td>
					<td colspan="3" style="color:black; font-weight:bold; padding-left:70px"><? echo $copyNo; ?></td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:x-large"><strong><u>Purchase Return/Delivery Challan</u></strong></td>
				</tr>
				<tr>
					<td width="120"><strong>Return Number:</strong></td>
					<td width="175"><? echo $dataArray[0][csf('issue_number')]; ?></td>
					<td width="110"><strong>Receive ID:</strong></td>
					<td width="175"><? echo $dataArray[0][csf('received_mrr_no')]; ?></td>
					<td width="100"><strong>Return To :</strong></td>
					<td><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Return Date:</strong></td>
					<td><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
					<td><strong>PI No:</strong></td>
					<td>
						<?
						$pi_no = return_field_value("pi_number", "com_pi_master_details", "id=" . $dataArray[0][csf('pi_id')], "pi_number");
						echo $pi_no;
						?>
					</td>
					<td><strong>Lc No:</strong></td>
					<td>
						<?
						$btb_lc_no = return_field_value("a.lc_number as lc_number", " com_btb_lc_master_details a, com_btb_lc_pi b", "a.id=b.com_btb_lc_master_details_id and b.pi_id=" . $dataArray[0][csf('pi_id')], "lc_number");
						echo $btb_lc_no;
						?>
					</td>
				</tr>
				<tr>
					<td><strong>Remarks:</strong></td>
					<td><? echo $dataArray[0][csf('remarks')]; ?></td>
					<td><strong>Origin Rcv ID:</strong></td>
					<td><? if ($sql_rcv[0][csf('entry_form')] == 9) echo $org_rcv_mst_no;
						else echo $dataArray[0][csf('received_mrr_no')]; ?></td>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="6">&nbsp;</td>
				</tr>
			</table>
			<br />
			<div style="width:100%;">
				<table align="center" cellspacing="0" width="<? echo $value_width; ?>" border="1" rules="all" class="rpt_table">
					<thead bgcolor="#dddddd" align="center">
						<th width="30">SL</th>
						<th width="250" align="center">Item Description</th>
						<th width="70" align="center">UOM</th>
						<th width="70" align="center">Lot</th>
						<th width="100" align="center">No Of Bag</th>
						<th width="100" align="center">No Of Cone</th>
						<th width="100" align="center">Return Qnty.</th>
						<? echo $column; ?>
						<th align="center">Store</th>
					</thead>
					<?
					$mrr_no = $dataArray[0][csf('issue_number')];;
					//$up_id =$data[1];
					$cond = "";
					if ($mrr_no != "") $cond .= " and c.issue_number='$mrr_no'";
					//if($up_id!="") $cond .= " and a.id='$up_id'";
					$i = 1;
					$sql_dtls = "select b.id as prod_id, b.product_name_details, a.id as tr_id, a.no_of_bags, a.cone_per_bag, a.store_id, a.cons_uom, a.cons_quantity, round(to_number(a.rcv_rate), 4) as cons_rate,a.rcv_amount as cons_amount, b.lot from inv_transaction a, product_details_master b, inv_issue_master c
				where c.id=a.mst_id and a.status_active=1 and a.company_id='$data[0]' and c.issue_number='$data[1]' and a.item_category=1 and transaction_type=3 and a.prod_id=b.id and b.status_active=1 ";
					$sql_result = sql_select($sql_dtls);

					foreach ($sql_result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$qnty += $row[csf('cons_quantity')];
						$no_of_bags += $row[csf('no_of_bags')];
						$cone_per_bag += $row[csf('cone_per_bag')];
						$totalAmount += $row[csf("cons_amount")];
					?>

						<tr bgcolor="<? echo $bgcolor; ?>">
							<td><? echo $i; ?></td>
							<td><? echo $row[csf('product_name_details')]; ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
							<td align="center"><? echo $row[csf('lot')]; ?></td>
							<td align="right"><? echo $row[csf('no_of_bags')]; ?></td>
							<td align="right"><? echo $row[csf('cone_per_bag')]; ?></td>
							<td align="right"><? echo $row[csf('cons_quantity')]; ?></td>
							<?
							if ($data[3] == 0) { ?>
								<td align="right"><? echo $row[csf('cons_rate')]; ?></td>
								<td align="right"><?php echo number_format($row[csf('cons_amount')], 2, ".", ""); ?></td>
							<?
							} ?>
							<td><? echo $store_library[$row[csf('store_id')]]; ?></td>

						</tr>
					<?php
						$i++;
					}
					?>
					<tr>
						<td align="right" colspan="4">Total</td>
						<td align="right"><? echo number_format($no_of_bags, 0, '', ','); ?></td>
						<td align="right"><? echo number_format($cone_per_bag, 0, '', ','); ?></td>
						<td align="right"><? echo number_format($qnty, 0, '', ','); ?></td>
						<?
						if ($data[3] == 0) { ?>
							<td align="right">&nbsp;</td>
							<td align="right"><?php echo number_format($totalAmount, 2, ".", ""); ?></td>
						<?
						} ?>
						<td align="right">&nbsp;</td>
					</tr>
				</table>

				<?
				echo signature_table(7, $data[0], "930px");
				?>
			</div>
		</div>
		<div style="page-break-after:always;"></div>
	<?
	}
	exit();
}

if ($action == "yarn_receive_return_print_2") {
	extract($_REQUEST);
	$data = explode('*', $data);
	// print_r ($data);

	$sql = " select id, issue_number, received_id, issue_date, supplier_id, pi_id, remarks from  inv_issue_master where issue_number='$data[1]' and entry_form=8 and item_category=1 and status_active=1 and is_deleted=0";
	$dataArray = sql_select($sql);

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$country_arr = return_library_array("select id,country_name from lib_country", "id", "country_name");
	$receive_arr = return_library_array("select id,recv_number from inv_receive_master", "id", "recv_number");
	$count_arr = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	?>
	<div style="width:1130px;">
		<table width="1110" cellspacing="0" align="right">
			<tr>
				<td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">

				<?
				$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td align="left" width="50">
					<?
					foreach ($data_array as $img_row) {
					?>
						<img src='../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />
					<?
					}
					?>
				</td>
				<td colspan="4" align="center" style="font-size:14px">
					<?
					echo show_company($data[0], '', '');
					?>
				</td>
			</tr>
			<tr>
				<td colspan="7" align="center" style="font-size:x-large"><strong><u>Purchase Return/Delivery Challan/Gate Pass</u></strong></td>
			</tr>
			<tr>
				<td width="120"><strong>Return Number:</strong></td>
				<td width="175"><? echo $dataArray[0][csf('issue_number')]; ?></td>
				<td width="110"><strong>Receive ID:</strong></td>
				<td width="175"><? echo $receive_arr[$dataArray[0][csf('received_id')]]; ?></td>
				<td width="100"><strong>Return To :</strong></td>
				<td><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Return Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
				<td><strong>PI No:</strong></td>
				<td>
					<?
					$pi_no = return_field_value("pi_number", "com_pi_master_details", "id=" . $dataArray[0][csf('pi_id')], "pi_number");
					echo $pi_no;
					?>
				</td>
				<td><strong>Lc No:</strong></td>
				<td>
					<?
					$btb_lc_no = return_field_value("a.lc_number as lc_number", " com_btb_lc_master_details a, com_btb_lc_pi b", "a.id=b.com_btb_lc_master_details_id and b.pi_id=" . $dataArray[0][csf('pi_id')], "lc_number");
					echo $btb_lc_no;
					?>
				</td>
			</tr>
			<tr>
				<td><strong>Remarks:</strong></td>
				<td><? echo $dataArray[0][csf('remarks')]; ?></td>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="7">&nbsp;</td>
			</tr>
		</table>
		<br />
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="1110" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="100" align="center">Lot No</th>
					<th width="100" align="center">Count</th>
					<th width="250" align="center">Item Description</th>
					<th width="100" align="center">No Of Bag</th>
					<th width="100" align="center">No Of Cone</th>
					<th width="100" align="center">Return Qnty.</th>
					<? if($data[3] == 1){?>
						<th width="100" align="center">UOM</th>
						<th width="100" align="center">Rate</th>
						<th width="100" align="center">Return Value</th>
					<? } ?>
					<th width="100" align="center">Store</th>
				</thead>
				<?
				$mrr_no = $dataArray[0][csf('issue_number')];;
				//$up_id =$data[1];
				$cond = "";
				if ($mrr_no != "")
					$cond .= " and c.issue_number='$mrr_no'";
				//if($up_id!="") $cond .= " and a.id='$up_id'";
				$i = 1;
				$sql_dtls = "select b.id as prod_id, b.product_name_details, a.id as tr_id, a.no_of_bags, a.cone_per_bag, a.store_id, a.cons_uom, a.cons_quantity, round(to_number(a.rcv_rate), 4) as cons_rate,a.rcv_amount as cons_amount,a.store_id,b.lot, b.yarn_count_id
				from inv_transaction a, product_details_master b, inv_issue_master c
				where c.id=a.mst_id and a.status_active=1 and a.company_id='$data[0]' and c.issue_number='$data[1]' and a.item_category=1 and transaction_type=3 and a.prod_id=b.id and b.status_active=1 ";
				//echo $sql_dtls;
				$sql_result = sql_select($sql_dtls);

				foreach ($sql_result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					$qnty += $row[csf('cons_quantity')];
					$no_of_bags += $row[csf('no_of_bags')];
					$cone_per_bag += $row[csf('cone_per_bag')];
					$totalAmount += $row[csf("cons_amount")];
				?>

					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $i; ?></td>
						<td><? echo $row[csf('lot')]; ?></td>
						<td><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></td>
						<td><? echo $row[csf('product_name_details')]; ?></td>
						<td align="right"><? echo $row[csf('no_of_bags')]; ?></td>
						<td align="right"><? echo $row[csf('cone_per_bag')]; ?></td>
						<td align="right"><? echo $row[csf('cons_quantity')]; ?></td>
						<? if($data[3] == 1){?>
							<td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
							<td align="right"><? echo $row[csf('cons_rate')]; ?></td>
							<td align="right"><? echo $row[csf('cons_amount')]; ?></td>
						<? } ?>
						<td><? echo $store_library[$row[csf('store_id')]]; ?></td>
					</tr>
				<?php
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="4">Total</td>
					<td align="right"><? echo number_format($no_of_bags, 0, '', ','); ?></td>
					<td align="right"><? echo number_format($cone_per_bag, 0, '', ','); ?></td>
					<td align="right"><? echo number_format($qnty, 2); ?></td>
					<? if($data[3] == 1){?>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right"><?php echo number_format($totalAmount, 2, ".", ""); ?></td>
					<? } ?>
					<td align="right">&nbsp;</td>
				</tr>
				<tfoot>
					<tr>
						<th colspan="11" style="text-align: left;">In Word : <?php echo number_to_words(number_format($qnty, 2)); ?></th>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(7, $data[0], "930px");
			?>
		</div>
	</div>

<?
	exit();
}

if ($action == "yarn_receive_multy_return_print_2") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$issue_number = str_replace(",", "','", $data[1]);

	$issue_number_arr = explode(",", $data[1]);

	$sql = " select id, issue_number, received_id, issue_date, supplier_id, pi_id, remarks from  inv_issue_master where issue_number='$issue_number_arr[0]' and entry_form=8 and item_category=1 and status_active=1 and is_deleted=0";
	$dataArray = sql_select($sql);

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	//$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	// $country_arr = return_library_array("select id,country_name from lib_country", "id", "country_name");
	//$receive_arr = return_library_array("select id,recv_number from inv_receive_master", "id", "recv_number");
	$count_arr = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
?>
	<div style="width:1030px;">
		<table cellspacing="0" border="0" width="100%">
			<tr>

			<tr class="form_caption">

				<?
				$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td colspan="2" rowspan="2" valign="top">
					<?
					foreach ($data_array as $img_row) {
					?>
						<img src='../<? echo $img_row[csf('image_location')]; ?>' width='120' align="middle" />
					<?
					}
					?>
				</td>


				<td colspan="10" align="center"><strong style="font-size:xx-large"><? echo $company_library[$data[0]]; ?></strong><br><? echo show_company($data[0], '', ''); ?></td>
			</tr>
			<td colspan="10" style="font-size:x-large" align="center"><strong><u>Purchase Return/Delivery Challan/Gate Pass.</u></strong></td>
			</tr>
			<tr>
				<td colspan="12"><strong>Return To :</strong><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
			</tr>
			<tr>
				<td colspan="12">
					<? echo return_field_value("address_1", "lib_supplier", "id=" . $dataArray[0][csf('supplier_id')], "address_1"); ?>
				</td>
			</tr>
		</table>

		<div style="width:100%;">
			<table align="right" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="120">Return No.</th>
					<th width="80">Return Date</th>
					<th width="120">MRR</th>
					<th width="100">PI No</th>
					<th width="100">BTB LC No</th>
					<th width="100" align="center">Lot No</th>
					<th width="50" align="center">Count</th>
					<th width="250" align="center">Item Description</th>
					<th width="60" align="center">No Of Bag</th>
					<th width="60" align="center">No Of Cone</th>
					<th align="center">Return Qty.</th>
				</thead>
				<?
				$i = 1;
				$sql_dtls = "select c.issue_number,c.issue_date,c.received_id,c.pi_id,
				b.id as prod_id, b.product_name_details, a.id as tr_id, a.no_of_bags, a.cone_per_bag, a.store_id, a.cons_uom, a.cons_quantity,b.lot, b.yarn_count_id
				from inv_transaction a, product_details_master b, inv_issue_master c
				where c.id=a.mst_id and a.status_active=1 and a.company_id='$data[0]' and c.issue_number in('" . $issue_number . "') and a.item_category=1 and transaction_type=3 and a.prod_id=b.id and b.status_active=1 ";
				$sql_result = sql_select($sql_dtls);

				foreach ($sql_result as $row) {
					$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $i; ?></td>
						<td><? echo $row[csf('issue_number')]; ?></td>
						<td><? echo change_date_format($row[csf('issue_date')]); ?></td>
						<td>

							<? echo return_field_value("recv_number", "inv_receive_master", "id=" . $row[csf('received_id')], "recv_number"); ?>

						</td>
						<td><? echo return_field_value("pi_number", "com_pi_master_details", "id=" . $row[csf('pi_id')], "pi_number"); ?></td>
						<td>
							<p><?
								$btb_lc_no = return_field_value("a.lc_number as lc_number", " com_btb_lc_master_details a, com_btb_lc_pi b", "a.id=b.com_btb_lc_master_details_id and b.pi_id=" . $row[csf('pi_id')], "lc_number");
								echo $btb_lc_no;
								?></p>
						</td>
						<td><? echo $row[csf('lot')]; ?></td>
						<td><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></td>
						<td><? echo $row[csf('product_name_details')]; ?></td>
						<td align="right"><? echo $row[csf('no_of_bags')]; ?></td>
						<td align="right"><? echo $row[csf('cone_per_bag')]; ?></td>
						<td align="right"><? echo number_format($row[csf('cons_quantity')], 2); ?></td>
					</tr>
				<?php
					$qnty += $row[csf('cons_quantity')];
					$no_of_bags += $row[csf('no_of_bags')];
					$cone_per_bag += $row[csf('cone_per_bag')];
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="9">Total</td>
					<td align="right"><? echo number_format($no_of_bags, 0, '', ','); ?></td>
					<td align="right"><? echo number_format($cone_per_bag, 0, '', ','); ?></td>
					<td align="right"><? echo number_format($qnty, 2); ?></td>
				</tr>
				<tfoot>
					<tr>

						<th colspan="12" align="left">In Word : <? echo number_to_words(number_format($qnty, 2)); ?></th>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(7, $data[0], "930px");
			?>
		</div>
	</div>
<?
	exit();
}

/*
|--------------------------------------------------------------------------
| for action
| return_qty_popup
|--------------------------------------------------------------------------
|
*/
if ($action == "return_qty_popup") {
	echo load_html_head_contents("Item List", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<script>
		function func_onkeyup_return_qty() {
			//alert('su..re');
			var updateQty = 0;
			var returnQty = $('#txt_return_qty').val() * 1;
			var originalRcvQty = '<?php echo $originalRcvQty; ?>';
			var hdnReceiveBalanceQty = $('#hdnReceiveBalanceQty').val() * 1;
			var hdnReturnQty = $('#hdnReturnQty').val();
			var distribiutionMethod = $('#cbo_distribiution_method').val();

			if (returnQty < 0) {
				alert("Return quantity can't be less than zero.");
				$('#txt_return_qty').val(updateQty);
				var s = 1;
				$("#tblReturnQty tbody tr").each(function() {
					$('#textReturnQty_' + s).val('');
					s++;
				});
				return;
			}

			if (hdnReceiveBalanceQty < returnQty) {
				alert("Return quantity can't exceed receive balance quantity.");
				$('#txt_return_qty').val(updateQty);
				var s = 1;
				$("#tblReturnQty tbody tr").each(function() {
					$('#textReturnQty_' + s).val('');
					s++;
				});
				return;
			}

			var dataArr = hdnReturnQty.split(',');
			var i = 0;
			if (distribiutionMethod == 1) {
				for (i; i < dataArr.length; i++) {
					var data = dataArr[i].split('_');
					var orderReceiveQty = data[0];
					var totalReceiveQty = data[1];
					var originalQty = ((orderReceiveQty * returnQty) / totalReceiveQty).toFixed(2);
					var qty = ((orderReceiveQty * returnQty) / totalReceiveQty).toFixed(2);
					var sl = i + 1;
					$('#textReturnQty_' + sl).val(qty);
					$('#hdnOriginalReturnQty_' + sl).val(originalQty);
				}
			} else {
				for (i; i < dataArr.length; i++) {
					var sl = i + 1;
					$('#textReturnQty_' + sl).removeAttr('readonly');
				}
			}
		}

		//func_onchange_distribution_method
		function func_onchange_distribution_method(distribiutionMethod) {
			//alert('su..re');
			var returnQty = $('#txt_return_qty').val() * 1;
			var hdnReturnQty = $('#hdnReturnQty').val();
			var distribiutionMethod = $('#cbo_distribiution_method').val();
			var dataArr = hdnReturnQty.split(',');
			var i = 0;
			if (distribiutionMethod == 1) {
				for (i; i < dataArr.length; i++) {
					var data = dataArr[i].split('_');
					var orderQty = data[0];
					var totalQty = data[1];
					var qty = ((orderQty * returnQty) / totalQty).toFixed(2);
					var sl = i + 1;
					$('#textReturnQty_' + sl).val(qty).attr('readonly', 'readonly');
				}
			} else {
				for (i; i < dataArr.length; i++) {
					var sl = i + 1;
					$('#textReturnQty_' + sl).removeAttr('readonly');
				}
			}
		}

		//func_close
		function func_close() {
			var totalReturnQty = 0;
			var m = 1;
			$("#tblReturnQty tbody tr").each(function() {
				var ordRtnQty = $(this).find('#textReturnQty_' + m).val();
				totalReturnQty = (totalReturnQty * 1) + (ordRtnQty * 1);
				m++;
			});
			$('#txt_return_qty').val(totalReturnQty.toFixed(2));

			var hdnReturnString = '';
			var isError = 0;
			var msg = ' ';
			var i = 1;
			$("#tblReturnQty tbody tr").each(function() {
				var hdnOrderNo = $(this).find('#hdnOrderNo_' + i).val();
				var originalOrderRtnQty = $(this).find('#hdnOriginalReturnQty_' + i).val();
				var orderReturnQty = $(this).find('#textReturnQty_' + i).val();
				var orderPrevReturnQty = $(this).find('#hdnReturnQty_' + i).val();
				var orderBalanceQty = $(this).find('#hdnOrderBalanceQty_' + i).val();
				var returnQty = $('#txt_return_qty').val();
				var distribiutionMethod = $('#cbo_distribiution_method').val();

				/*
				if(orderReturnQty*1 > orderPrevReturnQty*1)
				{
					alert(orderReturnQty+'='+orderPrevReturnQty);
					isError = 1;
				}
				*/

				if (orderReturnQty * 1 > orderBalanceQty * 1) {
					isError = 1;
					msg = ' balance ';
				}

				if (orderReturnQty * 1 > 0) {
					if (hdnReturnString != '') {
						hdnReturnString += ',';
					}
					hdnReturnString += hdnOrderNo + '_' + orderReturnQty + '_' + originalOrderRtnQty;
				}
				i++;
			});

			if (isError == 1) {
				alert('Order return quantity is greater than order receive' + msg + 'quantity.');
				$('#txt_return_qty').val('');
				var s = 1;
				$("#tblReturnQty tbody tr").each(function() {
					$('#textReturnQty_' + s).val('');
					s++;
				});
				return;
			}

			$('#hdnReturnString').val(hdnReturnString);
			var z = 1;
			$("#tblReturnQty tbody tr").each(function() {
				$('#textReturnQty_' + z).val('');
				$('#hdnReturnQty_' + z).val('');
				$('#hdnOrderBalanceQty_' + z).val('');
				$('#hdnOrderNo_' + z).val('');
				$('#hdnOriginalReturnQty_' + z).val('');
				z++;
			});
			$('#hdnReturnQty').val('');
			$('#hdnReceiveBalanceQty').val('');
			//alert(hdnReturnString+'='+totalReturnQty);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<?php
		//for return qty
		$sql = "
			SELECT
				a.booking_id AS BOOKING_ID, c.trans_id AS TRANS_ID, c.po_breakdown_id AS PO_BREAKDOWN_ID, c.quantity AS QUANTITY 
			FROM
				inv_issue_master a
				INNER JOIN inv_transaction b ON a.id = b.mst_id
				INNER JOIN order_wise_pro_details c ON b.id = c.trans_id
			WHERE
				a.received_id = " . $received_id . "
				AND a.entry_form = 8
				AND a.status_active = 1
				AND a.is_deleted = 0
				AND b.item_category = 1
				AND b.transaction_type = 3
				AND b.status_active = 1
				AND b.is_deleted = 0
				AND c.trans_type = 3
				AND c.status_active = 1
				AND c.is_deleted = 0
				AND c.prod_id = " . $prod_id . "
		";
		//echo $sql;
		$sqlRslt = sql_select($sql);
		$poIdArr = array();
		$bookingIdArr = array();
		$dataReturnArr = array();
		foreach ($sqlRslt as $row) {
			$poIdArr[$row['PO_BREAKDOWN_ID']] = $row['PO_BREAKDOWN_ID'];
			$bookingIdArr[$row['BOOKING_ID']] = $row['BOOKING_ID'];

			if ($row['TRANS_ID'] != $transId) {
				$dataReturnArr[$row['TRANS_ID']]['return_qnty'] += $row['QUANTITY'];
			} else {
				$dataReturnArr[$row['TRANS_ID']][$row['PO_BREAKDOWN_ID']]['current_return_qnty'] += $row['QUANTITY'];
			}
		}
		//echo "<pre>";
		//print_r($dataReturnArr); die;

		//for receive qty
		$sqlRcv = "
			SELECT
				a.booking_id AS BOOKING_ID, c.trans_id AS TRANS_ID, c.po_breakdown_id AS PO_BREAKDOWN_ID,c.is_sales AS IS_SALES, c.quantity AS QUANTITY 
			FROM
				inv_receive_master a
				INNER JOIN inv_transaction b ON a.id = b.mst_id
				INNER JOIN order_wise_pro_details c ON b.id = c.trans_id
			WHERE
				a.id = '" . $received_id . "'
				AND b.id = " . $rcv_trans_id . "
				AND a.status_active = 1
				AND a.is_deleted = 0
				AND b.item_category = 1
				AND b.transaction_type in (1,4)
				AND b.status_active = 1
				AND b.is_deleted = 0
				AND c.prod_id = " . $prod_id . "
				AND c.trans_type in (1,4)
				AND c.status_active = 1
				AND c.is_deleted = 0
		";
		//echo $sqlRcv;
		$sqlRcvRslt = sql_select($sqlRcv);
		$dataRcvArr = array();
		foreach ($sqlRcvRslt as $row) {
			if ($row['IS_SALES'] == 1) {
				$fsoPoIdArr[$row['PO_BREAKDOWN_ID']] = $row['PO_BREAKDOWN_ID'];
			} else {
				$poIdArr[$row['PO_BREAKDOWN_ID']] = $row['PO_BREAKDOWN_ID'];
			}

			$dataRcvArr[$row['TRANS_ID']][$row['PO_BREAKDOWN_ID']]['receive_qnty'] += $row['QUANTITY'];
			$dataRcvArr[$row['TRANS_ID']][$row['PO_BREAKDOWN_ID']]['is_sales'] = $row['IS_SALES'];
		}
		//echo "<pre>";
		//print_r($dataRcvArr); die;

		//data preparing here
		$dataArr = array();
		$totalReceiveQty = 0;
		$totalReturnQty = 0;
		$hdnReceiveBalanceQty = 0;
		foreach ($dataRcvArr as $transId => $orderArr) {
			foreach ($orderArr as $orderId => $row) {
				$dataArr[$transId][$orderId]['receive_qnty'] = $row['receive_qnty'];
				$dataArr[$transId][$orderId]['return_qnty'] = $dataReturnArr[$transId][$orderId]['return_qnty'];
				$dataArr[$transId][$orderId]['balance_qnty'] = $dataArr[$transId][$orderId]['receive_qnty'] - $dataArr[$transId][$orderId]['return_qnty'] * 1;
				$dataArr[$transId][$orderId]['current_return_qnty'] = $dataReturnArr[$transId][$orderId]['current_return_qnty'];
				$dataArr[$transId][$orderId]['is_sales'] = $row['is_sales'];

				$totalReceiveQty += $dataArr[$transId][$orderId]['receive_qnty'];
				$totalReturnQty += $dataArr[$transId][$orderId]['return_qnty'];
				$hdnReceiveBalanceQty += $dataArr[$transId][$orderId]['balance_qnty'];
			}
		}
		//echo "<pre>";
		//print_r($dataArr);

		//for order details
		$order_details = return_library_array("SELECT id, po_number FROM wo_po_break_down WHERE id IN(" . implode(',', $poIdArr) . ")", 'id', 'po_number');
		$fso_details = return_library_array("SELECT id, job_no FROM fabric_sales_order_mst WHERE id IN(" . implode(',', $fsoPoIdArr) . ")", 'id', 'job_no');
		?>
		<div align="center">
			<div style="width:320px; margin-top:25px">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300">
					<thead>
						<th>Return Qty</th>
						<th>Distribution Method</th>
					</thead>
					<tr class="general">
						<td>
							<input type="text" name="txt_return_qty" id="txt_return_qty" class="text_boxes_numeric" value="<?php echo $return_qnty; ?>" style="width:120px" onKeyUp="func_onkeyup_return_qty()" />
						</td>
						<td>
							<?php
							// onBlur="func_onkeyup_total_requisition_qty(this.value)"
							$distribiution_method = array(1 => "Proportionately", 2 => "Manually");
							echo create_drop_down("cbo_distribiution_method", 140, $distribiution_method, "", 0, "", $distribution_method, "func_onchange_distribution_method(this.value);", 0);
							?>
						</td>
					</tr>
				</table>
			</div>
			<div style="margin-top:10px;">
				<form name="yarnReqQnty_2" id="yarnReqQnty_2" autocomplete="off">
					<table id="tblReturnQty" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
						<thead>
							<tr>
								<th width="40">Sl No</th>
								<th width="120">Order No</th>
								<th width="100">Receive Qty</th>
								<th width="100">Prev. Return Qty</th>
								<th width="100">Balance Qty</th>
								<th width="100">Return Qty</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sl = 0;
							$hdnReturnQty = '';
							$totalRequisitionQty = 0;
							$allocationBalanceQty = 0;
							foreach ($dataArr as $transId => $orderArr) {
								foreach ($orderArr as $orderId => $row) {
									$sl++;
									if ($hdnReturnQty != '') {
										$hdnReturnQty .= ',';
									}
									$hdnReturnQty .= $row['receive_qnty'] . '_' . $totalReceiveQty;
									//current_receive_qnty

									$po_number = ($row['is_sales'] == 1) ? $fso_details[$orderId] : $order_details[$orderId];
							?>
									<tr valign="middle">
										<td align="center"><?php echo $sl; ?></td>
										<td><?php echo $po_number; ?></td>
										<td align="right"><?php echo number_format($row['receive_qnty'], 2, '.', ''); ?></td>
										<td align="right"><?php echo number_format($row['return_qnty'], 2, '.', ''); ?></td>
										<td align="right"><?php echo number_format($row['balance_qnty'], 2, '.', ''); ?></td>
										<td>
											<input type="text" name="textReturnQty[]" id="textReturnQty_<?php echo $sl; ?>" class="text_boxes_numeric" style="text-align:right" readonly value="<?php echo number_format($row['current_return_qnty'], 2, '.', ''); ?>" />
											<input type="hidden" name="hdnOriginalReturnQty[]" id="hdnOriginalReturnQty_<?php echo $sl; ?>" class="text_boxes_numeric" style="text-align:right" readonly value="<?php echo number_format($row['return_qnty'], 2, '.', ''); ?>" />
											<input type="hidden" name="hdnReturnQty[]" id="hdnReturnQty_<?php echo $sl; ?>" class="text_boxes_numeric" value="<?php echo number_format($row['receive_qnty'], 2, '.', ''); ?>" style="text-align:right" readonly />
											<input type="hidden" name="hdnOrderBalanceQty[]" id="hdnOrderBalanceQty_<?php echo $sl; ?>" class="text_boxes_numeric" value="<?php echo number_format($row['balance_qnty'], 2, '.', ''); ?>" style="text-align:right" readonly />
											<input type="hidden" name="hdnOrderNo[]" id="hdnOrderNo_<?php echo $sl; ?>" class="text_boxes_numeric" value="<?php echo $orderId; ?>" style="text-align:right" readonly />
										</td>
									</tr>
							<?php
								}
							}

							?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="2">Total</th>
								<th><?php echo number_format($totalReceiveQty, 2, '.', ''); ?></th>
								<th><?php echo number_format($totalReturnQty, 2, '.', ''); ?></th>
								<th><?php echo number_format($hdnReceiveBalanceQty, 2, '.', ''); ?></th>
								<th>
									<input type="text" name="hdnReturnString" id="hdnReturnString" class="text_boxes" value="" readonly />
									<input type="hidden" name="hdnReturnQty" id="hdnReturnQty" class="text_boxes" value="<? echo $hdnReturnQty; ?>" style="text-align:right" />
									<input type="hidden" name="hdnReceiveBalanceQty" id="hdnReceiveBalanceQty" class="text_boxes" value="<? echo number_format($hdnReceiveBalanceQty, 2, '.', ''); ?>" style="text-align:right" />
								</th>
							</tr>
						</tfoot>
					</table>
				</form>
			</div>
			<div style="margin-top:10px;">
				<table width="660" id="table_id">
					<tr>
						<td align="center">
							<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="func_close()" style="width:100px" />
						</td>
					</tr>
				</table>
			</div>
		</div>
	</body>
	<script>
		//func_onkeyup_total_requisition_qty();
	</script>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}
?>