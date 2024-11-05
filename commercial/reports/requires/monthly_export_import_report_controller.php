<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');


$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	$datediff_n = datediff( 'd', $txt_date_from, $txt_date_to);
    $date_arr=array();       
	for($k=0; $k<$datediff_n; $k++)
	{
		$newdate_n=add_date(str_replace("'","",$txt_date_from),$k);
		$yare_month=date("Y-m",strtotime($newdate_n));
		$date_arr[$yare_month]=$yare_month;
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	ob_start();
	?>
	<div style="width:1700px;">
		<fieldset style="width:100%">
			<table width="1440" cellpadding="0" cellspacing="0" id="caption" align="left" id="scroll_body">
				<tr>
					<td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:18px">Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr> 
				<tr>  
					<td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
				</tr> 
			</table>
			
			<div style="width:2070px;">
			<table width="2070" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left" id="scroll_body">
				<thead>
					<tr>
						<th width="70" rowspan="2">Month</th>
						<th width="150" rowspan="2">Bank</th>
						<th width="100" rowspan="2">Contract Value</th>
						<th width="100" rowspan="2">Export Lc</th>
						<th width="270" colspan="3">Lien</th>
						<th width="270"colspan="3">Amendment</th>
						<th width="540" colspan="6">Export</th>
						<th width="540"colspan="6">Import</th>
					</tr>
					<tr>
						<th width="90">SC </th>
						<th width="90">LC </th>
						<th width="90">Total </th>
						<th width="90">SC </th>
						<th width="90">LC </th>
						<th width="90">Total </th>
						<th width="90">Ex-Factory Qnty (Invoice)</th>
						<th width="90">Ex-Factory Value (Invoice)</th>
						<th width="90">Bank Submit (Collection)</th>
						<th width="90">Bank Submit (Purchess)</th>
						<th width="90">Possible. Realization.</th>
						<th width="90">Realization</th>
						<th width="90">BTB Value</th>
						<th width="90">TT/FDD Open</th>
						<th width="90">Company Acceptance</th>
						<th width="90">Bank Acceptance</th>
						<th width="90">Maturity Value</th>
						<th >Payment Value</th>
					</tr>
				</thead>
		    </table>
			<div style="width:2070px; overflow-y: scroll; max-height: 380px;" align="left" >
			<table width="2070" class="rpt_table" rules="all" border="1" id="scroll_body" >
				<tbody class="rpt_table">
				<?
				$cbo_month_from=str_pad($cbo_month_from,2,"0",STR_PAD_LEFT);
				//$export_sc=return_field_value("$group_concat(distinct doc_submission_mst_id) as id","com_export_doc_submission_invo","is_lc=1 and lc_sc_id in(".implode(',',$lc_id_arr).")","id");
				$sql_lc_sc_bank=sql_select("select id as sc_lc_id, lien_bank, contract_date as date_sc, 2 as type 
				from com_sales_contract where status_active=1 and is_deleted=0 and beneficiary_name='$cbo_company_name' and lien_bank>0
				union all 
				select id as sc_lc_id, lien_bank, lc_date as date_sc, 1 as type 
				from com_export_lc where status_active=1 and is_deleted=0 and beneficiary_name='$cbo_company_name' and lien_bank>0");
				foreach($sql_lc_sc_bank as $row)
				{
					if($row[csf("lien_bank")])
					{
						$sc_lc_wise_bank[$row[csf("sc_lc_id")]][$row[csf("type")]]=$row[csf("lien_bank")];
						$bank_array[date("Y-m",strtotime($row[csf("date_sc")]))][$row[csf("lien_bank")]]=$row[csf("lien_bank")];
					}
				}
				//echo "<pre>";print_r($bank_array['2021-01']);die;
				
				$sql_sc="select a.id as sc_id, a.contract_date as date_sc, a.lien_date, b.amendment_date, a.contract_value as contract_value, (case when a.lien_bank > 0 then a.contract_value else 0 end) as lien_contract_val, a.lien_bank, (case when a.lien_bank > 0 then a.id else 0 end) as lien_contract_id, a.lien_bank, b.id as amd_id, b.amendment_value as amendment_value 
				from com_sales_contract a left join com_sales_contract_amendment b on a.id=b.contract_id and b.status_active=1 and b.is_deleted=0 and b.amendment_value>0
				where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name='$cbo_company_name'";
				//echo $sql_sc;die;
				$sql_sc_result=sql_select($sql_sc);
				foreach($sql_sc_result as $row)
				{
					//echo date("Y-m",strtotime($row[csf("date_sc")]));die;
					if($sc_id_check[$row[csf("sc_id")]]=="")
					{
						$sc_id_check[$row[csf("sc_id")]]=$row[csf("sc_id")];
						if(strtotime($row[csf("date_sc")])>=strtotime($txt_date_from) && strtotime($row[csf("date_sc")])<=strtotime($txt_date_to))
						{
							$sc_data_arr[date("Y-m",strtotime($row[csf("date_sc")]))][$row[csf("lien_bank")]]["contract_value"]+=$row[csf("contract_value")];
							$sc_data_arr[date("Y-m",strtotime($row[csf("date_sc")]))][$row[csf("lien_bank")]]["contract_id"].=$row[csf("sc_id")].",";
						}
						if($row[csf("lien_contract_id")]>0 && strtotime($row[csf("lien_date")])>=strtotime($txt_date_from) && strtotime($row[csf("lien_date")])<=strtotime($txt_date_to))
						{
							$sc_data_arr[date("Y-m",strtotime($row[csf("lien_date")]))][$row[csf("lien_bank")]]["lien_contract_val"]+=$row[csf("lien_contract_val")];
							$sc_data_arr[date("Y-m",strtotime($row[csf("lien_date")]))][$row[csf("lien_bank")]]["lien_contract_id"].=$row[csf("lien_contract_id")].",";
						}
					}
					
					if($sc_amend_check[$row[csf("amd_id")]]=="" && $row[csf("lien_bank")]>0 && strtotime($row[csf("amendment_date")])>=strtotime($txt_date_from) && strtotime($row[csf("amendment_date")])<=strtotime($txt_date_to))
					{
						$sc_amend_check[$row[csf("amd_id")]]=$row[csf("amd_id")];
						$sc_data_arr[date("Y-m",strtotime($row[csf("amendment_date")]))][$row[csf("lien_bank")]]["amendment_contract_id"].=$row[csf("sc_id")].",";
						$sc_data_arr[date("Y-m",strtotime($row[csf("amendment_date")]))][$row[csf("lien_bank")]]["amendment_value"]+=$row[csf("amendment_value")];
					}
				}
				
				unset($sql_sc_result);
				//echo "<pre>";print_r($sc_data_arr);die;
				
				$sql_lc="select a.id as lc_id, a.lc_date as date_lc, a.lien_date, b.amendment_date, a.lc_value as lc_value, (case when a.lien_bank > 0 then a.lc_value else 0 end) as lien_lc_val, (case when a.lien_bank > 0 then a.id else 0 end) as lien_lc_id, a.lien_bank, b.id as amd_id, b.amendment_value as amendment_value
				from com_export_lc a left join com_export_lc_amendment b on a.id=b.export_lc_id and b.status_active=1 and b.is_deleted=0 and b.amendment_value>0
				where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name='$cbo_company_name'";
				//echo $sql_lc;
				$sql_lc_result=sql_select($sql_lc);
				foreach($sql_lc_result as $row)
				{
					if($lc_id_check[$row[csf("lc_id")]]=="")
					{
						$lc_id_check[$row[csf("lc_id")]]=$row[csf("lc_id")];
						if(strtotime($row[csf("date_lc")])>=strtotime($txt_date_from) && strtotime($row[csf("date_lc")])<=strtotime($txt_date_to))
						{
							$lc_data_arr[date("Y-m",strtotime($row[csf("date_lc")]))][$row[csf("lien_bank")]]["lc_value"]+=$row[csf("lc_value")];
							$lc_data_arr[date("Y-m",strtotime($row[csf("date_lc")]))][$row[csf("lien_bank")]]["lc_id"].=$row[csf("lc_id")].",";
						}
						if($row[csf("lien_lc_id")]>0 && strtotime($row[csf("lien_date")])>=strtotime($txt_date_from) && strtotime($row[csf("lien_date")])<=strtotime($txt_date_to))
						{
							$lc_data_arr[date("Y-m",strtotime($row[csf("lien_date")]))][$row[csf("lien_bank")]]["lien_lc_val"]+=$row[csf("lien_lc_val")];
							$lc_data_arr[date("Y-m",strtotime($row[csf("lien_date")]))][$row[csf("lien_bank")]]["lien_lc_id"].=$row[csf("lien_lc_id")].",";
						}
					}
					
					if($lc_amend_check[$row[csf("amd_id")]]=="" && $row[csf("lien_bank")]>0 && strtotime($row[csf("amendment_date")])>=strtotime($txt_date_from) && strtotime($row[csf("amendment_date")])<=strtotime($txt_date_to))
					{
						$lc_amend_check[$row[csf("amd_id")]]=$row[csf("amd_id")];
						$lc_data_arr[date("Y-m",strtotime($row[csf("amendment_date")]))][$row[csf("lien_bank")]]["amendment_lc_id"].=$row[csf("lc_id")].",";
						$lc_data_arr[date("Y-m",strtotime($row[csf("amendment_date")]))][$row[csf("lien_bank")]]["amendment_value"]+=$row[csf("amendment_value")];
					}
				}
				unset($sql_lc_result);
				
				//echo "<pre>";print_r($lc_data_arr);die;
				
				$sql_invoice="select id, is_lc, lc_sc_id, ex_factory_date as date_invoice, invoice_quantity, invoice_value, net_invo_value 
				from com_export_invoice_ship_mst 
				where status_active=1 and is_deleted=0 and benificiary_id='$cbo_company_name' and ex_factory_date between '$txt_date_from' and '$txt_date_to'";
				$sql_invoice_result=sql_select($sql_invoice);
				foreach($sql_invoice_result as $row)
				{
					//if($row[csf("is_lc")]==1) $lean_bank=$lc_wise_bank[$row[csf("lc_sc_id")]]; else $lean_bank=$sc_wise_bank[$row[csf("lc_sc_id")]];
					$lean_bank=$sc_lc_wise_bank[$row[csf("lc_sc_id")]][$row[csf("is_lc")]];
					if($lean_bank) $bank_array[date("Y-m",strtotime($row[csf("date_invoice")]))][$lean_bank]=$lean_bank;
					if($lean_bank>0)
					{
						$invoice_data_arr[date("Y-m",strtotime($row[csf("date_invoice")]))][$lean_bank]["invoice_id"].=$row[csf("id")].",";
						$invoice_data_arr[date("Y-m",strtotime($row[csf("date_invoice")]))][$lean_bank]["invoice_quantity"]+=$row[csf("invoice_quantity")];
						$invoice_data_arr[date("Y-m",strtotime($row[csf("date_invoice")]))][$lean_bank]["invoice_value"]+=$row[csf("invoice_value")];
						$invoice_data_arr[date("Y-m",strtotime($row[csf("date_invoice")]))][$lean_bank]["net_invo_value"]+=$row[csf("net_invo_value")];
					}
				}
				unset($sql_invoice_result);
				
				//echo "<pre>";print_r($invoice_data_arr);die;
				
				$sql_submission="select b.is_lc, b.lc_sc_id, a.submit_date as date_sub, (case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, (case when a.submit_type=1 then a.id else 0 end) as sub_collection_id, (case when a.submit_type=2 then b.net_invo_value else 0 end) as sub_purchase, (case when a.submit_type=2 then a.id else 0 end) as sub_purchase_id  
				from com_export_doc_submission_mst a, com_export_doc_submission_invo b 
				where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.doc_submission_mst_id and submit_date between '$txt_date_from' and '$txt_date_to' and entry_form=40";
				//echo $sql_submission;
				
				$sql_submission_result=sql_select($sql_submission);
				foreach($sql_submission_result as $row)
				{
					$lean_bank=$sc_lc_wise_bank[$row[csf("lc_sc_id")]][$row[csf("is_lc")]];
					if($lean_bank) $bank_array[date("Y-m",strtotime($row[csf("date_sub")]))][$lean_bank]=$lean_bank;
					if($lean_bank>0)
					{
						$sub_data_arr[date("Y-m",strtotime($row[csf("date_sub")]))][$lean_bank]["sub_collection"]+=$row[csf("sub_collection")];
						if($row[csf("sub_collection_id")] > 0 && $sub_cul_check[$row[csf("sub_collection_id")]]=="")
						{
							$sub_cul_check[$row[csf("sub_collection_id")]]=$row[csf("sub_collection_id")];
							$sub_data_arr[date("Y-m",strtotime($row[csf("date_sub")]))][$lean_bank]["sub_collection_id"].=$row[csf("sub_collection_id")].",";
						}
						$sub_data_arr[date("Y-m",strtotime($row[csf("date_sub")]))][$lean_bank]["sub_purchase"]+=$row[csf("sub_purchase")];
						if($row[csf("sub_purchase_id")] > 0 && $sub_pur_check[$row[csf("sub_purchase_id")]]=="")
						{
							$sub_pur_check[$row[csf("sub_purchase_id")]]=$row[csf("sub_purchase_id")];
							$sub_data_arr[date("Y-m",strtotime($row[csf("date_sub")]))][$lean_bank]["sub_purchase_id"].=$row[csf("sub_purchase_id")].",";
						}
					}
				}
				unset($sql_submission_result);
				
				//echo "<pre>";print_r($sub_data_arr);die;
				
				$sql_realization="select p.is_lc, p.lc_sc_id, a.id as rlz_id, a.received_date, b.id as rlz_dtls_id, b.document_currency 
				from  com_export_doc_submission_invo p, com_export_proceed_realization a, com_export_proceed_rlzn_dtls b 
				where p.doc_submission_mst_id=a.invoice_bill_id and a.id=b.mst_id and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.received_date between '$txt_date_from' and '$txt_date_to'";
				//echo $sql_realization;//die;
				$sql_realization_result=sql_select($sql_realization);
				foreach($sql_realization_result as $row)
				{
					$lean_bank=$sc_lc_wise_bank[$row[csf("lc_sc_id")]][$row[csf("is_lc")]];
					if($lean_bank) $bank_array[date("Y-m",strtotime($row[csf("received_date")]))][$lean_bank]=$lean_bank;
					if($lean_bank>0 && $rlz_dtls_id_check[$row[csf("rlz_dtls_id")]]=="")
					{
						$rlz_dtls_id_check[$row[csf("rlz_dtls_id")]]=$row[csf("rlz_dtls_id")];
						$rlz_data_arr[date("Y-m",strtotime($row[csf("received_date")]))][$lean_bank]["document_currency"]+=$row[csf("document_currency")];
					}
					if($lean_bank>0 && $rlz_id_check[$row[csf("rlz_id")]]=="")
					{
						$rlz_id_check[$row[csf("rlz_id")]]=$row[csf("rlz_id")];
						$rlz_data_arr[date("Y-m",strtotime($row[csf("received_date")]))][$lean_bank]["rlz_id"].=$row[csf("rlz_id")].",";
					}
				}
				unset($sql_realization_result);
				
				//echo "<pre>";print_r($rlz_data_arr);die;
				$btb_lc_wise_bank=sql_select("select id as btb_id, issuing_bank_id, lc_date
				from com_btb_lc_master_details 
				where status_active=1 and is_deleted=0 and importer_id='$cbo_company_name'");
				$lc_wise_bank=array();
				foreach($btb_lc_wise_bank as $row)
				{
					$lc_wise_bank[$row[csf("btb_id")]]=$row[csf("issuing_bank_id")];
					if($row[csf("issuing_bank_id")]) $bank_array[date("Y-m",strtotime($row[csf("lc_date")]))][$row[csf("issuing_bank_id")]]=$row[csf("issuing_bank_id")];
					//$bank_array[$row[csf("issuing_bank_id")]]=$row[csf("issuing_bank_id")];
				}
				//echo "<pre>";print_r($lc_wise_bank);die;
				unset($btb_lc_wise_bank);
				
				$btb_lc="select id as btb_id, issuing_bank_id, lc_date, payterm_id, lc_value as btb_lc_value 
				from com_btb_lc_master_details 
				where status_active=1 and is_deleted=0 and importer_id='$cbo_company_name' and lc_date between '$txt_date_from' and '$txt_date_to'";
				//echo $btb_lc;
				$btb_lc_result=sql_select($btb_lc);
				foreach($btb_lc_result as $row)
				{
					if($row[csf("payterm_id")]==3)
					{
						$btb_data_arr[date("Y-m",strtotime($row[csf("lc_date")]))][$row[csf("issuing_bank_id")]]["btb_lc_value_tt"]+=$row[csf("btb_lc_value")];
						$btb_data_arr[date("Y-m",strtotime($row[csf("lc_date")]))][$row[csf("issuing_bank_id")]]["btb_id_tt"].=$row[csf("btb_id")].",";
					}
					else
					{
						$btb_data_arr[date("Y-m",strtotime($row[csf("lc_date")]))][$row[csf("issuing_bank_id")]]["btb_lc_value"]+=$row[csf("btb_lc_value")];
						$btb_data_arr[date("Y-m",strtotime($row[csf("lc_date")]))][$row[csf("issuing_bank_id")]]["btb_id"].=$row[csf("btb_id")].",";
					}
				}
				//echo "<pre>";print_r($btb_data_arr);die;
				unset($btb_lc_result);
				
				$company_acceptance="select a.id as inv_id, b.btb_lc_id, a.company_acc_date, b.current_acceptance_value 
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c
				where a.id=b.import_invoice_id and b.btb_lc_id=c.id and c.payterm_id<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_acc_date between '$txt_date_from' and '$txt_date_to'";
				$company_acceptance_result=sql_select($company_acceptance);
				foreach($company_acceptance_result as $row)
				{
					$issu_bank=$lc_wise_bank[$row[csf("btb_lc_id")]];
					if($issu_bank) $bank_array[date("Y-m",strtotime($row[csf("company_acc_date")]))][$issu_bank]=$issu_bank;
					if($issu_bank>0 && $row[csf("company_acc_date")] != "" && $row[csf("company_acc_date")] != "0000-00-00")
					{
						$com_accep_data_arr[date("Y-m",strtotime($row[csf("company_acc_date")]))][$issu_bank]["company_accp_value"]+=$row[csf("current_acceptance_value")];
						if($inv_id_check[$row[csf("inv_id")]]=="")
						{
							$inv_id_check[$row[csf("inv_id")]]=$row[csf("inv_id")];
							$com_accep_data_arr[date("Y-m",strtotime($row[csf("company_acc_date")]))][$issu_bank]["company_accp_id"].=$row[csf("inv_id")].",";
						}
					}
				}
				unset($company_acceptance_result);
				
						
				$bank_acceptance="select a.id as inv_id, b.btb_lc_id, a.bank_acc_date, b.current_acceptance_value 
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c
				where a.id=b.import_invoice_id and b.btb_lc_id=c.id and c.payterm_id<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.bank_acc_date between '$txt_date_from' and '$txt_date_to'";
				$bank_acceptance_result=sql_select($bank_acceptance);
				foreach($bank_acceptance_result as $row)
				{
					$issu_bank=$lc_wise_bank[$row[csf("btb_lc_id")]];
					if($issu_bank) $bank_array[date("Y-m",strtotime($row[csf("bank_acc_date")]))][$issu_bank]=$issu_bank;
					if($issu_bank>0 && $row[csf("bank_acc_date")] != "" && $row[csf("bank_acc_date")] != "0000-00-00")
					{
						$bank_accep_data_arr[date("Y-m",strtotime($row[csf("bank_acc_date")]))][$issu_bank]["bank_accp_value"]+=$row[csf("current_acceptance_value")];
						if($bank_inv_id_check[$row[csf("inv_id")]]=="")
						{
							$bank_accep_data_arr[$row[csf("inv_id")]]=$row[csf("inv_id")];
							$bank_accep_data_arr[date("Y-m",strtotime($row[csf("bank_acc_date")]))][$issu_bank]["bank_accp_id"].=$row[csf("inv_id")].",";
						}
					}
					
				}
				unset($bank_acceptance_result);
				
				
				$maturity_acceptance="select a.id as inv_id, b.btb_lc_id, a.maturity_date, b.current_acceptance_value 
				from com_import_invoice_mst a, com_import_invoice_dtls b
				where a.id=b.import_invoice_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.maturity_date between '$txt_date_from' and '$txt_date_to'";
				//echo $company_acceptance;die;
				$maturity_acceptance_result=sql_select($maturity_acceptance);
				foreach($maturity_acceptance_result as $row)
				{
					$issu_bank=$lc_wise_bank[$row[csf("btb_lc_id")]];
					if($issu_bank) $bank_array[date("Y-m",strtotime($row[csf("maturity_date")]))][$issu_bank]=$issu_bank;
					if($issu_bank>0 && $row[csf("maturity_date")] != "" && $row[csf("maturity_date")] != "0000-00-00")
					{
						$mature_accep_data_arr[date("Y-m",strtotime($row[csf("maturity_date")]))][$issu_bank]["matured_value"]+=$row[csf("current_acceptance_value")];
						if($mature_inv_id_check[$row[csf("inv_id")]]=="")
						{
							$mature_inv_id_check[$row[csf("inv_id")]]=$row[csf("inv_id")];
							$mature_accep_data_arr[date("Y-m",strtotime($row[csf("maturity_date")]))][$issu_bank]["matured_id"].=$row[csf("inv_id")].",";
						}
					}
				}
				unset($maturity_acceptance_result);
				
				//echo $sql_payment;die;
				
				$sql_payment="select b.id as pay_id, c.id as accep_dtls_id, a.id as inv_id, b.payment_date, b.accepted_ammount as accepted_ammount, a.edf_paid_date, d.issuing_bank_id, d.lc_category, a.retire_source
				from COM_IMPORT_PAYMENT_COM b, com_import_invoice_mst a, com_import_invoice_dtls c, com_btb_lc_master_details d  
				where b.invoice_id=a.id and a.id=c.import_invoice_id and c.btb_lc_id=d.id and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.PAYTERM_ID=1 and b.payment_date between '$txt_date_from' and '$txt_date_to' and d.importer_id='$cbo_company_name'
				union all
				select b.id as pay_id, c.id as accep_dtls_id, a.id as inv_id, b.payment_date, b.accepted_ammount as accepted_ammount, a.edf_paid_date, d.issuing_bank_id, d.lc_category, a.retire_source
				from com_import_payment b, com_import_invoice_mst a, com_import_invoice_dtls c, com_btb_lc_master_details d  
				where b.invoice_id=a.id and a.id=c.import_invoice_id and c.btb_lc_id=d.id and b.status_active=1 and b.is_deleted=0 and d.PAYTERM_ID=2 and b.payment_date between '$txt_date_from' and '$txt_date_to' and d.importer_id='$cbo_company_name'";
				//echo $sql_payment;die;
				
				$sql_payment_result=sql_select($sql_payment);
				
				foreach($sql_payment_result as $row)
				{
					$issu_bank=$row[csf("issuing_bank_id")];
					if($pay_id_check[$row[csf("pay_id")]]=="")
					{
						$pay_id_check[$row[csf("pay_id")]]=$row[csf("pay_id")];
						if($issu_bank)
						{
							$bank_array[date("Y-m",strtotime($row[csf("payment_date")]))][$issu_bank]=$issu_bank;
							$pay_data_arr[date("Y-m",strtotime($row[csf("payment_date")]))][$issu_bank]["accepted_ammount"]+=$row[csf("accepted_ammount")];
							$pay_data_arr[date("Y-m",strtotime($row[csf("payment_date")]))][$issu_bank]["accepted_id"].=$row[csf("inv_id")].",";
							$total_payed_amt+=$row[csf("accepted_ammount")];
						}
						$total_payed_amt2+=$row[csf("accepted_ammount")];
					}
					$total_payed_amt3+=$row[csf("accepted_ammount")];			
				}
				unset($sql_payment_result);
				//echo $test_data.test;die;
				//echo "<pre> $total_payed_amt = $total_payed_amt2 = $total_payed_amt3";print_r($pay_data_arr);die;
				
				$k=1;
				foreach($date_arr as $year_month=>$year_data)
				{
					foreach($bank_array[$year_month] as $bank_id=>$bank_val)
					{
						if ($k%2==0)
						$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
							<td  width="70"style="word-break:break-all;"><? $month_id=explode("-",$year_month); echo $months[(int)$month_id[1]];?></td>
							<td width="150" style="word-break:break-all;" title="<?= $bank_id;?>"><? echo $bank_arr[$bank_id];?></td>
							<td width="100" style="word-break:break-all;"align="right"><a href="#report_details" onclick="open_details('<? echo chop($sc_data_arr[$year_month][$bank_id]['contract_id'],',');?>','contract_details','Contract Details','1300','SC');"><? echo number_format($sc_data_arr[$year_month][$bank_id]["contract_value"],2); $total_sc +=$sc_data_arr[$year_month][$bank_id]["contract_value"];?></a></td>

							<td width="100" style="word-break:break-all; "align="right"><a href="#report_details" onclick="open_details('<? echo chop($lc_data_arr[$year_month][$bank_id]['lc_id'],',');?>','contract_details','LC Details','1300','LC');"><? echo number_format($lc_data_arr[$year_month][$bank_id]["lc_value"],2); $total_lc +=$lc_data_arr[$year_month][$bank_id]["lc_value"]; ?></a></td>

							<td width="90" align="right"><a href="#report_details" onclick="open_details('<? echo chop($sc_data_arr[$year_month][$bank_id]['lien_contract_id'],',');?>','contract_details','Contract Details','1300','SC');"><? echo number_format($sc_data_arr[$year_month][$bank_id]["lien_contract_val"],2); $total_lein_sc +=$sc_data_arr[$year_month][$bank_id]["lien_contract_val"]; ?></a></td>

							<td width="90"align="right"><a href="#report_details" onclick="open_details('<? echo chop($lc_data_arr[$year_month][$bank_id]['lien_lc_id'],',');?>','contract_details','LC Details','1300','LC');"><? echo number_format($lc_data_arr[$year_month][$bank_id]["lien_lc_val"],2); $total_lein_lc +=$lc_data_arr[$year_month][$bank_id]["lien_lc_val"]; ?></a></td>

							<td width="90" align="right"><? $total_lein=$sc_data_arr[$year_month][$bank_id]["lien_contract_val"]+$lc_data_arr[$year_month][$bank_id]["lien_lc_val"]; echo number_format($total_lein,2); $total_lein_sc_lc +=$total_lein; ?></td>
							
							<td width="90"align="right"><a href="#report_details" onclick="open_details('<? echo chop($sc_data_arr[$year_month][$bank_id]['amendment_contract_id'],',');?>','amendment_details','Contract Details','1300','SC');"><? echo number_format($sc_data_arr[$year_month][$bank_id]["amendment_value"],2); $total_amendment_sc +=$sc_data_arr[$year_month][$bank_id]["amendment_value"]; ?></a></td>

							<td  width="90"align="right"><a href="#report_details" onclick="open_details('<? echo chop($lc_data_arr[$year_month][$bank_id]['amendment_lc_id'],',');?>','amendment_details','LC Details','1300','LC');"><? echo number_format($lc_data_arr[$year_month][$bank_id]["amendment_value"],2); $total_amendment_lc +=$lc_data_arr[$year_month][$bank_id]["amendment_value"]; ?></a></td>
												
							<td width="90"align="right"><? $total_amendment=$sc_data_arr[$year_month][$bank_id]["amendment_value"]+$lc_data_arr[$year_month][$bank_id]["amendment_value"]; echo number_format($total_lein,2); $total_amendment_sc_lc +=$total_amendment; ?></td>
							
							<td width="90" align="right"><a href="#report_details" onclick="open_details('<? echo chop($invoice_data_arr[$year_month][$bank_id]['invoice_id'],',');?>','invoice_details','Invoice Details','1300','Qnty');"><? echo number_format($invoice_data_arr[$year_month][$bank_id]["invoice_quantity"],0); $total_inv_qnty +=$invoice_data_arr[$year_month][$bank_id]["invoice_quantity"]; ?></a></td>

							<td width="90" align="right"><a href="#report_details" onclick="open_details('<? echo chop($invoice_data_arr[$year_month][$bank_id]['invoice_id'],',');?>','invoice_details','Invoice Details','1300','Amt');"><? echo number_format($invoice_data_arr[$year_month][$bank_id]["net_invo_value"],2); $total_inv_val +=$invoice_data_arr[$year_month][$bank_id]["net_invo_value"]; ?></a></td>

							<td width="90" align="right"><a href="#report_details" onclick="open_details('<? echo chop($sub_data_arr[$year_month][$bank_id]['sub_collection_id'],',');?>','submission_details','Submission Details','1300','Collection');"><? echo number_format($sub_data_arr[$year_month][$bank_id]["sub_collection"],2); $total_sub_collection +=$sub_data_arr[$year_month][$bank_id]["sub_collection"]; ?></a></td>

							<td width="90" align="right"><a href="#report_details" onclick="open_details('<? echo chop($sub_data_arr[$year_month][$bank_id]['sub_purchase_id'],',');?>','submission_details','Submission Details','1300','Purchase');"><? echo number_format($sub_data_arr[$year_month][$bank_id]["sub_purchase"],2); $total_sub_purchase +=$sub_data_arr[$year_month][$bank_id]["sub_purchase"]; ?></a></td>

							<td width="90" align="right"><a href="#report_details" onclick="open_details('<? echo chop($sub_data_arr[$year_month][$bank_id]['sub_collection_id'],',');?>','submission_details2','Possible. Realization Details','1300','Collection');"><? echo number_format($sub_data_arr[$year_month][$bank_id]["sub_collection"],2); $total_sub_possibole_realization +=$sub_data_arr[$year_month][$bank_id]["sub_collection"]; ?></a></td>
							
							<td width="90" align="right"><a href="#report_details" onclick="open_details('<? echo chop($rlz_data_arr[$year_month][$bank_id]['rlz_id'],',');?>','realization_details','Realization Details','1000','Value');"><? echo number_format($rlz_data_arr[$year_month][$bank_id]["document_currency"],2); $total_rlz +=$rlz_data_arr[$year_month][$bank_id]["document_currency"]; ?></a></td>
							<td width="90"align="right"><a href="#report_details" onclick="open_details('<? echo chop($btb_data_arr[$year_month][$bank_id]['btb_id'],','); ?>','btb_details','BTB Details','950','Value');"><? echo number_format($btb_data_arr[$year_month][$bank_id]["btb_lc_value"],2); $total_btb +=$btb_data_arr[$year_month][$bank_id]["btb_lc_value"]; ?></a></td>
							<td width="90"align="right"><a href="#report_details" onclick="open_details('<? echo chop($btb_data_arr[$year_month][$bank_id]['btb_id_tt'],','); ?>','btb_details','BTB Details','950','Value');"><? echo number_format($btb_data_arr[$year_month][$bank_id]["btb_lc_value_tt"],2); $total_btb_tt +=$btb_data_arr[$year_month][$bank_id]["btb_lc_value_tt"]; ?></a></td>
							
							<td width="90" align="right"><a href="#report_details" onclick="open_details('<? echo chop($com_accep_data_arr[$year_month][$bank_id]['company_accp_id'],','); ?>','accep_details','Company Acceptance Details','1300','com');"><? echo number_format($com_accep_data_arr[$year_month][$bank_id]["company_accp_value"] ,2); $total_accp_company +=$com_accep_data_arr[$year_month][$bank_id]["company_accp_value"] ; ?></a></td>
												
							<td width="90" align="right"><a href="#report_details" onclick="open_details('<? echo chop($bank_accep_data_arr[$year_month][$bank_id]['bank_accp_id'],','); ?>','bank_details','Bank Acceptance Details','1300','bank');"><? echo number_format($bank_accep_data_arr[$year_month][$bank_id]["bank_accp_value"],2); $total_accp_bank +=$bank_accep_data_arr[$year_month][$bank_id]["bank_accp_value"];?></a></td>
							<td width="90" align="right"><a href="#report_details" onclick="open_details('<? echo chop($mature_accep_data_arr[$year_month][$bank_id]['matured_id'],','); ?>','bank_details_mature','Maturity Details','1300','maturity');"><? echo number_format($mature_accep_data_arr[$year_month][$bank_id]["matured_value"],2); $total_accp_matured +=$mature_accep_data_arr[$year_month][$bank_id]["matured_value"]; ?></a></td>
							<td width="90" align="right"><a href="#report_details" onclick="open_details('<? echo chop($pay_data_arr[$year_month][$bank_id]['accepted_id'],','); ?>','payment_details','Payment Details','1300','payment','<?= $txt_date_from;?>','<?= $txt_date_to; ?>','<?= $bank_id; ?>');"><? echo number_format($pay_data_arr[$year_month][$bank_id]["accepted_ammount"],2); $total_pay +=$pay_data_arr[$year_month][$bank_id]["accepted_ammount"];?></a></td>
						</tr>
						<?
						$k++;
						$all_sub_collection_id.=$sub_data_arr[$year_month][$bank_id]['sub_collection_id'];
						
						$all_sub_col_id.=$sc_data_arr[$year_month][$bank_id]["contract_id"];
						$all_lc_data.=$lc_data_arr[$year_month][$bank_id]["lc_id"];
						$all_sc_data_arr.=$sc_data_arr[$year_month][$bank_id]["lien_contract_id"];
						$all_lc_data_arr.=$lc_data_arr[$year_month][$bank_id]["lien_lc_id"];
						$all_amendment_contract_data.=$sc_data_arr[$year_month][$bank_id]["amendment_contract_id"];
						$all_amendment_lc_data.=$lc_data_arr[$year_month][$bank_id]["amendment_lc_id"];
						$all_invoice_data.=$invoice_data_arr[$year_month][$bank_id]["invoice_id"];
						$all_sub_purchase_data.=$sub_data_arr[$year_month][$bank_id]["sub_purchase_id"];

						$all_realization_data.=$rlz_data_arr[$year_month][$bank_id]["rlz_id"];
						$all_btb_data.=$btb_data_arr[$year_month][$bank_id]["btb_id"];
						$all_btb_tt_data.=$btb_data_arr[$year_month][$bank_id]["btb_id_tt"];
						$all_com_accep_data.=$com_accep_data_arr[$year_month][$bank_id]["company_accp_id"];
						$all_bank_accep_data.=$bank_accep_data_arr[$year_month][$bank_id]["bank_accp_id"];
						$all_mature_accep_data.=$mature_accep_data_arr[$year_month][$bank_id]["matured_id"];
						$all_pay_data.=$pay_data_arr[$year_month][$bank_id]["accepted_id"];
					}
				}
				?>
				
				</tbody>
				<tfoot>
					<th></th>
					<th></th>
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_sub_col_id,','); ?>',
					'contract_details','Contract Details','1300','SC');"><? echo number_format($total_sc,2); ?></th>



					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_lc_data,','); ?>','contract_details','LC Details','1300','LC');"><? echo number_format($total_lc,2); ?></th>

					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_sc_data_arr,','); ?>','contract_details','Contract Details','1300','SC');"><? echo number_format($total_lein_sc,2); ?></th>



					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_lc_data_arr,','); ?>','contract_details','LC Details','1300','LC');"><? echo number_format($total_lein_lc,2); ?></th>
					<th align="right"><? echo number_format($total_lein_sc_lc,2); ?></th>
					
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_amendment_contract_data,','); ?>','amendment_details','Contract Details','1300','SC');"><? echo number_format($total_amendment_sc,2); ?></th>


					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_amendment_lc_data,','); ?>','amendment_details','LC Details','1300','LC');"><? echo number_format($total_amendment_lc,2); ?></th>


					<th align="right"><? echo number_format($total_amendment_sc_lc,2); ?></th>
					
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_invoice_data,','); ?>','invoice_details','Invoice Details','1300','Qnty');"><? echo number_format($total_inv_qnty,0); ?></th>

					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_invoice_data,','); ?>','invoice_details','Invoice Details','1300','Qnty');"><? echo number_format($total_inv_val,2); ?></th>
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_sub_collection_id,','); ?>','submission_details','Submission Details','1300','Collection');"><? echo number_format($total_sub_collection,2); ?></a></th>

					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_sub_purchase_data,','); ?>','submission_details','Submission Details','1300','Purchase');"><? echo number_format($total_sub_purchase,2); ?></th>
					
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_sub_collection_id,','); ?>','submission_details2','Possible. Realization Details','1300','Purchase');"><? echo number_format($total_sub_possibole_realization,2); ?></th>
					
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_realization_data,','); ?>','realization_details','Realization Details','1000','Value');"><? echo number_format($total_rlz,2); ?></a></th>
				
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_btb_data,','); ?>','btb_details','BTB Details','950','Value');"><? echo number_format($total_btb,2); ?></a></th>
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_btb_tt_data,','); ?>','btb_details','BTB Details','950','Value');"><? echo number_format($total_btb_tt,2); ?></a></th>
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_com_accep_data,','); ?>','accep_details','Company Acceptance Details','1300','com');"><? echo number_format($total_accp_company,2); ?></a></th>
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_bank_accep_data,','); ?>','bank_details','Bank Acceptance Details','1300','bank');"><? echo number_format($total_accp_bank,2); ?></a></th>
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_mature_accep_data,','); ?>','bank_details_mature','Maturity Details','1300','maturity');"><? echo number_format($total_accp_matured,2); ?></a></th>
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_pay_data,','); ?>','payment_details','Payment Details','1300','payment','<?= "";?>','<?= ""; ?>','<?= "0"; ?>');"><? echo number_format($total_pay,2); ?></a></th>
				</tfoot>
			</table>
		
		</fieldset>
		</div>
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

