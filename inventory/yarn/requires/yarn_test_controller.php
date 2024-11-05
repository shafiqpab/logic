<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data		= $_REQUEST['data'];
$action		= $_REQUEST['action'];
$user_id 	= $_SESSION['logic_erp']["user_id"];
$permission = $_SESSION['page_permission'];

// Array Declare
$yarn_test_for_arr = array(1=>'Bulk Yarn',2=>'Sample Yarn');
$yarn_test_result_arr = array(1=>'Nil',2=>'Major',3=>'Minor');
$yarn_test_acceptance_arr = array(1=>'Yes',2=>'No');

$phys_test_knitting_arr = array(1=>'Stripe(Patta)', 2=>'Thick & Thin Yarn', 3=>'Neps', 4=>'Poly-Propaline(Plastic Conta)', 5=>'Color Conta/Yarn', 6=>'Dead Fiber', 7=>'No Of Slub', 8=>'No Of Hole', 9=>'No Of Slub Hole', 10=>'Moisture Efect', 11=>'No Of Yarn Breakage', 12=>'No Of Setup', 13=>'Knotting End', 14=>'Haireness', 15=>'Hand Feel', 16=>'Twisting', 17=>'Contamination', 18=>'Foregin Fiber', 19=>'Oil Stain Yarn', 20=>'Foreign Matters', 21=>'Unlevel', 22=>'Double Yarn', 23=>'Fiber Migration', 24=>'Excessive Hard Yarn');
//asort($phys_test_knitting_arr);
$phys_test_dyeing_and_finishing_arr = array(1=>'Stripe(Patta)', 2=>'Thick & Thin Yarn', 3=>'Neps', 4=>'Color Conta', 5=>'Dead Fiber/Cotton', 6=>'No Of Slub', 7=>'No Of Hole', 8=>'No Of Slub Hole', 9=>'Moisture Efect', 10=>'Shrinkage', 11=>'Dye Pick Up%', 12=>'Enzyme Dosting %', 13=>'Knotting End', 14=>'Haireness', 15=>'Hand Feel', 16=>'Contamination', 17=>'Soft Yarn/Loose Yarn', 18=>'Oil Stain Yarn', 19=>'Bad Piecing', 20=>'Oily Slub', 21=>'Foreign Matters', 22=>'Black Specks Test', 23=>'Cotton Seeds Test', 24=>'Bursting', 25=>'Pilling', 26=>'Lustre', 27=>'Process loss %');
//asort($phys_test_dyeing_and_finishing_arr);

