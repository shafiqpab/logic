<!-- 
	Report Data Ref...............................................................
    Grey Stock (Kg)=>inventory/reports/grey_fabric_store/requires/closing_stock_report_controller.php

-->

<?php

	date_default_timezone_set("Asia/Dhaka");
	
	//include('../mailer/class.phpmailer.php');
	include('../includes/common.php');
	include('setting/mail_setting.php');
	
	$company_lib=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
	$floor_lib=return_library_array( "select ID, FLOOR_NAME from LIB_PROD_FLOOR where  status_active=1 and is_deleted=0", "ID", "FLOOR_NAME"  );
 	$conversion_rate=return_field_value("conversion_rate","currency_conversion_rate","is_deleted=0 and status_active=1 and id=(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)","");
	 
	
	
	$group_lib=return_library_array( "select id, GROUP_NAME from LIB_GROUP where  status_active=1 and is_deleted=0", "id", "GROUP_NAME"  );
	
	$companyStr=implode(',',array_flip($company_lib));	
	
	if($db_type==0)
	{
		$current_date = date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d",time()),0)));
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$prev_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d",time()),0))),'','',1);
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1); 
	}
	
	ob_start();
	$flag=0;
	
	
	
//Sewing.......................
	if($prev_date!=""){$sql_cond=" and a.production_date between '$prev_date' and '$prev_date'";}
	$productionSql="SELECT  a.SERVING_COMPANY,a.PRODUCTION_TYPE, a.FLOOR_ID, a.PRODUCTION_DATE, a.SEWING_LINE, a.PO_BREAK_DOWN_ID, a.ITEM_NUMBER_ID,sum(d.PRODUCTION_QNTY) as GOOD_QNTY,sum(b.SET_SMV*d.PRODUCTION_QNTY) as PRODUCE 
			from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown e
			where  a.production_type=5 and d.production_type=5 and a.id=d.mst_id and   a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.po_break_down_id=e.po_break_down_id and d.color_size_break_down_id=e.id and b.job_no=e.job_no_mst and c.id=e.po_break_down_id and  a.status_active=1 and a.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active in(1,2,3)  and e.status_active in(1,2,3) and e.is_deleted=0 $sql_cond 
			group by a.serving_company,a.production_type,a.floor_id, a.po_break_down_id,  a.production_date, a.sewing_line, a.item_number_id order by a.floor_id, a.po_break_down_id"; 
			
	//echo $productionSql;die;
	
	$productionSqlResult=sql_select($productionSql);	
	$sewing_production_po_data_arr=array();
	$all_po_id_arr=array();
	foreach($productionSqlResult as $row)
	{
		if($row[PRODUCTION_TYPE]==5){// sewing
			
			$sewing_production_po_data_arr[$row[PRODUCTION_TYPE]][$row[FLOOR_ID]]+=$row[GOOD_QNTY];
			//$sewing_production_po_produce_data_arr[$key]+=$row[csf('produce')];
		}
		//$all_po_id_arr[$row[PO_BREAK_DOWN_ID]]=$row[PO_BREAK_DOWN_ID];
	}
	
//Exfactory--------------------
	$ex_factory_date_con = " and b.ex_factory_date between '".$prev_date."' and '".$prev_date."'";
	 
	$exFactorySql="select a.DELIVERY_COMPANY_ID,a.DELIVERY_FLOOR_ID,sum(b.ex_factory_qnty) EX_FACTORY_QNTY  from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where b.delivery_mst_id=a.id $ex_factory_date_con and a.is_deleted=0 and a.status_active=1 group by a.delivery_company_id,a.delivery_floor_id";
	
	$exFactorySqlResult = sql_select($exFactorySql);
	$ex_fac_qty_arr=array();
	foreach($exFactorySqlResult as $rows)
	{
		$ex_fac_qty_arr[$rows[DELIVERY_COMPANY_ID]][$rows[DELIVERY_FLOOR_ID]]+=$rows[EX_FACTORY_QNTY];
	}
	


