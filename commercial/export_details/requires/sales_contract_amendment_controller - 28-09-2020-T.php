<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");

include('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//--------------------------- Start-------------------------------------//
if ($action == "load_drop_down_buyer_search") {
    echo create_drop_down("cbo_buyer_name", 165, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");

    exit();
}
//already used 
if ($action == 'populate_data_from_sales_contract') {
    $data_array = sql_select("select id,contact_system_id,contract_no,contract_date, beneficiary_name, buyer_name, convertible_to_lc, lien_bank, lien_date, contract_value, currency_name, last_shipment_date, expiry_date, shipping_mode, tolerance, pay_term, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, internal_file_no, shipping_line, remarks, tenor, discount_clauses, bl_clause, claim_adjustment from com_sales_contract where id='$data'");
    foreach ($data_array as $row) {
        if ($db_type == 0) {
            $attached_po_id = return_field_value("group_concat(wo_po_break_down_id)", "com_sales_contract_order_info", "com_sales_contract_id=$data and status_active=1 and is_deleted=0");
        } else {
             $attached_po_id=return_field_value("LISTAGG(wo_po_break_down_id, ',') WITHIN GROUP (ORDER BY wo_po_break_down_id) as po_id", "com_sales_contract_order_info", "com_sales_contract_id=$data and status_active=1 and is_deleted=0", "po_id");
            /*$attached_po_id = return_field_value("rtrim(xmlagg(xmlelement(e,wo_po_break_down_id,',').extract('//text()') order by wo_po_break_down_id).GetClobVal(),',') AS po_id ", "com_sales_contract_order_info", "com_sales_contract_id=$data and status_active=1 and is_deleted=0", "po_id");
            if($db_type==2) $attached_po_id = $attached_po_id->load();*/
        }

        echo "document.getElementById('txt_system_id').value 			= '" . $row[csf("id")] . "';\n";
        echo "document.getElementById('txt_internal_file_no').value		= '" . $row[csf("internal_file_no")] . "';\n";
        echo "document.getElementById('txt_contract_no').value 			= '" . $row[csf("contract_no")] . "';\n";
        echo "document.getElementById('cbo_beneficiary_name').value 	= '" . $row[csf("beneficiary_name")] . "';\n";
        echo "document.getElementById('cbo_buyer_name').value			= '" . $row[csf("buyer_name")] . "';\n";
        echo "document.getElementById('txt_contract_value').value 		= '" . $row[csf("contract_value")] . "';\n";
        echo "document.getElementById('cbo_currency_name').value 		= '" . $row[csf("currency_name")] . "';\n";
        echo "document.getElementById('cbo_convertible_to_lc').value 	= '" . $row[csf("convertible_to_lc")] . "';\n";
        echo "document.getElementById('cbo_lien_bank').value 			= '" . $row[csf("lien_bank")] . "';\n";
        echo "document.getElementById('txt_lien_date').value 			= '" . change_date_format($row[csf("lien_date")]) . "';\n";
        echo "document.getElementById('txt_last_shipment_date').value 	= '" . change_date_format($row[csf("last_shipment_date")]) . "';\n";
        echo "document.getElementById('txt_expiry_date').value 			= '" . change_date_format($row[csf("expiry_date")]) . "';\n";
        echo "document.getElementById('txt_tolerance').value 			= '" . $row[csf("tolerance")] . "';\n";
        echo "document.getElementById('cbo_shipping_mode').value 		= '" . $row[csf("shipping_mode")] . "';\n";
        echo "document.getElementById('cbo_pay_term').value 			= '" . $row[csf("pay_term")] . "';\n";
        echo "document.getElementById('txt_tenor').value 				= '" . $row[csf("tenor")] . "';\n";
        echo "document.getElementById('txt_port_of_entry').value 		= '" . $row[csf("port_of_entry")] . "';\n";
        echo "document.getElementById('txt_port_of_loading').value 		= '" . $row[csf("port_of_loading")] . "';\n";
        echo "document.getElementById('txt_port_of_discharge').value 	= '" . $row[csf("port_of_discharge")] . "';\n";
        echo "document.getElementById('txt_discount_clauses').value 	= '" . $row[csf("discount_clauses")] . "';\n";
        echo "document.getElementById('txt_bl_clause').value 			= '" . $row[csf("bl_clause")] . "';\n";
        echo "document.getElementById('txt_claim_adjustment').value 	= '" . $row[csf("claim_adjustment")] . "';\n";
        echo "document.getElementById('txt_remarks').value 				= '" . $row[csf("remarks")] . "';\n";
        echo "document.getElementById('hidden_selectedID').value 		= '" . $attached_po_id . "';\n";

        echo "document.getElementById('txt_amendment_no').value 			= '';\n";
        echo "document.getElementById('update_id').value 					= '';\n";
        echo "document.getElementById('txt_amendment_date').value 			= '';\n";
        echo "document.getElementById('txt_amendment_value').value 			= '';\n";
        echo "document.getElementById('hide_amendment_value').value 		= '';\n";
        echo "document.getElementById('cbo_value_change_by').value 			= '0';\n";
        echo "document.getElementById('hide_value_change_by').value 		= '';\n";
        echo "document.getElementById('txt_last_shipment_date_amnd').value	= '" . change_date_format($row[csf("last_shipment_date")]) . "';\n";
        echo "document.getElementById('txt_expiry_date_amend').value 		= '" . change_date_format($row[csf("expiry_date")]) . "';\n";
        echo "document.getElementById('cbo_shipping_mode_amnd').value 		= '" . $row[csf("shipping_mode")] . "';\n";
        echo "document.getElementById('cbo_inco_term').value 				= '" . $row[csf("inco_term")] . "';\n";
        echo "document.getElementById('txt_inco_term_place').value 			= '" . $row[csf("inco_term_place")] . "';\n";
        echo "document.getElementById('txt_port_of_entry_amnd').value 		= '" . $row[csf("port_of_entry")] . "';\n";
        echo "document.getElementById('txt_port_of_loading_amnd').value 	= '" . $row[csf("port_of_loading")] . "';\n";
        echo "document.getElementById('txt_port_of_discharge_amnd').value 	= '" . $row[csf("port_of_discharge")] . "';\n";
        echo "document.getElementById('cbo_pay_term_amnd').value 			= '" . $row[csf("pay_term")] . "';\n";
        echo "document.getElementById('txt_tenor_amnd').value 				= '" . $row[csf("tenor")] . "';\n";
        echo "document.getElementById('txt_claim_adjustment_amnd').value 	= '';\n";
        echo "document.getElementById('cbo_claim_adjust_by').value 			= '0';\n";
        echo "document.getElementById('hide_claim_adjust_by').value 		= '';\n";
        echo "document.getElementById('txt_discount_clauses_amnd').value 	= '" . $row[csf("discount_clauses")] . "';\n";
        echo "document.getElementById('txt_bl_clause_amnd').value 			= '" . $row[csf("bl_clause")] . "';\n";
        echo "document.getElementById('txt_remarks_amnd').value 			= '" . $row[csf("remarks")] . "';\n";

        echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_amendment_save',1);\n";

        exit();
    }
}

//alredy used
if ($action == "sales_contract_search") {
    echo load_html_head_contents("Sales Contract Form", "../../../", 1, 1, '', '1', '');
    ?>
    <script>
        function js_set_value(id)
        {
            $('#hidden_sales_contract_id').val(id);
            parent.emailwindow.hide();
        }

    </script>

    </head>

    <body>
        <div align="center" style="width:1020px;">
            <form name="searchscfrm"  id="searchscfrm">
                <fieldset style="width:100%;">
                    <legend>Enter search words</legend>           
                    <table cellpadding="0" cellspacing="0" width="80%" class="rpt_table">
                        <thead>
                        <th>Company</th>
                        <th>Buyer</th>
                        <th>Search By</th>
                        <th>Enter</th>
                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" /><input type="hidden" name="id_field" id="id_field" value="" /></th>
                        </thead>
                        <tr class="general">
                            <td>
                                <?
                                echo create_drop_down("cbo_company_name", 165, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--- Select ---", 0, "load_drop_down( 'sales_contract_amendment_controller', this.value, 'load_drop_down_buyer_search', 'buyer_td_id' );");
                                ?>                      
                            </td>
                            <td id="buyer_td_id">
                                <?
                                echo create_drop_down("cbo_buyer_name", 165, $blank_array, "", 1, "--- Select ---", $selected, "");
                                ?>
                            </td>                  
                            <td> 
                                <?
                                $arr = array(1 => 'SC No', 2 => 'File No');
                                echo create_drop_down("cbo_search_by", 165, $arr, "", 0, "--- Select ---", 0, "");
                                ?>
                            </td>						
                            <td id="search_by_td">
                                <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                                <input type="hidden" id="hidden_sales_contract_id" />
                            </td>                       
                            <td>
                                <input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_sc_search_list_view', 'search_div', 'sales_contract_amendment_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                            </td>
                        </tr>
                    </table>
                    <div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
                </fieldset>
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action == "create_sc_search_list_view") {
    $data = explode('**', $data);
    if ($data[0] != 0) {
        $company_id = " and beneficiary_name = $data[0]";
    } else {
        $company_id = "";
    }
    if ($data[1] != 0) {
        $buyer_id = " and buyer_name = $data[1]";
    } else {
        $buyer_id = "";
    }
    $search_by = $data[2];
    $search_text = $data[3];

    if ($search_by == 0) {
        $search_condition = "";
    } else if ($search_by == 1) {
        $search_condition = " and contract_no like '%$search_text%'";
    } else if ($search_by == 2) {
        $search_condition = " and internal_file_no like '%$search_text%'";
    }

    if ($db_type == 0)
        $year_field = "YEAR(insert_date) as year,";
    else if ($db_type == 2)
        $year_field = "to_char(insert_date,'YYYY') as year,";
    else
        $year_field = ""; //defined Later

    $sql = "select id,contract_no, internal_file_no, $year_field contact_prefix_number, contact_system_id,beneficiary_name, buyer_name,applicant_name, contract_value, lien_bank, pay_term, last_shipment_date, contract_date from com_sales_contract where status_active=1 and is_deleted=0 $company_id $buyer_id $search_condition order by id";

    $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
    $comp = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
    $bank_arr = return_library_array("select id, bank_name from lib_bank", 'id', 'bank_name');
    $arr = array(4 => $comp, 5 => $buyer_arr, 6 => $buyer_arr, 8 => $bank_arr, 9 => $pay_term);
    echo create_list_view("list_view", "Contract No,File No,Year,System ID,Company,Buyer Name,Applicant Name,Contract Value,Lien Bank,Pay Term,Ship Date,Contract Date", "80,80,50,65,70,70,70,100,110,70,80,70", "1020", "315", 0, $sql, "js_set_value", "id", "", 1, "0,0,0,0,beneficiary_name,buyer_name,applicant_name,0,lien_bank,pay_term,0,0", $arr, "contract_no,internal_file_no,year,contact_prefix_number,beneficiary_name,buyer_name,applicant_name,contract_value,lien_bank,pay_term,last_shipment_date,contract_date", "", '', '0,0,0,0,0,0,0,2,0,0,3,3');
    exit();
}


//already used
if ($action == "order_popup") {
    echo load_html_head_contents("Sales Contract Form", "../../../", 1, 1, '', '1', '');
    extract($_REQUEST);
    ?>

    <script>
                                    var selected_id = new Array, selected_name = new Array();
                                    function check_all_data() {
                                        var tbl_row_count = document.getElementById('tbl_list_search').rows.length;

                                        tbl_row_count = tbl_row_count - 1;
                                        for (var i = 1; i <= tbl_row_count; i++) {
                                            js_set_value(i);
                                        }
                                    }

                                    function toggle(x, origColor) {
                                        var newColor = 'yellow';
                                        if (x.style) {
                                            x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
                                        }
                                    }

                                    function js_set_value(str) {
                                        toggle(document.getElementById('search' + str), '#FFFFCC');

                                        if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
                                            selected_id.push($('#txt_individual_id' + str).val());

                                        } else {
                                            for (var i = 0; i < selected_id.length; i++) {
                                                if (selected_id[i] == $('#txt_individual_id' + str).val())
                                                    break;
                                            }
                                            selected_id.splice(i, 1);
                                        }
                                        var id = '';
                                        for (var i = 0; i < selected_id.length; i++) {
                                            id += selected_id[i] + ',';
                                        }
                                        id = id.substr(0, id.length - 1);

                                        $('#txt_selected_id').val(id);
                                    }

    </script>

    </head>

    <body>
        <div align="center" style="width:100%;" >
            <form name="searchpofrm"  id="searchpofrm">
                <fieldset style="width:830px">
                    <table width="650" cellspacing="0" cellpadding="0" class="rpt_table">
                        <thead>
                        <th>Company</th>
                        <th>Search By</th>
                        <th>Search</th>
                        <th>File No</th>
                        <th>SC/LC</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th> 
                        </thead>
                        <tr class="general">
                            <td align="center">
                                <?
                                echo create_drop_down("cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $company_id, "");
                                ?>
                            </td>
                            <td align="center">
                                <?
                                $arr = array(1 => 'PO Number', 2 => 'Job No', 3 => 'Style Ref No');
                                echo create_drop_down("cbo_search_by", 150, $arr, "", 0, "--- Select ---", '', "");
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_text" id="txt_search_text" class="text_boxes" style="width:150px" />
                                <input type="hidden" id="hidden_type" value="<? echo $types; ?>" />	
                                <input type="hidden" id="hidden_buyer_id" value="<? echo $buyer_id; ?>" />	
                                <input type="hidden" id="hidden_po_selectedID" value="<? echo $selectID; ?>" />
                                <input type="hidden" id="sales_contractID" value="<? echo $sales_contractID; ?>" />
                                <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />				
                            </td>
                            <td>
                                <input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes">
                            </td>
                            <td>
                                <input type="text" id="txt_sc_lc" name="txt_sc_lc" class="text_boxes">
                            </td>
                            <td align="center">
                                <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_company_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_text').value + '**' + document.getElementById('hidden_type').value + '**' + document.getElementById('hidden_buyer_id').value + '**' + document.getElementById('hidden_po_selectedID').value + '**' + document.getElementById('sales_contractID').value + '**' + document.getElementById('txt_file_no').value + '**' + document.getElementById('txt_sc_lc').value, 'create_po_search_list_view', 'search_div', 'sales_contract_amendment_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
                            </td>
                        </tr>
                    </table>
                    <div style="margin-top:5px" id="search_div" align="left"></div>
                </fieldset>
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action == "create_po_search_list_view") {
    $data = explode('**', $data);
    if ($data[0] != 0)
        $company = " and wm.company_name='$data[0]'";
    else {
        echo "Please Select Company First.";
        die;
    }
    if ($data[2] != '') {
        if ($data[1] == 1)
            $search_text = " and wb.po_number like '" . trim($data[2]) . "%'";
        else if ($data[1] == 2)
            $search_text = " and wm.job_no like '" . trim($data[2]) . "%'";
        else if ($data[1] == 3)
            $search_text = " and wm.style_ref_no like '" . trim($data[2]) . "%'";
    }
    $action_types = $data[3];
    $buyer_id = $data[4];
    if ($data[5] == "")
        $selected_order_id = "";
    else
        $selected_order_id = "and wb.id not in (" . $data[5] . ")";
    $sales_contractID = $data[6];
    $txt_file_no = $data[7];
    $txt_sc_lc = $data[8];
    if ($txt_file_no == "")
        $file_no_cond = "";
    else
        $file_no_cond = "and wb.file_no = '" . $data[7] . "'";
    if (trim($data[8]) == "")
        $txt_sc_lc_cond = "";
    else
        $txt_sc_lc_cond = "and wb.sc_lc like '%" . trim($data[8]) . "%'";


    if ($action_types == 'attached_po_status') {
        $lc_details = return_library_array("select id, export_lc_no from com_export_lc", 'id', 'export_lc_no');
        $sc_details = return_library_array("select id, contract_no from com_sales_contract", 'id', 'contract_no');

        $lc_array = array();
        $sc_array = array();
        $attach_qnty_array = array();
        $sql_lc_sc = "select com_export_lc_id as id, wo_po_break_down_id, sum(attached_qnty) as qnty, 1 as type from com_export_lc_order_info where status_active=1 and is_deleted=0 group by wo_po_break_down_id, com_export_lc_id
		union all
		select com_sales_contract_id as id, wo_po_break_down_id, sum(attached_qnty) as qnty, 2 as type from com_sales_contract_order_info where status_active=1 and is_deleted=0 group by wo_po_break_down_id, com_sales_contract_id
		";
        $lc_sc_Array = sql_select($sql_lc_sc);
        foreach ($lc_sc_Array as $row_lc_sc) {
            if (array_key_exists($row_lc_sc[csf('wo_po_break_down_id')], $attach_qnty_array)) {
                $attach_qnty_array[$row_lc_sc[csf('wo_po_break_down_id')]] += $row_lc_sc[csf('qnty')];
            } else {
                $attach_qnty_array[$row_lc_sc[csf('wo_po_break_down_id')]] = $row_lc_sc[csf('qnty')];
            }

            if ($row_lc_sc[csf('type')] == 1) {
                if ($row_lc_sc[csf('qnty')] > 0) {
                    if (array_key_exists($row_lc_sc[csf('wo_po_break_down_id')], $lc_array)) {
                        $lc_array[$row_lc_sc[csf('wo_po_break_down_id')]] .= "," . $row_lc_sc[csf('id')];
                    } else {
                        $lc_array[$row_lc_sc[csf('wo_po_break_down_id')]] = $row_lc_sc[csf('id')];
                    }
                }
            } else {
                if ($row_lc_sc[csf('qnty')] > 0) {
                    if (array_key_exists($row_lc_sc[csf('wo_po_break_down_id')], $sc_array)) {
                        $sc_array[$row_lc_sc[csf('wo_po_break_down_id')]] .= "," . $row_lc_sc[csf('id')];
                    } else {
                        $sc_array[$row_lc_sc[csf('wo_po_break_down_id')]] = $row_lc_sc[csf('id')];
                    }
                }
            }
        }

        $sql = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.gmts_item_id, wb.unit_price,wb.file_no,wb.sc_lc FROM wo_po_break_down wb, wo_po_details_master wm WHERE wb.job_no_mst = wm.job_no and wm.buyer_name like '$buyer_id' $company $search_text $file_no_cond $txt_sc_lc_cond and wb.is_deleted = 0 AND wb.status_active = 1 group by wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date, wb.job_no_mst, wm.style_ref_no, wm.gmts_item_id, wb.unit_price, wb.file_no, wb.sc_lc";
        ?>
        <div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1060" class="rpt_table" >
                <thead>
                <th width="30">SL</th>
                <th width="110">PO No</th>
                <th width="110">Item</th>
                <th width="110">Style No</th>
                <th width="90">PO Quantity</th>
                <th width="100">Price</th>
                <th width="80">Shipment Date</th>
                <th width="100">Attached With</th>
                <th>LC/SC</th>
                <th width="80">File No</th>
                <th width="80">SC/LC No</th>
                </thead>
            </table>
            <div style="width:1080px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1060" class="rpt_table" id="tbl_list_search" >
                    <?
                    $i = 1;
                    $nameArray = sql_select($sql);
                    foreach ($nameArray as $selectResult) {
                        if (array_key_exists($selectResult[csf('id')], $attach_qnty_array)) {
                            $order_attached_qnty = $attach_qnty_array[$selectResult[csf('id')]];

                            if ($order_attached_qnty >= $selectResult[csf('po_quantity')]) {
                                $all_lc_id = explode(",", $lc_array[$selectResult[csf('id')]]);
                                foreach ($all_lc_id as $lc_id) {
                                    if ($lc_id != 0) {
                                        if ($i % 2 == 0)
                                            $bgcolor = "#E9F3FF";
                                        else
                                            $bgcolor = "#FFFFFF";
                                        ?>	
                                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                            <td width="30"><? echo $i; ?></td>
                                            <td width="110"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                                            <td width="110">
                                                <p>
                                                    <?
                                                    $gmts_item = '';
                                                    $gmts_item_id = explode(",", $selectResult[csf('gmts_item_id')]);
                                                    foreach ($gmts_item_id as $item_id) {
                                                        if ($gmts_item == "")
                                                            $gmts_item = $garments_item[$item_id];
                                                        else
                                                            $gmts_item .= "," . $garments_item[$item_id];
                                                    }
                                                    echo $gmts_item;
                                                    ?>
                                                </p>
                                            </td>
                                            <td width="110"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                                            <td width="90" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
                                            <td width="100" align="right"><? echo number_format($selectResult[csf('po_total_price')], 2); ?></td>
                                            <td align="center" width="80"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
                                            <td width="100"><p><? echo $lc_details[$lc_id]; ?></p></td>
                                            <td align="center"><? echo 'LC'; ?></td>
                                            <td width="80" align="center"><p><? echo $selectResult[csf('file_no')]; ?></p></td>
                                            <td width="80" align="center"><p><? echo $selectResult[csf('sc_lc')]; ?></p></td>
                                        </tr>
                                        <?
                                        $i++;
                                    }
                                }

                                $all_sc_id = explode(",", $sc_array[$selectResult[csf('id')]]);

                                foreach ($all_sc_id as $sc_id) {
                                    if ($sc_id != 0) {
                                        if ($i % 2 == 0)
                                            $bgcolor = "#E9F3FF";
                                        else
                                            $bgcolor = "#FFFFFF";
                                        ?>	
                                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                            <td width="30"><? echo $i; ?></td>
                                            <td width="110"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                                            <td width="110">
                                                <p>
                                                    <?
                                                    $gmts_item = '';
                                                    $gmts_item_id = explode(",", $selectResult[csf('gmts_item_id')]);
                                                    foreach ($gmts_item_id as $item_id) {
                                                        if ($gmts_item == "")
                                                            $gmts_item = $garments_item[$item_id];
                                                        else
                                                            $gmts_item .= "," . $garments_item[$item_id];
                                                    }
                                                    echo $gmts_item;
                                                    ?>
                                                </p>
                                            </td>
                                            <td width="110"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                                            <td width="90" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
                                            <td width="100" align="right"><? echo number_format($selectResult[csf('po_total_price')], 2); ?></td>
                                            <td align="center" width="80"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
                                            <td width="100"><p><? echo $sc_details[$sc_id]; ?></p></td>
                                            <td align="center"><? echo 'SC'; ?></td>
                                            <td width="80" align="center"><p><? echo $selectResult[csf('file_no')]; ?></p></td>
                                            <td width="80" align="center"><p><? echo $selectResult[csf('sc_lc')]; ?></p></td>
                                        </tr>
                                        <?
                                        $i++;
                                    }
                                }
                            }
                        }
                    }
                    ?>
                </table>
            </div>
        </div>                   
        <?
        exit();
    }

    if ($action_types == 'order_select_popup') {
        $sql = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.gmts_item_id, wb.unit_price,wb.file_no,wb.sc_lc FROM wo_po_break_down wb, wo_po_details_master wm WHERE wb.job_no_mst = wm.job_no and wm.buyer_name like '$buyer_id' $selected_order_id $company $search_text $file_no_cond $txt_sc_lc_cond and wb.is_deleted = 0 AND wb.status_active = 1 group by wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date, wb.job_no_mst, wm.style_ref_no, wm.gmts_item_id, wb.unit_price, wb.file_no, wb.sc_lc";
        ?>
        <div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1060" class="rpt_table" >
                <thead>
                <th width="40">SL</th>
                <th width="130">PO No</th>
                <th width="130">Item</th>
                <th width="120">Style No</th>
                <th width="110">PO Quantity</th>
                <th width="120">Price</th>
                <th width="100">Shipment Date</th>
                <th width="80">File No</th>
                <th width="80">SC/LC No</th>
                </thead>
            </table>
            <div style="width:1080px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1060" class="rpt_table" id="tbl_list_search" >
                    <?
                    $i = 1;

                    $lc_attached_qnty_arr = return_library_array("select wo_po_break_down_id, sum(attached_qnty) as qty from com_export_lc_order_info where status_active = 1 and is_deleted=0 group by wo_po_break_down_id", "wo_po_break_down_id", "qty");
                    $sc_attached_qnty_arr = return_library_array("select wo_po_break_down_id, sum(attached_qnty) as qty from com_sales_contract_order_info where status_active = 1 and is_deleted=0 group by wo_po_break_down_id", "wo_po_break_down_id", "qty");

                    $nameArray = sql_select($sql);
                    foreach ($nameArray as $selectResult) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        //$lc_attached_qnty=return_field_value("sum(attached_qnty)","com_export_lc_order_info","wo_po_break_down_id='".$selectResult[csf('id')]."' and status_active = 1 and is_deleted=0");
                        //$sc_attached_qnty=return_field_value("sum(attached_qnty)","com_sales_contract_order_info","wo_po_break_down_id='".$selectResult[csf('id')]."' and status_active = 1 and is_deleted=0");
                        $lc_attached_qnty = $lc_attached_qnty_arr[$selectResult[csf('id')]];
                        $sc_attached_qnty = $sc_attached_qnty_arr[$selectResult[csf('id')]];
                        $order_attached_qnty = $sc_attached_qnty + $lc_attached_qnty;

                        if ($order_attached_qnty < $selectResult[csf('po_quantity')]) {
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)"> 
                                <td width="40" align="center"><?php echo "$i"; ?>
                                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>	
                                </td>	
                                <td width="130"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                                <td width="130">
                                    <p>
                                        <?
                                        $gmts_item = '';
                                        $gmts_item_id = explode(",", $selectResult[csf('gmts_item_id')]);
                                        foreach ($gmts_item_id as $item_id) {
                                            if ($gmts_item == "")
                                                $gmts_item = $garments_item[$item_id];
                                            else
                                                $gmts_item .= "," . $garments_item[$item_id];
                                        }
                                        echo $gmts_item;
                                        ?>
                                    </p>
                                </td> 
                                <td width="120"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                                <td width="110" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
                                <td width="120" align="right"><? echo number_format($selectResult[csf('po_total_price')], 2); ?></td>
                                <td width="100" align="center"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
                                <td width="80" align="center"><p><? echo $selectResult[csf('file_no')]; ?></p></td>
                                <td width="80" align="center"><p><? echo $selectResult[csf('sc_lc')]; ?></p></td>
                            </tr>
                            <?
                            $i++;
                        }
                    }
                    ?>
                </table>
            </div>
            <table width="790" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                        <div style="width:100%"> 
                            <div style="width:50%; float:left" align="left">
                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                            </div>
                            <div style="width:50%; float:left" align="left">
                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <?
    }
    exit();
}

//already user
if ($action == "show_po_active_listview") {
    $sql = "select wb.id, ci.id as idd, wm.gmts_item_id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.order_uom, wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$data' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id";

    /* $arr=array(9=>$attach_detach_array);
      echo create_list_view("list_view", "Order Number,Order Qty,Order Value,Attached Qty,Rate,Attached Value,Style Ref,Item,Job No,Status", "100,100,100,100,60,100,150,150,100,80","1050","200",0, $sql, "get_php_form_data", "idd", "'populate_order_details_form_data'", 0, "0,0,0,0,0,0,0,0,0,status_active", $arr, "po_number,po_quantity,po_total_price,attached_qnty,attached_rate,attached_value,style_ref_no,style_description,job_no_mst,status_active", "requires/sales_contract_amendment_controller",'','0,1,1,1,2,2,0,0,0,0','1,po_quantity,po_total_price,attached_qnty,0,attached_value,0,0,0,0'); */
    ?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" >
            <thead>
            <th width="100">Order Number</th>
            <th width="80">Order Qty</th>
            <th width="100">Order Value</th>
            <th width="80">Attached Qty</th>
            <th width="50">UOM</th>
            <th width="60">Rate</th>
            <th width="100">Attached Value</th>
            <th width="100">Attached Qty (Pcs)</th>
            <th width="120">Style Ref</th>
            <th width="130">Gmts. Item</th>
            <th width="80">Job No</th>
            <th>Status</th>
            </thead>
        </table>
        <div style="width:1100px; overflow-y:scroll; max-height:200px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1082" class="rpt_table" id="tbl_list_search" >     
                <?
                $i = 1;
                $nameArray = sql_select($sql);
                foreach ($nameArray as $selectResult) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    $order_qnty_in_pcs = $selectResult[csf('attached_qnty')] * $selectResult[csf('ratio')];
                    $total_order_qnty_in_pcs += $order_qnty_in_pcs;
                    $total_attc_value += $selectResult[csf('attached_value')];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="get_php_form_data('<? echo $selectResult[csf('idd')]; ?>', 'populate_order_details_form_data', 'requires/sales_contract_amendment_controller')"> 
                        <td width="100"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                        <td width="80" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
                        <td width="100" align="right"><? echo number_format($selectResult[csf('po_total_price')], 2); ?></td>
                        <td width="80" align="right"><? echo $selectResult[csf('attached_qnty')]; ?></td>
                        <td width="50" align="center"><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></td>
                        <td width="60" align="right"><? echo number_format($selectResult[csf('attached_rate')], 2); ?></td>
                        <td width="100" align="right"><? echo number_format($selectResult[csf('attached_value')], 2); ?></td>
                        <td width="100" align="right"><? echo $order_qnty_in_pcs; ?></td>
                        <td width="120"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                        <td width="130">
                            <p>
                                <?
                                $gmts_item = '';
                                $gmts_item_id = explode(",", $selectResult[csf('gmts_item_id')]);
                                foreach ($gmts_item_id as $item_id) {
                                    if ($gmts_item == "")
                                        $gmts_item = $garments_item[$item_id];
                                    else
                                        $gmts_item .= "," . $garments_item[$item_id];
                                }
                                echo $gmts_item;
                                ?>
                            </p>
                        </td> 
                        <td width="80"><? echo $selectResult[csf('job_no_mst')]; ?></td>
                        <td><? echo $attach_detach_array[$selectResult[csf('status_active')]]; ?></td>	
                    </tr>
                    <?
                    $i++;
                }
                ?>
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table">
            <tfoot>
            <th width="100">&nbsp;</th>
            <th width="80">&nbsp;</th>
            <th width="100">&nbsp;</th>
            <th width="80">&nbsp;</th>
            <th width="50">&nbsp;</th>
            <th width="60">Total</th>
            <th width="100" align="right"><? echo number_format($total_attc_value, 2); ?></th>
            <th width="100" align="right"><? echo number_format($total_order_qnty_in_pcs, 0); ?></th>
            <th width="120">&nbsp;</th>
            <th width="130">&nbsp;</th>
            <th width="80">&nbsp;</th>
            <th>&nbsp;</th>
            </tfoot>
        </table>
    </div>   
    <?
    exit();
}

