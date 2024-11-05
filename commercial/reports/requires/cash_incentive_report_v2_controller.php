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
    $cbo_buyer_name      = str_replace("'","",$cbo_buyer_name); 
	$cbo_bank_name      = str_replace("'","",$cbo_bank_name);   
    $txt_incentive_bank_no = str_replace("'","",$txt_incentive_bank_no);     
    $cbo_data_type      = str_replace("'","",$cbo_data_type);       
    $txt_search_data    = str_replace("'","",$txt_search_data);       
    $cbo_date_type      = str_replace("'","",$cbo_date_type);           
    $txt_date_from      = str_replace("'","",$txt_date_from);    
    $txt_date_to        = str_replace("'","",$txt_date_to); 
    $report_type        = str_replace("'","",$report_type); 

    $lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $buyer_arr = return_library_array("select id,buyer_name from lib_buyer where is_deleted=0","id","buyer_name");
    $bank_arr = return_library_array("select id,bank_name from lib_bank where is_deleted=0","id","bank_name");

    if($report_type==1)
    {
        if ($cbo_company_name) {$search_cloud.=" and a.company_id=$cbo_company_name";}
		if ($cbo_buyer_name) {$search_cloud.=" and c.BUYER_ID=$cbo_buyer_name";}
		
        if ($cbo_bank_name) {$search_cloud.=" and a.bank_id=$cbo_bank_name";}
        if ($txt_incentive_bank_no!="") {$search_cloud.=" and a.incentive_bank_file='$cbo_bank_name'";}
        if ($txt_search_data!="")
        {
            if($cbo_data_type==1)
            {
                $search_cloud.=" and a.sys_number_prefix_num='$txt_search_data'";
            }
            else if($cbo_data_type==2)
            {
                $search_cloud.=" and f.sys_number_prefix_num='$txt_search_data'";
            }
            else if($cbo_data_type==3)
            {
                $search_cloud.=" and c.invoice_no='$txt_search_data'";
            }
            else if($cbo_data_type==4)
            {
                $search_cloud.=" and c.exp_form_no='$txt_search_data'";
            }
        }


        if ($txt_date_from != '' && $txt_date_to != '') 
        {
            if ($db_type == 0) {
                $date_from= change_date_format($txt_date_from, 'yyyy-mm-dd');
                $date_to= change_date_format($txt_date_to, 'yyyy-mm-dd');
            } else if ($db_type == 2) {
                $date_from=change_date_format($txt_date_from, '', '', 1) ;
                $date_to= change_date_format($txt_date_to, '', '', 1);
            }
            if($cbo_date_type==1)
            {
                $search_cloud.= "and a.submission_date between '" .$date_from. "' and '" .$date_to. "'";	
            }  
            else
            {
                $search_cloud.= "and f.received_date between '" .$date_from. "' and '" .$date_to. "'";
            }
        }
		
		

        $mst_sql="SELECT a.id as ID, a.bank_id as BANK_ID, a.sys_number_prefix_num as SUBMISSION_NUM, a.submission_date as SUBMISSION_DATE, b.is_lc_sc as IS_LC_SC, b.lc_sc_id as LC_SC_ID, a.realization_id as REALIZATION_ID, a.submission_invoice_id as SUBMISSION_INVOICE_ID, a.net_realize_value as NET_REALIZE_VALUE, a.euro_incentive_chk as EURO_INCENTIVE_PAR, a.general_incentive_chk as GENERAL_INCENTIVE_PAR, sum(b.rlz_value) as RLZ_VALUE, sum(b.special_incentive) as SPECIAL_INCENTIVE, sum(b.euro_zone_incentive) as EURO_ZONE_INCENTIVE,  sum(b.general_incentive) as GENERAL_INCENTIVE, sum(b.market_incentive) as MARKET_INCENTIVE, f.id as RECV_ID, f.sys_number_prefix_num as RECEIVED_NUM, f.received_date as RECEIVED_DATE
        from com_export_invoice_ship_mst c, cash_incentive_submission a, cash_incentive_submission_dtls b
        left join cash_incentive_received_mst f on b.mst_id=f.cash_incentive_sub_id and f.status_active=1
        where c.id=b.submission_bill_id and a.id=b.mst_id and a.entry_form=566 and a.status_active=1 and b.status_active=1 and c.status_active=1 $search_cloud
        group by a.id, a.bank_id, a.sys_number_prefix_num, a.submission_date, b.is_lc_sc, b.lc_sc_id, a.realization_id, a.submission_invoice_id, a.net_realize_value,a.euro_incentive_chk, a.general_incentive_chk, f.id, f.sys_number_prefix_num, f.received_date";
        //echo $mst_sql;

        $mst_sql_result=sql_select($mst_sql);
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

        $incentive_recv_sql="SELECT a.id as ID, sum(b.document_currency) as DOCUMENT_CURRENCY, b.account_head_id as ACCOUNT_HEAD_ID, sum(b.domestic_currency) as DOMESTIC_CURRENCY from cash_incentive_received_mst a,cash_incentive_received_dtls b  where a.id in ($incentive_recv_id) and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,b.account_head_id";  
        // echo $incentive_recv_sql;
        $incentive_recv_sql_result=sql_select($incentive_recv_sql);
        $incentive_recv_info=array();
        foreach($incentive_recv_sql_result as $row)
        {
            $incentive_recv_info[$row['ID']]['DOCUMENT_CURRENCY']+=$row['DOCUMENT_CURRENCY'];
            $incentive_recv_info[$row['ID']]['ACCOUNT_HEAD_ID'].=$commercial_head[$row['ACCOUNT_HEAD_ID']].", ";
            $incentive_recv_info[$row['ID']]['DOMESTIC_CURRENCY']+=$row['DOMESTIC_CURRENCY'];
        }
        unset($incentive_recv_sql);
        unset($incentive_recv_sql_result);
        $invoice_sql="SELECT a.id as INVOICE_ID, a.net_invo_value as NET_INVO_VALUE
        from com_export_invoice_ship_mst a
        where a.id in ($submission_invoice_id) and a.status_active=1 ";
        // echo $invoice_sql;
        $invoice_sql_result=sql_select($invoice_sql);
        $invoice_info=array();
        foreach($invoice_sql_result as $row)
        {
            $invoice_info[$row["INVOICE_ID"]]=$row["NET_INVO_VALUE"];
        }
        unset($invoice_sql);
        unset($invoice_sql_result);
        $lc_sc_sql="";
        if($lc_id!="")
        {
            $lc_sc_sql="SELECT id as ID, export_lc_no as LC_SC_NO,  lc_value as LC_SC_VALUE, buyer_name as BUYER_NAME, 1 as IS_LC_SC
            from com_export_lc 
            where id in ($lc_id) and status_active=1 and is_deleted=0";
        }
        if($sc_id!="")
        {
            if($lc_sc_sql!=""){$lc_sc_sql.=" union all ";}
            $lc_sc_sql.="SELECT id as ID, contract_no as LC_SC_NO, contract_value as LC_SC_VALUE, buyer_name as BUYER_NAME, 2 as IS_LC_SC
            from com_sales_contract 
            where id in ($sc_id) and status_active=1 and is_deleted=0";
        }
        //echo $lc_sc_sql;
        $lc_sc_sql_result=sql_select($lc_sc_sql);
        $lc_sc_info=array();
        foreach($lc_sc_sql_result as $row)
        {
            $lc_sc_info[$row['IS_LC_SC']][$row['ID']]['LC_SC_NO']=$row['LC_SC_NO'];             
            $lc_sc_info[$row['IS_LC_SC']][$row['ID']]['LC_SC_VALUE']=$row['LC_SC_VALUE'];             
            $lc_sc_info[$row['IS_LC_SC']][$row['ID']]['BUYER_NAME']=$row['BUYER_NAME'];                        
        }
		//echo "<pre>";print_r($lc_sc_info);die;
        unset($lc_sc_sql);
        unset($lc_sc_sql_result);
        $div_width="1550";
        ob_start();
        ?>
            <div style="width:<?=$div_width+20;?>px;">
                <table width="<?=$div_width;?>px" >
                    <tr>
                        <td align="center" style="font-size:22px"><strong>CASH INCENTIVE REPORT</strong></td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:18px"><strong>COMPANY: <?=strtoupper($lib_company_arr[$cbo_company_name]);?>.</strong></td>
                    </tr>
                </table>
                <table width="<?=$div_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <thead>
                        <tr>
                            <th width="30">Sl. No.</th>
                            <th width="100">Bank Name</th>
                            <th width="80">Submission ID</th>
                            <th width="80">Receive ID</th>
                            <th width="80">Submission Date</th>
                            <th width="80">Receive Date</th>
                            <th width="120">Buyer Name</th>
                            <th width="100">Export L/C No.</th>
                            <th width="80">Export LC/SC Value ($)</th>
                            <th width="80">Total Net Invoice Value ($)</th>
                            <th width="80">Total Realized Value ($)</th>
                            <th width="80">Incentive Claim (%)</th>
                            <th width="80">Total Incentive claim ($)</th>
                            <th width="80">Cash Incentive Received Value $</th>
                            <th width="80">Exchange Rate</th>
                            <th width="100">Cash Incentive Received Value BTD</th>
                            <th width="100">Account Head</th>
                            <th >Balance ($)</th>
                        </tr>
                    </thead>
                </table>
                <div id="table_body" style="width:<?=$div_width+18;?>px; overflow-y: scroll; max-height: 350px;" align="left">
                    <table width="<?=$div_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align = "left">
                        <tbody >
                            <?
                                $i=1;
                                foreach($mst_sql_result as $row)
                                {
                                    if($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
                                    $invoice_value=$lc_sc_value=$incentive_claim_value=0;
                                    $lc_sc_no=$lc_sc_buyer=$buyer_name=$incentive_claim_name='';

                                    $submission_invoice_id_arr=explode(',',$row['SUBMISSION_INVOICE_ID']);
                                    foreach($submission_invoice_id_arr as $val)
                                    {
                                        $invoice_value+= $invoice_info[$val];
                                    }

                                    $lc_sc_id_arr=explode(',',$row['LC_SC_ID']);
                                    foreach($lc_sc_id_arr as $val)
                                    {
                                        $lc_sc_no.=$lc_sc_info[$row['IS_LC_SC']][$val]['LC_SC_NO'].', ';
                                        $lc_sc_buyer.=$lc_sc_info[$row['IS_LC_SC']][$val]['BUYER_NAME'].',';
                                        $lc_sc_value+=$lc_sc_info[$row['IS_LC_SC']][$val]['LC_SC_VALUE'];
                                    }

                                    $buyer_id=array_unique(explode(",",chop($lc_sc_buyer,',')));
                                    foreach($buyer_id as $val)
                                    {
                                        $buyer_name.=$buyer_arr[$val].', ';
                                    }

                                    if($row['SPECIAL_INCENTIVE'])
                                    {
                                        $incentive_claim_name='Special Incentive 1%';
                                        $incentive_claim_value+=$row['SPECIAL_INCENTIVE'];
                                    }
                                    if($row['EURO_ZONE_INCENTIVE'])
                                    {
                                        if($incentive_claim_name!=""){$incentive_claim_name.=", ";}
                                        $incentive_claim_name.='Euro Zone Incentive(Yarn) '.$row['EURO_INCENTIVE_PAR']."%";
                                        $incentive_claim_value+=$row['EURO_ZONE_INCENTIVE'];
                                    }
                                    if($row['GENERAL_INCENTIVE'])
                                    {
                                        if($incentive_claim_name!=""){$incentive_claim_name.=", ";}
                                        $incentive_claim_name.='General Incentive(Yarn) '.$row['GENERAL_INCENTIVE_PAR']."%";
                                        $incentive_claim_value+=$row['GENERAL_INCENTIVE'];
                                    }
                                    if($row['MARKET_INCENTIVE'])
                                    {
                                        if($incentive_claim_name!=""){$incentive_claim_name.=", ";}
                                        $incentive_claim_name.='New Market 4%';
                                        $incentive_claim_value+=$row['MARKET_INCENTIVE'];
                                    }
                                    ?>
                                        <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                                            <td width="30" align="center"><?=$i;?></td>
                                            <td width="100"><? echo $bank_arr[$row['BANK_ID']]; ?></td>
                                            <td width="80" align="center"><? echo $row['SUBMISSION_NUM']; ?></td>
                                            <td width="80" align="center"><? echo $row['RECEIVED_NUM'];  ?></td>
                                            <td width="80" align="center"><? echo change_date_format($row['SUBMISSION_DATE']); ?></td>
                                            <td width="80" align="center"><? echo change_date_format($row['RECEIVED_DATE']); ?></td>
                                            <td width="120"><? echo chop($buyer_name,', '); ?></td>
                                            <td width="100" style="word-break: break-all;"><? echo chop($lc_sc_no,', '); ?></td>
                                            <td width="80" align="right"><? echo number_format($lc_sc_value,2); ?></td>
                                            <td width="80" align="right"><? echo number_format($invoice_value,2); ?></td>
                                            <td width="80" align="right"><? echo number_format($row['RLZ_VALUE'],2); ?></td>
                                            <td width="80" ><? echo $incentive_claim_name;?></td>
                                            <td width="80" align="right"><? echo number_format($incentive_claim_value,2);?></td>
                                            <td width="80" align="right"><? echo number_format($incentive_recv_info[$row['RECV_ID']]['DOCUMENT_CURRENCY'],2);?></td>
                                            <td width="80" align="right"><? echo number_format($incentive_recv_info[$row['RECV_ID']]['DOMESTIC_CURRENCY']/$incentive_recv_info[$row['RECV_ID']]['DOCUMENT_CURRENCY'],2); ?></td>
                                            <td width="100" align="right"><? echo number_format($incentive_recv_info[$row['RECV_ID']]['DOMESTIC_CURRENCY'],2);?></td>
                                            <td width="100"><? echo rtrim($incentive_recv_info[$row['RECV_ID']]['ACCOUNT_HEAD_ID'],", ") ?></td>
                                            <td align="right" align="right"><? echo number_format($incentive_claim_value-$incentive_recv_info[$row['RECV_ID']]['DOCUMENT_CURRENCY'],2);?></td>
                                        </tr>
                                    <?
                                    $i++;
                                    $total_incentive_claim+=$incentive_claim_value;
                                    $total_document_currency+=$incentive_recv_info[$row['RECV_ID']]['DOCUMENT_CURRENCY']; 
                                    $total_domestic_currency+=$incentive_recv_info[$row['RECV_ID']]['DOMESTIC_CURRENCY']; 
                                    $total_cash_balance+=$incentive_claim_value-$incentive_recv_info[$row['RECV_ID']]['DOCUMENT_CURRENCY'];
                                }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="12"><strong>Total</strong></th>
                                <th><?echo number_format($total_incentive_claim,2);?></th>
                                <th><?echo number_format($total_document_currency,2);?></th>
                                <th></th>
                                <th><?echo number_format($total_domestic_currency,2);?></th>
                                <th></th>
                                <th><?echo number_format($total_cash_balance,2);?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        <?
    }
    else
    {
        if ($cbo_company_name) {$search_cloud.=" and a.company_id=$cbo_company_name";}
		if ($cbo_buyer_name) {$search_cloud.=" and c.BUYER_ID=$cbo_buyer_name";}
        if ($cbo_bank_name) {$search_cloud.=" and a.bank_id=$cbo_bank_name";}
        if ($txt_incentive_bank_no!="") {$search_cloud.=" and a.incentive_bank_file='$cbo_bank_name'";}
        if ($txt_search_data!="")
        {
            if($cbo_data_type==1)
            {
                $search_cloud.=" and a.sys_number_prefix_num='$txt_search_data'";
            }
            else if($cbo_data_type==2)
            {
                $search_cloud.=" and f.sys_number_prefix_num='$txt_search_data'";
            }
            else if($cbo_data_type==3)
            {
                $search_cloud.=" and c.invoice_no='$txt_search_data'";
            }
            else if($cbo_data_type==4)
            {
                $search_cloud.=" and c.exp_form_no='$txt_search_data'";
            }
        }


        if ($txt_date_from != '' && $txt_date_to != '') 
        {
            if ($db_type == 0) {
                $date_from= change_date_format($txt_date_from, 'yyyy-mm-dd');
                $date_to= change_date_format($txt_date_to, 'yyyy-mm-dd');
            } else if ($db_type == 2) {
                $date_from=change_date_format($txt_date_from, '', '', 1) ;
                $date_to= change_date_format($txt_date_to, '', '', 1);
            }
            if($cbo_date_type==1)
            {
                $search_cloud.= "and a.submission_date between '" .$date_from. "' and '" .$date_to. "'";	
            }  
            else
            {
                $search_cloud.= "and f.received_date between '" .$date_from. "' and '" .$date_to. "'";
            }
        }

        $mst_sql="SELECT a.id as ID, a.bank_id as BANK_ID, a.sys_number_prefix_num as SUBMISSION_NUM, a.submission_date as SUBMISSION_DATE, b.is_lc_sc as IS_LC_SC, b.lc_sc_id as LC_SC_ID, a.realization_id as REALIZATION_ID, a.submission_invoice_id as SUBMISSION_INVOICE_ID, a.net_realize_value as NET_REALIZE_VALUE, a.euro_incentive_chk as EURO_INCENTIVE_PAR, a.general_incentive_chk as GENERAL_INCENTIVE_PAR, sum(b.rlz_value) as RLZ_VALUE, sum(b.special_incentive) as SPECIAL_INCENTIVE, sum(b.euro_zone_incentive) as EURO_ZONE_INCENTIVE,  sum(b.general_incentive) as GENERAL_INCENTIVE, sum(b.market_incentive) as MARKET_INCENTIVE, c.invoice_no as INVOICE_NO, c.exp_form_no as EXP_FORM_NO, f.id as RECV_ID, f.sys_number_prefix_num as RECEIVED_NUM, f.received_date as RECEIVED_DATE
        from com_export_invoice_ship_mst c, cash_incentive_submission a, cash_incentive_submission_dtls b
        left join cash_incentive_received_mst f on b.mst_id=f.cash_incentive_sub_id and f.status_active=1
        where c.id=b.submission_bill_id and a.id=b.mst_id and a.entry_form=566 and a.status_active=1 and b.status_active=1 and c.status_active=1 $search_cloud
        group by a.id, a.bank_id, a.sys_number_prefix_num, a.submission_date, b.is_lc_sc, b.lc_sc_id, a.realization_id, a.submission_invoice_id, a.net_realize_value,a.euro_incentive_chk, a.general_incentive_chk, c.invoice_no, c.exp_form_no, f.id, f.sys_number_prefix_num, f.received_date";
        // echo $mst_sql;

        $mst_sql_result=sql_select($mst_sql);
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

        $incentive_recv_sql="SELECT a.id as ID, sum(b.document_currency) as DOCUMENT_CURRENCY, b.account_head_id as ACCOUNT_HEAD_ID, sum(b.domestic_currency) as DOMESTIC_CURRENCY from cash_incentive_received_mst a,cash_incentive_received_dtls b  where a.id in ($incentive_recv_id) and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,b.account_head_id";  
        // echo $incentive_recv_sql;
        $incentive_recv_sql_result=sql_select($incentive_recv_sql);
        $incentive_recv_info=array();
        foreach($incentive_recv_sql_result as $row)
        {
            $incentive_recv_info[$row['ID']]['DOCUMENT_CURRENCY']+=$row['DOCUMENT_CURRENCY'];
            $incentive_recv_info[$row['ID']]['ACCOUNT_HEAD_ID'].=$commercial_head[$row['ACCOUNT_HEAD_ID']].", ";
            $incentive_recv_info[$row['ID']]['DOMESTIC_CURRENCY']+=$row['DOMESTIC_CURRENCY'];
        }
        unset($incentive_recv_sql);
        unset($incentive_recv_sql_result);
        $invoice_sql="SELECT a.id as INVOICE_ID, a.net_invo_value as NET_INVO_VALUE
        from com_export_invoice_ship_mst a
        where a.id in ($submission_invoice_id) and a.status_active=1 ";
        // echo $invoice_sql;
        $invoice_sql_result=sql_select($invoice_sql);
        $invoice_info=array();
        foreach($invoice_sql_result as $row)
        {
            $invoice_info[$row["INVOICE_ID"]]=$row["NET_INVO_VALUE"];
        }
        unset($invoice_sql);
        unset($invoice_sql_result);
        $lc_sc_sql="";
        if($lc_id!="")
        {
            $lc_sc_sql="SELECT id as ID, export_lc_no as LC_SC_NO,  lc_value as LC_SC_VALUE, buyer_name as BUYER_NAME, 1 as IS_LC_SC
            from com_export_lc 
            where id in ($lc_id) and status_active=1 and is_deleted=0";
        }
        if($sc_id!="")
        {
            if($lc_sc_sql!=""){$lc_sc_sql.=" union all ";}
            $lc_sc_sql.="SELECT id as ID, contract_no as LC_SC_NO, contract_value as LC_SC_VALUE, buyer_name as BUYER_NAME, 2 as IS_LC_SC
            from com_sales_contract 
            where id in ($sc_id) and status_active=1 and is_deleted=0";
        }
        // echo $lc_sc_sql;
        $lc_sc_sql_result=sql_select($lc_sc_sql);
        $lc_sc_info=array();
        foreach($lc_sc_sql_result as $row)
        {
            $lc_sc_info[$row['IS_LC_SC']][$row['ID']]['LC_SC_NO']=$row['LC_SC_NO'];             
            $lc_sc_info[$row['IS_LC_SC']][$row['ID']]['LC_SC_VALUE']=$row['LC_SC_VALUE'];             
            $lc_sc_info[$row['IS_LC_SC']][$row['ID']]['BUYER_NAME']=$row['BUYER_NAME'];                        
        }
        unset($lc_sc_sql);
        unset($lc_sc_sql_result);
        $div_width="1750";
        ob_start();
        ?>
            <div style="width:<?=$div_width+20;?>px;">
                <table width="<?=$div_width;?>px" >
                    <tr>
                        <td align="center" style="font-size:22px"><strong>CASH INCENTIVE REPORT</strong></td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:18px"><strong>COMPANY: <?=strtoupper($lib_company_arr[$cbo_company_name]);?>.</strong></td>
                    </tr>
                </table>
                <table width="<?=$div_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <thead>
                        <tr>
                            <th width="30">Sl. No.</th>
                            <th width="100">Bank Name</th>
                            <th width="80">Submission ID</th>
                            <th width="80">Receive ID</th>
                            <th width="80">Submission Date</th>
                            <th width="80">Receive Date</th>
                            <th width="120">Buyer Name</th>
                            <th width="100">Export L/C No.</th>
                            <th width="100">EXP No</th>
                            <th width="100">Invoice No</th>
                            <th width="80">Export LC/SC Value ($)</th>
                            <th width="80">Total Net Invoice Value ($)</th>
                            <th width="80">Total Realized Value ($)</th>
                            <th width="80">Incentive Claim (%)</th>
                            <th width="80">Total Incentive claim ($)</th>
                            <th width="80">Cash Incentive Received Value $</th>
                            <th width="80">Exchange Rate</th>
                            <th width="100">Cash Incentive Received Value BTD</th>
                            <th width="100">Account Head</th>
                            <th >Balance ($)</th>
                        </tr>
                    </thead>
                </table>
                <div style="width:<?=$div_width+18;?>px; overflow-y: scroll; max-height: 350px;" align="left">
                    <table width="<?=$div_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all"   align = "right">
                        <tbody id="table_body" >
                            <?
                                $i=1;
                                foreach($mst_sql_result as $row)
                                {
                                    if($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
                                    $invoice_value=$lc_sc_value=$incentive_claim_value=0;
                                    $lc_sc_no=$lc_sc_buyer=$buyer_name=$incentive_claim_name='';

                                    $submission_invoice_id_arr=explode(',',$row['SUBMISSION_INVOICE_ID']);
                                    foreach($submission_invoice_id_arr as $val)
                                    {
                                        $invoice_value+= $invoice_info[$val];
                                    }

                                    $lc_sc_id_arr=explode(',',$row['LC_SC_ID']);
                                    foreach($lc_sc_id_arr as $val)
                                    {
                                        $lc_sc_no.=$lc_sc_info[$row['IS_LC_SC']][$val]['LC_SC_NO'].', ';
                                        $lc_sc_buyer.=$lc_sc_info[$row['IS_LC_SC']][$val]['BUYER_NAME'].',';
                                        $lc_sc_value+=$lc_sc_info[$row['IS_LC_SC']][$val]['LC_SC_VALUE'];
                                    }

                                    $buyer_id=array_unique(explode(",",chop($lc_sc_buyer,',')));
                                    foreach($buyer_id as $val)
                                    {
                                        $buyer_name.=$buyer_arr[$val].', ';
                                    }

                                    if($row['SPECIAL_INCENTIVE'])
                                    {
                                        $incentive_claim_name='Special Incentive 1%';
                                        $incentive_claim_value+=$row['SPECIAL_INCENTIVE'];
                                    }
                                    if($row['EURO_ZONE_INCENTIVE'])
                                    {
                                        if($incentive_claim_name!=""){$incentive_claim_name.=", ";}
                                        $incentive_claim_name.='Euro Zone Incentive(Yarn) '.$row['EURO_INCENTIVE_PAR']."%";
                                        $incentive_claim_value+=$row['EURO_ZONE_INCENTIVE'];
                                    }
                                    if($row['GENERAL_INCENTIVE'])
                                    {
                                        if($incentive_claim_name!=""){$incentive_claim_name.=", ";}
                                        $incentive_claim_name.='General Incentive(Yarn) '.$row['GENERAL_INCENTIVE_PAR']."%";
                                        $incentive_claim_value+=$row['GENERAL_INCENTIVE'];
                                    }
                                    if($row['MARKET_INCENTIVE'])
                                    {
                                        if($incentive_claim_name!=""){$incentive_claim_name.=", ";}
                                        $incentive_claim_name.='New Market 4%';
                                        $incentive_claim_value+=$row['MARKET_INCENTIVE'];
                                    }
                                    ?>
                                        <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                                            <td width="30" align="center"><?=$i;?></td>
                                            <td width="100"><? echo $bank_arr[$row['BANK_ID']]; ?></td>
                                            <td width="80" align="center"><? echo $row['SUBMISSION_NUM']; ?></td>
                                            <td width="80" align="center"><? echo $row['RECEIVED_NUM'];  ?></td>
                                            <td width="80" align="center"><? echo change_date_format($row['SUBMISSION_DATE']); ?></td>
                                            <td width="80" align="center"><? echo change_date_format($row['RECEIVED_DATE']); ?></td>
                                            <td width="120"><? echo chop($buyer_name,', '); ?></td>
                                            <td width="100" style="word-break: break-all;"><? echo chop($lc_sc_no,', '); ?></td>
                                            <td width="100" style="word-break: break-all;"><? echo $row['EXP_FORM_NO']; ?></td>
                                            <td width="100" style="word-break: break-all;"><? echo $row['INVOICE_NO']; ?></td>
                                            <td width="80" align="right"><? echo number_format($lc_sc_value,2); ?></td>
                                            <td width="80" align="right"><? echo number_format($invoice_value,2); ?></td>
                                            <td width="80" align="right"><? echo number_format($row['RLZ_VALUE'],2); ?></td>
                                            <td width="80" ><? echo $incentive_claim_name;?></td>
                                            <td width="80" align="right"><? echo number_format($incentive_claim_value,2);?></td>
                                            <td width="80" align="right"><? echo number_format($incentive_recv_info[$row['RECV_ID']]['DOCUMENT_CURRENCY'],2);?></td>
                                            <td width="80" align="right"><? echo number_format($incentive_recv_info[$row['RECV_ID']]['DOMESTIC_CURRENCY']/$incentive_recv_info[$row['RECV_ID']]['DOCUMENT_CURRENCY'],2); ?></td>
                                            <td width="100" align="right"><? echo number_format($incentive_recv_info[$row['RECV_ID']]['DOMESTIC_CURRENCY'],2);?></td>
                                            <td width="100"><? echo rtrim($incentive_recv_info[$row['RECV_ID']]['ACCOUNT_HEAD_ID'],", ") ?></td>
                                            <td align="right" align="right"><? echo number_format($incentive_claim_value-$incentive_recv_info[$row['RECV_ID']]['DOCUMENT_CURRENCY'],2);?></td>
                                        </tr>
                                    <?
                                    $i++;
                                    $total_incentive_claim+=$incentive_claim_value;
                                    $total_document_currency+=$incentive_recv_info[$row['RECV_ID']]['DOCUMENT_CURRENCY']; 
                                    $total_domestic_currency+=$incentive_recv_info[$row['RECV_ID']]['DOMESTIC_CURRENCY']; 
                                    $total_cash_balance+=$incentive_claim_value-$incentive_recv_info[$row['RECV_ID']]['DOCUMENT_CURRENCY'];
                                }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="14"><strong>Total</strong></th>
                                <th><?echo number_format($total_incentive_claim,2);?></th>
                                <th><?echo number_format($total_document_currency,2);?></th>
                                <th></th>
                                <th><?echo number_format($total_domestic_currency,2);?></th>
                                <th></th>
                                <th><?echo number_format($total_cash_balance,2);?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        <?
    }

    foreach (glob("$user_id*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w') or die('canot open');
    $is_created = fwrite($create_new_doc,ob_get_contents()) or die('canot write');
    echo "$html****$filename";
    exit();	
}