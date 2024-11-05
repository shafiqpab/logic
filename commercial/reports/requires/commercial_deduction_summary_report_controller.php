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
require_once('../../../includes/class4/class.conversions.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 200, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_style(this.value);",0 );
	exit();
}



if($action=="report_generate"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_name=str_replace("'","",$cbo_company_name);
	$buyer_name=str_replace("'","",$cbo_buyer_name);
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);
	$txt_exchange_rate=str_replace("'","",$txt_exchange_rate);

    $lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $buyer_arr = return_library_array("select id,buyer_name from lib_buyer where is_deleted=0","id","buyer_name");

    if ($cbo_company_name!="") {$company_id=" and a.benificiary_id in($company_name)";} else { echo "Please Select Company First."; die;}
    if($buyer_name>0) {$buyer_cond=" and a.buyer_id in ($cbo_buyer_name)"; }else {$buyer_cond="";}
    if ($file_no!='') { $file_no_cond= " and d.internal_file_no in( $txt_file_no)";} else { $file_no_cond=""; }

    if($buyer_name>0) {$buyer_bcond=" and b.buyer_id in ($cbo_buyer_name)"; }else {$buyer_cond="";}
    


    $search_cond.= " and a.EX_FACTORY_DATE between '".$from_date."' and '".$to_date."'";
    $realiz_cond= " and b.RECEIVED_DATE between '".$from_date."' and '".$to_date."'";
    $bl_date_cond= " and a.BL_DATE between '".$from_date."' and '".$to_date."'";
    $bill_date_cond= " and a.BILL_DATE between '".$from_date."' and '".$to_date."'";

    $lc_sc_invoice="SELECT a.id as invoice_id, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id as LC_SC_ID,a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.BONUS_AMMOUNT, a.COMMISSION, a.ATSITE_DISCOUNT_AMT FROM com_export_invoice_ship_mst  a, com_export_lc b, com_export_invoice_ship_dtls c where a.lc_sc_id=b.id and c.mst_id=a.id and a.IS_LC=1 $search_cond $company_id $buyer_cond group by a.id, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.bonus_ammount,a .COMMISSION, a.ATSITE_DISCOUNT_AMT
    union all 
    SELECT a.id as invoice_id, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id as LC_SC_ID, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.BONUS_AMMOUNT, a.COMMISSION, a.ATSITE_DISCOUNT_AMT FROM com_export_invoice_ship_mst  a, com_sales_contract b, com_export_invoice_ship_dtls c where a.lc_sc_id=b.id and c.mst_id=a.id and a.IS_LC=2 $search_cond $company_id $buyer_cond group by a.id, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value,  b.id, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.bonus_ammount, a.COMMISSION, a.ATSITE_DISCOUNT_AMT";

    $sc_sql=sql_select($lc_sc_invoice);
    $lc_sc_arr=array();$company_buyer_wish_arr=array();
    foreach($sc_sql as $row){
        $lc_sc_arr[$row["LC_SC_ID"]]=$row["LC_SC_ID"];
        $company_buyer_wish_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["BENIFICIARY_ID"]=$row["BENIFICIARY_ID"];
        $company_buyer_wish_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["BUYER_ID"]=$row["BUYER_ID"];
        $company_buyer_wish_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["INVOICE_ID"]=$row["INVOICE_ID"];
        $company_buyer_wish_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["INVOICE_QUANTITY"]+=$row["INVOICE_QUANTITY"];
        $company_buyer_wish_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["INVOICE_VALUE"]+=$row["INVOICE_VALUE"];
        $company_buyer_wish_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["LC_SC_ID"]=$row["LC_SC_ID"];
        $company_buyer_wish_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["DISCOUNT_AMMOUNT"]+=$row["DISCOUNT_AMMOUNT"];
        $company_buyer_wish_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["CLAIM_AMMOUNT"]+=$row["CLAIM_AMMOUNT"];
        $company_buyer_wish_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["OTHER_DISCOUNT_AMT"]+=$row["OTHER_DISCOUNT_AMT"];
        $company_buyer_wish_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["BONUS_AMMOUNT"]+=$row["BONUS_AMMOUNT"];
        $company_buyer_wish_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["COMMISSION"]+=$row["COMMISSION"];
        $company_buyer_wish_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["ATSITE_DISCOUNT_AMT"]+=$row["ATSITE_DISCOUNT_AMT"];
    }

    $sql_order_set=sql_select("SELECT a.BENIFICIARY_ID, a.BUYER_ID, (d.current_invoice_qnty*c.total_set_qnty) as invoice_qnty_pcs from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls d, wo_po_break_down b, wo_po_details_master c where a.id=d.mst_id and d.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.BENIFICIARY_ID in($company_name) $buyer_cond $search_cond and d.status_active=1 and d.is_deleted=0");

    $inv_qnty_pcs_arr=array();
     foreach($sql_order_set as $row)
     {
         $inv_qnty_pcs_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["INVOICE_QNTY_PCS"]+=$row["INVOICE_QNTY_PCS"];
     }

    //Export Proceeds Realization Partial Partial
    $dtls_sql_deduct="SELECT a.MST_ID, b.BENIFICIARY_ID, b.BUYER_ID, 
    sum(CASE WHEN a.ACCOUNT_HEAD in(63,64,137,136,146,149)  THEN a.DOCUMENT_CURRENCY ELSE 0 END) as Deductions_partial_qty,
    sum(CASE WHEN a.ACCOUNT_HEAD in(62)  THEN a.DOCUMENT_CURRENCY ELSE 0 END) as local_commission_partial_qty,
    sum(CASE WHEN a.ACCOUNT_HEAD in(194)  THEN a.DOCUMENT_CURRENCY ELSE 0 END) as bill_discount
    from com_export_proceed_rlzn_dtls a, com_export_proceed_realization b where a.mst_id=b.id and b.BENIFICIARY_ID in ($company_name) and a.status_active=1 and b.status_active=1 and b.buyer_partial_rlz=1 $realiz_cond $buyer_bcond and type=0 group by MST_ID, b.BENIFICIARY_ID, b.BUYER_ID";

	$dtls_sql_deduct_partial = sql_select($dtls_sql_deduct);
    $deductions_partial_arr=array();
	foreach($dtls_sql_deduct_partial as $row){
        $deductions_partial_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["DEDUCTIONS_PARTIAL_QTY"]+=$row["DEDUCTIONS_PARTIAL_QTY"];
        $deductions_partial_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["LOCAL_COMMISSION_PARTIAL_QTY"]+=$row["LOCAL_COMMISSION_PARTIAL_QTY"];
        $deductions_partial_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["BILL_DISCOUNT"]+=$row["BILL_DISCOUNT"];
    }
	
    // print_r($deductions_partial_arr);
    //Export Proceeds Realization
    $dtls_sql_distribute="SELECT a.MST_ID, b.BENIFICIARY_ID, b.BUYER_ID, 
    sum(CASE WHEN a.ACCOUNT_HEAD in(63,64,137,136,146,149)  THEN a.DOCUMENT_CURRENCY ELSE 0 END) as Deductions_relization_qty,
    sum(CASE WHEN a.ACCOUNT_HEAD in(62)  THEN a.DOCUMENT_CURRENCY ELSE 0 END) as local_commission_realization_qty,
    sum(CASE WHEN a.ACCOUNT_HEAD not in(62,63,64,137,136,146,149,194)  THEN a.DOCUMENT_CURRENCY ELSE 0 END) as other_Deductions_qty,
    sum(CASE WHEN a.ACCOUNT_HEAD in(194)  THEN a.DOCUMENT_CURRENCY ELSE 0 END) as bill_discount
    from com_export_proceed_rlzn_dtls a, com_export_proceed_realization b where  a.mst_id=b.id and b.BENIFICIARY_ID in ($company_name) and b.buyer_partial_rlz=0   $realiz_cond $buyer_bcond and a.status_active=1 and b.status_active=1 and type=0 group by a.MST_ID, b.BENIFICIARY_ID, b.BUYER_ID";

    $dtls_sql_deduct_realization = sql_select($dtls_sql_distribute);
    $deduct_realization=array();
    foreach($dtls_sql_deduct_realization as $row){
        $deduct_realization[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]['DEDUCTIONS_RELIZATION_QTY']+=$row["DEDUCTIONS_RELIZATION_QTY"];
        $deduct_realization[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]['LOCAL_COMMISSION_REALIZATION_QTY']+=$row["LOCAL_COMMISSION_REALIZATION_QTY"];
        $deduct_realization[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]['OTHER_DEDUCTIONS_QTY']+=$row["OTHER_DEDUCTIONS_QTY"];
        $deduct_realization[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]['BILL_DISCOUNT']+=$row["BILL_DISCOUNT"];
    }

    //BL CHARGE
    $bl_charge=sql_select("SELECT a.AIR_COMPANY_CHARGE, a.AIR_BUYER_CHARGE, a.BL_CHARGE, a.STAMP_CHARGE, a.OTHERS_CHARGE,a.SURRENDERED_CHARGE, a.ADJUSTMENT_CHARGE, a.SPECIAL_CHARGE, b.BUYER_ID, a.COMPANY_ID FROM BL_CHARGE a, com_export_invoice_ship_mst b where b.id=a.INVOICE_ID and  a.COMPANY_ID in($company_name) $bl_date_cond $buyer_bcond and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1");
    $bl_carge_arr=array();
    foreach($bl_charge as $row){
        $extra_carge=$row["BL_CHARGE"]+$row["STAMP_CHARGE"]+$row["OTHERS_CHARGE"]+$row["SURRENDERED_CHARGE"]+$row["ADJUSTMENT_CHARGE"]+$row["SPECIAL_CHARGE"];
        $bl_carge_arr[$row["COMPANY_ID"]][$row["BUYER_ID"]]["AIR_AMMPUNT_TK"]+=$row["AIR_COMPANY_CHARGE"]+$row["AIR_BUYER_CHARGE"];
        $bl_carge_arr[$row["COMPANY_ID"]][$row["BUYER_ID"]]["EXTRA_CARGE"]+= $extra_carge;
    }
    
    //C and F Bill Entry
    $cfbill_sql=sql_select("SELECT a.COMPANY_ID, a.BUYER_ID, b.AMOUNT  FROM CNF_BILL_MST a, cnf_bill_dtls b where a.id=b.mst_id and a.COMPANY_ID in($company_name) and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 $bill_date_cond $buyer_cond and a.STATUS_ACTIVE=1");
    $CandFBill_Arr=array();
    foreach($cfbill_sql as $row){
        $CandFBill_Arr[$row["COMPANY_ID"]][$row["BUYER_ID"]]["AMOUNT"]+=$row["AMOUNT"];
    }

    $discount_ammount="Exprot Invoice Entry Page> Discount Amount + Claim Amount + Other Deduction Amount +Inspection Amount Head+Deductions at Source (at Export Procced Realization + Export Procced Realization Partial Page) Penalty on Goods Discrepancy+ Penalty on Doc Discrepancy+ Buyer Discripency Fee + Late Inspection penalty+ Late presentation charges+ Late shipment penalty head amount.";
    $rebate_usd="Invoice Entry---- Commission Amount Export Proceed Realization + Export Proceed Realization Partial-----Buying House Commision";
    $other_didaction="Deductions at Source (at Procced Realization Enty) With out Bill Discount +Penalty on Goods Discrepancy+ Penalty on Doc Discrepancy+ Buyer Discripency Fee+ + Late Inspection penalty+ Late presentation charges+ Late shipment penalty + Buying House Commision head amount";
    $discount_for_atsine="Invoice Entry Page> Discount For At Sight Payment Amount + Export Porcced Realization entry Deduction at source Head Name (Bill Discount)";
    $air_fright_amm="BL Charge Entry Page> Air Freight Charge -Company + Air Freight Charge -Buyer";
    $miscellaneous="From Commercial>Import>C and F Bill Entry>Field Name: 'Total' +BL Charge Entry>all head amount without Air Freight Charge -Company+Air Freight Charge -Buyer amount";

    ob_start();
    ?>
    <div style="width:2023px;">
         <table width="2023" cellspacing="0" cellpadding="0">
                <?
                    $company_library=sql_select("select company_name from lib_company where id in(".$company_name.")");

                    foreach( $company_library as $row)
                    {
                        $company_names.=$row[csf('company_name')].", ";
                    }
                ?>     
            <tr>
                <td colspan="17" align="center" style="font-size:22px"><center><strong><? echo rtrim($company_names,", ");?></strong></center></td>
            </tr>
            <tr>
                <td colspan="17" align="center" width="2000"><p style="font-size:20px">Commercial Deduction Summary Report</p>
                </td>
            </tr>				
        </table>
        <br><br>
        <table width="2000" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="30"><p> Sl No</p></th>
                    <th width="150"><p>Company</p></th>
                    <th width="100"><p>Buyer Name</p></th>
                    <th width="100"><p>Export Qty (Pcs)</p></th>
                    <th width="120"><p>Export Value (USD)</p></th>
                    <th width="120" title="<?=$discount_ammount?>"><p>Discount Amount USD</p></th>
                    <th width="120" title="<?=$rebate_usd?>"><p>Rebate USD</p></th>
                    <th width="120" title="<?=$other_didaction?>"><p>Other Deduction USD</p></th>
                    <th width="120" title="<?=$discount_for_atsine?>"><p> Discount For At Sight Payment USD</p></th>
                    <th width="120" title="<?=$air_fright_amm?>"><p>Air Freight Ammount BDT</p></th>
                    <th width="120"><p>Air Freight Amount USD</p></th>
                    <th width="120" title="<?=$miscellaneous?>"><p>Miscellaneous Expense USD</p></th>
                    <th width="120"><p>UD Cost USD</p></th>
                    <th width="120"><p>Discount Amount Percentage</p></th>
                    <th width="120"><p>Air Feright Amount Percentage</p></th>
                    <th width="140"><p>Total Deduction Amount USD</p></th>
                    <th >Total Deduction Amount Percentage</th>
                </tr>
            </thead>
            </table>
            <div style="width:2000px; max-height:400px; overflow-x:hidden;" id="scroll_body">
            <table cellspacing="0" width="2000"  border="1" rules="all" class="rpt_table" id="tbl_body">
            <tbody id="table_body">
                <?
                    $i=1; $partial_qty=0;$relization_qty=0;
                    foreach($company_buyer_wish_arr as $company_key=>$buyer_data_arr)
                    {
                        $sub_invoce_qty=$sub_invoice_value=$sub_discount_ammount_usd=$sub_rebate_usd=$sub_other_deduction=$sub_discount_AtSight=$sub_air_ammpunt_tk=$sub_air_fright_ammount=$sub_miscellaneous_expense_usd=$sub_discount_amount_percentage=$sub_Air_Feright_Amount_Percentage=$sub_deduction_amount=$sub_deduction_amount_persentage=$partial_bill_discount=0;
                        foreach($buyer_data_arr as $row) 
                        {
                            $realization_id= $realization_arr[$row["INVOICE_ID"]][$row["LC_SC_ID"]];
                            $export_qty_pcs= $inv_qnty_pcs_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["INVOICE_QNTY_PCS"];
                            $partial_qty= $deductions_partial_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["DEDUCTIONS_PARTIAL_QTY"];
                            $relization_qty = $deduct_realization[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]['DEDUCTIONS_RELIZATION_QTY'];
                            //commision_qty
                            $local_com_partial_qty=$deductions_partial_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["LOCAL_COMMISSION_PARTIAL_QTY"];
                            $local_com_realization_qty=$deduct_realization[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["LOCAL_COMMISSION_REALIZATION_QTY"];
                            //other
                             $other_deduction=$deduct_realization[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]['OTHER_DEDUCTIONS_QTY'];
                            //FOR AT SIGHT
                            $bill_discount=$deduct_realization[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]['BILL_DISCOUNT'];

                            $partial_bill_discount= $deductions_partial_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["BILL_DISCOUNT"];

                            $discount_ammount_usd=$row["DISCOUNT_AMMOUNT"]+$row["CLAIM_AMMOUNT"]+$row["OTHER_DISCOUNT_AMT"]+$row["BONUS_AMMOUNT"]+$partial_qty+$relization_qty;
                            $rebate_usd=$row["COMMISSION"]+$local_com_partial_qty+$local_com_realization_qty;
                            $discount_AtSight=$row["ATSITE_DISCOUNT_AMT"]+$bill_discount+$partial_bill_discount;

                            $air_fright_ammount=$bl_carge_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["AIR_AMMPUNT_TK"]/$txt_exchange_rate;
                            
                            $miscellaneous_expense_usd= $CandFBill_Arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["AMOUNT"]+$bl_carge_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["EXTRA_CARGE"];
                              $miscell_usd= $miscellaneous_expense_usd/$txt_exchange_rate;
                            $total_deduction_amount_usd=$discount_ammount_usd+$rebate_usd+$other_deduction+$discount_AtSight+$air_fright_ammount+$miscell_usd;

                            if ($i%2==0) $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
                            ?>
                                <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                                    <td width="30" align="center"><?=$i;?></td>
                                    <td width="150"><p><? echo $lib_company_arr[$row['BENIFICIARY_ID']]; ?></p></td>
                                    <td width="100" align="center"><p><? echo $buyer_arr[$row['BUYER_ID']]; ?></p></td>
                                    <td width="100" align="right"> <p><? echo $export_qty_pcs; ?> </p></td>  
                                    <td width="120" align="right"><p><? echo number_format($row['INVOICE_VALUE'],2); ?></p></td>
                                    <td width="120" align="right" ><p> <a href="#" onClick="discount_amount_usd_details('didaction_ammount_usd','<? echo $row['BENIFICIARY_ID']."*".$row['BUYER_ID']."*".$from_date."*".$to_date; ?>');"> <? echo number_format($discount_ammount_usd,2); ?> </a></p></td>
                                    <td width="120" align="right"><p> <a href="#"onClick="rebate_details('rebate_pop_up','<? echo $row['BENIFICIARY_ID']."*".$row['BUYER_ID']."*".$from_date."*".$to_date; ?>');" > <? echo number_format($rebate_usd,2); ?></a> </p></td>

                                    <td width="120" align="right"><p> <a href="#"onClick="other_deduction_usd('other_deduction_pop_up','<? echo $row['BENIFICIARY_ID']."*".$row['BUYER_ID']."*".$from_date."*".$to_date; ?>');" > <? echo number_format($other_deduction,2); ?></a> </p></td>
                                    <td width="120" align="right"><p> <a href="#"onClick="discount_at_sight_payment_popup('discount_at_sight_payment','<? echo $row['BENIFICIARY_ID']."*".$row['BUYER_ID']."*".$from_date."*".$to_date; ?>');" > <? echo number_format($discount_AtSight,2); ?></a> </p></td>
                                    <td width="120" align="right"><p> <a href="#"onClick="air_freight_ammount_bdt_popup('air_freight_ammount_bdt','<? echo $row['BENIFICIARY_ID']."*".$row['BUYER_ID']."*".$from_date."*".$to_date; ?>');" > <? echo number_format($bl_carge_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["AIR_AMMPUNT_TK"],2);?></a> </p></td>
                                    <td width="120" align="right"><p><?echo number_format($air_fright_ammount,2); ?></p></td>                    
                                    <td width="120" align="right"><p> <a href="#"onClick="miscellaneous_expense_usd_popup('miscellaneous_expense_usd','<? echo $row['BENIFICIARY_ID']."*".$row['BUYER_ID']."*".$from_date."*".$to_date; ?>');" > <? echo number_format($miscell_usd,2);?></a> </p></td>
                                    <td width="120" align="right"></td>
                                    <td width="120" align="right"><p><? echo number_format(($discount_ammount_usd/$row['INVOICE_VALUE'])*100,2)." %"; ?></p></td>
                                    <td width="120" align="right"><p> <? echo number_format(($air_fright_ammount/$row['INVOICE_VALUE'])*100,2)." %"; ?></p></td>
                                    <td width="140" align="right"><p><? echo number_format($total_deduction_amount_usd,2) ?></p></td>                          
                                    <td  align="right"><p><? echo number_format(($total_deduction_amount_usd/$row['INVOICE_VALUE'])*100,2)." %" ?></p></td>                          
                                </tr>
                            <?
                            $i++;
                            $total_invoce_qty+=$export_qty_pcs;
                            $total_invoice_value+=$row['INVOICE_VALUE'];
                            $total_discount_ammount_usd+=$discount_ammount_usd;
                            $total_rebate_usd+=$rebate_usd;
                            $total_other_deduction+=$other_deduction;
                            $total_discount_AtSight+=$discount_AtSight;
                            $total_air_ammpunt_tk+=$bl_carge_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["AIR_AMMPUNT_TK"];
                            $total_air_fright_ammount+=$air_fright_ammount;
                            $total_miscellaneous_expense_usd+=$miscell_usd;
                            $total_discount_amount_percentage+=(($discount_ammount_usd/$row['INVOICE_VALUE'])*100);
                            $total_Air_Feright_Amount_Percentage+=(($air_fright_ammount/$row['INVOICE_VALUE'])*100);
                            $total_deduction_amount+=$total_deduction_amount_usd;
                            $total_deduction_amount_persentage+=(($total_deduction_amount_usd/$row['INVOICE_VALUE'])*100);
                            
                            $sub_invoce_qty+=$export_qty_pcs;
                            $sub_invoice_value+=$row['INVOICE_VALUE'];
                            $sub_discount_ammount_usd+=$discount_ammount_usd;
                            $sub_rebate_usd+=$rebate_usd;
                            $sub_other_deduction+=$other_deduction;
                            $sub_discount_AtSight+=$discount_AtSight;
                            $sub_air_ammpunt_tk+=$bl_carge_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["AIR_AMMPUNT_TK"];
                            $sub_air_fright_ammount+=$air_fright_ammount;
                            $sub_miscellaneous_expense_usd+=$miscell_usd;
                            $sub_discount_amount_percentage+=(($discount_ammount_usd/$row['INVOICE_VALUE'])*100);
                            $sub_Air_Feright_Amount_Percentage+=(($air_fright_ammount/$row['INVOICE_VALUE'])*100);
                            $sub_deduction_amount+=$total_deduction_amount_usd;
                            $sub_deduction_amount_persentage+=(($total_deduction_amount_usd/$row['INVOICE_VALUE'])*100);
                       }
                       $sub_dis_per=($sub_discount_ammount_usd/$sub_invoice_value)*100;
                       $sub_air_per=($sub_air_fright_ammount / $sub_invoice_value) * 100;
                       $sub_deduction_per=($sub_deduction_amount/$sub_invoice_value)*100;
                        //    if($company_key==2){
                        //     echo $sub_air_fright_ammount."_".$sub_invoice_value."_".$sub_air_per;
                        //    }
                       ?>
                        <tr>
                            <th colspan="3"><strong>Company Total : </strong></th>
                            <th align="right"><?=number_format($sub_invoce_qty,2)?></th>
                            <th align="right"><?=number_format($sub_invoice_value,2)?></th>
                            <th align="right"><?=number_format($sub_discount_ammount_usd,2)?></th>
                            <th align="right"><?=number_format($sub_rebate_usd,2)?></th>
                            <th align="right"><?=number_format($sub_other_deduction,2)?></th>
                            <th align="right"><?= number_format($sub_discount_AtSight,2);?></th>
                            <th align="right"><?=number_format($sub_air_ammpunt_tk,2)?></th>
                            <th align="right"><?=number_format($sub_air_fright_ammount,2)?></th>
                            <th align="right"><?=number_format($sub_miscellaneous_expense_usd,2)?></th>
                            <th></th>
                            <th align="right"><?=number_format($sub_dis_per,2)." %"?></th>
                            <th align="right"><?=number_format($sub_air_per,2)." %"?></th>
                            <th align="right"><?= number_format($sub_deduction_amount,2);?></th>
                            <th  align="right"><?=number_format($sub_deduction_per,2)." %"?></th>
                        </tr>
                       <?
                    }
                ?>
            </tbody>
            </div>
                    <?
                    $tot_dis_per=($total_discount_ammount_usd/$total_invoice_value)*100;
                    $tot_air_per=($total_air_fright_ammount / $total_invoice_value) * 100;
                    $tot_deduction_per=($total_deduction_amount/$total_invoice_value)*100;
                    ?>
            <tfoot>
                <tr>
                    <th colspan="3"><strong>Group Total: </strong></th>
                    <th  align="right"><?=number_format($total_invoce_qty,2)?></th>
                    <th  align="right"><?=number_format($total_invoice_value,2)?></th>
                    <th  align="right"><?=number_format($total_discount_ammount_usd,2)?></th>
                    <th  align="right"><?=number_format($total_rebate_usd,2)?></th>
                    <th  align="right"><?=number_format($total_other_deduction,2)?></th>
                    <th  align="right"><?= number_format($total_discount_AtSight,2);?></th>
                    <th  align="right"><?=number_format($total_air_ammpunt_tk,2)?></th>
                    <th  align="right"><?=number_format($total_air_fright_ammount,2)?></th>
                    <th  align="right"><?=number_format($total_miscellaneous_expense_usd,2)?></th>
                    <th></th>
                    <th  align="right"><?=number_format($tot_dis_per,2)." %"?></th>
                    <th  align="right"><?=number_format($tot_air_per,2)." %"?></th>
                    <th  align="right"><?= number_format($total_deduction_amount,2);?></th>
                    <th  align="right"><?=number_format($tot_deduction_per,2)." %"?></th>
                </tr>
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

if($action=="miscellaneous_expense_usd")
{
    echo load_html_head_contents("Details","../../../", 1, 1, $unicode,'','');
    extract($_REQUEST);
    $data=explode('*',$datas);
    $company_id = $data[0];
    $buyer_id = $data[1];
    $date_from = $data[2];
    $date_to = $data[3];
   
    if($buyer_id>0) {$buyer_cond=" and a.buyer_id in ($buyer_id)"; }else {$buyer_cond="";}
    if($buyer_id>0) {$buyer_con=" and b.buyer_id in ($buyer_id)"; }else {$buyer_cond="";}

    $invoice_date_cond = " AND a.EX_FACTORY_DATE between '".$date_from."' and '".$date_to."'";
    $blcharge_date_cond = " AND a.BL_DATE between '".$date_from."' and '".$date_to."'";
    $bill_date_cond = " AND a.BILL_DATE between '".$date_from."' and '".$date_to."'";

    $buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
    $lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

    $lc_sc_invoice="SELECT a.id as invoice_id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id as LC_SC_ID,a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.BONUS_AMMOUNT, a.COMMISSION, a.ATSITE_DISCOUNT_AMT FROM com_export_invoice_ship_mst  a, com_export_lc b, com_export_invoice_ship_dtls c where a.lc_sc_id=b.id and c.mst_id=a.id and a.IS_LC=1 and a.benificiary_id=$company_id $buyer_cond  group by a.id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.bonus_ammount,a .COMMISSION, a.ATSITE_DISCOUNT_AMT
    union all 
    SELECT a.id as invoice_id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id as LC_SC_ID, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.BONUS_AMMOUNT, a.COMMISSION, a.ATSITE_DISCOUNT_AMT FROM com_export_invoice_ship_mst  a, com_sales_contract b, com_export_invoice_ship_dtls c where a.lc_sc_id=b.id and c.mst_id=a.id and a.IS_LC=2 and a.benificiary_id=$company_id $buyer_cond group by a.id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value,  b.id, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.bonus_ammount, a.COMMISSION, a.ATSITE_DISCOUNT_AMT";
    $export_invoice = sql_select($lc_sc_invoice);

    $inv_arr=array();
    foreach($export_invoice as $row){
        $inv_arr[$row["INVOICE_ID"]]["INVOICE_NO"]=$row["INVOICE_NO"];
    }

   //C and F Bill Entry
    $cnf_bill_carge=sql_select("SELECT  a.COMPANY_ID, a.BUYER_ID, a.INVOICE_ID, 0 as BL_CHARGE, 0 as STAMP_CHARGE,0 as OTHERS_CHARGE, sum(b.AMOUNT) as AMOUNT, 0 as SURRENDERED_CHARGE,0 as ADJUSTMENT_CHARGE, 0 as SPECIAL_CHARGE
    FROM CNF_BILL_MST a, cnf_bill_dtls b where a.id=b.mst_id and a.COMPANY_ID =$company_id and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 $bill_date_cond $buyer_cond and a.STATUS_ACTIVE=1 group by a.COMPANY_ID, a.BUYER_ID, a.INVOICE_ID
    union all
    SELECT a.COMPANY_ID, b.BUYER_ID, a.INVOICE_ID, sum(a.BL_CHARGE) as BL_CHARGE, sum(a.STAMP_CHARGE) as STAMP_CHARGE, sum(a.OTHERS_CHARGE) as OTHERS_CHARGE, 0 as AMOUNT, sum(a.SURRENDERED_CHARGE) as SURRENDERED_CHARGE, sum(a.ADJUSTMENT_CHARGE) as ADJUSTMENT_CHARGE, sum(a.SPECIAL_CHARGE) as SPECIAL_CHARGE
    FROM BL_CHARGE a, com_export_invoice_ship_mst b where b.id=a.INVOICE_ID and  a.COMPANY_ID=$company_id $blcharge_date_cond $buyer_con and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1  group by a.COMPANY_ID, b.BUYER_ID, a.INVOICE_ID");
    ?>
    <div style="width:1300px;" id="report_div">
        <fieldset style="width:1300px; margin: 0 auto;">
            <table cellpadding="0" width="1300" class="rpt_table" rules="all" border="1" align="left">
                <thead>
                    <tr>
                        <th width="40"></th>
                        <th width="120"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="300" colspan="6">BL Charge Entry</th>
                        <th width="100"></th>
                    </tr>
                    <tr>
                        <th width="40" style="word-break: break-all;">SL</th>
                        <th width="120" style="word-break: break-all;">Company</th>
                        <th width="100" style="word-break: break-all;">Buyer</th>
                        <th width="100" style="word-break: break-all;">Invoice No.</th>
                        <th width="100" style="word-break: break-all;">C and F Bill Total value</th>
                        <th width="100" style="word-break: break-all;">BL Charge</th>                   
                        <th width="100" style="word-break: break-all;">Stump Charge </th>                                    
                        <th width="100" style="word-break: break-all;">Freight Adjustment/Local Charges </th>                   
                        <th width="100" style="word-break: break-all;">MBL Surrendered Fee </th>                   
                        <th width="100" style="word-break: break-all;">Special Permission </th>                                       
                        <th width="100" style="word-break: break-all;"> Othrers</th> 
                        <th width="100" style="word-break: break-all;">Total</th>                   
                    </tr>
                </thead>
                <tbody>
                    <?
                    $i = 1;
                    $tot_bill_qty = 0;
                    $tot_amount = 0;
                    foreach ($cnf_bill_carge as $row) { 

                        $total_carge=$row["AMOUNT"]+$row["BL_CHARGE"]+$row["STAMP_CHARGE"]+$row["OTHERS_CHARGE"]+$row["ADJUSTMENT_CHARGE"]+$row["SURRENDERED_CHARGE"]+$row["SPECIAL_CHARGE"];
                    ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $i; ?>">     
                        <td width="40" align="right" style="word-break: break-all;"><? echo $i; ?></td>
                        <td width="120" align="right" style="word-break: break-all;"><? echo $lib_company_arr[$row[csf('company_id')]]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $inv_arr[$row["INVOICE_ID"]]["INVOICE_NO"]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo number_format($row["AMOUNT"],2); ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo number_format($row["BL_CHARGE"],2); ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo number_format($row["STAMP_CHARGE"],2); ?></td>
                        
                        <td width="100" align="right" style="word-break: break-all;"><?  echo number_format($row["ADJUSTMENT_CHARGE"],2); ?></td>

                        <td width="100" align="right" style="word-break: break-all;"><?  echo number_format($row["SURRENDERED_CHARGE"],2); ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo number_format($row["SPECIAL_CHARGE"],2); ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo number_format($row["OTHERS_CHARGE"],2); ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo number_format($total_carge,2); ?></td>
                    </tr>
                    <?
                    $i++;
                    $total_ammount+=$row["AMOUNT"];
                    $total_bl_charge+=$row["BL_CHARGE"];
                    $total_stamp_charge+=$row["STAMP_CHARGE"];
                    $total_adjustment_charge+=$row["ADJUSTMENT_CHARGE"];
                    $total_surrendered_charge+=$row["SURRENDERED_CHARGE"];
                    $total_special_charge+=$row["SPECIAL_CHARGE"];
                    $total_others_charge+=$row["OTHERS_CHARGE"];
                    $all_total_carge+=$total_carge;
                    }
                    ?>                           
                </tbody>
                <tfoot>
                    <th colspan="4" align="right">Total&nbsp;</th>
                    <th width="100" align="right"><? echo number_format($total_ammount,2); ?></th>
                    <th width="100" align="right"><? echo number_format($total_bl_charge,2); ?></th>
                    <th width="100" align="right"><? echo number_format($total_stamp_charge,2); ?></th>
                    <th width="100" align="right"><? echo number_format($total_adjustment_charge,2); ?></th>
                    <th width="100" align="right"><? echo number_format($total_surrendered_charge,2); ?></th>
                    <th width="100" align="right"><? echo number_format($total_special_charge,2); ?></th>
                    <th width="100" align="right"><? echo number_format($total_others_charge,2); ?></th>
                    <th width="100" align="right"><? echo number_format($all_total_carge,2); ?></th>
                </tfoot>
            </table>
     
        </fieldset>
    </div>   
    <?    
    exit();	
}


if($action=="air_freight_ammount_bdt")
{
    echo load_html_head_contents("Details","../../../", 1, 1, $unicode,'','');
    extract($_REQUEST);
    $data=explode('*',$datas);
    $company_id = $data[0];
    $buyer_id = $data[1];
    $date_from = $data[2];
    $date_to = $data[3];
   
    if($buyer_id>0) {$buyer_cond=" and a.buyer_id in ($buyer_id)"; }else {$buyer_cond="";}
    if($buyer_id>0) {$buyer_con=" and b.buyer_id in ($buyer_id)"; }else {$buyer_cond="";}

    $invoice_date_cond = " AND a.EX_FACTORY_DATE between '".$date_from."' and '".$date_to."'";
    $blcharge_date_cond = " AND a.BL_DATE between '".$date_from."' and '".$date_to."'";
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
    $lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

    $lc_sc_invoice="SELECT a.id as invoice_id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id as LC_SC_ID,a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.BONUS_AMMOUNT, a.COMMISSION, a.ATSITE_DISCOUNT_AMT FROM com_export_invoice_ship_mst  a, com_export_lc b, com_export_invoice_ship_dtls c where a.lc_sc_id=b.id and c.mst_id=a.id and a.IS_LC=1 $invoice_date_cond and a.benificiary_id=$company_id $buyer_cond  group by a.id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.bonus_ammount,a .COMMISSION, a.ATSITE_DISCOUNT_AMT
    union all 
    SELECT a.id as invoice_id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id as LC_SC_ID, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.BONUS_AMMOUNT, a.COMMISSION, a.ATSITE_DISCOUNT_AMT FROM com_export_invoice_ship_mst  a, com_sales_contract b, com_export_invoice_ship_dtls c where a.lc_sc_id=b.id and c.mst_id=a.id and a.IS_LC=2 $invoice_date_cond and a.benificiary_id=$company_id $buyer_cond group by a.id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value,  b.id, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.bonus_ammount, a.COMMISSION, a.ATSITE_DISCOUNT_AMT";
    $export_invoice = sql_select($lc_sc_invoice);

    //BL CHARGE
    $bl_charge=sql_select("SELECT a.AIR_COMPANY_CHARGE, a.AIR_BUYER_CHARGE, a.INVOICE_ID, b.BUYER_ID, a.COMPANY_ID  FROM BL_CHARGE a, com_export_invoice_ship_mst b where b.id=a.INVOICE_ID and  a.COMPANY_ID=$company_id $blcharge_date_cond $buyer_con and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1");
    $air_fright_bl_carge_arr=array();
    foreach($bl_charge as $row){
        $air_fright_bl_carge_arr[$row["COMPANY_ID"]][$row["BUYER_ID"]][$row["INVOICE_ID"]]["TOTAL_COM_BUYER_CARGE"]+=$row["AIR_COMPANY_CHARGE"]+$row["AIR_BUYER_CHARGE"];
        $air_fright_bl_carge_arr[$row["COMPANY_ID"]][$row["BUYER_ID"]][$row["INVOICE_ID"]]["AIR_COMPANY_CHARGE"]+=$row["AIR_COMPANY_CHARGE"];
        $air_fright_bl_carge_arr[$row["COMPANY_ID"]][$row["BUYER_ID"]][$row["INVOICE_ID"]]["AIR_BUYER_CHARGE"]+=$row["AIR_BUYER_CHARGE"];
    }
    ?>
    <div style="width:790px;" id="report_div">
        <fieldset style="width:790px; margin: 0 auto;">
            <table cellpadding="0" width="790" class="rpt_table" rules="all" border="1" align="left">
                <thead>
                    <tr>
                        <th width="40" style="word-break: break-all;">SL</th>
                        <th width="120" style="word-break: break-all;">Company</th>
                        <th width="100" style="word-break: break-all;">Buyer</th>
                        <th width="100" style="word-break: break-all;">Invoice No.</th>
                        <th width="100" style="word-break: break-all;">Air Freight Charge -Company</th>
                        <th width="100" style="word-break: break-all;"> Air Freight Charge -Buyer</th>                   
                        <th width="100" style="word-break: break-all;"> Total</th>                   
                    </tr>
                </thead>
            </table>
            <div style="width:790px; max-height:310px; overflow-y:scroll" id="scroll_body" align="left">
            <table cellpadding="0" width="790" class="rpt_table" rules="all" border="1" id="html_search">
                <tbody>
                <?
                $i = 1;
                $tot_bill_qty = 0;
                $tot_amount = 0;
                foreach ($export_invoice as $row) { 
                   $com_buyer_carge= $air_fright_bl_carge_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]][$row["INVOICE_ID"]]["TOTAL_COM_BUYER_CARGE"];
                   $air_company_charge=$air_fright_bl_carge_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]][$row["INVOICE_ID"]]["AIR_COMPANY_CHARGE"];
                   $air_buyer_charge=$air_fright_bl_carge_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]][$row["INVOICE_ID"]]["AIR_BUYER_CHARGE"];
                ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $i; ?>">     
                        <td width="40" align="right" style="word-break: break-all;"><? echo $i; ?></td>
                        <td width="120" align="right" style="word-break: break-all;"><? echo $lib_company_arr[$row[csf('benificiary_id')]]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $row[csf('invoice_no')]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $air_company_charge; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $air_buyer_charge; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $com_buyer_carge; ?></td>
                    </tr>
                <?
                $i++;
                $total_air_company_charge+=$air_company_charge;
                $total_air_buyer_charge+=$air_buyer_charge;
                $total_com_buyer_carge+=$com_buyer_carge;
                }
                ?>                           
                </tbody>
                <tfoot>
                    <th colspan="4" align="right">Total&nbsp;</th>
                    <th width="100" align="right"><? echo number_format($total_air_company_charge,2); ?></th>
                    <th width="100" align="right"><? echo number_format($total_air_buyer_charge,2); ?></th>
                    <th width="100" align="right"><? echo number_format($total_com_buyer_carge,2); ?></th>
                </tfoot>
            </table>
        </fieldset>
    <?    
    exit();	
}

