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

if ($action=="load_drop_down_forwarder")
{
    echo create_drop_down( "cbo_forwarder_name", 150, "SELECT a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.status_active =1 and a.is_deleted=0 and b.supplier_id=a.id and b.tag_company='$data' and a.id in (SELECT supplier_id from lib_supplier_party_type where party_type in (30,31,32)) group by a.id, a.supplier_name order by supplier_name","id,supplier_name", 1, "--- Select Forwarder ---", $selected, "" );
	exit();
}

if($action=="report_generate")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $cbo_company_name   = str_replace("'","",$cbo_company_name);  
    $cbo_buyer_name     = str_replace("'","",$cbo_buyer_name);     
    $cbo_forwarder_name = str_replace("'","",$cbo_forwarder_name);       
    $txt_invoice_no     = str_replace("'","",$txt_invoice_no);       
    $cbo_year_selection = str_replace("'","",$cbo_year_selection);    
    $txt_date_from      = str_replace("'","",$txt_date_from);    
    $txt_date_to        = str_replace("'","",$txt_date_to); 
    $based_on       = str_replace("'","",$cbo_based_on);   
    $buyer_arr = return_library_array("select id,buyer_name from lib_buyer where is_deleted=0","id","buyer_name");
    $supplier_arr = return_library_array("select id,supplier_name from lib_supplier where is_deleted=0","id","supplier_name");

    if ($cbo_company_name!=0) {$company_id=" and a.company_id=$cbo_company_name";} else { echo "Please Select Company First."; die;}
    if ($cbo_buyer_name!=0) {$buyer_id=" and b.buyer_id=$cbo_buyer_name";} else { $buyer_id="";}
    if ($cbo_forwarder_name!=0) {$forwarder_id=" and b.forwarder_name=$cbo_forwarder_name";} else { $forwarder_id="";}
    if ($txt_invoice_no!='') {$invoice_no=" and b.invoice_no='$txt_invoice_no'";} else { $invoice_no="";}
    if(!$based_on)
    {
        if ($txt_date_from != '' && $txt_date_to != '') 
        {
            if ($db_type == 0) 
            {
                $date_cond = "and a.bl_charge_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
            } else if ($db_type == 2) {
                $date_cond = "and a.bl_charge_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
            }	
        } 
        else 
        {
            if($db_type==0){$date_cond=" and year(a.bl_charge_date)=".$cbo_year_selection."";}
            else{$date_cond=" and to_char(a.bl_charge_date,'YYYY')=".$cbo_year_selection."";}
        }
    }
    if ($txt_date_from != '' && $txt_date_to != '') 
	{
		if($based_on==1){
        if ($db_type == 0) {
            $date_cond_bill = "and b.ex_factory_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond_bill = "and b.ex_factory_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
		}
		}
		if($based_on==2){
        if ($db_type == 0) {
            $date_cond_bill = "and a.bl_date  between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond_bill = "and a.bl_date  between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
		}
        
		}
        if($based_on==3){
            if ($db_type == 0) {
                $date_cond_bill = "and b.invoice_date  between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
            } else if ($db_type == 2) {
                $date_cond_bill = "and b.invoice_date  between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
            }
            
            }
    } 
    else 
    {
        $date_cond_bill = '';
	}
    $mst_sql="SELECT a.id as ID,a.sys_number as SYS_NUMBER, a.company_id as COMPANY_ID,a.invoice_id as INVOICE_ID,a.bl_charge_date as BL_CHARGE_DATE, a.remarks as REAMRKS, a.bl_charge as BL_CHARGE,a.adjustment_charge AS ADJUSTMENT_CHARGE,a.surrendered_charge AS SURRENDERED_CHARGE,a.special_charge as SPECIAL_CHARGE, a.stamp_charge as STAMP_CHARGE, a.air_company_charge as AIR_COMPANY_CHARGE, a.air_buyer_charge as AIR_BUYER_CHARGE, a.others_charge as OTHERS_CHARGE,b.buyer_id as BUYER_ID,b.invoice_no as INVOICE_NO,b.invoice_date as INVOICE_DATE,invoice_value as INVOICE_VALUE,b.invoice_quantity as INVOICE_QUANTITY,b.bl_no as BL_NO,b.bl_date as BL_DATE,b.bl_rev_date as BL_REV_DATE,b.forwarder_name as FORWARDER_NAME,b.shipping_mode as SHIPPING_MODE,b.ex_factory_date as EX_FACTORY_DATE ,a.bl_date
    from bl_charge a, com_export_invoice_ship_mst b 
    where a.invoice_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $buyer_id $forwarder_id $invoice_no $date_cond $date_cond_bill";
   //  echo $mst_sql;
    $mst_sql_result=sql_select($mst_sql);
    unset($mst_sql);
    ob_start();
    ?>
        <div style="width:1880px;">
            <table width="1880" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th colspan="12">Information</th>
                        <th colspan="9">BL Charge Amount TK</th>
                    </tr>
                    <tr>
                        <th width="30">Sl No</th>
                        <th width="80">Invoice No.</th>
                        <th width="100">Invoice Date</th>
                        <th width="100">Forwarder Name</th>
                        <th width="100">Invoice Qnty. Pcs</th>
                        <th width="100">Invoice value</th>
                        <th width="100">Buyer Name</th>
                        <th width="100">Ship Mode</th>
                        <th width="100">Ex-Factory Date</th>
                        <th width="80">Copy B/L No</th>
                        <th width="80">Copy B/L Date</th>
                        <th width="80">Org B/L Rcv Date</th>
                        <th width="80">B/ L Charge</th>
                        <th width="80">Stamp Charge</th>
                        <th width="100" style="word-break:break-all;" >Air Freight Charge - Company</th>
                        <th width="80" style="word-break:break-all;" >Air Freight Charge -Buyer</th>
                        <th width="80"  style="word-break:break-all;" >Freight Adjustment/Local Charges</th>
                        <th width="80">MBL Surrendered Fee</th>
                        <th width="80">Special Permission</th>
                        <th width="80">Others</th>
                        <th >Total Amount TK</th>
                    </tr>
                </thead>
            </table>
            <div style="max-height: 380px; overflow-y:scroll;width:1880px" >
			<table width="1880" class="rpt_table" rules="all" border="1" id="table_body"  >
                <tbody >
                    <?
                        $i=1;
                        foreach($mst_sql_result as $row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF";
                            else $bgcolor="#FFFFFF";
                            ?>
                                <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')"  id="tr_<? echo $i;?>">
                                    <td width="30"style="word-break:break-all;" align="center"><?=$i;?></td>
                                    <td width="80"style="word-break:break-all;"align="center"><? echo $row['INVOICE_NO'];?></td>
                                    <td width="100"style="word-break:break-all;"><? echo change_date_format($row['INVOICE_DATE']);?></td>
                                    <td width="100"style="word-break:break-all;"><? echo $supplier_arr[$row['FORWARDER_NAME']];?></td>
                                    <td width="100"style="word-break:break-all;"><? echo $row['INVOICE_QUANTITY'];?></td>
                                    <td width="100"style="word-break:break-all;" align="right"><? echo number_format($row['INVOICE_VALUE'],2);?></td>
                                    <td width="100"style="word-break:break-all;"><? echo $buyer_arr[$row['BUYER_ID']];?></td>
                                    <td width="100"style="word-break:break-all;"><? echo $shipment_mode[$row['SHIPPING_MODE']];?></td>
                                    <td width="100"style="word-break:break-all;"><? echo change_date_format($row['EX_FACTORY_DATE']);?></td>
                                    <td width="80"style="word-break:break-all;"><? echo $row['BL_NO'];?></td>
                                    <td width="80"style="word-break:break-all;"align="center"><? echo change_date_format($row['BL_DATE']);?></td>
                                    <td width="80"style="word-break:break-all;"align="center"><? echo change_date_format($row['BL_REV_DATE']);?></td>
                                    <td width="80"style="word-break:break-all;"align="right"><? echo number_format($row['BL_CHARGE'],2);
                                    $total_bl_charge+=$row['BL_CHARGE'];?></td>
                                    <td width="80"style="word-break:break-all;"align="right"><? echo number_format($row['STAMP_CHARGE'],2);
                                    $total_stamp_charge+=$row['STAMP_CHARGE'];?></td>
                                    <td width="100"style="word-break:break-all;"align="right"><? echo number_format($row['AIR_COMPANY_CHARGE'],2);
                                    $total_air_company_charge+=$row['AIR_COMPANY_CHARGE'];?></td>
                                    <td width="80"style="word-break:break-all;"align="right"><? echo number_format($row['AIR_BUYER_CHARGE'],2);
                                    $total_air_buyer_charge+=$row['AIR_BUYER_CHARGE'];?></td>

                                    <td width="80"style="word-break:break-all;"align="right"><? echo number_format($row['ADJUSTMENT_CHARGE'],2);
                                    $total_adjustment_charge+=$row['ADJUSTMENT_CHARGE'];?></td>

                                    <td width="80"style="word-break:break-all;"align="right"><? echo number_format($row['SURRENDERED_CHARGE'],2);
                                    $total_surrendered_charge+=$row['SURRENDERED_CHARGE'];?></td>

                                    <td width="80"style="word-break:break-all;"align="right"><? echo number_format($row['SPECIAL_CHARGE'],2);
                                    $total_special_charge+=$row['SPECIAL_CHARGE'];?></td>

                                    <td width="80" align="right"><? echo number_format($row['OTHERS_CHARGE'],2);
                                    $total_others_charge+=$row['OTHERS_CHARGE'];?></td>
                                    <td align="right"><? $grand_total+=$total_amount=$row['BL_CHARGE']+$row['STAMP_CHARGE']+$row['AIR_COMPANY_CHARGE']+$row['AIR_BUYER_CHARGE']+$row['OTHERS_CHARGE']+$row['SURRENDERED_CHARGE']+$row['ADJUSTMENT_CHARGE']+$row['SPECIAL_CHARGE']; echo number_format($total_amount,2);?></td>
                                </tr>
                            <?
                            $i++;
                        }
                    ?>
                </tbody>
                <tfoot>
                    <th colspan="11"></th>
                    <th><strong>Total</strong></th>
                    <th><strong><?echo number_format($total_bl_charge,2);?></strong></th>
                    <th><strong><?echo number_format($total_stamp_charge,2);?></strong></th>
                    <th><strong><?echo number_format($total_air_company_charge,2);?></strong></th>
                    <th><strong><?echo number_format($total_air_buyer_charge,2);?></strong></th>

                    <th><strong><?echo number_format($total_adjustment_charge,2);?></strong></th>
                    <th><strong><?echo number_format($total_surrendered_charge,2);?></strong></th>
                    <th><strong><?echo number_format($total_special_charge,2);?></strong></th>

                    <th><strong><?echo number_format($total_others_charge,2);?></strong></th>
                    <th><strong><?echo number_format($grand_total,2);?></strong></th>
                </tfoot>
            </table>
            </div>
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