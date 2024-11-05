<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
	header("location:login.php");

require_once('../../../includes/common.php');

$user_name = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//for company_wise_report_button_setting
if($action=="company_wise_report_button_setting")
{
    extract($_REQUEST);
    $print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=4 and report_id=168 and is_deleted=0 and status_active=1");
    
    //echo $print_report_format;

    $print_report_format_arr=explode(",",$print_report_format);
    echo "$('#Print2').hide();\n";
    echo "$('#Print11').hide();\n";
    echo "$('#Print12').hide();\n";
    
    if($print_report_format != "")
    {
        foreach($print_report_format_arr as $id)
        {
            if($id==131){echo "$('#Print2').show();\n";}   
            if($id==353){echo "$('#Print11').show();\n";}             
            if($id==572){echo "$('#Print12').show();\n";}           
                        
        }
    }
    else
    {
        echo "$('#Print2').show();\n";
        echo "$('#Print11').show();\n";
        echo "$('#Print12').show();\n";
    }  
    exit(); 
}

//for load_drop_down_customer
if ($action == "load_drop_down_customer")
{
	$data = explode("_", $data);
	$within_group = $data[0];
	$company_id = $data[1];

	if ($company_id == 0)
	{
		echo create_drop_down("cbo_buyer_name", 110, $blank_array, "", 1, "--Select Buyer--", 0, "");
	}
	else
	{
		if ($within_group == 1)
		{
			echo create_drop_down("cbo_buyer_name", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 ".$company_cond." order by comp.company_name", "id,company_name", 1, "-- Select Buyer --", "0", "", 1);
		}
		else if ($within_group == 2)
		{
			echo create_drop_down("cbo_buyer_name", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".$company_id."' ".$buyer_cond." and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
		}
	}
	exit();
}

//for load_drop_down_cust_buyer
if ($action == "load_drop_down_cust_buyer") 
{
	$data = explode("_", $data);
	$within_group = $data[0];
	$company_id = $data[1];

	if ($company_id == 0) 
	{
		echo create_drop_down("cbo_buyer_id", 110, $blank_array, "", 1, "--Select Buyer--", 0, "");
	}
	else  
	{
		echo create_drop_down("cbo_buyer_id", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".$company_id."' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
	}
	exit();
}

if ($action == "load_drop_down_party_type")
{
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

if ($action == "job_no_search_popup")
{
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
                        onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value, 'create_job_no_search_list_view', 'search_div', 'knitting_plan_sales_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
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

if ($action == "create_job_no_search_list_view")
{
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
	// echo $sql;	die;
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

if ($action == "booking_no_search_popup")
{
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
            show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+ '**' + document.getElementById('cbo_year_selection').value + '**' + document.getElementById('cbo_within_group').value, 'create_order_no_search_list_view', 'search_div', 'knitting_plan_sales_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
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

if ($action == "create_order_no_search_list_view")
{
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

	if ($template == 1) 
    {
		$cbo_knitting_status = str_replace("'", "", $cbo_knitting_status);
		$based_on = str_replace("'", "", $cbo_based_on);
		$presentationType = str_replace("'", "", $presentationType);
		$type = str_replace("'", "", $cbo_type);
		$cbo_buyer_id = str_replace("'", "", $cbo_buyer_id);
		$company_name = $cbo_company_name;
		$cbo_within_group =  str_replace("'", "", $cbo_within_group);
		
		//for cbo_within_group
		$within_group_cond = " and e.within_group = ".$cbo_within_group;
		
		//for cbo_party_type
		if (str_replace("'", "", $cbo_party_type) == 0)
			$party_type = "%%";
		else
			$party_type = str_replace("'", "", $cbo_party_type);
		
		//for cbo_buyer_name(customer)
        if (str_replace("'", "", $cbo_buyer_name) == 0) 
        {
			if ($_SESSION['logic_erp']["data_level_secured"] == 1)
			{
				if ($_SESSION['logic_erp']["buyer_id"] != "")
					$buyer_id_cond = " and e.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				else
					$buyer_id_cond = "";
			}
			else
			{
				$buyer_id_cond = "";
			}
		}
		else
		{
			$buyer_id_cond = " and e.buyer_id = ".$cbo_buyer_name;
		}
		
		//for cbo_buyer_id(customer buyer)
		if ($cbo_buyer_id != 0)
		{
			$customer_buyer_cond = " and e.customer_buyer=".$cbo_buyer_id;
		}
		else
		{
			$customer_buyer_cond = "";
		}
		
		//for cbo_year
		$year_cond = "";
		$cbo_year = str_replace("'", "", $cbo_year);
		if (trim($cbo_year) != 0)
		{
			$year_cond = " and to_char(e.insert_date,'YYYY')=$cbo_year";
		}
		
		//for txt_sales_no
		$sales_no_cond = "";
		if (str_replace("'", "", $txt_sales_no) != "")
		{
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

		//for txt_booking_no
		$booking_search_cond = "";
		if (str_replace("'", "", trim($txt_booking_no)) != "")
		{
			$booking_number = "%" . trim(str_replace("'", "", $txt_booking_no)) . "%";
			$booking_search_cond = "and e.sales_booking_no like '$booking_number'";
		}
		
		//for year
		$year_field = "to_char(e.insert_date,'YYYY') as year";
		   
		//for program date
		$date_cond = "";
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

		if ($cbo_buyer_id != 0)
		{
            $buyer_cond = " and e.po_buyer=".$cbo_buyer_id;
		}

		//for txt_machine_dia
		if (str_replace("'", "", $txt_machine_dia) == "")
			$machine_dia = "%%";
		else
			$machine_dia = "%" . str_replace("'", "", $txt_machine_dia) . "%";
		
		//for txt_program_no
		if (str_replace("'", "", $txt_program_no) == "")
			$program_no = "%%";
		else
			$program_no = str_replace("'", "", $txt_program_no);
		
		//for knitting source
		if ($type > 0)
			$knitting_source_cond = "and b.knitting_source=$type";
		else
			$knitting_source_cond = "";
		
		//for cbo_knitting_status
		$status_cond = "";
		if ($cbo_knitting_status != "")
			$status_cond = "and b.status in($cbo_knitting_status)";


        if ($presentationType == 5)
        {

            if ($txt_machine_no != "")
            {
                $machine_id_ref = return_field_value("LISTAGG(cast(id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as machine_id", "lib_machine_name", "machine_no='$txt_machine_no'", "machine_id");
                
                //echo $machine_id_ref;
                $sql = "SELECT x.company_id, x.buyer_id, x.body_part_id, x.color_type_id, x.fabric_desc, x.gsm_weight, x.dia, x.width_dia_type, x.id, x.knitting_source, x.knitting_party, x.color_id, x.color_range, x.machine_dia, x.machine_gg, x.program_qnty, x.program_date, x.stitch_length, x.spandex_stitch_length, x.draft_ratio, x.machine_id, x.distribution_qnty, x.status, x.start_date, x.end_date, x.remarks,x.po_buyer,x.within_group,x.po_company_id, x.sales_booking_no, x.style_ref_no, x.job_no, x.booking_entry_form, x.booking_id, x.buyer_id, x.customer_buyer from(SELECT a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks,e.po_buyer,e.within_group,e.po_company_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id as customer, e.customer_buyer
                from
                    ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, ppl_entry_machine_datewise d, fabric_sales_order_mst e
                where
                    a.id=b.mst_id and b.id=c.dtls_id and b.id=d.dtls_id and c.po_id=e.id and c.is_sales=1 and d.machine_id in($machine_id_ref) and a.company_id=$company_name and b.knitting_party like '$party_type' and b.machine_dia like '$machine_dia' and b.id like '$program_no' and a.is_sales=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=1 and b.status_active=0 and c.is_deleted=1 and c.status_active=0 and e.entry_form = 472 $buyer_id_cond $customer_buyer_cond $sales_no_cond $year_cond $booking_search_cond $date_cond $status_cond $knitting_source_cond $within_group_cond 
                group by b.id, a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.knitting_source, b.knitting_party, b.color_id,e.po_buyer,e.within_group,e.po_company_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id, e.customer_buyer
                 union all 
                 SELECT a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks,e.po_buyer,e.within_group,e.po_company_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id as customer, e.customer_buyer
                from
                    ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, ppl_entry_machine_datewise d, fabric_sales_order_mst e
                where
                    a.id=b.mst_id and b.id=c.dtls_id and b.id=d.dtls_id and c.po_id=e.id and c.is_sales=1 and d.machine_id in($machine_id_ref) and a.company_id=$company_name and b.knitting_party like '$party_type' and b.machine_dia like '$machine_dia' and b.id like '$program_no' and a.is_sales=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.is_revised=1 and e.entry_form = 472 $buyer_id_cond $customer_buyer_cond $sales_no_cond $year_cond $booking_search_cond $date_cond $status_cond $knitting_source_cond $within_group_cond 
                group by b.id, a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.knitting_source, b.knitting_party, b.color_id,e.po_buyer,e.within_group,e.po_company_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id, e.customer_buyer) x order by x.knitting_source,x.machine_dia,x.machine_gg, x.id";
            }
            else
            {
                $sql = "SELECT x.company_id, x.buyer_id, x.body_part_id, x.color_type_id, x.fabric_desc, x.gsm_weight, x.dia, x.width_dia_type, x.id, x.knitting_source, x.knitting_party, x.color_id, x.color_range, x.machine_dia, x.machine_gg, x.program_qnty, x.program_date, x.stitch_length,
                x.spandex_stitch_length, x.draft_ratio, x.machine_id, x.distribution_qnty, x.status, x.start_date, x.end_date, x.remarks, x.po_buyer, x.within_group, x.po_company_id, x.sales_booking_no, x.style_ref_no, x.job_no, x.booking_entry_form, x.booking_id, x.customer, x.customer_buyer 
                from(SELECT a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, e.po_buyer, e.within_group, e.po_company_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id as customer, e.customer_buyer
                from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, fabric_sales_order_mst e   
                where a.id=b.mst_id and b.id=c.dtls_id and c.po_id=e.id and c.is_sales=1 and a.company_id=$company_name  and b.knitting_party like '$party_type' and b.machine_dia like '$machine_dia' and b.id like '$program_no' and a.is_sales=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=1 and b.status_active=0 and c.is_deleted=1 and c.status_active=0 and e.entry_form = 472 $buyer_id_cond $customer_buyer_cond $sales_no_cond $year_cond $booking_search_cond $date_cond $status_cond  $knitting_source_cond $within_group_cond
                group by b.id, a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.knitting_source, b.knitting_party, b.color_id,e.po_buyer,e.within_group,e.po_company_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id, e.customer_buyer 
                union all
                SELECT a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, e.po_buyer, e.within_group, e.po_company_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id as customer, e.customer_buyer
                from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, fabric_sales_order_mst e   
                where a.id=b.mst_id and b.id=c.dtls_id and c.po_id=e.id and c.is_sales=1 and a.company_id=$company_name  and b.knitting_party like '$party_type' and b.machine_dia like '$machine_dia' and b.id like '$program_no' and a.is_sales=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.is_revised=1 and e.entry_form = 472 $buyer_id_cond $customer_buyer_cond $sales_no_cond $year_cond $booking_search_cond $date_cond $status_cond  $knitting_source_cond $within_group_cond
                group by b.id, a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.knitting_source, b.knitting_party, b.color_id,e.po_buyer,e.within_group,e.po_company_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id, e.customer_buyer) x order by x.knitting_source, x.machine_dia, x.machine_gg, x.id";
            }

        }
        else
        {

            if ($txt_machine_no != "")
            {
                $machine_id_ref = return_field_value("LISTAGG(cast(id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as machine_id", "lib_machine_name", "machine_no='$txt_machine_no'", "machine_id");
                
                //echo $machine_id_ref;
                $sql = "SELECT a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id,e.po_buyer,e.within_group,e.po_company_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id as customer, e.customer_buyer
                from
                    ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, ppl_entry_machine_datewise d, fabric_sales_order_mst e
                where
                    a.id=b.mst_id and b.id=c.dtls_id and b.id=d.dtls_id and c.po_id=e.id and c.is_sales=1 and d.machine_id in($machine_id_ref) and a.company_id=$company_name and b.knitting_party like '$party_type' and b.machine_dia like '$machine_dia' and b.id like '$program_no' and a.is_sales=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.entry_form = 472 $buyer_id_cond $customer_buyer_cond $sales_no_cond $year_cond $booking_search_cond $date_cond $status_cond $knitting_source_cond $within_group_cond 
                group by b.id, a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.knitting_source, b.knitting_party, b.color_id,e.po_buyer,e.within_group,e.po_company_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id, e.customer_buyer order by b.knitting_source,b.machine_dia,b.machine_gg, b.id";
            }
            else
            {
              $sql = "SELECT a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id, e.po_buyer, e.within_group, e.po_company_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id as customer, e.customer_buyer
                from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, fabric_sales_order_mst e   
                where a.id=b.mst_id and b.id=c.dtls_id and c.po_id=e.id and c.is_sales=1 and a.company_id=$company_name  and b.knitting_party like '$party_type' and b.machine_dia like '$machine_dia' and b.id like '$program_no' and a.is_sales=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.entry_form = 472 $buyer_id_cond $customer_buyer_cond $sales_no_cond $year_cond $booking_search_cond $date_cond $status_cond  $knitting_source_cond $within_group_cond
                group by b.id, a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.knitting_source, b.knitting_party, b.color_id,e.po_buyer,e.within_group,e.po_company_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id, e.sales_booking_no, e.style_ref_no, e.job_no, e.booking_entry_form, e.booking_id, e.buyer_id, e.customer_buyer order by b.knitting_source, b.machine_dia, b.machine_gg, b.id";
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

        if(!empty($nameArray))  
        {
            $con = connect();
            //$r_id3=execute_query("delete from tmp_prog_no where userid=$user_name");
            //$r_id4=execute_query("delete from tmp_reqs_no where userid=$user_name");
            execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_name and entry_form in (91)");
            $r_id5=execute_query("delete from tmp_barcode_no where userid=$user_name");
            oci_commit($con);
        }

		$planIdArr =$prog_id_check=$booking_idArr=array();
		foreach($nameArray as $row)
		{
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

            if(!$prog_id_check[$row[csf('id')]])
            {
                $prog_id_check[$row[csf('id')]]=$row[csf('id')];
                $ProgNO = $row[csf('id')];
                $ProgNOArr[$row[csf('id')]] = $row[csf('id')];
                //$rID3=execute_query("insert into tmp_prog_no (userid, prog_no) values ($user_name,$ProgNO)");
            } 
		}
        fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 91, 1,$ProgNOArr, $empty_arr);
        fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 91, 4,$booking_idArr, $empty_arr);
        oci_commit($con);
        
        // echo "<pre>";print_r($booking_idArr);die;

        $noOfbooking = count($booking_idArr);
        if ($noOfbooking>0) 
        {
            /*$bookingCondition = '';        
            if($db_type == 2 && $noOfbooking > 1000)
            {
                $bookingCondition = " and (";
                $bookingArrNew = array_chunk($booking_idArr,999);
                foreach($bookingArrNew as $prod)
                {
                    $bookingCondition.=" id in('".implode("','",$prod)."') or";
                }
                $bookingCondition = chop($bookingCondition,'or');
                $bookingCondition .= ")";
            }
            else
            {
                $bookingCondition=" and id in('".implode("','",$booking_idArr)."')";
            }*/

            $booking_sql="SELECT a.booking_no, a.company_id, a.po_break_down_id, a.item_category, a.fabric_source, a.job_no ,a.entry_form, a.is_approved
            from wo_booking_mst a,GBL_TEMP_ENGINE b where a.id=b.ref_val and b.ref_from=4 and b.entry_form=91 a.entry_form in(86,88,118) and a.booking_type=1 and a.is_short in(1,2) and a.status_active=1 and a.is_deleted=0";
            // booking_no in('fal-fb-21-00654','fal-fb-21-00562','fal-fb-21-00667')
        }
         //echo $booking_sql;die;
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
        }

		$planIdCondition = '';
		$kintIdCondition = '';
		$bookingIdCondition = '';
		$programNoCondition = '';
		
		$noOfPlanId = count($planIdArr);
		/*if($db_type == 0)
		{
			$planIdCondition=" and dtls_id in('".implode("','",$planIdArr)."')";
			$kintIdCondition=" and knit_id in('".implode("','",$planIdArr)."')";
			$bookingIdCondition=" and a.booking_id in('".implode("','",$planIdArr)."')";
			$programNoCondition=" and b.program_no in('".implode("','",$planIdArr)."')";
		}*/
		
		//for requisition information
		$reqsData = sql_select("select a.knit_id, max(a.requisition_no) as reqs_no, LISTAGG(a.prod_id, ',') WITHIN GROUP (ORDER BY a.prod_id) as prod_id from ppl_yarn_requisition_entry a,GBL_TEMP_ENGINE b where a.knit_id=b.ref_val and b.entry_form=91 and b.ref_from=1 and b.user_id=$user_name and  a.status_active=1 and a.is_deleted=0  group by a.knit_id,a.requisition_no");
		$reqsDataArr = array();
		foreach ($reqsData as $row)
		{
		   $reqsDataArr[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
		   $reqsDataArr[$row[csf('knit_id')]]['prod_id'] = $row[csf('prod_id')];
		}
		//echo "<pre>";
		//print_r($reqsDataArr); die;
		
		$product_details_arr = array();
		$yarn_iss_arr = array();
		$yarn_IssRtn_arr = array();
		if(!empty($reqsDataArr))
		{
			$requisitionNoArr = array();
			$productIdArr = $reqs_no_check=array();$product_no_check=array();
			foreach($reqsDataArr as $row)
			{
				$requisitionNoArr[$row['reqs_no']] = $row['reqs_no'];
                if(!$reqs_no_check[$row['reqs_no']])
                {
                    $reqs_no_check[$row['reqs_no']]=$row['reqs_no'];
                    $ReqsNo = $row['reqs_no'];
                    $ReqsNoArr[$row['reqs_no']] = $row['reqs_no'];
                    //$rID4=execute_query("insert into tmp_reqs_no (userid, reqs_no) values ($user_name,$ReqsNo)");
                }
				$productId = explode(",", $row['prod_id']);
                
				foreach($productId as $prodId)
				{
                    if($product_no_check[$prodId]!=$prodId)
                    {
                        $product_no_check[$prodId]=$prodId;
					    $productIdArr[$prodId] = $prodId;
                    }
				}
			}
            fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 91, 2,$ReqsNoArr, $empty_arr);
            fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 91, 3,$productIdArr, $empty_arr);

            oci_commit($con);
			
			//--------------------------------
			/*$productIdCondition = '';
			$noOfProductId = count($productIdArr);
			if($db_type == 2 && $noOfProductId > 1000)
			{
				$productIdCondition = " and (";
				$productIdArrNew = array_chunk($productIdArr,999);
				foreach($productIdArrNew as $prod)
				{
					$productIdCondition.=" id in('".implode("','",$prod)."') or";
				}
				$productIdCondition = chop($productIdCondition,'or');
				$productIdCondition .= ")";
			}
			else
			{
				$productIdCondition=" and id in('".implode("','",$productIdArr)."')";
			}*/
			//--------------------------------
			$pro_sql = sql_select("select a.id, a.product_name_details, a.lot, a.supplier_id from product_details_master a,GBL_TEMP_ENGINE b where a.company_id=$company_name and a.id=b.ref_val and b.ref_from=3 and b.entry_form=91 and a.item_category_id=1 ");
			foreach ($pro_sql as $row)
			{
			   $product_details_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
			   $product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
			   $product_details_arr[$row[csf('id')]]['supplier'] = $row[csf('supplier_id')];
			}
			
			//--------------------------------
            $yarnIssueData = sql_select("select a.requisition_no, a.prod_id, sum(a.cons_quantity) as qnty from inv_transaction a,GBL_TEMP_ENGINE b where a.requisition_no=b.ref_val and b.ref_from=2 and b.entry_form=91 and b.user_id=$user_name and a.item_category=1 and a.transaction_type=2 and a.receive_basis in(3,8) and a.status_active=1 and a.is_deleted=0  group by a.requisition_no, a.prod_id");
			foreach ($yarnIssueData as $row)
			{
			   $yarn_iss_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]] = $row[csf('qnty')];
			}

           $yarnIssueRtnData = sql_select("SELECT a.booking_id AS reqsn_no,b.prod_id,SUM (b.cons_quantity) AS qnty,SUM (b.cons_reject_qnty) AS reject_qnty FROM GBL_TEMP_ENGINE c, inv_receive_master a, inv_transaction b WHERE a.booking_id = c.ref_val and c.ref_from=2 and c.entry_form=91 AND c.user_id = $user_name And a.id = b.mst_id AND a.receive_basis IN (3) AND a.entry_form = 9 AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active = 1
            AND a.is_deleted = 0 GROUP BY a.booking_id, b.prod_id
            union all 
            SELECT a.requisition_no AS reqsn_no,b.prod_id,SUM (b.cons_quantity) AS qnty,SUM (b.cons_reject_qnty) AS reject_qnty FROM GBL_TEMP_ENGINE c, inv_receive_master a, inv_transaction b WHERE  a.requisition_no = c.ref_val and c.ref_from=2 and c.entry_form=91 AND c.user_id = $user_name And a.id = b.mst_id AND a.receive_basis IN (8) AND a.entry_form = 9 AND b.item_category = 1 AND b.transaction_type = 4 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY a.requisition_no, b.prod_id");

			foreach ($yarnIssueRtnData as $row)
			{
				$yarn_IssRtn_arr[$row[csf('reqsn_no')]][$row[csf('prod_id')]] = $row[csf('qnty')];
				$yarn_IssRej_arr[$row[csf('reqsn_no')]][$row[csf('prod_id')]] = $row[csf('reject_qnty')];
			}
		}
		//echo "<pre>";
		//print_r($yarn_IssRtn_arr); die;
		
	   //for knitting production information
	  /*  $knitting_dataArray = sql_select("select a.booking_id, LISTAGG(a.id, ',') WITHIN GROUP (ORDER BY a.id) as knit_id, LISTAGG(b.gsm, ',') WITHIN GROUP (ORDER BY b.gsm) as gsm, sum(b.grey_receive_qnty) as knitting_qnty,sum(reject_fabric_receive) as fabric_reject_qty from GBL_TEMP_ENGINE c, inv_receive_master a, pro_grey_prod_entry_dtls b where a.booking_id=c.ref_val and c.entry_form=91 and c.ref_from=1 and c.user_id=$user_name and a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0  group by a.booking_id"); */
      $knitting_dataArray = sql_select("select a.booking_id, a.id as knit_id, b.gsm as gsm, b.grey_receive_qnty as knitting_qnty,b.reject_fabric_receive as fabric_reject_qty from GBL_TEMP_ENGINE c, inv_receive_master a, pro_grey_prod_entry_dtls b where a.booking_id=c.ref_val and c.entry_form=91 and c.ref_from=1 and c.user_id=$user_name and a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0  group by a.booking_id,a.id,b.gsm,b.grey_receive_qnty,b.reject_fabric_receive ");
       
      $knitDataArr = array();
      foreach ($knitting_dataArray as $row)
      {
          $knitDataArr[$row[csf('booking_id')]]['qnty'] = $row[csf('knitting_qnty')];
          $knitDataArr[$row[csf('booking_id')]]['knit_id'] .= $row[csf('knit_id')].',';
          $knitDataArr[$row[csf('booking_id')]]['gsm'] .= $row[csf('gsm')].',';
          $knitDataArr[$row[csf('booking_id')]]['reject_qnty'] = $row[csf('fabric_reject_qty')];
          //$knitDataArr[$row[csf('id')]]['qnty']
          //$knitDataArr[$row[csf('id')]]['knit_id']
      }
		//echo "<pre>";
		//print_r($knitDataArr); die;
		
        $knitting_recv_qnty_array = return_library_array("select a.booking_id, sum(b.grey_receive_qnty) as knitting_qnty from GBL_TEMP_ENGINE c, inv_receive_master a, pro_grey_prod_entry_dtls b where a.booking_id=c.ref_val and c.ref_from=1 and c.entry_form=91 and c.user_id=$user_name and a.id=b.mst_id and a.item_category=13 and a.entry_form=22 and a.receive_basis=9 and b.status_active=1 and b.is_deleted=0 group by a.booking_id", "booking_id", "knitting_qnty");

        $knit_recvProg_qty_arr = return_library_array("select b.program_no, sum(b.grey_receive_qnty) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b,GBL_TEMP_ENGINE c where a.id=b.mst_id and b.program_no=c.ref_val and c.ref_from=1 and c.entry_form=91 and c.user_id=$user_name and a.item_category=13 and a.entry_form=22 and a.receive_basis=11 and b.status_active=1 and b.is_deleted=0 group by b.program_no", "program_no", "knitting_qnty");

		$barcode_arr = array();
		$barcode_no_arr = $barcode_no_check=array();
        $barcodeData = sql_select("select b.mst_id, b.barcode_no from GBL_TEMP_ENGINE c, inv_receive_master a, pro_roll_details b where a.booking_id=c.ref_val and c.ref_from=1 and c.entry_form=91 and c.user_id=$user_name and a.id=b.mst_id and a.entry_form=2 and b.entry_form=2 and a.item_category=13 and a.receive_basis=2");
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
		
		$barcode_no_arr=array_filter($barcode_no_arr);
		if(!empty($barcode_no_arr))
		{
			$all_barcode_nos = implode(",",$barcode_no_arr);
			$all_barcode_no_cond=""; $barCond=""; 

            $delivery_qty_arr = return_library_array("select a.barcode_no, a.qnty from pro_roll_details a,tmp_barcode_no b where a.barcode_no=b.barcode_no and b.userid=$user_name and a.entry_form=58 and a.status_active=1 and a.is_deleted=0", "barcode_no", "qnty");

            $deliveryquantityArr = sql_select("SELECT a.booking_no,a.barcode_no,a.qnty as current_delivery 
            from tmp_barcode_no c, pro_roll_details a, pro_grey_prod_delivery_dtls b 
            where c.barcode_no=a.barcode_no and c.userid=$user_name and a.entry_form in(2,56) and a.booking_without_order=0 and a.status_active=1 and a.is_deleted=0 and a.barcode_no=b.barcode_num and a.mst_id=b.grey_sys_id and b.status_active=1 and b.is_deleted=0
            group by a.booking_no,a.barcode_no,a.qnty");

			$deliveryStorQtyArr = array();
			foreach ($deliveryquantityArr as $row)
			{
				$deliveryStorQtyArr[$row[csf('booking_no')]] += $row[csf('current_delivery')];
				//$deliveryStorNoOfRollArr[$row[csf('booking_no')]] += $row[csf('roll_no_delv')];
			}
		}

		$colspan = 30;
		$tbl_width = 4140;
		$search_by_arr = array(0 => "All", 1 => "Inside", 3 => "Outside");
        
        //$r_id3=execute_query("delete from tmp_prog_no where userid=$user_name");
        //$r_id4=execute_query("delete from tmp_reqs_no where userid=$user_name");
        execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_name and entry_form in (91)");
        $r_id5=execute_query("delete from tmp_barcode_no where userid=$user_name");
        oci_commit($con);
   
        $format_sql = sql_select("select format_id, template_name, report_id from lib_report_template where module_id in (2) and report_id in (1,2) and is_deleted=0 and status_active=1 order by id ");

        foreach ($format_sql as $val) {
            if($val[csf('report_id')]==1){
                $print_report_format_arr[$val[csf('report_id')]][$val[csf('template_name')]] = $val[csf('format_id')];
            }

            if($val[csf('report_id')]==2 )
            {
                $print_report_format_arr[$val[csf('report_id')]][$val[csf('template_name')]] = $val[csf('format_id')];
            }
        }
        
        //echo "<br />Execution Time: " . (microtime(true) - $started) . "S";die;

		if ($presentationType == 1 || $presentationType == 5)
		{
		   ob_start();
		   ?>
            <fieldset style="width:<? echo $tbl_width; ?>px;">
				<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width - 40; ?>">
					<tr>
						<td align="center" width="100%" colspan="<? echo $colspan + 7; ?>" style="font-size:16px"><strong>Knitting Plan Report: <? echo $search_by_arr[str_replace("'", "", $type)]; ?></strong></td>
						<td><input type="hidden" value="<? echo $type; ?>" id="typeForAttention"/></td>
					</tr>
				</table>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width - 40; ?>" class="rpt_table">
				   <thead>
					<th width="40"></th>
					<th width="40">SL</th>
					<th width='100'>Knitting Party</th>
					<th width="60">Program No</th>
					<th width="70">Req. No</th>
					<th width="80">Program Date</th>
					<th width="80">Start Date</th>
					<th width="80">T.O.D</th>
					<th width="120">Customer</th>
					<th width="80">Cust. Buyer</th>
					<th width="90">Style</th>
					<th width="130">Sales Order No</th>
					<th width="100">Sales/Fab. Booking No</th>
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
					<th width="100">Yarn Issue Qnty</th>
					<th width="100">Issue Return Qnty</th>
					<th width="100">Reject Qnty</th>
					<th width="100">Issue. Bal. Qnty</th>
					<th width="100">Knitting Qnty</th>
					<th width="100">Reject Fabric Qnty</th>
					<th width="100">Knit Balance Qnty</th>
					<th width="100">Delivery Store</th>
					<th width="100">Received Qnty</th>
					<th width="100">Recv. Bal. Qnty</th>
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
						$tot_balance = 0;
						$tot_balance_recv_qnty = 0;
						$tot_knitting_recv_qnty = 0;
						$yarn_issue_bl_qnty = 0;
						$actual_yarn_issue_qnty = 0;
						$machine_dia_gg_array = array();
				    
                        // Fabric Sales Order Entry
    				    $print_report_format=return_field_value("format_id"," lib_report_template","template_name = $company_name and module_id = 7 and report_id = 67 and status_active = 1 and is_deleted = 0");
    				    $fReportId=explode(",",$print_report_format);
    				    $fReportId=$fReportId[0];
    		
    					$party_array = array();
    					foreach ($nameArray as $row) 
                        {
    						if ($i % 2 == 0)
    							$bgcolor = "#E9F3FF";
    						else
    							$bgcolor = "#FFFFFF";
    						
    						$knitting_source = $row[csf('knitting_source')];    						
    						if ($knitting_source == 1)
							{
    							$knitting_cond = "Inside";
    							$knitting_party = $company_library[$row[csf('knitting_party')]];
    						}
							else
							{
    							$knitting_cond = "Outside";
    							$knitting_party = $supplier_details[$row[csf('knitting_party')]];
    						}
    						$machine_dia_gg = $row[csf('machine_dia')] . 'X' . $row[csf('machine_gg')];
    						
    						$machine_no = '';
    						$machine_id = explode(",", $row[csf("machine_id")]);
    						foreach ($machine_id as $val)
							{
    							if ($machine_no == '')
    								$machine_no = $machine_arr[$val];
    							else
    								$machine_no .= "," . $machine_arr[$val];
    						}
    		
    						$gmts_color = '';
    						$color_id = explode(",", $row[csf("color_id")]);
    						foreach ($color_id as $val)
							{
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
    						foreach ($prod_id as $val) 
                            {
    							$yarn_desc .= $product_details_arr[$val]['desc'] . ",";
    							$lot .= $product_details_arr[$val]['lot'] . ",";
    							$supplier .= $supplier_details[$product_details_arr[$val]['supplier']] . ",";
    							//$yarnRtnQty=$yarn_IssRtn_arr[$result_reqs[0][csf('reqs_no')]][$val];
    							//$yarn_issue_qnty+=$yarn_iss_arr[$result_reqs[0][csf('reqs_no')]][$val]-$yarnRtnQty;
    							$yarnRtnQty += $yarn_IssRtn_arr[$reqsDataArr[$row[csf('id')]]['reqs_no']][$val];
    							$yarn_issue_reject_qnty += $yarn_IssRej_arr[$reqsDataArr[$row[csf('id')]]['reqs_no']][$val];
    							$yarn_issue_qnty += $yarn_iss_arr[$reqsDataArr[$row[csf('id')]]['reqs_no']][$val];
    						}
    		
    						$yarn_desc = explode(",", substr($yarn_desc, 0, -1));
    						$lot = explode(",", substr($lot, 0, -1));
    						$supplier = explode(",", substr($supplier, 0, -1));
    						$po_id = array_unique(explode(",", $plan_details_array[$row[csf('id')]]));
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
                           // $print_report_format2=return_field_value("format_id"," lib_report_template","template_name =$booking_company  and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
                            //$fReportId2=explode(",",$print_report_format2);
                            //$fReportId2=$fReportId2[0];

                            // Short Fabric Booking
                            //$print_report_format3=return_field_value("format_id"," lib_report_template","template_name =$booking_company  and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");
                            //$fReportId3=explode(",",$print_report_format3);
                            //$fReportId3=$fReportId3[0];

                            if ($booking_entry_form==86 || $booking_entry_form==118) 
                            {// Budget Wise Fabric Booking and Main Fabric Booking V2
                                //$fbReportId=$fReportId2;
                                $fbReportIds= $print_report_format_arr[1][$booking_company];
                            }
                            else if($booking_entry_form==88)
                            {
                                //$fbReportId=$fReportId3;// Short Fabric Booking
                                $fbReportIds=$print_report_format_arr[2][$booking_company];
                            }
                            $fbReportIdArr=explode(",",$fbReportIds);
			                $fbReportId=$fbReportIdArr[0];

    		
    						$knitting_qnty = $knitDataArr[$row[csf('id')]]['qnty'] + $knit_recvProg_qty_arr[$row[csf('id')]];
    						$knit_id = $knitDataArr[$row[csf('id')]]['knit_id'];
    						$knit_id = array_unique(explode(",", $knit_id));
    						$fabric_reject_qnty = $knitDataArr[$row[csf('id')]]['reject_qnty']; 
    		
    		
    						$knit_recv_qty = 0;
    						$knitting_recv_qnty = 0;
    						foreach ($knit_id as $val) {
    							$delivery_qty = 0;
    							$barcode_nos = explode(",", chop($barcode_arr[$val], ','));
    							foreach ($barcode_nos as $barcode_no) {
    								$delivery_qty += $delivery_qty_arr[$barcode_no];
    							}
    							$knit_recv_qty += $knitting_recv_qnty_array[$val] + $delivery_qty;
    						}
    						$knitting_recv_qnty = $knit_recv_qty + $knit_recvProg_qty_arr[$row[csf('id')]];
    						
    						$balance_qnty = $row[csf('program_qnty')] - $knitting_qnty;
    						$balance_recv_qnty = $knitting_qnty - $knitting_recv_qnty;
    						//$yarn_issue_bl_qnty = $row[csf('program_qnty')] - $yarn_issue_qnty;
    	
    						$complete = '&nbsp;';
    						if ($knitting_qnty >= $row[csf('program_qnty')])
    						$complete = 'Complete';
    		
    		
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
    									<b><? echo number_format($sub_fab_rej_qnty, 2, '.', ''); ?></b></td>
    									<td align="right"><b><? echo number_format($sub_tot_balance, 2, '.', ''); ?></b>
    									<td align="right"><b><? echo number_format($sub_delv_store_qnty, 2, '.', ''); ?></b>
    									</td>
    									<td align="right">
    									<b><? echo number_format($sub_tot_knitting_recv_qnty, 2, '.', ''); ?></b>
    									</td>
    									<td align="right">
    									<b><? echo number_format($sub_tot_recv_balance, 2, '.', ''); ?></b></td>
    									<td>&nbsp;</td>
    									<td>&nbsp;</td>
    								</tr>
    								<?
    								$sub_tot_program_qnty = 0;
    								$sub_tot_knitting_qnty = 0;
    								$sub_tot_balance = 0;
    								$sub_yarn_issue_qnty = 0;
    								$sub_yarn_issue_rtn_qnty = 0;
    								$sub_yarn_issue_rej_qnty = 0;
    								$sub_yarn_issue_bl_qnty = 0;
    								$sub_fab_rej_qnty = 0;
    								$sub_delv_store_qnty = 0;
                                    $sub_tot_knitting_recv_qnty = 0;
                                    $sub_tot_recv_balance = 0;
    							}
    		
    							// if(!in_array($knitting_cond,$party_array))
    							if ($knitting_source_tmp[$knitting_source] == '') {
    								$knitting_source_tmp[$knitting_source] = $knitting_source;
    								?>
    								<tr bgcolor="#C6D9C9">
    									<td style="font-weight: 900" colspan="<? echo $colspan + 9+4; ?>"><b><?php echo $knitting_cond; ?></b></td>
    								</tr>
    								<?
    								$party_array[] = $knitting_cond;
    							}
    							?>
    							<tr bgcolor="#EFEFEF">
    								<td colspan="<? echo $colspan + 9+4; ?>"><b>Machine Dia:- <?php echo $machine_dia_gg; ?></b></td>
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
    							<td width="100"><p><? echo $knitting_party; ?></p></td>
    							<td width="60" align="center"><a href='##'
    							onClick="generate_report2(<? echo $row[csf('company_id')] . "," . $row[csf('id')]; ?>)"><? echo $row[csf('id')]; ?>
    							&nbsp;</a></td>
    							<td width="70" align="center"><? echo $reqsDataArr[$row[csf('id')]]['reqs_no']; ?>
    							&nbsp;</td>
    							<td width="80" align="center">
    							&nbsp;<? echo change_date_format($row[csf('program_date')]); ?></td>
    							<td width="80" align="center">
    							&nbsp;<? if ($row[csf('start_date')] != "0000-00-00") echo change_date_format($row[csf('start_date')]); ?></td>
    							<td width="80" align="center">
    							&nbsp;<? if ($row[csf('end_date')] != "0000-00-00") echo change_date_format($row[csf('end_date')]); ?></td>
    							<td width="120"><p><? echo $buyer_arr[$row[csf('customer')]]; ?></p></td>
    							<td width="80"><p><? echo $buyer_arr[$row[csf('customer_buyer')]]; ?></p></td>
    							<td width="90" align="center"><p><? echo $style_ref; ?></p></td>
                                <td width="130">
    							<div style="word-wrap:break-word; width:129px">
    							<? if ($type == 3) echo $sales_order_no;
    							else echo "<a href='##' onclick=\"generate_report(" . $row[csf('company_id')] . ",'" . $sale_booking_no . "','" . $sale_booking_no . "','" . $sales_order_no . "','" . $fReportId . "' )\">$sales_order_no</a>"; ?>
    							</div>
    							</td>
    							<td width="100" title="<? echo $booking_entry_form;?>"><p><? echo "<a href='##' onclick=\"generate_report(" . $row[csf('company_id')] . ",'" . $sale_booking_no . "','" . $sale_booking_no . "','" . $sales_order_no . "','" . $fbReportId . "' )\">$sale_booking_no</a>"; ?>&nbsp;</p></td>
    							<td width="80"><p><? echo $machine_no; ?>&nbsp;</p></td>
    							<td width="110"><p><? echo $machine_dia_gg; ?></p></td>
    							<td width="70"
    							align="center"><? echo number_format($row[csf('distribution_qnty')], 2); ?>
    							&nbsp;</td>
    							<td width="80"><p><? echo $knitting_program_status[$row[csf('status')]]; ?></p></td>
    							<td align="left" width="150"><? echo $row[csf('fabric_desc')];////correct upto ?></td>
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
    							<td align="right"
    							width="100"><? echo number_format($row[csf('program_qnty')], 2); ?></td>
    							<td align="right" width="100" >
                                    <? 
                                    $actual_yarn_issue_qnty = $yarn_issue_qnty-$yarnRtnQty;
                                    echo number_format($actual_yarn_issue_qnty, 2); ?>
                                </td>
    							<td align="right" width="100"><? echo number_format($yarnRtnQty, 2); ?></td>
    							<td align="right" width="100"><? echo number_format($yarn_issue_reject_qnty, 2); ?></td>
    							<td align="right" width="100">
                                    <? 
                                    $yarn_issue_bl_qnty = $row[csf('program_qnty')] - $actual_yarn_issue_qnty;
                                    echo number_format($yarn_issue_bl_qnty, 2); ?>
                                </td>
    							<td align="right" width="100">
    							<a  href='##' onClick="openmypage_popup('<? echo $row[csf('id')];?>','knitting_popup')"><? echo number_format($knitting_qnty,2,".","");?></a>
    							</td>
    							<td align="right" width="100"><? echo number_format($fabric_reject_qnty, 2); ?></td>
    							<td align="right" width="100"><? echo number_format($balance_qnty, 2); ?></td>
    							<td align="right" width="100"><a href="##" onClick="openmypage_popup('<? echo $row[csf('id')];?>','grey_purchase_delivery')">

    							<? echo number_format($deliveryStorQtyArr[$row[csf('id')]],2); $delv_store=$deliveryStorQtyArr[$row[csf('id')]];?>
    							</a>
    							</td>
    							<td align="right" width="100">
    							<a href="##" onClick="openmypage_popup('<? echo $row[csf('id')];?>','grey_receive_popup')"><? echo number_format($knitting_recv_qnty,2);?></a>
    							</td>
    							<td align="right" width="100"><? echo number_format($balance_recv_qnty, 2); ?></td>
    							<td align="center" width="80"><? echo $complete; ?></td>
    							<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
    						</tr>
    						<?
    						$sub_tot_program_qnty += $row[csf('program_qnty')];
    						$sub_tot_knitting_qnty += $knitting_qnty;
    						$sub_tot_balance += $balance_qnty;
    						$sub_tot_knitting_recv_qnty += $knitting_recv_qnty;
    						$sub_tot_recv_balance += $balance_recv_qnty;
    						$sub_yarn_issue_qnty += $actual_yarn_issue_qnty;
    						$sub_yarn_issue_rtn_qnty += $yarnRtnQty;
    						$sub_yarn_issue_rej_qnty += $yarn_issue_reject_qnty;
    						$sub_yarn_issue_bl_qnty += $yarn_issue_bl_qnty;
    						$sub_fab_rej_qnty += $fabric_reject_qnty;
    						$sub_delv_store_qnty += $delv_store;
    		
    						$tot_program_qnty += $row[csf('program_qnty')];
    						$tot_knitting_qnty += $knitting_qnty;
    						$tot_balance += $balance_qnty;
    						$tot_knitting_recv_qnty += $knitting_recv_qnty;
    						$tot_balance_recv_qnty += $balance_recv_qnty;
    						$tot_yarn_issue_qnty += $actual_yarn_issue_qnty;
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
        						<td align="right"><b><? echo number_format($sub_tot_program_qnty, 2, '.', ''); ?></b>
        						</td>
        						<td align="right"><b><? echo number_format($sub_yarn_issue_qnty, 2, '.', ''); ?></b>
        						<td align="right"><b><? echo number_format($sub_yarn_issue_rtn_qnty, 2, '.', ''); ?></b>
        						<td align="right"><b><? echo number_format($sub_yarn_issue_rej_qnty, 2, '.', ''); ?></b>
        						</td>
        						<td align="right"><b><? echo number_format($sub_yarn_issue_bl_qnty, 2, '.', ''); ?></b>
        						</td>
        						<td align="right"><b><? echo number_format($sub_tot_knitting_qnty, 2, '.', ''); ?></b>
        						<td align="right"><b><? echo number_format($sub_fab_rej_qnty, 2, '.', ''); ?></b>
        						</td>
        						<td align="right"><b><? echo number_format($sub_tot_balance, 2, '.', ''); ?></b></td>
        						<td align="right"><b><? echo number_format($sub_delv_store_qnty, 2, '.', ''); ?></b></td>
        						<td align="right">
        						<b><? echo number_format($sub_tot_knitting_recv_qnty, 2, '.', ''); ?></b></td>
        						<td align="right"><b><? echo number_format($sub_tot_recv_balance, 2, '.', ''); ?></b>
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
            				<th align="right"><? echo number_format($tot_yarn_issue_qnty, 2, '.', ''); ?></th>
            				<th align="right"><? echo number_format($tot_yarn_issue_rtn_qnty, 2, '.', ''); ?></th>
            				<th align="right"><? echo number_format($tot_yarn_issue_rej_qnty, 2, '.', ''); ?></th>
            				<th align="right"><? echo number_format($tot_yarn_issue_bl_qnty, 2, '.', ''); ?></th>
            				<th align="right"><? echo number_format($tot_knitting_qnty, 2, '.', ''); ?></th>
            				<th align="right"><? echo number_format($tot_fab_rej_qnty, 2, '.', ''); ?></th>
            				<th align="right"><? echo number_format($tot_balance, 2, '.', ''); ?></th>
            				<th align="right"><? echo number_format($tot_delv_store_qnty, 2, '.', ''); ?></th>
            				<th align="right"><? echo number_format($tot_knitting_recv_qnty, 2, '.', ''); ?></th>
            				<th align="right"><? echo number_format($tot_balance_recv_qnty, 2, '.', ''); ?></th>
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
    disconnect($con);
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
                id: ["value_receive_qnty_in", "value_receive_qnty_out", "value_receive_qnty_tot"],
                col: [7, 8, 9],
                operation: ["sum", "sum", "sum"],
                write_method: ["innerHTML", "innerHTML", "innerHTML"]
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
    <div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
        style="width:100px" class="formbutton"/></div>
        <fieldset style="width:1037px;">
            <div id="report_container">
                
                <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
                    <thead>
                        <th colspan="12"><b>Grey Receive Info</b></th>
                    </thead>
                    <thead>
                        <th width="30">SL</th>
                        <th width="115">Receive Id</th>
                        <th width="95">Receive Basis</th>
                        <th width="110">Product Details</th>
                        <th width="100">Booking / Program No</th>
                        <th width="60">Machine No</th>
                        <th width="75">Production Date</th>
                        <th width="80">Inhouse Production</th>
                        <th width="80">Outside Production</th>
                        <th width="80">Production Qnty</th>
                        <th width="70">Challan No</th>
                        <th>Kniting Com.</th>
                    </thead>
                </table>
                <div style="width:1038px; max-height:330px; overflow-y:scroll" id="scroll_body">
                    <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0"
                    id="tbl_list_search">
                    <?
                    $i = 1;
                    $total_receive_qnty = 0;
                    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
                    $supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
                    $product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');

                    $sql = "select * from (
                        select  a.receive_date, a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_company,a.knitting_source,  a.receive_basis, a.challan_no, sum(b.grey_receive_qnty) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b , ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d where a.id=b.mst_id and a.booking_id = d.id and c.id = d.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id = $program_id and a.company_id = $companyID and b.status_active=1 and b.is_deleted=0 group by a.receive_date,a.receive_basis,a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_source,  a.knitting_company, a.challan_no
                        union all
                        select a.receive_date, a.recv_number,c.booking_no, b.prod_id, b.machine_no_id,a.knitting_company, a.knitting_source,   a.receive_basis, a.challan_no,   sum(b.grey_receive_qnty) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b , ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d where a.id=b.mst_id  and b.program_no = d.id and c.id = d.mst_id and a.item_category=13 and a.entry_form=22 and a.receive_basis=11 and b.status_active=1 and b.is_deleted=0 and b.program_no in($program_id) and a.company_id = $companyID group by a.receive_date,a.receive_basis,a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_source, a.knitting_company, a.challan_no
                    ) order by receive_date
                    ";

                    $result = sql_select($sql);
                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        $total_receive_qnty += $row[csf('knitting_qnty')];
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>"
                            onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115" align="center"><? echo $row[csf('recv_number')]; ?></td>
                            <td width="95" align="center"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
                            <td width="110" align="center"><? echo $product_arr[$row[csf('prod_id')]]; ?></td>
                            <td width="100" align="center"><? echo $row[csf('booking_no')]; ?></td>
                            <td width="60" align="center"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
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
            <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="95">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="75" align="right">Total</th>
                    <th width="80" align="right"
                    id="value_receive_qnty_in"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
                    <th width="80" align="right"
                    id="value_receive_qnty_out"><? echo number_format($total_receive_qnty_out, 2, '.', ''); ?></th>
                    <th width="80" align="right"
                    id="value_receive_qnty_tot"><? echo number_format($total_receive_qnty, 2, '.', ''); ?></th>
                    <th width="70">&nbsp;</th>
                    <th>&nbsp;</th>
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
    <div style="width:750px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
        style="width:100px" class="formbutton"/></div>
    <fieldset style="width:740px; margin-left:2px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                <thead>
                    <th colspan="11"><b>Grey Delivery Info</b></th>
                </thead>
                <thead>
                    <th width="30">SL</th>
                    <th width="125">Receive Id</th>
                    <th width="150">Product Details</th>
                    <th width="75">Production Date</th>
                    <th width="80">Delivery Quantity</th>
                    <th>Kniting Com.</th>
                </thead>
            </table>
            <div style="width:740px; max-height:330px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                    <?
                    $i = 1;
                    $total_receive_qnty = 0;
                    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
                    $supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
                    $product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');

                    $sql = "select c.sys_number,c.knitting_company,c.knitting_source,c.delevery_date, a.booking_no, sum(b.current_delivery)  as quantity, b.product_id from pro_roll_details a,pro_grey_prod_delivery_dtls b, pro_grey_prod_delivery_mst c where a.mst_id=b.grey_sys_id and b.mst_id = c.id and a.barcode_no=b.barcode_num and a.entry_form=2 and a.receive_basis=2 and a.booking_without_order=0 and a.booking_no = '$program_id' and c.company_id = $companyID and b.entry_form = 56 and b.status_active=1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by c.sys_number,c.knitting_company,c.knitting_source,c.delevery_date, a.booking_no, b.product_id order by c.delevery_date";

                    $deliveryStorQtyArr = array();
                    foreach ($deliveryquantityArr as $row) {
                        $deliveryStorQtyArr[$row[csf('booking_no')]] += $row[csf('current_delivery')];
                    }

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
                            <td width="125"><p><? echo $row[csf('sys_number')]; ?></p></td>
                            <td width="150"><p><? echo $product_arr[$row[csf('product_id')]]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('delevery_date')]); ?></td>
                            <td align="right" width="80">
                                <?
                                echo number_format($row[csf('quantity')], 2, '.', '');
                                $total_receive_qnty_in += $row[csf('quantity')];
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
                        <th colspan="4" align="right">Total</th>
                        <th align="right"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
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
                        <th width="75">Production Date</th>
                        <th width="80">Inhouse Production</th>
                        <th width="80">Outside Production</th>
                        <th width="80">Production Qnty</th>
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
                        foreach($result_22 as $row_22)
                        {
                            $booking_id .= $row_22[csf('id')].",";
                        }

                        $booking_id =  chop($booking_id,',');
                        if($booking_id != ""){
                        $sql_extend = " union all 
                        select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.quantity) as quantity,a.booking_no 
                        from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c 
                        where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=22 and c.entry_form=22 and a.receive_basis in (9,11) 
                        and b.status_active=1 and b.is_deleted=0 and a.company_id = $companyID
                        and a.booking_id in ($booking_id) 
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
                        where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=2 and c.entry_form=2 and a.receive_basis=2 
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

if ($action == "print")
{
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
	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name'); 
    $yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by id, yarn_count","id","yarn_count");

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
	$book_dataArray = sql_select("SELECT A.BUYER_ID, B.BOOKING_NO, B.PO_BREAK_DOWN_ID AS PO_ID, B.JOB_NO, C.PO_NUMBER, D.STYLE_REF_NO FROM WO_BOOKING_MST A,WO_BOOKING_DTLS B, WO_PO_BREAK_DOWN C,WO_PO_DETAILS_MASTER D WHERE A.BOOKING_NO=B.BOOKING_NO AND B.PO_BREAK_DOWN_ID=C.ID AND C.JOB_NO_MST=D.JOB_NO AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND B.BOOKING_NO IN(SELECT SALES_BOOKING_NO FROM FABRIC_SALES_ORDER_MST WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PO_ID FROM PPL_PLANNING_ENTRY_PLAN_DTLS WHERE DTLS_ID IN(".$program_ids.") AND STATUS_ACTIVE=1 AND IS_DELETED=0))");
	foreach ($book_dataArray as $row)
	{
		$booking_array[$row['BOOKING_NO']]['booking_no'] = $row['BOOKING_NO'];
		$booking_array[$row['BOOKING_NO']]['po_id'] = $row['PO_ID'];
		$booking_array[$row['BOOKING_NO']]['buyer_id'] = $row['BUYER_ID'];
		$booking_array[$row['BOOKING_NO']]['po_no'] = $row['PO_NUMBER'];
		$booking_array[$row['BOOKING_NO']]['job_no'] = $row['JOB_NO'];
		$booking_array[$row['BOOKING_NO']]['style_ref_no'] = $row['STYLE_REF_NO'];
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
	$dataArray = sql_select("SELECT A.ID, A.KNITTING_SOURCE, A.KNITTING_PARTY, B.BUYER_ID, B.BOOKING_NO, B.COMPANY_ID, LISTAGG(CAST(B.PO_ID AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY B.PO_ID) AS PO_ID, A.IS_SALES, B.WITHIN_GROUP FROM PPL_PLANNING_INFO_ENTRY_DTLS A, PPL_PLANNING_ENTRY_PLAN_DTLS B WHERE A.ID=B.DTLS_ID AND A.ID IN (".$program_ids.") AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 GROUP BY A.ID, A.KNITTING_SOURCE, A.KNITTING_PARTY, B.BUYER_ID, B.BOOKING_NO, B.COMPANY_ID,A.IS_SALES,B.WITHIN_GROUP");
	$k_source = "";
	$sup = $sales_ids = "";
	foreach ($dataArray as $row)
	{
		if ($duplicate_arr[$row['KNITTING_SOURCE']][$row['KNITTING_PARTY']] == "")
		{
			$duplicate_arr[$row['KNITTING_SOURCE']][$row['KNITTING_PARTY']] = $row['KNITTING_PARTY'];
		}
		
		if ($row['KNITTING_SOURCE'] == 1)
		{
			$knitting_factory .= $company_details[$row['KNITTING_PARTY']] . ",";
		}
		else if ($row['KNITTING_SOURCE'] == 3)
		{
			$knitting_factory .= $supplier_details[$row['KNITTING_PARTY']] . ",";
		}
        $knitting_factory=implode(",",array_unique(explode(",",$knitting_factory)));

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
    <div style="width:1200px; margin-left:5px;">
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
            <td width="100%" align="center" style="font-size:20px;"><b><u>Knitting Program</u></b></td>
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
    <th width="50">Program No</th>
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
$company_id = '';
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
    <th colspan="19" align="right"><b>Total</b></th>
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
if (count($sql_collarCuff) > 0) {
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
<div style="float:left; border:1px solid #000;">
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
<?
exit();
}

if ($action == "requisition_print_two_05092021")
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
	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name'); 
    $yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by id, yarn_count","id","yarn_count");

	$po_dataArray = sql_select("select id,job_no,buyer_id,style_ref_no,within_group,sales_booking_no,booking_without_order from fabric_sales_order_mst where status_active=1 and is_deleted=0");
	foreach ($po_dataArray as $row) {
        $sales_array[$row[csf('id')]]['no'] = $row[csf('job_no')];
		$sales_array[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
		$sales_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
		$sales_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$sales_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
		$sales_array[$row[csf('id')]]['booking_without_order'] = $row[csf('booking_without_order')];
	}

	$book_dataArray = sql_select("select a.buyer_id,b.booking_no,b.po_break_down_id as po_id,b.job_no,c.po_number,d.style_ref_no from wo_booking_mst a,wo_booking_dtls b, wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	foreach ($book_dataArray as $row) {
		$booking_array[$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];
		$booking_array[$row[csf('booking_no')]]['po_id'] = $row[csf('po_id')];
		$booking_array[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
		$booking_array[$row[csf('booking_no')]]['po_no'] = $row[csf('po_number')];
		$booking_array[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
		$booking_array[$row[csf('booking_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
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
	$reqsn_dataArray = sql_select("select knit_id, requisition_no,requisition_date,prod_id,sum(no_of_cone) as no_of_cone , sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where knit_id in($program_ids) and status_active=1 and is_deleted=0 group by knit_id, prod_id, requisition_no,requisition_date");
	foreach ($reqsn_dataArray as $row) {
		$prod_id_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('yarn_qnty')];
		$knit_id_array[$row[csf('knit_id')]] .= $row[csf('prod_id')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsn'] .= $row[csf('requisition_no')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsd'] .= $row[csf('requisition_date')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['qnty'] += $row[csf('yarn_qnty')];
		$rqsn_array[$row[csf('prod_id')]]['no_of_cone'] += $row[csf('no_of_cone')];
	}

	$sales_order_no = '';
	$buyer_name = '';
	$knitting_factory = '';
    $booking_no = '';
	$wg_yes_booking = '';
	$company = '';
	$order_buyer = '';
	$style_ref_no = '';
	if ($db_type == 0) {
		$dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id, group_concat(distinct(b.po_id)) as po_id,a.is_sales from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id,a.is_sales");
	} else {
		$dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id, LISTAGG(cast(b.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_id) as po_id,a.is_sales,b.within_group from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id,a.is_sales,b.within_group");
	}

	$k_source = "";
	$sup = $sales_ids = "";
	foreach ($dataArray as $row) {
		if ($duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] == "") {
			$duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] = $row[csf('knitting_party')];
		}
		if ($row[csf('knitting_source')] == 1) {
			$knitting_factory .= $company_details[$row[csf('knitting_party')]] . ",";
		} else if ($row[csf('knitting_source')] == 3) {
			$knitting_factory .= $supplier_details[$row[csf('knitting_party')]] . ",";
		}
        $knitting_factory=implode(",",array_unique(explode(",",$knitting_factory)));

		if ($buyer_name == "") {
			$buyer_name = $buyer_arr[$row[csf('buyer_id')]];
		}
		if ($booking_no != '') {
			$booking_no .= "," . $row[csf('booking_no')];
		} else {
			$booking_no = $row[csf('booking_no')];
		}

		if ($company == "") {
			$company = $company_details[$row[csf('company_id')]];
		}
		if ($company_id == "") {
			$company_id = $row[csf('company_id')];
		}
        $order_nos .= "," . $booking_array2[$row[csf('booking_no')]]['po_no'];
		$is_sales = $row[csf('is_sales')];
		$sales_ids .= $row[csf('po_id')] . ",";
		$k_source = $row[csf('knitting_source')];
		$sup = $row[csf('knitting_party')];
	}
    $sales_id = array_unique(explode(",", $sales_ids));
    $booking_nos = array_unique(explode(",", $booking_no));

    $order_buyer=$style_ref_no=$job_no=$order_nos="";
	foreach ($sales_id as $pid) {
		$sales_order_no .= $sales_array[$pid]['no'] . ","; 
        if ($sales_array[$pid]['within_group'] == 2) {
            $order_buyer .= $buyer_arr[$sales_array[$pid]['buyer_id']] . ",";
            $style_ref_no .= "," . $sales_array[$pid]['style_ref_no'];
            $job_no .= "";
            $order_ids .= "";
        }else{
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
        }
	}
    $sales_nos = rtrim(implode(",", array_unique(explode(",", $sales_order_no))), ",");
	$order_buyers = rtrim(implode(",", array_unique(explode(",", $order_buyer))), ",");
	$style_ref_nos = ltrim(implode(",", array_unique(explode(",", $style_ref_no))), ",");
	$job_nos = implode(",", array_unique(explode(",", rtrim($job_no,","))));
	$booking_noss = implode(",", $booking_nos);

    if($program_ids!="")
    {
        $feedingResult =  sql_select("SELECT dtls_id, seq_no, count_id, feeding_id FROM ppl_planning_count_feed_dtls WHERE dtls_id in($program_ids) and status_active=1 and is_deleted=0");

        $feedingDataArr = array();
        foreach ($feedingResult as $row) {
            $feedingSequence[$row[csf('seq_no')]] =  $row[csf('seq_no')];
            $feedingDataArr[$row[csf('dtls_id')]][$row[csf('seq_no')]]['count_id'] = $row[csf('count_id')];
            $feedingDataArr[$row[csf('dtls_id')]][$row[csf('seq_no')]]['feeding_id'] = $row[csf('feeding_id')];  
        }
    }
	?>
    <div style="width:1200px; margin-left:5px;">

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
            <td width="100%" align="center" style="font-size:20px;"><b><u>Knitting Program</u></b></td>
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
                <td width="140" style="font-size:18px"><b>Attention </b></td>
                <td>:</td>
                <?
                if ($typeForAttention == 1) {
                  echo "<td style=\"font-size:18px; font-weight:bold;\"> Knitting Manager </td>";
              } else {
                  ?>
                  <td style="font-size:18px; font-weight:bold;"><b><?
                    if ($k_source == 3) {
                     $ComArray = sql_select("select id,contact_person from lib_supplier where id=$sup");
                     foreach ($ComArray as $row) {
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
    <th width="50">Program No</th>
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
$company_id = '';
$sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks,b.advice,b.batch_no,b.tube_ref_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
//echo $sql;
$nameArray = sql_select($sql);

$advice = "";
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
    if ($machine_id[0] != "") {
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
} else {
 ?>
 <tr bgcolor="<? echo $bgcolor; ?>">
    <td width="25"><? echo $i; ?></td>
    <td width="60" align="center" style="font-size:16px;"><b><? echo $row[csf('program_id')]; ?></b></td>
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
    <th colspan="19" align="right"><b>Total</b></th>
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
if (count($sql_collarCuff) > 0) {
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
?>
<table border="1" rules="all" class="rpt_table">
    <tr>
        <td style="font-size:24px; font-weight:bold; width:20px;">ADVICE:</td>
        <td style="font-size:20px; width:100%;">     <? echo $advice; ?></td>
    </tr>
</table>
<div style="margin-top:60px; text-align: left;"><strong>Rate/Kg =</strong></div>
<br/>
<div style="float:left; border:1px solid #000;">
    <table border="1" rules="all" class="rpt_table" width="400" height="200">
        <thead>
            <th colspan="2" style="font-size:20px; font-weight:bold;">Please Strictly Avoid The Following Faults….
            </th>
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

                        </table>
                    </div>
                    <?
		echo signature_table(213, $company_id, "1180px");
		?>
    </div>
    <?
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
	$brand_arr 		= return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$company_arr 	= return_library_array("select id,company_name from lib_company", "id", "company_name");
	$imge_arr		= return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$count_arr 		= return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 ", 'id', 'yarn_count');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

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
	
	$data_sql="select a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id";
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

	foreach($ex_mc_id as $mc_id)
	{
		// program array loop
		foreach($program_data_arr as $prog_no=>$prog_data)
		{

		?>
		<style type="text/css">
			.page_break	{ page-break-after: always;
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
		<div style="width:555px;">
			<!--<table width="100%" cellpadding="0" cellspacing="0">-->
            <table width="100%" cellspacing="2" cellpadding="2" border="1" rules="all" class="rpt_table">
				<tr>
					<td width="100" align="right" style="border-left:hidden; border-top:hidden; border-right:hidden; border-bottom:hidden;"> 
						<img src='../../<? echo $imge_arr[str_replace("'","",$prog_data['company_id'])]; ?>' height='100%' width='100%' alt="not found" />
					</td>
					<td colspan="2" width="455" align="center" valign="middle" style="font-size:16px;border-left:hidden; border-top:hidden; border-right:hidden; border-bottom:hidden;"><b><? echo $company_arr[$prog_data['company_id']]; ?></b></td>
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
			</table>
			<div style="margin-top:10px; width:555px; font-size:14px;">Note: This is Software Generated Copy, Signature is not Required.</div>
			<div class="page_break">&nbsp;</div>
		</div>
		<?
		}
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
	$brand_arr 		= return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$company_arr 	= return_library_array("select id,company_name from lib_company", "id", "company_name");
	$imge_arr		= return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$count_arr 		= return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 ", 'id', 'yarn_count');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

    $floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name'); 


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
			$jobNo		= $row[csf('job_no_mst')];
			$poQuantity	= $row[csf('poQuantity')];
			$style 		= $row[csf('style_ref_no')];
			$po_details[$row[csf('id')]]['po_number']= $row[csf('po_number')];
			$ref_no 	= $row[csf('grouping')];
			$order_nature=$order_nature_booking_arr[$row[csf('booking_no')]];
		}
		unset($job_data_sql);
		*/
		
		$data_sql="SELECT a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, a.dye_type, b.buyer_id, b.customer_buyer, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.after_wash_gsm, b.po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in (".$program_ids.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, a.dye_type, b.buyer_id, b.customer_buyer, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.after_wash_gsm, b.po_id";
		//, b.yarn_desc
		$dataArray = sql_select($data_sql);
		$sales_id_arr = array();
        $machine_ids="";
		foreach ($dataArray as $row)
		{
			$sales_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
            $machine_ids .= $row[csf('machine_id')].",";
		}

        //fiend floor id/name
        $machine_ids=chop($machine_ids,",");
        $machine_floor_arr = return_library_array("select id, floor_id from  lib_machine_name where id in ($machine_ids)", 'id', 'floor_id');
        

		
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
			$program_data_arr[$row[csf('id')]]['after_wash_gsm']=$row[csf('after_wash_gsm')];
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
				.page_break	{ page-break-after: always;
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
                                        //$program_array[$barcode_sl] = $prog_no."-".$prog_data['knitting_party']."-".$mc_id."-".$prog_data['location_id'];
                                        $program_array[$barcode_sl] = $prog_no."/".$mc_id;
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
                                <td width="132" class="font_size_define">
                                    <? 
                                    echo $machine_arr[$mc_id].' '.'['.$floor_arr[$machine_floor_arr[$mc_id]].']';
                                    ?>
                                </td>
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
                                <td class="font_size_define">A/W GSM</td>
                                <td class="font_size_define"><? echo $prog_data['after_wash_gsm']; ?></td>
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
                                <td class="font_size_define">M/C Target QTY</td>
                                <td class="font_size_define"><? echo $machin_prog[$mc_id][$prog_no]['distribution_qnty']; ?></td>
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

                            <tr><td class="font_size_define"><? echo signature_table(119, $prog_data['company_id'], "1180px","","1"); ?> </td></tr>

						</tbody>
					</table>
				</div>

				<? //echo signature_table(119, $prog_data['company_id'], "1180px","","2"); ?>
                
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
?>