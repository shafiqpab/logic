<?
ini_set('precision', 8);
ini_set("display_errors", 0);
require_once('../../includes/common.php');
$company_library = return_library_array( "select id, company_short_name from lib_company where status_active=1 and is_deleted=0 ", "id", "company_short_name",$con);

$conversion_rate=return_field_value("conversion_rate","currency_conversion_rate","is_deleted=0 and status_active=1 and id=(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)","",$con);

$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
//$previous_date='05-Sep-2022';$current_date='05-Sep-2022';
//$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
$from_date=$previous_date;
$to_date=$previous_date;
$companyStr=implode(',',array_keys($company_library));



function get_dyes_chemical_data($from_date,$to_date){
   $cbo_company_name	="1";
   $cbo_store_name	="0";
   $cbo_item_category_id	="0";
   $item_account_id	="";
   $item_group_id	="";
   $value_with	="0";
   $get_upto	="0";
   $txt_days	="";
   $get_upto_qnty	="0";
   $txt_qnty	="";
 




   if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
   if ($cbo_item_category_id==0) $item_category_id=" and b.item_category_id in(5,6,7,23)"; else $item_category_id=" and b.item_category_id='$cbo_item_category_id'";
   if ($cbo_item_category_id==0) $item_category_cond=" and a.item_category in(5,6,7,23)"; else $item_category_cond=" and a.item_category='$cbo_item_category_id'";
   if ($item_account_id==0) $item_account=""; else $item_account=" and a.prod_id in ($item_account_id)";
   if ($item_account_id==0) $item_account_cond=""; else $item_account_cond=" and b.id in ($item_account_id)";
   if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";


   if($db_type==0)
   {
	   $from_date=change_date_format($from_date,'yyyy-mm-dd');
	   $to_date=change_date_format($to_date,'yyyy-mm-dd');
   }
   else if($db_type==2)
   {
	   $from_date=change_date_format($from_date,'','',1);
	   $to_date=change_date_format($to_date,'','',1);
   }
   else
   {
	   $from_date=""; $to_date="";
   }
   $search_cond="";

   
   $trans_sql="SELECT b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.batch_lot as LOT_NO from inv_transaction a, product_details_master b
   where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond $item_category_id $item_category_cond order by b.id ASC";
   $trnasactionData = sql_select($trans_sql);
   $data_array=array();
   foreach($trnasactionData as $row_p)
   {
	   if($row_p["TRANSACTION_TYPE"]==1)
	   {
		   if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
		   {
			   $data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
			   $data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
		   }
		   else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
		   {
			   $data_array[$row_p["PROD_ID"]]['purchase']+=$row_p["CONS_QUANTITY"];
			   $data_array[$row_p["PROD_ID"]]['purchase_amt']+=$row_p["CONS_AMOUNT"];
		   }
	   }
	   else if($row_p["TRANSACTION_TYPE"]==2)
	   {
		   if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
		   {
			   $data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
			   $data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
		   }
		   else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
		   {
			   $data_array[$row_p["PROD_ID"]]['issue']+=$row_p["CONS_QUANTITY"];
			   $data_array[$row_p["PROD_ID"]]['issue_amt']+=$row_p["CONS_AMOUNT"];
		   }
	   }
	   else if($row_p["TRANSACTION_TYPE"]==3)
	   {
		   if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
		   {
			   $data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
			   $data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
		   }
		   else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
		   {
			   $data_array[$row_p["PROD_ID"]]['receive_return']+=$row_p["CONS_QUANTITY"];
			   $data_array[$row_p["PROD_ID"]]['receive_return_amt']+=$row_p["CONS_AMOUNT"];
		   }
	   }
	   else if($row_p["TRANSACTION_TYPE"]==4)
	   {
		   if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
		   {
			   $data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
			   $data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
		   }
		   else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
		   {
			   $data_array[$row_p["PROD_ID"]]['issue_return']+=$row_p["CONS_QUANTITY"];
			   $data_array[$row_p["PROD_ID"]]['issue_return_amt']+=$row_p["CONS_AMOUNT"];
		   }
	   }
	   else if($row_p["TRANSACTION_TYPE"]==5)
	   {
		   if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
		   {
			   $data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
			   $data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
		   }
		   else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
		   {
			   $data_array[$row_p["PROD_ID"]]['item_transfer_receive']+=$row_p["CONS_QUANTITY"];
			   $data_array[$row_p["PROD_ID"]]['item_transfer_receive_amt']+=$row_p["CONS_AMOUNT"];
		   }
	   }
	   else if($row_p["TRANSACTION_TYPE"]==6)
	   {
		   if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
		   {
			   $data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
			   $data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
		   }
		   else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
		   {
			   $data_array[$row_p["PROD_ID"]]['item_transfer_issue']+=$row_p["CONS_QUANTITY"];
			   $data_array[$row_p["PROD_ID"]]['item_transfer_issue_amt']+=$row_p["CONS_AMOUNT"];
		   }
	   }
	   if($batch_lot_check[$row_p["PROD_ID"]][$row_p["LOT_NO"]]=="" && $row_p["LOT_NO"] !="")
	   {
		   $batch_lot_check[$row_p["PROD_ID"]][$row_p["LOT_NO"]]=$row_p["LOT_NO"];
		   $data_array[$row_p["PROD_ID"]]['lot_no'].=$row_p["LOT_NO"].",";
	   }
   }
   

   if ($cbo_item_category_id==0) $item_category_cond=" and item_category in(5,6,7,23)"; else $item_category_cond=" and item_category='$cbo_item_category_id'";
   $returnRes_date="select prod_id as PROD_ID, min(transaction_date) as MIN_DATE, max(transaction_date) as MAX_DATE from inv_transaction where is_deleted=0 and status_active=1 $item_category_cond group by prod_id";
   $result_returnRes_date = sql_select($returnRes_date);
   foreach($result_returnRes_date as $row)
   {
	   $date_array[$row["PROD_ID"]]['min_date']=$row["MIN_DATE"];
	   $date_array[$row["PROD_ID"]]['max_date']=$row["MAX_DATE"];
   }
   

   $wo_qty_arr=return_library_array("select b.item_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.item_category in (5,6,7,23) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_id","item_id","qty");

   $pi_qty_arr=return_library_array("select b.item_prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_prod_id","item_prod_id","qty");



		   $rcv_qnty_array=return_library_array("SELECT b.prod_id, sum(b.cons_quantity) as cons_quantity from  inv_receive_master a,  inv_transaction b where a.id=b.mst_id and a.receive_basis in(1,2) and b.transaction_type=1 and b.status_active=1 group by b.prod_id","prod_id","cons_quantity");
		   
			$sql="SELECT b.id, b.item_code, b.item_category_id,b.item_group_id,b.unit_of_measure,b.item_description,b.sub_group_name, b.current_stock,avg_rate_per_unit, b.re_order_label from product_details_master b where b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' $item_category_id $group_id $prod_cond $item_account_cond $search_cond order by b.id";
		   //echo $sql;die;
		   $result = sql_select($sql);
		   foreach($result as $row)
		   {
		

			   $ageOfDays = datediff("d",$date_array[$row[csf("id")]]['min_date'],date("Y-m-d"));
			   $daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d"));


			   $issue_qty=$data_array[$row[csf("id")]]['issue'];

			   $transfer_out_qty=$data_array[$row[csf("id")]]['item_transfer_issue'];
			   $transfer_in_qty=$data_array[$row[csf("id")]]['item_transfer_receive'];

			   $transfer_out_qty_amt=$data_array[$row[csf("id")]]['item_transfer_issue_amt'];
			   $transfer_in_qty_amt=$data_array[$row[csf("id")]]['item_transfer_receive_amt'];

			   $openingBalance = $data_array[$row[csf("id")]]['rcv_total_opening']-$data_array[$row[csf("id")]]['iss_total_opening'];
			   $openingBalanceValue = $data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];
			   if($openingBalanceValue>0 && $openingBalance>0) $openingRate=$openingBalanceValue/$openingBalance; else $openingRate=0;
			   $totalReceive = $data_array[$row[csf("id")]]['purchase']+$data_array[$row[csf("id")]]['issue_return']+$transfer_in_qty;//+$openingBalance
			   $totalIssue = $data_array[$row[csf("id")]]['issue']+$data_array[$row[csf("id")]]['receive_return']+$transfer_out_qty;

			   $totalReceive_amt = $data_array[$row[csf("id")]]['purchase_amt']+$data_array[$row[csf("id")]]['issue_return_amt']+$transfer_in_qty_amt;
			   $totalIssue_amt = $data_array[$row[csf("id")]]['issue_amt']+$data_array[$row[csf("id")]]['receive_return_amt']+$transfer_out_qty_amt;

			   $closingStock=$openingBalance+$totalReceive-$totalIssue;
			   $stockValue=$openingBalanceValue+$totalReceive_amt-$totalIssue_amt;
			   if($stockValue>0 && $closingStock>0) $avg_rate=$stockValue/$closingStock; else $avg_rate=0;
   
			   if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
			   {
				   $pipeLine_qty=$wo_qty_arr[$row[csf("id")]]+$pi_qty_arr[$row[csf("id")]]-$data_array[$row[csf("id")]]['rcv_total_wo']-$rcv_qnty_array[$row[csf("id")]];

				   if($pipeLine_qty<0) $pipeLine_qty=0;
				   if($value_with==0)
				   {
					   if(number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000 || number_format($totalReceive,3)>0.000 || number_format($totalIssue,3)>0.000)
					   {

							   $tot_closing_stock+=$closingStock;
							   $tot_stock_value+=$stockValue;  

					   }
				   }
			   }
		   }
   
	
		$retData['qty']= $tot_closing_stock; 
		$retData['val']= $tot_stock_value;
		return $retData;

}