//Yarn...................
		if ($db_type == 0)
			$select_field = "group_concat(distinct(a.store_id))";
		else if ($db_type == 2)
			$select_field = "listagg(a.store_id,',') within group (order by a.store_id)";
			
			
			$receive_array = array();
          	$sql_receive = "Select a.prod_id, $select_field as store_id, max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone,
          	sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $prev_date . "' then a.cons_quantity else 0 end) as rcv_total_opening,
          	sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $prev_date . "' then a.cons_amount else 0 end) as rcv_total_opening_amt,
          	sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $prev_date . "' then a.cons_rate else 0 end) as rcv_total_opening_rate,
          	sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_quantity else 0 end) as purchase,
          	sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_amount else 0 end) as purchase_amt,
          	sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_quantity else 0 end) as rcv_loan,
          	sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_amount else 0 end) as rcv_loan_amt,
          	sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_quantity else 0 end) as rcv_inside_return,
          	sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_amount else 0 end) as rcv_inside_return_amt,
          	sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_quantity else 0 end) as rcv_outside_return,
          	sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_amount else 0 end) as rcv_outside_return_amt 
          	from inv_transaction a, inv_receive_master c where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.prod_id";
			
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
          	sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $prev_date . "' then a.cons_quantity else 0 end) as issue_total_opening,
          	sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $prev_date . "' then a.cons_rate else 0 end) as issue_total_opening_rate,
          	sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $prev_date . "' then a.cons_amount else 0 end) as issue_total_opening_amt,
          	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
          	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_quantity else 0 end) as issue_inside,
          	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
          	sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_quantity else 0 end) as issue_outside,
          	sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_amount else 0 end) as issue_outside_amt,
          	sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_quantity else 0 end) as rcv_return,
          	sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_amount else 0 end) as rcv_return_amt,
          	sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_quantity else 0 end) as issue_loan,
          	sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_amount else 0 end) as issue_loan_amt			
          	from inv_transaction a, inv_issue_master c
          	where a.mst_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.prod_id";
			
			 //echo $sql_issue; 
			
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
          	sum(case when a.transaction_type=6 and a.transaction_date<'" . $prev_date . "' then a.cons_quantity else 0 end) as trans_out_total_opening,
          	sum(case when a.transaction_type=6 and a.transaction_date<'" . $prev_date . "' then a.cons_amount else 0 end) as trans_out_total_opening_amt,
          	sum(case when a.transaction_type=6 and a.transaction_date<'" . $prev_date . "' then a.cons_rate else 0 end) as trans_out_total_opening_rate,
          	sum(case when a.transaction_type=5 and a.transaction_date<'" . $prev_date . "' then a.cons_quantity else 0 end) as trans_in_total_opening,
          	sum(case when a.transaction_type=5 and a.transaction_date<'" . $prev_date . "' then a.cons_rate else 0 end) as trans_in_total_opening_rate,
          	sum(case when a.transaction_type=5 and a.transaction_date<'" . $prev_date . "' then a.cons_amount else 0 end) as trans_in_total_opening_amt,
          	sum(case when a.transaction_type=6 and a.transaction_date between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_quantity else 0 end) as transfer_out_qty,
          	sum(case when a.transaction_type=6 and a.transaction_date between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_amount else 0 end) as transfer_out_amt,
          	sum(case when a.transaction_type=5 and a.transaction_date between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_quantity else 0 end) as transfer_in_qty,
          	sum(case when a.transaction_type=5 and a.transaction_date between '" . $prev_date . "' and '" . $prev_date . "' then a.cons_amount else 0 end) as transfer_in_amt 
          	from inv_transaction a left join inv_item_transfer_dtls d on a.mst_id = d.mst_id and d.status_active= 1 and d.is_deleted = 0 and a.prod_id = d.to_prod_id and d.item_category = 1, inv_item_transfer_mst c where a.mst_id=c.id and a.transaction_type in (5,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1  and c.is_deleted=0 group by a.prod_id";

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
          		//$product_wgt_cone_arr[$transRow[csf("prod_id")]]["weight_per_bag"] = $transRow[csf("weight_per_bag")];
          	}

          	unset($result_sql_transfer);
		
		$sql = "select a.id, a.avg_rate_per_unit 
		from product_details_master a
		where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0  $search_cond group by a.id, a.avg_rate_per_unit";
		

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

				
				$yarnStock+=$stockInHand;
				$valueUsd+=$stock_value_usd;
			}





