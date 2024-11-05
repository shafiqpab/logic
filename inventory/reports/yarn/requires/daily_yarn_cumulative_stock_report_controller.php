<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//--------------------------------------------------------------------------------------------

if ($action == "load_drop_down_store")
{
	$data = explode("**", $data);

	if ($data[1] == 2)
	{
		$disable = 1;

	}
	else
	{
		$disable = 0;
	}

	echo create_drop_down("cbo_store_name", 100, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type in(1)  order by a.store_name", "id,store_name", 1, "-- All Store--", 0, "", $disable);
	exit();
}

//load drop down supplier
if ($action == "load_drop_down_supplier")
{
	if($data){$companyCon=" and a.tag_company='$data'";}
	else{$companyCon="";}
	echo create_drop_down("cbo_supplier", 120, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $companyCon and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 0, "-- Select --", 0, "", 0);
	exit();
}

if ($action == "eval_multi_select") {
	echo "set_multiselect('cbo_supplier','0','0','','0');\n";
	exit();
}

if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$companyArr[0] = "All Company";
	$supplierArr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buy_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$yarnTestQalityCommentsArr = return_library_array("select prod_id, yarn_quality_coments from inv_yarn_test_mst where  status_active=1 and is_deleted=0", 'prod_id', 'yarn_quality_coments');

	if ($db_type == 0) {
		$exchange_rate = return_field_value("conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and is_deleted=0 order by id DESC limit 0,1");
		$from_date = change_date_format($from_date, 'yyyy-mm-dd');
		$to_date = change_date_format($to_date, 'yyyy-mm-dd');
	} else if ($db_type == 2) {
		$exchange_rate = return_field_value("conversion_rate", "(SELECT conversion_rate FROM currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 ORDER BY id DESC)", "ROWNUM = 1");
		$from_date = change_date_format($from_date, '', '', 1);
		$to_date = change_date_format($to_date, '', '', 1);
	} else {
		$from_date = "";
		$to_date = "";
		$exchange_rate = 1;
	}

	$txt_lot_no = trim($txt_lot_no);

	$txt_composition = trim($txt_composition);

	ob_start();
	?>
	<style>
	a {
		color: #0254EB
	}
	a:visited {
		color: #0254EB
	}
	a.morelink {
		text-decoration:none;
		outline: none;
	}
	.morecontent span {
		display: none;
	}
	.comment {
		width: 400px;
		background-color: #f0f0f0;
		margin: 10px;
	}
	</style>
	<?
	$search_cond = "";$search_cond_transfer = "";

	if ($cbo_dyed_type >0)
	{
		if ($cbo_dyed_type==2)
		{
			$search_cond .= " and a.dyed_type in (0,2)";
		}else {
			$search_cond .= " and a.dyed_type in (1)";
		}
	}

	if ($cbo_yarn_type > 0)
	{
		$search_cond .= " and a.yarn_type in ($cbo_yarn_type)";
	}

	if ($txt_count != "")
	{
		$search_cond .= " and a.yarn_count_id in($txt_count)";
	}

	if ($txt_lot_no != "")
	{
		if($lot_search_type == 1)
		{
			if($db_type == 2)
			{
				$search_cond .= " and regexp_like (a.lot, '^".trim($txt_lot_no)."')";
			}
			else
			{
				$search_cond .= " and a.lot like '".trim($txt_lot_no)."%'";
			}
		}
		else
		{
			$search_cond .= " and a.lot='" . trim($txt_lot_no) . "'";
		}
	}

	if ($cbo_supplier > 0)
	{
		$search_cond .= "  and a.supplier_id in($cbo_supplier)";
	}
	if ($txt_composition != "")
	{
		$search_cond .= " and a.yarn_comp_type1st in (" .$txt_composition_id .")";
	}

	if ($show_val_column == 1)
	{
		$value_width = 400;
		$span = 3;
		$column = '<th rowspan="2" width="90">Avg. Rate (Tk)</th><th rowspan="2" width="110">Stock Value</th><th rowspan="2" width="100">Avg. Rate (USD)</th><th rowspan="2" width="100">Stock Value (USD)</th>';
	}
	else
	{
		$value_width = 0;
		$span = 0;
		$column = '';
	}
	//echo $store_wise;die;
	if ($store_wise == 1)
	{
		if ($store_name == 0)
			$store_cond .= "";
		else
			$store_cond .= " and a.store_id = $store_name";
		$table_width = '2900' + $value_width;
		$colspan = '28' + $span;

		if ($db_type == 0)
			$select_field = "group_concat(distinct(a.store_id))";
		else if ($db_type == 2)
			$select_field = "listagg(a.store_id,',') within group (order by a.store_id)";

		$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	}
	else
	{
		$select_field = "0";
		$table_width = '2900' + $value_width;
		$colspan = '29' + $span;
	}

	if ($cbo_company_name == 0)
	{
		$company_cond = "";
		$nameArray = sql_select("select allocation from variable_settings_inventory where item_category_id=1 and variable_list=18 and status_active=1 and is_deleted=0");
	}
	else
	{
		$company_cond = " and a.company_id=$cbo_company_name";
		$nameArray = sql_select("select allocation from variable_settings_inventory where company_name=$cbo_company_name and item_category_id=1 and variable_list=18 and status_active=1 and is_deleted=0");
	}
	$allocated_qty_variable_settings = $nameArray[0][csf('allocation')];

	$receive_array = array();
	$sql_receive = "Select a.prod_id, $select_field as store_id, max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone,
	sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as rcv_total_opening,
	sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as rcv_total_opening_amt,
	sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $from_date . "' then a.cons_rate else 0 end) as rcv_total_opening_rate,
	sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as purchase,
	sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as purchase_amt,
	sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_loan,
	sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_loan_amt,
	sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_inside_return,
	sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_inside_return_amt,
	sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_outside_return,
	sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_outside_return_amt
	from inv_transaction a, inv_receive_master c where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $store_cond group by a.prod_id";
	//echo $sql_receive;

	$result_sql_receive = sql_select($sql_receive);
	foreach ($result_sql_receive as $row)
	{
		$receive_array[$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
		$receive_array[$row[csf("prod_id")]]['rcv_total_opening'] = $row[csf("rcv_total_opening")];
		$receive_array[$row[csf("prod_id")]]['rcv_total_opening_amt'] = $row[csf("rcv_total_opening_amt")];
		$receive_array[$row[csf("prod_id")]]['purchase'] = $row[csf("purchase")];
		$receive_array[$row[csf("prod_id")]]['purchase_amt'] = $row[csf("purchase_amt")];
		$receive_array[$row[csf("prod_id")]]['rcv_loan'] = $row[csf("rcv_loan")];
		$receive_array[$row[csf("prod_id")]]['rcv_loan_amt'] = $row[csf("rcv_loan_amt")];
		$receive_array[$row[csf("prod_id")]]['rcv_inside_return'] = $row[csf("rcv_inside_return")];
		$receive_array[$row[csf("prod_id")]]['rcv_inside_return_amt'] = $row[csf("rcv_inside_return_amt")];
		$receive_array[$row[csf("prod_id")]]['rcv_outside_return'] = $row[csf("rcv_outside_return")];
		$receive_array[$row[csf("prod_id")]]['rcv_outside_return_amt'] = $row[csf("rcv_outside_return_amt")];
		$receive_array[$row[csf("prod_id")]]['weight_per_bag'] = $row[csf("weight_per_bag")];
		$receive_array[$row[csf("prod_id")]]['weight_per_cone'] = $row[csf("weight_per_cone")];
		$receive_array[$row[csf("prod_id")]]['rcv_total_opening_rate'] = $row[csf("rcv_total_opening_rate")];
	}

	unset($result_sql_receive);

	$issue_array = array();
	$sql_issue = "select a.prod_id, $select_field as store_id,
	sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as issue_total_opening,
	sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $from_date . "' then a.cons_rate else 0 end) as issue_total_opening_rate,
	sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as issue_total_opening_amt,
	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as issue_inside,
	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
	sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as issue_outside,
	sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_outside_amt,
	sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_return,
	sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_return_amt,
	sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as issue_loan,
	sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_loan_amt
	from inv_transaction a, inv_issue_master c
	where a.mst_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond group by a.prod_id";
	$result_sql_issue = sql_select($sql_issue);
	foreach ($result_sql_issue as $row)
	{
		$issue_array[$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
		$issue_array[$row[csf("prod_id")]]['issue_total_opening'] = $row[csf("issue_total_opening")];
		$issue_array[$row[csf("prod_id")]]['issue_total_opening_amt'] = $row[csf("issue_total_opening_amt")];
		$issue_array[$row[csf("prod_id")]]['issue_total_opening_rate'] = $row[csf("issue_total_opening_rate")];
		$issue_array[$row[csf("prod_id")]]['issue_inside'] = $row[csf("issue_inside")];
		$issue_array[$row[csf("prod_id")]]['issue_inside_amt'] = $row[csf("issue_inside_amt")];
		$issue_array[$row[csf("prod_id")]]['issue_outside'] = $row[csf("issue_outside")];
		$issue_array[$row[csf("prod_id")]]['issue_outside_amt'] = $row[csf("issue_outside_amt")];
		$issue_array[$row[csf("prod_id")]]['rcv_return'] = $row[csf("rcv_return")];
		$issue_array[$row[csf("prod_id")]]['rcv_return_amt'] = $row[csf("rcv_return_amt")];
		$issue_array[$row[csf("prod_id")]]['issue_loan'] = $row[csf("issue_loan")];
		$issue_array[$row[csf("prod_id")]]['issue_loan_amt'] = $row[csf("issue_loan_amt")];
	}

	unset($result_sql_issue);
	if ($store_wise == 1) {
		$trans_criteria_cond = "";
	} else {
		$trans_criteria_cond = " and c.transfer_criteria=1";
	}
	$transfer_qty_array = array();
	$sql_transfer = "select a.prod_id,
	sum(case when a.transaction_type=6 and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as trans_out_total_opening,
	sum(case when a.transaction_type=6 and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as trans_out_total_opening_amt,
	sum(case when a.transaction_type=6 and a.transaction_date<'" . $from_date . "' then a.cons_rate else 0 end) as trans_out_total_opening_rate,
	sum(case when a.transaction_type=5 and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as trans_in_total_opening,
	sum(case when a.transaction_type=5 and a.transaction_date<'" . $from_date . "' then a.cons_rate else 0 end) as trans_in_total_opening_rate,
	sum(case when a.transaction_type=5 and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as trans_in_total_opening_amt,
	sum(case when a.transaction_type=6 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as transfer_out_qty,
	sum(case when a.transaction_type=6 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as transfer_out_amt,
	sum(case when a.transaction_type=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as transfer_in_qty,
	sum(case when a.transaction_type=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as transfer_in_amt
	from inv_transaction a, inv_item_transfer_mst c where a.mst_id=c.id and a.transaction_type in (5,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1  and c.is_deleted=0 $trans_criteria_cond $store_cond group by a.prod_id";
	//echo $sql_transfer;
	$result_sql_transfer = sql_select($sql_transfer);
	foreach ($result_sql_transfer as $transRow)
	{
		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_qty'] = $transRow[csf("transfer_out_qty")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_amt'] = $transRow[csf("transfer_out_amt")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_qty'] = $transRow[csf("transfer_in_qty")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_amt'] = $transRow[csf("transfer_in_amt")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening'] = $transRow[csf("trans_out_total_opening")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_amt'] = $transRow[csf("trans_out_total_opening_amt")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening'] = $transRow[csf("trans_in_total_opening")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_amt'] = $transRow[csf("trans_in_total_opening_amt")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_rate'] = $transRow[csf("trans_in_total_opening_rate")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_rate'] = $transRow[csf("trans_out_total_opening_rate")];
	}

	unset($result_sql_transfer);

	if ($db_type == 0)
	{
		$yarn_allo_sql = sql_select("select product_id, group_concat(buyer_id) as buyer_id, group_concat(allocate_qnty) as allocate_qnty from com_balk_yarn_allocate where status_active=1 group by product_id");
	} else if ($db_type == 2)
	{
		$yarn_allo_sql = sql_select("select product_id, LISTAGG(CAST(buyer_id as VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY buyer_id) as buyer_id, LISTAGG(CAST(allocate_qnty AS VARCHAR(4000)),',') WITHIN GROUP(ORDER BY allocate_qnty) as allocate_qnty from com_balk_yarn_allocate where status_active=1 group by product_id");
	}
	$yarn_allo_arr = array();
	foreach ($yarn_allo_sql as $row)
	{
		$yarn_allo_arr[$row[csf("product_id")]]['product_id'] = $row[csf("product_id")];
		$yarn_allo_arr[$row[csf("product_id")]]['buyer_id'] = implode(",", array_unique(explode(",", $row[csf("buyer_id")])));
		$yarn_allo_arr[$row[csf("product_id")]]['allocate_qnty'] = implode(",", array_unique(explode(",", $row[csf("allocate_qnty")])));
	}

	unset($yarn_allo_sql);

	$sql_returnable = sql_select("select prod_id,sum(case when entry_form = 3 and trans_type=2 then returnable_qnty else 0 end ) as returnable_qnty,sum(case when entry_form = 9 and trans_type=4 then quantity else 0 end ) as issue_return_qty from order_wise_pro_details  where status_active=1 and is_deleted=0 and entry_form in(3,9) and trans_type in(2,4) group by prod_id");


	foreach ($sql_returnable as $row)
	{
		$returnable_qnty_arr[$row[csf("prod_id")]]['returnable_qnty'] = ($row[csf("returnable_qnty")]-$row[csf("issue_return_qty")]);
	}


	if ($type == 5)
	{
		$mrr_rate_sql = sql_select("select prod_id, min(transaction_date) as min_date, max(transaction_date), sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction
			where status_active=1 and is_deleted=0 and item_category=1 and transaction_type in(1,4,5) group by prod_id");
		$mrr_rate_arr = array();
		foreach ($mrr_rate_sql as $row) {
			$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
			$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
			$mrr_rate_arr[$row[csf("prod_id")]] = $row[csf("cons_amount")] / $row[csf("cons_quantiy")];
		}

		unset($mrr_rate_sql);

		$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit from product_details_master a
		where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0  $company_cond $search_cond
		group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";

		$result = sql_select($sql);
		$yarnTestQalityCommentsArr = return_library_array("select prod_id, yarn_quality_coments from inv_yarn_test_mst where  status_active=1 and is_deleted=0", 'prod_id', 'yarn_quality_coments');

		$i = 1;
		//ob_start();
		?>

		<div>
			<table width="1680" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Yarn Stock </td>
					</tr>
					<tr style="border:none;">
						<td colspan="16" align="center" style="border:none; font-size:14px;">
							Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
						</td>
					</tr>
					<tr  style="word-break:normal;">
						<th rowspan="2" width="40">SL</th>
						<th rowspan="2" width="120">Company Name</th>
						<th colspan="7">Description</th>
						<th rowspan="2" width="100">Stock In Hand</th>

						<th rowspan="2" width="100">Allocated to Order</th>
						<th rowspan="2" width="100">Un Allocated Qty.</th>

						<th rowspan="2" width="90">Returnable</th>
						<th rowspan="2" width="100">Cum. Stock Qty</th>
						<th rowspan="2" width="50">Age (Days)</th>
						<th rowspan="2" width="140">Yarn Quality<br>Comments</th>
						<th rowspan="2">Remarks</th>
					</tr>
					<tr>
						<th width="60">Prod.ID</th>
						<th width="60">Count</th>
						<th width="150">Composition</th>
						<th width="100">Yarn Type</th>
						<th width="80">Color</th>
						<th width="100">Lot</th>
						<th width="80">Supplier</th>
					</tr>
				</thead>
			</table>
			<div style="width:1680px; overflow-y:scroll; max-height:220px" id="scroll_body" >
				<table width="1660" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
					$grand_total_alocatted=0;$grand_total_free_stock=0;
					$tot_stock_value = 0;
					foreach ($result as $row)
					{
						$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));

						$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
						if ($row[csf("yarn_comp_type2nd")] != 0)
							$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
						$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
						$transfer_in_amt = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
						$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
						$transfer_out_amt = $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];

						$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
						$trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];
						$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];
						$trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];

						$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
						$openingBalanceAmt = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt);

						$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
						$totalRcvAmt = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $transfer_in_amt;
						$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;
						$totalIssueAmt = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] + $issue_array[$row[csf("id")]]['issue_loan_amt'] + $transfer_out_amt;

						$stockInHand = $openingBalance + $totalRcv - $totalIssue;
						$tot_rcv_qnty = $openingBalance + $totalRcv;
						$stockInHandAmt = $openingBalanceAmt + $totalRcvAmt - $totalIssueAmt;
						$tot_rcv_amt = $openingBalanceAmt + $totalRcvAmt;
								//$avg_rate=$tot_rcv_amt/$tot_rcv_qnty;
						$avg_rate = $mrr_rate_arr[$row[csf("id")]];

								//subtotal and group-----------------------
						$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];

						$returnable_qnty = $returnable_qnty_arr[$row[csf("id")]]['returnable_qnty'];
						$returnable_qnty = ($returnable_qnty<0)?0:$returnable_qnty;


						$cumStockQty = ($returnable_qnty+$stockInHand);

						if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0))
						{

							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							if ($value_with == 1)
							{
								if (number_format($stockInHand, 2) > 0.00)
								{
									if (!in_array($check_string, $checkArr)) {
										$checkArr[$i] = $check_string;
										if ($i > 1) {
											?>
											<tr bgcolor="#CCCCCC" style="font-weight:bold">
												<td colspan="9" align="right">Sub Total</td>
												<td align="right"><? echo number_format($total_stock_in_hand, 2); ?></td>
												<td align="right"><? echo number_format($total_alocatted, 2);$total_alocatted = 0; ?></td>
												<td align="right"><? echo number_format($total_free_stock, 2);$total_free_stock = 0; ?></td>
												<td align="right">&nbsp;</td>
												<td align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($sub_cum_stock_qty, 2); ?></td>
												<td align="right">&nbsp;</td>
												<td align="right">&nbsp;</td>
												<td align="right">&nbsp;</td>
											</tr>
											<?
											$total_stock_in_hand = 0;
											$sub_stock_value = 0;
											$sub_stock_value_usd = 0;

											$sub_cum_stock_qty=0;
										}
									}

									  $stock_value = $stockInHand * $avg_rate;
									  $avg_rate_usd = $avg_rate / $exchange_rate;
									  $stock_value_usd = $stock_value / $exchange_rate;
									  ?>
									  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="120"><p><? echo $companyArr[$row[csf("company_id")]]; ?>&nbsp;</p></td>
										<td width="60"><? echo $row[csf("id")]; ?></td>
										<td width="60"><p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?>&nbsp;</p></td>
										<td width="150"><p><? echo $compositionDetails; ?>&nbsp;</p></td>
										<td width="100"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
										<td width="80"><p><? echo $color_name_arr[$row[csf("color")]]; ?>&nbsp;</p></td>
										<td width="100"> <p>
											<?
											if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "")
											{
												?>
												<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"><? echo $row[csf("lot")]; ?></a>
												<?
											}else{
												echo $row[csf("lot")];
											}
											?>
										&nbsp;</p> </td>
										<td width="80"><p><? echo $supplierArr[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>
										<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" ><a href='##' onclick="openmypage_stock('<? echo $row[csf('id')] . "_" . $store_name ?>', 'stock_popup')"><? echo number_format($stockInHand, 2); ?></a></td>

										<td width="100" align="right"><p style="word-wrap:break-word; word-break: break-all;" ><a href='##' onclick="openmypage('<? echo $row[csf('id')]; ?>','allocation_popup')"><? echo number_format($row[csf("allocated_qnty")], 2);?></a></p></td>

										<td width="100" align="right"><p style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($row[csf("available_qnty")], 2) ;?></p></td>

										<td width="90" align="right" title=""><a href='##' onclick="openmypage('<? echo $row[csf('id')]; ?>','returnable_popup')"><? echo number_format($returnable_qnty, 4); ?></a></td>

										<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($cumStockQty, 2);?>
                                        </td>
										<td width="50" align="right"><? echo $ageOfDays; ?></td>
										<td width="140" align="left"><p>
											<?
											if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "")
											{
												?>
												<span class="comment more"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?> </span>
												<?
											}
											?>
										</p></td>
										<td align="right">
											<p>
												<?
												$buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
												$allocate_qnty_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['allocate_qnty']);
												$buyer_all = "";
												$m = 0;
												foreach ($buyer_id_arr as $buy_id) {
													if ($buyer_all != "")
														$buyer_all .= "<br>";
													$buyer_all .= $buy_short_name_arr[$buy_id];
													if ($buyer_all != "")
														$buyer_all .= "&nbsp;Qnty: " . $allocate_qnty_arr[$m];
													$m++;
												}
												echo $buyer_all;
												?>&nbsp;
											</p>
										</td>
									  </tr>
									  <?
									  $i++;

									  $total_stock_in_hand += $stockInHand;
									  $sub_stock_value += $stock_value;
									  $sub_stock_value_usd += $stock_value_usd;
									  $sub_cum_stock_qty +=  $cumStockQty;

									  $grand_total_stock_in_hand += $stockInHand;
									  $tot_stock_value += $stock_value;
									  $tot_stock_value_usd += $stock_value_usd;
									  $tot_cum_stock_qty += $cumStockQty;

									  $total_alocatted += $row[csf("allocated_qnty")];
									  $total_free_stock += $row[csf("available_qnty")];
									  $grand_total_alocatted += $row[csf("allocated_qnty")];
									  $grand_total_free_stock += $row[csf("available_qnty")];

								  }
							  }
						    else
							{
									if (!in_array($check_string, $checkArr))
									{
										$checkArr[$i] = $check_string;
										if ($i > 1) {
											?>
											<tr bgcolor="#CCCCCC" style="font-weight:bold">
												<td colspan="9" align="right">Sub Total</td>
												<td align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($total_stock_in_hand, 2); ?></td>
												<td align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($total_alocatted, 2);$total_alocatted = 0; ?></td>
												<td align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($total_free_stock, 2);$total_free_stock = 0; ?></td>
												<td align="right">&nbsp;</td>
												<td align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($sub_cum_stock_qty, 2); ?></td>
												<td align="right">&nbsp;</td>
												<td align="right">&nbsp;</td>
												<td width="" align="right">&nbsp;</td>
											</tr>
											<?
											$total_stock_in_hand = 0;
											$sub_stock_value = 0;
											$sub_cum_stock_qty = 0;
											$sub_stock_value_usd = 0;
										}
									}

									$stock_value = $stockInHand * $avg_rate;
									$avg_rate_usd = $avg_rate / $exchange_rate;
									$stock_value_usd = $stock_value / $exchange_rate;
							  ?>
							  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>
								<td width="120"><p><? echo $companyArr[$row[csf("company_id")]]; ?>&nbsp;</p></td>
								<td width="60"><? echo $row[csf("id")]; ?></td>
								<td width="60"><p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?>&nbsp;</p></td>
								<td width="150"><p><? echo $compositionDetails; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $color_name_arr[$row[csf("color")]]; ?>&nbsp;</p></td>
								<td width="100"><p style="word-break:break-all;">
									<?
									if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "")
									{
										?>
										<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
										<?
									}else{
										echo $row[csf("lot")];
									}
									?>
								&nbsp;</p></td>
								<td width="80"><p><? echo $supplierArr[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>
								<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" >
									<a href='##' onclick="openmypage_stock('<? echo $row[csf('id')] . "_" . $store_name ?>', 'stock_popup')"><? echo number_format($stockInHand, 2); ?></a>
								</td>

								<td width="100" align="right"><p style="word-wrap:break-word; word-break: break-all;" ><a href='##' onclick="openmypage('<? echo $row[csf('id')]; ?>','allocation_popup')"><? echo number_format($row[csf("allocated_qnty")], 2);?></a></p></td>
								<td width="100" align="right"><p style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($row[csf("available_qnty")], 2) ;?></p></td>


								<td width="90" align="right"><a href='##' onclick="openmypage('<? echo $row[csf('id')]; ?>','returnable_popup')"><? echo number_format($returnable_qnty, 4); ?></a></td>
								<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($cumStockQty, 2); ?></td>
								<td width="50" align="right"><? echo $ageOfDays; ?></td>
								<td width="140" align="left">
									<span class="comment more">
										<?
										if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "")
										{
											echo $yarnTestQalityCommentsArr[$row[csf("id")]];
										}
										?>
									</span>
								</td>
								<td align="right">
									<p>
										<?
										$buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
										$allocate_qnty_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['allocate_qnty']);
										$buyer_all = "";
										$m = 0;
										foreach ($buyer_id_arr as $buy_id) {
											if ($buyer_all != "")
												$buyer_all .= "<br>";
											$buyer_all .= $buy_short_name_arr[$buy_id];
											if ($buyer_all != "")
												$buyer_all .= "&nbsp;Qnty: " . $allocate_qnty_arr[$m];
											$m++;
										}
										echo $buyer_all;
										?>&nbsp;
									</p>
								</td>
							  </tr>
							  <?
							  $i++;
							  $total_stock_in_hand += $stockInHand;
							  $sub_stock_value += $stock_value;
							  $sub_stock_value_usd += $stock_value_usd;

							  $sub_cum_stock_qty += $cumStockQty;

							  $grand_total_stock_in_hand += $stockInHand;
							  $tot_stock_value += $stock_value;
							  $tot_stock_value_usd += $stock_value_usd;
							  $tot_cum_stock_qty += $cumStockQty;

							  $total_alocatted += $row[csf("allocated_qnty")];
							  $total_free_stock += $row[csf("available_qnty")];

							  $grand_total_alocatted += $row[csf("allocated_qnty")];
							  $grand_total_free_stock += $row[csf("available_qnty")];

							}
						}
					}
					?>
					<tr bgcolor="#CCCCCC" style="font-weight:bold">
						<td colspan="9" align="right">Sub Total</td>
						<td align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($total_stock_in_hand, 2); ?></td>
						<td align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($total_alocatted, 2);$total_alocatted = 0; ?></td>
						<td align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($total_free_stock, 2);$total_free_stock = 0; ?></td>
						<td align="right">&nbsp;</td>
						<td align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($sub_cum_stock_qty, 2); ?></td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</table>
			</div>
			<table width="1660" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">


				<tr class="tbl_bottom">
					<td width="40"></td>
					<td width="120"></td>
					<td width="60"></td>
					<td width="60"></td>
					<td width="150"></td>
					<td width="100"></td>
					<td width="80"></td>
					<td width="100"></td>
					<td width="80" align="right">Grand Total</td>
					<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"  id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 2); ?></td>
					<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($grand_total_alocatted, 2); ?></td>
					<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($grand_total_free_stock, 2); ?></td>
					<td width="90" align="right">&nbsp;</td>
					<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($tot_cum_stock_qty, 2); ?></td>
					<td width="50" align="right">&nbsp;</td>
					<td width="140" align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
				</tr>
			</table>
		</div>
		<?
	}