//load drop down supplier
if ($action == "load_drop_down_supplier")
{
	if($data){$companyCon=" and a.tag_company='$data'";}
	else{$companyCon="";}
	echo create_drop_down("cbo_supplier", 120, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $companyCon and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}


if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$companyArr 		= return_library_array("select id,company_name from lib_company", "id", "company_name");
	//$companyArr[0] 	= "All Company";
	$supplierArr 		= return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$supplierFullArr 		= return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

	$yarn_count_arr 	= return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr 	= return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buy_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	if ($db_type == 0) {
		$exchange_rate = return_field_value("conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and is_deleted=0 order by id DESC limit 0,1");
		$current_server_date = change_date_format( date("Y-m-d"), 'yyyy-mm-dd');
	} else if ($db_type == 2) {
		$exchange_rate = return_field_value("conversion_rate", "(SELECT conversion_rate FROM currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 ORDER BY id DESC)", "ROWNUM = 1");
		$current_server_date = change_date_format( date("Y-m-d"), '', '', 1);
	} else {
		$current_server_date = "";
		$exchange_rate = 1;
	}

	if ($cbo_company_name == 0) {
		$company_cond = "";
		$nameArray = sql_select("select allocation from variable_settings_inventory where item_category_id=1 and variable_list=18 and status_active=1 and is_deleted=0");
	} else {
		$company_cond = " and a.company_id=$cbo_company_name";
		$nameArray = sql_select("select allocation from variable_settings_inventory where company_name=$cbo_company_name and item_category_id=1 and variable_list=18 and status_active=1 and is_deleted=0");
	}

	$allocated_qty_variable_settings = $nameArray[0][csf('allocation')];

	$receive_array = array();
	$sql_receive = "Select a.prod_id, max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone,
	sum(case when a.transaction_type in (1,4) and a.transaction_date < '" . $current_server_date . "' then a.cons_quantity else 0 end) as rcv_total_opening,
	sum(case when a.transaction_type in (1,4) and a.transaction_date < '" . $current_server_date . "' then a.cons_amount else 0 end) as rcv_total_opening_amt,
	sum(case when a.transaction_type in (1,4) and a.transaction_date < '" . $current_server_date . "' then a.cons_rate else 0 end) as rcv_total_opening_rate,
	sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date = '" . $current_server_date . "' then a.cons_quantity else 0 end) as purchase,
	sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date = '" . $current_server_date . "' then a.cons_amount else 0 end) as purchase_amt,
	sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date = '" . $current_server_date . "' then a.cons_quantity else 0 end) as rcv_loan,
	sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date = '" . $current_server_date . "' then a.cons_amount else 0 end) as rcv_loan_amt,
	sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date = '" . $current_server_date . "' then a.cons_quantity else 0 end) as rcv_inside_return,
	sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date = '" . $current_server_date . "' then a.cons_amount else 0 end) as rcv_inside_return_amt,
	sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date =  '" . $current_server_date . "' then a.cons_quantity else 0 end) as rcv_outside_return,
	sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date = '" . $current_server_date . "' then a.cons_amount else 0 end) as rcv_outside_return_amt
	from inv_transaction a, inv_receive_master c where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond group by a.prod_id";

	//echo $sql_receive; die;
	$result_sql_receive = sql_select($sql_receive);
	foreach ($result_sql_receive as $row)
	{
		//$receive_array[$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
		$receive_array[$row[csf("prod_id")]]['rcv_total_opening'] 		= $row[csf("rcv_total_opening")];
		$receive_array[$row[csf("prod_id")]]['rcv_total_opening_amt'] 	= $row[csf("rcv_total_opening_amt")];
		$receive_array[$row[csf("prod_id")]]['purchase'] 				= $row[csf("purchase")];
		$receive_array[$row[csf("prod_id")]]['purchase_amt'] 			= $row[csf("purchase_amt")];
		$receive_array[$row[csf("prod_id")]]['rcv_loan'] 				= $row[csf("rcv_loan")];
		$receive_array[$row[csf("prod_id")]]['rcv_loan_amt'] 			= $row[csf("rcv_loan_amt")];
		$receive_array[$row[csf("prod_id")]]['rcv_inside_return'] 		= $row[csf("rcv_inside_return")];
		$receive_array[$row[csf("prod_id")]]['rcv_inside_return_amt'] 	= $row[csf("rcv_inside_return_amt")];
		$receive_array[$row[csf("prod_id")]]['rcv_outside_return'] 		= $row[csf("rcv_outside_return")];
		$receive_array[$row[csf("prod_id")]]['rcv_outside_return_amt'] 	= $row[csf("rcv_outside_return_amt")];
		$receive_array[$row[csf("prod_id")]]['weight_per_bag'] 			= $row[csf("weight_per_bag")];
		$receive_array[$row[csf("prod_id")]]['weight_per_cone'] 		= $row[csf("weight_per_cone")];
		$receive_array[$row[csf("prod_id")]]['rcv_total_opening_rate'] 	= $row[csf("rcv_total_opening_rate")];
	}
	unset($result_sql_receive);

	$issue_array = array();
	$sql_issue = "select a.prod_id,
	sum(case when a.transaction_type in (2,3) and a.transaction_date < '" . $current_server_date . "' then a.cons_quantity else 0 end) as issue_total_opening,
	sum(case when a.transaction_type in (2,3) and a.transaction_date < '" . $current_server_date . "' then a.cons_rate else 0 end) as issue_total_opening_rate,
	sum(case when a.transaction_type in (2,3) and a.transaction_date < '" . $current_server_date . "' then a.cons_amount else 0 end) as issue_total_opening_amt,
	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  = '" . $current_server_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  = '" . $current_server_date . "' then a.cons_quantity else 0 end) as issue_inside,
	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  = '" . $current_server_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
	sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  = '" . $current_server_date . "' then a.cons_quantity else 0 end) as issue_outside,
	sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  = '" . $current_server_date . "' then a.cons_amount else 0 end) as issue_outside_amt,
	sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date = '" . $current_server_date . "' then a.cons_quantity else 0 end) as rcv_return,
	sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date = '" . $current_server_date . "' then a.cons_amount else 0 end) as rcv_return_amt,
	sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date = '" . $current_server_date . "' then a.cons_quantity else 0 end) as issue_loan,
	sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date = '" . $current_server_date . "' then a.cons_amount else 0 end) as issue_loan_amt
	from inv_transaction a, inv_issue_master c
	where a.mst_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.prod_id";

	$result_sql_issue = sql_select($sql_issue);
	foreach ($result_sql_issue as $row)
	{
		//$issue_array[$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
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
	$sql_transfer = " select a.prod_id, sum(case when a.transaction_type=6 and a.transaction_date < '" . $current_server_date . "' then a.cons_quantity else 0 end) as trans_out_total_opening, sum(case when a.transaction_type=6 and a.transaction_date < '" . $current_server_date . "' then a.cons_amount else 0 end) as trans_out_total_opening_amt, sum(case when a.transaction_type=6 and a.transaction_date < '" . $current_server_date . "' then a.cons_rate else 0 end) as trans_out_total_opening_rate, sum(case when a.transaction_type=5 and a.transaction_date < '" . $current_server_date . "' then a.cons_quantity else 0 end) as trans_in_total_opening, sum(case when a.transaction_type=5 and a.transaction_date < '" . $current_server_date . "' then a.cons_rate else 0 end) as trans_in_total_opening_rate, sum(case when a.transaction_type=5 and a.transaction_date < '" . $current_server_date . "' then a.cons_amount else 0 end) as trans_in_total_opening_amt, sum(case when a.transaction_type=6 and a.transaction_date = '" . $current_server_date . "' then a.cons_quantity else 0 end) as transfer_out_qty, sum(case when a.transaction_type=6 and a.transaction_date = '" . $current_server_date . "' then a.cons_amount else 0 end) as transfer_out_amt, sum(case when a.transaction_type=5 and a.transaction_date = '" . $current_server_date . "' then a.cons_quantity else 0 end) as transfer_in_qty, sum(case when a.transaction_type=5 and a.transaction_date = '" . $current_server_date . "' then a.cons_amount else 0 end) as transfer_in_amt from inv_transaction a, inv_item_transfer_mst c where a.mst_id=c.id and a.transaction_type in (5,6) and a.item_category=1 and a.status_active=1 and a is_deleted=0 and c.status_active=1  and c.is_deleted=0 $trans_criteria_cond group by a.prod_id";

	$result_sql_transfer = sql_select($sql_transfer);
	foreach ($result_sql_transfer as $transRow)
	{
		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_qty'] 			= $transRow[csf("transfer_out_qty")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_amt'] 			= $transRow[csf("transfer_out_amt")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_qty'] 			= $transRow[csf("transfer_in_qty")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_amt'] 			= $transRow[csf("transfer_in_amt")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening'] 	= $transRow[csf("trans_out_total_opening")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_amt'] = $transRow[csf("trans_out_total_opening_amt")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening'] 		= $transRow[csf("trans_in_total_opening")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_amt'] 	= $transRow[csf("trans_in_total_opening_amt")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_rate'] 	= $transRow[csf("trans_in_total_opening_rate")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_rate'] 	= $transRow[csf("trans_out_total_opening_rate")];
	}
	unset($result_sql_transfer);

	if ($db_type == 0) {
		$yarn_allo_sql = sql_select("select product_id, group_concat(buyer_id) as buyer_id, group_concat(allocate_qnty) as allocate_qnty from com_balk_yarn_allocate where status_active=1 group by product_id");
	    //LISTAGG(CAST( a.lc_sc_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_id) as lc_id
	} else if ($db_type == 2) {
		$yarn_allo_sql = sql_select("select product_id, LISTAGG(CAST(buyer_id as VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY buyer_id) as buyer_id, LISTAGG(CAST(allocate_qnty AS VARCHAR(4000)),',') WITHIN GROUP(ORDER BY allocate_qnty) as allocate_qnty from com_balk_yarn_allocate where status_active=1 group by product_id");
	}

	$yarn_allo_arr = array();
	foreach ($yarn_allo_sql as $row)
	{
		$yarn_allo_arr[$row[csf("product_id")]]['product_id'] 	= $row[csf("product_id")];
		$yarn_allo_arr[$row[csf("product_id")]]['buyer_id'] 	= implode(",", array_unique(explode(",", $row[csf("buyer_id")])));
		$yarn_allo_arr[$row[csf("product_id")]]['allocate_qnty'] = implode(",", array_unique(explode(",", $row[csf("allocate_qnty")])));
	}
	unset($yarn_allo_sql);

	$mrr_rate_sql = sql_select("select prod_id, min(transaction_date) as min_date, max(transaction_date), sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction
	where status_active=1 and is_deleted=0 and item_category=1 and transaction_type in(1,4,5) group by prod_id");
	$mrr_rate_arr = array();
	foreach ($mrr_rate_sql as $row)
	{
		$date_array[$row[csf("prod_id")]]['min_date'] 	= $row[csf("min_date")];
		$date_array[$row[csf("prod_id")]]['max_date'] 	= $row[csf("max_date")];
		$mrr_rate_arr[$row[csf("prod_id")]] 			= $row[csf("cons_amount")] / $row[csf("cons_quantiy")];
	}
	unset($mrr_rate_sql);

	$cbo_supplier=str_replace("'","",$cbo_supplier);
	$txt_lot=trim($txt_lot);
	$cbo_yarn_count=str_replace("'","",$cbo_yarn_count);
	$cbo_yarn_type=str_replace("'","",$cbo_yarn_type);

	$sql_cond="";
	if ($cbo_company_name>0) $sql_cond =" and a.company_id=$cbo_company_name";
	if ($cbo_supplier>0) $sql_cond.=" and a.supplier_id='$cbo_supplier'";
	if ($txt_lot!="") $sql_cond.=" and a.lot = '$txt_lot'";
	if ($cbo_yarn_count>0) $sql_cond.=" and a.yarn_count_id='$cbo_yarn_count'";
	if ($cbo_yarn_type>0)  $sql_cond.=" and a.yarn_type=$cbo_yarn_type ";

	$check_variable = sql_select("select yes_no from VARIABLE_SETTINGS_INVENTORY where VARIABLE_LIST=49 and company_name = $cbo_company_name");
	$is_receive = $check_variable[0]['YES_NO']; 

	if($is_receive == 1) //test source yarn receive
	{
		$sql_cond="";
		if ($cbo_company_name>0) $sql_cond =" and a.company_id=$cbo_company_name";
		if ($cbo_supplier>0) $sql_cond.=" and a.supplier_id='$cbo_supplier'";
		if ($txt_lot!="") $sql_cond.=" and a.lot = '$txt_lot'";
		if ($cbo_yarn_count>0) $sql_cond.=" and a.yarn_count_id='$cbo_yarn_count'";
		if ($cbo_yarn_type>0)  $sql_cond.=" and a.yarn_type=$cbo_yarn_type ";

		$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit
		from product_details_master a
		where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $sql_cond
		group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";

		
		//  echo $sql;die;

		$result = sql_select($sql);

		$i = 1;
		ob_start();
		?>
		<div style="width:800px;" >
			<table width="800" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="40">SL</th>
						<th width="60">Prod.ID</th>
						<th width="100">Lot</th>
						<th width="60">Count</th>
						<th width="">Composition</th>
						<th width="80">Color</th>
						<th width="100">Yarn Type</th>
						<th width="80">Supplier</th>
						<th width="119">Stock</th>
					</tr>
				</thead>
			</table>
			<div style="width:800px;overflow-y:scroll; max-height:350px" id="scroll_body" >
				<table width="780" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?
					$tot_stock_value = 0;
					foreach ($result as $row)
					{

						$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
						if ($row[csf("yarn_comp_type2nd")] != 0)
							$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";

						$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
						$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

						$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
						$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

						$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);

						$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;

						$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;

						//echo $openingBalance.'='.$totalRcv.'='.$totalIssue."<br>";
						$stockInHand = $openingBalance + $totalRcv - $totalIssue;
						//echo $openingBalance;

						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";


						if($stockInHand >= $txt_qnty)
						{
							if($value_with == 1)
							{
								if (number_format($stockInHand, 2) > 0.00)
								{
									/*show_list_view('<? echo $row[csf("id")]."*".$row[csf("lot")];?>','create_yarn_test_popup_list_view','list_container','requires/yarn_test_controller','setFilterGrid("list_view",-1)');*/
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="60"><? echo $row[csf("id")]; ?></td>
										<td width="100"><p><a href='##' onClick="openmypage_stock('<? echo $row[csf("id")]."*".$row[csf("lot")]; ?>', 'lot_popup');">
										<? echo $row[csf("lot")]; ?>
										</a></p></td>
										<td width="60"><p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></p></td>
										<td width=""><p><? echo $compositionDetails; ?></p></td>
										<td width="80"><p><? echo $color_name_arr[$row[csf("color")]]; ?></p></td>
										<td width="100"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?></p></td>
										<td width="80"><p><? echo $supplierArr[$row[csf("supplier_id")]]; ?></p></td>
										<td width="100" align="right"><? echo number_format($stockInHand, 2); ?></td>
									</tr>
									<?
									$i++;
								}
							}
							else
							{
								$stockInHand = (int)$stockInHand;
								if(number_format($stockInHand, 2) <= 0.00)
								{
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="60"><? echo $row[csf("id")]; ?></td>
										<td width="100"><p><a href='##' onClick="openmypage_stock('<? echo $row[csf("id")]."*".$row[csf("lot")]; ?>', 'lot_popup');">
										<? echo $row[csf("lot")]; ?>
										</a></p></td>
										<td width="60"><p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></p></td>
										<td width=""><p><? echo $compositionDetails; ?></p></td>
										<td width="80"><p><? echo $color_name_arr[$row[csf("color")]]; ?></p></td>
										<td width="100"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?></p></td>
										<td width="80"><p><? echo $supplierArr[$row[csf("supplier_id")]]; ?></p></td>
										<td width="100" align="right"><? echo number_format($stockInHand, 2); ?></td>
									</tr>
									<?
									$i++;
								}
							}
						}

					}
				?>
				</table>
			</div>
		</div>
		<?
		exit();
	}
	else if($is_receive == 2) //test source yarn parking GRN
	{
		$sql_cond="";
		if ($cbo_company_name>0) $sql_cond =" and a.company_id=$cbo_company_name";
		if ($cbo_supplier>0) $sql_cond.=" and a.supplier_id='$cbo_supplier'";
		if ($txt_lot!="") $sql_cond.=" and b.lot = '$txt_lot'";
		if ($cbo_yarn_count>0) $sql_cond.=" and b.yarn_count='$cbo_yarn_count'";
		if ($cbo_yarn_type>0)  $sql_cond.=" and b.yarn_type=$cbo_yarn_type ";

		$sql = "select a.id, a.company_id, a.supplier_id, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, b.color_name, b.lot, b.parking_quantity from inv_receive_master a, quarantine_parking_dtls b where     a.id = b.mst_id and a.status_active = 1 and b.status_active = 1 and a.is_deleted = 0 and b.is_deleted = 0 $sql_cond";

		// echo $sql;die;

		$result = sql_select($sql);

		// echo "<pre>";
		// print_r($result); die;

		$i = 1;
		ob_start();
		?>
		<div style="width:800px;" >
			<table width="800" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="40">SL</th>
						<!-- <th width="60">Prod.ID</th> -->
						<th width="100">Lot</th>
						<th width="60">Count</th>
						<th width="">Composition</th>
						<th width="80">Color</th>
						<th width="100">Yarn Type</th>
						<th width="80">Supplier</th>
						<th width="119">Parking Quantity</th>
					</tr>
				</thead>
			</table>
			<div style="width:800px;overflow-y:scroll; max-height:350px" id="scroll_body" >
				<table width="780" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?
					$tot_stock_value = 0;
					foreach ($result as $row)
					{
						$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";


						if($row[csf("parking_quantity")] >= $txt_qnty)
						{
							if($value_with == 1)
							{
								if (number_format($row[csf("parking_quantity")], 2) > 0.00)
								{
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40"><? echo $i; ?></td>
										
										<td width="100"><p><a href='##' onClick="openmypage_stock('<? echo $row[csf("id")]."*".$row[csf("lot")]; ?>', 'lot_popup');">
										<? echo $row[csf("lot")]; ?>
										</a></p></td>
										<td width="60"><p><? echo $yarn_count_arr[$row[csf("yarn_count")]]; ?></p></td>
										<td width=""><p><? echo $compositionDetails; ?></p></td>
										<td width="80"><p><? echo $color_name_arr[$row[csf("color_name")]]; ?></p></td>
										<td width="100"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?></p></td>
										<td width="80"><p><? echo $supplierFullArr[$row[csf("supplier_id")]]; ?></p></td>
										<td width="100" align="right"><?= number_format($row[csf("parking_quantity")], 2)?></td>
									</tr>
									<?
									$i++;
								}
							}
							else
							{
								if(number_format($row[csf("parking_quantity")], 2) <= 0.00)
								{
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40"><? echo $i; ?></td>
										
										<td width="100"><p><a href='##' onClick="openmypage_stock('<? echo $row[csf("id")]."*".$row[csf("lot")]; ?>', 'lot_popup');">
										<? echo $row[csf("lot")]; ?>
										</a></p></td>
										<td width="60"><p><? echo $yarn_count_arr[$row[csf("yarn_count")]]; ?></p></td>
										<td width=""><p><? echo $compositionDetails; ?></p></td>
										<td width="80"><p><? echo $color_name_arr[$row[csf("color_name")]]; ?></p></td>
										<td width="100"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?></p></td>
										<td width="80"><p><? echo $supplierFullArr[$row[csf("supplier_id")]]; ?></p></td>
										<td width="100" align="right"><?= number_format($row[csf("parking_quantity")], 2)?></td>
									</tr>
									<?
									$i++;
								}
							}
						}

					}
				?>
				</table>
			</div>
		</div>
		<?
		exit();
	}
	
}

if ($action == "set_ready_to_app")
{
	list($ready_to_app_stauts,$update_id)=explode('_',$data);
	$con = connect();
	$rID=sql_update("INV_YARN_TEST_MST","READY_TO_APPROVED",$ready_to_app_stauts,"id",$update_id,0);
	if($rID) $flag=1; else $flag=0;

	if($flag==1)
	{
		oci_commit($con);
		echo "1**".$update_id;
	}
	else
	{
		oci_rollback($con);
		echo "10**";
	}


	disconnect($con);
}


