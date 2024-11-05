<?
date_default_timezone_set("Asia/Dhaka");

header('Content-type:text/html; charset=utf-8');
session_start();
//if(date("D",time())!='Tue'){echo "This mail will be send only day of Tue";die;}
	include('../../includes/common.php');
	require_once('../../mailer/class.phpmailer.php');
	require_once('../setting/mail_setting.php');


//$user_id = $_SESSION['logic_erp']["user_id"];
$user_id = 9999;

$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];



	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
    $previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
    //$previous_date = "06-Sep-2022";
	
	 //1,2,3,4,8
	$company_arr 	= return_library_array("select id, company_name from lib_company where status_active=1","id","company_name");
	$short_company_arr 	= return_library_array("select id, company_short_name from lib_company where status_active=1","id","company_short_name");


	$action="generate_report";

	$cbo_company_name=1;
	$cbo_dyed_type=0;
	$yarn_type_id="";
	$txt_count="";
	$txt_lot_no="";
	$from_date=$previous_date;
	$to_date=$previous_date;
	$store_wise=1;
	$store_name="1,40";
	$value_with=1;
	$supplier_id="";
	$show_val_column=1;
	$get_upto=0;
	$txt_days="";
	$get_upto_qnty=1;
	$txt_qnty=1;
	$type=1;
	$txt_composition="";
	$txt_composition_id="";
	$lot_search_type=0;
	$source_name=0;

//--------------------------------------------------------------------------------------------

