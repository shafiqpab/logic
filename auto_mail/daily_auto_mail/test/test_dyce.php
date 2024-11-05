<?		
ini_set('precision', 8);
ini_set("display_errors", 0);
require_once('../../includes/common.php');

 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));


	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
	$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
	//$previous_date='12-Aug-2021';$current_date='12-Aug-2021';
	$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	$txt_date=$previous_date;
	$to_date=$previous_date;
	$companyStr=implode(',',array_keys($company_library));


$datra =get_dyes_chemical_data("21-08-2022","21-08-2022");

echo $datra['qty'];


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
	
	 
		 $retData[qty]= number_format($tot_closing_stock,3); 
		 $retData[val]= number_format($tot_stock_value,2);
		 return $retData;

 }
	

 
	?>
 