if($action=="discount_at_sight_payment")
{
    echo load_html_head_contents("Details","../../../", 1, 1, $unicode,'','');
    extract($_REQUEST);
    $data=explode('*',$datas);
    $company_id = $data[0];
    $buyer_id = $data[1];
    $date_from = $data[2];
    $date_to = $data[3];
   
    if($buyer_id>0) {$buyer_cond=" and a.buyer_id in ($buyer_id)"; }else {$buyer_cond="";}
    if($buyer_id>0) {$buyer_con=" and b.buyer_id in ($buyer_id)"; }else {$buyer_cond="";}

    $invoice_date_cond = " AND a.EX_FACTORY_DATE between '".$date_from."' and '".$date_to."'";
    $submit_date_cond = " AND a.SUBMIT_DATE between '".$date_from."' and '".$date_to."'";
    $received_date_cond = " AND b.RECEIVED_DATE between '".$date_from."' and '".$date_to."'";

    $buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
    $lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

    $lc_sc_invoice="SELECT a.id as invoice_id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id as LC_SC_ID,a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.BONUS_AMMOUNT, a.COMMISSION, a.ATSITE_DISCOUNT_AMT FROM com_export_invoice_ship_mst  a, com_export_lc b, com_export_invoice_ship_dtls c where a.lc_sc_id=b.id and c.mst_id=a.id and a.IS_LC=1 $invoice_date_cond and a.benificiary_id=$company_id $buyer_cond  group by a.id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.bonus_ammount,a .COMMISSION, a.ATSITE_DISCOUNT_AMT
    union all 
    SELECT a.id as invoice_id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id as LC_SC_ID, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.BONUS_AMMOUNT, a.COMMISSION, a.ATSITE_DISCOUNT_AMT FROM com_export_invoice_ship_mst  a, com_sales_contract b, com_export_invoice_ship_dtls c where a.lc_sc_id=b.id and c.mst_id=a.id and a.IS_LC=2 $invoice_date_cond and a.benificiary_id=$company_id $buyer_cond group by a.id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value,  b.id, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.bonus_ammount, a.COMMISSION, a.ATSITE_DISCOUNT_AMT";
    $export_invoice = sql_select($lc_sc_invoice);
    ?>
    <div style="width:690px;" id="report_div">
        <fieldset style="width:690px; margin: 0 auto;">
            <table cellpadding="0" width="690" class="rpt_table" rules="all" border="1" align="left">
                <thead>
                    <tr>
                        <th width="40" style="word-break: break-all;">SL</th>
                        <th width="120" style="word-break: break-all;">Company</th>
                        <th width="100" style="word-break: break-all;">Buyer</th>
                        <th width="100" style="word-break: break-all;">Invoice No.</th>
                        <th width="100" style="word-break: break-all;">Invoice value (Gross)</th>
                        <th width="100" style="word-break: break-all;"> Discount For At Sight Payment Amount</th>                   
                    </tr>
                </thead>
            </table>
            <div style="width:690px; max-height:310px; overflow-y:scroll" id="scroll_body" align="left">
            <table cellpadding="0" width="690" class="rpt_table" rules="all" border="1" id="html_search">
                <tbody>
                <?
                $i = 1;
                $tot_bill_qty = 0;
                $tot_amount = 0;
                foreach ($export_invoice as $row) { 
                    $didaction_ammount=$row[csf('discount_ammount')]+$row[csf('bonus_ammount')]+$row[csf('claim_ammount')]+$row[csf('other_discount_amt')];
                ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $i; ?>">     
                        <td width="40" align="right" style="word-break: break-all;"><? echo $i; ?></td>
                        <td width="120" align="right" style="word-break: break-all;"><? echo $lib_company_arr[$row[csf('benificiary_id')]]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $row[csf('invoice_no')]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $row[csf('invoice_value')]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $row[csf('atsite_discount_amt')]; ?></td>
                    </tr>
                <?
                $i++;
                $total_inv_value+=$row[csf('invoice_value')];
                $total_atsite_discount_amt+=$row[csf('atsite_discount_amt')];
                }
                ?>                           
                </tbody>
                <tfoot>
                    <th colspan="4" align="right">Total&nbsp;</th>
                    <th width="100" align="right"><? echo number_format($total_inv_value,2); ?></th>
                    <th width="100" align="right"><? echo number_format($total_atsite_discount_amt,2); ?></th>
                </tfoot>
            </table>
        </fieldset>
        <br> <br>
        <?  
     $DocToBank="SELECT a.COMPANY_ID, a.BANK_REF_NO, a.BUYER_ID, a.ID, sum(b.NET_INVO_VALUE) as bill_value FROM com_export_doc_submission_mst  a, com_export_doc_submission_invo b where a.id=b.DOC_SUBMISSION_MST_ID and a.ENTRY_FORM=40 and a.COMPANY_ID=$company_id group by a.COMPANY_ID, a.BANK_REF_NO, a.BUYER_ID, a.ID";
     $document_sub_arr = sql_select($DocToBank);

     foreach($document_sub_arr as $row){
        $realization_bill_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]][$row["ID"]]['BANK_REF_NO']=$row["BANK_REF_NO"]; 
        $realization_bill_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]][$row["ID"]]['BILL_VALUE']+=$row["BILL_VALUE"]; 
     }

 
   
     
     //Export Proceeds Realization with partial
    $dtls_sql_distribute="SELECT b.INVOICE_BILL_ID, b.BENIFICIARY_ID, b.BUYER_ID, 
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(194)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS BILL_DISCOUNT
     from com_export_proceed_rlzn_dtls a, com_export_proceed_realization b where  a.mst_id=b.id and b.BENIFICIARY_ID in ($company_id) and b.buyer_partial_rlz=0 $received_date_cond $buyer_con and  a.status_active=1 and b.status_active=1 and type=0 group by b.INVOICE_BILL_ID, b.BENIFICIARY_ID, b.BUYER_ID
     union all
     SELECT b.INVOICE_BILL_ID, b.BENIFICIARY_ID, b.BUYER_ID, 
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(194)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS BILL_DISCOUNT
     from com_export_proceed_rlzn_dtls a, com_export_proceed_realization b where  a.mst_id=b.id and b.BENIFICIARY_ID in ($company_id) and b.buyer_partial_rlz=1 $received_date_cond $buyer_con and  a.status_active=1 and b.status_active=1 and type=0 group by b.INVOICE_BILL_ID, b.BENIFICIARY_ID, b.BUYER_ID";  
 
     $dtls_sql_deduct_realization = sql_select($dtls_sql_distribute);
        ?>
        <fieldset style="width:790px; margin: 0 auto;">
            <table cellpadding="0" width="790" class="rpt_table" rules="all" border="1" align="left">
                <thead>
                    <tr><th colspan="12">From Realization</th></tr>
                    <tr>
                        <th width="40" style="word-break: break-all;">SL</th>
                        <th width="120" style="word-break: break-all;">Company</th>
                        <th width="100" style="word-break: break-all;">Buyer</th>
                        <th width="100" style="word-break: break-all;">Bill No</th>
                        <th width="100" style="word-break: break-all;">Bill Value</th>
                        <th width="100" style="word-break: break-all;">Bill Discount amount</th>                      
                    </tr>
                </thead>
            </table>
            <div style="width:790px; max-height:310px; overflow-y:scroll" id="scroll_body" align="left">
            <table cellpadding="0" width="790" class="rpt_table" rules="all" border="1" id="html_search">
                <tbody>
                <?
                $i = 1;
                $tot_bill_qty = 0;
                $tot_amount = 0;
                foreach ($dtls_sql_deduct_realization as $row) { 
                    $realization_bill_arr[$row["ID"]][$row["BUYER_ID"]][$row["INVOICE_BILL_ID"]]['BANK_REF_NO'];
                    $realization_bill_arr[$row["ID"]][$row["BUYER_ID"]][$row["INVOICE_BILL_ID"]]['BILL_VALUE'];
                    ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $i; ?>">     
                        <td width="40" align="right" style="word-break: break-all;"><? echo $i; ?></td>
                        <td width="120" align="right" style="word-break: break-all;"><? echo $lib_company_arr[$row[csf('benificiary_id')]]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo  $realization_bill_arr[$row["ID"]][$row["BUYER_ID"]][$row["INVOICE_BILL_ID"]]['BANK_REF_NO']; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo number_format($realization_bill_arr[$row["ID"]][$row["BUYER_ID"]][$row["INVOICE_BILL_ID"]]['BILL_VALUE'],2); ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo number_format($row["BILL_DISCOUNT"],2); ?></td>
                    </tr>
                <?
                $i++;
                $total_bill_value+=$realization_bill_arr[$row["ID"]][$row["BUYER_ID"]][$row["INVOICE_BILL_ID"]]['BILL_VALUE'];
                $total_bill_discount+=$row["BILL_DISCOUNT"];
                }
                ?>                           
                </tbody>
                <tfoot>
                    <th colspan="4" align="right">Total&nbsp;</th>
                    <th width="100" align="right"><? echo number_format($total_bill_value,2); ?></th>
                    <th width="100" align="right"><? echo number_format($total_bill_discount,2); ?></th>
                </tfoot>
            </table>
        </fieldset>
    </div>
    <?    
    exit();	
}