if ($action == "populate_order_details_form_data") {
    $data_array = sql_select("select wb.id, ci.id as idd, wm.style_description, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date,wb.job_no_mst, wm.style_ref_no, wm.gmts_item_id, wb.unit_price,ci.attached_qnty,ci.attached_rate, ci.attached_value, ci.status_active, ci.com_sales_contract_id, ci.fabric_description, ci.category_no, ci.hs_code from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.id='$data' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id");

    foreach ($data_array as $row) {
        echo "$('#tbl_order_list tbody tr:not(:first)').remove();\n";

        $gmts_item = '';
        $gmts_item_id = explode(",", $row[csf('gmts_item_id')]);
        foreach ($gmts_item_id as $item_id) {
            if ($gmts_item == "")
                $gmts_item = $garments_item[$item_id];
            else
                $gmts_item .= "," . $garments_item[$item_id];
        }

        echo "document.getElementById('txtordernumber_1').value 			= '" . $row[csf("po_number")] . "';\n";
        echo "document.getElementById('txtorderqnty_1').value 				= '" . $row[csf("po_quantity")] . "';\n";
        echo "document.getElementById('txtordervalue_1').value 			= '" . $row[csf("po_total_price")] . "';\n";
        echo "document.getElementById('txtattachedqnty_1').value 			= '" . $row[csf("attached_qnty")] . "';\n";
        echo "document.getElementById('hideattachedqnty_1').value 			= '" . $row[csf("attached_qnty")] . "';\n";
        echo "document.getElementById('hiddenunitprice_1').value 			= '" . $row[csf("attached_rate")] . "';\n";
        echo "document.getElementById('txtattachedvalue_1').value 			= '" . $row[csf("attached_value")] . "';\n";
        echo "document.getElementById('txtstyleref_1').value 				= '" . $row[csf("style_ref_no")] . "';\n";
        echo "document.getElementById('txtitemname_1').value 				= '" . $gmts_item . "';\n";
        echo "document.getElementById('txtjobno_1').value 					= '" . $row[csf("job_no_mst")] . "';\n";
        echo "document.getElementById('cbopostatus_1').value 				= '" . $row[csf("status_active")] . "';\n";
        echo "document.getElementById('txtfabdescrip_1').value 				= '" . $row[csf("fabric_description")] . "';\n";
        echo "document.getElementById('txtcategory_1').value 				= '" . $row[csf("category_no")] . "';\n";
        echo "document.getElementById('txthscode_1').value 				= '" . $row[csf("hs_code")] . "';\n";

        echo "document.getElementById('hiddenwopobreakdownid_1').value 	= '" . $row[csf("id")] . "';\n";
        echo "document.getElementById('hiddensalescontractorderid').value 	= '" . $row[csf("idd")] . "';\n";
        echo "document.getElementById('txt_tot_row').value 	= '1';\n";

        echo "math_operation( 'totalOrderqnty', 'txtorderqnty_', '+', 1 );\n";
        echo "math_operation( 'totalOrdervalue', 'txtordervalue_', '+', 1 );\n";
        echo "math_operation( 'totalAttachedqnty', 'txtattachedqnty_', '+', 1 );\n";
        echo "math_operation( 'totalAttachedvalue', 'txtattachedvalue_', '+', 1 );\n";

        $order_attahed_qnty_sc = 0;
        $order_attahed_qnty_lc = 0;
        $order_attahed_val_sc = 0;
        $order_attahed_val_lc = 0;
        $sc_no = '';
        $lc_no = '';
        $sql_sc = "SELECT a.contract_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_sales_contract a, com_sales_contract_order_info b WHERE a.id=b.com_sales_contract_id and b.wo_po_break_down_id='" . $row[csf("id")] . "' and b.id!='" . $data . "' and b.status_active = 1 and b.is_deleted=0 group by a.id, a.contract_no";
        $result_array_sc = sql_select($sql_sc);
        foreach ($result_array_sc as $scArray) {
            if ($sc_no == "")
                $sc_no = $scArray[csf('contract_no')];
            else
                $sc_no .= "," . $scArray[csf('contract_no')];
            $order_attahed_qnty_sc += $scArray[csf('at_qt')];
            //$order_attahed_val_sc+=$scArray[csf('at_val')];
        }

        $sql_lc = "SELECT a.export_lc_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_export_lc a, com_export_lc_order_info b WHERE a.id=b.com_export_lc_id and b.wo_po_break_down_id='" . $row[csf("id")] . "' and b.status_active = 1 and b.is_deleted=0 group by a.id, a.export_lc_no";
        $result_array_sc = sql_select($sql_lc);
        foreach ($result_array_sc as $lcArray) {
            if ($lc_no == "")
                $lc_no = $lcArray[csf('export_lc_no')];
            else
                $lc_no .= "," . $lcArray[csf('export_lc_no')];
            $order_attahed_qnty_lc += $lcArray[csf('at_qt')];
            //$order_attahed_val_lc+=$lcArray[csf('at_val')];
        }

        $order_attached_qnty = $order_attahed_qnty_sc + $order_attahed_qnty_lc;
        //$order_attached_val=$order_attahed_val_sc+$order_attahed_val_lc;

        echo "document.getElementById('order_attached_qnty_1').value 		= '" . $order_attached_qnty . "';\n";
        echo "document.getElementById('order_attached_lc_no_1').value 		= '" . $lc_no . "';\n";
        echo "document.getElementById('order_attached_lc_qty_1').value 	    = '" . $order_attahed_qnty_lc . "';\n";
        echo "document.getElementById('order_attached_sc_no_1').value 		= '" . $sc_no . "';\n";
        echo "document.getElementById('order_attached_sc_qty_1').value 	    = '" . $order_attahed_qnty_sc . "';\n";

        if ($db_type == 0) {
            $attached_po_id = return_field_value("group_concat(wo_po_break_down_id)", "com_sales_contract_order_info", "com_sales_contract_id='" . $row[csf("com_sales_contract_id")] . "' and status_active=1 and is_deleted=0");
        } else {
            $attached_po_id = return_field_value("LISTAGG(wo_po_break_down_id, ',') WITHIN GROUP (ORDER BY wo_po_break_down_id) as po_id", "com_sales_contract_order_info", "com_sales_contract_id='" . $row[csf("com_sales_contract_id")] . "' and status_active=1 and is_deleted=0", "po_id");

            /*$attached_po_id = return_field_value("rtrim(xmlagg(xmlelement(e,wo_po_break_down_id,',').extract('//text()') order by wo_po_break_down_id).GetClobVal(),',') AS po_id ", "com_sales_contract_order_info", "com_sales_contract_id='" . $row[csf("com_sales_contract_id")] . "' and status_active=1 and is_deleted=0", "po_id");
            $attached_po_id = $attached_po_id->load();*/
        }

        echo "document.getElementById('hidden_selectedID').value 		= '" . $attached_po_id . "';\n";

        echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_po_selection_save',2);\n";
        exit();
    }
}

