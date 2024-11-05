<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
    header("location:login.php");
    die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_supplier") {
    echo create_drop_down("cbo_supplier_name", 160, "select s.id, s.supplier_name from LIB_SUPPLIER s, LIB_SUPPLIER_PARTY_TYPE sp, LIB_SUPPLIER_TAG_COMPANY sc where s.id = sp.supplier_id and s.id = sc.supplier_id and sp.party_type = 2 and sc.tag_company = '$data' and s.is_deleted = 0 and s.status_active = 1 order by s.supplier_name", "id,supplier_name", 1, "-- Select Supplier --", 0, "reset_lot()");
//echo create_drop_down("cbo_supplier_name", 140, "select b.id, b.supplier_name  from lib_supplier_tag_company a, lib_supplier b where a.supplier_id = b.id and a.tag_company = '$data' and b.status_active=1 and b.is_deleted=0 order by b.supplier_name", "id,supplier_name", 1, "-- Select Supplier --", 0, "reset_lot()");
    exit();
}

if ($action == "lot_no_search") {
    echo load_html_head_contents("Lot No Info", "../../../../", 1, 1, $unicode, "");
    extract($_REQUEST);
    ?>
    <script>
        function js_set_value(str) {

            var splitData = str.split("_");
            $("#hidden_product").val(splitData[0]); // wo/pi id
            $("#hidden_lot").val(splitData[1]);

            parent.emailwindow.hide();
        }
    </script> 
    <input type="hidden" value="" id="hidden_product">
    <input type="hidden" value="" id="hidden_lot">
    <?
    $sql = "select id,supplier_id,lot,product_name_details from product_details_master where company_id=$cbo_company_name and item_category_id=1 and supplier_id = $cbo_supplier_name";
    $supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
    $arr = array(1 => $supplier_arr);
    echo create_list_view("list_view", "Product Id, Supplier, Lot, Item Description", "70,160,70", "600", "260", 0, $sql, "js_set_value", "id,lot", "", 1, "0,supplier_id,0,0", $arr, "id,supplier_id,lot,product_name_details", "", "setFilterGrid('list_view',-1)", "0", "", "");
    ?> 

    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script> 

    <?
}