$get_dyes_chemical_data_arr =get_dyes_chemical_data($from_date,$to_date);

//------------------------------------------------------------------------------------------------------

$filename="reference_wise_finish_stock_data/tmp/grand_stock_qty.txt";
$greyStockQty = file_get_contents($filename);

	
//Yearn stock qty val-------------------------------------- 

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
										 
 
								
										$i++;
										$stock_value_usd = ($stockInHand * $row[csf("avg_rate_per_unit")]) / $exchange_rate;
										
										$tot_stock_in_hand += $stockInHand;
										$tot_stock_value_usd += $stock_value_usd;

										 
									}
								}
								
							}
						}
				 
			 


				$yarnStockData['stock']=$tot_stock_in_hand;
				$yarnStockData['valueUsd']=$tot_stock_value_usd;
				return $yarnStockData;
 
}
 


	$yarnStockData=get_yarn_stock_data($previous_date,$previous_date);
	$stock=$yarnStockData['stock'];
	$valueUsd=$yarnStockData['valueUsd'];
  //Yearn stock qty val--------------------------------------end;    
                     
		
 //Dyeing--------------------------------------                            
 $dye_date_con=" and a.process_end_date between '$previous_date' and '$previous_date'";
	
 $dye_sql="select sum(b.production_qty) as production_qty,c.total_trims_weight