?>
<script>
	$(document).ready(function()
	{
		var showChar = 30;
		//var ellipsestext = "...";
		var ellipsestext = "";
		var moretext = "more";
		var lesstext = "less";
		$('.more').each(function() {

			var content = $(this).html();

			if(content.length > showChar) {

				var c = content.substr(0,showChar);
				var h = content.substr(showChar-1, content.length - showChar);

				var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

				$(this).html(html);
			}

		});

		$(".morelink").click(function(){

			if($(this).hasClass("less")) {
				$(this).removeClass("less");
				$(this).html(moretext);
			} else {
				$(this).addClass("less");
				$(this).html(lesstext);
			}

			$(this).parent().prev().toggle();
			$(this).prev().toggle();
			return false;
		});
	});
</script>
<?

$html = ob_get_contents();
ob_clean();
        //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
foreach (glob("*.xls") as $filename) {
            //if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
}
        //---------end------------//
$name = time();
$filename = $user_id . "_" . $name . ".xls";
$create_new_doc = fopen($filename, 'w');
$is_created = fwrite($create_new_doc, $html);
echo "$html**$filename";
exit();
}

if ($action == "yarn_test_report")
{
	extract($_REQUEST);
	echo load_html_head_contents("Yarn Test Report Print", "../../../../", 1, 1, '', '', '');
	$data = explode('*', $data);

	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

	$supplierArr 		= return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$yarn_count_arr 	= return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr 	= return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buy_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');



	if($db_type==0) $color_cond=" and color_name!=''"; else $color_cond=" and color_name IS NOT NULL";
	$color_range_arr = return_library_array("select id,color_name from lib_color where status_active=1 and grey_color=1 $color_cond order by color_name", "id", "color_name");


	$sql_for_array = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit
	from product_details_master a
	where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.company_id='$data[0]'
	group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
	$product_array=array();
	$result = sql_select($sql_for_array);
	foreach( $result as $prod_val){

		$compositionDetails = $composition[$prod_val[csf("yarn_comp_type1st")]] . " " . $prod_val[csf("yarn_comp_percent1st")] . "%\n";
		if ($prod_val[csf("yarn_comp_type2nd")] != 0)
			$compositionDetails .= $composition[$prod_val[csf("yarn_comp_type2nd")]] . " " . $prod_val[csf("yarn_comp_percent2nd")] . "%";

		$product_array[$prod_val[csf('lot')]]['count']			= $prod_val[csf('yarn_count_id')];
		$product_array[$prod_val[csf('lot')]]['composition']	= $compositionDetails;
		$product_array[$prod_val[csf('lot')]]['color']			= $prod_val[csf('color')];
		$product_array[$prod_val[csf('lot')]]['yarn_type']		= $prod_val[csf('yarn_type')];
		$product_array[$prod_val[csf('lot')]]['supplier_id']	= $prod_val[csf('supplier_id')];
	}

	$sql = "select a.id, a.company_id, a.test_date, a.test_for, a.prod_id, a.lot_number, a.specimen_wgt, a.specimen_length, a.fabric_construct, a.color, a.actual_yarn_count, a.yarn_apperance_grad, a.twist_per_inc, a.moisture_content, a.csp_minimum, a.csp_actual, a.yarn_quality_coments,
	b.id as dtls_id, b.fabric_fault, b.fabric_point, b.fabric_tot_point, b.fabric_point_y2, b.fabric_class, b.remarks
	from inv_yarn_test_mst a, inv_yarn_test_dtls b
	where a.id=b.mst_id and a.company_id='$data[0]' and a.prod_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$data_arr = sql_select($sql);
	?>
	<div style="width:800px;" align="center">
		<div style="width:100%;">
			<table width="100%" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
				<tr class="form_caption">
					<?
					$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
					?>
					<td align="left" width="50">
						<?
						foreach ($data_array as $img_row)
						{
							?>
							<img src='../../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
							align="middle"/>
							<?
						}
						?>
					</td>
					<td colspan="2" align="center">
						<strong style="font-size:18px"><? echo $company_library[$data[0]]; ?></strong><br>
						<?
						echo show_company($data[0], '', array('city'));
						?>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center" style="font-size:16px"><strong><u>Assessment of Numerical Test & Physical Inspection Report
						<br/>Yarn Test Report</u></strong></center></td>
					</tr>
					<tr> <td colspan="3">&nbsp;</td> </tr>
					<tr>
						<?
						if($data_arr[0][csf('test_for')] == 1){
							$checked_bulk = "checked";
						}else if($data_arr[0][csf('test_for')] == 2){
							$checked_sample = "checked";
						}else{
							$checked_bulk = "";
							$checked_sample = "";
						}
						$checked = "checked";
						?>
						<td colspan="3" style="text-align:center;"><strong>
							<input type="checkbox" name="bulk_yarn" value="Bulk Yarn" <? echo $checked_bulk; ?> onClick="return false;" /> Bulk Yarn
							<input type="checkbox" name="sample_yarn" value="Sample Yarn" <? echo $checked_sample; ?> onClick="return false;" /> Sample Yarn
						</strong></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:left;">
							<strong>Test Date: </strong><? echo change_date_format($data_arr[0][csf('test_date')]); ?>
						</td>
						<td style="text-align:right; padding-right:10px;">
							<strong>K. Date: </strong>&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
						</td>
					</tr>
					<tr>
						<td colspan="3"><strong>Product Details : </strong> <?

						$product_dtls = $data_arr[0][csf('prod_id')].", ".$data_arr[0][csf('lot_number')].", ".$yarn_count_arr[$product_array[$data_arr[0][csf('lot_number')]]['count']].", ".$product_array[$data_arr[0][csf('lot_number')]]['composition'].", ".$color_name_arr[$product_array[$data_arr[0][csf('lot_number')]]['color']].", ".$yarn_type[$product_array[$data_arr[0][csf('lot_number')]]['yarn_type']].", ".$product_array[$data_arr[0][csf('lot_number')]]['composition'].", ".$supplierArr[$product_array[$data_arr[0][csf('lot_number')]]['supplier_id']];
						echo $product_dtls;

						?></td>
					</tr>

				</table>
			</div>
			<div style="width:100%;">
				<table style="" cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
					<tr>
						<td colspan="3"  bgcolor="#dddddd"><b>Basic Yarn Information :</b></td>
					</tr>
					<tr>
						<td width="35" align="center">1</td><td width="61%">Tested Sample Weight</td><td  style="text-align:center;"><? echo $data_arr[0][csf('specimen_wgt')]; ?> kg</td>
					</tr>
					<tr>
						<td width="35" align="center">2</td><td >Tested Sample Length</td><td  style="text-align:center;"><? echo $data_arr[0][csf('specimen_length')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">3</td><td >Fabric Construction</td><td  style="text-align:center;"><? echo $data_arr[0][csf('fabric_construct')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">4</td><td >Color Range</td><td  style="text-align:center;"><? echo $color_range_arr[$data_arr[0][csf('color')]]; ?></td>
					</tr>
					<tr>
						<td colspan="3"  bgcolor="#dddddd"><b>Numerical Test :</b></td>
					</tr>
					<tr>
						<td width="35" align="center">1</td><td >Actual Yarn Count</td><td  style="text-align:center;"><? echo $data_arr[0][csf('actual_yarn_count')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">2</td><td >Yarn Appearance (Grade)</td><td  style="text-align:center;"><? echo $data_arr[0][csf('yarn_apperance_grad')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">3</td><td >Twist Per Inch (TPI)</td><td  style="text-align:center;"><? echo $data_arr[0][csf('twist_per_inc')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">4</td><td >Moisture Content</td><td  style="text-align:center;"><? echo $data_arr[0][csf('moisture_content')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">5</td><td >CSP Minimum</td><td  style="text-align:center;"><? echo $data_arr[0][csf('csp_minimum')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">6</td><td >CSP Actual</td><td  style="text-align:center;"><? echo $data_arr[0][csf('csp_actual')]; ?></td>
					</tr>
				</table>
			</div>
			<br/>
			<div style="width:100%;">
				<table cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
					<thead bgcolor="#dddddd" style="font-size:13px">
						<tr>
							<td colspan="7"><strong> Physical Test : </strong></td>
						</tr>
						<tr>
							<th width="35" align="center">SL</th>
							<th width="150">Fabric Fault</th>
							<th width="70">Point</th>
							<th width="70">Total Point</th>
							<th width="70">Point / 100Y2</th>
							<th width="120">Fabric Class</th>
							<th >Remarks</th>
						</tr>
					</thead>
					<tbody>
						<?
						$i=1;
						foreach($data_arr as $row)
						{
							?>
							<tr valign="middle" height="30">
								<td width="35" align="center"><? echo $i; ?></td>
								<td ><? echo $row[csf('fabric_fault')]; ?></td>
								<td align="center"><? echo $row[csf('fabric_point')]; ?></td>
								<td align="center"><? echo $row[csf('fabric_tot_point')]; ?></td>
								<td align="center"><? echo $row[csf('fabric_point_y2')]; ?></td>
								<td align="center"><? echo $row[csf('fabric_class')]; ?></td>
								<td style="text-align:justify;"><? echo $row[csf('remarks')]; ?></td>
							</tr>
							<?
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<br/>
			<div style="width:100%;">
				<table cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
					<tr>
						<td width="100%" colspan="7" style=" text-align:justify; word-break:normal; height:80px;text-align:left;" valign="top"><strong><u>Yarn Quality Comments</u></strong>:  <? echo $data_arr[0][csf('yarn_quality_coments')]; ?></td>
					</tr>
				</table>
			</div>
			<br/> <br/> <br/><br/> <br/> <br/>
			<div style="width:100%;">
				<table cellspacing="0" width="100%" border="0" >
					<tr align="center">
						<td>Manager QA(Knit)</td>
						<td>Sr. Manager(Knit)</td>
						<td>Manager QA(Dyeing)</td>
						<td>GM/DCM(Textile)</td>
						<td>H. of Textile QA & Test Lab.</td>
					</tr>
				</table>
			</div>
		</div>
		<?
	}

if ($action == "mrr_remarks")
{
	echo load_html_head_contents("MRR Statement", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	$sql= "Select a.id, a.recv_number, a.receive_date, b.cons_quantity, a.remarks
	from inv_receive_master a, inv_transaction b
	where a.id=b.mst_id and a.entry_form=1 and b.item_category=1 and b.transaction_type=1 and b.prod_id=$prod_id and b.status_active=1 and a.status_active=1";
//echo $sql;
	$result= sql_select($sql);
	?>
	<div align="center">
		<table width="550" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="50">SL</th>
					<th width="130">MRR NO.</th>
					<th width="100">MRR Date</th>
					<th width="100">MRR Qty</th>
					<th>Remarks</th>
				</tr>
			</thead>
			<tbody>
				<?
				$i=1;
				foreach($result as $row)
				{
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
						<td align="center"><? echo $i;?></td>
						<td><p><? echo $row[csf("recv_number")]; ?>&nbsp;</p></td>
						<td align="center"><p><? if($row[csf("receive_date")]!="" && $row[csf("receive_date")]!="0000-00-00") echo change_date_format($row[csf("receive_date")]); ?>&nbsp;</p></td>
						<td align="right"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
						<td><p><? echo $row[csf("remarks")]; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
				?>
			</tbody>
		</table>
		<br>
		<table width="550" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th>Balk Yarn Allocation Remarks</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td bgcolor="#FFFFFF" align="center">
						<?
						$all_remarks_arr=array();
						$all_remarks="";
						$remarks_result= sql_select("Select remarks as allocate_remark from com_balk_yarn_allocate where product_id=$prod_id and status_active=1 and is_deleted=0");
						foreach ($remarks_result as $value)
						{
							if(in_array($value[csf('allocate_remark')], $all_remarks_arr)==false)
							{
								$all_remarks_arr[]=$value[csf('allocate_remark')];
								$all_remarks.=$value[csf('allocate_remark')].",";
							}
						}
						echo chop($all_remarks, ",");
						?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?
	exit();
}

if ($action == "allocation_popup")
{
	echo load_html_head_contents("Allocation Statement", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	$buy_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

	if ($db_type == 0) {
		$sql_allocation = "select item_id,job_no, po_break_down_id,group_concat(booking_no) booking_no, sum(qnty) as allocate_qty,is_dyied_yarn,allocation_date from inv_material_allocation_dtls where status_active=1 and is_deleted=0 and item_id='$prod_id' and qnty>0 group by item_id,job_no,po_break_down_id,is_dyied_yarn,allocation_date";
	}else{
		$sql_allocation = "select item_id,job_no, po_break_down_id,listagg(cast(booking_no as varchar2(4000)), ',') within group (order by booking_no) as booking_no, sum(qnty) as allocate_qty,is_dyied_yarn,allocation_date from inv_material_allocation_dtls where status_active=1 and is_deleted=0 and item_id='$prod_id' and qnty>0 group by item_id,job_no,po_break_down_id,is_dyied_yarn,allocation_date";
	}
	$result_allocation = sql_select($sql_allocation);

	$po_break_down_arr = $job_arr =  array();
	foreach ($result_allocation as $row) {
		/*if($row[csf("is_dyied_yarn")] == 1){
			$job_arr[$row[csf("job_no")]] = "'".$row[csf("job_no")]."'";
		} else {
			$po_break_down_arr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
		}*/
		$job_arr[$row[csf("job_no")]] = "'".$row[csf("job_no")]."'";
	}

	if(!empty($job_arr)){
		$sql_cond = " and a.job_no_mst in(".implode(",",$job_arr).")";
	}else{
		$sql_cond = (!empty($po_break_down_arr))?" and a.id in(".implode(",",$po_break_down_arr).")":"";
	}

	$po_number_arr = array();
	if($sql_cond !=""){
		$po_sql = sql_select("select a.id,a.job_no_mst,a.shipment_date,b.buyer_name, a.file_no,a.grouping,a.po_number from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $sql_cond");
		foreach ($po_sql as $row) {
			$po_number_arr[$row[csf("id")]]['po'] = $row[csf("po_number")];
			$po_number_arr[$row[csf("id")]]['file'] = $row[csf("file_no")];
			$po_number_arr[$row[csf("id")]]['ref'] = $row[csf("grouping")];
			$po_number_arr[$row[csf("id")]]['job_no'] = $row[csf("job_no_mst")];
			$po_number_arr[$row[csf("id")]]['shipment_date'] = $row[csf("shipment_date")];
			$po_number_arr[$row[csf("job_no_mst")]]['buyer_name'] = $row[csf("buyer_name")];
		}
	}

	$job_no_array = array();
	$jobsql = "select a.id, a.job_no,a.buyer_id,a.sales_booking_no, a.style_ref_no,a.within_group from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0";
	$jobData = sql_select($jobsql);
	foreach ($jobData as $row) {
		$sales_order_arr[$row[csf('job_no')]]['sales_booking_no']=$row[csf('sales_booking_no')];
		$sales_order_arr[$row[csf('job_no')]]['buyer_id']=$row[csf('buyer_id')];
		$sales_order_arr[$row[csf('job_no')]]['within_group']=$row[csf('within_group')];
	}

	$planning_array = array();
	$plan_sql="select a.po_id,a.booking_no,a.dtls_id,b.requisition_no,b.prod_id from ppl_planning_entry_plan_dtls a,ppl_yarn_requisition_entry b where a.dtls_id=b.knit_id and a.status_active=1 and b.status_active=1 and b.prod_id=$prod_id group by a.po_id,a.booking_no,a.dtls_id,b.requisition_no,b.prod_id";
	$planData = sql_select($plan_sql);
	foreach ($planData as $row) {
		$planning_array[$row[csf('po_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]]=$row[csf('booking_no')];
	}

	$issue_array = $job_wose_issue_array = $issue_basis_arr = array();
	$prod_cond2 = " and c.prod_id in (".$prod_id.")";
	$sql_issue = "select a.id,a.issue_basis,b.requisition_no,c.po_breakdown_id,c.prod_id, sum(c.quantity) as issue_qty,b.job_no from inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and (a.issue_purpose in(1,2) or a.issue_purpose in(1,2,15) and b.job_no is not null) and c.trans_type=2 and a.entry_form=3 and b.item_category=1 $prod_cond2  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by a.id,a.issue_basis,b.requisition_no,c.po_breakdown_id,c.prod_id,b.job_no";

	$result_issue = sql_select($sql_issue);
	$issue_array_req=$booking_arr=array();
	foreach ($result_issue as $row) {

		$issue_job[$row[csf("id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] = $row[csf("job_no")];
		$issue_basis_arr[$row[csf("id")]]["basis"] = $row[csf("issue_basis")];
		$issue_basis_arr[$row[csf("id")]]["issue_id"] = $row[csf("id")];

		if($row[csf('issue_basis')] == 1){
			$issue_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
			$issue_arr[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
		}else{
			$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			$booking_arr[] = $planning_array[$row[csf('po_breakdown_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]];
			$booking_arr = array_unique($booking_arr);
			foreach ($booking_arr as $booking) {
				if($booking != ""){
					$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_qty"] += $row[csf("issue_qty")];
					$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_id"][] = $row[csf("id")];
				}
			}


		}
		$job_wose_issue_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']] += $row[csf("issue_qty")];
	}

	$issue_return_array = $issue_return_req_array = $job_wose_issue_return_array = array();
	$sql_return = "Select b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose, sum(c.quantity) as issue_return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and c.trans_type=4 and a.entry_form=9 and b.id=c.trans_id and b.item_category=1 and c.issue_purpose in(1,2,7,15,38,46) $prod_cond2 and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose";
	$result_return = sql_select($sql_return);
	foreach ($result_return as $row) {
		$issue_basis = $issue_basis_arr[$row[csf("issue_id")]]["basis"];
		if($issue_basis == 3){
			$issue_id = $issue_basis_arr[$row[csf("issue_id")]]["issue_id"];
			$issue_return_req_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$issue_id] = $row[csf("issue_return_qty")];
		}else{
			$issue_job = $issue_job[$row[csf("issue_id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]];
			if($issue_job!="" && ($row[csf("issue_purpose")]==7 || $row[csf("issue_purpose")]==15 || $row[csf("issue_purpose")]==38 || $row[csf("issue_purpose")]==46)){
				$issue_return_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
			}else{
				$issue_return_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
			}
		}

		$job_wose_issue_return_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']] += $row[csf("issue_return_qty")];
	}

	if($prod_id!="")
	{
		$sql_rcv_rtn = "select a.prod_id,sum(case when a.transaction_type in (3)  then a.cons_quantity else 0 end) as recieved_return_qty
from inv_transaction a, product_details_master b, inv_issue_master c
where a.mst_id=c.id and a.prod_id=b.id and b.dyed_type=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id=$prod_id  group by a.prod_id";
		$result_sql_rcv_rtn = sql_select($sql_rcv_rtn);

		$rcv_rtn_qty_arr = array();
		foreach ($result_sql_rcv_rtn as $row) {
			$rcv_rtn_qty_arr[$row[csf("prod_id")]] = $row[csf("recieved_return_qty")];
		}
	}
	?>
	<script>
		function openmypage_job(job,prod_id,action,title,popup_width)
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'daily_yarn_stock_report_controller.php?job='+job+'&prod_id='+prod_id+'&action='+action, title, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../../');
		}
	</script>
	<div align="center">
		<table width="1090" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<th width="25">SL</th>
				<th width="75">Date</th>
				<th width="110">Job /FSO NO.</th>
				<th width="100">Buyer</th>
				<th width="100">Order No.</th>
				<th width="100">Shipment Date</th>
				<th width="100">File No.</th>
				<th width="100">Ref. No.</th>
				<th width="110">Booking No.</th>
				<th width="75">Allocated Qty</th>
				<th width="70">Issue Qty</th>
				<th width="60">Issue Rtn Qty</th>
				<th width="">Cumul. Balance</th>
			</thead>
		</table>
		<div style="width:1090px; max-height:300px" id="scroll_body">
			<table width="1090" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?
				$i = 1;
				$balance = '';
				if(!empty($result_allocation)){
					foreach ($result_allocation as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";


						if($row[csf("is_dyied_yarn")] == 1){

							$issue_qty = $job_wose_issue_array[$row[csf("job_no")]];
						}else{
							$issue_qty=0;

							if($row[csf("po_break_down_id")]==""){
								$issue_basis = array_unique($issue_arr[$row[csf('job_no')]][$prod_id]);
							}else{
								$issue_basis = array_unique($issue_arr[$row[csf("po_break_down_id")]][$prod_id]);
							}
							foreach ($issue_basis as $basis) {
								if($basis==3){
									if($row[csf("booking_no")] != ""){
										$booking_nos = explode(",",$row[csf("booking_no")]);
										foreach ($booking_nos as $booking_row) {

											$issue_qty += $issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_qty"];
										}
									}
								}else{
									$issue_qty += $issue_array[$row[csf('job_no')]][$prod_id]["issue_qty"];
								}
							}
						}

						$within_group=$sales_order_arr[$row[csf('job_no')]]['within_group'];
						$sales_booking_no=$sales_order_arr[$row[csf('job_no')]]['sales_booking_no'];

						if($sales_booking_no!="")
						{
							$return_qty = 0;
							if($within_group==1)
							{
								$buyer_id=return_field_value("buyer_id as buyer_id","wo_booking_mst","booking_no ='".$sales_booking_no."' and is_deleted=0 and status_active=1","buyer_id");
								$buyername=$buy_name_arr[$buyer_id];
								$po_number=$po_number_arr[$row[csf("po_break_down_id")]]['po'];
							}
							else
							{
								$buyer_id=$sales_order_arr[$row[csf('job_no')]]['buyer_id'];
								$buyername=$buy_name_arr[$buyer_id];
								$po_number="";
							}
							$shipment_date = "";
						}
						else
						{
							$po_number=$po_number_arr[$row[csf("po_break_down_id")]]['po'];
							$buyername=$buy_name_arr[$po_number_arr[$row[csf("job_no")]]['buyer_name']];
							$shipment_date = $po_number_arr[$row[csf("po_break_down_id")]]['shipment_date'];
							if($row[csf("is_dyied_yarn")] == 1){
								$return_qty = $job_wose_issue_return_array[$row[csf("job_no")]];
							}else{
								foreach ($issue_basis as $basis) {
									if($basis==3){
										$booking_nos = explode(",",$row[csf("booking_no")]);
										foreach ($booking_nos as $booking_row) {
											$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_id"]);
											$return_qty=0;
											foreach ($issue_ids as $issue_id) {
												$return_qty += $issue_return_req_array[$row[csf('po_break_down_id')]][$prod_id][$issue_id];
											}
										}
									}else{
										$return_qty = $issue_return_array[$row[csf("po_break_down_id")]][$prod_id];
									}
								}
							}
						}
						$booking_no = implode(", ",array_unique(explode(",", $row[csf("booking_no")])));
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="25" align="center"><? echo $i; ?></td>
							<td width="75" align="center"><? echo change_date_format($row[csf("allocation_date")]); ?></td>
							<td width="110">
								<div style="word-wrap:break-word; width:110px;text-align: center;">
									<? if($sales_booking_no!="")
									{
										?>
										<a href="#report_details" onClick="openmypage_job('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('item_id')]; ?>','requisition_details_popup','Requisition Details','480px')"><? echo $row[csf("job_no")]; ?></a>
										<?
									}
									else
									{
										echo $row[csf("job_no")];
									}
									?>

								</div>
							</td>
							<td width="100" align="center"><p><? echo $buyername; ?></div></td>
								<td width="100" title='<? echo $row[csf("po_break_down_id")]; ?>' align="center"><p><? echo $po_number; ?></p></td>
								<td width="100" align="center"><? echo change_date_format($shipment_date); ?></td>
								<td width="100"><p><? echo $po_number_arr[$row[csf("po_break_down_id")]]['file']; ?></p></td>
								<td width="100"><p><? echo $po_number_arr[$row[csf("po_break_down_id")]]['ref']; ?></p></td>
								<td width="110"><p><? echo $booking_no; ?></p></td>
								<td width="75" align="right">
								<?
								$rcv_rtn_qty = $rcv_rtn_qty_arr[$row[csf("item_id")]];
								$allocate_qty = $row[csf("allocate_qty")];
								if($row[csf("is_dyied_yarn")]==1)
								{
									$allocate_qty = ($allocate_qty-$rcv_rtn_qty);
								}
								echo number_format($allocate_qty, 2);
								?>
								</td>
								<td width="70" align="right"><? echo number_format($issue_qty, 2); ?></td>
								<td width="60" align="right"><? echo number_format($return_qty, 2); ?></td>
								<td align="right">
									<?
									$balance = $balance + $row[csf("allocate_qty")] - $issue_qty + $return_qty;
									echo number_format($balance, 2);
									?>
								</td>
							</tr>
							<?
							$i++;
							$issue_qty=$return_qty=0;
						}
					}else{
						echo "<tr colspan='13'><th style='text-align:center;'>No Data Found</th></tr>";
					}
					?>
				</table>
			</div>
		</div>
		<?
		exit();
	}

if ($action == "requisition_details_popup")
{
	echo load_html_head_contents("Requisition Statement", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	if ($db_type == 0) {
		$grp_con="group_concat(dtls_id) as prog_no";
	} else {
		$grp_con="LISTAGG(dtls_id, ',') WITHIN GROUP (ORDER BY id) as prog_no";
	}
	$job_no_array = array();
	$jobsql = ("select a.id, a.job_no,a.sales_booking_no, a.style_ref_no,a.within_group from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.job_no='$job'");
	$jobData = sql_select($jobsql);
	foreach ($jobData as $row) {
		$job_no_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
		$job_no_array[$row[csf('id')]]['job_id'] = $row[csf('id')];
		$job_no_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
		$po_id=$row[csf('id')];
		$within_group=$row[csf('within_group')];
	}
	$prog_no=return_field_value("$grp_con","ppl_planning_entry_plan_dtls","po_id ='".$po_id."' and is_deleted=0 and status_active=1 and is_sales=1","prog_no");
	?>
	<div>
    <fieldset style="width:430px; text-align:center">
        <legend>Requisition Details</legend>
        <table width="400" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <th width="50">SL</th>
                <th width="100">Requisition Date</th>
                <th width="130">Requisition No</th>
                <th width="">Requisition Qty.</th>
            </thead>
        </table>
        <div style="width:420px; overflow-y:scroll; max-height:300px" id="scroll_body">
            <table width="400" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                <?
                $i = 1; $tot_recv_qnty=0;
                $sql = "select a.id, a.knit_id, a.requisition_no, a.requisition_date,sum(a.yarn_qnty) as qnty from ppl_yarn_requisition_entry a	where a.status_active=1 and a.is_deleted=0 and a.prod_id in($prod_id) and a.knit_id in($prog_no) group by a.id, a.knit_id, a.requisition_no,a.requisition_date order by a.id";
                $result = sql_select($sql);
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    $receive_qty = $row[csf("qnty")];
                    $tot_recv_qnty += $receive_qty;
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="50"><? echo $i; ?></td>
                        <td width="100"><p><? echo change_date_format($row[csf("requisition_date")]); ?></p></td>
                        <td width="130" align="center"><? echo $row[csf("requisition_no")]; ?>&nbsp;</td>
                        <td width="" align="right"><? echo number_format($receive_qty, 2); ?>&nbsp;</td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                <tfoot>
                    <tr>
                        <th colspan="3" align="right">Total </th>
                        <th  align="right"><? echo number_format($tot_recv_qnty, 2); ?> </th>
                    </tr>
                </tfoot>
            </table>

        </fieldset>
    </div>
    <?
}

if ($action == "stock_popup") {
				echo load_html_head_contents("Stock Details", "../../../../", 1, 1, $unicode, '', '');
				extract($_REQUEST);
				$prod_id_ref = explode("_", $prod_id);
				$prod_id = $prod_id_ref[0];
				$store_id = $prod_id_ref[1];
				?>
				<fieldset style="width:720px">
					<legend>Yarn Receive Details</legend>
					<table width="720" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
						<thead>
							<th width="50">SL</th>
							<th width="120">MRR No.</th>
							<th width="100">Receive Date</th>
							<th width="110">Receive Qty.</th>
							<th width="110">Receive Basis</th>
							<th>BTB LC No.</th>
						</thead>
					</table>
					<div style="width:720px; overflow-y:scroll; max-height:300px" id="scroll_body">
						<table width="700" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
							<?
							$i = 1;
							$tot_recv_qnty = '';
							$btblc_arr = return_library_array("select b.pi_id, a.lc_number from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.pi_entry_form=165", 'pi_id', 'lc_number');
							$store_cond = "";
							if ($store_id > 0)
								$store_cond = " and b.store_id=$store_id";
							$sql = "select c.id, c.recv_number, c.receive_date, c.receive_basis, c.booking_id, sum(b.cons_quantity) as qnty from inv_transaction b, inv_receive_master c
							where b.mst_id=c.id and b.prod_id=$prod_id and c.entry_form=1 and b.transaction_type=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 $store_cond group by c.id, c.recv_number, c.receive_date, c.receive_basis, c.booking_id order by c.id";

							$result = sql_select($sql);
							foreach ($result as $row) {
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								$receive_qty = $row[csf("qnty")];
								$tot_recv_qnty += $receive_qty;

								$btblc_no = '';
								if ($row[csf("receive_basis")] == 1) {
									$btblc_no = $btblc_arr[$row[csf("booking_id")]];
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="50"><? echo $i; ?></td>
									<td width="120"><p><? echo $row[csf("recv_number")]; ?></p></td>
									<td width="100" align="center"><? echo change_date_format($row[csf("receive_date")]); ?>&nbsp;</td>
									<td width="110" align="right"><? echo number_format($receive_qty, 2); ?>&nbsp;</td>
									<td width="110"><p><? echo $receive_basis_arr[$row[csf("receive_basis")]]; ?></p></td>
									<td><p><? echo $btblc_no; ?>&nbsp;</p></td>
								</tr>
								<?
								$i++;
							}

							$sql = "select c.id,c.transfer_system_id,c.transfer_date,sum(b.cons_quantity) as qnty from inv_transaction b,inv_item_transfer_mst c where b.mst_id=c.id and b.prod_id=$prod_id and b.transaction_type=5 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 group by c.id, c.transfer_system_id, c.transfer_date order by c.id";
							$result = sql_select($sql);
							foreach ($result as $row) {
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								$receive_qty = $row[csf("qnty")];
								$tot_recv_qnty += $receive_qty;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="50"><? echo $i; ?></td>
									<td width="120"><p><? echo $row[csf("transfer_system_id")]; ?></p></td>
									<td width="100" align="center"><? echo change_date_format($row[csf("transfer_date")]); ?>&nbsp;</td>
									<td width="110" align="right"><? echo number_format($receive_qty, 2); ?>&nbsp;</td>
									<td width="110">Transfer</td>
									<td>&nbsp;</td>
								</tr>
								<?
								$i++;
							}
							?>
							<tfoot>
								<th colspan="3">Total</th>
								<th><? echo number_format($tot_recv_qnty, 2); ?>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tfoot>
						</table>
					</div>
				</fieldset>
				<?
				exit();
			}

if ($action == "stock_popup_mrr") {
	echo load_html_head_contents("Stock Details", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	$pord_ref = explode(",", $prod_id);
	$prod_id = $pord_ref[0];
	$mrr_id = $pord_ref[1];
	$type = $pord_ref[2];
	?>
	<fieldset style="width:720px">
		<legend>Yarn Receive Details</legend>
		<table width="720" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<th width="50">SL</th>
				<th width="120">MRR No.</th>
				<th width="100">Receive Date</th>
				<th width="110">Receive Qty.</th>
				<th width="110">Receive Basis</th>
				<th>BTB LC No.</th>
			</thead>
		</table>
		<div style="width:720px; overflow-y:scroll; max-height:300px" id="scroll_body">
			<table width="700" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?
				$i = 1;
				$tot_recv_qnty = '';
				$btblc_arr = return_library_array("select b.pi_id, a.lc_number from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.item_category_id=1", 'pi_id', 'lc_number');
				if (($type == 1) || $type == 4) {
					$sql = "select c.id, c.recv_number, c.receive_date, c.receive_basis, c.booking_id, sum(b.cons_quantity) as qnty from inv_transaction b, inv_receive_master c
					where b.mst_id=c.id and b.prod_id=$prod_id and c.id=$mrr_id and c.entry_form in(1,9) and b.transaction_type in(1,4) and b.item_category=1 and b.status_active=1 and b.is_deleted=0 group by c.id, c.recv_number, c.receive_date, c.receive_basis, c.booking_id order by c.id";
				} else if($type== 5){
					$sql = "select c.id, c.transfer_system_id as recv_number, c.transfer_date as receive_date,  null as receive_basis, 0 as booking_id, sum(b.cons_quantity) as qnty from inv_transaction b,  inv_item_transfer_mst c
					where b.mst_id=c.id and b.prod_id=$prod_id and c.id=$mrr_id and b.transaction_type=5 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 group by c.id, c.transfer_system_id, c.transfer_date order by c.id";
				}

	//echo $sql;

				$result = sql_select($sql);
				foreach ($result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					$receive_qty = $row[csf("qnty")];
					$tot_recv_qnty += $receive_qty;

					$btblc_no = '';
					if ($row[csf("receive_basis")] == 1) {
						$btblc_no = $btblc_arr[$row[csf("booking_id")]];
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="50"><? echo $i; ?></td>
						<td width="120"><p><? echo $row[csf("recv_number")]; ?></p></td>
						<td width="100" align="center"><? echo change_date_format($row[csf("receive_date")]); ?>&nbsp;</td>
						<td width="110" align="right"><? echo number_format($receive_qty, 2); ?>&nbsp;</td>
						<td width="110"><p><? echo $receive_basis_arr[$row[csf("receive_basis")]]; ?></p></td>
						<td><p><? echo $btblc_no; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}

				$sql = "select c.id, c.transfer_system_id, c.transfer_date, sum(b.cons_quantity) as qnty from inv_transaction b, inv_item_transfer_mst c
				where b.mst_id=c.id and b.prod_id=$prod_id and b.transaction_type=5 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 group by c.id, c.transfer_system_id, c.transfer_date order by c.id";
				$result = sql_select($sql);
				foreach ($result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					$receive_qty = $row[csf("qnty")];
					$tot_recv_qnty += $receive_qty;
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="50"><? echo $i; ?></td>
						<td width="120"><p><? echo $row[csf("transfer_system_id")]; ?></p></td>
						<td width="100" align="center"><? echo change_date_format($row[csf("transfer_date")]); ?>&nbsp;</td>
						<td width="110" align="right"><? echo number_format($receive_qty, 2); ?>&nbsp;</td>
						<td width="110">Transfer</td>
						<td>&nbsp;</td>
					</tr>
					<?
					$i++;
				}
				?>
				<tfoot>
					<th colspan="3">Total</th>
					<th><? echo number_format($tot_recv_qnty, 2); ?>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if ($action == "transferPopup") {
	echo load_html_head_contents("Transfer Details", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);

	if ($store_name == 0)
		$store_cond = "";
	else
		$store_cond = " and a.store_id=$store_name";

	$sql_transfer = "select
	sum(case when c.transfer_criteria=1 then a.cons_quantity else 0 end) as com_trans_qty,
	sum(case when c.transfer_criteria=2 then a.cons_quantity else 0 end) as store_trans_qty
	from inv_transaction a, inv_item_transfer_mst c where a.mst_id=c.id and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' and a.transaction_type in (5,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=$trans_type and c.transfer_criteria in(1,2) and a.prod_id=$prod_id $store_cond";
	$transferData = sql_select($sql_transfer);
	$com_trans_qty = $transferData[0][csf('com_trans_qty')];
	$store_trans_qty = $transferData[0][csf('store_trans_qty')];

	$cap_arr = array(5 => "In", 6 => "Out");
	?>
	<div align="center">
		<fieldset style="width:420px; margin-left:7px;">
			<legend>Transfer <? echo $cap_arr[$trans_type]; ?> Details</legend>
			<table width="400" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<th width="50">SL</th>
					<th width="150">Transfer Type</th>
					<th>Transfer Qty.</th>
				</thead>
				<tr bgcolor="#E9F3FF" onclick="change_color('tr_1', '#E9F3FF')" id="tr_1">
					<td width="50">1</td>
					<td width="150">Company To Company</td>
					<td align="right"><? echo number_format($com_trans_qty, 2); ?>&nbsp;</td>
				</tr>
				<tr bgcolor="#FFFFFF" onclick="change_color('tr_2', '#FFFFFF')" id="tr_2">
					<td width="50">2</td>
					<td width="150">Store To Store</td>
					<td align="right"><? echo number_format($store_trans_qty, 2); ?>&nbsp;</td>
				</tr>
				<tfoot>
					<th colspan="2">Total</th>
					<th><? echo number_format($com_trans_qty + $store_trans_qty, 2); ?>&nbsp;</th>
				</tfoot>
			</table>
		</fieldset>
	</div>
	<?
	exit();
}

if($action=="company_wise_report_button_setting"){
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=36 and is_deleted=0 and status_active=1");

	if($print_report_format != "")
	{
		$print_report_format_arr=explode(",",$print_report_format);
		echo "$('#search').hide();\n";
		echo "$('#search1').hide();\n";
		echo "$('#search2').hide();\n";
		echo "$('#search3').hide();\n";
		echo "$('#search4').hide();\n";
		echo "$('#search5').hide();\n";
		echo "$('#search6').hide();\n";
		echo "$('#search7').hide();\n";

		foreach($print_report_format_arr as $id)
		{
			if($id==152){echo "$('#search').show();\n";}
			if($id==108){echo "$('#search1').show();\n";}
			if($id==102){echo "$('#search2').show();\n";}
			if($id==103){echo "$('#search3').show();\n";}
			if($id==104){echo "$('#search4').show();\n";}
			if($id==105){echo "$('#search5').show();\n";}
			if($id==106){echo "$('#search6').show();\n";}
			if($id==107){echo "$('#search7').show();\n";}
		}
	}
	else
	{
		echo "$('#search').show();\n";
		echo "$('#search1').show();\n";
		echo "$('#search2').show();\n";
		echo "$('#search3').show();\n";
		echo "$('#search4').show();\n";
		echo "$('#search5').show();\n";
		echo "$('#search6').show();\n";
		echo "$('#search7').show();\n";
	}
	exit();
}

if($action == "composition_popup")
{
	echo load_html_head_contents("Composition Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);

	?>
	<script>

	var selected_id = new Array(); var selected_name = new Array();

	function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

		tbl_row_count = tbl_row_count-1;
		for( var i = 1; i <= tbl_row_count; i++ ) {
			js_set_value( i );
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
	var old=document.getElementById('txt_pre_composition_row_id').value;
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
	toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

	if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
		selected_id.push( $('#txt_individual_id' + str).val() );
		selected_name.push( $('#txt_individual' + str).val() );

	}
	else {
		for( var i = 0; i < selected_id.length; i++ ) {
			if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
		}
		selected_id.splice( i, 1 );
		selected_name.splice( i, 1 );
	}

	var id = ''; var name = '';
	for( var i = 0; i < selected_id.length; i++ ) {
		id += selected_id[i] + ',';
		name += selected_name[i] + ',';
	}

	id = id.substr( 0, id.length - 1 );
	name = name.substr( 0, name.length - 1 );

	$('#hidden_composition_id').val(id);
	$('#hidden_composition').val(name);
}
</script>
</head>
<fieldset style="width:390px">
<legend>Yarn Receive Details</legend>
<input type="hidden" name="hidden_composition" id="hidden_composition" value="">
<input type="hidden" name="hidden_composition_id" id="hidden_composition_id" value="">
<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
	<thead>
		<tr>
			<th colspan="2">
				<? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?>
			</th>
		</tr>
		<tr>
			<th width="50">SL</th>
			<th width="">Composition Name</th>
		</tr>
	</thead>
</table>
<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
	<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
		<?
		$i = 1;

		$result=sql_select("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name");
		$pre_composition_id_arr=explode(",",$pre_composition_id);
		foreach ($result as $row)
		{
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";


			if(in_array($row[csf("id")],$pre_composition_id_arr))
			{
				if($pre_composition_ids=="") $pre_composition_ids=$i; else $pre_composition_ids.=",".$i;
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
				<td width="50">
					<? echo $i; ?>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
					<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("composition_name")]; ?>"/>
				</td>
				<td width=""><p><? echo $row[csf("composition_name")]; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>
		<input type="hidden" name="txt_pre_composition_row_id" id="txt_pre_composition_row_id" value="<?php echo $pre_composition_ids; ?>"/>
	</table>
</div>
<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
	<tr>
		<td align="center" height="30" valign="bottom">
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
</fieldset>
<script type="text/javascript">
	setFilterGrid('table_body',-1);
	set_all();
</script>
<?
}

if ($action == "returnable_popup")
{
	echo load_html_head_contents("Returnable Quantity", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	$buy_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

	$planning_array = array();
	$plan_sql="select a.po_id,a.booking_no,a.dtls_id,b.requisition_no,b.prod_id from ppl_planning_entry_plan_dtls a,ppl_yarn_requisition_entry b where a.dtls_id=b.knit_id and a.status_active=1 and b.status_active=1 and b.prod_id=$prod_id group by a.po_id,a.booking_no,a.dtls_id,b.requisition_no,b.prod_id";
	$planData = sql_select($plan_sql);
	foreach ($planData as $row) {
		$planning_array[$row[csf('po_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]]=$row[csf('booking_no')];
	}

	$sql_issue_retun = "Select a.recv_number,a.receive_basis,a.booking_id,a.receive_date,a.issue_id,c.po_breakdown_id,c.prod_id,
	sum(c.quantity) as issue_return_qty
	from inv_receive_master a,inv_transaction b,order_wise_pro_details c
	where a.id=b.mst_id and b.id=c.trans_id and b.item_category=1 and c.entry_form ='9' and c.trans_type=4 and c.status_active=1 and c.prod_id=$prod_id
	group by a.recv_number,a.receive_basis,a.booking_id,a.receive_date,a.issue_id,c.po_breakdown_id,c.prod_id";

	$issuer_return = sql_select($sql_issue_retun);
	$issue_return_array = array();
	foreach ($issuer_return as $row)
	{
		$issue_return_array[$row[csf("issue_id")]][$row[csf("prod_id")]]['issue_return_qty'] = $row[csf("issue_return_qty")];
		$issue_return_array[$row[csf("issue_id")]][$row[csf("prod_id")]]['booking_id'] = $row[csf("booking_id")];
	}

	$sql_returnable = "Select a.id,a.issue_basis,a.booking_id,a.issue_date,c.po_breakdown_id,c.prod_id,
	sum(c.returnable_qnty) AS returable_qty
	from inv_issue_master a,inv_transaction b, order_wise_pro_details c
	where a.id=b.mst_id and b.id=c.trans_id and b.item_category=1 and c.entry_form ='3' and c.trans_type=2 and c.status_active=1 and c.prod_id=$prod_id and c.returnable_qnty>0
	group by a.id,a.issue_basis,a.booking_id,a.issue_date,c.po_breakdown_id,c.prod_id";
	$result = sql_select($sql_returnable);

	foreach ($result as $row)
	{
		$order_id .= $row[csf("po_breakdown_id")].",";
	}

	$order_id = chop($order_id,",");

	if($order_id!="")
	{
		$po_sql = "select a.id,a.job_no_mst,a.shipment_date,b.buyer_name, a.file_no,a.grouping,a.po_number from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.id in($order_id)";
		$po_result = sql_select($po_sql);
	foreach ($po_result as $row) {
			$po_number_arr[$row[csf("id")]]['po'] = $row[csf("po_number")];
			$po_number_arr[$row[csf("id")]]['file'] = $row[csf("file_no")];
			$po_number_arr[$row[csf("id")]]['ref'] = $row[csf("grouping")];
			$po_number_arr[$row[csf("id")]]['job_no'] = $row[csf("job_no_mst")];
			$po_number_arr[$row[csf("id")]]['shipment_date'] = $row[csf("shipment_date")];
			$po_number_arr[$row[csf("id")]]['buyer_name'] = $row[csf("buyer_name")];
		}
	}
	?>
	<div align="center">
		<table width="1090" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<th width="25">SL</th>
				<th width="75">Date</th>
				<th width="110">Job</th>
				<th width="100">Buyer</th>
				<th width="100">Order No.</th>
				<th width="110">Booking No.</th>
				<th width="75">Returnable Qty</th>
				<th width="75">Issue Rtn Qty</th>
				<th width="75">Cumul. Rtn Balance</th>
			</thead>
		</table>
		<div style="width:1090px; max-height:300px" id="scroll_body">
		<table width="1090" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;
			$balance = '';
			if(!empty($result))
			{
				$total_returable_qty = $total_issue_return_qty = $total_cumul_rtn_balance = 0;
				foreach ($result as $row)
				{
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					$po_number = $po_number_arr[$row[csf("po_breakdown_id")]]['po'];
					$job_no_mst = $po_number_arr[$row[csf("po_breakdown_id")]]['job_no'];
					$buyername = $buy_name_arr[$po_number_arr[$row[csf("po_breakdown_id")]]['buyer_name']];

					$returable_qty = $row[csf('returable_qty')];
					$issue_return_qty = $issue_return_array[$row[csf("id")]][$row[csf("prod_id")]]['issue_return_qty'];
					$cumul_rtn_balance =  ($returable_qty-$issue_return_qty);
					$cumul_rtn_balance = ($cumul_rtn_balance<0)?0:$cumul_rtn_balance;

					$total_returable_qty += $returable_qty;
					$total_issue_return_qty += $issue_return_qty;
					$total_cumul_rtn_balance += $cumul_rtn_balance;

					if($row[csf("issue_basis")]==3){
						$booking_id = $issue_return_array[$row[csf("id")]][$row[csf("prod_id")]]['booking_id'];
						$booking_no = $planning_array[$row[csf("po_breakdown_id")]][$booking_id][$row[csf('prod_id')]];
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="25" align="center"><? echo $i; ?></td>
						<td width="75" align="center"><? echo change_date_format($row[csf("issue_date")]); ?></td>
						<td width="110" style="word-wrap:break-word; text-align: center;"><? echo $job_no_mst; ?></td>
						<td width="100" align="center"><p><? echo $buyername; ?></td>
						<td width="100" title='<? echo $po_number; ?>' align="center"><p><? echo $po_number; ?></p></td>
						<td width="110"><p><? echo $booking_no; ?></p></td>
						<td width="75" align="right"><? echo number_format($returable_qty, 2); ?></td>
						<td width="75" align="right"><? echo number_format($issue_return_qty, 2); ?></td>
						<td width="75" align="right"><? echo number_format($cumul_rtn_balance, 2); ?></td>
					</tr>
					<?
                    $i++;
                    $issue_qty=$return_qty=0;
				}
				?>
                <tfoot>
                    <th width="25">&nbsp;</th>
                    <th width="75">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="110">Total&nbsp;</th>
                    <th width="75"><? echo number_format($total_returable_qty, 2); ?></th>
                    <th width="75"><? echo number_format($total_issue_return_qty, 2); ?></th>
                    <th width="75"><? echo number_format($total_cumul_rtn_balance, 2); ?></th>
                </tfoot>

                <?

			}else{
				echo "<tr colspan='9'><th style='text-align:center;'>No Data Found</th></tr>";
			}
			?>
		</table>
		</div>
	</div>
	<?
exit();
}
?>