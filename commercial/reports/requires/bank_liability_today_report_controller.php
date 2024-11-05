<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.emblishments.php');
require_once('../../../includes/class4/class.washes.php');
require_once('../../../includes/class4/class.fabrics.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$seource_des_array=array(3=>"Non EPZ",4=>"Non EPZ",5=>"Abroad",6=>"Abroad",11=>"EPZ",12=>"EPZ");

	// if($action=="print_button_variable_setting")
	// {
	//     $print_report_format=0;
	//     $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=5 and report_id=112 and is_deleted=0 and status_active=1");
	//     //echo $print_report_format; die;
	//     echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	//     echo "print_report_button_setting('".$print_report_format."');\n";
	//     exit();
	// }

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
	$sql_cond_payment="";
	if($cbo_company_name>0) $sql_cond_payment=" and a.company_id=$cbo_company_name";
	$currency_convert_result=sql_select("select * from (select a.id, a.conversion_rate, max(id) over () as max_pk from currency_conversion_rate a  where a.status_active=1 and a.currency=2) where id=max_pk");
	$currency_convert_rate=$currency_convert_result[0][csf("conversion_rate")];
	$tk_btb_lc_arr=return_library_array( "select id from com_btb_lc_master_details where currency_id=1",'id','id');
	//print_r($tk_btb_lc_arr);
	//$sql_payment="select a.lc_id, a.invoice_id, b.accepted_ammount from com_import_payment_mst a, com_import_payment b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $sql_cond_payment";
	$sql_payment="select a.lc_id, b.invoice_id, b.accepted_ammount 
	from com_import_payment b left join com_import_payment_mst a on b.mst_id=a.id and a.status_active=1  $sql_cond_payment  
	where b.status_active=1 and b.is_deleted=0";
	//echo $sql_payment;
	$sql_payment_result=sql_select($sql_payment);
	$invoice_wise_payment=array();
	foreach($sql_payment_result as $row)
	{
		if($tk_btb_lc_arr[$row[csf("lc_id")]])
		{
			$invoice_wise_payment[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")]/$currency_convert_rate;
		}
		else
		{
			$invoice_wise_payment[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")];
		}
	}
	unset($sql_payment_result);
	
	$sql_payment_atsite="select lc_id, invoice_id, accepted_ammount from com_import_payment_com where status_active=1 and is_deleted=0";
	//echo $sql_payment;
	$sql_payment_atsite_result=sql_select($sql_payment_atsite);
	$invoice_payment_atsite=array();
	foreach($sql_payment_atsite_result as $row)
	{
		if($tk_btb_lc_arr[$row[csf("lc_id")]])
		{
			$invoice_payment_atsite[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")]/$currency_convert_rate;
		}
		else
		{
			$invoice_payment_atsite[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")];
		}
	}
	unset($sql_payment_atsite_result);
	
	//$invoice_payment_atsite=return_library_array( "select invoice_id, sum(accepted_ammount) as accepted_ammount from com_import_payment_com where status_active=1 and is_deleted=0 group by invoice_id",'invoice_id','accepted_ammount');
	
	$sql_cond="";
	if($cbo_company_name>0) $sql_cond=" and a.importer_id=$cbo_company_name";
	if($cbo_lein_bank>0) $sql_cond.=" and a.issuing_bank_id=$cbo_lein_bank";
	
	if($db_type==0)
	{
		$ifdbc_edf_sql="SELECT a.id as btb_lc_id, a.importer_id, a.issuing_bank_id, a.lc_category, a.lc_number, a.lc_value, a.maturity_from_id, a.payterm_id, a.lc_type_id, b.id as import_inv_id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, b.bank_ref, sum(c.current_acceptance_value) as edf_loan_value, 1 as type 
		from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
		where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and CONVERT(a.lc_category,SIGNED)>0 $sql_cond 
		group by a.id, a.importer_id, a.issuing_bank_id, a.lc_category, a.lc_number, a.lc_value, a.maturity_from_id, a.payterm_id, a.lc_type_id, b.id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, b.bank_ref
		order by btb_lc_id";
	}
	else
	{
		$ifdbc_edf_sql="SELECT a.id as btb_lc_id, a.importer_id, a.issuing_bank_id, a.lc_category, a.lc_number, a.lc_value, a.maturity_from_id, a.payterm_id, a.lc_type_id, b.id as import_inv_id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, b.bank_ref, sum(c.current_acceptance_value) as edf_loan_value, 1 as type 
		from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
		where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and to_number(a.lc_category)>0 $sql_cond 
		group by a.id, a.importer_id, a.issuing_bank_id, a.lc_category, a.lc_number, a.lc_value, a.maturity_from_id, a.payterm_id, a.lc_type_id, b.id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, b.bank_ref
		order by btb_lc_id";
	}
	
	//echo $ifdbc_edf_sql;die;
	$ifdbc_edf_sql_result=sql_select($ifdbc_edf_sql);
	$ifdbc_edf_data=array();$lc_wise_payment=array();
	foreach($ifdbc_edf_sql_result as $row)
	{
		if($tk_btb_lc_arr[$row[csf("btb_lc_id")]])
		{
			$row[csf("lc_value")]=$row[csf("lc_value")]/$currency_convert_rate;
			$row[csf("edf_loan_value")]=$row[csf("edf_loan_value")]/$currency_convert_rate;
		}
		
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
			if($row[csf("bank_ref")]!='')
			{
				$lc_wise_payment[$row[csf("btb_lc_id")]]+=$row[csf("edf_loan_value")];
			}

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
					if($row[csf("payterm_id")]==2)
					{
						$paid_value=$invoice_wise_payment[$row[csf("import_inv_id")]];
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
		$btb_sql="SELECT a.id, a.importer_id, a.issuing_bank_id, a.lc_type_id, a.lc_value, a.lc_type_id from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 and a.ref_closing_status<>1 and CONVERT(a.lc_category,SIGNED)>0 and a.payterm_id<>3 $sql_cond order by a.importer_id, a.issuing_bank_id";
	}
	else
	{
		$btb_sql="SELECT a.id, a.importer_id, a.issuing_bank_id, a.lc_type_id, a.lc_value, a.lc_type_id from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.payterm_id<>3 $sql_cond order by a.importer_id, a.issuing_bank_id";
	}
	
	//echo $btb_sql;die;
	$btb_sql_result=sql_select($btb_sql);
	$btb_data=array();$all_btb_company=array();$all_btb_bank=array();
	foreach($btb_sql_result as $row)
	{
		if($tk_btb_lc_arr[$row[csf("id")]])
		{
			$row[csf("lc_value")]=$row[csf("lc_value")]/$currency_convert_rate;
		}
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
	if($cbo_lein_bank>0) $bank_ord_cond=" and a.tag_bank=$cbo_lein_bank";
	/* $order_sql="select c.id as po_id, a.bank_id, b.company_name, (c.po_quantity*b.total_set_qnty) as order_quantity, c.po_total_price as order_total, c.status_active 
	from lib_buyer a, wo_po_details_master b, wo_po_break_down c 
	where a.id=b.buyer_name and b.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.bank_id>0 and c.is_deleted=0 and c.shiping_status<>3 $com_ord_cond $bank_ord_cond order by po_id"; */
	$order_sql="select c.id as po_id, a.tag_bank as bank_id, b.company_name, (c.po_quantity*b.total_set_qnty) as order_quantity, c.po_total_price as order_total, c.status_active 
	from lib_buyer_tag_bank a, wo_po_details_master b, wo_po_break_down c 
	where a.buyer_id=b.buyer_name and b.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and c.status_active in (1,2,3) and c.is_deleted=0 and c.shiping_status<>3  and a.TAG_BANK>0 $com_ord_cond $bank_ord_cond order by po_id";
	//echo $order_sql;// die;
	$order_sql_result=sql_select($order_sql);
	$pending_ord_qnty=0;$tot_pending_ord_qnty=0;$tot_pending_ord_value=0;
	$po_chk_arr=array();
	foreach($order_sql_result as $row)
	{
		$ord_rate=0;
		if(!in_array($row["PO_ID"],$po_chk_arr))
		{
			$po_chk_arr[]=$row["PO_ID"];
			if($row[csf("order_total")]>0 && $row[csf("order_quantity")] >0)
			{
				$ord_rate=($row[csf("order_total")]/$row[csf("order_quantity")])*1;
			}
			$pendin_qnty=($row[csf("order_quantity")]-$ex_fact_data[$row[csf("po_id")]])*1;
			if($pendin_qnty>0)
			{
				$pending_ord_value=($pendin_qnty*$ord_rate);
				if($row["STATUS_ACTIVE"]==1)
				{
					$pending_ord_data_arr[$row[csf("company_name")]][$row[csf("bank_id")]]["pendin_qnty"]+=$pendin_qnty;
					$pending_ord_data_arr[$row[csf("company_name")]][$row[csf("bank_id")]]["pending_ord_value"]+=$pending_ord_value;
					
					$pending_ord_data_bank_arr[$row[csf("bank_id")]]["pendin_qnty"]+=$pendin_qnty;
					$pending_ord_data_bank_arr[$row[csf("bank_id")]]["pending_ord_value"]+=$pending_ord_value;
					$order_wise_pending_value[$row[csf("po_id")]]=$pending_ord_value;
					$tot_pending_ord_qnty+=$pendin_qnty;
					$tot_pending_ord_value+=$pending_ord_value;
				}
				elseif($row["STATUS_ACTIVE"]==2)
				{
					$tot_hold_ord_qnty+=$pendin_qnty;
					$tot_hold_ord_value+=$pending_ord_value;
				}
				elseif($row["STATUS_ACTIVE"]==3)
				{
					$tot_cancel_ord_qnty+=$pendin_qnty;
					$tot_cancel_ord_value+=$pending_ord_value;
				}
			}
		}
		//$tot_pending_ord_qnty+=$pendin_qnty;
	}
	//echo "<pre>";print_r($order_wise_pending_value);die;
	
	//echo $pendin_qnty."ord_rate".$ord_rate."ord_rates".$pending_ord_value;
	unset($order_sql_result);
	unset($po_chk_arr);
	
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
	$realize_data_arr=array();$lc_wise_rlz=array();$proceed_dtls_check=array();
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
	/* if($cbo_company_name>0) $beneficiary_cond=" and b.company_id=$cbo_company_name";
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
	} */
	if($cbo_company_name>0) $beneficiary_cond=" and a.beneficiary_id=$cbo_company_name";
	if($cbo_lein_bank>0) $bank_id_cond=" and d.lien_bank=$cbo_lein_bank";
	$bank_cd_sql="SELECT a.BENEFICIARY_ID, sum(b.loan_amount) as LOAN_AMOUNT, d.LIEN_BANK 
	from com_pre_export_finance_mst a, com_pre_export_finance_dtls b, com_pre_export_lc_wise_dtls c, com_export_lc d 
	where a.id=b.mst_id and b.id=c.pre_export_dtls_id and c.lc_sc_id=d.id and b.bank_account_id=10 and c.export_type=1 and a.status_active=1 and b.status_active=1 and d.status_active=1 $beneficiary_cond $bank_id_cond
	group by a.beneficiary_id, d.lien_bank
	union all
	SELECT a.BENEFICIARY_ID, sum(b.loan_amount) as LOAN_AMOUNT, d.LIEN_BANK
	from com_pre_export_finance_mst a, com_pre_export_finance_dtls b, com_pre_export_lc_wise_dtls c, com_sales_contract d 
	where a.id=b.mst_id and b.id=c.pre_export_dtls_id and c.lc_sc_id=d.id and b.bank_account_id=10 and c.export_type=2 and a.status_active=1 and b.status_active=1 and d.status_active=1 $beneficiary_cond $bank_id_cond
	group by a.beneficiary_id, d.lien_bank";
	// echo $bank_cd_sql;
	$bank_cd_data=sql_select($bank_cd_sql);
	$sod_cd_amt=0;
	foreach($bank_cd_data as $row)
	{
		$sod_cd_amt+=$row["LOAN_AMOUNT"];
		$sod_cd_amt_arr[$row["BENEFICIARY_ID"]][$row["LIEN_BANK"]]+=$row["LOAN_AMOUNT"];
		$sod_cd_amt_bank_arr[$row["LIEN_BANK"]]+=$row["LOAN_AMOUNT"];
	}
	unset($bank_cd_data);
	
	$bill_com_cond="";
	if($cbo_company_name>0) $bill_com_cond=" and b.company_id=$cbo_company_name";
	if($cbo_lein_bank>0) $bill_bank_cond=" and b.lien_bank=$cbo_lein_bank";
	$bill_trans="SELECT b.id as bill_id, sum(a.lc_sc_curr) as bill_value 
	from com_export_doc_sub_trans a, com_export_doc_submission_mst b 
	where a.doc_submission_mst_id=b.id and b.entry_form = 40 and b.lien_bank > 0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond 
	group by b.id";
	// echo $bill_trans;
	$bill_trans_result=sql_select($bill_trans);


	$bill_trans_data=array();
	foreach($bill_trans_result as $row)
	{
		$bill_trans_data[$row[csf("bill_id")]]+=$row[csf("bill_value")];
	}
	unset($bill_trans_result);
	
	$bill_sql="SELECT b.id as bill_id, b.company_id, b.lien_bank, b.submit_type, a.is_lc, sum(a.net_invo_value) as bill_value, a.submission_dtls_id 
	from com_export_doc_submission_invo a, com_export_doc_submission_mst b 
	where a.doc_submission_mst_id=b.id and b.entry_form = 40 and b.lien_bank > 0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond 
	group by b.id, b.company_id, b.lien_bank, b.submit_type, a.is_lc, a.submission_dtls_id ";
	// echo $bill_sql;
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
		if($realize_data_arr[$row[csf("bill_id")]]=="" && $row[csf("submission_dtls_id")]!=0 && $row[csf("is_lc")]==2)
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
	                <td title="All Retire Source With out EDF And PAD">ABP</td>
	                <?
					foreach($all_btb_bank as $com_id=>$com_data)
					{
						foreach($com_data as $bank_id)
						{
							?>
	                        <td align="right" title="<? //print_r($fdbp_count_data);?>"><a href='#report_detals'  onclick= "openmypage('<? echo $com_id; ?>','<? echo $bank_id; ?>','ifdbc_popup','ABP Info','2')"><? echo number_format($ifdbc_edf_data[$com_id][$bank_id]["ifdbc"],2);?></a></td>
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
	                        <td align="right" title="<? print_r($edf_count_data);?>"><a href='#report_detals'  onclick= "openmypage('<? echo $com_id; ?>','<? echo $bank_id; ?>','ifdbc_popup','PAD (EDF)','3')"><? echo number_format($ifdbc_edf_data[$com_id][$bank_id]["edf"],2);?></a></td>
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
	                <td>ABP - Machinery/Cash Lc</td>
	                <?
					foreach($all_btb_bank as $com_id=>$com_data)
					{
						foreach($com_data as $bank_id)
						{
							?>
	                        <td align="right"><a href='#report_detals'  onclick= "openmypage('<? echo $com_id; ?>','<? echo $bank_id; ?>','margine_popup','ABP - Machinery','7')"><? echo number_format(($margin_btb_data[$com_id][$bank_id]),2);?></a></td>
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
	                <!-- <td>ODG/RL (BDT)</td> -->
	                <td>OG</td>
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
	                <!-- <td>SOD/CD (BDT)</td> -->
	                <td>PC/RL</td>
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
	            	<td colspan="<? echo $count_col+3;?>" title="<? echo $tot_pending_ord_qnty; ?>">&nbsp;&nbsp;<a href="##" onClick="openmypage_popup('<?  echo $cbo_company_name."__".$cbo_lein_bank; ?>','Order In Hand Info','order_in_hand_popup');" ><? echo number_format($tot_pending_ord_value,2) ?></a></td>
	            </tr>
                <tr bgcolor="#CCCCCC">
                	<td align="center">18</td>
	                <td>Order In Hold Total</td>
	            	<td colspan="<? echo $count_col+3;?>" title="<? echo $tot_hold_ord_qnty; ?>">&nbsp;&nbsp;<a href="##" onClick="openmypage_popup('<?  echo $cbo_company_name."__".$cbo_lein_bank; ?>','Order In Hold Info','order_in_hold_popup');" ><? echo number_format($tot_hold_ord_value,2) ?></a></td>
	            </tr>
                <tr bgcolor="#CCCCCC">
                	<td align="center">19</td>
	                <td>Order In Cancel Total</td>
	            	<td colspan="<? echo $count_col+3;?>" title="<? echo $tot_pending_ord_qnty; ?>">&nbsp;&nbsp;<a href="##" onClick="openmypage_popup('<?  echo $cbo_company_name."__".$cbo_lein_bank; ?>','Order In Cancel Info','order_in_cancel_popup');" ><? echo number_format($tot_cancel_ord_value,2) ?></a></td>
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

		disconnect($con);
		//Mail send------------------------------------------
	
	if($is_mail_send==1){

		require_once('../../../mailer/class.phpmailer.php');
		require_once('../../../auto_mail/setting/mail_setting.php');
		$mailToArr=array();
		 
		$sql = "SELECT c.email_address as MAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=71 and a.COMPANY_ID=$cbo_company_name and b.mail_user_setup_id=c.id";
		$mail_sql=sql_select($sql);
		$mailArr=array();
		foreach($mail_sql as $row)
		{
			$mailArr[$row[MAIL]]=$row[MAIL]; 
		}
		$to=implode(',',$mailArr);
		
		$att_file_arr[]=$filename.'**'.$filename;
		
		$subject="Bank Liability Position As Of Today";
		$mailBody="Please check Attached file.";
		$header=mailHeader();
		echo sendMailMailer( $to, $subject, $mailBody, $from_mail,$att_file_arr );
		
	}
	//------------------------------------End;
	exit(); 
}

if($action=="report_generate_retention")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_lein_bank=str_replace("'","",$cbo_lein_bank); 
	$txt_exchange_rate=str_replace("'","",$txt_exchange_rate);
	//echo $hide_year;die;
	$company_arr=return_library_array( "select company_name,id from  lib_company",'id','company_name');
	$bank_arr=return_library_array( "select bank_name,id from lib_bank",'id','bank_name');
	$sql_cond_payment="";
	if($cbo_company_name>0) $sql_cond_payment=" and a.company_id=$cbo_company_name";
	$currency_convert_result=sql_select("select * from (select a.id, a.conversion_rate, max(id) over () as max_pk from currency_conversion_rate a  where a.status_active=1 and a.currency=2) where id=max_pk");
	$currency_convert_rate=$currency_convert_result[0][csf("conversion_rate")];
	$tk_btb_lc_arr=return_library_array( "select id from com_btb_lc_master_details where currency_id=1",'id','id');
	//print_r($tk_btb_lc_arr);
	
	/*$sql_payment="select a.lc_id, a.invoice_id, b.accepted_ammount from com_import_payment_mst a, com_import_payment b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $sql_cond_payment";
	$sql_payment_result=sql_select($sql_payment);
	$lc_wise_payment=array();$invoice_wise_payment=array();
	foreach($sql_payment_result as $row)
	{
		//$lc_wise_payment[$row[csf("lc_id")]]+=$row[csf("accepted_ammount")];
		$invoice_wise_payment[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")];
	}
	unset($sql_payment_result);*/
	
	
	
	$sql_payment="select a.lc_id, b.invoice_id, b.accepted_ammount 
	from com_import_payment b left join com_import_payment_mst a on b.mst_id=a.id and a.status_active=1  $sql_cond_payment  
	where b.status_active=1 and b.is_deleted=0";
	//echo $sql_payment;
	$sql_payment_result=sql_select($sql_payment);
	$lc_wise_payment=array();$invoice_wise_payment=array();
	foreach($sql_payment_result as $row)
	{
		//$lc_wise_payment[$row[csf("lc_id")]]+=$row[csf("accepted_ammount")];
		$invoice_wise_payment[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")];
	}
	
	$sql_cond="";
	if($cbo_company_name>0) $sql_cond=" and a.importer_id=$cbo_company_name";
	if($cbo_lein_bank>0) $sql_cond.=" and a.issuing_bank_id=$cbo_lein_bank";
	
	if($db_type==0)
	{
		$ifdbc_edf_sql="select a.id as btb_lc_id, a.importer_id, a.issuing_bank_id, a.lc_category, a.lc_number, a.lc_value, a.maturity_from_id, a.payterm_id, a.lc_type_id, b.id as import_inv_id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, sum(c.current_acceptance_value) as edf_loan_value, 1 as type 
		from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
		where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and CONVERT(a.lc_category, SIGNED)>0 $sql_cond 
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
	//echo $ifdbc_edf_sql."<br>";
	
	//echo $ifdbc_edf_sql;die;
	$ifdbc_edf_sql_result=sql_select($ifdbc_edf_sql);
	$ifdbc_edf_data=array();$edf_count=0;
	foreach($ifdbc_edf_sql_result as $row)
	{
		if($row[csf("lc_type_id")]==2)
		{
			$margin_btb_data[$row[csf("issuing_bank_id")]][$row[csf("importer_id")]]+=$row[csf("edf_loan_value")]-$invoice_wise_payment[$row[csf("import_inv_id")]];
			$margin_btb_bank_total[$row[csf("issuing_bank_id")]]+=$row[csf("edf_loan_value")]-$invoice_wise_payment[$row[csf("import_inv_id")]];
		}
		else
		{
			$lc_wise_payment[$row[csf("btb_lc_id")]]+=$row[csf("edf_loan_value")];
			$maturity_date="";
			/*if($row[csf("maturity_from_id")]==1) $maturity_date=$row[csf("bank_acc_date")];
			else if($row[csf("maturity_from_id")]==2) $maturity_date=$row[csf("shipment_date")];
			else if($row[csf("maturity_from_id")]==3) $maturity_date=$row[csf("nagotiate_date")];
			else if($row[csf("maturity_from_id")]==4) $maturity_date=$row[csf("bill_date")];
			else $maturity_date="";*/
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
					if($row[csf("edf_paid_date")] != "" && $row[csf("edf_paid_date")] != "0000-00-00")
					{
						$paid_value=$row[csf("edf_loan_value")];
					}
				}
				
				if($maturity_date!="" && $maturity_date!="0000-00-00")
				{
					//$edf_count++;
					//$edf_count_data[$row[csf("import_inv_id")]]=($row[csf("edf_loan_value")]-$paid_value)."=".$row[csf("lc_number")]."=".$row[csf("lc_value")];
					$ifdbc_edf_data[$row[csf("issuing_bank_id")]][$row[csf("importer_id")]]["edf"]+=$row[csf("edf_loan_value")]-$paid_value;
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
							$ifdbc_edf_data[$row[csf("issuing_bank_id")]][$row[csf("importer_id")]]["ifdbc"]+=$row[csf("edf_loan_value")]-$invoice_wise_payment[$row[csf("import_inv_id")]];
							$ifdbc_bank_total[$row[csf("issuing_bank_id")]]+=$row[csf("edf_loan_value")]-$invoice_wise_payment[$row[csf("import_inv_id")]];
						}
					}
				}
			}
		}
		
	}	
	unset($ifdbc_edf_sql_result);
	//echo "<pre>";print_r($margin_btb_data);die;
	
	if($db_type==0)
	{
		$btb_sql="select a.id, a.importer_id, a.issuing_bank_id, a.lc_type_id, a.lc_value from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 and a.ref_closing_status<>1 and CONVERT(a.lc_category, SIGNED)>0 and a.lc_type_id<>2 $sql_cond order by a.importer_id, a.issuing_bank_id";
	}
	else
	{
		$btb_sql="select a.id, a.importer_id, a.issuing_bank_id, a.lc_type_id, a.lc_value from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.lc_type_id<>2 $sql_cond order by a.importer_id, a.issuing_bank_id";
	}
	
	$btb_sql_result=sql_select($btb_sql);
	$btb_data=array();$all_btb_bank=array();
	foreach($btb_sql_result as $row)
	{
		$pending_value=$row[csf("lc_value")]-$lc_wise_payment[$row[csf("id")]];
		$all_btb_bank[$row[csf("issuing_bank_id")]][$row[csf("importer_id")]]=$row[csf("importer_id")];
		if($pending_value>0)
		{
			$btb_data[$row[csf("issuing_bank_id")]][$row[csf("importer_id")]]+=$row[csf("lc_value")]-$lc_wise_payment[$row[csf("id")]];
			$btb_bank_total[$row[csf("issuing_bank_id")]]+=$row[csf("lc_value")]-$lc_wise_payment[$row[csf("id")]];
		}
	}
	unset($btb_sql_result);
	
	$pak_beneficiary_cond="";
	if($cbo_company_name>0) $pak_beneficiary_cond=" and c.beneficiary_name=$cbo_company_name";
	$packing_sql=sql_select("select c.beneficiary_name, c.lien_bank, a.loan_amount  
	from com_pre_export_finance_dtls a, com_pre_export_lc_wise_dtls b, com_export_lc c 
	where a.id=b.pre_export_dtls_id and b.lc_sc_id=c.id and b.export_type=1 and a.loan_type=20 and a.status_active=1 and a.is_deleted=0 $pak_beneficiary_cond
	union all
	select c.beneficiary_name, c.lien_bank, a.loan_amount  
	from com_pre_export_finance_dtls a, com_pre_export_lc_wise_dtls b, com_sales_contract c 
	where a.id=b.pre_export_dtls_id and b.lc_sc_id=c.id and b.export_type=2 and a.loan_type=20 and a.status_active=1 and a.is_deleted=0 $pak_beneficiary_cond");
	$odg_amt_arr=array();
	foreach($packing_sql as $row)
	{
		if($row[csf("lien_bank")])
		{
			$odg_amt+=$row[csf("loan_amount")];
			$odg_amt_arr[$row[csf("lien_bank")]][$row[csf("beneficiary_name")]]+=$row[csf("loan_amount")];
			$odg_amt_bankWise_arr[$row[csf("lien_bank")]]+=$row[csf("loan_amount")];
		}
	}
	unset($packing_sql);
	$bank_id_cond=$beneficiary_cond="";
	if($cbo_company_name>0) $beneficiary_cond=" and b.company_id=$cbo_company_name";
	if($cbo_lein_bank>0) $bank_id_cond=" and a.id=$cbo_lein_bank";
	$bank_cd_sql=sql_select("select b.company_id, a.id, b.account_type, b.loan_limit as loan_amount 
	from lib_bank a, lib_bank_account b 
	where a.id=b.account_id and b.account_type in(5,6,10,81,95) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $beneficiary_cond $bank_id_cond");
	$sod_cd_amt=0;
	foreach($bank_cd_sql as $row)
	{
		if($row[csf("account_type")]==10)
		{
			$sod_cd_amt_arr[$row[csf("id")]][$row[csf("company_id")]]+=$row[csf("loan_amount")];
		}
		else
		{
			$sod_dfc_amt_arr[$row[csf("id")]][$row[csf("company_id")]][$row[csf("account_type")]]+=$row[csf("loan_amount")];
		}
		
		
		//$sod_dfc_amt_bank_arr[$row[csf("id")]]+=$row[csf("loan_amount")];
	}
	unset($bank_cd_sql);
	
	$exfact_com_cond="";
	if($cbo_company_name>0) $exfact_com_cond=" and a.company_id=$cbo_company_name";
	$ex_fact_data=return_library_array("select b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
	//echo "<pre>";print_r($ex_fact_data[33372]);echo "<br>";//die;
	$com_cond="";
	if($cbo_company_name>0) $com_ord_cond=" and b.company_name=$cbo_company_name";
	if($cbo_lein_bank>0) $bank_ord_cond=" and a.TAG_BANK=$cbo_lein_bank";
	$order_sql="select c.id as po_id, b.job_no, a.bank_id, b.company_name, (c.po_quantity*b.total_set_qnty) as order_quantity, c.po_total_price as order_total 
	from lib_buyer_tag_bank a, wo_po_details_master b, wo_po_break_down c 
	where a.buyer_id=b.buyer_name and b.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and a.TAG_BANK>0 $com_ord_cond $bank_ord_cond order by po_id";
	//echo $order_sql;die;
	$order_sql_result=sql_select($order_sql);
	$pending_ord_qnty=0;$tot_pending_ord_qnty=0;$tot_pending_ord_value=0;
	$com_bank_order_value=array();$po_id_arr=array();
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
			$pending_ord_data_arr[$row[csf("bank_id")]][$row[csf("company_name")]]["pendin_qnty"]+=$pendin_qnty;
			$pending_ord_data_arr[$row[csf("bank_id")]][$row[csf("company_name")]]["pending_ord_value"]+=$pending_ord_value;
			
			$pending_ord_data_bank_arr[$row[csf("bank_id")]]["pendin_qnty"]+=$pendin_qnty;
			$pending_ord_data_bank_arr[$row[csf("bank_id")]]["pending_ord_value"]+=$pending_ord_value;
			$order_wise_pending_value[$row[csf("po_id")]]=$pending_ord_value;
			$tot_pending_ord_qnty+=$pendin_qnty;
			$tot_pending_ord_value+=$pending_ord_value;
		}
		
		$job_wise_bank_com[$row[csf("job_no")]]=$row[csf("bank_id")]."__".$row[csf("company_name")];
		$com_bank_order_value[$row[csf("bank_id")]][$row[csf("company_name")]]+=$row[csf("order_total")];
		$po_id_arr[$row[csf("po_id")]]=$row[csf("po_id")];
	}
	unset($order_sql_result);

	$po_id_in=where_con_using_array($po_id_arr,0,'a.wo_po_break_down_id');
	$attach_po_sql="SELECT b.import_mst_id as BTB_MST_ID from com_sales_contract_order_info a,com_btb_export_lc_attachment b where a.com_sales_contract_id=b.lc_sc_id and b.is_lc_sc=1 and a.status_active=1 $po_id_in
	union all 
	select b.import_mst_id as BTB_MST_ID from com_export_lc_order_info a,com_btb_export_lc_attachment b where a.com_export_lc_id=b.lc_sc_id and b.is_lc_sc=0 and a.status_active=1 and b.status_active=1 $po_id_in";
	// echo $attach_po_sql;
	$lc_sc_res=sql_select($attach_po_sql);
	$btb_id_arr=array();
	foreach($lc_sc_res as $row)
	{
		$btb_id_arr[$row['BTB_MST_ID']]=$row['BTB_MST_ID'];
	}
	unset($lc_sc_res);

	$btb_id_in=where_con_using_array($btb_id_arr,0,'id');
	$btb_sql="SELECT id, importer_id as IMPORTER_ID, issuing_bank_id as ISSUING_BANK_ID, lc_value as LC_VALUE from com_btb_lc_master_details where status_active=1 $btb_id_in ";
	// echo $btb_sql;
	$btb_data=sql_select($btb_sql);
	$btb_value_arr=array();
	foreach($btb_data as $row)
	{
		$btb_value_arr[$row['ISSUING_BANK_ID']][$row['IMPORTER_ID']]+=$row['LC_VALUE'];
	}
	
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
	
	if(str_replace("'","",$cbo_company_name)>0) $com_cond=" and a.beneficiary_name=$cbo_company_name";
	$attach_order_sql="select b.wo_po_break_down_id, c.job_no_mst from com_sales_contract a, com_sales_contract_order_info b, wo_po_break_down c where a.id=b.com_sales_contract_id and b.wo_po_break_down_id=c.id and a.status_active=1 and b.status_active=1 $com_cond
	union all 
	select b.wo_po_break_down_id, c.job_no_mst from com_export_lc a, com_export_lc_order_info b, wo_po_break_down c where a.id=b.com_export_lc_id and b.wo_po_break_down_id=c.id and a.status_active=1 and b.status_active=1 $com_cond";
	//echo $attach_order_sql; die;
	$attach_order_sql_result=sql_select($attach_order_sql);
	$attach_order_id=$all_job_no=array();
	foreach($attach_order_sql_result as $row)
	{
		$attach_order_id[$row[csf("wo_po_break_down_id")]]=$row[csf("wo_po_break_down_id")];
		$powiseJobNoArr[$row[csf("wo_po_break_down_id")]]=$row[csf("job_no_mst")];
		$all_job_no[$row[csf("job_no_mst")]]=$row[csf("job_no_mst")];
	}
	
	if(count($attach_order_id)>0)
	{
		$condition= new condition();
		
		if(implode(",",$attach_order_id)!='')
		{
			$condition->po_id(" in( ".implode(",",$attach_order_id)." ) ");
		}
		
		$condition->init();
		$fabric= new fabric($condition);
		$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
		
		$yarn= new yarn($condition);
		$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
		
		$trims= new trims($condition);
		$trims_costing_arr=$trims->getAmountArray_by_order();
		//echo "tppps";die;
		$emblishment= new emblishment($condition);
		$emblishment_costing_arr=$emblishment->getAmountArray_by_orderAndEmbname();
		
		$wash= new wash($condition);
		$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
		
		foreach($attach_order_id as $bompoid)
		{
			$job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['0']+=array_sum($fabric_costing_arr['sweater']['grey'][$bompoid])+array_sum($fabric_costing_arr['knit']['grey'][$bompoid])+array_sum($fabric_costing_arr['woven']['grey'][$bompoid]);
			$job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['1']+=$yarn_costing_arr[$bompoid];
			$job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['2']+=$trims_costing_arr[$bompoid];
			$job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['3']+=$emblishment_costing_arr[$bompoid][1]+$emblishment_costing_arr_name_wash[$bompoid][3]+$emblishment_costing_arr[$bompoid][4]+$emblishment_costing_arr[$bompoid][5];
		}
		//$budge_btb_open_amt+=array_sum($job_wise_budge_amt);
	}
	
	//echo "<pre>";print_r($job_wise_btb_value);die;
	
	$btb_open=array();
	foreach($all_job_no as $job=>$job_num)
	{
		$bank_com_ref=explode("__",$job_wise_bank_com[$job_num]);
		$bank_id=$bank_com_ref[0];
		$company_id=$bank_com_ref[1];
		$job_wisie_btb_value=$job_wisie_budge_value=0;
		if($bank_id && $company_id)
		{
			$job_wisie_btb_value=$job_wise_btb_value[$job_num][1]+$job_wise_btb_value[$job_num][2];
			$job_wisie_budge_value=$job_wise_budge_amt[$job_num][0]+$job_wise_budge_amt[$job_num][1]+$job_wise_budge_amt[$job_num][2]+$job_wise_budge_amt[$job_num][3];
			$btb_open[$bank_id][$company_id] +=$job_wisie_budge_value-$job_wisie_btb_value;
		}
	}
	
	
	
	/*
	if($cbo_company_name>0) $budge_com_cond=" and d.company_name=$cbo_company_name";
	$budge_btb_open_sql="select d.job_no, (d.job_quantity*d.total_set_qnty) as job_quantity, a.costing_per, b.amount, b.id as dtls_id, 1 as type
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
	
	$btb_open=array();
	foreach($all_job_no as $job=>$job_num)
	{
		$bank_com_ref=explode("__",$job_wise_bank_com[$job_num]);
		$bank_id=$bank_com_ref[0];
		$company_id=$bank_com_ref[1];
		if($bank_id && $company_id)
		{
			$btb_open[$bank_id][$company_id] +=($job_wise_budge_amt[$job_num][1]-$job_wise_btb_value[$job_num][1])+($job_wise_budge_amt[$job_num][2]-$job_wise_btb_value[$job_num][2])+($job_wise_budge_amt[$job_num][3]-$job_wise_btb_value[$job_num][3]);
		}
	}
	*/
	//echo "<pre>";print_r($btb_open);die;
	//+($row[csf("trims_cost")]-$job_wise_btb_value[$row[csf("job_no")]][2])+$row[csf("embel_cost")]
	//echo $btb_open; die;
	
	$beneficiary_cond="";
	if($cbo_company_name>0) $beneficiary_cond=" and b.benificiary_id=$cbo_company_name";
	$proceed_rlz_sql="select b.invoice_bill_id, c.document_currency as document_currency from com_export_proceed_realization b, com_export_proceed_rlzn_dtls c where b.id=c.mst_id and b.is_invoice_bill=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $beneficiary_cond";
	//echo $proceed_rlz_sql;die;
	$realize_data_arr=array();
	$proceed_rlz_sql_result=sql_select($proceed_rlz_sql);
	foreach($proceed_rlz_sql_result as $row)
	{
		$realize_data_arr[$row[csf("invoice_bill_id")]]+=$row[csf("document_currency")];
	}
	unset($proceed_rlz_sql_result);
	
	$bill_com_cond="";
	if($cbo_company_name>0) $bill_com_cond=" and b.company_id=$cbo_company_name";
	if($cbo_lein_bank>0) $bill_bank_cond=" and b.lien_bank=$cbo_lein_bank";
	$bill_sql="select b.id as bill_id, b.company_id, b.lien_bank, b.submit_type, sum(a.net_invo_value) as bill_value 
	from com_export_doc_submission_invo a, com_export_doc_submission_mst b 
	where a.doc_submission_mst_id=b.id and b.entry_form = 40 and b.lien_bank > 0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond 
	group by b.id, b.company_id, b.lien_bank, b.submit_type";
	//echo $bill_sql;
	$bill_sql_result=sql_select($bill_sql);
	$fdbp_data=array();
	foreach($bill_sql_result as $row)
	{
		if($row[csf("submit_type")]==2)
		{
			$fdbp_data[$row[csf("lien_bank")]][$row[csf("company_id")]]+=$row[csf("bill_value")]-$realize_data_arr[$row[csf("bill_id")]];
			$fdbp_bank_data[$row[csf("lien_bank")]]+=$row[csf("bill_value")]-$realize_data_arr[$row[csf("bill_id")]];
		}
		else
		{
			$bill_receiveable_arr[$row[csf("lien_bank")]][$row[csf("company_id")]]+=$row[csf("bill_value")]-$realize_data_arr[$row[csf("bill_id")]];
			$bill_receiveable_bank_arr[$row[csf("lien_bank")]]+=$row[csf("bill_value")]-$realize_data_arr[$row[csf("bill_id")]];
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
	$inv_sql=" select b.benificiary_id, a.lien_bank, b.id as inv_id, b.net_invo_value from com_export_lc a, com_export_invoice_ship_mst b where a.id=b.lc_sc_id and b.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $com_ship_cond $bank_ship_cond
	union all
	select b.benificiary_id, a.lien_bank, b.id as inv_id, b.net_invo_value from com_sales_contract a, com_export_invoice_ship_mst b where a.id=b.lc_sc_id and b.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $com_ship_cond $bank_ship_cond";
	//echo $inv_sql;die;
	$inv_sql_result=sql_select($inv_sql);
	foreach($inv_sql_result as $row)
	{
		$pending_value=$row[csf("net_invo_value")]-$inv_sub_arr[$row[csf("inv_id")]];
		if($pending_value>0)
		{
			$doc_ind_hand_arr[$row[csf("lien_bank")]][$row[csf("benificiary_id")]]+=$row[csf("net_invo_value")]-$inv_sub_arr[$row[csf("inv_id")]];
			$doc_ind_hand_bank_arr[$row[csf("lien_bank")]]+=$row[csf("net_invo_value")]-$inv_sub_arr[$row[csf("inv_id")]];
		}
	}
	
	//echo $pending_ord_qnty."==".$bill_receive."==". $odg_amt."==".$sod_cd_amt;die;
	//echo "<pre>";print_r($all_btb_bank);die;
	$count_col=0;$bank_col=0;
	foreach($all_btb_bank as $bank_id=>$bank_data)
	{
		foreach($bank_data as $com_val)
		{
			$count_col++;
		}
		$count_col++;
	}
	//echo "<pre>";print_r($bank_check);die;
	//echo $count_col;die;
	$table_width=(350+(100*$count_col));
	$div_width=20+$table_width;
	$fig_lac=100000;
	ob_start();
	?>
	<div style="width:<? echo $div_width; ?>px;" id="scroll_body">
	<fieldset style="width:100%">
	    <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
	    	<thead>
            	<tr>
	                <th colspan="<? echo $count_col+3;?>" style="font-weight:bold; font-size:16px; color:#F00">Figure In Lac</th>
	            </tr
	        	><tr>
	                <th width="250" rowspan="2" colspan="2">Particulars</th>
	                <?
					foreach($all_btb_bank as $bank_id=>$bank_data)
					{
						?>
	                    <th colspan="<? echo count($bank_data)+1; ?>" title="<? echo $bank_id; ?>"><? echo $bank_arr[$bank_id];?></th>
	                    <?
					}
					?>
                    <th rowspan="2" width="100">Group Total</th>
	            </tr>
	            <tr>
	            	<?
					//$count_col=0;
					foreach($all_btb_bank as $bank_id=>$bank_data)
					{
						foreach($bank_data as $com_id=>$com_data)
						{
							?>
							<th title="<? echo $com_id; ?>" width="100"><? echo $company_arr[$com_id];?></th>
							<?
						}
						?>
                        <th width="100">Total</th>
                        <?
					}
					?>
                    	
	            </tr>
                <tr>
                	<th width="200" style="font-weight:bold; font-size:14px;">Assets :</th>
                    <th width="50">Currency</th>	
	            	 <?
					foreach($all_btb_bank as $bank_id=>$bank_data)
					{
						foreach($bank_data as $com_id=>$com_data)
						{
							?>
							<th>USD</th>
							<?
						}
						?>
                        <th>USD</th>
                        <?
					}
					?>
                    <th>USD</th>
	            </tr>
	        </thead>
	        <tbody>
            	<tr bgcolor="#E9F3FF">
	                <td>Export Under Execution</td>
	            	<td align="center">&nbsp;</td>
	                <?
					foreach($all_btb_bank as $bank_id=>$bank_data)
					{
						foreach($bank_data as $com_id)
						{
							?>
	                        <td align="right" title="<? echo number_format(($pending_ord_data_arr[$bank_id][$com_id]["pending_ord_value"]),2);?>"><? echo number_format(($pending_ord_data_arr[$bank_id][$com_id]["pending_ord_value"]/$fig_lac),2);?></td>
	                       <?
							$com_pending_ord+=$pending_ord_data_arr[$bank_id][$com_id]["pending_ord_value"];
							$tot_asset[$bank_id][$com_id]+=$pending_ord_data_arr[$bank_id][$com_id]["pending_ord_value"];
						}
						?>
                        <td align="right" title="<? echo number_format(($com_pending_ord),2);?>"><? echo number_format(($com_pending_ord/$fig_lac),2); $grp_com_pending_ord+=$com_pending_ord; $com_pending_ord=0;?></td>
                        <?
					}
					//echo "<pre>";print_r($tot_asset);die;
					?>
                    <td align="right" title="<? echo number_format(($grp_com_pending_ord),2);?>"><? echo number_format(($grp_com_pending_ord/$fig_lac),2);?></td>
	            </tr>
                <tr bgcolor="#FFFFFF">
	                <td>Document Inhand</td>
                    <td align="center">&nbsp;</td>
	                <?
					foreach($all_btb_bank as $bank_id=>$bank_data)
					{
						foreach($bank_data as $com_id)
						{
							?>
	                        <td align="right" title="<? echo number_format(($doc_ind_hand_arr[$bank_id][$com_id]),2);?>"><? echo number_format(($doc_ind_hand_arr[$bank_id][$com_id]/$fig_lac),2);?></td>
	                       <?
							$com_pending_doc+=$doc_ind_hand_arr[$bank_id][$com_id];
							$tot_asset[$bank_id][$com_id]+=$doc_ind_hand_arr[$bank_id][$com_id];
						}
						?>
                        <td align="right" title="<? echo number_format(($com_pending_doc),2);?>"><? echo number_format(($com_pending_doc/$fig_lac),2); $grp_com_pending_doc+=$com_pending_doc; $com_pending_doc=0;?></td>
                        <?
					}
					?>
                    <td align="right" title="<? echo number_format(($grp_com_pending_doc),2);?>"><? echo number_format(($grp_com_pending_doc/$fig_lac),2);?></td>
	            </tr>
                <tr bgcolor="#E9F3FF">
	                <td>Bills To Be Realized</td>
                    <td align="center">&nbsp;</td>
	                <?
					foreach($all_btb_bank as $bank_id=>$bank_data)
					{
						foreach($bank_data as $com_id)
						{
							?>
	                        <td align="right" title="<? echo number_format(($bill_receiveable_arr[$bank_id][$com_id]),2);?>"><? echo number_format(($bill_receiveable_arr[$bank_id][$com_id]/$fig_lac),2);?></td>
	                       <?
							$com_bill_receiveable+=$bill_receiveable_arr[$bank_id][$com_id];
							$tot_asset[$bank_id][$com_id]+=$bill_receiveable_arr[$bank_id][$com_id];
						}
						?>
                        <td align="right" title="<? echo number_format(($com_bill_receiveable),2);?>"><? echo number_format(($com_bill_receiveable/$fig_lac),2); $grp_com_bill_receiveable+=$com_bill_receiveable; $com_bill_receiveable=0;?></td>
                        <?
					}
					?>
                    <td align="right" title="<? echo number_format(($grp_com_bill_receiveable),2);?>"><? echo number_format(($grp_com_bill_receiveable/$fig_lac),2);?></td>
	            </tr>
                <tr bgcolor="#FFFFFF">
	                <td>Balance In FC Accounts</td>
	            	<td align="center">&nbsp;</td>
	                <?
					foreach($all_btb_bank as $bank_id=>$bank_data)
					{
						foreach($bank_data as $com_id)
						{
							?>
	                        <td align="right" title="<? echo number_format(($sod_dfc_amt_arr[$bank_id][$com_id][5]),2);?>"><? echo number_format(($sod_dfc_amt_arr[$bank_id][$com_id][5]/$fig_lac),2);?></td>
	                       <?
							$com_tot_margin_dfc+=$sod_dfc_amt_arr[$bank_id][$com_id][5];
							$tot_asset[$bank_id][$com_id]+=$sod_dfc_amt_arr[$bank_id][$com_id][5];
						}
						?>
                        <td align="right" title="<? echo number_format(($com_tot_margin_dfc),2);?>"><? echo number_format(($com_tot_margin_dfc/$fig_lac),2); $grp_com_tot_margin_dfc+=$com_tot_margin_dfc; $com_tot_margin_dfc=0;?></td>
                        <?
					}
					?>
                    <td align="right" title="<? echo number_format(($grp_com_tot_margin_dfc),2);?>"><? echo number_format(($grp_com_tot_margin_dfc/$fig_lac),2);?></td>
	            </tr>
                <tr bgcolor="#E9F3FF">
	                <td>Balance In Retention (ERQ)</td>
	            	<td align="center">&nbsp;</td>
	                <?
					foreach($all_btb_bank as $bank_id=>$bank_data)
					{
						foreach($bank_data as $com_id)
						{
							?>
	                        <td align="right" title="<? echo number_format(($sod_dfc_amt_arr[$bank_id][$com_id][6]),2);?>"><? echo number_format(($sod_dfc_amt_arr[$bank_id][$com_id][6]/$fig_lac),2);?></td>
	                       <?
							$com_tot_margin_erq+=$sod_dfc_amt_arr[$bank_id][$com_id][6];
							$tot_asset[$bank_id][$com_id]+=$sod_dfc_amt_arr[$bank_id][$com_id][6];
						}
						?>
                        <td align="right" title="<? echo number_format(($com_tot_margin_erq),2);?>"><? echo number_format(($com_tot_margin_erq/$fig_lac),2); $grp_com_tot_margin_erq+=$com_tot_margin_erq; $com_tot_margin_erq=0;?></td>
                        <?
					}
					?>
                    <td align="right" title="<? echo number_format(($grp_com_tot_margin_erq),2);?>"><? echo number_format(($grp_com_tot_margin_erq/$fig_lac),2);?></td>
	            </tr>
                <tr bgcolor="#CCCCCC">
	                <td style="font-weight:bold; font-size:14px;">Total Asset :</td>
	            	<td align="center">&nbsp;</td>
	                <?
					//echo "<pre>";print_r($tot_asset);die;
					foreach($all_btb_bank as $bank_id=>$bank_data)
					{
						foreach($bank_data as $com_id)
						{
							?>
	                        <td align="right" title="<? echo number_format(($tot_asset[$bank_id][$com_id]),2);?>"><? echo number_format(($tot_asset[$bank_id][$com_id]/$fig_lac),2);?></td>
	                       <?
							$com_tot_asset+=$tot_asset[$bank_id][$com_id];
						}
						?>
                        <td align="right" title="<? echo number_format(($com_tot_asset),2);?>"><? echo number_format(($com_tot_asset/$fig_lac),2); $grp_com_tot_asset+=$com_tot_asset; $com_tot_asset=0;?></td>
                        <?
					}
					?>
                    <td align="right" title="<? echo number_format(($grp_com_tot_asset),2);?>"><? echo number_format(($grp_com_tot_asset/$fig_lac),2);?></td>
	            </tr>
                <tr bgcolor="#CCCCCC">
                	<td style="font-weight:bold; font-size:14px;">Bank Liability :</td>
                    <td align="center" style="font-weight:bold;">Currency</td>	
	            	 <?
					foreach($all_btb_bank as $bank_id=>$bank_data)
					{
						foreach($bank_data as $com_id=>$com_data)
						{
							?>
							<td align="center" style="font-weight:bold;">USD</td>
							<?
						}
						?>
                        <td align="center" style="font-weight:bold;">USD</td>
                        <?
					}
					?>
                    <td align="center" style="font-weight:bold;">USD</td>
	            </tr>
                <?
				foreach($all_btb_bank as $bank_id=>$bank_data)
				{
					foreach($bank_data as $com_id)
					{
						$total_libiality[$com_id][$bank_id]+=$btb_data[$com_id][$bank_id]+$ifdbc_edf_data[$com_id][$bank_id]["ifdbc"]+$ifdbc_edf_data[$com_id][$bank_id]["edf"]+$fdbp_data[$com_id][$bank_id];
						if($odg_amt_arr[$bank_id][$com_id]>0 || $sod_cd_amt_arr[$bank_id][$com_id] > 0)
						{
							$total_od_liability[$bank_id][$com_id]+=(($odg_amt_arr[$bank_id][$com_id]+$sod_cd_amt_arr[$bank_id][$com_id])/$txt_exchange_rate);
						}
						
					}
				}
				?>
                <tr bgcolor="#FFFFFF">
	                <td>BTB+FDBP Liability</td>
	            	<td align="center">&nbsp;</td>
	                <?
					//echo "<pre>";print_r($tot_asset);die;
					foreach($all_btb_bank as $bank_id=>$bank_data)
					{
						foreach($bank_data as $com_id)
						{
							?>
	                        <td align="right" title="<? echo number_format(($total_libiality[$bank_id][$com_id]),2);?>"><? echo number_format(($total_libiality[$bank_id][$com_id]/$fig_lac),2);?></td>
	                       <?
							$com_total_libiality+=$total_libiality[$bank_id][$com_id];
							$tot_liability_arr[$bank_id][$com_id]+=$total_libiality[$bank_id][$com_id];
						}
						?>
                        <td align="right" title="<? echo number_format(($com_total_libiality),2);?>"><? echo number_format(($com_total_libiality/$fig_lac),2); $grp_com_total_libiality+=$com_total_libiality; $com_total_libiality=0;?></td>
                        <?
					}
					?>
                    <td align="right" title="<? echo number_format(($grp_com_total_libiality),2);?>"><? echo number_format(($grp_com_total_libiality/$fig_lac),2);?></td>
	            </tr>
                <tr bgcolor="#E9F3FF">
	                <td>BTB to be Opened</td>
	            	<td align="center">&nbsp;</td>
	                <?
					//echo "<pre>";print_r($tot_asset);die;
					foreach($all_btb_bank as $bank_id=>$bank_data)
					{
						foreach($bank_data as $com_id)
						{
							?>
	                        <td align="right" title="<? echo number_format(($btb_open[$bank_id][$com_id]),2);?>">
							<a href='#report_detals'  onclick= "openmypage('<? echo $com_id; ?>','<? echo $bank_id; ?>','btb_opened_popup','BTB to be Opened','5')">
							<? echo fn_number_format((($com_bank_order_value[$bank_id][$com_id]*0.45)-$btb_value_arr[$bank_id][$com_id])/100000,2);?>
							 <? //echo number_format(($btb_open[$bank_id][$com_id]/$fig_lac),2);?>
							</a>
							</td>
	                       <?
							$com_btb_open+=$btb_open[$bank_id][$com_id];
							$tot_liability_arr[$bank_id][$com_id]+=$btb_open[$bank_id][$com_id];
						}
						?>
                        <td align="right" title="<? echo number_format(($com_btb_open),2);?>"><? echo number_format(($com_btb_open/$fig_lac),2); $grp_com_btb_open+=$com_btb_open; $com_btb_open=0;?></td>
                        <?
					}
					?>
                    <td align="right" title="<? echo number_format(($grp_com_btb_open),2);?>"><? echo number_format(($grp_com_btb_open/$fig_lac),2);?></td>
	            </tr>
                <tr bgcolor="#FFFFFF">
	                <td>OD Garments + OD General</td>
	            	<td align="center">&nbsp;</td>
	                <?
					foreach($all_btb_bank as $bank_id=>$bank_data)
					{
						foreach($bank_data as $com_id)
						{
							?>
	                        <td align="right" title="<? echo number_format(($total_od_liability[$bank_id][$com_id]),2);?>"><? echo number_format(($total_od_liability[$bank_id][$com_id]/$fig_lac),2);?></td>
	                       <?
							$com_total_od_liability+=$total_od_liability[$bank_id][$com_id];
							$tot_liability_arr[$bank_id][$com_id]+=$total_od_liability[$bank_id][$com_id];
						}
						?>
                        <td align="right" title="<? echo number_format(($com_total_od_liability),2);?>"><? echo number_format(($com_total_od_liability/$fig_lac),2); $grp_com_total_od_liability+=$com_total_od_liability; $com_total_od_liability=0;?></td>
                        <?
					}
					?>
                    <td align="right" title="<? echo number_format(($grp_com_total_od_liability),2);?>"><? echo number_format(($grp_com_total_od_liability/$fig_lac),2);?></td>
	            </tr>
                <tr bgcolor="#CCCCCC">
	                <td style="font-weight:bold; font-size:14px;">Total Bank Liability :</td>
	            	<td align="center">&nbsp;</td>
	                <?
					foreach($all_btb_bank as $bank_id=>$bank_data)
					{
						foreach($bank_data as $com_id)
						{
							?>
	                        <td align="right" title="<? echo number_format(($tot_liability_arr[$bank_id][$com_id]),2);?>"><? echo number_format(($tot_liability_arr[$bank_id][$com_id]/$fig_lac),2);?></td>
	                       <?
							$com_tot_liability+=$tot_liability_arr[$bank_id][$com_id];
						}
						?>
                        <td align="right" title="<? echo number_format(($com_tot_liability),2);?>"><? echo number_format(($com_tot_liability/$fig_lac),2); $grp_com_tot_liability+=$com_tot_liability; $com_tot_liability=0;?></td>
                        <?
					}
					?>
                    <td align="right" title="<? echo number_format(($grp_com_tot_liability),2);?>"><? echo number_format(($grp_com_tot_liability/$fig_lac),2);?></td>
	            </tr>
                <tr bgcolor="#FFFFCC">
	                <td>Loan From 3rd Party</td>
	            	<td align="center">&nbsp;</td>
	                <?
					foreach($all_btb_bank as $bank_id=>$bank_data)
					{
						foreach($bank_data as $com_id)
						{
							?>
	                        <td align="right" title="<? echo number_format(($sod_dfc_amt_arr[$bank_id][$com_id][81]),2);?>"><? echo number_format(($sod_dfc_amt_arr[$bank_id][$com_id][81]/$fig_lac),2);?></td>
	                       <?
							$com_tot_margin_sundry+=$sod_dfc_amt_arr[$bank_id][$com_id][81];
						}
						?>
                        <td align="right" title="<? echo number_format(($com_tot_margin_sundry),2);?>"><? echo number_format(($com_tot_margin_sundry/$fig_lac),2); $grp_com_tot_margin_sundry+=$com_tot_margin_sundry; $com_tot_margin_sundry=0;?></td>
                        <?
					}
					?>
                    <td align="right" title="<? echo number_format(($grp_com_tot_margin_sundry),2);?>"><? echo number_format(($grp_com_tot_margin_sundry/$fig_lac),2);?></td>
	            </tr>
                <tr bgcolor="#FFFFCC">
	                <td>Internal Loan</td>
	            	<td align="center">&nbsp;</td>
	                <?
					$count_cols=1;
					foreach($all_btb_bank as $bank_id=>$bank_data)
					{
						foreach($bank_data as $com_id)
						{
							?>
	                        <td align="right" title="<? echo number_format(($sod_dfc_amt_arr[$bank_id][$com_id][95]),2);?>"><? echo number_format(($sod_dfc_amt_arr[$bank_id][$com_id][95]/$fig_lac),2);?></td>
	                        <?
							$com_tot_margin_loan+=$sod_dfc_amt_arr[$bank_id][$com_id][95];
							$count_cols++;
						}
						?>
                        <td align="right" title="<? echo number_format(($com_tot_margin_loan),2);?>"><? echo number_format(($com_tot_margin_loan/$fig_lac),2); $grp_com_tot_margin_loan+=$com_tot_margin_loan; $com_tot_margin_loan=0;?></td>
                        <?
					}
					?>
                    <td align="right" title="<? echo number_format(($grp_com_tot_margin_loan),2);?>"><? echo number_format(($grp_com_tot_margin_loan/$fig_lac),2);?></td>
	            </tr>
                <?
				$net_retion=$grp_com_tot_asset-$grp_com_tot_liability;
				$net_retion_bdt=$net_retion*$txt_exchange_rate;
				?>
                <tr bgcolor="#E9F3FF">
	                <td colspan="<? echo $count_cols+2;?>" align="right">NET RETENTION  In USD LAKH :</td>
                    <td colspan="2" align="right" title="<? echo number_format($com_tot_asset,2)."=".$grp_com_tot_asset."=".$grp_com_tot_liability;?>"><? echo number_format(($net_retion/$fig_lac),2);?></td>
	            </tr>
                <tr bgcolor="#E9F3FF">
	                <td colspan="<? echo $count_cols+2;?>" align="right">NET RETENTION  In BDT LAKH :</td>
                    <td colspan="2" align="right" title="<? echo number_format($net_retion_bdt,2);?>"><? echo number_format(($net_retion_bdt/$fig_lac),2);?></td>
	            </tr>
	        </tbody>
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

if($action=="report_generate_bank_status")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_lein_bank=str_replace("'","",$cbo_lein_bank); 
	// $txt_exchange_rate=str_replace("'","",$txt_exchange_rate);

	$companyArr=return_library_array( "SELECT company_name,id from lib_company",'id','company_name');
	$bankArr=return_library_array( "SELECT bank_name,id from lib_bank",'id','bank_name');
	$buyerArr=return_library_array( "SELECT buyer_name,id from lib_buyer",'id','buyer_name');
	
	$sql_cond="";
	if($cbo_company_name) $sql_cond=" and a.beneficiary_name=$cbo_company_name";
	if($cbo_lein_bank) $sql_cond.=" and a.lien_bank=$cbo_lein_bank";
	
	$order_sql="SELECT a.id as LC_SC_ID, a.beneficiary_name as COMPANY_ID, a.buyer_name as BUYER_NAME, a.export_lc_no as LC_SC_NO, a.lc_value as LC_SC_VALUE, a.lien_bank as LIEN_BANK, sum(b.attached_value) as CONFIRM_ORDER, 0 as IS_LC_SC
	from com_export_lc a, com_export_lc_order_info b
	where a.id=b.com_export_lc_id and a.lien_bank > 0  and a.status_active=1 and b.status_active=1 $sql_cond
	group by a.id, a.beneficiary_name, a.buyer_name, a.export_lc_no, a.lc_value, a.lien_bank
	union all
	SELECT a.id as LC_SC_ID, a.beneficiary_name as COMPANY_ID, a.buyer_name as BUYER_NAME, a.contract_no as LC_SC_NO, a.contract_value as LC_SC_VALUE, a.lien_bank as LIEN_BANK, sum(b.attached_value) as CONFIRM_ORDER, 1 as IS_LC_SC
	from com_sales_contract a, com_sales_contract_order_info b
	where a.id=b.com_sales_contract_id and a.lien_bank > 0 and a.status_active=1 and b.status_active=1 $sql_cond
	group by a.id, a.beneficiary_name, a.buyer_name, a.contract_no, a.contract_value, a.lien_bank ";
	// echo $order_sql;die; 

	$order_sql_result=sql_select($order_sql);
	$all_data_arr=array();
	foreach($order_sql_result as $row)
	{
		$all_data_arr[$row["COMPANY_ID"]][$row["LIEN_BANK"]][$row["IS_LC_SC"]][$row["LC_SC_ID"]]["LC_SC_NO"]=$row["LC_SC_NO"];
		$all_data_arr[$row["COMPANY_ID"]][$row["LIEN_BANK"]][$row["IS_LC_SC"]][$row["LC_SC_ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
		$all_data_arr[$row["COMPANY_ID"]][$row["LIEN_BANK"]][$row["IS_LC_SC"]][$row["LC_SC_ID"]]["LC_SC_VALUE"]=$row["LC_SC_VALUE"];
		$all_data_arr[$row["COMPANY_ID"]][$row["LIEN_BANK"]][$row["IS_LC_SC"]][$row["LC_SC_ID"]]["CONFIRM_ORDER"]+=$row["CONFIRM_ORDER"];
	}
	unset($order_sql_result);

	$btb_sql="SELECT a.id as LC_SC_ID, a.beneficiary_name as COMPANY_ID, a.buyer_name as BUYER_NAME, a.export_lc_no as LC_SC_NO, a.lc_value as LC_SC_VALUE, a.lien_bank as LIEN_BANK, c.is_lc_sc as IS_LC_SC, d.id as BTB_ID, d.lc_value as BTB_VALUE
	from com_export_lc a, com_btb_export_lc_attachment c, com_btb_lc_master_details d
	where a.lien_bank > 0 and c.is_lc_sc=0 and a.id=c.lc_sc_id and c.import_mst_id=d.id and a.status_active=1  and c.status_active=1 and d.status_active=1 $sql_cond
	union all
	SELECT a.id as LC_SC_ID, a.beneficiary_name as COMPANY_ID, a.buyer_name as BUYER_NAME, a.contract_no as LC_SC_NO, a.contract_value as LC_SC_VALUE, a.lien_bank as LIEN_BANK, c.is_lc_sc as IS_LC_SC, d.id as BTB_ID, d.lc_value as BTB_VALUE
	from com_sales_contract a, com_btb_export_lc_attachment c, com_btb_lc_master_details d
	where  a.lien_bank > 0 and c.is_lc_sc=1 and a.id=c.lc_sc_id and c.import_mst_id = d.id and a.status_active=1 and c.status_active=1 and d.status_active=1 $sql_cond";
	// echo $btb_sql;die; 

	$btb_sql_result=sql_select($btb_sql);
	$arr_chk=array();
	foreach($btb_sql_result as $row)
	{
		$all_data_arr[$row["COMPANY_ID"]][$row["LIEN_BANK"]][$row["IS_LC_SC"]][$row["LC_SC_ID"]]["LC_SC_NO"]=$row["LC_SC_NO"];
		$all_data_arr[$row["COMPANY_ID"]][$row["LIEN_BANK"]][$row["IS_LC_SC"]][$row["LC_SC_ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
		$all_data_arr[$row["COMPANY_ID"]][$row["LIEN_BANK"]][$row["IS_LC_SC"]][$row["LC_SC_ID"]]["LC_SC_VALUE"]=$row["LC_SC_VALUE"];
		if($arr_chk[$row["IS_LC_SC"]][$row["LC_SC_ID"]][$row["BTB_ID"]]=="")
		{
			$arr_chk[$row["IS_LC_SC"]][$row["LC_SC_ID"]][$row["BTB_ID"]]=$row["BTB_ID"];
			$all_data_arr[$row["COMPANY_ID"]][$row["LIEN_BANK"]][$row["IS_LC_SC"]][$row["LC_SC_ID"]]["BTB_ID"].=$row["BTB_ID"].",";
			$all_data_arr[$row["COMPANY_ID"]][$row["LIEN_BANK"]][$row["IS_LC_SC"]][$row["LC_SC_ID"]]["BTB_VALUE"]+=$row["BTB_VALUE"];
		}
	}
	unset($btb_sql_result);
	unset($arr_chk);


	if($cbo_company_name) $sql_cond2=" and d.importer_id=$cbo_company_name";
	$import_sql="SELECT d.id as BTB_ID, d.lc_value as BTB_VALUE, d.payterm_id as PAYTERM_ID, e.id as INVOICE_DTLS_ID, e.current_acceptance_value as ACCEPTANCE_VALUE, f.retire_source as RETIRE_SOURCE, g.id as PAYMENT_ID, g.accepted_ammount as ACCEPTED_AMMOUNT, h.id as AT_SIGHT_PAYMENT_ID, h.accepted_ammount as ACCEPTED_AMMOUNT_AT_SIGHT
	from com_btb_lc_master_details d
	left join com_import_invoice_dtls e on d.id=e.btb_lc_id and e.status_active=1
	left join com_import_invoice_mst f on e.import_invoice_id=f.id and f.status_active=1
	left join com_import_payment g on g.lc_id=d.id and f.id=g.invoice_id and d.payterm_id<>1 and g.status_active=1
	left join com_import_payment_com h on h.lc_id=d.id and f.id=h.invoice_id and d.payterm_id=1 and h.status_active=1
	where d.status_active=1 $sql_cond2";
	// echo $import_sql;die; 

	$import_sql_result=sql_select($import_sql);
	$btb_data_arr=array();$arr_chk=$arr_chk2=$arr_chk3=array();
	foreach($import_sql_result as $row)
	{

		if($arr_chk[$row["BTB_ID"]][$row["INVOICE_DTLS_ID"]]=="")
		{
			$arr_chk[$row["BTB_ID"]][$row["INVOICE_DTLS_ID"]]=$row["INVOICE_DTLS_ID"];
			if($row["RETIRE_SOURCE"]==30)
			{
				$btb_data_arr[$row["BTB_ID"]]["EDF"]+=$row["ACCEPTANCE_VALUE"];
			}
			else
			{
				$btb_data_arr[$row["BTB_ID"]]["DEFERRED"]+=$row["ACCEPTANCE_VALUE"];
			}
		}

		if($row["AT_SIGHT_PAYMENT_ID"]!="" || $row["PAYMENT_ID"]!="")
		{
			if($row["PAYTERM_ID"]==1)
			{
				if($arr_chk2[$row["BTB_ID"]][$row["AT_SIGHT_PAYMENT_ID"]]=="")
				{
					$arr_chk2[$row["BTB_ID"]][$row["AT_SIGHT_PAYMENT_ID"]]=$row["AT_SIGHT_PAYMENT_ID"];
					$btb_data_arr[$row["BTB_ID"]]["PAYTERM_AMMOUNT"]+=$row["ACCEPTED_AMMOUNT_AT_SIGHT"];
					if($row["RETIRE_SOURCE"]==30)
					{
						$btb_data_arr[$row["BTB_ID"]]["PAID_EDF"]+=$row["ACCEPTED_AMMOUNT_AT_SIGHT"];
					}
					else
					{
						$btb_data_arr[$row["BTB_ID"]]["PAID_DEFERRED"]+=$row["ACCEPTED_AMMOUNT_AT_SIGHT"];
					}
				}
			}
			else
			{
				if($arr_chk3[$row["BTB_ID"]][$row["PAYMENT_ID"]]=="")
				{
					$arr_chk3[$row["BTB_ID"]][$row["PAYMENT_ID"]]=$row["PAYMENT_ID"];
					$btb_data_arr[$row["BTB_ID"]]["PAYTERM_AMMOUNT"]+=$row["ACCEPTED_AMMOUNT"];
					if($row["RETIRE_SOURCE"]==30)
					{
						$btb_data_arr[$row["BTB_ID"]]["PAID_EDF"]+=$row["ACCEPTED_AMMOUNT"];
					}
					else
					{
						$btb_data_arr[$row["BTB_ID"]]["PAID_DEFERRED"]+=$row["ACCEPTED_AMMOUNT"];
					}
				}
			}
		}
	}	
	unset($import_sql_result);
	unset($arr_chk);
	unset($arr_chk2);
	unset($arr_chk3);

	$export_sql="SELECT a.id as LC_SC_ID, a.beneficiary_name as COMPANY_ID, a.buyer_name as BUYER_NAME, a.export_lc_no as LC_SC_NO, a.lc_value as LC_SC_VALUE, a.lien_bank as LIEN_BANK, b.id as INVOICE_ID, b.invoice_value as INVOICE_VALUE, 0 as IS_LC_SC, c.id as DOC_DTLS_ID, c.net_invo_value as DOC_CURRENCY, f.id as RLZN_DTLS_ID, f.document_currency as RLZN_CURRENCY
	from com_export_lc a, com_export_invoice_ship_mst b
	left join com_export_doc_submission_invo c on c.is_converted=0 and b.id=invoice_id and b.lc_sc_id=b.lc_sc_id and c.is_lc=1 and c.status_active=1 and c.submission_dtls_id>0
	left join com_export_doc_submission_mst d on  c.doc_submission_mst_id=d.id and d.entry_form = 40 and d.status_active=1
	left join com_export_proceed_realization e on d.id=e.invoice_bill_id and e.is_invoice_bill=1 and e.status_active=1
	left join com_export_proceed_rlzn_dtls f on e.id=f.mst_id and f.status_active=1
	where a.id=b.lc_sc_id and a.lien_bank > 0 and b.is_lc=1 and a.status_active=1 and b.status_active=1 $sql_cond
	union all
	SELECT a.id as LC_SC_ID, a.beneficiary_name as COMPANY_ID, a.buyer_name as BUYER_NAME, a.contract_no as LC_SC_NO, a.contract_value as LC_SC_VALUE, a.lien_bank as LIEN_BANK, b.id as INVOICE_ID, b.invoice_value as INVOICE_VALUE, 1 as IS_LC_SC, c.id as DOC_DTLS_ID, c.net_invo_value as DOC_CURRENCY, f.id as RLZN_DTLS_ID, f.document_currency as RLZN_CURRENCY
	from com_sales_contract a, com_export_invoice_ship_mst b
	left join com_export_doc_submission_invo c on c.is_converted=0 and b.id=invoice_id and b.lc_sc_id=b.lc_sc_id and c.is_lc=2 and c.status_active=1 and c.submission_dtls_id>0
	left join com_export_doc_submission_mst d on  c.doc_submission_mst_id=d.id and d.entry_form = 40 and d.status_active=1
	left join com_export_proceed_realization e on d.id=e.invoice_bill_id and e.is_invoice_bill=1 and e.status_active=1
	left join com_export_proceed_rlzn_dtls f on e.id=f.mst_id and f.status_active=1
	where a.id=b.lc_sc_id and a.lien_bank > 0 and b.is_lc=2 and a.status_active=1 and b.status_active=1 $sql_cond";
	
	// echo $export_sql;die;

	$export_sql_result=sql_select($export_sql);
	$arr_chk=$arr_chk2=$arr_chk3=array();
	foreach($export_sql_result as $row)
	{
		$all_data_arr[$row["COMPANY_ID"]][$row["LIEN_BANK"]][$row["IS_LC_SC"]][$row["LC_SC_ID"]]["LC_SC_NO"]=$row["LC_SC_NO"];
		$all_data_arr[$row["COMPANY_ID"]][$row["LIEN_BANK"]][$row["IS_LC_SC"]][$row["LC_SC_ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
		$all_data_arr[$row["COMPANY_ID"]][$row["LIEN_BANK"]][$row["IS_LC_SC"]][$row["LC_SC_ID"]]["LC_SC_VALUE"]=$row["LC_SC_VALUE"];
		if($arr_chk[$row["IS_LC_SC"]][$row["LC_SC_ID"]][$row["INVOICE_ID"]]=="")
		{
			$arr_chk[$row["IS_LC_SC"]][$row["LC_SC_ID"]][$row["INVOICE_ID"]]=$row["INVOICE_ID"];
			$all_data_arr[$row["COMPANY_ID"]][$row["LIEN_BANK"]][$row["IS_LC_SC"]][$row["LC_SC_ID"]]["INVOICE_VALUE"]+=$row["INVOICE_VALUE"];
		}
		if($arr_chk2[$row["IS_LC_SC"]][$row["LC_SC_ID"]][$row["DOC_DTLS_ID"]]=="")
		{
			$arr_chk2[$row["IS_LC_SC"]][$row["LC_SC_ID"]][$row["DOC_DTLS_ID"]]=$row["DOC_DTLS_ID"];
			$all_data_arr[$row["COMPANY_ID"]][$row["LIEN_BANK"]][$row["IS_LC_SC"]][$row["LC_SC_ID"]]["DOC_CURRENCY"]+=$row["DOC_CURRENCY"];
		}
		if($arr_chk3[$row["IS_LC_SC"]][$row["LC_SC_ID"]][$row["RLZN_DTLS_ID"]]=="")
		{
			$arr_chk3[$row["IS_LC_SC"]][$row["LC_SC_ID"]][$row["RLZN_DTLS_ID"]]=$row["RLZN_DTLS_ID"];
			$all_data_arr[$row["COMPANY_ID"]][$row["LIEN_BANK"]][$row["IS_LC_SC"]][$row["LC_SC_ID"]]["RLZN_CURRENCY"]+=$row["RLZN_CURRENCY"];
		}
	}	
	unset($export_sql_result);
	unset($arr_chk);
	unset($arr_chk2);
	unset($arr_chk3);

	$tbl_width=1900;
	ob_start();
	?>
	<style>
		.right{text-align: right;}
		.wrd_brk{word-break: break-all;}
	</style>

	<div style="width:<? echo $tbl_width+20; ?>px;" id="scroll_body">
	<fieldset style="width:100%">
		<?
			$i=1;
			foreach($all_data_arr as $company_id=>$company_info)
			{
				?>
					<table width="<? echo $tbl_width; ?>" cellpadding="0" cellspacing="0"  rules="all" align="center">
						<thead>
							<tr>
								<th align="center" style="font-weight:bold; font-size:18px;"><?=$companyArr[$company_id];?></th>
							</tr>
							<tr>
								<th align="center"style="font-weight:bold; font-size:15px;">Buyer, Bank, Contract and Confirm Order wise Export and BTB Liability Payment Status</th>
							</tr>
						</thead>
					</table>
				<?
				foreach($company_info as $bank_id=>$bank_info)
				{
					$tot_contract_value=$tot_confirm_order=$tot_btb_value=$tot_paid_value=$tot_edf=$tot_deferred=$tot_btb_liability=$tot_btb_yet_to_open=$tot_export_value=$tot_doc_submitted=$tot_realized=$tot_unrealize=$tot_export_due=0;
					?>
						<table width="<? echo $tbl_width; ?>" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
							<thead>
								<tr>
									<th colspan="2"><?=$bankArr[$bank_id];?></th>
									<th rowspan="2" width="100">Contract Value ($)</th>
									<th rowspan="2" width="100">Confirm Order ($)</th>
									<th colspan="2">BTB Liability</th>
									<th colspan="7">BTB Payment & Liability</th>
									<th rowspan="2">BTB Yet To Open</th>
									<th colspan="5">Export Status</th>
								</tr>
								<tr>
									<th width="140">Buyer</th>
									<th width="200">Contract No</th>
									<th width="100">Value</th>
									<th width="50">%</th>
									<th width="100">Paid Value</th>
									<th width="50">%</th>
									<th width="100">EDF</th>
									<th width="50">%</th>
									<th width="100">Deferred/Balance</th>
									<th width="50">%</th>
									<th width="100">Total</th>
									<th width="100">Export Value</th>
									<th width="100">Doc Submitted</th>
									<th width="100">Realized</th>
									<th width="100">Unrealize</th>
									<th width="100">Export Due</th>
								</tr>
							</thead>
							<tbody>
								<?
								
								foreach($bank_info as $is_lc_sc_id=>$is_lc_sc_info)
								{
									foreach($is_lc_sc_info as $lc_sc_id=>$row)
									{
										if($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
										$btb_id_arr=array_unique(explode(",",chop($row["BTB_ID"],',')));
										$payment_amount=$edf_amount=$deferred_amount=0;
										foreach($btb_id_arr as $val)
										{
											$payment_amount+=$btb_data_arr[$val]["PAYTERM_AMMOUNT"];
											$edf_amount+=$btb_data_arr[$val]["EDF"]-$btb_data_arr[$val]["PAID_EDF"];
											// $deferred_amount+=$btb_data_arr[$val]["DEFERRED"]-$btb_data_arr[$val]["PAID_DEFERRED"];
											$deferred_amount=$row["BTB_VALUE"]-($payment_amount+$edf_amount);
										}			
										?>
										<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tr_<?=$i;?>','<?=$bgcolor;?>')" id="tr_<?=$i;?>">
											<td class="wrd_brk"><?=$buyerArr[$row["BUYER_NAME"]];?></td>
											<td class="wrd_brk"><?=$row["LC_SC_NO"];?></td>
											<td class="right"><?=number_format($row["LC_SC_VALUE"],2,".","");?></td>
											<td class="right"><?=number_format($row["CONFIRM_ORDER"],2,".","");?></td>
											<td class="right"><?=number_format($row["BTB_VALUE"],2,".","");?></td>
											<td class="right"><?=fn_number_format(($row["BTB_VALUE"]/$row["CONFIRM_ORDER"])*100,2,".","");?></td>
											<td class="right"><?=number_format($payment_amount,2,".","");?></td>
											<td class="right"><?=fn_number_format(($payment_amount/$row["CONFIRM_ORDER"])*100,2,".","");?></td>
											<td class="right"><?=number_format($edf_amount,2,".","");?></td>
											<td class="right"><?=fn_number_format(($edf_amount/$row["BTB_VALUE"])*100,2,".","");?></td>
											<td title="BTB Value-(Paid Value+EDF)"class="right"><?=number_format($deferred_amount,2,".","");?></td>
											<td class="right"><?=fn_number_format(($deferred_amount/$row["BTB_VALUE"])*100,2,".","");?></td>
											<td class="right"><?=number_format($edf_amount+$deferred_amount,2,".","");?></td>
											<td class="right"><?=number_format($row["CONFIRM_ORDER"]-$row["BTB_VALUE"],2,".","");?></td>
											<td class="right"><?=number_format($row["INVOICE_VALUE"],2,".","");?></td>
											<td class="right"><?=number_format($row["DOC_CURRENCY"],2,".","");?></td>
											<td class="right"><?=number_format($row["RLZN_CURRENCY"],2,".","");?></td>
											<td class="right"><?=number_format($row["DOC_CURRENCY"]-$row["RLZN_CURRENCY"],2,".","");?></td>
											<td class="right"><?=number_format($row["CONFIRM_ORDER"]-$row["INVOICE_VALUE"],2,".","");?></td>
										</tr>
										<?
										$i++;
										$tot_contract_value+= $row["LC_SC_VALUE"];
										$tot_confirm_order+=$row["CONFIRM_ORDER"];
										$tot_btb_value+=$row["BTB_VALUE"];
										$tot_paid_value+=$payment_amount;
										$tot_edf+=$edf_amount;
										$tot_deferred+=$deferred_amount;
										$tot_btb_liability+=$edf_amount+$deferred_amount;
										$tot_btb_yet_to_open+=$row["CONFIRM_ORDER"]-$row["BTB_VALUE"];
										$tot_export_value+=$row["INVOICE_VALUE"];
										$tot_doc_submitted+=$row["DOC_CURRENCY"];
										$tot_realized+=$row["RLZN_CURRENCY"];
										$tot_unrealize+=$row["DOC_CURRENCY"]-$row["RLZN_CURRENCY"];
										$tot_export_due+=$row["CONFIRM_ORDER"]-$row["INVOICE_VALUE"];
									}
								}
								?>               
							</tbody>
							<tfoot>
								<th></th>
								<th>Total </th>
								<th><?=number_format($tot_contract_value,2,".","");?></th>
								<th><?=number_format($tot_confirm_order,2,".","");?></th>
								<th><?=number_format($tot_btb_value,2,".","");?></th>
								<th></th>
								<th><?=number_format($tot_paid_value,2,".","");?></th>
								<th></th>
								<th><?=number_format($tot_edf,2,".","");?></th>
								<th></th>
								<th><?=number_format($tot_deferred,2,".","");?></th>
								<th></th>
								<th><?=number_format($tot_btb_liability,2,".","");?></th>
								<th><?=number_format($tot_btb_yet_to_open,2,".","");?></th>
								<th><?=number_format($tot_export_value,2,".","");?></th>
								<th><?=number_format($tot_doc_submitted,2,".","");?></th>
								<th><?=number_format($tot_realized,2,".","");?></th>
								<th><?=number_format($tot_unrealize,2,".","");?></th>
								<th><?=number_format($tot_export_due,2,".","");?></th>
							</tfoot>
						</table><br>
					<?
				}

			}
		?>
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
	
	$currency_convert_result=sql_select("select * from (select a.id, a.conversion_rate, max(id) over () as max_pk from currency_conversion_rate a  where a.status_active=1 and a.currency=2) where id=max_pk");
	$currency_convert_rate=$currency_convert_result[0][csf("conversion_rate")];
	$tk_btb_lc_arr=return_library_array( "select id from com_btb_lc_master_details where currency_id=1",'id','id');
	
	if($db_type==0)
	{
		$inv_sql="select a.id as btb_lc_id, b.bank_ref, c.current_acceptance_value as accep_value
		from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
		where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and CONVERT(a.lc_category, SIGNED)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id<>2";
	}
	else
	{
		$inv_sql="select a.id as btb_lc_id, b.bank_ref, c.current_acceptance_value as accep_value
		from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
		where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id<>2";
	}
	
	$inv_result=sql_select($inv_sql);
	$accp_data=array();
	foreach($inv_result as $row)
	{
		if($row[csf("bank_ref")]!='')
		{
			if($tk_btb_lc_arr[$row[csf("btb_lc_id")]])
			{
				$accp_data[$row[csf("btb_lc_id")]]+=$row[csf("accep_value")]/$currency_convert_rate;
			}
			else
			{
				$accp_data[$row[csf("btb_lc_id")]]+=$row[csf("accep_value")];
			}
			
		}
	}
	
	if($db_type==0)
	{
		$btb_sql="select a.id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 and a.ref_closing_status<>1 and CONVERT(a.lc_category, SIGNED)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id<>2 and a.payterm_id<>3 order by CONVERT(a.lc_category, SIGNED) ";
	}
	else
	{
		$btb_sql="select a.id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id<>2 and a.payterm_id<>3 order by to_number(a.lc_category) ";
	}
	
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
                <th width="130">Importer</th>
                <th width="70">LC Date</th>
                <th width="70">LC Expiry Date</th>
                <th>LC Amount (USD)</th>
            </thead>
            <tbody>
            <? 
			$i=1; $r=1;
			foreach($btb_sql_result as $row)  
			{
				if($tk_btb_lc_arr[$row[csf("id")]])
				{
					$row[csf("lc_value")]=$row[csf("lc_value")]/$currency_convert_rate;
				}
				
				$pendin_value =$row[csf("lc_value")]-$accp_data[$row[csf("id")]];
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
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center" title="<? echo $r; ?>"><? echo $i; ?></td>
                        <td><p><? echo $row[csf('lc_number')]; ?></p></td>
                        <td><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                        <td><p><? echo $bank_arr[$row[csf('issuing_bank_id')]]; ?>&nbsp;</p></td>
                        <td><p><? echo $company_arr[$row[csf('importer_id')]]; ?>&nbsp;</p></td>
                        <td align="center"><p><? if($row[csf('lc_date')] !="" && $row[csf('lc_date')] !="0000-00-00") echo change_date_format($row[csf('lc_date')]);  ?>&nbsp;</p></td>
                        <td align="center"><p><? if($row[csf('lc_expiry_date')] !="" && $row[csf('lc_expiry_date')] !="0000-00-00") echo change_date_format($row[csf('lc_expiry_date')]);  ?>&nbsp;</p></td>
                        <td align="right" title="<?= $pendin_value;?>"><? echo number_format($pendin_value,4);  ?></td>
                    </tr>
                    <?
                    $tot_pendin_value+=$pendin_value;
					$cat_pendin_value+=$pendin_value;
					$i++;
				}
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td colspan="7" align="right">Currency Wise Product Total:</td>
                <td align="right"><? echo number_format($cat_pendin_value,4);  ?></td>
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
	
	
	$currency_convert_result=sql_select("select * from (select a.id, a.conversion_rate, max(id) over () as max_pk from currency_conversion_rate a  where a.status_active=1 and a.currency=2) where id=max_pk");
	$currency_convert_rate=$currency_convert_result[0][csf("conversion_rate")];
	$tk_btb_lc_arr=return_library_array( "select id from com_btb_lc_master_details where currency_id=1",'id','id');
	
	
	$sql_cond_payment="";
	if($company_id>0) $sql_cond_payment=" and a.company_id=$company_id";
	$sql_payment="select a.lc_id, b.invoice_id, b.accepted_ammount 
	from com_import_payment b left join com_import_payment_mst a on b.mst_id=a.id and a.status_active=1 and a.is_deleted=0 $sql_cond_payment
	 where b.status_active=1 and b.is_deleted=0";
	$sql_payment_result=sql_select($sql_payment);
	$lc_wise_payment=array();$invoice_wise_payment=array();
	foreach($sql_payment_result as $row)
	{
		if($tk_btb_lc_arr[$row[csf("lc_id")]])
		{
			$invoice_wise_payment[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")]/$currency_convert_rate;
		}
		else
		{
			$invoice_wise_payment[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")];
		}
	}
	
	
	if($db_type==0)
	{
		$btb_sql="select a.id as btb_lc_id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.payterm_id, a.lc_value, b.id as inv_id, b.invoice_no, b.maturity_date, b.edf_paid_date, sum(c.current_acceptance_value) as accep_value
		from com_btb_lc_master_details a, com_import_invoice_dtls c, com_import_invoice_mst b 
		where a.id=c.btb_lc_id and c.import_invoice_id=b.id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.is_deleted=0 and a.status_active=1  and a.ref_closing_status<>1 and CONVERT(a.lc_category, SIGNED)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id=2
		group by a.id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.payterm_id, a.lc_value, b.id, b.invoice_no, b.maturity_date, b.edf_paid_date
		order by a.lc_category, a.id";
	}
	else
	{
		$btb_sql="select a.id as btb_lc_id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.payterm_id, a.lc_value, b.id as inv_id, b.invoice_no, b.maturity_date, b.edf_paid_date, sum(c.current_acceptance_value) as accep_value
		from com_btb_lc_master_details a, com_import_invoice_dtls c, com_import_invoice_mst b 
		where a.id=c.btb_lc_id and c.import_invoice_id=b.id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.is_deleted=0 and a.status_active=1  and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id=2
		group by a.id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.payterm_id, a.lc_value, b.id, b.invoice_no, b.maturity_date, b.edf_paid_date
		order by a.lc_category, a.id";
	}
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
                <th width="90">LC Amount (USD)</th>
                <th width="90">Accpt. Amount (USD)</th>
                <th width="90">Margin Amount (USD)</th>
                <th width="90">Net Due (USD)</th>
                <th width="90">Net Due (USD)</th>
                <th width="70">Maturity Date</th>
                <th>Supplier</th>
            </thead>
            <tbody>
            <? 
			$i=1; $r=1;
			foreach($btb_sql_result as $row)  
			{
				if($tk_btb_lc_arr[$row[csf("btb_lc_id")]])
				{
					$row[csf("lc_value")]=$row[csf("lc_value")]/$currency_convert_rate;
					$row[csf("accep_value")]=$row[csf("accep_value")]/$currency_convert_rate;
				}
				
				$margine_amt=0;
				//$pendin_value =$row[csf("lc_value")]-$accp_data[$row[csf("id")]];
				$payment_value=0;
				if($row[csf("payterm_id")]==1)
				{
					if($row[csf("edf_paid_date")]!="" && $row[csf("edf_paid_date")]!="0000-00-00")
					{
						$payment_value=$invoice_wise_payment[$row[csf("inv_id")]];
					}
				}
				else
				{
					$payment_value=$invoice_wise_payment[$row[csf("inv_id")]];
				}
				$pendin_value =$row[csf("accep_value")]-$payment_value;
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
                        <td title="<? echo $row[csf("payterm_id")]."=".$payment_value."=".$row[csf("inv_id")];?>"><p><? echo $row[csf('lc_number')]; ?></p></td>
                        <td><p><? echo $row[csf('invoice_no')]; ?></p></td>
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
	
	$currency_convert_result=sql_select("select * from (select a.id, a.conversion_rate, max(id) over () as max_pk from currency_conversion_rate a  where a.status_active=1 and a.currency=2) where id=max_pk");
	$currency_convert_rate=$currency_convert_result[0][csf("conversion_rate")];
	$tk_btb_lc_arr=return_library_array( "select id from com_btb_lc_master_details where currency_id=1",'id','id');
	
	$sql_payment="select a.lc_id, b.invoice_id, b.accepted_ammount 
	from com_import_payment b left join com_import_payment_mst a on b.mst_id=a.id and a.status_active=1  $sql_cond_payment  
	where b.status_active=1 and b.is_deleted=0";
	//echo $sql_payment;
	$sql_payment_result=sql_select($sql_payment);
	$lc_wise_payment=array();$invoice_wise_payment=array();
	foreach($sql_payment_result as $row)
	{
		if($tk_btb_lc_arr[$row[csf("lc_id")]])
		{
			$invoice_wise_payment[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")]/$currency_convert_rate;
		}
		else
		{
			$invoice_wise_payment[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")];
		}
	}
	//echo $invoice_wise_payment[322];die;
	if($type==2) $retire_source_cond=" and b.retire_source not in(30,31)"; else $retire_source_cond=" and b.retire_source in(30,31)";
	if($db_type==0)
	{
		$ifdbc_edf_sql="select a.id as btb_lc_id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value, a.maturity_from_id, a.payterm_id, b.id as import_inv_id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, b.loan_ref, sum(c.current_acceptance_value) as edf_loan_value, 1 as type 
		from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
		where a.id=c.btb_lc_id and c.import_invoice_id=b.id  and a.lc_category>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and CONVERT(a.lc_category, SIGNED)>0 and a.payterm_id<>3 and a.lc_type_id<>2 $retire_source_cond
		group by a.id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value, a.maturity_from_id, a.payterm_id, b.id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, b.loan_ref
		order by CONVERT(a.lc_category, SIGNED),btb_lc_id";
	}
	else
	{
		$ifdbc_edf_sql="select a.id as btb_lc_id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value, a.maturity_from_id, a.payterm_id, b.id as import_inv_id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, b.loan_ref, sum(c.current_acceptance_value) as edf_loan_value, 1 as type 
		from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
		where a.id=c.btb_lc_id and c.import_invoice_id=b.id  and a.lc_category>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and to_number(a.lc_category)>0 and a.payterm_id<>3 and a.lc_type_id<>2 $retire_source_cond
		group by a.id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value, a.maturity_from_id, a.payterm_id, b.id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, b.loan_ref
		order by to_number(a.lc_category), btb_lc_id";
	}
	//to_number(a.lc_category),
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
                <th width="100">Cumulative Balance (USD)</th>
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
			$invoice_payment_atsite=return_library_array( "select invoice_id, sum(accepted_ammount) as accepted_ammount from com_import_payment_com where status_active=1 and is_deleted=0 group by invoice_id",'invoice_id','accepted_ammount');
			foreach($ifdbc_edf_sql_result as $row)  
			{
				if($tk_btb_lc_arr[$row[csf("btb_lc_id")]])
				{
					$row[csf('lc_value')]=$row[csf('lc_value')]/$currency_convert_rate;
					$row[csf('edf_loan_value')]=$row[csf('edf_loan_value')]/$currency_convert_rate;
				}
				
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
						if($row[csf("payterm_id")]==2)
						{
							$paid_value=$invoice_wise_payment[$row[csf("import_inv_id")]];
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
						
						/*if($row[csf("edf_paid_date")] != "" && $row[csf("edf_paid_date")] != "0000-00-00")
						{
							$paid_value=$row[csf("edf_loan_value")];
						}*/
						//$paid_value=$invoice_payment_atsite[$row[csf("import_inv_id")]];
					}
					$pending_value=$row[csf("edf_loan_value")]-$paid_value;
				}
				//if($row[csf('lc_number')]=="802180514423") echo $paid_value.test;
				$maturity_date="";
				if($row[csf("maturity_from_id")]==1) $maturity_date=$row[csf("bank_acc_date")];
				else if($row[csf("maturity_from_id")]==2 || $row[csf("maturity_from_id")]==5) $maturity_date=$row[csf("shipment_date")];
				else if($row[csf("maturity_from_id")]==3) $maturity_date=$row[csf("nagotiate_date")];
				else if($row[csf("maturity_from_id")]==4) $maturity_date=$row[csf("bill_date")];
				else $maturity_date="";
				 
				if($cat_check[$row[csf("lc_category")]*1]=="" && number_format($pending_value,2)>0 && $maturity_date!="" && $maturity_date!="0000-00-00")
				{
					$cat_check[$row[csf("lc_category")]*1]=$row[csf("lc_category")]*1;
					if($type==3){ $tot_col_span=9;  $col_span=3;} else { $tot_col_span=8; $col_span=2;}
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
                            <td align="right"><? echo number_format($cat_cumilitive_balance,2);  ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
						</tr>
						<tr bgcolor="#FFFFCC"><td colspan="<? echo $tot_col_span;?>" title="<? echo $row[csf("lc_category")]; ?>"><? echo $supply_source[$row[csf("lc_category")]*1]; ?></td></tr>
						<?
					}
					$cat_lc_value=$cat_pendin_value=0;$r++;
				}
				
				
				if(number_format($pending_value,2)>0 && $maturity_date!="" && $maturity_date!="0000-00-00")
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if($btb_check[$row[csf('lc_number')]]=="")
					{
						$btb_check[$row[csf('lc_number')]]=$row[csf('lc_number')];
						$tot_lc_value+=$row[csf('lc_value')];
						$cat_lc_value+=$row[csf('lc_value')];
						$cumilitive_balance=$row[csf('lc_value')]-$pending_value;
						$tot_cumilitive_balance+=$cumilitive_balance;
						$cat_cumilitive_balance+=$cumilitive_balance;
					}
					else
					{
						$cumilitive_balance=$cumilitive_balance-$pending_value;
						$tot_cumilitive_balance+=$cumilitive_balance;
						$cat_cumilitive_balance+=$cumilitive_balance;
					}
					if($row[csf('lc_number')]=='072822050019')
					{
						echo  number_format($pending_value,2)."=".$pending_value."=".$maturity_date;
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center" title="<? echo $r; ?>"><? echo $i; ?></td>
						<td><p><? echo $row[csf('lc_number')]; ?></p></td>
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
                        <td align="right"><? echo number_format($cumilitive_balance,2);  ?></td>
                        <td align="center"><p><? echo change_date_format($maturity_date);  ?>&nbsp;</p></td>
                        <td align="center"><p><? if($row[csf('maturity_date')] !="" && $row[csf('maturity_date')] !="0000-00-00") echo change_date_format($row[csf('maturity_date')]);  ?>&nbsp;</p></td>
                        <td align="center"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
					</tr>
					<?
					
					$tot_pendin_value+=$pending_value;
					$cat_pendin_value+=$pending_value;
					$test_data.=$pending_value.",";
					$i++;
				}
			}
			
			?>
            <tr bgcolor="#CCCCCC">
                <td colspan="<? echo $col_span;?>" align="right" title="<? echo $row[csf("lc_category")]; ?>">Currency Wise Product Total:</td>
                <td align="right"><? echo number_format($cat_lc_value,2);  ?></td>
                <td align="right"><? echo number_format($cat_pendin_value,2);  ?></td>
                <td align="right"><? echo number_format($cat_cumilitive_balance,2);  ?></td>
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
                    <td align="right" id="value_tot_pendin_value"><? echo number_format($tot_cumilitive_balance,2); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
	//echo $test_data;
	//"<pre>";print_r($test_data);die;
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
		$invoice_sql="select b.doc_submission_mst_id, group_concat(a.invoice_no) as invoice_no, sum(a.invoice_quantity) as invoice_quantity 
		from com_export_invoice_ship_mst a, com_export_doc_submission_invo b, com_export_doc_submission_mst c 
		where a.id=b.invoice_id and b.doc_submission_mst_id=c.id and c.entry_form = 40 and c.lien_bank > 0 and c.submit_type=2 and c.company_id=$company_id and c.lien_bank=$bank_id and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.doc_submission_mst_id";
	}
	else
	{
		$invoice_sql="select b.doc_submission_mst_id, listagg(cast(a.invoice_no as varchar(4000)),',') within group (order by a.invoice_no) as invoice_no, sum(a.invoice_quantity) as invoice_quantity 
		from com_export_invoice_ship_mst a, com_export_doc_submission_invo b, com_export_doc_submission_mst c 
		where a.id=b.invoice_id and b.doc_submission_mst_id=c.id and c.entry_form = 40 and c.lien_bank > 0 and c.submit_type=2 and c.company_id=$company_id and c.lien_bank=$bank_id and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.doc_submission_mst_id";
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

if($action=="docs_in_hand_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	$bill_com_cond="";
	if($com_id>0) $bill_com_cond=" and b.company_id=$com_id";
	if($bank_id>0) $bill_bank_cond=" and b.lien_bank=$bank_id";
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
	if($com_id>0) $com_ship_cond=" and b.benificiary_id=$com_id";
	if($bank_id>0) $bank_ship_cond=" and a.lien_bank=$bank_id";
	
	if($db_type==0)
	{
		$ex_data_cond=" and b.ex_factory_date!='0000-00-00'";
	}
	else
	{
		$ex_data_cond=" and b.ex_factory_date is not null";
	}
	
	$inv_buyer_sql="select a.buyer_name, a.lien_bank, a.contract_no as lc_sc_no, a.pay_term, a.contract_value as lc_sc_value, b.benificiary_id, b.id as inv_id, b.net_invo_value, b.invoice_no, b.invoice_date, b.invoice_quantity, b.invoice_value, b.ex_factory_date, d.submit_date 
	from com_sales_contract a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c, com_export_doc_submission_mst d 
	where a.id=b.lc_sc_id and b.id=c.invoice_id and c.doc_submission_mst_id=d.id and d.entry_form = 39 and b.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $com_ship_cond $bank_ship_cond $ex_data_cond";
	//echo $inv_buyer_sql;die;
	$inv_buyer_result=sql_select($inv_buyer_sql);
	$inv_buyer_data=array();
	foreach($inv_buyer_result as $row)
	{
		$inv_rate=$row[csf("net_invo_value")]/$row[csf("invoice_quantity")];
		$pending_value=$row[csf("net_invo_value")]-$inv_sub_arr[$row[csf("inv_id")]];
		if($pending_value>0)
		{
			$buyer_submit_inv[$row[csf("inv_id")]]=$row[csf("inv_id")];
			$pending_qnty=$pending_value/$inv_rate;
			$inv_buyer_data[$row[csf("inv_id")]]["buyer_name"]=$row[csf("buyer_name")];
			$inv_buyer_data[$row[csf("inv_id")]]["lien_bank"]=$row[csf("lien_bank")];
			$inv_buyer_data[$row[csf("inv_id")]]["lc_sc_no"]=$row[csf("lc_sc_no")];
			$inv_buyer_data[$row[csf("inv_id")]]["pay_term"]=$row[csf("pay_term")];
			$inv_buyer_data[$row[csf("inv_id")]]["lc_sc_value"]=$row[csf("lc_sc_value")];
			$inv_buyer_data[$row[csf("inv_id")]]["benificiary_id"]=$row[csf("benificiary_id")];
			$inv_buyer_data[$row[csf("inv_id")]]["invoice_no"]=$row[csf("invoice_no")];
			$inv_buyer_data[$row[csf("inv_id")]]["invoice_date"]=$row[csf("invoice_date")];
			$inv_buyer_data[$row[csf("inv_id")]]["invoice_no"]=$row[csf("invoice_no")];
			$inv_buyer_data[$row[csf("inv_id")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
			if($row[csf("ex_factory_date")]!="" && $row[csf("ex_factory_date")]!="0000-00-00")
			{
				$inv_buyer_data[$row[csf("inv_id")]]["maturity_date"]=date('d-m-Y',(strtotime($row[csf("ex_factory_date")])+(86400*45)));
			}
			else
			{
				$inv_buyer_data[$row[csf("inv_id")]]["maturity_date"]="";
			}
			
			$inv_buyer_data[$row[csf("inv_id")]]["aging_days"]=((strtotime(date('d-m-Y'))-strtotime($row[csf("submit_date")]))/86400);
			$inv_buyer_data[$row[csf("inv_id")]]["pending_qnty"]=$pending_qnty;
			$inv_buyer_data[$row[csf("inv_id")]]["pending_value"]=$pending_value;
		}
	}
	unset($inv_buyer_result);
	//echo "<pre>";print_r($inv_buyer_data);die;
	$inv_sql=" select a.buyer_name, a.lien_bank, a.export_lc_no as lc_sc_no, a.pay_term, a.lc_value as lc_sc_value, a.tenor, b.benificiary_id, b.id as inv_id, b.net_invo_value, b.invoice_no, b.invoice_date, b.invoice_quantity, b.invoice_value, b.ex_factory_date, b.is_lc
	from com_export_lc a, com_export_invoice_ship_mst b 
	where a.id=b.lc_sc_id and b.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $com_ship_cond $bank_ship_cond $ex_data_cond
	union all
	select a.buyer_name, a.lien_bank, a.contract_no as lc_sc_no, a.pay_term, a.contract_value as lc_sc_value, a.tenor, b.benificiary_id, b.id as inv_id, b.net_invo_value, b.invoice_no, b.invoice_date, b.invoice_quantity, b.invoice_value, b.ex_factory_date, b.is_lc 
	from com_sales_contract a, com_export_invoice_ship_mst b 
	where a.id=b.lc_sc_id and b.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $com_ship_cond $bank_ship_cond $ex_data_cond";
	//echo $inv_sql;//die;
	$inv_sql_result=sql_select($inv_sql);
	$inv_data=array();
	foreach($inv_sql_result as $row)
	{
		$inv_rate=$row[csf("net_invo_value")]/$row[csf("invoice_quantity")];
		$pending_value=$row[csf("net_invo_value")]-$inv_sub_arr[$row[csf("inv_id")]];
		if($pending_value>0 && $buyer_submit_inv[$row[csf("inv_id")]] =="")
		{
			$pending_qnty=$pending_value/$inv_rate;
			$inv_data[$row[csf("inv_id")]]["buyer_name"]=$row[csf("buyer_name")];
			$inv_data[$row[csf("inv_id")]]["lien_bank"]=$row[csf("lien_bank")];
			$inv_data[$row[csf("inv_id")]]["lc_sc_no"]=$row[csf("lc_sc_no")];
			$inv_data[$row[csf("inv_id")]]["pay_term"]=$row[csf("pay_term")];
			$inv_data[$row[csf("inv_id")]]["lc_sc_value"]=$row[csf("lc_sc_value")];
			$inv_data[$row[csf("inv_id")]]["benificiary_id"]=$row[csf("benificiary_id")];
			$inv_data[$row[csf("inv_id")]]["invoice_no"]=$row[csf("invoice_no")];
			$inv_data[$row[csf("inv_id")]]["invoice_date"]=$row[csf("invoice_date")];
			$inv_data[$row[csf("inv_id")]]["invoice_no"]=$row[csf("invoice_no")];
			$inv_data[$row[csf("inv_id")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
			$inv_data[$row[csf("inv_id")]]["is_lc"]=$row[csf("is_lc")];
			$inv_data[$row[csf("inv_id")]]["maturity_date_cal"]=$row[csf("ex_factory_date")]."__".$row[csf("tenor")]."__".$row[csf("is_lc")];
			if($row[csf("ex_factory_date")]!="" && $row[csf("ex_factory_date")]!="0000-00-00")
			{
				if($row[csf("is_lc")]==1)
				{
					$tenor_priod=0;
					if($row[csf("tenor")]!="" && $row[csf("tenor")]>0) $tenor_priod=86400*$row[csf("tenor")];
					$maturity_date=strtotime($row[csf("ex_factory_date")])+$tenor_priod+(86400*10);
					$inv_data[$row[csf("inv_id")]]["maturity_date"]=date('d-m-Y',$maturity_date);
				}
				else
				{
					$inv_data[$row[csf("inv_id")]]["maturity_date"]=date('d-m-Y',(strtotime($row[csf("ex_factory_date")])+(86400*45)));
				}
			}
			else
			{
				$inv_data[$row[csf("inv_id")]]["maturity_date"]="";
			}
			
			$inv_data[$row[csf("inv_id")]]["aging_days"]=((strtotime(date('d-m-Y'))-strtotime($row[csf("ex_factory_date")]))/86400);
			$inv_data[$row[csf("inv_id")]]["pending_qnty"]=$pending_qnty;
			$inv_data[$row[csf("inv_id")]]["pending_value"]=$pending_value;
		}
	}
	unset($inv_buyer_result);
	//echo "<pre>";print_r($inv_data);die;
	
	//$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	//$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
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
    <div id="report_container" align="center" style="width:1180px">
	<fieldset style="width:1180px;">
        <table class="rpt_table" border="1" rules="all" width="1180" cellpadding="0" cellspacing="0">
            <thead>
                <th width="30">SL</th>
                <th width="120">Buyer</th>
                <th width="120">Invoice No</th>
                <th width="70">Invoice Date</th>
                <th width="90">Invoice Qty.</th>
                <th width="90">Invoice Value</th>
                <th width="50">LC/SC</th>
                <th width="80">Pay Terms</th>
                <th width="120">Lc/SC No</th>
                <th width="90">Lc/SC Value</th>
                <th width="70">Ex-factory Date</th>
                <th width="70">Maturity Date</th>
                <th width="70">Ageing Days</th>
                <th>Remarks</th>
            </thead>
            <tbody>
                <tr bgcolor="#66FFCC"><td colspan="14" style="font-weight:bold; font-size:14px;">Submission To Buyer / Shipping (Copy Docs) :</td></tr>
                <? 
                $i=1; $r=1;
                foreach($inv_buyer_data as $inv_id=>$val)  
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td><p><? echo $buyer_arr[$val['buyer_name']]; ?>&nbsp;</p></td>
                        <td><p><? echo $val['invoice_no']; ?>&nbsp;</p></td>
                        <td align="center"><p><? echo change_date_format($val['invoice_date']); ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($val['pending_qnty'],2); ?></td>

                        <td align="right"><? echo number_format($val['pending_value'],2); ?></td>
                        <td align="center"><p>SC</p></td>
                        <td align="center"><p><? echo $pay_term[$val['pay_term']]; ?>&nbsp;</p></td>
                        <td><p><? echo $val['lc_sc_no']; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($val['lc_sc_value'],2); ?></td>
                        <td align="center"><p><? if($val['ex_factory_date'] !="" && $val['ex_factory_date'] !="0000-00-00") echo change_date_format($val['ex_factory_date']); ?>&nbsp;</p></td>
                        <td align="center" ><p><? if($val['maturity_date']!="" && $val['maturity_date']!="0000-00-00") echo change_date_format($val['maturity_date']); ?>&nbsp;</p></td>
                        <td align="center"><p><? echo $val['aging_days']; ?>&nbsp;</p></td>
                        <td><p><? echo $val['remarks']; ?>&nbsp;</p></td>
                    </tr>
                    <?
                    $tot_pending_qnty+=$val['pending_qnty'];
                    $tot_pending_value+=$val['pending_value'];
					$gt_pending_qnty+=$val['pending_qnty'];
                    $gt_pending_value+=$val['pending_value'];
                    $i++;$r++;
                }
                ?>
                <tr bgcolor="#FFFFCC">
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total</td>
                    <td align="right"><? echo number_format($tot_pending_qnty,2);?></td>
                    <td align="right"><? echo number_format($tot_pending_value,2);?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr bgcolor="#66FFCC"><td colspan="14" style="font-weight:bold; font-size:14px;">Un Submitted Invoice :</td></tr>
                <? 
				$tot_pending_qnty=$tot_pending_value=0;
                $r=1;
                foreach($inv_data as $inv_id=>$val)  
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center"><? echo $r; ?></td>
                        <td><p><? echo $buyer_arr[$val['buyer_name']]; ?>&nbsp;</p></td>
                        <td><p><? echo $val['invoice_no']; ?>&nbsp;</p></td>
                        <td align="center"><p><? echo change_date_format($val['invoice_date']); ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($val['pending_qnty'],2); ?></td>
                        <td align="right"><? echo number_format($val['pending_value'],2); ?></td>
                        <td align="center"><p><? if($val['is_lc']==1) echo "LC"; else echo "SC";?></p></td>
                        <td align="center"><p><? echo $pay_term[$val['pay_term']]; ?>&nbsp;</p></td>
                        <td><p><? echo $val['lc_sc_no']; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($val['lc_sc_value'],2); ?></td>
                        <td align="center"><p><? if($val['ex_factory_date'] !="" && $val['ex_factory_date'] !="0000-00-00") echo change_date_format($val['ex_factory_date']); ?>&nbsp;</p></td>
                        <td align="center" title="<? echo $val['maturity_date_cal'];?>" ><p><? if($val['maturity_date']!="" && $val['maturity_date']!="0000-00-00") echo change_date_format($val['maturity_date']); ?>&nbsp;</p></td>
                        <td align="center"><p><? echo $val['aging_days']; ?>&nbsp;</p></td>
                        <td><p><? echo $val['remarks']; ?>&nbsp;</p></td>
                    </tr>
                    <?
                    $tot_pending_qnty+=$val['pending_qnty'];
                    $tot_pending_value+=$val['pending_value'];
					$gt_pending_qnty+=$val['pending_qnty'];
                    $gt_pending_value+=$val['pending_value'];
                    $i++;$r++;
                }
                ?>
                <tr bgcolor="#FFFFCC">
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total</td>
                    <td align="right"><? echo number_format($tot_pending_qnty,2);?></td>
                    <td align="right"><? echo number_format($tot_pending_value,2);?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr bgcolor="#CCCCCC">
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total</td>
                    <td align="right"><? echo number_format($gt_pending_qnty,2);?></td>
                    <td align="right"><? echo number_format($gt_pending_value,2);?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        	<!--<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total:</td>
                    <td align="right" id="value_tot_pendin_value"><?// echo number_format($tot_net_due_usd,2); ?></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>-->
        </table>
    </fieldset>
    </div>
    <?
}

if($action=="docs_forcast_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	//echo $com_id;die;

	$bank_id=$data_ref[1];
	$bill_com_cond="";
	if($com_id>0) $bill_com_cond=" and b.company_id=$com_id";
	if($bank_id>0) $bill_bank_cond=" and b.lien_bank=$bank_id";
	$bill_coll_sql="select a.invoice_id, sum(a.net_invo_value) as bill_value 
	from com_export_doc_submission_invo a, com_export_doc_submission_mst b 
	where a.doc_submission_mst_id=b.id and b.entry_form = 40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond 
	group by a.invoice_id";
	$bill_coll_sql_result=sql_select($bill_coll_sql);
	foreach($bill_coll_sql_result as $row)
	{
		$inv_sub_arr[$row[csf("invoice_id")]]=$row[csf("bill_value")];
	}
	unset($bill_coll_sql_result);
	if($com_id>0) $com_ship_cond=" and b.benificiary_id=$com_id";
	if($bank_id>0) $bank_ship_cond=" and a.lien_bank=$bank_id";
	
	//echo "<pre>";print_r($inv_buyer_data);die;
	$inv_sql=" select a.lien_bank, a.tenor, b.id as inv_id, b.benificiary_id, b.location_id, b.net_invo_value, b.invoice_quantity, b.invoice_value, b.ex_factory_date, b.is_lc
	from com_export_lc a, com_export_invoice_ship_mst b 
	where a.id=b.lc_sc_id and b.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.lien_bank>0 and b.location_id>0 $com_ship_cond $bank_ship_cond and b.ex_factory_date is not null
	union all
	select a.lien_bank, a.tenor, b.id as inv_id, b.benificiary_id, b.location_id, b.net_invo_value, b.invoice_quantity, b.invoice_value, b.ex_factory_date, b.is_lc 
	from com_sales_contract a, com_export_invoice_ship_mst b 
	where a.id=b.lc_sc_id and b.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.lien_bank>0 and b.location_id>0 $com_ship_cond $bank_ship_cond and b.ex_factory_date is not null
	order by ex_factory_date";
	//echo $inv_sql;//die;
	$inv_sql_result=sql_select($inv_sql);
	$forcast_data=$bank_com_location_data=$maturity_fortnightly_arr=array();
	foreach($inv_sql_result as $row)
	{
		$inv_rate=$row[csf("net_invo_value")]/$row[csf("invoice_quantity")];
		$pending_value=$row[csf("net_invo_value")]-$inv_sub_arr[$row[csf("inv_id")]];
		if($pending_value>0)
		{
			$pending_qnty=$pending_value/$inv_rate;
			$bank_com_location_data[$row[csf("lien_bank")]][$row[csf("benificiary_id")]][$row[csf("location_id")]]=$row[csf("lien_bank")]."*".$row[csf("benificiary_id")]."*".$row[csf("location_id")];
			if($row[csf("is_lc")]==1)
			{
				$tenor_priod=0;
				if($row[csf("tenor")]!="" && $row[csf("tenor")]>0) $tenor_priod=86400*$row[csf("tenor")];
				$maturity_date=date('d-m-Y',strtotime($row[csf("ex_factory_date")])+$tenor_priod+(86400*10));
			}
			else
			{
				$maturity_date=date('d-m-Y',(strtotime($row[csf("ex_factory_date")])+(86400*45)));
			}
			$maturi_fortnight=get_day_forthnightly($maturity_date);
			//###### for fortnight day ascending arr index once more
			$maturity_fortnightly_arr[strtotime(date('M-Y',strtotime($maturity_date)))][$maturi_fortnight]=$maturi_fortnight;
			$forcast_data[$row[csf("lien_bank")]][$row[csf("benificiary_id")]][$row[csf("location_id")]][$maturi_fortnight]+=$pending_qnty;
		}
	}
	unset($inv_buyer_result);
	$bank_colspan=$com_colspan=array();
	$tot_col=0;
	foreach($bank_com_location_data as $bank_id=>$bank_val)
	{
		foreach($bank_val as $com_id=>$com_data)
		{
			foreach($com_data as $location_id=>$location_data)
			{
				$bank_colspan[$bank_id]++;
				$com_colspan[$bank_id][$com_id]++;
				$tot_col++;
			}
		}
	}
	ksort($maturity_fortnightly_arr);
	//echo "<pre>";print_r($maturity_fortnightly_arr);die;
	//echo "<pre>";print_r($forcast_data);die;
	//echo "<pre>";print_r($bank_colspan);
	//echo "<pre>";print_r($com_colspan);
	//echo $tot_col;
	//die;
	
	//$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	$location_arr = return_library_array("select id, location_name from lib_location","id","location_name");
	$tbl_width=250+(100*$tot_col);
	$div_width=$tbl_width+20;
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
    <div id="report_container" align="center" style="width:<? echo $div_width;?>px">
	<fieldset style="width:<? echo $div_width;?>px;">
        <table class="rpt_table" border="1" rules="all" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
            <thead>
            	<tr>
                    <th width="150" rowspan="2">Bill To Be Receive</th>
                    <?
                    foreach($bank_com_location_data as $bank_id=>$bank_val)
                    {
                        ?>
                        <th colspan="<? echo $bank_colspan[$bank_id];?>"><? echo $bank_arr[$bank_id];?></th>
                        <?
                    }
                    ?>
                    <th width="100" rowspan="3">Grand Total</th>
                </tr>
                <tr>
                    <?
                    foreach($bank_com_location_data as $bank_id=>$bank_val)
                    {
						foreach($bank_val as $com_id=>$com_data)
						{
							?>
							<th colspan="<? echo $com_colspan[$bank_id][$com_id];?>"><? echo $company_arr[$com_id];?></th>
							<?
						}
                    }
                    ?>
                </tr>
                <tr>
                	<th>Name Of Month</th>
                    <?
                    foreach($bank_com_location_data as $bank_id=>$bank_val)
                    {
						foreach($bank_val as $com_id=>$com_data)
						{
							foreach($com_data as $location_id=>$location_data)
							{
								?>
                                <th width="100" title="<? echo $location_id;?>"><? echo $location_arr[$location_id];?></th>
                                <?
							}
							
						}
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <? 
                $i=1; $r=1;
				//###### for fortnight day ascending arr index once more
                foreach($maturity_fortnightly_arr as $sort_val)  
                {
					foreach($sort_val as $fort_id=>$fort_val)
					{
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center"><? echo $fort_val; ?></td>
                        <?
						foreach($bank_com_location_data as $bank_id=>$bank_val)
						{
							foreach($bank_val as $com_id=>$com_data)
							{
								foreach($com_data as $location_id=>$location_data)
								{
									?>
									<td align="right"><? echo number_format($forcast_data[$bank_id][$com_id][$location_id][$fort_val],2);?></td>
									<?
									$tot_val+=$forcast_data[$bank_id][$com_id][$location_id][$fort_val];
									$gt_val[$bank_id][$com_id][$location_id]+=$forcast_data[$bank_id][$com_id][$location_id][$fort_val];
								}
							}
						}
						?>
                        <td align="right"><p><? echo number_format($tot_val,2); $gt_tot_val+=$tot_val;  $tot_val=0; ?>&nbsp;</p></td>
                    </tr>
                    <?
                    $i++;
					}
                }
                ?>
            </tbody>
        	<tfoot>
                <tr class="tbl_bottom">
                	<th>Total:</th>
                    <?
                    foreach($bank_com_location_data as $bank_id=>$bank_val)
                    {
						foreach($bank_val as $com_id=>$com_data)
						{
							foreach($com_data as $location_id=>$location_data)
							{
								?>
                                <th align="right"><? echo number_format($gt_val[$bank_id][$com_id][$location_id],2);?></th>
                                <?
							}
							
						}
                    }
                    ?>
                    <th align="right"><? echo number_format($gt_tot_val,2);?></th>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
}

if($action=="bill_receiveable_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	$bill_com_cond="";

	if($com_id>0) $lc_sc_cond=" and a.beneficiary_name=$com_id";
	if($bank_id>0) $lc_sc_cond.=" and a.lien_bank=$bank_id";
	$sql_lc_sc="select a.id as lc_sc_id, a.tenor, 1 as type from com_export_lc a where a.status_active=1 and a.is_deleted=0 $lc_sc_cond
	union all
	select a.id as lc_sc_id, a.tenor, 2 as type from com_sales_contract a where a.status_active=1 and a.is_deleted=0 $lc_sc_cond";
	//echo $sql_lc_sc;die;
	$sql_lc_sc_result=sql_select($sql_lc_sc);
	$lc_sc_data=array();
	foreach($sql_lc_sc_result as $row)
	{
		$lc_sc_ids=$row[csf("lc_sc_id")]."__".$row[csf("type")];
		$lc_sc_data[$lc_sc_ids]=$row[csf("tenor")];
	}
	unset($sql_lc_sc_result);
	
	if($com_id>0) $beneficiary_cond=" and b.benificiary_id=$com_id";
	$proceed_rlz_sql="SELECT a.is_lc, a.lc_sc_id, b.invoice_bill_id, c.id as dtls_id, c.account_head, c.document_currency as document_currency 
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
		}
	}
	unset($proceed_rlz_sql_result);
	$bill_com_cond="";
	if($com_id>0) $bill_com_cond=" and b.company_id=$com_id";
	if($bank_id>0) $bill_bank_cond=" and b.lien_bank=$bank_id";
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
	
	if($db_type==0)
	{
		$bill_sql="SELECT b.id as bill_id, b.buyer_id, b.bank_ref_no, b.submit_date, b.bank_ref_date, b.submit_type, a.is_lc, a.lc_sc_id, group_concat(c.id) as inv_ids, group_concat(c.invoice_no) as invoice_no, group_concat(c.bl_no) as BL_NO, sum(c.net_invo_value) as inv_value, sum(c.invoice_quantity) as bill_qnty, sum(a.net_invo_value) as bill_value, a.submission_dtls_id
		from com_export_invoice_ship_mst c, com_export_doc_submission_invo a, com_export_doc_submission_mst b 
		where c.id=a.invoice_id and a.doc_submission_mst_id=b.id and b.entry_form = 40 and b.lien_bank > 0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond 
		group by b.id, b.buyer_id, b.bank_ref_no, b.submit_date, b.bank_ref_date, b.submit_type, a.is_lc, a.lc_sc_id, a.submission_dtls_id";
	}
	else
	{
		$bill_sql="SELECT b.id as bill_id, b.buyer_id, b.bank_ref_no, b.submit_date, b.bank_ref_date, b.submit_type, a.is_lc, a.lc_sc_id, listagg(cast(c.id as varchar(4000)),',') within group(order by c.id) as inv_ids, listagg(cast(c.invoice_no as varchar(4000)),',') within group(order by c.id) as invoice_no, sum(c.net_invo_value) as inv_value, sum(c.invoice_quantity) as bill_qnty, listagg(cast(c.bl_no as varchar(4000)),',') within group(order by c.bl_no) as BL_NO, sum(a.net_invo_value) as bill_value, a.submission_dtls_id
		from com_export_invoice_ship_mst c, com_export_doc_submission_invo a, com_export_doc_submission_mst b
		where c.id=a.invoice_id and a.doc_submission_mst_id=b.id and b.entry_form = 40 and b.lien_bank > 0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond 
		group by b.id, b.buyer_id, b.bank_ref_no, b.submit_date, b.bank_ref_date, b.submit_type, a.is_lc, a.lc_sc_id, a.submission_dtls_id";
	}
	//echo $bill_sql;
	$bill_sql_result=sql_select($bill_sql);
	//echo "<pre>";print_r($inv_data);die;
	//$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	//$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
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
    <div id="report_container" align="center" style="width:1180px">
	<fieldset style="width:1180px;">
        <table class="rpt_table" border="1" rules="all" width="1180" cellpadding="0" cellspacing="0">
            <thead>
				<tr>
					<th colspan="13">Bills Receiveable For LC</th>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="120">Buyer</th>
					<th width="120">Bank Ref. No</th>
					<th width="70">Submission Date</th>
					<th width="200">Invoice No</th>
					<th width="70">Bank Ref. Date</th>
					<th width="90">Bill Qty.</th>
					<th width="100">Bill Value</th>
					<th width="60">Tenor Days</th>
					<th width="70">Maturity Date</th>
					<th width="90">Bill Purchase Amount</th>
					<th width="70">Bill Purchase %</th>
					<th>Purchase Date</th>
				</tr>
            </thead>
            <tbody>
                <? 
                $i=1;
                foreach($bill_sql_result as $row)  
                {
					
					$bill_rate=$row[csf('inv_value')]/$row[csf('bill_qnty')];
					if($row[csf("submit_type")]==2){$pending_bill_value=$row[csf("bill_value")]-$bill_trans_data[$row[csf("bill_id")]];}
					else{$pending_bill_value=$row[csf("bill_value")];}
					$pending_bill_qnty=$pending_bill_value/$bill_rate;
					$ls_sc_ids=$row[csf('lc_sc_id')]."__".$row[csf('is_lc')];
					$maturity_date="";
					if($row[csf('bank_ref_date')] !="" && $row[csf('bank_ref_date')] !="0000-00-00")
					{
						$maturity_date=date('d-m-Y',(strtotime($row[csf("bank_ref_date")])+strtotime($lc_sc_data[$ls_sc_ids])));
					}
					$bill_value_trans=$bill_trans_data[$row[csf("bill_id")]];
					$purchase_percent=0;
					if($bill_value_trans!="")
					{
						$purchase_percent=(($bill_value_trans/$pending_bill_value)*100);
					}
					if($realize_data_arr[$row[csf("bill_id")]]=="" && $row[csf('is_lc')]==1)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td align="center"><? echo $i; ?></td>
                            <td><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
                            <td><p><? echo $row[csf('bank_ref_no')]; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo change_date_format($row[csf('submit_date')]); ?>&nbsp;</p></td>
                            <td><p><? echo implode(",",array_unique(explode(",",$row[csf('invoice_no')]))); ?>&nbsp;</p></td>
                            <td align="center"><p><? if($row[csf('bank_ref_date')] !="" && $row[csf('bank_ref_date')] !="0000-00-00") echo change_date_format($row[csf('bank_ref_date')]); ?>&nbsp;</p></td>
                            <td align="right"><? echo number_format($pending_bill_qnty,2); ?></td>
                            <td align="right"><? echo number_format($pending_bill_value,2); ?></td>
                            <td align="center"><p><? echo $lc_sc_data[$ls_sc_ids];?></p></td>
                            <td align="center"><p><? if($maturity_date !="") echo change_date_format($maturity_date); ?>&nbsp;</p></td>
                            <td align="right"><? echo number_format($bill_value_trans,2); ?></td>
                            <td align="right"><? echo number_format($purchase_percent,2); ?></td>
                            <td align="center"><p><? echo change_date_format($row[csf('submit_date')]); ?>&nbsp;</p></td>
                        </tr>
                        <?
                        $tot_pending_bill_qnty+=$pending_bill_qnty;
                        $tot_pending_bill_value+=$pending_bill_value;
                        $tot_bill_value_trans+=$bill_value_trans;
                        $i++;
					}
                }
                ?>
                <tr bgcolor="#CCCCCC">
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total</td>
                    <td align="right"><? echo number_format($tot_pending_bill_qnty,2);?></td>
                    <td align="right"><? echo number_format($tot_pending_bill_value,2);?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_bill_value_trans,2);?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        	<!--<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total:</td>
                    <td align="right" id="value_tot_pendin_value"><?// echo number_format($tot_net_due_usd,2); ?></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>-->
        </table>
		<?
			$bill_buyer_sql="SELECT a.ID, b.SUBMIT_DATE, b.POSSIBLE_REALI_DATE
			from com_export_doc_submission_invo a, com_export_doc_submission_mst b
			where a.doc_submission_mst_id=b.id and b.entry_form = 39 and b.lien_bank > 0 and a.is_lc=2 and a.is_converted=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond ";
			// echo $bill_buyer_sql;
			$bill_buyer_sql_result=sql_select($bill_buyer_sql);
			$bill_buyer_data=array();
			foreach($bill_buyer_sql_result as $row)
			{
				$bill_buyer_data[$row["ID"]]["SUBMIT_DATE"]=$row["SUBMIT_DATE"];
				$bill_buyer_data[$row["ID"]]["POSSIBLE_REALI_DATE"]=$row["POSSIBLE_REALI_DATE"];
			}
		?>
		<br>
		<table class="rpt_table" border="1" rules="all" width="720" cellpadding="0" cellspacing="0">
            <thead>
				<tr>
					<th colspan="8">Bills Receiveable For Sales Contract</th>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="100">Buyer</th>
					<th width="100">Submission to Buyer Date</th>
					<th width="100">Invoice No</th>
					<th width="100">BL No</th>
					<th width="100">Possible Reali Date</th>
					<th width="80">Bill Qty.</th>
					<th >Bill Value</th>
				</tr>
            </thead>
            <tbody>
                <? 
                $i=1;
                foreach($bill_sql_result as $row)  
                {
					// $bill_rate=$row[csf('inv_value')]/$row[csf('bill_qnty')];
					// $pending_bill_value=$row[csf('bill_value')];
					// $pending_bill_qnty=$pending_bill_value/$bill_rate;
					$bill_rate=$row[csf('inv_value')]/$row[csf('bill_qnty')];
					if($row[csf("submit_type")]==2){$pending_bill_value=$row[csf("bill_value")]-$bill_trans_data[$row[csf("bill_id")]];}
					else{$pending_bill_value=$row[csf("bill_value")];}
					$pending_bill_qnty=$pending_bill_value/$bill_rate;

					if($realize_data_arr[$row[csf("bill_id")]]=="" && $row[csf('is_lc')]==2 && $row[csf('submission_dtls_id')]!=0)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td align="center"><? echo $i; ?></td>
							<td><p><? echo $buyer_arr[$row['BUYER_ID']]; ?>&nbsp;</p></td>
							<td align="center"><p><? echo change_date_format($bill_buyer_data[$row["SUBMISSION_DTLS_ID"]]["SUBMIT_DATE"]); ?>&nbsp;</p></td>
							<td><p><? echo implode(", ",array_unique(explode(",",$row['INVOICE_NO']))); ?>&nbsp;</p></td>
							<td><p><? echo implode(", ",array_unique(explode(",", $row['BL_NO']))); ?>&nbsp;</p></td>
							<td align="center"><p><? echo change_date_format($bill_buyer_data[$row["SUBMISSION_DTLS_ID"]]["POSSIBLE_REALI_DATE"]); ?>&nbsp;</p></td>
							<td align="right"><? echo number_format($pending_bill_qnty,2); ?></td>
							<td align="right"><? echo number_format($pending_bill_value,2); ?></td>
						</tr>
						<?
						$tot_pending_bill_qnty+=$pending_bill_qnty;
						$tot_pending_bill_value+=$pending_bill_value;
						$tot_bill_value_trans+=$bill_value_trans;
						$i++;
					}
                }
                ?>
            </tbody>
		</table>
    </fieldset>
    </div>
    <?
}

if($action=="bank_order_in_hand_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	//echo $com_id."=".$bank_id;die;
	//echo $type.test;die;
	$company_arr = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$bank_arr = return_library_array("select id, bank_name from lib_bank where status_active=1 and is_deleted=0","id","bank_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	$dealing_mer_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
	//$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	//$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	if($com_id>0) $exfact_com_cond=" and a.company_id=$com_id";
	$ex_fact_data=return_library_array("select b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
                
	$str_cond="";
	if($com_id>0) $str_cond.=" and a.company_name=$com_id";
	if($bank_id>0) $str_cond.=" and d.TAG_BANK=$bank_id";
	$order_sql="SELECT a.company_name, b.bank_id, a.buyer_name, a.dealing_marchant, c.id as po_id, (c.po_quantity*a.total_set_qnty) as order_quantity, (c.po_total_price) as order_total, c.unit_price
	from wo_po_break_down c, wo_po_details_master a, lib_buyer b, lib_buyer_tag_bank d 
	where a.job_no=c.job_no_mst and a.buyer_name=b.id and b.id=d.BUYER_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and d.TAG_BANK>0 $str_cond 
	order by po_id";//
	//echo $order_sql;//die;
	$order_result=sql_select($order_sql);
	$dtls_data=array();$order_dup_check=array();
	foreach($order_result as $row)
	{
		if($order_dup_check[$row[csf("po_id")]]=="")
		{
			$order_dup_check[$row[csf("po_id")]]=$row[csf("po_id")];
			$pending_qnty=$row[csf("order_quantity")]-$ex_fact_data[$row[csf("po_id")]];
			$rate=$row[csf("order_total")]/$row[csf("order_quantity")];
			if($pending_qnty>0)
			{
				$pending_value=$pending_qnty*$rate;
				$dtls_data[$row[csf("company_name")]][$row[csf("bank_id")]][$row[csf("buyer_name")]][$row[csf("dealing_marchant")]]["order_quantity"]+=$pending_qnty;
				$dtls_data[$row[csf("company_name")]][$row[csf("bank_id")]][$row[csf("buyer_name")]][$row[csf("dealing_marchant")]]["order_total"]+=$pending_value;
			}
		}
		
	}
	//echo "<pre>";print_r($dtls_data);die;
	
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
    <div id="report_container" align="center" style="width:720px">
	<fieldset style="width:720px;">
        <table class="rpt_table" border="1" rules="all" width="720" cellpadding="0" cellspacing="0">
            <thead>
            	<tr>
                	<th width="40" rowspan="2">SL</th>
                    <th width="150" rowspan="2">Buyer Name</th>
                    <th width="150" rowspan="2">Dealing Marchent</th>
                    <th colspan="3">Order In Hand</th>
                </tr>
                <tr>
                	<th width="120">Quantity</th>
                    <th width="120">Value</th>
                    <th >Avg.  FOB</th>
                </tr>
            </thead>
            <tbody>
            <? 
			$i=1; $r=1;
			//echo "<pre>";print_r($dtls_data);die;
			$tot_pendin_value=0;
			foreach($dtls_data as $com_id=>$com_data)
			{
				?>
                <tr>
                	<td colspan="6" style="font-size:16px; font-weight:bold; text-align:center;"><? echo $company_arr[$com_id];?></td>
                </tr>
                <?
				$com_order_quantity=$com_order_total=0;
				foreach($com_data as $bank_id=>$bank_data)
				{
					$bank_order_quantity=$bank_order_total=0;
					?>
                    <tr>
                        <td colspan="6" style="font-size:14px; font-weight:bold; text-align:center;"><? echo $bank_arr[$bank_id];?></td>
                    </tr>
                    <?
					foreach($bank_data as $buyer_id=>$buyer_data)  
					{
						foreach($buyer_data as $marchand_id=>$row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$avg_fob = $row[("order_total")]/$row[("order_quantity")];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td align="center"><? echo $i; ?></td>
								<td title="<? echo $buyer_id; ?>"><p><? echo $buyer_arr[$buyer_id]; ?>&nbsp;</p></td>
								<td title="<? echo $marchand_id; ?>"><p><? echo $dealing_mer_arr[$marchand_id]; ?>&nbsp;</p></td>
								<td align="right"><? echo number_format($row[("order_quantity")],2);  ?></td>
								<td align="right"><? echo number_format($row[("order_total")],2);  ?></td>
								<td align="right"><? echo number_format($avg_fob,2);?></td>
							</tr>
							<?
							$tot_order_quantity+=$row[("order_quantity")];
							$tot_order_total+=$row[('order_total')];
							$bank_order_quantity+=$row[("order_quantity")];
							$bank_order_total+=$row[('order_total')];
							$com_order_quantity+=$row[("order_quantity")];
							$com_order_total+=$row[('order_total')];
							$i++;
						}
					}
					?>
                    <tr bgcolor="#FFFFCC">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right">Bank Total:</td>
                        <td align="right"><? echo number_format($bank_order_quantity,2)?></td>
                        <td align="right"><? echo number_format($bank_order_total,2)?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?
				}
				?>
                <tr bgcolor="#CCCCCC">
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Company Total:</td>
                    <td align="right"><? echo number_format($com_order_quantity,2)?></td>
                    <td align="right"><? echo number_format($com_order_total,2)?></td>
                    <td>&nbsp;</td>
                </tr>
                <?
			}
			
			?>
            </tbody>
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="3" align="right">Grand Total:</td>
                    <td align="right"><? echo number_format($tot_order_quantity,2); ?></td>
                    <td align="right"><? echo number_format($tot_order_total,2); ?></td>
                    <td><? $tot_avg_fob=$tot_order_total/$tot_order_quantity; echo number_format($tot_avg_fob,2); ?></td>
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
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	//$company_id=str_replace("'","",$company_id);
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
	$html='<div id="report_container" align="center" style="width:2750px">
	<fieldset style="width:2750px;">
        <table class="rpt_table" border="1" rules="all" width="2750" cellpadding="0" cellspacing="0">
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
                <th width="70">PO Insert Date</th>
				<th width="70">PO Rec.Date</th>
				<th width="70">Pub. Ship Date</th>
				<th width="60">Pcs/Set</th>
				<th width="120">PO Qty.(Pcs/set)</th>
                <th width="130">PO Qty.(Pcs)</th>
                <th width="70">Unit Price</th>
                <th width="140">PO Value ($)</th>
                <th width="130">Ship Qty.</th>
                <th width="140">Ship Value ($)</th>
                <th width="130">In Hand Qty.</th>
                <th width="140">In Hand Value ($)</th>
                <th width="70">CM</th>
                <th width="70">To Be</th>
                <th width="70">To be Done</th>
                <th width="70">To Be Pending</th>
                <th width="70">Ship Status</th>
				<th width="70">Approx. Payment Date</th>
				<th width="90">Payment Terms</th>
				<th width="80">Remarks</th>
				
            </thead>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2750" cellpadding="0" cellspacing="0" id="table_body">
            <tbody>';
		?>
	<div id="report_container" align="center" style="width:2750px">
	<fieldset style="width:2750px;">
        <table class="rpt_table" border="1" rules="all" width="2750" cellpadding="0" cellspacing="0">
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
                <th width="70">PO Insert Date</th>
				<th width="70">PO Rec.Date</th>
                <th width="70">Pub. Ship Date</th>
				<th width="60">Pcs/Set</th>
				<th width="120">PO Qty.(Pcs/set)</th>
                <th width="130">PO Qty.(Pcs)</th>
                <th width="70">Unit Price</th>
                <th width="140">PO Value ($)</th>
                <th width="130">Ship Qty.</th>
                <th width="140">Ship Value ($)</th>
                <th width="130">In Hand Qty.</th>
                <th width="140">In Hand Value ($)</th>
				<th width="70">CM</th>
                <th width="70">To Be</th>
                <th width="70">To be Done</th>
                <th width="70">To Be Pending</th>
                <th width="70">Ship Status</th>
				<th width="70">Approx. Payment Date</th>
				<th width="90">Payment Terms</th>
				<th width="80">Remarks</th>
            </thead>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2550" cellpadding="0" cellspacing="0" id="table_body">
            <tbody>
            <? 
                $i=1; 
                $InHandValue=0;
                $shipValue=0;
                $povalue=0;
                $exfact_com_cond="";
                if($com_id>0) $exfact_com_cond=" and a.company_id=$com_id";
				if($com_id>0) $lc_sc_com_cond=" and a.beneficiary_name=$com_id";
				if($bank_id>0) $lc_sc_com_cond.=" and a.lien_bank=$bank_id";
				
				
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
			
                $ex_fact_data=return_library_array("select b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
                //echo "<pre>";print_r($ex_fact_data);die;
              

				if($com_id>0) $com_conds=" and a.company_name=$com_id";
				$sel_precostww=sql_select("SELECT b.po_quantity as TOTAL_QTY, b.job_no_mst, b.PO_NUMBER FROM wo_po_details_master a, wo_po_break_down b where a.id=b.JOB_ID and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 $com_conds and a.status_active=1");
				$job_qty_arr=$job_po_wish_qty_arr=array();
				foreach($sel_precostww as $row){
					$job_qty_arr[$row["JOB_NO_MST"]]["TOTAL_QTY"]+=$row["TOTAL_QTY"];
					$job_po_wish_qty_arr[$row["JOB_NO_MST"]][$row["PO_NUMBER"]]["TOTAL_QTY"]+=$row["TOTAL_QTY"];
				}

				if($com_id>0) $com_conds=" and a.company_name=$com_id";
				$yearn_booking=sql_select("SELECT b.JOB_NO, b.AMOUNT as AMOUNT FROM wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.MST_ID $com_conds and a.status_active=1 and b.status_active=1");
				$yearn_booking_qty_arr=array();
				foreach($yearn_booking as $row){
					$yearn_booking_qty_arr[$row["JOB_NO"]]["AMOUNT"]+=$row["AMOUNT"];
				}

				if($com_id>0) $com_conds=" and a.COMPANY_ID=$com_id";
				$trims_booking=sql_select("SELECT b.JOB_NO, c.AMOUNT as AMOUNT FROM wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.id=b.BOOKING_MST_ID and b.id=c.WO_TRIM_BOOKING_DTLS_ID $com_conds and a.status_active=1 and b.status_active=1 and c.status_active=1");
				$trims_booking_qty_arr=array();
				foreach($trims_booking as $row){
					$trims_booking_qty_arr[$row["JOB_NO"]]["AMOUNT"]+=$row["AMOUNT"];
				}

				$com_cond="";
                if($com_id>0) $com_cond=" and a.company_name=$com_id";
				if($bank_id>0) $com_cond.=" and d.tag_bank=$bank_id";
                $order_sql="SELECT a.company_name, a.buyer_name, a.team_leader, a.dealing_marchant, a.style_ref_no, c.shiping_status, c.id as po_break_down_id, c.job_no_mst, c.po_number, (c.po_quantity*a.total_set_qnty) as order_quantity, (c.po_total_price) as order_total, c.pub_shipment_date, c.unit_price, a.style_owner as working_company, b.tenor, b.payment_term, a.remarks, c.po_received_date, to_char(c.insert_date,'dd-mm-yyyy') as insert_date, a.id as job_id,a.order_uom,c.po_quantity

                from wo_po_details_master a, lib_buyer b, wo_po_break_down c,lib_buyer_tag_bank d 
                where a.job_no=c.job_no_mst and b.id=a.buyer_name and d.buyer_id=a.buyer_name and b.id=d.buyer_id and a.status_active=1 and b.status_active=1 and c.status_active=1  and c.shiping_status<>3 and d.TAG_BANK>0 $com_cond order by c.pub_shipment_date";
				//echo $order_sql;die;
				
                $order_sql_result=sql_select($order_sql);
                
				$job_id_arr=array();
				foreach($order_sql_result as $row){
					// $job_id_arr[$row["JOB_ID"]]=$row["JOB_ID"];
					$style_job_arr[$row["STYLE_REF_NO"]]=$row["STYLE_REF_NO"];
				}
				// $con = connect();
				// $rid=execute_query("delete from GBL_TEMP_ENGINE where entry_form=154 and user_id=$user_id");
				// if($rid) oci_commit($con);
			
				if($com_id>0) $com_cond=" and a.COMPANY_ID=$com_id";
				$sql_qc=sql_select("SELECT a.STYLE_REF, a.COSTING_PER, b.FABRIC_COST, b.ACCESSORIES_COST, b.CM_COST FROM qc_mst a, qc_item_cost_summary b WHERE a.QC_NO=b.mst_id and a.OPTION_ID=1 and a.status_active=1 and b.status_active=1 $com_cond
				union all
				SELECT a.STYLE_REF, a.COSTING_PER, b.FABRIC_COST, b.ACCESSORIES_COST, b.CM_COST FROM qc_mst a, qc_item_cost_summary b WHERE a.QC_NO=b.mst_id and a.OPTION_ID=2 and a.status_active=1 and b.status_active=1 $com_cond
				union all 
				SELECT a.STYLE_REF, a.COSTING_PER, b.FABRIC_COST, b.ACCESSORIES_COST, b.CM_COST FROM qc_mst a, qc_item_cost_summary b WHERE a.QC_NO=b.mst_id and a.OPTION_ID=3 and a.status_active=1 and b.status_active=1 $com_cond
				union all 
				SELECT a.STYLE_REF, a.COSTING_PER, b.FABRIC_COST, b.ACCESSORIES_COST, b.CM_COST FROM qc_mst a, qc_item_cost_summary b WHERE a.QC_NO=b.mst_id and a.OPTION_ID=0 and a.OPTION_ID not in(1,2,3)  and a.status_active=1 and b.status_active=1 $com_cond");
				
            	$cm_cost_arr=array();
					foreach($sql_qc as $row){
						$cm_cost_arr[$row["STYLE_REF"]]["CM_COST"]=$row["CM_COST"];
						$cm_cost_arr[$row["STYLE_REF"]]["FABRIC_COST"]=$row["FABRIC_COST"];
						$cm_cost_arr[$row["STYLE_REF"]]["TRIMS_COST"]=$row["ACCESSORIES_COST"];
						$cm_cost_arr[$row["STYLE_REF"]]["COSTING_PER_ID"]=$row["COSTING_PER"];
					}
				 
                $bank_ids=array();
				$po_chk_arr=array();
                foreach($order_sql_result as $row)  
                {
					$total_job_qty= $job_qty_arr[$row["JOB_NO_MST"]]["TOTAL_QTY"];
					 $po_qty=$job_po_wish_qty_arr[$row["JOB_NO_MST"]][$row["PO_NUMBER"]]["TOTAL_QTY"];

					if($cm_cost_arr[$row["STYLE_REF_NO"]]["COSTING_PER_ID"]==1){
						$yearn_cost=$cm_cost_arr[$row["STYLE_REF_NO"]]["FABRIC_COST"]/12;
						$trims_cost=$cm_cost_arr[$row["STYLE_REF_NO"]]["TRIMS_COST"]/12;
					}else{
						$yearn_cost=$cm_cost_arr[$row["STYLE_REF_NO"]]["FABRIC_COST"];
						$trims_cost=$cm_cost_arr[$row["STYLE_REF_NO"]]["TRIMS_COST"];				
					}
					// echo $yearn_cost."____";;die;
					 $trims=$trims_cost*$total_job_qty;
					 $yearn=$yearn_cost*$total_job_qty;
					 $total_yearn_trims=$yearn+$trims;

					$yearn_booking=$yearn_booking_qty_arr[$row["JOB_NO_MST"]]["AMOUNT"];				
					$trims_booking= $trims_booking_qty_arr[$row["JOB_NO_MST"]]["AMOUNT"];
					// echo $yearn_booking."_".$trims_booking."___".$total_job_qty;die;
					$total_trims_yearn_booking=$yearn_booking+$trims_booking;
					
					
					if($po_chk_arr[$row['PO_BREAK_DOWN_ID']]=="")
					{
						$po_chk_arr[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
						$pending_ord_qnty=0;
						$Ship_value=0;
						$po_quantity_value=0;
						$pending_ord_value=0;
						$ord_rate = 0;
						if($row[csf('tenor')]!="") $tenor=$row[csf('tenor')]; else $tenor=0;
						$aprox_pay_date=date("d-M-Y",(strtotime($row[csf('pub_shipment_date')])+($tenor*86400)));
						
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
						$test_pending_data[$row[csf("po_break_down_id")]]=$pending_ord_value;
						$in_hand_qnty=$row[csf("order_quantity")]-$Ship_qnty;
						if($lien_bank_arr[$row[csf('po_break_down_id')]] !=""){
							$bank_ids[$lien_bank_arr[$row[csf('po_break_down_id')]]] = $lien_bank_arr[$row[csf('po_break_down_id')]];
						}
						// echo $total_yearn_trims."__".$total_job_qty."__".$in_hand_qnty."**";die;

						$tobedone=$total_yearn_trims/$total_job_qty*$po_qty;
                        //  echo $total_trims_yearn_booking."_".$total_job_qty."_".$po_qty;die;
						$totbeopen=$total_trims_yearn_booking/$total_job_qty*$po_qty;

						$po_unit_price=$po_quantity_value/$po_quantity;
						if($pendin_qnty>0)
						{
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
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p><? echo date('d-m-y',strtotime($row[csf('insert_date')])); ?>&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p><? echo date('d-m-y',strtotime($row[csf('po_received_date')])); ?>&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p><? echo date('d-m-y',strtotime($row[csf('pub_shipment_date')])); ?>&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="60" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
								<td width="120"  align="right"><?echo $row[csf('po_quantity')];?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130" title="<? echo $row[csf("order_quantity")]."=".$ex_fact_data[$row[csf("po_break_down_id")]];?>"><? echo number_format($po_quantity,0); ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right"><? echo number_format($po_unit_price,4);  ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140"><? echo number_format($po_quantity_value,2); ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130"><? echo number_format($Ship_qnty,0);  ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140"><? echo number_format($Ship_value,2); ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130"><? echo number_format($in_hand_qnty,0);  ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140"><? echo number_format($pending_ord_value,2);  ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" title="'.$shiping_status.'" width="70"> <? echo $cm_cost_arr[$row["STYLE_REF_NO"]]["CM_COST"] ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" title="'.$total_yearn_trims.'*'.$total_job_qty.'*'.$in_hand_qnty.'" width="70"> <? echo number_format($tobedone,2); ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" title="'.$total_trims_yearn_booking.'/'.$total_job_qty.'*'.$in_hand_qnty.'" width="70"><? echo number_format($totbeopen,2) ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" title="'.$tobedone.'-'.$totbeopen.'" width="70"><? echo number_format($tobedone-$totbeopen,2); ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" title="<? echo $shiping_status; ?>" width="70"><? echo  $shipment_status[$shiping_status]; ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" width="70"><p><? echo date('d-m-y',strtotime($aprox_pay_date)); ?>&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="90"><p><? echo $pay_term[$row[csf('payment_term')]];?>&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"><p><? echo $row[csf('remarks')];?>&nbsp;</p></td>
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
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p>'.date('d-m-y',strtotime($row[csf('insert_date')])).'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p>'.date('d-m-y',strtotime($row[csf('po_received_date')])).'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p>'.date('d-m-y',strtotime($row[csf('pub_shipment_date')])).'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="60" align="center">'.$unit_of_measurement[$row[csf('order_uom')]].'</td>
								<td width="120"  align="right">'.$row[csf('po_quantity')].'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130" title="'.$row[csf("order_quantity")].'='.$ex_fact_data[$row[csf("po_break_down_id")]].'">'.number_format($po_quantity,0).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right">'.number_format($po_unit_price,4).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140">'.number_format($po_quantity_value,2).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130">'.number_format($Ship_qnty,0).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140">'.number_format($Ship_value,2).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130">'.number_format($in_hand_qnty,0).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140">'.number_format($pending_ord_value,2).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" title="'.$shiping_status.'" width="70">'.$cm_cost_arr[$row["STYLE_REF_NO"]]["CM_COST"].'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" title="'."TOTAL YARN+TRIMS ".$total_yearn_trims.' / '." TOTAL JOB QTY ".$total_job_qty.' * '."PO QTY ".$po_qty.'" width="70">'.number_format($tobedone,2).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" title="'."TOTAL BOOIKING Y+T ".$total_trims_yearn_booking.' / '."TOTAL JOB QTY ".$total_job_qty.' * '."PO QTY ".$po_qty.'" width="70">'.number_format($totbeopen,2).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" title="'."To Be ".$tobedone.' - '."To be Done ".$totbeopen.'" width="70">'.number_format($totbeopen-$tobedone,2).'</td>								
								<td style="word-wrap:break-word; word-break: break-all;" align="center" title="'.$shiping_status.'" width="70">'.$shipment_status[$shiping_status].'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" width="70"><p>'. date('d-m-y',strtotime($aprox_pay_date)).'&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;"  width="90"><p>'.$pay_term[$row[csf('payment_term')]].'&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;"  width="80"><p>'.$row[csf('remarks')].'&nbsp;</p></td>
							</tr>';
							$i++;
							$tot_po_quantity+=$po_quantity;
							$povalue+=$po_quantity_value; 
							$tot_Ship_qnty+=$Ship_qnty;
							$shipValue+=$Ship_value;
							$total_in_hand_qnty+=$in_hand_qnty; 
							$InHandValue+=$pending_ord_value;
							$tot_po_quantity_pcs_set+=$row[csf('po_quantity')];
						}
					}
                    
                }
				//echo "<pre>";print_r($test_data);die;
                ?>
            </tbody>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2750" cellpadding="0" cellspacing="0" id="report_table_footer">
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="15" align="right">Total : </td>
					<td width="120"><?echo $tot_po_quantity_pcs_set?></td>
                    <td width="130" align="right" id="tot_po_quantity"><? echo number_format($tot_po_quantity,0); ?></td>
                    <td width="70">&nbsp;</td>
                    <td width="141" align="right" id="value_povalue"><? echo number_format($povalue,2); ?></td>
                    <td width="132" align="right" id="tot_Ship_qnty"><? echo number_format($tot_Ship_qnty,0); ?></td>
                    <td align="right" id="value_shipvalue" width="141"><? echo number_format($shipValue,2); ?></td>
                    <td width="132" align="right" id="total_in_hand_qnty"><? echo number_format($total_in_hand_qnty,0); ?></td>
                    <td align="right" id="value_inhandvalue" width="141"><? echo number_format($InHandValue,2); ?></td>
					<td width="71"></td>
                    <td width="71"></td>
                    <td width="71"></td>
                    <td width="71"></td>
                    <td width="70"></td>
                    <td width="70"></td>
                    <td width="90"></td>
                    <td width="82"></td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
	$html.='</tbody>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2750" cellpadding="0" cellspacing="0" id="report_table_footer">
        	<tfoot>
                <tr class="tbl_bottom">
					<td width="30"></td>
					<td width="110"> </td>
					<td width="110"> </td>
					<td width="100"> </td>
					<td width="100"> </td>
					<td width="100"></td>
					<td width="70"> </td>
					<td width="110"> </td>
					<td width="110"> </td>
					<td width="100"></td>
					<td width="100"> </td>
					<td width="70">  </td>
					<td width="70"> </td>
					<td width="70"> </td>
					<td width="60">Total</td>
					<td width="120" align="right">'.$tot_po_quantity_pcs_set.'</td>
                    <td width="130" align="right" id="tot_po_quantity">'.number_format($tot_po_quantity,0).'</td>
					<td width="70">&nbsp;</td>
                    <td width="140" align="right" id="value_povalue" width="140">'.number_format($povalue,2).'</td>
                    <td width="130" align="right" id="tot_Ship_qnty">'.number_format($tot_Ship_qnty,0).'</td>
                    <td align="right" id="value_shipvalue" width="140">'.number_format($shipValue,2).'</td>
                    <td width="130" align="right" id="total_in_hand_qnty">'.number_format($total_in_hand_qnty,0).'</td>
                    <td align="right" id="value_inhandvalue" width="140">'.number_format($InHandValue,2).'</td>
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="90">&nbsp;</td>
					<td width="80">&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>';

	// $r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=154");
	// oci_commit($con);
	// disconnect($con);
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
	<a href="bank_liability_today_report_controller.php?action=print_preview_2&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Merchant Wise" class="formbutton" style="width:110px"/></a>&nbsp;
	&nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_3&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Buyer Wise" class="formbutton" style="width:110px"/></a>&nbsp;
	&nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_4&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Month Wise" class="formbutton" style="width:110px"/></a>
	&nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_5&company_id='<? echo $company_id; ?>'&bank_ids='<? echo $bank_ids;?>'" style="text-decoration:none"><input type="button" value="Monthly Bank Wise" class="formbutton" style="width:130px"/></a>&nbsp;
    &nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_6&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Half Monthly" class="formbutton" style="width:110px;"/></a>&nbsp;
    &nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_7&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Payment Schedule Month wise" class="formbutton" style="width:200px;"/></a>&nbsp;
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
				col: [15,17,18,19,20,21],
				operation: ["sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}
		setFilterGrid("table_body",-1,tableFilters);
    </script>
    <?
	exit();
}

if($action=="order_in_hold_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	//$company_id=str_replace("'","",$company_id);
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
	$html='<div id="report_container" align="center" style="width:2550px">
	<fieldset style="width:2550px;">
        <table class="rpt_table" border="1" rules="all" width="2550" cellpadding="0" cellspacing="0">
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
                <th width="70">PO Insert Date</th>
				<th width="70">PO Rec.Date</th>
				<th width="70">Pub. Ship Date</th>
				<th width="60">UOM</th>
                <th width="130">PO Qty.</th>
                <th width="70">Unit Price</th>
                <th width="140">PO Value ($)</th>
                <th width="130">Ship Qty.</th>
                <th width="140">Ship Value ($)</th>
                <th width="130">In Hand Qty.</th>
                <th width="140">In Hand Value ($)</th>
                <th width="70">Ship Status</th>
				<th width="70">Approx. Payment Date</th>
				<th width="90">Payment Terms</th>
				<th width="80">Remarks</th>
				
            </thead>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2550" cellpadding="0" cellspacing="0" id="table_body">
            <tbody>';
		?>
	<div id="report_container" align="center" style="width:2300px">
	<fieldset style="width:2550px;">
        <table class="rpt_table" border="1" rules="all" width="2530" cellpadding="0" cellspacing="0">
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
                <th width="70">PO Insert Date</th>
				<th width="70">PO Rec.Date</th>
                <th width="70">Pub. Ship Date</th>
				<th width="60">UOM</th>
                <th width="130">PO Qty.</th>
                <th width="70">Unit Price</th>
                <th width="140">PO Value ($)</th>
                <th width="130">Ship Qty.</th>
                <th width="140">Ship Value ($)</th>
                <th width="130">In Hand Qty.</th>
                <th width="140">In Hand Value ($)</th>
                <th width="70">Ship Status</th>
				<th width="70">Approx. Payment Date</th>
				<th width="90">Payment Terms</th>
				<th width="80">Remarks</th>
            </thead>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2550" cellpadding="0" cellspacing="0" id="table_body">
            <tbody>
            <? 
                $i=1; 
                $InHandValue=0;
                $shipValue=0;
                $povalue=0;
                $exfact_com_cond="";
                if($com_id>0) $exfact_com_cond=" and a.company_id=$com_id";
				if($com_id>0) $lc_sc_com_cond=" and a.beneficiary_name=$com_id";
				if($bank_id>0) $lc_sc_com_cond.=" and a.lien_bank=$bank_id";
				
				
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
                if($com_id>0) $com_cond=" and a.company_name=$com_id";
				if($bank_id>0) $com_cond.=" and d.tag_bank=$bank_id";
                $order_sql="SELECT a.company_name, a.buyer_name, a.team_leader, a.dealing_marchant, a.style_ref_no, c.shiping_status, c.id as po_break_down_id, c.job_no_mst, c.po_number, (c.po_quantity*a.total_set_qnty) as order_quantity, (c.po_total_price) as order_total, c.pub_shipment_date, c.unit_price, a.style_owner as working_company, b.tenor, b.payment_term, a.remarks, c.po_received_date, to_char(c.insert_date,'dd-mm-yyyy') as insert_date

                from wo_po_details_master a, lib_buyer b, wo_po_break_down c,lib_buyer_tag_bank d 
                where a.job_no=c.job_no_mst and b.id=a.buyer_name and d.buyer_id=a.buyer_name and b.id=d.buyer_id and a.status_active=1 and b.status_active=1 and c.status_active=2 and c.shiping_status<>3 and d.TAG_BANK>0 $com_cond order by c.pub_shipment_date";
				//echo $order_sql;die;
				$po_chk_arr=array();
                $order_sql_result=sql_select($order_sql);
                $bank_ids=array();$po_chk_arr=array();
                foreach($order_sql_result as $row)  
                {
					if($po_chk_arr[$row['PO_BREAK_DOWN_ID']]=="")
					{
						$po_chk_arr[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
						$pending_ord_qnty=0;
						$Ship_value=0;
						$po_quantity_value=0;
						$pending_ord_value=0;
						$ord_rate = 0;
						if($row[csf('tenor')]!="") $tenor=$row[csf('tenor')]; else $tenor=0;
						$aprox_pay_date=date("d-M-Y",(strtotime($row[csf('pub_shipment_date')])+($tenor*86400)));
						
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
						$test_pending_data[$row[csf("po_break_down_id")]]=$pending_ord_value;
						$in_hand_qnty=$row[csf("order_quantity")]-$Ship_qnty;
						if($lien_bank_arr[$row[csf('po_break_down_id')]] !=""){
							$bank_ids[$lien_bank_arr[$row[csf('po_break_down_id')]]] = $lien_bank_arr[$row[csf('po_break_down_id')]];
						}
						$po_unit_price=$po_quantity_value/$po_quantity;
						if($pendin_qnty>0)
						{
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
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p><? echo date('d-m-y',strtotime($row[csf('insert_date')])); ?></p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p><? echo date('d-m-y',strtotime($row[csf('po_received_date')])); ?></p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p><? echo date('d-m-y',strtotime($row[csf('pub_shipment_date')])); ?></p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="60" align="center"><? echo "Pcs"; ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130" title="<? echo $row[csf("order_quantity")]."=".$ex_fact_data[$row[csf("po_break_down_id")]];?>"><? echo number_format($po_quantity,0); ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right"><? echo number_format($po_unit_price,4);  ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140"><? echo number_format($po_quantity_value,2); ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130"><? echo number_format($Ship_qnty,0);  ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140"><? echo number_format($Ship_value,2); ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130"><? echo number_format($in_hand_qnty,0);  ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140"><? echo number_format($pending_ord_value,2);  ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" title="<? echo $shiping_status; ?>" width="70"><? echo  $shipment_status[$shiping_status]; ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" width="70"><p><? echo date('d-m-y',strtotime($aprox_pay_date)); ?>&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="90"><p><? echo $pay_term[$row[csf('payment_term')]];?>&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"><p><? echo $row[csf('remarks')];?>&nbsp;</p></td>
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
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p>'.date('d-m-y',strtotime($row[csf('insert_date')])).'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p>'.date('d-m-y',strtotime($row[csf('po_received_date')])).'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p>'.date('d-m-y',strtotime($row[csf('pub_shipment_date')])).'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="60" align="center">Pcs</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130" title="'.$row[csf("order_quantity")].'='.$ex_fact_data[$row[csf("po_break_down_id")]].'">'.number_format($po_quantity,0).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right">'.number_format($po_unit_price,4).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140">'.number_format($po_quantity_value,2).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130">'.number_format($Ship_qnty,0).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140">'.number_format($Ship_value,2).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130">'.number_format($in_hand_qnty,0).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140">'.number_format($pending_ord_value,2).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" title="'.$shiping_status.'" width="70">'.$shipment_status[$shiping_status].'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" width="70"><p>'. date('d-m-y',strtotime($aprox_pay_date)).'&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;"  width="90"><p>'.$pay_term[$row[csf('payment_term')]].'&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;"  width="80"><p>'.$row[csf('remarks')].'&nbsp;</p></td>
							</tr>';
							$i++;
							$tot_po_quantity+=$po_quantity;
							$povalue+=$po_quantity_value; 
							$tot_Ship_qnty+=$Ship_qnty;
							$shipValue+=$Ship_value;
							$total_in_hand_qnty+=$in_hand_qnty; 
							$InHandValue+=$pending_ord_value;
						}
					}                    
                }
				//echo "<pre>";print_r($test_data);die;
                ?>
            </tbody>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2550" cellpadding="0" cellspacing="0" id="report_table_footer">
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="15" align="right">Total : </td>
                    <td width="130" align="right" id="tot_po_quantity"><? echo number_format($tot_po_quantity,0); ?></td>
                    <td width="71">&nbsp;</td>
                    <td width="141" align="right" id="value_povalue"><? echo number_format($povalue,2); ?></td>
                    <td width="132" align="right" id="tot_Ship_qnty"><? echo number_format($tot_Ship_qnty,0); ?></td>
                    <td align="right" id="value_shipvalue" width="141"><? echo number_format($shipValue,2); ?></td>
                    <td width="132" align="right" id="total_in_hand_qnty"><? echo number_format($total_in_hand_qnty,0); ?></td>
                    <td align="right" id="value_inhandvalue" width="141"><? echo number_format($InHandValue,2); ?></td>
                    <td width="70"></td>
                    <td width="70"></td>
                    <td width="90"></td>
                    <td width="82"></td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
	$html.='</tbody>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2550" cellpadding="0" cellspacing="0" id="report_table_footer">
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="15" align="right">&nbsp;&nbsp;Total : </td>
                    <td width="130" align="right" id="tot_po_quantity">'.number_format($tot_po_quantity,0).'</td>
					<td width="71">&nbsp;</td>
                    <td width="141" align="right" id="value_povalue" width="140">'.number_format($povalue,2).'</td>
                    <td width="132" align="right" id="tot_Ship_qnty">'.number_format($tot_Ship_qnty,0).'</td>
                    <td align="right" id="value_shipvalue" width="141">'.number_format($shipValue,2).'</td>
                    <td width="132" align="right" id="total_in_hand_qnty">'.number_format($total_in_hand_qnty,0).'</td>
                    <td align="right" id="value_inhandvalue" width="141">'.number_format($InHandValue,2).'</td>
                    <td width="71">&nbsp;</td>
                    <td width="71">&nbsp;</td>
                    <td width="91">&nbsp;</td>
					<td width="81">&nbsp;</td>
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
	<!-- <a href="bank_liability_today_report_controller.php?action=print_preview_2&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Merchant Wise" class="formbutton" style="width:110px"/></a>&nbsp;
	&nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_3&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Buyer Wise" class="formbutton" style="width:110px"/></a>&nbsp;
	&nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_4&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Month Wise" class="formbutton" style="width:110px"/></a>
	&nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_5&company_id='<? echo $company_id; ?>'&bank_ids='<? echo $bank_ids;?>'" style="text-decoration:none"><input type="button" value="Monthly Bank Wise" class="formbutton" style="width:130px"/></a>&nbsp;
    &nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_6&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Half Monthly" class="formbutton" style="width:110px;"/></a>&nbsp;
    &nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_7&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Payment Schedule Month wise" class="formbutton" style="width:200px;"/></a>&nbsp; -->
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
				col: [15,17,18,19,20,21],
				operation: ["sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}
		setFilterGrid("table_body",-1,tableFilters);
    </script>
    <?
	exit();
}

if($action=="order_in_cancel_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	//$company_id=str_replace("'","",$company_id);
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
	$html='<div id="report_container" align="center" style="width:2550px">
	<fieldset style="width:2550px;">
        <table class="rpt_table" border="1" rules="all" width="2550" cellpadding="0" cellspacing="0">
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
                <th width="70">PO Insert Date</th>
				<th width="70">PO Rec.Date</th>
				<th width="70">Pub. Ship Date</th>
				<th width="60">UOM</th>
                <th width="130">PO Qty.</th>
                <th width="70">Unit Price</th>
                <th width="140">PO Value ($)</th>
                <th width="130">Ship Qty.</th>
                <th width="140">Ship Value ($)</th>
                <th width="130">In Hand Qty.</th>
                <th width="140">In Hand Value ($)</th>
                <th width="70">Ship Status</th>
				<th width="70">Approx. Payment Date</th>
				<th width="90">Payment Terms</th>
				<th width="80">Remarks</th>
				
            </thead>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2550" cellpadding="0" cellspacing="0" id="table_body">
            <tbody>';
		?>
	<div id="report_container" align="center" style="width:2300px">
	<fieldset style="width:2550px;">
        <table class="rpt_table" border="1" rules="all" width="2530" cellpadding="0" cellspacing="0">
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
                <th width="70">PO Insert Date</th>
				<th width="70">PO Rec.Date</th>
                <th width="70">Pub. Ship Date</th>
				<th width="60">UOM</th>
                <th width="130">PO Qty.</th>
                <th width="70">Unit Price</th>
                <th width="140">PO Value ($)</th>
                <th width="130">Ship Qty.</th>
                <th width="140">Ship Value ($)</th>
                <th width="130">In Hand Qty.</th>
                <th width="140">In Hand Value ($)</th>
                <th width="70">Ship Status</th>
				<th width="70">Approx. Payment Date</th>
				<th width="90">Payment Terms</th>
				<th width="80">Remarks</th>
            </thead>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2550" cellpadding="0" cellspacing="0" id="table_body">
            <tbody>
            <? 
                $i=1; 
                $InHandValue=0;
                $shipValue=0;
                $povalue=0;
                $exfact_com_cond="";
                if($com_id>0) $exfact_com_cond=" and a.company_id=$com_id";
				if($com_id>0) $lc_sc_com_cond=" and a.beneficiary_name=$com_id";
				if($bank_id>0) $lc_sc_com_cond.=" and a.lien_bank=$bank_id";
				
				
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
                if($com_id>0) $com_cond=" and a.company_name=$com_id";
				if($bank_id>0) $com_cond.=" and d.tag_bank=$bank_id";
                $order_sql="SELECT a.company_name, a.buyer_name, a.team_leader, a.dealing_marchant, a.style_ref_no, c.shiping_status, c.id as po_break_down_id, c.job_no_mst, c.po_number, (c.po_quantity*a.total_set_qnty) as order_quantity, (c.po_total_price) as order_total, c.pub_shipment_date, c.unit_price, a.style_owner as working_company, b.tenor, b.payment_term, a.remarks, c.po_received_date, to_char(c.insert_date,'dd-mm-yyyy') as insert_date

                from wo_po_details_master a, lib_buyer b, wo_po_break_down c,lib_buyer_tag_bank d 
                where a.job_no=c.job_no_mst and b.id=a.buyer_name and d.buyer_id=a.buyer_name and b.id=d.buyer_id and a.status_active=1 and b.status_active=1 and c.status_active=3 and c.shiping_status<>3 and d.TAG_BANK>0 $com_cond order by c.pub_shipment_date";
				//echo $order_sql;die;
				
                $order_sql_result=sql_select($order_sql);
                $bank_ids=array();$po_chk_arr=array();
                foreach($order_sql_result as $row)  
                {
					if($po_chk_arr[$row['PO_BREAK_DOWN_ID']]=="")
					{
						$po_chk_arr[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
						$pending_ord_qnty=0;
						$Ship_value=0;
						$po_quantity_value=0;
						$pending_ord_value=0;
						$ord_rate = 0;
						if($row[csf('tenor')]!="") $tenor=$row[csf('tenor')]; else $tenor=0;
						$aprox_pay_date=date("d-M-Y",(strtotime($row[csf('pub_shipment_date')])+($tenor*86400)));
						
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
						$test_pending_data[$row[csf("po_break_down_id")]]=$pending_ord_value;
						$in_hand_qnty=$row[csf("order_quantity")]-$Ship_qnty;
						if($lien_bank_arr[$row[csf('po_break_down_id')]] !=""){
							$bank_ids[$lien_bank_arr[$row[csf('po_break_down_id')]]] = $lien_bank_arr[$row[csf('po_break_down_id')]];
						}
						$po_unit_price=$po_quantity_value/$po_quantity;
						if($pendin_qnty>0)
						{
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
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p><? echo date('d-m-y',strtotime($row[csf('insert_date')])); ?></p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p><? echo date('d-m-y',strtotime($row[csf('po_received_date')])); ?></p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p><? echo date('d-m-y',strtotime($row[csf('pub_shipment_date')])); ?></p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="60" align="center"><? echo "Pcs"; ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130" title="<? echo $row[csf("order_quantity")]."=".$ex_fact_data[$row[csf("po_break_down_id")]];?>"><? echo number_format($po_quantity,0); ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right"><? echo number_format($po_unit_price,4);  ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140"><? echo number_format($po_quantity_value,2); ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130"><? echo number_format($Ship_qnty,0);  ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140"><? echo number_format($Ship_value,2); ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130"><? echo number_format($in_hand_qnty,0);  ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140"><? echo number_format($pending_ord_value,2);  ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" title="<? echo $shiping_status; ?>" width="70"><? echo  $shipment_status[$shiping_status]; ?></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" width="70"><p><? echo date('d-m-y',strtotime($aprox_pay_date)); ?>&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="90"><p><? echo $pay_term[$row[csf('payment_term')]];?>&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"><p><? echo $row[csf('remarks')];?>&nbsp;</p></td>
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
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p>'.date('d-m-y',strtotime($row[csf('insert_date')])).'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p>'.date('d-m-y',strtotime($row[csf('po_received_date')])).'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="center"><p>'.date('d-m-y',strtotime($row[csf('pub_shipment_date')])).'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="60" align="center">Pcs</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130" title="'.$row[csf("order_quantity")].'='.$ex_fact_data[$row[csf("po_break_down_id")]].'">'.number_format($po_quantity,0).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right">'.number_format($po_unit_price,4).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140">'.number_format($po_quantity_value,2).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130">'.number_format($Ship_qnty,0).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140">'.number_format($Ship_value,2).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130">'.number_format($in_hand_qnty,0).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140">'.number_format($pending_ord_value,2).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" title="'.$shiping_status.'" width="70">'.$shipment_status[$shiping_status].'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="center" width="70"><p>'. date('d-m-y',strtotime($aprox_pay_date)).'&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;"  width="90"><p>'.$pay_term[$row[csf('payment_term')]].'&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;"  width="80"><p>'.$row[csf('remarks')].'&nbsp;</p></td>
							</tr>';
							$i++;
							$tot_po_quantity+=$po_quantity;
							$povalue+=$po_quantity_value; 
							$tot_Ship_qnty+=$Ship_qnty;
							$shipValue+=$Ship_value;
							$total_in_hand_qnty+=$in_hand_qnty; 
							$InHandValue+=$pending_ord_value;
						}
					}
                    
                }
				//echo "<pre>";print_r($test_data);die;
                ?>
            </tbody>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2550" cellpadding="0" cellspacing="0" id="report_table_footer">
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="15" align="right">Total : </td>
                    <td width="130" align="right" id="tot_po_quantity"><? echo number_format($tot_po_quantity,0); ?></td>
                    <td width="71">&nbsp;</td>
                    <td width="141" align="right" id="value_povalue"><? echo number_format($povalue,2); ?></td>
                    <td width="132" align="right" id="tot_Ship_qnty"><? echo number_format($tot_Ship_qnty,0); ?></td>
                    <td align="right" id="value_shipvalue" width="141"><? echo number_format($shipValue,2); ?></td>
                    <td width="132" align="right" id="total_in_hand_qnty"><? echo number_format($total_in_hand_qnty,0); ?></td>
                    <td align="right" id="value_inhandvalue" width="141"><? echo number_format($InHandValue,2); ?></td>
                    <td width="70"></td>
                    <td width="70"></td>
                    <td width="90"></td>
                    <td width="82"></td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
	$html.='</tbody>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2550" cellpadding="0" cellspacing="0" id="report_table_footer">
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="15" align="right">&nbsp;&nbsp;Total : </td>
                    <td width="130" align="right" id="tot_po_quantity">'.number_format($tot_po_quantity,0).'</td>
					<td width="71">&nbsp;</td>
                    <td width="141" align="right" id="value_povalue" width="140">'.number_format($povalue,2).'</td>
                    <td width="132" align="right" id="tot_Ship_qnty">'.number_format($tot_Ship_qnty,0).'</td>
                    <td align="right" id="value_shipvalue" width="141">'.number_format($shipValue,2).'</td>
                    <td width="132" align="right" id="total_in_hand_qnty">'.number_format($total_in_hand_qnty,0).'</td>
                    <td align="right" id="value_inhandvalue" width="141">'.number_format($InHandValue,2).'</td>
                    <td width="71">&nbsp;</td>
                    <td width="71">&nbsp;</td>
                    <td width="91">&nbsp;</td>
					<td width="81">&nbsp;</td>
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
	<!-- <a href="bank_liability_today_report_controller.php?action=print_preview_2&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Merchant Wise" class="formbutton" style="width:110px"/></a>&nbsp;
	&nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_3&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Buyer Wise" class="formbutton" style="width:110px"/></a>&nbsp;
	&nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_4&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Month Wise" class="formbutton" style="width:110px"/></a>
	&nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_5&company_id='<? echo $company_id; ?>'&bank_ids='<? echo $bank_ids;?>'" style="text-decoration:none"><input type="button" value="Monthly Bank Wise" class="formbutton" style="width:130px"/></a>&nbsp;
    &nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_6&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Half Monthly" class="formbutton" style="width:110px;"/></a>&nbsp;
    &nbsp; 
	<a href="bank_liability_today_report_controller.php?action=print_preview_7&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Payment Schedule Month wise" class="formbutton" style="width:200px;"/></a>&nbsp; -->
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
				col: [15,17,18,19,20,21],
				operation: ["sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}
		setFilterGrid("table_body",-1,tableFilters);
    </script>
    <?
	exit();
}

if($action=="btb_opened_popup")
{
	echo load_html_head_contents("BTB to be Opened Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$bank_arr=return_library_array( "select bank_name,id from lib_bank",'id','bank_name');
	$order_sql="SELECT c.id as PO_ID, c.po_total_price as ORDER_TOTAL 
	from LIB_BUYER_TAG_BANK a, wo_po_details_master b, wo_po_break_down c 
	where a.buyer_id=b.buyer_name and b.job_no=c.job_no_mst and b.company_name=$company_id and a.TAG_BANK=$bank_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and a.TAG_BANK>0";
	// echo $order_sql;
	$order_data=sql_select($order_sql);
	$po_id_arr=array();$po_value=0;
	foreach($order_data as $row)
	{
		$po_id_arr[$row['PO_ID']]=$row['PO_ID'];
		$po_value+=$row['ORDER_TOTAL'];
	}
	unset($order_data);

	$po_id_in=where_con_using_array($po_id_arr,0,'a.wo_po_break_down_id');
	$attach_order_sql="SELECT b.import_mst_id as BTB_MST_ID from com_sales_contract_order_info a,com_btb_export_lc_attachment b where a.com_sales_contract_id=b.lc_sc_id and b.is_lc_sc=1 and a.status_active=1 $po_id_in
	union all 
	select b.import_mst_id as BTB_MST_ID from com_export_lc_order_info a,com_btb_export_lc_attachment b where a.com_export_lc_id=b.lc_sc_id and b.is_lc_sc=0 and a.status_active=1 and b.status_active=1 $po_id_in";
	// echo $attach_order_sql;
	$attach_data=sql_select($attach_order_sql);
	$btb_id_arr=array();
	foreach($attach_data as $row)
	{
		$btb_id_arr[$row['BTB_MST_ID']]=$row['BTB_MST_ID'];
	}
	unset($attach_data);

	$btb_id_in=where_con_using_array($btb_id_arr,0,'id');
	$btb_sql="SELECT id,lc_value as LC_VALUE from com_btb_lc_master_details where status_active=1 $btb_id_in ";
	// echo $btb_sql;
	$btb_data=sql_select($btb_sql);
	$btb_value=0;
	foreach($btb_data as $row)
	{
		$btb_value+=$row['LC_VALUE'];
	}
	unset($btb_data);
	?>
	    <div id="report_container" align="center" style="width:300px">
		<fieldset style="width:300px;">
		    <table class="rpt_table" border="1" rules="all" width="300" cellpadding="0" cellspacing="0">

		        <thead>
					<tr>
						<th colspan="2">Bank Name <?=$bank_arr[$bank_id];?> </th>
					</tr>
					<tr>
						<th width="150">Particular</th>
						<th>Amount [$]</th>
					</tr>
		        </thead>
	            <tbody>
					<tr bgcolor="#E9F3FF" onClick="change_color('tr_1','#E9F3FF')" id="tr_1">
						<td>Order Value</td>
						<td align="right"><? echo fn_number_format($po_value,2); ?></td>
					</tr>
					<tr bgcolor="#FFFFFF" onClick="change_color('tr_2','#FFFFFF')" id="tr_2">
						<td>BTB need to Open</td>
						<td align="right"><? echo fn_number_format($po_value*0.45,2); ?></td>
					</tr>
					<tr bgcolor="#E9F3FF" onClick="change_color('tr_3','#E9F3FF')" id="tr_3">
						<td>BTB opened</td>
						<td align="right"><? echo fn_number_format($btb_value,2); ?></td>
					</tr>
					<tr bgcolor="#FFFFFF" onClick="change_color('tr_4','#FFFFFF')" id="tr_4">
						<td>BTB To Be Open</td>
						<td align="right"><? echo fn_number_format(($po_value*0.45)-$btb_value,2); ?></td>
					</tr>
					<tr bgcolor="#E9F3FF" onClick="change_color('tr_5','#E9F3FF')" id="tr_5">
						<td>Amount in lac</td>
						<td align="right"><? echo fn_number_format((($po_value*0.45)-$btb_value)/100000,2); ?></td>
					</tr>
	            </tbody>
        	</table>
	<?
	exit();
}

if ($action=="print_preview_2") // Created by Tipu
{
	//echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	$company_arr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$dealing_mer_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
	ob_start();
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
					$exfact_com_cond="";
    				if($com_id>0) $exfact_com_cond=" and a.company_id=$com_id";
	            	$ex_fact_data=return_library_array("SELECT b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
                	//echo "<pre>";print_r($ex_fact_data);die;

					/*$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, sum(b.order_quantity) as po_quantity, sum(b.order_total) as order_total, c.pub_shipment_date
					FROM wo_po_details_master a, lib_buyer b, wo_po_break_down c 
					WHERE a.job_no=c.job_no_mst and b.id=a.buyer_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.shiping_status<>3 and c.is_deleted=0 $com_cond 
					GROUP by a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, c.pub_shipment_date 
					ORDER by a.dealing_marchant asc";*/
					$com_cond="";
					if($com_id>0) $com_cond=" and a.company_name=$com_id";
					// if($bank_id>0) $com_cond.=" and b.bank_id=$bank_id";
					if($bank_id>0) $com_cond.=" and d.tag_bank=$bank_id";
					$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id as po_break_down_id, c.job_no_mst, c.po_number, sum(c.po_quantity*a.total_set_qnty) as po_quantity, sum(c.po_total_price) as order_total, c.pub_shipment_date
					FROM wo_po_details_master a, lib_buyer b, wo_po_break_down c , lib_buyer_tag_bank d 
					WHERE a.job_no=c.job_no_mst and b.id=a.buyer_name and d.buyer_id=a.buyer_name and b.id=d.buyer_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and d.TAG_BANK>0 $com_cond 
					GROUP by a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id, c.job_no_mst, c.po_number, c.pub_shipment_date 
					ORDER by a.buyer_name asc";
					//echo $main_sql;die;
					
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
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	$company_arr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$dealing_mer_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");

	ob_start();
	
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
					/*$exfact_com_cond="";
    				if($com_id>0) $exfact_com_cond=" and a.company_name=$com_id";
	            	$ex_fact_data=return_library_array("SELECT b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
						
					$com_cond="";
					if($com_id>0) $com_cond=" and a.company_name=$com_id";
					if($bank_id>0) $com_cond.=" and b.bank_id=$bank_id";
					$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id as po_break_down_id, c.job_no_mst, c.po_number, sum(c.po_quantity*a.total_set_qnty) as po_quantity, sum(c.po_total_price) as order_total, c.pub_shipment_date
					FROM wo_po_details_master a, lib_buyer b, wo_po_break_down c 
					WHERE a.job_no=c.job_no_mst and b.id=a.buyer_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and b.bank_id>0 $com_cond 
					GROUP by a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id, c.job_no_mst, c.po_number, c.pub_shipment_date 
					ORDER by a.buyer_name asc";*/
					$exfact_com_cond="";
    				if($com_id>0) $exfact_com_cond=" and a.company_id=$com_id";
	            	$ex_fact_data=return_library_array("SELECT b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");                	
					$com_cond="";
					if($com_id>0) $com_cond=" and a.company_name=$com_id";
					if($bank_id>0) $com_cond.=" and d.tag_bank=$bank_id";
					$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id as po_break_down_id, c.job_no_mst, c.po_number, sum(c.po_quantity*a.total_set_qnty) as po_quantity, sum(c.po_total_price) as order_total, c.pub_shipment_date
					FROM wo_po_details_master a, lib_buyer b, wo_po_break_down c, lib_buyer_tag_bank d 
					WHERE a.job_no=c.job_no_mst and b.id=a.buyer_name and d.buyer_id=a.buyer_name and b.id=d.buyer_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and d.TAG_BANK>0 $com_cond 
					GROUP by a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id, c.job_no_mst, c.po_number, c.pub_shipment_date 
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
						if($in_hand_value>0)
						{
							$buyer_rowspan[$row[csf('buyer_name')]]++;
							$in_hand_qnty_arr[$row[csf('buyer_name')]]+=$in_hand_qnty;
							$in_hand_value_arr[$row[csf('buyer_name')]]+=$in_hand_value;
						}
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
						if($in_hand_value>0)
						{
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


if ($action=="print_preview_6") // Created by Jahid
{
	//echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	$company_arr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	//$buyer_wise_tenor

	ob_start();
	$exfact_com_cond="";
	if($com_id>0) $exfact_com_cond=" and a.company_id=$com_id";
	$ex_fact_data=return_library_array("SELECT b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");                	
	$com_cond="";
	if($com_id>0) $com_cond=" and a.company_name=$com_id";
	if($bank_id>0) $com_cond.=" and d.tag_bank=$bank_id";
	$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, a.style_ref_no, c.shiping_status, c.id as po_break_down_id, c.job_no_mst, c.po_number, (c.po_quantity*a.total_set_qnty) as po_quantity, c.po_total_price as order_total, c.pub_shipment_date, b.bank_id, b.tenor, b.payment_term
	FROM wo_po_details_master a, lib_buyer b, wo_po_break_down c , lib_buyer_tag_bank d 
	WHERE a.job_no=c.job_no_mst and b.id=a.buyer_name and d.buyer_id=a.buyer_name and b.id=d.buyer_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and d.TAG_BANK>0 $com_cond 
	ORDER by c.pub_shipment_date";
	//echo $main_sql;die;
	
	$main_sql_result=sql_select($main_sql);	 
	foreach ($main_sql_result as $key => $row) 
	{
		$shipment_fortnight=get_day_forthnightly($row[csf("pub_shipment_date")]);
		$shipment_fortnight_arr[$shipment_fortnight]=$shipment_fortnight;
		
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
		if($in_hand_value>0)
		{
			$dtls_fortnight_data[$shipment_fortnight][$row[csf("po_break_down_id")]]["company_name"]=$row[csf("company_name")];
			$dtls_fortnight_data[$shipment_fortnight][$row[csf("po_break_down_id")]]["style_owner"]=$row[csf("style_owner")];
			$dtls_fortnight_data[$shipment_fortnight][$row[csf("po_break_down_id")]]["buyer_name"]=$row[csf("buyer_name")];
			$dtls_fortnight_data[$shipment_fortnight][$row[csf("po_break_down_id")]]["dealing_marchant"]=$row[csf("dealing_marchant")];
			$dtls_fortnight_data[$shipment_fortnight][$row[csf("po_break_down_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$dtls_fortnight_data[$shipment_fortnight][$row[csf("po_break_down_id")]]["shiping_status"]=$row[csf("shiping_status")];
			$dtls_fortnight_data[$shipment_fortnight][$row[csf("po_break_down_id")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
			$dtls_fortnight_data[$shipment_fortnight][$row[csf("po_break_down_id")]]["job_no_mst"]=$row[csf("job_no_mst")];
			$dtls_fortnight_data[$shipment_fortnight][$row[csf("po_break_down_id")]]["po_number"]=$row[csf("po_number")];
			$dtls_fortnight_data[$shipment_fortnight][$row[csf("po_break_down_id")]]["po_quantity"]=$row[csf("po_quantity")];
			$dtls_fortnight_data[$shipment_fortnight][$row[csf("po_break_down_id")]]["order_total"]=$row[csf("order_total")];
			$dtls_fortnight_data[$shipment_fortnight][$row[csf("po_break_down_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
			$dtls_fortnight_data[$shipment_fortnight][$row[csf("po_break_down_id")]]["bank_id"]=$row[csf("bank_id")];
			$dtls_fortnight_data[$shipment_fortnight][$row[csf("po_break_down_id")]]["tenor"]=$row[csf("tenor")];
			$dtls_fortnight_data[$shipment_fortnight][$row[csf("po_break_down_id")]]["payment_term"]=$row[csf("payment_term")];
			$dtls_fortnight_data[$shipment_fortnight][$row[csf("po_break_down_id")]]["in_hand_qnty"]=$in_hand_qnty;
			$dtls_fortnight_data[$shipment_fortnight][$row[csf("po_break_down_id")]]["in_hand_value"]=$in_hand_value;
		}
	}
	//echo "<pre>";print_r($dtls_fortnight_data);die;
    ?>
    <div id="report_container" align="center" style="width:1300px">
		<fieldset style="width:1300px;">
		    <table class="rpt_table" border="1" rules="all" width="1300" cellpadding="0" cellspacing="0">
		        <thead>
		            <th width="40">SL</th>
		            <th width="80">Company Name</th> 
		            <th width="80">Working Company</th> 
		            <th width="120">Buyer Name</th> 
		            <th width="110">Job No</th>
                    <th width="110">Style No</th>
		            <th width="110">PO No</th>
                    <th width="80">Pub. Ship Date</th>
		            <th width="110">In Hand Qty.</th>
		            <th width="120">In Hand Value ($)</th>
		            <th width="80">Approximate Payment Date</th>
                    <th width="80">Payment Terms</th>
		            <th>Bank Name</th>
		        </thead>
	            <tbody>
	            	<?
	                $i=1;
					foreach($dtls_fortnight_data as $fort_day=>$fort_val)
					{
						?>
                        <tr>
                        	<td colspan="13" style="font-size:18px; font-weight:bold;">Shipment Schedule from <?= $fort_day; ?></td>
                        </tr>
                        <?
						foreach ($fort_val as $po_id => $row) 
						{
							if($row['tenor']!="") $tenor=$row['tenor']; else $tenor=0;
							$aprox_pay_date=date("d-M-Y",(strtotime($row['pub_shipment_date'])+($tenor*86400)));
							
							if($row['shiping_status']>0) $shiping_status=$row['shiping_status']; else $shiping_status=1;
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td align="center"><? echo $i; ?></td>
								<td><p><? echo $company_arr[$row['company_name']]; ?>&nbsp;</p></td>
								<td><p><? echo $company_arr[$row['style_owner']]; ?>&nbsp;</p></td>
								<td><p><? echo $buyer_arr[$row['buyer_name']]; ?>&nbsp;</p></td>
								<td><p><? echo $row['job_no_mst']; ?>&nbsp;</p></td>
								<td><p><? echo $row['style_ref_no']; ?>&nbsp;</p></td>
								<td><p><? echo $row['po_number']; ?>&nbsp;</p></td>
								<td align="center"><p><? echo change_date_format($row['pub_shipment_date']); ?>&nbsp;</p></td>
								<td align="right"><? echo number_format($row['in_hand_qnty'],2); ?></td>
								<td align="right"><? echo number_format($row['in_hand_value'],2); ?></td>
								<td align="center"><p><? echo change_date_format($aprox_pay_date); ?>&nbsp;</p></td>
								<td><p><? echo $pay_term[$row['payment_term']];?>&nbsp;</p></td>
								<td><p><? echo $bank_arr[$row['bank_id']];?>&nbsp;</p></td>
							</tr>
							<?
							$i++;
							$fort_total_in_hand_qnty+=$row['in_hand_qnty'];
							$fort_total_InHandValue+=$row['in_hand_value'];
							$grand_total_in_hand_qnty+=$row['in_hand_qnty'];
							$grand_total_InHandValue+=$row['in_hand_value'];
						}
						?>
                        <tr bgcolor="#CCCCCC">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td align="right"><strong>Total:</strong></td>
                            <td align="right"><strong><? echo number_format($fort_total_in_hand_qnty,2);?></strong></td>
                            <td align="right"><strong><? echo number_format($fort_total_InHandValue,2);?></strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?
						$fort_total_in_hand_qnty=$fort_total_InHandValue=0;
					}
	                ?>
	            </tbody>
	        	<tfoot>
	                <tr>
	                    <th></th>
	                    <th></th>
	                    <th></th>
	                    <th></th>
                        <th></th>
	                    <th></th>
	                    <th colspan="2" align="right"><strong>Grand Total:</strong></th>
	                    <th align="right"><strong><? echo number_format($grand_total_in_hand_qnty,2);?></strong></th>
	                    <th align="right"><strong><? echo number_format($grand_total_InHandValue,2);?></strong></th>
                        <th></th>
	                    <th></th>
	                    <th></th>
	                </tr>
	            </tfoot>
        	</table>
		</fieldset>
	</div>
    <?
	//die;
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

if ($action=="print_preview_7") // Created by Jahid
{
	//echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	$company_arr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	//$buyer_wise_tenor

	ob_start();
	$exfact_com_cond="";
	if($com_id>0) $exfact_com_cond=" and a.company_id=$com_id";
	$ex_fact_data=return_library_array("SELECT b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");                	
	$com_cond="";
	if($com_id>0) $com_cond=" and a.company_name=$com_id";
	if($bank_id>0) $com_cond.=" and d.tag_bank=$bank_id";
	$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, a.style_ref_no, c.shiping_status, c.id as po_break_down_id, c.job_no_mst, c.po_number, (c.po_quantity*a.total_set_qnty) as po_quantity, c.po_total_price as order_total, c.pub_shipment_date, b.bank_id, b.tenor, b.payment_term
	FROM wo_po_details_master a, lib_buyer b, wo_po_break_down c , lib_buyer_tag_bank d 
	WHERE a.job_no=c.job_no_mst and b.id=a.buyer_name and d.buyer_id=a.buyer_name and b.id=d.buyer_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and d.TAG_BANK>0 $com_cond 
	ORDER by c.pub_shipment_date";
	//echo $main_sql;die;
	
	$main_sql_result=sql_select($main_sql);	 
	foreach ($main_sql_result as $key => $row) 
	{
		if($row[csf('tenor')]!="") $tenor=$row[csf('tenor')]; else $tenor=0;
		$aprox_pay_date=date("d-M-Y",(strtotime($row[csf("pub_shipment_date")])+($tenor*86400)));
		//$shipment_fortnight=get_day_forthnightly($row[csf("pub_shipment_date")]);
		$aprox_pay_month=$aprox_pay_date=date("M-Y",strtotime($aprox_pay_date));
		$aprox_pay_month_arr[$aprox_pay_month]=$aprox_pay_month;
		
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
		if($in_hand_value>0)
		{
			$dtls_fortnight_data[$aprox_pay_month][$row[csf("po_break_down_id")]]["company_name"]=$row[csf("company_name")];
			$dtls_fortnight_data[$aprox_pay_month][$row[csf("po_break_down_id")]]["style_owner"]=$row[csf("style_owner")];
			$dtls_fortnight_data[$aprox_pay_month][$row[csf("po_break_down_id")]]["buyer_name"]=$row[csf("buyer_name")];
			$dtls_fortnight_data[$aprox_pay_month][$row[csf("po_break_down_id")]]["dealing_marchant"]=$row[csf("dealing_marchant")];
			$dtls_fortnight_data[$aprox_pay_month][$row[csf("po_break_down_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$dtls_fortnight_data[$aprox_pay_month][$row[csf("po_break_down_id")]]["shiping_status"]=$row[csf("shiping_status")];
			$dtls_fortnight_data[$aprox_pay_month][$row[csf("po_break_down_id")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
			$dtls_fortnight_data[$aprox_pay_month][$row[csf("po_break_down_id")]]["job_no_mst"]=$row[csf("job_no_mst")];
			$dtls_fortnight_data[$aprox_pay_month][$row[csf("po_break_down_id")]]["po_number"]=$row[csf("po_number")];
			$dtls_fortnight_data[$aprox_pay_month][$row[csf("po_break_down_id")]]["po_quantity"]=$row[csf("po_quantity")];
			$dtls_fortnight_data[$aprox_pay_month][$row[csf("po_break_down_id")]]["order_total"]=$row[csf("order_total")];
			$dtls_fortnight_data[$aprox_pay_month][$row[csf("po_break_down_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
			$dtls_fortnight_data[$aprox_pay_month][$row[csf("po_break_down_id")]]["bank_id"]=$row[csf("bank_id")];
			$dtls_fortnight_data[$aprox_pay_month][$row[csf("po_break_down_id")]]["tenor"]=$row[csf("tenor")];
			$dtls_fortnight_data[$aprox_pay_month][$row[csf("po_break_down_id")]]["payment_term"]=$row[csf("payment_term")];
			$dtls_fortnight_data[$aprox_pay_month][$row[csf("po_break_down_id")]]["in_hand_qnty"]=$in_hand_qnty;
			$dtls_fortnight_data[$aprox_pay_month][$row[csf("po_break_down_id")]]["in_hand_value"]=$in_hand_value;
		}
	}
	//echo "<pre>";print_r($dtls_fortnight_data);die;
    ?>
    <div id="report_container" align="center" style="width:1300px">
		<fieldset style="width:1300px;">
		    <table class="rpt_table" border="1" rules="all" width="1300" cellpadding="0" cellspacing="0">
		        <thead>
		            <th width="40">SL</th>
		            <th width="80">Company Name</th> 
		            <th width="80">Working Company</th> 
		            <th width="120">Buyer Name</th> 
		            <th width="110">Job No</th>
                    <th width="110">Style No</th>
		            <th width="110">PO No</th>
                    <th width="80">Pub. Ship Date</th>
		            <th width="110">In Hand Qty.</th>
		            <th width="120">In Hand Value ($)</th>
		            <th width="80">Approximate Payment Date</th>
                    <th width="80">Payment Terms</th>
		            <th>Bank Name</th>
		        </thead>
	            <tbody>
	            	<?
	                $i=1;
					foreach($dtls_fortnight_data as $fort_day=>$fort_val)
					{
						?>
                        <tr>
                        	<td colspan="13" style="font-size:18px; font-weight:bold;">Approximate Payment Schedule For <?= $fort_day; ?></td>
                        </tr>
                        <?
						foreach ($fort_val as $po_id => $row) 
						{
							if($row['tenor']!="") $tenor=$row['tenor']; else $tenor=0;
							$aprox_pay_date=date("d-M-Y",(strtotime($row['pub_shipment_date'])+($tenor*86400)));
							
							if($row['shiping_status']>0) $shiping_status=$row['shiping_status']; else $shiping_status=1;
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td align="center"><? echo $i; ?></td>
								<td><p><? echo $company_arr[$row['company_name']]; ?>&nbsp;</p></td>
								<td><p><? echo $company_arr[$row['style_owner']]; ?>&nbsp;</p></td>
								<td><p><? echo $buyer_arr[$row['buyer_name']]; ?>&nbsp;</p></td>
								<td><p><? echo $row['job_no_mst']; ?>&nbsp;</p></td>
								<td><p><? echo $row['style_ref_no']; ?>&nbsp;</p></td>
								<td><p><? echo $row['po_number']; ?>&nbsp;</p></td>
								<td align="center"><p><? echo change_date_format($row['pub_shipment_date']); ?>&nbsp;</p></td>
								<td align="right"><? echo number_format($row['in_hand_qnty'],2); ?></td>
								<td align="right"><? echo number_format($row['in_hand_value'],2); ?></td>
								<td align="center"><p><? echo change_date_format($aprox_pay_date); ?>&nbsp;</p></td>
								<td><p><? echo $pay_term[$row['payment_term']];?>&nbsp;</p></td>
								<td><p><? echo $bank_arr[$row['bank_id']];?>&nbsp;</p></td>
							</tr>
							<?
							$i++;
							$fort_total_in_hand_qnty+=$row['in_hand_qnty'];
							$fort_total_InHandValue+=$row['in_hand_value'];
							$grand_total_in_hand_qnty+=$row['in_hand_qnty'];
							$grand_total_InHandValue+=$row['in_hand_value'];
						}
						?>
                        <tr bgcolor="#CCCCCC">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td align="right"><strong>Total:</strong></td>
                            <td align="right"><strong><? echo number_format($fort_total_in_hand_qnty,2);?></strong></td>
                            <td align="right"><strong><? echo number_format($fort_total_InHandValue,2);?></strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?
						$fort_total_in_hand_qnty=$fort_total_InHandValue=0;
					}
	                ?>
	            </tbody>
	        	<tfoot>
	                <tr>
	                    <th></th>
	                    <th></th>
	                    <th></th>
	                    <th></th>
                        <th></th>
	                    <th></th>
	                    <th colspan="2" align="right"><strong>Grand Total:</strong></th>
	                    <th align="right"><strong><? echo number_format($grand_total_in_hand_qnty,2);?></strong></th>
	                    <th align="right"><strong><? echo number_format($grand_total_InHandValue,2);?></strong></th>
                        <th></th>
	                    <th></th>
	                    <th></th>
	                </tr>
	            </tfoot>
        	</table>
		</fieldset>
	</div>
    <?
	//die;
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
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	$company_arr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$dealing_mer_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");

	ob_start();

	/*$com_cond="";
    if($company_id>0) $com_cond=" and a.company_name=$company_id";
	$ex_fact_data=return_library_array("SELECT b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
	//echo "<pre>";print_r($ex_fact_data);die;
	
	

	$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, sum(b.order_quantity) as po_quantity, sum(b.order_total) as order_total, $month_year_cond
	FROM wo_po_details_master a, wo_po_color_size_breakdown b, wo_po_break_down c 
	WHERE a.job_no=c.job_no_mst and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.shiping_status<>3 and c.is_deleted=0 $com_cond 
	GROUP by a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, c.pub_shipment_date 
	ORDER by c.pub_shipment_date asc";*/
	if ($db_type==0) $month_year_select="date_format(c.pub_shipment_date, '%M-%y') as month_year";
	else $month_year_select="to_char(c.pub_shipment_date, 'Month-YY') as month_year";
	$exfact_com_cond="";
	if($com_id>0) $exfact_com_cond=" and a.company_id=$com_id";
	$ex_fact_data=return_library_array("SELECT b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
	//echo "<pre>";print_r($ex_fact_data);die;
	$com_cond="";
	if($com_id>0) $com_cond=" and a.company_name=$com_id";
	if($bank_id>0) $com_cond.=" and d.tag_bank=$bank_id";
	$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id as po_break_down_id, c.job_no_mst, c.po_number, sum(c.po_quantity*a.total_set_qnty) as po_quantity, sum(c.po_total_price) as order_total, $month_year_select
	FROM wo_po_details_master a, lib_buyer b, wo_po_break_down c , lib_buyer_tag_bank d 
	WHERE a.job_no=c.job_no_mst and b.id=a.buyer_name and d.buyer_id=a.buyer_name and b.id=d.buyer_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and d.TAG_BANK>0 $com_cond 
	GROUP by a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id, c.job_no_mst, c.po_number, c.pub_shipment_date 
	ORDER by a.buyer_name asc";
	
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
		$pendin_value =$pendin_qnty*$ord_rate;
        if($pendin_qnty>0)

		{
			$buyer_rowspan[$row[csf('buyer_name')]]++;
			$in_hand_qnty_arr[$row[csf('buyer_name')]]+=$pendin_qnty;
			$in_hand_value_arr[$row[csf('buyer_name')]]+=$pendin_value;
	
			$main_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]]=$row[csf('month_year')];
			$lc_com_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]]['company_name']=$row[csf('company_name')];
			$w_com_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]]['style_owner']=$row[csf('style_owner')];
	
			$month_year_qty_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]][$row[csf('month_year')]]['po_quantity']+=$row[csf("po_quantity")];
			$month_year_qty_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]][$row[csf('month_year')]]['inhand_qty']+=$pendin_qnty;
			$month_year_qty_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]][$row[csf('month_year')]]['in_hand_value']+=$pendin_value;
		}
   		
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
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	$company_arr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$buyer_bank_arr = return_library_array("select id,bank_id from lib_buyer where status_active=1 and is_deleted=0","id","bank_id");
	$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	$location_array = return_library_array("select id, location_name from lib_location where status_active=1 ","id","location_name");
	$dealing_mer_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
	//print_r($location_array);die;

	/*$com_cond="";
    if($company_id>0) $com_cond=" and a.company_name=$company_id";
	$ex_fact_data=return_library_array("select b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
	//echo "<pre>";print_r($ex_fact_data);die;
	
	if ($db_type==0) $month_year_cond="date_format(c.pub_shipment_date, '%M-%y') as month_year";
	else $month_year_cond="to_char(c.pub_shipment_date, 'Month-YY') as month_year";

	$main_sql="select a.company_name, a.style_owner, a.buyer_name, a.location_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, sum(b.order_quantity) as po_quantity, sum(b.order_total) as order_total, $month_year_cond
	from wo_po_details_master a, wo_po_color_size_breakdown b, wo_po_break_down c
	where a.job_no=c.job_no_mst and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and c.is_deleted=0 $com_cond 
	group by a.company_name, a.location_name, a.style_owner, a.buyer_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, c.pub_shipment_date 
	order by c.pub_shipment_date asc";*/
	
	if ($db_type==0) $month_year_select="date_format(c.pub_shipment_date, '%M-%y') as month_year";
	else $month_year_select="to_char(c.pub_shipment_date, 'Month-YY') as month_year";
	$exfact_com_cond="";
	if($com_id>0) $exfact_com_cond=" and a.company_id=$com_id";
	$ex_fact_data=return_library_array("SELECT b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
	//echo "<pre>";print_r($ex_fact_data);die;
	$com_cond="";
	if($com_id>0) $com_cond=" and a.company_name=$com_id";
	if($bank_id>0) $com_cond.=" and d.tag_bank=$bank_id";
	//, a.location_name
	$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id as po_break_down_id, c.job_no_mst, c.po_number, b.bank_id, c.pub_shipment_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.po_total_price as order_total, $month_year_select
	FROM wo_po_details_master a, lib_buyer b, wo_po_break_down c , lib_buyer_tag_bank d 
	WHERE a.job_no=c.job_no_mst and b.id=a.buyer_name and d.buyer_id=a.buyer_name and b.id=d.buyer_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and d.TAG_BANK>0 $com_cond
	order by c.pub_shipment_date";
	
	//echo $main_sql;//die;

	$main_sql_result=sql_select($main_sql);

	$i=0;$test_count=0;$test_value=0;
	foreach ($main_sql_result as $value) 
	{
		/*if($buyer_bank_arr[$value[csf('buyer_name')]])
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
		}*/
		//$value[csf('bank_id')]
		$order_rate=$value[csf('order_total')]/$value[csf('po_quantity')];
		$pending_qnty=$value[csf('po_quantity')]-$ex_fact_data[$value[csf('po_break_down_id')]];
		$pending_value=$pending_qnty*$order_rate;
		if($pending_qnty>0)
		{
			$test_value+=$pending_value;
			$inhand_data_array[$value[csf('bank_id')]][$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["po_quantity"]+=$pending_qnty;
			$inhand_data_array[$value[csf('bank_id')]][$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["order_total"]+=$pending_value;
	
			//$dtls_data[$value[csf('bank_id')]][$value[csf('company_name')]][$value[csf('location_name')]]=$value[csf('location_name')];
			$dtls_data[$value[csf('bank_id')]][$value[csf('company_name')]][$value[csf('style_owner')]]=$value[csf('style_owner')];
			//$summery_data[$value[csf('company_name')]][$value[csf('location_name')]][$value[csf('month_year')]]["tot_qnty"]+=$value[csf('po_quantity')];
			$summery_data[$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_qnty"]+=$pending_qnty;
			//$summery_data[$value[csf('company_name')]][$value[csf('location_name')]][$value[csf('month_year')]]["tot_amt"]+=$value[csf('order_total')];
			$summery_data[$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_amt"]+=$pending_value;
	
			//$bank_wise_summery_data[$value[csf('bank_id')]][$value[csf('location_name')]][$value[csf('month_year')]]["tot_qnty"]+=$value[csf('po_quantity')];
			$bank_wise_summery_data[$value[csf('bank_id')]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_qnty"]+=$pending_qnty;
			//$bank_wise_summery_data[$value[csf('bank_id')]][$value[csf('location_name')]][$value[csf('month_year')]]["tot_amt"]+=$value[csf('order_total')];
			$bank_wise_summery_data[$value[csf('bank_id')]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_amt"]+=$pending_value;
			$all_month[$value[csf('month_year')]]=$value[csf('month_year')];
			$i++;
			$test_count++;
			$test_data[$value[csf('po_break_down_id')]]=$pending_value;
		}
	}
	//echo $test_value;die;
	//echo "<pre>";print_r($test_data);die;
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

	//print_r ( $grand_total_qnty);die;
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
	   					<th bgcolor="#d6fff1" colspan="<? echo ($dtls_tot_colspan*2)+1;?>">Shipment Schedule Of Order In Hand</th>
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
									<td align="right"><? echo $location_val[$month_id]["tot_qnty"]; 
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

function get_day_forthnightly($input_day)
{
    $input_month_year=date("m-Y",strtotime($input_day));
    $first_day="01-".$input_month_year;
    $fortnight_day=date('d-m-Y',strtotime($first_day."+2 week"));
    $last_day=date("t-m-Y", strtotime($input_day));
    $fortnight_first_month='01-15 '.date("M-Y",strtotime($input_day));
    $fortnight_last_month='16-'.date("t", strtotime($input_day)).' '.date("M-Y",strtotime($input_day));
    $difference = strtotime($last_day)-strtotime($first_day);
    $difference_day=(floor($difference / 86400) + 1);
    $forthnight_day_arr=array();
    for($i=1; $i<=$difference_day; $i++)
    {
        if($i==1)
        {
            $forthnight_day_arr[$first_day]=$fortnight_first_month;
            $next_day=date('d-m-Y',strtotime($first_day."+1 day"));
        }
        else
        {
            if(strtotime($next_day)>strtotime($fortnight_day))$fortnight_month=$fortnight_last_month; else $fortnight_month=$fortnight_first_month;
            $forthnight_day_arr[$next_day]=$fortnight_month;
            $next_day=date('d-m-Y',strtotime($next_day."+1 day"));
        }        
    }
    //return $forthnight_day_arr; // return whole month arr
	return $forthnight_day_arr[date('d-m-Y',strtotime($input_day))];// return specific fortnight
}

disconnect($con);
?>
