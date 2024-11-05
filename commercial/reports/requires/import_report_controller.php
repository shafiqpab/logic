<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == '' ) header('location:login.php');
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action==='report_generate')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_id      = str_replace("'","",$cbo_company_id);
	$cbo_issue_banking   = str_replace("'","",$cbo_issue_banking);
	$cbo_item_category_id= str_replace("'","",$cbo_item_category_id);
	$cbo_lc_type_id      = trim(str_replace("'","",$cbo_lc_type_id));
	$cbo_search_by       = str_replace("'","",$cbo_search_by);
	$txt_search_common   = trim(str_replace("'","",$txt_search_common));
	$cbo_supplier_id     = str_replace("'","",$cbo_supplier_id);
	$cbo_based_on        = str_replace("'","",$cbo_based_on);
	$txt_date_from       = str_replace("'","",$txt_date_from);
	$txt_date_to         = str_replace("'","",$txt_date_to);
	$txt_date            = str_replace("'","",$txt_date);

	$company_cond=$supplier_cond=$item_category_cond=$issue_banking_cond='';
	if ($cbo_company_id != 0) $company_cond=" and a.importer_id=$cbo_company_id";
	if ($cbo_supplier_id != 0) $supplier_cond=" and a.supplier_id=$cbo_supplier_id";
	if ($cbo_issue_banking != 0) $issue_banking_cond =" and d.issuing_bank_id=$cbo_issue_banking";
	if ($cbo_item_category_id != 0) $item_category_cond =" and a.item_category_id in($cbo_item_category_id)";

	$lc_type_cond='';
	if ($cbo_lc_type_id != 0) $lc_type_cond =" and d.lc_type_id=$cbo_lc_type_id";
	

	if ($db_type==0)
	{
		if ($txt_date != '') $txt_date = date('Y-m-d', strtotime($txt_date));		
		if ($txt_date_from != '' && $txt_date_to != '')
		{			
			$txt_date_from = date('Y-m-d', strtotime($txt_date_from));
			$txt_date_to   = date('Y-m-d', strtotime($txt_date_to));			
		}
	}
	else
	{
		if ($txt_date != '') $txt_date=date('d-M-Y', strtotime($txt_date));
		if ($txt_date_from != '' && $txt_date_to != '')
		{			
			$txt_date_from = date('d-M-Y', strtotime($txt_date_from));
			$txt_date_to = date('d-M-Y', strtotime($txt_date_to));			
		}
	}


	$date_cond=$pi_number_cond=$lc_number_cond='';
	if ($txt_search_common != '') 
	{
		if ($cbo_search_by==1)
		{
			$cbo_based_on=1;
			$pi_number_cond =" and a.pi_number='$txt_search_common'";
		} 
		else 
		{
			$cbo_based_on=3;
			$lc_number_cond =" and d.lc_number='$txt_search_common'";
		}		
	}
	else
	{
		if ($cbo_based_on==1) $date_cond=" and a.pi_date between '$txt_date_from' and '$txt_date_to'";
		else if ($cbo_based_on==2) $date_cond=" and a.pi_date <= '$txt_date'";
		else if ($cbo_based_on==3) $date_cond=" and d.lc_date between '$txt_date_from' and '$txt_date_to'";
		else if ($cbo_based_on==4) $date_cond=" and d.lc_date <= '$txt_date'";
		else if ($cbo_based_on==5) $date_cond=" and f.invoice_date between '$txt_date_from' and '$txt_date_to'";
		else if ($cbo_based_on==6) $date_cond=" and f.invoice_date <= '$txt_date'";
		else if ($cbo_based_on==11) $date_cond=" and g.payment_date between '$txt_date_from' and '$txt_date_to'";
	}	


	$issueBank_arr = return_library_array("select id,bank_name from lib_bank where is_deleted=0","id","bank_name");
	$supplier_arr= return_library_array("select id,supplier_name from lib_supplier where is_deleted=0", 'id','supplier_name');
	$payment_data_arr=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amount from  com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","invoice_id","paid_amount");


	if ($cbo_based_on==1 || $cbo_based_on==2)
	{			
		if($cbo_based_on==1)
		{
			$sql="SELECT a.id as PI_ID, a.pi_number as PI_NUMBER, a.supplier_id as SUPPLIER_ID, a.pi_date as PI_DATE, a.hs_code as HS_CODE, a.item_category_id as ITEM_CATEGORY_ID, a.net_total_amount as PI_VALUE, d.id as BTB_ID, d.lc_number as LC_NUMBER, d.lc_date as LC_DATE, d.lc_value as LC_VALUE, d.lc_type_id as LC_TYPE_ID, d.inco_term_id as INCO_TERM_ID, d.payterm_id as PAYTERM_ID, d.lc_expiry_date as LC_EXPIRY_DATE, d.issuing_bank_id as ISSUING_BANK_ID, d.tolerance as TOLERANCE, d.ud_no as UD_NO, d.UD_DATE as UD_DATE, d.cover_note_no as INS_COV_NO, f.id as INVOICE_ID, f.invoice_no as INVOICE_NO, f.invoice_date as INVOICE_DATE, f.company_acc_date as COMPANY_ACC_DATE, f.bank_acc_date as BANK_ACC_DATE, f.maturity_date as MATURITY_DATE, f.edf_paid_date as EDF_PAID_DATE, f.retire_source as RETIRE_SOURCE, sum(e.current_acceptance_value) as ACCEPTANCE_VALUE, g.payment_date as PAYMENT_DATE, sum(g.accepted_ammount) as PAYMENT_VALUE
			from com_pi_master_details a 		
			left join com_btb_lc_pi c on a.id=c.pi_id and c.status_active=1 and c.is_deleted=0 
			left join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id and d.status_active=1 and d.is_deleted=0 
			left join com_import_invoice_dtls e on d.id=e.btb_lc_id and e.status_active=1 and e.is_deleted=0
			left join com_import_invoice_mst f on e.import_invoice_id=f.id and f.status_active=1 and f.is_deleted=0
			left join com_import_payment g on f.id=g.invoice_id and g.status_active=1 and g.is_deleted=0
			where a.net_total_amount>0 and a.status_active=1 and a.is_deleted=0 $company_cond $pi_number_cond $item_category_cond $supplier_cond $date_cond
			group by a.id, a.pi_number, a.supplier_id, a.pi_date, a.net_total_amount, a.hs_code, a.item_category_id, d.id, d.lc_number, d.lc_date, d.lc_value, d.lc_type_id, d.inco_term_id, d.payterm_id, d.lc_expiry_date, d.issuing_bank_id, d.tolerance, d.ud_no, d.ud_date, d.cover_note_no, f.id, f.invoice_no, f.invoice_date, f.company_acc_date, f.bank_acc_date, f.maturity_date, f.edf_paid_date, f.retire_source, g.payment_date
			order by a.pi_date desc";
		}
		else
		{
			$sql="SELECT a.id as PI_ID, a.pi_number as PI_NUMBER, a.supplier_id as SUPPLIER_ID, a.pi_date as PI_DATE, a.hs_code as HS_CODE, a.item_category_id as ITEM_CATEGORY_ID, a.net_total_amount as PI_VALUE
			from com_pi_master_details a
			where a.net_total_amount>0 and a.status_active=1 and a.is_deleted=0 and a.pi_date > '31-Dec-2019' and a.id not in(select pi_id from com_btb_lc_pi where status_active=1 and is_deleted=0) $company_cond $pi_number_cond $item_category_cond $supplier_cond $date_cond
			group by a.id, a.pi_number, a.supplier_id, a.pi_date, a.net_total_amount, a.hs_code, a.item_category_id
			order by a.pi_date desc";
			//echo $sql;die;
		}
	}
	else if ($cbo_based_on==3 || $cbo_based_on==4)
	{
		
		
		if($cbo_based_on==3)
		{
			$sql="SELECT a.id as PI_ID, a.pi_number as PI_NUMBER, a.supplier_id as SUPPLIER_ID, a.pi_date as PI_DATE, a.hs_code as HS_CODE, a.item_category_id as ITEM_CATEGORY_ID, a.net_total_amount as PI_VALUE, d.id as BTB_ID, d.lc_number as LC_NUMBER, d.lc_date as LC_DATE, d.lc_value as LC_VALUE, d.lc_type_id as LC_TYPE_ID, d.inco_term_id as INCO_TERM_ID, d.payterm_id as PAYTERM_ID, d.lc_expiry_date as LC_EXPIRY_DATE, d.issuing_bank_id as ISSUING_BANK_ID, d.tolerance as TOLERANCE, d.ud_no as UD_NO, d.UD_DATE as UD_DATE, d.cover_note_no as INS_COV_NO, f.id as INVOICE_ID, f.invoice_no as INVOICE_NO, f.invoice_date as INVOICE_DATE, f.company_acc_date as COMPANY_ACC_DATE, f.bank_acc_date as BANK_ACC_DATE, f.maturity_date as MATURITY_DATE, f.edf_paid_date as EDF_PAID_DATE, f.retire_source as RETIRE_SOURCE, sum(e.current_acceptance_value) as ACCEPTANCE_VALUE, g.payment_date as PAYMENT_DATE, sum(g.accepted_ammount) as PAYMENT_VALUE
			from com_pi_master_details a 		
			join com_btb_lc_pi c on a.id=c.pi_id and c.status_active=1 and c.is_deleted=0 
			join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id and d.status_active=1 and d.is_deleted=0
			left join com_import_invoice_dtls e on d.id=e.btb_lc_id and e.status_active=1 and e.is_deleted=0
			left join com_import_invoice_mst f on e.import_invoice_id=f.id and f.status_active=1 and f.is_deleted=0
			left join com_import_payment g on f.id=g.invoice_id and g.status_active=1 and g.is_deleted=0
			where a.status_active=1 and a.is_deleted=0 $company_cond $pi_number_cond $lc_number_cond $issue_banking_cond $item_category_cond $supplier_cond $date_cond
			group by a.id, a.pi_number, a.supplier_id, a.pi_date, a.net_total_amount, a.hs_code, a.item_category_id, d.id, d.lc_number, d.lc_date, d.lc_value, d.lc_type_id, d.inco_term_id, d.payterm_id, d.lc_expiry_date, d.issuing_bank_id, d.tolerance, d.ud_no, d.ud_date, d.cover_note_no, f.id, f.invoice_no, f.invoice_date, f.company_acc_date, f.bank_acc_date, f.maturity_date, f.edf_paid_date, f.retire_source, g.payment_date
			order by a.pi_date desc";
		}
		else
		{
			$sql="SELECT a.id as PI_ID, a.pi_number as PI_NUMBER, a.supplier_id as SUPPLIER_ID, a.pi_date as PI_DATE, a.hs_code as HS_CODE, a.item_category_id as ITEM_CATEGORY_ID, a.net_total_amount as PI_VALUE, d.id as BTB_ID, d.lc_number as LC_NUMBER, d.lc_date as LC_DATE, d.lc_value as LC_VALUE, d.lc_type_id as LC_TYPE_ID, d.inco_term_id as INCO_TERM_ID, d.payterm_id as PAYTERM_ID, d.lc_expiry_date as LC_EXPIRY_DATE, d.issuing_bank_id as ISSUING_BANK_ID, d.tolerance as TOLERANCE, d.ud_no as UD_NO, d.UD_DATE as UD_DATE, d.cover_note_no as INS_COV_NO
			from com_pi_master_details a 		
			join com_btb_lc_pi c on a.id=c.pi_id and c.status_active=1 and c.is_deleted=0 
			join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id and d.status_active=1 and d.is_deleted=0 and d.lc_date > '31-Dec-2019' 
			where a.status_active=1 and a.is_deleted=0 and d.id not in(select btb_lc_id from com_import_invoice_dtls where status_active=1 and is_deleted=0) $company_cond $pi_number_cond $lc_number_cond $issue_banking_cond $item_category_cond $supplier_cond $date_cond
			group by a.id, a.pi_number, a.supplier_id, a.pi_date, a.net_total_amount, a.hs_code, a.item_category_id, d.id, d.lc_number, d.lc_date, d.lc_value, d.lc_type_id, d.inco_term_id, d.payterm_id, d.lc_expiry_date, d.issuing_bank_id, d.tolerance, d.ud_no, d.ud_date, d.cover_note_no
			order by a.pi_date desc";
		}
		
		//echo $sql;die;
	}
	else if ($cbo_based_on==5 || $cbo_based_on==6)
	{
		if($cbo_based_on==5)
		{
			$sql="SELECT a.id as PI_ID, a.pi_number as PI_NUMBER, a.supplier_id as SUPPLIER_ID, a.pi_date as PI_DATE, a.hs_code as HS_CODE, a.item_category_id as ITEM_CATEGORY_ID, a.net_total_amount as PI_VALUE, d.id as BTB_ID, d.lc_number as LC_NUMBER, d.lc_date as LC_DATE, d.lc_value as LC_VALUE, d.lc_type_id as LC_TYPE_ID, d.inco_term_id as INCO_TERM_ID, d.payterm_id as PAYTERM_ID, d.lc_expiry_date as LC_EXPIRY_DATE, d.issuing_bank_id as ISSUING_BANK_ID, d.tolerance as TOLERANCE, d.ud_no as UD_NO, d.UD_DATE as UD_DATE, d.cover_note_no as INS_COV_NO, f.id as INVOICE_ID, f.invoice_no as INVOICE_NO, f.invoice_date as INVOICE_DATE, f.company_acc_date as COMPANY_ACC_DATE, f.bank_acc_date as BANK_ACC_DATE, f.maturity_date as MATURITY_DATE, f.edf_paid_date as EDF_PAID_DATE, f.retire_source as RETIRE_SOURCE, sum(e.current_acceptance_value) as ACCEPTANCE_VALUE, g.payment_date as PAYMENT_DATE, sum(g.accepted_ammount) as PAYMENT_VALUE
			from com_pi_master_details a 		
			join com_btb_lc_pi c on a.id=c.pi_id and c.status_active=1 and c.is_deleted=0 
			join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id and d.status_active=1 and d.is_deleted=0
			join com_import_invoice_dtls e on d.id=e.btb_lc_id and e.status_active=1 and e.is_deleted=0
			join com_import_invoice_mst f on e.import_invoice_id=f.id and f.status_active=1 and f.is_deleted=0
			left join com_import_payment g on f.id=g.invoice_id and g.status_active=1 and g.is_deleted=0
			where a.status_active=1 and a.is_deleted=0 $company_cond $pi_number_cond $lc_number_cond $issue_banking_cond $item_category_cond $supplier_cond $date_cond
			group by a.id, a.pi_number, a.supplier_id, a.pi_date, a.net_total_amount, a.hs_code, a.item_category_id, d.id, d.lc_number, d.lc_date, d.lc_value, d.lc_type_id, d.inco_term_id, d.payterm_id, d.lc_expiry_date, d.issuing_bank_id, d.tolerance, d.ud_no, d.ud_date, d.cover_note_no, f.id, f.invoice_no, f.invoice_date, f.company_acc_date, f.bank_acc_date, f.maturity_date, f.edf_paid_date, f.retire_source, g.payment_date
			order by a.pi_date desc";
		}
		else
		{
			$sql="SELECT a.id as PI_ID, a.pi_number as PI_NUMBER, a.supplier_id as SUPPLIER_ID, a.pi_date as PI_DATE, a.hs_code as HS_CODE, a.item_category_id as ITEM_CATEGORY_ID, a.net_total_amount as PI_VALUE, d.id as BTB_ID, d.lc_number as LC_NUMBER, d.lc_date as LC_DATE, d.lc_value as LC_VALUE, d.lc_type_id as LC_TYPE_ID, d.inco_term_id as INCO_TERM_ID, d.payterm_id as PAYTERM_ID, d.lc_expiry_date as LC_EXPIRY_DATE, d.issuing_bank_id as ISSUING_BANK_ID, d.tolerance as TOLERANCE, d.ud_no as UD_NO, d.UD_DATE as UD_DATE, d.cover_note_no as INS_COV_NO, f.id as INVOICE_ID, f.invoice_no as INVOICE_NO, f.invoice_date as INVOICE_DATE, f.company_acc_date as COMPANY_ACC_DATE, f.bank_acc_date as BANK_ACC_DATE, f.maturity_date as MATURITY_DATE, f.edf_paid_date as EDF_PAID_DATE, f.retire_source as RETIRE_SOURCE, sum(e.current_acceptance_value) as ACCEPTANCE_VALUE
			from com_pi_master_details a 		
			join com_btb_lc_pi c on a.id=c.pi_id and c.status_active=1 and c.is_deleted=0 
			join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id and d.status_active=1 and d.is_deleted=0
			join com_import_invoice_dtls e on d.id=e.btb_lc_id and e.status_active=1 and e.is_deleted=0
			join com_import_invoice_mst f on e.import_invoice_id=f.id and f.status_active=1 and f.is_deleted=0 and f.invoice_date > '31-Dec-2019'
			where a.status_active=1 and a.is_deleted=0 and f.id not in(select invoice_id from com_import_payment where status_active=1 and is_deleted=0) $company_cond $pi_number_cond $lc_number_cond $issue_banking_cond $item_category_cond $supplier_cond $date_cond
			group by a.id, a.pi_number, a.supplier_id, a.pi_date, a.net_total_amount, a.hs_code, a.item_category_id, d.id, d.lc_number, d.lc_date, d.lc_value, d.lc_type_id, d.inco_term_id, d.payterm_id, d.lc_expiry_date, d.issuing_bank_id, d.tolerance, d.ud_no, d.ud_date, d.cover_note_no, f.id, f.invoice_no, f.invoice_date, f.company_acc_date, f.bank_acc_date, f.maturity_date, f.edf_paid_date, f.retire_source
			order by a.pi_date desc";
		}
		//echo $sql;die;
		
	}
	else if ($cbo_based_on==11)
	{
		$sql="SELECT a.id as PI_ID, a.pi_number as PI_NUMBER, a.supplier_id as SUPPLIER_ID, a.pi_date as PI_DATE, a.hs_code as HS_CODE, a.item_category_id as ITEM_CATEGORY_ID, a.net_total_amount as PI_VALUE, d.id as BTB_ID, d.lc_number as LC_NUMBER, d.lc_date as LC_DATE, d.lc_value as LC_VALUE, d.lc_type_id as LC_TYPE_ID, d.inco_term_id as INCO_TERM_ID, d.payterm_id as PAYTERM_ID, d.lc_expiry_date as LC_EXPIRY_DATE, d.issuing_bank_id as ISSUING_BANK_ID, d.tolerance as TOLERANCE, d.ud_no as UD_NO, d.UD_DATE as UD_DATE, d.cover_note_no as INS_COV_NO, f.id as INVOICE_ID, f.invoice_no as INVOICE_NO, f.invoice_date as INVOICE_DATE, f.company_acc_date as COMPANY_ACC_DATE, f.bank_acc_date as BANK_ACC_DATE, f.maturity_date as MATURITY_DATE, f.edf_paid_date as EDF_PAID_DATE, f.retire_source as RETIRE_SOURCE, sum(e.current_acceptance_value) as ACCEPTANCE_VALUE, g.payment_date as PAYMENT_DATE, sum(g.accepted_ammount) as PAYMENT_VALUE
		from com_pi_master_details a 
		join com_btb_lc_pi c on a.id=c.pi_id and c.status_active=1 and c.is_deleted=0 
		join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id and d.status_active=1 and d.is_deleted=0
		join com_import_invoice_dtls e on d.id=e.btb_lc_id and e.status_active=1 and e.is_deleted=0
		join com_import_invoice_mst f on e.import_invoice_id=f.id and f.status_active=1 and f.is_deleted=0
		join com_import_payment g on f.id=g.invoice_id and g.status_active=1 and g.is_deleted=0
		where a.status_active=1 and a.is_deleted=0 $company_cond $pi_number_cond $lc_number_cond $issue_banking_cond $item_category_cond $supplier_cond $date_cond
		group by a.id, a.pi_number, a.supplier_id, a.pi_date, a.net_total_amount, a.hs_code, a.item_category_id, d.id, d.lc_number, d.lc_date, d.lc_value, d.lc_type_id, d.inco_term_id, d.payterm_id, d.lc_expiry_date, d.issuing_bank_id, d.tolerance, d.ud_no, d.ud_date, d.cover_note_no, f.id, f.invoice_no, f.invoice_date, f.company_acc_date, f.bank_acc_date, f.maturity_date, f.edf_paid_date, f.retire_source, g.payment_date
		order by a.pi_date desc";
	}
	//echo $sql;die;

	$sql_res=sql_select($sql);
	$payment_values_arr=array();
	foreach ($sql_res as $val)
	{
		if ($val['BTB_ID'] !='') {
			$btb_ids.=$val['BTB_ID'].',';
		}

		if ($val['RETIRE_SOURCE']==30 || $val['RETIRE_SOURCE']==31) {
			if ($val['EDF_PAID_DATE']!='' && $val['EDF_PAID_DATE']!='0000-00-00') {
				$payment_values_arr[$val['INVOICE_ID']]+=$val['ACCEPTANCE_VALUE'];
			}
		}
		else
		{
			$payment_values_arr[$val['INVOICE_ID']]=$payment_data_arr[$val['INVOICE_ID']];
		}
	}
	//echo '<pre>';print_r($payment_value_arr);

	// LC SC Last Shipment Date and Remarks Part
	if ($btb_ids != '')
    {
        $btb_ids = array_flip(array_flip(explode(',', rtrim($btb_ids,','))));
        $btb_id_cond = '';

        if($db_type==2 && count($btb_ids)>999)
        {
            $btb_id_cond = ' and (';
            $btbIdArr = array_chunk($btb_ids,999);
            foreach($btbIdArr as $ids)
            {
                $ids = implode(',',$ids);
                $btb_id_cond .= " b.import_mst_id in($ids) or ";
            }
            $btb_id_cond = rtrim($pi_id_cond,'or ');
            $btb_id_cond .= ')';
        }
        else
        {
            $btb_ids = implode(',', $btb_ids);
            $btb_id_cond=" and b.import_mst_id in ($btb_ids)";
        }

       
        $sql_lc_sc="SELECT b.import_mst_id as BTB_ID, a.export_lc_no as LC_SC_NO, a.last_shipment_date as LC_SC_SHIP_DATE, a.remarks as LC_SC_REMARKS
		from com_export_lc a, com_btb_export_lc_attachment b 
		where a.id=b.lc_sc_id and b.is_lc_sc=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $btb_id_cond
		union all
		SELECT b.import_mst_id as BTB_ID, a.contract_no as LC_SC_NO, a.last_shipment_date as LC_SC_SHIP_DATE, a.remarks as LC_SC_REMARKS
		from com_sales_contract a, com_btb_export_lc_attachment b 
		where a.id=b.lc_sc_id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $btb_id_cond";
		$sql_lc_sc_res=sql_select($sql_lc_sc);
		$ls_sc_arr=array();
		foreach ($sql_lc_sc_res as $val) 
		{			
			$ls_sc_arr[$val['BTB_ID']]['LC_SC_NO']=$val['LC_SC_NO'];
			$ls_sc_arr[$val['BTB_ID']]['LC_SC_SHIP_DATE']=$val['LC_SC_SHIP_DATE'];
			$ls_sc_arr[$val['BTB_ID']]['LC_SC_REMARKS']=$val['LC_SC_REMARKS'];
		}
		unset($sql_lc_sc_res);
    }

	$width=2670;
	ob_start();
	?>
	<div width="<?= $width; ?>">
		<table width="<?= $width; ?>" cellspacing="0" cellpadding="0">
			<tr>
				<td colspan="31" align="center" width="<?= $width; ?>"><p style="font-weight:bold; font-size:20px">Import CI Statement</p>
				</td>
			</tr>				
		</table>
		<table class="rpt_table" border="1" rules="all" width="<?= $width; ?>" cellpadding="0" cellspacing="0" style="margin-left: 2px;">
			<thead>
				<tr>
					<th colspan="7">PI Information</th>
					<th colspan="15">LC Information</th>
					<th colspan="7">Acceptance Status</th>
					<th colspan="2">Payment Status</th>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="120">Supplier Name</th>
	                <th width="120">PI No</th>
					<th width="70">Date</th>
	                <th width="80">Value</th>
	                <th width="100">Item Name</th>
	                <th width="100">HS Code</th>

	                <th width="120">M L/C</th>
	                <th width="110">BTB L/C No</th>
	                <th width="70">Date</th>
	                <th width="80">Value</th>
	                <th width="80">L/C Type</th>
	                <th width="50">Incoterm</th>
	                <th width="80">Payment Term</th>
	                <th width="70">Last Shipment Date</th>
	                <th width="70">Expire Date</th>
	                <th width="120">Bank Name</th>
	                <th width="80">Tolerance</th>
	                <th width="150" title="LC Remarks">Remarks</th>
	                <th width="80">UD No</th>
	                <th width="70">UD Date</th>
	                <th width="80">Ins Cov No</th>

	                <th width="120">Invoice No</th>
	                <th width="70">Invoice Date</th>
	                <th width="70">Company Acc Date</th>
	                <th width="70">Bank Acc Date</th>
	                <th width="80">Acceptance Value</th>
	                <th width="70">Maturity Date</th>
	                <th width="100">Retirement Sources</th>

	                <th width="80">Payment Value</th>
	                <th width="70">Date</th>
				</tr>
			</thead>
		</table>

		<div style="width:<?= $width+20; ?>px; overflow-y:scroll; max-height:350px" id="scroll_body">			
		    <table class="rpt_table" border="1" rules="all" width="<?= $width; ?>" cellpadding="0" cellspacing="0" id="table_body">
		        <tbody>
		        	<?
		        	$i=1;
		        	$pi_id_arr=array();
		        	$tot_pi_value=$tot_lc_value=0;
		        	$tot_acceptance_value=$tot_payment_value=0;
		        	$tot_payment_values=0;
		        	foreach ($sql_res as $row)
		        	{
		        		if ($i%2==0) $bgcolor="#E9F3FF";
						else $bgcolor="#FFFFFF";
		        		$tolerance=($row['TOLERANCE']*$row['LC_VALUE'])/100;
		        		$payment_values=$payment_values_arr[$row['INVOICE_ID']]
		        		?>
			        	<tr bgcolor="<?= $bgcolor; ?>"  onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i;?>" style="text-decoration:none; cursor:pointer">
			        		<td width="30" align="center"><p><?= $i; ?></p></td>
			        		<?
			        		if ($pi_id_arr[$row['PI_ID']]=='')
			        		{			        			
			        			?>			        	
								<td width="120"><p><?= $supplier_arr[$row['SUPPLIER_ID']]; ?></p></td>
				                <td width="120"><p><?= $row['PI_NUMBER']; ?></p></td>
								<td width="70" align="center"><p><?= change_date_format($row['PI_DATE']); ?></p></td>
				                <td width="80" align="right"><p><?= number_format($row['PI_VALUE'],2); ?></p></td>
				                <td width="100"><p>&nbsp;<?= $item_category[$row['ITEM_CATEGORY_ID']]; ?></p></td>
				                <td width="100"><p>&nbsp;<?= $row['HS_CODE']; ?></p></td>

				                <td width="120"><?= $ls_sc_arr[$row['BTB_ID']]['LC_SC_NO']; ?></td>
				                <td width="110"><p><?= $row['LC_NUMBER']; ?></p></td>
				                <td width="70" align="center"><p><?= change_date_format($row['LC_DATE']); ?></p></td>
				                <td width="80" align="right"><p><?= number_format($row['LC_VALUE'],2); ?></p></td>
				                <td width="80"><p>&nbsp;<?= $lc_type[$row['LC_TYPE_ID']]; ?></p></td>
				                <td width="50"><p><?= $incoterm[$row['INCO_TERM_ID']]; ?></p></td>
				                <td width="80"><p><?= $pay_term[$row['PAYTERM_ID']]; ?></p></td>
				                <td width="70" align="center"><p><?= change_date_format($ls_sc_arr[$row['BTB_ID']]['LC_SC_SHIP_DATE']); ?></p></td>
				                <td width="70" align="center"><p><?= change_date_format($row['LC_EXPIRY_DATE']); ?></p></td>
				                <td width="120"><p><?= $issueBank_arr[$row['ISSUING_BANK_ID']]; ?></p></td>
				                <td width="80" align="right"><p><?= number_format($tolerance,2); ?></p></td>
				                <td width="150" title="LC Remarks"><p>&nbsp;<?= $ls_sc_arr[$row['BTB_ID']]['LC_SC_REMARKS']; ?></p></td>
				                <td width="80"><p><?= $row['UD_NO']; ?></p></td>
				                <td width="70" align="center"><p><?= change_date_format($row['UD_DATE']); ?></p></td>
				                <td width="80"><p><?= $row['INS_COV_NO']; ?></p></td>
			                	<?			                	
			        			$tot_pi_value+=$row['PI_VALUE'];
			        			$tot_lc_value+=$row['LC_VALUE'];
			        			$pi_id_arr[$row['PI_ID']]=$row['PI_ID'];
			            	}
			            	else 
			            	{
				            	?>			            	
								<td width="120">&nbsp;</td>
				                <td width="120">&nbsp;</td>
								<td width="70">&nbsp;</td>
				                <td width="80">&nbsp;</td>
				                <td width="100">&nbsp;</td>
				                <td width="100">&nbsp;</td>

				                <td width="120">&nbsp;</td>
				                <td width="110">&nbsp;</td>
				                <td width="70">&nbsp;</td>
				                <td width="80">&nbsp;</td>
				                <td width="80">&nbsp;</td>
				                <td width="50">&nbsp;</td>
				                <td width="80">&nbsp;</td>
				                <td width="70">&nbsp;</td>
				                <td width="70">&nbsp;</td>
				                <td width="120">&nbsp;</td>
				                <td width="80">&nbsp;</td>
				                <td width="150">&nbsp;</td>
				                <td width="80">&nbsp;</td>
				                <td width="70">&nbsp;</td>
				                <td width="80">&nbsp;</td>
				            	<?
			            	}	
			                ?>
			                <td width="120"><p><?= $row['INVOICE_NO']; ?></p></td>
			                <td width="70" align="center"><p><?= change_date_format($row['INVOICE_DATE']); ?></p></td>
			                <td width="70" align="center"><p><?= change_date_format($row['COMPANY_ACC_DATE']); ?></p></td>
			                <td width="70" align="center"><p><?= change_date_format($row['BANK_ACC_DATE']); ?></p></td>
			                <td width="80" align="right"><p><?= number_format($row['ACCEPTANCE_VALUE'],2); ?></p></td>
			                <td width="70" align="center"><p><?= change_date_format($row['MATURITY_DATE']); ?></p></td>
			                <td width="100"><p><?= $commercial_head[$row['RETIRE_SOURCE']]; ?></p></td>

			                <td width="80" align="right"><p><?= number_format($payment_values,2); ?></p></td>
			                <td width="70" align="center"><p><?= change_date_format($row['PAYMENT_DATE']); ?></p></td>
			        	</tr>
			        	<?
			        	$i++;
			        	$tot_acceptance_value+=$row['ACCEPTANCE_VALUE'];
			        	$tot_payment_value+=$row['PAYMENT_VALUE'];
			        	$tot_payment_values+=$payment_values;
			        }
			        ?>
			        <tr class="tbl_bottom">
		                <td colspan="4" align="right">Total:&nbsp;</td>
		                <td width="80" align="right"><?= number_format($tot_pi_value,2); ?></td>
		                <td width="100">&nbsp;</td>
		                <td width="100">&nbsp;</td>

		                <td width="120">&nbsp;</td>
		                <td width="110">&nbsp;</td>
		                <td width="70">&nbsp;</td>
		                <td width="80" align="right"><?= number_format($tot_lc_value,2); ?></td>
		                <td width="80">&nbsp;</td>
		                <td width="50">&nbsp;</td>
		                <td width="80">&nbsp;</td>
		                <td width="70">&nbsp;</td>
		                <td width="70">&nbsp;</td>
		                <td width="120">&nbsp;</td>
		                <td width="80">&nbsp;</td>
		                <td width="150">&nbsp;</td>
		                <td width="80">&nbsp;</td>
		                <td width="70">&nbsp;</td>
		                <td width="80">&nbsp;</td>
                
		                <td width="120">&nbsp;</td>
		                <td width="70">&nbsp;</td>
		                <td width="70">&nbsp;</td>
		                <td width="70">&nbsp;</td>
		                <td width="80" align="right"><?= number_format($tot_acceptance_value,2); ?></td>
		                <td width="70">&nbsp;</td>
		                <td width="100">&nbsp;</td>

		                <td width="80" align="right"><?= number_format($tot_payment_values,2); ?></td>
		                <td width="70">&nbsp;</td>
		            </tr>
		        </tbody>
		    </table>
		</div>
	</div>
	
	<?
	foreach (glob("$user_id*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename";
	exit();
}

if($action==='supplier_list_popup')
{
	echo load_html_head_contents("Supplier List", "../../../", 1, 1, '','','');
	extract($_REQUEST);
	?>
	<script>

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		function js_set_value( str )
		{
			var id = $('#txt_individual_id' + str).val()
			var name= $('#txt_individual_name' + str).val()
			$('#hidden_supplier_id').val(id);
			$('#hidden_supplier_name').val(name);
			parent.emailwindow.hide();
		}
    </script>

	</head>
	<?
	$catWiseParty= array(
		0=>"0", 1=>"1,2", 2=>"1,9", 3=>"1,9", 13=>"1,9", 14=>"1,9", 4=>"1,4,5", 5=>"1,3",
		6=>"1,3",
		7=>"1,3",
		9=>"1,6",
		10=>"1,6",
		11=>"1,8",
		12=>"1,20,21,22,23,24,30,31,32,35,36,37,38,39",
		24=>"1,20,21,22,23,24,30,31,32,35,36,37,38,39",
		25=>"1,20,21,22,23,24,30,31,32,35,36,37,38,39",
		31=>"1,26",
		32=>"1,92"
	);

	if ($catWiseParty[$category] != '') 
		$party_type = $catWiseParty[$category];
	else $party_type = "1,7";

	$result = sql_select("SELECT a.id as ID, a.supplier_name as SUPPLIER_NAME from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id  and c.tag_company in('$company') and b.party_type in ($party_type) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name");
	?>
	<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
	    	<input type="hidden" name="hidden_supplier_id" id="hidden_supplier_id">
	    	<input type="hidden" name="hidden_supplier_name" id="hidden_supplier_name">
	        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
	                <thead>
	                    <th width="50">SL</th>
	                    <th>Supplier Name</th>
	                </thead>
	            </table>
	            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
	                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
	                <?
	                    $i=1;
	                    foreach($result as $row)
	                    {
	                        if ($i%2==0) $bgcolor='#E9F3FF'; 
	                        else $bgcolor='#FFFFFF';
	                        ?>
	                        <tr bgcolor="<?= $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?= $i;?>" onClick="js_set_value(<?= $i; ?>)">
                                <td width="50" align="center"><?php echo "$i"; ?>
                                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?= $i ?>" value="<?= $row['ID']; ?>"/>
                                    <input type="hidden" name="txt_individual_name" id="txt_individual_name<?= $i ?>" value="<?= $row['SUPPLIER_NAME']; ?>"/>
                                </td>
                                <td><p><?= $row['SUPPLIER_NAME']; ?></p></td>
	                        </tr>
	                        <?
	                        $i++;
	                    }
	                ?>
	                </table>
	            </div>
	        </form>
	    </fieldset>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