//Greay Febric Stock Qty.............................................................start;
	
 		
$data_array=array();
        $sql_qury="SELECT b.id as id,b.detarmination_id, b.gsm, b.dia_width,
        sum(case when a.transaction_type=1 and a.transaction_date<'".$prev_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
        sum(case when a.transaction_type=2 and a.transaction_date<'".$prev_date."' then a.cons_quantity else 0 end) as iss_total_opening,
        sum(case when a.transaction_type=3 and a.transaction_date<'".$prev_date."' then a.cons_quantity else 0 end) as rcv_return_opening,
        sum(case when a.transaction_type=4 and a.transaction_date<'".$prev_date."' then a.cons_quantity else 0 end) as iss_return_opening,
        sum(case when a.transaction_type=5 and a.transaction_date<'".$prev_date."' then a.cons_quantity else 0 end) as transfer_in_opening,
        sum(case when a.transaction_type=6 and a.transaction_date<'".$prev_date."' then a.cons_quantity else 0 end) as transfer_out_opening,
        sum(case when a.transaction_type in (1,4,5) and a.transaction_date<'".$prev_date."' then a.cons_amount else 0 end) as total_rcv_value_opening,
        sum(case when a.transaction_type in (2,3,6) and a.transaction_date<'".$prev_date."' then a.cons_amount else 0 end) as total_issue_value_opening,
        sum(case when a.transaction_type=1 and a.transaction_date between '".$prev_date."' and '".$current_date."' then a.cons_quantity else 0 end) as receive,
        sum(case when a.transaction_type=2 and a.transaction_date between '".$prev_date."' and '".$current_date."' then a.cons_quantity else 0 end) as issue,
        sum(case when a.transaction_type=3 and a.transaction_date between '".$prev_date."' and '".$current_date."' then a.cons_quantity else 0 end) as rec_return,
        sum(case when a.transaction_type=4 and a.transaction_date between '".$prev_date."' and '".$current_date."' then a.cons_quantity else 0 end) as issue_return,
        sum(case when a.transaction_type=5 and a.transaction_date between '".$prev_date."' and '".$current_date."' then a.cons_quantity else 0 end) as transfer_in,
        sum(case when a.transaction_type=6 and a.transaction_date between '".$prev_date."' and '".$current_date."' then a.cons_quantity else 0 end) as transfer_out,
        sum(case when a.transaction_type in (1,4,5) and a.transaction_date between '".$prev_date."' and '".$current_date."' then a.cons_amount else 0 end) as total_rcv_value,
        sum(case when a.transaction_type in (2,3,6) and a.transaction_date between '".$prev_date."' and '".$current_date."' then a.cons_amount else 0 end) as total_issue_value
        from inv_transaction a, product_details_master b
        where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 AND a.item_category = 13
        group by b.id, b.detarmination_id, b.gsm, b.dia_width

        union all

        SELECT   c.id as id,c.detarmination_id, c.gsm, c.dia_width,
        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$prev_date."' then b.transfer_qnty else 0 end) as rcv_total_opening,
        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$prev_date."' then b.transfer_qnty else 0 end) as iss_total_opening,
        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$prev_date."' then b.transfer_qnty else 0 end) as rcv_return_opening,
        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$prev_date."' then b.transfer_qnty else 0 end) as iss_return_opening,
        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$prev_date."' then b.transfer_qnty else 0 end) as transfer_in_opening,
        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$prev_date."' then b.transfer_qnty else 0 end) as transfer_out_opening,
        sum(case when a.transfer_criteria in (2) and a.transfer_date<'".$prev_date."' then b.transfer_qnty else 0 end) as total_rcv_value_opening,
        sum(case when a.transfer_criteria in (2) and a.transfer_date<'".$prev_date."' then b.transfer_qnty else 0 end) as total_issue_value_opening,
        sum(case when a.transfer_criteria=2 and a.transfer_date between '".$prev_date."' and '".$current_date."' then b.transfer_qnty else 0 end) as receive,
        sum(case when a.transfer_criteria=2 and a.transfer_date between '".$prev_date."' and '".$current_date."' then b.transfer_qnty else 0 end) as issue,
        sum(case when a.transfer_criteria=2 and a.transfer_date between '".$prev_date."' and '".$current_date."' then b.transfer_qnty else 0 end) as rec_return,
        sum(case when a.transfer_criteria=2 and a.transfer_date between '".$prev_date."' and '".$current_date."' then b.transfer_qnty else 0 end) as issue_return,
        sum(case when a.transfer_criteria=2 and a.transfer_date between '".$prev_date."' and '".$current_date."' then b.transfer_qnty else 0 end) as transfer_in,
        sum(case when a.transfer_criteria=2 and a.transfer_date between '".$prev_date."' and '".$current_date."' then b.transfer_qnty else 0 end) as transfer_out,
        sum(case when a.transfer_criteria in (2) and a.transfer_date between '".$prev_date."' and '".$current_date."' then b.transfer_qnty else 0 end) as total_rcv_value,
        sum(case when a.transfer_criteria in (2) and a.transfer_date between '".$prev_date."' and '".$current_date."' then b.transfer_qnty else 0 end) as total_issue_value
        from inv_item_transfer_mst a,inv_item_transfer_dtls b, product_details_master c
        where a.id=b.mst_id and b.to_prod_id=c.id  AND a.item_category = 13 AND a.transfer_criteria = 2 and a.transfer_criteria=2 and a.transfer_date  between '".$prev_date."' and '".$current_date."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
        group by c.id,c.detarmination_id, c.gsm, c.dia_width  order by id ASC";

        //echo $sql_qury;die;

        $trnasactionData=sql_select($sql_qury);

        $prod_ids="";
        foreach($trnasactionData as $row)
        {
            $prod_ids[$row[csf("id")]]=$row[csf("id")];
            $data_array[$row[csf("id")]]['rcv_total_opening']=$row[csf("rcv_total_opening")];
            $data_array[$row[csf("id")]]['iss_total_opening']=$row[csf("iss_total_opening")];
            $data_array[$row[csf("id")]]['rcv_return_opening']=$row[csf("rcv_return_opening")];
            $data_array[$row[csf("id")]]['iss_return_opening']=$row[csf("iss_return_opening")];
            $data_array[$row[csf("id")]]['receive']=$row[csf("receive")];
            $data_array[$row[csf("id")]]['issue']=$row[csf("issue")];
            $data_array[$row[csf("id")]]['rec_return']=$row[csf("rec_return")];
            $data_array[$row[csf("id")]]['issue_return']=$row[csf("issue_return")];
            $data_array[$row[csf("id")]]['transfer_in']=$row[csf("transfer_in")];
            $data_array[$row[csf("id")]]['transfer_out']=$row[csf("transfer_out")];
            $data_array[$row[csf("id")]]['transfer_in_opening']=$row[csf("transfer_in_opening")];
            $data_array[$row[csf("id")]]['transfer_out_opening']=$row[csf("transfer_out_opening")];
            $data_array[$row[csf("id")]]['total_rcv_value_opening']=$row[csf("total_rcv_value_opening")];
            $data_array[$row[csf("id")]]['total_issue_value_opening']=$row[csf("total_issue_value_opening")];
            $data_array[$row[csf("id")]]['total_rcv_value']=$row[csf("total_rcv_value")];
            $data_array[$row[csf("id")]]['total_issue_value']=$row[csf("total_issue_value")];

            $data_array[$row[csf("id")]]['detarmination_id']=$row[csf("detarmination_id")];
            $data_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
            $data_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];

        }
		
		
	foreach($data_array as $prod_id=> $row)
	{

		$opening_rcv= ($row['rcv_total_opening']+$row['iss_return_opening']+$row['transfer_in_opening']);
		$opening_issue = $row['iss_total_opening']+$row['rcv_return_opening']+$row['transfer_out_opening'];
		$opening= $opening_rcv - $opening_issue;

		$opening_rate = $total_value_opening = 0;
		if($opening_rcv > 0){
		$opening_rate = $row['total_rcv_value_opening']/ $opening_rcv;
		}
		$total_value_opening = $opening *$opening_rate;

		$receive = $row['receive'];
		$issue_return = $row['issue_return'];
		$transfer_in = $row['transfer_in'];
		$totalReceive=$receive+$issue_return+$transfer_in;

		$total_rcv_value = $row['total_rcv_value'];

		$issue = $row['issue'];
		$rec_return = $row['rec_return'];
		$transfer_out = $row['transfer_out'];
		$totalIssue=$issue+$rec_return+$transfer_out;
		$total_issue_value=$row['total_issue_value'];

		$closingStock+=$opening+$totalReceive-$totalIssue;
		
	}
	