from  pro_fab_subprocess a, pro_fab_subprocess_dtls b,pro_batch_create_mst c
where a.id = b.mst_id and a.batch_id=c.id and a.load_unload_id = 2 and a.result=1 and b.load_unload_id=2 and b.entry_page=35 and a.status_active = 1 and b.status_active=1  and c.status_active=1 $dye_date_con  and a.company_id in($companyStr) group by c.total_trims_weight";
 //echo $dye_sql;die;
   
 $dyeing_qty=0;
 $dye_sql_result = sql_select($dye_sql, '', '', '', $con);
 foreach($dye_sql_result as $row)
 {
     $dyeing_qty+=($row[csf('production_qty')]+$row[csf('total_trims_weight')]);
 }
 unset($dye_sql_result);
 
 
 $sub_dye_sql="select sum(c.batch_weight) as batch_weight
from  pro_fab_subprocess a,pro_batch_create_mst c
where a.batch_id=c.id and a.load_unload_id = 2 $dye_date_con and a.company_id in($companyStr) and a.result=1 and a.entry_form=38 and c.status_active = 1 and c.status_active=1  and c.status_active=1";
  //echo $dye_sql;die;
   
 $sub_dye_sql_result = sql_select($sub_dye_sql, '', '', '', $con);
 foreach($sub_dye_sql_result as $row)
 {
     $dyeing_qty+=$row[csf('batch_weight')];
 }
 unset($dye_sql_result);	
 $dyeing=$dyeing_qty;

