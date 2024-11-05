<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action == "issue_popup") {
    echo load_html_head_contents("Roll Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $issu_num = ltrim(str_replace("'", "", $issu_num), ',');
    $issue_arr = explode(',', $issu_num);
?>
    <table width="295" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <thead>
            <tr>
                <th width="40">Sl No.</th>
                <th width="100">Issue ID</th>
                <th width="60">Issue qty</th>
                <th>Issue date</th>
            </tr>
        </thead>
        <tbody>
            <?
            foreach ($issue_arr as $key => $row) {
                $data = explode('=>', $row);
            ?>
                <tr>
                    <td width="40"><? echo $key + 1; ?></td>
                    <td width="100"><? echo $data[0]; ?></td>
                    <td width="60" align="center" ><? echo $data[2]; ?></td>
                    <td align="center" ><? echo $data[1]; ?></td>

                </tr>
            <?
            }
            ?>
        </tbody>
    </table>
<?

}

if ($action == "load_drop_down_buyer") {
    echo create_drop_down("cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
    exit();
}

if ($action == "jobnumbershow") {
    echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $data = explode('_', $data);
?>
    <script type="text/javascript">
        function js_set_value(id) {
            document.getElementById('selected_id').value = id;
            parent.emailwindow.hide();
        }
    </script>
    <input type="hidden" id="selected_id" name="selected_id" />
    <?
    if ($db_type == 0) $year_field = "SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
    else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year";
    if ($db_type == 0) $year_field_by = "and YEAR(a.insert_date)";
    else if ($db_type == 2) $year_field_by = " and to_char(a.insert_date,'YYYY')";
    if ($db_type == 0) $year_field_grpby = "GROUP BY a.job_no order by b.id desc";
    else if ($db_type == 2) $year_field_grpby = " GROUP BY a.job_no,a.id,a.buyer_name,a.style_ref_no,a.gmts_item_id,b.po_number,a.job_no_prefix_num,a.insert_date,b.id order by b.id desc";
    $year_job = str_replace("'", "", $year);
    if (trim($year) != 0) $year_cond = " $year_field_by=$year_job";
    else $year_cond = "";
    //$cbo_buyer_name=($cbo_buyer_name==0)?"%%" : "%$cbo_buyer_name%";

    if (trim($cbo_buyer_name) == 0) {
        $buyer_name_cond = "";
    } else {
        $buyer_name_cond = " and a.buyer_name=$cbo_buyer_name";
    }


    $sql = "select a.id,a.buyer_name,a.style_ref_no,a.gmts_item_id,b.po_number,a.job_no_prefix_num as job_prefix,$year_field , a.job_no from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and a.company_name=$company_id $buyer_name_cond $year_cond and a.is_deleted=0 $year_field_grpby";

    $buyer_arr = return_library_array("select id,buyer_name from lib_buyer", "id", "buyer_name");

    // echo $sql;

    ?>
    <table width="500" border="1" rules="all" class="rpt_table">
        <thead>

            <tr>
                <th width="30">SL</th>
                <th width="100">Job No</th>
                <th width="50">Job Prefix</th>
                <th width="40">Year</th>
                <th width="100">Buyer</th>
                <th width="100">Style</th>
                <th>Item Name</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:4500px; overflow:auto;">
        <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
            <? $rows = sql_select($sql);
            $i = 1;
            foreach ($rows as $data) {
                if ($i % 2 == 0) $bgcolor = "#E9F3FF";
                else $bgcolor = "#FFFFFF";
            ?>
                <tr bgcolor="<? echo  $bgcolor; ?>" onClick="js_set_value('<? echo $data[csf('job_no')]; ?>')" style="cursor:pointer;">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100">
                        <p><? echo $data[csf('job_no')]; ?></p>
                    </td>
                    <td align="center" width="50">
                        <p><? echo $data[csf('job_prefix')]; ?></p>
                    </td>
                    <td align="center" width="40">
                        <p><? echo $data[csf('year')]; ?></p>
                    </td>
                    <td width="100">
                        <p><? echo $buyer_arr[$data[csf('buyer_name')]]; ?></p>
                    </td>
                    <td width="100">
                        <p><? echo $data[csf('style_ref_no')]; ?></p>
                    </td>
                    <td>
                        <p><?
                            $itemid = explode(",", $data[csf('gmts_item_id')]);
                            foreach ($itemid as $index => $id) {
                                echo ($itemid[$index] == end($itemid)) ? $garments_item[$id] : $garments_item[$id] . ', ';
                            }
                            ?></p>
                    </td>
                </tr>
            <? $i++;
            } ?>
        </table>
    </div>
    <script>
        setFilterGrid("table_body2", -1);
    </script>
<?
    disconnect($con);
    exit();
}

if ($action == "report_generate") {
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    $cbo_company = str_replace("'", "", $cbo_company_id);
    $cbo_working_company_id = str_replace("'", "", $cbo_working_company_id);
    $cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
    $txt_requisition_no = trim(str_replace("'", "", $txt_requisition_no));
    $cbo_year = str_replace("'", "", $cbo_year);
    $job_number_show = str_replace("'", "", $job_number_show);
    $date_from = str_replace("'", "", $txt_date_from);
    $date_to = str_replace("'", "", $txt_date_to);
    $cbo_year_selection = str_replace("'", "", $cbo_year_selection);
    $cbo_year_selection = substr($cbo_year_selection, -2);

    $company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $buyer_library = return_library_array("select id,buyer_name from lib_buyer", "id", "buyer_name");
    $color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

    if ($cbo_company == 0) $cbo_company_cond = "";
    else $cbo_company_cond = " and a.company_id=$cbo_company";

    if ($cbo_buyer_name == 0) $buyer_cond = "";
    else $buyer_cond = " and b.buyer_id=$cbo_buyer_name ";

    if ($txt_requisition_no == "") $requ_no_cond = "";
    else $requ_no_cond = " and a.reqn_number_prefix_num=$txt_requisition_no and a.reqn_number like '%-$cbo_year_selection-%'";

    if ($job_number_show == "") $job_number = "";
    else $job_number = " and b.job_no='$job_number_show' ";

    if (str_replace("'", "", $txt_date_from) != "" && str_replace("'", "", $txt_date_to) != "") {
        if ($db_type == 0) {
            $start_date = change_date_format(str_replace("'", "", $txt_date_from), "yyyy-mm-dd", "");
            $end_date = change_date_format(str_replace("'", "", $txt_date_to), "yyyy-mm-dd", "");
        } else if ($db_type == 2) {
            $start_date = change_date_format(str_replace("'", "", $txt_date_from), "", "", 1);
            $end_date = change_date_format(str_replace("'", "", $txt_date_to), "", "", 1);
        }
        $date_cond = " and a.reqn_date between '$start_date' and '$end_date'";
    }

    if ($cbo_year != 0) $year = " and to_char(a.insert_date,'yyyy')=$cbo_year";
    else $year = "";



    $batchcolorsql = "SELECT  b.batch_color , b.po_id , b.prod_id FROM  pro_fab_reqn_for_batch_mst a JOIN  pro_fab_reqn_for_batch_dtls b ON b.mst_id = a.id WHERE b.entry_form = 553 $cbo_company_cond $buyer_cond $date_cond  $requ_no_cond $job_number $year AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 GROUP BY b.batch_color , b.po_id , b.prod_id ORDER BY  b.batch_color ";
    $data = sql_select($batchcolorsql);

    $batchcolor = array();
    $po_id = "";
    $prod_id = "";
    foreach ($data as $key => $row) {
        if ($key != 0) {
            if ($row[csf("batch_color")] > 0) {

                $batchcolor[$row[csf("prod_id")]] .= "," . $color_library[$row[csf("batch_color")]];
            }
            $po_id .= "," . $row[csf("po_id")];
            $prod_id .= "," . $row[csf("prod_id")];
        } else {
            if ($row[csf("batch_color")] > 0) {
                $batchcolor[$row[csf("prod_id")]] .= $color_library[$row[csf("batch_color")]];
            }
            $po_id .= $row[csf("po_id")];
            $prod_id .= $row[csf("prod_id")];
        }
    }

    $issue_qty_sql = "select c.quantity,c.prod_id,c.po_breakdown_id , c.trans_id , a.issue_number, a.issue_date from inv_issue_master a, inv_transaction b, order_wise_pro_details c where b.id=c.trans_id and b.mst_id=a.id and  c.prod_id in ($prod_id) and c.po_breakdown_id in ($po_id) and c.trans_type = 2 and c.entry_form = 61 and c.status_active = 1 and c.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 ";

    $issue_data = sql_select($issue_qty_sql);
    $issuetotal = array();
    $issu_num = array();
    foreach ($issue_data as $row) {
        $issuetotal[$row[csf("prod_id")]] += (float)$row[csf("quantity")];
        $issu_num[$row[csf("prod_id")]] .= ',' . $row[csf("issue_number")] . "=>" . $row[csf("issue_date")] . "=>" . $row[csf("quantity")];
    }

    $sql = "SELECT 
        a.id, a.company_id, a.reqn_date, a.reqn_number_prefix_num, b.job_no, b.buyer_id, SUM(b.reqn_qty) AS total_reqn_qty , b.prod_id 
    FROM 
        pro_fab_reqn_for_batch_mst a JOIN  pro_fab_reqn_for_batch_dtls b ON b.mst_id = a.id
    WHERE b.entry_form = 553 $cbo_company_cond $buyer_cond $date_cond  $requ_no_cond $job_number $year
        AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 
    GROUP BY 
    a.id, a.company_id, a.reqn_date, a.reqn_number_prefix_num, b.job_no, b.buyer_id, prod_id";

    // echo $sql;

    $data_array = sql_select($sql);

    ob_start();
    $total_req_qnty = 0;
    $total_issue_qnty = 0;

?>
    <div id="scroll_body" align="center" style="height:auto; width:auto; margin:0 auto; padding:0;">
        <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
                <td align="center" width="100%" colspan="16" class="form_caption">
                    <strong style="font-size:18px"><? echo ' Company Name:' . $company_library[$cbo_company]; ?></strong>
                </td>
            </tr>
            <tr>
                <td align="center" width="100%" colspan="16" class="form_caption"><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
            </tr>

        </table>
        <div align="center" style="height:auto;">
            <table width="800" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <th width="40">Sl No.</th>
                        <th width="100">Year</th>
                        <th width="100">Requisition Date</th>
                        <th width="100">Requisition No</th>
                        <th width="100">Buyer</th>
                        <th width="100">Job no</th>
                        <th width="100">Batch Color</th>
                        <th width="100">Requisition Qty</th>
                        <th width="60">Issue Qty</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div align="left" style="width:800px; max-height:auto; overflow-y:scroll;" id="scroll_body">
            <table align="left" cellspacing="0" width="800" border="1" rules="all" class="rpt_table" id="table_body">
                <tbody>
                    <?
                    if (count($data_array) < 1) {
                        echo "<span style='font-weight:bold;width:800px;margin-left:600px; align:center'>Data Not Found</span>";
                    }
                    $i = 1;
                    foreach ($data_array as $row) {
                        if ($i % 2 == 0) $bgcolor = "#E9F3FF";
                        else $bgcolor = "#FFFFFF";
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40" align="center"><? echo $i; ?></td>
                            <td width="100" align="center"><? echo date("Y", strtotime($row[csf("reqn_date")]));  ?></td>
                            <td width="100" align="center"><? echo change_date_format($row[csf("reqn_date")]); ?></td>
                            <td width="100" align="center"><? echo $row[csf("reqn_number_prefix_num")]; ?></td>
                            <td width="100" align="center"><? echo $buyer_library[$row[csf("buyer_id")]]; ?></td>
                            <td width="100" align="center"><? echo $row[csf("job_no")]; ?></td>
                            <td width="100" align="center">
                                <?
                                $color_string = ltrim($batchcolor[$row[csf("prod_id")]], ',');
                                $color_arr = array_unique(explode(',', $color_string));
                                $coler = "";
                                foreach ($color_arr as $row2) { $coler .= ", " . $row2;}
                                echo ltrim($coler, ',');
                                ?>
                            </td>

                            <td width="100" align="center"><? echo $row[csf("total_reqn_qty")]; ?></td>
                            <td width="60" align="center"><a href="##" onclick="openmypage_popup( '<? echo ltrim($issu_num[$row[csf("prod_id")]]); ?>' , 'issue_popup')"><? echo $issuetotal[$row[csf("prod_id")]]; ?> </td>

                        </tr>
                    <?
                        $total_req_qnty += $row[csf("total_reqn_qty")];
                        $total_issue_qnty += $issuetotal[$row[csf("prod_id")]];
                        $i++;
                    }
                    ?>
                </tbody>
            </table>

            <table align="left" cellspacing="0" width="800" border="1" rules="all" class="rpt_table">
                <tfoot>
                    <tr bgcolor="E5E5E5">
                        <td colspan="7" align="right">G/Total=</td>
                        <td id="value_req_qnty" align="center" width="100"><? echo number_format($total_req_qnty, 2); ?></td>
                        <td id="value_issue_qnty" align="center" width="60"><? echo number_format($total_issue_qnty, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?

    foreach (glob("$user_id*.xls") as $filename) {
        if (@filemtime($filename) < (time() - $seconds_old))
            @unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, ob_get_contents());
    $filename = $user_id . "_" . $name . ".xls";
    echo "$total_data####$filename";
    exit();
}
?>