//-----------------------------------------------end gray stock;		


//Dyes/Chemical...........................................
	
	$mrr_rate_sql=sql_select("select prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction where status_active=1and is_deleted=0 and item_category in(5,6,7,23) and transaction_type in(1,4,5)  group by prod_id"); // and  COMPANY_ID=1 
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
	 where  a.prod_id=b.id and a.item_category in(5,6,7,23) and b.item_category_id in (5,6,7,23)  and a.order_id=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	 group by a.expire_date,a.prod_id";
		
		//echo $dyes_chemical_stock_sql;
		
		$dyesChemicalStockSqlResult = sql_select($dyes_chemical_stock_sql);
			$dyesChemicalStockQty=0;$dyesChemicalStockAmountUsd=0;
			foreach ($dyesChemicalStockSqlResult as $row) 
			{
				$dyesChemicalStockQty+=$row[csf('stock_qty')];
				$dyesChemicalStockAmountUsd+=($row[csf('stock_qty')]*$mrr_rate_arr[$row[csf("prod_id")]])/$conversion_rate;
			}
			
			
//Printing/Chemical...........................................
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



$fabric_delivery_sql=" SELECT
         b.uom,
         SUM (d.current_delivery) AS current_delivery
    FROM inv_receive_master a,
         pro_finish_fabric_rcv_dtls b,
         pro_grey_prod_delivery_mst c,
         pro_grey_prod_delivery_dtls d
   WHERE a.id = b.mst_id
         
         AND a.id = d.grey_sys_id
         AND b.id = d.sys_dtls_id
         AND c.id = d.mst_id
         AND a.entry_form IN (7, 66)
         AND d.entry_form = 54
         AND a.item_category = 2
         AND a.status_active = 1
         AND c.delevery_date BETWEEN '" . $prev_date . "' and '" . $prev_date . "'
         AND a.is_deleted = 0
         AND b.uom != 0
         AND d.is_deleted = 0 
         AND d.status_active = 1
