<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($db_type==0)
{
    $select_year="year";
    $year_con="";
}
else
{
    $select_year="to_char";
    $year_con=",'YYYY'";
}


$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$user_name_arr=return_library_array( "select id, user_name from  user_passwd",'id','user_name');
$loan_party_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0 and id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name",'id','supplier_name');

if ($action=="load_drop_down_supplier")
{
    echo create_drop_down( "cbo_supplier_name", 130, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name","id,supplier_name",0, "-- Select --", 0, "",0 );
    exit();
}
if ($action=="eval_multi_select")
{
    echo "set_multiselect('cbo_supplier_name','0','0','','0');\n";
    exit();
}
//report generated here--------------------//
if($action=="generate_report")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $txt_challan_no=str_replace("'","",$txt_challan_no);
    $cbo_company_name=str_replace("'","",$cbo_company_name);
    $txt_brand=str_replace("'","",$txt_brand);
    $txt_yarn_count=str_replace("'","",$txt_yarn_count);
    $txt_date_from=str_replace("'","",$txt_date_from);
    $txt_date_to=str_replace("'","",$txt_date_to);

    $supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
    $yarn_pi_num_arr=return_library_array( "select id, pi_number from com_pi_master_details where item_category_id=1",'id','pi_number');

    $sqlCond = "";
    if($db_type==0)
    {
        if($txt_date_from!="" && $txt_date_to!=""){
            $sqlCond .=" and a.transaction_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' ";
        }
        $select_transaction_date="DATE_FORMAT(a.transaction_date,'%d-%m-%Y') as TRANSACTION_DATE";
    }else {
        if ($txt_date_from != "" && $txt_date_to != ""){
            $sqlCond .= " and a.transaction_date between '" . date("j-M-Y", strtotime($txt_date_from)) . "' and '" . date("j-M-Y", strtotime($txt_date_to)) . "' ";
        }
        $select_transaction_date=" to_char(a.transaction_date,'DD-MM-YYYY') as TRANSACTION_DATE";
    }

    if($txt_challan_no != "" ) $sqlCond .=" and c.issue_number_prefix_num = $txt_challan_no";

    if($txt_brand != "")
    {
        $sqlCond .=" and d.brand_name like '%$txt_brand%'";
    }

    if($txt_yarn_count != ""){
        $sqlCond .= " and e.yarn_count = '$txt_yarn_count'";
    }

    if($txt_yarn_lot != ""){
        $sqlCond .= " and b.lot = '$txt_yarn_lot'";
    }
    if($cbo_supplier_name != ""){
        $sqlCond .= " and c.supplier_id in ($cbo_supplier_name)";
    }

    $sqlReceiveReturn="select c.issue_number as ISSUE_NUMBER, a.id as TRANS_ID, $select_transaction_date, a.cons_quantity as RETURN_QTY,
       a.supplier_id as SUPPLIER_ID, e.yarn_count as YARN_COUNT, d.brand_name as BRAND_NAME, b.id as PROD_ID, c.pi_id as PI_ID,
       b.item_group_id as ITEM_GROUP_ID, b.sub_group_name as SUB_GROUP_NAME, b.item_description as ITEM_DESCRIPTION,
       b.product_name_details as PRODUCT_NAME_DETAILS, b.item_code as ITEM_CODE, b.item_size as ITEM_SIZE,
       b.model as MODEL, b.item_number as ITEM_NUMBER, a.cons_rate as CONS_RATE, a.cons_amount as CONS_AMOUNT,
       b.yarn_comp_percent1st as YARN_COMP_PERCENT1ST, b.yarn_comp_percent2nd as YARN_COMP_PERCENT2ND, c.received_id as RECEIVE_ID,
       b.yarn_comp_type1st as YARN_COMP_TYPE1ST, b.yarn_comp_type2nd as YARN_COMP_TYPE2ND, b.lot as LOT
    from inv_transaction a, product_details_master b left join lib_brand d on d.id = b.brand left join lib_yarn_count e on e.id = b.yarn_count_id, inv_issue_master c
	where a.prod_id = b.id and a.mst_id=c.id and c.entry_form = 8 and a.company_id=$cbo_company_name and a.transaction_type in (3) and a.status_active=1 
	  and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sqlCond order by c.id desc";
//    echo $sqlReceiveReturn;
//    die();
    $div_width="1220px";
    $table_width=1200;
    $sql_return_result=sql_select($sqlReceiveReturn);
    $returnDataArr = array();  $piID = array();

    foreach ($sql_return_result as $key => $returnData){
        $item_key = $returnData["ISSUE_NUMBER"]."*##*".$returnData["ITEM_DESCRIPTION"]."*##*".$returnData["YARN_COUNT"]."*##*".$returnData["BRAND_NAME"]."*##*".$returnData["LOT"];
        if($returnData["PI_ID"] > 0){
            array_push($piID, $returnData["PI_ID"]);
        }
        $returnDataArr[$returnData["SUPPLIER_ID"]]['key'] = $item_key;
        $returnDataArr[$returnData["SUPPLIER_ID"]]['item_data'][$item_key]['challan_no'] = $returnData["ISSUE_NUMBER"];
        $returnDataArr[$returnData["SUPPLIER_ID"]]['item_data'][$item_key]['date'] = $returnData["TRANSACTION_DATE"];
        $returnDataArr[$returnData["SUPPLIER_ID"]]['item_data'][$item_key]['count'] = $returnData["YARN_COUNT"];
        $returnDataArr[$returnData["SUPPLIER_ID"]]['item_data'][$item_key]['brand'] = $returnData["BRAND_NAME"];
        $parcent1st = "";
        if($returnData["YARN_COMP_PERCENT1ST"] > 0){
            $parcent1st = $returnData["YARN_COMP_PERCENT1ST"]."%";
        }
        $parcent2nd = "";
        if($returnData["YARN_COMP_PERCENT2ND"] > 0 ){
            $parcent2nd = $returnData["YARN_COMP_PERCENT2ND"]."%";
        }
        $returnDataArr[$returnData["SUPPLIER_ID"]]['item_data'][$item_key]['composition'] = $composition[$returnData["YARN_COMP_TYPE1ST"]].' '.$parcent1st.' '.$composition[$returnData["YARN_COMP_TYPE2ND"]].' '.$parcent2nd;
        $returnDataArr[$returnData["SUPPLIER_ID"]]['item_data'][$item_key]['lot'] = $returnData["LOT"];
        $returnDataArr[$returnData["SUPPLIER_ID"]]['item_data'][$item_key]['username'] = $user_arr[$returnData["INSERTED_BY"]];
        $returnDataArr[$returnData["SUPPLIER_ID"]]['item_data'][$item_key]['return_qty'] += $returnData["RETURN_QTY"];
        $returnDataArr[$returnData["SUPPLIER_ID"]]['item_data'][$item_key]['rate'][$key] = $returnData["CONS_RATE"];
        $returnDataArr[$returnData["SUPPLIER_ID"]]['item_data'][$item_key]['receive_id'] = $returnData["RECEIVE_ID"];
        $returnDataArr[$returnData["SUPPLIER_ID"]]['item_data'][$item_key]['pi'] = $returnData["PI_ID"];
    }

    $piIDUnique = array_chunk(array_unique($piID),999, true);
    $counter = false;
    $pi_id_cond = "";
    foreach ($piIDUnique as $key => $value){
        if($counter){
            $pi_id_cond .= " or b.pi_id in (".implode(',', $value).")";
        }else{
            $pi_id_cond .= " and b.pi_id in (".implode(',', $value).")";
        }
        $counter = true;
    }
    $lc_arr = return_library_array("select a.lc_number, b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $pi_id_cond", "pi_id", "lc_number");
    ob_start();
    ?>
    <div style="width:<? echo $div_width; ?>">
        <fieldset style="width:<? echo $div_width; ?>">
            <table width="<? echo $table_width; ?>" border="0" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left">
                <tr class="form_caption" style="border:none;">
                    <td colspan="12" align="center" style="border:none;font-size:16px; font-weight:bold; padding: 0px 2px;" ><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
                </tr>
                <tr style="border:none;">
                    <td colspan="12" align="center" style="border:none; font-size:13px; padding: 0px 2px;">
                        <?
                        echo rtrim(show_company($cbo_company_name, 1, array("plot_no" => "plot_no", "level_no" => "level_no", "road_no" => "road_no", "block_no" => "block_no", "city" => "city")),",");
                        ?>
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="12" align="center" style="border:none; font-size:14px; padding: 3px 2px;">
                        <strong>Date Range : <? echo $txt_date_from; ?> To <? echo $txt_date_to; ?></strong>
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="12" align="center" style="border:none; padding: 3px 2px;x">
                        <strong style="font-size:16px;">Yarn Return Report</strong>
                    </td>
                </tr>
            </table>
            <br/>
            <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"  align="left">
                <thead>
                <tr>
                    <th style="padding: 0px 2px;" width="30">SL</th>
                    <th style="padding: 0px 2px;" width="130">Supplier Name</th>
                    <th style="padding: 0px 2px;" width="110">Challan No</th>
                    <th style="padding: 0px 2px;" width="80">Date</th>
                    <th style="padding: 0px 2px;" width="110">L/C No.</th>
                    <th style="padding: 0px 2px;" width="80">Count</th>
                    <th style="padding: 0px 2px;" width="100">Brand</th>
                    <th style="padding: 0px 2px;" width="160">Composition</th>
                    <th style="padding: 0px 2px;" width="80">Lot No.</th>
                    <th style="padding: 0px 2px;" width="90">Qty. In KG</th>
                    <th style="padding: 0px 2px;" width="80">Rate</th>
                    <th style="padding: 0px 2px;" >Amount</th>

                </tr>
                </thead>
            </table>
            <br/>
            <div style="width:<? echo $div_width; ?>; overflow-y: scroll; max-height:260px;" id="scroll_body">
                <table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="1" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                    <tbody>
                        <?
                        $qtyGrandTotal = 0; $amountGrandTotal = 0; $counter = 0;
                        foreach ($returnDataArr as $supplier_id => $item_data){
                            $qtySubTotal = 0; $amountSubTotal = 0;
                            $counter++;
                            $rowspan = 0;
                            if($counter%2==0) $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                            ?>
                            <?

                            foreach ($item_data['item_data'] as $key => $value ){
                                $qty = $value['return_qty'];
                                $rate = array_sum($value['rate'])/count($value['rate']);
                                $amount = $qty*$rate;
                                $qtySubTotal += $qty;
                                $amountSubTotal += $amount;
                                if($rowspan > 0){
                                ?>
                                <tr bgcolor="<?=$bgcolor?>">
                                    <td style="padding: 0px 2px;" width="110" ><?=$value['challan_no']?></td>
                                    <td style="padding: 0px 2px;" width="80" align="center"><?=$value['date']?></td>
                                    <td style="padding: 0px 2px;" width="110"><?=$lc_arr[$value['pi']]?></td>
                                    <td style="padding: 0px 2px;" width="80" align="center"><?=$value['count']?></td>
                                    <td style="padding: 0px 2px;" width="100"><?=$value['brand']?></td>
                                    <td style="padding: 0px 2px;" width="160"><?=$value['composition']?></td>
                                    <td style="padding: 0px 2px;" width="80"><?=$value['lot']?></td>
                                    <td style="padding: 0px 2px;" width="90" align="right"><?=number_format($qty, 2)?></td>
                                    <td style="padding: 0px 2px;" width="80" align="right"><?=number_format($rate, 2)?></td>
                                    <td style="padding: 0px 2px;" align="right"><?=number_format($amount, 2)?></td>
                                </tr>
                                <?
                                }else{
                                    ?>
                                    <tr bgcolor="<?=$bgcolor?>">
                                        <td width="30" align="center" style="vertical-align: middle;padding: 0px 2px;" rowspan="<?=count($item_data['item_data'])?>"><?=$counter?></td>
                                        <td width="130" style="vertical-align: middle; padding: 0px 2px;" rowspan="<?=count($item_data['item_data'])?>"><?=$supplier_arr[$supplier_id]?></td>
                                        <td style="padding: 0px 2px;" width="110"><?=$value['challan_no']?></td>
                                        <td style="padding: 0px 2px;" width="80" align="center"><?=$value['date']?></td>
                                        <td style="padding: 0px 2px;" width="110"><?=$lc_arr[$value['pi']]?></td>
                                        <td style="padding: 0px 2px;" width="80" align="center"><?=$value['count']?></td>
                                        <td style="padding: 0px 2px;" width="100"><?=$value['brand']?></td>
                                        <td style="padding: 0px 2px;" width="160"><?=$value['composition']?></td>
                                        <td style="padding: 0px 2px;" width="80"><?=$value['lot']?></td>
                                        <td style="padding: 0px 2px;" width="90" align="right"><?=number_format($qty, 2)?></td>
                                        <td style="padding: 0px 2px;" width="80" align="right"><?=number_format($rate, 2)?></td>
                                        <td style="padding: 0px 2px;"  align="right"><?=number_format($amount, 2)?></td>
                                    </tr>
                                    <?
                                }
                                $rowspan++;
                            }
                            ?>
                            <tr><td colspan="12"></td></tr>
                            <tr bgcolor="#ffebcd">
                                <td style="padding: 0px 2px;" colspan="9" align="right"><strong>Sub Total</strong></td>
                                <td  style="padding: 0px 2px;" align="right" width="90"><strong><?=number_format($qtySubTotal, 2)?></strong></td>
                                <td  style="padding: 0px 2px;" align="right" width="80"><strong></strong></td>
                                <td  style="padding: 0px 2px;" align="right" ><strong><?=number_format($amountSubTotal, 2)?></strong></td>
                            </tr>
                            <tr><td colspan="12"></td></tr>
                        <?
                            $amountGrandTotal += $amountSubTotal;
                            $qtyGrandTotal += $qtySubTotal;
                        }
                        ?>
                        <tr><td colspan="12"></td></tr>
                        <tr bgcolor="#ffebcd">
                            <td style="padding: 0px 2px;" colspan="9" align="right"><strong>Grand Total</strong></td>
                            <td  style="padding: 0px 2px;" align="right" width="90"><strong><?=number_format($qtyGrandTotal, 2)?></strong></td>
                            <td  style="padding: 0px 2px;" align="right" width="80"><strong></strong></td>
                            <td  style="padding: 0px 2px;" align="right" ><strong><?=number_format($amountGrandTotal, 2)?></strong></td>
                        </tr>
                        <tr><td colspan="12"></td></tr>
                    </tbody>
                </table>
            </div>
        </fieldset>
    </div>
    <?

    foreach (glob("*.xls") as $filename) {
        if( @filemtime($filename) < (time()-$seconds_old) )
            @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    echo "$html**$filename**1";
    exit();

}

disconnect($con);
?>