if($action=="report_generate_2")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	 $cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	$datediff_n = datediff( 'd', $txt_date_from, $txt_date_to);
    $date_arr=array();       
	for($k=0; $k<$datediff_n; $k++)
	{
		$newdate_n=add_date(str_replace("'","",$txt_date_from),$k);
		$yare_month=date("Y-m",strtotime($newdate_n));
		$date_arr[$yare_month]=$yare_month;
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	ob_start();
	?>
	<div style="width:1700px;" id="scroll_body">
		<fieldset style="width:100%">
			<table width="1440" cellpadding="0" cellspacing="0" id="caption" align="left">
				<tr>
				<?
					$company_library=sql_select("SELECT id, COMPANY_NAME from lib_company where id in($cbo_company_name)");
					foreach( $company_library as $row)
					{
						$company_name.=$row["COMPANY_NAME"].", ";
					}
					?>
					<td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:18px">Company Name : <? echo rtrim($company_name,", "); ?></strong></td>
				</tr> 
				<tr>  
					<td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
				</tr> 
			</table>
			
			
			<table width="1970" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
				<thead>
					<tr>
						<th width="70" rowspan="2">Month</th>
						<th width="150" rowspan="2">Bank</th>
						<th width="100" rowspan="2">Contract Value</th>
						<th width="100" rowspan="2">Export Lc</th>
						<th colspan="3">Lien</th>
						<th colspan="3">Amendment</th>
						<th colspan="5">Export</th>
						<th colspan="6">Import</th>
					</tr>
					<tr>
						<th width="90">SC </th>
						<th width="90">LC </th>
						<th width="90">Total </th>
						<th width="90">SC </th>
						<th width="90">LC </th>
						<th width="90">Total </th>
						<th width="90">Ex-Factory Qnty (Invoice)</th>
						<th width="90">Ex-Factory Value (Invoice)</th>
						<th width="90">Bank Submit (Collection)</th>
						<th width="90">Bank Submit (Purchess)</th>
						<th width="90">Realization</th>
						<th width="90">BTB Value</th>
						<th width="90">TT/FDD Open</th>
						<th width="90">Company Acceptance</th>
						<th width="90">Bank Acceptance</th>
						<th width="90">Maturity Value</th>
						<th >Payment Value</th>
					</tr>
				</thead>
				<tbody>
				<?
				$cbo_month_from=str_pad($cbo_month_from,2,"0",STR_PAD_LEFT);
				//$export_sc=return_field_value("$group_concat(distinct doc_submission_mst_id) as id","com_export_doc_submission_invo","is_lc=1 and lc_sc_id in(".implode(',',$lc_id_arr).")","id");
				/*if($db_type==2)
				{
					$sql_sc=sql_select("select LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as sc_id, to_char(contract_date,'YYYY-MM') as date_sc, sum(contract_value) as contract_value,sum(case when lien_bank!=0 then contract_value else 0 end) as lien_contract_val, lien_bank from com_sales_contract where status_active=1 and is_deleted=0 and beneficiary_name='$cbo_company_name' and contract_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'  group by to_char(contract_date,'YYYY-MM'), lien_bank");
					
					$sql_lc=sql_select("select LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as lc_id, to_char(lc_date,'YYYY-MM') as date_lc, sum(lc_value) as lc_value, to_char(lc_date,'YYYY-MM') as date_lc, sum(case when lien_bank!=0 then lc_value else 0 end) as lien_lc_val, lien_bank from com_export_lc where status_active=1 and is_deleted=0 and beneficiary_name='$cbo_company_name' and lc_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'  group by to_char(lc_date,'YYYY-MM'), lien_bank");
					
					$sql_invoice=sql_select("select LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as invoice_id, to_char(ex_factory_date,'YYYY-MM') as date_invoice, sum(invoice_quantity) as invoice_quantity, sum(invoice_value) as invoice_value, sum(net_invo_value) as net_invo_value from com_export_invoice_ship_mst where status_active=1 and is_deleted=0 and benificiary_id='$cbo_company_name' and ex_factory_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' and ex_factory_date is not null  group by   to_char(ex_factory_date,'YYYY-MM')");
					
					$sql_submission=sql_select("select LISTAGG(CAST(a.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.id) as submission_id, to_char(a.submit_date,'YYYY-MM') as date_sub, sum(case when a.submit_type=1 then b.net_invo_value  else 0 end) as sub_collection, sum(case when a.submit_type=2 then b.net_invo_value  else 0 end) as sub_purchase  from com_export_doc_submission_mst a,  com_export_doc_submission_invo b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.doc_submission_mst_id and submit_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' and ENTRY_FORM=40  group by to_char(a.submit_date,'YYYY-MM')");
					$sql_realization=sql_select("select LISTAGG(CAST(a.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.id) as rlz_id, to_char(a.received_date,'YYYY-MM') as date_rlz, sum(b.document_currency) as document_currency from  com_export_proceed_realization a, com_export_proceed_rlzn_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and a.received_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'  group by to_char(a.received_date,'YYYY-MM')");
					
					$btb_lc=sql_select("select LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as btb_lc_id, to_char(lc_date,'YYYY-MM') as date_btb, sum(lc_value) as btb_lc_value from com_btb_lc_master_details where status_active=1 and is_deleted=0 and lc_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'  group by to_char(lc_date,'YYYY-MM')");
					
					
					$company_acceptance=sql_select("select LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as btb_inv_id, to_char(company_acc_date,'YYYY-MM') as date_company_accp, sum(document_value) as company_accp_value from com_import_invoice_mst where status_active=1 and is_deleted=0 and company_acc_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' and company_acc_date is not null  group by to_char(company_acc_date,'YYYY-MM')");
					
					
					$bank_acceptance=sql_select("select LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as btb_inv_id, to_char(bank_acc_date,'YYYY-MM') as date_bank_accp, sum(document_value) as bank_accp_value from com_import_invoice_mst where status_active=1 and is_deleted=0 and bank_acc_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' and bank_acc_date is not null group by to_char(bank_acc_date,'YYYY-MM')");
					
					$maturity_acceptance=sql_select("select LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as btb_inv_id, to_char(maturity_date,'YYYY-MM') as date_maturity_accp,sum(document_value) as matured_value from com_import_invoice_mst where status_active=1 and is_deleted=0 and maturity_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' and maturity_date is not null  group by to_char(maturity_date,'YYYY-MM')");
					
					$sql_payment=sql_select("select LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as pay_id, to_char(payment_date,'YYYY-MM') as date_pay, sum(accepted_ammount) as accepted_ammount from com_import_payment  where status_active=1 and is_deleted=0 and payment_date between  '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' group by to_char(payment_date,'YYYY-MM')");
				}
				else if($db_type==0)
				{
					$sql_sc=sql_select("select group_concat(id) as sc_id, date_format(contract_date,'%Y-%m') as date_sc, sum(contract_value) as contract_value,sum(case when lien_bank!=0 then contract_value else 0 end) as lien_contract_val from com_sales_contract where status_active=1 and is_deleted=0 and beneficiary_name='$cbo_company_name' and contract_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'  group by date_format(contract_date,'%Y-%m')");
					
					$sql_lc=sql_select("select group_concat(id) as lc_id, date_format(lc_date,'%Y-%m') as date_lc, sum(lc_value) as lc_value,sum(case when lien_bank!=0 then lc_value else 0 end) as lien_lc_val from com_export_lc where status_active=1 and is_deleted=0 and beneficiary_name='$cbo_company_name' and lc_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'  group by date_format(lc_date,'%Y-%m')");

					$sql_invoice=sql_select("select group_concat(id) as invoice_id, date_format(ex_factory_date,'%Y-%m') as date_invoice, sum(invoice_quantity) as invoice_quantity, sum(invoice_value) as invoice_value , sum(net_invo_value) as net_invo_value from com_export_invoice_ship_mst where status_active=1 and is_deleted=0 and benificiary_id='$cbo_company_name' and ex_factory_date!='0000-00-00' and ex_factory_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'  group by date_format(ex_factory_date,'%Y-%m')");
					
					$sql_submission=sql_select("select group_concat(a.id) as submission_id, date_format(submit_date,'%Y-%m') as date_sub, sum(case when a.submit_type=1 then b.net_invo_value  else 0 end) as sub_collection, sum(case when a.submit_type=2 then b.net_invo_value  else 0 end) as sub_purchase  from com_export_doc_submission_mst a,  com_export_doc_submission_invo b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.doc_submission_mst_id and submit_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."' and ENTRY_FORM=40  group by date_format(submit_date,'%Y-%m')");
					
					$sql_realization=sql_select("select group_concat(a.id) as rlz_id, date_format(received_date,'%Y-%m') as date_rlz, sum(b.document_currency) as document_currency from  com_export_proceed_realization a, com_export_proceed_rlzn_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and a.received_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'  group by date_format(received_date,'%Y-%m')");
					
					$btb_lc=sql_select("select group_concat(id) as btb_lc_id, date_format(lc_date,'%Y-%m') as date_btb, sum(lc_value) as btb_lc_value from com_btb_lc_master_details where status_active=1 and is_deleted=0 and lc_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'  group by date_format(lc_date,'%Y-%m')");
					
					
					$company_acceptance=sql_select("select group_concat(id) as btb_inv_id,  date_format(company_acc_date,'%Y-%m') as date_company_accp, sum(document_value) as company_accp_value from com_import_invoice_mst where status_active=1 and is_deleted=0 and company_acc_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."' and company_acc_date!='0000-00-00' group by date_format(company_acc_date,'%Y-%m')");
					
					
					$bank_acceptance=sql_select("select group_concat(id) as btb_inv_id,  date_format(bank_acc_date,'%Y-%m') as date_bank_accp, sum(document_value) as bank_accp_value from com_import_invoice_mst where status_active=1 and is_deleted=0 and bank_acc_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."' and bank_acc_date!='0000-00-00' group by date_format(bank_acc_date,'%Y-%m')");
					
					$maturity_acceptance=sql_select("select group_concat(id) as btb_inv_id,  date_format(maturity_date,'%Y-%m') as date_maturity_accp,sum(document_value) as matured_value from com_import_invoice_mst where status_active=1 and is_deleted=0 and maturity_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."' and maturity_date!='0000-00-00'  group by date_format(maturity_date,'%Y-%m')");
					
					$sql_payment=sql_select("select group_concat(id) as pay_id, date_format(payment_date,'%Y-%m') as date_pay, sum(accepted_ammount) as accepted_ammount from com_import_payment  where status_active=1 and is_deleted=0 and payment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'  group by date_format(payment_date,'%Y-%m')");
					
				}*/
				
				$sql_lc_sc_bank=sql_select("select id as sc_lc_id, lien_bank, contract_date as date_sc, 2 as type 
				from com_sales_contract where status_active=1 and is_deleted=0 and beneficiary_name in($cbo_company_name) and lien_bank>0
				union all 
				select id as sc_lc_id, lien_bank, lc_date as date_sc, 1 as type 
				from com_export_lc where status_active=1 and is_deleted=0 and beneficiary_name in($cbo_company_name) and lien_bank>0");
				foreach($sql_lc_sc_bank as $row)
				{
					if($row[csf("lien_bank")])
					{
						$sc_lc_wise_bank[$row[csf("sc_lc_id")]][$row[csf("type")]]=$row[csf("lien_bank")];
						$bank_array[date("Y-m",strtotime($row[csf("date_sc")]))][$row[csf("lien_bank")]]=$row[csf("lien_bank")];
					}
				}
				//echo "<pre>";print_r($bank_array['2021-01']);die;
				
				$sql_sc="select a.id as sc_id, a.contract_date as date_sc, a.lien_date, b.amendment_date, a.contract_value as contract_value, (case when a.lien_bank > 0 then a.contract_value else 0 end) as lien_contract_val, a.lien_bank, (case when a.lien_bank > 0 then a.id else 0 end) as lien_contract_id, a.lien_bank, b.id as amd_id, b.amendment_value as amendment_value 
				from com_sales_contract a left join com_sales_contract_amendment b on a.id=b.contract_id and b.status_active=1 and b.is_deleted=0 and b.amendment_value>0
				where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name in($cbo_company_name)";
				//echo $sql_sc;die;
				$sql_sc_result=sql_select($sql_sc);
				foreach($sql_sc_result as $row)
				{
					//echo date("Y-m",strtotime($row[csf("date_sc")]));die;
					if($sc_id_check[$row[csf("sc_id")]]=="")
					{
						$sc_id_check[$row[csf("sc_id")]]=$row[csf("sc_id")];
						if(strtotime($row[csf("date_sc")])>=strtotime($txt_date_from) && strtotime($row[csf("date_sc")])<=strtotime($txt_date_to))
						{
							$sc_data_arr[date("Y-m",strtotime($row[csf("date_sc")]))][$row[csf("lien_bank")]]["contract_value"]+=$row[csf("contract_value")];
							$sc_data_arr[date("Y-m",strtotime($row[csf("date_sc")]))][$row[csf("lien_bank")]]["contract_id"].=$row[csf("sc_id")].",";
						}
						if($row[csf("lien_contract_id")]>0 && strtotime($row[csf("lien_date")])>=strtotime($txt_date_from) && strtotime($row[csf("lien_date")])<=strtotime($txt_date_to))
						{
							$sc_data_arr[date("Y-m",strtotime($row[csf("lien_date")]))][$row[csf("lien_bank")]]["lien_contract_val"]+=$row[csf("lien_contract_val")];
							$sc_data_arr[date("Y-m",strtotime($row[csf("lien_date")]))][$row[csf("lien_bank")]]["lien_contract_id"].=$row[csf("lien_contract_id")].",";
						}
					}
					
					if($sc_amend_check[$row[csf("amd_id")]]=="" && $row[csf("lien_bank")]>0 && strtotime($row[csf("amendment_date")])>=strtotime($txt_date_from) && strtotime($row[csf("amendment_date")])<=strtotime($txt_date_to))
					{
						$sc_amend_check[$row[csf("amd_id")]]=$row[csf("amd_id")];
						$sc_data_arr[date("Y-m",strtotime($row[csf("amendment_date")]))][$row[csf("lien_bank")]]["amendment_contract_id"].=$row[csf("sc_id")].",";
						$sc_data_arr[date("Y-m",strtotime($row[csf("amendment_date")]))][$row[csf("lien_bank")]]["amendment_value"]+=$row[csf("amendment_value")];
					}
				}
				
				unset($sql_sc_result);
				//echo "<pre>";print_r($sc_data_arr);die;
				
				$sql_lc="select a.id as lc_id, a.lc_date as date_lc, a.lien_date, b.amendment_date, a.lc_value as lc_value, (case when a.lien_bank > 0 then a.lc_value else 0 end) as lien_lc_val, (case when a.lien_bank > 0 then a.id else 0 end) as lien_lc_id, a.lien_bank, b.id as amd_id, b.amendment_value as amendment_value
				from com_export_lc a left join com_export_lc_amendment b on a.id=b.export_lc_id and b.status_active=1 and b.is_deleted=0 and b.amendment_value>0
				where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name in($cbo_company_name)";
				//echo $sql_lc;
				$sql_lc_result=sql_select($sql_lc);
				foreach($sql_lc_result as $row)
				{
					if($lc_id_check[$row[csf("lc_id")]]=="")
					{
						$lc_id_check[$row[csf("lc_id")]]=$row[csf("lc_id")];
						if(strtotime($row[csf("date_lc")])>=strtotime($txt_date_from) && strtotime($row[csf("date_lc")])<=strtotime($txt_date_to))
						{
							$lc_data_arr[date("Y-m",strtotime($row[csf("date_lc")]))][$row[csf("lien_bank")]]["lc_value"]+=$row[csf("lc_value")];
							$lc_data_arr[date("Y-m",strtotime($row[csf("date_lc")]))][$row[csf("lien_bank")]]["lc_id"].=$row[csf("lc_id")].",";
						}
						if($row[csf("lien_lc_id")]>0 && strtotime($row[csf("lien_date")])>=strtotime($txt_date_from) && strtotime($row[csf("lien_date")])<=strtotime($txt_date_to))
						{
							$lc_data_arr[date("Y-m",strtotime($row[csf("lien_date")]))][$row[csf("lien_bank")]]["lien_lc_val"]+=$row[csf("lien_lc_val")];
							$lc_data_arr[date("Y-m",strtotime($row[csf("lien_date")]))][$row[csf("lien_bank")]]["lien_lc_id"].=$row[csf("lien_lc_id")].",";
						}
					}
					
					if($lc_amend_check[$row[csf("amd_id")]]=="" && $row[csf("lien_bank")]>0 && strtotime($row[csf("amendment_date")])>=strtotime($txt_date_from) && strtotime($row[csf("amendment_date")])<=strtotime($txt_date_to))
					{
						$lc_amend_check[$row[csf("amd_id")]]=$row[csf("amd_id")];
						$lc_data_arr[date("Y-m",strtotime($row[csf("amendment_date")]))][$row[csf("lien_bank")]]["amendment_lc_id"].=$row[csf("lc_id")].",";
						$lc_data_arr[date("Y-m",strtotime($row[csf("amendment_date")]))][$row[csf("lien_bank")]]["amendment_value"]+=$row[csf("amendment_value")];
					}
				}
				unset($sql_lc_result);
				
				//echo "<pre>";print_r($lc_data_arr);die;
				
				$sql_invoice="select id, is_lc, lc_sc_id, ex_factory_date as date_invoice, invoice_quantity, invoice_value, net_invo_value 
				from com_export_invoice_ship_mst 
				where status_active=1 and is_deleted=0 and benificiary_id in($cbo_company_name) and ex_factory_date between '$txt_date_from' and '$txt_date_to'";
				$sql_invoice_result=sql_select($sql_invoice);
				foreach($sql_invoice_result as $row)
				{
					//if($row[csf("is_lc")]==1) $lean_bank=$lc_wise_bank[$row[csf("lc_sc_id")]]; else $lean_bank=$sc_wise_bank[$row[csf("lc_sc_id")]];
					$lean_bank=$sc_lc_wise_bank[$row[csf("lc_sc_id")]][$row[csf("is_lc")]];
					if($lean_bank) $bank_array[date("Y-m",strtotime($row[csf("date_invoice")]))][$lean_bank]=$lean_bank;
					if($lean_bank>0)
					{
						$invoice_data_arr[date("Y-m",strtotime($row[csf("date_invoice")]))][$lean_bank]["invoice_id"].=$row[csf("id")].",";
						$invoice_data_arr[date("Y-m",strtotime($row[csf("date_invoice")]))][$lean_bank]["invoice_quantity"]+=$row[csf("invoice_quantity")];
						$invoice_data_arr[date("Y-m",strtotime($row[csf("date_invoice")]))][$lean_bank]["invoice_value"]+=$row[csf("invoice_value")];
						$invoice_data_arr[date("Y-m",strtotime($row[csf("date_invoice")]))][$lean_bank]["net_invo_value"]+=$row[csf("net_invo_value")];
					}
				}
				unset($sql_invoice_result);
				
				//echo "<pre>";print_r($invoice_data_arr);die;
				
				$sql_submission="select b.is_lc, b.lc_sc_id, a.submit_date as date_sub, (case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, (case when a.submit_type=1 then a.id else 0 end) as sub_collection_id, (case when a.submit_type=2 then b.net_invo_value else 0 end) as sub_purchase, (case when a.submit_type=2 then a.id else 0 end) as sub_purchase_id  
				from com_export_doc_submission_mst a, com_export_doc_submission_invo b 
				where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.doc_submission_mst_id and submit_date between '$txt_date_from' and '$txt_date_to' and entry_form=40";
				//echo $sql_submission;
				
				$sql_submission_result=sql_select($sql_submission);
				foreach($sql_submission_result as $row)
				{
					$lean_bank=$sc_lc_wise_bank[$row[csf("lc_sc_id")]][$row[csf("is_lc")]];
					if($lean_bank) $bank_array[date("Y-m",strtotime($row[csf("date_sub")]))][$lean_bank]=$lean_bank;
					if($lean_bank>0)
					{
						$sub_data_arr[date("Y-m",strtotime($row[csf("date_sub")]))][$lean_bank]["sub_collection"]+=$row[csf("sub_collection")];
						if($row[csf("sub_collection_id")] > 0 && $sub_cul_check[$row[csf("sub_collection_id")]]=="")
						{
							$sub_cul_check[$row[csf("sub_collection_id")]]=$row[csf("sub_collection_id")];
							$sub_data_arr[date("Y-m",strtotime($row[csf("date_sub")]))][$lean_bank]["sub_collection_id"].=$row[csf("sub_collection_id")].",";
						}
						$sub_data_arr[date("Y-m",strtotime($row[csf("date_sub")]))][$lean_bank]["sub_purchase"]+=$row[csf("sub_purchase")];
						if($row[csf("sub_purchase_id")] > 0 && $sub_pur_check[$row[csf("sub_purchase_id")]]=="")
						{
							$sub_pur_check[$row[csf("sub_purchase_id")]]=$row[csf("sub_purchase_id")];
							$sub_data_arr[date("Y-m",strtotime($row[csf("date_sub")]))][$lean_bank]["sub_purchase_id"].=$row[csf("sub_purchase_id")].",";
						}
					}
				}
				unset($sql_submission_result);
				
				//echo "<pre>";print_r($sub_data_arr);die;
				
				$sql_realization="select p.is_lc, p.lc_sc_id, a.id as rlz_id, a.received_date, b.id as rlz_dtls_id, b.document_currency 
				from  com_export_doc_submission_invo p, com_export_proceed_realization a, com_export_proceed_rlzn_dtls b 
				where p.doc_submission_mst_id=a.invoice_bill_id and a.id=b.mst_id and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.received_date between '$txt_date_from' and '$txt_date_to'";
				//echo $sql_realization;//die;
				$sql_realization_result=sql_select($sql_realization);
				foreach($sql_realization_result as $row)
				{
					$lean_bank=$sc_lc_wise_bank[$row[csf("lc_sc_id")]][$row[csf("is_lc")]];
					if($lean_bank) $bank_array[date("Y-m",strtotime($row[csf("received_date")]))][$lean_bank]=$lean_bank;
					if($lean_bank>0 && $rlz_dtls_id_check[$row[csf("rlz_dtls_id")]]=="")
					{
						$rlz_dtls_id_check[$row[csf("rlz_dtls_id")]]=$row[csf("rlz_dtls_id")];
						$rlz_data_arr[date("Y-m",strtotime($row[csf("received_date")]))][$lean_bank]["document_currency"]+=$row[csf("document_currency")];
					}
					if($lean_bank>0 && $rlz_id_check[$row[csf("rlz_id")]]=="")
					{
						$rlz_id_check[$row[csf("rlz_id")]]=$row[csf("rlz_id")];
						$rlz_data_arr[date("Y-m",strtotime($row[csf("received_date")]))][$lean_bank]["rlz_id"].=$row[csf("rlz_id")].",";
					}
				}
				unset($sql_realization_result);
				
				//echo "<pre>";print_r($rlz_data_arr);die;
				$btb_lc_wise_bank=sql_select("select id as btb_id, issuing_bank_id, lc_date
				from com_btb_lc_master_details 
				where status_active=1 and is_deleted=0 and importer_id in($cbo_company_name)");
				$lc_wise_bank=array();
				foreach($btb_lc_wise_bank as $row)
				{
					$lc_wise_bank[$row[csf("btb_id")]]=$row[csf("issuing_bank_id")];
					if($row[csf("issuing_bank_id")]) $bank_array[date("Y-m",strtotime($row[csf("lc_date")]))][$row[csf("issuing_bank_id")]]=$row[csf("issuing_bank_id")];
					//$bank_array[$row[csf("issuing_bank_id")]]=$row[csf("issuing_bank_id")];
				}
				//echo "<pre>";print_r($lc_wise_bank);die;
				unset($btb_lc_wise_bank);
				
				$btb_lc="select id as btb_id, issuing_bank_id, lc_date, payterm_id, lc_value as btb_lc_value 
				from com_btb_lc_master_details 
				where status_active=1 and is_deleted=0 and importer_id in($cbo_company_name) and lc_date between '$txt_date_from' and '$txt_date_to'";
				//echo $btb_lc;
				$btb_lc_result=sql_select($btb_lc);
				foreach($btb_lc_result as $row)
				{
					if($row[csf("payterm_id")]==3)
					{
						$btb_data_arr[date("Y-m",strtotime($row[csf("lc_date")]))][$row[csf("issuing_bank_id")]]["btb_lc_value_tt"]+=$row[csf("btb_lc_value")];
						$btb_data_arr[date("Y-m",strtotime($row[csf("lc_date")]))][$row[csf("issuing_bank_id")]]["btb_id_tt"].=$row[csf("btb_id")].",";
					}
					else
					{
						$btb_data_arr[date("Y-m",strtotime($row[csf("lc_date")]))][$row[csf("issuing_bank_id")]]["btb_lc_value"]+=$row[csf("btb_lc_value")];
						$btb_data_arr[date("Y-m",strtotime($row[csf("lc_date")]))][$row[csf("issuing_bank_id")]]["btb_id"].=$row[csf("btb_id")].",";
					}
				}
				//echo "<pre>";print_r($btb_data_arr);die;
				unset($btb_lc_result);
				
				$company_acceptance="select a.id as inv_id, b.btb_lc_id, a.company_acc_date, b.current_acceptance_value 
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c
				where a.id=b.import_invoice_id and b.btb_lc_id=c.id and c.payterm_id<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_acc_date between '$txt_date_from' and '$txt_date_to'";
				$company_acceptance_result=sql_select($company_acceptance);
				foreach($company_acceptance_result as $row)
				{
					$issu_bank=$lc_wise_bank[$row[csf("btb_lc_id")]];
					if($issu_bank) $bank_array[date("Y-m",strtotime($row[csf("company_acc_date")]))][$issu_bank]=$issu_bank;
					if($issu_bank>0 && $row[csf("company_acc_date")] != "" && $row[csf("company_acc_date")] != "0000-00-00")
					{
						$com_accep_data_arr[date("Y-m",strtotime($row[csf("company_acc_date")]))][$issu_bank]["company_accp_value"]+=$row[csf("current_acceptance_value")];
						if($inv_id_check[$row[csf("inv_id")]]=="")
						{
							$inv_id_check[$row[csf("inv_id")]]=$row[csf("inv_id")];
							$com_accep_data_arr[date("Y-m",strtotime($row[csf("company_acc_date")]))][$issu_bank]["company_accp_id"].=$row[csf("inv_id")].",";
						}
					}
				}
				unset($company_acceptance_result);
				
						
				$bank_acceptance="select a.id as inv_id, b.btb_lc_id, a.bank_acc_date, b.current_acceptance_value 
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c
				where a.id=b.import_invoice_id and b.btb_lc_id=c.id and c.payterm_id<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.bank_acc_date between '$txt_date_from' and '$txt_date_to'";
				$bank_acceptance_result=sql_select($bank_acceptance);
				foreach($bank_acceptance_result as $row)
				{
					$issu_bank=$lc_wise_bank[$row[csf("btb_lc_id")]];
					if($issu_bank) $bank_array[date("Y-m",strtotime($row[csf("bank_acc_date")]))][$issu_bank]=$issu_bank;
					if($issu_bank>0 && $row[csf("bank_acc_date")] != "" && $row[csf("bank_acc_date")] != "0000-00-00")
					{
						$bank_accep_data_arr[date("Y-m",strtotime($row[csf("bank_acc_date")]))][$issu_bank]["bank_accp_value"]+=$row[csf("current_acceptance_value")];
						if($bank_inv_id_check[$row[csf("inv_id")]]=="")
						{
							$bank_accep_data_arr[$row[csf("inv_id")]]=$row[csf("inv_id")];
							$bank_accep_data_arr[date("Y-m",strtotime($row[csf("bank_acc_date")]))][$issu_bank]["bank_accp_id"].=$row[csf("inv_id")].",";
						}
					}
					
				}
				unset($bank_acceptance_result);
				
				
				$maturity_acceptance="select a.id as inv_id, b.btb_lc_id, a.maturity_date, b.current_acceptance_value 
				from com_import_invoice_mst a, com_import_invoice_dtls b
				where a.id=b.import_invoice_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.maturity_date between '$txt_date_from' and '$txt_date_to'";
				//echo $company_acceptance;die;
				$maturity_acceptance_result=sql_select($maturity_acceptance);
				foreach($maturity_acceptance_result as $row)
				{
					$issu_bank=$lc_wise_bank[$row[csf("btb_lc_id")]];
					if($issu_bank) $bank_array[date("Y-m",strtotime($row[csf("maturity_date")]))][$issu_bank]=$issu_bank;
					if($issu_bank>0 && $row[csf("maturity_date")] != "" && $row[csf("maturity_date")] != "0000-00-00")
					{
						$mature_accep_data_arr[date("Y-m",strtotime($row[csf("maturity_date")]))][$issu_bank]["matured_value"]+=$row[csf("current_acceptance_value")];
						if($mature_inv_id_check[$row[csf("inv_id")]]=="")
						{
							$mature_inv_id_check[$row[csf("inv_id")]]=$row[csf("inv_id")];
							$mature_accep_data_arr[date("Y-m",strtotime($row[csf("maturity_date")]))][$issu_bank]["matured_id"].=$row[csf("inv_id")].",";
						}
					}
				}
				unset($maturity_acceptance_result);
				
				
				/*$sql_payment="select a.lc_id, b.payment_date as date_pay, b.accepted_ammount as accepted_ammount 
				from com_import_payment_mst a, com_import_payment b  
				where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.payment_date between '$txt_date_from' and '$txt_date_to'";
				
				$sql_payment="select b.id as pay_id, b.lc_id, b.payment_date, b.accepted_ammount as accepted_ammount, a.edf_paid_date, d.issuing_bank_id, d.lc_category
				from com_import_payment b, com_import_invoice_mst a, com_import_invoice_dtls c, com_btb_lc_master_details d  
				where b.invoice_id=a.id and a.id=c.import_invoice_id and c.btb_lc_id=d.id and b.status_active=1 and b.is_deleted=0 and a.edf_paid_date between '$txt_date_from' and '$txt_date_to' and d.lc_category in('3','03','5','05')
				union all
				select b.id as pay_id, b.lc_id, b.payment_date, b.accepted_ammount as accepted_ammount, a.edf_paid_date, d.issuing_bank_id, d.lc_category
				from com_import_payment b, com_import_invoice_mst a, com_import_invoice_dtls c, com_btb_lc_master_details d  
				where b.invoice_id=a.id and a.id=c.import_invoice_id and c.btb_lc_id=d.id and b.status_active=1 and b.is_deleted=0 and b.payment_date between '$txt_date_from' and '$txt_date_to' and d.lc_category not in('3','03','5','05')";*/
				//echo $sql_payment;die;
				
				$sql_payment="select b.id as pay_id, c.id as accep_dtls_id, a.id as inv_id, b.payment_date, c.current_acceptance_value as accepted_ammount, a.edf_paid_date, d.issuing_bank_id, d.lc_category, a.retire_source
				from com_import_payment b, com_import_invoice_mst a, com_import_invoice_dtls c, com_btb_lc_master_details d  
				where b.invoice_id=a.id and a.id=c.import_invoice_id and c.btb_lc_id=d.id and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.retire_source=30 and a.edf_paid_date between '$txt_date_from' and '$txt_date_to' and d.importer_id in($cbo_company_name) and d.lc_category in('3','03','5','05')
				union all
				select b.id as pay_id, c.id as accep_dtls_id, a.id as inv_id, b.payment_date, b.accepted_ammount as accepted_ammount, a.edf_paid_date, d.issuing_bank_id, d.lc_category, a.retire_source
				from com_import_payment b, com_import_invoice_mst a, com_import_invoice_dtls c, com_btb_lc_master_details d  
				where b.invoice_id=a.id and a.id=c.import_invoice_id and c.btb_lc_id=d.id and b.status_active=1 and b.is_deleted=0 and a.retire_source<>30 and b.payment_date between '$txt_date_from' and '$txt_date_to' and d.importer_id in($cbo_company_name) and d.lc_category not in('3','03','5','05')";
				//echo $sql_payment;
				
				$sql_payment_result=sql_select($sql_payment);
				/*foreach($sql_payment_result as $row)
				{
					$issu_bank=$row[csf("issuing_bank_id")];
					
					if($pay_id_check[$row[csf("pay_id")]]=="" && $issu_bank>0)
					{
						$pay_id_check[$row[csf("pay_id")]]=$row[csf("pay_id")];
						if($row[csf("lc_category")]*1==3 || $row[csf("lc_category")]*1==5)
						{
							if($row[csf("edf_paid_date")] !="" && $row[csf("edf_paid_date")]!="0000-00-00")
							{
								$bank_array[date("Y-m",strtotime($row[csf("edf_paid_date")]))][$issu_bank]=$issu_bank;
								$pay_data_arr[date("Y-m",strtotime($row[csf("edf_paid_date")]))][$issu_bank]["accepted_ammount"]+=$row[csf("accepted_ammount")];
								$pay_data_arr[date("Y-m",strtotime($row[csf("edf_paid_date")]))][$issu_bank]["accepted_id"].=$row[csf("pay_id")].",";
							}
							
						}
						else
						{
							$bank_array[date("Y-m",strtotime($row[csf("payment_date")]))][$issu_bank]=$issu_bank;
							$pay_data_arr[date("Y-m",strtotime($row[csf("payment_date")]))][$issu_bank]["accepted_ammount"]+=$row[csf("accepted_ammount")];
							$pay_data_arr[date("Y-m",strtotime($row[csf("payment_date")]))][$issu_bank]["accepted_id"].=$row[csf("pay_id")].",";
						}
					}
				}*/
				
				foreach($sql_payment_result as $row)
				{
					$issu_bank=$row[csf("issuing_bank_id")];
					if($row[csf("retire_source")]==30)
					{
						if($row[csf("edf_paid_date")] !="" && $row[csf("edf_paid_date")]!="0000-00-00" && $accep_id_check[$row[csf("accep_dtls_id")]]=="")
						{
							$accep_id_check[$row[csf("accep_dtls_id")]]=$row[csf("accep_dtls_id")];
							if($issu_bank)
							{
								$bank_array[date("Y-m",strtotime($row[csf("edf_paid_date")]))][$issu_bank]=$issu_bank;
								$pay_data_arr[date("Y-m",strtotime($row[csf("edf_paid_date")]))][$issu_bank]["accepted_ammount"]+=$row[csf("accepted_ammount")];
								$pay_data_arr[date("Y-m",strtotime($row[csf("edf_paid_date")]))][$issu_bank]["accepted_id"].=$row[csf("inv_id")].",";
							}
						}
					}
					else
					{
						if($pay_id_check[$row[csf("pay_id")]]=="")
						{
							$pay_id_check[$row[csf("pay_id")]]=$row[csf("pay_id")];
							if($issu_bank)
							{
								$bank_array[date("Y-m",strtotime($row[csf("payment_date")]))][$issu_bank]=$issu_bank;
								$pay_data_arr[date("Y-m",strtotime($row[csf("payment_date")]))][$issu_bank]["accepted_ammount"]+=$row[csf("accepted_ammount")];
								$pay_data_arr[date("Y-m",strtotime($row[csf("payment_date")]))][$issu_bank]["accepted_id"].=$row[csf("inv_id")].",";
							}
						}
					}			
				}
				unset($sql_payment_result);
				//echo $test_data.test;die;
				//echo "<pre>";print_r($bank_array['2019-05']);die;
				
				$k=1;
				foreach($date_arr as $year_month=>$year_data)
				{
					foreach($bank_array[$year_month] as $bank_id=>$bank_val)
					{
						if ($k%2==0)
						$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
							<td><? $month_id=explode("-",$year_month); echo $months[(int)$month_id[1]];?></td>
							<td title="<?= $bank_id;?>"><? echo $bank_arr[$bank_id];?></td>

							<td align="right" title="<? echo chop($sc_data_arr[$year_month][$bank_id]["contract_id"],","); ?>"><a href="#report_details" onclick="open_details('<? echo chop($sc_data_arr[$year_month][$bank_id]['contract_id'],',');?>','contract_details','Contract Details','1300','SC');"><? echo number_format($sc_data_arr[$year_month][$bank_id]["contract_value"],2); $total_sc +=$sc_data_arr[$year_month][$bank_id]["contract_value"];?></a></td>

							<td align="right" title="<? echo chop($lc_data_arr[$year_month][$bank_id]["lc_id"],","); ?>"><a href="#report_details" onclick="open_details('<? echo chop($lc_data_arr[$year_month][$bank_id]['lc_id'],',');?>','contract_details','LC Details','1300','LC');"><? echo number_format($lc_data_arr[$year_month][$bank_id]["lc_value"],2); $total_lc +=$lc_data_arr[$year_month][$bank_id]["lc_value"]; ?></a></td>


							<td align="right"  title="<? echo chop($sc_data_arr[$year_month][$bank_id]["lien_contract_id"],","); ?>"><a href="#report_details" onclick="open_details('<? echo chop($sc_data_arr[$year_month][$bank_id]['lien_contract_id'],',');?>','contract_details','Contract Details','1300','SC');"><? echo number_format($sc_data_arr[$year_month][$bank_id]["lien_contract_val"],2); $total_lein_sc +=$sc_data_arr[$year_month][$bank_id]["lien_contract_val"]; ?></a></td>


							<td align="right" title="<? echo chop($lc_data_arr[$year_month][$bank_id]["lien_lc_id"],","); ?>"><a href="#report_details" onclick="open_details('<? echo chop($lc_data_arr[$year_month][$bank_id]['lien_lc_id'],',');?>','contract_details','LC Details','1300','LC');"><? echo number_format($lc_data_arr[$year_month][$bank_id]["lien_lc_val"],2); $total_lein_lc +=$lc_data_arr[$year_month][$bank_id]["lien_lc_val"]; ?></a></td>



							<td align="right"><? $total_lein=$sc_data_arr[$year_month][$bank_id]["lien_contract_val"]+$lc_data_arr[$year_month][$bank_id]["lien_lc_val"]; echo number_format($total_lein,2); $total_lein_sc_lc +=$total_lein; ?></td>
							
							
							<td align="right"  title="<? echo chop($sc_data_arr[$year_month][$bank_id]["amendment_contract_id"],","); ?>"><a href="#report_details" onclick="open_details('<? echo chop($sc_data_arr[$year_month][$bank_id]['amendment_contract_id'],',');?>','amendment_details','Contract Details','1300','SC');"><? echo number_format($sc_data_arr[$year_month][$bank_id]["amendment_value"],2); $total_amendment_sc +=$sc_data_arr[$year_month][$bank_id]["amendment_value"]; ?></a></td>


							<td align="right" title="<? echo chop($lc_data_arr[$year_month][$bank_id]["amendment_lc_id"],","); ?>"><a href="#report_details" onclick="open_details('<? echo chop($lc_data_arr[$year_month][$bank_id]['amendment_lc_id'],',');?>','amendment_details','LC Details','1300','LC');"><? echo number_format($lc_data_arr[$year_month][$bank_id]["amendment_value"],2); $total_amendment_lc +=$lc_data_arr[$year_month][$bank_id]["amendment_value"]; ?></a></td>
												
							<td align="right"><? $total_amendment=$sc_data_arr[$year_month][$bank_id]["amendment_value"]+$lc_data_arr[$year_month][$bank_id]["amendment_value"]; echo number_format($total_lein,2); $total_amendment_sc_lc +=$total_amendment; ?></td>
							
							
							<td align="right" title="<? echo chop($invoice_data_arr[$year_month][$bank_id]["invoice_id"],","); ?>"><a href="#report_details" onclick="open_details('<? echo chop($invoice_data_arr[$year_month][$bank_id]['invoice_id'],',');?>','invoice_details','Invoice Details','1300','Qnty');"><? echo number_format($invoice_data_arr[$year_month][$bank_id]["invoice_quantity"],0); $total_inv_qnty +=$invoice_data_arr[$year_month][$bank_id]["invoice_quantity"]; ?></a></td>


							<td align="right" title="<? echo chop($invoice_data_arr[$year_month][$bank_id]["invoice_id"],","); ?>"><a href="#report_details" onclick="open_details('<? echo chop($invoice_data_arr[$year_month][$bank_id]['invoice_id'],',');?>','invoice_details','Invoice Details','1300','Amt');"><? echo number_format($invoice_data_arr[$year_month][$bank_id]["net_invo_value"],2); $total_inv_val +=$invoice_data_arr[$year_month][$bank_id]["net_invo_value"]; ?></a></td>

							<td align="right" title="<? echo chop($sub_data_arr[$year_month][$bank_id]["sub_collection_id"],","); ?>"><a href="#report_details" onclick="open_details('<? echo chop($sub_data_arr[$year_month][$bank_id]['sub_collection_id'],',');?>','submission_details','Submission Details','1300','Collection');"><? echo number_format($sub_data_arr[$year_month][$bank_id]["sub_collection"],2); $total_sub_collection +=$sub_data_arr[$year_month][$bank_id]["sub_collection"]; ?></a></td>

							<td align="right" title="<? echo chop($sub_data_arr[$year_month][$bank_id]["sub_purchase_id"],","); ?>"><a href="#report_details" onclick="open_details('<? echo chop($sub_data_arr[$year_month][$bank_id]['sub_purchase_id'],',');?>','submission_details','Submission Details','1300','Purchase');"><? echo number_format($sub_data_arr[$year_month][$bank_id]["sub_purchase"],2); $total_sub_purchase +=$sub_data_arr[$year_month][$bank_id]["sub_purchase"]; ?></a></td>
							
							
							<td align="right" title="<? echo chop($rlz_data_arr[$year_month][$bank_id]["rlz_id"],","); ?>"><a href="#report_details" onclick="open_details('<? echo chop($rlz_data_arr[$year_month][$bank_id]['rlz_id'],',');?>','realization_details','Realization Details','1000','Value');"><? echo number_format($rlz_data_arr[$year_month][$bank_id]["document_currency"],2); $total_rlz +=$rlz_data_arr[$year_month][$bank_id]["document_currency"]; ?></a></td>
							<td align="right" title="<? echo chop($btb_data_arr[$year_month][$bank_id]["btb_id"],","); ?>"><a href="#report_details" onclick="open_details('<? echo chop($btb_data_arr[$year_month][$bank_id]['btb_id'],','); ?>','btb_details','BTB Details','950','Value');"><? echo number_format($btb_data_arr[$year_month][$bank_id]["btb_lc_value"],2); $total_btb +=$btb_data_arr[$year_month][$bank_id]["btb_lc_value"]; ?></a></td>
							<td align="right" title="<? echo chop($btb_data_arr[$year_month][$bank_id]["btb_id_tt"],","); ?>"><a href="#report_details" onclick="open_details('<? echo chop($btb_data_arr[$year_month][$bank_id]['btb_id_tt'],','); ?>','btb_details','BTB Details','950','Value');"><? echo number_format($btb_data_arr[$year_month][$bank_id]["btb_lc_value_tt"],2); $total_btb_tt +=$btb_data_arr[$year_month][$bank_id]["btb_lc_value_tt"]; ?></a></td>
							
							<td align="right" title="<? echo chop($com_accep_data_arr[$year_month][$bank_id]["company_accp_id"],","); ?>"><a href="#report_details" onclick="open_details('<? echo chop($com_accep_data_arr[$year_month][$bank_id]['company_accp_id'],','); ?>','accep_details','Company Acceptance Details','1300','com');"><? echo number_format($com_accep_data_arr[$year_month][$bank_id]["company_accp_value"] ,2); $total_accp_company +=$com_accep_data_arr[$year_month][$bank_id]["company_accp_value"] ; ?></a></td>
												
							<td align="right" title="<? echo chop($bank_accep_data_arr[$year_month][$bank_id]["bank_accp_id"],","); ?>"><a href="#report_details" onclick="open_details('<? echo chop($bank_accep_data_arr[$year_month][$bank_id]['bank_accp_id'],','); ?>','bank_details','Bank Acceptance Details','1300','bank');"><? echo number_format($bank_accep_data_arr[$year_month][$bank_id]["bank_accp_value"],2); $total_accp_bank +=$bank_accep_data_arr[$year_month][$bank_id]["bank_accp_value"];?></a></td>
							<td align="right" title="<? echo chop($mature_accep_data_arr[$year_month][$bank_id]["matured_id"],","); ?>"><a href="#report_details" onclick="open_details('<? echo chop($mature_accep_data_arr[$year_month][$bank_id]['matured_id'],','); ?>','bank_details_mature','Maturity Details','1300','maturity');"><? echo number_format($mature_accep_data_arr[$year_month][$bank_id]["matured_value"],2); $total_accp_matured +=$mature_accep_data_arr[$year_month][$bank_id]["matured_value"]; ?></a></td>
							<td align="right" title="<? echo chop($pay_data_arr[$year_month][$bank_id]["accepted_id"],","); ?>"><a href="#report_details" onclick="open_details('<? echo chop($pay_data_arr[$year_month][$bank_id]['accepted_id'],','); ?>','payment_details','Payment Details','1300','payment','<?= $txt_date_from;?>','<?= $txt_date_to; ?>','<?= $bank_id; ?>');"><? echo number_format($pay_data_arr[$year_month][$bank_id]["accepted_ammount"],2); $total_pay +=$pay_data_arr[$year_month][$bank_id]["accepted_ammount"];?></a></td>
						</tr>
						<?
						$k++;
						$all_sub_collection_id.=$sub_data_arr[$year_month][$bank_id]['sub_collection_id'];
						
						$all_sub_col_id.=$sc_data_arr[$year_month][$bank_id]["contract_id"];
						$all_lc_data.=$lc_data_arr[$year_month][$bank_id]["lc_id"];
						$all_sc_data_arr.=$sc_data_arr[$year_month][$bank_id]["lien_contract_id"];
						$all_lc_data_arr.=$lc_data_arr[$year_month][$bank_id]["lien_lc_id"];
						$all_amendment_contract_data.=$sc_data_arr[$year_month][$bank_id]["amendment_contract_id"];
						$all_amendment_lc_data.=$lc_data_arr[$year_month][$bank_id]["amendment_lc_id"];
						$all_invoice_data.=$invoice_data_arr[$year_month][$bank_id]["invoice_id"];
						$all_sub_purchase_data.=$sub_data_arr[$year_month][$bank_id]["sub_purchase_id"];

						$all_realization_data.=$rlz_data_arr[$year_month][$bank_id]["rlz_id"];
						$all_btb_data.=$btb_data_arr[$year_month][$bank_id]["btb_id"];
						$all_btb_tt_data.=$btb_data_arr[$year_month][$bank_id]["btb_id_tt"];
						$all_com_accep_data.=$com_accep_data_arr[$year_month][$bank_id]["company_accp_id"];
						$all_bank_accep_data.=$bank_accep_data_arr[$year_month][$bank_id]["bank_accp_id"];
						$all_mature_accep_data.=$mature_accep_data_arr[$year_month][$bank_id]["matured_id"];
						$all_pay_data.=$pay_data_arr[$year_month][$bank_id]["accepted_id"];
					}
				}
				?>
				
				</tbody>
				<tfoot>
					<th></th>
					<th></th>
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_sub_col_id,','); ?>',
					'contract_details','Contract Details','1300','SC');"><? echo number_format($total_sc,2); ?></th>



					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_lc_data,','); ?>','contract_details','LC Details','1300','LC');"><? echo number_format($total_lc,2); ?></th>

					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_sc_data_arr,','); ?>','contract_details','Contract Details','1300','SC');"><? echo number_format($total_lein_sc,2); ?></th>



					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_lc_data_arr,','); ?>','contract_details','LC Details','1300','LC');"><? echo number_format($total_lein_lc,2); ?></th>
					<th align="right"><? echo number_format($total_lein_sc_lc,2); ?></th>
					
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_amendment_contract_data,','); ?>','amendment_details','Contract Details','1300','SC');"><? echo number_format($total_amendment_sc,2); ?></th>


					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_amendment_lc_data,','); ?>','amendment_details','LC Details','1300','LC');"><? echo number_format($total_amendment_lc,2); ?></th>


					<th align="right"><? echo number_format($total_amendment_sc_lc,2); ?></th>
					
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_invoice_data,','); ?>','invoice_details','Invoice Details','1300','Qnty');"><? echo number_format($total_inv_qnty,0); ?></th>

					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_invoice_data,','); ?>','invoice_details','Invoice Details','1300','Qnty');"><? echo number_format($total_inv_val,2); ?></th>



					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_sub_collection_id,','); ?>','submission_details','Submission Details','1300','Collection');"><? echo number_format($total_sub_collection,2); ?></a></th>




					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_sub_purchase_data,','); ?>','submission_details','Submission Details','1300','Purchase');"><? echo number_format($total_sub_purchase,2); ?></th>
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_realization_data,','); ?>','realization_details','Realization Details','1000','Value');"><? echo number_format($total_rlz,2); ?></a></th>
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_btb_data,','); ?>','btb_details','BTB Details','950','Value');"><? echo number_format($total_btb,2); ?></a></th>
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_btb_tt_data,','); ?>','btb_details','BTB Details','950','Value');"><? echo number_format($total_btb_tt,2); ?></a></th>
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_com_accep_data,','); ?>','accep_details','Company Acceptance Details','1300','com');"><? echo number_format($total_accp_company,2); ?></a></th>
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_bank_accep_data,','); ?>','bank_details','Bank Acceptance Details','1300','bank');"><? echo number_format($total_accp_bank,2); ?></a></th>
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_mature_accep_data,','); ?>','bank_details_mature','Maturity Details','1300','maturity');"><? echo number_format($total_accp_matured,2); ?></a></th>
					<th align="right"><a href="#report_details" onclick="open_details('<? echo chop($all_pay_data,','); ?>','payment_details','Payment Details','1300','payment','<?= '';?>','<?= ''; ?>','<?= "0"; ?>');"><? echo number_format($total_pay,2); ?></a></th>
				</tfoot>
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


