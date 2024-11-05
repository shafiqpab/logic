<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../../includes/common.php');
$user_name = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];



if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "- All Buyer -", $selected, "");
	exit();
}

if ($action == "report_generate") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$company_name = str_replace("'", "", $cbo_company_name);
	$cbo_knitting_source = str_replace("'", "", $cbo_knitting_source);
	$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
	$txt_sales_order_no = str_replace("'", "", $txt_sales_order_no);
	$txt_fab_booking_no = str_replace("'", "", $txt_fab_booking_no);
	$txt_prog_no = str_replace("'", "", $txt_prog_no);
	$txt_req_no = str_replace("'", "", $txt_req_no);
	$txt_date_from_requ = str_replace("'", "", $txt_date_from_requ);
	$txt_date_to_requ = str_replace("'", "", $txt_date_to_requ);
	$txt_date_from = str_replace("'", "", $txt_date_from);
	$txt_date_to = str_replace("'", "", $txt_date_to);
	$cbo_get_upto_qnty = str_replace("'", "", $cbo_get_upto_qnty);
	$year_id = str_replace("'", "", $cbo_year_selection);
	$txt_qnty = str_replace("'", "", $txt_qnty);
	$txt_ir_no = str_replace("'", "", $txt_ir_no);

	$compare_operator = "";
	if ($cbo_get_upto_qnty == 1) $compare_operator = ">";
	else if ($cbo_get_upto_qnty == 2) $compare_operator = "<";
	else if ($cbo_get_upto_qnty == 3) $compare_operator = ">=";
	else if ($cbo_get_upto_qnty == 4) $compare_operator = "<=";
	else if ($cbo_get_upto_qnty == 5) $compare_operator = "==";

	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_name_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$buyer_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$supplierArr = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$machine_no_arr = return_library_array("select id,machine_no from  lib_machine_name", "id", "machine_no");
	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");

	$sql_cond = "";
	if ($cbo_buyer_name > 0) $sql_cond .= " and a.buyer_id=$cbo_buyer_name";
	if ($txt_fab_booking_no != "") $sql_cond .= " and a.booking_no='$txt_fab_booking_no'";
	if ($txt_prog_no != "") $sql_cond .= " and b.id='$txt_prog_no'";
	if ($txt_req_no != "") $sql_cond .= " and c.requisition_no='$txt_req_no'";
	if ($cbo_knitting_source > 0) $knitting_cond = " and b.knitting_source='$cbo_knitting_source'";

	if ($year_id != 0) $year_cond = " and to_char(insert_date,'yyyy')=$year_id";
	else $year_cond = "";

	$sales_order_data = array();
	$sales_book_no = "";
	$all_s_booking_arr = array();
	if ($txt_sales_order_no != "") {
		$sql_sales = "select id, job_no, sales_booking_no, within_group,buyer_id,booking_without_order,style_ref_no from fabric_sales_order_mst where job_no='$txt_sales_order_no' $year_cond and status_active=1  order by id desc";
		//echo $sql_sales;
		//die;
		$sql_sales_result = sql_select($sql_sales);

		if (count($sql_sales_result) == 0) {
?>
			<div class="alert alert-danger">Data not found. Please try again.</div>
		<?
			die();
		}

		$sbookingNoChk = array();

		if (count($sql_sales_result) > 0) {
			foreach ($sql_sales_result as $val) {
				if ($sbookingNoChk[$val[csf('sales_booking_no')]] == "") {
					$sbookingNoChk[$val[csf('sales_booking_no')]] = $val[csf('sales_booking_no')];
					array_push($all_s_booking_arr, $val[csf('sales_booking_no')]);
				}
			}
		}
	}

	if ($txt_ir_no != "") {
		$job_info_sql = "SELECT b.id as prog_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,wo_booking_mst c,wo_po_break_down d, gbl_temp_engine e where a.id=b.mst_id and a.booking_no=c.booking_no and c.job_no=d.job_no_mst and b.is_sales=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.grouping='$txt_ir_no' group by b.id";
		//echo $job_info_sql;die;
		if (count(sql_select($job_info_sql)) == 0) {
		?>
			<div class="alert alert-danger">Data not found. Please try again.</div>
		<?
			die();
		}
		$all_prog_arr = array();
		foreach (sql_select($job_info_sql) as $row) {
			if ($progNoChk[$row[csf('prog_no')]] == "") {
				$progNoChk[$row[csf('prog_no')]] = $row[csf('prog_no')];
				array_push($all_prog_arr, $row[csf('prog_no')]);
			}
		}
	}

	//echo "<pre>";print_r($all_s_booking_arr);die;

	if (!empty($all_s_booking_arr)) {
		$sales_booking_cond = " " . where_con_using_array($all_s_booking_arr, 1, 'a.booking_no') . "";
	}
	if (!empty($all_prog_arr)) {
		$prog_cond = " " . where_con_using_array($all_prog_arr, 1, 'b.id') . "";
	}

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = " . $user_name . " and ref_from in(1,2,3,4) and  ENTRY_FORM=83");
	oci_commit($con);
	disconnect($con);

	if ($txt_date_from != "" && $txt_date_to != "") {
		$sql_demand = "select b.requisition_no, b.mst_id as demand_id, b.demand_qnty, b.save_string
		from ppl_yarn_demand_entry_dtls b, ppl_yarn_demand_entry_mst c
		where b.mst_id=c.id and c.demand_date between '$txt_date_from' and '$txt_date_to' and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";

		//echo $sql_demand;die;

		$sql_demand_result = sql_select($sql_demand);
		$demand_data = $demand_check = array();
		$all_demand_req = "";
		foreach ($sql_demand_result as $row) {
			$save_ref = explode(",", $row[csf("save_string")]);
			foreach ($save_ref as $data) {
				$data_ref = explode("_", $data);
				$demand_data[$row[csf("requisition_no")]][$data_ref[0]]["demand_qnty"] += $data_ref[2];
			}
			if ($demand_check[$row[csf("requisition_no")]][$row[csf("demand_id")]] == "") {
				$demand_check[$row[csf("requisition_no")]][$row[csf("demand_id")]] = $row[csf("demand_id")];
				$all_demand_req .= $row[csf("requisition_no")] . ",";
				$demand_data[$row[csf("requisition_no")]][0]["demand_id"] .= $row[csf("demand_id")] . ",";
			}
		}

		$all_demand_req = chop($all_demand_req, ",");
		if ($all_demand_req == "") $all_demand_req = 0;

		$sql = "SELECT a.company_id, a.buyer_id, a.booking_no, b.id as prog_no, b.knitting_source, b.knitting_party, b.program_qnty, c.requisition_no, c.yarn_qnty as req_qnty, c.prod_id, d.yarn_count_id, d.yarn_type, d.color as yarn_color, d.yarn_comp_type1st, d.brand, d.lot, d.supplier_id, c.requisition_date, b.machine_id
		from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c, product_details_master d
		where a.id=b.mst_id and b.id=c.knit_id and c.prod_id=d.id and b.is_sales=1 and a.company_id=$company_name $knitting_cond $sql_cond and  c.requisition_no in(select b.requisition_no
		from ppl_yarn_demand_entry_dtls b, ppl_yarn_demand_entry_mst c
		where b.mst_id=c.id and c.demand_date between '$txt_date_from' and '$txt_date_to' and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $sales_booking_cond $prog_cond";
		//echo $sql;die;

		$sql_result = sql_select($sql);
		if (count($sql_result) == 0) {
		?>
			<div class="alert alert-danger">Data not found. Please try again.</div>
		<?
			die();
		}
		$req_check = array();
		$progNoChk = array();
		$sbookNoChk = array();
		$all_prog_no_arr = array();
		$all_req_no_arr = array();
		$all_s_book_arr = array();
		foreach ($sql_result as $row) {
			if ($req_check[$row[csf("requisition_no")]] == "") {
				$req_check[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
				//$all_req.=$row[csf("requisition_no")].",";
				$all_req_no_arr[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
			}

			if ($progNoChk[$row[csf('prog_no')]] == "") {
				$progNoChk[$row[csf('prog_no')]] = $row[csf('prog_no')];
				$all_prog_no_arr[$row[csf("prog_no")]] = $row[csf("prog_no")];
			}

			if ($sbookNoChk[$row[csf('booking_no')]] == "") {
				$sbookNoChk[$row[csf('booking_no')]] = $row[csf('booking_no')];
				array_push($all_s_book_arr, $row[csf('booking_no')]);
			}
		}
	} else {
		if ($txt_date_from_requ != "" && $txt_date_to_requ != "") $sql_cond .= " and  c.requisition_date  between '$txt_date_from_requ' and '$txt_date_to_requ'";

		$sql = "SELECT a.company_id, a.buyer_id, a.booking_no, b.id as prog_no, b.knitting_source, b.knitting_party, b.program_qnty, c.requisition_no, c.yarn_qnty as req_qnty, c.prod_id, d.yarn_count_id, d.yarn_type, d.color as yarn_color, d.yarn_comp_type1st, d.brand, d.lot, d.supplier_id, c.requisition_date, b.machine_id
		from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c, product_details_master d
		where a.id=b.mst_id and b.id=c.knit_id and c.prod_id=d.id and b.is_sales=1 and a.company_id=$company_name $knitting_cond $sql_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $sales_booking_cond $prog_cond";
		echo $sql;
		die;
		$sql_result = sql_select($sql);
		if (count($sql_result) == 0) {
		?>
			<div class="alert alert-danger">Data not found. Please try again.</div>
	<?
			die();
		}
		$req_check = array();
		$progNoChk = array();
		$sbookNoChk = array();
		$all_req_no_arr = array();
		$all_prog_no_arr = array();
		foreach ($sql_result as $row) {
			if ($req_check[$row[csf("requisition_no")]] == "") {
				$req_check[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
				//$all_req.=$row[csf("requisition_no")].",";
				$all_req_no_arr[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
			}
			if ($progNoChk[$row[csf('prog_no')]] == "") {
				$progNoChk[$row[csf('prog_no')]] = $row[csf('prog_no')];
				$all_prog_no_arr[$row[csf("prog_no")]] = $row[csf("prog_no")];
			}
		}

		$all_req_no_arr = array_filter($all_req_no_arr);
		if (!empty($all_req_no_arr)) {
			fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 83, 2, $all_req_no_arr, $empty_arr);
			//die;
			$sql_demand = "SELECT b.requisition_no, b.mst_id as demand_id, b.demand_qnty, b.save_string
			from ppl_yarn_demand_entry_dtls b, ppl_yarn_demand_entry_mst c, gbl_temp_engine d
			where b.mst_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and b.requisition_no=d.ref_val  and d.user_id = " . $user_name . " and d.ref_from=2 and d.entry_form=83 ";
			//echo $sql_demand; die;
			$sql_demand_result = sql_select($sql_demand);
			$demand_data = $demand_check = array();
			$all_demand_req = "";
			foreach ($sql_demand_result as $row) {
				$save_ref = explode(",", $row[csf("save_string")]);
				foreach ($save_ref as $data) {
					$data_ref = explode("_", $data);
					$demand_data[$row[csf("requisition_no")]][$data_ref[0]]["demand_qnty"] += $data_ref[2];
				}
				if ($demand_check[$row[csf("requisition_no")]][$row[csf("demand_id")]] == "") {
					$demand_check[$row[csf("requisition_no")]][$row[csf("demand_id")]] = $row[csf("demand_id")];
					$demand_data[$row[csf("requisition_no")]][0]["demand_id"] .= $row[csf("demand_id")] . ",";
				}
				//$demand_data[$row[csf("requisition_no")]]["demand_qnty"]+=$row[csf("demand_qnty")];
			}
		}
	}

	$all_prog_no_arr = array_filter($all_prog_no_arr);
	//var_dump($all_prog_no_arr);die;

	if (!empty($all_prog_no_arr)) {
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 83, 1, $all_prog_no_arr, $empty_arr);
		//die;

		$knit_prod_sql = "SELECT a.booking_id,a.booking_no, b.grey_receive_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, gbl_temp_engine c where a.id=b.mst_id and  a.company_id=$company_name and a.receive_basis=2 and a.item_category=13 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=c.ref_val and c.user_id = " . $user_name . " and c.ref_from=1 and c.entry_form=83";

		//echo $knit_prod_sql;die;

		$production_info_arr = array();
		foreach (sql_select($knit_prod_sql) as $row) {
			$production_info_arr[$row[csf('booking_no')]]['grey_receive_qnty'] += $row[csf('grey_receive_qnty')];
		}
		//var_dump($production_info_arr);die;
		$sql_sales_info = "SELECT d.job_no,d.sales_booking_no,d.within_group,d.buyer_id,d.booking_without_order,d.style_ref_no,b.id ,c.po_id,d.po_buyer
		FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c,fabric_sales_order_mst d, gbl_temp_engine e
	    WHERE a.id = b.mst_id AND b.id = c.dtls_id and c.po_id=d.id AND a.company_id = $company_name AND b.is_sales = 1 and b.id=e.ref_val and e.user_id = " . $user_name . " and e.ref_from=1 and e.entry_form=83";
		//echo $sql_sales_info;die; 
		$sql_sales_info_result = sql_select($sql_sales_info);
		foreach ($sql_sales_info_result as $row) {
			$sales_order_data[$row[csf("sales_booking_no")]]["job_no"] = $row[csf("job_no")];
			$sales_order_data[$row[csf("sales_booking_no")]]["within_group"] = $row[csf("within_group")];
			$sales_order_data[$row[csf("sales_booking_no")]]["buyer_id"] = $row[csf("buyer_id")];
			$sales_order_data[$row[csf("sales_booking_no")]]["po_buyer"] = $row[csf("po_buyer")];
			$sales_order_data[$row[csf("sales_booking_no")]]["booking_without_order"] = $row[csf("booking_without_order")];
			$sales_order_data[$row[csf("sales_booking_no")]]["style_ref_no"] = $row[csf("style_ref_no")];
		}

		$job_info_sql = "SELECT b.id as prog_no,d.grouping from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,wo_booking_mst c,wo_po_break_down d, gbl_temp_engine e where a.id=b.mst_id and a.booking_no=c.booking_no and c.job_no=d.job_no_mst and b.is_sales=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.id=e.ref_val and e.user_id = " . $user_name . " and e.ref_from=1 and e.entry_form=83";
		//echo $job_info_sql;die;
		$job_info_arr = array();
		foreach (sql_select($job_info_sql) as $row) {
			$job_info_arr[$row[csf('prog_no')]]['int_ref'] = $row[csf('grouping')];
		}
	}

	$all_req_no_arr = array_filter($all_req_no_arr);
	if (!empty($all_req_no_arr)) {
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 83, 3, $all_req_no_arr, $empty_arr);
		//die;
		$yarn_issue_details_arr = array();
		$yarn_issue_remark_arr = array();

		$yarn_sql = sql_select("SELECT a.id as issue_id,a.remarks, b.requisition_no as requ_no, b.prod_id, b.store_id, b.cons_quantity as issue_qnty, b.return_qnty from inv_issue_master a,inv_transaction b, gbl_temp_engine c where a.id=b.mst_id and a.entry_form=3 and a.company_id=$company_name and a.issue_basis in(3,8) and  b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.requisition_no=c.ref_val and c.user_id = " . $user_name . " and c.ref_from=3 and c.entry_form=83 ");

		$all_issue_id_arr = array();
		$issueId_check = array();
		foreach ($yarn_sql as $row) {
			$yarn_issue_details_arr[$row[csf('requ_no')]][$row[csf('prod_id')]]['qty'] += $row[csf('issue_qnty')];
			$yarn_issue_details_arr[$row[csf('requ_no')]][$row[csf('prod_id')]]['ret_qty'] += $row[csf('return_qnty')];
			$yarn_issue_details_arr[$row[csf('requ_no')]][$row[csf('prod_id')]]['remark'] = $row[csf('remarks')];
			$issue_id_arr[$row[csf('issue_id')]] = $row[csf('issue_id')];

			if ($issueId_check[$row[csf("issue_id")]] == "") {
				$issueId_check[$row[csf("issue_id")]] = $row[csf("issue_id")];
				$all_issue_id_arr[$row[csf("issue_id")]] = $row[csf("issue_id")];
			}
		}
	}

	$all_issue_id_arr = array_filter($all_issue_id_arr);
	if (!empty($all_issue_id_arr)) {
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 83, 4, $all_issue_id_arr, $empty_arr);
		//die;
		$yarn_iss_ret_sql = sql_select("SELECT b.requisition_no, b.prod_id, b.store_id, b.cons_quantity as issue_return_qnty, b.cons_reject_qnty
		from inv_receive_master a,inv_transaction b, gbl_temp_engine c
		where a.id=b.mst_id and a.entry_form=9 and a.company_id=$company_name and a.receive_basis in(3,8) and  b.item_category=1 and b.transaction_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.issue_id=c.ref_val and c.user_id = " . $user_name . " and c.ref_from=4 and c.entry_form=83 "); //$issue_ids_cond

		foreach ($yarn_iss_ret_sql as $row) {
			$yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['iss_return_qty'] += $row[csf('issue_return_qnty')];
			$yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['iss_reject_qty'] += $row[csf('cons_reject_qnty')];
		}
	}

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = " . $user_name . " and ref_from in(1,2,3,4) and entry_form=83");
	oci_commit($con);
	disconnect($con);

	ob_start();
	?>
	<fieldset style="width:2710px;">
		<table cellpadding="0" cellspacing="0" width="2430">
			<tr>
				<td align="center" width="100%" style="font-size:16px"><strong>Requisition Against Demand Status</strong> <br> <b>
						<?
						if ($start_date != '') echo change_date_format($start_date) . ' To ' . change_date_format($end_date);
						else echo ''; ?></b>
				</td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2690" class="rpt_table" align="left">
			<thead>
				<th width="30">SL</th>
				<th width="120">Buyer</th>
				<th width="120">Style Ref.</th>
				<th width="120">FSO No</th>
				<th width="100">IR/IB</th>
				<th width="120">Fabric Booking No</th>
				<th width="60">Prog. No</th>
				<th width="120">Knitting Company</th>
				<th width="60">Req. No</th>
				<th width="70">Req. Date</th>
				<th width="130">M/C No</th>
				<th width="60">Y. Count</th>
				<th width="150">Y Composition</th>
				<th width="80">Y. Type</th>
				<th width="150">Supplier</th>
				<th width="100">Brand</th>
				<th width="80">Y. Color</th>
				<th width="70">Lot</th>
				<th width="80">Program Qty.</th>
				<th width="80">Req Qty.</th>
				<th width="80">Demand Qty.</th>
				<th width="80">Demand Balance</th>
				<th width="80">Issue Qty</th>
				<th width="80">Issue Balance Qty</th>
				<th width="80">Returnble Qty</th>
				<th width="80">Production Qty</th>
				<th width="80">Iss. Returned Qty</th>
				<th width="80">Balance Qty</th>
				<th>Remarks</th>
			</thead>
		</table>
		<div style="width:2710px; overflow-y:scroll; max-height:330px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2690" class="rpt_table" id="tbl_list_search" align="left">
				<?
				$i = 1;
				if ($compare_operator != "" && $txt_qnty > 0) {
					foreach ($sql_result as $row) {
						$d_balance = $row[csf("req_qnty")] - $demand_data[$row[csf("requisition_no")]][$row[csf("prod_id")]]["demand_qnty"];

						//echo $d_balance." ".$compare_operator." ".$txt_qnty."==";
						eval("\$cond=$d_balance $compare_operator $txt_qnty?1:0;");
						if ($cond) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							$issue_with_reject_qnty = $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['iss_return_qty'] + $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['iss_reject_qty'];
							$iss_balance = $demand_data[$row[csf("requisition_no")]][$row[csf("prod_id")]]["demand_qnty"] - $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['qty'];
				?>
							<tr bgcor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30" align="center"><? echo $i; ?></td>
								<td width="120">
									<p>
										<?
										if ($sales_order_data[$row[csf("booking_no")]]["within_group"] == 2) {
											echo $buyer_name_arr[$sales_order_data[$row[csf("booking_no")]]["buyer_id"]];
										} else {
											echo $buyer_name_arr[$sales_order_data[$row[csf("booking_no")]]["po_buyer"]];
										}
										?>&nbsp;</p>
								</td>
								<td width="120">
									<p><? echo $sales_order_data[$row[csf("booking_no")]]["style_ref_no"]; ?>&nbsp;</p>
								</td>
								<td width="120">
									<p><? echo $row[csf("booking_no")]; ?>&nbsp;</p>
								</td>
								<td width="100">
									<p><? echo $job_info_arr[$row[csf('prog_no')]]['int_ref']; ?>&nbsp;</p>
								</td>
								<td width="100">
									<p><? echo $sales_order_data[$row[csf("booking_no")]]["job_no"]; ?>&nbsp;</p>
								</td>
								<td width="120">
									<p><? echo $row[csf("booking_no")]; ?>&nbsp;</p>
								</td>
								<td width="60" align="center">
									<p><? echo $row[csf("prog_no")]; ?>&nbsp;</p>
								</td>
								<?
								if ($row[csf("knitting_source")] == 1)
									$knit_com = $company_library[$row[csf("knitting_party")]];
								else
									$knit_com = $supplierArr[$row[csf("knitting_party")]];

								?>
								<td width="120">
									<p><? echo $knit_com; ?>&nbsp;</p>
								</td>
								<td width="60" align="center">
									<p><? echo $row[csf("requisition_no")]; ?>&nbsp;</p>
								</td>
								<td width="70" align="center">
									<p><? if ($row[csf("requisition_date")] != "" && $row[csf("requisition_date")] != "0000-00-00") echo change_date_format($row[csf("requisition_date")]); ?>&nbsp;</p>
								</td>
								<td width="130">
									<p><?
										$mc_arr = explode(",", $row[csf("machine_id")]);
										$all_mc_no = "";
										foreach ($mc_arr as $mc_id) {
											$all_mc_no .= $machine_no_arr[$mc_id] . ",";
										}
										$all_mc_no = chop($all_mc_no, ",");
										echo $all_mc_no;
										?>&nbsp;</p>
								</td>
								<td width="60" align="center">
									<p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?>&nbsp;</p>
								</td>
								<td width="150">
									<p><? echo $composition[$row[csf("yarn_comp_type1st")]]; ?>&nbsp;</p>
								</td>
								<td width="80">
									<p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p>
								</td>
								<td width="150">
									<p><? echo $supplierArr[$row[csf("supplier_id")]]; ?>&nbsp;</p>
								</td>
								<td width="100">
									<p><? echo $brand_name_arr[$row[csf("brand")]]; ?>&nbsp;</p>
								</td>
								<td width="80">
									<p><? echo $color_library[$row[csf("yarn_color")]]; ?>&nbsp;</p>
								</td>
								<td width="70">
									<p><? echo $row[csf("lot")]; ?>&nbsp;</p>
								</td>
								<td width="80" align="right"><? echo number_format($row[csf("program_qnty")], 2); ?></td>
								<td width="80" align="right"><? echo number_format($row[csf("req_qnty")], 2); ?></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_issue(<? echo $row[csf("requisition_no")]; ?>,<? echo $row[csf("prod_id")]; ?>,'demand_popup','<? echo $txt_date_from; ?>','<? echo $txt_date_to; ?>');"><? echo number_format($demand_data[$row[csf("requisition_no")]][$row[csf("prod_id")]]["demand_qnty"], 2); ?></a></td>
								<td width="80" align="right"><? echo number_format($d_balance, 2); ?></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_issue(<? echo $row[csf("requisition_no")]; ?>,<? echo $row[csf("prod_id")]; ?>,'issueQnty_popup');"><? echo number_format($yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['qty'], 2); ?></a></td>
								<td width="80" align="right" title="Demand Qty-Issue Qty"><? echo number_format($iss_balance, 2, '.', ''); ?></td>
								<td width="80" align="right"><? echo number_format($yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['ret_qty'], 2); ?></td>
								<td width="80" align="right"><? echo number_format($production_info_arr[$row[csf('prog_no')]]['grey_receive_qnty'], 2); ?></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_issue(<? echo $row[csf("requisition_no")]; ?>,<? echo $row[csf("prod_id")]; ?>,'issueReturnQnty_popup');"><? echo number_format($issue_with_reject_qnty, 2); ?></a></td>
								<td width="80" align="right">
									<?
									$balance_qnty = $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['ret_qty'] - ($issue_with_reject_qnty);
									echo number_format($balance_qnty, 2, ".", "");
									?>
								</td>
								<td>&nbsp;</td>
							</tr>


						<?
							$i++;
							$total_program_qty += $row[csf("program_qnty")];
							$total_rec_qty += $row[csf("req_qnty")];
							$total_demand_qty += $demand_data[$row[csf("requisition_no")]][$row[csf("prod_id")]]["demand_qnty"];
							$total_demand_blanace += $d_balance;
							$total_demand_issue += $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['qty'];
							$total_production_qnty += $production_info_arr[$row[csf('prog_no')]]['grey_receive_qnty'];
							$d_balance = 0;
						}
					}
				} else {
					foreach ($sql_result as $row) {
						$d_balance = $row[csf("req_qnty")] - $demand_data[$row[csf("requisition_no")]][$row[csf("prod_id")]]["demand_qnty"];
						$iss_balance = $demand_data[$row[csf("requisition_no")]][$row[csf("prod_id")]]["demand_qnty"] - $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['qty'];
						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
						$issue_with_reject_qnty = $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['iss_return_qty'] + $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['iss_reject_qty'];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="120">
								<p>
									<?
									if ($sales_order_data[$row[csf("booking_no")]]["within_group"] == 2) {
										echo $buyer_name_arr[$sales_order_data[$row[csf("booking_no")]]["buyer_id"]];
									} else {
										echo $buyer_name_arr[$sales_order_data[$row[csf("booking_no")]]["po_buyer"]];
									}
									?>&nbsp;</p>
							</td>
							<td width="120">
								<p><? echo $sales_order_data[$row[csf("booking_no")]]["style_ref_no"]; ?>&nbsp;</p>
							</td>
							<td width="120">
								<p><? echo $sales_order_data[$row[csf("booking_no")]]["job_no"]; ?>&nbsp;</p>
							</td>
							<td width="100">
								<p><? echo $job_info_arr[$row[csf('prog_no')]]['int_ref']; ?>&nbsp;</p>
							</td>
							<td width="120">
								<p><? echo $row[csf("booking_no")]; ?>&nbsp;</p>
							</td>
							<td width="60" align="center">
								<p><? echo $row[csf("prog_no")]; ?>&nbsp;</p>
							</td>
							<?
							if ($row[csf("knitting_source")] == 1)
								$knit_com = $company_library[$row[csf("knitting_party")]];
							else
								$knit_com = $supplierArr[$row[csf("knitting_party")]];

							?>
							<td width="120">
								<p><? echo $knit_com; ?>&nbsp;</p>
							</td>
							<td width="60" align="center">
								<p><? echo $row[csf("requisition_no")]; ?>&nbsp;</p>
							</td>
							<td width="70" align="center">
								<p><? if ($row[csf("requisition_date")] != "" && $row[csf("requisition_date")] != "0000-00-00") echo change_date_format($row[csf("requisition_date")]); ?>&nbsp;</p>
							</td>
							<td width="130">
								<p><?
									$mc_arr = explode(",", $row[csf("machine_id")]);
									$all_mc_no = "";
									foreach ($mc_arr as $mc_id) {
										$all_mc_no .= $machine_no_arr[$mc_id] . ",";
									}
									$all_mc_no = chop($all_mc_no, ",");
									echo $all_mc_no;
									?>&nbsp;</p>
							</td>
							<td width="60" align="center">
								<p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?>&nbsp;</p>
							</td>
							<td width="150">
								<p><? echo $composition[$row[csf("yarn_comp_type1st")]]; ?>&nbsp;</p>
							</td>
							<td width="80">
								<p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p>
							</td>
							<td width="150">
								<p><? echo $supplierArr[$row[csf("supplier_id")]]; ?>&nbsp;</p>
							</td>
							<td width="100">
								<p><? echo $brand_name_arr[$row[csf("brand")]]; ?>&nbsp;</p>
							</td>
							<td width="80">
								<p><? echo $color_library[$row[csf("yarn_color")]]; ?>&nbsp;</p>
							</td>
							<td width="70">
								<p><? echo $row[csf("lot")]; ?>&nbsp;</p>
							</td>
							<td width="80" align="right"><? echo number_format($row[csf("program_qnty")], 2); ?></td>
							<td width="80" align="right"><? echo number_format($row[csf("req_qnty")], 2); ?></td>
							<td width="80" align="right"><a href="##" onClick="openmypage_issue(<? echo $row[csf("requisition_no")]; ?>,<? echo $row[csf("prod_id")]; ?>,'demand_popup','<? echo $txt_date_from; ?>','<? echo $txt_date_to; ?>');"><? echo number_format($demand_data[$row[csf("requisition_no")]][$row[csf("prod_id")]]["demand_qnty"], 2); ?></a></td>
							<td width="80" align="right"><? echo number_format($d_balance, 2); ?></td>
							<td width="80" align="right"><a href="##" onClick="openmypage_issue(<? echo $row[csf("requisition_no")]; ?>,<? echo $row[csf("prod_id")]; ?>,'issueQnty_popup');"><? echo number_format($yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['qty'], 2); ?></a></td>
							<td width="80" align="right" title="Demand Qty-Issue Qty"><? echo number_format($iss_balance, 2, '.', ''); ?></td>
							<td width="80" align="right"><? echo number_format($yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['ret_qty'], 2); ?></td>
							<td width="80" align="right"><? echo number_format($production_info_arr[$row[csf('prog_no')]]['grey_receive_qnty'], 2); ?></td>
							<td width="80" align="right"><a href="##" onClick="openmypage_issue(<? echo $row[csf("requisition_no")]; ?>,<? echo $row[csf("prod_id")]; ?>,'issueReturnQnty_popup');"><? echo number_format($issue_with_reject_qnty, 2); ?></a></td>
							<td width="80" align="right">
								<?
								$balance_qnty = $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['ret_qty'] - $issue_with_reject_qnty;
								echo number_format($balance_qnty, 2, ".", "");
								?>
							</td>
							<td>&nbsp;</td>
						</tr>
				<?
						$i++;
						$total_program_qty += $row[csf("program_qnty")];
						$total_rec_qty += $row[csf("req_qnty")];
						$total_demand_qty += $demand_data[$row[csf("requisition_no")]][$row[csf("prod_id")]]["demand_qnty"];
						$total_demand_blanace += $d_balance;
						$total_demand_issue += $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['qty'];
						$total_production_qnty += $production_info_arr[$row[csf('prog_no')]]['grey_receive_qnty'];
						$d_balance = 0;
					}
				}
				?>
			</table>
			<table width="2690" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
				<tfoot>
					<tr>

						<th width="30">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="130">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="70">Total : </th>
						<th width="80" id="value_tot_progs" style="text-align: right;"><? echo number_format($total_program_qty, 2); ?></th>
						<th width="80" id="value_total_rec_qty" style="text-align: right;"><? echo number_format($total_rec_qty, 2); ?> </th>
						<th width="80" id="value_tot_demand" style="text-align: right;"><? echo number_format($total_demand_qty, 2); ?></th>
						<th width="80" id="value_tot_balance2" style="text-align: right;"><? echo number_format($total_demand_blanace, 2); ?></th>
						<th width="80" id="value_tot_issue" style="text-align: right;"><? echo number_format($total_demand_issue, 2); ?></th>
						<th width="80" id="value_tot_iss_balance_qnty">&nbsp;</th>
						<th width="80" id="value_tot_returnable_qnty">&nbsp;</th>
						<th width="80" id="value_tot_production_qnty">&nbsp;</th>
						<th width="80" id="value_tot_return_qnty">&nbsp;</th>
						<th width="80" id="value_tot_bal_return_qnty">&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	foreach (glob("$user_name*.xls") as $filename) {
		if (@filemtime($filename) < (time() - $seconds_old))
			@unlink($filename);
	}
	$name = time();
	$filename = $user_name . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	$filename = "requires/" . $user_name . "_" . $name . ".xls";
	echo "$total_data####$filename";
	exit();
}