if ($action == "generate_report") {
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    $search_cond = "";
    if ($db_type == 0) {
        if ($from_date != "" && $to_date != "")
            $search_cond .= " and a.transaction_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
    }
    else {
        if ($from_date != "" && $to_date != "")
            $search_cond .= " and a.transaction_date  between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
    }

    $cbo_company_name = str_replace("'", "", trim($cbo_company_name));
    $cbo_supplier_name = str_replace("'", "", trim($cbo_supplier_name));
    $lot = str_replace("'", "", trim($txt_lot_no));
    $hidden_prod_no = str_replace("'", "", trim($hidden_prod_no));

    $companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
    $yarnCountArr = return_library_array("select id,yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", "id", "yarn_count");
    $colorArr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
    $brandArr = return_library_array("select id,yarn_brand from lib_yarn_brand where status_active=1 and is_deleted=0", "id", "yarn_brand");
    $buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
    $storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0", "id", "store_name");
    $methodArr = array(0 => "Weighted Average", 1 => "FIFO", 2 => "LIFO");

    $sql = sql_select("select id,product_name_details, yarn_count_id, lot, unit_of_measure, color from product_details_master where company_id = $cbo_company_name and supplier_id = $cbo_supplier_name and lot = '$lot' and item_category_id = 1 and id = $hidden_prod_no");

    $all_issue_trans_id = array();
    $sqlTransId = sql_select("select a.id,a.transaction_type
      from inv_transaction a, product_details_master b
      where a.prod_id = b.id and b.company_id = $cbo_company_name and b.supplier_id = $cbo_supplier_name and b.lot = '$lot'  and b.status_active = 1 and b.is_deleted = 0");


    foreach ($sqlTransId as $TransIdrow) {
        if ($TransIdrow[csf("transaction_type")] == 2) {
            $all_issue_trans_id[$TransIdrow[csf("id")]] = $TransIdrow[csf("id")];
        }
    }
    $issueTransIdArr = array_chunk($all_issue_trans_id, 999);
    $issue_job_cond = " and(";
    foreach ($issueTransIdArr as $issue_trans_id) {
        if ($issue_job_cond == " and(")
            $issue_job_cond .= " c.trans_id in(" . implode(',', $issue_trans_id) . ")";
        else
            $issue_job_cond .= " or c.trans_id in(" . implode(',', $issue_trans_id) . ")";
    }
    $issue_job_cond .= ")";

    $issue_job_order = sql_select("select a.id as job_id, a.job_no, a.style_ref_no, a.buyer_name, b.id as po_id, b.po_number,b.grouping, c.trans_id from wo_po_details_master a, wo_po_break_down b,  order_wise_pro_details c where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.trans_type=2 and c.entry_form=3 $issue_job_cond ");

    $jobIssueData = array();
    foreach ($issue_job_order as $row) {
        $jobIssueData[$row[csf("trans_id")]]["job_id"] = $row[csf("job_id")];
        $jobIssueData[$row[csf("trans_id")]]["job_no"] = $row[csf("job_no")];
        $jobIssueData[$row[csf("trans_id")]]["style_ref_no"] = $row[csf("style_ref_no")];
        $jobIssueData[$row[csf("trans_id")]]["buyer_name"] = $row[csf("buyer_name")];
        $jobIssueData[$row[csf("trans_id")]]["po_id"] = $row[csf("po_id")];
        $jobIssueData[$row[csf("trans_id")]]["po_number"] = $row[csf("po_number")];
		$jobIssueData[$row[csf("trans_id")]]["internal_ref"] = $row[csf("grouping")];
    }
    
    $tran_challan_sql = sql_select("select a.challan_no, a.id  ,a.transfer_system_id
            from inv_item_transfer_mst a,inv_transaction b, product_details_master c 
            where a.id = b.mst_id and b.prod_id = c.id  and c.lot = '$lot' and c.id =  $hidden_prod_no
            and b.transaction_type in (5,6)
            and a.is_deleted = 0 and a.status_active = 1 
            and b.is_deleted = 0 and b.status_active = 1 and c.is_deleted = 0 and c.status_active = 1");
    $transChallan = array();$transMrr = array();
    foreach($tran_challan_sql as $challanrow){
        $transChallan[$challanrow[csf("id")]] = $challanrow[csf("challan_no")];
        $transMrr[$challanrow[csf("id")]] = $challanrow[csf("transfer_system_id")];
    }
    ob_start();
    ?>

    <div> 
        <table style="width:1120px; float: left;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
            <thead>
                <tr style="border:none;">
                    <td colspan="9" align="center" style="border:none;font-size:16px; font-weight:bold" >Lot Wise Yarn Transaction Report </td> 
                </tr>
                <tr style="border:none;">
                    <td colspan="9" align="center" style="font-weight: bold; border: none;">
                        Company Name : <? echo $companyArr[$cbo_company_name]; ?> 
                        <?
                        if ($from_date != "" && $to_date != "") {
                            echo "<br/>From $from_date To $to_date";
                        }
                        ?> 
                    </td> 
                </tr>
            </thead>
        </table>
        <? foreach ($sql as $row) { ?>
            <table style="width:1120px; float: left;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
                <thead>
                    <tr style="border:none;">
                        <td colspan="10" style="border:none;font-size:12px; font-weight:bold;" >
                            Product Id: <? echo $row[csf('id')] ?>, Count: <? echo $yarnCountArr[$row[csf("yarn_count_id")]] ?>, Composition: <? echo $row[csf("product_name_details")] ?>, Color: <? echo $colorArr[$row[csf("color")]] ?>, Lot: <? echo $row[csf("lot")] ?>, UOM: <? echo $unit_of_measurement[$row[csf("unit_of_measure")]] ?>
                        </td> 

                    </tr>
                    <tr>
                        <td colspan="12" style="border:none;font-size:12px; font-weight:bold" >
                            Supplier: <? echo $supplierArr[$cbo_supplier_name]; ?>, Brand: <? $brandArr[$row[csf("brand")]] ?>
                        </td> 
                    </tr>
                    <tr>
                        <td colspan="12" style="border:none;font-size:12px; font-weight:bold" >
                            Method: <? echo $methodArr[$cbo_method]; ?>
                        </td> 
                    </tr>
                </thead>
            </table>
            <br/>
            <fieldset  style="width:1120px; float:left;">
                <table style="width:1120px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
                    <thead>
                        <tr>
                            <td colspan="11" style="font-size:15px; font-weight:bold">Receiving Status</td>
                        </tr>
                        <tr>
                            <th rowspan="2" width="50"> Sl</th>
                            <th rowspan="2" width="120"> Buyer </th>
                            <th rowspan="2" width="120">Trans Date</th>
                            <th rowspan="2" width="100">Callan No</th>
                            <th rowspan="2" width="120">Trans Ref No</th>
                            <th rowspan="2" width="120">Trans type</th>
                            <th rowspan="2" width="120">Purpose</th>
                            <th rowspan="2" width="120">Transfer With</th>
                            <th colspan="3">Receive</th>
                        </tr>
                        <tr>
                            <th width="80" align="center">Qnty</th>
                            <th width="80" align="center">Rate</th>
                            <th width="" align="center">Value</th>
                        </tr>
                    </thead>
                </table>
                <div style="width:1120px;  max-height:250px" id="scroll_body" align="left"> 
                    <table style="width:1120px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left" >
                        <?
                        //$sql_receive = "select a.id, a.transaction_date, a.transaction_type, b.recv_number, b.knitting_source, b.knitting_company, b.supplier_id, b.receive_purpose, a.cons_amount,a.cons_rate,a.cons_quantity,a.store_id,a.prod_id,a.buyer_id, a.cons_amount,a.company_id,a.cons_reject_qnty, b.challan_no, a.mst_id from inv_transaction a, inv_receive_master b where  a.mst_id=b.id and a.transaction_type in (1,4,5) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_cond and a.prod_id = " . $row[csf('id')] . " order by a.store_id,a.transaction_date asc";

                        $sql_receive = "select a.id, a.transaction_date transaction_date, a.transaction_type, b.recv_number, b.knitting_source, b.knitting_company, b.supplier_id, b.receive_purpose, a.cons_amount,a.cons_rate,a.cons_quantity,a.store_id as store_id ,a.prod_id,a.buyer_id, a.cons_amount,a.company_id,a.cons_reject_qnty, b.challan_no, a.mst_id from inv_transaction a, inv_receive_master b where  a.mst_id=b.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_cond and a.prod_id = " . $row[csf('id')] . " 
                    
                    union all 
                    
                    select a.id, a.transaction_date transaction_date, a.transaction_type, null recv_number, null knitting_source, null knitting_company, null supplier_id, null receive_purpose, a.cons_amount,a.cons_rate,a.cons_quantity,a.store_id store_id,a.prod_id,a.buyer_id, a.cons_amount,a.company_id,a.cons_reject_qnty, b.challan_no, a.mst_id from inv_transaction a, inv_item_transfer_mst b where  a.mst_id=b.id and a.transaction_type in (5) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_cond and a.prod_id = " . $row[csf('id')] . " order by store_id,transaction_date asc";
                        //echo $sql_receive;
                        // echo $sql_receive;
                        $receive_result = sql_select($sql_receive);
                        $i = 1;
                        foreach ($receive_result as $receiveRow) {

                            if ($receiveRow[csf("transaction_type")] == 1) {
                                $transWith = $supplierArr[$receiveRow[csf("supplier_id")]];
                            } else if ($receiveRow[csf("transaction_type")] == 4) {
                                if ($receiveRow[csf("knitting_source")] == 1) {
                                    $transWith = $companyArr[$receiveRow[csf("knitting_company")]];
                                } else {
                                    $transWith = $supplierArr[$receiveRow[csf("knitting_company")]];
                                }
                            } else {
                                $transWith = $companyArr[$receiveRow[csf("company_id")]];
                            }
                            
                            if ($receiveRow[csf("transaction_type")] == 5) {
                                $challan =$transChallan[$receiveRow[csf("mst_id")]];
                                $mrr_no =  $transMrr[$receiveRow[csf("mst_id")]];
                            }
                            else{
                                $challan = $receiveRow[csf("challan_no")];
                                $mrr_no = $receiveRow[csf("recv_number")];
                            }
                            if ($i % 2 == 0)
                                    $bgcolor = "#E9F3FF";
                                else
                                    $bgcolor = "#FFFFFF";
                            if (!in_array($receiveRow[csf("store_id")], $checkStoreArr)) {
                                $checkStoreArr[$i] = $receiveRow[csf("store_id")];
                                if ($i > 1) {
                                    ?>
                                    <tr bgcolor="#CCCCCC" style="font-weight: bold ">
                                        <td colspan="8" align="right"><b>Total</b></td>
                                        <td align="right"><? echo $tot_receive_qny; ?></td>
                                        <td>&nbsp;</td>
                                        <td align="right"><? echo number_format($tot_amount_qny,2); ?></td>
                                    </tr>
                                    <?
                                    $tot_receive_qny = 0;
                                    $tot_amount_qny = 0;
                                }
                                
                                ?>
                                <tr>
                                    <td colspan="14">
                                        <b>Store: <? echo $storeArr[$receiveRow[csf("store_id")]]; ?></b>
                                    </td>
                                </tr>
                            <? } ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="50"><? echo $i; ?></td>
                                <td width="120"><? echo $buyerArr[$receiveRow[csf("buyer_id")]] ?> &nbsp;</td>
                                <td width="120"><? echo change_date_format($receiveRow[csf("transaction_date")]) ?></td>
                                <td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@'; text-align: center;"><p><? echo $challan; ?></p></td>
                                <td width="120"><? echo $mrr_no; ?></td>
                                <td width="120"><? echo $transaction_type[$receiveRow[csf("transaction_type")]]; ?></td>
                                <td width="120"><? echo $yarn_issue_purpose[$receiveRow[csf("receive_purpose")]] ?></td>
                                <td width="120"><? echo $transWith ?></td>
                                <td width="80" align="right"><? echo $receiveRow[csf("cons_quantity")] ?></td>
                                <td width="80" align="right"><? echo number_format($receiveRow[csf("cons_rate")],2); ?></td>
                                <td width="" align="right"><? echo number_format($receiveRow[csf("cons_amount")],2) ?></td>
                            </tr>
                            <?
                            $i++;
                            $tot_receive_qny += $receiveRow[csf("cons_quantity")];
                            $tot_amount_qny += $receiveRow[csf("cons_amount")];
                            $currentStockQnty[$receiveRow[csf("store_id")]]["receiveQnty"] += $receiveRow[csf("cons_quantity")];
                            $currentStockReject[$receiveRow[csf("store_id")]] = $receiveRow[csf("cons_reject_qnty")];
                            $summary_array[$receiveRow[csf("store_id")]]['receive'] += $receiveRow[csf("cons_quantity")];
                            $summary_array[$receiveRow[csf("store_id")]]['reject'] += $receiveRow[csf("cons_reject_qnty")];
                        }
                        unset($i);
                        ?>
                        <tr bgcolor="#CCCCCC" style="font-weight: bold ">
                            <td colspan="8" align="right"><b>Total</b></td>
                            <td align="right"><? echo $tot_receive_qny; ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo number_format($tot_amount_qny,2); ?></td>
                        </tr>
                    </table> 
                </div>
            </fieldset>

            <fieldset style="width:1540px; float:left;margin-top: 5px;">
                <div>
                    <table style="width:1540px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
                        <thead>
                            <tr>
                                <td colspan="14" style="font-size:15px; font-weight:bold">Issue Status</td>
                            </tr>
                            <tr>
                                <th rowspan="2" width="50"> Sl</th>
                                <th rowspan="2" width="120">Buyer </th>
                                <th rowspan="2" width="120">Order No</th>
                                <th rowspan="2" width="120">Internal Ref</th>
                                <th rowspan="2" width="120">Style </th>
                                <th rowspan="2" width="120">Trans Date</th>
                                <th rowspan="2" width="100">Challan No.</th>
                                <th rowspan="2" width="120">Trans Ref No</th>
                                <th rowspan="2" width="120">Trans type</th>
                                <th rowspan="2" width="120">Purpose</th>
                                <th rowspan="2" width="120">Transfer With</th>
                                <th colspan="3">Issue</th>
                            </tr>
                            <tr>
                                <th width="80" align="center">Qnty</th>
                                <th width="80" align="center">Rate</th>
                                <th width="" align="center">Value</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:1540px;  max-height:250px" id="scroll_body" align="left"> 
                        <table style="width:1540px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left" >
                            <?
                            //$issue_result = sql_select("select a.id, a.company_id,a.transaction_date,a.transaction_type,b.issue_number,b.knit_dye_source,b.knit_dye_company,b.supplier_id,b.buyer_id,b.issue_purpose,a.cons_amount,a.cons_rate,a.cons_quantity,a.store_id,a.prod_id,b.challan_no, a.mst_id from inv_transaction a, inv_issue_master b where a.mst_id = b.id and a.transaction_type in (2,3,6) and a.item_category = 1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_cond and a.prod_id = " . $row[csf('id')] . " order by a.store_id,a.transaction_date asc");

                            $issue_sql = "select a.id, a.company_id,a.transaction_date as transaction_date,a.transaction_type,b.issue_number,b.knit_dye_source,b.knit_dye_company,b.supplier_id, b.buyer_id,b.issue_purpose,
                            a.cons_amount,a.cons_rate,a.cons_quantity,a.store_id as store_id,a.prod_id, b.challan_no, a.mst_id from inv_transaction a , inv_issue_master b 
                            where a.mst_id = b.id and a.transaction_type in (2,3) and a.item_category = 1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 
                            and b.is_deleted=0 $search_cond and a.prod_id = " . $row[csf('id')] . "

                            union all 

                            select a.id, a.company_id,a.transaction_date as transaction_date,a.transaction_type,b.transfer_system_id as issue_number,null as knit_dye_source , null as knit_dye_company ,null as supplier_id, null as buyer_id,null as issue_purposel,
                            a.cons_amount,a.cons_rate,a.cons_quantity,a.store_id as store_id,a.prod_id, b.challan_no, a.mst_id from inv_transaction a , inv_item_transfer_mst b 
                            where a.mst_id = b.id and a.transaction_type in (6) and a.item_category = 1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 
                            and b.is_deleted=0 $search_cond and a.prod_id = " . $row[csf('id')] . "

                            order by store_id,transaction_date asc";
                            
                            $issue_result = sql_select($issue_sql);
																				
                            $j = 1;
                            foreach ($issue_result as $issueRow) {

                                if ($issueRow[csf("transaction_type")] == 2) {
                                    if ($issueRow[csf("knit_dye_source")] == 1) {
                                        $transWithIssue = $companyArr[$issueRow[csf("knit_dye_company")]];
                                    } else {
                                        $transWithIssue = $supplierArr[$issueRow[csf("knit_dye_company")]];
                                    }
                                } else if ($issueRow[csf("transaction_type")] == 3) {
                                    $transWithIssue = $supplierArr[$issueRow[csf("supplier_id")]];
                                } else {
                                    $transWithIssue = $companyArr[$issueRow[csf("company_id")]];
                                }
                                
                                if ($issueRow[csf("transaction_type")] == 6) {
                                    $challan =$transChallan[$issueRow[csf("mst_id")]];
                                    $mrr_no =  $transMrr[$issueRow[csf("mst_id")]];
                                }
                                else{
                                    $challan = $issueRow[csf("challan_no")];
                                    $mrr_no =  $issueRow[csf("issue_number")];
                                }
                                if ($j % 2 == 0)
                                        $bgcolor = "#E9F3FF";
                                    else
                                        $bgcolor = "#FFFFFF";
                                if (!in_array($issueRow[csf("store_id")], $checkIssueStore)) {

                                    if ($j > 1) {
                                        ?>
                                        <tr bgcolor="#CCCCCC" style="font-weight: bold ">
                                            <td colspan="10" align="right"><b>Total</b></td>
                                            <td align="right"><? echo $tot_issue_qny; ?></td>
                                            <td>&nbsp;</td>
                                            <td align="right"><? echo number_format($tot_issue_amount_qny,2); ?></td>
                                        </tr>

                                        <?
                                        $tot_issue_qny = 0;
                                        $tot_issue_amount_qny = 0;
                                    }
                                    
                                    ?>
                                    <tr>
                                        <td colspan="13"><b>Store: <? echo $storeArr[$issueRow[csf("store_id")]]; ?></b></td>
                                    </tr>
                                    <?
                                    $checkIssueStore[$j] = $issueRow[csf("store_id")];
                                }
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('itr_<? echo $j; ?>', '<? echo $bgcolor; ?>')" id="itr_<? echo $j; ?>">
                                    <td width="50"><? echo $j; ?></td>
                                    <td width="120"><? echo $buyerArr[$jobIssueData[$issueRow[csf("id")]]["buyer_name"]]; ?></td>
                                    <td width="120" style="mso-number-format:'\@';"><? echo $jobIssueData[$issueRow[csf("id")]]["po_number"]; ?></td>
                                    <td width="120" style="mso-number-format:'\@';"><? echo $jobIssueData[$issueRow[csf("id")]]["internal_ref"]; ?></td>
                                    <td width="120" style="mso-number-format:'\@';"><? echo $jobIssueData[$issueRow[csf("id")]]["style_ref_no"]; ?></td>
                                    <td width="120"><? echo change_date_format($issueRow[csf("transaction_date")]); ?></td>
                                    <td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@'; text-align: center;"><p><? echo $challan; ?></p></td>
                                    <td width="120"><? echo $mrr_no; ?></td>
                                    <td width="120" ><? echo $transaction_type[$issueRow[csf("transaction_type")]]; ?></td>
                                    <td width="120"><? echo $yarn_issue_purpose[$issueRow[csf("issue_purpose")]]; ?></td>
                                    <td width="120"><? echo $transWithIssue; ?></td>
                                    <td width="80" align="right"><? echo $issueRow[csf("cons_quantity")]; ?></td>
                                    <td width="80" align="right"><? echo number_format($issueRow[csf("cons_rate")],2); ?></td>
                                    <td align="right"><? echo number_format($issueRow[csf("cons_amount")],2); ?></td>
                                </tr>

                                <?
                                $j++;
                                $tot_issue_qny += $issueRow[csf("cons_quantity")];
                                $tot_issue_amount_qny += $issueRow[csf("cons_amount")];
                                $currentStockQnty[$issueRow[csf("store_id")]]["issueQnty"] += $issueRow[csf("cons_quantity")];
                                $summary_array[$issueRow[csf("store_id")]]['issue'] += $issueRow[csf("cons_quantity")];
                            }
                            //print_r($summary_array);
                            unset($j);
                            ?>
                            <tr bgcolor="#CCCCCC" style="font-weight: bold ">
                                <td colspan="11" align="right"><b>Total</b></td>
                                <td align="right"><? echo $tot_issue_qny; ?></td>
                                <td>&nbsp;</td>
                                <td align="right"><? echo number_format($tot_issue_amount_qny,2); ?></td>
                            </tr>
                        </table>

                    </div>
                </div>
            </fieldset>

            <fieldset style="width:550px;float: left;margin-top: 5px;">
                <table style="width:550px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
                    <thead>
                        <tr>
                            <td colspan="6" style="font-size:15px; font-weight:bold">Current Stock Status</td>
                        </tr>
                        <tr>
                            <th width="150" align="center">Company</th>
                            <th width="80" align="center">Store</th>
                            <th width="80" align="center">Total Stock</th>
                            <th width="80" align="center">Intac</th>
                            <th width="80" align="center">Loose</th>
                            <th width="" align="center">Rejected</th>
                        </tr>
                    </thead>
                    <?
                    $k = 1;
                    foreach ($summary_array as $store => $sdata) { //[$receiveRow[csf("store_id")]]['issue']
                        $total_stock = $sdata["receive"] - $sdata["issue"];
                        if (!in_array($cbo_company_name, $companyCheck)) {
                            $curr_company = $companyArr[$cbo_company_name];
                        } else {
                            $curr_company = "";
                        }
                        if ($k % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('ctr_<? echo $k; ?>', '<? echo $bgcolor; ?>')" id="ctr_<? echo $k; ?>">
                            <td><? echo $curr_company; ?></td>
                            <td><? echo $storeArr[$store]; ?></td>
                            <td align="right"><? echo $total_stock; ?></td>
                            <td align="right"><? ?></td>
                            <td align="right"><? ?></td>
                            <td align="right"><? echo $sdata["reject"]; ?></td>
                        </tr>
                        <?
                        $companyCheck[$cbo_company_name] = $cbo_company_name;
                        $k++;
                    }
                    unset($k);
                    ?>
                </table>
            </fieldset>

            <fieldset style="width:800px;float: left;margin-top: 5px;">
                <table style="width:800px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
                    <thead>
                        <tr>
                            <td colspan="8" style="font-size:15px; font-weight:bold">Stock Summary</td>
                        </tr>
                        <tr>
                            <th width="150" align="center">Count</th>
                            <th width="150" align="center">Composition</th>
                            <th width="150" align="center">Company</th>
                            <th width="80" align="center">Store</th>
                            <th width="80" align="center">Receive Qnty</th>
                            <th width="80" align="center">Issue Qny</th>
                            <th width="80" align="center">Stock In Hand</th>
                            <th width="" align="center">Rejected Qnty</th>

                        </tr>
                    </thead>
                    <?
                    $k = 1;
                    foreach ($summary_array as $store => $sdata) { //[$receiveRow[csf("store_id")]]['issue']
                        $total_stock = $sdata["receive"] - $sdata["issue"];
                        $receiveQnty = $sdata["receive"];
                        $issueQnty = $sdata["issue"];
                        if (!in_array($yarnCountArr[$row[csf("yarn_count_id")]], $count_Check)) {
                            $summ_company = $companyArr[$cbo_company_name];
                            $compo = $row[csf("product_name_details")];
                            $count = $yarnCountArr[$row[csf("yarn_count_id")]];
                        } else {
                            $summ_company = "";
                            $compo = "";
                            $count = "";
                        }
                        if ($k % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('str_<? echo $k; ?>', '<? echo $bgcolor; ?>')" id="str_<? echo $k; ?>">
                            <td align="center"><? echo $count; ?></td>
                            <td><? echo $compo; ?></td>
                            <td><? echo $summ_company; ?></td>
                            <td><? echo $storeArr[$store]; ?></td>
                            <td align="right"><? echo $receiveQnty ?></td>
                            <td align="right"><? echo $issueQnty ?></td>
                            <td align="right"><? echo $total_stock; ?></td>
                            <td align="right"><? echo $sdata["reject"]; ?></td>
                        </tr>
                        <?
                        $count_Check[$yarnCountArr[$row[csf("yarn_count_id")]]] = $yarnCountArr[$row[csf("yarn_count_id")]];
                        $k++;
                    }
                    ?>
                </table>
            </fieldset>

        </div>
        <?
    }
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename";
    exit();
}

if($action == 'generate_report_lot_wise'){
$process = array( &$_POST );

extract(check_magic_quote_gpc( $process ));

$cbo_company_name 	    =   str_replace("'","",$cbo_company_name);
$cbo_supplier_name 	    =   str_replace("'","",$cbo_supplier_name);
$hidden_prod_no 		=   str_replace("'","",$hidden_prod_no);
$txt_lot_no 		    =   str_replace("'","",$txt_lot_no);
$txt_date_from 		    =   str_replace("'","",$from_date);
$txt_date_to 		    =   str_replace("'","",$to_date);

$sql_cond = ""; $lot_cond = "";

if(trim($txt_lot_no) != "") $sql_cond .=" and b.lot = '$txt_lot_no'"; $lot_cond =" and b.lot = '$txt_lot_no'";
if($cbo_supplier_name > 0) $sql_cond .=" and a.supplier_id = $cbo_supplier_name";
if($db_type==0)
{
    if($txt_date_from !="" && $txt_date_to !=""){
        $sql_cond .=" and a.transaction_date between '".change_date_format($txt_date_from, "yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' ";
    }
    $select_transaction_date="DATE_FORMAT(a.transaction_date,'%d-%m-%Y') as ISSUE_DATE";
    $select_program_date="DATE_FORMAT(f.program_date,'%d-%m-%Y') as ISSUE_DATE";
}else {
    if ($txt_date_from != "" && $txt_date_to != ""){
        $sql_cond .= " and a.transaction_date between '" . date("j-M-Y", strtotime($txt_date_from)) . "' and '" . date("j-M-Y", strtotime($txt_date_to)) . "' ";
    }
    $select_transaction_date=" to_char(a.transaction_date,'DD-MM-YYYY') as ISSUE_DATE";
    $select_program_date=" to_char(f.program_date,'DD-MM-YYYY') as ISSUE_DATE";
}
$company_arr = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
$supplier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
$totalRecieveSummary = 0;
$sqlReceive="select  c.recv_number as RECV_NUMBER, a.id as TRANS_ID, $select_transaction_date, a.cons_quantity as RECEIVED_QTY,
   e.yarn_count as YARN_COUNT, d.brand_name as BRAND_NAME, f.color_name as COLOR_NAME, b.id as PROD_ID, b.item_group_id as ITEM_GROUP_ID,
   b.sub_group_name as SUB_GROUP_NAME, b.item_description as ITEM_DESCRIPTION, c.knitting_company as KNITTING_COMPANY, c.knitting_source as KNITTING_SOURCE, 
   b.product_name_details as PRODUCT_NAME_DETAILS, b.item_code as ITEM_CODE, b.item_size as ITEM_SIZE, b.model as MODEL, 
   b.item_number as ITEM_NUMBER, b.yarn_comp_percent1st as YARN_COMP_PERCENT1ST, b.yarn_comp_percent2nd as YARN_COMP_PERCENT2ND, a.supplier_id as SUPPLIER_ID,
   b.yarn_comp_type1st as YARN_COMP_TYPE1ST, a.inserted_by as INSERTED_BY, b.yarn_comp_type2nd as YARN_COMP_TYPE2ND, b.lot as LOT,
   a.requisition_no as REQUISITION_NO
from inv_transaction a,
     product_details_master b left join lib_brand d on d.id = b.brand left join lib_yarn_count e on e.id = b.yarn_count_id left join lib_color f on f.id = b.color,
     inv_receive_master c
where a.prod_id = b.id and a.mst_id=c.id and c.entry_form = 1 and a.company_id=$cbo_company_name and a.transaction_type in (1) and a.status_active=1 
  and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond order by c.id desc";


    $sql_receive_result=sql_select($sqlReceive);
    $receiveDataArr = array(); $requID = array();

    foreach ($sql_receive_result as $key => $receiveData){
        $parcent1st = "";
        if($receiveData["YARN_COMP_PERCENT1ST"] > 0){
            $parcent1st = $receiveData["YARN_COMP_PERCENT1ST"]."%";
        }
        $parcent2nd = "";
        if($receiveData["YARN_COMP_PERCENT2ND"] > 0 ){
            $parcent2nd = $receiveData["YARN_COMP_PERCENT2ND"]."%";
        }
        $compositionStr = $composition[$receiveData["YARN_COMP_TYPE1ST"]].' '.$parcent1st.' '.$composition[$receiveData["YARN_COMP_TYPE2ND"]].' '.$parcent2nd;
        $item_key = $receiveData['ISSUE_NUMBER']."*##*".$receiveData["REQUISITION_NO"]."*##*".$compositionStr."*##*".$receiveData["COLOR"]."*##*".$receiveData["YARN_COUNT"]."*##*".$receiveData["BRAND_NAME"]."*##*".$receiveData["LOT"];
        if($receiveData["REQUISITION_NO"] > 0){
            array_push($requID, $receiveData["REQUISITION_NO"]);
        }

        $receiveDataArr[$item_key]['party'] = $supplier_arr[$receiveData['SUPPLIER_ID']];
        $receiveDataArr[$item_key]['recv_number'] = $receiveData['RECV_NUMBER'];
        $receiveDataArr[$item_key]['date'] = $receiveData["ISSUE_DATE"];
        $receiveDataArr[$item_key]['count'] = $receiveData["YARN_COUNT"];
        $receiveDataArr[$item_key]['brand'] = $receiveData["BRAND_NAME"];
        $receiveDataArr[$item_key]['composition'] = $compositionStr;
        $receiveDataArr[$item_key]['lot'] = $receiveData["LOT"];
        $receiveDataArr[$item_key]['receive_qty'] += $receiveData["RECEIVED_QTY"];
        $receiveDataArr[$item_key]['requ_no'] = $receiveData["REQUISITION_NO"];
        $totalRecieveSummary += $receiveDataArr[$item_key]['receive_qty'];
    }

    $requIDUnique = array_chunk(array_unique($requID),999, true);
    $counter = false;
    $requ_id_cond = "";
    foreach ($requIDUnique as $key => $value){
        if($counter){
            $requ_id_cond .= " or b.requisition_no in (".implode(',', $value).")";
        }else{
            $requ_id_cond .= " and b.requisition_no in (".implode(',', $value).")";
        }
        $counter = true;
    }
    $program_arr_receive = [];
    $orderInfoArrReq = [];
    if(count($requIDUnique) > 0) {
        $program_arr_receive = return_library_array("select a.id, b.requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b where a.id = b.knit_id $requ_id_cond", "requisition_no", "id");
    }
    $totalProgramSummery = 0;
    $sqlProgram="SELECT f.id as PROGRAM_ID, f.knitting_source as KNITTING_SOURCE, $select_program_date, f.knitting_party as KNITTING_PARTY, f.program_qnty as PROGRAM_QNTY,
       e.yarn_count AS yarn_count, d.brand_name AS brand_name, b.id AS prod_id, b.item_group_id AS item_group_id, b.sub_group_name AS sub_group_name,
       b.item_description AS item_description, b.product_name_details AS product_name_details, b.item_code AS item_code, b.item_size AS item_size,
       b.model AS model, b.item_number AS item_number, b.yarn_comp_percent1st AS yarn_comp_percent1st, b.yarn_comp_percent2nd AS yarn_comp_percent2nd,
       b.yarn_comp_type1st  AS yarn_comp_type1st, b.yarn_comp_type2nd  AS yarn_comp_type2nd, b.lot AS lot
        FROM
             inv_material_allocation_dtls  a, product_details_master b LEFT JOIN lib_brand   d ON d.id = b.brand LEFT JOIN lib_yarn_count e ON e.id = b.yarn_count_id,
             ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls f
        WHERE
              b.company_id = $cbo_company_name $lot_cond  and a.item_id = b.id and a.status_active = 1 and a.is_deleted = 0 and a.booking_no = c.booking_no
              and a.item_category = 1 and c.id = f.mst_id and c.company_id = $cbo_company_name
        group by
              f.id, f.knitting_source, f.program_qnty, f.knitting_party,  e.yarn_count, f.program_date, d.brand_name, b.id, b.item_group_id, b.sub_group_name, b.item_description, b.product_name_details,
              b.item_code, b.item_size, b.model, b.item_number, b.yarn_comp_percent1st, b.yarn_comp_percent2nd, b.yarn_comp_type1st,b.yarn_comp_type2nd,b.lot";

        $sql_progream_result=sql_select($sqlProgram);
        $programDataArr = array();

        foreach ($sql_progream_result as $key => $programData){
            $parcent1st = "";
            if($programData["YARN_COMP_PERCENT1ST"] > 0){
                $parcent1st = $programData["YARN_COMP_PERCENT1ST"]."%";
            }
            $parcent2nd = "";
            if($programData["YARN_COMP_PERCENT2ND"] > 0 ){
                $parcent2nd = $programData["YARN_COMP_PERCENT2ND"]."%";
            }
            $compositionStr = $composition[$programData["YARN_COMP_TYPE1ST"]].' '.$parcent1st.' '.$composition[$programData["YARN_COMP_TYPE2ND"]].' '.$parcent2nd;
            $item_key = $programData['PROGRAM_ID']."*##*".$programData["KNITTING_PARTY"]."*##*".$compositionStr."*##*".$programData["COLOR"]."*##*".$programData["YARN_COUNT"]."*##*".$programData["BRAND_NAME"]."*##*".$programData["LOT"];

            if($programData['KNITTING_SOURCE'] == 1){
                $party = $company_arr[$programData['KNITTING_PARTY']];
            }else{
                $party = $supplier_arr[$programData['KNITTING_PARTY']];
            }

            $programDataArr[$item_key]['party'] = $party;
            $programDataArr[$item_key]['program_no'] = $programData['PROGRAM_ID'];
            $programDataArr[$item_key]['date'] = $programData["ISSUE_DATE"];
            $programDataArr[$item_key]['count'] = $programData["YARN_COUNT"];
            $programDataArr[$item_key]['brand'] = $programData["BRAND_NAME"];
            $programDataArr[$item_key]['composition'] = $compositionStr;
            $programDataArr[$item_key]['lot'] = $programData["LOT"];
            $programDataArr[$item_key]['program_qnty'] += $programData["PROGRAM_QNTY"];
            $totalProgramSummery +=  $programDataArr[$item_key]['program_qnty'];
        }
    $totalIssueSummary = 0; $totalIssueWithoutProgramSummary = 0;
    $sqlIssue="select c.issue_number as ISSUE_NUMBER, a.id as TRANS_ID, $select_transaction_date, a.cons_quantity as ISSUE_QTY,
       c.loan_party as LOAN_PARTY, e.yarn_count as YARN_COUNT, d.brand_name as BRAND_NAME, f.color_name as COLOR_NAME, b.id as PROD_ID,
       b.item_group_id as ITEM_GROUP_ID, b.sub_group_name as SUB_GROUP_NAME, b.item_description as ITEM_DESCRIPTION, c.knit_dye_source as KNIT_DYE_SOURCE,
       b.product_name_details as PRODUCT_NAME_DETAILS, b.item_code as ITEM_CODE, b.item_size as ITEM_SIZE, c.issue_basis as ISSUE_BASIS, c.knit_dye_company as KNIT_DYE_COMPANY,  
       b.model as MODEL, b.item_number as ITEM_NUMBER, b.yarn_comp_percent1st as YARN_COMP_PERCENT1ST, a.buyer_id as BUYER_ID, 
       b.yarn_comp_percent2nd as YARN_COMP_PERCENT2ND, c.received_id as RECEIVE_ID, b.yarn_comp_type1st as YARN_COMP_TYPE1ST, a.inserted_by as INSERTED_BY,
       b.yarn_comp_type2nd as YARN_COMP_TYPE2ND, b.lot as LOT, a.requisition_no as REQUISITION_NO, c.buyer_job_no as BUYER_JOB_NO
    from inv_transaction a,
         product_details_master b left join lib_brand d on d.id = b.brand left join lib_yarn_count e on e.id = b.yarn_count_id left join lib_color f on f.id = b.color,
         inv_issue_master c
    where a.prod_id = b.id and a.mst_id=c.id and c.entry_form = 3 and a.company_id=$cbo_company_name and a.transaction_type in (2) and a.status_active=1 
      and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond order by c.id desc";
    //echo $sqlIssue;

        $sql_issue_result=sql_select($sqlIssue);
        $issueDataArr = array(); $issueDataWithoutProgramArr = array();  $requID = array();

        foreach ($sql_issue_result as $key => $issueData){
            $parcent1st = "";
            if($issueData["YARN_COMP_PERCENT1ST"] > 0){
                $parcent1st = $issueData["YARN_COMP_PERCENT1ST"]."%";
            }
            $parcent2nd = "";
            if($issueData["YARN_COMP_PERCENT2ND"] > 0 ){
                $parcent2nd = $issueData["YARN_COMP_PERCENT2ND"]."%";
            }
            $compositionStr = $composition[$issueData["YARN_COMP_TYPE1ST"]].' '.$parcent1st.' '.$composition[$issueData["YARN_COMP_TYPE2ND"]].' '.$parcent2nd;
            $item_key = $issueData['ISSUE_NUMBER']."*##*".$issueData["REQUISITION_NO"]."*##*".$compositionStr."*##*".$issueData["COLOR"]."*##*".$issueData["YARN_COUNT"]."*##*".$issueData["BRAND_NAME"]."*##*".$issueData["LOT"];
            if($issueData["REQUISITION_NO"] > 0){
                array_push($requID, $issueData["REQUISITION_NO"]);
            }

            if($issueData['KNIT_DYE_SOURCE'] == 1){
                $party = $company_arr[$issueData['KNIT_DYE_COMPANY']];
            }else{
                $party = $supplier_arr[$issueData['KNIT_DYE_COMPANY']];
            }
            if($issueData["REQUISITION_NO"] > 0){
                $issueDataArr[$item_key]['party'] = $party;
                $issueDataArr[$item_key]['issue_number'] = $issueData['ISSUE_NUMBER'];
                $issueDataArr[$item_key]['date'] = $issueData["ISSUE_DATE"];
                $issueDataArr[$item_key]['count'] = $issueData["YARN_COUNT"];
                $issueDataArr[$item_key]['brand'] = $issueData["BRAND_NAME"];
                $issueDataArr[$item_key]['composition'] = $compositionStr;
                $issueDataArr[$item_key]['lot'] = $issueData["LOT"];
                $issueDataArr[$item_key]['issue_qty'] += $issueData["ISSUE_QTY"];
                $issueDataArr[$item_key]['requ_no'] = $issueData["REQUISITION_NO"];
                $totalIssueSummary += $issueDataArr[$item_key]['issue_qty'];
            }else{
                $issueDataWithoutProgramArr[$item_key]['party'] = $party;
                $issueDataWithoutProgramArr[$item_key]['issue_number'] = $issueData['ISSUE_NUMBER'];
                $issueDataWithoutProgramArr[$item_key]['date'] = $issueData["ISSUE_DATE"];
                $issueDataWithoutProgramArr[$item_key]['count'] = $issueData["YARN_COUNT"];
                $issueDataWithoutProgramArr[$item_key]['brand'] = $issueData["BRAND_NAME"];
                $issueDataWithoutProgramArr[$item_key]['composition'] = $compositionStr;
                $issueDataWithoutProgramArr[$item_key]['lot'] = $issueData["LOT"];
                $issueDataWithoutProgramArr[$item_key]['issue_qty'] += $issueData["ISSUE_QTY"];
                $issueDataWithoutProgramArr[$item_key]['requ_no'] = $issueData["REQUISITION_NO"];
                $totalIssueWithoutProgramSummary += $issueDataWithoutProgramArr[$item_key]['issue_qty'];
            }
        }

        $requIDUnique = array_chunk(array_unique($requID),999, true);
        $counter = false;
        $requ_id_cond = "";
        foreach ($requIDUnique as $key => $value){
            if($counter){
                $requ_id_cond .= " or b.requisition_no in (".implode(',', $value).")";
            }else{
                $requ_id_cond .= " and b.requisition_no in (".implode(',', $value).")";
            }
            $counter = true;
        }
        $program_arr_issue = [];
        $orderInfoArrReq = [];
        if(count($requIDUnique) > 0) {
            $program_arr_issue = return_library_array("select a.id, b.requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b where a.id = b.knit_id $requ_id_cond", "requisition_no", "id");
        }

     $totalReturnedSummary = 0;

    $sqlIssueReturn="select c.recv_number as RECV_NUMBER, a.id as TRANS_ID, $select_transaction_date, a.cons_quantity as RETURN_QTY,
           e.yarn_count as YARN_COUNT, d.brand_name as BRAND_NAME, f.color_name as COLOR_NAME, b.id as PROD_ID, c.challan_no as CHALLAN_NO,
           b.item_group_id as ITEM_GROUP_ID, b.sub_group_name as SUB_GROUP_NAME, b.item_description as ITEM_DESCRIPTION, c.issue_id as ISSUE_ID,
           b.product_name_details as PRODUCT_NAME_DETAILS, b.item_code as ITEM_CODE, b.item_size as ITEM_SIZE, c.knitting_company as KNITTING_COMPANY,  
           b.yarn_comp_percent1st as YARN_COMP_PERCENT1ST, b.yarn_comp_percent2nd as YARN_COMP_PERCENT2ND, b.yarn_comp_type1st as YARN_COMP_TYPE1ST,
           b.yarn_comp_type2nd as YARN_COMP_TYPE2ND, b.lot as LOT, h.buyer_job_no as BUYER_JOB_NO, c.knitting_source as KNITTING_SOURCE
        from inv_transaction a,
             product_details_master b left join lib_brand d on d.id = b.brand left join lib_yarn_count e on e.id = b.yarn_count_id left join lib_color f on f.id = b.color,
             inv_receive_master c left join inv_issue_master h on h.id = c.issue_id
        where a.prod_id = b.id and a.mst_id=c.id and c.entry_form = 9 and a.company_id=$cbo_company_name and a.transaction_type in (4) and a.status_active=1 
          and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond order by c.id desc";
    //    echo $sqlIssueReturn;
    $div_width="1210px";
    $table_width=1190;
    $sql_issue_return_result=sql_select($sqlIssueReturn);
    $issueReturnDataArr = array(); $job_no_alt = array(); $issue_id = array();

    foreach ($sql_issue_return_result as $key => $issueReturnData){
        $parcent1st = "";
        if($issueReturnData["YARN_COMP_PERCENT1ST"] > 0){
            $parcent1st = $issueReturnData["YARN_COMP_PERCENT1ST"]."%";
        }
        $parcent2nd = "";
        if($issueReturnData["YARN_COMP_PERCENT2ND"] > 0 ){
            $parcent2nd = $issueReturnData["YARN_COMP_PERCENT2ND"]."%";
        }
        $compositionStr = $composition[$issueReturnData["YARN_COMP_TYPE1ST"]].' '.$parcent1st.' '.$composition[$issueReturnData["YARN_COMP_TYPE2ND"]].' '.$parcent2nd;
        $item_key = $issueReturnData['RECV_NUMBER']."*##*".$compositionStr."*##*".$issueReturnData["COLOR"]."*##*".$issueReturnData["YARN_COUNT"]."*##*".$issueReturnData["BRAND_NAME"]."*##*".$issueReturnData["LOT"];
        if($issueReturnData['KNITTING_SOURCE'] == 1){
            $party = $company_arr[$issueReturnData['KNITTING_COMPANY']];
        }else{
            $party = $supplier_arr[$issueReturnData['KNITTING_COMPANY']];
        }
        if($issueReturnData["ISSUE_ID"] > 0){
            array_push($issue_id, $issueReturnData["ISSUE_ID"]);
        }
        $issueReturnDataArr[$item_key]['party'] = $party;
        $issueReturnDataArr[$item_key]['date'] = $issueReturnData["ISSUE_DATE"];
        $issueReturnDataArr[$item_key]['count'] = $issueReturnData["YARN_COUNT"];
        $issueReturnDataArr[$item_key]['brand'] = $issueReturnData["BRAND_NAME"];
        $issueReturnDataArr[$item_key]['composition'] = $compositionStr;
        $issueReturnDataArr[$item_key]['lot'] = $issueReturnData["LOT"];
        $issueReturnDataArr[$item_key]['return_qty'] += $issueReturnData["RETURN_QTY"];
        $issueReturnDataArr[$item_key]['recv_number'] = $issueReturnData["RECV_NUMBER"];
        $issueReturnDataArr[$item_key]['issue_id'] = $issueReturnData["ISSUE_ID"];

        $totalReturnedSummary += $issueReturnDataArr[$item_key]['return_qty'];
    }

    $issueIdUnique = array_chunk(array_unique($issue_id),999, true);
    $counter = false;
    $issue_id_cond = "";
    foreach ($issueIdUnique as $key => $value){
        if($counter){
            $issue_id_cond .= " or mst_id in ('".implode("','", $value)."')";
        }else{
            $issue_id_cond .= " and mst_id in ('".implode("','", $value)."')";
        }
        $counter = true;
    }
    $program_arr_return = [];
    if(count($issue_id) > 0){
        $valid_issue = array();
        $req_no = sql_select("SELECT requisition_no as REQUISITION_NO, id as ID from inv_transaction where status_active = 1 and is_deleted = 0 and transaction_type = 2 $issue_id_cond");
        foreach ($req_no as $value){
            if($value['REQUISITION_NO'] != ""){
                array_push($valid_issue, $value['ID']);
            }
        }
       $program_arr_return = return_library_array("select a.id, c.mst_id from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, inv_transaction c where a.id = b.knit_id and b.requisition_no = c.requisition_no and c.id in (".implode(",", $valid_issue).")", "mst_id", "id");
    }

    ob_start();
?>
<div style="width:<? echo $div_width; ?>">
    <fieldset style="width:<? echo $div_width; ?>">
        <table width="<? echo $table_width; ?>" border="0" cellpadding="2" cellspacing="0" class="" rules="all" id="" align="left">
            <tr class="form_caption" style="border:none;">
                <td colspan="11" align="center" style="border:none;font-size:16px; font-weight:bold; padding: 0px 2px;" ><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
            </tr>
            <?
            if ($txt_date_from != "" && $txt_date_to != ""){
                ?>
                <tr style="border:none;">
                    <td colspan="11" align="center" style="border:none; font-size:14px; padding: 3px 2px;">
                        <strong>Date Range : <? echo $txt_date_from; ?> To <? echo $txt_date_to; ?></strong>
                    </td>
                </tr>
                <?
            }
            ?>
            <tr style="border:none;">
                <td colspan="11" align="center" style="border:none; padding: 4px 2px;">
                    <strong style="font-size:16px;">Lot Wise Yarn Transaction Details</strong>
                </td>
            </tr>
        </table>
        <br>
        <br>
        <table width="450" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"  align="left">
            <tbody bgcolor="#ffffff">
                <tr bgcolor="#d3d3d3">
                   <td align="center" style="vertical-align: middle; padding: 3px;" ><strong>Lot Wise Yarn Details Information</strong></td>
                   <td align="center" style="vertical-align: middle; padding: 3px;"><strong>Qty In KG</strong></td>
                </tr>
                <tr>
                   <td align="left" style="vertical-align: middle; padding: 2px;"><strong>Yarn Received</strong></td>
                    <td align="right" style="vertical-align: middle; padding: 2px;"><?=number_format($totalRecieveSummary, 2)?></td>
                </tr>
                <tr>
                   <td align="left" style="vertical-align: middle; padding: 2px;"><strong>Knitting Program</strong></td>
                    <td align="right" style="vertical-align: middle; padding: 2px;"><?=number_format($totalProgramSummery, 2)?></td>
                </tr>
                <tr>
                   <td align="left" style="vertical-align: middle; padding: 2px;"><strong>Program Wise Yarn Delivery</strong></td>
                    <td align="right" style="vertical-align: middle; padding: 2px;"><?=number_format($totalIssueSummary, 2)?></td>
                </tr>
                <tr>
                    <td align="left" style="vertical-align: middle; padding: 2px;"><strong>Program Wise Yarn Delivery Balance</strong></td>
                    <td align="right" style="vertical-align: middle; padding: 2px;"><?=number_format(($totalProgramSummery - $totalIssueSummary), 2)?></td>
                </tr>
                <tr>
                    <td align="left" style="vertical-align: middle; padding: 2px;"><strong>Without Program Yarn Delivery</strong></td>
                    <td align="right" style="vertical-align: middle; padding: 2px;"><?=number_format($totalIssueWithoutProgramSummary, 2)?></td>
                </tr>
                <tr>
                    <td align="left" style="vertical-align: middle; padding: 2px;"><strong>Total Yarn Delivery</strong></td>
                    <td align="right" style="vertical-align: middle; padding: 2px;"><?=number_format($totalIssueWithoutProgramSummary+$totalIssueSummary, 2)?></td>
                </tr>
                <tr>
                   <td align="left" style="vertical-align: middle; padding: 2px;"><strong>Yarn Return Received</strong></td>
                    <td align="right" style="vertical-align: middle; padding: 2px;"><?=number_format($totalReturnedSummary, 2)?></td>
                </tr>
                <tr>
                    <td align="left" style="vertical-align: middle; padding: 2px;" colspan="2"></td>
                </tr>
                <tr>
                    <td align="left" style="vertical-align: middle; padding: 2px;"><strong>Yarn Stock</strong></td>
                    <td align="right" style="vertical-align: middle; padding: 2px;"><strong><?=number_format((($totalRecieveSummary + $totalReturnedSummary) - ($totalIssueWithoutProgramSummary+$totalIssueSummary)), 2)?></strong></td>
                </tr>

            </tbody>
        </table>
        <table width="<? echo $table_width; ?>" border="0" cellpadding="2" cellspacing="0" class="" rules="all" id="" align="left">
            <tr style="border:none;">
                <td colspan="11" align="right" style="border:none; padding: 4px 2px;">
                    <strong>Report Date: <?=date('d-m-Y')?></strong>
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="11" align="right" style="border:none; padding: 4px 2px;">
                    <strong id="printDate"></strong>
                </td>
            </tr>
        </table>
        <br/>
        <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"  align="left">
            <thead>
            <tr>
                <th style="padding: 0px 2px;" width="30">SL</th>
                <th style="padding: 0px 2px;" width="140">Challan Type</th>
                <th style="padding: 0px 2px;" width="160">Party Name</th>
                <th style="padding: 0px 2px;" width="80">Date</th>
                <th style="padding: 0px 2px;" width="110">Challan No.</th>
                <th style="padding: 0px 2px;" width="110">Program No.</th>
                <th style="padding: 0px 2px;" width="100">Brand</th>
                <th style="padding: 0px 2px;" width="80">Count</th>
                <th style="padding: 0px 2px;" width="160">Composition</th>
                <th style="padding: 0px 2px;" width="80">Yarn Lot</th>
                <th style="padding: 0px 2px;">Qty. In KG</th>

            </tr>
            </thead>
        </table>
        <br/>
        <div style="width:<? echo $div_width; ?>; overflow-y: scroll; max-height:260px;" id="scroll_body">
            <table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="1" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                <tbody>
                <?
                $counter = 0;  $total = 0;
                if(count($receiveDataArr) > 0){
                    $counter += 1;
                    $loopIndex = 0;
                    foreach ($receiveDataArr as $key => $itemData){
                        $qty = $itemData['receive_qty'];
                        $total += $qty;
                        if($counter%2==0) $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                        if($loopIndex > 0){
                            ?>
                            <tr bgcolor="<?=$bgcolor?>">
                                <td width="160" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['party']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['date']?></td>
                                <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['recv_number']?></td>
                                <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$program_arr_receive[$itemData['requ_no']]?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="100"><?=$itemData['brand']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['count']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="160"><?=$itemData['composition']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80"><?=$itemData['lot']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" align="right"><?=number_format($qty, 2)?></td>
                            </tr>

                            <?
                        }else{
                            ?>
                            <tr bgcolor="<?=$bgcolor?>">
                                <td width="30" align="center" style="vertical-align: middle;padding: 0px 2px;" rowspan="<?=count($receiveDataArr)+3?>"><?=$counter?></td>
                                <td width="140" style="vertical-align: middle; padding: 0px 2px;" rowspan="<?=count($receiveDataArr)+3?>">Yarn Received</td>
                                <td width="160" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['party']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['date']?></td>
                                <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['recv_number']?></td>
                                <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$program_arr_receive[$itemData['requ_no']]?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="100"><?=$itemData['brand']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['count']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="160"><?=$itemData['composition']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80"><?=$itemData['lot']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" align="right"><?=number_format($qty, 2)?></td>
                            </tr>
                            <?
                        }
                        $loopIndex++;
                    }
                    ?>
                    <tr><td colspan="9"></td></tr>
                    <tr bgcolor="#d3d3d3">
                        <td style="padding: 0px 2px;" colspan="8" align="right"><strong>Total</strong></td>
                        <td  style="padding: 0px 2px;" align="right"><strong><?=number_format($total, 2)?></strong></td>
                    </tr>
                    <tr><td colspan="9"></td></tr>
                <?
                }
                if(count($programDataArr) > 0){
                    $total = 0;
                    $counter += 1;
                    $loopIndex = 0;
                    foreach ($programDataArr as $key => $itemData){
                        $qty = $itemData['program_qnty'];
                        $total += $qty;
                        if($counter%2==0) $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                        if($loopIndex > 0){
                            ?>
                            <tr bgcolor="<?=$bgcolor?>">
                                <td width="160" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['party']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['date']?></td>
                                <td width="110" style="vertical-align: middle; padding: 0px 2px;" ></td>
                                <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['program_no']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="100"><?=$itemData['brand']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['count']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="160"><?=$itemData['composition']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80"><?=$itemData['lot']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" align="right"><?=number_format($qty, 2)?></td>
                            </tr>

                            <?
                        }else{
                            ?>
                            <tr bgcolor="<?=$bgcolor?>">
                                <td width="30" align="center" style="vertical-align: middle;padding: 0px 2px;" rowspan="<?=count($programDataArr)+3?>"><?=$counter?></td>
                                <td width="140" style="vertical-align: middle; padding: 0px 2px;" rowspan="<?=count($programDataArr)+3?>">Knitting Program</td>
                                <td width="160" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['party']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['date']?></td>
                                <td width="110" style="vertical-align: middle; padding: 0px 2px;" ></td>
                                <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['program_no']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="100"><?=$itemData['brand']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['count']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="160"><?=$itemData['composition']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80"><?=$itemData['lot']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" align="right"><?=number_format($qty, 2)?></td>
                            </tr>
                            <?
                        }
                        $loopIndex++;
                    }
                    ?>
                    <tr><td colspan="9"></td></tr>
                    <tr bgcolor="#d3d3d3">
                        <td style="padding: 0px 2px;" colspan="8" align="right"><strong>Total</strong></td>
                        <td  style="padding: 0px 2px;" align="right"><strong><?=number_format($total, 2)?></strong></td>
                    </tr>
                    <tr><td colspan="9"></td></tr>
                <?
                }
                if(count($issueDataArr) > 0){
                    $total = 0;
                    $counter += 1;
                    $loopIndex = 0;
                    foreach ($issueDataArr as $key => $itemData){
                        $qty = $itemData['issue_qty'];
                        $total += $qty;
                        if($counter%2==0) $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                        if($loopIndex > 0){
                            ?>
                            <tr bgcolor="<?=$bgcolor?>">
                                <td width="160" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['party']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['date']?></td>
                                <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['issue_number']?></td>
                                <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$program_arr_issue[$itemData['requ_no']]?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="100"><?=$itemData['brand']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['count']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="160"><?=$itemData['composition']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80"><?=$itemData['lot']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" align="right"><?=number_format($qty, 2)?></td>
                            </tr>

                            <?
                        }else{
                            ?>
                            <tr bgcolor="<?=$bgcolor?>">
                                <td width="30" align="center" style="vertical-align: middle;padding: 0px 2px;" rowspan="<?=count($issueDataArr)+3?>"><?=$counter?></td>
                                <td width="140" style="vertical-align: middle; padding: 0px 2px;" rowspan="<?=count($issueDataArr)+3?>">Program Wise Yarn Delivery</td>
                                <td width="160" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['party']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['date']?></td>
                                <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['issue_number']?></td>
                                <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$program_arr_issue[$itemData['requ_no']]?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="100"><?=$itemData['brand']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['count']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="160"><?=$itemData['composition']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80"><?=$itemData['lot']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" align="right"><?=number_format($qty, 2)?></td>
                            </tr>
                            <?
                        }
                        $loopIndex++;
                    }
                    ?>
                    <tr><td colspan="9"></td></tr>
                    <tr bgcolor="#d3d3d3">
                        <td style="padding: 0px 2px;" colspan="8" align="right"><strong>Total</strong></td>
                        <td  style="padding: 0px 2px;" align="right"><strong><?=number_format($total, 2)?></strong></td>
                    </tr>
                    <tr><td colspan="9"></td></tr>
                <?
                }
                if(count($issueDataWithoutProgramArr) > 0){
                    $total = 0;
                    $counter += 1;
                    $loopIndex = 0;
                    foreach ($issueDataWithoutProgramArr as $key => $itemData){
                        $qty = $itemData['issue_qty'];
                        $total += $qty;
                        if($counter%2==0) $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                        if($loopIndex > 0){
                            ?>
                            <tr bgcolor="<?=$bgcolor?>">
                                <td width="160" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['party']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['date']?></td>
                                <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['issue_number']?></td>
                                <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$program_arr_issue[$itemData['requ_no']]?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="100"><?=$itemData['brand']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['count']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="160"><?=$itemData['composition']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80"><?=$itemData['lot']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" align="right"><?=number_format($qty, 2)?></td>
                            </tr>

                            <?
                        }else{
                            ?>
                            <tr bgcolor="<?=$bgcolor?>">
                                <td width="30" align="center" style="vertical-align: middle;padding: 0px 2px;" rowspan="<?=count($issueDataWithoutProgramArr)+3?>"><?=$counter?></td>
                                <td width="140" style="vertical-align: middle; padding: 0px 2px;" rowspan="<?=count($issueDataWithoutProgramArr)+3?>">Without Program Yarn Delivery</td>
                                <td width="160" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['party']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['date']?></td>
                                <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['issue_number']?></td>
                                <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$program_arr_issue[$itemData['requ_no']]?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="100"><?=$itemData['brand']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['count']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="160"><?=$itemData['composition']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" width="80"><?=$itemData['lot']?></td>
                                <td style="vertical-align: middle; padding: 0px 2px;" align="right"><?=number_format($qty, 2)?></td>
                            </tr>
                            <?
                        }
                        $loopIndex++;
                    }
                    ?>
                    <tr><td colspan="9"></td></tr>
                    <tr bgcolor="#d3d3d3">
                        <td style="padding: 0px 2px;" colspan="8" align="right"><strong>Total</strong></td>
                        <td  style="padding: 0px 2px;" align="right"><strong><?=number_format($total, 2)?></strong></td>
                    </tr>
                    <tr><td colspan="9"></td></tr>
                    <?
                }
                if(count($issueReturnDataArr) > 0){
                    $total = 0;
                    $counter += 1;
                    $loopIndex = 0;
                    foreach ($issueReturnDataArr as $key => $itemData){
                            $qty = $itemData['return_qty'];
                            $total += $qty;
                            if($counter%2==0) $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                            if($loopIndex > 0){
                                ?>
                                <tr bgcolor="<?=$bgcolor?>">
                                    <td width="160" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['party']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['date']?></td>
                                    <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['recv_number']?></td>
                                    <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$program_arr_return[$itemData['issue_id']]?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="100"><?=$itemData['brand']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['count']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="160"><?=$itemData['composition']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="80"><?=$itemData['lot']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" align="right"><?=number_format($qty, 2)?></td>
                                </tr>

                                <?
                            }else{
                                ?>
                                <tr bgcolor="<?=$bgcolor?>">
                                    <td width="30" align="center" style="vertical-align: middle;padding: 0px 2px;" rowspan="<?=count($issueReturnDataArr)+3?>"><?=$counter?></td>
                                    <td width="140" style="vertical-align: middle; padding: 0px 2px;" rowspan="<?=count($issueReturnDataArr)+3?>">Yarn Return Received</td>
                                    <td width="160" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['party']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['date']?></td>
                                    <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$itemData['recv_number']?></td>
                                    <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$program_arr_return[$itemData['issue_id']]?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="100"><?=$itemData['brand']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$itemData['count']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="160"><?=$itemData['composition']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="80"><?=$itemData['lot']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" align="right"><?=number_format($qty, 2)?></td>
                                </tr>
                                <?
                            }
                            $loopIndex++;
                        }
                        ?>
                        <tr><td colspan="9"></td></tr>
                        <tr bgcolor="#d3d3d3">
                            <td style="padding: 0px 2px;" colspan="8" align="right"><strong>Total</strong></td>
                            <td  style="padding: 0px 2px;" align="right"><strong><?=number_format($total, 2)?></strong></td>
                        </tr>
                        <tr><td colspan="9"></td></tr>
                    <?
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </fieldset>
</div>
<?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename";
    exit();
}

if($action == 'generate_report_lot_wise2'){
    $process = array( &$_POST );

    extract(check_magic_quote_gpc( $process ));

    $cbo_company_name 	    =   str_replace("'","",$cbo_company_name);
    $cbo_supplier_name 	    =   str_replace("'","",$cbo_supplier_name);
    $hidden_prod_no 		=   str_replace("'","",$hidden_prod_no);
    $txt_lot_no 		    =   str_replace("'","",$txt_lot_no);
    $txt_date_from 		    =   str_replace("'","",$from_date);
    $txt_date_to 		    =   str_replace("'","",$to_date);

    $sql_cond = ""; $lot_cond = "";

    if(trim($txt_lot_no) != "") $sql_cond .=" and b.lot = '$txt_lot_no'"; $lot_cond =" and b.lot = '$txt_lot_no'";
    if($cbo_supplier_name > 0) $sql_cond .=" and a.supplier_id = $cbo_supplier_name";
    if($db_type==0)
    {
        if($txt_date_from !="" && $txt_date_to !=""){
            $sql_cond .=" and a.transaction_date between '".change_date_format($txt_date_from, "yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' ";
        }
        $select_transaction_date="DATE_FORMAT(a.transaction_date,'%d-%m-%Y') as ISSUE_DATE";
        $select_program_date="DATE_FORMAT(f.program_date,'%d-%m-%Y') as ISSUE_DATE";
    }else {
        if ($txt_date_from != "" && $txt_date_to != ""){
            $sql_cond .= " and a.transaction_date between '" . date("j-M-Y", strtotime($txt_date_from)) . "' and '" . date("j-M-Y", strtotime($txt_date_to)) . "' ";
        }
        $select_transaction_date=" to_char(a.transaction_date,'DD-MM-YYYY') as ISSUE_DATE";
    }
    $company_arr = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $buyerArr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");

    $sqlIssue="select c.id as ISSUE_NUMBER, a.id as TRANS_ID, $select_transaction_date, a.prod_id as PROD_ID, a.cons_quantity as ISSUE_QTY, a.requisition_no as REQUISITION_NO, c.buyer_job_no as BUYER_JOB_NO
    from inv_transaction a, product_details_master b, inv_issue_master c
    where a.prod_id = b.id and a.mst_id=c.id and c.entry_form = 3 and a.company_id=$cbo_company_name and a.transaction_type = 2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond order by c.id desc";

    $sql_issue_result=sql_select($sqlIssue);
    $issueDataArr = []; $requID = []; $issue_id_arr = [];
    foreach ($sql_issue_result as $key => $issueData){
        $item_key = $issueData['ISSUE_NUMBER']."*##*".$issueData["REQUISITION_NO"]."*##*".$issueData["PROD_ID"]."*##*".$issueData["ISSUE_DATE"];
        if($issueData["REQUISITION_NO"] > 0){
            $requID[$issueData["REQUISITION_NO"]] = $issueData["REQUISITION_NO"];
        }
        $issue_id_arr[$issueData['ISSUE_NUMBER']] = $issueData['ISSUE_NUMBER'];
        $issueDataArr[$item_key]['issue_id'] = $issueData['ISSUE_NUMBER'];
        $issueDataArr[$item_key]['date'] = $issueData["ISSUE_DATE"];
        $issueDataArr[$item_key]['issue_qty'] += $issueData["ISSUE_QTY"];
        $issueDataArr[$item_key]['requ_no'] = $issueData["REQUISITION_NO"];
        $issueDataArr[$item_key]['prod'] = $issueData["PROD_ID"];
    }

    $requIDUnique = array_chunk($requID,999);
    $program_data_arr = [];
    if(count($requIDUnique) > 0) {
        $requ_id_cond = "";
        foreach ($requIDUnique as $key => $value) {
            if ($key == 0) {
                $requ_id_cond .= " a.requisition_no in (" . implode(',', $value) . ")";
            } else {
                $requ_id_cond .= " or a.requisition_no in (" . implode(',', $value) . ")";
            }
        }
        $sqlProgram = "SELECT f.id as PROGRAM_ID, f.program_qnty as PROGRAM_QNTY, c.booking_no as BOOKING_NO, a.prod_id as PROD_ID, c.buyer_id as BUYER_ID, sum(b.qnty) as ALLOCATED_QTY,
           a.requisition_no as REQUISITION_NO, a.yarn_qnty as YARN_QNTY
        FROM inv_material_allocation_dtls b, ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls f, ppl_yarn_requisition_entry a
        WHERE c.company_id = $cbo_company_name and a.status_active = 1 and a.is_deleted = 0 and c.id = f.mst_id and f.id = a.knit_id and b.booking_no = c.booking_no and a.prod_id = b.item_id
          and ($requ_id_cond) and b.status_active = 1 and b.is_deleted = 0 
        group by f.id, f.program_qnty, c.booking_no, a.prod_id, c.buyer_id, a.requisition_no, a.yarn_qnty";

        $sql_program_result = sql_select($sqlProgram);
        $booking_arr = [];
        foreach ($sql_program_result as $val) {
            $key = $val["PROD_ID"] . "#*#" . $val["REQUISITION_NO"];
            $program_data_arr[$key]['program'] = $val["PROGRAM_ID"];
            $program_data_arr[$key]['program_qty'] += $val["PROGRAM_QNTY"];
            $program_data_arr[$key]['booking'] = $val["BOOKING_NO"];
            $program_data_arr[$key]['buyer'] = $buyerArr[$val["BUYER_ID"]];
            $program_data_arr[$key]['requ_qty'] += $val["YARN_QNTY"];
            $program_data_arr[$key]['allocated_qty'] += $val["ALLOCATED_QTY"];
            $booking_arr[$val["BOOKING_NO"]] = $val["BOOKING_NO"];
        }
    }

    $issueIDUnique = array_chunk($issue_id_arr,999);
    $issueReturnDataArr = [];
    if(count($issueIDUnique) > 0){
        $issue_id_cond = "";
        foreach ($issueIDUnique as $key => $value) {
            if ($key == 0) {
                $issue_id_cond .= " c.issue_id in (" . implode(',', $value) . ")";
            } else {
                $issue_id_cond .= " or c.issue_id in (" . implode(',', $value) . ")";
            }
        }
        $sqlIssueReturn="select c.issue_id as ISSUE_ID, a.prod_id as PROD_ID,  a.cons_quantity as RETURN_QTY
        from inv_transaction a, product_details_master b, inv_receive_master c 
        where a.prod_id = b.id and a.mst_id=c.id and c.entry_form = 9 and a.company_id=$cbo_company_name and a.transaction_type = 4 and a.status_active=1 
          and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and ($issue_id_cond) order by c.id desc";

        $sql_issue_return_result=sql_select($sqlIssueReturn);

        foreach ($sql_issue_return_result as $key => $issueReturnData){
            $item_key = $issueReturnData['ISSUE_ID']."*##*".$issueReturnData["PROD_ID"];
            $issueReturnDataArr[$item_key]['return_qty'] += $issueReturnData["RETURN_QTY"];
        }
    }

    $bookingUnique = array_chunk($booking_arr,999);
    $job_arr = [];
    if(count($bookingUnique) > 0){
        $booking_cond = "";
        foreach ($bookingUnique as $key => $value) {
            if ($key == 0) {
                $booking_cond .= " booking_no in ('" . implode("','", $value) . "')";
            } else {
                $booking_cond .= " or booking_no in ('" . implode("','", $value) . "')";
            }
        }
        $sql_booking="select a.booking_no, b.job_no_mst, to_char(b.pub_shipment_date, 'dd-mm-YYYY') as s_date, b.po_number from wo_booking_mst a, wo_po_break_down b where a.job_no = b.job_no_mst and a.status_active = 1 and a.is_deleted = 0 and ($booking_cond) group by a.booking_no, b.job_no_mst, b.pub_shipment_date, b.po_number";
        $sql_booking_result=sql_select($sql_booking);

        foreach ($sql_booking_result as $key => $data){
            $job_arr[$data["BOOKING_NO"]]['booking'] = $data["BOOKING_NO"];
            $job_arr[$data["BOOKING_NO"]]['job'] = $data["JOB_NO_MST"];
            $job_arr[$data["BOOKING_NO"]]['ship_date'] = $data["S_DATE"];
            $job_arr[$data["BOOKING_NO"]]['order'][$key] = $data["PO_NUMBER"];
        }
    }

    $div_width="1560px";
    $table_width=1540;
    ob_start();
    ?>
    <div style="width:<? echo $div_width; ?>">
        <fieldset style="width:<? echo $div_width; ?>">
            <table width="<? echo $table_width; ?>" border="0" cellpadding="2" cellspacing="0" class="" rules="all" id="" align="left">
                <tr class="form_caption" style="border:none;">
                    <td colspan="11" align="center" style="border:none;font-size:16px; font-weight:bold; padding: 0px 2px;" ><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
                </tr>
                <?
                if ($txt_date_from != "" && $txt_date_to != ""){
                    ?>
                    <tr style="border:none;">
                        <td colspan="11" align="center" style="border:none; font-size:14px; padding: 3px 2px;">
                            <strong>Date Range : <? echo $txt_date_from; ?> To <? echo $txt_date_to; ?></strong>
                        </td>
                    </tr>
                    <?
                }
                ?>
                <tr style="border:none;">
                    <td colspan="11" align="center" style="border:none; padding: 4px 2px;">
                        <strong style="font-size:16px;">Lot Wise Yarn Transaction Details</strong>
                    </td>
                </tr>
            </table>
            <br>
            <table width="<? echo $table_width; ?>" border="0" cellpadding="2" cellspacing="0" class="" rules="all" id="" align="left">
                <tr style="border:none;">
                    <td colspan="11" align="right" style="border:none; padding: 4px 2px;">
                        <strong>Report Date: <?=date('d-m-Y')?></strong>
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="11" align="right" style="border:none; padding: 4px 2px;">
                        <strong id="printDate"></strong>
                    </td>
                </tr>
            </table>
            <br/>
            <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"  align="left">
                <thead>
                <tr>
                    <th style="padding: 0px 2px;" width="30">SL</th>
                    <th style="padding: 0px 2px;" width="80">Date</th>
                    <th style="padding: 0px 2px;" width="100">Job No.</th>
                    <th style="padding: 0px 2px;" width="140">Buyer</th>
                    <th style="padding: 0px 2px;" width="140">Order No.</th>
                    <th style="padding: 0px 2px;" width="90">Shipment Date</th>
                    <th style="padding: 0px 2px;" width="110">Booking No.</th>
                    <th style="padding: 0px 2px;" width="100">Allocated Qty.</th>
                    <th style="padding: 0px 2px;" width="100">Program No.</th>
                    <th style="padding: 0px 2px;" width="100">Program Qty.</th>
                    <th style="padding: 0px 2px;" width="100">Requisition Qty.</th>
                    <th style="padding: 0px 2px;" width="100">Requisition Balance</th>
                    <th style="padding: 0px 2px;" width="100">Issue Qty.</th>
                    <th style="padding: 0px 2px;" width="100">Issue Rtn. Qty.</th>
                    <th style="padding: 0px 2px;">Balance Qty.</th>

                </tr>
                </thead>
            </table>
            <br/>
            <div style="width:<? echo $div_width; ?>; overflow-y: scroll; max-height:260px;" id="scroll_body">
                <table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="1" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                    <tbody>
                    <?
                        $i = 1;
                        foreach ($issueDataArr as $k=> $v){
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                           ?>
                            <tr bgcolor="<?=$bgcolor?>">
                                <td align="center" valign="middle" style="padding: 1px 2px; font-size: 10pt;" width="30"><?=$i?></td>
                                <td align="center" valign="middle" style="padding: 1px 2px; font-size: 10pt;" width="80"><?=$v["date"]?></td>
                                <td align="left" valign="middle" style="padding: 1px 2px; font-size: 10pt;" width="100"><?=$job_arr[$program_data_arr[$v["prod"]."#*#".$v["requ_no"]]["booking"]]["job"]?></td>
                                <td align="left" valign="middle" style="padding: 1px 2px; font-size: 10pt;" width="140"><?=$program_data_arr[$v["prod"]."#*#".$v["requ_no"]]["buyer"]?></td>
                                <td align="left" valign="middle" style="padding: 1px 2px; font-size: 10pt;" width="140">
                                    <?
                                    $order_no = array_unique($job_arr[$program_data_arr[$v["prod"]."#*#".$v["requ_no"]]["booking"]]["order"]);
                                    echo implode(', ', $order_no);
                                    ?></td>
                                <td align="center" valign="middle" style="padding: 1px 2px; font-size: 10pt;" width="90"><?=$job_arr[$program_data_arr[$v["prod"]."#*#".$v["requ_no"]]["booking"]]["ship_date"]?></td>
                                <td align="center" valign="middle" style="padding: 1px 2px; font-size: 10pt;" width="110"><?=$program_data_arr[$v["prod"]."#*#".$v["requ_no"]]["booking"]?></td>
                                <td align="right" valign="middle" style="padding: 1px 2px; font-size: 10pt;" width="100"><?=$program_data_arr[$v["prod"]."#*#".$v["requ_no"]]["allocated_qty"]?></td>
                                <td align="center" valign="middle" style="padding: 1px 2px; font-size: 10pt;" width="100"><?=$program_data_arr[$v["prod"]."#*#".$v["requ_no"]]["program"]?></td>
                                <td align="right" valign="middle" style="padding: 1px 2px; font-size: 10pt;" width="100"><?=number_format($program_data_arr[$v["prod"]."#*#".$v["requ_no"]]["program_qty"], 2)?></td>
                                <td align="right" valign="middle" style="padding: 1px 2px; font-size: 10pt;" width="100"><?=number_format($program_data_arr[$v["prod"]."#*#".$v["requ_no"]]["requ_qty"], 2)?></td>
                                <td align="right" valign="middle" style="padding: 1px 2px; font-size: 10pt;" width="100"><?=number_format(($program_data_arr[$v["prod"]."#*#".$v["requ_no"]]["program_qty"]-$program_data_arr[$v["prod"]."#*#".$v["requ_no"]]["requ_qty"]), 2)?></td>
                                <td align="right" valign="middle" style="padding: 1px 2px; font-size: 10pt;" width="100"><?=number_format($v['issue_qty'], 2)?></td>
                                <td align="right" valign="middle" style="padding: 1px 2px; font-size: 10pt;" width="100"><?=number_format($issueReturnDataArr[$v["issue_id"]."*##*".$v["prod"]]["return_qty"], 2)?></td>
                                <?
                                $req_qty = isset($program_data_arr[$v["prod"]."#*#".$v["requ_no"]]["requ_qty"]) ? $program_data_arr[$v["prod"]."#*#".$v["requ_no"]]["requ_qty"] : 0;
                                $balance = ($req_qty - ($v['issue_qty']-$issueReturnDataArr[$v["issue_id"]."*##*".$v["prod"]]["return_qty"]));

                                ?>
                                <td align="right" valign="middle" style="padding: 1px 2px; font-size: 10pt;"><?=number_format(($balance > 0 ? $balance : 0), 2)?></td>
                            </tr>
                           <?
                           $i++;
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </fieldset>
    </div>
    <?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename";
    exit();
}
?>