if($action=="contract_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $ref_id."=".$popup_type;die;
	if($popup_type=="LC")
	{
        $sql="select id as lc_sc_id, export_lc_system_id as sys_num, lc_date as date_lc_sc, export_lc_no as lc_sc_no, lc_value as lc_sc_value, internal_file_no, bank_file_no, lc_year as lc_sc_year, buyer_name, applicant_name, lien_bank, issuing_bank_name, pay_term, tenor, inserted_by, insert_date, lien_date,beneficiary_name
		from com_export_lc where status_active=1 and is_deleted=0 and id in($ref_id) order by  lc_date ASC ";
	}
	else
	{
		$sql="select id as lc_sc_id, contact_system_id as sys_num, contract_date as date_lc_sc, contract_no as lc_sc_no, contract_value as lc_sc_value, internal_file_no, bank_file_no, sc_year as lc_sc_year, buyer_name, applicant_name, lien_bank, 0 as issuing_bank_name, pay_term, tenor, inserted_by, insert_date, lien_date,beneficiary_name  
		from com_sales_contract where status_active=1 and is_deleted=0 and id in($ref_id) order by  contract_date ASC";
	}
	//echo $sql;die;
	$result=sql_select($sql);
	$buyer_short_name=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body></html>');
			d.close();
		}	
	</script>
    	 <div> 
		<div style="width:1450px; margin-left:10px" id="report_div">
    	 <div style="width:870px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
             <div id="report_container"> </div>
        </div>
        <?
         ob_start();
		?>	
	<div style="width:1450px" align="center" id="scroll_body" >
	<fieldset style="width:100%;" >
		 <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1450" align="center" border="1">
            <thead>
                <th width="30">SL</th>
                <th width="100">Company</th>
                <th width="50">Internal File No.</th>
                <th width="50">Bank File No.</th>
                <th width="50">Year</th>
                <th width="80">Buyer</th> 
                <th width="80">Agent</th>
                <th width="40">SC/LC</th>
                <th width="100">SC/LC No.</th>
                <th width="70">SC/LC Date</th>
                <th width="70">Lien Date</th>
                <th width="90">SC/LC Value</th> 
                <th width="100">Lien Bank</th>
                <th width="100">Issuing Bank</th>
                <th width="70">Pay Term</th>
                <th width="50">Tenor</th>
                <th width="100">System Id</th> 
                <th width="130">Insert date & Time</th>
                <th>insert User Name</th>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<td align="center"><? echo $i; ?></td>
                    <td align="center"><p><? echo $company_arr[$row[csf("beneficiary_name")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("internal_file_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("bank_file_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("lc_sc_year")]; ?>&nbsp;</p></td>
                    <td><p><? echo $buyer_short_name[$row[csf("buyer_name")]]; ?>&nbsp;</p></td> 
                    <td><p><? echo $buyer_short_name[$row[csf("applicant_name")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $popup_type; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("lc_sc_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[csf("date_lc_sc")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[csf("lien_date")]); ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[csf("lc_sc_value")],2); ?></td> 
                    <td><p><? echo $bank_arr[$row[csf("lien_bank")]]; ?>&nbsp;</p></td> 
                    <td><p><? echo $bank_arr[$row[csf("issuing_bank_name")]]; ?>&nbsp;</p></td>
                    <td><p><? echo $pay_term[$row[csf("pay_term")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("tenor")]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("sys_num")]; ?>&nbsp;</p></td> 
                    <td><p><? echo $row[csf("insert_date")]; ?>&nbsp;</p></td> 
                    <td><p><? echo $user_arr[$row[csf("inserted_by")]]; ?>&nbsp;</p></td> 
				</tr>
				<?
				$i++;
				$total_value+=$row[csf("lc_sc_value")];
			}
            ?>
            </tbody>
            <tfoot>
            	<th>&nbsp;</th>
            	<th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th> 
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right" colspan="2"><? echo number_format($total_value,2); ?></th> 
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th> 
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
	</fieldset>
	</div>
    
	<?	
	
	
	$html=ob_get_contents();
	ob_flush();
	foreach (glob("$user_id*.xls") as $filename)
	{
		@unlink($filename);
	}
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) 
	{
		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
      </div> 
    <?
	die;
}

if($action=="amendment_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $ref_id."=".$popup_type;die;
	if($popup_type=="LC")
	{
        $sql="select a.id as lc_sc_id, a.export_lc_system_id as sys_num, a.lc_date as date_lc_sc, a.export_lc_no as lc_sc_no, a.lc_value as lc_sc_value, a.internal_file_no, a.bank_file_no, a.lc_year as lc_sc_year, a.buyer_name, a.applicant_name, a.lien_bank, a.issuing_bank_name, a.pay_term, a.tenor, a.inserted_by, a.insert_date, a.lien_date, b.amendment_no, b.amendment_date, b.amendment_value,a.beneficiary_name
		from com_export_lc a, com_export_lc_amendment b 
		where a.id=b.export_lc_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.amendment_value>0 and a.id in($ref_id) 
		order by date_lc_sc ASC ";
	}
	else
	{
		$sql="select a.id as lc_sc_id, a.contact_system_id as sys_num, a.contract_date as date_lc_sc, a.contract_no as lc_sc_no, a.contract_value as lc_sc_value, a.internal_file_no, a.bank_file_no, a.sc_year as lc_sc_year, a.buyer_name, a.applicant_name, a.lien_bank, 0 as issuing_bank_name, a.pay_term, a.tenor, a.inserted_by, a.insert_date, a.lien_date, b.amendment_no, b.amendment_date, b.amendment_value ,a.beneficiary_name
		from com_sales_contract a, com_sales_contract_amendment b
		where a.id=b.contract_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.amendment_value>0 and a.id in($ref_id) 
		order by date_lc_sc ASC";
	}
	//echo $sql;die;
	$result=sql_select($sql);
	$buyer_short_name=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	ob_start();
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>
    	 <div> 
		<div style="width:1450px; margin-left:10px" id="report_div">
    	 <div style="width:870px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
             <div id="report_container"> </div>
        </div>
        <?
         ob_start();
		?>	
	<div style="width:1680px" align="center" id="scroll_body" >
	<fieldset style="width:100%;" >
		 <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1680" align="center" border="1">
            <thead>
                <th width="30">SL</th>
                <th width="100">Company.</th>
                <th width="50">Internal File No.</th>
                <th width="50">Bank File No.</th>
                <th width="50">Year</th>
                <th width="80">Buyer</th> 
                <th width="80">Agent</th>
                <th width="40">SC/LC</th>
                <th width="100">SC/LC No.</th>
                <th width="70">SC/LC Date</th>
                <th width="70">Lien Date</th>
                <th width="90">SC/LC Value</th>
                <th width="70">Amendment No</th>
                <th width="70">Amendment Date</th>
                <th width="90">Amendment Value</th> 
                <th width="100">Lien Bank</th>
                <th width="100">Issuing Bank</th>
                <th width="70">Pay Term</th>
                <th width="50">Tenor</th>
                <th width="100">System Id</th> 
                <th width="130">Insert date & Time</th>
                <th>insert User Name</th>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<td align="center"><? echo $i; ?></td>
                    <td align="center"><p><? echo $company_arr[$row[csf("beneficiary_name")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("internal_file_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("bank_file_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("lc_sc_year")]; ?>&nbsp;</p></td>
                    <td><p><? echo $buyer_short_name[$row[csf("buyer_name")]]; ?>&nbsp;</p></td> 
                    <td><p><? echo $buyer_short_name[$row[csf("applicant_name")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $popup_type; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("lc_sc_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[csf("date_lc_sc")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[csf("lien_date")]); ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[csf("lc_sc_value")],2); ?></td> 
                    <td align="center"><p><? echo $row[csf("amendment_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[csf("amendment_date")]); ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[csf("amendment_value")],2); ?></td>
                    <td><p><? echo $bank_arr[$row[csf("lien_bank")]]; ?>&nbsp;</p></td> 
                    <td><p><? echo $bank_arr[$row[csf("issuing_bank_name")]]; ?>&nbsp;</p></td>
                    <td><p><? echo $pay_term[$row[csf("pay_term")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("tenor")]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("sys_num")]; ?>&nbsp;</p></td> 
                    <td><p><? echo $row[csf("insert_date")]; ?>&nbsp;</p></td> 
                    <td><p><? echo $user_arr[$row[csf("inserted_by")]]; ?>&nbsp;</p></td> 
				</tr>
				<?
				$i++;
				if($lc_id_check[$row[csf("lc_sc_id")]]=="")
				{
					$lc_id_check[$row[csf("lc_sc_id")]]=$row[csf("lc_sc_id")];
					$total_value+=$row[csf("lc_sc_value")];
				}
				$total_amend_value+=$row[csf("amendment_value")];
			}
            ?>
            </tbody>
            <tfoot>
            	<th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th> 
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right" colspan="2"><? echo number_format($total_value,2); ?></th> 
                <th align="right" colspan="3"><? echo number_format($total_amend_value,2); ?></th> 
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th> 
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
	</fieldset>
	</div>
    
	<?	
	
	
	$html=ob_get_contents();
	ob_flush();
	foreach (glob("$user_id*.xls") as $filename)
	{
		@unlink($filename);
	}
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) 
	{
		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
      </div> 
    <?
	die;
}

if($action=="invoice_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $ref_id."=".$popup_type;die; b.invoice_no, b.invoice_date, b.invoice_quantity, b.net_invo_value, b.ex_factory_date, b.bl_no, b.bl_date, b.inserted_by, b.insert_date 
	$sql=" select * from 
	(
    select a.id as lc_sc_id, a.export_lc_system_id as sys_num, a.lc_date as date_lc_sc, a.export_lc_no as lc_sc_no, a.internal_file_no, a.lc_year as lc_sc_year, a.buyer_name, a.lien_bank, a.pay_term, a.tenor, b.invoice_no, b.invoice_date, b.invoice_quantity, b.net_invo_value, b.ex_factory_date, b.bl_no, b.bl_date, b.inserted_by, b.insert_date,a.beneficiary_name 
	from com_export_lc a, com_export_invoice_ship_mst b 
	where a.id=b.lc_sc_id and b.is_lc=1 and b.status_active=1 and b.is_deleted=0 and b.id in($ref_id)  
	union all
	select a.id as lc_sc_id, a.contact_system_id as sys_num, a.contract_date as date_lc_sc, a.contract_no as lc_sc_no, a.internal_file_no, a.sc_year as lc_sc_year, a.buyer_name, a.lien_bank, a.pay_term, a.tenor, b.invoice_no, b.invoice_date, b.invoice_quantity, b.net_invo_value, b.ex_factory_date, b.bl_no, b.bl_date, b.inserted_by, b.insert_date,a.beneficiary_name  
	from com_sales_contract a,com_export_invoice_ship_mst b  
	where a.id=b.lc_sc_id and b.is_lc=2 and b.status_active=1 and b.is_deleted=0 and b.id in($ref_id)) order by invoice_date ASC";
	//echo $sql;die;
	$result=sql_select($sql);
	$buyer_short_name=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	ob_start();
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>
    	 <div> 
		<div style="width:870px; margin-left:30px" id="report_div">
    	 <div style="width:870px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
             <div id="report_container"> </div>
        </div>
        <?
         ob_start();
		?>	
	<div style="width:1550px" align="center" id="scroll_body" >
	<fieldset style="width:100%;" >
		 <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1540" align="center" border="1">
            <thead>
                <th width="30">SL</th>
                <th width="100">Company</th>
                <th width="80">Buyer</th>
                <th width="100">Lien Bank</th> 
                <th width="50">Internal File No.</th>
                <th width="50">Year</th>
                <th width="100">SC/LC No.</th>
                <th width="70">Pay Term</th>
                <th width="50">Tenor</th>
                <th width="100">Invoice No.</th>
                <th width="70">Invoice Date</th>
                <th width="80">Invoice Qnty</th>
                <th width="90">Invoice Value</th> 
                <th width="70">Ex-Factory Date</th>
                <th width="70">BL/Cargo No</th>
                <th width="70">BL/Cargo Date</th>
                <th width="100">System Id</th> 
                <th width="140">Insert date & Time</th>
                <th>insert User Name</th>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
               
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<td align="center"><? echo $i; ?></td>
                    <td><p><? echo $company_arr[$row[csf("beneficiary_name")]]; ?>&nbsp;</p></td>
                    <td><p><? echo $buyer_short_name[$row[csf("buyer_name")]]; ?>&nbsp;</p></td>
                    <td><p><? echo $bank_arr[$row[csf("lien_bank")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("internal_file_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("lc_sc_year")]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("lc_sc_no")]; ?>&nbsp;</p></td>
                    <td><p><? echo $pay_term[$row[csf("pay_term")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("tenor")]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[csf("invoice_date")]); ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[csf("invoice_quantity")],2); ?></td>
                    <td align="right"><? echo number_format($row[csf("net_invo_value")],2); ?></td>
                    <td align="center"><p><? if($row[csf("ex_factory_date")]!="" && $row[csf("ex_factory_date")]!="0000-00-00") echo change_date_format($row[csf("ex_factory_date")]); ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("bl_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row[csf("bl_date")]!="" && $row[csf("bl_date")]!="0000-00-00") echo change_date_format($row[csf("bl_date")]); ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("sys_num")]; ?>&nbsp;</p></td> 
                    <td><p><? echo $row[csf("insert_date")]; ?>&nbsp;</p></td> 
                    <td><p><? echo $user_arr[$row[csf("inserted_by")]]; ?>&nbsp;</p></td> 
				</tr>
				<?
				$i++;
				$total_qnty+=$row[csf("invoice_quantity")];
				$total_value+=$row[csf("net_invo_value")];
			}
            ?>
            </tbody>
            <tfoot>
            	<th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th> 
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_qnty,2); ?></th> 
                <th align="right"><? echo number_format($total_value,2); ?></th> 
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th> 
                <th>&nbsp;</th>
            </tfoot>
        </table>
	</fieldset>
	</div>
	<?	
	
	
	$html=ob_get_contents();
	ob_flush();
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) 
	{
		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
      </div> 
    <?
	die;
}

if($action=="submission_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $ref_id."=".$popup_type;die; b.invoice_no, b.invoice_date, b.invoice_quantity, b.net_invo_value, b.ex_factory_date, b.bl_no, b.bl_date, b.inserted_by, b.insert_date
	if($db_type==0) $rlz_month=" month(d.possible_reali_date) as possible_month"; else $rlz_month=" to_char(d.possible_reali_date,'MM') as possible_month"; 
	
	
	$sql=" select * from 
	(
    select a.id as lc_sc_id, a.export_lc_system_id as sys_num, a.lc_date as date_lc_sc, a.export_lc_no as lc_sc_no, a.internal_file_no, a.lc_year as lc_sc_year, a.buyer_name, a.lien_bank, a.pay_term, a.tenor, b.invoice_no, b.invoice_date, b.invoice_quantity, b.net_invo_value, b.ex_factory_date, b.bl_no, b.bl_date, d.submit_date, d.bank_ref_no, d.possible_reali_date, $rlz_month, (case when submit_type=1 then c.net_invo_value else 0 end) as collect_value, (case when submit_type=2 then c.net_invo_value else 0 end) as nagotiate_value, d.inserted_by, d.insert_date,a.beneficiary_name
	from com_export_lc a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c, com_export_doc_submission_mst d 
	where a.id=b.lc_sc_id and b.id=c.invoice_id and a.id=c.lc_sc_id and c.doc_submission_mst_id=d.id and d.entry_form=40 and b.is_lc=1 and c.is_lc=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.id in($ref_id)
	union all
	select a.id as lc_sc_id, a.contact_system_id as sys_num, a.contract_date as date_lc_sc, a.contract_no as lc_sc_no, a.internal_file_no, a.sc_year as lc_sc_year, a.buyer_name, a.lien_bank, a.pay_term, a.tenor, b.invoice_no, b.invoice_date, b.invoice_quantity, b.net_invo_value, b.ex_factory_date, b.bl_no, b.bl_date, d.submit_date, d.bank_ref_no, d.possible_reali_date, $rlz_month, (case when submit_type=1 then c.net_invo_value else 0 end) as collect_value, (case when submit_type=2 then c.net_invo_value else 0 end) as nagotiate_value, d.inserted_by, d.insert_date,a.beneficiary_name  
	from com_sales_contract a,com_export_invoice_ship_mst b, com_export_doc_submission_invo c, com_export_doc_submission_mst d 
	where a.id=b.lc_sc_id and b.id=c.invoice_id and a.id=c.lc_sc_id and c.doc_submission_mst_id=d.id and d.entry_form=40 and b.is_lc=2 and c.is_lc=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.id in($ref_id)) order by submit_date ASC";
	//echo $sql;die;
	$result=sql_select($sql);
	$buyer_short_name=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	ob_start();
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>
		<div style="width:870px; margin-left:30px" id="report_div">
    	 <div style="width:870px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
             <div id="report_container"> </div>
        </div>
        <?
         ob_start();
		?>	
	<div style="width:2100px" align="center" id="scroll_body" >
	<fieldset style="width:100%;" >
		 <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="2080" align="center" border="1">
            <thead>
                <th width="30">SL</th>
                <th width="100">Company</th>
                <th width="80">Buyer</th>
                <th width="100">Lien Bank</th> 
                <th width="50">Internal File No.</th>
                <th width="50">Year</th>
                <th width="100">SC/LC No.</th>
                <th width="70">Pay Term</th>
                <th width="50">Tenor</th>
                <th width="100">Invoice No.</th>
                <th width="70">Invoice Date</th>
                <th width="80">Invoice Qnty</th>
                <th width="90">Invoice Value</th> 
                <th width="70">Ex-Factory Date</th>
                <th width="70">BL/Cargo No</th>
                <th width="70">BL/Cargo Date</th>
                <th width="70">Doc Sub Date</th>
                <th width="50">BL Deviation</th>
                <th width="50">Doc Sub Deviation</th>
                <th width="80">Bill/FDBC No.</th>
                <th width="70">Poss. Rlz. Date</th>
                <th width="60">Poss. Rlz Month</th>
                <th width="80">Collection  Amount</th>
                <th width="80">Purchase Amount</th>
                <th width="100">System Id</th> 
                <th width="140">Insert date & Time</th>
                <th>insert User Name</th>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
               
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<td align="center"><? echo $i; ?></td>
                    <td><p><? echo $company_arr[$row[csf("beneficiary_name")]]; ?>&nbsp;</p></td>
                    <td><p><? echo $buyer_short_name[$row[csf("buyer_name")]]; ?>&nbsp;</p></td>
                    <td><p><? echo $bank_arr[$row[csf("lien_bank")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("internal_file_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("lc_sc_year")]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("lc_sc_no")]; ?>&nbsp;</p></td>
                    <td><p><? echo $pay_term[$row[csf("pay_term")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("tenor")]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[csf("invoice_date")]); ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[csf("invoice_quantity")],2); ?></td>
                    <td align="right"><? echo number_format($row[csf("net_invo_value")],2); ?></td>
                    <td align="center"><p><? if($row[csf("ex_factory_date")]!="" && $row[csf("ex_factory_date")]!="0000-00-00") echo change_date_format($row[csf("ex_factory_date")]); ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("bl_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row[csf("bl_date")]!="" && $row[csf("bl_date")]!="0000-00-00") echo change_date_format($row[csf("bl_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row[csf("submit_date")]!="" && $row[csf("submit_date")]!="0000-00-00") echo change_date_format($row[csf("submit_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p>
					<?
					if($row[csf("bl_date")]!="" && $row[csf("bl_date")]!="0000-00-00") $bl_date_se=strtotime($row[csf("bl_date")]); else  $bl_date_se=0;
					if($row[csf("ex_factory_date")]!="" && $row[csf("ex_factory_date")]!="0000-00-00") $ex_factory_date_se=strtotime($row[csf("ex_factory_date")]); else $ex_factory_date_se=0;
					if($bl_date_se>0) $bl_deviation=$bl_date_se-$ex_factory_date_se; else $bl_deviation=0;
					echo number_format(($bl_deviation/86400),0); 
					?>&nbsp;</p></td>
                    <td align="center"><p>
					<?
					if($row[csf("submit_date")]!="" && $row[csf("submit_date")]!="0000-00-00") $submit_date_se=strtotime($row[csf("submit_date")]); else $submit_date_se=0;
					if($submit_date_se>0) $submit_deviation=$submit_date_se-$bl_date_se; else $submit_deviation=0;
					echo number_format(($submit_deviation/86400),0); 
					?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("bank_ref_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row[csf("possible_reali_date")]!="" && $row[csf("possible_reali_date")]!="0000-00-00") echo change_date_format($row[csf("possible_reali_date")]); ?>&nbsp;</p></td>
                    <td><p><? echo $months[$row[csf("possible_month")]*1]; ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[csf("collect_value")],2); ?></td>
                    <td align="right"><? echo number_format($row[csf("nagotiate_value")],2); ?></td>
                    <td><p><? echo $row[csf("sys_num")]; ?>&nbsp;</p></td> 
                    <td><p><? echo $row[csf("insert_date")]; ?>&nbsp;</p></td> 
                    <td><p><? echo $user_arr[$row[csf("inserted_by")]]; ?>&nbsp;</p></td> 
				</tr>
				<?
				$i++;
				$total_qnty+=$row[csf("invoice_quantity")];
				$total_value+=$row[csf("net_invo_value")];
				$total_collect_value+=$row[csf("collect_value")];
				$total_nagotiate_value+=$row[csf("nagotiate_value")];
			}
            ?>
            </tbody>
            <tfoot>
            	<th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th> 
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_qnty,2); ?></th> 
                <th align="right"><? echo number_format($total_value,2); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right"><? echo number_format($total_collect_value,2); ?></th> 
                <th align="right"><? echo number_format($total_nagotiate_value,2); ?></th> 
                <th>&nbsp;</th>
                <th>&nbsp;</th> 
                <th>&nbsp;</th>
            </tfoot>
        </table>
	</fieldset>
	</div>
	<?	
	$html=ob_get_contents();
	ob_flush();
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>

	$(document).ready(function(e) 
	{
		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
    </div> 
    <?
	die;
}


if($action=="submission_details2")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $ref_id."=".$popup_type;die; b.invoice_no, b.invoice_date, b.invoice_quantity, b.net_invo_value, b.ex_factory_date, b.bl_no, b.bl_date, b.inserted_by, b.insert_date
	if($db_type==0) $rlz_month=" month(d.possible_reali_date) as possible_month"; else $rlz_month=" to_char(d.possible_reali_date,'MM') as possible_month"; 
	
	
	$sql=" select * from 
	(
    select a.id as lc_sc_id, a.export_lc_system_id as sys_num, a.lc_date as date_lc_sc, a.export_lc_no as lc_sc_no, a.internal_file_no, a.lc_year as lc_sc_year, a.buyer_name, a.lien_bank, a.pay_term, a.tenor, b.invoice_no, b.invoice_date, b.invoice_quantity, b.net_invo_value, b.ex_factory_date, b.bl_no, b.bl_date, d.submit_date, d.bank_ref_no, d.possible_reali_date, $rlz_month, (case when submit_type=1 then c.net_invo_value else 0 end) as collect_value, (case when submit_type=2 then c.net_invo_value else 0 end) as nagotiate_value, d.inserted_by, d.insert_date,a.beneficiary_name
	from com_export_lc a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c, com_export_doc_submission_mst d 
	where a.id=b.lc_sc_id and b.id=c.invoice_id and a.id=c.lc_sc_id and c.doc_submission_mst_id=d.id and d.entry_form=40 and b.is_lc=1 and c.is_lc=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.id in($ref_id)
	union all
	select a.id as lc_sc_id, a.contact_system_id as sys_num, a.contract_date as date_lc_sc, a.contract_no as lc_sc_no, a.internal_file_no, a.sc_year as lc_sc_year, a.buyer_name, a.lien_bank, a.pay_term, a.tenor, b.invoice_no, b.invoice_date, b.invoice_quantity, b.net_invo_value, b.ex_factory_date, b.bl_no, b.bl_date, d.submit_date, d.bank_ref_no, d.possible_reali_date, $rlz_month, (case when submit_type=1 then c.net_invo_value else 0 end) as collect_value, (case when submit_type=2 then c.net_invo_value else 0 end) as nagotiate_value, d.inserted_by, d.insert_date,a.beneficiary_name  
	from com_sales_contract a,com_export_invoice_ship_mst b, com_export_doc_submission_invo c, com_export_doc_submission_mst d 
	where a.id=b.lc_sc_id and b.id=c.invoice_id and a.id=c.lc_sc_id and c.doc_submission_mst_id=d.id and d.entry_form=40 and b.is_lc=2 and c.is_lc=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.id in($ref_id)) order by submit_date ASC";
	// echo $sql;die;
	$result=sql_select($sql);
	$buyer_short_name=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	ob_start();
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>
		<div style="width:870px; margin-left:30px" id="report_div">
    	 <div style="width:870px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
             <div id="report_container"> </div>
        </div>
        <?
         ob_start();
		?>	
	<div style="width:2100px" align="center" id="scroll_body" >
	<fieldset style="width:100%;" >
		 <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="2080" align="center" border="1">
            <thead>
                <th width="30">SL</th>
                <th width="100">Company</th>
                <th width="80">Buyer</th>
                <th width="100">Lien Bank</th> 
                <th width="50">Internal File No.</th>
                <th width="50">Year</th>
                <th width="100">SC/LC No.</th>
                <th width="70">Pay Term</th>
                <th width="50">Tenor</th>
                <th width="100">Invoice No.</th>
                <th width="70">Invoice Date</th>
                <th width="80">Invoice Qnty</th>
                <th width="90">Invoice Value</th> 
                <th width="70">Ex-Factory Date</th>
                <th width="70">BL/Cargo No</th>
                <th width="70">BL/Cargo Date</th>
                <th width="70">Doc Sub Date</th>
                <th width="50">BL Deviation</th>
                <th width="50">Doc Sub Deviation</th>
                <th width="80">Bill/FDBC No.</th>
                <th width="70">Poss. Rlz. Date</th>
                <th width="60">Poss. Rlz Month</th>
                <th width="80">Collection  Amount</th>
                <th width="80">Purchase Amount</th>
                <th width="100">System Id</th> 
                <th width="140">Insert date & Time</th>
                <th>insert User Name</th>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($result as $row)
			{								
				$tenor=$row[csf("tenor")];
				if($row[csf("tenor")]>0){
					$tenor=$row[csf("tenor")];
				}else{
					$tenor=0;
				}
				$next_date=date('Y-m-d', strtotime("+$tenor day", strtotime($row[csf("ex_factory_date")])));
				$month_name = date('F', strtotime($next_date));
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<td align="center"><? echo $i; ?></td>
                    <td><p><? echo $company_arr[$row[csf("beneficiary_name")]]; ?>&nbsp;</p></td>
                    <td><p><? echo $buyer_short_name[$row[csf("buyer_name")]]; ?>&nbsp;</p></td>
                    <td><p><? echo $bank_arr[$row[csf("lien_bank")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("internal_file_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("lc_sc_year")]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("lc_sc_no")]; ?>&nbsp;</p></td>
                    <td><p><? echo $pay_term[$row[csf("pay_term")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("tenor")]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[csf("invoice_date")]); ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[csf("invoice_quantity")],2); ?></td>
                    <td align="right"><? echo number_format($row[csf("net_invo_value")],2); ?></td>
                    <td align="center"><p><? if($row[csf("ex_factory_date")]!="" && $row[csf("ex_factory_date")]!="0000-00-00") echo change_date_format($row[csf("ex_factory_date")]); ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("bl_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row[csf("bl_date")]!="" && $row[csf("bl_date")]!="0000-00-00") echo change_date_format($row[csf("bl_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row[csf("submit_date")]!="" && $row[csf("submit_date")]!="0000-00-00") echo change_date_format($row[csf("submit_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p>
					<?
					if($row[csf("bl_date")]!="" && $row[csf("bl_date")]!="0000-00-00") $bl_date_se=strtotime($row[csf("bl_date")]); else  $bl_date_se=0;
					if($row[csf("ex_factory_date")]!="" && $row[csf("ex_factory_date")]!="0000-00-00") $ex_factory_date_se=strtotime($row[csf("ex_factory_date")]); else $ex_factory_date_se=0;
					if($bl_date_se>0) $bl_deviation=$bl_date_se-$ex_factory_date_se; else $bl_deviation=0;
					echo number_format(($bl_deviation/86400),0); 
					?>&nbsp;</p></td>
                    <td align="center"><p>
					<?
					if($row[csf("submit_date")]!="" && $row[csf("submit_date")]!="0000-00-00") $submit_date_se=strtotime($row[csf("submit_date")]); else $submit_date_se=0;
					if($submit_date_se>0) $submit_deviation=$submit_date_se-$bl_date_se; else $submit_deviation=0;
					echo number_format(($submit_deviation/86400),0); 
					?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("bank_ref_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><?  echo change_date_format($next_date) //change_date_format($row[csf("possible_reali_date")]); ?>&nbsp;</p></td>
                    <td><p><? echo $month_name; ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[csf("collect_value")],2); ?></td>
                    <td align="right"><? echo number_format($row[csf("nagotiate_value")],2); ?></td>
                    <td><p><? echo $row[csf("sys_num")]; ?>&nbsp;</p></td> 
                    <td><p><? echo $row[csf("insert_date")]; ?>&nbsp;</p></td> 
                    <td><p><? echo $user_arr[$row[csf("inserted_by")]]; ?>&nbsp;</p></td> 
				</tr>
				<?
				$i++;
				$total_qnty+=$row[csf("invoice_quantity")];
				$total_value+=$row[csf("net_invo_value")];
				$total_collect_value+=$row[csf("collect_value")];
				$total_nagotiate_value+=$row[csf("nagotiate_value")];
			}
            ?>
            </tbody>
            <tfoot>
            	<th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th> 
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_qnty,2); ?></th> 
                <th align="right"><? echo number_format($total_value,2); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right"><? echo number_format($total_collect_value,2); ?></th> 
                <th align="right"><? echo number_format($total_nagotiate_value,2); ?></th> 
                <th>&nbsp;</th>
                <th>&nbsp;</th> 
                <th>&nbsp;</th>
            </tfoot>
        </table>
	</fieldset>
	</div>
	<?	
	$html=ob_get_contents();
	ob_flush();
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>

	$(document).ready(function(e) 
	{
		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
    </div> 
    <?
	die;
}


if($action=="realization_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $ref_id."=".$popup_type;die;
	
	$sql_rlz="select a.submit_date, a.bank_ref_no, b.received_date, b.buyer_id, sum(c.document_currency) as rlz_value, sum(case when c.type=0 then c.document_currency else 0 end) as deduction_value, sum(case when c.type=1 then c.document_currency else 0 end) as distribution_value, sum(c.domestic_currency) as rlz_domestic_value, b.inserted_by, b.insert_date,a.company_id
	from com_export_doc_submission_mst a, com_export_proceed_realization b, com_export_proceed_rlzn_dtls c 
	where a.id=b.invoice_bill_id and b.id=c.mst_id and b.is_invoice_bill=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id in($ref_id)
	group by a.submit_date, a.bank_ref_no, b.received_date, b.buyer_id, b.inserted_by, b.insert_date,a.company_id order by received_date ASC";
	
	//echo $sql_rlz;die;
	$result=sql_select($sql_rlz);
	$buyer_short_name=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	//$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	ob_start();
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>
    	 <div> 
		<div style="width:970px; margin-left:30px" id="report_div">
    	 <div style="width:970px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
             <div id="report_container"> </div>
        </div>
        <?
         ob_start();
		?>	
	<div style="width:1080px" align="center" id="scroll_body" >
	<fieldset style="width:100%;" >
		 <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1070" align="center" border="1">
            <thead>
                <th width="30">SL</th>
                <th width="100">Company</th> 
                <th width="80">Buyer</th> 
                <th width="80">Bank Bill No</th>
                <th width="80">Realization Date</th>
                <th width="90">Deduction Value</th> 
                <th width="90">Distribution Value</th>
                <th width="90">Total Realization Value</th>
                <th width="70">Convertion Rate</th>
                <th width="90">Realization Value(BDT)</th>
                <th width="130">Insert date & Time</th>
                <th>insert User Name</th>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$convert_rate=$row[csf("rlz_domestic_value")]/$row[csf("rlz_value")];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<td align="center"><? echo $i; ?></td>
                    <td><p><? echo $company_arr[$row[csf("company_id")]]; ?>&nbsp;</p></td>
                    <td><p><? echo $buyer_short_name[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("bank_ref_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[csf("received_date")]); ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[csf("deduction_value")],2); ?></td>
                    <td align="right"><? echo number_format($row[csf("distribution_value")],2); ?></td>
                    <td align="right"><? echo number_format($row[csf("rlz_value")],2); ?></td>
                    <td align="right"><? echo number_format($convert_rate,2); ?></td>
                    <td align="right"><? echo number_format($row[csf("rlz_domestic_value")],2); ?></td>
                    <td><p><? echo $row[csf("insert_date")]; ?>&nbsp;</p></td> 
                    <td><p><? echo $user_arr[$row[csf("inserted_by")]]; ?>&nbsp;</p></td> 
				</tr>
				<?
				$i++;
				$total_deduction_value+=$row[csf("deduction_value")];
				$total_distribution_value+=$row[csf("distribution_value")];
				$total_rlz_value+=$row[csf("rlz_value")];
				$total_rlz_domestic_value+=$row[csf("rlz_domestic_value")];
			}
            ?>
            </tbody>
            <tfoot>
            	<th>&nbsp;</th>
            	<th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_deduction_value,2); ?></th> 
                <th align="right"><? echo number_format($total_distribution_value,2); ?></th>
                <th align="right"><? echo number_format($total_rlz_value,2); ?></th>
                <th>&nbsp;</th>
                <th align="right"><? echo number_format($total_rlz_domestic_value,2); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
	</fieldset>
	</div>
	<?	
	
	
	$html=ob_get_contents();
	ob_flush();
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) 
	{
		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
      </div> 
    <?
	die;
}

if($action=="btb_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $ref_id."=".$popup_type;die;
	
	$sql_btb="select a.btb_system_id, a.lc_number, a.lc_date, a.supplier_id, a.lc_value, a.payterm_id, a.tenor, a.inserted_by, a.insert_date,a.importer_id
	from com_btb_lc_master_details a
	where a.status_active=1 and a.is_deleted=0 and a.id in($ref_id) order by lc_date ASC";
	
	//echo $sql_rlz;die;
	$result=sql_select($sql_btb);
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	//$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	ob_start();
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>
    	 <div> 

		<div style="width:970px; margin-left:30px" id="report_div">
    	 <div style="width:970px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
             <div id="report_container"> </div>
        </div>
        <?
         ob_start();
		?>	
	<div style="width:1030px" align="center" id="scroll_body" >
	<fieldset style="width:100%;" >
		 <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1020" align="center" border="1">
            <thead>
                <th width="30">SL</th>
                <th width="100">Company</th> 
                <th width="100">LC No</th> 
                <th width="70">Lc Date</th>
                <th width="120">Supp Name</th>
                <th width="90">LC Value</th> 
                <th width="90">Pay Term</th>
                <th width="70">Tenor</th>
                <th width="110">System ID</th>
                <th width="130">Insert date & Time</th>
                <th>insert User Name</th>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$convert_rate=$row[csf("rlz_domestic_value")]/$row[csf("rlz_value")];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<td align="center"><? echo $i; ?></td>
                    <td align="center"><p><? echo $company_arr[$row[csf("importer_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("lc_number")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[csf("lc_date")]); ?>&nbsp;</p></td>
                    <td><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[csf("lc_value")],2); ?></td>
                    <td><p><? echo $pay_term[$row[csf("payterm_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("tenor")]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("btb_system_id")]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("insert_date")]; ?>&nbsp;</p></td> 
                    <td><p><? echo $user_arr[$row[csf("inserted_by")]]; ?>&nbsp;</p></td> 
				</tr>
				<?
				$i++;
				$total_lc_value+=$row[csf("lc_value")];
			}
            ?>
            </tbody>
            <tfoot>
            	<th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
	</fieldset>
	</div>
	<?	
	
	
	$html=ob_get_contents();
	ob_flush();
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) 
	{
		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
      </div> 
    <?
	die;
}

if($action=="accep_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $ref_id."=".$popup_type;die;
	
	$sql_accp="select a.btb_system_id, a.lc_number, a.lc_date, a.supplier_id, a.lc_value, a.payterm_id, a.tenor, a.currency_id, c.invoice_no, c.invoice_date, c.company_acc_date, sum(b.current_acceptance_value) as invoice_value, c.inserted_by, c.insert_date,a.importer_id
	from com_btb_lc_master_details a, com_import_invoice_dtls b, com_import_invoice_mst c
	where a.id=b.btb_lc_id and b.import_invoice_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.id in($ref_id)
	group by a.btb_system_id, a.lc_number, a.lc_date, a.supplier_id, a.lc_value, a.payterm_id, a.tenor, a.currency_id, c.invoice_no, c.invoice_date, c.company_acc_date, c.inserted_by, c.insert_date,a.importer_id order by company_acc_date ASC";
	
	//echo $sql_accp;die;
	$result=sql_select($sql_accp);
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	//$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	ob_start();
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>
    	 <div> 
		<div style="width:870px; margin-left:30px" id="report_div">
    	 <div style="width:870px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
             <div id="report_container"> </div>
        </div>
        <?
         ob_start();
		?>	
	<div style="width:1430px" align="center" id="scroll_body" >
	<fieldset style="width:100%;" >
		 <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1430" align="center" border="1">
            <thead>
                <th width="30">SL</th>
                <th width="100">Company</th> 
                <th width="100">LC No</th> 
                <th width="70">Lc Date</th>
                <th width="120">Supp Name</th>
                <th width="90">Pay Term</th>
                <th width="70">Tenor</th>
                <th width="100">Invoice No</th>
                <th width="70">Invoice Date</th>
                <th width="70">Com. Accep. Date</th>
                <th width="70">Currency</th>
                <th width="90">LC Value</th>
                <th width="90">Bill Value</th>
                <th width="110">System ID</th>
                <th width="130">Insert date & Time</th>
                <th>insert User Name</th>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$convert_rate=$row[csf("rlz_domestic_value")]/$row[csf("rlz_value")];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<td align="center"><? echo $i; ?></td>
                    <td align="center"><p><? echo $company_arr[$row[csf("importer_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("lc_number")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[csf("lc_date")]); ?>&nbsp;</p></td>
                    <td><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>
                    <td><p><? echo $pay_term[$row[csf("payterm_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("tenor")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[csf("invoice_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row[csf("company_acc_date")] !="" && $row[csf("company_acc_date")] !="0000-00-00" ) echo change_date_format($row[csf("company_acc_date")]); ?>&nbsp;</p></td>
                    <td><p><? echo $currency[$row[csf("currency_id")]]; ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[csf("lc_value")],2); ?></td>
                    <td align="right"><? echo number_format($row[csf("invoice_value")],2); ?></td>
                    <td><p><? echo $row[csf("btb_system_id")]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("insert_date")]; ?>&nbsp;</p></td> 
                    <td><p><? echo $user_arr[$row[csf("inserted_by")]]; ?>&nbsp;</p></td> 
				</tr>
				<?
				$i++;
				$total_lc_value+=$row[csf("lc_value")];
				$total_invoice_value+=$row[csf("invoice_value")];

			}
            ?>
            </tbody>
            <tfoot>
            	<th>&nbsp;</th>
            	<th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
                <th align="right"><? echo number_format($total_invoice_value,2); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
	</fieldset>
	</div>
	<?	
	$html=ob_get_contents();
	ob_flush();
	foreach (glob("$user_id*.xls") as $filename)  
	{
		@unlink($filename);
	}
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) 
	{
		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
	});	
	</script>
      </div> 
    <?
	die;
}
if($action=="bank_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $ref_id."=".$popup_type;die;
	if($db_type==0) $maturity_month=" month(c.maturity_date) as maturity_month"; else $maturity_month=" to_char(c.maturity_date,'MM') as maturity_month"; 
	$sql_accp="select a.btb_system_id, a.lc_number, a.lc_date, a.supplier_id, a.lc_value, a.payterm_id, a.tenor, a.currency_id, c.invoice_no, c.invoice_date, c.company_acc_date, c.bank_acc_date, c.maturity_date, $maturity_month, c.bank_ref, sum(b.current_acceptance_value) as invoice_value, c.inserted_by, c.insert_date, d.insert_date as bank_insert_date, d.inserted_by as insert_user
	from com_btb_lc_master_details a, com_import_invoice_dtls b, com_import_invoice_mst c left join   com_import_bank_accept_dtls d on   c.id = d.mst_id
	where a.id=b.btb_lc_id and b.import_invoice_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.id in($ref_id)
	group by a.btb_system_id, a.lc_number, a.lc_date, a.supplier_id, a.lc_value, a.payterm_id, a.tenor, a.currency_id, c.invoice_no, c.invoice_date, c.company_acc_date, c.bank_acc_date, c.maturity_date, c.bank_ref, c.inserted_by, c.insert_date, d.insert_date, d.inserted_by order by bank_acc_date ASC";
	
	//echo $sql_accp;//die;
	$result=sql_select($sql_accp);
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	//$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	ob_start();
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>
    	 <div> 
		<div style="width:870px; margin-left:30px" id="report_div">
    	 <div style="width:870px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
             <div id="report_container"> </div>
        </div>
        <?
         ob_start();
		?>	
	<div style="width:1620px" align="center" id="scroll_body" >
	<fieldset style="width:100%;" >
		 <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1620" align="center" border="1">
            <thead>
                <th width="30">SL</th>
                <th width="100">LC No</th> 
                <th width="70">Lc Date</th>
                <th width="120">Supp Name</th>
                <th width="90">Pay Term</th>
                <th width="70">Tenor</th>
                <th width="100">Invoice No</th>
                <th width="70">Invoice Date</th>
                <th width="70">Com. Accep. Date</th>
                <th width="70">Bank Accep. Date</th>
                <th width="70">Maturity Date</th>
                <th width="70">Maturity Month</th>
                <th width="100">Bank Ref</th>
                <th width="70">Currency</th>
                <th width="90">LC Value</th>
                <th width="90">Bill Value</th>
                <th width="110">System ID</th>
                <th width="130">Insert date & Time</th>
                <th>insert User Name</th>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$convert_rate=$row[csf("rlz_domestic_value")]/$row[csf("rlz_value")];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<td align="center"><? echo $i; ?></td>
                    <td align="center"><p><? echo $row[csf("lc_number")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[csf("lc_date")]); ?>&nbsp;</p></td>
                    <td><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>
                    <td><p><? echo $pay_term[$row[csf("payterm_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("tenor")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[csf("invoice_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row[csf("company_acc_date")] !="" && $row[csf("company_acc_date")] !="0000-00-00" ) echo change_date_format($row[csf("company_acc_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row[csf("bank_acc_date")] !="" && $row[csf("bank_acc_date")] !="0000-00-00" ) echo change_date_format($row[csf("bank_acc_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row[csf("maturity_date")] !="" && $row[csf("maturity_date")] !="0000-00-00" ) echo change_date_format($row[csf("maturity_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $months[$row[csf("maturity_month")]*1]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("bank_ref")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $currency[$row[csf("currency_id")]]; ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[csf("lc_value")],2); ?></td>
                    <td align="right"><? echo number_format($row[csf("invoice_value")],2); ?></td>
                    <td align="center"><p><? echo $row[csf("btb_system_id")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("bank_insert_date")]; ?>&nbsp;</p></td> 
                    <td align="center"><p><? echo $user_arr[$row[csf("insert_user")]]; ?>&nbsp;</p></td> 
				</tr>
				<?
				$i++;
				$total_lc_value+=$row[csf("lc_value")];
				$total_invoice_value+=$row[csf("invoice_value")];
			}
            ?>
            </tbody>
            <tfoot>
            	<th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
                <th align="right"><? echo number_format($total_invoice_value,2); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
	</fieldset>
	</div>
	<?	
	$html=ob_get_contents();
	ob_flush();
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) 
	{
		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
      </div> 
    <?
	die;
}

if($action=="bank_details_mature")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $ref_id."=".$popup_type;die;
	$pay_sql="select INVOICE_ID, PAYMENT_DATE, ACCEPTED_AMMOUNT from COM_IMPORT_PAYMENT where status_active=1 and is_deleted=0";
	//echo $pay_sql;die;
	$pay_sql_result=sql_select($pay_sql);
	$pay_data=array();$pay_value=array();
	foreach($pay_sql_result as $val)
	{
		$pay_data[$val["INVOICE_ID"]]=$val["PAYMENT_DATE"];
		$pay_value[$val["INVOICE_ID"]]+=$val["ACCEPTED_AMMOUNT"];
	}
	unset($pay_sql_result);
	
	if($db_type==0) $maturity_month=" month(c.maturity_date) as maturity_month"; else $maturity_month=" to_char(c.maturity_date,'MM') as maturity_month"; 
	$sql_accp="SELECT a.btb_system_id, a.lc_number, a.lc_date, a.supplier_id, a.lc_value, a.payterm_id, a.tenor, a.currency_id, c.id as inv_id, c.invoice_no, c.invoice_date, c.company_acc_date, c.bank_acc_date, c.maturity_date, c.edf_paid_date, $maturity_month, c.bank_ref, sum(b.current_acceptance_value) as invoice_value, c.inserted_by, c.insert_date, a.lc_category,a.importer_id
	from com_btb_lc_master_details a, com_import_invoice_dtls b, com_import_invoice_mst c
	where a.id=b.btb_lc_id and b.import_invoice_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.id in($ref_id)
	group by a.btb_system_id, a.lc_number, a.lc_date, a.supplier_id, a.lc_value, a.payterm_id, a.tenor, a.currency_id, c.id, c.invoice_no, c.invoice_date, c.company_acc_date, c.bank_acc_date, c.maturity_date, c.edf_paid_date, c.bank_ref, c.inserted_by, c.insert_date, a.lc_category,a.importer_id 
	order by maturity_date ASC";
	
	//echo $sql_accp;die;
	$result=sql_select($sql_accp);
	$tot_maturity_edf=$tot_maturity_btb=$tot_paid_edf=$tot_paid_btb=0;
	foreach($result as $row)
	{
		if(abs($row[csf("lc_category")])==3 || abs($row[csf("lc_category")])==5 || abs($row[csf("lc_category")])==11)
		{
			$tot_maturity_edf+=$row[csf("invoice_value")];
			if($row[csf("edf_paid_date")]!="" && $row[csf("edf_paid_date")]!="0000-00-00")
			{
				$tot_paid_edf+=$row[csf("invoice_value")];
			}
		}
		else
		{
			$tot_maturity_btb+=$row[csf("invoice_value")];
			$tot_paid_btb+=$pay_value[$row[csf("inv_id")]];
		}
	}
	
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	//$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	ob_start();
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>
    	 <div> 
		<div style="width:870px; margin-left:30px" id="report_div">
    	 <div style="width:870px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
             <div id="report_container"> </div>
        </div>
        <?
         ob_start();
		?>	
	<div style="width:1870px" align="center" id="scroll_body" >

	<fieldset style="width:100%;" >
		<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="900" align="left" border="1">
        	<thead>
            	<tr>
                	<th colspan="2" width="200">Maturity Value</th>
                    <th width="100" rowspan="2">Total Maturity</th>
                    <th colspan="2" width="200">Paid Value</th>
                    <th width="100" rowspan="2">Total Paid</th>
                    <th colspan="2" width="200">Balance Value</th>
                    <th width="100" rowspan="2">Total Balance</th>
                </tr>
                <tr>
                	<th width="100">EDF</th>
                    <th width="100">BTB</th>
                    <th width="100">EDF</th>
                    <th width="100">BTB</th>
                    <th width="100">EDF</th>
                    <th width="100">BTB</th>
                </tr>
            </thead>
            <tbody>
            	<?
				$bal_edf=$tot_maturity_edf-$tot_paid_edf;
				$bal_btb=$tot_maturity_btb-$tot_paid_btb;
				$tot_maturity=$tot_maturity_edf+$tot_maturity_btb;
				$tot_paid=$tot_paid_edf+$tot_paid_btb;
				$tot_bal=$bal_edf+$bal_btb;
				?>
            	<tr bgcolor="#FFFFFF">
                	<td align="right"><? echo number_format($tot_maturity_edf,2) ?></td>
                    <td align="right"><? echo number_format($tot_maturity_btb,2) ?></td>
                    <td align="right"><? echo number_format($tot_maturity,2) ?></td>
                    <td align="right"><? echo number_format($tot_paid_edf,2) ?></td>
                    <td align="right"><? echo number_format($tot_paid_btb,2) ?></td>
                    <td align="right"><? echo number_format($tot_paid,2) ?></td>
                    <td align="right"><? echo number_format($bal_edf,2) ?></td>
                    <td align="right"><? echo number_format($bal_btb,2) ?></td>
                    <td align="right"><? echo number_format($tot_bal,2) ?></td>
                </tr>
            </tbody>
        </table>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="900" align="left" border="1"><tr><td>&nbsp;</td></tr></table>		    		
		<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1870" align="center" border="1">
            <thead>
                <th width="30">SL</th>
                <th width="100">Company</th> 
                <th width="100">LC No</th> 
                <th width="70">Lc Date</th>
                <th width="120">Supp Name</th>
                <th width="90">Pay Term</th>
                <th width="70">Tenor</th>
                <th width="100">Invoice No</th>
                <th width="70">Invoice Date</th>
                <th width="70">Com. Accep. Date</th>
                <th width="70">Bank Accep. Date</th>
                <th width="70">Maturity Date</th>
                <th width="70">Maturity Month</th>
                <th width="100">Bank Ref</th>
                <th width="70">Currency</th>
                <th width="90">LC Value</th>
                <th width="90">Bill Value</th>
                <th width="70">Status</th>
                <th width="80">Paid Date</th>
                <th width="110">System ID</th>
                <th width="130">Insert date & Time</th>
                <th>insert User Name</th>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$convert_rate=$row[csf("rlz_domestic_value")]/$row[csf("rlz_value")];
				$paid_date="";
				if(abs($row[csf("lc_category")])==3 || abs($row[csf("lc_category")])==5 || abs($row[csf("lc_category")])==11)
				{
					$paid_date=$row[csf("edf_paid_date")];
				}
				else
				{
					$paid_date=$pay_data[$row[csf("inv_id")]];
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<td align="center"><? echo $i; ?></td>
                    <td align="center"><p><? echo $company_arr[$row[csf("importer_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("lc_number")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[csf("lc_date")]); ?>&nbsp;</p></td>
                    <td><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>
                    <td><p><? echo $pay_term[$row[csf("payterm_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("tenor")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[csf("invoice_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row[csf("company_acc_date")] !="" && $row[csf("company_acc_date")] !="0000-00-00" ) echo change_date_format($row[csf("company_acc_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row[csf("bank_acc_date")] !="" && $row[csf("bank_acc_date")] !="0000-00-00" ) echo change_date_format($row[csf("bank_acc_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row[csf("maturity_date")] !="" && $row[csf("maturity_date")] !="0000-00-00" ) echo change_date_format($row[csf("maturity_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $months[$row[csf("maturity_month")]*1]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("bank_ref")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $currency[$row[csf("currency_id")]]; ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[csf("lc_value")],2); ?></td>
                    <td align="right"><? echo number_format($row[csf("invoice_value")],2); ?></td>
                    <td align="center"><? if($paid_date !="" && $paid_date!="0000-00-00") echo "Paid"; else echo "&nbsp;"; ?></td>
                    <td align="center"><? if($paid_date !="" && $paid_date!="0000-00-00") echo change_date_format($paid_date); ?></td>
                    <td align="center"><p><? echo $row[csf("btb_system_id")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[csf("insert_date")]; ?>&nbsp;</p></td> 
                    <td align="center"><p><? echo $user_arr[$row[csf("inserted_by")]]; ?>&nbsp;</p></td> 
				</tr>
				<?
				$i++;
				$total_lc_value+=$row[csf("lc_value")];
				$total_invoice_value+=$row[csf("invoice_value")];
			}
            ?>
            </tbody>
            <tfoot>
            	<th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
                <th align="right"><? echo number_format($total_invoice_value,2); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
	</fieldset>
	</div>
	<?	
	$html=ob_get_contents();
	ob_flush();
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) 
	{
		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
      </div> 
    <?
	die;
}

if($action=="payment_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$date_cond="";$bank_cond="";
	if($date_form!="" && $date_to !="") $date_cond=" and d.payment_date between '$date_form' and '$date_to'";
	if($bank_id>0) $bank_cond=" and a.issuing_bank_id=$bank_id";	
	if($db_type==0) $maturity_month=" month(c.maturity_date) as maturity_month"; else $maturity_month=" to_char(c.maturity_date,'MM') as maturity_month"; 
	$sql_payment="select a.btb_system_id, a.lc_number, a.lc_date, a.supplier_id, a.lc_value, a.payterm_id, a.tenor, a.currency_id, c.id as inv_id, c.invoice_no, c.invoice_date, c.company_acc_date, c.bank_acc_date, c.maturity_date, $maturity_month, c.bank_ref, c.retire_source, c.edf_paid_date, d.payment_date as payment_date, b.current_acceptance_value as invoice_value, d.inserted_by as inserted_by, d.insert_date as insert_date, b.id as accep_dtls_id, b.current_acceptance_value, d.id as pay_id, d.accepted_ammount as accepted_ammount, a.importer_id, a.issuing_bank_id
	from com_btb_lc_master_details a, com_import_invoice_dtls b, com_import_invoice_mst c, com_import_payment d
	where a.id=b.btb_lc_id and b.import_invoice_id=c.id and c.id=d.invoice_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.invoice_id in($ref_id) and c.retire_source<>30 $date_cond $bank_cond
	union all
	select a.btb_system_id, a.lc_number, a.lc_date, a.supplier_id, a.lc_value, a.payterm_id, a.tenor, a.currency_id, c.id as inv_id, c.invoice_no, c.invoice_date, c.company_acc_date, c.bank_acc_date, c.maturity_date, $maturity_month, c.bank_ref, c.retire_source, c.edf_paid_date, d.payment_date as payment_date, b.current_acceptance_value as invoice_value, d.inserted_by as inserted_by, d.insert_date as insert_date, b.id as accep_dtls_id, b.current_acceptance_value, d.id as pay_id, d.accepted_ammount as accepted_ammount, a.importer_id, a.issuing_bank_id
	from com_btb_lc_master_details a, com_import_invoice_dtls b, com_import_invoice_mst c, COM_IMPORT_PAYMENT_COM d
	where a.id=b.btb_lc_id and b.import_invoice_id=c.id and c.id=d.invoice_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.invoice_id in($ref_id) and c.retire_source=30 $date_cond $bank_cond
	order by payment_date ASC";
	
	//echo $sql_payment;
	
	/*$sql_payment="select a.btb_system_id, a.lc_number, a.lc_date, a.supplier_id, a.lc_value, a.payterm_id, a.tenor, a.currency_id, c.id as inv_id, c.invoice_no, c.invoice_date, c.company_acc_date, c.bank_acc_date, c.maturity_date, $maturity_month, c.bank_ref, c.edf_paid_date as payment_date, c.inserted_by as inserted_by, c.insert_date as insert_date, b.id as inv_dtls_id, b.current_acceptance_value as invoice_value, b.id as pay_id, b.current_acceptance_value as accepted_ammount, a.lc_category, c.edf_paid_date
	from com_btb_lc_master_details a, com_import_invoice_dtls b, com_import_invoice_mst c
	where a.id=b.btb_lc_id and b.import_invoice_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id in($ref_id)
	order by payment_date ASC";*/
	
	//echo $sql_payment;die;
	$result=sql_select($sql_payment);
	$all_inv_id=array();
	$payment_data=array();
	foreach($result as $row)
	{
		$payment_data[$row[csf("inv_id")]]["importer_id"]=$row[csf("importer_id")];
		$payment_data[$row[csf("inv_id")]]["btb_system_id"]=$row[csf("btb_system_id")];
		$payment_data[$row[csf("inv_id")]]["lc_number"]=$row[csf("lc_number")];
		$payment_data[$row[csf("inv_id")]]["lc_date"]=$row[csf("lc_date")];
		$payment_data[$row[csf("inv_id")]]["supplier_id"]=$row[csf("supplier_id")];
		$payment_data[$row[csf("inv_id")]]["lc_value"]=$row[csf("lc_value")];
		$payment_data[$row[csf("inv_id")]]["payterm_id"]=$row[csf("payterm_id")];
		$payment_data[$row[csf("inv_id")]]["tenor"]=$row[csf("tenor")];
		$payment_data[$row[csf("inv_id")]]["currency_id"]=$row[csf("currency_id")];
		
		$payment_data[$row[csf("inv_id")]]["inv_id"]=$row[csf("inv_id")];
		$payment_data[$row[csf("inv_id")]]["invoice_no"]=$row[csf("invoice_no")];
		$payment_data[$row[csf("inv_id")]]["invoice_date"]=$row[csf("invoice_date")];
		$payment_data[$row[csf("inv_id")]]["company_acc_date"]=$row[csf("company_acc_date")];
		$payment_data[$row[csf("inv_id")]]["bank_acc_date"]=$row[csf("bank_acc_date")];
		$payment_data[$row[csf("inv_id")]]["maturity_date"]=$row[csf("maturity_date")];
		$payment_data[$row[csf("inv_id")]]["maturity_month"]=$row[csf("maturity_month")];
		$payment_data[$row[csf("inv_id")]]["bank_ref"]=$row[csf("bank_ref")];
		$payment_data[$row[csf("inv_id")]]["retire_source"]=$row[csf("retire_source")];
		
		//$payment_data[$row[csf("inv_id")]]["payment_date"]=$row[csf("payment_date")];
		$payment_data[$row[csf("inv_id")]]["inserted_by"]=$row[csf("inserted_by")];
		$payment_data[$row[csf("inv_id")]]["insert_date"]=$row[csf("insert_date")];
		if($inv_dtls_check[$row[csf("inv_dtls_id")]]=="")
		{
			$inv_dtls_check[$row[csf("inv_dtls_id")]]=$row[csf("inv_dtls_id")];
			$payment_data[$row[csf("inv_id")]]["invoice_value"]+=$row[csf("invoice_value")];
		}
		$issu_bank=$row[csf("issuing_bank_id")];
		if($payment_check[$row[csf("pay_id")]]=="" && $issu_bank)
		{
			$payment_check[$row[csf("pay_id")]]=$row[csf("pay_id")];
			$payment_data[$row[csf("inv_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
		}
		$payment_data[$row[csf("inv_id")]]["payment_date"]=$row[csf("payment_date")];
	}
	//echo "<pre>";print_r($payment_data);die;
	
	/*if(!empty($all_inv_id))
	{
		$sql_inv="select c.id as inv_id, sum(b.current_acceptance_value) as invoice_value
		from com_import_invoice_dtls b, com_import_invoice_mst c
		where b.import_invoice_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.id in(".implode(",",$all_inv_id).")
		group by c.id";
		$result_inv=sql_select($sql_inv);
		$inv_data=array();
		foreach($result_inv as $row)
		{
			$inv_data[$row[csf("inv_id")]]=$row[csf("invoice_value")];
		}
	}
	$pay_sql="select d.invoice_id, sum(d.accepted_ammount) as pay_amt from com_import_payment d where status_active=1 and d.is_deleted=0 and d.id in($ref_id) group by d.invoice_id";
	//echo $pay_sql;die;
	$pay_result=sql_select($pay_sql);
	$pay_data=array();
	foreach($pay_result as $row)
	{
		$pay_data[$row[csf("invoice_id")]]=$row[csf("pay_amt")];
	}*/
	//echo "<pre>";print_r($all_inv_id);die;
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	//$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	ob_start();
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>
    	 <div> 
		<div style="width:870px; margin-left:30px" id="report_div">
    	 <div style="width:870px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
             <div id="report_container"> </div>
        </div>
        <?
         ob_start();
		?>	
	<div style="width:1840px" align="center" id="scroll_body" >
	<fieldset style="width:100%;">
		 <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1830" align="center" border="1" align="left">
            <thead>
                <th width="30">SL</th>
                <th width="100">Company</th> 
                <th width="100">LC No</th> 
                <th width="70">Lc Date</th>
                <th width="120">Supp Name</th>
                <th width="80">Pay Term</th>
                <th width="60">Tenor</th>
                <th width="100">Invoice No</th>
                <th width="70">Invoice Date</th>
                <th width="70">Com. Accep. Date</th>
                <th width="70">Bank Accep. Date</th>
                <th width="70">Maturity Date</th>
                <th width="70">Maturity Month</th>
                <th width="90">Bank Ref</th>
                <th width="70">Paid Date</th>
                <th width="60">Currency</th>
                <th width="90">LC Value</th>
                <th width="90">Bill Value</th>
                <th width="90">Paid Amount</th>
                <th width="110">System ID</th>
                <th width="130">Insert date & Time</th>
                <th>insert User Name</th>
            </thead>
            <tbody>
            <?
			$i=1;
			$inv_check=array();
			foreach($payment_data as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$convert_rate=$row[("rlz_domestic_value")]/$row[("rlz_value")];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<td align="center"><? echo $i; ?></td>
                    <td align="center"><p><? echo $company_arr[$row[("importer_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[("lc_number")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[("lc_date")]); ?>&nbsp;</p></td>
                    <td><p><? echo $supplier_arr[$row[("supplier_id")]]; ?>&nbsp;</p></td>
                    <td><p><? echo $pay_term[$row[("payterm_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[("tenor")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[("invoice_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[("invoice_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row[("company_acc_date")] !="" && $row[("company_acc_date")] !="0000-00-00" ) echo change_date_format($row[("company_acc_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row[("bank_acc_date")] !="" && $row[("bank_acc_date")] !="0000-00-00" ) echo change_date_format($row[("bank_acc_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row[("maturity_date")] !="" && $row[("maturity_date")] !="0000-00-00" ) echo change_date_format($row[("maturity_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $months[$row[("maturity_month")]*1]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[("bank_ref")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row[("payment_date")] !="" && $row[("payment_date")] !="0000-00-00" ) echo change_date_format($row[("payment_date")]); ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $currency[$row[("currency_id")]]; ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[("lc_value")],2); ?></td>
                    <td align="right"><? echo number_format($row[("invoice_value")],2); ?></td>
                    <td align="right"><? echo number_format($row[("accepted_ammount")],2); ?></td>
                    <td align="center"><p><? echo $row[("btb_system_id")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $row[("insert_date")]; ?>&nbsp;</p></td> 
                    <td align="center"><p><? echo $user_arr[$row[("inserted_by")]]; ?>&nbsp;</p></td> 
				</tr>
				<?
				$i++;
				$total_lc_value+=$row[("lc_value")];
				$total_invoice_value+=$row[("invoice_value")];				
				$total_pay_value+=$row[("accepted_ammount")];
				
			}
            ?>
            </tbody>
            <tfoot>
            	<th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
                <th align="right"><? echo number_format($total_invoice_value,2); ?></th>
                <th align="right"><? echo number_format($total_pay_value,2); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
	</fieldset>
	</div>
	<?	
	
	
	$html=ob_get_contents();
	ob_flush();
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) 
	{
		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
      </div> 
    <?
	die;
}

?>
