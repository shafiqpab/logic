<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];
if ($_SESSION['logic_erp']['user_id'] == "") {
    header("location:login.php");
    die;
}
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];


if ($action == "load_drop_down_company_store") {
    extract($_REQUEST);

    $sql = "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$choosenCompany and b.category_type in(1)  order by a.store_name";

    echo create_drop_down("cbo_store_name", 110, $sql, "id,store_name", 0, "-- Select Store --", $selected, "", "");

    exit();
}


if ($action == "load_drop_down_buyer") {
    //$data=explode("_",$data);
    //if($data==1) $party="1,3,21,90"; else $party="80";
    echo create_drop_down("cbo_buyer_id", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' order by buy.buyer_name", "id,buyer_name", 1, "--Select Buyer--", $selected, "", "");
    //and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)
    exit();
}

if ($action == "booking_no_popup") {
    echo load_html_head_contents("Order No Info", "../../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $dataEx = explode("_", $data);
    $companyID = $dataEx[0];
    $buyer_name = $dataEx[1];
    ?>

    <script>
        $(function() {
            load_drop_down('party_wise_yarn_issue_report_controller', <? echo $companyID; ?>, 'load_drop_down_buyer', 'buyer_td');
        });

        var selected_id = new Array;
        var selected_name = new Array;

        function check_all_data() {
            var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
            tbl_row_count = tbl_row_count - 1;

            for (var i = 1; i <= tbl_row_count; i++) {
                $('#tr_' + i).trigger('click');
            }
        }

        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
            }
        }

        function js_set_value(str) {
            if (str != "") str = str.split("_");

            toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

            if (jQuery.inArray(str[1], selected_id) == -1) {
                selected_id.push(str[1]);
                selected_name.push(str[2]);

            } else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == str[1]) break;
                }
                selected_id.splice(i, 1);
                selected_name.splice(i, 1);
            }
            var id = '';
            var name = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
            }

            id = id.substr(0, id.length - 1);
            name = name.substr(0, name.length - 1);

            $('#txt_booking_no').val(name);
            $('#txt_booking_id').val(id);
            //$('#txt_order_id').val( name );
        }
    </script>

    </head>

    <body>
        <div align="center">
            <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
                <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                    <thead>
                        <th width="150">Company Name</th>
                        <th width="140">Buyer Name</th>
                        <th width="80">Booking No</th>
                        <th>Booking Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                    </thead>
                    <tr>
                        <td>
                            <input type="hidden" id="txt_booking_no">
                            <input type="hidden" id="txt_booking_id">
                            <input type="hidden" id="txt_order_id">
                            <input type="hidden" id="job_no">
                            <input type="hidden" id="cbo_year" value="<? echo $cbo_year; ?>">
                            <?
                            echo create_drop_down("cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $companyID, "load_drop_down( 'party_wise_yarn_issue_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                            ?>
                        </td>
                        <td id="buyer_td"><? echo create_drop_down("cbo_buyer_id", 140, $blank_array, "", 1, "-- All Buyer --"); ?></td>
                        <td>
                            <input type="text" id="booking_no_prefix_num" name="booking_no_prefix_num" class="text_boxes_numeric" style="width:75px" />
                        </td>
                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px">
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('job_no').value+'_'+document.getElementById('booking_no_prefix_num').value+'_'+document.getElementById('cbo_year').value, 'create_booking_search_list_view', 'search_div', 'party_wise_yarn_issue_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center">
                            <? echo load_month_buttons(1);  ?>
                        </td>
                    </tr>
                </table>
                <div style="margin-top:5px" id="search_div"></div>
            </form>
        </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>

    </html>
    <?
    exit();
}

if ($action == "create_booking_search_list_view") {
    $data = explode('_', $data);
    if ($data[0] != 0) $company = " and  a.company_id='$data[0]'";
    else {
        echo "Please Select Company First.";
        die;
    }
    if ($data[1] != 0) $buyer = " and a.buyer_id='$data[1]'";
    else $buyer = "";
    if ($data[4] != 0) $job_no = " and a.job_no='$data[4]'";
    else $job_no = '';
    if ($data[5] != 0) $booking_no = " and a.booking_no_prefix_num='$data[5]'";
    else $booking_no = '';
    if ($data[6] != 0) $cbo_year_con = " and to_char(b.insert_date,'YYYY')=$data[6]";
    else $cbo_year_con = '';




    //$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
    if ($db_type == 0) {
        if ($data[2] != "" &&  $data[3] != "") $booking_date  = "and a.booking_date  between '" . change_date_format($data[2], "yyyy-mm-dd", "-") . "' and '" . change_date_format($data[3], "yyyy-mm-dd", "-") . "'";
        else $booking_date = "";
    }
    if ($db_type == 2) {
        if ($data[2] != "" &&  $data[3] != "") $booking_date  = "and a.booking_date  between '" . change_date_format($data[2], "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format($data[3], "yyyy-mm-dd", "-", 1) . "'";
        else $booking_date = "";
    }
    $po_array = array();
    $sql_po = sql_select("select b.booking_no,c.po_number from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id $company $buyer $booking_no $booking_date and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
    foreach ($sql_po as $row) {
        $po_no_array[$row[csf("booking_no")]][$row[csf("po_number")]] = $row[csf("po_number")];
    }

    foreach ($po_no_array as $booking_number => $po_no_arr) {
        $po_array[$booking_number] = implode(',', $po_no_arr);
    }

    //print_r($po_array);die;


    $approved = array(0 => "No", 1 => "Yes");
    $is_ready = array(0 => "No", 1 => "Yes", 2 => "No");
    $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
    $comp = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
    $suplier = return_library_array("select id, short_name from lib_supplier", 'id', 'short_name');
    $arr = array(2 => $comp, 3 => $buyer_arr, 5 => $po_array, 6 => $item_category, 7 => $fabric_source, 8 => $suplier, 9 => $approved, 10 => $is_ready);

    $sql = "SELECT a.id,a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a, wo_po_details_master b where a.job_no=b.job_no $company $buyer $booking_no $booking_date $cbo_year_con and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 group by a.id,a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category,
    a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved
    UNION ALL
    SELECT a.id, a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,null as job_no, CAST(a.po_break_down_id AS nvarchar2(2000)) AS po_break_down_id, a.item_category, a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved from wo_non_ord_samp_booking_mst  a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no  and b.status_active=1 and b.is_deleted=0  where  a.entry_form_id=140 and  b.entry_form_id=140 $company $buyer $booking_no $booking_date $cbo_year_con and a.booking_type=4 and  a.status_active=1 and a.is_deleted=0 group by a.id, a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id, a.po_break_down_id, a.item_category, a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved ";


    echo  create_list_view("tbl_list_search", "Booking No,Booking Date,Company,Buyer,Job No.,PO number,Fabric Nature,Fabric Source,Approved,Is-Ready", "100,80,70,100,80,220,110,60,60", "1020", "230", 0, $sql, "js_set_value", "id,booking_no", "", 1, "0,0,company_id,buyer_id,0,booking_no,item_category,fabric_source,is_approved,ready_to_approved", $arr, "booking_no,booking_date,company_id,buyer_id,job_no,booking_no,item_category,fabric_source,is_approved,ready_to_approved", '', '', '0,3,0,0,0,0,0,0,0,0', '', 1);
    exit();
}

if ($action == "party_popup") {
    echo load_html_head_contents("Party Info", "../../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    <script>
        var selected_id = new Array;
        var selected_name = new Array;

        function check_all_data() {
            var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
            tbl_row_count = tbl_row_count - 1;

            for (var i = 1; i <= tbl_row_count; i++) {
                $('#tr_' + i).trigger('click');
            }
        }

        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
            }
        }

        function js_set_value(str) {

            if (str != "") str = str.split("_");

            toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

            if (jQuery.inArray(str[1], selected_id) == -1) {
                selected_id.push(str[1]);
                selected_name.push(str[2]);
            } else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == str[1]) break;
                }
                selected_id.splice(i, 1);
                selected_name.splice(i, 1);
            }
            var id = '';
            var name = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
            }

            id = id.substr(0, id.length - 1);
            name = name.substr(0, name.length - 1);

            $('#hide_party_id').val(id);
            $('#hide_party_name').val(name);
        }
    </script>
    <input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
    <input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
    <?

    if ($cbo_knitting_source == 3) {
        $sql = "select a.id, a.supplier_name as party_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$companyID and b.party_type in(1,9,20) and a.status_active=1  group by a.id, a.supplier_name order by a.supplier_name";
    } elseif ($cbo_knitting_source == 1) {
        $sql = "select id, company_name as party_name from lib_company where status_active=1 and is_deleted=0 order by company_name";
    }

    echo create_list_view("tbl_list_search", "Party Name", "380", "380", "270", 0, $sql, "js_set_value", "id,party_name", "", 1, "0", $arr, "party_name", "", 'setFilterGrid("tbl_list_search",-1);', '0', '', 1);

    exit();
}

