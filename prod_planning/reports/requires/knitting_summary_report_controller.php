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
   
    echo create_drop_down("cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 0, "", $selected, "");
    ?>
<!-- <script>set_multiselect('cbo_buyer_name','0','0','','');</script> -->
    <?
    exit();
}

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 110, "select id, season_name from lib_buyer_season where buyer_id in($data) and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

if ($action == "load_drop_down_party_type") {
    $explode_data = explode("**", $data);
    $data = $explode_data[0];
    $selected_company = $explode_data[1];
    //print_r ($selected_company);
    if ($data == 3) {
        echo create_drop_down("cbo_party_type", 120, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$selected_company' and b.party_type =20 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 0, "--- Select ---", $selected, "");
    } else if ($data == 1) {
        echo create_drop_down("cbo_party_type", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 0, "--- Select ---", $selected_company, "", 0, 0);
    } else {
        echo create_drop_down("cbo_party_type", 120, $blank_array, "", 0, "--- Select ---", $selected, "", 1);
    }
    exit();
}


if ($action == "job_no_search_popup") {
    echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>

    <script>

        var selected_id = new Array;
        var selected_name = new Array;

        function check_all_data()
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

            $('#hide_job_id').val(id);
            $('#hide_job_no').val(name);
        }

    </script>

    </head>

    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset style="width:780px;">
                    <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                        <thead>
                        <th>Buyer</th>
                        <th>Search By</th>
                        <th id="search_by_td_up" width="170">Please Enter Job No</th>
                        <th>Shipment Date</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                        <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                        <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">
    <?
    echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", 0, "", 0);
    ?>
                                </td>                 
                                <td align="center">	
                                    <?
                                    $search_by_arr = array(1 => "Job No", 2 => "Style Ref", 3 => "Order No");
                                    $dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
                                    echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
                                    ?>
                                </td>     
                                <td align="center" id="search_by_td">				
                                    <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                                </td> 
                                <td align="center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                                </td>	
                                <td align="center">
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+ <? echo $cbo_year;?>, 'create_job_no_search_list_view', 'search_div', 'knitting_summary_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
$company_library = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
if ($action == "create_job_no_search_list_view") {
    $data = explode('**', $data);
    $company_id = $data[0];
    $cbo_year = $data[6];

    if ($data[1] == 0) {
        if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
            if ($_SESSION['logic_erp']["buyer_id"] != "")
                $buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
            else
                $buyer_id_cond = "";
        }
        else {
            $buyer_id_cond = "";
        }
    } else {
        $buyer_id_cond = " and a.buyer_name=$data[1]";
    }

    $search_by = $data[2];
    $search_string = "%" . trim($data[3]) . "%";

    if ($search_by == 1)
        $search_field = "a.job_no";
    else if ($search_by == 2)
        $search_field = "a.style_ref_no";
    else
        $search_field = "b.po_number";

    $start_date = $data[4];
    $end_date = $data[5];

    if ($start_date != "" && $end_date != "") {
        if ($db_type == 0) {
            $date_cond = "and b.pub_shipment_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
        } else {
            $date_cond = "and b.pub_shipment_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
        }
    } else {
        $date_cond = "";
    }

    $arr = array(0 => $company_library, 1 => $buyer_arr);
    if ($db_type == 0)
    {
    $year_field = "YEAR(a.insert_date) as year";
    $year_cond = " and YEAR(a.insert_date) = $cbo_year ";
    }
    else if ($db_type == 2)
    {
    $year_field = "to_char(a.insert_date,'YYYY') as year";
    $year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
    }
    else
    {$year_field = "";
    $year_cond = "";
    } //defined Later
    
    if ($db_type == 0)
                $select_field = "group_concat(b.po_number) as po_number";
            else if ($db_type == 2)
                $select_field = "listagg(cast(b.po_number as varchar(4000)), ',') within group (order by b.po_number) as po_number";
    if ($db_type == 0)
        $group_year = "YEAR(a.insert_date) ";
    else if ($db_type == 2)
        $group_year = "to_char(a.insert_date,'YYYY')";
                
    $sql = "select a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $select_field, b.pub_shipment_date 
            from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond  $year_cond
            group by a.id, a.job_no, $group_year,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.pub_shipment_date 
            order by a.id, b.pub_shipment_date";
    //echo $sql;	
    echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170", "760", "220", 0, $sql, "js_set_value_job", "id,job_no", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "", '', '0,0,0,0,0,0,3', '',1);
    exit();
}