if ($action == "lot_popup")
{
	echo load_html_head_contents("Stock Details", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	$prod_id_ref = explode("*", $prod_id);
	$prod_id = $prod_id_ref[0];
	$lot_number = $prod_id_ref[1];


	?>
	<script>
		var permission = '<?php echo $permission; ?>';

		function set_data(id)
		{
			reset_form('LotNoPopUpFrm_1','','','','','update_id*cbo_company_id*prod_id*lot_number');
			get_php_form_data(id, 'populate_all_data_to_form', 'yarn_test_controller');
			$('#cboColor').attr('disabled','disabled');
		}

		function fnc_yarn_test_entry(operation)
		{

			if(form_validation('txtTestDate*cboTestFor*cboColor','Test Date*Test For*Color')==false)
			{
				return;
			}

			if( operation == 2 )
			{
				var r=confirm(" Press \"OK\" to Delete.  \n Press \"Cancel\" for not.");
				if (r==true)
				{
				}
				else
				{
					return;
				}
			}

			var update_id    = document.getElementById('update_id').value;
			var data_all = "";

			var data_all_knit = "";
			var total_row_knit = $("#knitingDataContainer tbody tr").length;
			var knitting_str="<? echo implode(',',array_keys($phys_test_knitting_arr)); ?>";
			var knit = knitting_str.split(',');

			var knitDataStr=Array();
			for (var i=0; i<total_row_knit; i++)
			{
				//data_all_knit+=get_submitted_data_string('testingParamKnit_'+knit[i]+'*txtPointKnit_'+knit[i]+'*cboResultKnit_'+knit[i]+'*cboAcceptanceKnit_'+knit[i]+'*txtFabricClassKnit_'+knit[i]+'*txtRemarksKnit_'+knit[i]+'*updatedtlsidknit_'+knit[i],"../../../",knit[i]);

				knitDataStr.push('testingParamKnit_'+knit[i]+'*txtPointKnit_'+knit[i]+'*cboResultKnit_'+knit[i]+'*cboAcceptanceKnit_'+knit[i]+'*txtFabricClassKnit_'+knit[i]+'*txtRemarksKnit_'+knit[i]+'*updatedtlsidknit_'+knit[i]);
			}

			knitDataStr=knitDataStr.join('*');
			data_all_knit+=get_submitted_data_string(knitDataStr,"../../../",1);

			var data_all_dye = "";
			var total_row_dye = $("#dyingDataContainer tbody tr").length;
			var dyeing_str="<? echo implode(',',array_keys($phys_test_dyeing_and_finishing_arr));?>";
			var dye = dyeing_str.split(',');

			var dyeDataStr=Array();
			for (var j=0; j<total_row_dye; j++)
			{
				//data_all_dye+=get_submitted_data_string('testingParamDye_'+dye[j]+'*txtPointDye_'+dye[j]+'*cboResultDye_'+dye[j]+'*cboAcceptanceDye_'+dye[j]+'*txtFabricClassDye_'+dye[j]+'*txtRemarksDye_'+dye[j]+'*updatedtlsiddye_'+dye[j],"../../../",dye[j]);
				dyeDataStr.push('testingParamDye_'+dye[j]+'*txtPointDye_'+dye[j]+'*cboResultDye_'+dye[j]+'*cboAcceptanceDye_'+dye[j]+'*txtFabricClassDye_'+dye[j]+'*txtRemarksDye_'+dye[j]+'*updatedtlsiddye_'+dye[j]);
			}

			dyeDataStr=dyeDataStr.join('*');
			data_all_dye += get_submitted_data_string(dyeDataStr,"../../../",1);

			data_all = data_all_knit+data_all_dye;

			var data="action=SaveUpdateDeleteTestData&operation="+operation+get_submitted_data_string('cbo_company_id*prod_id*lot_number*txtTestDate*cboTestFor*txtSpecimentWgt*txtSpecimentLenght*cboColor*txtReceiveQty*txtLcNumber*txtLcQty*txtActualYarnCountReq*txtActualYarnCountPhys*txtYarnAppearanceReq*txtYarnAppearancePhys*txtYarnComReq*txtYarnComPhys*txtPilReq*txtPilPhys*txtBrusReq*txtBrusPhys*txtTwistPerInchReq*txtTwistPerInchPhys*txtMoistureContentReq*txtMoistureContentPhys*txtIpiValueReq*txtIpiValuePhys*txtCspMinimumReq*txtCspMinimumPhys*txtCspActualReq*txtCspActualPhys*txtThinYarnReq*txtThinYarnPhys*txtThickReq*txtThickPhys*txtUReq*txtUPhys*txtCvReq*txtCvPhys*txtNepsPerKmReq*txtNepsPerKmPhys*txtHearinessReq*txtHearinessPhys*txtCountsCvReq*txtCountsCvPhys*txtSystemResult*txtGreyGsmKnit*txtGreyGashGsmKnit*txtRequiredGsmKnit*txtRequiredDiaKnit*txtMachineDiaKnit*txtStichLengthKnit*txtGreyGsmDye*txtBatchDye*txtFinishGsmDye*txtFinishDiaDye*txtLengthDye*txtWidthDye*cboCommentsAcceptanceKnit*txtCommentsKnit*cboCommentsAcceptanceDye*txtCommentsDye*cboCommentsAcceptanceAuthor*txtCommentsAuthor*approved_status',"../../../")+data_all+'&total_row_knit='+total_row_knit+'&total_row_dye='+total_row_dye+'&update_id='+update_id;
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","yarn_test_controller.php",true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_test_entry_response;
		}

		function fnc_yarn_test_entry_response()
		{
			if (http.readyState == 4)
			{
				//alert(http.responseText);return;
				var response = trim(http.responseText).split('**');
				show_msg(trim(response[0]));

				if(response[0] == 0 || response[0] == 1)
				{
					var update_id= response[1];
					//document.getElementById('update_id').value=update_id;
					var val='<? echo $prod_id."*".$lot_number; ?>'+'*'+update_id;
					show_list_view(val,'create_yarn_test_popup_list_view','list_container','yarn_test_controller','setFilterGrid("list_view",-1)');
				}

				if (response[0] == 0)
				{
					$('#msg_box_popp').fadeTo(100,1,function()
				 	{
						$('#msg_box_popp').html("Data is Save Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(15000);
					});
					reset_form('LotNoPopUpFrm_1','','','','');
					set_button_status(1, permission, 'fnc_yarn_test_entry', 1);
				}

				if (response[0] == 1)
				{
					$('#msg_box_popp').fadeTo(100,1,function()
				 	{
						$('#msg_box_popp').html("Data is Update Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(15000);
				 	});
				 	$('#cboColor').removeAttr('disabled','disabled');
				 	reset_form('LotNoPopUpFrm_1','','','','');
					set_button_status(0, permission, 'fnc_yarn_test_entry', 1);
				}

				if(response[0]==2)
				{
					$('#msg_box_popp').fadeTo(100,1,function()
				 	{
						$('#msg_box_popp').html("Data is Deleted Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(15000);
					});
					reset_form('LotNoPopUpFrm_1','list_container','','','','');
				}

				if (response[0] == 10)
				{
					$('#msg_box_popp').fadeTo(100,1,function()
				 	{
						$('#msg_box_popp').html("Invalid Operation").removeClass('messagebox').addClass('messagebox_error').fadeOut(15000);
				 	});
				}

				if (response[0] == 11)
				{
					$('#msg_box_popp').fadeTo(100,1,function()
				 	{
						$('#msg_box_popp').html("Duplicate Data Found, Please Check.").removeClass('messagebox').addClass('messagebox_error').fadeOut(15000);
				 	});
				}

				if (response[0] == 12)
				{
					var approve_status = response[1];
					if(approve_status==1)
					{
						var apprv_msg = " approved ";
					}
					else
					{
						var apprv_msg = " Partially Approved ";
					}

					$('#msg_box_popp').fadeTo(100,1,function()
				 	{
						$('#msg_box_popp').html("This lot testing is"+apprv_msg+",Can not update/Delete.").removeClass('messagebox').addClass('messagebox_error').fadeOut(15000);
				 	});
				}
				release_freezing();
			}
		}

		function fnResetForm()
		{
			parent.emailwindow.hide();
		}


		function ipi_value()
		{

	        var thin_yarn = parseFloat(document.getElementById('txtThinYarnReq').value*1);
	        var thin_yarn_phy = parseFloat(document.getElementById('txtThinYarnPhys').value*1);
	        //console.log(thin_yarn);
	        var thick = parseFloat(document.getElementById('txtThickReq').value*1);
	        var thick_phy = parseFloat(document.getElementById('txtThickPhys').value*1);
	         //console.log(thick);
	        var u_par = parseFloat(document.getElementById('txtUReq').value*1);
	        var u_par_phy = parseFloat(document.getElementById('txtUPhys').value*1);
	       //console.log(u_par);
	        var cv_par = parseFloat(document.getElementById('txtCvReq').value*1);
	        var cv_par_phy = parseFloat(document.getElementById('txtCvPhys').value*1);
	        //console.log(cv_par);
	        var neps_per_km = parseFloat(document.getElementById('txtNepsPerKmReq').value*1);
	        var neps_per_km_phy = parseFloat(document.getElementById('txtNepsPerKmPhys').value*1);
	        //console.log(neps_per_km);

        	document.getElementById('txtIpiValueReq').value = thin_yarn+thick+neps_per_km;//thin_yarn+thick+u_par+cv_par+neps_per_km;
        	document.getElementById('txtIpiValuePhys').value = thin_yarn_phy+thick_phy+u_par_phy+cv_par_phy+neps_per_km_phy;
    	}


    	function fnc_print()
		{
			var company_id=document.getElementById('cbo_company_id').value;
			var productID=document.getElementById('prod_id').value;

			var data=company_id + '*' + productID;
			var action='yarn_test_report2';
			window.open("../../reports/yarn/requires/daily_yarn_stock_report_controller.php?data=" + data + '&action=' + action, true);
		}

		function confirm_ready_to_app(str)
		{
			var update_id    = document.getElementById('update_id').value;
			if(update_id==''){alert("Please Save first.");return;}
			if(confirm("Ready to approve statuse change confirm?")==true){
				freeze_window(3);
				var res=return_global_ajax_value(str+'_'+update_id, 'set_ready_to_app', '', 'yarn_test_controller');
				var resArr=res.split('**');
				if(resArr[0]==1){
					show_list_view('<? echo $prod_id."*".$lot_number;?>','create_yarn_test_popup_list_view','list_container','yarn_test_controller','setFilterGrid("list_view",-1)');
					release_freezing();
				}
			}
		}

		function file_upload_info ( url, mst_id, det_id,  form, file_type, is_multi )
		{
			if (mst_id=="" || mst_id==0 )
			{
				alert('Please Select or Save any Information before File Upload.');return;
			}
			else
			{
				//alert(mst_id);return;
				file_uploader ( url, mst_id, det_id,  form, file_type, is_multi)
			}
		}

	</script>
	</head>
	<body onLoad="set_hotkey()">
		<div id="msg_box_popp" style="height:15px; width:400px; position:relative; left:250px; text-align: center;"></div>
		<? echo load_freeze_divs("../../../", $permission, 1); ?>
		<div style="width:750px; max-height:430px;  overflow-y:scroll;">
			<form name="LotNoPopUpFrm_1"  id="LotNoPopUpFrm_1" autocomplete="off">
			<fieldset style="width:250px;float:left; margin-left: 10px;">
				<legend>Basic Information</legend>
				<table width="100%" align="center" class="rpt_table" rules="all" cellspacing="0" cellpadding="0" border="0"  style="float:left">
		        	<tr>
						<td width="150" class="must_entry_caption">Test Date</td>
		                <td><input type="text" name="txtTestDate" id="txtTestDate" value="" class="datepicker" style="width:100px" placeholder="Date" /></td>
					</tr>
		            <tr>
						<td width="150" class="must_entry_caption">Test For</td>
						<td>
		                <?
							echo create_drop_down( "cboTestFor", 112, $yarn_test_for_arr,"",1, "-- Select Test --",$selected,"",'','','','','','','','' );
						?>
		                </td>
					</tr>
					<tr>
						<td width="150">Specimen Wgt</td>
						<td><input type="text" name="txtSpecimentWgt" id="txtSpecimentWgt" value="" class="text_boxes_numeric" style="width:100px" /></td>
					</tr>
					<tr>
						<td width="150">Speciment Length</td>
						<td><input type="text" name="txtSpecimentLenght" id="txtSpecimentLenght" value="" class="text_boxes_numeric" style="width:100px" /></td>
					</tr>
					<tr>
						<td width="150" class="must_entry_caption">Color Range</td>
						<td><? echo create_drop_down( "cboColor", 111,$color_range,"", 1,"--Select--", 0, "",0 );	?></td>
					</tr>
					<tr>
						<td width="150">Receive Qty</td>
						<td><input type="text" name="txtReceiveQty" id="txtReceiveQty" value="" class="text_boxes_numeric" style="width:100px"/></td>
					</tr>
					<tr>
						<td width="150">LC Number</td>
						<td><input type="text" name="txtLcNumber" id="txtLcNumber" value="" class="text_boxes" style="width:100px"/></td>
					</tr>
					<tr>
						<td width="150">LC Qty</td>
						<td><input type="text" name="txtLcQty" id="txtLcQty" value="" class="text_boxes_numeric" style="width:100px"/></td>
					</tr>
					<tr>
					<td >File</td>
					<td> <input type="button" class="image_uploader" style="width:112px" value="Click To Add File" onClick="file_upload_info ( '../../../', document.getElementById('update_id').value,'', 'yarn_test', 2 ,1)"> </td>
					</tr>
				</table>
			</fieldset>
	        <table width=""style="float:left">
	            <td width="75">
	                &nbsp;<br>
	                <input type="hidden" name="cbo_company_id" id="cbo_company_id" value="<? echo $cbo_company_id;?>" class="text_boxes_numeric" style="width:50px"/> <br>
	                <input type="hidden" name="prod_id" id="prod_id" value="<? echo $prod_id;?>" class="text_boxes" style="width:50px"/><br>
	                <input type="hidden" name="lot_number" id="lot_number" value="<? echo $lot_number; ?>"  class="text_boxes" style="width:50px"/><br>
	            </td>
	        </table>
		    <fieldset style="width:350px; margin-right: 10px; float: right;" >
		        <legend>Numerical Test</legend>
		        <table width="100%" align="center" class="rpt_table" rules="all" cellspacing="0" cellpadding="0" border="0"  style="float:left">
		        	<tr>
		                <th width="190"></th>
		                <th width="80">Require</th>
		                <th width="80">Physical</th>
		            </tr>
		            <tr>
		                <td width="190">Actual Yarn Count</td>
		                <td><input type="text" name="txtActualYarnCountReq" id="txtActualYarnCountReq" value="" class="text_boxes_numeric" style="width:80px" /></td>
		                <td><input type="text" name="txtActualYarnCountPhys" id="txtActualYarnCountPhys" value="" class="text_boxes_numeric" style="width:80px"/></td>
		            </tr>
		            <tr>
		                <td width="190">Yarn Appearance (Grade)</td>
		                <td><input type="text" name="txtYarnAppearanceReq" id="txtYarnAppearanceReq" value="" class="text_boxes" style="width:80px"/></td>
		                <td><input type="text" name="txtYarnAppearancePhys" id="txtYarnAppearancePhys" value="" class="text_boxes" style="width:80px"/></td>
		            </tr>
		            <tr>
		                <td width="190">Twist Per Inch (TPI)</td>
		                <td><input type="text" name="txtTwistPerInchReq" id="txtTwistPerInchReq" value="" class="text_boxes_numeric" style="width:80px"/></td>
		                <td><input type="text" name="txtTwistPerInchPhys" id="txtTwistPerInchPhys" value="" class="text_boxes_numeric" style="width:80px"/></td>
		            </tr>
		            <tr>
		                <td width="190">Moisture Content</td>
		                <td><input type="text" name="txtMoistureContentReq" id="txtMoistureContentReq" value="" class="text_boxes_numeric" style="width:80px"/></td>
		                <td><input type="text" name="txtMoistureContentPhys" id="txtMoistureContentPhys" value="" class="text_boxes_numeric" style="width:80px"/></td>
		            </tr>
		            <tr>
		                <td width="190">IPI Value (Uster)</td>
		                <td><input type="text" name="txtIpiValueReq" id="txtIpiValueReq" value="" class="text_boxes_numeric" style="width:80px" disabled="disabled" /></td>
		                <td><input type="text" name="txtIpiValuePhys" id="txtIpiValuePhys" value="" class="text_boxes_numeric" style="width:80px" disabled="disabled" /></td>
		            </tr>
		            <tr>
		                <td width="190">CSP Minimum</td>
		                <td><input type="text" name="txtCspMinimumReq" id="txtCspMinimumReq" value="" class="text_boxes_numeric" style="width:80px"/></td>
		                <td><input type="text" name="txtCspMinimumPhys" id="txtCspMinimumPhys" value="" class="text_boxes_numeric" style="width:80px"/></td>
		            </tr>
		            <tr>
		                <td width="190">CSP Actual</td>
		                <td><input type="text" name="txtCspActualReq" id="txtCspActualReq" value="" class="text_boxes_numeric" style="width:80px"/></td>
		                <td><input type="text" name="txtCspActualPhys" id="txtCspActualPhys" value="" class="text_boxes_numeric" style="width:80px"/></td>
		            </tr>
		            <tr>
		                <td width="190">Thin Yarn</td>
		                <td><input type="text" name="txtThinYarnReq" id="txtThinYarnReq" value="" class="text_boxes_numeric" onKeyUp="ipi_value();" style="width:80px"/></td>
		                <td><input type="text" name="txtThinYarnPhys" id="txtThinYarnPhys" value="" class="text_boxes_numeric" onKeyUp="ipi_value();" style="width:80px"/></td>
		            </tr>
		            <tr>
		                <td width="190">Thick</td>
		                <td><input type="text" name="txtThickReq" id="txtThickReq" value="" class="text_boxes_numeric" onKeyUp="ipi_value();" style="width:80px"/></td>
		                <td><input type="text" name="txtThickPhys" id="txtThickPhys" value="" class="text_boxes_numeric" onKeyUp="ipi_value();" style="width:80px"/></td>
		            </tr>
		            <tr>
		                <td width="190">U %</td>
		                <td><input type="text" name="txtUReq" id="txtUReq" value="" class="text_boxes_numeric" onKeyUp="ipi_value();" style="width:80px"/></td>
		                <td><input type="text" name="txtUPhys" id="txtUPhys" value="" class="text_boxes_numeric"  onkeyup="ipi_value();" style="width:80px"/></td>
		            </tr>
		            <tr>
		                <td width="190">CV %</td>
		                <td><input type="text" name="txtCvReq" id="txtCvReq" value="" class="text_boxes_numeric" onKeyUp="ipi_value();" style="width:80px"/></td>
		                <td><input type="text" name="txtCvPhys" id="txtCvPhys" value="" class="text_boxes_numeric" onKeyUp="ipi_value();" style="width:80px"/></td>
		            </tr>
		            <tr>
		                <td width="190">Neps Per KM</td>
		                <td><input type="text" name="txtNepsPerKmReq" id="txtNepsPerKmReq" value="" class="text_boxes_numeric" onKeyUp="ipi_value();" style="width:80px"/></td>
		                <td><input type="text" name="txtNepsPerKmPhys" id="txtNepsPerKmPhys" value="" class="text_boxes_numeric" onKeyUp="ipi_value();" style="width:80px"/></td>
		            </tr>
		            <tr>
		                <td width="190">Heariness %</td>
		                <td><input type="text" name="txtHearinessReq" id="txtHearinessReq" value="" class="text_boxes_numeric" style="width:80px"/></td>
		                <td><input type="text" name="txtHearinessPhys" id="txtHearinessPhys" value="" class="text_boxes_numeric" style="width:80px"/></td>
		            </tr>
		            <tr>
		                <td width="190">Counts CV %</td>
		                <td><input type="text" name="txtCountsCvReq" id="txtCountsCvReq" value="" class="text_boxes_numeric" style="width:80px"/></td>
		                <td><input type="text" name="txtCountsCvPhys" id="txtCountsCvPhys" value="" class="text_boxes_numeric" style="width:80px"/></td>
		            </tr>
		            <tr>
		                <td width="190">Actual Yarn Composition</td>
		                <td><input type="text" name="txtYarnComReq" id="txtYarnComReq" value="" class="text_boxes" style="width:80px"/></td>
		                <td><input type="text" name="txtYarnComPhys" id="txtYarnComPhys" value="" class="text_boxes" style="width:80px"/></td>
		            </tr>
		            <tr>
		                <td width="190">Pilling Test</td>
		                <td><input type="text" name="txtPilReq" id="txtPilReq" value="" class="text_boxes" style="width:80px"/></td>
		                <td><input type="text" name="txtPilPhys" id="txtPilPhys" value="" class="text_boxes" style="width:80px"/></td>
		            </tr>

		            <tr>
		                <td width="190">Brusting Test</td>
		                <td><input type="text" name="txtBrusReq" id="txtBrusReq" value="" class="text_boxes" style="width:80px"/></td>
		                <td><input type="text" name="txtBrusPhys" id="txtBrusPhys" value="" class="text_boxes" style="width:80px"/></td>
		            </tr>
		            <tr>
		                <td width="190">System Result</td>
		                <td colspan="2"><input type="text" name="txtSystemResult" id="txtSystemResult" value="" class="text_boxes" style="width:175px" placeholder="Pass/Fail" /></td>
		            </tr>
		        </table>
		    </fieldset>
		    <fieldset style="width:700px; float:left; margin:20px 10px 0px 10px;">
		        <legend>Physical Test for Knitting</legend>
		        <table id="tbl_kniting_test" width="600" style="margin: 10px 0px 0px 40px;" class="rpt_table" rules="all" cellspacing="0" cellpadding="0" border="0">
		            <tr>
		                <td width="100">Grey GSM</td>
		                <td><input type="text" name="txtGreyGsmKnit" id="txtGreyGsmKnit" value="" class="text_boxes_numeric" style="width:100px"/></td>
		                <td width="100">Grey Wash GSM</td>
		                <td><input type="text" name="txtGreyGashGsmKnit" id="txtGreyGashGsmKnit" value="" class="text_boxes_numeric" style="width:100px"/></td>
		                <td width="100">Required GSM</td>
		                <td><input type="text" name="txtRequiredGsmKnit" id="txtRequiredGsmKnit" value="" class="text_boxes_numeric" style="width:100px"/></td>
		            </tr>
		            <tr>
		                <td width="100">Required Dia</td>
		                <td><input type="text" name="txtRequiredDiaKnit" id="txtRequiredDiaKnit" value="" class="text_boxes_numeric" style="width:100px"/></td>
		                <td width="100">Machine Dia</td>
		                <td><input type="text" name="txtMachineDiaKnit" id="txtMachineDiaKnit" value="" class="text_boxes_numeric" style="width:100px"/></td>
		                <td width="100">Stich Length</td>
		                <td><input type="text" name="txtStichLengthKnit" id="txtStichLengthKnit" value="" class="text_boxes_numeric" style="width:100px"/></td>
		            </tr>
		        </table>
				<br>
	            <table id="knitingDataContainer" width="100%" align="center" class="rpt_table" rules="all" cellspacing="0" cellpadding="0" border="0">
	            	<thead>
	            		<tr>
							<th width="50">SL</th>
							<th width="200">Testing Parameters</th>
							<th width="80">Point</th>
							<th width="80">Result</th>
							<th width="80">Acceptance</th>
							<th width="100">Fabric Class</th>
							<th width="100">Remarks</th>
						</tr>
					</thead>
	            	<tbody>
	            		<?
	            		$i=1;
	            		foreach ($phys_test_knitting_arr as $key => $knit_value)
	            		{
		            		?>
		            		<tr>
								<td width="50"><? echo $i; ?></td>
								<td width="200"><? echo $knit_value; ?>
									<input type="hidden" id="testingParamKnit_<? echo $key; ?>" value="<? echo $key; ?>">
								</td>
								<td width="80">
									<input type="text" name="txtPointKnit_<? echo $key; ?>" id="txtPointKnit_<? echo $key; ?>" value="" class="text_boxes_numeric" style="width:80px"/>
									<input type="hidden" name="updatedtlsidknit_<? echo $key; ?>" id="updatedtlsidknit_<? echo $key; ?>" value=""/>
								</td>
								<td width="80">
									<?
									echo create_drop_down( "cboResultKnit_$key", 80, $yarn_test_result_arr,"",1, "--Select--",$selected );
									?>
								</td>
								<td width="80">
									<?
									echo create_drop_down( "cboAcceptanceKnit_$key", 80, $yarn_test_acceptance_arr,"",1, "--Select--",$selected );
									?>
								</td>
								<td width="100"><input type="text" name="txtFabricClassKnit_<? echo $key; ?>" id="txtFabricClassKnit_<? echo $key; ?>" value="" class="text_boxes" style="width:100px"/></td>
								<td width="100"><input type="text" name="txtRemarksKnit_<? echo $key; ?>" id="txtRemarksKnit_<? echo $key; ?>" value="" class="text_boxes" style="width:100px"/></td>
							</tr>
							<?
							$i++;
						}
						?>
					</tbody>
	            </table>
		    </fieldset>
		    <fieldset style="width:700px;float:left;margin:20px 10px 0px 10px">
		        <legend>Physical Test for Dyeing And Finishing</legend>
		        <table id="tbl_dying_test" width="600" style="margin: 10px 0px 0px 40px;" class="rpt_table" rules="all" cellspacing="0" cellpadding="0" border="0">
		            <tr>
		                <td width="100">Grey GSM</td>
		                <td><input type="text" name="txtGreyGsmDye" id="txtGreyGsmDye" value="" class="text_boxes_numeric" style="width:100px"/></td>
		                <td width="100">Batch</td>
		                <td><input type="text" name="txtBatchDye" id="txtBatchDye" value="" class="text_boxes" style="width:100px"/></td>
		                <td width="100">Finish GSM</td>
		                <td><input type="text" name="txtFinishGsmDye" id="txtFinishGsmDye" value="" class="text_boxes_numeric" style="width:100px"/></td>
		            </tr>
		            <tr>
		                <td width="100">Finish Dia</td>
		                <td><input type="text" name="txtFinishDiaDye" id="txtFinishDiaDye" value="" class="text_boxes_numeric" style="width:100px"/></td>
		                <td width="100">Length %</td>
		                <td><input type="text" name="txtLengthDye" id="txtLengthDye" value="" class="text_boxes_numeric" style="width:100px"/></td>
		                <td width="100">Width %</td>
		                <td><input type="text" name="txtWidthDye" id="txtWidthDye" value="" class="text_boxes_numeric" style="width:100px"/></td>
		            </tr>
		        </table>
				<br>
		        <table id="dyingDataContainer" width="100%" align="center" class="rpt_table" rules="all" cellspacing="0" cellpadding="0" border="0">
	            	<thead>
	            		<tr>
							<th width="50">SL</th>
							<th width="200">Testing Parameters</th>
							<th width="50">Point</th>
							<th width="100">Result</th>
							<th width="100">Acceptance</th>
							<th width="100">Fabric Class</th>
							<th width="100">Remarks</th>
						</tr>
					</thead>
	            	<tbody>
	            		<?
	            		$j=1;
	            		foreach ($phys_test_dyeing_and_finishing_arr as $key => $dye_value)
	            		{
		            		?>
		            		<tr>
								<td width="50"><? echo $j; ?></td>
								<td width="200"><? echo $dye_value; ?>
									<input type="hidden" id="testingParamDye_<? echo $key; ?>" value="<? echo $key; ?>">
								</td>
								<td width="50">
									<input type="text" name="txtPointDye_<? echo $key; ?>" id="txtPointDye_<? echo $key; ?>" value="" class="text_boxes_numeric" style="width:100px"/>
									<input type="hidden" name="updatedtlsiddye_<? echo $key; ?>" id="updatedtlsiddye_<? echo $key; ?>" value=""/>
								</td>
								<td width="100">
									<?
									echo create_drop_down( "cboResultDye_$key", 100, $yarn_test_result_arr,"",1, "--Select--",$selected );
									?>
								</td>
								<td width="100">
									<?
									echo create_drop_down( "cboAcceptanceDye_$key", 100, $yarn_test_acceptance_arr,"",1, "--Select--",$selected );
									?>
								</td>
								<td width="100"><input type="text" name="txtFabricClassDye_<? echo $key; ?>" id="txtFabricClassDye_<? echo $key; ?>" value="" class="text_boxes" style="width:100px"/></td>
								<td width="100"><input type="text" name="txtRemarksDye_<? echo $key; ?>" id="txtRemarksDye_<? echo $key; ?>" value="" class="text_boxes" style="width:100px"/></td>
							</tr>
							<?
							$j++;
						}
						?>
					</tbody>
	            </table>
		    </fieldset>
		    <fieldset style="width:700px;float:left;margin:20px 10px 0px 10px">
		        <table id="commentsDataContainer" width="100%" align="center" class="rpt_table" rules="all" cellspacing="0" cellpadding="0" border="0">
		            <tr>
		                <td width="200">Comentes For Knitting Dept.</td>
						<td width="100">
							<?
							echo create_drop_down( "cboCommentsAcceptanceKnit", 100, $comments_acceptance_arr,"",1, "--Select--",$selected );
							?>
						</td>
						<td><input type="text" name="txtCommentsKnit" id="txtCommentsKnit" value="" class="text_boxes" style="width:400px"/></td>
		            </tr>
		            <tr>
		                <td width="200">Comentes For Dyeing/Finishing Dept.</td>
						<td width="100">
							<?
							echo create_drop_down( "cboCommentsAcceptanceDye", 100, $comments_acceptance_arr,"",1, "--Select--",$selected );
							?>
						</td>
						<td><input type="text" name="txtCommentsDye" id="txtCommentsDye" value="" class="text_boxes" style="width:400px"/></td>
		            </tr>
		            <tr>
		                <td width="200">Comentes For Authorize Dept.</td>
						<td width="100">
							<?
							echo create_drop_down( "cboCommentsAcceptanceAuthor", 100, $comments_acceptance_arr,"",1, "--Select--",$selected );
							?>
						</td>
						<td><input type="text" name="txtCommentsAuthor" id="txtCommentsAuthor" value="" class="text_boxes" style="width:400px"/></td>
		            </tr>
                    <tr>
                    	<td>Ready To Approved</td>
                        <td><? echo create_drop_down( "cbo_ready_to_approved", 100, $yes_no,"", 1, "-- Select--", 2, "confirm_ready_to_app(this.value)","","" ); ?></td>
                    </tr>
		        </table>
		    </fieldset>
			<table cellpadding="0" cellspacing="1" width="700">
				<tr>
				   <td colspan="8" align="center"></td>
				</tr>
				<tr>
					<td align="center" colspan="6" valign="middle" class="button_container">
						<input type="hidden" id="update_id" name="update_id" value="" />
						<input type="hidden" id="approved_status" name="approved_status" value="" />
						<? echo load_submit_buttons( $permission, "fnc_yarn_test_entry", 0,0,"fnResetForm()",1);?>

					<input type="button" align="center" value="Print" id="btn_consignment" name="btn_consignment" class="formbutton" style="width:100px;" onClick="fnc_print()" />
					</td>
			   </tr>
			</table>
			</form>
			<fieldset style="width:700px;margin:0px 10px 0px 10px;">
				<div id="list_container"></div>
			</fieldset>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script type="text/javascript">show_list_view('<? echo $prod_id."*".$lot_number;?>','create_yarn_test_popup_list_view','list_container','yarn_test_controller','setFilterGrid("list_view",-1)');</script>
	</html>
	<?
	exit();
}

if ($action=="SaveUpdateDeleteTestData")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$approved_status = str_replace("'",'',$approved_status);

	if($operation==1 || $operation==2)
	{
		if($approved_status==1 || $approved_status==3)
		{
			echo "12**$approved_status";
			disconnect($con);exit();
		}
	}

	if($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if(is_duplicate_field( "id", " inv_yarn_test_mst", "prod_id=$prod_id and color=$cboColor and status_active=1 and is_deleted=0" ) == 1)
		{
			echo "11";
			disconnect($con);exit();
		}

		$id = return_next_id("id", "inv_yarn_test_mst", 1);
		$field_array= "id, company_id, prod_id, lot_number, test_date, test_for, specimen_wgt, specimen_length, color, receive_qty, lc_number, lc_qty, actual_yarn_count, actual_yarn_count_phy, yarn_apperance_grad, yarn_apperance_phy, actual_yarn_comp, actual_yarn_comp_phy, pilling, pilling_phy, brusting, brusting_phy, twist_per_inc, twist_per_inc_phy, moisture_content, moisture_content_phy, ipi_value, ipi_value_phy, csp_minimum, csp_minimum_phy, csp_actual, csp_actual_phy, thin_yarn, thin_yarn_phy, thick, thick_phy, u, u_phy, cv, cv_phy, neps_per_km, neps_per_km_phy, heariness, heariness_phy, counts_cv, counts_cv_phy, system_result, grey_gsm, grey_wash_gsm, required_gsm, required_dia, machine_dia, stich_length, grey_gsm_dye, batch, finish_gsm, finish_dia, length, width, inserted_by, insert_date, status_active, is_deleted";

		$data_array="(".$id.",".$cbo_company_id.",".$prod_id.",".$lot_number.",".$txtTestDate.",".$cboTestFor.",".$txtSpecimentWgt.",".$txtSpecimentLenght.",".$cboColor.",".$txtReceiveQty.",".$txtLcNumber.",".$txtLcQty.",".$txtActualYarnCountReq.",".$txtActualYarnCountPhys.",".$txtYarnAppearanceReq.",".$txtYarnAppearancePhys.",".$txtYarnComReq.",".$txtYarnComPhys.",".$txtPilReq.",".$txtPilPhys.",".$txtBrusReq.",".$txtBrusPhys.",".$txtTwistPerInchReq.",".$txtTwistPerInchPhys.",".$txtMoistureContentReq.",".$txtMoistureContentPhys.",".$txtIpiValueReq.",".$txtIpiValuePhys.",".$txtCspMinimumReq.",".$txtCspMinimumPhys.",".$txtCspActualReq.",".$txtCspActualPhys.",".$txtThinYarnReq.",".$txtThinYarnPhys.",".$txtThickReq.",".$txtThickPhys.",".$txtUReq.",".$txtUPhys.",".$txtCvReq.",".$txtCvPhys.",".$txtNepsPerKmReq.",".$txtNepsPerKmPhys.",".$txtHearinessReq.",".$txtHearinessPhys.",".$txtCountsCvReq.",".$txtCountsCvPhys.",".$txtSystemResult.",".$txtGreyGsmKnit.",".$txtGreyGashGsmKnit.",".$txtRequiredGsmKnit.",".$txtRequiredDiaKnit.",".$txtMachineDiaKnit.",".$txtStichLengthKnit.",".$txtGreyGsmDye.",".$txtBatchDye.",".$txtFinishGsmDye.",".$txtFinishDiaDye.",".$txtLengthDye.",".$txtWidthDye.",".$user_id.",'".$pc_date_time."','1',0)";

		$rID = sql_insert("inv_yarn_test_mst",$field_array,$data_array,0);
		//echo "10**insert into inv_yarn_test_mst ($field_array) values $data_array";die;
		$data_array_dtls='';
		$id_dtls = return_next_id("id", "inv_yarn_test_dtls", 1);
		$field_array_dtls= "id, mst_id, testing_parameters_id, fab_type, testing_parameters, fabric_point, result, acceptance, fabric_class, remarks, inserted_by, insert_date, status_active, is_deleted";
		$i=1;
		$part=0;
		foreach ($phys_test_knitting_arr as $key=>$val)
	    {
			$testing_param_knit = "testingParamKnit_".$key;
			$point_knit = "txtPointKnit_".$key;
			$cbo_result_knit = "cboResultKnit_".$key;
			$cbo_acceptance_knit = "cboAcceptanceKnit_".$key;
			$fabric_class_knit = "txtFabricClassKnit_".$key;
			$remarks_knit = "txtRemarksKnit_".$key;

			if(str_replace("'","",$$point_knit ) !='' || str_replace("'","",$$cbo_result_knit) !=0 || str_replace("'","",$$cbo_acceptance_knit) !=0 || str_replace("'","",$$fabric_class_knit) !='' || str_replace("'","",$$remarks_knit) !='')
			{
				$part = 1;
				if ($i != 1) $data_array_dtls .=",";
				$data_array_dtls .="(".$id_dtls.",".$id.",".$key.",'1',".$$testing_param_knit.",".$$point_knit.",".$$cbo_result_knit.",".$$cbo_acceptance_knit.",".$$fabric_class_knit.",".$$remarks_knit.",".$user_id.",'".$pc_date_time."','1',0)";
				$id_dtls = $id_dtls+1;
			}
			$i++;
		}
        //echo "10**insert into inv_yarn_test_dtls ($field_array_dtls) values $data_array_dtls";die;
		if ($part != 1)
		{
			$data_array_dtls = '';
		}

		$j=1;
		foreach ($phys_test_dyeing_and_finishing_arr as $key => $val)
	    {
			$testing_param_dye = "testingParamDye_".$key;
			$point_dye = "txtPointDye_".$key;
			$cbo_result_dye = "cboResultDye_".$key;
			$cbo_acceptance_dye = "cboAcceptanceDye_".$key;
			$fabric_class_dye = "txtFabricClassDye_".$key;
			$remarks_dye = "txtRemarksDye_".$key;

			if(str_replace("'","",$$point_dye ) != '' || str_replace("'","",$$cbo_result_dye) != 0 || str_replace("'","",$$cbo_acceptance_dye) != 0 || str_replace("'","",$$fabric_class_dye) != '' || str_replace("'","",$$remarks_dye) != '')
			{

				if ($part == 1 && $j == 1)
				{
					$data_array_dtls .=",";
				}

				if ($j != 1) $data_array_dtls .=",";
				$data_array_dtls .="(".$id_dtls.",".$id.",".$key.",'2',".$$testing_param_dye.",".$$point_dye.",".$$cbo_result_dye.",".$$cbo_acceptance_dye.",".$$fabric_class_dye.",".$$remarks_dye.",".$user_id.",'".$pc_date_time."','1',0)";
				$id_dtls = $id_dtls+1;
			}
			$j++;
		}

		$rID2 = sql_insert("inv_yarn_test_dtls",$field_array_dtls,$data_array_dtls,0);
		//echo "10**insert into inv_yarn_test_dtls ($field_array_dtls) values $data_array_dtls";

		//details part entry is not mendatory so dtls_id is zero for inv_yarn_test_comments table
		$id_dtls = 0;
		$comments_id = return_next_id("id", "inv_yarn_test_comments", 1);
		$field_array_comments="id, mst_table_id, dtls_id, comments_knit_acceptance, comments_knit, comments_dye_acceptance, comments_dye, comments_author_acceptance, comments_author, inserted_by, insert_date, status_active, is_deleted";
		$data_array_comments="(".$comments_id.",".$id.",".$id_dtls.",".$cboCommentsAcceptanceKnit.",".$txtCommentsKnit.",".$cboCommentsAcceptanceDye.",".$txtCommentsDye.",".$cboCommentsAcceptanceAuthor.",".$txtCommentsAuthor.",".$user_id.",'".$pc_date_time."','1',0)";
		$rID3=sql_insert("inv_yarn_test_comments",$field_array_comments,$data_array_comments,0);
		//echo "10**insert into inv_yarn_test_comments ($field_array_comments) values $data_array_comments";die;

		$flag=0;
		if ($rID == 1) $flag=1; else $flag=0;

		if ($flag==1 && $rID2 == 1)	$flag=1;

		if ($flag==1 && $rID3 == 1)	$flag=1;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array_update="test_date*test_for*specimen_wgt*specimen_length*color*receive_qty*lc_number*lc_qty*actual_yarn_count*actual_yarn_count_phy*yarn_apperance_grad*yarn_apperance_phy*actual_yarn_comp*actual_yarn_comp_phy*pilling*pilling_phy*brusting*brusting_phy*twist_per_inc*twist_per_inc_phy*moisture_content*moisture_content_phy*ipi_value*ipi_value_phy*csp_minimum*csp_minimum_phy*csp_actual*csp_actual_phy*thin_yarn*thin_yarn_phy*thick*thick_phy*u*u_phy*cv*cv_phy*neps_per_km*neps_per_km_phy*heariness*heariness_phy*counts_cv*counts_cv_phy*system_result*grey_gsm*grey_wash_gsm*required_gsm*required_dia*machine_dia*stich_length*grey_gsm_dye*batch*finish_gsm*finish_dia*length*width*updated_by*update_date*status_active*is_deleted";


		$data_array_update="".$txtTestDate."*".$cboTestFor."*".$txtSpecimentWgt."*".$txtSpecimentLenght."*".$cboColor."*".$txtReceiveQty."*".$txtLcNumber."*".$txtLcQty."*".$txtActualYarnCountReq."*".$txtActualYarnCountPhys."*".$txtYarnAppearanceReq."*".$txtYarnAppearancePhys."*".$txtYarnComReq."*".$txtYarnComPhys."*".$txtPilReq."*".$txtPilPhys."*".$txtBrusReq."*".$txtBrusPhys."*".$txtTwistPerInchReq."*".$txtTwistPerInchPhys."*".$txtMoistureContentReq."*".$txtMoistureContentPhys."*".$txtIpiValueReq."*".$txtIpiValuePhys."*".$txtCspMinimumReq."*".$txtCspMinimumPhys."*".$txtCspActualReq."*".$txtCspActualPhys."*".$txtThinYarnReq."*".$txtThinYarnPhys."*".$txtThickReq."*".$txtThickPhys."*".$txtUReq."*".$txtUPhys."*".$txtCvReq."*".$txtCvPhys."*".$txtNepsPerKmReq."*".$txtNepsPerKmPhys."*".$txtHearinessReq."*".$txtHearinessPhys."*".$txtCountsCvReq."*".$txtCountsCvPhys."*".$txtSystemResult."*".$txtGreyGsmKnit."*".$txtGreyGashGsmKnit."*".$txtRequiredGsmKnit."*".$txtRequiredDiaKnit."*".$txtMachineDiaKnit."*".$txtStichLengthKnit."*".$txtGreyGsmDye."*".$txtBatchDye."*".$txtFinishGsmDye."*".$txtFinishDiaDye."*".$txtLengthDye."*".$txtWidthDye."*".$user_id."*'".$pc_date_time."'*1*0";

		$id_dtls = return_next_id("id", "inv_yarn_test_dtls", 1);

		$field_array_dtls= "id, mst_id, testing_parameters_id, fab_type, testing_parameters, fabric_point, result, acceptance, fabric_class, remarks, inserted_by, insert_date, status_active, is_deleted";
		$field_array_up_dtls= "testing_parameters*fabric_point*result*acceptance*fabric_class*remarks*updated_by*update_date*status_active*is_deleted";

		$update_id = str_replace("'",'',$update_id);

		$part = 0;
		$add_comma=0;
		$data_array_dtls='';

		foreach ($phys_test_knitting_arr as $key => $val)
	    {
	    	$testing_param_knit = "testingParamKnit_".$key;
			$point_knit = "txtPointKnit_".$key;
			$cbo_result_knit = "cboResultKnit_".$key;
			$cbo_acceptance_knit = "cboAcceptanceKnit_".$key;
			$fabric_class_knit = "txtFabricClassKnit_".$key;
			$remarks_knit = "txtRemarksKnit_".$key;
			$update_dtlsid_knit = "updatedtlsidknit_".$key;

			if(str_replace("'",'',$$update_dtlsid_knit)=="")
			{
				if(str_replace("'","",$$point_knit ) !='' || str_replace("'","",$$cbo_result_knit) !=0 || str_replace("'","",$$cbo_acceptance_knit) !=0 || str_replace("'","",$$fabric_class_knit) !='' || str_replace("'","",$$remarks_knit) !='')
				{
					$part = 1;
					if ($add_comma != 0) $data_array_dtls .=",";
					$data_array_dtls .="(".$id_dtls.",".$update_id.",".$key.",'1',".$$testing_param_knit.",".$$point_knit.",".$$cbo_result_knit.",".$$cbo_acceptance_knit.",".$$fabric_class_knit.",".$$remarks_knit.",".$user_id.",'".$pc_date_time."','1',0)";
					$id_dtls = $id_dtls+1;
					$add_comma++;
				}
			}
			else
			{
				if(str_replace("'","",$$point_knit ) !='' || str_replace("'","",$$cbo_result_knit) !=0 || str_replace("'","",$$cbo_acceptance_knit) !=0 || str_replace("'","",$$fabric_class_knit) !='' || str_replace("'","",$$remarks_knit) !='')
				{
					$deleteIdKnit_array[]=str_replace("'",'',$$update_dtlsid_knit);
					$updateIdKnit_array[]=str_replace("'",'',$$update_dtlsid_knit);
					$data_array_upKnit_dtls[str_replace("'",'',$$update_dtlsid_knit)]=explode("*",("".$$testing_param_knit."*".$$point_knit."*".$$cbo_result_knit."*".$$cbo_acceptance_knit."*".$$fabric_class_knit."*".$$remarks_knit."*".$user_id."*'".$pc_date_time."'*1*0"));
				}
			}
		}

		//echo "10**insert into inv_yarn_test_dtls ($field_array_dtls) values $data_array_dtls";die;

		if ($part != 1)
		{
			$data_array_dtls = '';
		}

		$add_comma=0;

		foreach ($phys_test_dyeing_and_finishing_arr as $key => $val)
	    {
			$testing_param_dye = "testingParamDye_".$key;
			$point_dye = "txtPointDye_".$key;
			$cbo_result_dye = "cboResultDye_".$key;
			$cbo_acceptance_dye = "cboAcceptanceDye_".$key;
			$fabric_class_dye = "txtFabricClassDye_".$key;
			$remarks_dye = "txtRemarksDye_".$key;
			$update_dtlsid_dye = "updatedtlsiddye_".$key;

			if(str_replace("'",'',$$update_dtlsid_dye)=="")
			{
				if(str_replace("'","",$$point_dye ) !='' || str_replace("'","",$$cbo_result_dye) !=0 || str_replace("'","",$$cbo_acceptance_dye) !=0 || str_replace("'","",$$fabric_class_dye) !='' || str_replace("'","",$$remarks_dye) !='')
				{
					if ($part == 1 && $add_comma == 0)
					{
						$data_array_dtls .=",";
					}
					if ($add_comma != 0) $data_array_dtls .=",";
					$data_array_dtls .="(".$id_dtls.",".$update_id.",".$key.",'2',".$$testing_param_dye.",".$$point_dye.",".$$cbo_result_dye.",".$$cbo_acceptance_dye.",".$$fabric_class_dye.",".$$remarks_dye.",".$user_id.",'".$pc_date_time."','1',0)";
					$id_dtls = $id_dtls+1;
					$add_comma++;
				}
			}
			else
			{
				if(str_replace("'","",$$point_dye ) !='' || str_replace("'","",$$cbo_result_dye) !=0 || str_replace("'","",$$cbo_acceptance_dye) !=0 || str_replace("'","",$$fabric_class_dye) !='' || str_replace("'","",$$remarks_dye) !='')
				{
					$deleteIdDye_array[]=str_replace("'",'',$$update_dtlsid_dye);
					$updateIdDye_array[]=str_replace("'",'',$$update_dtlsid_dye);
					$data_array_upDye_dtls[str_replace("'",'',$$update_dtlsid_dye)]=explode("*",("".$$testing_param_dye."*".$$point_dye."*".$$cbo_result_dye."*".$$cbo_acceptance_dye."*".$$fabric_class_dye."*".$$remarks_dye."*".$user_id."*'".$pc_date_time."'*1*0"));
				}
			}
		}

		// Delete Knit Part
		$dtlsUpdate_knitID_array = array();
		$sql_dtls_knit="select id from inv_yarn_test_dtls where mst_id=$update_id and fab_type=1 and status_active=1 and is_deleted=0";
		$nameArrayKnit=sql_select($sql_dtls_knit);

		foreach($nameArrayKnit as $row)
		{
			$dtlsUpdate_knitID_array[]=$row[csf('id')];
		}

		if(implode(',',$deleteIdKnit_array) != "")
		{
			$distance_delete_knit_id = array_diff($dtlsUpdate_knitID_array, $deleteIdKnit_array);
		}
		else
		{
			$distance_delete_knit_id = $dtlsUpdate_knitID_array;
		}

		$field_array_del="updated_by*update_date*status_active*is_deleted";
		$data_array_del="'".$user_id."'*'".$pc_date_time."'*0*1";
		if(implode(',',$distance_delete_knit_id) != "")
		{
			foreach($distance_delete_knit_id as $id_val)
			{
				$delKnitrID=sql_delete("inv_yarn_test_dtls",$field_array_del,$data_array_del,"id","$id_val",1);
			}
		}
		if ($delKnitrID) $flag=1;

		// Delete Dye Part
		$dtlsUpdate_DyeID_array = array();
		$sql_dtls_knit="select id from inv_yarn_test_dtls where mst_id=$update_id and fab_type=2 and status_active=1 and is_deleted=0";
		$nameArrayKnit=sql_select($sql_dtls_knit);

		foreach($nameArrayKnit as $row)
		{
			$dtlsUpdate_DyeID_array[]=$row[csf('id')];
		}

		if(implode(',',$deleteIdDye_array) != "")
		{
			$distance_delete_dye_id = array_diff($dtlsUpdate_DyeID_array, $deleteIdDye_array);
		}
		else
		{
			$distance_delete_dye_id = $dtlsUpdate_DyeID_array;
		}

		if(implode(',',$distance_delete_dye_id) != "")
		{
			foreach($distance_delete_dye_id as $id_val)
			{
				$delDyerID=sql_delete("inv_yarn_test_dtls",$field_array_del,$data_array_del,"id","$id_val",1);
			}
		}
		if ($delDyerID) $flag=1;


		$field_array_up_comments = "comments_knit_acceptance*comments_knit*comments_dye_acceptance*comments_dye*comments_author_acceptance*comments_author*updated_by*update_date*status_active*is_deleted";
		$data_array_up_comments = "".$cboCommentsAcceptanceKnit."*".$txtCommentsKnit."*".$cboCommentsAcceptanceDye."*".$txtCommentsDye."*".$cboCommentsAcceptanceAuthor."*".$txtCommentsAuthor."*".$user_id."*'".$pc_date_time."'*1*0";

		$rID=sql_update("inv_yarn_test_mst",$field_array_update,$data_array_update,"id",$update_id,0);
		if($rID) $flag=1; else $flag=0;


		$commentsrID=sql_update("inv_yarn_test_comments",$field_array_up_comments,$data_array_up_comments,"mst_table_id",$update_id,0);
		if($flag==1)
		{
			if($commentsrID) $flag=1; else $flag=0;
		}

		if($data_array_upKnit_dtls != "")
		{
			$dtlsrID_knit = execute_query(bulk_update_sql_statement("inv_yarn_test_dtls","id",$field_array_up_dtls,$data_array_upKnit_dtls,$updateIdKnit_array),1);
			if($flag==1)
			{
				if($dtlsrID_knit) $flag=1; else $flag=0;
			}
		}

		if($data_array_upDye_dtls != "")
		{
			$dtlsrID_dye = execute_query(bulk_update_sql_statement("inv_yarn_test_dtls","id",$field_array_up_dtls,$data_array_upDye_dtls,$updateIdDye_array),1);
			if($flag==1)
			{
				if($dtlsrID_dye) $flag=1; else $flag=0;
			}
		}

		if($data_array_dtls != "")
		{
			$dtlsrID = sql_insert("inv_yarn_test_dtls",$field_array_dtls,$data_array_dtls,1);
			if($flag==1)
			{
				if($dtlsrID) $flag=1; else $flag=0;
			}
		}

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".$update_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".$update_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";

		$rID=sql_delete("inv_yarn_test_mst",$field_array,$data_array,"id",$update_id,0);
		$dtlsrID=sql_delete("inv_yarn_test_dtls",$field_array,$data_array,"mst_id",$update_id,0);
		$commentsrID=sql_delete("inv_yarn_test_comments",$field_array,$data_array,"mst_table_id",$update_id,0);

		if($db_type==2)
		{
			if($rID && $dtlsrID && $commentsrID)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		if($db_type==0)
		{
			if($rID && $dtlsrID && $commentsrID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con);
		die;
	}

}

if ($action=="create_yarn_test_popup_list_view")
{
	list($prod_id, $lot, $upd_id) = explode("*", $data);
	$sql = "select id, test_for, lot_number, test_date, color,ready_to_approved,approved from inv_yarn_test_mst where prod_id=$prod_id and status_active=1 and is_deleted=0 order by id desc";
	$sql_result = sql_select($sql);
	?>
	<div style="width:700px;">
        <table cellspacing="0" width="700"  border="1" rules="all" class="rpt_table" align="left">
            <thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="150">Lot</th>
                    <th width="80">Test Date</th>
                    <th width="100">Test For</th>
                    <th width="100">Color</th>
                    <th width="100">Ready To App</th>
                    <th>Approved Status</th>
                </tr>
            </thead>
        </table>
        <div style="width:720px; max-height:720px; overflow-y:auto;">
            <table cellspacing="0" width="700"  border="1" rules="all" align="left" class="rpt_table" id="list_view" >
                <tbody>
                <?
                $i=1;
	            foreach($sql_result as $row)
				{
					if ($i%2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					?>
                    <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="set_data('<? echo $row[csf("id")]; ?>')">
                        <td width="50"><? echo $i; ?></td>
                        <td width="150"><? echo $row[csf('lot_number')]; ?></td>
                        <td width="80"><? echo change_date_format($row[csf("test_date")], "dd-mm-yyyy"); ?></td>
                        <td width="100"><? echo $yarn_test_for_arr[$row[csf('test_for')]]; ?></td>
                        <td width="100"><? echo $color_range[$row[csf('color')]]; ?></td>
                        <td align="100"><? echo $yes_no[$row[csf('ready_to_approved')]]; ?></td>
                        <td align="center"><?
                        	$approved = $row[csf('approved')];
                        	if($approved==1) $ap_msg="Approved";
							else if($approved==3) $ap_msg="Partially Approved";
							else $ap_msg="";
							echo $ap_msg;?>
                        </td>
					</tr>
					<?
					$i++;
				}
				?>
                <tbody>
            </table>
        </div>
    </div>
    <?
    exit();
}

if ($action = "populate_all_data_to_form")
{
	$sql = "SELECT a.ready_to_approved,a.approved,a.id, a.test_date, a.test_for, a.specimen_wgt, a.specimen_length, a.color, a.receive_qty, a.lc_number, a.lc_qty, a.actual_yarn_count, a.actual_yarn_count_phy, a.yarn_apperance_grad, a.yarn_apperance_phy, a.actual_yarn_comp, a.actual_yarn_comp_phy, a.pilling, a.pilling_phy, a.brusting, a.brusting_phy, a.twist_per_inc, a.twist_per_inc_phy, a.moisture_content, a.moisture_content_phy, a.ipi_value, a.ipi_value_phy, a.csp_minimum, a.csp_minimum_phy, a.csp_actual, a.csp_actual_phy, a.thin_yarn, a.thin_yarn_phy, a.thick, a.thick_phy, a.u, a.u_phy, a.cv, a.cv_phy, a.neps_per_km, a.neps_per_km_phy, a.heariness, a.heariness_phy, a.counts_cv, a.counts_cv_phy, a.system_result, a.grey_gsm, a.grey_wash_gsm, a.required_gsm, a.required_dia, a.machine_dia, a.stich_length, a.grey_gsm_dye, a.batch, a.finish_gsm, a.finish_dia, a.length, a.width, c.comments_knit_acceptance, c.comments_knit, c.comments_dye_acceptance, c.comments_dye, c.comments_author_acceptance, c.comments_author from inv_yarn_test_mst a, inv_yarn_test_comments c where a.id=c.mst_table_id and a.id=$data and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	$sql_result = sql_select($sql);
	foreach ($sql_result as $row)
    {
    	if ($row[csf("specimen_wgt")]==0) $specimen_wgt='';else $specimen_wgt=$row[csf("specimen_wgt")];
    	if ($row[csf("specimen_length")]==0) $specimen_length='';else $specimen_length=$row[csf("specimen_length")];
    	if ($row[csf("receive_qty")]==0) $receive_qty='';else $receive_qty=$row[csf("receive_qty")];
    	if ($row[csf("lc_qty")]==0) $lc_qty='';else $lc_qty=$row[csf("lc_qty")];
    	if ($row[csf("actual_yarn_count")]==0) $actual_yarn_count='';else $actual_yarn_count=$row[csf("actual_yarn_count")];
    	if ($row[csf("actual_yarn_count_phy")]==0) $actual_yarn_count_phy='';else $actual_yarn_count_phy=$row[csf("actual_yarn_count_phy")];
    	if ($row[csf("yarn_apperance_grad")]=='') $yarn_apperance_grad='';else $yarn_apperance_grad=$row[csf("yarn_apperance_grad")];
    	if ($row[csf("yarn_apperance_phy")]=='') $yarn_apperance_phy='';else $yarn_apperance_phy=$row[csf("yarn_apperance_phy")];

    	if ($row[csf("actual_yarn_comp")]=='') $actual_yarn_comp='';else $actual_yarn_comp=$row[csf("actual_yarn_comp")];
    	if ($row[csf("actual_yarn_comp_phy")]=='') $actual_yarn_comp_phy='';else $actual_yarn_comp_phy=$row[csf("actual_yarn_comp_phy")];

    	if ($row[csf("pilling")]=='') $pilling='';else $pilling=$row[csf("pilling")];
    	if ($row[csf("pilling_phy")]=='') $pilling_phy='';else $pilling_phy=$row[csf("pilling_phy")];
    	if ($row[csf("brusting")]=='') $brusting='';else $brusting=$row[csf("brusting")];
    	if ($row[csf("brusting_phy")]=='') $brusting_phy='';else $brusting_phy=$row[csf("brusting_phy")];

    	if ($row[csf("twist_per_inc")]==0) $twist_per_inc='';else $twist_per_inc=$row[csf("twist_per_inc")];
    	if ($row[csf("twist_per_inc_phy")]==0) $twist_per_inc_phy='';else $twist_per_inc_phy=$row[csf("twist_per_inc_phy")];
    	if ($row[csf("moisture_content")]==0) $moisture_content='';else $moisture_content=$row[csf("moisture_content")];
    	if ($row[csf("moisture_content_phy")]==0) $moisture_content_phy='';else $moisture_content_phy=$row[csf("moisture_content_phy")];
    	if ($row[csf("ipi_value")]==0) $ipi_value='';else $ipi_value=$row[csf("ipi_value")];
    	if ($row[csf("ipi_value_phy")]==0) $ipi_value_phy='';else $ipi_value_phy=$row[csf("ipi_value_phy")];
    	if ($row[csf("csp_minimum")]==0) $csp_minimum='';else $csp_minimum=$row[csf("csp_minimum")];
    	if ($row[csf("csp_minimum_phy")]==0) $csp_minimum_phy='';else $csp_minimum_phy=$row[csf("csp_minimum_phy")];
    	if ($row[csf("csp_actual")]==0) $csp_actual='';else $csp_actual=$row[csf("csp_actual")];
    	if ($row[csf("csp_actual_phy")]==0) $csp_actual_phy='';else $csp_actual_phy=$row[csf("csp_actual_phy")];
    	if ($row[csf("thin_yarn")]==0) $thin_yarn='';else $thin_yarn=$row[csf("thin_yarn")];
    	if ($row[csf("thin_yarn_phy")]==0) $thin_yarn_phy='';else $thin_yarn_phy=$row[csf("thin_yarn_phy")];
    	if ($row[csf("thick")]==0) $thick='';else $thick=$row[csf("thick")];
    	if ($row[csf("thick_phy")]==0) $thick_phy='';else $thick_phy=$row[csf("thick_phy")];
    	if ($row[csf("u")]==0) $u='';else $u=$row[csf("u")];
    	if ($row[csf("u_phy")]==0) $u_phy='';else $u_phy=$row[csf("u_phy")];
    	if ($row[csf("cv")]==0) $cv='';else $cv=$row[csf("cv")];
    	if ($row[csf("cv_phy")]==0) $cv_phy='';else $cv_phy=$row[csf("cv_phy")];
    	if ($row[csf("neps_per_km")]==0) $neps_per_km='';else $neps_per_km=$row[csf("neps_per_km")];
    	if ($row[csf("neps_per_km_phy")]==0) $neps_per_km_phy='';else $neps_per_km_phy=$row[csf("neps_per_km_phy")];
    	if ($row[csf("heariness")]==0) $heariness='';else $heariness=$row[csf("heariness")];
    	if ($row[csf("heariness_phy")]==0) $heariness_phy='';else $heariness_phy=$row[csf("heariness_phy")];
    	if ($row[csf("counts_cv")]==0) $counts_cv='';else $counts_cv=$row[csf("counts_cv")];
    	if ($row[csf("counts_cv_phy")]==0) $counts_cv_phy='';else $counts_cv_phy=$row[csf("counts_cv_phy")];
    	if ($row[csf("grey_gsm")]==0) $grey_gsm='';else $grey_gsm=$row[csf("grey_gsm")];
    	if ($row[csf("grey_wash_gsm")]==0) $grey_wash_gsm='';else $grey_wash_gsm=$row[csf("grey_wash_gsm")];
    	if ($row[csf("required_gsm")]==0) $required_gsm='';else $required_gsm=$row[csf("required_gsm")];
    	if ($row[csf("required_dia")]==0) $required_dia='';else $required_dia=$row[csf("required_dia")];
    	if ($row[csf("machine_dia")]==0) $machine_dia='';else $machine_dia=$row[csf("machine_dia")];
    	if ($row[csf("stich_length")]==0) $stich_length='';else $stich_length=$row[csf("stich_length")];
    	if ($row[csf("grey_gsm_dye")]==0) $grey_gsm_dye='';else $grey_gsm_dye=$row[csf("grey_gsm_dye")];
    	if ($row[csf("batch")]==0) $batch='';else $batch=$row[csf("batch")];
    	if ($row[csf("finish_gsm")]==0) $finish_gsm='';else $finish_gsm=$row[csf("finish_gsm")];
    	if ($row[csf("finish_dia")]==0) $finish_dia='';else $finish_dia=$row[csf("finish_dia")];
    	if ($row[csf("length")]==0) $length='';else $length=$row[csf("length")];
    	if ($row[csf("width")]==0) $width='';else $width=$row[csf("width")];

    	echo "document.getElementById('txtTestDate').value = '".change_date_format($row[csf("test_date")], "dd-mm-yyyy")."';\n";
    	echo "document.getElementById('cboTestFor').value = '".$row[csf("test_for")]."';\n";
    	echo "document.getElementById('txtSpecimentWgt').value = '".$specimen_wgt."';\n";
    	echo "document.getElementById('txtSpecimentLenght').value = '".$specimen_length."';\n";
    	echo "document.getElementById('cboColor').value = '".$row[csf("color")]."';\n";
    	echo "document.getElementById('txtReceiveQty').value = '".$receive_qty."';\n";
    	echo "document.getElementById('txtLcNumber').value = '".$row[csf("lc_number")]."';\n";
    	echo "document.getElementById('txtLcQty').value = '".$lc_qty."';\n";
    	echo "document.getElementById('txtActualYarnCountReq').value = '".$actual_yarn_count."';\n";
    	echo "document.getElementById('txtActualYarnCountPhys').value = '".$actual_yarn_count_phy."';\n";
    	echo "document.getElementById('txtYarnAppearanceReq').value = '".$yarn_apperance_grad."';\n";
    	echo "document.getElementById('txtYarnAppearancePhys').value = '".$yarn_apperance_phy."';\n";

    	echo "document.getElementById('txtYarnComReq').value = '".$actual_yarn_comp."';\n";
    	echo "document.getElementById('txtYarnComPhys').value = '".$actual_yarn_comp_phy."';\n";

    	echo "document.getElementById('txtPilReq').value = '".$pilling."';\n";
    	echo "document.getElementById('txtPilPhys').value = '".$pilling_phy."';\n";
    	echo "document.getElementById('txtBrusReq').value = '".$brusting."';\n";
    	echo "document.getElementById('txtBrusPhys').value = '".$brusting_phy."';\n";

    	echo "document.getElementById('txtTwistPerInchReq').value = '".$twist_per_inc."';\n";
    	echo "document.getElementById('txtTwistPerInchPhys').value = '".$twist_per_inc_phy."';\n";
    	echo "document.getElementById('txtMoistureContentReq').value = '".$moisture_content."';\n";
    	echo "document.getElementById('txtMoistureContentPhys').value = '".$moisture_content_phy."';\n";
    	echo "document.getElementById('txtIpiValueReq').value = '".$ipi_value."';\n";
    	echo "document.getElementById('txtIpiValuePhys').value = '".$ipi_value_phy."';\n";
    	echo "document.getElementById('txtCspMinimumReq').value = '".$csp_minimum."';\n";
    	echo "document.getElementById('txtCspMinimumPhys').value = '".$csp_minimum_phy."';\n";
    	echo "document.getElementById('txtCspActualReq').value = '".$csp_actual."';\n";
    	echo "document.getElementById('txtCspActualPhys').value = '".$csp_actual_phy."';\n";
    	echo "document.getElementById('txtThinYarnReq').value = '".$thin_yarn."';\n";
    	echo "document.getElementById('txtThinYarnPhys').value = '".$thin_yarn_phy."';\n";
    	echo "document.getElementById('txtThickReq').value = '".$thick."';\n";
    	echo "document.getElementById('txtThickPhys').value = '".$thick_phy."';\n";
    	echo "document.getElementById('txtUReq').value = '".$u."';\n";
    	echo "document.getElementById('txtUPhys').value = '".$u_phy."';\n";
    	echo "document.getElementById('txtCvReq').value = '".$cv."';\n";
    	echo "document.getElementById('txtCvPhys').value = '".$cv_phy."';\n";
    	echo "document.getElementById('txtNepsPerKmReq').value = '".$neps_per_km."';\n";
    	echo "document.getElementById('txtNepsPerKmPhys').value = '".$neps_per_km_phy."';\n";
    	echo "document.getElementById('txtHearinessReq').value = '".$heariness."';\n";
    	echo "document.getElementById('txtHearinessPhys').value = '".$heariness_phy."';\n";
    	echo "document.getElementById('txtCountsCvReq').value = '".$counts_cv."';\n";
    	echo "document.getElementById('txtCountsCvPhys').value = '".$counts_cv_phy."';\n";
    	echo "document.getElementById('txtSystemResult').value = '".$row[csf("system_result")]."';\n";
    	echo "document.getElementById('txtGreyGsmKnit').value = '".$grey_gsm."';\n";
    	echo "document.getElementById('txtGreyGashGsmKnit').value = '".$grey_wash_gsm."';\n";
    	echo "document.getElementById('txtRequiredGsmKnit').value = '".$required_gsm."';\n";
    	echo "document.getElementById('txtRequiredDiaKnit').value = '".$required_dia."';\n";
    	echo "document.getElementById('txtMachineDiaKnit').value = '".$machine_dia."';\n";
    	echo "document.getElementById('txtStichLengthKnit').value = '".$stich_length."';\n";
    	echo "document.getElementById('txtGreyGsmDye').value = '".$grey_gsm_dye."';\n";
    	echo "document.getElementById('txtBatchDye').value = '".$batch."';\n";
    	echo "document.getElementById('txtFinishGsmDye').value = '".$finish_gsm."';\n";
    	echo "document.getElementById('txtFinishDiaDye').value = '".$finish_dia."';\n";
    	echo "document.getElementById('txtLengthDye').value = '".$length."';\n";
    	echo "document.getElementById('txtWidthDye').value = '".$width."';\n";
    	echo "document.getElementById('cboCommentsAcceptanceKnit').value = '".$row[csf("comments_knit_acceptance")]."';\n";
    	echo "document.getElementById('txtCommentsKnit').value = '".$row[csf("comments_knit")]."';\n";
    	echo "document.getElementById('cboCommentsAcceptanceDye').value = '".$row[csf("comments_dye_acceptance")]."';\n";
    	echo "document.getElementById('txtCommentsDye').value = '".$row[csf("comments_dye")]."';\n";
    	echo "document.getElementById('cboCommentsAcceptanceAuthor').value = '".$row[csf("comments_author_acceptance")]."';\n";
    	echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
    	echo "document.getElementById('txtCommentsAuthor').value = '".$row[csf("comments_author")]."';\n";
    	echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
    	echo "document.getElementById('approved_status').value = '".$row[csf("approved")]."';\n";
    	echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_yarn_test_entry',1);\n";
    }

  	$sql_dtls_knit = "SELECT a.id, b.id as dtls_id, b.testing_parameters, b.fabric_point, b.result, b.acceptance, b.fabric_class, b.remarks, b.fab_type, b.testing_parameters_id from inv_yarn_test_mst a,  inv_yarn_test_dtls b where a.id=b.mst_id and a.id=$data and b.fab_type in(1,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
    $sql_dtls_rslt_knit = sql_select($sql_dtls_knit);

    foreach ($sql_dtls_rslt_knit as $row)
    {
    	if ($row[csf('fab_type')]==1)
    	{
    		$row_id = $row[csf('testing_parameters_id')];
    		if ($row[csf("fabric_point")]==0) $fabric_point='';else $fabric_point=$row[csf("fabric_point")];
    		echo "document.getElementById('updatedtlsidknit_'+$row_id).value = ".$row[csf("dtls_id")].";\n";
    		echo "document.getElementById('testingParamKnit_'+$row_id).value = '".$row[csf("testing_parameters")]."';\n";
    		echo "document.getElementById('txtPointKnit_'+$row_id).value = '".$fabric_point."';\n";
    		echo "document.getElementById('cboResultKnit_'+$row_id).value = '".$row[csf("result")]."';\n";
    		echo "document.getElementById('cboAcceptanceKnit_'+$row_id).value = '".$row[csf("acceptance")]."';\n";
    		echo "document.getElementById('txtFabricClassKnit_'+$row_id).value = '".$row[csf("fabric_class")]."';\n";
    		echo "document.getElementById('txtRemarksKnit_'+$row_id).value = '".$row[csf("remarks")]."';\n";
    	}
    	else
    	{
    		$row_id = $row[csf('testing_parameters_id')];
    		if ($row[csf("fabric_point")]==0) $fabric_point='';else $fabric_point=$row[csf("fabric_point")];
    		echo "document.getElementById('updatedtlsiddye_'+$row_id).value = ".$row[csf("dtls_id")].";\n";
    		echo "document.getElementById('testingParamDye_'+$row_id).value = '".$row[csf("testing_parameters")]."';\n";
    		echo "document.getElementById('txtPointDye_'+$row_id).value = '".$fabric_point."';\n";
    		echo "document.getElementById('cboResultDye_'+$row_id).value = '".$row[csf("result")]."';\n";
    		echo "document.getElementById('cboAcceptanceDye_'+$row_id).value = '".$row[csf("acceptance")]."';\n";
    		echo "document.getElementById('txtFabricClassDye_'+$row_id).value = '".$row[csf("fabric_class")]."';\n";
    		echo "document.getElementById('txtRemarksDye_'+$row_id).value = '".$row[csf("remarks")]."';\n";
    	}
    }
}

?>