<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id = $_SESSION['logic_erp']['user_id'];

if ($action == "report_generate")
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));
    $cbo_company = str_replace("'", "", $cbo_company_name);
    $indent_no = str_replace("'", "", $txt_indent_no);
    $date_from = str_replace("'", "", $txt_date_from);
    $date_to = str_replace("'", "", $txt_date_to);
    $year_selection = str_replace("'", "", $cbo_year_selection);

    $country_arr = return_library_array("SELECT ID, COUNTRY_NAME FROM LIB_COUNTRY", 'ID', 'COUNTRY_NAME');
    $company_arr = return_library_array("SELECT ID, COMPANY_NAME FROM LIB_COMPANY", 'ID', 'COMPANY_NAME');
    $store_arr = return_library_array("SELECT ID, STORE_NAME FROM LIB_STORE_LOCATION", 'ID', 'STORE_NAME');
    $division_arr = return_library_array("SELECT ID, DIVISION_NAME FROM LIB_DIVISION", 'ID', 'DIVISION_NAME');
    $uom_arr = return_library_array("SELECT UNIT_ID, UNIT_NAME FROM LIB_UNIT_OF_MEASUREMENT", 'UNIT_ID', 'UNIT_NAME');
    $product_arr = return_library_array("SELECT ID, ITEM_CODE FROM PRODUCT_DETAILS_MASTER", 'ID', 'ITEM_CODE');

    $sql_company = sql_select("SELECT * FROM LIB_COMPANY WHERE IS_DELETED=0 AND STATUS_ACTIVE=1");
    foreach ($sql_company as $row) {
        if ($row['PLOT_NO'] != '') $plot_no = $row['PLOT_NO'] . ', ';
        if ($row['LEVEL_NO'] != '') $level_no = $row['LEVEL_NO'] . ', ';
        if ($row['ROAD_NO'] != '') $road_no = $row['ROAD_NO'] . ', ';
        if ($row['BLOCK_NO'] != '') $block_no = $row['BLOCK_NO'] . ', ';
        if ($row['CITY'] != '') $city = $row['CITY'] . ', ';
        if ($row['ZIP_CODE'] != '') $zip_code = $row['ZIP_CODE'] . ', ';
        if ($row['COUNTRY_ID'] != 0) $country = $country_arr[$row['COUNTRY_ID']];
        if ($row['EMAIL'] != '') $company_email = "Email:&nbsp;" . $row['EMAIL'];
        if ($row['CONTACT_NO'] != '') $contact_no = "TEL#&nbsp;" . $row['CONTACT_NO'];
        if ($row['BIN_NO'] != '') $bin_no = $row['BIN_NO'];

        $company_address[$row['ID']] = $plot_no . $level_no . $road_no . $block_no . $city . $zip_code . $country;
    }
    // echo $company_address[$cbo_company]; die;

    $search_cond = '';
    $from_date = '';
    $to_date = '';
    if ($cbo_company) {
        $search_cond .= " and a.COMPANY_ID=$cbo_company ";
    }
    if ($indent_no) {
        $search_cond .= " and a.ITEMISSUE_REQ_PREFIX_NUM='$indent_no' ";
    }
    if($year_selection){
        $search_cond .= " and to_char(a.INDENT_DATE,'YYYY')='$year_selection' ";
    }
    if ($date_from != '' && $date_to != '') {
        if ($db_type == 0) {
            $from_date = change_date_format($date_from, 'yyyy-mm-dd');
            $to_date = change_date_format($date_to, 'yyyy-mm-dd');
        } else if ($db_type == 2) {
            $from_date = change_date_format($date_from, '', '', -1);
            $to_date = change_date_format($date_to, '', '', -1);
        }
        $search_cond .= " and a.INDENT_DATE between '$from_date' and '$to_date' ";
    }

    $con = connect();
    $r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (81)");
    oci_commit($con);

    $main_sql = "SELECT a.ID, a.ITEMISSUE_REQ_SYS_ID, a.STORE_ID, a.DIVISION_ID, a.INDENT_DATE, b.ITEM_DESCRIPTION, b.REQ_QTY, b.UNIT_OF_MEASURE, b.REMARKS, b.PRODUCT_ID
    from INV_ITEMISSUE_REQUISITION_DTLS b , INV_ITEM_ISSUE_REQUISITION_MST a 
    where a.ID=b.MST_ID  and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 $search_cond";
    //echo $main_sql;
    $main_result = sql_select($main_sql);
    $main_array = array();
    foreach ($main_result as $row) {
        $main_array[$row['ID']][$row['STORE_ID']][$row['INDENT_DATE']][$row['DIVISION_ID']][$row['PRODUCT_ID']]['ID'] = $row['ID'];
        $main_array[$row['ID']][$row['STORE_ID']][$row['INDENT_DATE']][$row['DIVISION_ID']][$row['PRODUCT_ID']]['ITEMISSUE_REQ_SYS_ID'] = $row['ITEMISSUE_REQ_SYS_ID'];
        $main_array[$row['ID']][$row['STORE_ID']][$row['INDENT_DATE']][$row['DIVISION_ID']][$row['PRODUCT_ID']]['STORE_ID'] = $row['STORE_ID'];
        $main_array[$row['ID']][$row['STORE_ID']][$row['INDENT_DATE']][$row['DIVISION_ID']][$row['PRODUCT_ID']]['DIVISION_ID'] = $row['DIVISION_ID'];
        $main_array[$row['ID']][$row['STORE_ID']][$row['INDENT_DATE']][$row['DIVISION_ID']][$row['PRODUCT_ID']]['INDENT_DATE'] = $row['INDENT_DATE'];
        $main_array[$row['ID']][$row['STORE_ID']][$row['INDENT_DATE']][$row['DIVISION_ID']][$row['PRODUCT_ID']]['ITEM_DESCRIPTION'] = $row['ITEM_DESCRIPTION'];
        $main_array[$row['ID']][$row['STORE_ID']][$row['INDENT_DATE']][$row['DIVISION_ID']][$row['PRODUCT_ID']]['REQ_QTY'] += $row['REQ_QTY'];
        $main_array[$row['ID']][$row['STORE_ID']][$row['INDENT_DATE']][$row['DIVISION_ID']][$row['PRODUCT_ID']]['UNIT_OF_MEASURE'] = $row['UNIT_OF_MEASURE'];
        $main_array[$row['ID']][$row['STORE_ID']][$row['INDENT_DATE']][$row['DIVISION_ID']][$row['PRODUCT_ID']]['REMARKS'] = $row['REMARKS'];
        $main_array[$row['ID']][$row['STORE_ID']][$row['INDENT_DATE']][$row['DIVISION_ID']][$row['PRODUCT_ID']]['ISSUE_DATE'] = $row['ISSUE_DATE'];
        $main_array[$row['ID']][$row['STORE_ID']][$row['INDENT_DATE']][$row['DIVISION_ID']][$row['PRODUCT_ID']]['CONS_QUANTITY'] = $row['CONS_QUANTITY'];

        $req_id_arr[$row['ID']] = $row['ID'];
    }
    // $req_ids = implode(',', $req_id_arr);
    // echo "<pre>"; print_r($req_id_arr); die;

    if (count($req_id_arr)>0)
    {
        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 81, 1, $req_id_arr, $empty_arr);

        $sql = "SELECT a.ISSUE_DATE,A.STORE_ID,A.DIVISION_ID,A.REQ_ID, b.CONS_QUANTITY, b.PROD_ID
        from INV_ISSUE_MASTER a, INV_TRANSACTION b,GBL_TEMP_ENGINE C
        where a.ID=b.MST_ID and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.REQ_ID = C.REF_VAL and a.ENTRY_FORM = 21 and b.TRANSACTION_TYPE=2 and a.COMPANY_ID=$cbo_company  AND C.USER_ID= $user_id AND C.ENTRY_FORM=81 AND C.REF_FROM=1";
        //  echo $sql;// die;
        foreach (sql_select($sql) as $row) {
            $quantity_array[$row['REQ_ID']][$row['STORE_ID']][$row['ISSUE_DATE']][$row['DIVISION_ID']][$row['PROD_ID']]['ISSUE_DATE'] = $row['ISSUE_DATE'];
            $quantity_array[$row['REQ_ID']][$row['STORE_ID']][$row['ISSUE_DATE']][$row['DIVISION_ID']][$row['PROD_ID']]['CONS_QUANTITY'] += $row['CONS_QUANTITY'];
        }
        // echo "<pre>"; print_r($quantity_array); die;
    }


    // $sql = "SELECT a.ISSUE_DATE, b.CONS_QUANTITY, b.PROD_ID
    // from INV_ISSUE_MASTER a, INV_TRANSACTION b
    // where a.ID=b.MST_ID and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.REQ_ID IN($req_ids) and a.ENTRY_FORM = 21 and b.TRANSACTION_TYPE=2 and a.COMPANY_ID=$cbo_company";
    // // echo $sql; die;
    // foreach (sql_select($sql) as $row) {
    //     $quantity_array[$row['PROD_ID']]['ISSUE_DATE'] = $row['ISSUE_DATE'];
    //     $quantity_array[$row['PROD_ID']]['CONS_QUANTITY'] += $row['CONS_QUANTITY'];
    // }
    // // echo "<pre>"; print_r($quantity_array); die;

    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (81)");
    oci_commit($con);
    disconnect($con);

    $table_width = 1260;
    ob_start();
    ?>
    <style>
        .wrd_brk {
            word-break: break-all;
        }

        .left {
            text-align: left;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }
    </style>

    <body>
        <div style="width:100%">
            <table border="1" class="rpt_table" rules="all" width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header" style="margin-right: 17px;">
                <thead>
                    <tr>
                        <th colspan="13">
                            <h3 style="font-size: 16px; padding:3px;"><?= $company_arr[$cbo_company]; ?></h3>
                            <p style="padding:3px;"><?= $company_address[$cbo_company]; ?></p>
                            <p style="padding:3px;">Floor Requisition/Item Issue Requisition Status: From: <? echo date('d-m-Y', strtotime($date_from)); ?> To: <? echo date('d-m-Y', strtotime($date_to)); ?></p>
                        </th>
                    </tr>
                    <tr>
                        <th width="40"><p>SL No</p></th>
                        <th width="120"><p>Requisition/ Indent No</p></th>
                        <th width="140"><p>Store Name</p></th>
                        <th width="90"><p>Indent Date</p></th>
                        <th width="100"><p>Required Division</p></th>
                        <th width="80"><p>Item Code</p></th>
                        <th width="140"><p>Item Name</p></th>
                        <th width="100"><p>Requisition Qty</p></th>
                        <th width="70"><p>UOM</p></th>
                        <th width="90"><p>Issue Date</p></th>
                        <th width="80"><p>Issued Qty</p></th>
                        <th width="110"><p>Issued Balanc Qty</p></th>
                        <th><p>Remarks</p></th>
                    </tr>
                </thead>
            </table>
            <div style="width:<?= $table_width + 20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
                <table width="<?= $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                    <tbody>
                        <?
                        $i = 1;
                        foreach ($main_array as $indent_num => $indent_val) 
                        {
                            foreach ($indent_val as $store_id => $store_val) 
                            {
                                foreach ($store_val as $indent_date => $indent_date_val) 
                                {
                                    foreach ($indent_date_val as $division_id => $division_val) 
                                    {
                                        foreach ($division_val as $product_id => $item_val) 
                                        {
                                            if ($i % 2 == 0) {
                                                $bgcolor = "#E9F3FF";
                                            } else {
                                                $bgcolor = "#FFFFFF";
                                            }

                                            if ($quantity_array[$product_id]['CONS_QUANTITY']) $issued_qty = $quantity_array[$product_id]['CONS_QUANTITY'];
                                            else $issued_qty = 0;
                                            ?>
                                            <tr bgcolor="<?= $bgcolor; ?>">
                                                <td width="40" class="center"><p><?= $i; ?></p></td>
                                                <td width="120" class="center"><p><?= $item_val['ITEMISSUE_REQ_SYS_ID']; ?></p></td>
                                                <td width="140" class="center"><p><?= $store_arr[$item_val['STORE_ID']]; ?></p></td>
                                                <td width="90" class="center"><p><?= date('d-m-Y', strtotime($item_val['INDENT_DATE'])); ?></p></td>
                                                <td width="100" class="center"><p><?= $division_arr[$item_val['DIVISION_ID']]; ?></p></td>
                                                <td width="80" class="center"><p><?= $product_arr[$product_id]; ?></p></td>
                                                <td width="140"><p><?= $item_val['ITEM_DESCRIPTION']; ?></p></td>
                                                <td width="100" class="right"><p><?= $item_val['REQ_QTY']; ?></p></td>
                                                <td width="70" class="center"><p><?= $uom_arr[$item_val['UNIT_OF_MEASURE']]; ?></p></td>
                                                <td width="90" class="center"><p><?
                                                    if ($quantity_array[$product_id]['ISSUE_DATE']) echo date('d-m-Y', strtotime($quantity_array[$product_id]['ISSUE_DATE'])); ?></p>
                                                </td>
                                                <td width="80" class="right"><p><? echo $issued_qty; ?></p></td>
                                                <td width="110" class="right"><p><?= $item_val['REQ_QTY'] - $issued_qty; ?></p></td>
                                                <td class="center"><p><?= $item_val['REMARKS']; ?></p></td>
                                            </tr>
                                            <?
                                            $i++;
                                        }
                                    }
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
    <?
    foreach (glob("$user_id*.xls") as $filename) {
        if (@filemtime($filename) < (time() - $seconds_old)) @unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, ob_get_contents());
    $filename = $user_id . "_" . $name . ".xls";
    echo "$total_data****$filename";
    exit();
}

?>