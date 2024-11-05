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

if ($action == "load_drop_down_store") {
	$data = explode("**", $data);

	if ($data[1] == 2) {
		$disable = 1;
	} else {
		$disable = 0;
	}
	echo create_drop_down("cbo_store_name", 100, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type in(1)  order by a.store_name", "id,store_name", 1, "-- All Store--", 0, "", $disable);
	exit();
}

if ($action == "generate_report") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	// echo $txt_excange_rate;die;

	$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$companyArr[0] = "All Company";
	$supplierArr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$brandArr = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyer_dtls = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$composition_array = return_library_array("select id, COMPOSITION_NAME from LIB_COMPOSITION_ARRAY", 'id', 'COMPOSITION_NAME');
	$buy_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$yarnTestQalityCommentsArr = return_library_array("select prod_id, yarn_quality_coments from inv_yarn_test_mst where  status_active=1 and is_deleted=0", 'prod_id', 'yarn_quality_coments');
	$yarnTestArr = return_library_array("select prod_id, lot_number from inv_yarn_test_mst where status_active=1 and is_deleted=0", 'prod_id', 'lot_number');
	$floor_room_rack_arr = return_library_array("select floor_room_rack_id,floor_room_rack_name from lib_floor_room_rack_mst", 'floor_room_rack_id', 'floor_room_rack_name');
	//echo '<pre>';print_r($yarnTestArr);die;

	$variable_store_wise_rate = return_field_value("max(auto_transfer_rcv) auto_transfer_rcv", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=47 and item_category_id=1 and status_active=1 and is_deleted=0", "auto_transfer_rcv");
	$variable_store_wise_rate = ($variable_store_wise_rate == "") ? 0 : $variable_store_wise_rate;

	//=======================================================================
	$current_date = date("d-m-Y");
	$p = 1;
	$queryText = sql_select("select ID, COMPANY_ID, CON_DATE, CONVERSION_RATE from currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 and COMPANY_ID>0 order by COMPANY_ID, ID");
	$company_wise_data = array();
	foreach ($queryText as $row) {
		$company_wise_data[$row["COMPANY_ID"]]++;
	}
	//echo "<pre>";print_r($company_wise_data);die;
	//echo "select ID, COMPANY_ID, CON_DATE, CONVERSION_RATE from currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 and COMPANY_ID>0 order by COMPANY_ID, ID";die;
	//echo count($queryText);die;
	$conversion_data_arr = array();
	$previous_date = "";
	$company_check_arr = array();
	foreach ($queryText as $val) {
		if ($company_check_arr[$val["COMPANY_ID"]] == "") {
			$company_check_arr[$val["COMPANY_ID"]] = $val["COMPANY_ID"];
			$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($val["CON_DATE"])] = $val["CONVERSION_RATE"];
			$sStartDate = date("Y-m-d", strtotime($val["CON_DATE"]));
			$sCurrentDate = $sStartDate;
			$sEndDate = $sStartDate;
			$previous_date = $sStartDate;
			$previous_rate = $val["CONVERSION_RATE"];
			//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";

			$sStartDate = date("Y-m-d", strtotime("+1 day", strtotime($val["CON_DATE"])));
			$sEndDate = date("Y-m-d", strtotime($current_date));
			$sCurrentDate = $sStartDate;
			//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
			while ($sCurrentDate <= $sEndDate) {

				$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)] = $val["CONVERSION_RATE"];
				$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
			}
			$q = 1;
		} else {
			$q++;
			$sStartDate = date("Y-m-d", strtotime($previous_date));
			if ($company_wise_data[$val["COMPANY_ID"]] == $q) {
				$sEndDate = gmdate("Y-m-d", strtotime($val["CON_DATE"]));
				$sCurrentDate = $sStartDate;
				//echo $sCurrentDate."=".$sEndDate."=".$previous_rate."<br>";
				while ($sCurrentDate <= $sEndDate) {
					$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)] = $previous_rate;
					$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
				}

				$sStartDate = date("Y-m-d", strtotime("+1 day", strtotime($sEndDate)));
				$sEndDate = date("Y-m-d", strtotime($current_date));
				$sCurrentDate = $sStartDate;
				//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
				while ($sCurrentDate <= $sEndDate) {

					$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)] = $val["CONVERSION_RATE"];
					$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
				}
				$previous_date = $val["CON_DATE"];
				$previous_rate = $val["CONVERSION_RATE"];
			} else {
				$sEndDate = gmdate("Y-m-d", strtotime($val["CON_DATE"]));
				$sCurrentDate = $sStartDate;
				//echo $sCurrentDate."=".$sEndDate."=".$previous_rate."<br>";
				while ($sCurrentDate <= $sEndDate) {
					$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)] = $previous_rate;
					$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
				}
				$previous_date = $val["CON_DATE"];
				$previous_rate = $val["CONVERSION_RATE"];
			}
		}
		$p++;
	}
	unset($queryText);

	/* 	echo "<pre>";
	print_r($conversion_data_arr[17]);
	die; */

	//=======================================================================

	if ($db_type == 0) {

		/* if ($txt_excange_rate != "") {
			$exchange_rate = $txt_excange_rate;
		} else {
			$exchange_rate = return_field_value("conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and is_deleted=0 order by id DESC limit 0,1");
		} */

		$from_date = change_date_format($from_date, 'yyyy-mm-dd');
		$to_date = change_date_format($to_date, 'yyyy-mm-dd');
	} else if ($db_type == 2) {

		/* if ($txt_excange_rate != "") {
			$exchange_rate = $txt_excange_rate;
		} else {
			$exchange_rate = return_field_value("conversion_rate", "(SELECT conversion_rate FROM currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 ORDER BY id DESC)", "ROWNUM = 1");
		} */

		$from_date = change_date_format($from_date, '', '', 1);
		$to_date = change_date_format($to_date, '', '', 1);
	} else {
		$from_date = "";
		$to_date = "";
		//$exchange_rate = 1;
	}

	$txt_lot_no = trim($txt_lot_no);
	$txt_composition = trim($txt_composition);

	$search_cond = "";
	$search_cond2 = "";

	if ($yarn_type_id > 0) {
		$search_cond .= " and a.yarn_type in ($yarn_type_id)";
		$search_cond2 .= " and d.yarn_type in ($yarn_type_id)";
	}

	if ($txt_count != "") {
		$search_cond .= " and a.yarn_count_id in($txt_count)";
		$search_cond2 .= " and d.yarn_count_id in($txt_count)";
	}

	if ($txt_lot_no != "") {
		$search_cond .= " and trim(a.lot)='" . trim($txt_lot_no) . "'";
		$search_cond2 .= " and trim(d.lot)='" . trim($txt_lot_no) . "'";
	}

	if ($txt_supplier > 0) {
		$search_cond .= "  and a.supplier_id in($txt_supplier)";
		$search_cond2 .= "  and d.supplier_id in($txt_supplier)";
	}

	if ($txt_composition != "") {
		$search_cond .= " and a.yarn_comp_type1st in (" . $txt_composition_id . ")";
		$search_cond2 .= " and d.yarn_comp_type1st in (" . $txt_composition_id . ")";
	}

	if ($cbo_dyed_type > 0) {
		if ($cbo_dyed_type == 1) {
			if ($type == 18) {
				$search_cond2 .= " and d.dyed_type in (2)";
				$search_cond .= " and a.dyed_type in (2)";
			} else {
				$search_cond .= " and a.dyed_type in (1)";
			}
		} else
			$search_cond .= " and a.dyed_type in (2)";
		$search_cond2 .= " and d.dyed_type in (2)";
	}

	if ($txt_color != "") {
		$search_cond .= " and a.color in($txt_color)";
		$search_cond2 .= " and d.color in($txt_color)";
	}

	if ($cbo_company_name == 0) {
		$company_cond = "";
		$company_cond_mrr = "";
	} else {
		$company_cond = " and b.company_id=$cbo_company_name";
		$company_cond_mrr = " and a.company_id=$cbo_company_name";
	}

	if ($to_date != "")
		$mrr_date_cond = " and a.transaction_date<='$to_date'";
	if ($to_date != "")
		$rcv_date_cond = " and b.transaction_date<='$to_date'";

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
			text-decoration: none;
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

	if ($type == 6) {
		$issue_qnty_arr = sql_select("select a.prod_id, b.recv_trans_id, b.issue_qnty
		from  inv_transaction a,  inv_mrr_wise_issue_details b
		where a.id=b.issue_trans_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type in(2,3,6) and a.item_category=1 $company_cond_mrr $mrr_date_cond");
		$mrr_issue_qnty_arr = array();
		foreach ($issue_qnty_arr as $row) {
			$mrr_issue_qnty_arr[$row[csf("recv_trans_id")]][$row[csf("prod_id")]] += $row[csf("issue_qnty")];
		}
		unset($issue_qnty_arr);
		//echo "<pre>";
		//print_r($mrr_issue_qnty_arr[8706390]);
		//die;

		//for issue information
		$sql_issue_rslt = sql_select("SELECT b.ID, b.BUYER_ID FROM INV_ISSUE_MASTER b WHERE b.ITEM_CATEGORY=1" . $company_cond);
		$issue_data_arr = array();
		foreach ($sql_issue_rslt as $row) {
			$issue_data_arr[$row['ID']]['buyer_id'] = $row['BUYER_ID'];
		}
		unset($sql_issue_rslt);

		//for receive information
		if ($store_name != 0)  $store_cond = " and store_id in($store_name) ";
		$receive_sql = sql_select("select b.ID, b.RECV_NUMBER, b.RECEIVE_DATE, b.ISSUE_ID FROM INV_RECEIVE_MASTER b WHERE b.ITEM_CATEGORY=1 $store_cond " . $company_cond);
		$receive_data = array();
		foreach ($receive_sql as $row) {
			$receive_data[$row['ID']]['recv_number'] = $row['RECV_NUMBER'];
			$receive_data[$row['ID']]['receive_date'] = $row['RECEIVE_DATE'];
			$receive_data[$row['ID']]['issue_id'] = $row['ISSUE_ID'];
		}
		unset($receive_sql);

		//for transfer information
		$transfer_sql = sql_select("select b.id, b.transfer_system_id, b.transfer_date from inv_item_transfer_mst b where b.item_category=1 " . $company_cond);
		$transfer_data = array();
		foreach ($transfer_sql as $row) {
			$transfer_data[$row[csf("id")]]["transfer_system_id"] = $row[csf("transfer_system_id")];
			$transfer_data[$row[csf("id")]]["transfer_date"] = $row[csf("transfer_date")];
		}
		unset($transfer_sql);

		//for transaction information
		$mrr_rate_sql = sql_select("select prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction
      	where status_active=1 and is_deleted=0 and item_category=1 and transaction_type in(1,4,5) and cons_quantity>0 group by prod_id");
		$mrr_rate_arr = array();
		foreach ($mrr_rate_sql as $row) {
			$mrr_rate_arr[$row[csf("prod_id")]] = $row[csf("cons_amount")] / $row[csf("cons_quantiy")];
		}
		unset($mrr_rate_sql);

		if ($db_type == 0) {
			$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, b.mst_id, b.transaction_type, group_concat(b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount
	      	from product_details_master a, inv_transaction b
	      	where a.id=b.prod_id and a.item_category_id=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(1,4) $company_cond $search_cond  $rcv_date_cond
	      	group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, b.mst_id, b.transaction_type
	      	union all
	      	select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, b.mst_id, b.transaction_type, group_concat(b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount
	      	from product_details_master a, inv_transaction b, inv_item_transfer_mst c
	      	where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(5) $company_cond $search_cond  $rcv_date_cond
	      	group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, b.mst_id, b.transaction_type";
		} else {
			$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, b.mst_id, b.transaction_type, b.buyer_id, d.pay_mode, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount
	      	from product_details_master a, inv_transaction b left join wo_yarn_dyeing_mst d on b.pi_wo_batch_no=d.id
	      	where a.id=b.prod_id and a.item_category_id=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(1,4) $company_cond $search_cond  $rcv_date_cond
	      	group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, b.mst_id, b.transaction_type, b.buyer_id, d.pay_mode
	      	union all
	      	select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, b.mst_id, b.transaction_type, b.buyer_id, d.pay_mode,listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount
	      	from product_details_master a, inv_transaction b left join wo_yarn_dyeing_mst d on b.pi_wo_batch_no=d.id, inv_item_transfer_mst c
	      	where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(5) $company_cond $search_cond  $rcv_date_cond
	      	group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, b.mst_id, b.transaction_type, b.buyer_id, d.pay_mode
			order by  yarn_count_id,  yarn_comp_type1st, yarn_type,id";
		}
		//echo $sql;die;
		$result = sql_select($sql);
		$i = 1;
		//ob_start();
	?>
		<div>
			<table width="1780" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:'Arial Narrow'; font-size:14px" rules="all" id="table_header_1">
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="17" align="center" style="border:none;font-size:16px; font-weight:bold">Daily Yarn Stock </td>
					</tr>
					<tr style="border:none;">
						<td colspan="17" align="center" style="border:none; font-size:14px;">
							Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="17" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
						</td>
					</tr>
					<tr>
						<th rowspan="2" width="40">SL</th>
						<th rowspan="2" width="120">Company Name</th>
						<th colspan="7">Description</th>
						<th rowspan="2" width="100">Stock In Hand</th>
						<th rowspan="2" width="90">Avg. Rate (USD)</th>
						<th rowspan="2" width="100">Stock Value (USD)</th>
						<th rowspan="2" width="120">MRR No.</th>
						<th rowspan="2" width="80">Receive Date</th>
						<th rowspan="2" width="100">Buyer</th>
						<th rowspan="2" width="50">Age (Days)</th>
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
			<div style="width:1780px; overflow-y:scroll; max-height:350px" id="scroll_body">
				<table width="1760" border="1" cellpadding="2" style="font:'Arial Narrow';" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
					$tot_stock_value = 0;
					foreach ($result as $row) {
						$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
						if ($row[csf("yarn_comp_type2nd")] != 0)
							$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";

						$totalRcv = $row[csf("cons_quantity")];
						$totalIssue = 0;
						$stockInHand = $avg_rate = 0;

						$trans_id_arr = array_unique(explode(",", $row[csf("trans_id")]));
						foreach ($trans_id_arr as $tr_id) {
							$totalIssue += $mrr_issue_qnty_arr[$tr_id][$row[csf("id")]];
						}

						$stockInHand = $totalRcv - $totalIssue;

						//subtotal and group-----------------------
						$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];
						//$receive_data[$row[csf("id")]]["recv_number"]
						$mrr_number = $mrr_date = "";
						if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4) {
							$mrr_number = $receive_data[$row[csf("mst_id")]]["recv_number"];
							$mrr_date = $receive_data[$row[csf("mst_id")]]["receive_date"];
						} else {
							$mrr_number = $transfer_data[$row[csf("mst_id")]]["transfer_system_id"];
							$mrr_date = $transfer_data[$row[csf("mst_id")]]["transfer_date"];
						}

						$ageOfDays = datediff("d", $mrr_date, date("Y-m-d"));

						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$avg_rate_usd = 0;
						$avg_rate = $row[csf("cons_amount")] / $row[csf("cons_quantity")];
						$avg_rate = (is_nan($avg_rate) == 1 || is_infinite($avg_rate)) ? 0 : $avg_rate;

						$stock_value = $stockInHand * $avg_rate;
						$stock_value_usd = $stock_value / $exchange_rate;
						$stock_value_usd = (is_nan($stock_value_usd) == 1 || is_infinite($stock_value_usd)) ? 0 : $stock_value_usd;

						$avg_rate_usd = $stock_value_usd / $stockInHand;
						$avg_rate_usd = (is_nan($avg_rate_usd) == 1 || is_infinite($avg_rate_usd)) ? 0 : $avg_rate_usd;
						$avg_rate_usd = abs($avg_rate_usd);

						//for supplier
						if ($row[csf('is_within_group')] == 1) {
							$supplier_name = $companyArr[$row[csf('supplier_id')]];
						} else {
							$supplier_name = $supplierArr[$row[csf('supplier_id')]];
						}
						//end for supplier

						//for buyer
						if ($row[csf("transaction_type")] == 4) {
							$row[csf('buyer_id')] = $issue_data_arr[$receive_data[$row[csf('mst_id')]]['issue_id']]['buyer_id'];
						}
						//end for buyer

						if ($value_with == 1) {
							if (number_format($stockInHand, 2) > 0.00) {
								if (!in_array($check_string, $checkArr)) {
									$checkArr[$i] = $check_string;
									if ($i > 1) {
					?>
										<tr bgcolor="#CCCCCC" style="font-weight:bold">
											<td colspan="9" align="right">Sub Total</td>
											<td width="100" align="right"><? echo number_format($sub_stock_in_hand, 2); ?></td>
											<td width="90" align="right">&nbsp;</td>
											<td width="100" align="right"><? echo number_format($sub_stock_value_usd, 2); ?></td>
											<td width="120" align="right">&nbsp;</td>
											<td width="80" align="right">&nbsp;</td>
											<td width="100" align="right">&nbsp;</td>
											<td width="50">&nbsp;</td>
											<td width="140">&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
								<?
										$sub_stock_in_hand = 0;
										$sub_stock_value = 0;
										$sub_stock_value_usd = 0;
									}
								}

								//$stock_value_usd=($stockInHand*$row[csf("avg_rate_per_unit")])/$exchange_rate;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40"><? echo $i; ?></td>
									<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $companyArr[$row[csf("company_id")]]; ?></td>
									<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $row[csf("id")]; ?></td>
									<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
									<td width="150" style="word-wrap:break-word; word-break: break-all;"><? echo $compositionDetails; ?></td>
									<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
									<td width="80" style="word-wrap:break-word; word-break: break-all;"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
									<td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@';">
										<p>
											<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") {
											?>
												<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"><? echo $row[csf("lot")]; ?></a>
											<?
											} else {
												echo $row[csf("lot")];
											}
											?>
										</p>
									</td>
									<td width="80" style="word-wrap:break-word; word-break: break-all;" title="<? echo "transaction Id=" . $row[csf("trans_id")] . "Receive Qnty=" . $totalRcv . "Issue Qnty=" . $totalIssue; ?>"><? echo $supplier_name; ?></td>
									<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right"><a href='##' onclick="openmypage_stock('<? echo $row[csf('id')] . "," . $row[csf('mst_id')] . "," . $row[csf('transaction_type')]; ?>', 'stock_popup_mrr')"><? echo number_format($stockInHand, 2); ?></a></td>
									<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right" title="<? echo $row[csf("cons_amount")] . "/" . $row[csf("cons_quantity")] . "=" . $avg_rate . "=" . $exchange_rate; ?>"><? echo number_format($avg_rate_usd, 4); ?></td>
									<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($stock_value_usd, 2); ?></td>
									<td width="120" align="center" title="<? echo "transaction type =" . $row[csf("transaction_type")]; ?>">
										<p><? echo $mrr_number; ?>&nbsp;</p>
									</td>
									<td width="80" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo change_date_format($mrr_date); ?></td>
									<td width="100" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo $buyer_dtls[$row[csf('buyer_id')]]; ?></td>
									<td width="50" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo $ageOfDays; ?></td>
									<?
									if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") {
									?>
										<td width="140" align="left"><span class="comment more"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
									<?
									} else {
									?>
										<td width="140" align="left"></td>
									<?
									}
									?>
									<td width="" align="right" style="word-wrap:break-word; word-break: break-all;">
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
										?>
									</td>
								</tr>
								<?
								$i++;
								/* $sub_stock_in_hand+=$stockInHand;
                                  $sub_stock_value+=$stock_value;
                                  $sub_stock_value_usd+=$stock_value_usd;

                                  $grand_total_stock_in_hand+=$stockInHand;
                                  $grand_total_stock_value+=$stock_value;
                                  $grand_total_stock_value_usd+=$stock_value_usd; */

								$sub_stock_in_hand += $stockInHand;
								$sub_stock_value += $stock_value;
								$sub_stock_value_usd += $stock_value_usd;

								$grand_total_stock_in_hand += $stockInHand;
								$grand_total_stock_value += $stock_value;
								$grand_total_stock_value_usd += $stock_value_usd;
							}
						} else {
							//$stock_value_usd=($stockInHand*$row[csf("avg_rate_per_unit")])/$exchange_rate;
							if ($stockInHand >= 0) {
								if (!in_array($check_string, $checkArr)) {
									$checkArr[$i] = $check_string;
									if ($i > 1) {
								?>
										<tr bgcolor="#CCCCCC" style="font-weight:bold">
											<td colspan="9" align="right">Sub Total</td>
											<td width="100" align="right"><? echo number_format($sub_stock_in_hand, 2); ?></td>
											<td width="90" align="right">&nbsp;</td>
											<td width="100" align="right"><? echo number_format($sub_stock_value_usd, 2); ?></td>
											<td width="120" align="right">&nbsp;</td>
											<td width="80" align="right">&nbsp;</td>
											<td width="100" align="right">&nbsp;</td>
											<td width="50">&nbsp;</td>
											<td width="140">&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
								<?
										$sub_stock_in_hand = 0;
										$sub_stock_value = 0;
										$sub_stock_value_usd = 0;
									}
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40"><? echo $i; ?></td>
									<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $companyArr[$row[csf("company_id")]]; ?></td>
									<td width="60"><? echo $row[csf("id")]; ?></td>
									<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
									<td width="150" style="word-wrap:break-word; word-break: break-all;"><? echo $compositionDetails; ?></td>
									<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
									<td width="80" style="word-wrap:break-word; word-break: break-all;"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
									<td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@';">
										<p>
											<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
												<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
											<? } else {
												echo $row[csf("lot")];
											}
											?>
											&nbsp;
										</p>
									</td>
									<td width="80" style="word-wrap:break-word; word-break: break-all;" title="<? echo "transaction Id=" . $row[csf("trans_id")] . "Receive Qnty=" . $totalRcv . "Issue Qnty=" . $totalIssue; ?>"><? echo $supplier_name; ?></td>
									<td width="100" align="right"><a href='##' onclick="openmypage_stock('<? echo $row[csf('id')] . "," . $row[csf('mst_id')] . "," . $row[csf('transaction_type')]; ?>', 'stock_popup_mrr')"><? echo number_format($stockInHand, 2); ?></a></td>
									<td width="90" align="right" title="<? echo $row[csf("cons_amount")] . "/" . $row[csf("cons_quantity")] . "=" . $avg_rate . "=" . $exchange_rate; ?>">
										<p><? echo number_format($avg_rate_usd, 4); ?></p>
									</td>
									<td width="100" align="right"><? echo number_format($stock_value_usd, 2); ?></td>
									<td width="120" align="center" title="<? echo "transaction type =" . $row[csf("transaction_type")]; ?>">
										<p><? echo $mrr_number; ?>&nbsp;</p>
									</td>
									<td width="80" align="center">
										<p><? echo change_date_format($mrr_date); ?>&nbsp;</p>
									</td>
									<td width="100"><? echo $buyer_dtls[$row[csf('buyer_id')]]; ?></td>
									<td width="50" align="center"><? echo $ageOfDays; ?></td>
									<?
									if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") {
									?>
										<td width="140" align="left"><span class="comment more"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
									<?
									} else {
									?>
										<td width="140" align="left"></td>
									<?
									}
									?>
									<td width="" align="right">
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
								$sub_stock_in_hand += $stockInHand;
								$sub_stock_value += $stock_value;
								$sub_stock_value_usd += $stock_value_usd;

								$grand_total_stock_in_hand += $stockInHand;
								$grand_total_stock_value += $stock_value;
								$grand_total_stock_value_usd += $stock_value_usd;
							}
						}
					}
					?>
					<tr bgcolor="#CCCCCC" style="font-weight:bold">
						<td colspan="9" align="right">Sub Total</td>
						<td width="100" align="right"><? echo number_format($sub_stock_in_hand, 2); ?></td>
						<td width="90" align="right">&nbsp;</td>
						<td width="100" align="right"><? echo number_format($sub_stock_value_usd, 2); ?></td>
						<td width="120" align="right">&nbsp;</td>
						<td width="80" align="right">&nbsp;</td>
						<td width="100" align="right">&nbsp;</td>
						<td width="50">&nbsp;</td>
						<td width="140">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
			<table width="1780" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
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
					<td width="100" align="right" id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 2); ?></td>
					<td width="90" align="right">&nbsp;</td>
					<td width="100" align="right"><? echo number_format($grand_total_stock_value_usd, 2); ?></td>
					<td width="120" align="right">&nbsp;</td>
					<td width="80" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="50" align="right">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</div>
		<?
	} else {
		if ($show_val_column == 1) {
			$value_width = 400;
			$span = 3;
			$column = '<th rowspan="2" width="90">Avg. Rate (Tk)</th><th rowspan="2" width="110">Stock Value</th><th rowspan="2" width="100">Avg. Rate (USD)</th><th rowspan="2" width="100">Stock Value (USD)</th>';
		} else {
			$value_width = 0;
			$span = 0;
			$column = '';
		}

		if ($store_wise == 1) {
			if ($store_name == 0)
				$store_cond .= "";
			else
				$store_cond .= " and a.store_id = $store_name";
			$table_width = '3900' + $value_width;
			$colspan = '32' + $span;
			$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		} else {
			$table_width = '3400' + $value_width;
			$colspan = '33' + $span;
		}

		if ($cbo_company_name == 0) {
			$company_cond = "";
			$nameArray = sql_select("select allocation from variable_settings_inventory where item_category_id=1 and variable_list=18 and status_active=1 and is_deleted=0");
		} else {
			$company_cond = " and a.company_id=$cbo_company_name";
			$nameArray = sql_select("select allocation from variable_settings_inventory where company_name=$cbo_company_name and item_category_id=1 and variable_list=18 and status_active=1 and is_deleted=0");
		}

		$allocated_qty_variable_settings = $nameArray[0][csf('allocation')];

		if ($source_name > 0) {
			$source_cond = "  and c.source =$source_name";
		} else {
			$source_cond = "";
		}

		//echo $to_date;

		$fromDate = strtotime($from_date);
		$toDate =  strtotime($to_date);

		$vs_select_consrate_amount = ($variable_store_wise_rate > 0) ? " a.store_rate as cons_rate , a.store_amount as cons_amount " : " a.cons_rate,a.cons_amount";

		if ($type == 18) {
			$sql_receive_sum = "SELECT a.id as trans_id,a.prod_id,a.receive_basis,a.store_id,a.transaction_type,a.transaction_date, a.weight_per_bag, a.weight_per_cone, a.cons_quantity,$vs_select_consrate_amount,a.floor_id, a.room, a.rack, a.self, a.bin_box,a.buyer_id,c.receive_purpose,c.knitting_source, c.source, c.remarks from inv_transaction a, inv_receive_master c, product_details_master d where a.mst_id=c.id and a.prod_id = d.id and a.item_category=1 and a.transaction_type in (1,4) and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $company_cond $store_cond $source_cond $search_cond2
			union all SELECT a.id as trans_id,a.prod_id,a.receive_basis,a.store_id,a.transaction_type,a.transaction_date,a.weight_per_bag, a.weight_per_cone,a.cons_quantity,$vs_select_consrate_amount,a.floor_id, a.room, a.rack, a.self, a.bin_box,a.buyer_id,0 as receive_purpose,0 as knitting_source, 0 as source, null as remarks from inv_transaction a where a.receive_basis=30 and a.item_category=1 and a.transaction_type in (1,4) AND a.transaction_date between '" . $from_date . "' and '" . $to_date . "' and a.status_active=1 and a.is_deleted=0 $company_cond $store_cond ";
			// echo $sql_receive_sum; die;
			// echo $search_cond; die;
			$result_sql_receive_sum = sql_select($sql_receive_sum);
			$inside_issue_return_total = 0;
			$outside_issue_return_total = 0;
			$pi_receive_total = 0;
			$independent_receive_total = 0;
			foreach ($result_sql_receive_sum as $row) {
				if ($row['TRANSACTION_TYPE'] == 4 && $row['KNITTING_SOURCE'] == 1) //inside issue return
				{
					$inside_issue_return_total += $row['CONS_QUANTITY'];
				}
				if ($row['TRANSACTION_TYPE'] == 4 && $row['KNITTING_SOURCE'] == 3) //outside issue return
				{
					$outside_issue_return_total += $row['CONS_QUANTITY'];
				}
				if ($row['TRANSACTION_TYPE'] == 1 && $row['RECEIVE_BASIS'] == 1) //pi basis receive
				{
					$pi_receive_total += $row['CONS_QUANTITY'];
				}
				if ($row['TRANSACTION_TYPE'] == 1 && $row['RECEIVE_BASIS'] == 4) //independent basis receive
				{
					$independent_receive_total += $row['CONS_QUANTITY'];
				}
			}
		}

		// echo $sql_receive_sum; die;
		$sql_receive = "SELECT a.id as trans_id,a.company_id,a.prod_id,a.receive_basis,a.store_id,a.transaction_type,a.transaction_date, a.weight_per_bag, a.weight_per_cone, a.cons_quantity,$vs_select_consrate_amount,a.floor_id, a.room, a.rack, a.self, a.bin_box,a.buyer_id,c.receive_purpose,c.knitting_source, c.source, c.remarks from inv_transaction a, inv_receive_master c where a.mst_id=c.id and a.item_category=1 and a.transaction_type in (1,4) and a.transaction_date <= '" . $to_date . "' and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $company_cond $store_cond $source_cond union all SELECT a.id as trans_id,a.company_id,a.prod_id,a.receive_basis,a.store_id,a.transaction_type,a.transaction_date,a.weight_per_bag, a.weight_per_cone,a.cons_quantity,$vs_select_consrate_amount,a.floor_id, a.room, a.rack, a.self, a.bin_box,a.buyer_id,0 as receive_purpose,0 as knitting_source, 0 as source, null as remarks from inv_transaction a where a.receive_basis=30 and a.item_category=1 and a.transaction_type in (1,4) AND a.transaction_date <= '" . $to_date . "' and a.status_active=1 and a.is_deleted=0 $company_cond $store_cond";

		$result_sql_receive = sql_select($sql_receive);
		$inside_issue_return_total = 0;
		$outside_issue_return_total = 0;
		$storeArr = $checkedRcvProdBuyerArr = array();
		foreach ($result_sql_receive as $row) {

			$transCompanyId = $row[csf("company_id")];
			$transDate = date("d-m-Y", strtotime($row[csf("transaction_date")]));
			$exchange_rate = ($txt_excange_rate != "") ? $txt_excange_rate : $conversion_data_arr[$transCompanyId][$transDate];

			$transaction_date = strtotime($row[csf("transaction_date")]);
			$transaction_type = $row[csf("transaction_type")];
			$consQuantity = $row[csf("cons_quantity")];
			$consAmount = $row[csf("cons_amount")];
			$consRate = $row[csf("cons_rate")];
			$receive_basis = $row[csf("receive_basis")];
			$receivePurpose = $row[csf("receive_purpose")];
			$knitting_source = $row[csf("knitting_source")];

			$rcv_total_opening_rate = ($transaction_date < $fromDate) ? $consRate : 0;
			$rcv_total_opening = ($transaction_date < $fromDate) ? $consQuantity : 0;
			$rcv_total_opening_amt = ($transaction_date < $fromDate) ? $consAmount : 0;

			$purchase = ($transaction_type == 1 && $receivePurpose != 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
			$purchase_amt = ($transaction_type == 1 && $receivePurpose != 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consAmount : 0;
			$rcv_loan = ($transaction_type == 1 && $receivePurpose == 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
			$rcv_loan_amt = ($transaction_type == 1 && $receivePurpose == 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consAmount : 0;
			$rcv_inside_return = ($transaction_type == 4 && $knitting_source == 1 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
			$rcv_inside_return_amt = ($transaction_type == 4 && $knitting_source == 1 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consAmount : 0;
			$rcv_outside_return = ($transaction_type == 4 && $knitting_source != 1 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
			$rcv_outside_return_amt = ($transaction_type == 4 && $knitting_source != 1 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consAmount : 0;
			$rcv_adjustment_qty = ($receive_basis == 30) ? $consQuantity : 0;
			$rcv_adjustment_amt = ($receive_basis == 30) ? $consAmount : 0;


			$receive_array[$row[csf("prod_id")]]['rcv_total_opening'] += $rcv_total_opening;
			$receive_array[$row[csf("prod_id")]]['rcv_total_opening_rate'] = $rcv_total_opening_rate;
			$receive_array[$row[csf("prod_id")]]['rcv_total_opening_amt'] += $rcv_total_opening_amt;
			$receive_array[$row[csf("prod_id")]]['rcv_total_opening_amt_usd'] += ($rcv_total_opening_amt / $exchange_rate);
			$receive_array[$row[csf("prod_id")]]['purchase'] += $purchase;
			$receive_array[$row[csf("prod_id")]]['purchase_amt'] += $purchase_amt;
			$receive_array[$row[csf("prod_id")]]['purchase_amt_usd'] += ($purchase_amt / $exchange_rate);
			$receive_array[$row[csf("prod_id")]]['rcv_loan'] += $rcv_loan;
			$receive_array[$row[csf("prod_id")]]['rcv_loan_amt'] += $rcv_loan_amt;
			$receive_array[$row[csf("prod_id")]]['rcv_loan_amt_usd'] += ($rcv_loan_amt / $exchange_rate);

			$receive_array[$row[csf("prod_id")]]['rcv_inside_return'] += $rcv_inside_return;
			$receive_array[$row[csf("prod_id")]]['rcv_inside_return_amt'] += $rcv_inside_return_amt;
			$receive_array[$row[csf("prod_id")]]['rcv_inside_return_amt_usd'] += ($rcv_inside_return_amt / $exchange_rate);

			$receive_array[$row[csf("prod_id")]]['rcv_outside_return'] += $rcv_outside_return;
			$receive_array[$row[csf("prod_id")]]['rcv_outside_return_amt'] += $rcv_outside_return_amt;
			$receive_array[$row[csf("prod_id")]]['rcv_outside_return_amt_usd'] += ($rcv_outside_return_amt / $exchange_rate);
			$receive_array[$row[csf("prod_id")]]['rcv_adjustment_qty'] += $rcv_adjustment_qty;
			$receive_array[$row[csf("prod_id")]]['rcv_adjustment_amt'] += $rcv_adjustment_amt;
			$receive_array[$row[csf("prod_id")]]['rcv_adjustment_amt_usd'] += ($rcv_adjustment_amt / $exchange_rate);

			$receive_array[$row[csf("prod_id")]]['store_id'][$row[csf("store_id")]] = $row[csf("store_id")];
			$receive_array[$row[csf("prod_id")]]['floor_id'][$row[csf("floor_id")]] = $row[csf("floor_id")];
			$receive_array[$row[csf("prod_id")]]['room'][$row[csf("room")]] = $row[csf("room")];
			$receive_array[$row[csf("prod_id")]]['rack'][$row[csf("rack")]] = $row[csf("rack")];
			$receive_array[$row[csf("prod_id")]]['self'][$row[csf("self")]] = $row[csf("self")];
			$receive_array[$row[csf("prod_id")]]['bin_box'][$row[csf("bin_box")]] = $row[csf("bin_box")];
			$receive_array[$row[csf("prod_id")]]['pay_mode'] = 0; //$row[csf("pay_mode")]; JAHID HASAN
			$receive_array[$row[csf("prod_id")]]['last_receive_date'][$row[csf("transaction_date")]] = $row[csf("transaction_date")];
			$receive_array[$row[csf("prod_id")]]['receive_basis'] = $row[csf("receive_basis")];
			$receive_array[$row[csf("prod_id")]]['remarks'] = $row[csf("remarks")];
			$receive_array[$row[csf("prod_id")]]['source'] = $row[csf("source")];

			if ($row[csf("weight_per_bag")] != "" && $row[csf("weight_per_bag")] > 0) {
				$receive_array[$row[csf("prod_id")]]['weight_per_bag'][$row[csf("weight_per_bag")]] = $row[csf("weight_per_bag")];
			}

			if ($row[csf("weight_per_cone")] != "" && $row[csf("weight_per_cone")] > 0) {
				$receive_array[$row[csf("prod_id")]]['weight_per_cone'][$row[csf("weight_per_cone")]] = $row[csf("weight_per_cone")];
			}

			if ($row[csf("buyer_id")] > 0) {
				$rcv_trans_buyer_arr[$row[csf("prod_id")]][$row[csf("trans_id")]] = $row[csf("buyer_id")];
			}


			/**
			 * Button Name : count & composition & lot
			 * Button ID : 19
			 * Button Description : count & composition & lot grouping summary
			 */
			if ($type == 19) {
				if ($get_upto == 1 && $txt_days > 0) {

					$oldStockFromDate = $fromDate;
					$oldStockToDate = strtotime("-$txt_days day", $toDate);

					$newStockFromDate = strtotime("+1 day", $oldStockToDate); // Greater than
					$newStockToDate = $toDate;
					//echo date("d-m-Y", $oldStockFromDate) . "==" . date("d-m-Y", $oldStockToDate) . "<br>";
					//echo date("d-m-Y", $newStockFromDate) . "==" . date("d-m-Y", $newStockToDate) . "<br>";

					if ($oldStockFromDate != "" && $oldStockToDat) { // Old Stock
						$fromDate = $oldStockFromDate;
						$toDate = $oldStockToDat;

						$rcv_total_opening = ($transaction_date < $fromDate) ? $consQuantity : 0;
						$purchase = ($transaction_type == 1 && $receivePurpose != 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$rcv_loan = ($transaction_type == 1 && $receivePurpose == 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$rcv_inside_return = ($transaction_type == 4 && $knitting_source == 1 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$rcv_outside_return = ($transaction_type == 4 && $knitting_source != 1 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$rcv_adjustment_qty = ($receive_basis == 30) ? $consQuantity : 0;

						$receive_array[$row[csf("prod_id")]]['rcv_total_opening_old_stock'] += $rcv_total_opening;
						$receive_array[$row[csf("prod_id")]]['purchase_old_stock'] += $purchase;
						$receive_array[$row[csf("prod_id")]]['rcv_loan_old_stock'] += $rcv_loan;
						$receive_array[$row[csf("prod_id")]]['rcv_inside_return_old_stock'] += $rcv_inside_return;
						$receive_array[$row[csf("prod_id")]]['rcv_outside_return_old_stock'] += $rcv_outside_return;
						$receive_array[$row[csf("prod_id")]]['rcv_adjustment_qty_old_stock'] += $rcv_adjustment_qty;
					}

					if ($newStockFromDate != "" && $newStockToDate) { // New Stock
						$fromDate = $newStockFromDate;
						$toDate = $newStockToDate;

						$rcv_total_opening = ($transaction_date < $fromDate) ? $consQuantity : 0;
						$purchase = ($transaction_type == 1 && $receivePurpose != 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$rcv_loan = ($transaction_type == 1 && $receivePurpose == 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$rcv_inside_return = ($transaction_type == 4 && $knitting_source == 1 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$rcv_outside_return = ($transaction_type == 4 && $knitting_source != 1 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$rcv_adjustment_qty = ($receive_basis == 30) ? $consQuantity : 0;

						$receive_array[$row[csf("prod_id")]]['rcv_total_opening_new_stock'] += $rcv_total_opening;
						$receive_array[$row[csf("prod_id")]]['purchase_new_stock'] += $purchase;
						$receive_array[$row[csf("prod_id")]]['rcv_loan_new_stock'] += $rcv_loan;
						$receive_array[$row[csf("prod_id")]]['rcv_inside_return_new_stock'] += $rcv_inside_return;
						$receive_array[$row[csf("prod_id")]]['rcv_outside_return_new_stock'] += $rcv_outside_return;
						$receive_array[$row[csf("prod_id")]]['rcv_adjustment_qty_new_stock'] += $rcv_adjustment_qty;
					}
				}
			}
		}

		//echo "<pre>"; print_r($receive_array); die;
		unset($result_sql_receive);

		if ($type == 18) {
			$inside_issue_total = 0;
			$outside_issue_total = 0;
			$dyeing_issue_total = 0;
			$adjust_issue_total = 0;
			$receive_return_total = 0;
			$sql_issue_sum = "select a.prod_id,a.receive_basis,a.store_id as store_id,a.transaction_date,a.transaction_type,a.cons_quantity,$vs_select_consrate_amount,c.knit_dye_source,c.issue_purpose,c.entry_form, a.floor_id, a.room,a.rack, a.self, a.bin_box from inv_transaction a, inv_issue_master c, product_details_master d where a.mst_id=c.id and a.prod_id = d.id and a.item_category=1 and a.transaction_type IN (2, 3) and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond $search_cond2 $company_cond
			 union all select a.prod_id,a.receive_basis,a.store_id,a.transaction_date,a.transaction_type,a.cons_quantity,$vs_select_consrate_amount,0 as knit_dye_source,0 as issue_purpose,0 as entry_form, a.floor_id, a.room,a.rack, a.self, a.bin_box from inv_transaction a where a.item_category=1 and a.transaction_type IN (2, 3) AND a.transaction_date between '" . $from_date . "' and '" . $to_date . "' and a.receive_basis=30  and a.status_active=1 and a.is_deleted=0 $store_cond $company_cond";
			//  echo $sql_issue_sum; die;
			$result_sql_issue_sum = sql_select($sql_issue_sum);
			foreach ($result_sql_issue_sum as $row) {
				if ($row['TRANSACTION_TYPE'] == 2 && $row['ISSUE_PURPOSE'] == 2) //dyeing purpose issue
				{
					$dyeing_issue_total += $row['CONS_QUANTITY'];
				}
				if ($row['TRANSACTION_TYPE'] == 2 && $row['ISSUE_PURPOSE'] == 30) //adjust purpose issue
				{
					$adjust_issue_total += $row['CONS_QUANTITY'];
				}
				if ($row['TRANSACTION_TYPE'] == 3) //total receive return
				{
					$receive_return_total += $row['CONS_QUANTITY'];
				}
				if ($row['TRANSACTION_TYPE'] == 2 && $row['KNIT_DYE_SOURCE'] == 1) //inside total issue
				{
					$inside_issue_total += $row['CONS_QUANTITY'];
				}
				if ($row['TRANSACTION_TYPE'] == 2 && $row['KNIT_DYE_SOURCE'] == 3 && $row['ISSUE_PURPOSE'] != 2) //outside total issue 

				{
					$outside_issue_total += $row['CONS_QUANTITY'];
				}
			}
		}
		// echo $sql_issue_sum; die;
		$issue_array = array();
		$sql_issue = "select a.company_id,a.prod_id,a.receive_basis,a.store_id as store_id,a.transaction_date,a.transaction_type,a.cons_quantity,$vs_select_consrate_amount,c.knit_dye_source,c.issue_purpose,c.entry_form, a.floor_id, a.room,a.rack, a.self, a.bin_box from inv_transaction a, inv_issue_master c where a.mst_id=c.id and a.item_category=1 and a.transaction_type IN (2, 3) and a.transaction_date <= '" . $to_date . "' and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond union all select a.company_id,a.prod_id,a.receive_basis,a.store_id,a.transaction_date,a.transaction_type,a.cons_quantity,$vs_select_consrate_amount,0 as knit_dye_source,0 as issue_purpose,0 as entry_form, a.floor_id, a.room,a.rack, a.self, a.bin_box from inv_transaction a where a.item_category=1 and a.transaction_type IN (2, 3) AND a.transaction_date <= '" . $to_date . "' and a.receive_basis=30  and a.status_active=1 and a.is_deleted=0 $store_cond";
		//echo $sql_issue;
		//die;

		$result_sql_issue = sql_select($sql_issue);
		foreach ($result_sql_issue as $row) {

			$transCompanyId = $row[csf("company_id")];
			$transDate = date("d-m-Y", strtotime($row[csf("transaction_date")]));
			$exchange_rate = ($txt_excange_rate != "") ? $txt_excange_rate : $conversion_data_arr[$transCompanyId][$transDate];

			$transaction_date = strtotime($row[csf("transaction_date")]);
			$transaction_type = $row[csf("transaction_type")];
			$entryForm = $row[csf("entry_form")];
			$consQuantity = $row[csf("cons_quantity")];
			$consAmount = $row[csf("cons_amount")];
			$consRate = $row[csf("cons_rate")];
			$receive_basis = $row[csf("receive_basis")];
			$issuePurpose = $row[csf("issue_purpose")];
			$knitdyeSource = $row[csf("knit_dye_source")];

			$issue_total_opening_rate = ($transaction_date < $fromDate) ? $consRate : 0;
			$issue_total_opening = ($transaction_date < $fromDate) ? $consQuantity : 0;
			$issue_total_opening_amt = ($transaction_date < $fromDate) ? $consAmount : 0;

			$issue_inside = ($transaction_type == 2 && $knitdyeSource == 1 && $issuePurpose != 5 &&  (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
			$issue_inside_amt = ($transaction_type == 2 && $knitdyeSource == 1 && $issuePurpose != 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consAmount : 0;
			$issue_outside = ($transaction_type == 2 && $knitdyeSource != 1 && $issuePurpose != 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
			$issue_outside_amt = ($transaction_type == 2 && $knitdyeSource != 1 && $issuePurpose != 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consAmount : 0;
			$rcv_return = ($transaction_type == 3 && $entryForm = 8 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
			$rcv_return_amt = ($transaction_type == 3 && $entryForm = 8 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consAmount : 0;
			$issue_loan = ($transaction_type == 2 && $issuePurpose == 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
			$issue_loan_amt = ($transaction_type == 2 && $issuePurpose == 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consAmount : 0;
			$issue_adjustment_qty = ($receive_basis == 30 && ($transaction_date <= $toDate)) ? $consQuantity : 0;
			$issue_adjustment_amt = ($receive_basis == 30 && ($transaction_date <= $toDate)) ? $consAmount : 0;

			$issue_array[$row[csf("prod_id")]]['issue_total_opening_rate'] = $issue_total_opening_rate;
			$issue_array[$row[csf("prod_id")]]['issue_total_opening'] += $issue_total_opening;
			$issue_array[$row[csf("prod_id")]]['issue_total_opening_amt'] += $issue_total_opening_amt;
			$issue_array[$row[csf("prod_id")]]['issue_total_opening_amt_usd'] += ($issue_total_opening_amt / $exchange_rate);
			$issue_array[$row[csf("prod_id")]]['issue_inside'] += $issue_inside;
			$issue_array[$row[csf("prod_id")]]['issue_inside_amt'] += $issue_inside_amt;
			$issue_array[$row[csf("prod_id")]]['issue_inside_amt_usd'] += ($issue_inside_amt / $exchange_rate);
			$issue_array[$row[csf("prod_id")]]['issue_outside'] += $issue_outside;
			$issue_array[$row[csf("prod_id")]]['issue_outside_amt'] += $issue_outside_amt;
			$issue_array[$row[csf("prod_id")]]['issue_outside_amt_usd'] += ($issue_outside_amt / $exchange_rate);
			$issue_array[$row[csf("prod_id")]]['rcv_return'] += $rcv_return;
			$issue_array[$row[csf("prod_id")]]['rcv_return_amt'] += $rcv_return_amt;
			$issue_array[$row[csf("prod_id")]]['rcv_return_amt_usd'] += ($rcv_return_amt / $exchange_rate);
			$issue_array[$row[csf("prod_id")]]['issue_loan'] += $issue_loan;
			$issue_array[$row[csf("prod_id")]]['issue_loan_amt'] += $issue_loan_amt;
			$issue_array[$row[csf("prod_id")]]['issue_loan_amt_usd'] += ($issue_loan_amt / $exchange_rate);
			$issue_array[$row[csf("prod_id")]]['issue_adjustment_qty'] += $row[csf("issue_adjustment_qty")];
			$issue_array[$row[csf("prod_id")]]['issue_adjustment_amt'] += $row[csf("issue_adjustment_amt")];
			$issue_array[$row[csf("prod_id")]]['issue_adjustment_amt_usd'] += ($row[csf("issue_adjustment_amt")] / $exchange_rate);

			$issue_array[$row[csf("prod_id")]]['store_id'][$row[csf("store_id")]] = $row[csf("store_id")];
			$issue_array[$row[csf("prod_id")]]['floor_id'][$row[csf("floor_id")]] = $row[csf("floor_id")];
			$issue_array[$row[csf("prod_id")]]['room'][$row[csf("room")]] = $row[csf("room")];
			$issue_array[$row[csf("prod_id")]]['rack'][$row[csf("rack")]] = $row[csf("rack")];
			$issue_array[$row[csf("prod_id")]]['self'][$row[csf("self")]] = $row[csf("self")];
			$issue_array[$row[csf("prod_id")]]['bin_box'][$row[csf("bin_box")]] = $row[csf("bin_box")];
			$issue_array[$row[csf("prod_id")]]['last_issue_date'][$row[csf("transaction_date")]] = $row[csf("transaction_date")];


			/**
			 * Button Name : count & composition & lot
			 * Button ID : 19
			 * Button Description : count & composition & lot grouping summary
			 */
			if ($type == 19) {
				if ($get_upto == 1 && $txt_days > 0) {
					$oldStockFromDate = $fromDate;
					$oldStockToDate = strtotime("-$txt_days day", $toDate);

					$newStockFromDate = strtotime("+1 day", $oldStockToDate); // Greater than
					$newStockToDate = $toDate;
					//echo date("d-m-Y", $oldStockFromDate) . "==" . date("d-m-Y", $oldStockToDate) . "<br>";
					//echo date("d-m-Y", $currentStockFromDate) . "==" . date("d-m-Y", $currentStockToDate);

					if ($oldStockFromDate != "" && $oldStockToDat) { // Old Stock
						$fromDate = $oldStockFromDate;
						$toDate = $oldStockToDat;

						$issue_total_opening = ($transaction_date < $fromDate) ? $consQuantity : 0;
						$issue_inside = ($transaction_type == 2 && $knitdyeSource == 1 && $issuePurpose != 5 &&  (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$issue_outside = ($transaction_type == 2 && $knitdyeSource != 1 && $issuePurpose != 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$rcv_return = ($transaction_type == 3 && $entryForm = 8 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$issue_loan = ($transaction_type == 2 && $issuePurpose == 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$issue_adjustment_qty = ($receive_basis == 30 && ($transaction_date <= $toDate)) ? $consQuantity : 0;

						$issue_array[$row[csf("prod_id")]]['issue_total_opening_old_stock'] += $issue_total_opening;
						$issue_array[$row[csf("prod_id")]]['issue_inside_old_stock'] += $issue_inside;
						$issue_array[$row[csf("prod_id")]]['issue_outside_old_stock'] += $issue_outside;
						$issue_array[$row[csf("prod_id")]]['rcv_return_old_stock'] += $rcv_return;
						$issue_array[$row[csf("prod_id")]]['issue_loan_old_stock'] += $issue_loan;
						$issue_array[$row[csf("prod_id")]]['issue_adjustment_qty_old_stock'] += $row[csf("issue_adjustment_qty")];
					}

					if ($newStockFromDate != "" && $newStockToDate) { // New Stock
						$fromDate = $newStockFromDate;
						$toDate = $newStockToDate;

						$issue_total_opening = ($transaction_date < $fromDate) ? $consQuantity : 0;
						$issue_inside = ($transaction_type == 2 && $knitdyeSource == 1 && $issuePurpose != 5 &&  (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$issue_outside = ($transaction_type == 2 && $knitdyeSource != 1 && $issuePurpose != 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$rcv_return = ($transaction_type == 3 && $entryForm = 8 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$issue_loan = ($transaction_type == 2 && $issuePurpose == 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$issue_adjustment_qty = ($receive_basis == 30 && ($transaction_date <= $toDate)) ? $consQuantity : 0;

						$issue_array[$row[csf("prod_id")]]['issue_total_opening_new_stock'] += $issue_total_opening;
						$issue_array[$row[csf("prod_id")]]['issue_inside_new_stock'] += $issue_inside;
						$issue_array[$row[csf("prod_id")]]['issue_outside_new_stock'] += $issue_outside;
						$issue_array[$row[csf("prod_id")]]['rcv_return_new_stock'] += $rcv_return;
						$issue_array[$row[csf("prod_id")]]['issue_loan_new_stock'] += $issue_loan;
						$issue_array[$row[csf("prod_id")]]['issue_adjustment_qty_new_stock'] += $row[csf("issue_adjustment_qty")];
					}
				}
			}
		}
		//echo "<pre>"; print_r($issue_array); die;
		unset($result_sql_issue);


		if ($store_wise == 1) {
			$trans_criteria_cond = "";
		} else {
			$trans_criteria_cond = " and c.transfer_criteria=1"; // transfer_criteria : Company to company
		}

		$transfer_qty_array = array();
		$sql_transfer = "select a.id as trans_id,a.company_id,a.prod_id,a.buyer_id,a.transaction_type,a.transaction_date,a.cons_quantity,$vs_select_consrate_amount from inv_transaction a, inv_item_transfer_mst c where a.mst_id=c.id and a.transaction_type in (5,6) and a.item_category=1 AND a.transaction_date <= '" . $to_date . "' and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $trans_criteria_cond $store_cond";
		//echo $sql_transfer;
		//die();

		$result_sql_transfer = sql_select($sql_transfer);
		$checkedProdBuyerArr = array();
		foreach ($result_sql_transfer as $transRow) {

			$transCompanyId = $transRow[csf("company_id")];
			$transDate = date("d-m-Y", strtotime($transRow[csf("transaction_date")]));
			$exchange_rate = ($txt_excange_rate != "") ? $txt_excange_rate : $conversion_data_arr[$transCompanyId][$transDate];

			$transaction_date = strtotime($transRow[csf("transaction_date")]);
			$transaction_type = $transRow[csf("transaction_type")];
			$consQuantity = $transRow[csf("cons_quantity")];
			$consAmount = $transRow[csf("cons_amount")];
			$consRate = $rotransRoww[csf("cons_rate")];

			$trans_out_total_opening = ($transaction_type == 6 && $transaction_date < $fromDate) ? $consQuantity : 0;
			$trans_out_total_opening_amt = ($transaction_type == 6 && $transaction_date < $fromDate) ? $consAmount : 0;
			$trans_out_total_opening_rate = ($transaction_type == 6 && $transaction_date < $fromDate) ? $consRate : 0;
			$trans_in_total_opening = ($transaction_type == 5 && $transaction_date < $fromDate) ? $consQuantity : 0;
			$trans_in_total_opening_amt = ($transaction_type == 5 && $transaction_date < $fromDate) ? $consAmount : 0;
			$trans_in_total_opening_rate = ($transaction_type == 5 && $transaction_date < $fromDate) ? $consRate : 0;
			$transfer_out_qty = ($transaction_type == 6 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
			$transfer_out_amt = ($transaction_type == 6 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consAmount : 0;
			$transfer_in_qty = ($transaction_type == 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
			$transfer_in_amt = ($transaction_type == 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consAmount : 0;

			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening'] += $trans_out_total_opening;
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_amt'] += $trans_out_total_opening_amt;
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_amt_usd'] += ($trans_out_total_opening_amt / $exchange_rate);
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_rate'] += $trans_in_total_opening_rate;

			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening'] += $trans_in_total_opening;
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_amt'] += $trans_in_total_opening_amt;
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_amt_usd'] += ($trans_in_total_opening_amt / $exchange_rate);
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_rate'] += $trans_out_total_opening_rate;

			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_qty'] += $transfer_out_qty;
			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_amt'] += $transfer_out_amt;
			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_amt_usd'] += ($transfer_out_amt / $exchange_rate);
			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_qty'] += $transfer_in_qty;
			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_amt'] += $transfer_in_amt;
			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_usd'] += ($transfer_in_amt / $exchange_rate);

			if ($transRow[csf("buyer_id")] > 0) {
				$rcv_trans_buyer_arr[$transRow[csf("prod_id")]][$transRow[csf("trans_id")]] = $transRow[csf("buyer_id")];
			}

			/**
			 * Button Name : count & composition & lot
			 * Button ID : 19
			 * Button Description : count & composition & lot grouping summary
			 */
			if ($type == 19) {
				if ($get_upto == 1 && $txt_days > 0) {
					$oldStockFromDate = $fromDate;
					$oldStockToDate = strtotime("-$txt_days day", $toDate);

					$newStockFromDate = strtotime("+1 day", $oldStockToDate); // Greater than
					$newStockToDate = $toDate;
					//echo date("d-m-Y", $oldStockFromDate) . "==" . date("d-m-Y", $oldStockToDate) . "<br>";
					//echo date("d-m-Y", $currentStockFromDate) . "==" . date("d-m-Y", $currentStockToDate);

					if ($oldStockFromDate != "" && $oldStockToDat) { // Old Stock
						$fromDate = $oldStockFromDate;
						$toDate = $oldStockToDat;

						$trans_out_total_opening = ($transaction_type == 6 && $transaction_date < $fromDate) ? $consQuantity : 0;
						$trans_in_total_opening = ($transaction_type == 5 && $transaction_date < $fromDate) ? $consQuantity : 0;
						$transfer_out_qty = ($transaction_type == 6 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$transfer_in_qty = ($transaction_type == 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;

						$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_old_stock'] += $trans_out_total_opening;
						$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_old_stock'] += $trans_in_total_opening;
						$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_qty_old_stock'] += $transfer_out_qty;
						$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_qty_old_stock'] += $transfer_in_qty;
					}

					if ($newStockFromDate != "" && $newStockToDate) { // New Stock
						$fromDate = $newStockFromDate;
						$toDate = $newStockToDate;

						$trans_out_total_opening = ($transaction_type == 6 && $transaction_date < $fromDate) ? $consQuantity : 0;
						$trans_in_total_opening = ($transaction_type == 5 && $transaction_date < $fromDate) ? $consQuantity : 0;
						$transfer_out_qty = ($transaction_type == 6 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;
						$transfer_in_qty = ($transaction_type == 5 && (($fromDate <= $transaction_date) && ($toDate >= $transaction_date))) ? $consQuantity : 0;

						$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_new_stock'] += $trans_out_total_opening;
						$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_new_stock'] += $trans_in_total_opening;
						$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_qty_new_stock'] += $transfer_out_qty;
						$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_qty_new_stock'] += $transfer_in_qty;
					}
				}
			}
		}
		unset($result_sql_transfer);

		//echo "<pre>";
		//print_r($rcv_trans_buyer_arr);

		unset($result_sql_transfer);

		if ($db_type == 0) {
			$yarn_allo_sql = sql_select("select product_id, group_concat(buyer_id) as buyer_id, group_concat(allocate_qnty) as allocate_qnty from com_balk_yarn_allocate where status_active=1 group by product_id");
		} else if ($db_type == 2) {
			$yarn_allo_sql = sql_select("select product_id, LISTAGG(CAST(buyer_id as VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY buyer_id) as buyer_id, LISTAGG(CAST(allocate_qnty AS VARCHAR(4000)),',') WITHIN GROUP(ORDER BY allocate_qnty) as allocate_qnty from com_balk_yarn_allocate where status_active=1 group by product_id");
		}
		$yarn_allo_arr = array();
		foreach ($yarn_allo_sql as $row) {
			$yarn_allo_arr[$row[csf("product_id")]]['product_id'] = $row[csf("product_id")];
			$yarn_allo_arr[$row[csf("product_id")]]['buyer_id'] = implode(",", array_unique(explode(",", $row[csf("buyer_id")])));
			$yarn_allo_arr[$row[csf("product_id")]]['allocate_qnty'] = implode(",", array_unique(explode(",", $row[csf("allocate_qnty")])));
		}

		unset($yarn_allo_sql);

		if ($type == 1) {
		?>
			<style type="text/css">
				.wrap_break {
					word-wrap: break-word;
					word-break: break-all;
				}
			</style>
			<?
			$date_array = array();
			if ($store_name != 0)  $store_cond = " and store_id in($store_name) ";
			if ($cbo_company_name != 0)  $company_cond = " and company_id=$cbo_company_name ";
			$returnRes_date = "select prod_id, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 $company_cond $store_cond group by prod_id";
			$result_returnRes_date = sql_select($returnRes_date);
			foreach ($result_returnRes_date as $row) {
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
			}

			//## bellow query based on rcv transaction balance quantity in transaction table
			$age_sql = "select PROD_ID, min(transaction_date) as MIN_DATE from inv_transaction where is_deleted=0 and status_active=1 and transaction_type in(1,4,5) and BALANCE_QNTY>0 and item_category=1 $company_cond $store_cond group by prod_id";
			$age_sql_result = sql_select($age_sql);
			foreach ($age_sql_result as $row) {
				$date_array[$row["PROD_ID"]]['min_date'] = $row["MIN_DATE"];
			}
			unset($age_sql_result);

			/*if ($source_name > 0)
			{
				$prodIds_cond = "  ".where_con_using_array($prodIdsArr,0,'a.id')." ";
			}
			else
			{
				$prodIds_cond="";
			}*/

			$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group,a.brand
			from product_details_master a
			where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond  group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group,a.brand order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id"; //$prodIds_cond
			//echo $sql;die;
			$result = sql_select($sql);
			$all_prod_id_arr = array();
			foreach ($result as $row) {
				if ($productIdChk[$row[csf('id')]] == "") {
					$productIdChk[$row[csf('id')]] = $row[csf('id')];
					$all_prod_id_arr[$row[csf("id")]] = $row[csf("id")];
				}
			}
			$all_prod_id_arr = array_filter($all_prod_id_arr);
			//var_dump($all_prod_id_arr);die;
			if (!empty($all_prod_id_arr)) {
				$con = connect();
				execute_query("DELETE FROM TMP_PROD_ID WHERE USERID = " . $user_id . "");
				oci_commit($con);

				$con = connect();
				foreach ($all_prod_id_arr as $prodId) {
					execute_query("INSERT INTO TMP_PROD_ID(PROD_ID,USERID) VALUES(" . $prodId . ", " . $user_id . ")");
					oci_commit($con);
				}
			}
			//die;
			$data_file = sql_select("SELECT a.prod_id, b.image_location, b.master_tble_id from inv_yarn_test_mst a, common_photo_library b, tmp_prod_id c where a.id=b.master_tble_id and a.prod_id=c.prod_id and c.userid=$user_id and b.form_name='yarn_test' and b.is_deleted=0 and b.file_type=2");
			$file_arr = array();
			foreach ($data_file as $row) {
				$file_arr[$row[csf("prod_id")]]['file'] = $row[csf("image_location")];
			}

			unset($data_file);

			$yarnTestQalityCommentsArr = return_library_array("SELECT a.prod_id as prod_id, b.comments_author as comments_author from inv_yarn_test_mst a, inv_yarn_test_comments b, tmp_prod_id c where a.id=b.mst_table_id and a.prod_id=c.prod_id and c.userid=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.comments_author IS NOT NULL", 'prod_id', 'comments_author');


			$mrr_rate_sql = sql_select("select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction
				where status_active=1 and is_deleted=0 and item_category=1 and transaction_type in(1,4,5) group by prod_id");
			$mrr_rate_arr = array();
			foreach ($mrr_rate_sql as $row) {
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
				$mrr_rate_arr[$row[csf("prod_id")]] = $row[csf("cons_amount")] / $row[csf("cons_quantiy")];
				$total_rcv_qty[$row[csf("prod_id")]] = $row[csf("cons_quantiy")];
			}

			//echo "<pre>";
			//print_r($mrr_rate_arr);

			$r_id111 = execute_query("DELETE FROM TMP_PROD_ID WHERE USERID=$user_id ");
			if ($r_id111) {
				oci_commit($con);
			}
			?>
			<div>
				<table width="<? echo $table_width + 20; ?>" border="1" style="font:'Arial Narrow';" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<tr class="form_caption" style="border:none;">
						<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:16px; font-weight:bold">Daily Yarn Stock </td>
					</tr>
					<tr style="border:none;">
						<td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:14px;">
							Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
						</td>
					</tr>
				</table>
				<table width="<? echo $table_width + 80; ?>" border="1" style="font:'Arial Narrow';" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
					<thead>
						<tr>
							<th rowspan="2" width="30">SL</th>
							<th rowspan="2" width="130">Company</th>
							<th colspan="12">Description</th>
							<th rowspan="2" width="100">Wgt. Bag/Cone</th>
							<th rowspan="2" width="100">Source</th>
							<th rowspan="2" width="110">OpeningStock</th>
							<th colspan="6">Receive</th>
							<th colspan="6">Delivery</th>
							<th rowspan="2" width="100">Stock InHand</th>
							<?
							echo $column;
							if ($store_wise == 1) {
								echo '<th rowspan="2" width="100">Store Name</th>';
							} else {
								echo '<th rowspan="2" width="100">Allocated to Order </th>';
								echo '<th rowspan="2" width="100">Un Allocated Qty.</th>';
							}
							?>
							<th rowspan="2" width="50">Age (Days)</th>
							<th rowspan="2" width="50">DOH</th>
							<th rowspan="2" width="140">Yarn Quality<br>Comments</th>
							<th rowspan="2">Remarks</th>
						</tr>
						<tr>
							<th width="60">Prod.ID</th>
							<th width="60">View File</th>
							<th width="60">Count</th>
							<th width="150">Composition</th>
							<th width="100">Yarn Type</th>
							<th width="120">Color</th>
							<th width="100">Last Receive Date</th>
							<th width="100">Last Issue Date</th>
							<th width="100">Total Received Qty</th>
							<th width="100">Lot</th>
							<th width="100">Supplier</th>
							<th width="100">Brand</th>
							<th width="90">Purchase</th>
							<th width="90">Inside Return</th>
							<th width="90">Outside Return</th>
							<th width="90">Transfer In</th>
							<th width="90">Loan</th>
							<th width="100">Total Recv</th>
							<th width="90">Inside</th>
							<th width="90">Outside</th>
							<th width="90">Recv. Return</th>
							<th width="90">Trans. Out</th>
							<th width="90">Loan</th>
							<th width="100">Total Delivery</th>
						</tr>
					</thead>
				</table>
				<div style="width:<? echo $table_width + 98; ?>px; overflow-y:scroll; max-height:350px" id="scroll_body">
					<table style="width:<? echo $table_width + 80; ?>px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$i = 1;
						$tot_stock_value = 0;
						foreach ($result as $row) {
							$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
							$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));

							$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
							if ($row[csf("yarn_comp_type2nd")] != 0)
								$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";

							$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
							$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
							$transfer_in_amt = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
							$transfer_out_amt = $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];


							$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
							$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];
							$trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];
							$trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];

							$pay_mode = $receive_array[$row[csf("id")]]['pay_mode'];
							$receive_basis = $receive_array[$row[csf("id")]]['receive_basis'];

							$openingbalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);

							$openingBalanceAmt = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt);

							$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $receive_array[$row[csf("id")]]['rcv_adjustment_qty'] + $transfer_in_qty;

							$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $issue_array[$row[csf("id")]]['issue_adjustment_qty'] + $transfer_out_qty;

							$totalRcvAmt = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $receive_array[$row[csf("id")]]['rcv_adjustment_amt'] + $transfer_in_amt;

							$totalIssueAmt = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] + $issue_array[$row[csf("id")]]['issue_loan_amt'] + $issue_array[$row[csf("id")]]['issue_adjustment_amt'] + $transfer_out_qty;


							$stockInHand = $openingBalance + $totalRcv - $totalIssue;

							$stockInHandAmt = $openingBalanceAmt + $totalRcvAmt - $totalIssueAmt;

							//subtotal and group-----------------------
							$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];

							if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0)) {
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								//for supplier
								if ($row[csf('is_within_group')] == 1) {
									$supplier_name = $companyArr[$row[csf('supplier_id')]];
								} else {
									$supplier_name = $supplierArr[$row[csf('supplier_id')]];
								}
								//end for supplier

								if ($value_with == 1) {
									if (number_format($stockInHand, 2) > 0.00) {
										if (!in_array($check_string, $checkArr)) {
											$checkArr[$i] = $check_string;
											if ($i > 1) {
						?>
												<tr bgcolor="#CCCCCC" style="font-weight:bold">
													<td colspan="16" align="right">Sub Total</td>
													<td width="110" align="right" class="wrap_break"><? echo number_format($total_opening_balance, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_purchase, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_inside_return, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_outside_return, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_in_qty, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_rcv_loan, 2); ?></td>
													<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_rcv, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_inside, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_outside, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_receive_return, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_out_qty, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_loan, 2); ?></td>
													<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_delivery, 2); ?></td>
													<td width="100" align="right" class="wrap_break"><? echo number_format($total_stock_in_hand, 4); ?></td>
													<?
													if ($show_val_column == 1) {
														echo '<td width="90" align="right" >&nbsp;</td>';
														echo '<td width="110" align="right" class="wrap_break">' . number_format($sub_stock_value, 2) . '</td>';
														echo '<td width="100" align="right">&nbsp;</td>';
														echo '<td width="100" align="right" class="wrap_break">' . number_format($sub_stock_value_usd, 2) . '</td>';
													}

													if ($store_wise == 1) {
														echo '<td width="100">&nbsp;</td>';
													} else {
														if ($allocated_qty_variable_settings == 1) {
															echo '<td width="100"  class="wrap_break" align="right">' . number_format($total_alocatted, 2) . '</td>';
															echo '<td width="100" class="wrap_break" align="right">' . number_format($total_free_stock, 2) . '</td>';
														} else {
															echo '<td width="100" align="right">&nbsp;</td>';
															echo '<td width="100" align="right">&nbsp;</td>';
														}
													}
													?>
													<td width="50" align="right">&nbsp;</td>
													<td width="50" align="right">&nbsp;</td>
													<td align="right">&nbsp;</td>
													<td width="" align="right">&nbsp;</td>
												</tr>
										<?
												$total_opening_balance = 0;
												$total_purchase = 0;
												$total_inside_return = 0;
												$total_outside_return = 0;
												$total_rcv_loan = 0;
												$total_total_rcv = 0;
												$total_issue_inside = 0;
												$total_issue_outside = 0;
												$total_receive_return = 0;
												$total_issue_loan = 0;
												$total_total_delivery = 0;
												$total_stock_in_hand = 0;
												$total_alocatted = 0;
												$total_free_stock = 0;
												$sub_stock_value = 0;
												$sub_stock_value_usd = 0;
												$total_transfer_out_qty = 0;
												$total_transfer_in_qty = 0;
											}
										}
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30"><? echo $i; ?></td>
											<td width="130" class="wrap_break">
												<p><? echo $companyArr[$row[csf("company_id")]]; ?></p>
											</td>
											<td width="60"><? echo $row[csf("id")]; ?></td>
											<td width="60" align="center">
												<p>
													<a href="javascript:void()" onClick="downloiadFile('<?= $row[csf("id")]; ?>');">
														<? if ($file_arr[$row[csf("id")]]['file'] != '') echo 'View File'; ?></a>
												</p>
											</td>
											<td width="60" class="wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
											<td width="150" class="wrap_break"><? echo $compositionDetails; ?></td>
											<td width="100" class="wrap_break"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
											<td width="120" class="wrap_break"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
											<td width="100">
												<p><? echo max($receive_array[$row[csf("id")]]["last_receive_date"]); ?></p>
											</td>
											<td width="100">
												<p><? echo max($issue_array[$row[csf("id")]]["last_issue_date"]); ?></p>
											</td>
											<td width="100">
												<p><? echo $totalRcv; ?></p>
											</td>
											<td width="100">
												<p>
													<?
													if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
														<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?></a>
													<? } else if ($yarnTestArr[$row[csf("id")]] != "") { ?>
														<a href='##' onclick="show_test_report2('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?></a>
													<? } else {
														echo $row[csf("lot")];
													} ?>
												</p>
											</td>
											<td width="100" class="wrap_break">
												<?
												echo $supplier_name;
												?>
											</td>
											<td width="100" class="wrap_break"><? echo $brandArr[$row[csf("brand")]]; ?></td>
											<td width="100" class="wrap_break"><? echo 'Bg:' . max($receive_array[$row[csf("id")]]['weight_per_bag']) . '; ' . 'Cn:' . max($receive_array[$row[csf("id")]]['weight_per_cone']); ?></td>
											<td width="100" class="wrap_break"><? echo $source[$receive_array[$row[csf("id")]]['source']]; ?></td>
											<td width="110" class="wrap_break" align="right"><? echo number_format($openingBalance, 2); ?></td>
											<td width="90" class="wrap_break" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break">
												<?
												if ($store_wise == 1) {
													echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
												} else {
													echo number_format($transfer_in_qty, 2);
												}
												?>
											</td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
											<td width="100" align="right" class="wrap_break"><? echo number_format($totalRcv, 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break">
												<?
												if ($store_wise == 1) {
													echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
												} else {
													echo number_format($transfer_out_qty, 2);
												}
												?>
											</td>
											<td width="90" class="wrap_break" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
											<td width="100" align="right" class="wrap_break"><? echo number_format($totalIssue, 2); ?></td>

											<td width="100" align="right" class="wrap_break"><a href='##' onclick="openmypage_stock_upto('<? echo $row[csf('id')] . "_" . $store_name . "_" . $row[csf('company_id')]; ?>', 'stock_popup_store_methode_upto')"><? echo number_format($stockInHand, 4); ?></a></td>

											<?

											$stock_value = $stock_value_usd = 0;
											if ($show_val_column == 1) {

												$avg_rate = 0;
												if (number_format($stockInHandAmt, 2) > 0 && number_format($stockInHand, 2) > 0) $avg_rate = $stockInHandAmt / $stockInHand;

												if ($avg_rate > 0) {
													$avg_rate = $avg_rate;
												} else {
													$avg_rate = "0.00";
												}

												if (number_format($stockInHand, 4) > 0) {
													$stock_value = $stockInHand * $avg_rate;
													if (number_format($stock_value, 2) > 0 && number_format($txt_excange_rate, 2) > 0) {
														$stock_value_usd = $stock_value / $txt_excange_rate;
													} else {
														$stock_value_usd = "0.00";
													}
												}

												$avz_rates_usd = 0;

												if (number_format($avg_rate, 2) > 0 && number_format($txt_excange_rate, 2) > 0) {
													$avz_rates_usd = $avg_rate / $txt_excange_rate;
												} else {
													$avz_rates_usd = "0.00";
												}

												echo '<td width="90" align="right" class="wrap_break">' . number_format($avg_rate, 2) . '</td>';
												echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
												echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
												echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
											}

											if ($store_wise == 1) {
												$store_name = '';
												//$receive_store_id = explode(",", $receive_array[$row[csf("id")]]['store_id']);
												//$issue_store_id = explode(",", $issue_array[$row[csf("id")]]['store_id']);
												$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
												$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];

												$store_id_arr = array_unique(array_merge($receive_store_id, $issue_store_id));
												foreach ($store_id_arr as $key => $val) {
													if ($store_name == "")
														$store_name = $store_arr[$val];
													else
														$store_name .= ", " . $store_arr[$val];
												}
												echo '<td width="100" class="wrap_break">' . $store_name . '</td>';
											} else {
												if ($allocated_qty_variable_settings == 1) {
													echo "<td width='100' align='right' class='wrap_break'><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
													echo '<td width="100" align="right" class="wrap_break">' . number_format($row[csf("available_qnty")], 2) . '</td>';
												} else {
													echo '<td width="100" align="right">&nbsp;</td>';
													echo '<td width="100" align="right">&nbsp;</td>';
												}
											}
											?>
											<td width="50" align="right" class="wrap_break"><? echo $ageOfDays; //$ageOfDays;         
																							?></td>
											<td width="50" align="right" class="wrap_break">
												<? if ($daysOnHand >= 180) { ?>
													<p style="background-color: red;" title="180 days or above">
														<?
														if ($stockInHand > 0)
															echo $daysOnHand;
														else
															echo "&nbsp;"; //$daysOnHand;
														?></p>
												<? } else { ?>
													<p>
														<?
														if ($stockInHand > 0)
															echo $daysOnHand;
														else
															echo "&nbsp;"; //$daysOnHand;
														?></p>
												<? } ?>
											</td>

											<?
											if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") {
											?>
												<td width="140" align="left"><span class="comment more" style="word-wrap: break-word;word-break: break-all;"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
											<?  } else { ?>
												<td width="140" align="left"><span class="wrap_break"><? echo "&nbsp;"; ?></span></td>
											<? 	} ?>


											<td width="" align="center" class="wrap_break">
												<?
												/*$buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
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
											echo $buyer_all;*/
												?>
												<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a>
											</td>
										</tr>
										<?
										$i++;

										$total_opening_balance += $openingBalance;
										$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
										$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
										$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
										$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
										$total_total_rcv += $totalRcv;
										$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
										$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
										$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
										$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
										$total_total_delivery += $totalIssue;
										$total_stock_in_hand += $stockInHand;
										$total_alocatted += $row[csf("allocated_qnty")];
										$total_free_stock += $row[csf("available_qnty")];
										$sub_stock_value += $stock_value;
										$sub_stock_value_usd += $stock_value_usd;
										$total_transfer_out_qty += $transfer_out_qty;
										$total_transfer_in_qty += $transfer_in_qty;

										//grand total===========================
										$grand_total_opening_balance += $openingBalance;
										$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
										$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
										$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
										$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
										$grand_total_total_rcv += $totalRcv;
										$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
										$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
										$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
										$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
										$grand_total_total_delivery += $totalIssue;
										$grand_total_stock_in_hand += $stockInHand;
										$grand_total_alocatted += $row[csf("allocated_qnty")];
										$grand_total_free_stock += $row[csf("available_qnty")];
										$tot_stock_value += $stock_value;
										$tot_stock_value_usd += $stock_value_usd;

										$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
										$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
									}
								} else {
									if (number_format($stockInHand, 2) <= '0.00') {
										if ($receive_array[$row[csf("id")]]['purchase'] != 0 || $receive_array[$row[csf("id")]]['rcv_inside_return'] != 0 || $receive_array[$row[csf("id")]]['rcv_outside_return'] != 0 || $receive_array[$row[csf("id")]]['rcv_loan'] != 0 || $issue_array[$row[csf("id")]]['issue_inside'] != 0 || $issue_array[$row[csf("id")]]['issue_outside'] != 0 || $issue_array[$row[csf("id")]]['rcv_return'] != 0 || $issue_array[$row[csf("id")]]['issue_loan'] != 0) {

											if (!in_array($check_string, $checkArr)) {
												$checkArr[$i] = $check_string;
												if ($i > 1) {
										?>
													<tr bgcolor="#CCCCCC" style="font-weight:bold">
														<td colspan="16" align="right">Sub Total</td>
														<td width="110" class="wrap_break" align="right" class="wrap_break"><? echo number_format($total_opening_balance, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_purchase, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_inside_return, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_outside_return, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_in_qty, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_rcv_loan, 2); ?></td>
														<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_rcv, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_inside, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_outside, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_receive_return, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_out_qty, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_loan, 2); ?></td>
														<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_delivery, 2); ?></td>
														<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
														<?
														if ($show_val_column == 1) {
															echo '<td width="90" align="right" >&nbsp;</td>';
															echo '<td width="110" align="right" class="wrap_break">' . number_format($sub_stock_value, 2) . '</td>';
															echo '<td width="100" align="right">&nbsp;</td>';
															echo '<td width="100" align="right" class="wrap_break">' . number_format($sub_stock_value_usd, 2) . '</td>';
														}

														if ($store_wise == 1) {
															echo '<td width="100">&nbsp;</td>';
														} else {
															if ($allocated_qty_variable_settings == 1) {
																echo '<td width="100"  class="wrap_break" align="right">' . number_format($total_alocatted, 2) . '</td>';
																echo '<td width="100" class="wrap_break" align="right">' . number_format($total_free_stock, 2) . '</td>';
															} else {
																echo '<td width="100" align="right">&nbsp;</td>';
																echo '<td width="100" align="right">&nbsp;</td>';
															}
														}
														?>
														<td width="50" align="right">&nbsp;</td>
														<td width="50" align="right">&nbsp;</td>
														<td width="" align="right">&nbsp;</td>
														<td width="" align="right">&nbsp;</td>
													</tr>
											<?
													$total_opening_balance = 0;
													$total_purchase = 0;
													$total_inside_return = 0;
													$total_outside_return = 0;
													$total_rcv_loan = 0;
													$total_total_rcv = 0;
													$total_issue_inside = 0;
													$total_issue_outside = 0;
													$total_receive_return = 0;
													$total_issue_loan = 0;
													$total_total_delivery = 0;
													$total_stock_in_hand = 0;
													$total_alocatted = 0;
													$total_free_stock = 0;
													$sub_stock_value = 0;
													$sub_stock_value_usd = 0;
													$total_transfer_out_qty = 0;
													$total_transfer_in_qty = 0;
												}
											}
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td width="30"><? echo $i; ?></td>
												<td width="130" class="wrap_break">
													<p><? echo $companyArr[$row[csf("company_id")]]; ?></p>
												</td>
												<td width="60" class="wrap_break"><? echo $row[csf("id")]; ?></td>
												<td width="60" align="center">
													<p>
														<a href="javascript:void()" onClick="downloiadFile('<?= $row[csf("id")]; ?>');">
															<? if ($file_arr[$row[csf("id")]]['file'] != '') echo 'View File'; ?></a>
													</p>
												</td>
												<td width="60" class="wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
												<td width="150" class="wrap_break"><? echo $compositionDetails; ?></td>
												<td width="100" class="wrap_break"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
												<td width="120" class="wrap_break"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
												<td width="100">
													<p><? echo $yarn_type[$row[csf("last_receive_date")]]; ?></p>
												</td>
												<td width="100">
													<p><? echo $yarn_type[$row[csf("last_issue_date")]]; ?></p>
												</td>
												<td width="100">
													<p><? echo $yarn_type[$row[csf("totalRcv")]]; ?></p>
												</td>
												<td width="100" class="wrap_break" style="mso-number-format:'\@';">
													<p>
														<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
															<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
														<? } else {
															echo $row[csf("lot")];
														}
														?>
														&nbsp;
													</p>
												</td>

												<td width="100" class="wrap_break">
													<?
													echo $supplier_name;
													?>
												</td>
												<td width="100" class="wrap_break"><? echo $brandArr[$row[csf("brand")]]; ?></td>
												<td width="100" class="wrap_break"><? echo 'Bg:' . max($receive_array[$row[csf("id")]]['weight_per_bag']) . '; ' . 'Cn:' . max($receive_array[$row[csf("id")]]['weight_per_cone']); ?></td>
												<td width="100" class="wrap_break"><? echo $source[$receive_array[$row[csf("id")]]['source']]; ?></td>
												<td width="110" class="wrap_break" align="right"><? echo number_format($openingBalance, 2); ?></td>
												<td width="90" class="wrap_break" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_in_qty, 2);
													}
													?>
												</td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($totalRcv, 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_out_qty, 2);
													}
													?>
												</td>
												<td width="90" class="wrap_break" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($totalIssue, 2); ?></td>

												<td width="100" align="right" class="wrap_break"><a href='##' onclick="openmypage_stock_upto('<? echo $row[csf('id')] . "_" . $store_name . "_" . $row[csf('company_id')]; ?>', 'stock_popup_store_methode_upto')"><? echo number_format($stockInHand, 4); ?></a></td>


												<?
												$stock_value = $stock_value_usd = 0;
												if ($show_val_column == 1) {

													$avg_rate = 0;
													if (number_format($stockInHandAmt, 4) > 0 && number_format($stockInHand, 2) > 0) $avg_rate = $stockInHandAmt / $stockInHand;

													if ($avg_rate > 0) {
														$avg_rate = $avg_rate;
													} else {
														$avg_rate = "0.00";
													}

													if (number_format($stockInHand, 4) > 0) {

														$stock_value = $stockInHand * $avg_rate;

														if (number_format($stock_value, 2) > 0 && number_format($txt_excange_rate, 2) > 0) {
															$stock_value_usd = $stock_value / $txt_excange_rate;
														} else {
															$stock_value_usd = "0.00";
														}
													}

													$avz_rates_usd = 0;

													if (number_format($avg_rate, 2) > 0 && number_format($txt_excange_rate, 2) > 0) {
														$avz_rates_usd = $avg_rate / $txt_excange_rate;
													} else {
														$avz_rates_usd = "0.00";
													}


													echo '<td width="90" align="right" class="wrap_break">' . number_format($avg_rate, 2) . '</td>';
													echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
													echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
													echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
												}

												if ($store_wise == 1) {
													$store_name = '';
													$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
													$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];
													$store_id_arr = array_unique(array_merge($receive_store_id, $issue_store_id));
													foreach ($store_id_arr as $key => $val) {
														if ($store_name == "")
															$store_name = $store_arr[$val];
														else
															$store_name .= ", " . $store_arr[$val];
													}
													echo '<td width="100" class="wrap_break">' . $store_name . '</td>';
												} else {
													if ($allocated_qty_variable_settings == 1) {
														echo "<td width='100' align='right' class='wrap_break'><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
														echo '<td width="100" align="right" class="wrap_break">' . number_format($row[csf("available_qnty")], 2) . '</td>';
													} else {
														echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
														echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
													}
												}
												?>
												<td width="50" align="right" class="wrap_break"><? echo $ageOfDays; //$ageOfDays;         
																								?></td>
												<td width="50" align="right" class="wrap_break"><?
																								if ($stockInHand > 0)
																									echo $daysOnHand;
																								else
																									echo "&nbsp;"; //$daysOnHand;
																								?></td>

												<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
													<td width="140" align="left"><span class="comment more" style="word-wrap: break-word;word-break: break-all;"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
												<?  } else { ?>
													<td width="140" align="left"><span><? echo "&nbsp;"; ?></span></td>
												<? 	} ?>

												<td width="" align="center" class="wrap_break">

													<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a>
												</td>
											</tr>
											<?
											$i++;

											$total_opening_balance += $openingBalance;
											$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
											$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
											$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
											$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
											$total_total_rcv += $totalRcv;
											$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
											$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
											$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
											$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
											$total_total_delivery += $totalIssue;
											$total_stock_in_hand += $stockInHand;
											$total_alocatted += $row[csf("allocated_qnty")];
											$total_free_stock += $row[csf("available_qnty")];
											$sub_stock_value += $stock_value;
											$sub_stock_value_usd += $stock_value_usd;
											$total_transfer_out_qty += $transfer_out_qty;
											$total_transfer_in_qty += $transfer_in_qty;

											//grand total===========================
											$grand_total_opening_balance += $openingBalance;
											$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
											$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
											$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
											$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
											$grand_total_total_rcv += $totalRcv;
											$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
											$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
											$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
											$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
											$grand_total_total_delivery += $totalIssue;
											$grand_total_stock_in_hand += $stockInHand;
											$grand_total_alocatted += $row[csf("allocated_qnty")];
											$grand_total_free_stock += $row[csf("available_qnty")];
											$tot_stock_value += $stock_value;
											$tot_stock_value_usd += $stock_value_usd;

											$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
											$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
										}
									} else {
										if (!in_array($check_string, $checkArr)) {
											$checkArr[$i] = $check_string;
											if ($i > 1) {
											?>
												<tr bgcolor="#CCCCCC" style="font-weight:bold">
													<td colspan="16" align="right">Sub Total</td>
													<td width="110" class="wrap_break" align="right" class="wrap_break"><? echo number_format($total_opening_balance, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_purchase, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_inside_return, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_outside_return, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_in_qty, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_rcv_loan, 2); ?></td>
													<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_rcv, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_inside, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_outside, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_receive_return, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_out_qty, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_loan, 2); ?></td>
													<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_delivery, 2); ?></td>
													<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
													<?
													if ($show_val_column == 1) {
														echo '<td width="90" align="right" >&nbsp;</td>';
														echo '<td width="110" align="right" class="wrap_break">' . number_format($sub_stock_value, 2) . '</td>';
														echo '<td width="100" align="right">&nbsp;</td>';
														echo '<td width="100" align="right" class="wrap_break">' . number_format($sub_stock_value_usd, 2) . '</td>';
													}

													if ($store_wise == 1) {
														echo '<td width="100">&nbsp;</td>';
													} else {
														if ($allocated_qty_variable_settings == 1) {
															echo '<td width="100"  class="wrap_break" align="right">' . number_format($total_alocatted, 2) . '</td>';
															echo '<td width="100" class="wrap_break" align="right">' . number_format($total_free_stock, 2) . '</td>';
														} else {
															echo '<td width="100" align="right">&nbsp;</td>';
															echo '<td width="100" align="right">&nbsp;</td>';
														}
													}
													?>
													<td width="50" align="right">&nbsp;</td>
													<td width="50" align="right">&nbsp;</td>
													<td width="" align="right">&nbsp;</td>
													<td width="" align="right">&nbsp;</td>
												</tr>
										<?
												$total_opening_balance = 0;
												$total_purchase = 0;
												$total_inside_return = 0;
												$total_outside_return = 0;
												$total_rcv_loan = 0;
												$total_total_rcv = 0;
												$total_issue_inside = 0;
												$total_issue_outside = 0;
												$total_receive_return = 0;
												$total_issue_loan = 0;
												$total_total_delivery = 0;
												$total_stock_in_hand = 0;
												$total_alocatted = 0;
												$total_free_stock = 0;
												$sub_stock_value = 0;
												$sub_stock_value_usd = 0;
												$total_transfer_out_qty = 0;
												$total_transfer_in_qty = 0;
											}
										}
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30"><? echo $i; ?></td>
											<td width="130" class="wrap_break">
												<p><? echo $companyArr[$row[csf("company_id")]]; ?></p>
											</td>
											<td width="60" class="wrap_break"><? echo $row[csf("id")]; ?></td>
											<td width="60" align="center">
												<p>
													<a href="javascript:void()" onClick="downloiadFile('<?= $row[csf("id")]; ?>');">
														<? if ($file_arr[$row[csf("id")]]['file'] != '') echo 'View File'; ?></a>
												</p>
											</td>
											<td width="60" class="wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
											<td width="150" class="wrap_break"><? echo $compositionDetails; ?></td>
											<td width="100" class="wrap_break"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
											<td width="120" class="wrap_break"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
											<td width="100">
												<p><? echo $yarn_type[$row[csf("last_receive_date")]]; ?></p>
											</td>
											<td width="100">
												<p><? echo $yarn_type[$row[csf("last_issue_date")]]; ?></p>
											</td>
											<td width="100">
												<p><? echo $yarn_type[$row[csf("totalRcv")]]; ?></p>
											</td>
											<td width="100" class="wrap_break" style="mso-number-format:'\@';">
												<p>
													<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
														<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
													<? } else {
														echo $row[csf("lot")];
													}
													?>
													&nbsp;
												</p>
											</td>

											<td width="100" class="wrap_break">
												<?
												echo $supplier_name;
												?>
											</td>
											<td width="100" class="wrap_break"><? echo $brandArr[$row[csf("brand")]]; ?></td>
											<td width="100" class="wrap_break"><? echo 'Bg:' . max($receive_array[$row[csf("id")]]['weight_per_bag']) . '; ' . 'Cn:' . max($receive_array[$row[csf("id")]]['weight_per_cone']); ?></td>
											<td width="100" class="wrap_break"><? echo $source[$receive_array[$row[csf("id")]]['source']]; ?></td>
											<td width="110" class="wrap_break" align="right"><? echo number_format($openingBalance, 2); ?></td>
											<td width="90" class="wrap_break" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break">
												<?
												if ($store_wise == 1) {
													echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
												} else {
													echo number_format($transfer_in_qty, 2);
												}
												?>
											</td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
											<td width="100" align="right" class="wrap_break"><? echo number_format($totalRcv, 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break">
												<?
												if ($store_wise == 1) {
													echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
												} else {
													echo number_format($transfer_out_qty, 2);
												}
												?>
											</td>
											<td width="90" class="wrap_break" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
											<td width="100" align="right" class="wrap_break"><? echo number_format($totalIssue, 2); ?></td>
											<td width="100" align="right" class="wrap_break"><a href='##' onclick="openmypage_stock_upto('<? echo $row[csf('id')] . "_" . $store_name . "_" . $row[csf('company_id')]; ?>', 'stock_popup_store_methode_upto')"><? echo number_format($stockInHand, 4); ?></a></td>
											<?
											$stock_value = 0;
											if ($show_val_column == 1) {

												$avg_rate = ($stockInHandAmt / $stockInHand);
												if ($avg_rate > 0) {
													$avg_rate = $avg_rate;
												} else {
													$avg_rate = "0.00";
												}

												$stock_value = $stockInHand * $avg_rate;
												$stock_value_usd = ($stockInHand * $avg_rate) / $exchange_rate;
												$avz_rates_usd = 0;

												if (number_format($stock_value_usd, 2) > 0 && number_format($stockInHand, 2) > 0) {
													$avz_rates_usd = $stock_value_usd / $stockInHand;
												} else {
													$avz_rates_usd = "0.00";
												}

												echo '<td width="90" align="right" class="wrap_break">' . number_format($avg_rate, 2) . '</td>';
												echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
												echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
												echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
											}

											if ($store_wise == 1) {
												$store_name = '';
												$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
												$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];
												$store_id_arr = array_unique(array_merge($receive_store_id, $issue_store_id));
												foreach ($store_id_arr as $key => $val) {
													if ($store_name == "")
														$store_name = $store_arr[$val];
													else
														$store_name .= ", " . $store_arr[$val];
												}
												echo '<td width="100" class="wrap_break">' . $store_name . '</td>';
											} else {
												if ($allocated_qty_variable_settings == 1) {
													echo "<td width='100' align='right' class='wrap_break'><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
													echo '<td width="100" align="right" class="wrap_break">' . number_format($row[csf("available_qnty")], 2) . '</td>';
												} else {
													echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
													echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
												}
											}
											?>
											<td width="50" align="right" class="wrap_break"><? echo $ageOfDays; //$ageOfDays;         
																							?></td>
											<td width="50" align="right" class="wrap_break"><?
																							if ($stockInHand > 0)
																								echo $daysOnHand;
																							else
																								echo "&nbsp;"; //$daysOnHand;
																							?></td>

											<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
												<td width="140" align="left"><span class="comment more" style="word-wrap: break-word;word-break: break-all;"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
											<?  } else { ?>
												<td width="140" align="left"><span><? echo "&nbsp;"; ?></span></td>
											<? 	} ?>

											<td width="" align="center" class="wrap_break">

												<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a>
											</td>
										</tr>
						<?
										$i++;

										$total_opening_balance += $openingBalance;
										$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
										$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
										$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
										$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
										$total_total_rcv += $totalRcv;
										$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
										$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
										$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
										$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
										$total_total_delivery += $totalIssue;
										$total_stock_in_hand += $stockInHand;
										$total_alocatted += $row[csf("allocated_qnty")];
										$total_free_stock += $row[csf("available_qnty")];
										$sub_stock_value += $stock_value;
										$sub_stock_value_usd += $stock_value_usd;
										$total_transfer_out_qty += $transfer_out_qty;
										$total_transfer_in_qty += $transfer_in_qty;

										//grand total===========================
										$grand_total_opening_balance += $openingBalance;
										$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
										$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
										$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
										$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
										$grand_total_total_rcv += $totalRcv;
										$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
										$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
										$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
										$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
										$grand_total_total_delivery += $totalIssue;
										$grand_total_stock_in_hand += $stockInHand;
										$grand_total_alocatted += $row[csf("allocated_qnty")];
										$grand_total_free_stock += $row[csf("available_qnty")];
										$tot_stock_value += $stock_value;
										$tot_stock_value_usd += $stock_value_usd;

										$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
										$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
									}
								} // end else
							}
						}
						?>
						<tr bgcolor="#CCCCCC" style="font-weight:bold">
							<td colspan="16" align="right">Sub Total</td>
							<td width="110" class="wrap_break" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
							<td width="90" align="right" class="wrap_break"><? echo number_format($total_purchase, 2); ?></td>
							<td width="90" align="right" class="wrap_break"><? echo number_format($total_inside_return, 2); ?></td>
							<td width="90" align="right" class="wrap_break"><? echo number_format($total_outside_return, 2); ?></td>
							<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_in_qty, 2); ?></td>
							<td width="90" align="right" class="wrap_break"><? echo number_format($total_rcv_loan, 2); ?></td>
							<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_rcv, 2); ?></td>
							<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_inside, 2); ?></td>
							<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_outside, 2); ?></td>
							<td width="90" align="right" class="wrap_break"><? echo number_format($total_receive_return, 2); ?></td>
							<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_out_qty, 2); ?></td>
							<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_loan, 2); ?></td>
							<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_delivery, 2); ?></td>
							<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
							<?
							if ($show_val_column == 1) {
								echo '<td width="90" align="right">&nbsp;</td>';
								echo '<td width="110" class="wrap_break" align="right">' . number_format($sub_stock_value, 2) . '</td>';
								echo '<td width="100" align="right">&nbsp;</td>';
								echo '<td width="100" class="wrap_break" align="right">' . number_format($sub_stock_value_usd, 2) . '</td>';
							}

							if ($store_wise == 1) {
								echo '<td width="100">&nbsp;</td>';
							} else {
								if ($allocated_qty_variable_settings == 1) {
									echo '<td width="100" align="right">' . number_format($total_alocatted, 2) . '</td>';
									echo '<td width="100" align="right">' . number_format($total_free_stock, 2) . '</td>';
								} else {
									echo '<td width="100" align="right">&nbsp;</td>';
									echo '<td width="100" align="right">&nbsp;</td>';
								}
							}
							?>
							<td width="50" align="right">&nbsp;</td>
							<td width="50" align="right">&nbsp;</td>
							<td width="" align="right">&nbsp;</td>
							<td width="" align="right">&nbsp;</td>
						</tr>
					</table>
				</div>
				<table style="width:<? echo $table_width + 80; ?>px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
					<tr class="tbl_bottom">
						<td width="30"></td>
						<td width="130"></td>
						<td width="60"></td>
						<td width="60"></td>
						<td width="60"></td>
						<td width="150"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="120"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100" align="right">Grand Total</td>
						<td width="110" class="wrap_break" align="right" id="value_total_opening_balance"><? echo number_format($grand_total_opening_balance, 2); ?></td>
						<td width="90" align="right" id="value_total_purchase" class="wrap_break"><? echo number_format($grand_total_purchase, 2); ?></td>
						<td width="90" align="right" id="value_total_inside_return" class="wrap_break"><? echo number_format($grand_total_inside_return, 2); ?></td>
						<td width="90" align="right" id="value_total_outside_return" class="wrap_break"><? echo number_format($grand_total_outside_return, 2); ?></td>
						<td width="90" align="right" id="value_total_transfer_in" class="wrap_break"><? echo number_format($grand_total_transfer_in_qty, 2); ?></td>
						<td width="90" class="wrap_break" align="right" id="value_total_rcv_loan"><? echo number_format($grand_total_rcv_loan, 2); ?></td>
						<td width="100" class="wrap_break" align="right" id="value_total_total_rcv"><? echo number_format($grand_total_total_rcv, 2); ?></td>
						<td width="90" class="wrap_break" align="right" id="value_total_issue_inside"><? echo number_format($grand_total_issue_inside, 2); ?></td>
						<td width="90" class="wrap_break" align="right" id="value_total_issue_outside"><? echo number_format($grand_total_issue_outside, 2); ?></td>
						<td width="90" class="wrap_break" align="right" id="value_total_receive_return"><? echo number_format($grand_total_receive_return, 2); ?></td>
						<td width="90" align="right" class="wrap_break" id="value_total_transfer_out"><? echo number_format($grand_total_transfer_out_qty, 2); ?></td>
						<td width="90" align="right" class="wrap_break" id="value_total_issue_loan"><? echo number_format($grand_total_issue_loan, 2); ?></td>
						<td width="100" align="right" class="wrap_break" id="value_total_total_delivery"><? echo number_format($grand_total_total_delivery, 2); ?></td>
						<td width="100" align="right" class="wrap_break" id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 4); ?></td>
						<?
						if ($show_val_column == 1) {
							echo '<td width="90" align="right">&nbsp;</td>';
							echo '<td width="110" class="wrap_break" align="right">' . number_format($tot_stock_value, 2) . '</td>';
							echo '<td width="100" align="right">&nbsp;</td>';
							echo '<td width="100" class="wrap_break" align="right">' . number_format($tot_stock_value_usd, 2) . '</td>';
						}

						if ($store_wise == 1) {
							echo '<td width="100">&nbsp;</td>';
						} else {
							if ($allocated_qty_variable_settings == 1) {
								echo '<td width="100" align="right" id="value_total_alocatted">' . number_format($grand_total_alocatted, 2) . '</td>';
								echo '<td width="100" align="right" id="value_total_free_stock">' . number_format($grand_total_free_stock, 2) . '</td>';
							} else {
								echo '<td width="100" align="right" id="value_total_alocatted">&nbsp;</td>';
								echo '<td width="100" align="right" id="value_total_free_stock">&nbsp;</td>';
							}
						}
						?>
						<td width="50" align="right">&nbsp;</td>
						<td width="50" align="right">&nbsp;</td>
						<td width="140" align="right">&nbsp;</td>
						<td width="" align="right">&nbsp;</td>
					</tr>
				</table>
			</div>
		<?
		} else if ($type == 2) {
			if ($value_with == 0)
				$stock_cond = "";
			else
				$stock_cond = "  and a.current_stock>0";

			$count_arr = array();
			$sql = "select a.id, a.yarn_count_id, a.yarn_type from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond $stock_cond group by a.id, a.yarn_count_id, a.yarn_type order by a.yarn_count_id, a.yarn_type";

			$result = sql_select($sql);
			foreach ($result as $row) {

				$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
				$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
				$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
				$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

				$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
				$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
				$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;
				$stockInHand = ($openingBalance + $totalRcv) - $totalIssue;
				$count_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]] += $stockInHand;
			}
			$i = 1;
			ob_start();
		?>
			<div style="margin-top:5px">
				<table width="702" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="4" align="center" style="border:none;font-size:16px; font-weight:bold">Count Wise Yarn Stock Summary Report</td>
						</tr>
						<tr style="border:none;">
							<td colspan="4" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="4" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
							</td>
						</tr>
						<tr>
							<th width="70">SL</th>
							<th width="150">Count</th>
							<th width="200">Type</th>
							<th>Stock In Hand</th>
						</tr>
					</thead>
				</table>
				<div style="width:720px; overflow-y:scroll; max-height:350px" id="scroll_body">
					<table width="702" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$tot_stock_qty = 0;
						foreach ($count_arr as $count => $value) {
							foreach ($value as $type => $stock_qnty) {
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								if (!in_array($count, $checkArr)) {
									$checkArr[$i] = $count;
									if ($i > 1) {
						?>
										<tr bgcolor="#CCCCCC" style="font-weight:bold">
											<td colspan="3" align="right">Sub Total</td>
											<td align="right"><? echo number_format($count_tot_qnty, 2); ?></td>
										</tr>
								<?
										$count_tot_qnty = 0;
									}
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="70"><? echo $i; ?></td>
									<td width="150">
										<p><? echo $yarn_count_arr[$count]; ?>&nbsp;</p>
									</td>
									<td width="200">
										<p><? echo $yarn_type[$type]; ?>&nbsp;</p>
									</td>
									<td align="right"><? echo number_format($stock_qnty, 2); ?></td>
								</tr>
						<?
								$i++;

								$count_tot_qnty += $stock_qnty;
								$tot_stock_qty += $stock_qnty;
							}
						}
						?>
						<tr bgcolor="#CCCCCC" style="font-weight:bold">
							<td colspan="3" align="right">Sub Total</td>
							<td align="right"><? echo number_format($count_tot_qnty, 2); ?></td>
						</tr>
					</table>
				</div>
				<table width="720" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
					<tr class="tbl_bottom">
						<td width="70">&nbsp;</td>
						<td width="150">&nbsp;</td>
						<td width="200">Total</td>
						<td align="right" style="padding-right:18px"><? echo number_format($tot_stock_qty, 2); ?></td>
					</tr>
				</table>
			</div>
		<?
		} else if ($type == 3) {
			if ($value_with == 0)
				$stock_cond = "";
			else
				$stock_cond = "  and a.current_stock>0";

			$type_arr = array();
			$sql = "select a.id, a.yarn_type, a.yarn_count_id from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond $stock_cond group by a.id, a.yarn_type, a.yarn_count_id order by a.yarn_type, a.yarn_count_id";

			//echo $sql;die;
			$result = sql_select($sql);
			foreach ($result as $row) {

				$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
				$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
				$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
				$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

				$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
				$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
				$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;
				$stockInHand = ($openingBalance + $totalRcv) - $totalIssue;
				$type_arr[$row[csf("yarn_type")]][$row[csf("yarn_count_id")]] += $stockInHand;
			}
			$i = 1;
			//ob_start();
		?>
			<div style="margin-top:5px">
				<table width="702" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="4" align="center" style="border:none;font-size:16px; font-weight:bold">Count Wise Yarn Stock Summary Report</td>
						</tr>
						<tr style="border:none;">
							<td colspan="4" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="4" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
							</td>
						</tr>
						<tr>
							<th width="70">SL</th>
							<th width="200">Type</th>
							<th width="150">Count</th>
							<th>Stock In Hand</th>
						</tr>
					</thead>
				</table>
				<div style="width:720px; overflow-y:scroll; max-height:350px" id="scroll_body">
					<table width="702" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$tot_stock_qty = 0;
						foreach ($type_arr as $type => $value) {
							foreach ($value as $count => $stock_qnty) {
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								if (!in_array($type, $checkArr)) {
									$checkArr[$i] = $type;
									if ($i > 1) {
						?>
										<tr bgcolor="#CCCCCC" style="font-weight:bold">
											<td colspan="3" align="right">Sub Total</td>
											<td align="right"><? echo number_format($count_tot_qnty, 2); ?></td>
										</tr>
								<?
										$count_tot_qnty = 0;
									}
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="70"><? echo $i; ?></td>
									<td width="200">
										<p><? echo $yarn_type[$type]; ?>&nbsp;</p>
									</td>
									<td width="150">
										<p><? echo $yarn_count_arr[$count]; ?>&nbsp;</p>
									</td>
									<td align="right"><? echo number_format($stock_qnty, 2); ?></td>
								</tr>
						<?
								$i++;

								$count_tot_qnty += $stock_qnty;
								$tot_stock_qty += $stock_qnty;
							}
						}
						?>
						<tr bgcolor="#CCCCCC" style="font-weight:bold">
							<td colspan="3" align="right">Sub Total</td>
							<td align="right"><? echo number_format($count_tot_qnty, 2); ?></td>
						</tr>
					</table>
				</div>
				<table width="720" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
					<tr class="tbl_bottom">
						<td width="70">&nbsp;</td>
						<td width="150">&nbsp;</td>
						<td width="200">Total</td>
						<td align="right" style="padding-right:18px"><? echo number_format($tot_stock_qty, 2); ?></td>
					</tr>
				</table>
			</div>
		<?
		} else if ($type == 4) {
			if ($value_with == 0)
				$stock_cond = "";
			else
				$stock_cond = " and a.current_stock>0";

			$pipe_line_purchase_arr = array();
			$pipe_line_ydbooking_arr = array();
			$pipe_line_pi_arr = array();
			$sql_booking = sql_select("select b.yarn_count, b.yarn_type, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.color_name, sum(b.supplier_order_quantity) as qnty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.pay_mode!=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.yarn_count, b.yarn_type, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.color_name"); //and a.wo_number='OG-15-00043'
			foreach ($sql_booking as $row) {
				if ($row[csf("yarn_comp_type2nd")] != 0) {
					$compositionDtls = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")] . '**' . $row[csf("yarn_comp_type2nd")] . '**' . $row[csf("yarn_comp_percent2nd")];
				} else {
					$compositionDtls = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")];
				}
				$pipe_line_purchase_arr[$row[csf("yarn_count")]][$row[csf("yarn_type")]][$compositionDtls][$row[csf("color_name")]] = $row[csf("qnty")];
			}
			//print_r($pipe_line_purchase_arr);
			$sql_ywdbooking = sql_select("select c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.color, sum(b.yarn_wo_qty) as qnty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and c.item_category_id=1 and a.pay_mode!=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.color");
			foreach ($sql_ywdbooking as $row) {
				if ($row[csf("yarn_comp_type2nd")] != 0) {
					$compositionDtl = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")] . '**' . $row[csf("yarn_comp_type2nd")] . '**' . $row[csf("yarn_comp_percent2nd")];
				} else {
					$compositionDtl = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")];
				}
				$pipe_line_ydbooking_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$compositionDtl][$row[csf("color")]] = $row[csf("qnty")];
			}

			$sql_pi = sql_select("select b.count_name, b.yarn_type, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.color_id, sum(b.quantity) as qnty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.count_name, b.yarn_type, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.color_id");  //and a.id in (1161,1162)
			foreach ($sql_pi as $row) {
				if ($row[csf("yarn_composition_item2")] != 0) {
					$compositionDtlPi = $row[csf("yarn_composition_item1")] . '**' . $row[csf("yarn_composition_percentage1")] . '**' . $row[csf("yarn_composition_item2")] . '**' . $row[csf("yarn_composition_percentage2")];
				} else {
					$compositionDtlPi = $row[csf("yarn_composition_item1")] . '**' . $row[csf("yarn_composition_percentage1")];
				}
				$pipe_line_pi_arr[$row[csf("count_name")]][$row[csf("yarn_type")]][$compositionDtlPi][$row[csf("color_id")]] = $row[csf("qnty")];
			}
			//print_r ($pipe_line_pi_arr);
			$pipelineArr = array();

			$sql_ppl = "select a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond $stock_cond group by  a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color order by a.yarn_type, a.yarn_count_id";


			$result_ppl = sql_select($sql_ppl);
			foreach ($result_ppl as $row) {
				if ($row[csf("yarn_comp_type2nd")] != 0) {
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")] . '**' . $row[csf("yarn_comp_type2nd")] . '**' . $row[csf("yarn_comp_percent2nd")];
				} else {
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")];
				}
				if ($row[csf("receive_basis")] == 2) {
					if ($row[csf("receive_purpose")] == 16) {
						$pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $pipe_line_purchase_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]];
					} else if ($row[csf("receive_purpose")] == 2) {
						$pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $pipe_line_ydbooking_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]];
					}
				} else {
					$pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $pipe_line_pi_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]];
				}
				//$pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]]=$pipe_line_qty;
			}
			//print_r($pipelineArr);
			$type_arr = array();

			if ($cbo_dyed_type == 0) {
				$sql = "select a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color order by a.yarn_type, a.yarn_count_id";
			} else {
				$sql = "select a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color from product_details_master a, inv_transaction b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.entry_form=1 and b.transaction_type=1 $company_cond $search_cond group by a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color order by a.yarn_type, a.yarn_count_id";
			}
			//echo $sql;die;
			$result = sql_select($sql);
			foreach ($result as $row) {
				//$pipe_line_qty=0;
				$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
				$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

				$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
				$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

				$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
				$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
				$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;
				$stockInHand = ($openingBalance + $totalRcv) - $totalIssue;
				if ($row[csf("yarn_comp_type2nd")] != 0) {
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")] . '**' . $row[csf("yarn_comp_type2nd")] . '**' . $row[csf("yarn_comp_percent2nd")];
				} else {
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")];
				}

				$pipe_line_qty = $pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]];

				$type_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $stockInHand;
				$pipe_line_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] = $pipe_line_qty;
			}
			//print_r($pipe_line_arr);
			$colorArr = return_library_array("select id, color_name from lib_color", "id", "color_name");
			$i = 1;
			//ob_start();
		?>
			<div style="margin-top:5px">
				<table width="802" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="7" align="center" style="border:none;font-size:16px; font-weight:bold">Composition Wise Yarn Stock Summary Report</td>
						</tr>
						<tr style="border:none;">
							<td colspan="7" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="7" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
							</td>
						</tr>
						<tr>
							<th width="50">SL</th>
							<th width="100">Count</th>
							<th width="200">Composition</th>
							<th width="100">Type</th>
							<th width="100">Color</th>
							<th width="100">Stock In Hand</th>
							<th>Pipe Line</th>
						</tr>
					</thead>
				</table>
				<div style="width:820px; overflow-y:scroll; max-height:350px" id="scroll_body">
					<table width="802" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$tot_stock_qty = 0;
						foreach ($type_arr as $count => $value) {
							foreach ($value as $type => $type_val) {
								foreach ($type_val as $compo => $comp_val) {
									foreach ($comp_val as $color => $stock_qty) {
										$pipeline_qty = $pipe_line_arr[$count][$type][$compo][$color];
										$bal_pipeline_qty = $pipeline_qty - $stock_qty;
										//echo $pipeline_qty.'=='.$stock_qty;
										$ex_comp = explode('**', $compo);
										$comp_1type = $ex_comp[0];
										$comp_1per = $ex_comp[1];
										$comp_2type = $ex_comp[2];
										$comp_2per = $ex_comp[3];
										$fullCompositionValue = "";
										if ($comp_2type != 0) {
											$fullCompositionValue = $composition[$comp_1type] . ',' . $comp_1per . '%  ' . $composition[$comp_2type] . ',' . $comp_2per . '%';
										} else {
											$fullCompositionValue = $composition[$comp_1type] . ',' . $comp_1per . '% ';
										}

										if ($i % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";
						?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="50"><? echo $i; ?></td>
											<td width="100">
												<p><? echo $yarn_count_arr[$count]; ?>&nbsp;</p>
											</td>
											<td width="200">
												<p><? echo $fullCompositionValue; ?>&nbsp;</p>
											</td>
											<td width="100">
												<p><? echo $yarn_type[$type]; ?>&nbsp;</p>
											</td>
											<td width="100">
												<p><? echo $colorArr[$color]; ?>&nbsp;</p>
											</td>
											<td width="100" align="right"><? echo number_format($stock_qty, 2); ?></td>
											<td align="right"><? echo number_format($bal_pipeline_qty, 2); ?></td>
										</tr>
						<?
										$i++;

										$count_tot_qnty += $stock_qty;
										$tot_stock_qty += $stock_qty;
										$tot_pipeline_qty += $bal_pipeline_qty;
									}
								}
							}
						}
						?>
						<!--<tr bgcolor="#CCCCCC" style="font-weight:bold">
			<td colspan="3" align="right">Sub Total</td>
			<td align="right"><? // echo number_format($count_tot_qnty,2);          
								?></td>
			</tr>-->
					</table>
				</div>
				<table width="820" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
					<tr class="tbl_bottom">
						<td width="50">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="200">Total</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100" align="right"><? echo number_format($tot_stock_qty, 2); ?></td>
						<td align="right" style="padding-right:18px"><? echo number_format($tot_pipeline_qty, 2); ?></td>
					</tr>
				</table>
			</div>
		<?
		} else if ($type == 5 || $type == 10) {

			$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, a.brand
			from product_details_master a
			where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond
			group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, a.brand
			order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
			//echo $sql;//die;echo count($result); //
			$result = sql_select($sql);

			// For Yarn Test //
			$yarnTestQalityCommentsArr = return_library_array("select a.prod_id as prod_id, b.comments_author as comments_author from inv_yarn_test_mst a, inv_yarn_test_comments b where a.id=b.mst_table_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.comments_author IS NOT NULL", 'prod_id', 'comments_author');
			//print_r($yarnTestQalityCommentsArr);
			/*
			$prod_ids_arr = array();
			foreach($result as $row)
			{
				array_push($prod_ids_arr, $row[csf("id")]);
			}*/
			if ($store_name != 0)  $store_cond = " and store_id in($store_name) ";
			if ($cbo_company_name != 0)  $company_cond = " and company_id=$cbo_company_name ";

			$mrr_info_sql = sql_select("SELECT prod_id,
			SUM(case when transaction_type in (1,4,5) then cons_quantity else 0 end) as mrr_total_recieved_quantity,
			MIN(case when transaction_type in (1,4,5) and BALANCE_QNTY>0 then transaction_date else null end) as all_type_rcv_mrr_min_date,
			MAX(case when transaction_type in (1,4,5) then transaction_date else null end) as all_type_rcv_mrr_max_date,
			MAX(case when transaction_type in (1,4,5) then transaction_date else null end) as mrr_recieved_last_date,
			MAX(case when transaction_type in (2,3,6) then transaction_date else null end) as mrr_issue_last_date
	    	FROM inv_transaction
	    	WHERE status_active = 1 AND is_deleted = 0 AND item_category = 1 $company_cond $store_cond  
			GROUP BY prod_id"); // ".where_con_using_array($prod_ids_arr,0,'prod_id')."
			$mrr_info_arr = array();
			foreach ($mrr_info_sql as $row) {
				$mrr_info_arr[$row[csf("prod_id")]]['all_type_rcv_mrr_min_date'] = $row[csf("all_type_rcv_mrr_min_date")];
				$mrr_info_arr[$row[csf("prod_id")]]['all_type_rcv_mrr_max_date'] = $row[csf("all_type_rcv_mrr_max_date")];

				$mrr_info_arr[$row[csf("prod_id")]]['mrr_recieved_last_date'] = $row[csf("mrr_recieved_last_date")];
				$mrr_info_arr[$row[csf("prod_id")]]['mrr_total_recieved_quantity'] = $row[csf("mrr_total_recieved_quantity")];
				$mrr_info_arr[$row[csf("prod_id")]]['mrr_issue_last_date'] = $row[csf("mrr_issue_last_date")];
			}

			unset($mrr_rate_sql);

			$i = 1;
			//ob_start();
			if ($type == 5) {
				$tblWidth = "2050";
				$colspan = "10";
			} else {
				$tblWidth = "1540";
				$colspan = "8";
			}
		?>
			<style type="text/css">
				table tr th,
				table tr td {
					word-wrap: break-word;
					word-break: break-all;
				}
			</style>

			<div>
				<table width="<? echo $tblWidth; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="18" align="center" style="border:none;font-size:16px; font-weight:bold">Daily Yarn Stock </td>
						</tr>
						<tr style="border:none;">
							<td colspan="18" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="18" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
							</td>
						</tr>
						<tr style="word-break:normal;">
							<th rowspan="2" width="40">SL</th>
							<th rowspan="2" width="120">Company Name</th>
							<th colspan="12" width="810">Description</th>
							<th rowspan="2" width="100">Stock In Hand</th>

							<th rowspan="2" width="100">Allocated to Order</th>
							<th rowspan="2" width="100">Un Allocated Qty.</th>
							<?
							if ($type == 5) {
							?>
								<th rowspan="2" width="90">Avg. Rate (USD)</th>
								<th rowspan="2" width="100">Stock Value (USD)</th>
							<?
							}
							?>
							<th rowspan="2" width="50">Age (Days)</th>
							<th rowspan="2" width="140">Yarn Quality<br>Comments</th>
							<th rowspan="2">Remarksss</th>
						</tr>
						<tr>
							<th width="60">Prod.ID</th>
							<th width="60">Count</th>
							<th width="150">Composition</th>
							<th width="100">Yarn Type</th>
							<th width="80">Color</th>
							<th width="100">Last Receive Date</th>
							<th width="100">Last Issue Date</th>
							<th width="100">Total Received Qty</th>
							<th width="100">Lot</th>
							<th width="80">Supplier</th>
							<?
							if ($type == 5) {
							?>
								<th width="100">Brand</th>
							<?
							}
							?>
							<th width="80">Buyer</th>
						</tr>
					</thead>
				</table>
				<div style="width:2050px; overflow-y:scroll; max-height:350px" id="scroll_body">
					<table width="<?php echo $tblWidth; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$grand_total_alocatted = 0;
						$grand_total_free_stock = 0;
						$tot_stock_value = 0;
						//echo "<pre>";print_r($receive_array[620188]);
						//echo "<pre>";print_r($issue_array[620188]);
						foreach ($result as $row) {
							$ageOfDays = datediff("d", $mrr_info_arr[$row[csf("id")]]['all_type_rcv_mrr_min_date'], date("Y-m-d"));
							$daysOnHand = datediff("d", $mrr_info_arr[$row[csf("id")]]['all_type_rcv_mrr_max_date'], date("Y-m-d"));

							$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";

							if ($row[csf("yarn_comp_type2nd")] != 0) {
								$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
							}

							$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
							$transfer_in_amt = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
							$transfer_in_amt_usd = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt_usd'];
							$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
							$transfer_out_amt = $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];
							$transfer_out_amt_usd = $transfer_qty_array[$row[csf("id")]]['transfer_out_amt_usd'];

							$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
							$trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];
							$trans_out_total_opening_amt_usd = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt_usd'];
							$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];
							$trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];
							$trans_in_total_opening_amt_usd = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt_usd'];

							$pay_mode = $receive_array[$row[csf("id")]]['pay_mode'];
							$receive_basis = $receive_array[$row[csf("id")]]['receive_basis'];
							$remarks = $receive_array[$row[csf("id")]]['remarks'];

							//echo "(".$receive_array[$row[csf("id")]]['rcv_total_opening']."+".$trans_in_total_opening.")-(".$issue_array[$row[csf("id")]]['issue_total_opening']."+".$trans_out_total_opening.")";

							$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);

							$openingBalanceAmt = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt);
							$openingBalanceAmtUsd = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt_usd'] + $trans_in_total_opening_amt_usd) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt_usd'] + $trans_out_total_opening_amt_usd);

							//echo $totalRcv ."=". $receive_array[$row[csf("id")]]['purchase'] ."+". $receive_array[$row[csf("id")]]['rcv_inside_return'] ."+". $receive_array[$row[csf("id")]]['rcv_outside_return'] ."+". $receive_array[$row[csf("id")]]['rcv_loan'] ."+". $transfer_in_qty;

							$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;


							$totalRcvAmt = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $transfer_in_amt;

							$totalRcvAmtUsd = $receive_array[$row[csf("id")]]['purchase_amt_usd'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt_usd'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt_usd'] + $receive_array[$row[csf("id")]]['rcv_loan_amt_usd'] + $transfer_in_amt_usd;

							$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;

							$totalIssueAmt = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] + $issue_array[$row[csf("id")]]['issue_loan_amt'] + $transfer_out_amt;

							$totalIssueAmtUsd = $issue_array[$row[csf("id")]]['issue_inside_amt_usd'] + $issue_array[$row[csf("id")]]['issue_outside_amt_usd'] + $issue_array[$row[csf("id")]]['rcv_return_amt_usd'] + $issue_array[$row[csf("id")]]['issue_loan_amt_usd'] + $transfer_out_amt_usd;

							//echo $row[csf("id")]."=>". $openingBalance ."+". $totalRcv ."-". $totalIssue."<br>"; //die();

							$stockInHand = $openingBalance + $totalRcv - $totalIssue;
							$tot_rcv_qnty = $openingBalance + $totalRcv;
							$stockInHandAmt = $openingBalanceAmt + $totalRcvAmt - $totalIssueAmt;
							$stockInHandAmtUsd = $openingBalanceAmtUsd + $totalRcvAmtUsd - $totalIssueAmtUsd;

							$tot_rcv_amt = $openingBalanceAmt + $totalRcvAmt;
							$tot_rcv_amt_usd = $openingBalanceAmtUsd + $totalRcvAmtUsd;

							$avg_rate = 0;
							if ($tot_rcv_amt != 0 && $tot_rcv_qnty != 0) {
								$avg_rate = $tot_rcv_amt / $tot_rcv_qnty;
							}

							$avg_rate_usd = 0;
							if ($tot_rcv_amt_usd != 0 && $tot_rcv_qnty != 0) {
								$avg_rate_usd = $tot_rcv_amt_usd / $tot_rcv_qnty;
							}

							$avg_rate = (is_nan($avg_rate) == 1 || is_infinite($avg_rate)) ? 0 : $avg_rate;
							$stock_value = $stockInHand * $avg_rate;
							$stock_value = (is_nan($stock_value) == 1 || is_infinite($stock_value)) ? 0 : $stock_value;

							$stock_value_usd = $stockInHand * $avg_rate_usd;

							$avg_rate_usd = (is_nan($avg_rate_usd) == 1 || is_infinite($avg_rate_usd)) ? 0 : $avg_rate_usd;
							$stock_value_usd = (is_nan($stock_value_usd) == 1 || is_infinite($stock_value_usd)) ? 0 : $stock_value_usd;

							//subtotal and group-----------------------
							$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];

							if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0)) {

								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								//for supplier
								if ($row[csf('is_within_group')] == 1) {
									$supplier_name = $companyArr[$row[csf('supplier_id')]];
								} else {
									$supplier_name = $supplierArr[$row[csf('supplier_id')]];
								}
								//end for supplier

								//number_format($stockInHand,2)
								if ($value_with == 1) {
									if (number_format($stockInHand, 2) > 0.00) {
										if (!in_array($check_string, $checkArr)) {
											$checkArr[$i] = $check_string;
											if ($i > 1) {
						?>
												<tr bgcolor="#CCCCCC" style="font-weight:bold">
													<td colspan="<? echo ($type == 5) ? 14 :  13; ?>" align="right">Sub Total</td>
													<td align="right"><? echo number_format($total_stock_in_hand, 2); ?></td>
													<td align="right"><? echo number_format($total_alocatted, 2);
																		$total_alocatted = 0; ?></td>
													<td align="right"><? echo number_format($total_free_stock, 2);
																		$total_free_stock = 0; ?></td>
													<?
													if ($type == 5) {
													?>
														<td align="right">&nbsp;</td>
														<td align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($sub_stock_value_usd, 2); ?></td>
													<?
													}
													?>

													<td align="right">&nbsp;</td>
													<td align="right">&nbsp;</td>
													<td align="right">&nbsp;</td>
												</tr>
										<?
												$total_stock_in_hand = 0;
												$sub_stock_value = 0;
												$sub_stock_value_usd = 0;
											}
										}

										/* $stock_value = $stockInHand * $avg_rate;
										$stock_value_usd = $stockInHand * $avg_rate_usd;
										//$avg_rate_usd = $avg_rate / $exchange_rate; */

										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="40"><? echo $i; ?></td>
											<td width="120">
												<p><? echo $companyArr[$row[csf("company_id")]]; ?></p>
											</td>
											<td width="60"><? echo $row[csf("id")]; ?></td>
											<td width="60">
												<p style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?>&nbsp;</p>
											</td>
											<td width="150">
												<p><? echo $compositionDetails; ?></p>
											</td>
											<td width="100">
												<p style="mso-number-format:'\@';"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></p>
											</td>
											<td width="80">
												<p><? echo $color_name_arr[$row[csf("color")]]; ?></p>
											</td>
											<td width="100">
												<p><? echo $mrr_info_arr[$row[csf("id")]]['mrr_recieved_last_date']; ?></p>
											</td>
											<td width="100">
												<p><? echo $mrr_info_arr[$row[csf("id")]]['mrr_issue_last_date']; ?></p>
											</td>
											<td width="100">
												<p><? echo number_format($mrr_info_arr[$row[csf("id")]]['mrr_total_recieved_quantity'], 2, ".", ""); ?></p>
											</td>
											<td width="100">
												<p style="mso-number-format:'\@';">
													<?
													if ($yarnTestArr[$row[csf("id")]] != "") { ?>
														<a href='##' onclick="show_test_report2('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?></a>
													<?
													} else if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") {
													?>
														<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"><? echo $row[csf("lot")]; ?></a>
														<?
													} else {
														echo $row[csf("lot")];
													}
														?>&nbsp;
												</p>
											</td>
											<td width="80">
												<p>
													<?
													echo $supplier_name;
													?></p>
											</td>
											<td width="100">
												<p>
													<?
													echo $brandArr[$row[csf("brand")]];
													?></p>
											</td>
											<td width="80">
												<? echo $buy_short_name_arr[min($rcv_trans_buyer_arr[$row[csf("id")]])]; ?>
											</td>
											<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><a href='##' onclick="openmypage_stock('<? echo $row[csf('id')] . "_" . $store_name ?>', 'stock_popup')"><? echo number_format($stockInHand, 2); ?></a></td>

											<td width="100" align="right">
												<p style="word-wrap:break-word; word-break: break-all;"><a href='##' onclick="openmypage('<? echo $row[csf('id')]; ?>','allocation_popup')"><? echo number_format($row[csf("allocated_qnty")], 2); ?></a></p>
											</td>
											<td width="100" align="right">
												<p style="word-wrap:break-word; word-break: break-all;"><? echo number_format($row[csf("available_qnty")], 2); ?></p>
											</td>
											<?
											if ($type == 5) {
											?>
												<td width="90" align="right" title="<? echo "op bal qnty=" . $openingBalance . "tot rcv qnty=" . $totalRcv . "op bal Amt" . $openingBalanceAmt . "tot_rcv Amt" . $totalRcvAmt . "Rate=" . $avg_rate . "=" . $exchange_rate; ?>"><? echo number_format($avg_rate_usd, 4); ?></td>


												<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($stock_value_usd, 2); ?></td>
											<?
											}
											?>

											<td width="50" align="right"><? echo $ageOfDays; ?></td>
											<td width="140" align="left">
												<p>
													<?
													if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") {
													?>
														<span class="comment more"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?> </span>
													<?
													}
													?>
												</p>
											</td>
											<td align="right">
												<p><? echo $remarks; ?></p>
											</td>
										</tr>
										<?
										$i++;

										$total_stock_in_hand += $stockInHand;
										$sub_stock_value += $stock_value;
										$sub_stock_value_usd += $stock_value_usd;

										$grand_total_stock_in_hand += $stockInHand;
										$tot_stock_value += $stock_value;
										$tot_stock_value_usd += $stock_value_usd;

										$total_alocatted += $row[csf("allocated_qnty")];
										$total_free_stock += $row[csf("available_qnty")];
										$grand_total_alocatted += $row[csf("allocated_qnty")];
										$grand_total_free_stock += $row[csf("available_qnty")];
									}
								} else {
									if (!in_array($check_string, $checkArr)) {
										$checkArr[$i] = $check_string;
										if ($i > 1) {
										?>
											<tr bgcolor="#CCCCCC" style="font-weight:bold">
												<td colspan="<? echo ($type == 5) ? 14 : 11; ?>" align="right">Sub Total</td>
												<td align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_stock_in_hand, 2); ?></td>
												<td align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_alocatted, 2);
																														$total_alocatted = 0; ?></td>
												<td align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_free_stock, 2);
																														$total_free_stock = 0; ?></td>

												<?
												if ($type == 5) {
												?>
													<td align="right">&nbsp;</td>
													<td align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($sub_stock_value_usd, 2); ?></td>
												<?
												}
												?>

												<td align="right">&nbsp;</td>
												<td align="right">&nbsp;</td>
												<td width="" align="right">&nbsp;</td>
											</tr>
									<?
											$total_stock_in_hand = 0;
											$sub_stock_value = 0;
											$sub_stock_value_usd = 0;
										}
									}

									/* $stock_value = $stockInHand * $avg_rate;
									$stock_value_usd = $stockInHand * $avg_rate_usd;

									$avg_rate_usd = (is_nan($avg_rate_usd) == 1 || is_infinite($avg_rate_usd)) ? 0 : $avg_rate_usd;
									$stock_value_usd = (is_nan($stock_value_usd) == 1 || is_infinite($stock_value_usd)) ? 0 : $stock_value_usd; */

									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="120">
											<p><? echo $companyArr[$row[csf("company_id")]]; ?>&nbsp;</p>
										</td>
										<td width="60"><? echo $row[csf("id")]; ?></td>
										<td width="60">
											<p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?>&nbsp;</p>
										</td>
										<td width="150">
											<p><? echo $compositionDetails; ?>&nbsp;</p>
										</td>
										<td width="100">
											<p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p>
										</td>
										<td width="80">
											<p><? echo $color_name_arr[$row[csf("color")]]; ?>&nbsp;</p>
										</td>
										<td width="100">
											<p><? echo max($receive_array[$row[csf("id")]]['last_receive_date']); ?></p>
										</td>
										<td width="100">
											<p><? echo max($issue_array[$row[csf("id")]]['last_issue_date']); ?></p>
										</td>
										<td width="100">
											<p><? echo $totalRcv; ?></p>
										</td>
										<td width="100">
											<p style="word-break:break-all;">
												<?
												if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") {
												?>
													<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
												<?
												} else {
													echo $row[csf("lot")];
												}
												?>
												&nbsp;
											</p>
										</td>
										<td width="80">
											<p>
												<?
												echo $supplier_name;
												?>
												&nbsp;
											</p>
										</td>
										<td width="100">
											<p>
												<?
												echo $brandArr[$row[csf("brand")]];
												?>
												&nbsp;
											</p>
										</td>
										<td width="80">
											<? echo $buy_short_name_arr[min($rcv_trans_buyer_arr[$row[csf("id")]])]; ?>
										</td>
										<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">
											<a href='##' onclick="openmypage_stock('<? echo $row[csf('id')] . "_" . $store_name ?>', 'stock_popup')"><? echo number_format($stockInHand, 2); ?></a>
										</td>

										<td width="100" align="right">
											<p style="word-wrap:break-word; word-break: break-all;"><a href='##' onclick="openmypage('<? echo $row[csf('id')]; ?>','allocation_popup')"><? echo number_format($row[csf("allocated_qnty")], 2); ?></a></p>
										</td>
										<td width="100" align="right">
											<p style="word-wrap:break-word; word-break: break-all;"><? echo number_format($row[csf("available_qnty")], 2); ?></p>
										</td>
										<?
										if ($type == 5) {
										?>
											<td width="90" align="right"><? echo number_format($avg_rate_usd, 4); ?></td>
											<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($stock_value_usd, 2); ?></td>
										<?
										}
										?>

										<td width="50" align="right"><? echo $ageOfDays; ?></td>
										<td width="140" align="left">
											<span class="comment more">
												<?
												if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") {
													echo $yarnTestQalityCommentsArr[$row[csf("id")]];
												}
												?>
											</span>
										</td>
										<td align="right">
											<p><? echo $remarks; ?></p>
										</td>
									</tr>
						<?
									$i++;
									$total_stock_in_hand += $stockInHand;
									$sub_stock_value += $stock_value;
									$sub_stock_value_usd += $stock_value_usd;

									$grand_total_stock_in_hand += $stockInHand;
									$tot_stock_value += $stock_value;
									$tot_stock_value_usd += $stock_value_usd;

									$total_alocatted += $row[csf("allocated_qnty")];
									$total_free_stock += $row[csf("available_qnty")];

									$grand_total_alocatted += $row[csf("allocated_qnty")];
									$grand_total_free_stock += $row[csf("available_qnty")];
								}
							}
						}
						?>
						<tr bgcolor="#CCCCCC" style="font-weight:bold">
							<td colspan="<? echo ($type == 5) ? 14 :  13; ?>" align="right">Sub Total</td>
							<td align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_stock_in_hand, 2); ?></td>
							<td align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_alocatted, 2);
																									$total_alocatted = 0; ?></td>
							<td align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_free_stock, 2);
																									$total_free_stock = 0; ?></td>

							<?
							if ($type == 5) {
							?>
								<td align="right">&nbsp;</td>
								<td align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($sub_stock_value_usd, 2); ?></td>
							<?
							}
							?>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
						</tr>
					</table>
				</div>
				<table width="<?php echo $tblWidth; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
					<tr class="tbl_bottom">

						<td width="40"></td>
						<td width="120"></td>
						<td width="60"></td>
						<td width="60"></td>
						<td width="150"></td>
						<td width="100"></td>
						<td width="80"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>

						<td width="80"></td>
						<td width="100"></td>
						<td width="80" align="right">Grand Total</td>
						<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 2); ?></td>

						<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($grand_total_alocatted, 2); ?></td>
						<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($grand_total_free_stock, 2); ?></td>
						<?
						if ($type == 5) {
						?>
							<td width="90" align="right">&nbsp;</td>
							<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($tot_stock_value_usd, 2); ?></td>
						<?
						}
						?>

						<td width="50" align="right">&nbsp;</td>
						<td width="140" align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</table>
			</div>
		<?
		} else if ($type == 7) {
			if ($value_with == 0)
				$stock_cond = "";
			else
				$stock_cond = " and a.current_stock>0";

			$count_arr = array();
			$sql = "select a.id, a.yarn_count_id, a.yarn_type from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond $stock_cond group by a.id, a.yarn_count_id, a.yarn_type order by a.yarn_count_id, a.yarn_type";
			$result = sql_select($sql);
			foreach ($result as $row) {

				$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
				$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
				$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
				$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

				$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
				$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
				$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;
				$stockInHand = ($openingBalance + $totalRcv) - $totalIssue;
				//$count_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]]+=$stockInHand;
				$count_arr[$row[csf("yarn_type")]][$row[csf("yarn_count_id")]] += $stockInHand;
				$header_arr[$row[csf("yarn_count_id")]] = $row[csf("yarn_count_id")];
			}
			//echo count($header_arr);
			$i = 1;
			//ob_start();
		?>
			<div style="margin-top:5px">
				<div style="max-height:350px" id="scroll_body">
					<table border="1" cellpadding="0" cellspacing="1" class="rpt_table" rules="all" id="table_header_1">
						<thead>
							<tr class="form_caption">
								<td align="center" colspan="<? echo round(count($header_arr)); ?>" style="border:none;font-size:16px; font-weight:bold">Count & Categoty Wise Yarn Stock</td>
							</tr>
							<tr>
								<td align="center" colspan="<? echo round(count($header_arr)); ?>" style="border:none;font-size:14px;">
									Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
								</td>
							</tr>
							<tr>
								<td align="center" colspan="<? echo round(count($header_arr)); ?>" style="border:none;font-size:12px;">
									<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
								</td>
							</tr>
							<tr>
								<th width="70">Yarn Type</th>

								<? foreach ($header_arr as $count_id => $count_id) { ?><th width="70"><? echo $yarn_count_arr[$count_id]; ?></th> <? } ?>
								<th width="70">Count Total</th>
							</tr>

						</thead>
						<tbody>
							<?
							$yarn_type_tot = array();
							foreach ($count_arr as $yarn_type_id => $value) {
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td> <? echo $yarn_type[$yarn_type_id] ?></td>
									<? foreach ($header_arr as $count_id => $type) { ?>
										<td> <?
												echo $count_arr[$yarn_type_id][$count_id];
												$yarn_type_tot[$yarn_type_id] += $count_arr[$yarn_type_id][$count_id];
												$count_tot[$count_id] += $count_arr[$yarn_type_id][$count_id];
												?></td>
									<? } ?>
								</tr>
							<?
								$i++;
							}
							?>
							<tr>
								<td>
									<b>Grand Total </b>
								</td>
								<? foreach ($header_arr as $count_id => $type) { ?>
									<td>
										<?
										echo $count_tot[$count_id];
										$GrandTotal += $count_tot[$count_id];
										?>
									</td>
								<? } ?>
								<td>
									<? echo $GrandTotal; ?>
								</td>
							</tr>
							<tr>
								<td><b>Percentage</b></td>
								<? foreach ($header_arr as $count_id => $type) { ?>
									<td>
										<?
										echo number_format(($count_tot[$count_id] / $GrandTotal) * 100, 2) . "%";
										?>
									</td>
								<? } ?>

							</tr>
						</tbody>
					</table>
				</div>
			<?
		} else if ($type == 8) {
			if ($show_val_column == 1) {
				$value_width = 400;
				$span = 2;
				$column = '<th rowspan="2" width="100">AVG Rate (TK)</th><th rowspan="2" width="100">Stock Value(Tk)</th><th rowspan="2" width="100">AVG Rate (USD)</th><th rowspan="2" width="100">Stock Value (USD)</th>';
			} else {
				$value_width = 0;
				$span = 0;
				$column = '';
			}

			if ($store_wise == 1) {
				$table_width = '3300';
				$colspan = '38';
				$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
			} else {
				$table_width = '3400';
				$colspan = '38';
			}

			if ($store_name != 0)  $store_cond = " and store_id in($store_name) ";
			if ($cbo_company_name != 0)  $company_cond = " and company_id=$cbo_company_name ";
			$date_array = array();
			$returnRes_date = "select prod_id, max(case when transaction_type in (1,5) and transaction_date <='$to_date' then transaction_date else null end) as min_date, max(case when transaction_date <='$to_date' then transaction_date else null end) as max_date, MAX ( CASE WHEN transaction_type =1 and transaction_date <='$to_date' THEN transaction_date ELSE NULL END) AS last_rcv_date, MAX ( CASE WHEN transaction_type in (1,2) and transaction_date <='$to_date' THEN transaction_date ELSE NULL END) AS last_transection_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 $company_cond $store_cond group by prod_id";
			//echo $returnRes_date;
			//die;

			$result_returnRes_date = sql_select($returnRes_date);
			foreach ($result_returnRes_date as $row) {
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
				$date_array[$row[csf("prod_id")]]['last_rcv_date'] = $row[csf("last_rcv_date")];
				$date_array[$row[csf("prod_id")]]['last_transection_date'] = $row[csf("last_transection_date")];
			}
			unset($result_returnRes_date);


			$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group
			from product_details_master a
			where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";

			// echo $search_cond; echo "here it is";

			$result = sql_select($sql);
			$i = 1;
			//ob_start();
			?>
				<div>
					<table width="<? echo $table_width + $value_width + 20; ?>" border="1" style="font:'Arial Narrow';" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
						<thead>
							<tr class="form_caption" style="border:none;">
								<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:16px; font-weight:bold">Daily Yarn Stock </td>
							</tr>
							<tr style="border:none;">
								<td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:14px;">
									Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
								</td>
							</tr>
							<tr style="border:none;">
								<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
									<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
								</td>
							</tr>
							<tr>
								<th rowspan="2" width="30">SL</th>
								<th rowspan="2" width="130">Company</th>
								<th colspan="7">Description</th>
								<th rowspan="2" width="100">Wgt. Bag/Cone</th>
								<th rowspan="2" width="100">Opening Rate</th>
								<th rowspan="2" width="110">OpeningStock</th>
								<th rowspan="2" width="120">Opening Value</th>
								<th colspan="7">Receive</th>
								<th colspan="7">Delivery</th>
								<th rowspan="2" width="100">Stock InHand</th>
								<?
								echo $column;
								if ($store_wise == 1) {
									echo '<th rowspan="2" width="100">Store Name</th>';
								} else {
									echo '<th rowspan="2" width="100">Allocated to Order</th>';
									echo '<th rowspan="2" width="100">Un Allocated Qty.</th>';
								}
								?>
								<th rowspan="2" width="50">Age (Days)</th>
								<th rowspan="2" width="50">DOH</th>
								<th rowspan="2" width="50">Last Receive Date</th>
								<th rowspan="2" width="50">Last Transection Date</th>
								<th rowspan="2" width="140">Yarn Quality<br>Comments</th>
								<th rowspan="2">Remarks</th>
							</tr>
							<tr>
								<th width="60">Prod.ID</th>
								<th width="60">Count</th>
								<th width="150">Composition</th>
								<th width="100">Yarn Type</th>
								<th width="120">Color</th>
								<th width="100">Lot</th>
								<th width="100">Supplier</th>
								<th width="90">Purchase</th>
								<th width="90">Inside Return</th>
								<th width="90">Outside Return</th>
								<th width="90">Transfer In</th>
								<th width="90">Loan</th>
								<th width="100">Total Recv</th>
								<th width="100">Total Recv Value</th>
								<th width="90">Inside</th>
								<th width="90">Outside</th>
								<th width="90">Recv. Return</th>
								<th width="90">Trans. Out</th>
								<th width="90">Loan</th>
								<th width="100">Total Delivery</th>
								<th width="100">Total Delivery value</th>
							</tr>
						</thead>
					</table>
					<div style="width:<? echo $table_width + $value_width + 20; ?>px; overflow-y:scroll; max-height:350px" id="scroll_body">
						<table width="<? echo $table_width + $value_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
							<?
							$tot_stock_value = 0;
							//echo $to_date.jshid;die;
							foreach ($result as $row) {
								$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d", strtotime($to_date)));
								$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d", strtotime($to_date)));

								$lastReceivedate = change_date_format($date_array[$row[csf("id")]]['last_rcv_date']);
								$lastTransDate = change_date_format($date_array[$row[csf("id")]]['last_transection_date']);

								$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
								if ($row[csf("yarn_comp_type2nd")] != 0)
									$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
								$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
								$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

								$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
								$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];
								$trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];
								$trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];

								$pay_mode = $receive_array[$row[csf("id")]]['pay_mode'];
								$receive_basis = $receive_array[$row[csf("id")]]['receive_basis'];

								$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
								$openingBalance = ($openingBalance <= 0) ? 0 : $openingBalance;


								$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;

								$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;

								$totalRcvValue = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];

								$totalIssueValue = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] + $issue_array[$row[csf("id")]]['issue_loan_amt'] + $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];

								$openingBalanceAmt = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt);
								$openingBalanceAmt = ($openingBalanceAmt <= 0) ? 0 : $openingBalanceAmt;

								$totalRcvAmt = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];

								$tot_rcv_amt = $openingBalanceAmt + $totalRcvAmt;
								$closing_value = $tot_rcv_amt - $totalIssueValue;
								$tot_rcv_qnty = $openingBalance + $totalRcv;

								$openingAmount = $openingBalanceAmt;
								$openingAmount = ($openingBalance == 0) ? 0 : $openingAmount;

								$openingRate = 0;
								if ($openingAmount != 0 && $openingBalance != 0) {
									$openingRate = $openingAmount / $openingBalance;
								}

								$stockInHand = $openingBalance + $totalRcv - $totalIssue;
								$stockInHand = ($stockInHand <= 0) ? 0 : $stockInHand;
								$closing_value = ($stockInHand == 0) ? 0 : $closing_value;
								$avg_rate = 0;
								if ($closing_value != 0 && $stockInHand != 0) {
									$avg_rate = $closing_value / $stockInHand;
								}

								//echo $value_with."<br>";
								//subtotal and group-----------------------							
								$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];

								//for supplier
								if ($row[csf('is_within_group')] == 1) {
									$supplier_name = $companyArr[$row[csf('supplier_id')]];
								} else {
									$supplier_name = $supplierArr[$row[csf('supplier_id')]];
								}
								//end for supplier

								if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0)) {

									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";

									//echo $value_with.testdd;die;
									if ($value_with == 1) {
										if (number_format($stockInHand, 2, '.', '') > 0.00) {
											if (!in_array($check_string, $checkArr)) {
												$checkArr[$i] = $check_string;
												if ($i > 1) {
							?>
													<tr bgcolor="#CCCCCC" style="font-weight:bold">
														<td colspan="10" align="right">Sub Total</td>
														<td width="100"></td>
														<td width="110" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_opening_balance, 2); ?></td>
														<td width="120" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_openingBalanceAmt, 2); ?></td>
														<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_purchase, 2); ?></td>
														<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_inside_return, 2); ?></td>
														<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_outside_return, 2); ?></td>
														<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_transfer_in_qty, 2); ?></td>
														<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_rcv_loan, 2); ?></td>
														<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_total_rcv, 2); ?></td>
														<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_totalRcvValue, 2); ?></td>
														<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_issue_inside, 2); ?></td>
														<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_issue_outside, 2); ?></td>
														<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_receive_return, 2); ?></td>
														<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_transfer_out_qty, 2); ?></td>
														<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_issue_loan, 2); ?></td>
														<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_total_delivery, 2); ?></td>
														<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_totalIssueValue, 2); ?></td>
														<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_stock_in_hand, 2); ?></td>
														<?
														if ($show_val_column == 1) {
															echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">  &nbsp;</td>';
															echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($sub_stock_value, 2) . '</td>';
															echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"> &nbsp; </td>';
															echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($sub_stock_value_usd, 2) . '</td>';
															//echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
														}

														if ($store_wise == 1) {
															echo '<td width="100" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
														} else {
															if ($allocated_qty_variable_settings == 1) {
																echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_alocatted, 2) . '</td>';
																echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_free_stock, 2) . '</td>';
															} else {
																echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;"align="right">&nbsp;</td>';
																echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">&nbsp;</td>';
															}
														}
														?>
														<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
														<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
														<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
														<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
														<td width="140" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
														<td align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
													</tr>
											<?
													$total_opening_balance = 0;
													$total_openingBalanceAmt = 0;
													$total_purchase = 0;
													$total_inside_return = 0;
													$total_outside_return = 0;
													$total_rcv_loan = 0;
													$total_total_rcv = 0;
													$total_totalRcvValue = 0;
													$total_issue_inside = 0;
													$total_issue_outside = 0;
													$total_receive_return = 0;
													$total_issue_loan = 0;
													$total_total_delivery = 0;
													$total_totalIssueValue = 0;
													$total_stock_in_hand = 0;
													$total_alocatted = 0;
													$total_free_stock = 0;
													$sub_stock_value = 0;
													$sub_stock_value_usd = 0;
													$total_transfer_out_qty = 0;
													$total_transfer_in_qty = 0;
												}
											}
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td width="30"><? echo $i; ?></td>
												<td width="130" style="word-wrap:break-word; word-break: break-all;">
													<p><? echo $companyArr[$row[csf("company_id")]]; ?></p>
												</td>
												<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $row[csf("id")]; ?></td>
												<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
												<td width="150" style="word-wrap:break-word;word-wrap:break-word:150px;"><? echo $compositionDetails; ?></td>
												<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
												<td width="100" style="word-wrap:break-word; word-break: break-all;">
													<p>
														<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
															<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
														<? } else {
															echo $row[csf("lot")];
														}
														?>
														&nbsp;
													</p>
												</td>
												<td width="100" style="word-wrap:break-word; word-break: break-all;">
													<?
													//commented dated 16.09.2021
													/*if($receive_basis==2) // work order basis
												{
													if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
													{
														echo $companyArr[$row[csf("supplier_id")]];
													}else{
														echo $supplierArr[$row[csf("supplier_id")]];
													}
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}*/
													echo $supplier_name;
													?>
												</td>
												<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo 'Bg:' . max($receive_array[$row[csf("id")]]['weight_per_bag']) . '; ' . 'Cn:' . max($receive_array[$row[csf("id")]]['weight_per_cone']); ?></td>
												<td width="100" style="word-wrap:break-word; word-break: break-all; text-align: right;">
													<p><? echo number_format($openingRate, 2); ?></p>
												</td>
												<td width="110" style="word-wrap:break-word; word-break: break-all;" align="right" title="<? echo "rcv=" . $receive_array[$row[csf("id")]]['rcv_total_opening'] . ",tr_in=" . $trans_in_total_opening . ",iss=" . $issue_array[$row[csf("id")]]['issue_total_opening'] . ",tr_out=" . $trans_out_total_opening; ?>"><? echo number_format($openingBalance, 2); ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all; text-align: right;" title="<? echo "rece_tot=" . $receive_array[$row[csf("id")]]['rcv_total_opening_amt'] . ",tra_in=" . $trans_in_total_opening_amt . ",iss_tot=" . $issue_array[$row[csf("id")]]['issue_total_opening_amt'] . ",tr_out=" . $trans_out_total_opening_amt; ?>">
													<p><? echo number_format($openingAmount, 2); ?></p>
												</td>
												<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_in_qty, 2);
													}
													?>
												</td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
												<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalRcv, 2); ?></td>
												<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalRcvValue, 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_out_qty, 2);
													}
													?>
												</td>
												<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
												<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalIssue, 2); ?></td>
												<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalIssueValue, 2) ?></td>
												<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($stockInHand, 2); ?></td>
												<?
												$stock_value = 0;
												if ($show_val_column == 1) {
													/* $avg_rate=$totalRcvValue/$totalRcv;*/
													$avg_rate = ((number_format($stockInHand, 2) <= 0) || is_nan($avg_rate) == 1 || is_infinite($avg_rate)) ? 0 : $avg_rate;

													$stock_value = floatval($stockInHand) * (float)$avg_rate;
													//var_dump($stock_value);
													$stock_value = (is_nan($stock_value) == 1 || is_infinite($stock_value)) ? 0 : $stock_value;

													$avg_rate_usd = $avg_rate / $exchange_rate;
													$avg_rate_usd = (is_nan($avg_rate_usd) == 1 || is_infinite($avg_rate_usd)) ? 0 : $avg_rate_usd;

													$stock_value_usd = $stock_value / $exchange_rate;
													$stock_value_usd = (is_nan($stock_value_usd) == 1 || is_infinite($stock_value_usd)) ? 0 : $stock_value_usd;

													echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($avg_rate, 2) . '</td>';
													echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($closing_value, 2) . '</td>';
													echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($avg_rate_usd, 2);
													'</td>';
													echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($stock_value_usd, 2) . '</td>';
												}

												if ($store_wise == 1) {
													$store_name = '';
													$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
													$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];

													if (!empty($receive_store_id) && !empty($issue_store_id)) {
														$store_id_arr = array_unique(array_merge($receive_store_id, $issue_store_id));
													} else {
														if (!empty($receive_store_id)) {
															$store_id_arr = array_unique($receive_store_id);
														} else {
															$store_id_arr = array_unique($issue_store_id);
														}
													}

													//echo "tesf";
													//print_r($store_id_arr);

													foreach ($store_id_arr as $key => $val) {
														if ($store_name == "")
															$store_name = $store_arr[$val];
														else
															$store_name .= ", " . $store_arr[$val];
													}

													echo '<td width="100" style="word-wrap:break-word; word-break: break-all;">' . $store_name . '</td>';
												} else {
													if ($allocated_qty_variable_settings == 1) {
														echo "<td width='100' align='right' style='word-wrap:break-word; word-break: break-all;' ><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
														echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($row[csf("available_qnty")], 2) . '</td>';
													} else {
														echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
														echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
													}
												}
												?>
												<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo $ageOfDays; //$ageOfDays; 
																																	?></td>
												<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;"><?
																																	if ($stockInHand > 0)
																																		echo $daysOnHand;
																																	else
																																		echo "&nbsp;"; //$daysOnHand;
																																	?></td>
												<td width="50" align="left" style="word-wrap:break-word; word-break: break-all;"><? echo $lastReceivedate; ?></td>
												<td width="50" align="left" style="word-wrap:break-word; word-break: break-all;"><? echo $lastTransDate; ?></td>
												<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
													<td width="140" align="left"><span class="comment more"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
												<?  } else { ?>
													<td width="140" align="left"><span class="comment more"><? echo "&nbsp;"; ?></span></td>
												<? 	} ?>


												<td width=""></td>
											</tr>
											<?
											$i++;

											$total_opening_balance += $openingBalance;
											$total_openingBalanceAmt += $openingBalanceAmt;
											$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
											$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
											$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
											$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
											$total_total_rcv += $totalRcv;
											$total_totalRcvValue += $totalRcvValue;
											$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
											$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
											$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
											$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
											$total_total_delivery += $totalIssue;
											$total_totalIssueValue += $totalIssueValue;
											$total_stock_in_hand += $stockInHand;
											$total_alocatted += $row[csf("allocated_qnty")];
											$total_free_stock += $row[csf("available_qnty")];
											$sub_stock_value += $closing_value;
											$sub_stock_value_usd += $stock_value_usd;
											$total_transfer_out_qty += $transfer_out_qty;
											$total_transfer_in_qty += $transfer_in_qty;

											//grand total===========================
											$grand_total_opening_balance += $openingBalance;
											$grand_total_opening_amount_value += $openingAmount;
											$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
											$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
											$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
											$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
											$grand_total_total_rcv += $totalRcv;
											$grand_total_rcv_amount_value += $totalRcvValue; //$totalIssueValue
											$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
											$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
											$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
											$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
											$grand_total_total_delivery += $totalIssue;
											$grand_total_issue_amount_value += $totalIssueValue;
											$grand_total_stock_in_hand += $stockInHand;
											$grand_total_alocatted += $row[csf("allocated_qnty")];
											$grand_total_free_stock += $row[csf("available_qnty")];
											$tot_stock_value += $closing_value;
											$tot_stock_value_usd += $stock_value_usd;

											$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
											$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
										}
									} else {
										if (number_format($stockInHand, 2, '.', '') <= 0.00) {
											if (number_format($openingBalance, 2, '.', '') != 0 || number_format($receive_array[$row[csf("id")]]['purchase'], 2, '.', '') != 0 || number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2, '.', '') != 0 || number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2, '.', '') != 0 || number_format($transfer_in_qty, 2, '.', '') != 0 || number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2, '.', '') != 0 || number_format($issue_array[$row[csf("id")]]['issue_inside'], 2, '.', '') != 0 || number_format($issue_array[$row[csf("id")]]['issue_outside'], 2, '.', '') != 0 || number_format($issue_array[$row[csf("id")]]['rcv_return'], 2, '.', '') != 0 || number_format($transfer_out_qty, 2, '.', '') != 0 || number_format($issue_array[$row[csf("id")]]['issue_loan'], 2, '.', '') != 0) {
												if (!in_array($check_string, $checkArr)) {
													$checkArr[$i] = $check_string;
													if ($i > 1) {
											?>
														<tr bgcolor="#CCCCCC" style="font-weight:bold">
															<td colspan="10" align="right">Sub Total</td>
															<td width="100"></td>
															<td width="110" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_opening_balance, 2); ?></td>
															<td width="120" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_openingBalanceAmt, 2); ?></td>
															<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_purchase, 2); ?></td>
															<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_inside_return, 2); ?></td>
															<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_outside_return, 2); ?></td>
															<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_transfer_in_qty, 2); ?></td>
															<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_rcv_loan, 2); ?></td>
															<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_total_rcv, 2); ?></td>
															<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_totalRcvValue, 2); ?></td>
															<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_issue_inside, 2); ?></td>
															<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_issue_outside, 2); ?></td>
															<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_receive_return, 2); ?></td>
															<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_transfer_out_qty, 2); ?></td>
															<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_issue_loan, 2); ?></td>
															<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_total_delivery, 2); ?></td>
															<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_totalIssueValue, 2); ?></td>
															<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_stock_in_hand, 2); ?></td>
															<?
															if ($show_val_column == 1) {
																echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">  &nbsp;</td>';
																echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($sub_stock_value, 2) . '</td>';
																echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"> &nbsp; </td>';
																echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($sub_stock_value_usd, 2) . '</td>';
																//echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
															}

															if ($store_wise == 1) {
																echo '<td width="100" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
															} else {
																if ($allocated_qty_variable_settings == 1) {
																	echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_alocatted, 2) . '</td>';
																	echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_free_stock, 2) . '</td>';
																} else {
																	echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;"align="right">&nbsp;</td>';
																	echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">&nbsp;</td>';
																}
															}
															?>
															<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
															<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
															<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
															<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
															<td width="140" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
															<td align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
														</tr>
												<?
														$total_opening_balance = 0;
														$total_openingBalanceAmt = 0;
														$total_purchase = 0;
														$total_inside_return = 0;
														$total_outside_return = 0;
														$total_rcv_loan = 0;
														$total_total_rcv = 0;
														$total_totalRcvValue = 0;
														$total_issue_inside = 0;
														$total_issue_outside = 0;
														$total_receive_return = 0;
														$total_issue_loan = 0;
														$total_total_delivery = 0;
														$total_totalIssueValue = 0;
														$total_stock_in_hand = 0;
														$total_alocatted = 0;
														$total_free_stock = 0;
														$sub_stock_value = 0;
														$sub_stock_value_usd = 0;
														$total_transfer_out_qty = 0;
														$total_transfer_in_qty = 0;
													}
												}

												if ($store_wise == 1) {
													$store_name = '';
													$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
													$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];

													if (!empty($receive_store_id) && !empty($issue_store_id)) {
														$store_id_arr = array_unique(array_merge($receive_store_id, $issue_store_id));
													} else {
														if (!empty($receive_store_id)) {
															$store_id_arr = array_unique($receive_store_id);
														} else {
															$store_id_arr = array_unique($issue_store_id);
														}
													}

													// echo "<pre>";
													// print_r($store_id_arr);
													$flag = 0;

													foreach ($store_id_arr as $key => $val) {
														if ($val == $store_id) {
															$flag = 1;
														}
														if ($store_name == "")
															$store_name = $store_arr[$val];
														else
															$store_name .= ", " . $store_arr[$val];
													}
												}
												if ($store_id == 0) $flag = 1;
												if ($flag != 1) {
													continue;
												}



												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
													<td width="30"><? echo $i; ?></td>
													<td width="130" style="word-wrap:break-word; word-break: break-all;">
														<p><? echo $companyArr[$row[csf("company_id")]]; ?></p>
													</td>
													<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $row[csf("id")]; ?></td>
													<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
													<td width="150" style="word-wrap:break-word;word-wrap:break-word:150px;"><? echo $compositionDetails; ?></td>
													<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
													<td width="100" style="word-wrap:break-word; word-break: break-all;">
														<p>
															<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
																<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
															<? } else {
																echo $row[csf("lot")];
															}
															?>
															&nbsp;
														</p>
													</td>
													<td width="100" style="word-wrap:break-word; word-break: break-all;">
														<?
														//commented dated 16.09.2021
														/*if($receive_basis==2) // work order basis
												{
													if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
													{
														echo $companyArr[$row[csf("supplier_id")]];
													}else{
														echo $supplierArr[$row[csf("supplier_id")]];
													}
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}*/
														echo $supplier_name;
														?>
													</td>
													<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo 'Bg:' . max($receive_array[$row[csf("id")]]['weight_per_bag']) . '; ' . 'Cn:' . max($receive_array[$row[csf("id")]]['weight_per_cone']); ?></td>
													<td width="100" style="word-wrap:break-word; word-break: break-all; text-align: right;">
														<p><? echo number_format($openingRate, 2); ?></p>
													</td>
													<td width="110" style="word-wrap:break-word; word-break: break-all;" align="right" title="<? echo "rcv=" . $receive_array[$row[csf("id")]]['rcv_total_opening'] . ",tr_in=" . $trans_in_total_opening . ",iss=" . $issue_array[$row[csf("id")]]['issue_total_opening'] . ",tr_out=" . $trans_out_total_opening; ?>"><? echo number_format($openingBalance, 2); ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all; text-align: right;" title="<? echo "rece_tot=" . $receive_array[$row[csf("id")]]['rcv_total_opening_amt'] . ",tra_in=" . $trans_in_total_opening_amt . ",iss_tot=" . $issue_array[$row[csf("id")]]['issue_total_opening_amt'] . ",tr_out=" . $trans_out_total_opening_amt; ?>">
														<p><? echo number_format($openingAmount, 2); ?></p>
													</td>
													<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
													<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
													<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
													<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">
														<?
														if ($store_wise == 1) {
															echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
														} else {
															echo number_format($transfer_in_qty, 2);
														}
														?>
													</td>
													<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
													<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalRcv, 2); ?></td>
													<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalRcvValue, 2); ?></td>
													<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
													<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
													<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
													<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">
														<?
														if ($store_wise == 1) {
															echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
														} else {
															echo number_format($transfer_out_qty, 2);
														}
														?>
													</td>
													<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
													<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalIssue, 2); ?></td>
													<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalIssueValue, 2) ?></td>
													<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($stockInHand, 2); ?></td>
													<?
													$stock_value = 0;
													if ($show_val_column == 1) {
														/* $avg_rate=$totalRcvValue/$totalRcv;*/
														$avg_rate = ((number_format($stockInHand, 2) <= 0) || is_nan($avg_rate) == 1 || is_infinite($avg_rate)) ? 0 : $avg_rate;
														$stock_value = floatval($stockInHand) * (float)$avg_rate;
														//var_dump($stock_value);
														$stock_value = (is_nan($stock_value) == 1 || is_infinite($stock_value)) ? 0 : $stock_value;

														$avg_rate_usd = $avg_rate / $exchange_rate;
														$avg_rate_usd = (is_nan($avg_rate_usd) == 1 || is_infinite($avg_rate_usd)) ? 0 : $avg_rate_usd;

														$stock_value_usd = $stock_value / $exchange_rate;
														$stock_value_usd = (is_nan($stock_value_usd) == 1 || is_infinite($stock_value_usd)) ? 0 : $stock_value_usd;



														echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($avg_rate, 2) . '</td>';
														echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($closing_value, 2) . '</td>';
														echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($avg_rate_usd, 2);
														'</td>';
														echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($stock_value_usd, 2) . '</td>';
													}

													if ($store_wise == 1) {
														// $store_name = '';
														// $receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
														// $issue_store_id = $issue_array[$row[csf("id")]]['store_id'];

														// if(!empty($receive_store_id) && !empty($issue_store_id))
														// {
														// 	$store_id_arr = array_unique(array_merge($receive_store_id, $issue_store_id));
														// }
														// else{
														// 	if(!empty($receive_store_id))
														// 	{
														// 		$store_id_arr = array_unique($receive_store_id);
														// 	}
														// 	else{
														// 		$store_id_arr = array_unique($issue_store_id);
														// 	}
														// }

														// // echo "<pre>";
														// // print_r($store_id_arr);

														// foreach ($store_id_arr as $key=>$val) 
														// {
														// 	if ($store_name == "")
														// 		$store_name = $store_arr[$val];
														// 	else
														// 		$store_name .= ", " . $store_arr[$val];
														// } 

														echo '<td width="100" style="word-wrap:break-word; word-break: break-all;">' . $store_name . '</td>';
													} else {
														if ($allocated_qty_variable_settings == 1) {
															echo "<td width='100' align='right' style='word-wrap:break-word; word-break: break-all;' ><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
															echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($row[csf("available_qnty")], 2) . '</td>';
														} else {
															echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
															echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
														}
													}
													?>
													<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo $ageOfDays; //$ageOfDays; 
																																		?></td>
													<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;"><?
																																		if ($stockInHand > 0)
																																			echo $daysOnHand;
																																		else
																																			echo "&nbsp;"; //$daysOnHand;
																																		?></td>
													<td width="50" align="left" style="word-wrap:break-word; word-break: break-all;"><? echo $lastReceivedate; ?></td>
													<td width="50" align="left" style="word-wrap:break-word; word-break: break-all;"><? echo $lastTransDate; ?></td>

													<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
														<td width="140" align="left"><span class="comment more"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
													<?  } else { ?>
														<td width="140" align="left"><span class="comment more"><? echo "&nbsp;"; ?></span></td>
													<? 	} ?>


													<td width=""></td>
												</tr>
												<?
												$i++;

												$total_opening_balance += $openingBalance;
												$total_openingBalanceAmt += $openingBalanceAmt;
												$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
												$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
												$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
												$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
												$total_total_rcv += $totalRcv;
												$total_totalRcvValue += $totalRcvValue;
												$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
												$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
												$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
												$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
												$total_total_delivery += $totalIssue;
												$total_totalIssueValue += $totalIssueValue;
												$total_stock_in_hand += $stockInHand;
												$total_alocatted += $row[csf("allocated_qnty")];
												$total_free_stock += $row[csf("available_qnty")];
												$sub_stock_value += $closing_value;
												$sub_stock_value_usd += $stock_value_usd;
												$total_transfer_out_qty += $transfer_out_qty;
												$total_transfer_in_qty += $transfer_in_qty;

												//grand total===========================
												$grand_total_opening_balance += $openingBalance;
												$grand_total_opening_amount_value += $openingAmount;
												$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
												$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
												$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
												$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
												$grand_total_total_rcv += $totalRcv;
												$grand_total_rcv_amount_value += $totalRcvValue; //$totalIssueValue
												$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
												$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
												$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
												$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
												$grand_total_total_delivery += $totalIssue;
												$grand_total_issue_amount_value += $totalIssueValue;
												$grand_total_stock_in_hand += $stockInHand;
												$grand_total_alocatted += $row[csf("allocated_qnty")];
												$grand_total_free_stock += $row[csf("available_qnty")];
												$tot_stock_value += $closing_value;
												$tot_stock_value_usd += $stock_value_usd;

												$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
												$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
											}
										} elseif (number_format($stockInHand, 2, '.', '') >= 0.00) {
											if (!in_array($check_string, $checkArr)) {
												$checkArr[$i] = $check_string;
												if ($i > 1) {
												?>
													<tr bgcolor="#CCCCCC" style="font-weight:bold">
														<td colspan="10" align="right">Sub Total</td>
														<td width="100"></td>
														<td width="110" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
														<td width="120" align="right"><? echo number_format($total_openingBalanceAmt, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
														<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
														<td width="100" align="right"><? echo number_format($total_totalRcvValue, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
														<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
														<td width="100" align="right"><? echo number_format($total_totalIssueValue, 2); ?></td>
														<td width="100" align="right"><? echo number_format($total_stock_in_hand, 2); ?></td>
														<?
														if ($show_val_column == 1) {
															echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">  &nbsp;</td>';
															echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($sub_stock_value, 2) . '</td>';
															echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"> &nbsp; </td>';
															echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($sub_stock_value_usd, 2) . '</td>';
															//echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
														}

														if ($store_wise == 1) {
															echo '<td width="100" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
														} else {
															if ($allocated_qty_variable_settings == 1) {
																echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_alocatted, 2) . '</td>';
																echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_free_stock, 2) . '</td>';
															} else {
																echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;"align="right">&nbsp;</td>';
																echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">&nbsp;</td>';
															}
														}
														?>
														<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
														<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
														<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
														<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
														<td width="140" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
														<td align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
													</tr>
											<?
													$total_opening_balance = 0;
													$total_openingBalanceAmt = 0;
													$total_purchase = 0;
													$total_inside_return = 0;
													$total_outside_return = 0;
													$total_rcv_loan = 0;
													$total_total_rcv = 0;
													$total_totalRcvValue = 0;
													$total_issue_inside = 0;
													$total_issue_outside = 0;
													$total_receive_return = 0;
													$total_issue_loan = 0;
													$total_total_delivery = 0;
													$total_totalIssueValue = 0;
													$total_stock_in_hand = 0;
													$total_alocatted = 0;
													$total_free_stock = 0;
													$sub_stock_value = 0;
													$sub_stock_value_usd = 0;
													$total_transfer_out_qty = 0;
													$total_transfer_in_qty = 0;
												}
											}

											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td width="30"><? echo $i; ?></td>
												<td width="130" style="word-wrap:break-word; word-break: break-all;">
													<p><? echo $companyArr[$row[csf("company_id")]]; ?></p>
												</td>
												<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $row[csf("id")]; ?></td>
												<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
												<td width="150" style="word-wrap:break-word;word-wrap:break-word:150px;"><? echo $compositionDetails; ?></td>
												<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
												<td width="100" style="word-wrap:break-word; word-break: break-all;">
													<p>
														<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
															<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
														<? } else {
															echo $row[csf("lot")];
														}
														?>
														&nbsp;
													</p>
												</td>
												<td width="100" style="word-wrap:break-word; word-break: break-all;">
													<?
													//commented dated 16.09.2021
													/*if($receive_basis==2) // work order basis
											{
												if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
												{
													echo $companyArr[$row[csf("supplier_id")]];
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}
											}else{
												echo $supplierArr[$row[csf("supplier_id")]];
											}*/
													echo $supplier_name;
													?>
												</td>
												<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo 'Bg:' . max($receive_array[$row[csf("id")]]['weight_per_bag']) . '; ' . 'Cn:' . max($receive_array[$row[csf("id")]]['weight_per_cone']); ?></td>
												<td width="100" style="word-wrap:break-word; word-break: break-all; text-align: right;">
													<p><? echo number_format($openingRate, 2); ?></p>
												</td>
												<td width="110" style="word-wrap:break-word; word-break: break-all;" align="right" title="<? echo "rcv=" . $receive_array[$row[csf("id")]]['rcv_total_opening'] . ",tr_in=" . $trans_in_total_opening . ",iss=" . $issue_array[$row[csf("id")]]['issue_total_opening'] . ",tr_out=" . $trans_out_total_opening; ?>"><? echo number_format($openingBalance, 2); ?></td>
												<td width="100" style="word-wrap:break-word; word-break: break-all; text-align: right;" title="<? echo "rece_tot=" . $receive_array[$row[csf("id")]]['rcv_total_opening_amt'] . ",tra_in=" . $trans_in_total_opening_amt . ",iss_tot=" . $issue_array[$row[csf("id")]]['issue_total_opening_amt'] . ",tr_out=" . $trans_out_total_opening_amt; ?>">
													<p><? echo number_format($openingAmount, 2); ?></p>
												</td>
												<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_in_qty, 2);
													}
													?>
												</td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
												<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalRcv, 2); ?></td>
												<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalRcvValue, 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_out_qty, 2);
													}
													?>
												</td>
												<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
												<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalIssue, 2); ?></td>
												<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalIssueValue, 2) ?></td>
												<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($stockInHand, 2); ?></td>
												<?
												$stock_value = 0;
												if ($show_val_column == 1) {
													/* $avg_rate=$totalRcvValue/$totalRcv;*/
													$avg_rate = ((number_format($stockInHand, 2) <= 0) || is_nan($avg_rate) == 1 || is_infinite($avg_rate)) ? 0 : $avg_rate;
													$stock_value = floatval($stockInHand) * (float)$avg_rate;
													//var_dump($stock_value);
													$stock_value = (is_nan($stock_value) == 1 || is_infinite($stock_value)) ? 0 : $stock_value;

													$avg_rate_usd = $avg_rate / $exchange_rate;
													$avg_rate_usd = (is_nan($avg_rate_usd) == 1 || is_infinite($avg_rate_usd)) ? 0 : $avg_rate_usd;

													$stock_value_usd = $stock_value / $exchange_rate;
													$stock_value_usd = (is_nan($stock_value_usd) == 1 || is_infinite($stock_value_usd)) ? 0 : $stock_value_usd;

													echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($avg_rate, 2) . '</td>';
													echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($closing_value, 2) . '</td>';
													echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($avg_rate_usd, 2);
													'</td>';
													echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($stock_value_usd, 2) . '</td>';
												}

												if ($store_wise == 1) {
													$store_name = '';
													$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
													$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];

													if (!empty($receive_store_id) && !empty($issue_store_id)) {
														$store_id_arr = array_unique(array_merge($receive_store_id, $issue_store_id));
													} else {
														if (!empty($receive_store_id)) {
															$store_id_arr = array_unique($receive_store_id);
														} else {
															$store_id_arr = array_unique($issue_store_id);
														}
													}

													//echo "tesf";
													//print_r($store_id_arr);

													foreach ($store_id_arr as $key => $val) {
														if ($store_name == "")
															$store_name = $store_arr[$val];
														else
															$store_name .= ", " . $store_arr[$val];
													}

													echo '<td width="100" style="word-wrap:break-word; word-break: break-all;">' . $store_name . '</td>';
												} else {
													if ($allocated_qty_variable_settings == 1) {
														echo "<td width='100' align='right' style='word-wrap:break-word; word-break: break-all;' ><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
														echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($row[csf("available_qnty")], 2) . '</td>';
													} else {
														echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
														echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
													}
												}
												?>
												<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo $ageOfDays; ?></td>
												<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;"><?
																																	if ($stockInHand > 0)
																																		echo $daysOnHand;
																																	else
																																		echo "&nbsp;"; //$daysOnHand;
																																	?></td>
												<td width="50" align="left" style="word-wrap:break-word; word-break: break-all;"><? echo $lastReceivedate; ?></td>
												<td width="50" align="left" style="word-wrap:break-word; word-break: break-all;"><? echo $lastTransDate; ?></td>

												<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
													<td width="140" align="left"><span class="comment more"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
												<?  } else { ?>
													<td width="140" align="left"><span class="comment more"><? echo "&nbsp;"; ?></span></td>
												<? 	} ?>


												<td width=""></td>
											</tr>
							<?
											$i++;

											$total_opening_balance += $openingBalance;
											$total_openingBalanceAmt += $openingBalanceAmt;
											$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
											$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
											$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
											$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
											$total_total_rcv += $totalRcv;
											$total_totalRcvValue += $totalRcvValue;
											$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
											$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
											$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
											$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
											$total_total_delivery += $totalIssue;
											$total_totalIssueValue += $totalIssueValue;
											$total_stock_in_hand += $stockInHand;
											$total_alocatted += $row[csf("allocated_qnty")];
											$total_free_stock += $row[csf("available_qnty")];
											$sub_stock_value += $closing_value;
											$sub_stock_value_usd += $stock_value_usd;
											$total_transfer_out_qty += $transfer_out_qty;
											$total_transfer_in_qty += $transfer_in_qty;

											//grand total===========================
											$grand_total_opening_balance += $openingBalance;
											$grand_total_opening_amount_value += $openingAmount;
											$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
											$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
											$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
											$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
											$grand_total_total_rcv += $totalRcv;
											$grand_total_rcv_amount_value += $totalRcvValue; //$totalIssueValue
											$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
											$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
											$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
											$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
											$grand_total_total_delivery += $totalIssue;
											$grand_total_issue_amount_value += $totalIssueValue;
											$grand_total_stock_in_hand += $stockInHand;
											$grand_total_alocatted += $row[csf("allocated_qnty")];
											$grand_total_free_stock += $row[csf("available_qnty")];
											$tot_stock_value += $closing_value;
											$tot_stock_value_usd += $stock_value_usd;

											$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
											$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
										}
									}
								}
							}
							?>
							<tr bgcolor="#CCCCCC" style="font-weight:bold">
								<td colspan="10" align="right">Sub Total</td>
								<td width="100"></td>
								<td width="110" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_opening_balance, 2); ?></td>
								<td width="120" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_openingBalanceAmt, 2); ?></td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_purchase, 2); ?></td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_inside_return, 2); ?></td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_outside_return, 2); ?></td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_transfer_in_qty, 2); ?></td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_rcv_loan, 2); ?></td>
								<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_total_rcv, 2); ?></td>
								<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_totalRcvValue, 2); ?></td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_issue_inside, 2); ?></td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_issue_outside, 2); ?></td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_receive_return, 2); ?></td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_transfer_out_qty, 2); ?></td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_issue_loan, 2); ?></td>
								<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_total_delivery, 2); ?></td>
								<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_totalIssueValue, 2); ?></td>
								<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($total_stock_in_hand, 2); ?></td>
								<?
								if ($show_val_column == 1) {
									echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">  &nbsp;</td>';
									echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($sub_stock_value, 2) . '</td>';
									echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"> &nbsp; </td>';
									echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($sub_stock_value_usd, 2) . '</td>';
								}

								if ($store_wise == 1) {
									echo '<td width="100" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
								} else {
									if ($allocated_qty_variable_settings == 1) {
										echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_alocatted, 2) . '</td>';
										echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_free_stock, 2) . '</td>';
									} else {
										echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;"align="right">&nbsp;</td>';
										echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">&nbsp;</td>';
									}
								}
								?>
								<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
								<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
								<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
								<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
								<td width="140" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
								<td align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
							</tr>
						</table>
					</div>
					<table width="<? echo $table_width + $value_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
						<tr class="tbl_bottom">
							<td width="30"></td>
							<td width="130"></td>
							<td width="60"></td>
							<td width="60"></td>
							<td width="150"></td>
							<td width="100"></td>
							<td width="120"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100" align="right">Grand Total</td>
							<td width="100"></td>
							<td width="110" align="right" id="value_total_opening_balance" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($grand_total_opening_balance, 2); ?></td>
							<td width="120" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($grand_total_opening_amount_value, 2); ?></td>
							<td width="90" align="right" id="value_total_purchase" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($grand_total_purchase, 2); ?></td>
							<td width="90" align="right" id="value_total_inside_return" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($grand_total_inside_return, 2); ?></td>
							<td width="90" align="right" id="value_total_outside_return" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($grand_total_outside_return, 2); ?></td>
							<td width="90" align="right" id="value_total_transfer_in" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($grand_total_transfer_in_qty, 2); ?></td>
							<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_rcv_loan"><? echo number_format($grand_total_rcv_loan, 2); ?></td>
							<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_total_rcv"><? echo number_format($grand_total_total_rcv, 2); ?></td>
							<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($grand_total_rcv_amount_value, 2); ?></td>
							<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_issue_inside"><? echo number_format($grand_total_issue_inside, 2); ?></td>
							<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_issue_outside"><? echo number_format($grand_total_issue_outside, 2); ?></td>
							<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_receive_return"><? echo number_format($grand_total_receive_return, 2); ?></td>
							<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_transfer_out"><? echo number_format($grand_total_transfer_out_qty, 2); ?></td>
							<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_issue_loan"><? echo number_format($grand_total_issue_loan, 2); ?></td>
							<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_total_delivery"><? echo number_format($grand_total_total_delivery, 2); ?></td>
							<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($grand_total_issue_amount_value, 2); ?></td>
							<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 2); ?></td>
							<?
							if ($show_val_column == 1) {
								echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" >&nbsp;</td>';
								echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" >' . number_format($tot_stock_value, 2) . '</td>';
								echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" >&nbsp;</td>';
								echo '<td width="100" align="right"style="word-wrap:break-word; word-break: break-all;"  >' . number_format($tot_stock_value_usd, 2) . '</td>';
							}

							if ($store_wise == 1) {
								echo '<td width="100">&nbsp;</td>';
							} else {
								if ($allocated_qty_variable_settings == 1) {
									echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"  id="value_total_alocatted" >' . number_format($grand_total_alocatted, 2) . '</td>';
									echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"  id="value_total_free_stock" >' . number_format($grand_total_free_stock, 2) . '</td>';
								} else {
									echo '<td width="100" align="right" id="value_total_alocatted">&nbsp;</td>';
									echo '<td width="100" align="right" id="value_total_free_stock">&nbsp;</td>';
								}
							}
							?>
							<td width="50" align="right">&nbsp;</td>
							<td width="50" align="right">&nbsp;</td>
							<td width="50" align="right">&nbsp;</td>
							<td width="50" align="right">&nbsp;</td>
							<td width="140" align="right">&nbsp;</td>
							<td width="" align="right">&nbsp;</td>
						</tr>
					</table>
				</div>
			<?
		} else if ($type == 9) {

			if ($show_val_column == 1) {
				$value_width = 300;
				$span = 3;
				$column = '<th rowspan="2" width="90">Avg. Rate (USD)</th>
				<th rowspan="2" width="100">Stock Value (USD)</th>';
			} else {
				$value_width = 0;
				$span = 0;
				$column = '';
			}

			if ($store_name != 0)  $store_cond = " and store_id in($store_name) ";
			if ($cbo_company_name != 0)  $company_cond = " and company_id=$cbo_company_name ";
			$date_array = array();
			$returnRes_date = "select prod_id, MIN(case when transaction_type in (1,4,5) and BALANCE_QNTY>0 then transaction_date else null end) as min_date, max(transaction_date) as max_date 
			from inv_transaction 
			where is_deleted=0 and status_active=1 and item_category=1 $company_cond $store_cond group by prod_id";
			$result_returnRes_date = sql_select($returnRes_date);
			foreach ($result_returnRes_date as $row) {
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
			}

			$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group
			from product_details_master a
			where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";

			//echo $sql;
			//die;//echo count($result);
			$result = sql_select($sql);
			$i = 1;
			//ob_start();
			?>
				<div>
					<table width="<? echo $table_width + 20; ?>" border="1" style="font:'Arial Narrow';" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
						<thead>
							<tr class="form_caption" style="border:none;">
								<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:16px; font-weight:bold">Daily Yarn Stock </td>
							</tr>
							<tr style="border:none;">
								<td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:14px;">
									Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
								</td>
							</tr>
							<tr style="border:none;">
								<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
									<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
								</td>
							</tr>
							<tr>
								<th rowspan="2" width="30">SL</th>
								<th rowspan="2" width="130">Company</th>
								<th colspan="7">Description</th>
								<th rowspan="2" width="100">Wgt. Bag/Cone</th>
								<th rowspan="2" width="110">OpeningStock</th>
								<th colspan="6">Receive</th>
								<th colspan="6">Delivery</th>
								<th rowspan="2" width="100">Stock InHand</th>
								<?
								echo $column;
								if ($store_wise == 1) {
									echo '<th rowspan="2" width="100">Store Name</th>';
								} else {
									echo '<th rowspan="2" width="100">Allocated to Order</th>';
									echo '<th rowspan="2" width="100">Un Allocated Qty.</th>';
								}
								?>
								<th rowspan="2" width="50">Age (Days)</th>
								<th rowspan="2" width="50">DOH</th>
								<th rowspan="2" width="140">Yarn Quality<br>Comments</th>
								<th rowspan="2">Remarks</th>
							</tr>
							<tr>
								<th width="60">Prod.ID</th>
								<th width="60">Count</th>
								<th width="150">Composition</th>
								<th width="100">Yarn Type</th>
								<th width="120">Color</th>
								<th width="100">Lot</th>
								<th width="100">Supplier</th>
								<th width="90">Purchase</th>
								<th width="90">Inside Return</th>
								<th width="90">Outside Return</th>
								<th width="90">Transfer In</th>
								<th width="90">Loan</th>
								<th width="100">Total Recv</th>
								<th width="90">Inside</th>
								<th width="90">Outside</th>
								<th width="90">Recv. Return</th>
								<th width="90">Trans. Out</th>
								<th width="90">Loan</th>
								<th width="100">Total Delivery</th>
							</tr>
						</thead>
					</table>
					<div style="width:<? echo $table_width + 20; ?>px; overflow-y:scroll; max-height:350px" id="scroll_body">
						<table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
							<?
							$tot_stock_value = 0;
							foreach ($result as $row) {
								$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
								$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));

								$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
								if ($row[csf("yarn_comp_type2nd")] != 0)
									$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
								$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
								$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

								$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
								$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

								$pay_mode = $receive_array[$row[csf("id")]]['pay_mode'];
								$receive_basis = $receive_array[$row[csf("id")]]['receive_basis'];

								$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);

								$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
								$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;

								$stockInHand = $openingBalance + $totalRcv - $totalIssue;

								//subtotal and group-----------------------
								$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];

								if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0)) {
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";

									//for supplier
									if ($row[csf('is_within_group')] == 1) {
										$supplier_name = $companyArr[$row[csf('supplier_id')]];
									} else {
										$supplier_name = $supplierArr[$row[csf('supplier_id')]];
									}
									//end for supplier

									if ($value_with == 1) {
										if (number_format($stockInHand, 2) > 0.00) {

											if (!in_array($check_string, $checkArr)) {
												$checkArr[$i] = $check_string;
												if ($i > 1) {
							?>
													<tr bgcolor="#CCCCCC" style="font-weight:bold">
														<td colspan="10" align="right">Sub Total</td>
														<td width="110" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
														<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
														<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
														<td width="100" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
														<?
														if ($show_val_column == 1) {
															echo '<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
															echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($sub_stock_value_usd, 2) . '</td>';
														}

														if ($store_wise == 1) {
															echo '<td width="100" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
														} else {
															if ($allocated_qty_variable_settings == 1) {
																echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_alocatted, 2) . '</td>';
																echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_free_stock, 2) . '</td>';
															} else {
																echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;"align="right">&nbsp;</td>';
																echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">&nbsp;</td>';
															}
														}
														?>
														<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
														<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
														<td align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
														<td width="" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
													</tr>
											<?
													$total_opening_balance = 0;
													$total_purchase = 0;
													$total_inside_return = 0;
													$total_outside_return = 0;
													$total_rcv_loan = 0;
													$total_total_rcv = 0;
													$total_issue_inside = 0;
													$total_issue_outside = 0;
													$total_receive_return = 0;
													$total_issue_loan = 0;
													$total_total_delivery = 0;
													$total_stock_in_hand = 0;
													$total_alocatted = 0;
													$total_free_stock = 0;
													$sub_stock_value = 0;
													$sub_stock_value_usd = 0;
													$total_transfer_out_qty = 0;
													$total_transfer_in_qty = 0;
												}
											}
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td width="30"><? echo $i; ?></td>
												<td width="130" style="word-wrap:break-word; word-break: break-all;">
													<p><? echo $companyArr[$row[csf("company_id")]]; ?></p>
												</td>
												<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $row[csf("id")]; ?></td>
												<td width="60" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
												<td width="150" style="word-wrap:break-word;word-wrap:break-word:150px;"><? echo $compositionDetails; ?></td>
												<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
												<td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@';">
													<p>
														<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
															<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
														<? } else {
															echo $row[csf("lot")];
														}
														?>
														&nbsp;
													</p>
												</td>
												<td width="100" style="word-wrap:break-word; word-break: break-all;">
													<?
													//commented dated 16.09.2021
													/*if($receive_basis==2) // work order basis
											{
												if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
												{
													echo $companyArr[$row[csf("supplier_id")]];
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}
											}else{
												echo $supplierArr[$row[csf("supplier_id")]];
											}*/
													echo $supplier_name;
													?>
												</td>
												<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo 'Bg:' . max($receive_array[$row[csf("id")]]['weight_per_bag']) . '; ' . 'Cn:' . max($receive_array[$row[csf("id")]]['weight_per_cone']); ?></td>
												<td width="110" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($openingBalance, 2); ?></td>
												<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_in_qty, 2);
													}
													?>
												</td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
												<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalRcv, 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
												<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_out_qty, 2);
													}
													?>
												</td>
												<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
												<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalIssue, 2); ?></td>
												<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($stockInHand, 4); ?></td>
												<?
												$stock_value = 0;
												if ($show_val_column == 1) {
													$avg_rate_per_unit_usd = $row[csf("avg_rate_per_unit")] / $exchange_rate;
													$stock_value = $stockInHand * $row[csf("avg_rate_per_unit")];
													$stock_value_usd = ($stockInHand * $row[csf("avg_rate_per_unit")]) / $exchange_rate;
													echo '<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($avg_rate_per_unit_usd, 2) . '</td>';

													echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($stock_value_usd, 2) . '</td>';
												}

												if ($store_wise == 1) {
													$store_name = '';
													$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
													$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];
													$store_id_arr = array_unique(array_merge($receive_store_id, $issue_store_id));
													foreach ($store_id_arr as $key => $val) {
														if ($store_name == "")
															$store_name = $store_arr[$val];
														else
															$store_name .= ", " . $store_arr[$val];
													}
													echo '<td width="100" style="word-wrap:break-word; word-break: break-all;">' . $store_name . '</td>';
												} else {
													if ($allocated_qty_variable_settings == 1) {
														echo "<td width='100' align='right' style='word-wrap:break-word; word-break: break-all;' ><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
														echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($row[csf("available_qnty")], 2) . '</td>';
													} else {
														echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
														echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
													}
												}
												?>
												<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo $ageOfDays; //$ageOfDays;         
																																	?></td>
												<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;"><?
																																	if ($stockInHand > 0)
																																		echo $daysOnHand;
																																	else
																																		echo "&nbsp;"; //$daysOnHand;
																																	?>
												</td>
												<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
													<td width="140" align="left"><span class="comment more"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
												<?  } else { ?>
													<td width="140" align="left"><span class="comment more"><? echo "&nbsp;"; ?></span></td>
												<? 	} ?>
												<td width="" align="center" style="word-wrap:break-word; word-break: break-all;">
													<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a>
												</td>
											</tr>
											<?
											$i++;

											$total_opening_balance += $openingBalance;
											$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
											$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
											$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
											$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
											$total_total_rcv += $totalRcv;
											$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
											$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
											$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
											$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
											$total_total_delivery += $totalIssue;
											$total_stock_in_hand += $stockInHand;
											$total_alocatted += $row[csf("allocated_qnty")];
											$total_free_stock += $row[csf("available_qnty")];
											$sub_stock_value += $stock_value;
											$sub_stock_value_usd += $stock_value_usd;
											$total_transfer_out_qty += $transfer_out_qty;
											$total_transfer_in_qty += $transfer_in_qty;

											//grand total===========================
											$grand_total_opening_balance += $openingBalance;
											$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
											$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
											$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
											$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
											$grand_total_total_rcv += $totalRcv;
											$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
											$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
											$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
											$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
											$grand_total_total_delivery += $totalIssue;
											$grand_total_stock_in_hand += $stockInHand;
											$grand_total_alocatted += $row[csf("allocated_qnty")];
											$grand_total_free_stock += $row[csf("available_qnty")];
											$tot_stock_value += $stock_value;
											$tot_stock_value_usd += $stock_value_usd;

											$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
											$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
										}
									} else {

										if (!in_array($check_string, $checkArr)) {
											$checkArr[$i] = $check_string;
											if ($i > 1) {
											?>
												<tr bgcolor="#CCCCCC" style="font-weight:bold">
													<td colspan="10" align="right">Sub Total</td>
													<td width="110" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
													<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
													<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
													<td width="100" align="right"><? echo number_format($total_stock_in_hand, 2); ?></td>
													<?
													if ($show_val_column == 1) {
														echo '<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
														echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($sub_stock_value_usd, 2) . '</td>';
													}

													if ($store_wise == 1) {
														echo '<td width="100" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
													} else {
														if ($allocated_qty_variable_settings == 1) {
															echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_alocatted, 2) . '</td>';
															echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_free_stock, 2) . '</td>';
														} else {
															echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;"align="right">&nbsp;</td>';
															echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">&nbsp;</td>';
														}
													}
													?>
													<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
													<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
													<td align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
													<td width="" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
												</tr>
										<?
												$total_opening_balance = 0;
												$total_purchase = 0;
												$total_inside_return = 0;
												$total_outside_return = 0;
												$total_rcv_loan = 0;
												$total_total_rcv = 0;
												$total_issue_inside = 0;
												$total_issue_outside = 0;
												$total_receive_return = 0;
												$total_issue_loan = 0;
												$total_total_delivery = 0;
												$total_stock_in_hand = 0;
												$total_alocatted = 0;
												$total_free_stock = 0;
												$sub_stock_value = 0;
												$sub_stock_value_usd = 0;
												$total_transfer_out_qty = 0;
												$total_transfer_in_qty = 0;
											}
										}
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30"><? echo $i; ?></td>
											<td width="130" style="word-wrap:break-word; word-break: break-all;">
												<p><? echo $companyArr[$row[csf("company_id")]]; ?></p>
											</td>
											<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $row[csf("id")]; ?></td>
											<td width="60" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
											<td width="150" style="word-wrap:break-word;word-wrap:break-word:150px;"><? echo $compositionDetails; ?></td>
											<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
											<td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@';">
												<p>
													<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
														<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
													<? } else {
														echo $row[csf("lot")];
													}
													?>
													&nbsp;
												</p>
											</td>
											<td width="100" style="word-wrap:break-word; word-break: break-all;">
												<?
												//commented dated 16.09.2021
												/*if($receive_basis==2) // work order basis
											{
												if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
												{
													echo $companyArr[$row[csf("supplier_id")]];
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}
											}else{
												echo $supplierArr[$row[csf("supplier_id")]];
											}*/
												echo $supplier_name;
												?>
											</td>
											<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo 'Bg:' . max($receive_array[$row[csf("id")]]['weight_per_bag']) . '; ' . 'Cn:' . max($receive_array[$row[csf("id")]]['weight_per_cone']); ?></td>
											<td width="110" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($openingBalance, 2); ?></td>
											<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
											<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
											<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
											<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">
												<?
												if ($store_wise == 1) {
													echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
												} else {
													echo number_format($transfer_in_qty, 2);
												}
												?>
											</td>
											<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
											<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalRcv, 2); ?></td>
											<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
											<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
											<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
											<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">
												<?
												if ($store_wise == 1) {
													echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
												} else {
													echo number_format($transfer_out_qty, 2);
												}
												?>
											</td>
											<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
											<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalIssue, 2); ?></td>
											<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($stockInHand, 4); ?></td>
											<?
											$stock_value = 0;
											if ($show_val_column == 1) {
												$avg_rate_per_unit_usd = $row[csf("avg_rate_per_unit")] / $exchange_rate;
												$stock_value = $stockInHand * $row[csf("avg_rate_per_unit")];
												$stock_value_usd = ($stockInHand * $row[csf("avg_rate_per_unit")]) / $exchange_rate;

												echo '<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($avg_rate_per_unit_usd, 2) . '</td>';

												echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($stock_value_usd, 2) . '</td>';
											}

											if ($store_wise == 1) {
												$store_name = '';
												$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
												$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];
												$store_id_arr = array_unique(array_merge($receive_store_id, $issue_store_id));
												foreach ($store_id_arr as $key => $val) {
													if ($store_name == "")
														$store_name = $store_arr[$val];
													else
														$store_name .= ", " . $store_arr[$val];
												}
												echo '<td width="100" style="word-wrap:break-word; word-break: break-all;">' . $store_name . '</td>';
											} else {
												if ($allocated_qty_variable_settings == 1) {
													echo "<td width='100' align='right' style='word-wrap:break-word; word-break: break-all;' ><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
													echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($row[csf("available_qnty")], 2) . '</td>';
												} else {
													echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
													echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
												}
											}
											?>
											<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo $ageOfDays; //$ageOfDays;         
																																?></td>
											<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;"><?
																																if ($stockInHand > 0)
																																	echo $daysOnHand;
																																else
																																	echo "&nbsp;"; //$daysOnHand;
																																?></td>

											<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
												<td width="140" align="left"><span class="comment more"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
											<?  } else { ?>
												<td width="140" align="left"><span class="comment more"></span></td>
											<? 	} ?>

											<td width="" align="center" style="word-wrap:break-word; word-break: break-all;">
												<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a>
											</td>
										</tr>
							<?
										$i++;

										$total_opening_balance += $openingBalance;
										$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
										$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
										$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
										$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
										$total_total_rcv += $totalRcv;
										$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
										$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
										$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
										$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
										$total_total_delivery += $totalIssue;
										$total_stock_in_hand += $stockInHand;
										$total_alocatted += $row[csf("allocated_qnty")];
										$total_free_stock += $row[csf("available_qnty")];
										$sub_stock_value += $stock_value;
										$sub_stock_value_usd += $stock_value_usd;
										$total_transfer_out_qty += $transfer_out_qty;
										$total_transfer_in_qty += $transfer_in_qty;

										//grand total===========================
										$grand_total_opening_balance += $openingBalance;
										$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
										$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
										$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
										$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
										$grand_total_total_rcv += $totalRcv;
										$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
										$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
										$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
										$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
										$grand_total_total_delivery += $totalIssue;
										$grand_total_stock_in_hand += $stockInHand;
										$grand_total_alocatted += $row[csf("allocated_qnty")];
										$grand_total_free_stock += $row[csf("available_qnty")];
										$tot_stock_value += $stock_value;
										$tot_stock_value_usd += $stock_value_usd;

										$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
										$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
									}
								}
							}
							?>
							<tr bgcolor="#CCCCCC" style="font-weight:bold">
								<td colspan="10" align="right">Sub Total</td>
								<td width="110" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
								<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
								<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
								<td width="100" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
								<?
								if ($show_val_column == 1) {
									echo '<td width="90" align="right">&nbsp;</td>';
									echo '<td width="100" align="right">' . number_format($sub_stock_value_usd, 2) . '</td>';
								}

								if ($store_wise == 1) {
									echo '<td width="100">&nbsp;</td>';
								} else {
									if ($allocated_qty_variable_settings == 1) {
										echo '<td width="100" align="right">' . number_format($total_alocatted, 2) . '</td>';
										echo '<td width="100" align="right">' . number_format($total_free_stock, 2) . '</td>';
									} else {
										echo '<td width="100" align="right">&nbsp;</td>';
										echo '<td width="100" align="right">&nbsp;</td>';
									}
								}
								?>
								<td width="50" align="right">&nbsp;</td>
								<td width="50" align="right">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td width="" align="right">&nbsp;</td>
							</tr>
						</table>
					</div>
					<table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
						<tr class="tbl_bottom">
							<td width="30"></td>
							<td width="130"></td>
							<td width="60"></td>
							<td width="60"></td>
							<td width="150"></td>
							<td width="100"></td>
							<td width="120"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100" align="right">Grand Total</td>
							<td width="110" align="right" id="value_total_opening_balance"><? echo number_format($grand_total_opening_balance, 2); ?></td>
							<td width="90" align="right" id="value_total_purchase"><? echo number_format($grand_total_purchase, 2); ?></td>
							<td width="90" align="right" id="value_total_inside_return"><? echo number_format($grand_total_inside_return, 2); ?></td>
							<td width="90" align="right" id="value_total_outside_return"><? echo number_format($grand_total_outside_return, 2); ?></td>
							<td width="90" align="right" id="value_total_transfer_in"><? echo number_format($grand_total_transfer_in_qty, 2); ?></td>
							<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_rcv_loan"><? echo number_format($grand_total_rcv_loan, 2); ?></td>
							<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_total_rcv"><? echo number_format($grand_total_total_rcv, 2); ?></td>
							<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_issue_inside"><? echo number_format($grand_total_issue_inside, 2); ?></td>
							<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_issue_outside"><? echo number_format($grand_total_issue_outside, 2); ?></td>
							<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_receive_return"><? echo number_format($grand_total_receive_return, 2); ?></td>
							<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_transfer_out"><? echo number_format($grand_total_transfer_out_qty, 2); ?></td>
							<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_issue_loan"><? echo number_format($grand_total_issue_loan, 2); ?></td>
							<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_total_delivery"><? echo number_format($grand_total_total_delivery, 2); ?></td>
							<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 4); ?></td>
							<?
							if ($show_val_column == 1) {
								echo '<td width="90" align="right">&nbsp;</td>';
								echo '<td width="100" align="right">' . number_format($tot_stock_value_usd, 2) . '</td>';
							}

							if ($store_wise == 1) {
								echo '<td width="100">&nbsp;</td>';
							} else {
								if ($allocated_qty_variable_settings == 1) {
									echo '<td width="100" align="right" id="value_total_alocatted">' . number_format($grand_total_alocatted, 2) . '</td>';
									echo '<td width="100" align="right" id="value_total_free_stock">' . number_format($grand_total_free_stock, 2) . '</td>';
								} else {
									echo '<td width="100" align="right" id="value_total_alocatted">&nbsp;</td>';
									echo '<td width="100" align="right" id="value_total_free_stock">&nbsp;</td>';
								}
							}
							?>
							<td width="50" align="right">&nbsp;</td>
							<td width="50" align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td width="" align="right">&nbsp;</td>
						</tr>
					</table>
				</div>
			<?
		} else if ($type == 11) {
			/*---------- count & composition grouping summary ----
			Made by : Md Didarul Alam
			Date: 06/02/2021
			Requsition By: Narban Group
			/*--------------------------------------------------*/

			$count_lib_array = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 $yarn_count_ids_cond and is_deleted=0", 'id', 'yarn_count');

			if ($value_with == 0)
				$search_cond .= "";
			else
				$search_cond .= "  and a.current_stock>0";

			//$sql = "select a.id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_type2nd from product_details_master a, inv_transaction b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.entry_form=1 and b.transaction_type=1 $company_cond $search_cond group by a.id, a.yarn_type,a.yarn_count_id,a.yarn_comp_type1st,a.yarn_comp_type2nd order by a.yarn_type, a.yarn_count_id";

			$sql = "select a.id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_type2nd, a.allocated_qnty, a.available_qnty from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_type2nd, a.allocated_qnty, a.available_qnty order by a.yarn_type, a.yarn_count_id";
			$type_arr = array();
			$result = sql_select($sql);
			foreach ($result as $row) {
				//$pipe_line_qty=0;
				$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
				$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

				$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
				$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

				$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
				$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $receive_array[$row[csf("id")]]['rcv_adjustment_qty'] + $transfer_in_qty;
				$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $issue_array[$row[csf("id")]]['issue_adjustment_qty'] + $transfer_out_qty;
				$stockInHand = ($openingBalance + $totalRcv) - $totalIssue;

				if ($row[csf("yarn_comp_type2nd")] != 0) {
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_type2nd")];
				} else {
					$composition_val = $row[csf("yarn_comp_type1st")];
				}

				$counts_arr[$count_lib_array[$row[csf("yarn_count_id")]]]['stock_in_hand'] += $stockInHand;
				$counts_arr[$count_lib_array[$row[csf("yarn_count_id")]]]['allocated_qnty'] += $row[csf("allocated_qnty")];
				$counts_arr[$count_lib_array[$row[csf("yarn_count_id")]]]['available_qnty'] += $row[csf("available_qnty")];

				$count_composition_arr[$count_lib_array[$row[csf("yarn_count_id")]]][$composition_val]['stock_in_hand'] += $stockInHand;
				$count_composition_arr[$count_lib_array[$row[csf("yarn_count_id")]]][$composition_val]['allocated_qnty'] += $row[csf("allocated_qnty")];
				$count_composition_arr[$count_lib_array[$row[csf("yarn_count_id")]]][$composition_val]['available_qnty'] += $row[csf("available_qnty")];
			}

			ksort($count_composition_arr);
			?>
				<div style="width:1000px; margin: 0 auto;">
					<table width="802" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
						<thead>
							<tr class="form_caption" style="border:none;">
								<td colspan="3" align="center" style="border:none;font-size:16px; font-weight:bold">Count & Composition Wise Yarn Stock Summary Report</td>
							</tr>
							<tr style="border:none;">
								<td colspan="3" align="center" style="border:none; font-size:14px;">
									Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
								</td>
							</tr>
							<tr style="border:none;">
								<td colspan="3" align="center" style="border:none;font-size:12px; font-weight:bold">
									<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
								</td>
							</tr>
							<tr>
								<th width="40%">Count/Composition</th>
								<th width="20%">Stock In Hand</th>
								<th width="20%">Allocated to Order</th>
								<th width="20%">Un Allocated Qty.</th>
							</tr>
						</thead>
					</table>
					<div style="width:820px; overflow-y:scroll; max-height:350px" id="scroll_body">
						<table width="802" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
							<?
							$tot_stock_qty = 0;
							$i = 1;
							foreach ($count_composition_arr as $count => $composition_arr) {
								if (!in_array($count, $counts_arr)) {
							?>
									<tr bgcolor="#CCCCCC">
										<td width="40%" style="padding-left:5px;">
											<b>
												<?php
												//$captionData = $count_lib_array[$count];
												$captionData = $count;
												echo $captionData;
												?>
											</b>
										</td>
										<td width="20%" align="right" style="padding-right:5px;">
											<b>
												<?php
												echo number_format($counts_arr[$count]['stock_in_hand'], 2);
												?>
											</b>
										</td>
										<td width="20%" align="right" style="padding-right:5px;"><b><? echo number_format($counts_arr[$count]['allocated_qnty'], 2); ?></b></td>
										<td width="20%" align="right" style="padding-right:5px;"><b><? echo number_format($counts_arr[$count]['available_qnty'], 2); ?></b></td>
									</tr>
								<?
								}

								foreach ($composition_arr as $compo => $stock_qty) {
									$ex_comp = explode('**', $compo);
									$comp_1type = $ex_comp[0];
									$comp_2type = $ex_comp[1];
									$fullCompositionValue = "";
									if ($comp_2type != 0) {
										$fullCompositionValue = $composition[$comp_1type] . ',' . $composition[$comp_2type];
									} else {
										$fullCompositionValue = $composition[$comp_1type];
									}

									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";

								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40%" style="padding-left:5px;">
											<p><? echo $fullCompositionValue; ?>&nbsp;</p>
										</td>
										<td width="20%" align="right" style="padding-right:5px;"><? echo number_format($stock_qty['stock_in_hand'], 2); ?></td>
										<td width="20%" align="right" style="padding-right:5px;"><? echo number_format($stock_qty['allocated_qnty'], 2); ?></td>
										<td width="20%" align="right" style="padding-right:5px;"><? echo number_format($stock_qty['available_qnty'], 2); ?></td>
									</tr>
							<?
									$i++;
									$tot_stock_qty += $stock_qty['stock_in_hand'];
									$tot_allocated_qty += $stock_qty['allocated_qnty'];
									$tot_available_qty += $stock_qty['available_qnty'];
								}
							}
							?>
						</table>
					</div>
					<table width="802" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
						<tr class="tbl_bottom">
							<td width="40%" style="padding-right:5px;">Total</td>
							<td width="20%" align="right" style="padding-right:5px;"><? echo number_format($tot_stock_qty, 2); ?></td>
							<td width="20%" align="right" style="padding-right:5px;"><? echo number_format($tot_allocated_qty, 2); ?></td>
							<td width="20%" align="right" style="padding-right:5px;"><? echo number_format($tot_available_qty, 2); ?></td>
						</tr>
					</table>
				</div>
			<?
			//$countWiseStock
		} else if ($type == 19) {
			/*---------- count & composition & lot grouping summary ----
			Made by : Md Didarul Alam
			Date: 05/02/2024
			Requsition By: Hames Group
			/*--------------------------------------------------*/

			$count_lib_array = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 $yarn_count_ids_cond and is_deleted=0", 'id', 'yarn_count');

			if ($value_with == 0)
				$search_cond .= "";
			else
				$search_cond .= "  and a.current_stock>0";

			$prod_sql = "select a.id,a.lot,yarn_type,a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_type2nd, a.allocated_qnty, a.available_qnty from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond";
			//$prod_sql//die;

			$prod_result = sql_select($prod_sql);
			$data_arr = array();
			$transfer_in_qty = $transfer_out_qty = $trans_out_total_opening = $trans_in_total_opening = $openingBalance = $totalRcv = $transfer_in_qty = $stockInHand = 0;

			$transfer_in_qty_old_stock = $transfer_out_qty_old_stock = $trans_out_total_opening_old_stock = $trans_in_total_opening_old_stock = $openingBalance_old_stock = $totalRcv_old_stock = $transfer_in_qty_old_stock = $stockInHand_old_stock = 0;

			$transfer_in_qty_new_stock = $transfer_out_qty_new_stock = $trans_out_total_opening_new_stock = $trans_in_total_opening_new_stock = $openingBalance_new_stock = $totalRcv_new_stock = $transfer_in_qty_new_stock = $stockInHand_new_stock = 0;
			foreach ($prod_result as $row) {
				//$pipe_line_qty=0;

				/** Stock In hand <td> */
				$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
				$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

				$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
				$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

				$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
				$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $receive_array[$row[csf("id")]]['rcv_adjustment_qty'] + $transfer_in_qty;
				$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $issue_array[$row[csf("id")]]['issue_adjustment_qty'] + $transfer_out_qty;
				$stockInHand = ($openingBalance + $totalRcv) - $totalIssue;
				/** Stock In hand End <td> */

				/** Old Stock In hand ($text_days>) <td> */
				$transfer_in_qty_old_stock = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty_old_stock'];
				$transfer_out_qty_old_stock = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty_old_stock'];

				$trans_out_total_opening_old_stock = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_old_stock'];
				$trans_in_total_opening_old_stock = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_old_stock'];

				$openingBalance_old_stock = ($receive_array[$row[csf("id")]]['rcv_total_opening_old_stock'] + $trans_in_total_opening_old_stock) - ($issue_array[$row[csf("id")]]['issue_total_opening_old_stock'] + $trans_out_total_opening_old_stock);

				$totalRcv_old_stock = $receive_array[$row[csf("id")]]['purchase_old_stock'] + $receive_array[$row[csf("id")]]['rcv_inside_return_old_stock'] + $receive_array[$row[csf("id")]]['rcv_outside_return_old_stock'] + $receive_array[$row[csf("id")]]['rcv_loan_old_stock'] + $receive_array[$row[csf("id")]]['rcv_adjustment_qty_old_stock'] + $transfer_in_qty_old_stock;

				$totalIssue_old_stock = $issue_array[$row[csf("id")]]['issue_inside_old_stock'] + $issue_array[$row[csf("id")]]['issue_outside_old_stock'] + $issue_array[$row[csf("id")]]['rcv_return_old_stock'] + $issue_array[$row[csf("id")]]['issue_loan_old_stock'] + $issue_array[$row[csf("id")]]['issue_adjustment_qty_old_stock'] + $transfer_out_qty_old_stock;
				$stockInHand_old_stock = ($openingBalance_old_stock + $totalRcv_old_stock) - $totalIssue_old_stock;
				/** Old Stock In hand ($text_days>)End <td> */


				/** New Stock In hand ($text_days>) <td> */
				$transfer_in_qty_new_stock = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty_new_stock'];
				$transfer_out_qty_new_stock = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty_new_stock'];

				$trans_out_total_opening_new_stock = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_new_stock'];
				$trans_in_total_opening_new_stock = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_new_stock'];

				$openingBalance_new_stock = ($receive_array[$row[csf("id")]]['rcv_total_opening_new_stock'] + $trans_in_total_opening_new_stock) - ($issue_array[$row[csf("id")]]['issue_total_opening_new_stock'] + $trans_out_total_opening_new_stock);

				$totalRcv_new_stock = $receive_array[$row[csf("id")]]['purchase_new_stock'] + $receive_array[$row[csf("id")]]['rcv_inside_return_new_stock'] + $receive_array[$row[csf("id")]]['rcv_outside_return_new_stock'] + $receive_array[$row[csf("id")]]['rcv_loan_new_stock'] + $receive_array[$row[csf("id")]]['rcv_adjustment_qty_new_stock'] + $transfer_in_qty_new_stock;

				$totalIssue_new_stock = $issue_array[$row[csf("id")]]['issue_inside_new_stock'] + $issue_array[$row[csf("id")]]['issue_outside_new_stock'] + $issue_array[$row[csf("id")]]['rcv_return_new_stock'] + $issue_array[$row[csf("id")]]['issue_loan_new_stock'] + $issue_array[$row[csf("id")]]['issue_adjustment_qty_new_stock'] + $transfer_out_qty_new_stock;
				$stockInHand_new_stock = ($openingBalance_new_stock + $totalRcv_new_stock) - $totalIssue_new_stock;
				/** New Stock In hand ($text_days>)End <td> */


				$yarn_count_id = $row[csf("yarn_count_id")];
				$composition_id = $row[csf("yarn_comp_type1st")];
				$yarn_type_id = $row[csf("yarn_type")];
				$prod_id = $row[csf("id")];
				$lot = $row[csf("lot")];

				$data_arr[$prod_id]['yarn_count_id'] = $yarn_count_id;
				$data_arr[$prod_id]['composition_id'] = $composition_id;
				$data_arr[$prod_id]['yarn_type_id'] = $yarn_type_id;
				$data_arr[$prod_id]['lot'] = $lot;
				$data_arr[$prod_id]['stock_in_hand'] += $stockInHand;
				$data_arr[$prod_id]['stock_in_hand_old'] += $stockInHand_old_stock;
				$data_arr[$prod_id]['stock_in_hand_new'] += $stockInHand_new_stock;
			}
			unset($prod_result);


			/* 
			echo "<pre>";
			print_r($data_arr) ;
			die; */
			?>
				<div style="width:1000px; margin: 0 auto;">
					<table width="900" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
						<thead>
							<tr class="form_caption" style="border:none;">
								<td colspan="7" align="center" style="border:none;font-size:16px; font-weight:bold">Count & Composition & Lot Wise Yarn Stock Summary Report</td>
							</tr>
							<tr style="border:none;">
								<td colspan="7" align="center" style="border:none; font-size:14px;">
									Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
								</td>
							</tr>
							<tr style="border:none;">
								<td colspan="7" align="center" style="border:none;font-size:12px; font-weight:bold">
									<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
								</td>
							</tr>
							<tr>
								<th width="50">Count</th>
								<th width="250">Composition</th>
								<th width="100">Yarn Type</th>
								<th width="100">Lot</th>
								<th width="100">0-<? echo $txt_days; ?> </th>
								<th width="100"><? echo $txt_days; ?>></th>
								<th width="100">Stock In Hand</th>
							</tr>
						</thead>
					</table>
					<div style="width:925px; overflow-y:scroll; max-height:350px" id="scroll_body">
						<table width="900" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
							<?
							$tot_stock_qty = 0;
							$i = 1;
							foreach ($data_arr as $prod_id => $row) {
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d", strtotime($to_date)));
								$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d", strtotime($to_date)));
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="50" style="padding-left:5px;">
										<p><? echo $count_lib_array[$row['yarn_count_id']]; ?>&nbsp;</p>
									</td>
									<td width="250" style="padding-left:5px;">
										<p><? echo $composition[$row['composition_id']]; ?>&nbsp;</p>
									</td>
									<td width="100" style="padding-left:5px;">
										<p><? echo $yarn_type[$row['yarn_type_id']]; ?>&nbsp;</p>
									</td>
									<td width="100" style="padding-left:5px;" title="<? echo $prod_id; ?>">
										<p><? echo $row['lot']; ?>&nbsp;</p>
									</td>
									<td width="100" align="right" style="padding-left:5px; word-wrap: break-word;word-break: break-all;">
										<? echo number_format($row['stock_in_hand_old'], 2); ?>
									</td>
									<td width="100" align="right" style="padding-left:5px; word-wrap: break-word;word-break: break-all;">
										<? echo number_format($row['stock_in_hand_new'], 2); ?>
									</td>
									<td width="100" align="right" style="padding-right:5px; word-wrap: break-word;word-break: break-all;"><? echo number_format($row['stock_in_hand'], 2); ?></td>
								</tr>
							<?
								$i++;
								$tot_stock_qty += $row['stock_in_hand'];

								$tot_stock_qty_old += $row['stock_in_hand_old'];
								$tot_stock_qty_new += $row['stock_in_hand_new'];
							}

							?>
						</table>
					</div>
					<table width="900" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
						<tr class="tbl_bottom">
							<td width="50" style="padding-right:5px;">Total</td>
							<td width="250" style="padding-right:5px;">&nbsp;</td>
							<td width="100" style="padding-right:5px;">&nbsp;</td>
							<td width="100" style="padding-right:5px;">&nbsp;</td>
							<td width="100" style="padding-right:5px;word-wrap: break-word;word-break: break-all;"><? echo number_format($tot_stock_qty_old, 2); ?></td>
							<td width="100" style="padding-right:5px;word-wrap: break-word;word-break: break-all;"><? echo number_format($tot_stock_qty_new, 2); ?></td>
							<td width="100" align="right" style="padding-right:5px; word-wrap: break-word;word-break: break-all;"><? echo number_format($tot_stock_qty, 2); ?></td>
						</tr>
					</table>
				</div>
			<?

		} else if ($type == 12) {
			?>
				<style type=" text/css">
					.wrap_break {
						word-wrap: break-word;
						word-break: break-all;
					}
				</style>
				<?
				if ($store_name != 0)  $store_cond = " and store_id in($store_name) ";
				if ($cbo_company_name != 0)  $company_cond = " and company_id=$cbo_company_name ";
				$date_array = array();
				$returnRes_date = "select prod_id, MIN(case when transaction_type in (1,4,5) and BALANCE_QNTY>0 then transaction_date else null end) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 $company_cond $store_cond group by prod_id";
				$result_returnRes_date = sql_select($returnRes_date);
				foreach ($result_returnRes_date as $row) {
					$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
					$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
				}

				$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group
			from product_details_master a
			where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
				$result = sql_select($sql);
				$i = 1;
				//ob_start();

				$yarnTestQalityCommentsArr = return_library_array("select a.prod_id as prod_id, b.comments_author as comments_author from inv_yarn_test_mst a, inv_yarn_test_comments b where a.id=b.mst_table_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.comments_author IS NOT NULL", 'prod_id', 'comments_author');

				$mrr_rate_sql = sql_select("select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction
				where status_active=1 and is_deleted=0 and item_category=1 and transaction_type in(1,4,5) group by prod_id");
				$mrr_rate_arr = array();
				foreach ($mrr_rate_sql as $row) {
					$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
					$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
					$mrr_rate_arr[$row[csf("prod_id")]] = $row[csf("cons_amount")] / $row[csf("cons_quantiy")];
				}

				//echo "<pre>";
				//print_r($mrr_rate_arr);
				?>
				<div>
					<table width="<? echo $table_width + 20; ?>" border="1" style="font:'Arial Narrow';" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
						<tr class="form_caption" style="border:none;">
							<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:16px; font-weight:bold">Daily Yarn Stock </td>
						</tr>
						<tr style="border:none;">
							<td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
							</td>
						</tr>
					</table>
					<table width="<? echo $table_width + 20; ?>" border="1" style="font:'Arial Narrow';" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
						<thead>
							<tr>
								<th rowspan="2" width="30">SL</th>
								<th rowspan="2" width="130">Company</th>
								<th colspan="7">Description</th>
								<th rowspan="2" width="100">Wgt. Bag/Cone</th>
								<th rowspan="2" width="110">OpeningStock</th>
								<th colspan="6">Receive</th>
								<th colspan="6">Delivery</th>
								<th rowspan="2" width="100">Stock InHand</th>
								<?
								echo $column;
								if ($store_wise == 1) {
									echo '<th rowspan="2" width="100">Store Name</th>';
								} else {
									echo '<th rowspan="2" width="100">Allocated to Order</th>';
									echo '<th rowspan="2" width="100">Un Allocated Qty.</th>';
								}
								?>
								<th rowspan="2" width="50">Age (Days)</th>
								<th rowspan="2" width="50">DOH</th>
								<th rowspan="2" width="140">Yarn Quality<br>Comments</th>
								<th rowspan="2">Remarks</th>
							</tr>
							<tr>
								<th width="60">Prod.ID</th>
								<th width="60">Count</th>
								<th width="150">Composition</th>
								<th width="100">Yarn Type</th>
								<th width="120">Color</th>
								<th width="100">Lot</th>
								<th width="100">Supplier</th>
								<th width="90">Purchase</th>
								<th width="90">Inside Return</th>
								<th width="90">Outside Return</th>
								<th width="90">Transfer In</th>
								<th width="90">Loan</th>
								<th width="100">Total Recv</th>
								<th width="90">Inside</th>
								<th width="90">Outside</th>
								<th width="90">Recv. Return</th>
								<th width="90">Trans. Out</th>
								<th width="90">Loan</th>
								<th width="100">Total Delivery</th>
							</tr>
						</thead>
					</table>
					<div style="width:<? echo $table_width + 38; ?>px; overflow-y:scroll; max-height:350px" id="scroll_body">
						<table style="width:<? echo $table_width + 20; ?>px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
							<?
							$tot_stock_value = 0;
							foreach ($result as $row) {
								if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "" || $yarnTestArr[$row[csf("id")]] != "") {
									$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
									$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));

									$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
									if ($row[csf("yarn_comp_type2nd")] != 0)
										$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
									$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
									$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

									$transfer_in_amt = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
									$transfer_out_amt = $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];


									$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
									$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

									$trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];
									$trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];

									$pay_mode = $receive_array[$row[csf("id")]]['pay_mode'];
									$receive_basis = $receive_array[$row[csf("id")]]['receive_basis'];

									$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);

									$openingBalanceAmt = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt);

									$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $receive_array[$row[csf("id")]]['rcv_adjustment_qty'] + $transfer_in_qty;

									$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $issue_array[$row[csf("id")]]['issue_adjustment_qty'] + $transfer_out_qty;

									$totalRcvAmt = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $receive_array[$row[csf("id")]]['rcv_adjustment_amt'] + $transfer_in_amt;

									$totalIssueAmt = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] + $issue_array[$row[csf("id")]]['issue_loan_amt'] + $issue_array[$row[csf("id")]]['issue_adjustment_amt'] + $transfer_out_qty;


									$stockInHand = $openingBalance + $totalRcv - $totalIssue;

									$stockInHandAmt = $openingBalanceAmt + $totalRcvAmt - $totalIssueAmt;

									//subtotal and group-----------------------
									$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];

									if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0)) {
										if ($i % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";

										//for supplier
										if ($row[csf('is_within_group')] == 1) {
											$supplier_name = $companyArr[$row[csf('supplier_id')]];
										} else {
											$supplier_name = $supplierArr[$row[csf('supplier_id')]];
										}
										//end for supplier

										if ($value_with == 1) {
											if (number_format($stockInHand, 2) > 0.00) {
												if (!in_array($check_string, $checkArr)) {
													$checkArr[$i] = $check_string;
													if ($i > 1) {
							?>
														<tr bgcolor="#CCCCCC" style="font-weight:bold">
															<td colspan="10" align="right">Sub Total</td>
															<td width="110" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
															<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
															<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
															<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
															<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
															<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
															<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
															<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
															<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
															<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
															<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
															<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
															<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
															<td width="100" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
															<?
															if ($show_val_column == 1) {
																echo '<td width="90" align="right">&nbsp;</td>';
																echo '<td width="110" align="right" class="wrap_break">' . number_format($sub_stock_value, 2) . '</td>';
																echo '<td width="100" align="right">&nbsp;</td>';
																echo '<td width="100" align="right" class="wrap_break">' . number_format($sub_stock_value_usd, 2) . '</td>';
															}

															if ($store_wise == 1) {
																echo '<td width="100">&nbsp;</td>';
															} else {
																if ($allocated_qty_variable_settings == 1) {
																	echo '<td width="100"  class="wrap_break" align="right">' . number_format($total_alocatted, 2) . '</td>';
																	echo '<td width="100" class="wrap_break" align="right">' . number_format($total_free_stock, 2) . '</td>';
																} else {
																	echo '<td width="100" align="right">&nbsp;</td>';
																	echo '<td width="100" align="right">&nbsp;</td>';
																}
															}
															?>
															<td width="50" align="right">&nbsp;</td>
															<td width="50" align="right">&nbsp;</td>
															<td align="right">&nbsp;</td>
															<td width="" align="right">&nbsp;</td>
														</tr>
												<?
														$total_opening_balance = 0;
														$total_purchase = 0;
														$total_inside_return = 0;
														$total_outside_return = 0;
														$total_rcv_loan = 0;
														$total_total_rcv = 0;
														$total_issue_inside = 0;
														$total_issue_outside = 0;
														$total_receive_return = 0;
														$total_issue_loan = 0;
														$total_total_delivery = 0;
														$total_stock_in_hand = 0;
														$total_alocatted = 0;
														$total_free_stock = 0;
														$sub_stock_value = 0;
														$sub_stock_value_usd = 0;
														$total_transfer_out_qty = 0;
														$total_transfer_in_qty = 0;
													}
												}
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
													<td width="30"><? echo $i; ?></td>
													<td width="130" class="wrap_break">
														<p><? echo $companyArr[$row[csf("company_id")]]; ?></p>
													</td>
													<td width="60"><? echo $row[csf("id")]; ?></td>
													<td width="60" class="wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
													<td width="150" class="wrap_break"><? echo $compositionDetails; ?></td>
													<td width="100" class="wrap_break"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
													<td width="120" class="wrap_break"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
													<td width="100">
														<p>
															<?
															if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
																<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?></a>
															<? } else if ($yarnTestArr[$row[csf("id")]] != "") { ?>
																<a href='##' onclick="show_test_report2('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?></a>
															<? } ?>
														</p>
													</td>
													<td width="100" class="wrap_break">
														<?
														//commented dated 16.09.2021
														/*if($receive_basis==2) // work order basis
													{
														if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
														{
															echo $companyArr[$row[csf("supplier_id")]];
														}else{
															echo $supplierArr[$row[csf("supplier_id")]];
														}
													}else{
														echo $supplierArr[$row[csf("supplier_id")]];
													}*/
														echo $supplier_name;
														?>
													</td>
													<td width="100" class="wrap_break"><? echo 'Bg:' . max($receive_array[$row[csf("id")]]['weight_per_bag']) . '; ' . 'Cn:' . max($receive_array[$row[csf("id")]]['weight_per_cone']); ?></td>
													<td width="110" class="wrap_break" align="right"><? echo number_format($openingBalance, 2); ?></td>
													<td width="90" class="wrap_break" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
													<td width="90" align="right" class="wrap_break">
														<?
														if ($store_wise == 1) {
															echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
														} else {
															echo number_format($transfer_in_qty, 2);
														}
														?>
													</td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
													<td width="100" align="right" class="wrap_break"><? echo number_format($totalRcv, 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
													<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
													<td width="90" align="right" class="wrap_break">
														<?
														if ($store_wise == 1) {
															echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
														} else {
															echo number_format($transfer_out_qty, 2);
														}
														?>
													</td>
													<td width="90" class="wrap_break" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
													<td width="100" align="right" class="wrap_break"><? echo number_format($totalIssue, 2); ?></td>
													<td width="100" align="right" class="wrap_break"><? echo number_format($stockInHand, 4); ?></td>
													<?

													$stock_value = 0;
													if ($show_val_column == 1) {
														$avg_rate = ($stockInHandAmt / $stockInHand);
														if ($avg_rate > 0) {
															$avg_rate = $avg_rate;
														} else {
															$avg_rate = "0.00";
														}

														$stock_value = $stockInHand * $avg_rate;
														$stock_value_usd = ($stockInHand * $avg_rate) / $exchange_rate;
														$avz_rates_usd = 0;

														if (number_format($stock_value_usd, 2) > 0 && number_format($stockInHand, 2) > 0) {
															$avz_rates_usd = $stock_value_usd / $stockInHand;
														} else {
															$avz_rates_usd = "0.00";
														}

														echo '<td width="90" align="right" class="wrap_break">' . number_format($avg_rate, 2) . '</td>';
														echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
														echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
														echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
													}

													if ($store_wise == 1) {
														$store_name = '';
														$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
														$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];
														$store_id_arr = array_unique(array_merge($receive_store_id, $issue_store_id));

														foreach ($store_id_arr as $key => $val) {
															if ($store_name == "")
																$store_name = $store_arr[$val];
															else
																$store_name .= ", " . $store_arr[$val];
														}
														echo '<td width="100" class="wrap_break">' . $store_name . '</td>';
													} else {
														if ($allocated_qty_variable_settings == 1) {
															echo "<td width='100' align='right' class='wrap_break'><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
															echo '<td width="100" align="right" class="wrap_break">' . number_format($row[csf("available_qnty")], 2) . '</td>';
														} else {
															echo '<td width="100" align="right">&nbsp;</td>';
															echo '<td width="100" align="right">&nbsp;</td>';
														}
													}
													?>
													<td width="50" align="right" class="wrap_break"><? echo $ageOfDays; //$ageOfDays;         
																									?></td>
													<td width="50" align="right" class="wrap_break"><?
																									if ($stockInHand > 0)
																										echo $daysOnHand;
																									else
																										echo "&nbsp;"; //$daysOnHand;
																									?></td>

													<?
													if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") {
													?>
														<td width="140" align="left"><span class="comment more" style="word-wrap: break-word;word-break: break-all;"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
													<?  } else { ?>
														<td width="140" align="left"><span class="wrap_break"><? echo "&nbsp;"; ?></span></td>
													<? 	} ?>


													<td width="" align="center" class="wrap_break">
														<?
														/*$buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
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
												echo $buyer_all;*/
														?>
														<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a>
													</td>
												</tr>
												<?
												$i++;

												$total_opening_balance += $openingBalance;
												$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
												$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
												$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
												$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
												$total_total_rcv += $totalRcv;
												$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
												$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
												$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
												$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
												$total_total_delivery += $totalIssue;
												$total_stock_in_hand += $stockInHand;
												$total_alocatted += $row[csf("allocated_qnty")];
												$total_free_stock += $row[csf("available_qnty")];
												$sub_stock_value += $stock_value;
												$sub_stock_value_usd += $stock_value_usd;
												$total_transfer_out_qty += $transfer_out_qty;
												$total_transfer_in_qty += $transfer_in_qty;

												//grand total===========================
												$grand_total_opening_balance += $openingBalance;
												$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
												$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
												$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
												$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
												$grand_total_total_rcv += $totalRcv;
												$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
												$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
												$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
												$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
												$grand_total_total_delivery += $totalIssue;
												$grand_total_stock_in_hand += $stockInHand;
												$grand_total_alocatted += $row[csf("allocated_qnty")];
												$grand_total_free_stock += $row[csf("available_qnty")];
												$tot_stock_value += $stock_value;
												$tot_stock_value_usd += $stock_value_usd;

												$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
												$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
											}
										} else {
											if (!in_array($check_string, $checkArr)) {
												$checkArr[$i] = $check_string;
												if ($i > 1) {
												?>
													<tr bgcolor="#CCCCCC" style="font-weight:bold">
														<td colspan="10" align="right">Sub Total</td>
														<td width="110" class="wrap_break" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
														<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
														<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
														<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
														<?
														if ($show_val_column == 1) {
															echo '<td width="90" align="right" >&nbsp;</td>';
															echo '<td width="110" align="right" class="wrap_break">' . number_format($sub_stock_value, 2) . '</td>';
															echo '<td width="100" align="right">&nbsp;</td>';
															echo '<td width="100" align="right" class="wrap_break">' . number_format($sub_stock_value_usd, 2) . '</td>';
														}

														if ($store_wise == 1) {
															echo '<td width="100">&nbsp;</td>';
														} else {
															if ($allocated_qty_variable_settings == 1) {
																echo '<td width="100"  class="wrap_break" align="right">' . number_format($total_alocatted, 2) . '</td>';
																echo '<td width="100" class="wrap_break" align="right">' . number_format($total_free_stock, 2) . '</td>';
															} else {
																echo '<td width="100" align="right">&nbsp;</td>';
																echo '<td width="100" align="right">&nbsp;</td>';
															}
														}
														?>
														<td width="50" align="right">&nbsp;</td>
														<td width="50" align="right">&nbsp;</td>
														<td width="" align="right">&nbsp;</td>
														<td width="" align="right">&nbsp;</td>
													</tr>
											<?
													$total_opening_balance = 0;
													$total_purchase = 0;
													$total_inside_return = 0;
													$total_outside_return = 0;
													$total_rcv_loan = 0;
													$total_total_rcv = 0;
													$total_issue_inside = 0;
													$total_issue_outside = 0;
													$total_receive_return = 0;
													$total_issue_loan = 0;
													$total_total_delivery = 0;
													$total_stock_in_hand = 0;
													$total_alocatted = 0;
													$total_free_stock = 0;
													$sub_stock_value = 0;
													$sub_stock_value_usd = 0;
													$total_transfer_out_qty = 0;
													$total_transfer_in_qty = 0;
												}
											}
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td width="30"><? echo $i; ?></td>
												<td width="130" class="wrap_break">
													<p><? echo $companyArr[$row[csf("company_id")]]; ?></p>
												</td>
												<td width="60" class="wrap_break"><? echo $row[csf("id")]; ?></td>
												<td width="60" class="wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
												<td width="150" class="wrap_break"><? echo $compositionDetails; ?></td>
												<td width="100" class="wrap_break"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
												<td width="120" class="wrap_break"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
												<td width="100" class="wrap_break" style="mso-number-format:'\@';">
													<p>
														<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
															<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
														<? } else {
															echo $row[csf("lot")];
														}
														?>
														&nbsp;
													</p>
												</td>

												<td width="100" class="wrap_break">
													<?
													//commented dated 16.09.2021
													/*if($receive_basis==2) // work order basis
												{
													if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
													{
														echo $companyArr[$row[csf("supplier_id")]];
													}else{
														echo $supplierArr[$row[csf("supplier_id")]];
													}
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}*/
													echo $supplier_name;
													?>
												</td>
												<td width="100" class="wrap_break"><? echo 'Bg:' . max($receive_array[$row[csf("id")]]['weight_per_bag']) . '; ' . 'Cn:' . max($receive_array[$row[csf("id")]]['weight_per_cone']); ?></td>
												<td width="110" class="wrap_break" align="right"><? echo number_format($openingBalance, 2); ?></td>
												<td width="90" class="wrap_break" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_in_qty, 2);
													}
													?>
												</td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($totalRcv, 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_out_qty, 2);
													}
													?>
												</td>
												<td width="90" class="wrap_break" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($totalIssue, 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($stockInHand, 4); ?></td>
												<?
												$stock_value = 0;
												if ($show_val_column == 1) {

													$avg_rate = ($stockInHandAmt / $stockInHand);
													if ($avg_rate > 0) {
														$avg_rate = $avg_rate;
													} else {
														$avg_rate = "0.00";
													}

													$stock_value = $stockInHand * $avg_rate;
													$stock_value_usd = ($stockInHand * $avg_rate) / $exchange_rate;
													$avz_rates_usd = 0;

													if (number_format($stock_value_usd, 2) > 0 && number_format($stockInHand, 2) > 0) {
														$avz_rates_usd = $stock_value_usd / $stockInHand;
													} else {
														$avz_rates_usd = "0.00";
													}

													echo '<td width="90" align="right" class="wrap_break">' . number_format($avg_rate, 2) . '</td>';
													echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
													echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
													echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
												}

												if ($store_wise == 1) {
													$store_name = '';
													$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
													$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];
													$store_id_arr = array_unique(array_merge($receive_store_id, $issue_store_id));
													foreach ($store_id_arr as $key => $val) {
														if ($store_name == "")
															$store_name = $store_arr[$val];
														else
															$store_name .= ", " . $store_arr[$val];
													}
													echo '<td width="100" class="wrap_break">' . $store_name . '</td>';
												} else {
													if ($allocated_qty_variable_settings == 1) {
														echo "<td width='100' align='right' class='wrap_break'><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
														echo '<td width="100" align="right" class="wrap_break">' . number_format($row[csf("available_qnty")], 2) . '</td>';
													} else {
														echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
														echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
													}
												}
												?>
												<td width="50" align="right" class="wrap_break"><? echo $ageOfDays; //$ageOfDays;         
																								?></td>
												<td width="50" align="right" class="wrap_break"><?
																								if ($stockInHand > 0)
																									echo $daysOnHand;
																								else
																									echo "&nbsp;"; //$daysOnHand;
																								?></td>

												<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
													<td width="140" align="left"><span class="comment more" style="word-wrap: break-word;word-break: break-all;"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
												<?  } else { ?>
													<td width="140" align="left"><span><? echo "&nbsp;"; ?></span></td>
												<? 	} ?>

												<td width="" align="center" class="wrap_break">
													<?
													/*$buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
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
											echo $buyer_all;*/
													?>
													<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a>
												</td>
											</tr>
							<?
											$i++;

											$total_opening_balance += $openingBalance;
											$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
											$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
											$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
											$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
											$total_total_rcv += $totalRcv;
											$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
											$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
											$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
											$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
											$total_total_delivery += $totalIssue;
											$total_stock_in_hand += $stockInHand;
											$total_alocatted += $row[csf("allocated_qnty")];
											$total_free_stock += $row[csf("available_qnty")];
											$sub_stock_value += $stock_value;
											$sub_stock_value_usd += $stock_value_usd;
											$total_transfer_out_qty += $transfer_out_qty;
											$total_transfer_in_qty += $transfer_in_qty;

											//grand total===========================
											$grand_total_opening_balance += $openingBalance;
											$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
											$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
											$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
											$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
											$grand_total_total_rcv += $totalRcv;
											$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
											$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
											$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
											$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
											$grand_total_total_delivery += $totalIssue;
											$grand_total_stock_in_hand += $stockInHand;
											$grand_total_alocatted += $row[csf("allocated_qnty")];
											$grand_total_free_stock += $row[csf("available_qnty")];
											$tot_stock_value += $stock_value;
											$tot_stock_value_usd += $stock_value_usd;

											$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
											$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
										}
									}
								}
							}
							?>
							<tr bgcolor="#CCCCCC" style="font-weight:bold">
								<td colspan="10" align="right">Sub Total</td>
								<td width="110" class="wrap_break" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
								<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
								<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
								<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
								<?
								if ($show_val_column == 1) {
									echo '<td width="90" align="right">&nbsp;</td>';
									echo '<td width="110" class="wrap_break" align="right">' . number_format($sub_stock_value, 2) . '</td>';
									echo '<td width="100" align="right">&nbsp;</td>';
									echo '<td width="100" class="wrap_break" align="right">' . number_format($sub_stock_value_usd, 2) . '</td>';
								}

								if ($store_wise == 1) {
									echo '<td width="100">&nbsp;</td>';
								} else {
									if ($allocated_qty_variable_settings == 1) {
										echo '<td width="100" align="right">' . number_format($total_alocatted, 2) . '</td>';
										echo '<td width="100" align="right">' . number_format($total_free_stock, 2) . '</td>';
									} else {
										echo '<td width="100" align="right">&nbsp;</td>';
										echo '<td width="100" align="right">&nbsp;</td>';
									}
								}
								?>
								<td width="50" align="right">&nbsp;</td>
								<td width="50" align="right">&nbsp;</td>
								<td width="" align="right">&nbsp;</td>
								<td width="" align="right">&nbsp;</td>
							</tr>
						</table>
					</div>
					<table style="width:<? echo $table_width + 20; ?>px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
						<tr class="tbl_bottom">
							<td width="30"></td>
							<td width="130"></td>
							<td width="60"></td>
							<td width="60"></td>
							<td width="150"></td>
							<td width="100"></td>
							<td width="120"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100" align="right">Grand Total</td>
							<td width="110" class="wrap_break" align="right" id="value_total_opening_balance"><? echo number_format($grand_total_opening_balance, 2); ?></td>
							<td width="90" align="right" id="value_total_purchase"><? echo number_format($grand_total_purchase, 2); ?></td>
							<td width="90" align="right" id="value_total_inside_return"><? echo number_format($grand_total_inside_return, 2); ?></td>
							<td width="90" align="right" id="value_total_outside_return"><? echo number_format($grand_total_outside_return, 2); ?></td>
							<td width="90" align="right" id="value_total_transfer_in"><? echo number_format($grand_total_transfer_in_qty, 2); ?></td>
							<td width="90" class="wrap_break" align="right" id="value_total_rcv_loan"><? echo number_format($grand_total_rcv_loan, 2); ?></td>
							<td width="100" class="wrap_break" align="right" id="value_total_total_rcv"><? echo number_format($grand_total_total_rcv, 2); ?></td>
							<td width="90" class="wrap_break" align="right" id="value_total_issue_inside"><? echo number_format($grand_total_issue_inside, 2); ?></td>
							<td width="90" class="wrap_break" align="right" id="value_total_issue_outside"><? echo number_format($grand_total_issue_outside, 2); ?></td>
							<td width="90" class="wrap_break" align="right" id="value_total_receive_return"><? echo number_format($grand_total_receive_return, 2); ?></td>
							<td width="90" align="right" class="wrap_break" id="value_total_transfer_out"><? echo number_format($grand_total_transfer_out_qty, 2); ?></td>
							<td width="90" align="right" class="wrap_break" id="value_total_issue_loan"><? echo number_format($grand_total_issue_loan, 2); ?></td>
							<td width="100" align="right" class="wrap_break" id="value_total_total_delivery"><? echo number_format($grand_total_total_delivery, 2); ?></td>
							<td width="100" align="right" class="wrap_break" id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 4); ?></td>
							<?
							if ($show_val_column == 1) {
								echo '<td width="90" align="right">&nbsp;</td>';
								echo '<td width="110" class="wrap_break" align="right">' . number_format($tot_stock_value, 2) . '</td>';
								echo '<td width="100" align="right">&nbsp;</td>';
								echo '<td width="100" class="wrap_break" align="right">' . number_format($tot_stock_value_usd, 2) . '</td>';
							}

							if ($store_wise == 1) {
								echo '<td width="100">&nbsp;</td>';
							} else {
								if ($allocated_qty_variable_settings == 1) {
									echo '<td width="100" align="right" id="value_total_alocatted">' . number_format($grand_total_alocatted, 2) . '</td>';
									echo '<td width="100" align="right" id="value_total_free_stock">' . number_format($grand_total_free_stock, 2) . '</td>';
								} else {
									echo '<td width="100" align="right" id="value_total_alocatted">&nbsp;</td>';
									echo '<td width="100" align="right" id="value_total_free_stock">&nbsp;</td>';
								}
							}
							?>
							<td width="50" align="right">&nbsp;</td>
							<td width="50" align="right">&nbsp;</td>
							<td width="140" align="right">&nbsp;</td>
							<td width="" align="right">&nbsp;</td>
						</tr>
					</table>
				</div>
			<?
		} else if ($type == 13) {
			if ($show_val_column == 1) {
				$value_width = 400;
				$span = 3;
				$column = '<th rowspan="2" width="90">Avg. Rate (Tk)</th><th rowspan="2" width="110">Stock Value</th><th rowspan="2" width="100">Avg. Rate (USD)</th><th rowspan="2" width="100">Stock Value (USD)</th>';
			} else {
				$value_width = 0;
				$span = 0;
				$column = '';
			}
			if ($store_wise == 1) {
				if ($store_name == 0)
					$store_cond .= "";
				else
					$store_cond .= " and a.store_id = $store_name";
				//$table_width = '3900' + $value_width;
				$table_width = '3370' + $value_width;
				$colspan = '32' + $span;
				//$colspan = '38' + $span;
				$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
			} else {
				//$table_width = '3400' + $value_width;
				$table_width = '2800' + $value_width;
				$colspan = '33' + $span;
			}
			?>
				<style type="text/css">
					.wrap_break {
						word-wrap: break-word;
						word-break: break-all;
					}
				</style>
				<?
				if ($store_name != 0)  $store_cond = " and store_id in($store_name) ";
				if ($cbo_company_name != 0)  $company_cond = " and company_id=$cbo_company_name ";
				$date_array = array();
				$returnRes_date = "select prod_id, MIN(case when transaction_type in (1,4,5) and BALANCE_QNTY>0 then transaction_date else null end) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 $company_cond $store_cond group by prod_id";
				$result_returnRes_date = sql_select($returnRes_date);
				foreach ($result_returnRes_date as $row) {
					$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
					$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
				}

				$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group
			from product_details_master a
			where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
				$result = sql_select($sql);
				$i = 1;
				//ob_start();
				?>
				<div>
					<table width="<? echo $table_width + 20; ?>" border="1" style="font:'Arial Narrow';" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
						<tr class="form_caption" style="border:none;">
							<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:16px; font-weight:bold">Daily Yarn Stock </td>
						</tr>
						<tr style="border:none;">
							<td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
							</td>
						</tr>
					</table>
					<table width="<? echo $table_width + 20; ?>" border="1" style="font:'Arial Narrow';" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
						<thead>
							<tr>
								<th rowspan="2" width="30">SL</th>
								<th rowspan="2" width="130">Company <br>rack wise</th>
								<th colspan="7">Description</th>
								<th rowspan="2" width="100">Wgt. Bag/Cone</th>
								<th rowspan="2" width="110">OpeningStock</th>
								<th colspan="6">Receive</th>
								<th colspan="6">Delivery</th>
								<th rowspan="2" width="100">Stock InHand</th>
								<?
								echo $column;
								if ($store_wise == 1) {
									echo '<th rowspan="2" width="100">Store Name</th>';
									echo '<th rowspan="2" width="100">Floor</th>';
									echo '<th rowspan="2" width="100">Room</th>';
									echo '<th rowspan="2" width="100">Rack</th>';
									echo '<th rowspan="2" width="100">Self</th>';
									echo '<th rowspan="2" width="100">Bin-Box</th>';
								} else {
									echo '<th rowspan="2" width="100">Allocated to Order</th>';
									echo '<th rowspan="2" width="100">Un Allocated Qty.</th>';
								}
								?>
								<th rowspan="2" width="50">Age (Days)</th>
								<th rowspan="2" width="50">DOH</th>
								<th rowspan="2" width="140">Yarn Quality<br>Comments</th>
								<th rowspan="2">Remarks</th>
							</tr>
							<tr>
								<th width="60">Prod.ID</th>
								<th width="60">Count</th>
								<th width="150">Composition</th>
								<th width="100">Yarn Type</th>
								<th width="120">Color</th>
								<th width="100">Lot</th>
								<th width="100">Supplier</th>
								<th width="90">Purchase</th>
								<th width="90">Inside Return</th>
								<th width="90">Outside Return</th>
								<th width="90">Transfer In</th>
								<th width="90">Loan</th>
								<th width="100">Total Recv</th>
								<th width="90">Inside</th>
								<th width="90">Outside</th>
								<th width="90">Recv. Return</th>
								<th width="90">Trans. Out</th>
								<th width="90">Loan</th>
								<th width="100">Total Delivery</th>
							</tr>
						</thead>
					</table>
					<div style="width:<? echo $table_width + 38; ?>px; overflow-y:scroll; max-height:350px" id="scroll_body">
						<table style="width:<? echo $table_width + 20; ?>px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
							<?
							$tot_stock_value = 0;
							foreach ($result as $row) {
								$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
								$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));

								$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
								if ($row[csf("yarn_comp_type2nd")] != 0)
									$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
								$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
								$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

								$transfer_in_amt = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
								$transfer_out_amt = $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];


								$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
								$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

								$trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];
								$trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];

								$pay_mode = $receive_array[$row[csf("id")]]['pay_mode'];
								$receive_basis = $receive_array[$row[csf("id")]]['receive_basis'];

								$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);

								$openingBalanceAmt = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt);

								$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $receive_array[$row[csf("id")]]['rcv_adjustment_qty'] + $transfer_in_qty;

								$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $issue_array[$row[csf("id")]]['issue_adjustment_qty'] + $transfer_out_qty;

								$totalRcvAmt = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $receive_array[$row[csf("id")]]['rcv_adjustment_amt'] + $transfer_in_amt;

								$totalIssueAmt = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] + $issue_array[$row[csf("id")]]['issue_loan_amt'] + $issue_array[$row[csf("id")]]['issue_adjustment_amt'] + $transfer_out_qty;


								$stockInHand = $openingBalance + $totalRcv - $totalIssue;

								$stockInHandAmt = $openingBalanceAmt + $totalRcvAmt - $totalIssueAmt;

								//subtotal and group-----------------------
								$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];

								if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0)) {
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";

									//for supplier
									if ($row[csf('is_within_group')] == 1) {
										$supplier_name = $companyArr[$row[csf('supplier_id')]];
									} else {
										$supplier_name = $supplierArr[$row[csf('supplier_id')]];
									}
									//end for supplier

									if ($value_with == 1) {
										if (number_format($stockInHand, 2) > 0.00) {
											if (!in_array($check_string, $checkArr)) {
												$checkArr[$i] = $check_string;
												if ($i > 1) {
							?>
													<tr bgcolor="#CCCCCC" style="font-weight:bold">
														<td colspan="10" align="right">Sub Total</td>
														<td width="110" align="right" class="wrap_break"><? echo number_format($total_opening_balance, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_purchase, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_inside_return, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_outside_return, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_in_qty, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_rcv_loan, 2); ?></td>
														<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_rcv, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_inside, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_outside, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_receive_return, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_out_qty, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_loan, 2); ?></td>
														<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_delivery, 2); ?></td>
														<td width="100" align="right" class="wrap_break"><? echo number_format($total_stock_in_hand, 4); ?></td>
														<?
														if ($show_val_column == 1) {
															echo '<td width="90" align="right">&nbsp;</td>';
															echo '<td width="110" align="right" class="wrap_break">' . number_format($sub_stock_value, 2) . '</td>';
															echo '<td width="100" align="right">&nbsp;</td>';
															echo '<td width="100" align="right" class="wrap_break">' . number_format($sub_stock_value_usd, 2) . '</td>';
														}

														if ($store_wise == 1) {
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
														} else {
															if ($allocated_qty_variable_settings == 1) {
																echo '<td width="100"  class="wrap_break" align="right">' . number_format($total_alocatted, 2) . '</td>';
																echo '<td width="100" class="wrap_break" align="right">' . number_format($total_free_stock, 2) . '</td>';
															} else {
																echo '<td width="100" align="right">&nbsp;</td>';
																echo '<td width="100" align="right">&nbsp;</td>';
															}
														}
														?>
														<td width="50" align="right">&nbsp;</td>
														<td width="50" align="right">&nbsp;</td>
														<td align="right">&nbsp;</td>
														<td width="" align="right">&nbsp;</td>
													</tr>
											<?
													$total_opening_balance = 0;
													$total_purchase = 0;
													$total_inside_return = 0;
													$total_outside_return = 0;
													$total_rcv_loan = 0;
													$total_total_rcv = 0;
													$total_issue_inside = 0;
													$total_issue_outside = 0;
													$total_receive_return = 0;
													$total_issue_loan = 0;
													$total_total_delivery = 0;
													$total_stock_in_hand = 0;
													$total_alocatted = 0;
													$total_free_stock = 0;
													$sub_stock_value = 0;
													$sub_stock_value_usd = 0;
													$total_transfer_out_qty = 0;
													$total_transfer_in_qty = 0;
												}
											}
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td width="30"><? echo $i; ?></td>
												<td width="130" class="wrap_break">
													<p><? echo $companyArr[$row[csf("company_id")]]; ?></p>
												</td>
												<td width="60"><? echo $row[csf("id")]; ?></td>
												<td width="60" class="wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
												<td width="150" class="wrap_break"><? echo $compositionDetails; ?></td>
												<td width="100" class="wrap_break"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
												<td width="120" class="wrap_break"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
												<td width="100">
													<p>
														<?
														if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
															<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?></a>
														<? } else if ($yarnTestArr[$row[csf("id")]] != "") { ?>
															<a href='##' onclick="show_test_report2('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?></a>
														<? } else {
															echo $row[csf("lot")];
														} ?>
													</p>
												</td>
												<td width="100" class="wrap_break">
													<?
													//commented dated 16.09.2021
													/*if($receive_basis==2) // work order basis
												{
													if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
													{
														echo $companyArr[$row[csf("supplier_id")]];
													}else{
														echo $supplierArr[$row[csf("supplier_id")]];
													}
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}*/
													echo $supplier_name;
													?>
												</td>
												<td width="100" class="wrap_break"><? echo 'Bg:' . max($receive_array[$row[csf("id")]]['weight_per_bag']) . '; ' . 'Cn:' . max($receive_array[$row[csf("id")]]['weight_per_cone']); ?></td>
												<td width="110" class="wrap_break" align="right"><? echo number_format($openingBalance, 2); ?></td>
												<td width="90" class="wrap_break" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_in_qty, 2);
													}
													?>
												</td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($totalRcv, 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_out_qty, 2);
													}
													?>
												</td>
												<td width="90" class="wrap_break" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($totalIssue, 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($stockInHand, 4); ?></td>
												<?

												$stock_value = 0;
												if ($show_val_column == 1) {
													$avg_rate = ($stockInHandAmt / $stockInHand);
													if ($avg_rate > 0) {
														$avg_rate = $avg_rate;
													} else {
														$avg_rate = "0.00";
													}

													$stock_value = $stockInHand * $avg_rate;
													$stock_value_usd = ($stockInHand * $avg_rate) / $exchange_rate;
													$avz_rates_usd = 0;

													if (number_format($stock_value_usd, 2) > 0 && number_format($stockInHand, 2) > 0) {
														$avz_rates_usd = $stock_value_usd / $stockInHand;
													} else {
														$avz_rates_usd = "0.00";
													}

													echo '<td width="90" align="right" class="wrap_break">' . number_format($avg_rate, 2) . '</td>';
													echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
													echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
													echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
												}

												if ($store_wise == 1) {
													$store_name = '';
													//$receive_store_id = explode(",", $receive_array[$row[csf("id")]]['store_id']);
													//$issue_store_id = explode(",", $issue_array[$row[csf("id")]]['store_id']);
													$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
													$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];

													if (!empty($issue_store_id)) {
														$store_id_arr = array_unique(array_merge($receive_store_id, $issue_store_id));
													} else {
														$store_id_arr = $receive_store_id;
													}

													foreach ($store_id_arr as $key => $val) {
														if ($store_name == "")
															$store_name = $store_arr[$val];
														else
															$store_name .= ", " . $store_arr[$val];
													}
													echo '<td width="100" class="wrap_break">' . $store_name . '</td>';

													$floor_name = '';
													$receive_floor_id = $receive_array[$row[csf("id")]]['floor_id'];
													$issue_floor_id = $issue_array[$row[csf("id")]]['floor_id'];
													if (!empty($issue_floor_id)) {
														$floor_id = array_unique(array_merge($receive_floor_id, $issue_floor_id));
													} else {
														$floor_id = $receive_floor_id;
													}

													foreach ($floor_id as $val) {
														if ($floor_name == "")
															$floor_name = $floor_room_rack_arr[$val];
														else
															$floor_name .= ", " . $floor_room_rack_arr[$val];
													}

													echo '<td width="100" class="wrap_break">' . $floor_name . '</td>';

													$room_name = '';
													$receive_room_id = $receive_array[$row[csf("id")]]['room'];
													$issue_room_id = $issue_array[$row[csf("id")]]['room'];
													if (!empty($issue_room_id)) {
														$room_id = array_unique(array_merge($receive_room_id, $issue_room_id));
													} else {
														$room_id = $receive_room_id;
													}

													foreach ($room_id as $val) {
														if ($room_name == "")
															$room_name = $floor_room_rack_arr[$val];
														else
															$room_name .= ", " . $floor_room_rack_arr[$val];
													}

													echo '<td width="100" class="wrap_break">' . $room_name . '</td>';

													$rack_name = '';
													$receive_rack_id = $receive_array[$row[csf("id")]]['rack'];
													$issue_rack_id = $issue_array[$row[csf("id")]]['rack'];

													if (!empty($issue_rack_id)) {
														$rack_id = array_unique(array_merge($receive_rack_id, $issue_rack_id));
													} else {
														$rack_id = $receive_rack_id;
													}

													foreach ($rack_id as $val) {
														if ($rack_name == "")
															$rack_name = $floor_room_rack_arr[$val];
														else
															$rack_name .= ", " . $floor_room_rack_arr[$val];
													}
													echo '<td width="100" class="wrap_break">' . $rack_name . '</td>';

													$self_name = '';
													$receive_self_id = $receive_array[$row[csf("id")]]['self'];
													$issue_self_id = $issue_array[$row[csf("id")]]['self'];

													if (!empty($issue_self_id)) {
														$self_id = array_unique(array_merge($receive_self_id, $issue_self_id));
													} else {
														$self_id = $receive_self_id;
													}


													foreach ($self_id as $val) {
														if ($self_name == "")
															$self_name = $floor_room_rack_arr[$val];
														else
															$self_name .= ", " . $floor_room_rack_arr[$val];
													}

													echo '<td width="100" class="wrap_break">' . $self_name . '</td>';

													$bin_box_name = '';
													$receive_bin_box_id = $receive_array[$row[csf("id")]]['bin_box'];
													$issue_bin_box_id = $issue_array[$row[csf("id")]]['bin_box'];

													if (!empty($issue_bin_box_id)) {
														$bin_box_id = array_unique(array_merge($receive_bin_box_id, $issue_bin_box_id));
													} else {
														$bin_box_id = $receive_bin_box_id;
													}

													foreach ($bin_box_id as $val) {
														if ($bin_box_name == "")
															$bin_box_name = $floor_room_rack_arr[$val];
														else
															$bin_box_name .= ", " . $floor_room_rack_arr[$val];
													}

													echo '<td width="100" class="wrap_break">' . $bin_box_name . '</td>';
												} else {
													if ($allocated_qty_variable_settings == 1) {
														echo "<td width='100' align='right' class='wrap_break'><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
														echo '<td width="100" align="right" class="wrap_break">' . number_format($row[csf("available_qnty")], 2) . '</td>';
													} else {
														echo '<td width="100" align="right">&nbsp;</td>';
														echo '<td width="100" align="right">&nbsp;</td>';
													}
												}
												?>
												<td width="50" align="right" class="wrap_break"><? echo $ageOfDays; //$ageOfDays;         
																								?></td>
												<td width="50" align="right" class="wrap_break">
													<? if ($daysOnHand >= 180) { ?>
														<p style="background-color: red;" title="180 days or above">
															<?
															if ($stockInHand > 0)
																echo $daysOnHand;
															else
																echo "&nbsp;"; //$daysOnHand;
															?></p>
													<? } else { ?>
														<p>
															<?
															if ($stockInHand > 0)
																echo $daysOnHand;
															else
																echo "&nbsp;"; //$daysOnHand;
															?></p>
													<? } ?>
												</td>

												<?
												if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") {
												?>
													<td width="140" align="left"><span class="comment more" style="word-wrap: break-word;word-break: break-all;"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
												<?  } else { ?>
													<td width="140" align="left"><span class="wrap_break"><? echo "&nbsp;"; ?></span></td>
												<? 	} ?>


												<td width="" align="center" class="wrap_break">
													<?
													/*$buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
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
											echo $buyer_all;*/
													?>
													<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a>
												</td>
											</tr>
											<?
											$i++;

											$total_opening_balance += $openingBalance;
											$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
											$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
											$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
											$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
											$total_total_rcv += $totalRcv;
											$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
											$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
											$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
											$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
											$total_total_delivery += $totalIssue;
											$total_stock_in_hand += $stockInHand;
											$total_alocatted += $row[csf("allocated_qnty")];
											$total_free_stock += $row[csf("available_qnty")];
											$sub_stock_value += $stock_value;
											$sub_stock_value_usd += $stock_value_usd;
											$total_transfer_out_qty += $transfer_out_qty;
											$total_transfer_in_qty += $transfer_in_qty;

											//grand total===========================
											$grand_total_opening_balance += $openingBalance;
											$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
											$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
											$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
											$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
											$grand_total_total_rcv += $totalRcv;
											$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
											$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
											$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
											$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
											$grand_total_total_delivery += $totalIssue;
											$grand_total_stock_in_hand += $stockInHand;
											$grand_total_alocatted += $row[csf("allocated_qnty")];
											$grand_total_free_stock += $row[csf("available_qnty")];
											$tot_stock_value += $stock_value;
											$tot_stock_value_usd += $stock_value_usd;

											$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
											$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
										}
									} else {
										if (!in_array($check_string, $checkArr)) {
											$checkArr[$i] = $check_string;
											if ($i > 1) {
											?>
												<tr bgcolor="#CCCCCC" style="font-weight:bold">
													<td colspan="10" align="right">Sub Total</td>
													<td width="110" class="wrap_break" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
													<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
													<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
													<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
													<?
													if ($show_val_column == 1) {
														echo '<td width="90" align="right" >&nbsp;</td>';
														echo '<td width="110" align="right" class="wrap_break">' . number_format($sub_stock_value, 2) . '</td>';
														echo '<td width="100" align="right">&nbsp;</td>';
														echo '<td width="100" align="right" class="wrap_break">' . number_format($sub_stock_value_usd, 2) . '</td>';
													}

													if ($store_wise == 1) {
														echo '<td width="100">&nbsp;</td>';
														echo '<td width="100">&nbsp;</td>';
														echo '<td width="100">&nbsp;</td>';
														echo '<td width="100">&nbsp;</td>';
														echo '<td width="100">&nbsp;</td>';
														echo '<td width="100">&nbsp;</td>';
													} else {
														if ($allocated_qty_variable_settings == 1) {
															echo '<td width="100"  class="wrap_break" align="right">' . number_format($total_alocatted, 2) . '</td>';
															echo '<td width="100" class="wrap_break" align="right">' . number_format($total_free_stock, 2) . '</td>';
														} else {
															echo '<td width="100" align="right">&nbsp;</td>';
															echo '<td width="100" align="right">&nbsp;</td>';
														}
													}
													?>
													<td width="50" align="right">&nbsp;</td>
													<td width="50" align="right">&nbsp;</td>
													<td width="" align="right">&nbsp;</td>
													<td width="" align="right">&nbsp;</td>
												</tr>
										<?
												$total_opening_balance = 0;
												$total_purchase = 0;
												$total_inside_return = 0;
												$total_outside_return = 0;
												$total_rcv_loan = 0;
												$total_total_rcv = 0;
												$total_issue_inside = 0;
												$total_issue_outside = 0;
												$total_receive_return = 0;
												$total_issue_loan = 0;
												$total_total_delivery = 0;
												$total_stock_in_hand = 0;
												$total_alocatted = 0;
												$total_free_stock = 0;
												$sub_stock_value = 0;
												$sub_stock_value_usd = 0;
												$total_transfer_out_qty = 0;
												$total_transfer_in_qty = 0;
											}
										}
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30"><? echo $i; ?></td>
											<td width="130" class="wrap_break">
												<p><? echo $companyArr[$row[csf("company_id")]]; ?></p>
											</td>
											<td width="60" class="wrap_break"><? echo $row[csf("id")]; ?></td>
											<td width="60" class="wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
											<td width="150" class="wrap_break"><? echo $compositionDetails; ?></td>
											<td width="100" class="wrap_break"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
											<td width="120" class="wrap_break"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
											<td width="100" class="wrap_break" style="mso-number-format:'\@';">
												<p>
													<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
														<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
													<? } else {
														echo $row[csf("lot")];
													}
													?>
													&nbsp;
												</p>
											</td>

											<td width="100" class="wrap_break">
												<?
												//commented by Zaman dated 19.09.2021
												/*if($receive_basis==2) // work order basis
											{
												if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
												{
													echo $companyArr[$row[csf("supplier_id")]];
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}
											}else{
												echo $supplierArr[$row[csf("supplier_id")]];
											}*/
												echo $supplier_name;
												?>
											</td>
											<td width="100" class="wrap_break"><? echo 'Bg:' . max($receive_array[$row[csf("id")]]['weight_per_bag']) . '; ' . 'Cn:' . max($receive_array[$row[csf("id")]]['weight_per_cone']); ?></td>
											<td width="110" class="wrap_break" align="right"><? echo number_format($openingBalance, 2); ?></td>
											<td width="90" class="wrap_break" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break">
												<?
												if ($store_wise == 1) {
													echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
												} else {
													echo number_format($transfer_in_qty, 2);
												}
												?>
											</td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
											<td width="100" align="right" class="wrap_break"><? echo number_format($totalRcv, 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break">
												<?
												if ($store_wise == 1) {
													echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
												} else {
													echo number_format($transfer_out_qty, 2);
												}
												?>
											</td>
											<td width="90" class="wrap_break" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
											<td width="100" align="right" class="wrap_break"><? echo number_format($totalIssue, 2); ?></td>
											<td width="100" align="right" class="wrap_break"><? echo number_format($stockInHand, 4); ?></td>
											<?
											$stock_value = 0;
											if ($show_val_column == 1) {

												$avg_rate = ($stockInHandAmt / $stockInHand);
												if ($avg_rate > 0) {
													$avg_rate = $avg_rate;
												} else {
													$avg_rate = "0.00";
												}

												$stock_value = $stockInHand * $avg_rate;
												$stock_value_usd = ($stockInHand * $avg_rate) / $exchange_rate;
												$avz_rates_usd = 0;

												if (number_format($stock_value_usd, 2) > 0 && number_format($stockInHand, 2) > 0) {
													$avz_rates_usd = $stock_value_usd / $stockInHand;
												} else {
													$avz_rates_usd = "0.00";
												}

												echo '<td width="90" align="right" class="wrap_break">' . number_format($avg_rate, 2) . '</td>';
												echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
												echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
												echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
											}

											if ($store_wise == 1) {
												$store_name = '';
												//$receive_store_id = explode(",", $receive_array[$row[csf("id")]]['store_id']);
												//$issue_store_id = explode(",", $issue_array[$row[csf("id")]]['store_id']);
												$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
												$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];

												$store_id_arr = array_unique(array_merge($receive_store_id, $issue_store_id));
												foreach ($store_id_arr as $key => $val) {
													if ($store_name == "")
														$store_name = $store_arr[$val];
													else
														$store_name .= ", " . $store_arr[$val];
												}
												echo '<td width="100" class="wrap_break">' . $store_name . '</td>';

												$floor_name = '';
												$receive_floor_id = $receive_array[$row[csf("id")]]['floor_id'];
												$issue_floor_id = $issue_array[$row[csf("id")]]['floor_id'];

												$floor_id = array_unique(array_merge($receive_floor_id, $issue_floor_id));
												foreach ($floor_id as $val) {
													if ($floor_name == "")
														$floor_name = $floor_room_rack_arr[$val];
													else
														$floor_name .= ", " . $floor_room_rack_arr[$val];
												}

												echo '<td width="100" class="wrap_break">' . $floor_name . '</td>';

												$room_name = '';
												$receive_room_id = $receive_array[$row[csf("id")]]['room'];
												$issue_room_id = $issue_array[$row[csf("id")]]['room'];

												$room_id = array_unique(array_merge($receive_room_id, $issue_room_id));
												foreach ($room_id as $val) {
													if ($room_name == "")
														$room_name = $floor_room_rack_arr[$val];
													else
														$room_name .= ", " . $floor_room_rack_arr[$val];
												}

												echo '<td width="100" class="wrap_break">' . $room_name . '</td>';

												$rack_name = '';
												$receive_rack_id = $receive_array[$row[csf("id")]]['rack'];
												$issue_rack_id = $issue_array[$row[csf("id")]]['rack'];

												$rack_id = array_unique(array_merge($receive_rack_id, $issue_rack_id));
												foreach ($rack_id as $val) {
													if ($rack_name == "")
														$rack_name = $floor_room_rack_arr[$val];
													else
														$rack_name .= ", " . $floor_room_rack_arr[$val];
												}
												echo '<td width="100" class="wrap_break">' . $rack_name . '</td>';

												$self_name = '';
												$receive_self_id = $receive_array[$row[csf("id")]]['self'];
												$issue_self_id = $issue_array[$row[csf("id")]]['self'];

												$self_id = array_unique(array_merge($receive_self_id, $issue_self_id));

												foreach ($self_id as $val) {
													if ($self_name == "")
														$self_name = $floor_room_rack_arr[$val];
													else
														$self_name .= ", " . $floor_room_rack_arr[$val];
												}

												echo '<td width="100" class="wrap_break">' . $self_name . '</td>';

												$bin_box_name = '';
												$receive_bin_box_id = $receive_array[$row[csf("id")]]['bin_box'];
												$issue_bin_box_id = $issue_array[$row[csf("id")]]['bin_box'];

												$bin_box_id = array_unique(array_merge($receive_bin_box_id, $issue_bin_box_id));
												foreach ($bin_box_id as $val) {
													if ($bin_box_name == "")
														$bin_box_name = $floor_room_rack_arr[$val];
													else
														$bin_box_name .= ", " . $floor_room_rack_arr[$val];
												}

												echo '<td width="100" class="wrap_break">' . $bin_box_name . '</td>';
											} else {
												if ($allocated_qty_variable_settings == 1) {
													echo "<td width='100' align='right' class='wrap_break'><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
													echo '<td width="100" align="right" class="wrap_break">' . number_format($row[csf("available_qnty")], 2) . '</td>';
												} else {
													echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
													echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
												}
											}
											?>
											<td width="50" align="right" class="wrap_break"><? echo $ageOfDays; //$ageOfDays;         
																							?></td>
											<td width="50" align="right" class="wrap_break"><?
																							if ($stockInHand > 0)
																								echo $daysOnHand;
																							else
																								echo "&nbsp;"; //$daysOnHand;
																							?></td>

											<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
												<td width="140" align="left"><span class="comment more" style="word-wrap: break-word;word-break: break-all;"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
											<?  } else { ?>
												<td width="140" align="left"><span><? echo "&nbsp;"; ?></span></td>
											<? 	} ?>

											<td width="" align="center" class="wrap_break">
												<?
												/*$buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
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
										echo $buyer_all;*/
												?>
												<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a>
											</td>
										</tr>
							<?
										$i++;

										$total_opening_balance += $openingBalance;
										$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
										$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
										$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
										$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
										$total_total_rcv += $totalRcv;
										$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
										$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
										$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
										$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
										$total_total_delivery += $totalIssue;
										$total_stock_in_hand += $stockInHand;
										$total_alocatted += $row[csf("allocated_qnty")];
										$total_free_stock += $row[csf("available_qnty")];
										$sub_stock_value += $stock_value;
										$sub_stock_value_usd += $stock_value_usd;
										$total_transfer_out_qty += $transfer_out_qty;
										$total_transfer_in_qty += $transfer_in_qty;

										//grand total===========================
										$grand_total_opening_balance += $openingBalance;
										$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
										$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
										$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
										$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
										$grand_total_total_rcv += $totalRcv;
										$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
										$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
										$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
										$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
										$grand_total_total_delivery += $totalIssue;
										$grand_total_stock_in_hand += $stockInHand;
										$grand_total_alocatted += $row[csf("allocated_qnty")];
										$grand_total_free_stock += $row[csf("available_qnty")];
										$tot_stock_value += $stock_value;
										$tot_stock_value_usd += $stock_value_usd;

										$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
										$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
									}
								}
							}
							?>
							<tr bgcolor="#CCCCCC" style="font-weight:bold">
								<td colspan="10" align="right" class="wrap_break">Sub Total</td>
								<td width="110" class="wrap_break" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_purchase, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_inside_return, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_outside_return, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_in_qty, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_rcv_loan, 2); ?></td>
								<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_rcv, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_inside, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_outside, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_receive_return, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_out_qty, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_loan, 2); ?></td>
								<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_delivery, 2); ?></td>
								<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
								<?
								if ($show_val_column == 1) {
									echo '<td width="90" align="right">&nbsp;</td>';
									echo '<td width="110" class="wrap_break" align="right">' . number_format($sub_stock_value, 2) . '</td>';
									echo '<td width="100" align="right">&nbsp;</td>';
									echo '<td width="100" class="wrap_break" align="right">' . number_format($sub_stock_value_usd, 2) . '</td>';
								}

								if ($store_wise == 1) {
									echo '<td width="100">&nbsp;</td>';
									echo '<td width="100">&nbsp;</td>';
									echo '<td width="100">&nbsp;</td>';
									echo '<td width="100">&nbsp;</td>';
									echo '<td width="100">&nbsp;</td>';
									echo '<td width="100">&nbsp;</td>';
								} else {
									if ($allocated_qty_variable_settings == 1) {
										echo '<td width="100" align="right">' . number_format($total_alocatted, 2) . '</td>';
										echo '<td width="100" align="right">' . number_format($total_free_stock, 2) . '</td>';
									} else {
										echo '<td width="100" align="right">&nbsp;</td>';
										echo '<td width="100" align="right">&nbsp;</td>';
									}
								}
								?>
								<td width="50" align="right">&nbsp;</td>
								<td width="50" align="right">&nbsp;</td>
								<td width="" align="right">&nbsp;</td>
								<td width="" align="right">&nbsp;</td>
							</tr>
						</table>
					</div>
					<table style="width:<? echo $table_width + 20; ?>px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
						<tr class="tbl_bottom">
							<td width="30"></td>
							<td width="130"></td>
							<td width="60"></td>
							<td width="60"></td>
							<td width="150"></td>
							<td width="100"></td>
							<td width="120"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100" align="right">Grand Total</td>
							<td width="110" class="wrap_break" align="right" id="value_total_opening_balance"><? echo number_format($grand_total_opening_balance, 2); ?></td>
							<td width="90" class="wrap_break" align="right" id="value_total_purchase"><? echo number_format($grand_total_purchase, 2); ?></td>
							<td width="90" class="wrap_break" align="right" id="value_total_inside_return"><? echo number_format($grand_total_inside_return, 2); ?></td>
							<td width="90" class="wrap_break" align="right" id="value_total_outside_return"><? echo number_format($grand_total_outside_return, 2); ?></td>
							<td width="90" class="wrap_break" align="right" id="value_total_transfer_in"><? echo number_format($grand_total_transfer_in_qty, 2); ?></td>
							<td width="90" class="wrap_break" align="right" id="value_total_rcv_loan"><? echo number_format($grand_total_rcv_loan, 2); ?></td>
							<td width="100" class="wrap_break" align="right" id="value_total_total_rcv"><? echo number_format($grand_total_total_rcv, 2); ?></td>
							<td width="90" class="wrap_break" align="right" id="value_total_issue_inside"><? echo number_format($grand_total_issue_inside, 2); ?></td>
							<td width="90" class="wrap_break" align="right" id="value_total_issue_outside"><? echo number_format($grand_total_issue_outside, 2); ?></td>
							<td width="90" class="wrap_break" align="right" id="value_total_receive_return"><? echo number_format($grand_total_receive_return, 2); ?></td>
							<td width="90" align="right" class="wrap_break" id="value_total_transfer_out"><? echo number_format($grand_total_transfer_out_qty, 2); ?></td>
							<td width="90" align="right" class="wrap_break" id="value_total_issue_loan"><? echo number_format($grand_total_issue_loan, 2); ?></td>
							<td width="100" align="right" class="wrap_break" id="value_total_total_delivery"><? echo number_format($grand_total_total_delivery, 2); ?></td>
							<td width="100" align="right" class="wrap_break" id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 4); ?></td>
							<?
							if ($show_val_column == 1) {
								echo '<td width="90" align="right">&nbsp;</td>';
								echo '<td width="110" class="wrap_break" align="right">' . number_format($tot_stock_value, 2) . '</td>';
								echo '<td width="100" align="right">&nbsp;</td>';
								echo '<td width="100" class="wrap_break" align="right">' . number_format($tot_stock_value_usd, 2) . '</td>';
							}

							if ($store_wise == 1) {
								echo '<td width="100">&nbsp;</td>';
								echo '<td width="100">&nbsp;</td>';
								echo '<td width="100">&nbsp;</td>';
								echo '<td width="100">&nbsp;</td>';
								echo '<td width="100">&nbsp;</td>';
								echo '<td width="100">&nbsp;</td>';
							} else {
								if ($allocated_qty_variable_settings == 1) {
									echo '<td width="100" class="wrap_break" align="right" id="value_total_alocatted">' . number_format($grand_total_alocatted, 2) . '</td>';
									echo '<td width="100" class="wrap_break" align="right" id="value_total_free_stock">' . number_format($grand_total_free_stock, 2) . '</td>';
								} else {
									echo '<td width="100" align="right" id="value_total_alocatted">&nbsp;</td>';
									echo '<td width="100" align="right" id="value_total_free_stock">&nbsp;</td>';
								}
							}
							?>
							<td width="50" align="right">&nbsp;</td>
							<td width="50" align="right">&nbsp;</td>
							<td width="140" align="right">&nbsp;</td>
							<td width="" align="right">&nbsp;</td>
						</tr>
					</table>
				</div>
			<?
		} else if ($type == 14) {
			if ($value_with == 0)
				$search_cond .= "";
			else
				$search_cond .= "  and a.current_stock>0";

			$count_arr = array();

			if ($store_name != 0)  $store_cond = " and a.store_id in($store_name) ";
			if ($cbo_company_name != 0)  $company_cond = " and a.company_id=$cbo_company_name ";
			$date_array = array();
			$returnRes_date = "select a.prod_id, MIN(case when a.transaction_type in (1,4,5) and a.BALANCE_QNTY>0 then a.transaction_date else null end) as min_date, max(a.transaction_date) as max_date from inv_transaction a where a.is_deleted=0 and a.status_active=1 and a.item_category=1 $company_cond $store_cond group by prod_id";
			//echo $returnRes_date;

			$result_returnRes_date = sql_select($returnRes_date);
			foreach ($result_returnRes_date as $row) {
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
			}


			// $sql = "SELECT a.id, a.yarn_count_id, a.unit_of_measure, b.source, b.remarks, d.yarn_category_type, e.yarn_count, e.sequence_no,nvl(c.buyer_id,0) as buyer_id from product_details_master a, inv_receive_master b, inv_transaction c,lib_composition_array d,lib_yarn_count e where a.yarn_comp_type1st=d.id and a.id=c.prod_id and c.mst_id=b.id and a.yarn_count_id=e.id and a.item_category_id=1 and d.yarn_category_type >0 and b.source<>0 and c.status_active=1 and c.is_deleted=0 and b.entry_form=1 $company_cond $search_cond group by b.source, b.remarks, a.id, a.yarn_count_id, a.unit_of_measure, d.yarn_category_type, e.sequence_no, e.yarn_count,c.buyer_id order by e.sequence_no";

			$sql = "SELECT a.id, a.yarn_count_id, a.unit_of_measure, b.source, b.remarks, a.yarn_comp_type1st as yarn_category_type, e.yarn_count, e.sequence_no, NVL(c.buyer_id, 0) AS buyer_id FROM product_details_master a INNER JOIN inv_transaction c ON c.prod_id = a.id INNER JOIN inv_receive_master b ON b.id = c.mst_id LEFT JOIN lib_composition_array d ON a.yarn_comp_type1st = d.id and d.yarn_category_type > 0 LEFT JOIN lib_yarn_count e ON a.yarn_count_id = e.id   WHERE a.item_category_id = 1 AND b.source <> 0 AND c.status_active = 1 AND c.is_deleted = 0 AND b.entry_form = 1 AND a.company_id = 1 $company_cond $search_cond GROUP BY b.source, b.remarks, a.id, a.yarn_count_id, a.unit_of_measure, a.yarn_comp_type1st, e.sequence_no, e.yarn_count, c.buyer_id ORDER BY e.sequence_no";

			// echo $sql;
			$result = sql_select($sql);
			// echo "<pre>";
			// print_r($duplicateChk);
			$import_arr = array();
			$import_amount_arr = array();
			$local_arr = array();
			$local_amount_arr = array();
			$duplicate_check = array();
			$mainArr = array();
			foreach ($result as $row) {
				if ($duplicateChk[$row[csf('id')]] == "") {
					$duplicateChk[$row[csf('id')]] = $row[csf('id')];
					if ($row[csf("source")] == 1 || $row[csf("source")] == 2) {
						$transfer_in_qty1 = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
						$transfer_out_qty1 = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
						$transfer_in_amt1 = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
						$transfer_out_amt1 = $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];

						$trans_out_total_opening1 = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
						$trans_in_total_opening1 = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];
						$trans_in_total_opening_amt1 = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];
						$trans_out_total_opening_amt1 = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];

						$openingBalance1 = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening1) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening1);

						$openingBalanceAmt1 = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt1) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt1);

						$totalRcv1 = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty1;

						$totalRcvAmt1 = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $receive_array[$row[csf("id")]]['rcv_adjustment_amt'] + $transfer_in_amt1;

						$totalIssue1 = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty1;

						$totalIssueAmt1 = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] + $issue_array[$row[csf("id")]]['issue_loan_amt'] + $issue_array[$row[csf("id")]]['issue_adjustment_amt'] + $transfer_out_amt1;

						$stockInHand1 = ($openingBalance1 + $totalRcv1) - $totalIssue1;
						$stockInHandAmt1 = $openingBalanceAmt1 + $totalRcvAmt1 - $totalIssueAmt1;


						$import_arr[$row[csf("yarn_category_type")]][$row[csf("yarn_count_id")]][$row[csf("buyer_id")]]['import_yarn'] += $stockInHand1;

						$import_amount_arr[$row[csf("yarn_category_type")]][$row[csf("yarn_count_id")]][$row[csf("buyer_id")]]['import_yarn'] += ($stockInHandAmt1 / $exchange_rate);
					} else if ($row[csf("source")] == 3) {
						$transfer_in_qty3 = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
						$transfer_out_qty3 = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
						$transfer_in_amt3 = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
						$transfer_out_amt3 = $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];

						$trans_out_total_opening3 = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
						$trans_in_total_opening3 = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];
						$trans_in_total_opening_amt3 = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];
						$trans_out_total_opening_amt3 = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];

						$openingBalance3 = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening3) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening3);

						$openingBalanceAmt3 = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt3) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt3);

						$totalRcv3 = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty3;

						$totalRcvAmt3 = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $receive_array[$row[csf("id")]]['rcv_adjustment_amt'] + $transfer_in_amt3;

						$totalIssue3 = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty3;

						$totalIssueAmt3 = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] + $issue_array[$row[csf("id")]]['issue_loan_amt'] + $issue_array[$row[csf("id")]]['issue_adjustment_amt'] + $transfer_out_amt3;

						$stockInHand3 = ($openingBalance3 + $totalRcv3) - $totalIssue3;
						$stockInHandAmt3 = $openingBalanceAmt3 + $totalRcvAmt3 - $totalIssueAmt3;

						$local_arr[$row[csf("yarn_category_type")]][$row[csf("yarn_count_id")]][$row[csf("buyer_id")]]['local_yarn'] += $stockInHand3;
						$local_amount_arr[$row[csf("yarn_category_type")]][$row[csf("yarn_count_id")]][$row[csf("buyer_id")]]['local_yarn'] += ($stockInHandAmt3 / $exchange_rate);
					}

					$mainArr[$row[csf("yarn_category_type")]][$row[csf("yarn_count_id")]][$row[csf("buyer_id")]]['unit_of_measure'] = $row[csf("unit_of_measure")];
					$mainArr[$row[csf("yarn_category_type")]][$row[csf("yarn_count_id")]][$row[csf("buyer_id")]]['source'] .= $row[csf("source")] . ',';
					$mainArr[$row[csf("yarn_category_type")]][$row[csf("yarn_count_id")]][$row[csf("buyer_id")]]['prod_id'] = $row[csf("id")];
					$mainArr[$row[csf("yarn_category_type")]][$row[csf("yarn_count_id")]][$row[csf("buyer_id")]]['remarks'] = $row[csf("remarks")];
				}
			}
			// echo "<pre>";print_r($mainArr);
			$i = 1;
			ob_start();
			?>
				<style>
					.wrd_brk {
						word-break: break-all;
						word-wrap: break-word;
					}
				</style>
				<div style="margin-top:5px">
					<table width="1170" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
						<thead>
							<tr class="form_caption" style="border:none;">
								<td colspan="8" align="center" style="border:none;font-size:16px; font-weight:bold">Yarn Stock Summary Report</td>
							</tr>
							<tr style="border:none;">
								<td colspan="8" align="center" style="border:none; font-size:14px;">
									Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
								</td>
							</tr>
							<tr style="border:none;">
								<td colspan="8" align="center" style="border:none;font-size:12px; font-weight:bold">
									<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
								</td>
							</tr>
							<tr>
								<th width="150" rowspan="2" style="font-size:16px;">Yarn Category</th>
								<th width="50" rowspan="2" style="font-size:16px;">SL</th>
								<th width="100" rowspan="2" style="font-size:16px;">Yarn Count</th>
								<th width="150" rowspan="2" style="font-size:16px;">Buyer</th>
								<th width="90" rowspan="2" style="font-size:16px;">Unit</th>
								<th width="200" colspan="2" style="font-size:16px;">Stock Position</th>
								<th width="150" rowspan="2" style="font-size:16px;">Total Stock Quantity</th>
								<th rowspan="2" style="font-size:16px;">Remarks</th>
							</tr>
							<tr>
								<th width="150">Local Yarn</th>
								<th width="150">Imported Yarn</th>
							</tr>
						</thead>
					</table>
					<div style="width:1190px; overflow-y:scroll; max-height:350px" id="scroll_body">
						<table width="1170" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
							<?
							$yarn_cat_count   = array();
							$yarn_count_count = array();
							foreach ($mainArr as $k_yct => $v_yct) {
								foreach ($v_yct as $k_count => $k_data) {
									foreach ($k_data as $k_buyer => $row) {
										// echo "<pre>";
										// print_r($row);
										$ageOfDays = datediff("d", $date_array[$row["prod_id"]]['min_date'], date("Y-m-d"));
										//$daysOnHand = datediff("d", $date_array[$row["prod_id"]]['max_date'], date("Y-m-d"));

										$local_sources = array_unique(explode(",", chop($row['source'], ",")));
										$local_yarn_qty = 0;
										foreach ($local_sources as $val) {
											if ($val == 3) {
												$local_yarn_qty += $local_arr[$k_yct][$k_count][$k_buyer]['local_yarn'];
											}
										}


										$import_sources = array_unique(explode(",", chop($row['source'], ",")));
										$import_yarn_qty = 0;
										foreach ($import_sources as $val) {
											if ($val == 1 || $val == 2) {
												$import_yarn_qty += $import_arr[$k_yct][$k_count][$k_buyer]['import_yarn'];
											}
										}

										$stockInHand = $local_yarn_qty + $import_yarn_qty;

										if ($value_with == 1) {
											if (number_format($stockInHand, 2) > 0.00) {
												$yarn_cat_count[$k_yct]++;
												//$yarn_count_count[$k_yct][$k_count]++;
											}
										} else {
											if ($stockInHand >= 0) {
												$yarn_cat_count[$k_yct]++;
												//$yarn_count_count[$k_yct][$k_count]++;
											}
										}
									}
								}
							}




							$gtot_local_yarn_qty = 0;
							$gtot_local_yarn_amount = 0;
							$gtot_import_yarn_qty = 0;
							$gtot_import_yarn_amount = 0;
							$gtot_stock_qty = 0;
							$gtot_stock_amount = 0;
							foreach ($mainArr as $k_yct => $v_yct) {
								$tot_local_yarn_qty = 0;
								$tot_import_yarn_qty = 0;
								$tot_stock_qty = 0;
								$i = 1;
								foreach ($v_yct as $k_count => $k_data) {
									foreach ($k_data as $k_buyer => $row) {

										if ($i % 2 == 0) $bgcolor = "#E9F3FF";
										else $bgcolor = "#FFFFFF";


										$yarn_cat_span = $yarn_cat_count[$k_yct];
										$yarn_count_span = $yarn_count_count[$k_yct][$k_count];


										$local_sources = array_unique(explode(",", chop($row['source'], ",")));
										$local_yarn_qty = 0;
										$local_yarn_amount = 0;
										foreach ($local_sources as $val) {
											if ($val == 3) {
												$local_yarn_qty += $local_arr[$k_yct][$k_count][$k_buyer]['local_yarn'];
												$local_yarn_amount += $local_amount_arr[$k_yct][$k_count][$k_buyer]['local_yarn'];
											}
										}


										$import_sources = array_unique(explode(",", chop($row['source'], ",")));
										$import_yarn_qty = 0;
										$import_yarn_amount = 0;
										foreach ($import_sources as $val) {
											if ($val == 1 || $val == 2) {
												$import_yarn_qty += $import_arr[$k_yct][$k_count][$k_buyer]['import_yarn'];
												$import_yarn_amount += $import_amount_arr[$k_yct][$k_count][$k_buyer]['import_yarn'];
											}
										}

										$stockInHand = $local_yarn_qty + $import_yarn_qty;
										$stockInHandAmount = $local_yarn_amount + $import_yarn_amount;

										if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0)) {

											if ($value_with == 1) {
												if (number_format($stockInHand, 2) > 0.00) {

							?>
													<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

														<?
														if (!in_array($k_yct, $k_chk)) {
															$k_chk[] = $k_yct;
														?>
															<td width="150" class="wrd_brk" valign="middle" align="center" rowspan="<? echo $yarn_cat_span; ?>">
																<p><? echo $composition_array[$k_yct]; //echo $yarn_type_for_entry[$k_yct]; 
																	?>&nbsp;</p>
															</td>
														<? } ?>
														<td width="50" class="wrd_brk" rowspan="<? echo $yarn_count_span; ?>"><? echo $i; ?></td>
														<td width="100" class="wrd_brk" valign="middle" align="center" rowspan="<? echo $yarn_count_span; ?>">
															<p><? echo $yarn_count_arr[$k_count]; ?>&nbsp;</p>
														</td>

														<td width="150" class="wrd_brk" align="center">
															<p><? echo $buyer_dtls[$k_buyer]; ?>&nbsp;</p>
														</td>
														<td width="90" class="wrd_brk" align="center">
															<p><? echo $unit_of_measurement[$row['unit_of_measure']]; ?>&nbsp;</p>
														</td>
														<td width="150" class="wrd_brk" align="right">
															<?
															echo number_format($local_yarn_qty, 2);
															?>&nbsp;</td>
														<td width="150" class="wrd_brk" align="right">
															<?
															echo number_format($import_yarn_qty, 2);
															?>&nbsp;</td>
														<td width="150" class="wrd_brk" align="right"><? echo number_format($stockInHand, 2); ?>&nbsp;</td>
														<td class="wrd_brk" align="center"><? echo $row['remarks']; ?></td>
													</tr>
												<?
													$i++;

													$tot_local_yarn_qty += $local_yarn_qty;
													$tot_import_yarn_qty += $import_yarn_qty;
													$tot_stock_qty += $stockInHand;

													$gtot_local_yarn_qty += $local_yarn_qty;
													$gtot_local_yarn_amount += $local_yarn_amount;
													$gtot_import_yarn_qty += $import_yarn_qty;
													$gtot_import_yarn_amount += $import_yarn_amount;
													$gtot_stock_qty += $stockInHand;
													$gtot_stock_amount += $stockInHandAmount;
												}
											} else {
												if ($stockInHand >= 0) {

												?>
													<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

														<?
														if (!in_array($k_yct, $k_chk)) {
															$k_chk[] = $k_yct;
														?>
															<td width="50" class="wrd_brk" rowspan="<? echo $yarn_cat_span; ?>"><? echo $i; ?></td>
															<td width="150" class="wrd_brk" valign="middle" align="center" rowspan="<? echo $yarn_cat_span; ?>">
																<p><? echo $composition_array[$k_yct]; //echo $yarn_type_for_entry[$k_yct]; 
																	?>&nbsp;</p>
															</td>
														<? } ?>
														<td width="100" class="wrd_brk" valign="middle" align="center" rowspan="<? echo $yarn_count_span; ?>">
															<p><? echo $yarn_count_arr[$k_count]; ?>&nbsp;</p>
														</td>

														<td width="150" class="wrd_brk" align="center">
															<p><? echo $buyer_dtls[$k_buyer]; ?>&nbsp;</p>
														</td>
														<td width="90" class="wrd_brk" align="center">
															<p><? echo $unit_of_measurement[$row['unit_of_measure']]; ?>&nbsp;</p>
														</td>
														<td width="150" class="wrd_brk" align="right">
															<?
															echo number_format($local_yarn_qty, 2);
															?>&nbsp;</td>
														<td width="150" class="wrd_brk" align="right">
															<?
															echo number_format($import_yarn_qty, 2);
															?>&nbsp;</td>
														<td width="150" class="wrd_brk" align="right"><? echo number_format($stockInHand, 2); ?>&nbsp;</td>
														<td class="wrd_brk" align="center"><? echo $row['remarks']; ?></td>
													</tr>
								<?

													$i++;
													$tot_local_yarn_qty += $local_yarn_qty;
													$tot_import_yarn_qty += $import_yarn_qty;
													$tot_stock_qty += $stockInHand;

													$gtot_local_yarn_qty += $local_yarn_qty;
													$gtot_local_yarn_amount += $local_yarn_amount;
													$gtot_import_yarn_qty += $import_yarn_qty;
													$gtot_import_yarn_amount += $import_yarn_amount;
													$gtot_stock_qty += $stockInHand;
													$gtot_stock_amount += $stockInHandAmount;
												}
											}
										}
									}
								}
								?>
								<tr bgcolor="#CCCCCC" style="font-weight:bold">
									<td colspan="5" align="right">Sub Total :</td>
									<td align="right"><? echo number_format($tot_local_yarn_qty, 2); ?>&nbsp;</td>
									<td align="right"><? echo number_format($tot_import_yarn_qty, 2); ?>&nbsp;</td>
									<td align="right"><? echo number_format($tot_stock_qty, 2); ?>&nbsp;</td>
									<td align="right">&nbsp;</td>
								</tr>
							<?


							}
							?>

						</table>
					</div>
					<table width="1170" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
						<tr class="tbl_bottom">
							<td width="50">&nbsp;</td>
							<td width="150">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="150">&nbsp;</td>
							<td width="90" style="font-weight: bold; font-size:15px;">Grand Total :</td>
							<td align="right" width="150" style="font-weight: bold; font-size:16px;"><? echo number_format($gtot_local_yarn_qty, 2); ?></th>
							<td align="right" width="150" style="font-weight: bold; font-size:16px;"><? echo number_format($gtot_import_yarn_qty, 2); ?></th>
							<td align="right" width="150" style="font-weight: bold; font-size:16px;"><? echo number_format($gtot_stock_qty, 2); ?></td>
							<td align="right">&nbsp;</td>
						</tr>
					</table>
					<br>
					<table width="1170" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
						<tr class="tbl_bottom">
							<td width="50">&nbsp;</td>
							<td width="150">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="150">&nbsp;</td>
							<td width="90" style="font-weight: bold; font-size:15px;">Total Amount:</td>
							<td align="right" width="150" style="font-weight: bold; font-size:16px;"><? echo '$' . number_format($gtot_local_yarn_amount, 2); ?></th>
							<td align="right" width="150" style="font-weight: bold; font-size:16px;"><? echo '$' . number_format($gtot_import_yarn_amount, 2); ?></th>
							<td align="right" width="150" style="font-weight: bold; font-size:16px;"><? echo '$' . number_format($gtot_stock_amount, 2); ?></td>
							<td align="right">&nbsp;</td>
						</tr>
					</table>
				</div>
			<?
		} else if ($type == 15) {
			if ($value_with == 0)
				$search_cond .= "";
			else
				$search_cond .= "  and a.current_stock>0";
			$pipe_line_purchase_arr = array();
			$pipe_line_ydbooking_arr = array();
			$pipe_line_pi_arr = array();
			$sql_booking = sql_select("select b.yarn_count, b.yarn_type, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.color_name, sum(b.supplier_order_quantity) as qnty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.pay_mode!=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.yarn_count, b.yarn_type, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.color_name"); //and a.wo_number='OG-15-00043'
			foreach ($sql_booking as $row) {
				if ($row[csf("yarn_comp_type2nd")] != 0) {
					$compositionDtls = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")] . '**' . $row[csf("yarn_comp_type2nd")] . '**' . $row[csf("yarn_comp_percent2nd")];
				} else {
					$compositionDtls = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")];
				}
				$pipe_line_purchase_arr[$row[csf("yarn_count")]][$row[csf("yarn_type")]][$compositionDtls][$row[csf("color_name")]] = $row[csf("qnty")];
			}
			//print_r($pipe_line_purchase_arr);
			$sql_ywdbooking = sql_select("select c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.color, sum(b.yarn_wo_qty) as qnty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and c.item_category_id=1 and a.pay_mode!=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.color");
			foreach ($sql_ywdbooking as $row) {
				if ($row[csf("yarn_comp_type2nd")] != 0) {
					$compositionDtl = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")] . '**' . $row[csf("yarn_comp_type2nd")] . '**' . $row[csf("yarn_comp_percent2nd")];
				} else {
					$compositionDtl = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")];
				}
				$pipe_line_ydbooking_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$compositionDtl][$row[csf("color")]] = $row[csf("qnty")];
			}

			$sql_pi = sql_select("select b.count_name, b.yarn_type, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.color_id, sum(b.quantity) as qnty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.count_name, b.yarn_type, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.color_id");  //and a.id in (1161,1162)
			foreach ($sql_pi as $row) {
				if ($row[csf("yarn_composition_item2")] != 0) {
					$compositionDtlPi = $row[csf("yarn_composition_item1")] . '**' . $row[csf("yarn_composition_percentage1")] . '**' . $row[csf("yarn_composition_item2")] . '**' . $row[csf("yarn_composition_percentage2")];
				} else {
					$compositionDtlPi = $row[csf("yarn_composition_item1")] . '**' . $row[csf("yarn_composition_percentage1")];
				}
				$pipe_line_pi_arr[$row[csf("count_name")]][$row[csf("yarn_type")]][$compositionDtlPi][$row[csf("color_id")]] = $row[csf("qnty")];
			}
			//print_r ($pipe_line_pi_arr);
			$pipelineArr = array();

			$sql_ppl = "select a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by  a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color order by a.yarn_type, a.yarn_count_id";

			/* if ($cbo_dyed_type == 0) {
				$sql_ppl = "select a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by  a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color order by a.yarn_type, a.yarn_count_id";
			} else {
				$sql_ppl = "select  a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color, c.receive_basis,	c.receive_purpose from product_details_master a, inv_transaction b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.entry_form=1 and b.transaction_type in(1,4) $company_cond $search_cond group by a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color, c.receive_basis, c.receive_purpose order by a.yarn_type, a.yarn_count_id";  //and c.recv_number in ('OG-YRV-15-00040','OG-YRV-15-00041','OG-YRV-15-00042','OG-YRV-15-00043','OG-YRV-15-00044')
			}*/

			$result_ppl = sql_select($sql_ppl);
			foreach ($result_ppl as $row) {
				if ($row[csf("yarn_comp_type2nd")] != 0) {
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")] . '**' . $row[csf("yarn_comp_type2nd")] . '**' . $row[csf("yarn_comp_percent2nd")];
				} else {
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")];
				}
				if ($row[csf("receive_basis")] == 2) {
					if ($row[csf("receive_purpose")] == 16) {
						$pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $pipe_line_purchase_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]];
					} else if ($row[csf("receive_purpose")] == 2) {
						$pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $pipe_line_ydbooking_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]];
					}
				} else {
					$pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $pipe_line_pi_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]];
				}
				//$pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]]=$pipe_line_qty;
			}
			//print_r($pipelineArr);


			if ($store_name > 0) {
				$prodIds_cond = "  " . where_con_using_array($prodIdsArr, 0, 'a.id') . " ";
			} else {
				$prodIds_cond = "";
			}

			if ($store_name != 0)  $store_cond = " and store_id in($store_name) ";
			if ($cbo_company_name != 0)  $company_cond = " and company_id=$cbo_company_name ";

			$date_array = array();
			$returnRes_date = "select prod_id, MIN(case when transaction_type in (1,4,5) and BALANCE_QNTY>0 then transaction_date else null end) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 $company_cond $store_cond group by prod_id";
			$result_returnRes_date = sql_select($returnRes_date);
			foreach ($result_returnRes_date as $row) {
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
			}

			$type_arr = array();
			$storeArr = array();

			if ($cbo_dyed_type == 0) {
				$sql = "select a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color,a.store_id from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond $prodIds_cond group by a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color,a.store_id order by a.yarn_type, a.yarn_count_id";
			} else {
				$sql = "select a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color,a.store_id from product_details_master a, inv_transaction b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.entry_form=1 and b.transaction_type=1 $company_cond $search_cond $prodIds_cond group by a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color,a.store_id order by a.yarn_type, a.yarn_count_id";
			}
			//echo $sql;die;
			$result = sql_select($sql);
			foreach ($result as $row) {
				//$pipe_line_qty=0;
				$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
				$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

				$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
				$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

				$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
				$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
				$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;
				$stockInHand = ($openingBalance + $totalRcv) - $totalIssue;
				if ($row[csf("yarn_comp_type2nd")] != 0) {
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")] . '**' . $row[csf("yarn_comp_type2nd")] . '**' . $row[csf("yarn_comp_percent2nd")];
				} else {
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")];
				}

				$pipe_line_qty = $pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]];

				$type_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $stockInHand;
				$pipe_line_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] = $pipe_line_qty;

				$prodIdArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] = $row[csf("id")];

				$storeArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] = $receive_array[$row[csf("id")]]['store_id'];
			}
			//echo "<pre>";print_r($storeArr);echo "</pre>";
			$colorArr = return_library_array("select id, color_name from lib_color", "id", "color_name");
			$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
			$i = 1;
			//ob_start();
			?>
				<div style="margin-top:5px">
					<table width="802" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
						<thead>
							<tr class="form_caption" style="border:none;">
								<td colspan="7" align="center" style="border:none;font-size:16px; font-weight:bold">Count , Composition And Color Wise Yarn Stock Summary Report</td>
							</tr>
							<tr style="border:none;">
								<td colspan="7" align="center" style="border:none; font-size:14px;">
									Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
								</td>
							</tr>
							<tr style="border:none;">
								<td colspan="7" align="center" style="border:none;font-size:12px; font-weight:bold">
									<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
								</td>
							</tr>
							<tr>
								<th width="50">SL</th>
								<th width="100">Store</th>
								<th width="100">Count</th>
								<th width="200">Composition</th>
								<th width="100">Type</th>
								<th width="100">Color</th>
								<th>Stock In Hand</th>
							</tr>
						</thead>
					</table>
					<div style="width:820px; overflow-y:scroll; max-height:350px" id="scroll_body">
						<table width="802" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
							<?
							$tot_stock_qty = 0;
							$sub_count = '';
							foreach ($type_arr as $count => $value) {
								foreach ($value as $type => $type_val) {
									foreach ($type_val as $compo => $comp_val) {
										foreach ($comp_val as $color => $stock_qty) {

											if ($i % 2 == 0) $bgcolor = "#E9F3FF";
											else $bgcolor = "#FFFFFF";


											$pipeline_qty = $pipe_line_arr[$count][$type][$compo][$color];
											$store_id = $storeArr[$count][$type][$compo][$color];
											$prod_id = $prodIdArr[$count][$type][$compo][$color];
											$bal_pipeline_qty = $pipeline_qty - $stock_qty;
											//echo $store_id.'=='.$prod_id;
											$ex_comp = explode('**', $compo);
											$comp_1type = $ex_comp[0];
											$comp_1per = $ex_comp[1];
											$comp_2type = $ex_comp[2];
											$comp_2per = $ex_comp[3];
											$fullCompositionValue = "";
											if ($comp_2type != 0) {
												$fullCompositionValue = $composition[$comp_1type] . ',' . $comp_1per . '%  ' . $composition[$comp_2type] . ',' . $comp_2per . '%';
											} else {
												$fullCompositionValue = $composition[$comp_1type] . ',' . $comp_1per . '% ';
											}

											$ageOfDays = datediff("d", $date_array[$prod_id]['min_date'], date("Y-m-d"));

											if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stock_qty > $txt_qnty) || ($get_upto_qnty == 2 && $stock_qty < $txt_qnty) || ($get_upto_qnty == 3 && $stock_qty >= $txt_qnty) || ($get_upto_qnty == 4 && $stock_qty <= $txt_qnty) || ($get_upto_qnty == 5 && $stock_qty == $txt_qnty) || $get_upto_qnty == 0)) {
												if (!in_array($count, $checkArr)) {
													$checkArr[$i] = $count;
													if ($i > 1) {
							?>
														<tr bgcolor="#CCCCCC" style="font-weight:bold">
															<td colspan="6" align="right"><? echo $yarn_count_arr[$sub_count]; ?> Total</td>
															<td align="right"><? echo number_format($count_tot_qnty, 2); ?></td>
														</tr>
												<?
														$count_tot_qnty = 0;
													}
												}
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
													<td width="50"><? echo $i; ?></td>
													<td width="100">
														<p><? echo $store_arr[$store_id]; ?>&nbsp;</p>
													</td>
													<td width="100">
														<p><? echo $yarn_count_arr[$count]; ?>&nbsp;</p>
													</td>
													<td width="200">
														<p><? echo $fullCompositionValue; ?>&nbsp;</p>
													</td>
													<td width="100">
														<p><? echo $yarn_type[$type]; ?>&nbsp;</p>
													</td>
													<td width="100">
														<p><? echo $colorArr[$color]; ?>&nbsp;</p>
													</td>
													<td align="right"><? echo number_format($stock_qty, 2); ?></td>
												</tr>
							<?
												$i++;

												$count_tot_qnty += $stock_qty;
												$tot_stock_qty += $stock_qty;
												$tot_pipeline_qty += $bal_pipeline_qty;
												$sub_count = $count;
											}
										}
									}
								}
							}
							?>
							<tr bgcolor="#CCCCCC" style="font-weight:bold">
								<td colspan="6" align="right"><? echo $yarn_count_arr[$sub_count]; ?> Total</td>
								<td align="right"><? echo number_format($count_tot_qnty, 2);  ?></td>
							</tr>

						</table>
					</div>
					<table width="820" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
						<tr class="tbl_bottom">
							<td width="50">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="200">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">Grand Total : </td>
							<td align="right" style="padding-right:18px"><? echo number_format($tot_stock_qty, 2); ?></td>
						</tr>
					</table>
				</div>
			<?
		} else if ($type == 16) {
			if ($value_with == 0)
				$search_cond .= "";
			else
				$search_cond .= "  and a.current_stock>0";
			$pipe_line_purchase_arr = array();
			$pipe_line_ydbooking_arr = array();
			$pipe_line_pi_arr = array();
			$sql_booking = sql_select("select b.yarn_count, b.yarn_type, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.color_name, sum(b.supplier_order_quantity) as qnty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.pay_mode!=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.yarn_count, b.yarn_type, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.color_name"); //and a.wo_number='OG-15-00043'
			foreach ($sql_booking as $row) {
				if ($row[csf("yarn_comp_type2nd")] != 0) {
					$compositionDtls = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")] . '**' . $row[csf("yarn_comp_type2nd")] . '**' . $row[csf("yarn_comp_percent2nd")];
				} else {
					$compositionDtls = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")];
				}
				$pipe_line_purchase_arr[$row[csf("yarn_count")]][$row[csf("yarn_type")]][$compositionDtls][$row[csf("color_name")]] = $row[csf("qnty")];
			}
			//print_r($pipe_line_purchase_arr);
			$sql_ywdbooking = sql_select("select c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.color, sum(b.yarn_wo_qty) as qnty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and c.item_category_id=1 and a.pay_mode!=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.color");
			foreach ($sql_ywdbooking as $row) {
				if ($row[csf("yarn_comp_type2nd")] != 0) {
					$compositionDtl = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")] . '**' . $row[csf("yarn_comp_type2nd")] . '**' . $row[csf("yarn_comp_percent2nd")];
				} else {
					$compositionDtl = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")];
				}
				$pipe_line_ydbooking_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$compositionDtl][$row[csf("color")]] = $row[csf("qnty")];
			}

			$sql_pi = sql_select("select b.count_name, b.yarn_type, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.color_id, sum(b.quantity) as qnty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.count_name, b.yarn_type, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.color_id");  //and a.id in (1161,1162)
			foreach ($sql_pi as $row) {
				if ($row[csf("yarn_composition_item2")] != 0) {
					$compositionDtlPi = $row[csf("yarn_composition_item1")] . '**' . $row[csf("yarn_composition_percentage1")] . '**' . $row[csf("yarn_composition_item2")] . '**' . $row[csf("yarn_composition_percentage2")];
				} else {
					$compositionDtlPi = $row[csf("yarn_composition_item1")] . '**' . $row[csf("yarn_composition_percentage1")];
				}
				$pipe_line_pi_arr[$row[csf("count_name")]][$row[csf("yarn_type")]][$compositionDtlPi][$row[csf("color_id")]] = $row[csf("qnty")];
			}
			//print_r ($pipe_line_pi_arr);
			$pipelineArr = array();

			$sql_ppl = "select a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by  a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color order by a.yarn_type, a.yarn_count_id";

			/* if ($cbo_dyed_type == 0) {
				$sql_ppl = "select a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by  a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color order by a.yarn_type, a.yarn_count_id";
			} else {
				$sql_ppl = "select  a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color, c.receive_basis,	c.receive_purpose from product_details_master a, inv_transaction b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.entry_form=1 and b.transaction_type in(1,4) $company_cond $search_cond group by a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color, c.receive_basis, c.receive_purpose order by a.yarn_type, a.yarn_count_id";  //and c.recv_number in ('OG-YRV-15-00040','OG-YRV-15-00041','OG-YRV-15-00042','OG-YRV-15-00043','OG-YRV-15-00044')
			}*/

			$result_ppl = sql_select($sql_ppl);
			foreach ($result_ppl as $row) {
				if ($row[csf("yarn_comp_type2nd")] != 0) {
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")] . '**' . $row[csf("yarn_comp_type2nd")] . '**' . $row[csf("yarn_comp_percent2nd")];
				} else {
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")];
				}
				if ($row[csf("receive_basis")] == 2) {
					if ($row[csf("receive_purpose")] == 16) {
						$pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $pipe_line_purchase_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]];
					} else if ($row[csf("receive_purpose")] == 2) {
						$pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $pipe_line_ydbooking_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]];
					}
				} else {
					$pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $pipe_line_pi_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]];
				}
				//$pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]]=$pipe_line_qty;
			}
			//print_r($pipelineArr);
			$type_arr = array();

			if ($cbo_dyed_type == 0) {
				$sql = "select a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color order by a.yarn_type, a.yarn_count_id";
			} else {
				$sql = "select a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color from product_details_master a, inv_transaction b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.entry_form=1 and b.transaction_type=1 $company_cond $search_cond group by a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color order by a.yarn_type, a.yarn_count_id";
			}
			//echo $sql;die;
			$result = sql_select($sql);
			foreach ($result as $row) {
				//$pipe_line_qty=0;
				$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
				$transfer_in_amt = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
				$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

				$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
				$trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];
				$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];
				$trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];

				$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);

				$openingBalanceAmt = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt);

				$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;

				$totalRcvAmt = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $transfer_in_amt;

				$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;

				$stockInHand  = ($openingBalance + $totalRcv) - $totalIssue;
				$tot_rcv_qnty = $openingBalance + $totalRcv;
				$tot_rcv_amt  = $openingBalanceAmt + $totalRcvAmt;
				//$avg_rate     = $tot_rcv_amt/$tot_rcv_qnty;

				if ($row[csf("yarn_comp_type2nd")] != 0) {
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")] . '**' . $row[csf("yarn_comp_type2nd")] . '**' . $row[csf("yarn_comp_percent2nd")];
				} else {
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")];
				}

				$pipe_line_qty = $pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]];

				$type_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $stockInHand;

				$tot_rcv_qnty_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $tot_rcv_qnty;
				$tot_rcv_amt_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $tot_rcv_amt;

				$pipe_line_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] = $pipe_line_qty;
			}
			//print_r($pipe_line_arr);
			$colorArr = return_library_array("select id, color_name from lib_color", "id", "color_name");
			$i = 1;
			//ob_start();
			?>
				<style>
					.wrd_brk {
						word-break: break-all;
						word-wrap: break-word;
					}
				</style>
				<div style="margin-top:5px">
					<table width="1002" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
						<thead>
							<tr class="form_caption" style="border:none;">
								<td colspan="7" align="center" style="border:none;font-size:16px; font-weight:bold">Composition Wise Yarn Stock Summary Report</td>
							</tr>
							<tr style="border:none;">
								<td colspan="7" align="center" style="border:none; font-size:14px;">
									Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
								</td>
							</tr>
							<tr style="border:none;">
								<td colspan="7" align="center" style="border:none;font-size:12px; font-weight:bold">
									<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
								</td>
							</tr>
							<tr>
								<th width="50">SL</th>
								<th width="100">Count</th>
								<th width="200">Composition</th>
								<th width="100">Type</th>
								<th width="100">Color</th>
								<th width="100">Stock In Hand</th>
								<th width="100">AVG. Rate (USD)</th>
								<th width="100">Value (USD)</th>
								<th>Pipe Line</th>
							</tr>
						</thead>
					</table>
					<div style="width:1020px; overflow-y:scroll; max-height:350px" id="scroll_body">
						<table width="1002" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
							<?
							$tot_stock_qty = $avg_rate = $stock_value = $avg_rate_usd = $stock_value_usd = $tot_stock_value_usd = 0;
							foreach ($type_arr as $count => $value) {
								foreach ($value as $type => $type_val) {
									foreach ($type_val as $compo => $comp_val) {
										foreach ($comp_val as $color => $stock_qty) {

											if ($value_with == 1) {
												if (number_format($stock_qty, 2) > 0.00) {
													$pipeline_qty = $pipe_line_arr[$count][$type][$compo][$color];
													$bal_pipeline_qty = $pipeline_qty - $stock_qty;

													$rcv_qnty = $tot_rcv_qnty_arr[$count][$type][$compo][$color];
													$rcv_amt = $tot_rcv_amt_arr[$count][$type][$compo][$color];

													$avg_rate = $rcv_amt / $rcv_qnty;
													$avg_rate = (is_nan($avg_rate) == 1 || is_infinite($avg_rate)) ? 0 : $avg_rate;

													$stock_value = $stock_qty * $avg_rate;

													$avg_rate_usd = $avg_rate / $exchange_rate;
													$avg_rate_usd = (is_nan($avg_rate_usd) == 1 || is_infinite($avg_rate_usd)) ? 0 : $avg_rate_usd;

													$stock_value_usd = $stock_value / $exchange_rate;
													$stock_value_usd = (is_nan($stock_value_usd) == 1 || is_infinite($stock_value_usd)) ? 0 : $stock_value_usd;


													//echo $pipeline_qty.'=='.$stock_qty;
													$ex_comp = explode('**', $compo);
													$comp_1type = $ex_comp[0];
													$comp_1per = $ex_comp[1];
													$comp_2type = $ex_comp[2];
													$comp_2per = $ex_comp[3];
													$fullCompositionValue = "";
													if ($comp_2type != 0) {
														$fullCompositionValue = $composition[$comp_1type] . ',' . $comp_1per . '%  ' . $composition[$comp_2type] . ',' . $comp_2per . '%';
													} else {
														$fullCompositionValue = $composition[$comp_1type] . ',' . $comp_1per . '% ';
													}

													if ($i % 2 == 0)
														$bgcolor = "#E9F3FF";
													else
														$bgcolor = "#FFFFFF";

							?>
													<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
														<td width="50"><? echo $i; ?></td>
														<td width="100" class="wrd_brk">
															<p><? echo $yarn_count_arr[$count]; ?>&nbsp;</p>
														</td>
														<td width="200" class="wrd_brk">
															<p><? echo $fullCompositionValue; ?>&nbsp;</p>
														</td>
														<td width="100" class="wrd_brk">
															<p><? echo $yarn_type[$type]; ?>&nbsp;</p>
														</td>
														<td width="100" class="wrd_brk">
															<p><? echo $colorArr[$color]; ?>&nbsp;</p>
														</td>
														<td width="100" align="right" class="wrd_brk"> <? echo number_format($stock_qty, 2); ?></td>
														<td width="100" align="right" class="wrd_brk" title="( Value/Stock In Hand )"><? echo number_format($avg_rate_usd, 4); ?></td>
														<td width="100" align="right" class="wrd_brk"><? echo number_format($stock_value_usd, 2); ?></td>
														<td align="right" class="wrd_brk"><? echo number_format($bal_pipeline_qty, 2); ?></td>
													</tr>
													<?
													$i++;

													$count_tot_qnty += $stock_qty;
													$tot_stock_qty += $stock_qty;
													$tot_pipeline_qty += $bal_pipeline_qty;
													if ($stock_value_usd > 0) {
														$tot_stock_value_usd += $stock_value_usd;
													}
												}
											} else {
												if (number_format($stock_qty, 2) <= '0.00') {
													$pipeline_qty = $pipe_line_arr[$count][$type][$compo][$color];
													$bal_pipeline_qty = $pipeline_qty - $stock_qty;

													$rcv_qnty = $tot_rcv_qnty_arr[$count][$type][$compo][$color];
													$rcv_amt = $tot_rcv_amt_arr[$count][$type][$compo][$color];

													$avg_rate = $rcv_amt / $rcv_qnty;
													$avg_rate = (is_nan($avg_rate) == 1 || is_infinite($avg_rate)) ? 0 : $avg_rate;

													$stock_value = $stock_qty * $avg_rate;

													$avg_rate_usd = $avg_rate / $exchange_rate;
													$avg_rate_usd = (is_nan($avg_rate_usd) == 1 || is_infinite($avg_rate_usd)) ? 0 : $avg_rate_usd;

													$stock_value_usd = $stock_value / $exchange_rate;
													$stock_value_usd = (is_nan($stock_value_usd) == 1 || is_infinite($stock_value_usd)) ? 0 : $stock_value_usd;


													//echo $pipeline_qty.'=='.$stock_qty;
													$ex_comp = explode('**', $compo);
													$comp_1type = $ex_comp[0];
													$comp_1per = $ex_comp[1];
													$comp_2type = $ex_comp[2];
													$comp_2per = $ex_comp[3];
													$fullCompositionValue = "";
													if ($comp_2type != 0) {
														$fullCompositionValue = $composition[$comp_1type] . ',' . $comp_1per . '%  ' . $composition[$comp_2type] . ',' . $comp_2per . '%';
													} else {
														$fullCompositionValue = $composition[$comp_1type] . ',' . $comp_1per . '% ';
													}

													if ($i % 2 == 0)
														$bgcolor = "#E9F3FF";
													else
														$bgcolor = "#FFFFFF";

													?>
													<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
														<td width="50"><? echo $i; ?></td>
														<td width="100" class="wrd_brk">
															<p><? echo $yarn_count_arr[$count]; ?>&nbsp;</p>
														</td>
														<td width="200" class="wrd_brk">
															<p><? echo $fullCompositionValue; ?>&nbsp;</p>
														</td>
														<td width="100" class="wrd_brk">
															<p><? echo $yarn_type[$type]; ?>&nbsp;</p>
														</td>
														<td width="100" class="wrd_brk">
															<p><? echo $colorArr[$color]; ?>&nbsp;</p>
														</td>
														<td width="100" align="right" class="wrd_brk"> <? echo number_format($stock_qty, 2); ?></td>
														<td width="100" align="right" class="wrd_brk" title="( Value/Stock In Hand )"><? echo number_format($avg_rate_usd, 4); ?></td>
														<td width="100" align="right" class="wrd_brk"><? echo number_format($stock_value_usd, 2); ?></td>
														<td align="right" class="wrd_brk"><? echo number_format($bal_pipeline_qty, 2); ?></td>
													</tr>
							<?
													$i++;

													$count_tot_qnty += $stock_qty;
													$tot_stock_qty += $stock_qty;
													$tot_pipeline_qty += $bal_pipeline_qty;
													if ($stock_value_usd > 0) {
														$tot_stock_value_usd += $stock_value_usd;
													}
												}
											}
										}
									}
								}
							}
							?>
						</table>
					</div>
					<table width="1020" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
						<tr class="tbl_bottom">
							<td width="50">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="200">Total</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100" align="right" class="wrd_brk"><? echo number_format($tot_stock_qty, 2); ?></td>
							<td width="100" align="right">&nbsp;</td>
							<td width="100" align="right" class="wrd_brk"><? echo number_format($tot_stock_value_usd, 2); ?></td>
							<td align="right" style="padding-right:18px" class="wrd_brk"><? echo number_format($tot_pipeline_qty, 2); ?></td>
						</tr>
					</table>
				</div>
			<?
		} else if ($type == 17) {
			if ($show_val_column == 1) {
				$value_width = 400;
				$span = 3;
				$column = '<th rowspan="2" width="90">Avg. Rate (Tk)</th><th rowspan="2" width="110">Stock Value</th><th rowspan="2" width="100">Avg. Rate (USD)</th><th rowspan="2" width="100">Stock Value (USD)</th>';
			} else {
				$value_width = 0;
				$span = 0;
				$column = '';
			}

			if ($store_wise == 1) {
				if ($store_name == 0)
					$store_cond .= "";
				else
					$store_cond .= " and a.store_id = $store_name";
				$table_width = '3440' + $value_width;
				//$table_width = '4340' + $value_width;
				$colspan = '36' + $span;
				$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
				$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where company_id=$cbo_company_name and status_active=1 and is_deleted=0 group by floor_room_rack_id, floor_room_rack_name order by floor_room_rack_name", "floor_room_rack_id", "floor_room_rack_name");
			} else {
				$table_width = '3040' + $value_width;
				$colspan = '32' + $span;
			}
			?>
				<style type="text/css">
					.wrap_break {
						word-wrap: break-word;
						word-break: break-all;
					}
				</style>
				<?

				$con = connect();
				execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = " . $user_id . " and ref_from in(1) and  ENTRY_FORM=39");
				oci_commit($con);
				disconnect($con);


				//============================================


				$r_wise_receive_array = array();

				$sql_receive = "SELECT a.prod_id,a.receive_basis,d.pay_mode as ydw_pay_mode,e.pay_mode as purchase_order_pay_mode,a.store_id, max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone,
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
			sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_outside_return_amt,0 as rcv_adjustment_qty, 0 as rcv_adjustment_amt, a.floor_id, a.room, a.rack, a.self, a.bin_box
			from inv_transaction a left join wo_non_order_info_mst e on a.pi_wo_batch_no=e.id, inv_receive_master c left join wo_yarn_dyeing_mst d on c.booking_id=d.id and c.receive_purpose=2 where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $store_cond group by a.store_id,a.prod_id,a.receive_basis,d.pay_mode,e.pay_mode,a.floor_id,a.room,a.rack, a.self, a.bin_box

			union all  

			SELECT a.prod_id,a.receive_basis,0 as ydw_pay_mode,0 as purchase_order_pay_mode,a.store_id,max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone, 0 as rcv_total_opening,0 as rcv_total_opening_amt, 0 as rcv_total_opening_rate, 0 as purchase, 0 as purchase_amt,0 as rcv_loan, 0 as rcv_loan_amt,0 as rcv_inside_return,0 as rcv_inside_return_amt,0 as rcv_outside_return,0 as rcv_outside_return_amt,sum(case when a.transaction_type in (1,4) and a.receive_basis=30 and a.transaction_date<= '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_adjustment_qty,sum(case when a.transaction_type in (1,4) and a.receive_basis=30 and a.transaction_date<= '" . $to_date . "' then a.cons_amount else 0 end) as rcv_adjustment_amt, a.floor_id, a.room, a.rack, a.self, a.bin_box from inv_transaction a where a.receive_basis=30 and a.item_category=1 and a.transaction_type in (1,4) and a.status_active=1 and a.is_deleted=0 $company_cond $store_cond group by a.store_id,a.prod_id,a.receive_basis, a.floor_id, a.room,a.rack, a.self, a.bin_box";

				//echo $sql_receive;

				$result_sql_receive = sql_select($sql_receive);
				$storeArr = array();
				foreach ($result_sql_receive as $row) {
					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_total_opening_rate'] = $row[csf("rcv_total_opening_rate")];
					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_total_opening'] += $row[csf("rcv_total_opening")];;
					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_total_opening_amt'] += $row[csf("rcv_total_opening_amt")];
					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['purchase'] += $row[csf("purchase")];
					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['purchase_amt'] += $row[csf("purchase_amt")];
					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_loan'] += $row[csf("rcv_loan")];
					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_loan_amt'] += $row[csf("rcv_loan_amt")];
					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_inside_return'] += $row[csf("rcv_inside_return")];
					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_inside_return_amt'] += $row[csf("rcv_inside_return_amt")];
					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_outside_return'] += $row[csf("rcv_outside_return")];
					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_outside_return_amt'] += $row[csf("rcv_outside_return_amt")];
					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_adjustment_qty'] += $row[csf("rcv_adjustment_qty")];
					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_adjustment_amt'] += $row[csf("rcv_adjustment_amt")];

					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['pay_mode'] = 0;
					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['last_receive_date'][$row[csf("transaction_date")]] = $row[csf("transaction_date")];
					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['receive_basis'] = $row[csf("receive_basis")];
					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['remarks'] = $row[csf("remarks")];
					$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['source'] = $row[csf("source")];

					if ($row[csf("weight_per_bag")] != "" && $row[csf("weight_per_bag")] > 0) {
						$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['weight_per_bag'][$row[csf("weight_per_bag")]] = $row[csf("weight_per_bag")];
					}

					if ($row[csf("weight_per_cone")] != "" && $row[csf("weight_per_cone")] > 0) {
						$r_wise_receive_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['weight_per_cone'][$row[csf("weight_per_cone")]] = $row[csf("weight_per_cone")];
					}

					if ($row[csf("buyer_id")] > 0) {
						$rcv_trans_buyer_arr[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]][$row[csf("trans_id")]] = $row[csf("buyer_id")];
					}
				}

				unset($result_sql_receive);

				$r_wise_issue_array = array();
				$sql_issue = "select a.prod_id, a.store_id as store_id,
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
			sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_loan_amt,
			0 as issue_adjustment_qty,0 as issue_adjustment_amt, a.floor_id, a.room,a.rack, a.self, a.bin_box
			from inv_transaction a, inv_issue_master c
			where a.mst_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond group by a.store_id,a.prod_id, a.floor_id, a.room,a.rack, a.self, a.bin_box

			union all 

			select a.prod_id, a.store_id,
			0 as issue_total_opening,
			0 as issue_total_opening_rate,
			0 as issue_total_opening_amt,
			0 as issue_inside_amt,
			0 as issue_inside,
			0 as issue_inside_amt,
			0 as issue_outside,
			0 as issue_outside_amt,
			0 as rcv_return,
			0 as rcv_return_amt,
			0 as issue_loan,
			0 as issue_loan_amt,
			sum(case when a.transaction_type in (2,3) and a.receive_basis=30 and a.transaction_date<='" . $to_date . "' then a.cons_quantity else 0 end) as issue_adjustment_qty,sum(case when a.transaction_type in (2,3) and a.receive_basis=30 and a.transaction_date<='" . $to_date . "' then a.cons_amount else 0 end) as issue_adjustment_amt, a.floor_id, a.room,a.rack, a.self, a.bin_box
			from inv_transaction a
			where a.item_category=1 and a.status_active=1 and a.is_deleted=0 $store_cond group by a.store_id,a.prod_id, a.floor_id, a.room,a.rack, a.self, a.bin_box";
				//echo $sql_issue;
				$result_sql_issue = sql_select($sql_issue);
				foreach ($result_sql_issue as $row) {
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['store_id'][$row[csf("store_id")]] = $row[csf("store_id")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['floor_id'][$row[csf("floor_id")]] = $row[csf("floor_id")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['room'][$row[csf("room")]] = $row[csf("room")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rack'][$row[csf("rack")]] = $row[csf("rack")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['self'][$row[csf("self")]] = $row[csf("self")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['bin_box'][$row[csf("bin_box")]] = $row[csf("bin_box")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_total_opening'] += $row[csf("issue_total_opening")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_total_opening_amt'] += $row[csf("issue_total_opening_amt")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_total_opening_rate'] = $row[csf("issue_total_opening_rate")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_inside'] += $row[csf("issue_inside")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_inside_amt'] += $row[csf("issue_inside_amt")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_outside'] += $row[csf("issue_outside")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_outside_amt'] += $row[csf("issue_outside_amt")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_return'] += $row[csf("rcv_return")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_return_amt'] += $row[csf("rcv_return_amt")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_loan'] += $row[csf("issue_loan")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_loan_amt'] += $row[csf("issue_loan_amt")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_adjustment_qty'] += $row[csf("issue_adjustment_qty")];
					$r_wise_issue_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_adjustment_amt'] += $row[csf("issue_adjustment_amt")];
				}

				unset($result_sql_issue);
				if ($store_wise == 1) {
					$trans_criteria_cond = "";
				} else {
					$trans_criteria_cond = " and c.transfer_criteria=1";
				}
				$r_wise_transfer_qty_array = array();
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
				foreach ($result_sql_transfer as $transRow) {
					$r_wise_transfer_qty_array[$transRow[csf("prod_id")]][$transRow[csf("store_id")]][$transRow[csf("floor_id")]][$transRow[csf("room")]][$transRow[csf("rack")]][$transRow[csf("self")]][$transRow[csf("bin_box")]]['transfer_out_qty'] = $transRow[csf("transfer_out_qty")];
					$r_wise_transfer_qty_array[$transRow[csf("prod_id")]][$transRow[csf("store_id")]][$transRow[csf("floor_id")]][$transRow[csf("room")]][$transRow[csf("rack")]][$transRow[csf("self")]][$transRow[csf("bin_box")]]['transfer_out_amt'] = $transRow[csf("transfer_out_amt")];
					$r_wise_transfer_qty_array[$transRow[csf("prod_id")]][$transRow[csf("store_id")]][$transRow[csf("floor_id")]][$transRow[csf("room")]][$transRow[csf("rack")]][$transRow[csf("self")]][$transRow[csf("bin_box")]]['transfer_in_qty'] = $transRow[csf("transfer_in_qty")];
					$r_wise_transfer_qty_array[$transRow[csf("prod_id")]][$transRow[csf("store_id")]][$transRow[csf("floor_id")]][$transRow[csf("room")]][$transRow[csf("rack")]][$transRow[csf("self")]][$transRow[csf("bin_box")]]['transfer_in_amt'] = $transRow[csf("transfer_in_amt")];
					$r_wise_transfer_qty_array[$transRow[csf("prod_id")]][$transRow[csf("store_id")]][$transRow[csf("floor_id")]][$transRow[csf("room")]][$transRow[csf("rack")]][$transRow[csf("self")]][$transRow[csf("bin_box")]]['trans_out_total_opening'] = $transRow[csf("trans_out_total_opening")];
					$r_wise_transfer_qty_array[$transRow[csf("prod_id")]][$transRow[csf("store_id")]][$transRow[csf("floor_id")]][$transRow[csf("room")]][$transRow[csf("rack")]][$transRow[csf("self")]][$transRow[csf("bin_box")]]['trans_out_total_opening_amt'] = $transRow[csf("trans_out_total_opening_amt")];
					$r_wise_transfer_qty_array[$transRow[csf("prod_id")]][$transRow[csf("store_id")]][$transRow[csf("floor_id")]][$transRow[csf("room")]][$transRow[csf("rack")]][$transRow[csf("self")]][$transRow[csf("bin_box")]]['trans_in_total_opening'] = $transRow[csf("trans_in_total_opening")];
					$r_wise_transfer_qty_array[$transRow[csf("prod_id")]][$transRow[csf("store_id")]][$transRow[csf("floor_id")]][$transRow[csf("room")]][$transRow[csf("rack")]][$transRow[csf("self")]][$transRow[csf("bin_box")]]['trans_in_total_opening_amt'] = $transRow[csf("trans_in_total_opening_amt")];
					$r_wise_transfer_qty_array[$transRow[csf("prod_id")]][$transRow[csf("store_id")]][$transRow[csf("floor_id")]][$transRow[csf("room")]][$transRow[csf("rack")]][$transRow[csf("self")]][$transRow[csf("bin_box")]]['trans_in_total_opening_rate'] = $transRow[csf("trans_in_total_opening_rate")];
					$r_wise_transfer_qty_array[$transRow[csf("prod_id")]][$transRow[csf("store_id")]][$transRow[csf("floor_id")]][$transRow[csf("room")]][$transRow[csf("rack")]][$transRow[csf("self")]][$transRow[csf("bin_box")]]['trans_out_total_opening_rate'] = $transRow[csf("trans_out_total_opening_rate")];
				}

				unset($result_sql_transfer);



				$sql = "SELECT a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group,a.brand,b.store_id,b.floor_id,b.room,b.rack,b.self,b.bin_box
			from product_details_master a, inv_transaction b
			where a.id=b.prod_id and a.item_category_id=1 and a.status_active=1  and a.is_deleted=0 and b.item_category=1  $company_cond $search_cond and b.transaction_type=1  group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group,a.brand,b.store_id,b.floor_id,b.room,b.rack,b.self,b.bin_box order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id"; //$prodIds_cond//and a.id=73242
				//echo $sql;//die;
				$result = sql_select($sql);
				$productIdChk = array();
				$all_prod_id_arr = array();
				$main_data_arr = array();
				foreach ($result as $row) {
					if ($productIdChk[$row[csf('id')]] == "") {
						$productIdChk[$row[csf('id')]] = $row[csf('id')];
						$all_prod_id_arr[$row[csf("id")]] = $row[csf("id")];
					}
				}

				//echo "<pre>";print_r($main_data_arr);die;

				$all_prod_id_arr = array_filter($all_prod_id_arr);

				if (!empty($all_prod_id_arr)) {
					fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 39, 1, $all_prod_id_arr, $empty_arr);
					//die;


					if ($store_name != 0)  $store_cond = " and a.store_id in($store_name) ";
					if ($cbo_company_name != 0)  $company_cond = " and a.company_id=$cbo_company_name ";
					$mrr_rate_sql = sql_select("SELECT a.prod_id, MIN(case when a.BALANCE_QNTY>0 then a.transaction_date else null end) as min_date, max(a.transaction_date) as max_date, sum(a.cons_quantity) as cons_quantiy, sum(a.cons_amount) as cons_amount 
				from inv_transaction a, gbl_temp_engine b
				where a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type in(1,4,5) and a.prod_id=b.ref_val  and b.user_id = " . $user_id . " and b.ref_from=1 and b.entry_form=39 $company_cond $store_cond group by a.prod_id");
					$mrr_rate_arr = array();

					foreach ($mrr_rate_sql as $row) {
						$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
						$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
						$mrr_rate_arr[$row[csf("prod_id")]] = $row[csf("cons_amount")] / $row[csf("cons_quantiy")];
						$total_rcv_qty[$row[csf("prod_id")]] = $row[csf("cons_quantiy")];
					}
					unset($mrr_rate_sql);
				}
				//echo $sql;die;
				$con = connect();
				execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = " . $user_id . " and ref_from in(1) and entry_form=39");
				oci_commit($con);
				disconnect($con);

				?>
				<div>
					<table width="<? echo $table_width + 80; ?>" border="1" style="font:'Arial Narrow';" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
						<tr class="form_caption" style="border:none;">
							<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:16px; font-weight:bold">Daily Yarn Stock </td>
						</tr>
						<tr style="border:none;">
							<td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
							</td>
						</tr>
					</table>
					<table width="<? echo $table_width + 80; ?>" border="1" style="font:'Arial Narrow';" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
						<thead>
							<tr>
								<th rowspan="2" width="30">SL</th>
								<th rowspan="2" width="130">Company</th>
								<th colspan="8">Description</th>
								<th rowspan="2" width="100">Wgt. Bag/Cone</th>
								<th rowspan="2" width="100">Source</th>
								<th rowspan="2" width="110">OpeningStock</th>
								<th colspan="6">Receive</th>
								<th colspan="6">Delivery</th>
								<th rowspan="2" width="100">Stock InHand</th>
								<?
								if ($store_wise == 1) {
									echo '<th colspan="6" width="600">Stock Details</th>';
								}

								if ($store_wise != 1) {
									echo '<th rowspan="2" width="100">Allocated to Order </th>';
									echo '<th rowspan="2" width="100">Un Allocated Qty.</th>';
								}

								echo $column;
								?>
								<th rowspan="2" width="50">Age (Days)</th>
								<th rowspan="2" width="50">DOH</th>
								<th rowspan="2" width="140">Yarn Quality<br>Comments</th>
								<th rowspan="2">Remarks</th>
							</tr>
							<tr>
								<th width="60">Prod.ID</th>
								<th width="60">Count</th>
								<th width="150">Composition</th>
								<th width="100">Yarn Type</th>
								<th width="120">Color</th>
								<th width="100">Lot</th>
								<th width="100">Supplier</th>
								<th width="100">Brand</th>
								<th width="90">Purchase</th>
								<th width="90">Inside Return</th>
								<th width="90">Outside Return</th>
								<th width="90">Transfer In</th>
								<th width="90">Loan</th>
								<th width="100">Total Recv</th>
								<th width="90">Inside</th>
								<th width="90">Outside</th>
								<th width="90">Recv. Return</th>
								<th width="90">Trans. Out</th>
								<th width="90">Loan</th>
								<th width="100">Total Delivery</th>
								<?
								if ($store_wise == 1) {
									echo '<th width="100">Store Name</th>';
									echo '<th width="100">Floor</th>';
									echo '<th width="100">Room</th>';
									echo '<th width="100">Rack</th>';
									echo '<th width="100">Self</th>';
									echo '<th width="100">Bin/Box</th>';
								}
								?>
							</tr>
						</thead>
					</table>
					<div style="width:<? echo $table_width + 98; ?>px; overflow-y:scroll; max-height:350px" id="scroll_body">
						<table style="width:<? echo $table_width + 80; ?>px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
							<?

							$i = 1;
							$stockInHand = 0;

							foreach ($result as $row) {
								//echo "<pre>";print_r($row);
								$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
								$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));


								$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row["yarn_comp_percent1st"] . "%\n";
								if ($row["yarn_comp_type2nd"] != 0)
									$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row["yarn_comp_percent2nd"] . "%";

								//===========================================

								if ($prodIdChk[$row[csf('id')]] == "") {
									$prodIdChk[$row[csf('id')]] = $row[csf('id')];



									$mp_wise_transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
									$mp_wise_transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];



									$mp_wise_trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
									$mp_wise_trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];


									$mp_wise_openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $mp_wise_trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $mp_wise_trans_out_total_opening);

									$mp_wise_totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $receive_array[$row[csf("id")]]['rcv_adjustment_qty'] + $mp_wise_transfer_in_qty;

									$mp_wise_totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $issue_array[$row[csf("id")]]['issue_adjustment_qty'] + $mp_wise_transfer_out_qty;

									//echo $openingBalance;

									$mp_wise_stockInHand = $mp_wise_openingBalance + $mp_wise_totalRcv - $mp_wise_totalIssue;
								}



								//===================================

								$transfer_in_qty = $r_wise_transfer_qty_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['transfer_in_qty'];

								$transfer_out_qty = $r_wise_transfer_qty_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['transfer_out_qty'];

								$transfer_in_amt = $r_wise_transfer_qty_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['transfer_in_amt'];
								$transfer_out_amt = $r_wise_transfer_qty_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['transfer_out_amt'];


								$trans_out_total_opening = $r_wise_transfer_qty_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['trans_out_total_opening'];
								$trans_in_total_opening = $r_wise_transfer_qty_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['trans_in_total_opening'];
								$trans_in_total_opening_amt = $r_wise_transfer_qty_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['trans_in_total_opening_amt'];
								$trans_out_total_opening_amt += $r_wise_transfer_qty_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['trans_out_total_opening_amt'];

								$pay_mode = $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['pay_mode'];
								$receive_basis = $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['receive_basis'];

								$openingBalance = ($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_total_opening'] + $trans_in_total_opening) - ($r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_total_opening'] + $trans_out_total_opening);



								$openingBalanceAmt = ($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) - ($r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt);



								$totalRcv = $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['purchase'] + $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_inside_return'] + $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_outside_return'] + $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_loan'] + $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_adjustment_qty'] + $transfer_in_qty;

								$totalIssue = $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_inside'] + $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_outside'] + $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_return'] + $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_loan'] + $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_adjustment_qty'] + $transfer_out_qty;

								$totalRcvAmt = $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['purchase_amt'] + $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_outside_return_amt'] + $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_loan_amt'] + $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_adjustment_amt'] + $transfer_in_amt;

								$totalIssueAmt = $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_inside_amt'] + $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_outside_amt'] + $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_return_amt'] + $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_loan_amt'] + $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_adjustment_amt'] + $transfer_out_qty;

								//echo $totalIssue;
								//echo $openingBalance;
								$stockInHand = $openingBalance + $totalRcv - $totalIssue;

								$stockInHandAmt = $openingBalanceAmt + $totalRcvAmt - $totalIssueAmt;



								//subtotal and group-----------------------
								$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];

								if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $mp_wise_stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $mp_wise_stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $mp_wise_stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $mp_wise_stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $mp_wise_stockInHand == $txt_qnty) || $get_upto_qnty == 0)) {
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";

									//for supplier
									if ($row[csf('is_within_group')] == 1) {
										$supplier_name = $companyArr[$row[csf('supplier_id')]];
									} else {
										$supplier_name = $supplierArr[$row[csf('supplier_id')]];
									}
									//end for supplier

									if ($value_with == 1) {
										if (number_format($mp_wise_stockInHand, 2) > 0.00 && number_format($stockInHand, 2) > 0.00) {
											if (!in_array($check_string, $checkArr)) {
												$checkArr[$i] = $check_string;
												if ($i > 1) {
							?>
													<tr bgcolor="#CCCCCC" style="font-weight:bold">
														<td colspan="12" align="right">Sub Total</td>
														<td width="110" align="right" class="wrap_break"><? echo number_format($total_opening_balance, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_purchase, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_inside_return, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_outside_return, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_in_qty, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_rcv_loan, 2); ?></td>
														<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_rcv, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_inside, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_outside, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_receive_return, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_out_qty, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_loan, 2); ?></td>
														<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_delivery, 2); ?></td>
														<td width="100" align="right" class="wrap_break"><? echo number_format($total_stock_in_hand, 4); ?></td>
														<?
														if ($store_wise == 1) {
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
														} else {
															if ($allocated_qty_variable_settings == 1) {
																echo '<td width="100"  class="wrap_break" align="right">' . number_format($total_alocatted, 2) . '</td>';
																echo '<td width="100" class="wrap_break" align="right">' . number_format($total_free_stock, 2) . '</td>';
															} else {
																echo '<td width="100" align="right">&nbsp;</td>';
																echo '<td width="100" align="right">&nbsp;</td>';
															}
														}
														if ($show_val_column == 1) {
															echo '<td width="90" align="right" >&nbsp;</td>';
															echo '<td width="110" align="right" class="wrap_break">' . number_format($sub_stock_value, 2) . '</td>';
															echo '<td width="100" align="right">&nbsp;</td>';
															echo '<td width="100" align="right" class="wrap_break">' . number_format($sub_stock_value_usd, 2) . '</td>';
														}


														?>
														<td width="50" align="right">&nbsp;</td>
														<td width="50" align="right">&nbsp;</td>
														<td align="right">&nbsp;</td>
														<td width="" align="right">&nbsp;</td>
													</tr>
											<?
													$total_opening_balance = 0;
													$total_purchase = 0;
													$total_inside_return = 0;
													$total_outside_return = 0;
													$total_rcv_loan = 0;
													$total_total_rcv = 0;
													$total_issue_inside = 0;
													$total_issue_outside = 0;
													$total_receive_return = 0;
													$total_issue_loan = 0;
													$total_total_delivery = 0;
													$total_stock_in_hand = 0;
													$total_alocatted = 0;
													$total_free_stock = 0;
													$sub_stock_value = 0;
													$sub_stock_value_usd = 0;
													$total_transfer_out_qty = 0;
													$total_transfer_in_qty = 0;
												}
											}
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td width="30"><? echo $i; ?></td>
												<td width="130" class="wrap_break">
													<p><? echo $companyArr[$row[csf("company_id")]]; ?></p>
												</td>
												<td width="60"><? echo $row[csf("id")]; ?></td>
												<td width="60" class="wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
												<td width="150" class="wrap_break"><? echo $compositionDetails; ?></td>
												<td width="100" class="wrap_break"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
												<td width="120" class="wrap_break"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
												<td width="100">
													<p>
														<?
														if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
															<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?></a>
														<? } else if ($yarnTestArr[$row[csf("id")]] != "") { ?>
															<a href='##' onclick="show_test_report2('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?></a>
														<? } else {
															echo $row[csf("lot")];
														} ?>
													</p>
												</td>
												<td width="100" class="wrap_break">
													<?
													echo $supplier_name;
													?>
												</td>
												<td width="100" class="wrap_break"><? echo $brandArr[$row[csf("brand")]]; ?></td>
												<td width="100" class="wrap_break"><? echo 'Bg:' . max($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['weight_per_bag']) . '; ' . 'Cn:' . max($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['weight_per_cone']); ?></td>
												<td width="100" class="wrap_break"><? echo $source[$r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['source']]; ?></td>
												<td width="110" class="wrap_break" align="right"><? echo number_format($openingBalance, 2); ?></td>
												<td width="90" class="wrap_break" align="right"><? echo number_format($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['purchase'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_inside_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_outside_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break">
													<?
													echo number_format($transfer_in_qty, 2);
													// if ($store_wise == 1) {
													// 	echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
													// } else {
													// 	echo number_format($transfer_in_qty, 2);
													// }
													?>
												</td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_loan'], 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($totalRcv, 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_inside'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_outside'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break">
													<?
													echo number_format($transfer_out_qty, 2);
													// if ($store_wise == 1) {
													// 	echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
													// } else {
													// 	echo number_format($transfer_out_qty, 2);
													// }
													?>
												</td>
												<td width="90" class="wrap_break" align="right"><? echo number_format($r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_loan'], 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($totalIssue, 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($stockInHand, 4); ?></td>

												<?
												if ($store_wise == 1) {
													echo '<td width="100" class="wrap_break" >' . $store_arr[$row[csf("store_id")]] . '</td>';
													echo '<td width="100" class="wrap_break">' . $floor_room_rack_arr[$row[csf("floor_id")]] . '</td>';
													echo '<td width="100" class="wrap_break">' . $floor_room_rack_arr[$row[csf("room")]] . '</td>';
													echo '<td width="100" class="wrap_break">' . $floor_room_rack_arr[$row[csf("rack")]] . '</td>';
													echo '<td width="100" class="wrap_break">' . $floor_room_rack_arr[$row[csf("self")]] . '</td>';
													echo '<td width="100" class="wrap_break">' . $floor_room_rack_arr[$row[csf("bin_box")]] . '</td>';
												} else {
													if ($allocated_qty_variable_settings == 1) {
														$allocated_qnty = 0;
														$available_qnty = 0;
														if ($stockInHand > 0) {
															$allocated_qnty = $row[csf("allocated_qnty")];
															$available_qnty = $row[csf("available_qnty")];
															$total_alocatted += $row[csf("allocated_qnty")];
															$total_free_stock += $row[csf("available_qnty")];
															$grand_total_alocatted += $row[csf("allocated_qnty")];
															$grand_total_free_stock += $row[csf("available_qnty")];
														}
														echo "<td width='100' align='right' class='wrap_break'><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($allocated_qnty, 2) . "</a></td>";
														echo '<td width="100" align="right" class="wrap_break">' . number_format($available_qnty, 2) . '</td>';
													} else {
														echo '<td width="100" align="right">&nbsp;</td>';
														echo '<td width="100" align="right">&nbsp;</td>';
													}
												}
												$stock_value = 0;
												if ($show_val_column == 1) {
													$avg_rate = ($stockInHandAmt / $stockInHand);
													if ($avg_rate > 0) {
														$avg_rate = $avg_rate;
													} else {
														$avg_rate = "0.00";
													}

													$stock_value = $stockInHand * $avg_rate;
													//Previous
													// $stock_value_usd = ($stockInHand * $avg_rate) / $exchange_rate;

													$stock_value_usd = 0;
													if (number_format($stock_value, 2) > 0 && number_format($txt_excange_rate, 2) > 0) {
														$stock_value_usd = $stock_value / $txt_excange_rate;
													} else {
														$stock_value_usd = "0.00";
													}

													$avz_rates_usd = 0;
													//Previous
													// if(number_format($stock_value_usd,2)>0 && number_format($stockInHand,2)>0){
													// 	$avz_rates_usd=$stock_value_usd/$stockInHand;
													// }else{
													// 	$avz_rates_usd="0.00";
													// }

													if (number_format($avg_rate, 2) > 0 && number_format($txt_excange_rate, 2) > 0) {
														$avz_rates_usd = $avg_rate / $txt_excange_rate;
													} else {
														$avz_rates_usd = "0.00";
													}

													echo '<td width="90" align="right" class="wrap_break">' . number_format($avg_rate, 2) . '</td>';
													echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
													echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
													echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
												}


												?>
												<td width="50" align="right" class="wrap_break">
													<?
													if ($stockInHand > 0)
														echo $ageOfDays;
													else
														echo "&nbsp;";
													//echo $ageOfDays; //$ageOfDays;         
													?>
												</td>
												<td width="50" align="right" class="wrap_break">
													<? if ($daysOnHand >= 180) { ?>
														<p style="background-color: red;" title="180 days or above">
															<?
															if ($stockInHand > 0)
																echo $daysOnHand;
															else
																echo "&nbsp;"; //$daysOnHand;
															?></p>
													<? } else { ?>
														<p>
															<?
															if ($stockInHand > 0)
																echo $daysOnHand;
															else
																echo "&nbsp;"; //$daysOnHand;
															?></p>
													<? } ?>
												</td>

												<?
												if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") {
												?>
													<td width="140" align="left"><span class="comment more" style="word-wrap: break-word;word-break: break-all;"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
												<?  } else { ?>
													<td width="140" align="left"><span class="wrap_break"><? echo "&nbsp;"; ?></span></td>
												<? 	} ?>


												<td width="" align="center" class="wrap_break">
													<?
													/*$buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
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
											echo $buyer_all;*/
													?>
													<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a>
												</td>
											</tr>
											<?
											$i++;

											$total_opening_balance += $openingBalance;
											$total_purchase += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['purchase'];
											$total_inside_return += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_inside_return'];
											$total_outside_return += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_outside_return'];
											$total_rcv_loan += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_loan'];
											$total_total_rcv += $totalRcv;
											$total_issue_inside += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_inside'];
											$total_issue_outside += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_outside'];
											$total_receive_return += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_return'];
											$total_issue_loan += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_loan'];
											$total_total_delivery += $totalIssue;
											$total_stock_in_hand += $stockInHand;

											$sub_stock_value += $stock_value;
											$sub_stock_value_usd += $stock_value_usd;
											$total_transfer_out_qty += $transfer_out_qty;
											$total_transfer_in_qty += $transfer_in_qty;

											//grand total===========================
											$grand_total_opening_balance += $openingBalance;
											$grand_total_purchase += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['purchase'];
											$grand_total_inside_return += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_inside_return'];
											$grand_total_outside_return += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_outside_return'];
											$grand_total_rcv_loan += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_loan'];
											$grand_total_total_rcv += $totalRcv;
											$grand_total_issue_inside += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_inside'];
											$grand_total_issue_outside += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_outside'];
											$grand_total_receive_return += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_return'];
											$grand_total_issue_loan += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_loan'];
											$grand_total_total_delivery += $totalIssue;
											$grand_total_stock_in_hand += $stockInHand;

											$tot_stock_value += $stock_value;
											$tot_stock_value_usd += $stock_value_usd;

											$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['transfer_out_qty'];
											$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['transfer_in_qty'];
										}
									} else {
										if (number_format($mp_wise_stockInHand, 2) <= '0.00' && number_format($stockInHand, 2) <= '0.00') {
											// if( $receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['purchase'] != 0 || $receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_inside_return'] != 0 || $receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_outside_return'] != 0 || $receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_loan'] != 0 || $issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_inside'] != 0 || $issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_outside'] != 0 || $issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_return']!= 0 || $issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_loan']!= 0)
											// {

											if (!in_array($check_string, $checkArr)) {
												$checkArr[$i] = $check_string;
												if ($i > 1) {
											?>
													<tr bgcolor="#CCCCCC" style="font-weight:bold">
														<td colspan="12" align="right">Sub Total</td>
														<td width="110" class="wrap_break" align="right" class="wrap_break"><? echo number_format($total_opening_balance, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_purchase, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_inside_return, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_outside_return, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_in_qty, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_rcv_loan, 2); ?></td>
														<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_rcv, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_inside, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_outside, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_receive_return, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_out_qty, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_loan, 2); ?></td>
														<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_delivery, 2); ?></td>
														<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
														<?

														if ($store_wise == 1) {
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
														} else {
															if ($allocated_qty_variable_settings == 1) {
																echo '<td width="100"  class="wrap_break" align="right">' . number_format($total_alocatted, 2) . '</td>';
																echo '<td width="100" class="wrap_break" align="right">' . number_format($total_free_stock, 2) . '</td>';
															} else {
																echo '<td width="100" align="right">&nbsp;</td>';
																echo '<td width="100" align="right">&nbsp;</td>';
															}
														}
														if ($show_val_column == 1) {
															echo '<td width="90" align="right" >&nbsp;</td>';
															echo '<td width="110" align="right" class="wrap_break">' . number_format($sub_stock_value, 2) . '</td>';
															echo '<td width="100" align="right">&nbsp;</td>';
															echo '<td width="100" align="right" class="wrap_break">' . number_format($sub_stock_value_usd, 2) . '</td>';
														}
														?>
														<td width="50" align="right">&nbsp;</td>
														<td width="50" align="right">&nbsp;</td>
														<td width="" align="right">&nbsp;</td>
														<td width="" align="right">&nbsp;</td>
													</tr>
											<?
													$total_opening_balance = 0;
													$total_purchase = 0;
													$total_inside_return = 0;
													$total_outside_return = 0;
													$total_rcv_loan = 0;
													$total_total_rcv = 0;
													$total_issue_inside = 0;
													$total_issue_outside = 0;
													$total_receive_return = 0;
													$total_issue_loan = 0;
													$total_total_delivery = 0;
													$total_stock_in_hand = 0;
													$total_alocatted = 0;
													$total_free_stock = 0;
													$sub_stock_value = 0;
													$sub_stock_value_usd = 0;
													$total_transfer_out_qty = 0;
													$total_transfer_in_qty = 0;
												}
											}
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td width="30"><? echo $i; ?></td>
												<td width="130" class="wrap_break">
													<p><? echo $companyArr[$row[csf("company_id")]]; ?></p>
												</td>
												<td width="60" class="wrap_break"><? echo $row[csf("id")]; ?></td>
												<td width="60" class="wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
												<td width="150" class="wrap_break"><? echo $compositionDetails; ?></td>
												<td width="100" class="wrap_break"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
												<td width="120" class="wrap_break"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
												<td width="100" class="wrap_break" style="mso-number-format:'\@';">
													<p>
														<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
															<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
														<? } else {
															echo $row[csf("lot")];
														}
														?>
														&nbsp;
													</p>
												</td>

												<td width="100" class="wrap_break">
													<?
													echo $supplier_name;
													?>
												</td>
												<td width="100" class="wrap_break"><? echo $brandArr[$row[csf("brand")]]; ?></td>
												<td width="100" class="wrap_break"><? echo 'Bg:' . max($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['weight_per_bag']) . '; ' . 'Cn:' . max($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['weight_per_cone']); ?></td>
												<td width="100" class="wrap_break"><? echo $source[$r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['source']]; ?></td>
												<td width="110" class="wrap_break" align="right"><? echo number_format($openingBalance, 2); ?></td>
												<td width="90" class="wrap_break" align="right"><? echo number_format($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['purchase'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_inside_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_outside_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_in_qty, 2);
													}
													?>
												</td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_loan'], 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($totalRcv, 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_inside'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_outside'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_out_qty, 2);
													}
													?>
												</td>
												<td width="90" class="wrap_break" align="right"><? echo number_format($r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_loan'], 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($totalIssue, 2); ?></td>

												<td width="100" align="right" class="wrap_break"><? echo number_format($stockInHand, 4); ?></td>


												<?

												if ($store_wise == 1) {
													echo '<td width="100" class="wrap_break" >' . $store_arr[$row[csf("store_id")]] . '</td>';
													echo '<td width="100" class="wrap_break">' . $floor_room_rack_arr[$row[csf("floor_id")]] . '</td>';
													echo '<td width="100" class="wrap_break">' . $floor_room_rack_arr[$row[csf("room")]] . '</td>';
													echo '<td width="100" class="wrap_break">' . $floor_room_rack_arr[$row[csf("rack")]] . '</td>';
													echo '<td width="100" class="wrap_break">' . $floor_room_rack_arr[$row[csf("self")]] . '</td>';
													echo '<td width="100" class="wrap_break">' . $floor_room_rack_arr[$row[csf("bin_box")]] . '</td>';
												} else {
													if ($allocated_qty_variable_settings == 1) {
														echo "<td width='100' align='right' class='wrap_break'><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
														echo '<td width="100" align="right" class="wrap_break">' . number_format($row[csf("available_qnty")], 2) . '</td>';
													} else {
														echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
														echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
													}
												}
												$stock_value = 0;
												if ($show_val_column == 1) {

													if ($stockInHand > 0) {
														$avg_rate = ($stockInHandAmt / $stockInHand);
													}

													if ($avg_rate > 0) {
														$avg_rate = $avg_rate;
													} else {
														$avg_rate = "0.00";
													}

													$stock_value = $stockInHand * $avg_rate;
													//PREVIOUS
													//$stock_value_usd = ($stockInHand * $avg_rate) / $exchange_rate;
													$stock_value_usd = 0;
													if (number_format($stock_value, 2) > 0 && number_format($txt_excange_rate, 2) > 0) {
														$stock_value_usd = $stock_value / $txt_excange_rate;
													} else {
														$stock_value_usd = "0.00";
													}

													$avz_rates_usd = 0;
													//PREVIOUS
													// if(number_format($stock_value_usd,2)>0 && number_format($stockInHand,2)>0) {
													// 	$avz_rates_usd=$stock_value_usd/$stockInHand;
													// }else{
													// 	$avz_rates_usd = "0.00";
													// }

													if (number_format($avg_rate, 2) > 0 && number_format($txt_excange_rate, 2) > 0) {
														$avz_rates_usd = $avg_rate / $txt_excange_rate;
													} else {
														$avz_rates_usd = "0.00";
													}


													echo '<td width="90" align="right" class="wrap_break">' . number_format($avg_rate, 2) . '</td>';
													echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
													echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
													echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
												}
												?>
												<td width="50" align="right" class="wrap_break"><? echo $ageOfDays; //$ageOfDays;         
																								?></td>
												<td width="50" align="right" class="wrap_break"><?
																								if ($stockInHand > 0)
																									echo $daysOnHand;
																								else
																									echo "&nbsp;"; //$daysOnHand;
																								?></td>

												<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
													<td width="140" align="left"><span class="comment more" style="word-wrap: break-word;word-break: break-all;"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
												<?  } else { ?>
													<td width="140" align="left"><span><? echo "&nbsp;"; ?></span></td>
												<? 	} ?>

												<td width="" align="center" class="wrap_break">

													<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a>
												</td>
											</tr>
											<?
											$i++;

											$total_opening_balance += $openingBalance;
											$total_purchase += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['purchase'];
											$total_inside_return += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_inside_return'];
											$total_outside_return += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_outside_return'];
											$total_rcv_loan += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_loan'];
											$total_total_rcv += $totalRcv;
											$total_issue_inside += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_inside'];
											$total_issue_outside += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_outside'];
											$total_receive_return += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_return'];
											$total_issue_loan += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_loan'];
											$total_total_delivery += $totalIssue;
											$total_stock_in_hand += $stockInHand;
											$total_alocatted += $row[csf("allocated_qnty")];
											$total_free_stock += $row[csf("available_qnty")];
											$sub_stock_value += $stock_value;
											$sub_stock_value_usd += $stock_value_usd;
											$total_transfer_out_qty += $transfer_out_qty;
											$total_transfer_in_qty += $transfer_in_qty;

											//grand total===========================
											$grand_total_opening_balance += $openingBalance;
											$grand_total_purchase += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['purchase'];
											$grand_total_inside_return += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_inside_return'];
											$grand_total_outside_return += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_outside_return'];
											$grand_total_rcv_loan += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_loan'];
											$grand_total_total_rcv += $totalRcv;
											$grand_total_issue_inside += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_inside'];
											$grand_total_issue_outside += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_outside'];
											$grand_total_receive_return += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_return'];
											$grand_total_issue_loan += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_loan'];
											$grand_total_total_delivery += $totalIssue;
											$grand_total_stock_in_hand += $stockInHand;
											$grand_total_alocatted += $row[csf("allocated_qnty")];
											$grand_total_free_stock += $row[csf("available_qnty")];
											$tot_stock_value += $stock_value;
											$tot_stock_value_usd += $stock_value_usd;

											$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['transfer_out_qty'];
											$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['transfer_in_qty'];
											//}
										} else {
											if (!in_array($check_string, $checkArr)) {
												$checkArr[$i] = $check_string;
												if ($i > 1) {
											?>
													<tr bgcolor="#CCCCCC" style="font-weight:bold">
														<td colspan="12" align="right">Sub Total</td>
														<td width="110" class="wrap_break" align="right" class="wrap_break"><? echo number_format($total_opening_balance, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_purchase, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_inside_return, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_outside_return, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_in_qty, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_rcv_loan, 2); ?></td>
														<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_rcv, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_inside, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_outside, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_receive_return, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_out_qty, 2); ?></td>
														<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_loan, 2); ?></td>
														<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_delivery, 2); ?></td>
														<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
														<?

														if ($store_wise == 1) {
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
															echo '<td width="100">&nbsp;</td>';
														} else {
															if ($allocated_qty_variable_settings == 1) {
																echo '<td width="100"  class="wrap_break" align="right">' . number_format($total_alocatted, 2) . '</td>';
																echo '<td width="100" class="wrap_break" align="right">' . number_format($total_free_stock, 2) . '</td>';
															} else {
																echo '<td width="100" align="right">&nbsp;</td>';
																echo '<td width="100" align="right">&nbsp;</td>';
															}
														}
														if ($show_val_column == 1) {
															echo '<td width="90" align="right" >&nbsp;</td>';
															echo '<td width="110" align="right" class="wrap_break">' . number_format($sub_stock_value, 2) . '</td>';
															echo '<td width="100" align="right">&nbsp;</td>';
															echo '<td width="100" align="right" class="wrap_break">' . number_format($sub_stock_value_usd, 2) . '</td>';
														}
														?>
														<td width="50" align="right">&nbsp;</td>
														<td width="50" align="right">&nbsp;</td>
														<td width="" align="right">&nbsp;</td>
														<td width="" align="right">&nbsp;</td>
													</tr>
											<?
													$total_opening_balance = 0;
													$total_purchase = 0;
													$total_inside_return = 0;
													$total_outside_return = 0;
													$total_rcv_loan = 0;
													$total_total_rcv = 0;
													$total_issue_inside = 0;
													$total_issue_outside = 0;
													$total_receive_return = 0;
													$total_issue_loan = 0;
													$total_total_delivery = 0;
													$total_stock_in_hand = 0;
													$total_alocatted = 0;
													$total_free_stock = 0;
													$sub_stock_value = 0;
													$sub_stock_value_usd = 0;
													$total_transfer_out_qty = 0;
													$total_transfer_in_qty = 0;
												}
											}
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td width="30"><? echo $i; ?></td>
												<td width="130" class="wrap_break">
													<p><? echo $companyArr[$row[csf("company_id")]]; ?></p>
												</td>
												<td width="60" class="wrap_break"><? echo $row[csf("id")]; ?></td>
												<td width="60" class="wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
												<td width="150" class="wrap_break"><? echo $compositionDetails; ?></td>
												<td width="100" class="wrap_break"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
												<td width="120" class="wrap_break"><? echo $color_name_arr[$row[csf("color")]]; ?></td>

												<td width="100" class="wrap_break" style="mso-number-format:'\@';">
													<p>
														<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
															<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')] ?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
														<? } else {
															echo $row[csf("lot")];
														}
														?>
														&nbsp;
													</p>
												</td>

												<td width="100" class="wrap_break">
													<?
													echo $supplier_name;
													?>
												</td>
												<td width="100" class="wrap_break"><? echo $brandArr[$row[csf("brand")]]; ?></td>
												<td width="100" class="wrap_break"><? echo 'Bg:' . max($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['weight_per_bag']) . '; ' . 'Cn:' . max($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['weight_per_cone']); ?></td>
												<td width="100" class="wrap_break"><? echo $source[$r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['source']]; ?></td>
												<td width="110" class="wrap_break" align="right"><? echo number_format($openingBalance, 2); ?></td>
												<td width="90" class="wrap_break" align="right"><? echo number_format($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['purchase'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_inside_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_outside_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_in_qty, 2);
													}
													?>
												</td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_loan'], 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($totalRcv, 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_inside'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_outside'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_out_qty, 2);
													}
													?>
												</td>
												<td width="90" class="wrap_break" align="right"><? echo number_format($r_wise_issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($totalIssue, 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($stockInHand, 4); ?></td>
												<?

												if ($store_wise == 1) {
													echo '<td width="100" class="wrap_break" >' . $store_arr[$row[csf("store_id")]] . '</td>';
													echo '<td width="100" class="wrap_break">' . $floor_room_rack_arr[$row[csf("floor_id")]] . '</td>';
													echo '<td width="100" class="wrap_break">' . $floor_room_rack_arr[$row[csf("room")]] . '</td>';
													echo '<td width="100" class="wrap_break">' . $floor_room_rack_arr[$row[csf("rack")]] . '</td>';
													echo '<td width="100" class="wrap_break">' . $floor_room_rack_arr[$row[csf("self")]] . '</td>';
													echo '<td width="100" class="wrap_break">' . $floor_room_rack_arr[$row[csf("bin_box")]] . '</td>';
												} else {
													if ($allocated_qty_variable_settings == 1) {
														echo "<td width='100' align='right' class='wrap_break'><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
														echo '<td width="100" align="right" class="wrap_break">' . number_format($row[csf("available_qnty")], 2) . '</td>';
													} else {
														echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
														echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
													}
												}

												$stock_value = 0;
												if ($show_val_column == 1) {

													$avg_rate = ($stockInHandAmt / $stockInHand);
													if ($avg_rate > 0) {
														$avg_rate = $avg_rate;
													} else {
														$avg_rate = "0.00";
													}

													$stock_value = $stockInHand * $avg_rate;
													$stock_value_usd = ($stockInHand * $avg_rate) / $exchange_rate;
													$avz_rates_usd = 0;

													if (number_format($stock_value_usd, 2) > 0 && number_format($stockInHand, 2) > 0) {
														$avz_rates_usd = $stock_value_usd / $stockInHand;
													} else {
														$avz_rates_usd = "0.00";
													}

													echo '<td width="90" align="right" class="wrap_break">' . number_format($avg_rate, 2) . '</td>';
													echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
													echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
													echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
												}

												?>
												<td width="50" align="right" class="wrap_break"><? echo $ageOfDays; //$ageOfDays;         
																								?></td>
												<td width="50" align="right" class="wrap_break"><?
																								if ($stockInHand > 0)
																									echo $daysOnHand;
																								else
																									echo "&nbsp;"; //$daysOnHand;
																								?></td>

												<? if ($yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
													<td width="140" align="left"><span class="comment more" style="word-wrap: break-word;word-break: break-all;"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
												<?  } else { ?>
													<td width="140" align="left"><span><? echo "&nbsp;"; ?></span></td>
												<? 	} ?>

												<td width="" align="center" class="wrap_break">

													<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a>
												</td>
											</tr>
							<?
											$i++;

											$total_opening_balance += $openingBalance;
											$total_purchase += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['purchase'];
											$total_inside_return += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_inside_return'];
											$total_outside_return += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_outside_return'];
											$total_rcv_loan += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_loan'];
											$total_total_rcv += $totalRcv;
											$total_issue_inside += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_inside'];
											$total_issue_outside += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_outside'];
											$total_receive_return += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_return'];
											$total_issue_loan += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_loan'];
											$total_total_delivery += $totalIssue;
											$total_stock_in_hand += $stockInHand;
											$total_alocatted += $row[csf("allocated_qnty")];
											$total_free_stock += $row[csf("available_qnty")];
											$sub_stock_value += $stock_value;
											$sub_stock_value_usd += $stock_value_usd;
											$total_transfer_out_qty += $transfer_out_qty;
											$total_transfer_in_qty += $transfer_in_qty;

											//grand total===========================
											$grand_total_opening_balance += $openingBalance;
											$grand_total_purchase += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['purchase'];
											$grand_total_inside_return += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_inside_return'];
											$grand_total_outside_return += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_outside_return'];
											$grand_total_rcv_loan += $r_wise_receive_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_loan'];
											$grand_total_total_rcv += $totalRcv;
											$grand_total_issue_inside += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_inside'];
											$grand_total_issue_outside += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_outside'];
											$grand_total_receive_return += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['rcv_return'];
											$grand_total_issue_loan += $r_wise_issue_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['issue_loan'];
											$grand_total_total_delivery += $totalIssue;
											$grand_total_stock_in_hand += $stockInHand;
											$grand_total_alocatted += $row[csf("allocated_qnty")];
											$grand_total_free_stock += $row[csf("available_qnty")];
											$tot_stock_value += $stock_value;
											$tot_stock_value_usd += $stock_value_usd;

											$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['transfer_out_qty'];
											$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]][$row[csf("store_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]['transfer_in_qty'];
										}
									} // end else
								}
							}
							?>
							<tr bgcolor="#CCCCCC" style="font-weight:bold">
								<td colspan="12" align="right">Sub Total</td>
								<td width="110" class="wrap_break" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_purchase, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_inside_return, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_outside_return, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_in_qty, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_rcv_loan, 2); ?></td>
								<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_rcv, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_inside, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_outside, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_receive_return, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_transfer_out_qty, 2); ?></td>
								<td width="90" align="right" class="wrap_break"><? echo number_format($total_issue_loan, 2); ?></td>
								<td width="100" align="right" class="wrap_break"><? echo number_format($total_total_delivery, 2); ?></td>
								<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
								<?


								if ($store_wise == 1) {
									echo '<td width="100">&nbsp;</td>';
									echo '<td width="100">&nbsp;</td>';
									echo '<td width="100">&nbsp;</td>';
									echo '<td width="100">&nbsp;</td>';
									echo '<td width="100">&nbsp;</td>';
									echo '<td width="100">&nbsp;</td>';
								} else {
									if ($allocated_qty_variable_settings == 1) {
										echo '<td width="100" align="right">' . number_format($total_alocatted, 2) . '</td>';
										echo '<td width="100" align="right">' . number_format($total_free_stock, 2) . '</td>';
									} else {
										echo '<td width="100" align="right">&nbsp;</td>';
										echo '<td width="100" align="right">&nbsp;</td>';
									}
								}
								if ($show_val_column == 1) {
									echo '<td width="90" align="right">&nbsp;</td>';
									echo '<td width="110" class="wrap_break" align="right">' . number_format($sub_stock_value, 2) . '</td>';
									echo '<td width="100" align="right">&nbsp;</td>';
									echo '<td width="100" class="wrap_break" align="right">' . number_format($sub_stock_value_usd, 2) . '</td>';
								}
								?>
								<td width="50" align="right">&nbsp;</td>
								<td width="50" align="right">&nbsp;</td>
								<td width="" align="right">&nbsp;</td>
								<td width="" align="right">&nbsp;</td>
							</tr>
						</table>
					</div>
					<table style="width:<? echo $table_width + 80; ?>px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
						<tr class="tbl_bottom">
							<td width="30"></td>
							<td width="130"></td>
							<td width="60"></td>
							<td width="60"></td>
							<td width="150"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="120"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100" align="right">Grand Total</td>
							<td width="110" class="wrap_break" align="right" id="value_total_opening_balance"><? echo number_format($grand_total_opening_balance, 2); ?></td>
							<td width="90" align="right" id="value_total_purchase" class="wrap_break"><? echo number_format($grand_total_purchase, 2); ?></td>
							<td width="90" align="right" id="value_total_inside_return" class="wrap_break"><? echo number_format($grand_total_inside_return, 2); ?></td>
							<td width="90" align="right" id="value_total_outside_return" class="wrap_break"><? echo number_format($grand_total_outside_return, 2); ?></td>
							<td width="90" align="right" id="value_total_transfer_in" class="wrap_break"><? echo number_format($grand_total_transfer_in_qty, 2); ?></td>
							<td width="90" class="wrap_break" align="right" id="value_total_rcv_loan"><? echo number_format($grand_total_rcv_loan, 2); ?></td>
							<td width="100" class="wrap_break" align="right" id="value_total_total_rcv"><? echo number_format($grand_total_total_rcv, 2); ?></td>
							<td width="90" class="wrap_break" align="right" id="value_total_issue_inside"><? echo number_format($grand_total_issue_inside, 2); ?></td>
							<td width="90" class="wrap_break" align="right" id="value_total_issue_outside"><? echo number_format($grand_total_issue_outside, 2); ?></td>
							<td width="90" class="wrap_break" align="right" id="value_total_receive_return"><? echo number_format($grand_total_receive_return, 2); ?></td>
							<td width="90" align="right" class="wrap_break" id="value_total_transfer_out"><? echo number_format($grand_total_transfer_out_qty, 2); ?></td>
							<td width="90" align="right" class="wrap_break" id="value_total_issue_loan"><? echo number_format($grand_total_issue_loan, 2); ?></td>
							<td width="100" align="right" class="wrap_break" id="value_total_total_delivery"><? echo number_format($grand_total_total_delivery, 2); ?></td>
							<td width="100" align="right" class="wrap_break" id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 4); ?></td>
							<?


							if ($store_wise == 1) {
								echo '<td width="100">&nbsp;</td>';
								echo '<td width="100">&nbsp;</td>';
								echo '<td width="100">&nbsp;</td>';
								echo '<td width="100">&nbsp;</td>';
								echo '<td width="100">&nbsp;</td>';
								echo '<td width="100">&nbsp;</td>';
							} else {
								if ($allocated_qty_variable_settings == 1) {
									echo '<td width="100" align="right" id="value_total_alocatted">' . number_format($grand_total_alocatted, 2) . '</td>';
									echo '<td width="100" align="right" id="value_total_free_stock">' . number_format($grand_total_free_stock, 2) . '</td>';
								} else {
									echo '<td width="100" align="right" id="value_total_alocatted">&nbsp;</td>';
									echo '<td width="100" align="right" id="value_total_free_stock">&nbsp;</td>';
								}
							}
							if ($show_val_column == 1) {
								echo '<td width="90" align="right">&nbsp;</td>';
								echo '<td width="110" class="wrap_break" align="right">' . number_format($tot_stock_value, 2) . '</td>';
								echo '<td width="100" align="right">&nbsp;</td>';
								echo '<td width="100" class="wrap_break" align="right">' . number_format($tot_stock_value_usd, 2) . '</td>';
							}
							?>
							<td width="50" align="right">&nbsp;</td>
							<td width="50" align="right">&nbsp;</td>
							<td width="140" align="right">&nbsp;</td>
							<td width="" align="right">&nbsp;</td>
						</tr>
					</table>
				</div>
			<?
		} else if ($type == 18) {
			if ($store_name != 0)  $store_cond = " and store_id in($store_name) ";
			if ($cbo_company_name != 0)  $company_cond = " and company_id=$cbo_company_name ";

			$date_array = array();
			$days_sql = "select prod_id, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 $company_cond $store_cond group by prod_id";
			$days_sql_result = sql_select($days_sql);
			foreach ($days_sql_result as $row) {
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
			}
			unset($days_sql_result);
			//## bellow query based on rcv transaction balance quantity in transaction table
			$age_sql = "select prod_id, min(transaction_date) as min_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 and transaction_type in(1,4,5) and BALANCE_QNTY>0 $company_cond $store_cond group by prod_id";
			// echo $age_sql; die;

			$age_sql_result = sql_select($age_sql);
			foreach ($age_sql_result as $row) {
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
			}
			unset($age_sql_result);


			$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group
			from product_details_master a
			where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";

			// echo $sql;

			$result = sql_select($sql);
			$i = 1;
			//ob_start();
			?>


				<!-- old  -->
				<?
				$tot_stock_value = 0;

				foreach ($result as $row) {
					$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
					$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));

					$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
					if ($row[csf("yarn_comp_type2nd")] != 0)
						$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
					$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
					$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

					$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
					$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];
					$trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];
					$trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];

					$pay_mode = $receive_array[$row[csf("id")]]['pay_mode'];
					$receive_basis = $receive_array[$row[csf("id")]]['receive_basis'];

					$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);

					$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;

					$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;

					$totalRcvValue = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];

					$totalIssueValue = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] + $issue_array[$row[csf("id")]]['issue_loan_amt'] + $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];

					$openingBalanceAmt = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt);

					$totalRcvAmt = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];

					$tot_rcv_amt = $openingBalanceAmt + $totalRcvAmt;

					$tot_rcv_qnty = $openingBalance + $totalRcv;

					$avg_rate = $tot_rcv_amt / $tot_rcv_qnty;

					$openingAmount = 0;
					$openingRate = 0;
					if (($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) > 0) {
						$openingRate = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) / ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening);
					}

					$openingAmount = $openingBalance * $openingRate;

					$stockInHand = $openingBalance + $totalRcv - $totalIssue;

					//echo $value_with."<br>";
					//subtotal and group-----------------------							
					$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];

					//for supplier
					if ($row[csf('is_within_group')] == 1) {
						$supplier_name = $companyArr[$row[csf('supplier_id')]];
					} else {
						$supplier_name = $supplierArr[$row[csf('supplier_id')]];
					}
					//end for supplier

					if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0)) {

						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						//echo $value_with.testdd;die;
						if ($value_with == 1) {
							if (number_format($stockInHand, 2, '.', '') > 0.00) {
								if (!in_array($check_string, $checkArr)) {
									$checkArr[$i] = $check_string;
									if ($i > 1) {

										$total_opening_balance = 0;
										$total_openingBalanceAmt = 0;
										$total_purchase = 0;
										$total_inside_return = 0;
										$total_outside_return = 0;
										$total_rcv_loan = 0;
										$total_total_rcv = 0;
										$total_totalRcvValue = 0;
										$total_issue_inside = 0;
										$total_issue_outside = 0;
										$total_receive_return = 0;
										$total_issue_loan = 0;
										$total_total_delivery = 0;
										$total_totalIssueValue = 0;
										$total_stock_in_hand = 0;
										$total_alocatted = 0;
										$total_free_stock = 0;
										$sub_stock_value = 0;
										$sub_stock_value_usd = 0;
										$total_transfer_out_qty = 0;
										$total_transfer_in_qty = 0;
									}
								}

								$stock_value = 0;
								if ($show_val_column == 1) {
									/* $avg_rate=$totalRcvValue/$totalRcv;*/
									$avg_rate = (is_nan($avg_rate) == 1 || is_infinite($avg_rate)) ? 0 : $avg_rate;
									$stock_value = floatval($stockInHand) * (float)$avg_rate;
									//var_dump($stock_value);
									$stock_value = (is_nan($stock_value) == 1 || is_infinite($stock_value)) ? 0 : $stock_value;

									$avg_rate_usd = $avg_rate / $exchange_rate;
									$avg_rate_usd = (is_nan($avg_rate_usd) == 1 || is_infinite($avg_rate_usd)) ? 0 : $avg_rate_usd;

									$stock_value_usd = $stock_value / $exchange_rate;
									$stock_value_usd = (is_nan($stock_value_usd) == 1 || is_infinite($stock_value_usd)) ? 0 : $stock_value_usd;
								}



								$i++;

								$total_opening_balance += $openingBalance;
								$total_openingBalanceAmt += $openingBalanceAmt;
								$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
								$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
								$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
								$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
								$total_total_rcv += $totalRcv;
								$total_totalRcvValue += $totalRcvValue;
								$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
								$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
								$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
								$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
								$total_total_delivery += $totalIssue;
								$total_totalIssueValue += $totalIssueValue;
								$total_stock_in_hand += $stockInHand;
								$total_alocatted += $row[csf("allocated_qnty")];
								$total_free_stock += $row[csf("available_qnty")];
								$sub_stock_value += $stock_value;
								$sub_stock_value_usd += $stock_value_usd;
								$total_transfer_out_qty += $transfer_out_qty;
								$total_transfer_in_qty += $transfer_in_qty;

								//grand total===========================
								$grand_total_opening_balance += $openingBalance;
								$grand_total_opening_amount_value += $openingAmount;
								$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
								$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
								$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
								$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
								$grand_total_total_rcv += $totalRcv;
								$grand_total_rcv_amount_value += $totalRcvValue; //$totalIssueValue
								$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
								$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
								$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
								$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
								$grand_total_total_delivery += $totalIssue;
								$grand_total_issue_amount_value += $totalIssueValue;
								$grand_total_stock_in_hand += $stockInHand;
								$grand_total_alocatted += $row[csf("allocated_qnty")];
								$grand_total_free_stock += $row[csf("available_qnty")];
								$tot_stock_value += $stock_value;
								$tot_stock_value_usd += $stock_value_usd;

								$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
								$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
							}
						} else {
							if (number_format($stockInHand, 2, '.', '') <= 0.00) {
								if (number_format($openingBalance, 2, '.', '') != 0 || number_format($receive_array[$row[csf("id")]]['purchase'], 2, '.', '') != 0 || number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2, '.', '') != 0 || number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2, '.', '') != 0 || number_format($transfer_in_qty, 2, '.', '') != 0 || number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2, '.', '') != 0 || number_format($issue_array[$row[csf("id")]]['issue_inside'], 2, '.', '') != 0 || number_format($issue_array[$row[csf("id")]]['issue_outside'], 2, '.', '') != 0 || number_format($issue_array[$row[csf("id")]]['rcv_return'], 2, '.', '') != 0 || number_format($transfer_out_qty, 2, '.', '') != 0 || number_format($issue_array[$row[csf("id")]]['issue_loan'], 2, '.', '') != 0) {
									if (!in_array($check_string, $checkArr)) {
										$checkArr[$i] = $check_string;
										if ($i > 1) {

											$total_opening_balance = 0;
											$total_openingBalanceAmt = 0;
											$total_purchase = 0;
											$total_inside_return = 0;
											$total_outside_return = 0;
											$total_rcv_loan = 0;
											$total_total_rcv = 0;
											$total_totalRcvValue = 0;
											$total_issue_inside = 0;
											$total_issue_outside = 0;
											$total_receive_return = 0;
											$total_issue_loan = 0;
											$total_total_delivery = 0;
											$total_totalIssueValue = 0;
											$total_stock_in_hand = 0;
											$total_alocatted = 0;
											$total_free_stock = 0;
											$sub_stock_value = 0;
											$sub_stock_value_usd = 0;
											$total_transfer_out_qty = 0;
											$total_transfer_in_qty = 0;
										}
									}

									$stock_value = 0;
									if ($show_val_column == 1) {
										/* $avg_rate=$totalRcvValue/$totalRcv;*/
										$avg_rate = (is_nan($avg_rate) == 1 || is_infinite($avg_rate)) ? 0 : $avg_rate;
										$stock_value = floatval($stockInHand) * (float)$avg_rate;
										//var_dump($stock_value);
										$stock_value = (is_nan($stock_value) == 1 || is_infinite($stock_value)) ? 0 : $stock_value;

										$avg_rate_usd = $avg_rate / $exchange_rate;
										$avg_rate_usd = (is_nan($avg_rate_usd) == 1 || is_infinite($avg_rate_usd)) ? 0 : $avg_rate_usd;

										$stock_value_usd = $stock_value / $exchange_rate;
										$stock_value_usd = (is_nan($stock_value_usd) == 1 || is_infinite($stock_value_usd)) ? 0 : $stock_value_usd;
									}


									$i++;

									$total_opening_balance += $openingBalance;
									$total_openingBalanceAmt += $openingBalanceAmt;
									$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
									$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
									$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
									$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
									$total_total_rcv += $totalRcv;
									$total_totalRcvValue += $totalRcvValue;
									$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
									$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
									$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
									$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
									$total_total_delivery += $totalIssue;
									$total_totalIssueValue += $totalIssueValue;
									$total_stock_in_hand += $stockInHand;
									$total_alocatted += $row[csf("allocated_qnty")];
									$total_free_stock += $row[csf("available_qnty")];
									$sub_stock_value += $stock_value;
									$sub_stock_value_usd += $stock_value_usd;
									$total_transfer_out_qty += $transfer_out_qty;
									$total_transfer_in_qty += $transfer_in_qty;

									//grand total===========================
									$grand_total_opening_balance += $openingBalance;
									$grand_total_opening_amount_value += $openingAmount;
									$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
									$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
									$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
									$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
									$grand_total_total_rcv += $totalRcv;
									$grand_total_rcv_amount_value += $totalRcvValue; //$totalIssueValue
									$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
									$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
									$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
									$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
									$grand_total_total_delivery += $totalIssue;
									$grand_total_issue_amount_value += $totalIssueValue;
									$grand_total_stock_in_hand += $stockInHand;
									$grand_total_alocatted += $row[csf("allocated_qnty")];
									$grand_total_free_stock += $row[csf("available_qnty")];
									$tot_stock_value += $stock_value;
									$tot_stock_value_usd += $stock_value_usd;

									$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
									$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
								}
							} elseif (number_format($stockInHand, 2, '.', '') >= 0.00) {
								if (!in_array($check_string, $checkArr)) {
									$checkArr[$i] = $check_string;
									if ($i > 1) {

										$total_opening_balance = 0;
										$total_openingBalanceAmt = 0;
										$total_purchase = 0;
										$total_inside_return = 0;
										$total_outside_return = 0;
										$total_rcv_loan = 0;
										$total_total_rcv = 0;
										$total_totalRcvValue = 0;
										$total_issue_inside = 0;
										$total_issue_outside = 0;
										$total_receive_return = 0;
										$total_issue_loan = 0;
										$total_total_delivery = 0;
										$total_totalIssueValue = 0;
										$total_stock_in_hand = 0;
										$total_alocatted = 0;
										$total_free_stock = 0;
										$sub_stock_value = 0;
										$sub_stock_value_usd = 0;
										$total_transfer_out_qty = 0;
										$total_transfer_in_qty = 0;
									}
								}

								$stock_value = 0;
								if ($show_val_column == 1) {
									/* $avg_rate=$totalRcvValue/$totalRcv;*/
									$avg_rate = (is_nan($avg_rate) == 1 || is_infinite($avg_rate)) ? 0 : $avg_rate;
									$stock_value = floatval($stockInHand) * (float)$avg_rate;
									//var_dump($stock_value);
									$stock_value = (is_nan($stock_value) == 1 || is_infinite($stock_value)) ? 0 : $stock_value;

									$avg_rate_usd = $avg_rate / $exchange_rate;
									$avg_rate_usd = (is_nan($avg_rate_usd) == 1 || is_infinite($avg_rate_usd)) ? 0 : $avg_rate_usd;

									$stock_value_usd = $stock_value / $exchange_rate;
									$stock_value_usd = (is_nan($stock_value_usd) == 1 || is_infinite($stock_value_usd)) ? 0 : $stock_value_usd;
								}

								$i++;

								$total_opening_balance += $openingBalance;
								$total_openingBalanceAmt += $openingBalanceAmt;
								$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
								$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
								$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
								$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
								$total_total_rcv += $totalRcv;
								$total_totalRcvValue += $totalRcvValue;
								$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
								$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
								$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
								$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
								$total_total_delivery += $totalIssue;
								$total_totalIssueValue += $totalIssueValue;
								$total_stock_in_hand += $stockInHand;
								$total_alocatted += $row[csf("allocated_qnty")];
								$total_free_stock += $row[csf("available_qnty")];
								$sub_stock_value += $stock_value;
								$sub_stock_value_usd += $stock_value_usd;
								$total_transfer_out_qty += $transfer_out_qty;
								$total_transfer_in_qty += $transfer_in_qty;

								//grand total===========================
								$grand_total_opening_balance += $openingBalance;
								$grand_total_opening_amount_value += $openingAmount;
								$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
								$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
								$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
								$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
								$grand_total_total_rcv += $totalRcv;
								$grand_total_rcv_amount_value += $totalRcvValue; //$totalIssueValue
								$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
								$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
								$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
								$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
								$grand_total_total_delivery += $totalIssue;
								$grand_total_issue_amount_value += $totalIssueValue;
								$grand_total_stock_in_hand += $stockInHand;
								$grand_total_alocatted += $row[csf("allocated_qnty")];
								$grand_total_free_stock += $row[csf("available_qnty")];
								$tot_stock_value += $stock_value;
								$tot_stock_value_usd += $stock_value_usd;

								$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
								$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
							}
						}
					}
				}
				?>

				<!-- new  -->
				<div style=" margin:auto; width:520px; ">
					<table style=" margin:auto; width:520px">
						<thead>
							<tr class="form_caption" style="border:none; width:60px; font-size: 16pt">
								<td align="center" style="border:none;font-size:18pt; font-weight:bold; width:60px;">Daily Yarn Stock </td>
							</tr>
							<tr style="border:none; width:260px; font-size: 16pt">
								<td align="center" style="border:none; font-size:18pt; width:260px;">
									Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
								</td>
							</tr>
							<tr style="border:none; width:200px; font-size: 16pt">
								<td align="center" style="border:none;font-size:18pt; font-weight:bold; width:200px;">
									<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
								</td>
							</tr>
						</thead>
					</table>
					<table width="<? echo 600; ?>" border="1" style="font:'Arial Narrow';" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
						<thead>
							<tr>
								<th style="font-size: 16pt" align="center" width="60">SL</th>
								<th style="font-size: 16pt" align="center" width="240">Type</th>
								<th style="font-size: 16pt" align="center" width="300">Grey Yarn</th>
							</tr>
						</thead>
					</table>
					<div style="width:<? echo 600 + 20; ?>px; overflow-y:scroll; max-height:350px" id="scroll_body">
						<table width="<? echo 600; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
							<tr>
								<td style="font-size: 16pt" align="center" width="60">1</td>
								<td style="font-size: 16pt" width="240">Opening Stock</td>
								<td style="font-size: 16pt" align="right" width="300"><? echo number_format($grand_total_opening_balance, 2); ?></td>
							</tr>
							<tr>
								<td style="font-size: 16pt" align="center">2</td>
								<td style="font-size: 16pt">Inside Delivery</td>
								<td style="font-size: 16pt" align="right"><? echo number_format($inside_issue_total, 2); ?></td>
							</tr>
							<tr>
								<td style="font-size: 16pt" align="center">3</td>
								<td style="font-size: 16pt">Outside Delivery</td>
								<td style="font-size: 16pt" align="right"><? echo number_format($outside_issue_total, 2); ?></td>
							</tr>
							<tr>
								<td style="font-size: 16pt" align="center">4</td>
								<td style="font-size: 16pt">Yarn Delivery For Yarn Dyeing</td>
								<td style="font-size: 16pt" align="right"><? echo number_format($dyeing_issue_total, 2); ?></td>
							</tr>
							<tr>
								<td style="font-size: 16pt" align="center">5</td>
								<td style="font-size: 16pt">Audit / Adjust Issue</td>
								<td style="font-size: 16pt" align="right"><? echo number_format($adjust_issue_total, 2); ?></td>
							</tr>
							<tr>
								<td style="font-size: 16pt" align="center">6</td>
								<td style="font-size: 16pt">Supplier Return</td>
								<td style="font-size: 16pt" align="right"><? echo number_format($receive_return_total, 2); ?></td>
							</tr>
							<tr>
								<td style="font-size: 16pt" align="center">7</td>
								<td style="font-size: 16pt">In-Side Issue Return</td>
								<td style="font-size: 16pt" align="right"><? echo number_format($inside_issue_return_total, 2); ?></td>
							</tr>
							<tr>
								<td style="font-size: 16pt" align="center">8</td>
								<td style="font-size: 16pt">Out-Side Issue Return</td>
								<td style="font-size: 16pt" align="right"><? echo number_format($outside_issue_return_total, 2); ?></td>
							</tr>
							<tr>
								<td style="font-size: 16pt" align="center">9</td>
								<td style="font-size: 16pt">Supplier PI Receive</td>
								<td style="font-size: 16pt" align="right"><? echo number_format($pi_receive_total, 2); ?></td>
							</tr>
							<tr>
								<td style="font-size: 16pt" align="center">10</td>
								<td style="font-size: 16pt">Independent Receive</td>
								<td style="font-size: 16pt" align="right"><? echo number_format($independent_receive_total, 2); ?></td>
							</tr>
							<?
							$closing_stock = ($grand_total_opening_balance + $inside_issue_return_total + $outside_issue_return_total + $pi_receive_total + $independent_receive_total) - ($inside_issue_total + $outside_issue_total + $dyeing_issue_total + $adjust_issue_total + $receive_return_total);
							// $re = ($grand_total_opening_balance + $inside_issue_return_total + $outside_issue_return_total + $pi_receive_total + $independent_receive_total);
							// $iss = ($inside_issue_total + $outside_issue_total + $dyeing_issue_total + $adjust_issue_total + $receive_return_total);
							?>
							<tr>
								<td style="font-size: 16pt" align="center">11</td>
								<td style="font-size: 16pt">Closing Stock</td>
								<td style="font-size: 16pt" align="right"><? echo number_format($closing_stock, 2) ?></td>
							</tr>
							<?
							// echo bcsub($re, $iss, 2) 
							// $numericPart = preg_replace('/[^0-9.]/', '', number_format($re,2));
							// $numericPart2 = preg_replace('/[^0-9.]/', '', number_format($iss,2));
							// echo "re:".$numericPart."<br>";
							// echo "issue:".$numericPart2."<br>";
							// echo number_format($numericPart-$numericPart2, 2);
							?>
						</table>
					</div>
				</div>
				<br>
		<?
		}
	}
		?>
		<script>
			$(document).ready(function() {
				var showChar = 30;
				//var ellipsestext = "...";
				var ellipsestext = "";
				var moretext = "more";
				var lesstext = "less";
				$('.more').each(function() {
					var content = $(this).html();

					if (content.length > showChar) {

						var c = content.substr(0, showChar);
						var h = content.substr(showChar - 1, content.length - showChar);

						var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

						$(this).html(html);
					}

				});

				$(".morelink").click(function() {
					if ($(this).hasClass("less")) {
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
		foreach (glob("*.xls") as $filename) {
			@unlink($filename);
		}
		//---------end------------//
		$name = time();
		$filename = $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w+');
		$is_created = fwrite($create_new_doc, $html);
		echo "$html**$filename";
		exit();
	}

	if ($action == "yarn_test_report2") {
		extract($_REQUEST);
		echo load_html_head_contents("Yarn Test Report", "../../../../", 1, 1, '', '', '');
		list($company_ID, $prod_ID) = explode("*", $data);

		$yarn_test_for_arr = array(1 => 'Bulk Yarn', 2 => 'Sample Yarn');
		$yarn_test_result_arr = array(1 => 'Nil', 2 => 'Major', 3 => 'Minor');
		$yarn_test_acceptance_arr = array(1 => 'Yes', 2 => 'No');
		$comments_acceptance_arr = array(1 => 'Acceptable', 2 => 'Special', 3 => 'Consideration', 4 => 'Not Acceptable');
		$phys_test_knitting_arr = array(1 => 'Stripe(Patta)', 2 => 'Thick & Thin Yarn', 3 => 'Neps', 4 => 'Poly-Propaline(Plastic Conta)', 5 => 'Color Conta/Yarn', 6 => 'Dead Fiber', 7 => 'No Of Slub', 8 => 'No Of Hole', 9 => 'No Of Slub Hole', 10 => 'Moisture Efect', 11 => 'No Of Yarn Breakage', 12 => 'No Of Setup', 13 => 'Knotting End', 14 => 'Haireness', 15 => 'Hand Feel', 16 => 'Twisting', 17 => 'Contamination', 18 => 'Foregin Fiber', 19 => 'Oil Stain Yarn', 20 => 'Foreign Matters', 21 => 'Unlevel', 22 => 'Double Yarn', 23 => 'Fiber Migration', 24 => 'Excessive Hard Yarn');
		//asort($phys_test_knitting_arr);
		$phys_test_dyeing_and_finishing_arr = array(1 => 'Stripe(Patta)', 2 => 'Thick & Thin Yarn', 3 => 'Neps', 4 => 'Color Conta', 5 => 'Dead Fiber/Cotton', 6 => 'No Of Slub', 7 => 'No Of Hole', 8 => 'No Of Slub Hole', 9 => 'Moisture Efect', 10 => 'Shrinkage', 11 => 'Dye Pick Up%', 12 => 'Enzyme Dosting %', 13 => 'Knotting End', 14 => 'Haireness', 15 => 'Hand Feel', 16 => 'Contamination', 17 => 'Soft Yarn/Loose Yarn', 18 => 'Oil Stain Yarn', 19 => 'Bad Piecing', 20 => 'Oily Slub', 21 => 'Foreign Matters', 22 => 'Black Specks Test', 23 => 'Cotton Seeds Test', 24 => 'Bursting', 25 => 'Pilling', 26 => 'Lustre', 27 => 'Process loss %');
		//asort($phys_test_dyeing_and_finishing_arr);

		$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
		$product_name_details = return_field_value("product_name_details", "product_details_master", "id=$prod_ID");
		$lot_number = return_field_value("lot", "product_details_master", "id=$prod_ID");


		$sql_brand = "select a.id, a.brand_name from lib_brand a, product_details_master b where a.id=b.brand and b.id=$prod_ID";
		//echo $sql_brand; die;
		$sql_brand_name = sql_select($sql_brand);
		$brand_name = $sql_brand_name[0][csf('brand_name')];


		?>

		<style type="text/css">
			/* .a4size {
           width: 21cm;
           height: 26.7cm;
           font-family: Cambria, Georgia, serif;
        } */
			/*   @media print {
               table {border-spacing: 0px; padding: 0px;}
				td th{padding: 0px;}
        size: A4 portrait;
        }*/
			.rpt_table2 td {
				border: 1px solid #8bAF00;
			}
		</style>
		<table style="width: 1300px;" align="center">
			<tr>
				<td align="center" style="font-size:xx-large"><strong><? echo $company_arr[$company_ID]; ?></strong></td>
			</tr>
			<tr>
				<td align="center" style="font-size: 16px;">
					<?
					$nameArray = sql_select("select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$company_ID");
					foreach ($nameArray as $result) {
					?>
						<? echo $result[csf('plot_no')]; ?>
						<? echo $result[csf('level_no')]; ?>
						<? echo $result[csf('road_no')]; ?>
						<? echo $result[csf('block_no')]; ?>
						<? echo $result[csf('city')]; ?>
						<? echo $result[csf('zip_code')]; ?>
						<? echo $result[csf('province')]; ?>
						<? echo $country_arr[$result[csf('country_id')]]; ?><br>
						<? echo $result[csf('email')]; ?>
					<? echo $result[csf('website')];
					}
					?>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size: 25px;">Assessment of Numerical Test &amp; Physical Inspection Report</td>
			</tr>
			<tr>
				<td align="center" style="font-size: 25px;">Yarn Test Report</td>
			</tr>
		</table>
		<br />

		<div style="font-size: 25px; margin-left: 5px;" title='Product ID=<? echo $prod_ID; ?> and Lot Number=<? echo $lot_number; ?>'><strong>Product Details: <? echo $prod_ID . ', ' . $lot_number . ', ' . $brand_name . ', ' . $product_name_details; ?></strong></div><br>
		<div style="margin-left: 5px;">
			<?
			$sql_mst_comments = "select a.id, a.company_id, a.prod_id, a.lot_number, a.test_date, a.test_for, a.specimen_wgt, a.specimen_length, a.color, a.receive_qty, a.lc_number, a.lc_qty, a.actual_yarn_count, a.actual_yarn_count_phy, a.yarn_apperance_grad, a.yarn_apperance_phy, a.actual_yarn_comp, a.actual_yarn_comp_phy, a.pilling, a.pilling_phy, a.brusting, a.brusting_phy, a.twist_per_inc, a.twist_per_inc_phy, a.moisture_content, a.moisture_content_phy, a.ipi_value, a.ipi_value_phy, a.csp_minimum, a.csp_minimum_phy, a.csp_actual, a.csp_actual_phy, a.thin_yarn, a.thin_yarn_phy, a.thick, a.thick_phy, a.u, a.u_phy, a.cv, a.cv_phy, a.neps_per_km, a.neps_per_km_phy, a.heariness, a.heariness_phy, a.counts_cv, a.system_result, a.counts_cv_phy, a.grey_gsm, a.grey_wash_gsm, a.required_gsm, a.required_dia, a.machine_dia, a.stich_length, a.grey_gsm_dye, a.batch, a.finish_gsm, a.finish_dia, a.length, a.width, b.comments_knit_acceptance, b.comments_knit, b.comments_dye_acceptance, b.comments_dye, b.comments_author_acceptance, b.comments_author from inv_yarn_test_mst a, inv_yarn_test_comments b where a.id=b.mst_table_id and a.prod_id=$prod_ID and a.company_id=$company_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id desc";
			$sql_mst_comments_rslt = sql_select($sql_mst_comments);
			$yarn_info_arr = array();

			foreach ($sql_mst_comments_rslt as $value) {
				$attribute = array('id', 'company_id', 'prod_id', 'lot_number', 'test_date', 'test_for', 'specimen_wgt', 'specimen_length', 'color', 'receive_qty', 'lc_number', 'lc_qty', 'actual_yarn_count', 'actual_yarn_count_phy', 'yarn_apperance_grad', 'yarn_apperance_phy', 'actual_yarn_comp', 'actual_yarn_comp_phy', 'pilling', 'pilling_phy', 'brusting', 'brusting_phy', 'twist_per_inc', 'twist_per_inc_phy', 'moisture_content', 'moisture_content_phy', 'ipi_value', 'ipi_value_phy', 'csp_minimum', 'csp_minimum_phy', 'csp_actual', 'csp_actual_phy', 'thin_yarn', 'thin_yarn_phy', 'thick', 'thick_phy', 'u', 'u_phy', 'cv', 'cv_phy', 'neps_per_km', 'neps_per_km_phy', 'heariness', 'heariness_phy', 'counts_cv', 'system_result', 'counts_cv_phy');

				foreach ($attribute as $attr) {
					$yarn_info_arr[$value[csf('id')]][$attr] = $value[csf($attr)];
				}
			}

			$sql_dtls = "select a.id, a.color, a.grey_gsm, a.grey_wash_gsm, a.required_gsm, a.required_dia, a.machine_dia, a.stich_length, a.grey_gsm_dye, a.batch, a.finish_gsm, a.finish_dia, a.length, a.width, b.id as dtls_id, b.testing_parameters_id, b.fab_type, b.testing_parameters, b.fabric_point, b.result, b.acceptance, b.fabric_class, b.remarks from inv_yarn_test_mst a, inv_yarn_test_dtls b where a.id=b.mst_id and a.prod_id=$prod_ID and a.company_id=$company_ID and b.fab_type in(1,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.color, b.testing_parameters_id";
			$sql_dtls_result = sql_select($sql_dtls);
			$color_range_arr = array();
			$knit_mstdata_arr = array();
			$color_knit_dtls_arr = array();
			$color_dye_dtls_arr = array();
			$dtls_data_arr = array();
			foreach ($sql_dtls_result as $value) {
				$color_range_arr[$value[csf('id')]]['color'] = $value[csf('color')];
				$attribute_mst = array('grey_gsm', 'grey_wash_gsm', 'required_gsm', 'required_dia', 'machine_dia', 'stich_length', 'grey_gsm_dye', 'batch', 'finish_gsm', 'finish_dia', 'length', 'width');
				foreach ($attribute_mst as $attr) {
					$knit_mstdata_arr[$value[csf('id')]][$attr] = $value[csf($attr)];
				}

				$color_knit_dtls_arr[$value[csf('fab_type')]][$value[csf('id')]]['dtls_id'] .= $value[csf('dtls_id')] . ',';
				$color_dye_dtls_arr[$value[csf('fab_type')]][$value[csf('id')]]['dtls_id'] .= $value[csf('dtls_id')] . ',';

				//$dtls_data_arr[$value[csf('dtls_id')]]['testing_parameters']=$value[csf('testing_parameters')];
				$dtls_data_arr[$value[csf('dtls_id')]]['testing_parameters_id'] = $value[csf('testing_parameters_id')];
				$dtls_data_arr[$value[csf('dtls_id')]]['fabric_point'] = $value[csf('fabric_point')];
				$dtls_data_arr[$value[csf('dtls_id')]]['result'] = $value[csf('result')];
				$dtls_data_arr[$value[csf('dtls_id')]]['acceptance'] = $value[csf('acceptance')];
				$dtls_data_arr[$value[csf('dtls_id')]]['fabric_class'] = $value[csf('fabric_class')];
				$dtls_data_arr[$value[csf('dtls_id')]]['remarks'] = $value[csf('remarks')];
			}
			?>
			<table cellspacing="1" cellpadding="1" class="rpt_table2" rules="all" style="border: 2px solid black;">
				<caption style="background-color: #dbb768; font-weight: bold; text-align: left; border-top: 2px solid black; border-right: 2px solid black; border-left: 2px solid black;">Basic Yarn Information</caption>
				<tr>
					<td width="50"><b>1</b></td>
					<td width="150"><b>Color Range</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="121" style="border-left: 2px solid black;"><b><? echo $color_range[$value['color']]; ?></b></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>2</b></td>
					<td width="150"><b>Test For</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="121" style="border-left: 2px solid black;"><? echo $yarn_test_for_arr[$value['test_for']]; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>3</b></td>
					<td width="150"><b>Specimen Weight</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="121" style="border-left: 2px solid black;"><? if ($value['specimen_wgt'] == 0) echo '';
																				else echo $value['specimen_wgt']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>4</b></td>
					<td width="150"><b>Specimen Length</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="121" style="border-left: 2px solid black;"><? if ($value['specimen_length'] == 0) echo '';
																				else echo $value['specimen_length']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>5</b></td>
					<td width="150"><b>Receive Quantity</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="121" style="border-left: 2px solid black;"><? if ($value['receive_qty'] == 0) echo '';
																				else echo $value['receive_qty']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>6</b></td>
					<td width="150"><b>LC Quantity</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="121" style="border-left: 2px solid black;"><? if ($value['lc_qty'] == 0) echo '';
																				else echo $value['lc_qty']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>7</b></td>
					<td width="150"><b>LC Number</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="121" style="border-left: 2px solid black;"><? echo $value['lc_number']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>8</b></td>
					<td width="150"><b>Test Date</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="121" style="border-left: 2px solid black;"><? echo $value['test_date']; ?></td>
					<?
					}
					?>
				</tr>
			</table>
			<br>
			<table cellspacing="1" cellpadding="1" class="rpt_table2" rules="all" style="border: 2px solid black;">
				<caption style="background-color: #dbb768; font-weight: bold; text-align: left; border-top: 2px solid black; border-right: 2px solid black; border-left: 2px solid black;">Numerical Test</caption>
				<tr style="background-color: #ddb;">
					<td colspan="2"></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td style="border-left: 2px solid black;"><b>Require</b></td>
						<td><b>Physical</b></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>1</b></td>
					<td width="150"><b>Actual Yarn Count</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="60" style="border-left: 2px solid black;"><? if ($value['actual_yarn_count'] == 0) echo '';
																				else echo $value['actual_yarn_count']; ?></td>
						<td width="60"><? if ($value['actual_yarn_count_phy'] == 0) echo '';
										else echo $value['actual_yarn_count_phy']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>2</b></td>
					<td width="150"><b>Yarn Apperance (Grade)</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="60" style="border-left: 2px solid black;"><? if ($value['yarn_apperance_grad'] == '') echo '';
																				else echo $value['yarn_apperance_grad']; ?></td>
						<td width="60"><? if ($value['yarn_apperance_phy'] == '') echo '';
										else echo $value['yarn_apperance_phy']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>3</b></td>
					<td width="150"><b>Twist Per Inch (TPI)</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="60" style="border-left: 2px solid black;"><? if ($value['twist_per_inc'] == 0) echo '';
																				else echo $value['twist_per_inc']; ?></td>
						<td width="60"><? if ($value['twist_per_inc_phy'] == 0) echo '';
										else echo $value['twist_per_inc_phy']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>4</b></td>
					<td width="150"><b>Moisture Content</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="60" style="border-left: 2px solid black;"><? if ($value['moisture_content'] == 0) echo '';
																				else echo $value['moisture_content']; ?></td>
						<td width="60"><? if ($value['moisture_content_phy'] == 0) echo '';
										else echo $value['moisture_content_phy']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>5</b></td>
					<td width="150"><b>IPI Value (Uster)</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="60" style="border-left: 2px solid black;"><? if ($value['ipi_value'] == 0) echo '';
																				else echo $value['ipi_value']; ?></td>
						<td width="60"><? if ($value['ipi_value_phy'] == 0) echo '';
										else echo $value['ipi_value_phy']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>6</b></td>
					<td width="150"><b>CSP Minimum</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="60" style="border-left: 2px solid black;"><? if ($value['csp_minimum'] == 0) echo '';
																				else echo $value['csp_minimum']; ?></td>
						<td width="60"><? if ($value['csp_minimum_phy'] == 0) echo '';
										else echo $value['csp_minimum_phy']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>7</b></td>
					<td width="150"><b>CSP Actua</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="60" style="border-left: 2px solid black;"><? if ($value['csp_actual'] == 0) echo '';
																				else echo $value['csp_actual']; ?></td>
						<td width="60"><? if ($value['csp_actual_phy'] == 0) echo '';
										else echo $value['csp_actual_phy']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>8</b></td>
					<td width="150"><b>Thin Yarn</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="60" style="border-left: 2px solid black;"><? if ($value['thin_yarn'] == 0) echo '';
																				else echo $value['thin_yarn']; ?></td>
						<td width="60"><? if ($value['thin_yarn_phy'] == 0) echo '';
										else echo $value['thin_yarn_phy']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>9</b></td>
					<td width="150"><b>Thick</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="60" style="border-left: 2px solid black;"><? if ($value['thick'] == 0) echo '';
																				else echo $value['thick']; ?></td>
						<td width="60"><? if ($value['thick_phy'] == 0) echo '';
										else echo $value['thick_phy']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>10</b></td>
					<td width="150"><b>U %</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="60" style="border-left: 2px solid black;"><? if ($value['u'] == 0) echo '';
																				else echo $value['u']; ?></td>
						<td width="60"><? if ($value['u_phy'] == 0) echo '';
										else echo $value['u_phy']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>11</b></td>
					<td width="150"><b>CV %</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="60" style="border-left: 2px solid black;"><? if ($value['cv'] == 0) echo '';
																				else echo $value['cv']; ?></td>
						<td width="60"><? if ($value['cv_phy'] == 0) echo '';
										else echo $value['cv_phy']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>12</b></td>
					<td width="150"><b>Neps Per KM</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="60" style="border-left: 2px solid black;"><? if ($value['neps_per_km'] == 0) echo '';
																				else echo $value['neps_per_km']; ?></td>
						<td width="60"><? if ($value['neps_per_km_phy'] == 0) echo '';
										else echo $value['neps_per_km_phy']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>13</b></td>
					<td width="150"><b>Heariness %</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="60" style="border-left: 2px solid black;"><? if ($value['heariness'] == 0) echo '';
																				else echo $value['heariness']; ?></td>
						<td width="60"><? if ($value['heariness_phy'] == 0) echo '';
										else echo $value['heariness_phy']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>14</b></td>
					<td width="150"><b>Counts CV %</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="60" style="border-left: 2px solid black;"><? if ($value['counts_cv'] == 0) echo '';
																				else echo $value['counts_cv']; ?></td>
						<td width="60"><? if ($value['counts_cv_phy'] == 0) echo '';
										else echo $value['counts_cv_phy']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>15</b></td>
					<td width="150"><b>Actual Yarn Composition</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="60" style="border-left: 2px solid black;"><? if ($value['actual_yarn_comp'] == '') echo '';
																				else echo $value['actual_yarn_comp']; ?></td>
						<td width="60"><? if ($value['actual_yarn_comp_phy'] == '') echo '';
										else echo $value['actual_yarn_comp_phy']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>16</b></td>
					<td width="150"><b>Pilling Test</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="60" style="border-left: 2px solid black;"><? if ($value['pilling'] == '') echo '';
																				else echo $value['pilling']; ?></td>
						<td width="60"><? if ($value['pilling_phy'] == '') echo '';
										else echo $value['pilling_phy']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>17</b></td>
					<td width="150"><b>Brusting Test</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td width="60" style="border-left: 2px solid black;"><? if ($value['brusting'] == '') echo '';
																				else echo $value['brusting']; ?></td>
						<td width="60"><? if ($value['brusting_phy'] == '') echo '';
										else echo $value['brusting_phy']; ?></td>
					<?
					}
					?>
				</tr>
				<tr>
					<td width="50"><b>18</b></td>
					<td width="150"><b>System Result</b></td>
					<?
					foreach ($yarn_info_arr as $value) {
					?>
						<td colspan="2" width="120" style="border-left: 2px solid black;"><? echo $value['system_result']; ?></td>
					<?
					}
					?>
				</tr>
			</table>
		</div>
		<br>
		<div style="margin-left: 5px;">
			<!-- i am here -->
			<table>
				<?
				foreach ($color_range_arr as $mst_id => $color_val) {
					$knit_dtls = array_unique(explode(",", rtrim($color_knit_dtls_arr[1][$mst_id]['dtls_id'], ',')));
					$dye_dtls = array_unique(explode(",", rtrim($color_dye_dtls_arr[2][$mst_id]['dtls_id'], ',')));

					$grey_gsm = $knit_mstdata_arr[$mst_id]['grey_gsm'];
					if ($grey_gsm == 0) $grey_gsm = '';
					$grey_wash_gsm = $knit_mstdata_arr[$mst_id]['grey_wash_gsm'];
					if ($grey_wash_gsm == 0) $grey_wash_gsm = '';
					$required_gsm = $knit_mstdata_arr[$mst_id]['required_gsm'];
					if ($required_gsm == 0) $required_gsm = '';
					$required_dia = $knit_mstdata_arr[$mst_id]['required_dia'];
					if ($required_dia == 0) $required_dia = '';
					$machine_dia = $knit_mstdata_arr[$mst_id]['machine_dia'];
					if ($machine_dia == 0) $machine_dia = '';
					$stich_length = $knit_mstdata_arr[$mst_id]['stich_length'];
					if ($stich_length == 0) $stich_length = '';

					$grey_gsm_dye = $knit_mstdata_arr[$mst_id]['grey_gsm_dye'];
					if ($grey_gsm_dye == 0) $grey_gsm_dye = '';
					$finish_gsm = $knit_mstdata_arr[$mst_id]['finish_gsm'];
					if ($finish_gsm == 0) $finish_gsm = '';
					$finish_dia = $knit_mstdata_arr[$mst_id]['finish_dia'];
					if ($finish_dia == 0) $finish_dia = '';
					$length = $knit_mstdata_arr[$mst_id]['length'];
					if ($length == 0) $length = '';
					$width = $knit_mstdata_arr[$mst_id]['width'];
					if ($width == 0) $width = '';
				?>
					<tr>
						<td style="vertical-align: top;">
							<table cellspacing="1" cellpadding="1" class="rpt_table2" rules="all" border="1" style="width: 660px; margin-right: 20px; border: 2px solid black;">
								<caption style="background-color: #dbb768; font-weight: bold; border-top: 2px solid black; border-left: 2px solid black; border-right: 2px solid black;">Knitting For <? echo $color_range[$color_val['color']]; ?></caption>
								<tr>
									<td width="80"><b>Gray GSM</b></td>
									<td width="80"><? echo $grey_gsm; ?></td>
									<td width="80" rowspan="2"></td>
									<td width="95"><b>Gray Wash GSM</b></td>
									<td width="80"><? echo $grey_wash_gsm; ?></td>
									<td width="80" rowspan="2"></td>
									<td width="80"><b>Required GSM</b></td>
									<td><? echo $required_gsm; ?></td>
								</tr>
								<tr>
									<td width="80"><b>Required Dia</b></td>
									<td width="80"><? echo $required_dia; ?></td>
									<td width="95"><b>Machine Dia</b></td>
									<td width="80"><? echo $machine_dia; ?></td>
									<td width="80"><b>Stich Length</b></td>
									<td><? echo $stich_length; ?></td>
								</tr>
								<tr style="background-color: #ddb;">
									<td width="160" colspan="2"><b>Testing Parameters</b></td>
									<td width="80"><b>Point</b></td>
									<td width="95"><b>Result</b></td>
									<td width="80"><b>Acceptance</b></td>
									<td width="80"><b>Fabric Class</b></td>
									<td colspan="2"><b>Remarks</b></td>
								</tr>
								<?
								foreach ($knit_dtls as $row) {
								?>
									<tr>
										<td width="160" colspan="2"><strong><? echo $phys_test_knitting_arr[$dtls_data_arr[$row]['testing_parameters_id']]; ?></strong></td>
										<td width="80"><? if ($dtls_data_arr[$row]['fabric_point'] == 0) echo '';
														else echo $dtls_data_arr[$row]['fabric_point']; ?></td>
										<td width="95"><? echo $yarn_test_result_arr[$dtls_data_arr[$row]['result']]; ?></td>
										<td width="80"><? echo $yarn_test_acceptance_arr[$dtls_data_arr[$row]['acceptance']]; ?></td>
										<td width="80"><? echo $dtls_data_arr[$row]['fabric_class']; ?></td>
										<td colspan="2"><? echo $dtls_data_arr[$row]['remarks']; ?></td>
									</tr>
								<?
								}
								?>
							</table>
						</td>
						<td style="vertical-align: top;">
							<table cellspacing="1" cellpadding="1" class="rpt_table2" rules="all" border="1" style="width: 650px; margin-right: 20px; border: 2px solid black;">
								<caption style="background-color: #dbb768; font-weight: bold; border-top: 2px solid black; border-left: 2px solid black; border-right: 2px solid black;">Dyeing For <? echo $color_range[$color_val['color']]; ?></caption>
								<tr>
									<td width="80"><b>Gray GSM</b></td>
									<td width="80"><? echo $grey_gsm_dye; ?></td>
									<td width="80" rowspan="2"></td>
									<td width="80"><b>Batch</b></td>
									<td width="80"><? echo $knit_mstdata_arr[$mst_id]['batch']; ?></td>
									<td width="80" rowspan="2"></td>
									<td width="80"><b>Finish GSM</b></td>
									<td><? echo $finish_gsm; ?></td>
								</tr>
								<tr>
									<td width="80"><b>Finish Dia</b></td>
									<td width="80"><? echo $finish_dia; ?></td>
									<td width="80"><b>Length %</b></td>
									<td width="80"><? echo $length; ?></td>
									<td width="80"><b>Width</b></td>
									<td><? echo $width; ?></td>
								</tr>
								<tr style="background-color: #ddb;">
									<td width="160" colspan="2"><b>Testing Parameters</b></td>
									<td width="80"><b>Point</b></td>
									<td width="80"><b>Result</b></td>
									<td width="80"><b>Acceptance</b></td>
									<td width="80"><b>Fabric Class</b></td>
									<td colspan="2"><b>Remarks</b></td>
								</tr>
								<?
								foreach ($dye_dtls as $row) {
								?>
									<tr>
										<td width="160" colspan="2"><b><? echo $phys_test_dyeing_and_finishing_arr[$dtls_data_arr[$row]['testing_parameters_id']]; ?></b></td>
										<td width="80"><? if ($dtls_data_arr[$row]['fabric_point'] == 0) echo '';
														else echo $dtls_data_arr[$row]['fabric_point']; ?></td>
										<td width="80"><? echo $yarn_test_result_arr[$dtls_data_arr[$row]['result']]; ?></td>
										<td width="80"><? echo $yarn_test_acceptance_arr[$dtls_data_arr[$row]['acceptance']]; ?></td>
										<td width="80"><? echo $dtls_data_arr[$row]['fabric_class']; ?></td>
										<td colspan="2"><? echo $dtls_data_arr[$row]['remarks']; ?></td>
									</tr>
								<?
								}
								?>
							</table>
						</td>
					</tr>
					<tr>
						<td></td>
					</tr>
					<tr>
						<td></td>
					</tr>
					<tr>
						<td></td>
					</tr>
					<tr>
						<td></td>
					</tr>
				<?
				}
				?>
			</table>
		</div>
		<br>
		<div style="width: 1320px; margin-left: 5px;">
			<?
			foreach ($sql_mst_comments_rslt as $row) {
			?>
				<table cellspacing="1" cellpadding="1" class="rpt_table2" rules="all" border="1" style="width: 1335px; border: 2px solid black;">
					<caption style="background-color: #dbb768; font-weight: bold; border-top: 2px solid black; border-left: 2px solid black; border-right:2px solid black;">Comments For <? echo $color_range[$row[csf('color')]]; ?></caption>
					<tr style="font-weight: bold;">
						<td width="200" align="center"><b>Department</b></td>
						<td width="100" align="center"><b>Acceptance</b></td>
						<td align="center"><b>Comments</b></td>
					</tr>
					<tr>
						<td width="200"><b>Comentes For Knitting Dept.</b></td>
						<td width="100"><? echo $comments_acceptance_arr[$row[csf('comments_knit_acceptance')]]; ?></td>
						<td><? echo $row[csf('comments_knit')]; ?></td>
					</tr>
					<tr>
						<td width="200"><b>Comentes For Dyeing/Finishing Dept.</b></td>
						<td width="100"><? echo $comments_acceptance_arr[$row[csf('comments_dye_acceptance')]]; ?></td>
						<td><? echo $row[csf('comments_dye')]; ?></td>
					</tr>
					<tr>
						<td width="200"><b>Comentes For Authorize Dept.</b></td>
						<td width="100"><? echo $comments_acceptance_arr[$row[csf('comments_author_acceptance')]]; ?></td>
						<td><? echo $row[csf('comments_author')]; ?></td>
					</tr>
				</table>
				<br>
			<?
				echo signature_table(259, $company_ID, "1030px", '', 0);
			}
			?>
		</div>
	<?
	}

	if ($action == "yarn_test_report") {
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



		if ($db_type == 0) $color_cond = " and color_name!=''";
		else $color_cond = " and color_name IS NOT NULL";
		$color_range_arr = return_library_array("select id,color_name from lib_color where status_active=1 and grey_color=1 $color_cond order by color_name", "id", "color_name");


		$sql_for_array = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit
	from product_details_master a
	where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.company_id='$data[0]'
	group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
		$product_array = array();
		$result = sql_select($sql_for_array);
		foreach ($result as $prod_val) {
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
							foreach ($data_array as $img_row) {
							?>
								<img src='../../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />
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
									<br />Yarn Test Report</u></strong></center>
						</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<?
						if ($data_arr[0][csf('test_for')] == 1) {
							$checked_bulk = "checked";
						} else if ($data_arr[0][csf('test_for')] == 2) {
							$checked_sample = "checked";
						} else {
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

																			$product_dtls = $data_arr[0][csf('prod_id')] . ", " . $data_arr[0][csf('lot_number')] . ", " . $yarn_count_arr[$product_array[$data_arr[0][csf('lot_number')]]['count']] . ", " . $product_array[$data_arr[0][csf('lot_number')]]['composition'] . ", " . $color_name_arr[$product_array[$data_arr[0][csf('lot_number')]]['color']] . ", " . $yarn_type[$product_array[$data_arr[0][csf('lot_number')]]['yarn_type']] . ", " . $product_array[$data_arr[0][csf('lot_number')]]['composition'] . ", " . $supplierArr[$product_array[$data_arr[0][csf('lot_number')]]['supplier_id']];
																			echo $product_dtls;

																			?></td>
					</tr>

				</table>
			</div>
			<div style="width:100%;">
				<table style="" cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
					<tr>
						<td colspan="3" bgcolor="#dddddd"><b>Basic Yarn Information :</b></td>
					</tr>
					<tr>
						<td width="35" align="center">1</td>
						<td width="61%">Tested Sample Weight</td>
						<td style="text-align:center;"><? echo $data_arr[0][csf('specimen_wgt')]; ?> kg</td>
					</tr>
					<tr>
						<td width="35" align="center">2</td>
						<td>Tested Sample Length</td>
						<td style="text-align:center;"><? echo $data_arr[0][csf('specimen_length')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">3</td>
						<td>Fabric Construction</td>
						<td style="text-align:center;"><? echo $data_arr[0][csf('fabric_construct')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">4</td>
						<td>Color Range</td>
						<td style="text-align:center;"><? echo $color_range_arr[$data_arr[0][csf('color')]]; ?></td>
					</tr>
					<tr>
						<td colspan="3" bgcolor="#dddddd"><b>Numerical Test :</b></td>
					</tr>
					<tr>
						<td width="35" align="center">1</td>
						<td>Actual Yarn Count</td>
						<td style="text-align:center;"><? echo $data_arr[0][csf('actual_yarn_count')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">2</td>
						<td>Yarn Appearance (Grade)</td>
						<td style="text-align:center;"><? echo $data_arr[0][csf('yarn_apperance_grad')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">3</td>
						<td>Twist Per Inch (TPI)</td>
						<td style="text-align:center;"><? echo $data_arr[0][csf('twist_per_inc')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">4</td>
						<td>Moisture Content</td>
						<td style="text-align:center;"><? echo $data_arr[0][csf('moisture_content')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">5</td>
						<td>CSP Minimum</td>
						<td style="text-align:center;"><? echo $data_arr[0][csf('csp_minimum')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">6</td>
						<td>CSP Actual</td>
						<td style="text-align:center;"><? echo $data_arr[0][csf('csp_actual')]; ?></td>
					</tr>
				</table>
			</div>
			<br />
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
							<th>Remarks</th>
						</tr>
					</thead>
					<tbody>
						<?
						$i = 1;
						foreach ($data_arr as $row) {
						?>
							<tr valign="middle" height="30">
								<td width="35" align="center"><? echo $i; ?></td>
								<td><? echo $row[csf('fabric_fault')]; ?></td>
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
			<br />
			<div style="width:100%;">
				<table cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
					<tr>
						<td width="100%" colspan="7" style=" text-align:justify; word-break:normal; height:80px;text-align:left;" valign="top"><strong><u>Yarn Quality Comments</u></strong>: <? echo $data_arr[0][csf('yarn_quality_coments')]; ?></td>
					</tr>
				</table>
			</div>
			<br /> <br /> <br /><br /> <br /> <br />
			<div style="width:100%;">
				<table cellspacing="0" width="100%" border="0">
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

	if ($action == "mrr_remarks") {
		echo load_html_head_contents("MRR Statement", "../../../../", 1, 1, $unicode, '', '');
		extract($_REQUEST);
		$sql = "Select a.id, a.recv_number, a.receive_date, b.cons_quantity, a.remarks
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and a.entry_form=1 and b.item_category=1 and b.transaction_type=1 and b.prod_id=$prod_id and b.status_active=1 and a.status_active=1";
		//echo $sql;
		$result = sql_select($sql);
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
					$i = 1;
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
							<td align="center"><? echo $i; ?></td>
							<td>
								<p><? echo $row[csf("recv_number")]; ?>&nbsp;</p>
							</td>
							<td align="center">
								<p><? if ($row[csf("receive_date")] != "" && $row[csf("receive_date")] != "0000-00-00") echo change_date_format($row[csf("receive_date")]); ?>&nbsp;</p>
							</td>
							<td align="right"><? echo number_format($row[csf("cons_quantity")], 2); ?></td>
							<td>
								<p><? echo $row[csf("remarks")]; ?>&nbsp;</p>
							</td>
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
							$all_remarks_arr = array();
							$all_remarks = "";
							$remarks_result = sql_select("Select remarks as allocate_remark from com_balk_yarn_allocate where product_id=$prod_id and status_active=1 and is_deleted=0");
							foreach ($remarks_result as $value) {
								if (in_array($value[csf('allocate_remark')], $all_remarks_arr) == false) {
									$all_remarks_arr[] = $value[csf('allocate_remark')];
									$all_remarks .= $value[csf('allocate_remark')] . ",";
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

	if ($action == "allocation_popup") {
		echo load_html_head_contents("Allocation Statement", "../../../../", 1, 1, $unicode, '', '');
		extract($_REQUEST);
		$buy_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

		if ($companyID == 0) {
			$company_cond = "";
		} else {
			$company_cond = " AND b.company_id=$companyID";
		}

		$sql_allocation = "SELECT a.id,a.item_id, a.job_no, a.po_break_down_id, a.booking_no, c.entry_form, SUM (a.qnty) AS allocate_qty, a.allocation_date, a.is_sales, b.dyed_type  AS is_dyied_yarn, c.booking_without_order FROM inv_material_allocation_dtls a, inv_material_allocation_mst c,product_details_master b WHERE a.mst_id = c.id AND a.item_id = c.item_id AND a.item_id = b.id AND a.item_id = $prod_id AND a.qnty > 0 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 $company_cond GROUP BY a.item_id, a.job_no, a.po_break_down_id, a.allocation_date, a.is_sales, b.dyed_type, a.booking_no, c.entry_form, c.booking_without_order,a.id order by a.id";

		//echo $sql_allocation;

		$result_allocation = sql_select($sql_allocation);
		foreach ($result_allocation as $row) {
			$is_dyied_yarn = $row[csf("is_dyied_yarn")];
			$job_no = preg_replace('/\s+/', '', $row[csf("job_no")]);

			$expBookinNo = explode("-", $row[csf("booking_no")]);

			if ($expBookinNo[1] == 'SMN' && $row[csf("is_sales")] != 1) {
				$row[csf("po_break_down_id")] = '';
			}

			if ($expBookinNo[1] == 'SMN') {
				$smn_booking_arr[$row[csf("booking_no")]] = "'" . $row[csf("booking_no")] . "'";
			}

			if ($row[csf("is_sales")] == 1) {
				if ($row[csf("po_break_down_id")] != "") {
					$fso_arr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
					$sales_job_arr[$row[csf("po_break_down_id")]]['job_no'] = $row[csf("job_no")];
				}

				$sales_job_arr["'" . $job_no . "'"]['is_sales'] = $row[csf("is_sales")];
			} else {
				if ($job_no != "") {
					$job_arr[$job_no] = $job_no;
				}

				if ($row[csf("po_break_down_id")] != "") {
					$po_break_down_arr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
				}
			}
		}

		/* echo "<pre>";
		print_r($job_arr); die(); */

		if (!empty($fso_arr)) {
			$fso_cond = " and a.id in(" . implode(",", $fso_arr) . ")";

			$jobsql = "select a.id, a.job_no,a.po_job_no,a.buyer_id,a.po_buyer,a.sales_booking_no, a.style_ref_no,a.within_group from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $fso_cond";
			//echo $jobsql;
			$jobData = sql_select($jobsql);
			$job_no_array = array();
			foreach ($jobData as $row) {
				$sales_order_arr[$row[csf('job_no')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
				$sales_order_arr[$row[csf('job_no')]]['po_buyer'] = $row[csf('po_buyer')];
				$sales_order_arr[$row[csf('job_no')]]['po_job_no'] = $row[csf('po_job_no')];
				$sales_order_arr[$row[csf('job_no')]]['buyer_id'] = $row[csf('buyer_id')];
				$sales_order_arr[$row[csf('job_no')]]['within_group'] = $row[csf('within_group')];
				$sales_order_arr[$row[csf('job_no')]]['style_ref_no'] = $row[csf('style_ref_no')];

				if ($row[csf('within_group')] == 1) {
					$job_arr[$row[csf('po_job_no')]] = $row[csf('po_job_no')];
				}
			}

			unset($jobData);
		}

		$sql_cond = "";
		if ($is_dyied_yarn == 1) // old dyed and dyed yarn structure adjust
		{
			if (!empty($job_arr)) {
				$sql_cond .= " and a.job_no_mst in('" . implode("','", $job_arr) . "')";
			}

			if (!empty($po_break_down_arr)) {
				$sql_cond .= (!empty($po_break_down_arr)) ? " and a.id in(" . implode(",", $po_break_down_arr) . ")" : "";
			}
		} else {
			if (!empty($job_arr)) {
				$sql_cond .= " and a.job_no_mst in('" . implode("','", $job_arr) . "')";
			}

			if (!empty($po_break_down_arr)) {
				$sql_cond .= (!empty($po_break_down_arr)) ? " and a.id in(" . implode(",", $po_break_down_arr) . ")" : "";
			}
		}

		//echo $sql_cond;
		//echo "<pre>";
		//print_r($sales_job_arr);
		$po_number_arr = $int_ref_arr = array();
		if ($sql_cond != "") {
			$po_sql = "select a.id,a.job_no_mst,a.shipment_date,b.buyer_name, a.file_no,a.grouping,a.po_number,a.status_active, b.style_ref_no from wo_po_break_down a, wo_po_details_master b where a.job_id=b.id and a.status_active in(1,3) and a.is_deleted=0 $sql_cond";
		}
		//echo $po_sql;
		$po_result = sql_select($po_sql);
		foreach ($po_result as $row) {
			$po_number_arr[$row[csf("id")]]['po'] = $row[csf("po_number")];
			$po_number_arr[$row[csf("id")]]['status_active'] = $row[csf("status_active")];
			$po_number_arr[$row[csf("id")]]['file'] = $row[csf("file_no")];
			$po_number_arr[$row[csf("id")]]['job_no'] = $row[csf("job_no_mst")];
			$po_number_arr[$row[csf("id")]]['style_ref_no'] = $row[csf("style_ref_no")];
			$po_number_arr[$row[csf("id")]]['shipment_date'] = $row[csf("shipment_date")];
			$po_number_arr[$row[csf("job_no_mst")]]['buyer_name'] = $row[csf("buyer_name")];
			$int_ref_arr[$row[csf("job_no_mst")]] = $row[csf("grouping")];
		}
		unset($po_result);

		//echo "<pre>";
		//print_r($int_ref_arr);	die();

		$planning_array = array();
		$planning_requisition_array = array();
		$plan_sql = "select a.po_id,a.booking_no,a.dtls_id,b.requisition_no,b.prod_id from ppl_planning_entry_plan_dtls a,ppl_yarn_requisition_entry b where a.dtls_id=b.knit_id  and b.prod_id=$prod_id group by a.po_id,a.booking_no,a.dtls_id,b.requisition_no,b.prod_id"; // and a.status_active=1 and b.status_active=1 ommit it program can be delete after issue
		//echo $plan_sql; die();
		$planData = sql_select($plan_sql);
		foreach ($planData as $row) {
			$planning_array[$row[csf('po_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]][] = $row[csf('booking_no')];
			$planning_requisition_array[$row[csf('booking_no')]][$row[csf('prod_id')]] = $row[csf('requisition_no')];
		}

		$prod_cond2 = " and c.prod_id in (" . $prod_id . ")";
		$prodCond = " and b.prod_id in (" . $prod_id . ")";
		$sql_issue = "select a.id,a.issue_basis,a.issue_purpose,a.booking_id,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type, sum(c.quantity) as issue_qty,b.job_no,c.is_sales from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and (a.issue_purpose in(1,2) or a.issue_purpose in(1,2,7,12,15,38,44,46) and b.job_no is not null) and c.trans_type=2 and a.entry_form=3 and b.item_category=1 $prod_cond2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 group by a.id,a.issue_basis,a.issue_purpose,a.booking_id,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type,b.job_no,c.is_sales";
		//echo $sql_issue;
		$result_issue = sql_select($sql_issue);
		$issue_array_req = $booking_arr = $issue_array = $issue_basis_arr = array();
		foreach ($result_issue as $row) {
			$issue_job[$row[csf("id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("job_no")];
			$issue_basis_arr[$row[csf("id")]]["basis"] = $row[csf("issue_basis")];
			$issue_basis_arr[$row[csf("id")]]["issue_id"] = $row[csf("id")];
			$issue_basis = $row[csf("issue_basis")];
			$issue_purpose =  $row[csf("issue_purpose")];
			$booking_id =  $row[csf("booking_id")];
			$po_id = $row[csf("po_breakdown_id")];
			$is_sales = $row[csf("is_sales")];

			if ($row[csf("job_no")] != "") {
				$job_no = $row[csf("job_no")];
			} else if ($is_sales == 1) {
				$job_no = $sales_job_arr[$po_id]['job_no'];
				$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			} else {
				$job_no = $po_number_arr[$po_id]['job_no'];
				$issue_arr[$job_no][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			}

			//echo "==".$job_no."<br>";

			if ($row[csf('dyed_type')] == 1) // Except SMN booking
			{
				if ($row[csf("issue_basis")] == 1) {
					$booking_no = return_field_value("fab_booking_no", "wo_yarn_dyeing_dtls", "mst_id ='" . $booking_id . "' and job_no ='" . $job_no . "' and is_deleted=0 and status_active=1 group by fab_booking_no", "fab_booking_no");
				} else {
					$booking_arr = $planning_array[$row[csf('po_breakdown_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]];
					$booking_arr = array_unique($booking_arr);
				}

				if (!empty($booking_arr) || $booking_no != "") {
					if ($row[csf("issue_basis")] == 1) {
						$expBookinNo = explode("-", $booking_no);

						if (($expBookinNo[1] != 'SMN')) {
							$issue_array[$job_no][$booking_no][$po_id][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
						}
					} else {
						foreach ($booking_arr as $booking_no) {
							$expBookinNo = explode("-", $booking_no);

							if (($expBookinNo[1] != 'SMN')) {
								$issue_array[$job_no][$booking_no][$po_id][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
							}
						}
					}
				} else // for old dyed yarn structure
				{
					$issue_array[$job_no][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
				}
			} else {
				$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("issue_basis")];

				if ($row[csf("issue_basis")] == 3 || $row[csf("issue_basis")] == 1 || $row[csf("issue_basis")] == 8) {
					if ($row[csf("issue_basis")] == 1) {
						if (($issue_basis == 1) && ($issue_purpose == 2 || $issue_purpose == 7 || $issue_purpose == 12 || $issue_purpose == 15 || $issue_purpose == 38 || $issue_purpose == 44 || $issue_purpose == 46)) {
							$booking_no = return_field_value("fab_booking_no", "wo_yarn_dyeing_dtls", "mst_id ='" . $booking_id . "' and job_no ='" . $job_no . "' and is_deleted=0 and status_active=1 group by fab_booking_no", "fab_booking_no");
							$booking_no = trim($booking_no); // Remove both side whitespace
							$expBookinNo = explode("-", $booking_no);

							if ($expBookinNo[1] != 'SMN') {
								if ($booking_no != "") {
									$issue_array[$job_no][$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
								} else {
									$issue_array[$job_no][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
								}
							}
						}
					} else {
						$booking_arr = $planning_array[$row[csf('po_breakdown_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]];
						$booking_arr = array_unique($booking_arr);
						foreach ($booking_arr as $booking) {
							$expBookinNo = explode("-", $booking);
							if ($expBookinNo[1] != 'SMN') {
								if ($booking != "") {
									$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_qty"] += $row[csf("issue_qty")];
									$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_id"][] = $row[csf("id")];
								} else {
									if ($row[csf("is_sales")] == 1) {
										$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_qty"] += $row[csf("issue_qty")];
									} else {
										$issue_array_req[$row[csf("prod_id")]][$booking]["issue_qty"] += $row[csf("issue_qty")];
									}
								}
							}
						}
					}
				}
			}
		}

		/* echo "<pre>";
		print_r($issue_array_req);
		echo "</pre>";
		die; */

		$issue_return_array = $issue_return_req_array = $job_wose_issue_return_array = array();
		$sql_return = "Select a.receive_basis, a.booking_id,a.booking_no,b.requisition_no,b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose,d.dyed_type,c.is_sales, c.quantity as issue_return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and c.trans_type=4 and c.prod_id=d.id and a.entry_form=9 and b.id=c.trans_id and b.item_category=1  and c.issue_purpose in(1,2,7,12,15,38,44,46) and a.receive_basis not in (2) $prod_cond2 and a.status_active=1 and b.status_active=1 and c.status_active=1"; //group by a.booking_id,a.booking_no,b.requisition_no,b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose,d.dyed_type,c.is_sales
		//echo $sql_return;
		$result_return = sql_select($sql_return);
		$booking_no = "";

		foreach ($result_return as $row) {
			$issue_basis = $row[csf("receive_basis")];
			$po_id = $row[csf("po_breakdown_id")];
			$is_sales = $row[csf("is_sales")];

			if ($row[csf('dyed_type')] == 1) {
				if ($is_sales == 1) {
					$job_no = $sales_job_arr[$po_id]['job_no'];
					$issue_arr[$po_id][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
				} else {
					$job_no = $po_number_arr[$po_id]['job_no'];
					$issue_arr[$job_no][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
				}

				if ($issue_basis == 3 || $issue_basis == 8) {
					if ($issue_basis == 3) {
						$booking_no = return_field_value("booking_no", "ppl_yarn_requisition_entry a,ppl_planning_entry_plan_dtls b", "a.requisition_no ='" . $row[csf('booking_id')] . "' and a.prod_id=" . $row[csf('prod_id')] . " and a.knit_id =b.dtls_id group by booking_no", "booking_no");
					} else {
						$booking_no = return_field_value("booking_no", "ppl_yarn_requisition_entry a,ppl_planning_entry_plan_dtls b", "a.requisition_no ='" . $row[csf('requisition_no')] . "' and a.prod_id=" . $row[csf('prod_id')] . " and a.knit_id =b.dtls_id group by booking_no", "booking_no");
					}
				} else {
					$booking_no = return_field_value("fab_booking_no", "wo_yarn_dyeing_dtls", "mst_id ='" . $row[csf('booking_id')] . "' and job_no ='" . $job_no . "' and is_deleted=0 and status_active=1 group by fab_booking_no", "fab_booking_no");
				}

				$booking_no = trim($booking_no); // Remove both side whitespace

				if ($booking_no != "") {
					$expBookinNo = explode("-", $booking_no);

					if (($expBookinNo[1] != 'SMN')) {
						$job_wose_issue_return_array[$job_no][$booking_no][$po_id][$row[csf('prod_id')]] += $row[csf("issue_return_qty")];
					}
				} else {
					$job_wose_issue_return_array[$job_no][$row[csf('prod_id')]] += $row[csf("issue_return_qty")];
				}
			} else {
				if ($issue_basis == 3 || $issue_basis == 8) {
					if ($issue_basis == 3) {
						$booking_no = return_field_value("booking_no", "ppl_yarn_requisition_entry a,ppl_planning_entry_plan_dtls b", "a.requisition_no ='" . $row[csf('booking_id')] . "' and a.prod_id=" . $row[csf('prod_id')] . " and a.knit_id =b.dtls_id group by booking_no", "booking_no");
					} else {
						$booking_no = return_field_value("booking_no", "ppl_yarn_requisition_entry a,ppl_planning_entry_plan_dtls b", "a.requisition_no ='" . $row[csf('requisition_no')] . "' and a.prod_id=" . $row[csf('prod_id')] . " and a.knit_id =b.dtls_id group by booking_no", "booking_no");
					}

					$expBookinNo = explode("-", $booking_no);

					if ($expBookinNo[1] != 'SMN') {
						$issue_id = $issue_basis_arr[$row[csf("issue_id")]]["issue_id"];
						$issue_return_po_array[$issue_id][$row[csf('prod_id')]] .= $row[csf('po_breakdown_id')] . ",";
						$issue_return_req_array[$booking_no][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('issue_id')]] += $row[csf("issue_return_qty")];
					}
				} else {
					if ($issue_basis == 1) {
						if (($row[csf("issue_purpose")] == 2 || $row[csf("issue_purpose")] == 7 || $row[csf("issue_purpose")] == 15 || $row[csf("issue_purpose")] == 38 || $row[csf("issue_purpose")] == 44 || $row[csf("issue_purpose")] == 46)) {
							$booking_id = $row[csf("booking_id")];

							if ($row[csf("is_sales")] == 1) {
								$job_no = $sales_job_arr[$row[csf("po_breakdown_id")]]['job_no'];
							} else {
								//echo $row[csf("po_breakdown_id")]."heloo";
								$job_no = $po_number_arr[$row[csf("po_breakdown_id")]]['job_no'];
							}

							$booking_no = return_field_value("fab_booking_no", "wo_yarn_dyeing_dtls", "mst_id ='" . $booking_id . "' and job_no ='" . $job_no . "' and is_deleted=0 and status_active=1 group by fab_booking_no", "fab_booking_no");

							$booking_no = trim($booking_no); // Remove both side whitespace

							if ($booking_no != "") {
								$expBookinNo = explode("-", $booking_no);
								if (($expBookinNo[1] != 'SMN')) {
									$issue_return_array[$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
								}
							} else {
								$booking_no = 0; // for old data picup
								$issue_return_array[$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
							}
						}
					}
				}
			}
		}

		/* echo "<pre>";
		print_r($issue_return_array);  die(); */

		$smn_booking_cond = (!empty($smn_booking_arr)) ? " and d.booking_no in (" . implode(",", $smn_booking_arr) . ")  " : "";
		$smn_booking_cond2 = (!empty($smn_booking_arr)) ? " and ( d.booking_no in (" . implode(",", $smn_booking_arr) . ") or d.fab_booking_no in (" . implode(",", $smn_booking_arr) . ") ) " : "";
		if ($smn_booking_cond != "") {
			$smn_sql = "SELECT sum(x.cons_quantity) as issue_qty,x.issue_id, x.booking_no,x.prod_id,x.buyer_id from(
			SELECT distinct a.company_id,a.id as issue_id,a.buyer_id, a.issue_basis, a.issue_purpose, d.booking_no, b.cons_quantity, b.id,b.prod_id
			from inv_issue_master a, inv_transaction b, ppl_yarn_requisition_entry c, ppl_planning_entry_plan_dtls d where a.id=b.mst_id and b.requisition_no=c.requisition_no and c.knit_id=d.dtls_id and b.prod_id=c.prod_id and b.transaction_type=2 and b.item_category=1 and  a.issue_purpose in (1,8) and  a.status_active=1 and b.status_active=1 and c.status_active=1 $smn_booking_cond $prodCond ) x group by x.company_id,x.issue_id, x.buyer_id, x.issue_basis, x.issue_purpose, x.booking_no,x.prod_id
			union all
			SELECT sum(y.cons_quantity) as issue_qty,y.issue_id, y.booking_no,y.prod_id,y.buyer_id from(
			SELECT distinct a.company_id,a.id as issue_id, a.buyer_id, a.issue_basis, a.issue_purpose, (d.booking_no || d.fab_booking_no) as booking_no , b.cons_quantity, b.id,b.prod_id
			from inv_issue_master a, inv_transaction b,wo_yarn_dyeing_mst c, wo_yarn_dyeing_dtls d where a.id=b.mst_id and a.booking_no=c.ydw_no  and d.product_id=b.prod_id and c.id=d.mst_id and b.transaction_type=2 and b.item_category=1 and  a.issue_purpose in (2,7,12,15,38,44,46) and  a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $smn_booking_cond2 $prodCond ) y group by y.company_id,y.issue_id, y.buyer_id, y.issue_basis, y.issue_purpose, y.booking_no,y.prod_id"; //and d.job_no=b.job_no
			//echo $smn_sql; die;
			$smn_res = sql_select($smn_sql);
			$smn_issue_booking_qnty_arr = array();
			foreach ($smn_res as $srow) {
				$smn_issue_booking_qnty_arr[$srow[csf('booking_no')]][$srow[csf('prod_id')]]['issue_qty'] += $srow[csf('issue_qty')];
				$smn_issue_booking_qnty_arr[$srow[csf('booking_no')]][$srow[csf('prod_id')]]['buyer_id'] = $srow[csf('buyer_id')];
				$smnIssueIdArr[$srow[csf("issue_id")]] = $srow[csf("issue_id")];
			}
		}

		//echo "<pre>";
		//print_r($smn_issue_booking_qnty_arr); die();

		//for smaple without order issue return qty
		$smnIssueIdcond = "";
		if (!empty($smnIssueIdArr)) {
			$smnIssueIdcond = "and b.issue_id in(" . implode(',', $smnIssueIdArr) . ")";

			$sqlRtnQty = "Select a.receive_basis as RECEIVE_BASIS,a.booking_id as BOOKING_ID, b.issue_id as ISSUE_ID,b.requisition_no as REQUISITION_NO, b.prod_id as PROD_ID, b.order_qnty as RETURN_QTY from inv_receive_master a, inv_transaction b, product_details_master d where a.id=b.mst_id and b.prod_id=d.id and a.entry_form=9 and b.item_category=1 and b.transaction_type=4 and a.receive_basis not in (2) and a.status_active=1 and b.status_active=1 " . $prodCond . " " . $smnIssueIdcond . " ";
			//echo $sqlRtnQty; die;
			$sqlRtnQtyRslt = sql_select($sqlRtnQty);

			$smn_issue_return_array = array();
			foreach ($sqlRtnQtyRslt as $row) {
				if ($row['RECEIVE_BASIS'] == 3) // Requisition 
				{
					$smn_booking_no = return_field_value("b.booking_no as BOOKING_NO", "ppl_yarn_requisition_entry a,ppl_planning_entry_plan_dtls b", "a.knit_id=b.dtls_id and a.requisition_no ='" . $row["BOOKING_ID"] . "' and a.prod_id ='" . $row["PROD_ID"] . "' and a.status_active=1 and a.is_deleted=0", "BOOKING_NO");
				} else if ($row['RECEIVE_BASIS'] == 8) // Demand
				{
					$smn_booking_no = return_field_value("b.booking_no as BOOKING_NO", "ppl_yarn_requisition_entry a,ppl_planning_entry_plan_dtls b", "a.knit_id=b.dtls_id and a.requisition_no ='" . $row["REQUISITION_NO"] . "' and a.prod_id ='" . $row["PROD_ID"] . "' and a.status_active=1 and a.is_deleted=0", "BOOKING_NO");
				} else {
					$smn_booking_no = return_field_value("(fab_booking_no || booking_no) as booking_no", "wo_yarn_dyeing_dtls", "mst_id ='" . $row["BOOKING_ID"] . "' and product_id ='" . $row["PROD_ID"] . "' and is_deleted=0 and status_active=1 group by fab_booking_no,booking_no", "booking_no");
				}

				$smn_issue_return_array[$smn_booking_no][$row['PROD_ID']] += $row['RETURN_QTY'];
			}
		}

		/* echo "<pre>";
		print_r($smn_issue_return_array); die; */
	?>
		<script>
			function openmypage_job(job, prod_id, action, title, popup_width) {
				emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'daily_yarn_stock_report_controller.php?job=' + job + '&prod_id=' + prod_id + '&action=' + action, title, 'width=' + popup_width + ', height=400px, center=1, resize=0, scrolling=0', '../../../');
			}
		</script>
		<div align="center">
			<table width="1190" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<th width="25">SL</th>
					<th width="75">Date</th>
					<th width="110">Job /FSO NO.</th>
					<th width="100">Buyer</th>
					<th width="100">Style Ref.</th>
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
			<div style="width:1190px; max-height:300px" id="scroll_body">
				<table width="1190" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
					$i = 1;
					$balance = '';

					if (!empty($result_allocation)) {
						foreach ($result_allocation as $row) {
							if ($row[csf("booking_without_order")] == 1) {
								$row[csf("po_break_down_id")] = '';
							}

							if ($po_number_arr[$row[csf("po_break_down_id")]]['status_active'] == 3) {
								$bgcolor = "#f0ad4e";
								$title = "This PO is cancel PO";
							} else if ($i % 2 == 0) {
								$bgcolor = "#E9F3FF";
								$title = "";
							} else {
								$bgcolor = "#FFFFFF";
								$title = "";
							}

							if ($row[csf("po_break_down_id")] == "" && $row[csf("booking_no")] == "") {
								$issue_basis = array_unique($issue_arr[$row[csf('job_no')]][$row[csf('item_id')]]);
							} else if ($row[csf("po_break_down_id")] == "") {
								$issue_basis = array_unique($issue_arr[$row[csf('job_no')]][$row[csf('item_id')]]);
							} else {
								$issue_basis = array_unique($issue_arr[$row[csf("po_break_down_id")]][$row[csf('item_id')]]);
							}

							$expBookinNo = explode("-", $row[csf("booking_no")]);

							if ($expBookinNo[1] == 'SMN') {
								$smn_issue_booking_qnty = $smn_issue_booking_qnty_arr[$row[csf("booking_no")]][$row[csf('item_id')]]['issue_qty'];
							}

							//print_r($issue_basis);
							if ($row[csf("is_dyied_yarn")] == 1) {
								if ($row[csf("booking_no")] != "") {
									if (($expBookinNo[1] != 'SMN')) {
										$issue_qty += $issue_array[$row[csf("job_no")]][$row[csf("booking_no")]][$row[csf("po_break_down_id")]][$prod_id]["issue_qty"];
									}
								} else // old structure of dyed yarn
								{
									$issue_qty += $issue_array[$row[csf("job_no")]][$prod_id]["issue_qty"];
									//echo $row[csf("job_no")]."==".$prod_id."==".$issue_qty."<br>";
								}
							} else {
								$issue_qty = $issue_qty_wo = 0;

								foreach ($issue_basis as $basis) {
									if ($basis == 3 || $basis == 1 || $basis == 8) {
										if ($basis == 1) {
											$expBookinNo = explode("-", $row[csf("booking_no")]);

											if (($row[csf("booking_no")] != "") && ($expBookinNo[1] != 'SMN')) {
												$issue_qty_wo = $issue_array[$row[csf('job_no')]][$row[csf("booking_no")]][$row[csf("po_break_down_id")]][$row[csf('item_id')]]["issue_qty"];
											} else {
												$issue_qty_wo = $issue_array[$row[csf('job_no')]][$row[csf('item_id')]]["issue_qty"];
											}
										} else {
											if ($row[csf("booking_no")] != "" && $row[csf("po_break_down_id")] != "") {
												$issue_qty = $issue_array_req[$row[csf("po_break_down_id")]][$row[csf('item_id')]][$row[csf("booking_no")]]["issue_qty"];
											} else {
												$issue_qty = $issue_array_req[$row[csf('item_id')]][$row[csf("booking_no")]]["issue_qty"];
											}
										}
									}
								}
							}

							if ($row[csf('is_sales')] == 1) {
								$po_number = "";
								$shipment_date = "";
								$within_group = $sales_order_arr[$row[csf('job_no')]]['within_group'];

								if ($within_group == 1) {
									$group_ref = $int_ref_arr[$sales_order_arr[$row[csf('job_no')]]['po_job_no']];
									$buyer_id = $sales_order_arr[$row[csf('job_no')]]['po_buyer'];
									$buyername = $buy_name_arr[$buyer_id];
									$style_ref_no = $sales_order_arr[$row[csf('job_no')]]['style_ref_no'];
								} else {
									$buyer_id = $sales_order_arr[$row[csf('job_no')]]['buyer_id'];
									$buyername = $buy_name_arr[$buyer_id];
									$group_ref = "";
								}
							} else {
								if ($row[csf("booking_no")] != "" && $expBookinNo[1] == 'SMN') {
									$buyer_id = $smn_issue_booking_qnty_arr[$row[csf("booking_no")]][$row[csf('item_id')]]['buyer_id'];
									$buyername = $buy_name_arr[$buyer_id];
								} else {
									if (!empty($buy_name_arr[$po_number_arr[$row[csf("job_no")]]['buyer_name']])) {
										$buyername = $buy_name_arr[$po_number_arr[$row[csf("job_no")]]['buyer_name']];
									}
								}

								$po_number = $po_number_arr[$row[csf("po_break_down_id")]]['po'];
								$group_ref = $int_ref_arr[$row[csf("job_no")]];
								$shipment_date = $po_number_arr[$row[csf("po_break_down_id")]]['shipment_date'];
								$style_ref_no = $po_number_arr[$row[csf("po_break_down_id")]]['style_ref_no'];
							}

							if ($row[csf("is_dyied_yarn")] == 1) {
								if ($row[csf("booking_no")] != "") {
									$return_qty = $job_wose_issue_return_array[$row[csf("job_no")]][$row[csf("booking_no")]][$row[csf("po_break_down_id")]][$row[csf('item_id')]];
								} else {
									$return_qty = $job_wose_issue_return_array[$row[csf("job_no")]][$row[csf('item_id')]];
								}

								//for smaple without order issue return qty
								$expBookinNo = explode("-", $row[csf("booking_no")]);
								if ($expBookinNo[1] == 'SMN') {
									$return_qty = $return_qty + $smn_issue_return_array[$row[csf("booking_no")]][$row[csf('item_id')]];
								}
							} else {
								$return_qty = 0;
								foreach ($issue_basis as $basis) {
									if ($basis == 3 || $basis == 8) {
										$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$row[csf('item_id')]][$row[csf("booking_no")]]["issue_id"]);

										foreach ($issue_ids as $issue_id) {
											$return_qty += $issue_return_req_array[$row[csf("booking_no")]][$row[csf('po_break_down_id')]][$row[csf('item_id')]][$issue_id];
										}
									} else {
										if ($basis == 1) // booking basis-- work order
										{
											$return_qty += $issue_return_array[$row[csf("booking_no")]][$row[csf("po_break_down_id")]][$row[csf('item_id')]];
										} else {
											$return_qty += $issue_return_array[$row[csf("po_break_down_id")]][$row[csf('item_id')]];
										}
									}
								}

								//for smaple without order issue return qty
								$expBookinNo = explode("-", $row[csf("booking_no")]);
								if ($expBookinNo[1] == 'SMN') {
									$return_qty = $return_qty + $smn_issue_return_array[$row[csf("booking_no")]][$prod_id];
								}
							}

					?>

							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" title="<? echo $title; ?>">
								<td width="25" align="center" title="<? echo $row[csf("id")]; ?>"><? echo $i; ?></td>
								<td width="75" align="center"><? echo change_date_format($row[csf("allocation_date")]); ?></td>
								<td width="110">
									<div style="word-wrap:break-word; width:110px;text-align: center;">
										<?
										$requisitionNo = $planning_requisition_array[$row[csf("booking_no")]][$prod_id];
										if ($requisitionNo != "") {
										?>
											<a href="#report_details" onClick="openmypage_job('<? echo $row[csf('job_no')] . "**" . $row[csf('po_break_down_id')]; ?>','<? echo $row[csf('item_id')]; ?>','requisition_details_popup','Requisition Details','480px')"><? echo $row[csf("job_no")]; ?></a>
										<?
										} else {
											echo $row[csf("job_no")];
										}
										?>
									</div>
								</td>
								<td width="100" align="center">
									<p><? echo $buyername; ?>
			</div>
			</td>
			<td width="100" align="center">
				<p><? echo $style_ref_no; ?></p>
			</td>
			<td width="100" title='<? echo $row[csf("po_break_down_id")]; ?>' align="center">
				<p><? echo $po_number; ?></p>
			</td>
			<td width="100" align="center"><? echo change_date_format($shipment_date); ?></td>
			<td width="100">
				<p><? echo $po_number_arr[$row[csf("po_break_down_id")]]['file']; ?></p>
			</td>
			<td width="100">
				<p><? echo $group_ref; ?></p>
			</td>
			<td width="110">
				<p><? echo $row[csf("booking_no")]; ?></p>
			</td>
			<td width="75" align="right">
				<?
							$rcv_rtn_qty = 0;
							$allocate_qty = $row[csf("allocate_qty")];

							echo number_format($allocate_qty, 2);
				?>
			</td>
			<td width="70" align="right" title="<? echo $issue_id . "==" . $issue_qty . '==' . $issue_qty_wo . '==' . $smn_issue_booking_qnty; ?>"><? echo number_format(($issue_qty + $issue_qty_wo + $smn_issue_booking_qnty), 2); //$issue_qty."+".$issue_qty_wo."+".$smn_issue_booking_qnty;  
																																					?></td>
			<td width="60" align="right"><? echo number_format($return_qty, 2); ?></td>
			<td align="right">
				<?
							$balance = $balance + ($row[csf("allocate_qty")] + number_format($return_qty, 2, ".", "")) - (number_format(($issue_qty + $issue_qty_wo + $smn_issue_booking_qnty), 2, ".", "") + number_format($rcv_rtn_qty, 2, ".", ""));
							echo number_format($balance, 2);
				?>
			</td>
			</tr>
	<?
							$i++;
							$issue_qty = $return_qty = $issue_qty_wo = $smn_issue_booking_qnty = 0;
						}
					} else {
						echo "<tr colspan='13'><th style='text-align:center;'>No Data Found</th></tr>";
					}
	?>
	</table>
		</div>
			</div>
		<?
		exit();
	}

	if ($action == "requisition_details_popup") {
		echo load_html_head_contents("Requisition Statement", "../../../../", 1, 1, $unicode, '', '');
		extract($_REQUEST);
		if ($db_type == 0) {
			$grp_con = "group_concat(dtls_id) as prog_no";
		} else {
			$grp_con = "LISTAGG(dtls_id, ',') WITHIN GROUP (ORDER BY id) as prog_no";
		}

		$job_str_arr = explode("**", $job);
		$job_no = $job_str_arr[0];
		$po_id = $job_str_arr[1];

		$job_no_array = array();
		$jobsql = ("select a.id, a.job_no,a.sales_booking_no, a.style_ref_no,a.within_group from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.id=$po_id");
		$jobData = sql_select($jobsql);
		foreach ($jobData as $row) {
			$job_no_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
			$job_no_array[$row[csf('id')]]['job_id'] = $row[csf('id')];
			$job_no_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
			$within_group = $row[csf('within_group')];
		}

		$prog_no = return_field_value("$grp_con", "ppl_planning_entry_plan_dtls", "po_id ='" . $po_id . "' and is_sales=1", "prog_no"); //and is_deleted=0 and status_active=1: ommit program can be deleted even after issue cause revised
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
							$i = 1;
							$tot_recv_qnty = 0;
							$sql = "select a.id, a.knit_id, a.requisition_no, a.requisition_date,sum(a.yarn_qnty) as qnty from ppl_yarn_requisition_entry a,ppl_planning_entry_plan_dtls b	where a.knit_id=b.dtls_id and b.po_id=$po_id and a.status_active=1 and a.is_deleted=0 and a.prod_id in($prod_id) and a.knit_id in($prog_no) group by a.id, a.knit_id, a.requisition_no,a.requisition_date order by a.id";
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
									<td width="100">
										<p><? echo change_date_format($row[csf("requisition_date")]); ?></p>
									</td>
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
									<th align="right"><? echo number_format($tot_recv_qnty, 2); ?> </th>
								</tr>
							</tfoot>
						</table>
					</div>
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

		$company_id = return_field_value("company_id", "PRODUCT_DETAILS_MASTER", "id=$prod_id", "company_id");

		$vs_acknoldgement = return_field_value("max(auto_transfer_rcv) auto_transfer_rcv", "variable_settings_inventory", "company_name=$company_id and variable_list=27 and item_category_id=1 and status_active=1 and is_deleted=0", "auto_transfer_rcv");
		$vs_acknoldgement_yes = ($vs_acknoldgement == "" || $vs_acknoldgement == 1) ? 0 : 1;
		$is_acknowledge_cond = ($vs_acknoldgement_yes == 1) ? " and c.is_acknowledge=1" : ""


		?>
			<fieldset style="width:720px">
				<legend>Yarn Receive Details</legend>
				<table width="720" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="100">MRR No.</th>
						<th width="100">Transaction Type</th>
						<th width="70">Receive Date</th>
						<th width="100">Receive Qty.</th>
						<th width="100">Receive Basis</th>
						<th width="100">Receive Purpose</th>
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

						$sql = "select c.id, c.recv_number, c.receive_date, c.receive_basis, c.receive_purpose, c.booking_id, b.transaction_type, sum(b.cons_quantity) as qnty from inv_transaction b, inv_receive_master c where b.mst_id=c.id and b.prod_id=$prod_id and c.entry_form in(1,9) and b.transaction_type in(1,4) and b.item_category=1 and b.status_active=1 and b.is_deleted=0 $store_cond group by c.id, c.recv_number, c.receive_date, c.receive_basis, c.receive_purpose, c.booking_id, b.transaction_type order by c.id,c.receive_date";
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

							//for receive basis and purpose
							$rcv_basis = '';
							$rcv_purpose = '';
							if ($row[csf("transaction_type")] == 1) {
								$rcv_basis = $receive_basis_arr[$row[csf("receive_basis")]];
								$rcv_purpose = $yarn_issue_purpose[$row[csf("receive_purpose")]];
							} else if ($row[csf("transaction_type")] == 4) {
								$rcv_basis = $issue_basis[$row[csf("receive_basis")]];
								$rcv_purpose = 'Issue Return';
							}
							//end for receive basis and purpose
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="100">
									<p><? echo $row[csf("recv_number")]; ?></p>
								</td>
								<td width="100" align="center"><? echo $transaction_type[$row[csf("transaction_type")]]; ?>&nbsp;</td>
								<td width="70" align="center"><? echo change_date_format($row[csf("receive_date")]); ?>&nbsp;</td>
								<td width="100" align="right"><? echo number_format($receive_qty, 2); ?>&nbsp;</td>
								<td width="100">
									<p><? echo $rcv_basis; ?></p>
								</td>
								<td width="100">
									<p><? echo $rcv_purpose; ?></p>
								</td>
								<td>
									<p><? echo $btblc_no; ?>&nbsp;</p>
								</td>
							</tr>
						<?
							$i++;
						}

						$sql = "select c.id,c.transfer_system_id,c.transfer_date, b.transaction_type, sum(b.cons_quantity) as qnty from inv_transaction b,inv_item_transfer_mst c where b.mst_id=c.id and b.prod_id=$prod_id and b.transaction_type=5 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 group by c.id, c.transfer_system_id, c.transfer_date, b.transaction_type order by c.id,c.transfer_date";

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
								<td width="30"><? echo $i; ?></td>
								<td width="100">
									<p><? echo $row[csf("transfer_system_id")]; ?></p>
								</td>
								<td width="100" align="center"><? echo $transaction_type[$row[csf("transaction_type")]]; ?>&nbsp;</td>
								<td width="70" align="center"><? echo change_date_format($row[csf("transfer_date")]); ?>&nbsp;</td>
								<td width="100" align="right"><? echo number_format($receive_qty, 2); ?>&nbsp;</td>
								<td width="100">Transfer</td>
								<td width="100">Transfer</td>
								<td>&nbsp;</td>
							</tr>
						<?
							$i++;
						}
						?>
						<tfoot>
							<th colspan="4">Receive Total</th>
							<th><? echo number_format($tot_recv_qnty, 2); ?>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
				</div>

				<legend style="margin-top:10px;">Yarn Issue Details</legend>
				<table width="720" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="100">MRR No.</th>
						<th width="100">Transaction Type</th>
						<th width="70">Issue Date</th>
						<th width="100">Issue Qty.</th>
						<th width="100">Issue Basis</th>
						<th>Issue Purpose</th>
					</thead>
				</table>
				<div style="width:720px; overflow-y:scroll; max-height:300px" id="scroll_body">
					<table width="700" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$i = 1;
						$store_cond = "";
						if ($store_id > 0) $store_cond = " and b.store_id=$store_id";

						$sql = "select c.id, c.issue_number as recv_number, c.issue_date, c.issue_basis, c.issue_purpose, c.booking_id, b.transaction_type, sum(b.cons_quantity) as qnty from inv_transaction b, inv_issue_master c where b.mst_id=c.id and b.prod_id=$prod_id and c.entry_form in(3,8) and b.transaction_type in(2,3) and b.item_category=1 and b.status_active=1 and b.is_deleted=0 $store_cond group by c.id, c.issue_number, c.issue_date, c.issue_basis, c.issue_purpose, c.booking_id, b.transaction_type order by c.id,c.issue_date";
						//echo $sql;
						$result = sql_select($sql);
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							//for issue basis
							$iss_basis = '';
							$iss_purpose = '';
							if ($row[csf("transaction_type")] == 3) {
								$iss_basis = $receive_basis_arr[$row[csf("issue_basis")]];
								$iss_purpose = 'Receive Return';
							} else if ($row[csf("transaction_type")] == 2) {
								$iss_basis = $issue_basis[$row[csf("issue_basis")]];
								$iss_purpose = $yarn_issue_purpose[$row[csf("issue_purpose")]];
							}
							//end for issue basis

							$issue_qty = $row[csf("qnty")];
							$tot_issue_qnty += $issue_qty;
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="100">
									<p><? echo $row[csf("recv_number")]; ?></p>
								</td>
								<td width="100" align="center"><? echo $transaction_type[$row[csf("transaction_type")]]; ?>&nbsp;</td>
								<td width="70" align="center"><? echo change_date_format($row[csf("issue_date")]); ?>&nbsp;</td>
								<td width="100" align="right"><? echo number_format($issue_qty, 2); ?>&nbsp;</td>
								<td width="100"><? echo $iss_basis; ?></td>
								<td><? echo $iss_purpose; ?></td>
							</tr>
						<?
							$i++;
						}

						$sql = "select c.id, c.transfer_system_id,c.transfer_date, b.transaction_type, sum(b.cons_quantity) as qnty from inv_transaction b,inv_item_transfer_mst c where b.mst_id=c.id and b.prod_id=$prod_id and b.transaction_type=6 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 $is_acknowledge_cond group by c.id, c.transfer_system_id, c.transfer_date, b.transaction_type order by c.id,c.transfer_date";

						//echo $sql;
						$result = sql_select($sql);
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$issue_qty = $row[csf("qnty")];
							$tot_issue_qnty += $issue_qty;
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="100">
									<p><? echo $row[csf("transfer_system_id")]; ?></p>
								</td>
								<td width="100" align="center"><? echo $transaction_type[$row[csf("transaction_type")]]; ?>&nbsp;</td>
								<td width="70" align="center"><? echo change_date_format($row[csf("transfer_date")]); ?>&nbsp;</td>
								<td width="100" align="right"><? echo number_format($issue_qty, 2); ?>&nbsp;</td>
								<td width="100">Transfer</td>
								<td>Transfer</td>
							</tr>
						<?
							$i++;
						}
						?>
						<tfoot>
							<tr>
								<th colspan="4">Issue Total</th>
								<th><? echo number_format($tot_issue_qnty, 2); ?>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
							<tr>
								<th colspan="4">Stock</th>
								<th><? $stock_tot_qnty = $tot_recv_qnty - $tot_issue_qnty;
									echo number_format($stock_tot_qnty, 2); ?>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
		<?
		exit();
	}

	if ($action == "stock_popup_store_methode_upto") {
		echo load_html_head_contents("Stock Details", "../../../../", 1, 1, $unicode, '', '');
		extract($_REQUEST);
		$prod_id_ref = explode("_", $prod_id);
		$prod_id = $prod_id_ref[0];
		$store_id = $prod_id_ref[1];
		$company_id = $prod_id_ref[2];

		$store_upto_variable = return_field_value("store_method", "variable_settings_inventory", "company_name='$company_id' and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");

		if ($store_upto_variable == "" || $store_upto_variable < 2) {
			$store_method_upto = ", b.store_id";
		} else {
			if ($store_upto_variable == 2) {
				$store_method_upto = ", b.store_id,b.floor_id";
			}
			if ($store_upto_variable == 3) {
				$store_method_upto = " , b.store_id,b.floor_id,b.room";
			}
			if ($store_upto_variable == 4) {
				$store_method_upto = " , b.store_id,b.floor_id,b.room,b.rack";
			}
			if ($store_upto_variable == 5) {
				$store_method_upto = " , b.store_id,b.floor_id,b.room,b.rack,b.self";
			}
			if ($store_upto_variable == 6) {
				$store_method_upto = " , b.store_id,b.floor_id,b.room,b.rack,b.self,b.bin_box";
			}
		}

		$store_name_arr = return_library_array("select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company_id and b.category_type in(1) and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name", "id", "store_name");

		$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where company_id=$company_id and status_active=1 and is_deleted=0 group by floor_room_rack_id, floor_room_rack_name order by floor_room_rack_name", "floor_room_rack_id", "floor_room_rack_name");
		?>
			<fieldset style="width:1220px">
				<legend>Yarn Receive Details</legend>
				<table width="1220" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="100">MRR No.</th>
						<th width="100">Transaction Type</th>
						<th width="70">Receive Date</th>
						<th width="80">Store</th>
						<th width="80">Floor</th>
						<th width="80">Room</th>
						<th width="80">Rack</th>
						<th width="80">Self</th>
						<th width="100">Bin/Box</th>
						<th width="100">Receive Qty.</th>
						<th width="100">Receive Basis</th>
						<th width="100">Receive Purpose</th>
						<th>BTB LC No.</th>
					</thead>
				</table>

				<div style="width:1220px; overflow-y:scroll; max-height:300px" id="scroll_body">
					<table width="1200" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$i = 1;
						$tot_recv_qnty = '';
						$btblc_arr = return_library_array("select b.pi_id, a.lc_number from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.pi_entry_form=165", 'pi_id', 'lc_number');
						$store_cond = "";
						if ($store_id > 0)
							$store_cond = " and b.store_id=$store_id";

						$sql = "select c.id, c.recv_number, c.receive_date, c.receive_basis, c.receive_purpose, c.booking_id, b.transaction_type, sum(b.cons_quantity) as qnty $store_method_upto from inv_transaction b, inv_receive_master c where b.mst_id=c.id and b.prod_id=$prod_id and b.company_id=$company_id and c.entry_form in(1,9) and b.transaction_type in(1,4) and b.item_category=1 and b.status_active=1 and b.is_deleted=0 $store_cond  group by c.id, c.recv_number, c.receive_date, c.receive_basis, c.receive_purpose, c.booking_id, b.transaction_type $store_method_upto order by c.id,c.receive_date";
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

							//for receive basis and purpose
							$rcv_basis = '';
							$rcv_purpose = '';
							if ($row[csf("transaction_type")] == 1) {
								$rcv_basis = $receive_basis_arr[$row[csf("receive_basis")]];
								$rcv_purpose = $yarn_issue_purpose[$row[csf("receive_purpose")]];
							} else if ($row[csf("transaction_type")] == 4) {
								$rcv_basis = $issue_basis[$row[csf("receive_basis")]];
								$rcv_purpose = 'Issue Return';
							}
							//end for receive basis and purpose
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="100">
									<p><? echo $row[csf("recv_number")]; ?></p>
								</td>
								<td width="100" align="center"><? echo $transaction_type[$row[csf("transaction_type")]]; ?>&nbsp;</td>
								<td width="70" align="center"><? echo change_date_format($row[csf("receive_date")]); ?>&nbsp;</td>
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
								<td width="100" align="right"><? echo number_format($receive_qty, 2); ?>&nbsp;</td>
								<td width="100">
									<p><? echo $rcv_basis; ?></p>
								</td>
								<td width="100">
									<p><? echo $rcv_purpose; ?></p>
								</td>
								<td>
									<p><? echo $btblc_no; ?>&nbsp;</p>
								</td>
							</tr>
						<?
							$i++;
						}

						$sql = "select c.id,c.transfer_system_id,c.transfer_date, b.transaction_type, sum(b.cons_quantity) as qnty $store_method_upto from inv_transaction b,inv_item_transfer_mst c where b.mst_id=c.id and b.prod_id=$prod_id and b.transaction_type=5 and b.company_id=$company_id and b.item_category=1 and b.status_active=1 and b.is_deleted=0 group by c.id, c.transfer_system_id, c.transfer_date, b.transaction_type $store_method_upto order by c.id,c.transfer_date";
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
								<td width="30"><? echo $i; ?></td>
								<td width="100">
									<p><? echo $row[csf("transfer_system_id")]; ?></p>
								</td>
								<td width="100" align="center"><? echo $transaction_type[$row[csf("transaction_type")]]; ?>&nbsp;</td>
								<td width="70" align="center"><? echo change_date_format($row[csf("transfer_date")]); ?>&nbsp;</td>
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
								<td width="100" align="right"><? echo number_format($receive_qty, 2); ?>&nbsp;</td>
								<td width="100">Transfer</td>
								<td width="100">Transfer</td>
								<td>&nbsp;</td>
							</tr>
						<?
							$i++;
						}
						?>
						<tfoot>
							<th colspan="10">Receive Total</th>
							<th><? echo number_format($tot_recv_qnty, 2); ?>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
				</div>

				<legend style="margin-top:10px;">Yarn Issue Details</legend>
				<table width="1220" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="100">MRR No.</th>
						<th width="100">Transaction Type</th>
						<th width="70">Issue Date</th>
						<th width="80">Store</th>
						<th width="80">Floor</th>
						<th width="80">Room</th>
						<th width="80">Rack</th>
						<th width="80">Self</th>
						<th width="100">Bin/Box</th>
						<th width="100">Issue Qty.</th>
						<th width="100">Issue Basis</th>
						<th>Issue Purpose</th>
					</thead>
				</table>
				<div style="width:1220px; overflow-y:scroll; max-height:300px" id="scroll_body">

					<table width="1200" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$i = 1;
						$store_cond = "";
						if ($store_id > 0) $store_cond = " and b.store_id=$store_id";

						$sql = "select c.id, c.issue_number as recv_number, c.issue_date, c.issue_basis, c.issue_purpose, c.booking_id, b.transaction_type, sum(b.cons_quantity) as qnty $store_method_upto from inv_transaction b, inv_issue_master c where b.mst_id=c.id and b.prod_id=$prod_id and c.entry_form in(3,8) and b.transaction_type in(2,3) and b.company_id=$company_id and b.item_category=1 and b.status_active=1 and b.is_deleted=0 $store_cond group by c.id, c.issue_number, c.issue_date, c.issue_basis, c.issue_purpose, c.booking_id, b.transaction_type $store_method_upto order by c.id,c.issue_date";
						//echo $sql;
						$result = sql_select($sql);
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							//for issue basis
							$iss_basis = '';
							$iss_purpose = '';
							if ($row[csf("transaction_type")] == 3) {
								$iss_basis = $receive_basis_arr[$row[csf("issue_basis")]];
								$iss_purpose = 'Receive Return';
							} else if ($row[csf("transaction_type")] == 2) {
								$iss_basis = $issue_basis[$row[csf("issue_basis")]];
								$iss_purpose = $yarn_issue_purpose[$row[csf("issue_purpose")]];
							}
							//end for issue basis

							$issue_qty = $row[csf("qnty")];
							$tot_issue_qnty += $issue_qty;
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="100">
									<p><? echo $row[csf("recv_number")]; ?></p>
								</td>
								<td width="100" align="center"><? echo $transaction_type[$row[csf("transaction_type")]]; ?>&nbsp;</td>
								<td width="70" align="center"><? echo change_date_format($row[csf("issue_date")]); ?>&nbsp;</td>
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
								<td width="100" align="right"><? echo number_format($issue_qty, 2); ?>&nbsp;</td>
								<td width="100"><? echo $iss_basis; ?></td>
								<td><? echo $iss_purpose; ?></td>
							</tr>
						<?
							$i++;
						}

						$sql = "select c.id, c.transfer_system_id,c.transfer_date, b.transaction_type, sum(b.cons_quantity) as qnty $store_method_upto from inv_transaction b,inv_item_transfer_mst c where b.mst_id=c.id and b.prod_id=$prod_id and b.transaction_type=6 and b.company_id=$company_id and b.item_category=1 and b.status_active=1 and b.is_deleted=0 group by c.id, c.transfer_system_id, c.transfer_date, b.transaction_type $store_method_upto order by c.id,c.transfer_date";
						$result = sql_select($sql);
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$issue_qty = $row[csf("qnty")];
							$tot_issue_qnty += $issue_qty;
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="100">
									<p><? echo $row[csf("transfer_system_id")]; ?></p>
								</td>
								<td width="100" align="center"><? echo $transaction_type[$row[csf("transaction_type")]]; ?>&nbsp;</td>
								<td width="70" align="center"><? echo change_date_format($row[csf("transfer_date")]); ?>&nbsp;</td>
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
								<td width="100" align="right"><? echo number_format($issue_qty, 2); ?>&nbsp;</td>
								<td width="100">Transfer</td>
								<td>Transfer</td>
							</tr>
						<?
							$i++;
						}
						?>
						<tfoot>
							<tr>
								<th colspan="10">Issue Total</th>
								<th><? echo number_format($tot_issue_qnty, 2); ?>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
							<tr>
								<th colspan="10">Stock</th>
								<th><? $stock_tot_qnty = $tot_recv_qnty - $tot_issue_qnty;
									echo number_format($stock_tot_qnty, 2); ?>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
						</tfoot>
					</table>
				</div>

				<legend style="margin-top:10px; width: 910px;">Stock Details</legend>
				<?
				$sql = "SELECT prod_id, round (sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)),2) as balance $store_method_upto
			from inv_transaction b
			where company_id=$company_id and prod_id=$prod_id and item_category=1 and status_active=1 and is_deleted=0
			group by prod_id $store_method_upto";
				//echo $sql;

				$arr = array(1 => $store_name_arr, 2 => $floor_room_rack_arr, 3 => $floor_room_rack_arr, 4 => $floor_room_rack_arr, 5 => $floor_room_rack_arr, 6 => $floor_room_rack_arr, 7 => $floor_room_rack_arr);

				echo  create_list_view("list_view", "Product Id,Store,Floor,Room,Rack,Shelf,Bin,Current Stock", "100,150,100,100,100,100,100", "900", "250", 0, $sql, "js_set_value", "prod_id,store_id,floor_id,room,rack,self,bin_box,balance", "", 1, "0,store_id,floor_id,room,rack,self,bin_box,0", $arr, "prod_id,store_id,floor_id,room,rack,self,bin_box,balance", "", 'setFilterGrid("list_view",-1);');
				?>


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
						} else if ($type == 5) {
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
								<td width="120">
									<p><? echo $row[csf("recv_number")]; ?></p>
								</td>
								<td width="100" align="center"><? echo change_date_format($row[csf("receive_date")]); ?>&nbsp;</td>
								<td width="110" align="right"><? echo number_format($receive_qty, 2); ?>&nbsp;</td>
								<td width="110">
									<p><? echo $receive_basis_arr[$row[csf("receive_basis")]]; ?></p>
								</td>
								<td>
									<p><? echo $btblc_no; ?>&nbsp;</p>
								</td>
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
								<td width="120">
									<p><? echo $row[csf("transfer_system_id")]; ?></p>
								</td>
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

	if ($action == "company_wise_report_button_setting") {
		extract($_REQUEST);
		$buttonIdArr = ['102#count_wise_summ', '103#type_wise_summ', '104#composition_wise_summ', '105#stock_only', '106#count_type_wise_2', '107#report_1', '108#show_1', '152#mrr_wise_stock', '338#yarn_test', '195#show_2', '778#rack_wise', '811#composition_wise_summ2', '812#stock_only2', '813#count_composition', '814#source_wise', '815#cc_wise', '23#search10', '23#search10', '906#count_composition_lot'];
		$print_report_format_arr = get_report_button_array($data, 6, 36, $user_id, $buttonIdArr);
		exit();
	}

	if ($action == "composition_popup") {
		echo load_html_head_contents("Composition Info", "../../../../", 1, 1, '', '1', '');
		extract($_REQUEST);
		?>
			<script>
				var selected_id = new Array();
				var selected_name = new Array();

				function check_all_data() {
					var tbl_row_count = document.getElementById('table_body').rows.length;

					tbl_row_count = tbl_row_count - 1;
					for (var i = 1; i <= tbl_row_count; i++) {
						js_set_value(i);
					}
				}

				function toggle(x, origColor) {
					var newColor = 'yellow';
					if (x.style) {
						x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
					}
				}

				function set_all() {
					var old = document.getElementById('txt_pre_composition_row_id').value;
					if (old != "") {
						old = old.split(",");
						for (var k = 0; k < old.length; k++) {
							js_set_value(old[k])
						}
					}
				}

				function js_set_value(str) {

					toggle(document.getElementById('search' + str), '#FFFFCC');

					if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
						selected_id.push($('#txt_individual_id' + str).val());
						selected_name.push($('#txt_individual' + str).val());

					} else {
						for (var i = 0; i < selected_id.length; i++) {
							if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
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
								<? echo create_drop_down("cbo_string_search_type", 150, $string_search_type, '', 1, "-- Searching Type --"); ?>
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

						$result = sql_select("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name");
						$pre_composition_id_arr = explode(",", $pre_composition_id);
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";


							if (in_array($row[csf("id")], $pre_composition_id_arr)) {
								if ($pre_composition_ids == "") $pre_composition_ids = $i;
								else $pre_composition_ids .= "," . $i;
							}
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>">
								<td width="50">
									<? echo $i; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>" />
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("composition_name")]; ?>" />
								</td>
								<td width="">
									<p><? echo $row[csf("composition_name")]; ?></p>
								</td>
							</tr>
						<?
							$i++;
						}
						?>
						<input type="hidden" name="txt_pre_composition_row_id" id="txt_pre_composition_row_id" value="<?php echo $pre_composition_ids; ?>" />
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
				setFilterGrid('table_body', -1);
				set_all();
			</script>
		<?
	}

	if ($action == "supplier_popup") {
		echo load_html_head_contents("Supplier Info", "../../../../", 1, 1, '', '1', '');
		extract($_REQUEST);
		?>
			<script>
				var selected_id = new Array();
				var selected_name = new Array();

				function check_all_data() {
					var tbl_row_count = document.getElementById('table_body').rows.length;

					tbl_row_count = tbl_row_count - 1;
					for (var i = 1; i <= tbl_row_count; i++) {
						js_set_value(i);
					}
				}

				function toggle(x, origColor) {
					var newColor = 'yellow';
					if (x.style) {
						x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
					}
				}

				function js_set_value(str) {

					toggle(document.getElementById('search' + str), '#FFFFCC');

					if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
						selected_id.push($('#txt_individual_id' + str).val());
						selected_name.push($('#txt_individual' + str).val());

					} else {
						for (var i = 0; i < selected_id.length; i++) {
							if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
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

					$('#hidden_supplier_id').val(id);
					$('#hidden_supplier').val(name);
				}
			</script>
			</head>
			<fieldset style="width:390px">

				<input type="hidden" name="hidden_supplier" id="hidden_supplier" value="">
				<input type="hidden" name="hidden_supplier_id" id="hidden_supplier_id" value="">
				<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th width="50">SL</th>
							<th width="">Supplier Name</th>
						</tr>
					</thead>
				</table>
				<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
					<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?

						if ($companyID) {
							$companyCon = " and a.tag_company='$companyID'";
						} else {
							$companyCon = "";
						}

						$result = sql_select("select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $companyCon and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name");
						$i = 1;
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>">
								<td width="50">
									<? echo $i; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>" />
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("supplier_name")]; ?>" />
								</td>
								<td width="">
									<p><? echo $row[csf("supplier_name")]; ?></p>
								</td>
							</tr>
						<?
							$i++;
						}
						?>

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
				setFilterGrid('table_body', -1);
			</script>
		<?
	}

	if ($action == "yarn_type_popup") {
		echo load_html_head_contents("Yarn Type Info", "../../../../", 1, 1, '', '1', '');
		extract($_REQUEST);
		?>
			<script>
				var selected_id = new Array();
				var selected_name = new Array();

				function check_all_data() {
					var tbl_row_count = document.getElementById('table_body').rows.length;

					tbl_row_count = tbl_row_count - 1;
					for (var i = 1; i <= tbl_row_count; i++) {
						js_set_value(i);
					}
				}

				function toggle(x, origColor) {
					var newColor = 'yellow';
					if (x.style) {
						x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
					}
				}

				function js_set_value(str) {

					toggle(document.getElementById('search' + str), '#FFFFCC');

					if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
						selected_id.push($('#txt_individual_id' + str).val());
						selected_name.push($('#txt_individual' + str).val());

					} else {
						for (var i = 0; i < selected_id.length; i++) {
							if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
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

					$('#hidden_yarn_type_id').val(id);
					$('#hidden_yarn_type').val(name);
				}
			</script>
			</head>
			<fieldset style="width:390px">
				<input type="hidden" name="hidden_yarn_type" id="hidden_yarn_type" value="">
				<input type="hidden" name="hidden_yarn_type_id" id="hidden_yarn_type_id" value="">
				<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th width="50">SL</th>
							<th width="">Yarn Type Name</th>
						</tr>
					</thead>
				</table>
				<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
					<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$i = 1;
						foreach ($yarn_type as $key => $val) {
							//var_dump($val);
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>">
								<td width="50">
									<? echo $i; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $key; ?>" />
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $val; ?>" />
								</td>
								<td width="">
									<p><? echo $val; ?></p>
								</td>
							</tr>
						<?
							$i++;
						}
						?>

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
				setFilterGrid('table_body', -1);
			</script>
		<?
	}

	if ($action == "yarn_count_popup") {
		echo load_html_head_contents("Yarn Count Info", "../../../../", 1, 1, '', '1', '');
		extract($_REQUEST);
		?>
			<script>
				var selected_id = new Array();
				var selected_name = new Array();

				function check_all_data() {
					var tbl_row_count = document.getElementById('table_body').rows.length;

					tbl_row_count = tbl_row_count - 1;
					for (var i = 1; i <= tbl_row_count; i++) {
						js_set_value(i);
					}
				}

				function toggle(x, origColor) {
					var newColor = 'yellow';
					if (x.style) {
						x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
					}
				}

				function js_set_value(str) {

					toggle(document.getElementById('search' + str), '#FFFFCC');

					if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
						selected_id.push($('#txt_individual_id' + str).val());
						selected_name.push($('#txt_individual' + str).val());

					} else {
						for (var i = 0; i < selected_id.length; i++) {
							if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
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

					$('#hidden_yarn_count_id').val(id);
					$('#hidden_yarn_count').val(name);
				}
			</script>
			</head>
			<fieldset style="width:390px">

				<input type="hidden" name="hidden_yarn_count" id="hidden_yarn_count" value="">
				<input type="hidden" name="hidden_yarn_count_id" id="hidden_yarn_count_id" value="">
				<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th width="50">SL</th>
							<th width="">Yarn Count Name</th>
						</tr>
					</thead>
				</table>
				<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
					<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$result = sql_select("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count");
						$i = 1;
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>">
								<td width="50">
									<? echo $i; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>" />
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("yarn_count")]; ?>" />
								</td>
								<td width="">
									<p><? echo $row[csf("yarn_count")]; ?></p>
								</td>
							</tr>
						<?
							$i++;
						}
						?>

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
				setFilterGrid('table_body', -1);
			</script>
		<?
	}

	if ($action == "yarn_color_popup") {
		echo load_html_head_contents("Yarn Color Info", "../../../../", 1, 1, '', '1', '');
		extract($_REQUEST);
		?>
			<script>
				var selected_id = new Array();
				var selected_name = new Array();

				function check_all_data() {
					var tbl_row_count = document.getElementById('table_body').rows.length;

					tbl_row_count = tbl_row_count - 1;
					for (var i = 1; i <= tbl_row_count; i++) {
						js_set_value(i);
					}
				}

				function toggle(x, origColor) {
					var newColor = 'yellow';
					if (x.style) {
						x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
					}
				}

				function js_set_value(str) {

					toggle(document.getElementById('search' + str), '#FFFFCC');

					if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
						selected_id.push($('#txt_individual_id' + str).val());
						selected_name.push($('#txt_individual' + str).val());

					} else {
						for (var i = 0; i < selected_id.length; i++) {
							if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
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

					$('#hidden_yarn_color_id').val(id);
					$('#hidden_yarn_color').val(name);
				}
			</script>
			</head>
			<fieldset style="width:390px">
				<input type="hidden" name="hidden_yarn_color" id="hidden_yarn_color" value="">
				<input type="hidden" name="hidden_yarn_color_id" id="hidden_yarn_color_id" value="">
				<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th width="50">SL</th>
							<th width="">Color Name</th>
						</tr>
					</thead>
				</table>
				<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
					<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?

						if ($companyID == 0)
							$com_cond .= "";
						else
							$com_cond .= " and b.company_id in($companyID)";

						$rslt_color = sql_select("SELECT a.id, a.color_name from lib_color a, product_details_master b where a.id=b.color $com_cond and b.item_category_id=1 and a.status_active = 1 and a.is_deleted = 0 group by a.id, a.color_name order by a.color_name");
						$i = 1;
						foreach ($rslt_color as $val) {

							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>">
								<td width="50">
									<? echo $i; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $val[csf('id')];; ?>" />
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $val[csf('color_name')]; ?>" />
								</td>
								<td width="" title="<? echo $val[csf('id')]; ?>">
									<p><? echo $val[csf('color_name')]; ?></p>
								</td>
							</tr>
						<?
							$i++;
						}
						?>

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
				setFilterGrid('table_body', -1);
			</script>
		<?
	}

	if ($action == "get_yarn_test_file") {
		extract($_REQUEST);
		$img_sql = "select a.prod_id, b.image_location, b.master_tble_id, b.real_file_name from inv_yarn_test_mst a, common_photo_library b where a.id=b.master_tble_id and b.form_name='yarn_test' and a.prod_id='$id' and b.is_deleted=0 and b.file_type=2";
		//echo $img_sql;die;
		$img_sql_res = sql_select($img_sql);
		if (count($img_sql_res) == 0) {
			echo "<div style='text-align:center;color:red;font-size:18px;'>Image/File is not available.</div>";
			die();
		}
		foreach ($img_sql_res as $img) {
			echo '<p style="display:inline-block;word-wrap:break-word;word-break:break-all;width:115px;padding-right:10px;vertical-align:top;margin:0px;"><a href="?action=downloiadFile&file=' . urlencode($img[csf("image_location")]) . '"><img src="../../../../file_upload/blank_file.png" width="89px" height="97px"></a><br>' . $img[csf("real_file_name")] . '</p>';
		}
	}

	if ($action == "downloiadFile") {
		if (isset($_REQUEST["file"])) {
			$file = urldecode($_REQUEST["file"]); // Decode URL-encoded string

			$filepath = "../../../../" . $file;
			// Process download
			if (file_exists($filepath)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($filepath));
				flush(); // Flush system output buffer
				readfile($filepath);
				exit();
			}
		}
	}

		?>