// $daynigSql = "SELECT SUM (b.production_qty) AS production_qty, c.total_trims_weight
// FROM pro_fab_subprocess     a,  pro_fab_subprocess_dtls b, pro_batch_create_mst   c
// WHERE     a.id = b.mst_id
// 	 AND a.batch_id = c.id
// 	 AND a.load_unload_id=2
// 	 AND a.result = 1
// 	 AND b.load_unload_id =2
// 	 AND b.entry_page = 35
// 	 AND a.status_active = 1
// 	 AND b.status_active = 1
// 	 AND c.status_active = 1
// 	 $dye_date_con
// 	 AND a.company_id IN ($companyStr)
// GROUP BY c.total_trims_weight
// union all
// SELECT SUM (c.batch_weight)   AS production_qty,0 as total_trims_weight
// FROM pro_fab_subprocess a, pro_batch_create_mst c
// WHERE     a.batch_id = c.id
//    AND a.load_unload_id = 2
//    $dye_date_con
//    AND a.company_id IN ($companyStr)
//    AND a.result = 1
//    AND a.entry_form = 38
//    AND c.status_active = 1
//    AND c.status_active = 1
//    AND c.status_active = 1";
// echo $daynigSql;die;


//Dyeing--------------------------------------end      		
     
//Kniting Production--------------------------------------                            
 $str_cond_f	=" and a.receive_date between '".$previous_date."' and '".$previous_date."'";
  $sql_qty="select sum(c.quantity) as qtyinhouse 
  from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,wo_po_break_down e, wo_po_details_master f where a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.receive_basis!=4 and a.knitting_source in(1,3) and a.knitting_company in($companyStr) $str_cond_f and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
union all
  select sum(b.PRODUCT_QNTY) as qtyinhouse  from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id  and a.product_type=2 and b.product_type=2 and a.company_id in($companyStr) and a.product_date between '".$previous_date."' and '".$current_date."'   and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1	 
  ";//and a.ENTRY_FORM=159 
  //echo $sql_qty;die;
  
 $sql_result=sql_select( $sql_qty, '', '', '', $con);
 $kniting_pro_qty=0;
 foreach($sql_result as $row)
 {
     $kniting_pro_qty += $row[csf('qtyinhouse')];
 }				
 unset($sql_result);
 $knite=$kniting_pro_qty;
//Kniting Production--------------------------------------end ;


	
//Dyes/Chemical...........................................
//Note: [Report Name: Closing Stock] path:C: inventory\reports\dyes_and_chemical_store\requires\closing_stock_report_controller.php
	
// $mrr_rate_sql=sql_select("select prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction where status_active=1 and  COMPANY_ID=1 and is_deleted=0 and item_category in(5,6,7,23) and transaction_type in(1,4,5) group by prod_id");
// $mrr_rate_arr=array();
// foreach($mrr_rate_sql as $row)
// {
//     $mrr_rate_arr[$row[csf("prod_id")]]=$row[csf("cons_amount")]/$row[csf("cons_quantiy")];
// }
        
    
    