GROUP BY  b.uom";//

		$fabric_delivery_result = sql_select($fabric_delivery_sql);
			foreach ($fabric_delivery_result as $row) 
			{
				$fabric_delivery_qty[$row[csf('uom')]]+=$row[csf('current_delivery')];

			}			
			
		
			
//Kniting Production--------------------------------------                            
	$str_cond_f	=" and a.receive_date between '".$prev_date."' and '".$prev_date."'";
	 $sql_qty="select sum(c.quantity) as qtyinhouse 
	 from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,wo_po_break_down e, wo_po_details_master f where a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.receive_basis!=4 and a.knitting_source in(1,3) $str_cond_f and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
  union all
     select sum(b.PRODUCT_QNTY) as qtyinhouse  from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id  and a.product_type in(2) and b.product_type in(2)  and a.product_date between '".$prev_date."' and '".$prev_date."'   and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1	 
  union all
	SELECT sum(case when a.booking_without_order=1 then b.grey_receive_qnty end ) as qtyinhouse from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.receive_basis!=4 and a.buyer_id>0    and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1  AND a.receive_date BETWEEN  '".$prev_date."' and '".$prev_date."'";//and a.ENTRY_FORM=159 
	     //echo $sql_qty;die;
	 
	$sql_result=sql_select( $sql_qty, '', '', '', $con);
	$kniting_pro_qty=0;
	foreach($sql_result as $row)
	{
		$kniting_pro_qty += $row[csf('qtyinhouse')];
	}				
	unset($sql_result);
	$knite=$kniting_pro_qty;
