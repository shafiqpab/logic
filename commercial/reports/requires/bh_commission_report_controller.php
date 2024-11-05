<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');


$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_buyer")
{
    echo create_drop_down("cbo_buyer_name", 140, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "--- Select Buyer ---", $selected, "");
	exit();
}

if($action=="report_generate")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $cbo_company_name   = str_replace("'","",$cbo_company_name);
    $cbo_bank_name      = str_replace("'","",$cbo_bank_name);   
    $cbo_buyer_name     = str_replace("'","",$cbo_buyer_name);     
    $txt_job_no         = str_replace("'","",$txt_job_no);       
    $txt_style_no       = str_replace("'","",$txt_style_no);       
    $cbo_year_selection = str_replace("'","",$cbo_year_selection);    
    $txt_date_from      = str_replace("'","",$txt_date_from);    
    $txt_date_to        = str_replace("'","",$txt_date_to); 

    $lib_company_arr=return_library_array( "select id, company_name from lib_company is_deleted=0",'id','company_name');
    $buyer_arr = return_library_array("select id,buyer_name from lib_buyer where is_deleted=0","id","buyer_name");
    $bank_arr = return_library_array("select id,bank_name from lib_bank where is_deleted=0","id","bank_name");

    if ($cbo_company_name!=0) {$company_id=" and company_id=$cbo_company_name";} else { echo "Please Select Company First."; die;}
    if ($cbo_bank_name!=0) {$bank_id=" and bank_id=$cbo_bank_name";} else { $bank_id="";}
    if ($cbo_buyer_name!=0) {$buyer_id=" and buyer_id=$cbo_buyer_name";} else { $buyer_id="";}
    if ($txt_job_no!='') {$job_no=" and h.job_no_mst='$txt_job_no'";} else { $job_no="";}
    if ($txt_style_no!='') {$style_no=" and i.style_ref_no='$txt_style_no'";} else { $style_no="";}
    if ($txt_date_from != '' && $txt_date_to != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and commision_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and commision_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
		}	
    } 
    else 
    {
        if($db_type==0){$date_cond=" and year(commision_date)=".$cbo_year_selection."";}
        else{$date_cond=" and to_char(commision_date,'YYYY')=".$cbo_year_selection."";}
	}
    $mst_sql="SELECT id as ID, sys_number as SYS_NUMBER, company_id as COMPANY_ID, bank_id as BANK_ID, commision_date as COMMISION_DATE, submission_invoice_id as SUBMISSION_INVOICE_ID, realization_id as REALIZATION_ID, bill_no as BILL_NO, remarks as REMARKS, buying_house_info as BUYING_HOUSE_INFO
    from bh_commission 	
    where status_active=1 and is_deleted=0 $company_id $bank_id $date_cond";
    // echo $mst_sql;
    $mst_sql_result=sql_select($mst_sql);
    foreach($mst_sql_result as $row)
    {
        $realization_id.=$row['REALIZATION_ID'].',';
    }
    $realization_id=chop($realization_id,',');
    
    $details_sql="SELECT a.id as REALIZATION_ID, a.buyer_id as BUYER_ID,a.received_date as REALIZATION_DATE, sum(b.document_currency) as DOCUMENT_CURRENCY, c.bank_ref_no as BANK_REF_NO, d.id as EXPORT_DOC_DTLS_ID, d.net_invo_value as NET_INVO_VALUE, d.is_lc as IS_LC, e.export_lc_no as SC_LC_NO, e.lc_date as SC_LC_DATE, f.invoice_no as INVOICE_NO,f.invoice_date as INVOICE_DATE,g.po_breakdown_id as PO_BREAKDOWN_ID,g.current_invoice_qnty as CURRENT_INVOICE_QNTY, g.current_invoice_value as CURRENT_INVOICE_VALUE, h.po_number as PO_NUMBER, h.job_no_mst as JOB_NO_MST, i.style_ref_no as STYLE_REF_NO, i.order_uom as ORDER_UOM, i.total_set_qnty as TOTAL_SET_QNTY, j.costing_per as COSTING_PER, k.commission_amount as COMMISSION_AMOUNT
	from com_export_proceed_realization a,com_export_proceed_rlzn_dtls b, com_export_doc_submission_mst c, com_export_doc_submission_invo d, com_export_lc e ,com_export_invoice_ship_mst f,com_export_invoice_ship_dtls g,wo_po_break_down h,wo_po_details_master i,wo_pre_cost_mst j
	left join wo_pre_cost_commiss_cost_dtls k on j.job_no=k.job_no and k.particulars_id=2 and k.status_active=1 and k.is_deleted=0
	where a.id in ($realization_id) and b.mst_id=a.id and a.invoice_bill_id=c.id and d.lc_sc_id=e.id and d.invoice_id=f.id and f.id=g.mst_id and g.po_breakdown_id=h.id and h.job_no_mst=i.job_no and i.id=j.job_id and d.is_lc=1 and b.type=1 and c.id=d.doc_submission_mst_id and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0 and i.status_active=1 and i.is_deleted=0 and j.status_active=1 and j.is_deleted=0 $job_no $style_no
	group by a.id, a.buyer_id ,a.received_date , c.bank_ref_no, d.id, d.net_invo_value, d.is_lc, e.export_lc_no, e.lc_date, f.invoice_no ,f.invoice_date ,g.po_breakdown_id ,g.current_invoice_qnty, g.current_invoice_value, h.po_number, h.job_no_mst, i.style_ref_no, i.order_uom , i.total_set_qnty , j.costing_per, k.commission_amount
	union all
	SELECT a.id as REALIZATION_ID, a.buyer_id as BUYER_ID,a.received_date as REALIZATION_DATE, sum(b.document_currency) as DOCUMENT_CURRENCY, c.bank_ref_no as BANK_REF_NO, d.id as EXPORT_DOC_DTLS_ID, d.net_invo_value as NET_INVO_VALUE, d.is_lc as IS_LC, e.contract_no as SC_LC_NO, e.contract_date as SC_LC_DATE, f.invoice_no as INVOICE_NO,f.invoice_date as INVOICE_DATE, g.po_breakdown_id as PO_BREAKDOWN_ID,g.current_invoice_qnty as CURRENT_INVOICE_QNTY, g.current_invoice_value as CURRENT_INVOICE_VALUE, h.po_number as PO_NUMBER, h.job_no_mst as JOB_NO_MST, i.style_ref_no as STYLE_REF_NO, i.order_uom as ORDER_UOM, i.total_set_qnty as TOTAL_SET_QNTY, j.costing_per as COSTING_PER, k.commission_amount as COMMISSION_AMOUNT
	from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_mst c, com_export_doc_submission_invo d, com_sales_contract e ,com_export_invoice_ship_mst f,com_export_invoice_ship_dtls g, wo_po_break_down h,wo_po_details_master i,wo_pre_cost_mst j
	left join wo_pre_cost_commiss_cost_dtls k on j.job_no=k.job_no and k.particulars_id=2 and k.status_active=1 and k.is_deleted=0
	where a.id in ($realization_id) and b.mst_id=a.id and a.invoice_bill_id=c.id and d.lc_sc_id=e.id and d.invoice_id=f.id and f.id=g.mst_id and g.po_breakdown_id=h.id and h.job_no_mst=i.job_no and i.id=j.job_id and d.is_lc=2 and b.type=1 and c.id=d.doc_submission_mst_id and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0 and i.status_active=1 and i.is_deleted=0 and j.status_active=1 and j.is_deleted=0 $job_no $style_no
	group by a.id, a.buyer_id ,a.received_date , c.bank_ref_no, d.id, d.net_invo_value, d.is_lc, e.contract_no, e.contract_date, f.invoice_no ,f.invoice_date ,g.po_breakdown_id, g.current_invoice_qnty, g.current_invoice_value, h.po_number, h.job_no_mst, i.style_ref_no, i.order_uom , i.total_set_qnty , j.costing_per, k.commission_amount";
    // echo $details_sql;die;
    $details_sql_result=sql_select($details_sql);
    unset($details_sql);
    $data_info=array();
    foreach($details_sql_result as $row)
    {
        $data_info[$row['REALIZATION_ID']][$row['EXPORT_DOC_DTLS_ID']][$row['PO_BREAKDOWN_ID']]['BUYER_ID']=$row['BUYER_ID'];
        $data_info[$row['REALIZATION_ID']][$row['EXPORT_DOC_DTLS_ID']][$row['PO_BREAKDOWN_ID']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
        $data_info[$row['REALIZATION_ID']][$row['EXPORT_DOC_DTLS_ID']][$row['PO_BREAKDOWN_ID']]['PO_NUMBER']=$row['PO_NUMBER'];
        $data_info[$row['REALIZATION_ID']][$row['EXPORT_DOC_DTLS_ID']][$row['PO_BREAKDOWN_ID']]['JOB_NO_MST']=$row['JOB_NO_MST'];
        $data_info[$row['REALIZATION_ID']][$row['EXPORT_DOC_DTLS_ID']][$row['PO_BREAKDOWN_ID']]['BANK_REF_NO']=$row['BANK_REF_NO'];
        $data_info[$row['REALIZATION_ID']][$row['EXPORT_DOC_DTLS_ID']][$row['PO_BREAKDOWN_ID']]['SC_LC_NO']=$row['SC_LC_NO'];
        $data_info[$row['REALIZATION_ID']][$row['EXPORT_DOC_DTLS_ID']][$row['PO_BREAKDOWN_ID']]['INVOICE_NO']=$row['INVOICE_NO'];
        $data_info[$row['REALIZATION_ID']][$row['EXPORT_DOC_DTLS_ID']][$row['PO_BREAKDOWN_ID']]['INVOICE_DATE']=change_date_format($row['INVOICE_DATE']);
        $data_info[$row['REALIZATION_ID']][$row['EXPORT_DOC_DTLS_ID']][$row['PO_BREAKDOWN_ID']]['INVOICE_QNTY']=$row['CURRENT_INVOICE_QNTY'];
        // $data_info[$row['REALIZATION_ID']][$row['EXPORT_DOC_DTLS_ID']][$row['PO_BREAKDOWN_ID']]['INVOICE_VALUE']=$row['NET_INVO_VALUE'];
        $data_info[$row['REALIZATION_ID']][$row['EXPORT_DOC_DTLS_ID']][$row['PO_BREAKDOWN_ID']]['INVOICE_VALUE']=$row['CURRENT_INVOICE_VALUE'];
        $data_info[$row['REALIZATION_ID']][$row['EXPORT_DOC_DTLS_ID']][$row['PO_BREAKDOWN_ID']]['REALIZATION_DATE']=change_date_format($row['REALIZATION_DATE']);

        $commission_rate=0;
        $commission_am=$row['COMMISSION_AMOUNT']*1;
		$set_qnty=$row['TOTAL_SET_QNTY']*1;
		$invoice_qnty=$row['CURRENT_INVOICE_QNTY']*1;
		if($row['COSTING_PER']==1){
			$commission_rate=($commission_am/12)/$set_qnty;
			$commission_amount=$commission_rate*$invoice_qnty;
		}
		if($row['COSTING_PER']==2){
			$commission_rate=($commission_am/1)/$set_qnty;
			$commission_amount=$commission_rate*$invoice_qnty;
		}
		if($row['COSTING_PER']==3){
			$commission_rate=($commission_am/24)/$set_qnty;
			$commission_amount=$commission_rate*$invoice_qnty;
		}
		if($row['COSTING_PER']==4){
			$commission_rate=($commission_am/36)/$set_qnty;
			$commission_amount=$commission_rate*$invoice_qnty;
		}
		if($row['COSTING_PER']==5){
			$commission_rate=($commission_am/48)/$set_qnty;
			$commission_amount=$commission_rate*$invoice_qnty;
		}
        $data_info[$row['REALIZATION_ID']][$row['EXPORT_DOC_DTLS_ID']][$row['PO_BREAKDOWN_ID']]['COMMISSION_RATE']=number_format($commission_rate,4);
        $data_info[$row['REALIZATION_ID']][$row['EXPORT_DOC_DTLS_ID']][$row['PO_BREAKDOWN_ID']]['COMMISSION_AMOUNT']=number_format($commission_amount,4);
    }
    unset($details_sql_result);
    ob_start();
    ?>
        <div style="width:1440px;">
            <table width="1400" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="30">Sl No</th>
                        <th width="80">Commision Date</th>
                        <th width="100">Buyer</th>
                        <th width="100">Style No</th>
                        <th width="100">Order No</th>
                        <th width="100">Job No</th>
                        <th width="100">Bank Ref/ Bill No</th>
                        <th width="100">LC/SC No</th>
                        <th width="100">Invoice No</th>
                        <th width="80">Invoice Date</th>
                        <th width="80">Inoviec Qty PCs</th>
                        <th width="80">Inv. Value</th>
                        <th width="80">Realize Date</th>
                        <th width="80">Commission Rate</th>
                        <th width="80">Commission Amount</th>
                        <th >Bank Name</th>
                    </tr>
                </thead>
                <tbody id="table_body" style="max-height: 50px; overflow-y:scroll;width:1400px">
                    <?
                        $i=1;
                        foreach($mst_sql_result as $row)
                        {
                            $realization_arr=explode(',',$row['REALIZATION_ID']);
                            foreach($realization_arr as $rows)
                            {
                                foreach($data_info[$rows] as $key)
                                {  
                                    foreach($key as $val)
                                    {
                                        if ($i%2==0) $bgcolor="#E9F3FF";
                                        else $bgcolor="#FFFFFF";
                                        ?>
                                            <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')"  id="tr_<? echo $i;?>">
                                                <td align="center"><?=$i;?></td>
                                                <td align="center"><? echo change_date_format($row['COMMISION_DATE']);?></td>
                                                <td><? echo $buyer_arr[$val['BUYER_ID']];?></td>
                                                <td><? echo $val['STYLE_REF_NO'];?></td>
                                                <td><? echo $val['PO_NUMBER'];?></td>
                                                <td><? echo $val['JOB_NO_MST'];?></td>
                                                <td><? echo $val['BANK_REF_NO'];?></td>
                                                <td><? echo $val['SC_LC_NO'];?></td>
                                                <td><? echo $val['INVOICE_NO'];?></td>
                                                <td align="center"><? echo $val['INVOICE_DATE'];?></td>
                                                <td align="right"><? echo $val['INVOICE_QNTY'];?></td>
                                                <td align="right"><? echo $val['INVOICE_VALUE'];?></td>
                                                <td align="center"><? echo $val['REALIZATION_DATE'];?></td>
                                                <td align="right"><? echo number_format($val['COMMISSION_RATE'],4);?></td>
                                                <td align="right"><? echo number_format($val['COMMISSION_AMOUNT'],4);
                                                $total_commission_amount+=$val['COMMISSION_AMOUNT'];?></td>
                                                <td><? echo $bank_arr[$row['BANK_ID']];?></td>
                                            </tr>
                                        <?
                                        $i++;
                                    } 
                                }
                            }
                        }
                    ?>
                </tbody>
                <tfoot>
                    <th colspan="13"></th>
                    <th><strong>Total</strong></th>
                    <th><strong><?echo number_format($total_commission_amount,4);?></strong></th>
                    <th></th>
                </tfoot>
            </table>
        </div>
    <?
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    $name=time();
    $filename=$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc,ob_get_contents());
    echo "$html****$filename";
    exit();	
}