if($action=="other_deduction_pop_up")
{
    echo load_html_head_contents("Details","../../../", 1, 1, $unicode,'','');
    extract($_REQUEST);
    $data=explode('*',$datas);
    $company_id = $data[0];
    $buyer_id = $data[1];
    $date_from = $data[2];
    $date_to = $data[3];
    if($buyer_id>0) {$buyer_cond=" and a.buyer_id in ($buyer_id)"; }else {$buyer_cond="";}
    if($buyer_id>0) {$buyer_con=" and b.buyer_id in ($buyer_id)"; }else {$buyer_cond="";}

    $invoice_date_cond = " AND a.EX_FACTORY_DATE between '".$date_from."' and '".$date_to."'";
    $submit_date_cond = " AND a.SUBMIT_DATE between '".$date_from."' and '".$date_to."'";
    $received_date_cond = " AND b.RECEIVED_DATE between '".$date_from."' and '".$date_to."'";

    $buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
    $lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

     $DocToBank="SELECT a.COMPANY_ID, a.BANK_REF_NO, a.BUYER_ID, a.ID, sum(b.NET_INVO_VALUE) as bill_value FROM com_export_doc_submission_mst  a, com_export_doc_submission_invo b where a.id=b.DOC_SUBMISSION_MST_ID and a.ENTRY_FORM=40  $buyer_cond and a.COMPANY_ID=$company_id group by a.COMPANY_ID, a.BANK_REF_NO, a.BUYER_ID, a.ID";
     $other_document_sub_arr = sql_select($DocToBank);

     $bill_arr=array();
     foreach($other_document_sub_arr as $row){
        $bill_arr[$row["ID"]]["BANK_REF_NO"]=$row["BANK_REF_NO"];
        $bill_arr[$row["ID"]]["BILL_VALUE"]+=$row["BILL_VALUE"];
     }
     
    $dtls_sql_distribute="SELECT b.INVOICE_BILL_ID, b.BENIFICIARY_ID, b.BUYER_ID, A.ACCOUNT_HEAD,
    SUM(A.DOCUMENT_CURRENCY) AS  DOCUMENT_CURRENCY,
    SUM(CASE WHEN A.ACCOUNT_HEAD not in(62,63,64,137,136,146,149,194)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS TOTAL_REALIZATION_VAL
    from com_export_proceed_rlzn_dtls a, com_export_proceed_realization b where  a.mst_id=b.id and b.BENIFICIARY_ID in ($company_id) and b.buyer_partial_rlz=0 and A.ACCOUNT_HEAD not in(62,63,64,137,136,146,149,194) $received_date_cond $buyer_con and  a.status_active=1 and b.status_active=1 and type=0 group by b.INVOICE_BILL_ID, b.BENIFICIARY_ID, b.BUYER_ID, A.ACCOUNT_HEAD";

    $dtls_sql_deduct_realization = sql_select($dtls_sql_distribute);
    foreach($dtls_sql_deduct_realization as $row){

        $data_arrr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["BENIFICIARY_ID"]=$row["BENIFICIARY_ID"];
        $data_arrr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["BUYER_ID"]=$row["BUYER_ID"];
        $data_arrr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["ACCOUNT_HEAD"]=$row["ACCOUNT_HEAD"];
        $data_arrr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["INVOICE_BILL_ID"]=$row["INVOICE_BILL_ID"];
        $data_arrr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]]["TOTAL_REALIZATION_VAL"]+=$row["TOTAL_REALIZATION_VAL"];

        $data_short_realiz_arr[$row['BENIFICIARY_ID']][$row['BUYER_ID']][$row['ACCOUNT_HEAD']]['SHORT_VALUE']+=$row['DOCUMENT_CURRENCY'];
        $short_realization_arr[$row['ACCOUNT_HEAD']]['ACCOUNT_HEAD']=$row['ACCOUNT_HEAD'];
		//$short_realization_arr[$row['ACCOUNT_HEAD']]['SHORT_VALUE']+=$row['DOCUMENT_CURRENCY'];     
    }
    $tbl_width=950+($z*100);

        ?>
        <fieldset style="width:<?=$tbl_width?>; margin: 0 auto;">
            <table cellpadding="0" width="<?=$tbl_width;?>" class="rpt_table" rules="all" border="1" align="left">
                <thead>
                    <tr>
                        <th width="40" style="word-break: break-all;">SL</th>
                        <th width="120" style="word-break: break-all;">Company</th>
                        <th width="100" style="word-break: break-all;">Buyer</th>
                        <th width="100" style="word-break: break-all;">Bill No</th>
                        <th width="100" style="word-break: break-all;">Bill Value</th>
                    <?
                    foreach($short_realization_arr as $row){?>
                        <th width="100" style="word-break: break-all;"><? echo $commercial_head[$row['ACCOUNT_HEAD']]; ?></th>
                      <?
                      $z++;
                    }?>  
                       <th width="80" style="word-break: break-all;">Total Deduction</th>                     
                </thead>
                <tbody>
                <?
                $i = 1;
                $tot_bill_qty = 0;
                $tot_amount = 0;
                foreach ($data_arrr as $com_key=>$buyer_data_arr) {   
                    foreach($buyer_data_arr as $row){                                   
                          ?>
                        <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $i; ?>">     
                            <td width="40" align="center" style="word-break: break-all;"><? echo $i; ?></td>
                            <td width="120" align="ricenterght" style="word-break: break-all;"><? echo $lib_company_arr[$row[csf('benificiary_id')]]; ?></td>
                            <td width="100" align="center" style="word-break: break-all;"><? echo $buyer_arr[$row['BUYER_ID']]; ?></td>
                            <td width="100" align="center" style="word-break: break-all;"><?  echo  $bill_arr[$row["INVOICE_BILL_ID"]]["BANK_REF_NO"]; ?></td>
                            <td width="100" align="right" style="word-break: break-all;"><?  echo number_format($bill_arr[$row["INVOICE_BILL_ID"]]["BILL_VALUE"],2); ?></td>
                            <?foreach($short_realization_arr as $val){
                            ?>
                            <td width="100" align="right" style="word-break: break-all;"><?  echo number_format($data_short_realiz_arr[$row['BENIFICIARY_ID']][$row['BUYER_ID']][$val['ACCOUNT_HEAD']]['SHORT_VALUE'],2); ?></td>   
                            <?
                            $total_short_realization_arr[$val['ACCOUNT_HEAD']]+=$data_short_realiz_arr[$row['BENIFICIARY_ID']][$row['BUYER_ID']][$val['ACCOUNT_HEAD']]['SHORT_VALUE'];
                            }
                            ?>
                            <td width="100" align="right" style="word-break: break-all;"><?  echo number_format($row["TOTAL_REALIZATION_VAL"],2); ?></td>
                            <?
                    }
                        ?>                                    
                    </tr>
                    <?
                    $i++;
                    $total_bill_value+=$bill_arr[$row["INVOICE_BILL_ID"]]["BILL_VALUE"];
                    $total_realization_val+=$row["TOTAL_REALIZATION_VAL"];
                  
                    }
                    ?>                           
                </tbody>
                <tfoot>
                    <th colspan="4" align="right">Total&nbsp;</th>
                    <th width="100" align="right"><? echo number_format($total_bill_value,2); ?></th>
                    <?
						foreach($short_realization_arr as $val)
						{
							?>
								<th ><?=number_format($total_short_realization_arr[$val['ACCOUNT_HEAD']],2);?></th> 
							<?
						}
					?>   
                     <th width="100" align="right"><? echo number_format($total_realization_val,2); ?></th>        
                </tfoot>
            </table>
        </fieldset>
    </div>
    </div> 
    <?
}