if ($action == "report_generate_xl") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$company_name = str_replace("'", "", $cbo_company_name);
	$cbo_knitting_source = str_replace("'", "", $cbo_knitting_source);
	$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
	$txt_sales_order_no = str_replace("'", "", $txt_sales_order_no);
	$txt_fab_booking_no = str_replace("'", "", $txt_fab_booking_no);
	$txt_prog_no = str_replace("'", "", $txt_prog_no);
	$txt_req_no = str_replace("'", "", $txt_req_no);
	$txt_date_from_requ = str_replace("'", "", $txt_date_from_requ);
	$txt_date_to_requ = str_replace("'", "", $txt_date_to_requ);
	$txt_date_from = str_replace("'", "", $txt_date_from);
	$txt_date_to = str_replace("'", "", $txt_date_to);
	$cbo_get_upto_qnty = str_replace("'", "", $cbo_get_upto_qnty);
	$year_id = str_replace("'", "", $cbo_year_selection);
	$txt_qnty = str_replace("'", "", $txt_qnty);
	$txt_ir_no = str_replace("'", "", $txt_ir_no);

	$compare_operator = "";
	if ($cbo_get_upto_qnty == 1) $compare_operator = ">";
	else if ($cbo_get_upto_qnty == 2) $compare_operator = "<";
	else if ($cbo_get_upto_qnty == 3) $compare_operator = ">=";
	else if ($cbo_get_upto_qnty == 4) $compare_operator = "<=";
	else if ($cbo_get_upto_qnty == 5) $compare_operator = "==";

	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_name_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$buyer_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$supplierArr = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$machine_no_arr = return_library_array("select id,machine_no from  lib_machine_name", "id", "machine_no");
	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");

	$sql_cond = "";
	if ($cbo_buyer_name > 0) $sql_cond .= " and a.buyer_id=$cbo_buyer_name";
	if ($txt_fab_booking_no != "") $sql_cond .= " and a.booking_no='$txt_fab_booking_no'";
	if ($txt_prog_no != "") $sql_cond .= " and b.id='$txt_prog_no'";
	if ($txt_req_no != "") $sql_cond .= " and c.requisition_no='$txt_req_no'";
	if ($cbo_knitting_source > 0) $knitting_cond = " and b.knitting_source='$cbo_knitting_source'";

	if ($year_id != 0) $year_cond = " and to_char(insert_date,'yyyy')=$year_id";
	else $year_cond = "";

	$sales_order_data = array();
	$sales_book_no = "";
	$all_s_booking_arr = array();
	if ($txt_sales_order_no != "") {
		$sql_sales = "select id, job_no, sales_booking_no, within_group,buyer_id,booking_without_order,style_ref_no from fabric_sales_order_mst where job_no='$txt_sales_order_no' $year_cond and status_active=1  order by id desc";
		//echo $sql_sales;die;
		$sql_sales_result = sql_select($sql_sales);

		if (count($sql_sales_result) == 0) {
	?>
			<div class="alert alert-danger">Data not found. Please try again.</div>
		<?
			die();
		}

		$sbookingNoChk = array();

		if (count($sql_sales_result) > 0) {
			foreach ($sql_sales_result as $val) {
				if ($sbookingNoChk[$val[csf('sales_booking_no')]] == "") {
					$sbookingNoChk[$val[csf('sales_booking_no')]] = $val[csf('sales_booking_no')];
					array_push($all_s_booking_arr, $val[csf('sales_booking_no')]);
				}
			}
		}
	}

	if ($txt_ir_no != "") {
		$job_info_sql = "SELECT b.id as prog_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,wo_booking_mst c,wo_po_break_down d, gbl_temp_engine e where a.id=b.mst_id and a.booking_no=c.booking_no and c.job_no=d.job_no_mst and b.is_sales=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.grouping='$txt_ir_no' group by b.id";
		//echo $job_info_sql;die;
		if (count(sql_select($job_info_sql)) == 0) {
		?>
			<div class="alert alert-danger">Data not found. Please try again.</div>
		<?
			die();
		}
		$all_prog_arr = array();
		foreach (sql_select($job_info_sql) as $row) {
			if ($progNoChk[$row[csf('prog_no')]] == "") {
				$progNoChk[$row[csf('prog_no')]] = $row[csf('prog_no')];
				array_push($all_prog_arr, $row[csf('prog_no')]);
			}
		}
	}

	//echo "<pre>";print_r($all_s_booking_arr);die;

	if (!empty($all_s_booking_arr)) {
		$sales_booking_cond = " " . where_con_using_array($all_s_booking_arr, 1, 'a.booking_no') . "";
	}
	if (!empty($all_prog_arr)) {
		$prog_cond = " " . where_con_using_array($all_prog_arr, 1, 'b.id') . "";
	}

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = " . $user_name . " and ref_from in(1,2,3,4) and  ENTRY_FORM=83");
	oci_commit($con);
	disconnect($con);

	if ($txt_date_from != "" && $txt_date_to != "") {
		$sql_demand = "select b.requisition_no, b.mst_id as demand_id, b.demand_qnty, b.save_string
		from ppl_yarn_demand_entry_dtls b, ppl_yarn_demand_entry_mst c
		where b.mst_id=c.id and c.demand_date between '$txt_date_from' and '$txt_date_to' and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";

		//echo $sql_demand;die;

		$sql_demand_result = sql_select($sql_demand);
		$demand_data = $demand_check = array();
		$all_demand_req = "";
		foreach ($sql_demand_result as $row) {
			$save_ref = explode(",", $row[csf("save_string")]);
			foreach ($save_ref as $data) {
				$data_ref = explode("_", $data);
				$demand_data[$row[csf("requisition_no")]][$data_ref[0]]["demand_qnty"] += $data_ref[2];
			}
			if ($demand_check[$row[csf("requisition_no")]][$row[csf("demand_id")]] == "") {
				$demand_check[$row[csf("requisition_no")]][$row[csf("demand_id")]] = $row[csf("demand_id")];
				$all_demand_req .= $row[csf("requisition_no")] . ",";
				$demand_data[$row[csf("requisition_no")]][0]["demand_id"] .= $row[csf("demand_id")] . ",";
			}
		}

		$all_demand_req = chop($all_demand_req, ",");
		if ($all_demand_req == "") $all_demand_req = 0;

		$sql = "SELECT a.company_id, a.buyer_id, a.booking_no, b.id as prog_no, b.knitting_source, b.knitting_party, b.program_qnty, c.requisition_no, c.yarn_qnty as req_qnty, c.prod_id, d.yarn_count_id, d.yarn_type, d.color as yarn_color, d.yarn_comp_type1st, d.brand, d.lot, d.supplier_id, c.requisition_date, b.machine_id
		from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c, product_details_master d
		where a.id=b.mst_id and b.id=c.knit_id and c.prod_id=d.id and b.is_sales=1 and a.company_id=$company_name $knitting_cond $sql_cond and  c.requisition_no in(select b.requisition_no
		from ppl_yarn_demand_entry_dtls b, ppl_yarn_demand_entry_mst c
		where b.mst_id=c.id and c.demand_date between '$txt_date_from' and '$txt_date_to' and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $sales_booking_cond $prog_cond";
		//echo $sql;die;

		$sql_result = sql_select($sql);
		if (count($sql_result) == 0) {
		?>
			<div class="alert alert-danger">Data not found. Please try again.</div>
		<?
			die();
		}
		$req_check = array();
		$progNoChk = array();
		$sbookNoChk = array();
		$all_prog_no_arr = array();
		$all_req_no_arr = array();
		$all_s_book_arr = array();
		foreach ($sql_result as $row) {
			if ($req_check[$row[csf("requisition_no")]] == "") {
				$req_check[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
				//$all_req.=$row[csf("requisition_no")].",";
				$all_req_no_arr[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
			}

			if ($progNoChk[$row[csf('prog_no')]] == "") {
				$progNoChk[$row[csf('prog_no')]] = $row[csf('prog_no')];
				$all_prog_no_arr[$row[csf("prog_no")]] = $row[csf("prog_no")];
			}

			if ($sbookNoChk[$row[csf('booking_no')]] == "") {
				$sbookNoChk[$row[csf('booking_no')]] = $row[csf('booking_no')];
				array_push($all_s_book_arr, $row[csf('booking_no')]);
			}
		}
	} else {
		if ($txt_date_from_requ != "" && $txt_date_to_requ != "") $sql_cond .= " and  c.requisition_date  between '$txt_date_from_requ' and '$txt_date_to_requ'";

		$sql = "SELECT a.company_id, a.buyer_id, a.booking_no, b.id as prog_no, b.knitting_source, b.knitting_party, b.program_qnty, c.requisition_no, c.yarn_qnty as req_qnty, c.prod_id, d.yarn_count_id, d.yarn_type, d.color as yarn_color, d.yarn_comp_type1st, d.brand, d.lot, d.supplier_id, c.requisition_date, b.machine_id
		from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c, product_details_master d
		where a.id=b.mst_id and b.id=c.knit_id and c.prod_id=d.id and b.is_sales=1 and a.company_id=$company_name $knitting_cond $sql_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $sales_booking_cond $prog_cond";
		// echo $sql;die;
		$sql_result = sql_select($sql);
		if (count($sql_result) == 0) {
		?>
			<div class="alert alert-danger">Data not found. Please try again.</div>
		<?
			die();
		}
		$req_check = array();
		$progNoChk = array();
		$sbookNoChk = array();
		$all_req_no_arr = array();
		$all_prog_no_arr = array();

		foreach ($sql_result as $row) {
			if ($req_check[$row[csf("requisition_no")]] == "") {
				$req_check[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
				$all_req_no_arr[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
			}
			if ($progNoChk[$row[csf('prog_no')]] == "") {
				$progNoChk[$row[csf('prog_no')]] = $row[csf('prog_no')];
				$all_prog_no_arr[$row[csf("prog_no")]] = $row[csf("prog_no")];
			}
		}

		$all_req_no_arr = array_filter($all_req_no_arr);
		if (!empty($all_req_no_arr)) {
			fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 83, 2, $all_req_no_arr, $empty_arr);
			//die;
			$sql_demand = "SELECT b.requisition_no, b.mst_id as demand_id, b.demand_qnty, b.save_string
			from ppl_yarn_demand_entry_dtls b, ppl_yarn_demand_entry_mst c, gbl_temp_engine d
			where b.mst_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and b.requisition_no=d.ref_val  and d.user_id = " . $user_name . " and d.ref_from=2 and d.entry_form=83 ";
			//echo $sql_demand; die;
			$sql_demand_result = sql_select($sql_demand);
			$demand_data = $demand_check = array();
			$all_demand_req = "";
			foreach ($sql_demand_result as $row) {
				$save_ref = explode(",", $row[csf("save_string")]);
				foreach ($save_ref as $data) {
					$data_ref = explode("_", $data);
					$demand_data[$row[csf("requisition_no")]][$data_ref[0]]["demand_qnty"] += $data_ref[2];
				}
				if ($demand_check[$row[csf("requisition_no")]][$row[csf("demand_id")]] == "") {
					$demand_check[$row[csf("requisition_no")]][$row[csf("demand_id")]] = $row[csf("demand_id")];
					$demand_data[$row[csf("requisition_no")]][0]["demand_id"] .= $row[csf("demand_id")] . ",";
				}
				//$demand_data[$row[csf("requisition_no")]]["demand_qnty"]+=$row[csf("demand_qnty")];
			}
		}
	}

	$all_prog_no_arr = array_filter($all_prog_no_arr);
	//var_dump($all_prog_no_arr);die;

	if (!empty($all_prog_no_arr)) {
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 83, 1, $all_prog_no_arr, $empty_arr);
		//die;

		$knit_prod_sql = "SELECT a.booking_id,a.booking_no, b.grey_receive_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, gbl_temp_engine c where a.id=b.mst_id and  a.company_id=$company_name and a.receive_basis=2 and a.item_category=13 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=c.ref_val and c.user_id = " . $user_name . " and c.ref_from=1 and c.entry_form=83";

		//echo $knit_prod_sql;die;

		$production_info_arr = array();
		foreach (sql_select($knit_prod_sql) as $row) {
			$production_info_arr[$row[csf('booking_no')]]['grey_receive_qnty'] += $row[csf('grey_receive_qnty')];
		}
		//var_dump($production_info_arr);die;
		$sql_sales_info = "SELECT d.job_no,d.sales_booking_no,d.within_group,d.buyer_id,d.booking_without_order,d.style_ref_no,b.id ,c.po_id,d.po_buyer
		FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c,fabric_sales_order_mst d, gbl_temp_engine e
	    WHERE a.id = b.mst_id AND b.id = c.dtls_id and c.po_id=d.id AND a.company_id = $company_name AND b.is_sales = 1 and b.id=e.ref_val and e.user_id = " . $user_name . " and e.ref_from=1 and e.entry_form=83";
		//echo $sql_sales_info;die; 
		$sql_sales_info_result = sql_select($sql_sales_info);
		foreach ($sql_sales_info_result as $row) {
			$sales_order_data[$row[csf("sales_booking_no")]]["job_no"] = $row[csf("job_no")];
			$sales_order_data[$row[csf("sales_booking_no")]]["within_group"] = $row[csf("within_group")];
			$sales_order_data[$row[csf("sales_booking_no")]]["buyer_id"] = $row[csf("buyer_id")];
			$sales_order_data[$row[csf("sales_booking_no")]]["po_buyer"] = $row[csf("po_buyer")];
			$sales_order_data[$row[csf("sales_booking_no")]]["booking_without_order"] = $row[csf("booking_without_order")];
			$sales_order_data[$row[csf("sales_booking_no")]]["style_ref_no"] = $row[csf("style_ref_no")];
		}

		$job_info_sql = "SELECT b.id as prog_no,d.grouping from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,wo_booking_mst c,wo_po_break_down d, gbl_temp_engine e where a.id=b.mst_id and a.booking_no=c.booking_no and c.job_no=d.job_no_mst and b.is_sales=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.id=e.ref_val and e.user_id = " . $user_name . " and e.ref_from=1 and e.entry_form=83";
		//echo $job_info_sql;die;
		$job_info_arr = array();
		foreach (sql_select($job_info_sql) as $row) {
			$job_info_arr[$row[csf('prog_no')]]['int_ref'] = $row[csf('grouping')];
		}
	}

	$all_req_no_arr = array_filter($all_req_no_arr);
	if (!empty($all_req_no_arr)) {
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 83, 3, $all_req_no_arr, $empty_arr);
		//die;
		$yarn_issue_details_arr = array();
		$yarn_issue_remark_arr = array();

		$yarn_sql = sql_select("SELECT a.id as issue_id,a.remarks, b.requisition_no as requ_no, b.prod_id, b.store_id, b.cons_quantity as issue_qnty, b.return_qnty from inv_issue_master a,inv_transaction b, gbl_temp_engine c where a.id=b.mst_id and a.entry_form=3 and a.company_id=$company_name and a.issue_basis in(3,8) and  b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.requisition_no=c.ref_val and c.user_id = " . $user_name . " and c.ref_from=3 and c.entry_form=83 ");
		$all_issue_id_arr = array();
		$issueId_check = array();
		foreach ($yarn_sql as $row) {
			$yarn_issue_details_arr[$row[csf('requ_no')]][$row[csf('prod_id')]]['qty'] += $row[csf('issue_qnty')];
			$yarn_issue_details_arr[$row[csf('requ_no')]][$row[csf('prod_id')]]['ret_qty'] += $row[csf('return_qnty')];
			$yarn_issue_details_arr[$row[csf('requ_no')]][$row[csf('prod_id')]]['remark'] = $row[csf('remarks')];
			$issue_id_arr[$row[csf('issue_id')]] = $row[csf('issue_id')];

			if ($issueId_check[$row[csf("issue_id")]] == "") {
				$issueId_check[$row[csf("issue_id")]] = $row[csf("issue_id")];
				$all_issue_id_arr[$row[csf("issue_id")]] = $row[csf("issue_id")];
			}
		}
	}

	$all_issue_id_arr = array_filter($all_issue_id_arr);
	if (!empty($all_issue_id_arr)) {
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 83, 4, $all_issue_id_arr, $empty_arr);
		//die;
		$yarn_iss_ret_sql = sql_select("SELECT a.booking_no as requ_no, b.prod_id, b.store_id, b.cons_quantity as issue_return_qnty, b.cons_reject_qnty
		from inv_receive_master a,inv_transaction b, gbl_temp_engine c
		where a.id=b.mst_id and a.entry_form=9 and a.company_id=$company_name and a.receive_basis in(3) and  b.item_category=1 and b.transaction_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.issue_id=c.ref_val and c.user_id = " . $user_name . " and c.ref_from=4 and c.entry_form=83 "); //$issue_ids_cond
		foreach ($yarn_iss_ret_sql as $row) {
			$yarn_issue_details_arr[$row[csf('requ_no')]][$row[csf('prod_id')]]['iss_return_qty'] += $row[csf('issue_return_qnty')];
			$yarn_issue_details_arr[$row[csf('requ_no')]][$row[csf('prod_id')]]['iss_reject_qty'] += $row[csf('cons_reject_qnty')];
		}
		//for demand
		$requisiton_cond = ($txt_req_no != "") ? " and b.requisition_no=$txt_req_no" : "";
		$sql_demand = "SELECT b.prod_id, b.cons_quantity as issue_return_qnty, b.cons_reject_qnty, c.requisition_no
		from inv_receive_master a, inv_transaction b, ppl_yarn_demand_entry_dtls c, gbl_temp_engine d
		where a.id = b.mst_id and a.booking_id = c.mst_id and a.entry_form = 9 and a.company_id = $company_name and a.receive_basis in(8) and b.item_category = 1 and b.transaction_type = 4 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted =0  and a.issue_id=d.ref_val and d.user_id = " . $user_name . " and d.ref_from=4 and d.entry_form=83 $requisiton_cond";
		//echo $sql_demand;//$issue_ids_cond
		$sql_demand_rslt = sql_select($sql_demand);
		foreach ($sql_demand_rslt as $row) {
			$yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['iss_return_qty'] += $row[csf('issue_return_qnty')];
			$yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['iss_reject_qty'] += $row[csf('cons_reject_qnty')];
		}
		//echo "<pre>";print_r($demand_data);die;
	}

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = " . $user_name . " and ref_from in(1,2,3,4) and entry_form=83");
	oci_commit($con);
	disconnect($con);

	// ob_start();
	$html = '';
	$html .= '<table cellpadding="0" cellspacing="0" width="2430">
	<tr>
		<td align="center" width="100%" style="font-size:16px"><strong>Requisition  Against Demand Status</strong> <br> <b>';

	if ($start_date != '') $html .= change_date_format($start_date) . ' To ' . change_date_format($end_date);
	else $html .= '';
	$html .= '</b>
				</td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2690" class="rpt_table" align="left" >
			<thead>
				<th width="30">SL</th>
				<th width="120">Buyer</th>
				<th width="120">Style Ref.</th>
				<th width="120">FSO No</th>
				<th width="100">IR/IB</th>
				<th width="120">Fabric Booking No</th>
				<th width="60">Prog. No</th>
				<th width="120">Knitting Company</th>
				<th width="60">Req. No</th>
                <th width="70">Req. Date</th>
                <th width="130">M/C No</th>
				<th width="60">Y. Count</th>
				<th width="150">Y Composition</th>
				<th width="80">Y. Type</th>
				<th width="150">Supplier</th>
				<th width="100">Brand</th>
				<th width="80">Y. Color</th>
				<th width="70">Lot</th>
				<th width="80">Program Qty.</th>
				<th width="80">Req Qty.</th>
				<th width="80">Demand Qty.</th>
				<th width="80">Demand Balance</th>
				<th width="80">Issue Qty</th>
				<th width="80">Issue Balance Qty</th>
				<th width="80">Returnble Qty</th>
				<th width="80">Production Qty</th>
				<th width="80">Iss. Returned Qty</th>
				<th width="80">Balance Qty</th>
				<th>Remarks</th>
			</thead>
		</table>
		<div style="width:2710px; overflow-y:scroll; max-height:330px;" id="scroll_body">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2690" class="rpt_table" id="tbl_list_search"  align="left">';
	$i = 1;
	$tot_issue_bal_qty = 0;
	$tot_returnable_qty = 0;
	$tot_production_qty = 0;
	$tot_issue_rtn_qty = 0;
	$tot_bal_qty = 0;
	if ($compare_operator != "" && $txt_qnty > 0) {
		foreach ($sql_result as $row) {
			$d_balance = $row[csf("req_qnty")] - $demand_data[$row[csf("requisition_no")]][$row[csf("prod_id")]]["demand_qnty"];

			//echo $d_balance." ".$compare_operator." ".$txt_qnty."==";
			eval("\$cond=$d_balance $compare_operator $txt_qnty?1:0;");
			if ($cond) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
				$issue_with_reject_qnty = $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['iss_return_qty'] + $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['iss_reject_qty'];
				$iss_balance = $demand_data[$row[csf("requisition_no")]][$row[csf("prod_id")]]["demand_qnty"] - $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['qty'];
				$html .= '<tr bgcor="" onClick="" id="tr_' . $i . '">
						<td width="30" align="center">' . $i . '</td>
						<td width="120"><p>';

				if ($sales_order_data[$row[csf("booking_no")]]["within_group"] == 2) {
					$html .= $buyer_name_arr[$sales_order_data[$row[csf("booking_no")]]["buyer_id"]];
				} else {
					$html .= $buyer_name_arr[$sales_order_data[$row[csf("booking_no")]]["po_buyer"]];
				}
				$html .= '&nbsp;</p></td>
							<td width="120"><p>' . $sales_order_data[$row[csf("booking_no")]]["style_ref_no"] . '&nbsp;</p></td>
							<td width="120"><p>' . $row[csf("booking_no")] . '&nbsp;</p></td>
							<td width="100"><p>' . $job_info_arr[$row[csf('prog_no')]]['int_ref'] . '&nbsp;</p></td>
							<td width="100"><p>' . $sales_order_data[$row[csf("booking_no")]]["job_no"] . '&nbsp;</p></td>
							<td width="120"><p>' . $row[csf("booking_no")] . '&nbsp;</p></td>
							<td width="60" align="center"><p>' . $row[csf("prog_no")] . '&nbsp;</p></td>';
				if ($row[csf("knitting_source")] == 1)
					$knit_com = $company_library[$row[csf("knitting_party")]];
				else
					$knit_com = $supplierArr[$row[csf("knitting_party")]];
				$html .= '<td width="120"><p>' . $knit_com . '&nbsp;</p></td>
							<td width="60" align="center"><p>' . $row[csf("requisition_no")] . '&nbsp;</p></td>
                            <td width="70" align="center"><p>';
				if ($row[csf("requisition_date")] != "" && $row[csf("requisition_date")] != "0000-00-00") $html .= change_date_format($row[csf("requisition_date")]) . '&nbsp;</p></td>
                            <td width="130"><p>';
				$mc_arr = explode(",", $row[csf("machine_id")]);
				$all_mc_no = "";
				foreach ($mc_arr as $mc_id) {
					$all_mc_no .= $machine_no_arr[$mc_id] . ",";
				}
				$all_mc_no = chop($all_mc_no, ",");
				$html .= $all_mc_no;

				$html .= '&nbsp;</p></td>
							<td width="60" align="center"><p>' . $yarn_count_arr[$row[csf("yarn_count_id")]] . '&nbsp;</p></td>
							<td width="150"><p>' . $composition[$row[csf("yarn_comp_type1st")]] . '&nbsp;</p></td>
							<td width="80"><p>' . $yarn_type[$row[csf("yarn_type")]] . '&nbsp;</p></td>
							<td width="150"><p>' . $supplierArr[$row[csf("supplier_id")]] . '&nbsp;</p></td>
							<td width="100"><p>' . $brand_name_arr[$row[csf("brand")]] . '&nbsp;</p></td>
							<td width="80"><p>' . $color_library[$row[csf("yarn_color")]] . '&nbsp;</p></td>
							<td width="70"><p>' . $row[csf("lot")] . '&nbsp;</p></td>
							<td width="80" align="right">' . number_format($row[csf("program_qnty")], 2) . '</td>
							<td width="80" align="right">' . number_format($row[csf("req_qnty")], 2) . '</td>
							<td width="80" align="right" >' . number_format($demand_data[$row[csf("requisition_no")]][$row[csf("prod_id")]]["demand_qnty"], 2) . '</td>
							<td width="80" align="right">' . number_format($d_balance, 2) . '</td>
							<td width="80" align="right">' . number_format($yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['qty'], 2) . '</td>
							<td width="80" align="right" title="Demand Qty-Issue Qty">' . number_format($iss_balance, 2, '.', '') . '</td>
							<td width="80" align="right">' . number_format($yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['ret_qty'], 2) . '</td>
							<td width="80" align="right">' . number_format($production_info_arr[$row[csf('prog_no')]]['grey_receive_qnty'], 2) . '</td>
							<td width="80" align="right">' . number_format($issue_with_reject_qnty, 2) . '</td>
							<td width="80" align="right">';

				$balance_qnty = $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['ret_qty'] - ($issue_with_reject_qnty);
				$html .= number_format($balance_qnty, 2, ".", "");
				$html .= '</td>
							<td>&nbsp;</td>
						</tr>';

				$i++;
				$total_program_qty += $row[csf("program_qnty")];
				$total_rec_qty += $row[csf("req_qnty")];
				$total_demand_qty += $demand_data[$row[csf("requisition_no")]][$row[csf("prod_id")]]["demand_qnty"];
				$total_demand_blanace += $d_balance;
				$total_demand_issue += $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['qty'];
				$total_production_qnty += $production_info_arr[$row[csf('prog_no')]]['grey_receive_qnty'];
				$d_balance = 0;
				$tot_returnable_qty += $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['ret_qty'];
				$tot_issue_bal_qty += $iss_balance;
				$tot_issue_rtn_qty += $issue_with_reject_qnty;
				$tot_bal_qty += $balance_qnty;
			}
		}
	} else {
		foreach ($sql_result as $row) {
			$d_balance = $row[csf("req_qnty")] - $demand_data[$row[csf("requisition_no")]][$row[csf("prod_id")]]["demand_qnty"];
			$iss_balance = $demand_data[$row[csf("requisition_no")]][$row[csf("prod_id")]]["demand_qnty"] - $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['qty'];
			if ($i % 2 == 0) $bgcolor = "#E9F3FF";
			else $bgcolor = "#FFFFFF";
			$issue_with_reject_qnty = $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['iss_return_qty'] + $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['iss_reject_qty'];
			$html .= '<tr id="tr_' . $i . '">
					<td width="30" align="center">' . $i . '</td>
					<td width="120"><p>';

			if ($sales_order_data[$row[csf("booking_no")]]["within_group"] == 2) {
				$html .= $buyer_name_arr[$sales_order_data[$row[csf("booking_no")]]["buyer_id"]];
			} else {
				$html .= $buyer_name_arr[$sales_order_data[$row[csf("booking_no")]]["po_buyer"]];
			}
			$html .= '&nbsp;</p></td>
						<td width="120"><p>' . $sales_order_data[$row[csf("booking_no")]]["style_ref_no"] . '&nbsp;</p></td>
						<td width="120"><p>' . $sales_order_data[$row[csf("booking_no")]]["job_no"] . '&nbsp;</p></td>
						<td width="100"><p>' . $job_info_arr[$row[csf('prog_no')]]['int_ref'] . '&nbsp;</p></td>
						<td width="120"><p>' . $row[csf("booking_no")] . '&nbsp;</p></td>
						<td width="60" align="center"><p>' . $row[csf("prog_no")] . '&nbsp;</p></td>';
		?>

	<?
			if ($row[csf("knitting_source")] == 1)
				$knit_com = $company_library[$row[csf("knitting_party")]];
			else
				$knit_com = $supplierArr[$row[csf("knitting_party")]];

			$html .= '<td width="120"><p>' . $knit_com . '&nbsp;</p></td>
						<td width="60" align="center"><p>' . $row[csf("requisition_no")] . '&nbsp;</p></td>
                        <td width="70" align="center"><p>';
			if ($row[csf("requisition_date")] != "" && $row[csf("requisition_date")] != "0000-00-00") $html .= change_date_format($row[csf("requisition_date")]) . '&nbsp;</p></td>
                        <td width="130"><p>';

			$mc_arr = explode(",", $row[csf("machine_id")]);
			$all_mc_no = "";
			foreach ($mc_arr as $mc_id) {
				$all_mc_no .= $machine_no_arr[$mc_id] . ",";
			}
			$all_mc_no = chop($all_mc_no, ",");
			$html .= $all_mc_no;
			$html .= '&nbsp;</p></td>
						<td width="60" align="center"><p>' . $yarn_count_arr[$row[csf("yarn_count_id")]] . '&nbsp;</p></td>
						<td width="150"><p>' . $composition[$row[csf("yarn_comp_type1st")]] . '&nbsp;</p></td>
						<td width="80"><p>' . $yarn_type[$row[csf("yarn_type")]] . '&nbsp;</p></td>
						<td width="150"><p>' . $supplierArr[$row[csf("supplier_id")]] . '&nbsp;</p></td>
						<td width="100"><p>' . $brand_name_arr[$row[csf("brand")]] . '&nbsp;</p></td>
						<td width="80"><p>' . $color_library[$row[csf("yarn_color")]] . '&nbsp;</p></td>
						<td width="70"><p>' . $row[csf("lot")] . '&nbsp;</p></td>
						<td width="80" align="right">' . number_format($row[csf("program_qnty")], 2) . '</td>
						<td width="80" align="right">' . number_format($row[csf("req_qnty")], 2) . '</td>
						<td width="80" align="right">' . number_format($demand_data[$row[csf("requisition_no")]][$row[csf("prod_id")]]["demand_qnty"], 2) . '</td>
						<td width="80" align="right">' . number_format($d_balance, 2) . '</td>
						<td width="80" align="right">' . number_format($yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['qty'], 2) . '</td>
						<td width="80" align="right" title="Demand Qty-Issue Qty">' . number_format($iss_balance, 2, '.', '') . '</td>
						<td width="80" align="right">' . number_format($yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['ret_qty'], 2) . '</td>
						<td width="80" align="right">' . number_format($production_info_arr[$row[csf('prog_no')]]['grey_receive_qnty'], 2) . '</td>
						<td width="80" align="right">' . number_format($issue_with_reject_qnty, 2) . '</td>
						<td width="80" align="right">';

			$balance_qnty = $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['ret_qty'] - $issue_with_reject_qnty;
			$html .= number_format($balance_qnty, 2, ".", "");
			$html .= '</td>
							<td>&nbsp;</td>
						</tr>';

			$i++;
			$total_program_qty += $row[csf("program_qnty")];
			$total_rec_qty += $row[csf("req_qnty")];
			$total_demand_qty += $demand_data[$row[csf("requisition_no")]][$row[csf("prod_id")]]["demand_qnty"];
			$total_demand_blanace += $d_balance;
			$total_demand_issue += $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['qty'];
			$total_production_qnty += $production_info_arr[$row[csf('prog_no')]]['grey_receive_qnty'];
			$d_balance = 0;
			$tot_returnable_qty += $yarn_issue_details_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['ret_qty'];
			$tot_issue_bal_qty += $iss_balance;
			$tot_issue_rtn_qty += $issue_with_reject_qnty;
			$tot_bal_qty += $balance_qnty;
		}
	}
	$html .= '</table>
			<table width="2690"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"  align="left">
				<tfoot>
					<tr>
	
						<th width="30">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="130">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="70">Total : </th>
						<th width="80" id="value_tot_progs" style="text-align: right;">' . number_format($total_program_qty, 2) . '</th>
						<th width="80" id="value_total_rec_qty" style="text-align: right;">' . number_format($total_rec_qty, 2) . ' </th>
						<th width="80" id="value_tot_demand" style="text-align: right;">' . number_format($total_demand_qty, 2) . '</th>
						<th width="80" id="value_tot_balance2" style="text-align: right;">' . number_format($total_demand_blanace, 2) . '</th>
						<th width="80" id="value_tot_issue" style="text-align: right;">' . number_format($total_demand_issue, 2) . '</th>
						<th width="80" id="value_tot_iss_balance_qnty">' . number_format($tot_issue_bal_qty, 2) . '</th>
						<th width="80" id="value_tot_returnable_qnty">' . number_format($tot_returnable_qty, 2) . '</th>
						<th width="80" id="value_tot_production_qnty">' . number_format($total_production_qnty, 2) . '</th>
						<th width="80" id="value_tot_return_qnty">' . number_format($tot_issue_rtn_qty, 2) . '</th>
						<th width="80" id="value_tot_bal_return_qnty">' . number_format($tot_bal_qty, 2) . '</th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>';

	foreach (glob("swgfsr_*.xls") as $filename) {
		@unlink($filename);
	}
	$name = time();
	$filename = "swgfsr_" . $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$filename####$filename";
	exit();
}