//Kniting Production--------------------------------------end 

 //Dyeing--------------------------------------                            
    $dye_date_con=" and a.process_end_date between '$prev_date' and '$prev_date'";
	
	$dye_sql="select sum(b.production_qty) as production_qty,c.total_trims_weight
from  pro_fab_subprocess a, pro_fab_subprocess_dtls b,pro_batch_create_mst c
where a.id = b.mst_id and a.batch_id=c.id and a.load_unload_id = 2 and a.result=1 and b.load_unload_id=2 and b.entry_page=35 and a.status_active = 1 and b.status_active=1  and c.status_active=1 and a.company_id in($companyStr) $dye_date_con  group by c.total_trims_weight"; //
	 //echo $dye_sql;
	  
	$dyeing_qty=0;
	$dye_sql_result = sql_select($dye_sql, '', '', '', $con);
	foreach($dye_sql_result as $row)
	{
		$dyeing_qty+=($row[csf('production_qty')]+$row[csf('total_trims_weight')]);
	}
	unset($dye_sql_result);
	
	
	$sub_dye_sql="select sum(c.batch_weight) as batch_weight
from  pro_fab_subprocess a,pro_batch_create_mst c
where a.batch_id=c.id and a.load_unload_id = 2 $dye_date_con  and a.result=1 and a.entry_form=38 and c.status_active = 1 and c.status_active=1  and c.status_active=1 and a.company_id in($companyStr)";//
	  //echo $dye_sql;die;
	  
	$sub_dye_sql_result = sql_select($sub_dye_sql, '', '', '', $con);
	foreach($sub_dye_sql_result as $row)
	{
		$dyeing_qty+=$row[csf('batch_weight')];
	}
	unset($dye_sql_result);	
	$dyeing=$dyeing_qty;

//Dyeing--------------------------------------end  



		//Finishing (Kg/Yds)...............................................
		
		$finishigSql="select b.REJECT_QTY,b.receive_qnty,b.uom from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=7 and a.item_category=2 and a.receive_basis=5  and a.receive_date between '$prev_date' and '$prev_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.knitting_company in($companyStr)"; //
		//echo $finishigSql;
		$finishigSqlResult = sql_select($finishigSql);
		foreach ($finishigSqlResult as $row) 
		{
			$finishQty[$row[csf('uom')]]+=$row[csf('receive_qnty')];
			$finishRejQty[$row[csf('uom')]]+=$row['REJECT_QTY'];

		}
			
			
			
		$sub_finishig_sql="select 12 as uom, b.product_qnty as finish_qty,b.REJECT_QNTY from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id  and a.product_type=4 and a.product_date between '$prev_date' and '$prev_date' and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1
"; 
		$sub_finishig_sql_result = sql_select($sub_finishig_sql);
		foreach ($sub_finishig_sql_result as $row) 
		{
			$finishQty[$row[csf('uom')]]+=$row[csf('finish_qty')];
			$finishRejQty[$row[csf('uom')]]+=$row[csf('REJECT_QNTY')];

		}
	