if ($action == "populate_attached_po_id") {
    $data = explode("**", $data);
    $sc_id = $data[0];
    $type = $data[1];
    $amnd_id = $data[2];

    if ($db_type == 0) {
        $attached_po_id = return_field_value("group_concat(wo_po_break_down_id)", "com_sales_contract_order_info", "com_sales_contract_id='$sc_id' and status_active=1 and is_deleted=0");
    } else {
        $attached_po_id = return_field_value("LISTAGG(wo_po_break_down_id, ',') WITHIN GROUP (ORDER BY wo_po_break_down_id) as po_id", "com_sales_contract_order_info", "com_sales_contract_id='$sc_id' and status_active=1 and is_deleted=0", "po_id");
        /*$attached_po_id = return_field_value("rtrim(xmlagg(xmlelement(e,wo_po_break_down_id,',').extract('//text()') order by wo_po_break_down_id).GetClobVal(),',') AS po_id ", "com_sales_contract_order_info", "com_sales_contract_id='$sc_id' and status_active=1 and is_deleted=0", "po_id");
        $attached_po_id = $attached_po_id->load();*/
    }

    if ($type == 2) {
        $con = connect();
        execute_query("update com_sales_contract_amendment set po_id='$attached_po_id' where id='$amnd_id'");
        disconnect($con);
    }

    echo "document.getElementById('hidden_selectedID').value 		= '" . $attached_po_id . "';\n";
    exit();
}