//     $dyes_chemical_stock_sql="select b.id as prod_id,
//     sum((case when a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end)-
//     (case when a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end)) stock_qty,
//     sum((case when a.transaction_type in (1,4,5)  then a.cons_amount else 0 end)-
//     (case when a.transaction_type in (2,3,6)  then a.cons_amount else 0 end)) stock_amount
//     from inv_transaction a , product_details_master b
// 	 where a.prod_id=b.id and b.company_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and  b.is_deleted=0 and a.transaction_type in (1,2,3,4,5,6) and b.item_category_id in (5,6,7,23) and  a.item_category in (5,6,7,23) and a.order_id=0 
// 	group by b.id order by b.id";
//     //echo $dyes_chemical_stock_sql;die;
    
//     $dyesChemicalStockSqlResult = sql_select($dyes_chemical_stock_sql);
//         $dyesChemicalStockQty=0;$dyesChemicalStockAmountUsd=0;
//         foreach ($dyesChemicalStockSqlResult as $row) 
//         {
//             $dyesChemicalStockQty+=$row[csf('stock_qty')];
//             $dyesChemicalStockAmountUsd+=($row[csf('stock_amount')]);
//         }
//         $dyesChemicalStockAmountUsd=$dyesChemicalStockAmountUsd/$conversion_rate;
 
		// $get_dyes_chemical_data_arr =get_dyes_chemical_data("21-08-2022","21-08-2022");
		// $dyesChemicalStockQty = $get_dyes_chemical_data_arr['qty'];
		// $dyesChemicalStockAmountUsd = $get_dyes_chemical_data_arr['val']/$conversion_rate;


$dyesChemicalStockQty = $get_dyes_chemical_data_arr['qty'];
$dyesChemicalStockAmountUsd = $get_dyes_chemical_data_arr['val']/$conversion_rate;

//Dyes/Chemical...........................................end;



//Printing/Chemical...........................................
$print_chemical_stock_sql="select 
sum((case when a.transaction_type in (1,4,5) then a.cons_quantity else 0 end)-
(case when a.transaction_type in (2,3,6) then a.cons_quantity else 0 end)) stock_qty,
sum((case when a.transaction_type in (1,4,5) then a.cons_amount else 0 end)-
(case when a.transaction_type in (2,3,6) then a.cons_amount else 0 end)) stock_amount
from inv_transaction a where a.item_category=22 and a.status_active=1 and a.is_deleted=0  and a.TRANSACTION_DATE<= '$to_date'";			
$printChemicalStockSqlResult = sql_select($print_chemical_stock_sql);
    $printChemicalStockQty=0;$printChemicalStockAmountUsd=0;
    foreach ($printChemicalStockSqlResult as $row) 
    {
        $printChemicalStockQty+=$row[csf('stock_qty')];
        $printChemicalStockAmountUsd+=($row[csf('stock_amount')]/$conversion_rate);

    }
//Printing/Chemical...........................................end;

//Finishing (Kg/Yds)...............................................
		
$finishigSql="select b.REJECT_QTY,b.receive_qnty,b.uom from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=7 and a.item_category=2 and a.receive_basis=5 and a.knitting_company in($companyStr) and a.receive_date between '$from_date' and '$to_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; //and a.recv_number = 'FTML-FFPE-18-03926'
$finishigSqlResult = sql_select($finishigSql);
foreach ($finishigSqlResult as $row) 
{
    $finishQty[$row[csf('uom')]]+=$row[csf('receive_qnty')];
    $finishRejQty[$row[csf('uom')]]+=$row['REJECT_QTY'];

}
    

$sub_finishig_sql="select 12 as uom, b.product_qnty as finish_qty,b.REJECT_QNTY from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id  and a.product_type=4 and a.product_date between '$from_date' and '$to_date' and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1"; 
 
