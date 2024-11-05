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

if ($action=="load_drop_down_store")
{
    echo create_drop_down( "cbo_store_name", 110, "select a.id, a.store_name from lib_store_location a,lib_store_location_category b  where a.id=b.store_location_id and a.company_id='$data' and b.category_type in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- ALL --", 0, "" );
    exit();
}

if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location_name", 110, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $company_location_credential_cond  order by location_name","id,location_name", 1, "-- ALL --", $selected, "" );
    exit();
}

if ($action=="load_drop_down_loan_party")
{
    echo create_drop_down( "cbo_party_name", 120, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b 
	where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name","id,supplier_name", 1, "-- ALL --", $selected, "","","" );
    exit();
}

//report generated here--------------------//
if($action=="generate_report")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $cbo_item_cat=str_replace("'","",$cbo_item_cat);
    $cbo_company_name=str_replace("'","",$cbo_company_name);
    $cbo_location_name=str_replace("'","",$cbo_location_name);
    $cbo_store_name=str_replace("'","",$cbo_store_name);
    $cbo_party_name=str_replace("'","",$cbo_party_name);
    $txt_date_from=str_replace("'","",$txt_date_from);
    $txt_date_to=str_replace("'","",$txt_date_to);
    $cbo_based_on=str_replace("'","",$cbo_based_on);
    $cbo_purpose=str_replace("'","",$cbo_purpose);

    $party_arr = return_library_array("select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b	where a.id=b.supplier_id and b.tag_company=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name", "id", "supplier_name");
    $item_group_arr = return_library_array("select id, item_name from lib_item_group where status_active = 1", "id", "item_name");
    $user_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name");

    if($db_type==0)
    {
        if($cbo_based_on==1)
        {
            if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $date_cond="";
        }
        else
        {
            if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond="";
        }

        $select_insert_date="DATE_FORMAT(a.insert_date,'%d-%m-%Y') as INSERT_DATE";
        $select_insert_time="DATE_FORMAT(a.insert_date,'%H:%i:%S') as INSERT_TIME";
    }else{
        if($cbo_based_on==1)
        {
            if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $date_cond="";
        }
        else
        {
            if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."   01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond="";
        }
        $select_insert_date=" to_char(a.insert_date,'DD-MON-YYYY') as INSERT_DATE";//HH24:MI:SS
        $select_insert_time=" to_char(a.insert_date,'HH24:MI:SS') as INSERT_TIME";
    }

    $item_cond=""; $purpose_cond=""; $location_store_cond="";
    if($cbo_item_cat !=0 ) $item_cond=" and a.item_category=$cbo_item_cat"; else $item_cond=" and a.item_category in(5,6,7,23)";

    if($cbo_purpose!="" && $cbo_purpose!=0)
    {
        $purpose_cond=" and c.issue_purpose=$cbo_purpose";
    }

    if($cbo_location_name > 0){
        $location_store_cond .= " and c.location_id = $cbo_location_name";
    }

    if($cbo_store_name > 0){
        $location_store_cond .= " and c.store_id = $cbo_store_name";
    }
    if($cbo_party_name > 0){
        $location_store_cond .= " and c.loan_party = $cbo_party_name";
    }

    $sqlIssue="select c.issue_number as ISSUE_NUMBER, c.id as ISSUE_ID, a.id as TRANS_ID, a.transaction_type as TRANSACTION_TYPE, a.transaction_date as TRANSACTION_DATE, 
       a.item_category as ITEM_CATEGORY, a.cons_quantity as ISSUE_QTY, a.cons_uom as CONS_UOM, b.id as PROD_ID, b.item_group_id as ITEM_GROUP_ID,
       b.sub_group_name as SUB_GROUP_NAME, b.item_description as ITEM_DESCRIPTION,  b.product_name_details as PRODUCT_NAME_DETAILS, b.item_code as ITEM_CODE, b.item_size as ITEM_SIZE, b.model as MODEL,
       b.item_number as ITEM_NUMBER, a.cons_rate as CONS_RATE, a.cons_amount as CONS_AMOUNT, a.requisition_no as REQUISITION_NO,
       a.inserted_by as INSERTED_BY, $select_insert_date, $select_insert_time, c.booking_id as BOOKING_ID, c.booking_no as BOOKING_NO,
       c.issue_basis as ISSUE_BASIS, c.issue_purpose as ISSUE_PURPOSE, c.loan_party as LOAN_PARTY, c.batch_no as BATCH_NO 
    from inv_transaction a, product_details_master b, inv_issue_master c
	where a.prod_id = b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in (2) and a.status_active=1 
	  and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $purpose_cond $location_store_cond order by c.id desc";
//    echo $sqlIssue;
    $div_width="1690px";
    $table_width=1650;
    $sql_issue_result=sql_select($sqlIssue);
    $issueDataArr = array(); $issueDataArrCat = array(); $requ_id = array(); $booking_id = array(); $batch_id = array();
    $issueReturnArr = array(); $issueTransferArr = array(); $issue_id = array(); $issueReturnData = array();

    foreach ($sql_issue_result as $key => $issueData){
        $item_key = $issueData["ISSUE_NUMBER"]."*##*".$issueData["ITEM_GROUP_ID"]."*##*".$issueData["ITEM_DESCRIPTION"]."*##*".$issueData["SUB_GROUP_NAME"]."*##*".$issueData["ITEM_SIZE"]."*##*".$issueData["MODEL"]."*##*".$issueData["ITEM_NUMBER"]."*##*".$issueData["ITEM_CODE"];
        $parent_key = $issueData["ISSUE_PURPOSE"];
        array_push($issue_id, $issueData["ISSUE_ID"]);
        if($issueData["REQUISITION_NO"] > 0){
            array_push($requ_id, $issueData["REQUISITION_NO"]);
        }
        if($issueData["ISSUE_BASIS"] == 2 && $issueData["BOOKING_ID"] != ""){
            array_push($booking_id, $issueData["BOOKING_ID"]);
        }
        if($issueData["ISSUE_BASIS"] == 5 && $issueData["BATCH_NO"] != ""){
            $batchExplode = explode(',', $issueData["BATCH_NO"]);
            foreach ($batchExplode as $batch){
                array_push($batch_id, $batch);
            }
        }
        $issueDataArr[$parent_key]['issue_purpose'] = $general_issue_purpose[$issueData["ISSUE_PURPOSE"]];
        $issueDataArr[$parent_key]['item_key'] = $item_key;
        $issueDataArr[$parent_key]['item_data'][$item_key]['issue_no'] = $issueData["ISSUE_NUMBER"];
        $issueDataArr[$parent_key]['item_data'][$item_key]['loan_party'] = $party_arr[$issueData["LOAN_PARTY"]];
        $issueDataArr[$parent_key]['item_data'][$item_key]['item_category'] = $item_category[$issueData["ITEM_CATEGORY"]];
        $issueDataArr[$parent_key]['item_data'][$item_key]['item_group'] = $item_group_arr[$issueData["ITEM_GROUP_ID"]];
        $issueDataArr[$parent_key]['item_data'][$item_key]['item_description'] = $issueData["ITEM_DESCRIPTION"];
        $issueDataArr[$parent_key]['item_data'][$item_key]['issue_basis'] = $issueData["ISSUE_BASIS"];
        $issueDataArr[$parent_key]['item_data'][$item_key]['booking_no'] = $issueData["BOOKING_NO"];
        $issueDataArr[$parent_key]['item_data'][$item_key]['batch_no'] = $issueData["BATCH_NO"];
        $issueDataArr[$parent_key]['item_data'][$item_key]['booking_id'] = $issueData["BOOKING_ID"];
        $issueDataArr[$parent_key]['item_data'][$item_key]['requ_id'] = $issueData["REQUISITION_NO"];
        $issueDataArr[$parent_key]['item_data'][$item_key]['username'] = $user_arr[$issueData["INSERTED_BY"]];
        $issueDataArr[$parent_key]['item_data'][$item_key]['issue_qty'][$issueData["ISSUE_ID"]] += $issueData["ISSUE_QTY"];
        $issueDataArr[$parent_key]['item_data'][$item_key]['uom'] = $unit_of_measurement[$issueData["CONS_UOM"]];
        $issueDataArr[$parent_key]['item_data'][$item_key]['avg_rate'][$key] = $issueData["CONS_RATE"];
    }

    $issue_id_uni = array_chunk(array_unique($issue_id),999, true);
    $counter = false;
    $issue_id_cond = "";
    foreach ($issue_id_uni as $key => $value){
        if($counter){
            $issue_id_cond .= " or a.issue_id in (".implode(',', $value).")";
        }else{
            $issue_id_cond .= " and a.issue_id in (".implode(',', $value).")";
        }
        $counter = true;
    }
    $requisitionArr = [];
    if(count($requ_id) > 0) {
        $requ_id_uni = array_chunk(array_unique($requ_id), 999, true);
        $counter = false;
        $requ_id_cond = "";
        foreach ($requ_id_uni as $key => $value) {
            if ($counter) {
                $requ_id_cond .= " or id in (" . implode(',', $value) . ")";
            } else {
                $requ_id_cond .= " and id in (" . implode(',', $value) . ")";
            }
            $counter = true;
        }

        $requisitionArr = return_library_array("select id, requ_no from dyes_chem_issue_requ_mst where status_active = 1 and is_deleted = 0 $requ_id_cond", "id", "requ_no");
    }

    $bookingArr = [];
    if(count($booking_id) > 0) {
        $booking_id_uni = array_chunk(array_unique($booking_id), 999, true);
        $counter = false;
        $booking_id_cond = "";
        foreach ($booking_id_uni as $key => $value) {
            if ($counter) {
                $booking_id_cond .= " or id in (" . implode(',', $value) . ")";
            } else {
                $booking_id_cond .= " and id in (" . implode(',', $value) . ")";
            }
            $counter = true;
        }

        $bookingArr = return_library_array("select id, wo_number from wo_non_order_info_mst where status_active = 1 and is_deleted = 0 $booking_id_cond", "id", "wo_number");
    }

    $batchArr = [];
    if(count($batch_id) > 0) {
        $batch_id_uni = array_chunk(array_unique($batch_id), 999, true);
        $counter = false;
        $batch_id_cond = "";
        foreach ($batch_id_uni as $key => $value) {
            if ($counter) {
                $batch_id_cond .= " or id in (" . implode(',', $value) . ")";
            } else {
                $batch_id_cond .= " and id in (" . implode(',', $value) . ")";
            }
            $counter = true;
        }

        $batchArr = return_library_array("select id, batch_no from PRO_BATCH_CREATE_MST where status_active = 1 and is_deleted = 0 $batch_id_cond", "id", "batch_no");
    }

    $issueReturnSql = "SELECT a.issue_id as ISSUE_ID, a.item_category AS ITEM_CATEGORY, a.cons_quantity AS ISSUE_RETURN_QTY, a.cons_uom AS CONS_UOM,
       b.id  AS PROD_ID, b.item_group_id AS ITEM_GROUP_ID, b.sub_group_name AS SUB_GROUP_NAME, b.item_description AS ITEM_DESCRIPTION,
       b.item_code AS ITEM_CODE, b.item_size AS ITEM_SIZE, b.model AS MODEL, b.item_number AS ITEM_NUMBER, a.cons_rate  AS CONS_RATE, a.cons_amount AS CONS_AMOUNT
    FROM inv_transaction a, product_details_master b, inv_receive_master c
    WHERE a.prod_id = b.id AND a.mst_id = c.id AND a.company_id = $cbo_company_name AND a.transaction_type IN ( 4 ) AND a.status_active = 1 AND a.is_deleted = 0 AND 
          c.status_active = 1 AND c.is_deleted = 0 $item_cond $issue_id_cond";

    $issueReturnSqlResult = sql_select($issueReturnSql);
    foreach ($issueReturnSqlResult as $key => $issueReturnData){
        $item_key = $issueReturnData["ITEM_GROUP_ID"]."*##*".$issueReturnData["ITEM_DESCRIPTION"]."*##*".$issueReturnData["SUB_GROUP_NAME"]."*##*".$issueReturnData["ITEM_SIZE"]."*##*".$issueReturnData["MODEL"]."*##*".$issueReturnData["ITEM_NUMBER"]."*##*".$issueReturnData["ITEM_CODE"];
        $issueReturnData[$item_key][$issueReturnData['ISSUE_ID']] += $issueReturnData["ISSUE_RETURN_QTY"];
    }
    ob_start();
    ?>
    <div style="width:<? echo $div_width; ?>">
        <fieldset style="width:<? echo $div_width; ?>">
            <table width="<? echo $table_width+10; ?>" border="0" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left">
                <tr class="form_caption" style="border:none;">
                    <td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
                </tr>
                <tr style="border:none;">
                    <td colspan="16" align="center" style="border:none; font-size:14px;">
                        Date : <? echo $txt_date_from; ?> To <? echo $txt_date_to; ?></td>
                </tr>
            </table>
            <br/>
            <table width="<? echo $table_width+10; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"  align="left">
                <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="100">Issue No.</th>
                    <th width="120">Category</th>
                    <th width="110">Group</th>
                    <th width="170">Item Description</th>
                    <th width="100">Issue Purpose</th>
                    <th width="70">UOM</th>
                    <th width="130">Loan Party</th>
                    <th width="110">Issue Basis</th>
                    <th width="110">Requisition</th>
                    <th width="100">Issue Qty.</th>
                    <th width="90">Return Qty.</th>
                    <th width="100">Net Issue</th>
                    <th width="90">Avg. Rate</th>
                    <th width="110">Net Value</th>
                    <th width="110">Insert By</th>

                </tr>
                </thead>
            </table>
            <br/>
            <div style="width:<? echo $div_width; ?>; overflow-y: scroll; max-height:260px;" id="scroll_body">
                <table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                    <tbody>
                    <?
                    $counter = 1;
                    $issueSubTotalGrand = 0; $issueReturnSubTotalGrand = 0; $issueValueSubtotalGrand = 0; $netIssueSubtotalGrand = 0;
                    foreach ($issueDataArr as $key => $data){
                        $issueSubTotal = 0; $issueReturnSubTotal = 0; $issueValueSubtotal = 0; $netIssueSubtotal = 0;
                        foreach ($data['item_data'] as $item_key => $item_data){
                            if($counter%2==0) $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<?=$bgcolor?>" style="font-size: 10pt;">
                                <td valign="middle" width="30" align="center"><?=$counter;?></td>
                                <td valign="middle" width="100"><?=$item_data['issue_no']?></td>
                                <td valign="middle" width="120"><?=$item_data['item_category']?></td>
                                <td valign="middle" width="110"><?=$item_data['item_group']?></td>
                                <td valign="middle" width="170"><?=$item_data['item_description']?></td>
                                <td valign="middle" width="100"><?=$data['issue_purpose']?></td>
                                <td valign="middle" width="70" align="center"><?=$item_data['uom']?></td>
                                <?
                                $issueReturnQty = 0;
                                foreach ($item_data['issue_qty'] as $issue_key => $qty_val){
                                    $issueReturnQty += isset($issueReturnData[$item_key][$issue_key]) ? $issueReturnData[$item_key][$issue_key] : 0;
                                }
                                $issueQty = array_sum($item_data['issue_qty']);
                                $issueSubTotal += $issueQty;
                                $net_issue = $issueQty - $issueReturnQty;
                                $issueReturnSubTotal += $issueReturnQty;
                                $netIssueSubtotal += $net_issue;
                                $avg_rate = array_sum($item_data["avg_rate"])/count($item_data["avg_rate"]);
                                $issueValueSubtotal += $net_issue*$avg_rate;
                                ?>
                                <td valign="middle" width="130"><?=$item_data['loan_party']?></td>
                                <td valign="middle" width="110"><?=$receive_basis_arr[$item_data['issue_basis']]?></td>
                                <?
                                if($item_data['issue_basis'] == 7){
                                    ?>
                                    <td valign="middle" width="110"><?=$requisitionArr[$item_data['requ_id']]?></td>
                                    <?
                                }elseif($item_data['issue_basis'] == 5){
                                    $batchExp = explode(',', $item_data['batch_no']);
                                    $batchStr = "";
                                    foreach ($batchExp as $batch){
                                        $batchStr .= $batchArr[$batch].", ";
                                    }
                                    ?>
                                    <td valign="middle" width="110"><?=rtrim($batchStr, ", ")?></td>
                                    <?
                                }elseif($item_data['issue_basis'] == 2){
                                    ?>
                                    <td valign="middle" width="110"><?=$bookingArr[$item_data['booking_id']]?></td>
                                    <?
                                }else{
                                    ?>
                                    <td valign="middle" width="110"><?=$item_data['booking_no']?></td>
                                    <?
                                }
                                ?>
                                <td valign="middle" width="100" align="right"><?=number_format($issueQty, 3)?></td>
                                <td valign="middle" width="90" align="right"><?=number_format($issueReturnQty, 3);?></td>
                                <td valign="middle" width="100" align="right"><?=number_format($net_issue, 3);?></td>
                                <td valign="middle" width="90" align="right"><?=number_format($avg_rate, 3)?></td>
                                <td valign="middle" width="110" align="right"><?=number_format($net_issue*$avg_rate, 3);?></td>
                                <td valign="middle" width="100"><?=$item_data['username']?></td>
                            </tr>
                            <?
                            $counter++;
                        }
                        $issueSubTotalGrand += $issueSubTotal;
                        $issueReturnSubTotalGrand += $issueReturnSubTotal;
                        $issueValueSubtotalGrand += $issueValueSubtotal;
                        $netIssueSubtotalGrand += $netIssueSubtotal;
                        ?>
                        <tr><td colspan="16"></td></tr>
                        <tr bgcolor="#ffebcd">
                            <td colspan="10" align="right"><strong>Sub Total</strong></td>
                            <td  align="right"><strong><?=number_format($issueSubTotal, 3)?></strong></td>
                            <td  align="right"><strong><?=number_format($issueReturnSubTotal, 3)?></strong></td>
                            <td  align="right"><strong><?=number_format($netIssueSubtotal, 3)?></strong></td>
                            <td  align="right"><strong></strong></td>
                            <td  align="right"><strong><?=number_format($issueValueSubtotal,3)?></strong></td>
                            <td  align="right"></td>
                        </tr>
                        <tr><td colspan="16"></td></tr>
                        <?
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="10" align="right" style="text-align: right !important;"><strong>Grand Total</strong></td>
                        <td align="right" style="text-align: right !important;"><strong><?=number_format($issueSubTotalGrand, 3)?></strong></td>
                        <td align="right" style="text-align: right !important;"><strong><?=number_format($issueReturnSubTotalGrand, 3)?></strong></td>
                        <td align="right" style="text-align: right !important;"><strong><?=number_format($netIssueSubtotalGrand, 3)?></strong></td>
                        <td >&nbsp;</td>
                        <td align="right" style="text-align: right !important;"><strong><?=number_format($issueValueSubtotalGrand, 3)?></strong></td>
                        <td align="right" style="text-align: right !important;"></td>
                    </tr>
                    </tfoot>
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
    echo "$html**$filename**$cbo_item_cat**1";
    exit();

}
if($action=="generate_report_purpose_wise")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $cbo_item_cat=str_replace("'","",$cbo_item_cat);
    $cbo_company_name=str_replace("'","",$cbo_company_name);
    $cbo_location_name=str_replace("'","",$cbo_location_name);
    $cbo_store_name=str_replace("'","",$cbo_store_name);
    $cbo_party_name=str_replace("'","",$cbo_party_name);
    $txt_date_from=str_replace("'","",$txt_date_from);
    $txt_date_to=str_replace("'","",$txt_date_to);
    $cbo_based_on=str_replace("'","",$cbo_based_on);
    $cbo_purpose=str_replace("'","",$cbo_purpose);

    $party_arr = return_library_array("select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b	where a.id=b.supplier_id and b.tag_company=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name", "id", "supplier_name");

    if($db_type==0)
    {
        if($cbo_based_on==1)
        {
            if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $date_cond="";
        }
        else
        {
            if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond="";
        }

        $select_insert_date="DATE_FORMAT(a.insert_date,'%d-%m-%Y') as INSERT_DATE";
        $select_insert_time="DATE_FORMAT(a.insert_date,'%H:%i:%S') as INSERT_TIME";
    }else{
        if($cbo_based_on==1)
        {
            if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $date_cond="";
        }
        else
        {
            if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."   01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond="";
        }
        $select_insert_date=" to_char(a.insert_date,'DD-MON-YYYY') as INSERT_DATE";//HH24:MI:SS
        $select_insert_time=" to_char(a.insert_date,'HH24:MI:SS') as INSERT_TIME";
    }

    $item_cond=""; $purpose_cond=""; $location_store_cond="";
    if($cbo_item_cat !=0 ) $item_cond=" and a.item_category=$cbo_item_cat"; else $item_cond=" and a.item_category in(5,6,7,23)";

    if($cbo_purpose!="" && $cbo_purpose!=0)
    {
        $purpose_cond=" and c.issue_purpose=$cbo_purpose";
    }

    if($cbo_location_name > 0){
        $location_store_cond .= " and c.location_id = $cbo_location_name";
    }

    if($cbo_store_name > 0){
        $location_store_cond .= " and c.store_id = $cbo_store_name";
    }
    if($cbo_party_name > 0){
        $location_store_cond .= " and c.loan_party = $cbo_party_name";
    }

    $sqlIssue="select c.id as ISSUE_ID, a.id as TRANS_ID, a.transaction_type as TRANSACTION_TYPE, a.transaction_date as TRANSACTION_DATE,
       a.item_category as ITEM_CATEGORY, a.cons_quantity as ISSUE_QTY, a.cons_uom as CONS_UOM, b.id as PROD_ID, b.item_group_id as ITEM_GROUP_ID,
       b.sub_group_name as SUB_GROUP_NAME, b.item_description as ITEM_DESCRIPTION,  b.product_name_details as PRODUCT_NAME_DETAILS, b.item_code as ITEM_CODE, b.item_size as ITEM_SIZE, b.model as MODEL,
       b.item_number as ITEM_NUMBER, a.cons_rate as CONS_RATE, a.cons_amount as CONS_AMOUNT,
       a.inserted_by as INSERTED_BY, $select_insert_date, $select_insert_time, c.booking_id as BOOKING_ID, c.booking_no as BOOKING_NO,
       c.issue_basis as ISSUE_BASIS, c.issue_purpose as ISSUE_PURPOSE, c.loan_party as LOAN_PARTY 
    from inv_transaction a, product_details_master b, inv_issue_master c
	where a.prod_id = b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in (2) and a.status_active=1 
	  and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $purpose_cond $location_store_cond";
    $table_width=1200;
    $div_width="1215px";
    $table_width1=1060;
    $div_width1="1075px";
    $sql_issue_result=sql_select($sqlIssue);
    $issueDataArr = array(); $issueDataArrCat = array(); $issueReturnArr = array(); $issueTransferArr = array(); $issue_id = array(); $issueReturnData = array();

    foreach ($sql_issue_result as $key => $issueData){
        $item_key = $issueData["ITEM_GROUP_ID"]."*##*".$issueData["ITEM_DESCRIPTION"]."*##*".$issueData["SUB_GROUP_NAME"]."*##*".$issueData["ITEM_SIZE"]."*##*".$issueData["MODEL"]."*##*".$issueData["ITEM_NUMBER"]."*##*".$issueData["ITEM_CODE"];
        $parent_key = $issueData["LOAN_PARTY"]."*##*".$issueData["ITEM_CATEGORY"]."*##*".$issueData["ISSUE_PURPOSE"];
        $parent_cat = $issueData["ITEM_CATEGORY"]."*##*".$issueData["ISSUE_PURPOSE"];
        array_push($issue_id, $issueData["ISSUE_ID"]);
        $issueDataArr[$parent_key]['loan_party'] = $party_arr[$issueData["LOAN_PARTY"]];
        $issueDataArr[$parent_key]['item_category'] = $item_category[$issueData["ITEM_CATEGORY"]];
        $issueDataArr[$parent_key]['issue_purpose'] = $general_issue_purpose[$issueData["ISSUE_PURPOSE"]];
        $issueDataArr[$parent_key]['item_key'] = $item_key;
        $issueDataArr[$parent_key]['item_data'][$item_key]['product_name'] = $issueData["PRODUCT_NAME_DETAILS"];
        $issueDataArr[$parent_key]['item_data'][$item_key]['issue_qty'][$issueData["ISSUE_ID"]] += $issueData["ISSUE_QTY"];
        $issueDataArr[$parent_key]['item_data'][$item_key]['uom'] = $unit_of_measurement[$issueData["CONS_UOM"]];
        $issueDataArr[$parent_key]['item_data'][$item_key]['avg_rate'][$key] = $issueData["CONS_RATE"];

        $issueDataArrCat[$parent_cat]['item_category'] = $item_category[$issueData["ITEM_CATEGORY"]];
        $issueDataArrCat[$parent_cat]['issue_purpose'] = $general_issue_purpose[$issueData["ISSUE_PURPOSE"]];
        $issueDataArrCat[$parent_cat]['item_key'] = $item_key;
        $issueDataArrCat[$parent_cat]['item_data'][$item_key]['product_name'] = $issueData["PRODUCT_NAME_DETAILS"];
        $issueDataArrCat[$parent_cat]['item_data'][$item_key]['issue_qty'][$issueData["ISSUE_ID"]] += $issueData["ISSUE_QTY"];
        $issueDataArrCat[$parent_cat]['item_data'][$item_key]['uom'] = $unit_of_measurement[$issueData["CONS_UOM"]];
        $issueDataArrCat[$parent_cat]['item_data'][$item_key]['avg_rate'][$key] = $issueData["CONS_RATE"];
    }

    $issue_id_uni = array_chunk(array_unique($issue_id),999, true);
    $counter = false;
    $issue_id_cond = "";
    foreach ($issue_id_uni as $key => $value){
        if($counter){
            $issue_id_cond .= " or a.issue_id in (".implode(',', $value).")";
        }else{
            $issue_id_cond .= " and a.issue_id in (".implode(',', $value).")";
        }
        $counter = true;
    }
//    echo $issue_id_cond;

    $issueReturnSql = "SELECT a.issue_id as ISSUE_ID, a.item_category AS ITEM_CATEGORY, a.cons_quantity AS ISSUE_RETURN_QTY, a.cons_uom AS CONS_UOM,
       b.id  AS PROD_ID, b.item_group_id AS ITEM_GROUP_ID, b.sub_group_name AS SUB_GROUP_NAME, b.item_description AS ITEM_DESCRIPTION,
       b.item_code AS ITEM_CODE, b.item_size AS ITEM_SIZE, b.model AS MODEL, b.item_number AS ITEM_NUMBER, a.cons_rate  AS CONS_RATE, a.cons_amount AS CONS_AMOUNT
    FROM inv_transaction a, product_details_master b, inv_receive_master c
    WHERE a.prod_id = b.id AND a.mst_id = c.id AND a.company_id = $cbo_company_name AND a.transaction_type IN ( 4 ) AND a.status_active = 1 AND a.is_deleted = 0 AND 
          c.status_active = 1 AND c.is_deleted = 0 $item_cond $issue_id_cond";
//    echo $issueReturnSql;

    $issueReturnSqlResult = sql_select($issueReturnSql);
    foreach ($issueReturnSqlResult as $key => $issueReturnData){
        $item_key = $issueReturnData["ITEM_GROUP_ID"]."*##*".$issueReturnData["ITEM_DESCRIPTION"]."*##*".$issueReturnData["SUB_GROUP_NAME"]."*##*".$issueReturnData["ITEM_SIZE"]."*##*".$issueReturnData["MODEL"]."*##*".$issueReturnData["ITEM_NUMBER"]."*##*".$issueReturnData["ITEM_CODE"];
        $issueReturnData[$item_key][$issueReturnData['ISSUE_ID']] += $issueReturnData["ISSUE_RETURN_QTY"];
    }
    ob_start();
    ?>
    <div style="width:<? echo $div_width; ?>">
        <fieldset style="width:<? echo $div_width; ?>">
            <table width="<? echo $table_width+10; ?>" border="0" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left">
                <tr class="form_caption" style="border:none;">
                    <td colspan="12" align="center" style="border:none;font-size:16px; font-weight:bold" >Purpose Wise Dyes and Chemical Issue Report</td>
                </tr>
                <tr style="border:none;">
                    <td colspan="12" align="center" style="border:none; font-size:14px;">
                        Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
                </tr>
            </table>
            <br/>
            <table width="<? echo $table_width+10; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"  align="left">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="140">Party Name</th>
                        <th width="100">Issue Purpose</th>
                        <th width="130">Category</th>
                        <th width="200">Product</th>
                        <th width="80">UOM</th>
                        <th width="100">Issue Qty.</th>
                        <th width="90">Return Qty.</th>
                        <th width="100">Net Issue</th>
                        <th width="90">Avg. Rate</th>
                        <th width="110">Net Value</th>

                    </tr>
                </thead>
            </table>
            <br/>
            <div style="width:<? echo $div_width; ?>; overflow-y: scroll; max-height:260px;" id="scroll_body">
                <table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                   <tbody>
                       <?
                       $counter = 1;
                       $issueSubTotalGrand = 0; $issueReturnSubTotalGrand = 0; $issueValueSubtotalGrand = 0; $netIssueSubtotalGrand = 0;
                       foreach ($issueDataArr as $key => $data){
                           $issueSubTotal = 0; $issueReturnSubTotal = 0; $issueValueSubtotal = 0; $netIssueSubtotal = 0;
                           foreach ($data['item_data'] as $item_key => $item_data){
                               if($counter%2==0) $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                               ?>
                               <tr bgcolor="<?=$bgcolor?>" style="font-size: 10pt;">
                                   <td width="30" align="center"><?=$counter;?></td>
                                   <td width="140"><?=$data['loan_party'];?></td>
                                   <td width="100"><?=$data['issue_purpose'];?></td>
                                   <td width="130"><?=$data['item_category'];?></td>
                                   <td width="200"><?=$item_data['product_name']?></td>
                                   <td width="80" align="center"><?=$item_data['uom']?></td>
                                   <?
                                   $issueReturnQty = 0;
                                   foreach ($item_data['issue_qty'] as $issue_key => $qty_val){
                                       $issueReturnQty += isset($issueReturnData[$item_key][$issue_key]) ? $issueReturnData[$item_key][$issue_key] : 0;
                                   }
                                   $issueQty = array_sum($item_data['issue_qty']);
                                   $issueSubTotal += $issueQty;
                                   $net_issue = $issueQty - $issueReturnQty;
                                   $issueReturnSubTotal += $issueReturnQty;
                                   $netIssueSubtotal += $net_issue;
                                   $avg_rate = array_sum($item_data["avg_rate"])/count($item_data["avg_rate"]);
                                   $issueValueSubtotal += $net_issue*$avg_rate;
                                   ?>
                                   <td width="100" align="right"><?=number_format($issueQty, 3)?></td>
                                   <td width="90" align="right"><?=number_format($issueReturnQty, 3);?></td>
                                   <td width="100" align="right"><?=number_format($net_issue, 3);?></td>
                                   <td width="90" align="right"><?=number_format($avg_rate, 3)?></td>
                                   <td width="100" align="right"><?=number_format($net_issue*$avg_rate, 3);?></td>
                               </tr>
                       <?
                               $counter++;
                           }
                           $issueSubTotalGrand += $issueSubTotal;
                           $issueReturnSubTotalGrand += $issueReturnSubTotal;
                           $issueValueSubtotalGrand += $issueValueSubtotal;
                           $netIssueSubtotalGrand += $netIssueSubtotal;
                       ?>
                           <tr><td colspan="11"></td></tr>
                           <tr bgcolor="#ffebcd">
                               <td colspan="6" align="right"><strong><?=$data['loan_party'];?> --> <?=$data['issue_purpose'];?> --> <?=$data['item_category'];?> --> Sub Total</strong></td>
                               <td  align="right"><strong><?=number_format($issueSubTotal, 3)?></strong></td>
                               <td  align="right"><strong><?=number_format($issueReturnSubTotal, 3)?></strong></td>
                               <td  align="right"><strong><?=number_format($netIssueSubtotal, 3)?></strong></td>
                               <td  align="right"><strong></strong></td>
                               <td  align="right"><strong><?=number_format($issueValueSubtotal,3)?></strong></td>
                           </tr>
                           <tr><td colspan="11"></td></tr>
                       <?
                       }
                       ?>
                   </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" align="right" style="text-align: right !important;"><strong>Grand Total</strong></td>
                                <td align="right" style="text-align: right !important;"><strong><?=number_format($issueSubTotalGrand, 3)?></strong></td>
                                <td align="right" style="text-align: right !important;"><strong><?=number_format($issueReturnSubTotalGrand, 3)?></strong></td>
                                <td align="right" style="text-align: right !important;"><strong><?=number_format($netIssueSubtotalGrand, 3)?></strong></td>
                                <td >&nbsp;</td>
                                <td align="right" style="text-align: right !important;"><strong><?=number_format($issueValueSubtotalGrand, 3)?></strong></td>
                            </tr>
                        </tfoot>
                </table>
            </div>
        </fieldset>
    </div>
    <div style="width:<? echo $div_width1; ?>; margin-top:15px;">
        <fieldset style="width:<? echo $div_width1; ?>">
            <table width="<? echo $table_width1+10; ?>" border="0" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left">
                <tr class="form_caption" style="border:none;">
                    <td colspan="10" align="center" style="border:none;font-size:16px; font-weight:bold; color: #000;" >Category Wise Issue</td>
                </tr>
            </table>
            <br/>
            <table width="<? echo $table_width1+10; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"  align="left">
                <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="100">Issue Purpose</th>
                    <th width="130">Category</th>
                    <th width="200">Product</th>
                    <th width="80">UOM</th>
                    <th width="100">Issue Qty.</th>
                    <th width="90">Return Qty.</th>
                    <th width="100">Net Issue</th>
                    <th width="90">Avg. Rate</th>
                    <th width="110">Net Value</th>

                </tr>
                </thead>
            </table>
            <br/>
            <div style="width:<? echo $div_width1; ?>; overflow-y: scroll; max-height:260px;" id="scroll_body1">
                <table width="<? echo $table_width1; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1" align="left">
                    <tbody>
                    <?
                    $counter = 1;
                    $issueSubTotalGrand = 0; $issueReturnSubTotalGrand = 0; $issueValueSubtotalGrand = 0; $netIssueSubtotalGrand = 0;
                    foreach ($issueDataArrCat as $key => $data){
                        $issueSubTotal = 0; $issueReturnSubTotal = 0; $issueValueSubtotal = 0; $netIssueSubtotal = 0;
                        foreach ($data['item_data'] as $item_key => $item_data){
                            if($counter%2==0) $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<?=$bgcolor?>" style="font-size: 10pt;">
                                <td width="30" align="center"><?=$counter;?></td>
                                <td width="100"><?=$data['issue_purpose'];?></td>
                                <td width="130"><?=$data['item_category'];?></td>
                                <td width="200"><?=$item_data['product_name']?></td>
                                <td width="80" align="center"><?=$item_data['uom']?></td>
                                <?
                                $issueReturnQty = 0;
                                foreach ($item_data['issue_qty'] as $issue_key => $qty_val){
                                    $issueReturnQty += isset($issueReturnData[$item_key][$issue_key]) ? $issueReturnData[$item_key][$issue_key] : 0;
                                }
                                $issueQty = array_sum($item_data['issue_qty']);
                                $issueSubTotal += $issueQty;
                                $net_issue = $issueQty - $issueReturnQty;
                                $issueReturnSubTotal += $issueReturnQty;
                                $netIssueSubtotal += $net_issue;
                                $avg_rate = array_sum($item_data["avg_rate"])/count($item_data["avg_rate"]);
                                $issueValueSubtotal += $net_issue*$avg_rate;
                                ?>
                                <td width="100" align="right"><?=number_format($issueQty, 3)?></td>
                                <td width="90" align="right"><?=number_format($issueReturnQty, 3);?></td>
                                <td width="100" align="right"><?=number_format($net_issue, 3);?></td>
                                <td width="90" align="right"><?=number_format($avg_rate, 3)?></td>
                                <td width="100" align="right"><?=number_format($net_issue*$avg_rate, 3);?></td>
                            </tr>
                            <?
                            $counter++;
                        }
                        $issueSubTotalGrand += $issueSubTotal;
                        $issueReturnSubTotalGrand += $issueReturnSubTotal;
                        $issueValueSubtotalGrand += $issueValueSubtotal;
                        $netIssueSubtotalGrand += $netIssueSubtotal;
                        ?>
                        <tr><td colspan="10"></td></tr>
                        <tr bgcolor="#ffebcd">
                            <td colspan="5" align="right"><strong><?=$data['issue_purpose'];?> --> <?=$data['item_category'];?> --> Sub Total</strong></td>
                            <td  align="right"><strong><?=number_format($issueSubTotal, 3)?></strong></td>
                            <td  align="right"><strong><?=number_format($issueReturnSubTotal, 3)?></strong></td>
                            <td  align="right"><strong><?=number_format($netIssueSubtotal, 3)?></strong></td>
                            <td  align="right"><strong></strong></td>
                            <td  align="right"><strong><?=number_format($issueValueSubtotal,3)?></strong></td>
                        </tr>
                        <tr><td colspan="10"></td></tr>
                        <?
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5" align="right" style="text-align: right !important;"><strong>Grand Total</strong></td>
                        <td align="right" style="text-align: right !important;"><strong><?=number_format($issueSubTotalGrand, 3)?></strong></td>
                        <td align="right" style="text-align: right !important;"><strong><?=number_format($issueReturnSubTotalGrand, 3)?></strong></td>
                        <td align="right" style="text-align: right !important;"><strong><?=number_format($netIssueSubtotalGrand, 3)?></strong></td>
                        <td >&nbsp;</td>
                        <td align="right" style="text-align: right !important;"><strong><?=number_format($issueValueSubtotalGrand, 3)?></strong></td>
                    </tr>
                    </tfoot>
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
    echo "$html**$filename**$cbo_item_cat**2";
    exit();

}
?>