if ($action == "order_list_for_attach") {
    $explode_data = explode("**", $data); //0->wo_po_break_down id's, 1->table row
    $data = $explode_data[0];
    $table_row = $explode_data[1];

    if ($data != "") {
        $data_array = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no,wm.style_description,wb.unit_price FROM wo_po_break_down wb, wo_po_details_master wm WHERE wb.job_no_mst = wm.job_no AND wb.id in ($data) AND wb.is_deleted = 0 AND wb.status_active = 1";

        $data_array = sql_select($data_array);
        foreach ($data_array as $row) {
            $table_row++;
            $order_attahed_qnty_sc = 0;
            $order_attahed_qnty_lc = 0;
            $order_attahed_val_sc = 0;
            $order_attahed_val_lc = 0;
            $sc_no = '';
            $lc_no = '';
            $sql_sc = "SELECT a.contract_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_sales_contract a, com_sales_contract_order_info b WHERE a.id=b.com_sales_contract_id and b.wo_po_break_down_id='" . $row[csf("id")] . "' and b.status_active = 1 and b.is_deleted=0 group by a.id, a.contract_no";
            $result_array_sc = sql_select($sql_sc);
            foreach ($result_array_sc as $scArray) {
                if ($sc_no == "")
                    $sc_no = $scArray[csf('export_lc_no')];
                else
                    $sc_no .= "," . $scArray[csf('contract_no')];
                $order_attahed_qnty_sc += $scArray[csf('at_qt')];
                $order_attahed_val_sc += $scArray[csf('at_val')];
            }

            $sql_lc = "SELECT a.export_lc_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_export_lc a, com_export_lc_order_info b WHERE a.id=b.com_export_lc_id and b.wo_po_break_down_id='" . $row[csf("id")] . "' and b.status_active = 1 and b.is_deleted=0 group by a.id, a.export_lc_no";
            $result_array_sc = sql_select($sql_lc);
            foreach ($result_array_sc as $lcArray) {
                if ($lc_no == "")
                    $lc_no = $lcArray[csf('export_lc_no')];
                else
                    $lc_no .= "," . $lcArray[csf('export_lc_no')];
                $order_attahed_qnty_lc += $lcArray[csf('at_qt')];
                $order_attahed_val_lc += $lcArray[csf('at_val')];
            }

            $order_attached_qnty = $order_attahed_qnty_sc + $order_attahed_qnty_lc;
            $order_attached_val = $order_attahed_val_sc + $order_attahed_val_lc;

            $remaining_qnty = $row[csf("po_quantity")] - $order_attached_qnty;
            $remaining_value = $row[csf("po_total_price")] - $order_attached_val;
            ?>	
            <tr class="general" id="tr_<? echo $table_row; ?>">
                <td>
                    <input type="text" name="txtordernumber_<? echo $table_row; ?>" id="txtordernumber_<? echo $table_row; ?>" class="text_boxes" style="width:100px"  value="<? echo $row[csf("po_number")]; ?>" onDblClick= "openmypage('requires/sales_contract_controller.php?action=order_popup&types=order_select_popup&buyer_id=' + document.getElementById('cbo_buyer_name').value + '&selectID=' + document.getElementById('hidden_selectedID').value + '&sales_contractID=' + document.getElementById('txt_system_id').value + '&company_id=' + document.getElementById('cbo_beneficiary_name').value, 'PO Selection Form', '<? echo $table_row; ?>')" readonly= "readonly" placeholder="Double Click" />
                    <input type="hidden" name="hiddenwopobreakdownid_<? echo $table_row; ?>" id="hiddenwopobreakdownid_<? echo $table_row; ?>" readonly= "readonly" value="<? echo $row[csf("id")]; ?>" />
                </td>
                <td>
                    <input type="text" name="txtorderqnty_<? echo $table_row; ?>" id="txtorderqnty_<? echo $table_row; ?>" class="text_boxes" style="width:65px; text-align:right" readonly= "readonly" value="<? echo $row[csf("po_quantity")]; ?>" />
                </td>
                <td>
                    <input type="text" name="txtordervalue_<? echo $table_row; ?>" id="txtordervalue_<? echo $table_row; ?>" class="text_boxes" style="width:80px; text-align:right" readonly= "readonly" value="<? echo number_format($row[csf("po_total_price")], 2, '.', ''); ?>" />
                </td>
                <td>
                    <input type="text" name="txtattachedqnty_<? echo $table_row; ?>" id="txtattachedqnty_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:65px" onKeyUp="validate_attach_qnty(<? echo $table_row; ?>)" value="<? echo $remaining_qnty; ?>" />
                    <input type="hidden" name="hideattachedqnty_<? echo $table_row; ?>" id="hideattachedqnty_<? echo $table_row; ?>" class="text_boxes_numeric" value="<? echo $remaining_qnty; ?>"/>
                </td>
                <td>
                    <input type="text" name="hiddenunitprice_<? echo $table_row; ?>" id="hiddenunitprice_<? echo $table_row; ?>" value="<? echo $row[csf("unit_price")]; ?>" style="width:50px" class="text_boxes_numeric" onKeyUp="calculate_attach_val(<? echo $table_row; ?>)" />
                </td>
                <td>
                    <input type="text" name="txtattachedvalue_<? echo $table_row; ?>" id="txtattachedvalue_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:80px" readonly= "readonly" value="<? echo number_format($remaining_value, 2, '.', ''); ?>" />
                </td>
                <td>
                    <input type="text" name="txtstyleref_<? echo $table_row; ?>" id="txtstyleref_<? echo $table_row; ?>" class="text_boxes" style="width:90px" readonly= "readonly" value="<? echo $row[csf("style_ref_no")]; ?>" />
                </td>
                <td>
                    <input type="text" name="txtitemname_<? echo $table_row; ?>" id="txtitemname_<? echo $table_row; ?>" class="text_boxes" style="width:110px" readonly= "readonly" value="<? echo $row[csf("style_description")]; ?>" />
                </td>
                <td>
                    <input type="text" name="txtjobno_<? echo $table_row; ?>" id="txtjobno_<? echo $table_row; ?>" class="text_boxes" style="width:80px" readonly= "readonly" value="<? echo $row[csf("job_no_mst")]; ?>"  />
                </td>
                <td><input type="text" name="txtfabdescrip_<? echo $table_row; ?>" id="txtfabdescrip_<? echo $table_row; ?>" class="text_boxes" style="width:80px" /></td>
                <td><input type="text" name="txtcategory_<? echo $table_row; ?>" id="txtcategory_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:50px" /></td>
                <td><input type="text" name="txthscode_<? echo $table_row; ?>" id="txthscode_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:40px"/></td>    
                <td> <? echo create_drop_down("cbopostatus_" . $table_row, 80, $attach_detach_array, "", $row[csf("status_active")], "", 1, ""); ?> </td>
            <input type="hidden" name="order_attached_qnty_<? echo $table_row; ?>" id="order_attached_qnty_<? echo $table_row; ?>" value="<? echo $order_attached_qnty; ?>" readonly= "readonly" />
            <input type="hidden" name="order_attached_lc_no_<? echo $table_row; ?>" id="order_attached_lc_no_<? echo $table_row; ?>" value="<? echo $lc_no; ?>" readonly= "readonly" />
            <input type="hidden" name="order_attached_lc_qty_<? echo $table_row; ?>" id="order_attached_lc_qty_<? echo $table_row; ?>" value="<? echo $order_attahed_qnty_lc; ?>" readonly= "readonly" />
            <input type="hidden" name="order_attached_sc_no_<? echo $table_row; ?>" id="order_attached_sc_no_<? echo $table_row; ?>" value="<? echo $sc_no; ?>" readonly= "readonly" />
            <input type="hidden" name="order_attached_sc_qty_<? echo $table_row; ?>" id="order_attached_sc_qty_<? echo $table_row; ?>" value="<? echo $order_attahed_qnty_sc; ?>" readonly= "readonly" />
            </tr>
            <?
        }//end foreach
    }//end if data condition
    exit();
}