$sub_finishig_sql_result = sql_select($sub_finishig_sql);
foreach ($sub_finishig_sql_result as $row) 
{
    $finishQty[$row[csf('uom')]]+=$row[csf('finish_qty')];
    $finishRejQty[$row[csf('uom')]]+=$row[csf('REJECT_QNTY')];

}

//Finishing (Kg/Yds)...............................................end;


//Fabric Delivery to Store (Kg/Yds)........................................
$fabric_delivery_sql=" SELECT
         b.uom,
         SUM (d.current_delivery) AS current_delivery
    FROM inv_receive_master a,
         pro_finish_fabric_rcv_dtls b,
         pro_grey_prod_delivery_mst c,
         pro_grey_prod_delivery_dtls d
   WHERE a.company_id in($companyStr)
         AND a.id = b.mst_id
         AND a.id = d.grey_sys_id
         AND b.id = d.sys_dtls_id
         AND c.id = d.mst_id
         AND a.entry_form IN (7, 66)
         AND d.entry_form = 54
         AND a.item_category = 2
         AND a.status_active = 1
         AND c.delevery_date BETWEEN '" . $from_date . "' and '" . $to_date . "'
         AND a.is_deleted = 0
         AND b.uom != 0
         AND d.is_deleted = 0
         AND d.status_active = 1
GROUP BY  b.uom";

		$fabric_delivery_result = sql_select($fabric_delivery_sql);
        foreach ($fabric_delivery_result as $row) 
        {
            $fabric_delivery_qty[$row[csf('uom')]]+=$row[csf('current_delivery')];

        }

//Fabric Delivery to Store (Kg/Yds)........................................end;
ob_start();
?>

<table cellpadding="3" cellspacing="0" border="1" rules="all">
    <tr bgcolor="#DDD"><th>Textile</th><th>Qty/Value</th></tr>	
    <tr><td>Yarn Stock (Kg)</td><td align="right"><? echo number_format($stock);//number_format($yearnStockQty);?></td></tr> 
    <tr><td>Yarn Value (USD)</td><td align="right"><? echo number_format($valueUsd);//number_format($yearnStockVal);?></td></tr>
    <!-- <tr><td>Grey Stock (Kg)</td><td align="right">< ? echo number_format($greyStockQty);?></td></tr>	 -->
    <tr><td>Dyes/Chemical Stock (Kg)</td><td align="right"><? echo number_format($dyesChemicalStockQty);?></td></tr>	
    <tr><td>Dyes/Chemical Value (USD)</td><td align="right"><? echo number_format($dyesChemicalStockAmountUsd);?></td></tr>	
    <tr><td>Printing Chemical Stock (Kg)</td><td align="right"><? echo number_format($printChemicalStockQty);?></td></tr>	
    <tr><td title="Usd:<?=$conversion_rate;?>">Printing Chemical Value (USD)</td><td align="right"><? echo number_format($printChemicalStockAmountUsd);?></td></tr>	
     
    <tr><td>Knitting (Kg)</td><td align="right"><? echo number_format($knite);?></td></tr> 
    <tr><td>Dyeing (Kg)</td><td align="right"><? echo number_format($dyeing);?></td></tr> 
    <tr><td>Finishing (Kg/Yds)</td><td align="right"><? echo number_format($finishQty[12]).' / '.number_format($finishQty[27]);?></td></tr>
    <tr><td>Finishing Reject Qty (Kg/Yds)</td><td align="right"><? echo number_format($finishRejQty[12]).' / '.number_format($finishRejQty[27]);?></td></tr>
    
    <tr><td> Fabric Delivery to Store (Kg/Yds)</td><td align="right"><? echo number_format($fabric_delivery_qty[12]).' / '.number_format($fabric_delivery_qty[27]);?></td></tr>
</table>



<?

$html = ob_get_contents();
ob_clean();
$file_name = 'html/textile.html';
$create_file = fopen($file_name, 'w');	
fwrite($create_file,$html);
echo $html;


 ?>