if ($action == "issueQnty_popup") {
	echo load_html_head_contents("Issue PopUp", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	//echo $requ_id."==".$prod_id;die;
	$prog_sql = sql_select("select b.requisition_no,b.knit_id from ppl_yarn_requisition_entry b where b.status_active=1 and b.is_deleted=0 and b.requisition_no=$requ_id");
	$prog_arr = array();
	foreach ($prog_sql as $row) {
		$prog_arr[$row[csf('requisition_no')]]['plan'] = $row[csf('knit_id')];
	}

	$issue_sql = sql_select("select a.issue_date, a.issue_number, b.requisition_no as requ_no, b.cons_quantity as issue_qnty, b.return_qnty
	from inv_issue_master a, inv_transaction b
	where a.id=b.mst_id and a.entry_form=3 and a.issue_basis in (3,8) and b.item_category=1 and b.transaction_type=2 and b.requisition_no=$requ_id and b.prod_id=$prod_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

	?>
	<div style=" width:460px">
		<table id="" cellspacing="0" cellpadding="0" border="1" rules="all" width="460px" class="rpt_table">
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<th colspan="6">Issue Details</th>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="80">Issue Date</th>
					<th width="110">Issue ID</th>
					<th width="80">Requ. No</th>
					<th width="80">Prog. No.</th>
					<th width="80">Issue Qty</th>
				</tr>
			</thead>
		</table>
		<div style="width:480px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="460" class="rpt_table" id="table_body">
				<tbody>
					<?
					$i = 1;
					$total_issue_qty = 0;
					foreach ($issue_sql as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="80">
								<p><? echo  change_date_format($row[csf('issue_date')]); ?></p>
							</td>
							<td width="110">
								<p><? echo $row[csf('issue_number')]; ?></p>
							</td>
							<td width="80">
								<p><? echo $row[csf('requ_no')]; ?></p>
							</td>
							<td width="80" align="center"><? echo $prog_arr[$row[csf('requ_no')]]['plan']; ?></td>
							<td align="right" width="80"><? echo number_format($row[csf('issue_qnty')], 2); ?></td>
						</tr>
					<?
						$i++;
						$total_issue_qty += $row[csf('issue_qnty')];
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="5" align="right"> Total </th>
						<th align="right"><? echo number_format($total_issue_qty, 2); ?> </th>
					</tr>
				</tfoot>
			</table>
		</div>
		<script>
			setFilterGrid("table_body", -1);
		</script>
	</div>
<?
}

if ($action == "demand_popup") {
	echo load_html_head_contents("Issue PopUp", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);

	if ($date_from && $date_to != "") {
		$dateCond = "and c.demand_date between '$date_from' and '$date_to'";
	} else {
		$dateCond = "";
	}

	$sql_demand = "select b.requisition_no, b.mst_id as demand_id, b.demand_qnty, b.save_string, c.demand_system_no, c.demand_date
	from ppl_yarn_demand_entry_dtls b, ppl_yarn_demand_entry_mst c
	where  b.mst_id=c.id and b.requisition_no=$requ_id $dateCond  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";

	$sql_demand_result = sql_select($sql_demand);
	$demand_pop_data = array();
	$i = 0;
	foreach ($sql_demand_result as $row) {
		$save_ref = explode(",", $row[csf("save_string")]);
		foreach ($save_ref as $data) {
			$data_ref = explode("_", $data);
			if ($data_ref[0] == $prod_id) {
				$demand_pop_data[$i]["demand_qnty"] = $data_ref[2];
				$demand_pop_data[$i]["demand_system_no"] = $row[csf("demand_system_no")];
				$demand_pop_data[$i]["demand_date"] = $row[csf("demand_date")];
				$i++;
			}
		}
	}
?>
	<div style=" width:460px">
		<table id="" cellspacing="0" cellpadding="0" border="1" rules="all" width="460px" class="rpt_table">
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<th colspan="4">Demand Details</th>
				</tr>
				<tr>
					<th width="50">SL</th>
					<th width="200">Demand No</th>
					<th width="100">Demand Date</th>
					<th>Demand Qty</th>
				</tr>
			</thead>
		</table>
		<div style="width:480px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="460" class="rpt_table" id="table_body">
				<tbody>
					<?
					$i = 1;
					$total_issue_qty = 0;
					foreach ($demand_pop_data as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="50" align="center"><? echo $i; ?></td>
							<td width="200">
								<p><? echo $row[('demand_system_no')]; ?></p>
							</td>
							<td width="100" align="center">
								<p><? if ($row[('demand_date')] != "" && $row[('demand_date')] != "0000-00-00") echo change_date_format($row[('demand_date')]); ?></p>
							</td>
							<td align="right"><? echo number_format($row[('demand_qnty')], 2); ?></td>
						</tr>
					<?
						$i++;
						$total_issue_qty += $row[('demand_qnty')];
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="3" align="right"> Total </th>
						<th align="right"><? echo number_format($total_issue_qty, 2); ?> </th>
					</tr>
				</tfoot>
			</table>
		</div>
		<script>
			setFilterGrid("table_body", -1);
		</script>
	</div>
<?
}

if ($action == "issueReturnQnty_popup") {
	echo load_html_head_contents("Issue PopUp", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	$prog_sql = sql_select("select b.requisition_no, b.knit_id from ppl_yarn_requisition_entry b where b.status_active=1 and b.is_deleted=0 and b.requisition_no=$requ_id");
	$prog_arr = array();
	foreach ($prog_sql as $row) {
		$prog_arr[$row[csf('requisition_no')]]['plan'] = $row[csf('knit_id')];
	}

	$yarn_iss_ret_sql = sql_select("select a.receive_date, a.recv_number, b.requisition_no, b.prod_id, b.store_id, b.cons_quantity as issue_return_qnty, b.cons_reject_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=9 and a.receive_basis in(3,8) and b.item_category=1 and b.transaction_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.requisition_no = '$requ_id' and b.prod_id=$prod_id");
?>
	<div style=" width:460px">
		<table id="" cellspacing="0" cellpadding="0" border="1" rules="all" width="460px" class="rpt_table">
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<th colspan="7" align="center" width="460"> Issue Return Details </th>
				</tr>
				<tr>
					<th width="10">SL</th>
					<th width="80">Return Date</th>
					<th width="110">Iss. Return ID</th>
					<th width="80">Requ. No</th>
					<th width="80">Prog. No.</th>
					<th width="80">Iss.Return Qty</th>
					<th width="80">Reject Qty</th>
				</tr>
			</thead>
		</table>
		<div style="width:480px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="460" class="rpt_table" id="table_body">
				<tbody>
					<?
					if (count($yarn_iss_ret_sql) < 1) {
						echo "<tr><td align='center'><b>No Data Found</b></td></tr>";
						die;
					} else {
						$i = 1;
						$total_issue_return_qnty = 0;
						foreach ($yarn_iss_ret_sql as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
					?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="10" align="center"><? echo $i; ?></td>
								<td width="80" align="center">
									<p><? echo  change_date_format($row[csf('receive_date')]); ?></p>
								</td>
								<td width="110" align="center">
									<p><? echo $row[csf('recv_number')]; ?></p>
								</td>
								<td width="80" align="center">
									<p><? echo $row[csf('requisition_no')]; ?></p>
								</td>
								<td width="80" align="center"><? echo $prog_arr[$row[csf('requisition_no')]]['plan']; ?></td>
								<td align="right" width="80"><? echo number_format($row[csf('issue_return_qnty')], 2); ?></td>
								<td align="right" width="80"><? echo number_format($row[csf('cons_reject_qnty')], 2); ?></td>
							</tr>
					<?
							$i++;
							$total_issue_return_qnty += $row[csf('issue_return_qnty')];
							$total_issue_reject_qnty += $row[csf('cons_reject_qnty')];
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="5" align="right"> Total </th>
						<th align="right"><? echo number_format($total_issue_return_qnty, 2); ?> </th>
						<th align="right"><? echo number_format($total_issue_reject_qnty, 2); ?> </th>
					</tr>
				</tfoot>
			</table>
		</div>
		<script>
			setFilterGrid("table_body", -1);
		</script>
	</div>
<?
}
?>