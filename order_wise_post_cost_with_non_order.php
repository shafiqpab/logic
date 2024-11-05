<?php
require_once('includes/common.php');
require_once('includes/class4/class.conditions.php');
require_once('includes/class4/class.reports.php');
require_once('includes/class4/class.yarns.php');
require_once('includes/class4/class.conversions.php');
require_once('includes/class4/class.trims.php');
require_once('includes/class4/class.emblishments.php');
require_once('includes/class4/class.commisions.php');
require_once('includes/class4/class.commercials.php');
require_once('includes/class4/class.others.php');
require_once('includes/class4/class.washes.php');
require_once('includes/class4/class.fabrics.php');
extract($_REQUEST);

// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];

	if($db_type==2)
	{
		$form_date=change_date_format($form_date, "yyyy-mm-dd","-",1);
		$to_date=change_date_format($to_date, "yyyy-mm-dd","-",1);
	}
	else
	{
		$form_date=change_date_format($form_date, "yyyy-mm-dd");
		$to_date=change_date_format($to_date, "yyyy-mm-dd");
	}

//****************************************************************************************************************************************************************************


	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	//$exchange_rate_arr=return_library_array( "select job_no,exchange_rate from wo_pre_cost_mst",'job_no','exchange_rate');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$company_name=$company_id;
	$exfactory_order_cond='';
	if(str_replace("'","",$order_id)!="")	$exfactory_order_cond=" and a.po_break_down_id in (".$order_id.")  ";
	$exfactoryQtyArr=array();

	$exfactory_data=sql_select("select a.po_break_down_id,a.shiping_status, a.ex_factory_date,
		sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty 
		from pro_ex_factory_mst a,pro_ex_factory_delivery_mst b where 1=1 and b.id=a.delivery_mst_id and b.company_id=$company_id  and a.status_active=1 and a.is_deleted=0 and a.ex_factory_date between '".$form_date."' and '".$to_date."' $exfactory_order_cond group by a.po_break_down_id,a.shiping_status, a.ex_factory_date");//and a.po_break_down_id=10787

	$full_shipment_data=array();
	foreach($exfactory_data as $exfatory_row){
		if($exfatory_row[csf('shiping_status')]==3)
		{
			$full_shipment_data[$exfatory_row[csf('po_break_down_id')]]['full_shipment_date']=$exfatory_row[csf('ex_factory_date')];
			$full_shipment_po[$exfatory_row[csf('po_break_down_id')]]=$exfatory_row[csf('po_break_down_id')];
		}
		$exfactory_po_arr[$exfatory_row[csf('po_break_down_id')]]=$exfatory_row[csf('po_break_down_id')];
		$exfactoryQtyArr[$exfatory_row[csf('po_break_down_id')]]+=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('ex_factory_return_qnty')];
	} 
	
	$exfactory_previous_sql=sql_select("select a.po_break_down_id,
		sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty 
		from pro_ex_factory_mst a,pro_ex_factory_delivery_mst b where 1=1 and b.id=a.delivery_mst_id and b.company_id=$company_id and a.po_break_down_id in (".implode(",",$full_shipment_po).")  and a.status_active=1 and a.is_deleted=0 and a.ex_factory_date<'".$form_date."' group by a.po_break_down_id");
	$exfactory_previous_data=array();
	foreach($exfactory_previous_sql as $ex_pre_val)
	{
		$exfactory_previous_data[$ex_pre_val[csf('po_break_down_id')]]=$ex_pre_val[csf('ex_factory_qnty')]-$ex_pre_val[csf('ex_factory_return_qnty')];
	}
	
	$sql="select a.job_no_prefix_num, a.job_no,  a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv,a.currency_id, a.quotation_id,b.id, b.po_number,b.grouping,b.file_no, b.status_active as cancelled_status,b.shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status,c.item_number_id,c.order_quantity , c.order_rate,c.order_total , c.plan_cut_qnty,c.shiping_status  as shiping_status_c,d.exchange_rate from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_mst d where a.id=b.job_id and  a.id=c.job_id and  a.id=d.job_id and  b.job_id=d.job_id and b.id=c.po_break_down_id  and a.company_name='$company_name' and b.id in (".implode(",",$exfactory_po_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 and c.status_active in(1,3) and c.is_deleted=0 order by  b.id";
	$result=sql_select($sql);

	$totalSetQnty="";
	$currencyId="";
	$quotationId="";
	$poNumberArr=array();
	$poIdArr=array();
	$poQtyArr=array();
	$poPcutQtyArr=array();
	$poValueArr=array();
	$ShipDateArr=array();
	$gmtsItemArr=array();
	$shipingStatus="";
	$job_information_arr=array(); 
	$po_information_arr=array();
	foreach($result as $row){
		
		$po_information_arr[$row[csf('id')]]['cancelled_status']=$row[csf('cancelled_status')];
		$po_information_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$po_information_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$po_information_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$po_information_arr[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
		
		$po_information_arr[$row[csf('id')]]['ratio']=$row[csf('ratio')];
		$po_information_arr[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
		$po_information_arr[$row[csf('id')]]['quotation_id']=$row[csf('quotation_id')];
		$po_information_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$po_information_arr[$row[csf('id')]]['shiping_status']=$row[csf('shiping_status')];
		$po_information_arr[$row[csf('id')]]['item_number_id']=$row[csf('item_number_id')];
		$po_information_arr[$row[csf('id')]]['shipment_date']=date("d-m-Y",strtotime($row[csf('shipment_date')]));
		$po_information_arr[$row[csf('id')]]['order_quantity']+=$row[csf('order_quantity')];
		$po_information_arr[$row[csf('id')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];
		$po_information_arr[$row[csf('id')]]['order_total']+=$row[csf('order_total')];
		$quotation_id_arr[$row[csf('quotation_id')]]=$row[csf('quotation_id')];
		$job_information_arr[$row[csf('job_no')]]['job_quantity']+=$row[csf('order_quantity')];
		if(!in_array($row[csf('id')],$po_check_arr))
		{
			$exfactory_qty_job[$row[csf('job_no')]]+=$exfactoryQtyArr[$row[csf('id')]];
			$exfactory_qty_quotation[$row[csf('quotation_id')]]+=$exfactoryQtyArr[$row[csf('id')]];
			$po_check_arr[]=$row[csf('id')];
		}
		$po_ids.=$row[csf('id')].',';
		$all_job_arr[$row[csf('job_no')]]=$row[csf('job_no')];
		$exchange_rate_arr[$row[csf('job_no')]]=$row[csf('exchange_rate')];
	}
	foreach($po_information_arr as $po_id=>$po_data)
	{
		if(in_array($po_id,$full_shipment_po))
		{
			//$exfactoryQtyArr[$po_id]=$po_data['order_quantity']-$exfactory_previous_data[$po_id];
		}
		$po_information_arr[$po_id]['unitPrice']=$po_data['order_total']/$po_data['order_quantity'];
	}

		
	foreach($exfactory_data as $exfatory_row){

		$exfactoryQty+=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('ex_factory_return_qnty')];
		$exfactoryValue+=($exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('ex_factory_return_qnty')])*$po_information_arr[$exfatory_row[csf('po_break_down_id')]]['unitPrice'];
		$shortExcessQty+=$po_information_arr[$exfatory_row[csf('po_break_down_id')]]['order_quantity']-$exfatory_row[csf('ex_factory_qnty')]+$exfatory_row[csf('ex_factory_return_qnty')];
		$shortExcessValue+=($po_information_arr[$exfatory_row[csf('po_break_down_id')]]['order_quantity']-$exfatory_row[csf('ex_factory_qnty')]+$exfatory_row[csf('ex_factory_return_qnty')])*$po_information_arr[$exfatory_row[csf('po_break_down_id')]]['unitPrice'];
	}
	
		
	if(!empty($quotation_id_arr)){
		$quaOfferQnty=0;
		$quaConfirmPrice=0;
		$quaConfirmPriceDzn=0;
		$quaPriceWithCommnPcs=0;
		$quaCostingPer=0;
		$quaCostingPerQty=0;
		$sqlQua="select a.id,a.offer_qnty,a.costing_per,b.confirm_price,b.confirm_price_dzn,b.price_with_commn_pcs from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.id in (".implode(",",$quotation_id_arr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$dataQua=sql_select($sqlQua);
		$quation_data_arr=array();
		foreach($dataQua as $rowQua){
			
			$quaCostingPer=$rowQua[csf('costing_per')];
			$quaCostingPerQty=0;
			if($quaCostingPer==1){
				$quaCostingPerQty=12;
			}
			if($quaCostingPer==2){
				$quaCostingPerQty=1;
			}
			if($quaCostingPer==3){
				$quaCostingPerQty=24;
			}
			if($quaCostingPer==4){
				$quaCostingPerQty=36;
			}
			if($quaCostingPer==5){
				$quaCostingPerQty=48;
			}
			
			$quation_data_arr[$rowQua[csf('id')]]['quaOfferQnty']			=$rowQua[csf('offer_qnty')];
			$quation_data_arr[$rowQua[csf('id')]]['quaConfirmPrice']		=$rowQua[csf('confirm_price')];
			$quation_data_arr[$rowQua[csf('id')]]['quaConfirmPriceDzn']		=$rowQua[csf('confirm_price_dzn')];
			$quation_data_arr[$rowQua[csf('id')]]['quaPriceWithCommnPcs']	=$rowQua[csf('price_with_commn_pcs')];
			$quation_data_arr[$rowQua[csf('id')]]['quaCostingPer']			=$rowQua[csf('costing_per')];
			$quation_data_arr[$rowQua[csf('id')]]['quaCostingPerQty']		=$quaCostingPerQty;
		}
	}

		$condition= new condition();
		$condition->job_no("in ('".implode("','",$all_job_arr)."')");

		$condition->init();
		$costPerArr=$condition->getCostingPerArr();
		
		$condition= new condition();
		$condition->po_id("in (".implode(",",$exfactory_po_arr).")");

		$condition->init();
		$fabric= new fabric($condition);
		$yarn= new yarn($condition);

		$conversion= new conversion($condition);
		$trim= new trims($condition);
		$emblishment= new emblishment($condition);
		$wash= new wash($condition);
		$commercial= new commercial($condition);
		$commision= new commision($condition);
		$other= new other($condition);
		
	// Yarn ============================
		$totYarn=0;
		$YarnData=array();$ComissionData=array();
		
		
		$yarn_data_array=$yarn->getOrderWiseYarnQtyAndAmountArray();
		foreach($yarn_data_array as $po_id=>$po_row)
		{
			$cancelled_po=$po_information_arr[$po_id]['cancelled_status'];
			if($cancelled_po==1)
			{
				if($exfactoryQtyArr[$po_id]>=$po_row['qty'])
				{
					$YarnData['preCost']['qty']+=$po_row['qty'];
					$YarnData['preCost']['amount']+=$po_row['amount'];
				}
				else
				{
					$YarnData['preCost']['qty']+=$exfactoryQtyArr[$po_id];
					$YarnData['preCost']['amount']+=($po_row['amount']*$exfactoryQtyArr[$po_id])/$po_row['qty'];
				}
			}
			else //Cancelled PO
			{
				if($exfactoryQtyArr[$po_id]>=$po_row['qty'])
				{
					$YarnData['preCost']['can_qty']+=$po_row['qty'];
					$YarnData['preCost']['can_amount']+=$po_row['amount'];
				}
				else
				{
					$YarnData['preCost']['can_qty']+=$exfactoryQtyArr[$po_id];
					$YarnData['preCost']['can_amount']+=($po_row['amount']*$exfactoryQtyArr[$po_id])/$po_row['qty'];
				}
			}
		}
		
		if(!empty($quotation_id_arr)){
			$sql_yarn_pri="select f.id as yarn_id,f.quotation_id,f.cons_ratio,f.cons_qnty,f.rate,f.amount,count_id,copm_one_id,percent_one,type_id   from wo_pri_quo_fab_yarn_cost_dtls f where f.quotation_id in (".implode(",",$quotation_id_arr).") and f.is_deleted=0 and f.status_active=1  order by f.id";
			$data_arr_yarn_pri=sql_select($sql_yarn_pri);
			foreach($data_arr_yarn_pri as $yarn_row_pri){
				$yarnrate=$yarn_row_pri[csf("rate")];
				$quaCostingPerQty=$quation_data_arr[$yarn_row_pri[csf("quotation_id")]]['quaCostingPerQty'];
				$quaOfferQnty=$quation_data_arr[$yarn_row_pri[csf("quotation_id")]]['quaOfferQnty'];
				if($quaOfferQnty>$exfactory_qty_quotation[$yarn_row_pri[csf('quotation_id')]])
				{
					$quaOfferQnty=$exfactory_qty_quotation[$yarn_row_pri[csf('quotation_id')]];
				}
				$consQnty=($yarn_row_pri[csf("cons_qnty")]/$quaCostingPerQty)*($quaOfferQnty);
				$amount=$consQnty*$yarnrate;
				
				$YarnData['mkt']['qty']+=$consQnty;
				$YarnData['mkt']['amount']+=$amount;
			}
		}
		//without booking for yarn actual
		$YarnIssue_non=array();$can_YarnIssue_non=array();
		 //$sql_non="select x.*,(x.cons_quantity*x.cons_rate) cons_amount from ( select c.cons_quantity,b.yarn_count_id,b.yarn_comp_type1st, b.yarn_comp_percent1st,b.yarn_type, c.cons_rate,d.issue_basis,d.booking_no,d.id as mstId,b.lot from  product_details_master b,inv_transaction c,inv_issue_master d where   c.mst_id=d.id  and c.prod_id=b.id and c.transaction_type in(2) and d.entry_form in(3)  and d.issue_date between '".$form_date."' and '".$to_date."' and d.issue_purpose in(8) and b.is_deleted=0 and b.status_active=1 and d.issue_purpose!=2 ) x"; 
		 $sql_non=" select (c.cons_quantity*c.cons_rate) cons_amount ,c.cons_quantity,b.yarn_count_id,b.yarn_comp_type1st, b.yarn_comp_percent1st,b.yarn_type, c.cons_rate,d.issue_basis,d.booking_no,d.id as mstId,b.lot from  product_details_master b,inv_transaction c,inv_issue_master d where c.mst_id=d.id and c.prod_id=b.id and c.transaction_type in(2) and d.entry_form in(3) and d.company_id=$company_id and d.issue_date between '".$form_date."' and '".$to_date."' and d.issue_purpose in(8) and b.is_deleted=0 and b.status_active=1 and d.issue_purpose!=2 ";
		//echo $sql_non; die;  
		$data_Yac_non=sql_select($sql_non);
		foreach($data_Yac_non as $row){
			//$index="'".$row[csf("yarn_count_id")]."_".$row[csf("yarn_comp_type1st")]."_".$row[csf("yarn_comp_percent1st")]."_".$row[csf("yarn_type")]."'";
			//$YarnIssue[$row[csf("po_breakdown_id")]]['qty']+=($row[csf("quantity")]-$row[csf("returnable_qnty")]);
			$YarnIssue_non[$row[csf("mstId")]]['qty']+=$row[csf("cons_quantity")];
			$YarnIssue_non[$row[csf("mstId")]]['amount']+=$row[csf("cons_amount")];
			$YarnIssue_avg_rate_non[$row[csf("lot")]]['avg_rate']+=$row[csf("cons_amount")]/$row[csf("quantity")];
			$mstIdArr[$row[csf("mstId")]]=$row[csf("mstId")];
			
			//$yarn_lots.=$row[csf("lot")].',';
		}
		$sql_yarn_ret="select 
		c.transaction_type,a.issue_id,a.entry_form,c.cons_quantity,c.cons_rate from inv_receive_master a,inv_transaction c where  c.mst_id=a.id and  c.transaction_type in(4,5,6) and a.entry_form in (9,11) and a.issue_id in(".implode(",",$mstIdArr).") and a.company_id=$company_id and a.is_deleted=0 and  a.status_active=1 and c.is_deleted=0 and  c.status_active=1 and c.item_category=1";
		$data_Yac_non_ret=sql_select($sql_yarn_ret);
		foreach($data_Yac_non_ret as $row){
			$YarnIssue_ret_non[$row[csf("issue_id")]]['qty']+=$row[csf("cons_quantity")];
			$YarnIssue_ret_non[$row[csf("issue_id")]]['amount']+=$row[csf("cons_quantity")]*$row[csf("cons_rate")];
		}
		foreach($YarnIssue_non as $ind=>$value){
			
			$YarnData['acl']['non_qty']+=($value['qty']-$YarnIssueReturn[$ind]['qty']);
			$YarnData['acl']['non_amount']+=($value['amount']-$YarnIssueReturn[$ind]['amount']);
		}
		
		/*$yarn_lots=rtrim($yarn_lots,',');
		$yarn_lots=array_unique(explode(",",$yarn_lots));
		$lot_tmp='';
		foreach($yarn_lots as $lot)
		{
			if($lot_tmp=='') $lot_tmp="'".$lot."'";else $lot_tmp.=","."'".$lot."'";
		}*/
		//End....
		$YarnIssue=array();$can_YarnIssue=array();
		 $sql="select x.*,(x.quantity*x.cons_rate) cons_amount from ( select a.id,a.trans_id,a.trans_type,a.entry_form,a.po_breakdown_id,a.prod_id, a.quantity,a.issue_purpose,a.returnable_qnty,a.is_sales,b.yarn_count_id,b.yarn_comp_type1st, b.yarn_comp_percent1st,b.yarn_type, c.cons_rate,d.issue_basis,d.booking_no,b.lot from order_wise_pro_details a, product_details_master b,inv_transaction c,inv_issue_master d where a.prod_id=b.id and a.trans_id=c.id and c.mst_id=d.id and a.trans_type in(2) and a.entry_form in(3) and a.po_breakdown_id in(".implode(",",$exfactory_po_arr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.issue_purpose!=2 )x";
		//echo $sql;die; 
		$data_Yac=sql_select($sql);
		foreach($data_Yac as $row){
			//$index="'".$row[csf("yarn_count_id")]."_".$row[csf("yarn_comp_type1st")]."_".$row[csf("yarn_comp_percent1st")]."_".$row[csf("yarn_type")]."'";
			//$YarnIssue[$row[csf("po_breakdown_id")]]['qty']+=($row[csf("quantity")]-$row[csf("returnable_qnty")]);
				$po_id=$row[csf("po_breakdown_id")];
			$cancelled_po=$po_information_arr[$po_id]['cancelled_status'];
			if($cancelled_po==1)
			{
			$YarnIssue[$row[csf("po_breakdown_id")]]['qty']+=$row[csf("quantity")];
			$YarnIssue[$row[csf("po_breakdown_id")]]['amount']+=$row[csf("cons_amount")];
			$YarnIssue_avg_rate[$row[csf("lot")]]['avg_rate']+=$row[csf("cons_amount")]/$row[csf("quantity")];
			}
			else
			{
			$can_YarnIssue[$row[csf("po_breakdown_id")]]['qty']+=$row[csf("quantity")];
			$can_YarnIssue[$row[csf("po_breakdown_id")]]['amount']+=$row[csf("cons_amount")];
			$can_YarnIssue_avg_rate[$row[csf("lot")]]['avg_rate']+=$row[csf("cons_amount")]/$row[csf("quantity")];
			}
			$yarn_lots.=$row[csf("lot")].',';
		}
		$yarn_lots=rtrim($yarn_lots,',');
		$yarn_lots=array_unique(explode(",",$yarn_lots));
		$lot_tmp='';
		foreach($yarn_lots as $lot)
		{
			if($lot_tmp=='') $lot_tmp="'".$lot."'";else $lot_tmp.=","."'".$lot."'";
		}
	//print_r($data_arr_yarn);die;	
		//echo $lot_tmp;
		$grey_fab_array=array();  //Knitting Cost actual 
		$sql_knit_prod="select c.yarn_lot,b.po_breakdown_id,
		 sum(CASE WHEN b.entry_form =2 and b.trans_type in(1) and a.item_category=13  THEN  b.quantity ELSE 0 END) AS grey_qnty
		  from inv_receive_master a, order_wise_pro_details b,pro_grey_prod_entry_dtls c where a.id=c.mst_id and c.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2) and a.item_category in(13)  and b.trans_type in(1) and c.yarn_lot in(".$lot_tmp.") and b.po_breakdown_id in(".implode(",",$exfactory_po_arr).") group by c.yarn_lot,b.po_breakdown_id";
		 // echo $sql_knit_prod;die;
		$result_knit=sql_select( $sql_knit_prod );
		$knit_grey_qty=$knit_grey_amt=0;
		foreach($result_knit as $row){
			$job_no=$po_information_arr[$row[csf('po_breakdown_id')]]['job_no'];
			$g_exchange_rate=$exchange_rate_arr[$job_no];
			$avg_rate=$YarnIssue_avg_rate[$row[csf("yarn_lot")]]['avg_rate']/$g_exchange_rate;
			$knit_grey_qty+=$row[csf("grey_qnty")];
			$knit_grey_amt+=$row[csf("grey_qnty")]*$avg_rate;
		}
		$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master", "id", "avg_rate_per_unit"  );

		$grey_fab_trans_array=array(); 
		$sql_grey_trans="select c.from_order_id,a.from_prod_id,
		 sum(CASE WHEN b.entry_form in(83,13) and b.trans_type in(5) THEN  b.quantity ELSE 0 END) AS grey_in,
		 sum(CASE WHEN b.entry_form in(83,13) and b.trans_type in(6) THEN  b.quantity ELSE 0 END) AS grey_out
		  from  inv_item_transfer_mst c, inv_item_transfer_dtls a, order_wise_pro_details b where c.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(83,13) and b.trans_type in(5,6) and b.po_breakdown_id in(".implode(",",$exfactory_po_arr).") group by  a.from_prod_id,c.from_order_id";//
		//echo $sql_grey_trans;die;  
		$result_grey_trans=sql_select( $sql_grey_trans );
		$grey_fab_trans_qty_acl=$grey_fab_trans_amt_acl=0;$from_order_id='';
		foreach ($result_grey_trans as $row)
		{
			$avg_rate=$avg_rate_array[$row[csf('from_prod_id')]];
			$from_order_id.=$row[csf('from_order_id')].',';
			$grey_fab_trans_qty_acl+=$row[csf('grey_in')]-$row[csf('grey_out')];
			$grey_fab_trans_amt_acl+=($row[csf('grey_in')]-$row[csf('grey_out')])*$avg_rate;
		}
		
		$from_order_id=rtrim($from_order_id,',');
		$from_order_ids=array_unique(explode(",",$from_order_id));
		
		$subconOutBillData="select b.order_id, 
		sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
		sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
		from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id in(2,4) and b.order_id in(".implode(",",$exfactory_po_arr).") group by b.order_id";
		$subconOutBillDataArray=sql_select($subconOutBillData);
		$tot_knit_charge=0;
		foreach($subconOutBillDataArray as $subRow)
		{
			$job_no=$po_information_arr[$subRow[csf('order_id')]]['job_no'];
			$g_exchange_rate=$exchange_rate_arr[$job_no];
			$tot_knit_charge+=$subRow[csf('knit_bill')]/$g_exchange_rate;
		}
		// Transfer Fin Actual
		$fin_fab_trans_array=array(); 
		$sql_fin_trans="select b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty,
				 	sum(CASE WHEN b.trans_type=5 THEN b.quantity END) AS in_qty,
					sum(CASE WHEN b.trans_type=6 THEN b.quantity END) AS out_qty,
				 c.from_order_id
				  from inv_transaction a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(15) and a.item_category=2 and a.transaction_type in(5,6) and b.trans_type in(5,6) and b.po_breakdown_id in(".implode(",",$exfactory_po_arr).") group by b.trans_type, b.po_breakdown_id,c.from_order_id,b.prod_id";
				  
		$result_fin_trans=sql_select( $sql_fin_trans );
		$fin_from_order_id='';$fin_fab_trans_qty=$fin_fab_trans_amt=$fin_in_qnty=$fin_out_qnty=$fin_in_amt=$fin_out_amt=0;
		foreach ($result_fin_trans as $row)
		{
			$fin_from_order_id.=$row[csf('from_order_id')].',';
			$avg_rate=$avg_rate_array[$row[csf('prod_id')]];
			$fin_in_qnty+=$row[csf('in_qty')];
			$fin_out_qnty+=$row[csf('out_qty')];
			$fin_in_amt+=$row[csf('in_qty')]*$avg_rate;
			$fin_out_amt+=$row[csf('out_qty')]*$avg_rate;
		}
		
		
		unset($result_fin_trans);
		$fin_from_order_id=rtrim($fin_from_order_id,',');
		$fin_from_order_ids=array_unique(explode(",",$fin_from_order_id));
		
		$fin_trans_in_qty=$fin_in_qnty;
		$fin_trans_out_qty=$fin_out_qnty;
		
		$fin_trans_in_amt=$fin_in_amt;
		$fin_trans_out_amt=$fin_out_amt;
		
		$tot_fin_fab_transfer_qty=$fin_trans_in_qty-$fin_trans_out_qty;
		$tot_fin_fab_transfer_amt=$fin_trans_in_amt-$fin_trans_out_amt;
		
		$tot_fin_fab_transfer_cost=$tot_fin_fab_transfer_amt;
		$tot_grey_fab_cost_acl=$grey_fab_trans_amt_acl;
		
		if($from_order_id!="") //Grey Transfer
		{
			$condition1= new condition();
			$condition1->po_id("in($from_order_id)");
			$condition1->init();
			$conversion1= new conversion($condition1);
			$conversion_costing_arr_process=$conversion1->getAmountArray_by_orderAndProcess();
			$conversion1= new conversion($condition1);
			$conversion_costing_arr_process_qty=$conversion1->getQtyArray_by_orderAndProcess();
			
			 $knit_cost=$knit_qty=0;
			 foreach($from_order_ids as $po_id)
			 {
				$knit_cost+=array_sum($conversion_costing_arr_process[$po_id][1]);
				$knit_qty+=array_sum($conversion_costing_arr_process_qty[$po_id][1]);
			 }
		}
		$knit_cost=$knit_qty=$knit_charge=0;
		if($fin_from_order_id!="") //Finish Transfer
		{
			$condition2= new condition();
			$condition2->po_id("in($fin_from_order_id)");
			$condition2->init();
			$conversion2= new conversion($condition2);

			$fin_conversion_costing_arr_process=$conversion2->getAmountArray_by_orderAndProcess();

			$conversion2= new conversion($condition2);
			$fin_conversion_costing_arr_process_qty=$conversion2->getQtyArray_by_orderAndProcess();
			 foreach($fin_from_order_ids as $po_id)
			 {
				$tot_dye_finish_cost_pre=0;$tot_dye_finish_cost_pre_qty=0;
				foreach($conversion_cost_head_array as $process_id=>$val)
				{
					if($process_id!=30 && $process_id!=1 && $process_id!=35) //Yarn Dyeing,Knitting,Aop
					{
						$tot_dye_finish_cost_pre+=array_sum($fin_conversion_costing_arr_process[$po_id][$process_id]);
						$tot_dye_finish_cost_pre_qty+=array_sum($fin_conversion_costing_arr_process_qty[$po_id][$process_id]);
					}
				}
			 }
		}
		
		$knit_charge=$knit_cost/$knit_qty;
		
			
		$YarnIssueReturn=array();
		$sql="select 
		c.transaction_type,a.trans_type,a.entry_form,
		a.quantity,a.po_breakdown_id,c.cons_rate from order_wise_pro_details a,inv_transaction c where  a.trans_id=c.id and  a.trans_type in(4,5,6) and a.entry_form in (9,11) and a.po_breakdown_id in(".implode(",",$exfactory_po_arr).") and a.is_deleted=0 and  a.status_active=1 and c.is_deleted=0 and  c.status_active=1 and c.item_category=1";
		//echo $sql;die;
		//$sql="select a.id,a.trans_id,a.trans_type,a.entry_form,a.po_breakdown_id,a.prod_id,a.quantity,a.issue_purpose,a.returnable_qnty,a.is_sales,b.yarn_count_id,b.yarn_comp_type1st,  b.yarn_comp_percent1st,b.yarn_type,c.cons_rate,c.cons_amount from order_wise_pro_details a, product_details_master b,inv_transaction c where a.prod_id=b.id and a.trans_id=c.id and  a.trans_type=4 and a.entry_form=9 and a.po_breakdown_id in(".implode(",",$exfactory_po_arr).") and a.is_deleted=0 and  a.status_active=1 and b.is_deleted=0 and  b.status_active=1";

		$data_Yac=sql_select($sql);
		foreach($data_Yac as $row){
			
			$po_id=$row[csf("po_breakdown_id")];
			$cancelled_po=$po_information_arr[$po_id]['cancelled_status'];
			if($row[csf("transaction_type")]==4 && $row[csf("trans_type")]==4 && $row[csf("entry_form")]==9)
			{
				if($cancelled_po==1){
				$YarnIssueReturn[$row[csf("po_breakdown_id")]]['qty']+=$row[csf("quantity")];
				$YarnIssueReturn[$row[csf("po_breakdown_id")]]['amount'] += $row[csf("quantity")]*$row[csf("cons_rate")];
				}
				else{
					$YarnIssueReturn[$row[csf("po_breakdown_id")]]['can_qty']+=$row[csf("quantity")];
					$YarnIssueReturn[$row[csf("po_breakdown_id")]]['can_amount'] += $row[csf("quantity")]*$row[csf("cons_rate")];
				}
			}
			else if($row[csf("transaction_type")]==5 && $row[csf("trans_type")]==5 && $row[csf("entry_form")]==11)
			{
				if($cancelled_po==1){
				$YarnIssueReturn[$row[csf("po_breakdown_id")]]['qty']+=$row[csf("quantity")]*(-1);
				$YarnIssueReturn[$row[csf("po_breakdown_id")]]['amount'] += ($row[csf("quantity")]*$row[csf("cons_rate")])*(-1);
				}
				else
				{
				$YarnIssueReturn[$row[csf("po_breakdown_id")]]['can_qty']+=$row[csf("quantity")]*(-1);
				$YarnIssueReturn[$row[csf("po_breakdown_id")]]['can_amount'] += ($row[csf("quantity")]*$row[csf("cons_rate")])*(-1);
				}
			}
			else if($row[csf("transaction_type")]==6 && $row[csf("trans_type")]==6 && $row[csf("entry_form")]==11)
			{ 	if($cancelled_po==1){
				$YarnIssueReturn[$row[csf("po_breakdown_id")]]['qty']+=$row[csf("quantity")];
				$YarnIssueReturn[$row[csf("po_breakdown_id")]]['amount'] += $row[csf("quantity")]*$row[csf("cons_rate")];
			   }
			   else
			   {
				   $YarnIssueReturn[$row[csf("po_breakdown_id")]]['can_qty']+=$row[csf("quantity")];
				  $YarnIssueReturn[$row[csf("po_breakdown_id")]]['can_amount'] += $row[csf("quantity")]*$row[csf("cons_rate")];
			   }
			}
			
		}

		foreach($YarnIssue as $ind=>$value){
			
			$YarnData['acl']['qty']+=(($value['qty']-$YarnIssueReturn[$ind]['qty'])*$exfactoryQtyArr[$ind])/$po_information_arr[$ind]['order_quantity'];
			$YarnData['acl']['amount']+=(($value['amount']-$YarnIssueReturn[$ind]['amount'])*$exfactoryQtyArr[$ind])/$po_information_arr[$ind]['order_quantity'];
		}
		foreach($can_YarnIssue as $ind=>$value){
			
			$YarnData['acl']['can_qty']+=(($value['qty']-$YarnIssueReturn[$ind]['can_qty'])*$exfactoryQtyArr[$ind])/$po_information_arr[$ind]['order_quantity'];
			$YarnData['acl']['can_amount']+=(($value['amount']-$YarnIssueReturn[$ind]['can_amount'])*$exfactoryQtyArr[$ind])/$po_information_arr[$ind]['order_quantity'];
		}

		 $sql_conv_yarn="select b.id as po_id,c.job_no, d.composition  from   wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c,wo_pre_cost_fabric_cost_dtls d  where  b.job_no_mst=c.job_no and c.job_no=d.job_no  and c.fabric_description=d.id  and b.status_active=1 and c.cons_process in(30) and b.is_deleted=0 and b.id in(".implode(",",$exfactory_po_arr).") and c.amount>0 ";
		 
		$result_conv_yarn=sql_select($sql_conv_yarn);
		$conv_yarn_arr=array();
		foreach($result_conv_yarn as $row){
			$index="".$row[csf("composition")]."";
			$conv_yarn_arr['preCost']['amount']+= array_sum($conversion_costing_arr_process[$row[csf("po_id")]][30]);
			$conv_yarn_arr['preCost']['qty']+= array_sum($conversion_costing_arr_process_qty[$row[csf("po_id")]][30]);
		}
		
		
		
		
		if(!empty($quotation_id_arr)){
			 $pq_conv_yarn_data="select a.composition,b.quotation_id,b.cost_head,b.charge_unit,
				(CASE WHEN b.cons_type=30 THEN b.amount END) AS yarn_dyeing_cost,
				(CASE WHEN b.cons_type=30 THEN  b.req_qnty END) AS req_qnty
				from wo_pri_quo_fabric_cost_dtls a, wo_pri_quo_fab_conv_cost_dtls b where a.id=b.cost_head and b.quotation_id in (".implode(",",$quotation_id_arr).") and b.cons_type=30 and b.status_active=1  and b.is_deleted=0";
				$result_price_conv_yarn=sql_select($pq_conv_yarn_data);
				foreach($result_price_conv_yarn as $p_row)
				{
					$pri_yarnrate=$p_row[csf("charge_unit")];
					$mktcons=($p_row[csf('req_qnty')]/$quaCostingPerQty)*($quaOfferQnty);
					$amount=$mktcons*$pri_yarnrate;
					$conv_yarn_arr['mkt']['amount']+=$amount;	
					$conv_yarn_arr['mkt']['qty']+=$mktcons;	
				}
			}


// Yarn End============================
// Fabric Purch ============================
		$totPrecons=0;
		$totPreAmt=0;
		$fabPur=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
		$fabPurArr=array();
		 $sql = "select b.id as po_id,c.id, c.job_no,item_number_id,c.uom, c.body_part_id, c.fab_nature_id, c.color_type_id, c.fabric_description, c.avg_cons, c.fabric_source, c.rate, c.amount,c.avg_finish_cons,c.status_active from wo_pre_cost_fabric_cost_dtls c,wo_po_break_down b where b.job_id=c.job_id  and  b.id in(".implode(",",$exfactory_po_arr).") and c.fabric_source=2 and c.status_active=1";
		$data_fabPur=sql_select($sql);
		foreach($data_fabPur as $fabPur_row){
			$cancelled_po=$po_information_arr[$fabPur_row[csf('po_id')]]['cancelled_status'];
			if($fabPur_row[csf('fab_nature_id')]==2){
				if($cancelled_po==1)
				{
				$Precons=$fabPur['knit']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				//echo $Precons.', ';
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				$fabPurArr['pre']['knit']['qty']+=$Precons;
				$fabPurArr['pre']['knit']['amount']+=$Preamt;
				}
				else //Cancel PO
				{
					$Precons=$fabPur['knit']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				//echo $Precons.', ';
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				$fabPurArr['pre']['knit']['can_qty']+=$Precons;
				$fabPurArr['pre']['knit']['can_amount']+=$Preamt;
				}
			}
			if($fabPur_row[csf('fab_nature_id')]==3){
				if($cancelled_po==1)
				{
				$Precons=$fabPur['woven']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				$fabPurArr['pre']['woven']['qty']+=$Precons;
				$fabPurArr['pre']['woven']['amount']+=$Preamt;
				}
				else //Cancel
				{
				$Precons=$fabPur['woven']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				$fabPurArr['pre']['woven']['can_qty']+=$Precons;
				$fabPurArr['pre']['woven']['can_amount']+=$Preamt;
				}
			}
		}
		 


		if(!empty($quotation_id_arr)){
			$totMktcons=0;
			$totMktAmt=0;
			$sql = "select id, item_number_id, body_part_id, fab_nature_id,quotation_id, color_type_id, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pri_quo_fabric_cost_dtls where quotation_id in (".implode(",",$quotation_id_arr).") and fabric_source=2";
			$data_fabPur=sql_select($sql);
			foreach($data_fabPur as $fabPur_row){
				$quaCostingPerQty=$quation_data_arr[$fabPur_row[csf("quotation_id")]]['quaCostingPerQty'];
				$quaOfferQnty=$quation_data_arr[$fabPur_row[csf("quotation_id")]]['quaOfferQnty'];
				if($quaOfferQnty>$exfactory_qty_quotation[$fabPur_row[csf('quotation_id')]])
				{
					$quaOfferQnty=$exfactory_qty_quotation[$fabPur_row[csf('quotation_id')]];
				}
				$mktcons=($fabPur_row[csf('avg_cons')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$fabPur_row[csf('rate')];
				if($fabPur_row[csf('fab_nature_id')]==2){
					$fabPurArr['mkt']['knit']['qty']+=$mktcons;
					$fabPurArr['mkt']['knit']['amount']+=$mktamt;
				}
				if($fabPur_row[csf('fab_nature_id')]==3){
					$fabPurArr['mkt']['woven']['qty']+=$mktcons;
					$fabPurArr['mkt']['woven']['amount']+=$mktamt;
				}
			}
		}

		$sql_non_book = "select a.booking_no,a.item_category,a.exchange_rate,b.grey_fabric as grey_fab_qnty,b.finish_fabric as fin_fab_qnty,b.rate,b.amount from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=4 and a.fabric_source=2  and a.booking_date between '".$form_date."' and '".$to_date."'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_non_fabPur=sql_select($sql_non_book);
		$fabPurArr_non_acl=array();
		foreach($data_non_fabPur as $fabPur_row){
			if($fabPur_row[csf('item_category')]==2)
			{
				$fabPurArr_non_acl[$fabPur_row[csf('booking_no')]][2]['qty']+=$fabPur_row[csf('grey_fab_qnty')];
				if($fabPur_row[csf('grey_fab_qnty')]>0){
					$fabPurArr_non_acl[$fabPur_row[csf('booking_no')]][2]['amount']+=$fabPur_row[csf('amount')];
				}
			}
			if($fabPur_row[csf('item_category')]==3){
				$fabPurArr_non_acl[$fabPur_row[csf('booking_no')]][3]['qty']+=$fabPur_row[csf('grey_fab_qnty')];
				if($fabPur_row[csf('grey_fab_qnty')]>0){
					$fabPurArr_non_acl[$fabPur_row[csf('booking_no')]][3]['amount']+=$fabPur_row[csf('amount')];
				}
			}
		}
		foreach($fabPurArr_non_acl as $booking_no=>$fabPur_book ){
			foreach($fabPur_book as $cat=>$fabPur_acl ){
				 
				if($cat==2){
						 
						$fabPurArr['acl']['knit']['non_qty']+=$fabPur_acl['qty'];
						if($fabPur_acl['qty']>0){
							$fabPurArr['acl']['knit']['non_amount']+=$fabPur_acl['amount'];
						 
					}
				}
				if($cat==3){
					 
					$fabPurArr['acl']['woven']['non_qty']+=$fabPur_acl['qty'];
					if($fabPur_acl['qty']>0){
						$fabPurArr['acl']['woven']['non_amount']+=$fabPur_acl['amount'];
					      }
				}
			} //Cat
		}
		
		
		
		
		$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=1 and a.fabric_source=2 and b.po_break_down_id in(".implode(",",$exfactory_po_arr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_fabPur=sql_select($sql);
		
		/*foreach($data_fabPur as $fabPur_row){
			if($fabPur_row[csf('item_category')]==2){
				$fabPurArr['acl']['knit']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
				if($fabPur_row[csf('grey_fab_qnty')]>0){
					$fabPurArr['acl']['knit']['amount']+=$fabPur_row[csf('amount')];
				}
			}
			if($fabPur_row[csf('item_category')]==3){
				$fabPurArr['acl']['woven']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
				if($fabPur_row[csf('grey_fab_qnty')]>0){
					$fabPurArr['acl']['woven']['amount']+=$fabPur_row[csf('amount')];
				}
			}
		}*/
	
		$fabPurArr_acl=array();
		foreach($data_fabPur as $fabPur_row){
			if($fabPur_row[csf('item_category')]==2){
				$fabPurArr_acl[$fabPur_row[csf('po_break_down_id')]][2]['qty']+=$fabPur_row[csf('grey_fab_qnty')];
				if($fabPur_row[csf('grey_fab_qnty')]>0){
					$fabPurArr_acl[$fabPur_row[csf('po_break_down_id')]][2]['amount']+=$fabPur_row[csf('amount')];
				}
			}
			if($fabPur_row[csf('item_category')]==3){
				$fabPurArr_acl[$fabPur_row[csf('po_break_down_id')]][3]['qty']+=$fabPur_row[csf('grey_fab_qnty')];
				if($fabPur_row[csf('grey_fab_qnty')]>0){
					$fabPurArr_acl[$fabPur_row[csf('po_break_down_id')]][3]['amount']+=$fabPur_row[csf('amount')];
				}
			}
		}
		//print_r($fabPurArr_acl);die;
		foreach($fabPurArr_acl as $po_id=>$fabPur_po ){
			foreach($fabPur_po as $cat=>$fabPur_acl ){
				$cancelled_po=$po_information_arr[$po_id]['cancelled_status'];
				if($cat==2){
						if($cancelled_po==1)
						{
						$fabPurArr['acl']['knit']['qty']+=($fabPur_acl['qty']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
						if($fabPur_acl['qty']>0){
							$fabPurArr['acl']['knit']['amount']+=($fabPur_acl['amount']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
						}
						else //Cancel PO No
						{
							$fabPurArr['acl']['knit']['can_qty']+=($fabPur_acl['qty']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
						if($fabPur_acl['qty']>0){
							$fabPurArr['acl']['knit']['can_amount']+=($fabPur_acl['amount']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
						}
						}
					}
				}
				if($cat==3){
					if($cancelled_po==1)
						{
					$fabPurArr['acl']['woven']['qty']+=($fabPur_acl['qty']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
					if($fabPur_acl['qty']>0){
						$fabPurArr['acl']['woven']['amount']+=($fabPur_acl['amount']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
					}
						}
						else //Cancel
						{
							$fabPurArr['acl']['woven']['can_qty']+=($fabPur_acl['qty']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
							if($fabPur_acl['qty']>0){
								$fabPurArr['acl']['woven']['can_amount']+=($fabPur_acl['amount']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
							}
						}
				}
			}
		}
		
		//print_r($fabPurArr);die;
	// Fabric Purch End ============================
		$conversion= new conversion($condition);
		
		$knitQtyArr=$conversion->getQtyArray_by_jobFabricAndProcess();
		
		$knitAmtArr=$conversion->getAmountArray_by_jobFabricAndProcess();
		
		 $sql = "select a.id, a.job_no,a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, a.rate, a.amount,a.avg_finish_cons,a.status_active,b.cons_process,c.id as po_id from wo_po_break_down c, wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_fab_conv_cost_dtls b where a.job_no=b.job_no and a.id=b.fabric_description and  c.job_no_mst=a.job_no  and c.job_no_mst=b.job_no   and  c.id in(".implode(",",$exfactory_po_arr).") and b.cons_process=1 and a.status_active=1";

		$data_knit=sql_select($sql);
		foreach($data_knit as $row_knit){
			$cancelled_po=$po_information_arr[$row_knit[csf('po_id')]]['cancelled_status'];
			if($row_knit[csf('cons_process')]==1){
				$index="'".$row_knit[csf('body_part_id')]."_".$row_knit[csf('color_type_id')]."_".$row_knit[csf('fabric_description')]."_".$row_knit[csf('gsm_weight')]."'";
				if($cancelled_po==1)
				{
				$knitData_temp[$row_knit[csf('job_no')]][$index]['qty']=array_sum($knitQtyArr[$row_knit[csf('job_no')]][$row_knit[csf('id')]][$row_knit[csf('cons_process')]]);
				$knitData_temp[$row_knit[csf('job_no')]][$index]['amount']=array_sum($knitAmtArr[$row_knit[csf('job_no')]][$row_knit[csf('id')]][$row_knit[csf('cons_process')]]);
				}
				else //Cancel PO
				{
				$knitData_temp_can[$row_knit[csf('job_no')]][$index]['qty']=array_sum($knitQtyArr[$row_knit[csf('job_no')]][$row_knit[csf('id')]][$row_knit[csf('cons_process')]]);
				$knitData_temp_can[$row_knit[csf('job_no')]][$index]['amount']=array_sum($knitAmtArr[$row_knit[csf('job_no')]][$row_knit[csf('id')]][$row_knit[csf('cons_process')]]);
				}
			}
			
		}
		
		foreach($knitData_temp as $job_no=>$job_data)
		{
			foreach($job_data as $index=>$index_data)
			{
				//echo $job_no;
				$knitData['pre']['qty']+=($index_data['qty']*$exfactory_qty_job[$job_no])/$job_information_arr[$job_no]['job_quantity'];
				$knitData['pre']['amount']+=($index_data['amount']*$exfactory_qty_job[$job_no])/$job_information_arr[$job_no]['job_quantity'];
			}
			
		}
		
		unset($knitData_temp);
		
		foreach($knitData_temp_can as $job_no=>$job_data)
		{
			foreach($job_data as $index=>$index_data)
			{
				//echo $job_no;
				$knitData['pre']['can_qty']+=($index_data['qty']*$exfactory_qty_job[$job_no])/$job_information_arr[$job_no]['job_quantity'];
				$knitData['pre']['can_amount']+=($index_data['amount']*$exfactory_qty_job[$job_no])/$job_information_arr[$job_no]['job_quantity'];
			}
			
		}
		
		unset($knitData_temp_can);
		
		if(!empty($quotation_id_arr)){
			$sql = "select a.id,a.quotation_id, a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, b.cons_type,b.req_qnty,b.charge_unit from wo_pri_quo_fabric_cost_dtls a, wo_pri_quo_fab_conv_cost_dtls b where a.quotation_id=b.quotation_id and a.id=b.cost_head and  a.quotation_id in (".implode(",",$quotation_id_arr).") and b.cons_type=1";
			$data_knit=sql_select($sql);
			foreach($data_knit as $row_knit){
				
				if($row_knit[csf('cons_type')]==1){
					$quaCostingPerQty=$quation_data_arr[$row_knit[csf("quotation_id")]]['quaCostingPerQty'];
					$quaOfferQnty=$quation_data_arr[$row_knit[csf("quotation_id")]]['quaOfferQnty'];
					if($quaOfferQnty>$exfactory_qty_quotation[$row_knit[csf('quotation_id')]])
					{
						$quaOfferQnty=$exfactory_qty_quotation[$row_knit[csf('quotation_id')]];
					}
					
					$mktcons=($row_knit[csf('req_qnty')]/$quaCostingPerQty)*($quaOfferQnty);
					$mktamt=$mktcons*$row_knit[csf('charge_unit')];
					$knitData['mkt']['qty']+=$mktcons;
					$knitData['mkt']['amount']+=$mktamt;
				}
			}
		}

		$sql = "select  b.receive_qty,b.order_id,b.rate,b.amount,c.product_name_details from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=2 and b.order_id in(".implode(",",$exfactory_po_arr).") and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";

		$data_knit=sql_select($sql);
		foreach($data_knit as $row_knit){
			$knitData_temp['acl'][$row_knit[csf('order_id')]]['qty']+=$row_knit[csf('receive_qty')];
			$knitData_temp['acl'][$row_knit[csf('order_id')]]['amount']+=$row_knit[csf('amount')];
		}

		$sql = "select  sum(b.delivery_qty) as delivery_qty,b.order_id,sum(b.amount) as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=2 and b.order_id in(".implode(",",$exfactory_po_arr).") and a.party_source=1 and a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1 group by b.order_id";
		
		$data_knit=sql_select($sql);
		foreach($data_knit as $row_knit){

			$knitData_temp['acl'][$row_knit[csf('order_id')]]['qty']+=$row_knit[csf('delivery_qty')];
			$knitData_temp['acl'][$row_knit[csf('order_id')]]['amount']+=$row_knit[csf('amount')];
		}
		foreach($knitData_temp['acl'] as $po_id=>$val)
		{
			$cancelled_po=$po_information_arr[$po_id]['cancelled_status'];
			if($cancelled_po==1)
			{
			$knitData['acl']['qty']+=($val['qty']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
			$knitData['acl']['amount']+=($val['amount']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
			}
			else //Cancel PO
			{
			$knitData['acl']['can_qty']+=($val['qty']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
			$knitData['acl']['can_amount']+=($val['amount']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
			}
		}
		// Knitting Bill non
		$sql_outb_non = "select  b.receive_qty,b.order_id,b.rate,b.amount,c.product_name_details as descrip from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=2 and a.company_id=$company_id and a.bill_Date between '".$form_date."' and '".$to_date."' and a.bill_for=3 and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";

		$data_knit_outNon=sql_select($sql_outb_non);
		foreach($data_knit_outNon as $row_knit){
			$knitData_temp_non['acl'][$row_knit[csf('descrip')]]['qty']+=$row_knit[csf('receive_qty')];
			$knitData_temp_non['acl'][$row_knit[csf('descrip')]]['amount']+=$row_knit[csf('amount')];
		}
		
		$sql_non = "select  sum(b.delivery_qty) as delivery_qty,b.order_id,sum(b.amount) as amount,c.product_name_details as descrip from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=2   and a.bill_Date between '".$form_date."' and '".$to_date."' and a.bill_for=3 and a.party_source=1 and a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1 group by b.order_id";
		
		$data_non_knit=sql_select($sql_non);
		foreach($data_non_knit as $row_knit){

			$knitData_temp_non['acl'][$row_knit[csf('descrip')]]['qty']+=$row_knit[csf('delivery_qty')];
			$knitData_temp_non['acl'][$row_knit[csf('descrip')]]['amount']+=$row_knit[csf('amount')];
		}
		foreach($knitData_temp_non['acl'] as $desc_key=>$val)
		{
			$knitData['acl']['non_qty']+=$val['qty'];
			$knitData['acl']['non_amount']+=$val['amount'];
			 
		}
		
		
	// Fabric Kniting  End============================
	// Fabric Dye Finish  ==============================
		$finishData=array();
		$conversion= new conversion($condition);
		$finishQtyArr=$conversion->getQtyArray_by_orderFabricAndProcess();
		//$conversion= new conversion($condition);
		$finishAmtArr=$conversion->getAmountArray_by_orderFabricAndProcess();

		$sql = "select a.id, a.job_no,a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, a.rate, a.amount,a.avg_finish_cons,a.status_active,b.cons_process,d.id as po_id from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_fab_conv_cost_dtls b,wo_po_break_down d where a.job_id=b.job_id and  a.job_id=d.job_id and  b.job_id=d.job_id and a.id=b.fabric_description  and  d.id in(".implode(",",$exfactory_po_arr).") and b.cons_process not in(1,2,30,35)";

		$data_finish=sql_select($sql);
		foreach($data_finish as $row_finish){
			$cancelled_po=$po_information_arr[$row_finish[csf('po_id')]]['cancelled_status'];
			$index="'".$row_finish[csf('body_part_id')]."_".$row_finish[csf('color_type_id')]."_".$row_finish[csf('fabric_description')]."_".$row_finish[csf('gsm_weight')]."'";
			if($cancelled_po==1)
			{
			$finishData_temp[$row_finish[csf('job_no')]][$index]['qty']=array_sum($finishQtyArr[$row_finish[csf('po_id')]][$row_finish[csf('id')]][$row_finish[csf('cons_process')]]);
			$finishData_temp[$row_finish[csf('job_no')]][$index]['amount']=array_sum($finishAmtArr[$row_finish[csf('po_id')]][$row_finish[csf('id')]][$row_finish[csf('cons_process')]]);
			}
			else //Cancel PO
			{
			 $finishData_temp_can[$row_finish[csf('job_no')]][$index]['qty']=array_sum($finishQtyArr[$row_finish[csf('po_id')]][$row_finish[csf('id')]][$row_finish[csf('cons_process')]]);
			$finishData_temp_can[$row_finish[csf('job_no')]][$index]['amount']=array_sum($finishAmtArr[$row_finish[csf('po_id')]][$row_finish[csf('id')]][$row_finish[csf('cons_process')]]);
			}
		}
		unset($data_finish);
		foreach($finishData_temp as $job_id=>$job_data)
		{
			foreach($job_data as $f_index=>$f_index_data)
			{
			$finishData['pre']['qty']+=($f_index_data['qty']*$exfactory_qty_job[$job_id])/$job_information_arr[$job_id]['job_quantity'];
			$finishData['pre']['amount']+=($f_index_data['amount']*$exfactory_qty_job[$job_id])/$job_information_arr[$job_id]['job_quantity'];
			}
		}
		unset($finishData_temp);	
		
		foreach($finishData_temp_can as $job_id=>$job_data)
		{
			foreach($job_data as $f_index=>$f_index_data)
			{
			$finishData['pre']['can_qty']+=($f_index_data['qty']*$exfactory_qty_job[$job_id])/$job_information_arr[$job_id]['job_quantity'];
			$finishData['pre']['can_amount']+=($f_index_data['amount']*$exfactory_qty_job[$job_id])/$job_information_arr[$job_id]['job_quantity'];
			}
		}
		unset($finishData_temp_can);	
		
		
		if(!empty($quotation_id_arr)){
			$sql = "select a.id,a.quotation_id, a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, b.cons_type,b.req_qnty,b.charge_unit from wo_pri_quo_fabric_cost_dtls a, wo_pri_quo_fab_conv_cost_dtls b where a.quotation_id=b.quotation_id and a.id=b.cost_head and  a.quotation_id in (".implode(",",$quotation_id_arr).") and b.cons_type not in(1,2,30,35)";

			$data_finish=sql_select($sql);
			foreach($data_finish as $row_finish){
				$quaCostingPerQty=$quation_data_arr[$row_finish[csf("quotation_id")]]['quaCostingPerQty'];
				$quaOfferQnty=$quation_data_arr[$row_finish[csf("quotation_id")]]['quaOfferQnty'];
				if($quaOfferQnty>$exfactory_qty_quotation[$row_finish[csf('quotation_id')]])
				{
					$quaOfferQnty=$exfactory_qty_quotation[$row_finish[csf('quotation_id')]];
				}
				$mktcons=($row_finish[csf('req_qnty')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row_finish[csf('charge_unit')];
				$finishData['mkt']['qty']+=$mktcons;
				$finishData['mkt']['amount']+=$mktamt;
			}
		}
		// Non Bill Dying Finishing
			$sql_non_dying = "select  sum(b.receive_qty) as receive_qty,b.order_id,sum(b.amount) as amount from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=4  and a.bill_Date between '".$form_date."' and '".$to_date."' and  a.is_deleted=0 and a.status_active=1 and a.bill_for=3  and a.company_id=$company_id and  b.is_deleted=0 and b.status_active=1 group by b.order_id";
		//echo $sql;die;
		$data_finish_dyingNon=sql_select($sql_non_dying);
		foreach($data_finish_dyingNon as $row_finish){
			$finishData_temp_acl_non[$row_finish[csf('order_id')]]['qty']+=$row_finish[csf('receive_qty')];
			$finishData_temp_acl_non[$row_finish[csf('order_id')]]['amount']+=$row_finish[csf('amount')];
		}
		
		$sql_dying_out = "select  b.body_part_id,b.order_id,b.receive_qty,b.rate,b.amount from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id  and a.process_id=4 and b.sub_process_id in(31,35) and a.bill_Date between '".$form_date."' and '".$to_date."' and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1 and a.bill_for=3 and a.company_id=$company_id";
		$data_aop_non=sql_select($sql_dying_out);
		foreach($data_aop_non as $row_aop){
			$finishData_temp_acl_non[$row_aop[csf('order_id')]]['qty']+=$row_aop[csf('receive_qty')];
			$finishData_temp_acl_non[$row_aop[csf('order_id')]]['amount']+=$row_aop[csf('amount')];
		}
		
		foreach($finishData_temp_acl_non as $po_id=>$p_val)
		{
			$finishData['acl']['non_qty']+=$p_val['qty'];
			$finishData['acl']['non_amount']+=$p_val['amount'];
			
		}
		
		unset($finishData_temp_acl_non);
		//Dying Fin Non End 
		
		//print_r($finishData);die;
		$sql = "select  sum(b.receive_qty) as receive_qty,b.order_id,sum(b.amount) as amount from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=4 and b.order_id in(".implode(",",$exfactory_po_arr).") and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1 group by b.order_id";
		//echo $sql;die;
		$data_finish=sql_select($sql);
		foreach($data_finish as $row_finish){
			//$cancelled_po=$po_information_arr[$row_finish[csf('order_id')]]['cancelled_status'];
			//if($cancelled_po==1)
			//{
			$finishData_temp_acl[$row_finish[csf('order_id')]]['qty']+=$row_finish[csf('receive_qty')];
			$finishData_temp_acl[$row_finish[csf('order_id')]]['amount']+=$row_finish[csf('amount')];
			//}
			/*else //Cancel PO
			{
			$finishData_temp_acl[$row_finish[csf('order_id')]]['can_qty']+=$row_finish[csf('receive_qty')];
			$finishData_temp_acl[$row_finish[csf('order_id')]]['can_amount']+=$row_finish[csf('amount')];
			}*/
		}
	// Fabric Dye Finish  End============================
	// Fabric AOP  ==============================
		$aopData=array();
		$aopQtyArr=$conversion->getQtyArray_by_orderFabricAndProcess();
		$aopAmtArr=$conversion->getAmountArray_by_orderFabricAndProcess();

		$sql = "select a.id, a.job_no,a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, a.rate, a.amount,a.avg_finish_cons,a.status_active,b.cons_process,d.id as po_id from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_fab_conv_cost_dtls b,wo_po_break_down d where a.job_id=b.job_id and a.job_id=d.job_id and a.id=b.fabric_description and  d.id in(".implode(",",$exfactory_po_arr).")  and b.cons_process=35";
		$data_aop=sql_select($sql);
		
		foreach($data_aop as $row_aop){
			$index="'".$row_aop[csf('body_part_id')]."_".$row_aop[csf('color_type_id')]."_".$row_aop[csf('fabric_description')]."_".$row_aop[csf('gsm_weight')]."'";
			$finishData_temp[$row_aop[csf('job_no')]][$index]['qty']+=array_sum($aopQtyArr[$row_aop[csf('po_id')]][$row_aop[csf('id')]][$row_aop[csf('cons_process')]]);
			$finishData_temp[$row_aop[csf('job_no')]][$index]['amount']+=array_sum($aopAmtArr[$row_aop[csf('po_id')]][$row_aop[csf('id')]][$row_aop[csf('cons_process')]]);
		}
		
		
		foreach($finishData_temp as $job_id=>$job_data){
			foreach($job_data as $aop_index=>$aop_index_data){
				$finishData['pre']['qty']+=($aop_index_data['qty']*$exfactory_qty_job[$job_id])/$job_information_arr[$job_id]['job_quantity'];
				$finishData['pre']['amount']+=($aop_index_data['amount']*$exfactory_qty_job[$job_id])/$job_information_arr[$job_id]['job_quantity'];
			}
		}
unset($finishData_temp);

		if(!empty($quotation_id_arr)){
			$sql = "select a.id,a.quotation_id, a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, b.cons_type,b.req_qnty,b.charge_unit from wo_pri_quo_fabric_cost_dtls a, wo_pri_quo_fab_conv_cost_dtls b where a.quotation_id=b.quotation_id and a.id=b.cost_head and  a.quotation_id in (".implode(",",$quotation_id_arr).") and b.cons_type=35";
			
			$data_aop=sql_select($sql);
			foreach($data_aop as $row_aop){
				
				if($row_aop[csf('cons_type')]==35){
					$quaCostingPerQty=$quation_data_arr[$row_aop[csf("quotation_id")]]['quaCostingPerQty'];
					$quaOfferQnty=$quation_data_arr[$row_aop[csf("quotation_id")]]['quaOfferQnty'];
					if($quaOfferQnty>$exfactory_qty_quotation[$row_aop[csf('quotation_id')]])
					{
						$quaOfferQnty=$exfactory_qty_quotation[$row_aop[csf('quotation_id')]];
					}
					$mktcons=($row_aop[csf('req_qnty')]/$quaCostingPerQty)*($quaOfferQnty);
					$mktamt=$mktcons*$row_aop[csf('charge_unit')];
					$finishData['mkt']['qty']+=$mktcons;
					$finishData['mkt']['amount']+=$mktamt;
				}
			}
		}

		$sql = "select  b.body_part_id,b.order_id,b.receive_qty,b.rate,b.amount from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id  and a.process_id=4 and b.sub_process_id in(31,35) and b.order_id in(".implode(",",$exfactory_po_arr).") and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_aop=sql_select($sql);
		foreach($data_aop as $row_aop){
			$finishData_temp_acl[$row_aop[csf('order_id')]]['qty']+=$row_aop[csf('receive_qty')];
			$finishData_temp_acl[$row_aop[csf('order_id')]]['amount']+=$row_aop[csf('amount')];
		}
		
		foreach($finishData_temp_acl as $po_id=>$p_val)
		{
			$cancelled_po=$po_information_arr[$po_id]['cancelled_status'];
			if($cancelled_po==1)
			{
			$finishData['acl']['qty']+=($p_val['qty']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
			$finishData['acl']['amount']+=($p_val['amount']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
			}
			else
			{
			$finishData['acl']['can_qty']+=($p_val['qty']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
			$finishData['acl']['can_amount']+=($p_val['amount']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
			}
		}
		
		unset($finishData_temp_acl);

	// Fabric AOP  End============================
	// Trim Cost ============================

		$trim_groupArr=array();
		$conversion_factor=sql_select("select id,item_name,trim_uom,conversion_factor from  lib_item_group  ");
		foreach($conversion_factor as $row_f){
			$trim_groupArr[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
			$trim_groupArr[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
			$trim_groupArr[$row_f[csf('id')]]['item_name']=$row_f[csf('item_name')];
		}

		$trimData=array();
		$trimQtyArr=$trim->getQtyArray_by_jobAndPrecostdtlsid();

		$trimAmtArr=$trim->getAmountArray_by_jobAndPrecostdtlsid();

		$sql = "select b.id as po_id , a.id, a.job_no, a.trim_group, a.description,a.brand_sup_ref, a.cons_uom, a.cons_dzn_gmts, a.rate, a.amount,a.apvl_req, a.nominated_supp,a.status_active from wo_pre_cost_trim_cost_dtls a,wo_po_break_down b  where a.job_id=b.job_id and  b.id in(".implode(",",$exfactory_po_arr).") and a.status_active=1";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$cancelled_po=$po_information_arr[$row[csf('po_id')]]['cancelled_status'];
			if($cancelled_po==1)
			{
			$trimData['pre']['qty']+=($trimQtyArr[$row[csf('job_no')]][$row[csf('id')]]*$exfactory_qty_job[$row[csf('job_no')]])/$job_information_arr[$row[csf('job_no')]]['job_quantity'];
			$trimData['pre']['amount']+=($trimAmtArr[$row[csf('job_no')]][$row[csf('id')]]*$exfactory_qty_job[$row[csf('job_no')]])/$job_information_arr[$row[csf('job_no')]]['job_quantity'];
			}
			else //Cancel PO
			{
			$trimData['pre']['can_qty']+=($trimQtyArr[$row[csf('job_no')]][$row[csf('id')]]*$exfactory_qty_job[$row[csf('job_no')]])/$job_information_arr[$row[csf('job_no')]]['job_quantity'];
			$trimData['pre']['can_amount']+=($trimAmtArr[$row[csf('job_no')]][$row[csf('id')]]*$exfactory_qty_job[$row[csf('job_no')]])/$job_information_arr[$row[csf('job_no')]]['job_quantity'];	
			}

		}
		
		if(!empty($quotation_id_arr)){
			$sql = "select id,quotation_id,  trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active from wo_pri_quo_trim_cost_dtls  where quotation_id in (".implode(",",$quotation_id_arr).")";
			$data_array=sql_select($sql);
			foreach($data_array as $row){
				$quaCostingPerQty=$quation_data_arr[$row[csf("quotation_id")]]['quaCostingPerQty'];
				$quaOfferQnty=$quation_data_arr[$row[csf("quotation_id")]]['quaOfferQnty'];
				
				if($quaOfferQnty>$exfactory_qty_quotation[$row[csf('quotation_id')]])
				{
					$quaOfferQnty=$exfactory_qty_quotation[$row[csf('quotation_id')]];
				}
				$mktcons=($row[csf('cons_dzn_gmts')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row[csf('rate')];
				$trimData['mkt']['qty']+=$mktcons;
				$trimData['mkt']['amount']+=$mktamt;
			}
		}

		$trimsRecArr=array();
		$trimsRecRateArr=array();
		$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,b.quantity as quantity,a.rate,a.cons_rate  from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in(".implode(",",$exfactory_po_arr).") ");
		foreach($receive_qty_data as $row){
		
			
			$trimsRecArr[$row[csf('po_breakdown_id')]]['qty']+=$row[csf('quantity')]*$trim_groupArr[$row[csf('item_group_id')]]['con_factor'];
			$trimsRecRateArr[$row[csf('item_group_id')]]['rate']=$row[csf('rate')];
			$trimsRecArr[$row[csf('po_breakdown_id')]]['amount']+=($row[csf('quantity')]*$trim_groupArr[$row[csf('item_group_id')]]['con_factor'])*$row[csf('rate')];
			$trimsRecRateArr[$row[csf('item_group_id')]]['cons_uom']=$trim_groupArr[$row[csf('item_group_id')]]['cons_uom'];
		}
		
		$trimsRecReArr=array();
		$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id,d.quantity as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  d.po_breakdown_id in(".implode(",",$exfactory_po_arr).")");
		foreach($receive_rtn_qty_data as $row){
			$trimsRecReArr[$row[csf('po_breakdown_id')]]['qty']+=$row[csf('quantity')]*$trim_groupArr[$row[csf('item_group_id')]]['con_factor'];
			$trimsRecReArr[$row[csf('po_breakdown_id')]]['amount']+=($row[csf('quantity')]*$trim_groupArr[$row[csf('item_group_id')]]['con_factor'])*$trimsRecRateArr[$row[csf('item_group_id')]]['rate'];
		}
		
		
		
		$trim_data_acl=array();
		foreach($trimsRecArr as $ind=>$value){
			$trim_data_acl[$ind]['qty']+=$trimsRecArr[$ind]['qty']-$trimsRecReArr[$ind]['qty'];
			$trim_data_acl[$ind]['amount']+=$trimsRecArr[$ind]['amount']-$trimsRecReArr[$ind]['amount'];
		}
		/*
		$TrimsDataArray=sql_select("select b.po_breakdown_id,sum(b.quantity) as qty,sum(b.quantity*a.cons_rate) as amount
					from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.transaction_type=2 and b.entry_form =25 and b.trans_type=2 and a.item_category in(4)  and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.po_breakdown_id in(".implode(",",$exfactory_po_arr).") group by b.po_breakdown_id");
		$trim_data_acl=array();
		foreach($TrimsDataArray as $invRow){
			$trim_data_acl[$invRow[csf('po_breakdown_id')]]['qty']+=$invRow[csf('qty')];
			$trim_data_acl[$invRow[csf('po_breakdown_id')]]['amount']+=$invRow[csf('amount')];
		}*/
		//echo "<pre>";
		//print_r($trim_data_acl);die;
		foreach($trim_data_acl as $po_id=>$ta_val)
		{
				$cancelled_po=$po_information_arr[$po_id]['cancelled_status'];
				if($cancelled_po==1)
				{
				$trimData['acl']['qty']+=($ta_val['qty']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
				$trimData['acl']['amount']+=($ta_val['amount']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
				}
				else
				{
				$trimData['acl']['can_qty']+=($ta_val['qty']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
				$trimData['acl']['can_amount']+=($ta_val['amount']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
				}
			
		}
	// Trim Cost End============================
	// Embl Cost ============================
		$embData=array();
		$embQtyArr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
		//$emblishment= new emblishment($condition);
		$embAmtArr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
		$sql = "select b.id as po_id,a.id, a.job_no, a.emb_name, a.emb_type, a.cons_dzn_gmts, a.rate, a.amount,a.status_active from wo_pre_cost_embe_cost_dtls a,wo_po_break_down b  where a.job_id=b.job_id and  b.id in(".implode(",",$exfactory_po_arr).") and a.emb_name !=3";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$cancelled_po=$po_information_arr[$row[csf('po_id')]]['cancelled_status'];
			if($cancelled_po==1)
			{
			$embData['pre']['qty']+=($embQtyArr[$row[csf('job_no')]][$row[csf('id')]]*$exfactory_qty_job[$row[csf('job_no')]])/$job_information_arr[$row[csf('job_no')]]['job_quantity'];
			$embData['pre']['amount']+=($embAmtArr[$row[csf('job_no')]][$row[csf('id')]]*$exfactory_qty_job[$row[csf('job_no')]])/$job_information_arr[$row[csf('job_no')]]['job_quantity'];
			}
			else //Cancel PO
			{
			$embData['pre']['can_qty']+=($embQtyArr[$row[csf('job_no')]][$row[csf('id')]]*$exfactory_qty_job[$row[csf('job_no')]])/$job_information_arr[$row[csf('job_no')]]['job_quantity'];
			$embData['pre']['can_amount']+=($embAmtArr[$row[csf('job_no')]][$row[csf('id')]]*$exfactory_qty_job[$row[csf('job_no')]])/$job_information_arr[$row[csf('job_no')]]['job_quantity'];
			}
		}

		if(!empty($quotation_id_arr)){
			$sql = "select id, emb_name, emb_type, cons_dzn_gmts, rate,quotation_id, amount,status_active from wo_pri_quo_embe_cost_dtls where quotation_id in (".implode(",",$quotation_id_arr).") and emb_name !=3";
			
			$data_array=sql_select($sql);
			foreach($data_array as $row){
				$quaCostingPerQty=$quation_data_arr[$row[csf("quotation_id")]]['quaCostingPerQty'];
				$quaOfferQnty=$quation_data_arr[$row[csf("quotation_id")]]['quaOfferQnty'];
				
				if($quaOfferQnty>$exfactory_qty_quotation[$row[csf('quotation_id')]])
				{
					$quaOfferQnty=$exfactory_qty_quotation[$row[csf('quotation_id')]];
				}
					
				$mktcons=($row[csf('cons_dzn_gmts')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*($row[csf('rate')]*1);
				$embData['mkt']['qty']+=$mktcons;
				$embData['mkt']['amount']+=$mktamt;
			}
		}
		
		$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and b.emblishment_name !=3 and b.po_break_down_id in(".implode(",",$exfactory_po_arr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array=sql_select($sql);
		$embData_acl=array();
		foreach($data_array as $row){
			
			$embData_acl[$row[csf('po_break_down_id')]]['qty']+=$row[csf('wo_qnty')];
			$embData_acl[$row[csf('po_break_down_id')]]['amount']+=($row[csf('amount')]);
		}
		
		foreach($embData_acl as $po_id=>$em_val){
			$cancelled_po=$po_information_arr[$po_id]['cancelled_status'];
			if($cancelled_po==1)
			{
			$embData['acl']['qty']+=($em_val['qty']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
			$embData['acl']['amount']+=($em_val['amount']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
			}
			else //Cancel PO
			{
			$embData['acl']['can_qty']+=($em_val['qty']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
			$embData['acl']['can_amount']+=($em_val['amount']*$exfactoryQtyArr[$po_id])/$po_information_arr[$po_id]['order_quantity'];
			}
		}
	// Embl Cost End ============================
	// Wash Cost ============================
		$washData=array();
		$washQtyArr=$wash->getQtyArray_by_jobAndEmblishmentid();
		$washAmtArr=$wash->getAmountArray_by_jobAndEmblishmentid();
		$sql = "select b.id as po_id, a.id, a.job_no, a.emb_name,a.emb_type, a.cons_dzn_gmts,a.rate, a.amount,a.status_active from wo_pre_cost_embe_cost_dtls a,wo_po_break_down b  where a.job_id=b.job_id and  b.id in(".implode(",",$exfactory_po_arr).") and a.emb_name =3 and a.status_active=1 and b.status_active=1";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$cancelled_po=$po_information_arr[$row[csf('po_id')]]['cancelled_status'];
			if($cancelled_po==1)
			{
			$washData['pre']['qty']+=$washQtyArr[$row[csf('job_no')]][$row[csf('id')]];
			$washData['pre']['amount']+=$washAmtArr[$row[csf('job_no')]][$row[csf('id')]];
			}
			else
			{
			$washData['pre']['can_qty']+=$washQtyArr[$row[csf('job_no')]][$row[csf('id')]];
			$washData['pre']['can_amount']+=$washAmtArr[$row[csf('job_no')]][$row[csf('id')]];
			}
		}
		$sql_commi="select b.id as po_id,c.job_no, c.commission_amount,c.commision_rate  from   wo_po_break_down b,wo_pre_cost_commiss_cost_dtls c where  b.job_no_mst=c.job_no  and b.status_active=1  and b.is_deleted=0 and b.id in(".implode(",",$exfactory_po_arr).") and c.commission_amount>0 and c.status_active=1 ";
		 
		$result_sql_commi=sql_select($sql_commi);
		$commi_arr=array();
		$commission_data_array=$commision->getAmountArray_by_order();
		foreach($result_sql_commi as $row){
			$commissionData['pre']['amount']+= $commission_data_array[$row[csf("po_id")]];
			$commissionData['pre']['rate']+= $row[csf("commision_rate")];
		}
		$sql_commercial="select b.id as po_id,c.job_no, c.amount,c.rate  from   wo_po_break_down b,wo_pre_cost_comarci_cost_dtls c where  b.job_no_mst=c.job_no  and b.status_active=1  and b.is_deleted=0 and b.id in(".implode(",",$exfactory_po_arr).") and c.amount>0 and c.status_active=1 ";
		 
		$result_commercial=sql_select($sql_commercial);
		//$commi_arr=array();
			//$commercial= new commercial($condition);
		$commercial_data_array=$commercial->getAmountArray_by_order();
		foreach($result_commercial as $row){
			$commercialData['pre']['amount']+= $commercial_data_array[$row[csf("po_id")]];
			$commercialData['pre']['rate']+= $row[csf("rate")];
		}
		$sql_other="select b.id as po_id,c.job_no from   wo_po_break_down b,wo_pre_cost_dtls c where  b.job_no_mst=c.job_no  and b.status_active=1  and b.is_deleted=0 and b.id in(".implode(",",$exfactory_po_arr).") and c.status_active=1  ";
		 
		$result_other=sql_select($sql_other);
		//$commi_arr=array();
			$other= new other($condition);
		$other_costArr=$other->getAmountArray_by_order();
		foreach($result_other as $row){
			$otherData['pre']['freight']+= $other_costArr[$row[csf("po_id")]]['freight'];
			$otherData['pre']['lab_test']+= $other_costArr[$row[csf("po_id")]]['lab_test'];
			$otherData['pre']['inspection']+= $other_costArr[$row[csf("po_id")]]['inspection'];
			$otherData['pre']['currier_pre_cost']+= $other_costArr[$row[csf("po_id")]]['currier_pre_cost'];
			//$otherData['pre']['rate']+= $row[csf("rate")];
		}
		//
		
		if(!empty($quotation_id_arr)){
			$sql = "select id, emb_name, emb_type, cons_dzn_gmts, rate,quotation_id, amount,status_active from wo_pri_quo_embe_cost_dtls where quotation_id in (".implode(",",$quotation_id_arr).") and emb_name =3";
			$data_array=sql_select($sql);
			foreach($data_array as $row){
				$quaCostingPerQty=$quation_data_arr[$row[csf("quotation_id")]]['quaCostingPerQty'];
				$quaOfferQnty=$quation_data_arr[$row[csf("quotation_id")]]['quaOfferQnty'];
				if($quaOfferQnty>$exfactory_qty_quotation[$row[csf('quotation_id')]])
				{
					$quaOfferQnty=$exfactory_qty_quotation[$row[csf('quotation_id')]];
				}
				$mktcons=($row[csf('cons_dzn_gmts')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row[csf('rate')];
				$washData['mkt']['qty']+=$mktcons;
				$washData['mkt']['amount']+=$mktamt;
			}
		}
		$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and b.emblishment_name =3 and b.po_break_down_id in(".implode(",",$exfactory_po_arr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array=sql_select($sql);
		$wash_data_acl=array();
		foreach($data_array as $row){
			$wash_data_acl[$row[csf('po_break_down_id')]]['qty']+=$row[csf('wo_qnty')];
			$wash_data_acl[$row[csf('po_break_down_id')]]['amount']+=($row[csf('amount')]);
		}
		
		foreach($wash_data_acl as $order_id=>$order_val){
				$cancelled_po=$po_information_arr[$order_id]['cancelled_status'];
				if($cancelled_po==1)
				{
				$washData['acl']['qty']+=($order_val['qty']*$exfactoryQtyArr[$order_id])/$po_information_arr[$order_id]['order_quantity'];
				$washData['acl']['amount']+=($order_val['amount']*$exfactoryQtyArr[$order_id])/$po_information_arr[$order_id]['order_quantity'];
				}
				else //Cancel PO
				{
					$washData['acl']['can_qty']+=($order_val['qty']*$exfactoryQtyArr[$order_id])/$po_information_arr[$order_id]['order_quantity'];
					$washData['acl']['can_amount']+=($order_val['amount']*$exfactoryQtyArr[$order_id])/$po_information_arr[$order_id]['order_quantity'];
				}
		}

	$post_cost_data_arr=array();

	$post_cost_data_arr['ShipDateArr']				=$ShipDateArr;
	$post_cost_data_arr['exfactoryQty']				=$exfactoryQty;
	
	$post_cost_data_arr['exfactoryValue']			=$exfactoryValue;
	$post_cost_data_arr['shortExcessQty']			=$shortExcessQty;
	$post_cost_data_arr['shortExcessValue']			=$shortExcessValue;
	$post_cost_data_arr['shipingStatus']			=$shipment_status[$shipingStatus];
	$post_cost_data_arr['YarnData']					=$YarnData;
	$post_cost_data_arr['conv_yarn_arr']			=$conv_yarn_arr;
	$post_cost_data_arr['poNumberArr']				=$poNumberArr;
	$post_cost_data_arr['fabPurArr']				=$fabPurArr;
	
	$post_cost_data_arr['finishData']				=$finishData;
	$post_cost_data_arr['trimData']					=$trimData;
	$post_cost_data_arr['unitPrice']				=$unitPrice;
	$post_cost_data_arr['gmtsItemArr']				=$gmtsItemArr;
	$post_cost_data_arr['knitData']					=$knitData;
	$post_cost_data_arr['aopData']					=$aopData;
	$post_cost_data_arr['grey_fab_trans_qty_acl']	=$grey_fab_trans_qty_acl;
	$post_cost_data_arr['tot_grey_fab_cost_acl']	=$tot_grey_fab_cost_acl;

	$post_cost_data_arr['tot_fin_fab_transfer_qty']	=$tot_fin_fab_transfer_qty;
	$post_cost_data_arr['tot_fin_fab_transfer_cost']=$tot_fin_fab_transfer_cost;
	$post_cost_data_arr['grey_fab_trans_qty_acl']	=$grey_fab_trans_qty_acl;
	$post_cost_data_arr['tot_fin_fab_transfer_qty']	=$tot_fin_fab_transfer_qty;
	$post_cost_data_arr['tot_fin_fab_transfer_cost']=$tot_fin_fab_transfer_cost;
	//$post_cost_data_arr['trim_groupArr']			=$trim_groupArr;
	$post_cost_data_arr['embData']					=$embData;
	$post_cost_data_arr['washData']					=$washData;
	$post_cost_data_arr['commissionData']			=$commissionData;
	$post_cost_data_arr['commercialData']			=$commercialData;
	
	
	$post_cost_data_arr['otherData']				=$otherData;
	$post_cost_data_arr['exfactoryQty']				=$exfactoryQty;
	$post_cost_data_arr['quaOfferQnty']				=$quaOfferQnty;
	$post_cost_data_arr['quaPriceWithCommnPcs']		=$quaPriceWithCommnPcs;

/*
die;*/
//echo "<pre>";
//print_r($post_cost_data_arr);die;

//echo  json_encode($post_cost_data_arr);

 echo json_encode($post_cost_data_arr);die;


