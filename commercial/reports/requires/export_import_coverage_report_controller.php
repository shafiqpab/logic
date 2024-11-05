<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$seource_des_array=array(3=>"Non EPZ",4=>"Non EPZ",5=>"Abroad",6=>"Abroad",11=>"EPZ",12=>"EPZ");

if ($action=="load_drop_down_year")
{
	$sql=sql_select("select lc_year as lc_sc_year from com_export_lc where beneficiary_name='$data' and status_active=1 and is_deleted=0  union all select sc_year as lc_sc_year from com_sales_contract where beneficiary_name='$data' and status_active=1 and is_deleted=0");
	foreach($sql as $row)
	{
		$lc_sc_year[$row[csf("lc_sc_year")]]=$row[csf("lc_sc_year")];
	}
	echo create_drop_down( "hide_year", 100, $lc_sc_year,"", 1, "-- Select --", $selected, "",0 );
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_date_form=str_replace("'","",$txt_date_form);
	//echo $txt_date_form;die;
	$company_arr=return_library_array( "select company_name,id from  lib_company",'id','company_name');
	$bank_arr=return_library_array( "select bank_name,id from lib_bank",'id','bank_name');
	//$suplier_name_arr=return_library_array( "select id,supplier_name from  lib_supplier",'id','supplier_name');
	//echo $cbo_company_name.'____'.$cbo_buyer_name.'____'.$cbo_lein_bank.'____'.$txt_file_no; die;lc_year sc_year
	$rlz_sql=sql_select("select a.invoice_bill_id, sum(b.document_currency) as rlz_value 
	from  com_export_proceed_realization a, com_export_proceed_rlzn_dtls b 
	where a.id=b.mst_id and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.benificiary_id=$cbo_company_name and a.received_date <='$txt_date_form'
	group by a.invoice_bill_id");
	$rlz_data_array=array();
	foreach($rlz_sql as $row)
	{
		$rlz_data_array[$row[csf("invoice_bill_id")]]+=$row[csf("rlz_value")];
		$rlz_id_arr[$row[csf("invoice_bill_id")]]=$row[csf("invoice_bill_id")];
	}
	$submit_sql=sql_select("select b.invoice_id, sum(b.net_invo_value) as submit_value 
	from  com_export_doc_submission_mst a, com_export_doc_submission_invo b 
	where a.id=b.doc_submission_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.submit_date <='$txt_date_form'
	group by b.invoice_id");
	$submit_data_array=array();
	foreach($submit_sql as $row)
	{
		$submit_data_array[$row[csf("invoice_id")]]+=$row[csf("submit_value")];
	}
	
	$ex_factory_sql=sql_select("select a.po_break_down_id, sum(b.production_qnty) as exfect_qnty 
	from pro_ex_factory_mst a, pro_ex_factory_dtls b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ex_factory_date <='$txt_date_form'
	group by a.po_break_down_id");
	$ex_factory_data_array=array();
	foreach($ex_factory_sql as $row)
	{
		$ex_factory_data_array[$row[csf("po_break_down_id")]]+=$row[csf("exfect_qnty")];
	}
	//echo "<pre>";print_r($ex_factory_data_array);die;
	if($db_type==0) $possiable_date_cond=" and c.possible_reali_date !='0000-00-00'"; else $possiable_date_cond=" and c.possible_reali_date is not null";
	$bill_sql="select c.id as bill_id, a.lien_bank, a.pay_term, c.possible_reali_date, sum(b.net_invo_value) as bill_value, 1 as type 
	from com_sales_contract a, com_export_doc_submission_invo b, com_export_doc_submission_mst c 
	where a.id=b.lc_sc_id and b.doc_submission_mst_id=c.id and b.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.lien_bank>0 and c.submit_date <='$txt_date_form' and c.submit_date <='$txt_date_form' and a.lien_bank in(10,7,13) and c.entry_form=40 $possiable_date_cond  
	group by c.id, a.lien_bank, a.pay_term, c.possible_reali_date
	union all
	select c.id as bill_id, a.lien_bank, a.pay_term, c.possible_reali_date, sum(b.net_invo_value) as bill_value, 2 as type 
	from com_export_lc a, com_export_doc_submission_invo b, com_export_doc_submission_mst c 
	where a.id=b.lc_sc_id and b.doc_submission_mst_id=c.id and b.is_lc=1 and a.ref_closing_status<>1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.lien_bank>0 and c.submit_date <='$txt_date_form' and a.lien_bank in(10,7,13)  and c.entry_form=40 $possiable_date_cond
	group by c.id, a.lien_bank, a.pay_term, c.possible_reali_date
	order by possible_reali_date";
	
	//echo $bill_sql;die;
	//date("Y-m",strtotime($row[csf("ex_factory_date")]))
	$bill_result=sql_select($bill_sql);
	$all_data_arr=array();
	$unrealize_data=array();
	foreach($bill_result as $row)
	{
		$rest_unrlz_amt=$row[csf("bill_value")]-$rlz_data_array[$row[csf("bill_id")]];
		//if($rest_unrlz_amt>0.99)
		if($rlz_id_arr[$row[csf("bill_id")]]=="")
		{
			$test_bill_id.=$row[csf("bill_id")]."=".$row[csf("possible_reali_date")]."=".date("Y-m",strtotime($row[csf("possible_reali_date")]))."**";
			$all_month_data[date("Y-m",strtotime($row[csf("possible_reali_date")]))]=date("Y-m",strtotime($row[csf("possible_reali_date")]));
			if($row[csf("pay_term")]==1)
			{
				$all_data_arr[$row[csf("lien_bank")]][date("Y-m",strtotime($row[csf("possible_reali_date")]))]["at_site_unrealize"]+=$rest_unrlz_amt;
			}
			else
			{
				$all_data_arr[$row[csf("lien_bank")]][date("Y-m",strtotime($row[csf("possible_reali_date")]))]["usence_unrealize"]+=$rest_unrlz_amt;
			}
		}
	}
	//echo $test_bill_id;die;
	//echo count($all_month_data);die;
	//ksort($all_month_data);
	//echo "<pre>";print_r($all_month_data);
	//echo "<pre>";print_r($unrealize_data);
	//die;
	
	//if($db_type==0) $bl_date_cond=" and b.bl_date !='0000-00-00'"; else $bl_date_cond=" and b.bl_date is not null";
	$invoice_sql="select b.id as invoice_id, a.lien_bank, a.pay_term, b.bl_date, b.ex_factory_date, b.net_invo_value as net_invo_value, 1 as type 
	from com_sales_contract a, com_export_invoice_ship_mst b
	where a.id=b.lc_sc_id and b.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.lien_bank>0  and b.invoice_date <='$txt_date_form' and a.lien_bank in(10,7,13) $bl_date_cond
	union all
	select b.id as invoice_id, a.lien_bank, a.pay_term, b.bl_date, b.ex_factory_date, b.net_invo_value as net_invo_value, 2 as type 
	from com_export_lc a, com_export_invoice_ship_mst b 
	where a.id=b.lc_sc_id and b.is_lc=1 and a.ref_closing_status<>1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.lien_bank>0 and b.invoice_date <='$txt_date_form' and a.lien_bank in(10,7,13) $bl_date_cond
	order by bl_date"; 
	
	//echo $invoice_sql;die;
	//date("Y-m",strtotime($row[csf("ex_factory_date")]))
	$invoice_result=sql_select($invoice_sql);
	$unsubmit_data=array();
	foreach($invoice_result as $row)
	{
		$rest_unsub_amt=$row[csf("net_invo_value")]-$submit_data_array[$row[csf("invoice_id")]];
		if($rest_unsub_amt>0.99)
		{
			if($row[csf("bl_date")] !="0000-00-00" && $row[csf("bl_date")] !="")
			{
				if(date("Y-m",strtotime($row[csf("bl_date")]))=="2018-01") 
				{
					$test_data.=$row[csf("invoice_id")]."=".$rest_unsub_amt."=".$row[csf("net_invo_value")]."=".$submit_data_array[$row[csf("invoice_id")]]."*";
				}
				$all_month_data[date("Y-m",strtotime($row[csf("bl_date")]))]=date("Y-m",strtotime($row[csf("bl_date")]));
				if($row[csf("pay_term")]==1)
				{
					$all_data_arr[$row[csf("lien_bank")]][date("Y-m",strtotime($row[csf("bl_date")]))]["at_site_unsubmit"]+=$rest_unsub_amt;
				}
				else
				{
					$all_data_arr[$row[csf("lien_bank")]][date("Y-m",strtotime($row[csf("bl_date")]))]["usence_unsubmit"]+=$rest_unsub_amt;
				}
			}
			else if($row[csf("ex_factory_date")] !="0000-00-00" && $row[csf("ex_factory_date")] !="")
			{
				$bl_date=strtotime($row[csf("ex_factory_date")])+604800;
				$all_month_data[date("Y-m",$bl_date)]=date("Y-m",$bl_date);
				if(date("Y-m",$bl_date)=="2018-01") $test_data.=$row[csf("invoice_id")]."=".$rest_unsub_amt."=".$row[csf("net_invo_value")]."=".$submit_data_array[$row[csf("invoice_id")]]."*";
				if($row[csf("pay_term")]==1)
				{
					$all_data_arr[$row[csf("lien_bank")]][date("Y-m",$bl_date)]["at_site_unsubmit"]+=$rest_unsub_amt;
				}
				else
				{
					$all_data_arr[$row[csf("lien_bank")]][date("Y-m",$bl_date)]["usence_unsubmit"]+=$rest_unsub_amt;
				}
			}
		}
	}
	//echo $test_data;die;
	//echo count($all_month_data);die;
	//ksort($all_month_data);
	//echo "<pre>";print_r($all_month_data);
	//echo "<pre>";print_r($unrealize_data);
	//die;
	
	
	if($db_type==0) $ship_date_cond=" and c.pub_shipment_date !='0000-00-00'"; else $ship_date_cond=" and c.pub_shipment_date is not null";
	$order_sql="select c.id as po_id, c.shiping_status, a.lien_bank, a.pay_term, a.tenor, c.pub_shipment_date, (c.po_quantity*d.total_set_qnty) as po_qnty_pcs, (c.unit_price/d.total_set_qnty) as pcs_unit_price, 1 as type 
	from com_sales_contract a, com_sales_contract_order_info b, wo_po_break_down c, wo_po_details_master d
	where a.id=b.com_sales_contract_id and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no and a.tenor>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.lien_bank>0 and a.lien_bank in(10,7,13) and c.shiping_status<>3 $ship_date_cond
	union all
	select c.id as po_id, c.shiping_status, a.lien_bank, a.pay_term, a.tenor, c.pub_shipment_date, (c.po_quantity*d.total_set_qnty) as po_qnty_pcs, (c.unit_price/d.total_set_qnty) as pcs_unit_price, 2 as type 
	from com_export_lc a, com_export_lc_order_info b, wo_po_break_down c, wo_po_details_master d
	where a.id=b.com_export_lc_id and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no and a.ref_closing_status<>1 and a.tenor>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.lien_bank>0 and a.lien_bank in(10,7,13) and c.shiping_status<>3 $ship_date_cond
	order by pub_shipment_date";
	
	//echo $order_sql;die;
	//date("Y-m",strtotime($row[csf("ex_factory_date")]))
	$order_result=sql_select($order_sql);
	$order_data=array();
	foreach($order_result as $row)
	{
		
		$order_date=(strtotime($row[csf("pub_shipment_date")])+($row[csf("tenor")]*24*60*60));
		$order_rest_qnty=$row[csf("po_qnty_pcs")]-$ex_factory_data_array[$row[csf("po_id")]];
		
		if($order_id_check[$row[csf("po_id")]]=="" && $order_rest_qnty>0.99)
		{
			$order_id_check[$row[csf("po_id")]]=$row[csf("po_id")];
			$all_month_data[date("Y-m",$order_date)]=date("Y-m",$order_date);
			if($row[csf("pay_term")]==1)
			{
				$all_data_arr[$row[csf("lien_bank")]][date("Y-m",$order_date)]["at_site_order"]+=$order_rest_qnty*$row[csf("pcs_unit_price")];
				$order_test_data[$row[csf("lien_bank")]][date("Y-m",$order_date)]["at_site_order"]+=$order_rest_qnty*$row[csf("pcs_unit_price")];
			}
			else
			{
				$all_data_arr[$row[csf("lien_bank")]][date("Y-m",$order_date)]["usence_order"]+=$order_rest_qnty*$row[csf("pcs_unit_price")];
				$order_test_data[$row[csf("lien_bank")]][date("Y-m",$order_date)]["usence_order"]+=$order_rest_qnty*$row[csf("pcs_unit_price")];
			}
			if(date("Y-m",$order_date)=="2014-11")
			{
				$ord_test_data.=$row[csf("po_id")]."=".$row[csf("shiping_status")]."=".$row[csf("po_qnty_pcs")]."=".$ex_factory_data_array[$row[csf("po_id")]]."*";
			}
		}
	}
	//echo $ord_test_data;die;
	//echo "<pre>";print_r($order_test_data);die;
	//echo count($all_month_data);die;
	/*ksort($all_month_data);
	echo "<pre>";print_r($all_month_data);
	echo "<pre>";print_r($order_data);
	die;*/
	$payment_data_array=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amt from com_import_payment where status_active=1 and is_deleted=0 and payment_date <='$txt_date_form' group by invoice_id","invoice_id","paid_amt");
	$btb_sql="select a.id as btb_lc_id, a.lc_date, a.tenor, a.payterm_id, a.lc_category, a.lc_value, a.maturity_from_id, a.issuing_bank_id, b.id as import_inv_id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, sum(c.current_acceptance_value) as edf_loan_value, 1 as type 
	from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
	where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.lc_category>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and a.importer_id=$cbo_company_name and a.issuing_bank_id>0 and a.lc_date <='$txt_date_form' and b.invoice_date <='$txt_date_form' and a.issuing_bank_id in(10,7,13)
	group by a.id, a.lc_date, a.tenor, a.payterm_id, a.lc_category, a.lc_value, a.maturity_from_id, a.issuing_bank_id, b.id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date";
	//echo $btb_sql;die;
	
	/*if($db_type==0)
	{
		$btb_dails_sql="select a.id as btb_lc_id, a.payterm_id, a.lc_category, a.lc_date, a.lc_value from com_btb_lc_master_details a where a.lc_category>0 and a.is_deleted=0 and a.status_active=1 and a.id in($btb_ids)";
	}
	else
	{
		$btb_ids_arr=array_chunk(array_unique(explode(",",$btb_ids)),999);
		$btb_dails_sql="select a.id as btb_lc_id, a.payterm_id, a.lc_category, a.lc_date, a.lc_value from com_btb_lc_master_details a where a.lc_category>0 and a.is_deleted=0 and a.status_active=1";
		$p=1;
		foreach($btb_ids_arr as $btb_id)
		{
			if($p==1) $btb_dails_sql.=" and ( a.id in(".implode(",",$btb_id).")"; else $btb_dails_sql.=" or a.id in(".implode(",",$btb_id).")";
		}
		$btb_dails_sql.=")";
		
	}*/
	
	$btb_sql_result=sql_select($btb_sql);
	foreach($btb_sql_result as $row)
	{
		if($row[csf("nagotiate_date")]!="" && $row[csf("nagotiate_date")]!="0000-00-00" && (abs($row[csf("lc_category")])==3 || abs($row[csf("lc_category")])==5 || abs($row[csf("lc_category")])==11))
		{
			$lc_wise_edf_paid[$row[csf("btb_lc_id")]]+=$row[csf("edf_loan_value")];
		}
		
		if($row[csf("bank_acc_date")]!="" && $row[csf("bank_acc_date")]!="0000-00-00" && (abs($row[csf("lc_category")])!=3 || abs($row[csf("lc_category")])!=5 || abs($row[csf("lc_category")])!=11))
		{
			$lc_wise_edf_paid_btb[$row[csf("btb_lc_id")]]+=$row[csf("edf_loan_value")];
		}
		
		if($row[csf("payterm_id")]==1 && $row[csf("maturity_date")]!="" && $row[csf("maturity_date")]!="0000-00-00")
		{
			$paid_values=0;

			if($row[csf("edf_paid_date")]!="" && $row[csf("edf_paid_date")]!="0000-00-00")
			{
				$paid_values=$row[csf("edf_loan_value")];
			}
			
			if(abs($row[csf("lc_category")])==3 || abs($row[csf("lc_category")])==5 || abs($row[csf("lc_category")])==11)
			{
				$actual_rest_edf=$row[csf("edf_loan_value")]-$paid_values;
				if($actual_rest_edf>0.99)
				{
					$all_month_data[date('Y-m',strtotime($row[csf("maturity_date")]))]=date('Y-m',strtotime($row[csf("maturity_date")]));
					if(date('Y-m',strtotime($row[csf("maturity_date")]))=="2014-10") $edf_test_data.=$row[csf("import_inv_id")]."=".$row[csf("edf_paid_date")]."=".$row[csf("edf_loan_value")]."=".$paid_values."*";
					$all_data_arr[$row[csf("issuing_bank_id")]][date('Y-m',strtotime($row[csf("maturity_date")]))]["edf_actual"]+=$actual_rest_edf;
				}
			}
		}
		
		if($row[csf("payterm_id")]!=1 && $row[csf("maturity_date")]!="" && $row[csf("maturity_date")]!="0000-00-00")
		{
			if(abs($row[csf("lc_category")])!=3 && abs($row[csf("lc_category")])!=5 && abs($row[csf("lc_category")])!=11)
			{
				$actual_rest_btb=$row[csf("edf_loan_value")]-$payment_data_array[$row[csf("import_inv_id")]];
				if($actual_rest_btb>0.99)
				{
					$all_month_data[date('Y-m',strtotime($row[csf("maturity_date")]))]=date('Y-m',strtotime($row[csf("maturity_date")]));
					$all_data_arr[$row[csf("issuing_bank_id")]][date('Y-m',strtotime($row[csf("maturity_date")]))]["btb_actual"]+=$row[csf("edf_loan_value")]-$payment_data_array[$row[csf("import_inv_id")]];
				}
			}
		}
		
		$invoce_data_arr[$row[csf("btb_lc_id")]][$row[csf("import_inv_id")]]["lc_date"]=$row[csf("lc_date")];
		$invoce_data_arr[$row[csf("btb_lc_id")]][$row[csf("import_inv_id")]]["maturity_date"]=$row[csf("maturity_date")];
		$invoce_data_arr[$row[csf("btb_lc_id")]][$row[csf("import_inv_id")]]["edf_paid_date"]=$row[csf("edf_paid_date")];
		$invoce_data_arr[$row[csf("btb_lc_id")]][$row[csf("import_inv_id")]]["edf_loan_value"]=$row[csf("edf_loan_value")];
	}
	//echo $edf_test_data;die;
	//echo count($all_month_data);die;
	/*ksort($all_month_data);
	echo "<pre>";print_r($all_month_data);
	echo "<pre>";print_r($actual_bank_liability);
	die;*/
	
	$btb_id_sql="select b.id as btb_id, b.lc_date, b.tenor, b.payterm_id, b.lc_category, b.lc_value, b.issuing_bank_id, 1 as type 
	from com_btb_export_lc_attachment a,  com_btb_lc_master_details b 
	where a.import_mst_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.is_lc_sc=1 and b.ref_closing_status<>1 and b.importer_id=$cbo_company_name and b.issuing_bank_id>0 and b.ref_closing_status<>1 and b.lc_date <='$txt_date_form' and b.issuing_bank_id in(10,7,13) and b.payterm_id <>3
	group by b.id, b.lc_date, b.tenor, b.payterm_id, b.lc_category, b.lc_value, b.issuing_bank_id
	union all 
	select b.id as btb_id, b.lc_date, b.tenor, b.payterm_id, b.lc_category, b.lc_value, b.issuing_bank_id, 2 as type 
	from com_btb_export_lc_attachment a, com_btb_lc_master_details b 
	where a.import_mst_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.is_lc_sc=0 and b.ref_closing_status<>1 and b.importer_id=$cbo_company_name and b.issuing_bank_id>0 and b.ref_closing_status<>1 and b.lc_date <='$txt_date_form' and b.issuing_bank_id in(10,7,13) and b.payterm_id <>3
	group by b.id, b.lc_date, b.tenor, b.payterm_id, b.lc_category, b.lc_value, b.issuing_bank_id";
	//echo $btb_id_sql;die;
	$btb_id_result=sql_select($btb_id_sql);
	foreach($btb_id_result as $row)
	{
		//$lc_date=add_date($row[csf("lc_date")],$row[csf("tenor")]);
		$lc_date=add_date($txt_date_form,$row[csf("tenor")]);
		//$test_data.=$row[csf("tenor")]."=";
		//$test_data_arr[$row[csf("issuing_bank_id")]][date('Y-m',strtotime($lc_date))]=$lc_date;
		//echo $txt_date_form."=".$row[csf("tenor")]."=".$lc_date;die;
		//if(date('Y-m',strtotime($lc_date))=="2019-09") {echo date('Y-m',strtotime($lc_date))."=".$txt_date_form."=".$row[csf("tenor")]."=".$row[csf("btb_id")];die;}
		
		if($btb_check[$row[csf("btb_id")]]=="")
		{
			$btb_check[$row[csf("btb_id")]]=$row[csf("btb_id")];
			if($row[csf("payterm_id")]==1 && (abs($row[csf("lc_category")])==3 || abs($row[csf("lc_category")])==5 || abs($row[csf("lc_category")])==11))
			{
				$tobe_rest_edf=$row[csf("lc_value")]-$lc_wise_edf_paid[$row[csf("btb_id")]];
				if($tobe_rest_edf>0.99)
				{
					$all_month_data[date('Y-m',strtotime($lc_date))]=date('Y-m',strtotime($lc_date));
					$all_data_arr[$row[csf("issuing_bank_id")]][date('Y-m',strtotime($lc_date))]["edf_create"]+=$row[csf("lc_value")]-$lc_wise_edf_paid[$row[csf("btb_id")]];
					$test_data_arr[$row[csf("issuing_bank_id")]][date('Y-m',strtotime($lc_date))]+=$row[csf("lc_value")]-$lc_wise_edf_paid[$row[csf("btb_id")]];
				}
				
			}
			
			if(abs($row[csf("lc_category")])!=3 && abs($row[csf("lc_category")])!=5 && abs($row[csf("lc_category")])!=11)
			{
				$lc_local_paid=0;
				/*foreach($invoce_data_arr[$row[csf("btb_id")]] as $inv_id=>$value)
				{
					$lc_local_paid+=$payment_data_array[$inv_id];
				}
				$tobe_rest_btb=$row[csf("lc_value")]-$lc_local_paid;*/
				$tobe_rest_btb=$row[csf("lc_value")]-$lc_wise_edf_paid_btb[$row[csf("btb_id")]];
				if($tobe_rest_btb>0.99)
				{
					$all_month_data[date('Y-m',strtotime($lc_date))]=date('Y-m',strtotime($lc_date));
					$all_data_arr[$row[csf("issuing_bank_id")]][date('Y-m',strtotime($lc_date))]["btb_create"]+=$tobe_rest_btb;
				}
			}
		}
	}
	//echo "<pre>";print_r($all_data_arr);die;
	//echo $test_data;die;
	//echo "<pre>";print_r($test_data_arr);die;
	//echo count($all_month_data);die;
	/*ksort($all_month_data);
	echo "<pre>";print_r($all_month_data);
	echo "<pre>";print_r($actual_bank_liability);
	die;*/
	ob_start();
	$div_width=300+(count($all_month_data)*80)+170;
	$table_width=300+(count($all_month_data)*80)+150;
	ksort($all_month_data);
	//echo "<pre>";print_r($all_month_data);die;
	?>
	<div style="width:<? echo $div_width; ?>px;" id="scroll_body">
	<fieldset style="width:100%">
		<table width="<? echo $div_width; ?>" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
			<thead>
				<tr>
					<th width="300">Particulars</th>
					<?
						foreach($all_month_data as $month_data)
						{
							$month_data_ref=explode("-",$month_data);
							?>
							<th width="80"><? echo $months_short[abs($month_data_ref[1])]."-".$month_data_ref[0]; ?></th>
							<?
						}
					?>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
				<?
				$k=1;$i=1;
				$m=1;
				foreach($all_data_arr as $bank_id=>$bank_val)
				{
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td colspan="<? echo count($all_month_data)+2; ?>" title="<? echo $bank_id;?>" style="font-size:16px; font-weight:bold;">Bank Name: <? echo $bank_arr[$bank_id];?>.</td>
					</tr>
					<?
					
					if($i>1)
					{
						$m=$m+1; 
						if($m%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>"> <td colspan="<? echo count($all_month_data)+2; ?>">&nbsp;</td></tr>
						<?
					}
					$i++;
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td width="300" style="font-size:16px; font-weight:bold;">Fund Held at Bank (Hand Writing)</td>
						<?
						foreach($all_month_data as $month_data)
						{
							?>
							<td width="80"></td>
							<?
						}
						?>
						<td></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">ERQ Balance</td>
						<?
						foreach($all_month_data as $month_data)
						{
							?>
							<td></td>
							<?
						}
						?>
						<td></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">DAD Balance</td>
						<?
						foreach($all_month_data as $month_data)
						{
							?>
							<td></td>
							<?
						}
						?>
						<td></td>
					</tr>
					
					
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td style="font-size:14px; font-weight:bold;">Un Realized</td>
						<?
						foreach($all_month_data as $month_data)
						{
							?>
							<td></td>
							<?
						}
						?>
						<td></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">At Sight</td>
						<?
						$at_site_total=0;
						foreach($all_month_data as $month_data)
						{
							?>
							<td align="right"><? echo number_format($bank_val[$month_data]["at_site_unrealize"],2); ?></td>
							<?
							$at_site_total+=$bank_val[$month_data]["at_site_unrealize"];
							$unrlz_month_total[$month_data]+=$bank_val[$month_data]["at_site_unrealize"];
						}
						?>
						<td align="right"><? echo number_format($at_site_total,2); ?></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">Usance</td>
						<?
						$usence_total=0;
						foreach($all_month_data as $month_data)
						{
							?>
							<td align="right"><? echo number_format($bank_val[$month_data]["usence_unrealize"],2); ?></td>
							<?
							$usence_total+=$bank_val[$month_data]["usence_unrealize"];
							$unrlz_month_total[$month_data]+=$bank_val[$month_data]["usence_unrealize"];
						}
						?>
						<td align="right"><? echo number_format($usence_total,2); ?></td>
					</tr>
                    <?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">LC Realization & Others Deduction @ 1% :</td>
						<?
						$unrealize_gt_total=0;
						foreach($all_month_data as $month_data)
						{
							?>
							<td align="right" title="<? echo $unrlz_month_total[$month_data].test; ?>"><? $deduct_amt=($unrlz_month_total[$month_data]/100)*1; echo "- ".number_format($deduct_amt,2); ?></td>
							<?
							$deduct_amt_arr[$month_data]=$deduct_amt;
							$tot_deduct_amt+=$deduct_amt;
						}
						?>
						<td align="right"><? echo " - ".number_format($tot_deduct_amt,2); $tot_deduct_amt=0;?></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">Total :</td>
						<?
						$unrealize_gt_total=0;
						foreach($all_month_data as $month_data)
						{
							$net_unrlz=$unrlz_month_total[$month_data]-$deduct_amt_arr[$month_data];
							?>
							<td align="right"><? echo number_format($net_unrlz,2); ?></td>
							<?
							$unrealize_gt_total+=$net_unrlz;
						}
						?>
						<td align="right"><? echo number_format($unrealize_gt_total,2); ?></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td style="font-size:14px; font-weight:bold;">Unsubmitted Invoice</td>
						<?
						foreach($all_month_data as $month_data)
						{
							?>
							<td></td>
							<?
						}
						?>
						<td></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">At Sight</td>
						<?
						$at_site_total=0;
						foreach($all_month_data as $month_data)
						{
							?>
							<td align="right"><? echo number_format($bank_val[$month_data]["at_site_unsubmit"],2); ?></td>
							<?
							$at_site_total+=$bank_val[$month_data]["at_site_unsubmit"];
							$unsubmit_month_total[$month_data]+=$bank_val[$month_data]["at_site_unsubmit"];
						}
						?>
						<td align="right"><? echo number_format($at_site_total,2); ?></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">Defered</td>
						<?
						$usence_total=0;
						foreach($all_month_data as $month_data)
						{
							?>
							<td align="right"><? echo number_format($bank_val[$month_data]["usence_unsubmit"],2); ?></td>
							<?
							$usence_total+=$bank_val[$month_data]["usence_unsubmit"];
							$unsubmit_month_total[$month_data]+=$bank_val[$month_data]["usence_unsubmit"];
						}
						?>
						<td align="right"><? echo number_format($usence_total,2); ?></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">LC Realization & Others Deduction @ 1% :</td>
						<?
						foreach($all_month_data as $month_data)
						{
							?>
							<td align="right" title="<? echo $unrlz_month_total[$month_data].test; ?>"><? $deduct_inv_qnty=($unsubmit_month_total[$month_data]/100)*1; echo "- ".number_format($deduct_inv_qnty,2); ?></td>
							<?
							$deduct_inv_qnty_arr[$month_data]=$deduct_inv_qnty;
							$tot_deduct_inv_qnty+=$deduct_inv_qnty;
						}
						?>
						<td align="right"><? echo " - ".number_format($tot_deduct_inv_qnty,2);  $tot_deduct_inv_qnty=0; ?></td>
					</tr>
                    <?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">Total :</td>
						<?
						$unsubmit_gt_total=0;
						foreach($all_month_data as $month_data)
						{
							$net_invoice_qnty=$unsubmit_month_total[$month_data]-$deduct_inv_qnty_arr[$month_data];
							?>
							<td align="right"><? echo number_format($net_invoice_qnty,2); ?></td>
							<?
							$unsubmit_gt_total+=$net_invoice_qnty;
						}
						?>
						<td align="right"><? echo number_format($unsubmit_gt_total,2);  ?></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">Un Realized+Unsubmitted Invoice</td>
						<?
						$unrlz_unsub_value_total=0;
						foreach($all_month_data as $month_data)
						{
							$net_unrlz=$unrlz_month_total[$month_data]-$deduct_amt_arr[$month_data];
							$net_unsubmit=$unsubmit_month_total[$month_data]-$deduct_inv_qnty_arr[$month_data];
							$unrlz_unsubmit=$net_unrlz+$net_unsubmit;
							?>
							<td align="right"><? echo number_format(($unrlz_unsubmit),2); ?></td>
							<?
							$unrlz_unsub_value_total+=$unrlz_unsubmit;
						}
						?>
						<td align="right"><? echo number_format($unrlz_unsub_value_total,2);   ?></td>
					</tr>
					
					
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td style="font-size:14px; font-weight:bold;">Order In Hand</td>
						<?
						foreach($all_month_data as $month_data)
						{
							?>
							<td></td>
							<?
						}
						?>
						<td></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">At Sight</td>
						<?
						$at_site_total=0;
						foreach($all_month_data as $month_data)
						{
							?>
							<td align="right"><? echo number_format($bank_val[$month_data]["at_site_order"],2); ?></td>
							<?
							$at_site_total+=$bank_val[$month_data]["at_site_order"];
							$order_month_total[$month_data]+=$bank_val[$month_data]["at_site_order"];
						}
						?>
						<td align="right"><? echo number_format($at_site_total,2); ?></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">Defered</td>
						<?
						$usence_total=0;
						foreach($all_month_data as $month_data)
						{
							?>
							<td align="right"><? echo number_format($bank_val[$month_data]["usence_order"],2); ?></td>
							<?
							$usence_total+=$bank_val[$month_data]["usence_order"];
							$order_month_total[$month_data]+=$bank_val[$month_data]["usence_order"];
						}
						?>
						<td align="right"><? echo number_format($usence_total,2); ?></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">Total :</td>
						<?
						$order_gt_total=0;
						foreach($all_month_data as $month_data)
						{
							?>
							<td align="right"><? echo number_format($order_month_total[$month_data],2); ?></td>
							<?
							$order_gt_total+=$order_month_total[$month_data];
						}
						?>
						<td align="right"><? echo number_format($order_gt_total,2);  ?></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">Un Realized+Unsubmitted Invoice+Order In Hand</td>
						<?
						$unrlz_unsub_order_value_total=0;
						foreach($all_month_data as $month_data)
						{
							$net_unrlz=$unrlz_month_total[$month_data]-$deduct_amt_arr[$month_data];
							$net_unsubmit=$unsubmit_month_total[$month_data]-$deduct_inv_qnty_arr[$month_data];
							$unrlz_unsubmit_inHand=$net_unrlz+$net_unsubmit+$order_month_total[$month_data];
							?>
							<td align="right"><? echo number_format($unrlz_unsubmit_inHand,2); ?></td>
							<?
							$month_unrlz_unsub_order_total[$month_data]=$unrlz_unsubmit_inHand;
							$unrlz_unsub_order_value_total+=$unrlz_unsubmit_inHand;
						}
						?>
						<td align="right"><? echo number_format($unrlz_unsub_order_value_total,2);  ?></td>
					</tr>
				   
					
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td style="font-size:14px; font-weight:bold;">Bank Liabilities</td>
						<?
						foreach($all_month_data as $month_data)
						{
							?>
							<td></td>
							<?
						}
						?>
						<td></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">EDF Payment</td>
						<?
						$edf_actual_total=0;
						foreach($all_month_data as $month_data)
						{
							?>
							<td align="right"><? echo number_format($bank_val[$month_data]["edf_actual"],2); ?></td>
							<?
							$edf_actual_total+=$bank_val[$month_data]["edf_actual"];
							$actual_month_total[$month_data]+=$bank_val[$month_data]["edf_actual"];
						}
						?>
						<td align="right"><? echo number_format($edf_actual_total,2); ?></td>
					</tr>
                   <?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">EDF Interest (provisional amount)</td>
						<?
						$provision_edf_actual_total=0;
						foreach($all_month_data as $month_data)
						{
							$provision_edf_actual=($bank_val[$month_data]["edf_actual"]/100)*3;
							?>
							<td align="right"><? echo number_format($provision_edf_actual,2); ?></td>
							<?
							$provision_edf_actual_arr[$month_data]=$provision_edf_actual;
							$provision_edf_actual_total+=$provision_edf_actual;
						}
						?>
						<td align="right"><? echo number_format($provision_edf_actual_total,2); ?></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">BTB Payment</td>
						<?
						$btb_actual_total=0;
						foreach($all_month_data as $month_data)
						{
							?>
							<td align="right"><? echo number_format($bank_val[$month_data]["btb_actual"],2); ?></td>
							<?
							$btb_actual_total+=$bank_val[$month_data]["btb_actual"];
							$actual_month_total[$month_data]+=$bank_val[$month_data]["btb_actual"];
						}
						?>
						<td align="right"><? echo number_format($btb_actual_total,2); ?></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">Total :</td>
						<?
						$actual_gt_total=0;
						foreach($all_month_data as $month_data)
						{
							$net_actual_month_total=$actual_month_total[$month_data]+$provision_edf_actual_arr[$month_data];
							?>
							<td align="right"><? echo number_format($net_actual_month_total,2); ?></td>
							<?
							$actual_gt_total+=$net_actual_month_total;
							$net_actual_tobe[$month_data]+=$net_actual_month_total;
						}
						?>
						<td align="right"><? echo number_format($actual_gt_total,2);  ?></td>
					</tr>
					
					
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td style="font-size:14px; font-weight:bold;">To be Created</td>
						<?
						foreach($all_month_data as $month_data)
						{
							?>
							<td></td>
							<?
						}
						?>
						<td></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">EDF Payment</td>
						<?
						$edf_actual_total=0;
						foreach($all_month_data as $month_data)
						{
							?>
							<td align="right"><? echo number_format($bank_val[$month_data]["edf_create"],2); ?></td>
							<?
							$edf_actual_total+=$bank_val[$month_data]["edf_create"];
							$tobe_month_total[$month_data]+=$bank_val[$month_data]["edf_create"];
						}
						?>
						<td align="right"><? echo number_format($edf_actual_total,2); ?></td>
					</tr>
                    <?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">EDF Interest (provisional amount)</td>
						<?
						$provision_edf_actual_total=0;
						foreach($all_month_data as $month_data)
						{
							$provision_edf_create=($bank_val[$month_data]["edf_create"]/100)*3;
							?>
							<td align="right"><? echo number_format($provision_edf_create,2); ?></td>
							<?
							$provision_edf_create_arr[$month_data]=$provision_edf_create;
							$provision_edf_actual_total+=$provision_edf_create;
						}
						?>
						<td align="right"><? echo number_format($provision_edf_actual_total,2); ?></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">BTB Payment</td>
						<?
						$btb_actual_total=0;
						foreach($all_month_data as $month_data)
						{
							?>
							<td align="right"><? echo number_format($bank_val[$month_data]["btb_create"],2); ?></td>
							<?
							$btb_actual_total+=$bank_val[$month_data]["btb_create"];
							$tobe_month_total[$month_data]+=$bank_val[$month_data]["btb_create"];
						}
						?>
						<td align="right"><? echo number_format($btb_actual_total,2); ?></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">Total :</td>
						<?
						$tobe_gt_total=0;
						foreach($all_month_data as $month_data)
						{
							$net_created=$tobe_month_total[$month_data]+$provision_edf_create_arr[$month_data];
							?>
							<td align="right"><? echo number_format($net_created,2); ?></td>
							<?
							$actual_gt_total+=$net_created;
							$net_actual_tobe[$month_data]+=$net_created;
						}
						?>
						<td align="right"><? echo number_format($actual_gt_total,2); ?></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">Total Bank Liabilities</td>
						<?
						$liability_create_value_total=0;
						foreach($all_month_data as $month_data)
						{
							?>
							<td align="right"><? echo number_format(($net_actual_tobe[$month_data]),2); ?></td>
							<?
							$liability_create_value_total+=$net_actual_tobe[$month_data];
						}
						?>
						<td align="right"><? echo number_format($liability_create_value_total,2);   ?></td>
					</tr>
					<?
					$m=$m+1;
					if($m%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
						<td align="right">Excess / (Shortage) Fund in <? echo $bank_arr[$bank_id]; ?>.</td>
						<?
						$all_value_total=0;
						foreach($all_month_data as $month_data)
						{
							$short_exe=$month_unrlz_unsub_order_total[$month_data]-$net_actual_tobe[$month_data];
							?>
							<td align="right"><? echo number_format($short_exe,2); ?></td>
							<?
							$all_value_total+=$short_exe;
							$gramd_short_exe[$month_data]+=$short_exe;
						}
						?>
						<td align="right"><? echo number_format($all_value_total,2);?></td>
					</tr>
					<?
					unset($unrlz_month_total); unset($unsubmit_month_total);unset($order_month_total);unset($actual_month_total);unset($tobe_month_total); unset($net_actual_tobe);
					$m++;
				}
				if($m%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
                    <td align="right" style="font-size:14px; font-weight:bold;">Grand Total Excess / (Shortage) Fund</td>
                    <?
                    $gramd_short_exe_total=0;
                    foreach($all_month_data as $month_data)
                    {
                        ?>
                        <td align="right"><? echo number_format($gramd_short_exe[$month_data],2); ?></td>
                        <?
                        $gramd_short_exe_total+=$gramd_short_exe[$month_data];
                    }
                    ?>
                    <td align="right"><? echo number_format($gramd_short_exe_total,2);?></td>
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

disconnect($con);
?>