if($action=="didaction_ammount_usd")
{
    echo load_html_head_contents("Details","../../../", 1, 1, $unicode,'','');
    extract($_REQUEST);
    $data=explode('*',$datas);
    $company_id = $data[0];
    $buyer_id = $data[1];
    $date_from = $data[2];
    $date_to = $data[3];
   
    if($buyer_id>0) {$buyer_cond=" and a.buyer_id in ($buyer_id)"; }else {$buyer_cond="";}
    if($buyer_id>0) {$buyer_con=" and b.buyer_id in ($buyer_id)"; }else {$buyer_cond="";}

    $invoice_date_cond = " AND a.EX_FACTORY_DATE between '".$date_from."' and '".$date_to."'";
    $submit_date_cond = " AND a.SUBMIT_DATE between '".$date_from."' and '".$date_to."'";
    $received_date_cond = " AND b.RECEIVED_DATE between '".$date_from."' and '".$date_to."'";

    $buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
    $lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

    $lc_sc_invoice="SELECT a.id as invoice_id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id as LC_SC_ID,a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.BONUS_AMMOUNT FROM com_export_invoice_ship_mst  a, com_export_lc b, com_export_invoice_ship_dtls c where a.lc_sc_id=b.id and c.mst_id=a.id and a.IS_LC=1 $invoice_date_cond and a.benificiary_id=$company_id $buyer_cond  group by a.id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.bonus_ammount HAVING 
    SUM(CASE WHEN a.discount_ammount > 0 OR a.claim_ammount > 0 OR a.other_discount_amt > 0 OR a.BONUS_AMMOUNT > 0 THEN 1 ELSE 0 END) > 0
    union all 
    SELECT a.id as invoice_id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id as LC_SC_ID, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.BONUS_AMMOUNT FROM com_export_invoice_ship_mst  a, com_sales_contract b, com_export_invoice_ship_dtls c where a.lc_sc_id=b.id and c.mst_id=a.id and a.IS_LC=2 $invoice_date_cond and a.benificiary_id=$company_id $buyer_cond group by a.id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value,  b.id, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.bonus_ammount
    HAVING 
    SUM( CASE WHEN a.discount_ammount > 0 OR a.claim_ammount > 0 OR a.other_discount_amt > 0 OR a.BONUS_AMMOUNT > 0 THEN 1 ELSE 0 END) > 0";
    $export_invoice = sql_select($lc_sc_invoice);
    ?>
    <div style="width:1090px;" id="report_div">
        <fieldset style="width:1090px; margin: 0 auto;">
            <table cellpadding="0" width="1090" class="rpt_table" rules="all" border="1" align="left">
                <thead>
                    <tr>
                        <th width="40" style="word-break: break-all;">SL</th>
                        <th width="120" style="word-break: break-all;">Company</th>
                        <th width="100" style="word-break: break-all;">Buyer</th>
                        <th width="100" style="word-break: break-all;">Invoice No.</th>
                        <th width="100" style="word-break: break-all;">Invoice value (Gross)</th>
                        <th width="100" style="word-break: break-all;">Discount Amount</th>
                        <th width="100" style="word-break: break-all;">Inspection Amount Head</th>
                        <th width="100" style="word-break: break-all;">Claim Amount</th>                        
                        <th width="100" style="word-break: break-all;">Other Deduction Amount</th>                        
                        <th width="100" style="word-break: break-all;">Total Deduction</th>                        
                    </tr>
                </thead>
            </table>
            <div style="width:1090px; max-height:310px; overflow-y:scroll" id="scroll_body" align="left">
            <table cellpadding="0" width="1090" class="rpt_table" rules="all" border="1" id="html_search">
                <tbody>
                <?
                $i = 1;
                $tot_bill_qty = 0;
                $tot_amount = 0;
                foreach ($export_invoice as $row) { 
                    $didaction_ammount=$row[csf('discount_ammount')]+$row[csf('bonus_ammount')]+$row[csf('claim_ammount')]+$row[csf('other_discount_amt')];
                ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $i; ?>">     
                        <td width="40" align="right" style="word-break: break-all;"><? echo $i; ?></td>
                        <td width="120" align="right" style="word-break: break-all;"><? echo $lib_company_arr[$row[csf('benificiary_id')]]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $row[csf('invoice_no')]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $row[csf('invoice_value')]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $row[csf('discount_ammount')]; ?></td>
            
                        <td width="100" align="right" style="word-break: break-all;"><? echo $row[csf('bonus_ammount')]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><? echo $row[csf('claim_ammount')]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><? echo $row[csf('other_discount_amt')]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><? echo $didaction_ammount; ?></td> 
                    </tr>
                <?
                $i++;
                $total_inv_value+=$row[csf('invoice_value')];
                $total_discount_ammount+=$row[csf('discount_ammount')];
                $total_bonus_ammount+=$row[csf('bonus_ammount')];
                $total_claim_ammount+=$row[csf('claim_ammount')];
                $total_other_discount_amt+=$row[csf('other_discount_amt')];
                $total_didaction_ammount+=$didaction_ammount;
                }
                ?>                           
                </tbody>
                <tfoot>
                    <th colspan="4" align="right">Total&nbsp;</th>
                    <th width="100" align="right"><? echo number_format($total_inv_value,2); ?></th>
                    <th width="100" align="right"><? echo number_format($total_discount_ammount,2); ?></th>
                    <th width="100" align="right"> <? echo number_format($total_bonus_ammount,2); ?></th>
                    <th width="100" align="right"> <? echo number_format($total_claim_ammount,2); ?></th>
                    <th width="100" align="right"> <? echo number_format($total_other_discount_amt,2); ?></th>
                    <th width="100" align="right"> <? echo number_format($total_didaction_ammount,2); ?></th>
                </tfoot>
            </table>
        </fieldset>
        <br> <br>
        <?  
     $DocToBank="SELECT a.COMPANY_ID, a.BANK_REF_NO, a.BUYER_ID, a.ID, sum(b.NET_INVO_VALUE) as bill_value FROM com_export_doc_submission_mst  a, com_export_doc_submission_invo b where a.id=b.DOC_SUBMISSION_MST_ID and a.ENTRY_FORM=40  and a.COMPANY_ID=$company_id group by a.COMPANY_ID, a.BANK_REF_NO, a.BUYER_ID, a.ID";
     $document_sub_arr = sql_select($DocToBank);

     $bank_ref_arr=array();
     foreach($document_sub_arr as $row){
        $bank_ref_arr[$row["COMPANY_ID"]][$row["BUYER_ID"]][$row["ID"]]["BANK_REF_NO"]=$row["BANK_REF_NO"];
        $bank_ref_arr[$row["COMPANY_ID"]][$row["BUYER_ID"]][$row["ID"]]["BILL_VALUE"]+=$row["BILL_VALUE"];
     }

   
     $dtls_sql_distribute="SELECT b.INVOICE_BILL_ID, b.BENIFICIARY_ID, b.BUYER_ID, 
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(64)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS  PENALTY_ON_GOODS_DISCREPANCY,
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(63)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS PENALTY_ON_DOC_DISCREPANCY,
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(149)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS BUYER_DISCRIPENCY_FEE,
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(146)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS LATE_INSPECTION_PENALTY,
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(137)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS LATE_PRESENTATION_CHARGES,
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(136)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS LATE_SHIPMENT_PENALTY,
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(63,64,137,136,146,149)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS TOTAL_DEDUCTION
     from com_export_proceed_rlzn_dtls a, com_export_proceed_realization b where  a.mst_id=b.id and b.BENIFICIARY_ID in ($company_id) and b.buyer_partial_rlz=0 $received_date_cond $buyer_con and  a.status_active=1 and b.status_active=1 and type=0 group by b.INVOICE_BILL_ID, b.BENIFICIARY_ID, b.BUYER_ID
     HAVING 
     SUM(CASE WHEN A.ACCOUNT_HEAD IN (64, 63, 149, 146, 137, 136) THEN A.DOCUMENT_CURRENCY ELSE 0 END) > 0 
     union all
     SELECT b.INVOICE_BILL_ID, b.BENIFICIARY_ID, b.BUYER_ID, 
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(64)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS  PENALTY_ON_GOODS_DISCREPANCY,
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(63)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS PENALTY_ON_DOC_DISCREPANCY,
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(149)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS BUYER_DISCRIPENCY_FEE,
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(146)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS LATE_INSPECTION_PENALTY,
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(137)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS LATE_PRESENTATION_CHARGES,
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(136)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS LATE_SHIPMENT_PENALTY,
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(63,64,137,136,146,149)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS TOTAL_DEDUCTION
     from com_export_proceed_rlzn_dtls a, com_export_proceed_realization b where a.mst_id=b.id and b.BENIFICIARY_ID in ($company_id) and a.status_active=1 and b.status_active=1 and b.buyer_partial_rlz=1 $received_date_cond $buyer_con and type=0 group by b.INVOICE_BILL_ID, b.BENIFICIARY_ID, b.BUYER_ID
     HAVING 
    SUM(CASE WHEN A.ACCOUNT_HEAD IN (64, 63, 149, 146, 137, 136) THEN A.DOCUMENT_CURRENCY ELSE 0 END) > 0";
 
     $dtls_sql_deduct_realization = sql_select($dtls_sql_distribute);
    
        ?>
        <fieldset style="width:1090px; margin: 0 auto;">
            <table cellpadding="0" width="1090" class="rpt_table" rules="all" border="1" align="left">
                <thead>
                    <tr><th colspan="12">From Realization</th></tr>
                    <tr>
                        <th width="40" style="word-break: break-all;">SL</th>
                        <th width="120" style="word-break: break-all;">Company</th>
                        <th width="100" style="word-break: break-all;">Buyer</th>
                        <th width="100" style="word-break: break-all;">Bill No</th>
                        <th width="100" style="word-break: break-all;">Bill Value</th>
                        <th width="100" style="word-break: break-all;">Penalty on Goods Discrepancy</th>
                        <th width="100" style="word-break: break-all;">Penalty on Doc Discrepancy</th>
                        <th width="100" style="word-break: break-all;">Buyer Discripency Fee</th>                        
                        <th width="100" style="word-break: break-all;">Late Inspection penalty</th>                        
                        <th width="100" style="word-break: break-all;">Late presentation charges</th>                        
                        <th width="100" style="word-break: break-all;">Late shipment penalty</th>                        
                        <th width="100" style="word-break: break-all;">Total Deduction</th>                        
                    </tr>
                </thead>
            </table>
            <div style="width:1090px; max-height:310px; overflow-y:scroll" id="scroll_body" align="left">
            <table cellpadding="0" width="1090" class="rpt_table" rules="all" border="1" id="html_search">
                <tbody>
                <?
                $i = 1;
                $tot_bill_qty = 0;
                $tot_amount = 0;
                foreach ($dtls_sql_deduct_realization as $row) { 

                    $bank_ref_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]][$row["INVOICE_BILL_ID"]]["BANK_REF_NO"]

                     ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $i; ?>">     
                        <td width="40" align="right" style="word-break: break-all;"><? echo $i; ?></td>
                        <td width="120" align="right" style="word-break: break-all;"><? echo $lib_company_arr[$row[csf('benificiary_id')]]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $bank_ref_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]][$row["INVOICE_BILL_ID"]]["BANK_REF_NO"]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $bank_ref_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]][$row["INVOICE_BILL_ID"]]["BILL_VALUE"]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $row["PENALTY_ON_GOODS_DISCREPANCY"]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><? echo $row["PENALTY_ON_DOC_DISCREPANCY"]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><? echo $row["BUYER_DISCRIPENCY_FEE"]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><? echo $row["LATE_INSPECTION_PENALTY"]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><? echo $row["LATE_PRESENTATION_CHARGES"]; ?></td> 
                        <td width="100" align="right" style="word-break: break-all;"><? echo $row["LATE_SHIPMENT_PENALTY"]; ?></td> 
                        <td width="100" align="right" style="word-break: break-all;"><? echo $row["TOTAL_DEDUCTION"]; ?></td> 
                    </tr>
                <?
                $i++;
                $total_bill_value+=$bank_ref_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]][$row["INVOICE_BILL_ID"]]["BILL_VALUE"];
                $total_planty_on_good+=$row["PENALTY_ON_GOODS_DISCREPANCY"];
                $total_planty_on_doc+=$row["PENALTY_ON_DOC_DISCREPANCY"];
                $total_buyer_discripency_fee+=$row["BUYER_DISCRIPENCY_FEE"];
                $total_late_inspection_penalty+=$row["LATE_INSPECTION_PENALTY"];
                $total_late_presentation_charges+=$row["LATE_PRESENTATION_CHARGES"];
                $total_late_shipment_penalty+=$row["LATE_SHIPMENT_PENALTY"];
                $total_didaction+=$row["TOTAL_DEDUCTION"];
                }
                ?>                           
                </tbody>
                <tfoot>
                    <th colspan="4" align="right">Total&nbsp;</th>
                    <th width="100" align="right"><? echo number_format($total_bill_value,2); ?></th>
                    <th width="100" align="right"><? echo number_format($total_planty_on_good,2); ?></th>
                    <th width="100" align="right"> <? echo number_format($total_planty_on_doc,2); ?></th>
                    <th width="100" align="right"> <? echo number_format($total_buyer_discripency_fee,2); ?></th>
                    <th width="100" align="right"> <? echo number_format($total_late_inspection_penalty,2); ?></th>
                    <th width="100" align="right"> <? echo number_format($total_late_presentation_charges,2); ?></th>
                    <th width="100" align="right"> <? echo number_format($total_late_shipment_penalty,2); ?></th>
                    <th width="100" align="right"> <? echo number_format($total_didaction,2); ?></th>
                </tfoot>
            </table>
        </fieldset>
    </div>
    <?
    exit();	
}