if ($action == "report_generate_party_wise") {
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    $cbo_store_name = str_replace("'", "", $cbo_store_name);
    $cbo_buyer_id = str_replace("'", "", $cbo_buyer_id);
    $txt_booking_no = str_replace("'", "", $txt_booking_no);
    $cbo_knitting_source = str_replace("'", "", $cbo_knitting_source);
    $txt_knitting_com_id = str_replace("'", "", $txt_knitting_com_id);
    $cbo_yarn_type = str_replace("'", "", $cbo_yarn_type);
    $cbo_yarn_count = str_replace("'", "", $cbo_yarn_count);
    $txt_lot_no = str_replace("'", "", trim($txt_lot_no));
    $cbo_issue_purpose = str_replace("'", "", $cbo_issue_purpose);
    $cbo_using_item = str_replace("'", "", $cbo_using_item);
    $txt_date_to = str_replace("'", "", $txt_date_to);
    $txt_date_from = str_replace("'", "", $txt_date_from);
    //var_dump($txt_knitting_com_id);

    $booking_req_cond = "";
    $sql_cond = "";

    if ($cbo_store_name > 0) {
        $sql_cond .= " and a.store_id in(" . $cbo_store_name . ")";
    }
    if ($cbo_buyer_id > 0) {
        $sql_cond .= " and c.buyer_id in(" . $cbo_buyer_id . ")";
    }
    if ($txt_booking_no != "") {
        $booking_no = str_replace(",", "','", $txt_booking_no);
        $sql_cond .= " and c.booking_no in($booking_no)";
    }

    if ($cbo_knitting_source > 0) {
        $sql_cond .= " and c.knit_dye_source in(" . $cbo_knitting_source . ")";
    }
    if ($txt_knitting_com_id > 0) {
        $sql_cond .= " and c.knit_dye_company in(" . $txt_knitting_com_id . ")";
    }
    if ($cbo_yarn_type != "") {
        $sql_cond .= " and b.yarn_type in(" . $cbo_yarn_type . ")";
    }
    if ($cbo_yarn_count != "") {
        $sql_cond .= " and b.yarn_count_id in(" . $cbo_yarn_count . ")";
    }

    if ($txt_lot_no != "") {
        $lot_no_cond = " and b.lot='" . $txt_lot_no . "'";
        if ($lot_search_type == 1) {
            $lot_no_cond = " and b.lot like '%" . $txt_lot_no . "%'";
        }
        $sql_cond .= $lot_no_cond;
    }

    if ($cbo_issue_purpose != "") {
        $sql_cond .= " and c.issue_purpose in (" . $cbo_issue_purpose . ")";
    }

    if ($cbo_using_item != "") {
        $sql_cond .= " and a.using_item in (" . $cbo_using_item . ")";
    }

    if ($db_type == 0) {
        if ($txt_date_from != "" && $txt_date_to != "") {
            $sql_cond .= " and c.issue_date between '" . change_date_format($txt_date_from, "yyyy-mm-dd") . "' and '" . change_date_format($txt_date_to, "yyyy-mm-dd") . "' ";
        }
        $select_issue_date = "DATE_FORMAT(c.issue_date,'%d-%m-%Y') as ISSUE_DATE";
    } else {
        if ($txt_date_from != "" && $txt_date_to != "") {
            $sql_cond .= " and c.issue_date between '" . date("j-M-Y", strtotime($txt_date_from)) . "' and '" . date("j-M-Y", strtotime($txt_date_to)) . "' ";
        }
        $select_issue_date = " to_char(c.issue_date,'DD-MM-YYYY') as ISSUE_DATE";
    }
    $company_arr = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $user_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name");
    $party_arr = return_library_array("select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b	where a.id=b.supplier_id and b.tag_company=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name", "id", "supplier_name");
    $supplier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 $supplier_ids_cond and is_deleted=0", "id", "supplier_name");

    $con = connect();
    $r_id1 = execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (1990)");
    if ($r_id1) {
        oci_commit($con);
    }

    $sqlIssue = "SELECT c.issue_number as ISSUE_NUMBER, a.id as TRANS_ID, $select_issue_date, a.cons_quantity as ISSUE_QTY,
       c.loan_party as LOAN_PARTY, e.yarn_count as YARN_COUNT, d.brand_name as BRAND_NAME, f.color_name as COLOR_NAME, b.id as PROD_ID,
       b.item_group_id as ITEM_GROUP_ID, b.sub_group_name as SUB_GROUP_NAME, b.item_description as ITEM_DESCRIPTION, c.knit_dye_source as KNIT_DYE_SOURCE,
       b.product_name_details as PRODUCT_NAME_DETAILS, b.item_code as ITEM_CODE, b.item_size as ITEM_SIZE, c.issue_basis as ISSUE_BASIS, c.knit_dye_company as KNIT_DYE_COMPANY,
       b.model as MODEL, b.item_number as ITEM_NUMBER, b.yarn_comp_percent1st as YARN_COMP_PERCENT1ST, a.buyer_id as BUYER_ID,
       b.yarn_comp_percent2nd as YARN_COMP_PERCENT2ND, c.received_id as RECEIVE_ID, b.yarn_comp_type1st as YARN_COMP_TYPE1ST, a.inserted_by as INSERTED_BY,
       b.yarn_comp_type2nd as YARN_COMP_TYPE2ND, b.lot as LOT, a.requisition_no as REQUISITION_NO, c.buyer_job_no as BUYER_JOB_NO, a.cons_rate as CONS_RATE
    from inv_transaction a,
         product_details_master b left join lib_brand d on d.id = b.brand left join lib_yarn_count e on e.id = b.yarn_count_id left join lib_color f on f.id = b.color,
         inv_issue_master c
	where a.prod_id = b.id and a.mst_id=c.id and c.entry_form = 3 and a.company_id=$cbo_company_name and a.transaction_type in (2) and a.status_active=1
	  and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond order by c.id desc";

    //echo $sqlIssue;


    $div_width = "1750px";
    $table_width = 1730;
    $sql_issue_result = sql_select($sqlIssue);
    $issueDataArr = array();
    $requID = array();
    $jobNo = array();
    $partyRowSpan = array();
    $temp = array();
    $reqNoChk = array();
    $all_req_no_arr = array();

    foreach ($sql_issue_result as $key => $issueData) {
        $parcent1st = "";
        if ($issueData["YARN_COMP_PERCENT1ST"] > 0) {
            $parcent1st = $issueData["YARN_COMP_PERCENT1ST"] . "%";
        }
        $parcent2nd = "";
        if ($issueData["YARN_COMP_PERCENT2ND"] > 0) {
            $parcent2nd = $issueData["YARN_COMP_PERCENT2ND"] . "%";
        }
        $compositionStr = $composition[$issueData["YARN_COMP_TYPE1ST"]] . ' ' . $parcent1st . ' ' . $composition[$issueData["YARN_COMP_TYPE2ND"]] . ' ' . $parcent2nd;
        $item_key = $issueData["REQUISITION_NO"] . "*##*" . $compositionStr . "*##*" . $issueData["COLOR"] . "*##*" . $issueData["YARN_COUNT"] . "*##*" . $issueData["BRAND_NAME"] . "*##*" . $issueData["LOT"];
        if ($issueData["REQUISITION_NO"] > 0) {
            //array_push($requID, $issueData["REQUISITION_NO"]);
            if ($reqNoChk[$issueData["REQUISITION_NO"]] == "") {
                $reqNoChk[$issueData["REQUISITION_NO"]] = $issueData["REQUISITION_NO"];
                $all_req_no_arr[$issueData["REQUISITION_NO"]] = $issueData["REQUISITION_NO"];
            }
        }
        if ($issueData["BUYER_JOB_NO"] != "") {
            array_push($jobNo, $issueData["BUYER_JOB_NO"]);
        }
        if ($issueData['KNIT_DYE_SOURCE'] == 1) {
            $partyKey = $company_arr[$issueData['KNIT_DYE_COMPANY']];
        } else {
            $partyKey = $supplier_arr[$issueData['KNIT_DYE_COMPANY']];
        }

        $issueDataArr[$partyKey][$issueData['ISSUE_NUMBER']]['item_data'][$item_key]['date'] = $issueData["ISSUE_DATE"];
        $issueDataArr[$partyKey][$issueData['ISSUE_NUMBER']]['item_data'][$item_key]['count'] = $issueData["YARN_COUNT"];
        $issueDataArr[$partyKey][$issueData['ISSUE_NUMBER']]['item_data'][$item_key]['brand'] = $issueData["BRAND_NAME"];
        $issueDataArr[$partyKey][$issueData['ISSUE_NUMBER']]['item_data'][$item_key]['composition'] = $compositionStr;
        $issueDataArr[$partyKey][$issueData['ISSUE_NUMBER']]['item_data'][$item_key]['lot'] = $issueData["LOT"];
        $issueDataArr[$partyKey][$issueData['ISSUE_NUMBER']]['item_data'][$item_key]['issue_qty'] += $issueData["ISSUE_QTY"];
        $issueDataArr[$partyKey][$issueData['ISSUE_NUMBER']]['item_data'][$item_key]['cons_rate'] = $issueData["CONS_RATE"];
        $issueDataArr[$partyKey][$issueData['ISSUE_NUMBER']]['item_data'][$item_key]['requ_no'] = $issueData["REQUISITION_NO"];
        $issueDataArr[$partyKey][$issueData['ISSUE_NUMBER']]['item_data'][$item_key]['job_no'] = $issueData["BUYER_JOB_NO"];
        $issueDataArr[$partyKey][$issueData['ISSUE_NUMBER']]['item_data'][$item_key]['color'] = $issueData["COLOR_NAME"];
        $issueDataArr[$partyKey][$issueData['ISSUE_NUMBER']]['item_data'][$item_key]['inserted_by'] = $user_arr[$issueData["INSERTED_BY"]];
    }

    //echo "<pre>";print_r($issueDataArr);echo "</pre>";

    $partySpanArr = [];
    foreach ($issueDataArr as $party => $issueNumber) {
        $partySpan = 0;
        foreach ($issueNumber as $issue => $itemData) {
            foreach ($itemData['item_data'] as $value) {
                $partySpan += 1;
            }
            $partySpan += 1;
        }
        $partySpanArr[$party] = $partySpan;
    }

    $jobNoUnique = array_chunk(array_unique($jobNo), 999, true);
    $counter = false;
    $job_no_cond = "";
    foreach ($jobNoUnique as $key => $value) {
        if ($counter) {
            $job_no_cond .= " or a.job_no in ('" . implode("','", $value) . "')";
        } else {
            $job_no_cond .= " and a.job_no in ('" . implode("','", $value) . "')";
        }
        $counter = true;
    }
    /* $requIDUnique = array_chunk(array_unique($requID),999, true);
    $counter = false;
    $requ_id_cond = "";
    $requ_id_cond1 = "";
    foreach ($requIDUnique as $key => $value){
        if($counter){
            $requ_id_cond .= " or b.requisition_no in (".implode(',', $value).")";
            $requ_id_cond1 .= " or d.requisition_no in (".implode(',', $value).")";
        }else{
            $requ_id_cond .= " and b.requisition_no in (".implode(',', $value).")";
            $requ_id_cond1 .= " and d.requisition_no in (".implode(',', $value).")";
        }
        $counter = true;
    } */
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
    $orderInfoArr = [];
    if (count($jobNoUnique) > 0) {
        $orderInfo = sql_select("select a.style_ref_no, b.po_number, a.job_no, a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst $job_no_cond");
        foreach ($orderInfo as $value) {
            $orderInfoArr['job_wise'][$value[csf('job_no')]]['po_number'] = $value[csf('po_number')];
            $orderInfoArr['job_wise'][$value[csf('job_no')]]['style'] = $value[csf('style_ref_no')];
            $orderInfoArr['job_wise'][$value[csf('job_no')]]['buyer'] = $buyer_arr[$value[csf('buyer_name')]];
        }
    }
    $program_arr = [];
    $orderInfoArrReq = [];
    $all_req_no_arr = array_filter($all_req_no_arr);
    //var_dump($all_req_no_arr);die;
    if (!empty($all_req_no_arr)) {
        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1990, 1, $all_req_no_arr, $empty_arr);
        //die;

        $program_arr = return_library_array("SELECT a.id, b.requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, GBL_TEMP_ENGINE c where a.id = b.knit_id and b.requisition_no=c.ref_val and c.user_id=$user_id and c.entry_form=1990", "requisition_no", "id");

        $orderInfoReq = sql_select("SELECT a.style_ref_no, b.po_number, a.job_no, a.buyer_name, d.requisition_no from wo_po_details_master a, wo_po_break_down b, ppl_planning_info_entry_dtls c, ppl_yarn_requisition_entry d, ppl_planning_entry_plan_dtls, e GBL_TEMP_ENGINE f where a.job_no = b.job_no_mst and c.id = d.knit_id and c.id = e.dtls_id and b.id = e.po_id and d.requisition_no=f.ref_val and f.user_id=$user_id and f.entry_form=1990");

        foreach ($orderInfoReq as $value) {
            $orderInfoArrReq['req_wise'][$value[csf('requisition_no')]]['po_number'] = $value[csf('po_number')];
            $orderInfoArrReq['req_wise'][$value[csf('requisition_no')]]['style'] = $value[csf('style_ref_no')];
            $orderInfoArrReq['req_wise'][$value[csf('requisition_no')]]['buyer'] = $buyer_arr[$value[csf('buyer_name')]];
        }
    }
    /* if(count($requIDUnique) > 0) {

        $program_arr = return_library_array("select a.id, b.requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b where a.id = b.knit_id $requ_id_cond", "requisition_no", "id");
        $orderInfoReq = sql_select("select a.style_ref_no, b.po_number, a.job_no, a.buyer_name, d.requisition_no from wo_po_details_master a, wo_po_break_down b, ppl_planning_info_entry_dtls c, ppl_yarn_requisition_entry d, ppl_planning_entry_plan_dtls e where a.job_no = b.job_no_mst and c.id = d.knit_id and c.id = e.dtls_id and b.id = e.po_id $requ_id_cond1");
        foreach ($orderInfoReq as $value){
            $orderInfoArrReq['req_wise'][$value[csf('requisition_no')]]['po_number'] = $value[csf('po_number')];
            $orderInfoArrReq['req_wise'][$value[csf('requisition_no')]]['style'] = $value[csf('style_ref_no')];
            $orderInfoArrReq['req_wise'][$value[csf('requisition_no')]]['buyer'] = $buyer_arr[$value[csf('buyer_name')]];
        }
    } */
    //    print_r($orderInfoArrReq);
    $orderInfoArrMain = array_merge($orderInfoArr, $orderInfoArrReq);

    $usd_arr = array();
    $sqlSelectData = sql_select("select con_date,conversion_rate from currency_conversion_rate where currency=2 and is_deleted=0 order by con_date desc");
    foreach ($sqlSelectData as $row) {
        $usd_arr[date('d-m-Y', strtotime($row[csf('con_date')]))] = $row[csf('conversion_rate')];
    }
    //echo "<pre>";print_r($usd_arr);echo "</pre>";

    $con = connect();
    $r_id111 = execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (1990)");
    if ($r_id111) {
        oci_commit($con);
    }

    ob_start();
    ?>
    <div style="width:<? echo $div_width; ?>">
        <fieldset style="width:<? echo $div_width; ?>">
            <table width="<? echo $table_width; ?>" border="0" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left">
                <tr class="form_caption" style="border:none;">
                    <td colspan="13" align="center" style="border:none;font-size:16px; font-weight:bold; padding: 0px 2px;"><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></td>
                </tr>
                <tr style="border:none;">
                    <td colspan="13" align="center" style="border:none; font-size:13px; padding: 0px 2px;">
                        <?
                        echo rtrim(show_company($cbo_company_name, 1, array("plot_no" => "plot_no", "level_no" => "level_no", "road_no" => "road_no", "block_no" => "block_no", "city" => "city")), ",");
                        ?>
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="13" align="center" style="border:none; font-size:14px; padding: 3px 2px;">
                        <strong>Date Range : <? echo $txt_date_from; ?> To <? echo $txt_date_to; ?></strong>
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="13" align="center" style="border:none; padding: 3px 2px">
                        <strong style="font-size:16px;">Yarn Delivery Report</strong>
                    </td>
                </tr>
            </table>
            <br />
            <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
                <thead>
                    <tr>
                        <th style="padding: 0px 2px;" width="30">SL</th>
                        <th style="padding: 0px 2px;" width="130">Party</th>
                        <th style="padding: 0px 2px;" width="110">Challan No.</th>
                        <th style="padding: 0px 2px;" width="80">Date</th>
                        <th style="padding: 0px 2px;" width="110">Program No.</th>
                        <th style="padding: 0px 2px;" width="130">Buyer</th>
                        <th style="padding: 0px 2px;" width="130">Style No.</th>
                        <th style="padding: 0px 2px;" width="110">Order No.</th>
                        <th style="padding: 0px 2px;" width="110">Color</th>
                        <th style="padding: 0px 2px;" width="80">Yarn Count</th>
                        <th style="padding: 0px 2px;" width="100">Brand</th>
                        <th style="padding: 0px 2px;" width="160">Composition</th>
                        <th style="padding: 0px 2px;" width="80">Yarn Lot</th>
                        <th style="padding: 0px 2px;" width="100">Qty. In KG</th>
                        <th style="padding: 0px 2px;" width="100">Rate</th>
                        <th style="padding: 0px 2px;">Inserted By</th>

                    </tr>
                </thead>
            </table>
            <br />
            <div style="width:<? echo $div_width; ?>; overflow-y: scroll; max-height:260px;" id="scroll_body">
                <table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="1" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                    <tbody>
                        <?
                        $qtyGrandTotal = 0;
                        $counter = 0;
                        $mainCounter = 0;
                        foreach ($issueDataArr as $party_id => $issueNumberArrInner) {
                            $total = 0;
                            $rowspan1 = 0;
                            $counter++;
                            foreach ($issueNumberArrInner as $issueNumber => $itemData) {
                                $qtySubTotal = 0;
                                $rowspan = 0;
                                foreach ($itemData['item_data'] as $key => $value) {

                                    $exchangeRate = $usd_arr[date('d-m-Y', strtotime($value['date']))];
                                    if ($exchangeRate == "") {
                                        foreach ($usd_arr as $rate_date => $rat) {
                                            if (strtotime($rate_date) <= strtotime($value['date'])) {
                                                $rate_date = date('d-m-Y', strtotime($rate_date));
                                                $exchangeRate = $rat;
                                                break;
                                            }
                                        }
                                    }

                                    $qty = $value['issue_qty'];
                                    $qtySubTotal += $qty;
                                    //                                if($counter%2==0) $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                                    if ($rowspan > 0) {
                        ?>
                                        <tr bgcolor="#FFFFFF">
                                            <td style="vertical-align: middle;padding: 0px 2px;" width="80" align="center"><?= $value['date'] ?></td>
                                            <td style="vertical-align: middle;padding: 0px 2px;" width="110"><?= $program_arr[$value['requ_no']] ?></td>
                                            <?
                                            if ($value['job_no'] != "") {
                                            ?>
                                                <td style="vertical-align: middle;padding: 0px 2px;" width="130" align="center"><?= $orderInfoArrMain['job_wise'][$value['job_no']]['buyer'] ?></td>
                                                <td style="vertical-align: middle;padding: 0px 2px;" width="130"><?= $orderInfoArrMain['job_wise'][$value['job_no']]['style'] ?></td>
                                                <td style="vertical-align: middle;padding: 0px 2px;" width="110"><?= $orderInfoArrMain['job_wise'][$value['job_no']]['po_number'] ?></td>
                                            <?
                                            } else {
                                            ?>
                                                <td style="vertical-align: middle;padding: 0px 2px;" width="130" align="center"><?= $orderInfoArrMain['req_wise'][$value['requ_no']]['buyer'] ?></td>
                                                <td style="vertical-align: middle;padding: 0px 2px;" width="130"><?= $orderInfoArrMain['req_wise'][$value['requ_no']]['style'] ?></td>
                                                <td style="vertical-align: middle;padding: 0px 2px;" width="110"><?= $orderInfoArrMain['req_wise'][$value['requ_no']]['po_number'] ?></td>

                                            <?
                                            }
                                            ?>
                                            <td style="vertical-align: middle;padding: 0px 2px;" width="110"><?= $value['color'] ?></td>
                                            <td style="vertical-align: middle;padding: 0px 2px;" width="80" align="center"><?= $value['count'] ?></td>
                                            <td style="vertical-align: middle;padding: 0px 2px;" width="100"><?= $value['brand'] ?></td>
                                            <td style="vertical-align: middle;padding: 0px 2px;" width="160"><?= $value['composition'] ?></td>
                                            <td style="vertical-align: middle;padding: 0px 2px;" width="80"><?= $value['lot'] ?></td>
                                            <td style="vertical-align: middle;padding: 0px 2px;" align="right" width="100"><?= number_format($qty, 2) ?></td>
                                            <td style="vertical-align: middle;padding: 0px 2px;" align="right" width="100">
                                                <?= number_format($value['cons_rate'] / $exchangeRate, 2); ?>
                                            </td>
                                            <td style="vertical-align: middle;padding: 0px 2px;"><?= $value['inserted_by'] ?></td>
                                        </tr>

                                    <?
                                    } else {
                                    ?>
                                        <tr bgcolor="#FFFFFF">
                                            <?
                                            if ($rowspan1 == 0) {
                                            ?>
                                                <td width="30" align="center" style="vertical-align: middle;padding: 0px 2px;" rowspan="<?= $partySpanArr[$party_id] + 1 ?>"><?= $counter ?></td>
                                                <td width="130" style="vertical-align: middle; padding: 0px 2px;" rowspan="<?= $partySpanArr[$party_id] + 1 ?>"><?= $party_id ?></td>
                                            <?
                                            }
                                            ?>
                                            <td width="110" style="vertical-align: middle; padding: 0px 2px;" rowspan="<?= count($itemData['item_data']) + 1 ?>"><?= $issueNumber ?></td>
                                            <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?= $value['date'] ?></td>
                                            <td style="vertical-align: middle; padding: 0px 2px;" width="110"><?= $program_arr[$value['requ_no']] ?></td>
                                            <?
                                            if ($value['job_no'] != "") {
                                            ?>
                                                <td style="vertical-align: middle;padding: 0px 2px;" width="130" align="center"><?= $orderInfoArrMain['job_wise'][$value['job_no']]['buyer'] ?></td>
                                                <td style="vertical-align: middle;padding: 0px 2px;" width="130"><?= $orderInfoArrMain['job_wise'][$value['job_no']]['style'] ?></td>
                                                <td style="vertical-align: middle;padding: 0px 2px;" width="110"><?= $orderInfoArrMain['job_wise'][$value['job_no']]['po_number'] ?></td>
                                            <?
                                            } else {
                                            ?>
                                                <td style="vertical-align: middle;padding: 0px 2px;" width="130" align="center"><?= $orderInfoArrMain['req_wise'][$value['requ_no']]['buyer'] ?></td>
                                                <td style="vertical-align: middle;padding: 0px 2px;" width="130"><?= $orderInfoArrMain['req_wise'][$value['requ_no']]['style'] ?></td>
                                                <td style="vertical-align: middle;padding: 0px 2px;" width="110"><?= $orderInfoArrMain['req_wise'][$value['requ_no']]['po_number'] ?></td>

                                            <?
                                            }
                                            ?>
                                            <td style="vertical-align: middle; padding: 0px 2px;" width="110"><?= $value['color'] ?></td>
                                            <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?= $value['count'] ?></td>
                                            <td style="vertical-align: middle; padding: 0px 2px;" width="100"><?= $value['brand'] ?></td>
                                            <td style="vertical-align: middle; padding: 0px 2px;" width="160"><?= $value['composition'] ?></td>
                                            <td style="vertical-align: middle; padding: 0px 2px;" width="80"><?= $value['lot'] ?></td>
                                            <td style="vertical-align: middle;padding: 0px 2px;" align="right" width="100"><?= number_format($qty, 2) ?></td>
                                            <td style="vertical-align: middle;padding: 0px 2px;" align="right" width="100">
                                                <?= number_format($value['cons_rate'] / $exchangeRate, 2); ?>
                                            </td>
                                            <td style="vertical-align: middle;padding: 0px 2px;"><?= $value['inserted_by'] ?></td>
                                        </tr>
                                <?
                                    }
                                    $rowspan++;
                                    $rowspan1++;
                                }
                                ?>
                                <tr bgcolor="#E9F3FF">
                                    <td style="padding: 0px 2px;" colspan="10" align="right"><strong>Sub Total</strong></td>
                                    <td style="padding: 0px 2px;" align="right"><strong><?= number_format($qtySubTotal, 2) ?></strong></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?
                                $total += $qtySubTotal;
                            }
                            ?>
                            <tr bgcolor="#d3d3d3">
                                <td style="padding: 0px 2px;" colspan="11" align="right"><strong>Total</strong></td>
                                <td style="padding: 0px 2px;" align="right"><strong><?= number_format($total, 2) ?></strong></td>
                                <td></td>
                                <td></td>

                            </tr>

                        <?
                            $qtyGrandTotal += $total;
                            $mainCounter++;
                        }
                        ?>
                        <tr>
                            <td colspan="14"></td>
                        </tr>
                        <tr bgcolor="#ffffe0">
                            <td style="padding: 0px 2px;" colspan="13" align="right"><strong>Grand Total</strong></td>
                            <td style="padding: 0px 2px;" align="right"><strong><?= number_format($qtyGrandTotal, 2) ?></strong></td>
                            <td></td>
                            <td></td>

                        </tr>
                        <tr>
                            <td colspan="14"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </fieldset>
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