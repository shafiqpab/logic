<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");

require_once('../../../includes/common.php');

$user_name = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_buyer") {
    echo create_drop_down("cbo_buyer_id", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "- All Buyer -", $selected, "");
    exit();
}

if($action=="company_wise_report_button_setting")
{
    extract($_REQUEST);


    $print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=4 and report_id=168 and is_deleted=0 and status_active=1");

    //echo $print_report_format;

    $print_report_format_arr=explode(",",$print_report_format);
    echo "$('#Print2').hide();\n";
    echo "$('#Print11').hide();\n";
    echo "$('#Print12').hide();\n";
    echo "$('#RequisitionPrint').hide();\n";
    echo "$('#RequisitionPrint3').hide();\n";
    echo "$('#KnittingCard').hide();\n";
    echo "$('#Knitting_Car_9').hide();\n";
    echo "$('#Knitting_Car_10').hide();\n";
    echo "$('#Knitting_Card_11').hide();\n";
    echo "$('#RequisitionPrint4').hide();\n";

    if($print_report_format != "")
    {
        foreach($print_report_format_arr as $id)
        {
            if($id==131){echo "$('#Print2').show();\n";}
            if($id==353){echo "$('#Print11').show();\n";}
            if($id==572){echo "$('#Print12').show();\n";}
            if($id==130){echo "$('#RequisitionPrint').show();\n";}
            if($id==133){echo "$('#KnittingCard').show();\n";}
            if($id==356){echo "$('#Knitting_Car_9').show();\n";}
            if($id==132){echo "$('#RequisitionPrint3').show();\n";}
            if($id==424){echo "$('#Knitting_Car_10').show();\n";}
            if($id==503){echo "$('#Knitting_Card_11').show();\n";}
            if($id==807){echo "$('#RequisitionPrint4').show();\n";}


        }
    }
    else
    {
        echo "$('#Print2').show();\n";
        echo "$('#Print11').show();\n";
        echo "$('#Print12').show();\n";
        echo "$('#RequisitionPrint').show();\n";
        echo "$('#RequisitionPrint3').show();\n";
        echo "$('#KnittingCard').show();\n";
        echo "$('#Knitting_Car_9').show();\n";
        echo "$('#Knitting_Car_10').show();\n";
        echo "$('#Knitting_Card_11').show();\n";
        echo "$('#RequisitionPrint4').show();\n";
    }
    exit();
}

if ($action == "load_drop_down_party_type") {
    $explode_data = explode("**", $data);
    $data = $explode_data[0];
    $selected_company = $explode_data[1];
    //print_r ($selected_company);
    if ($data == 3) {
        echo create_drop_down("cbo_party_type", 110, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$selected_company' and b.party_type =20 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "--- Select ---", $selected, "");
    } else if ($data == 1) {
        echo create_drop_down("cbo_party_type", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--- Select ---", $selected_company, "", 0, 0);
    } else {
        echo create_drop_down("cbo_party_type", 110, $blank_array, "", 1, "--- Select ---", $selected, "", 1);
    }
    exit();
}

$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");

if ($action == "job_no_search_popup") {
    echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>

    <script>

        var selected_id = new Array;
        var selected_name = new Array;

        /* function check_all_data()
         {
         var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
         tbl_row_count = tbl_row_count - 1;

         for (var i = 1; i <= tbl_row_count; i++)
         {
         $('#tr_' + i).trigger('click');
         }
         }

         function toggle(x, origColor) {
         var newColor = 'yellow';
         if (x.style) {
         x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
         }
         }

         function js_set_value_job(str) {

         if (str != "")
         str = str.split("_");

         toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

         if (jQuery.inArray(str[1], selected_id) == -1) {
         selected_id.push(str[1]);
         selected_name.push(str[2]);

         } else {
         for (var i = 0; i < selected_id.length; i++) {
         if (selected_id[i] == str[1])
         break;
         }
         selected_id.splice(i, 1);
         selected_name.splice(i, 1);
         }
         var id = '';
         var name = '';
         for (var i = 0; i < selected_id.length; i++) {
         id += selected_id[i] + ',';
         name += selected_name[i] + '*';
         }

         id = id.substr(0, id.length - 1);
         name = name.substr(0, name.length - 1);

         $('#hide_job_no').val(id);
         $('#hide_job_id').val(name);
     }*/


     function js_set_value_job(str) {
            //alert(str);
            $('#hide_job_no').val(str);
            parent.emailwindow.hide();
        }
    </script>

</head>

<body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
            <fieldset style="width:780px;">
                <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
                class="rpt_table" id="tbl_list">
                <thead>
                    <th>PO Company</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Sales No</th>
                    <th>Booking Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value=""/>
                    <input type="hidden" name="hide_job_id" id="hide_job_id" value=""/>
                </thead>
                <tbody>
                    <tr>
                        <td align="center">
                           <?

                           echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.company_name from  lib_company buy where buy.status_active =1 and buy.is_deleted=0   order by buy.company_name", "id,company_name", 1, "-- All--", 0, "", 0);
                           ?>
                       </td>
                       <td align="center">
                           <?
                           $search_by_arr = array(1 => "Sales No", 2 => "Style Ref", 3 => "Booking No");
                           $dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
                           echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
                           ?>
                       </td>
                       <td align="center" id="search_by_td">
                        <input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
                        id="txt_search_common"/>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
                        style="width:70px" readonly>To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
                        readonly>
                    </td>
                    <td align="center">
                        <input type="button" name="button" class="formbutton" value="Show"
                        onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value, 'create_job_no_search_list_view', 'search_div', 'knitting_status_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
                        style="width:100px;"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </tbody>
        </table>
        <div style="margin-top:15px" id="search_div"></div>
    </fieldset>
</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_job_no_search_list_view") {
    $data = explode('**', $data);
    $company_id = $data[0];

    if ($data[1] == 0) {
        if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
            if ($_SESSION['logic_erp']["buyer_id"] != "")
                $buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
            else
                $buyer_id_cond = "";
        } else {
            $buyer_id_cond = "";
        }
    } else {
        $buyer_id_cond = " and a.buyer_id=$data[1]";
    }

    $search_by = $data[2];
    $search_string = "%" . trim($data[3]) . "%";

    if ($search_by == 1)
        $search_field = "a.job_no";
    else if ($search_by == 2)
        $search_field = "a.style_ref_no";
    else
        $search_field = "a.sales_booking_no";

    $start_date = $data[4];
    $end_date = $data[5];

    if ($start_date != "" && $end_date != "") {
        if ($db_type == 0) {
            $date_cond = "and a.booking_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
        } else {
            $date_cond = "and a.booking_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
        }
    } else {
        $date_cond = "";
    }

    $arr = array(0 => $company_library, 1 => $company_library);
    if ($db_type == 0)
        $year_field = "YEAR(a.insert_date) as year";
    else if ($db_type == 2)
        $year_field = "to_char(a.insert_date,'YYYY') as year";
    else
        $year_field = ""; //defined Later

    $sql = "select a.id, a.job_no, $year_field, a.company_id, a.buyer_id, a.style_ref_no,a.booking_date,a.sales_booking_no from  fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0  and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by a.id, a.booking_date";
    // echo $sql;   die;
    echo create_list_view("tbl_list_search", "Company,Buyer/Unit,Year,Sales No,Style Ref., Booking No, Booking Date", "120,120,50,110,120,120,80", "800", "220", 0, $sql, "js_set_value_job", "id,job_no", "", 1, "company_id,buyer_id,0,0,0,0,0", $arr, "company_id,buyer_id,year,job_no,style_ref_no,sales_booking_no,booking_date", "", '', '0,0,0,0,0,0,3', '');
    exit();
}

if ($action == "machine_no_search_popup")
{
    echo load_html_head_contents("Machine Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    <script>
        function js_set_value(str) {
            $('#hide_machine').val(str);
            parent.emailwindow.hide();
        }
    </script>
    <input type="hidden" id="hide_machine" name="hide_machine">
    <?
    $sql = "select id,machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0";
    echo create_list_view("tbl_machine", "Machine No", "200", "240", "250", 0, $sql, "js_set_value", "id,machine_no", "", 1, "0", $arr, "machine_no", "", "setFilterGrid('tbl_machine',-1);", '0', "", "");
    exit();
}

if ($action == "booking_no_search_popup") {
    echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    <script>
        function js_set_value(str) {

            var booking_no = str.split("_");
            //alert(str);
            $('#hide_booking_no').val(str);
            parent.emailwindow.hide();
        }

        function show_data_list()
        {
            if($("#txt_search_common").val() =="" && $("#cbo_buyer_name").val() ==0 )
            {
                if($("#txt_date_from").val() =="" && $("#txt_date_to").val() =="" )
                {
                    alert("Please select any reference");
                    return;
                }

            }
            show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+ '**' + document.getElementById('cbo_year_selection').value + '**' + document.getElementById('cbo_within_group').value, 'create_order_no_search_list_view', 'search_div', 'knitting_status_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
        }
 </script>

</head>

<body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
            <fieldset style="width:780px;">
                <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
                class="rpt_table" id="tbl_list">
                <thead>
                    <th>Within Group</th>
                    <th> PO Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Search</th>
                    <th>Booking Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
                    <input type="hidden" name="hide_booking_no" id="hide_booking_no" value=""/>

                </thead>
                <tbody>
                    <tr>
                        <td align="center">
                           <?
                                echo create_drop_down("cbo_within_group", 60, $yes_no, "", 0, "", 1, '', 0);
                           ?>
                       </td>
                        <td align="center">
                           <?
                           echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", 0, "", 0);
                           ?>
                       </td>
                       <td align="center">
                           <?
                           $search_by_arr = array(4 => "Booking No", 3 => "Job No", 1 => "Order No", 2 => "Style Ref");
                           $dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
                           echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", 4, "", $dd, 0);
                           ?>
                       </td>
                       <td align="center" id="search_by_td">
                        <input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
                        id="txt_search_common"/>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
                        style="width:55px" readonly>To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"
                        readonly>
                    </td>
                    <td align="center">
                        <input type="button" name="button" class="formbutton" value="Show"
                        onClick="show_data_list();"
                        style="width:100px;"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </tbody>
        </table>
        <div style="margin-top:15px" id="search_div"></div>
    </fieldset>
</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_order_no_search_list_view") {
    $data = explode('**', $data);
    $company_id = $data[0];
    $year_id = $data[6];
    $within_group = $data[7];

    if ($data[1] == 0) {
        if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
            if ($_SESSION['logic_erp']["buyer_id"] != "")
                $buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
            else
                $buyer_id_cond = "";
        } else {
            $buyer_id_cond = "";
        }
    } else {
        $buyer_id_cond = " and a.buyer_id=$data[1]";
    }


    $search_by = $data[2];
    $search_string = "%" . trim($data[3]) . "%";

    if ($db_type == 2) {
        $year_field_con = " and to_char(d.insert_date,'YYYY')";
        $year_field_con2 = " and to_char(a.insert_date,'YYYY')";

        if ($year_id != 0) $year_cond = "$year_field_con=$year_id"; else $year_cond = "";
        if ($year_id != 0) $year_cond2 = "$year_field_con2=$year_id"; else $year_cond2 = "";
    } else {
        if ($year_id != 0) $year_cond = "and year(d.insert_date) =$year_id"; else $year_cond = "";
        if ($year_id != 0) $year_cond2 = "and year(a.insert_date) =$year_id"; else $year_cond2 = "";

    }
    if ($search_by == 1)
        $search_field = "c.po_number";
    else if ($search_by == 2)
        $search_field = "d.style_ref_no";
    else if ($search_by == 3)
        $search_field = "a.job_no";
    else
        $search_field = "a.booking_no";

    $start_date = $data[4];
    $end_date = $data[5];

    if ($start_date != "" && $end_date != "") {
        if ($db_type == 0) {
            $date_cond = "and a.booking_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
        } else {
            $date_cond = "and a.booking_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
        }
    } else {
        $date_cond = "";
    }

    $arr = array(0 => $company_library, 1 => $buyer_arr);

    if ($db_type == 0)
        $year_field = "YEAR(a.insert_date) as year";
    else if ($db_type == 2)
        $year_field = "to_char(a.insert_date,'YYYY') as year";
    else
        $year_field = ""; //defined Later


    if($within_group==1){
        $sql = "SELECT a.id,a.booking_no, d.job_no, $year_field,a.company_id,a.buyer_id,a.booking_date,c.po_number,d.style_ref_no from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d, fabric_sales_order_mst e where  c.job_no_mst=d.job_no and b.po_break_down_id=c.id and b.job_no=d.job_no and a.booking_no=b.booking_no and a.booking_no=e.sales_booking_no and e.within_group=1 and a.status_active=1 and a.is_deleted=0 and e.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $year_cond group by a.id,a.booking_no, d.job_no,a.company_id,a.buyer_id,a.insert_date,a.booking_date,c.po_number,d.style_ref_no
        order by a.booking_no, a.booking_date";
    }
    else
    {
        $sql ="SELECT null as id,a.sales_booking_no as booking_no, null as job_no, to_char(a.insert_date,'YYYY') as year,a.company_id,a.buyer_id, a.booking_date,null as po_number,a.style_ref_no from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  and a.within_group=2 and a.sales_booking_no like '$search_string'  $buyer_id_cond $date_cond $year_cond2 group by a.sales_booking_no, a.insert_date, a.company_id,a.buyer_id, a.booking_date, a.style_ref_no order by a.sales_booking_no, a.booking_date";
    }

    echo create_list_view("tbl_list_search", "Company,Buyer Name,Booking No,Job No,Style,PO No,Booking Date", "120,100,100,100,100,100,100", "760", "220", 0, $sql, "js_set_value", "booking_no", "", 1, "company_id,buyer_id,0,0,0,0,0", $arr, "company_id,buyer_id,booking_no,job_no,style_ref_no,po_number,booking_date", "", '', '0,0,0,0,0,0,3', '', 1);
    exit();
}

//for req qty
if ($action == "req_qty_popup")
{
    echo load_html_head_contents("Rquisition Details", "../../../", 1, 1, $unicode, '', '');
    extract($_REQUEST);

    $sql = "SELECT A.KNIT_ID, A.REQUISITION_NO, A.YARN_QNTY FROM PPL_YARN_REQUISITION_ENTRY A WHERE  A.ID IN(".$req_id.")"; //A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND
    //echo $sql;
    $sql_rslt = sql_select($sql);
    $req_data = array();
    foreach($sql_rslt as $row)
    {
        $req_data[$row['KNIT_ID']][$row['REQUISITION_NO']]['QTY'] += $row['YARN_QNTY'];
    }
    ?>
    <fieldset style="width:300px">
        <table width="300" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <th width="30">Sl</th>
                <th width="80">Program No</th>
                <th width="80">Requisition No</th>
                <th>Requisition Qty</th>
            </thead>
        </table>
        <div style="width:300px; overflow-y:scroll; max-height:300px" id="scroll_body">
            <table width="280" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                <tbody>
                <?
                if(empty($req_data))
                {
                    echo get_empty_data_msg();
                }
                $sl = 0;
                foreach($req_data as $prg_no=>$prg_arr)
                {
                    foreach($prg_arr as $rq_no=>$row)
                    {
                        $sl++;
                        ?>
                        <tr>
                            <td width="30" align="center"><? echo $sl;?></td>
                            <td width="80" align="center" ><? echo $prg_no;?></td>
                            <td width="80" align="center"><? echo $rq_no;?></td>
                            <td align="right"><? echo number_format($row['QTY'], 2); ?></td>
                        </tr>
                        <?
                        $tot_qnty += $row['QTY'];
                    }
                }
                ?>
                </tbody>
                <tfoot>
                    <th colspan="3" align="right">Total&nbsp;</th>
                    <th><? echo number_format($tot_qnty, 2); ?></th>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    exit();
}

//for Issue qty
if ($action == "issue_qty_popup")
{
    echo load_html_head_contents("Issue Details", "../../../", 1, 1, $unicode, '', '');
    extract($_REQUEST);

    $sql="SELECT b.id as propotion_id,a.issue_number, a.issue_date, a.challan_no, a.booking_no, a.knit_dye_source, b.quantity AS issue_qnty, c.lot, c.id AS prod_id, c.dyed_type, c.product_name_details, c.avg_rate_per_unit, d.id AS trans_id, d.cons_rate, f.dye_charge,d.requisition_no,a.challan_no,a.store_id FROM inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d, inv_mrr_wise_issue_details e left join inv_transaction f on f.id=e.recv_trans_id and f.prod_id = e.prod_id WHERE     a.id = d.mst_id AND d.id = b.trans_id AND b.prod_id = c.id AND d.id=e.issue_trans_id AND d.transaction_type = 2 AND d.item_category = 1 AND c.item_category_id = 1 AND b.trans_type = 2 AND b.entry_form = 3 AND d.requisition_no IN(".$req_no.") AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND b.issue_purpose != 2 GROUP BY a.id, b.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.booking_no, a.knit_dye_source, c.lot, c.dyed_type, c.product_name_details, c.avg_rate_per_unit, d.id, d.cons_rate, f.dye_charge,b.quantity,d.requisition_no,a.challan_no,a.store_id";

    //echo $sql;
    $sql_rslt = sql_select($sql);

    $all_req_arr = array();
    foreach($sql_rslt as $row)
    {
        array_push($all_req_arr,$row[csf('requisition_no')]);
    }

    $demand_no = array();
    $req_sql_zs = sql_select("select a.demand_system_no, b.requisition_no from ppl_yarn_demand_entry_mst a, ppl_yarn_demand_entry_dtls b where a.id = b.mst_id ".where_con_using_array($all_req_arr,0,'b.requisition_no')." ");
    foreach($req_sql_zs as $row)
    {
        $demand_no[$row[csf('requisition_no')]]['demand_system_no'] = $row[csf('demand_system_no')];
    }

    $rslt_program = sql_select("select a.knit_id, a.requisition_no from ppl_yarn_requisition_entry a where a.status_active = 1 ".where_con_using_array($all_req_arr,0,'a.requisition_no')." ");
    foreach($rslt_program as $row)
    {
        $program_no_arr[$row[csf('requisition_no')]]['knit_id'] = $row[csf('knit_id')];
    }


    $store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');

    ?>
    <fieldset style="width:730px">
        <table width="750" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <th width="30">Sl</th>
                <th width="100">Issue No</th>
                <th width="80">Program No</th>
                <th width="80">Requsition No</th>
                <th width="120">Demend No</th>
                <th width="80">Date</th>
                <th width="80">Challan No</th>
                <th width="80">Issue Qnty</th>
                <th>Store</th>
            </thead>
        </table>
        <div style="width:770px; overflow-y:scroll; max-height:300px" id="scroll_body">
            <table width="750" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                <tbody>
                <?
                if(empty($sql_rslt))
                {
                    echo get_empty_data_msg();
                }
                $sl = 0;
                $tot_qnty = 0;
                foreach($sql_rslt as $rq_no=>$row)
                {
                    $sl++;
                    ?>
                    <tr>
                        <td width="30" align="center"><? echo $sl;?></td>
                        <td width="100" align="center" ><? echo $row[csf('issue_number')];?></td>
                        <td width="80" align="center"><? echo $program_no_arr[$row[csf('requisition_no')]]['knit_id'];?></td>
                        <td width="80" align="center"><? echo $row[csf('requisition_no')];?></td>
                        <td width="120" align="center"><? echo $demand_no[$row[csf('requisition_no')]]['demand_system_no']; ?></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]);?></td>
                        <td width="80" align="center"><? echo $row[csf('challan_no')];?></td>
                        <td width="80" align="right"><? echo $row[csf('issue_qnty')];?></td>
                        <td align="center"><?php echo $store_arr[$row[csf("store_id")]]; ?></td>
                    </tr>
                    <?
                    $tot_qnty += $row[csf('issue_qnty')];
                }
                ?>
                </tbody>
                <tfoot>
                    <th colspan="7" align="right">Total&nbsp;</th>
                    <th align="right"><? echo number_format($tot_qnty, 2); ?></th>
                    <th></th>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    exit();
}

//for Issue Rtn qty
if ($action == "issue_rtn_qty_popup")
{
    echo load_html_head_contents("Issue Return Details", "../../../", 1, 1, $unicode, '', '');
    extract($_REQUEST);

    $sql="SELECT b.id as propotion_id, a.recv_number, a.receive_date, a.challan_no, a.booking_no, a.knitting_source, b.quantity AS returned_qnty, c.lot, c.dyed_type,c.id  AS prod_id, c.product_name_details, c.avg_rate_per_unit, d.id AS trans_id, d.cons_rate, g.dye_charge,d.requisition_no,d.cons_uom  FROM inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction  d, inv_transaction  e, inv_mrr_wise_issue_details f, inv_transaction g  WHERE a.id = d.mst_id AND d.id = b.trans_id AND b.prod_id = c.id AND e.mst_id=a.issue_id AND e.prod_id=b.prod_id AND e.id=f.issue_trans_id AND f.recv_trans_id=g.id AND d.transaction_type = 4 AND d.item_category = 1 AND c.item_category_id = 1 AND b.trans_type = 4 AND b.entry_form = 9 AND d.requisition_no IN(".$req_no.") AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND b.issue_purpose != 2 GROUP BY a.id, b.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.booking_no, a.knitting_source, c.lot, c.dyed_type, c.product_name_details, c.avg_rate_per_unit, d.id, d.cons_rate, g.dye_charge,b.quantity,d.requisition_no,d.cons_uom ";

    //echo $sql;
    $sql_rslt = sql_select($sql);

    $all_req_arr = array();
    foreach($sql_rslt as $row)
    {
        array_push($all_req_arr,$row[csf('requisition_no')]);
    }

    $demand_no = array();
    $req_sql_zs = sql_select("select a.demand_system_no, b.requisition_no from ppl_yarn_demand_entry_mst a, ppl_yarn_demand_entry_dtls b where a.id = b.mst_id ".where_con_using_array($all_req_arr,0,'b.requisition_no')." ");
    foreach($req_sql_zs as $row)
    {
        $demand_no[$row[csf('requisition_no')]]['demand_system_no'] = $row[csf('demand_system_no')];
    }

    $rslt_program = sql_select("select a.knit_id, a.requisition_no from ppl_yarn_requisition_entry a where a.status_active = 1 ".where_con_using_array($all_req_arr,0,'a.requisition_no')." ");
    foreach($rslt_program as $row)
    {
        $program_no_arr[$row[csf('requisition_no')]]['knit_id'] = $row[csf('knit_id')];
    }


    $store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');

    ?>
    <fieldset style="width:650px">
        <table width="620" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <th width="30">Sl</th>
                <th width="100">Return No</th>
                <th width="100">Ret Challan</th>
                <th width="80">Date</th>
                <th width="150">Item Description</th>
                <th width="80">Return Qnty</th>
                <th>UOM</th>
            </thead>
        </table>
        <div style="width:640px; overflow-y:scroll; max-height:300px" id="scroll_body">
            <table width="620" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                <tbody>
                <?
                if(empty($sql_rslt))
                {
                    echo get_empty_data_msg();
                }
                $sl = 0;
                $tot_rtn_qnty = 0;
                foreach($sql_rslt as $rq_no=>$row)
                {
                    $sl++;
                    ?>
                    <tr>
                        <td width="30" align="center"><? echo $sl;?></td>
                        <td width="100" align="center" ><? echo $row[csf('recv_number')];?></td>
                        <td width="100" align="center"><? echo $row[csf('challan_no')];?></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]);?></td>
                        <td width="150" align="center"><? echo $row[csf('product_name_details')];?></td>
                        <td width="80" align="right"><? echo number_format($row[csf('returned_qnty')],2);?></td>
                        <td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]];?></td>
                    </tr>
                    <?
                    $tot_rtn_qnty += $row[csf('returned_qnty')];
                }
                ?>
                </tbody>
                <tfoot>
                    <th colspan="5" align="right">Total&nbsp;</th>
                    <th align="right"><? echo number_format($tot_rtn_qnty, 2); ?></th>
                    <th></th>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    exit();
}

if ($action == "internal_ref_no_search_popup")
{
    echo load_html_head_contents("Booking Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    <script>
        function js_set_value(internal_ref)
        {
            $('#hidden_internal_ref').val(internal_ref);
            parent.emailwindow.hide();
        }
    </script>
</head>
<body>
    <div align="center" style="width:750px;">
        <form name="searchwofrm" id="searchwofrm" autocomplete=off>
            <fieldset style="width:100%;">
                <legend>Enter search words</legend>
                <table cellpadding="0" cellspacing="0" width="835" class="rpt_table" border="1" rules="all">
                    <thead>
                        <th>Po Buyer</th>
                        <th>Booking Date</th>
                        <th>Booking Type</th>
                        <th>Search By</th>
                        <th id="search_by_td_up" width="150">Please Enter Booking No</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:90px"
                            class="formbutton"/>
                            <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
                            value="<? echo $companyID; ?>">
                            <input type="hidden" name="cbo_within_group" id="cbo_within_group" class="text_boxes"
                            value="<? echo $cbo_within_group; ?>">
                            <input type="hidden" name="hidden_internal_ref" id="hidden_internal_ref" class="text_boxes"
                            value="">
                        </th>
                    </thead>
                    <tr>
                        <td align="center">
                            <?
                            echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
                            style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
                            readonly>
                        </td>
                        <td align="center">
                            <?
                            $booking_type_arr = array(1 => "Fabric Booking", 2 => "Sample Booking");
                            echo create_drop_down("cbo_booking_type", 100, $booking_type_arr, "", 0, '', '', '');
                            ?>
                        </td>
                        <td align="center">
                            <?
                            $search_by_arr = array(1 => "Booking No", 2 => "Job No", 3 => "IR/IB");
                            $dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
                            echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", 3, $dd, 0);
                            ?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
                            id="txt_search_common"/>
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show"
                            onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_po_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_booking_type').value, 'create_internal_ref_search_list_view', 'search_div', 'knitting_status_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
                            style="width:90px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
                <div style="width:100%; margin-top:5px; margin-left:3px" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
if ($action == "create_internal_ref_search_list_view")
{
    $data = explode("_", $data);
    $search_string = "%" . trim($data[0]) . "%";
    $search_by = $data[1];
    $company_id = $data[2];
    $buyer_id = $data[3];
    $date_from = trim($data[4]);
    $date_to = trim($data[5]);
    $cbo_within_group = trim($data[6]);
    $booking_type = trim($data[7]);
    $cbo_within_group=1;

    if ($buyer_id == 0)
    {
        $buyer_id_cond = "";
    }
    else
    {
        $buyer_id_cond = " and a.buyer_id=$buyer_id";
    }

    $search_field_cond = "";
    $search_field_cond_2 = "";

    if (trim($data[0]) != "")
    {
        if ($search_by == 1)
        {
            if ($cbo_within_group == 1)
            {
                $search_field_cond = "and a.booking_no like '$search_string'";
                $search_field_cond_2 = "and a.booking_no like '$search_string'";
            }
            else
            {
                $search_field_cond = "and c.sales_booking_no like '$search_string'";
                $search_field_cond_2 = "and b.sales_booking_no like '$search_string'";
            }
        }
        else if($search_by == 3 || $search_by == 1) {
                //for internal ref.
                $internalRef_cond = '';$booking_nos_cond = '';$booking_nos_cond2 = '';
                $internalRef_cond = " and a.grouping like '$search_string'";
                $sql_bookings=sql_select("select b.booking_no,a.job_no_mst,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $internalRef_cond group by b.booking_no,a.job_no_mst,a.grouping");
                $booking_nos="";$bookingArrChk=array();$internalRefArr=array();
                foreach ($sql_bookings as $row) {
                    $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no_mst')]]['grouping']=$row[csf('grouping')];
                    if($bookingArrChk[$row[csf('booking_no')]]!=$row[csf('booking_no')])
                    {
                        $booking_nos.="'".$row[csf('booking_no')]."',";
                        $bookingArrChk[$row[csf('booking_no')]]=$row[csf('booking_no')];
                    }
                }
                $booking_nos=chop($booking_nos,",");
                $booking_nos_cond = "and a.booking_no in($booking_nos)";
                $booking_nos_cond2 = "and c.sales_booking_no in($booking_nos)";
                unset($sql_bookings);
        }
        else
        {
            $search_field_cond = "and a.job_no like '$search_string'";
            //for internal ref.
            $internalRef_cond = '';$booking_nos_cond = '';$booking_nos_cond2 = '';
            $internalRef_cond = " and a.job_no_mst like '$search_string'";
            $sql_bookings=sql_select("select b.booking_no,a.job_no_mst,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $internalRef_cond group by b.booking_no,a.job_no_mst,a.grouping");
            $booking_nos="";$bookingArrChk=array();$internalRefArr=array();
            foreach ($sql_bookings as $row) {
                $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no_mst')]]['grouping']=$row[csf('grouping')];
                if($bookingArrChk[$row[csf('booking_no')]]!=$row[csf('booking_no')])
                {
                    $booking_nos.="'".$row[csf('booking_no')]."',";
                    $bookingArrChk[$row[csf('booking_no')]]=$row[csf('booking_no')];
                }
            }
            $booking_nos=chop($booking_nos,",");
            $booking_nos_cond = "and a.booking_no in($booking_nos)";
            $booking_nos_cond2 = "and c.sales_booking_no in($booking_nos)";
            unset($sql_bookings);
        }
    }
    /*else
    {
            //for internal ref.
            $booking_nos_cond = '';$booking_nos_cond2 = '';
            $sql_bookings=sql_select("select b.booking_no,a.job_no_mst,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.booking_no,a.job_no_mst,a.grouping");
            $booking_nos="";$bookingArrChk=array();$internalRefArr=array();
            foreach ($sql_bookings as $row) {
                $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no_mst')]]['grouping']=$row[csf('grouping')];
                if($bookingArrChk[$row[csf('booking_no')]]!=$row[csf('booking_no')])
                {
                    $booking_nos.="'".$row[csf('booking_no')]."',";
                    $bookingArrChk[$row[csf('booking_no')]]=$row[csf('booking_no')];
                }
            }
            $booking_nos=chop($booking_nos,",");
            $booking_nos_cond = "and a.booking_no in($booking_nos)";
            $booking_nos_cond2 = "and c.sales_booking_no in($booking_nos)";
            unset($sql_bookings);
    }
*/
    $date_cond = '';
    if ($cbo_within_group == 1)
    {

    }
    $date_field = ($cbo_within_group == 2) ? "c.booking_date" : "a.booking_date";
    if ($date_from != "" && $date_to != "")
    {
        if ($db_type == 0)
        {
            $date_cond = "and $date_field between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
        }
        else
        {
            $date_cond = "and $date_field between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
        }
    }

    if ($cbo_within_group == 1)
    {
        //for fabric booking
        if($booking_type == 1)
        {
            $sql = "select a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, b.job_no, b.style_ref_no from wo_booking_mst a, wo_po_details_master b, fabric_sales_order_mst c where a.job_no=b.job_no and a.booking_no=c.sales_booking_no and a.supplier_id=$company_id and a.pay_mode=5 and a.fabric_source in(1,2) and a.status_active =1 and a.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond $booking_nos_cond group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, b.job_no,b.style_ref_no";
        }
        //for sample booking
        else
        {
            $sql = "select a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.job_no, d.style_ref_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, fabric_sales_order_mst c, sample_development_mst d where a.booking_no=b.booking_no and a.booking_no=c.sales_booking_no and b.style_id = d.id and a.supplier_id=$company_id and a.pay_mode=5 and a.fabric_source in(1,2) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond $booking_nos_cond group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.job_no, d.style_ref_no";
        }
    }
    else
    {
        $sql = "select c.id, c.sales_booking_no booking_no, c.booking_date,c.buyer_id, c.company_id,c.job_no, c.style_ref_no from fabric_sales_order_mst c where c.company_id=$company_id and c.status_active =1 and c.is_deleted=0 $date_cond $search_field_cond $booking_nos_cond2 and c.within_group=2 group by c.id, c.sales_booking_no, c.booking_date, c.buyer_id, c.company_id, c.job_no, c.style_ref_no";
    }
    //echo $sql;

    $result = sql_select($sql);
    $poArr = array();
    $buyerArr = array();
    $jobsArrChks = array();
    $jobs_nos="";
    foreach ($result as $row)
    {
        //for buyer
        $buyerArr[$row[csf('buyer_id')]] = $row[csf('buyer_id')];

        //for po
        if ($row[csf('po_break_down_id')] != "")
        {
            $po_ids = explode(",", $row[csf('po_break_down_id')]);
            foreach ($po_ids as $po_id)
            {
                $poArr[$po_id] = $po_id;
            }
        }

        if ($row[csf('job_no')] != "")
        {
            if($jobsArrChks[$row[csf('job_no')]]!=$row[csf('job_no')])
            {
                $jobs_nos.="'".$row[csf('job_no')]."',";
                $jobsArrChks[$row[csf('job_no')]]=$row[csf('job_no')];
            }
        }
    }



    //for partial
    if($db_type==0)
    {
        $sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, group_concat(c.po_break_down_id) as po_break_down_id, c.job_no from wo_booking_mst a, wo_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond $booking_nos_cond and a.entry_form=108 group by a.id, a.booking_no,a.booking_date,a.buyer_id,a.company_id,a.delivery_date,a.currency_id,c.job_no";
    }
    else
    {
        //for fabric booking
        if($booking_type == 1)
        {
            $sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, c.job_no from wo_booking_mst a, wo_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond $booking_nos_cond and a.entry_form=108 group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, c.job_no";
        }
        //for sample booking
        else
        {
            //$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, c.job_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond and a.entry_form=108 group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, c.job_no";
            $sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond $booking_nos_cond  group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id";
        }
    }
    //echo $sql_partial;
    $result_partial = sql_select($sql_partial);
    foreach ($result_partial as $row)
    {
        //for buyer
        $buyerArr[$row[csf('buyer_id')]] = $row[csf('buyer_id')];

        //for po
        if ($row[csf('po_break_down_id')] != "")
        {
            $po_ids = explode(",", $row[csf('po_break_down_id')]);
            foreach ($po_ids as $po_id)
            {
                $poArr[$po_id] = $po_id;
            }
        }
        if ($row[csf('job_no')] != "")
        {
            if($jobsArrChks[$row[csf('job_no')]]!=$row[csf('job_no')])
            {
                $jobs_nos.="'".$row[csf('job_no')]."',";
                $jobsArrChks[$row[csf('job_no')]]=$row[csf('job_no')];
            }
        }
    }
    //echo "<pre>";
    //print_r($buyerArr);
    $jobs_nos=chop($jobs_nos,",");
    if (trim($data[0]) == "")
    {
        //for internal ref.
        $sql_bookings=sql_select("select b.booking_no,a.job_no_mst,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst in($jobs_nos) group by b.booking_no,a.job_no_mst,a.grouping");
        $internalRefArr=array();
        foreach ($sql_bookings as $row) {
            $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no_mst')]]['grouping']=$row[csf('grouping')];
        }
        unset($sql_bookings);
    }

    //for company details
    $company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');

    //for buyer details
    //$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
    $buyer_arr = array();
    if(!empty($buyerArr))
    {
        $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where 1=1".where_con_using_array($buyerArr,0,'id'), "id", "buyer_name");
    }

    //for buyer details
    //$po_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "buyer_name");
    $po_arr = array();
    if(!empty($poArr))
    {
        $po_arr = return_library_array("select id, po_number from wo_po_break_down where 1=1".where_con_using_array($poArr,0,'id'), "id", "buyer_name");
    }
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="80">PO Buyer</th>
            <th width="120">Booking No</th>
            <th width="90">Job No</th>
            <th width="120">Style Ref.</th>
            <th width="80">Booking Date</th>
            <th width="100">IR/IB</th>
            <th>PO No.</th>
        </thead>
    </table>
    <div style="width:840px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table"
        id="tbl_list_search">
        <?
        $i = 1;
        $j = 1;
        foreach ($result as $row)
        {
            if ($i % 2 == 0)
                $bgcolor = "#E9F3FF";
            else
                $bgcolor = "#FFFFFF";

            if ($row[csf('po_break_down_id')] != "")
            {
                $po_no = '';
                $po_ids = explode(",", $row[csf('po_break_down_id')]);
                foreach ($po_ids as $po_id)
                {
                    if ($po_no == "")
                        $po_no = $po_arr[$po_id];
                    else
                        $po_no .= "," . $po_arr[$po_id];
                }
            }
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                onClick="js_set_value('<? echo $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no')]]['grouping']; ?>')">
                <td width="40"><? echo $i; ?></td>
                <td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
                <td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
                <td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
                <td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
                <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                <td width="100"><p><? echo $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no')]]['grouping']; ?></p></td>
                <td><p><? echo $po_no; ?>&nbsp;</p></td>
            </tr>
            <?
            $i++;
        }

        //for partial
        foreach ($result_partial as $row)
        {
            if ($j % 2 == 0)
                $bgcolor = "#E9F3FF";
            else
                $bgcolor = "#FFFFFF";

            if ($row[csf('po_break_down_id')] != "")
            {
                $po_no = '';
                $po_ids = array_unique(explode(",", $row[csf('po_break_down_id')]));
                foreach ($po_ids as $po_id)
                {
                    if ($po_no == "")
                        $po_no = $po_arr[$po_id];
                    else
                        $po_no .= "," . $po_arr[$po_id];
                }
            }
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                onClick="js_set_value('<? echo $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no')]]['grouping']; ?>')">
                <td width="40"><? echo $j; ?></td>
                <td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
                <td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
                <td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
                <td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
                <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                <td width="100"><p><? echo $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no')]]['grouping']; ?></p></td>
                <td><p><? echo $po_no; ?>&nbsp;</p></td>
            </tr>
            <?
            $j++;
        }
        ?>
    </table>
</div>
<?
exit();
}

$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
$machine_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
$feeder = array(1 => "Full Feeder", 2 => "Half Feeder");

//--------------------------------------------------------------------------------------------------------------------
$tmplte = explode("**", $data);
if ($tmplte[0] == "viewtemplate")
    $template = $tmplte[1];
else
    $template = $lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template == "")
    $template = 1;

if ($action == "report_generate")
{
    $started = microtime(true);
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));
    $txt_machine_no = str_replace("'", "", $txt_machine_no);
    $cbo_booking_type = str_replace("'", "", $cbo_booking_type);
    if ($cbo_booking_type==1)
    {
      $booking_type_cond="and e.booking_type in (1) and e.booking_without_order=0";
    }
    else if($cbo_booking_type==2)
    {
        $booking_type_cond="and e.booking_type in (3) ";
    }
    else if($cbo_booking_type==3)
    {
        $booking_type_cond="and e.booking_type in (4) and e.booking_without_order=0";
    }
    else if($cbo_booking_type==4)
    {
        $booking_type_cond="and e.booking_type in (4) and e.booking_without_order=1";
    }


    if ($template == 1)
    {
        $cbo_knitting_status = str_replace("'", "", $cbo_knitting_status);
        $based_on = str_replace("'", "", $cbo_based_on);
        $presentationType = str_replace("'", "", $presentationType);
        $type = str_replace("'", "", $cbo_type);
        $cbo_buyer_id = str_replace("'", "", $cbo_buyer_id);
        $company_name = $cbo_company_name;
        if (str_replace("'", "", $cbo_party_type) == 0)
            $party_type = "%%";
        else
            $party_type = str_replace("'", "", $cbo_party_type);

        if (str_replace("'", "", $cbo_buyer_name) == 0)
        {
            if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
                if ($_SESSION['logic_erp']["buyer_id"] != "")
                    $buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
                else
                    $buyer_id_cond = "";
            } else {
                $buyer_id_cond = "";
            }
        } else {
            $buyer_id_cond = " and a.buyer_id=$cbo_buyer_name";
        }
        $year_cond = "";

        if ($cbo_buyer_id != 0)
        {
            $poBuyerCond = " and a.buyer_id=$cbo_buyer_id";
            $po_buyer_cond = " and e.po_buyer=$cbo_buyer_id";
        }
        else
        {
            $poBuyerCond = "";
            $po_buyer_cond = "";
        }

        //echo $po_buyer_cond;

        $cbo_year = str_replace("'", "", $cbo_year);
        $year_cond = "";
        if (trim($cbo_year) != 0) {
            if ($db_type == 0)
                $year_cond = " and YEAR(e.insert_date)=$cbo_year";
            else if ($db_type == 2)
                $year_cond = " and to_char(e.insert_date,'YYYY')=$cbo_year";
            else
                $year_cond = "";
        }

        $sales_no_cond = "";
        if (str_replace("'", "", $txt_sales_no) != "") {

            $chk_prefix_sales_no=explode("-",str_replace("'", "", $txt_sales_no));
            if($chk_prefix_sales_no[3]!="")
            {
                $sales_number = "%" . trim(str_replace("'", "", $txt_sales_no));
                $sales_no_cond = "and e.job_no like '$sales_number'";
            }
            else
            {
                $sales_number = trim(str_replace("'", "", $txt_sales_no));
                $sales_no_cond = "and e.job_no_prefix_num = '$sales_number'";
            }
        }


       $booking_search_cond = "";
       if (str_replace("'", "", trim($txt_booking_no)) != "") {

           $booking_number = "%" . trim(str_replace("'", "", $txt_booking_no)) . "%";
           $booking_search_cond = "and e.sales_booking_no like '$booking_number'";
       }

       if ($db_type == 0){
           $year_field = "YEAR(e.insert_date) as year";
           $year_field_cond = "YEAR(a.insert_date) as year";
        } else if ($db_type == 2){
           $year_field = "to_char(e.insert_date,'YYYY') as year";
           $year_field_cond = "to_char(a.insert_date,'YYYY') as year";
        }


        //for program date
        if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "")
        {
            if ($based_on == 2)
            {
                $date_cond = " and b.program_date between " . trim($txt_date_from) . " and " . trim($txt_date_to) . "";
            }
            else
            {
                $date_cond = " and b.start_date between " . trim($txt_date_from) . " and " . trim($txt_date_to) . "";
            }
        }
        else
        {
            $date_cond = "";
        }

        if ($cbo_buyer_id != 0)
        {
            $buyer_cond = " and e.po_buyer=".$cbo_buyer_id;
        }

        if (str_replace("'", "", $txt_machine_dia) == "")
            $machine_dia = "%%";
        else
            $machine_dia = "%" . str_replace("'", "", $txt_machine_dia) . "%";

        if (str_replace("'", "", $txt_program_no) == "")
            $program_no = "%%";
        else
            $program_no = str_replace("'", "", $txt_program_no);

        //--------------------------------
        if ($type > 0)
            $knitting_source_cond = "and b.knitting_source=$type";
        else
            $knitting_source_cond = "";

        $status_cond = "";
        if ($cbo_knitting_status != "")
            $status_cond = "and b.status in($cbo_knitting_status)";



        //internal ref and job // job no add in search panel only for Show button. .CRM id: 17003
        $txt_jobref = str_replace("'", "", trim($txt_job_no)) ;
        $txt_internalref = "%" . str_replace("'", "", trim($txt_internal_ref)) . "%";
        if (str_replace("'", "", trim($txt_internal_ref)) != "" || str_replace("'", "", trim($txt_job_no)) != "")
        {
                //for internal ref.
                $internalRef_cond = '';$booking_nos_internal_ref_cond = '';
                $internalRef_cond = " and a.grouping like '$txt_internalref'";

                $jobRef_cond = '';$booking_nos_internal_ref_cond = '';
                $jobRef_cond = " and c.job_no_prefix_num='$txt_jobref'";

                if($presentationType==1 &&  str_replace("'", "", trim($txt_job_no)) != "")
                {
                    $sql_bookings=sql_select("select b.booking_no from wo_po_break_down a,wo_booking_dtls b,wo_po_details_master c where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.job_id=c.id and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobRef_cond");
                }
                else
                {
                    $sql_bookings=sql_select("select b.booking_no from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $internalRef_cond");
                }


                $booking_nos="";$bookingArrChk=array();
                foreach ($sql_bookings as $row) {
                    if($bookingArrChk[$row[csf('booking_no')]]!=$row[csf('booking_no')])
                    {
                        $booking_nos.="'".$row[csf('booking_no')]."',";
                        $bookingArrChk[$row[csf('booking_no')]]=$row[csf('booking_no')];
                    }
                }
                $booking_nos=chop($booking_nos,",");
                $booking_nos_internal_ref_cond = "and e.sales_booking_no in($booking_nos)";
                unset($sql_bookings);
        }
        //echo $booking_nos_internal_ref_cond;

        $company_names=str_replace("'", "", trim($company_name));
        if ($txt_machine_no != "")
        {

            if ($db_type == 0)
            {
                $machine_id_ref = return_field_value("group_concat(id) as machine_id", "lib_machine_name", "machine_no='$txt_machine_no'", "machine_id");
            }
            else if ($db_type == 2)
            {
                $machine_id_ref = return_field_value("LISTAGG(cast(id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as machine_id", "lib_machine_name", "machine_no='$txt_machine_no'", "machine_id");
            }
            //echo $machine_id_ref;

            /* if(!empty($machine_id_ref))
            {
                $machine_id_ref_cond = " and regexp_like(b.machine_id, '(^|,)$machine_id_ref(,|$)')";
            }
            else
            {
                $machine_id_ref_cond="";
            } */

            if($presentationType == 5)
            {
                $sql = "SELECT a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id,e.po_buyer,e.within_group,e.po_company_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id as sales_buyer,e.id as sales_id,e.booking_without_order
                from
                ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, ppl_entry_machine_datewise d, fabric_sales_order_mst e
                where
                a.id=b.mst_id and b.id=c.dtls_id and b.id=d.dtls_id and c.po_id=e.id and c.is_sales=1 and d.machine_id in($machine_id_ref) and a.company_id=$company_name and b.knitting_party like '$party_type' and b.machine_dia like '$machine_dia' and b.id like '$program_no' and a.is_sales=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=1 and b.status_active=0 and c.is_deleted=1 and c.status_active=0 $buyer_id_cond $po_buyer_cond $sales_no_cond $year_cond $booking_search_cond $date_cond $status_cond $knitting_source_cond  $booking_nos_internal_ref_cond $booking_type_cond
                group by b.id, a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.knitting_source, b.knitting_party, b.color_id,e.po_buyer,e.within_group,e.po_company_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id, e.id,e.booking_without_order
                union all
                SELECT a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id,e.po_buyer,e.within_group,e.po_company_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id as sales_buyer,e.id as sales_id,e.booking_without_order
                from
                ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, ppl_entry_machine_datewise d, fabric_sales_order_mst e
                where
                a.id=b.mst_id and b.id=c.dtls_id and b.id=d.dtls_id and c.po_id=e.id and c.is_sales=1 and d.machine_id in($machine_id_ref) and a.company_id=$company_name and b.knitting_party like '$party_type' and b.machine_dia like '$machine_dia' and b.id like '$program_no' and a.is_sales=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.is_revised=1 and c.is_revised=1 $buyer_id_cond $po_buyer_cond $sales_no_cond $year_cond $booking_search_cond $date_cond $status_cond $knitting_source_cond  $booking_nos_internal_ref_cond $booking_type_cond
                group by b.id, a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.knitting_source, b.knitting_party, b.color_id,e.po_buyer,e.within_group,e.po_company_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id, e.id,e.booking_without_order";
                // order by b.knitting_source,b.machine_dia,b.machine_gg, b.id
            }
            else
            {

                $sql = "SELECT a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id,e.po_buyer,e.within_group,e.po_company_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id as sales_buyer,e.id as sales_id,e.booking_without_order, b.status_active, b.is_deleted, b.is_revised
                from
                    ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, ppl_entry_machine_datewise d, fabric_sales_order_mst e
                where
                    a.id=b.mst_id and b.id=c.dtls_id and b.id=d.dtls_id and c.po_id=e.id and c.is_sales=1 and d.machine_id in($machine_id_ref) and a.company_id in($company_names) and b.knitting_party like '$party_type' and b.machine_dia like '$machine_dia' and b.id like '$program_no' and a.is_sales=1 and a.is_deleted=0 and a.status_active=1 $buyer_id_cond $po_buyer_cond $sales_no_cond $year_cond $booking_search_cond $date_cond $status_cond $knitting_source_cond  $booking_nos_internal_ref_cond $booking_type_cond
                group by b.id, a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.knitting_source, b.knitting_party, b.color_id,e.po_buyer,e.within_group,e.po_company_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id, e.id, e.booking_without_order, b.status_active, b.is_deleted, b.is_revised order by b.knitting_source,b.machine_dia,b.machine_gg, b.id"; //and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1
            }
        }
        else
        {
            if($presentationType == 5)
            {
                $sql = "SELECT a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id,e.po_buyer,e.within_group,e.po_company_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id as sales_buyer,e.id as sales_id,e.booking_without_order
                from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, fabric_sales_order_mst e
                where a.id=b.mst_id and b.id=c.dtls_id and c.po_id=e.id and c.is_sales=1  and a.company_id=$company_name  and b.knitting_party like '$party_type' and b.machine_dia like '$machine_dia' and b.id like '$program_no' and a.is_sales=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=1 and b.status_active=0 and c.is_deleted=1 and c.status_active=0 $buyer_id_cond $po_buyer_cond $sales_no_cond $year_cond $booking_search_cond $date_cond $status_cond  $knitting_source_cond $booking_nos_internal_ref_cond $booking_type_cond
                group by b.id, a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.knitting_source, b.knitting_party, b.color_id,e.po_buyer,e.within_group,e.po_company_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id, e.id,e.booking_without_order
                union all
                SELECT a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id,e.po_buyer,e.within_group,e.po_company_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id as sales_buyer,e.id as sales_id,e.booking_without_order
                    from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, fabric_sales_order_mst e
                    where a.id=b.mst_id and b.id=c.dtls_id and c.po_id=e.id and c.is_sales=1  and a.company_id=$company_name  and b.knitting_party like '$party_type' and b.machine_dia like '$machine_dia' and b.id like '$program_no' and a.is_sales=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.is_revised=1 and c.is_revised=1 $buyer_id_cond $po_buyer_cond $sales_no_cond $year_cond $booking_search_cond $date_cond $status_cond  $knitting_source_cond $booking_nos_internal_ref_cond $booking_type_cond
                    group by b.id, a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.knitting_source, b.knitting_party, b.color_id,e.po_buyer,e.within_group,e.po_company_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id, e.id,e.booking_without_order ";
                //order by b.knitting_source, b.machine_dia, b.machine_gg, b.id
            }
            else
            {

                $sql = "SELECT a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id,e.po_buyer,e.within_group,e.po_company_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id as sales_buyer,e.id as sales_id,e.booking_without_order, b.status_active, b.is_deleted, b.is_revised
                from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, fabric_sales_order_mst e
                where a.id=b.mst_id and b.id=c.dtls_id and c.po_id=e.id and c.is_sales=1  and a.company_id in($company_names)  and b.knitting_party like '$party_type' and b.machine_dia like '$machine_dia' and b.id like '$program_no' and a.is_sales=1 and a.is_deleted=0 and a.status_active=1 $buyer_id_cond $po_buyer_cond $sales_no_cond $year_cond $booking_search_cond $date_cond $status_cond  $knitting_source_cond $booking_nos_internal_ref_cond $booking_type_cond
                group by b.id, a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.knitting_source, b.knitting_party, b.color_id,e.po_buyer,e.within_group,e.po_company_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id, e.id,e.booking_without_order, b.status_active, b.is_deleted, b.is_revised order by b.knitting_source, b.machine_dia, b.machine_gg, b.id";
                //echo $sql;//and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1
            }
        }
        //echo $sql; //die;
        $nameArray = sql_select($sql);
        if(empty($nameArray))
        {
            echo get_empty_data_msg();
            die;
        }

        //--------------------------------


        $con = connect();
        $r_id3=execute_query("delete from tmp_prog_no where userid=$user_name");
        $r_id4=execute_query("delete from tmp_reqs_no where userid=$user_name");
        $r_id5=execute_query("delete from tmp_barcode_no where userid=$user_name");
        $r_id6=execute_query("delete from tmp_booking_id where userid=$user_name");
        $r_id7=execute_query("delete from tmp_prod_id where userid=$user_name");

        oci_commit($con);



        $planIdArr =$prog_id_check=$booking_idArr=$salesArr=array();

        foreach($nameArray as $row)
        {
            array_push($salesArr,$row[csf('sales_id')]);
            $planIdArr[$row[csf('id')]] = $row[csf('id')];
            if ($row[csf('booking_entry_form')]==86) // Budget Wise Fabric Booking
            {
                $booking_idArr[$row[csf('booking_id')]] = $row[csf('booking_id')];
            }
            else if ($row[csf('booking_entry_form')]==88) // Short Fabric Booking
            {
                $booking_idArr[$row[csf('booking_id')]] = $row[csf('booking_id')];
            }
            else if ($row[csf('booking_entry_form')]==118) // Main Fabric Booking V2
            {
                $booking_idArr[$row[csf('booking_id')]] = $row[csf('booking_id')];
            }
            else if ($row[csf('booking_entry_form')]==108) // Main Fabric Booking V2
            {
                $booking_idArr[$row[csf('booking_id')]] = $row[csf('booking_id')];
            }
            else if ($row[csf('booking_entry_form')]==140) // Non order sample
            {
                $booking_idArr[$row[csf('booking_id')]] = $row[csf('booking_id')];
            }



            if(!$prog_id_check[$row[csf('id')]])
            {
                $prog_id_check[$row[csf('id')]]=$row[csf('id')];
                $ProgNO = $row[csf('id')];
                $rID3=execute_query("insert into tmp_prog_no (userid, prog_no) values ($user_name,$ProgNO)");
            }
        }
        if($rID3)
        {
            oci_commit($con);
        }



        // echo "<pre>";print_r($booking_idArr);die;

        foreach($booking_idArr as $kbk_id=>$v_bk_id)
        {
            $rID6 = execute_query("insert into tmp_booking_id (userid, booking_id) values ($user_name,$v_bk_id)");
        }

        if($rID6)
        {
            oci_commit($con);
        }

        $booking_sql="SELECT a.booking_no, a.company_id, a.buyer_id, a.po_break_down_id, a.item_category, a.fabric_source, a.job_no, a.entry_form, a.is_approved from wo_booking_mst a, tmp_booking_id b where a.id = b.booking_id and b.userid=".$user_name." and a.entry_form in(86,88,118,108) and a.booking_type=1 and a.is_short in(1,2) and a.status_active=1 and a.is_deleted=0
        union all
        SELECT a.booking_no, a.company_id, a.buyer_id, null as po_break_down_id, a.item_category, a.fabric_source, a.job_no, a.entry_form_id as entry_form, a.is_approved
        from wo_non_ord_samp_booking_mst a, tmp_booking_id b  where a.id = b.booking_id and b.userid=".$user_name." and a.entry_form_id in(140) and a.booking_type=4  and a.status_active=1 and a.is_deleted=0";
        $booking_sql_dataArr = sql_select($booking_sql);
        $booking_Arr=array();
        foreach($booking_sql_dataArr as $row)
        {
            $booking_Arr[$row[csf('booking_no')]]['booking_company_id'] = $row[csf('company_id')];
            $booking_Arr[$row[csf('booking_no')]]['booking_order_id'] = $row[csf('po_break_down_id')];
            $booking_Arr[$row[csf('booking_no')]]['booking_fabric_natu'] = $row[csf('item_category')];
            $booking_Arr[$row[csf('booking_no')]]['booking_fabric_source'] = $row[csf('fabric_source')];
            $booking_Arr[$row[csf('booking_no')]]['booking_job_no'] = $row[csf('job_no')];
            $booking_Arr[$row[csf('booking_no')]]['is_approved'] = $row[csf('is_approved')];
            $booking_Arr[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
        }

        /*$sql_sales = "SELECT d.id as SALES_ID, d.job_no as JOB_NO, c.grouping as GROUPING,c.job_no_mst as JOB_NO_MST  FROM wo_booking_mst a, wo_po_details_master b,wo_po_break_down c,fabric_sales_order_mst d WHERE a.id=d.booking_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and a.pay_mode=5 and a.fabric_source in(1,2) and a.supplier_id=$company_name and a.status_active =1 and a.is_deleted =0 and a.item_category=2 ".where_con_using_array($salesArr, '0', 'd.id')." group by d.id, d.job_no, c.grouping,c.job_no_mst";*/

       $sql_sales = "SELECT d.id as SALES_ID, d.job_no as JOB_NO, c.grouping as GROUPING,c.job_no_mst as JOB_NO_MST,d.po_buyer as PO_BUYER  FROM wo_booking_mst a,wo_booking_dtls x, wo_po_details_master b,wo_po_break_down c,fabric_sales_order_mst d WHERE a.booking_no=x.booking_no and x.job_no=b.job_no and b.id=c.job_id and a.id=d.booking_id and b.job_no=c.job_no_mst and a.pay_mode=5 and a.fabric_source in(1,2) and a.supplier_id in($company_names) and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and x.status_active =1 and x.is_deleted =0 and a.item_category=2 and d.booking_without_order=0 ".where_con_using_array($salesArr, '0', 'd.id')." group by d.id, d.job_no, c.grouping,c.job_no_mst,d.po_buyer";


        $sqlSalesResult = sql_select($sql_sales);
        $po_break_dtls_arr=array();
        foreach ($sqlSalesResult as $rows)
        {
            $po_break_dtls_arr[$rows['SALES_ID']]['GROUPING'] = $rows['GROUPING'];
            $po_break_dtls_arr[$rows['SALES_ID']]['JOB_NO'].=$rows['JOB_NO_MST'].",";
            $po_break_dtls_arr[$rows['SALES_ID']]['PO_BUYER'] = $rows['PO_BUYER'];
        }
        unset($sqlSalesResult);

        if($presentationType == 5)
		{

			if ($db_type == 0)
			{
			   //$reqsData = sql_select("select a.knit_id, a.requisition_no as reqs_no, group_concat(distinct(a.prod_id)) as prod_id from ppl_yarn_requisition_entry a ,tmp_prog_no b where a.knit_id=b.prog_no and b.userid=$user_name and a.status_active=1 and a.is_deleted=0  group by a.knit_id");
				$reqsData = sql_select("SELECT A.id, A.knit_id as KNIT_ID, A.requisition_no as REQUISITION_NO, A.prod_id as PROD_ID, A.yarn_qty as YARN_QNTY FROM PPL_YARN_REQUISITION_ENTRY A, TMP_PROG_NO B WHERE A.knit_id=B.prog_no AND B.userid=".$user_name." AND A.status_active=0 AND A.is_deleted=1 ORDER BY A.knit_id, A.requisition_no ASC");
			}
			else
			{
			   //$reqsData = sql_select("select a.knit_id, max(a.requisition_no) as reqs_no, LISTAGG(a.prod_id, ',') WITHIN GROUP (ORDER BY a.prod_id) as prod_id from ppl_yarn_requisition_entry a,tmp_prog_no b where a.knit_id=b.prog_no and b.userid=$user_name and  a.status_active=1 and a.is_deleted=0  group by a.knit_id,a.requisition_no");

				$reqsData = sql_select("SELECT A.ID, A.KNIT_ID, A.REQUISITION_NO, A.PROD_ID, A.YARN_QNTY FROM PPL_YARN_REQUISITION_ENTRY A, TMP_PROG_NO B WHERE A.KNIT_ID=B.PROG_NO AND B.USERID=".$user_name." AND A.STATUS_ACTIVE=0 AND A.IS_DELETED=1 ORDER BY A.KNIT_ID, A.REQUISITION_NO ASC");
			}
		}
		else
		{

			if ($db_type == 0)
			{
			   //$reqsData = sql_select("select a.knit_id, a.requisition_no as reqs_no, group_concat(distinct(a.prod_id)) as prod_id from ppl_yarn_requisition_entry a ,tmp_prog_no b where a.knit_id=b.prog_no and b.userid=$user_name and a.status_active=1 and a.is_deleted=0  group by a.knit_id");
				$reqsData = sql_select("SELECT A.id, A.knit_id as KNIT_ID, A.requisition_no as REQUISITION_NO, A.prod_id as PROD_ID, A.yarn_qty as YARN_QNTY FROM PPL_YARN_REQUISITION_ENTRY A, TMP_PROG_NO B WHERE A.knit_id=B.prog_no AND B.userid=".$user_name." ORDER BY A.knit_id, A.requisition_no ASC"); //AND A.status_active=1 AND A.is_deleted=0
			}
			else
			{
			   //$reqsData = sql_select("select a.knit_id, max(a.requisition_no) as reqs_no, LISTAGG(a.prod_id, ',') WITHIN GROUP (ORDER BY a.prod_id) as prod_id from ppl_yarn_requisition_entry a,tmp_prog_no b where a.knit_id=b.prog_no and b.userid=$user_name and  a.status_active=1 and a.is_deleted=0  group by a.knit_id,a.requisition_no");

				$reqsData = sql_select("SELECT A.ID, A.KNIT_ID, A.REQUISITION_NO, A.PROD_ID, A.YARN_QNTY FROM PPL_YARN_REQUISITION_ENTRY A, TMP_PROG_NO B WHERE A.KNIT_ID=B.PROG_NO AND B.USERID=".$user_name." ORDER BY A.KNIT_ID, A.REQUISITION_NO ASC"); //AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
			}
		}

        $reqsDataArr = array();
        $req_data_arr = array();
        foreach ($reqsData as $row)
        {
           $req_data_arr[$row['KNIT_ID']]['prod_id'][$row['PROD_ID']] = $row['PROD_ID'];
           $reqsDataArr[$row['KNIT_ID']]['reqs_no'] = $row['REQUISITION_NO'];
           $reqsDataArr[$row['KNIT_ID']]['reqs_qty'] += $row['YARN_QNTY'];
           $reqsDataArr[$row['KNIT_ID']]['reqs_id'][$row['ID']] = $row['ID'];
        }

        foreach($req_data_arr as $prog_key=>$prog_val)
        {
            $reqsDataArr[$prog_key]['prod_id'] = implode(',',$prog_val['prod_id']);
        }
        //echo "<pre>";
        //print_r($reqsDataArr); die;

        $product_details_arr = array();
        $yarn_iss_arr = array();
        $yarn_IssRtn_arr = array();
        if(!empty($reqsDataArr))
        {
            $requisitionNoArr = array();
            $productIdArr = $reqs_no_check=array();
            foreach($reqsDataArr as $row)
            {
                $requisitionNoArr[$row['reqs_no']] = $row['reqs_no'];

                if(!$reqs_no_check[$row['reqs_no']])
                {
                    $reqs_no_check[$row['reqs_no']]=$row['reqs_no'];
                    $ReqsNo = $row['reqs_no'];
                    $rID4=execute_query("insert into tmp_reqs_no (userid, reqs_no) values ($user_name,$ReqsNo)");
                }
                $productId = explode(",", $row['prod_id']);
                foreach($productId as $prodId)
                {
                    $productIdArr[$prodId] = $prodId;
                }
            }
            if($rID4)
            {
                oci_commit($con);
            }

            foreach($productIdArr as $k_prd=>$v_prd)
            {
                $rID7=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_name, $v_prd)");
            }

            if($rID7)
            {
                oci_commit($con);
            }

            $pro_sql = sql_select("select a.id, a.product_name_details, a.lot, a.supplier_id from product_details_master a, tmp_prod_id b where a.id = b.prod_id and b.userid = $user_name and a.company_id in($company_names) and a.item_category_id=1");
            foreach ($pro_sql as $row)
            {
               $product_details_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
               $product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
               $product_details_arr[$row[csf('id')]]['supplier'] = $row[csf('supplier_id')];
            }

            //--------------------------------
            $yarnIssueData = sql_select("select a.requisition_no, a.prod_id, sum(a.cons_quantity) as qnty from inv_transaction a,tmp_reqs_no b where a.requisition_no=b.reqs_no and b.userid=$user_name and a.item_category=1 and a.transaction_type=2 and a.receive_basis in(3,8) and a.status_active=1 and a.is_deleted=0  group by a.requisition_no, a.prod_id");
            foreach ($yarnIssueData as $row)
            {
               $yarn_iss_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]+= $row[csf('qnty')];
            }
           /* echo "<pre>";
            print_r($yarn_iss_arr);
            echo "</pre>";*/

           /* $yarnIssueRtnData = sql_select("SELECT a.booking_id AS reqsn_no,b.prod_id,SUM (b.cons_quantity) AS qnty,SUM (b.cons_reject_qnty) AS reject_qnty FROM tmp_reqs_no c, inv_receive_master a, inv_transaction b WHERE a.booking_id = c.reqs_no AND c.userid = $user_name And a.id = b.mst_id AND a.receive_basis IN (3) AND a.entry_form = 9 AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active = 1
            AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 GROUP BY a.booking_id, b.prod_id
            union all
            SELECT a.requisition_no AS reqsn_no,b.prod_id,SUM (b.cons_quantity) AS qnty,SUM (b.cons_reject_qnty) AS reject_qnty FROM tmp_reqs_no c, inv_receive_master a, inv_transaction b WHERE  a.requisition_no = c.reqs_no AND c.userid = $user_name And a.id = b.mst_id AND a.receive_basis IN (8) AND a.entry_form = 9 AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 GROUP BY a.requisition_no, b.prod_id"); */

            $yarnIssueRtnData = sql_select("SELECT b.requisition_no AS reqsn_no,b.prod_id,SUM (b.cons_quantity) AS qnty,SUM (b.cons_reject_qnty) AS reject_qnty FROM tmp_reqs_no c, inv_receive_master a, inv_transaction b WHERE b.requisition_no = c.reqs_no AND c.userid = $user_name And a.id = b.mst_id AND a.receive_basis IN (3) AND a.entry_form = 9 AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active = 1
            AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 GROUP BY b.requisition_no, b.prod_id
            union all
            SELECT b.requisition_no AS reqsn_no,b.prod_id,SUM (b.cons_quantity) AS qnty,SUM (b.cons_reject_qnty) AS reject_qnty FROM tmp_reqs_no c, inv_receive_master a, inv_transaction b WHERE  b.requisition_no = c.reqs_no AND c.userid = $user_name And a.id = b.mst_id AND a.receive_basis IN (8) AND a.entry_form = 9 AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 GROUP BY b.requisition_no, b.prod_id");

            foreach ($yarnIssueRtnData as $row)
            {
                $yarn_IssRtn_arr[$row[csf('reqsn_no')]][$row[csf('prod_id')]] += $row[csf('qnty')];
                $yarn_IssRej_arr[$row[csf('reqsn_no')]][$row[csf('prod_id')]] += $row[csf('reject_qnty')];
            }

        }
        //echo "<pre>";
        //print_r($yarn_IssRtn_arr); die;

        if ($db_type == 0)
        {
            $knitting_dataArray = sql_select("SELECT a.booking_id, group_concat(a.id) as knit_id,group_concat(b.gsm) as gsm, sum(b.grey_receive_qnty) as knitting_qnty,sum(reject_fabric_receive) as fabric_reject_qty,sum(b.grey_receive_qnty_pcs) as grey_receive_qnty_pcs
            from tmp_prog_no c, inv_receive_master a, pro_grey_prod_entry_dtls b where c.prog_no=a.booking_id and c.userid=$user_name and a.id=b.mst_id and item_category=13 and a.entry_form=2 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 group by a.booking_id");
        }
        else
        {
            /* $knitting_dataArray = sql_select("SELECT a.booking_id, LISTAGG(a.id, ',') WITHIN GROUP (ORDER BY a.id) as knit_id, LISTAGG(b.gsm, ',') WITHIN GROUP (ORDER BY b.gsm) as gsm, sum(b.grey_receive_qnty) as knitting_qnty,sum(reject_fabric_receive) as fabric_reject_qty,sum(b.grey_receive_qnty_pcs) as grey_receive_qnty_pcs
            from tmp_prog_no c, inv_receive_master a, pro_grey_prod_entry_dtls b
            where a.booking_id=c.prog_no and c.userid=$user_name and a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0  group by a.booking_id"); */

            $knitting_dataArray = sql_select("SELECT a.booking_id, a.id as knit_id, b.gsm as gsm, b.grey_receive_qnty as knitting_qnty,b.reject_fabric_receive as fabric_reject_qty,b.grey_receive_qnty_pcs as grey_receive_qnty_pcs
            from tmp_prog_no c, inv_receive_master a, pro_grey_prod_entry_dtls b
            where c.prog_no=a.booking_id and c.userid=$user_name and a.id=b.mst_id  and a.entry_form=2  and a.item_category=13 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 group by a.booking_id,a.id,b.gsm,b.grey_receive_qnty,b.reject_fabric_receive,b.grey_receive_qnty_pcs");

        }

        $knitDataArr = array();
        foreach ($knitting_dataArray as $row)
        {
            $knitDataArr[$row[csf('booking_id')]]['qnty'] += $row[csf('knitting_qnty')];
            $knitDataArr[$row[csf('booking_id')]]['knit_id'] .= $row[csf('knit_id')].',';
            $knitDataArr[$row[csf('booking_id')]]['gsm'] = $row[csf('gsm')];
            $knitDataArr[$row[csf('booking_id')]]['reject_qnty'] += $row[csf('fabric_reject_qty')];
            $knitDataArr[$row[csf('booking_id')]]['grey_receive_qnty_pcs'] += $row[csf('grey_receive_qnty_pcs')];
            //$knitDataArr[$row[csf('id')]]['qnty']
            //$knitDataArr[$row[csf('id')]]['knit_id']
        }
        //echo "<pre>";
        //print_r($knitDataArr); die;


        $knitting_qc_sql="SELECT distinct a.recv_number, a.booking_id, a.knitting_source, a.knitting_company, d.barcode_no, d.roll_weight, d.roll_status
        from tmp_prog_no c, inv_receive_master a, pro_grey_prod_entry_dtls b, PRO_QC_RESULT_MST d
        where c.prog_no=a.booking_id and c.userid=$user_name and a.id=b.mst_id and b.id=d.PRO_DTLS_ID
        and a.item_category=13 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
        //echo $knitting_qc_sql;die;
        $knitting_qc_sql_result = sql_select($knitting_qc_sql);
        $knitQcDataArr = array();
        foreach ($knitting_qc_sql_result as $row)
        {
            $knitQcDataArr[$row[csf('booking_id')]]['qnty'] += $row[csf('roll_weight')];
            if($row[csf('roll_status')]==1)
            {
                $knitQcDataArr[$row[csf('booking_id')]]['qc_pass_qnty'] += $row[csf('roll_weight')];
            }
            else if($row[csf('roll_status')]==2)
            {
                $knitQcDataArr[$row[csf('booking_id')]]['held_up_qnty'] += $row[csf('roll_weight')];
            }
        }
        // echo "<pre>"; print_r($knitQcDataArr); die;

        $sql_batchData = "SELECT b.program_no, b.batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b,tmp_prog_no c where a.id=b.mst_id and b.program_no=c.prog_no and c.userid=$user_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
        //echo $sql_batchData;die;
        $rslt_batchData = sql_select($sql_batchData);
        $batchDataArr = array();
        foreach ($rslt_batchData as $row)
        {
            $batchDataArr[$row[csf('program_no')]]['batch_qnty'] += $row[csf('batch_qnty')];
        }
        unset($rslt_batchData);
        //echo "<pre>";print_r($batchDataArr);die;

        $knitting_recv_qnty_array = return_library_array("SELECT a.booking_id, sum(b.grey_receive_qnty) as knitting_qnty from tmp_prog_no c, inv_receive_master a, pro_grey_prod_entry_dtls b where c.prog_no=a.booking_id and c.userid=$user_name and a.id=b.mst_id and a.item_category=13 and a.entry_form=22 and a.receive_basis=9 and b.status_active=1 and b.is_deleted=0 group by a.booking_id", "booking_id", "knitting_qnty");

        $knitting_issue_qnty_array = return_library_array("SELECT a.booking_id, sum(b.issue_qnty) as knitting_qnty from tmp_prog_no c, inv_issue_master a, inv_grey_fabric_issue_dtls b where c.prog_no=a.booking_id and c.userid=$user_name and a.id=b.mst_id and a.item_category=13 and a.entry_form=16 and a.issue_basis=3 and b.status_active=1 and b.is_deleted=0 group by a.booking_id", "booking_id", "knitting_qnty");


        $knit_recvProg_qty_arr = return_library_array("SELECT b.program_no, sum(b.grey_receive_qnty) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b,tmp_prog_no c where a.id=b.mst_id and b.program_no=c.prog_no and c.userid=$user_name and a.item_category=13 and a.entry_form=22 and a.receive_basis=11 and b.status_active=1 and b.is_deleted=0 group by b.program_no", "program_no", "knitting_qnty");

        $knit_issueProg_qty_arr = return_library_array("SELECT b.program_no, sum(b.issue_qnty) as knitting_qnty from inv_issue_master a, inv_grey_fabric_issue_dtls b,tmp_prog_no c where a.id=b.mst_id and b.program_no=c.prog_no and c.userid=$user_name and a.item_category=13 and a.entry_form=16 and a.issue_basis=1 and b.status_active=1 and b.is_deleted=0 group by b.program_no", "program_no", "knitting_qnty");

        $barcode_arr = array();
        $barcode_no_arr = $barcode_no_check=array();
        $barcodeData = sql_select("SELECT b.mst_id, b.barcode_no from tmp_prog_no c, inv_receive_master a, pro_roll_details b where a.booking_id=c.prog_no and c.userid=$user_name and a.id=b.mst_id and a.entry_form=2 and b.entry_form=2 and a.item_category=13 and a.receive_basis=2");
        foreach ($barcodeData as $row)
        {
            $barcode_arr[$row[csf('mst_id')]] .= $row[csf('barcode_no')] . ",";
            $barcode_no_arr[] = $row[csf('barcode_no')];

            if(!$barcode_no_check[$row[csf('barcode_no')]])
            {
                $barcode_no_check[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
                $BarcodeNO = $row[csf('barcode_no')];
                $rID5=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_name,$BarcodeNO)");
            }
        }
        if($rID5)
        {
            oci_commit($con);
        }
        //echo "<pre>";
        //print_r($barcode_arr); die;


        $split_sql="SELECT c.barcode_no as mother_barcode, d.barcode_no , d.qnty, d.qc_pass_qnty_pcs, d.coller_cuff_size from pro_roll_split c , pro_roll_details d,tmp_barcode_no e 
        where c.entry_form = 75 and  c.split_from_id = d.roll_split_from and c.barcode_no=e.barcode_no and e.userid=$user_name and c.status_active = 1 and d.status_active = 1";

        $split_sql_data = sql_select($split_sql);
        foreach ($split_sql_data as $key => $row)
        {
            $mother_barcode_array[$row[csf('mother_barcode')]]['child'] = $row[csf('barcode_no')];         
        }
        foreach ($mother_barcode_array as $motherBarcode => $row)
        {
            if($mother_barcode_array[$motherBarcode]['child']!="")
            {
                $childBarcode=$mother_barcode_array[$motherBarcode]['child'];
                $rID5=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_name,$childBarcode)");
            }
        }

        $barcode_no_arr=array_filter($barcode_no_arr);
        if(!empty($barcode_no_arr))
        {
            $all_barcode_nos = implode(",",$barcode_no_arr);
            $all_barcode_no_cond=""; $barCond="";

            //$delivery_qty_arr = return_library_array("select a.barcode_no, a.qnty from pro_roll_details a,tmp_poid b where a.po_breakdown_id=b.poid and b.userid=$user_name and a.entry_form=58 and a.status_active=1 and a.is_deleted=0", "barcode_no", "qnty");

            $delivery_qty_arr = return_library_array("select a.barcode_no, a.qnty from pro_roll_details a,tmp_barcode_no b where a.barcode_no=b.barcode_no and b.userid=$user_name and a.entry_form=58 and a.status_active=1 and a.is_deleted=0", "barcode_no", "qnty");

            $issue_qty_arr = return_library_array("select a.barcode_no, a.qnty from pro_roll_details a,tmp_barcode_no b where a.barcode_no=b.barcode_no and b.userid=$user_name and a.entry_form=61 and a.status_active=1 and a.is_deleted=0", "barcode_no", "qnty");

            $recv_for_batch_qty_arr = return_library_array("select a.barcode_no, a.qnty from pro_roll_details a,tmp_barcode_no b where a.barcode_no=b.barcode_no and b.userid=$user_name and a.entry_form=62 and a.status_active=1 and a.is_deleted=0", "barcode_no", "qnty");

            $deliveryquantityArr = sql_select("select a.booking_no,a.barcode_no,a.qnty as current_delivery from tmp_barcode_no c, pro_roll_details a, pro_grey_prod_delivery_dtls b where c.barcode_no=a.barcode_no and c.userid=$user_name and a.entry_form in(2,56) and a.booking_without_order=0 and a.status_active=1 and a.is_deleted=0 and a.mst_id=b.grey_sys_id group by a.booking_no,a.barcode_no,a.qnty");

            $deliveryStorQtyArr = array();
            foreach ($deliveryquantityArr as $row)
            {
                $deliveryStorQtyArr[$row[csf('booking_no')]] += $row[csf('current_delivery')];
                //$deliveryStorNoOfRollArr[$row[csf('booking_no')]] += $row[csf('roll_no_delv')];
            }
        }

        $colspan = 33;
        $tbl_width = 5100;
        $search_by_arr = array(0 => "All", 1 => "Inside", 3 => "Outside");

        $r_id3=execute_query("delete from tmp_prog_no where userid=$user_name");
        $r_id4=execute_query("delete from tmp_reqs_no where userid=$user_name");
        $r_id5=execute_query("delete from tmp_barcode_no where userid=$user_name");
        $r_id6=execute_query("delete from tmp_booking_id where userid=$user_name");
        $r_id7=execute_query("delete from tmp_prod_id where userid=$user_name");

        oci_commit($con);

        //echo "<br />Execution Time: " . (microtime(true) - $started) . "S";die;

        if ($presentationType == 1 || $presentationType == 5)
        {
           ob_start();
           ?>
            <fieldset style="width:<? echo $tbl_width; ?>px;">
                <table cellpadding="0" cellspacing="0" width="<? echo $tbl_width - 40; ?>">
                    <tr>
                        <td align="center" width="100%" colspan="<? echo $colspan + 7; ?>" style="font-size:16px">
                        <strong>Knitting Plan Report: <? echo $search_by_arr[str_replace("'", "", $type)]; ?></strong></td>
                        <td><input type="hidden" value="<? echo $type; ?>" id="typeForAttention"/></td>
                    </tr>
                </table>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width - 40; ?>" class="rpt_table">
                   <thead>
                        <th width="40"></th>
                        <th width="40">SL</th>
                        <? echo "<th width='100'>Party Name</th>";//if ($type == 3) ?>
                        <th width="60">Program No</th>
                        <th width="70">Req. No</th>
                        <th width="100">Program Status</th>
                        <th width="80">Program Date</th>
                        <th width="80">Start Date</th>
                        <th width="80">T.O.D</th>
                        <th width="120">PO Company</th>
                        <th width="80">PO Buyer</th>
                        <th width="90">Style</th>
                        <th width="130">Sales Order No</th>
                        <th width="100">Fabirc Booking No</th>
                        <th width="100">Job No</th>
                        <th width="100">Internal Ref.</th>
                        <th width="80">M/C no</th>
                        <th width="110">Dia / GG</th>
                        <th width="70">Distribution Qnty</th>
                        <th width="80">Status</th>
                        <th width="150">Fabric Desc.</th>

                        <th width="170">Desc.Of Yarn</th>
                        <th width="130">Supplier<? //echo $company_library[str_replace("'","",$company_name)]; ?></th>
                        <th width="70">Lot</th>
                        <th width="100">Fabric Color</th>
                        <th width="100">Color Range</th>
                        <th width="100">Color Type</th>
                        <th width="100">Stitch Length</th>
                        <th width="100">Sp. Stitch Length</th>
                        <th width="100">Draft Ratio</th>
                        <th width="70">Fabric Gsm</th>
                        <th width="70">Fabric Dia</th>
                        <th width="80">Width/Dia Type</th>
                        <th width="100">Program Qnty</th>
                        <th width="80">Req. Qnty</th>
                        <th width="80">Req. Bal. Qnty</th>
                        <th width="100">Yarn Issue Qnty</th>
                        <th width="100">Issue Return Qnty</th>
                        <th width="100">Reject Qnty</th>
                        <th width="100">Issue. Bal. Qnty</th>
                        <th width="100" title="Without Reject Qty">Knitting Qnty</th>
                        <th width="100" title="With Reject Qty">Knitting QC Qnty</th>

                        <th width="100">Reject Fabric Qnty</th>
                        <th width="100">Knit Balance Qnty</th>
                        <th width="100">Delivery Store</th>
                        <th width="100">Received Qnty</th>
                        <th width="100">Recv. Bal. Qnty</th>

                        <th width="100">Issue to Batch</th>
                        <th width="100">Batch Issue Balance Qnty</th>
                        <th width="100">Receive From Batch</th>
                        <th width="100">Batch Receive  Balance Qnty</th>

                        <th width="80">Knitting Status</th>
                        <th>Remarks</th>
                    </thead>
                </table>
                <div style="width:<? echo $tbl_width - 20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width - 40; ?>" class="rpt_table" id="tbl_list_search">
                        <tbody>
                            <?
                            $i = 1;
                            $k = 1;
                            $tot_program_qnty = 0;
                            $tot_knitting_qnty = 0;
                            $tot_knitting_qc_qnty = 0;
                            $tot_knitting_qnty_in_pcs = 0;
                            $tot_balance = 0;
                            $tot_balance_recv_qnty = 0;
                            $tot_knitting_recv_qnty = 0;
                            $tot_balance_issue_qnty = 0;
                            $tot_knitting_issue_qnty = 0;
                            $tot_balance_rec_for_batch_qnty = 0;
                            $tot_knitting_rec_for_batch_qnty = 0;
                            $machine_dia_gg_array = array();

                            //for rpt template
                            $sql_rpt_tmplt = sql_select("select format_id, template_name, report_id from lib_report_template where module_id=2 and status_active=1 and is_deleted=0 and report_id in(1,2)");
                            $rpt_tmplt_arr = array();
                            foreach($sql_rpt_tmplt as $trow)
                            {
                                $exp_frmt = array();
                                $exp_frmt = explode(",", $trow[csf('format_id')]);
                                if($trow[csf('report_id')] == 1)
                                {
                                    $rpt_tmplt_arr[$trow[csf('template_name')]][1] = $exp_frmt[0];
                                }
                                else
                                {
                                    $rpt_tmplt_arr[$trow[csf('template_name')]][2] = $exp_frmt[0];
                                }
                            }

                            // Fabric Sales Order Entry
                            $print_report_format=return_field_value("format_id"," lib_report_template","template_name in($company_names)  and module_id=7 and report_id=67 and is_deleted=0 and status_active=1");
                            $fReportId=explode(",",$print_report_format);
                            $fReportId=$fReportId[0];

                            $party_array = array();
                            foreach ($nameArray as $row)
                            {
                                if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                                else
                                $bgcolor = "#FFFFFF";
                                $internal_ref = $po_break_dtls_arr[$row[csf('sales_id')]]['GROUPING'];
                                $jobNos = implode(",", array_unique(explode(",", chop($po_break_dtls_arr[$row[csf('sales_id')]]['JOB_NO'],","))));

                                $knitting_source = $row[csf('knitting_source')];
                                if ($knitting_source == 1) {
                                    $knitting_cond = "Inside";
                                    $knitting_party = $company_library[$row[csf('knitting_party')]];
                                } else {
                                    $knitting_cond = "Outside";
                                    $knitting_party = $supplier_details[$row[csf('knitting_party')]];
                                }
                                $machine_dia_gg = $row[csf('machine_dia')] . 'X' . $row[csf('machine_gg')];

                                $machine_no = '';
                                $machine_id = explode(",", $row[csf("machine_id")]);
                                foreach ($machine_id as $val) {
                                    if ($machine_no == '')
                                        $machine_no = $machine_arr[$val];
                                    else
                                        $machine_no .= "," . $machine_arr[$val];
                                }

                                $gmts_color = '';
                                $color_id = explode(",", $row[csf("color_id")]);
                                foreach ($color_id as $val) {
                                    if ($gmts_color == '')
                                        $gmts_color = $color_library[$val];
                                    else
                                        $gmts_color .= "," . $color_library[$val];
                                }

                                $yarn_issue_reject_qnty = 0;
                                $yarnRtnQty = 0;
                                $yarn_issue_qnty = 0;

                                //echo $reqsDataArr[$row[csf('id')]]['prod_id']."=<br>";
                                $prod_id = array_unique(explode(",", $reqsDataArr[$row[csf('id')]]['prod_id']));
                                $yarn_desc = '';
                                $lot = '';
                                $supplier = '';
                                $productId = '';
                                $reqs_no = '';
                                foreach ($prod_id as $val)
                                {
                                    $yarn_desc .= $product_details_arr[$val]['desc'] . ",";
                                    $lot .= $product_details_arr[$val]['lot'] . ",";
                                    $supplier .= $supplier_details[$product_details_arr[$val]['supplier']] . ",";

                                    $productId .= $val . ",";
                                    $req_no_str .= $reqsDataArr[$row[csf('id')]]['reqs_no']. ",";

                                    $yarn_issue_qnty += $yarn_iss_arr[$reqsDataArr[$row[csf('id')]]['reqs_no']][$val];
                                    $yarnRtnQty += $yarn_IssRtn_arr[$reqsDataArr[$row[csf('id')]]['reqs_no']][$val];
                                    //$actual_yarn_issue_qnty= $yarn_iss_arr[$reqsDataArr[$row[csf('id')]]['reqs_no']][$val] - $yarnRtnQty;
                                    $actual_yarn_issue_qnty=  $yarn_issue_qnty - $yarnRtnQty;
                                    $yarn_issue_reject_qnty += $yarn_IssRej_arr[$reqsDataArr[$row[csf('id')]]['reqs_no']][$val];
                                    //echo $yarn_iss_arr[$reqsDataArr[$row[csf('id')]]['reqs_no']][$val] ."-". $yarnRtnQty."<br/>";


                                }


                                $yarn_desc = explode(",", substr($yarn_desc, 0, -1));
                                $lot = explode(",", substr($lot, 0, -1));
                                $supplier = explode(",", substr($supplier, 0, -1));
                                $po_id = array_unique(explode(",", $plan_details_array[$row[csf('id')]]));
                                //var_dump($po_id);
                                $style_ref = '';
                                $sales_order_no = '';
                                $sale_booking_no = '';

                                $style_ref = $row[csf('style_ref_no')];
                                $sale_booking_no = $row[csf('sales_booking_no')];
                                $booking_entry_form = $row[csf('booking_entry_form')];
                                $sales_order_no = $row[csf('job_no')];

                                $booking_company=$booking_Arr[$sale_booking_no]['booking_company_id'];
                                $booking_order_id=$booking_Arr[$sale_booking_no]['booking_order_id'];
                                $booking_fabric_natu=$booking_Arr[$sale_booking_no]['booking_fabric_natu'];
                                $booking_fabric_source=$booking_Arr[$sale_booking_no]['booking_fabric_source'];
                                $booking_job_no=$booking_Arr[$sale_booking_no]['booking_job_no'];
                                $is_approved_id=$booking_Arr[$sale_booking_no]['is_approved'];

                                // Budget Wise Fabric Booking and Main Fabric Booking V2
                                $fReportId2 = $rpt_tmplt_arr[$booking_company][1];

                                // Short Fabric Booking
                                $fReportId3 = $rpt_tmplt_arr[$booking_company][2];

                                if ($booking_entry_form==86 || $booking_entry_form==118)
                                {// Budget Wise Fabric Booking and Main Fabric Booking V2
                                    $fbReportId=$fReportId2;
                                }
                                else if($booking_entry_form==88)
                                {
                                    $fbReportId=$fReportId3;// Short Fabric Booking
                                }

                                $knitting_qc_qnty=$knitQcDataArr[$row[csf('id')]]['qnty'];

                                $knitting_qnty = $knitDataArr[$row[csf('id')]]['qnty'] + $knit_recvProg_qty_arr[$row[csf('id')]];
                                $knitting_qnty_in_pcs = $knitDataArr[$row[csf('id')]]['grey_receive_qnty_pcs'];

                                $knit_id = $knitDataArr[$row[csf('id')]]['knit_id'];

                                $knit_id = array_unique(explode(",", $knit_id));
                                $fabric_reject_qnty = $knitDataArr[$row[csf('id')]]['reject_qnty'];


                                $knit_recv_qty = 0;$knit_issue_qty = 0;$knit_recv_for_batch_qty = 0;
                                $knitting_recv_qnty = 0;$knitting_issue_qnty = 0;$knitting_recv_for_batch_qnty = 0;
                                foreach ($knit_id as $val) {
                                    $delivery_qty = 0;$issue_qty = 0; $recv_for_batch_qty = 0;
                                    $barcode_nos = explode(",", chop($barcode_arr[$val], ','));
                                    foreach ($barcode_nos as $barcode_no) {
                                        $delivery_qty += $delivery_qty_arr[$barcode_no];
                                        $issue_qty += $issue_qty_arr[$barcode_no];
                                        $recv_for_batch_qty += $recv_for_batch_qty_arr[$barcode_no];

                                        if($mother_barcode_array[$barcode_no]['child']!="")
                                        {
                                            $barcode_number[]= $mother_barcode_array[$barcode_no]['child'];
                                        }
                                    }
                                    $knit_recv_qty += $knitting_recv_qnty_array[$val] + $delivery_qty;
                                    $knit_issue_qty += $knitting_issue_qnty_array[$val] + $issue_qty;
                                    $knit_recv_for_batch_qty += $recv_for_batch_qty;
                                    //echo $delivery_qty."<br/>";
                                }
                                foreach ($barcode_number as $barcode_noss) 
                                {
                                     $knit_issue_qty += $issue_qty_arr[$barcode_noss];    
                                     $knit_recv_for_batch_qty += $recv_for_batch_qty_arr[$barcode_noss];
                                }

                                $knitting_recv_qnty = $knit_recv_qty + $knit_recvProg_qty_arr[$row[csf('id')]];
                                $knitting_issue_qnty = $knit_issue_qty + $knit_issueProg_qty_arr[$row[csf('id')]];
                                $knitting_recv_for_batch_qnty = $knit_recv_for_batch_qty ;
                                //echo $knit_recv_qty .'+'. $knit_recvProg_qty_arr[$row[csf('id')]]."<br/>";

                                $balance_qnty = $row[csf('program_qnty')] - $knitting_qnty;
                                $balance_recv_qnty = $knitting_qnty - $knitting_recv_qnty;
                                $balance_issue_qnty = $knitting_recv_qnty - $knitting_issue_qnty;
                                $balance_recv_for_batch_qnty = $knitting_issue_qnty - $knitting_recv_for_batch_qnty;

                                //echo $row[csf('program_qnty')] ."-". $actual_yarn_issue_qnty."<br/>";

                                $complete = '&nbsp;';
                                if ($knitting_qnty >= $row[csf('program_qnty')])
                                $complete = 'Complete';

                                //for req qty
                                $req_qnty = $reqsDataArr[$row[csf('id')]]['reqs_qty'];
                                $req_bal_qnty = $row[csf('program_qnty')]-$req_qnty;
                                $req_id_str = implode(',',$reqsDataArr[$row[csf('id')]]['reqs_id']);

                                //$yarn_issue_bl_qnty = $row[csf('program_qnty')] - $actual_yarn_issue_qnty;
                                $yarn_issue_bl_qnty = $req_qnty - $actual_yarn_issue_qnty;

                                //for po company and buyer
                                $po_company = '';
                                $po_buyer = '';
                                if($row[csf('within_group')]==1)
                                {
                                    //$po_company = $company_library[$row[csf('po_company_id')]];
                                    $po_company = $company_library[$row[csf('sales_buyer')]];
                                    $po_buyer = $buyer_arr[$booking_Arr[$sale_booking_no]['buyer_id']];

                                    if($po_buyer=="")
                                    {
                                         $po_buyer = $buyer_arr[$po_break_dtls_arr[$row[csf('sales_id')]]['PO_BUYER']];
                                    }
                                }
                                else
                                {
                                    $po_buyer = $buyer_arr[$row[csf('sales_buyer')]];
                                }
                                //end for po company and buyer

                                //=========== For Program Status =================

                                $program_status = '';
                                if($row[csf('status_active')]==1 && $row[csf('is_deleted')]==0 && $row[csf('is_revised')]==0)
                                {
                                    $program_status = "Active";
                                }
                                else if($row[csf('status_active')]==0 && $row[csf('is_deleted')]==1 && $row[csf('is_revised')]==0)
                                {
                                    $program_status = "Delete";
                                }
                                else if($row[csf('status_active')]==1 && $row[csf('is_deleted')]==0 && $row[csf('is_revised')]==1)
                                {
                                    $program_status = "Revised";
                                }

                                if (!in_array($machine_dia_gg, $machine_dia_gg_array[$knitting_source]))
                                {
                                    if ($k != 1)
                                    {
                                        ?>
                                        <tr bgcolor="#CCCCCC">
                                            <td colspan="<? echo $colspan; ?>" align="right"><b>Sub Total</b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_program_qnty, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_req_qnty, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_req_bal_qnty, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_yarn_issue_qnty, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? //echo number_format($sub_yarn_issue_qnty, 2, '.', ''); ?>retrn</b></td>
                                            <td align="right">
                                            <b><? //echo number_format($sub_yarn_issue_qnty, 2, '.', ''); ?>rect</b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_yarn_issue_bl_qnty, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_knitting_qnty, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_knitting_qc_qnty, 2, '.', ''); ?></b></td>

                                            <td align="right">
                                            <b><? //echo number_format($sub_tot_knitting_qnty, 2, '.', ''); ?>reject</b></td>
                                            <td align="right"><b><? echo number_format($sub_tot_balance, 2, '.', ''); ?></b>
                                            <td align="right"><b><? //echo number_format($sub_tot_balance, 2, '.', ''); ?>store</b>
                                            </td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_knitting_recv_qnty, 2, '.', ''); ?></b>
                                            </td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_recv_balance, 2, '.', ''); ?></b></td>


                                            <td align="right">
                                            <b><? echo number_format($sub_tot_knitting_issue_qnty, 2, '.', ''); ?></b>
                                            </td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_issue_balance, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_knitting_rec_for_batch_qnty, 2, '.', ''); ?></b>
                                            </td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_rec_for_batch_balance, 2, '.', ''); ?></b></td>



                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <?
                                        $sub_tot_program_qnty = 0;
                                        $sub_tot_req_qnty = 0;
                                        $sub_tot_req_bal_qnty = 0;
                                        $sub_tot_knitting_qnty = 0;
                                        $sub_tot_knitting_qc_qnty = 0;
                                        $sub_tot_knitting_qnty_in_pcs = 0;
                                        $sub_tot_balance = 0;
                                        $sub_yarn_issue_qnty = 0;
                                        $sub_yarn_issue_rtn_qnty = 0;
                                        $sub_yarn_issue_rej_qnty = 0;
                                        $sub_yarn_issue_bl_qnty = 0;
                                        $sub_fab_rej_qnty = 0;
                                        $sub_delv_store_qnty = 0;

                                        $sub_tot_knitting_recv_qnty = 0;
                                        $sub_tot_recv_balance = 0;

                                        $sub_tot_knitting_issue_qnty = 0;
                                        $sub_tot_issue_balance = 0;
                                        $sub_tot_knitting_rec_for_batch_qnty = 0;
                                        $sub_tot_rec_for_batch_balance = 0;


                                    }

                                    // if(!in_array($knitting_cond,$party_array))
                                    if ($knitting_source_tmp[$knitting_source] == '') {
                                        $knitting_source_tmp[$knitting_source] = $knitting_source;
                                        ?>
                                        <tr bgcolor="#C6D9C9">
                                            <td style="font-weight: 900" colspan="<? echo $colspan + 9+8+3; ?>"><b><?php echo $knitting_cond; ?></b></td>
                                        </tr>
                                        <?
                                        $party_array[] = $knitting_cond;
                                    }
                                    ?>
                                    <tr bgcolor="#EFEFEF">
                                        <td colspan="<? echo $colspan + 9+8+3; ?>"><b>Machine Dia:- <?php echo $machine_dia_gg; ?></b></td>
                                    </tr>
                                    <?
                                    $machine_dia_gg_array[$knitting_source][] = $machine_dia_gg;
                                    $k++;
                                }
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                    <td width="40" align="center" valign="middle">
                                    <input type="checkbox" id="tbl_<? echo $i; ?>" name="check[]"
                                    onClick="selected_row(<? echo $i; ?>);"/>
                                    <input id="promram_id_<? echo $i; ?>" name="promram_id[]" type="hidden"
                                    value="<? echo $row[csf('id')]; ?>"/>
                                    <input id="job_no_<? echo $i; ?>" name="job_no[]" type="hidden"
                                    value="<? echo $job_no; ?>"/>
                                    <input id="source_id_<? echo $i; ?>" name="source_id_[]" type="hidden"
                                    value="<? echo $knitting_source; ?>"/>
                                    <input id="party_id_<? echo $i; ?>" name="party_id_[]" type="hidden"
                                    value="<? echo $row[csf('knitting_party')]; ?>"/>
                                    </td>
                                    <td width="40"><? echo $i; ?></td>
                                    <? //echo $knitting_party;//"<td width='100'><p><a href='##' onclick=\"generate_report_party(" . $row[csf('company_id')] . "," . $row[csf('id')] . ")\">" . $knitting_party . "</a></p></td>"; echo $knitting_party;//if ($type == 3)  ?>
                                    <td width="100"><p><? echo $knitting_party; ?></p></td>
                                    <td width="60" align="center"><a href='##'
                                    onClick="generate_report2(<? echo $row[csf('company_id')] . "," . $row[csf('id')]; ?>)"><? echo $row[csf('id')]; ?>
                                    &nbsp;</a></td>
                                    <td width="70" align="center" title="<? echo $row[csf('id')];?>"><? echo $reqsDataArr[$row[csf('id')]]['reqs_no']; ?>
                                    &nbsp;</td>
                                    <td width="100" align="center">
                                    &nbsp;<? echo $program_status; ?></td>
                                    <td width="80" align="center">
                                    &nbsp;<? echo change_date_format($row[csf('program_date')]); ?></td>
                                    <td width="80" align="center">
                                    &nbsp;<? if ($row[csf('start_date')] != "0000-00-00") echo change_date_format($row[csf('start_date')]); ?></td>
                                    <td width="80" align="center">
                                    &nbsp;<? if ($row[csf('end_date')] != "0000-00-00") echo change_date_format($row[csf('end_date')]); ?></td>
                                    <td width="120"><p><? echo $po_company; ?></p></td>
                                    <td width="80"><p><? echo $po_buyer; ?></p></td>
                                    <td width="90" align="center"><p><? echo $style_ref; ?></p></td>
                                    <td width="130">
                                    <div style="word-wrap:break-word; width:129px">
                                    <? if ($type == 3) echo $sales_order_no;
                                    else echo "<a href='##' onclick=\"generate_report(" . $row[csf('company_id')] . ",'" . $sale_booking_no . "','" . $sale_booking_no . "','" . $sales_order_no . "','" . $fReportId . "' )\">$sales_order_no</a>"; ?>
                                    </div>
                                    </td>

                                    <? if($sale_booking_no !='' && $booking_order_id !='' && $booking_job_no !='' && $booking_entry_form !='')
                                    { ?>
                                        <td width="100" title="<? echo $booking_entry_form;?>"><p><? echo "<a href='##' onclick=\"generate_booking_report('".$sale_booking_no."',".$booking_company.",'".$booking_order_id."',".$booking_fabric_natu.",".$booking_fabric_source.",".$is_approved_id.",'".$booking_job_no."','".$booking_entry_form."','".$fbReportId."' )\">$sale_booking_no</a>"; ?>&nbsp;</p></td>
                                    <? } else { ?>
                                        <td width="100" title=""><p><? echo $sale_booking_no; ?>&nbsp;</p></td>
                                    <? } ?>

                                    <td width="100" align="center"><p><? echo $jobNos; ?>&nbsp;</p></td>
                                    <td width="100" align="center"><p><? echo $internal_ref; ?>&nbsp;</p></td>
                                    <td width="80"><p><? echo $machine_no; ?>&nbsp;</p></td>
                                    <td width="110"><p><? echo $machine_dia_gg; ?></p></td>
                                    <td width="70"
                                    align="center"><? echo number_format($row[csf('distribution_qnty')], 2); ?>
                                    &nbsp;</td>
                                    <td width="80"><p><? echo $knitting_program_status[$row[csf('status')]]; ?></p></td>
                                    <td align="right" width="150"><? echo $row[csf('fabric_desc')];////correct upto ?></td>
                                    <td width="170"><p><? echo join(",", array_unique($yarn_desc)); ?>&nbsp;</p></td>
                                    <td width="130">
                                    <div style="word-wrap:break-word; width:130px"><? echo implode(", ", array_unique($supplier)); ?>
                                    &nbsp;</div>
                                    </td>
                                    <td width="70"><p><? echo join(", ", array_unique($lot)); ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo $gmts_color; ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo $color_range[$row[csf('color_range')]]; ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo $color_type[$row[csf('color_type_id')]]; ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo $row[csf('spandex_stitch_length')]; ?>&nbsp;</p></td>
                                    <td align="right" width="100"><? echo number_format($row[csf('draft_ratio')], 2); ?>
                                    &nbsp;</td>
                                    <td width="70"><p><? echo $row[csf('gsm_weight')]; ?>&nbsp;</p></td>
                                    <td width="70"><p><? echo $row[csf('dia')]; ?>&nbsp;</p></td>
                                    <td width="80"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?>&nbsp;</td>
                                    <td align="right" width="100"><? echo number_format($row[csf('program_qnty')], 2); ?></td>
                                    <td align="right" width="80"><a href='##' onClick="func_req_qty('<? echo $req_id_str; ?>')"><? echo number_format($req_qnty, 2); ?></a></td>
                                    <td align="right" width="80" title="(Program Qnty-Req. Qnty)"><? echo number_format($req_bal_qnty, 2); ?></td>
                                    <td align="right" width="100"><a href='##' onClick="func_issue_qty('<? echo $reqsDataArr[$row[csf('id')]]['reqs_no']; ?>')"><? echo number_format($yarn_issue_qnty, 2); ?></a></td>

                                    <!-- echo chop($req_no_str,','); -->

                                    <td align="right" width="100"><a href='##' onClick="func_issue_rtn_qty('<? echo $reqsDataArr[$row[csf('id')]]['reqs_no']; ?>')"><? echo number_format($yarnRtnQty, 2); ?></a></td>
                                    <td align="right" width="100"><? echo number_format($yarn_issue_reject_qnty, 2); ?></td>
                                    <td align="right" width="100" title="Program Qnty - (yarn issue-issue Return)"><? echo number_format($yarn_issue_bl_qnty, 2); ?></td>
                                    <td align="right" width="100" title="<?=$row[csf('id')];?>">
                                    <a  href='##' onClick="openmypage_popup('<? echo $row[csf('id')];?>','knitting_popup','<? echo $row[csf('company_id')];?>')"><? echo number_format($knitting_qnty,2,".","");?></a>
                                    </td>
                                    <td align="right" width="100">
                                    <a  href='##' onClick="openmypage_popup('<? echo $row[csf('id')];?>','knitting_qc_popup','<? echo $row[csf('company_id')];?>')"><? echo number_format($knitting_qc_qnty,2,".","");?></a>
                                    </td>

                                    <td align="right" width="100"><? echo number_format($fabric_reject_qnty, 2); ?></td>
                                    <td align="right" width="100"><? echo number_format($balance_qnty, 2); ?></td>
                                    <td align="right" width="100"><a href="##" onClick="openmypage_popup('<? echo $row[csf('id')];?>','grey_purchase_delivery','<? echo $row[csf('company_id')];?>')">

                                    <? echo number_format($deliveryStorQtyArr[$row[csf('id')]],2); $delv_store=$deliveryStorQtyArr[$row[csf('id')]];?>
                                    </a>
                                    </td>
                                    <td align="right" width="100">
                                    <a href="##" onClick="openmypage_popup('<? echo $row[csf('id')];?>','grey_receive_popup','<? echo $row[csf('company_id')];?>')"><? echo number_format($knitting_recv_qnty,2);?></a>
                                    </td>
                                    <td align="right" width="100"><? echo number_format($balance_recv_qnty, 2); ?></td>


                                    <td align="right" width="100">
                                    <a href="##" onClick="openmypage_popup('<? echo $row[csf('id')];?>','grey_issue_popup','<? echo $row[csf('company_id')];?>')"><? echo number_format($knitting_issue_qnty,2);?></a>
                                    </td>
                                    <td align="right" width="100"><? echo number_format($balance_issue_qnty, 2); ?></td>

                                    <td align="right" width="100">
                                    <a href="##" onClick="openmypage_popup('<? echo $row[csf('id')];?>','grey_recv_for_batch_popup','<? echo $row[csf('company_id')];?>')"><? echo number_format($knitting_recv_for_batch_qnty,2);?></a>
                                    </td>
                                    <td align="right" width="100"><? echo number_format($balance_recv_for_batch_qnty, 2); ?></td>

                                    <td align="center" width="80"><? echo $complete; ?></td>
                                    <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                                </tr>
                                <?
                                $sub_tot_program_qnty += $row[csf('program_qnty')];
                                $sub_tot_req_qnty += $req_qnty;
                                $sub_tot_req_bal_qnty += $req_bal_qnty;
                                $sub_tot_knitting_qnty += $knitting_qnty;
                                $sub_tot_knitting_qc_qnty += $knitting_qc_qnty;
                                $sub_tot_knitting_qnty_in_pcs += $knitting_qnty_in_pcs;
                                $sub_tot_balance += $balance_qnty;
                                $sub_tot_knitting_recv_qnty += $knitting_recv_qnty;
                                $sub_tot_recv_balance += $balance_recv_qnty;

                                $sub_tot_knitting_issue_qnty += $knitting_issue_qnty;
                                $sub_tot_issue_balance += $balance_issue_qnty;
                                $sub_tot_knitting_rec_for_batch_qnty += $knitting_recv_for_batch_qnty;
                                $sub_tot_rec_for_batch_balance += $balance_recv_for_batch_qnty;

                                $sub_yarn_issue_qnty += $yarn_issue_qnty;
                                $sub_yarn_issue_rtn_qnty += $yarnRtnQty;
                                $sub_yarn_issue_rej_qnty += $yarn_issue_reject_qnty;
                                $sub_yarn_issue_bl_qnty += $yarn_issue_bl_qnty;
                                $sub_fab_rej_qnty += $fabric_reject_qnty;
                                $sub_delv_store_qnty += $delv_store;

                                $tot_program_qnty += $row[csf('program_qnty')];
                                $tot_req_qnty += $req_qnty;
                                $tot_req_bal_qnty += $req_bal_qnty;
                                $tot_knitting_qnty += $knitting_qnty;
                                $tot_knitting_qc_qnty += $knitting_qc_qnty;
                                $tot_knitting_qnty_in_pcs += $knitting_qnty_in_pcs;
                                $tot_balance += $balance_qnty;
                                $tot_knitting_recv_qnty += $knitting_recv_qnty;
                                $tot_balance_recv_qnty += $balance_recv_qnty;

                                $tot_knitting_issue_qnty += $knitting_issue_qnty;
                                $tot_balance_issue_qnty += $balance_issue_qnty;
                                $tot_knitting_rec_for_batch_qnty += $knitting_recv_for_batch_qnty;
                                $tot_balance_rec_for_batch_qnty += $balance_recv_for_batch_qnty;

                                $tot_yarn_issue_qnty += $yarn_issue_qnty;
                                $tot_yarn_issue_rtn_qnty += $yarnRtnQty;
                                $tot_yarn_issue_rej_qnty += $yarn_issue_reject_qnty;
                                $tot_yarn_issue_bl_qnty += $yarn_issue_bl_qnty;
                                $tot_fab_rej_qnty += $fabric_reject_qnty;
                                $tot_delv_store_qnty += $delv_store;

                                $i++;
                            }
                            if ($i > 1)
                            {
                                ?>
                                <tr bgcolor="#CCCCCC">
                                    <td colspan="<? echo $colspan; ?>" align="right"><b>Sub Total</b></td>
                                    <td align="right"><b><? echo number_format($sub_tot_program_qnty, 2, '.', ''); ?></b></td>
                                    <td align="right"><b><? echo number_format($sub_tot_req_qnty, 2, '.', ''); ?></b></td>
                                    <td align="right"><b><? echo number_format($sub_tot_req_bal_qnty, 2, '.', ''); ?></b></td>
                                    <td align="right"><b><? echo number_format($sub_yarn_issue_qnty, 2, '.', ''); ?></b>
                                    <td align="right"><b><? echo number_format($sub_yarn_issue_rtn_qnty, 2, '.', ''); ?></b>
                                    <td align="right"><b><? echo number_format($sub_yarn_issue_rej_qnty, 2, '.', ''); ?></b>
                                    </td>
                                    <td align="right"><b><? echo number_format($sub_yarn_issue_bl_qnty, 2, '.', ''); ?></b>
                                    </td>
                                    <td align="right"><b><? echo number_format($sub_tot_knitting_qnty, 2, '.', ''); ?></b>
                                    <td align="right"><b><? echo number_format($sub_tot_knitting_qc_qnty, 2, '.', ''); ?></b>

                                    <td align="right"><b><? echo number_format($sub_fab_rej_qnty, 2, '.', ''); ?></b>
                                    </td>
                                    <td align="right"><b><? echo number_format($sub_tot_balance, 2, '.', ''); ?></b></td>
                                    <td align="right"><b><? echo number_format($sub_delv_store_qnty, 2, '.', ''); ?></b></td>
                                    <td align="right">
                                    <b><? echo number_format($sub_tot_knitting_recv_qnty, 2, '.', ''); ?></b></td>
                                    <td align="right"><b><? echo number_format($sub_tot_recv_balance, 2, '.', ''); ?></b>
                                    </td>

                                    <td align="right">
                                    <b><? echo number_format($sub_tot_knitting_issue_qnty, 2, '.', ''); ?></b></td>
                                    <td align="right"><b><? echo number_format($sub_tot_issue_balance, 2, '.', ''); ?></b>
                                    </td>
                                    <td align="right">
                                    <b><? echo number_format($sub_tot_knitting_rec_for_batch_qnty, 2, '.', ''); ?></b></td>
                                    <td align="right"><b><? echo number_format($sub_tot_rec_for_batch_balance, 2, '.', ''); ?></b>
                                    </td>

                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <?
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <th colspan="<? echo $colspan; ?>" align="right">Grand Total</th>
                            <th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?></th>
                            <th align="right"><b><? echo number_format($tot_req_qnty, 2, '.', ''); ?></b></th>
                            <th align="right"><b><? echo number_format($tot_req_bal_qnty, 2, '.', ''); ?></b></th>
                            <th align="right"><? echo number_format($tot_yarn_issue_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_yarn_issue_rtn_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_yarn_issue_rej_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_yarn_issue_bl_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_knitting_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_knitting_qc_qnty, 2, '.', ''); ?></th>

                            <th align="right"><? echo number_format($tot_fab_rej_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_balance, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_delv_store_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_knitting_recv_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_balance_recv_qnty, 2, '.', ''); ?></th>

                            <th align="right"><? echo number_format($tot_knitting_issue_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_balance_issue_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_knitting_rec_for_batch_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_balance_rec_for_batch_qnty, 2, '.', ''); ?></th>

                            <th></th>
                            <th></th>
                        </tfoot>
                    </table>
                </div>
            </fieldset>
            <?
        }
        else if ($presentationType == 6)
        {
            $colspan = 33;
            $tbl_width = 5500;
           ob_start();
           ?>
            <fieldset style="width:<? echo $tbl_width; ?>px;">
                <table cellpadding="0" cellspacing="0" width="<? echo $tbl_width - 40; ?>">
                    <tr>
                        <td align="center" width="100%" colspan="<? echo $colspan + 7; ?>" style="font-size:16px">
                        <strong>Knitting Plan Report: <? echo $search_by_arr[str_replace("'", "", $type)]; ?></strong></td>
                        <td><input type="hidden" value="<? echo $type; ?>" id="typeForAttention"/></td>
                    </tr>
                </table>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width - 40; ?>" class="rpt_table">
                   <thead>
                        <th width="40"></th>
                        <th width="40">SL</th>
                        <th width='100'>Party Name</th>
                        <th width="60">Program No</th>
                        <th width="70">Req. No</th>
                        <th width="100">Program Status</th>
                        <th width="80">Program Date</th>
                        <th width="80">Start Date</th>
                        <th width="80">T.O.D</th>
                        <th width="120">PO Company</th>
                        <th width="80">PO Buyer</th>
                        <th width="90">Style</th>
                        <th width="130">Sales Order No</th>
                        <th width="100">Fabirc Booking No</th>
                        <th width="100">Job No</th>
                        <th width="100">Internal Ref.</th>
                        <th width="80">M/C no</th>
                        <th width="110">Dia / GG</th>
                        <th width="70">Distribution Qnty</th>
                        <th width="80">Status</th>
                        <th width="150">Fabric Desc.</th>

                        <th width="170">Desc.Of Yarn</th>
                        <th width="130">Supplier</th>
                        <th width="70">Lot</th>
                        <th width="100">Fabric Color</th>
                        <th width="100">Color Range</th>
                        <th width="100">Color Type</th>
                        <th width="100">Stitch Length</th>
                        <th width="100">Sp. Stitch Length</th>
                        <th width="100">Draft Ratio</th>
                        <th width="70">Fabric Gsm</th>
                        <th width="70">Fabric Dia</th>
                        <th width="80">Width/Dia Type</th>
                        <th width="100">Program Qnty</th>
                        <th width="80">Req. Qnty</th>
                        <th width="80">Req. Bal. Qnty</th>
                        <th width="100">Yarn Issue Qnty</th>
                        <th width="100">Issue Return Qnty</th>
                        <th width="100">Reject Qnty</th>
                        <th width="100">Issue. Bal. Qnty</th>
                        <th width="100">Knitting Production Qty</th>
                        <th width="100">Knitting QC Qnty</th>
                        <th width="100">QC Pass Qty</th>

                        <th width="100">Reject Fabric Qnty</th>
                        <th width="100">Held Up Qty</th>
                        <th width="100">QC Balance Qty</th>
                        <th width="100">Knit Balance Qnty</th>
                        <th width="100">Delivery Store</th>
                        <th width="100">Received Qnty</th>
                        <th width="100">Recv. Bal. Qnty</th>

                        <th width="100">Issue to Batch</th>
                        <th width="100">Batch Issue Balance Qnty</th>
                        <th width="100">Receive From Batch</th>
                        <th width="100">Batch Receive  Balance Qnty</th>
                        <th width="100">Batch Qnty</th>
                        <th width="80">Knitting Status</th>
                        <th>Remarks</th>
                    </thead>
                </table>
                <div style="width:<? echo $tbl_width - 20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width - 40; ?>" class="rpt_table" id="tbl_list_search">
                        <tbody>
                            <?
                            $i = 1;
                            $k = 1;
                            $tot_program_qnty = 0;
                            $tot_knitting_qnty = 0;
                            $tot_knitting_qc_qnty = 0;
                            $tot_knitting_qnty_in_pcs = 0;
                            $tot_balance = 0;
                            $tot_balance_recv_qnty = 0;
                            $tot_knitting_recv_qnty = 0;
                            $tot_balance_issue_qnty = 0;
                            $tot_knitting_issue_qnty = 0;
                            $tot_balance_rec_for_batch_qnty = 0;
                            $tot_knitting_rec_for_batch_qnty = 0;
                            $machine_dia_gg_array = array();

                            //for rpt template
                            $sql_rpt_tmplt = sql_select("select format_id, template_name, report_id from lib_report_template where module_id=2 and status_active=1 and is_deleted=0 and report_id in(1,2)");
                            $rpt_tmplt_arr = array();
                            foreach($sql_rpt_tmplt as $trow)
                            {
                                $exp_frmt = array();
                                $exp_frmt = explode(",", $trow[csf('format_id')]);
                                if($trow[csf('report_id')] == 1)
                                {
                                    $rpt_tmplt_arr[$trow[csf('template_name')]][1] = $exp_frmt[0];
                                }
                                else
                                {
                                    $rpt_tmplt_arr[$trow[csf('template_name')]][2] = $exp_frmt[0];
                                }
                            }

                            // Fabric Sales Order Entry
                            $print_report_format=return_field_value("format_id"," lib_report_template","template_name in($company_names)  and module_id=7 and report_id=67 and is_deleted=0 and status_active=1");
                            $fReportId=explode(",",$print_report_format);
                            $fReportId=$fReportId[0];

                            $party_array = array();
                            foreach ($nameArray as $row)
                            {
                                if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                                else
                                $bgcolor = "#FFFFFF";
                                $internal_ref = $po_break_dtls_arr[$row[csf('sales_id')]]['GROUPING'];
                                $jobNos = implode(",", array_unique(explode(",", chop($po_break_dtls_arr[$row[csf('sales_id')]]['JOB_NO'],","))));

                                $knitting_source = $row[csf('knitting_source')];
                                if ($knitting_source == 1) {
                                    $knitting_cond = "Inside";
                                    $knitting_party = $company_library[$row[csf('knitting_party')]];
                                } else {
                                    $knitting_cond = "Outside";
                                    $knitting_party = $supplier_details[$row[csf('knitting_party')]];
                                }
                                $machine_dia_gg = $row[csf('machine_dia')] . 'X' . $row[csf('machine_gg')];

                                $machine_no = '';
                                $machine_id = explode(",", $row[csf("machine_id")]);
                                foreach ($machine_id as $val) {
                                    if ($machine_no == '')
                                        $machine_no = $machine_arr[$val];
                                    else
                                        $machine_no .= "," . $machine_arr[$val];
                                }

                                $gmts_color = '';
                                $color_id = explode(",", $row[csf("color_id")]);
                                foreach ($color_id as $val) {
                                    if ($gmts_color == '')
                                        $gmts_color = $color_library[$val];
                                    else
                                        $gmts_color .= "," . $color_library[$val];
                                }

                                $yarn_issue_reject_qnty = 0;
                                $yarnRtnQty = 0;
                                $yarn_issue_qnty = 0;

                                //echo $reqsDataArr[$row[csf('id')]]['prod_id']."=<br>";
                                $prod_id = array_unique(explode(",", $reqsDataArr[$row[csf('id')]]['prod_id']));
                                $yarn_desc = '';
                                $lot = '';
                                $supplier = '';
                                $productId = '';
                                $reqs_no = '';
                                foreach ($prod_id as $val)
                                {
                                    $yarn_desc .= $product_details_arr[$val]['desc'] . ",";
                                    $lot .= $product_details_arr[$val]['lot'] . ",";
                                    $supplier .= $supplier_details[$product_details_arr[$val]['supplier']] . ",";

                                    $productId .= $val . ",";
                                    $req_no_str .= $reqsDataArr[$row[csf('id')]]['reqs_no']. ",";

                                    $yarn_issue_qnty += $yarn_iss_arr[$reqsDataArr[$row[csf('id')]]['reqs_no']][$val];
                                    $yarnRtnQty += $yarn_IssRtn_arr[$reqsDataArr[$row[csf('id')]]['reqs_no']][$val];
                                    //$actual_yarn_issue_qnty= $yarn_iss_arr[$reqsDataArr[$row[csf('id')]]['reqs_no']][$val] - $yarnRtnQty;
                                    $actual_yarn_issue_qnty=  $yarn_issue_qnty - $yarnRtnQty;
                                    $yarn_issue_reject_qnty += $yarn_IssRej_arr[$reqsDataArr[$row[csf('id')]]['reqs_no']][$val];
                                    //echo $yarn_iss_arr[$reqsDataArr[$row[csf('id')]]['reqs_no']][$val] ."-". $yarnRtnQty."<br/>";


                                }


                                $yarn_desc = explode(",", substr($yarn_desc, 0, -1));
                                $lot = explode(",", substr($lot, 0, -1));
                                $supplier = explode(",", substr($supplier, 0, -1));
                                $po_id = array_unique(explode(",", $plan_details_array[$row[csf('id')]]));
                                //var_dump($po_id);
                                $style_ref = '';
                                $sales_order_no = '';
                                $sale_booking_no = '';

                                $style_ref = $row[csf('style_ref_no')];
                                $sale_booking_no = $row[csf('sales_booking_no')];
                                $booking_entry_form = $row[csf('booking_entry_form')];
                                $sales_order_no = $row[csf('job_no')];

                                $booking_company=$booking_Arr[$sale_booking_no]['booking_company_id'];
                                $booking_order_id=$booking_Arr[$sale_booking_no]['booking_order_id'];
                                $booking_fabric_natu=$booking_Arr[$sale_booking_no]['booking_fabric_natu'];
                                $booking_fabric_source=$booking_Arr[$sale_booking_no]['booking_fabric_source'];
                                $booking_job_no=$booking_Arr[$sale_booking_no]['booking_job_no'];
                                $is_approved_id=$booking_Arr[$sale_booking_no]['is_approved'];

                                // Budget Wise Fabric Booking and Main Fabric Booking V2
                                $fReportId2 = $rpt_tmplt_arr[$booking_company][1];

                                // Short Fabric Booking
                                $fReportId3 = $rpt_tmplt_arr[$booking_company][2];

                                if ($booking_entry_form==86 || $booking_entry_form==118)
                                {// Budget Wise Fabric Booking and Main Fabric Booking V2
                                    $fbReportId=$fReportId2;
                                }
                                else if($booking_entry_form==88)
                                {
                                    $fbReportId=$fReportId3;// Short Fabric Booking
                                }

                                $knitting_qc_qnty=$knitQcDataArr[$row[csf('id')]]['qnty'];

                                $qc_pass_qnty = $knitQcDataArr[$row[csf('id')]]['qc_pass_qnty'];
                                $held_up_qnty = $knitQcDataArr[$row[csf('id')]]['held_up_qnty'];
                                $batch_qnty = $batchDataArr[$row[csf('id')]]['batch_qnty'];

                                //$knitting_qnty = $knitDataArr[$row[csf('id')]]['qnty'] + $knit_recvProg_qty_arr[$row[csf('id')]];
                                $knitting_qnty = $knitDataArr[$row[csf('id')]]['qnty'] + $knitDataArr[$row[csf('id')]]['reject_qnty'];
                                $qc_balance =  $knitting_qnty-$knitting_qc_qnty;
                                $knitting_qnty_in_pcs = $knitDataArr[$row[csf('id')]]['grey_receive_qnty_pcs'];

                                $knit_id = $knitDataArr[$row[csf('id')]]['knit_id'];

                                $knit_id = array_unique(explode(",", $knit_id));
                                $fabric_reject_qnty = $knitDataArr[$row[csf('id')]]['reject_qnty'];


                                $knit_recv_qty = 0;$knit_issue_qty = 0;$knit_recv_for_batch_qty = 0;
                                $knitting_recv_qnty = 0;$knitting_issue_qnty = 0;$knitting_recv_for_batch_qnty = 0;
                                foreach ($knit_id as $val) {
                                    $delivery_qty = 0;$issue_qty = 0; $recv_for_batch_qty = 0;
                                    $barcode_nos = explode(",", chop($barcode_arr[$val], ','));
                                    foreach ($barcode_nos as $barcode_no) {
                                        $delivery_qty += $delivery_qty_arr[$barcode_no];
                                        $issue_qty += $issue_qty_arr[$barcode_no];
                                        $recv_for_batch_qty += $recv_for_batch_qty_arr[$barcode_no];
                                        if($mother_barcode_array[$barcode_no]['child']!="")
                                        {
                                            $barcode_number[]= $mother_barcode_array[$barcode_no]['child'];
                                        }
                                    }
                                    $knit_recv_qty += $knitting_recv_qnty_array[$val] + $delivery_qty;
                                    $knit_issue_qty += $knitting_issue_qnty_array[$val] + $issue_qty;
                                    $knit_recv_for_batch_qty += $recv_for_batch_qty;
                                    //echo $delivery_qty."<br/>";
                                }
                                foreach ($barcode_number as $barcode_noss) 
                                {
                                     $knit_issue_qty += $issue_qty_arr[$barcode_noss];    
                                     $knit_recv_for_batch_qty += $recv_for_batch_qty_arr[$barcode_noss];
                                }

                                $knitting_recv_qnty = $knit_recv_qty + $knit_recvProg_qty_arr[$row[csf('id')]];
                                $knitting_issue_qnty = $knit_issue_qty + $knit_issueProg_qty_arr[$row[csf('id')]];
                                $knitting_recv_for_batch_qnty = $knit_recv_for_batch_qty ;
                                //echo $knit_recv_qty .'+'. $knit_recvProg_qty_arr[$row[csf('id')]]."<br/>";

                                $balance_qnty = $row[csf('program_qnty')] - $knitting_qnty;
                                $balance_recv_qnty = $knitting_qnty - $knitting_recv_qnty;
                                $balance_issue_qnty = $knitting_recv_qnty - $knitting_issue_qnty;
                                $balance_recv_for_batch_qnty = $knitting_issue_qnty - $knitting_recv_for_batch_qnty;

                                //echo $row[csf('program_qnty')] ."-". $actual_yarn_issue_qnty."<br/>";

                                $complete = '&nbsp;';
                                if ($knitting_qnty >= $row[csf('program_qnty')])
                                $complete = 'Complete';

                                //for req qty
                                $req_qnty = $reqsDataArr[$row[csf('id')]]['reqs_qty'];
                                $req_bal_qnty = $row[csf('program_qnty')]-$req_qnty;
                                $req_id_str = implode(',',$reqsDataArr[$row[csf('id')]]['reqs_id']);

                                //$yarn_issue_bl_qnty = $row[csf('program_qnty')] - $actual_yarn_issue_qnty;
                                $yarn_issue_bl_qnty = $req_qnty - $actual_yarn_issue_qnty;

                                //for po company and buyer
                                $po_company = '';
                                $po_buyer = '';
                                if($row[csf('within_group')]==1)
                                {
                                    //$po_company = $company_library[$row[csf('po_company_id')]];
                                    $po_company = $company_library[$row[csf('sales_buyer')]];
                                    $po_buyer = $buyer_arr[$booking_Arr[$sale_booking_no]['buyer_id']];

                                    if($po_buyer=="")
                                    {
                                         $po_buyer = $buyer_arr[$po_break_dtls_arr[$row[csf('sales_id')]]['PO_BUYER']];
                                    }
                                }
                                else
                                {
                                    $po_buyer = $buyer_arr[$row[csf('sales_buyer')]];
                                }
                                //end for po company and buyer

                                //=========== For Program Status =================

                                $program_status = '';
                                if($row[csf('status_active')]==1 && $row[csf('is_deleted')]==0 && $row[csf('is_revised')]==0)
                                {
                                    $program_status = "Active";
                                }
                                else if($row[csf('status_active')]==0 && $row[csf('is_deleted')]==1 && $row[csf('is_revised')]==0)
                                {
                                    $program_status = "Delete";
                                }
                                else if($row[csf('status_active')]==1 && $row[csf('is_deleted')]==0 && $row[csf('is_revised')]==1)
                                {
                                    $program_status = "Revised";
                                }

                                if (!in_array($machine_dia_gg, $machine_dia_gg_array[$knitting_source]))
                                {
                                    if ($k != 1)
                                    {
                                        ?>
                                        <tr bgcolor="#CCCCCC">
                                            <td colspan="<? echo $colspan; ?>" align="right"><b>Sub Total</b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_program_qnty, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_req_qnty, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_req_bal_qnty, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_yarn_issue_qnty, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_yarn_issue_rtn_qnty, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_yarn_issue_rej_qnty, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_yarn_issue_bl_qnty, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_knitting_qnty, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_knitting_qc_qnty, 2, '.', ''); ?></b></td>
                                            <td align="right"><b><? echo number_format($sub_tot_qc_pass_qnty, 2, '.', ''); ?></b></td>

                                            <td align="right">
                                            <b><? echo number_format($sub_fab_rej_qnty, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_held_up_qnty, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_qc_balance, 2, '.', ''); ?></b></td>
                                            <td align="right"><b><? echo number_format($sub_tot_balance, 2, '.', ''); ?></b>
                                            <td align="right"><b><? //echo number_format($sub_tot_balance, 2, '.', ''); ?>store</b>
                                            </td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_knitting_recv_qnty, 2, '.', ''); ?></b>
                                            </td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_recv_balance, 2, '.', ''); ?></b></td>


                                            <td align="right">
                                            <b><? echo number_format($sub_tot_knitting_issue_qnty, 2, '.', ''); ?></b>
                                            </td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_issue_balance, 2, '.', ''); ?></b></td>
                                            <td align="right">
                                            <b><? echo number_format($sub_tot_knitting_rec_for_batch_qnty, 2, '.', ''); ?></b>
                                            </td>
                                            <td align="right"><b><? echo number_format($sub_tot_rec_for_batch_balance, 2, '.', ''); ?></b></td>
                                            <td align="right"><b><? echo number_format($sub_batch_qnty, 2, '.', ''); ?></b></td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <?
                                        $sub_tot_program_qnty = 0;
                                        $sub_tot_req_qnty = 0;
                                        $sub_tot_req_bal_qnty = 0;
                                        $sub_tot_knitting_qnty = 0;
                                        $sub_tot_knitting_qc_qnty = 0;
                                        $sub_tot_qc_pass_qnty = 0;
                                        $sub_tot_held_up_qnty = 0;
                                        $sub_tot_qc_balance = 0;
                                        $sub_tot_knitting_qnty_in_pcs = 0;
                                        $sub_tot_balance = 0;
                                        $sub_yarn_issue_qnty = 0;
                                        $sub_yarn_issue_rtn_qnty = 0;
                                        $sub_yarn_issue_rej_qnty = 0;
                                        $sub_yarn_issue_bl_qnty = 0;
                                        $sub_fab_rej_qnty = 0;
                                        $sub_delv_store_qnty = 0;

                                        $sub_tot_knitting_recv_qnty = 0;
                                        $sub_tot_recv_balance = 0;

                                        $sub_tot_knitting_issue_qnty = 0;
                                        $sub_tot_issue_balance = 0;
                                        $sub_tot_knitting_rec_for_batch_qnty = 0;
                                        $sub_tot_rec_for_batch_balance = 0;
                                        $sub_batch_qnty = 0;


                                    }

                                    // if(!in_array($knitting_cond,$party_array))
                                    if ($knitting_source_tmp[$knitting_source] == '') {
                                        $knitting_source_tmp[$knitting_source] = $knitting_source;
                                        ?>
                                        <tr bgcolor="#C6D9C9">
                                            <td style="font-weight: 900" colspan="<? echo $colspan + 9+8+7; ?>"><b><?php echo $knitting_cond; ?></b></td>
                                        </tr>
                                        <?
                                        $party_array[] = $knitting_cond;
                                    }
                                    ?>
                                    <tr bgcolor="#EFEFEF">
                                        <td colspan="<? echo $colspan + 9+8+7; ?>"><b>Machine Dia:- <?php echo $machine_dia_gg; ?></b></td>
                                    </tr>
                                    <?
                                    $machine_dia_gg_array[$knitting_source][] = $machine_dia_gg;
                                    $k++;
                                }
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                    <td width="40" align="center" valign="middle">
                                    <input type="checkbox" id="tbl_<? echo $i; ?>" name="check[]"
                                    onClick="selected_row(<? echo $i; ?>);"/>
                                    <input id="promram_id_<? echo $i; ?>" name="promram_id[]" type="hidden"
                                    value="<? echo $row[csf('id')]; ?>"/>
                                    <input id="job_no_<? echo $i; ?>" name="job_no[]" type="hidden"
                                    value="<? echo $job_no; ?>"/>
                                    <input id="source_id_<? echo $i; ?>" name="source_id_[]" type="hidden"
                                    value="<? echo $knitting_source; ?>"/>
                                    <input id="party_id_<? echo $i; ?>" name="party_id_[]" type="hidden"
                                    value="<? echo $row[csf('knitting_party')]; ?>"/>
                                    </td>
                                    <td width="40"><? echo $i; ?></td>
                                    <? //echo $knitting_party;//"<td width='100'><p><a href='##' onclick=\"generate_report_party(" . $row[csf('company_id')] . "," . $row[csf('id')] . ")\">" . $knitting_party . "</a></p></td>"; echo $knitting_party;//if ($type == 3)  ?>
                                    <td width="100"><p><? echo $knitting_party; ?></p></td>
                                    <td width="60" align="center"><a href='##'
                                    onClick="generate_report2(<? echo $row[csf('company_id')] . "," . $row[csf('id')]; ?>)"><? echo $row[csf('id')]; ?>
                                    &nbsp;</a></td>
                                    <td width="70" align="center" title="<? echo $row[csf('id')];?>"><? echo $reqsDataArr[$row[csf('id')]]['reqs_no']; ?>
                                    &nbsp;</td>
                                    <td width="100" align="center">
                                    &nbsp;<? echo $program_status; ?></td>
                                    <td width="80" align="center">
                                    &nbsp;<? echo change_date_format($row[csf('program_date')]); ?></td>
                                    <td width="80" align="center">
                                    &nbsp;<? if ($row[csf('start_date')] != "0000-00-00") echo change_date_format($row[csf('start_date')]); ?></td>
                                    <td width="80" align="center">
                                    &nbsp;<? if ($row[csf('end_date')] != "0000-00-00") echo change_date_format($row[csf('end_date')]); ?></td>
                                    <td width="120"><p><? echo $po_company; ?></p></td>
                                    <td width="80"><p><? echo $po_buyer; ?></p></td>
                                    <td width="90" align="center"><p><? echo $style_ref; ?></p></td>
                                    <td width="130">
                                    <div style="word-wrap:break-word; width:129px">
                                    <? if ($type == 3) echo $sales_order_no;
                                    else echo "<a href='##' onclick=\"generate_report(" . $row[csf('company_id')] . ",'" . $sale_booking_no . "','" . $sale_booking_no . "','" . $sales_order_no . "','" . $fReportId . "' )\">$sales_order_no</a>"; ?>
                                    </div>
                                    </td>

                                    <? if($sale_booking_no !='' && $booking_order_id !='' && $booking_job_no !='' && $booking_entry_form !='')
                                    { ?>
                                        <td width="100" title="<? echo $booking_entry_form;?>"><p><? echo "<a href='##' onclick=\"generate_booking_report('".$sale_booking_no."',".$booking_company.",'".$booking_order_id."',".$booking_fabric_natu.",".$booking_fabric_source.",".$is_approved_id.",'".$booking_job_no."','".$booking_entry_form."','".$fbReportId."' )\">$sale_booking_no</a>"; ?>&nbsp;</p></td>
                                    <? } else { ?>
                                        <td width="100" title=""><p><? echo $sale_booking_no; ?>&nbsp;</p></td>
                                    <? } ?>

                                    <td width="100" align="center"><p><? echo $jobNos; ?>&nbsp;</p></td>
                                    <td width="100" align="center"><p><? echo $internal_ref; ?>&nbsp;</p></td>
                                    <td width="80"><p><? echo $machine_no; ?>&nbsp;</p></td>
                                    <td width="110"><p><? echo $machine_dia_gg; ?></p></td>
                                    <td width="70"
                                    align="center"><? echo number_format($row[csf('distribution_qnty')], 2); ?>
                                    &nbsp;</td>
                                    <td width="80"><p><? echo $knitting_program_status[$row[csf('status')]]; ?></p></td>
                                    <td align="right" width="150"><? echo $row[csf('fabric_desc')];////correct upto ?></td>
                                    <td width="170"><p><? echo join(",", array_unique($yarn_desc)); ?>&nbsp;</p></td>
                                    <td width="130">
                                    <div style="word-wrap:break-word; width:130px"><? echo implode(", ", array_unique($supplier)); ?>
                                    &nbsp;</div>
                                    </td>
                                    <td width="70"><p><? echo join(", ", array_unique($lot)); ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo $gmts_color; ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo $color_range[$row[csf('color_range')]]; ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo $color_type[$row[csf('color_type_id')]]; ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo $row[csf('spandex_stitch_length')]; ?>&nbsp;</p></td>
                                    <td align="right" width="100"><? echo number_format($row[csf('draft_ratio')], 2); ?>
                                    &nbsp;</td>
                                    <td width="70"><p><? echo $row[csf('gsm_weight')]; ?>&nbsp;</p></td>
                                    <td width="70"><p><? echo $row[csf('dia')]; ?>&nbsp;</p></td>
                                    <td width="80"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?>&nbsp;</td>
                                    <td align="right" width="100"><? echo number_format($row[csf('program_qnty')], 2); ?></td>
                                    <td align="right" width="80"><a href='##' onClick="func_req_qty('<? echo $req_id_str; ?>')"><? echo number_format($req_qnty, 2); ?></a></td>
                                    <td align="right" width="80" title="(Program Qnty-Req. Qnty)"><? echo number_format($req_bal_qnty, 2); ?></td>
                                    <td align="right" width="100"><a href='##' onClick="func_issue_qty('<? echo $reqsDataArr[$row[csf('id')]]['reqs_no']; ?>')"><? echo number_format($yarn_issue_qnty, 2); ?></a></td>

                                    <!-- echo chop($req_no_str,','); -->

                                    <td align="right" width="100"><a href='##' onClick="func_issue_rtn_qty('<? echo $reqsDataArr[$row[csf('id')]]['reqs_no']; ?>')"><? echo number_format($yarnRtnQty, 2); ?></a></td>
                                    <td align="right" width="100"><? echo number_format($yarn_issue_reject_qnty, 2); ?></td>
                                    <td align="right" width="100" title="Program Qnty - (yarn issue-issue Return)"><? echo number_format($yarn_issue_bl_qnty, 2); ?></td>
                                    <td align="right" width="100" title="<?=$row[csf('id')];?>">
                                    <a  href='##' onClick="openmypage_popup_nz('<? echo $row[csf('id')];?>','knitting_popup_nz','<? echo $row[csf('company_id')];?>')"><? echo number_format($knitting_qnty,2,".","");?></a>
                                    </td>
                                    <td align="right" width="100">
                                    <a  href='##' onClick="openmypage_popup_nz('<? echo $row[csf('id')];?>','knitting_qc_popup_nz','<? echo $row[csf('company_id')];?>')"><? echo number_format($knitting_qc_qnty,2,".","");?></a>
                                    </td>
                                    <td align="right" width="100">
                                    <a  href='##' onClick="openmypage_popup_nz('<? echo $row[csf('id')];?>','knitting_qc_pass_popup_nz','<? echo $row[csf('company_id')];?>')"><? echo number_format($qc_pass_qnty,2,".","");?></a>
                                    </td>

                                    <td align="right" width="100"><a  href='##' onClick="openmypage_popup_nz('<? echo $row[csf('id')];?>','knitting_reject_popup_nz','<? echo $row[csf('company_id')];?>')"><? echo number_format($fabric_reject_qnty,2,".","");?></td>
                                    <td align="right" width="100"><a  href='##' onClick="openmypage_popup_nz('<? echo $row[csf('id')];?>','knitting_held_up_popup_nz','<? echo $row[csf('company_id')];?>')"><? echo number_format($held_up_qnty,2,".","");?></a> </td>
                                    <td align="right" width="100" title="<? echo ( "Knitting Production Qty-Knitting QC Qnty" );?>"><a  href='##' onClick="openmypage_popup_nz('<? echo $row[csf('id')];?>','knitting_qc_balance_popup_nz','<? echo $row[csf('company_id')];?>')"><? echo number_format($qc_balance,2,".","");?></a></td>
                                    <td align="right" width="100"><? echo number_format($balance_qnty, 2); ?></td>
                                    <td align="right" width="100"><a href="##" onClick="openmypage_popup('<? echo $row[csf('id')];?>','grey_purchase_delivery','<? echo $row[csf('company_id')];?>')">

                                    <? echo number_format($deliveryStorQtyArr[$row[csf('id')]],2); $delv_store=$deliveryStorQtyArr[$row[csf('id')]];?>
                                    </a>
                                    </td>
                                    <td align="right" width="100">
                                    <a href="##" onClick="openmypage_popup('<? echo $row[csf('id')];?>','grey_receive_popup','<? echo $row[csf('company_id')];?>')"><? echo number_format($knitting_recv_qnty,2);?></a>
                                    </td>
                                    <td align="right" width="100"><? echo number_format($balance_recv_qnty, 2); ?></td>


                                    <td align="right" width="100">
                                    <a href="##" onClick="openmypage_popup('<? echo $row[csf('id')];?>','grey_issue_popup','<? echo $row[csf('company_id')];?>')"><? echo number_format($knitting_issue_qnty,2);?></a>
                                    </td>
                                    <td align="right" width="100"><? echo number_format($balance_issue_qnty, 2); ?></td>

                                    <td align="right" width="100">
                                    <a href="##" onClick="openmypage_popup('<? echo $row[csf('id')];?>','grey_recv_for_batch_popup','<? echo $row[csf('company_id')];?>')"><? echo number_format($knitting_recv_for_batch_qnty,2);?></a>
                                    </td>
                                    <td align="right" width="100"><? echo number_format($balance_recv_for_batch_qnty, 2); ?></td>
                                    <td align="right" width="100"><a  href='##' onClick="openmypage_popup_nz('<? echo $row[csf('id')];?>','batch_qnty_popup_nz','<? echo $row[csf('company_id')];?>')"><? echo number_format($batch_qnty,2,".","");?></a></td>

                                    <td align="center" width="80"><? echo $complete; ?></td>
                                    <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                                </tr>
                                <?
                                $sub_tot_program_qnty += $row[csf('program_qnty')];
                                $sub_tot_req_qnty += $req_qnty;
                                $sub_tot_req_bal_qnty += $req_bal_qnty;
                                $sub_tot_knitting_qnty += $knitting_qnty;
                                $sub_tot_knitting_qc_qnty += $knitting_qc_qnty;
                                $sub_tot_qc_pass_qnty += $qc_pass_qnty;
                                $sub_tot_held_up_qnty += $held_up_qnty;
                                $sub_tot_qc_balance += $qc_balance;
                                $sub_tot_knitting_qnty_in_pcs += $knitting_qnty_in_pcs;
                                $sub_tot_balance += $balance_qnty;
                                $sub_tot_knitting_recv_qnty += $knitting_recv_qnty;
                                $sub_tot_recv_balance += $balance_recv_qnty;

                                $sub_tot_knitting_issue_qnty += $knitting_issue_qnty;
                                $sub_tot_issue_balance += $balance_issue_qnty;
                                $sub_tot_knitting_rec_for_batch_qnty += $knitting_recv_for_batch_qnty;
                                $sub_tot_rec_for_batch_balance += $balance_recv_for_batch_qnty;

                                $sub_yarn_issue_qnty += $yarn_issue_qnty;
                                $sub_yarn_issue_rtn_qnty += $yarnRtnQty;
                                $sub_yarn_issue_rej_qnty += $yarn_issue_reject_qnty;
                                $sub_yarn_issue_bl_qnty += $yarn_issue_bl_qnty;
                                $sub_fab_rej_qnty += $fabric_reject_qnty;
                                $sub_delv_store_qnty += $delv_store;
                                $sub_batch_qnty += $batch_qnty;

                                $tot_program_qnty += $row[csf('program_qnty')];
                                $tot_req_qnty += $req_qnty;
                                $tot_req_bal_qnty += $req_bal_qnty;
                                $tot_knitting_qnty += $knitting_qnty;
                                $tot_knitting_qc_qnty += $knitting_qc_qnty;
                                $tot_qc_pass_qnty += $qc_pass_qnty;
                                $tot_held_up_qnty += $held_up_qnty;
                                $tot_qc_balance += $qc_balance;
                                $tot_knitting_qnty_in_pcs += $knitting_qnty_in_pcs;
                                $tot_balance += $balance_qnty;
                                $tot_knitting_recv_qnty += $knitting_recv_qnty;
                                $tot_balance_recv_qnty += $balance_recv_qnty;

                                $tot_knitting_issue_qnty += $knitting_issue_qnty;
                                $tot_balance_issue_qnty += $balance_issue_qnty;
                                $tot_knitting_rec_for_batch_qnty += $knitting_recv_for_batch_qnty;
                                $tot_balance_rec_for_batch_qnty += $balance_recv_for_batch_qnty;

                                $tot_yarn_issue_qnty += $yarn_issue_qnty;
                                $tot_yarn_issue_rtn_qnty += $yarnRtnQty;
                                $tot_yarn_issue_rej_qnty += $yarn_issue_reject_qnty;
                                $tot_yarn_issue_bl_qnty += $yarn_issue_bl_qnty;
                                $tot_fab_rej_qnty += $fabric_reject_qnty;
                                $tot_delv_store_qnty += $delv_store;
                                $tot_batch_qnty += $batch_qnty;

                                $i++;
                            }
                            if ($i > 1)
                            {
                                ?>
                                <tr bgcolor="#CCCCCC">
                                <td colspan="<? echo $colspan; ?>" align="right"><b>Sub Total</b></td>
                                <td align="right">
                                <b><? echo number_format($sub_tot_program_qnty, 2, '.', ''); ?></b></td>
                                <td align="right">
                                <b><? echo number_format($sub_tot_req_qnty, 2, '.', ''); ?></b></td>
                                <td align="right">
                                <b><? echo number_format($sub_tot_req_bal_qnty, 2, '.', ''); ?></b></td>
                                <td align="right">
                                <b><? echo number_format($sub_yarn_issue_qnty, 2, '.', ''); ?></b></td>
                                <td align="right">
                                <b><? echo number_format($sub_yarn_issue_rtn_qnty, 2, '.', ''); ?></b></td>
                                <td align="right">
                                <b><? echo number_format($sub_yarn_issue_rej_qnty, 2, '.', ''); ?></b></td>
                                <td align="right">
                                <b><? echo number_format($sub_yarn_issue_bl_qnty, 2, '.', ''); ?></b></td>
                                <td align="right">
                                <b><? echo number_format($sub_tot_knitting_qnty, 2, '.', ''); ?></b></td>
                                <td align="right">
                                <b><? echo number_format($sub_tot_knitting_qc_qnty, 2, '.', ''); ?></b></td>
                                <td align="right"><b><? echo number_format($sub_tot_qc_pass_qnty, 2, '.', ''); ?></b></td>

                                <td align="right">
                                <b><? echo number_format($sub_fab_rej_qnty, 2, '.', ''); ?></b></td>
                                <td align="right">
                                <b><? echo number_format($sub_tot_held_up_qnty, 2, '.', ''); ?></b></td>
                                <td align="right">
                                <b><? echo number_format($sub_tot_qc_balance, 2, '.', ''); ?></b></td>
                                <td align="right"><b><? echo number_format($sub_tot_balance, 2, '.', ''); ?></b>
                                <td align="right"><b><? //echo number_format($sub_tot_balance, 2, '.', ''); ?>store</b>
                                </td>
                                <td align="right">
                                <b><? echo number_format($sub_tot_knitting_recv_qnty, 2, '.', ''); ?></b>
                                </td>
                                <td align="right">
                                <b><? echo number_format($sub_tot_recv_balance, 2, '.', ''); ?></b></td>


                                <td align="right">
                                <b><? echo number_format($sub_tot_knitting_issue_qnty, 2, '.', ''); ?></b>
                                </td>
                                <td align="right">
                                <b><? echo number_format($sub_tot_issue_balance, 2, '.', ''); ?></b></td>
                                <td align="right">
                                <b><? echo number_format($sub_tot_knitting_rec_for_batch_qnty, 2, '.', ''); ?></b>
                                </td>
                                <td align="right">
                                <b><? echo number_format($sub_tot_rec_for_batch_balance, 2, '.', ''); ?></b></td>
                                <td align="right">
                                <b><? echo number_format($sub_batch_qnty, 2, '.', ''); ?></b></td>

                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                </tr>
                                <?
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <th colspan="<? echo $colspan; ?>" align="right">Grand Total</th>
                            <th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?></th>
                            <th align="right"><b><? echo number_format($tot_req_qnty, 2, '.', ''); ?></b></th>
                            <th align="right"><b><? echo number_format($tot_req_bal_qnty, 2, '.', ''); ?></b></th>
                            <th align="right"><? echo number_format($tot_yarn_issue_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_yarn_issue_rtn_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_yarn_issue_rej_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_yarn_issue_bl_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_knitting_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_knitting_qc_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_qc_pass_qnty, 2, '.', ''); ?></th>

                            <th align="right"><? echo number_format($tot_fab_rej_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_held_up_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_qc_balance, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_balance, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_delv_store_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_knitting_recv_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_balance_recv_qnty, 2, '.', ''); ?></th>

                            <th align="right"><? echo number_format($tot_knitting_issue_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_balance_issue_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_knitting_rec_for_batch_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_balance_rec_for_batch_qnty, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($tot_batch_qnty, 2, '.', ''); ?></th>

                            <th></th>
                            <th></th>
                        </tfoot>
                    </table>
                </div>
            </fieldset>
            <?
        }
    }

    echo "<br />Execution Time: " . (microtime(true) - $started) . "S";
    foreach (glob("$user_name*.xls") as $filename)
    {
        if (@filemtime($filename) < (time() - $seconds_old))
        @unlink($filename);
    }
    //---------end------------//
    $html =ob_get_contents();
    ob_clean();
    $total_data=$html;

    $html = strip_tags($html, '<table><thead><tbody><tfoot><tr><td><th>');


    $name = time();
    $filename = $user_name . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    $filename = "requires/" . $user_name . "_" . $name . ".xls";
    echo "$total_data####$filename";
    exit();
}

if($action == "knitting_popup")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);

    $machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
    $receive_basis = array(2 => "Knitting Plan",11=>'Service Booking Based');
    ?>
    <script>

        var tableFilters = {
            col_operation: {
                id: ["value_receive_qnty_in", "value_receive_qnty_out", "value_receive_qnty_tot", "value_receive_qnty_in_pcs_tot"],
                col: [ 9,10,11,12],
                operation: ["sum", "sum", "sum", "sum"],
                write_method: ["innerHTML", "innerHTML", "innerHTML", "innerHTML"]
            }
        }
        $(document).ready(function (e) {
            setFilterGrid('tbl_list_search', -1, tableFilters);
        });

        function print_window() {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            $('#tbl_list_search tr:first').hide();

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";

            $('#tbl_list_search tr:first').show();
        }

    </script>

    <div style="width:1317px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
        style="width:100px" class="formbutton"/></div>
        <fieldset style="width:1317px;">
            <div id="report_container">

                <table border="1" class="rpt_table" rules="all" width="1300" cellpadding="0" cellspacing="0">
                    <thead>
                        <th colspan="15"><b>Grey Receive Info</b></th>
                    </thead>
                    <thead>
                        <th width="30">SL chk</th>
                        <th width="115">Receive Id</th>
                        <th width="95">Receive Basis</th>
                        <th width="110">Product Details</th>
                        <th width="100">Booking / Program No</th>
                        <th width="100">Barcode No</th>
                        <th width="60">Machine No</th>
                        <th width="75">Production Date</th>
                        <th width="100">Shift</th>
                        <th width="80">Inhouse Production</th>
                        <th width="80">Outside Production</th>
                        <th width="80">Production Qnty</th>
                        <th width="80">Production Qnty in Pcs</th>
                        <th width="70">Challan No</th>
                        <th>Kniting Com.</th>
                    </thead>
                </table>
                <div style="width:1318px; max-height:330px; overflow-y:scroll" id="scroll_body">
                    <table border="1" class="rpt_table" rules="all" width="1300" cellpadding="0" cellspacing="0"
                    id="tbl_list_search">
                    <?
                    $i = 1;
                    $total_receive_qnty = 0;
                    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
                    $supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
                    $product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');

                    $sql = "SELECT * from (
                        select  a.receive_date, a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_company,a.knitting_source,  a.receive_basis, a.challan_no,b.shift_name, sum(b.grey_receive_qnty) as knitting_qnty,sum(b.grey_receive_qnty_pcs) as grey_receive_qnty_pcs, b.id as dtls_id from inv_receive_master a, pro_grey_prod_entry_dtls b , ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d where a.id=b.mst_id and a.booking_id = d.id and c.id = d.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id = $program_id and a.company_id = $companyID and b.status_active=1 and b.is_deleted=0 group by a.receive_date,a.receive_basis,a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_source,  a.knitting_company, a.challan_no,b.shift_name, b.id
                        union all
                        select a.receive_date, a.recv_number,c.booking_no, b.prod_id, b.machine_no_id,a.knitting_company, a.knitting_source,   a.receive_basis, a.challan_no,b.shift_name,   sum(b.grey_receive_qnty) as knitting_qnty,sum(b.grey_receive_qnty_pcs) as grey_receive_qnty_pcs, b.id as dtls_id from inv_receive_master a, pro_grey_prod_entry_dtls b , ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d where a.id=b.mst_id  and b.program_no = d.id and c.id = d.mst_id and a.item_category=13 and a.entry_form=22 and a.receive_basis=11 and b.status_active=1 and b.is_deleted=0 and b.program_no in($program_id) and a.company_id = $companyID group by a.receive_date,a.receive_basis,a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_source, a.knitting_company, a.challan_no,b.shift_name, b.id
                    ) order by receive_date
                    ";

                    //echo $sql;
                    $result = sql_select($sql);
                    $dtlsIdChk = array();
                    $dtls_id_arr = array();
                    foreach ($result as $row)
                    {
                        if($dtlsIdChk[$row[csf('dtls_id')]] == "")
                        {
                            $dtlsIdChk[$row[csf('dtls_id')]] = $row[csf('dtls_id')];
                            array_push($dtls_id_arr,$row[csf('dtls_id')]);
                        }
                    }

                    if(!empty($dtls_id_arr))
                    {
                        $rcv_barcode_sql = "SELECT  a.dtls_id, a.barcode_no from pro_roll_details a where a.entry_form=2 ".where_con_using_array($dtls_id_arr,0,'a.dtls_id')."";

                        //echo $rcv_barcode_sql;
                        $res_rcv_barcode_sql = sql_select($rcv_barcode_sql);
                        $rcv_barcode_arr = array();
                        foreach($res_rcv_barcode_sql as $row)
                        {
                            $rcv_barcode_arr[$row[csf('dtls_id')]]["barcode_no"] .= $row[csf('barcode_no')].',';
                        }

                    }

                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        $total_receive_qnty += $row[csf('knitting_qnty')];
                        $total_receive_qnty_in_pcs += $row[csf('grey_receive_qnty_pcs')];
                        $barcode_nos = $rcv_barcode_arr[$row[csf('dtls_id')]]["barcode_no"];
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>"
                            onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115" align="center"><? echo $row[csf('recv_number')]; ?></td>
                            <td width="95" align="center"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
                            <td width="110" align="center"><? echo $product_arr[$row[csf('prod_id')]]; ?></td>
                            <td width="100" align="center"><? echo $row[csf('booking_no')]; ?></td>
                            <td width="100" align="center"><p><? echo chop($barcode_nos,','); ?></p></td>
                            <td width="60" align="center"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="100" align="center"><? echo $shift_name[$row[csf('shift_name')]]; ?></td>
                            <td align="right" width="80">
                                <?
                                if ($row[csf('knitting_source')] != 3) {
                                    echo number_format($row[csf('knitting_qnty')], 2, '.', '');
                                    $total_receive_qnty_in += $row[csf('knitting_qnty')];
                                } else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80">
                                <?
                                if ($row[csf('knitting_source')] == 3) {
                                    echo number_format($row[csf('knitting_qnty')], 2, '.', '');
                                    $total_receive_qnty_out += $row[csf('knitting_qnty')];
                                } else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80"><? echo number_format($row[csf('knitting_qnty')], 2, '.', ''); ?></td>
                            <td align="right" width="80"><? echo number_format($row[csf('grey_receive_qnty_pcs')], 2, '.', ''); ?></td>
                            <td width="70" align="center"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td>
                                <p><? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p>
                            </td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="1300" cellpadding="0" cellspacing="0">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="95">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="75" align="right"></th>
                    <th width="100" align="right">Total</th>
                    <th width="80" align="right"
                    id="value_receive_qnty_in"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
                    <th width="80" align="right"
                    id="value_receive_qnty_out"><? echo number_format($total_receive_qnty_out, 2, '.', ''); ?></th>
                    <th width="80" align="right"
                    id="value_receive_qnty_tot"><? echo number_format($total_receive_qnty, 2, '.', ''); ?></th>
                    <th width="80" align="right"
                    id="value_receive_qnty_in_pcs_tot"><? echo number_format($total_receive_qnty_in_pcs, 2, '.', ''); ?></th>
                    <th width="70">&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    exit();

}

if($action == "knitting_popup_nz")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);

    $machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
    ?>
    <script>

        var tableFilters = {
            col_operation: {
                id: ["value_receive_qnty_tot"],
                col: [9],
                operation: ["sum"],
                write_method: ["innerHTML"]
            }
        }
        $(document).ready(function (e) {
            setFilterGrid('tbl_list_search', -1, tableFilters);
        });

        function print_window() {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            $('#tbl_list_search tr:first').hide();

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";

            $('#tbl_list_search tr:first').show();
        }

    </script>

    <div style="width:1062px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
        style="width:100px" class="formbutton"/></div>
        <fieldset style="width:1062px;">
            <div id="report_container">

                <table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0">
                    <thead>
                        <th colspan="12"><b>Knitting Production Info</b></th>
                    </thead>
                    <thead>
                        <th width="30">SL chk</th>
                        <th width="115">Receive Id</th>
                        <th width="110">Product Details</th>
                        <th width="100">Booking / Program No</th>
                        <th width="100">Barcode No</th>
                        <th width="70">Roll No.</th>
                        <th width="60">Machine No</th>
                        <th width="75">Production Date</th>
                        <th width="100">Production Shift</th>
                        <th width="80">Roll Weight</th>
                        <th width="80">Prod. Operator</th>
                        <th>Kniting Com.</th>
                    </thead>
                </table>
                <div style="width:1063px; max-height:330px; overflow-y:scroll" id="scroll_body">
                    <table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0"
                    id="tbl_list_search">
                    <?
                    $i = 1;
                    $receive_qnty = 0;
                    $total_receive_qnty = 0;
                    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
                    $supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
                    $product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
                    $operator_arr = return_library_array("select id,first_name from lib_employee where status_active=1 and is_deleted=0", 'id', 'first_name');

                    $sql = "SELECT  a.receive_date, a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_company,a.knitting_source,  a.receive_basis, a.challan_no,b.shift_name, sum(e.qnty) as knitting_qnty, sum(e.reject_qnty) as reject_qnty, b.id as dtls_id, e.barcode_no, b.operator_name, e.roll_no from inv_receive_master a, pro_grey_prod_entry_dtls b , ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d, pro_roll_details e where a.id=b.mst_id and a.booking_id = d.id and c.id = d.mst_id and b.id = e.dtls_id and a.id=e.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id = $program_id and a.company_id = $companyID and b.status_active=1 and b.is_deleted=0 group by a.receive_date,a.receive_basis,a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_source,  a.knitting_company, a.challan_no,b.shift_name, b.id, e.barcode_no, b.operator_name,e.roll_no";

                    //echo $sql;
                    $result = sql_select($sql);

                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        $receive_qnty = $row[csf('knitting_qnty')]+$row[csf('reject_qnty')];
                        $total_receive_qnty += $row[csf('knitting_qnty')]+$row[csf('reject_qnty')];
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>"
                            onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115" align="center"><? echo $row[csf('recv_number')]; ?></td>
                            <td width="110" align="center"><? echo $product_arr[$row[csf('prod_id')]]; ?></td>
                            <td width="100" align="center"><? echo $row[csf('booking_no')]; ?></td>
                            <td width="100" align="center"><p><? echo $row[csf('barcode_no')]; ?></p></td>
                            <td width="70" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                            <td width="60" align="center"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="100" align="center"><? echo $shift_name[$row[csf('shift_name')]]; ?></td>
                            <td align="right" width="80"><? echo number_format($receive_qnty, 2, '.', ''); ?></td>
                            <td align="right" width="80"><? echo $operator_arr[$row[csf('operator_name')]];?></td>
                            <td>
                                <p><? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p>
                            </td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="75" align="right"></th>
                    <th width="100" align="right"><b>Total:</b></th>
                    <th width="80" align="right"
                    id="value_receive_qnty_tot"><b><? echo number_format($total_receive_qnty, 2, '.', ''); ?></b></th>
                    <th width="80" align="right">&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    exit();

}
if($action == "knitting_qc_popup_nz")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);

    $machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
    ?>
    <script>

        var tableFilters = {
            col_operation: {
                id: ["value_qc_qnty_tot"],
                col: [9],
                operation: ["sum"],
                write_method: ["innerHTML"]
            }
        }
        $(document).ready(function (e) {
            setFilterGrid('tbl_list_search', -1, tableFilters);
        });

        function print_window() {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            $('#tbl_list_search tr:first').hide();

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";

            $('#tbl_list_search tr:first').show();
        }

    </script>

    <div style="width:1062px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
        style="width:100px" class="formbutton"/></div>
        <fieldset style="width:1062px;">
            <div id="report_container">

                <table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0">
                    <thead>
                        <th colspan="12"><b>Knitting QC Info</b></th>
                    </thead>
                    <thead>
                        <th width="30">SL chk</th>
                        <th width="115">Receive Id</th>
                        <th width="110">Product Details</th>
                        <th width="100">Booking / Program No</th>
                        <th width="100">Barcode No</th>
                        <th width="70">Roll No.</th>
                        <th width="60">Machine No</th>
                        <th width="75">QC Date</th>
                        <th width="100">QC Shift</th>
                        <th width="80">QC Qty</th>
                        <th width="80">QC Operator</th>
                        <th>Kniting Com.</th>
                    </thead>
                </table>
                <div style="width:1063px; max-height:330px; overflow-y:scroll" id="scroll_body">
                    <table border="1" class="rpt_table" rules="all" width="1072" cellpadding="0" cellspacing="0"
                    id="tbl_list_search">
                    <?
                    $i = 1;
                    $receive_qnty = 0;
                    $total_receive_qnty = 0;
                    $total_qc_qnty = 0;
                    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
                    $supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
                    $product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
                    $operator_arr = return_library_array("select id,first_name from lib_employee where item_category_id=13", 'id', 'first_name');

                    $sql = "SELECT a.receive_date, a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_company,a.knitting_source,  a.receive_basis, a.challan_no,b.shift_name, sum(e.qnty) as knitting_qnty, sum(e.reject_qnty) as reject_qnty, b.id as dtls_id, e.barcode_no, b.operator_name, e.roll_no from inv_receive_master a, pro_grey_prod_entry_dtls b , ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d, pro_roll_details e where a.id=b.mst_id and a.booking_id = d.id and c.id = d.mst_id and b.id = e.dtls_id and a.id=e.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id = $program_id and a.company_id = $companyID and b.status_active=1 and b.is_deleted=0 group by a.receive_date,a.receive_basis,a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_source,  a.knitting_company, a.challan_no,b.shift_name, b.id, e.barcode_no, b.operator_name, e.roll_no";

                    //echo $sql;
                    $result = sql_select($sql);
                    $dtlsIdChk = array();
                    $dtls_id_arr = array();
                    foreach ($result as $row)
                    {
                        if($dtlsIdChk[$row[csf('dtls_id')]] == "")
                        {
                            $dtlsIdChk[$row[csf('dtls_id')]] = $row[csf('dtls_id')];
                            array_push($dtls_id_arr,$row[csf('dtls_id')]);
                        }
                    }

                    if(!empty($dtls_id_arr))
                    {
                        $knitting_qc_sql="SELECT distinct a.recv_number, a.booking_id, a.knitting_source, a.knitting_company, d.barcode_no, d.roll_weight, d.roll_status, d.qc_name, d.qc_date, d.insert_date
                        from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_qc_result_mst d
                        where a.id=b.mst_id and b.id=d.pro_dtls_id and a.item_category=13 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ".where_con_using_array($dtls_id_arr,0,'b.id')."";

                        //echo $knitting_qc_sql;
                        $res_knitting_qc = sql_select($knitting_qc_sql);
                        $knitting_qc_arr = array();
                        foreach($res_knitting_qc as $row)
                        {
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_qnty"] += $row[csf('roll_weight')];
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_name"] = $row[csf('qc_name')];
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_date"] = $row[csf('qc_date')];
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_result"] = $row[csf('roll_status')];
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"] = $row[csf('insert_date')];
                        }
                        unset($res_knitting_qc);

                    }

                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        $receive_qnty = $row[csf('knitting_qnty')]+$row[csf('reject_qnty')];
                        $total_receive_qnty += $row[csf('knitting_qnty')]+$row[csf('reject_qnty')];
                        $qc_qnty = $knitting_qc_arr[$row[csf('barcode_no')]]["qc_qnty"];
                        $total_qc_qnty +=$knitting_qc_arr[$row[csf('barcode_no')]]["qc_qnty"];
                        $qc_name = $knitting_qc_arr[$row[csf('barcode_no')]]["qc_name"];
                        $qc_date = $knitting_qc_arr[$row[csf('barcode_no')]]["qc_date"];

                        if(number_format($qc_qnty,2) >0.00)
                        {
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>"
                                onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="30"><? echo $i; ?></td>
                                <td width="115" align="center"><? echo $row[csf('recv_number')]; ?></td>
                                <td width="110" align="center"><? echo $product_arr[$row[csf('prod_id')]]; ?></td>
                                <td width="100" align="center"><? echo $row[csf('booking_no')]; ?></td>
                                <td width="100" align="center"><p><? echo $row[csf('barcode_no')]; ?></p></td>
                                <td width="70" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                                <td width="60" align="center"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></td>
                                <td width="75" align="center" title="<? echo date("H:i", strtotime($knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"]));?>"><? echo change_date_format($qc_date); ?></td>
                                <td width="100" align="center" >
                                    <?
                                        if(!empty($knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"]))
                                        {


                                            // if($knitting_qc_arr[$row[csf('barcode_no')]]["qc_result"] == 1)
                                            // {

                                                $start = date("H:i", strtotime($knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"]));

                                                $sql_s = "SELECT shift_name,start_time,end_time from shift_duration_entry where production_type=1 and status_active=1 and is_deleted=0";
                                                $sql_s_res = sql_select($sql_s);
                                                $shift_name_arr = array();
                                                foreach ($sql_s_res as $val_s)
                                                {
                                                    $currentTime = strtotime($start);
                                                    $startTime = strtotime($val_s[csf("start_time")]);
                                                    $endTime = strtotime($val_s[csf("end_time")]);
                                                    //echo $start.'='.$startTime.'='.$endTime;
                                                    if(
                                                            (
                                                            $startTime < $endTime &&
                                                            $currentTime >= $startTime &&
                                                            $currentTime <= $endTime
                                                            ) ||
                                                            (
                                                            $startTime > $endTime && (
                                                            $currentTime >= $startTime ||
                                                            $currentTime <= $endTime
                                                            )
                                                            )
                                                    ){
                                                        array_push($shift_name_arr,$shift_name[$val_s[csf('shift_name')]]);
                                                        //echo $shift_name[$val_s[csf('shift_name')]];
                                                    }
                                                }
                                                echo implode(",",array_filter(array_unique($shift_name_arr)));

                                            //}
                                        }
                                    ?>
                                </td>
                                <td align="right" width="80"><? echo number_format($qc_qnty, 2, '.', ''); ?></td>
                                <td align="center" width="80"><? echo $qc_name;?></td>
                                <td align="center">
                                    <p><? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p>
                                </td>
                            </tr>
                            <?
                        }
                        $i++;
                    }
                    ?>
                </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="1072" cellpadding="0" cellspacing="0">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="75" align="right"></th>
                    <th width="100" align="right"><b>Total:</b></th>
                    <th width="80" align="right" id="value_qc_qnty_tot"><b><? echo number_format($total_qc_qnty, 2, '.', ''); ?></b></th>
                    <th width="80" align="right">&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    exit();

}
if($action == "knitting_qc_pass_popup_nz")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);

    $machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
    ?>
    <script>

        var tableFilters = {
            col_operation: {
                id: ["value_qc_pass_qnty_tot"],
                col: [9],
                operation: ["sum"],
                write_method: ["innerHTML"]
            }
        }
        $(document).ready(function (e) {
            setFilterGrid('tbl_list_search', -1, tableFilters);
        });

        function print_window() {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            $('#tbl_list_search tr:first').hide();

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";

            $('#tbl_list_search tr:first').show();
        }

    </script>

    <div style="width:1062px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
        style="width:100px" class="formbutton"/></div>
        <fieldset style="width:1062px;">
            <div id="report_container">

                <table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0">
                    <thead>
                        <th colspan="12"><b>Knitting QC Pass Info</b></th>
                    </thead>
                    <thead>
                        <th width="30">SL chk</th>
                        <th width="115">Receive Id</th>
                        <th width="110">Product Details</th>
                        <th width="100">Booking / Program No</th>
                        <th width="100">Barcode No</th>
                        <th width="70">Roll No.</th>
                        <th width="60">Machine No</th>
                        <th width="75">QC Date</th>
                        <th width="100">QC Shift</th>
                        <th width="80">QC Pass Qty</th>
                        <th width="80">QC Operator</th>
                        <th>Kniting Com.</th>
                    </thead>
                </table>
                <div style="width:1063px; max-height:330px; overflow-y:scroll" id="scroll_body">
                    <table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0"
                    id="tbl_list_search">
                    <?
                    $i = 1;
                    $receive_qnty = 0;
                    $total_receive_qnty = 0;
                    $total_qc_qnty = 0;
                    $total_qc_pass_qnty = 0;
                    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
                    $supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
                    $product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
                    $operator_arr = return_library_array("select id,first_name from lib_employee where item_category_id=13", 'id', 'first_name');

                    $sql = "SELECT a.receive_date, a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_company,a.knitting_source,  a.receive_basis, a.challan_no,b.shift_name, sum(e.qnty) as knitting_qnty, sum(e.reject_qnty) as reject_qnty, b.id as dtls_id, e.barcode_no, b.operator_name, e.roll_no from inv_receive_master a, pro_grey_prod_entry_dtls b , ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d, pro_roll_details e where a.id=b.mst_id and a.booking_id = d.id and c.id = d.mst_id and b.id = e.dtls_id and a.id=e.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id = $program_id and a.company_id = $companyID and b.status_active=1 and b.is_deleted=0 group by a.receive_date,a.receive_basis,a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_source,  a.knitting_company, a.challan_no,b.shift_name, b.id, e.barcode_no, b.operator_name, e.roll_no";

                    //echo $sql;
                    $result = sql_select($sql);
                    $dtlsIdChk = array();
                    $dtls_id_arr = array();
                    foreach ($result as $row)
                    {
                        if($dtlsIdChk[$row[csf('dtls_id')]] == "")
                        {
                            $dtlsIdChk[$row[csf('dtls_id')]] = $row[csf('dtls_id')];
                            array_push($dtls_id_arr,$row[csf('dtls_id')]);
                        }
                    }

                    if(!empty($dtls_id_arr))
                    {
                        $knitting_qc_sql="SELECT distinct a.recv_number, a.booking_id, a.knitting_source, a.knitting_company, d.barcode_no, d.roll_weight, d.roll_status, d.qc_name, d.roll_status, d.qc_date, d.insert_date
                        from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_qc_result_mst d
                        where a.id=b.mst_id and b.id=d.pro_dtls_id and a.item_category=13 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ".where_con_using_array($dtls_id_arr,0,'b.id')."";

                        //echo $rcv_barcode_sql;
                        $res_knitting_qc = sql_select($knitting_qc_sql);
                        $knitting_qc_arr = array();
                        foreach($res_knitting_qc as $row)
                        {
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_qnty"] += $row[csf('roll_weight')];
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_name"] = $row[csf('qc_name')];
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_date"] = $row[csf('qc_date')];
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"] = $row[csf('insert_date')];
                            if($row[csf('roll_status')]==1)
                            {
                                $knitting_qc_arr[$row[csf('barcode_no')]]["qc_pass_qnty"] += $row[csf('roll_weight')];
                            }
                        }
                        unset($res_knitting_qc);

                    }

                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        $receive_qnty = $row[csf('knitting_qnty')]+$row[csf('reject_qnty')];
                        $total_receive_qnty += $row[csf('knitting_qnty')]+$row[csf('reject_qnty')];
                        $qc_qnty = $knitting_qc_arr[$row[csf('barcode_no')]]["qc_qnty"];
                        $total_qc_qnty +=$knitting_qc_arr[$row[csf('barcode_no')]]["qc_qnty"];
                        $qc_name = $knitting_qc_arr[$row[csf('barcode_no')]]["qc_name"];
                        $qc_pass_qnty = $knitting_qc_arr[$row[csf('barcode_no')]]["qc_pass_qnty"];
                        $total_qc_pass_qnty += $knitting_qc_arr[$row[csf('barcode_no')]]["qc_pass_qnty"];
                        $qc_date = $knitting_qc_arr[$row[csf('barcode_no')]]["qc_date"];

                        if(number_format($qc_pass_qnty,2)>0.00)
                        {
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>"
                                onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="30"><? echo $i; ?></td>
                                <td width="115" align="center"><? echo $row[csf('recv_number')]; ?></td>
                                <td width="110" align="center"><? echo $product_arr[$row[csf('prod_id')]]; ?></td>
                                <td width="100" align="center"><? echo $row[csf('booking_no')]; ?></td>
                                <td width="100" align="center"><p><? echo $row[csf('barcode_no')]; ?></p></td>
                                <td width="70" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                                <td width="60" align="center"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></td>
                                <td width="75" align="center" title="<? echo $knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"];?>"><? echo change_date_format($qc_date); ?></td>
                                <td width="100" align="center">
                                    <?
                                        if(!empty($knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"]))
                                        {
                                            // if($knitting_qc_arr[$row[csf('barcode_no')]]["qc_result"] == 1)
                                            // {

                                                $start = date("H:i", strtotime($knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"]));

                                                $sql_s = "SELECT shift_name,start_time,end_time from shift_duration_entry where production_type=1 and status_active=1 and is_deleted=0";
                                                $sql_s_res = sql_select($sql_s);
                                                $shift_name_arr = array();
                                                foreach ($sql_s_res as $val_s)
                                                {
                                                    $currentTime = strtotime($start);
                                                    $startTime = strtotime($val_s[csf("start_time")]);
                                                    $endTime = strtotime($val_s[csf("end_time")]);
                                                    //echo $start.'='.$startTime.'='.$endTime;
                                                    if(
                                                            (
                                                            $startTime < $endTime &&
                                                            $currentTime >= $startTime &&
                                                            $currentTime <= $endTime
                                                            ) ||
                                                            (
                                                            $startTime > $endTime && (
                                                            $currentTime >= $startTime ||
                                                            $currentTime <= $endTime
                                                            )
                                                            )
                                                    ){
                                                        array_push($shift_name_arr,$shift_name[$val_s[csf('shift_name')]]);
                                                        //echo $shift_name[$val_s[csf('shift_name')]];
                                                    }
                                                }
                                                echo implode(",",array_filter(array_unique($shift_name_arr)));

                                            //}
                                        }
                                    ?>
                                </td>
                                <td align="right" width="80"><? echo number_format($qc_pass_qnty, 2, '.', ''); ?></td>
                                <td align="center" width="80"><? echo $qc_name;?></td>
                                <td align="center">
                                    <p><? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p>
                                </td>
                            </tr>
                            <?
                        }
                        $i++;
                    }
                    ?>
                </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="75" align="right"></th>
                    <th width="100" align="right"><b>Total:</b></th>
                    <th width="80" align="right" id="value_qc_pass_qnty_tot"><b><? echo number_format($total_qc_pass_qnty, 2, '.', ''); ?></b></th>
                    <th width="80" align="right">&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    exit();

}
if($action == "knitting_reject_popup_nz")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);

    $machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
    ?>
    <script>

        var tableFilters = {
            col_operation: {
                id: ["value_reject_qnty_tot"],
                col: [9],
                operation: ["sum"],
                write_method: ["innerHTML"]
            }
        }
        $(document).ready(function (e) {
            setFilterGrid('tbl_list_search', -1, tableFilters);
        });

        function print_window() {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            $('#tbl_list_search tr:first').hide();

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";

            $('#tbl_list_search tr:first').show();
        }

    </script>

    <div style="width:1062px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
        style="width:100px" class="formbutton"/></div>
        <fieldset style="width:1062px;">
            <div id="report_container">

                <table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0">
                    <thead>
                        <th colspan="12"><b>Knitting QC Pass Info</b></th>
                    </thead>
                    <thead>
                        <th width="30">SL chk</th>
                        <th width="115">Receive Id</th>
                        <th width="110">Product Details</th>
                        <th width="100">Booking / Program No</th>
                        <th width="100">Barcode No</th>
                        <th width="70">Roll No.</th>
                        <th width="60">Machine No</th>
                        <th width="75">QC Date</th>
                        <th width="100">QC Shift</th>
                        <th width="80">Reject Qty</th>
                        <th width="80">QC Operator</th>
                        <th>Kniting Com.</th>
                    </thead>
                </table>
                <div style="width:1063px; max-height:330px; overflow-y:scroll" id="scroll_body">
                    <table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0"
                    id="tbl_list_search">
                    <?
                    $i = 1;
                    $receive_qnty = 0;
                    $total_receive_qnty = 0;
                    $total_qc_qnty = 0;
                    $total_qc_pass_qnty = 0;
                    $total_reject_qnty = 0;
                    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
                    $supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
                    $product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
                    $operator_arr = return_library_array("select id,first_name from lib_employee where item_category_id=13", 'id', 'first_name');

                    $sql = "SELECT a.receive_date, a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_company,a.knitting_source,  a.receive_basis, a.challan_no,b.shift_name, sum(e.qnty) as knitting_qnty, sum(e.reject_qnty) as reject_qnty, b.id as dtls_id, e.barcode_no, b.operator_name, e.roll_no from inv_receive_master a, pro_grey_prod_entry_dtls b , ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d, pro_roll_details e where a.id=b.mst_id and a.booking_id = d.id and c.id = d.mst_id and b.id = e.dtls_id and a.id=e.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id = $program_id and a.company_id = $companyID and b.status_active=1 and b.is_deleted=0 and e.reject_qnty>0 group by a.receive_date,a.receive_basis,a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_source,  a.knitting_company, a.challan_no,b.shift_name, b.id, e.barcode_no, b.operator_name, e.roll_no ";

                    //echo $sql;
                    $result = sql_select($sql);
                    $dtlsIdChk = array();
                    $dtls_id_arr = array();
                    foreach ($result as $row)
                    {
                        if($dtlsIdChk[$row[csf('dtls_id')]] == "")
                        {
                            $dtlsIdChk[$row[csf('dtls_id')]] = $row[csf('dtls_id')];
                            array_push($dtls_id_arr,$row[csf('dtls_id')]);
                        }
                    }

                    if(!empty($dtls_id_arr))
                    {
                        $knitting_qc_sql="SELECT distinct a.recv_number, a.booking_id, a.knitting_source, a.knitting_company, d.barcode_no, d.roll_weight, d.roll_status, d.qc_name, d.roll_status, d.qc_date, d.insert_date
                        from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_qc_result_mst d
                        where a.id=b.mst_id and b.id=d.pro_dtls_id and a.item_category=13 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ".where_con_using_array($dtls_id_arr,0,'b.id')."";

                        //echo $rcv_barcode_sql;
                        $res_knitting_qc = sql_select($knitting_qc_sql);
                        $knitting_qc_arr = array();
                        foreach($res_knitting_qc as $row)
                        {
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_qnty"] += $row[csf('roll_weight')];
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_name"] = $row[csf('qc_name')];
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_date"] = $row[csf('qc_date')];
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"] = $row[csf('insert_date')];
                            if($row[csf('roll_status')]==1)
                            {
                                $knitting_qc_arr[$row[csf('barcode_no')]]["qc_pass_qnty"] += $row[csf('roll_weight')];
                            }
                        }
                        unset($res_knitting_qc);

                    }

                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        $receive_qnty = $row[csf('knitting_qnty')]+$row[csf('reject_qnty')];
                        $total_reject_qnty += $row[csf('reject_qnty')];
                        $total_receive_qnty += $row[csf('knitting_qnty')]+$row[csf('reject_qnty')];
                        $qc_qnty = $knitting_qc_arr[$row[csf('barcode_no')]]["qc_qnty"];
                        $total_qc_qnty +=$knitting_qc_arr[$row[csf('barcode_no')]]["qc_qnty"];
                        $qc_name = $knitting_qc_arr[$row[csf('barcode_no')]]["qc_name"];
                        $qc_pass_qnty = $knitting_qc_arr[$row[csf('barcode_no')]]["qc_pass_qnty"];
                        $total_qc_pass_qnty += $knitting_qc_arr[$row[csf('barcode_no')]]["qc_pass_qnty"];
                        $qc_date = $knitting_qc_arr[$row[csf('barcode_no')]]["qc_date"];
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>"
                            onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115" align="center"><? echo $row[csf('recv_number')]; ?></td>
                            <td width="110" align="center"><? echo $product_arr[$row[csf('prod_id')]]; ?></td>
                            <td width="100" align="center"><? echo $row[csf('booking_no')]; ?></td>
                            <td width="100" align="center"><p><? echo $row[csf('barcode_no')]; ?></p></td>
                            <td width="70" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                            <td width="60" align="center"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></td>
                            <td width="75" align="center" title="<? echo $knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"];?>"><? echo change_date_format($qc_date); ?></td>
                            <td width="100" align="center">
                                <?
                                    if(!empty($knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"]))
                                    {
                                        // if($knitting_qc_arr[$row[csf('barcode_no')]]["qc_result"] == 1)
                                        // {

                                            $start = date("H:i", strtotime($knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"]));

                                            $sql_s = "SELECT shift_name,start_time,end_time from shift_duration_entry where production_type=1 and status_active=1 and is_deleted=0";
                                            $sql_s_res = sql_select($sql_s);
                                            $shift_name_arr = array();
                                            foreach ($sql_s_res as $val_s)
                                            {
                                                $currentTime = strtotime($start);
                                                $startTime = strtotime($val_s[csf("start_time")]);
                                                $endTime = strtotime($val_s[csf("end_time")]);
                                                //echo $start.'='.$startTime.'='.$endTime;
                                                if(
                                                        (
                                                        $startTime < $endTime &&
                                                        $currentTime >= $startTime &&
                                                        $currentTime <= $endTime
                                                        ) ||
                                                        (
                                                        $startTime > $endTime && (
                                                        $currentTime >= $startTime ||
                                                        $currentTime <= $endTime
                                                        )
                                                        )
                                                ){
                                                    array_push($shift_name_arr,$shift_name[$val_s[csf('shift_name')]]);
                                                    //echo $shift_name[$val_s[csf('shift_name')]];
                                                }
                                            }
                                            echo implode(",",array_filter(array_unique($shift_name_arr)));

                                        //}
                                    }
                                ?>
                            </td>
                            <td align="right" width="80"><? echo number_format($row[csf('reject_qnty')], 2, '.', ''); ?></td>
                            <td align="center" width="80"><? echo $qc_name;?></td>
                            <td align="center">
                                <p><? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p>
                            </td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="75" align="right"></th>
                    <th width="100" align="right"><b>Total:</b></th>
                    <th width="80" align="right" id="value_reject_qnty_tot"><b><? echo number_format($total_reject_qnty, 2, '.', ''); ?></b></th>
                    <th width="80" align="right">&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    exit();

}

if($action == "knitting_held_up_popup_nz")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);

    $machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
    ?>
    <script>

        var tableFilters = {
            col_operation: {
                id: ["value_held_up_qnty_tot"],
                col: [9],
                operation: ["sum"],
                write_method: ["innerHTML"]
            }
        }
        $(document).ready(function (e) {
            setFilterGrid('tbl_list_search', -1, tableFilters);
        });

        function print_window() {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            $('#tbl_list_search tr:first').hide();

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";

            $('#tbl_list_search tr:first').show();
        }

    </script>

    <div style="width:1062px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
        style="width:100px" class="formbutton"/></div>
        <fieldset style="width:1062px;">
            <div id="report_container">

                <table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0">
                    <thead>
                        <th colspan="12"><b>Knitting QC Pass Info</b></th>
                    </thead>
                    <thead>
                        <th width="30">SL chk</th>
                        <th width="115">Receive Id</th>
                        <th width="110">Product Details</th>
                        <th width="100">Booking / Program No</th>
                        <th width="100">Barcode No</th>
                        <th width="70">Roll No.</th>
                        <th width="60">Machine No</th>
                        <th width="75">QC Date</th>
                        <th width="100">QC Shift</th>
                        <th width="80">Held Up Qty</th>
                        <th width="80">QC Operator</th>
                        <th>Kniting Com.</th>
                    </thead>
                </table>
                <div style="width:1063px; max-height:330px; overflow-y:scroll" id="scroll_body">
                    <table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0"
                    id="tbl_list_search">
                    <?
                    $i = 1;
                    $receive_qnty = 0;
                    $total_receive_qnty = 0;
                    $total_qc_qnty = 0;
                    $total_qc_pass_qnty = 0;
                    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
                    $supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
                    $product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
                    $operator_arr = return_library_array("select id,first_name from lib_employee where item_category_id=13", 'id', 'first_name');

                    $sql = "SELECT a.receive_date, a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_company,a.knitting_source,  a.receive_basis, a.challan_no,b.shift_name, sum(e.qnty) as knitting_qnty, sum(e.reject_qnty) as reject_qnty, b.id as dtls_id, e.barcode_no, b.operator_name, e.roll_no from inv_receive_master a, pro_grey_prod_entry_dtls b , ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d, pro_roll_details e where a.id=b.mst_id and a.booking_id = d.id and c.id = d.mst_id and b.id = e.dtls_id and a.id=e.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id = $program_id and a.company_id = $companyID and b.status_active=1 and b.is_deleted=0 group by a.receive_date,a.receive_basis,a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_source,  a.knitting_company, a.challan_no,b.shift_name, b.id, e.barcode_no, b.operator_name, e.roll_no ";

                    //echo $sql;
                    $result = sql_select($sql);
                    $dtlsIdChk = array();
                    $dtls_id_arr = array();
                    foreach ($result as $row)
                    {
                        if($dtlsIdChk[$row[csf('dtls_id')]] == "")
                        {
                            $dtlsIdChk[$row[csf('dtls_id')]] = $row[csf('dtls_id')];
                            array_push($dtls_id_arr,$row[csf('dtls_id')]);
                        }
                    }

                    if(!empty($dtls_id_arr))
                    {
                        $knitting_qc_sql="SELECT distinct a.recv_number, a.booking_id, a.knitting_source, a.knitting_company, d.barcode_no, d.roll_weight, d.roll_status, d.qc_name, d.roll_status, d.qc_date, d.insert_date
                        from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_qc_result_mst d
                        where a.id=b.mst_id and b.id=d.pro_dtls_id and a.item_category=13 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ".where_con_using_array($dtls_id_arr,0,'b.id')."";

                        //echo $rcv_barcode_sql;
                        $res_knitting_qc = sql_select($knitting_qc_sql);
                        $knitting_qc_arr = array();
                        foreach($res_knitting_qc as $row)
                        {
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_qnty"] += $row[csf('roll_weight')];
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_name"] = $row[csf('qc_name')];
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_date"] = $row[csf('qc_date')];
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"] = $row[csf('insert_date')];
                            if($row[csf('roll_status')]==2)
                            {
                                $knitting_qc_arr[$row[csf('barcode_no')]]["held_up_qnty"] += $row[csf('roll_weight')];
                            }
                        }
                        unset($res_knitting_qc);

                    }

                    foreach ($result as $row)
                    {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        $receive_qnty = $row[csf('knitting_qnty')]+$row[csf('reject_qnty')];
                        $total_receive_qnty += $row[csf('knitting_qnty')]+$row[csf('reject_qnty')];
                        $qc_qnty = $knitting_qc_arr[$row[csf('barcode_no')]]["qc_qnty"];
                        $total_qc_qnty +=$knitting_qc_arr[$row[csf('barcode_no')]]["qc_qnty"];
                        $qc_name = $knitting_qc_arr[$row[csf('barcode_no')]]["qc_name"];
                        $qc_date = $knitting_qc_arr[$row[csf('barcode_no')]]["qc_date"];
                        $held_up_qnty = $knitting_qc_arr[$row[csf('barcode_no')]]["held_up_qnty"];
                        $total_held_up_qnty += $knitting_qc_arr[$row[csf('barcode_no')]]["held_up_qnty"];
                        if(number_format($held_up_qnty,2)>0.00)
                        {
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>"
                                onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="30"><? echo $i; ?></td>
                                <td width="115" align="center"><? echo $row[csf('recv_number')]; ?></td>
                                <td width="110" align="center"><? echo $product_arr[$row[csf('prod_id')]]; ?></td>
                                <td width="100" align="center"><? echo $row[csf('booking_no')]; ?></td>
                                <td width="100" align="center"><p><? echo $row[csf('barcode_no')]; ?></p></td>
                                <td width="70" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                                <td width="60" align="center"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></td>
                                <td width="75" align="center" title="<? echo $knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"];?>"><? echo change_date_format($qc_date); ?></td>
                                <td width="100" align="center">
                                    <?
                                        if(!empty($knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"]))
                                        {
                                            // if($knitting_qc_arr[$row[csf('barcode_no')]]["qc_result"] == 1)
                                            // {

                                                $start = date("H:i", strtotime($knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"]));

                                                $sql_s = "SELECT shift_name,start_time,end_time from shift_duration_entry where production_type=1 and status_active=1 and is_deleted=0";
                                                $sql_s_res = sql_select($sql_s);
                                                $shift_name_arr = array();
                                                foreach ($sql_s_res as $val_s)
                                                {
                                                    $currentTime = strtotime($start);
                                                    $startTime = strtotime($val_s[csf("start_time")]);
                                                    $endTime = strtotime($val_s[csf("end_time")]);
                                                    //echo $start.'='.$startTime.'='.$endTime;
                                                    if(
                                                            (
                                                            $startTime < $endTime &&
                                                            $currentTime >= $startTime &&
                                                            $currentTime <= $endTime
                                                            ) ||
                                                            (
                                                            $startTime > $endTime && (
                                                            $currentTime >= $startTime ||
                                                            $currentTime <= $endTime
                                                            )
                                                            )
                                                    ){
                                                        array_push($shift_name_arr,$shift_name[$val_s[csf('shift_name')]]);
                                                        //echo $shift_name[$val_s[csf('shift_name')]];
                                                    }
                                                }
                                                echo implode(",",array_filter(array_unique($shift_name_arr)));

                                            //}
                                        }
                                    ?>
                                </td>
                                <td align="right" width="80"><? echo number_format($held_up_qnty, 2, '.', ''); ?></td>
                                <td align="center" width="80"><? echo $qc_name;?></td>
                                <td align="center">
                                    <p><? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p>
                                </td>
                            </tr>
                            <?
                        }
                        $i++;
                    }
                    ?>
                </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="75" align="right"></th>
                    <th width="100" align="right"><b>Total:</b></th>
                    <th width="80" align="right" id="value_held_up_qnty_tot"><b><? echo number_format($total_held_up_qnty, 2, '.', ''); ?></b></th>
                    <th width="80" align="right">&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    exit();

}

if($action == "knitting_qc_balance_popup_nz")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);

    $machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
    ?>
    <script>

        var tableFilters = {
            col_operation: {
                id: ["value_qc_balance_qnty_tot"],
                col: [9],
                operation: ["sum"],
                write_method: ["innerHTML"]
            }
        }
        $(document).ready(function (e) {
            setFilterGrid('tbl_list_search', -1, tableFilters);
        });

        function print_window() {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            $('#tbl_list_search tr:first').hide();

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";

            $('#tbl_list_search tr:first').show();
        }

    </script>

    <div style="width:1142px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
        style="width:100px" class="formbutton"/></div>
        <fieldset style="width:1142px;">
            <div id="report_container">

                <table border="1" class="rpt_table" rules="all" width="1125" cellpadding="0" cellspacing="0">
                    <thead>
                        <th colspan="12"><b>Knitting QC Balance Info</b></th>
                    </thead>
                    <thead>
                        <th width="30">SL</th>
                        <th width="115">Receive Id</th>
                        <th width="110">Product Details</th>
                        <th width="100">Booking / Program No</th>
                        <th width="100">Barcode No</th>
                        <th width="70">Roll No.</th>
                        <th width="60">Machine No</th>
                        <th width="75">QC Date</th>
                        <th width="100">QC Shift</th>
                        <th width="80">QC Balance Qty</th>
                        <th width="80">QC Operator</th>
                        <th>Kniting Com.</th>
                    </thead>
                </table>
                <div style="width:1143px; max-height:330px; overflow-y:scroll" id="scroll_body">
                    <table border="1" class="rpt_table" rules="all" width="1125" cellpadding="0" cellspacing="0"
                    id="tbl_list_search">
                    <?
                    $i = 1;
                    $receive_qnty = 0;
                    $total_receive_qnty = 0;
                    $total_qc_qnty = 0;
                    $total_qc_pass_qnty = 0;
                    $total_qc_balance = 0;
                    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
                    $supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
                    $product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
                    $operator_arr = return_library_array("select id,first_name from lib_employee where item_category_id=13", 'id', 'first_name');

                    $sql = "SELECT a.receive_date, a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_company,a.knitting_source,  a.receive_basis, a.challan_no,b.shift_name, sum(e.qnty) as knitting_qnty, sum(e.reject_qnty) as reject_qnty, b.id as dtls_id, e.barcode_no, b.operator_name, e.roll_no from inv_receive_master a, pro_grey_prod_entry_dtls b , ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d, pro_roll_details e where a.id=b.mst_id and a.booking_id = d.id and c.id = d.mst_id and b.id = e.dtls_id and a.id=e.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id = $program_id and a.company_id = $companyID and b.status_active=1 and b.is_deleted=0 group by a.receive_date,a.receive_basis,a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_source,  a.knitting_company, a.challan_no,b.shift_name, b.id, e.barcode_no, b.operator_name, e.roll_no order by receive_date
                    ";

                    //echo $sql;
                    $result = sql_select($sql);
                    $dtlsIdChk = array();
                    $dtls_id_arr = array();
                    foreach ($result as $row)
                    {
                        if($dtlsIdChk[$row[csf('dtls_id')]] == "")
                        {
                            $dtlsIdChk[$row[csf('dtls_id')]] = $row[csf('dtls_id')];
                            array_push($dtls_id_arr,$row[csf('dtls_id')]);
                        }
                    }

                    if(!empty($dtls_id_arr))
                    {
                        $knitting_qc_sql="SELECT distinct a.recv_number, a.booking_id, a.knitting_source, a.knitting_company, d.barcode_no, d.roll_weight, d.roll_status, d.qc_name, d.roll_status, d.qc_date, d.insert_date
                        from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_qc_result_mst d
                        where a.id=b.mst_id and b.id=d.pro_dtls_id and a.item_category=13 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ".where_con_using_array($dtls_id_arr,0,'b.id')."";

                        //echo $rcv_barcode_sql;
                        $res_knitting_qc = sql_select($knitting_qc_sql);
                        $knitting_qc_arr = array();
                        foreach($res_knitting_qc as $row)
                        {
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_qnty"] += $row[csf('roll_weight')];
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_name"] = $row[csf('qc_name')];
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_date"] = $row[csf('qc_date')];
                            $knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"] = $row[csf('insert_date')];
                            if($row[csf('roll_status')]==2)
                            {
                                $knitting_qc_arr[$row[csf('barcode_no')]]["held_up_qnty"] += $row[csf('roll_weight')];
                            }
                        }
                        unset($res_knitting_qc);

                    }

                    foreach ($result as $row)
                    {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        $receive_qnty = $row[csf('knitting_qnty')]+$row[csf('reject_qnty')];
                        $total_receive_qnty += $row[csf('knitting_qnty')]+$row[csf('reject_qnty')];
                        $qc_qnty = $knitting_qc_arr[$row[csf('barcode_no')]]["qc_qnty"];
                        $total_qc_qnty +=$knitting_qc_arr[$row[csf('barcode_no')]]["qc_qnty"];
                        $qc_name = $knitting_qc_arr[$row[csf('barcode_no')]]["qc_name"];
                        $qc_date = $knitting_qc_arr[$row[csf('barcode_no')]]["qc_date"];
                        $qc_balance = $receive_qnty- $qc_qnty;
                        $total_qc_balance += $receive_qnty- $qc_qnty;

                        if(number_format($qc_balance,2)>0.00)
                        {
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>"
                                onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="30"><? echo $i; ?></td>
                                <td width="115" align="center"><? echo $row[csf('recv_number')]; ?></td>
                                <td width="110" align="center"><? echo $product_arr[$row[csf('prod_id')]]; ?></td>
                                <td width="100" align="center"><? echo $row[csf('booking_no')]; ?></td>
                                <td width="100" align="center"><p><? echo $row[csf('barcode_no')]; ?></p></td>
                                <td width="70" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                                <td width="60" align="center"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></td>
                                <td width="75" align="center" title="<? echo $knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"];?>"><? echo change_date_format($qc_date); ?></td>
                                <td width="100" align="center">
                                    <?
                                        if(!empty($knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"]))
                                        {
                                            // if($knitting_qc_arr[$row[csf('barcode_no')]]["qc_result"] == 1)
                                            // {

                                                $start = date("H:i", strtotime($knitting_qc_arr[$row[csf('barcode_no')]]["qc_insert_date"]));

                                                $sql_s = "SELECT shift_name,start_time,end_time from shift_duration_entry where production_type=1 and status_active=1 and is_deleted=0";
                                                $sql_s_res = sql_select($sql_s);
                                                $shift_name_arr = array();
                                                foreach ($sql_s_res as $val_s)
                                                {
                                                    $currentTime = strtotime($start);
                                                    $startTime = strtotime($val_s[csf("start_time")]);
                                                    $endTime = strtotime($val_s[csf("end_time")]);
                                                    //echo $start.'='.$startTime.'='.$endTime;
                                                    if(
                                                            (
                                                            $startTime < $endTime &&
                                                            $currentTime >= $startTime &&
                                                            $currentTime <= $endTime
                                                            ) ||
                                                            (
                                                            $startTime > $endTime && (
                                                            $currentTime >= $startTime ||
                                                            $currentTime <= $endTime
                                                            )
                                                            )
                                                    ){
                                                        array_push($shift_name_arr,$shift_name[$val_s[csf('shift_name')]]);
                                                        //echo $shift_name[$val_s[csf('shift_name')]];
                                                    }
                                                }
                                                echo implode(",",array_filter(array_unique($shift_name_arr)));

                                            //}
                                        }
                                    ?>
                                </td>
                                <td align="right" width="80"><? echo number_format($qc_balance, 2, '.', ''); ?></td>
                                <td align="center" width="80"><? echo $qc_name;?></td>
                                <td align="center">
                                    <p><? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p>
                                </td>
                            </tr>
                            <?
                        }
                        $i++;
                    }
                    ?>
                </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="1125" cellpadding="0" cellspacing="0">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="75" align="right"></th>
                    <th width="100" align="right"><b>Total:</b></th>
                    <th width="80" align="right" id="value_qc_balance_qnty_tot"><b><? echo number_format($total_qc_balance, 2, '.', ''); ?></b></th>
                    <th width="80" align="right">&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    exit();

}

if($action == "batch_qnty_popup_nz")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);

    ?>
    <script>

        var tableFilters = {
            col_operation: {
                id: ["value_total_batch_qnty"],
                col: [2],
                operation: ["sum"],
                write_method: ["innerHTML"]
            }
        }
        $(document).ready(function (e) {
            setFilterGrid('tbl_list_search', -1, tableFilters);
        });

        function print_window() {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            $('#tbl_list_search tr:first').hide();

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";

            $('#tbl_list_search tr:first').show();
        }

    </script>

    <div style="width:340px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
        style="width:100px" class="formbutton"/></div>
        <fieldset style="width:340px;">
            <div id="report_container">

                <table border="1" class="rpt_table" rules="all" width="280" cellpadding="0" cellspacing="0">
                    <thead>
                        <th colspan="3"><b>Batch Info</b></th>
                    </thead>
                    <thead>
                        <th width="30">SL</th>
                        <th width="150">Batch No</th>
                        <th width="">Batch Qnty</th>
                    </thead>
                </table>
                <div style="width:340px; max-height:330px; overflow-y:scroll" id="scroll_body">
                    <table border="1" class="rpt_table" rules="all" width="280" cellpadding="0" cellspacing="0"
                    id="tbl_list_search">
                    <?
                    $i = 1;
                    $receive_qnty = 0;
                    $total_receive_qnty = 0;
                    $total_qc_qnty = 0;
                    $total_qc_pass_qnty = 0;
                    $total_qc_balance = 0;

                    $sql_batchData = "SELECT a.batch_no,b.program_no, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.program_no = $program_id and a.company_id = $companyID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.batch_no,b.program_no";

                    //echo $sql_batchData;
                    $batch_result = sql_select($sql_batchData);

                    foreach ($batch_result as $row)
                    {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>"
                            onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="150" align="center"><? echo $row[csf('batch_no')]; ?></td>
                            <td width="" align="right"><? echo number_format($row[csf('batch_qnty')],2); ?></td>
                        </tr>
                        <?
                        $i++;
                        $total_batch_qnty +=$row[csf('batch_qnty')];
                    }
                    ?>
                </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="280" cellpadding="0" cellspacing="0">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="150">&nbsp;</th>
                    <th id="value_total_batch_qnty"><? echo number_format($total_batch_qnty,2); ?></th>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    exit();

}

if($action == "knitting_qc_popup")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);

    $machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
    $receive_basis = array(2 => "Knitting Plan",11=>'Service Booking Based');
    ?>
    <script>

        var tableFilters = {
            col_operation: {
                id: ["value_inhouse_qnty_tot", "value_outside_qnty_in_pcs_tot"],
                col: [ 3, 4],
                operation: ["sum", "sum"],
                write_method: ["innerHTML", "innerHTML"]
            }
        }
        $(document).ready(function (e) {
            setFilterGrid('tbl_list_search', -1, tableFilters);
        });

        function print_window() {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            $('#tbl_list_search tr:first').hide();

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";

            $('#tbl_list_search tr:first').show();
        }

    </script>
    <div style="width:400px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
        style="width:100px" class="formbutton"/>
    </div>
    <fieldset style="width:400px;">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="400" cellpadding="0" cellspacing="0">
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Barcode No</th>
                    <th width="100">Kniting Com</th>
                    <th width="80">QC In house Production Qty</th>
                    <th width="">Qc Outside Production Qty</th>
                </thead>
            </table>
            <div style="width:418px; max-height:330px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="400" cellpadding="0" cellspacing="0" id="tbl_list_search">
                    <?
                    $i = 1;
                    $total_inhouse_qnty = 0;$total_outside_qnty=0;
                    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
                    $supplier_arr = return_library_array("select id, short_name from  lib_supplier", "id", "short_name");

                    $knitting_qc_sql="SELECT distinct a.recv_number, a.booking_id, a.knitting_source, a.knitting_company, d.barcode_no, d.roll_weight
                    from inv_receive_master a, pro_grey_prod_entry_dtls b, PRO_QC_RESULT_MST d
                    where a.id=b.mst_id and b.id=d.PRO_DTLS_ID and a.booking_id in($program_id) and a.company_id = $companyID and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
                    // echo $knitting_qc_sql;
                    $knitting_qc_sql_result = sql_select($knitting_qc_sql);
                    foreach ($knitting_qc_sql_result as $row)
                    {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";
                        if ($row[csf('knitting_source')]==1)
                        {
                            $inhouse_qty=$row[csf('roll_weight')];
                            $knitting_company=$company_library[$row[csf('knitting_company')]];
                            $total_inhouse_qnty += $row[csf('roll_weight')];
                        }
                        else
                        {
                            $outside_qty=$row[csf('roll_weight')];
                            $knitting_company=$supplier_arr[$row[csf('knitting_company')]];
                            $total_outside_qnty += $row[csf('roll_weight')];
                        }
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>"
                            onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="100" align="center"><p><? echo $row[csf('barcode_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $knitting_company; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($inhouse_qty, 2, '.', ''); ?></p></td>
                            <td width="" align="right"><p><? echo number_format($outside_qty, 2, '.', ''); ?></p></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="400" cellpadding="0" cellspacing="0">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100" align="right">Total</th>
                    <th width="80" align="right" id="value_inhouse_qnty_tot"><? echo number_format($total_inhouse_qnty, 2, '.', ''); ?></th>
                    <th width="" align="right" id="value_outside_qnty_in_pcs_tot"><? echo number_format($total_outside_qnty, 2, '.', ''); ?></th>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    exit();
}

if ($action == "grey_purchase_delivery")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    <script>
        function print_window() {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";
        }
    </script>
    <div style="width:915px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
        style="width:100px" class="formbutton"/></div>
    <fieldset style="width:905px; margin-left:2px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="885" cellpadding="0" cellspacing="0">
                <thead>
                    <th colspan="11"><b>Grey Delivery Info</b></th>
                </thead>
                <thead>
                    <th width="30">SL</th>
                    <th width="125">Delivery Id</th>
                    <th width="150">Product Details</th>
                    <th width="80">Booking / Program No</th>
                    <th width="80">Delivery Date</th>
                    <th width="80">Inhouse Delivery</th>
                    <th width="80">Outside Delivery</th>
                    <th width="80">Total Delivery Quantity</th>
                    <th width="80">Delivery Qnty in Pcs</th>
                    <th>Kniting Com.</th>
                </thead>
            </table>
            <div style="width:905px; max-height:330px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="885" cellpadding="0" cellspacing="0">
                    <?
                    $i = 1;
                    $total_receive_qnty = 0;
                    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
                    $supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
                    $product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');

                    $sql = "SELECT c.sys_number,c.knitting_company,c.knitting_source,c.delevery_date, a.booking_no, sum(b.current_delivery)  as quantity, SUM (b.current_delivery_qnty_in_pcs) as pcs_qnty, b.product_id from pro_roll_details a,pro_grey_prod_delivery_dtls b, pro_grey_prod_delivery_mst c where a.mst_id=b.grey_sys_id and b.mst_id = c.id and a.barcode_no=b.barcode_num and a.entry_form=2 and a.receive_basis=2 and a.booking_without_order=0 and a.booking_no = '$program_id' and c.company_id = $companyID and b.entry_form = 56 and b.status_active=1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by c.sys_number,c.knitting_company,c.knitting_source,c.delevery_date, a.booking_no, b.product_id order by c.delevery_date";

                    //echo $sql;
                    $deliveryStorQtyArr = array();
                    foreach ($deliveryquantityArr as $row) {
                        $deliveryStorQtyArr[$row[csf('booking_no')]] += $row[csf('current_delivery')];
                    }

                    $result = sql_select($sql);
                    $total_receive_qnty_in=$total_receive_qnty_out=$total_receive_qnty=$total_receive_qnty_pcs=0;
                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";


                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>"
                            onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="125"><p><? echo $row[csf('sys_number')]; ?></p></td>
                            <td width="150"><p><? echo $product_arr[$row[csf('product_id')]]; ?></p></td>
                            <td width="80" align="center"><? echo $row[csf('booking_no')]; ?></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('delevery_date')]); ?></td>
                            <td width="80" align="right">
                                <?
                                if ($row[csf('knitting_source')] == 1)
                                {
                                    echo number_format($row[csf('quantity')], 2, '.', '');
                                    $total_receive_qnty_in += $row[csf('quantity')];
                                }
                                else echo "0.00";
                                ?>
                            </td>
                            <td width="80" align="right">
                                <?
                                if ($row[csf('knitting_source')] == 3)
                                {
                                    echo number_format($row[csf('quantity')], 2, '.', '');
                                    $total_receive_qnty_out += $row[csf('quantity')];
                                }
                                else echo "0.00";
                                ?>
                            </td>
                            <td align="right" width="80">
                                <?
                                if ($row[csf('knitting_source')] != 2)
                                {
                                    echo number_format($row[csf('quantity')], 2, '.', '');
                                    $total_receive_qnty += $row[csf('quantity')];
                                }
                                else echo "0.00";
                                ?>
                            </td>
                            <td align="right" width="80">
                                <?
                                echo number_format($row[csf('pcs_qnty')], 2, '.', '');
                                $total_receive_qnty_pcs += $row[csf('pcs_qnty')];
                                ?>
                            </td>
                            <td>
                                <? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?>
                            </td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="5" align="right">Total</th>
                        <th align="right"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty_out, 2, '.', ''); ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty, 2, '.', ''); ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty_pcs, 2, '.', ''); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>
        </div>
    </fieldset>
    <?
    exit();
}

if ($action == "grey_receive_popup")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');

    extract($_REQUEST);
    $order_id = explode('_', $order_id);
    ?>
    <script>
        function print_window() {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";
        }
    </script>
    <div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
        style="width:100px" class="formbutton"/></div>
        <fieldset style="width:1037px; margin-left:2px">
            <div id="report_container">
                <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
                    <thead>
                        <th colspan="11"><b>Grey Receive / Purchase Info</b></th>
                    </thead>
                    <thead>
                        <th width="30">SL</th>
                        <th width="125">Receive Id</th>
                        <th width="95">Receive Basis</th>
                        <th width="150">Product Details</th>
                        <th width="110">Booking/PI/ Production No</th>
                        <th width="75">Receive Date</th>
                        <th width="80">Inhouse Receive</th>
                        <th width="80">Outside Receive</th>
                        <th width="80">Total Receive Qnty</th>
                        <th width="65">Challan No</th>
                        <th>Kniting Com.</th>
                    </thead>
                </table>
                <div style="width:1037px; max-height:330px; overflow-y:scroll" id="scroll_body">
                    <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
                        <?
                        $i = 1;
                        $total_receive_qnty = 0;
                        $product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');



                        //$sql = "select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id $receive_basis_cond and a.entry_form in (22,58) and c.entry_form in (22,58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id";

                        /*

                        58

                        select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.qnty) as quantity
                        from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
                        where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form = 58 and c.entry_form = 58 and a.status_active=1 and a.is_deleted=0
                        and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.booking_no = '6153'
                        group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id


                        //22__9
                        select a.id, a.recv_number, b.grey_receive_qnty
                        from inv_receive_master a, pro_grey_prod_entry_dtls b, inv_receive_master c
                        where a.id = b.mst_id and a.entry_form =22 and a.receive_basis =9 and a.company_id = 3  and  a.booking_id = c.id and c.entry_form=2 and c.receive_basis = 2*/


                        $sql_22 ="select a.recv_number as booking_no,a.id
                        from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c
                        where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=2 and c.entry_form=2 and a.receive_basis=2
                        and b.status_active=1 and b.is_deleted=0 and a.booking_id = '$program_id' and b.trans_id = 0 and a.company_id = $companyID";
                        $result_22 = sql_select($sql_22);
                        $bookingIdArr = array();
                        $bookingIdChk = array();
                        foreach($result_22 as $row_22)
                        {
                            //$booking_id .= $row_22[csf('id')].",";
                            if($bookingIdChk[$row_22[csf('id')]] == "")
                            {
                                $bookingIdChk[$row_22[csf('id')]] = $row_22[csf('id')];
                                array_push($bookingIdArr,$row_22[csf('id')]);
                            }
                        }

                        //$booking_id =  chop($booking_id,',');
                        if(!empty($bookingIdArr)){
                        $sql_extend = " union all
                        select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.quantity) as quantity,a.booking_no
                        from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c
                        where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=22 and c.entry_form=22 and a.receive_basis in (9,11)
                        and b.status_active=1 and b.is_deleted=0 and a.company_id = $companyID
                        ".where_con_using_array($bookingIdArr,0,'a.booking_id')."
                        group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id,a.booking_no ";
                    }
                    $sql =  "select * from (
                        select b.recv_number, b.receive_date, b.receive_basis, b.knitting_source, b.challan_no, b.knitting_company, c.prod_id, sum(a.qnty) as quantity,b.booking_no
                        from pro_roll_details a,inv_receive_master b, pro_grey_prod_entry_dtls c
                        where a.entry_form = 58 and a.mst_id = b.id and b.id = c.mst_id and a.dtls_id = c.id
                        and a.booking_no = '$program_id'
                        and a.status_active = 1 and a.is_deleted = 0 and b.company_id = $companyID
                        group by b.recv_number, b.receive_date, b.receive_basis, b.knitting_source, b.challan_no, b.knitting_company, c.prod_id,b.booking_no
                        union all
                        select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.quantity) as quantity,a.booking_no
                        from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c
                        where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=22 and c.entry_form=22 and a.receive_basis=2
                        and b.status_active=1 and b.is_deleted=0 and a.booking_id = '$program_id'  and b.trans_id <> 0  and a.company_id = $companyID
                        group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id,a.booking_no
                        $sql_extend
                    ) order by receive_date";

                    //echo $sql;

                        $result = sql_select($sql);
                        foreach ($result as $row) {
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            $total_receive_qnty += $row[csf('quantity')];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>"
                                onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="30"><? echo $i; ?></td>
                                <td width="125"><p><? echo $row[csf('recv_number')]; ?></p></td>
                                <td width="95"><p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></p></td>
                                <td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                                <td width="110"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                                <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                <td align="right" width="80">
                                    <?
                                    if ($row[csf('knitting_source')] != 3) {
                                        echo number_format($row[csf('quantity')], 2, '.', '');
                                        $total_receive_qnty_in += $row[csf('quantity')];
                                    } else echo "&nbsp;";
                                    ?>
                                </td>
                                <td align="right" width="80">
                                    <?
                                    if ($row[csf('knitting_source')] == 3) {
                                        echo number_format($row[csf('quantity')], 2, '.', '');
                                        $total_receive_qnty_out += $row[csf('quantity')];
                                    } else echo "&nbsp;";
                                    ?>
                                </td>
                                <td align="right"
                                width="80"><? echo number_format($row[csf('quantity')], 2, '.', ''); ?></td>
                                <td width="65"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                                <td>
                                    <p><? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?>
                                &nbsp;</p></td>
                            </tr>
                            <?
                            $i++;
                        }
                        ?>
                        <tfoot>
                            <th colspan="6" align="right">Total</th>
                            <th align="right"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($total_receive_qnty_out, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($total_receive_qnty, 2, '.', ''); ?></th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                        </tfoot>
                    </table>
                </div>
            </div>
        </fieldset>
        <?
        exit();
}
if ($action == "grey_issue_popup")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');

    extract($_REQUEST);
    $order_id = explode('_', $order_id);
    ?>
    <script>
        function print_window() {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";
        }
    </script>
    <div style="width:737px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
        style="width:100px" class="formbutton"/></div>
        <fieldset style="width:737px; margin-left:2px">
            <div id="report_container">
                <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                    <thead>
                        <th colspan="11"><b>Grey Issue Info</b></th>
                    </thead>
                    <thead>

                        <th width="30">SL</th>
                        <th width="125">Issue Id</th>
                        <th width="125">Issue Purpose</th>
                        <th width="75">Issue Date</th>
                        <th width="150">Product Details</th>
                        <th width="80">Issue Qnty</th>
                        <th>Kniting Com.</th>
                    </thead>
                </table>
                <div style="width:737px; max-height:330px; overflow-y:scroll" id="scroll_body">
                    <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                        <?
                        $i = 1;
                        $total_receive_qnty = 0;
                        $product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');

                        $sql_22 ="select a.recv_number as booking_no,a.id
                        from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c
                        where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=2 and c.entry_form=2 and a.receive_basis=2
                        and b.status_active=1 and b.is_deleted=0 and a.booking_id = '$program_id' and b.trans_id = 0 and a.company_id = $companyID";
                        $result_22 = sql_select($sql_22);
                        $bookingIdChk = array();
                        $bookingIdArr = array();
                        foreach($result_22 as $row_22)
                        {
                            //$booking_id .= $row_22[csf('id')].",";
                            if($bookingIdChk[$row_22[csf('id')]] == "")
                            {
                                $bookingIdChk[$row_22[csf('id')]] = $row_22[csf('id')];
                                array_push($bookingIdArr,$row_22[csf('id')]);
                            }
                        }

                        //$booking_id =  chop($booking_id,',');

                        if(!empty($bookingIdArr))
                        {
                            $bookingIdCond = "".where_con_using_array($bookingIdArr,0,'a.booking_id')."";
                        }



                     $sql="select a.issue_number, a.issue_date, a.issue_purpose,a.knit_dye_company,a.knit_dye_source, b.prod_id, sum(b.issue_qnty) as quantity from inv_issue_master a, inv_grey_fabric_issue_dtls b,order_wise_pro_details c where  a.id=b.mst_id and b.trans_id=c.trans_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=16 and a.issue_basis=3 and b.status_active=1 and b.is_deleted=0 and a.company_id = $companyID
                        $bookingIdCond group by a.issue_number, a.issue_date, a.issue_purpose,a.knit_dye_company,a.knit_dye_source, b.prod_id
                        union all
                        select a.issue_number, a.issue_date, a.issue_purpose,a.knit_dye_company,a.knit_dye_source, b.prod_id, sum(b.issue_qnty) as quantity from inv_issue_master a, inv_grey_fabric_issue_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.trans_id=c.trans_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=16 and a.issue_basis=1 and b.status_active=1 and b.is_deleted=0 and a.company_id = $companyID
                        $bookingIdCond group by a.issue_number, a.issue_date, a.issue_purpose,a.knit_dye_company,a.knit_dye_source, b.prod_id
                         union all

                        select b.issue_number, b.issue_date, b.issue_purpose, b.knit_dye_company,b.knit_dye_source, c.prod_id, sum(a.qnty) as quantity
                        from pro_roll_details a,inv_issue_master b, inv_grey_fabric_issue_dtls c
                        where a.entry_form = 61 and a.mst_id = b.id and b.id = c.mst_id and a.dtls_id = c.id
                        and a.booking_no in ('$program_id')
                        and a.status_active = 1 and a.is_deleted = 0 and b.company_id = $companyID
                        group by b.issue_number, b.issue_date, b.issue_purpose, b.knit_dye_company,b.knit_dye_source, c.prod_id";


                        //echo $sql;

                        $result = sql_select($sql);
                        foreach ($result as $row) {
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            $total_receive_qnty += $row[csf('quantity')];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>"
                                onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="30"><? echo $i; ?></td>
                                <td width="125"><p><? echo $row[csf('issue_number')]; ?></p></td>
                                <td width="125"><p><? echo $service_type[$row[csf('issue_purpose')]]; ?></p></td>
                                <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                                <td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>

                                <td align="right"
                                width="80"><? echo number_format($row[csf('quantity')], 2, '.', ''); ?></td>

                                <td>
                                    <p><? if ($row[csf('knit_dye_source')] == 1) echo $company_library[$row[csf('knit_dye_company')]]; else if ($row[csf('knit_dye_source')] == 3) echo $supplier_details[$row[csf('knit_dye_company')]]; ?>
                                &nbsp;</p></td>

                            </tr>
                            <?
                            $i++;
                        }
                        ?>
                        <tfoot>
                            <th colspan="5" align="right">Total</th>
                            <th align="right"><? echo number_format($total_receive_qnty, 2, '.', ''); ?></th>
                            <th>&nbsp;</th>
                        </tfoot>
                    </table>
                </div>
            </div>
        </fieldset>
        <?
        exit();
}
if ($action == "grey_recv_for_batch_popup")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');

    extract($_REQUEST);
    $order_id = explode('_', $order_id);
    ?>
    <script>
        function print_window() {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";
        }
    </script>
    <div style="width:737px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
        style="width:100px" class="formbutton"/></div>
        <fieldset style="width:737px; margin-left:2px">
            <div id="report_container">
                <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                    <thead>
                        <th colspan="11"><b>Grey Issue Info</b></th>
                    </thead>
                    <thead>
                        <th width="30">SL</th>
                        <th width="125">Receive Id</th>
                        <th width="125">Receive Purpose</th>
                        <th width="75">Receive Date</th>
                        <th width="150">Product Details</th>
                        <th width="80">Receive Qnty</th>
                        <th>Kniting Com.</th>
                    </thead>
                </table>
                <div style="width:737px; max-height:330px; overflow-y:scroll" id="scroll_body">
                    <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                        <?
                        $i = 1;
                        $total_receive_qnty = 0;
                        $product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');

                        $sql_22 ="select a.recv_number as booking_no,a.id
                        from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c
                        where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=2 and c.entry_form=2 and a.receive_basis=2
                        and b.status_active=1 and b.is_deleted=0 and a.booking_id = '$program_id' and b.trans_id = 0 and a.company_id = $companyID";
                        $result_22 = sql_select($sql_22);
                        foreach($result_22 as $row_22)
                        {
                            $booking_id .= $row_22[csf('id')].",";
                        }

                        $booking_id =  chop($booking_id,',');


                        $sql_issue=sql_select("select b.issue_number, b.issue_date, b.issue_purpose, b.knit_dye_company,b.knit_dye_source, c.prod_id, sum(a.qnty) as quantity,a.barcode_no
                        from pro_roll_details a,inv_issue_master b, inv_grey_fabric_issue_dtls c
                        where a.entry_form = 61 and a.mst_id = b.id and b.id = c.mst_id and a.dtls_id = c.id
                        and a.booking_no in ('$program_id')
                        and a.status_active = 1 and a.is_deleted = 0 and b.company_id = $companyID
                        group by b.issue_number, b.issue_date, b.issue_purpose, b.knit_dye_company,b.knit_dye_source, c.prod_id,a.barcode_no");
                        $barcodeNos="";
                        foreach ($sql_issue as $row) {
                            $barcodeNos .= $row[csf('barcode_no')].",";
                        }
                        $barcodeNos =  chop($barcodeNos,',');



                        $sql = "select b.recv_number, b.receive_date, b.receive_basis,b.dyeing_company,b.dyeing_source, c.prod_id, sum(a.qnty) as quantity from inv_receive_mas_batchroll b,pro_grey_batch_dtls c,pro_roll_details a where b.id=c.mst_id and c.id=a.dtls_id and a.entry_form=62 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in ($barcodeNos) and b.company_id = $companyID  group by b.recv_number, b.receive_date, b.receive_basis,b.dyeing_company,b.dyeing_source, c.prod_id";


                        $result = sql_select($sql);
                        foreach ($result as $row) {
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            $total_receive_qnty += $row[csf('quantity')];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>"
                                onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="30"><? echo $i; ?></td>
                                <td width="125"><p><? echo $row[csf('recv_number')]; ?></p></td>
                                <td width="125"><p><? echo $yarn_issue_purpose[$row[csf('receive_basis')]]; ?></p></td>
                                <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                <td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>

                                <td align="right"
                                width="80"><? echo number_format($row[csf('quantity')], 2, '.', ''); ?></td>

                                <td>
                                    <p><? if ($row[csf('dyeing_source')] == 1) echo $company_library[$row[csf('dyeing_company')]]; else if ($row[csf('dyeing_source')] == 3) echo $supplier_details[$row[csf('dyeing_company')]]; ?>
                                &nbsp;</p></td>

                            </tr>
                            <?
                            $i++;
                        }
                        ?>
                        <tfoot>
                            <th colspan="5" align="right">Total</th>
                            <th align="right"><? echo number_format($total_receive_qnty, 2, '.', ''); ?></th>
                            <th>&nbsp;</th>
                        </tfoot>
                    </table>
                </div>
            </div>
        </fieldset>
        <?
        exit();
}
if ($action == "print") {
     echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
     extract($_REQUEST);
     $data = explode('*', $data);
     $company_id = $data[0];
     $program_id = $data[1];
    //echo $program_id; die;

     $company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
     $country_arr = return_library_array("select id,country_name from lib_country", 'id', 'country_name');
     $count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
     $brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
     $buyer_details = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");


     if ($db_type == 0) {
      $plan_details_array = return_library_array("select dtls_id, group_concat(po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company_id group by dtls_id", "dtls_id", "po_id");
  } else {
      $plan_details_array = return_library_array("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company_id group by dtls_id", "dtls_id", "po_id");
  }

  $sales_array = array();
  $po_dataArray = sql_select("select id, grouping, file_no, po_number, job_no_mst from wo_po_break_down");
  foreach ($po_dataArray as $row) {
      $sales_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
      $sales_array[$row[csf('id')]]['job_no'] = $row[csf('job_no_mst')];
      $sales_array[$row[csf('id')]]['file'] = $row[csf('file_no')];
      $sales_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
  }

  $product_details_array = array();
  $sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0";
  $result = sql_select($sql);

  foreach ($result as $row) {
      $compos = '';
      if ($row[csf('yarn_comp_percent2nd')] != 0) {
       $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
   } else {
       $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
   }

   $product_details_array[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
   $product_details_array[$row[csf('id')]]['comp'] = $compos;
   $product_details_array[$row[csf('id')]]['type'] = $yarn_type[$row[csf('yarn_type')]];
   $product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
   $product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
   $product_details_array[$row[csf('id')]]['color'] = $row[csf('color')];
}
?>
<div style="width:860px">
    <div style="margin-left:20px; width:850px">
        <div style="width:100px;float:left;position:relative;margin-top:10px">
            <? $image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$company_id' and form_name='company_details' and is_deleted=0"); ?>
            <img src="../../<? echo $image_location; ?>" height='100%' width='100%'/>
        </div>
        <div style="width:50px;float:left;position:relative;margin-top:10px"></div>
        <div style="width:710px;float:left;position:relative;">
            <table width="100%" style="margin-top:10px">
                <tr>
                    <td align="center" style="font-size:16px;">
                       <?
                       echo $company_details[$company_id];
                       ?>
                   </td>
               </tr>
               <tr>
                <td align="center" style="font-size:14px">
                   <?
                   $nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id");
                   foreach ($nameArray as $result) {
                    ?>
                    Plot No: <? echo $result['plot_no']; ?>
                    Level No: <? echo $result['level_no'] ?>
                    Road No: <? echo $result['road_no']; ?>
                    Block No: <? echo $result['block_no']; ?>
                    City No: <? echo $result['city']; ?>
                    Zip Code: <? echo $result['zip_code']; ?>
                    Country: <? echo $country_arr[$result['country_id']]; ?><br>
                    Email Address: <? echo $result['email']; ?>
                    Website No: <?
                    echo $result['website'];
                }
                ?>
            </td>
        </tr>
        <tr>
            <td height="10"></td>
        </tr>
        <tr>
            <td width="100%" align="center" style="font-size:14px;"><b><u>Knitting Program</u></b></td>
        </tr>
    </table>
</div>
</div>
<div style="margin-left:10px;float:left; width:850px">
   <?

   $dataArray = sql_select("select id, mst_id, knitting_source, knitting_party, program_date, color_range, stitch_length,spandex_stitch_length, feeder, machine_dia, machine_gg, program_qnty, remarks from ppl_planning_info_entry_dtls where id=$program_id");

   $mst_dataArray = sql_select("select booking_no, buyer_id, fabric_desc, gsm_weight, dia from ppl_planning_info_entry_mst where id=" . $dataArray[0][csf('mst_id')]);
   $booking_no = $mst_dataArray[0][csf('booking_no')];
   $buyer_id = $mst_dataArray[0][csf('buyer_id')];
   $fabric_desc = $mst_dataArray[0][csf('fabric_desc')];
   $gsm_weight = $mst_dataArray[0][csf('gsm_weight')];
   $dia = $mst_dataArray[0][csf('dia')];
   ?>
   <table width="100%" style="margin-top:20px" cellspacing="7">
    <tr>
        <td width="140"><b>Program No:</b></td>
        <td width="170"><? echo $dataArray[0][csf('id')]; ?></td>
        <td width="170"><b>Program Date:</b></td>
        <td><? echo change_date_format($dataArray[0][csf('program_date')]); ?></td>
    </tr>
    <tr>
        <td><b>Factory:</b></td>
        <td>
          <?
          if ($dataArray[0][csf('knitting_source')] == 1)
           echo $company_details[$dataArray[0][csf('knitting_party')]];
       else if ($dataArray[0][csf('knitting_source')] == 3)
           echo $supplier_details[$dataArray[0][csf('knitting_party')]];
       ?>
   </td>
   <td><b>Fabrication & FGSM:</b></td>
   <td><? echo $fabric_desc . " & " . $gsm_weight; ?></td>
</tr>
<tr>
    <td><b>Address:</b></td>
    <td colspan="3">
      <?
      $address = '';
      if ($dataArray[0][csf('knitting_source')] == 1) {
       $addressArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,city from lib_company where id=$company_id");
       foreach ($nameArray as $result) {
        ?>
        Plot No: <? echo $result['plot_no']; ?>
        Level No: <? echo $result['level_no'] ?>
        Road No: <? echo $result['road_no']; ?>
        Block No: <? echo $result['block_no']; ?>
        City No: <? echo $result['city']; ?>
        Country: <?
        echo $country_arr[$result['country_id']];
    }
} else if ($dataArray[0][csf('knitting_source')] == 3) {
   $address = return_field_value("address_1", "lib_supplier", "id=" . $dataArray[0][csf('knitting_party')]);
   echo $address;
}
?>
</td>
</tr>
<tr>
    <td><b>Buyer Name:</b></td>
    <td>
      <?
      echo $buyer_details[$buyer_id];

      $po_id = array_unique(explode(",", $plan_details_array[$dataArray[0][csf('id')]]));
      $po_no = '';
      $job_no = '';
      $ref_cond = '';
      $file_cond = '';

      foreach ($po_id as $val) {
       if ($po_no == '')
        $po_no = $sales_array[$val]['no'];
    else
        $po_no .= "," . $sales_array[$val]['no'];
    if ($job_no == '')
        $job_no = $sales_array[$val]['job_no'];
    if ($ref_cond == "")
        $ref_cond = $sales_array[$val]['ref'];
    else
        $ref_cond .= "," . $sales_array[$val]['ref'];
    if ($file_cond == "")
        $file_cond = $sales_array[$val]['file'];
    else
        $file_cond .= "," . $sales_array[$val]['file'];
}
?>
</td>
<td><b>Order No:</b></td>
<td><? echo $po_no; ?></td>
</tr>
<tr>
    <td><b>Booking No:</b></td>
    <td><b><? echo $booking_no; ?></b></td>
    <td><b>Job No:</b></td>
    <td><b><? echo $job_no; ?></b></td>
</tr>
<tr>
    <td><b>Internal Ref:</b></td>
    <td><b><? echo implode(",", array_unique(explode(",", $ref_cond))); ?></b></td>
    <td><b>File No:</b></td>
    <td><b><? echo implode(",", array_unique(explode(",", $file_cond))); ?></b></td>
</tr>
<tr>
    <td><b>Style Ref :</b></td>
    <td><?
      if ($job_no != '') {
       $style_val = return_field_value("style_ref_no", "wo_po_details_master", "job_no='$job_no'", "style_ref_no");
   }

   echo $style_val;
   ?></td>
</tr>
</table>

<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
class="rpt_table">
<thead>
    <th width="40">SL</th>
    <th width="80">Requisition No</th>
    <th width="80">Lot No</th>
    <th width="220">Yarn Description</th>
    <th width="100">Color</th>
    <th width="110">Brand</th>
    <th width="100">Requisition Qty.</th>
    <th>No of Cone</th>
</thead>
<?
$i = 1;
$tot_reqsn_qnty = 0;
$sql = "select requisition_no, prod_id,no_of_cone, yarn_qnty from ppl_yarn_requisition_entry where knit_id='" . $dataArray[0][csf('id')] . "' and status_active=1 and is_deleted=0";
$nameArray = sql_select($sql);
foreach ($nameArray as $selectResult) {
 ?>
 <tr>
    <td width="40" align="center"><? echo $i; ?></td>
    <td width="80">&nbsp;&nbsp;<? echo $selectResult[csf('requisition_no')]; ?></td>
    <td width="80">
        &nbsp;&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?></td>
        <td width="220">
            &nbsp;&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?></td>
            <td width="100">
                &nbsp;&nbsp;<? echo $color_library[$product_details_array[$selectResult[csf('prod_id')]]['color']]; ?></td>
                <td width="110">
                    &nbsp;&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?></td>
                    <td width="100" align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?>
                        &nbsp;&nbsp;</td>
                        <td align="right"><? echo number_format($selectResult[csf('no_of_cone')]); ?></td>
                    </tr>
                    <?
                    $tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
                    $i++;
                }
                ?>
                <tfoot>
                    <th colspan="6" align="right"><b>Total</b></th>
                    <th align="right"><? echo number_format($tot_reqsn_qnty, 2); ?>&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
            <table width="850" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top:20px;"
            class="rpt_table">
            <tr>
                <td width="100">&nbsp;&nbsp;<b>Colour:</b></td>
                <td width="120">&nbsp;&nbsp;<? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
                <td width="100">&nbsp;&nbsp;<b>GGSM OR S/L:</b></td>
                <td width="120">&nbsp;&nbsp;<? echo $dataArray[0][csf('stitch_length')]; ?></td>
                <td width="100">&nbsp;&nbsp;<b>Spandex S/L:</b></td>
                <td width="110">&nbsp;&nbsp;<? echo $dataArray[0][csf('spandex_stitch_length')]; ?></td>

                <td width="100">&nbsp;&nbsp;<b>FGSM:</b></td>
                <td>&nbsp;&nbsp;<? echo $gsm_weight; ?></td>
            </tr>
        </table>
        <table style="margin-top:20px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
        class="rpt_table">
        <thead>
            <th width="100">Finish Dia</th>
            <th width="230">Machine Dia & Gauge</th>
            <th width="80">Feeder</th>
            <th width="110">Program Qnty</th>


            <th>Remarks</th>
        </thead>
        <tr>
            <td width="100">&nbsp;&nbsp;<? echo $dia; ?></td>
            <td width="230">
                &nbsp;&nbsp;<? echo $dataArray[0][csf('machine_dia')] . "X" . $dataArray[0][csf('machine_gg')]; ?></td>
                <td width="80">&nbsp;&nbsp;<? echo $feeder[$dataArray[0][csf('feeder')]]; ?></td>
                <td width="110" align="right">
                    &nbsp;&nbsp;<? echo number_format($dataArray[0][csf('program_qnty')], 2); ?>&nbsp;&nbsp;</td>
                    <td><? echo $dataArray[0][csf('remarks')]; ?></td>
                </tr>
                <tr height="70" valign="middle">
                    <td colspan="5"><b>Advice:</b></td>
                </tr>
            </table>
            <table width="850">
                <tr>
                    <td width="100%" height="90" colspan="5"></td>
                </tr>
                <tr>
                    <td width="25%" align="center"><strong style="text-decoration:overline">Checked By</strong></td>
                    <td width="25%" align="center"><strong style="text-decoration:overline">Store Incharge</strong></td>
                    <td width="25%" align="center"><strong style="text-decoration:overline">Knitting Manager</strong>
                    </td>
                    <td width="25%" align="center"><strong style="text-decoration:overline">Authorised By</strong></td>
                </tr>
            </table>
        </div>
    </div>
    <?
    exit();
}

if ($action == "requisition_print")
{
    echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
    extract($_REQUEST);

    $program_ids = $data;

    $company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
    $brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
    if ($db_type == 0) {
        $plan_details_array = return_library_array("select dtls_id, group_concat(distinct(po_id)) as po_id from ppl_planning_entry_plan_dtls where dtls_id in($program_ids) group by dtls_id", "dtls_id", "po_id");
    } else {
        $plan_details_array = return_library_array("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where dtls_id in($program_ids) group by dtls_id", "dtls_id", "po_id");
    }


    $po_dataArray = sql_select("select id, po_number, job_no_mst from wo_po_break_down");
    foreach ($po_dataArray as $row) {
        $sales_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
        $sales_array[$row[csf('id')]]['job_no'] = $row[csf('job_no_mst')];
    }

    $product_details_array = array();
    $sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0";
    $result = sql_select($sql);

    foreach ($result as $row) {
        $compos = '';
        if ($row[csf('yarn_comp_percent2nd')] != 0) {
            $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
        } else {
            $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
        }

        $product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
        $product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
        $product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
        $product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
    }


    $knit_id_array = array();
    $prod_id_array = array();
    $rqsn_array = array();
    $reqsn_dataArray = sql_select("select knit_id, requisition_no, prod_id,sum(no_of_cone) as no_of_cone , sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where knit_id in($program_ids) and status_active=1 and is_deleted=0 group by knit_id, prod_id, requisition_no");
    foreach ($reqsn_dataArray as $row) {
        $prod_id_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('yarn_qnty')];
        $knit_id_array[$row[csf('knit_id')]] .= $row[csf('prod_id')] . ",";
        $rqsn_array[$row[csf('prod_id')]]['reqsn'] .= $row[csf('requisition_no')] . ",";
        $rqsn_array[$row[csf('prod_id')]]['qnty'] += $row[csf('yarn_qnty')];
        $rqsn_array[$row[csf('prod_id')]]['no_of_cone'] += $row[csf('no_of_cone')];
    }

    $order_no = '';
    $buyer_name = '';
    $knitting_factory = '';
    $job_no = '';
    $booking_no = '';
    $company = '';
    if ($db_type == 0) {
        $dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id, group_concat(distinct(b.po_id)) as po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id");
    } else {
        $dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id, LISTAGG(cast(b.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_id) as po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id");
    }
    foreach ($dataArray as $row) {
        if ($duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] == "") {
            $duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] = $row[csf('knitting_party')];

            if ($row[csf('knitting_source')] == 1)
                $knitting_factory .= $company_details[$row[csf('knitting_party')]] . ",";
            else if ($row[csf('knitting_source')] == 3)
                $knitting_factory .= $supplier_details[$row[csf('knitting_party')]] . ",";
        }

        if ($buyer_name == "")
            $buyer_name = $buyer_arr[$row[csf('buyer_id')]];
        if ($booking_no == "")
            $booking_no = $row[csf('booking_no')];
        if ($company == "")
            $company = $company_details[$row[csf('company_id')]];

        $po_id = explode(",", $row[csf('po_id')]);

        foreach ($po_id as $val) {
            $order_no .= $sales_array[$val]['no'] . ",";
            if ($job_no == "")
                $job_no = $sales_array[$val]['job_no'];
        }
    }

    $order_no = array_unique(explode(",", substr($order_no, 0, -1)));
    ?>
    <div style="width:1200px; margin-left:5px">
        <table width="100%" style="margin-top:10px">
            <tr>
                <td width="100%" align="center" style="font-size:20px;"><b><? echo $company; ?></b></td>
            </tr>
            <tr>
                <td width="100%" align="center" style="font-size:20px;"><b><u>Knitting Program</u></b></td>
            </tr>
        </table>
        <div style="border:1px solid;margin-top:10px; width:950px">
            <table width="100%" cellpadding="2" cellspacing="5">
                <tr>
                    <td width="140"><b>Knitting Factory </b></td>
                    <td>:</td>
                    <td><? echo substr($knitting_factory, 0, -1); ?></td>
                </tr>
                <tr>
                    <td><b>Buyer Name </b></td>
                    <td>:</td>
                    <td><? echo $buyer_name; ?></td>
                </tr>
                <tr>
                    <td><b>Style </b></td>
                    <td>:</td>
                    <td><?
                      if ($job_no != '') {
                       $style_val = return_field_value("style_ref_no", "wo_po_details_master", "job_no='$job_no'", "style_ref_no");
                   }

                   echo $style_val;
                   ?></td>
               </tr>
               <tr>
                <td><b>Order No </b></td>
                <td>:</td>
                <td><? echo implode(",", $order_no); ?></td>
            </tr>
            <tr>
                <td><b>Job No </b></td>
                <td>:</td>
                <td><? echo $job_no; ?></td>
            </tr>
            <tr>
                <td><b>Booking No </b></td>
                <td>:</td>
                <td><? echo $booking_no; ?></td>
            </tr>
        </table>
    </div>
    <table width="950" style="margin-top:10px" border="1" rules="all" cellpadding="0" cellspacing="0"
    class="rpt_table">
    <thead>
        <th width="30">SL</th>
        <th width="100">Requisition No</th>
        <th width="100">Brand</th>
        <th width="100">Lot No</th>
        <th width="200">Yarn Description</th>
        <th width="100">Color</th>
        <th width="100">Requisition Qty.</th>
        <th>No Of Cone</th>
    </thead>
    <?
    $j = 1;
    $tot_reqsn_qty = 0;
    foreach ($rqsn_array as $prod_id => $data) {
        if ($j % 2 == 0)
         $bgcolor = "#E9F3FF";
     else
         $bgcolor = "#FFFFFF";
     ?>
     <tr bgcolor="<? echo $bgcolor; ?>">
        <td width="30"><? echo $j; ?></td>
        <td width="100"><? echo substr($data['reqsn'], 0, -1); ?></td>
        <td width="100"><p><? echo $product_details_array[$prod_id]['brand']; ?>&nbsp;</p></td>
        <td width="100"><p><? echo $product_details_array[$prod_id]['lot']; ?></p></td>
        <td width="200"><p><? echo $product_details_array[$prod_id]['desc']; ?></p>
        </th>
        <td width="100"><p><? echo $product_details_array[$prod_id]['color']; ?>&nbsp;</p></td>
        <td width="100" align="right"><p><? echo number_format($data['qnty'], 2, '.', ''); ?></p></td>
        <td align="right"><? echo number_format($data['no_of_cone']); ?></td>
    </tr>
    <?
    $tot_reqsn_qty += $data['qnty'];
    $tot_no_of_cone += $data['no_of_cone'];
    $j++;
}
?>
<tfoot>
    <th colspan="6" align="right">Total</th>
    <th align="right"><? echo number_format($tot_reqsn_qty, 2, '.', ''); ?></th>
    <th><? echo number_format($tot_no_of_cone); ?></th>
</tfoot>
</table>

<table style="margin-top:10px;" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0"
class="rpt_table">
<thead>
    <th width="25">SL</th>
    <th width="60">Program No & Date</th>
    <th width="120">Fabrication</th>
    <th width="50">GSM</th>
    <th width="50">F. Dia</th>
    <th width="60">Dia Type</th>
    <th width="50">S/L</th>
    <th width="50">Spandex S/L</th>
    <th width="50">Feeder</th>
    <th width="60">Color</th>
    <th width="60">Color Range</th>
    <th width="60">Machine No</th>
    <th width="70">Machine Dia & GG</th>
    <th width="70">Knit Plan Date</th>
    <th width="70">Prpgram Qty.</th>
    <th width="110">Yarn Description</th>
    <th width="50">Lot</th>
    <th width="70">Yarn Qty.(KG)</th>
    <th>Remarks</th>
</thead>
<?
            //stitch_length,spandex_stitch_length, feeder, machine_dia, machine_gg, program_qnty, remarks from ppl_planning_info_entry_dtls
$i = 1;
$s = 1;
$tot_program_qnty = 0;
$tot_yarn_reqsn_qnty = 0;
$company_id = '';
$sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
$nameArray = sql_select($sql);
foreach ($nameArray as $row) {
    if ($i % 2 == 0)
     $bgcolor = "#E9F3FF";
 else
     $bgcolor = "#FFFFFF";

 $color = '';
 $color_id = explode(",", $row[csf('color_id')]);

 foreach ($color_id as $val) {
     if ($color == '')
      $color = $color_library[$val];
  else
      $color .= "," . $color_library[$val];
}

if ($company_id == '')
 $company_id = $row[csf('company_id')];

$machine_no = '';
$machine_id = explode(",", $row[csf('machine_id')]);

foreach ($machine_id as $val) {
 if ($machine_no == '')
  $machine_no = $machine_arr[$val];
else
  $machine_no .= "," . $machine_arr[$val];
}

if ($knit_id_array[$row[csf('program_id')]] != "") {
 $all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
 $row_span = count($all_prod_id);
 $z = 0;
 foreach ($all_prod_id as $prod_id) {
  ?>
  <tr bgcolor="<? echo $bgcolor; ?>">
   <?
   if ($z == 0) {
    ?>
    <td width="25" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
    <td width="60" rowspan="<? echo $row_span; ?>" align="center">
     <? echo $row[csf('program_id')] . '<br>' . change_date_format($row[csf('program_date')]); ?>
 </td>
 <td width="120" rowspan="<? echo $row_span; ?>">
    <p><? echo $row[csf('fabric_desc')]; ?></p></td>
    <td width="50" rowspan="<? echo $row_span; ?>">
        <p><? echo $row[csf('gsm_weight')]; ?></p></th>
        <td width="50" rowspan="<? echo $row_span; ?>">
            <p><? echo $row[csf('fabric_dia')]; ?></p></td>
            <td width="60" rowspan="<? echo $row_span; ?>">
                <p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>
                <td width="50" rowspan="<? echo $row_span; ?>">
                    <p><? echo $row[csf('stitch_length')]; ?></p></td>
                    <td width="50" rowspan="<? echo $row_span; ?>">
                        <p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
                        <td width="50" rowspan="<? echo $row_span; ?>">
                            <p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
                            <td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color; ?></p></td>
                            <td width="60" rowspan="<? echo $row_span; ?>">
                                <p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
                                <td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $machine_no; ?></p></td>
                                <td width="70" rowspan="<? echo $row_span; ?>">
                                    <p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
                                    <td width="70"
                                    rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]) . " to " . change_date_format($row[csf('end_date')]); ?></td>
                                    <td width="70" align="right"
                                    rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>
                                    &nbsp;</td>
                                    <?
                                    $tot_program_qnty += $row[csf('program_qnty')];
                                    $i++;
                                }
                                ?>
                                <td width="110"><p><? echo $product_details_array[$prod_id]['desc']; ?>&nbsp;</p></td>
                                <td width="50"><p><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</p></td>
                                <td width="70"
                                align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id], 2, '.', ''); ?></td>
                                <?
                                if ($z == 0) {
                                    ?>
                                    <td rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                                    <?
                                }
                                ?>
                            </tr>
                            <?
                            $tot_yarn_reqsn_qnty += $prod_id_array[$row[csf('program_id')]][$prod_id];
                            $z++;
                        }
                    } else {
                     ?>
                     <tr bgcolor="<? echo $bgcolor; ?>">
                        <td width="25"><? echo $i; ?></td>
                        <td width="60"
                        align="center"><? echo $row[csf('program_id')] . '<br>' . change_date_format($row[csf('program_date')]); ?></td>
                        <td width="120"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
                        <td width="50"><p><? echo $row[csf('gsm_weight')]; ?></p>
                        </th>
                        <td width="50"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
                        <td width="60"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>
                        <td width="50"><p><? echo $row[csf('stitch_length')]; ?></p></td>
                        <td width="50"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
                        <td width="50"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
                        <td width="60"><p><? echo $color; ?></p></td>
                        <td width="60"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
                        <td width="60"><p><? echo $machine_no; ?></p></td>
                        <td width="70"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
                        <td width="70"><? echo change_date_format($row[csf('start_date')]) . " to " . change_date_format($row[csf('end_date')]); ?></td>
                        <td width="70" align="right"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>
                            &nbsp;</td>
                            <td width="110"><p>&nbsp;</p></td>
                            <td width="50"><p>&nbsp;</p></td>
                            <td width="70" align="right">&nbsp;</td>
                            <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                        </tr>
                        <?
                        $tot_program_qnty += $row[csf('program_qnty')];
                        $i++;
                    }
                }
                ?>
                <tfoot>
                    <th colspan="14" align="right"><b>Total</b></th>
                    <th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_yarn_reqsn_qnty, 2, '.', ''); ?></th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
            <br>
            <?
            $sql_strip = "select a.color_number_id, a.stripe_color, a.measurement, a.uom, b.dtls_id, b.no_of_feeder as no_of_feeder from wo_pre_stripe_color a, ppl_planning_feeder_dtls b where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_id and a.color_number_id=b.color_id and a.stripe_color=b.stripe_color_id and b.dtls_id in($program_ids) and b.no_of_feeder>0 and a.status_active=1 and a.is_deleted=0";
            $result_stripe = sql_select($sql_strip);
            if (count($result_stripe) > 0) {
               ?>
               <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
                <thead>
                    <tr>
                        <th colspan="7">Stripe Measurement</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Prog. no</th>
                        <th width="140">Color</th>
                        <th width="130">Stripe Color</th>
                        <th width="70">Measurement</th>
                        <th width="50">UOM</th>
                        <th>No Of Feeder</th>
                    </tr>
                </thead>
                <?
                $i = 1;
                $tot_feeder = 0;
                foreach ($result_stripe as $row) {
                 if ($i % 2 == 0)
                  $bgcolor = "#E9F3FF";
              else
                  $bgcolor = "#FFFFFF";
              $tot_feeder += $row[csf('no_of_feeder')];
              ?>
              <tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="50" align="center"><? echo $row[csf('dtls_id')]; ?></td>
                <td width="140"><p><? echo $color_library[$row[csf('color_number_id')]]; ?></p></td>
                <td width="130"><p><? echo $color_library[$row[csf('stripe_color')]]; ?></p></td>
                <td width="70" align="center"><? echo $row[csf('measurement')]; ?></td>
                <td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                <td align="right" style="padding-right:10px"><? echo $row[csf('no_of_feeder')]; ?>&nbsp;</td>
            </tr>
            <?
            $tot_masurement += $row[csf('measurement')];
            $i++;
        }
        ?>
    </tbody>
    <tfoot>
        <th colspan="4">Total</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th style="padding-right:10px"><? echo $tot_feeder; ?>&nbsp;</th>
    </tfoot>
</table>
<?
}
echo signature_table(41, $company_id, "1180px");
?>
</div>
<?
exit();
}

if ($action == "requisition_print_one")
{
    extract($_REQUEST);
    $data = explode('**', $data);
    if ($data[2])
    {
        echo load_html_head_contents("Program Qnty Info", "../", 1, 1, '', '', '');
    }
    else
    {
        echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
    }

    $typeForAttention = $data[1];
    $program_ids = $data[0];
    $within_group = $data[3];
    //$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
    $brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
    $floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
    $yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by id, yarn_count","id","yarn_count");
    $imge_arr = return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    $location_array=return_library_array( "select id, location_name from lib_location",'id','location_name');

    $company_info = sql_select("select ID, COMPANY_NAME, PLOT_NO, ROAD_NO, CITY, CONTACT_NO, COUNTRY_ID from lib_company where status_active=1 and is_deleted=0 order by company_name");
    foreach($company_info as $row)
    {
        $company_details[$row['ID']] = $row['COMPANY_NAME'];
        $company_address_arr[$row['ID']] = 'Plot No:'.$row['PLOT_NO'].', Road No:'.$row['ROAD_NO'].', City / Town:'.$row['CITY'].', Country:'.$country_name_arr[$row['COUNTRY_ID']].', Contact No:'.$row['CONTACT_NO'];
    }
    unset($company_info);

    //for supplier
    $sqlSupplier = sql_select("select id as ID, supplier_name as SUPPLIER_NAME, short_name as SHORT_NAME, ADDRESS_1 from lib_supplier");
    foreach($sqlSupplier as $row)
    {
        $supplier_arr[$row['ID']] = $row['SHORT_NAME'];
        $supplier_dtls_arr[$row['ID']] = $row['SUPPLIER_NAME'];
        $supplier_address_arr[$row['ID']] = $row['ADDRESS_1'];
    }
    unset($sqlSupplier);

    //for sales order information
    $po_dataArray = sql_select("SELECT ID, JOB_NO, BUYER_ID, STYLE_REF_NO, WITHIN_GROUP, SALES_BOOKING_NO, BOOKING_WITHOUT_ORDER FROM FABRIC_SALES_ORDER_MST WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PO_ID FROM PPL_PLANNING_ENTRY_PLAN_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0)");
    foreach ($po_dataArray as $row)
    {
        $sales_array[$row['ID']]['no'] = $row['JOB_NO'];
        $sales_array[$row['ID']]['sales_booking_no'] = $row['SALES_BOOKING_NO'];
        $sales_array[$row['ID']]['buyer_id'] = $row['BUYER_ID'];
        $sales_array[$row['ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
        $sales_array[$row['ID']]['within_group'] = $row['WITHIN_GROUP'];
        $sales_array[$row['ID']]['booking_without_order'] = $row['BOOKING_WITHOUT_ORDER'];
    }

    //for booking information
    $book_dataArray = sql_select("SELECT A.BUYER_ID, B.BOOKING_NO, B.PO_BREAK_DOWN_ID AS PO_ID, B.JOB_NO, C.PO_NUMBER, C.GROUPING, D.STYLE_REF_NO FROM WO_BOOKING_MST A,WO_BOOKING_DTLS B, WO_PO_BREAK_DOWN C,WO_PO_DETAILS_MASTER D WHERE A.BOOKING_NO=B.BOOKING_NO AND B.PO_BREAK_DOWN_ID=C.ID AND C.JOB_NO_MST=D.JOB_NO AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND B.BOOKING_NO IN(SELECT SALES_BOOKING_NO FROM FABRIC_SALES_ORDER_MST WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PO_ID FROM PPL_PLANNING_ENTRY_PLAN_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0))");
    foreach ($book_dataArray as $row)
    {
        $booking_array[$row['BOOKING_NO']]['booking_no'] = $row['BOOKING_NO'];
        $booking_array[$row['BOOKING_NO']]['po_id'] = $row['PO_ID'];
        $booking_array[$row['BOOKING_NO']]['buyer_id'] = $row['BUYER_ID'];
        $booking_array[$row['BOOKING_NO']]['po_no'] = $row['PO_NUMBER'];
        $booking_array[$row['BOOKING_NO']]['job_no'] = $row['JOB_NO'];
        $booking_array[$row['BOOKING_NO']]['style_ref_no'] = $row['STYLE_REF_NO'];
        $booking_array[$row['BOOKING_NO']]['internal_ref'] = $row['GROUPING'];
    }

    //for product information
    $product_details_array = array();
    $sql = "SELECT ID, SUPPLIER_ID, LOT, CURRENT_STOCK, YARN_COMP_TYPE1ST, YARN_COMP_PERCENT1ST, YARN_COMP_TYPE2ND, YARN_COMP_PERCENT2ND, YARN_COUNT_ID, YARN_TYPE, COLOR, BRAND FROM PRODUCT_DETAILS_MASTER WHERE ITEM_CATEGORY_ID=1 AND STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PROD_ID FROM PPL_YARN_REQUISITION_ENTRY WHERE KNIT_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0 GROUP BY PROD_ID)";
    $result = sql_select($sql);
    foreach ($result as $row)
    {
        $compos = '';
        if ($row['YARN_COMP_PERCENT2ND'] != 0)
        {
            $compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
        }
        else
        {
            $compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']];
        }

        $product_details_array[$row['ID']]['desc'] = $count_arr[$row['YARN_COUNT_ID']] . " " . $compos . " " . $yarn_type[$row['YARN_TYPE']];
        $product_details_array[$row['ID']]['lot'] = $row['LOT'];
        $product_details_array[$row['ID']]['brand'] = $brand_arr[$row['BRAND']];
        $product_details_array[$row['ID']]['color'] = $color_library[$row['COLOR']];
    }

    //for requisition information
    $knit_id_array = array();
    $prod_id_array = array();
    $rqsn_array = array();
    $reqsn_dataArray = sql_select("SELECT KNIT_ID, REQUISITION_NO, REQUISITION_DATE, PROD_ID, SUM(NO_OF_CONE) AS NO_OF_CONE, SUM(YARN_QNTY) AS YARN_QNTY FROM PPL_YARN_REQUISITION_ENTRY WHERE KNIT_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0 GROUP BY KNIT_ID, PROD_ID, REQUISITION_NO,REQUISITION_DATE");
    foreach ($reqsn_dataArray as $row)
    {
        $prod_id_array[$row['KNIT_ID']][$row['PROD_ID']] = $row['YARN_QNTY'];
        $knit_id_array[$row['KNIT_ID']] .= $row['PROD_ID'] . ",";
        $rqsn_array[$row['PROD_ID']]['reqsn'] .= $row['REQUISITION_NO'] . ",";
        $rqsn_array[$row['PROD_ID']]['reqsd'] .= $row['REQUISITION_DATE'] . ",";
        $rqsn_array[$row['PROD_ID']]['qnty'] += $row['YARN_QNTY'];
        $rqsn_array[$row['PROD_ID']]['no_of_cone'] += $row['NO_OF_CONE'];
    }

    $sales_order_no = '';
    $buyer_name = '';
    $knitting_factory = '';
    $booking_no = '';
    $wg_yes_booking = '';
    $company = '';
    $order_buyer = '';
    $style_ref_no = '';
    $program_date = '';
    $location_id = '';

    $dataArray = sql_select("SELECT A.ID, A.KNITTING_SOURCE, A.KNITTING_PARTY, B.BUYER_ID, B.BOOKING_NO, B.COMPANY_ID, LISTAGG(CAST(B.PO_ID AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY B.PO_ID) AS PO_ID, A.IS_SALES, B.WITHIN_GROUP, A.PROGRAM_DATE, A.LOCATION_ID FROM PPL_PLANNING_INFO_ENTRY_DTLS A, PPL_PLANNING_ENTRY_PLAN_DTLS B WHERE A.ID=B.DTLS_ID AND A.ID IN (".$program_ids.") AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 GROUP BY A.ID, A.KNITTING_SOURCE, A.KNITTING_PARTY, B.BUYER_ID, B.BOOKING_NO, B.COMPANY_ID,A.IS_SALES,B.WITHIN_GROUP,A.PROGRAM_DATE,A.LOCATION_ID");
    $k_source = "";
    $sup = $sales_ids = "";
    foreach ($dataArray as $row)
    {
        if ($duplicate_arr[$row['KNITTING_SOURCE']][$row['KNITTING_PARTY']] == "")
        {
            $duplicate_arr[$row['KNITTING_SOURCE']][$row['KNITTING_PARTY']] = $row['KNITTING_PARTY'];
        }

        $knitting_factory_address = '';
        if ($row['KNITTING_SOURCE'] == 1)
        {
            $knitting_factory .= $company_details[$row['KNITTING_PARTY']] . ",";
            $knitting_factory_address .= $company_address_arr[$row['KNITTING_PARTY']] . ",";
        }
        else if ($row['KNITTING_SOURCE'] == 3)
        {
            $knitting_factory .= $supplier_details[$row['KNITTING_PARTY']] . ",";
            $knitting_factory_address .= $supplier_address_arr[$row['KNITTING_PARTY']] . ",";
        }
        $knitting_factory=implode(",",array_unique(explode(",",$knitting_factory)));
        $knitting_factory_address=implode(",",array_unique(explode(",",$knitting_factory_address)));

        if ($buyer_name == "")
        {
            $buyer_name = $buyer_arr[$row['BUYER_ID']];
        }

        if ($booking_no != '')
        {
            $booking_no .= "," . $row['BOOKING_NO'];
        }
        else
        {
            $booking_no = $row['BOOKING_NO'];
        }

        if ($program_date != '')
        {
            $program_date .= "," . $row['PROGRAM_DATE'];
        }
        else
        {
            $program_date = $row['PROGRAM_DATE'];
        }

        if ($company == "")
        {
            $company = $company_details[$row['COMPANY_ID']];
        }

        if ($location_id == "")
        {
            $location_id = $location_array[$row['LOCATION_ID']];
        }

        if ($company_id == "")
        {
            $company_id = $row['COMPANY_ID'];
        }
        $order_nos .= "," . $booking_array2[$row['BOOKING_NO']]['po_no'];
        $is_sales = $row['IS_SALES'];
        $sales_ids .= $row['PO_ID'] . ",";
        $k_source = $row['KNITTING_SOURCE'];
        $sup = $row['KNITTING_PARTY'];
    }
    $sales_id = array_unique(explode(",", $sales_ids));
    $booking_nos = array_unique(explode(",", $booking_no));
    $program_dates = array_unique(explode(",", $program_date));

    $order_buyer=$style_ref_no=$job_no=$order_nos="";
    foreach ($sales_id as $pid)
    {
        $sales_order_no .= $sales_array[$pid]['no'] . ",";
        if ($sales_array[$pid]['within_group'] == 2)
        {
            $order_buyer .= $buyer_arr[$sales_array[$pid]['buyer_id']] . ",";
            $style_ref_no .= "," . $sales_array[$pid]['style_ref_no'];
            $job_no .= "";
            $order_ids .= "";
            $internal_ref .= "";
        }
        else
        {
            if($sales_array[$pid]['booking_without_order'] != 1)
            {
                $order_buyer .= $buyer_arr[$booking_array[$sales_array[$pid]['sales_booking_no']]['buyer_id']] . ",";
            }
            else
            {
                //for sample without order
                $booking_buyer = return_field_value("buyer_id", "wo_non_ord_samp_booking_mst", "booking_no='".$sales_array[$pid]['sales_booking_no']."'");
                $order_buyer .= $buyer_arr[$booking_buyer].",";
            }

            $style_ref_no .= "," . $booking_array[$sales_array[$pid]['sales_booking_no']]['style_ref_no'];
            $job_no .= $booking_array[$sales_array[$pid]['sales_booking_no']]['job_no'] . ",";
            $order_ids .= $booking_array[$sales_array[$pid]['sales_booking_no']]['po_no'] . ",";
            $internal_ref .= $booking_array[$sales_array[$pid]['sales_booking_no']]['internal_ref'] . ",";
        }
    }

    $sales_nos = rtrim(implode(",", array_unique(explode(",", $sales_order_no))), ",");
    $order_buyers = rtrim(implode(",", array_unique(explode(",", $order_buyer))), ",");
    $style_ref_nos = ltrim(implode(",", array_unique(explode(",", $style_ref_no))), ",");
    $job_nos = implode(",", array_unique(explode(",", rtrim($job_no,","))));
    $booking_noss = implode(",", $booking_nos);
    $program_datess = implode(",", $program_dates);
    if($program_ids!="")
    {
        $feedingResult =  sql_select("SELECT DTLS_ID, SEQ_NO, COUNT_ID, FEEDING_ID FROM PPL_PLANNING_COUNT_FEED_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0");

        $feedingDataArr = array();
        foreach ($feedingResult as $row)
        {
            $feedingSequence[$row['SEQ_NO']] = $row['SEQ_NO'];
            $feedingDataArr[$row['DTLS_ID']][$row['SEQ_NO']]['count_id'] = $row['COUNT_ID'];
            $feedingDataArr[$row['DTLS_ID']][$row['SEQ_NO']]['feeding_id'] = $row['FEEDING_ID'];
        }
    }
    ?>
    <div style="width:1200px; margin-left:5px;">
        <div style="width:100px;float:left;position:relative;margin-top:10px;margin-bottom:-80px">
            <img src='../../<? echo $imge_arr[$company_id]; ?>' height='100%' width='100%'  />
        </div>
        <table width="100%" style="margin-top:10px">

            <tr>
                <td width="100%" align="center" style="font-size:20px;">

                <b><? echo $company; ?></b></td>
            </tr>
            <tr>
                <td align="center" style="font-size:14px">
                 <?
                 echo show_company($company_id, '', '');
                 ?>
             </td>
         </tr>
         <tr>
            <td width="100%" align="center" style="font-size:20px;"><b><u>Knitting Program</u></b></td>
            </tr>
            <tr>
                <td width="100%" align="center" style="font-size:16px;"><? echo $location_id; ?></td>
            </tr>
        </table>
        <div style="margin-top:10px; width:950px">
            <table width="100%" cellpadding="2" cellspacing="5">
                <tr>
                    <td width="140"><b style="font-size:18px">Knitting Factory </b></td>
                    <td>:</td>
                    <td style="font-size:18px"><b><? echo substr($knitting_factory, 0, -1); ?></b></td>
                </tr>
                <tr>
                    <td width="140"><b style="font-size:16px">Address</b></td>
                    <td>:</td>
                    <td style="font-size:16px"><? echo substr($knitting_factory_address, 0, -1); ?></td>
                </tr>
                <tr>
                    <td width="140" style="font-size:18px"><b>Attention </b></td>
                    <td>:</td>
                    <?
                    if ($typeForAttention == 1)
                    {
                      echo "<td style=\"font-size:18px; font-weight:bold;\"> Knitting Manager </td>";
                    }
                    else
                    {
                      ?>
                      <td style="font-size:18px; font-weight:bold;"><b><?
                        if ($k_source == 3)
                        {
                         $ComArray = sql_select("select id,contact_person from lib_supplier where id=$sup");
                         foreach ($ComArray as $row)
                         {
                          echo $row[csf('contact_person')];
                        }
                  } else {

                     echo "";
                 }
                 ?></b></td>
                 <? } ?>
                </tr>
                <tr>
                    <td style="font-size:16px"><b>Buyer Name </b></td>
                    <td>:</td>
                    <td style="font-size:16px"><? echo $order_buyers; ?></td>
                    <td><span  id="barcode_img_id" ></span></td>

                </tr>
                <tr>
                    <td style="font-size:16px"><b>Style </b></td>
                    <td>:</td>
                    <td style="font-size:16px"><? echo $style_ref_nos; ?></td>
                </tr>
                <tr>
                    <td style="font-size:16px"><b>Order No </b></td>
                    <td>:</td>
                    <td style="font-size:16px"><? echo rtrim($order_ids,","); ?></td>
                </tr>
                <tr>
                    <td style="font-size:16px"><b>Internal Ref. </b></td>
                    <td>:</td>
                    <td style="font-size:16px"><? echo rtrim($internal_ref,","); ?></td>
                </tr>
                <tr>
                    <td style="font-size:16px"><b>Job No </b></td>
                    <td>:</td>
                    <td style="font-size:16px"><? echo $job_nos; ?></td>
                </tr>
                <tr>
                    <td style="font-size:16px"><b>Booking No </b></td>
                    <td>:</td>
                    <td style="font-size:16px"><? echo $booking_noss; ?></td>
                    <td style="font-size:16px"><b>&nbsp;&nbsp;Prog. Date</b> : <? echo change_date_format($program_datess); ?></td>
                </tr>
                <tr>
                    <td style="font-size:16px"><b>Sales Order No </b></td>
                    <td>:</td>
                    <td style="font-size:16px"><? echo $sales_nos; ?></td>
                </tr>
            </table>
        </div>
    <table width="1050" style="margin-top:10px" border="1" rules="all" cellpadding="0" cellspacing="0"
    class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="100">Requisition No</th>
            <th width="100">Requisition Date</th>
            <th width="100">Brand</th>
            <th width="100">Lot No</th>
            <th width="200">Yarn Description</th>
            <th width="100">Color</th>
            <th width="100">Requisition Qty.</th>
            <th>No Of Cone</th>
        </thead>
        <?
        $j = 1;
        $tot_reqsn_qty = 0;
        foreach ($rqsn_array as $prod_id => $data)
        {
            if ($j % 2 == 0)
                $bgcolor = "#E9F3FF";
            else
                $bgcolor = "#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <td width="30"><? echo $j; ?></td>
                <td width="100"><? echo substr($data['reqsn'], 0, -1); ?></td>
                <td width="100"><? echo substr($data['reqsd'], 0, -1); ?></td>
                <td width="100"><p><? echo $product_details_array[$prod_id]['brand']; ?>&nbsp;</p></td>
                <td width="100"><p><? echo $product_details_array[$prod_id]['lot']; ?></p></td>
                <td width="200"><p><? echo $product_details_array[$prod_id]['desc']; ?></p>
                </th>
                <td width="100"><p><? echo $product_details_array[$prod_id]['color']; ?>&nbsp;</p></td>
                <td width="100" align="right"><p><? echo number_format($data['qnty'], 2, '.', ''); ?></p></td>
                <td align="right"><? echo number_format($data['no_of_cone']); ?></td>
            </tr>
            <?
            $tot_reqsn_qty += $data['qnty'];
            $tot_no_of_cone += $data['no_of_cone'];
            $j++;
        }
        ?>
        <tfoot>
            <th colspan="7" align="right">Total</th>
            <th align="right"><? echo number_format($tot_reqsn_qty, 2, '.', ''); ?></th>
            <th><? echo number_format($tot_no_of_cone); ?></th>
        </tfoot>
    </table>

    <table style="margin-top:10px;" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0"
    class="rpt_table" align="center">
        <thead align="center">
            <th width="25">SL</th>
            <th width="50">Program No</th>
            <th width="120">Batch No</th>
            <th width="120">Tube/Ref. No</th>
            <th width="100">Color Type</th>
            <th width="120">Fabrication</th>
            <th width="50">GSM</th>
            <th width="40">F. Dia</th>
            <th width="60">Dia Type</th>

            <th width="45">Floor</th>

            <th width="45">M/c. No</th>
            <th width="50">M/c. Dia & GG</th>
            <th width="100">Color</th>
            <th width="60">Color Range</th>
            <th width="50">S/L</th>
            <th width="50">Spandex S/L</th>
            <th width="50">Feeder</th>
            <th width="100">Count Feeding</th>
            <th width="70">Knit Start</th>
            <th width="70">Knit End</th>
            <th width="70">Prpgram Qty.</th>
            <th width="110">Yarn Description</th>
            <th width="50">Lot</th>
            <th width="70">Yarn Qty.(KG)</th>
            <th>Remarks</th>
        </thead>
        <?
        $i = 1;
        $s = 1;
        $tot_program_qnty = 0;
        $tot_yarn_reqsn_qnty = 0;
        $company_id = '';
        $sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks,b.advice,b.batch_no,b.tube_ref_no, a.color_type_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
        //echo $sql;
        $nameArray = sql_select($sql);
        $advice = "";
        foreach ($nameArray as $row)
        {
            if ($i % 2 == 0)
             $bgcolor = "#E9F3FF";
            else
             $bgcolor = "#FFFFFF";

            $color = '';
            $color_id = explode(",", $row[csf('color_id')]);

            foreach ($color_id as $val)
            {
                if ($color == '')
                  $color = $color_library[$val];
                else
                  $color .= "," . $color_library[$val];
            }

            if ($company_id == '')
            $company_id = $row[csf('company_id')];

            $machine_no = '';
            $machine_id = explode(",", $row[csf('machine_id')]);

            foreach ($machine_id as $val)
            {
                if ($machine_no == '')
                    $machine_no = $machine_arr[$val];
                else
                    $machine_no .= "," . $machine_arr[$val];
            }
            if ($machine_id[0] != "")
            {
             $sql_floor = sql_select("select id, machine_no, floor_id from lib_machine_name where id=$machine_id[0] and status_active=1 and is_deleted=0  order by seq_no");
            }

            $count_feeding = "";
            foreach($feedingDataArr[$row[csf('program_id')]] as $feedingSequence=>$feedingData)
            {
                if($count_feeding =="")
                {
                    $count_feeding = $feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
                }
                else
                {
                    $count_feeding .= ",".$feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
                }
            }

            if ($knit_id_array[$row[csf('program_id')]] != "") {
            $all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
            $row_span = count($all_prod_id);
            $z = 0;

            foreach ($all_prod_id as $prod_id)
            {
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <?
                    if ($z == 0)
                    {
                        ?>
                        <td width="25" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
                        <td width="60" rowspan="<? echo $row_span; ?>" align="center" style="font-size:16px;">
                        <? echo $row[csf('program_id')]; ?></td>
                        <td width="120" rowspan="<? echo $row_span; ?>" align="center">
                        <? echo $row[csf('batch_no')]; ?></td>
                        <td width="120" rowspan="<? echo $row_span; ?>" align="center">
                        <? echo $row[csf('tube_ref_no')]; ?></td>
                        <td width="100" rowspan="<? echo $row_span; ?>" align="center">
                        <? echo $color_type[$row[csf('color_type_id')]]; ?></td>
                        <td width="120" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
                        <td width="50" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('gsm_weight')]; ?></p></th>
                        <td width="50" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
                        <td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>
                        <td width="60" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>
                        <td width="60" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $machine_no; ?></p></td>
                        <td width="70" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
                        <td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color; ?></p></td>
                        <td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
                        <td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('stitch_length')]; ?></p></td>
                        <td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
                        <td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
                        <td width="100" rowspan="<? echo $row_span; ?>"><p><? echo $count_feeding; ?></p></td>
                        <td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
                        <td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
                        <td width="70" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?></td>
                        <?
                        $tot_program_qnty += $row[csf('program_qnty')];
                            $i++;
                    }
                    ?>
                    <td width="110"><p><? echo $product_details_array[$prod_id]['desc']; ?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</p></td>
                    <td width="70" align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id], 2, '.', ''); ?></td>
                    <?
                    if ($z == 0) {
                        ?>
                        <td rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                        <?
                    }
                    ?>
                </tr>
                    <?
                    $tot_yarn_reqsn_qnty += $prod_id_array[$row[csf('program_id')]][$prod_id];
                    $z++;
                }
            }
            else
            {
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td width="25"><? echo $i; ?></td>
                    <td width="60" align="center" style="font-size:16px;"><b><? echo $row[csf('program_id')]; ?></b></td>
                    <td width="120" align="center"><? echo $row[csf('batch_no')]; ?></td>
                    <td width="120" align="center"><? echo $row[csf('tube_ref_no')]; ?></td>
                    <td width="100" align="center"><? echo $color_type[$row[csf('color_type_id')]]; ?></td>
                    <td width="120"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
                    <td width="50" align="center"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
                    <td width="50" align="center"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
                    <td width="60"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>
                    <td width="50" align="center"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>
                    <td width="50" align="center"><p><? echo $machine_no; ?></p></td>
                    <td width="50"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
                    <td width="50"><p><? echo $color; ?></p></td>
                    <td width="60"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
                    <td width="60"><p><? echo $row_span; ?><p><? echo $row[csf('stitch_length')]; ?></p></td>
                    <td width="60"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
                    <td width="70"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
                    <td width="100" rowspan="<? echo $row_span; ?>"><p><? echo $count_feeding; ?></p></td>
                    <td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
                    <td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
                    <td width="70" align="right"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?></td>
                    <td width="110"><p>&nbsp;</p></td>
                    <td width="50"><p>&nbsp;</p></td>
                    <td width="70" align="right">&nbsp;</td>
                    <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                </tr>
                <?
                $tot_program_qnty += $row[csf('program_qnty')];
                $i++;
            }
            $advice = $row[csf('advice')];
        }
        ?>
        <tfoot>
            <th colspan="20" align="right"><b>Total</b></th>
            <th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>

            <th align="right"><? echo number_format($tot_yarn_reqsn_qnty, 2, '.', ''); ?></th>
            <th>&nbsp;</th>
        </tfoot>
    </table>
    <br>
    <?
    $sql_collarCuff = sql_select("select id, body_part_id, grey_size, finish_size, qty_pcs from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id in($program_ids) order by id");
    if (count($sql_collarCuff) > 0)
    {
       ?>
       <table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
       class="rpt_table">
       <thead>
            <tr>
                <th width="50">SL</th>
                <th width="200">Body Part</th>
                <th width="200">Grey Size</th>
                <th width="200">Finish Size</th>
                <th>Quantity Pcs</th>
            </tr>
        </thead>
        <tbody>
            <?
            $i = 1;
            $total_qty_pcs = 0;
            foreach ($sql_collarCuff as $row) {
             if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
             ?>
             <tr>
                <td align="center"><p><? echo $i; ?>&nbsp;</p></td>
                <td><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</p></td>
                <td style="padding-left:5px"><p><? echo $row[csf('grey_size')]; ?>&nbsp;</p></td>
                <td style="padding-left:5px"><p><? echo $row[csf('finish_size')]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo number_format($row[csf('qty_pcs')], 0);
                    $total_qty_pcs += $row[csf('qty_pcs')]; ?>&nbsp;&nbsp;</p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th align="right">Total</th>
                <th align="right"><? echo number_format($total_qty_pcs, 0); ?>&nbsp;</th>
            </tr>
        </tfoot>
        </table>
        <?
    }
    ?>
    <br>
    <?
    $sql_strip = "select a.color_number_id, a.stripe_color, a.measurement, a.uom, b.dtls_id, b.no_of_feeder as no_of_feeder from wo_pre_stripe_color a, ppl_planning_feeder_dtls b where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_id and a.color_number_id=b.color_id and a.stripe_color=b.stripe_color_id and b.dtls_id in($program_ids) and b.no_of_feeder>0 and a.status_active=1 and a.is_deleted=0";
    $result_stripe = sql_select($sql_strip);
    if (count($result_stripe) > 0)
    {
       ?>
       <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
        <thead>
            <tr>
                <th colspan="7">Stripe Measurement</th>
            </tr>
            <tr>
                <th width="30">SL</th>
                <th width="60">Prog. no</th>
                <th width="140">Color</th>
                <th width="130">Stripe Color</th>
                <th width="70">Measurement</th>
                <th width="50">UOM</th>
                <th>No Of Feeder</th>
            </tr>
        </thead>
        <?
        $i = 1;
        $tot_feeder = 0;
        foreach ($result_stripe as $row)
        {
            if ($i % 2 == 0)
            $bgcolor = "#E9F3FF";
            else
            $bgcolor = "#FFFFFF";
            $tot_feeder += $row[csf('no_of_feeder')];
            ?>
            <tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="50" align="center"><? echo $row[csf('dtls_id')]; ?></td>
                <td width="140"><p><? echo $color_library[$row[csf('color_number_id')]]; ?></p></td>
                <td width="130"><p><? echo $color_library[$row[csf('stripe_color')]]; ?></p></td>
                <td width="70" align="center"><? echo $row[csf('measurement')]; ?></td>
                <td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                <td align="right" style="padding-right:10px"><? echo $row[csf('no_of_feeder')]; ?>&nbsp;</td>
            </tr>
            <?
            $tot_masurement += $row[csf('measurement')];
            $i++;
        }
        ?>
        </tbody>
        <tfoot>
            <th colspan="4">Total</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th style="padding-right:10px"><? echo $tot_feeder; ?>&nbsp;</th>
        </tfoot>
        </table>
        <?
    }
    ?>
    <table border="1" rules="all" class="rpt_table">
        <tr>
            <td style="font-size:24px; font-weight:bold; width:20px;">ADVICE:</td>
            <td style="font-size:20px; width:100%;">     <? echo $advice; ?></td>
        </tr>
    </table>
    <div style="margin-top:60px; text-align: left;"><strong>Rate/Kg =</strong></div>
    <br/>
    <div style="float:left; width: 401px;">
        <div style="border:1px solid #000;">
        <table border="1" rules="all" class="rpt_table" width="400" height="200">
            <thead>
                <th colspan="2" style="font-size:20px; font-weight:bold;">Please Strictly Avoid The Following Faults….</th>
            <thead>
            <tbody>
                <tr>
                    <td style="width:190px; font-size:14px;"><b> 1.</b> Patta</td>
                    <td style="font-size:14px;"><b> 8.</b> Sinker mark</td>
                </tr>
                <tr>
                    <td style="font-size:14px;"><b> 2.</b> Loop</td>
                    <td style="font-size:14px;"><b> 9.</b> Needle mark</td>
                </tr>
                <tr>
                    <td style="font-size:14px;"><b> 3.</b> Hole</td>
                    <td style="font-size:14px;"><b> 10.</b> Oil mark</td>
                </tr>
                <tr>
                    <td><b> 4.</b> Star marks</td>
                    <td><b> 11.</b> Dia mark/Crease Mark</td>
                </tr>
                <tr>
                    <td style="font-size:14px;"><b> 5.</b> Barre</td>
                    <td style="font-size:14px;"><b> 12.</b> Wheel Free</td>
                </tr>
                <tr>
                    <td style="font-size:14px;"><b> 6.</b> Drop Stitch</td>
                    <td style="font-size:14px;"><b> 13.</b> Slub</td>
                </tr>
                <tr>
                    <td style="font-size:14px;"><b> 7.</b> Lot mixing</td>
                    <td style="font-size:14px;"><b> 14.</b> Other contamination</td>
                </tr>
            </tbody>
        </table>
        </div>
        <br>
        <div>
        <p>বিঃ দ্রঃ<br>
        * প্রোগ্রামের শেষ পর্যায়ে অবশ্যই সূতা মিল করে চালাতে হবে।<br>
        * সূতা মেশিনে উঠানোর সময় ১/১ করে উঠতে হবে।<br>
        * রোল মার্কিং সঠিকভাবে করতে হবে।<br>
        * রোলে কোন প্রকার কাটা-কাটি বা ভুল লেখা চলবেনা।<br>
        * রোলের গায়ে জব, কালার, মেশিন নম্বর, তারিখ এবং শিফট অবশ্যই লিখতে হবে।
        </p>
        </div>
    </div>
    <div style="float:right; border:1px solid #000;">
        <table border="1" rules="all" class="rpt_table" width="400" height="150">
            <thead>
                <th colspan="2" style="font-size:18px; font-weight:bold;">Please Mark The Role The Each Role as
                    Follows
                </th>
            <thead>
            <tr>
                <td width="200" style="font-size:14px;"><b> 1.</b> Manufacturing Factory Name</td>
                <td style="font-size:14px;"><b> 6.</b> Fabrics Type</td>
            </tr>
            <tr>
                <td style="font-size:14px;"><b> 2.</b> Prog. Company Name</td>
                <td style="font-size:14px;"><b> 7.</b> Finished Dia</td>
            </tr>
            <tr>
                <td style="font-size:14px;"><b> 3.</b> Buyer, Style,Order no.</td>
                <td style="font-size:14px;"><b> 8.</b> Finished Gsm & Color</td>
            </tr>
            <tr>
                <td style="font-size:14px;"><b> 4.</b> Yarn Count, Lot & Brand</td>
                <td style="font-size:14px;"><b> 9.</b> Yarn Composition</td>
            </tr>
            <tr>
                <td style="font-size:14px;"><b> 5.</b> M/C No., Dia, Stitch Length</td>
                <td style="font-size:14px;"><b> 10.</b> Knit Program No</td>
            </tr>
        </table>
    </div>
    <?
    echo signature_table(213, $company_id, "1180px");
    ?>
    </div>
    <div>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
            function generateBarcode( valuess,id ){
                var value = valuess;//$("#barcodeValue").val();
                // alert(value)
                var btype = 'code39';//$("input[name=btype]:checked").val();
                var renderer ='bmp';// $("input[name=renderer]:checked").val();

                var settings = {
                    output:renderer,
                    bgColor: '#FFFFFF',
                    color: '#000000',
                    barWidth: 2,
                    barHeight: 30,
                    moduleSize:5,
                    posX: 10,
                    posY: 20,
                    addQuietZone: 1
                };
                $("#barcode_img_id").html('11');
                    value = {code:value, rect: false};

                $("#barcode_img_id").barcode(value, btype, settings);
            }
            generateBarcode('<? echo $program_ids; ?>');
            </script>
    </div>
    <?
    exit();
}

if ($action == "requisition_print_two")
{
    extract($_REQUEST);
    $data = explode('**', $data);
    if ($data[2])
    {
        echo load_html_head_contents("Program Qnty Info", "../", 1, 1, '', '', '');
    }
    else
    {
        echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
    }

    $typeForAttention = $data[1];
    $program_ids = $data[0];
    $within_group = $data[3];
    // $company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
    $brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
    $floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
    $exporter_ref_arr = return_library_array("select id, EXPORTERS_REFERENCE from LIB_BUYER", 'id', 'EXPORTERS_REFERENCE');
    $yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by id, yarn_count","id","yarn_count");

    $company_info = sql_select("select ID, COMPANY_NAME, PLOT_NO, ROAD_NO, CITY, CONTACT_NO, COUNTRY_ID from lib_company where status_active=1 and is_deleted=0 order by company_name");
    foreach($company_info as $row)
    {
        $company_details[$row['ID']] = $row['COMPANY_NAME'];
        $company_address_arr[$row['ID']] = 'Plot No:'.$row['PLOT_NO'].', Road No:'.$row['ROAD_NO'].', City / Town:'.$row['CITY'].', Country:'.$country_name_arr[$row['COUNTRY_ID']].', Contact No:'.$row['CONTACT_NO'];
    }
    unset($company_info);

    //for supplier
    $sqlSupplier = sql_select("select id as ID, supplier_name as SUPPLIER_NAME, short_name as SHORT_NAME, ADDRESS_1 from lib_supplier");
    foreach($sqlSupplier as $row)
    {
        $supplier_arr[$row['ID']] = $row['SHORT_NAME'];
        $supplier_dtls_arr[$row['ID']] = $row['SUPPLIER_NAME'];
        $supplier_address_arr[$row['ID']] = $row['ADDRESS_1'];
    }
    unset($sqlSupplier);

    //for sales order information
    $po_dataArray = sql_select("SELECT ID, JOB_NO, BUYER_ID, STYLE_REF_NO, WITHIN_GROUP, SALES_BOOKING_NO, BOOKING_WITHOUT_ORDER FROM FABRIC_SALES_ORDER_MST WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PO_ID FROM PPL_PLANNING_ENTRY_PLAN_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0)");
    foreach ($po_dataArray as $row)
    {
        $sales_array[$row['ID']]['no'] = $row['JOB_NO'];
        $sales_array[$row['ID']]['sales_booking_no'] = $row['SALES_BOOKING_NO'];
        $sales_array[$row['ID']]['buyer_id'] = $row['BUYER_ID'];
        $sales_array[$row['ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
        $sales_array[$row['ID']]['within_group'] = $row['WITHIN_GROUP'];
        $sales_array[$row['ID']]['booking_without_order'] = $row['BOOKING_WITHOUT_ORDER'];
    }

    //for booking information
    $book_dataArray = sql_select("SELECT A.BUYER_ID, B.BOOKING_NO, B.PO_BREAK_DOWN_ID AS PO_ID, B.JOB_NO, C.PO_NUMBER, D.STYLE_REF_NO, C.GROUPING FROM WO_BOOKING_MST A,WO_BOOKING_DTLS B, WO_PO_BREAK_DOWN C,WO_PO_DETAILS_MASTER D WHERE A.BOOKING_NO=B.BOOKING_NO AND B.PO_BREAK_DOWN_ID=C.ID AND C.JOB_NO_MST=D.JOB_NO AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND B.BOOKING_NO IN(SELECT SALES_BOOKING_NO FROM FABRIC_SALES_ORDER_MST WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PO_ID FROM PPL_PLANNING_ENTRY_PLAN_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0))");
    foreach ($book_dataArray as $row)
    {
        $booking_array[$row['BOOKING_NO']]['booking_no'] = $row['BOOKING_NO'];
        $booking_array[$row['BOOKING_NO']]['po_id'] = $row['PO_ID'];
        $booking_array[$row['BOOKING_NO']]['buyer_id'] = $row['BUYER_ID'];
        $booking_array[$row['BOOKING_NO']]['po_no'] = $row['PO_NUMBER'];
        $booking_array[$row['BOOKING_NO']]['job_no'] = $row['JOB_NO'];
        $booking_array[$row['BOOKING_NO']]['style_ref_no'] = $row['STYLE_REF_NO'];
        $booking_array[$row['BOOKING_NO']]['internal_ref'] = $row['GROUPING'];
    }

    //for product information
    $product_details_array = array();
    $sql = "SELECT ID, SUPPLIER_ID, LOT, CURRENT_STOCK, YARN_COMP_TYPE1ST, YARN_COMP_PERCENT1ST, YARN_COMP_TYPE2ND, YARN_COMP_PERCENT2ND, YARN_COUNT_ID, YARN_TYPE, COLOR, BRAND FROM PRODUCT_DETAILS_MASTER WHERE ITEM_CATEGORY_ID=1 AND STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PROD_ID FROM PPL_YARN_REQUISITION_ENTRY WHERE KNIT_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0 GROUP BY PROD_ID)";
    $result = sql_select($sql);
    foreach ($result as $row)
    {
        $compos = '';
        if ($row['YARN_COMP_PERCENT2ND'] != 0)
        {
            $compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
        }
        else
        {
            $compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']];
        }

        $product_details_array[$row['ID']]['desc'] = $count_arr[$row['YARN_COUNT_ID']] . " " . $compos . " " . $yarn_type[$row['YARN_TYPE']];
        $product_details_array[$row['ID']]['lot'] = $row['LOT'];
        $product_details_array[$row['ID']]['brand'] = $brand_arr[$row['BRAND']];
        $product_details_array[$row['ID']]['color'] = $color_library[$row['COLOR']];
    }

    //for requisition information
    $knit_id_array = array();
    $prod_id_array = array();
    $rqsn_array = array();
    $reqsn_dataArray = sql_select("SELECT KNIT_ID, REQUISITION_NO, REQUISITION_DATE, PROD_ID, SUM(NO_OF_CONE) AS NO_OF_CONE, SUM(YARN_QNTY) AS YARN_QNTY FROM PPL_YARN_REQUISITION_ENTRY WHERE KNIT_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0 GROUP BY KNIT_ID, PROD_ID, REQUISITION_NO,REQUISITION_DATE");
    foreach ($reqsn_dataArray as $row)
    {
        $prod_id_array[$row['KNIT_ID']][$row['PROD_ID']] = $row['YARN_QNTY'];
        $knit_id_array[$row['KNIT_ID']] .= $row['PROD_ID'] . ",";
        $rqsn_array[$row['PROD_ID']]['reqsn'] .= $row['REQUISITION_NO'] . ",";
        $rqsn_array[$row['PROD_ID']]['reqsd'] .= $row['REQUISITION_DATE'] . ",";
        $rqsn_array[$row['PROD_ID']]['qnty'] += $row['YARN_QNTY'];
        $rqsn_array[$row['PROD_ID']]['no_of_cone'] += $row['NO_OF_CONE'];

        $requisiontNoArr[$row['KNIT_ID']]['requisition_no'] = $row['REQUISITION_NO'];
    }

    $sales_order_no = '';
    $buyer_name = '';
    $knitting_factory = '';
    $booking_no = '';
    $wg_yes_booking = '';
    $company = '';
    $order_buyer = '';
    $style_ref_no = '';
    $dataArray = sql_select("SELECT A.ID, A.KNITTING_SOURCE, A.KNITTING_PARTY, B.BUYER_ID, B.BOOKING_NO, B.COMPANY_ID, LISTAGG(CAST(B.PO_ID AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY B.PO_ID) AS PO_ID, A.IS_SALES, B.WITHIN_GROUP FROM PPL_PLANNING_INFO_ENTRY_DTLS A, PPL_PLANNING_ENTRY_PLAN_DTLS B WHERE A.ID=B.DTLS_ID AND A.ID IN (".$program_ids.") AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 GROUP BY A.ID, A.KNITTING_SOURCE, A.KNITTING_PARTY, B.BUYER_ID, B.BOOKING_NO, B.COMPANY_ID,A.IS_SALES,B.WITHIN_GROUP");
    $k_source = "";
    $sup = $sales_ids = "";
    foreach ($dataArray as $row)
    {
        if ($duplicate_arr[$row['KNITTING_SOURCE']][$row['KNITTING_PARTY']] == "")
        {
            $duplicate_arr[$row['KNITTING_SOURCE']][$row['KNITTING_PARTY']] = $row['KNITTING_PARTY'];
        }

        $knitting_factory_address = '';
        if ($row['KNITTING_SOURCE'] == 1)
        {
            $knitting_factory .= $company_details[$row['KNITTING_PARTY']] . ",";
            $knitting_factory_address .= $company_address_arr[$row['KNITTING_PARTY']] . ",";
        }
        else if ($row['KNITTING_SOURCE'] == 3)
        {
            $knitting_factory .= $supplier_details[$row['KNITTING_PARTY']] . ",";
            $knitting_factory_address .= $supplier_address_arr[$row['KNITTING_PARTY']] . ",";
        }
        $knitting_factory=implode(",",array_unique(explode(",",$knitting_factory)));
        $knitting_factory_address=implode(",",array_unique(explode(",",$knitting_factory_address)));

        if ($buyer_name == "")
        {
            $buyer_name = $buyer_arr[$row['BUYER_ID']];
        }

        if ($booking_no != '')
        {
            $booking_no .= "," . $row['BOOKING_NO'];
        }
        else
        {
            $booking_no = $row['BOOKING_NO'];
        }

        if ($company == "")
        {
            $company = $company_details[$row['COMPANY_ID']];
        }

        if ($company_id == "")
        {
            $company_id = $row['COMPANY_ID'];
        }
        $order_nos .= "," . $booking_array2[$row['BOOKING_NO']]['po_no'];
        $is_sales = $row['IS_SALES'];
        $sales_ids .= $row['PO_ID'] . ",";
        $k_source = $row['KNITTING_SOURCE'];
        $sup = $row['KNITTING_PARTY'];
    }
    $sales_id = array_unique(explode(",", $sales_ids));
    $booking_nos = array_unique(explode(",", $booking_no));

    $order_buyer=$style_ref_no=$job_no=$order_nos="";
    foreach ($sales_id as $pid)
    {
        $sales_order_no .= $sales_array[$pid]['no'] . ",";
        if ($sales_array[$pid]['within_group'] == 2)
        {
            if($dataArray[0]['KNITTING_SOURCE'] == 3)
            {
                $order_buyer .= $exporter_ref_arr[$sales_array[$pid]['buyer_id']] . ",";
            }
            else
            {
                $order_buyer .= $buyer_arr[$sales_array[$pid]['buyer_id']] . ",";
            }

            $style_ref_no .= "," . $sales_array[$pid]['style_ref_no'];
            $job_no .= "";
            $order_ids .= "";
            $internal_ref .= "";
        }
        else
        {
            if($sales_array[$pid]['booking_without_order'] != 1)
            {
                if($dataArray[0]['KNITTING_SOURCE'] == 3)
                {
                    $order_buyer .= $exporter_ref_arr[$booking_array[$sales_array[$pid]['sales_booking_no']]['buyer_id']] . ",";
                }
                else
                {
                    $order_buyer .= $buyer_arr[$booking_array[$sales_array[$pid]['sales_booking_no']]['buyer_id']] . ",";
                }

            }
            else
            {
                //for sample without order
                $booking_buyer = return_field_value("buyer_id", "wo_non_ord_samp_booking_mst", "booking_no='".$sales_array[$pid]['sales_booking_no']."'");
                if($dataArray[0]['KNITTING_SOURCE'] == 3)
                {
                    $order_buyer .= $exporter_ref_arr[$booking_buyer].",";
                }
                else
                {
                    $order_buyer .= $buyer_arr[$booking_buyer].",";
                }

            }
            if($sales_array[$pid]['booking_without_order'] != 1)
            {
                $style_ref_no .= "," . $booking_array[$sales_array[$pid]['sales_booking_no']]['style_ref_no'];
            }
            else
            {
                $style_ref_no .= "," . $sales_array[$pid]['style_ref_no'];
            }


            $job_no .= $booking_array[$sales_array[$pid]['sales_booking_no']]['job_no'] . ",";
            $order_ids .= $booking_array[$sales_array[$pid]['sales_booking_no']]['po_no'] . ",";
            $internal_ref .= $booking_array[$sales_array[$pid]['sales_booking_no']]['internal_ref'] . ",";
        }
    }

    $sales_nos = rtrim(implode(",", array_unique(explode(",", $sales_order_no))), ",");
    $order_buyers = rtrim(implode(",", array_unique(explode(",", $order_buyer))), ",");
    $style_ref_nos = ltrim(implode(",", array_unique(explode(",", $style_ref_no))), ",");
    $job_nos = implode(",", array_unique(explode(",", rtrim($job_no,","))));
    $booking_noss = implode(",", $booking_nos);
    if($program_ids!="")
    {
        $feedingResult =  sql_select("SELECT DTLS_ID, SEQ_NO, COUNT_ID, FEEDING_ID FROM PPL_PLANNING_COUNT_FEED_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0");

        $feedingDataArr = array();
        foreach ($feedingResult as $row)
        {
            $feedingSequence[$row['SEQ_NO']] = $row['SEQ_NO'];
            $feedingDataArr[$row['DTLS_ID']][$row['SEQ_NO']]['count_id'] = $row['COUNT_ID'];
            $feedingDataArr[$row['DTLS_ID']][$row['SEQ_NO']]['feeding_id'] = $row['FEEDING_ID'];
        }
    }
    ?>
    <div style="width:1250px; margin-left:5px;">
        <table width="100%" style="margin-top:10px">
            <tr>
                <td width="100%" align="center" style="font-size:20px;"><b><? echo $company; ?></b></td>
            </tr>
            <tr>
                <td align="center" style="font-size:14px">
                 <?
                 echo show_company($company_id, '', '');
                 ?>
             </td>
         </tr>
         <tr>
            <td width="100%" align="center" style="font-size:20px;"><b><u>Knitting program / Yarn Store Requisition</u></b></td>
        </tr>
    </table>
    <div style="margin-top:10px; width:950px">
        <table width="100%" cellpadding="2" cellspacing="5">
            <tr>
                <td width="140"><b style="font-size:18px">Knitting Factory </b></td>
                <td>:</td>
                <td style="font-size:18px"><b><? echo substr($knitting_factory, 0, -1); ?></b></td>
            </tr>
            <tr>
                <td width="140"><b style="font-size:18px">Address</b></td>
                <td>:</td>
                <td style="font-size:18px"><? echo $knitting_factory_address; ?></td>
            </tr>
            <tr>
                <td width="140" style="font-size:18px"><b>Attention </b></td>
                <td>:</td>
                <?
                if ($typeForAttention == 1)
                {
                  echo "<td style=\"font-size:18px; font-weight:bold;\"> Knitting Manager </td>";
                }
                else
                {
                  ?>
                  <td style="font-size:18px; font-weight:bold;"><b><?
                    if ($k_source == 3)
                    {
                     $ComArray = sql_select("select id,contact_person from lib_supplier where id=$sup");
                     foreach ($ComArray as $row)
                     {
                      echo $row[csf('contact_person')];
                    }
              } else {

                 echo "";
             }
             ?></b></td>
             <? } ?>
         </tr>
         <tr>
            <td><b>Buyer Name </b></td>
            <td>:</td>
            <td><? echo $order_buyers; ?></td>
        </tr>
        <tr>
            <td><b>Style </b></td>
            <td>:</td>
            <td><? echo $style_ref_nos; ?></td>
        </tr>
        <tr>
            <td><b>Order No </b></td>
            <td>:</td>
            <td><? echo rtrim($order_ids,","); ?></td>
        </tr>
        <tr>
            <td><b>Internal Ref. </b></td>
            <td>:</td>
            <td><?
            if($dataArray[0]['KNITTING_SOURCE'] == 3)
            {
                echo "1";
            }
            echo rtrim($internal_ref,","); ?></td>
        </tr>
        <tr>
            <td><b>Job No </b></td>
            <td>:</td>
            <td><? echo $job_nos; ?></td>
        </tr>
        <tr>
            <td><b>Booking No </b></td>
            <td>:</td>
            <td><? echo $booking_noss; ?></td>
        </tr>
        <tr>
            <td><b>Sales Order No </b></td>
            <td>:</td>
            <td><? echo $sales_nos; ?></td>
        </tr>
    </table>
    </div>
    <table width="1050" style="margin-top:10px" border="1" rules="all" cellpadding="0" cellspacing="0"
    class="rpt_table">
    <thead>
        <th width="30">SL</th>
        <th width="100">Requisition No</th>
        <th width="100">Requisition Date</th>
        <th width="100">Brand</th>
        <th width="100">Lot No</th>
        <th width="200">Yarn Description</th>
        <th width="100">Color</th>
        <th width="100">Requisition Qty.</th>
        <th>No Of Cone</th>
    </thead>
    <?
    $j = 1;
    $tot_reqsn_qty = 0;
    foreach ($rqsn_array as $prod_id => $data) {
        if ($j % 2 == 0)
        $bgcolor = "#E9F3FF";
    else
        $bgcolor = "#FFFFFF";
    ?>
    <tr bgcolor="<? echo $bgcolor; ?>">
        <td width="30"><? echo $j; ?></td>
        <td width="100"><? echo substr($data['reqsn'], 0, -1); ?></td>
        <td width="100"><? echo substr($data['reqsd'], 0, -1); ?></td>
        <td width="100"><p><? echo $product_details_array[$prod_id]['brand']; ?>&nbsp;</p></td>
        <td width="100"><p><? echo $product_details_array[$prod_id]['lot']; ?></p></td>
        <td width="200"><p><? echo $product_details_array[$prod_id]['desc']; ?></p>
        </th>
        <td width="100"><p><? echo $product_details_array[$prod_id]['color']; ?>&nbsp;</p></td>
        <td width="100" align="right"><p><? echo number_format($data['qnty'], 2, '.', ''); ?></p></td>
        <td align="right"><? echo number_format($data['no_of_cone']); ?></td>
    </tr>
    <?
    $tot_reqsn_qty += $data['qnty'];
    $tot_no_of_cone += $data['no_of_cone'];
    $j++;
}
?>
<tfoot>
    <th colspan="7" align="right">Total</th>
    <th align="right"><? echo number_format($tot_reqsn_qty, 2, '.', ''); ?></th>
    <th><? echo number_format($tot_no_of_cone); ?></th>
</tfoot>
</table>

<table style="margin-top:10px;" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0"
class="rpt_table" align="center">
<thead align="center">
    <th width="25">SL</th>
    <th width="100">Program Date</th>
    <th width="50">Program No</th>
    <th width="50">Requisition No</th>
    <th width="120">Batch No</th>
    <th width="120">Tube/Ref. No</th>
    <th width="120">Fabrication</th>
    <th width="50">GSM</th>
    <th width="40">F. Dia</th>
    <th width="60">Dia Type</th>

    <th width="45">Floor</th>

    <th width="45">M/c. No</th>
    <th width="50">M/c. Dia & GG</th>
    <th width="100">Color</th>
    <th width="60">Color Range</th>
    <th width="50">S/L</th>
    <th width="50">Spandex S/L</th>
    <th width="50">Feeder</th>
    <th width="100">Count Feeding</th>
    <th width="70">Knit Start</th>
    <th width="70">Knit End</th>
    <th width="70">Prpgram Qty.</th>
    <th width="110">Yarn Description</th>
    <th width="50">Lot</th>
    <th width="70">Yarn Qty.(KG)</th>
    <th>Remarks</th>

</thead>
<?
$i = 1;
$s = 1;
$tot_program_qnty = 0;
$tot_yarn_reqsn_qnty = 0;
//$company_id = '';
$sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks,b.advice,b.batch_no,b.tube_ref_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
//echo $sql;
$nameArray = sql_select($sql);
$advice = "";
foreach ($nameArray as $row)
{
    if ($i % 2 == 0)
     $bgcolor = "#E9F3FF";
    else
     $bgcolor = "#FFFFFF";

    $color = '';
    $color_id = explode(",", $row[csf('color_id')]);

    foreach ($color_id as $val)
    {
        if ($color == '')
          $color = $color_library[$val];
        else
          $color .= "," . $color_library[$val];
    }

    if ($company_id == '') $company_id = $row[csf('company_id')];

    $machine_no = '';
    $machine_id = explode(",", $row[csf('machine_id')]);

    foreach ($machine_id as $val)
    {
        if ($machine_no == '')
            $machine_no = $machine_arr[$val];
        else
            $machine_no .= "," . $machine_arr[$val];
    }
    if ($machine_id[0] != "")
    {
     $sql_floor = sql_select("select id, machine_no, floor_id from lib_machine_name where id=$machine_id[0] and status_active=1 and is_deleted=0  order by seq_no");
    }

    $count_feeding = "";
    foreach($feedingDataArr[$row[csf('program_id')]] as $feedingSequence=>$feedingData)
    {
        if($count_feeding =="")
        {
            $count_feeding = $feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
        }
        else
        {
            $count_feeding .= ",".$feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
        }
    }

    if ($knit_id_array[$row[csf('program_id')]] != "") {
    $all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
    $row_span = count($all_prod_id);
    $z = 0;

 foreach ($all_prod_id as $prod_id)
 {
    ?>
    <tr bgcolor="<? echo $bgcolor; ?>">
        <?
        if ($z == 0)
        {
            ?>
            <td width="25" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
            <td width="100" rowspan="<? echo $row_span; ?>" align="center" style="font-size:16px;">
            <? echo change_date_format($row[csf('program_date')]); ?></td>
            <td width="60" rowspan="<? echo $row_span; ?>" align="center" style="font-size:16px;">
            <? echo $row[csf('program_id')]; ?></td>
            <td width="60" rowspan="<? echo $row_span; ?>" align="center" style="font-size:16px;">
            <? echo $requisiontNoArr[$row[csf('program_id')]]['requisition_no']; ?></td>
            <td width="120" rowspan="<? echo $row_span; ?>" align="center">
            <? echo $row[csf('batch_no')]; ?></td>
            <td width="120" rowspan="<? echo $row_span; ?>" align="center">
            <? echo $row[csf('tube_ref_no')]; ?></td>
            <td width="120" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
            <td width="50" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('gsm_weight')]; ?></p></th>
            <td width="50" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
            <td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>
            <td width="60" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>
            <td width="60" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $machine_no; ?></p></td>
            <td width="70" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
            <td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color; ?></p></td>
            <td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
            <td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('stitch_length')]; ?></p></td>
            <td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
            <td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
            <td width="100" rowspan="<? echo $row_span; ?>"><p><? echo $count_feeding; ?></p></td>
            <td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
            <td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
            <td width="70" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?></td>
            <?
            $tot_program_qnty += $row[csf('program_qnty')];
                $i++;
        }
        ?>
        <td width="110"><p><? echo $product_details_array[$prod_id]['desc']; ?>&nbsp;</p></td>
        <td width="50" align="center"><p><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</p></td>
        <td width="70" align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id], 2, '.', ''); ?></td>
        <?
        if ($z == 0) {
            ?>
            <td rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
            <?
        }
        ?>
    </tr>
        <?
        $tot_yarn_reqsn_qnty += $prod_id_array[$row[csf('program_id')]][$prod_id];
        $z++;
    }
}
else
{
 ?>
 <tr bgcolor="<? echo $bgcolor; ?>">
    <td width="25"><? echo $i; ?></td>
    <td width="100" align="center" style="font-size:16px;"><? echo change_date_format($row[csf('program_date')]); ?></td>
    <td width="60" align="center" style="font-size:16px;"><? echo $row[csf('program_id')]; ?></td>
    <td width="60" align="center" style="font-size:16px;"><? echo  $requisiontNoArr[$row[csf('program_id')]]['requisition_no'] ; ?></td>
    <td width="120" align="center"><? echo $row[csf('batch_no')]; ?></td>
    <td width="120" align="center"><? echo $row[csf('tube_ref_no')]; ?></td>
    <td width="120"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
    <td width="50" align="center"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
    <td width="50" align="center"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
    <td width="60"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>
    <td width="50" align="center"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>
    <td width="50" align="center"><p><? echo $machine_no; ?></p></td>
    <td width="50"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
    <td width="50"><p><? echo $color; ?></p></td>
    <td width="60"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
    <td width="60"><p><? echo $row_span; ?><p><? echo $row[csf('stitch_length')]; ?></p></td>
    <td width="60"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
    <td width="70"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
    <td width="100" rowspan="<? echo $row_span; ?>"><p><? echo $count_feeding; ?></p></td>
    <td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
    <td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
    <td width="70" align="right"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?></td>
    <td width="110"><p>&nbsp;</p></td>
    <td width="50"><p>&nbsp;</p></td>
    <td width="70" align="right">&nbsp;</td>
    <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
</tr>
<?
$tot_program_qnty += $row[csf('program_qnty')];
$i++;
}
$advice = $row[csf('advice')];
}
?>
<tfoot>
    <th colspan="21" align="right"><b>Total</b></th>
    <th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?>&nbsp;</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>

    <th align="right"><? echo number_format($tot_yarn_reqsn_qnty, 2, '.', ''); ?></th>
    <th>&nbsp;</th>

</tfoot>

</table>
<br>
<?


$sql_collarCuff = sql_select("select id, body_part_id, grey_size, finish_size, qty_pcs, dtls_id from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id in($program_ids) order by id");

$progWiseDataArr = array();
foreach($sql_collarCuff as $row)
{
    $progWiseDataArr[$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('grey_size')]][$row[csf('finish_size')]][$row[csf('qty_pcs')]]['dtls_id']=$row[csf('dtls_id')];
    $progWiseDataArr[$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('grey_size')]][$row[csf('finish_size')]][$row[csf('qty_pcs')]]['body_part_id']=$row[csf('body_part_id')];
    $progWiseDataArr[$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('grey_size')]][$row[csf('finish_size')]][$row[csf('qty_pcs')]]['grey_size']=$row[csf('grey_size')];
    $progWiseDataArr[$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('grey_size')]][$row[csf('finish_size')]][$row[csf('qty_pcs')]]['finish_size']=$row[csf('finish_size')];
    $progWiseDataArr[$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('grey_size')]][$row[csf('finish_size')]][$row[csf('qty_pcs')]]['qty_pcs']=$row[csf('qty_pcs')];
}
unset($sql_collarCuff);
// echo "<pre>";
// print_r($progWiseDataArr);
// echo "</pre>";

if (count($progWiseDataArr) > 0) {
   ?>
   <table style="margin-top:10px;" width="730" border="1" rules="all" cellpadding="0" cellspacing="0"
   class="rpt_table">
   <thead>
    <tr>
        <th width="50">SL</th>
        <th width="200">Body Part</th>
        <th width="100">Prog No</th>
        <th width="100">Color</th>
        <th width="100">Grey Size</th>
        <th width="100">Finish Size</th>
        <th>Quantity Pcs</th>
    </tr>
</thead>
<tbody>
    <?
    foreach ($progWiseDataArr as $prog_id=>$prog_data)
    {
        foreach($prog_data as $b_part_id=>$b_part_data)
        {
            foreach($b_part_data as $g_size=>$g_size_data)
            {
                foreach($g_size_data as $f_size=>$f_size_data)
                {
                    foreach($f_size_data as $row=>$val)
                    {
                        $prog_count[$prog_id]++;
                    }
                }
            }
        }
    }
    $i = 1;
    $total_qty_pcs = 0;
    foreach ($progWiseDataArr as $prog_id=>$prog_data)
    {
        foreach($prog_data as $b_part_id=>$b_part_data)
        {
            foreach($b_part_data as $g_size=>$g_size_data)
            {
                foreach($g_size_data as $f_size=>$f_size_data)
                {
                    foreach($f_size_data as $row=>$val)
                    {

                        $prog_span = $prog_count[$prog_id];
                        // echo "<pre>";
                        // print_r($val);
                        // echo "</pre>";
                        if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                        ?>
                        <tr>

                            <?
                                  if(!in_array($prog_id,$prog_chk))
                                  {
                                    $prog_chk[]=$prog_id;
                                    ?>
                                    <td align="center" rowspan="<? echo $prog_span ;?>" valign='middle' style="text-align:center"><p><? echo $i; ?>&nbsp;</p></td>
                                    <td rowspan="<? echo $prog_span ;?>" valign='middle' style="text-align:center"><p><? echo $body_part[$val['body_part_id']]; ?>&nbsp;</p></td>
                                    <td title="<? echo $prog_id;?>" rowspan="<? echo $prog_span ;?>" valign='middle' style="text-align:center"><p><? echo $prog_id; ?>&nbsp;</p></td>
                                    <td rowspan="<? echo $prog_span ;?>" valign='middle' style="text-align:center"><p><? echo $color_library[$stripeWiseColorArr[$prog_id]['color_number_id']]; ?>&nbsp;</p></td>
                                    <?
                                  }
                            ?>

                            <td style="padding-left:5px"><p><? echo $val['grey_size']; ?>&nbsp;</p></td>
                            <td style="padding-left:5px"><p><? echo $val['finish_size']; ?>&nbsp;</p></td>
                            <td align="right"><p><? echo number_format($val['qty_pcs'], 0);
                                $total_qty_pcs += $val['qty_pcs']; ?>&nbsp;&nbsp;</p></td>
                        </tr>
                        <?

                    }
                }
            }
        }
        $i++;
    }
    ?>
</tbody>
<tfoot>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th align="right">Total</th>
        <th align="right"><? echo number_format($total_qty_pcs, 0); ?>&nbsp;</th>
    </tr>
</tfoot>
</table>
<?
}
?>
<br>
<?
$sql_fedder = sql_select("select a.id, a.color_id, a.stripe_color_id, a.no_of_feeder, max(b.measurement) as measurement, max(b.uom) as uom,a.dtls_id from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.dtls_id in($program_ids) and b.job_no in('".$sales_nos."') and a.no_of_feeder>0 group by a.id, a.color_id, a.stripe_color_id, a.no_of_feeder,a.dtls_id order by a.id,a.dtls_id");

$stripeDataArr = array();
foreach($sql_fedder as $row)
{
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['dtls_id']=$row[csf('dtls_id')];
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['color_id']=$row[csf('color_id')];
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['stripe_color_id']=$row[csf('stripe_color_id')];
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['measurement']=$row[csf('measurement')];
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['uom']=$row[csf('uom')];
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['no_of_feeder']=$row[csf('no_of_feeder')];


}
foreach ($stripeDataArr as $prog_ids=>$prog_data)
{
    foreach($prog_data as $c_number_id=>$c_number_data)
    {
        foreach($c_number_data as $stripe_color_id=>$stripe_color_data)
        {
            foreach($stripe_color_data as $m_id=>$m_data)
            {
                foreach($m_data as $u_id=>$u_data)
                {
                    foreach($u_data as $row=>$val)
                    {
                        $s_prog_count[$prog_ids]++;
                    }
                }
            }
        }
    }
}
/* echo "<pre>";
print_r($s_prog_count);
echo "</pre>"; */

if (count($sql_fedder) > 0) {
    ?>
    <table style="margin-top:10px;" width="650" border="1" rules="all" cellpadding="0" cellspacing="0"
    class="rpt_table">
    <thead>
        <tr>
            <th width="50">SL</th>
            <th width="100">Program No</th>
            <th width="120">Color</th>
            <th width="120">Stripe Color</th>
            <th width="100">Measurement</th>
            <th width="100">UOM</th>
            <th>No Of Feeder</th>
        </tr>
    </thead>
    <tbody>
        <?
        $i = 1;
        $total_feeder = 0;
        foreach ($sql_fedder as $row) {
            if ($i % 2 == 0)
                $bgcolor = "#E9F3FF";
            else
                $bgcolor = "#FFFFFF";
            ?>
            <tr>

            <?
                $prog_ids=$row[csf('dtls_id')];
                $s_prog_span = $s_prog_count[$prog_ids];
                /*if(!in_array($prog_ids,$prog_chks))
                {
                    $prog_chks[]=$prog_ids;
                    ?>
                    <td align="center" rowspan="<? echo $s_prog_span;?>" valign='middle' style="text-align:center"><p><? echo $i; ?>&nbsp;</p></td>
                    <td align="center" rowspan="<? echo $s_prog_span;?>" valign='middle' style="text-align:center"><p><? echo $row[csf('dtls_id')]; ?>&nbsp;</p></td>
                    <td align="center" rowspan="<? echo $s_prog_span;?>" valign='middle' style="text-align:center"><p><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</p></td>
                    <?
                }*/
                ?>
            <!--    <td align="center"><p><? //echo $color_library[$row[csf('stripe_color_id')]]; ?>&nbsp;</p></td>
                <td align="center"><p><? //echo number_format($row[csf('measurement')], 2); ?>&nbsp;</p></td>
                <td align="center"><p><? //echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
                <td align="center"><p><? //echo number_format($row[csf('no_of_feeder')], 0);
                //$total_feeder += $row[csf('no_of_feeder')]; ?>&nbsp;</p></td>  -->

                <td align="center" style="text-align:center"><p><? echo $i; ?>&nbsp;</p></td>
                <td align="center" style="text-align:center"><p><? echo $row[csf('dtls_id')]; ?>&nbsp;</p></td>
                <td align="center" style="text-align:center"><p><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo $color_library[$row[csf('stripe_color_id')]]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo number_format($row[csf('measurement')], 2); ?>&nbsp;</p></td>
                <td align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo number_format($row[csf('no_of_feeder')], 0);
                $total_feeder += $row[csf('no_of_feeder')]; ?>&nbsp;</p></td>
            </tr>
            <?
            $i++;
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th align="right">Total:</th>
            <th style="text-align: center;"><? echo number_format($total_feeder, 0); ?></th>
        </tr>
    </tfoot>
</table>
<?
}

?>
<table border="1" rules="all" class="rpt_table">
    <tr>
        <td style="font-size:24px; font-weight:bold; width:20px;">ADVICE:</td>
        <td style="font-size:20px; width:100%;">     <? echo $advice; ?></td>
    </tr>
</table>
<!-- <div style="margin-top:60px; text-align: left;"><strong>Rate/Kg =</strong></div> -->
<br/>
<div style="float:left; width: 401px;">
    <div style="border:1px solid #000;">
    <table border="1" rules="all" class="rpt_table" width="400" height="200">
        <thead>
            <th colspan="2" style="font-size:20px; font-weight:bold;">Please Strictly Avoid The Following Faults….</th>
        <thead>
        <tbody>
            <tr>
                <td style="width:190px; font-size:14px;"><b> 1.</b> Patta</td>
                <td style="font-size:14px;"><b> 8.</b> Sinker mark</td>
            </tr>
            <tr>
                <td style="font-size:14px;"><b> 2.</b> Loop</td>
                <td style="font-size:14px;"><b> 9.</b> Needle mark</td>
            </tr>
            <tr>
                <td style="font-size:14px;"><b> 3.</b> Hole</td>
                <td style="font-size:14px;"><b> 10.</b> Oil mark</td>
            </tr>
            <tr>
                <td><b> 4.</b> Star marks</td>
                <td><b> 11.</b> Dia mark/Crease Mark</td>
            </tr>
            <tr>
                <td style="font-size:14px;"><b> 5.</b> Barre</td>
                <td style="font-size:14px;"><b> 12.</b> Wheel Free</td>
            </tr>
            <tr>
                <td style="font-size:14px;"><b> 6.</b> Drop Stitch</td>
                <td style="font-size:14px;"><b> 13.</b> Slub</td>
            </tr>
            <tr>
                <td style="font-size:14px;"><b> 7.</b> Lot mixing</td>
                <td style="font-size:14px;"><b> 14.</b> Other contamination</td>
            </tr>
        </tbody>
    </table>
    </div>
    <br>
    <div>
    <p>বিঃ দ্রঃ<br>
    * প্রোগ্রামের শেষ পর্যায়ে অবশ্যই সূতা মিল করে চালাতে হবে।<br>
    * সূতা মেশিনে উঠানোর সময় ১/১ করে উঠতে হবে।<br>
    * রোল মার্কিং সঠিকভাবে করতে হবে।<br>
    * রোলে কোন প্রকার কাটা-কাটি বা ভুল লেখা চলবেনা।<br>
    * রোলের গায়ে জব, কালার, মেশিন নম্বর, তারিখ এবং শিফট অবশ্যই লিখতে হবে।
    </p>
    </div>
</div>
<div style="float:right; border:1px solid #000;">
    <table border="1" rules="all" class="rpt_table" width="400" height="150">
        <thead>
            <th colspan="2" style="font-size:18px; font-weight:bold;">Please Mark The Role The Each Role as
                Follows
            </th>
        <thead>
        <tr>
            <td width="200" style="font-size:14px;"><b> 1.</b> Manufacturing Factory Name</td>
            <td style="font-size:14px;"><b> 6.</b> Fabrics Type</td>
        </tr>
        <tr>
            <td style="font-size:14px;"><b> 2.</b> Prog. Company Name</td>
            <td style="font-size:14px;"><b> 7.</b> Finished Dia</td>
        </tr>
        <tr>
            <td style="font-size:14px;"><b> 3.</b> Buyer, Style,Order no.</td>
            <td style="font-size:14px;"><b> 8.</b> Finished Gsm & Color</td>
        </tr>
        <tr>
            <td style="font-size:14px;"><b> 4.</b> Yarn Count, Lot & Brand</td>
            <td style="font-size:14px;"><b> 9.</b> Yarn Composition</td>
        </tr>
        <tr>
            <td style="font-size:14px;"><b> 5.</b> M/C No., Dia, Stitch Length</td>
            <td style="font-size:14px;"><b> 10.</b> Knit Program No</td>
        </tr>
    </table>
</div>
<?
echo signature_table(213, $company_id, "1180px",1);
?>
</div>
<?
exit();
}

if ($action == "requisition_print_three")
{
    extract($_REQUEST);
    $data = explode('**', $data);
    if ($data[2])
    {
        echo load_html_head_contents("Program Qnty Info", "../", 1, 1, '', '', '');
    }
    else
    {
        echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
    }

    $typeForAttention = $data[1];
    $program_ids = $data[0];
    $within_group = $data[3];
    // $company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
    $brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
    $floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
    $yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by id, yarn_count","id","yarn_count");
    $user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );

    $company_info = sql_select("select ID, COMPANY_NAME, PLOT_NO, ROAD_NO, CITY, CONTACT_NO, COUNTRY_ID from lib_company where status_active=1 and is_deleted=0 order by company_name");
    foreach($company_info as $row)
    {
        $company_details[$row['ID']] = $row['COMPANY_NAME'];
        $company_address_arr[$row['ID']] = 'Plot No:'.$row['PLOT_NO'].', Road No:'.$row['ROAD_NO'].', City / Town:'.$row['CITY'].', Country:'.$country_name_arr[$row['COUNTRY_ID']].', Contact No:'.$row['CONTACT_NO'];
    }
    unset($company_info);

    //for supplier
    $sqlSupplier = sql_select("select id as ID, supplier_name as SUPPLIER_NAME, short_name as SHORT_NAME, ADDRESS_1 from lib_supplier");
    foreach($sqlSupplier as $row)
    {
        $supplier_arr[$row['ID']] = $row['SHORT_NAME'];
        $supplier_dtls_arr[$row['ID']] = $row['SUPPLIER_NAME'];
        $supplier_address_arr[$row['ID']] = $row['ADDRESS_1'];
    }
    unset($sqlSupplier);

    //for sales order information
    $po_dataArray = sql_select("SELECT ID, JOB_NO, BUYER_ID, STYLE_REF_NO, WITHIN_GROUP, SALES_BOOKING_NO, BOOKING_WITHOUT_ORDER FROM FABRIC_SALES_ORDER_MST WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PO_ID FROM PPL_PLANNING_ENTRY_PLAN_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0)");
    foreach ($po_dataArray as $row)
    {
        $sales_array[$row['ID']]['no'] = $row['JOB_NO'];
        $sales_array[$row['ID']]['sales_booking_no'] = $row['SALES_BOOKING_NO'];
        $sales_array[$row['ID']]['buyer_id'] = $row['BUYER_ID'];
        $sales_array[$row['ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
        $sales_array[$row['ID']]['within_group'] = $row['WITHIN_GROUP'];
        $sales_array[$row['ID']]['booking_without_order'] = $row['BOOKING_WITHOUT_ORDER'];
    }

    //for booking information
    $book_dataArray = sql_select("SELECT A.BUYER_ID, B.BOOKING_NO, B.PO_BREAK_DOWN_ID AS PO_ID, B.JOB_NO, C.PO_NUMBER, D.STYLE_REF_NO, C.GROUPING FROM WO_BOOKING_MST A,WO_BOOKING_DTLS B, WO_PO_BREAK_DOWN C,WO_PO_DETAILS_MASTER D WHERE A.BOOKING_NO=B.BOOKING_NO AND B.PO_BREAK_DOWN_ID=C.ID AND C.JOB_NO_MST=D.JOB_NO AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND B.BOOKING_NO IN(SELECT SALES_BOOKING_NO FROM FABRIC_SALES_ORDER_MST WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PO_ID FROM PPL_PLANNING_ENTRY_PLAN_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0))");
    foreach ($book_dataArray as $row)
    {
        $booking_array[$row['BOOKING_NO']]['booking_no'] = $row['BOOKING_NO'];
        $booking_array[$row['BOOKING_NO']]['po_id'] = $row['PO_ID'];
        $booking_array[$row['BOOKING_NO']]['buyer_id'] = $row['BUYER_ID'];
        $booking_array[$row['BOOKING_NO']]['po_no'] = $row['PO_NUMBER'];
        $booking_array[$row['BOOKING_NO']]['job_no'] = $row['JOB_NO'];
        $booking_array[$row['BOOKING_NO']]['style_ref_no'] = $row['STYLE_REF_NO'];
        $booking_array[$row['BOOKING_NO']]['internal_ref'] = $row['GROUPING'];
    }

    //for product information
    $product_details_array = array();
    $sql = "SELECT ID, SUPPLIER_ID, LOT, CURRENT_STOCK, YARN_COMP_TYPE1ST, YARN_COMP_PERCENT1ST, YARN_COMP_TYPE2ND, YARN_COMP_PERCENT2ND, YARN_COUNT_ID, YARN_TYPE, COLOR, BRAND FROM PRODUCT_DETAILS_MASTER WHERE ITEM_CATEGORY_ID=1 AND STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PROD_ID FROM PPL_YARN_REQUISITION_ENTRY WHERE KNIT_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0 GROUP BY PROD_ID)";
    $result = sql_select($sql);
    foreach ($result as $row)
    {
        $compos = '';
        if ($row['YARN_COMP_PERCENT2ND'] != 0)
        {
            $compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
        }
        else
        {
            $compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']];
        }

        $product_details_array[$row['ID']]['desc'] = $count_arr[$row['YARN_COUNT_ID']] . " " . $compos . " " . $yarn_type[$row['YARN_TYPE']];
        $product_details_array[$row['ID']]['lot'] = $row['LOT'];
        $product_details_array[$row['ID']]['brand'] = $brand_arr[$row['BRAND']];
        $product_details_array[$row['ID']]['color'] = $color_library[$row['COLOR']];
    }

    //for requisition information
    $knit_id_array = array();
    $prod_id_array = array();
    $rqsn_array = array();
    $reqsn_dataArray = sql_select("SELECT KNIT_ID, REQUISITION_NO, REQUISITION_DATE, PROD_ID, SUM(NO_OF_CONE) AS NO_OF_CONE, SUM(YARN_QNTY) AS YARN_QNTY FROM PPL_YARN_REQUISITION_ENTRY WHERE KNIT_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0 GROUP BY KNIT_ID, PROD_ID, REQUISITION_NO,REQUISITION_DATE");
    foreach ($reqsn_dataArray as $row)
    {
        $prod_id_array[$row['KNIT_ID']][$row['PROD_ID']] = $row['YARN_QNTY'];
        $knit_id_array[$row['KNIT_ID']] .= $row['PROD_ID'] . ",";
        $rqsn_array[$row['PROD_ID']]['reqsn'] .= $row['REQUISITION_NO'] . ",";
        $rqsn_array[$row['PROD_ID']]['reqsd'] .= change_date_format($row['REQUISITION_DATE']) . ",";
        $rqsn_array[$row['PROD_ID']]['qnty'] += $row['YARN_QNTY'];
        $rqsn_array[$row['PROD_ID']]['no_of_cone'] += $row['NO_OF_CONE'];

        $requisiontNoArr[$row['KNIT_ID']]['requisition_no'] = $row['REQUISITION_NO'];
    }

    $sales_order_no = '';
    $buyer_name = '';
    $knitting_factory = '';
    $booking_no = '';
    $wg_yes_booking = '';
    $company = '';
    $order_buyer = '';
    $style_ref_no = '';
    $dataArray = sql_select("SELECT A.ID, A.KNITTING_SOURCE, A.KNITTING_PARTY, B.BUYER_ID, B.BOOKING_NO, B.COMPANY_ID, LISTAGG(CAST(B.PO_ID AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY B.PO_ID) AS PO_ID, A.IS_SALES, B.WITHIN_GROUP FROM PPL_PLANNING_INFO_ENTRY_DTLS A, PPL_PLANNING_ENTRY_PLAN_DTLS B WHERE A.ID=B.DTLS_ID AND A.ID IN (".$program_ids.") AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 GROUP BY A.ID, A.KNITTING_SOURCE, A.KNITTING_PARTY, B.BUYER_ID, B.BOOKING_NO, B.COMPANY_ID,A.IS_SALES,B.WITHIN_GROUP");
    $k_source = "";
    $sup = $sales_ids = "";
    foreach ($dataArray as $row)
    {
        if ($duplicate_arr[$row['KNITTING_SOURCE']][$row['KNITTING_PARTY']] == "")
        {
            $duplicate_arr[$row['KNITTING_SOURCE']][$row['KNITTING_PARTY']] = $row['KNITTING_PARTY'];
        }

        $knitting_factory_address = '';
        if ($row['KNITTING_SOURCE'] == 1)
        {
            $knitting_factory .= $company_details[$row['KNITTING_PARTY']] . ",";
            $knitting_factory_address .= $company_address_arr[$row['KNITTING_PARTY']] . ",";
        }
        else if ($row['KNITTING_SOURCE'] == 3)
        {
            $knitting_factory .= $supplier_details[$row['KNITTING_PARTY']] . ",";
            $knitting_factory_address .= $supplier_address_arr[$row['KNITTING_PARTY']] . ",";
        }
        $knitting_factory=implode(",",array_unique(explode(",",$knitting_factory)));
        $knitting_factory_address=implode(",",array_unique(explode(",",$knitting_factory_address)));

        if ($buyer_name == "")
        {
            $buyer_name = $buyer_arr[$row['BUYER_ID']];
        }

        if ($booking_no != '')
        {
            $booking_no .= "," . $row['BOOKING_NO'];
        }
        else
        {
            $booking_no = $row['BOOKING_NO'];
        }

        if ($company == "")
        {
            $company = $company_details[$row['COMPANY_ID']];
        }

        if ($company_id == "")
        {
            $company_id = $row['COMPANY_ID'];
        }
        $order_nos .= "," . $booking_array2[$row['BOOKING_NO']]['po_no'];
        $is_sales = $row['IS_SALES'];
        $sales_ids .= $row['PO_ID'] . ",";
        $k_source = $row['KNITTING_SOURCE'];
        $sup = $row['KNITTING_PARTY'];
    }
    $sales_id = array_unique(explode(",", $sales_ids));
    $booking_nos = array_unique(explode(",", $booking_no));

    $order_buyer=$style_ref_no=$job_no=$order_nos="";
    foreach ($sales_id as $pid)
    {
        $sales_order_no .= $sales_array[$pid]['no'] . ",";
        if ($sales_array[$pid]['within_group'] == 2)
        {
            $order_buyer .= $buyer_arr[$sales_array[$pid]['buyer_id']] . ",";
            $style_ref_no .= "," . $sales_array[$pid]['style_ref_no'];
            $job_no .= "";
            $order_ids .= "";
            $internal_ref .= "";
        }
        else
        {
            if($sales_array[$pid]['booking_without_order'] != 1)
            {
                $order_buyer .= $buyer_arr[$booking_array[$sales_array[$pid]['sales_booking_no']]['buyer_id']] . ",";
            }
            else
            {
                //for sample without order
                $booking_buyer = return_field_value("buyer_id", "wo_non_ord_samp_booking_mst", "booking_no='".$sales_array[$pid]['sales_booking_no']."'");
                $order_buyer .= $buyer_arr[$booking_buyer].",";
            }
            if($sales_array[$pid]['booking_without_order'] != 1)
            {
                $style_ref_no .= "," . $booking_array[$sales_array[$pid]['sales_booking_no']]['style_ref_no'];
            }
            else
            {
                $style_ref_no .= "," . $sales_array[$pid]['style_ref_no'];
            }


            $job_no .= $booking_array[$sales_array[$pid]['sales_booking_no']]['job_no'] . ",";
            $order_ids .= $booking_array[$sales_array[$pid]['sales_booking_no']]['po_no'] . ",";
            $internal_ref .= $booking_array[$sales_array[$pid]['sales_booking_no']]['internal_ref'] . ",";
        }
    }

    $sales_nos = rtrim(implode(",", array_unique(explode(",", $sales_order_no))), ",");
    $order_buyers = rtrim(implode(",", array_unique(explode(",", $order_buyer))), ",");
    $style_ref_nos = ltrim(implode(",", array_unique(explode(",", $style_ref_no))), ",");
    $job_nos = implode(",", array_unique(explode(",", rtrim($job_no,","))));
    $booking_noss = implode(",", $booking_nos);
    if($program_ids!="")
    {
        $feedingResult =  sql_select("SELECT DTLS_ID, SEQ_NO, COUNT_ID, FEEDING_ID FROM PPL_PLANNING_COUNT_FEED_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0");

        $feedingDataArr = array();
        foreach ($feedingResult as $row)
        {
            $feedingSequence[$row['SEQ_NO']] = $row['SEQ_NO'];
            $feedingDataArr[$row['DTLS_ID']][$row['SEQ_NO']]['count_id'] = $row['COUNT_ID'];
            $feedingDataArr[$row['DTLS_ID']][$row['SEQ_NO']]['feeding_id'] = $row['FEEDING_ID'];
        }
    }
    ?>
    <div style="width:1250px; margin-left:5px;">
        <table width="100%" style="margin-top:10px">
            <tr>
                <td width="100%" align="center" style="font-size:40px;"><b><? echo $company; ?></b></td>
            </tr>
            <tr>
                <td align="center" style="font-size:24px">
                 <?
                 echo show_company($company_id, '', '');
                 ?>
             </td>
         </tr>
         <tr>
            <td width="100%" align="center" style="font-size:32px;"><b>Knitting program / Yarn Store Requisition</b></td>
        </tr>
    </table>
    <div style="margin-top:10px; width:1285px">
        <table width="100%" cellpadding="2" cellspacing="5">
            <tr>
                <td width="200" valign="top" style="font-size:20px"><b>Knitting Factory </b></td>
                <td style="font-size:20px"><b>:</b></td>
                <td  width="500" style="font-size:20px"><b><? echo substr($knitting_factory, 0, -1); ?></b></td>

                <td width="150" style="font-size:20px"><b >Buyer Name </b></td>
                <td style="font-size:20px"><b>:</b></td>
                <td width="200" style="font-size:20px"><b><? echo $order_buyers; ?></b></td>

                <td width="150" style="font-size:20px"><b>Job No </b></td>
                <td style="font-size:20px"><b>:</b></td>
                <td width="200" style="font-size:20px"><b><? echo $job_nos; ?></b></td>
            </tr>
            <tr>
                <td  width="200" valign="top" style="font-size:20px"><b >Address </b></td>
                <td style="font-size:20px" valign="top"><b>:</b></td>
                <td  width="500" style="font-size:20px"><b><? echo $knitting_factory_address; ?></b></td>

                <td width="150" style="font-size:20px"><b>Style </b></td>
                <td style="font-size:20px"><b>:</b></td>
                <td width="200" style="font-size:20px"><b><? echo $style_ref_nos; ?></b></td>

                <td width="150" style="font-size:20px"><b>Booking No </b></td>
                <td style="font-size:20px"><b>:</b></td>
                <td width="200" style="font-size:20px"><b><? echo $booking_noss; ?></b></td>
            </tr>
            <tr>
                <td width="200" style="font-size:20px"><b>Sales Order No </b></td>
                <td style="font-size:20px"><b>:</b></td>
                <td width="500" style="font-size:20px"><b><? echo $sales_nos; ?></b></td>
            </tr>
    </table>
    </div>
    <table width="1150" style="margin-top:10px" border="1" rules="all" cellpadding="0" cellspacing="0"
    class="rpt_table">
    <thead ">
        <th width="30" style="font-size:20px;line-height:1">SL</th>
        <th width="175" style="font-size:20px;line-height:1">Requisition No</th>
        <th width="175" style="font-size:20px;line-height:1">Requisition Date</th>
        <th width="100" style="font-size:20px;line-height:1">Brand</th>
        <th width="100" style="font-size:20px;line-height:1">Lot No</th>
        <th width="250" style="font-size:20px;line-height:1">Yarn Description</th>
        <th width="100" style="font-size:20px;line-height:1">Color</th>
        <th width="120" style="font-size:20px;line-height:1">Requisition Qty.</th>
        <th style="font-size:20px;line-height:1">No Of Cone</th>
    </thead>
    <?
    $j = 1;
    $tot_reqsn_qty = 0;
    foreach ($rqsn_array as $prod_id => $data) {
        if ($j % 2 == 0)
        $bgcolor = "#E9F3FF";
    else
        $bgcolor = "#FFFFFF";
    ?>
    <tr bgcolor="<? echo $bgcolor; ?>">
        <td width="30" style="font-size:20px" align="center"><? echo $j; ?></td>
        <td width="150" style="font-size:20px" align="center"><? echo substr($data['reqsn'], 0, -1); ?></td>
        <td width="150" style="font-size:20px" align="center">
            <?
            echo implode(",",array_unique(explode(",",chop(substr($data['reqsd'], 0, -1),","))));
             ?>
        </td>
        <td width="100" style="font-size:20px" align="center"><p><? echo $product_details_array[$prod_id]['brand']; ?>&nbsp;</p></td>
        <td width="100" style="font-size:20px" align="center"><p><? echo $product_details_array[$prod_id]['lot']; ?></p></td>
        <td width="200" style="font-size:20px" align="center"><p><? echo $product_details_array[$prod_id]['desc']; ?></p>
        </th>
        <td width="100" style="font-size:20px" align="center"><p><? echo $product_details_array[$prod_id]['color']; ?>&nbsp;</p></td>
        <td width="120" style="font-size:20px" align="right"><p><? echo number_format($data['qnty'], 2, '.', ''); ?></p></td>
        <td align="right" style="font-size:20px" align="center"><? echo number_format($data['no_of_cone']); ?></td>
    </tr>
    <?
    $tot_reqsn_qty += $data['qnty'];
    $tot_no_of_cone += $data['no_of_cone'];
    $j++;
}
?>
<tfoot>
    <th colspan="7" align="right" style="font-size:20px" >Total</th>
    <th style="font-size:20px"  align="right"><? echo number_format($tot_reqsn_qty, 2, '.', ''); ?></th>
    <th style="font-size:20px" align="right"><? echo number_format($tot_no_of_cone); ?></th>
</tfoot>
</table>

<table style="margin-top:10px;" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0"
class="rpt_table" align="center">
<thead align="center" >
    <th width="25" style="font-size:20px;line-height:1">SL</th>
    <th width="100" style="font-size:20px;line-height:1">Program Date</th>
    <th width="50" style="font-size:20px;line-height:1">Program No</th>
    <th width="50" style="font-size:20px;line-height:1">Yarn Req. No</th>
    <th width="120" style="font-size:20px;line-height:1">Fabrication</th>
    <th width="50" style="font-size:20px;line-height:1">GSM</th>
    <th width="50" style="font-size:20px;line-height:1">M/c. Dia & GG</th>
    <th width="60" style="font-size:20px;line-height:1">F. Dia <br>Dia Type</th>
    <th width="100" style="font-size:20px;line-height:1">Color</th>
    <th width="50" style="font-size:20px;line-height:1">S/L</th>
    <th width="110" style="font-size:20px;line-height:1">Yarn Description</th>
    <th width="50" style="font-size:20px;line-height:1">Lot</th>
    <th width="70" style="font-size:20px;line-height:1">Yarn Qty.(KG)</th>
    <th width="70" style="font-size:20px;line-height:1">Prpgram Qty.</th>
    <th width="70" style="font-size:20px;line-height:1">Knit Start</th>
    <th width="70" style="font-size:20px;line-height:1">Knit End</th>
    <th style="font-size:20px;line-height:1">Remarks</th>

</thead>
<?
$i = 1;
$s = 1;
$tot_program_qnty = 0;
$tot_yarn_reqsn_qnty = 0;
//$company_id = '';
$sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks,b.advice,b.batch_no,b.tube_ref_no,a.inserted_by from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
//echo $sql;
$nameArray = sql_select($sql);
$prepared_by = $user_library[$nameArray[0][csf('inserted_by')]];
$advice = "";
foreach ($nameArray as $row)
{
    if ($i % 2 == 0)
     $bgcolor = "#E9F3FF";
    else
     $bgcolor = "#FFFFFF";

    $color = '';
    $color_id = explode(",", $row[csf('color_id')]);

    foreach ($color_id as $val)
    {
        if ($color == '')
          $color = $color_library[$val];
        else
          $color .= "," . $color_library[$val];
    }

    if ($company_id == '') $company_id = $row[csf('company_id')];

    $machine_no = '';
    $machine_id = explode(",", $row[csf('machine_id')]);

    foreach ($machine_id as $val)
    {
        if ($machine_no == '')
            $machine_no = $machine_arr[$val];
        else
            $machine_no .= "," . $machine_arr[$val];
    }
    if ($machine_id[0] != "")
    {
     $sql_floor = sql_select("select id, machine_no, floor_id from lib_machine_name where id=$machine_id[0] and status_active=1 and is_deleted=0  order by seq_no");
    }

    $count_feeding = "";
    foreach($feedingDataArr[$row[csf('program_id')]] as $feedingSequence=>$feedingData)
    {
        if($count_feeding =="")
        {
            $count_feeding = $feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
        }
        else
        {
            $count_feeding .= ",".$feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
        }
    }

    if ($knit_id_array[$row[csf('program_id')]] != "")
    {
        $all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
        $row_span = count($all_prod_id);
        $z = 0;

        foreach ($all_prod_id as $prod_id)
        {
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <?
                if ($z == 0)
                {
                    ?>
                    <td style="font-size:20px;" width="25" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
                    <td style="font-size:20px;" width="100" rowspan="<? echo $row_span; ?>" align="center" style="font-size:16px;">
                    <? echo change_date_format($row[csf('program_date')]); ?></td>
                    <td style="font-size:20px;" width="60" rowspan="<? echo $row_span; ?>" align="center" style="font-size:16px;">
                    <? echo $row[csf('program_id')]; ?></td>
                    <td style="font-size:20px;" width="60" rowspan="<? echo $row_span; ?>" align="center" style="font-size:16px;">
                    <? echo $requisiontNoArr[$row[csf('program_id')]]['requisition_no']; ?></td>
                    <td style="font-size:20px;" align="center" width="120" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
                    <td style="font-size:20px;" width="50" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('gsm_weight')]; ?></p></th>
                    <td style="font-size:20px;" align="center" width="70" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
                    <td style="font-size:20px;" align="center" width="60" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_dia')].', '.$fabric_typee[$row[csf('diatype')]]; ?></p></td>
                    <td style="font-size:20px;" align="center" width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color; ?></p></td>
                    <td style="font-size:20px;" align="center" width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('stitch_length')]; ?></p></td>
                    <?

                    $tot_program_qnty += $row[csf('program_qnty')];
                    $i++;
                }
                ?>

                    <td style="font-size:20px;" align="center" width="110"><p><? echo $product_details_array[$prod_id]['desc']; ?>&nbsp;</p></td>
                    <td style="font-size:20px;" align="center" width="50" align="center"><p><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</p></td>
                    <td style="font-size:20px;" align="center" width="70" align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id], 2, '.', ''); ?></td>
                    <?
                    if ($z == 0) {
                    ?>
                    <td style="font-size:20px;"  width="70" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?></td>
                    <td style="font-size:20px;" align="center" width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
                    <td style="font-size:20px;" align="center" width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
                    <td style="font-size:20px;" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>

                    <? } ?>


            </tr>
                <?
                $tot_yarn_reqsn_qnty += $prod_id_array[$row[csf('program_id')]][$prod_id];
                $z++;
        }
    }
    else
    {
        ?>
        <tr bgcolor="<? echo $bgcolor; ?>">
            <td style="font-size:20px;" align="center" width="25"><? echo $i; ?></td>
            <td style="font-size:20px;" width="100" align="center" style="font-size:16px;"><? echo change_date_format($row[csf('program_date')]); ?></td>
            <td style="font-size:20px;"  width="60" align="center" style="font-size:16px;"><? echo $row[csf('program_id')]; ?></td>
            <td style="font-size:20px;"  width="60" align="center" style="font-size:16px;"><? echo  $requisiontNoArr[$row[csf('program_id')]]['requisition_no'] ; ?></td>
            <td style="font-size:20px;" align="center" width="120"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
            <td style="font-size:20px;"  width="50" align="center"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
            <td style="font-size:20px;" align="center" width="50"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
            <td style="font-size:20px;" align="center" width="60"><p><? echo $row[csf('fabric_dia')].', '.$fabric_typee[$row[csf('diatype')]]; ?></p></td>
            <td style="font-size:20px;" align="center" width="50"><p><? echo $color; ?></p></td>
            <td style="font-size:20px;" align="center" width="60"><p><? echo $row[csf('stitch_length')]; ?></p></td>
            <td style="font-size:20px;" align="center" width="110"><p>&nbsp;</p></td>
            <td style="font-size:20px;" align="center" width="50"><p>&nbsp;</p></td>
            <td style="font-size:20px;" align="center" width="70" align="right">&nbsp;</td>
            <td style="font-size:20px;"  width="70" align="right"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?></td>
            <td style="font-size:20px;" align="center" width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
            <td style="font-size:20px;" align="center" width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
            <td style="font-size:20px;" align="center"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
        </tr>
        <?
        $tot_program_qnty += $row[csf('program_qnty')];
        $i++;
    }
    $advice = $row[csf('advice')];
}
?>
<tfoot>
    <th style="font-size:20px;"  colspan="12" align="right"><b>Total</b></th>
    <th style="font-size:20px;"  align="right"><? echo number_format($tot_yarn_reqsn_qnty, 2, '.', ''); ?></th>
    <th style="font-size:20px;"  align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?>&nbsp;</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>

</tfoot>

</table>
<br>
<?


$sql_collarCuff = sql_select("select id, body_part_id, grey_size, finish_size, qty_pcs, dtls_id from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id in($program_ids) order by id");

$progWiseDataArr = array();
foreach($sql_collarCuff as $row)
{
    $progWiseDataArr[$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('grey_size')]][$row[csf('finish_size')]][$row[csf('qty_pcs')]]['dtls_id']=$row[csf('dtls_id')];
    $progWiseDataArr[$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('grey_size')]][$row[csf('finish_size')]][$row[csf('qty_pcs')]]['body_part_id']=$row[csf('body_part_id')];
    $progWiseDataArr[$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('grey_size')]][$row[csf('finish_size')]][$row[csf('qty_pcs')]]['grey_size']=$row[csf('grey_size')];
    $progWiseDataArr[$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('grey_size')]][$row[csf('finish_size')]][$row[csf('qty_pcs')]]['finish_size']=$row[csf('finish_size')];
    $progWiseDataArr[$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('grey_size')]][$row[csf('finish_size')]][$row[csf('qty_pcs')]]['qty_pcs']=$row[csf('qty_pcs')];
}
unset($sql_collarCuff);
// echo "<pre>";
// print_r($progWiseDataArr);
// echo "</pre>";

if (count($progWiseDataArr) > 0) {
   ?>
   <table style="margin-top:10px;" width="730" border="1" rules="all" cellpadding="0" cellspacing="0"
   class="rpt_table">
   <thead>
    <tr>
        <th width="50" style="font-size:20px;" align="center">SL</th>
        <th width="200" style="font-size:20px;" align="center"> Body Part</th>
        <th width="100" style="font-size:20px;" align="center">Prog No</th>
        <th width="100" style="font-size:20px;" align="center">Color</th>
        <th width="100" style="font-size:20px;" align="center">Grey Size</th>
        <th width="100" style="font-size:20px;" align="center">Finish Size</th>
        <th style="font-size:20px;line-height:1" align="center">Quantity Pcs</th>
    </tr>
</thead>
<tbody>
    <?
    foreach ($progWiseDataArr as $prog_id=>$prog_data)
    {
        foreach($prog_data as $b_part_id=>$b_part_data)
        {
            foreach($b_part_data as $g_size=>$g_size_data)
            {
                foreach($g_size_data as $f_size=>$f_size_data)
                {
                    foreach($f_size_data as $row=>$val)
                    {
                        $prog_count[$prog_id]++;
                    }
                }
            }
        }
    }
    $i = 1;
    $total_qty_pcs = 0;
    foreach ($progWiseDataArr as $prog_id=>$prog_data)
    {
        foreach($prog_data as $b_part_id=>$b_part_data)
        {
            foreach($b_part_data as $g_size=>$g_size_data)
            {
                foreach($g_size_data as $f_size=>$f_size_data)
                {
                    foreach($f_size_data as $row=>$val)
                    {

                        $prog_span = $prog_count[$prog_id];
                        // echo "<pre>";
                        // print_r($val);
                        // echo "</pre>";
                        if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                        ?>
                        <tr>

                            <?
                                  if(!in_array($prog_id,$prog_chk))
                                  {
                                    $prog_chk[]=$prog_id;
                                    ?>
                                    <td style="font-size:20px;"  align="center" rowspan="<? echo $prog_span ;?>" valign='middle' style="text-align:center"><p><? echo $i; ?>&nbsp;</p></td>
                                    <td style="font-size:20px;" align="center" rowspan="<? echo $prog_span ;?>" valign='middle' style="text-align:center"><p><? echo $body_part[$val['body_part_id']]; ?>&nbsp;</p></td>
                                    <td style="font-size:20px;" align="center" title="<? echo $prog_id;?>" rowspan="<? echo $prog_span ;?>" valign='middle' style="text-align:center"><p><? echo $prog_id; ?>&nbsp;</p></td>
                                    <td style="font-size:20px;" align="center" rowspan="<? echo $prog_span ;?>" valign='middle' style="text-align:center"><p><? echo $color_library[$stripeWiseColorArr[$prog_id]['color_number_id']]; ?>&nbsp;</p></td>
                                    <?
                                  }
                            ?>

                            <td style="font-size:20px;padding-left:5px"><p><? echo $val['grey_size']; ?>&nbsp;</p></td>
                            <td style="font-size:20px;padding-left:5px"><p><? echo $val['finish_size']; ?>&nbsp;</p></td>
                            <td style="font-size:20px;" align="right"><p><? echo number_format($val['qty_pcs'], 0);
                                $total_qty_pcs += $val['qty_pcs']; ?>&nbsp;&nbsp;</p></td>
                        </tr>
                        <?

                    }
                }
            }
        }
        $i++;
    }
    ?>
</tbody>
<tfoot>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th style="font-size:20px;" align="right">Total</th>
        <th style="font-size:20px;" align="right"><? echo number_format($total_qty_pcs, 0); ?>&nbsp;</th>
    </tr>
</tfoot>
</table>
<?
}
?>
<br>
<?
$sql_fedder = sql_select("select a.id, a.color_id, a.stripe_color_id, a.no_of_feeder, max(b.measurement) as measurement, max(b.uom) as uom,a.dtls_id from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.dtls_id in($program_ids) and b.job_no in('".$sales_nos."') and a.no_of_feeder>0 group by a.id, a.color_id, a.stripe_color_id, a.no_of_feeder,a.dtls_id order by a.id,a.dtls_id");

$stripeDataArr = array();
foreach($sql_fedder as $row)
{
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['dtls_id']=$row[csf('dtls_id')];
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['color_id']=$row[csf('color_id')];
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['stripe_color_id']=$row[csf('stripe_color_id')];
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['measurement']=$row[csf('measurement')];
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['uom']=$row[csf('uom')];
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['no_of_feeder']=$row[csf('no_of_feeder')];


}
foreach ($stripeDataArr as $prog_ids=>$prog_data)
{
    foreach($prog_data as $c_number_id=>$c_number_data)
    {
        foreach($c_number_data as $stripe_color_id=>$stripe_color_data)
        {
            foreach($stripe_color_data as $m_id=>$m_data)
            {
                foreach($m_data as $u_id=>$u_data)
                {
                    foreach($u_data as $row=>$val)
                    {
                        $s_prog_count[$prog_ids]++;
                    }
                }
            }
        }
    }
}
/* echo "<pre>";
print_r($s_prog_count);
echo "</pre>"; */

if (count($sql_fedder) > 0) {
    ?>
    <table style="margin-top:10px;" width="650" border="1" rules="all" cellpadding="0" cellspacing="0"
    class="rpt_table">
    <thead>
        <tr>
            <th width="50" style="font-size:20px;" align="center">SL</th>
            <th width="100" style="font-size:20px;" align="center">Program No</th>
            <th width="120" style="font-size:20px;" align="center">Color</th>
            <th width="120" style="font-size:20px;" align="center">Stripe Color</th>
            <th width="100" style="font-size:20px;" align="center">Measurement</th>
            <th width="100" style="font-size:20px;" align="center">UOM</th>
            <th style="font-size:20px;line-height:1" align="center">No Of Feeder</th>
        </tr>
    </thead>
    <tbody>
        <?
        $i = 1;
        $total_feeder = 0;
        foreach ($sql_fedder as $row) {
            if ($i % 2 == 0)
                $bgcolor = "#E9F3FF";
            else
                $bgcolor = "#FFFFFF";
            ?>
            <tr>

            <?
                $prog_ids=$row[csf('dtls_id')];
                $s_prog_span = $s_prog_count[$prog_ids];
                /*if(!in_array($prog_ids,$prog_chks))
                {
                    $prog_chks[]=$prog_ids;
                    ?>
                    <td align="center" rowspan="<? echo $s_prog_span;?>" valign='middle' style="text-align:center"><p><? echo $i; ?>&nbsp;</p></td>
                    <td align="center" rowspan="<? echo $s_prog_span;?>" valign='middle' style="text-align:center"><p><? echo $row[csf('dtls_id')]; ?>&nbsp;</p></td>
                    <td align="center" rowspan="<? echo $s_prog_span;?>" valign='middle' style="text-align:center"><p><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</p></td>
                    <?
                }*/
                ?>
            <!--    <td align="center"><p><? //echo $color_library[$row[csf('stripe_color_id')]]; ?>&nbsp;</p></td>
                <td align="center"><p><? //echo number_format($row[csf('measurement')], 2); ?>&nbsp;</p></td>
                <td align="center"><p><? //echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
                <td align="center"><p><? //echo number_format($row[csf('no_of_feeder')], 0);
                //$total_feeder += $row[csf('no_of_feeder')]; ?>&nbsp;</p></td>  -->

                <td style="font-size:20px;"  align="center" style="text-align:center"><p><? echo $i; ?>&nbsp;</p></td>
                <td style="font-size:20px;" align="center" style="text-align:center"><p><? echo $row[csf('dtls_id')]; ?>&nbsp;</p></td>
                <td style="font-size:20px;" align="center" style="text-align:center"><p><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</p></td>
                <td style="font-size:20px;" align="center"><p><? echo $color_library[$row[csf('stripe_color_id')]]; ?>&nbsp;</p></td>
                <td style="font-size:20px;" align="center"><p><? echo number_format($row[csf('measurement')], 2); ?>&nbsp;</p></td>
                <td style="font-size:20px;" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
                <td style="font-size:20px;" align="center"><p><? echo number_format($row[csf('no_of_feeder')], 0);
                $total_feeder += $row[csf('no_of_feeder')]; ?>&nbsp;</p></td>
            </tr>
            <?
            $i++;
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th style="font-size:20px;" align="right">Total:</th>
            <th style="font-size:20px;text-align: center;"><? echo number_format($total_feeder, 0); ?></th>
        </tr>
    </tfoot>
</table>
<?
}

?>
<br>
<table border="1" rules="all" class="rpt_table">
    <tr>
        <td style="font-size:24px; font-weight:bold; width:20px;">ADVICE:</td>
        <td style="font-size:20px; width:100%;">     <? echo $advice; ?></td>
    </tr>
</table>
<!-- <div style="margin-top:60px; text-align: left;"><strong>Rate/Kg =</strong></div> -->
<br/>
<div style="float:left; width: 452px;">
    <div style="border:1px solid #000;">
        <table border="1" rules="all" class="rpt_table" width="450" height="150">
            <thead>
                <th colspan="2" style="font-size:18px; font-weight:bold;padding:5px;">Please Strictly Avoid The Following Knitting Faults Like..
                </th>
            <thead>
            <tr>
                <td width="450" style="font-size:20px;">Hole,  Star Mark,  Lycra Drop,  Lycra Out,  Loop, Lot Mixing</td>
            </tr>
            <tr>
                <td width="450" style="font-size:20px;">Oil Spot,  Dia Mark,  Needle Line,  Fly contra etc.</td>
            </tr>

        </table>
    </div>
    <br>
    <div>
    <p style="font-size:20px;">বিঃ দ্রঃ<br>
    * প্রোগ্রামের শেষ পর্যায়ে অবশ্যই সূতা মিল করে চালাতে হবে।<br>
    * সূতা মেশিনে উঠানোর সময় ১/১ করে উঠতে হবে।<br>
    * রোল মার্কিং সঠিকভাবে করতে হবে।<br>
    * রোলে কোন প্রকার কাটা-কাটি বা ভুল লেখা চলবেনা।<br>
    * রোলের গায়ে জব, কালার, মেশিন নম্বর, তারিখ এবং শিফট অবশ্যই লিখতে হবে।<br>
    * প্রতিটা রোল এর ওজন ২৫ কেজির বেশি হতে হবে
    </p>
    </div>
</div>
<div style="float:right; border:1px solid #000;">
    <table border="1" rules="all" class="rpt_table" width="700" height="150">
        <thead>
            <th colspan="3" style="font-size:18px; font-weight:bold;padding:5px;">Please Mark The Role The Each Role as
                Follows
            </th>
        <thead>
        <tr>
            <td valign='top' width="200" style="font-size:20px;"><b> 1.</b> Manufacturing Factory Name</td>
            <td valign='top' style="font-size:20px;"><b> 2.</b>  Prog. Company Name</td>
            <td valign='top' style="font-size:20px;"><b> 3.</b> Buyer, Style,Order no.</td>
        </tr>
        <tr>
            <td valign='top' width="200" style="font-size:20px;"><b> 4.</b> Yarn Count, Lot & Brand</td>
            <td valign='top' style="font-size:20px;"><b> 5.</b>  M/C No, Dia, Stitch Length</td>
            <td valign='top' style="font-size:20px;"><b> 6.</b> Fabrics Type & Finished Dia</td>
        </tr>
        <tr>
            <td valign='top' width="200" style="font-size:20px;"><b> 7.</b> Finished Gsm & Color</td>
            <td valign='top' style="font-size:20px;"><b> 8.</b>  Yarn Composition</td>
            <td valign='top' style="font-size:20px;"><b> 9.</b> Knit Program No</td>
        </tr>
    </table>
</div>
<?

echo signature_table(213, $company_id, "1180px",1,40,$prepared_by);
?>
</div>
<?
exit();
}

if ($action == "requisition_print_four")
{
    extract($_REQUEST);
    $data = explode('**', $data);
    //var_dump($data);
    if ($data[2])
    {
        echo load_html_head_contents("Program Qnty Info", "../", 1, 1, '', '', '');
    }
    else
    {
        echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
    }

    $typeForAttention = $data[1];
    $program_ids = $data[0];
    $within_group = $data[3];
    // $company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
    $brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
    $floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
    $yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by id, yarn_count","id","yarn_count");

    $company_info = sql_select("select ID, COMPANY_NAME, PLOT_NO, ROAD_NO, CITY, CONTACT_NO, COUNTRY_ID from lib_company where status_active=1 and is_deleted=0 order by company_name");
    foreach($company_info as $row)
    {
        $company_details[$row['ID']] = $row['COMPANY_NAME'];
        $company_address_arr[$row['ID']] = 'Plot No:'.$row['PLOT_NO'].', Road No:'.$row['ROAD_NO'].', City / Town:'.$row['CITY'].', Country:'.$country_name_arr[$row['COUNTRY_ID']].', Contact No:'.$row['CONTACT_NO'];
    }
    unset($company_info);

    //for supplier
    $sqlSupplier = sql_select("select id as ID, supplier_name as SUPPLIER_NAME, short_name as SHORT_NAME, ADDRESS_1 from lib_supplier");
    foreach($sqlSupplier as $row)
    {
        $supplier_arr[$row['ID']] = $row['SHORT_NAME'];
        $supplier_dtls_arr[$row['ID']] = $row['SUPPLIER_NAME'];
        $supplier_address_arr[$row['ID']] = $row['ADDRESS_1'];
    }
    unset($sqlSupplier);

    //for sales order information
    $po_dataArray = sql_select("SELECT ID, JOB_NO, BUYER_ID, STYLE_REF_NO, WITHIN_GROUP, SALES_BOOKING_NO, BOOKING_WITHOUT_ORDER FROM FABRIC_SALES_ORDER_MST WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PO_ID FROM PPL_PLANNING_ENTRY_PLAN_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0)");
    foreach ($po_dataArray as $row)
    {
        $sales_array[$row['ID']]['no'] = $row['JOB_NO'];
        $sales_array[$row['ID']]['sales_booking_no'] = $row['SALES_BOOKING_NO'];
        $sales_array[$row['ID']]['buyer_id'] = $row['BUYER_ID'];
        $sales_array[$row['ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
        $sales_array[$row['ID']]['within_group'] = $row['WITHIN_GROUP'];
        $sales_array[$row['ID']]['booking_without_order'] = $row['BOOKING_WITHOUT_ORDER'];
    }

    //for booking information
    $book_dataArray = sql_select("SELECT A.BUYER_ID, B.BOOKING_NO, B.PO_BREAK_DOWN_ID AS PO_ID, B.JOB_NO, C.PO_NUMBER, D.STYLE_REF_NO, C.GROUPING FROM WO_BOOKING_MST A,WO_BOOKING_DTLS B, WO_PO_BREAK_DOWN C,WO_PO_DETAILS_MASTER D WHERE A.BOOKING_NO=B.BOOKING_NO AND B.PO_BREAK_DOWN_ID=C.ID AND C.JOB_NO_MST=D.JOB_NO AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND B.BOOKING_NO IN(SELECT SALES_BOOKING_NO FROM FABRIC_SALES_ORDER_MST WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PO_ID FROM PPL_PLANNING_ENTRY_PLAN_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0))");
    foreach ($book_dataArray as $row)
    {
        $booking_array[$row['BOOKING_NO']]['booking_no'] = $row['BOOKING_NO'];
        $booking_array[$row['BOOKING_NO']]['po_id'] = $row['PO_ID'];
        $booking_array[$row['BOOKING_NO']]['buyer_id'] = $row['BUYER_ID'];
        $booking_array[$row['BOOKING_NO']]['po_no'] = $row['PO_NUMBER'];
        $booking_array[$row['BOOKING_NO']]['job_no'] = $row['JOB_NO'];
        $booking_array[$row['BOOKING_NO']]['style_ref_no'] = $row['STYLE_REF_NO'];
        $booking_array[$row['BOOKING_NO']]['internal_ref'] = $row['GROUPING'];
    }

    //for product information
    $product_details_array = array();
    $sql = "SELECT ID, SUPPLIER_ID, LOT, CURRENT_STOCK, YARN_COMP_TYPE1ST, YARN_COMP_PERCENT1ST, YARN_COMP_TYPE2ND, YARN_COMP_PERCENT2ND, YARN_COUNT_ID, YARN_TYPE, COLOR, BRAND FROM PRODUCT_DETAILS_MASTER WHERE ITEM_CATEGORY_ID=1 AND STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PROD_ID FROM PPL_YARN_REQUISITION_ENTRY WHERE KNIT_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0 GROUP BY PROD_ID)";
    $result = sql_select($sql);
    foreach ($result as $row)
    {
        $compos = '';
        if ($row['YARN_COMP_PERCENT2ND'] != 0)
        {
            $compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
        }
        else
        {
            $compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']];
        }

        $product_details_array[$row['ID']]['desc'] = $count_arr[$row['YARN_COUNT_ID']] . " " . $compos . " " . $yarn_type[$row['YARN_TYPE']];
        $product_details_array[$row['ID']]['lot'] = $row['LOT'];
        $product_details_array[$row['ID']]['brand'] = $brand_arr[$row['BRAND']];
        $product_details_array[$row['ID']]['color'] = $color_library[$row['COLOR']];
    }

    //for requisition information
    $knit_id_array = array();
    $prod_id_array = array();
    $rqsn_array = array();
    $reqsn_dataArray = sql_select("SELECT KNIT_ID, REQUISITION_NO, REQUISITION_DATE, PROD_ID, SUM(NO_OF_CONE) AS NO_OF_CONE, SUM(YARN_QNTY) AS YARN_QNTY FROM PPL_YARN_REQUISITION_ENTRY WHERE KNIT_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0 GROUP BY KNIT_ID, PROD_ID, REQUISITION_NO,REQUISITION_DATE");
    foreach ($reqsn_dataArray as $row)
    {
        $prod_id_array[$row['KNIT_ID']][$row['PROD_ID']] = $row['YARN_QNTY'];
        $knit_id_array[$row['KNIT_ID']] .= $row['PROD_ID'] . ",";
        $rqsn_array[$row['PROD_ID']]['reqsn'] .= $row['REQUISITION_NO'] . ",";
        $rqsn_array[$row['PROD_ID']]['reqsd'] .= $row['REQUISITION_DATE'] . ",";
        $rqsn_array[$row['PROD_ID']]['qnty'] += $row['YARN_QNTY'];
        $rqsn_array[$row['PROD_ID']]['no_of_cone'] += $row['NO_OF_CONE'];

        $requisiontNoArr[$row['KNIT_ID']]['requisition_no'] = $row['REQUISITION_NO'];
    }

    $sales_order_no = '';
    $buyer_name = '';
    $knitting_factory = '';
    $booking_no = '';
    $wg_yes_booking = '';
    $company = '';
    $order_buyer = '';
    $style_ref_no = '';
    $dataArray = sql_select("SELECT A.ID, A.KNITTING_SOURCE, A.KNITTING_PARTY, B.BUYER_ID, B.BOOKING_NO, B.COMPANY_ID, LISTAGG(CAST(B.PO_ID AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY B.PO_ID) AS PO_ID, A.IS_SALES, B.WITHIN_GROUP FROM PPL_PLANNING_INFO_ENTRY_DTLS A, PPL_PLANNING_ENTRY_PLAN_DTLS B WHERE A.ID=B.DTLS_ID AND A.ID IN (".$program_ids.") AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 GROUP BY A.ID, A.KNITTING_SOURCE, A.KNITTING_PARTY, B.BUYER_ID, B.BOOKING_NO, B.COMPANY_ID,A.IS_SALES,B.WITHIN_GROUP");
    $k_source = "";
    $sup = $sales_ids = "";
    foreach ($dataArray as $row)
    {
        if ($duplicate_arr[$row['KNITTING_SOURCE']][$row['KNITTING_PARTY']] == "")
        {
            $duplicate_arr[$row['KNITTING_SOURCE']][$row['KNITTING_PARTY']] = $row['KNITTING_PARTY'];
        }

        $knitting_factory_address = '';
        if ($row['KNITTING_SOURCE'] == 1)
        {
            $knitting_factory .= $company_details[$row['KNITTING_PARTY']] . ",";
            $knitting_factory_address .= $company_address_arr[$row['KNITTING_PARTY']] . ",";
        }
        else if ($row['KNITTING_SOURCE'] == 3)
        {
            $knitting_factory .= $supplier_details[$row['KNITTING_PARTY']] . ",";
            $knitting_factory_address .= $supplier_address_arr[$row['KNITTING_PARTY']] . ",";
        }
        $knitting_factory=implode(",",array_unique(explode(",",$knitting_factory)));
        $knitting_factory_address=implode(",",array_unique(explode(",",$knitting_factory_address)));

        if ($buyer_name == "")
        {
            $buyer_name = $buyer_arr[$row['BUYER_ID']];
        }

        if ($booking_no != '')
        {
            $booking_no .= "," . $row['BOOKING_NO'];
        }
        else
        {
            $booking_no = $row['BOOKING_NO'];
        }

        if ($company == "")
        {
            $company = $company_details[$row['COMPANY_ID']];
        }

        if ($company_id == "")
        {
            $company_id = $row['COMPANY_ID'];
        }
        $order_nos .= "," . $booking_array2[$row['BOOKING_NO']]['po_no'];
        $is_sales = $row['IS_SALES'];
        $sales_ids .= $row['PO_ID'] . ",";
        $k_source = $row['KNITTING_SOURCE'];
        $sup = $row['KNITTING_PARTY'];
    }
    $sales_id = array_unique(explode(",", $sales_ids));
    $booking_nos = array_unique(explode(",", $booking_no));

    $order_buyer=$style_ref_no=$job_no=$order_nos="";
    foreach ($sales_id as $pid)
    {
        $sales_order_no .= $sales_array[$pid]['no'] . ",";
        if ($sales_array[$pid]['within_group'] == 2)
        {
            $order_buyer .= $buyer_arr[$sales_array[$pid]['buyer_id']] . ",";
            $style_ref_no .= "," . $sales_array[$pid]['style_ref_no'];
            $job_no .= "";
            $order_ids .= "";
            $internal_ref .= "";
        }
        else
        {
            if($sales_array[$pid]['booking_without_order'] != 1)
            {
                $order_buyer .= $buyer_arr[$booking_array[$sales_array[$pid]['sales_booking_no']]['buyer_id']] . ",";
            }
            else
            {
                //for sample without order
                $booking_buyer = return_field_value("buyer_id", "wo_non_ord_samp_booking_mst", "booking_no='".$sales_array[$pid]['sales_booking_no']."'");
                $order_buyer .= $buyer_arr[$booking_buyer].",";
            }
            if($sales_array[$pid]['booking_without_order'] != 1)
            {
                $style_ref_no .= "," . $booking_array[$sales_array[$pid]['sales_booking_no']]['style_ref_no'];
            }
            else
            {
                $style_ref_no .= "," . $sales_array[$pid]['style_ref_no'];
            }


            $job_no .= $booking_array[$sales_array[$pid]['sales_booking_no']]['job_no'] . ",";
            $order_ids .= $booking_array[$sales_array[$pid]['sales_booking_no']]['po_no'] . ",";
            $internal_ref .= $booking_array[$sales_array[$pid]['sales_booking_no']]['internal_ref'] . ",";
        }
    }

    $sales_nos = rtrim(implode(",", array_unique(explode(",", $sales_order_no))), ",");
    $order_buyers = rtrim(implode(",", array_unique(explode(",", $order_buyer))), ",");
    $style_ref_nos = ltrim(implode(",", array_unique(explode(",", $style_ref_no))), ",");
    $job_nos = implode(",", array_unique(explode(",", rtrim($job_no,","))));
    $booking_noss = implode(",", $booking_nos);
    if($program_ids!="")
    {
        $feedingResult =  sql_select("SELECT DTLS_ID, SEQ_NO, COUNT_ID, FEEDING_ID FROM PPL_PLANNING_COUNT_FEED_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0");

        $feedingDataArr = array();
        foreach ($feedingResult as $row)
        {
            $feedingSequence[$row['SEQ_NO']] = $row['SEQ_NO'];
            $feedingDataArr[$row['DTLS_ID']][$row['SEQ_NO']]['count_id'] = $row['COUNT_ID'];
            $feedingDataArr[$row['DTLS_ID']][$row['SEQ_NO']]['feeding_id'] = $row['FEEDING_ID'];
        }
    }
    ?>
    <div style="width:1250px; margin-left:5px;">
        <table width="100%" style="margin-top:10px">
            <tr>
                <td width="100%" align="center" style="font-size:20px;"><b><? echo $company; ?></b></td>
            </tr>
            <tr>
                <td align="center" style="font-size:14px">
                 <?
                 echo show_company($company_id, '', '');
                 ?>
             </td>
         </tr>
         <tr>
            <td width="100%" align="center" style="font-size:20px;"><b><u>Knitting program / Yarn Store Requisition</u></b></td>
        </tr>
    </table>
    <div style="margin-top:10px; width:950px">
        <table width="100%" cellpadding="2" cellspacing="5">
            <tr>
                <td width="140"><b style="font-size:18px">Knitting Factory </b></td>
                <td>:</td>
                <td style="font-size:18px"><b><? echo substr($knitting_factory, 0, -1); ?></b></td>
            </tr>
            <tr>
                <td width="140"><b style="font-size:18px">Address</b></td>
                <td>:</td>
                <td style="font-size:18px"><? echo $knitting_factory_address; ?></td>
            </tr>
            <tr>
                <td width="140" style="font-size:18px"><b>Attention </b></td>
                <td>:</td>
                <?
                if ($typeForAttention == 1)
                {
                  echo "<td style=\"font-size:18px; font-weight:bold;\"> Knitting Manager </td>";
                }
                else
                {
                  ?>
                  <td style="font-size:18px; font-weight:bold;"><b><?
                    if ($k_source == 3)
                    {
                     $ComArray = sql_select("select id,contact_person from lib_supplier where id=$sup");
                     foreach ($ComArray as $row)
                     {
                      echo $row[csf('contact_person')];
                    }
              } else {

                 echo "";
             }
             ?></b></td>
             <? } ?>
         </tr>
         <tr>
            <td><b>Buyer Name </b></td>
            <td>:</td>
            <td><? echo $order_buyers; ?></td>
        </tr>
        <tr>
            <td><b>Style </b></td>
            <td>:</td>
            <td><? echo $style_ref_nos; ?></td>
        </tr>
        <tr>
            <td><b>Order No </b></td>
            <td>:</td>
            <td><? echo rtrim($order_ids,","); ?></td>
        </tr>
        <tr>
            <td><b>Internal Ref. </b></td>
            <td>:</td>
            <td><? echo rtrim($internal_ref,","); ?></td>
        </tr>
        <tr>
            <td><b>Job No </b></td>
            <td>:</td>
            <td><? echo $job_nos; ?></td>
        </tr>
        <tr>
            <td><b>Booking No </b></td>
            <td>:</td>
            <td><? echo $booking_noss; ?></td>
        </tr>
        <tr>
            <td><b>Sales Order No </b></td>
            <td>:</td>
            <td><? echo $sales_nos; ?></td>
        </tr>
    </table>
    </div>
    <table width="1050" style="margin-top:10px" border="1" rules="all" cellpadding="0" cellspacing="0"
    class="rpt_table">
    <thead>
        <th width="30">SL</th>
        <th width="100">Requisition No</th>
        <th width="100">Requisition Date</th>
        <th width="100">Brand</th>
        <th width="100">Lot No</th>
        <th width="200">Yarn Description</th>
        <th width="100">Color</th>
        <th width="100">Requisition Qty.</th>
        <th>No Of Cone</th>
    </thead>
    <?
    $j = 1;
    $tot_reqsn_qty = 0;
    foreach ($rqsn_array as $prod_id => $data) {
        if ($j % 2 == 0)
        $bgcolor = "#E9F3FF";
    else
        $bgcolor = "#FFFFFF";
    ?>
    <tr bgcolor="<? echo $bgcolor; ?>">
        <td width="30"><? echo $j; ?></td>
        <td width="100"><? echo substr($data['reqsn'], 0, -1); ?></td>
        <td width="100"><? echo substr($data['reqsd'], 0, -1); ?></td>
        <td width="100"><p><? echo $product_details_array[$prod_id]['brand']; ?>&nbsp;</p></td>
        <td width="100"><p><? echo $product_details_array[$prod_id]['lot']; ?></p></td>
        <td width="200"><p><? echo $product_details_array[$prod_id]['desc']; ?></p>
        </th>
        <td width="100"><p><? echo $product_details_array[$prod_id]['color']; ?>&nbsp;</p></td>
        <td width="100" align="right"><p><? echo number_format($data['qnty'], 2, '.', ''); ?></p></td>
        <td align="right"><? echo number_format($data['no_of_cone']); ?></td>
    </tr>
    <?
    $tot_reqsn_qty += $data['qnty'];
    $tot_no_of_cone += $data['no_of_cone'];
    $j++;
}
?>
<tfoot>
    <th colspan="7" align="right">Total</th>
    <th align="right"><? echo number_format($tot_reqsn_qty, 2, '.', ''); ?></th>
    <th><? echo number_format($tot_no_of_cone); ?></th>
</tfoot>
</table>

<table style="margin-top:10px;" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0"
class="rpt_table" align="center">
<thead align="center">
    <th width="25">SL</th>
    <th width="100">Program Date</th>
    <th width="50">Program No</th>
    <th width="50">Requisition No</th>
    <th width="120">Batch No</th>
    <th width="120">Tube/Ref. No</th>
    <th width="120">Fabrication</th>
    <th width="50">GSM</th>
    <th width="40">F. Dia</th>
    <th width="60">Dia Type</th>

    <th width="45">Floor</th>

    <th width="45">M/c. No</th>
    <th width="50">M/c. Dia & GG</th>
    <th width="100">Color</th>
    <th width="60">Color Range</th>
    <th width="50">S/L</th>
    <th width="50">Spandex S/L</th>
    <th width="50">Feeder</th>
    <th width="100">Count Feeding</th>
    <th width="70">Knit Start</th>
    <th width="70">Knit End</th>
    <th width="70">Prpgram Qty.</th>
    <th width="110">Yarn Description</th>
    <th width="50">Lot</th>
    <th width="70">Yarn Qty.(KG)</th>
    <th width="70">No. Of Ply</th>
    <th>Remarks</th>

</thead>
<?
$i = 1;
$s = 1;
$tot_program_qnty = 0;
$tot_yarn_reqsn_qnty = 0;
//$company_id = '';
$sql = "SELECT a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks,b.advice,b.batch_no,b.tube_ref_no, b.no_of_ply from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
// echo $sql;
$nameArray = sql_select($sql);
$advice = "";
foreach ($nameArray as $row)
{
    if ($i % 2 == 0)
     $bgcolor = "#E9F3FF";
    else
     $bgcolor = "#FFFFFF";

    $color = '';
    $color_id = explode(",", $row[csf('color_id')]);

    foreach ($color_id as $val)
    {
        if ($color == '')
          $color = $color_library[$val];
        else
          $color .= "," . $color_library[$val];
    }

    if ($company_id == '') $company_id = $row[csf('company_id')];

    $machine_no = '';
    $machine_id = explode(",", $row[csf('machine_id')]);

    foreach ($machine_id as $val)
    {
        if ($machine_no == '')
            $machine_no = $machine_arr[$val];
        else
            $machine_no .= "," . $machine_arr[$val];
    }
    if ($machine_id[0] != "")
    {
     $sql_floor = sql_select("select id, machine_no, floor_id from lib_machine_name where id=$machine_id[0] and status_active=1 and is_deleted=0  order by seq_no");
    }

    $count_feeding = "";
    foreach($feedingDataArr[$row[csf('program_id')]] as $feedingSequence=>$feedingData)
    {
        if($count_feeding =="")
        {
            $count_feeding = $feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
        }
        else
        {
            $count_feeding .= ",".$feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
        }
    }

    if ($knit_id_array[$row[csf('program_id')]] != "")
    {
        $all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
        $row_span = count($all_prod_id);
        $z = 0;

        foreach ($all_prod_id as $prod_id)
        {
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <?
                if ($z == 0)
                {
                    ?>
                    <td width="25" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
                    <td width="100" rowspan="<? echo $row_span; ?>" align="center" style="font-size:16px;">
                    <? echo change_date_format($row[csf('program_date')]); ?></td>
                    <td width="60" rowspan="<? echo $row_span; ?>" align="center" style="font-size:16px;">
                    <? echo $row[csf('program_id')]; ?></td>
                    <td width="60" rowspan="<? echo $row_span; ?>" align="center" style="font-size:16px;">
                    <? echo $requisiontNoArr[$row[csf('program_id')]]['requisition_no']; ?></td>
                    <td width="120" rowspan="<? echo $row_span; ?>" align="center">
                    <? echo $row[csf('batch_no')]; ?></td>
                    <td width="120" rowspan="<? echo $row_span; ?>" align="center">
                    <? echo $row[csf('tube_ref_no')]; ?></td>
                    <td width="120" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
                    <td width="50" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('gsm_weight')]; ?></p></th>
                    <td width="50" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
                    <td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>
                    <td width="60" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>
                    <td width="60" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $machine_no; ?></p></td>
                    <td width="70" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
                    <td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color; ?></p></td>
                    <td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
                    <td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('stitch_length')]; ?></p></td>
                    <td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
                    <td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
                    <td width="100" rowspan="<? echo $row_span; ?>"><p><? echo $count_feeding; ?></p></td>
                    <td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
                    <td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
                    <td width="70" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?></td>
                    <?
                    $tot_program_qnty += $row[csf('program_qnty')];
                        $i++;
                }
                ?>
                <td width="110"><p><? echo $product_details_array[$prod_id]['desc']; ?>&nbsp;</p></td>
                <td width="50" align="center"><p><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</p></td>
                <td width="70" align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id], 2, '.', ''); ?></td>
                <td width="70" align="right"><? echo $row[csf('no_of_ply')]; ?></td>
                <?
                if ($z == 0) {
                    ?>
                    <td rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                    <?
                }
                ?>
            </tr>
                <?
                $tot_yarn_reqsn_qnty += $prod_id_array[$row[csf('program_id')]][$prod_id];
                $z++;
            }
    }
    else
    {
    ?>
    <tr bgcolor="<? echo $bgcolor; ?>">
        <td width="25"><? echo $i; ?></td>
        <td width="100" align="center" style="font-size:16px;"><? echo change_date_format($row[csf('program_date')]); ?></td>
        <td width="60" align="center" style="font-size:16px;"><? echo $row[csf('program_id')]; ?></td>
        <td width="60" align="center" style="font-size:16px;"><? echo  $requisiontNoArr[$row[csf('program_id')]]['requisition_no'] ; ?></td>
        <td width="120" align="center"><? echo $row[csf('batch_no')]; ?></td>
        <td width="120" align="center"><? echo $row[csf('tube_ref_no')]; ?></td>
        <td width="120"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
        <td width="50" align="center"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
        <td width="50" align="center"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
        <td width="60"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>
        <td width="50" align="center"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>
        <td width="50" align="center"><p><? echo $machine_no; ?></p></td>
        <td width="50"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
        <td width="50"><p><? echo $color; ?></p></td>
        <td width="60"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
        <td width="60"><p><? echo $row_span; ?><p><? echo $row[csf('stitch_length')]; ?></p></td>
        <td width="60"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
        <td width="70"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
        <td width="100" rowspan="<? echo $row_span; ?>"><p><? echo $count_feeding; ?></p></td>
        <td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
        <td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
        <td width="70" align="right"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?></td>
        <td width="110"><p>&nbsp;</p></td>
        <td width="50"><p>&nbsp;</p></td>
        <td width="70" align="right">&nbsp;</td>
        <td width="70" align="right"><? echo $row[csf('no_of_ply')]; ?></td>
        <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
        </tr>
        <?
        $tot_program_qnty += $row[csf('program_qnty')];
        $i++;
    }
$advice = $row[csf('advice')];
}
?>
<tfoot>
    <th colspan="21" align="right"><b>Total</b></th>
    <th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?>&nbsp;</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>

    <th align="right"><? echo number_format($tot_yarn_reqsn_qnty, 2, '.', ''); ?></th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>

</tfoot>

</table>

<br>
<?


$sql_collarCuff = sql_select("select a.id, a.body_part_id, a.grey_size, a.finish_size, a.qty_pcs, b.color_id, a.dtls_id from ppl_planning_collar_cuff_dtls a, ppl_planning_info_entry_dtls b where a.dtls_id=b.id and  a.status_active=1 and a.is_deleted=0 and a.dtls_id in($program_ids) order by a.id");

// var_dump($sql_collarCuff);


$sqlRowCount= count($sql_collarCuff);

$progWiseDataArr = array();
$finishSizeChk = array();
$finishSizeArr = array();
foreach($sql_collarCuff as $row)
{
    $progWiseDataArr[$row[csf('dtls_id')]][$row[csf('body_part_id')]]['dtls_id']=$row[csf('dtls_id')];
    $progWiseDataArr[$row[csf('dtls_id')]][$row[csf('body_part_id')]]['body_part_id']=$row[csf('body_part_id')];
    $progWiseDataArr[$row[csf('dtls_id')]][$row[csf('body_part_id')]]['color_id']=$row[csf('color_id')];

    $qtyPcsArr[$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('finish_size')]]['qty_pcs'] +=$row[csf('qty_pcs')];

    if($finishSizeChk[$row[csf('finish_size')]] == "")
    {
        $finishSizeChk[$row[csf('finish_size')]] = $row[csf('finish_size')];
        array_push($finishSizeArr,$row[csf('finish_size')]);
    }
}
unset($sql_collarCuff);
// echo "<pre>";
// print_r($qtyPcsArr);
// echo "</pre>";

if($sqlRowCount>0)
{
    ?>
    <table style="margin-top:10px;" width="70%" border="1" rules="all" cellpadding="0" cellspacing="0"
    class="rpt_table">
        <thead>
            <tr>
                <th width="30" rowspan="3">SL</th>
                <th width="80" rowspan="3">Body Part</th>
                <th width="80" rowspan="3">Prog No</th>
                <th width="80" rowspan="3">Color</th>
                <th width="50" title="<? echo count($finishSizeArr);?>" colspan="<? echo count($finishSizeArr);?>">Finish Size</th>
                <th width="50" rowspan="3">G. Total</th>
            </tr>
            <tr>
                <th width="50" colspan="<? echo count($finishSizeArr);?>">Quantity Pcs</th>

            </tr>
            <tr>
                <?
                    foreach($finishSizeArr as $row)
                    { ?>
                        <th width="50"><? echo $row; ?></th>
                    <? }
                ?>

            </tr>
        </thead>
        <tbody>
            <?
            $i = 1;
            $total_qty_pcs = 0;
            $sizeQtyPcsArr = array();
            // var_dump($progWiseDataArr);
            foreach ($progWiseDataArr as $prog_id=>$prog_data)
            {
                foreach($prog_data as $b_part_id=>$val)
                {
                    // var_dump($prog_id);
                    ?>

                    <tr>
                        <td align="center"  valign='middle' style="text-align:center"><p><? echo $i; ?>&nbsp;</p></td>
                        <td  valign='middle' style="text-align:center"><p><? echo $body_part[$val['body_part_id']]; ?>&nbsp;</p></td>
                        <td title="<? echo $prog_id;?>"  valign='middle' style="text-align:center"><p><? echo $prog_id; ?>&nbsp;</p></td>
                        <td  valign='middle' style="text-align:center"><p><? echo $color_library[$val['color_id']]; ?>&nbsp;</p></td>
                        <?
                        $g_tot_qtyPcs = 0;
                        foreach($finishSizeArr as $row)
                        { ?>
                            <td style="padding-left:5px" align="right">
                                <? echo $qtyPcsArr[$prog_id][$val['body_part_id']][$row]['qty_pcs'];
                                $g_tot_qtyPcs +=$qtyPcsArr[$prog_id][$val['body_part_id']][$row]['qty_pcs'];
                                $sizeQtyPcsArr[$row] +=$qtyPcsArr[$prog_id][$val['body_part_id']][$row]['qty_pcs'];

                                ?>
                            </td>
                        <? }
                        ?>

                        <td align="right"><? echo number_format($g_tot_qtyPcs, 0);?></td>
                    </tr>

                    <?
                }

                $i++;
            }

                ?>

        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th align="right">Total:</th>

                <?
                    $mst_g_tot_qtyPcs=0;
                    foreach($finishSizeArr as $row)
                    { ?>
                        <th style="padding-left:5px" align="right">
                            <? echo $sizeQtyPcsArr[$row]; $mst_g_tot_qtyPcs+=$sizeQtyPcsArr[$row];

                            ?>
                        </th>
                    <? } ?>
                <th><? echo number_format($mst_g_tot_qtyPcs, 0);?></th>
            </tr>
        </tfoot>
    </table>

    <br>
    <?
}
$sql_fedder = sql_select("select a.id, a.color_id, a.stripe_color_id, a.no_of_feeder, max(b.measurement) as measurement, max(b.uom) as uom,a.dtls_id from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.dtls_id in($program_ids) and b.job_no in('".$sales_nos."') and a.no_of_feeder>0 group by a.id, a.color_id, a.stripe_color_id, a.no_of_feeder,a.dtls_id order by a.id,a.dtls_id");

$stripeDataArr = array();
foreach($sql_fedder as $row)
{
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['dtls_id']=$row[csf('dtls_id')];
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['color_id']=$row[csf('color_id')];
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['stripe_color_id']=$row[csf('stripe_color_id')];
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['measurement']=$row[csf('measurement')];
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['uom']=$row[csf('uom')];
        $stripeDataArr[$row[csf('dtls_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]][$row[csf('uom')]][$row[csf('no_of_feeder')]]['no_of_feeder']=$row[csf('no_of_feeder')];


}
foreach ($stripeDataArr as $prog_ids=>$prog_data)
{
    foreach($prog_data as $c_number_id=>$c_number_data)
    {
        foreach($c_number_data as $stripe_color_id=>$stripe_color_data)
        {
            foreach($stripe_color_data as $m_id=>$m_data)
            {
                foreach($m_data as $u_id=>$u_data)
                {
                    foreach($u_data as $row=>$val)
                    {
                        $s_prog_count[$prog_ids]++;
                    }
                }
            }
        }
    }
}
/* echo "<pre>";
print_r($s_prog_count);
echo "</pre>"; */

if (count($sql_fedder) > 0) {
    ?>
    <table style="margin-top:10px;" width="650" border="1" rules="all" cellpadding="0" cellspacing="0"
    class="rpt_table">
    <thead>
        <tr>
            <th width="50">SL</th>
            <th width="100">Program No</th>
            <th width="120">Color</th>
            <th width="120">Stripe Color</th>
            <th width="100">Measurement</th>
            <th width="100">UOM</th>
            <th>No Of Feeder</th>
        </tr>
    </thead>
    <tbody>
        <?
        $i = 1;
        $total_feeder = 0;
        foreach ($sql_fedder as $row) {
            if ($i % 2 == 0)
                $bgcolor = "#E9F3FF";
            else
                $bgcolor = "#FFFFFF";
            ?>
            <tr>

            <?
                $prog_ids=$row[csf('dtls_id')];
                $s_prog_span = $s_prog_count[$prog_ids];
                /*if(!in_array($prog_ids,$prog_chks))
                {
                    $prog_chks[]=$prog_ids;
                    ?>
                    <td align="center" rowspan="<? echo $s_prog_span;?>" valign='middle' style="text-align:center"><p><? echo $i; ?>&nbsp;</p></td>
                    <td align="center" rowspan="<? echo $s_prog_span;?>" valign='middle' style="text-align:center"><p><? echo $row[csf('dtls_id')]; ?>&nbsp;</p></td>
                    <td align="center" rowspan="<? echo $s_prog_span;?>" valign='middle' style="text-align:center"><p><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</p></td>
                    <?
                }*/
                ?>
            <!--    <td align="center"><p><? //echo $color_library[$row[csf('stripe_color_id')]]; ?>&nbsp;</p></td>
                <td align="center"><p><? //echo number_format($row[csf('measurement')], 2); ?>&nbsp;</p></td>
                <td align="center"><p><? //echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
                <td align="center"><p><? //echo number_format($row[csf('no_of_feeder')], 0);
                //$total_feeder += $row[csf('no_of_feeder')]; ?>&nbsp;</p></td>  -->

                <td align="center" style="text-align:center"><p><? echo $i; ?>&nbsp;</p></td>
                <td align="center" style="text-align:center"><p><? echo $row[csf('dtls_id')]; ?>&nbsp;</p></td>
                <td align="center" style="text-align:center"><p><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo $color_library[$row[csf('stripe_color_id')]]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo number_format($row[csf('measurement')], 2); ?>&nbsp;</p></td>
                <td align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo number_format($row[csf('no_of_feeder')], 0);
                $total_feeder += $row[csf('no_of_feeder')]; ?>&nbsp;</p></td>
            </tr>
            <?
            $i++;
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th align="right">Total:</th>
            <th style="text-align: center;"><? echo number_format($total_feeder, 0); ?></th>
        </tr>
    </tfoot>
</table>
<?
}

?>
<table border="1" rules="all" class="rpt_table">
    <tr>
        <td style="font-size:24px; font-weight:bold; width:20px;">ADVICE:</td>
        <td style="font-size:20px; width:100%;">     <? echo $advice; ?></td>
    </tr>
</table>
<!-- <div style="margin-top:60px; text-align: left;"><strong>Rate/Kg =</strong></div> -->
<br/>
<div style="float:left; width: 401px;">
    <div style="border:1px solid #000;">
    <table border="1" rules="all" class="rpt_table" width="400" height="200">
        <thead>
            <th colspan="2" style="font-size:20px; font-weight:bold;">Please Strictly Avoid The Following Faults….</th>
        <thead>
        <tbody>
            <tr>
                <td style="width:190px; font-size:14px;"><b> 1.</b> Patta</td>
                <td style="font-size:14px;"><b> 8.</b> Sinker mark</td>
            </tr>
            <tr>
                <td style="font-size:14px;"><b> 2.</b> Loop</td>
                <td style="font-size:14px;"><b> 9.</b> Needle mark</td>
            </tr>
            <tr>
                <td style="font-size:14px;"><b> 3.</b> Hole</td>
                <td style="font-size:14px;"><b> 10.</b> Oil mark</td>
            </tr>
            <tr>
                <td><b> 4.</b> Star marks</td>
                <td><b> 11.</b> Dia mark/Crease Mark</td>
            </tr>
            <tr>
                <td style="font-size:14px;"><b> 5.</b> Barre</td>
                <td style="font-size:14px;"><b> 12.</b> Wheel Free</td>
            </tr>
            <tr>
                <td style="font-size:14px;"><b> 6.</b> Drop Stitch</td>
                <td style="font-size:14px;"><b> 13.</b> Slub</td>
            </tr>
            <tr>
                <td style="font-size:14px;"><b> 7.</b> Lot mixing</td>
                <td style="font-size:14px;"><b> 14.</b> Other contamination</td>
            </tr>
        </tbody>
    </table>
    </div>
    <br>
    <div>
    <p>বিঃ দ্রঃ<br>
    * প্রোগ্রামের শেষ পর্যায়ে অবশ্যই সূতা মিল করে চালাতে হবে।<br>
    * সূতা মেশিনে উঠানোর সময় ১/১ করে উঠতে হবে।<br>
    * রোল মার্কিং সঠিকভাবে করতে হবে।<br>
    * রোলে কোন প্রকার কাটা-কাটি বা ভুল লেখা চলবেনা।<br>
    * রোলের গায়ে জব, কালার, মেশিন নম্বর, তারিখ এবং শিফট অবশ্যই লিখতে হবে।
    </p>
    </div>
</div>
<div style="float:right; border:1px solid #000;">
    <table border="1" rules="all" class="rpt_table" width="400" height="150">
        <thead>
            <th colspan="2" style="font-size:18px; font-weight:bold;">Please Mark The Role The Each Role as
                Follows
            </th>
        <thead>
        <tr>
            <td width="200" style="font-size:14px;"><b> 1.</b> Manufacturing Factory Name</td>
            <td style="font-size:14px;"><b> 6.</b> Fabrics Type</td>
        </tr>
        <tr>
            <td style="font-size:14px;"><b> 2.</b> Prog. Company Name</td>
            <td style="font-size:14px;"><b> 7.</b> Finished Dia</td>
        </tr>
        <tr>
            <td style="font-size:14px;"><b> 3.</b> Buyer, Style,Order no.</td>
            <td style="font-size:14px;"><b> 8.</b> Finished Gsm & Color</td>
        </tr>
        <tr>
            <td style="font-size:14px;"><b> 4.</b> Yarn Count, Lot & Brand</td>
            <td style="font-size:14px;"><b> 9.</b> Yarn Composition</td>
        </tr>
        <tr>
            <td style="font-size:14px;"><b> 5.</b> M/C No., Dia, Stitch Length</td>
            <td style="font-size:14px;"><b> 10.</b> Knit Program No</td>
        </tr>
    </table>
</div>
<?
echo signature_table(213, $company_id, "1180px",1);
?>
</div>
<?
exit();
}

//knitting_card_print_1
if ($action == "knitting_card_print_1")
{
    echo load_html_head_contents("Knitting Card Info", "../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $program_ids =  $data;

    if(!$program_ids)
    {
        echo "Program is not found . ";
        die;
    }

    $sub_subcontract = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
    $brand_arr      = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
    $company_arr    = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $imge_arr       = return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    $count_arr      = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 ", 'id', 'yarn_count');
    $color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
    $floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');

    $feeder = array(1 => "Full Feeder", 2 => "Half Feeder");

    $machineId_arr=array();
    $sql_mc=sql_select("select id, machine_no, floor_id from lib_machine_name");
    foreach( $sql_mc as $row)
    {
        $machineId_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
    }
    unset($sql_mc);

    if ($db_type == 0)
        $item_id_cond="group_concat(distinct(b.item_id))";
    else if ($db_type==2)
        $item_id_cond="LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id)";

    $result_machin_prog = sql_select("SELECT machine_id,dtls_id,distribution_qnty from ppl_planning_info_machine_dtls WHERE DTLS_ID IN($program_ids)");
    $machin_prog = array();
    foreach ($result_machin_prog as $row)
    {
        $machin_prog[$row[csf('machine_id')]][$row[csf('dtls_id')]]['distribution_qnty'] = $row[csf('distribution_qnty')];
    }

    $reqsDataArr = array();
    $program_cond2 = ($program_ids) ? " and knit_id in(".$program_ids.")" : "";
    if ($db_type == 0)
    {
        $reqsData = sql_select("select knit_id, requisition_no as reqs_no, group_concat(distinct(prod_id)) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id");
    }
    else
    {
        $reqsData = sql_select("select knit_id, max(requisition_no) as reqs_no, LISTAGG(prod_id, ',') WITHIN GROUP (ORDER BY prod_id) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id,requisition_no");
    }

    foreach ($reqsData as $row)
    {
        $reqsDataArr[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
        $reqsDataArr[$row[csf('knit_id')]]['prod_id'] = $row[csf('prod_id')];
        $prod_arr[] = $row[csf('prod_id')];
    }
    unset($reqsData);

    if(!empty($prod_arr))
    {
        $product_details_arr = array();
        $procuct_cond = (!empty($prod_arr))?" and id in(".implode(",",$prod_arr).")":"";
        $pro_sql = sql_select("select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 $procuct_cond");
        foreach ($pro_sql as $row)
        {
            $compos = '';
            if ($row[csf('yarn_comp_percent2nd')] != 0)
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
            }
            else
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
            }

            $product_details_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
            $product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
            $product_details_arr[$row[csf('id')]]['brand_name'] = $row[csf('brand')];
            //$product_details_arr[$row[csf('id')]]['supplier'] = $row[csf('supplier_id')];
            $yarn_details_arr[$row[csf('id')]]['yarn_count'] = $count_arr[$row[csf('yarn_count_id')]];
            $yarn_details_arr[$row[csf('id')]]['yarn_type'] = $yarn_type[$row[csf('yarn_type')]];
            $yarn_details_arr[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
            $yarn_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
            $yarn_details_arr[$row[csf('id')]]['composition'] = $compos;
            $yarn_details_arr[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
        }
        unset($pro_sql);
    }
    //echo "<pre>";
    //print_r($yarn_details_arr);
    //ppl_planning_info_entry_dtls

    $data_sql="SELECT a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id,a.location_id as location1, c.location_id as location2, c.job_no, b.color_type_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b left join fabric_sales_order_mst c on c.id=b.po_id and c.status_active=1 and c.is_deleted=0 where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id, a.location_id, c.location_id, c.job_no, b.color_type_id";
    //, b.yarn_desc
    //echo $data_sql;
    $dataArray = sql_select($data_sql);
    $bookingNoArr = array();
    //$progNoArr = array();
    foreach ($dataArray as $row)
    {
        //for booking no
        $bookingNoArr[$row[csf('booking_no')]] = $row[csf('booking_no')];

        //for prog no
        //$progNoArr[$row[csf('id')]] = $row[csf('id')];
    }

    //for booking qty
    $booking_qnty_arr = array();
    $sql_data = sql_select("select a.booking_no, a.buyer_id, sum(b.grey_fab_qnty ) as grey_fab_qnty, a.quality_level from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.fabric_source=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0".where_con_using_array($bookingNoArr, '1', 'a.booking_no')." group by a.booking_no, a.buyer_id, a.quality_level");
    foreach ($sql_data as $row)
    {
        $booking_qnty_arr[$row[csf('booking_no')]]['qty'] += $row[csf('grey_fab_qnty')];
        $booking_qnty_arr[$row[csf('booking_no')]]['buyer'] += $row[csf('buyer_id')];
        //$order_nature_booking_arr[$row[csf('booking_no')]]= $row[csf('quality_level')];
    }
    unset($sql_data);

    //for int. ref.
    $sqlBooking = "SELECT a.grouping AS GROUPING, b.booking_no AS BOOKING_NO FROM wo_po_break_down a, wo_booking_dtls b where a.id = b.po_break_down_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0".where_con_using_array($bookingNoArr, '1', 'b.booking_no')." GROUP BY a.grouping, b.booking_no";
    //echo $sqlBooking;
    $sqlBookingRslt = sql_select($sqlBooking);
    $bookingInfoArr = array();
    foreach($sqlBookingRslt as $row)
    {
        $bookingInfoArr[$row['BOOKING_NO']]['int_ref'] = $row['GROUPING'];
    }
    unset($sqlBookingRslt);

    //for po buyer
    $sqlPoBuyer = sql_select("select sales_booking_no AS BOOKING_NO, po_buyer AS BUYER from fabric_sales_order_mst where status_active = 1 AND is_deleted = 0".where_con_using_array($bookingNoArr, '1', 'sales_booking_no'));
    $poBuyerArr = array();
    foreach($sqlPoBuyer as $row)
    {
        $poBuyerArr[$row['BOOKING_NO']] = $row['BUYER'];
    }
    unset($sqlPoBuyer);

    $company_id = '';
    $orderNo = "";
    $knitting_factory = '';
    $location = '';
    $program_data_arr=array();
    foreach ($dataArray as $row)
    {
        $knitting_factory='';
        if ($row[csf('knitting_source')] == 1)
            $knitting_factory = $company_arr[$row[csf('knitting_party')]] . ",";
        else if ($row[csf('knitting_source')] == 3)
            $knitting_factory = $supplier_details[$row[csf('knitting_party')]] . ",";

        if($row[csf('knitting_source')] == 1)
        {
            $location = return_field_value("location_name", "lib_location", "id='" . $row[csf('location1')] . "'");
        }
        else if($row[csf('knitting_source')] == 3)
        {
            $location = return_field_value("location_name", "lib_location", "id='" . $row[csf('location2')] . "'");
        }

        $yarn_desc='';
        $lot_no="";
        $brand_name="";
        $yarn_dtls="";
        if($orderNo=="")
        {
            $orderNo .= $row[csf('po_id')];
            $po_number .= $po_details[$row[csf('po_id')]]['po_number'];
        }
        else
        {
            $orderNo .= ",".$row[csf('po_id')];
            $po_number .= ",".$po_details[$row[csf('po_id')]]['po_number'];
        }

        if($reqsDataArr[$row[csf('id')]]['prod_id'] != '')
        {
            $prod_id = array_unique(explode(",", $reqsDataArr[$row[csf('id')]]['prod_id']));
            foreach ($prod_id as $val)
            {
                $yarn_desc .= $product_details_arr[$val]['desc'] . ", ";
                $lot_no .= $product_details_arr[$val]['lot'] . ", ";

                $brand_name .= $brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . ", ";
                //$yarn_dtls .= $product_details_arr[$val]['desc'] . ",".$product_details_arr[$val]['lot'] . ",".$brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . "<br>";
                $yarn_dtls .= $yarn_details_arr[$val]['yarn_count'] . ", ".$yarn_details_arr[$val]['composition'] . ", ".$yarn_details_arr[$val]['yarn_type'].", ".$yarn_details_arr[$val]['color'].', '.$yarn_details_arr[$val]['brand'].", ".$yarn_details_arr[$val]['lot'] . "<br>";
            }

            $yarn_desc = implode(",",array_filter(array_unique(explode(",", substr($yarn_desc, 0, -1)))));
            $lot_no = implode(",",array_filter(array_unique(explode(",", substr($lot_no, 0, -1)))));
            $brand_name = implode(",",array_filter(array_unique(explode(",", substr($brand_name, 0, -1)))));
        }
        $ex_mc_id=array_unique(explode(",",$row[csf('machine_id')]));

        /*$machine_name="";
        foreach($ex_mc_id as $mc_id)
        {
            if($machine_name=='') $machine_name=$machine_arr[$mc_id]; else $machine_name.=','.$machine_arr[$mc_id];
        }*/

        //for color
        $color_name="";
        $ex_color_id=array_unique(explode(",",$row[csf('color_id')]));
        foreach($ex_color_id as $color_id)
        {
            if($color_name=='')
                $color_name=$color_library[$color_id];
            else
                $color_name.=', '.$color_library[$color_id];
        }

        $program_data_arr[$row[csf('id')]]['po_number']=$po_number;
        $program_data_arr[$row[csf('id')]]['co_efficient']=$row[csf('co_efficient')];
        $program_data_arr[$row[csf('id')]]['draft_ratio']=$row[csf('draft_ratio')];
        $program_data_arr[$row[csf('id')]]['start_date']=$row[csf('start_date')];
        $program_data_arr[$row[csf('id')]]['end_date']=$row[csf('end_date')];
        $program_data_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
        $program_data_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
        $program_data_arr[$row[csf('id')]]['color_id']=$color_name;
        //$program_data_arr[$row[csf('id')]]['prog_qty']+=$row[csf('program_qnty')];
        $program_data_arr[$row[csf('id')]]['prog_qty']=$row[csf('program_qnty')];
        $program_data_arr[$row[csf('id')]]['s_length']=$row[csf('stitch_length')];
        $program_data_arr[$row[csf('id')]]['fabric_dia']=$row[csf('fabric_dia')];
        $program_data_arr[$row[csf('id')]]['program_date']=$row[csf('program_date')];
        $program_data_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
        $program_data_arr[$row[csf('id')]]['booking_qty']=$booking_qnty_arr[$row[csf('booking_no')]]['qty'];
        $program_data_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];

        $program_data_arr[$row[csf('id')]]['yarn_dtls']= $yarn_dtls;
        $program_data_arr[$row[csf('id')]]['yarn_desc']= $yarn_desc;
        $program_data_arr[$row[csf('id')]]['lot']= $lot_no;
        $program_data_arr[$row[csf('id')]]['brand_name']= $brand_name;
        $program_data_arr[$row[csf('id')]]['mc_nmae']= $machine_name;
        $program_data_arr[$row[csf('id')]]['knit_factory']= $knitting_factory;
        $program_data_arr[$row[csf('id')]]['sub_party']= $supplier_details[$row[csf('subcontract_party')]];
        $program_data_arr[$row[csf('id')]]['gsm_weight']=$row[csf('gsm_weight')];
        $program_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
        $program_data_arr[$row[csf('id')]]['location']= $location;
        $program_data_arr[$row[csf('id')]]['job_no']= $row[csf('job_no')];
        $program_data_arr[$row[csf('id')]]['color_type_id']= $row[csf('color_type_id')];

        //for buyer
        //$program_data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
        if($booking_qnty_arr[$row[csf('booking_no')]]['buyer'] != '' && $booking_qnty_arr[$row[csf('booking_no')]]['buyer'] != 0)
        {
            $program_data_arr[$row[csf('id')]]['buyer_id']=$booking_qnty_arr[$row[csf('booking_no')]]['buyer'];
        }
        else
        {
            $program_data_arr[$row[csf('id')]]['buyer_id']=$poBuyerArr[$row[csf('booking_no')]];
        }

        $program_data_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
        $program_data_arr[$row[csf('id')]]['spandex_stitch_length']=$row[csf('spandex_stitch_length')];
        $program_data_arr[$row[csf('id')]]['feeder']=$row[csf('feeder')];
        $program_data_arr[$row[csf('id')]]['advice']=$row[csf('advice')];
        $program_data_arr[$row[csf('id')]]['knit_id']=$row[csf('mst_id')];
        $program_data_arr[$row[csf('id')]]['width_dia_type']=$row[csf('width_dia_type')];
        $program_data_arr[$row[csf('id')]]['int_ref']=$bookingInfoArr[$row[csf('booking_no')]]['int_ref'];
    }
    unset($dataArray);


    $inc_no=1;
    foreach($ex_mc_id as $mc_id)
    {
        if ($floor_id_all == '') $floor_id_all = $machineId_arr[$mc_id]['floor_id']; else $floor_id_all .= "," . $machineId_arr[$mc_id]['floor_id'];


        $floor_name="";
        $floor_ids = array_filter(array_unique(explode(",", $floor_id_all)));
        // var_dump($floor_ids);
        foreach ($floor_ids as $ids) {
            if ($floor_name == '') $floor_name = $floor_arr[$ids]; else $floor_name .= "," . $floor_arr[$ids];
        }
        //var_dump($floor_name);

        // program array loop
        foreach($program_data_arr as $prog_no=>$prog_data)
        {

        ?>
        <style type="text/css">
            .page_break { page-break-after: always;
            }
            #font_size_define{
                font-size:14px;
                font-family:'Arial Narrow';
            }
            .font_size_define{
                font-size:14px;
                font-family:'Arial Narrow';
            }
            #dataTable tbody tr span{
                 opacity:0.2;
                 color:gray;
            }
            #dataTable tbody tr{
                vertical-align:middle;
            }
        </style>
        <div style="width:650px;">
            <!--<table width="100%" cellpadding="0" cellspacing="0">-->
            <table width="100%" cellspacing="2" cellpadding="2" border="1" rules="all" class="rpt_table" style="margin-left:60px">
                <tr>
                    <td width="150" align="right" style="border-left:hidden; border-top:hidden; border-right:hidden; border-bottom:hidden;padding-top:10px">
                        <img src='../../<? echo $imge_arr[str_replace("'","",$prog_data['company_id'])]; ?>' height='100%' width='100%'  />
                    </td>
                    <td colspan="1" width="455" align="center" valign="middle" style="font-size:16px;border-left:hidden; border-top:hidden; border-right:hidden; border-bottom:hidden;" ><b style="font-size:22px"><? echo $company_arr[$prog_data['company_id']]; ?></b><br><? echo $prog_data['location']; ?></td>
                    <td style="border-left:hidden;border-right:hidden; border-top:hidden; border-bottom:hidden; width:300px;">
                        <span  id="barcode_img_id_<? echo $inc_no;?>" ></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="border-left:hidden; border-right:hidden;">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3" width="100%" align="center" style="font-size:20px;border-left:hidden; border-top:hidden; border-right:hidden;"><b>KNIT CARD</b></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>PROGRAM NO</b></td>
                    <td width="350" class="font_size_define"><b><? echo $prog_no; ?></b></td>
                    <td width="105" class="font_size_define" style="border-left:hidden;"><b>Date : <? echo date('d-m-Y', strtotime($prog_data['program_date'])); ?></b></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>KNIT PARTY</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['knit_factory']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>BUYER</b></td>
                    <td colspan="2" class="font_size_define"><? echo $buyer_arr[$prog_data['buyer_id']]; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>BOOKING NO</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['booking_no']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>Textile REF. No</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['job_no']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>Floor Name</b></td>
                    <td colspan="2" class="font_size_define"><? echo $floor_name; ?></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>M/C NO</b></td>
                    <td colspan="2" class="font_size_define"><? echo $machine_arr[$mc_id];?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>M/C DIA & GG</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['machine_dia']." x ".$prog_data['machine_gg']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>F. DIA</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['fabric_dia']." "."[".$fabric_typee[$prog_data['width_dia_type']]."]"; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>F. TYPE</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['fabric_desc']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>Feeder</b></td>
                    <td colspan="2" class="font_size_define"><? echo $feeder[$prog_data['feeder']]; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>Color TYPE</b></td>
                    <td colspan="2" class="font_size_define"><? echo $color_type[$prog_data['color_type_id']]; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>GSM</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['gsm_weight']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>COUNT</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['yarn_dtls']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>LOT</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['lot']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>BRAND</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['brand_name']; ?></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>SL</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['s_length']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>Spandex SL</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['spandex_stitch_length']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>COLOR</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['color_id']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>P. QTY. (Kg)</b></td>
                    <td colspan="2" class="font_size_define"><? echo number_format($prog_data['prog_qty'],2);?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>M/C Distrb. Qty</b></td>
                    <td colspan="2" class="font_size_define"><? echo number_format($machin_prog[$mc_id][$prog_no]['distribution_qnty'],2); ?></td>
                </tr>
            </table>
            <? echo signature_table(119, $prog_data['company_id'], "700px"); ?>
            <div class="page_break">&nbsp;</div>
        </div>
        <div>
            <script type="text/javascript" src="../../js/jquery.js"></script>
            <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
            <script>
                function generateBarcode( valuess,id ){
                    var value = valuess;//$("#barcodeValue").val();
                    var btype = 'code39';//$("input[name=btype]:checked").val();
                    var renderer ='bmp';// $("input[name=renderer]:checked").val();

                    var settings = {
                      output:renderer,
                      bgColor: '#FFFFFF',
                      color: '#000000',
                      barWidth: 2,
                      barHeight: 30,
                      moduleSize:5,
                      posX: 10,
                      posY: 20,
                      addQuietZone: 1
                    };
                    $("#barcode_img_id_<? echo $inc_no; ?>").html('11');
                     value = {code:value, rect: false};

                    $("#barcode_img_id_<? echo $inc_no; ?>").barcode(value, btype, settings);
                }
                generateBarcode('<? echo $prog_no; ?>');
             </script>
        </div>
        <?
        }
        $inc_no++;
    }
    exit();
}

//knitting_card_print_7
if ($action == "knitting_card_print_7")
{
    echo load_html_head_contents("Knitting Card Info", "../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $program_ids =  $data;

    if(!$program_ids)
    {
        echo "Program is not found . ";
        die;
    }

    $sub_subcontract = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
    $brand_arr      = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
    $company_arr    = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $imge_arr       = return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    $count_arr      = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 ", 'id', 'yarn_count');
    $color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
    $yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by id, yarn_count","id","yarn_count");
    $lib_fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where status_active=1", "id", "fabric_composition_name");

    if ($db_type == 0)
        $item_id_cond="group_concat(distinct(b.item_id))";
    else if ($db_type==2)
        $item_id_cond="LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id)";

    $result_machin_prog = sql_select("SELECT machine_id,dtls_id,distribution_qnty from ppl_planning_info_machine_dtls WHERE DTLS_ID IN($program_ids)");
    $machin_prog = array();
    foreach ($result_machin_prog as $row)
    {
        $machin_prog[$row[csf('machine_id')]][$row[csf('dtls_id')]]['distribution_qnty'] = $row[csf('distribution_qnty')];
    }

    $reqsDataArr = array();
    $program_cond2 = ($program_ids) ? " and knit_id in(".$program_ids.")" : "";
    if ($db_type == 0)
    {
        $reqsData = sql_select("select knit_id, requisition_no as reqs_no, group_concat(distinct(prod_id)) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id");
    }
    else
    {
        $reqsData = sql_select("select knit_id, max(requisition_no) as reqs_no, LISTAGG(prod_id, ',') WITHIN GROUP (ORDER BY prod_id) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id,requisition_no");
    }

    foreach ($reqsData as $row)
    {
        $reqsDataArr[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
        $reqsDataArr[$row[csf('knit_id')]]['prod_id'] = $row[csf('prod_id')];
        $prod_arr[] = $row[csf('prod_id')];
    }
    unset($reqsData);

    if(!empty($prod_arr))
    {
        $product_details_arr = array();
        $procuct_cond = (!empty($prod_arr))?" and id in(".implode(",",$prod_arr).")":"";
        $pro_sql = sql_select("select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 $procuct_cond");
        foreach ($pro_sql as $row)
        {
            $compos = '';
            if ($row[csf('yarn_comp_percent2nd')] != 0)
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
            }
            else
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
            }

            $product_details_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
            $product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
            $product_details_arr[$row[csf('id')]]['brand_name'] = $row[csf('brand')];
            //$product_details_arr[$row[csf('id')]]['supplier'] = $row[csf('supplier_id')];
            $yarn_details_arr[$row[csf('id')]]['yarn_count'] = $count_arr[$row[csf('yarn_count_id')]];
            $yarn_details_arr[$row[csf('id')]]['yarn_type'] = $yarn_type[$row[csf('yarn_type')]];
            $yarn_details_arr[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
            $yarn_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
            $yarn_details_arr[$row[csf('id')]]['composition'] = $compos;
            $yarn_details_arr[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
        }
        unset($pro_sql);
    }
    //echo "<pre>";
    //print_r($yarn_details_arr);

    $data_sql="SELECT a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id,b.determination_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id,b.determination_id";
    //, b.yarn_desc
    //echo $data_sql;
    $dataArray = sql_select($data_sql);
    $bookingNoArr = array();
    $determination_id_arr = array();
    //$progNoArr = array();
    foreach ($dataArray as $row)
    {
        //for booking no
        $bookingNoArr[$row[csf('booking_no')]] = $row[csf('booking_no')];
        array_push($determination_id_arr,$row[csf('determination_id')]);
        //for prog no
        //$progNoArr[$row[csf('id')]] = $row[csf('id')];
    }

    if(!empty($determination_id_arr))
    {
        $rslt_f_deter=sql_select("SELECT id,construction, fabric_composition_id from lib_yarn_count_determina_mst where status_active=1 and is_deleted=0 ".where_con_using_array($determination_id_arr,0,'id')." ");

        $fabric_info_arr= array();
        foreach($rslt_f_deter as $row)
        {
            $fabric_info_arr[$row[csf("id")]]['f_type']=$row[csf("construction")].','.$lib_fabric_composition[$row[csf("fabric_composition_id")]];
        }
        unset($rslt_deter);
        //echo "<pre>";print_r($fabric_info_arr);

        $rslt_y_deter=sql_select("SELECT mst_id, copmposition_id, percent,count_id, type_id, yarn_rate from lib_yarn_count_determina_dtls where status_active=1 and is_deleted=0 ".where_con_using_array($determination_id_arr,0,'mst_id')."");
        $yarn_info_arr= array();
        foreach($rslt_y_deter as $row)
        {
            $yarn_info_arr[$row[csf("mst_id")]]['y_comp'] .=$composition[$row[csf("copmposition_id")]].',';
        }
        unset($rslt_y_deter);
        //echo "<pre>";print_r($yarn_info_arr);
    }

    //for booking qty
    $booking_qnty_arr = array();
    $sql_data = sql_select("select a.booking_no, a.buyer_id, sum(b.grey_fab_qnty ) as grey_fab_qnty, a.quality_level from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.fabric_source=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0".where_con_using_array($bookingNoArr, '1', 'a.booking_no')." group by a.booking_no, a.buyer_id, a.quality_level");
    foreach ($sql_data as $row)
    {
        $booking_qnty_arr[$row[csf('booking_no')]]['qty'] += $row[csf('grey_fab_qnty')];
        $booking_qnty_arr[$row[csf('booking_no')]]['buyer'] += $row[csf('buyer_id')];
        //$order_nature_booking_arr[$row[csf('booking_no')]]= $row[csf('quality_level')];
    }
    unset($sql_data);

    //for int. ref.
    $sqlBooking = "SELECT a.grouping AS GROUPING, b.booking_no AS BOOKING_NO FROM wo_po_break_down a, wo_booking_dtls b where a.id = b.po_break_down_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0".where_con_using_array($bookingNoArr, '1', 'b.booking_no')." GROUP BY a.grouping, b.booking_no";
    //echo $sqlBooking;
    $sqlBookingRslt = sql_select($sqlBooking);
    $bookingInfoArr = array();
    foreach($sqlBookingRslt as $row)
    {
        if($row['GROUPING']!="")
        {
          $bookingInfoArr[$row['BOOKING_NO']]['int_ref'] = $row['GROUPING'];
        }
    }
    unset($sqlBookingRslt);

    //for po buyer
    $sqlPoBuyer = sql_select("select sales_booking_no AS BOOKING_NO, po_buyer AS BUYER from fabric_sales_order_mst where status_active = 1 AND is_deleted = 0".where_con_using_array($bookingNoArr, '1', 'sales_booking_no'));
    $poBuyerArr = array();
    foreach($sqlPoBuyer as $row)
    {
        $poBuyerArr[$row['BOOKING_NO']] = $row['BUYER'];
    }
    unset($sqlPoBuyer);

    $company_id = '';
    $orderNo = "";
    $knitting_factory = '';
    $program_data_arr=array();
    foreach ($dataArray as $row)
    {
        $knitting_factory='';
        if ($row[csf('knitting_source')] == 1)
            $knitting_factory = $company_arr[$row[csf('knitting_party')]] . ",";
        else if ($row[csf('knitting_source')] == 3)
            $knitting_factory = $supplier_details[$row[csf('knitting_party')]] . ",";

        $yarn_desc='';
        $lot_no="";
        $brand_name="";
        $yarn_dtls="";
        $yarn_composition="";
        $yarn_types="";
        if($orderNo=="")
        {
            $orderNo .= $row[csf('po_id')];
            $po_number .= $po_details[$row[csf('po_id')]]['po_number'];
        }
        else
        {
            $orderNo .= ",".$row[csf('po_id')];
            $po_number .= ",".$po_details[$row[csf('po_id')]]['po_number'];
        }

        if($reqsDataArr[$row[csf('id')]]['prod_id'] != '')
        {
            $prod_id = array_unique(explode(",", $reqsDataArr[$row[csf('id')]]['prod_id']));
            foreach ($prod_id as $val)
            {
                $yarn_desc .= $product_details_arr[$val]['desc'] . ", ";
                $lot_no .= $product_details_arr[$val]['lot'] . ", ";

                //$brand_name .= $brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . ", ";
                $brand_name .= $brand_arr[$product_details_arr[$val]['brand_name']]. ", ";
                //$yarn_dtls .= $product_details_arr[$val]['desc'] . ",".$product_details_arr[$val]['lot'] . ",".$brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . "<br>";
                //$yarn_dtls .= $yarn_details_arr[$val]['yarn_count'] . ", ".$yarn_details_arr[$val]['composition'] . ", ".$yarn_details_arr[$val]['yarn_type'].", ".$yarn_details_arr[$val]['color'].', '.$yarn_details_arr[$val]['brand'].", ".$yarn_details_arr[$val]['lot'] . "<br>";
                //$yarn_dtls .= $yarn_details_arr[$val]['yarn_count']. "<br>";
                $yarn_dtls .= $yarn_details_arr[$val]['yarn_count'] ." ". $yarn_details_arr[$val]['composition']  ." ". $yarn_details_arr[$val]['yarn_type']. "<br>";
                $yarn_types .=$yarn_details_arr[$val]['yarn_type']. "<br>";
                $yarn_composition .= $yarn_details_arr[$val]['composition']. "<br>";
            }

            $yarn_desc = implode(",",array_filter(array_unique(explode(",", substr($yarn_desc, 0, -1)))));
            $lot_no = implode(",",array_filter(array_unique(explode(",", substr($lot_no, 0, -1)))));
            $brand_name = implode(",",array_filter(array_unique(explode(",", substr($brand_name, 0, -1)))));
        }
        $ex_mc_id=array_unique(explode(",",$row[csf('machine_id')]));

        /*$machine_name="";
        foreach($ex_mc_id as $mc_id)
        {
            if($machine_name=='') $machine_name=$machine_arr[$mc_id]; else $machine_name.=','.$machine_arr[$mc_id];
        }*/

        //for color
        $color_name="";
        $ex_color_id=array_unique(explode(",",$row[csf('color_id')]));
        foreach($ex_color_id as $color_id)
        {
            if($color_name=='')
                $color_name=$color_library[$color_id];
            else
                $color_name.=', '.$color_library[$color_id];
        }

        $program_data_arr[$row[csf('id')]]['po_number']=$po_number;
        $program_data_arr[$row[csf('id')]]['co_efficient']=$row[csf('co_efficient')];
        $program_data_arr[$row[csf('id')]]['draft_ratio']=$row[csf('draft_ratio')];
        $program_data_arr[$row[csf('id')]]['start_date']=$row[csf('start_date')];
        $program_data_arr[$row[csf('id')]]['end_date']=$row[csf('end_date')];
        $program_data_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
        $program_data_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
        $program_data_arr[$row[csf('id')]]['color_id']=$color_name;
        //$program_data_arr[$row[csf('id')]]['prog_qty']+=$row[csf('program_qnty')];
        $program_data_arr[$row[csf('id')]]['prog_qty']=$row[csf('program_qnty')];
        $program_data_arr[$row[csf('id')]]['s_length']=$row[csf('stitch_length')];
        $program_data_arr[$row[csf('id')]]['fabric_dia']=$row[csf('fabric_dia')];
        $program_data_arr[$row[csf('id')]]['program_date']=$row[csf('program_date')];
        $program_data_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
        $program_data_arr[$row[csf('id')]]['booking_qty']=$booking_qnty_arr[$row[csf('booking_no')]]['qty'];
        $program_data_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];

        $program_data_arr[$row[csf('id')]]['yarn_dtls']= $yarn_dtls;
        $program_data_arr[$row[csf('id')]]['yarn_types']= $yarn_types;
        $program_data_arr[$row[csf('id')]]['yarn_desc']= $yarn_desc;
        $program_data_arr[$row[csf('id')]]['lot']= $lot_no;
        $program_data_arr[$row[csf('id')]]['yarn_composition']= $yarn_composition;
        $program_data_arr[$row[csf('id')]]['brand_name']= $brand_name;
        $program_data_arr[$row[csf('id')]]['mc_nmae']= $machine_name;
        $program_data_arr[$row[csf('id')]]['knit_factory']= $knitting_factory;
        $program_data_arr[$row[csf('id')]]['sub_party']= $supplier_details[$row[csf('subcontract_party')]];
        $program_data_arr[$row[csf('id')]]['gsm_weight']=$row[csf('gsm_weight')];
        $program_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
        $program_data_arr[$row[csf('id')]]['determination_id']=$row[csf('determination_id')];

        //for buyer
        //$program_data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
        if($booking_qnty_arr[$row[csf('booking_no')]]['buyer'] != '' && $booking_qnty_arr[$row[csf('booking_no')]]['buyer'] != 0)
        {
            $program_data_arr[$row[csf('id')]]['buyer_id']=$booking_qnty_arr[$row[csf('booking_no')]]['buyer'];
        }
        else
        {
            $program_data_arr[$row[csf('id')]]['buyer_id']=$poBuyerArr[$row[csf('booking_no')]];
        }

        $program_data_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
        $program_data_arr[$row[csf('id')]]['spandex_stitch_length']=$row[csf('spandex_stitch_length')];
        $program_data_arr[$row[csf('id')]]['feeder']=$row[csf('feeder')];
        $program_data_arr[$row[csf('id')]]['advice']=$row[csf('advice')];
        $program_data_arr[$row[csf('id')]]['knit_id']=$row[csf('mst_id')];
        $program_data_arr[$row[csf('id')]]['width_dia_type']=$row[csf('width_dia_type')];
        $program_data_arr[$row[csf('id')]]['int_ref']=$bookingInfoArr[$row[csf('booking_no')]]['int_ref'];
    }
    unset($dataArray);
    //echo "<pre>";print_r($program_data_arr);

    if($program_ids!="")
    {
        // echo "SELECT DTLS_ID, SEQ_NO, COUNT_ID, FEEDING_ID FROM PPL_PLANNING_COUNT_FEED_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0";
        $feedingResult =  sql_select("SELECT DTLS_ID, SEQ_NO, COUNT_ID, FEEDING_ID FROM PPL_PLANNING_COUNT_FEED_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0");

        $feedingDataArr = array();
        foreach ($feedingResult as $row)
        {
            $feedingSequence[$row['SEQ_NO']] = $row['SEQ_NO'];
            $feedingDataArr[$row['DTLS_ID']][$row['SEQ_NO']]['count_id'] = $row['COUNT_ID'];
            $feedingDataArr[$row['DTLS_ID']][$row['SEQ_NO']]['feeding_id'] = $row['FEEDING_ID'];
        }
    }

    foreach($ex_mc_id as $mc_id)
    {
        // program array loop
        foreach($program_data_arr as $prog_no=>$prog_data)
        {
            $count_feeding = "";
            foreach($feedingDataArr[$prog_no] as $feedingSequence=>$feedingData)
            {
                if($count_feeding =="")
                {
                    $count_feeding = $feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
                }
                else
                {
                    $count_feeding .= ",".$feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
                }
            }


        $company_id=$prog_data['company_id'];



        ?>
        <style type="text/css">
            .page_break { page-break-after: always;
            }
            #font_size_define{
                font-size:14px;
                font-family:'Arial Narrow';
            }
            .font_size_define{
                font-size:14px;
                font-family:'Arial Narrow';
            }
            #dataTable tbody tr span{
                 opacity:0.2;
                 color:gray;
            }
            #dataTable tbody tr{
                vertical-align:middle;
            }
        </style>
        <div style="width:655px;">
            <!--<table width="100%" cellpadding="0" cellspacing="0">-->
            <table width="100%" cellspacing="2" cellpadding="2" border="1" rules="all" class="rpt_table">
                <tr>
                    <td width="100" align="right" style="border-left:hidden; border-top:hidden; border-right:hidden; border-bottom:hidden;">
                        <img src='../../<? echo $imge_arr[str_replace("'","",$prog_data['company_id'])]; ?>' height='100%' width='100%' alt="not found" />
                    </td>
                    <td colspan="1" width="455" align="center" valign="middle" style="font-size:16px;border-left:hidden; border-top:hidden; border-right:hidden; border-bottom:hidden;"><b><? echo $company_arr[$prog_data['company_id']]; ?></b></td>
                    <td><span  id="barcode_img_id_<? echo $inc_no;?>" ></span></td>
                </tr>
                <tr>
                    <td colspan="3" style="border-left:hidden; border-right:hidden;">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3" width="100%" align="center" style="font-size:20px;border-left:hidden; border-top:hidden; border-right:hidden;"><b>KNIT CARD</b></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>PROGRAM NO</b></td>
                    <td width="350" class="font_size_define"><b><? echo $prog_no; ?></b></td>
                    <td width="105" class="font_size_define" style="border-left:hidden;"><b>Date : <? echo date('d-m-Y', strtotime($prog_data['program_date'])); ?></b></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>KNIT PARTY</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['knit_factory']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>BUYER</b></td>
                    <td colspan="2" class="font_size_define"><? echo $buyer_arr[$prog_data['buyer_id']]; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>BOOKING NO</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['booking_no']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>INT. REF.</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['int_ref']; ?></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>M/C NO</b></td>
                    <td colspan="2" class="font_size_define"><? echo $machine_arr[$mc_id];?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>M/C DIA & GG</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['machine_dia']." x ".$prog_data['machine_gg']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>F. DIA</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['fabric_dia']." "."[".$fabric_typee[$prog_data['width_dia_type']]."]"; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>Yarn Composition</b></td>
                    <td colspan="2" class="font_size_define"><? echo chop($yarn_info_arr[$prog_data['determination_id']]['y_comp'],','); ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>F. TYPE</b></td>
                    <td colspan="2" class="font_size_define"><? echo $fabric_info_arr[$prog_data['determination_id']]['f_type']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>GSM</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['gsm_weight']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>COUNT</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['yarn_dtls']; ?></td>
                </tr>

                <tr>
                    <td class="font_size_define"><b>LOT</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['lot']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>BRAND</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['brand_name']; ?></td>
                </tr>

                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>SL</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['s_length']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>Spandex SL</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['spandex_stitch_length']; ?></td>
                </tr>

                <tr>
                    <td class="font_size_define"><b>COLOR</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['color_id']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>P. QTY. (Kg)</b></td>
                    <td colspan="2" class="font_size_define"><? echo number_format($prog_data['prog_qty'],2);?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>Feeder</b></td>
                    <td colspan="2" class="font_size_define"><? echo $feeder[$prog_data['feeder']]; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>Knit Start</b></td>
                    <td colspan="2" class="font_size_define"><? echo change_date_format($prog_data['start_date']); ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>Knit End</b></td>
                    <td colspan="2" class="font_size_define"><? echo change_date_format($prog_data['end_date']); ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>Count Feeding</b></td>
                    <td colspan="2" class="font_size_define"><? echo $count_feeding; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>Remarks</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['remarks']; ?></td>
                </tr>
            </table>
            <br>
            <?
            $sql_collarCuff = sql_select("select id, body_part_id, grey_size, finish_size, qty_pcs from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id in($prog_no) order by id");
            if (count($sql_collarCuff) > 0)
            {
            ?>
                <table style="margin-top:10px;" width="655" border="1" rules="all" cellpadding="0" cellspacing="0"
                class="rpt_table">
                    <thead>
                        <tr>
                            <th width="50">SL</th>
                            <th width="200">Body Part</th>
                            <th width="200">Grey Size</th>
                            <th width="100">Finish Size</th>
                            <th>Quantity Pcs</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        $i = 1;
                        $total_qty_pcs = 0;
                        foreach ($sql_collarCuff as $row) {
                        if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                        ?>
                        <tr>
                            <td align="center"><p><? echo $i; ?>&nbsp;</p></td>
                            <td><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</p></td>
                            <td style="padding-left:5px"><p><? echo $row[csf('grey_size')]; ?>&nbsp;</p></td>
                            <td style="padding-left:5px"><p><? echo $row[csf('finish_size')]; ?>&nbsp;</p></td>
                            <td align="right"><p><? echo number_format($row[csf('qty_pcs')], 0);
                                $total_qty_pcs += $row[csf('qty_pcs')]; ?>&nbsp;&nbsp;</p></td>
                            </tr>
                            <?
                            $i++;
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th align="right">Total</th>
                            <th align="right"><? echo number_format($total_qty_pcs, 0); ?>&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
                <?
            }
            ?>

            <div style="margin-top:10px; width:555px; font-size:14px;"><?
            echo signature_table(213, $company_id, "655px",5);
            ?><!--Note: This is Software Generated Copy, Signature is not Required.--></div>
            <div class="page_break">&nbsp;</div>


        </div>
        <div>
                <script type="text/javascript" src="../../js/jquery.js"></script>
                <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
                <script>
                    function generateBarcode( valuess,id ){
                        var value = valuess;//$("#barcodeValue").val();
                        // alert(value)
                        var btype = 'code39';//$("input[name=btype]:checked").val();
                        var renderer ='bmp';// $("input[name=renderer]:checked").val();

                        var settings = {
                          output:renderer,
                          bgColor: '#FFFFFF',
                          color: '#000000',
                          barWidth: 1,
                          barHeight: 30,
                          moduleSize:5,
                          posX: 10,
                          posY: 20,
                          addQuietZone: 1
                        };
                        $("#barcode_img_id_<? echo $inc_no; ?>").html('11');
                         value = {code:value, rect: false};

                        $("#barcode_img_id_<? echo $inc_no; ?>").barcode(value, btype, settings);
                    }
                    generateBarcode('<? echo $prog_no; ?>');
                 </script>
            </div>
        <?
        }
        $inc_no++;
    }

    exit();
}

//knitting_card_print_8
if ($action == "knitting_card_print_8")
{
    echo load_html_head_contents("Knitting Card Info", "../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $program_ids =  $data;

    if(!$program_ids)
    {
        echo "Program is not found.";
        die;
    }

    //$sub_subcontract = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
    $brand_arr      = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
    $company_arr    = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $company_short_arr  = return_library_array("select id,company_short_name from lib_company", "id", "company_short_name");
    $imge_arr       = return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    $count_arr      = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 ", 'id', 'yarn_count');
    $color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

    if ($db_type == 0)
        $item_id_cond="group_concat(distinct(b.item_id))";
    else if ($db_type==2)
        $item_id_cond="LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id)";

    //for machine distrubution qty
    $result_machin_prog = sql_select("SELECT machine_id, dtls_id, distribution_qnty from ppl_planning_info_machine_dtls WHERE DTLS_ID IN(".$program_ids.")");
    $machin_prog = array();
    foreach ($result_machin_prog as $row)
    {
        $machin_prog[$row[csf('machine_id')]][$row[csf('dtls_id')]]['distribution_qnty'] = $row[csf('distribution_qnty')];
    }
    //echo "<pre>";
    //print_r($machin_prog);
    //echo "</pre>";

    $reqsDataArr = array();
    $program_cond2 = ($program_ids) ? " and knit_id in(".$program_ids.")" : "";

    if ($db_type == 0)
    {
        $reqsData = sql_select("select knit_id, requisition_no as reqs_no, group_concat(distinct(prod_id)) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id");
    }
    else
    {
        $reqsData = sql_select("select knit_id, max(requisition_no) as reqs_no, LISTAGG(prod_id, ',') WITHIN GROUP (ORDER BY prod_id) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id,requisition_no");
    }

    $prog_prod_arr = array();
    foreach ($reqsData as $row)
    {
        $reqsDataArr[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
        $reqsDataArr[$row[csf('knit_id')]]['prod_id'] = $row[csf('prod_id')];
        $prod_arr[] = $row[csf('prod_id')];

        $expProd_id = explode(',', $row[csf('prod_id')]);
        foreach($expProd_id as $key=>$val)
        {
            $prog_prod_arr[$row[csf('knit_id')]][$val] = $val;
        }
    }
    //echo "<pre>";
    //print_r($prog_prod_arr);
    //echo "</pre>";

    if(!empty($prod_arr))
    {
        $product_details_arr = array();
        $procuct_cond = (!empty($prod_arr))?" and id in(".implode(",",$prod_arr).")":"";
        $pro_sql = sql_select("select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand, product_name_details from product_details_master where item_category_id=1 $procuct_cond");
        foreach ($pro_sql as $row)
        {
            $compos = '';
            if ($row[csf('yarn_comp_percent2nd')] != 0)
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
            }
            else
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
            }

            $product_details_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
            $product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
            $product_details_arr[$row[csf('id')]]['brand_name'] = $row[csf('brand')];
            //$product_details_arr[$row[csf('id')]]['supplier'] = $row[csf('supplier_id')];
            $yarn_details_arr[$row[csf('id')]]['yarn_count'] = $count_arr[$row[csf('yarn_count_id')]];
            $yarn_details_arr[$row[csf('id')]]['yarn_type'] = $yarn_type[$row[csf('yarn_type')]];
            $yarn_details_arr[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
            $yarn_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
            $yarn_details_arr[$row[csf('id')]]['composition'] = $compos;

            $yarn_details_arr[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
        }

        //for yarn test
        $sql_yarn_test = sql_select("SELECT a.prod_id AS PROD_ID, b.comments_knit COMMENTS_KNIT FROM inv_yarn_test_mst a, INV_YARN_TEST_COMMENTS b WHERE a.id = b.mst_table_id AND a.prod_id IN(".implode(",",$prod_arr).") AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0");
        $yarn_test_info = array();
        foreach($sql_yarn_test as $yarn_test)
        {
            $yarn_test_info[$yarn_test['PROD_ID']]['comments_knit'] = $yarn_test['COMMENTS_KNIT'];
        }
    }

    //echo "<pre>";
    //print_r($yarn_details_arr);

    /*
    $booking_qnty_arr = array();
    $sql_data = sql_select("select a.booking_no, sum(b.grey_fab_qnty ) as grey_fab_qnty,a.quality_level from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.fabric_source=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.booking_no,a.quality_level");
    foreach ($sql_data as $row)
    {
        $booking_qnty_arr[$row[csf('booking_no')]] += $row[csf('grey_fab_qnty')];
        $order_nature_booking_arr[$row[csf('booking_no')]]= $row[csf('quality_level')];
    }
    unset($sql_data);
    */

        /*$product_details_array = array();
        $sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0";
        $result = sql_select($sql);

        foreach ($result as $row)
        {
            $compos = '';
            if ($row[csf('yarn_comp_percent2nd')] != 0)
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
            }
            else
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
            }

            $product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
            $product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
        }
        unset($result);*/
        $order_no = '';
        $buyer_name = '';
        $knitting_factory = '';
        $job_no = '';
        $booking_no = '';
        $company = '';
        $jobNo="";
        $poQuantity="";

        /*
        $job_data_sql=sql_select("select a.id,a.grouping, a.job_no_mst,a.po_number, sum(a.po_quantity) as poQuantity, b.booking_no, c.style_ref_no from wo_po_break_down a, ppl_planning_entry_plan_dtls b, wo_po_details_master c where a.id=b.po_id and b.dtls_id in (".$program_ids.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst=job_no group by a.id, a.grouping, a.job_no_mst, a.po_number, b.booking_no, c.style_ref_no");
        $po_details= array();
        foreach($job_data_sql as $row)
        {
            $jobNo      = $row[csf('job_no_mst')];
            $poQuantity = $row[csf('poQuantity')];
            $style      = $row[csf('style_ref_no')];
            $po_details[$row[csf('id')]]['po_number']= $row[csf('po_number')];
            $ref_no     = $row[csf('grouping')];
            $order_nature=$order_nature_booking_arr[$row[csf('booking_no')]];
        }
        unset($job_data_sql);
        */

        $data_sql="select a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, a.dye_type, b.buyer_id, b.customer_buyer, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in (".$program_ids.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, a.dye_type, b.buyer_id, b.customer_buyer, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id";
        //, b.yarn_desc
        $dataArray = sql_select($data_sql);
        $sales_id_arr = array();
        foreach ($dataArray as $row)
        {
            $sales_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
        }

        //for sales order infomation
        $sql_fab_sales = sql_select("SELECT id AS ID, job_no AS JOB_NO, style_ref_no AS STYLE_REF_NO, buyer_id AS BUYER_ID, within_group as WITHIN_GROUP,CUSTOMER_BUYER FROM fabric_sales_order_mst WHERE id IN(".implode(',',$sales_id_arr).")");
        $fab_sales_arr = array();
        foreach($sql_fab_sales as $row)
        {
            $fab_sales_arr[$row['ID']]['sales_order_no'] = $row['JOB_NO'];
            $fab_sales_arr[$row['ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
            $fab_sales_arr[$row['ID']]['buyer_id'] = $row['BUYER_ID'];
            $fab_sales_arr[$row['ID']]['within_group'] = $row['WITHIN_GROUP'];
            $fab_sales_arr[$row['ID']]['customer_buyer'] = $row['CUSTOMER_BUYER'];
        }

        $program_data_arr=array();
        $sql = "select count_id,feeding_id from ppl_planning_count_feed_dtls where dtls_id=".$dataArray[0][csf('id')]." order by seq_no";
        $data_array = sql_select($sql);
        $count_feeding_data="";
        foreach ($data_array as $row)
        {
            //$count_feeding_data_arr[]=$row[csf('count_id')].'_'.$row[csf('feeding_id')];
            if($count_feeding_data !="") $count_feeding_data .=",";
            $count_feeding_data .= $count_arr[$row[csf('count_id')]].'-'.$feeding_arr[$row[csf('feeding_id')]];
        }

        $company_id = '';
        $buyer_name = '';
        $booking_no = '';
        $orderNo = "";
        foreach ($dataArray as $row)
        {
            $knitting_factory='';
            if ($row[csf('knitting_source')] == 1)
                $knitting_factory = $company_arr[$row[csf('knitting_party')]] . ",";
            else if ($row[csf('knitting_source')] == 3)
                $knitting_factory = $supplier_details[$row[csf('knitting_party')]] . ",";

            $yarn_desc='';
            $lot_no="";
            $brand_name=array();
            $yarn_dtls="";
            /*if($orderNo=="")
            {
                $orderNo .= $row[csf('po_id')];
                $po_number .= $po_details[$row[csf('po_id')]]['po_number'];
            }
            else
            {
                $orderNo .= ",".$row[csf('po_id')];
                $po_number .= ",".$po_details[$row[csf('po_id')]]['po_number'];
            }*/

            $prod_id = array_unique(explode(",", $reqsDataArr[$row[csf('id')]]['prod_id']));
            foreach ($prod_id as $val)
            {
                $yarn_desc .= $product_details_arr[$val]['desc'] . ",";
                $lot_no .= $product_details_arr[$val]['lot'] . ",";
                $brand_name[$product_details_arr[$val]['brand_name']] = $brand_arr[$product_details_arr[$val]['brand_name']];
                //$yarn_dtls .= $product_details_arr[$val]['desc'] . ",".$product_details_arr[$val]['lot'] . ",".$brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . "<br>";
                $yarn_dtls .= $yarn_details_arr[$val]['yarn_count'] . ", ".$yarn_details_arr[$val]['composition'] . ", ".$yarn_details_arr[$val]['yarn_type'].", ".$yarn_details_arr[$val]['color'].', '.$yarn_details_arr[$val]['brand'].", ".$yarn_details_arr[$val]['lot'] . "<br>";
            }

            $yarn_desc = implode(", ",array_filter(array_unique(explode(",", substr($yarn_desc, 0, -1)))));
            $lot_no = implode(", ",array_filter(array_unique(explode(",", substr($lot_no, 0, -1)))));
            $brand_name = implode(", ", $brand_name);
            $ex_mc_id=array_unique(explode(",",$row[csf('machine_id')]));

            /*$machine_name="";
            foreach($ex_mc_id as $mc_id)
            {
                if($machine_name=='') $machine_name=$machine_arr[$mc_id]; else $machine_name.=','.$machine_arr[$mc_id];
            }*/

            $color_name="";
            $ex_color_id=array_unique(explode(",",$row[csf('color_id')]));
            foreach($ex_color_id as $color_id)
            {
                if($color_name=='') $color_name=$color_library[$color_id];
                else $color_name.=','.$color_library[$color_id];
            }

            //$program_data_arr[$row[csf('id')]]['po_number']=$po_number;
            $program_data_arr[$row[csf('id')]]['co_efficient']=$row[csf('co_efficient')];
            $program_data_arr[$row[csf('id')]]['draft_ratio']=$row[csf('draft_ratio')];
            $program_data_arr[$row[csf('id')]]['start_date']=$row[csf('start_date')];
            $program_data_arr[$row[csf('id')]]['end_date']=$row[csf('end_date')];
            $program_data_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
            $program_data_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
            $program_data_arr[$row[csf('id')]]['color_id']=$color_name;
            //$program_data_arr[$row[csf('id')]]['prog_qty']+=$row[csf('program_qnty')];
            $program_data_arr[$row[csf('id')]]['prog_qty']=$row[csf('program_qnty')];
            $program_data_arr[$row[csf('id')]]['s_length']=$row[csf('stitch_length')];
            $program_data_arr[$row[csf('id')]]['fabric_dia']=$row[csf('fabric_dia')];
            $program_data_arr[$row[csf('id')]]['program_date']=$row[csf('program_date')];
            $program_data_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
            $program_data_arr[$row[csf('id')]]['booking_qty']=$booking_qnty_arr[$row[csf('booking_no')]];
            $program_data_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];

            $program_data_arr[$row[csf('id')]]['machine_id']=$row[csf('machine_id')];
            $program_data_arr[$row[csf('id')]]['knitting_party']=$row[csf('knitting_party')];
            $program_data_arr[$row[csf('id')]]['location_id']=$row[csf('location_id')];

            $program_data_arr[$row[csf('id')]]['yarn_dtls']= $yarn_dtls;
            $program_data_arr[$row[csf('id')]]['yarn_desc']= $yarn_desc;
            $program_data_arr[$row[csf('id')]]['lot']= $lot_no;
            $program_data_arr[$row[csf('id')]]['brand_name']= $brand_name;
            $program_data_arr[$row[csf('id')]]['mc_nmae']= $machine_name;
            $program_data_arr[$row[csf('id')]]['knit_factory']= $knitting_factory;
            $program_data_arr[$row[csf('id')]]['sub_party']= $supplier_details[$row[csf('subcontract_party')]];
            $program_data_arr[$row[csf('id')]]['gsm_weight']=$row[csf('gsm_weight')];
            $program_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];

            //for buyer
            //$program_data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
            if($fab_sales_arr[$row[csf('po_id')]]['within_group'] == 1)
            {
                $program_data_arr[$row[csf('id')]]['buyer_id']=$company_short_arr[$fab_sales_arr[$row[csf('po_id')]]['buyer_id']];
            }
            else
            {
                $program_data_arr[$row[csf('id')]]['buyer_id']=$buyer_arr[$fab_sales_arr[$row[csf('po_id')]]['buyer_id']];
            }
            //$program_data_arr[$row[csf('id')]]['customer_buyer']=$row[csf('customer_buyer')];
            $program_data_arr[$row[csf('id')]]['customer_buyer']=$fab_sales_arr[$row[csf('po_id')]]['customer_buyer'];

            $program_data_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
            $program_data_arr[$row[csf('id')]]['spandex_stitch_length']=$row[csf('spandex_stitch_length')];
            $program_data_arr[$row[csf('id')]]['feeder']=$row[csf('feeder')];
            $program_data_arr[$row[csf('id')]]['advice']=$row[csf('advice')];
            $program_data_arr[$row[csf('id')]]['knit_id']=$row[csf('mst_id')];
            $program_data_arr[$row[csf('id')]]['width_dia_type']=$row[csf('width_dia_type')];
            $program_data_arr[$row[csf('id')]]['dye_type']=$row[csf('dye_type')];
            //$program_data_arr[$row[csf('id')]]['style']=$style;
            $program_data_arr[$row[csf('id')]]['style']=$fab_sales_arr[$row[csf('po_id')]]['style_ref_no'];
            $program_data_arr[$row[csf('id')]]['sales_order_no']=$fab_sales_arr[$row[csf('po_id')]]['sales_order_no'];
            $program_data_arr[$row[csf('id')]]['requisition_no']=$reqsDataArr[$row[csf('id')]]['reqs_no'];
        }
        unset($dataArray);

        /*if($orderNo!="")
        {
            $sql_tna = sql_select("select job_no,po_number_id,task_start_date,task_finish_date FROM tna_process_mst WHERE status_active=1 AND is_deleted=0 and po_number_id in ($orderNo) and task_number=60");
            $tnaData = array();

            if(!empty($sql_tna))
            {
                foreach($sql_tna as $tna_row)
                {
                    $tnaData[$tna_row[csf('job_no')]][$tna_row[csf('po_number_id')]]['task_start_date'] =  $tna_row[csf('task_start_date')];

                    $tnaData[$tna_row[csf('job_no')]][$tna_row[csf('po_number_id')]]['task_finish_date'] =  $tna_row[csf('task_finish_date')];
                }
            }

        }*/
        //$knit_id_arr = return_library_array("select a.requisition_no from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b where a.knit_id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.id in($program_ids) group by a.requisition_no", "requisition_no", "requisition_no");
        //echo "<pre>";
        //print_r($ex_mc_id);

        $barcode_sl=1;
        $inc_no=1;
        $program_array = array();
        foreach($ex_mc_id as $mc_id)
        {
            // program array loop
            foreach($program_data_arr as $prog_no=>$prog_data)
            {
                ?>
                <style type="text/css">
                    .page_break { page-break-after: always;
                    }
                    #font_size_define{
                        font-size:14px;
                        font-family:'Arial Narrow';
                    }
                    .font_size_define{
                        font-size:14px;
                        font-family:'Arial Narrow';
                    }
                    #dataTable tbody tr span{
                         opacity:0.2;
                         color:gray;
                    }
                    #dataTable tbody tr{
                        vertical-align:middle;
                    }
                </style>
                <div style="width:1180px;">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td width="70" align="right">
                                <img  src='../../<? echo $imge_arr[str_replace("'","",$prog_data['company_id'])]; ?>' height='100%' width='100%' />
                            </td>
                            <td>
                                <table width="100%" style="margin-top:10px">
                                    <tr>
                                        <td width="100%" align="center" style="font-size:20px;"><b><? echo $company_arr[$prog_data['company_id']]; ?></b></td>

                                    </tr>
                                    <tr>
                                        <td align="center" style="font-size:14px"><? echo show_company($prog_data['company_id'], '', ''); ?></td>
                                    </tr>
                                    <tr>
                                        <td width="100%" align="center" style="font-size:16px;"><b><u>Knit Card</u></b> <b style=" float:right;color:#000"><? if($fbooking_order_nature[$order_nature]) echo "(".$fbooking_order_nature[$order_nature].")".'&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;';else echo " ";?></b></td>
                                    </tr>

                                </table>
                            </td>

                            <td><span  id="barcode_img_id_<? echo $inc_no; ?>" ></span></td>
                        </tr>
                    </table>
                    <div style="margin-top:5px; width:1180px">
                        <table cellspacing="2" cellpadding="2" border="1" rules="all" width="100%" class="rpt_table" style="" id="dataTable">
                            <thead height="25">
                                <th colspan="2" width="230" id="font_size_define">Program Details</th>
                                <th colspan="2" width="233" id="font_size_define">Job Details</th>
                                <th colspan="2" width="250" id="font_size_define">Yarn/Fabric Details</th>
                                <th colspan="2" width="233" id="font_size_define">M/C Details</th>
                                <th colspan="2" width="233" id="font_size_define">Technical Details</th>
                            </thead>
                            <tbody>
                                <tr height="22">
                                    <td width="100" class="font_size_define">Knit Party</td>
                                    <td width="132" class="font_size_define"><? echo $prog_data['knit_factory']; ?></td>
                                    <td width="100" class="font_size_define">Cust. Buyer</td>
                                    <td width="132" class="font_size_define"><? echo $buyer_arr[$prog_data['customer_buyer']];//$buyer_arr[$prog_data['customer_buyer']]; ?></td>
                                    <td width="80" class="font_size_define">Req. No</td>
                                    <td width="170" class="font_size_define"><? echo $prog_data['requisition_no']; ?></td>
                                    <td width="100" class="font_size_define">M/C No</td>
                                    <td width="132" class="font_size_define"><? echo $machine_arr[$mc_id];?></td>
                                    <td width="100" class="font_size_define">Stitch Length</td>
                                    <td width="132" class="font_size_define"><? echo $prog_data['s_length']; ?></td>
                                </tr>
                                <tr height="22">
                                    <td class="font_size_define">Program No</td>
                                    <td class="font_size_define"><? echo $prog_no; ?></td>
                                    <td class="font_size_define">Customer</td>
                                    <td class="font_size_define"><? echo $prog_data['buyer_id']; ?></td>
                                    <td class="font_size_define" rowspan="4">Yarn Desc</td>
                                    <td class="font_size_define" rowspan="4"><? echo $prog_data['yarn_desc']; ?></td>
                                    <td class="font_size_define">Dia x Gauge</td>
                                    <td class="font_size_define"><? echo $prog_data['machine_dia']." x ".$prog_data['machine_gg']; ?></td>
                                    <td class="font_size_define">Span. Stitch Length</td>
                                    <td class="font_size_define"><? echo $prog_data['spandex_stitch_length']; ?></td>
                                </tr>
                                <tr height="22">
                                    <td class="font_size_define">Program Date</td>
                                    <td class="font_size_define"><? echo change_date_format($prog_data['program_date']); ?></td>
                                    <td class="font_size_define">Sales Job/Booking  No</td>
                                    <td class="font_size_define"><? echo $prog_data['booking_no']; ?></td>
                                    <td class="font_size_define">Finished Dia</td>
                                    <td class="font_size_define"><? echo $prog_data['fabric_dia']." "."[".$fabric_typee[$prog_data['width_dia_type']]."]"; ?></td>
                                    <td class="font_size_define">M/C RPM</td>
                                    <td class="font_size_define"><span>Write&nbsp;</span></td>
                                </tr>
                                <tr height="22">
                                    <td class="font_size_define">Program Qty</td>
                                    <td class="font_size_define"><? echo number_format($prog_data['prog_qty'],2);?></td>
                                    <td class="font_size_define">Sales Order No</td>
                                    <td class="font_size_define"><? echo $prog_data['sales_order_no']; ?></td>
                                    <td class="font_size_define">Fabric Type</td>
                                    <td class="font_size_define"><? echo $prog_data['fabric_desc']; ?></td>
                                    <td class="font_size_define">Counter</td>
                                    <td class="font_size_define"><span>Write&nbsp;</span></td>
                                </tr>
                                <tr height="22">
                                    <td class="font_size_define">Target/Shift</td>
                                    <td class="font_size_define"><span>Write&nbsp;</span></td>
                                    <td class="font_size_define">Style</td>
                                    <td class="font_size_define"><? echo $prog_data['style']; ?></td>
                                    <td class="font_size_define">FGSM</td>
                                    <td class="font_size_define"><? echo $prog_data['gsm_weight']; ?></td>
                                    <td class="font_size_define">Feeder</td>
                                    <td class="font_size_define"><? echo $feeder[$prog_data['feeder']]; ?></td>
                                </tr>
                                <tr height="22">
                                    <td class="font_size_define">Program Start</td>
                                    <td class="font_size_define"><? echo change_date_format($prog_data['start_date']); ?></td>
                                    <td class="font_size_define">Color</td>
                                    <td class="font_size_define"><? echo $prog_data['color_id']; ?></td>
                                    <td class="font_size_define">Yarn Brand</td>
                                    <td class="font_size_define"><? echo $prog_data['brand_name']; ?></td>
                                    <td class="font_size_define">M/C Target QTY</td>
                                    <td class="font_size_define"><? echo $machin_prog[$mc_id][$prog_no]['distribution_qnty']; ?></td>
                                    <td class="font_size_define"></td>
                                    <td class="font_size_define"></td>
                                </tr>
                                <tr height="22">
                                    <td class="font_size_define">Program End</td>
                                    <td class="font_size_define"><? echo change_date_format($prog_data['end_date']); ?></td>
                                    <td class="font_size_define">Dye Type</td>
                                    <td class="font_size_define"><? echo $prog_data['dye_type']; ?></td>
                                    <td class="font_size_define">Shade No.</td>
                                    <td class="font_size_define"><span>Write&nbsp;</span></td>
                                    <td class="font_size_define"></td>
                                    <td class="font_size_define"></td>
                                    <td class="font_size_define"></td>
                                    <td class="font_size_define"></td>
                                </tr>
                                <tr>
                                    <td rowspan="<? echo count($prog_prod_arr[$prog_no])+1; ?>">Yarn Lot and Test Result</td>
                                    <td id="font_size_define" align="center"><b>Lot No</b></td>
                                    <td id="font_size_define" align="center" colspan="8"><b>Comments</b></td>
                                </tr>
                                <?php
                                //foreach($product_details_arr as $key=>$val)
                                foreach($prog_prod_arr[$prog_no] as $key=>$val)
                                {
                                    ?>
                                <tr>
                                    <td><? echo $product_details_arr[$key]['lot']; ?></td>
                                    <td colspan="8"><? echo $yarn_test_info[$key]['comments_knit']; ?></td>
                                </tr>

                                    <?php
                                }
                                ?>
                                <tr height="50">
                                    <td class="font_size_define">Technical Instruction</td>
                                    <td class="font_size_define" colspan="9"><span>Write&nbsp;</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top:10px; width:920px">
                        <table cellspacing="2" cellpadding="2" rules="" width="100%">
                            <tbody>
                                <tr><td class="font_size_define"><u><b>বিঃ দ্রঃ</b></u></td></tr>
                                <tr><td class="font_size_define">* প্রোগ্রামের শেষ পর্যায়ে অবশ্যই সূতা মিল করে চালাতে হবে।</td></tr>
                                <tr><td class="font_size_define">* সূতা মেশিনে উঠানোর সময় ১/১ করে উঠতে হবে।</td></tr>
                                <tr><td class="font_size_define">* রোল মার্কিং সঠিকভাবে করতে হবে। </td></tr>
                                <tr><td class="font_size_define">* রোলে কোন প্রকার কাটা-কাটি বা ভুল লেখা চলবেনা।</td></tr>
                                <tr><td class="font_size_define">* রোলের গায়ে জব, কালার, মেশিন নম্বর, তারিখ এবং শিফট অবশ্যই লিখতে হবে।</td></tr>
                                <tr><td class="font_size_define">&nbsp;</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <? echo signature_table(119, $prog_data['company_id'], "920px","","20"); ?>
                    <div class="page_break">&nbsp;</div>
                </div>
                <div>
                    <script type="text/javascript" src="../../js/jquery.js"></script>
                    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
                    <script>
                        function generateBarcode( valuess,id ){
                            var value = valuess;//$("#barcodeValue").val();
                            // alert(value)
                            var btype = 'code39';//$("input[name=btype]:checked").val();
                            var renderer ='bmp';// $("input[name=renderer]:checked").val();

                            var settings = {
                              output:renderer,
                              bgColor: '#FFFFFF',
                              color: '#000000',
                              barWidth: 1,
                              barHeight: 30,
                              moduleSize:5,
                              posX: 10,
                              posY: 20,
                              addQuietZone: 1
                            };
                            $("#barcode_img_id_<? echo $inc_no; ?>").html('11');
                             value = {code:value, rect: false};

                            $("#barcode_img_id_<? echo $inc_no; ?>").barcode(value, btype, settings);
                        }
                        generateBarcode('<? echo $prog_no; ?>');
                     </script>
                </div>
                <?
            }
            $inc_no++;
        }
        exit();
}

//knitting_card_print_9
if ($action == "knitting_card_print_9_OLD-issueID-13938")
{
    echo load_html_head_contents("Knitting Card Info", "../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $program_ids =  $data;

    if(!$program_ids)
    {
        echo "Program is not found . ";
        die;
    }

    $sub_subcontract = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
    $brand_arr      = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
    $buyer_brand_arr = return_library_array("select id, brand_name from lib_buyer_brand", 'id', 'brand_name');
    $company_arr    = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $imge_arr       = return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    $count_arr      = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 ", 'id', 'yarn_count');
    $color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
    $yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by id, yarn_count","id","yarn_count");
    $floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
    $machineId_arr=array();
    $sql_mc=sql_select("select id, machine_no, floor_id from lib_machine_name");
    $lib_machine_arr=return_library_array("select id,machine_no from  lib_machine_name where status_active=1 and is_deleted=0","id","machine_no");

    foreach( $sql_mc as $row)
    {
        $machineId_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
    }
    unset($sql_mc);


    if ($db_type == 0)
        $item_id_cond="group_concat(distinct(b.item_id))";
    else if ($db_type==2)
        $item_id_cond="LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id)";

    $result_machin_prog = sql_select("SELECT machine_id,dtls_id,distribution_qnty from ppl_planning_info_machine_dtls WHERE DTLS_ID IN($program_ids)");
    $machin_prog = array();
    foreach ($result_machin_prog as $row)
    {
        $machin_prog[$row[csf('machine_id')]][$row[csf('dtls_id')]]['distribution_qnty'] = $row[csf('distribution_qnty')];
    }

    $reqsDataArr = array();
    $program_cond2 = ($program_ids) ? " and knit_id in(".$program_ids.")" : "";
    if ($db_type == 0)
    {
        $reqsData = sql_select("select knit_id, requisition_no as reqs_no, group_concat(distinct(prod_id)) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id");
    }
    else
    {
        $reqsData = sql_select("select knit_id, max(requisition_no) as reqs_no, LISTAGG(prod_id, ',') WITHIN GROUP (ORDER BY prod_id) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id,requisition_no");
    }

    foreach ($reqsData as $row)
    {
        $reqsDataArr[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
        $reqsDataArr[$row[csf('knit_id')]]['prod_id'] = $row[csf('prod_id')];
        $prod_arr[] = $row[csf('prod_id')];
    }
    unset($reqsData);


    //for booking information


    if(!empty($prod_arr))
    {
        $product_details_arr = array();
        $procuct_cond = (!empty($prod_arr))?" and id in(".implode(",",$prod_arr).")":"";
        $pro_sql = sql_select("select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 $procuct_cond");
        foreach ($pro_sql as $row)
        {
            $compos = '';
            if ($row[csf('yarn_comp_percent2nd')] != 0)
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
            }
            else
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
            }

            $product_details_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
            $product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
            $product_details_arr[$row[csf('id')]]['brand_name'] = $row[csf('brand')];
            //$product_details_arr[$row[csf('id')]]['supplier'] = $row[csf('supplier_id')];
            $yarn_details_arr[$row[csf('id')]]['yarn_count'] = $count_arr[$row[csf('yarn_count_id')]];
            $yarn_details_arr[$row[csf('id')]]['yarn_type'] = $yarn_type[$row[csf('yarn_type')]];
            $yarn_details_arr[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
            $yarn_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
            $yarn_details_arr[$row[csf('id')]]['composition'] = $compos;
            $yarn_details_arr[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
        }
        unset($pro_sql);
    }
    //echo "<pre>";
    //print_r($yarn_details_arr);

    $data_sql="SELECT a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id, c.job_no, b.color_type_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b left join fabric_sales_order_mst c on c.id=b.po_id and c.status_active=1 and c.is_deleted=0 where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id, c.job_no, b.color_type_id";
    //, b.yarn_desc
    //echo $data_sql;
    $dataArray = sql_select($data_sql);
    $bookingNoArr = array();
    //$progNoArr = array();
    $progWiseMachineNoArr = array();
    foreach ($dataArray as $row)
    {
        //for booking no
        $bookingNoArr[$row[csf('booking_no')]] = $row[csf('booking_no')];

        //for prog no
        //$progNoArr[$row[csf('id')]] = $row[csf('id')];
        $progWiseMachineNoArr[$row[csf('id')]] = $row[csf('machine_id')];
    }
    //print_r( $progWiseMachineNoArr);
    foreach ($progWiseMachineNoArr as $progs => $row)
    {
        $progWiseMachineNoArray[$progs] = explode(",", $row);
    }
    //print_r( $progWiseMachineNoArray[$progs])  ;
    $programWiseMachineArr=array();
    foreach ($progWiseMachineNoArray as $progNos => $progData)
    {
        foreach ($progData as $keys => $machineIds)
        {
            //$programWiseMachineArr[$progNos].=$machine_arr[$machineIds].",";
            $programWiseMachineArr[$progNos]=$machine_arr[$machineIds];
            $progMacArr[$progNos]=$machineIds;
        }
    }
    //print_r( $progMacArr);
    //for booking qty
    $booking_qnty_arr = array();
    $sql_data = sql_select("select a.booking_no, a.buyer_id, sum(b.grey_fab_qnty ) as grey_fab_qnty, a.quality_level from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.fabric_source=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0".where_con_using_array($bookingNoArr, '1', 'a.booking_no')." group by a.booking_no, a.buyer_id, a.quality_level");
    foreach ($sql_data as $row)
    {
        $booking_qnty_arr[$row[csf('booking_no')]]['qty'] += $row[csf('grey_fab_qnty')];
        $booking_qnty_arr[$row[csf('booking_no')]]['buyer'] += $row[csf('buyer_id')];
        //$order_nature_booking_arr[$row[csf('booking_no')]]= $row[csf('quality_level')];
    }
    unset($sql_data);

    //for int. ref.
    $sqlBooking = "SELECT a.grouping AS GROUPING, b.booking_no AS BOOKING_NO, b.job_no AS JOB_NO, c.brand_id AS BRAND_ID FROM wo_po_break_down a, wo_booking_dtls b, wo_po_details_master c  where a.id = b.po_break_down_id AND a.job_no_mst=c.job_no AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0".where_con_using_array($bookingNoArr, '1', 'b.booking_no')." GROUP BY a.grouping, b.booking_no, b.job_no, c.brand_id";
    //echo $sqlBooking;
    $sqlBookingRslt = sql_select($sqlBooking);
    $bookingInfoArr = array();
    foreach($sqlBookingRslt as $row)
    {
        $bookingInfoArr[$row['BOOKING_NO']]['int_ref'] .= $row['GROUPING'].',';
        $bookingInfoArr[$row['BOOKING_NO']]['job_no'] = $row['JOB_NO'];
        $bookingInfoArr[$row['BOOKING_NO']]['brand_id'] = $row['BRAND_ID'];
    }
    unset($sqlBookingRslt);

    //for po buyer
    $sqlPoBuyer = sql_select("select sales_booking_no AS BOOKING_NO, po_buyer AS BUYER from fabric_sales_order_mst where status_active = 1 AND is_deleted = 0".where_con_using_array($bookingNoArr, '1', 'sales_booking_no'));
    $poBuyerArr = array();
    foreach($sqlPoBuyer as $row)
    {
        $poBuyerArr[$row['BOOKING_NO']] = $row['BUYER'];
    }
    unset($sqlPoBuyer);

    $company_id = '';
    $orderNo = "";
    $knitting_factory = '';
    $program_data_arr=array();

    foreach ($dataArray as $row)
    {
        $knitting_factory='';
        if ($row[csf('knitting_source')] == 1)
            $knitting_factory = $company_arr[$row[csf('knitting_party')]] . ",";
        else if ($row[csf('knitting_source')] == 3)
            $knitting_factory = $supplier_details[$row[csf('knitting_party')]] . ",";

        $yarn_desc='';
        $lot_no="";
        $brand_name="";
        $yarn_dtls="";
        $yarn_types="";
        if($orderNo=="")
        {
            $orderNo .= $row[csf('po_id')];
            $po_number .= $po_details[$row[csf('po_id')]]['po_number'];
        }
        else
        {
            $orderNo .= ",".$row[csf('po_id')];
            $po_number .= ",".$po_details[$row[csf('po_id')]]['po_number'];
        }

        if($reqsDataArr[$row[csf('id')]]['prod_id'] != '')
        {
            $prod_id = array_unique(explode(",", $reqsDataArr[$row[csf('id')]]['prod_id']));
            foreach ($prod_id as $val)
            {
                $yarn_desc .= $product_details_arr[$val]['desc'] . ", ";
                $lot_no .= $product_details_arr[$val]['lot'] . ", ";

                //$brand_name .= $brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . ", ";
                $brand_name .= $brand_arr[$product_details_arr[$val]['brand_name']]. ", ";
                //$yarn_dtls .= $product_details_arr[$val]['desc'] . ",".$product_details_arr[$val]['lot'] . ",".$brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . "<br>";
                //$yarn_dtls .= $yarn_details_arr[$val]['yarn_count'] . ", ".$yarn_details_arr[$val]['composition'] . ", ".$yarn_details_arr[$val]['yarn_type'].", ".$yarn_details_arr[$val]['color'].', '.$yarn_details_arr[$val]['brand'].", ".$yarn_details_arr[$val]['lot'] . "<br>";
                // $yarn_dtls .= $yarn_details_arr[$val]['yarn_count']. "<br>";
                $yarn_dtls .= $yarn_details_arr[$val]['yarn_count'] . ", ".$yarn_details_arr[$val]['composition'] . ", ".$yarn_details_arr[$val]['yarn_type'].", ".$yarn_details_arr[$val]['color'].', '.$yarn_details_arr[$val]['brand'].", ".$yarn_details_arr[$val]['lot'] . "<br>";
                $yarn_types .=$yarn_details_arr[$val]['yarn_type']. "<br>";
            }

            $yarn_desc = implode(",",array_filter(array_unique(explode(",", substr($yarn_desc, 0, -1)))));
            $lot_no = implode(",",array_filter(array_unique(explode(",", substr($lot_no, 0, -1)))));
            $brand_name = implode(",",array_filter(array_unique(explode(",", substr($brand_name, 0, -1)))));
        }
        $ex_mc_id=array_unique(explode(",",$row[csf('machine_id')]));

        /*$machine_name="";
        foreach($ex_mc_id as $mc_id)
        {
            if($machine_name=='') $machine_name=$machine_arr[$mc_id]; else $machine_name.=','.$machine_arr[$mc_id];
        }*/

        //for color
        $color_name="";
        $ex_color_id=array_unique(explode(",",$row[csf('color_id')]));
        foreach($ex_color_id as $color_id)
        {
            if($color_name=='')
                $color_name=$color_library[$color_id];
            else
                $color_name.=', '.$color_library[$color_id];
        }

        $program_data_arr[$row[csf('id')]]['po_number']=$po_number;
        $program_data_arr[$row[csf('id')]]['co_efficient']=$row[csf('co_efficient')];
        $program_data_arr[$row[csf('id')]]['draft_ratio']=$row[csf('draft_ratio')];
        $program_data_arr[$row[csf('id')]]['start_date']=$row[csf('start_date')];
        $program_data_arr[$row[csf('id')]]['end_date']=$row[csf('end_date')];
        $program_data_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
        $program_data_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
        $program_data_arr[$row[csf('id')]]['color_id']=$color_name;
        //$program_data_arr[$row[csf('id')]]['prog_qty']+=$row[csf('program_qnty')];
        $program_data_arr[$row[csf('id')]]['prog_qty']=$row[csf('program_qnty')];
        $program_data_arr[$row[csf('id')]]['s_length']=$row[csf('stitch_length')];
        $program_data_arr[$row[csf('id')]]['fabric_dia']=$row[csf('fabric_dia')];
        $program_data_arr[$row[csf('id')]]['program_date']=$row[csf('program_date')];
        $program_data_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
        $program_data_arr[$row[csf('id')]]['booking_qty']=$booking_qnty_arr[$row[csf('booking_no')]]['qty'];
        $program_data_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];

        $program_data_arr[$row[csf('id')]]['yarn_dtls']= $yarn_dtls;
        $program_data_arr[$row[csf('id')]]['yarn_types']= $yarn_types;
        $program_data_arr[$row[csf('id')]]['yarn_desc']= $yarn_desc;
        $program_data_arr[$row[csf('id')]]['lot']= $lot_no;
        $program_data_arr[$row[csf('id')]]['brand_name']= $brand_name;
        $program_data_arr[$row[csf('id')]]['mc_nmae']= $machine_name;
        $program_data_arr[$row[csf('id')]]['knit_factory']= $knitting_factory;
        $program_data_arr[$row[csf('id')]]['sub_party']= $supplier_details[$row[csf('subcontract_party')]];
        $program_data_arr[$row[csf('id')]]['gsm_weight']=$row[csf('gsm_weight')];
        $program_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
        $program_data_arr[$row[csf('id')]]['text_ref']= $row[csf('job_no')];
        $program_data_arr[$row[csf('id')]]['color_type_id']= $row[csf('color_type_id')];

        //for buyer
        //$program_data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
        if($booking_qnty_arr[$row[csf('booking_no')]]['buyer'] != '' && $booking_qnty_arr[$row[csf('booking_no')]]['buyer'] != 0)
        {
            $program_data_arr[$row[csf('id')]]['buyer_id']=$booking_qnty_arr[$row[csf('booking_no')]]['buyer'];
        }
        else
        {
            $program_data_arr[$row[csf('id')]]['buyer_id']=$poBuyerArr[$row[csf('booking_no')]];
        }

        $program_data_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
        $program_data_arr[$row[csf('id')]]['spandex_stitch_length']=$row[csf('spandex_stitch_length')];
        $program_data_arr[$row[csf('id')]]['feeder']=$row[csf('feeder')];
        $program_data_arr[$row[csf('id')]]['advice']=$row[csf('advice')];
        $program_data_arr[$row[csf('id')]]['knit_id']=$row[csf('mst_id')];
        $program_data_arr[$row[csf('id')]]['width_dia_type']=$row[csf('width_dia_type')];
        $program_data_arr[$row[csf('id')]]['int_ref']=$bookingInfoArr[$row[csf('booking_no')]]['int_ref'];
        $program_data_arr[$row[csf('id')]]['job_no']=$bookingInfoArr[$row[csf('booking_no')]]['job_no'];
        $program_data_arr[$row[csf('id')]]['brand_id']=$bookingInfoArr[$row[csf('booking_no')]]['brand_id'];
    }
    unset($dataArray);

    if($program_ids!="")
    {
        // echo "SELECT DTLS_ID, SEQ_NO, COUNT_ID, FEEDING_ID FROM PPL_PLANNING_COUNT_FEED_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0";
        $feedingResult =  sql_select("SELECT DTLS_ID, SEQ_NO, COUNT_ID, FEEDING_ID FROM PPL_PLANNING_COUNT_FEED_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0");

        $feedingDataArr = array();
        foreach ($feedingResult as $row)
        {
            $feedingSequence[$row['SEQ_NO']] = $row['SEQ_NO'];
            $feedingDataArr[$row['DTLS_ID']][$row['SEQ_NO']]['count_id'] = $row['COUNT_ID'];
            $feedingDataArr[$row['DTLS_ID']][$row['SEQ_NO']]['feeding_id'] = $row['FEEDING_ID'];
        }
    }

    foreach($ex_mc_id as $mc_id)
    {
        if ($floor_id_all == '') $floor_id_all = $machineId_arr[$mc_id]['floor_id']; else $floor_id_all .= "," . $machineId_arr[$mc_id]['floor_id'];



        $floor_name="";
        $floor_ids = array_filter(array_unique(explode(",", $floor_id_all)));
        // var_dump($floor_ids);
        foreach ($floor_ids as $ids) {
            if ($floor_name == '') $floor_name = $floor_arr[$ids]; else $floor_name .= "," . $floor_arr[$ids];
        }
        //var_dump($floor_name);

        // program array loop
        foreach($program_data_arr as $prog_no=>$prog_data)
        {
            $count_feeding = "";
            foreach($feedingDataArr[$prog_no] as $feedingSequence=>$feedingData)
            {
                if($count_feeding =="")
                {
                    $count_feeding = $feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
                }
                else
                {
                    $count_feeding .= ",".$feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
                }
            }


            $company_id=$prog_data['company_id'];

            //$machineName=$progWiseMachineNoArr[$prog_no];
            //echo $machineName."=";


            ?>
            <style type="text/css">
                .page_break { page-break-after: always;
                }
                #font_size_define{
                    font-size:14px;
                    font-family:'Arial Narrow';
                }
                .font_size_define{
                    font-size:14px;
                    font-family:'Arial Narrow';
                }
                #dataTable tbody tr span{
                    opacity:0.2;
                    color:gray;
                }
                #dataTable tbody tr{
                    vertical-align:middle;
                }
            </style>
            <div style="width:655px;">
                <!--<table width="100%" cellpadding="0" cellspacing="0">-->
                <table width="100%" cellspacing="2" cellpadding="2" border="1" rules="all" class="rpt_table">
                    <tr>
                        <td width="100" align="right" style="border-left:hidden; border-top:hidden; border-right:hidden; border-bottom:hidden;">
                            <img src='../../<? echo $imge_arr[str_replace("'","",$prog_data['company_id'])]; ?>' height='100%' width='100%' alt="not found" />
                        </td>
                        <td colspan="2" width="455" align="center" valign="middle" style="font-size:16px;border-left:hidden; border-top:hidden; border-right:hidden; border-bottom:hidden;"><b><? echo $company_arr[$prog_data['company_id']]; ?></b></td>
                        <td><span  id="barcode_img_id_<? echo $inc_no;?>" ></span></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="border-left:hidden; border-right:hidden;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" width="100%" align="center" style="font-size:20px;border-left:hidden; border-top:hidden; border-right:hidden;"><b>KNIT CARD</b></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>PROGRAM NO</b></td>
                        <td width="300" class="font_size_define"><b><? echo $prog_no; ?></b></td>
                        <td class="font_size_define" ><b>Date : </b></td>
                        <td width="300" class="font_size_define"><b><? echo date('d-m-Y', strtotime($prog_data['program_date'])); ?></b></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>KNIT PARTY</b></td>
                        <td colspan="3" class="font_size_define"><? echo $prog_data['knit_factory']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>BUYER </b></td>
                        <td width="300" class="font_size_define"><? echo $buyer_arr[$prog_data['buyer_id']];; ?></td>
                        <td class="font_size_define" ><b>Brand : </b></td>

                        <td width="300" class="font_size_define"><? echo $buyer_brand_arr[$prog_data['brand_id']]; ?></td>

                    </tr>
                    <tr>
                        <td class="font_size_define"><b>BOOKING NO</b></td>
                        <td width="300" class="font_size_define"><? echo $prog_data['booking_no']; ?></td>
                        <td class="font_size_define" ><b>Job No : </b></td>
                        <td width="300" class="font_size_define"><? echo$prog_data['job_no']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>Textile REF. No</b></td>
                        <td width="300" class="font_size_define"><? echo $prog_data['text_ref']; ?></td>
                        <td class="font_size_define" ><b>Int. Ref  </b></td>

                        <td width="300" class="font_size_define"><? echo rtrim($prog_data['int_ref'],','); ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>Floor Name</b></td>
                        <td colspan="3" class="font_size_define"><? echo $floor_name; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>M/C NO</b></td>
                        <td width="300" class="font_size_define"><? echo  $machine_arr[$mc_id];//chop($programWiseMachineArr[$prog_no],","); // ?></td>
                        <td class="font_size_define" ><b>M/C DIA & GG </b></td>
                        <td width="300" class="font_size_define"><? echo $prog_data['machine_dia']." x ".$prog_data['machine_gg']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>F. DIA</b></td>
                        <td width="300" class="font_size_define"><? echo $prog_data['fabric_dia']." "."[".$fabric_typee[$prog_data['width_dia_type']]."]"; ?></td>
                        <td class="font_size_define" ><b>GSM</b></td>
                        <td width="300" class="font_size_define"><? echo $prog_data['gsm_weight']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>F. TYPE </b></td>
                        <td colspan="3" class="font_size_define"><? echo $prog_data['fabric_desc']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>Color TYPE</b></td>
                        <td width="300" class="font_size_define"><? echo $color_type[$prog_data['color_type_id']]; ?></td>
                        <td class="font_size_define" ><b>COLOR</b></td>
                        <td width="300" class="font_size_define"><? echo $prog_data['color_id']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>COUNT</b></td>
                        <td colspan="3" class="font_size_define"><? echo $prog_data['yarn_dtls']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>LOT</b></td>
                        <td colspan="3" class="font_size_define"><? echo $prog_data['lot']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>BRAND</b></td>
                        <td colspan="3" class="font_size_define"><? echo $prog_data['brand_name']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>SL</b></td>
                        <td width="300" class="font_size_define"><? echo $prog_data['s_length']; ?></td>
                        <td class="font_size_define" ><b>Spandex SL</b></td>
                        <td width="300" class="font_size_define"><? echo $prog_data['spandex_stitch_length']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>P. QTY. (Kg)</b></td>
                        <td width="300" class="font_size_define"><? echo number_format($prog_data['prog_qty'],2);?></td>
                        <td class="font_size_define" ><b>M/C Distrb. Qty</b></td>
                        <td width="300" class="font_size_define"><? echo number_format($machin_prog[$mc_id][$prog_no]['distribution_qnty'],2); ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>Remarks</b></td>
                        <td colspan="3" class="font_size_define"><? echo $prog_data['remarks']; ?></td>
                    </tr>

                </table>
                <br>
                <?
                // echo "select id, body_part_id, grey_size, finish_size, qty_pcs, Needle Per CM from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id in($prog_no) order by id";
                $sql_collarCuff = sql_select("SELECT id, body_part_id, grey_size, finish_size, qty_pcs, needle_per_cm from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id in($prog_no) order by id");
                if (count($sql_collarCuff) > 0)
                {
                ?>
                    <table style="margin-top:10px;" width="655" border="1" rules="all" cellpadding="0" cellspacing="0"
                    class="rpt_table">
                        <thead>
                            <tr>
                                <th width="50">SL</th>
                                <th width="200">Body Part</th>
                                <th width="100">Grey Size</th>
                                <th width="100">Finish Size</th>
                                <th width="100">Quantity Pcs</th10>
                                <th>Needle Per CM</th10>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $i = 1;
                            $total_qty_pcs = 0;
                            foreach ($sql_collarCuff as $row) {
                            if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                            ?>
                            <tr>
                                <td align="center"><p><? echo $i; ?>&nbsp;</p></td>
                                <td><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</p></td>
                                <td style="padding-left:5px"><p><? echo $row[csf('grey_size')]; ?>&nbsp;</p></td>
                                <td style="padding-left:5px"><p><? echo $row[csf('finish_size')]; ?>&nbsp;</p></td>
                                <td align="right"><p><? echo number_format($row[csf('qty_pcs')], 0);
                                    $total_qty_pcs += $row[csf('qty_pcs')]; ?>&nbsp;&nbsp;</p></td>
                                <td align="right"><p><? echo $row[csf('needle_per_cm')]; ?>&nbsp;&nbsp;</p></td>
                                </tr>
                                <?
                                $i++;
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th align="right">Total</th>
                                <th align="right"><? echo number_format($total_qty_pcs, 0); ?>&nbsp;</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                    <?
                }
                ?>
                <br>

                <?

                $sql_strip = "SELECT a.color_number_id, a.stripe_color, a.measurement, a.uom, b.dtls_id, b.no_of_feeder as no_of_feeder from wo_pre_stripe_color a, ppl_planning_feeder_dtls b where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_id and a.color_number_id=b.color_id and a.stripe_color=b.stripe_color_id and b.dtls_id in($prog_no) and a.status_active=1 and a.is_deleted=0 GROUP BY a.color_number_id, a.stripe_color, a.measurement, a.uom, b.dtls_id, b.no_of_feeder"; //and b.no_of_feeder>0
                $result_stripe = sql_select($sql_strip);
                if (count($result_stripe) > 0)
                {
                ?>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
                    <thead>
                        <tr>
                            <th colspan="7">Stripe Measurement</th>
                        </tr>
                        <tr>
                            <th width="30">SL</th>
                            <th width="100">Prog. no</th>
                            <th width="140">Color</th>
                            <th width="130">Stripe Color</th>
                            <th width="100">Measurement</th>
                            <th width="80">UOM</th>
                            <th>No Of Feeder</th>
                        </tr>
                    </thead>
                    <?
                    $i = 1;
                    $tot_feeder = 0;

                    foreach ($result_stripe as $row)
                    {
                        if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                        else
                        $bgcolor = "#FFFFFF";
                        $tot_feeder += $row[csf('no_of_feeder')];
                        ?>
                        <tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
                            <td width="30" align="center"><? echo $i; ?></td>
                            <td width="100" align="center" ><? echo $row[csf('dtls_id')]; ?></td>
                            <td width="140" align="center"><p><? echo $color_library[$row[csf('color_number_id')]]; ?></p></td>
                            <td width="130" align="center"><p><? echo $color_library[$row[csf('stripe_color')]]; ?></p></td>
                            <td width="100" align="center"><? echo $row[csf('measurement')]; ?></td>
                            <td width="80" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                            <td align="right" style="padding-right:10px"><? echo $row[csf('no_of_feeder')]; ?>&nbsp;</td>
                        </tr>
                        <?
                        $tot_masurement += $row[csf('measurement')];
                        $i++;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <th colspan="6"><b>Total : </b></th>
                        <th style="padding-right:10px"><? echo $tot_feeder; ?>&nbsp;</th>
                    </tfoot>
                    </table>
                    <?
                }
                ?>


                <div style="margin-top:10px; width:555px; font-size:14px;"><?
                echo signature_table(213, $company_id, "655px",5);
                ?><!--Note: This is Software Generated Copy, Signature is not Required.--></div>
                <div class="page_break">&nbsp;</div>


            </div>
            <div>
                    <script type="text/javascript" src="../../js/jquery.js"></script>
                    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
                    <script>
                        function generateBarcode( valuess,id ){
                            var value = valuess;//$("#barcodeValue").val();
                            // alert(value)
                            var btype = 'code39';//$("input[name=btype]:checked").val();
                            var renderer ='bmp';// $("input[name=renderer]:checked").val();

                            var settings = {
                            output:renderer,
                            bgColor: '#FFFFFF',
                            color: '#000000',
                            barWidth: 1,
                            barHeight: 30,
                            moduleSize:5,
                            posX: 10,
                            posY: 20,
                            addQuietZone: 1
                            };
                            $("#barcode_img_id_<? echo $inc_no; ?>").html('11');
                            value = {code:value, rect: false};

                            $("#barcode_img_id_<? echo $inc_no; ?>").barcode(value, btype, settings);
                        }
                        generateBarcode('<? echo $prog_no; ?>');
                    </script>
                </div>
            <?
        }
        $inc_no++;
    }

    exit();
}
if ($action == "knitting_card_print_9")
{
    echo load_html_head_contents("Knitting Card Info", "../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $program_ids =  $data;

    if(!$program_ids)
    {
        echo "Program is not found . ";
        die;
    }

    $sub_subcontract = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
    $brand_arr      = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
    $buyer_brand_arr = return_library_array("select id, brand_name from lib_buyer_brand", 'id', 'brand_name');
    $company_arr    = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $imge_arr       = return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    $count_arr      = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 ", 'id', 'yarn_count');
    $color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
    $yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by id, yarn_count","id","yarn_count");
    $floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
    $machineId_arr=array();
    $sql_mc=sql_select("select id, machine_no, floor_id from lib_machine_name");
    $lib_machine_arr=return_library_array("select id,machine_no from  lib_machine_name where status_active=1 and is_deleted=0","id","machine_no");

    foreach( $sql_mc as $row)
    {
        $machineId_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
    }
    unset($sql_mc);


    if ($db_type == 0)
        $item_id_cond="group_concat(distinct(b.item_id))";
    else if ($db_type==2)
        $item_id_cond="LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id)";

    $result_machin_prog = sql_select("SELECT machine_id,dtls_id,distribution_qnty from ppl_planning_info_machine_dtls WHERE DTLS_ID IN($program_ids)");
    $machin_prog = array();
    foreach ($result_machin_prog as $row)
    {
        $machin_prog[$row[csf('machine_id')]][$row[csf('dtls_id')]]['distribution_qnty'] = $row[csf('distribution_qnty')];
    }

    $reqsDataArr = array();
    $program_cond2 = ($program_ids) ? " and knit_id in(".$program_ids.")" : "";
    if ($db_type == 0)
    {
        $reqsData = sql_select("select knit_id, requisition_no as reqs_no, group_concat(distinct(prod_id)) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id");
    }
    else
    {
        $reqsData = sql_select("select knit_id, max(requisition_no) as reqs_no, LISTAGG(prod_id, ',') WITHIN GROUP (ORDER BY prod_id) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id,requisition_no");
    }

    foreach ($reqsData as $row)
    {
        $reqsDataArr[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
        $reqsDataArr[$row[csf('knit_id')]]['prod_id'] = $row[csf('prod_id')];
        $prod_arr[] = $row[csf('prod_id')];
    }
    unset($reqsData);


    //for booking information


    if(!empty($prod_arr))
    {
        $product_details_arr = array();
        $procuct_cond = (!empty($prod_arr))?" and id in(".implode(",",$prod_arr).")":"";
        $pro_sql = sql_select("select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 $procuct_cond");
        foreach ($pro_sql as $row)
        {
            $compos = '';
            if ($row[csf('yarn_comp_percent2nd')] != 0)
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
            }
            else
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
            }

            $product_details_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
            $product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
            $product_details_arr[$row[csf('id')]]['brand_name'] = $row[csf('brand')];
            //$product_details_arr[$row[csf('id')]]['supplier'] = $row[csf('supplier_id')];
            $yarn_details_arr[$row[csf('id')]]['yarn_count'] = $count_arr[$row[csf('yarn_count_id')]];
            $yarn_details_arr[$row[csf('id')]]['yarn_type'] = $yarn_type[$row[csf('yarn_type')]];
            $yarn_details_arr[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
            $yarn_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
            $yarn_details_arr[$row[csf('id')]]['composition'] = $compos;
            $yarn_details_arr[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
        }
        unset($pro_sql);
    }
    //echo "<pre>";
    //print_r($yarn_details_arr);pre_cost_fabric_cost_dtls_id
    $data_sql="SELECT a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type,a.no_of_ply, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id, c.job_no, c.style_ref_no, c.booking_without_order, b.color_type_id,c.within_group,b.pre_cost_fabric_cost_dtls_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b left join fabric_sales_order_mst c on c.id=b.po_id and c.status_active=1 and c.is_deleted=0 where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type,a.no_of_ply, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id, c.job_no, c.style_ref_no, c.booking_without_order, b.color_type_id,c.within_group,b.pre_cost_fabric_cost_dtls_id order by a.id asc";
    //, b.yarn_desc
    //echo $data_sql;
    $dataArray = sql_select($data_sql);
    $bookingNoArr = array();
    //$progNoArr = array();
    $progWiseMachineNoArr = array();
    foreach ($dataArray as $row)
    {
        //for booking no
        $bookingNoArr[$row[csf('booking_no')]] = $row[csf('booking_no')];

        //for prog no
        //$progNoArr[$row[csf('id')]] = $row[csf('id')];
        $progWiseMachineNoArr[$row[csf('id')]] = $row[csf('machine_id')];
        $withInGrpArr[$row[csf('job_no')]]['within_group']=$row[csf('within_group')];
        $withInGrpArr[$row[csf('job_no')]]['pre_cost_fabric_cost_dtls_id']=$row[csf('pre_cost_fabric_cost_dtls_id')];
        $withInGrpArr[$row[csf('job_no')]]['color_id']=$row[csf('color_id')];


    }
    //print_r( $progWiseMachineNoArr);

    $programWiseMachineArr=array();
    /* foreach ($progWiseMachineNoArray as $progNos => $progData)
    {
        foreach ($progData as $keys => $machineIds)
        {
            //$programWiseMachineArr[$progNos].=$machine_arr[$machineIds].",";
            $programWiseMachineArr[$progNos]=$machine_arr[$machineIds];
            $progMacArr[$progNos]=$machineIds;
        }
    }*/
    //print_r( $progMacArr);
    //for booking qty
    $booking_qnty_arr = array();
    $sql_data = sql_select("select a.booking_no, a.buyer_id, sum(b.grey_fab_qnty ) as grey_fab_qnty, a.quality_level from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.fabric_source=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0".where_con_using_array($bookingNoArr, '1', 'a.booking_no')." group by a.booking_no, a.buyer_id, a.quality_level");
    foreach ($sql_data as $row)
    {
        $booking_qnty_arr[$row[csf('booking_no')]]['qty'] += $row[csf('grey_fab_qnty')];
        $booking_qnty_arr[$row[csf('booking_no')]]['buyer'] += $row[csf('buyer_id')];
        //$order_nature_booking_arr[$row[csf('booking_no')]]= $row[csf('quality_level')];
    }
    unset($sql_data);

    //for int. ref.
    $sqlBooking = "SELECT a.grouping AS GROUPING, b.booking_no AS BOOKING_NO, b.job_no AS JOB_NO, c.brand_id AS BRAND_ID FROM wo_po_break_down a, wo_booking_dtls b, wo_po_details_master c  where a.id = b.po_break_down_id AND a.job_no_mst=c.job_no AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0".where_con_using_array($bookingNoArr, '1', 'b.booking_no')." GROUP BY a.grouping, b.booking_no, b.job_no, c.brand_id";
    //echo $sqlBooking;
    $sqlBookingRslt = sql_select($sqlBooking);
    $bookingInfoArr = array();
    foreach($sqlBookingRslt as $row)
    {
        $bookingInfoArr[$row['BOOKING_NO']]['int_ref'] .= $row['GROUPING'].',';
        $bookingInfoArr[$row['BOOKING_NO']]['job_no'] = $row['JOB_NO'];
        $bookingInfoArr[$row['BOOKING_NO']]['brand_id'] = $row['BRAND_ID'];
    }
    unset($sqlBookingRslt);

    //for Style. Brand.// Sample Requisition With Booking
    $sqlSMNBooking = "SELECT b.booking_no AS BOOKING_NO, c.brand_id AS BRAND_ID, c.STYLE_REF_NO
    FROM WO_NON_ORD_SAMP_BOOKING_MST a, WO_NON_ORD_SAMP_BOOKING_DTLS b, SAMPLE_DEVELOPMENT_MST c
    where a.booking_no = b.booking_no AND b.STYLE_ID=c.id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 and a.entry_form_id=140 ".where_con_using_array($bookingNoArr, '1', 'b.booking_no')." GROUP BY b.booking_no, c.brand_id , c.STYLE_REF_NO";
    //echo $sqlSMNBooking;
    $sqlSMNBookingRslt = sql_select($sqlSMNBooking);
    $smnbookingInfoArr = array();
    foreach($sqlSMNBookingRslt as $row)
    {
        $smnbookingInfoArr[$row['BOOKING_NO']]['brand_id'] = $row['BRAND_ID'];
    }
    unset($sqlSMNBookingRslt);

    //for po buyer
    $sqlPoBuyer = sql_select("select sales_booking_no AS BOOKING_NO, po_buyer AS BUYER from fabric_sales_order_mst where status_active = 1 AND is_deleted = 0".where_con_using_array($bookingNoArr, '1', 'sales_booking_no'));
    $poBuyerArr = array();
    foreach($sqlPoBuyer as $row)
    {
        $poBuyerArr[$row['BOOKING_NO']] = $row['BUYER'];
    }
    unset($sqlPoBuyer);

    $company_id = '';
    $orderNo = "";
    $knitting_factory = '';
    $program_data_arr=array();
    $job_no_arr=array();

    foreach ($dataArray as $row)
    {
        $knitting_factory='';
        if ($row[csf('knitting_source')] == 1)
            $knitting_factory = $company_arr[$row[csf('knitting_party')]] . ",";
        else if ($row[csf('knitting_source')] == 3)
            $knitting_factory = $supplier_details[$row[csf('knitting_party')]] . ",";

        $yarn_desc='';
        $lot_no="";
        $brand_name="";
        $yarn_dtls="";
        $yarn_types="";
        if($orderNo=="")
        {
            $orderNo .= $row[csf('po_id')];
            $po_number .= $po_details[$row[csf('po_id')]]['po_number'];
        }
        else
        {
            $orderNo .= ",".$row[csf('po_id')];
            $po_number .= ",".$po_details[$row[csf('po_id')]]['po_number'];
        }

        if($reqsDataArr[$row[csf('id')]]['prod_id'] != '')
        {
            $prod_id = array_unique(explode(",", $reqsDataArr[$row[csf('id')]]['prod_id']));
            foreach ($prod_id as $val)
            {
                $yarn_desc .= $product_details_arr[$val]['desc'] . ", ";
                $lot_no .= $product_details_arr[$val]['lot'] . ", ";

                //$brand_name .= $brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . ", ";
                $brand_name .= $brand_arr[$product_details_arr[$val]['brand_name']]. ", ";
                //$yarn_dtls .= $product_details_arr[$val]['desc'] . ",".$product_details_arr[$val]['lot'] . ",".$brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . "<br>";
                //$yarn_dtls .= $yarn_details_arr[$val]['yarn_count'] . ", ".$yarn_details_arr[$val]['composition'] . ", ".$yarn_details_arr[$val]['yarn_type'].", ".$yarn_details_arr[$val]['color'].', '.$yarn_details_arr[$val]['brand'].", ".$yarn_details_arr[$val]['lot'] . "<br>";
                // $yarn_dtls .= $yarn_details_arr[$val]['yarn_count']. "<br>";
                $yarn_dtls .= $yarn_details_arr[$val]['yarn_count'] . ", ".$yarn_details_arr[$val]['composition'] . ", ".$yarn_details_arr[$val]['yarn_type'].", ".$yarn_details_arr[$val]['color'].', '.$yarn_details_arr[$val]['brand'].", ".$yarn_details_arr[$val]['lot'] . "<br>";
                $yarn_types .=$yarn_details_arr[$val]['yarn_type']. "<br>";
            }

            $yarn_desc = implode(",",array_filter(array_unique(explode(",", substr($yarn_desc, 0, -1)))));
            $lot_no = implode(",",array_filter(array_unique(explode(",", substr($lot_no, 0, -1)))));
            $brand_name = implode(",",array_filter(array_unique(explode(",", substr($brand_name, 0, -1)))));
        }
        $ex_mc_id=array_unique(explode(",",$row[csf('machine_id')]));

        /*$machine_name="";
        foreach($ex_mc_id as $mc_id)
        {
            if($machine_name=='') $machine_name=$machine_arr[$mc_id]; else $machine_name.=','.$machine_arr[$mc_id];
        }*/

        //for color
        $color_name="";
        $ex_color_id=array_unique(explode(",",$row[csf('color_id')]));
        foreach($ex_color_id as $color_id)
        {
            if($color_name=='')
                $color_name=$color_library[$color_id];
            else
                $color_name.=', '.$color_library[$color_id];
        }

        $program_data_arr[$row[csf('id')]]['po_number']=$po_number;
        $program_data_arr[$row[csf('id')]]['co_efficient']=$row[csf('co_efficient')];
        $program_data_arr[$row[csf('id')]]['draft_ratio']=$row[csf('draft_ratio')];
        $program_data_arr[$row[csf('id')]]['start_date']=$row[csf('start_date')];
        $program_data_arr[$row[csf('id')]]['end_date']=$row[csf('end_date')];
        $program_data_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
        $program_data_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
        $program_data_arr[$row[csf('id')]]['color_id']=$color_name;
        //$program_data_arr[$row[csf('id')]]['prog_qty']+=$row[csf('program_qnty')];
        $program_data_arr[$row[csf('id')]]['prog_qty']=$row[csf('program_qnty')];
        $program_data_arr[$row[csf('id')]]['s_length']=$row[csf('stitch_length')];
        $program_data_arr[$row[csf('id')]]['fabric_dia']=$row[csf('fabric_dia')];
        $program_data_arr[$row[csf('id')]]['program_date']=$row[csf('program_date')];
        $program_data_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
        $program_data_arr[$row[csf('id')]]['booking_qty']=$booking_qnty_arr[$row[csf('booking_no')]]['qty'];
        $program_data_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];

        $program_data_arr[$row[csf('id')]]['yarn_dtls']= $yarn_dtls;
        $program_data_arr[$row[csf('id')]]['yarn_types']= $yarn_types;
        $program_data_arr[$row[csf('id')]]['yarn_desc']= $yarn_desc;
        $program_data_arr[$row[csf('id')]]['lot']= $lot_no;
        $program_data_arr[$row[csf('id')]]['brand_name']= $brand_name;
        $program_data_arr[$row[csf('id')]]['mc_nmae']= $machine_name;
        $program_data_arr[$row[csf('id')]]['knit_factory']= $knitting_factory;
        $program_data_arr[$row[csf('id')]]['sub_party']= $supplier_details[$row[csf('subcontract_party')]];
        $program_data_arr[$row[csf('id')]]['gsm_weight']=$row[csf('gsm_weight')];
        $program_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
        $program_data_arr[$row[csf('id')]]['text_ref']= $row[csf('job_no')];
        $program_data_arr[$row[csf('id')]]['color_type_id']= $row[csf('color_type_id')];

        //for buyer
        //$program_data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
        if($booking_qnty_arr[$row[csf('booking_no')]]['buyer'] != '' && $booking_qnty_arr[$row[csf('booking_no')]]['buyer'] != 0)
        {
            $program_data_arr[$row[csf('id')]]['buyer_id']=$booking_qnty_arr[$row[csf('booking_no')]]['buyer'];
        }
        else
        {
            $program_data_arr[$row[csf('id')]]['buyer_id']=$poBuyerArr[$row[csf('booking_no')]];
        }
        $advice = str_replace("\n","\\n",$row[csf("advice")]);
        $program_data_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
        $program_data_arr[$row[csf('id')]]['spandex_stitch_length']=$row[csf('spandex_stitch_length')];
        $program_data_arr[$row[csf('id')]]['feeder']=$row[csf('feeder')];
        $program_data_arr[$row[csf('id')]]['advice'] = $advice;
        $program_data_arr[$row[csf('id')]]['knit_id']=$row[csf('mst_id')];
        $program_data_arr[$row[csf('id')]]['width_dia_type']=$row[csf('width_dia_type')];
        $program_data_arr[$row[csf('id')]]['int_ref']=$bookingInfoArr[$row[csf('booking_no')]]['int_ref'];
        $program_data_arr[$row[csf('id')]]['job_no']=$bookingInfoArr[$row[csf('booking_no')]]['job_no'];

        if ($row[csf('booking_without_order')]==1)
        {
            $program_data_arr[$row[csf('id')]]['brand_id']=$smnbookingInfoArr[$row[csf('booking_no')]]['brand_id'];
        }
        else
        {
            $program_data_arr[$row[csf('id')]]['brand_id']=$bookingInfoArr[$row[csf('booking_no')]]['brand_id'];
        }

        $program_data_arr[$row[csf('id')]]['no_of_ply']=$row[csf('no_of_ply')];
        $program_data_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
        $job_no_arr[$bookingInfoArr[$row[csf('booking_no')]]['job_no']]=$bookingInfoArr[$row[csf('booking_no')]]['job_no'];
    }
    unset($dataArray);

    if($program_ids!="")
    {
        // echo "SELECT DTLS_ID, SEQ_NO, COUNT_ID, FEEDING_ID FROM PPL_PLANNING_COUNT_FEED_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0";
        $feedingResult =  sql_select("SELECT DTLS_ID, SEQ_NO, COUNT_ID, FEEDING_ID FROM PPL_PLANNING_COUNT_FEED_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0");

        $feedingDataArr = array();
        foreach ($feedingResult as $row)
        {
            $feedingSequence[$row['SEQ_NO']] = $row['SEQ_NO'];
            $feedingDataArr[$row['DTLS_ID']][$row['SEQ_NO']]['count_id'] = $row['COUNT_ID'];
            $feedingDataArr[$row['DTLS_ID']][$row['SEQ_NO']]['feeding_id'] = $row['FEEDING_ID'];
        }
    }

    if( count($job_no_arr) > 0 )
    {
        $job_cond =  where_con_using_array($job_no_arr,1,"job_no");
        $washResult =  sql_select("SELECT job_no, emb_type,id  from wo_pre_cost_embe_cost_dtls where emb_name=3 and is_deleted=0 and status_active=1 $job_cond group by job_no, emb_type,id order by id");
        $washTypeArr = array();
        foreach ($washResult as $row)
        {
            $washTypeArr[$row[csf('job_no')]][] = $emblishment_wash_type[$row[csf('emb_type')]];
        }
    }

    $inc_no=1;
    foreach ($progWiseMachineNoArr as $progs => $row)
    {
        $mArr=explode(",", $row);
        foreach ($mArr as $mId)
        {
            $company_id=$program_data_arr[$progs]['company_id'];
            //echo $mId."<br/>";
            //$progWiseMachineNoArray[$progs][$mId] = $mId;

            if ($floor_id_all == '') $floor_id_all = $machineId_arr[$mId]['floor_id']; else $floor_id_all .= "," . $machineId_arr[$mId]['floor_id'];
            $floor_name="";
            $floor_ids = array_filter(array_unique(explode(",", $floor_id_all)));
            // var_dump($floor_ids);
            foreach ($floor_ids as $ids) {
                if ($floor_name == '') $floor_name = $floor_arr[$ids]; else $floor_name .= "," . $floor_arr[$ids];
            }
            //var_dump($floor_name);

            ?>
            <style type="text/css">
                .page_break { page-break-after: always;
                }
                #font_size_define{
                    font-size:14px;
                    font-family:'Arial Narrow';
                }
                .font_size_define{
                    font-size:14px;
                    font-family:'Arial Narrow';
                }
                #dataTable tbody tr span{
                    opacity:0.2;
                    color:gray;
                }
                #dataTable tbody tr{
                    vertical-align:middle;
                }
            </style>
            <div style="width:755px;">
                <!--<table width="100%" cellpadding="0" cellspacing="0">-->
                <table width="100%" cellspacing="2" cellpadding="2" border="1" rules="all" class="rpt_table">
                    <tr>
                        <td width="100" align="right" style="border-left:hidden; border-top:hidden; border-right:hidden; border-bottom:hidden;">
                            <img src='../../<? echo $imge_arr[str_replace("'","",$program_data_arr[$progs]['company_id'])]; ?>' height='100%' width='100%' alt="not found" />
                        </td>
                        <td colspan="1" width="455" align="center" valign="middle" style="font-size:16px;border-left:hidden; border-top:hidden; border-right:hidden; border-bottom:hidden;"><b><? echo $company_arr[$program_data_arr[$progs]['company_id']]; ?></b></td>
                        <td style="border-left:hidden;border-right:hidden; border-top:hidden; border-bottom:hidden;width:300px;">
                            <span  id="barcode_img_id_<? echo $inc_no;?>" ></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="border-left:hidden; border-right:hidden;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" width="100%" align="center" style="font-size:20px;border-left:hidden; border-top:hidden; border-right:hidden;"><b>KNIT CARD</b></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>PROGRAM NO</b></td>
                        <td width="300" class="font_size_define"><b><? echo  $progs;//$prog_no; ?></b></td>
                        <td class="font_size_define" ><b>Date : </b></td>
                        <td width="300" class="font_size_define"><b><? echo date('d-m-Y', strtotime($program_data_arr[$progs]['program_date'])); ?></b></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>KNIT PARTY</b></td>
                        <td colspan="3" class="font_size_define"><? echo $program_data_arr[$progs]['knit_factory']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>BUYER </b></td>
                        <td width="300" class="font_size_define"><? echo $buyer_arr[$program_data_arr[$progs]['buyer_id']];; ?></td>
                        <td class="font_size_define" ><b>Brand : </b></td>

                        <td width="300" class="font_size_define"><? echo $buyer_brand_arr[$program_data_arr[$progs]['brand_id']]; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>BOOKING NO</b></td>
                        <td width="300" class="font_size_define"><? echo $program_data_arr[$progs]['booking_no']; ?></td>
                        <td class="font_size_define" ><b>Job No : </b></td>
                        <td width="300" class="font_size_define"><? echo $program_data_arr[$progs]['job_no']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>Textile REF. No</b></td>
                        <td width="300" class="font_size_define"><? echo $program_data_arr[$progs]['text_ref']; ?></td>
                        <td class="font_size_define" ><b>Int. Ref  </b></td>

                        <td width="300" class="font_size_define"><? echo rtrim($program_data_arr[$progs]['int_ref'],','); ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>Floor Name</b></td>
                        <td class="font_size_define"><? echo $floor_name; ?></td>
                        <td class="font_size_define"><b>Style Ref.</b></td>
                        <td class="font_size_define"><? echo  $program_data_arr[$progs]['style_ref_no']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>M/C NO</b></td>
                        <td width="300" class="font_size_define"><? echo  $machine_arr[$mId];//chop($programWiseMachineArr[$prog_no],","); // ?></td>
                        <td class="font_size_define" ><b>M/C DIA & GG </b></td>
                        <td width="300" class="font_size_define"><? echo $program_data_arr[$progs]['machine_dia']." x ".$program_data_arr[$progs]['machine_gg']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>F. DIA</b></td>
                        <td width="300" class="font_size_define"><? echo $program_data_arr[$progs]['fabric_dia']." "."[".$fabric_typee[$program_data_arr[$progs]['width_dia_type']]."]"; ?></td>
                        <td class="font_size_define" ><b>GSM</b></td>
                        <td width="300" class="font_size_define"><? echo $program_data_arr[$progs]['gsm_weight']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>F. TYPE </b></td>
                        <td colspan="3" class="font_size_define"><? echo $program_data_arr[$progs]['fabric_desc']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>Color TYPE</b></td>
                        <td width="300" class="font_size_define"><? echo $color_type[$program_data_arr[$progs]['color_type_id']]; ?></td>
                        <td class="font_size_define" ><b>COLOR</b></td>
                        <td width="300" class="font_size_define"><? echo $program_data_arr[$progs]['color_id']; ?></td>
                    </tr>
                    <tr>

                    <tr>
                        <td width="80"  class="font_size_define"><b>Yarn Details</b></td>
                        <td width="200" colspan="3" class="font_size_define"><? echo $program_data_arr[$progs]['yarn_dtls']; ?></td>
                    </tr>

                    <!-- <td class="font_size_define"><b>COUNT 777</b></td>
                        <td width="300" class="font_size_define"><? //echo $program_data_arr[$progs]['yarn_dtls']; ?></td>
                        <td class="font_size_define" ><b>Feeder</b></td>
                        <td width="300" class="font_size_define"><? //echo $feeder[$program_data_arr[$progs]['feeder']]; ?></td>
                    </tr> -->
                    <tr>
                        <td class="font_size_define"><b>LOT</b></td>
                        <td colspan="3" class="font_size_define"><? echo $program_data_arr[$progs]['lot']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>BRAND</b></td>
                        <td width="300" class="font_size_define"><? echo $program_data_arr[$progs]['brand_name'];?></td>
                        <td class="font_size_define"><b>Wash type</b></td>
                        <td width="300" class="font_size_define"><? echo implode(",", array_unique($washTypeArr[$program_data_arr[$progs]['job_no']]));?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>SL</b></td>
                        <td width="300" class="font_size_define"><? echo $program_data_arr[$progs]['s_length']; ?></td>
                        <td class="font_size_define" ><b>Spandex SL</b></td>
                        <td width="300" class="font_size_define"><? echo $program_data_arr[$progs]['spandex_stitch_length']; ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>P. QTY. (Kg)</b></td>
                        <td width="300" class="font_size_define"><? echo number_format($program_data_arr[$progs]['prog_qty'],2);?></td>
                        <td class="font_size_define" ><b>M/C Distrb. Qty</b></td>
                        <td width="300" class="font_size_define"><? echo number_format($machin_prog[$mId][$progs]['distribution_qnty'],2); ?></td>
                    </tr>
                    <tr>
                        <td class="font_size_define"><b>Remarks</b></td>
                        <td class="font_size_define"><? echo $program_data_arr[$progs]['remarks']; ?></td>
                        <td class="font_size_define"><b>No Of Ply</b></td>
                        <td class="font_size_define"><? echo $program_data_arr[$progs]['no_of_ply']; ?></td>
                    </tr>



                </table>
                <br>
                <table width="100%" cellspacing="2" cellpadding="2" border="1" rules="all" class="rpt_table">
                    <tr>
                        <td width="100" class="font_size_define"><b>Advice</b></td>
                        <td class="font_size_define" colspan="3"><? echo $program_data_arr[$progs]['advice']; ?></td>
                    </tr>
                </table>
                <?
                // echo "select id, body_part_id, grey_size, finish_size, qty_pcs, Needle Per CM from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id in($progs) order by id";
                $sql_collarCuff = sql_select("SELECT id, body_part_id, grey_size, finish_size, qty_pcs, needle_per_cm from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id in($progs) order by id");
                if (count($sql_collarCuff) > 0)
                {
                    ?>
                    <table style="margin-top:10px;" width="655" border="1" rules="all" cellpadding="0" cellspacing="0"
                    class="rpt_table">
                        <thead>
                            <tr>
                                <th width="50">SL</th>
                                <th width="200">Body Part</th>
                                <th width="100">Grey Size</th>
                                <th width="100">Finish Size</th>
                                <th width="100">Quantity Pcs</th10>
                                <th>Needle Per CM</th10>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $i = 1;
                            $total_qty_pcs = 0;
                            foreach ($sql_collarCuff as $row) {
                            if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                            ?>
                            <tr>
                                <td align="center"><p><? echo $i; ?>&nbsp;</p></td>
                                <td><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</p></td>
                                <td style="padding-left:5px"><p><? echo $row[csf('grey_size')]; ?>&nbsp;</p></td>
                                <td style="padding-left:5px"><p><? echo $row[csf('finish_size')]; ?>&nbsp;</p></td>
                                <td align="right"><p><? echo number_format($row[csf('qty_pcs')], 0);
                                    $total_qty_pcs += $row[csf('qty_pcs')]; ?>&nbsp;&nbsp;</p></td>
                                <td align="right"><p><? echo $row[csf('needle_per_cm')]; ?>&nbsp;&nbsp;</p></td>
                                </tr>
                                <?
                                $i++;
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th align="right">Total</th>
                                <th align="right"><? echo number_format($total_qty_pcs, 0); ?>&nbsp;</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                    <?
                }
                ?>
                <br>

                <?

                    $sql_fedder = sql_select("select a.id, a.color_id, a.stripe_color_id, a.no_of_feeder, max(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.dtls_id IN(".$progs.") and b.job_no in('".$program_data_arr[$progs]['text_ref']."') and a.no_of_feeder>0 group by a.id, a.color_id, a.stripe_color_id, a.no_of_feeder order by a.id");
                   if (count($sql_fedder) > 0) {
                       ?>
                       <table style="margin-top:10px;" width="650" border="1" rules="all" cellpadding="0" cellspacing="0"
                       class="rpt_table">
                       <thead>
                            <tr>
                                <th colspan="7">Stripe Measurement</th>
                            </tr>
                           <tr>
                               <th width="50">SL</th>
                               <th width="100">Program No</th>
                               <th width="120">Color</th>
                               <th width="120">Stripe Color</th>
                               <th width="100">Measurement</th>
                               <th width="100">UOM</th>
                               <th>No Of Feeder</th>
                           </tr>
                       </thead>
                       <tbody>

                           <?
                           $i = 1;
                           $total_feeder = 0;
                           foreach ($sql_fedder as $row) {
                               if ($i % 2 == 0)
                                   $bgcolor = "#E9F3FF";
                               else
                                   $bgcolor = "#FFFFFF";
                               ?>

                               <tr>
                                   <td align="center"><p><? echo $i; ?>&nbsp;</p></td>
                                   <td align="center"><p><? echo $progs; ?>&nbsp;</p></td>
                                   <td align="center"><p><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</p></td>
                                   <td align="center"><p><? echo $color_library[$row[csf('stripe_color_id')]]; ?>&nbsp;</p></td>
                                   <td align="right"><p><? echo number_format($row[csf('measurement')], 2); ?>&nbsp;</p></td>
                                   <td align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
                                   <td align="right"><p><? echo number_format($row[csf('no_of_feeder')], 0);
                                   $total_feeder += $row[csf('no_of_feeder')]; ?>&nbsp;</p></td>
                               </tr>
                               <?
                               $i++;
                           }
                           ?>
                       </tbody>
                       <tfoot>
                           <tr>
                               <th></th>
                               <th></th>
                               <th></th>
                               <th></th>
                               <th></th>
                               <th align="right">Total:</th>
                               <th align="right"><? echo number_format($total_feeder, 0); ?></th>
                           </tr>
                       </tfoot>
                   </table>
                   <?
               }








                ?>


                <div style="margin-top:10px; width:555px; font-size:14px;"><?
                echo signature_table(213, $company_id, "655px",5);
                ?><!--Note: This is Software Generated Copy, Signature is not Required.--></div>
                <div class="page_break">&nbsp;</div>


            </div>

            <?

        }

        ?>
        <div>
                <script type="text/javascript" src="../../js/jquery.js"></script>
                <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
                <script>
                    function generateBarcode( valuess,id ){
                        var value = valuess;//$("#barcodeValue").val();
                        // alert(value)
                        var btype = 'code39';//$("input[name=btype]:checked").val();
                        var renderer ='bmp';// $("input[name=renderer]:checked").val();

                        var settings = {
                        output:renderer,
                        bgColor: '#FFFFFF',
                        color: '#000000',
                        barWidth: 2,
                        barHeight: 30,
                        moduleSize:5,
                        posX: 10,
                        posY: 20,
                        addQuietZone: 1
                        };
                        $("#barcode_img_id_<? echo $inc_no; ?>").html('11');
                        value = {code:value, rect: false};

                        $("#barcode_img_id_<? echo $inc_no; ?>").barcode(value, btype, settings);
                    }
                    generateBarcode('<? echo $progs; ?>');
                </script>
            </div>
            <?
       $inc_no++;
    }
    exit();
}

if ($action == "knitting_card_print_8_08012022")
{
    echo load_html_head_contents("Knitting Card Info", "../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $program_ids =  $data;

    if(!$program_ids)
    {
        echo "Program is not found.";
        die;
    }

    //$sub_subcontract = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
    $brand_arr      = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
    $company_arr    = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $imge_arr       = return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    $count_arr      = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 ", 'id', 'yarn_count');
    $color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

    if ($db_type == 0)
        $item_id_cond="group_concat(distinct(b.item_id))";
    else if ($db_type==2)
        $item_id_cond="LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id)";

    //for machine distrubution qty
    $result_machin_prog = sql_select("SELECT machine_id, dtls_id, distribution_qnty from ppl_planning_info_machine_dtls WHERE DTLS_ID IN(".$program_ids.")");
    $machin_prog = array();
    foreach ($result_machin_prog as $row)
    {
        $machin_prog[$row[csf('machine_id')]][$row[csf('dtls_id')]]['distribution_qnty'] = $row[csf('distribution_qnty')];
    }
    //echo "<pre>";
    //print_r($machin_prog);
    //echo "</pre>";

    $reqsDataArr = array();
    $program_cond2 = ($program_ids) ? " and knit_id in(".$program_ids.")" : "";

    if ($db_type == 0)
    {
        $reqsData = sql_select("select knit_id, requisition_no as reqs_no, group_concat(distinct(prod_id)) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id");
    }
    else
    {
        $reqsData = sql_select("select knit_id, max(requisition_no) as reqs_no, LISTAGG(prod_id, ',') WITHIN GROUP (ORDER BY prod_id) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id,requisition_no");
    }

    $prog_prod_arr = array();
    foreach ($reqsData as $row)
    {
        $reqsDataArr[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
        $reqsDataArr[$row[csf('knit_id')]]['prod_id'] = $row[csf('prod_id')];
        $prod_arr[] = $row[csf('prod_id')];

        $expProd_id = explode(',', $row[csf('prod_id')]);
        foreach($expProd_id as $key=>$val)
        {
            $prog_prod_arr[$row[csf('knit_id')]][$val] = $val;
        }
    }
    //echo "<pre>";
    //print_r($prog_prod_arr);
    //echo "</pre>";

    if(!empty($prod_arr))
    {
        $product_details_arr = array();
        $procuct_cond = (!empty($prod_arr))?" and id in(".implode(",",$prod_arr).")":"";
        $pro_sql = sql_select("select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand, product_name_details from product_details_master where item_category_id=1 $procuct_cond");
        foreach ($pro_sql as $row)
        {
            $compos = '';
            if ($row[csf('yarn_comp_percent2nd')] != 0)
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
            }
            else
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
            }

            $product_details_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
            $product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
            $product_details_arr[$row[csf('id')]]['brand_name'] = $row[csf('brand')];
            //$product_details_arr[$row[csf('id')]]['supplier'] = $row[csf('supplier_id')];
            $yarn_details_arr[$row[csf('id')]]['yarn_count'] = $count_arr[$row[csf('yarn_count_id')]];
            $yarn_details_arr[$row[csf('id')]]['yarn_type'] = $yarn_type[$row[csf('yarn_type')]];
            $yarn_details_arr[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
            $yarn_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
            $yarn_details_arr[$row[csf('id')]]['composition'] = $compos;

            $yarn_details_arr[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
        }

        //for yarn test
        $sql_yarn_test = sql_select("SELECT a.prod_id AS PROD_ID, b.comments_knit COMMENTS_KNIT FROM inv_yarn_test_mst a, INV_YARN_TEST_COMMENTS b WHERE a.id = b.mst_table_id AND a.prod_id IN(".implode(",",$prod_arr).") AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0");
        $yarn_test_info = array();
        foreach($sql_yarn_test as $yarn_test)
        {
            $yarn_test_info[$yarn_test['PROD_ID']]['comments_knit'] = $yarn_test['COMMENTS_KNIT'];
        }
    }

    //echo "<pre>";
    //print_r($yarn_details_arr);

    /*
    $booking_qnty_arr = array();
    $sql_data = sql_select("select a.booking_no, sum(b.grey_fab_qnty ) as grey_fab_qnty,a.quality_level from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.fabric_source=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.booking_no,a.quality_level");
    foreach ($sql_data as $row)
    {
        $booking_qnty_arr[$row[csf('booking_no')]] += $row[csf('grey_fab_qnty')];
        $order_nature_booking_arr[$row[csf('booking_no')]]= $row[csf('quality_level')];
    }
    unset($sql_data);
    */

        /*$product_details_array = array();
        $sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0";
        $result = sql_select($sql);

        foreach ($result as $row)
        {
            $compos = '';
            if ($row[csf('yarn_comp_percent2nd')] != 0)
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
            }
            else
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
            }

            $product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
            $product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
        }
        unset($result);*/
        $order_no = '';
        $buyer_name = '';
        $knitting_factory = '';
        $job_no = '';
        $booking_no = '';
        $company = '';
        $jobNo="";
        $poQuantity="";

        /*
        $job_data_sql=sql_select("select a.id,a.grouping, a.job_no_mst,a.po_number, sum(a.po_quantity) as poQuantity, b.booking_no, c.style_ref_no from wo_po_break_down a, ppl_planning_entry_plan_dtls b, wo_po_details_master c where a.id=b.po_id and b.dtls_id in (".$program_ids.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst=job_no group by a.id, a.grouping, a.job_no_mst, a.po_number, b.booking_no, c.style_ref_no");
        $po_details= array();
        foreach($job_data_sql as $row)
        {
            $jobNo      = $row[csf('job_no_mst')];
            $poQuantity = $row[csf('poQuantity')];
            $style      = $row[csf('style_ref_no')];
            $po_details[$row[csf('id')]]['po_number']= $row[csf('po_number')];
            $ref_no     = $row[csf('grouping')];
            $order_nature=$order_nature_booking_arr[$row[csf('booking_no')]];
        }
        unset($job_data_sql);
        */

        $data_sql="select a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, a.dye_type, b.buyer_id, b.customer_buyer, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in (".$program_ids.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, a.dye_type, b.buyer_id, b.customer_buyer, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id";
        //, b.yarn_desc
        $dataArray = sql_select($data_sql);
        $sales_id_arr = array();
        foreach ($dataArray as $row)
        {
            $sales_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
        }

        //for sales order infomation
        $sql_fab_sales = sql_select("SELECT id AS ID, job_no AS JOB_NO, style_ref_no AS STYLE_REF_NO, buyer_id AS BUYER_ID FROM fabric_sales_order_mst WHERE id IN(".implode(',',$sales_id_arr).")");
        $fab_sales_arr = array();
        foreach($sql_fab_sales as $row)
        {
            $fab_sales_arr[$row['ID']]['sales_order_no'] = $row['JOB_NO'];
            $fab_sales_arr[$row['ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
            $fab_sales_arr[$row['ID']]['buyer_id'] = $row['BUYER_ID'];
        }

        $program_data_arr=array();
        $sql = "select count_id,feeding_id from ppl_planning_count_feed_dtls where dtls_id=".$dataArray[0][csf('id')]." order by seq_no";
        $data_array = sql_select($sql);
        $count_feeding_data="";
        foreach ($data_array as $row)
        {
            //$count_feeding_data_arr[]=$row[csf('count_id')].'_'.$row[csf('feeding_id')];
            if($count_feeding_data !="") $count_feeding_data .=",";
            $count_feeding_data .= $count_arr[$row[csf('count_id')]].'-'.$feeding_arr[$row[csf('feeding_id')]];
        }

        $company_id = '';
        $buyer_name = '';
        $booking_no = '';
        $orderNo = "";
        foreach ($dataArray as $row)
        {
            $knitting_factory='';
            if ($row[csf('knitting_source')] == 1)
                $knitting_factory = $company_arr[$row[csf('knitting_party')]] . ",";
            else if ($row[csf('knitting_source')] == 3)
                $knitting_factory = $supplier_details[$row[csf('knitting_party')]] . ",";

            $yarn_desc='';
            $lot_no="";
            $brand_name=array();
            $yarn_dtls="";
            /*if($orderNo=="")
            {
                $orderNo .= $row[csf('po_id')];
                $po_number .= $po_details[$row[csf('po_id')]]['po_number'];
            }
            else
            {
                $orderNo .= ",".$row[csf('po_id')];
                $po_number .= ",".$po_details[$row[csf('po_id')]]['po_number'];
            }*/

            $prod_id = array_unique(explode(",", $reqsDataArr[$row[csf('id')]]['prod_id']));
            foreach ($prod_id as $val)
            {
                $yarn_desc .= $product_details_arr[$val]['desc'] . ",";
                $lot_no .= $product_details_arr[$val]['lot'] . ",";
                $brand_name[$product_details_arr[$val]['brand_name']] = $brand_arr[$product_details_arr[$val]['brand_name']];
                //$yarn_dtls .= $product_details_arr[$val]['desc'] . ",".$product_details_arr[$val]['lot'] . ",".$brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . "<br>";
                $yarn_dtls .= $yarn_details_arr[$val]['yarn_count'] . ", ".$yarn_details_arr[$val]['composition'] . ", ".$yarn_details_arr[$val]['yarn_type'].", ".$yarn_details_arr[$val]['color'].', '.$yarn_details_arr[$val]['brand'].", ".$yarn_details_arr[$val]['lot'] . "<br>";
            }

            $yarn_desc = implode(", ",array_filter(array_unique(explode(",", substr($yarn_desc, 0, -1)))));
            $lot_no = implode(", ",array_filter(array_unique(explode(",", substr($lot_no, 0, -1)))));
            $brand_name = implode(", ", $brand_name);
            $ex_mc_id=array_unique(explode(",",$row[csf('machine_id')]));

            /*$machine_name="";
            foreach($ex_mc_id as $mc_id)
            {
                if($machine_name=='') $machine_name=$machine_arr[$mc_id]; else $machine_name.=','.$machine_arr[$mc_id];
            }*/

            $color_name="";
            $ex_color_id=array_unique(explode(",",$row[csf('color_id')]));
            foreach($ex_color_id as $color_id)
            {
                if($color_name=='') $color_name=$color_library[$color_id];
                else $color_name.=','.$color_library[$color_id];
            }

            //$program_data_arr[$row[csf('id')]]['po_number']=$po_number;
            $program_data_arr[$row[csf('id')]]['co_efficient']=$row[csf('co_efficient')];
            $program_data_arr[$row[csf('id')]]['draft_ratio']=$row[csf('draft_ratio')];
            $program_data_arr[$row[csf('id')]]['start_date']=$row[csf('start_date')];
            $program_data_arr[$row[csf('id')]]['end_date']=$row[csf('end_date')];
            $program_data_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
            $program_data_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
            $program_data_arr[$row[csf('id')]]['color_id']=$color_name;
            //$program_data_arr[$row[csf('id')]]['prog_qty']+=$row[csf('program_qnty')];
            $program_data_arr[$row[csf('id')]]['prog_qty']=$row[csf('program_qnty')];
            $program_data_arr[$row[csf('id')]]['s_length']=$row[csf('stitch_length')];
            $program_data_arr[$row[csf('id')]]['fabric_dia']=$row[csf('fabric_dia')];
            $program_data_arr[$row[csf('id')]]['program_date']=$row[csf('program_date')];
            $program_data_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
            $program_data_arr[$row[csf('id')]]['booking_qty']=$booking_qnty_arr[$row[csf('booking_no')]];
            $program_data_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];

            $program_data_arr[$row[csf('id')]]['machine_id']=$row[csf('machine_id')];
            $program_data_arr[$row[csf('id')]]['knitting_party']=$row[csf('knitting_party')];
            $program_data_arr[$row[csf('id')]]['location_id']=$row[csf('location_id')];

            $program_data_arr[$row[csf('id')]]['yarn_dtls']= $yarn_dtls;
            $program_data_arr[$row[csf('id')]]['yarn_desc']= $yarn_desc;
            $program_data_arr[$row[csf('id')]]['lot']= $lot_no;
            $program_data_arr[$row[csf('id')]]['brand_name']= $brand_name;
            $program_data_arr[$row[csf('id')]]['mc_nmae']= $machine_name;
            $program_data_arr[$row[csf('id')]]['knit_factory']= $knitting_factory;
            $program_data_arr[$row[csf('id')]]['sub_party']= $supplier_details[$row[csf('subcontract_party')]];
            $program_data_arr[$row[csf('id')]]['gsm_weight']=$row[csf('gsm_weight')];
            $program_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];

            //$program_data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
            $program_data_arr[$row[csf('id')]]['buyer_id']=$fab_sales_arr[$row[csf('po_id')]]['buyer_id'];
            $program_data_arr[$row[csf('id')]]['customer_buyer']=$row[csf('customer_buyer')];

            $program_data_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
            $program_data_arr[$row[csf('id')]]['spandex_stitch_length']=$row[csf('spandex_stitch_length')];
            $program_data_arr[$row[csf('id')]]['feeder']=$row[csf('feeder')];
            $program_data_arr[$row[csf('id')]]['advice']=$row[csf('advice')];
            $program_data_arr[$row[csf('id')]]['knit_id']=$row[csf('mst_id')];
            $program_data_arr[$row[csf('id')]]['width_dia_type']=$row[csf('width_dia_type')];
            $program_data_arr[$row[csf('id')]]['dye_type']=$row[csf('dye_type')];
            //$program_data_arr[$row[csf('id')]]['style']=$style;
            $program_data_arr[$row[csf('id')]]['style']=$fab_sales_arr[$row[csf('po_id')]]['style_ref_no'];
            $program_data_arr[$row[csf('id')]]['sales_order_no']=$fab_sales_arr[$row[csf('po_id')]]['sales_order_no'];
            $program_data_arr[$row[csf('id')]]['requisition_no']=$reqsDataArr[$row[csf('id')]]['reqs_no'];
        }
        unset($dataArray);

        /*if($orderNo!="")
        {
            $sql_tna = sql_select("select job_no,po_number_id,task_start_date,task_finish_date FROM tna_process_mst WHERE status_active=1 AND is_deleted=0 and po_number_id in ($orderNo) and task_number=60");
            $tnaData = array();

            if(!empty($sql_tna))
            {
                foreach($sql_tna as $tna_row)
                {
                    $tnaData[$tna_row[csf('job_no')]][$tna_row[csf('po_number_id')]]['task_start_date'] =  $tna_row[csf('task_start_date')];

                    $tnaData[$tna_row[csf('job_no')]][$tna_row[csf('po_number_id')]]['task_finish_date'] =  $tna_row[csf('task_finish_date')];
                }
            }

        }*/
        //$knit_id_arr = return_library_array("select a.requisition_no from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b where a.knit_id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.id in($program_ids) group by a.requisition_no", "requisition_no", "requisition_no");
        //echo "<pre>";
        //print_r($ex_mc_id);

        $barcode_sl=1;
        $program_array = array();
        foreach($ex_mc_id as $mc_id)
        {
            // program array loop
            foreach($program_data_arr as $prog_no=>$prog_data)
            {
            ?>
            <style type="text/css">
                .page_break { page-break-after: always;
                }
                #font_size_define{
                    font-size:14px;
                    font-family:'Arial Narrow';
                }
                .font_size_define{
                    font-size:14px;
                    font-family:'Arial Narrow';
                }
                #dataTable tbody tr span{
                     opacity:0.2;
                     color:gray;
                }
                #dataTable tbody tr{
                    vertical-align:middle;
                }
            </style>
            <div style="width:1180px;">
                <table width="100%" cellpadding="0" cellspacing="0" >
                    <tr>
                        <td width="70" align="right">
                            <img  src='../../<? echo $imge_arr[str_replace("'","",$prog_data['company_id'])]; ?>' height='100%' width='100%' />
                        </td>
                        <td>
                            <table width="100%" style="margin-top:10px">
                                <tr>
                                    <td width="100%" align="center" style="font-size:20px;"><b><? echo $company_arr[$prog_data['company_id']]; ?></b></td>
                                </tr>
                                <tr>
                                    <td align="center" style="font-size:14px"><? echo show_company($prog_data['company_id'], '', ''); ?></td>
                                </tr>
                                <tr>
                                    <td width="100%" align="center" style="font-size:16px;"><b><u>Knit Card</u></b> <b style=" float:right;color:#000"><? if($fbooking_order_nature[$order_nature]) echo "(".$fbooking_order_nature[$order_nature].")".'&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;';else echo " ";?></b></td>
                                </tr>
                                <tr>
                                    <td width="100%" align="right">
                                    <div id="div_<?php echo $barcode_sl;?>"></div>
                                    <?php
                                        //$program_array[$barcode_sl] = $prog_no."-".$prog_data['knitting_party'];
                                        $program_array[$barcode_sl] = $prog_no."-".$prog_data['knitting_party']."-".$mc_id."-".$prog_data['location_id'];
                                        $barcode_sl++;
                                     ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <div style="margin-top:5px; width:1180px">
                    <table cellspacing="2" cellpadding="2" border="1" rules="all" width="100%" class="rpt_table" style="" id="dataTable">
                        <thead height="25">
                            <th colspan="2" width="230" id="font_size_define">Program Details</th>
                            <th colspan="2" width="233" id="font_size_define">Job Details</th>
                            <th colspan="2" width="250" id="font_size_define">Yarn/Fabric Details</th>
                            <th colspan="2" width="233" id="font_size_define">M/C Details</th>
                            <th colspan="2" width="233" id="font_size_define">Technical Details</th>
                        </thead>
                        <tbody>
                            <tr height="22">
                                <td width="100" class="font_size_define">Knit Party</td>
                                <td width="132" class="font_size_define"><? echo $prog_data['knit_factory']; ?></td>
                                <td width="100" class="font_size_define">Cust. Buyer</td>
                                <td width="132" class="font_size_define"><? echo $buyer_arr[$prog_data['customer_buyer']]; ?></td>
                                <td width="80" class="font_size_define">Req. No</td>
                                <td width="170" class="font_size_define"><? echo $prog_data['requisition_no']; ?></td>
                                <td width="100" class="font_size_define">M/C No</td>
                                <td width="132" class="font_size_define"><? echo $machine_arr[$mc_id];?></td>
                                <td width="100" class="font_size_define">Stitch Length</td>
                                <td width="132" class="font_size_define"><? echo $prog_data['s_length']; ?></td>
                            </tr>
                            <tr height="22">
                                <td class="font_size_define">Program No</td>
                                <td class="font_size_define"><? echo $prog_no; ?></td>
                                <td class="font_size_define">Customer</td>
                                <td class="font_size_define"><? echo $buyer_arr[$prog_data['buyer_id']]; ?></td>
                                <td class="font_size_define" rowspan="4">Yarn Desc</td>
                                <td class="font_size_define" rowspan="4"><? echo $prog_data['yarn_desc']; ?></td>
                                <td class="font_size_define">Dia x Gauge</td>
                                <td class="font_size_define"><? echo $prog_data['machine_dia']." x ".$prog_data['machine_gg']; ?></td>
                                <td class="font_size_define">Span. Stitch Length</td>
                                <td class="font_size_define"><? echo $prog_data['spandex_stitch_length']; ?></td>
                            </tr>
                            <tr height="22">
                                <td class="font_size_define">Program Date</td>
                                <td class="font_size_define"><? echo change_date_format($prog_data['program_date']); ?></td>
                                <td class="font_size_define">Sales Job/Booking  No</td>
                                <td class="font_size_define"><? echo $prog_data['booking_no']; ?></td>
                                <td class="font_size_define">Finished Dia</td>
                                <td class="font_size_define"><? echo $prog_data['fabric_dia']." "."[".$fabric_typee[$prog_data['width_dia_type']]."]"; ?></td>
                                <td class="font_size_define">M/C RPM</td>
                                <td class="font_size_define"><span>Write&nbsp;</span></td>
                            </tr>
                            <tr height="22">
                                <td class="font_size_define">Program Qty</td>
                                <td class="font_size_define"><? echo number_format($prog_data['prog_qty'],2);?></td>
                                <td class="font_size_define">Sales Order No</td>
                                <td class="font_size_define"><? echo $prog_data['sales_order_no']; ?></td>
                                <td class="font_size_define">Fabric Type</td>
                                <td class="font_size_define"><? echo $prog_data['fabric_desc']; ?></td>
                                <td class="font_size_define">Counter</td>
                                <td class="font_size_define"><span>Write&nbsp;</span></td>
                            </tr>
                            <tr height="22">
                                <td class="font_size_define">Target/Shift</td>
                                <td class="font_size_define"><span>Write&nbsp;</span></td>
                                <td class="font_size_define">Style</td>
                                <td class="font_size_define"><? echo $prog_data['style']; ?></td>
                                <td class="font_size_define">FGSM</td>
                                <td class="font_size_define"><? echo $prog_data['gsm_weight']; ?></td>
                                <td class="font_size_define">Feeder</td>
                                <td class="font_size_define"><? echo $feeder[$prog_data['feeder']]; ?></td>
                            </tr>
                            <tr height="22">
                                <td class="font_size_define">Program Start</td>
                                <td class="font_size_define"><? echo change_date_format($prog_data['start_date']); ?></td>
                                <td class="font_size_define">Color</td>
                                <td class="font_size_define"><? echo $prog_data['color_id']; ?></td>
                                <td class="font_size_define">Yarn Brand</td>
                                <td class="font_size_define"><? echo $prog_data['brand_name']; ?></td>
                                <td class="font_size_define">M/C Target QTY</td>
                                <td class="font_size_define"><? echo $machin_prog[$mc_id][$prog_no]['distribution_qnty']; ?></td>
                                <td class="font_size_define"></td>
                                <td class="font_size_define"></td>
                            </tr>
                            <tr height="22">
                                <td class="font_size_define">Program End</td>
                                <td class="font_size_define"><? echo change_date_format($prog_data['end_date']); ?></td>
                                <td class="font_size_define">Dye Type</td>
                                <td class="font_size_define"><? echo $prog_data['dye_type']; ?></td>
                                <td class="font_size_define">Shade No.</td>
                                <td class="font_size_define"><span>Write&nbsp;</span></td>
                                <td class="font_size_define"></td>
                                <td class="font_size_define"></td>
                                <td class="font_size_define"></td>
                                <td class="font_size_define"></td>
                            </tr>
                            <tr>
                                <td rowspan="<? echo count($prog_prod_arr[$prog_no])+1; ?>">Yarn Lot and Test Result</td>
                                <td id="font_size_define" align="center"><b>Lot No</b></td>
                                <td id="font_size_define" align="center" colspan="8"><b>Comments</b></td>
                            </tr>
                            <?php
                            //foreach($product_details_arr as $key=>$val)
                            foreach($prog_prod_arr[$prog_no] as $key=>$val)
                            {
                                ?>
                            <tr>
                                <td><? echo $product_details_arr[$key]['lot']; ?></td>
                                <td colspan="8"><? echo $yarn_test_info[$key]['comments_knit']; ?></td>
                            </tr>

                                <?php
                            }
                            ?>
                            <tr height="50">
                                <td class="font_size_define">Technical Instruction</td>
                                <td class="font_size_define" colspan="9"><span>Write&nbsp;</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top:10px; width:920px">
                    <table cellspacing="2" cellpadding="2" rules="" width="100%">
                        <tbody>
                            <tr><td class="font_size_define"><u><b>বিঃ দ্রঃ</b></u></td></tr>
                            <tr><td class="font_size_define">* প্রোগ্রামের শেষ পর্যায়ে অবশ্যই সূতা মিল করে চালাতে হবে।</td></tr>
                            <tr><td class="font_size_define">* সূতা মেশিনে উঠানোর সময় ১/১ করে উঠতে হবে।</td></tr>
                            <tr><td class="font_size_define">* রোল মার্কিং সঠিকভাবে করতে হবে। </td></tr>
                            <tr><td class="font_size_define">* রোলে কোন প্রকার কাটা-কাটি বা ভুল লেখা চলবেনা।</td></tr>
                            <tr><td class="font_size_define">* রোলের গায়ে জব, কালার, মেশিন নম্বর, তারিখ এবং শিফট অবশ্যই লিখতে হবে।</td></tr>
                            <tr><td class="font_size_define">&nbsp;</td></tr>
                        </tbody>
                    </table>
                </div>
                <? echo signature_table(119, $prog_data['company_id'], "920px","","20"); ?>
                <div class="page_break">&nbsp;</div>
            </div>
            <?
        }
    }
    ?>

    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>

        var barcode_array =<? echo json_encode($program_array); ?>;
         function generateBarcode(id,valuess) {

            var value = valuess;
            var btype = 'code39';
            var renderer = 'bmp';
            var settings = {
                output: renderer,
                bgColor: '#FFFFFF',
                color: '#000000',
                barWidth: 1,
                barHeight: 30,
                moduleSize: 5,
                posX: 10,
                posY: 20,
                addQuietZone: 1
            };
            $("#div_"+ id).html(valuess);
            value = {code: value, rect: false};
            $("#div_"+ id).show().barcode(value, btype, settings);
        }

        for (var i in barcode_array) {
            generateBarcode(i,''+barcode_array[i]+'');
        }
    </script>
    <?
    exit();
    //$brand_name
}

//knitting_card_print_10
if ($action == "knitting_card_print_10")
{
    echo load_html_head_contents("Knitting Card Info", "../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $program_ids =  $data;

    if(!$program_ids)
    {
        echo "Program is not found . ";
        die;
    }

    $sub_subcontract = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
    $brand_arr      = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
    $company_arr    = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $imge_arr       = return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    $count_arr      = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 ", 'id', 'yarn_count');
    $color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
    $yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by id, yarn_count","id","yarn_count");
    $po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number");
    $pub_shipment_date=return_library_array( "select id,pub_shipment_date from wo_po_break_down", "id", "pub_shipment_date");

    if ($db_type == 0)
        $item_id_cond="group_concat(distinct(b.item_id))";
    else if ($db_type==2)
        $item_id_cond="LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id)";

    $result_machin_prog = sql_select("SELECT machine_id,dtls_id,distribution_qnty from ppl_planning_info_machine_dtls WHERE DTLS_ID IN($program_ids)");
    $machin_prog = array();
    foreach ($result_machin_prog as $row)
    {
        $machin_prog[$row[csf('machine_id')]][$row[csf('dtls_id')]]['distribution_qnty'] = $row[csf('distribution_qnty')];
    }

    $reqsDataArr = array();
    $program_cond2 = ($program_ids) ? " and knit_id in(".$program_ids.")" : "";
    if ($db_type == 0)
    {
        $reqsData = sql_select("select knit_id, requisition_no as reqs_no, group_concat(distinct(prod_id)) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id");
    }
    else
    {
        $reqsData = sql_select("select knit_id, max(requisition_no) as reqs_no, LISTAGG(prod_id, ',') WITHIN GROUP (ORDER BY prod_id) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id,requisition_no");
    }

    foreach ($reqsData as $row)
    {
        $reqsDataArr[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
        $reqsDataArr[$row[csf('knit_id')]]['prod_id'] = $row[csf('prod_id')];
        $prod_arr[] = $row[csf('prod_id')];
    }
    unset($reqsData);

    if(!empty($prod_arr))
    {
        $product_details_arr = array();
        $procuct_cond = (!empty($prod_arr))?" and id in(".implode(",",$prod_arr).")":"";
        $pro_sql = sql_select("select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 $procuct_cond");
        foreach ($pro_sql as $row)
        {
            $compos = '';
            if ($row[csf('yarn_comp_percent2nd')] != 0)
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
            }
            else
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
            }

            $product_details_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
            $product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
            $product_details_arr[$row[csf('id')]]['brand_name'] = $row[csf('brand')];
            //$product_details_arr[$row[csf('id')]]['supplier'] = $row[csf('supplier_id')];
            $yarn_details_arr[$row[csf('id')]]['yarn_count'] = $count_arr[$row[csf('yarn_count_id')]];
            $yarn_details_arr[$row[csf('id')]]['yarn_type'] = $yarn_type[$row[csf('yarn_type')]];
            $yarn_details_arr[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
            $yarn_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
            $yarn_details_arr[$row[csf('id')]]['composition'] = $compos;
            $yarn_details_arr[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
        }
        unset($pro_sql);
    }
    //echo "<pre>";
    //print_r($yarn_details_arr);

    $data_sql="SELECT a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id, c.style_ref_no, c.job_no from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b left join fabric_sales_order_mst c on c.id=b.po_id and c.status_active=1 and c.is_deleted=0 where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id, c.style_ref_no, c.job_no";
    //, b.yarn_desc
    //echo $data_sql;
    $dataArray = sql_select($data_sql);
    $bookingNoArr = array();
    //$progNoArr = array();
    foreach ($dataArray as $row)
    {
        //for booking no
        $bookingNoArr[$row[csf('booking_no')]] = $row[csf('booking_no')];

        //for prog no
        //$progNoArr[$row[csf('id')]] = $row[csf('id')];
    }

    //for booking qty

    $booking_info_arr = array();

    $sql_data = sql_select("select a.booking_no, a.buyer_id, sum(b.grey_fab_qnty ) as grey_fab_qnty, a.quality_level, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.fabric_source=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0".where_con_using_array($bookingNoArr, '1', 'a.booking_no')." group by a.booking_no, a.buyer_id, a.quality_level, a.po_break_down_id");
    foreach ($sql_data as $row)
    {
        $po_id=explode(",",$row[csf("po_break_down_id")]);
        $po_number_string="";
        foreach($po_id as $key=> $value ){
            $po_number_string.=$po_number[$value].",";
            $pub_shipment_date_string.=change_date_format($pub_shipment_date[$value]).",";
        }

        $booking_info_arr[$row[csf('booking_no')]]['qty'] += $row[csf('grey_fab_qnty')];
        $booking_info_arr[$row[csf('booking_no')]]['buyer'] += $row[csf('buyer_id')];
        $booking_info_arr[$row[csf('booking_no')]]['po_no'] = $po_number_string;
        $booking_info_arr[$row[csf('booking_no')]]['pub_shipment_date'] = $pub_shipment_date_string;
        //$order_nature_booking_arr[$row[csf('booking_no')]]= $row[csf('quality_level')];

    }
    unset($sql_data);
    //var_dump($booking_qnty_arr);

    //for int. ref.
    $sqlBooking = "SELECT a.grouping AS GROUPING, b.booking_no AS BOOKING_NO FROM wo_po_break_down a, wo_booking_dtls b where a.id = b.po_break_down_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0".where_con_using_array($bookingNoArr, '1', 'b.booking_no')." GROUP BY a.grouping, b.booking_no";
    //echo $sqlBooking;
    $sqlBookingRslt = sql_select($sqlBooking);
    $bookingInfoArr = array();
    foreach($sqlBookingRslt as $row)
    {
        $bookingInfoArr[$row['BOOKING_NO']]['int_ref'] = $row['GROUPING'];
    }
    unset($sqlBookingRslt);

    //for po buyer

    $sqlPoBuyer = sql_select("select sales_booking_no AS BOOKING_NO, po_buyer AS BUYER from fabric_sales_order_mst where status_active = 1 AND is_deleted = 0".where_con_using_array($bookingNoArr, '1', 'sales_booking_no'));
    $poBuyerArr = array();
    foreach($sqlPoBuyer as $row)
    {
        $poBuyerArr[$row['BOOKING_NO']] = $row['BUYER'];
    }
    unset($sqlPoBuyer);

    $company_id = '';
    $orderNo = "";
    $knitting_factory = '';
    $program_data_arr=array();
    foreach ($dataArray as $row)
    {
        $knitting_factory='';
        if ($row[csf('knitting_source')] == 1)
            $knitting_factory = $company_arr[$row[csf('knitting_party')]] . ",";
        else if ($row[csf('knitting_source')] == 3)
            $knitting_factory = $supplier_details[$row[csf('knitting_party')]] . ",";

        $yarn_desc='';
        $lot_no="";
        $brand_name="";
        $yarn_dtls="";
        $yarn_composition="";
        $yarn_types="";
        if($orderNo=="")
        {
            $orderNo .= $row[csf('po_id')];
            $po_number .= $po_details[$row[csf('po_id')]]['po_number'];
        }
        else
        {
            $orderNo .= ",".$row[csf('po_id')];
            $po_number .= ",".$po_details[$row[csf('po_id')]]['po_number'];
        }

        if($reqsDataArr[$row[csf('id')]]['prod_id'] != '')
        {
            $prod_id = array_unique(explode(",", $reqsDataArr[$row[csf('id')]]['prod_id']));
            foreach ($prod_id as $val)
            {
                $yarn_desc .= $product_details_arr[$val]['desc'] . ", ";
                $lot_no .= $product_details_arr[$val]['lot'] . ", ";

                //$brand_name .= $brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . ", ";
                $brand_name .= $brand_arr[$product_details_arr[$val]['brand_name']]. ", ";
                //$yarn_dtls .= $product_details_arr[$val]['desc'] . ",".$product_details_arr[$val]['lot'] . ",".$brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . "<br>";
                //$yarn_dtls .= $yarn_details_arr[$val]['yarn_count'] . ", ".$yarn_details_arr[$val]['composition'] . ", ".$yarn_details_arr[$val]['yarn_type'].", ".$yarn_details_arr[$val]['color'].', '.$yarn_details_arr[$val]['brand'].", ".$yarn_details_arr[$val]['lot'] . "<br>";
                //$yarn_dtls .= $yarn_details_arr[$val]['yarn_count']. "<br>";
                $yarn_dtls .= $yarn_details_arr[$val]['yarn_count']. "<br>";
                $yarn_types .=$yarn_details_arr[$val]['yarn_type']. "<br>";
                $yarn_composition .= $yarn_details_arr[$val]['composition']. "<br>";
            }

            $yarn_desc = implode(",",array_filter(array_unique(explode(",", substr($yarn_desc, 0, -1)))));
            $lot_no = implode(",",array_filter(array_unique(explode(",", substr($lot_no, 0, -1)))));
            $brand_name = implode(",",array_filter(array_unique(explode(",", substr($brand_name, 0, -1)))));
        }
        $ex_mc_id=array_unique(explode(",",$row[csf('machine_id')]));

        /*$machine_name="";
        foreach($ex_mc_id as $mc_id)
        {
            if($machine_name=='') $machine_name=$machine_arr[$mc_id]; else $machine_name.=','.$machine_arr[$mc_id];
        }*/

        //for color
        $color_name="";
        $ex_color_id=array_unique(explode(",",$row[csf('color_id')]));
        foreach($ex_color_id as $color_id)
        {
            if($color_name=='')
                $color_name=$color_library[$color_id];
            else
                $color_name.=', '.$color_library[$color_id];
        }

        $styles=$program_stl_data_arr[$row[csf('id')]]['style_ref_no'];

        $program_data_arr[$row[csf('id')]]['po_number']=$po_number;
        $program_data_arr[$row[csf('id')]]['styles']=$styles;
        $program_data_arr[$row[csf('id')]]['co_efficient']=$row[csf('co_efficient')];
        $program_data_arr[$row[csf('id')]]['draft_ratio']=$row[csf('draft_ratio')];
        $program_data_arr[$row[csf('id')]]['start_date']=$row[csf('start_date')];
        $program_data_arr[$row[csf('id')]]['end_date']=$row[csf('end_date')];
        $program_data_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
        $program_data_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
        $program_data_arr[$row[csf('id')]]['color_id']=$color_name;
        //$program_data_arr[$row[csf('id')]]['prog_qty']+=$row[csf('program_qnty')];
        $program_data_arr[$row[csf('id')]]['prog_qty']=$row[csf('program_qnty')];
        $program_data_arr[$row[csf('id')]]['s_length']=$row[csf('stitch_length')];
        $program_data_arr[$row[csf('id')]]['fabric_dia']=$row[csf('fabric_dia')];
        $program_data_arr[$row[csf('id')]]['program_date']=$row[csf('program_date')];
        $program_data_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
        $program_data_arr[$row[csf('id')]]['booking_qty']=$booking_info_arr[$row[csf('booking_no')]]['qty'];
        $program_data_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];

        $program_data_arr[$row[csf('id')]]['yarn_dtls']= $yarn_dtls;
        $program_data_arr[$row[csf('id')]]['yarn_types']= $yarn_types;
        $program_data_arr[$row[csf('id')]]['yarn_desc']= $yarn_desc;
        $program_data_arr[$row[csf('id')]]['lot']= $lot_no;
        $program_data_arr[$row[csf('id')]]['yarn_composition']= $yarn_composition;
        $program_data_arr[$row[csf('id')]]['brand_name']= $brand_name;
        $program_data_arr[$row[csf('id')]]['mc_nmae']= $machine_name;
        $program_data_arr[$row[csf('id')]]['knit_factory']= $knitting_factory;
        $program_data_arr[$row[csf('id')]]['sub_party']= $supplier_details[$row[csf('subcontract_party')]];
        $program_data_arr[$row[csf('id')]]['gsm_weight']=$row[csf('gsm_weight')];
        $program_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];

        //for buyer
        //$program_data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
        if($booking_info_arr[$row[csf('booking_no')]]['buyer'] != '' && $booking_info_arr[$row[csf('booking_no')]]['buyer'] != 0)
        {
            $program_data_arr[$row[csf('id')]]['buyer_id']=$booking_info_arr[$row[csf('booking_no')]]['buyer'];
        }
        else
        {
            $program_data_arr[$row[csf('id')]]['buyer_id']=$poBuyerArr[$row[csf('booking_no')]];
        }

        $program_data_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
        $program_data_arr[$row[csf('id')]]['spandex_stitch_length']=$row[csf('spandex_stitch_length')];
        $program_data_arr[$row[csf('id')]]['feeder']=$row[csf('feeder')];
        $program_data_arr[$row[csf('id')]]['advice']=$row[csf('advice')];
        $program_data_arr[$row[csf('id')]]['knit_id']=$row[csf('mst_id')];
        $program_data_arr[$row[csf('id')]]['width_dia_type']=$row[csf('width_dia_type')];
        $program_data_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
        $program_data_arr[$row[csf('id')]]['sales_order_no']=$row[csf('job_no')];
        $program_data_arr[$row[csf('id')]]['int_ref']=$bookingInfoArr[$row[csf('booking_no')]]['int_ref'];
        $program_data_arr[$row[csf('id')]]['po_no']=$booking_info_arr[$row[csf('booking_no')]]['po_no'];
        $program_data_arr[$row[csf('id')]]['pub_shipment_date']=$booking_info_arr[$row[csf('booking_no')]]['pub_shipment_date'];
    }
    unset($dataArray);

    if($program_ids!="")
    {
        // echo "SELECT DTLS_ID, SEQ_NO, COUNT_ID, FEEDING_ID FROM PPL_PLANNING_COUNT_FEED_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0";
        $feedingResult =  sql_select("SELECT DTLS_ID, SEQ_NO, COUNT_ID, FEEDING_ID FROM PPL_PLANNING_COUNT_FEED_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0");

        $feedingDataArr = array();
        foreach ($feedingResult as $row)
        {
            $feedingSequence[$row['SEQ_NO']] = $row['SEQ_NO'];
            $feedingDataArr[$row['DTLS_ID']][$row['SEQ_NO']]['count_id'] = $row['COUNT_ID'];
            $feedingDataArr[$row['DTLS_ID']][$row['SEQ_NO']]['feeding_id'] = $row['FEEDING_ID'];
        }
    }

    foreach($ex_mc_id as $mc_id)
    {
        // program array loop
        foreach($program_data_arr as $prog_no=>$prog_data)
        {
            $count_feeding = "";
            foreach($feedingDataArr[$prog_no] as $feedingSequence=>$feedingData)
            {
                if($count_feeding =="")
                {
                    $count_feeding = $feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
                }
                else
                {
                    $count_feeding .= ",".$feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
                }
            }


        $company_id=$prog_data['company_id'];

        $pub_shipment_date = implode(",",array_filter(array_unique(explode(",", chop($prog_data['pub_shipment_date'])))));
        $po_number = implode(",",array_filter(array_unique(explode(",", chop($prog_data['po_number'])))));


        ?>
        <style type="text/css">
            .page_break { page-break-after: always;
            }
            #font_size_define{
                font-size:14px;
                font-family:'Arial Narrow';
            }
            .font_size_define{
                font-size:14px;
                font-family:'Arial Narrow';
            }
            #dataTable tbody tr span{
                 opacity:0.2;
                 color:gray;
            }
            #dataTable tbody tr{
                vertical-align:middle;
            }
        </style>
        <div style="width:800px; margin-left: 10px;">
            <!--<table width="100%" cellpadding="0" cellspacing="0">-->
            <table width="100%" cellspacing="2" cellpadding="2" border="1" rules="all" class="rpt_table">
                <tr>
                    <td width="100" align="right" style="border-left:hidden; border-top:hidden; border-right:hidden; border-bottom:hidden;">
                        <img src='../../<? echo $imge_arr[str_replace("'","",$prog_data['company_id'])]; ?>' height='100%' width='100%' alt="not found" />
                    </td>
                    <td  width="455" align="center" valign="middle" style="font-size:16px;border-left:hidden; border-top:hidden; border-right:hidden; border-bottom:hidden;"><b><? echo $company_arr[$prog_data['company_id']]; ?></b></td>
                    <td><span  id="barcode_img_id_<? echo $inc_no;?>" ></span></td>
                </tr>
                <tr>
                    <td colspan="3" style="border-left:hidden; border-right:hidden;">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3" width="100%" align="center" style="font-size:20px;border-left:hidden; border-top:hidden; border-right:hidden;"><b>KNIT CARD</b></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>PROGRAM NO</b></td>
                    <td width="350" class="font_size_define"><b><? echo $prog_no; ?></b></td>
                    <td width="105" class="font_size_define" style="border-left:hidden;"><b>Date : <? echo date('d-m-Y', strtotime($prog_data['program_date'])); ?></b></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>KNIT PARTY</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['knit_factory']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>BUYER</b></td>
                    <td colspan="2" class="font_size_define"><? echo $buyer_arr[$prog_data['buyer_id']]; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>BOOKING NO</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['booking_no']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>Style No</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['style_ref_no']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>Fire Date/<br>Shipment Date</b></td>
                    <td colspan="2" class="font_size_define">
                        <? echo implode(",",array_filter(array_unique(explode(",", chop($prog_data['pub_shipment_date']))))); ?>
                    </td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>Textile REF. No</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['sales_order_no']; ?></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>M/C NO</b></td>
                    <td colspan="2" class="font_size_define"><? echo $machine_arr[$mc_id];?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>M/C DIA & GG</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['machine_dia']." x ".$prog_data['machine_gg']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>F. DIA</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['fabric_dia']." "."[".$fabric_typee[$prog_data['width_dia_type']]."]"; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>F. TYPE</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['fabric_desc']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>GSM</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['gsm_weight']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>COUNT</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['yarn_dtls']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>LOT</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['lot']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>BRAND</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['brand_name']; ?></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>SL</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['s_length']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>COLOR</b></td>
                    <td colspan="2" class="font_size_define"><? echo $prog_data['color_id']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>P. QTY. (Kg)</b></td>
                    <td colspan="2" class="font_size_define"><? echo number_format($prog_data['prog_qty'],2);?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>M/C Distrb. Qty</b></td>
                    <td colspan="2" class="font_size_define"><? echo number_format($machin_prog[$mc_id][$prog_no]['distribution_qnty'],2); ?></td>
                </tr>
            </table>
        </div>
        <div style="margin-top:10px; margin-left: 10px; width:800px;">
            <table  cellspacing="2" cellpadding="2" border="1" rules="all" width="100%" class="rpt_table" id="dataTable">

                    <thead height="25">
                        <tr>
                            <td width="128" height="20" colspan='11' style="font-size:20px"><b>Style :</b></td>
                        </tr>
                        <tr>
                            <th width="64" height="20">Date</th>
                            <th width="64">Shift</th>
                            <th width="68">Order Qty</th>
                            <th width="74">No. Of Roll</th>
                            <th width="99">Production qty</th>
                            <th width="69">Reject qty</th>
                            <th width="80">Balance Qty</th>
                            <th width="78">Operator Id</th>
                            <th width="100">Name</th>
                            <th width="66">Signature</th>
                            <th width="150">Remarks</th>
                        </tr>
                    </thead>

                    <? $row_count=3;
                    for($i=1; $i<=$row_count; $i++)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr height="24" bgcolor="<? echo $bgcolor; ?>">
                            <td rowspan="3">&nbsp;</td>
                            <td align="center" height="24">Shift-A</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr height="24">
                            <td align="center" height="24">Shift-B</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr height="24">

                            <td align="center" height="24">Shift-C</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>

                        <?
                    }
                    ?>

                </table>
            </div>
            <div style="margin-top:10px; margin-left: 10px; width:800px">
                <table cellspacing="2" cellpadding="2" rules="all" width="100%" style="font-size:14px; font-family:'Arial Narrow'">
                    <tbody>
                    <tr><td><u><b>বিঃ দ্রঃ</b></u></td></tr>
                        <tr><td>* প্রোগ্রামের শেষ পর্যায়ে অবশ্যই সূতা মিল করে চালাতে হবে।</td></tr>
                        <tr><td>* সূতা মেশিনে উঠানোর সময় ১/১ করে উঠতে হবে।</td></tr>
                        <tr><td>* রোল মার্কিং সঠিকভাবে করতে হবে। </td></tr>
                        <tr><td>* রোলে কোন প্রকার কাটা-কাটি বা ভুল লেখা চলবেনা।</td></tr>
                        <tr><td>* রোলের গায়ে তারিখ/ অপারেটরের নাম এবং শিফট অবশ্যই লিখতে হবে।</td></tr>
                        <tr><td>&nbsp;</td></tr>
                    </tbody>
                </table>

                <?
                //echo signature_table(213, $company_id, "655px","","20");
                echo signature_table(213, $company_id, "655px",1,"20");
                ?>
            </div>

        <div>
                <script type="text/javascript" src="../../js/jquery.js"></script>
                <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
                <script>
                    function generateBarcode( valuess,id ){
                        var value = valuess;//$("#barcodeValue").val();
                        // alert(value)
                        var btype = 'code39';//$("input[name=btype]:checked").val();
                        var renderer ='bmp';// $("input[name=renderer]:checked").val();

                        var settings = {
                          output:renderer,
                          bgColor: '#FFFFFF',
                          color: '#000000',
                          barWidth: 1,
                          barHeight: 30,
                          moduleSize:5,
                          posX: 10,
                          posY: 20,
                          addQuietZone: 1
                        };
                        $("#barcode_img_id_<? echo $inc_no; ?>").html('11');
                         value = {code:value, rect: false};

                        $("#barcode_img_id_<? echo $inc_no; ?>").barcode(value, btype, settings);
                    }
                    generateBarcode('<? echo $prog_no; ?>');
                 </script>
            </div>
        <?
        }
        $inc_no++;
    }

    exit();
}

//knitting_card_print_11
if ($action == "knitting_card_print_11")
{
    echo load_html_head_contents("Knitting Card Info", "../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $program_ids =  $data;

    if(!$program_ids)
    {
        echo "Program is not found . ";
        die;
    }

    $sub_subcontract = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
    $brand_arr      = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
    $company_arr    = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $imge_arr       = return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    $count_arr      = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 ", 'id', 'yarn_count');
    $color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
    $floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
    $machineId_arr=array();
    $sql_mc=sql_select("select id, machine_no, floor_id from lib_machine_name");
    foreach( $sql_mc as $row)
    {
        $machineId_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
    }
    unset($sql_mc);

    if ($db_type == 0)
        $item_id_cond="group_concat(distinct(b.item_id))";
    else if ($db_type==2)
        $item_id_cond="LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id)";

    $result_machin_prog = sql_select("SELECT machine_id,dtls_id,distribution_qnty from ppl_planning_info_machine_dtls WHERE DTLS_ID IN($program_ids)");
    $machin_prog = array();
    foreach ($result_machin_prog as $row)
    {
        $machin_prog[$row[csf('machine_id')]][$row[csf('dtls_id')]]['distribution_qnty'] = $row[csf('distribution_qnty')];
    }

    $reqsDataArr = array();
    $program_cond2 = ($program_ids) ? " and knit_id in(".$program_ids.")" : "";
    if ($db_type == 0)
    {
        $reqsData = sql_select("select knit_id, requisition_no as reqs_no, group_concat(distinct(prod_id)) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id");
    }
    else
    {
        $reqsData = sql_select("select knit_id, max(requisition_no) as reqs_no, LISTAGG(prod_id, ',') WITHIN GROUP (ORDER BY prod_id) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id,requisition_no");
    }

    foreach ($reqsData as $row)
    {
        $reqsDataArr[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
        $reqsDataArr[$row[csf('knit_id')]]['prod_id'] = $row[csf('prod_id')];
        $prod_arr[] = $row[csf('prod_id')];
    }
    unset($reqsData);

    if(!empty($prod_arr))
    {
        $product_details_arr = array();
        $procuct_cond = (!empty($prod_arr))?" and id in(".implode(",",$prod_arr).")":"";
        $pro_sql = sql_select("select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 $procuct_cond");
        foreach ($pro_sql as $row)
        {
            $compos = '';
            if ($row[csf('yarn_comp_percent2nd')] != 0)
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
            }
            else
            {
                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
            }

            $product_details_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
            $product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
            $product_details_arr[$row[csf('id')]]['brand_name'] = $row[csf('brand')];
            //$product_details_arr[$row[csf('id')]]['supplier'] = $row[csf('supplier_id')];
            $yarn_details_arr[$row[csf('id')]]['yarn_count'] = $count_arr[$row[csf('yarn_count_id')]];
            $yarn_details_arr[$row[csf('id')]]['yarn_type'] = $yarn_type[$row[csf('yarn_type')]];
            $yarn_details_arr[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
            $yarn_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
            $yarn_details_arr[$row[csf('id')]]['composition'] = $compos;
            $yarn_details_arr[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
        }
        unset($pro_sql);
    }
    //echo "<pre>";
    //print_r($yarn_details_arr);
    //ppl_planning_info_entry_dtls

    $data_sql="SELECT a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id,a.location_id as location1, c.location_id as location2, c.job_no, b.color_type_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b left join fabric_sales_order_mst c on c.id=b.po_id and c.status_active=1 and c.is_deleted=0 where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id, a.location_id, c.location_id, c.job_no, b.color_type_id";
    //, b.yarn_desc
    //echo $data_sql;
    $dataArray = sql_select($data_sql);
    $bookingNoArr = array();
    //$progNoArr = array();
    foreach ($dataArray as $row)
    {
        //for booking no
        $bookingNoArr[$row[csf('booking_no')]] = $row[csf('booking_no')];

        //for prog no
        //$progNoArr[$row[csf('id')]] = $row[csf('id')];
    }

    //for booking qty
    $booking_qnty_arr = array();
    $sql_data = sql_select("select a.booking_no, a.buyer_id, sum(b.grey_fab_qnty ) as grey_fab_qnty, a.quality_level from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.fabric_source=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0".where_con_using_array($bookingNoArr, '1', 'a.booking_no')." group by a.booking_no, a.buyer_id, a.quality_level");
    foreach ($sql_data as $row)
    {
        $booking_qnty_arr[$row[csf('booking_no')]]['qty'] += $row[csf('grey_fab_qnty')];
        $booking_qnty_arr[$row[csf('booking_no')]]['buyer'] += $row[csf('buyer_id')];
        //$order_nature_booking_arr[$row[csf('booking_no')]]= $row[csf('quality_level')];
    }
    unset($sql_data);

    //for int. ref.
    $sqlBooking = "SELECT a.grouping AS GROUPING, b.booking_no AS BOOKING_NO,c.style_ref_no as STYLE_REF_NO FROM wo_po_details_master c, wo_po_break_down a, wo_booking_dtls b where a.job_id=c.id and a.id = b.po_break_down_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0".where_con_using_array($bookingNoArr, '1', 'b.booking_no')." GROUP BY a.grouping, b.booking_no,c.style_ref_no";
    //echo $sqlBooking;
    $sqlBookingRslt = sql_select($sqlBooking);
    $bookingInfoArr = array();
    foreach($sqlBookingRslt as $row)
    {
        $bookingInfoArr[$row['BOOKING_NO']]['int_ref'] = $row['GROUPING'];
        $bookingInfoArr[$row['BOOKING_NO']]['style_ref_no'] = $row['STYLE_REF_NO'];
    }
    unset($sqlBookingRslt);

    //for po buyer
    $sqlPoBuyer = sql_select("select sales_booking_no AS BOOKING_NO, po_buyer AS BUYER from fabric_sales_order_mst where status_active = 1 AND is_deleted = 0".where_con_using_array($bookingNoArr, '1', 'sales_booking_no'));
    $poBuyerArr = array();
    foreach($sqlPoBuyer as $row)
    {
        $poBuyerArr[$row['BOOKING_NO']] = $row['BUYER'];
    }
    unset($sqlPoBuyer);

    $company_id = '';
    $orderNo = "";
    $knitting_factory = '';
    $location = '';
    $program_data_arr=array();
    foreach ($dataArray as $row)
    {
        $knitting_factory='';
        if ($row[csf('knitting_source')] == 1)
            $knitting_factory = $company_arr[$row[csf('knitting_party')]] . ",";
        else if ($row[csf('knitting_source')] == 3)
            $knitting_factory = $supplier_details[$row[csf('knitting_party')]] . ",";

        if($row[csf('knitting_source')] == 1)
        {
            $location = return_field_value("location_name", "lib_location", "id='" . $row[csf('location1')] . "'");
        }
        else if($row[csf('knitting_source')] == 3)
        {
            $location = return_field_value("location_name", "lib_location", "id='" . $row[csf('location2')] . "'");
        }

        $yarn_desc='';
        $lot_no="";
        $brand_name="";
        $yarn_dtls="";
        if($orderNo=="")
        {
            $orderNo .= $row[csf('po_id')];
            $po_number .= $po_details[$row[csf('po_id')]]['po_number'];
        }
        else
        {
            $orderNo .= ",".$row[csf('po_id')];
            $po_number .= ",".$po_details[$row[csf('po_id')]]['po_number'];
        }

        if($reqsDataArr[$row[csf('id')]]['prod_id'] != '')
        {
            $prod_id = array_unique(explode(",", $reqsDataArr[$row[csf('id')]]['prod_id']));
            foreach ($prod_id as $val)
            {
                $yarn_desc .= $product_details_arr[$val]['desc'] . ", ";
                $lot_no .= $product_details_arr[$val]['lot'] . ", ";

                //$brand_name .= $brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . ", ";
                $brand_name .= $brand_arr[$product_details_arr[$val]['brand_name']] . ", ";
                //$yarn_dtls .= $product_details_arr[$val]['desc'] . ",".$product_details_arr[$val]['lot'] . ",".$brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . "<br>";
                //$yarn_dtls .= $yarn_details_arr[$val]['yarn_count'] . ", ".$yarn_details_arr[$val]['composition'] . ", ".$yarn_details_arr[$val]['yarn_type'].", ".$yarn_details_arr[$val]['color'].', '.$yarn_details_arr[$val]['brand'].", ".$yarn_details_arr[$val]['lot'] . "<br>";
                $yarn_dtls .= $yarn_details_arr[$val]['yarn_count'] . ", ".$yarn_details_arr[$val]['composition'] . ", ".$yarn_details_arr[$val]['yarn_type'].", ".$yarn_details_arr[$val]['color'] . "<br>";
            }

            $yarn_desc = implode(",",array_filter(array_unique(explode(",", substr($yarn_desc, 0, -1)))));
            $lot_no = implode(",",array_filter(array_unique(explode(",", substr($lot_no, 0, -1)))));
            $brand_name = implode(",",array_filter(array_unique(explode(",", substr($brand_name, 0, -1)))));
        }
        $ex_mc_id=array_unique(explode(",",$row[csf('machine_id')]));

        /*$machine_name="";
        foreach($ex_mc_id as $mc_id)
        {
            if($machine_name=='') $machine_name=$machine_arr[$mc_id]; else $machine_name.=','.$machine_arr[$mc_id];
        }*/

        //for color
        $color_name="";
        $ex_color_id=array_unique(explode(",",$row[csf('color_id')]));
        foreach($ex_color_id as $color_id)
        {
            if($color_name=='')
                $color_name=$color_library[$color_id];
            else
                $color_name.=', '.$color_library[$color_id];
        }

        $program_data_arr[$row[csf('id')]]['po_number']=$po_number;
        $program_data_arr[$row[csf('id')]]['co_efficient']=$row[csf('co_efficient')];
        $program_data_arr[$row[csf('id')]]['draft_ratio']=$row[csf('draft_ratio')];
        $program_data_arr[$row[csf('id')]]['start_date']=$row[csf('start_date')];
        $program_data_arr[$row[csf('id')]]['end_date']=$row[csf('end_date')];
        $program_data_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
        $program_data_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
        $program_data_arr[$row[csf('id')]]['color_id']=$color_name;
        //$program_data_arr[$row[csf('id')]]['prog_qty']+=$row[csf('program_qnty')];
        $program_data_arr[$row[csf('id')]]['prog_qty']=$row[csf('program_qnty')];
        $program_data_arr[$row[csf('id')]]['s_length']=$row[csf('stitch_length')];
        $program_data_arr[$row[csf('id')]]['fabric_dia']=$row[csf('fabric_dia')];
        $program_data_arr[$row[csf('id')]]['program_date']=$row[csf('program_date')];
        $program_data_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
        $program_data_arr[$row[csf('id')]]['booking_qty']=$booking_qnty_arr[$row[csf('booking_no')]]['qty'];
        $program_data_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];

        $program_data_arr[$row[csf('id')]]['yarn_dtls']= $yarn_dtls;
        $program_data_arr[$row[csf('id')]]['yarn_desc']= $yarn_desc;
        $program_data_arr[$row[csf('id')]]['lot']= $lot_no;
        $program_data_arr[$row[csf('id')]]['brand_name']= $brand_name;
        $program_data_arr[$row[csf('id')]]['mc_nmae']= $machine_name;
        $program_data_arr[$row[csf('id')]]['knit_factory']= $knitting_factory;
        $program_data_arr[$row[csf('id')]]['sub_party']= $supplier_details[$row[csf('subcontract_party')]];
        $program_data_arr[$row[csf('id')]]['gsm_weight']=$row[csf('gsm_weight')];
        $program_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
        $program_data_arr[$row[csf('id')]]['location']= $location;
        $program_data_arr[$row[csf('id')]]['job_no']= $row[csf('job_no')];
        $program_data_arr[$row[csf('id')]]['color_type_id']= $row[csf('color_type_id')];

        //for buyer
        //$program_data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
        if($booking_qnty_arr[$row[csf('booking_no')]]['buyer'] != '' && $booking_qnty_arr[$row[csf('booking_no')]]['buyer'] != 0)
        {
            $program_data_arr[$row[csf('id')]]['buyer_id']=$booking_qnty_arr[$row[csf('booking_no')]]['buyer'];
        }
        else
        {
            $program_data_arr[$row[csf('id')]]['buyer_id']=$poBuyerArr[$row[csf('booking_no')]];
        }

        $program_data_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
        $program_data_arr[$row[csf('id')]]['spandex_stitch_length']=$row[csf('spandex_stitch_length')];
        $program_data_arr[$row[csf('id')]]['feeder']=$row[csf('feeder')];
        $program_data_arr[$row[csf('id')]]['advice']=$row[csf('advice')];
        $program_data_arr[$row[csf('id')]]['knit_id']=$row[csf('mst_id')];
        $program_data_arr[$row[csf('id')]]['width_dia_type']=$row[csf('width_dia_type')];
        $program_data_arr[$row[csf('id')]]['int_ref']=$bookingInfoArr[$row[csf('booking_no')]]['int_ref'];
        $program_data_arr[$row[csf('id')]]['style_ref_no']=$bookingInfoArr[$row[csf('booking_no')]]['style_ref_no'];
    }
    unset($dataArray);


    $inc_no=1;
    foreach($ex_mc_id as $mc_id)
    {
        if ($floor_id_all == '') $floor_id_all = $machineId_arr[$mc_id]['floor_id']; else $floor_id_all .= "," . $machineId_arr[$mc_id]['floor_id'];


        $floor_name="";
        $floor_ids = array_filter(array_unique(explode(",", $floor_id_all)));
        // var_dump($floor_ids);
        foreach ($floor_ids as $ids) {
            if ($floor_name == '') $floor_name = $floor_arr[$ids]; else $floor_name .= "," . $floor_arr[$ids];
        }
        //var_dump($floor_name);

        // program array loop
        foreach($program_data_arr as $prog_no=>$prog_data)
        {

        ?>
        <style type="text/css">
            .page_break { page-break-after: always;
            }
            #font_size_define{
                font-size:14px;
                font-family:'Arial Narrow';
            }
            .font_size_define{
                font-size:14px;
                font-family:'Arial Narrow';
            }
            #dataTable tbody tr span{
                 opacity:0.2;
                 color:gray;
            }
            #dataTable tbody tr{
                vertical-align:middle;
            }
            #signatureTblId{
                margin-top: -70px;
            }

            #barcode_img_id_<? echo $inc_no;?>{
               height: 5%;
            }

            #mainDiv
            {
                width: 100%;
                height:100%;
                position:absolute;
                top:0px;
                bottom:0px;
                margin: auto;
                margin-top: 0px !important;

            }



        </style>
        <div style="width:650px;" id="mainDiv">
            <!--<table width="100%" cellpadding="0" cellspacing="0">-->
            <table width="100%" cellspacing="2" cellpadding="2" border="1" rules="all" class="rpt_table" style="margin-left:60px">
                <tr>
                    <td width="150" align="right" style="border-left:hidden; border-top:hidden; border-right:hidden; border-bottom:hidden;padding-top:2">
                        <img src='../../<? echo $imge_arr[str_replace("'","",$prog_data['company_id'])]; ?>' height='80%' width='80%'  />
                    </td>
                    <td colspan="2" width="250" align="center" valign="middle" style="font-size:16px;border-left:hidden; border-top:hidden; border-right:hidden; border-bottom:hidden;" ><b style="font-size:22px"><? echo $company_arr[$prog_data['company_id']]; ?></b><br><? echo $prog_data['location']; ?></td>
                    <td colspan="2"valign="middle" style="border-left:hidden;border-right:hidden; border-top:hidden; border-bottom:hidden; text-align: right;height: 5%">
                        <span  id="barcode_img_id_<? echo $inc_no;?>" ></span>
                    </td>
                </tr>

                <tr>
                    <td colspan="5" width="100%" align="center" style="font-size:20px;border-left:hidden; border-top:hidden; border-right:hidden;"><b>KNIT CARD</b></td>
                </tr>
                <tr>
                    <td width="150" class="font_size_define"><b>PROGRAM NO</b></td>
                    <td width="250" class="font_size_define"><b><? echo $prog_no; ?></b></td>

                    <td width="120" class="font_size_define" ><b>Date : <? echo date('d-m-Y', strtotime($prog_data['program_date'])); ?></b></td>

                    <td width="150" class="font_size_define"><b>Requisition No</b></td>
                    <td width="200" class="font_size_define"><b><? echo  $reqsDataArr[$prog_no]['reqs_no']; ?></b></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>KNIT PARTY</b></td>
                    <td  class="font_size_define"><? echo $prog_data['knit_factory']; ?></td>
                    <td class="font_size_define"><b>BUYER</b></td>
                    <td colspan="2"  class="font_size_define"><? echo $buyer_arr[$prog_data['buyer_id']]; ?></td>
                </tr>

                <tr>
                    <td class="font_size_define"><b>BOOKING NO</b></td>
                    <td  class="font_size_define"><? echo $prog_data['booking_no']; ?></td>
                    <td class="font_size_define"><b>Textile REF. No</b></td>
                    <td colspan="2"  class="font_size_define"><? echo $prog_data['job_no']; ?></td>
                </tr>

                <tr>
                    <td class="font_size_define"><b>Style Ref</b></td>
                    <td  class="font_size_define"><? echo $prog_data['style_ref_no'];  ?></td>
                    <td class="font_size_define"><b>Floor Name</b></td>
                    <td colspan="2"  class="font_size_define"><? echo $floor_name; ?></td>

                </tr>

                <tr>
                    <td class="font_size_define"><b>M/C NO</b></td>
                    <td  class="font_size_define"><? echo $machine_arr[$mc_id];?></td>
                    <td class="font_size_define"><b>F. DIA</b></td>
                    <td colspan="2"  class="font_size_define"><? echo $prog_data['fabric_dia']." "."[".$fabric_typee[$prog_data['width_dia_type']]."]"; ?></td>

                </tr>
                <tr>
                    <td class="font_size_define"><b>M/C DIA & GG</b></td>
                    <td  class="font_size_define"><? echo $prog_data['machine_dia']." x ".$prog_data['machine_gg']; ?></td>
                    <td class="font_size_define"><b>GSM</b></td>
                    <td class="font_size_define"><? echo $prog_data['gsm_weight']; ?></td>
                    <td class="font_size_define"><b>SL-<? echo $prog_data['s_length']; ?></b></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>F. TYPE</b></td>
                    <td colspan="4" class="font_size_define"><? echo $prog_data['fabric_desc']; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>COUNT</b></td>
                    <td  class="font_size_define"><? echo $prog_data['yarn_dtls']; ?></td>
                    <td class="font_size_define"><b>BRAND</b></td>
                    <td  class="font_size_define"><? echo $prog_data['brand_name']; ?></td>
                    <td class="font_size_define"><b>LOT-<? echo $prog_data['lot']; ?></b></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>COLOR</b></td>
                    <td  class="font_size_define"><? echo $prog_data['color_id']; ?></td>
                    <td class="font_size_define"><b>Color TYPE</b></td>
                    <td colspan="2"  class="font_size_define"><? echo $color_type[$prog_data['color_type_id']]; ?></td>
                </tr>
                <tr>
                    <td class="font_size_define"><b>P. QTY. (Kg)</b></td>
                    <td  class="font_size_define"><? echo number_format($prog_data['prog_qty'],2);?></td>
                    <td class="font_size_define"><b>M/C Distrb. Qty</b></td>
                    <td colspan="2" class="font_size_define"><? echo number_format($machin_prog[$mc_id][$prog_no]['distribution_qnty'],2); ?></td>

                </tr>

            </table>
            <? echo signature_table(213, $prog_data['company_id'], "700px",1,"20"); ?>
            <div class="page_break">&nbsp;</div>
        </div>
        <div>
            <script type="text/javascript" src="../../js/jquery.js"></script>
            <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
            <script>
                function generateBarcode( valuess,id ){
                    var value = valuess;//$("#barcodeValue").val();
                    var btype = 'code39';//$("input[name=btype]:checked").val();
                    var renderer ='bmp';// $("input[name=renderer]:checked").val();

                    var settings = {
                      output:renderer,
                      bgColor: '#FFFFFF',
                      color: '#000000',
                      barWidth: 2,
                      barHeight: 30,
                      moduleSize:5,
                      posX: 10,
                      posY: 20,
                      addQuietZone: 1
                    };
                    $("#barcode_img_id_<? echo $inc_no; ?>").html('11');
                     value = {code:value, rect: false};

                    $("#barcode_img_id_<? echo $inc_no; ?>").barcode(value, btype, settings);
                }
                generateBarcode('<? echo $prog_no; ?>');
             </script>
        </div>
        <?
        }
        $inc_no++;
    }
    exit();
}
?>