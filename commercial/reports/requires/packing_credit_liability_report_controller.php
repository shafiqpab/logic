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
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	exit();
}

if ($action=="load_drop_down_applicant")
{
	$sql = "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (22,23)) order by buyer_name";
 	echo create_drop_down( "cbo_applicant", 110, $sql,"id,buyer_name", 1, "-- Select --", 0, "" );
	exit();

}


//Company Details
$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$bank_arr=return_library_array("select id,bank_name from   lib_bank","id","bank_name");

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_year=str_replace("'","",$cbo_file_year);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$txt_date_from_loan=str_replace("'","",$txt_date_from_loan);
	$txt_date_to_loan = str_replace("'","",$txt_date_to_loan);

	if($txt_year!=0)
	{
		$year_lc=" and e.lc_year='$txt_year'";
		$year_sc=" and e.sc_year='$txt_year'";
	}
	else
	{
		$year_lc="";
		$year_sc="";
	}

	?>
    <div style="width:1250px;" align="center">
        <fieldset>
        <table width="1250" cellpadding="0" cellspacing="0" id="caption">
            <tr>
               <td align="center" width="100%" colspan="20" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
            </tr>
            <tr>
               <td align="center" width="100%" colspan="20" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
            </tr>
        </table>
        <table width="1250" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="" align="left">
            <thead>
                <tr>
                    <th width="30">Sl</th>
                    <th width="100">Lien Bank</th>
                    <th width="100">Buyer</th>
                    <th width="100">LC/SC No.</th>
                    <th width="60">File No</th>
                    <th width="100">LC/SC Value</th>
                    <th width="100">Loan Number</th>
                    <th width="70">Loan Date</th>
                    <th width="100">Loan Type</th>
                    <th width="100">Loan Amount(TK)</th>
                    <th width="100">Loan Amount	</th>
                    <th width="100">Addj. Amount(TK)</th>
                    <th width="100">Balance(TK)</th>
                    <th width="">Expiry Date</th>
                </tr>
            </thead>
        </table>
    
        <div style="width:1270px; overflow-y: scroll; overflow-x:hidden; max-height:350px;font-size:12px;" id="scroll_body">
        <table width="1250" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <tbody>
            <?
            $cbo_company_name=str_replace("'","",$cbo_company_name);
            if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";
    
            $cbo_lien_bank=str_replace("'","",$cbo_lien_bank);
            if($cbo_lien_bank == 0) $cbo_lien_bank="%%"; else $cbo_lien_bank = $cbo_lien_bank;
    
            $loan_ref=str_replace("'","",$loan_ref);
    
            if($loan_ref == 0) $loan_ref=""; else $loan_ref = $loan_ref;
            if($loan_ref != ""){
                $loan_ref_cond = " and c.loan_number like '%$loan_ref%'";
            }else{
                $loan_ref_cond="";
            }
            //echo $loan_ref;
    
            $cbo_file_year=str_replace("'","",$cbo_file_year);
            if($cbo_file_year == 0) $cbo_file_year="%%"; else $cbo_file_year = $cbo_file_year;
    
            $txt_file_no=str_replace("'","",trim($txt_file_no));
            if($txt_file_no == "") $txt_file_no_cond=""; else $txt_file_no_cond = " and e.internal_file_no like '%$txt_file_no%'";
            $txt_lc_sc_no=str_replace("'","",trim($txt_lc_sc_no));
            if($txt_lc_sc_no == "")
            {
                $txt_lc_no_cond="";
                $txt_sc_no_cond="";
            }
            else
            {
                $txt_lc_no_cond = " and e.export_lc_no like '%$txt_lc_sc_no%'";
                $txt_sc_no_cond = " and e.contract_no like '%$txt_lc_sc_no%'";
            }
    		$expiry_date_serch_cond = "";
            if($txt_date_from != "" && $txt_date_to != ""){
                
				$expiry_date_serch_cond = " and c.loan_expire_date between '$txt_date_from' and '$txt_date_to'";
            }
			if($txt_date_from_loan != "" && $txt_date_to_loan != ""){
                
				$expiry_date_serch_cond .= " and b.loan_date between '$txt_date_from_loan' and '$txt_date_to_loan'";
            }
    
            $loan_sum_array = array();
            $rlz_sql="select a.is_lc, a.lc_sc_id, c.document_currency, c.domestic_currency, c.id, c.ac_loan_no
            from com_export_doc_submission_invo a, com_export_proceed_realization b, com_export_proceed_rlzn_dtls c
            where a.doc_submission_mst_id=b.invoice_bill_id and b.id=c.mst_id and b.is_invoice_bill=1 and c.account_head=20 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
    
            $rlz_result=sql_select($rlz_sql);
            $rlz_data=array();
            foreach ($rlz_result as $key => $value) {
                if($rlz_check[$value[csf("id")]]=="")
                {
                    $rlz_check[$value[csf("id")]]=$value[csf("id")];
                    $rlz_data[$value[csf("lc_sc_id")]][$value[csf("is_lc")]][$value[csf("ac_loan_no")]]["document_currency"]+=$value[csf("domestic_currency")];
                    $rlz_data[$value[csf("lc_sc_id")]][$value[csf("is_lc")]]["domestic_currency"]+=$value[csf("domestic_currency")];
                }
            }
            // echo "<pre>";
            // print_r($rlz_data);
            $sql="SELECT b.loan_date, c.loan_number, c.loan_expire_date, c.id, c.loan_amount,c.conversion_rate, d.lc_sc_id, d.export_type, e.internal_file_no, e.bank_file_no, e.lc_year as lc_sc_year, e.export_lc_no as lc_sc, e.lc_value as lc_sc_value, e.lien_bank, e.buyer_name, 1 as type, c.loan_type
            from com_pre_export_finance_mst b, com_pre_export_finance_dtls c, com_pre_export_lc_wise_dtls d, com_export_lc e
            where b.id=c.mst_id and c.id=d.pre_export_dtls_id and d.lc_sc_id=e.id and d.export_type=1 and b.status_active=1 and b.is_deleted=0 $loan_ref_cond and  c.status_active=1 and c.is_deleted=0 and e.beneficiary_name like '$cbo_company_name' and e.lien_bank like '$cbo_lien_bank' and  e.status_active=1 and e.is_deleted=0 $year_lc $txt_file_no_cond $txt_lc_no_cond $expiry_date_serch_cond    
            UNION ALL    
            select b.loan_date, c.loan_number, c.loan_expire_date, c.id, c.loan_amount, c.conversion_rate,d.lc_sc_id, d.export_type, e.internal_file_no, e.bank_file_no, e.sc_year as lc_sc_year, e.contract_no as lc_sc, e.contract_value as lc_sc_value, e.lien_bank, e.buyer_name, 2 as type, c.loan_type
            from com_pre_export_finance_mst b, com_pre_export_finance_dtls c, com_pre_export_lc_wise_dtls d, com_sales_contract e
            where b.id=c.mst_id and c.id=d.pre_export_dtls_id and d.lc_sc_id=e.id and d.export_type=2 and b.status_active=1 and b.is_deleted=0 $loan_ref_cond  and c.status_active=1 and c.is_deleted=0 and  e.beneficiary_name like '$cbo_company_name' and e.lien_bank like '$cbo_lien_bank' and e.status_active=1 and e.is_deleted=0  $year_sc $txt_file_no_cond $txt_sc_no_cond  $expiry_date_serch_cond order by lc_sc_id";
    
            //echo $sql; //die();
            $i= 1;
            $k=1;
            $item_group_array=array();
            $year=array();
            $sql_result=sql_select($sql);
    
            $subtotal_ls_sc_value = 0; $subtotal_loan_amount = 0; $subtotal_total_dom_balance = 0; $subtotal_adjustmentAmount = 0; $subtotal_balance = 0; $grand_ls_sc_value = 0; $grand_loan_amount=0; $grand_total_dom_balance = 0; $grand_adjustmentAmount=0; $balance=0;
            $lc_sc_val_arr = array();
            
            foreach($sql_result as $key => $rows)
            {
                if($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30" ><? echo $i;?></td>
                    <td width="100"><p><? echo $bank_arr[$rows[csf('lien_bank')]]; ?></p></td>
                    <td width="100"><p><? echo $buyer_arr[$rows[csf('buyer_name')]]; ?></p></td>
                    <td width="100"><p><? echo $rows[csf('lc_sc')]; ?></p></td>
                    <td width="60"><p><? echo $rows[csf('internal_file_no')];?></p></td>
                    <td width="100" align="right"><p><?
                    $lcscVal = $rows[csf('lc_sc_value')];
                    if(!in_array($lcscVal, $lc_sc_val_arr))
                    {
                        $lcscValue = $rows[csf('lc_sc_value')];
                        echo number_format($lcscValue,2);
                        $lc_sc_val_arr[] = $rows[csf('lc_sc_value')];
                    }
                    ?></p></td>
                    <td width="100"><p><? echo $rows[csf('loan_number')];?></p></td>
                    <td width="70" align="center"><p><? echo change_date_format($rows[csf('loan_date')]); ?></p></td>
                    <td width="100" align="center"><p><? echo $commercial_head[$rows[csf('loan_type')]]; ?></p></td>
                    <td width="100" align="right"><p><? $loanAmount = $rows[csf('loan_amount')]; echo number_format($loanAmount,2); ?></p></td>
                    <td width="100" align="right"><p><? $loanAmountDom = $rows[csf('loan_amount')]/$rows[csf('conversion_rate')]; echo number_format($loanAmountDom,2); ?></p></td>
                    <td width="100" align="right"><p><? $adjustmentAmount = $rlz_data[$rows[csf("lc_sc_id")]][$rows[csf("type")]][$rows[csf("loan_number")]]["document_currency"]; echo number_format($adjustmentAmount,2); ?><p></td>
    
                    <td align="right" width="100"><p><? $balance = $loanAmount - $adjustmentAmount; echo number_format($balance,2); ?></p></td>
    
                    <td width="" align="center"><p><? echo change_date_format($rows[csf('loan_expire_date')]); ?></p></td>
    
                </tr>
                <?
                $subtotal_ls_sc_value += $lcscValue;
                $subtotal_loan_amount += $loanAmount;
                $subtotal_total_dom_balance += $loanAmountDom;
                $subtotal_adjustmentAmount += $adjustmentAmount;
                $subtotal_balance += $balance;
                // SUB TOTAL by lc sc group
                if ($sql_result[$key+1][csf('lc_sc')] != $rows[csf('lc_sc')])
                { 
					?>
                    <tr bgcolor="#d9d9d9">
                        <td colspan="9" align="right"><b>Sub Total</b></td>
                        <td align="right"><p><b><? echo number_format($subtotal_loan_amount,2);?></b></p></td>
                        <td align="right"><p><b><? echo number_format($subtotal_total_dom_balance,2);?></b></p></td>
                        <td align="right"><p><b><? echo number_format($subtotal_adjustmentAmount,2);?></b></p></td>
                        <td align="right"><p><b><? echo number_format($subtotal_balance,2);?></b></p></td>
                        <td >&nbsp;</td>
                    </tr>
                    <?
                    $subtotal_ls_sc_value = 0;
                    $subtotal_loan_amount = 0;
                    $subtotal_total_dom_balance = 0;
                    $subtotal_adjustmentAmount = 0;
                    $subtotal_balance = 0;
                }
                $i++;
                $grand_ls_sc_value += $lcscValue;
                $grand_loan_amount += $loanAmount;
                $grand_total_dom_balance += $loanAmountDom;
                $grand_adjustmentAmount += $adjustmentAmount;
                $grand_balance += $balance;
            }
            ?>
            <tr bgcolor="#CCCCCC">
                <td colspan="5" align="right"><b>Total</b></td>
                <td align="right"><b><? echo number_format($grand_ls_sc_value,2);?></b></td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td align="right"><p><b><? echo number_format($grand_loan_amount,2);?></b></p></td>
                <td align="right"><p><b><? echo number_format($grand_total_dom_balance,2);?></b></p></td>
                <td align="right"><p><b><? echo number_format($grand_adjustmentAmount,2);?></b></p></td>
                <td align="right"><p><b><? echo number_format($grand_balance,2);?></b></p></td>
                <td >&nbsp;</td>
            </tr>
            </tbody>
        </table>
        </div>
        </fieldset>
    </div>
	<?
	exit();
}


disconnect($con);
?>