if($action=="rebate_pop_up")
{
    echo load_html_head_contents("Details","../../../", 1, 1, $unicode,'','');
    extract($_REQUEST);
    $data=explode('*',$datas);
    $company_id = $data[0];
    $buyer_id = $data[1];
    $date_from = $data[2];
    $date_to = $data[3];
   
    if($buyer_id>0) {$buyer_cond=" and a.buyer_id in ($buyer_id)"; }else {$buyer_cond="";}
    if($buyer_id>0) {$buyer_con=" and b.buyer_id in ($buyer_id)"; }else {$buyer_cond="";}

    $invoice_date_cond = " AND a.EX_FACTORY_DATE between '".$date_from."' and '".$date_to."'";
    $submit_date_cond = " AND a.SUBMIT_DATE between '".$date_from."' and '".$date_to."'";
    $received_date_cond = " AND b.RECEIVED_DATE between '".$date_from."' and '".$date_to."'";

    $buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
    $lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

    $lc_sc_invoice="SELECT a.id as invoice_id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id as LC_SC_ID,a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.BONUS_AMMOUNT, a.COMMISSION, a.ATSITE_DISCOUNT_AMT FROM com_export_invoice_ship_mst  a, com_export_lc b, com_export_invoice_ship_dtls c where a.lc_sc_id=b.id and c.mst_id=a.id and a.IS_LC=1 $invoice_date_cond and a.benificiary_id=$company_id $buyer_cond  group by a.id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.bonus_ammount,a .COMMISSION, a.ATSITE_DISCOUNT_AMT
    union all 
    SELECT a.id as invoice_id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value, b.id as LC_SC_ID, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.BONUS_AMMOUNT, a.COMMISSION, a.ATSITE_DISCOUNT_AMT FROM com_export_invoice_ship_mst  a, com_sales_contract b, com_export_invoice_ship_dtls c where a.lc_sc_id=b.id and c.mst_id=a.id and a.IS_LC=2 $invoice_date_cond and a.benificiary_id=$company_id $buyer_cond group by a.id, a.invoice_no, a.benificiary_id, a.buyer_id, a.invoice_quantity, a.invoice_value,  b.id, a.discount_ammount, a.claim_ammount, a.other_discount_amt, a.bonus_ammount, a.COMMISSION, a.ATSITE_DISCOUNT_AMT";
    $export_invoice = sql_select($lc_sc_invoice);
    ?>
    <div style="width:790px;" id="report_div">
        <fieldset style="width:790px; margin: 0 auto;">
            <table cellpadding="0" width="790" class="rpt_table" rules="all" border="1" align="left">
                <thead>
                    <tr>
                        <th width="30" style="word-break: break-all;">SL</th>
                        <th width="120" style="word-break: break-all;">Company</th>
                        <th width="100" style="word-break: break-all;">Buyer</th>
                        <th width="100" style="word-break: break-all;">Invoice No.</th>
                        <th width="100" style="word-break: break-all;">Invoice value (Gross)</th>
                        <th width="100" style="word-break: break-all;">Commission Amount</th>                   
                    </tr>
                </thead>
            </table>
            <div style="width:790px; max-height:310px; overflow-y:scroll" id="scroll_body" align="left">
            <table cellpadding="0" width="790" class="rpt_table" rules="all" border="1" id="html_search">
                <tbody>
                <?
                $i = 1;
                $tot_bill_qty = 0;
                $tot_amount = 0;
                foreach ($export_invoice as $row) { 
                    $didaction_ammount=$row[csf('discount_ammount')]+$row[csf('bonus_ammount')]+$row[csf('claim_ammount')]+$row[csf('other_discount_amt')];
                ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $i; ?>">     
                        <td width="30" align="center" style="word-break: break-all;"><? echo $i; ?></td>
                        <td width="120" align="right" style="word-break: break-all;"><? echo $lib_company_arr[$row[csf('benificiary_id')]]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $row[csf('invoice_no')]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $row[csf('invoice_value')]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $row[csf('commission')]; ?></td>
                    </tr>
                <?
                $i++;
                $total_inv_value+=$row[csf('invoice_value')];
                $total_commission+=$row[csf('commission')];
                }
                ?>                           
                </tbody>
                <tfoot>
                    <th colspan="4" align="right">Total&nbsp;</th>
                    <th width="100" align="right"><? echo number_format($total_inv_value,2); ?></th>
                    <th width="100" align="right"><? echo number_format($total_commission,2); ?></th>
                </tfoot>
            </table>
        </fieldset>
        <br> <br>
        <?  
     $DocToBank="SELECT a.COMPANY_ID, a.BANK_REF_NO, a.BUYER_ID, a.ID, sum(b.NET_INVO_VALUE) as bill_value FROM com_export_doc_submission_mst  a, com_export_doc_submission_invo b where a.id=b.DOC_SUBMISSION_MST_ID and a.ENTRY_FORM=40  $buyer_cond and a.COMPANY_ID=$company_id group by a.COMPANY_ID, a.BANK_REF_NO, a.BUYER_ID, a.ID";
     $document_sub_arr = sql_select($DocToBank);

     $bank_ref_arr=array();
     foreach($document_sub_arr as $row){
        $bank_ref_arr[$row["COMPANY_ID"]][$row["BUYER_ID"]][$row["ID"]]["BANK_REF_NO"]=$row["BANK_REF_NO"];
        $bank_ref_arr[$row["COMPANY_ID"]][$row["BUYER_ID"]][$row["ID"]]["BILL_VALUE"]+=$row["BILL_VALUE"];

     }


     //Export Proceeds Realization with Realization Partial Partial
     $dtls_sql_distribute="SELECT b.INVOICE_BILL_ID, b.BENIFICIARY_ID, b.BUYER_ID, 
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(62)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS LOCAL_COMMISSION
     from com_export_proceed_rlzn_dtls a, com_export_proceed_realization b where  a.mst_id=b.id and b.BENIFICIARY_ID in ($company_id) and b.buyer_partial_rlz=0 $received_date_cond $buyer_con and  a.status_active=1 and b.status_active=1 and type=0 group by b.INVOICE_BILL_ID, b.BENIFICIARY_ID, b.BUYER_ID
     union all
     SELECT b.INVOICE_BILL_ID, b.BENIFICIARY_ID, b.BUYER_ID, 
     SUM(CASE WHEN A.ACCOUNT_HEAD IN(62)  THEN A.DOCUMENT_CURRENCY ELSE 0 END) AS LOCAL_COMMISSION
     from com_export_proceed_rlzn_dtls a, com_export_proceed_realization b where a.mst_id=b.id and b.BENIFICIARY_ID in ($company_id) and a.status_active=1 and b.status_active=1 and b.buyer_partial_rlz=1 $received_date_cond $buyer_con and type=0 group by b.INVOICE_BILL_ID, b.BENIFICIARY_ID, b.BUYER_ID";

     $dtls_sql_deduct_realization = sql_select($dtls_sql_distribute);

        ?>
        <fieldset style="width:790px; margin: 0 auto;">
            <table cellpadding="0" width="790" class="rpt_table" rules="all" border="1" align="left">
                <thead>
                    <tr><th colspan="12">From Realization</th></tr>
                    <tr>
                        <th width="30" style="word-break: break-all;">SL</th>
                        <th width="120" style="word-break: break-all;">Company</th>
                        <th width="100" style="word-break: break-all;">Buyer</th>
                        <th width="100" style="word-break: break-all;">Bill No</th>
                        <th width="100" style="word-break: break-all;">Bill Value</th>
                        <th width="100" style="word-break: break-all;">Buying House Commision</th>                      
                    </tr>
                </thead>
            </table>
            <div style="width:790px; max-height:310px; overflow-y:scroll" id="scroll_body" align="left">
            <table cellpadding="0" width="790" class="rpt_table" rules="all" border="1" id="html_search">
                <tbody>
                <?
                $i = 1;
                $tot_bill_qty = 0;
                $tot_amount = 0;
                foreach ($dtls_sql_deduct_realization as $row) { 

                    // echo $row["INVOICE_BILL_ID"]."__";

                     ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $i; ?>">     
                        <td width="30" align="center" style="word-break: break-all;"><? echo $i; ?></td>
                        <td width="120" align="center" style="word-break: break-all;"><? echo $lib_company_arr[$row[csf('benificiary_id')]]; ?></td>
                        <td width="100" align="center" style="word-break: break-all;"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                        <td width="100" align="center" style="word-break: break-all;"><?  echo $bank_ref_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]][$row["INVOICE_BILL_ID"]]["BANK_REF_NO"]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $bank_ref_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]][$row["INVOICE_BILL_ID"]]["BILL_VALUE"]; ?></td>
                        <td width="100" align="right" style="word-break: break-all;"><?  echo $row["LOCAL_COMMISSION"]; ?></td>
                    </tr>
                <?
                $i++;
                $total_bill_value+=$bank_ref_arr[$row["BENIFICIARY_ID"]][$row["BUYER_ID"]][$row["INVOICE_BILL_ID"]]["BILL_VALUE"];
                $total_local_commission+=$row["LOCAL_COMMISSION"];
                }
                ?>                           
                </tbody>
                <tfoot>
                    <th colspan="4" align="right">Total&nbsp;</th>
                    <th width="100" align="right"><? echo number_format($total_bill_value,2); ?></th>
                    <th width="100" align="right"><? echo number_format($total_local_commission,2); ?></th>
                </tfoot>
            </table>
        </fieldset>
    </div>
    <?
    exit();	  
}