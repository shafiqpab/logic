<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$seource_des_array=array(3=>"Non EPZ",4=>"Non EPZ",5=>"Abroad",6=>"Abroad",11=>"EPZ",12=>"EPZ");

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_lein_bank=str_replace("'","",$cbo_lein_bank); 
	$txt_exchange_rate=str_replace("'","",$txt_exchange_rate);
	//echo $hide_year;die;
	$company_arr=return_library_array( "select company_name,id from  lib_company",'id','company_name');
	$bank_arr=return_library_array( "select bank_name,id from lib_bank",'id','bank_name');
	
	/*$lc_sc_sql="select id as lc_sc_id, lien_bank, convertible_to_lc, contract_value as lc_sc_value, converted_from, 1 as type from com_sales_contract where beneficiary_name=$cbo_company_name and lien_bank=$cbo_lein_bank and sc_year<='$hide_year' and sc_year>'2015'
	union all
	select id as lc_sc_id, lien_bank, replacement_lc as convertible_to_lc, lc_value as lc_sc_value, null as converted_from, 2 as type from com_export_lc where beneficiary_name=$cbo_company_name and lien_bank=$cbo_lein_bank and lc_year<='$hide_year' and lc_year>'2015'";
	
	//echo $lc_sc_sql;
	//where beneficiary_name=$cbo_company_name and lien_bank=$cbo_lein_bank and lc_year='$hide_year'
	//echo $lc_sc_sql;//die;
	
	$lc_sc_sql_result=sql_select($lc_sc_sql);
	$lc_id=$sc_id="";
	$file_value=$sc_value_1_3=$lc_value_1=$sc_value_2=$lc_value_2=0;
	foreach($lc_sc_sql_result as $row)
	{
		if($row[csf("type")]==1)
		{
			$sc_id.=$row[csf("lc_sc_id")].",";
			if($row[csf("convertible_to_lc")]!=2)
			{
				$sc_value_1_3+=$row[csf("lc_sc_value")];
			}
			else
			{
				if($row[csf("converted_from")]>0)
				{
					$lc_value_1+=$row[csf("lc_sc_value")];
				}
				else
				{
					$sc_value_2+=$row[csf("lc_sc_value")];
				}
			}
		}
		else
		{
			$lc_id.=$row[csf("lc_sc_id")].",";
			if($row[csf("convertible_to_lc")]==2)
			{
				$lc_value_2+=$row[csf("lc_sc_value")];
			}
			else
			{
				$lc_value_1+=$row[csf("lc_sc_value")];
			}
		}
	}
	//$file_value=(($sc_value_1_3-$lc_value_1)+$sc_value_2+$lc_value_2+$lc_value_1);
	$file_value=($sc_value_1_3+$sc_value_2+$lc_value_2);
	$sc_id=chop($sc_id,",");
	$lc_id=chop($lc_id,",");
	if($sc_id!="")
	{
		$sc_id_arr=array_unique(explode(",",$sc_id));
		if($db_type==0)
		{
			$rlz_sc_cond=" and a.lc_sc_id in(".implode(",",$sc_id_arr).")";
		}
		else
		{
			$sc_id_arr=array_chunk($sc_id_arr,999);
			$rlz_sc_cond=" and (";
			foreach($sc_id_arr as $id_arr)
			{
				$rlz_sc_cond.="a.lc_sc_id in(".implode(",",$id_arr).") or";
			}
			$rlz_sc_cond=chop($rlz_sc_cond,"or");
			$rlz_sc_cond.=")";
			//echo $rlz_sc_cond;die;
		}
	}
	
	if($lc_id!="")
	{
		$lc_id_arr=array_unique(explode(",",$lc_id));
		if($db_type==0)
		{
			$rlz_lc_cond=" and a.lc_sc_id in(".implode(",",$lc_id_arr).")";
		}
		else
		{
			$lc_id_arr=array_chunk($lc_id_arr,999);
			$rlz_lc_cond=" and (";
			foreach($lc_id_arr as $id_arr)
			{
				$rlz_lc_cond.="a.lc_sc_id in(".implode(",",$id_arr).") or";
			}
			$rlz_lc_cond=chop($rlz_lc_cond,"or");
			$rlz_lc_cond.=")";
		}
	}
	if($lc_id=="" && $sc_id=="") die;*/
	
	$sql_cond_payment="";
	if($cbo_company_name>0) $sql_cond_payment=" and a.company_id=$cbo_company_name";
	$sql_payment="select a.lc_id, a.invoice_id, b.accepted_ammount from com_import_payment_mst a, com_import_payment b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $sql_cond_payment";
	$sql_payment_result=sql_select($sql_payment);
	$lc_wise_payment=array();$invoice_wise_payment=array();
	foreach($sql_payment_result as $row)
	{
		//$lc_wise_payment[$row[csf("lc_id")]]+=$row[csf("accepted_ammount")];
		$invoice_wise_payment[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")];
	}
	unset($sql_payment_result);
	
	$sql_cond="";
	if($cbo_company_name>0) $sql_cond=" and a.importer_id=$cbo_company_name";
	if($cbo_lein_bank>0) $sql_cond.=" and a.issuing_bank_id=$cbo_lein_bank";
	
	
	$ifdbc_edf_sql="select a.id as btb_lc_id, a.importer_id, a.issuing_bank_id, a.lc_category, a.lc_number, a.lc_value, a.maturity_from_id, a.payterm_id, a.lc_type_id, b.id as import_inv_id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, sum(c.current_acceptance_value) as edf_loan_value, 1 as type 
	from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
	where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and to_number(a.lc_category)>0 $sql_cond 
	group by a.id, a.importer_id, a.issuing_bank_id, a.lc_category, a.lc_number, a.lc_value, a.maturity_from_id, a.payterm_id, a.lc_type_id, b.id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source
	order by btb_lc_id";
	//echo $ifdbc_edf_sql;die;
	$ifdbc_edf_sql_result=sql_select($ifdbc_edf_sql);
	$ifdbc_edf_data=array();$edf_count=0;
	foreach($ifdbc_edf_sql_result as $row)
	{
		if($row[csf("lc_type_id")]==2)
		{
			$margin_btb_data[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]+=$row[csf("edf_loan_value")]-$invoice_wise_payment[$row[csf("import_inv_id")]];
			$margin_btb_bank_total[$row[csf("issuing_bank_id")]]+=$row[csf("edf_loan_value")]-$invoice_wise_payment[$row[csf("import_inv_id")]];
		}
		else
		{
			$lc_wise_payment[$row[csf("btb_lc_id")]]+=$row[csf("edf_loan_value")];
			$maturity_date="";
			if($row[csf("maturity_from_id")]==1) $maturity_date=$row[csf("bank_acc_date")];
			else if($row[csf("maturity_from_id")]==2) $maturity_date=$row[csf("shipment_date")];
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
					if($row[csf("edf_paid_date")] != "" && $row[csf("edf_paid_date")] != "0000-00-00")
					{
						$paid_value=$row[csf("edf_loan_value")];
					}
				}
				
				if($maturity_date!="" && $maturity_date!="0000-00-00")
				{
					//$edf_count++;
					//$edf_count_data[$row[csf("import_inv_id")]]=($row[csf("edf_loan_value")]-$paid_value)."=".$row[csf("lc_number")]."=".$row[csf("lc_value")];
					$ifdbc_edf_data[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]["edf"]+=$row[csf("edf_loan_value")]-$paid_value;
					$edf_bank_total[$row[csf("issuing_bank_id")]]+=$row[csf("edf_loan_value")]-$paid_value;
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
	//echo "<pre>";print_r($margin_btb_data);die;
	
	
	$btb_sql="select a.id, a.importer_id, a.issuing_bank_id, a.lc_type_id, a.lc_value from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.lc_type_id<>2 $sql_cond order by a.importer_id, a.issuing_bank_id";
	$btb_sql_result=sql_select($btb_sql);
	$btb_data=array();$all_btb_company=array();$all_btb_bank=array();
	foreach($btb_sql_result as $row)
	{
		$pending_value=$row[csf("lc_value")]-$lc_wise_payment[$row[csf("id")]];
		$all_btb_company[$row[csf("importer_id")]]=$row[csf("importer_id")];
		$all_btb_bank[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]=$row[csf("issuing_bank_id")];
		if($pending_value>0)
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
	/*$ma_sql="select a.id as btb_lc_id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.lc_value, b.invoice_no, b.maturity_date, sum(c.current_acceptance_value) as accep_value
	from com_btb_lc_master_details a, com_import_invoice_dtls c, com_import_invoice_mst b 
	where a.id=c.btb_lc_id and c.import_invoice_id=b.id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.is_deleted=0 and a.status_active=1  and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id=2
	group by a.id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.lc_value, b.invoice_no, b.maturity_date
	order by a.lc_category, a.id";*/
	unset($btb_sql_result);
	//company_id
	$exfact_com_cond="";
	if($cbo_company_name>0) $exfact_com_cond=" and a.company_id=$cbo_company_name";
	$ex_fact_data=return_library_array("select b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
	//echo "<pre>";print_r($ex_fact_data);die;
	$com_cond="";
	if($cbo_company_name>0) $com_cond=" and b.company_name=$cbo_company_name";
	$order_sql="select a.po_break_down_id, sum(a.order_quantity) as order_quantity, sum(a.order_total) as order_total from wo_po_color_size_breakdown a, wo_po_details_master b, wo_po_break_down c 
	where b.job_no=c.job_no_mst and a.po_break_down_id=c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.shiping_status<>3 $com_cond 
	group by a.po_break_down_id";
	//echo $order_sql;
	$order_sql_result=sql_select($order_sql);
	$pending_ord_qnty=0;$tot_pending_ord_qnty=0;
	$pending_ord_value=0;
	foreach($order_sql_result as $row)
	{
		$ord_rate=0;
		if($row[csf("order_total")]>0 && $row[csf("order_quantity")] >0)
		{
			$ord_rate=($row[csf("order_total")]/$row[csf("order_quantity")])*1;
		}
		$pendin_qnty=($row[csf("order_quantity")]-$ex_fact_data[$row[csf("po_break_down_id")]])*1;
		$tot_pending_ord_qnty+=$pendin_qnty;
		$pending_ord_value+=($pendin_qnty*$ord_rate);
		
	}
	//echo $pendin_qnty."ord_rate".$ord_rate."ord_rates".$pending_ord_value;
	
	unset($order_sql_result);
	if($cbo_company_name>0) $job_btb_cond=" and d.importer_id=$cbo_company_name";
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
	//$budge_btb_open_sql="select sum(b.fabric_cost) as fabric_cost, sum(b.trims_cost) as trims_cost, sum(b.embel_cost) as embel_cost from wo_po_details_master a, wo_pre_cost_dtls b where a.job_no=b.job_no and a.status_active=1 and b.status_active=1 $budge_com_cond ";
	
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
	}
	
	//+($row[csf("trims_cost")]-$job_wise_btb_value[$row[csf("job_no")]][2])+$row[csf("embel_cost")]
	//echo $btb_open; die;
	
	$beneficiary_cond="";
	if($cbo_company_name>0) $beneficiary_cond=" and b.benificiary_id=$cbo_company_name";
	$proceed_rlz_sql="select b.invoice_bill_id, c.document_currency as document_currency from com_export_proceed_realization b, com_export_proceed_rlzn_dtls c where b.id=c.mst_id and b.is_invoice_bill=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $beneficiary_cond";
	//echo $proceed_rlz_sql;die;
	$realize_data_arr=array();
	$proceed_rlz_sql_result=sql_select($proceed_rlz_sql);
	$rlz_value=0;
	foreach($proceed_rlz_sql_result as $row)
	{
		$realize_data_arr[$row[csf("invoice_bill_id")]]+=$row[csf("document_currency")];
		$rlz_value+=$row[csf("document_currency")];
	}
	unset($proceed_rlz_sql_result);
	
	$bill_com_cond="";
	if($cbo_company_name>0) $bill_com_cond=" and b.company_id=$cbo_company_name";
	$bill_sql="select b.id as bill_id, b.bank_ref_no, b.company_id, b.possible_reali_date, b.lien_bank, sum(a.net_invo_value) as bill_value from com_export_doc_submission_invo a, com_export_doc_submission_mst b where a.doc_submission_mst_id=b.id and b.entry_form = 40 and b.lien_bank > 0 and b.submit_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond group by b.id, b.bank_ref_no, b.company_id, b.possible_reali_date, b.lien_bank";
	$bill_sql_result=sql_select($bill_sql);
	$fdbp_data=array();
	foreach($bill_sql_result as $row)
	{
		$fdbp_data[$row[csf("company_id")]][$row[csf("lien_bank")]]+=$row[csf("bill_value")]-$realize_data_arr[$row[csf("bill_id")]];
		$fdbp_bank_data[$row[csf("lien_bank")]]+=$row[csf("bill_value")]-$realize_data_arr[$row[csf("bill_id")]];
	}
	unset($bill_sql_result);
	
	$bill_coll_sql="select b.id as bill_id, sum(a.net_invo_value) as bill_value from com_export_doc_submission_invo a, com_export_doc_submission_mst b where a.doc_submission_mst_id=b.id and b.entry_form = 40 and b.submit_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond group by b.id";
	$bill_coll_sql_result=sql_select($bill_coll_sql);
	$bill_receive=0;
	foreach($bill_coll_sql_result as $row)
	{
		$bill_receive+=$row[csf("bill_value")]-$realize_data_arr[$row[csf("bill_id")]];
	}
	unset($bill_coll_sql_result);
	$pak_beneficiary_cond="";
	if($cbo_company_name>0) $pak_beneficiary_cond=" and b.beneficiary_id=$cbo_company_name";
	$packing_sql=sql_select("select sum(a.loan_amount) as loan_amount  from com_pre_export_finance_dtls a, com_pre_export_finance_mst b where a.mst_id=b.id and a.loan_type=20 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $pak_beneficiary_cond");
	$odg_amt=0;
	foreach($packing_sql as $row)
	{
		$odg_amt+=$row[csf("loan_amount")];
	}
	unset($packing_sql);
	$bank_id_cond="";
	if($cbo_lein_bank>0) $bank_id_cond=" and a.id=$cbo_lein_bank";
	$bank_cd_sql=sql_select("select sum(b.loan_limit) as loan_amount from lib_bank a, lib_bank_account b where a.id=b.account_id and b.account_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bank_id_cond");
	$sod_cd_amt=0;
	foreach($bank_cd_sql as $row)
	{
		$sod_cd_amt+=$row[csf("loan_amount")];
	}
	unset($bank_cd_sql);
	
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
	$table_width=(200+(100*$tot_col));
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
	            </tr>
	            <tr>
	            	<?
					foreach($all_btb_bank as $com_id=>$com_data)
					{
						?>
	                    <th colspan="<? echo count($com_data)+1; ?>" title="<? echo $com_id; ?>"><? echo $company_arr[$com_id];?></th>
	                    <?
					}
					foreach($bank_check as $bank_id)
					{
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
							?>
	                        <th width="100"><? echo $bank_arr[$bank_id];?></th>
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
                        <td align="right"><? echo number_format($com_btb_total,2); $com_btb_total=0;?></td>
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
	                        <td align="right" title="<? print_r($fdbp_count_data);?>"><a href='#report_detals'  onclick= "openmypage('<? echo $com_id; ?>','<? echo $bank_id; ?>','ifdbc_popup','IFDBC Info','2')"><? echo number_format($ifdbc_edf_data[$com_id][$bank_id]["ifdbc"],2);?></a>.</td>
	                        <?	
							$total_libiality[$com_id][$bank_id]+=$ifdbc_edf_data[$com_id][$bank_id]["ifdbc"];
							$com_ifdbc_total+=$ifdbc_edf_data[$com_id][$bank_id]["ifdbc"];
						}
						?>
                        <td align="right"><? echo number_format($com_ifdbc_total,2);$com_ifdbc_total=0;?></td>
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
                        <td align="right"><? echo number_format($com_edf_total,2); $com_edf_total=0;?></td>
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
                        <td align="right"><? echo number_format($com_fdbp_total,2); $com_fdbp_total=0;?></td>
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
	            </tr>
	            <tr bgcolor="#CCCCCC">
	            	<td align="center">5</td>
	                <td>Total Liability (USD)</td>
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
                        <td align="right"><? echo number_format($com_libiality_usd,2); $com_libiality_usd=0;?></td>
                        <?
					}
					foreach($bank_check as $bank_id)
					{
						?>
	                    <td align="right"><? echo number_format($total_bank_libiality[$bank_id],2);?></td>
	                    <?
					}
					?>
	            </tr>
	            <tr bgcolor="#FFFFCC">
	            	<td align="center">6</td>
	                <td>Total Liability (BDT)</td>
	                <?
					foreach($all_btb_bank as $com_id=>$com_data)
					{
						foreach($com_data as $bank_id)
						{
							?>
	                        <td align="right"><? echo number_format(($total_libiality[$com_id][$bank_id]*$txt_exchange_rate),2);?></td>
	                       <?
							$com_tot_libiality_usd+=($total_libiality[$com_id][$bank_id]*$txt_exchange_rate);
						}
						?>
                        <td align="right"><? echo number_format($com_tot_libiality_usd,2); $com_tot_libiality_usd=0;?></td>
                        <?
					}
					foreach($bank_check as $bank_id)
					{
						?>
	                    <td align="right"><? echo number_format(($total_bank_libiality[$bank_id]*$txt_exchange_rate),2);?></td>
	                    <?
					}
					?>
	            </tr>
                <tr bgcolor="#CCCCCC">
	            	<td align="center"></td>
	                <td></td>
	                <?
					foreach($all_btb_bank as $com_id=>$com_data)
					{
						foreach($com_data as $bank_id)
						{
							?>
	                        <td align="right">&nbsp;</td>
	                        <?
						}
						?>
                        <td align="right">&nbsp;</td>
                        <?
					}
					foreach($bank_check as $bank_id)
					{
						?>
	                    <td align="right">&nbsp;</td>
	                    <?
					}
					?>
	            </tr>
                <tr bgcolor="#FFFFCC">
	            	<td align="center">7</td>
	                <td>IFDBC - Machinery</td>
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
                        <td align="right"><? echo number_format($com_tot_margin_btb,2); $com_tot_margin_btb=0;?></td>
                        <?
					}
					foreach($bank_check as $bank_id)
					{
						?>
	                    <td align="right"><? echo number_format(($margin_btb_bank_total[$bank_id]),2);?></td>
	                    <?
					}
					?>
	            </tr>
	        </tbody>
	    </table>
	    <table width="400" cellpadding="0" cellspacing="0" align="left" style="margin-top:30px;">
	    	<tr><td colspan="2" style="font-size:16px; font-weight:bold;">Others :</td></tr>
	        <tr><td colspan="2">&nbsp;</td></tr>
	        <tr><td width="250">Export Under Execution (USD)</td><td><? echo number_format($pending_ord_qnty,2) ?></td></tr>
	        <tr><td title="Bill Value - Realize Value">Bills Receiveable (USD)</td><td><? echo number_format($bill_receive,2) ?></td></tr>
	        <tr><td colspan="2">&nbsp;</td></tr>
	        <tr><td colspan="2" style="font-size:16px; font-weight:bold;">Accounts Balance :</td></tr>
	        <tr><td colspan="2">&nbsp;</td></tr>
	        <tr><td>ODG (PC) (BDT)</td><td><? echo number_format($odg_amt,2) ?></td></tr>
	        <tr><td>SOD (CD Limit) (BDT)</td><td><? echo number_format($sod_cd_amt,2) ?></td></tr>
	        <tr><td>BTB To Be Open (USD)</td><td><? echo number_format($btb_open,2) ?></td></tr>
	        <tr><td>Order In Hand (USD)</td><td title="<? echo $tot_pending_ord_qnty; ?>"><p><a href="##" onClick="openmypage_popup('<? echo $company_id; ?>','Order In Hand Info','order_in_hand_popup');" > <? echo number_format($pending_ord_value,2) ?></a></p></td>
	        </tr>
	    </table>
	</fieldset>
	</div>
	<?
		foreach (glob("$user_id*.xls") as $filename) 
		{
			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename";
		exit();
}

if($action=="btb_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	$inv_sql="select a.id as btb_lc_id, c.current_acceptance_value as accep_value
	from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
	where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id<>2";
	$inv_result=sql_select($inv_sql);
	$accp_data=array();
	foreach($inv_result as $row)
	{
		$accp_data[$row[csf("btb_lc_id")]]+=$row[csf("accep_value")];
	}
	
	$btb_sql="select a.id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id<>2 order by to_number(a.lc_category) ";
	//echo $btb_sql;//die;
	$btb_sql_result=sql_select($btb_sql);
	/*$btb_data=array();$all_btb_company=array();$all_btb_bank=array();
	foreach($btb_sql_result as $row)
	{
		$all_btb_company[$row[csf("importer_id")]]=$row[csf("importer_id")];
		$all_btb_bank[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]=$row[csf("issuing_bank_id")];
		$btb_data[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]+=$row[csf("lc_value")]-$lc_wise_payment[$row[csf("id")]];
		$btb_bank_total[$row[csf("issuing_bank_id")]]+=$row[csf("lc_value")]-$lc_wise_payment[$row[csf("id")]];
	}*/
	?>
    
    <script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
    
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="report_container" align="center" style="width:820px">
	<fieldset style="width:820px;">
        <table class="rpt_table" border="1" rules="all" width="800" cellpadding="0" cellspacing="0">
            <thead>
                <th width="50">SL</th>
                <th width="120">BTB LC No</th>
                <th width="130">Applicant</th>
                <th width="130">Bank</th>
                <th width="130"> Benficiary</th>
                <th width="70">LC Date</th>
                <th width="70">LC Expiry Date</th>
                <th>LC Amount (USD)</th>
            </thead>
            <tbody>
            <? 
			$i=1; $r=1;
			foreach($btb_sql_result as $row)  
			{
				$pendin_value =$row[csf("lc_value")]-$accp_data[$row[csf("id")]];
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($cat_check[$row[csf("lc_category")]*1]=="" && $pendin_value>0)
				{
					$cat_check[$row[csf("lc_category")]*1]=$row[csf("lc_category")]*1;
					
					if($r==1)
					{
						?>
                        <tr bgcolor="#FFFFCC"><td colspan="8" title="<? echo $row[csf("lc_category")]; ?>"><? echo $supply_source[$row[csf("lc_category")]*1]; ?></td></tr>
                        <?
					}
					else
					{
						?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="7" align="right" title="<? echo $row[csf("lc_category")]; ?>">Currency Wise Product Total:</td>
                            <td align="right"><? echo number_format($cat_pendin_value,2);  ?></td>
                        </tr>
                        <tr bgcolor="#FFFFCC"><td colspan="8" title="<? echo $row[csf("lc_category")]; ?>"><? echo $supply_source[$row[csf("lc_category")]*1]; ?></td></tr>
                        <?
					}
					$cat_pendin_value=0;$r++;
				}
				
				if($pendin_value>0)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center" title="<? echo $r; ?>"><? echo $i; ?></td>
                        <td><p><? echo $row[csf('lc_number')]; ?>&nbsp;</p></td>
                        <td><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                        <td><p><? echo $bank_arr[$row[csf('issuing_bank_id')]]; ?>&nbsp;</p></td>
                        <td><p><? echo $company_arr[$row[csf('importer_id')]]; ?>&nbsp;</p></td>
                        <td align="center"><p><? if($row[csf('lc_date')] !="" && $row[csf('lc_date')] !="0000-00-00") echo change_date_format($row[csf('lc_date')]);  ?>&nbsp;</p></td>
                        <td align="center"><p><? if($row[csf('lc_expiry_date')] !="" && $row[csf('lc_expiry_date')] !="0000-00-00") echo change_date_format($row[csf('lc_expiry_date')]);  ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($pendin_value,2);  ?></td>
                    </tr>
                    <?
                    $tot_pendin_value+=$pendin_value;
					$cat_pendin_value+=$pendin_value;
				}
				$i++;
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td colspan="7" align="right">Currency Wise Product Total:</td>
                <td align="right"><? echo number_format($cat_pendin_value,2);  ?></td>
            </tr>
            </tbody>
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="7" align="right">Total:</td>
                    <td align="right" id="value_tot_pendin_value"><? echo number_format($tot_pendin_value,2); ?></td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
}

if($action=="margine_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	/*$inv_sql="select a.id as btb_lc_id, b.invoice_no, b.maturity_date, c.current_acceptance_value as accep_value
	from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
	where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id=2";
	$inv_result=sql_select($inv_sql);
	$accp_data=array();
	foreach($inv_result as $row)
	{
		$accp_data[$row[csf("btb_lc_id")]]+=$row[csf("accep_value")];
	}*/
	
	
	$sql_cond_payment="";
	if($company_id>0) $sql_cond_payment=" and a.company_id=$company_id";
	$sql_payment="select a.lc_id, a.invoice_id, b.accepted_ammount from com_import_payment_mst a, com_import_payment b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $sql_cond_payment";
	$sql_payment_result=sql_select($sql_payment);
	$lc_wise_payment=array();$invoice_wise_payment=array();
	foreach($sql_payment_result as $row)
	{
		//$lc_wise_payment[$row[csf("lc_id")]]+=$row[csf("accepted_ammount")];
		$invoice_wise_payment[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")];
	}
	
	
	
	$btb_sql="select a.id as btb_lc_id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.lc_value, b.id as inv_id, b.invoice_no, b.maturity_date, sum(c.current_acceptance_value) as accep_value
	from com_btb_lc_master_details a, com_import_invoice_dtls c, com_import_invoice_mst b 
	where a.id=c.btb_lc_id and c.import_invoice_id=b.id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.is_deleted=0 and a.status_active=1  and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id=2
	group by a.id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.lc_value, b.id, b.invoice_no, b.maturity_date
	order by a.lc_category, a.id";
	//echo $btb_sql;	
	/*
	$btb_sql="select a.id as btb_lc_id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.lc_value, b.invoice_no, b.maturity_date, sum(c.current_acceptance_value) as accep_value
	from com_btb_lc_master_details a 
	left join com_import_invoice_dtls c on a.id=c.btb_lc_id and c.is_deleted=0 and c.status_active=1
	left join com_import_invoice_mst b on c.import_invoice_id=b.id and b.is_deleted=0 and b.status_active=1
	where a.is_deleted=0 and a.status_active=1  and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id=2
	group by a.id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.lc_value, b.invoice_no, b.maturity_date
	order by a.lc_category, a.id";
	$btb_sql="select a.id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id=2 order by to_number(a.lc_category) ";*/
	//echo $btb_sql;//die;
	$btb_sql_result=sql_select($btb_sql);
	?>
    
    <script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
    
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="report_container" align="center" style="width:1040px">
	<fieldset style="width:1040px;">
        <table class="rpt_table" border="1" rules="all" width="1040" cellpadding="0" cellspacing="0">
            <thead>
                <th width="30">SL</th>
                <th width="120">Margin LC No</th>
                <th width="120">Invoice No</th>
                <th width="70">Currency</th>
                <th width="90">LC Amount</th>
                <th width="90">Accpt. Amount</th>
                <th width="90">Margin Amount</th>
                <th width="90">Net Due</th>
                <th width="90">Net Due (USD)</th>
                <th width="70">Maturity Date</th>
                <th>Supplier</th>
            </thead>
            <tbody>
            <? 
			$i=1; $r=1;
			foreach($btb_sql_result as $row)  
			{
				$margine_amt=0;
				//$pendin_value =$row[csf("lc_value")]-$accp_data[$row[csf("id")]];
				$pendin_value =$row[csf("accep_value")]-$invoice_wise_payment[$row[csf("inv_id")]];
				if($row[csf("margin")]) $margine_amt=($row[csf("lc_value")]/100)*$row[csf("margin")];
				$net_due=$row[csf('accep_value')]-$margine_amt;
				if($row[csf("currency_id")]==7) $net_due_usd=$net_due * 0.0093;
				else if($row[csf("currency_id")]==3) $net_due_usd=$net_due * 1.10;
				else $net_due_usd=$net_due * 1;
				if($cat_check[$row[csf("lc_category")]*1]=="" && $pendin_value>0)
				{
					$cat_check[$row[csf("lc_category")]*1]=$row[csf("lc_category")]*1;
					
					if($r==1)
					{
						?>
                        <tr bgcolor="#FFFFCC"><td colspan="11" title="<? echo $row[csf("lc_category")]; ?>"><? echo $supply_source[$row[csf("lc_category")]*1]; ?></td></tr>
                        <?
					}
					else
					{
						?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="8" align="right" title="<? echo $row[csf("lc_category")]; ?>">Currency Wise Product Total:</td>
                            <td align="right"><? echo number_format($cat_net_due_usd,2);  ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr bgcolor="#FFFFCC"><td colspan="11" title="<? echo $row[csf("lc_category")]; ?>"><? echo $supply_source[$row[csf("lc_category")]*1]; ?></td></tr>
                        <?
						$cat_net_due_usd=0;
					}
					$r++;
				}
				
				if($pendin_value>0)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center" title="<? echo $r; ?>"><? echo $i; ?></td>
                        <td><p><? echo $row[csf('lc_number')]; ?>&nbsp;</p></td>
                        <td><p><? echo $row[csf('invoice_no')]; ?>&nbsp;</p></td>
                        <td><p><? echo $currency[$row[csf('currency_id')]]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('lc_value')],2); ?></td>
                        <td align="right"><? echo number_format($row[csf('accep_value')],2); ?></td>
                        <td align="right"><? echo number_format($margine_amt,2);  ?></td>
                        <td align="right" title="<? echo $row[csf("margin")]; ?>"><? echo number_format($net_due,2);  ?></td>
                        <td align="right"><? echo number_format($net_due_usd,2);  ?></td>
                        <td align="center"><p><? if($row[csf('maturity_date')] !="" && $row[csf('maturity_date')] !="0000-00-00") echo change_date_format($row[csf('maturity_date')]);  ?>&nbsp;</p></td>
                        <td><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                    </tr>
                    <?
                    $tot_net_due_usd+=$net_due_usd;
					$cat_net_due_usd+=$net_due_usd;
				}
				$i++;
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td colspan="8" align="right" title="<? echo $row[csf("lc_category")]; ?>">Currency Wise Product Total:</td>
                <td align="right"><? echo number_format($cat_net_due_usd,2);  ?></td>
                <td></td>
                <td></td>
            </tr>
            </tbody>
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total:</td>
                    <td align="right" id="value_tot_pendin_value"><? echo number_format($tot_net_due_usd,2); ?></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
}

if($action=="ifdbc_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	//echo $type.test;die;
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	//$sql_payment="select a.lc_id, a.invoice_id, b.accepted_ammount from com_import_payment_mst a, com_import_payment b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $sql_cond_payment";
	$sql_payment="select a.lc_id, b.invoice_id, b.accepted_ammount from com_import_payment_mst a, com_import_payment b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $sql_cond_payment";
	//echo $sql_payment;die;
	$sql_payment_result=sql_select($sql_payment);
	$lc_wise_payment=array();$invoice_wise_payment=array();
	foreach($sql_payment_result as $row)
	{
		$invoice_wise_payment[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")];
	}
	//echo $invoice_wise_payment[322];die;
	if($type==2) $retire_source_cond=" and b.retire_source not in(30,31)"; else $retire_source_cond=" and b.retire_source in(30,31)";
	$ifdbc_edf_sql="select a.id as btb_lc_id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value, a.maturity_from_id, a.payterm_id, b.id as import_inv_id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, b.loan_ref, sum(c.current_acceptance_value) as edf_loan_value, 1 as type 
	from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
	where a.id=c.btb_lc_id and c.import_invoice_id=b.id  and a.lc_category>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and to_number(a.lc_category)>0 and a.payterm_id<>3 and a.lc_type_id<>2 $retire_source_cond
	group by a.id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value, a.maturity_from_id, a.payterm_id, b.id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, b.loan_ref
	order by to_number(a.lc_category),btb_lc_id";
	//echo $ifdbc_edf_sql;die;
	$ifdbc_edf_sql_result=sql_select($ifdbc_edf_sql);
	?>
    
    <script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
    
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="report_container" align="center" style="width:920px">
	<fieldset style="width:920px;">
        <table class="rpt_table" border="1" rules="all" width="900" cellpadding="0" cellspacing="0">
            <thead>
                <th width="50">SL</th>
                <th width="120">BTB LC No</th>
                <?
				if($type==3)
				{
					?>
                    <th width="100">EDF No.</th>
                    <?
				}
				?>
                <th width="100">LC Amount (USD)</th>
                <th width="100">Acc. Amount (USD)</th>
                <?
				if($type==2)
				{
					?>
					<th width="100">Acc. Date</th>
					<th width="100">Maturity Date</th>
					<th> Suppliers</th>
                    <?
				}
				else
				{
					?>
					<th width="100">Disbursement Date</th>
					<th width="100">Maturity Date</th>
					<th> Suppliers</th>
                    <?
				}
				?>
            </thead>
            <tbody>
            <? 
			$i=1; $r=1;
			//echo "<pre>";print_r($ifdbc_edf_sql_result);die;
			foreach($ifdbc_edf_sql_result as $row)  
			{
				//echo $type."=".$row[csf("retire_source")]."<br>";
				$paid_value = 0;
				if($type==2)
				{
					$pending_value=$row[csf("edf_loan_value")]-$invoice_wise_payment[$row[csf("import_inv_id")]];
				}
				else
				{
					$paid_value=0;
					if($row[csf("payterm_id")]==3) 
					{
						$paid_value=$row[csf("edf_loan_value")];
					}
					else
					{
						if($row[csf("edf_paid_date")] != "" && $row[csf("edf_paid_date")] != "0000-00-00")
						{
							$paid_value=$row[csf("edf_loan_value")];
						}
					}
					$pending_value=$row[csf("edf_loan_value")]-$paid_value;
				}
				//if($row[csf('lc_number')]=="802180514423") echo $paid_value.test;
				$maturity_date="";
				if($row[csf("maturity_from_id")]==1) $maturity_date=$row[csf("bank_acc_date")];
				else if($row[csf("maturity_from_id")]==2) $maturity_date=$row[csf("shipment_date")];
				else if($row[csf("maturity_from_id")]==3) $maturity_date=$row[csf("nagotiate_date")];
				else if($row[csf("maturity_from_id")]==4) $maturity_date=$row[csf("bill_date")];
				else $maturity_date="";
				 
				if($cat_check[$row[csf("lc_category")]*1]=="" && $pending_value>0 && $maturity_date!="" && $maturity_date!="0000-00-00")
				{
					$cat_check[$row[csf("lc_category")]*1]=$row[csf("lc_category")]*1;
					if($type==3){ $tot_col_span=8;  $col_span=3;} else { $tot_col_span=7; $col_span=2;}
					if($r==1)
					{
						?>
						<tr bgcolor="#FFFFCC"><td colspan="<? echo $tot_col_span;?>" title="<? echo $row[csf("lc_category")]; ?>"><? echo $supply_source[$row[csf("lc_category")]*1]; ?></td></tr>
						<?
					}
					else
					{
						?>
						<tr bgcolor="#CCCCCC">
							<td colspan="<? echo $col_span;?>" align="right" title="<? echo $row[csf("lc_category")]; ?>">Currency Wise Product Total:</td>
							<td align="right"><? echo number_format($cat_lc_value,2);  ?></td>
							<td align="right"><? echo number_format($cat_pendin_value,2);  ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
						</tr>
						<tr bgcolor="#FFFFCC"><td colspan="<? echo $tot_col_span;?>" title="<? echo $row[csf("lc_category")]; ?>"><? echo $supply_source[$row[csf("lc_category")]*1]; ?></td></tr>
						<?
					}
					$cat_lc_value=$cat_pendin_value=0;$r++;
				}
				
				
				if($pending_value>0 && $maturity_date!="" && $maturity_date!="0000-00-00")
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center" title="<? echo $r; ?>"><? echo $i; ?></td>
						<td><p><? echo $row[csf('lc_number')]; ?>&nbsp;</p></td>
                        <?
						if($type==3)
						{
							?>
                            <td><p><? echo $row[csf('loan_ref')]; ?>&nbsp;</p></td>
                            <?
						}
						?>
						<td align="right"><? echo number_format($row[csf('lc_value')],2);  ?></td>
						<td align="right" title="<? echo $row[csf("edf_loan_value")]."=".$invoice_wise_payment[$row[csf("import_inv_id")]]; ?>"><? echo number_format($pending_value,2);  ?></td>
                        <td align="center"><p><? echo change_date_format($maturity_date);  ?>&nbsp;</p></td>
                        <td align="center"><p><? if($row[csf('maturity_date')] !="" && $row[csf('maturity_date')] !="0000-00-00") echo change_date_format($row[csf('maturity_date')]);  ?>&nbsp;</p></td>
                        <td align="center"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
					</tr>
					<?
					$tot_lc_value+=$row[csf('lc_value')];
					$cat_lc_value+=$row[csf('lc_value')];
					$tot_pendin_value+=$pending_value;
					$cat_pendin_value+=$pending_value;
					$i++;
				}
			}
			
			?>
            <tr bgcolor="#CCCCCC">
                <td colspan="<? echo $col_span;?>" align="right" title="<? echo $row[csf("lc_category")]; ?>">Currency Wise Product Total:</td>
                <td align="right"><? echo number_format($cat_lc_value,2);  ?></td>
                <td align="right"><? echo number_format($cat_pendin_value,2);  ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?
			if($type==3) $col_span=3; else $col_span=2;
			
			?>
            </tbody>
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="<? echo $col_span;?>" align="right">Total:</td>
                    <td align="right" id="value_tot_lc_value"><? echo number_format($tot_lc_value,2); ?></td>
                    <td align="right" id="value_tot_pendin_value"><? echo number_format($tot_pendin_value,2); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
}

if($action=="fdbp_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	//echo $type.test;die;
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	//$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	//$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	$lc_sc_sql=sql_select("select id, export_lc_no, 1 as type from com_export_lc where beneficiary_name=$company_id
	union all select id, contract_no as export_lc_no, 2 as type from com_sales_contract where beneficiary_name=$company_id");
	$lc_sc_num=array();
	foreach($lc_sc_sql as $row)
	{
		$lc_sc_num[$row[csf("id")]][$row[csf("type")]]=$row[csf("export_lc_no")];
	}
	unset($lc_sc_sql);
	$proceed_rlz_sql="select b.invoice_bill_id, c.document_currency as document_currency 
	from com_export_proceed_realization b, com_export_proceed_rlzn_dtls c 
	where b.id=c.mst_id and b.is_invoice_bill=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.benificiary_id=$company_id";
	//echo $proceed_rlz_sql;die;
	$realize_data_arr=array();
	$proceed_rlz_sql_result=sql_select($proceed_rlz_sql);
	foreach($proceed_rlz_sql_result as $row)
	{
		$realize_data_arr[$row[csf("invoice_bill_id")]]+=$row[csf("document_currency")];
	}
	unset($proceed_rlz_sql_result);
	if($db_type==0)
	{
		$invoice_sql="select b.doc_submission_mst_id, group_concat(a.invoice_no) as invoice_no, sum(a.invoice_quantity) as invoice_quantity from com_export_invoice_ship_mst a, com_export_doc_submission_invo b, com_export_doc_submission_mst c where a.id=b.invoice_id and b.doc_submission_mst_id=c.id and c.entry_form = 40 and c.lien_bank > 0 and c.submit_type=2 and c.company_id=$company_id and c.lien_bank=$bank_id and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.doc_submission_mst_id";
	}
	else
	{
		$invoice_sql="select b.doc_submission_mst_id, listagg(cast(a.invoice_no as varchar(4000)),',') within group (order by a.invoice_no) as invoice_no, sum(a.invoice_quantity) as invoice_quantity from com_export_invoice_ship_mst a, com_export_doc_submission_invo b, com_export_doc_submission_mst c where a.id=b.invoice_id and b.doc_submission_mst_id=c.id and c.entry_form = 40 and c.lien_bank > 0 and c.submit_type=2 and c.company_id=$company_id and c.lien_bank=$bank_id and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.doc_submission_mst_id";
	}
	//echo $invoice_sql;die;
	$invoice_result=sql_select($invoice_sql);
	$invoice_data=array();
	foreach($invoice_result as $row)
	{
		$invoice_data[$row[csf("doc_submission_mst_id")]]["invoice_no"]=implode(",",array_unique(explode(",",$row[csf("invoice_no")])));
		$invoice_data[$row[csf("doc_submission_mst_id")]]["invoice_quantity"]=$row[csf("invoice_quantity")];
	}
	unset($invoice_result);
	
	
	$bill_sql="select b.id as bill_id, b.buyer_id, b.bank_ref_no as bill_no, b.submit_date as bill_date, b.company_id, b.possible_reali_date, b.lien_bank, a.is_lc, a.lc_sc_id, a.id as bill_dtls_id, a.net_invo_value as bill_value, c.id as bill_pur_id, c.lc_sc_curr as bill_purchase_value 
	from com_export_doc_submission_invo a, com_export_doc_submission_mst b, com_export_doc_sub_trans c
	where a.doc_submission_mst_id=b.id and b.id=c.doc_submission_mst_id  and b.entry_form = 40 and b.lien_bank > 0 and b.submit_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id=$company_id and b.lien_bank=$bank_id";
	//echo $bill_sql;
	$bill_sql_result=sql_select($bill_sql);
	$fdbp_data=array();
	foreach($bill_sql_result as $row)
	{
		$fdbp_data[$row[csf("bill_id")]]["bill_id"]=$row[csf("bill_id")];
		$fdbp_data[$row[csf("bill_id")]]["buyer_id"]=$row[csf("buyer_id")];
		$fdbp_data[$row[csf("bill_id")]]["bill_no"]=$row[csf("bill_no")];
		$fdbp_data[$row[csf("bill_id")]]["bill_date"]=$row[csf("bill_date")];
		$fdbp_data[$row[csf("bill_id")]]["company_id"]=$row[csf("company_id")];
		$fdbp_data[$row[csf("bill_id")]]["possible_reali_date"]=$row[csf("possible_reali_date")];
		$fdbp_data[$row[csf("bill_id")]]["lien_bank"]=$row[csf("lien_bank")];
		$fdbp_data[$row[csf("bill_id")]]["is_lc"]=$row[csf("is_lc")];
		$fdbp_data[$row[csf("bill_id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
		if($bill_dtls_check[$row[csf("bill_dtls_id")]]=="")
		{
			$bill_dtls_check[$row[csf("bill_dtls_id")]]=$row[csf("bill_dtls_id")];
			$fdbp_data[$row[csf("bill_id")]]["bill_value"]+=$row[csf("bill_value")];
			$tot_bill_valu+=$row[csf("bill_value")];
		}
		if($bill_purchase_check[$row[csf("bill_pur_id")]]=="")
		{
			$bill_purchase_check[$row[csf("bill_pur_id")]]=$row[csf("bill_pur_id")];
			$fdbp_data[$row[csf("bill_id")]]["bill_purchase_value"]+=$row[csf("bill_purchase_value")];
		}
		
	}
	//echo "<br>".$tot_bill_valu.jahid;
	?>
    <script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
    
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="report_container" align="center" style="width:1030px">
	<fieldset style="width:1030px;">
        <table class="rpt_table" border="1" rules="all" width="1030" cellpadding="0" cellspacing="0">
            <thead>
                <th width="30">SL</th>
                <th width="130">Invoice No.</th>
                <th width="100">Export Bill No.</th>
                <th width="70">Bill Date</th>
                <th width="90">Inv/Bill Qty/Pcs</th>
                <th width="100">Bill Value</th>
                <th width="100">Purchase Amount</th>
                <th width="60">(%)</th>
                <th width="70">Purchase Date</th>
                <th width="100">LC/SC No</th>
                <th width="60">LC / SC</th>
                <th>Buyer Name</th>
            </thead>
            <tbody>
            <? 
			$i=1; $r=1;
			//echo "<pre>";print_r($ifdbc_edf_sql_result);die;
			$tot_pendin_value=0;
			foreach($fdbp_data as $bill_id=>$row)  
			{
				//echo $type."=".$row[csf("retire_source")]."<br>";
				$pendin_value = 0;
				$pendin_value=$row[("bill_value")]-$realize_data_arr[$row[("bill_id")]];
				$bill_purchase_percent=(($row[("bill_purchase_value")]/$row[("bill_value")])*100);
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($pendin_value>0)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center" title="<? echo $r; ?>"><? echo $i; ?></td>
						<td><p><? echo $invoice_data[$row[("bill_id")]]["invoice_no"]; ?>&nbsp;</p></td>
						<td><p><? echo $row[("bill_no")]; ?>&nbsp;</p></td>
                        <td align="center"><p><? if($row[('bill_date')] !="" && $row[('bill_date')] !="0000-00-00") echo change_date_format($row[('bill_date')]);?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($invoice_data[$row[("bill_id")]]["invoice_quantity"],2);  ?></td>
                        <td align="right"><? echo number_format($row[("bill_value")],2);  ?></td>
                        <td align="right"><? echo number_format($row[("bill_purchase_value")],2);  ?></td>
						<td align="right"><p><? echo number_format($bill_purchase_percent,2);?>&nbsp;</p></td>
                        <td align="center"><p><? if($row[('bill_date')] !="" && $row[('bill_date')] !="0000-00-00") echo change_date_format($row[('bill_date')]);?>&nbsp;</p></td>
						<td><p><? echo $lc_sc_num[$row[('lc_sc_id')]][$row[("is_lc")]]; ?>&nbsp;</p></td>
						<td align="center"><p><? if($row[("is_lc")]==1) echo "LC"; else echo "SC"; ?>&nbsp;</p></td>
						<td><p><? echo $buyer_arr[$row[("buyer_id")]]; ?>&nbsp;</p></td>
					</tr>
					<?
					$tot_bill_qnty+=$invoice_data[$row[("bill_id")]]["invoice_quantity"];
					$tot_bill_value+=$row[('bill_value')];
					$tot_bill_purchase_value+=$row[('bill_purchase_value')];
					$tot_pendin_value+=$pendin_value;
					$i++;
				}
				
			}
			?>
            </tbody>
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="4" align="right" title="<? echo $tot_pendin_value; ?>">Total:</td>
                    <td align="right" id="tot_bill_qnty"><? echo number_format($tot_bill_qnty,2); ?></td>
                    <td align="right" id="value_tot_bill_value"><? echo number_format($tot_bill_value,2); ?></td>
                    <td align="right" id="value_tot_bill_purchase_value"><? echo number_format($tot_bill_purchase_value,2); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
}


if($action=="order_in_hand_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$company_arr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$team_lead_arr = return_library_array("select id, team_leader_name from lib_marketing_team where status_active=1 and is_deleted=0","id","team_leader_name");
	$dealing_mer_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
	?>
	<script>
	function print_window()
	{
		$('#table_body tbody tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		$('#table_body tbody tr:first').show();
		d.close();
	}

	</script>	
    <? 
	ob_start(); 
	$html='<div id="report_container" align="center" style="width:2130px">
	<fieldset style="width:2130px;">
        <table class="rpt_table" border="1" rules="all" width="2130" cellpadding="0" cellspacing="0">
            <thead>
                <th width="30">SL</th>
                <th width="110">Company Name</th>
                <th width="110">Working Company</th>
                <th width="100">Buyer Name</th>
                <th width="100">Team Leader</th>
                <th width="100"> Dealing Merchant</th>
                <th width="70">Job No</th>
                <th width="110">Style No</th>
                <th width="110">PO No</th>
                <th width="100">SC/LC</th>
                <th width="100">Bank Name</th>
                <th width="70">Pub. Ship Date</th>
				<th width="60">UOM</th>
                <th width="130">PO Qty.</th>
                <th width="70">Unit Price</th>
                <th width="140">PO Value ($)</th>
                <th width="130">Ship Qty.</th>
                <th width="140">Ship Value ($)</th>
                <th width="130">In Hand Qty.</th>
                <th width="140">In Hand Value ($)</th>
                <th>Ship Status</th>
            </thead>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2130" cellpadding="0" cellspacing="0" id="table_body">
            <tbody>';
		?>
	<div id="report_container" align="center" style="width:2130px">
	<fieldset style="width:2130px;">
        <table class="rpt_table" border="1" rules="all" width="2130" cellpadding="0" cellspacing="0">
            <thead>
                <th width="30">SL</th>
                <th width="110">Company Name</th>
                <th width="110">Working Company</th>
                <th width="100">Buyer Name</th>
                <th width="100">Team Leader</th>
                <th width="100"> Dealing Merchant</th>
                <th width="70">Job No</th>
                <th width="110">Style No</th>
                <th width="110">PO No</th>
                <th width="100">SC/LC</th>
                <th width="100">Bank Name</th>
                <th width="70">Pub. Ship Date</th>
				<th width="60">UOM</th>
                <th width="130">PO Qty.</th>
                <th width="70">Unit Price</th>
                <th width="140">PO Value ($)</th>
                <th width="130">Ship Qty.</th>
                <th width="140">Ship Value ($)</th>
                <th width="130">In Hand Qty.</th>
                <th width="140">In Hand Value ($)</th>
                <th>Ship Status</th>
            </thead>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2130" cellpadding="0" cellspacing="0" id="table_body">
            <tbody>
            <? 
                $i=1; 
                $InHandValue=0;
                $shipValue=0;
                $povalue=0;
                $exfact_com_cond="";
                if($company_id>0) $exfact_com_cond=" and a.company_id=$company_id";
				if($company_id>0) $lc_sc_com_cond=" and a.beneficiary_name=$company_id";
				
				$lc_sc_sql="select b.wo_po_break_down_id, a.contract_no, 1 as type, a.lien_bank from com_sales_contract a, com_sales_contract_order_info b where a.id=b.com_sales_contract_id and a.status_active=1 and b.status_active=1 $lc_sc_com_cond
				union all
				select b.wo_po_break_down_id, a.export_lc_no as contract_no, 2 as type, a.lien_bank from com_export_lc a, com_export_lc_order_info b where a.id=b.com_export_lc_id and a.status_active=1 and b.status_active=1 $lc_sc_com_cond";
				//echo $lc_sc_sql;die;
				$lc_sc_result=sql_select($lc_sc_sql);
				$po_lc_sc_no=array();
				$lien_bank_arr=array();
				foreach($lc_sc_result as $row)  
                {
					if($duplicate_check[$row[csf("wo_po_break_down_id")]][$row[csf("contract_no")]]=="")
					{
						$duplicate_check[$row[csf("wo_po_break_down_id")]][$row[csf("contract_no")]]=$row[csf("contract_no")];
						if($po_lc_sc_no[$row[csf("wo_po_break_down_id")]]=="") $po_lc_sc_no[$row[csf("wo_po_break_down_id")]]=$row[csf("contract_no")].","; else $po_lc_sc_no[$row[csf("wo_po_break_down_id")]].=$row[csf("contract_no")];
					}

					if ($row[csf("type")]==2) 
					{
						$lien_bank_arr[$row[csf("wo_po_break_down_id")]]=$row[csf("lien_bank")];
					}
					else
					{
						$lien_bank_arr[$row[csf("wo_po_break_down_id")]]=$row[csf("lien_bank")];
					}

				}
				/*echo "<pre>";
				print_r($lien_bank_arr);
				echo "</pre>";*/
                
                $ex_fact_data=return_library_array("select b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
                //echo "<pre>";print_r($ex_fact_data);die;
                $com_cond="";
                if($company_id>0) $com_cond=" and a.company_name=$company_id";
                $order_sql="SELECT a.company_name, a.buyer_name, a.team_leader, a.dealing_marchant, a.style_ref_no, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, sum(b.order_quantity) as order_quantity, sum(b.order_total) as order_total, c.pub_shipment_date, c.unit_price, a.style_owner as working_company
                from wo_po_details_master a, wo_po_color_size_breakdown b, wo_po_break_down c 
                where a.job_no=c.job_no_mst and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.shiping_status<>3 and c.is_deleted=0 $com_cond 
                group by a.company_name, a.buyer_name, a.team_leader, a.dealing_marchant, a.style_ref_no, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, c.pub_shipment_date, c.unit_price, a.style_owner";
				//echo $order_sql;die;
				
                $order_sql_result=sql_select($order_sql);
                $bank_ids=array();
                foreach($order_sql_result as $row)  
                {
                    $pending_ord_qnty=0;
                    $Ship_value=0;
                    $po_quantity_value=0;
                    $pending_ord_value=0;
                    $ord_rate = 0;
                    if($row[csf("order_total")] > 0 && $row[csf("order_quantity")] > 0)
                    {
                        $ord_rate =$row[csf("order_total")]/$row[csf("order_quantity")];
                    }
                    $po_quantity_value =$row[csf("order_total")];
                    $po_quantity =$row[csf("order_quantity")];
                    $Ship_qnty =$ex_fact_data[$row[csf("po_break_down_id")]];
                    $Ship_value =$Ship_qnty*$ord_rate;
                    $pendin_qnty =$row[csf("order_quantity")]-$ex_fact_data[$row[csf("po_break_down_id")]];
                    $pending_ord_value =$pendin_qnty*$ord_rate;
					$in_hand_qnty=$row[csf("order_quantity")]-$Ship_qnty;
					if($lien_bank_arr[$row[csf('po_break_down_id')]] !=""){
						$bank_ids[$lien_bank_arr[$row[csf('po_break_down_id')]]] = $lien_bank_arr[$row[csf('po_break_down_id')]];
					}
					
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if($row[csf('shiping_status')]>0) $shiping_status=$row[csf('shiping_status')]; else $shiping_status=1;
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center" width="30"><? echo $i; ?></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="110"><p><? echo $company_arr[$row[csf('company_name')]]; ?>&nbsp;</p></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="110"><p><? echo $company_arr[$row[csf('working_company')]]; ?>&nbsp;</p></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="100"><p><? echo $team_lead_arr[$row[csf('team_leader')]]; ?>&nbsp;</p></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="100"><p><? echo $dealing_mer_arr[$row[csf('dealing_marchant')]]; ?>&nbsp;</p></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="70"><p><? echo $row[csf('job_no_mst')]; ?>&nbsp;</p></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="110"><p><? echo $row[csf('style_ref_no')];  ?>&nbsp;</p></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="110"><p><? echo $row[csf('po_number')];  ?>&nbsp;</p></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="100"><? echo chop($po_lc_sc_no[$row[csf('po_break_down_id')]],","); ?></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="100"><? echo $bank_arr[$lien_bank_arr[$row[csf('po_break_down_id')]]]; ?></td>
                        <td width="70" align="center"><p><? if($row[csf('pub_shipment_date')] !="" && $row[csf('pub_shipment_date')] !="0000-00-00") echo date("d-M-Y", strtotime($row[csf('pub_shipment_date')])); ?></p></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="60" align="center"><? echo "Pcs"; ?></td>
                        <td style="word-wrap:break-word; word-break: break-all;" align="right" width="130"><? echo number_format($po_quantity,0); ?></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="70" align="right"><? echo number_format($row[csf('unit_price')],2);  ?></td>
                        <td style="word-wrap:break-word; word-break: break-all;" align="right" width="140"><? echo number_format($po_quantity_value,2); ?></td>
                        <td style="word-wrap:break-word; word-break: break-all;" align="right" width="130"><? echo number_format($Ship_qnty,0);  ?></td>
                        <td style="word-wrap:break-word; word-break: break-all;" align="right" width="140"><? echo number_format($Ship_value,2); ?></td>
                        <td style="word-wrap:break-word; word-break: break-all;" align="right" width="130"><? echo number_format($in_hand_qnty,0);  ?></td>
                        <td style="word-wrap:break-word; word-break: break-all;" align="right" width="140"><? echo number_format($pending_ord_value,2);  ?></td>
                        <td style="word-wrap:break-word; word-break: break-all;" align="center" title="<? echo $shiping_status; ?>"><? echo  $shipment_status[$shiping_status]; ?></td>
                    </tr>
                    <?
					$html.='<tr bgcolor="'. $bgcolor.'" onClick="change_color(\'tr_'. $i.'\',\''.$bgcolor.'\')" id="tr_'.$i.'">
                        <td align="center" width="30" style="word-break:break-all">'.$i.'</td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="110"><p>'.$company_arr[$row[csf('company_name')]].'&nbsp;</p></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="110"><p>'.$company_arr[$row[csf('working_company')]].'&nbsp;</p></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="100"><p>'.$buyer_arr[$row[csf('buyer_name')]].'&nbsp;</p></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="100"><p>'.$team_lead_arr[$row[csf('team_leader')]].'&nbsp;</p></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="100"><p>'.$dealing_mer_arr[$row[csf('dealing_marchant')]].'&nbsp;</p></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="70"><p>'.$row[csf('job_no_mst')].'&nbsp;</p></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="110"><p>'.$row[csf('style_ref_no')].'&nbsp;</p></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="110"><p>'.$row[csf('po_number')].'&nbsp;</p></td>
						<td width="100" style="word-wrap:break-word; word-break: break-all;">'.chop($po_lc_sc_no[$row[csf('po_break_down_id')]],",").'</td>
						<td width="100" style="word-wrap:break-word; word-break: break-all;">'.$bank_arr[$lien_bank_arr[$row[csf('po_break_down_id')]]].'</td>
                        <td width="70" align="center"><p>'.date("d-M-Y", strtotime($row[csf('pub_shipment_date')])).'</p></td>
                        <td style="word-wrap:break-word; word-break: break-all;" width="60" align="center">Pcs</td>
                        <td style="word-wrap:break-word; word-break: break-all;" align="right" width="130">'.number_format($po_quantity,0).'</td>
						<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right">'.number_format($row[csf('unit_price')],2).'</td>
                        <td style="word-wrap:break-word; word-break: break-all;" align="right" width="140">'.number_format($po_quantity_value,2).'</td>
                        <td style="word-wrap:break-word; word-break: break-all;" align="right" width="130">'.number_format($Ship_qnty,0).'</td>
                        <td style="word-wrap:break-word; word-break: break-all;" align="right" width="140">'.number_format($Ship_value,2).'</td>
                        <td style="word-wrap:break-word; word-break: break-all;" align="right" width="130">'.number_format($in_hand_qnty,0).'</td>
                        <td style="word-wrap:break-word; word-break: break-all;" align="right" width="140">'.number_format($pending_ord_value,2).'</td>
                        <td style="word-wrap:break-word; word-break: break-all;" align="center" title="'.$shiping_status.'">'.$shipment_status[$shiping_status].'</td>
                    </tr>';
                    $i++;
					$tot_po_quantity+=$po_quantity;
					$povalue+=$po_quantity_value; 
					$tot_Ship_qnty+=$Ship_qnty;
					$shipValue+=$Ship_value;
					$total_in_hand_qnty+=$in_hand_qnty; 
					$InHandValue+=$pending_ord_value;
                }
                ?>
            </tbody>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2130" cellpadding="0" cellspacing="0" id="report_table_footer">
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="13" align="right">Total : </td>
                    <td width="130" align="right" id="tot_po_quantity"><? echo number_format($tot_po_quantity,0); ?></td>
                    <td width="70">&nbsp;</td>
                    <td width="140" align="right" id="value_povalue"><? echo number_format($povalue,2); ?></td>
                    <td width="130" align="right" id="tot_Ship_qnty"><? echo number_format($tot_Ship_qnty,0); ?></td>
                    <td align="right" id="value_shipvalue" width="140"><? echo number_format($shipValue,2); ?></td>
                    <td width="130" align="right" id="total_in_hand_qnty"><? echo number_format($total_in_hand_qnty,0); ?></td>
                    <td align="right" id="value_inhandvalue" width="140"><? echo number_format($InHandValue,2); ?></td>
                    <td width="58"></td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
	$html.='</tbody>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2130" cellpadding="0" cellspacing="0" id="report_table_footer">
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="13" align="right">&nbsp;&nbsp;Total : </td>
                    <td width="130" align="right" id="tot_po_quantity">'.number_format($tot_po_quantity,0).'</td>
					<td width="70">&nbsp;</td>
                    <td width="140" align="right" id="value_povalue" width="140">'.number_format($povalue,2).'</td>
                    <td width="130" align="right" id="tot_Ship_qnty">'.number_format($tot_Ship_qnty,0).'</td>
                    <td align="right" id="value_shipvalue" width="140">'.number_format($shipValue,2).'</td>
                    <td width="130" align="right" id="total_in_hand_qnty">'.number_format($total_in_hand_qnty,0).'</td>
                    <td align="right" id="value_inhandvalue" width="140">'.number_format($InHandValue,2).'</td>
                    <td width="58"></td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>';
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
		//if( @filemtime($filename) < (time()-$seconds_old) )
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	ob_end_clean();
	//echo "$total_data****$filename";
	$bank_ids = implode(",",$bank_ids);
	
	
	?>
    <p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	&nbsp; <a href="<? echo $filename; ?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_2&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Excel Preview 2" class="formbutton" style="width:110px"/></a>&nbsp;
	&nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_3&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Excel Preview 3" class="formbutton" style="width:110px"/></a>&nbsp;
	&nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_4&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Excel Preview 4" class="formbutton" style="width:110px"/></a>
	&nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_5&company_id='<? echo $company_id; ?>'&bank_ids='<? echo $bank_ids;?>'" style="text-decoration:none"><input type="button" value="Excel Preview 5" class="formbutton" style="width:110px"/></a>&nbsp;
	</p>
    <?

	echo $html; 

	?>
    <script>
	var tableFilters = 
	{
		col_60: "none",
		col_operation: {
			id: ["tot_po_quantity","value_povalue","tot_Ship_qnty","value_shipvalue","total_in_hand_qnty","value_inhandvalue"],
			col: [13,15,16,17,18,19],
			operation: ["sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	setFilterGrid("table_body",-1,tableFilters);
    </script>
    <?
	exit();
}

if ($action=="print_preview_2") // Created by Tipu
{
	//echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$company_arr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$dealing_mer_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");

	ob_start();

	$com_cond="";
    if($company_id>0) $com_cond=" and a.company_name=$company_id";
    ?>
    <div id="report_container" align="center" style="width:1140px">
		<fieldset style="width:1140px;">
		    <table class="rpt_table" border="1" rules="all" width="1140" cellpadding="0" cellspacing="0">
		        <thead>
		            <th width="40">SL</th>
		            <th width="110">Company Name</th> 
		            <th width="100">Working Company</th> 
		            <th width="100">Buyer Name</th> 
		            <th width="100"> Dealing Merchant</th>
		            <th width="90">UOM</th>
		            <th width="130">PO Qty.</th>
		            <th width="130">In Hand Qty.</th>
		            <th width="140">In Hand Value ($)</th>
		            <th width="100">Shipment Date</th>
		            <th>Ship Status</th>
		        </thead>
		    </table>
		    <table class="rpt_table" border="1" rules="all" width="1140" cellpadding="0" cellspacing="0" id="table_body">
	            <tbody>
	            	<?

	            	$ex_fact_data=return_library_array("SELECT b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
                	//echo "<pre>";print_r($ex_fact_data);die;

					$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, sum(b.order_quantity) as po_quantity, sum(b.order_total) as order_total, c.pub_shipment_date
					FROM wo_po_details_master a, wo_po_color_size_breakdown b, wo_po_break_down c 
					WHERE a.job_no=c.job_no_mst and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.shiping_status<>3 and c.is_deleted=0 $com_cond 
					GROUP by a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, c.pub_shipment_date 
					ORDER by a.dealing_marchant asc";
					// echo $main_sql;die;
					
	                $main_sql_result=sql_select($main_sql);	 
	               
	                $i=1;
	                $merchant_wise_total_po_quantity=$merchant_wise_InHandValue=0;
                	foreach ($main_sql_result as $dealing_marchant_key => $row) 
                	{
                		$in_hand_value=0;
						$ord_rate = 0;

						if($row[csf("order_total")] > 0 && $row[csf("po_quantity")] > 0)
						{
							$ord_rate =$row[csf("order_total")]/$row[csf("po_quantity")];
						}
						$po_quantity =$row[csf("po_quantity")];
						$pendin_qnty =$row[csf("po_quantity")]-$ex_fact_data[$row[csf("po_break_down_id")]];
						$in_hand_value =$pendin_qnty*$ord_rate;
		                $Ship_qnty =$ex_fact_data[$row[csf("po_break_down_id")]];
						$in_hand_qnty=$row[csf("po_quantity")]-$Ship_qnty;
						if($row[csf('shiping_status')]>0) $shiping_status=$row[csf('shiping_status')]; else $shiping_status=1;
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						if(!in_array($row[csf('dealing_marchant')], $chk))
	                   	{	                   		
	                   		if ($i!=1) 
	                   		{
			                    ?>
			                   	<tr>
			                        <td colspan="6" align="right"><strong>Merchant Total :</strong></td>
			                        <td width="130"><strong><? echo $merchant_wise_total_po_quantity; ?></strong></td>
			                        <td width="130"><strong><? echo $merchant_wise_total_in_hand_qnty; ?></strong></td>
			                        <td width="140"><strong><? echo $merchant_wise_InHandValue; ?></strong></td>
			                        <td width="100"></td>
			                        <td></td>
			                    </tr>
			                   <?			                   	                   
		               		}
		               		unset($merchant_wise_total_po_quantity);
		               		unset($merchant_wise_total_in_hand_qnty);
		               		unset($merchant_wise_InHandValue);
                   		}
			            $chk[]=$row[csf('dealing_marchant')];

						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td width="40"><? echo $i; ?></td>
	                        <td width="110"><? echo $company_arr[$row[csf('company_name')]]; ?></td>
	                        <td width="100"><? echo $company_arr[$row[csf('style_owner')]]; ?></td>
	                        <td width="100"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
	                        <td width="100"><? echo $dealing_mer_arr[$row[csf('dealing_marchant')]]; ?></td>
	                        <td width="90"><? echo "Pcs"; ?></td>
	                        <td width="130"><? echo $po_quantity; ?></td>
	                        <td width="130"><? echo $in_hand_qnty; ?></td>
	                        <td width="140"><? echo $in_hand_value; ?></td>
	                        <td width="100" align="center"><p><? if($row[csf('pub_shipment_date')] !="" && $row[csf('pub_shipment_date')] !="0000-00-00") echo date("d-M-Y", strtotime($row[csf('pub_shipment_date')])); ?></p></td>
	                        <td><? echo $shipment_status[$shiping_status]; ?></td>
	                    </tr>
	                    <?
	                    $i++;
	                    $merchant_wise_total_po_quantity+=$po_quantity;
	                    $merchant_wise_total_in_hand_qnty+=$in_hand_qnty;
	                    $merchant_wise_InHandValue+=$in_hand_value;
	                    $grand_total_po_quantity+=$po_quantity;
	                    $grand_total_in_hand_qnty+=$in_hand_qnty;
						$grand_total_InHandValue+=$in_hand_value;
                	}

	                    ?>
	                   	<tr>
	                        <td colspan="6" align="right"><strong>Merchant Total :</strong></td>
	                        <td width="130"><strong><? echo $merchant_wise_total_po_quantity;?></strong></td>
	                        <td width="130"><strong><? echo $merchant_wise_total_in_hand_qnty;?></strong></td>
	                        <td width="140"><strong><? echo $merchant_wise_InHandValue; ?></strong></td>
	                        <td width="100"></td>
	                        <td></td>
	                    </tr>
	            </tbody>
        	</table>

        	<table class="rpt_table" border="1" rules="all" width="1140" cellpadding="0" cellspacing="0" id="report_table_footer">
	        	<tfoot>
	                <tr>
	                    <td width="40"></td>
	                    <td width="110"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="90" align="right"><strong>Grand Total:</strong></td>
	                    <td width="130"><strong><? echo $grand_total_po_quantity;?></strong></td>
	                    <td width="130"><strong><? echo $grand_total_in_hand_qnty;?></strong></td>
	                    <td width="140"><strong><? echo $grand_total_InHandValue; ?></strong></td>
	                    <td width="100"></td>
	                    <td></td>
	                </tr>
	            </tfoot>
        	</table>
		</fieldset>
	</div>
    <?
    foreach (glob("$user_id*.xls") as $filename2) 
	{
		@unlink($filename2);
		//if( @filemtime($filename2) < (time()-$seconds_old) )
	}
	//---------end------------//
	$name=time();
	$filename2=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename2, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename2=$user_id."_".$name.".xls";
	//ob_end_clean();
	//echo "$total_data****$filename2";

	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($filename2).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename2));
    flush(); // Flush system output buffer
    readfile($filename2);
    exit;
}

if ($action=="print_preview_3") // Created by Tipu
{
	//echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$company_arr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$dealing_mer_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");

	ob_start();

	$com_cond="";
    if($company_id>0) $com_cond=" and a.company_name=$company_id";
    ?>
    <div id="report_container" align="center" style="width:1240px">
		<fieldset style="width:1240px;">
		    <table class="rpt_table" border="1" rules="all" width="1240" cellpadding="0" cellspacing="0">
		        <thead>
		            <th width="40">SL</th>
		            <th width="100">Company Name</th> 
		            <th width="100">Working Company</th> 
		            <th width="100">Buyer Name</th> 
		            <th width="100"> Dealing Merchant</th>
		            <th width="130">PO Qty.</th>
		            <th width="130">In Hand Qty.</th>
		            <th width="140">In Hand Value ($)</th>
		            <th width="130">Total</th>
		            <th>Total Amount</th>
		        </thead>
		    </table>
		    <table class="rpt_table" border="1" rules="all" width="1240" cellpadding="0" cellspacing="0" id="table_body">
	            <tbody>
	            	<?

	            	$ex_fact_data=return_library_array("SELECT b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
                	//echo "<pre>";print_r($ex_fact_data);die;

					$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, sum(b.order_quantity) as po_quantity, sum(b.order_total) as order_total, c.pub_shipment_date
					FROM wo_po_details_master a, wo_po_color_size_breakdown b, wo_po_break_down c 
					WHERE a.job_no=c.job_no_mst and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.shiping_status<>3 and c.is_deleted=0 $com_cond 
					GROUP by a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, c.pub_shipment_date 
					ORDER by a.buyer_name asc";
					 //echo $main_sql;die;
					
	                $main_sql_result=sql_select($main_sql);	 
	               	foreach ($main_sql_result as $key => $row) 
	               	{
	               		$in_hand_value=0;
						$ord_rate = 0;

						if($row[csf("order_total")] > 0 && $row[csf("po_quantity")] > 0)
						{
							$ord_rate =$row[csf("order_total")]/$row[csf("po_quantity")];
						}
						$po_quantity =$row[csf("po_quantity")];
						$pendin_qnty =$row[csf("po_quantity")]-$ex_fact_data[$row[csf("po_break_down_id")]];
						$in_hand_value =$pendin_qnty*$ord_rate;
		                $Ship_qnty =$ex_fact_data[$row[csf("po_break_down_id")]];
						$in_hand_qnty=$row[csf("po_quantity")]-$Ship_qnty;

	               		$buyer_rowspan[$row[csf('buyer_name')]]++;
	               		$in_hand_qnty_arr[$row[csf('buyer_name')]]+=$in_hand_qnty;
	               		$in_hand_value_arr[$row[csf('buyer_name')]]+=$in_hand_value;
	               	}
	               	/*echo "<pre>";
	               	print_r($buyer_data_arr);die;*/
	                $i=1;

                	foreach ($main_sql_result as $dealing_marchant_key => $row) 
                	{
                		$in_hand_value=0;
						$ord_rate = 0;

						if($row[csf("order_total")] > 0 && $row[csf("po_quantity")] > 0)
						{
							$ord_rate =$row[csf("order_total")]/$row[csf("po_quantity")];
						}
						$po_quantity =$row[csf("po_quantity")];
						$pendin_qnty =$row[csf("po_quantity")]-$ex_fact_data[$row[csf("po_break_down_id")]];
						$in_hand_value =$pendin_qnty*$ord_rate;
		                $Ship_qnty =$ex_fact_data[$row[csf("po_break_down_id")]];
						$in_hand_qnty=$row[csf("po_quantity")]-$Ship_qnty;
						if($row[csf('shiping_status')]>0) $shiping_status=$row[csf('shiping_status')]; else $shiping_status=1;
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						 
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td width="40"><? echo $i; ?></td>
	                        <td width="100"><? echo $company_arr[$row[csf('company_name')]]; ?></td>
	                        <td width="100"><? echo $company_arr[$row[csf('style_owner')]]; ?></td>
	                        <td width="100"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
	                        <td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $dealing_mer_arr[$row[csf('dealing_marchant')]]; ?></td>
	                        <td width="130"><? echo $po_quantity; ?></td>
	                        <td width="130"><? echo $in_hand_qnty; ?></td>
	                        <td width="140"><? echo $in_hand_value; ?></td>

	                        <? 
	                        if(!in_array($row[csf('buyer_name')], $chk))
		                   	{
			                    ?>
			                        <td width="130" rowspan="<? echo $buyer_rowspan[$row[csf('buyer_name')]]; ?>"><? echo $in_hand_qnty_arr[$row[csf('buyer_name')]]; $total_in_hand_qnty+=$in_hand_qnty_arr[$row[csf('buyer_name')]]; ?></td>
			                        <td rowspan="<? echo $buyer_rowspan[$row[csf('buyer_name')]]; ?>"><? echo $in_hand_value_arr[$row[csf('buyer_name')]]; $total_in_hand_value+=$in_hand_value_arr[$row[csf('buyer_name')]]; ?></td>
			                    <?
	                   		}
				            $chk[]=$row[csf('buyer_name')]; 
				            ?>

	                    </tr>
	                    <?
	                    $i++;
	                    $grand_total_po_quantity+=$po_quantity;
	                    $grand_total_in_hand_qnty+=$in_hand_qnty;
						$grand_total_InHandValue+=$in_hand_value;
                	}

	                    ?>
	            </tbody>
        	</table>

        	<table class="rpt_table" border="1" rules="all" width="1240" cellpadding="0" cellspacing="0" id="report_table_footer">
	        	<tfoot>
	                <tr>
	                    <td width="40"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="100" align="right"><strong>Grand Total:</strong></td>
	                    <td width="130"><strong><? echo $grand_total_po_quantity;?></strong></td>
	                    <td width="130"><strong><? echo $grand_total_in_hand_qnty;?></strong></td>
	                    <td width="140"><strong><? echo $grand_total_InHandValue; ?></strong></td>
	                    <td width="130"><strong><? echo $total_in_hand_qnty; ?></strong></td>
	                    <td><strong><? echo $total_in_hand_value; ?></strong></td>
	                </tr>
	            </tfoot>
        	</table>
		</fieldset>
	</div>
    <?
    foreach (glob("$user_id*.xls") as $filename2) 
	{
		@unlink($filename2);
		//if( @filemtime($filename2) < (time()-$seconds_old) )
	}
	//---------end------------//
	$name=time();
	$filename2=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename2, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename2=$user_id."_".$name.".xls";
	//ob_end_clean();
	//echo "$total_data****$filename2";

	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($filename2).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename2));
    flush(); // Flush system output buffer
    readfile($filename2);
    exit;
}

if ($action=="print_preview_4") // Created by Tipu
{
	//echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$company_arr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$dealing_mer_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");

	ob_start();

	$com_cond="";
    if($company_id>0) $com_cond=" and a.company_name=$company_id";
	$ex_fact_data=return_library_array("SELECT b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
	//echo "<pre>";print_r($ex_fact_data);die;
	
	if ($db_type==0) $month_year_cond="date_format(c.pub_shipment_date, '%M-%y') as month_year";
	else $month_year_cond="to_char(c.pub_shipment_date, 'Month-YY') as month_year";

	$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, sum(b.order_quantity) as po_quantity, sum(b.order_total) as order_total, $month_year_cond
	FROM wo_po_details_master a, wo_po_color_size_breakdown b, wo_po_break_down c 
	WHERE a.job_no=c.job_no_mst and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.shiping_status<>3 and c.is_deleted=0 $com_cond 
	GROUP by a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, c.pub_shipment_date 
	ORDER by c.pub_shipment_date asc";
	 // and to_char(a.insert_date,'YYYY') = 2019
	
	//echo $main_sql;die;

	$main_sql_result=sql_select($main_sql);
	$main_arr=array();
	$month_year_qty_arr=array();
	foreach ($main_sql_result as $key => $row) 
   	{
   		$in_hand_value=0;
		$ord_rate = 0;

		if($row[csf("order_total")] > 0 && $row[csf("po_quantity")] > 0)
		{
			$ord_rate =$row[csf("order_total")]/$row[csf("po_quantity")];
		}
		$po_quantity =$row[csf("po_quantity")];
		$pendin_qnty =$row[csf("po_quantity")]-$ex_fact_data[$row[csf("po_break_down_id")]];
		$in_hand_value =$pendin_qnty*$ord_rate;
        $Ship_qnty =$ex_fact_data[$row[csf("po_break_down_id")]];
		$in_hand_qnty=$row[csf("po_quantity")]-$Ship_qnty;

   		$buyer_rowspan[$row[csf('buyer_name')]]++;
   		$in_hand_qnty_arr[$row[csf('buyer_name')]]+=$in_hand_qnty;
   		$in_hand_value_arr[$row[csf('buyer_name')]]+=$in_hand_value;

   		$main_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]]=$row[csf('month_year')];
   		$lc_com_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]]['company_name']=$row[csf('company_name')];
   		$w_com_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]]['style_owner']=$row[csf('style_owner')];
   		

   		$month_year_qty_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]][$row[csf('month_year')]]['po_quantity']+=$row[csf("po_quantity")];
   		$month_year_qty_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]][$row[csf('month_year')]]['inhand_qty']+=$in_hand_qnty;
   		$month_year_qty_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]][$row[csf('month_year')]]['in_hand_value']+=$in_hand_value;
   		/*$month_year_qty_arr[$row[csf('month_year')]]['po_quantity']+=$row[csf("po_quantity")];
   		$month_year_qty_arr[$row[csf('month_year')]]['inhand_qty']+=$in_hand_qnty;
   		$month_year_qty_arr[$row[csf('month_year')]]['in_hand_value']+=$in_hand_value;*/

   		$month_year_arr[$row[csf('month_year')]]=$row[csf('month_year')];
   	}   	
	 	$divWith=count($month_year_arr);
	 	$table_width=1000+($divWith*300);
       	/*echo "<pre>";
       	print_r($month_year_arr);die;*/
       //echo '<pre>';print_r($main_arr);die;
    ?>
    <div id="report_container" align="center" style="width:1010px">
		<fieldset style="width:<? echo $table_width; ?>px;">
		    <table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0">
		        <thead>
		        	<tr>
			            <th width="40" rowspan="2">SL</th>
			            <th width="100" rowspan="2">Company Name</th> 
			            <th width="100" rowspan="2">Working Company</th> 
			            <th width="100" rowspan="2">Buyer Name</th> 
			            <th width="100" rowspan="2">Merchandiser</th>
			            <?
	                    foreach($month_year_arr as $month_year => $row)
	                    {	                    	
                    		?>
	                    	<th width="300" colspan="3"><? echo $month_year; ?></th>
	                    	<?                    	
	                    }
	                    ?>
			            <th width="130" rowspan="2">Total Inhand Qty</th>
			            <th width="130" rowspan="2">Total Inhand Value</th>
		            </tr>
		            <tr> 
		            	<?
	                    foreach($month_year_arr as $month_year => $row)
	                    {	                    	
                    	?>          	
			            <th width="100">PO Qty</th>
			            <th width="100">In hand Qty</th>
			            <th width="100">In hand value</th>
			            <?                    	
	                    }
	                    ?>
		            </tr>
		        </thead>
		    </table>
		    <table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	            <tbody>
	            	<?
	                $i=1;
	                $grandTotal_in_hand_qnty=$grandTotal_in_hand_value=0;
                	foreach ($main_arr as $buyer => $buyer_data)
                	{  // $com_id = [$buyer][$buyer_data]['company_name'];
                        //echo $com_id.'system';
                		foreach ($buyer_data as $marcent => $row) 
                		{                			
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$lc_company_name=$lc_com_arr[$buyer][$marcent]['company_name'];
							$w_company_name=$w_com_arr[$buyer][$marcent]['style_owner'];
							?>
		                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		                        <td width="40"><? echo $i; ?></td>
		                        <td width="100"><? echo $company_arr[$lc_company_name]; ?></td>
		                        <td width="100"><? echo $company_arr[$w_company_name]; ?></td>
		                        <td width="100"><? echo $buyer_arr[$buyer]; ?></td>
		                        <td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $dealing_mer_arr[$marcent]; ?></td>
		                        <?
		                        $total_po_quantity=$total_inhand_qty=$total_in_hand_value=0;
			                    foreach($month_year_arr as $month_year => $row)
			                    {	 
			                    	$po_quantity=$month_year_qty_arr[$buyer][$marcent][$month_year]['po_quantity'];
			                    	$inhand_qty=$month_year_qty_arr[$buyer][$marcent][$month_year]['inhand_qty'];
			                    	$in_hand_value=$month_year_qty_arr[$buyer][$marcent][$month_year]['in_hand_value'];

			                    	$grand_Total_po_quantity[$month_year]+=$po_quantity;                   	
			                    	$grand_Total_inhand_qty[$month_year]+=$inhand_qty;                   	
			                    	$grand_Total_in_hand_value[$month_year]+=$in_hand_value;                   	
			                    	?> 
			                        <td width="100"><? echo number_format($po_quantity,2); $total_po_quantity+=$po_quantity; ?></td>
			                        <td width="100"><? echo number_format($inhand_qty,2); $total_inhand_qty+=$inhand_qty; ?></td>
			                        <td width="100"><? echo number_format($in_hand_value,2); $total_in_hand_value+=$in_hand_value; ?></td>
				                    <?                    	
			                    }
			                    ?>
								<td width="130"><? echo $total_inhand_qty;  ?></td>
		                        <td width="130"><? echo $total_in_hand_value; ?></td>

		                    </tr>
		                    <?
		                    $i++;
		                    $grandTotal_in_hand_qnty+=$total_inhand_qty;
		                    $grandTotal_in_hand_value+=$total_in_hand_value;
                		}
                	}
	                ?>
	            </tbody>
        	</table>

        	<table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
	        	<tfoot>
	                <tr>
	                    <td width="40"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="100" align="right"><strong>Grand Total:</strong></td>
	                    <?
	                    foreach($month_year_arr as $month_year => $row)
	                    {	                    	
                    		?>
	                    <td width="100"><strong><? echo number_format($grand_Total_po_quantity[$month_year],2); ?></strong></td>
	                    <td width="100"><strong><? echo number_format($grand_Total_inhand_qty[$month_year],2); ?></strong></td>
	                    <td width="100"><strong><? echo number_format($grand_Total_in_hand_value[$month_year],2); ?></strong></td>
	                    <?
		                }
		                ?>
	                    <td width="130"><strong><? echo $grandTotal_in_hand_qnty; ?></strong></td>
	                    <td width="130"><strong><? echo $grandTotal_in_hand_value; ?></strong></td>
	                </tr>
	            </tfoot>
        	</table>
		</fieldset>
	</div>
    <?
    foreach (glob("$user_id*.xls") as $filename2) 
	{
		@unlink($filename2);
		//if( @filemtime($filename2) < (time()-$seconds_old) )
	}
	//---------end------------//
	$name=time();
	$filename2=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename2, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename2=$user_id."_".$name.".xls";
	//ob_end_clean();
	//echo "$total_data****$filename2";

	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($filename2).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename2));
    flush(); // Flush system output buffer
    readfile($filename2);
    exit;
}
if ($action=="print_preview_5") // Created by Shafiq-Sumon
{
	//echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$company_arr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$buyer_bank_arr = return_library_array("select id,bank_id from lib_buyer where status_active=1 and is_deleted=0","id","bank_id");
	$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	$location_array = return_library_array("select id, location_name from lib_location where status_active=1 ","id","location_name");
	$dealing_mer_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
	//print_r($location_array);die;
	

	$com_cond="";
    if($company_id>0) $com_cond=" and a.company_name=$company_id";
	$ex_fact_data=return_library_array("select b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
	//echo "<pre>";print_r($ex_fact_data);die;
	
	if ($db_type==0) $month_year_cond="date_format(c.pub_shipment_date, '%M-%y') as month_year";
	else $month_year_cond="to_char(c.pub_shipment_date, 'Month-YY') as month_year";

	$main_sql="select a.company_name, a.style_owner, a.buyer_name, a.location_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, sum(b.order_quantity) as po_quantity, sum(b.order_total) as order_total, $month_year_cond
	from wo_po_details_master a, wo_po_color_size_breakdown b, wo_po_break_down c
	where a.job_no=c.job_no_mst and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and c.is_deleted=0 $com_cond 
	group by a.company_name, a.location_name, a.style_owner, a.buyer_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, c.pub_shipment_date 
	order by c.pub_shipment_date asc";
	 // and to_char(a.insert_date,'YYYY') = 2019
	
	//echo $main_sql;//die;
	//$bank_array = str_replace("'","",explode(",",$bank_ids));

	$main_sql_result=sql_select($main_sql);

	$i=0;
	foreach ($main_sql_result as $value) 
	{
		if($buyer_bank_arr[$value[csf('buyer_name')]])
		{
			$inhand_data_array[$buyer_bank_arr[$value[csf('buyer_name')]]][$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["po_quantity"]+=$value[csf('po_quantity')];
			$inhand_data_array[$buyer_bank_arr[$value[csf('buyer_name')]]][$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["order_total"]+=$value[csf('order_total')];

			//$dtls_data[$buyer_bank_arr[$value[csf('buyer_name')]]][$value[csf('company_name')]][$value[csf('location_name')]]=$value[csf('location_name')];
			$dtls_data[$buyer_bank_arr[$value[csf('buyer_name')]]][$value[csf('company_name')]][$value[csf('style_owner')]]=$value[csf('style_owner')];
			//$summery_data[$value[csf('company_name')]][$value[csf('location_name')]][$value[csf('month_year')]]["tot_qnty"]+=$value[csf('po_quantity')];
			$summery_data[$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_qnty"]+=$value[csf('po_quantity')];
			//$summery_data[$value[csf('company_name')]][$value[csf('location_name')]][$value[csf('month_year')]]["tot_amt"]+=$value[csf('order_total')];
			$summery_data[$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_amt"]+=$value[csf('order_total')];

			//$bank_wise_summery_data[$buyer_bank_arr[$value[csf('buyer_name')]]][$value[csf('location_name')]][$value[csf('month_year')]]["tot_qnty"]+=$value[csf('po_quantity')];
			$bank_wise_summery_data[$buyer_bank_arr[$value[csf('buyer_name')]]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_qnty"]+=$value[csf('po_quantity')];
			//$bank_wise_summery_data[$buyer_bank_arr[$value[csf('buyer_name')]]][$value[csf('location_name')]][$value[csf('month_year')]]["tot_amt"]+=$value[csf('order_total')];
			$bank_wise_summery_data[$buyer_bank_arr[$value[csf('buyer_name')]]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_amt"]+=$value[csf('order_total')];
			$all_month[$value[csf('month_year')]]=$value[csf('month_year')];
		}
		$i++;
	}

	//echo "<pre>";print_r($inhand_data_array);die;
	foreach($dtls_data as $bank_id=>$bank_val)
	{
		foreach($bank_val as $com_id=>$com_val)
		{
			foreach($com_val as $location_id=>$loc_val)
			{
				$bank_colspan[$bank_id]++;
				$com_colspan[$bank_id][$com_id]++;
				$dtls_colspan++;
			}
		}
	}
	$tot_colspan=0;
	foreach($summery_data as $com_id=>$com_val)
	{
		foreach($com_val as $location_id=>$loc_val)
		{
			$tot_colspan++;
			$tot_com_colspan[$com_id]++;
			
		}
	}
	$dtls_tot_colspan=$dtls_colspan+$tot_colspan;

	//print_r ( $grand_total_qnty);//die;
	//echo "<pre>";print_r($grand_total_qnty);//die;
	//echo $count_col."=".$com_col."=".$bank_col;die;
	$table_width=(150+(100*($dtls_tot_colspan*2)));
	$div_width=$table_width+20;

	ob_start();
    ?>
    <div id="report_container" align="center" style="width:<? echo $div_width; ?>px">
		<fieldset style="width:<? echo $table_width; ?>px;">
		    <table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0">
		        <thead>
	   				<tr>
	   					<th bgcolor="#d6fff1" colspan="<? echo ($dtls_tot_colspan*2)+1;?>">Inhand Report Bank Wise</th>
					</tr>
					<tr>	   					
	   					<th bgcolor="#b4f3bf">Bank</th>
						   <? 
							 foreach ($dtls_data as $bank_id => $bank_name) 
							 {
								?>
								<th bgcolor="#eaffc9" colspan="<? echo $bank_colspan[$bank_id]*2;?>" title="<? echo $bank_id ?>"><? echo $bank_arr[$bank_id];?></th>
								<?
								$bank_tot_colspan+=$bank_colspan[$bank_id]*2;
						 	  }  
						   ?>
					   	<!-- <th colspan="<? //echo $month_colspan;?>">Brack Bank</th> -->
					   	<th bgcolor="#ffe2c9" colspan="<? echo $tot_colspan*2;?>">Total</th>
					</tr>
					<tr>
						<th bgcolor="#b4f3bf" colspan="">Factories</th>
						<? 
						foreach($dtls_data as $bank_id=>$bank_val)
						{
							foreach($bank_val as $com_id=>$com_val)
							{									
								?>
								<th bgcolor="#dfefff" colspan="<? echo $com_colspan[$bank_id][$com_id]*2;?>"><? echo $company_arr[$com_id];?></th>
								<?
								$fac_tot_colspan+=$com_colspan[$bank_id][$com_id]*2;
							}
						}  
						
						foreach($summery_data as $com_id=>$com_val)
						{
							?>
							<th bgcolor="#b4f3bf" colspan="<? echo $tot_com_colspan[$com_id]*2;?>"><? echo $company_arr[$com_id];?></th>
							<?
						}
						?>						

					</tr>
					<tr>
						<th bgcolor="#bffffb">Location</th>
						<? 
						foreach($dtls_data as $bank_id=>$bank_val)
						{
							foreach($bank_val as $com_id=>$com_val)
							{
								foreach($com_val as $location_id=>$location_val)
								{
									?>
									<!-- <th bgcolor="#e8bfff" colspan="2"><? //echo $location_array[$location_id];?></th> -->
									<th bgcolor="#b4f3bf" colspan="2"><? echo $location_array[$location_id];?></th>
									<?
								}
								
							}
						}
						foreach($summery_data as $com_id=>$com_val)
						{
							foreach($com_val as $location_id=>$location_val)
							{
								?>
								<th bgcolor="#bffffb" colspan="2"><? echo $location_array[$location_id];?></th>
								<?
							}
						} 
						?>
					</tr>
					<tr>
						<th bgcolor="#b4f3bf">Month</th>
						<? 
						foreach ($dtls_data as $bank_name => $company_data) 
						{
							foreach ($company_data as $company => $location_data) 
							{ # code...
								foreach ($location_data as $location => $month_data) 
								{
									?>
									<th width="100">Inhand Qnty</th>
									<th width="100">Inhand Value</th>
									<?
								}
							}
						} 
						foreach($summery_data as $com_id=>$com_val)
						{
							foreach($com_val as $location_id=>$location_val)
							{
								?>
								<th>Inhand Qnty</th>
								<th>Inhand Value</th>
								<?
							}
						}
						?>
					</tr>
		        </thead>				
		    
	            <tbody>
					
	            	<?
	                $i=1;
	                //$grandTotal_in_hand_qnty=$grandTotal_in_hand_value=0;
                	foreach ($all_month as $month_id => $month_name) 
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        	<td bgcolor="#c9ffef"><p>&nbsp;<? echo $month_name;?></p></td>
                            <?
							foreach ($dtls_data as $bank_name => $company_data) 
							{
								foreach ($company_data as $company => $location_data) 
								{ # code...
									foreach ($location_data as $location => $month_data) 
									{
										?>
										<td align="right"><? echo $inhand_data_array[$bank_name][$company][$location][$month_id]["po_quantity"];?></td>
										<td align="right"><? echo number_format($inhand_data_array[$bank_name][$company][$location][$month_id]["order_total"],2);?></td>
										<?
										$grand_total_qnty[$bank_name][$company][$location]['tot_qnty'] += $inhand_data_array[$bank_name][$company][$location][$month_id]["po_quantity"];
										$grand_total_qnty[$bank_name][$company][$location]['tot_amt'] += $inhand_data_array[$bank_name][$company][$location][$month_id]["order_total"];
									}
								}
							} 
							foreach($summery_data as $com_id=>$com_val)
							{
								foreach($com_val as $location_id=>$location_val)
								{
									//$summery_data[$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_qnty"]+=$value[csf('po_quantity')];
									?>
									<td align="right"><? echo  $location_val[$month_id]["tot_qnty"]; 
									$grand_tot_summery_qnty_total +=$location_val[$month_id]["tot_qnty"];?></td>
									<td align="right"><? echo number_format($location_val[$month_id]["tot_amt"],2);
									$grand_tot_summery_value_total  += $location_val[$month_id]["tot_amt"] ;
									?></td>
									<?
									$toatl_qnty[$com_id][$location_id]["total_po_quantity"] += $location_val[$month_id]["tot_qnty"];
									$toatl_qnty[$com_id][$location_id]["total_po_value"] += $location_val[$month_id]["tot_amt"] ;
								}
							}
							?>
                        </tr>
                        <?
						$i++;
						//$tot_summery_qnty=0;$tot_summery_value=0;
						//$toatl_qnty=0;$toatl_value=0;
					}
	                ?>
					
	            </tbody>
				<tfoot>
					<tr bgcolor="#ffc1bf">
						<td align="right"><strong>Total:</strong></td>
					<?
						foreach ($dtls_data as $bank_name => $company_data) 
						{
							foreach ($company_data as $company => $location_data) 
							{ # code...
								foreach ($location_data as $location => $month_data) 
								{
									?>
									<td align="right"><strong><? echo $grand_total_qnty[$bank_name][$company][$location]['tot_qnty'];?></strong></td>
									<td align="right"><strong><? echo number_format($grand_total_qnty[$bank_name][$company][$location]['tot_amt'],2);?></strong></td>
									<?
									//$grand_total_qnty[$bank_name][$company][$location]['tot_qnty'] += $inhand_data_array[$bank_name][$company][$location][$month_id]["po_quantity"];
									//$grand_total_qnty[$bank_name][$company][$location]['tot_amt'] += $inhand_data_array[$bank_name][$company][$location][$month_id]["order_total"];
								}
							}
						}
						
						
						foreach($summery_data as $com_id=>$com_val)
						{
							foreach($com_val as $location_id=>$location_val)
							{
								?>
								<td align="right"><strong><? echo $toatl_qnty[$com_id][$location_id]["total_po_quantity"];?></strong></td>
								<td align="right"><strong><? echo number_format($toatl_qnty[$com_id][$location_id]["total_po_value"] ,2);?></strong></td>
								<?
							}
						}
						?>
					</tr>
				</tfoot>
        	</table>
			<? 
				//echo "<pre>";print_r($grand_total_qnty);
				//echo "<pre>";print_r($dtls_data);
				//die;
			?>
		</fieldset>
	</div>
    <?
    foreach (glob("$user_id*.xls") as $filename2) 
	{
		@unlink($filename2);
		//if( @filemtime($filename2) < (time()-$seconds_old) )
	}
	//---------end------------//
	$name=time();
	$filename2=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename2, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename2=$user_id."_".$name.".xls";
	//ob_end_clean();
	//echo "$total_data****$filename2";

	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($filename2).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename2));
    flush(); // Flush system output buffer
    readfile($filename2);
    exit;
}
disconnect($con);
?>
