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
    $cbo_company_name   = str_replace("'","",$cbo_company_name);
    $cbo_bank_name      = str_replace("'","",$cbo_bank_name);   
    $txt_file_no        = str_replace("'","",$txt_file_no);     
    $txt_file_year      = str_replace("'","",$txt_file_year);       
    $cbo_date_type      = str_replace("'","",$cbo_date_type);           
    $txt_date_from      = str_replace("'","",$txt_date_from);    
    $txt_date_to        = str_replace("'","",$txt_date_to); 

    $lib_company_arr=return_library_array( "select id, company_name from lib_company is_deleted=0",'id','company_name');
    $buyer_arr = return_library_array("select id,buyer_name from lib_buyer where is_deleted=0","id","buyer_name");
    $bank_arr = return_library_array("select id,bank_name from lib_bank where is_deleted=0","id","bank_name");

    if ($cbo_company_name!=0) {$company_id=" and a.company_id=$cbo_company_name";} else { echo "Please Select Company First."; die;}
    if ($cbo_bank_name!=0) {$bank_id=" and a.bank_id=$cbo_bank_name";} else { $bank_id="";}

    if ($txt_file_no!='') {$internal_file=" and a.internal_file_no='$txt_file_no'";} else { $internal_file="";}
    if ($txt_file_year!='') 
    {
        $lc_file_year=" and d.lc_year='$txt_file_year'";
        $sc_file_year=" and d.sc_year='$txt_file_year'";
    }
    else
    { $lc_file_year="";$sc_file_year="";}

    if($cbo_date_type==1)
    {
        if ($txt_date_from != '' && $txt_date_to != '') 
        {
            if ($db_type == 0) {
                $date_cond = "and a.submission_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
            } else if ($db_type == 2) {
                $date_cond = "and a.submission_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
            }	
        } 
        else 
        {
            $date_cond='';
        }
    }
    if($cbo_date_type==2)
    {
        if ($txt_date_from != '' && $txt_date_to != '') 
        {
            if ($db_type == 0) {
                $date_cond = "and b.received_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
            } else if ($db_type == 2) {
                $date_cond = "and b.received_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
            }	
        } 
        else 
        {
            $date_cond='';
        }
    }

    $mst_sql="SELECT a.id as ID, a.bank_id as BANK_ID, a.submission_date as SUBMISSION_DATE, a.is_lc_sc as IS_LC_SC, a.lc_sc_id as LC_SC_ID, a.realization_id as REALIZATION_ID, a.submission_invoice_id as SUBMISSION_INVOICE_ID, a.internal_file_no as INTERNAL_FILE_NO, a.net_realize_value as NET_REALIZE_VALUE, a.amount as INCENTIVE_CLAIM, b.id as RECV_ID
    from  com_export_lc d,cash_incentive_submission a
    left join cash_incentive_received_mst b on a.id=b.cash_incentive_sub_id and b.status_active=1 and b.is_deleted=0 
    where a.internal_file_no=d.internal_file_no and a.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_id $bank_id $internal_file $date_cond $lc_file_year
    group by a.id, a.bank_id, a.submission_date, b.id, a.is_lc_sc, a.lc_sc_id, a.realization_id, a.submission_invoice_id, a.internal_file_no, a.net_realize_value, a.amount
    union all
    SELECT a.id as ID, a.bank_id as BANK_ID, a.submission_date as SUBMISSION_DATE, a.is_lc_sc as IS_LC_SC, a.lc_sc_id as LC_SC_ID, a.realization_id as REALIZATION_ID, a.submission_invoice_id as SUBMISSION_INVOICE_ID, a.internal_file_no as INTERNAL_FILE_NO, a.net_realize_value as NET_REALIZE_VALUE, a.amount as INCENTIVE_CLAIM, b.id as RECV_ID
    from  com_sales_contract d,cash_incentive_submission a
    left join cash_incentive_received_mst b on a.id=b.cash_incentive_sub_id and b.status_active=1 and b.is_deleted=0 
    where a.internal_file_no=d.internal_file_no and a.is_lc_sc=2 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_id $bank_id $internal_file $date_cond $sc_file_year
    group by a.id, a.bank_id, a.submission_date, b.id, a.is_lc_sc, a.lc_sc_id, a.realization_id, a.submission_invoice_id, a.internal_file_no, a.net_realize_value, a.amount";

    // echo $mst_sql;die;
    $mst_sql_result=sql_select($mst_sql);
    unset($mst_sql);
    foreach($mst_sql_result as $row)
    {
        if($row['IS_LC_SC']==1)
        {
            $lc_id.=$row['LC_SC_ID'].',';
        }
        if($row['IS_LC_SC']==2)
        {
            $sc_id.=$row['LC_SC_ID'].',';
        }
        $submission_invoice_id.=$row['SUBMISSION_INVOICE_ID'].',';
        if($row['RECV_ID']!=''){$incentive_recv_id.=$row['RECV_ID'].',';}
    }
    $submission_invoice_id=implode(",",array_unique(explode(",",chop($submission_invoice_id,','))));
    $incentive_recv_id=implode(",",array_unique(explode(",",chop($incentive_recv_id,','))));
    $lc_id=implode(",",array_unique(explode(",",chop($lc_id,','))));
    $sc_id=implode(",",array_unique(explode(",",chop($sc_id,','))));

    $incentive_recv_sql="SELECT a.id as ID, sum(b.document_currency) as DOCUMENT_CURRENCY, b.conversion_rate as CONVERSION_RATE, sum(b.domestic_currency) as DOMESTIC_CURRENCY from cash_incentive_received_mst a,cash_incentive_received_dtls b  where a.id in ($incentive_recv_id) and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,b.conversion_rate";  
    // echo $incentive_recv_sql;die;
    $incentive_recv_sql_result=sql_select($incentive_recv_sql);
    $incentive_recv_info=array();
    foreach($incentive_recv_sql_result as $row)
    {
        $incentive_recv_info[$row['ID']]['DOCUMENT_CURRENCY']=$row['DOCUMENT_CURRENCY'];
        $incentive_recv_info[$row['ID']]['CONVERSION_RATE']=$row['CONVERSION_RATE'];
        $incentive_recv_info[$row['ID']]['DOMESTIC_CURRENCY']=$row['DOMESTIC_CURRENCY'];
    }
    unset($incentive_recv_sql);
    unset($incentive_recv_sql_result);
    $invoice_sql="SELECT a.id as SUBMISSION_INVOICE_ID, sum(b.net_invo_value) as INVOICE_VALUE 
	from com_export_doc_submission_mst a, com_export_doc_submission_invo b
	where a.id=b.doc_submission_mst_id and  a.id in ($submission_invoice_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id";
    // echo $invoice_sql;die;
    $invoice_sql_result=sql_select($invoice_sql);
    $invoice_info=array();
    foreach($invoice_sql_result as $row)
    {
        $invoice_info[$row["SUBMISSION_INVOICE_ID"]]=$row["INVOICE_VALUE"];
    }
    unset($invoice_sql);
    unset($invoice_sql_result);
    $lc_sc_sql="SELECT id as ID, export_lc_no as LC_SC_NO, lc_date as LC_SC_DATE, lc_value as LC_SC_VALUE, buyer_name as BUYER_NAME, bank_file_no as BANK_FILE_NO, lc_year as FILE_YEAR, 1 as IS_LC_SC
	from com_export_lc 
	where id in ($lc_id) and status_active=1 and is_deleted=0
	union all
	SELECT id as ID, contract_no as LC_SC_NO, contract_date as LC_SC_DATE, contract_value as LC_SC_VALUE, buyer_name as BUYER_NAME, bank_file_no as BANK_FILE_NO, sc_year as FILE_YEAR, 2 as IS_LC_SC
	from com_sales_contract 
	where id in ($sc_id) and status_active=1 and is_deleted=0";
    // echo $details_sql;die;
    $lc_sc_sql_result=sql_select($lc_sc_sql);
    $lc_sc_info=array();
    foreach($lc_sc_sql_result as $row)
    {
        $lc_sc_info[$row['IS_LC_SC']][$row['ID']]['LC_SC_NO']=$row['LC_SC_NO'];       
        $lc_sc_info[$row['IS_LC_SC']][$row['ID']]['LC_SC_DATE']=$row['LC_SC_DATE'];       
        $lc_sc_info[$row['IS_LC_SC']][$row['ID']]['LC_SC_VALUE']=$row['LC_SC_VALUE'];             
        $lc_sc_info[$row['IS_LC_SC']][$row['ID']]['BUYER_NAME']=$row['BUYER_NAME'];             
        $lc_sc_info[$row['IS_LC_SC']][$row['ID']]['BANK_FILE_NO']=$row['BANK_FILE_NO'];             
        $lc_sc_info[$row['IS_LC_SC']][$row['ID']]['FILE_YEAR']=$row['FILE_YEAR'];             
    }
    unset($lc_sc_sql);
    unset($lc_sc_sql_result);
    ob_start();
    ?>
        <div style="width:1440px;">
            <table width="1400" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="30">Sl. No.</th>
                        <th width="100">Bank Name</th>
                        <th width="80">File No</th>
                        <th width="50">File Year</th>
                        <th width="80">Bank File</th>
                        <th width="120">Buyer Name</th>
                        <th width="100">Export L/C No.</th>
                        <th width="100">LC Date</th>
                        <th width="100">Export L/c Value ($)</th>
                        <th width="80">Total Export Value ($)</th>
                        <th width="80">Realize Value ($)</th>
                        <th width="80">Total Incentive claim ($)</th>
                        <th width="100">Cash Incentive Received Value $</th>
                        <th width="80">Exchange Rate</th>
                        <th width="110">Cash Incentive Received Value BTD</th>
                        <th width="110">Cash Incentive Rcv Balance ($)</th>
                    </tr>
                </thead>
            </table>
            <div style="max-height: 380px; overflow-y:scroll;width:1400px">
                <table  width="1400" class="rpt_table" rules="all" border="1" id="table_body"> 
                <tbody >
                    <?
                        $i=1;
                        foreach($mst_sql_result as $row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF";
                            else $bgcolor="#FFFFFF";
                            $invoice_value=$lc_sc_value=0;$lc_sc_no=$file_year=$lc_sc_date=$lc_sc_buyer=$lc_sc_bank_file=$buyer_name='';

                            $submission_invoice_id_arr=explode(',',$row['SUBMISSION_INVOICE_ID']);
                            foreach($submission_invoice_id_arr as $val)
                            {
                                $invoice_value+= $invoice_info[$val];
                            }

                            $lc_sc_id_arr=explode(',',$row['LC_SC_ID']);
                            foreach($lc_sc_id_arr as $val)
                            {
                                $lc_sc_no.=$lc_sc_info[$row['IS_LC_SC']][$val]['LC_SC_NO'].', ';
                                $file_year.=$lc_sc_info[$row['IS_LC_SC']][$val]['FILE_YEAR'].', ';
                                $lc_sc_date.=change_date_format($lc_sc_info[$row['IS_LC_SC']][$val]['LC_SC_DATE']).', ';
                                $lc_sc_buyer.=$lc_sc_info[$row['IS_LC_SC']][$val]['BUYER_NAME'].',';
                                $lc_sc_value+=$lc_sc_info[$row['IS_LC_SC']][$val]['LC_SC_VALUE'];
                                if($lc_sc_info[$row['IS_LC_SC']][$val]['BANK_FILE_NO']!=''){$lc_sc_bank_file.=$lc_sc_info[$row['IS_LC_SC']][$val]['BANK_FILE_NO'].', ';}
                            }

                            $buyer_id=array_unique(explode(",",chop($lc_sc_buyer,',')));
                            foreach($buyer_id as $val)
                            {
                                $buyer_name.=$buyer_arr[$val].', ';
                            }

                            ?>
                                <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                                    <td width="30" style="word-break:break-all;"align="center"><?=$i;?></td>
                                    <td width="100" style="word-break:break-all;"><? echo $bank_arr[$row['BANK_ID']]; ?></td>
                                    <td width="80"style="word-break:break-all;"><? echo $row['INTERNAL_FILE_NO']; ?></td>
                                    <td width="50"style="word-break:break-all;"><? echo implode(",",array_unique(explode(",",chop($file_year,', ')))); ?></td>
                                    <td width="80"style="word-break:break-all;"><? echo chop($lc_sc_bank_file,', '); ?></td>
                                    <td width="120"style="word-break:break-all;"><? echo chop($buyer_name,', '); ?></td>
                                    <td width="100"style="word-break:break-all;"><? echo chop($lc_sc_no,', '); ?></td>
                                    <td width="100"style="word-break:break-all;" align="center"><? echo chop($lc_sc_date,', '); ?></td>
                                    <td width="100"style="word-break:break-all;"align="right"><? echo number_format($lc_sc_value,2); ?></td>
                                    <td width="80"style="word-break:break-all;"align="right"><? echo number_format($invoice_value,2); ?></td>
                                    <td width="80" style="word-break:break-all;"align="right"><? echo number_format($row['NET_REALIZE_VALUE'],2); ?></td>
                                    <td width="80"style="word-break:break-all;"align="right"><a href="##" onClick="openmypage_popup('<? echo $row['ID']; ?>','Submission Info','submission_popup');" ><? echo number_format($row['INCENTIVE_CLAIM'],2);
                                    $total_incentive_claim+=$row['INCENTIVE_CLAIM']; ?></a></td>
                                    <td width="100" style="word-break:break-all;"align="right"><a href="##" onClick="openmypage_popup('<? echo $row['RECV_ID']; ?>','Received Info','received_popup');" ><? echo number_format($incentive_recv_info[$row['RECV_ID']]['DOCUMENT_CURRENCY'],2);
                                    $total_document_currency+=$incentive_recv_info[$row['RECV_ID']]['DOCUMENT_CURRENCY']; ?></a></td>
                                    <td width="80"style="word-break:break-all;" align="right"><? echo number_format($incentive_recv_info[$row['RECV_ID']]['CONVERSION_RATE'],2); ?></td>
                                    <td width="110"style="word-break:break-all;" align="right"><? echo number_format($incentive_recv_info[$row['RECV_ID']]['DOMESTIC_CURRENCY'],2); 
                                    $total_domestic_currency+=$incentive_recv_info[$row['RECV_ID']]['DOMESTIC_CURRENCY']; ?></td>
                                    <td width="110" align="right"><? echo number_format($row['INCENTIVE_CLAIM']-$incentive_recv_info[$row['RECV_ID']]['DOCUMENT_CURRENCY'],2); 
                                    $total_cash_balance+=$row['INCENTIVE_CLAIM']-$incentive_recv_info[$row['RECV_ID']]['DOCUMENT_CURRENCY']; ?></td>
                                </tr>
                            <?
                            $i++;
                        }
                    ?>
                </tbody>
                 <tfoot>
                    <tr>
                        <th colspan="11"><strong>Total</strong></th>
                        <th><?echo number_format($total_incentive_claim,2);?></th>
                        <th><?echo number_format($total_document_currency,2);?></th>
                        <th></th>
                        <th><?echo number_format($total_domestic_currency,2);?></th>
                        <th><?echo number_format($total_cash_balance,2);?></th>
                    </tr>
                 </tfoot>
            </div> </table>
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

if($action=="submission_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

    $sql_result=sql_select("SELECT sys_number as SYS_NUMBER,submission_date as SUBMISSION_DATE, special_submitted as SPECIAL_SUBMITTED, euro_incentive as EURO_INCENTIVE, general_incentive as GENERAL_INCENTIVE, market_submitted as MARKET_SUBMITTED, amount as AMOUNT  from cash_incentive_submission  where status_active=1 and is_deleted=0 and id=$id");
	?>
    <div id="report_container" align="center" style="width:750px">
	<fieldset style="width:750px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="750" cellpadding="0" cellspacing="0">
             	<thead>
                    <th width="100">Submission ID</th>
                    <th width="80" >Submission Date</th>
                    <th width="140">Submitted to Bank (Special Incentine 1% )</th>
                    <th width="100">Euro Zone 2% </th>
                    <th width="100">General Incentive 4% </th>
                    <th width="100">Submitted to Bank (New Market 4% ) </th>
                    <th >Total Incentive Claim Value(USD)</th>
                </thead>
                <tbody>
                    <tr>
                        <td><?echo $sql_result[0]['SYS_NUMBER'];?></td>
                        <td align="center"><?echo $sql_result[0]['SUBMISSION_DATE'];?></td>
                        <td align="right"><?echo $sql_result[0]['SPECIAL_SUBMITTED'];?></td>
                        <td align="right"><?echo $sql_result[0]['EURO_INCENTIVE'];?></td>
                        <td align="right"><?echo $sql_result[0]['GENERAL_INCENTIVE'];?></td>
                        <td align="right"><?echo $sql_result[0]['MARKET_SUBMITTED'];?></td>
                        <td align="right"><?echo $sql_result[0]['AMOUNT'];?></td>
                    </tr>
                </tbody>   
            </table>
        </fieldset>
    </div>
	<?
    exit();
}

if($action=="received_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

    $sql_result=sql_select("SELECT a.sys_number as SYS_NUMBER, a.received_date as RECEIVED_DATE, a.bill_no as BILL_NO, b.account_head_id as ACCOUNT_HEAD_ID, b.document_currency as DOCUMENT_CURRENCY, b.conversion_rate as CONVERSION_RATE, b.domestic_currency as DOMESTIC_CURRENCY from cash_incentive_received_mst a,cash_incentive_received_dtls b  where a.id=$id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	?>
    <div id="report_container" align="center" style="width:750px">
	<fieldset style="width:750px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="750" cellpadding="0" cellspacing="0">
             	<thead>
                    <th width="100">System ID</th>
                    <th width="80">Received Date</th>
                    <th width="100">Bill No</th>
                    <th width="150">Account Head</th>
                    <th width="100">Document Currency (USD)</th>
                    <th width="100">Conversion Rate</th>
                    <th >Domestic Currency</th>
                </thead>
                <tbody>
                    <?
                        foreach($sql_result as $row)
                        {
                            ?>
                                <tr>
                                    <td><?echo $row['SYS_NUMBER'];?></td>
                                    <td align="center"><?echo $row['RECEIVED_DATE'];?></td>
                                    <td><?echo $row['BILL_NO'];?></td>
                                    <td><?echo $commercial_head[$row['ACCOUNT_HEAD_ID']];?></td>
                                    <td align="right"><?echo $row['DOCUMENT_CURRENCY'];$total_document+=$row['DOCUMENT_CURRENCY'];?></td>
                                    <td align="right"><?echo $row['CONVERSION_RATE'];?></td>
                                    <td align="right"><?echo $row['DOMESTIC_CURRENCY'];$total_domestic+=$row['DOMESTIC_CURRENCY'];?></td>
                                </tr>
                            <?
                        }
                    ?>
                </tbody>   
                <tfoot>
                    <tr>
                        <th colspan="4">Total</th>
                        <th><?echo number_format($total_document,2);?></th>
                        <th>&nbsp;</th>
                        <th><?echo number_format($total_domestic,2);?></th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </div>
	<?
    exit();
}