//Machine Summary.............................

$machine_sql= "select id,category_id,machine_group,dia_width,gauge from lib_machine_name where category_id in (1,2) and is_deleted = 0 and status_active = 1";
$machine_sql_result=sql_select($machine_sql);
foreach($machine_sql_result as $row)
{
	if($row[csf(machine_group)] and $row[csf(dia_width)] and $row[csf(gauge)] and $row[csf(category_id)]==1){
		$machineIdArr[$row[csf(category_id)]][$row[csf(id)]]=$row[csf(id)];
	}
	else if($row[csf(category_id)]==2)
	{
		$machineIdArr[$row[csf(category_id)]][$row[csf(id)]]=$row[csf(id)];
	}
	

}

/* union all
	select f.machine_id as machine_no_id, 2 as active_machie_type  from pro_batch_create_dtls b, fabric_sales_order_mst d, pro_fab_subprocess f, pro_batch_create_mst a where f.batch_id=a.id and a.working_company_id=1 and f.process_end_date = '".$prev_date."'   and f.service_source in(1) and a.entry_form=0 and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=1 and b.is_sales=1 group by  f.machine_id */
	
//1=kniting;2=Dyeing;
	   
	$sql="select b.machine_no_id, 1 as active_machie_type from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.machine_no_id>0 and a.receive_date = '".$prev_date."'  group by b.machine_no_id
	 
union all

SELECT f.machine_id AS machine_no_id, 2 AS active_machie_type
    FROM pro_batch_create_dtls b, pro_fab_subprocess f, pro_batch_create_mst a
   WHERE     f.batch_id = a.id
         AND f.process_end_date = '".$prev_date."'
         AND f.service_source=1
         AND a.entry_form = 0
         AND a.id = b.mst_id
         AND f.batch_id = b.mst_id
         AND f.entry_form = 35
         AND f.load_unload_id = 2
         AND a.batch_against IN (1, 2,3,11)
         AND b.status_active = 1
         AND b.is_deleted = 0
         AND a.status_active = 1
         AND a.is_deleted = 0
         AND f.status_active = 1
         AND f.is_deleted = 0
GROUP BY f.machine_id
	";	
	//echo $sql;   
	   
$sql_result=sql_select($sql);
foreach($sql_result as $row)
{
	if($row[csf(active_machie_type)]==1){$active_kniting_machine_id_arr[$row[csf(machine_no_id)]]=$row[csf(machine_no_id)];}
	if($row[csf(active_machie_type)]==2){$active_dyeing_machine_id_arr[$row[csf(machine_no_id)]]=$row[csf(machine_no_id)];}

}
	$total_kniting_machine=count($machineIdArr[1]);
	$total_dyeing_machine=count($machineIdArr[2]);
	$total_idle_kniting_machine=$total_kniting_machine-count($active_kniting_machine_id_arr);
	$total_idle_dyeing_machine=$total_dyeing_machine-count($active_dyeing_machine_id_arr);

			
