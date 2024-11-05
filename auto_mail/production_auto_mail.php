<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../includes/common.php');
//require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');
 
$company_lib = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name",$con);
$floor_lib = return_library_array( "select id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1", "id", "floor_name");
$conversion_rate=return_field_value("conversion_rate","currency_conversion_rate","is_deleted=0 and status_active=1 and id=(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)","",$con);


	if($db_type==0)
	{
		$current_date = date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0)));
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$previous_date= date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
		$previous_3month_date = date('Y-m-d H:i:s', strtotime('-92 day', strtotime($current_date))); 
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
		$previous_3month_date = change_date_format(date('Y-m-d H:i:s', strtotime('-180 day', strtotime($current_date))),'','',1); 
	}

	
	if($db_type==0){
		$str_cond	=" and insert_date between '".$previous_date."' and '".$previous_date."'";
	}
	else
	{
		$str_cond	=" and insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}

	
	function fn_remove_zero($int,$format){
		return $int>0?number_format($int,$format):'';
		
	}





	
//Exfactory--------------------
	$ex_factory_date_con = " and b.ex_factory_date between '".$previous_date."' and '".$previous_date."'";
	$ex_factory_sql="select a.delivery_company_id,a.delivery_floor_id,sum(b.ex_factory_qnty) ex_factory_qnty  from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where b.delivery_mst_id=a.id   $ex_factory_date_con and a.is_deleted=0 and a.status_active=1 group by a.delivery_company_id,a.delivery_floor_id";
	 //echo $ex_factory_sql;die;
	
	$ex_factory_sql_result = sql_select($ex_factory_sql);
	foreach($ex_factory_sql_result as $rows)
	{
		$ex_fac_qty_arr[$rows[csf("delivery_company_id")]][$rows[csf("delivery_floor_id")]]+=$rows[csf("ex_factory_qnty")];
	} 
	//print_r($ex_fac_qty_arr);
	unset($ex_factory_sql_result);
                                
//Production ---------------------------                           
	
	$production_date_con = " and a.production_date between '".$previous_date."' and '".$previous_date."'";
	$production_sql="SELECT a.po_break_down_id,a.serving_company as company_id,a.floor_id, SUM (d.production_qnty) AS production_quantity
    FROM pro_garments_production_mst a, pro_garments_production_dtls d
    WHERE a.production_type=5 AND d.production_type=5 AND a.id = d.mst_id  AND a.is_deleted = 0 AND a.status_active = 1  AND d.is_deleted = 0 AND d.status_active = 1 $production_date_con
group by a.serving_company,a.po_break_down_id,a.floor_id order by a.floor_id desc";	
	   
	   //echo $production_sql;die;
	
	$production_sql_result = sql_select($production_sql);
	$production_qty=array();
	foreach($production_sql_result as $rows)
	{
		$production_qty['sewing_out'][$rows[csf("company_id")]][$rows[csf("floor_id")]]+=$rows[csf("production_quantity")];
	}
	unset($production_sql_result); 
	//var_dump($poly_qty_arr);
	
	/*$sql_subcon="select a.company_id,a.floor_id,sum(d.prod_qnty) as good_qnty 
from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d
where a.production_type=5 and d.production_type=5 and a.id=d.dtls_id and a.status_active=1 and a.is_deleted=0 and a.company_id =$compid $production_date_con group by a.company_id,a.floor_id";
	
	$sql_subcon_result = sql_select($sql_subcon, '', '', '', $con);
	foreach($sql_subcon_result as $rows)
	{
		$poly_qty+=$rows[csf("good_qnty")];
		$poly_qty_arr[$rows[csf("company_id")]][$rows[csf("floor_id")]]+=$rows[csf("good_qnty")];
	}
	unset($production_sql_result); */


	$html="<table border='1' rules='all'>";
	$html.="<tr><th colspan='3'>Daily Report</th></tr>";
	$html.="<tr><th colspan='3'>$current_date</th></tr>";

	foreach($production_qty['sewing_out'] as $company_id=>$floorArr){
	$html.="<tr><th colspan='3'>{$company_lib[$company_id]}</th></tr>";
	$html.="<tr bgcolor='#CCCCCC'><th rowspan='2'>Garments</th><th>Production</th><th rowspan='2'>Shipment</th></tr>";
	$html.="<tr bgcolor='#CCCCCC'><th>in Sewing</th></tr>";
		
		$total_sewing_in_qty=0;$total_ex_fac_qty=0;
		foreach($floorArr as $floor_id=>$sewing_qty){
		$html.="
			<tr>
				<td>{$floor_lib[$floor_id]}</td>
				<td align='right'>$sewing_qty</td>
				<td align='right'>{$ex_fac_qty_arr[$company_id][$floor_id]}</td>
			</tr>
			";
			$total_sewing_in_qty+=$sewing_qty;
			$total_ex_fac_qty+=$ex_fac_qty_arr[$company_id][$floor_id];
		}
		$html.="
			<tr bgcolor='#DDDDDD'>
				<td align='right'><b>Total</b></td>
				<td align='right'><b>$total_sewing_in_qty</b></td>
				<td align='right'><b>$total_ex_fac_qty</b></td>
			</tr>
			";
	}

   $html.="</table>";


$message = $html;
$message .=  "<br>";


//Yarn Stork.........................................................................start
		if ($db_type == 0)
			$select_field = "group_concat(distinct(a.store_id))";
		else if ($db_type == 2)
			$select_field = "listagg(a.store_id,',') within group (order by a.store_id)";
			
			
			$receive_array = array();
          	$sql_receive = "Select a.prod_id, $select_field as store_id, max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone,
          	sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $previous_date . "' then a.cons_quantity else 0 end) as rcv_total_opening,
          	sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $previous_date . "' then a.cons_amount else 0 end) as rcv_total_opening_amt,
          	sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $previous_date . "' then a.cons_rate else 0 end) as rcv_total_opening_rate,
          	sum(case when a.transaction_type =1 and c.receive_purpose<>5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as purchase,
          	sum(case when a.transaction_type =1 and c.receive_purpose<>5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as purchase_amt,
          	sum(case when a.transaction_type =1 and c.receive_purpose=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as rcv_loan,
          	sum(case when a.transaction_type =1 and c.receive_purpose=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as rcv_loan_amt,
          	sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as rcv_inside_return,
          	sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as rcv_inside_return_amt,
          	sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as rcv_outside_return,
          	sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as rcv_outside_return_amt 
          	from inv_transaction a, inv_receive_master c where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id in(".implode(',',array_keys($company_lib)).") $store_cond group by a.prod_id";
			
			 //echo $sql_receive;die;  
		
          	$result_sql_receive = sql_select($sql_receive);
          	foreach ($result_sql_receive as $row) {
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
          		//$receive_array[$row[csf("prod_id")]]['weight_per_bag'] = $row[csf("weight_per_bag")];
          		$receive_array[$row[csf("prod_id")]]['weight_per_cone'] = $row[csf("weight_per_cone")];
          		$receive_array[$row[csf("prod_id")]]['rcv_total_opening_rate'] = $row[csf("rcv_total_opening_rate")];

          		//$product_wgt_cone_arr[$row[csf("prod_id")]]['weight_per_bag'] = $row[csf("weight_per_bag")];
          		//$product_wgt_cone_arr[$row[csf("prod_id")]]['weight_per_cone'] = $row[csf("weight_per_cone")];
          	}

          	unset($result_sql_receive);
		
          	$issue_array = array();
          	$sql_issue = "select a.prod_id, $select_field as store_id,
          	sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $previous_date . "' then a.cons_quantity else 0 end) as issue_total_opening,
          	sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $previous_date . "' then a.cons_rate else 0 end) as issue_total_opening_rate,
          	sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $previous_date . "' then a.cons_amount else 0 end) as issue_total_opening_amt,
          	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
          	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as issue_inside,
          	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
          	sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as issue_outside,
          	sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as issue_outside_amt,
          	sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as rcv_return,
          	sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as rcv_return_amt,
          	sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as issue_loan,
          	sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as issue_loan_amt			
          	from inv_transaction a, inv_issue_master c
          	where a.mst_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond group by a.prod_id";
			
			//echo $sql_issue;die; 
			
          	$result_sql_issue = sql_select($sql_issue);
          	foreach ($result_sql_issue as $row) {
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
		
		
         
          	
			$trans_criteria_cond = " and c.transfer_criteria=1";
			$transfer_qty_array = array();
          	

          	$sql_transfer = "select a.prod_id, 
          	sum(case when a.transaction_type=6 and a.transaction_date<'" . $previous_date . "' then a.cons_quantity else 0 end) as trans_out_total_opening,
          	sum(case when a.transaction_type=6 and a.transaction_date<'" . $previous_date . "' then a.cons_amount else 0 end) as trans_out_total_opening_amt,
          	sum(case when a.transaction_type=6 and a.transaction_date<'" . $previous_date . "' then a.cons_rate else 0 end) as trans_out_total_opening_rate,
          	sum(case when a.transaction_type=5 and a.transaction_date<'" . $previous_date . "' then a.cons_quantity else 0 end) as trans_in_total_opening,
          	sum(case when a.transaction_type=5 and a.transaction_date<'" . $previous_date . "' then a.cons_rate else 0 end) as trans_in_total_opening_rate,
          	sum(case when a.transaction_type=5 and a.transaction_date<'" . $previous_date . "' then a.cons_amount else 0 end) as trans_in_total_opening_amt,
          	sum(case when a.transaction_type=6 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as transfer_out_qty,
          	sum(case when a.transaction_type=6 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as transfer_out_amt,
          	sum(case when a.transaction_type=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as transfer_in_qty,
          	sum(case when a.transaction_type=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as transfer_in_amt 
          	from inv_transaction a left join inv_item_transfer_dtls d on a.mst_id = d.mst_id and d.status_active= 1 and d.is_deleted = 0 and a.prod_id = d.to_prod_id and d.item_category = 1, inv_item_transfer_mst c where a.mst_id=c.id and a.transaction_type in (5,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1  and c.is_deleted=0 $trans_criteria_cond $store_cond group by a.prod_id";
	//echo $sql_transfer;die; 
		
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
          		//$product_wgt_cone_arr[$transRow[csf("prod_id")]]["weight_per_bag"] = $transRow[csf("weight_per_bag")];
          	}

          	unset($result_sql_transfer);
		
		$sql = "select a.id, a.avg_rate_per_unit 
		from product_details_master a
		where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.company_id in(".implode(',',array_keys($company_lib)).") $search_cond group by a.id, a.avg_rate_per_unit";
		//echo $sql;die;
			
			$result = sql_select($sql);
			foreach ($result as $row) 
			{
				$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
				$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

				$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
				$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

				$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);

				$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
				$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;

				$stockInHand = $openingBalance + $totalRcv - $totalIssue;
				$stock_value = 0;
				$stock_value = $stockInHand * $row[csf("avg_rate_per_unit")];
				$stock_value_usd = ($stockInHand * $row[csf("avg_rate_per_unit")]) / $conversion_rate;

				
				$stock+=$stockInHand;
				$valueUsd+=$stock_value_usd;
			}

//Yarn Stork.........................................................................end

//Greay Febric Stock Qty.............................................................start;
	$date_to=date("d-M-Y",strtotime($previous_date));
	$date_frm=date('Y-m-d',strtotime($previous_date));
	
	if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
	else if($db_type==2) $end_date=change_date_format($date_to,"","",1);
	$date_cond=" and e.transaction_date <= '$end_date'";
	
	$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
	$sql = "SELECT sum(b.qnty) as receive_qty,b.po_breakdown_id as po_id, c.prod_id,c.febric_description_id,c.gsm,c.color_range_id, c.width,d.company_id,d.buyer_id, d.style_ref_no, d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id as lc_company_id,d.po_buyer,d.po_job_no,d.booking_without_order,d.booking_type,d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no
	from inv_receive_master a,inv_transaction e,pro_grey_prod_entry_dtls c,pro_roll_details b,fabric_sales_order_mst d
	where a.id=e.mst_id and e.id=c.trans_id and c.id=b.dtls_id and b.po_breakdown_id=d.id $within_group_cond and b.entry_form in(58,2) and c.trans_id>0 and a.receive_basis in(2,4,10) and a.item_category=13 and d.company_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=1 $date_cond
	group by b.po_breakdown_id,c.prod_id,c.febric_description_id,c.gsm,c.color_range_id,c.width,d.company_id,d.buyer_id, d.style_ref_no,d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id, d.po_buyer,d.po_job_no,d.booking_without_order, d.booking_type, d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no";
	 //echo $sql;die;
	$masterData=sql_select($sql);

	if(empty($masterData))
	{
		/* If sales order data not found in receive then this part will check for transfer in data*/
		$trans_in_row = sql_select("SELECT a.company_id,a.to_order_id as po_id,b.from_prod_id as prod_id, e.color_range,d.sales_booking_no, d.buyer_id, d.style_ref_no, d.within_group, d.job_no, d.po_company_id as lc_company_id,d.po_buyer, d.po_job_no, d.booking_without_order, d.booking_type, d.booking_entry_form , c.detarmination_id,c.gsm
			from inv_item_transfer_mst a,inv_item_transfer_dtls b left join ppl_planning_info_entry_dtls e on b.to_program = e.id, fabric_sales_order_mst d , product_details_master c
			where a.id=b.mst_id and a.to_order_id=d.id and b.from_prod_id = c.id and a.status_active=1 and b.status_active=1 and d.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 and a.company_id = $company_name $order_no_cond $booking_no_cond $date_cond
			group by a.company_id,a.to_order_id,b.from_prod_id, e.color_range, d.sales_booking_no, d.buyer_id, d.style_ref_no, d.within_group, d.job_no,d.po_company_id, d.po_buyer,d.po_job_no, d.booking_without_order, d.booking_type,d.booking_entry_form,c.detarmination_id,c.gsm");

		foreach($trans_in_row as $row)
		{
			$poids .= $row[csf("po_id")].",";
			$prod_id .= $row[csf("prod_id")].",";

			$salesData[$row[csf("po_id")]]['working_company_id'] = $row[csf("company_id")];
			$salesData[$row[csf("po_id")]]['booking_no'] = $row[csf("sales_booking_no")];
			$salesData[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
			$salesData[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
			$salesData_color_range[$row[csf("po_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]]['color_range_id'] .= $row[csf("color_range")].",";
			$salesData[$row[csf("po_id")]]['lc_company_id'] = $row[csf("lc_company_id")];
			$salesData[$row[csf("po_id")]]['fso_no'] = $row[csf("job_no")];

			// within group yes
			if($row[csf("within_group")]==1)
			{
				$salesData[$row[csf("po_id")]]['buyer_id'] = $row[csf("po_buyer")];
				$salesData[$row[csf("po_id")]]['job_no'] = $row[csf("po_job_no")];
			} else {
				$salesData[$row[csf("po_id")]]['buyer_id'] = $row[csf("buyer_id")];
				$salesData[$row[csf("po_id")]]['job_no'] = "";
			}

			if($row[csf('booking_type')] == 4)
			{
				if($row[csf('booking_without_order')] == 1)
				{
					$bookingType = "Sample Without Order";
				}
				else
				{
					$bookingType =  "Sample With Order";
				}
			}
			else
			{
				$bookingType =  $booking_type_arr[$row[csf('booking_entry_form')]];
			}
			$salesData[$row[csf("po_id")]]['booking_type'] = $bookingType;
		}
		unset($trans_in_row);

	}
	else
	{
		$prodWiseSalesDataStatus = $prodWiseOpening=array();
		foreach($masterData as $row)
		{
			$poids .= $row[csf("po_id")].",";
			$prod_id .= $row[csf("prod_id")].",";
			$all_po_arr[$row[csf('po_id')]] = $row[csf('po_id')];
			$determinationids .= ",".$row[csf('febric_description_id')];
			$receive_barcodes[$row[csf('barcode_no')]] = $row[csf('barcode_no')];

			$salesData[$row[csf("po_id")]]['booking_id'] = $row[csf("booking_id")];
			$salesData[$row[csf("po_id")]]['working_company_id'] = $row[csf("company_id")];
			$salesData[$row[csf("po_id")]]['booking_no'] = $row[csf("sales_booking_no")];
			$salesData[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
			$salesData[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
			$salesData[$row[csf("po_id")]]['lc_company_id'] = $row[csf("lc_company_id")];
			$salesData[$row[csf("po_id")]]['fso_no'] = $row[csf("job_no")];

			// within group yes
			if($row[csf("within_group")]==1)
			{
				$salesData[$row[csf("po_id")]]['buyer_id'] = $row[csf("po_buyer")];
				$salesData[$row[csf("po_id")]]['job_no'] = $row[csf("po_job_no")];
			} else {
				$salesData[$row[csf("po_id")]]['buyer_id'] = $row[csf("buyer_id")];
				$salesData[$row[csf("po_id")]]['job_no'] = "";
			}

			if($row[csf('booking_type')] == 4)
			{
				if($row[csf('booking_without_order')] == 1)
				{
					$bookingType = "Sample Without Order";
				}
				else
				{
					$bookingType =  "Sample With Order";
				}
			}
			else
			{
				$bookingType =  $booking_type_arr[$row[csf('booking_entry_form')]];
			}
			$salesData[$row[csf("po_id")]]['booking_type'] = $bookingType;

			
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
			if($row[csf("color_range_id")]!=""){
				if($transaction_date >= $date_frm){
					$prodWiseSalesDataStatus[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_range_id")]] .= $row[csf('febric_description_id')]."*".$row[csf('gsm')]."*".$row[csf('width')]."*".$row[csf("receive_qty")]."*".$row[csf("store_id")]."*1*1_";
				}else{
					if($transaction_date < $date_frm){
						$prodWiseSalesDataStatus[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_range_id")]] .= $row[csf('febric_description_id')]."*".$row[csf('gsm')]."*".$row[csf('width')]."*".$row[csf("receive_qty")]."*".$row[csf("store_id")]."*1*2_";
						$receiveOpening[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_range_id")]] += $row[csf("receive_qty")];
					}
				}
			}
		}
	}

	$trans_in_sql = "SELECT a.from_order_id,a.to_order_id,e.transaction_date,e.store_id,b.from_prod_id,c.dia_width,c.gsm,c.detarmination_id,sum(d.qnty) as transfer_in_qnty,d.barcode_no,f.company_id,f.buyer_id,f.style_ref_no, f.job_no, f.job_no_prefix_num, f.sales_booking_no, f.booking_id,f.within_group,f.po_company_id as lc_company_id,f.po_buyer,f.po_job_no,f.booking_without_order,f.booking_type,f.booking_entry_form
	from inv_item_transfer_mst a,inv_transaction e,inv_item_transfer_dtls b,product_details_master c,pro_roll_details d,fabric_sales_order_mst f
	where a.entry_form=133 and a.status_active=1 and a.transfer_criteria=4 and a.id=e.mst_id and e.id=b.to_trans_id and b.from_prod_id=c.id and b.id=d.dtls_id and d.po_breakdown_id=f.id and b.status_active=1 $toOrderIdCond $date_cond $store_cond $pocompany_cond2 $booking_no_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2 and d.entry_form=133 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
	group by a.from_order_id,a.to_order_id,e.transaction_date,e.store_id,b.from_prod_id,c.dia_width,c.gsm,c.detarmination_id,d.barcode_no,f.company_id,f.buyer_id,f.style_ref_no, f.job_no, f.job_no_prefix_num, f.sales_booking_no, f.booking_id,f.within_group,f.po_company_id,f.po_buyer,f.po_job_no,f.booking_without_order,f.booking_type,f.booking_entry_form";
	//echo $trans_in_sql; die;
	
	$trans_in_data = sql_select($trans_in_sql);
	foreach($trans_in_data as $row)
	{
		$poids .= $row[csf("to_order_id")].",";
		$salesData[$row[csf("to_order_id")]]['booking_id'] = $row[csf("booking_id")];
		$salesData[$row[csf("to_order_id")]]['working_company_id'] = $row[csf("company_id")];
		$salesData[$row[csf("to_order_id")]]['booking_no'] = $row[csf("sales_booking_no")];
		$salesData[$row[csf("to_order_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
		$salesData[$row[csf("to_order_id")]]['within_group'] = $row[csf("within_group")];
		$salesData[$row[csf("to_order_id")]]['lc_company_id'] = $row[csf("lc_company_id")];
		$salesData[$row[csf("to_order_id")]]['fso_no'] = $row[csf("job_no")];

		// within group yes
		if($row[csf("within_group")]==1)
		{
			$salesData[$row[csf("to_order_id")]]['buyer_id'] = $row[csf("po_buyer")];
			$salesData[$row[csf("to_order_id")]]['job_no'] = $row[csf("po_job_no")];
		} else {
			$salesData[$row[csf("to_order_id")]]['buyer_id'] = $row[csf("buyer_id")];
			$salesData[$row[csf("to_order_id")]]['job_no'] = "";
		}

		if($row[csf('booking_type')] == 4)
		{
			if($row[csf('booking_without_order')] == 1)
			{
				$bookingType = "Sample Without Order";
			}
			else
			{
				$bookingType = "Sample With Order";
			}
		}
		else
		{
			$bookingType = $booking_type_arr[$row[csf('booking_entry_form')]];
		}

		$salesData[$row[csf("to_order_id")]]['booking_type'] = $bookingType;
	}

	$determinationids = implode(",", array_filter(array_unique(explode(",",chop($determinationids,",")))));
	$determinationidArr=explode(",",$determinationids);

	if($db_type==2 && count($determinationidArr)>999)
	{
		$determinationidsArr=array_chunk($determinationidArr, 999);
		$determinationid_cond=" and (";
		foreach ($determinationidsArr as $value)
		{
			$determinationid_cond .="a.id in (".implode(",", $value).") or ";
		}
		$determinationid_cond=chop($determinationid_cond,"or ");
		$determinationid_cond.=")";
	}
	else
	{
		$determinationid_cond=" and a.id in (".implode(",", $determinationidArr).")";
	}

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";
	//echo $sql_deter;die;
	$data_array=sql_select($sql_deter);

	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$row[csf('construction')];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
		}
	}

	if($within_group==1)
	{
		$booking_year_condition="";
		if ($db_type == 0)
		{

			if($cbo_year>0)
			{
				$booking_year_condition=" and YEAR(a.insert_date)=$cbo_year";
			}
		}
		else if ($db_type == 2)
		{
			if($cbo_year>0)
			{
				$booking_year_condition=" and to_char(a.insert_date,'YYYY')=$cbo_year";
			}
		}
	}

	$poids = implode(",", array_filter(array_unique(explode(",",chop($poids,",")))));
	$poids_arr=explode(",",$poids);

	if($db_type==2 && count($poids_arr)>999)
	{
		$poids_chunk=array_chunk($poids_arr,999) ;
		$salse_id_cond = " and (";
		$trans_po_id_cond = " and (";
		$po_cond=" and (";
		$toOrderIdCond = " and (";
		$fromOrderIdCond = " and (";
		$ProductionCond = " and (";

		foreach($poids_chunk as $chunk_arr)
		{
			$po_cond.=" d.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
			$trans_po_id_cond.=" c.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
			$salse_id_cond.=" a.id in(".implode(",",$chunk_arr).") or ";
			$toOrderIdCond.=" a.to_order_id in(".implode(",",$chunk_arr).") or ";
			$fromOrderIdCond.=" a.from_order_id in(".implode(",",$chunk_arr).") or ";
			$ProductionCond.=" b.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
		}

		$fromOrderIdCond =chop($fromOrderIdCond,"or ");
		$toOrderIdCond =chop($toOrderIdCond,"or ");
		$salse_id_cond=chop($salse_id_cond,"or ");
		$po_cond=chop($po_cond,"or ");
		$trans_po_id_cond=chop($trans_po_id_cond,"or ");
		$ProductionCond=chop($ProductionCond,"or ");

		$fromOrderIdCond .=")";
		$toOrderIdCond .=")";
		$salse_id_cond.=")";
		$po_cond.=")";
		$trans_po_id_cond.=")";
		$ProductionCond.=")";
	}
	else
	{
		$fromOrderIdCond=" and a.from_order_id in($poids)";
		$toOrderIdCond=" and a.to_order_id in($poids)";
		$salse_id_cond=" and a.id in($poids)";
		$po_cond=" and d.po_breakdown_id in($poids)";
		$trans_po_id_cond=" and c.po_breakdown_id in($poids)";
		$ProductionCond=" and b.po_breakdown_id in($poids)";
	}

	// add salses id in where clause
	if($salse_id_cond!="")
	{
		$salesSql ="SELECT a.id,sum(b.grey_qty) as fso_qty, sum(b.finish_qty) as booking_qty,a.po_job_no
		from fabric_sales_order_mst a,fabric_sales_order_dtls b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $salse_id_cond
		group by a.id,a.company_id,a.buyer_id, a.style_ref_no, a.job_no, a.job_no_prefix_num, a.sales_booking_no, a.booking_id,a.within_group,a.po_job_no";

		$sales_result = sql_select($salesSql);

		foreach ($sales_result as $row) {
			$salesData[$row[csf('id')]]['fso_qty'] = $row[csf('fso_qty')];
			$salesData[$row[csf('id')]]['booking_qty'] = $row[csf('booking_qty')];
			$po_jobs = explode(",",$row[csf('po_job_no')]);
			foreach ($po_jobs as $po_job) {
				if($po_job!=""){
					$po_job_arr[$row[csf('po_job_no')]] = "'".$po_job."'";
				}
			}

		}

		if(!empty($po_job_arr)){
			if($db_type==2 && count($po_job_arr)>999)
			{
				$job_chunk=array_chunk($po_job_arr,999) ;
				$job_cond = " (";

				foreach($job_chunk as $chunk_arr)
				{
					$job_cond.=" job_no in(".implode(",",$chunk_arr).") or ";
				}

				$job_cond = chop($job_cond,"or ");
				$job_cond .=")";
			}
			else
			{
				$job_cond=" job_no in(".implode(",",$po_job_arr).")";
			}

			$job_sql = sql_select("SELECT job_no,product_category,product_dept,product_code,season_buyer_wise,style_description from wo_po_details_master where $job_cond and status_active!=0 and is_deleted!=1");
			foreach ($job_sql as $job_row) {
				$job_info[$job_row[csf("job_no")]]["product_category"] 	= $product_category[$job_row[csf("product_category")]];
				$job_info[$job_row[csf("job_no")]]["product_dept"] 		= $product_dept[$job_row[csf("product_dept")]] . "<br />".$job_row[csf("product_code")];
				$job_info[$job_row[csf("job_no")]]["season"] 			= $job_row[csf("season_buyer_wise")];
				$job_info[$job_row[csf("job_no")]]["style_ref_no"] 		= $job_row[csf("style_description")];
			}
		}
	}

	//echo $salesSql;die;

	
	$production_sql = sql_select("SELECT a.color_range_id,b.barcode_no,a.yarn_lot,a.yarn_count,b.po_breakdown_id,a.prod_id from pro_grey_prod_entry_dtls a,pro_roll_details b where a.trans_id=0 and a.status_active=1 and a.id=b.dtls_id and b.entry_form in(2)");
	foreach ($production_sql as $production_row) {
		$barcode_color_range[$production_row[csf("barcode_no")]] = $production_row[csf("color_range_id")];

		$yarn_info[$production_row[csf("po_breakdown_id")]][$production_row[csf("prod_id")]][$production_row[csf("color_range_id")]]["yarn_lot"] = $production_row[csf("yarn_lot")];
		$yarn_info[$production_row[csf("po_breakdown_id")]][$production_row[csf("prod_id")]][$production_row[csf("color_range_id")]]["yarn_count"] = $production_row[csf("yarn_count")];
	}

	

	if($poids!="")
	{
		$trans_out_sql = "SELECT a.from_order_id,e.transaction_date,b.from_prod_id,d.qnty as transfer_out_qnty,d.barcode_no	from inv_item_transfer_mst a,inv_transaction e,inv_item_transfer_dtls b,product_details_master c,pro_roll_details d,fabric_sales_order_mst f where a.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 and a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and b.from_prod_id=c.id and a.from_order_id=f.id and b.status_active=1 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=6 $fromOrderIdCond $date_cond $store_cond $pocompany_cond2 $booking_no_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2 and a.id=d.mst_id and d.entry_form=133 and b.id=d.dtls_id and d.status_active=1 and d.is_deleted=0";
		
		//echo $trans_out_sql;die;
		
		$trans_out_data = sql_select($trans_out_sql);

		foreach($trans_out_data as $row)
		{
			
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));

			$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
			if($transaction_date >= $date_frm){
				$transOutQnty[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$color_ranges] += $row[csf("transfer_out_qnty")];
			}else{
				if($transaction_date < $date_frm){
					$openingTransOutQnty[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$color_ranges] += $row[csf("transfer_out_qnty")];
				}
			}
		}

		$issue_sql = "SELECT d.po_breakdown_id,e.transaction_date,d.booking_no as prog_no,b.prod_id,d.barcode_no,d.qnty as issue_qty from inv_issue_master a,inv_transaction e,inv_grey_fabric_issue_dtls b,pro_roll_details d,fabric_sales_order_mst f where a.status_active=1 and a.is_deleted=0 and a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and a.item_category=13 and a.entry_form=61 and d.entry_form=61 and e.transaction_type=2	and e.status_active=1 and e.is_deleted=0 and d.po_breakdown_id=f.id  and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.is_returned<>1 $po_cond $date_cond $store_cond $pocompany_cond2 $booking_no_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2";

	//echo $issue_sql;die;
	
		$sql_iss=sql_select($issue_sql);

		$knit_issue_arr=array();
		foreach($sql_iss as $row)
		{
			
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));

			$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
			if($transaction_date >= $date_frm){
				$knit_issue_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$color_ranges]['issue_qty'] += $row[csf('issue_qty')];
			}else{
				if($transaction_date < $date_frm){
					$opening_issue[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$color_ranges]['issue_qty'] += $row[csf('issue_qty')];
				}
			}
		}

		unset($sql_iss);

		
		$sql_issue_return = sql_select("SELECT b.prod_id,e.transaction_date,d.po_breakdown_id as po_id,d.qnty as issue_return_qty, d.barcode_no from inv_receive_master a,inv_transaction e, pro_grey_prod_entry_dtls b, pro_roll_details d,fabric_sales_order_mst f where a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and a.item_category=13 and a.entry_form=84 and e.transaction_type=4	and d.entry_form=84 and a.receive_basis in(0) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.po_breakdown_id=f.id $po_cond $store_cond $pocompany_cond2 $booking_no_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2");
		$inssue_return_array=array();
		foreach($sql_issue_return as $row)
		{
			
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));

			$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
			if($transaction_date >= $date_frm){
				$inssue_return_array[$row[csf('po_id')]][$row[csf('prod_id')]][$color_ranges]['issue_return_qty'] += $row[csf('issue_return_qty')];
			}else{
				if($transaction_date < $date_frm){
					$opening_issue_return[$row[csf('po_id')]][$row[csf('prod_id')]][$color_ranges]['issue_return_qty'] += $row[csf('issue_return_qty')];
				}
			}
		}
		unset($sql_issue_return);

		foreach($trans_in_data as $row)
		{
			$prod_id .= $row[csf("from_prod_id")].",";
			
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
			$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
			if($transaction_date >= $date_frm){
				$prodWiseSalesDataStatus[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$color_ranges] .= $row[csf('detarmination_id')]."*".$row[csf('gsm')]."*".$row[csf('dia_width')]."*".$row[csf("transfer_in_qnty")]."*".$row[csf("store_id")]."*3*1*".$row[csf("from_order_id")]."_";
			}else{
				if($transaction_date < $date_frm){
					$prodWiseSalesDataStatus[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$color_ranges] .= $row[csf('detarmination_id')]."*".$row[csf('gsm')]."*".$row[csf('dia_width')]."*".$row[csf("transfer_in_qnty")]."*".$row[csf("store_id")]."*3*2*".$row[csf("from_order_id")]."_";
					$transferInOpening[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$color_ranges] += $row[csf("transfer_in_qnty")];
				}
			}
			$all_po_arr[$row[csf('to_order_id')]] = $row[csf('to_order_id')];
		}

		unset($trans_out_data);
		unset($trans_in_data);
	}

	$prodId = chop($prod_id,",");

	$prodIdArr = array_filter(array_unique(explode(",",$prodId)));
	if(count($prodIdArr)>0)
	{
		$prodId = implode(",", $prodIdArr);
		$prodCond = $all_prod_id_cond = "";

		if($db_type==2 && count($prodIdArr)>999)
		{
			$prodIdArr_chunk=array_chunk($prodIdArr,999) ;
			foreach($prodIdArr_chunk as $chunk_arr)
			{
				$prodCond.=" a.prod_id in(".implode(",",$chunk_arr).") or ";
			}
			$all_prod_id_cond.=" and (".chop($prodCond,'or ').")";
		}
		else
		{
			$all_prod_id_cond=" and a.prod_id in($prodId)";
		}
	}

	if($prodId!="")
	{
		$transaction_date_array=array();
		$sql_date="SELECT c.po_breakdown_id,a.prod_id, min(a.transaction_date) as min_date, max(a.transaction_date) as max_date
		from inv_transaction a,order_wise_pro_details c
		where a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=13
		$all_prod_id_cond $trans_po_id_cond $store_cond2 group by c.po_breakdown_id,a.prod_id";

//echo $sql_date;die;

		$sql_date_result=sql_select($sql_date);
		foreach( $sql_date_result as $row )
		{
			$transaction_date_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
			$transaction_date_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
		}
		unset($sql_date_result);
	}








	$tot_recv_qty=0;

	foreach($prodWiseSalesDataStatus as $poId=>$prodArr)
	{
		foreach ($prodArr as $prodId=>$colorRange)
		{
			$opening=$iss_qty=$trans_out_qty=0;
			foreach ($colorRange as $crange=>$row)
			{

				$yarn_lot = $yarn_info[$poId][$prodId][$crange]["yarn_lot"];
				$yarn_count = $yarn_info[$poId][$prodId][$crange]["yarn_count"];

				$all_prodData = explode("_",chop($row,"_"));
				$recv_qnty=$trans_in_qty=$opening_recv=$opening_trans=0;

				foreach ($all_prodData as $prodData) {
					$data = explode("*",$prodData);
					if($data[5] == 1){
						if($data[6] == 1){
							$recv_qnty += $data[3]*1;
						}
					/*if($data[6] == 2){
						$opening_recv += $data[3]*1;
					}*/
				}

				if($data[5] == 3){
					if($data[6] == 1){
						$trans_in_qty += $data[3]*1;
					}
					/*if($data[6] == 2){
						$opening_trans += $data[3]*1;
					}*/

					$from_order_id = $data[7];

					$yarn_lot = $yarn_info[$from_order_id][$prodId][$crange]["yarn_lot"];
					$yarn_count = $yarn_info[$from_order_id][$prodId][$crange]["yarn_count"];
				}
				$detarmination_id = $data[0];
				$store_id = $data[4];
			}


			$issue_return_qnty  = $inssue_return_array[$poId][$prodId][$crange]['issue_return_qty'];
			$iss_qty 			= $knit_issue_arr[$poId][$prodId][$crange]['issue_qty'];

			$opening_receive  = $receiveOpening[$poId][$prodId][$crange];
			$opening_trans_in = $transferInOpening[$poId][$prodId][$crange];

			$opening_title = "Receive=".number_format($opening_receive,2) ."+". number_format($opening_trans_in,2)."\nIssue=".number_format($opening_issue[$poId][$prodId][$crange]['issue_qty'],2) ."+". number_format($openingTransOutQnty[$poId][$prodId][$crange],2);

			$opening = ($opening_receive+$opening_trans_in)-($opening_issue[$poId][$prodId][$crange]['issue_qty']+$openingTransOutQnty[$poId][$prodId][$crange]);

			// roll wise $recv_ret_qty page did not developed yet
			$recv_tot_qty  = ($recv_qnty+$issue_return_qnty+$trans_in_qty);
			$trans_out_qty = $transOutQnty[$poId][$prodId][$crange];
			$iss_tot_qty   = ($iss_qty+$trans_out_qty);

			$stock_qty 	   = $opening+($recv_tot_qty-$iss_tot_qty);
			$stock_qty     = number_format($stock_qty,2,".","");

			$daysOnHand = datediff("d",change_date_format($transaction_date_array[$poId][$prodId]['max_date'],'','',1),date("Y-m-d"));
			$ageOfDays 	= datediff("d",change_date_format($transaction_date_array[$poId][$prodId]['min_date'],'','',1),date("Y-m-d"));

			$product_category 	= $job_info[$salesData[$poId]['job_no']]["product_category"];
			$product_dept 		= $job_info[$salesData[$poId]['job_no']]["product_dept"];
			$season 			= $season_arr[$job_info[$salesData[$poId]['job_no']]["season"]];
			$style_ref_no 		= $job_info[$salesData[$poId]['job_no']]["style_ref_no"];

				if($stock_qty>=0){
					$tot_opening  		+= $opening;
					$tot_recv_qty 		+= $recv_qnty;
					$tot_iss_ret_qty 	+= $issue_return_qnty;
					$tot_trans_in_qty 	+= $trans_in_qty;
					$grand_tot_recv_qty += $recv_tot_qty;

					$tot_iss_qty 		+= $iss_qty;
					$tot_rec_ret_qty 	+= $recv_ret_qty;
					$tot_trans_out_qty 	+= $trans_out_qty;
					$grand_tot_iss_qty 	+= ($iss_qty+$trans_out_qty);
					$grand_stock_qty 	+= $stock_qty;
				}
			
			}
		}
	}
//Greay Febric Stock Qty.............................................................end;



//Dyes/Chemical........................................... start;
	
	$mrr_rate_sql=sql_select("select prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction where status_active=1 and is_deleted=0 and item_category in(5,6,7,23) and transaction_type in(1,4,5)  group by prod_id");
	$mrr_rate_arr=array();
	foreach($mrr_rate_sql as $row)
	{
		$mrr_rate_arr[$row[csf("prod_id")]]=$row[csf("cons_amount")]/$row[csf("cons_quantiy")];
	}
			
	$dyes_chemical_stock_sql="select a.expire_date,a.prod_id,
	sum((case when a.transaction_type in (1,4,5) then a.cons_quantity else 0 end)-
	(case when a.transaction_type in (2,3,6) then a.cons_quantity else 0 end)) stock_qty,
	sum((case when a.transaction_type in (1,4,5) then a.cons_amount else 0 end)-
	(case when a.transaction_type in (2,3,6) then a.cons_amount else 0 end)) stock_amount
	
	from inv_transaction a , product_details_master b
	where  a.prod_id=b.id and a.item_category in(5,6,7,23) and b.company_id in(".implode(',',array_keys($company_lib)).") and b.item_category_id in (5,6,7,23)  and a.order_id=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by a.expire_date,a.prod_id";
	
	
	$dyesChemicalStockSqlResult = sql_select($dyes_chemical_stock_sql);
	$dyesChemicalStockQty=0;$dyesChemicalStockAmountUsd=0;
	foreach ($dyesChemicalStockSqlResult as $row) 
	{
		$dyesChemicalStockQty+=$row[csf('stock_qty')];
		//$dyesChemicalStockAmountUsd+=($row[csf('stock_amount')]/$conversion_rate);
		$dyesChemicalStockAmountUsd+=($row[csf('stock_qty')]*$mrr_rate_arr[$row[csf("prod_id")]])/$conversion_rate;
	}
//Dyes/Chemical........................................... end;


//Printing/Chemical...........................................start
	$print_chemical_stock_sql="select 
	sum((case when a.transaction_type in (1,4,5) then a.cons_quantity else 0 end)-
	(case when a.transaction_type in (2,3,6) then a.cons_quantity else 0 end)) stock_qty,
	sum((case when a.transaction_type in (1,4,5) then a.cons_amount else 0 end)-
	(case when a.transaction_type in (2,3,6) then a.cons_amount else 0 end)) stock_amount
	from inv_transaction a where a.item_category=22 and a.status_active=1 and a.is_deleted=0";			
	$printChemicalStockSqlResult = sql_select($print_chemical_stock_sql);
	$printChemicalStockQty=0;$printChemicalStockAmountUsd=0;
	foreach ($printChemicalStockSqlResult as $row) 
	{
		$printChemicalStockQty+=$row[csf('stock_qty')];
		$printChemicalStockAmountUsd+=($row[csf('stock_amount')]/$conversion_rate);
	}
	
//Printing/Chemical...........................................end;

//Kniting Production-------------------------------------- start;                           
	$str_cond_f	=" and a.receive_date between '".$previous_date."' and '".$previous_date."'";
	$sql_qty="select sum(c.quantity) as qtyinhouse 
	 from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,wo_po_break_down e, wo_po_details_master f where a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.receive_basis!=4 and a.knitting_source in(1,3) and a.knitting_company in(".implode(',',array_keys($company_lib)).") $str_cond_f and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
  union all
     select sum(b.PRODUCT_QNTY) as qtyinhouse  from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id  and a.product_type in(2) and b.product_type in(2) and a.company_id in(".implode(',',array_keys($company_lib)).") and a.product_date between '".$previous_date."' and '".$previous_date."'   and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1	 
	 ";//and a.ENTRY_FORM=159 
	 
	$sql_result=sql_select( $sql_qty);
	$kniting_pro_qty=0;
	foreach($sql_result as $row)
	{
		$kniting_pro_qty += $row[csf('qtyinhouse')];
	}				
	unset($sql_result);
	$knite=$kniting_pro_qty;
//Kniting Production--------------------------------------end; 

//Dyeing-------------------------------------- start  ;                         
    $dye_date_con=" and a.process_end_date between '$previous_date' and '$previous_date'";
	
	$dye_sql="select sum(b.production_qty) as production_qty,c.total_trims_weight
from  pro_fab_subprocess a, pro_fab_subprocess_dtls b,pro_batch_create_mst c
where a.id = b.mst_id and a.batch_id=c.id and a.load_unload_id = 2 and a.result=1 and b.load_unload_id=2 and b.entry_page=35 and a.status_active = 1 and b.status_active=1  and c.status_active=1 $dye_date_con  and a.company_id in(".implode(',',array_keys($company_lib)).") group by c.total_trims_weight";
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
where a.batch_id=c.id and a.load_unload_id = 2 $dye_date_con and a.company_id in(".implode(',',array_keys($company_lib)).") and a.result=1 and a.entry_form=38 and c.status_active = 1 and c.status_active=1  and c.status_active=1";
	 //echo $dye_sql;die;
	  
	$sub_dye_sql_result = sql_select($sub_dye_sql, '', '', '', $con);
	foreach($sub_dye_sql_result as $row)
	{
		$dyeing_qty+=$row[csf('batch_weight')];
	}
	unset($dye_sql_result);	
	$dyeing=$dyeing_qty;

//Dyeing--------------------------------------end ;     		
		
//Finishing (Kg/Yds)............................................... start;
		
	$finishigSql="select b.receive_qnty,b.uom from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=7 and a.item_category=2 and a.receive_basis=5 and a.knitting_company in(".implode(',',array_keys($company_lib)).") and a.receive_date between '$previous_date' and '$previous_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; //and a.recv_number = 'FTML-FFPE-18-03926'
	$finishigSqlResult = sql_select($finishigSql);
	foreach ($finishigSqlResult as $row) 
	{
		$finishQty[$row[csf('uom')]]+=$row[csf('receive_qnty')];

	}
		
		
	$sub_finishig_sql="select 12 as uom, b.product_qnty as finish_qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id  and a.product_type=4 and a.product_date between '$previous_date' and '$previous_date' and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1
"; 
	$sub_finishig_sql_result = sql_select($sub_finishig_sql);
	foreach ($sub_finishig_sql_result as $row) 
	{
		$finishQty[$row[csf('uom')]]+=$row[csf('finish_qty')];

	}

//Finishing (Kg/Yds)............................................... end;


//Fabric Delivery to Store............................................... start;

	$fabric_delivery_sql=" SELECT
         b.uom,
         SUM (d.current_delivery) AS current_delivery
    FROM inv_receive_master a,
         pro_finish_fabric_rcv_dtls b,
         pro_grey_prod_delivery_mst c,
         pro_grey_prod_delivery_dtls d
   WHERE a.company_id in(".implode(',',array_keys($company_lib)).")
         AND a.id = b.mst_id
         AND a.id = d.grey_sys_id
         AND b.id = d.sys_dtls_id
         AND c.id = d.mst_id
         AND a.entry_form IN (7, 66)
         AND d.entry_form = 54
         AND a.item_category = 2
         AND a.status_active = 1
         AND c.delevery_date BETWEEN '$previous_date' and '$previous_date'
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
//Fabric Delivery to Store............................................... end;

$html='<table cellpadding="3" cellspacing="0" border="1" rules="all">';
    $html.='<tr bgcolor="#DDD"><th>Textile</th><th>Qty/Value</th></tr>';	
    $html.='<tr><td>Yarn Stock (Kg)</td><td align="right">'.number_format($stock).'</td></tr>'; 
    $html.='<tr><td>Yarn Value (USD)</td><td align="right">'.number_format($valueUsd).'</td></tr>';
    $html.='<tr><td>Grey Stock (Kg)</td><td align="right">'.number_format($grand_stock_qty).'</td></tr>';
    $html.='<tr><td>Dyes/Chemical Stock (Kg)</td><td align="right">'.number_format($dyesChemicalStockQty).'</td></tr>';
    $html.='<tr><td>Dyes/Chemical Value (USD)</td><td align="right">'.number_format($dyesChemicalStockAmountUsd).'</td></tr>';
    $html.='<tr><td>Printing Chemical Stock (Kg)</td><td align="right">'.number_format($printChemicalStockQty).'</td></tr>';	
    $html.='<tr><td>Printing Chemical Value (USD)</td><td align="right">'.number_format($printChemicalStockAmountUsd).'</td></tr>';	
     
    $html.='<tr><td>Knitting (Kg)</td><td align="right">'.number_format($knite).'</td></tr>'; 
    $html.='<tr><td>Dyeing (Kg)</td><td align="right">'.number_format($dyeing).'</td></tr>'; 
    $html.='<tr><td>Finishing (Kg/Yds)</td><td align="right">'. number_format($finishQty[12]).' / '.number_format($finishQty[27]) .'</td></tr>';
    $html.='<tr><td> Fabric Delivery to Store (Kg/Yds)</td><td align="right">'. number_format($fabric_delivery_qty[12]).' / '.number_format($fabric_delivery_qty[27]).'</td></tr>';
$html.='</table>';

//Greay Febric Stock Qty.............................................................start;




	$mail_item=18;
	$to="";	
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=18 and b.mail_user_setup_id=c.id AND a.MAIL_TYPE=1 and a.IS_DELETED=0 and b.IS_DELETED=0 and c.IS_DELETED=0 and A.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and c.STATUS_ACTIVE=1";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
	

	$subject="Daily Production of ( Date :".date("d-m-Y", strtotime($previous_date)).")";
	$message .= $html;
	$header=mailHeader();
	//if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	if($_REQUEST['isview']==1){
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $to.$message;
	}
	else{
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}

	}

	
	//echo $message;




		
?>