if ($action == "generate_report")
{
	ob_start();
	?>
	<div style="width: 440px;float: center;">
		<p style="font-size:20px; text-align: center;">
			Yarn Stock Ageing Report <br>
			<span style="font-size:12px; text-align: center;">Date: <?= change_date_format($previous_date); ?></span>
		</p>
	</div>

	<?
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));	


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

          	if ($yarn_type_id > 0)
          	{
          		$search_cond .= " and a.yarn_type in ($yarn_type_id)";
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

          	if ($supplier_id > 0)
          	{
          		$search_cond .= "  and a.supplier_id in($supplier_id)";
          	}
          	if ($txt_composition != "")
          	{
						//$search_cond .= " and a.product_name_details like '%" . trim($txt_composition) . "%'";
						 //$search_cond .= " and (a.yarn_comp_type1st = " .$txt_composition_id . " or a.yarn_comp_type2nd = " .$txt_composition_id .") " ;
          		$search_cond .= " and a.yarn_comp_type1st in (" .$txt_composition_id .")";
          	}

          	if ($show_val_column == 1) {
          		$value_width = 400;
          		$span = 3;
          		$column = '<th rowspan="2" width="90">Avg. Rate (Tk)</th><th rowspan="2" width="110">Stock Value</th><th rowspan="2" width="100">Avg. Rate (USD)</th><th rowspan="2" width="100">Stock Value (USD)</th>';
          	} else {
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
          			$store_cond .= " and a.store_id in($store_name)";
          		$table_width = '3000' + $value_width;
          		$colspan = '29' + $span;

          		if ($db_type == 0)
          			$select_field = "group_concat(distinct(a.store_id))";
          		else if ($db_type == 2)
          			$select_field = "listagg(a.store_id,',') within group (order by a.store_id)";

          		$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
          	}
          	else
          	{
          		$select_field = "0";
          		$table_width = '3000' + $value_width;
          		$colspan = '30' + $span;
          	}

          	if ($cbo_company_name == 0) {
          		$company_cond = "";
          		$nameArray = sql_select("select allocation from variable_settings_inventory where item_category_id=1 and variable_list=18 and status_active=1 and is_deleted=0");
          	} else {
          		$company_cond = " and a.company_id=$cbo_company_name";
          		$nameArray = sql_select("select allocation from variable_settings_inventory where company_name=$cbo_company_name and item_category_id=1 and variable_list=18 and status_active=1 and is_deleted=0");
          	}
          	$allocated_qty_variable_settings = $nameArray[0][csf('allocation')];
		    	//$allocated_qty_variable_settings=0;

			if ($source_name > 0)
			{
				$source_cond = "  and c.source =$source_name";
			}
			else
			{
				$source_cond="";
			}


          	$receive_array = array();
			$prodIdsArr = array();
			$sql_rcv = "Select a.prod_id, c.source,a.store_id
			from inv_transaction a, inv_receive_master c where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.source<>0 $company_cond $store_cond $source_cond group by a.prod_id, c.source,a.store_id";
			//echo $sql_rcv;
			$result_rcv = sql_select($sql_rcv);
			foreach ($result_rcv as $row)
			{
				$receive_array[$row[csf("prod_id")]]['source'] = $row[csf("source")];
				$receive_array[$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];

				if($prodIdChk[$row[csf('prod_id')]] == "")
				{
					$prodIdChk[$row[csf('prod_id')]] = $row[csf('prod_id')];
					array_push($prodIdsArr,$row[csf('prod_id')]);
				}
			}
			
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
			//$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
			//$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;
          	$result_sql_receive = sql_select($sql_receive);
          	foreach ($result_sql_receive as $row)
          	{
          		//$receive_array[$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
          		$receive_array[$row[csf("prod_id")]]['rcv_total_opening'] += $row[csf("rcv_total_opening")];
          		$receive_array[$row[csf("prod_id")]]['rcv_total_opening_amt'] += $row[csf("rcv_total_opening_amt")];
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
          	sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as issue_outside,
          	sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_outside_amt,
          	sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_return,
          	sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_return_amt,
          	sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as issue_loan,
          	sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_loan_amt
          	from inv_transaction a, inv_issue_master c
          	where a.mst_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond group by a.prod_id";
          	//echo $sql_issue; die;
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

          	if ($db_type == 0) {
          		$yarn_allo_sql = sql_select("select product_id, group_concat(buyer_id) as buyer_id, group_concat(allocate_qnty) as allocate_qnty from com_balk_yarn_allocate where status_active=1 group by product_id");
		    //LISTAGG(CAST( a.lc_sc_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_id) as lc_id
          	} else if ($db_type == 2) {
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

      	if ($type == 1)
      	{
			if ($value_with == 0)
					$search_cond .= "";
				else
					$search_cond .= "  and a.current_stock>0";
			?>
			<style type="text/css">
				.wrap_break {
					word-wrap: break-word;
					word-break: break-all;
				}
			</style>
			<?
			$date_array = array();
			$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 group by prod_id";
			$result_returnRes_date = sql_select($returnRes_date);
			foreach ($result_returnRes_date as $row)
			{
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
			}

			if ($source_name > 0)
			{
				$prodIds_cond = "  ".where_con_using_array($prodIdsArr,0,'a.id')." ";
			}
			else
			{
				$prodIds_cond="";
			}

			$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit
			from product_details_master a
			where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond $prodIds_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";

			//var_dump($date_array);
			/*if ($cbo_dyed_type == 0) {
				$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit
				from product_details_master a
				where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
			} else {
				$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit
				from product_details_master a, inv_transaction b, inv_receive_master c
				where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.entry_form=1 and b.transaction_type=1 $company_cond $search_cond
				group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_type,a.id";
			}*/

			//echo $sql;
			//die;//echo count($result);
			$result = sql_select($sql);
			$i = 1;
			//ob_start();
			
				$tot_stock_value = 0;
				foreach ($result as $row)
				{
					$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
					$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));

					$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
					if ($row[csf("yarn_comp_type2nd")] != 0)
						$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
					$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
					$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

					$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
					$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

					$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);

					$openingBalanceAmt = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt']) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt']);
					
					$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;

					$totalRcvAmt = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];

					$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;

					$totalIssueAmt = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] + $issue_array[$row[csf("id")]]['issue_loan_amt'] + $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];

					$stockInHand = ($openingBalance + $totalRcv) - $totalIssue;
					$stockInHandAmt = $openingBalanceAmt + $totalRcvAmt - $totalIssueAmt;

				    //subtotal and group-----------------------
					$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];

					if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0))
					    {
					
							if($value_with == 1)
							{
								if (number_format($stockInHand, 2) > 0.00)
								{
									$uom_id=12; 
									
									//$ageOfDays = datediff("d", $mrr_date, date("Y-m-d"));
									//if($uom_id!=1)
									//{
										
									//}


										$stock_value = 0;
										if ($show_val_column == 1) {
											$stock_value = $stockInHandAmt;
											$stock_value_usd = $stockInHandAmt / $exchange_rate;
											$avz_rates_usd=0;
											if(number_format($stock_value_usd,2)>0 && number_format($stockInHand,2)>0) $avz_rates_usd=$stock_value_usd/$stockInHand;

											$avg_rate = ($stockInHandAmt/$stockInHand); //$mrr_rate_arr[$row[csf("id")]];
											if($avg_rate>0)
											{
												$avg_rate = $avg_rate;
											}else{
												$avg_rate = "0.00"; //$mrr_rate_arr[$row[csf("id")]];
											}
											$company_id=$row[csf("company_id")];
											$dataArrTex[$company_id][$uom_id][$ageOfDays]['stock_qntys']+=$stockInHand;
											$dataArrTex[$company_id][$uom_id][$ageOfDays]['stock_amounts']+=$stock_value;


											//echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
											
										}
									
							    }
						    }
						
                    }
                }
                
        }
       
		$ageRangeTex=array(1=> "1-30", 2=> "31-60", 3=> "61-90", 4=> "91-120", 5=> "121-150", 6=> "151-180", 7=> "Above 180");

		foreach ($dataArrTex as $companyIDtex => $companyDataTex) 
		{
			foreach ($companyDataTex as $uomIDtex => $uomDataTex) 
			{
				foreach ($uomDataTex as $ageKeyTex => $valueTex) 
				{

					$comUomArrTex[$companyIDtex][$uomIDtex]=$uomIDtex;
					if($ageKeyTex<=30)
					{
						$mainArrTex[$companyIDtex][1][$uomIDtex]+=$valueTex['stock_qntys'];
						$mainArrTex2[$companyIDtex][1]+=$valueTex['stock_qntys'];
						$mainArrTex3[$companyIDtex][1]+=$valueTex['stock_amounts'];
					}
					else if($ageKeyTex>30 && $ageKeyTex<=60)
					{
						$mainArrTex[$companyIDtex][2][$uomIDtex]+=$valueTex['stock_qntys'];
						$mainArrTex2[$companyIDtex][2]+=$valueTex['stock_qntys'];
						$mainArrTex3[$companyIDtex][2]+=$valueTex['stock_amounts'];
					}
					else if($ageKeyTex>60 && $ageKeyTex<=90)
					{
						$mainArrTex[$companyIDtex][3][$uomIDtex]+=$valueTex['stock_qntys'];
						$mainArrTex2[$companyIDtex][3]+=$valueTex['stock_qntys'];
						$mainArrTex3[$companyIDtex][3]+=$valueTex['stock_amounts'];
					}
					else if($ageKeyTex>90 && $ageKeyTex<=120)
					{
						$mainArrTex[$companyIDtex][4][$uomIDtex]+=$valueTex['stock_qntys'];
						$mainArrTex2[$companyIDtex][4]+=$valueTex['stock_qntys'];
						$mainArrTex3[$companyIDtex][4]+=$valueTex['stock_amounts'];
					}
					else if($ageKeyTex>120 && $ageKeyTex<=150)
					{
						$mainArrTex[$companyIDtex][5][$uomIDtex]+=$valueTex['stock_qntys'];
						$mainArrTex2[$companyIDtex][5]+=$valueTex['stock_qntys'];
						$mainArrTex3[$companyIDtex][5]+=$valueTex['stock_amounts'];
					}

					else if($ageKeyTex>150 && $ageKeyTex<=180)
					{
						$mainArrTex[$companyIDtex][6][$uomIDtex]+=$valueTex['stock_qntys'];
						$mainArrTex2[$companyIDtex][6]+=$valueTex['stock_qntys'];
						$mainArrTex3[$companyIDtex][6]+=$valueTex['stock_amounts'];
					}
					else if($ageKeyTex>180)
					{
						$mainArrTex[$companyIDtex][7][$uomIDtex]+=$valueTex['stock_qntys'];
						$mainArrTex2[$companyIDtex][7]+=$valueTex['stock_qntys'];
						$mainArrTex3[$companyIDtex][7]+=$valueTex['stock_amounts'];
					}
				}
			}
		}
		/*echo "<pre>";
		print_r($mainArrTex3);
		echo "</pre>";*/

		//sort($comUomArrTex);
		foreach ($mainArrTex2 as $companyIDsTex => $companyDatasTex) 
		{

			 ?>		
	 		<div style="height: 250px;width: auto;float: left;">
	            <table border="1" rules="all" cellpadding="0" cellspacing="0" width="440" class="rpt_table" style="float: left; margin-right: 10px;margin-bottom: 10px;">
	               	<caption style="background-color:#f9f9f9; font-size: 16px; font-weight: bold;">Yarn Stock (Without Seamless)</caption>
	               <thead>
	                  <th width="30">SL</th>
	                  <th width="100">Age Days</th>
	                  <?
	                 foreach ($comUomArrTex[$companyIDsTex] as $uomKeyTex => $uomNameTex) 
	     			 {
		                     ?>
		                     <th width="100">Stock (<? echo $unit_of_measurement[$uomNameTex]; ?>)</th>
		                     <?
	                  }
	                  ?>
	                   <th width="120">Total Value (Taka)</th>
	               </thead>
	              <tbody>
		              <?
					    $i=1; 
					    ksort($companyDatasTex);

						/*foreach ($companyDatasTex as $ageKeyRangeTex => $ageDataTex) 
						{*/ 
						foreach ($ageRangeTex as $ageKeyRangeTex => $ageDataTex) 
						{
							?>
							<tr>
			                     <td><? echo $i; ?></td>
			                     <td><? echo $ageRangeTex[$ageKeyRangeTex]; ?></td>


			                     <?
								foreach ($comUomArrTex[$companyIDsTex] as $uomKeyTex => $uomNameTex) 
			     			 	{
			     			 		$totalArr[$companyIDsTex][$uomKeyTex]+=ceil($mainArrTex[$companyIDsTex][$ageKeyRangeTex][$uomKeyTex]);
									?>
								 		<td align="right">
								 			<? 
								 				$ageWiseTexQnty=ceil($mainArrTex[$companyIDsTex][$ageKeyRangeTex][$uomKeyTex]);
								 				echo number_format($ageWiseTexQnty);
								 				//echo number_format($mainArrTex[$companyIDsTex][$ageKeyRangeTex][$uomKeyTex],2); 
								 			?>
								 			
								 		</td>
									<?	
								}
							

							?>
								<td align="right">
									<? 
									$ageWiseTexAmount=ceil($mainArrTex3[$companyIDsTex][$ageKeyRangeTex]);
									echo number_format($ageWiseTexAmount);
									//echo number_format($mainArrTex3[$companyIDsTex][$ageKeyRangeTex],2); 
									$totalArrAmountTex[$companyIDsTex]+=ceil($mainArrTex3[$companyIDsTex][$ageKeyRangeTex]);
									?>
								
								</td>
							 </tr>  
							 <?
							 $i++;
						}
						?>
				 	</tbody>
			 		<tfoot>
				 		<tr>
				 			<th></th>
				 			<th></th>
				 			<?
				 			 
								foreach ($comUomArrTex[$companyIDsTex] as $uomKeyTex => $uomNameTex) 
		     			 		{
		     			 			?>

				 					<th align="right"><? echo number_format($totalArr[$companyIDsTex][$uomKeyTex],0); ?></th>

				 					<?
				 				}
				 			
				 		?>
				 			<th align="right"><? echo number_format($totalArrAmountTex[$companyIDsTex],0); ?></th>
				 		</tr>
			 		</tfoot>
	   			</table>
			</div>
				<?
		}


	
	$message=ob_get_contents();
	ob_clean();

	$to='';
	$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=112 and b.mail_user_setup_id=c.id and a.company_id in($cbo_company_name)  and   A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
 
	
	
	$mail_sql=sql_select($sql);
	$receverMailArr=array();
	foreach($mail_sql as $row)
	{
		//$mailAdd="ma.kaiyum1992@gmail.com";
		$receverMailArr[$row[csf('email_address')]]=$row[csf('email_address')];		
	}

	$to=implode(',',$receverMailArr);
 
	
	
	$subject="Yarn Stock Ageing Report";
	$header=mailHeader();
	if($_REQUEST['isview']==1){
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo  $message;
	}
	else{
		//echo $to."<br/>".$subject."<br/>". $message."<br/>". $from_mail."<br/>".$att_file_arr;
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr );}
	}
	
}

//}//company
 
 
?>