//amendment popup
if ($action == "amendment_popup") {
    echo load_html_head_contents("Sales Contract Amendment Form", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    <script>
        function js_set_value(id)
        {
            $('#hidden_amendment_no').val(id);
            parent.emailwindow.hide();
        }
    </script>
    <div align="center" style="width:100%; margin-top:10px">
        <input type="hidden" id="hidden_amendment_no" value="" />
        <?
        $sql = "SELECT id, amendment_no, amendment_date, contract_no, contract_value FROM com_sales_contract_amendment WHERE contract_id='$contract_no' and amendment_no<>0 and status_active=1 and is_deleted=0 and is_original=0 order by amendment_no";

        echo create_list_view("list_view", "Amendment No,Amendment Date,Contract No,Contract Value", "110,100,150,130", "600", "250", 0, $sql, "js_set_value", "id", "", 1, 0, 0, "amendment_no,amendment_date,contract_no,contract_value", "", 'setFilterGrid(\'list_view\',-1)', '0,3,0,2');
        ?>
    </div>
    <?
    exit();
}


if ($action == "get_amendment_data") {
    $data_array = sql_select("SELECT contract_id, amendment_no, amendment_date, amendment_value, value_change_by, last_shipment_date, expiry_date, shipping_mode, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, pay_term, tenor, claim_adjustment, claim_adjust_by, discount_clauses, remarks FROM com_sales_contract_amendment WHERE id='$data' and status_active=1 and is_deleted=0");

    foreach ($data_array as $row) {
        echo "document.getElementById('txt_amendment_no').value 			= '" . $row[csf("amendment_no")] . "';\n";
        echo "document.getElementById('txt_amendment_date').value 			= '" . change_date_format($row[csf("amendment_date")]) . "';\n";
        echo "document.getElementById('txt_amendment_value').value 		= '" . $row[csf("amendment_value")] . "';\n";
        echo "document.getElementById('hide_amendment_value').value 		= '" . $row[csf("amendment_value")] . "';\n";
        echo "document.getElementById('cbo_value_change_by').value 		= '" . $row[csf("value_change_by")] . "';\n";
        echo "document.getElementById('hide_value_change_by').value 		= '" . $row[csf("value_change_by")] . "';\n";
        echo "document.getElementById('txt_claim_adjustment_amnd').value 	= '" . $row[csf("claim_adjustment")] . "';\n";
        echo "document.getElementById('hide_claim_adjustment_amnd').value 	= '" . $row[csf("claim_adjustment")] . "';\n";
        echo "document.getElementById('cbo_claim_adjust_by').value 		= '" . $row[csf("claim_adjust_by")] . "';\n";
        echo "document.getElementById('hide_claim_adjust_by').value 		= '" . $row[csf("claim_adjust_by")] . "';\n";

        $sql = sql_select("SELECT last_shipment_date, expiry_date, shipping_mode, pay_term, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, remarks, tenor, discount_clauses, bl_clause FROM com_sales_contract WHERE id='" . $row[csf('contract_id')] . "'");

        echo "document.getElementById('txt_last_shipment_date_amnd').value	= '" . change_date_format($sql[0][csf("last_shipment_date")]) . "';\n";
        echo "document.getElementById('txt_expiry_date_amend').value 		= '" . change_date_format($sql[0][csf("expiry_date")]) . "';\n";
        echo "document.getElementById('cbo_shipping_mode_amnd').value 		= '" . $sql[0][csf("shipping_mode")] . "';\n";
        echo "document.getElementById('cbo_inco_term').value 				= '" . $sql[0][csf("inco_term")] . "';\n";
        echo "document.getElementById('txt_inco_term_place').value 		= '" . $sql[0][csf("inco_term_place")] . "';\n";
        echo "document.getElementById('txt_port_of_entry_amnd').value 		= '" . $sql[0][csf("port_of_entry")] . "';\n";
        echo "document.getElementById('txt_port_of_loading_amnd').value 	= '" . $sql[0][csf("port_of_loading")] . "';\n";
        echo "document.getElementById('txt_port_of_discharge_amnd').value 	= '" . $sql[0][csf("port_of_discharge")] . "';\n";
        echo "document.getElementById('cbo_pay_term_amnd').value 			= '" . $sql[0][csf("pay_term")] . "';\n";
        echo "document.getElementById('txt_tenor_amnd').value 				= '" . $sql[0][csf("tenor")] . "';\n";
        echo "document.getElementById('txt_discount_clauses_amnd').value 	= '" . $sql[0][csf("discount_clauses")] . "';\n";
        echo "document.getElementById('txt_remarks_amnd').value 			= '" . $sql[0][csf("remarks")] . "';\n";
        echo "document.getElementById('update_id').value 					= '" . $data . "';\n";
        echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_amendment_save',1);\n";
    }
    exit();
}


if ($action == "save_update_delete_amendment") {
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    if ($operation == 0) {  // Insert Here
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        if (is_duplicate_field("amendment_no", "com_sales_contract_amendment", "amendment_no=$txt_amendment_no and contract_id=$txt_system_id") == 1) {
            echo "11**0";
            die;
        }

        if ($db_type == 0) {
            $attached_po_id = return_field_value("group_concat(wo_po_break_down_id)", "com_sales_contract_order_info", "com_sales_contract_id=$txt_system_id and status_active=1 and is_deleted=0");
        } else {
            $attached_po_id = return_field_value("LISTAGG(wo_po_break_down_id, ',') WITHIN GROUP (ORDER BY wo_po_break_down_id) as po_id", "com_sales_contract_order_info", "com_sales_contract_id=$txt_system_id and status_active=1 and is_deleted=0", "po_id");
            /*$attached_po_id = return_field_value("rtrim(xmlagg(xmlelement(e,wo_po_break_down_id,',').extract('//text()') order by wo_po_break_down_id).GetClobVal(),',') AS po_id ", "com_sales_contract_order_info", "com_sales_contract_id=$txt_system_id and status_active=1 and is_deleted=0", "po_id");
            $attached_po_id = $attached_po_id->load();*/
        }

        $user_id = '';
        $entry_date = '';
        $data_array = sql_select("select contract_no, contract_value, last_shipment_date, expiry_date, shipping_mode, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, pay_term, max_btb_limit, foreign_comn, local_comn, tolerance, remarks, tenor, discount_clauses, bl_clause, claim_adjustment, updated_by, inserted_by, update_date, insert_date from com_sales_contract where id=$txt_system_id");

        if ($data_array[0][csf('updated_by')] == 0) {
            $user_id = $data_array[0][csf('inserted_by')];
            $entry_date = $data_array[0][csf('insert_date')];
        } else {
            $user_id = $data_array[0][csf('updated_by')];
            $entry_date = $data_array[0][csf('update_date')];
        }

        $contract_value = $data_array[0][csf('contract_value')];
        $contract_no = $data_array[0][csf('contract_no')];
        $claim_adjustment = $data_array[0][csf('claim_adjustment')];

        if (str_replace("'", '', $cbo_value_change_by) == 1)
            $new_contract_value = $contract_value + str_replace("'", '', $txt_amendment_value);
        else if (str_replace("'", '', $cbo_value_change_by) == 2)
            $new_contract_value = $contract_value - str_replace("'", '', $txt_amendment_value);
        else
            $new_contract_value = $contract_value;

        if (str_replace("'", '', $cbo_claim_adjust_by) == 1)
            $new_claim_adjustment = $claim_adjustment + str_replace("'", '', $txt_claim_adjustment_amnd);
        else if (str_replace("'", '', $cbo_claim_adjust_by) == 2)
            $new_claim_adjustment = $claim_adjustment - str_replace("'", '', $txt_claim_adjustment_amnd);

        $maximum_tolarence = 0;
        $minimum_tolarence = 0;
        $maximum_tolarence = $new_contract_value + ($new_contract_value * str_replace("'", '', $data_array[0][csf('tolerance')])) / 100;
        $minimum_tolarence = $new_contract_value - ($new_contract_value * str_replace("'", '', $data_array[0][csf('tolerance')])) / 100;

        $foreign_comn_value = 0;
        $local_comn_value = 0;
        $foreign_comn_value = ($new_contract_value * str_replace("'", '', $data_array[0][csf('foreign_comn')])) / 100;
        $local_comn_value = ($new_contract_value * str_replace("'", '', $data_array[0][csf('local_comn')])) / 100;

        $max_btb_limit_value = ($new_contract_value * str_replace("'", '', $data_array[0][csf('max_btb_limit')])) / 100;

        //update sales contract table
		//echo "10**select contract_value, initial_contract_value from com_sales_contract where id=$txt_system_id";die;
		$lc_sql=sql_select("select contract_value, initial_contract_value from com_sales_contract where id=$txt_system_id");
		$ini_contract_value=$lc_sql[0][csf("initial_contract_value")];
		$contract_value=$lc_sql[0][csf("contract_value")];
		if($ini_contract_value>0)
		{
			$field_array_update = "contract_value*maximum_tolarence*minimum_tolarence*last_shipment_date*expiry_date*shipping_mode*pay_term*inco_term*inco_term_place*port_of_entry* port_of_loading*port_of_discharge*max_btb_limit_value*foreign_comn_value*local_comn_value*remarks*discount_clauses*bl_clause*tenor*claim_adjustment*updated_by*update_date";

        	$data_array_update = $new_contract_value . "*" . $maximum_tolarence . "*" . $minimum_tolarence . "*" . $txt_last_shipment_date_amnd . "*" . $txt_expiry_date_amend . "*" . $cbo_shipping_mode_amnd . "*" . $cbo_pay_term_amnd . "*" . $cbo_inco_term . "*" . $txt_inco_term_place . "*" . $txt_port_of_entry_amnd . "*" . $txt_port_of_loading_amnd . "*" . $txt_port_of_discharge_amnd . "*" . $max_btb_limit_value . "*" . $foreign_comn_value . "*" . $local_comn_value . "*" . $txt_remarks_amnd . "*" . $txt_discount_clauses_amnd . "*" . $txt_bl_clause_amnd . "*" . $txt_tenor_amnd . "*'" . $new_claim_adjustment . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
		}
		else
		{
			$field_array_update = "contract_value*maximum_tolarence*minimum_tolarence*last_shipment_date*expiry_date*shipping_mode*pay_term*inco_term*inco_term_place*port_of_entry* port_of_loading*port_of_discharge*max_btb_limit_value*foreign_comn_value*local_comn_value*remarks*discount_clauses*bl_clause*tenor*claim_adjustment*updated_by*update_date*initial_contract_value";

        	$data_array_update = $new_contract_value . "*" . $maximum_tolarence . "*" . $minimum_tolarence . "*" . $txt_last_shipment_date_amnd . "*" . $txt_expiry_date_amend . "*" . $cbo_shipping_mode_amnd . "*" . $cbo_pay_term_amnd . "*" . $cbo_inco_term . "*" . $txt_inco_term_place . "*" . $txt_port_of_entry_amnd . "*" . $txt_port_of_loading_amnd . "*" . $txt_port_of_discharge_amnd . "*" . $max_btb_limit_value . "*" . $foreign_comn_value . "*" . $local_comn_value . "*" . $txt_remarks_amnd . "*" . $txt_discount_clauses_amnd . "*" . $txt_bl_clause_amnd . "*" . $txt_tenor_amnd . "*'" . $new_claim_adjustment . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'" . $contract_value . "'";
		}
        

        /* $rID=sql_update("com_sales_contract",$field_array_update,$data_array_update,"id","".$txt_system_id."",1);
          if($rID) $flag=1; else $flag=0; */

        if (is_duplicate_field("amendment_no", "com_sales_contract_amendment", "amendment_no=0 and contract_id=$txt_system_id") == 0) {
            $id = return_next_id("id", "com_sales_contract_amendment", 1);

            $field_array = "id, amendment_no, amendment_date, contract_id, contract_no, contract_value, amendment_value, value_change_by, last_shipment_date, expiry_date, shipping_mode, pay_term, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, remarks, tenor, discount_clauses, bl_clause, claim_adjustment, claim_adjust_by, po_id, is_original, inserted_by, insert_date";

            $amnd_date = "";
            $data_array_amnd = "(" . $id . ",0,'" . $amnd_date . "'," . $txt_system_id . ",'" . $contract_no . "'," . $contract_value . ",0,0,'" . $data_array[0][csf('last_shipment_date')] . "','" . $data_array[0][csf('expiry_date')] . "','" . $data_array[0][csf('shipping_mode')] . "','" . $data_array[0][csf('pay_term')] . "','" . $data_array[0][csf('inco_term')] . "','" . $data_array[0][csf('inco_term_place')] . "','" . $data_array[0][csf('port_of_entry')] . "','" . $data_array[0][csf('port_of_loading')] . "','" . $data_array[0][csf('port_of_discharge')] . "','" . $data_array[0][csf('remarks')] . "','" . $data_array[0][csf('tenor')] . "','" . $data_array[0][csf('discount_clauses')] . "','" . $data_array[0][csf('bl_clause')] . "','" . $data_array[0][csf('claim_adjustment')] . "',0,'" . $attached_po_id . "',1," . $user_id . ",'" . $entry_date . "')";

            /* $rID2=sql_insert("com_sales_contract_amendment",$field_array,$data_array_amnd,0);
              if($flag==1)
              {
              if($rID2) $flag=1; else $flag=0;
              } */

            $id += 1;
        } else {
            $id = return_next_id("id", "com_sales_contract_amendment", 1);
        }

        /* 	$data_array.=",(".$id.",".$txt_amendment_no.",".$txt_amendment_date.",".$txt_system_id.",'".$contract_no."',".$new_contract_value.",".$txt_amendment_value.",".$cbo_value_change_by.",".$txt_last_shipment_date_amnd.",".$txt_expiry_date_amend.",".$cbo_shipping_mode_amnd.",".$cbo_pay_term_amnd.",".$cbo_inco_term.",".$txt_inco_term_place.",".$txt_port_of_entry_amnd.",".$txt_port_of_loading_amnd.",".$txt_port_of_discharge_amnd.",".$txt_remarks_amnd.",".$txt_tenor_amnd.",".$txt_discount_clauses_amnd.",".$txt_bl_clause_amnd.",".$txt_claim_adjustment_amnd.",".$cbo_claim_adjust_by.",'".$attached_po_id."',0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; */

        $shipment_date = strtotime($data_array[0][csf('last_shipment_date')]);
        $shipment_date_amnd = strtotime(str_replace("'", "", $txt_last_shipment_date_amnd));
        $expiry_date = strtotime($data_array[0][csf('expiry_date')]);
        $expiry_date_amnd = strtotime(str_replace("'", "", $txt_expiry_date_amend));

        $field_array_amnd = "id, amendment_no, amendment_date, contract_id, contract_no, contract_value, amendment_value, value_change_by, claim_adjustment, claim_adjust_by";
        $data_array_amnd2 = "(" . $id . "," . $txt_amendment_no . "," . $txt_amendment_date . "," . $txt_system_id . ",'" . $contract_no . "','" . $new_contract_value . "'," . $txt_amendment_value . "," . $cbo_value_change_by . "," . $txt_claim_adjustment_amnd . "," . $cbo_claim_adjust_by;

        if ($shipment_date != $shipment_date_amnd) {
            $field_array_amnd .= ",last_shipment_date";
            $data_array_amnd2 .= "," . $txt_last_shipment_date_amnd;
        }

        if ($expiry_date != $expiry_date_amnd) {
            $field_array_amnd .= ",expiry_date";
            $data_array_amnd2 .= "," . $txt_expiry_date_amend;
        }

        if ($data_array[0][csf('shipping_mode')] != str_replace("'", "", $cbo_shipping_mode_amnd)) {
            $field_array_amnd .= ",shipping_mode";
            $data_array_amnd2 .= "," . $cbo_shipping_mode_amnd;
        }

        if ($data_array[0][csf('pay_term')] != str_replace("'", "", $cbo_pay_term_amnd)) {
            $field_array_amnd .= ",pay_term";
            $data_array_amnd2 .= "," . $cbo_pay_term_amnd;
        }

        if ($data_array[0][csf('inco_term')] != str_replace("'", "", $cbo_inco_term)) {
            $field_array_amnd .= ",inco_term";
            $data_array_amnd2 .= "," . $cbo_inco_term;
        }

        if ($data_array[0][csf('inco_term_place')] != str_replace("'", "", $txt_inco_term_place)) {
            $field_array_amnd .= ",inco_term_place";
            $data_array_amnd2 .= "," . $txt_inco_term_place;
        }

        if ($data_array[0][csf('port_of_entry')] != str_replace("'", "", $txt_port_of_entry_amnd)) {
            $field_array_amnd .= ",port_of_entry";
            $data_array_amnd2 .= "," . $txt_port_of_entry_amnd;
        }

        if ($data_array[0][csf('port_of_loading')] != str_replace("'", "", $txt_port_of_loading_amnd)) {
            $field_array_amnd .= ",port_of_loading";
            $data_array_amnd2 .= "," . $txt_port_of_loading_amnd;
        }

        if ($data_array[0][csf('port_of_discharge')] != str_replace("'", "", $txt_port_of_discharge_amnd)) {
            $field_array_amnd .= ",port_of_discharge";
            $data_array_amnd2 .= "," . $txt_port_of_discharge_amnd;
        }

        if ($data_array[0][csf('remarks')] != str_replace("'", "", $txt_remarks_amnd)) {
            $field_array_amnd .= ",remarks";
            $data_array_amnd2 .= "," . $txt_remarks_amnd;
        }

        if ($data_array[0][csf('tenor')] != str_replace("'", "", $txt_tenor_amnd)) {
            $field_array_amnd .= ",tenor";
            $data_array_amnd2 .= "," . $txt_tenor_amnd;
        }

        if ($data_array[0][csf('discount_clauses')] != str_replace("'", "", $txt_discount_clauses_amnd)) {
            $field_array_amnd .= ",discount_clauses";
            $data_array_amnd2 .= "," . $txt_discount_clauses_amnd;
        }

        if ($data_array[0][csf('bl_clause')] != str_replace("'", "", $txt_bl_clause_amnd)) {
            $field_array_amnd .= ",bl_clause";
            $data_array_amnd2 .= "," . $txt_bl_clause_amnd;
        }

        $field_array_amnd .= ",inserted_by, insert_date";
        $data_array_amnd2 .= "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

        $isFirstamnd = is_duplicate_field("amendment_no", "com_sales_contract_amendment", "amendment_no=0 and contract_id=$txt_system_id");

        $rID = sql_update("com_sales_contract", $field_array_update, $data_array_update, "id", "" . $txt_system_id . "", 1);
        if ($rID)
            $flag = 1;
        else
            $flag = 0;

        if ($isFirstamnd == 0) {
            $rID2 = sql_insert("com_sales_contract_amendment", $field_array, $data_array_amnd, 0);
            if ($flag == 1) {
                if ($rID2)
                    $flag = 1;
                else
                    $flag = 0;
            }
        }

        $rID3 = sql_insert("com_sales_contract_amendment", $field_array_amnd, $data_array_amnd2, 1);
        if ($flag == 1) {
            if ($rID3)
                $flag = 1;
            else
                $flag = 0;
        }

        if ($db_type == 0) {
            if ($flag == 1) {
                mysql_query("COMMIT");
                echo "0**0**" . str_replace("'", '', $txt_system_id);
            } else {
                mysql_query("ROLLBACK");
                echo "5**0**" . str_replace("'", '', $txt_system_id);
            }
        } else if ($db_type == 2 || $db_type == 1) {
            if ($flag == 1) {
                oci_commit($con);
                echo "0**0**" . str_replace("'", '', $txt_system_id);
            } else {
                oci_rollback($con);
                echo "5**0**" . str_replace("'", '', $txt_system_id);
            }
        }
        disconnect($con);
        die;
    } else if ($operation == 1) {   // Update Here
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        $last_amendment_id = return_field_value("max(id)", "com_sales_contract_amendment", "contract_id=$txt_system_id");

        if ($last_amendment_id != str_replace("'", '', $update_id)) {
            echo "14**1";
            die;
        }

        if (is_duplicate_field("id", "com_sales_contract_amendment", "amendment_no=$txt_amendment_no and contract_id=$txt_system_id and id<>$update_id") == 1) {
            echo "11**1";
            die;
        }

        /* $attached_po_id=return_field_value("group_concat(wo_po_break_down_id)","com_sales_contract_order_info","com_sales_contract_id=$txt_system_id and status_active=1 and is_deleted=0 group by com_sales_contract_id"); */

        $data_array = sql_select("select contract_no, contract_value, last_shipment_date, expiry_date, shipping_mode, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, pay_term, max_btb_limit, foreign_comn, local_comn, tolerance, remarks, tenor, discount_clauses, bl_clause, claim_adjustment from com_sales_contract where id=$txt_system_id");

        $contract_value = $data_array[0][csf('contract_value')];
        $contract_no = $data_array[0][csf('contract_no')];
        $claim_adjustment = $data_array[0][csf('claim_adjustment')];

        if (str_replace("'", '', $hide_value_change_by) == 1)
            $contract_value = $contract_value - str_replace("'", '', $hide_amendment_value);
        else if (str_replace("'", '', $hide_value_change_by) == 2)
            $contract_value = $contract_value + str_replace("'", '', $hide_amendment_value);

        if (str_replace("'", '', $cbo_value_change_by) == 1)
            $new_contract_value = $contract_value + str_replace("'", '', $txt_amendment_value);
        else if (str_replace("'", '', $cbo_value_change_by) == 2)
            $new_contract_value = $contract_value - str_replace("'", '', $txt_amendment_value);
        else
            $new_contract_value = $contract_value;

        if (str_replace("'", '', $hide_claim_adjust_by) == 1)
            $claim_adjustment = $claim_adjustment - str_replace("'", '', $hide_claim_adjustment_amnd);
        else if (str_replace("'", '', $hide_claim_adjust_by) == 2)
            $claim_adjustment = $claim_adjustment + str_replace("'", '', $hide_claim_adjustment_amnd);

        if (str_replace("'", '', $cbo_claim_adjust_by) == 1)
            $new_claim_adjustment = $claim_adjustment + str_replace("'", '', $txt_claim_adjustment_amnd);
        else if (str_replace("'", '', $cbo_claim_adjust_by) == 2)
            $new_claim_adjustment = $claim_adjustment - str_replace("'", '', $txt_claim_adjustment_amnd);

        $maximum_tolarence = 0;
        $minimum_tolarence = 0;
        $maximum_tolarence = $new_contract_value + ($new_contract_value * str_replace("'", '', $data_array[0][csf('tolerance')])) / 100;
        $minimum_tolarence = $new_contract_value - ($new_contract_value * str_replace("'", '', $data_array[0][csf('tolerance')])) / 100;

        $foreign_comn_value = 0;
        $local_comn_value = 0;
        $foreign_comn_value = ($new_contract_value * str_replace("'", '', $data_array[0][csf('foreign_comn')])) / 100;
        $local_comn_value = ($new_contract_value * str_replace("'", '', $data_array[0][csf('local_comn')])) / 100;

        $max_btb_limit_value = ($new_contract_value * str_replace("'", '', $data_array[0][csf('max_btb_limit')])) / 100;

        //update sales contract table
        $field_array_update = "contract_value*maximum_tolarence*minimum_tolarence*last_shipment_date*expiry_date*shipping_mode*pay_term*inco_term*inco_term_place*port_of_entry* port_of_loading*port_of_discharge*max_btb_limit_value*foreign_comn_value*local_comn_value*remarks*discount_clauses*bl_clause*tenor*claim_adjustment*updated_by*update_date";

        $data_array_update = $new_contract_value . "*" . $maximum_tolarence . "*" . $minimum_tolarence . "*" . $txt_last_shipment_date_amnd . "*" . $txt_expiry_date_amend . "*" . $cbo_shipping_mode_amnd . "*" . $cbo_pay_term_amnd . "*" . $cbo_inco_term . "*" . $txt_inco_term_place . "*" . $txt_port_of_entry_amnd . "*" . $txt_port_of_loading_amnd . "*" . $txt_port_of_discharge_amnd . "*" . $max_btb_limit_value . "*" . $foreign_comn_value . "*" . $local_comn_value . "*" . $txt_remarks_amnd . "*" . $txt_discount_clauses_amnd . "*" . $txt_bl_clause_amnd . "*" . $txt_tenor_amnd . "*'" . $new_claim_adjustment . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

        /* $rID=sql_update("com_sales_contract",$field_array_update,$data_array_update,"id","".$txt_system_id."",0);
          if($rID) $flag=1; else $flag=0; */

        /* $field_array="amendment_no*amendment_date*contract_id*contract_value*amendment_value*value_change_by*last_shipment_date*expiry_date*shipping_mode*pay_term* inco_term*inco_term_place*port_of_entry*port_of_loading*port_of_discharge*remarks*tenor*discount_clauses*bl_clause*claim_adjustment*claim_adjust_by*po_id*updated_by*update_date";	

          $data_array=$txt_amendment_no."*".$txt_amendment_date."*".$txt_system_id."*".$new_contract_value."*".$txt_amendment_value."*".$cbo_value_change_by."*".$txt_last_shipment_date_amnd."*".$txt_expiry_date_amend."*".$cbo_shipping_mode_amnd."*".$cbo_pay_term_amnd."*".$cbo_inco_term."*".$txt_inco_term_place."*".$txt_port_of_entry_amnd."*".$txt_port_of_loading_amnd."*".$txt_port_of_discharge_amnd."*".$txt_remarks_amnd."*".$txt_tenor_amnd."*".$txt_discount_clauses_amnd."*".$txt_bl_clause_amnd."*".$txt_claim_adjustment_amnd."*".$cbo_claim_adjust_by."*'".$attached_po_id."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; */
        $shipment_date = strtotime($data_array[0][csf('last_shipment_date')]);
        $shipment_date_amnd = strtotime(str_replace("'", "", $txt_last_shipment_date_amnd));
        $expiry_date = strtotime($data_array[0][csf('expiry_date')]);
        $expiry_date_amnd = strtotime(str_replace("'", "", $txt_expiry_date_amend));

        $field_array_amnd = "amendment_date*contract_id*contract_no*contract_value*amendment_value*value_change_by*claim_adjustment*claim_adjust_by*updated_by*update_date";
        $data_array_amnd = $txt_amendment_date . "*" . $txt_system_id . "*'" . $contract_no . "'*'" . $new_contract_value . "'*" . $txt_amendment_value . "*" . $cbo_value_change_by . "*" . $txt_claim_adjustment_amnd . "*" . $cbo_claim_adjust_by . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

        if ($shipment_date != $shipment_date_amnd) {
            $field_array_amnd .= "*last_shipment_date";
            $data_array_amnd .= "*" . $txt_last_shipment_date_amnd;
        }

        if ($expiry_date != $expiry_date_amnd) {
            $field_array_amnd .= "*expiry_date";
            $data_array_amnd .= "*" . $txt_expiry_date_amend;
        }

        if ($data_array[0][csf('shipping_mode')] != str_replace("'", "", $cbo_shipping_mode_amnd)) {
            $field_array_amnd .= "*shipping_mode";
            $data_array_amnd .= "*" . $cbo_shipping_mode_amnd;
        }

        if ($data_array[0][csf('pay_term')] != str_replace("'", "", $cbo_pay_term_amnd)) {
            $field_array_amnd .= "*pay_term";
            $data_array_amnd .= "*" . $cbo_pay_term_amnd;
        }

        if ($data_array[0][csf('inco_term')] != str_replace("'", "", $cbo_inco_term)) {
            $field_array_amnd .= "*inco_term";
            $data_array_amnd .= "*" . $cbo_inco_term;
        }

        if ($data_array[0][csf('inco_term_place')] != str_replace("'", "", $txt_inco_term_place)) {
            $field_array_amnd .= "*inco_term_place";
            $data_array_amnd .= "*" . $txt_inco_term_place;
        }

        if ($data_array[0][csf('port_of_entry')] != str_replace("'", "", $txt_port_of_entry_amnd)) {
            $field_array_amnd .= "*port_of_entry";
            $data_array_amnd .= "*" . $txt_port_of_entry_amnd;
        }

        if ($data_array[0][csf('port_of_loading')] != str_replace("'", "", $txt_port_of_loading_amnd)) {
            $field_array_amnd .= "*port_of_loading";
            $data_array_amnd .= "*" . $txt_port_of_loading_amnd;
        }

        if ($data_array[0][csf('port_of_discharge')] != str_replace("'", "", $txt_port_of_discharge_amnd)) {
            $field_array_amnd .= "*port_of_discharge";
            $data_array_amnd .= "*" . $txt_port_of_discharge_amnd;
        }

        if ($data_array[0][csf('remarks')] != str_replace("'", "", $txt_remarks_amnd)) {
            $field_array_amnd .= "*remarks";
            $data_array_amnd .= "*" . $txt_remarks_amnd;
        }

        if ($data_array[0][csf('tenor')] != str_replace("'", "", $txt_tenor_amnd)) {
            $field_array_amnd .= "*tenor";
            $data_array_amnd .= "*" . $txt_tenor_amnd;
        }

        if ($data_array[0][csf('discount_clauses')] != str_replace("'", "", $txt_discount_clauses_amnd)) {
            $field_array_amnd .= "*discount_clauses";
            $data_array_amnd .= "*" . $txt_discount_clauses_amnd;
        }

        if ($data_array[0][csf('bl_clause')] != str_replace("'", "", $txt_bl_clause_amnd)) {
            $field_array_amnd .= "*bl_clause";
            $data_array_amnd .= "*" . $txt_bl_clause_amnd;
        }

        $rID = sql_update("com_sales_contract", $field_array_update, $data_array_update, "id", "" . $txt_system_id . "", 0);
        if ($rID)
            $flag = 1;
        else
            $flag = 0;

        $rID2 = sql_update("com_sales_contract_amendment", $field_array_amnd, $data_array_amnd, "id", "" . $update_id . "", 1);
        if ($flag == 1) {
            if ($rID2)
                $flag = 1;
            else
                $flag = 0;
        }
        //echo "6**1**".$data_array_amnd;die;
        if ($db_type == 0) {
            if ($flag == 1) {
                mysql_query("COMMIT");
                echo "1**0**" . str_replace("'", '', $txt_system_id);
            } else {
                mysql_query("ROLLBACK");
                echo "6**1**" . str_replace("'", '', $txt_system_id);
            }
        } else if ($db_type == 2 || $db_type == 1) {
            if ($flag == 1) {
                oci_commit($con);
                echo "1**0**" . str_replace("'", '', $txt_system_id);
            } else {
                oci_rollback($con);
                echo "6**1**" . str_replace("'", '', $txt_system_id);
            }
        }
        disconnect($con);
        die;
    }
}

if ($action == "save_update_delete_contract_order_info") {
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    if ($operation == 0) {  // Insert Here
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        /* $last_amendment_id=return_field_value("max(id)","com_sales_contract_amendment","contract_id=$txt_system_id");

          if($last_amendment_id!=str_replace("'", '', $update_id))
          {
          echo "14**1";
          die;
          } */

        $field_array = "id,com_sales_contract_id,wo_po_break_down_id,attached_qnty,attached_rate,attached_value,fabric_description,category_no,hs_code,status_active,inserted_by,insert_date,sc_amendment_id";
        $id = return_next_id("id", "com_sales_contract_order_info", 1);
        for ($j = 1; $j <= $noRow; $j++) {
            $hiddenwopobreakdownid = "hiddenwopobreakdownid_" . $j;
            $txtattachedqnty = "txtattachedqnty_" . $j;
            $hiddenunitprice = "hiddenunitprice_" . $j;
            $txtattachedvalue = "txtattachedvalue_" . $j;
            $cbopostatus = "cbopostatus_" . $j;
            $txtfabdescrip = "txtfabdescrip_" . $j;
            $txtcategory = "txtcategory_" . $j;
            $txthscode = "txthscode_" . $j;

            if ($$hiddenwopobreakdownid != "") {
                if ($data_array != "")
                    $data_array .= ",";

                $data_array .= "(" . $id . "," . $txt_system_id . "," . $$hiddenwopobreakdownid . "," . $$txtattachedqnty . "," . $$hiddenunitprice . "," . $$txtattachedvalue . "," . $$txtfabdescrip . "," . $$txtcategory . "," . $$txthscode . "," . $$cbopostatus . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $update_id . ")";
                $id = $id + 1;
            }
        }
        //print_r($data_array);die;

        $rID = sql_insert("com_sales_contract_order_info", $field_array, $data_array, 1);

        if ($db_type == 0) {
            if ($rID) {
                mysql_query("COMMIT");
                echo "0**" . str_replace("'", '', $txt_system_id) . "**0";
            } else {
                mysql_query("ROLLBACK");
                echo "5**0**0";
            }
        } else if ($db_type == 2 || $db_type == 1) {
            if ($rID) {
                oci_commit($con);
                echo "0**" . str_replace("'", '', $txt_system_id) . "**0";
            } else {
                oci_rollback($con);
                echo "5**0**0";
            }
        }
        disconnect($con);
        die;
    } else if ($operation == 1) {   // Update Here
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        /* $last_amendment_id=return_field_value("max(id)","com_sales_contract_amendment","contract_id=$txt_system_id");

          if($last_amendment_id!=str_replace("'", '', $update_id))
          {
          echo "14**1";
          die;
          } */

        //update code here
        $field_array = "id,com_sales_contract_id,wo_po_break_down_id,attached_qnty,attached_rate,attached_value,fabric_description,category_no,hs_code,status_active,inserted_by,insert_date";
        $field_array_update = "wo_po_break_down_id*attached_qnty*attached_rate*attached_value*fabric_description*category_no*hs_code*status_active*updated_by*update_date";

        $hiddensalescontractorderid = str_replace("'", '', $hiddensalescontractorderid);
        $id = return_next_id("id", "com_sales_contract_order_info", 1);
        for ($j = 1; $j <= $noRow; $j++) {
            $hiddenwopobreakdownid = "hiddenwopobreakdownid_" . $j;
            $txtattachedqnty = "txtattachedqnty_" . $j;
            $hiddenunitprice = "hiddenunitprice_" . $j;
            $txtattachedvalue = "txtattachedvalue_" . $j;
            $cbopostatus = "cbopostatus_" . $j;
            $txtfabdescrip = "txtfabdescrip_" . $j;
            $txtcategory = "txtcategory_" . $j;
            $txthscode = "txthscode_" . $j;

            if ($j == 1) {
                if ($hiddensalescontractorderid != "") {
                    if (str_replace("'", '', $$cbopostatus) == 0) {
                        $invoice_no = "";
                        $po_id = $$hiddenwopobreakdownid;
                        $sql_invoice = "select a.invoice_no from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id and a.lc_sc_id=$txt_system_id and a.is_lc=2 and b.po_breakdown_id=$po_id and b.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.invoice_no";
                        $data = sql_select($sql_invoice);
                        if (count($data) > 0) {
                            foreach ($data as $row) {
                                if ($invoice_no == "")
                                    $invoice_no = $row[csf('invoice_no')];
                                else
                                    $invoice_no .= ",\n" . $row[csf('invoice_no')];
                            }

                            echo "13**" . $invoice_no . "**1";
                            die;
                        }
                    }

                    $data_array_update = "" . $$hiddenwopobreakdownid . "*" . $$txtattachedqnty . "*" . $$hiddenunitprice . "*" . $$txtattachedvalue . "*" . $$txtfabdescrip . "*" . $$txtcategory . "*" . $$txthscode . "*" . $$cbopostatus . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
                }
                else {
                    if ($data_array != "")
                        $data_array .= ",";

                    $data_array .= "(" . $id . "," . $txt_system_id . "," . $$hiddenwopobreakdownid . "," . $$txtattachedqnty . "," . $$hiddenunitprice . "," . $$txtattachedvalue . "," . $$txtfabdescrip . "," . $$txtcategory . "," . $$txthscode . "," . $$cbopostatus . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
                    $id = $id + 1;
                }
            }
            else {
                if ($$hiddenwopobreakdownid != "") {
                    if ($data_array != "")
                        $data_array .= ",";

                    $data_array .= "(" . $id . "," . $txt_system_id . "," . $$hiddenwopobreakdownid . "," . $$txtattachedqnty . "," . $$hiddenunitprice . "," . $$txtattachedvalue . "," . $$txtfabdescrip . "," . $$txtcategory . "," . $$txthscode . "," . $$cbopostatus . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
                    $id = $id + 1;
                }
            }
        }

        //echo "insert into com_sales_contract_order_info (".$field_array.") values".$data_array;die;

        $flag = 1;
        if ($data_array != "") {
            $rID2 = sql_insert("com_sales_contract_order_info", $field_array, $data_array, 0);
            if ($flag == 1) {
                if ($rID2)
                    $flag = 1;
                else
                    $flag = 0;
            }
        }

        if ($data_array_update != "") {
            $rID = sql_update("com_sales_contract_order_info", $field_array_update, $data_array_update, "id", "" . $hiddensalescontractorderid . "", 1);
            if ($flag == 1) {
                if ($rID)
                    $flag = 1;
                else
                    $flag = 0;
            }
        }
        if ($db_type == 0) {
            if ($flag == 1) {
                mysql_query("COMMIT");
                echo "1**" . str_replace("'", '', $txt_system_id) . "**0";
            } else {
                mysql_query("ROLLBACK");
                echo "6**0**1";
            }
        } else if ($db_type == 2 || $db_type == 1) {
            if ($flag == 1) {
                oci_commit($con);
                echo "1**" . str_replace("'", '', $txt_system_id) . "**0";
            } else {
                oci_rollback($con);
                echo "6**0**1";
            }
        }
        disconnect($con);
        die;
    }
}

if($action == "print_amendment_letter"){
	extract($_REQUEST);
	//var_dump ($_REQUEST);
	$cbo_beneficiary_name = str_replace("'","",$cbo_beneficiary_name);
	$txt_amendment_date = str_replace("'","",$txt_amendment_date);
	$txt_internal_file_no = str_replace("'","",$txt_internal_file_no);
	$txt_contract_no = str_replace("'","",$txt_contract_no);
	$txt_lien_date = str_replace("'","",$txt_lien_date);
	$txt_contract_value = str_replace("'","",$txt_contract_value);
	$txt_amendment_no = str_replace("'","",$txt_amendment_no);
	$txt_amendment_value = str_replace("'","",$txt_amendment_value);
	$hide_amendment_value = str_replace("'","",$hide_amendment_value);
	$cbo_value_change_by = str_replace("'","",$hide_value_change_by);
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	$txt_last_shipment_date_amnd = str_replace("'","",$txt_last_shipment_date_amnd);
	$txt_port_of_discharge = str_replace("'","",$txt_port_of_discharge);
	
	$txt_expiry_date_amend = str_replace("'","",$txt_expiry_date_amend);
	$txt_system_id = str_replace("'","",$txt_system_id);
	$update_id = str_replace("'","",$update_id);
	//$hide_value_change_by = str_replace("'","",$hide_value_change_by);
	$cbo_lien_bank = str_replace("'","",$cbo_lien_bank);
	$cbo_currency_name = str_replace("'","",$cbo_currency_name);
    
    
    $buyer_array_res = sql_select("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id order by buy.buyer_name");

    foreach($buyer_array_res as $row){
        $buyer_array[$row[csf('id')]]=$row[csf('buyer_name')];
    }
	$bank_array_res = sql_select("select id, bank_name, branch_name, contact_person, contact_no, email, address, designation  from lib_bank where id = $cbo_lien_bank and issusing_bank=1 and is_deleted=0 and status_active =1");
	foreach ($bank_array_res as $row){
		$bank_array[$row[csf("id")]]["bank_name"] =$row[csf("bank_name")];
		$bank_array[$row[csf("id")]]["branch_name"] =$row[csf("branch_name")];
		$bank_array[$row[csf("id")]]["contact_person"] =$row[csf("contact_person")];
		$bank_array[$row[csf("id")]]["contact_no"] =$row[csf("contact_no")];
		$bank_array[$row[csf("id")]]["email"] =$row[csf("email")];
		$bank_array[$row[csf("id")]]["address"] =$row[csf("address")];
		$bank_array[$row[csf("id")]]["designation"] =$row[csf("designation")];
	}

	$designation_array_res = sql_select("select id, system_designation, custom_designation, custom_designation_local from lib_designation where is_deleted=0 and status_active =1");

	foreach ($designation_array_res as $row){
		$designation_array[$row[csf("id")]]["system_designation"] =$row[csf("system_designation")];
		$designation_array[$row[csf("id")]]["custom_designation"] =$row[csf("custom_designation")];
		$designation_array[$row[csf("id")]]["custom_designation_local"] =$row[csf("custom_designation_local")];
	}
	$company = return_library_array("select id, company_name from lib_company where is_deleted = 0 and status_active = 1", "id", "company_name");

	$sql = "select id, contact_prefix_number, contact_system_id, contract_no,contract_date, beneficiary_name, contract_value, lien_date,buyer_name 
	from com_sales_contract 
	where beneficiary_name = '".$cbo_beneficiary_name."' and is_deleted = 0 and  id=$txt_system_id
	order by id";

	$contact_no_result = sql_select($sql);

	foreach ($contact_no_result as $row) {
		$contract_no_details[$row[csf("id")]]["contact_prefix_number"] = $row[csf("contact_prefix_number")];
		$contract_no_details[$row[csf("id")]]["contact_system_id"] = $row[csf("contact_system_id")];
		$contract_no_details[$row[csf("id")]]["contract_no"] = $row[csf("contract_no")];
		$contract_no_details[$row[csf("id")]]["contract_date"] = $row[csf("contract_date")];
		$contract_no_details[$row[csf("id")]]["contract_value"] = $row[csf("contract_value")];
	}

	//echo $sql;//die;
	//var_dump($company);
	ob_start();
	?>
	<style type="text/css">
			.a4size {
	           width: 21cm;
	           height: 24.7cm;
	           font-family: Cambria, Georgia, serif;
			   font-size: 14px;
			   text-align:left;
			   padding-top:40px;
	        }
			.a4size table tr td, .a4size table tr th{
				font-size:inherit!important;
				}
	        @media print {
	        .a4size{ font-family: Cambria;font-size: 16px;margin: 90px 100PX 54px 25px;
	            }
	        size: A4 portrait;
	        }
	</style>
	<div class="a4size">
	<br/>
		<table width="794" style="text-align:left;">
			<thead>
				<tr>
					<th width="25"></th>
					<th width="650">
						DATE :  <? echo strtoupper($txt_amendment_date); ?><br/>
						INTERNAL REF : <? echo $txt_internal_file_no; ?><br/> 
						SYSTEM REF : <? echo $contract_no_details[$txt_system_id]["contact_system_id"]; ?>
					</th>
					<th width="25"></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="25"></td>
					<td width="650" style="padding-top: 35px;">
					<strong>TO<br/>
						THE <? echo strtoupper($designation_array[$bank_array[$cbo_lien_bank]["designation"]]["custom_designation"]); ?><br/>
						<? echo strtoupper($bank_array[$cbo_lien_bank]["bank_name"]); ?><br/>
						<? echo strtoupper($bank_array[$cbo_lien_bank]["address"]); ?><br/>
					</strong>
					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td style="padding-top: 60px;">
					<strong>SUBJECT:</strong> REQUEST FOR LIEN SALES CONTRACT NO: <strong><? echo $txt_contract_no; ?></strong> <strong>(AMD - <? echo $txt_amendment_no; ?>)</strong> DT: <strong><? echo strtoupper($contract_no_details[$txt_system_id]["contract_date"]); ?></strong> VALUE <? echo strtoupper($increase_decrease[$cbo_value_change_by]);?> BY  <strong><? echo ($cbo_currency_name == 2) ? "$": "TK";  echo $txt_amendment_value; ?></strong> TOTAL VALUE <strong><? echo ($cbo_currency_name == 2) ? "$": "tk";  echo $txt_contract_value; ?></strong> FOR OPENING BTB L/C.
					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td width="650" style="padding-top: 45px;">
						DEAR SIR<br/><br/>

						WE WOULD LIKE TO INFORM YOU THAT WE HAVE RECEIVED A SALES CONTRACT NO: <strong><? echo $txt_contract_no; ?></strong> <strong>(AMD - <? echo $txt_amendment_no; ?>)</strong> DT: <strong><? echo strtoupper($contract_no_details[$txt_system_id]["contract_date"]); ?></strong> VALUE INCREASE/DECREASE <strong><? echo ($cbo_currency_name == 2) ? "$": "TK";  echo $txt_amendment_value; ?></strong> TOTAL VALUE <strong><? echo ($cbo_currency_name == 2) ? "$": "tk";  echo $txt_contract_value; ?></strong> FOR OPENING BTB L/C
                        <table>
                            <tr>
                                <td width="200">NAME OF THE BUYER </td>
                                <td>: <? echo $buyer_array[$row[csf('buyer_name')]];?></td>
                            </tr>
                            <tr>
                                <td width="250"> ADDRESS</td>
                                <td>: AS PER SALES CONTRACT</td>
                            </tr>
                            <tr>
                                <td width="250">SHIPMENT DATE </td>
                                <td>: <? echo $txt_last_shipment_date_amnd;?></td>
                            </tr>
                            <tr>
                                <td width="250">COMMODITY </td>
                                <td>: AS PER SALES CONTRACT </td>
                            </tr>
                            <tr>
                                <td width="250">PORT OF DESTINATION </td>
                                <td>: AS PER SALES CONTRACT <? //echo $txt_port_of_discharge; ?> </td>
                            </tr>
                        </table>
						<p>WE WOULD THEREFORE REQUEST YOU TO OPEN THE BTB L/C AGAINST THIS SALES CONTRACT AT THE EARLIEST.</p>
					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td width="650">
					<p>WE ASSURE YOU AND UNDERTAKE THAT WE WILL BE FULLY RESPONSIBLE FOR ADJUSTMENT OF THE LIABILITIES TO BE CREATED AGAINST THIS SALES CONTRACT.</p>

					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td width="650">
					    <p>PLEASE NOTE THAT WE ARE LIABLE TO REPLACE THE SALES CONTRACT BY EXPORT L/C BEFORE ANY SHIPMENT MADE.</p>
					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td width="650" style="padding-top: 45px;">
					<strong>THANKING YOU,<br/> </strong>  
					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td style="padding-top: 40px;">
					<? echo strtoupper($company[$cbo_beneficiary_name]); ?>	
					</td>
					<td width="25"></td>
				</tr>
			</tbody>
		</table>
	</div>
	<?
	$html = ob_get_contents();
	ob_clean();
	echo $html;
	exit();
}

?>