if ($action == "machine_no_search_popup") {
    echo load_html_head_contents("Machine Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    <script>
        var selected_id = new Array;
        var selected_name = new Array;

        function check_all_data()
        {
            var tbl_row_count = document.getElementById('tbl_machine').rows.length;
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

        function js_set_value(str) {

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

            $('#hide_machine_id').val(id);
            $('#hide_machine').val(name);
        }
    </script>
    <input type="hidden" id="hide_machine" name="hide_machine" >
    <input type="hidden" id="hide_machine_id" name="hide_machine_id" >
    <?
    $sql = "select id,machine_no from lib_machine_name where category_id=1 and company_id = $companyID and status_active=1 and is_deleted=0";
    //echo $sql;
    echo create_list_view("tbl_machine", "Machine No", "200", "240", "250", 0, $sql, "js_set_value", "id,machine_no", "", 1, "0", $arr, "machine_no", "", "setFilterGrid('tbl_machine',-1);", '0', "", 1);
    exit();
}


if ($action == "order_no_search_popup") {
    echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>

    <script>

        var selected_id = new Array;
        var selected_name = new Array;

        function check_all_data()
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

        function js_set_value(str) {

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
            var id = ''; var name = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
                name += selected_name[i] + '*';
            }

            id = id.substr(0, id.length - 1);
            name = name.substr(0, name.length - 1);

            $('#hide_order_id').val(id);
            $('#hide_order_no').val(name);
        }

    </script>

    </head>

    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset style="width:780px;">
                    <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                        <thead>
                        <th>Buyer</th>
                        <th>Search By</th>
                        <th id="search_by_td_up" width="170">Please Enter Order No</th>
                        <th>Shipment Date</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                        <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                        <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">
    <?
    echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", 0, "", 0);
    ?>
                                </td>                 
                                <td align="center">	
    <?
    $search_by_arr = array(1 => "Order No", 2 => "Style Ref", 3 => "Job No");
    $dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
    echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
    ?>
                                </td>     
                                <td align="center" id="search_by_td">				
                                    <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                                </td> 
                                <td align="center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                                </td>	
                                <td align="center">
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value + '**' + '<? echo $job_IDs?>', 'create_order_no_search_list_view', 'search_div', 'knitting_summary_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if ($action == "create_order_no_search_list_view") {
    $data = explode('**', $data);
    $company_id = $data[0];

    if ($data[1] == 0) {
        if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
            if ($_SESSION['logic_erp']["buyer_id"] != "")
                $buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
            else
                $buyer_id_cond = "";
        }
        else {
            $buyer_id_cond = "";
        }
    } else {
        $buyer_id_cond = " and a.buyer_name=$data[1]";
    }

    $search_by = $data[2];
    $search_string = "%" . trim($data[3]) . "%";

    if ($search_by == 1)
        $search_field = "b.po_number";
    else if ($search_by == 2)
        $search_field = "a.style_ref_no";
    else
        $search_field = "a.job_no";

    $start_date = $data[4];
    $end_date = $data[5];
    $pre_selected_job = $data[6];
    if($pre_selected_job != ""){
        $pre_job_cond = " and a.id in ($pre_selected_job)";
    }

    if ($start_date != "" && $end_date != "") {
        if ($db_type == 0) {
            $date_cond = "and b.pub_shipment_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
        } else {
            $date_cond = "and b.pub_shipment_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
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

    $sql = "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $pre_job_cond order by b.id, b.pub_shipment_date";
    //echo $sql;
    echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170", "760", "220", 0, $sql, "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "", '', '0,0,0,0,0,0,3', '', 1);
    exit();
}

$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
$color_type_arr = return_library_array("select id, color_type_id from wo_pre_cost_fabric_cost_dtls", 'id', 'color_type_id');
$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
$machine_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');

//--------------------------------------------------------------------------------------------------------------------

if ($action == "report_generate")
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));
    $txt_machine_id = str_replace("'", "", $txt_machine_id);
    $txt_machine_dia = trim(str_replace("'", "", $txt_machine_dia));
	$txt_booking_no = trim(str_replace("'", "", $txt_booking_no));

    $cbo_knitting_status = str_replace("'", "", $cbo_knitting_status); 
    $based_on = str_replace("'", "", $cbo_based_on);
    $report_type = str_replace("'", "", $report_type);
    $cbo_type = str_replace("'", "", $cbo_type);
    $cbo_party_type = str_replace("'", "", $cbo_party_type);
    $company_name = $cbo_company_name;
    $companyId= str_replace("'", "", $cbo_company_name);
    
		
		if ( $txt_booking_no !=""){$bookingCond = " and a.booking_no like '%$txt_booking_no'";} else {$bookingCond = "";}
		
        if ( $cbo_type != 0){
            $partyTypeCond = " and b.knitting_source = '$cbo_type'";
            if ( $cbo_party_type != 0)
            {    
                $partyTypeCond .= " and b.knitting_party in ( $cbo_party_type ) ";
            }    
        }
        
        if (str_replace("'", "", $cbo_buyer_name) == 0)
		{
                $buyer_id_cond = "";
        }
		else
		{
           $cbo_buyer_name= str_replace("'", "", $cbo_buyer_name);
            $buyer_id_cond = " and c.buyer_id in ($cbo_buyer_name)";
        }

        if (str_replace("'", "", $cbo_season_id) == 0)
		{
                $season_cond = "";
        }
		else
		{
           $cbo_season_id= str_replace("'", "", $cbo_season_id);
           $season_cond = " and a.season_buyer_wise=$cbo_season_id";
        }

        $cbo_year = str_replace("'", "", $cbo_year);
        $year_cond = "";
        if (trim($cbo_year) != 0)
		{
            if ($db_type == 0)
                $year_cond = " and YEAR(a.insert_date)=$cbo_year";
            else if ($db_type == 2)
                $year_cond = " and to_char(a.insert_date,'YYYY')=$cbo_year";
            else
                $year_cond = "";
        }

		//echo "**$txt_job_no";
        $job_cond = "";
        if (str_replace("'", "", trim($txt_job_no)) != "")
		{
            if (str_replace("'", "", $hide_job_id) != "")
			{
                $job_cond = "and a.id in(" . str_replace("'", "", $hide_job_id) . ")";
            }
			else
			{
                $job_number = "%" . trim(str_replace("'", "", $txt_job_no)) . "%";
                $job_cond = "and a.job_no like '$job_number'";
            }
        }
    
        $po_search_cond = "";
        if (str_replace("'", "", trim($txt_order_no)) != "") {
            if (str_replace("'", "", $hide_order_id) != "") {
                $po_search_cond = "and b.id in(" . str_replace("'", "", $hide_order_id) . ")";
            } else {
                $po_number = "%" . trim(str_replace("'", "", $txt_order_no)) . "%";
                $po_search_cond = "and b.po_number like '$po_number'";
            }
        }
        if ($txt_machine_dia != "") 
        {
            $mcDiaCond = " and b.machine_dia = $txt_machine_dia";
        }else
        {
            $mcDiaCond = "";
        }
		
        if ($txt_machine_id != "") 
        {
            $mcNoCond = " and b.machine_id in ($txt_machine_id) ";
        }else
        {
            $mcNoCond = "";
        }
		
        if ($cbo_knitting_status != "") 
        {
            $status_cond = " and b.status in ($cbo_knitting_status )";
        }
		else
		{
            $status_cond= "";
        }


        if ($db_type == 0)
            $year_field = "YEAR(a.insert_date) as year";
        else if ($db_type == 2)
            $year_field = "to_char(a.insert_date,'YYYY') as year";

        $po_array = array();
        $po_id = "";
        $x = 0;
        $costing_sql = sql_select("SELECT a.job_no,$year_field, a.style_ref_no,a.season_buyer_wise, b.id, b.po_number,b.pub_shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_search_cond $job_cond $year_cond $season_cond ");
        foreach ($costing_sql as $row)
		{
            $po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
            $po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
            $po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
            $po_array[$row[csf('id')]]['ship_date'] = $row[csf('pub_shipment_date')];
            $po_array[$row[csf('id')]]['season_buyer_wise'] = $row[csf('season_buyer_wise')];
            $po_array[$row[csf('id')]]['year'] = $row[csf('year')];
            $po_id .= $row[csf('id')] . ",";
        }

        if ( str_replace("'", "", trim($txt_order_no)) != "" || str_replace("'", "", $txt_job_no) != "")
		{
            if ($po_id == "")
			{
                echo "<div align='center'><font style='color:#F00; font-size:18px; font-weight:bold'>No Data Found.</font></div>";
                die;
            }
        }

        $po_id_cond = "";  $po_id_cond2 = "";
       	$po_id_cond = "";   $po_id_cond2 = "";
        if ($po_id != "")
		{
            $po_id = substr($po_id, 0, -1);
           	if ($db_type == 0)
			{
			$po_id_cond = "and c.po_id in(" . $po_id . ")";
			 $po_id_cond2 = "and c.po_breakdown_id in(" . $po_id . ")";
			}
			// $po_id_cond2 = "and c.po_breakdown_id in(" . $po_id . ")";
            else
			{
                $po_ids = explode(",", $po_id);
                if (count($po_ids) > 1000) {
                    $po_id_cond = "and (";
					$po_id_cond2 = "and (";
                    $po_ids = array_chunk($po_ids, 1000);
                    $z = 0;
                    foreach ($po_ids as $id) {
                        $id = implode(",", $id);
                        if ($z == 0)
							{
                            $po_id_cond .= " c.po_id in(" . $id . ")";
							$po_id_cond2 .= " c.po_breakdown_id in(" . $id . ")";
							}
                        else {
                            $po_id_cond .= " or c.po_id in(" . $id . ")";
							$po_id_cond2 .= " or c.po_breakdown_id in(" . $id . ")";
							}
                        $z++;
                    }
                    $po_id_cond .= ")";
					$po_id_cond2 .= ")";
                } else
					{
                   	 $po_id_cond = " and c.po_id in(" . $po_id . ")";
					 $po_id_cond2 = " and c.po_breakdown_id in(" . $po_id . ")";
					}
					
            }
        }

        if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "") {
            if ($based_on == 2) {
                $date_cond = " and b.program_date between " . trim($txt_date_from) . " and " . trim($txt_date_to) . "";
            } else {
                $date_cond = " and b.start_date between " . trim($txt_date_from) . " and " . trim($txt_date_to) . "";
            }
        } else {
            $date_cond = "";
        }

        if (str_replace("'", "", $txt_machine_dia) == "")
            $machine_dia = "%%";
        else
            $machine_dia = "%" . str_replace("'", "", $txt_machine_dia) . "%";

        if (str_replace("'", "", $txt_program_no) == "")
            $program_no = "";
        else
            $program_no = " and b.id =". str_replace("'", "", $txt_program_no) ;

        //comments out date 07.11.2021
        /*$yarn_count_arr_from_determina = array();
        $return_count_arr = sql_select("select a.mst_id , (select b.yarn_count from lib_yarn_count b where b.id = a.count_id and b.status_active = 1 and b.is_deleted = 0 ) as yarn_count from lib_yarn_count_determina_dtls a where a.is_deleted = 0 and a.status_active = 1");
        foreach($return_count_arr as $ycount)
        {
              $yarn_count_arr_from_determina[$ycount[csf("mst_id")]] .= $ycount[csf("yarn_count")] . ",";
        }
        unset( $return_count_arr);*/
        //=====================receive START
        $recv_array=array();
        $sql_recv=sql_select("select a.booking_id,c.po_breakdown_id as po_id,sum(c.quantity) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=22 and c.entry_form=22 and a.receive_basis=9 and b.status_active=1 and b.is_deleted=0 $po_id_cond2 group by a.booking_id,c.po_breakdown_id");
        foreach($sql_recv as $row)
        {
                $recv_array[$row[csf('booking_id')]][$row[csf('po_id')]]=$row[csf('knitting_qnty')];
        }
		  unset( $sql_recv);
        $knitting_recv_qnty_array=array(); $prod_id_arr=array();
        $sql_prod=sql_select("select a.id, a.booking_id,c.po_breakdown_id as po_id, sum(c.quantity) as knitting_qnty, max(b.trans_id) as trans_id from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c  where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=2 and c.entry_form=2 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 $po_id_cond2 group by a.id,c.po_breakdown_id, a.booking_id");
        foreach($sql_prod as $row)
        {
			if($row[csf('trans_id')]>0)
			{
					$knitting_recv_qnty_array[$row[csf('booking_id')]][$row[csf('po_id')]]+=$row[csf('knitting_qnty')];
			}
			else
			{ 
					$prod_id_arr[$row[csf('booking_id')]].=$row[csf('id')].",";
			}
        }
         unset( $sql_prod);
        
         $seasonArr = return_library_array("select id,season_name from lib_buyer_season ","id","season_name");
        $delivery_qty_arr = return_library_array("select c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=58 and c.status_active=1 and c.is_deleted=0 $po_id_cond2 ", "barcode_no", "qnty");

 	 //unset( $delivery_qty_arr);
        $barcode_arr = array();
      //  $barcodeData = sql_select("select mst_id, barcode_no from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and b.entry_form=2 and a.item_category=13 and a.receive_basis=2");
	     $barcodeData = sql_select("select c.mst_id, c.barcode_no from inv_receive_master a, pro_roll_details c where a.id=c.mst_id and a.entry_form=2 and c.entry_form=2 and a.item_category=13 and a.receive_basis=2 $po_id_cond2");
        foreach ($barcodeData as $row) {
            $barcode_arr[$row[csf('mst_id')]] .= $row[csf('barcode_no')] . ",";
        }
        unset( $barcodeData);
        //==========================receive  END
		$yarn_count_dtls = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", "id", "yarn_count");
		$search_by_arr = array(0 => "All",1 => "Inside",3 => "Outside");
        if ($report_type == 1)
		{
            ob_start();
            ?>
            <fieldset style="width:2180px;">
                <table cellpadding="0" cellspacing="0" width="2160">
                    <tr>
                        <td align="center" width="100%" colspan="24" style="font-size:16px"><strong>Knitting Summary Report</strong></td>
                    </tr>
                </table>	
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2160" class="rpt_table" align="left">
                    <thead>
                    <th width="40">SL</th>
                    <th width="80">Buyer</th>
                    <th width="130">Order No</th>
                    <th width="130">Style</th>
                    <th width="130">Season</th>
                    <th width="100">Booking No</th>
                    <th width="70">Program No</th>
                    <th width="80">Start Date</th>
                    <th width="80">T.O.D</th>
                    <th width="100">Fabric Type</th>
                    <th width="100">Yarn Count</th>
                    <th width="100">Yarn Lot</th>
                    <th width="70">M/C Dia</th>
                    <th width="70">F/Dia</th>
                    <th width="70">F/Gsm</th>
                    <th width="70">Factory Name</th>
                    <th width="70">Color</th>                    
                    <th width="80">F.Booking Qty</th>
                    <th width="70">Program Qty</th>
                    <th width="70" style="word-break:break-all">Unprogram Qty</th>                    
                    <th width="70">Yarn Delivery</th>
                    <th width="70">Yarn Del Balance</th>                    
                    <th width="70" style="word-break:break-all">Knitting Production Qty</th>                 
                    <th width="70">Fab Rec. Qty</th>
                    <th width="70">Yarn Return</th>
                    <th>Ttl F/Bal</th>
                    </thead>
                </table>
                <div style="width:2180px; overflow-y:scroll; max-height:330px;" id="scroll_body">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2160" class="rpt_table" id="tbl_list_search" align="left">
                        <tbody>
                    <?
					$sql = "SELECT a.booking_no,c.buyer_id,c.po_id, b.id as program_no,b.start_date, b.end_date,b.color_id,b.knitting_source,b.knitting_party, c.gsm_weight,b.machine_dia,b.fabric_dia,c.fabric_desc,c.determination_id,c.yarn_desc , a.booking_no ,c.program_qnty from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,ppl_planning_entry_plan_dtls c where a.id= b.mst_id and a.id = c.mst_id and b.id= c.dtls_id and a.company_id =$cbo_company_name $po_id_cond $buyer_id_cond $date_cond $program_no $mcDiaCond $mcNoCond $status_cond  $partyTypeCond $bookingCond and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 order by c.buyer_id,b.id,a.booking_no"; 
                    //echo $sql;
                    $result_array = sql_select($sql);
                    $data_array = array();
                    $programNoChk = array();
					foreach($result_array as $gRow)
					{
						$data_array[$gRow[csf('buyer_id')]][$gRow[csf('booking_no')]][$gRow[csf('program_no')]]['buyer_id'] = $gRow[csf('buyer_id')];
						$data_array[$gRow[csf('buyer_id')]][$gRow[csf('booking_no')]][$gRow[csf('program_no')]]['order'] .= $po_array[$gRow[csf('po_id')]]['no'].",";
						$data_array[$gRow[csf('buyer_id')]][$gRow[csf('booking_no')]][$gRow[csf('program_no')]]['po_id'] .= $gRow[csf('po_id')].",";
						$data_array[$gRow[csf('buyer_id')]][$gRow[csf('booking_no')]][$gRow[csf('program_no')]]['style'] = $po_array[$gRow[csf("po_id")]]['style_ref'];
						$data_array[$gRow[csf('buyer_id')]][$gRow[csf('booking_no')]][$gRow[csf('program_no')]]['start_date'] = $gRow[csf('start_date')];
						$data_array[$gRow[csf('buyer_id')]][$gRow[csf('booking_no')]][$gRow[csf('program_no')]]['end_date'] = $gRow[csf('end_date')];
						$data_array[$gRow[csf('buyer_id')]][$gRow[csf('booking_no')]][$gRow[csf('program_no')]]['color_id'] = $color_library[$gRow[csf('color_id')]];
						$data_array[$gRow[csf('buyer_id')]][$gRow[csf('booking_no')]][$gRow[csf('program_no')]]['fabric_desc'] = $gRow[csf('fabric_desc')];
						$data_array[$gRow[csf('buyer_id')]][$gRow[csf('booking_no')]][$gRow[csf('program_no')]]['determination_id'] = $gRow[csf('determination_id')];
						$data_array[$gRow[csf('buyer_id')]][$gRow[csf('booking_no')]][$gRow[csf('program_no')]]['machine_dia'] = $gRow[csf('machine_dia')];
						$data_array[$gRow[csf('buyer_id')]][$gRow[csf('booking_no')]][$gRow[csf('program_no')]]['fabric_dia'] = $gRow[csf('fabric_dia')];
						$data_array[$gRow[csf('buyer_id')]][$gRow[csf('booking_no')]][$gRow[csf('program_no')]]['gsm_weight'] = $gRow[csf('gsm_weight')];
						$data_array[$gRow[csf('buyer_id')]][$gRow[csf('booking_no')]][$gRow[csf('program_no')]]['knitting_source'] = $gRow[csf('knitting_source')];
						$data_array[$gRow[csf('buyer_id')]][$gRow[csf('booking_no')]][$gRow[csf('program_no')]]['knitting_party'] = $gRow[csf('knitting_party')];
                        $data_array[$gRow[csf('buyer_id')]][$gRow[csf('booking_no')]][$gRow[csf('program_no')]]['season'] = $po_array[$gRow[csf("po_id")]]['season_buyer_wise'];

                        if($programNoChk[$gRow[csf('program_no')]] == "")
                        {
                            $programNoChk[$gRow[csf('program_no')]] = $gRow[csf('program_no')];
                            $data_array[$gRow[csf('buyer_id')]][$gRow[csf('booking_no')]][$gRow[csf('program_no')]]['program_qnty'] += $gRow[csf('program_qnty')];
                        }

						$PoIdArr[$gRow[csf('po_id')]]=$gRow[csf('po_id')];
						$program_noArr[$gRow[csf('program_no')]]=$gRow[csf('program_no')];
					}
					
					$booking_row_span_arr=array();
					$booking_row_span_arr=array();
					foreach($data_array as $buyer_id=> $buyer_data)
					{
						$buyer_row_span=0;
						foreach($buyer_data as $booking_id => $booking_data)
						{
							$booking_row_span =0;
							foreach($booking_data as $program_id=> $row)
							{
								
								
								$booking_row_span++;
								$buyer_row_span++;
							}
							
							$buyer_row_span_arr[$buyer_id]=$buyer_row_span;
							$booking_row_span_arr[$buyer_id][$booking_id]=$booking_row_span;
						}
						
					}
					//print_r($booking_row_span_arr);
					//echo $booking_row_span_arr['D n C-Fb-17-00038'];
					//------------	
					
					$program_noArr_nos="'".implode("','",$program_noArr)."'";
					$program_nos_arr = explode(",", $program_noArr_nos);

					$all_prog_nos_cond="";
					$progCond="";
					$prog_cond = '';
					if($db_type==2 && count($program_nos_arr)>999)
					{
						$all_search_prog_arr_chunk=array_chunk($program_nos_arr,999) ;
						foreach($all_search_prog_arr_chunk as $chunk_arr)
						{
							$chunk_arr_value=implode(",",$chunk_arr);
							$progCond.=" booking_id in($chunk_arr_value) or ";
							$prog_cond.=" c.knit_id in($chunk_arr_value) or ";
						}

						$all_prog_nos_cond.=" and (".chop($progCond,'or ').")";
						$prog_cond_str = " and (".chop($prog_cond,'or ').")";
					}
					else
					{
						$all_prog_nos_cond = " and booking_id in($program_noArr_nos)";
						$prog_cond_str = " and c.knit_id in($program_noArr_nos)";
					}
					
					//for issue
					$knit_issue_arr=array();
					$sql_data = sql_select("select b.id as trans_id, c.knit_id as program_no, b.cons_quantity, a.id as issue_id, d.lot, d.yarn_count_id from inv_issue_master a, inv_transaction b, ppl_yarn_requisition_entry c, product_details_master d where a.id = b.mst_id and b.requisition_no = c.requisition_no and b.prod_id = d.id and c.prod_id = d.id and b.receive_basis = 3 and b.transaction_type = 2 and a.issue_basis = 3 and a.entry_form = 3 and a.company_id = $company_name and b.item_category = 1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0".$prog_cond_str);
					$transId_chk = array();
					foreach($sql_data as $row)
					{
						if($transId_chk[$row[csf('trans_id')]] == "")
						{
							$transId_chk[$row[csf('trans_id')]] = $row[csf('trans_id')];
							$knit_issue_arr[$row[csf('program_no')]]['qnty'] += $row[csf('cons_quantity')];
							$knit_issue_arr[$row[csf('program_no')]]['lot'][$row[csf('lot')]] = $row[csf('lot')];
							$knit_issue_arr[$row[csf('program_no')]]['issue_id'] .= $row[csf('issue_id')].",";
							$knit_issue_arr[$row[csf('program_no')]]['trans_id'] .= $row[csf('trans_id')].",";
							$knit_issue_arr[$row[csf('program_no')]]['yarn_count'][$row[csf('yarn_count_id')]] = $yarn_count_dtls[$row[csf('yarn_count_id')]];
						}
					}
					unset( $sql_data);
					//end for issue
					
					//for issue return
					$knit_issue_return =array();
					$sql_iss_return = sql_select("select b.id as trans_id, a.id, b.cons_quantity as iss_return_qty, a.booking_id, c.knit_id from inv_receive_master a, inv_transaction b , ppl_yarn_requisition_entry c where a.id = b.mst_id and a.booking_id = c.requisition_no and a.item_category = 1 and b.transaction_type = 4 and a.entry_form = 9 and b.company_id = $cbo_company_name and b.status_active = 1 and b.is_deleted = 0 and a.is_deleted = 0 and a.status_active = 1 and a.receive_basis = 3".$prog_cond_str." order by b.id");
					$transId_chk = array();
					foreach($sql_iss_return as $row)
					{
						if($transId_chk[$row[csf('trans_id')]] == "")
						{
							$transId_chk[$row[csf('trans_id')]] = $row[csf('trans_id')];
							$knit_issue_return_arr[$row[csf('knit_id')]]['ret_qnty'] += $row[csf('iss_return_qty')];
							$knit_issue_return_arr[$row[csf('knit_id')]]['ids'] .= $row[csf('id')].",";
						}
					}
					unset( $sql_iss_return);
					//end for issue return

					//for grey receive qty
					$sql= "select booking_id,sum(b.grey_receive_qnty) as grey_receive_qnty from inv_receive_master a,  pro_grey_prod_entry_dtls b where a.id=b.mst_id $all_prog_nos_cond and a.receive_basis=2 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by booking_id";							
					$nameArray=sql_select($sql);							 
					foreach($nameArray as $rows3)
					{
						$knitProQtyArr[$rows3[csf('booking_id')]]+=$rows3[csf('grey_receive_qnty')];	
					}
					
					//for grey fabric qty
					$sql="select a.booking_no,a.grey_fab_qnty FROM wo_booking_dtls a WHERE a.po_break_down_id in (".implode(',',$PoIdArr).") and a.status_active=1 and a.is_deleted=0";
					$nameArray=sql_select($sql);							 
					foreach($nameArray as $rows2)
					{
						$fbookingQty[$rows2[csf('booking_no')]]+=$rows2[csf('grey_fab_qnty')];	
					}
												 
					//---------------------------------							 
					$i=$m=1;
					foreach($data_array as $buyer_id=> $buyer_data)
					{
					   // $y=1;
						foreach($buyer_data as $booking_id=> $booking_data)
						{
							$y=1;
							foreach($booking_data as $program_id=> $row)
							{
							if ($m % 2 == 0)
							$bgcolor = "#E9F3FF";
							else
							$bgcolor = "#FFFFFF";
							$knitting_recv_qnty = $knit_issue_return_qnty = 0;
							$po_id_arr = explode(",",$row['po_id']);
							foreach($po_id_arr as $po_id){
								$knitting_recv_qnty += $knitting_recv_qnty_array[$program_id][$po_id];
							}
						//$knitting_recv_qnty=$knitting_recv_qnty_array[$program_id][$row[csf('po_id')]];
						$knit_issue_return_qnty= $knit_issue_return_arr[$program_id]['ret_qnty'];                                
						$knit_id=array_unique(explode(",",substr($prod_id_arr[$program_id],0,-1)));
						foreach($knit_id as $val)
						{
							//$barcode_arr[$val];
							$delivery_qty = 0;
							$barcode_nos = explode(",", chop($barcode_arr[$val], ','));
							foreach ($barcode_nos as $barcode_no) {
								$delivery_qty += $delivery_qty_arr[$barcode_no];
							}
							
							$knitting_recv_qnty += $delivery_qty;
							foreach($po_id_arr as $po_id){
								if($recv_array[$val][$po_id]>0)
								{
									if($chk_prog_arr[$val]=='')
									{
								$knitting_recv_qnty+=$recv_array[$val][$po_id];
									}
								}
							}
						}
						$buyer_td_span=$buyer_row_span_arr[$buyer_id];	
						$booking_td_span=$booking_row_span_arr[$buyer_id][$booking_id];
						//echo $buyer_id.'='.$program_id;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>"> 
						<?
						if($y==1)
						{
						?>
						<td width="40" style="word-break:break-all" rowspan="<? echo $booking_td_span;?>"><? echo $i;?></td>
						<td width="80" style="word-break:break-all" rowspan="<? echo $booking_td_span;?>"><? echo $buyer_arr[$row["buyer_id"]];?></td>
						<td width="130" style="word-break:break-all" rowspan="<? echo $booking_td_span;?>"><p><? echo chop($row['order'],",");?></p></td>
						<td width="130" style="word-break:break-all" rowspan="<? echo $booking_td_span;?>"><p><? echo $row['style'];?></p></td>
                        <td width="130" style="word-break:break-all" rowspan="<? echo $booking_td_span;?>"><p><? echo $seasonArr[$row['season']];?></p></td>
						<td width="100" style="word-break:break-all" rowspan="<? echo $booking_td_span;?>"><p><? echo $booking_id;?></p></td>
						<?
						}
						?>
						 <td width="70" style="word-break:break-all"><p><a href="#" onClick="generate_report(<? echo $company_name ?>,'<? echo $program_id;?>')"><? echo $program_id;?></a></p></td>
						<td width="80" style="word-break:break-all"><p><? if ($row[csf('start_date')] != "0000-00-00") echo change_date_format($row['start_date']);?></p></td>
						<td width="80" style="word-break:break-all"><p><? if ($row[csf('end_date')] != "0000-00-00") echo change_date_format($row['end_date']);?></p></td>
						<td width="100" style="word-break:break-all">
							<p>
								<? 
								$fabric_desc_arr= explode(",",$row["fabric_desc"]);
								echo $fabric_desc_arr[0];
								?>
							</p>
						</td>
						<td width="100" style="word-break:break-all">
							<? 
						    //$yarn_count = $yarn_count_arr_from_determina[$row["determination_id"]];
							//echo chop($yarn_count, ",");
							//echo implode(",",array_unique(explode(",", chop($yarn_count, ','))));
							echo implode(', ', $knit_issue_arr[$program_id]['yarn_count']);
							?>
						</td>
						<td width="100" style="word-break:break-all"><? echo implode(", ",$knit_issue_arr[$program_id]['lot']);?></td>
						<td width="70" style="word-break:break-all"><? echo $row["machine_dia"];?></td>
						<td width="70" style="word-break:break-all">&nbsp;<? echo $row["fabric_dia"];?></td>
						<td width="70" style="word-break:break-all"><? echo $row["gsm_weight"];?></td>
						<td width="70" style="word-break:break-all">
							<p>
							<? 
							if($row["knitting_source"] == 1 ){
								echo $company_library[$row["knitting_party"]];
							}else{
								echo $supplier_details[$row["knitting_party"]];
							}
							?>
							</p>
						</td>
						<td width="70" style="word-break:break-all"><p><? echo $row["color_id"];?></p></td>
						<?
						if($y==1)
						{
						?>
						<td width="80" style="word-break:break-all" align="right" rowspan="<? echo $booking_td_span;?>">
						<? 
						$fbooking_qty=$fbookingQty[$booking_id];							
						echo number_format($fbooking_qty,2);
						$sub_booking_qty+=$fbooking_qty;
						$buyer_sub_booking_qty +=$fbooking_qty;
						$grand_total_sub_booking_qty +=$fbooking_qty;
						?>
						</td>
						<? } ?>
						<td width="70" style="word-break:break-all" align="right"><?  echo number_format($row["program_qnty"],2);?></td>
						<?
						if($y==1)
						{
						?>
						<td width="70" style="word-break:break-all" align="right" rowspan="<? echo $booking_td_span;?>"><? 
							$booking_program_qnty=0;
							foreach($booking_data as $rows9)
							{
								$booking_program_qnty+=$rows9["program_qnty"];	
							}
							$UnProgramQty=$fbooking_qty-$booking_program_qnty;
							echo number_format($UnProgramQty,2);
							$sub_UnProgramQty+=$UnProgramQty;
							$buyer_sub_UnProgramQty+=$UnProgramQty;
							$grand_total_UnProgramQty+=$UnProgramQty;
						
						?></td>
						<? } ?>
						<td width="70" style="word-break:break-all" align="right"><p><a href="javascript:openmypage('<? echo chop($knit_issue_arr[$program_id]['issue_id'],',')?>**<? echo $knit_issue_arr[$program_id]['trans_id'];?>','issue_popup')"><? echo number_format($knit_issue_arr[$program_id]["qnty"],2);?></a></p></td>
						<td width="70" style="word-break:break-all" align="right"><p><? echo number_format( $row["program_qnty"] - $knit_issue_arr[$program_id]["qnty"],2);?></p></td>
						<td width="70" style="word-break:break-all" align="right"><p><a href="javascript:openmypage('<? echo $program_id;?>','knitting_prod_popup')"><? echo $knitProQtyArr[$program_id]; ?></a></p></td>
						<td width="70" style="word-break:break-all" align="right"><p><a href="#" onClick="openmypage('<? echo $program_id?>**<? echo chop($row['po_id'],',')?>**<? echo $companyId;?>**<? echo $row["knitting_source"];?>','receive_popup')"><? echo  number_format($knitting_recv_qnty,2);?></a></p></td>
						<td width="70" style="word-break:break-all" align="right"><p><a href="#" onClick="openmypage('<? echo chop($knit_issue_return_arr[$program_id]['ids'],',');?>','issue_return_popup')"><? echo  number_format($knit_issue_return_qnty,2)?></a></p></td>
						<td style="word-break:break-all" align="right"><p><? echo  number_format($row["program_qnty"]-$knitting_recv_qnty,2);?></p></td>
					</tr>
						<?
                         $m++;$y++;
                         $sub_programqnty+=$row["program_qnty"];      
                         $sub_knit_issueqnty+=$knit_issue_arr[$program_id]["qnty"];
                         $sub_delivery_balance+= $row["program_qnty"] - $knit_issue_arr[$program_id]["qnty"];
                         $sub_knitting_recv_qnty += $knitting_recv_qnty;
                         $sub_knit_issue_return_qnty+= $knit_issue_return_qnty;
                         $sub_total_f_value +=$row["program_qnty"]-$knitting_recv_qnty;
                         //$sub_booking_qty+=$fbooking_qty;
                         $sub_knit_pro_qty+=$knitProQtyArr[$program_id];
                         //$sub_UnProgramQty+=$UnProgramQty;
                                                             
                         $buyer_sub_programqnty +=  $row["program_qnty"];      
                         $buyer_sub_knit_issueqnty+= $knit_issue_arr[$program_id]["qnty"];
                         $buyer_sub_delivery_balance+= $row["program_qnty"] - $knit_issue_arr[$program_id]["qnty"];
                         $buyer_sub_knitting_recv_qnty += $knitting_recv_qnty;
                         $buyer_sub_knit_issue_return_qnty+= $knit_issue_return_qnty;
                         $buyer_sub_total_f_value +=$row["program_qnty"]-$knitting_recv_qnty;
                         //$buyer_sub_booking_qty +=$fbooking_qty;
                         $buyer_sub_knit_pro_qty+=$knitProQtyArr[$program_id];
                         // $buyer_sub_UnProgramQty+=$UnProgramQty;
                         
                         $grand_programqnty +=  $row["program_qnty"];      
                         $grand_knit_issueqnty+= $knit_issue_arr[$program_id]["qnty"];
                         $grand_delivery_balance+= $row["program_qnty"] - $knit_issue_arr[$program_id]["qnty"];
                         $grand_knitting_recv_qnty += $knitting_recv_qnty;
                         $grand_knit_issue_return_qnty+= $knit_issue_return_qnty;
                         $grand_total_f_value +=$row["program_qnty"]-$knitting_recv_qnty;
                         // $grand_total_sub_booking_qty +=$fbooking_qty;
                         $grand_total_knit_pro_qty+=$knitProQtyArr[$program_id];
                         //$grand_total_UnProgramQty+=$UnProgramQty;
                        }
                        ?>
						<tr style="background-color: #EFEFEF;font-weight: bold;">
							<td colspan="15">&nbsp;</td>
							<td colspan="2" style="font-weight: bold; text-align: right;"> Booking SubTotal</td>
							<td align="right" style="word-break:break-all"><p><? echo  number_format($sub_booking_qty,2);?></p></td>
							<td align="right" style="word-break:break-all"><p><? echo  number_format($sub_programqnty,2);?></p></td>
							<td align="right" style="word-break:break-all"><p><? echo  number_format($sub_UnProgramQty,2);?></p></td>
							<td align="right" style="word-break:break-all"><p><? echo  number_format($sub_knit_issueqnty,2);?></p></td>
							<td align="right" style="word-break:break-all"><p><? echo  number_format($sub_delivery_balance,2)?></p></td>
							<td align="right" style="word-break:break-all"><p><? echo  number_format($sub_knit_pro_qty,2)?></p></td>
							<td align="right" style="word-break:break-all"><p><? echo  number_format($sub_knitting_recv_qnty,2)?></p></td>
							<td align="right" style="word-break:break-all"><p><? echo  number_format($sub_knit_issue_return_qnty,2)?></p></td>
							<td align="right" style="word-break:break-all"><p><? echo  number_format($sub_total_f_value,2)?></p></td>
						</tr>
						<?
						$sub_programqnty =$sub_knit_issueqnty =$sub_delivery_balance=$sub_knitting_recv_qnty =$sub_knit_issue_return_qnty=$sub_total_f_value =$sub_booking_qty=$sub_knit_pro_qty=$sub_UnProgramQty= 0;
						}
						?>
						<tr style="background-color: #EFEFEF;font-weight: bold;">
							<td  colspan="15">&nbsp;</td>
							<td colspan="2" style="font-weight: bold; text-align: right;"> Buyer SubTotal</td>
							<td align="right" style="word-break:break-all"><p><? echo  number_format($buyer_sub_booking_qty,2);?></p></td>
							<td align="right" style="word-break:break-all"><p><? echo  number_format($buyer_sub_programqnty,2);?></p></td>
							<td align="right" style="word-break:break-all"><p><? echo  number_format($buyer_sub_UnProgramQty,2);?></p></td>
							<td align="right" style="word-break:break-all"><p><? echo  number_format($buyer_sub_knit_issueqnty,2);?></p></td>
							<td align="right" style="word-break:break-all"><p><? echo  number_format($buyer_sub_delivery_balance,2)?></p></td>
							<td align="right" style="word-break:break-all"><p><?  echo  number_format($buyer_sub_delivery_balance,2)?></p></td>
							<td align="right" style="word-break:break-all"><p><? echo  number_format($buyer_sub_knitting_recv_qnty,2)?></p></td>
							<td align="right" style="word-break:break-all"><p><? echo  number_format($buyer_sub_knit_issue_return_qnty,2)?></p></td>
							<td align="right" style="word-break:break-all"><p><? echo  number_format($buyer_sub_total_f_value,2)?></p></td>
						</tr>
						<?
						$buyer_sub_programqnty =$buyer_sub_knit_issueqnty =$buyer_sub_delivery_balance=$buyer_sub_knitting_recv_qnty =$buyer_sub_knit_issue_return_qnty=$buyer_sub_total_f_value =$buyer_sub_booking_qty= $buyer_sub_delivery_balance=$buyer_sub_delivery_balance=0;
						$i++;
					}
					//die;
				   ?>
				</tbody>
				<tr style="background-color: #EFEFEF;font-weight: bold;">
					<td colspan="15">&nbsp;</td>
					<td colspan="2" style="font-weight: bold; text-align: right;">Grand Total</td>
					<td align="right" style="word-break:break-all"><p><? echo  number_format($grand_total_sub_booking_qty,2);?></p></td>
					<td align="right" style="word-break:break-all"><p><? echo  number_format($grand_programqnty,2);?></p></td>
					<td align="right" style="word-break:break-all"><p><? echo  number_format($grand_total_UnProgramQty,2);?></p></td>
					<td align="right" style="word-break:break-all"><p><? echo  number_format($grand_knit_issueqnty,2);?></p></td>
					<td align="right" style="word-break:break-all"><p><? echo  number_format($grand_delivery_balance,2)?></p></td>
					<td align="right" style="word-break:break-all"><p><? echo  number_format($grand_total_knit_pro_qty,2)?></p></td>
					<td align="right" style="word-break:break-all"><p><? echo  number_format($grand_knitting_recv_qnty,2)?></p></td>
					<td align="right" style="word-break:break-all"><p><? echo  number_format($grand_knit_issue_return_qnty,2)?></p></td>
					<td align="right" style="word-break:break-all"><p><? echo  number_format($grand_total_f_value,2)?></p></td>
				 </tr>
			   
			</table>
		</div>
		<br />
		<? 
			$po_cond = "";
			if (str_replace("'", "", trim($txt_order_no)) != "") {
				if (str_replace("'", "", $hide_order_id) != "") {
				$po_cond = " and b.po_break_down_id in(" . str_replace("'", "", $hide_order_id) . ")";
				} 
			}
			//echo $txt_job_no;die;
			$job_cond = "";
			//if (str_replace("*", "','", trim($txt_job_no)) != "") {
				if (str_replace("'", "", $txt_job_no) != "") {
					$job_cond = " and a.job_no in(" . str_replace("*", "','", $txt_job_no) . ")";
				} 
			//}
			
			if($po_cond != "" || $job_cond != "")
			{
                ?>
                <div>
                    <table  cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table" id="tbl_list_search">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Order No.</th>
                                <th>Booking Qnty</th>
                                <th>Program Qnty</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?    
                        
                            $program_qnty_arr = return_library_array("select po_id,
                                sum(program_qnty) as program_qnty
                                from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 and is_sales!=1
                                group by  po_id", "po_id", "program_qnty");

                                $result = sql_select("select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty
                                from wo_booking_mst a, wo_booking_dtls b
                                where a.booking_no=b.booking_no and a.company_id =$cbo_company_name and a.item_category=2 and a.fabric_source=1 
                                and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 
                                $po_cond $job_cond
                                group by b.po_break_down_id, a.job_no
                                order by b.po_break_down_id");
                                $i = 1;
                                foreach($result as $row)
                                {
                                    $balance = $row[csf("qnty")] - $program_qnty_arr[$row[csf("po_break_down_id")]];
                        ?>    
                                <tr>
                                    <td><? echo $i?></td>
                                    <td>&nbsp;<? echo $po_array[$row[csf("po_break_down_id")]]['no']?></td>
                                    <td align="right"><? echo number_format($row[csf("qnty")],2); ?></td>
                                    <td align="right"><? echo number_format($program_qnty_arr[$row[csf("po_break_down_id")]],2); ?></td>
                                    <td align="right"><? echo number_format($balance,2);?></td>
                                </tr>
                                <? }?>
                        </tbody>
                    </table>
                    </div>
                    <? } ?>
            </fieldset>      
            <?            
        } 

    	foreach (glob("$user_name*.xls") as $filename) {
        if (@filemtime($filename) < (time() - $seconds_old))
            @unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = $user_name . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, ob_get_contents());
    $filename = "requires/" . $user_name . "_" . $name . ".xls";
    echo "$total_data####$filename";
    exit();
}

if($action == 'issue_popup')
{
    echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $data = explode("**", $ids);
    $iss_ids = $data[0];
    $iss_ids = implode(",",array_unique(explode(",",$iss_ids)));
    $trans_ids = $data[1];
    $trans_ids = implode(",",array_unique(explode(",",chop($trans_ids,","))));
	?>
	<fieldset style="width:370px; margin-left:2px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="4"><b>Yarn issue</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="100">Issue Date</th>
                    <th width="100">Issue No</th>
                    <th width="">Qnty</th>
				</thead>
             </table>
             <div style="width:370px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0">
                    <?	
                   $sql="select a.issue_number, a.issue_date, sum(b.cons_quantity) as qnty from inv_issue_master a, inv_transaction b  where a.id = b.mst_id and a.id in ($iss_ids) and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and a.entry_form = 3 and a.issue_basis = 3 and a.item_category =1 and b.transaction_type = 2 and b.id in ($trans_ids) group by a.issue_number, a.issue_date";
                    $result=sql_select($sql);
                    $i = 1;
                    foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_issue_qnty+=$row[csf('qnty')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                          <td width="30" align="center"><? echo $i; ?></td>
                          <td width="100" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                          <td width="100" align="center"><? echo $row[csf('issue_number')]; ?></td>
                          <td width="" align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tr>
                        <th colspan="3">Total =</th>
                        <th align="right"><? echo number_format($total_issue_qnty,2); ?></th>
                    </tr>
                </table>
            </div>	
        </div>
	</fieldset>
	<?
	exit();
}

if($action == 'knitting_prod_popup')
{
    echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $data = explode("**", $ids);
    $program_id = $data[0];
    $supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
    
    ?>
    <fieldset style="width:770px; margin-left:2px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
                <thead>
                    <th colspan="9"><b>Knitting Production</b></th>
                </thead>
                <thead>
                    <th width="30">SL</th>
                    <th width="60">Date</th>
                    <th width="120">Production Id</th>
                    <th width="80">Machine No</th>
                    <th width="100">Order No</th>
                    <th width="100">Barcode No</th>
                    <th width="140">Kniting Com</th>
                    <th width="60">Inhouse Production</th>
                    <th width="60">Outside Production</th>
                </thead>
             </table>
             <div style="width:770px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
                    <tbody>
                    <?  
                  //  $sql= "SELECT knitting_source,knitting_company,receive_date,recv_number,b.machine_no_id,sum(c.qc_pass_qnty) as grey_receive_qnty,b.order_id,c.barcode_no from inv_receive_master a,  pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and booking_id =$program_id and a.receive_basis=2 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by knitting_source,knitting_company,receive_date,recv_number,b.machine_no_id,b.order_id,c.barcode_no";
				    $sql= "SELECT a.knitting_source,a.knitting_company,a.receive_date,a.recv_number,b.machine_no_id,(b.grey_receive_qnty) as grey_receive_qnty,b.order_id,b.id as dtls_id from inv_receive_master a,  pro_grey_prod_entry_dtls b where a.id=b.mst_id  and booking_id =$program_id and a.receive_basis=2 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
                    // echo $sql;                           
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
                        $orderIdArray[] = $row[csf('order_id')].",";
                    }
                    $poId = chop(implode(",",array_unique(array_filter($orderIdArray))),',');

                    $po_no_library = return_library_array("select id, po_number from wo_po_break_down where id in($poId)", "id", "po_number");
                    // $barcode_library = return_library_array("select barcode_no, po_breakdown_id from pro_roll_details where po_breakdown_id in($poId)", "po_breakdown_id", "barcode_no");
						$roll_sql=sql_select("select barcode_no, po_breakdown_id from pro_roll_details where dtls_id=".$row[csf('dtls_id')]."");
				//	echo "select barcode_no, po_breakdown_id from pro_roll_details where dtls_id=".$row[csf('dtls_id')]."";
					$barcode_no=$roll_sql[0][csf('barcode_no')];
                    $i = 1;
                    $total_inhouse_qnty = 0;
                    $total_outbount_qnty = 0;
                    foreach($result as $row)
                    {
                        if($row[csf('grey_receive_qnty')]>0)
                        {
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF"; 
                        
                            $total_issue_qnty+=$row[csf('qnty')];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                              <td width="30" align="center"><? echo $i; ?></td>
                              <td width="60" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                              <td width="120" align="center"><? echo $row[csf('recv_number')]; ?></td>
                              <td width="80" align="center"><? echo $row[csf('machine_no_id')]; ?></td>
                              <td width="100" align="center">
                                <? 
                                    foreach (explode(",", $row[csf('order_id')]) as $val) 
                                    {
                                        $po_number .= $po_no_library[$val].",";
                                    }  
                                    echo chop($po_number,',');
                                    unset($po_number);
                                ?>                              
                              </td>
                              <td width="100" align="center"><? echo $barcode_no; ?></td>
                              <td width="140" align="left">
                              <? //echo $company_library[$row[csf('knitting_company')]]; 
                                if($row[csf('knitting_source')] == 1 ){
                                    echo $company_library[$row[csf('knitting_company')]];
                                }else{
                                    echo $supplier_details[$row[csf('knitting_company')]];
                                }
                              
                              ?>
                              </td>
                              <td width="60" align="right"><? if($row[csf('knitting_source')]==1) echo $row[csf('grey_receive_qnty')]; ?></td>
                              <td width="60" align="right"><? if($row[csf('knitting_source')]==3) echo $row[csf('grey_receive_qnty')]; ?></td>
                              
                            </tr>
                            <?
                            $i++;
                            if($row[csf('knitting_source')]==1) $total_inhouse_qnty += $row[csf('grey_receive_qnty')];
                            if($row[csf('knitting_source')]==3) $total_outbount_qnty += $row[csf('grey_receive_qnty')];
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold;">
                            <td colspan="7" align="right">Total </td>
                            <td align="right"><? echo $total_inhouse_qnty; ?></td>
                            <td align="right"><? echo $total_outbount_qnty; ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>  
        </div>
    </fieldset>
    <?
    exit();
}

if($action == 'issue_return_popup')
{
    echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
	?>
	<fieldset style="width:370px; margin-left:2px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="4"><b>Yarn Issue Return</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="100">Issue Return Date</th>
                    <th width="100">Issue Return No</th>
                    <th width="">Qnty</th>
				</thead>
             </table>
             <div style="width:370px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0">
                    <?		
                    $sql="select a.recv_number, a.receive_date, sum(b.cons_quantity) as qnty from inv_receive_master a, inv_transaction b  where a.id = b.mst_id and a.id in ($ids) and b.status_active = 1 and b.is_deleted = 0 and a.entry_form = 9 and a.company_id = $companyID and b.transaction_type =4 and a.receive_basis = 3 group by a.recv_number, a.receive_date";
                    $result=sql_select($sql);
                    $i = 1;
                    foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_iss_ret_qnty+=$row[csf('qnty')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                          <td width="30" align="center"><? echo $i; ?></td>
                          <td width="100" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                          <td width="100" align="center"><? echo $row[csf('recv_number')]; ?></td>
                          <td width="" align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                        <tr>
                            <th colspan="3">Total =</th>
                            <th align="right"><? echo number_format($total_iss_ret_qnty,2); ?></th>
                        </tr>
                </table>
            </div>	
        </div>
	</fieldset>
	<?
	exit();
}

if($action == 'receive_popup')
{
    echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    
    $data = explode("**", $ids);
    $program_no = $data[0];
    $po_ids = $data[1];
    $companyId = $data[2];
    $knit_source = $data[3];
    
	?>
	<fieldset style="width:370px; margin-left:2px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="4"><b>Yarn Receive</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="100">Receive Date</th>
                    <th width="150">Receive No</th>
                    <th width="">Qnty</th>
				</thead>
             </table>
             <div style="width:370px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0">
                    <?	
                    
                    if($knit_source == 1)
                    {
                    $sql_22 = "select a.recv_number as booking_no,a.id,c.trans_id
                    from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c  
                    where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=2 and c.entry_form=2 and a.receive_basis=2 
                    and b.status_active=1 and b.is_deleted=0 and a.booking_id = '$program_no'  and a.company_id = $companyId
                    and c.po_breakdown_id in ($po_ids) ";
                    }
                    else
                    {
                    $sql_22 = "
                    select a.recv_number as booking_no,a.id as id,c.trans_id
                    from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c  
                    where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=2 and c.entry_form=2 and a.receive_basis=2 
                    and b.status_active=1 and b.is_deleted=0 and a.booking_id = '$program_no' and b.trans_id = 0 and a.company_id = $companyId
                    and c.po_breakdown_id in ($po_ids) 
                    union     
                    select b.booking_no, b.id as id,null as trans_id
                    from wo_booking_dtls a, wo_booking_mst b
                    where a.booking_no = b.booking_no and a.program_no = '$program_no' and a.booking_type = 3 
                    and a.process = 1 and b.item_category = 12
                    and a.status_active = 1 and a.is_deleted = 0
                    and b.status_active = 1 and b.is_deleted = 0 and a.po_break_down_id in ($po_ids) ";
                    }
                  $booking_id = "";
                  $result_22 = sql_select($sql_22);
                   foreach($result_22 as $row_22)
                   {
                       if($row_22[csf('trans_id')]==0)
					   {
					   $booking_id .= $row_22[csf('id')].",";
					   }
                   }
                   //for entry form 22
                  // echo $booking_id.'=';
                   $booking_id =  chop($booking_id,',');
                   if($booking_id != "0"){
                       $sql_extend = " union  
                    select a.recv_number,a.receive_date,sum(c.quantity) as qnty 
                    from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c 
                    where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=22 and c.entry_form=22 and a.receive_basis in (9) 
                    and b.status_active=1 and b.is_deleted=0 and a.company_id = $companyId
                    and a.booking_id in ($booking_id) and c.po_breakdown_id in($po_ids) 
                    group by a.recv_number,a.receive_date ";
                   }
                    
                  $sql =  "select b.recv_number,b.receive_date, sum(a.qnty) qnty
                    from pro_roll_details a,inv_receive_master b
                    where a.entry_form = 58 and a.mst_id = b.id
                    and a.booking_no = '$program_no' and a.po_breakdown_id in($po_ids)
                    and a.status_active = 1 and a.is_deleted = 0 and b.company_id = $companyId
                    group by b.recv_number,b.receive_date
                    union 
                    select a.recv_number, a.receive_date, sum(c.quantity) as qnty
                    from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c  
                    where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=2 and c.entry_form=2 and a.receive_basis=2 
                    and b.status_active=1 and b.is_deleted=0 and a.booking_id = '$program_no' and c.trans_id>0 and c.po_breakdown_id in($po_ids)  and a.company_id = $companyId
                    group by a.recv_number,a.receive_date
                    $sql_extend ";   //and b.trans_id <> 0   
                 
                    /*
                    
                   $fabricData = sql_select("select variable_list, auto_update, fabric_roll_level from variable_settings_production where company_name =$companyId and variable_list in(15) and item_category_id=13 and is_deleted=0 and status_active=1");
                   $variable = $fabricData[0][csf('auto_update')];
                   if($variable == 1)
                   {
                       $sql="select a.id,a.recv_number, a.receive_date, sum(b.qnty) as qnty
                        from inv_receive_master a, pro_roll_details b, pro_grey_prod_entry_dtls c 
                        where a.id = b.mst_id and c.id = b.dtls_id
                        and b.booking_no = '$program_no' and b.entry_form in (58,22,2) and b.po_breakdown_id in ($po_ids)
                        and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
                        and c.status_active = 1 and c.is_deleted = 0
                        group by a.id, a.recv_number, a.receive_date";
                   }else{
                   
                echo    $sql="select a.id,a.recv_number, a.receive_date, sum(b.qnty) as qnty
                        from inv_receive_master a, pro_roll_details b, pro_grey_prod_entry_dtls c 
                        where a.id = b.mst_id and c.id = b.dtls_id
                        and b.booking_no = '$program_no' and b.entry_form in (58,22,2) and b.po_breakdown_id in ($po_ids)
                        and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
                        and c.status_active = 1 and c.is_deleted = 0
                        group by a.id, a.recv_number, a.receive_date";
                   }*/
                    $result=sql_select($sql);
                    $i = 1;
                    foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_rcv_qnty+=$row[csf('qnty')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                          <td width="30" align="center"><? echo $i; ?></td>
                          <td width="100" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                          <td width="150" align="center"><? echo $row[csf('recv_number')]; ?></td>
                          <td width="" align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                        <tr>
                            <th colspan="3">Total =</th>
                            <th align="right"><? echo number_format($total_rcv_qnty,2); ?></th>
                        </tr>
                </table>
            </div>	
        </div>
	</fieldset>
	<?
	exit();
}

if ($action == "create_booking_no_search_list_view")
{
    $data = explode('**', $data);
    $company_id = $data[0];
    $cbo_year = $data[6];

    if ($data[1] == 0) {
        if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
            if ($_SESSION['logic_erp']["buyer_id"] != "")
                $buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
            else
                $buyer_id_cond = "";
        }
        else {
            $buyer_id_cond = "";
        }
    } else {
        $buyer_id_cond = " and a.buyer_name=$data[1]";
    }

    $search_by = $data[2];
    $search_string = "%" . trim($data[3]) . "%";

    if ($search_by == 1)
        $search_field = "a.job_no";
    else if ($search_by == 2)
        $search_field = "a.style_ref_no";
    else
        $search_field = "b.po_number";

    $start_date = $data[4];
    $end_date = $data[5];

    if ($start_date != "" && $end_date != "") {
        if ($db_type == 0) {
            $date_cond = "and b.pub_shipment_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
        } else {
            $date_cond = "and b.pub_shipment_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
        }
    } else {
        $date_cond = "";
    }

    $arr = array(0 => $company_library, 1 => $buyer_arr);
    if ($db_type == 0)
    {
    $year_field = "YEAR(a.insert_date) as year";
    $year_cond = " and YEAR(a.insert_date) = $cbo_year ";
    }
    else if ($db_type == 2)
    {
    $year_field = "to_char(a.insert_date,'YYYY') as year";
    $year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
    }
    else
    {$year_field = "";
    $year_cond = "";
    } //defined Later
    
    if ($db_type == 0)
                $select_field = "group_concat(b.po_number) as po_number";
            else if ($db_type == 2)
                $select_field = "listagg(cast(b.po_number as varchar(4000)), ',') within group (order by b.po_number) as po_number";

    $sql = "select a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $select_field, b.pub_shipment_date 
            from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond  $year_cond
            group by a.id, a.job_no, to_char(a.insert_date,'YYYY'),a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.pub_shipment_date 
            order by a.id, b.pub_shipment_date";
    //echo $sql;	
    echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170", "760", "220", 0, $sql, "js_set_value_job", "id,job_no", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "", '', '0,0,0,0,0,0,3', '',1);
    exit();
}
?>