<?		
	ini_set('precision', 8);
	ini_set("display_errors", 0);
	require_once('../../includes/common.php');
 

	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
	$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);

 
	function get_yarn_stock_data($from_date,$to_date)
	{
		
 
		$cbo_company_name	="0";
		$cbo_dyed_type	="0";
		$cbo_yarn_type	="";
		$txt_count	="";
		$txt_lot_no	="";
		$store_wise	="2";
		$store_name	="0";
		$value_with	="1";
		$cbo_supplier	="";
		$show_val_column	=1;
		$get_upto	="0";
		$txt_days	="";
		$get_upto_qnty	="0";
		$txt_qnty	="";
		$type	="1";
		$txt_composition	="";
		$txt_composition_id	="";
		$lot_search_type	="0";
 
 

 
 

		$process = array(&$_POST);
		extract(check_magic_quote_gpc($process));
 
		$exchange_rate = return_field_value("conversion_rate", "(SELECT conversion_rate FROM currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 ORDER BY id DESC)", "ROWNUM = 1");
	
	
		$txt_lot_no = trim($txt_lot_no);
		$txt_composition = trim($txt_composition);

	
	 	
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
						$search_cond .= " and regexp_like (a.lot, '^".trim($txt_lot_no)."')";
	
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
						  $store_cond .= " and a.store_id = $store_name";
					  $table_width = '2900' + $value_width;
					  $colspan = '28' + $span;
	
					  $select_field = "listagg(a.store_id,',') within group (order by a.store_id)";
	
					  $store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
				  }
				  else
				  {
					  $select_field = "0";
					  $table_width = '2900' + $value_width;
					  $colspan = '29' + $span;
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
			 
				  $result_sql_receive = sql_select($sql_receive);
				  foreach ($result_sql_receive as $row)
				  {
					  $receive_array[$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
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
	
				   
					  $yarn_allo_sql = sql_select("select product_id, LISTAGG(CAST(buyer_id as VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY buyer_id) as buyer_id, LISTAGG(CAST(allocate_qnty AS VARCHAR(4000)),',') WITHIN GROUP(ORDER BY allocate_qnty) as allocate_qnty from com_balk_yarn_allocate where status_active=1 group by product_id");
				   
				  $yarn_allo_arr = array();
				  foreach ($yarn_allo_sql as $row)
				  {
					  $yarn_allo_arr[$row[csf("product_id")]]['product_id'] = $row[csf("product_id")];
					  $yarn_allo_arr[$row[csf("product_id")]]['buyer_id'] = implode(",", array_unique(explode(",", $row[csf("buyer_id")])));
					  $yarn_allo_arr[$row[csf("product_id")]]['allocate_qnty'] = implode(",", array_unique(explode(",", $row[csf("allocate_qnty")])));
				  }
	
				  unset($yarn_allo_sql);
	
			 
					$date_array = array();
					$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 group by prod_id";
					$result_returnRes_date = sql_select($returnRes_date);
					foreach ($result_returnRes_date as $row)
					{
						$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
						$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
					}
		
					$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit
					from product_details_master a
					where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
		
					$result = sql_select($sql);
					$i = 1;
				
				 
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
	
								$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
								$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;
	
								$stockInHand = $openingBalance + $totalRcv - $totalIssue;
	
								//subtotal and group-----------------------
								$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];
	
								if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0))
									{
									
									if($value_with == 1)
									{
										if (number_format($stockInHand, 2) > 0.00)
										{
											if (!in_array($check_string, $checkArr))
											{
												$checkArr[$i] = $check_string;
												if ($i > 1)
												{
													?>
													 
													<?
												
												}
											}
											?>
											 
												<?
	
												$stock_value = 0;
												if ($show_val_column == 1)
												{
													//echo $avz_rates_usd;die;
													$stock_value = $stockInHand * $row[csf("avg_rate_per_unit")];
													$stock_value_usd = ($stockInHand * $row[csf("avg_rate_per_unit")]) / $exchange_rate;
	
													$avz_rates_usd=0;
													if(number_format($stock_value_usd,2)>0 && number_format($stockInHand,2)>0) $avz_rates_usd=$stock_value_usd/$stockInHand;
	 
												}
	
											 
												?>
												 
											<?
											$i++;
	
											
											$tot_stock_in_hand += $stockInHand;
											$tot_stock_value_usd += $stock_value_usd;
	
											 
										}
									}
									
								}
							}
					 
				 


					$yarnStockData['stock']=number_format($tot_stock_in_hand,2);
					$yarnStockData['valueUsd']=$tot_stock_value_usd;
					return $yarnStockData;

 
 
			 
		 
}


$yarnStockData=get_yarn_stock_data($previous_date,$previous_date);

var_dump($yarnStockData['stock']);

	?>
 