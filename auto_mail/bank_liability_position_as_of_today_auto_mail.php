<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
//require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

require_once('../includes/class4/class.conditions.php');
require_once('../includes/class4/class.reports.php');
require_once('../includes/class4/class.yarns.php');
require_once('../includes/class4/class.trims.php');
require_once('../includes/class4/class.emblishments.php');
require_once('../includes/class4/class.washes.php');
require_once('../includes/class4/class.fabrics.php');

$company_library=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
$buyer_library=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
// $supplier_library = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");

$team_leader=return_library_array("select id,team_leader_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0 order by team_leader_name","id","team_leader_name");
$dealing_merchand=return_library_array("select id,team_member_name from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name","id","team_member_name");
$seource_des_array=array(3=>"Non EPZ",4=>"Non EPZ",5=>"Abroad",6=>"Abroad",11=>"EPZ",12=>"EPZ");

		
	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);	
	if($_REQUEST['view_date']){
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
	}
	$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-2 day', strtotime($current_date))),'','',1); 	
	$date_cond	=" and a.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";

	

foreach($company_library as $compid=>$compname)
{

	ob_start();	

		$cbo_company_name=$compid;	
		$txt_exchange_rate=83;
		//echo $hide_year;die;
		$company_arr=return_library_array( "select company_name,id from  lib_company",'id','company_name');
		$bank_arr=return_library_array( "select bank_name,id from lib_bank",'id','bank_name');
		$sql_cond_payment="";
		if($cbo_company_name>0) $sql_cond_payment=" and a.company_id=$cbo_company_name";
		//$sql_payment="select a.lc_id, a.invoice_id, b.accepted_ammount from com_import_payment_mst a, com_import_payment b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $sql_cond_payment";
		$sql_payment="select a.lc_id, b.invoice_id, b.accepted_ammount 
		from com_import_payment b left join com_import_payment_mst a on b.mst_id=a.id and a.status_active=1  $sql_cond_payment  
		where b.status_active=1 and b.is_deleted=0";
		//echo $sql_payment;
		$sql_payment_result=sql_select($sql_payment);
		$invoice_wise_payment=array();
		foreach($sql_payment_result as $row)
		{
			$invoice_wise_payment[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")];
		}
		//echo $invoice_wise_payment[984];die;
		unset($sql_payment_result);
		$invoice_payment_atsite=return_library_array( "select invoice_id, sum(accepted_ammount) as accepted_ammount from com_import_payment_com where status_active=1 and is_deleted=0 group by invoice_id",'invoice_id','accepted_ammount');
		
		$sql_cond="";
		if($cbo_company_name>0) $sql_cond=" and a.importer_id=$cbo_company_name";
		if($cbo_lein_bank>0) $sql_cond.=" and a.issuing_bank_id=$cbo_lein_bank";
		
		if($db_type==0)
		{
			$ifdbc_edf_sql="select a.id as btb_lc_id, a.importer_id, a.issuing_bank_id, a.lc_category, a.lc_number, a.lc_value, a.maturity_from_id, a.payterm_id, a.lc_type_id, b.id as import_inv_id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, sum(c.current_acceptance_value) as edf_loan_value, 1 as type 
			from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
			where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and CONVERT(a.lc_category,SIGNED)>0 $sql_cond 
			group by a.id, a.importer_id, a.issuing_bank_id, a.lc_category, a.lc_number, a.lc_value, a.maturity_from_id, a.payterm_id, a.lc_type_id, b.id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source
			order by btb_lc_id";
		}
		else
		{
			$ifdbc_edf_sql="select a.id as btb_lc_id, a.importer_id, a.issuing_bank_id, a.lc_category, a.lc_number, a.lc_value, a.maturity_from_id, a.payterm_id, a.lc_type_id, b.id as import_inv_id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, sum(c.current_acceptance_value) as edf_loan_value, 1 as type 
			from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
			where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and to_number(a.lc_category)>0 $sql_cond 
			group by a.id, a.importer_id, a.issuing_bank_id, a.lc_category, a.lc_number, a.lc_value, a.maturity_from_id, a.payterm_id, a.lc_type_id, b.id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source
			order by btb_lc_id";
		}
		
		// echo $ifdbc_edf_sql;die;
		$ifdbc_edf_sql_result=sql_select($ifdbc_edf_sql);
		$ifdbc_edf_data=array();$lc_wise_payment=array();
		foreach($ifdbc_edf_sql_result as $row)
		{
			if($row[csf("lc_type_id")]==2)
			{
				$paid_amount=0;
				if($row[csf("payterm_id")]==1) 
				{
					if($row[csf("edf_paid_date")]!="" && $row[csf("edf_paid_date")]!="0000-00-00")
					{
						$paid_amount=$invoice_wise_payment[$row[csf("import_inv_id")]];
					}
				}
				else
				{
					$paid_amount=$invoice_wise_payment[$row[csf("import_inv_id")]];
				}
				$margin_btb_data[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]+=$row[csf("edf_loan_value")]-$paid_amount;
				$margin_btb_bank_total[$row[csf("issuing_bank_id")]]+=$row[csf("edf_loan_value")]-$paid_amount;
				/*$pe_amt=$row[csf("edf_loan_value")]-$paid_amount;
				if($row[csf("import_inv_id")]==984)
				{
					$test_datasss=$paid_amount."=".$pe_amt;
				}*/
			}
			else
			{
				$lc_wise_payment[$row[csf("btb_lc_id")]]+=$row[csf("edf_loan_value")];
				$maturity_date="";
				if($row[csf("maturity_from_id")]==1) $maturity_date=$row[csf("bank_acc_date")];
				else if($row[csf("maturity_from_id")]==2 || $row[csf("maturity_from_id")]==5) $maturity_date=$row[csf("shipment_date")];
				else if($row[csf("maturity_from_id")]==3) $maturity_date=$row[csf("nagotiate_date")];
				else if($row[csf("maturity_from_id")]==4) $maturity_date=$row[csf("bill_date")];
				else $maturity_date="";
				if($row[csf("retire_source")]==30 || $row[csf("retire_source")]==31)
				{
					$paid_value=0;
					
					if($row[csf("payterm_id")]==3) 
					{
						$paid_value=$row[csf("edf_loan_value")];
					}
					else
					{
						if($row[csf("edf_paid_date")] != "" && $row[csf("edf_paid_date")] != "0000-00-00" && strtotime($row[csf("edf_paid_date")])<strtotime("25-10-2020"))
						{
							$paid_value=$row[csf("edf_loan_value")];
						}
						else
						{
							$paid_value=$invoice_payment_atsite[$row[csf("import_inv_id")]];
						}
					}
					
					if($maturity_date!="" && $maturity_date!="0000-00-00")
					{
						
						//$edf_count_data[$row[csf("import_inv_id")]]=($row[csf("edf_loan_value")]-$paid_value)."=".$row[csf("lc_number")]."=".$row[csf("lc_value")];
						$pending_value=$row[csf("edf_loan_value")]-$paid_value;
						if($pending_value>0)
						{
							$edf_count[$row[csf("issuing_bank_id")]][$row[csf("import_inv_id")]]=$pending_value;
							if($row[csf("issuing_bank_id")]==2) $edf_count_test.=$pending_value.",";
							$ifdbc_edf_data[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]["edf"]+=$row[csf("edf_loan_value")]-$paid_value;
							$edf_bank_total[$row[csf("issuing_bank_id")]]+=$row[csf("edf_loan_value")]-$paid_value;
						}
					}
					
				}
				else
				{
					if($row[csf("payterm_id")] != 3)
					{
						if($maturity_date!="" && $maturity_date!="0000-00-00")
						{
							if(($row[csf("edf_loan_value")]-$invoice_wise_payment[$row[csf("import_inv_id")]])>0)
							{
								$fdbp_count_data[$row[csf("import_inv_id")]]=($row[csf("edf_loan_value")]-$invoice_wise_payment[$row[csf("import_inv_id")]])."=".$row[csf("lc_number")]."=".$row[csf("lc_value")];
								$ifdbc_edf_data[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]["ifdbc"]+=$row[csf("edf_loan_value")]-$invoice_wise_payment[$row[csf("import_inv_id")]];
								$ifdbc_bank_total[$row[csf("issuing_bank_id")]]+=$row[csf("edf_loan_value")]-$invoice_wise_payment[$row[csf("import_inv_id")]];
							}
						}
					}
				}
			}
			
		}	
		unset($ifdbc_edf_sql_result);
		//echo $edf_count_test."<pre>";print_r($edf_count);die;
		//echo $test_datasss;die;
		if($db_type==0)
		{
			$btb_sql="select a.id, a.importer_id, a.issuing_bank_id, a.lc_type_id, a.lc_value, a.lc_type_id from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 and a.ref_closing_status<>1 and CONVERT(a.lc_category,SIGNED)>0 $sql_cond order by a.importer_id, a.issuing_bank_id";
		}
		else
		{
			$btb_sql="select a.id, a.importer_id, a.issuing_bank_id, a.lc_type_id, a.lc_value, a.lc_type_id from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 and a.ref_closing_status<>1 and to_number(a.lc_category)>0 $sql_cond order by a.importer_id, a.issuing_bank_id";
		}
		
		//echo $btb_sql;die;
		$btb_sql_result=sql_select($btb_sql);
		$btb_data=array();$all_btb_company=array();$all_btb_bank=array();
		foreach($btb_sql_result as $row)
		{
			$pending_value=$row[csf("lc_value")]-$lc_wise_payment[$row[csf("id")]];
			$all_btb_company[$row[csf("importer_id")]]=$row[csf("importer_id")];
			$all_btb_bank[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]=$row[csf("issuing_bank_id")];
			if($pending_value>0 && $row[csf("lc_type_id")]!=2)
			{
				$btb_data[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]+=$row[csf("lc_value")]-$lc_wise_payment[$row[csf("id")]];
				$btb_bank_total[$row[csf("issuing_bank_id")]]+=$row[csf("lc_value")]-$lc_wise_payment[$row[csf("id")]];
				/*if($row[csf("lc_type_id")]==2)
				{
					$margin_btb_data[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]+=$row[csf("lc_value")]-$lc_wise_payment[$row[csf("id")]];
					$margin_btb_bank_total[$row[csf("issuing_bank_id")]]+=$row[csf("lc_value")]-$lc_wise_payment[$row[csf("id")]];
				}
				else
				{
					$btb_data[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]+=$row[csf("lc_value")]-$lc_wise_payment[$row[csf("id")]];
					$btb_bank_total[$row[csf("issuing_bank_id")]]+=$row[csf("lc_value")]-$lc_wise_payment[$row[csf("id")]];
				}*/
			}
		}
		//echo "<pre>";print_r($all_btb_bank);die;
		/*$ma_sql="select a.id as btb_lc_id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.lc_value, b.invoice_no, b.maturity_date, sum(c.current_acceptance_value) as accep_value
		from com_btb_lc_master_details a, com_import_invoice_dtls c, com_import_invoice_mst b 
		where a.id=c.btb_lc_id and c.import_invoice_id=b.id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.is_deleted=0 and a.status_active=1  and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id=2
		group by a.id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.lc_value, b.invoice_no, b.maturity_date
		order by a.lc_category, a.id";*/
		unset($btb_sql_result);
		
		$exfact_com_cond="";
		if($cbo_company_name>0) $exfact_com_cond=" and a.company_id=$cbo_company_name";
		$ex_fact_data=return_library_array("select b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
		//echo "<pre>";print_r($ex_fact_data[33372]);echo "<br>";//die;
		$com_cond="";
		if($cbo_company_name>0) $com_ord_cond=" and b.company_name=$cbo_company_name";
		if($cbo_lein_bank>0) $bank_ord_cond=" and a.bank_id=$cbo_lein_bank";
		$order_sql="select c.id as po_id, a.bank_id, b.company_name, (c.po_quantity*b.total_set_qnty) as order_quantity, c.po_total_price as order_total 
		from lib_buyer a, wo_po_details_master b, wo_po_break_down c 
		where a.id=b.buyer_name and b.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and a.bank_id>0 $com_ord_cond $bank_ord_cond order by po_id";
		//echo $order_sql;die;
		$order_sql_result=sql_select($order_sql);
		$pending_ord_qnty=0;$tot_pending_ord_qnty=0;$tot_pending_ord_value=0;
		
		foreach($order_sql_result as $row)
		{
			$ord_rate=0;
			if($row[csf("order_total")]>0 && $row[csf("order_quantity")] >0)
			{
				$ord_rate=($row[csf("order_total")]/$row[csf("order_quantity")])*1;
			}
			$pendin_qnty=($row[csf("order_quantity")]-$ex_fact_data[$row[csf("po_id")]])*1;
			if($pendin_qnty>0)
			{
				$pending_ord_value=($pendin_qnty*$ord_rate);
				$pending_ord_data_arr[$row[csf("company_name")]][$row[csf("bank_id")]]["pendin_qnty"]+=$pendin_qnty;
				$pending_ord_data_arr[$row[csf("company_name")]][$row[csf("bank_id")]]["pending_ord_value"]+=$pending_ord_value;
				
				$pending_ord_data_bank_arr[$row[csf("bank_id")]]["pendin_qnty"]+=$pendin_qnty;
				$pending_ord_data_bank_arr[$row[csf("bank_id")]]["pending_ord_value"]+=$pending_ord_value;
				$order_wise_pending_value[$row[csf("po_id")]]=$pending_ord_value;
				$tot_pending_ord_qnty+=$pendin_qnty;
				$tot_pending_ord_value+=$pending_ord_value;
			}
			//$tot_pending_ord_qnty+=$pendin_qnty;
		}
		//echo "<pre>";print_r($order_wise_pending_value);die;
		
		//echo $pendin_qnty."ord_rate".$ord_rate."ord_rates".$pending_ord_value;
		unset($order_sql_result);
		
		/*if($cbo_company_name>0) $job_btb_cond=" and d.importer_id=$cbo_company_name";
		$job_btb_sql="select a.job_no, b.id as pi_dtls_id, b.net_pi_amount, 1 as type  
		from wo_non_order_info_dtls a, com_pi_item_details  b, com_btb_lc_pi c, com_btb_lc_master_details d
		 where a.id=b.work_order_dtls_id and b.pi_id=c.pi_id and c.com_btb_lc_master_details_id=d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.item_category_id=1 and b.item_category_id=1 and a.job_no is not null $job_btb_cond
		union all 
		select a.job_no, b.id as pi_dtls_id, b.net_pi_amount, 2 as type  
		from wo_booking_dtls a, com_pi_item_details  b, com_btb_lc_pi c, com_btb_lc_master_details d
		 where a.id=b.work_order_dtls_id and b.pi_id=c.pi_id and c.com_btb_lc_master_details_id=d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.booking_type=2 and b.item_category_id=4 and a.job_no is not null $job_btb_cond";
		//echo $job_btb_sql;die;
		$job_btb_result=sql_select($job_btb_sql);
		$job_wise_btb_value=array();
		foreach($job_btb_result as $row)
		{
			if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
			{
				$pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				$job_wise_btb_value[$row[csf("job_no")]][$row[csf("type")]]+=$row[csf("net_pi_amount")];
			}
		}
		
		if($cbo_company_name>0) $budge_com_cond=" and d.company_name=$cbo_company_name";
		$budge_btb_open_sql="select  a.job_no, (d.job_quantity*d.total_set_qnty) as job_quantity, a.costing_per, b.amount, b.id as dtls_id, 1 as type
		from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_fab_yarn_cost_dtls b 
		where c.job_no_mst=d.job_no and d.job_no=a.job_no and a.job_no=b.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.amount > 0 and c.shiping_status<>3 $budge_com_cond 	
		union all 
		select  a.job_no, (d.job_quantity*d.total_set_qnty) as job_quantity, a.costing_per, b.amount, b.id as dtls_id, 2 as type
		from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b 
		where c.job_no_mst=d.job_no and d.job_no=a.job_no and a.job_no=b.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.amount > 0 and c.shiping_status<>3 $budge_com_cond
		union all 
		select  a.job_no, (d.job_quantity*d.total_set_qnty) as job_quantity, a.costing_per, b.amount, b.id as dtls_id, 3 as type
		from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_embe_cost_dtls b 
		where c.job_no_mst=d.job_no and d.job_no=a.job_no and a.job_no=b.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.amount > 0 and c.shiping_status<>3 and b.emb_name <>3 $budge_com_cond";
		//echo $budge_btb_open_sql;die;
		$budge_btb_open_result=sql_select($budge_btb_open_sql);
		$job_wise_budge_amt=array();
		foreach($budge_btb_open_result as $row)
		{
			$dzn_qnty=0;
			$costing_per_id=$row[csf('costing_per')];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
			if($costing_per_id==1) $dzn_qnty=12;
			else if($costing_per_id==3) $dzn_qnty=12*2;
			else if($costing_per_id==4) $dzn_qnty=12*3;
			else if($costing_per_id==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			$amount=0;
			$amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("job_quantity")];
			if($job_check[$row[csf("dtls_id")]]=="")
			{
				$job_check[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
				$all_job_no[$row[csf('job_no')]]=$row[csf('job_no')];
				$job_wise_budge_amt[$row[csf('job_no')]][$row[csf('type')]]+=$amount;
			}
		}
		
		$btb_open=0;
		foreach($all_job_no as $job=>$job_num)
		{
			$btb_open +=($job_wise_budge_amt[$job_num][1]-$job_wise_btb_value[$job_num][1])+($job_wise_budge_amt[$job_num][2]-$job_wise_btb_value[$job_num][2])+($job_wise_budge_amt[$job_num][3]-$job_wise_btb_value[$job_num][3]);
		}*/
		
		//+($row[csf("trims_cost")]-$job_wise_btb_value[$row[csf("job_no")]][2])+$row[csf("embel_cost")]
		//echo $btb_open; die;
		
		$beneficiary_cond="";
		if($cbo_company_name>0) $beneficiary_cond=" and b.benificiary_id=$cbo_company_name";
		$proceed_rlz_sql="select a.is_lc, a.lc_sc_id, b.invoice_bill_id, c.id as dtls_id, c.account_head, c.document_currency as document_currency 
		from com_export_doc_submission_invo a, com_export_proceed_realization b, com_export_proceed_rlzn_dtls c 
		where a.doc_submission_mst_id=b.invoice_bill_id and b.id=c.mst_id and b.is_invoice_bill=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $beneficiary_cond";
		//echo $proceed_rlz_sql;die;
		$realize_data_arr=array();$lc_wise_rlz=array();
		$proceed_rlz_sql_result=sql_select($proceed_rlz_sql);
		foreach($proceed_rlz_sql_result as $row)
		{
			if($proceed_dtls_check[$row[csf("dtls_id")]]=="")
			{
				$proceed_dtls_check[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
				$realize_data_arr[$row[csf("invoice_bill_id")]]+=$row[csf("document_currency")];
				if($row[csf("account_head")]==20 || $row[csf("account_head")]==22 || $row[csf("account_head")]==16)
				{
					$lc_sc_id=$row[csf("lc_sc_id")]."__".$row[csf("is_lc")];
					$lc_wise_rlz[$lc_sc_id]+=$row[csf("document_currency")];
				}
			}
		}
		unset($proceed_rlz_sql_result);
		
		$pak_beneficiary_cond="";
		if($cbo_company_name>0) $pak_beneficiary_cond=" and c.beneficiary_name=$cbo_company_name";
		$packing_sql=sql_select("select c.beneficiary_name, c.lien_bank, a.loan_amount, b.export_type, b.lc_sc_id  
		from com_pre_export_finance_dtls a, com_pre_export_lc_wise_dtls b, com_export_lc c 
		where a.id=b.pre_export_dtls_id and b.lc_sc_id=c.id and b.export_type=1 --and a.loan_type=20 
		and a.status_active=1 and a.is_deleted=0 $pak_beneficiary_cond
		union all
		select c.beneficiary_name, c.lien_bank, a.loan_amount, b.export_type, b.lc_sc_id  
		from com_pre_export_finance_dtls a, com_pre_export_lc_wise_dtls b, com_sales_contract c 
		where a.id=b.pre_export_dtls_id and b.lc_sc_id=c.id and b.export_type=2 --and a.loan_type=20 
		and a.status_active=1 and a.is_deleted=0 $pak_beneficiary_cond");
		$odg_amt_arr=array();
		foreach($packing_sql as $row)
		{
			if($row[csf("lien_bank")])
			{
				$lc_sc_id=$row[csf("lc_sc_id")]."__".$row[csf("export_type")];
				$odg_amt+=$row[csf("loan_amount")];
				$odg_amt_arr[$row[csf("beneficiary_name")]][$row[csf("lien_bank")]]+=$row[csf("loan_amount")]-$lc_wise_rlz[$lc_sc_id];
				$odg_amt_bankWise_arr[$row[csf("lien_bank")]]+=$row[csf("loan_amount")]-$lc_wise_rlz[$lc_sc_id];
			}
		}
		unset($packing_sql);
		
		$bank_id_cond=$beneficiary_cond="";
		if($cbo_company_name>0) $beneficiary_cond=" and b.company_id=$cbo_company_name";
		if($cbo_lein_bank>0) $bank_id_cond=" and a.id=$cbo_lein_bank";
		$bank_cd_sql=sql_select("select b.company_id, a.id, b.loan_limit as loan_amount 
		from lib_bank a, lib_bank_account b 
		where a.id=b.account_id and b.account_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $beneficiary_cond $bank_id_cond");
		$sod_cd_amt=0;
		foreach($bank_cd_sql as $row)
		{
			$sod_cd_amt+=$row[csf("loan_amount")];
			$sod_cd_amt_arr[$row[csf("company_id")]][$row[csf("id")]]+=$row[csf("loan_amount")];
			$sod_cd_amt_bank_arr[$row[csf("id")]]+=$row[csf("loan_amount")];
		}
		unset($bank_cd_sql);
		
		$bill_com_cond="";
		if($cbo_company_name>0) $bill_com_cond=" and b.company_id=$cbo_company_name";
		if($cbo_lein_bank>0) $bill_bank_cond=" and b.lien_bank=$cbo_lein_bank";
		$bill_trans="select b.id as bill_id, sum(a.lc_sc_curr) as bill_value 
		from com_export_doc_sub_trans a, com_export_doc_submission_mst b 
		where a.doc_submission_mst_id=b.id and b.entry_form = 40 and b.lien_bank > 0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond 
		group by b.id";
		$bill_trans_result=sql_select($bill_trans);
		$bill_trans_data=array();
		foreach($bill_trans_result as $row)
		{
			$bill_trans_data[$row[csf("bill_id")]]+=$row[csf("bill_value")];
		}
		unset($bill_trans_result);
		
		$bill_sql="select b.id as bill_id, b.company_id, b.lien_bank, b.submit_type, a.is_lc, sum(a.net_invo_value) as bill_value 
		from com_export_doc_submission_invo a, com_export_doc_submission_mst b 
		where a.doc_submission_mst_id=b.id and b.entry_form = 40 and b.lien_bank > 0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond 
		group by b.id, b.company_id, b.lien_bank, b.submit_type, a.is_lc";
		//echo $bill_sql;
		$bill_sql_result=sql_select($bill_sql);
		$fdbp_data=array();
		foreach($bill_sql_result as $row)
		{
			if($realize_data_arr[$row[csf("bill_id")]]=="" && $row[csf("is_lc")]==1)
			{
				if($row[csf("submit_type")]==2)
				{
					$rcvable_value=$row[csf("bill_value")]-$bill_trans_data[$row[csf("bill_id")]];
					$fdbp_data[$row[csf("company_id")]][$row[csf("lien_bank")]]+=$bill_trans_data[$row[csf("bill_id")]];
					$fdbp_bank_data[$row[csf("lien_bank")]]+=$bill_trans_data[$row[csf("bill_id")]];
					
					$bill_receiveable_arr[$row[csf("company_id")]][$row[csf("lien_bank")]]+=$rcvable_value;
					$bill_receiveable_bank_arr[$row[csf("lien_bank")]]+=$rcvable_value;
				}
				else
				{
					$bill_receiveable_arr[$row[csf("company_id")]][$row[csf("lien_bank")]]+=$row[csf("bill_value")];
					$bill_receiveable_bank_arr[$row[csf("lien_bank")]]+=$row[csf("bill_value")];
				}
			}
		}
		unset($bill_sql_result);
		
		$bill_coll_sql="select a.invoice_id, sum(a.net_invo_value) as bill_value 
		from com_export_doc_submission_invo a, com_export_doc_submission_mst b 
		where a.doc_submission_mst_id=b.id and b.entry_form = 40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond 
		group by a.invoice_id";
		$bill_coll_sql_result=sql_select($bill_coll_sql);
		$bill_receive=0;
		foreach($bill_coll_sql_result as $row)
		{
			$inv_sub_arr[$row[csf("invoice_id")]]=$row[csf("bill_value")];
		}
		unset($bill_coll_sql_result);
		if($cbo_company_name>0) $com_ship_cond=" and b.benificiary_id=$cbo_company_name";
		if($cbo_lein_bank>0) $bank_ship_cond=" and a.lien_bank=$cbo_lein_bank";
		/*$inv_sql=" select b.benificiary_id, a.lien_bank, b.id as inv_id, b.net_invo_value from com_export_lc a, com_export_invoice_ship_mst b where a.id=b.lc_sc_id and b.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $com_ship_cond $bank_ship_cond
		union all
		select b.benificiary_id, a.lien_bank, b.id as inv_id, b.net_invo_value from com_sales_contract a, com_export_invoice_ship_mst b where a.id=b.lc_sc_id and b.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $com_ship_cond $bank_ship_cond";*/
		if($db_type==0)
		{
			$ex_data_cond=" and b.ex_factory_date!='0000-00-00'";
		}
		else
		{
			$ex_data_cond=" and b.ex_factory_date is not null";
		}
		
		$inv_sql=" select b.benificiary_id, a.lien_bank, b.id as inv_id, b.net_invo_value 
		from com_export_lc a, com_export_invoice_ship_mst b 
		where a.id=b.lc_sc_id and b.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.lien_bank>0 and b.location_id>0 $com_ship_cond $bank_ship_cond $ex_data_cond
		union all
		select b.benificiary_id, a.lien_bank, b.id as inv_id, b.net_invo_value 
		from com_sales_contract a, com_export_invoice_ship_mst b 
		where a.id=b.lc_sc_id and b.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.lien_bank>0 and b.location_id>0 $com_ship_cond $bank_ship_cond $ex_data_cond";
		//echo $inv_sql;die;
		$inv_sql_result=sql_select($inv_sql);
		foreach($inv_sql_result as $row)
		{
			$pending_value=$row[csf("net_invo_value")]-$inv_sub_arr[$row[csf("inv_id")]];
			if($pending_value>0)
			{
				$doc_ind_hand_arr[$row[csf("benificiary_id")]][$row[csf("lien_bank")]]+=$row[csf("net_invo_value")]-$inv_sub_arr[$row[csf("inv_id")]];
				$doc_ind_hand_bank_arr[$row[csf("lien_bank")]]+=$row[csf("net_invo_value")]-$inv_sub_arr[$row[csf("inv_id")]];
			}
		}
		
		
		//echo $pending_ord_qnty."==".$bill_receive."==". $odg_amt."==".$sod_cd_amt;die;
		//echo "<pre>";print_r($all_btb_bank);die;
		$count_col=0;$bank_col=0;
		foreach($all_btb_bank as $com_id=>$com_data)
		{
			foreach($com_data as $bank_val)
			{
				$count_col++;
				if($bank_check[$bank_val]=="")
				{
					$bank_check[$bank_val]=$bank_val;
					$bank_col++;
				}
			}
			$count_col++;
		}
		//echo "<pre>";print_r($bank_check);die;
		//echo $count_col."=".$com_col."=".$bank_col;die;
		$tot_col=$count_col+$bank_col;
		$table_width=(300+(100*$tot_col));
		$div_width=20+$table_width;
		ob_start();
		?>
		<div style="width:<? echo $div_width; ?>px;" id="scroll_body">
		<fieldset style="width:100%">
			<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
				<thead>
					<tr>
						<th width="50" rowspan="3">SL</th>
						<th width="150" rowspan="3">Facility</th>
						<th colspan="<? echo $count_col; ?>">Amount</th>
						<th colspan="<? echo $bank_col; ?>">Bank Total</th>
						<th rowspan="3" width="100">Group Total</th>
					</tr>
					<tr>
						<?
						$count_col=0;
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							?>
							<th colspan="<? echo count($com_data)+1; ?>" title="<? echo $com_id; ?>"><? echo $company_arr[$com_id];?></th>
							<?
							
						}
						foreach($bank_check as $bank_id)
						{
							$count_col++;
							?>
							<th rowspan="2" width="100"><? echo $bank_arr[$bank_id];?></th>
							<?
						}
						?>
					</tr>
					<tr>
						<?
						
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							foreach($com_data as $bank_id)
							{
								$count_col++;
								?>
								<th width="100" title="<? echo $bank_id; ?>"><? echo $bank_arr[$bank_id];?></th>
								<?	
							}
							?>
							<th width="100">Total</th>
							<?
						}
						?>
					</tr>
				</thead>
				<tbody>
					<tr bgcolor="#FFFFFF">
						<td align="center">1</td>
						<td>BTB LC</td>
						<?
						$total_libiality=array();$total_bank_libiality=array();
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							foreach($com_data as $bank_id)
							{
								?>
								<td align="right"><a href='#report_detals'  onclick= "openmypage('<? echo $com_id; ?>','<? echo $bank_id; ?>','btb_popup','BTB Info','1')"><? echo number_format($btb_data[$com_id][$bank_id],2);?></a></td>
								<?
								$total_libiality[$com_id][$bank_id]+=$btb_data[$com_id][$bank_id];
								$com_btb_total+=$btb_data[$com_id][$bank_id];
							}
							?>
							<td align="right"><? echo number_format($com_btb_total,2); $grp_com_btb_total+=$com_btb_total; $com_btb_total=0;?></td>
							<?
						}
						foreach($bank_check as $bank_id)
						{
							?>
							<td align="right"><? echo number_format($btb_bank_total[$bank_id],2);?></td>
							<?
							$total_bank_libiality[$bank_id]+=$btb_bank_total[$bank_id];
						}
						?>
						<td align="right"><? echo number_format($grp_com_btb_total,2);?></td>
					</tr>
					<tr bgcolor="#E9F3FF">
						<td align="center">2</td>
						<td title="All Retire Source With out EDF And PAD">IFDBC</td>
						<?
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							foreach($com_data as $bank_id)
							{
								?>
								<td align="right" title="<? print_r($fdbp_count_data);?>"><a href='#report_detals'  onclick= "openmypage('<? echo $com_id; ?>','<? echo $bank_id; ?>','ifdbc_popup','IFDBC Info','2')"><? echo number_format($ifdbc_edf_data[$com_id][$bank_id]["ifdbc"],2);?></a></td>
								<?	
								$total_libiality[$com_id][$bank_id]+=$ifdbc_edf_data[$com_id][$bank_id]["ifdbc"];
								$com_ifdbc_total+=$ifdbc_edf_data[$com_id][$bank_id]["ifdbc"];
							}
							?>
							<td align="right"><? echo number_format($com_ifdbc_total,2); $grp_com_ifdbc_total+=$com_ifdbc_total; $com_ifdbc_total=0;?></td>
							<?
						}
						foreach($bank_check as $bank_id)
						{
							?>
							<td align="right"><? echo number_format($ifdbc_bank_total[$bank_id],2);?></td>
							<?
							$total_bank_libiality[$bank_id]+=$ifdbc_bank_total[$bank_id];
						}
						?>
						<td align="right"><? echo number_format($grp_com_ifdbc_total,2);?></td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td align="center">3</td>
						<td title="Only Retire Source EDF And PAD">PAD (EDF)</td>
						<?
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							foreach($com_data as $bank_id)
							{
								?>
								<td align="right" title="<? print_r($edf_count_data);?>"><a href='#report_detals'  onclick= "openmypage('<? echo $com_id; ?>','<? echo $bank_id; ?>','ifdbc_popup','IFDBC Info','3')"><? echo number_format($ifdbc_edf_data[$com_id][$bank_id]["edf"],2);?></a></td>
								<?
								$total_libiality[$com_id][$bank_id]+=$ifdbc_edf_data[$com_id][$bank_id]["edf"];	
								$com_edf_total+=$ifdbc_edf_data[$com_id][$bank_id]["edf"];
							}
							?>
							<td align="right"><? echo number_format($com_edf_total,2); $grp_com_edf_total+=$com_edf_total; $com_edf_total=0;?></td>
							<?
						}
						foreach($bank_check as $bank_id)
						{
							?>
							<td align="right"><? echo number_format($edf_bank_total[$bank_id],2);?></td>
							<?
							$total_bank_libiality[$bank_id]+=$edf_bank_total[$bank_id];
						}
						?>
						<td align="right"><? echo number_format($grp_com_edf_total,2);?></td>
					</tr>
					<tr bgcolor="#E9F3FF">
						<td align="center">4</td>
						<td>FDBP</td>
						<?
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							foreach($com_data as $bank_id)
							{
								?>
								<td align="right"><a href='#report_detals'  onclick= "openmypage('<? echo $com_id; ?>','<? echo $bank_id; ?>','fdbp_popup','FDBP Info','4')"><? echo number_format($fdbp_data[$com_id][$bank_id],2);?></a></td>
								<?	
								$total_libiality[$com_id][$bank_id]+=$fdbp_data[$com_id][$bank_id];	
								$com_fdbp_total+=$fdbp_data[$com_id][$bank_id];	
							}
							?>
							<td align="right"><? echo number_format($com_fdbp_total,2); $grp_com_fdbp_total+=$com_fdbp_total; $com_fdbp_total=0;?></td>
							<?
						}
						foreach($bank_check as $bank_id)
						{
							?>
							<td align="right"><? echo number_format($fdbp_bank_data[$bank_id],2);?></td>
							<?
							$total_bank_libiality[$bank_id]+=$fdbp_bank_data[$bank_id];
						}
						?>
						<td align="right"><? echo number_format($grp_com_fdbp_total,2);?></td>
	
					</tr>
					<tr bgcolor="#FFFFCC">
						<td align="center">5</td>
						<td>BTB Liability (USD)</td>
						<?
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							foreach($com_data as $bank_id)
							{
								?>
								<td align="right"><? echo number_format($total_libiality[$com_id][$bank_id],2);?></td>
								<?
								$com_libiality_usd+=$total_libiality[$com_id][$bank_id];
							}
							?>
							<td align="right"><? echo number_format($com_libiality_usd,2); $grp_com_libiality_usd+=$com_libiality_usd; $com_libiality_usd=0;?></td>
							<?
						}
						foreach($bank_check as $bank_id)
						{
							?>
							<td align="right"><? echo number_format($total_bank_libiality[$bank_id],2);?></td>
							<?
						}
						?>
						<td align="right"><? echo number_format($grp_com_libiality_usd,2);?></td>
					</tr>
					<tr bgcolor="#FFFFCC">
						<td align="center">6</td>
						<td>BTB Liability (BDT)</td>
						<?
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							foreach($com_data as $bank_id)
							{
								?>
								<td align="right"><? echo number_format(($total_libiality[$com_id][$bank_id]*$txt_exchange_rate),2);?></td>
							   <?
								$com_tot_libiality_usd+=($total_libiality[$com_id][$bank_id]*$txt_exchange_rate);
								$tot_liability_arr[$com_id][$bank_id]+=($total_libiality[$com_id][$bank_id]*$txt_exchange_rate);
							}
							?>
							<td align="right"><? echo number_format($com_tot_libiality_usd,2); $grp_com_tot_libiality_usd+=$com_tot_libiality_usd; $com_tot_libiality_usd=0;?></td>
							<?
						}
						foreach($bank_check as $bank_id)
						{
							?>
							<td align="right"><? echo number_format(($total_bank_libiality[$bank_id]*$txt_exchange_rate),2);?></td>
							<?
							$tot_liability_bank_arr[$bank_id]+=($total_bank_libiality[$bank_id]*$txt_exchange_rate);
						}
						?>
						<td align="right"><? echo number_format($grp_com_tot_libiality_usd,2);?></td>
					</tr>
					<tr bgcolor="#CCCCCC">
						<td align="center" colspan="<? echo $count_col+5;?>">&nbsp;</td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td align="center">7</td>
						<td>IFDBC - Machinery/Cash Lc</td>
						<?
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							foreach($com_data as $bank_id)
							{
								?>
								<td align="right"><a href='#report_detals'  onclick= "openmypage('<? echo $com_id; ?>','<? echo $bank_id; ?>','margine_popup','IFDBC - Machinery','7')"><? echo number_format(($margin_btb_data[$com_id][$bank_id]),2);?></a></td>
							   <?
								$com_tot_margin_btb+=$margin_btb_data[$com_id][$bank_id];
							}
							?>
							<td align="right"><? echo number_format($com_tot_margin_btb,2); $grp_com_tot_margin_btb+=$com_tot_margin_btb; $com_tot_margin_btb=0;?></td>
							<?
						}
						foreach($bank_check as $bank_id)
						{
							?>
							<td align="right"><? echo number_format(($margin_btb_bank_total[$bank_id]),2);?></td>
							<?
						}
						?>
						<td align="right"><? echo number_format($grp_com_tot_margin_btb,2);?></td>
					</tr>
					<tr bgcolor="#CCCCCC">
						<td align="center" colspan="<? echo $count_col+5;?>">&nbsp;</td>
					</tr>
					<tr bgcolor="#E9F3FF">
						<td align="center">8</td>
						<td>ODG/RL (BDT)</td>
						<?
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							foreach($com_data as $bank_id)
							{
								?>
								<td align="right"><? echo number_format(($odg_amt_arr[$com_id][$bank_id]),2);?></td>
							   <?
								$com_tot_margin_odg+=$odg_amt_arr[$com_id][$bank_id];
								$total_od_liability[$com_id][$bank_id]+=$odg_amt_arr[$com_id][$bank_id];
							}
							?>
							<td align="right"><? echo number_format($com_tot_margin_odg,2); $grp_com_tot_margin_odg+=$com_tot_margin_odg; $com_tot_margin_odg=0;?></td>
							<?
						}
						foreach($bank_check as $bank_id)
						{
							?>
							<td align="right"><? echo number_format(($odg_amt_bankWise_arr[$bank_id]),2);?></td>
							<?
							$total_od_liability_bank[$bank_id]+=$odg_amt_bankWise_arr[$bank_id];
						}
						?>
						<td align="right"><? echo number_format($grp_com_tot_margin_odg,2);?></td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td align="center">9</td>
						<td>SOD/CD (BDT)</td>
						<?
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							foreach($com_data as $bank_id)
							{
								?>
								<td align="right"><? echo number_format(($sod_cd_amt_arr[$com_id][$bank_id]),2);?></td>
							   <?
								$com_tot_margin_cd+=$sod_cd_amt_arr[$com_id][$bank_id];
								$total_od_liability[$com_id][$bank_id]+=$sod_cd_amt_arr[$com_id][$bank_id];
							}
							?>
							<td align="right"><? echo number_format($com_tot_margin_cd,2); $grp_com_tot_margin_cd+=$com_tot_margin_cd; $com_tot_margin_cd=0;?></td>
							<?
						}
						foreach($bank_check as $bank_id)
						{
							?>
							<td align="right"><? echo number_format(($sod_cd_amt_bank_arr[$bank_id]),2);?></td>
							<?
							$total_od_liability_bank[$bank_id]+=$sod_cd_amt_bank_arr[$bank_id];
						}
						?>
						<td align="right"><? echo number_format($grp_com_tot_margin_cd,2);?></td>
					</tr>
					<tr bgcolor="#FFFFCC">
						<td align="center">10</td>
						<td>Total OD Liability (BDT)</td>
						<?
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							foreach($com_data as $bank_id)
							{
								?>
								<td align="right"><? echo number_format(($total_od_liability[$com_id][$bank_id]),2);?></td>
							   <?
								$com_total_od_liability+=$total_od_liability[$com_id][$bank_id];
								$tot_liability_arr[$com_id][$bank_id]+=$total_od_liability[$com_id][$bank_id];
							}
							?>
							<td align="right"><? echo number_format($com_total_od_liability,2); $grp_com_total_od_liability+=$com_total_od_liability; $com_total_od_liability=0;?></td>
							<?
						}
						foreach($bank_check as $bank_id)
						{
							?>
							<td align="right"><? echo number_format(($total_od_liability_bank[$bank_id]),2);?></td>
							<?
							$tot_liability_bank_arr[$bank_id]+=$total_od_liability_bank[$bank_id];
						}
						?>
						<td align="right"><? echo number_format($grp_com_total_od_liability,2);?></td>
					</tr>
					<tr bgcolor="#FFFFCC">
						<td align="center">11</td>
						<td>Total Liability (BDT)</td>
						<?
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							foreach($com_data as $bank_id)
							{
								?>
								<td align="right"><? echo number_format(($tot_liability_arr[$com_id][$bank_id]),2);?></td>
							   <?
								$com_tot_liability+=$tot_liability_arr[$com_id][$bank_id];
							}
							?>
							<td align="right"><? echo number_format($com_tot_liability,2); $grp_com_tot_liability+=$com_tot_liability; $com_tot_liability=0;?></td>
							<?
						}
						foreach($bank_check as $bank_id)
						{
							?>
							<td align="right"><? echo number_format(($tot_liability_bank_arr[$bank_id]),2);?></td>
							<?
						}
						?>
						<td align="right"><? echo number_format($grp_com_tot_liability,2);?></td>
					</tr>
					<tr bgcolor="#FFFFCC">
						<td align="center">12</td>
						<td>Total Liability (USD)</td>
						<?
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							foreach($com_data as $bank_id)
							{
								?>
								<td align="right"><? echo number_format(($tot_liability_arr[$com_id][$bank_id]/$txt_exchange_rate),2);?></td>
							   <?
								$com_tot_liability_usd+=$tot_liability_arr[$com_id][$bank_id]/$txt_exchange_rate;
							}
							?>
							<td align="right"><? echo number_format($com_tot_liability_usd,2); $grp_com_tot_liability_usd+=$com_tot_liability_usd; $com_tot_liability_usd=0;?></td>
							<?
						}
						foreach($bank_check as $bank_id)
						{
							?>
							<td align="right"><? echo number_format(($tot_liability_bank_arr[$bank_id]/$txt_exchange_rate),2);?></td>
							<?
						}
						?>
						<td align="right"><? echo number_format($grp_com_tot_liability_usd,2);?></td>
					</tr>
					<tr bgcolor="#CCCCCC">
						<td align="center" colspan="<? echo $count_col+5;?>">&nbsp;</td>
					</tr> 
					<tr bgcolor="#CCCCCC">
						<td align="center" style="font-weight:bold; font-size:14px;" colspan="<? echo $count_col+5;?>">Export Under Execution (USD)</td>
					</tr>                
					<tr bgcolor="#E9F3FF">
						<td align="center">13</td>
						<td>Order In Hand</td>
						<?
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							foreach($com_data as $bank_id)
							{
								?>
								<td align="right" title="<? echo $pending_ord_data_arr[$com_id][$bank_id]["pendin_qnty"];?>"><a href="##" onClick="openmypage_popup('<? echo $com_id."__".$bank_id; ?>','Order In Hand Info','bank_order_in_hand_popup');" ><? echo number_format($pending_ord_data_arr[$com_id][$bank_id]["pending_ord_value"],2);?></a></td>
							   <?
								$com_pending_ord+=$pending_ord_data_arr[$com_id][$bank_id]["pending_ord_value"];
								$tot_in_hand[$com_id][$bank_id]+=$pending_ord_data_arr[$com_id][$bank_id]["pending_ord_value"];
							}
							?>
							<td align="right"><a href="##" onClick="openmypage_popup('<? echo $com_id."__".$cbo_lein_bank; ?>','Order In Hand Info','bank_order_in_hand_popup');" ><? echo number_format($com_pending_ord,2); $grp_com_pending_ord+=$com_pending_ord; $com_pending_ord=0;?></a></td>
							<?
						}
						foreach($bank_check as $bank_id)
						{
							?>
							<td align="right"><? echo number_format($pending_ord_data_bank_arr[$bank_id]["pending_ord_value"],2);?></td>
							<?
							$tot_in_hand_bank[$bank_id]+=$pending_ord_data_bank_arr[$bank_id]["pending_ord_value"];
						}
						?>
						<td align="right"><a href="##" onClick="openmypage_popup('<? echo $cbo_company_name."__".$cbo_lein_bank; ?>','Order In Hand Info','bank_order_in_hand_popup');" ><? echo number_format($grp_com_pending_ord,2);?></a></td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td align="center">14</td>
						<td>Docs In Hand</td>
						<?
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							foreach($com_data as $bank_id)
							{
								?>
	
								<td align="right"><a href="##" onClick="openmypage_popup('<? echo $com_id."__".$bank_id; ?>','Order In Hand Info','docs_in_hand_popup');" ><? echo number_format($doc_ind_hand_arr[$com_id][$bank_id],2);?></a></td>
							   <?
								$com_pending_doc+=$doc_ind_hand_arr[$com_id][$bank_id];
								$tot_in_hand[$com_id][$bank_id]+=$doc_ind_hand_arr[$com_id][$bank_id];
							}
							?>
							<td align="right"><? echo number_format($com_pending_doc,2); $grp_com_pending_doc+=$com_pending_doc; $com_pending_doc=0;?></td>
							<?
						}
						foreach($bank_check as $bank_id)
						{
							?>
							<td align="right"><? echo number_format($doc_ind_hand_bank_arr[$bank_id],2);?></td>
							<?
							$tot_in_hand_bank[$bank_id]+=$doc_ind_hand_bank_arr[$bank_id];
						}
						?>
						<td align="right"><a href="##" onClick="openmypage_popup('<? echo $cbo_company_name."__".$cbo_lein_bank; ?>','Order In Hand Info','docs_forcast_popup');" ><? echo number_format($grp_com_pending_doc,2);?></a></td>
					</tr>
					<tr bgcolor="#FFFFCC">
						<td align="center">15</td>
						<td>Total In Hand</td>
						<?
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							foreach($com_data as $bank_id)
							{
								?>
								<td align="right"><? echo number_format($tot_in_hand[$com_id][$bank_id],2);?></td>
							   <?
								$com_tot_in_hand+=$tot_in_hand[$com_id][$bank_id];
							}
							?>
							<td align="right"><? echo number_format($com_tot_in_hand,2); $grp_com_tot_in_hand+=$com_tot_in_hand; $com_tot_in_hand=0;?></td>
							<?
						}
						foreach($bank_check as $bank_id)
						{
							?>
							<td align="right"><? echo number_format($tot_in_hand_bank[$bank_id],2);?></td>
							<?
						}
						?>
						<td align="right"><? echo number_format($grp_com_tot_in_hand,2);?></td>
					</tr>
					<tr bgcolor="#CCCCCC">
						<td align="center" colspan="<? echo $count_col+5;?>">&nbsp;</td>
					</tr>
					<tr bgcolor="#E9F3FF">
						<td align="center">16</td>
						<td>Bills Receiveable (USD)</td>
						<?
						foreach($all_btb_bank as $com_id=>$com_data)
						{
							foreach($com_data as $bank_id)
							{
								//$bill_receiveable_arr[$row[csf("company_id")]][$row[csf("lien_bank")]]+=$row[csf("bill_value")]-$realize_data_arr[$row[csf("bill_id")]];
								//$bill_receiveable_bank_arr[$row[csf("lien_bank")]]+=$row[csf("bill_value")]-$realize_data_arr[$row[csf("bill_id")]];
								?>
								<td align="right"><a href="##" onClick="openmypage_popup('<? echo $com_id."__".$bank_id; ?>','Order In Hand Info','bill_receiveable_popup');" ><? echo number_format($bill_receiveable_arr[$com_id][$bank_id],2);?></a></td>
							   <?
								$com_bill_receiveable+=$bill_receiveable_arr[$com_id][$bank_id];
							}
							?>
							<td align="right"><? echo number_format($com_bill_receiveable,2); $grp_com_bill_receiveable+=$com_bill_receiveable; $com_bill_receiveable=0;?></td>
							<?
						}
						foreach($bank_check as $bank_id)
						{
							?>
							<td align="right"><? echo number_format($bill_receiveable_bank_arr[$bank_id],2);?></td>
							<?
						}
						?>
						<td align="right"><? echo number_format($grp_com_bill_receiveable,2);?></td>
					</tr>
					<tr bgcolor="#CCCCCC">
						<td align="center" colspan="<? echo $count_col+5;?>">&nbsp;</td>
					</tr>
					<tr bgcolor="#CCCCCC">
						<td align="center">17</td>
						<td>Order In Hand Total</td>
						<td colspan="<? echo $count_col+3;?>" title="<? echo $tot_pending_ord_qnty; ?>"><a href="##" onClick="openmypage_popup('<?  echo $cbo_company_name."__".$cbo_lein_bank; ?>','Order In Hand Info','order_in_hand_popup');" > <? echo number_format($tot_pending_ord_value,2) ?></a></td>
					</tr>
				</tbody>
			</table>
			<!--<table width="400" cellpadding="0" cellspacing="0" align="left" style="margin-top:30px;">
				<tr><td colspan="2" style="font-size:16px; font-weight:bold;">Others :</td></tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr><td width="250">Export Under Execution (USD)</td><td><?// echo number_format($pending_ord_qnty,2) ?></td></tr>
				<tr><td title="Bill Value - Realize Value">Bills Receiveable (USD)</td><td><?// echo number_format($bill_receive,2) ?></td></tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr><td colspan="2" style="font-size:16px; font-weight:bold;">Accounts Balance :</td></tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr><td>BTB To Be Open (USD)</td><td><?// echo number_format($btb_open,2) ?></td></tr>
				<tr><td>Order In Hand (USD)</td><td title="<?// echo $tot_pending_ord_qnty; ?>"><p><a href="##" onClick="openmypage_popup('<?// echo $company_id; ?>','Order In Hand Info','order_in_hand_popup');" > <?// echo number_format($pending_ord_value,2) ?></a></p></td>
				</tr>
			</table>-->
		</fieldset>
		</div>
		<?
 
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=71 and b.mail_user_setup_id=c.id and a.company_id=$compid and   A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		$toArr[$row[csf('email_address')]]=$row[csf('email_address')]; 
	}
	
 	$to=implode(',',$toArr);
	
	$subject = "Bank Liability Position As Of Today";
	
	$message=ob_get_contents();
	ob_clean();
	$header=mailHeader();
	// if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);
	if($_REQUEST['isview']==1){
		$mail_item=71;
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);
	}



	

}
	
	





?> 