?>


	<table cellspacing="0" cellpadding="1" border="1" rules="all">
            <thead>
                <tr>
                    <th colspan="3">Daily  Report</th>
                </tr>
                <tr>
                    <th colspan="3"><?=reset($group_lib);?></th>
                </tr>
                <tr>
                    <th colspan="3">Date-<?= $prev_date;?></th>
                </tr>
            </thead>
            
            <tr bgcolor="#BBB">
                <td rowspan="2">Garments / Floor</td>
                <td>Production</td>
                <td rowspan="2">Shipment</td>
            </tr>
            <tr bgcolor="#BBB">
                <td>in Sewing</td>
            </tr>
            <?
			$sewingTotal=0;$exfactoryTotal=0;
			foreach($sewing_production_po_data_arr as $working_company_id=>$floorDataArr){
				foreach($floorDataArr as $floor_id=>$sewingQty){
					$sewingTotal+=$sewingQty;
					$exfactoryTotal+=$ex_fac_qty_arr[$working_company_id][$floor_id];
					?>
                    <tr>
                    	<td><?= $floor_lib[$floor_id];?></td>
                    	<td align="right"><?= $sewingQty;?></td>
                    	<td align="right"><?= $ex_fac_qty_arr[$working_company_id][$floor_id];?></td>
                    </tr>
                    <?
				}
			}
			?>
            <tfoot>
                <th>Total</th>
                <th align="right"><?= $sewingTotal;?></th>
                <th align="right"><?= $exfactoryTotal;?></th>
            </tfoot>
 		</table>
		
        <br />

        <table cellspacing="0" cellpadding="1" border="1" rules="all">
            <thead>
                <th>Textile</th>
                <th>Qty/Value</th>
            </thead>
            <tr><td>Yarn Stock (Kg)</td><td align="right"><?= number_format($yarnStock,2);?></td></tr>
            <tr><td>Yarn Value (USD)</td><td align="right"><?= number_format($valueUsd,2);?></td></tr>
            <tr><td>Grey Stock (Kg)</td><td align="right"><?= number_format($closingStock,2);?></td></tr>
            <tr><td>Dyes/Chemical Stock (Kg)</td><td align="right"><?= number_format($dyesChemicalStockQty,2);?></td></tr>
            <tr><td>Dyes/Chemical Value (USD)</td><td align="right"><?= number_format($dyesChemicalStockAmountUsd,2);?></td></tr>
            <tr><td>Printing Chemical Stock (Kg)</td><td align="right"><?= number_format($printChemicalStockQty,2);?></td></tr>
            <tr><td>Printing Chemical Value (USD)</td><td align="right"><?= number_format($printChemicalStockAmountUsd,2);?></td></tr>
            <tr><td>Knitting (Kg)</td><td align="right"><?= number_format($knite,2);?></td></tr>
            <tr><td>Dyeing (Kg)</td><td align="right"><?= number_format($dyeing,2);?></td></tr>
            <tr><td>Finishing (Kg)</td><td align="right"><?= number_format($finishQty[12],2);?></td></tr>
            <tr><td>Fabric Delivery to Store (Kg)</td><td align="right"><?= number_format($fabric_delivery_qty[12],2);?></td></tr>
            <tfoot bgcolor="#CCCCCC">
            	<th>Total Yarn+DC+PC Value(USD)</th><th align="right"><?= number_format($valueUsd+$dyesChemicalStockAmountUsd+$printChemicalStockAmountUsd,2) ?></th>
            </tfoot>
        </table>
        
        <br />
        <table cellspacing="0" border="1" rules="all" width="243">
            <tr bgcolor="#CCCCCC">
                <th colspan="4">Machine Summary	</th>
            </tr>		
            <tr>
                <th>Dept</th>
                <th>Total M/C</th>
                <th>Idle M/C</th>
                <th>Idle M/C%</th>
            </tr>
            <tr>
                <td>Knitting</td>
                <td align="right"><? echo $total_kniting_machine;?></td>
                <td align="right"><? echo $total_idle_kniting_machine;?></td>
                <td align="right"><? echo number_format(($total_idle_kniting_machine*100)/$total_kniting_machine,2);?></td>
            </tr>
            <tr>
                <td>Dyeing</td>
                <td align="right"><? echo $total_dyeing_machine;?></td>
                <td align="right"><? echo $total_idle_dyeing_machine;?></td>
                <td align="right"><? echo number_format(($total_idle_dyeing_machine*100)/$total_dyeing_machine,2);?></td>
            </tr>
        </table>
        


<?
	
	$mail_item=18;
	$to="";
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=18 and b.mail_user_setup_id=c.id and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 group by c.email_address";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
	
	$subject="Daily Production Auto Mail";
	 
	$message="";
	$message=ob_get_contents();
	 ob_clean();
	$header=mailHeader();
	if($_REQUEST['isview']==1){
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	}

		



?>

 