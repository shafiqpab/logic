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

if ($action == "print_button_variable_setting") {
    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=6 and report_id=116 and is_deleted=0 and status_active=1", "format_id", "format_id");
    echo "print_report_button_setting('" . implode(',', $print_report_format_arr) . "');\n";
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
            load_drop_down('daily_yarn_issue_report_controller', <? echo $companyID; ?>, 'load_drop_down_buyer', 'buyer_td');
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
                            echo create_drop_down("cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $companyID, "load_drop_down( 'daily_yarn_issue_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
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
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('job_no').value+'_'+document.getElementById('booking_no_prefix_num').value+'_'+document.getElementById('cbo_year').value, 'create_booking_search_list_view', 'search_div', 'daily_yarn_issue_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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

if ($action == "report_generate") {
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));


    if (str_replace("'", "", $cbo_store_name) != 0)
        $store_cond = " and b.store_id in(" . str_replace("'", "", $cbo_store_name) . ")";

    if (str_replace("'", "", $cbo_buyer_id) != 0)
        $buyerCond = " and a.buyer_id in(" . str_replace("'", "", $cbo_buyer_id) . ")";

    if (str_replace("'", "", $txt_booking_no) != "") {
        $booking_no = str_replace(",", "','", $txt_booking_no);
        $booking_cond = " and a.booking_no in($booking_no)";

        $sqlProgram = "select b.requisition_no from ppl_planning_entry_plan_dtls a ,ppl_yarn_requisition_entry b where a.dtls_id=b.knit_id and a.status_active = 1 and a.is_deleted = 0 $booking_cond group by b.requisition_no";
        $sqlProgramRslt = sql_select($sqlProgram);
        $bookingNoArr = array();
        foreach ($sqlProgramRslt as $row) {
            $bookingNoArr[$row[csf('requisition_no')]] = $row[csf('requisition_no')];
        }

        $booking_req_cond = " and b.requisition_no in('" . implode("','", $bookingNoArr) . "')";
    }

    if (str_replace("'", "", $cbo_display_type) != 0)
        $display_cond = " and a.knit_dye_source in(" . str_replace("'", "", $cbo_display_type) . ")";

    if (str_replace("'", "", $cbo_yarn_type) != "")
        $yarn_type_cond = " and c.yarn_type in(" . str_replace("'", "", $cbo_yarn_type) . ")";
    if (str_replace("'", "", $cbo_yarn_count) != "")
        $yarn_count_cond = " and c.yarn_count_id in(" . str_replace("'", "", $cbo_yarn_count) . ")";

    //for lot
    /*
    if (str_replace("'", "", trim($txt_lot_no)) == "")
        $lot_no = "%%";
    else
        $lot_no = "%" . str_replace("'", "", trim($txt_lot_no)) . "%";
	*/
    $txt_lot_no = str_replace("'", "", trim($txt_lot_no));
    $lot_no = '';
    if ($txt_lot_no != "") {
        if ($lot_search_type == 1) {
            /*if($db_type == 2)
			{
				$lot_no = " and regexp_like (c.lot, '^".$txt_lot_no."')";
			}
			else
			{
				$lot_no = " and c.lot like '".$txt_lot_no."%'";
			}*/
            $lot_no = " and c.lot like '%" . $txt_lot_no . "%'";
        } else {
            $lot_no = " and c.lot='" . $txt_lot_no . "'";
        }
    }

    if (str_replace("'", "", $cbo_issue_purpose) != "" && str_replace("'", "", $cbo_issue_purpose) != 0) {
        $issue_purpose_cond = " and a.issue_purpose in (" . str_replace("'", "", $cbo_issue_purpose) . ")";
    } else {
        $issue_purpose_cond = " and a.issue_purpose in (1,2,4,7,8,15,16,38,46,3,5,6,12,26,29,30,39,40,45,50,51,54,74)";
    }

    if (str_replace("'", "", $cbo_basis) != "" && str_replace("'", "", $cbo_basis) != 0) {
        $issue_basis_cond = " and a.issue_basis in (" . str_replace("'", "", $cbo_basis) . ")";
    }

    if (str_replace("'", "", $cbo_using_item) != "" && str_replace("'", "", $cbo_using_item) != 0)
        $using_item_cond = " and b.using_item in (" . str_replace("'", "", $cbo_using_item) . ")";

    if ($db_type == 0) {
        $from_date = change_date_format(str_replace("'", "", $txt_date_from), 'yyyy-mm-dd');
        $to_date = change_date_format(str_replace("'", "", $txt_date_to), 'yyyy-mm-dd');
    }
    if ($db_type == 2) {
        $from_date = change_date_format(str_replace("'", "", $txt_date_from), '', '', 1);
        $to_date = change_date_format(str_replace("'", "", $txt_date_to), '', '', 1);
    }


    if ($type == 1 || $type == 6) // Show Button
    {
        // $sql = "SELECT a.id as issue_id,a.issue_number, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.buyer_id, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, a.challan_no,b.id as trans_id, b.store_id, b.brand_id, sum(b.cons_quantity) as issue_qnty,b.requisition_no, sum(b.return_qnty) as return_qnty,b.cons_rate,b.cons_amount, b.using_item, c.brand, c.yarn_type, c.yarn_count_id, c.lot, c.color, c.avg_rate_per_unit,c.yarn_comp_type1st,c.yarn_comp_percent1st ,c.id as product_id, a.remarks,a.buyer_job_no from inv_issue_master a, inv_transaction b,  product_details_master c where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.issue_basis not in (1,2) and a.issue_purpose not in (7,12,15,29,38,40,45,46,50,51,74,3) and a.issue_date between '$from_date' and '$to_date' and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lot_no $store_cond $yarn_type_cond $yarn_count_cond $issue_purpose_cond $issue_basis_cond $using_item_cond $buyerCond $booking_req_cond $display_cond group by a.id,a.issue_number, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.buyer_id, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, a.challan_no,b.id,b.store_id, b.brand_id,b.requisition_no,b.cons_rate,b.cons_amount, b.using_item, c.brand, c.yarn_type, c.yarn_count_id, c.lot, c.color, c.avg_rate_per_unit,c.yarn_comp_type1st,c.yarn_comp_percent1st ,c.id, a.remarks,a.buyer_job_no order by a.knit_dye_source,a.issue_number, a.issue_date";
        $sql = "SELECT a.id as issue_id,a.issue_number, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.buyer_id, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, a.challan_no,b.id as trans_id, b.store_id, b.brand_id, sum(b.cons_quantity) as issue_qnty,b.requisition_no, sum(b.return_qnty) as return_qnty,b.cons_rate,b.cons_amount, b.using_item, c.brand, c.yarn_type, c.yarn_count_id, c.lot, c.color, c.avg_rate_per_unit,c.yarn_comp_type1st,c.yarn_comp_percent1st ,c.id as product_id, a.remarks,a.buyer_job_no from inv_issue_master a, inv_transaction b,  product_details_master c where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.issue_basis not in (2) and a.issue_purpose not in (7,12,15,29,38,40,45,46,50,51,74,3) and a.issue_date between '$from_date' and '$to_date' and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lot_no $store_cond $yarn_type_cond $yarn_count_cond $issue_purpose_cond $issue_basis_cond $using_item_cond $buyerCond $booking_req_cond $display_cond group by a.id,a.issue_number, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.buyer_id, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, a.challan_no,b.id,b.store_id, b.brand_id,b.requisition_no,b.cons_rate,b.cons_amount, b.using_item, c.brand, c.yarn_type, c.yarn_count_id, c.lot, c.color, c.avg_rate_per_unit,c.yarn_comp_type1st,c.yarn_comp_percent1st ,c.id, a.remarks,a.buyer_job_no
        order by a.knit_dye_source,a.issue_number, a.issue_date";
        // echo $sql; die;

        $sql_summary = "SELECT a.knit_dye_source, sum(b.cons_quantity) as issue_qnty from inv_issue_master a, inv_transaction b, product_details_master c where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.issue_date between '$from_date' and '$to_date' and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lot_no $store_cond $yarn_type_cond $yarn_count_cond $issue_purpose_cond $issue_basis_cond $using_item_cond $buyerCond $booking_req_cond $display_cond group by a.knit_dye_source order by a.knit_dye_source";

        $query = "SELECT a.issue_number, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.buyer_id, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no, a.other_party, b.requisition_no, b.store_id, b.brand_id, b.cons_quantity as issue_qnty, b.return_qnty, b.cons_rate, b.cons_amount, c.brand, c.yarn_count_id, c.yarn_type, c.lot, c.color, c.avg_rate_per_unit, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.id as product_id from inv_issue_master a, inv_transaction b, product_details_master c where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.issue_basis in (1,2) and a.issue_purpose in (3,d4,7,8,12,15,29,38,40,45,46,50,51,74) and a.issue_date between '$from_date' and '$to_date' and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lot_no $yarn_type_cond $yarn_count_cond $issue_purpose_cond $using_item_cond $booking_req_cond order by a.issue_purpose, a.issue_date";

        $nameArray = sql_select($query);

        // echo $sql;die;
        $result = sql_select($sql);
        $result_summery = sql_select($sql_summary);
        $transIdChk = array();
        $trans_arr = array();
        $productIdsChk = array();
        $productIds_arr = array();
        $knit_dye_source_wise_issue_qnty_arr = array();
        $issue_purpose_wise_issue_qnty_arr = array();
        $sample_without_order_issue_qnty_arr = array();
        $booking_ids = array();
        $$samp_info = array();
        foreach ($result as $row) {

            if ($row[csf("booking_no")] != "") {
                $all_booking_no_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
            }

            if ($row[csf("requisition_no")] != "") {
                $requisition_nos .= $row[csf("requisition_no")] . ",";
            }

            $buyer_ids .= $row[csf("buyer_id")] . ",";
            $color_ids .= $row[csf("color")] . ",";
            $product_ids .= $row[csf("product_id")] . ",";
            $yarn_count_ids .= $row[csf("yarn_count_id")] . ",";
            $brand_ids .= $row[csf("brand")] . ",";
            $supplier_ids .= $row[csf("knit_dye_company")] . ",";
            $location_ids .= $row[csf("location_id")] . ",";
            $store_ids .= $row[csf("store_id")] . ",";


            $trans_ids .= $row[csf("trans_id")] . ",";
            $issue_numbers .= "'" . $row[csf("issue_number")] . "',";

            if ($row[csf("knit_dye_source")] == 1 || $row[csf("knit_dye_source")] == 3) {
                $knit_dye_source_wise_issue_qnty_arr[$row[csf("knit_dye_source")]] += $row[csf("issue_qnty")];
            }

            if ($row[csf("issue_purpose")] != 1 &&  $row[csf("issue_purpose")] != 2 && $row[csf("issue_purpose")] != 7 && $row[csf("issue_purpose")] != 8 && $row[csf("issue_purpose")] != 4 && $row[csf("issue_purpose")] != 4 && $row[csf("issue_purpose")] != 29 && $row[csf("issue_purpose")] != 40 && $row[csf("issue_purpose")] != 45 && $row[csf("issue_purpose")] != 74 && $row[csf("issue_purpose")] != 3) {
                $issue_purpose_wise_issue_qnty_arr[$row[csf("issue_purpose")]] += $row[csf("issue_qnty")];
            }

            if ($row[csf("issue_purpose")] == 8) {
                $sample_without_order_issue_qnty_arr[$row[csf("issue_purpose")]] += $row[csf("issue_qnty")];
            }

            if ($row[csf("issue_basis")] == 8) {
                if ($transIdChk[$row[csf("trans_id")]] == "") {
                    $transIdChk[$row[csf("trans_id")]] = $row[csf("trans_id")];
                    array_push($trans_arr, $row[csf('trans_id')]);
                }

                if ($productIdsChk[$row[csf("product_id")]] == "") {
                    $productIdsChk[$row[csf("product_id")]] = $row[csf("product_id")];
                    array_push($productIds_arr, $row[csf('product_id')]);
                }
            }
            if ($row[csf("issue_basis")] == 1) {
                if ($row[csf("issue_purpose")] == 2) {
                    //  echo "**".$row[csf('booking_id')]."**";
                    $booking_ids[$row[csf('issue_number')]]['BOOKING_ID'] =  $row[csf('booking_id')];
                }
            }
        }

        $sample = sql_select("SELECT a.mst_id,
                    a.job_no_id,
                    a.booking_no,
                    b.internal_ref
                    FROM wo_yarn_dyeing_dtls a, sample_development_mst b
                    WHERE     a.job_no_id = b.id
                --  AND a.mst_id = 3589
                    AND a.status_active = 1
                    AND a.is_deleted = 0
                    GROUP BY a.mst_id,
                    a.job_no_id,
                    a.booking_no,
                    b.internal_ref");
        foreach ($sample as $row) {
            $samp_info[$row['MST_ID']]['INTERNAL_REF'] = $row['INTERNAL_REF'];
        }


        foreach ($nameArray as $row) {
            $issue_purpose_wise_issue_qnty_arr[$row[csf("issue_purpose")]] += $row[csf("issue_qnty")];
        }
        //var_dump($knit_dye_source_wise_issue_qnty_arr);die;

        //echo $location_ids; die;
        $booking_nos = chop($booking_nos, ",");
        $buyer_ids = chop($buyer_ids, ",");
        $supplier_ids = chop($supplier_ids, ",");
        $location_ids = chop($location_ids, ",");
        $store_ids = chop($store_ids, ",");
        $color_ids = chop($color_ids, ",");
        $product_ids = chop($product_ids, ",");
        $yarn_count_ids = chop($yarn_count_ids, ",");
        $brand_ids = chop($brand_ids, ",");
        $requisition_nos = chop($requisition_nos, ",");
        $trans_ids = chop($trans_ids, ",");
        $issue_numbers = chop($issue_numbers, ",");

        $buyer_ids = implode(",", array_filter(array_unique(explode(",", $buyer_ids))));
        $supplier_ids = implode(",", array_filter(array_unique(explode(",", $supplier_ids))));
        $location_ids = implode(",", array_filter(array_unique(explode(",", $location_ids))));
        $store_ids = implode(",", array_filter(array_unique(explode(",", $store_ids))));
        $color_ids = implode(",", array_filter(array_unique(explode(",", $color_ids))));
        $product_ids = implode(",", array_filter(array_unique(explode(",", $product_ids))));
        $yarn_count_ids = implode(",", array_filter(array_unique(explode(",", $yarn_count_ids))));
        $brand_ids = implode(",", array_filter(array_unique(explode(",", $brand_ids))));
        $issue_numbers = implode(",", array_filter(array_unique(explode(",", $issue_numbers))));

        if (!empty($trans_arr)) {
            $sql_sales_info = "SELECT a.trans_id,a.prod_id, b.job_no,b.style_ref_no, b.po_job_no, b.sales_booking_no,b.customer_buyer from order_wise_pro_details a, fabric_sales_order_mst b
            where a.po_breakdown_id=b.id " . where_con_using_array($trans_arr, 0, 'a.trans_id') . " ";
            //echo $sql_sales_info;
            $rslt_sales_info = sql_select($sql_sales_info);

            $all_sales_info_arr = array();
            foreach ($rslt_sales_info as $s_row) {
                $all_sales_info_arr[$s_row[csf('trans_id')]][$s_row[csf('prod_id')]]['job_no'] = $s_row[csf('job_no')];
                $all_sales_info_arr[$s_row[csf('trans_id')]][$s_row[csf('prod_id')]]['style_ref_no'] = $s_row[csf('style_ref_no')];
                $all_sales_info_arr[$s_row[csf('trans_id')]][$s_row[csf('prod_id')]]['po_job_no'] = $s_row[csf('po_job_no')];
                $all_sales_info_arr[$s_row[csf('trans_id')]][$s_row[csf('prod_id')]]['sales_booking_no'] = $s_row[csf('sales_booking_no')];
                $all_sales_info_arr[$s_row[csf('trans_id')]][$s_row[csf('prod_id')]]['customer_buyer'] = $s_row[csf('customer_buyer')];
            }
            //echo "<pre>";print_r($all_sales_info_arr);
        }

        if (!empty($productIds_arr)) {
            $sql_rcv_info = "SELECT a.booking_no,b.prod_id from inv_receive_master a, inv_transaction b
            where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=1 and b.transaction_type=1  " . where_con_using_array($productIds_arr, 0, 'b.prod_id') . " ";
            //echo $sql_rcv_info;
            $rslt_rcv_info = sql_select($sql_rcv_info);

            $all_rcv_info_arr = array();
            foreach ($rslt_rcv_info as $rcv_row) {
                $all_rcv_info_arr[$rcv_row[csf('prod_id')]]['booking_no'] = $rcv_row[csf('booking_no')];
            }
        }

        $demand_sql = "SELECT b.ISSUE_NUMBER,
        a.KNITTING_SOURCE, a.KNITTING_COMPANY, c.REQUISITION_NO, e.booking_no,
        sum(d.YARN_QNTY) as req_qnty
        from PPL_YARN_DEMAND_ENTRY_MST a , INV_ISSUE_MASTER b, PPL_YARN_DEMAND_REQSN_DTLS c, PPL_YARN_REQUISITION_ENTRY d, 
            PPL_PLANNING_ENTRY_PLAN_DTLS e,  INV_TRANSACTION h
        where h.DEMAND_NO = a.DEMAND_SYSTEM_NO
            and h.mst_id = b.id
            and c.mst_id = a.id
            and c.REQUISITION_NO = d.REQUISITION_NO
            and e.DTLS_ID = d.knit_id
            and a.status_active = 1
            and b.status_active = 1
            and c.status_active = 1
            and d.status_active = 1
            and e.status_active = 1
            and h.status_active = 1
            and b.COMPANY_ID = $cbo_company_name
        group by
            b.ISSUE_NUMBER,
        a.KNITTING_SOURCE, a.KNITTING_COMPANY, c.REQUISITION_NO, e.booking_no";
        $demand_arr = sql_select($demand_sql);
        $demand_dtls_arr = array();
        foreach ($demand_arr as $row) {
            $demand_dtls_arr[$row['ISSUE_NUMBER']]['KNITTING_SOURCE'] = $row['KNITTING_SOURCE'];
            $demand_dtls_arr[$row['ISSUE_NUMBER']]['KNITTING_COMPANY'] = $row['KNITTING_COMPANY'];
            $demand_dtls_arr[$row['ISSUE_NUMBER']]['REQUISITION_NO'] = $row['REQUISITION_NO'];
            $demand_dtls_arr[$row['ISSUE_NUMBER']]['BOOKING_NO'] = $row['BOOKING_NO'];
            $demand_dtls_arr[$row['ISSUE_NUMBER']]['REQ_QNTY'] = $row['REQ_QNTY'];
        }
    } else if ($type == 2) // // Report-2
    {

        $sql = "SELECT b.cons_quantity as issue_qnty, c.brand,c.yarn_type, c.yarn_count_id
        from inv_issue_master a, inv_transaction b, product_details_master c
        where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.issue_date between '$from_date' and '$to_date' and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lot_no $store_cond $yarn_type_cond $yarn_count_cond $issue_purpose_cond $using_item_cond $buyerCond $booking_req_cond $display_cond
        order by a.knit_dye_source,a.issue_number, a.issue_date";
        $result = sql_select($sql);
    } else if ($type == 3) // order wise separated row Sql // Report-3
    {

        $sql = "SELECT a.id as issue_id,a.issue_number, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.buyer_id, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, a.challan_no,b.id as trans_id, b.requisition_no, b.store_id, b.brand_id, b.cons_quantity as issue_qnty,b.requisition_no,  b.return_qnty,b.cons_rate,b.cons_amount, b.using_item, c.brand, c.yarn_type, c.yarn_count_id, c.lot, c.color, c.avg_rate_per_unit,c.yarn_comp_type1st,c.yarn_comp_percent1st ,c.id as product_id,d.po_breakdown_id
        from inv_issue_master a, inv_transaction b, product_details_master c, order_wise_pro_details d
        where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.issue_date between '$from_date' and '$to_date' and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and b.id=d.trans_id and  d.entry_form=3 and d.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lot_no $yarn_type_cond $yarn_count_cond $issue_purpose_cond $store_cond $using_item_cond $buyerCond $booking_req_cond $display_cond
        order by a.knit_dye_source,c.yarn_comp_type1st,c.yarn_comp_percent1st,a.issue_number, a.issue_date";

        $result = sql_select($sql);
        foreach ($result as $row) {

            if ($row[csf("booking_no")] != "") {
                $all_booking_no_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
            }

            if ($row[csf("requisition_no")] != "") {
                $requisition_nos .= $row[csf("requisition_no")] . ",";
            }

            $buyer_ids .= $row[csf("buyer_id")] . ",";
            $color_ids .= $row[csf("color")] . ",";
            $product_ids .= $row[csf("product_id")] . ",";
            $yarn_count_ids .= $row[csf("yarn_count_id")] . ",";
            $brand_ids .= $row[csf("brand")] . ",";
            $supplier_ids .= $row[csf("knit_dye_company")] . ",";
            $location_ids .= $row[csf("location_id")] . ",";
            $store_ids .= $row[csf("store_id")] . ",";


            $trans_ids .= $row[csf("trans_id")] . ",";
            $issue_numbers .= "'" . $row[csf("issue_number")] . "',";
        }
        //echo $location_ids; die;
        $booking_nos = chop($booking_nos, ",");
        $buyer_ids = chop($buyer_ids, ",");
        $supplier_ids = chop($supplier_ids, ",");
        $location_ids = chop($location_ids, ",");
        $store_ids = chop($store_ids, ",");
        $color_ids = chop($color_ids, ",");
        $product_ids = chop($product_ids, ",");
        $yarn_count_ids = chop($yarn_count_ids, ",");
        $brand_ids = chop($brand_ids, ",");
        $requisition_nos = chop($requisition_nos, ",");
        $trans_ids = chop($trans_ids, ",");
        $issue_numbers = chop($issue_numbers, ",");

        $buyer_ids = implode(",", array_filter(array_unique(explode(",", $buyer_ids))));
        $supplier_ids = implode(",", array_filter(array_unique(explode(",", $supplier_ids))));
        $location_ids = implode(",", array_filter(array_unique(explode(",", $location_ids))));
        $store_ids = implode(",", array_filter(array_unique(explode(",", $store_ids))));
        $color_ids = implode(",", array_filter(array_unique(explode(",", $color_ids))));
        $product_ids = implode(",", array_filter(array_unique(explode(",", $product_ids))));
        $yarn_count_ids = implode(",", array_filter(array_unique(explode(",", $yarn_count_ids))));
        $brand_ids = implode(",", array_filter(array_unique(explode(",", $brand_ids))));
        $issue_numbers = implode(",", array_filter(array_unique(explode(",", $issue_numbers))));
    }

    if ($type == 5) // SWO-With Plan Button
    {
        $sql = "SELECT a.id as issue_id,a.issue_number, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.buyer_id, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, a.challan_no,b.id as trans_id, b.store_id, b.brand_id, sum(b.cons_quantity) as issue_qnty,b.requisition_no, sum(b.return_qnty) as return_qnty,b.cons_rate,b.cons_amount, b.using_item, c.brand, c.yarn_type, c.yarn_count_id, c.lot, c.color, c.avg_rate_per_unit,c.yarn_comp_type1st,c.yarn_comp_percent1st ,c.id as product_id from inv_issue_master a, inv_transaction b,  product_details_master c where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.issue_purpose in (1,8) and a.issue_date between '$from_date' and '$to_date' and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lot_no $store_cond $yarn_type_cond $yarn_count_cond $using_item_cond $buyerCond $booking_req_cond $display_cond
        group by a.id,a.issue_number, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.buyer_id, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, a.challan_no,b.id,b.store_id, b.brand_id,b.requisition_no,b.cons_rate,b.cons_amount, b.using_item, c.brand, c.yarn_type, c.yarn_count_id, c.lot, c.color, c.avg_rate_per_unit,c.yarn_comp_type1st,c.yarn_comp_percent1st ,c.id
        order by a.knit_dye_source,a.issue_number, a.issue_date";
        //echo $sql;
        $result = sql_select($sql);
        $result_summery = sql_select($sql_summary);
        foreach ($result as $row) {

            if ($row[csf("booking_no")] != "") {
                $all_booking_no_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
            }

            if ($row[csf("requisition_no")] != "") {
                $requisition_nos .= $row[csf("requisition_no")] . ",";
            }

            $buyer_ids .= $row[csf("buyer_id")] . ",";
            $color_ids .= $row[csf("color")] . ",";
            $product_ids .= $row[csf("product_id")] . ",";
            $yarn_count_ids .= $row[csf("yarn_count_id")] . ",";
            $brand_ids .= $row[csf("brand")] . ",";
            $supplier_ids .= $row[csf("knit_dye_company")] . ",";
            $location_ids .= $row[csf("location_id")] . ",";
            $store_ids .= $row[csf("store_id")] . ",";


            $trans_ids .= $row[csf("trans_id")] . ",";
            $issue_numbers .= "'" . $row[csf("issue_number")] . "',";

            if ($row[csf("issue_purpose")] != 1 &&  $row[csf("issue_purpose")] != 2 &&  $row[csf("issue_purpose")] != 8 &&  $row[csf("issue_purpose")] != 4) {
                $issue_purpose_wise_issue_qnty_arr[$row[csf("issue_purpose")]] += $row[csf("issue_qnty")];
            }

            if ($row[csf("issue_purpose")] == 8) {
                $sample_without_order_issue_qnty_arr[$row[csf("issue_purpose")]] += $row[csf("issue_qnty")];
            }
        }

        //echo $location_ids; die;
        $booking_nos = chop($booking_nos, ",");
        $buyer_ids = chop($buyer_ids, ",");
        $supplier_ids = chop($supplier_ids, ",");
        $location_ids = chop($location_ids, ",");
        $store_ids = chop($store_ids, ",");
        $color_ids = chop($color_ids, ",");
        $product_ids = chop($product_ids, ",");
        $yarn_count_ids = chop($yarn_count_ids, ",");
        $brand_ids = chop($brand_ids, ",");
        $requisition_nos = chop($requisition_nos, ",");
        $trans_ids = chop($trans_ids, ",");
        $issue_numbers = chop($issue_numbers, ",");

        $buyer_ids = implode(",", array_filter(array_unique(explode(",", $buyer_ids))));
        $supplier_ids = implode(",", array_filter(array_unique(explode(",", $supplier_ids))));
        $location_ids = implode(",", array_filter(array_unique(explode(",", $location_ids))));
        $store_ids = implode(",", array_filter(array_unique(explode(",", $store_ids))));
        $color_ids = implode(",", array_filter(array_unique(explode(",", $color_ids))));
        $product_ids = implode(",", array_filter(array_unique(explode(",", $product_ids))));
        $yarn_count_ids = implode(",", array_filter(array_unique(explode(",", $yarn_count_ids))));
        $brand_ids = implode(",", array_filter(array_unique(explode(",", $brand_ids))));
        $issue_numbers = implode(",", array_filter(array_unique(explode(",", $issue_numbers))));
    }

    if ($supplier_ids != "") {
        $supplier_ids_cond = "and id in ($supplier_ids)";
    } else {
        $supplier_ids_cond = "";
    }
    if ($location_ids != "") {
        $location_ids_cond = "and id in ($location_ids)";
    } else {
        $location_ids_cond = "";
    }
    if ($store_ids != "") {
        $store_ids_cond = "and id in ($store_ids)";
    } else {
        $store_ids_cond = "";
    }
    //if ($buyer_ids!="") {$buyer_ids_cond="and id in ($buyer_ids)";}else{$buyer_ids_cond="";}
    if ($color_ids != "") {
        $color_ids_cond = "and id in ($color_ids)";
    } else {
        $color_ids_cond = "";
    }
    if ($yarn_count_ids != "") {
        $yarn_count_ids_cond = "and id in ($yarn_count_ids)";
    } else {
        $yarn_count_ids_cond = "";
    }
    if ($brand_ids != "") {
        $brand_ids_cond = "and id in ($brand_ids)";
    } else {
        $brand_ids_cond = "";
    }

    $company_arr = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $supplier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 $supplier_ids_cond and is_deleted=0", "id", "supplier_name");
    $locat_arr = return_library_array("select id, store_location from lib_store_location where status_active=1  $location_ids_cond and is_deleted=0", "id", "store_location");

    $location_arr = return_library_array("select id,location_name from lib_location where status_active =1 and is_deleted=0 $location_ids_cond ", "id", "location_name");
    //echo "nnnnnnnn";
    //print_r($location_arr);

    $store_arr = return_library_array("select id, store_name from lib_store_location where  status_active=1 $store_ids_cond and is_deleted=0", "id", "store_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
    $color_arr = return_library_array("select id,color_name from lib_color where status_active=1 $color_ids_cond and is_deleted=0", "id", "color_name");
    $count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 $yarn_count_ids_cond and is_deleted=0", 'id', 'yarn_count');
    $brand_arr = return_library_array("select id, brand_name from lib_brand where status_active=1 $brand_ids_cond and is_deleted=0", 'id', 'brand_name');
    $other_party_arr = return_library_array("select id,other_party_name from lib_other_party", "id", "other_party_name");

    $booking_arr = array();
    $all_job_arr = array();
    $bookingIdArr = array();

    $dataArray = sql_select("select a.id,a.buyer_id, a.booking_no, b.job_no, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name  group by a.id,a.buyer_id, a.booking_no, b.job_no "); // and a.company_id=$cbo_company_name    $job_no_cond
    foreach ($dataArray as $row) {
        $booking_arr[$row[csf('booking_no')]]['qnty'] = $row[csf('qnty')];
        $booking_arr[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
        $booking_arr[$row[csf('booking_no')]]['job'] = $row[csf('job_no')];
        $booking_arr[$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];
        $all_job_arr[$row[csf('job_no')]] = $row[csf('job_no')];
        array_push($bookingIdArr, $row[csf('id')]);
    }
    //var_dump($booking_arr); //and a.booking_no in($booking_nos)
    if (!empty($bookingIdArr)) {
        $salesDataArray = sql_select("SELECT a.id, a.job_no from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_name and a.within_group=1 " . where_con_using_array($bookingIdArr, 0, 'a.booking_id') . " ");
        foreach ($salesDataArray as $row) {
            $all_job_arr[$row[csf('job_no')]] = $row[csf('job_no')];
        }
    }

    // Requ wise Booking show
    $reqibooking_sql = "SELECT a.requisition_no, b.id, c.booking_no,c.buyer_id, b.is_sales
    from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c
    where a.knit_id=b.id and b.mst_id=c.id and c.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
    // and a.company_id=$cbo_company_name    $job_no_cond //  and a.requisition_no='5678'
    // echo $reqibooking_sql;die;
    $reqibookingarray = sql_select($reqibooking_sql);
    $reqibooking_arr = array();
    foreach ($reqibookingarray as $row) {
        $reqibooking_arr[$row[csf('requisition_no')]]['booking_no'] = $row[csf('booking_no')];
        $reqibooking_arr[$row[csf('requisition_no')]]['buyer_id'] = $row[csf('buyer_id')];
        $reqibooking_arr[$row[csf('requisition_no')]]['program_no'] = $row[csf('id')];
        $reqibooking_arr[$row[csf('requisition_no')]]['is_sales'] = $row[csf('is_sales')];
    }
    // echo "<pre>";
    // print_r($reqibooking_arr);die;

    $all_JOB_cond = "";
    $JobCond = "";
    $all_job_arr = array_filter($all_job_arr);
    $all_job_ids = "'" . implode("','", $all_job_arr) . "'";

    if ($db_type == 2 && count($all_job_arr) > 999) {
        $all_job_chunk_arr = array_chunk($all_job_arr, 999);
        foreach ($all_job_chunk_arr as $chunk_arr) {
            $chunk_arr_value = "'" . implode("','", $chunk_arr) . "'";
            $JobCond .= " a.job_no in($chunk_arr_value) or ";
        }

        $all_JOB_cond .= " and (" . chop($JobCond, 'or ') . ")";
    } else {
        $all_JOB_cond = " and a.job_no in($all_job_ids)";
    }

    if ($all_JOB_cond != "") {
        $data_yarn = sql_select("select b.id, b.ydw_no, a.job_no, sum(a.yarn_wo_qty) as qnty, a.fab_booking_no from wo_yarn_dyeing_mst b, wo_yarn_dyeing_dtls a where b.id=a.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $all_JOB_cond group by b.id, b.ydw_no, a.job_no, a.fab_booking_no "); //

        $yarn_booking_array = array();
        $yarn_sales_booking_array = array();
        foreach ($data_yarn as $row) {
            $yarn_booking_array[$row[csf('ydw_no')]]['qnty'] = $row[csf('qnty')];
            $yarn_booking_array[$row[csf('ydw_no')]]['job'] = $row[csf('job_no')];
            $yarn_sales_booking_array[$row[csf('ydw_no')]][$row[csf('job_no')]]['fab_booking_no'] = $row[csf('fab_booking_no')];
            $all_job_arr[$row[csf('job_no')]] = $row[csf('job_no')];
        }
    }

    $job_array = array();
    $job_array_order_wise = array();
    $po_array = array();

    $sql_job = "SELECT a.job_no, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.file_no, b.grouping as ref_no, c.booking_no, c.copmposition, c.booking_type, c.is_short
	from wo_po_details_master a, wo_booking_dtls c, wo_po_break_down b
	where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $all_JOB_cond
	group by a.job_no, a.buyer_name, a.style_ref_no,b.id, b.po_number,b.file_no,b.grouping,c.booking_no,c.copmposition, c.booking_type, c.is_short";
    //echo $sql_job;
    $sql_job_result = sql_select($sql_job);
    foreach ($sql_job_result as $row) {
        $job_array[$row[csf('job_no')]]['job_no'] = $row[csf('job_no')];
        $job_array[$row[csf('job_no')]]['buyer_name'] = $row[csf('buyer_name')];
        $job_array[$row[csf('job_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
        $job_array[$row[csf('job_no')]]['copmposition'] = $row[csf('copmposition')];
        $job_array[$row[csf('job_no')]]['po_number'] .= $row[csf('po_number')] . ',';

        $po_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];

        $job_array[$row[csf('booking_no')]]['po_number'][$row[csf('po_number')]] = $row[csf('po_number')];
        $job_array[$row[csf('booking_no')]]['ref'][$row[csf('ref_no')]] = $row[csf('ref_no')];
        $job_array[$row[csf('booking_no')]]['file'][$row[csf('file_no')]] = $row[csf('file_no')];
        $job_array[$row[csf('booking_no')]]['buyer'] = $row[csf('buyer_name')];
        $job_array_order_wise[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
        $job_array_order_wise[$row[csf('booking_no')]][$row[csf('id')]]['ref'][$row[csf('ref_no')]] = $row[csf('ref_no')];

        if ($row[csf('booking_type')] == 1 && $row[csf('is_short')] == 1) {
            $job_array[$row[csf('booking_no')]]['booking_type'][$row[csf('booking_type')]] = 'Short';
        } else  if ($row[csf('booking_type')] == 1 && $row[csf('is_short')] == 2) {
            $job_array[$row[csf('booking_no')]]['booking_type'][$row[csf('booking_type')]] = 'Main';
        }
    }
    $booking_without_array = array();
    $data_without = sql_select("select a.id, a.booking_no, a.buyer_id, sum(b.grey_fabric) as qnty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_name and b.status_active=1 and b.is_deleted=0 group by a.id, a.booking_no, a.buyer_id");
    foreach ($data_without as $row) {
        $booking_without_array[$row[csf('booking_no')]]['qnty'] = $row[csf('qnty')];
        $booking_without_array[$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];
        $booking_without_array[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
        $booking_arr[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
    }

    if ($issue_numbers != "") {
        $issue_numbers = explode(",", $issue_numbers);
        $issue_numbers_chnk = array_chunk($issue_numbers, 999);
        $issue_no_cond = " and";
        foreach ($issue_numbers_chnk as $dtls_id) {
            if ($issue_no_cond == " and")  $issue_no_cond .= "(c.issue_number in(" . implode(',', $dtls_id) . ")";
            else $issue_no_cond .= " or c.issue_number in(" . implode(',', $dtls_id) . ")";
        }
        $issue_no_cond .= ")";
        //echo $issue_no_cond;die;

        // echo "SELECT a.recv_number,a.booking_no, b.cons_quantity, c.issue_number,b.prod_id, b.id as trans_id from inv_receive_master a, inv_transaction b, inv_issue_master c where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 $issue_no_cond";

        $issue_return_res = sql_select("SELECT a.recv_number,a.booking_no, b.cons_quantity, c.issue_number,b.prod_id, b.id as trans_id from inv_receive_master a, inv_transaction b, inv_issue_master c where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 $issue_no_cond");
    }

    $transIdChk = array();
    foreach ($issue_return_res as $val) {
        if ($transIdChk[$val[csf("trans_id")]] == "") {
            $transIdChk[$val[csf("trans_id")]] = $val[csf("trans_id")];
            $issue_return_qnty_arr[$val[csf("issue_number")]][$val[csf("booking_no")]][$val[csf("prod_id")]] += $val[csf("cons_quantity")];
        }
    }


    if ($requisition_nos != "") {

        $requisition_arr = array();
        $requisition_nos = explode(",", $requisition_nos);
        $requisition_nos_chnk = array_chunk($requisition_nos, 999);
        $requisition_nos_cond = " and";
        foreach ($requisition_nos_chnk as $dtls_id) {
            if ($requisition_nos_cond == " and")  $requisition_nos_cond .= "(requisition_no in(" . implode(',', $dtls_id) . ")";
            else $requisition_nos_cond .= " or requisition_no in(" . implode(',', $dtls_id) . ")";
        }
        $requisition_nos_cond .= ")";
        //echo $requisition_nos_cond;die;
        // echo "select requisition_no, knit_id, sum(yarn_qnty) as qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0  $requisition_nos_cond group by requisition_no,knit_id";
        $datareqsnArray = sql_select("select requisition_no, knit_id, sum(yarn_qnty) as qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0  $requisition_nos_cond group by requisition_no,knit_id");
        foreach ($datareqsnArray as $row) {
            $requisition_arr[$row[csf('requisition_no')]]['qnty'] = $row[csf('qnty')];
            $requisition_arr[$row[csf('requisition_no')]]['knit_id'] = $row[csf('knit_id')];

            $knit_ids .= $row[csf("knit_id")] . ",";
        }
    }

    $knit_ids = chop($knit_ids, ",");
    $knit_ids = implode(",", array_filter(array_unique(explode(",", $knit_ids))));

    if ($knit_ids != "") {
        $knit_ids = explode(",", $knit_ids);
        $knit_ids_chnk = array_chunk($knit_ids, 999);
        $knit_ids_cond = " and";
        foreach ($knit_ids_chnk as $dtls_id) {
            if ($knit_ids_cond == " and")  $knit_ids_cond .= "(b.id in(" . implode(',', $dtls_id) . ")";
            else $knit_ids_cond .= " or b.id in(" . implode(',', $dtls_id) . ")";
        }
        $knit_ids_cond .= ")";
        //echo $knit_ids_cond;die;
        $planning_arr = return_library_array("select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id  $knit_ids_cond", "id", "booking_no");
        //var_dump($planning_arr);

        $sql_planning_info_arr =  "select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id  $knit_ids_cond and a.status_active=1 and a. is_deleted=0 and b.status_active=1 and b.is_deleted=0";
        $booking_no_arr = array();
        foreach (sql_select($sql_planning_info_arr) as $rows) {
            array_push($booking_no_arr, $rows[csf('booking_no')]);
        }

        $booking_no_cond = where_con_using_array($booking_no_arr, 1, "a.booking_no");

        $sql_smn_info =  "SELECT c.id, c.requisition_number, c.company_id, c.buyer_name, c.style_ref_no, a.booking_no, c.buyer_ref,c.internal_ref, a.booking_type from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_mst c
        where a.booking_no=b.booking_no and b.style_id=c.id and  c.company_id=$cbo_company_name $booking_no_cond and c.entry_form_id=203 and a.entry_form_id=140 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
        and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by c.id DESC";

        //echo $sql_smn_info;

        $smn_info_arr = array();
        $smn_ref_info_arr = array();
        foreach (sql_select($sql_smn_info) as $row) {
            $smn_info_arr[$row[csf('booking_no')]]['requisition_number'] = $row[csf('requisition_number')];
            $smn_info_arr[$row[csf('booking_no')]]['buyer_name'] = $row[csf('buyer_name')];
            $smn_info_arr[$row[csf('booking_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
            $smn_info_arr[$row[csf('booking_no')]]['buyer_ref'] = $row[csf('buyer_ref')];
            $smn_info_arr[$row[csf('booking_no')]]['booking_type'] = $row[csf('booking_type')];
            $smn_ref_info_arr[$row[csf('booking_no')]]['ref'][$row[csf('internal_ref')]] = $row[csf('internal_ref')];

            if ($row[csf('booking_type')] == 4) {
                $smn_ref_info_arr[$row[csf('booking_no')]]['booking_type'][$row[csf('booking_type')]] = 'Sample';
            }
        }
        //var_dump($smn_info_arr);

    }

    $usd_arr = array();
    $sqlSelectData = sql_select("select con_date,conversion_rate from currency_conversion_rate where currency=2 and is_deleted=0 order by con_date desc");
    foreach ($sqlSelectData as $row) {
        $usd_arr[date('d-m-Y', strtotime($row[csf('con_date')]))] = $row[csf('conversion_rate')];
    }

    if ($zero_val == 1) {
        $value_width = 3030;
        $span = 31;
        $column = '';
    } else {
        $value_width = 3280;
        $span = 33;
        $column = '<th rowspan="2" width="90">Avg. Rate (Tk)</th><th rowspan="2" width="110">Stock Value</th>';
    }

    ob_start();

    if ($type == 1) // Show Button
    {
    ?>
        <style>
            .wrd_brk {
                word-break: break-all;
                word-wrap: break-word;
            }
        </style>
        <fieldset style="width:<? echo $value_width + 18; ?>px;">
            <table cellpadding="0" cellspacing="0" width="<? echo $value_width; ?>" style="float: left;">
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong>From Date: <? echo change_date_format(str_replace("'", "", $txt_date_from)); ?> To Date: <? echo change_date_format(str_replace("'", "", $txt_date_to)); ?></strong></td>
                </tr>
            </table>
            <table style="float: left; margin-bottom: 10px;" width="250" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th colspan="2"><b>Summary</b></th>
                </thead>
                <?
                //summary issue qnty
                $inside_outside_array_summary = array();
                $caption_summary = '';
                $summary_issueQnty = 0;
                $grand_total = 0;

                foreach ($knit_dye_source_wise_issue_qnty_arr as $key => $values) {
                ?>
                    <tr>
                        <td><b><? echo  $knitting_source[$key]; ?></b></td>
                        <td align="right"><b><? echo  number_format($values, 2); ?></b></td>
                    </tr>
                <?
                    $grand_total += $values;
                }

                foreach ($issue_purpose_wise_issue_qnty_arr as $key => $values) {
                ?>
                    <tr>
                        <td><b><? echo  $yarn_issue_purpose[$key]; ?></b></td>
                        <td align="right"><b><? echo  number_format($values, 2); ?></b></td>
                    </tr>
                <?
                    $grand_total += $values;
                }
                //end summary issue qnty
                $g_total = 0;
                $sample = 0;
                ?>
                <tr style="background-color: #f9f9f9;">
                    <td><b>Total</b></td>
                    <td align="right"><b><? echo  number_format($grand_total, 2); ?></b></td>
                </tr>
                <?
                foreach ($sample_without_order_issue_qnty_arr as $key => $values) {
                ?>
                    <tr>
                        <td><b><? echo  $yarn_issue_purpose[$key]; ?></b></td>
                        <td align="right"><b><? echo  number_format($values, 2);
                                                $sample += $values; ?></b></td>
                    </tr>
                <?
                }
                ?>
                <tr>
                    <td><b>Grand Total</b></td>
                    <td align="right"><b><?php echo number_format($grand_total + $sample, 2); ?></b></td>
                </tr>
            </table>

            <br>
            <!--  <table style="float: left; margin-bottom: 10px;" width="250" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th colspan="2"><b>Summary</b></th>
                </thead>
                <?
                //summary issue qnty
                $inside_outside_array_summary = array();
                $kk = 1;
                $caption_summary = '';
                $summary_issueQnty = 0;
                $grand_total = 0;
                foreach ($result_summery as $rows) {
                    if ($rows[csf('knit_dye_source')] == 1) {
                        $caption_summary = 'Inside';
                    } else if ($rows[csf('knit_dye_source')] == 3) {
                        $caption_summary = 'Outside';
                    } else {

                        $caption_summary = '';
                    }

                    if (in_array($rows[csf('knit_dye_source')], $inside_outside_array_summary)) {
                        $print_caption = 0;
                    } else {
                        $print_caption = 1;
                        $inside_outside_array_summary[$kk] = $rows[csf('knit_dye_source')];
                    }

                    if ($print_caption == 1) {
                        $summary_issueQnty = $rows[csf('issue_qnty')];
                    }
                    if ($print_caption == 1 && $rows[csf('knit_dye_source')] != 0) {
                ?>
                        <tr>
                            <td><b><?php echo $caption_summary; ?></b></td>

                            <td align="right"><b><? echo number_format($summary_issueQnty, 2); ?></b></td>

                        </tr>
                        <?
                        $grand_total += $summary_issueQnty;
                    }

                    $kk++;
                }

                foreach ($issue_purpose_wise_issue_qnty_arr as $key => $values) {
                        ?>
                    <tr>
                        <td><b><? echo  $yarn_issue_purpose[$key]; ?></b></td>
                        <td align="right"><b><? echo  number_format($values, 2); ?></b></td>
                    </tr>
                    <?
                    $grand_total += $values;
                }
                //end summary issue qnty
                $g_total = 0;
                $sample = 0;
                    ?>
                <tr style="background-color: #f9f9f9;">
                    <td><b>Total</b></td>
                    <td align="right"><b><? echo  number_format($grand_total, 2); ?></b></td>
                </tr>
                <?
                foreach ($sample_without_order_issue_qnty_arr as $key => $values) {
                ?>
                        <tr>
                            <td><b><? echo  $yarn_issue_purpose[$key]; ?></b></td>
                            <td align="right"><b><? echo  number_format($values, 2);
                                                    $sample += $values; ?></b></td>
                        </tr>
                        <?
                    }
                        ?>
                <tr>
                    <td><b>Grand Total</b></td>
                    <td align="right"><b><?php echo number_format($grand_total + $sample, 2); ?></b></td>
                </tr>
            </table> -->

            <table width="<? echo $value_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">
                <thead>
                    <th width="30">SL</th>
                    <th width="90">Job No</th>
                    <th width="100">Buyer Name</th>
                    <th width="80">File No.</th>
                    <th width="80">Ref. No</th>
                    <th width="135">Style No</th>
                    <th width="150">Order No</th>
                    <th width="100">Issue Basis</th>
                    <th width="110">Issue No</th>
                    <th width="70">Issue Date</th>
                    <th width="80">Challan No</th>
                    <th width="60">Count</th>
                    <th width="70">Yarn Brand</th>
                    <th width="100">Composition</th>
                    <th width="80">Type</th>
                    <th width="90">Color</th>
                    <th width="70">Lot No</th>
                    <th width="90">Issue Qty</th>
                    <? echo $column; ?>
                    <th width="90">Returnable Qty.</th>
                    <th width="100">Return Qty.</th>
                    <th width="100">Net Issue Qty.</th>
                    <th width="60">Rate/Kg<br>(USD)</th>
                    <th width="100">Net Issue Amount</th>
                    <th width="100">Issue Purpose</th>
                    <th width="100">Using Item</th>
                    <th width="110">Booking</th>
                    <th width="100">Reqn. No</th>
                    <th width="100">Booking/ <br>Reqn. Qty</th>
                    <th width="130">Issue To</th>
                    <th width="130">Location</th>
                    <th width="100">Store</th>
                    <th>Remarks</th>
                </thead>
            </table>
            <div style="width:<? echo $value_width + 18; ?>px; overflow-y: scroll; max-height:380px; float: left;" id="scroll_body">
                <table width="<? echo $value_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">
                    <tbody>
                        <?
                        $i = 1;
                        $total_iss_qnty = 0;
                        $issue_qnty = 0;
                        $issue_amount = 0;
                        $return_qnty = 0;
                        $issue_amount_return = 0;
                        $issue_balance_qnty = 0;
                        $issue_amount_qnty = 0;
                        $caption = '';
                        $knitting_party = '';
                        $total_amount = 0;
                        $grand_total_amount = 0;

                        $issue_amount_grand = 0;
                        $issue_qnty_grand = 0;
                        $return_qnty_grand = 0;
                        $total_amount_grand = 0;
                        $issue_amount_return_grand = 0;
                        $issue_balance_qnty_grand = 0;
                        $issue_amount_qnty_grand = 0;

                        $inside_outside_array = array();
                        $issue_purpose_array = array();
                        //$reqi_wise_booking=0;
                        foreach ($result as $row) {
                            $exchangeRate = $usd_arr[date('d-m-Y', strtotime($row[csf('issue_date')]))];
                            if ($exchangeRate == "") {
                                foreach ($usd_arr as $rate_date => $rat) {
                                    if (strtotime($rate_date) <= strtotime($row[csf('issue_date')])) {
                                        $rate_date = date('d-m-Y', strtotime($rate_date));
                                        $exchangeRate = $rat;
                                        break;
                                    }
                                }
                            }

                            //----------------------------------------------------------------------
                            /*$exchangeRate=$usd_arr[date('d-m-Y',strtotime($row[csf('issue_date')]))];
                            if(is_null($exchangeRate)){
                                for($day=1;$day<366;$day++){
                                    $previousDate=date('d-m-Y', strtotime("-$day day",strtotime($row[csf('issue_date')])));
                                    $exchangeRate=$usd_arr[$previousDate];
                                    if(!is_null($exchangeRate)){break;}
                                }
                            }*/
                            //-----------------------------------------------------------------------

                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            if ($row[csf('knit_dye_source')] == 1) {
                                $knitting_party = $company_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $location_arr[$row[csf('location_id')]];
                                $caption = 'Inside';
                            } else if ($row[csf('knit_dye_source')] == 3) {
                                $knitting_party = $supplier_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $locat_arr[$row[csf('location_id')]];
                                $caption = 'Outside';
                            } else {
                                $knitting_party = "";
                                $knitting_location = '';
                                $caption = '';
                            }

                            if (in_array($row[csf('knit_dye_source')], $inside_outside_array)) {
                                $print_caption = 0;
                            } else {
                                $print_caption = 1;
                                $inside_outside_array[$i] = $row[csf('knit_dye_source')];
                            }

                            if ($print_caption == 1 && $i != 1) {
                        ?>
                                <tr class="tbl_bottom"> <!-- inhouse total -->
                                    <td colspan="17" align="right"><b>Total</b></td>
                                    <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_qnty, 2); ?></b></td>
                                    <?
                                    if ($zero_val == 0) {
                                    ?><td align="right" style="word-break: break-all;"></td>
                                        <td align="right"><b><?php echo number_format($total_amount, 2); ?></b></td><?
                                                                                                                }
                                                                                                                    ?>
                                    <td align="right" style="word-break: break-all;"><b><?php echo number_format($return_qnty, 2); ?></b></td>
                                    <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_amount_return, 2);
                                                                                        $total_issue_return = 0; ?></b></td>

                                    <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_balance_qnty, 2); //$total_issue_return=0; 
                                                                                        ?></b></td>
                                    <td>&nbsp;</td>
                                    <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_amount_qnty, 2); //$total_issue_return=0; 
                                                                                        ?></b></td>
                                    <td colspan="10"></td>
                                </tr>
                            <?
                                $issue_amount_grand += $issue_amount;
                                $issue_qnty_grand += $issue_qnty;
                                $return_qnty_grand += $return_qnty;
                                $total_amount_grand += $total_amount;
                                $issue_amount_return_grand += $issue_amount_return;
                                $issue_balance_qnty_grand += $issue_balance_qnty;
                                $issue_amount_qnty_grand += $issue_amount_qnty;
                                $issue_amount = 0;
                                $issue_qnty = 0;
                                $total_amount = 0;
                                $return_qnty = 0;
                                $issue_amount_return = 0;
                                $issue_balance_qnty = 0;
                                $issue_amount_qnty = 0;
                            }

                            if ($print_caption == 1) {
                            ?>
                                <tr>
                                    <td colspan="<? echo $span; ?>" style="font-size:14px;" bgcolor="#CCCCCC"><b><?php echo $caption; ?></b></td>
                                </tr>
                            <?
                            }

                            // =====start oooooo
                            ?>

                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">

                                <?
                                $buyer = '';
                                $job_no = '';
                                $styRef = '';
                                $booking_reqsn_qty = 0;
                                $knit_id = '';
                                $order_no = '';
                                $order_ref = '';
                                $order_file = '';

                                if ($row[csf('issue_basis')] == 1) {
                                    //$booking_req_no = $row[csf('booking_no')];
                                    $reqi_wise_booking = $row[csf('booking_no')];
                                    if ($row[csf('issue_purpose')] == 1 || $row[csf('issue_purpose')] == 4) {
                                        $job_no = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['job_no'];
                                        $buyer = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['buyer_name'];
                                        $styRef = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['style_ref_no'];

                                        $order_no = $job_array[$booking_req_no]['po_number'];
                                        $order_ref = $job_array[$booking_req_no]['ref'];
                                        $order_file = $job_array[$booking_req_no]['file'];
                                        $booking_reqsn_qty = $booking_arr[$row[csf('booking_no')]]['qnty'];
                                    } else if ($row[csf('issue_purpose')] == 2) {
                                        $job_no = $job_array[$yarn_booking_array[$row[csf('booking_no')]]['job']]['job_no'];
                                        //$buyer = $job_array[$yarn_booking_array[$row[csf('booking_no')]]['job']]['buyer_name'];
                                        $buyer = $row[csf('buyer_id')];
                                        $styRef = $job_array[$yarn_booking_array[$row[csf('booking_no')]]['job']]['style_ref_no'];
                                        //$order_no = $job_array[$booking_req_no]['po_number'];
                                        $order = chop($job_array[$yarn_booking_array[$row[csf('booking_no')]]['job']]['po_number'], ',');
                                        //   $poIds=chop($order_n,',');
                                        $order_no = array_unique(explode(",", $order));
                                        $order_ref = $job_array[$booking_req_no]['ref'];
                                        $order_file = $job_array[$booking_req_no]['file'];
                                    } else {
                                        $job_no = '';
                                        //$buyer = '';
                                        $buyer = $booking_without_array[$row[csf('booking_no')]]['buyer_id'];
                                        $styRef = '';
                                        $order_no = '';
                                        $order_ref = '';
                                        $order_file = '';
                                        $booking_reqsn_qty = '';
                                    }
                                } else if ($row[csf('issue_basis')] == 3) {
                                    $booking_req_no = $row[csf('requisition_no')];

                                    //$reqibooking_arr[$row[csf('requisition_no')]] = $row[csf('booking_no')];
                                    $reqi_wise_booking = $reqibooking_arr[$row[csf('requisition_no')]]['booking_no'];
                                    //echo $row[csf('requisition_no')].'='.$reqi_wise_booking.'<br>';

                                    $knit_id = $requisition_arr[$row[csf('requisition_no')]]['knit_id'];
                                    $job_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['job_no'];
                                    $buyer = $booking_arr[$planning_arr[$knit_id]]['buyer_id'];
                                    //$buyer = $reqibooking_arr[$row[csf('requisition_no')]]['buyer_id'];
                                    $styRef = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['style_ref_no'];

                                    //$order_no = $job_array[$booking_req_no]['po_number'];
                                    $order_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['booking_no']]['po_number'];

                                    //$order_no=$job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['po_number'];
                                    $order_ref = $job_array[$reqi_wise_booking]['ref'];
                                    $order_file = $job_array[$booking_arr[$planning_arr[$knit_id]]['booking_no']]['file'];

                                    $booking_reqsn_qty = $requisition_arr[$row[csf('requisition_no')]]['qnty'];
                                } else if ($row[csf('issue_basis')] == 8) {
                                    $job_no = $all_sales_info_arr[$row[csf('trans_id')]][$row[csf('product_id')]]['po_job_no'];
                                    $order_no = $all_sales_info_arr[$row[csf('trans_id')]][$row[csf('product_id')]]['job_no'];
                                    $styRef = $all_sales_info_arr[$row[csf('trans_id')]][$row[csf('product_id')]]['style_ref_no'];
                                    $booking_req_no = $demand_dtls_arr[$row['ISSUE_NUMBER']]['REQUISITION_NO'];
                                    $booking_reqsn_qty = $demand_dtls_arr[$row['ISSUE_NUMBER']]['REQ_QNTY'];
                                    $reqi_wise_booking = $demand_dtls_arr[$row['ISSUE_NUMBER']]['BOOKING_NO'];
                                } else {
                                    $booking_req_no = "";
                                    $buyer = $row[csf('buyer_id')];
                                    $job_no = '';
                                    $styRef = '';
                                    $booking_reqsn_qty = 0;
                                    $order_no = '';
                                    $order_ref = '';
                                    $order_file = '';
                                }

                                ?>

                                <td width="30" class="wrd_brk"><? echo $i; ?></td>
                                <td width="90" align="center" class="wrd_brk">
                                    <p><? echo $job_no; ?></p>
                                </td>
                                <td width="100" title="<? echo $buyer; ?>" class="wrd_brk">
                                    <p><? echo $buyer_arr[$buyer]; ?></p>
                                </td>
                                <td width="80" class="wrd_brk">
                                    <p><?
                                        $file_no = implode(",", $order_file);
                                        echo $file_no;
                                        ?></p>
                                </td>
                                <td width="80" class="wrd_brk">
                                    <p><?
                                        $ref_no = implode(",", $order_ref);
                                        echo $ref_no;
                                        ?></p>
                                </td>
                                <td width="135" class="wrd_brk">
                                    <p><? echo $styRef; ?> &nbsp;</p>
                                </td>
                                <td width="150" class="wrd_brk">
                                    <p><?
                                        if ($row[csf('issue_basis')] == 8) {
                                            echo $order_no;
                                        } else {
                                            $order_n = implode(", ", $order_no);
                                            echo $order_n;
                                        }

                                        ?></p>
                                </td>
                                <td width="100" class="wrd_brk">
                                    <p><? echo $issue_basis[$row[csf('issue_basis')]]; ?></p>
                                </td>
                                <td width="110" class="wrd_brk">
                                    <p><? echo $row[csf('issue_number')]; ?></p>
                                </td>
                                <td width="70" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                                <td width="80" class="wrd_brk" class="wrd_brk">
                                    <p><? echo $row[csf('challan_no')]; ?></p>
                                </td>
                                <td width="60" class="wrd_brk" align="center">
                                    <p><? $yarn_count = $count_arr[$row[csf('yarn_count_id')]];
                                        echo $yarn_count; ?> &nbsp;</p>
                                </td>
                                <td width="70" class="wrd_brk">
                                    <p><? echo $brand_arr[$row[csf('brand')]]; ?></p>
                                </td>
                                <td width="100" class="wrd_brk">
                                    <p><? echo $composition[$row[csf('yarn_comp_type1st')]] . $row[csf('yarn_comp_percent1st')] . '%'; ?></p>
                                </td>
                                <td width="80" class="wrd_brk">
                                    <p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p>
                                </td>
                                <td width="90" class="wrd_brk">
                                    <p><? echo $color_arr[$row[csf('color')]]; ?></p>
                                </td>
                                <td width="70" class="wrd_brk">
                                    <p><? echo $row[csf('lot')]; ?></p>
                                </td>
                                <td width="90" align="right" class="wrd_brk">
                                    <?
                                    echo number_format($row[csf('issue_qnty')], 2);
                                    $total_iss_qnty += $row[csf('issue_qnty')];
                                    $issue_qnty += $row[csf('issue_qnty')];
                                    ?>
                                </td>
                                <?
                                if ($zero_val == 0) {
                                ?>
                                    <td width="90" align="right" class="wrd_brk">
                                        <?
                                        $avgg = $row[csf('avg_rate_per_unit')];
                                        $avg_rate = $avgg / 78;
                                        echo number_format($avg_rate, 4);
                                        ?>
                                    </td>
                                    <td width="110" align="right" class="wrd_brk">
                                        <?
                                        $amount = $row[csf('issue_qnty')] * $avg_rate;
                                        echo number_format($amount, 2);
                                        $total_amount += $amount;
                                        $grand_total_amount += $amount;
                                        ?>
                                    </td>
                                <?
                                }
                                ?>
                                <td width="90" class="wrd_brk" align="right">
                                    <?
                                    echo number_format($row[csf('return_qnty')], 2);
                                    $total_ret_qnty += $row[csf('return_qnty')];
                                    $return_qnty += $row[csf('return_qnty')];
                                    ?>
                                </td> <!-- report one================================ -->
                                <td width="100" class="wrd_brk" align="right">
                                    <p>
                                        <?
                                        echo number_format($issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]], 2);
                                        $total_issue_return += $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]];
                                        $grand_issue_return += $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]];

                                        $total_iss_amount_return += $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]];
                                        $issue_amount_return += $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]];
                                        ?>
                                    </p>
                                </td>
                                <td width="100" class="wrd_brk" align="right">
                                    <p><? echo number_format($row[csf('issue_qnty')] - $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]], 2);
                                        $issue_balance_qnty += $row[csf('issue_qnty')] - $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]];
                                        //$total_issue_balance_qnty +=  $issue_balance_qnty;
                                        ?></p>
                                </td>
                                <td width="60" class="wrd_brk" align="right"><? echo number_format($row[csf('cons_rate')] / $exchangeRate, 4); ?></td>

                                <td width="100" class="wrd_brk" align="right">
                                    <p><? echo number_format(($row[csf('cons_rate')] / $exchangeRate) * ($row[csf('issue_qnty')] - $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]]), 2);
                                        $issue_amount_qnty += ($row[csf('cons_rate')] / $exchangeRate) * ($row[csf('issue_qnty')] - $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]]);
                                        //$total_issue_balance_qnty +=  $issue_balance_qnty;
                                        ?></p>
                                </td>
                                <td width="100" class="wrd_brk">
                                    <p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p>
                                </td>
                                <td width="100" class="wrd_brk">
                                    <p><? echo $using_item_arr[$row[csf("using_item")]]; ?></p>
                                </td>
                                <td width="100" class="wrd_brk" align="center">
                                    <?

                                    echo $reqi_wise_booking;
                                    unset($reqi_wise_booking);

                                    ?></td>
                                <td width="110" class="wrd_brk" align="center" title="<? echo $row[csf('requisition_no')]; ?>"><? echo $booking_req_no; ?> </td>

                                <td width="100" class="wrd_brk" align="right"><? echo number_format($booking_reqsn_qty, 2); ?></td>
                                <td width="130" class="wrd_brk">
                                    <p><? echo $knitting_party; ?></p>
                                </td>
                                <td width="130" class="wrd_brk">
                                    <p><? echo $knitting_location; ?></p>
                                </td>
                                <td width="100" class="wrd_brk">
                                    <p><? echo $store_arr[$row[csf('store_id')]]; ?>
                                </td>
                                <td class="wrd_brk">
                                    <p><? echo $row[csf('remarks')]; ?>
                                </td>
                            </tr>
                        <?
                            $i++;
                        }
                        //===============end

                        if (count($result) > 0) {
                        ?>
                            <tr class="tbl_bottom">
                                <td colspan="17" align="right"><b>Total</b></td>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_qnty, 2); ?></b></td>

                                <?
                                if ($zero_val == 0) {
                                ?>
                                    <td align="right" style="word-break: break-all;"></td>
                                    <td align="right"><b><?php echo number_format($total_amount, 2); ?></b></td>
                                <?
                                }
                                ?>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($return_qnty, 2); //$return_qnty=0; 
                                                                                    ?></b></td>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_amount_return, 2);
                                                                                    $total_issue_return = 0; ?></b></td>

                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_balance_qnty, 2);
                                                                                    //$total_issue_return=0;

                                                                                    ?></b></td>
                                <td>&nbsp;</td>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_amount_qnty, 2);
                                                                                    //$total_issue_return=0;

                                                                                    ?></b></td>

                                <td colspan="109"></td>
                            </tr>
                        <?

                            $issue_qnty_grand += $issue_qnty;
                            $issue_amount_grand += $issue_amount;
                            $return_qnty_grand += $return_qnty;
                            $total_amount_grand += $total_amount;
                            $issue_amount_return_grand += $issue_amount_return;
                            $issue_balance_qnty_grand += $issue_balance_qnty;
                            $issue_amount_qnty_grand += $issue_amount_qnty;
                        }
                        ?>
                        <tr style="background-color: grey;">
                            <td colspan="17" align="right"><b>Inside + Outside Grand Total</b></td>
                            <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_qnty_grand, 2); ?></b></td>

                            <?
                            if ($zero_val == 0) {
                            ?>
                                <td align="right" style="word-break: break-all;"></td>
                                <td align="right"><b><?php echo number_format($total_amount_grand, 2); ?></b></td>
                            <?
                            }
                            ?>
                            <td align="right" style="word-break: break-all;"><b><?php echo number_format($return_qnty_grand, 2); ?></b></td>
                            <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_amount_return_grand, 2);
                                                                                $total_issue_return = 0; ?></b></td>
                            <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_balance_qnty_grand, 2); //$total_issue_return=0; 
                                                                                ?></b></td>
                            <td></td>
                            <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_amount_qnty_grand, 2); //$total_issue_return=0; 
                                                                                ?></b></td>

                            <td colspan="10"></td>
                        </tr>

                        <?
                        $k = 1;
                        $issue_amount = 0;
                        $issue_qnty = 0;
                        $knitting_party = '';
                        $total_amount = 0;
                        $return_qnty = 0;
                        //for library dtls
                        $lib_arr = array();
                        foreach ($nameArray as $row) {
                            $lib_arr['brand'][$row[csf('brand')]] = $row[csf('brand')];
                            $lib_arr['yarn_count'][$row[csf('yarn_count_id')]] = $row[csf('yarn_count_id')];
                            $lib_arr['color'][$row[csf('color')]] = $row[csf('color')];

                            $yt_issue_numbers .= "'" . $row[csf("issue_number")] . "',";
                        }

                        $yt_issue_numbers = chop($yt_issue_numbers, ",");

                        $yt_issue_numbers = implode(",", array_filter(array_unique(explode(",", $yt_issue_numbers))));

                        if ($yt_issue_numbers != "") {
                            $yt_issue_numbers = explode(",", $yt_issue_numbers);
                            $issue_numbers_chnk = array_chunk($yt_issue_numbers, 999);
                            $yt_issue_no_cond = " and";
                            foreach ($issue_numbers_chnk as $dtls_id) {
                                if ($yt_issue_no_cond == " and")  $yt_issue_no_cond .= "(c.issue_number in(" . implode(',', $dtls_id) . ")";
                                else $yt_issue_no_cond .= " or c.issue_number in(" . implode(',', $dtls_id) . ")";
                            }
                            $yt_issue_no_cond .= ")";
                            //echo $issue_no_cond;die;

                            //   echo "SELECT a.recv_number,a.booking_no, b.cons_quantity, c.issue_number,b.prod_id, b.id as trans_id from inv_receive_master a, inv_transaction b, inv_issue_master c where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 $yt_issue_no_cond";

                            $yt_issue_return_res = sql_select("SELECT a.recv_number,a.booking_no, b.cons_quantity, c.issue_number,b.prod_id, b.id as trans_id from inv_receive_master a, inv_transaction b, inv_issue_master c where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 $yt_issue_no_cond");
                        }

                        $yt_transIdChk = array();
                        foreach ($yt_issue_return_res as $val) {
                            if ($yt_transIdChk[$val[csf("trans_id")]] == "") {
                                $yt_transIdChk[$val[csf("trans_id")]] = $val[csf("trans_id")];
                                $yt_issue_return_qnty_arr[$val[csf("issue_number")]][$val[csf("prod_id")]] += $val[csf("cons_quantity")];
                            }
                        }
                        //var_dump($yt_issue_return_qnty_arr);

                        $color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0 and id in(" . implode(',', $lib_arr['color']) . ")", "id", "color_name");
                        $count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 and id in(" . implode(',', $lib_arr['yarn_count']) . ")", 'id', 'yarn_count');
                        $brand_arr = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0 and id in(" . implode(',', $lib_arr['brand']) . ")", 'id', 'brand_name');
                        //end for library dtls

                        $grnd_purpose_issue_qnty = 0;
                        $grnd_purpose_issue_amount = 0;
                        $grnd_purpose_total_amount = 0;
                        $grnd_purpose_return_qnty = 0;
                        $grnd_purpose_issue_return_qnty = 0;
                        $grnd_purpose_issue_balance_qnty = 0;
                        $grnd_purpose_issue_amount_qnty = 0;

                        foreach ($nameArray as $row) {
                            $exchangeRate = $usd_arr[date('d-m-Y', strtotime($row[csf('issue_date')]))];
                            if ($exchangeRate == "") {
                                foreach ($usd_arr as $rate_date => $rat) {
                                    if (strtotime($rate_date) <= strtotime($row[csf('issue_date')])) {
                                        $rate_date = date('d-m-Y', strtotime($rate_date));
                                        $exchangeRate = $rat;
                                        break;
                                    }
                                }
                            }

                            //----------------------------------------------------------------------
                            /*$exchangeRate=$usd_arr[date('d-m-Y',strtotime($row[csf('issue_date')]))];
                            if(is_null($exchangeRate)){
                                for($day=1;$day<366;$day++){
                                    $previousDate=date('d-m-Y', strtotime("-$day day",strtotime($row[csf('issue_date')])));
                                    $exchangeRate=$usd_arr[$previousDate];
                                    if(!is_null($exchangeRate)){break;}
                                }
                            }*/
                            //-----------------------------------------------------------------------

                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            if ($row[csf('knit_dye_source')] == 1) {
                                $knitting_party = $company_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $location_arr[$row[csf('location_id')]];
                            } else if ($row[csf('knit_dye_source')] == 3) {
                                $knitting_party = $supplier_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $locat_arr[$row[csf('location_id')]];
                            } else {
                                $knitting_party = "";
                                $knitting_location = '';
                            }
                            if (in_array($row[csf('issue_purpose')], $issue_purpose_array)) {
                                $print_caption = 0;
                            } else {
                                $print_caption = 1;
                                $issue_purpose_array[$i] = $row[csf('issue_purpose')];
                            }


                            if ($print_caption == 1 && $k != 1) {
                        ?>
                                <tr class="tbl_bottom">
                                    <td colspan="17" align="right"><b>Total</b></td>
                                    <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_qnty, 2); ?></b></td>

                                    <?
                                    if ($zero_val == 0) {
                                    ?>
                                        <td align="right" style="word-break: break-all;"></td>
                                        <td align="right"><b><?php echo number_format($total_amount, 2); ?></b></td>
                                    <?
                                    }
                                    ?>
                                    <td align="right" style="word-break: break-all;"><b><?php echo number_format($return_qnty, 2); ?></b></td>
                                    <td align="right" style="word-break: break-all;">
                                        <p>
                                            <?
                                            echo number_format($yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]], 2);
                                            $returnQntypurpose = $yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                                            ?>
                                        </p>
                                    </td>
                                    <td>&nbsp;</td>
                                    <td align="right" style="word-break: break-all;">
                                        &nbsp;
                                        <p>
                                            <?
                                            //echo number_format($issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]],2); $returnQntypurpose=$issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                                            ?>
                                        </p>
                                    </td>
                                    <td>&nbsp;</td>
                                    <td colspan="10"></td>
                                </tr>
                            <?
                                $issue_qnty = 0;
                                $issue_amount = 0;
                                $total_amount = 0;
                                $return_qnty = 0;
                                $total_issue_return = 0;
                                $total_issue_balance_qnty = 0;
                            }
                            if ($print_caption == 1) {
                            ?>
                                <tr>
                                    <td colspan="<? echo $span; ?>" style="font-size:14px;" bgcolor="#CCCCCC"><b><?php echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></b></td>
                                </tr>
                            <?
                            }
                            ?>

                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">

                                <?
                                $buyer = '';
                                $job_no = '';
                                $styRef = '';
                                $booking_reqsn_qty = 0;
                                $knit_id = '';
                                $order_no = '';
                                $order_ref = '';
                                $order_file = '';
                                if ($row[csf('issue_basis')] == 1) {
                                    $booking_req_no = $row[csf('booking_no')];

                                    if ($row[csf('issue_purpose')] == 8) {
                                        $buyer = $booking_without_array[$row[csf('booking_no')]]['buyer_id'];
                                        $booking_reqsn_qty = $booking_without_array[$row[csf('booking_no')]]['qnty'];
                                        $job_no = '';
                                        $styRef = '';
                                        $order_no = "";
                                        $order_ref = "";
                                        $order_file = "";
                                    } else {
                                        $job_no = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['job_no'];
                                        $buyer = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['buyer_name'];
                                        $styRef = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['style_ref_no'];
                                        $order_no = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['po_number'];
                                        $order_ref = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['ref'];
                                        $order_file = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['file'];
                                        $booking_reqsn_qty = $booking_arr[$row[csf('booking_no')]]['qnty'];
                                    }
                                } else if ($row[csf('issue_basis')] == 3) {
                                    $booking_req_no = $row[csf('requisition_no')];
                                    $reqi_wise_booking = $reqibooking_arr[$row[csf('requisition_no')]]['booking_no'];
                                    $knit_id = $requisition_arr[$row[csf('requisition_no')]]['knit_id'];
                                    $job_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['job_no'];
                                    $buyer = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['buyer_name'];
                                    $styRef = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['style_ref_no'];
                                    $order_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['po_number'];
                                    $order_ref = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['ref'];
                                    $order_file = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['file'];
                                    $booking_reqsn_qty = $requisition_arr[$row[csf('requisition_no')]]['qnty'];
                                } else {
                                    $booking_req_no = "";
                                    $buyer = $row[csf('buyer_id')];
                                    $job_no = '';
                                    $styRef = '';
                                    $order_no = '';
                                    $order_ref = '';
                                    $order_file = '';
                                    $booking_reqsn_qty = 0;
                                }

                                if ($row[csf('issue_purpose')] == 5) {
                                    $knitting_party = $other_party_arr[$row[csf('other_party')]];
                                } else if ($row[csf('issue_purpose')] == 3) {
                                    $knitting_party = $buyer_arr[$row[csf('buyer_id')]];
                                    $buyer = '';
                                }
                                ?>

                                <td width="30"><? echo $i; ?></td>
                                <td width="90" align="center" style="word-break: break-all;">
                                    <p><? echo $job_no; ?></p>
                                </td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $buyer_arr[$buyer]; ?></p>
                                </td>
                                <td width="80" style="word-break: break-all;">
                                    <p><?
                                        echo $file_no = implode(",", $order_file);
                                        echo $file_no;
                                        ?></p>
                                </td>
                                <td width="80" style="word-break: break-all;">
                                    <p><?
                                        echo $ref_no = implode(",", $order_ref);
                                        echo $ref_no;
                                        ?></p>
                                </td>
                                <td width="135" style="word-break: break-all;">
                                    <p><? echo $styRef; ?></p>
                                </td>
                                <td width="150" style="word-break: break-all;">
                                    <p><?
                                        $order_n = implode(",", $order_no);
                                        echo $order_n;
                                        ?></p>
                                </td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $issue_basis[$row[csf('issue_basis')]]; ?></p>
                                </td>
                                <td width="110" style="word-break: break-all;">
                                    <p><? echo $row[csf('issue_number')]; ?></p>
                                </td>
                                <td width="70" style="word-break: break-all;" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                                <td width="80" style="word-break: break-all;">
                                    <p><? echo $row[csf('challan_no')]; ?></p>
                                </td>
                                <td width="60" style="word-break: break-all;" align="center">
                                    <p><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p>
                                </td>
                                <td width="70" style="word-break: break-all;">
                                    <p><? echo $brand_arr[$row[csf('brand')]]; ?></p>
                                </td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $composition[$row[csf('yarn_comp_type1st')]] . $row[csf('yarn_comp_percent1st')] . '%'; ?></p>
                                </td>
                                <td width="80" style="word-break: break-all;">
                                    <p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p>
                                </td>
                                <td width="90" style="word-break: break-all;">
                                    <p><? echo $color_arr[$row[csf('color')]]; ?></p>
                                </td>
                                <td width="70" style="word-break: break-all;">
                                    <p><? echo $row[csf('lot')]; ?></p>
                                </td>
                                <td width="90" style="word-break: break-all;" align="right">
                                    <?
                                    echo number_format($row[csf('issue_qnty')], 2);
                                    $total_iss_qnty += $row[csf('issue_qnty')];
                                    $issue_qnty += $row[csf('issue_qnty')];
                                    ?>
                                </td>
                                <?
                                if ($zero_val == 0) {
                                ?>
                                    <td width="90" align="right" style="word-break: break-all;">
                                        <?
                                        $avgg = $row[csf('avg_rate_per_unit')];
                                        $avg_rate = $avgg / 78;
                                        echo number_format($avg_rate, 4);
                                        ?>
                                    </td>
                                    <td width="110" align="right" style="word-break: break-all;">
                                        <?
                                        $amount = $row[csf('issue_qnty')] * $avg_rate;
                                        echo number_format($amount, 2);
                                        $total_amount += $amount;
                                        $grand_total_amount += $amount;

                                        ?>
                                    </td>
                                <?
                                }
                                ?>
                                <td width="90" align="right" style="word-break: break-all;">
                                    <?
                                    echo number_format($row[csf('return_qnty')], 2);
                                    $total_ret_qnty += $row[csf('return_qnty')];
                                    $return_qnty += $row[csf('return_qnty')];
                                    ?>
                                </td>

                                <td width="100" style="word-break: break-all;" align="right">
                                    <p title="<? echo 'issue_number : ' . $row[csf("issue_number")] . '**product_id :' . $row[csf("product_id")] ?>">
                                        <?
                                        echo number_format($yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]], 2);
                                        $total_issue_return += $yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                                        $grand_issue_return += $yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];

                                        ?>
                                    </p>
                                </td>

                                <td width="100" style="word-break: break-all;" align="right">
                                    <p>
                                        <?
                                        echo number_format($row[csf('issue_qnty')] - $yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]], 2);

                                        $total_issue_balance_qnty += $row[csf('issue_qnty')] - $yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                                        ?>
                                    </p>
                                </td>
                                <td width="60" align="right" style="word-break: break-all;"><? echo number_format($row[csf('cons_rate')] / $exchangeRate, 4); ?></td>
                                <td width="100" style="word-break: break-all;" align="right">
                                    <p>
                                        <?
                                        echo number_format(($row[csf('cons_rate')] / $exchangeRate) * ($row[csf('issue_qnty')] - $yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]]), 2);

                                        $total_issue_amount_qnty += ($row[csf('cons_rate')] / $exchangeRate) * ($row[csf('issue_qnty')] - $yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]]);
                                        ?>
                                    </p>
                                </td>

                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p>
                                </td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $using_item_arr[$row[csf("using_item")]]; ?></p>
                                </td>

                                <td width="110" style="word-break: break-all;" align="center"><? echo $booking_req_no; ?></td>
                                <td width="100" style="word-break: break-all;" align="right"><? echo $reqi_wise_booking;
                                                                                                unset($reqi_wise_booking); ?></td>
                                <td width="100" style="word-break: break-all;" align="right"><? echo number_format($booking_reqsn_qty, 2); ?></td>
                                <td width="130" style="word-break: break-all;">
                                    <p><? echo $knitting_party; ?></p>
                                </td>
                                <td width="130" style="word-break: break-all;">
                                    <p><? echo $knitting_location; ?></p>
                                </td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $store_arr[$row[csf('store_id')]]; ?>
                                </td>
                                <td style="word-break: break-all;">
                                    <p><? echo $row[csf('remarks')]; ?>
                                </td>
                            </tr>

                        <?
                            $k++;
                            $i++;
                            $grnd_purpose_issue_qnty += $row[csf('issue_qnty')];
                            $grnd_purpose_issue_amount += $row[csf('cons_amount')] / $exchangeRate;
                            $grnd_purpose_total_amount += $amount;
                            $grnd_purpose_return_qnty += $row[csf('return_qnty')];
                            $grnd_purpose_issue_return_qnty += $yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                            $grnd_purpose_issue_balance_qnty += $row[csf('issue_qnty')] - $yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                            $grnd_purpose_issue_amount_qnty += ($row[csf('cons_rate')] / $exchangeRate) * ($row[csf('issue_qnty')] - $yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]]);
                        }

                        if (count($nameArray) > 0) {
                        ?>
                            <tr class="tbl_bottom">
                                <td colspan="17" align="right"><b>Total</b></td>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_qnty, 2); ?></b></td>

                                <?
                                if ($zero_val == 0) {
                                ?>
                                    <td align="right" style="word-break: break-all;"></td>
                                    <td align="right"><b><?php echo number_format($total_amount, 2); ?></b></td>
                                <?
                                }
                                ?>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($return_qnty, 2);
                                                                                    $return_qnty = 0; ?></b></td>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($total_issue_return, 2);
                                                                                    $total_issue_return = 0; ?></b></td>

                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($total_issue_balance_qnty, 2);
                                                                                    $total_issue_balance_qnty = 0; ?></b></td>
                                <td>&nbsp;</td>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($total_issue_amount_qnty, 2);
                                                                                    $total_issue_amount_qnty = 0; ?></b></td>
                                <td colspan="10"></td>
                            </tr>
                        <?
                        }
                        ?>
                        <tr>
                            <th colspan="17" align="right">Issue Purpose Wise Grand Total</th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_qnty, 2); ?></th>

                            <?
                            if ($zero_val == 0) {
                            ?>
                                <th></th>
                                <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_total_amount, 2); ?></th>
                            <?
                            }
                            ?>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_return_qnty, 2); ?></th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_return_qnty, 2); ?></th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_balance_qnty, 2); ?></th>
                            <th></th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_amount_qnty, 2); ?></th>

                            <th colspan="9" align="right"></th>
                        </tr>
                    </tbody>

                    <tfoot style="background-color: grey;">
                        <tr>
                            <th colspan="17" align="right"> Grand Total</th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_qnty + $issue_qnty_grand, 2); ?></th>

                            <?
                            if ($zero_val == 0) {
                            ?>
                                <th></th>
                                <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_total_amount + $total_amount_grand, 2); ?></th>
                            <?
                            }
                            ?>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_return_qnty + $return_qnty_grand, 2); ?></th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_return_qnty + $issue_amount_return_grand, 2); ?></th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_balance_qnty + $issue_balance_qnty_grand, 2); ?></th>
                            <th></th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_amount_qnty + $issue_amount_qnty_grand, 2); ?></th>

                            <th colspan="10" align="right"></th>
                        </tr>
                    </tfoot>

                </table>
            </div>
        </fieldset>
    <?
    } else if ($type == 2) // Report 2
    {

        foreach ($result as $row) {
            $headArray[$row[csf("yarn_count_id")]] = $row[csf("yarn_count_id")];
            $ArrayCount[$row[csf("yarn_type")]][$row[csf("yarn_count_id")]] += $row[csf("issue_qnty")];
            $i++;
        }
    ?>
        <fieldset style="width:<? echo round(count($headArray) * 70 + 220) ?>px;">
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" width="100%" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" style="font-size:14px"><strong><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" style="font-size:14px"><strong>From Date: <? echo change_date_format(str_replace("'", "", $txt_date_from)); ?> To Date: <? echo change_date_format(str_replace("'", "", $txt_date_to)); ?></strong></td>
                </tr>
            </table>
            <div style="overflow-y: scroll; " id="scroll_body">
                <table border="1" cellpadding="0" cellspacing="1" class="rpt_table" rules="all" id="table_header_1">
                    <thead>
                        <tr>
                            <th width="35">SL</th>
                            <th width="70">Yarn Type</th>
                            <?
                            foreach ($headArray as $countId => $countId) {
                                echo "<th width=\"70\">" . $count_arr[$countId] . "</th>";
                            }
                            ?>
                            <th>Count Total</th>
                        </tr>
                    </thead>
                    <?
                    $i = 1;
                    foreach ($ArrayCount as $yarn_type_id => $countId_arr) {
                        $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                            <td> <? echo $i; ?></td>
                            <td> <? echo $yarn_type[$yarn_type_id]; ?></td>
                            <?
                            foreach ($headArray as $countId => $value) {
                                echo "<td align=\"right\">" . $ArrayCount[$yarn_type_id][$countId] . " </td>";
                                $yarn_type_tot[$yarn_type_id] += $ArrayCount[$yarn_type_id][$countId];
                                $count_tot[$countId] += $ArrayCount[$yarn_type_id][$countId]
                            ?>
                            <? } ?>
                            <? echo "<td align=\"right\">" . $yarn_type_tot[$yarn_type_id] . "</td>"; ?>
                        </tr>
                    <?
                        $i++;
                    }
                    ?>
                    <tr>
                        <td colspan="2" align="center">
                            <b>Grand Total </b>
                        </td>
                        <? foreach ($headArray as $countId => $value) { ?>
                            <?
                            echo "<td align=\"right\">" . $count_tot[$countId] . "</td>";
                            $GrandTotal += $count_tot[$countId];
                            ?>
                        <?
                        } ?>
                        <td colspan="2" align="right">
                            <b><? echo $GrandTotal; ?> </b>
                        </td>
                    </tr>
                    <tfoot>
                        <tr>
                            <th colspan="2"></th>
                            <? foreach ($headArray as $countId => $value) { ?>
                                <th align="right">
                                    <? echo number_format(($count_tot[$countId] / $GrandTotal) * 100, 2) . "%" ?>
                                </th>
                            <? } ?>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </fieldset>
    <?
    } else if ($type == 3) // order wise separated row // // Report-3
    {
    ?>
        <fieldset style="width:<? echo $value_width + 18; ?>px;">
            <table cellpadding="0" cellspacing="0" width="<? echo $value_width; ?>" style="float: left;">
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong>From Date: <? echo change_date_format(str_replace("'", "", $txt_date_from)); ?> To Date: <? echo change_date_format(str_replace("'", "", $txt_date_to)); ?></strong></td>
                </tr>
            </table>
            <table width="<? echo $value_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">
                <thead>
                    <th width="30">SL</th>
                    <th width="90">Job No</th>
                    <th width="100">Buyer Name</th>
                    <th width="80">File No.</th>
                    <th width="80">Ref. No</th>
                    <th width="135">Style No</th>
                    <th width="150">Order No</th>
                    <th width="100">Issue Basis</th>
                    <th width="110">Issue No</th>
                    <th width="70">Issue Date</th>
                    <th width="80">Challan No</th>
                    <th width="60">Count</th>
                    <th width="70">Yarn Brand</th>
                    <th width="100">Composition</th>
                    <th width="80">Type</th>
                    <th width="90">Color</th>
                    <th width="70">Lot No</th>
                    <th width="90">Issue Qty</th>
                    <th width="60">Rate/Kg</th>
                    <th width="70">Issue Amount</th>
                    <? echo $column; ?>
                    <th width="90">Returnable Qty.</th>
                    <th width="100">Return Qty.</th>
                    <th width="100">Issue Purpose</th>
                    <th width="100">Using Item</th>
                    <th width="110">Booking/ Reqn. No</th>
                    <th width="100">Booking/ Reqn. Qty</th>
                    <th width="130">Issue To</th>
                    <th width="130">Location</th>
                    <th>Store</th>
                </thead>
            </table>
            <div style="width:<? echo $value_width + 18; ?>px; overflow-y: scroll; max-height:380px; float: left;" id="scroll_body">
                <table width="<? echo $value_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">
                    <tbody>
                        <?
                        $i = 1;
                        $total_iss_qnty = 0;
                        $issue_qnty = 0;
                        $issue_amount = 0;
                        $return_qnty = 0;
                        $issue_amount_return = 0;
                        $caption = '';
                        $knitting_party = '';
                        $total_amount = 0;
                        $grand_total_amount = 0;

                        $issue_amount_grand = 0;
                        $issue_qnty_grand = 0;
                        $return_qnty_grand = 0;
                        $total_amount_grand = 0;
                        $issue_amount_return_grand = 0;
                        $inside_outside_array = array();
                        $issue_purpose_array = array();

                        foreach ($result as $row) {
                            $exchangeRate = $usd_arr[date('d-m-Y', strtotime($row[csf('issue_date')]))];
                            if ($exchangeRate == "") {
                                foreach ($usd_arr as $rate_date => $rat) {
                                    if (strtotime($rate_date) <= strtotime($row[csf('issue_date')])) {
                                        $rate_date = date('d-m-Y', strtotime($rate_date));
                                        $exchangeRate = $rat;
                                        break;
                                    }
                                }
                            }

                            //----------------------------------------------------------------------
                            /*$exchangeRate=$usd_arr[date('d-m-Y',strtotime($row[csf('issue_date')]))];
                            if(is_null($exchangeRate)){
                                for($day=1;$day<366;$day++){
                                    $previousDate=date('d-m-Y', strtotime("-$day day",strtotime($row[csf('issue_date')])));
                                    $exchangeRate=$usd_arr[$previousDate];
                                    if(!is_null($exchangeRate)){break;}
                                }
                            }*/
                            //-----------------------------------------------------------------------

                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            if ($row[csf('knit_dye_source')] == 1) {
                                $knitting_party = $company_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $location_arr[$row[csf('location_id')]];
                                $caption = 'Inside';
                            } else if ($row[csf('knit_dye_source')] == 3) {
                                $knitting_party = $supplier_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $locat_arr[$row[csf('location_id')]];
                                $caption = 'Outside';
                            } else {
                                $knitting_party = "";
                                $knitting_location = '';
                                $caption = '';
                            }

                            if (in_array($row[csf('knit_dye_source')], $inside_outside_array)) {
                                $print_caption = 0;
                            } else {
                                $print_caption = 1;
                                $inside_outside_array[$i] = $row[csf('knit_dye_source')];
                            }


                            if (!in_array($row[csf('knit_dye_source')] . "_" . $composition[$row[csf('yarn_comp_type1st')]] . $row[csf('yarn_comp_percent1st')] . '%', $checkComArr)) {
                                $checkComArr[$i] = $row[csf('knit_dye_source')] . "_" . $composition[$row[csf('yarn_comp_type1st')]] . $row[csf('yarn_comp_percent1st')] . '%';
                                if ($i > 1) {
                        ?>
                                    <tr class="tbl_bottom">
                                        <td colspan="17" align="right"><b>Composition Wise</b></td>
                                        <td align="right" style="word-break: break-all;"><b> <?php
                                                                                                echo number_format($com_source_arr[implode("____", array_unique(explode("____", chop($compo_source, "____"))))]["issue_qnty"], 2, ".", "");
                                                                                                ?></b></td>
                                        <td></td>
                                        <td align="right" style="word-break: break-all;"><b> <?php
                                                                                                echo number_format($com_source_arr[implode("____", array_unique(explode("____", chop($compo_source, "____"))))]["cons_amount"], 2, ".", "");
                                                                                                ?></b></td>
                                        <?
                                        if ($zero_val == 0) {
                                        ?>
                                            <td align="right" style="word-break: break-all;"></td>
                                            <td align="right"><b><?php
                                                                    echo number_format($com_source_arr[implode("____", array_unique(explode("____", chop($compo_source, "____"))))]["amount"], 2, ".", "");
                                                                    ?></b></td>
                                        <? }
                                        ?>
                                        <td align="right" style="word-break: break-all;"><b><?php
                                                                                            echo number_format($com_source_arr[implode("____", array_unique(explode("____", chop($compo_source, "____"))))]["returnable_qnty"], 2, ".", "");
                                                                                            ?></b></td>
                                        <td align="right" style="word-break: break-all;"><b><?php
                                                                                            echo number_format($com_source_arr[implode("____", array_unique(explode("____", chop($compo_source, "____"))))]["return_qnty"], 2, ".", "");
                                                                                            ?></b></td>
                                        <td colspan="7"></td>
                                    </tr>
                                <?
                                    //$com_source_arr[$row[csf('knit_dye_source')]."_". $composition[$row[csf('yarn_comp_type1st')]].$row[csf('yarn_comp_percent1st')].'%']["issue_qnty"] =0;
                                    $compo_source = "";
                                }
                            }
                            if ($print_caption == 1 && $i != 1) {
                                ?>
                                <tr class="tbl_bottom"> <!-- inhouse total -->
                                    <td colspan="17" align="right"><b>Total</b></td>
                                    <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_qnty, 2); ?></b></td>
                                    <td></td>
                                    <td style="word-break: break-all;"><b><?php echo number_format($issue_amount, 2); ?></b></td>
                                    <?
                                    if ($zero_val == 0) {
                                    ?>
                                        <td align="right" style="word-break: break-all;"></td>
                                        <td align="right"><b><?php echo number_format($total_amount, 2); ?></b></td>
                                    <?
                                    }
                                    ?>
                                    <td align="right" style="word-break: break-all;"><b><?php echo number_format($return_qnty, 2); ?></b></td>
                                    <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_amount_return, 2);
                                                                                        $total_issue_return = 0; ?></b></td>
                                    <td colspan="7"></td>
                                </tr>
                            <?
                                $issue_amount_grand += $issue_amount;
                                $issue_qnty_grand += $issue_qnty;
                                $return_qnty_grand += $return_qnty;
                                $total_amount_grand += $total_amount;
                                $issue_amount_return_grand += $issue_amount_return;
                                $issue_amount = 0;
                                $issue_qnty = 0;
                                $total_amount = 0;
                                $return_qnty = 0;
                                $issue_amount_return = 0;
                            }
                            if ($print_caption == 1) {
                            ?>
                                <tr>
                                    <td colspan="<? echo $span; ?>" style="font-size:14px;" bgcolor="#CCCCCC"><b><?php echo $caption; ?></b></td>
                                </tr>
                            <?
                            }
                            ?>



                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">
                                <td width="30"><? echo $i; ?></td>
                                <?
                                $buyer = '';
                                $job_no = '';
                                $styRef = '';
                                $booking_reqsn_qty = 0;
                                $knit_id = '';
                                $order_no = '';
                                $order_ref = '';
                                $order_file = '';
                                if ($row[csf('issue_basis')] == 1) {
                                    $booking_req_no = $row[csf('booking_no')];
                                    if ($row[csf('issue_purpose')] == 1 || $row[csf('issue_purpose')] == 4) {
                                        $job_no = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['job_no'];
                                        $buyer = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['buyer_name'];
                                        $styRef = $job_array_order_wise[$row[csf('po_breakdown_id')]]['style_ref_no'];

                                        $order_no = $job_array[$booking_req_no]['po_number'];
                                        $order_ref = $job_array_order_wise[$booking_req_no][$row[csf('po_breakdown_id')]]['ref'];
                                        $order_file = $job_array[$booking_req_no]['file'];
                                        $booking_reqsn_qty = $booking_arr[$row[csf('booking_no')]]['qnty'];
                                    } else if ($row[csf('issue_purpose')] == 2) {
                                        $job_no = $job_array[$yarn_booking_array[$row[csf('booking_no')]]['job']]['job_no'];
                                        $buyer = $job_array[$yarn_booking_array[$row[csf('booking_no')]]['job']]['buyer_name'];
                                        $styRef = $job_array_order_wise[$row[csf('po_breakdown_id')]]['style_ref_no'];
                                        $order_no = $job_array[$booking_req_no]['po_number'];
                                        $order_ref = $job_array_order_wise[$booking_req_no][$row[csf('po_breakdown_id')]]['ref'];
                                        $order_file = $job_array[$booking_req_no]['file'];
                                    } else {
                                        $job_no = '';
                                        $buyer = '';
                                        $styRef = '';
                                        $order_no = '';
                                        $order_ref = '';
                                        $order_file = '';
                                        $booking_reqsn_qty = '';
                                    }
                                } else if ($row[csf('issue_basis')] == 3) {
                                    $booking_req_no = $row[csf('requisition_no')];
                                    $reqi_wise_booking = $reqibooking_arr[$row[csf('requisition_no')]]['booking_no'];
                                    $knit_id = $requisition_arr[$row[csf('requisition_no')]]['knit_id'];
                                    $job_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['job_no'];
                                    $buyer = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['buyer_name'];
                                    $styRef = $job_array_order_wise[$row[csf('po_breakdown_id')]]['style_ref_no'];

                                    //$order_no = $job_array[$booking_req_no]['po_number'];
                                    $order_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['booking_no']]['po_number'];

                                    //$order_no=$job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['po_number'];
                                    //$order_ref = $job_array[$booking_req_no]['ref'];
                                    $order_ref = $job_array_order_wise[$booking_arr[$planning_arr[$knit_id]]['booking_no']][$row[csf('po_breakdown_id')]]['ref'];

                                    $order_file = $job_array[$booking_req_no]['file'];
                                    $booking_reqsn_qty = $requisition_arr[$row[csf('requisition_no')]]['qnty'];
                                } else {
                                    $booking_req_no = "";
                                    $buyer = $row[csf('buyer_id')];
                                    $job_no = '';
                                    $styRef = '';
                                    $booking_reqsn_qty = 0;
                                    $order_no = '';
                                    $order_ref = '';
                                    $order_file = '';
                                }
                                ?>
                                <td width="90" align="center" style="word-break: break-all;">
                                    <p><? echo $job_no; ?></p>
                                </td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $buyer_arr[$buyer]; ?></p>
                                </td>
                                <td width="80" style="word-break: break-all;">
                                    <p><?
                                        $file_no = implode(",", $order_file);
                                        echo $file_no; ?></p>
                                </td>
                                <td width="80" style="word-break: break-all;">
                                    <p><?
                                        $ref_no = implode(",", $order_ref);
                                        echo $ref_no;
                                        ?></p>
                                </td>
                                <td width="135" style="word-break: break-all;">
                                    <p><? echo $styRef; ?></p>
                                </td>
                                <td width="150" style="word-break: break-all;">
                                    <p><?
                                        $order_n = implode(",", $order_no);
                                        echo $order_n;
                                        //echo $po_array[$row[csf('po_breakdown_id')]]["po_number"]; 
                                        ?></p>
                                </td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $issue_basis[$row[csf('issue_basis')]]; ?></p>
                                </td>
                                <td width="110" style="word-break: break-all;">
                                    <p><? echo $row[csf('issue_number')]; ?></p>
                                </td>
                                <td width="70" style="word-break: break-all;" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                                <td width="80" style="word-break: break-all;">
                                    <p><? echo $row[csf('challan_no')]; ?></p>
                                </td>
                                <td width="60" style="word-break: break-all;" align="center">
                                    <p><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p>
                                </td>
                                <td width="70" style="word-break: break-all;">
                                    <p><? echo $brand_arr[$row[csf('brand')]]; ?></p>
                                </td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $composition[$row[csf('yarn_comp_type1st')]] . $row[csf('yarn_comp_percent1st')] . '%'; ?></p>
                                </td>
                                <td width="80" style="word-break: break-all;">
                                    <p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p>
                                </td>
                                <td width="90" style="word-break: break-all;">
                                    <p><? echo $color_arr[$row[csf('color')]]; ?></p>
                                </td>
                                <td width="70" style="word-break: break-all;">
                                    <p><? echo $row[csf('lot')]; ?></p>
                                </td>
                                <td width="90" style="word-break: break-all;" align="right">
                                    <?
                                    echo number_format($row[csf('issue_qnty')], 2);
                                    $total_iss_qnty += $row[csf('issue_qnty')];
                                    $issue_qnty += $row[csf('issue_qnty')];

                                    $com_source_arr[$row[csf('knit_dye_source')] . "_" . $composition[$row[csf('yarn_comp_type1st')]] . $row[csf('yarn_comp_percent1st')] . '%']["issue_qnty"] += $row[csf('issue_qnty')];
                                    //-----------k

                                    ?>
                                </td>
                                <td width="60" align="right" style="word-break: break-all;">
                                    <?
                                    echo number_format($row[csf('cons_rate')] / $exchangeRate, 2);
                                    ?>
                                </td>
                                <td width="70" align="right" style="word-break: break-all;">
                                    <?
                                    //$cons_amount=$row[csf('cons_amount')]/$exchangeRate;
                                    $cons_amount = $row[csf('issue_qnty')] * ($row[csf('cons_rate')] / $exchangeRate);
                                    echo number_format($cons_amount, 2);
                                    $total_iss_amount += $cons_amount;
                                    $issue_amount += $cons_amount;

                                    $com_source_arr[$row[csf('knit_dye_source')] . "_" . $composition[$row[csf('yarn_comp_type1st')]] . $row[csf('yarn_comp_percent1st')] . '%']["cons_amount"] += $cons_amount;
                                    //echo "string";
                                    ?>
                                </td>
                                <?
                                if ($zero_val == 0) {
                                ?>
                                    <td width="90" align="right" style="word-break: break-all;">
                                        <?
                                        //$avg_rate = $row[csf('cons_rate')];
                                        //$avg_rate = $avgg / 78;
                                        //echo number_format($avg_rate, 4);
                                        //---
                                        $avgg = $row[csf('avg_rate_per_unit')];
                                        $avg_rate = $avgg / 78;
                                        echo number_format($avg_rate, 4);
                                        ?>
                                    </td>
                                    <td width="110" align="right" style="word-break: break-all;">
                                        <?
                                        $amount = $row[csf('issue_qnty')] * $avg_rate;
                                        echo number_format($amount, 2);
                                        $total_amount += $amount;
                                        $grand_total_amount += $amount;
                                        $com_source_arr[$row[csf('knit_dye_source')] . "_" . $composition[$row[csf('yarn_comp_type1st')]] . $row[csf('yarn_comp_percent1st')] . '%']["amount"] += $amount;
                                        ?>
                                    </td>
                                <?
                                }
                                ?>
                                <td width="90" align="right" style="word-break: break-all;">
                                    <?
                                    echo number_format($row[csf('return_qnty')], 2);
                                    $total_ret_qnty += $row[csf('return_qnty')];
                                    $return_qnty += $row[csf('return_qnty')];
                                    $com_source_arr[$row[csf('knit_dye_source')] . "_" . $composition[$row[csf('yarn_comp_type1st')]] . $row[csf('yarn_comp_percent1st')] . '%']["returnable_qnty"] += $row[csf('return_qnty')];
                                    ?>
                                </td> <!-- report one================================ -->
                                <td width="100" style="word-break: break-all;" align="right">
                                    <p>
                                        <?
                                        echo number_format($issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]], 2);
                                        $total_issue_return += $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]];
                                        $grand_issue_return += $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]];

                                        $total_iss_amount_return += $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]];
                                        $issue_amount_return += $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]];
                                        $com_source_arr[$row[csf('knit_dye_source')] . "_" . $composition[$row[csf('yarn_comp_type1st')]] . $row[csf('yarn_comp_percent1st')] . '%']["return_qnty"] += $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("requisition_no")]][$row[csf("product_id")]];
                                        ?>
                                    </p>
                                </td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p>
                                </td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $using_item_arr[$row[csf("using_item")]]; ?></p>
                                </td>
                                <td width="110" style="word-break: break-all;" align="center"><? echo $booking_req_no . '<br>' . $reqi_wise_booking;
                                                                                                unset($reqi_wise_booking); ?></td>
                                <td width="100" style="word-break: break-all;" align="right"><? echo number_format($booking_reqsn_qty, 2); ?></td>
                                <td width="130" style="word-break: break-all;">
                                    <p><? echo $knitting_party; ?></p>
                                </td>
                                <td width="130" style="word-break: break-all;">
                                    <p><? echo $knitting_location; ?></p>
                                </td>
                                <td style="word-break: break-all;">
                                    <p><? echo $store_arr[$row[csf('store_id')]]; ?>
                                </td>
                            </tr>

                        <?
                            $compo_source .= $row[csf('knit_dye_source')] . "_" . $composition[$row[csf('yarn_comp_type1st')]] . $row[csf('yarn_comp_percent1st')] . '%' . "____";
                            $i++;
                        } ?>

                        <tr class="tbl_bottom">
                            <td colspan="17" align="right"><b>Composition Wise</b></td>
                            <td align="right" style="word-break: break-all;"><b> <?php echo number_format($com_source_arr[implode("____", array_unique(explode("____", chop($compo_source, "____"))))]["issue_qnty"], 2, ".", ""); ?></b></td>
                            <td></td>
                            <td style="word-break: break-all;"><b><?php echo number_format($com_source_arr[implode("____", array_unique(explode("____", chop($compo_source, "____"))))]["cons_amount"], 2, ".", ""); ?></b></td>
                            <?
                            if ($zero_val == 0) {
                            ?>
                                <td align="right" style="word-break: break-all;"></td>
                                <td align="right"><b><?php echo number_format($com_source_arr[implode("____", array_unique(explode("____", chop($compo_source, "____"))))]["amount"], 2, ".", ""); ?></b></td>
                            <? }
                            ?>
                            <td align="right" style="word-break: break-all;"><b><?php echo number_format($com_source_arr[implode("____", array_unique(explode("____", chop($compo_source, "____"))))]["returnable_qnty"], 2, ".", ""); ?></b></td>
                            <td align="right" style="word-break: break-all;"><b><?php echo number_format($com_source_arr[implode("____", array_unique(explode("____", chop($compo_source, "____"))))]["return_qnty"], 2, ".", ""); ?></b></td>
                            <td colspan="7"></td>
                        </tr>
                        <?
                        if (count($result) > 0) {
                        ?>
                            <tr class="tbl_bottom">
                                <td colspan="17" align="right"><b>Total</b></td>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_qnty, 2); ?></b></td>
                                <td></td>
                                <td style="word-break: break-all;"><b><?php echo number_format($issue_amount, 2); ?></b></td>
                                <?
                                if ($zero_val == 0) {
                                ?>
                                    <td align="right" style="word-break: break-all;"></td>
                                    <td align="right"><b><?php echo number_format($total_amount, 2); ?></b></td>
                                <?
                                }
                                ?>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($return_qnty, 2); //$return_qnty=0; 
                                                                                    ?></b></td>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_amount_return, 2);
                                                                                    $total_issue_return = 0; ?></b></td>
                                <td colspan="7"></td>
                            </tr>
                        <?

                            $issue_qnty_grand += $issue_qnty;
                            $issue_amount_grand += $issue_amount;
                            $return_qnty_grand += $return_qnty;
                            $total_amount_grand += $total_amount;
                            $issue_amount_return_grand += $issue_amount_return;
                        }
                        ?>
                        <tr style="background-color: grey;">
                            <td colspan="17" align="right"><b>Inside + Outside Grand Total</b></td>
                            <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_qnty_grand, 2); ?></b></td>
                            <td></td>
                            <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_amount_grand, 2); ?></b></td>
                            <?
                            if ($zero_val == 0) {
                            ?>
                                <td align="right" style="word-break: break-all;"></td>
                                <td align="right"><b><?php echo number_format($total_amount_grand, 2); ?></b></td>
                            <?
                            }
                            ?>
                            <td align="right" style="word-break: break-all;"><b><?php echo number_format($return_qnty_grand, 2); ?></b></td>
                            <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_amount_return_grand, 2);
                                                                                $total_issue_return = 0; ?></b></td>
                            <td colspan="7"></td>
                        </tr>
                        <?


                        $k = 1;
                        $issue_amount = 0;
                        $issue_qnty = 0;
                        $knitting_party = '';
                        $total_amount = 0;
                        $return_qnty = 0;

                        if ($product_ids != "") {
                            $query = "SELECT a.issue_number, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.buyer_id, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no, a.other_party, b.requisition_no, b.store_id, b.brand_id, b.cons_quantity as issue_qnty, b.return_qnty,b.cons_rate,b.cons_amount, c.brand,c.yarn_type, c.yarn_count_id, c.yarn_type, c.lot, c.color, c.avg_rate_per_unit,c.yarn_comp_type1st,c.yarn_comp_percent1st ,c.id as product_id
                           from inv_issue_master a, inv_transaction b, product_details_master c
                           where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.issue_purpose not in (1,2,4) and a.issue_date between '$from_date' and '$to_date' and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lot_no and c.id in($product_ids) $yarn_type_cond $yarn_count_cond $issue_purpose_cond $using_item_cond
                           order by a.issue_purpose, a.issue_date";
                        }


                        $nameArray = sql_select($query);

                        $grnd_purpose_issue_qnty = 0;
                        $grnd_purpose_issue_amount = 0;
                        $grnd_purpose_total_amount = 0;
                        $grnd_purpose_return_qnty = 0;
                        $grnd_purpose_issue_return_qnty = 0;

                        foreach ($nameArray as $row) {

                            $exchangeRate = $usd_arr[date('d-m-Y', strtotime($row[csf('issue_date')]))];
                            if ($exchangeRate == "") {
                                foreach ($usd_arr as $rate_date => $rat) {
                                    if (strtotime($rate_date) <= strtotime($row[csf('issue_date')])) {
                                        $rate_date = date('d-m-Y', strtotime($rate_date));
                                        $exchangeRate = $rat;
                                        break;
                                    }
                                }
                            }

                            //----------------------------------------------------------------------
                            /*$exchangeRate=$usd_arr[date('d-m-Y',strtotime($row[csf('issue_date')]))];
                            if(is_null($exchangeRate)){
                                for($day=1;$day<366;$day++){
                                    $previousDate=date('d-m-Y', strtotime("-$day day",strtotime($row[csf('issue_date')])));
                                    $exchangeRate=$usd_arr[$previousDate];
                                    if(!is_null($exchangeRate)){break;}
                                }
                            }*/
                            //-----------------------------------------------------------------------


                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            if ($row[csf('knit_dye_source')] == 1) {
                                $knitting_party = $company_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $location_arr[$row[csf('location_id')]];
                            } else if ($row[csf('knit_dye_source')] == 3) {
                                $knitting_party = $supplier_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $locat_arr[$row[csf('location_id')]];
                            } else {
                                $knitting_party = "";
                                $knitting_location = '';
                            }
                            if (in_array($row[csf('issue_purpose')], $issue_purpose_array)) {
                                $print_caption = 0;
                            } else {
                                $print_caption = 1;
                                $issue_purpose_array[$i] = $row[csf('issue_purpose')];
                            }



                            if ($print_caption == 1 && $k != 1) {
                        ?>
                                <tr class="tbl_bottom">
                                    <td colspan="17" align="right"><b>Total</b></td>
                                    <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_qnty, 2); ?></b></td>
                                    <td></td>
                                    <td><b><?php echo number_format($issue_amount, 2); ?></b></td>
                                    <?
                                    if ($zero_val == 0) {
                                    ?>
                                        <td align="right" style="word-break: break-all;"></td>
                                        <td align="right"><b><?php echo number_format($total_amount, 2); ?></b></td>
                                    <?
                                    }
                                    ?>
                                    <td align="right" style="word-break: break-all;"><b><?php echo number_format($return_qnty, 2); ?></b></td>
                                    <td align="right" style="word-break: break-all;">
                                        <p>
                                            <?
                                            echo number_format($issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]], 2);
                                            $returnQntypurpose = $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                                            ?>
                                        </p>
                                    </td>
                                    <td colspan="7"></td>
                                </tr>
                            <?
                                $issue_qnty = 0;
                                $issue_amount = 0;
                                $total_amount = 0;
                                $return_qnty = 0;
                                $total_issue_return = 0;
                            }
                            if ($print_caption == 1) {
                            ?>
                                <tr>
                                    <td colspan="<? echo $span; ?>" style="font-size:14px;" bgcolor="#CCCCCC"><b><?php echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></b></td>
                                </tr>
                            <?
                            }
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">
                                <td width="30"><? echo $i; ?></td>
                                <?
                                $buyer = '';
                                $job_no = '';
                                $styRef = '';
                                $booking_reqsn_qty = 0;
                                $knit_id = '';
                                $order_no = '';
                                $order_ref = '';
                                $order_file = '';

                                if ($row[csf('issue_basis')] == 1) {
                                    $booking_req_no = $row[csf('booking_no')];

                                    if ($row[csf('issue_purpose')] == 8) {
                                        $buyer = $booking_without_array[$row[csf('booking_no')]]['buyer_id'];
                                        $booking_reqsn_qty = $booking_without_array[$row[csf('booking_no')]]['qnty'];
                                        $job_no = '';
                                        $styRef = '';
                                        $order_no = "";
                                        $order_ref = "";
                                        $order_file = "";
                                    } else {
                                        $job_no = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['job_no'];
                                        $buyer = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['buyer_name'];
                                        $styRef = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['style_ref_no'];
                                        $order_no = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['po_number'];
                                        $order_ref = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['ref'];
                                        $order_file = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['file'];
                                        $booking_reqsn_qty = $booking_arr[$row[csf('booking_no')]]['qnty'];
                                    }
                                } else if ($row[csf('issue_basis')] == 3) {
                                    $booking_req_no = $row[csf('requisition_no')];
                                    $reqi_wise_booking = $reqibooking_arr[$row[csf('requisition_no')]]['booking_no'];
                                    $knit_id = $requisition_arr[$row[csf('requisition_no')]]['knit_id'];
                                    $job_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['job_no'];
                                    $buyer = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['buyer_name'];
                                    $styRef = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['style_ref_no'];
                                    $order_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['po_number'];
                                    $order_ref = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['ref'];
                                    $order_file = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['file'];
                                    $booking_reqsn_qty = $requisition_arr[$row[csf('requisition_no')]]['qnty'];
                                } else {
                                    $booking_req_no = "";
                                    $buyer = $row[csf('buyer_id')];
                                    $job_no = '';
                                    $styRef = '';
                                    $order_no = '';
                                    $order_ref = '';
                                    $order_file = '';
                                    $booking_reqsn_qty = 0;
                                }

                                if ($row[csf('issue_purpose')] == 5) {
                                    $knitting_party = $other_party_arr[$row[csf('other_party')]];
                                } else if ($row[csf('issue_purpose')] == 3) {
                                    $knitting_party = $buyer_arr[$row[csf('buyer_id')]];
                                    $buyer = '';
                                }
                                ?>
                                <td width="90" align="center" style="word-break: break-all;">
                                    <p><? echo $job_no; ?></p>
                                </td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $buyer_arr[$buyer]; ?></p>
                                </td>
                                <td width="80" style="word-break: break-all;">
                                    <p><? echo $file_no = implode(",", $order_file);
                                        echo $file_no; ?></p>
                                </td>
                                <td width="80" style="word-break: break-all;">
                                    <p><? echo $ref_no = implode(",", $order_ref);
                                        echo $ref_no; ?></p>
                                </td>
                                <td width="135" style="word-break: break-all;">
                                    <p><? echo $styRef; ?></p>
                                </td>
                                <td width="150" style="word-break: break-all;">
                                    <p><? $order_n = implode(",", $order_no);
                                        echo $order_n; ?></p>
                                </td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $issue_basis[$row[csf('issue_basis')]]; ?></p>
                                </td>
                                <td width="110" style="word-break: break-all;">
                                    <p><? echo $row[csf('issue_number')]; ?></p>
                                </td>
                                <td width="70" style="word-break: break-all;" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                                <td width="80" style="word-break: break-all;">
                                    <p><? echo $row[csf('challan_no')]; ?></p>
                                </td>
                                <td width="60" style="word-break: break-all;" align="center">
                                    <p><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p>
                                </td>
                                <td width="70" style="word-break: break-all;">
                                    <p><? echo $brand_arr[$row[csf('brand')]]; ?></p>
                                </td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $composition[$row[csf('yarn_comp_type1st')]] . $row[csf('yarn_comp_percent1st')] . '%'; ?></p>
                                </td>
                                <td width="80" style="word-break: break-all;">
                                    <p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p>
                                </td>
                                <td width="90" style="word-break: break-all;">
                                    <p><? echo $color_arr[$row[csf('color')]]; ?></p>
                                </td>
                                <td width="70" style="word-break: break-all;">
                                    <p><? echo $row[csf('lot')]; ?></p>
                                </td>
                                <td width="90" style="word-break: break-all;" align="right"> <? echo number_format($row[csf('issue_qnty')], 2);
                                                                                                $total_iss_qnty += $row[csf('issue_qnty')];
                                                                                                $issue_qnty += $row[csf('issue_qnty')]; ?>
                                </td>
                                <td width="60" align="right" style="word-break: break-all;"><? echo number_format($row[csf('cons_rate')] / $exchangeRate, 2); ?></td>
                                <td width="70" align="right" style="word-break: break-all;">
                                    <?
                                    echo number_format($row[csf('cons_amount')] / $exchangeRate, 2);
                                    $total_iss_amount += $row[csf('cons_amount')] / $exchangeRate;
                                    $issue_amount += $row[csf('cons_amount')] / $exchangeRate;
                                    ?>
                                </td>

                                <?
                                if ($zero_val == 0) {
                                ?>
                                    <td width="90" align="right" style="word-break: break-all;">
                                        <?
                                        $avgg = $row[csf('avg_rate_per_unit')];
                                        $avg_rate = $avgg / 78;
                                        echo number_format($avg_rate, 4);
                                        ?>
                                    </td>
                                    <td width="110" align="right" style="word-break: break-all;">
                                        <?
                                        $amount = $row[csf('issue_qnty')] * $avg_rate;
                                        echo number_format($amount, 2);
                                        $total_amount += $amount;
                                        $grand_total_amount += $amount;

                                        ?>
                                    </td>
                                <?
                                }
                                ?>
                                <td width="90" align="right" style="word-break: break-all;">
                                    <?
                                    echo number_format($row[csf('return_qnty')], 2);
                                    $total_ret_qnty += $row[csf('return_qnty')];
                                    $return_qnty += $row[csf('return_qnty')];
                                    ?>
                                </td>

                                <td width="100" style="word-break: break-all;" align="right">
                                    <p>
                                        <?
                                        echo number_format($issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]], 2);
                                        $total_issue_return += $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                                        $grand_issue_return += $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];

                                        ?>
                                    </p>
                                </td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p>
                                </td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $using_item_arr[$row[csf("using_item")]]; ?></p>
                                </td>
                                <td width="110" style="word-break: break-all;" align="center"><? echo $booking_req_no . '<br>' . $reqi_wise_booking;
                                                                                                unset($reqi_wise_booking); ?></td>
                                <td width="100" style="word-break: break-all;" align="right"><? echo number_format($booking_reqsn_qty, 2); ?></td>
                                <td width="130" style="word-break: break-all;">
                                    <p><? echo $knitting_party; ?></p>
                                </td>
                                <td width="130" style="word-break: break-all;">
                                    <p><? echo $knitting_location; ?></p>
                                </td>
                                <td style="word-break: break-all;">
                                    <p><? echo $store_arr[$row[csf('store_id')]]; ?>
                                </td>
                            </tr>
                        <?
                            $k++;
                            $i++;
                            $grnd_purpose_issue_qnty += $row[csf('issue_qnty')];
                            $grnd_purpose_issue_amount += $row[csf('cons_amount')] / $exchangeRate;
                            $grnd_purpose_total_amount += $amount;
                            $grnd_purpose_return_qnty += $row[csf('return_qnty')];
                            $grnd_purpose_issue_return_qnty += $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                        }

                        if (count($nameArray) > 0) {
                        ?>
                            <tr class="tbl_bottom">
                                <td colspan="17" align="right"><b>Total</b></td>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_qnty, 2); ?></b></td>
                                <td></td>
                                <td><b><?php echo number_format($issue_amount, 2); ?></b></td>
                                <?
                                if ($zero_val == 0) {
                                ?>
                                    <td align="right" style="word-break: break-all;"></td>
                                    <td align="right"><b><?php echo number_format($total_amount, 2); ?></b></td>
                                <?
                                }
                                ?>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($return_qnty, 2);
                                                                                    $return_qnty = 0; ?></b></td>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($total_issue_return, 2);
                                                                                    $total_issue_return = 0; ?></b></td>
                                <td colspan="7"></td>
                            </tr>
                        <?
                        }
                        ?>
                    </tbody>
                    <tfoot style="background-color: grey;">
                        <th colspan="17" align="right">Issue Purpose Wise Grand Total</th>
                        <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_qnty, 2); ?></th>
                        <th></th>
                        <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_amount, 2); ?></th>
                        <?
                        if ($zero_val == 0) {
                        ?>
                            <th></th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_total_amount, 2); ?></th>
                        <?
                        }
                        ?>
                        <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_return_qnty, 2); ?></th>
                        <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_return_qnty, 2); ?></th>
                        <th colspan="7" align="right"></th>
                    </tfoot>

                    <!--<tfoot>
                            <th colspan="17" align="right">Grand Total</th>
                            <th align="right" style="word-break: break-all;" ><?php //echo number_format($total_iss_qnty, 2); 
                                                                                ?></th>
                            <th></th>
                            <th align="right" style="word-break: break-all;" ><?php //echo number_format($total_iss_amount, 2); 
                                                                                ?></th>
                            <?
                            if ($zero_val == 0) {
                            ?>
                                <th></th>
                                <th align="right" style="word-break: break-all;" ><?php //echo number_format($grand_total_amount, 2); 
                                                                                    ?></th>
                                <?
                            }
                                ?>
                            <th align="right" style="word-break: break-all;" ><?php //echo number_format($total_ret_qnty, 2); 
                                                                                ?></th>
                            <th align="right" style="word-break: break-all;" ><?php //echo number_format($grand_issue_return, 2); 
                                                                                ?></th>
                            <th colspan="7" align="right"></th>
                    </tfoot>-->
                </table>
            </div>
        </fieldset>
    <?
    } else if ($type == 4) // Report buttom 4
    {
        // Made by MD Didarul Alam
        // Date : 06-06-2020

        $sql = "select * from (
        SELECT a.company_id,a.issue_date, a.id as issue_id,a.issue_number_prefix_num as issue_number, a.issue_basis,a.issue_purpose,a.buyer_id,a.booking_no,b.requisition_no, sum(b.cons_quantity) as issue_qnty, b.cons_rate, c.brand,c.id as product_id,c.lot,c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st,c.yarn_comp_percent1st,d.po_breakdown_id,d.trans_id  from inv_issue_master a, inv_transaction b left join order_wise_pro_details d on b.id=d.trans_id and b.prod_id= d.prod_id and d.trans_type=2,product_details_master c where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.issue_date between '$from_date' and '$to_date' and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lot_no $store_cond $yarn_type_cond $yarn_count_cond $issue_purpose_cond $using_item_cond $buyerCond $booking_req_cond $display_cond
            group by a.company_id,a.issue_date,a.id , a.issue_number_prefix_num,a.issue_basis,a.issue_purpose,a.buyer_id,a.booking_no,b.requisition_no,b.cons_rate,c.brand,c.id,c.lot,c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st,c.yarn_comp_percent1st,d.po_breakdown_id,d.trans_id)
            order by yarn_count_id";

        $results = sql_select($sql);

        $transissueIdChk = array();
        foreach ($results as $row) {

            $compPercent = $row[csf('yarn_comp_percent1st')];
            $yanrType = $row[csf('yarn_type')];
            $yarnComposition = $row[csf('yarn_comp_type1st')];
            $yarnCount = $row[csf('yarn_count_id')];
            $product_id = $row[csf('product_id')];
            $lot = $row[csf('lot')];
            $issue_date = $row[csf('issue_date')];
            $issue_id = $row[csf('issue_id')];

            $groupKey = $yarnCount . "**" . $yarnComposition . "**" . $compPercent . "**" . $yanrType;

            if ($transissueIdChk[$row[csf("trans_id")]] == "") {
                $transissueIdChk[$row[csf("trans_id")]] = $row[csf("trans_id")];

                $groupdataArr[$groupKey][$issue_date][$issue_id][$product_id]['issue_date'] = $row[csf('issue_date')];
                $groupdataArr[$groupKey][$issue_date][$issue_id][$product_id]['issue_id'] = $row[csf('issue_id')];
                $groupdataArr[$groupKey][$issue_date][$issue_id][$product_id]['issue_number'] = $row[csf('issue_number')];
                $groupdataArr[$groupKey][$issue_date][$issue_id][$product_id]['brand'] = $row[csf('brand')];
                $groupdataArr[$groupKey][$issue_date][$issue_id][$product_id]['product_id'] = $row[csf('product_id')];
                $groupdataArr[$groupKey][$issue_date][$issue_id][$product_id]['lot'] = $row[csf('lot')];
                $groupdataArr[$groupKey][$issue_date][$issue_id][$product_id]['issue_basis'] = $row[csf('issue_basis')];
                $groupdataArr[$groupKey][$issue_date][$issue_id][$product_id]['issue_purpose'] = $row[csf('issue_purpose')];
                $groupdataArr[$groupKey][$issue_date][$issue_id][$product_id]['buyer_id'] = $row[csf('buyer_id')];
                $groupdataArr[$groupKey][$issue_date][$issue_id][$product_id]['booking_no'] = $row[csf('booking_no')];
                $groupdataArr[$groupKey][$issue_date][$issue_id][$product_id]['requisition_no'] = $row[csf('requisition_no')];
                $groupdataArr[$groupKey][$issue_date][$issue_id][$product_id]['issue_qnty'] += $row[csf('issue_qnty')];
                $groupdataArr[$groupKey][$issue_date][$issue_id][$product_id]['cons_rate'] = $row[csf('cons_rate')];
                $groupdataArr[$groupKey][$issue_date][$issue_id][$product_id]['po_breakdown_id'] = $row[csf('po_breakdown_id')];
            }

            if ($row[csf('po_breakdown_id')] != "") {
                $po_id .= $row[csf('po_breakdown_id')] . ",";
            }

            $issue_ids .= $row[csf("issue_id")] . ",";
        }

        $issue_ids = chop($issue_ids, ",");
        $issue_ids = implode(",", array_filter(array_unique(explode(",", $issue_ids))));
        //echo "<pre>";
        //print_r($issue_ids);

        $po_id_cond = "";
        if ($po_id != "") {

            $po_id = substr($po_id, 0, -1);
            if ($db_type == 0) {
                $po_id_cond = "and c.id in(" . $po_id . ")";
            } else {
                $po_ids = explode(",", $po_id);
                if (count($po_ids) > 100) {
                    $po_id_cond = "and (";
                    $po_ids = array_chunk($po_ids, 100);
                    $z = 0;
                    foreach ($po_ids as $id) {
                        $id = implode(",", $id);
                        if ($z == 0)
                            $po_id_cond .= " c.id in(" . $id . ")";
                        else
                            $po_id_cond .= " or c.id in(" . $id . ")";
                        $z++;
                    }
                    $po_id_cond .= ")";
                } else {
                    $po_id_cond = "and c.id in(" . $po_id . ")";
                }
            }

            $poSql = "select c.id,c.grouping from wo_po_break_down c where c.status_active=1 and c.is_deleted=0 $po_id_cond";
            $poSqlResult = sql_select($poSql);

            $poDataArr = array();
            foreach ($poSqlResult as $po_row) {
                $poDataArr[$po_row[csf('id')]]['int_ref'] .= $po_row[csf('grouping')] . ",";
            }
        }

        // ================= Issue Return ===============//
        if ($issue_ids != "") {
            $issue_ids = explode(",", $issue_ids);
            $issue_ids_chnk = array_chunk($issue_ids, 999);
            $issue_no_cond = " and";
            foreach ($issue_ids_chnk as $issueId) {
                if ($issue_no_cond == " and")  $issue_no_cond .= "(c.id in(" . implode(',', $issueId) . ")";
                else $issue_no_cond .= " or c.id in(" . implode(',', $issueId) . ")";
            }
            $issue_no_cond .= ")";
            //echo $issue_no_cond;die;
            $issue_return_res = sql_select("SELECT a.recv_number,a.booking_no, b.cons_quantity, c.id as issue_id,b.id as trans_id,d.brand,d.id as product_id,d.lot,d.yarn_type, d.yarn_count_id, d.yarn_comp_type1st,d.yarn_comp_percent1st from inv_receive_master a, inv_transaction b, inv_issue_master c,product_details_master d where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and b.prod_id=d.id and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 $issue_no_cond");
        }

        $transIdChk = array();
        foreach ($issue_return_res as $val) {
            if ($transIdChk[$val[csf("trans_id")]] == "") {
                $transIdChk[$val[csf("trans_id")]] = $val[csf("trans_id")];
                //$issue_return_qnty_arr[$val[csf("issue_number")]][$val[csf("booking_no")]][$val[csf("prod_id")]] += $val[csf("cons_quantity")];

                $compPercent = $val[csf('yarn_comp_percent1st')];
                $yanrType = $val[csf('yarn_type')];
                $yarnComposition = $val[csf('yarn_comp_type1st')];
                $yarnCount = $val[csf('yarn_count_id')];
                $prod_id = $val[csf('product_id')];

                $groupKey = $yarnCount . "**" . $yarnComposition . "**" . $compPercent . "**" . $yanrType;
                $issue_return_qnty_arr[$groupKey][$val[csf('issue_id')]] += $val[csf("cons_quantity")];
            }
        }

        //echo "<pre>";
        //print_r($issue_return_qnty_arr);
    ?>
        <fieldset style="width:1150px;">

            <table cellpadding="0" cellspacing="0" width="1130" style="float: left;">
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong>From Date: <? echo change_date_format(str_replace("'", "", $txt_date_from)); ?> To Date: <? echo change_date_format(str_replace("'", "", $txt_date_to)); ?></strong></td>
                </tr>
            </table>

            <table width="1130" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">
                <thead>
                    <th width="30">SL</th>
                    <th width="130">Issue Date</th>
                    <th width="100">Issue No</th>
                    <th width="150">Brand - Lot</th>
                    <th width="100">Buyer</th>
                    <th width="100">Int. Ref.</th>
                    <th width="120">Purpose</th>
                    <th width="200">Booking/ Reqn. No</th>
                    <th width="100">Issue Qty.</th>
                    <th width="100">Return Qty.</th>
                    <th width="100">Net Issue Qty.</th>
                    <th width="100">Avg Rate [USD]</th>
                    <th width="100">Net Value [USD]</th>
                </thead>
            </table>
            <div style="width:1150px; overflow-y: scroll; max-height:380px; float: left;" id="scroll_body">
                <table width="1130" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">
                    <tbody>
                        <?
                        $i = 1;
                        $k = 0;
                        $issue_grand_total_qnty = 0;
                        $issue_grand_total_amount = 0;
                        $issue_rtn_grand_total_qnty = 0;
                        $net_issue_grand_total_qnty = 0;
                        $order_ref = "";
                        foreach ($groupdataArr as $groupKey => $issueDateArr) {
                            foreach ($issueDateArr as $issueDate => $issueIdArr) {
                                foreach ($issueIdArr as $issueId => $prodIdArr) {
                                    foreach ($prodIdArr as $prod_id => $row) {

                                        $compPercent = $row['yarn_comp_percent1st'];
                                        $yanrType = $row['yarn_type'];
                                        $yarnComposition = $row['yarn_comp_type1st'];
                                        $yarnCount = $row['yarn_count_id'];
                                        $issueDate = $row['issue_date'];

                                        $caption = $yarnCount . "**" . $yarnComposition . "**" . $compPercent . "**" . $yanrType;
                                        $order_ref = implode(",", array_unique(explode(",", chop($poDataArr[$row['po_breakdown_id']]['int_ref'], ","))));

                                        $exchangeRate = $usd_arr[date('d-m-Y', strtotime($row['issue_date']))];
                                        if ($exchangeRate == "") {
                                            foreach ($usd_arr as $rate_date => $rat) {
                                                if (strtotime($rate_date) <= strtotime($row['issue_date'])) {
                                                    $rate_date = date('d-m-Y', strtotime($rate_date));
                                                    $exchangeRate = $rat;
                                                    break;
                                                }
                                            }
                                        }

                                        if ($brand_arr[$row['brand']] != "") {
                                            $brand_lot = $brand_arr[$row['brand']] . "-" . $row['lot'];
                                        } else {
                                            $brand_lot = $row['lot'];
                                        }

                                        if ($row['issue_basis'] == 1) {
                                            $booking_req_no = $row['booking_no'];
                                        } else if ($row['issue_basis'] == 3) {
                                            $booking_req_no = $row['requisition_no'];
                                        } else {
                                            $booking_req_no = "";
                                        }

                                        $buyer_name = $buyer_arr[$row['buyer_id']];

                                        //echo $groupKey."==".$issueId."|<br>";
                                        $issue_return_qnty = $issue_return_qnty_arr[$groupKey][$issueId];

                                        if ($i % 2 == 0)
                                            $bgcolor = "#E9F3FF";
                                        else
                                            $bgcolor = "#FFFFFF";

                                        //echo $exchangeRate;

                                        //$avgRate = ($row[csf('issue_amount')]/$exchangeRate);
                                        //$amount = $row[csf('issue_qnty')] * $exchangeRate;
                                        $amount =  (($row['issue_qnty'] - $issue_return_qnty) * number_format($row['cons_rate'] / $exchangeRate, 2));

                                        if (!in_array($groupKey, $yarnDescriptionArr)) {
                                            if ($i != 1) {
                        ?>
                                                <tr bgcolor="#CCCCCC">
                                                    <td colspan="8" align="right"><b>Sub Total</b></td>
                                                    <td align="center"><?php echo number_format($issue_subtotal_qnty, 2, '.', ''); ?></td>
                                                    <td align="center"><?php echo number_format($issue_rtn_subtotal_qnty, 2, '.', ''); ?></td>
                                                    <td align="center"><?php echo number_format($net_issue_subtotal_qnty, 2, '.', ''); ?></td>
                                                    <td>&nbsp;</td>
                                                    <td align="right"><?php echo number_format($issue_subtotal_amount, 2, '.', ''); ?></td>
                                                </tr>
                                            <?
                                                $issue_subtotal_qnty = 0;
                                                $issue_subtotal_amount = 0;
                                                $issue_rtn_subtotal_qnty = 0;
                                                $net_issue_subtotal_qnty = 0;
                                            }

                                            $yarnDescriptionArr[$i] = $groupKey;

                                            ?>
                                            <tr>
                                                <td colspan="13" bgcolor="#EEEEEE">
                                                    <b>
                                                        <?php
                                                        $captionData = explode("**", $yarnDescriptionArr[$i]);
                                                        echo $count_arr[$captionData[0]] . " " . $composition[$captionData[1]] . " " . $captionData[2] . "% " . $yarn_type[$captionData[3]];
                                                        ?>
                                                    </b>
                                                </td>
                                            </tr>
                                        <?
                                        }
                                        ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">
                                            <td width="30" align="center"><? echo $i; ?></td>
                                            <td width="130" align="center" style="word-break: break-all;">
                                                <p><? echo change_date_format($row['issue_date']); ?>&nbsp;</p>
                                            </td>
                                            <td width="100" style="word-break: break-all;text-align:center;">
                                                <p><? echo $row['issue_number']; ?></p>
                                            </td>
                                            <td width="150" style="word-break: break-all;">
                                                <p><? echo $brand_lot; ?></p>
                                            </td>
                                            <td width="100" style="word-break: break-all;">
                                                <p><? echo $buyer_name; ?></p>
                                            </td>
                                            <td width="100" style="word-break: break-all;">
                                                <p><? echo $order_ref; ?></p>
                                            </td>
                                            <td width="120" style="word-break: break-all;">
                                                <p><? echo $yarn_issue_purpose[$row['issue_purpose']]; ?></p>
                                            </td>
                                            <td width="200" style="word-break: break-all;">
                                                <p><? echo $booking_req_no; ?></p>
                                            </td>
                                            <td width="100" style="word-break: break-all; text-align:center;">
                                                <p><? echo number_format($row['issue_qnty'], 2); ?></p>
                                            </td>
                                            <td width="100" style="word-break: break-all; text-align:center;">
                                                <p><? echo number_format($issue_return_qnty, 2); ?></p>
                                            </td>
                                            <td width="100" style="word-break: break-all; text-align:center;">
                                                <p><? echo number_format(($row['issue_qnty'] - $issue_return_qnty), 2); ?></p>
                                            </td>
                                            <td width="100" align="right" style="word-break: break-all;"><? echo number_format($row['cons_rate'] / $exchangeRate, 2); ?></td>
                                            <td width="100" style="word-break: break-all; text-align:right;">
                                                <p><? echo number_format($amount, 2); ?></p>
                                            </td>
                                        </tr>
                        <?
                                        $issue_subtotal_qnty += $row['issue_qnty'];
                                        $issue_subtotal_amount += $amount;
                                        $issue_grand_total_qnty += $row['issue_qnty'];
                                        $issue_grand_total_amount += $amount;

                                        $issue_rtn_subtotal_qnty += $issue_return_qnty;
                                        $issue_rtn_grand_total_qnty += $issue_return_qnty;
                                        $net_issue_subtotal_qnty += ($row['issue_qnty'] - $issue_return_qnty);
                                        $net_issue_grand_total_qnty += ($row['issue_qnty'] - $issue_return_qnty);

                                        $i++;
                                    }
                                }
                            }
                        }
                        ?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="8" align="right"><b>Sub Total</b></td>
                            <td align="center"><?php echo number_format($issue_subtotal_qnty, 2, '.', ''); ?></td>
                            <td align="center"><?php echo number_format($issue_rtn_subtotal_qnty, 2, '.', ''); ?></td>
                            <td align="center"><?php echo number_format($net_issue_subtotal_qnty, 2, '.', ''); ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><?php echo number_format($issue_subtotal_amount, 2, '.', ''); ?></td>
                        </tr>
                    <tfoot style="background-color: grey;">
                        <th colspan="8" align="right">Grand Total</th>
                        <th style="word-break: break-all; text-align:center;"><?php echo number_format($issue_grand_total_qnty, 2); ?></th>
                        <th style="word-break: break-all;text-align:center;"><?php echo number_format($issue_rtn_grand_total_qnty, 2); ?></th>
                        <th style="word-break: break-all;text-align:center;"><?php echo number_format($net_issue_grand_total_qnty, 2); ?></th>
                        <th>&nbsp;</th>
                        <th align="right" style="word-break: break-all;"><?php echo number_format($issue_grand_total_amount, 2); ?></th>
                    </tfoot>
                </table>
            </div>
        </fieldset>
    <?
    } else if ($type == 5) // SWO- With Plan Button
    {
    ?>
        <fieldset style="width:<? echo $value_width + 18; ?>px;">
            <table cellpadding="0" cellspacing="0" width="<? echo $value_width; ?>" style="float: left;">
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong>From Date: <? echo change_date_format(str_replace("'", "", $txt_date_from)); ?> To Date: <? echo change_date_format(str_replace("'", "", $txt_date_to)); ?></strong></td>
                </tr>
            </table>


            <table width="<? echo $value_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">
                <thead>
                    <th width="30">SL</th>
                    <th width="150">Sample booking no</th>
                    <th width="100">Buyer Name</th>
                    <th width="80">Sample Req. No</th>
                    <th width="80">Ref. No</th>
                    <th width="135">Style No</th>
                    <th width="100">Issue Basis</th>
                    <th width="110">Issue No</th>
                    <th width="70">Issue Date</th>
                    <th width="80">Challan No</th>
                    <th width="60">Count</th>
                    <th width="70">Yarn Brand</th>
                    <th width="100">Composition</th>
                    <th width="80">Type</th>
                    <th width="90">Color</th>
                    <th width="70">Lot No</th>
                    <th width="90">Issue Qty</th>
                    <? echo $column; ?>
                    <th width="90">Returnable Qty.</th>
                    <th width="100">Return Qty.</th>
                    <th width="100">Net Issue Qty.</th>
                    <th width="60">Rate/Kg</th>
                    <th width="100">Net Issue Amount</th>
                    <th width="100">Issue Purpose</th>
                    <th width="100">Using Item</th>
                    <th width="100">Reqn. No</th>
                    <th width="100">Program. No</th>
                    <th width="100">Booking/ Reqn. Qty</th>
                    <th width="130">Issue To</th>
                    <th width="130">Location</th>
                    <th>Store</th>
                </thead>
            </table>
            <div style="width:<? echo $value_width + 18; ?>px; overflow-y: scroll; max-height:380px; float: left;" id="scroll_body">
                <table width="<? echo $value_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">
                    <tbody>
                        <?
                        $i = 1;
                        $total_iss_qnty = 0;
                        $issue_qnty = 0;
                        $issue_amount = 0;
                        $return_qnty = 0;
                        $issue_amount_return = 0;
                        $issue_balance_qnty = 0;
                        $issue_amount_qnty = 0;
                        $caption = '';
                        $knitting_party = '';
                        $total_amount = 0;
                        $grand_total_amount = 0;

                        $issue_amount_grand = 0;
                        $issue_qnty_grand = 0;
                        $return_qnty_grand = 0;
                        $total_amount_grand = 0;
                        $issue_amount_return_grand = 0;
                        $issue_balance_qnty_grand = 0;
                        $issue_amount_qnty_grand = 0;

                        $inside_outside_array = array();
                        $issue_purpose_array = array();
                        //$reqi_wise_booking=0;

                        //===============end


                        ?>


                        <?
                        $k = 1;
                        $issue_amount = 0;
                        $issue_qnty = 0;
                        $knitting_party = '';
                        $total_amount = 0;
                        $return_qnty = 0;
                        $query = "SELECT a.issue_number, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.buyer_id, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no, a.other_party, b.requisition_no, b.store_id, b.brand_id, b.cons_quantity as issue_qnty, b.return_qnty, b.cons_rate, b.cons_amount, c.brand, c.yarn_count_id, c.yarn_type, c.lot, c.color, c.avg_rate_per_unit, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.id as product_id, a.location_id from inv_issue_master a, inv_transaction b, product_details_master c where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.issue_basis in (3) and a.issue_purpose in (1,8)  and a.issue_date between '$from_date' and '$to_date' and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lot_no $yarn_type_cond $yarn_count_cond  $using_item_cond $booking_req_cond order by a.issue_purpose, a.issue_date";


                        //echo $query;
                        $nameArray = sql_select($query);

                        //for library dtls
                        $lib_arr = array();
                        foreach ($nameArray as $row) {
                            $lib_arr['brand'][$row[csf('brand')]] = $row[csf('brand')];
                            $lib_arr['yarn_count'][$row[csf('yarn_count_id')]] = $row[csf('yarn_count_id')];
                            $lib_arr['color'][$row[csf('color')]] = $row[csf('color')];
                        }

                        $color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0 and id in(" . implode(',', $lib_arr['color']) . ")", "id", "color_name");
                        $count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 and id in(" . implode(',', $lib_arr['yarn_count']) . ")", 'id', 'yarn_count');
                        $brand_arr = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0 and id in(" . implode(',', $lib_arr['brand']) . ")", 'id', 'brand_name');
                        //end for library dtls

                        $grnd_purpose_issue_qnty = 0;
                        $grnd_purpose_issue_amount = 0;
                        $grnd_purpose_total_amount = 0;
                        $grnd_purpose_return_qnty = 0;
                        $grnd_purpose_issue_return_qnty = 0;
                        $grnd_purpose_issue_balance_qnty = 0;
                        $grnd_purpose_issue_amount_qnty = 0;

                        foreach ($nameArray as $row) {
                            $exchangeRate = $usd_arr[date('d-m-Y', strtotime($row[csf('issue_date')]))];
                            if ($exchangeRate == "") {
                                foreach ($usd_arr as $rate_date => $rat) {
                                    if (strtotime($rate_date) <= strtotime($row[csf('issue_date')])) {
                                        $rate_date = date('d-m-Y', strtotime($rate_date));
                                        $exchangeRate = $rat;
                                        break;
                                    }
                                }
                            }

                            //----------------------------------------------------------------------
                            /*$exchangeRate=$usd_arr[date('d-m-Y',strtotime($row[csf('issue_date')]))];
                            if(is_null($exchangeRate)){
                                for($day=1;$day<366;$day++){
                                    $previousDate=date('d-m-Y', strtotime("-$day day",strtotime($row[csf('issue_date')])));
                                    $exchangeRate=$usd_arr[$previousDate];
                                    if(!is_null($exchangeRate)){break;}
                                }
                            }*/
                            //-----------------------------------------------------------------------

                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            if ($row[csf('knit_dye_source')] == 1) {
                                $knitting_party = $company_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $location_arr[$row[csf('location_id')]];
                            } else if ($row[csf('knit_dye_source')] == 3) {
                                $knitting_party = $supplier_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $locat_arr[$row[csf('location_id')]];
                            } else {
                                $knitting_party = "";
                                $knitting_location = '';
                            }
                            if (in_array($row[csf('issue_purpose')], $issue_purpose_array)) {
                                $print_caption = 0;
                            } else {
                                $print_caption = 1;
                                $issue_purpose_array[$i] = $row[csf('issue_purpose')];
                            }



                            if ($print_caption == 1) {
                        ?>
                                <!-- <tr><td colspan="<? echo $span; ?>" style="font-size:14px;" bgcolor="#CCCCCC"><b><?php echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></b></td></tr>  -->
                            <?
                            }

                            $buyer = '';
                            $job_no = '';
                            $styRef = '';
                            $booking_reqsn_qty = 0;
                            $knit_id = '';
                            $order_no = '';
                            $order_ref = '';
                            $order_file = '';
                            if ($row[csf('issue_basis')] == 1) {
                                $booking_req_no = $row[csf('booking_no')];

                                if ($row[csf('issue_purpose')] == 1 || $row[csf('issue_purpose')] == 8) {
                                    $buyer = $booking_without_array[$row[csf('booking_no')]]['buyer_id'];

                                    $booking_reqsn_qty = $booking_without_array[$row[csf('booking_no')]]['qnty'];
                                    $job_no = '';
                                    $styRef = '';
                                    $order_no = "";
                                    $order_ref = "";
                                    $order_file = "";
                                } else {
                                    $job_no = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['job_no'];
                                    $buyer = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['buyer_name'];
                                    $styRef = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['style_ref_no'];
                                    $order_no = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['po_number'];
                                    $order_ref = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['ref'];
                                    $order_file = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['file'];
                                    $booking_reqsn_qty = $booking_arr[$row[csf('booking_no')]]['qnty'];
                                }
                            } else if ($row[csf('issue_basis')] == 3) {
                                // $booking_req_no = $row[csf('requisition_no')];
                                // $reqi_wise_booking = $reqibooking_arr[$row[csf('requisition_no')]]['booking_no'];
                                $program_no = $reqibooking_arr[$row[csf('requisition_no')]]['program_no'];
                                $knit_id = $requisition_arr[$row[csf('requisition_no')]]['knit_id'];
                                $job_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['job_no'];
                                // $buyer = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['buyer_name'];
                                //$styRef = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['style_ref_no'];
                                $order_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['po_number'];
                                // $order_ref = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['ref'];
                                //$order_file = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['file'];
                                $booking_reqsn_qty = $requisition_arr[$row[csf('requisition_no')]]['qnty'];

                                if ($row[csf('issue_purpose')] == 1 || $row[csf('issue_purpose')] == 8) {
                                    $booking_req_no = $row[csf('requisition_no')];
                                    $reqi_wise_booking = $reqibooking_arr[$row[csf('requisition_no')]]['booking_no'];
                                    $is_sales = $reqibooking_arr[$row[csf('requisition_no')]]['is_sales'];

                                    $requisition_number = $smn_info_arr[$reqi_wise_booking]['requisition_number'];
                                    $buyer = $smn_info_arr[$reqi_wise_booking]['buyer_name'];
                                    $styRef = $smn_info_arr[$reqi_wise_booking]['style_ref_no'];
                                    $order_ref = $smn_info_arr[$reqi_wise_booking]['buyer_ref'];
                                }
                            } else {
                                $booking_req_no = "";
                                $buyer = $row[csf('buyer_id')];
                                $job_no = '';
                                $styRef = '';
                                $order_no = '';
                                $order_ref = '';
                                $order_file = '';
                                $booking_reqsn_qty = 0;
                            }

                            if ($row[csf('issue_purpose')] == 5) {
                                $knitting_party = $other_party_arr[$row[csf('other_party')]];
                            } else if ($row[csf('issue_purpose')] == 3) {
                                $knitting_party = $buyer_arr[$row[csf('buyer_id')]];
                                $buyer = '';
                            }

                            //$data=explode("-",$reqi_wise_booking);
                            //var_dump( $data);
                            if ($is_sales == 2) {
                            ?>

                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">
                                    <td width="30"><? echo $i; ?></td>
                                    <td width="150" align="center" style="word-break: break-all;">
                                        <p><? echo $reqi_wise_booking; ?></p>
                                    </td>
                                    <td width="100" style="word-break: break-all;">
                                        <p><? echo  $buyer_arr[$buyer] //$buyer_arr[$buyer]; 
                                            ?></p>
                                    </td>
                                    <td width="80" style="word-break: break-all;">
                                        <p><?
                                            //echo $file_no = implode(",", $order_file);
                                            echo $requisition_number;
                                            ?></p>
                                    </td>
                                    <td width="80" style="word-break: break-all;">
                                        <p><?
                                            //echo $ref_no = implode(",", $order_ref);
                                            echo $order_ref;
                                            ?></p>
                                    </td>
                                    <td width="135" style="word-break: break-all;">
                                        <p><? echo $styRef; ?></p>
                                    </td>
                                    <td width="100" style="word-break: break-all;">
                                        <p><? echo $issue_basis[$row[csf('issue_basis')]]; ?></p>
                                    </td>
                                    <td width="110" style="word-break: break-all;">
                                        <p><? echo $row[csf('issue_number')]; ?></p>
                                    </td>
                                    <td width="70" style="word-break: break-all;" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                                    <td width="80" style="word-break: break-all;">
                                        <p><? echo $row[csf('challan_no')]; ?></p>
                                    </td>
                                    <td width="60" style="word-break: break-all;" align="center">
                                        <p><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p>
                                    </td>
                                    <td width="70" style="word-break: break-all;">
                                        <p><? echo $brand_arr[$row[csf('brand')]]; ?></p>
                                    </td>
                                    <td width="100" style="word-break: break-all;">
                                        <p><? echo $composition[$row[csf('yarn_comp_type1st')]] . $row[csf('yarn_comp_percent1st')] . '%'; ?></p>
                                    </td>
                                    <td width="80" style="word-break: break-all;">
                                        <p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p>
                                    </td>
                                    <td width="90" style="word-break: break-all;">
                                        <p><? echo $color_arr[$row[csf('color')]]; ?></p>
                                    </td>
                                    <td width="70" style="word-break: break-all;">
                                        <p><? echo $row[csf('lot')]; ?></p>
                                    </td>
                                    <td width="90" style="word-break: break-all;" align="right">
                                        <?
                                        echo number_format($row[csf('issue_qnty')], 2);
                                        $total_iss_qnty += $row[csf('issue_qnty')];
                                        $issue_qnty += $row[csf('issue_qnty')];
                                        ?>
                                    </td>
                                    <?
                                    if ($zero_val == 0) {
                                    ?>
                                        <td width="90" align="right" style="word-break: break-all;">
                                            <?
                                            $avgg = $row[csf('avg_rate_per_unit')];
                                            $avg_rate = $avgg / 78;
                                            echo number_format($avg_rate, 4);
                                            ?>
                                        </td>
                                        <td width="110" align="right" style="word-break: break-all;">
                                            <?
                                            $amount = $row[csf('issue_qnty')] * $avg_rate;
                                            echo number_format($amount, 2);
                                            $total_amount += $amount;
                                            $grand_total_amount += $amount;

                                            ?>
                                        </td>
                                    <?
                                    }
                                    ?>
                                    <td width="90" align="right" style="word-break: break-all;">
                                        <?
                                        echo number_format($row[csf('return_qnty')], 2);
                                        $total_ret_qnty += $row[csf('return_qnty')];
                                        $return_qnty += $row[csf('return_qnty')];
                                        ?>
                                    </td>

                                    <td width="100" style="word-break: break-all;" align="right">
                                        <p>
                                            <?
                                            echo number_format($issue_return_qnty_arr[$row[csf("issue_number")]][$booking_req_no][$row[csf("product_id")]], 2);
                                            $total_issue_return += $issue_return_qnty_arr[$row[csf("issue_number")]][$booking_req_no][$row[csf("product_id")]];
                                            $grand_issue_return += $issue_return_qnty_arr[$row[csf("issue_number")]][$booking_req_no][$row[csf("product_id")]];

                                            ?>
                                        </p>
                                    </td>

                                    <td width="100" style="word-break: break-all;" align="right">
                                        <p>
                                            <?
                                            echo number_format($row[csf('issue_qnty')] - $issue_return_qnty_arr[$row[csf("issue_number")]][$booking_req_no][$row[csf("product_id")]], 2);

                                            $total_issue_balance_qnty += $row[csf('issue_qnty')] - $issue_return_qnty_arr[$row[csf("issue_number")]][$booking_req_no][$row[csf("product_id")]];
                                            ?>
                                        </p>
                                    </td>
                                    <td width="60" align="right" style="word-break: break-all;"><? echo number_format($row[csf('cons_rate')] / $exchangeRate, 2); ?></td>
                                    <td width="100" style="word-break: break-all;" align="right">
                                        <p>
                                            <?
                                            echo number_format(($row[csf('cons_rate')] / $exchangeRate) * ($row[csf('issue_qnty')] - $issue_return_qnty_arr[$row[csf("issue_number")]][$booking_req_no][$row[csf("product_id")]]), 2);

                                            $total_issue_amount_qnty += ($row[csf('cons_rate')] / $exchangeRate) * ($row[csf('issue_qnty')] - $issue_return_qnty_arr[$row[csf("issue_number")]][$booking_req_no][$row[csf("product_id")]]);
                                            ?>
                                        </p>
                                    </td>

                                    <td width="100" style="word-break: break-all;">
                                        <p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p>
                                    </td>
                                    <td width="100" style="word-break: break-all;">
                                        <p><? echo $using_item_arr[$row[csf("using_item")]]; ?></p>
                                    </td>
                                    <td width="100" style="word-break: break-all;" align="center"><? echo $booking_req_no; ?></td>
                                    <td width="100" style="word-break: break-all;" align="center"><? echo $program_no; ?></td>
                                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($booking_reqsn_qty, 2); ?></td>
                                    <td width="130" style="word-break: break-all;">
                                        <p><? echo $knitting_party; ?></p>
                                    </td>
                                    <td width="130" style="word-break: break-all;">
                                        <p><? echo $knitting_location; ?></p>
                                    </td>
                                    <td style="word-break: break-all;">
                                        <p><? echo $store_arr[$row[csf('store_id')]]; ?>
                                    </td>
                                </tr>

                            <?
                                $k++;
                                $i++;
                                $grnd_purpose_issue_qnty += $row[csf('issue_qnty')];
                                $grnd_purpose_issue_amount += $row[csf('cons_amount')] / $exchangeRate;
                                $grnd_purpose_total_amount += $amount;
                                $grnd_purpose_return_qnty += $row[csf('return_qnty')];
                                $grnd_purpose_issue_return_qnty += $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                                $grnd_purpose_issue_balance_qnty += $row[csf('issue_qnty')] - $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                                $grnd_purpose_issue_amount_qnty += ($row[csf('cons_rate')] / $exchangeRate) * ($row[csf('issue_qnty')] - $issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]]);
                            }
                        }

                        if (count($nameArray) > 0) {
                            ?>
                            <tr class="tbl_bottom">
                                <td colspan="16" align="right"><b>Total</b></td>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_qnty, 2); ?></b></td>

                                <?
                                if ($zero_val == 0) {
                                ?>
                                    <td align="right" style="word-break: break-all;"></td>
                                    <td align="right"><b><?php echo number_format($total_amount, 2); ?></b></td>
                                <?
                                }
                                ?>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($return_qnty, 2);
                                                                                    $return_qnty = 0; ?></b></td>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($total_issue_return, 2);
                                                                                    $total_issue_return = 0; ?></b></td>

                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($total_issue_balance_qnty, 2);
                                                                                    $total_issue_balance_qnty = 0; ?></b></td>
                                <td>&nbsp;</td>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($total_issue_amount_qnty, 2);
                                                                                    $total_issue_amount_qnty = 0; ?></b></td>
                                <td colspan="8"></td>
                            </tr>
                        <?
                        }
                        ?>
                        <tr>
                            <th colspan="16" align="right">Issue Purpose Wise Grand Total</th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_qnty, 2); ?></th>

                            <?
                            if ($zero_val == 0) {
                            ?>
                                <th></th>
                                <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_total_amount, 2); ?></th>
                            <?
                            }
                            ?>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_return_qnty, 2); ?></th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_return_qnty, 2); ?></th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_balance_qnty, 2); ?></th>
                            <th></th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_amount_qnty, 2); ?></th>

                            <th colspan="8" align="right"></th>
                        </tr>
                    </tbody>

                    <tfoot style="background-color: grey;">
                        <tr>
                            <th colspan="16" align="right"> Grand Total</th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_qnty + $issue_qnty_grand, 2); ?></th>

                            <?
                            if ($zero_val == 0) {
                            ?>
                                <th></th>
                                <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_total_amount + $total_amount_grand, 2); ?></th>
                            <?
                            }
                            ?>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_return_qnty + $return_qnty_grand, 2); ?></th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_return_qnty + $issue_amount_return_grand, 2); ?></th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_balance_qnty + $issue_balance_qnty_grand, 2); ?></th>
                            <th></th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_amount_qnty + $issue_amount_qnty_grand, 2); ?></th>

                            <th colspan="8" align="right"></th>
                        </tr>
                    </tfoot>

                </table>
            </div>
        </fieldset>
    <?
    } else if ($type == 6) // Show2 Button
    {
        if ($zero_val == 1) {
            $value_width = 1865;
            $span = 20;
            $column = '';
        } else {
            $value_width = 2115;
            $span = 22;
            $column = '<th rowspan="2" width="90">Avg. Rate (Tk)</th><th rowspan="2" width="110">Stock Value</th>';
        }
    ?>
        <style>
            .wrd_brk {
                word-break: break-all;
                word-wrap: break-word;
            }
        </style>
        <fieldset style="width:<? echo $value_width + 18; ?>px;">
            <table cellpadding="0" cellspacing="0" width="<? echo $value_width; ?>" style="float: left;">
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong>From Date: <? echo change_date_format(str_replace("'", "", $txt_date_from)); ?> To Date: <? echo change_date_format(str_replace("'", "", $txt_date_to)); ?></strong></td>
                </tr>
            </table>
            <table style="float: left; margin-bottom: 10px;" width="250" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th colspan="2"><b>Summary</b></th>
                </thead>
                <?
                //summary issue qnty
                $inside_outside_array_summary = array();
                $kk = 1;
                $caption_summary = '';
                $summary_issueQnty = 0;
                $grand_total = 0;
                foreach ($result_summery as $rows) {
                    if ($rows[csf('knit_dye_source')] == 1) {
                        $caption_summary = 'Inside';
                    } else if ($rows[csf('knit_dye_source')] == 3) {
                        $caption_summary = 'Outside';
                    } else {

                        $caption_summary = '';
                    }

                    if (in_array($rows[csf('knit_dye_source')], $inside_outside_array_summary)) {
                        $print_caption = 0;
                    } else {
                        $print_caption = 1;
                        $inside_outside_array_summary[$kk] = $rows[csf('knit_dye_source')];
                    }

                    if ($print_caption == 1) {
                        $summary_issueQnty = $rows[csf('issue_qnty')];
                    }
                    if ($print_caption == 1 && $rows[csf('knit_dye_source')] != 0) {
                ?>
                        <tr>
                            <td><b><?php echo $caption_summary; ?></b></td>

                            <td align="right"><b><? echo number_format($summary_issueQnty, 2); ?></b></td>

                        </tr>
                    <?
                        $grand_total += $summary_issueQnty;
                    }

                    $kk++;
                }

                foreach ($issue_purpose_wise_issue_qnty_arr as $key => $values) {
                    ?>
                    <tr>
                        <td><b><? echo  $yarn_issue_purpose[$key]; ?></b></td>
                        <td align="right"><b><? echo  number_format($values, 2); ?></b></td>
                    </tr>
                <?
                    $grand_total += $values;
                }
                //end summary issue qnty
                $g_total = 0;
                $sample = 0;
                ?>
                <tr style="background-color: #f9f9f9;">
                    <td><b>Total</b></td>
                    <td align="right"><b><? echo  number_format($grand_total, 2); ?></b></td>
                </tr>
                <?
                foreach ($sample_without_order_issue_qnty_arr as $key => $values) {
                ?>
                    <tr>
                        <td><b><? echo  $yarn_issue_purpose[$key]; ?></b></td>
                        <td align="right"><b><? echo  number_format($values, 2);
                                                $sample += $values; ?></b></td>
                    </tr>
                <?
                }
                ?>
                <tr>
                    <td><b>Grand Total</b></td>
                    <td align="right"><b><?php echo number_format($grand_total + $sample, 2); ?></b></td>
                </tr>
            </table>

            <table width="<? echo $value_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Issue Basis</th>
                    <th width="110">Issue No</th>
                    <th width="70">Issue Date</th>
                    <th width="60">Count</th>
                    <th width="70">Yarn Brand</th>
                    <th width="100">Composition</th>
                    <th width="80">Type</th>
                    <th width="90">Color</th>
                    <th width="70">Lot No</th>
                    <th width="90">Issue Qty</th>
                    <? echo $column; ?>

                    <th width="100">Issue Purpose</th>
                    <th width="100">Booking Type</th>
                    <th width="110">Buyer Name</th>
                    <th width="100">IR/IB</th>
                    <th width="130">Issue To</th>
                    <th width="130">Location</th>
                    <th width="100">Store</th>
                    <th width="100">PI / LC NUMBER</th>
                    <th>Remarks</th>
                </thead>
            </table>
            <div style="width:<? echo $value_width + 18; ?>px; overflow-y: scroll; max-height:380px; float: left;" id="scroll_body">
                <table width="<? echo $value_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">
                    <tbody>
                        <?
                        $i = 1;
                        $total_iss_qnty = 0;
                        $issue_qnty = 0;
                        $issue_amount = 0;
                        $return_qnty = 0;
                        $issue_amount_return = 0;
                        $issue_balance_qnty = 0;
                        $issue_amount_qnty = 0;
                        $caption = '';
                        $knitting_party = '';
                        $total_amount = 0;
                        $grand_total_amount = 0;

                        $issue_amount_grand = 0;
                        $issue_qnty_grand = 0;
                        $return_qnty_grand = 0;
                        $total_amount_grand = 0;
                        $issue_amount_return_grand = 0;
                        $issue_balance_qnty_grand = 0;
                        $issue_amount_qnty_grand = 0;

                        $inside_outside_array = array();
                        $issue_purpose_array = array();
                        //$reqi_wise_booking=0;
                        // echo "<pre>";
                        // print_r($result); die;
                        foreach ($result as $row) {
                            $exchangeRate = $usd_arr[date('d-m-Y', strtotime($row[csf('issue_date')]))];
                            if ($exchangeRate == "") {
                                foreach ($usd_arr as $rate_date => $rat) {
                                    if (strtotime($rate_date) <= strtotime($row[csf('issue_date')])) {
                                        $rate_date = date('d-m-Y', strtotime($rate_date));
                                        $exchangeRate = $rat;
                                        break;
                                    }
                                }
                            }

                            //----------------------------------------------------------------------
                            /*$exchangeRate=$usd_arr[date('d-m-Y',strtotime($row[csf('issue_date')]))];
                            if(is_null($exchangeRate)){
                                for($day=1;$day<366;$day++){
                                    $previousDate=date('d-m-Y', strtotime("-$day day",strtotime($row[csf('issue_date')])));
                                    $exchangeRate=$usd_arr[$previousDate];
                                    if(!is_null($exchangeRate)){break;}
                                }
                            }*/
                            //-----------------------------------------------------------------------

                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            if ($row[csf('knit_dye_source')] == 1) {
                                $knitting_party = $company_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $location_arr[$row[csf('location_id')]];
                                $caption = 'Inside';
                            } else if ($row[csf('knit_dye_source')] == 3) {
                                $knitting_party = $supplier_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $locat_arr[$row[csf('location_id')]];
                                $caption = 'Outside';
                            } else {
                                $knitting_party = "";
                                $knitting_location = '';
                                $caption = '';
                            }

                            if (in_array($row[csf('knit_dye_source')], $inside_outside_array)) {
                                $print_caption = 0;
                            } else {
                                $print_caption = 1;
                                $inside_outside_array[$i] = $row[csf('knit_dye_source')];
                            }

                            if ($print_caption == 1 && $i != 1) {
                        ?>
                                <tr class="tbl_bottom"> <!-- inhouse total -->
                                    <td colspan="10" align="right"><b>Total</b></td>
                                    <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_qnty, 2); ?></b></td>
                                    <?
                                    if ($zero_val == 0) {
                                    ?><td align="right" style="word-break: break-all;"></td>
                                        <td align="right"><b><?php echo number_format($total_amount, 2); ?></b></td><?
                                                                                                                }
                                                                                                                    ?>
                                    <td colspan="9"></td>
                                </tr>
                            <?
                                $issue_amount_grand += $issue_amount;
                                $issue_qnty_grand += $issue_qnty;
                                $return_qnty_grand += $return_qnty;
                                $total_amount_grand += $total_amount;
                                $issue_amount_return_grand += $issue_amount_return;
                                $issue_balance_qnty_grand += $issue_balance_qnty;
                                $issue_amount_qnty_grand += $issue_amount_qnty;
                                $issue_amount = 0;
                                $issue_qnty = 0;
                                $total_amount = 0;
                                $return_qnty = 0;
                                $issue_amount_return = 0;
                                $issue_balance_qnty = 0;
                                $issue_amount_qnty = 0;
                            }

                            if ($print_caption == 1) {
                            ?>
                                <tr>
                                    <td colspan="<? echo $span; ?>" style="font-size:14px;" bgcolor="#CCCCCC"><b><?php echo $caption; ?></b></td>
                                </tr>
                            <?
                            }

                            // =====start oooooo
                            ?>

                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">

                                <?
                                $buyer = '';
                                $job_no = '';
                                $styRef = '';
                                $booking_reqsn_qty = 0;
                                $knit_id = '';
                                $order_no = '';
                                $order_ref = '';
                                $order_file = '';
                                $booking_type = '';
                                $booking_type_pre = '';

                                if ($row[csf('issue_basis')] == 1) {
                                    //$booking_req_no = $row[csf('booking_no')];
                                    $reqi_wise_booking = $row[csf('booking_no')];
                                    if ($row[csf('issue_purpose')] == 1 || $row[csf('issue_purpose')] == 4) //knitting
                                    {
                                        $job_no = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['job_no'];
                                        $buyer = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['buyer_name'];
                                        $styRef = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['style_ref_no'];

                                        $order_no = $job_array[$booking_req_no]['po_number'];
                                        $order_ref = $job_array[$booking_req_no]['ref'];
                                        $order_file = $job_array[$booking_req_no]['file'];
                                        $booking_reqsn_qty = $booking_arr[$row[csf('booking_no')]]['qnty'];
                                    } else if ($row[csf('issue_purpose')] == 2) //yarn dyeing
                                    {
                                        $job_no = $job_array[$yarn_booking_array[$row[csf('booking_no')]]['job']]['job_no'];
                                        //$buyer = $job_array[$yarn_booking_array[$row[csf('booking_no')]]['job']]['buyer_name'];
                                        $buyer = $row[csf('buyer_id')];
                                        $styRef = $job_array[$yarn_booking_array[$row[csf('booking_no')]]['job']]['style_ref_no'];
                                        //$order_no = $job_array[$booking_req_no]['po_number'];
                                        $order = chop($job_array[$yarn_booking_array[$row[csf('booking_no')]]['job']]['po_number'], ',');
                                        //$booking_type = $job_array[$yarn_booking_array[$row[csf('booking_no')]]['fab_booking_no']]['booking_type'];
                                        //$booking_type = $yarn_booking_array[$row[csf('booking_no')]]['fab_booking_no'];

                                        //   $poIds=chop($order_n,',');
                                        $order_no = array_unique(explode(",", $order));
                                        $order_ref = $job_array[$booking_req_no]['ref'];
                                        $order_file = $job_array[$booking_req_no]['file'];
                                        $issue_no = $row[csf('issue_number')];

                                        if ($row[csf('buyer_job_no')] != '') {
                                            $buyerJobNo = explode("-", $row[csf('buyer_job_no')]);
                                            if ($buyerJobNo[1] == 'FSOE') {
                                                $booking_type = $job_array[$yarn_sales_booking_array[$row[csf('booking_no')]][$row[csf('buyer_job_no')]]['fab_booking_no']]['booking_type'];
                                                $ref = $job_array[$yarn_sales_booking_array[$row[csf('booking_no')]][$row[csf('buyer_job_no')]]['fab_booking_no']]['ref'];
                                            }
                                        } else {

                                            $main_chcek = sql_select("select booking_no from INV_ISSUE_MASTER where issue_number = '$issue_no'");

                                            $main_type_check = explode("-", $main_check[0]['BOOKING_NO']);
                                            if (csf($main_type_check[1]) == "FB") {
                                                $booking_type_pre = csf($main_type_check[1]);
                                            } else {

                                                $booking = sql_select("select c.BOOKING_NO, c.FAB_BOOKING_NO, c.ENTRY_FORM from INV_ISSUE_MASTER a, WO_YARN_DYEING_DTLS c where a.booking_id = c.MST_ID and a.issue_number = '$issue_no'");
                                                if ($booking[0]['ENTRY_FORM'] == 42) {
                                                    $booking_type_prefix = explode("-", $booking[0]['BOOKING_NO']);
                                                    $booking_type_pre =  $booking_type_prefix[1];
                                                } else {
                                                    $booking_type_prefix = explode("-", $booking[0]['FAB_BOOKING_NO']);
                                                    $booking_type_pre =  $booking_type_prefix[1];
                                                    // echo $booking_type_pre;
                                                }
                                                $booking_id = $booking_ids[$issue_no]['BOOKING_ID'];
                                                if ($booking_type_pre == 'SMN') {
                                                    $int_ref =  $samp_info[$booking_id]['INTERNAL_REF'];
                                                }
                                                // echo $ref;
                                                // print_r($booking_type_pre);

                                            }

                                            // $booking_type = $booking;
                                        }
                                    } else {
                                        $job_no = '';
                                        //$buyer = '';
                                        $buyer = $booking_without_array[$row[csf('booking_no')]]['buyer_id'];
                                        $styRef = '';
                                        $order_no = '';
                                        $order_ref = '';
                                        $order_file = '';
                                        $booking_reqsn_qty = '';
                                    }
                                } else if ($row[csf('issue_basis')] == 3) {
                                    $booking_req_no = $row[csf('requisition_no')];

                                    //$reqibooking_arr[$row[csf('requisition_no')]] = $row[csf('booking_no')];
                                    $reqi_wise_booking = $reqibooking_arr[$row[csf('requisition_no')]]['booking_no'];
                                    //echo $row[csf('requisition_no')].'='.$reqi_wise_booking.'<br>';

                                    $knit_id = $requisition_arr[$row[csf('requisition_no')]]['knit_id'];
                                    $job_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['job_no'];
                                    $buyer = $booking_arr[$planning_arr[$knit_id]]['buyer_id'];
                                    //$buyer = $reqibooking_arr[$row[csf('requisition_no')]]['buyer_id'];
                                    $styRef = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['style_ref_no'];

                                    //$order_no = $job_array[$booking_req_no]['po_number'];
                                    $order_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['booking_no']]['po_number'];

                                    //$order_no=$job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['po_number'];
                                    $order_ref = $job_array[$reqi_wise_booking]['ref'];
                                    $order_file = $job_array[$booking_arr[$planning_arr[$knit_id]]['booking_no']]['file'];

                                    $booking_reqsn_qty = $requisition_arr[$row[csf('requisition_no')]]['qnty'];
                                } else if ($row[csf('issue_basis')] == 8) {
                                    $reqi_wise_booking = $reqibooking_arr[$row[csf('requisition_no')]]['booking_no'];

                                    $job_no = $all_sales_info_arr[$row[csf('trans_id')]][$row[csf('product_id')]]['po_job_no'];
                                    $order_no = $all_sales_info_arr[$row[csf('trans_id')]][$row[csf('product_id')]]['job_no'];
                                    $styRef = $all_sales_info_arr[$row[csf('trans_id')]][$row[csf('product_id')]]['style_ref_no'];
                                    $pi_no = $all_rcv_info_arr[$row[csf('product_id')]]['booking_no'];
                                    $sales_booking_no = $all_sales_info_arr[$row[csf('trans_id')]][$row[csf('product_id')]]['sales_booking_no'];
                                    if ($all_sales_info_arr[$row[csf('trans_id')]][$row[csf('product_id')]]['customer_buyer'] != "") {
                                        $buyer = $all_sales_info_arr[$row[csf('trans_id')]][$row[csf('product_id')]]['customer_buyer'];
                                    } else {
                                        $buyer = $smn_info_arr[$reqi_wise_booking]['buyer_name'];
                                    }


                                    if ($smn_ref_info_arr[$reqi_wise_booking]['booking_type'] != "") {
                                        $booking_type = $smn_ref_info_arr[$reqi_wise_booking]['booking_type'];
                                    } else {
                                        $booking_type =  $job_array[$sales_booking_no]['booking_type'];
                                    }

                                    if ($smn_ref_info_arr[$reqi_wise_booking]['ref'] != "") {
                                        $ref = $smn_ref_info_arr[$reqi_wise_booking]['ref'];
                                    } else {
                                        $ref = $job_array[$sales_booking_no]['ref'];
                                    }
                                } else {
                                    $booking_req_no = "";
                                    $buyer = $row[csf('buyer_id')];
                                    $job_no = '';
                                    $styRef = '';
                                    $booking_reqsn_qty = 0;
                                    $order_no = '';
                                    $order_ref = '';
                                    $order_file = '';
                                }

                                ?>

                                <td width="30" class="wrd_brk"><? echo $i; ?></td>
                                <td width="100" class="wrd_brk">
                                    <p><? echo $issue_basis[$row[csf('issue_basis')]]; ?></p>
                                </td>
                                <td width="110" class="wrd_brk">
                                    <p><? echo $row[csf('issue_number')]; ?></p>
                                </td>
                                <td width="70" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                                <td width="60" class="wrd_brk" align="center">
                                    <p><? $yarn_count = $count_arr[$row[csf('yarn_count_id')]];
                                        echo $yarn_count; ?> &nbsp;</p>
                                </td>
                                <td width="70" class="wrd_brk">
                                    <p><? echo $brand_arr[$row[csf('brand')]]; ?></p>
                                </td>
                                <td width="100" class="wrd_brk">
                                    <p><? echo $composition[$row[csf('yarn_comp_type1st')]] . $row[csf('yarn_comp_percent1st')] . '%'; ?></p>
                                </td>
                                <td width="80" class="wrd_brk">
                                    <p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p>
                                </td>
                                <td width="90" class="wrd_brk">
                                    <p><? echo $color_arr[$row[csf('color')]]; ?></p>
                                </td>
                                <td width="70" class="wrd_brk">
                                    <p><? echo $row[csf('lot')]; ?></p>
                                </td>
                                <td width="90" align="right" class="wrd_brk">
                                    <?
                                    echo number_format($row[csf('issue_qnty')], 2);
                                    $total_iss_qnty += $row[csf('issue_qnty')];
                                    $issue_qnty += $row[csf('issue_qnty')];
                                    ?>
                                </td>
                                <?
                                if ($zero_val == 0) {
                                ?>
                                    <td width="90" align="right" class="wrd_brk">
                                        <?
                                        $avgg = $row[csf('avg_rate_per_unit')];
                                        $avg_rate = $avgg / 78;
                                        echo number_format($avg_rate, 4);
                                        ?>
                                    </td>
                                    <td width="110" align="right" class="wrd_brk">
                                        <?
                                        $amount = $row[csf('issue_qnty')] * $avg_rate;
                                        echo number_format($amount, 2);
                                        $total_amount += $amount;
                                        $grand_total_amount += $amount;
                                        ?>
                                    </td>
                                <?
                                }
                                ?>

                                <td width="100" class="wrd_brk">
                                    <p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p>
                                </td>
                                <td width="100" class="wrd_brk" align="center">
                                    <p>
                                        <?
                                        if ($booking_type_pre == 'SMN' || $booking_type_pre == 'SM') {
                                            echo "Sample";
                                        } else if (csf($booking_type_pre) == "FB") {
                                            echo "Main";
                                        } else {
                                            $booking_type = implode(",", $booking_type);
                                            echo $booking_type;
                                        }


                                        ?></p>
                                </td>
                                <td width="100" class="wrd_brk" align="center"><? echo $buyer_arr[$buyer]; ?></td>
                                <td width="110" class="wrd_brk" align="center" title="<? echo $row[csf('requisition_no')]; ?>">
                                    <?
                                    if ($booking_type_pre == 'SMN') {
                                        echo $int_ref;
                                    } else {
                                        $ref = implode(",", $ref);
                                        echo $ref;
                                    }


                                    ?>
                                </td>

                                <td width="130" class="wrd_brk">
                                    <p><? echo $knitting_party; ?></p>
                                </td>
                                <td width="130" class="wrd_brk">
                                    <p><? echo $knitting_location; ?></p>
                                </td>
                                <td width="100" class="wrd_brk">
                                    <p><? echo $store_arr[$row[csf('store_id')]]; ?>
                                </td>
                                <td width="100" class="wrd_brk" align="right"><? echo $pi_no; ?></td>
                                <td class="wrd_brk">
                                    <p><? //echo $row[csf('remarks')]; 
                                        ?>
                                </td>
                            </tr>
                        <?
                            $i++;
                        }
                        //===============end

                        if (count($result) > 0) {
                        ?>
                            <tr class="tbl_bottom">
                                <td colspan="10" align="right"><b>Total</b></td>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_qnty, 2); ?></b></td>

                                <?
                                if ($zero_val == 0) {
                                ?>
                                    <td align="right" style="word-break: break-all;"></td>
                                    <td align="right"><b><?php echo number_format($total_amount, 2); ?></b></td>
                                <?
                                }
                                ?>
                                <td colspan="9"></td>
                            </tr>
                        <?

                            $issue_qnty_grand += $issue_qnty;
                            $issue_amount_grand += $issue_amount;
                            $return_qnty_grand += $return_qnty;
                            $total_amount_grand += $total_amount;
                            $issue_amount_return_grand += $issue_amount_return;
                            $issue_balance_qnty_grand += $issue_balance_qnty;
                            $issue_amount_qnty_grand += $issue_amount_qnty;
                        }
                        ?>
                        <tr style="background-color: grey;">
                            <td colspan="10" align="right"><b>Inside + Outside Grand Total</b></td>
                            <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_qnty_grand, 2); ?></b></td>

                            <?
                            if ($zero_val == 0) {
                            ?>
                                <td align="right" style="word-break: break-all;"></td>
                                <td align="right"><b><?php echo number_format($total_amount_grand, 2); ?></b></td>
                            <?
                            }
                            ?>
                            <td colspan="9"></td>
                        </tr>

                        <?
                        $k = 1;
                        $issue_amount = 0;
                        $issue_qnty = 0;
                        $knitting_party = '';
                        $total_amount = 0;
                        $return_qnty = 0;
                        $query = "SELECT a.issue_number, a.issue_basis, a.issue_purpose, a.booking_id, a.booking_no, a.buyer_id, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no, a.other_party, b.requisition_no, b.store_id, b.brand_id, b.cons_quantity as issue_qnty, b.return_qnty, b.cons_rate, b.cons_amount, c.brand, c.yarn_count_id, c.yarn_type, c.lot, c.color, c.avg_rate_per_unit, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.id as product_id from inv_issue_master a, inv_transaction b, product_details_master c where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.issue_basis in (1,2) and a.issue_purpose in (7,8,12,15,38,46,50,51) and a.issue_date between '$from_date' and '$to_date' and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lot_no $yarn_type_cond $yarn_count_cond $issue_purpose_cond $using_item_cond $booking_req_cond order by a.issue_purpose, a.issue_date";
                        //}
                        //echo $query;
                        $nameArray = sql_select($query);

                        //for library dtls
                        $lib_arr = array();
                        foreach ($nameArray as $row) {
                            $lib_arr['brand'][$row[csf('brand')]] = $row[csf('brand')];
                            $lib_arr['yarn_count'][$row[csf('yarn_count_id')]] = $row[csf('yarn_count_id')];
                            $lib_arr['color'][$row[csf('color')]] = $row[csf('color')];

                            $yt_issue_numbers .= "'" . $row[csf("issue_number")] . "',";
                        }

                        $yt_issue_numbers = chop($yt_issue_numbers, ",");

                        $yt_issue_numbers = implode(",", array_filter(array_unique(explode(",", $yt_issue_numbers))));

                        if ($yt_issue_numbers != "") {
                            $yt_issue_numbers = explode(",", $yt_issue_numbers);
                            $issue_numbers_chnk = array_chunk($yt_issue_numbers, 999);
                            $yt_issue_no_cond = " and";
                            foreach ($issue_numbers_chnk as $dtls_id) {
                                if ($yt_issue_no_cond == " and")  $yt_issue_no_cond .= "(c.issue_number in(" . implode(',', $dtls_id) . ")";
                                else $yt_issue_no_cond .= " or c.issue_number in(" . implode(',', $dtls_id) . ")";
                            }
                            $yt_issue_no_cond .= ")";
                            //echo $issue_no_cond;die;

                            //   echo "SELECT a.recv_number,a.booking_no, b.cons_quantity, c.issue_number,b.prod_id, b.id as trans_id from inv_receive_master a, inv_transaction b, inv_issue_master c where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 $yt_issue_no_cond";

                            $yt_issue_return_res = sql_select("SELECT a.recv_number,a.booking_no, b.cons_quantity, c.issue_number,b.prod_id, b.id as trans_id from inv_receive_master a, inv_transaction b, inv_issue_master c where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 $yt_issue_no_cond");
                        }

                        $yt_transIdChk = array();
                        foreach ($yt_issue_return_res as $val) {
                            if ($yt_transIdChk[$val[csf("trans_id")]] == "") {
                                $yt_transIdChk[$val[csf("trans_id")]] = $val[csf("trans_id")];
                                $yt_issue_return_qnty_arr[$val[csf("issue_number")]][$val[csf("prod_id")]] += $val[csf("cons_quantity")];
                            }
                        }
                        //var_dump($yt_issue_return_qnty_arr);

                        $color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0 and id in(" . implode(',', $lib_arr['color']) . ")", "id", "color_name");
                        $count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 and id in(" . implode(',', $lib_arr['yarn_count']) . ")", 'id', 'yarn_count');
                        $brand_arr = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0 and id in(" . implode(',', $lib_arr['brand']) . ")", 'id', 'brand_name');
                        //end for library dtls

                        $grnd_purpose_issue_qnty = 0;
                        $grnd_purpose_issue_amount = 0;
                        $grnd_purpose_total_amount = 0;
                        $grnd_purpose_return_qnty = 0;
                        $grnd_purpose_issue_return_qnty = 0;
                        $grnd_purpose_issue_balance_qnty = 0;
                        $grnd_purpose_issue_amount_qnty = 0;

                        foreach ($nameArray as $row) {
                            $exchangeRate = $usd_arr[date('d-m-Y', strtotime($row[csf('issue_date')]))];
                            if ($exchangeRate == "") {
                                foreach ($usd_arr as $rate_date => $rat) {
                                    if (strtotime($rate_date) <= strtotime($row[csf('issue_date')])) {
                                        $rate_date = date('d-m-Y', strtotime($rate_date));
                                        $exchangeRate = $rat;
                                        break;
                                    }
                                }
                            }

                            //----------------------------------------------------------------------
                            /*$exchangeRate=$usd_arr[date('d-m-Y',strtotime($row[csf('issue_date')]))];
                            if(is_null($exchangeRate)){
                                for($day=1;$day<366;$day++){
                                    $previousDate=date('d-m-Y', strtotime("-$day day",strtotime($row[csf('issue_date')])));
                                    $exchangeRate=$usd_arr[$previousDate];
                                    if(!is_null($exchangeRate)){break;}
                                }
                            }*/
                            //-----------------------------------------------------------------------

                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            if ($row[csf('knit_dye_source')] == 1) {
                                $knitting_party = $company_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $location_arr[$row[csf('location_id')]];
                            } else if ($row[csf('knit_dye_source')] == 3) {
                                $knitting_party = $supplier_arr[$row[csf('knit_dye_company')]];
                                $knitting_location = $locat_arr[$row[csf('location_id')]];
                            } else {
                                $knitting_party = "";
                                $knitting_location = '';
                            }
                            if (in_array($row[csf('issue_purpose')], $issue_purpose_array)) {
                                $print_caption = 0;
                            } else {
                                $print_caption = 1;
                                $issue_purpose_array[$i] = $row[csf('issue_purpose')];
                            }


                            if ($print_caption == 1 && $k != 1) {
                        ?>
                                <tr class="tbl_bottom">
                                    <td colspan="10" align="right"><b>Total</b></td>
                                    <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_qnty, 2); ?></b></td>

                                    <?
                                    if ($zero_val == 0) {
                                    ?>
                                        <td align="right" style="word-break: break-all;"></td>
                                        <td align="right"><b><?php echo number_format($total_amount, 2); ?></b></td>
                                    <?
                                    }
                                    ?>

                                    <td colspan="9"></td>
                                </tr>
                            <?
                                $issue_qnty = 0;
                                $issue_amount = 0;
                                $total_amount = 0;
                                $return_qnty = 0;
                                $total_issue_return = 0;
                                $total_issue_balance_qnty = 0;
                            }
                            if ($print_caption == 1) {
                            ?>
                                <tr>
                                    <td colspan="<? echo $span; ?>" style="font-size:14px;" bgcolor="#CCCCCC"><b><?php echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></b></td>
                                </tr>
                            <?
                            }
                            ?>

                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">

                                <?
                                $buyer = '';
                                $job_no = '';
                                $styRef = '';
                                $booking_reqsn_qty = 0;
                                $knit_id = '';
                                $order_no = '';
                                $order_ref = '';
                                $order_file = '';
                                if ($row[csf('issue_basis')] == 1) {
                                    $booking_req_no = $row[csf('booking_no')];

                                    if ($row[csf('issue_purpose')] == 8) {
                                        $buyer = $booking_without_array[$row[csf('booking_no')]]['buyer_id'];
                                        $booking_reqsn_qty = $booking_without_array[$row[csf('booking_no')]]['qnty'];
                                        $job_no = '';
                                        $styRef = '';
                                        $order_no = "";
                                        $order_ref = "";
                                        $order_file = "";
                                    } else {
                                        $job_no = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['job_no'];
                                        $buyer = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['buyer_name'];
                                        $styRef = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['style_ref_no'];
                                        $order_no = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['po_number'];
                                        $order_ref = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['ref'];
                                        $order_file = $job_array[$booking_arr[$row[csf('booking_no')]]['job']]['file'];
                                        $booking_reqsn_qty = $booking_arr[$row[csf('booking_no')]]['qnty'];
                                    }
                                } else if ($row[csf('issue_basis')] == 3) {
                                    $booking_req_no = $row[csf('requisition_no')];
                                    $reqi_wise_booking = $reqibooking_arr[$row[csf('requisition_no')]]['booking_no'];
                                    $knit_id = $requisition_arr[$row[csf('requisition_no')]]['knit_id'];
                                    $job_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['job_no'];
                                    $buyer = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['buyer_name'];
                                    $styRef = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['style_ref_no'];
                                    $order_no = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['po_number'];
                                    $order_ref = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['ref'];
                                    $order_file = $job_array[$booking_arr[$planning_arr[$knit_id]]['job']]['file'];
                                    $booking_reqsn_qty = $requisition_arr[$row[csf('requisition_no')]]['qnty'];
                                } else {
                                    $booking_req_no = "";
                                    $buyer = $row[csf('buyer_id')];
                                    $job_no = '';
                                    $styRef = '';
                                    $order_no = '';
                                    $order_ref = '';
                                    $order_file = '';
                                    $booking_reqsn_qty = 0;
                                }

                                if ($row[csf('issue_purpose')] == 5) {
                                    $knitting_party = $other_party_arr[$row[csf('other_party')]];
                                } else if ($row[csf('issue_purpose')] == 3) {
                                    $knitting_party = $buyer_arr[$row[csf('buyer_id')]];
                                    $buyer = '';
                                }
                                ?>

                                <td width="30"><? echo $i; ?></td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $issue_basis[$row[csf('issue_basis')]]; ?></p>
                                </td>
                                <td width="110" style="word-break: break-all;">
                                    <p><? echo $row[csf('issue_number')]; ?></p>
                                </td>
                                <td width="70" style="word-break: break-all;" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                                <td width="60" style="word-break: break-all;" align="center">
                                    <p><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p>
                                </td>
                                <td width="70" style="word-break: break-all;">
                                    <p><? echo $brand_arr[$row[csf('brand')]]; ?></p>
                                </td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $composition[$row[csf('yarn_comp_type1st')]] . $row[csf('yarn_comp_percent1st')] . '%'; ?></p>
                                </td>
                                <td width="80" style="word-break: break-all;">
                                    <p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p>
                                </td>
                                <td width="90" style="word-break: break-all;">
                                    <p><? echo $color_arr[$row[csf('color')]]; ?></p>
                                </td>
                                <td width="70" style="word-break: break-all;">
                                    <p><? echo $row[csf('lot')]; ?></p>
                                </td>
                                <td width="90" style="word-break: break-all;" align="right">
                                    <?
                                    echo number_format($row[csf('issue_qnty')], 2);
                                    $total_iss_qnty += $row[csf('issue_qnty')];
                                    $issue_qnty += $row[csf('issue_qnty')];
                                    ?>
                                </td>
                                <?
                                if ($zero_val == 0) {
                                ?>
                                    <td width="90" align="right" style="word-break: break-all;">
                                        <?
                                        $avgg = $row[csf('avg_rate_per_unit')];
                                        $avg_rate = $avgg / 78;
                                        echo number_format($avg_rate, 4);
                                        ?>
                                    </td>
                                    <td width="110" align="right" style="word-break: break-all;">
                                        <?
                                        $amount = $row[csf('issue_qnty')] * $avg_rate;
                                        echo number_format($amount, 2);
                                        $total_amount += $amount;
                                        $grand_total_amount += $amount;

                                        ?>
                                    </td>
                                <?
                                }
                                ?>

                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p>
                                </td>
                                <td width="100" style="word-break: break-all;" align="center">
                                    <p>
                                        <?
                                        $booking_type = implode(",", $booking_type);
                                        if ($row[csf('issue_purpose')] == 8) {
                                            $booking_type = "Sample";
                                        };
                                        echo $booking_type;
                                        ?>
                                    </p>
                                </td>

                                <td width="110" style="word-break: break-all;" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]] ?></td>
                                <td width="100" style="word-break: break-all;" align="right">
                                    <?
                                    $ref = implode(",", $ref);
                                    echo $ref;

                                    ?>
                                </td>

                                <td width="130" style="word-break: break-all;">
                                    <p><? echo $knitting_party; ?></p>
                                </td>
                                <td width="130" style="word-break: break-all;">
                                    <p><? echo $knitting_location; ?></p>
                                </td>
                                <td width="100" style="word-break: break-all;">
                                    <p><? echo $store_arr[$row[csf('store_id')]]; ?>
                                </td>
                                <td width="100" style="word-break: break-all;" align="right"><? echo $pi_no; ?></td>
                                <td style="word-break: break-all;">
                                    <p><? echo $row[csf('remarks')]; ?>
                                </td>
                            </tr>

                        <?
                            $k++;
                            $i++;
                            $grnd_purpose_issue_qnty += $row[csf('issue_qnty')];
                            $grnd_purpose_issue_amount += $row[csf('cons_amount')] / $exchangeRate;
                            $grnd_purpose_total_amount += $amount;
                            $grnd_purpose_return_qnty += $row[csf('return_qnty')];
                            $grnd_purpose_issue_return_qnty += $yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                            $grnd_purpose_issue_balance_qnty += $row[csf('issue_qnty')] - $yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]];
                            $grnd_purpose_issue_amount_qnty += ($row[csf('cons_rate')] / $exchangeRate) * ($row[csf('issue_qnty')] - $yt_issue_return_qnty_arr[$row[csf("issue_number")]][$row[csf("product_id")]]);
                        }

                        if (count($nameArray) > 0) {
                        ?>
                            <tr class="tbl_bottom">
                                <td colspan="10" align="right"><b>Total</b></td>
                                <td align="right" style="word-break: break-all;"><b><?php echo number_format($issue_qnty, 2); ?></b></td>

                                <?
                                if ($zero_val == 0) {
                                ?>
                                    <td align="right" style="word-break: break-all;"></td>
                                    <td align="right"><b><?php echo number_format($total_amount, 2); ?></b></td>
                                <?
                                }
                                ?>

                                <td colspan="9"></td>
                            </tr>
                        <?
                        }
                        ?>
                        <tr>
                            <th colspan="10" align="right">Issue Purpose Wise Grand Total</th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_qnty, 2); ?></th>

                            <?
                            if ($zero_val == 0) {
                            ?>
                                <th></th>
                                <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_total_amount, 2); ?></th>
                            <?
                            }
                            ?>

                            <th colspan="9" align="right"></th>
                        </tr>
                    </tbody>

                    <tfoot style="background-color: grey;">
                        <tr>
                            <th colspan="10" align="right"> Grand Total</th>
                            <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_issue_qnty + $issue_qnty_grand, 2); ?></th>

                            <?
                            if ($zero_val == 0) {
                            ?>
                                <th></th>
                                <th align="right" style="word-break: break-all;"><?php echo number_format($grnd_purpose_total_amount + $total_amount_grand, 2); ?></th>
                            <?
                            }
                            ?>
                            <th colspan="9" align="right"></th>
                        </tr>
                    </tfoot>

                </table>
            </div>
        </fieldset>
    <?
    }

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

if ($action == "report_generate_party_wise") {
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    $cbo_store_name = str_replace("'", "", $cbo_store_name);
    $cbo_buyer_id = str_replace("'", "", $cbo_buyer_id);
    $txt_booking_no = str_replace("'", "", $txt_booking_no);
    $cbo_display_type = str_replace("'", "", $cbo_display_type);
    $cbo_yarn_type = str_replace("'", "", $cbo_yarn_type);
    $cbo_yarn_count = str_replace("'", "", $cbo_yarn_count);
    $txt_lot_no = str_replace("'", "", trim($txt_lot_no));
    $cbo_issue_purpose = str_replace("'", "", $cbo_issue_purpose);
    $cbo_using_item = str_replace("'", "", $cbo_using_item);
    $txt_date_to = str_replace("'", "", $txt_date_to);
    $txt_date_from = str_replace("'", "", $txt_date_from);


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

    if ($cbo_display_type > 0) {
        $sql_cond .= " and c.knit_dye_source in(" . $cbo_display_type . ")";
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
                    <td colspan="13" align="center" style="border:none; padding: 3px 2px;x">
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