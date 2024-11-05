<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
	header("location:login.php");

require_once('../../../includes/common.php');
$user_name = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

function get_users_buyer()
{
	$byr_str = '';
	if ($_SESSION['logic_erp']['data_level_secured'] == 1)
	{
		if ($_SESSION['logic_erp']['buyer_id'] != '')
		{
			$byr_str = $_SESSION['logic_erp']['buyer_id'];
		}
	}
	return $byr_str;
}

/*
|------------------------------------------------------------------------
| for company_wise_report_button_setting
|------------------------------------------------------------------------
*/
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

/*
|------------------------------------------------------------------------
| for load_drop_down_buyer
|------------------------------------------------------------------------
*/
if ($action == "load_drop_down_buyer")
{
	echo create_drop_down("cbo_buyer_id", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "- All Buyer -", $selected, "");
	exit();
}

/*
|------------------------------------------------------------------------
| for load_drop_down_party_type
|------------------------------------------------------------------------
*/
if ($action == "load_drop_down_party_type")
{
	$explode_data = explode("**", $data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];
	if ($data == 3)
	{
		echo create_drop_down("cbo_party_type", 110, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$selected_company' and b.party_type =20 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "--- Select ---", $selected, "");
	}
	else if ($data == 1)
	{
		echo create_drop_down("cbo_party_type", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--- Select ---", $selected_company, "", 0, 0);
	}
	else
	{
		echo create_drop_down("cbo_party_type", 110, $blank_array, "", 1, "--- Select ---", $selected, "", 1);
	}
	exit();
}

$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");

/*
|------------------------------------------------------------------------
| for job_no_search_popup
|------------------------------------------------------------------------
*/
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
                        onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value, 'create_job_no_search_list_view', 'search_div', 'knitting_summary_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
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

/*
|------------------------------------------------------------------------
| for create_job_no_search_list_view
|------------------------------------------------------------------------
*/
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

/*
|------------------------------------------------------------------------
| for machine_no_search_popup
|------------------------------------------------------------------------
*/
if ($action == "machine_no_search_popup")
{
	echo load_html_head_contents("Machine Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        function js_set_value(str)
		{
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

/*
|------------------------------------------------------------------------
| for booking_no_search_popup
|------------------------------------------------------------------------
*/
if ($action == "booking_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        function js_set_value(str)
		{
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
            show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+ '**' + document.getElementById('cbo_year_selection').value + '**' + document.getElementById('cbo_within_group').value, 'create_order_no_search_list_view', 'search_div', 'knitting_summary_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
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
                           $search_by_arr = array(4 => "Booking No", 3 => "Job No", 1 => "Order No", 2 => "Style Ref", 5 => "IR/IB No");
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

/*
|------------------------------------------------------------------------
| for create_order_no_search_list_view
|------------------------------------------------------------------------
*/
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
	else if ($search_by == 5)
		$search_field = "c.grouping";
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
  	$array_booking_type= array(2=>"Main", 1=> "Short");
	$arr = array(0 => $company_library, 1 => $buyer_arr, 7=>$array_booking_type);


	if ($db_type == 0)
		$year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2)
		$year_field = "to_char(a.insert_date,'YYYY') as year";
	else
		$year_field = ""; //defined Later


    if($within_group==1){
        $sql = "SELECT a.id,a.booking_no, d.job_no, $year_field,a.company_id,a.buyer_id,a.booking_date,c.po_number,d.style_ref_no, a.is_short  from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d, fabric_sales_order_mst e where  c.job_no_mst=d.job_no and b.po_break_down_id=c.id and b.job_no=d.job_no and a.booking_no=b.booking_no and a.booking_no=e.sales_booking_no and e.within_group=1 and a.status_active=1 and a.is_deleted=0 and e.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $year_cond group by a.id,a.booking_no, d.job_no,a.company_id,a.buyer_id,a.insert_date,a.booking_date,c.po_number,d.style_ref_no, a.is_short
        order by a.booking_no, a.booking_date";
    }
    else
    {
        $sql ="SELECT null as id,a.sales_booking_no as booking_no, null as job_no, to_char(a.insert_date,'YYYY') as year,a.company_id,a.buyer_id, a.booking_date,null as po_number,a.style_ref_no from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  and a.within_group=2 and a.sales_booking_no like '$search_string'  $buyer_id_cond $date_cond $year_cond2 group by a.sales_booking_no, a.insert_date, a.company_id,a.buyer_id, a.booking_date, a.style_ref_no order by a.sales_booking_no, a.booking_date";
    }
	//echo $sql;

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Booking No,Job No,Style,PO No,Booking Date, booking Type", "120,100,100,100,100,100,100,100", "880", "220", 0, $sql, "js_set_value", "booking_no", "", 1, "company_id,buyer_id,0,0,0,0,0,is_short", $arr, "company_id,buyer_id,booking_no,job_no,style_ref_no,po_number,booking_date,is_short", "", '', '0,0,0,0,0,0,3,0', '', 1);
	exit();
}

/*
|------------------------------------------------------------------------
| for req_qty_popup
|------------------------------------------------------------------------
*/
if ($action == "req_qty_popup")
{
	echo load_html_head_contents("Rquisition Details", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);

	$sql = "SELECT A.KNIT_ID, A.REQUISITION_NO, A.YARN_QNTY FROM PPL_YARN_REQUISITION_ENTRY A WHERE A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.ID IN(".$req_id.")";
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

/*
|------------------------------------------------------------------------
| for report_generate
|------------------------------------------------------------------------
*/
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
		$companyId = str_replace("'", "", $cbo_company_name);

		$yarn_count_dtls = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", "id", "yarn_count");

		//for po company
		$buyer_id_cond = '';
        if (str_replace("'", "", $cbo_buyer_name) == 0)
        {
			$byr_id = get_users_buyer();
			if($byr_id != '')
			{
				$buyer_id_cond = " AND A.BUYER_ID IN (".$byr_id.")";
			}
		}
		else
		{
			$buyer_id_cond = " AND A.BUYER_ID IN (".$cbo_buyer_name.")";

		}


		if (str_replace("'", "", $cbo_party_type) == 0)
			$party_type = "%%";
		else
			$party_type = str_replace("'", "", $cbo_party_type);

		$year_cond = "";

        if ($cbo_buyer_id != 0)
		{
			$poBuyerCond = " AND A.BUYER_ID = ".$cbo_buyer_id;
			$po_buyer_cond = " AND E.PO_BUYER = ".$cbo_buyer_id;
		}
		else
		{
			$poBuyerCond = "";
			$po_buyer_cond = "";
		}

       	//echo $po_buyer_cond;

		$cbo_year = str_replace("'", "", $cbo_year);
		$year_cond = "";
		if (trim($cbo_year) != 0)
		{
			$year_cond = " AND TO_CHAR(E.INSERT_DATE,'YYYY') = ".$cbo_year;
		}

		$sales_no_cond = "";
		if (str_replace("'", "", $txt_sales_no) != "")
		{
			$chk_prefix_sales_no=explode("-",str_replace("'", "", $txt_sales_no));
			if($chk_prefix_sales_no[3]!="")
			{
				$sales_number = "%" . trim(str_replace("'", "", $txt_sales_no));
			  	$sales_no_cond = " AND E.JOB_NO LIKE '".$sales_number."'";
			}
			else
			{
				$sales_number = trim(str_replace("'", "", $txt_sales_no));
				$sales_no_cond = " AND E.JOB_NO_PREFIX_NUM = '".$sales_number."'";
			}
		}

		$booking_search_cond = "";
		if (str_replace("'", "", trim($txt_booking_no)) != "")
		{
			$booking_number = "%" . trim(str_replace("'", "", $txt_booking_no)) . "%";
			$booking_search_cond = "  AND E.SALES_BOOKING_NO LIKE '$booking_number'";
		}

		$year_field = "TO_CHAR(E.INSERT_DATE,'YYYY') AS YEAR";

		//for program date
		if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "")
		{
			if ($based_on == 2)
			{
				$date_cond = " AND B.PROGRAM_DATE BETWEEN " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
			}
			else
			{
				$date_cond = " AND B.START_DATE BETWEEN " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
			}
		}
		else
		{
			$date_cond = "";
		}

		if ($cbo_buyer_id != 0)
		{
            $buyer_cond = " AND E.PO_BUYER = ".$cbo_buyer_id;
		}

		if (str_replace("'", "", $txt_machine_dia) == "")
			$machine_dia = "%%";
		else
			$machine_dia = "%" . str_replace("'", "", $txt_machine_dia) . "%";

		if (str_replace("'", "", $txt_program_no) == "")
			$program_no = "%%";
		else
			$program_no = str_replace("'", "", $txt_program_no);

		if ($type > 0)
			$knitting_source_cond = " AND B.KNITTING_SOURCE = ".$type;
		else
			$knitting_source_cond = "";

		$status_cond = "";
		if ($cbo_knitting_status != "")
			$status_cond = " AND B.STATUS IN(".$cbo_knitting_status.")";

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

			$sql = "SELECT
			A.BOOKING_NO,
			B.ID, B.START_DATE, B.END_DATE, B.COLOR_ID, B.KNITTING_SOURCE, B.KNITTING_PARTY, B.MACHINE_DIA, B.FABRIC_DIA,
			C.ID AS DTLS_ID, C.PO_ID, C.GSM_WEIGHT, C.FABRIC_DESC, C.DETERMINATION_ID, C.YARN_DESC, C.PROGRAM_QNTY ,
			E.BUYER_ID, E.WITHIN_GROUP, E.STYLE_REF_NO, E.JOB_NO, E.BOOKING_ENTRY_FORM, E.BOOKING_ID,
			F.ID AS SALES_ID, F.GREY_QTY
			FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C, FABRIC_SALES_ORDER_MST E, FABRIC_SALES_ORDER_DTLS F, PPL_ENTRY_MACHINE_DATEWISE G
 			WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND C.PO_ID = E.ID AND E.ID = F.MST_ID AND C.PO_ID = F.MST_ID AND B.ID=G.DTLS_ID AND C.IS_SALES = 1 AND G.MACHINE_ID IN(".$machine_id_ref.") AND A.COMPANY_ID = ".$company_name."  AND B.KNITTING_PARTY LIKE '".$party_type."' AND B.MACHINE_DIA LIKE '".$machine_dia."' AND B.ID LIKE '".$program_no."' AND A.IS_SALES=1 AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND F.IS_DELETED=0 AND F.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND C.STATUS_ACTIVE=1 ".$buyer_id_cond.$po_buyer_cond.$sales_no_cond.$year_cond.$booking_search_cond.$date_cond.$status_cond.$knitting_source_cond."
			ORDER BY B.ID, A.BOOKING_NO";
		}
		else
		{
			$sql = "SELECT
			A.BOOKING_NO,
			B.ID, B.START_DATE, B.END_DATE, B.COLOR_ID, B.KNITTING_SOURCE, B.KNITTING_PARTY, B.MACHINE_DIA, B.FABRIC_DIA,
			C.ID AS DTLS_ID, C.PO_ID, C.GSM_WEIGHT, C.FABRIC_DESC, C.DETERMINATION_ID, C.YARN_DESC, C.PROGRAM_QNTY ,
			E.BUYER_ID, E.WITHIN_GROUP, E.STYLE_REF_NO, E.JOB_NO, E.BOOKING_ENTRY_FORM, E.BOOKING_ID,
			F.ID AS SALES_ID, F.GREY_QTY, B.MACHINE_GG, E.PO_JOB_NO
			FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C, FABRIC_SALES_ORDER_MST E, FABRIC_SALES_ORDER_DTLS F
			WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND C.PO_ID = E.ID AND E.ID = F.MST_ID AND C.PO_ID = F.MST_ID AND C.IS_SALES = 1  AND A.COMPANY_ID = ".$company_name."  AND B.KNITTING_PARTY LIKE '".$party_type."' AND B.MACHINE_DIA LIKE '".$machine_dia."' AND B.ID LIKE '".$program_no."' AND A.IS_SALES=1 AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND F.IS_DELETED=0 AND F.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND C.STATUS_ACTIVE=1 ".$buyer_id_cond.$po_buyer_cond.$sales_no_cond.$year_cond.$booking_search_cond.$date_cond.$status_cond.$knitting_source_cond."
			ORDER BY B.ID, A.BOOKING_NO";
		}

		//echo $sql;
		//die;
		$nameArray = sql_select($sql);
		if(empty($nameArray))
		{
			echo get_empty_data_msg();
			die;
		}

        if(!empty($nameArray))
        {
            $con = connect();
            $r_id3=execute_query("delete from tmp_prog_no where userid=$user_name");

            if($r_id3 && $r_id4 && $r_id5 && $r_id6 && $r_id7)
            {
                oci_commit($con);
            }
        }

		$planIdArr = array();
		$prog_id_check = array();
		foreach($nameArray as $row)
		{
			$planIdArr[$row['ID']] = $row['ID'];
            if(!$prog_id_check[$row['ID']])
            {
                $prog_id_check[$row['ID']]=$row['ID'];
                $ProgNO = $row['ID'];
                $rID3=execute_query("insert into tmp_prog_no (userid, prog_no) values ($user_name, $ProgNO)");
            }

            if($rID3)
            {
                oci_commit($con);
            }
		}

		//for issue
		$knit_issue_arr=array();



		$sql_data = sql_select("SELECT B.ID AS TRANS_ID, C.KNIT_ID AS PROGRAM_NO, B.CONS_QUANTITY, A.ID AS ISSUE_ID, D.LOT, D.YARN_COUNT_ID FROM INV_ISSUE_MASTER A, INV_TRANSACTION B, PPL_YARN_REQUISITION_ENTRY C, PRODUCT_DETAILS_MASTER D, TMP_PROG_NO E WHERE A.ID = B.MST_ID AND B.REQUISITION_NO = C.REQUISITION_NO AND B.PROD_ID = D.ID AND C.PROD_ID = D.ID AND C.KNIT_ID = E.PROG_NO AND E.USERID = ".$user_name." AND B.RECEIVE_BASIS in(3,8) AND A.ISSUE_BASIS in(3,8) AND A.ENTRY_FORM = 3 AND A.COMPANY_ID = ".$company_name." AND B.ITEM_CATEGORY = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0");
		$transId_chk = array();
		foreach($sql_data as $row)
		{
			if($transId_chk[$row['TRANS_ID']] == "")
			{
				$transId_chk[$row['TRANS_ID']] = $row['TRANS_ID'];
				$knit_issue_arr[$row['PROGRAM_NO']]['qnty'] += $row['CONS_QUANTITY'];
				$knit_issue_arr[$row['PROGRAM_NO']]['lot'][$row['LOT']] = $row['LOT'];
				$knit_issue_arr[$row['PROGRAM_NO']]['issue_id'] .= $row['ISSUE_ID'].",";
				$knit_issue_arr[$row['PROGRAM_NO']]['trans_id'] .= $row['TRANS_ID'].",";
				$knit_issue_arr[$row['PROGRAM_NO']]['yarn_count'][$row['YARN_COUNT_ID']] .= $yarn_count_dtls[$row['YARN_COUNT_ID']].",";
			}
		}
		unset( $sql_data);
		//end for issue

		//for issue return
		$knit_issue_return =array();


		$sql_iss_return = sql_select("SELECT B.ID AS TRANS_ID, A.ID, B.CONS_QUANTITY AS ISS_RETURN_QTY, A.BOOKING_ID, C.KNIT_ID FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, PPL_YARN_REQUISITION_ENTRY C, TMP_PROG_NO D WHERE A.ID = B.MST_ID AND A.BOOKING_ID = C.REQUISITION_NO AND C.KNIT_ID = D.PROG_NO AND D.USERID = ".$user_name." AND A.ITEM_CATEGORY = 1 AND B.TRANSACTION_TYPE = 4 AND A.ENTRY_FORM = 9 AND B.COMPANY_ID = ".$company_name." AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND A.IS_DELETED = 0 AND A.STATUS_ACTIVE = 1 AND A.RECEIVE_BASIS = 3
		UNION ALL
		SELECT B.ID AS TRANS_ID, A.ID, B.CONS_QUANTITY AS ISS_RETURN_QTY, A.BOOKING_ID, C.KNIT_ID FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, PPL_YARN_REQUISITION_ENTRY C, TMP_PROG_NO D WHERE A.ID = B.MST_ID AND A.REQUISITION_NO = C.REQUISITION_NO AND C.KNIT_ID = D.PROG_NO AND D.USERID = ".$user_name." AND A.ITEM_CATEGORY = 1 AND B.TRANSACTION_TYPE = 4 AND A.ENTRY_FORM = 9 AND B.COMPANY_ID = ".$company_name." AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND A.IS_DELETED = 0 AND A.STATUS_ACTIVE = 1 AND A.RECEIVE_BASIS = 8");
		$transId_chk = array();
		foreach($sql_iss_return as $row)
		{
			if($transId_chk[$row['TRANS_ID']] == "")
			{
				$transId_chk[$row['TRANS_ID']] = $row['TRANS_ID'];
				$knit_issue_return_arr[$row['KNIT_ID']]['ret_qnty'] += $row['ISS_RETURN_QTY'];
				$knit_issue_return_arr[$row['KNIT_ID']]['ids'] .= $row['ID'].",";
			}
		}
		unset( $sql_iss_return);
		//end for issue return


        $sql_prod=sql_select("SELECT A.ID, A.BOOKING_ID, B.ID AS PRO_ID, B.GREY_RECEIVE_QNTY, B.TRANS_ID, B.ORDER_ID FROM INV_RECEIVE_MASTER A, PRO_GREY_PROD_ENTRY_DTLS B, TMP_PROG_NO C WHERE A.ID=B.MST_ID AND A.BOOKING_ID=C.PROG_NO AND C.USERID = ".$user_name." AND A.ITEM_CATEGORY=13 AND A.ENTRY_FORM=2 AND A.RECEIVE_BASIS=2 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0");
        $knitting_recv_qnty_array=array();
		$duplicate_check = array();
		$duplicate_booking_check = array();
		$booking_array = array();
        foreach($sql_prod as $row)
        {
			if($duplicate_check[$row['PRO_ID']] != $row['PRO_ID'])
			{
				$duplicate_check[$row['PRO_ID']] = $row['PRO_ID'];
				$knitting_recv_qnty_array[$row['BOOKING_ID']] += $row['GREY_RECEIVE_QNTY'];
				//$knitting_order_array[$row['BOOKING_ID']] = $row['ORDER_ID'];
			}

			if($duplicate_booking_check[$row['BOOKING_ID']] != $row['BOOKING_ID'])
			{
				$duplicate_booking_check[$row['BOOKING_ID']] = $row['BOOKING_ID'];
				array_push($booking_array,$row['BOOKING_ID']);
			}
        }
		unset( $sql_prod);

		//for grey receive qty

		/* $sql= "SELECT A.BOOKING_ID, B.ID, B.GREY_RECEIVE_QNTY FROM INV_RECEIVE_MASTER A, PRO_GREY_PROD_ENTRY_DTLS B, TMP_PROG_NO C WHERE A.ID=B.MST_ID AND A.BOOKING_ID=C.PROG_NO AND C.USERID = ".$user_name." AND A.RECEIVE_BASIS=2 AND A.ENTRY_FORM=2 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0";
		$sql_rslt=sql_select($sql);
		$duplicate_check = array();
		foreach($sql_rslt as $row)
		{
			if($duplicate_check[$row['ID']] != $row['ID'])
			{
				$duplicate_check[$row['ID']] = $row['ID'];
				$knitProQtyArr[$row['BOOKING_ID']] += $row['GREY_RECEIVE_QNTY'];
			}
		} */
		// $sql= "SELECT A.BOOKING_ID, B.ID, B.GREY_RECEIVE_QNTY, B.ORDER_ID FROM INV_RECEIVE_MASTER A, PRO_GREY_PROD_ENTRY_DTLS B WHERE A.ID=B.MST_ID  AND A.ENTRY_FORM=58 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 ".where_con_using_array($order_array,1,'B.ORDER_ID')." ";
		// //echo $sql;
		// $sql_rslt=sql_select($sql);
		// $duplicate_check = array();
		// foreach($sql_rslt as $row)
		// {
		// 	if($duplicate_check[$row['ID']] != $row['ID'])
		// 	{
		// 		$duplicate_check[$row['ID']] = $row['ID'];
		// 		$knitProQtyArr[$row['ORDER_ID']] += $row['GREY_RECEIVE_QNTY'];
		// 	}
		// }

		$sql =  "SELECT A.BOOKING_NO,B.BOOKING_ID, SUM(A.QNTY) QNTY FROM PRO_ROLL_DETAILS A, INV_RECEIVE_MASTER B WHERE A.ENTRY_FORM = 58 AND A.MST_ID = B.ID AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 ".where_con_using_array($booking_array,1,'A.BOOKING_NO')."  GROUP BY A.BOOKING_NO,B.BOOKING_ID";

		//echo $sql;die;
		$sql_rslt=sql_select($sql);
		$duplicate_check = array();
		foreach($sql_rslt as $row)
		{
			$knitProQtyArr[$row['BOOKING_NO']] += $row['QNTY'];

		}

		// echo "<pre>";
		// print_r($knitProQtyArr);
		// echo "</pre>";

        $r_id3=execute_query("delete from tmp_prog_no where userid=$user_name");
        if($r_id3) $flag=1; else $flag=0;

        if($flag==1)
        {
            oci_commit($con);
        }

		$print_data = array();
		$sales_booking_qty = array();
		$sales_prog_qty = array();
		$duplicate_check = array();
		$duplicate_check2 = array();
		$po_job_no_arr = array();
		foreach ($nameArray as $row)
		{
			if($duplicate_check[$row['DTLS_ID']] != $row['DTLS_ID'])
			{
				$duplicate_check[$row['DTLS_ID']] = $row['DTLS_ID'];

				//for factory name
				if ($row['KNITTING_SOURCE'] == 1)
				{
					$row['FACTORY_NAME'] = $company_library[$row['KNITTING_PARTY']];
				}
				else
				{
					$row['FACTORY_NAME'] = $supplier_details[$row['KNITTING_PARTY']];
				}
				//end for factory name

				//for knitting party
				if ($row['WITHIN_GROUP'] == 1)
				{
					$row['KNITTING_PARTY'] = $company_library[$row['BUYER_ID']];
				}
				else
				{
					$row['KNITTING_PARTY'] = $buyer_arr[$row['BUYER_ID']];
				}
				//end for knitting party

				//for color
				$color_arr = array();
				$exp_color = array();
				$exp_color = explode(",", $row['COLOR_ID']);
				foreach ($exp_color as $key=>$val)
				{
					$color_arr[$val] = $color_library[$val];
				}
				//end for color

				//for fabric type
				$exp_fab_desc = array();
				$exp_fab_desc = explode(",", $row['FABRIC_DESC']);
				$row['FABRIC_TYPE'] = $exp_fab_desc[0];
				//end for fabric type

				$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']]['PO_ID'] = $row['PO_ID'];
				$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']]['KNITTING_SOURCE'] = $row['KNITTING_SOURCE'];
				$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']]['KNITTING_PARTY'] = $row['KNITTING_PARTY'];
				$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
				$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']]['START_DATE'] = change_date_format($row['START_DATE']);
				$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']]['END_DATE'] = change_date_format($row['END_DATE']);

				$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']]['FABRIC_TYPE'] = $row['FABRIC_TYPE'];
				$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']]['FABRIC_GSM'] = $row['GSM_WEIGHT'];
				$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']]['FABRIC_DIA'] = $row['FABRIC_DIA'];
				$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']]['MACHINE_DIA'] = $row['MACHINE_DIA'];
				$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']]['MACHINE_GG'] = $row['MACHINE_GG'];
				$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']]['FACTORY_NAME'] = $row['FACTORY_NAME'];
				$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']]['FABRIC_COLOR'] = implode(', ', $color_arr);
				$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']]['PROGRAM_QNTY'] += $row['PROGRAM_QNTY'];
				$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']]['PO_JOB_NO'] = $row['PO_JOB_NO'];
				//for sales program qty
				$sales_prog_qty[$row['JOB_NO']][$row['BOOKING_NO']]['PROGRAM_QNTY'] += $row['PROGRAM_QNTY'];
			}

			//for sales booking qty
			if($duplicate_check2[$row['SALES_ID']] != $row['SALES_ID'])
			{
				$duplicate_check2[$row['SALES_ID']] = $row['SALES_ID'];
				$sales_booking_qty[$row['JOB_NO']][$row['BOOKING_NO']]['SALES_QNTY'] += $row['GREY_QTY'];
			}

			if($jo_no_chk[$row['PO_JOB_NO']] == "")
			{
				$jo_no_chk[$row['PO_JOB_NO']] = $row['PO_JOB_NO'];
				array_push($po_job_no_arr,$row[csf('PO_JOB_NO')]);
			}
		}

		$break_down_arr = array();
		$break_down_cond = '';
		if(!empty($po_job_no_arr))
		{
			$break_down_cond = where_con_using_array($po_job_no_arr, '1', 'job_no_mst');
		}

		$poBreakData = "select job_no_mst, grouping from wo_po_break_down where status_active=1 and is_deleted=0 $break_down_cond";
		//echo $poBreakData;

		foreach (sql_select($poBreakData) as $rows)
		{
			$break_down_arr[$rows[csf('job_no_mst')]]['grouping'] = $rows[csf('grouping')];
		}
		//var_dump($break_down_arr);


		$col = 25;
		$colspan = 31;
		$tbl_width = 2160;
		$search_by_arr = array(0 => "All", 1 => "Inside", 3 => "Outside");

		if ($presentationType == 1)
		{
		   ob_start();
		   ?>
           <style>
			   .cls_break td{
				  word-break:break-all;
				}

				.cls_tot{
					text-align:right;
					font-weight:bold;
				}
		   </style>
            <fieldset style="width:<? echo $tbl_width; ?>px;">
				<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width - 40; ?>">
					<tr>
						<td align="center" width="100%" colspan="<? echo $col; ?>" style="font-size:16px">
						<strong>Knitting Plan Report: <? echo $search_by_arr[str_replace("'", "", $type)]; ?></strong></td>
						<td><input type="hidden" value="<? echo $type; ?>" id="typeForAttention"/></td>
					</tr>
				</table>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width - 40; ?>" class="rpt_table">
				   <thead>
					<th width="40">SL</th>
					<th width='120'>Party Name</th>
					<th width="120">Sales Order No</th>
					<th width="100">Style</th>
					<th width="100">Booking No</th>
					<th width="80">Program No</th>
					<th width="100">Internal Ref.</th>
					<th width="80">Start Date</th>
					<th width="80">T.O.D</th>
                    <th width="100">Fabric Type</th>
                    <th width="100">Yarn Count</th>
					<th width="60">M/C Dia <br> M/C GG</th>
					<th width="60">F/Dia</th>
					<th width="60">F/GSM</th>
					<th width="120">Factory Name</th>
					<th width="120">Color</th>
					<th width="80">F. Booking Qty</th>
					<th width="80">Program Qty</th>
					<th width="80">Unprogram Qty</th>
					<th width="80">Yarn Delivery</th>
					<th width="80">Yarn Delivery Bal Qty</th>
					<th width="80">Knitting Production Qty</th>
					<th width="80">Fab Rec. Qty</th>
					<th width="80">Yarn Return</th>
					<th width="80">Ttl F/Bal</th>
				</thead>
				</table>
				<div style="width:<? echo $tbl_width - 20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width - 40; ?>" class="rpt_table" id="tbl_list_search">
						<tbody>
						<?
						$i = 0;
						foreach($print_data as $k_sales_no=>$v_sales_no)
						{
							foreach($v_sales_no as $k_booking_no=>$v_booking_no)
							{
								$b_span = 1;
								$bk_tot_sales_qty = 0;
								$bk_tot_unprog_qty = 0;
								$bk_tot_prog_qty = 0;
								$bk_tot_issue_qty = 0;
								$bk_tot_issblns_qty = 0;
								$bk_tot_prd_qty = 0;
								$bk_tot_rcv_qty = 0;
								$bk_tot_issrtn_qty = 0;
								$bk_tot_fabblns_qty = 0;
								foreach ($v_booking_no as $k_prog_no=>$row)
								{
									$yarn_count = implode(', ', $knit_issue_arr[$k_prog_no]['yarn_count']);
									$row['YARN_COUNT'] = implode(",",array_unique(explode(",",chop($yarn_count ,","))));
									$row['ISSUE_QTY'] = $knit_issue_arr[$k_prog_no]['qnty'];
									$row['ISSUE_RTN_QTY'] = $knit_issue_return_arr[$k_prog_no]['ret_qnty'];
									$row['ISSUE_BLNS_QTY'] = $row['PROGRAM_QNTY'] - $row['ISSUE_QTY'];
									$row['PRODUCTION_QTY'] = $knitting_recv_qnty_array[$k_prog_no];
									//$row['ORDER'] = $knitting_order_array[$k_prog_no];
									$row['RECEIVE_QTY'] = $knitProQtyArr[$k_prog_no];
									$row['FAB_BLNS_QTY'] = $row['PROGRAM_QNTY'] - $row['RECEIVE_QTY'];
									$row['SALES_QNTY'] = $sales_booking_qty[$k_sales_no][$k_booking_no]['SALES_QNTY'];
									$row['UN_PROG_QTY'] = $row['SALES_QNTY'] - $sales_prog_qty[$k_sales_no][$k_booking_no]['PROGRAM_QNTY'];

									$r_span = count($print_data[$k_sales_no][$k_booking_no]);
									?>
									<tr bgcolor="#FFFFFF" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" class="cls_break">
										<?
										if($b_span == 1)
										{
											$i++;
											?>
											<td width="40" rowspan="<? echo $r_span; ?>" valign="middle" align="center"><? echo $i; ?></td>
											<td width="120" rowspan="<? echo $r_span; ?>" valign="middle"><? echo $row['KNITTING_PARTY']; ?></td>
											<td width="120" rowspan="<? echo $r_span; ?>" valign="middle"><? echo $k_sales_no; ?></td>
											<td width="100" rowspan="<? echo $r_span; ?>" valign="middle"><? echo $row['STYLE_REF_NO']; ?></td>
											<td width="100" rowspan="<? echo $r_span; ?>" valign="middle"><? echo $k_booking_no; ?></td>
											<?
										}
										?>
										<td width="80"><a href="#" onClick="generate_report(<? echo $company_name ?>,'<? echo $k_prog_no;?>')"><? echo $k_prog_no; ?></a></td>
										<td width="100"><? echo $break_down_arr[$row['PO_JOB_NO']]['grouping']; ?></td>
										<td width="80"><? echo $row['START_DATE']; ?></td>
										<td width="80"><? echo $row['END_DATE']; ?></td>
										<td width="100"><? echo $row['FABRIC_TYPE']; ?></td>
										<td width="100"><? echo $row['YARN_COUNT']; ?></td>
										<td width="60" align="center"><? echo $row['MACHINE_DIA'].'X'.$row['MACHINE_GG']; ?></td>
										<td width="60" align="center"><? echo $row['FABRIC_DIA']; ?></td>
										<td width="60" align="center"><? echo $row['FABRIC_GSM']; ?></td>
										<td width="120"><? echo $row['FACTORY_NAME']; ?></td>
										<td width="120"><? echo $row['FABRIC_COLOR']; ?></td>
										<?
										if($b_span == 1)
										{
											?>
											<td width="80" align="right" rowspan="<? echo $r_span; ?>" valign="middle"><? echo decimal_format($row['SALES_QNTY'], '1', ','); ?></td>
											<?
											$bk_tot_sales_qty += $row['SALES_QNTY'];
											$g_tot_sales_qty += $row['SALES_QNTY'];
										}
										?>
										<td width="80" align="right"><? echo decimal_format($row['PROGRAM_QNTY'], '1', ','); ?></td>
										<?
										if($b_span == 1)
										{
											$b_span++;
											?>
											<td width="80" align="right" rowspan="<? echo $r_span; ?>" valign="middle"><? echo decimal_format($row['UN_PROG_QTY'], '1', ','); ?></td>
											<?
											$bk_tot_unprog_qty += $row['UN_PROG_QTY'];
											$g_tot_unprog_qty += $row['UN_PROG_QTY'];
										}
										?>
										<td width="80" align="right"><a href="javascript:openmypage('<? echo chop($knit_issue_arr[$k_prog_no]['issue_id'],',')?>**<? echo $knit_issue_arr[$k_prog_no]['trans_id'];?>','issue_popup')"><? echo decimal_format($row['ISSUE_QTY'], '1', ','); ?></a></td>
										<td width="80" align="right"><? echo decimal_format($row['ISSUE_BLNS_QTY'], '1', ','); ?></td>
										<td width="80" align="right"><a href="javascript:openmypage('<? echo $k_prog_no;?>','knitting_prod_popup')"><? echo decimal_format($row['PRODUCTION_QTY'], '1', ','); ?></a></td>
										<td width="80" align="right"><a href="#" onClick="openmypage('<? echo $k_prog_no?>**<? echo chop($row['PO_ID'],',')?>**<? echo $companyId;?>**<? echo $row['KNITTING_SOURCE'];?>','receive_popup')"><? echo decimal_format($row['RECEIVE_QTY'], '1', ','); ?></a></td>
										<td width="80" align="right"><a href="#" onClick="openmypage('<? echo chop($knit_issue_return_arr[$k_prog_no]['ids'],',');?>','issue_return_popup')"><? echo decimal_format($row['ISSUE_RTN_QTY'], '1', ','); ?></a></td>
										<td width="80" align="right"><? echo decimal_format($row['FAB_BLNS_QTY'], '1', ','); ?></td>
									</tr>
									<?
									$bk_tot_prog_qty += $row['PROGRAM_QNTY'];
									$bk_tot_issue_qty += $row['ISSUE_QTY'];
									$bk_tot_issblns_qty += $row['ISSUE_BLNS_QTY'];
									$bk_tot_prd_qty += $row['PRODUCTION_QTY'];
									$bk_tot_rcv_qty += $row['RECEIVE_QTY'];
									$bk_tot_issrtn_qty += $row['ISSUE_RTN_QTY'];
									$bk_tot_fabblns_qty += $row['FAB_BLNS_QTY'];

									$g_tot_prog_qty += $row['PROGRAM_QNTY'];
									$g_tot_issue_qty += $row['ISSUE_QTY'];
									$g_tot_issblns_qty += $row['ISSUE_BLNS_QTY'];
									$g_tot_prd_qty += $row['PRODUCTION_QTY'];
									$g_tot_rcv_qty += $row['RECEIVE_QTY'];
									$g_tot_issrtn_qty += $row['ISSUE_RTN_QTY'];
									$g_tot_fabblns_qty += $row['FAB_BLNS_QTY'];
								}
								?>
                                <tr bgcolor="#CCCCCC">
                                	<td colspan="16" class="cls_tot">Booking Sub Total</td>
                                    <td class="cls_tot"><? echo decimal_format($bk_tot_sales_qty, '1', ','); ?></td>
                                    <td class="cls_tot"><? echo decimal_format($bk_tot_prog_qty, '1', ','); ?></td>
                                    <td class="cls_tot"><? echo decimal_format($bk_tot_unprog_qty, '1', ','); ?></td>
                                    <td class="cls_tot"><? echo decimal_format($bk_tot_issue_qty, '1', ','); ?></td>
                                    <td class="cls_tot"><? echo decimal_format($bk_tot_issblns_qty, '1', ','); ?></td>
                                    <td class="cls_tot"><? echo decimal_format($bk_tot_prd_qty, '1', ','); ?></td>
                                    <td class="cls_tot"><? echo decimal_format($bk_tot_rcv_qty, '1', ','); ?></td>
                                    <td class="cls_tot"><? echo decimal_format($bk_tot_issrtn_qty, '1', ','); ?></td>
                                    <td class="cls_tot"><? echo decimal_format($bk_tot_fabblns_qty, '1', ','); ?></td>
                                </tr>
                                <?
							}
						}
    				    ?>
    				    </tbody>
        				<tfoot>
                            <tr>
                                <th colspan="16" class="cls_tot">Grand Total</th>
                                <th class="cls_tot"><? echo decimal_format($g_tot_sales_qty, '1', ','); ?></th>
                                <th class="cls_tot"><? echo decimal_format($g_tot_prog_qty, '1', ','); ?></th>
                                <th class="cls_tot"><? echo decimal_format($g_tot_unprog_qty, '1', ','); ?></th>
                                <th class="cls_tot"><? echo decimal_format($g_tot_issue_qty, '1', ','); ?></th>
                                <th class="cls_tot"><? echo decimal_format($g_tot_issblns_qty, '1', ','); ?></th>
                                <th class="cls_tot"><? echo decimal_format($g_tot_prd_qty, '1', ','); ?></th>
                                <th class="cls_tot"><? echo decimal_format($g_tot_rcv_qty, '1', ','); ?></th>
                                <th class="cls_tot"><? echo decimal_format($g_tot_issrtn_qty, '1', ','); ?></th>
                                <th class="cls_tot"><? echo decimal_format($g_tot_fabblns_qty, '1', ','); ?></th>
                            </tr>
        				</tfoot>
                    </table>
                </div>
            </fieldset>
            <?
		}
	}

    echo "<br />Execution Time: " . (microtime(true) - $started).'S';
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

/*
|------------------------------------------------------------------------
| for issue_popup
|------------------------------------------------------------------------
*/
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
                    $sql = "select a.issue_number, a.issue_date, sum(b.cons_quantity) as qnty from inv_issue_master a, inv_transaction b where a.id = b.mst_id and a.id in ($iss_ids) and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and a.entry_form = 3 and a.issue_basis in(3,8) and a.item_category =1 and b.transaction_type = 2 and b.id in ($trans_ids) group by a.issue_number, a.issue_date";
					//echo $sql;
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

/*
|------------------------------------------------------------------------
| for knitting_prod_popup
|------------------------------------------------------------------------
*/
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
				    $sql= "SELECT a.knitting_source,a.knitting_company,a.receive_date,a.recv_number,b.machine_no_id,(b.grey_receive_qnty) as grey_receive_qnty,b.order_id,b.id as dtls_id from inv_receive_master a,  pro_grey_prod_entry_dtls b where a.id=b.mst_id  and booking_id =$program_id and a.receive_basis=2 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by dtls_id";
                     //echo $sql;
                    $result=sql_select($sql);
					$dtls_id_arr = array();
                    foreach($result as $row)
                    {
                        $orderIdArray[] = $row[csf('order_id')].",";

						if($duplicate_chk[$row[csf('dtls_id')]]=='')
						{
							$duplicate_chk[$row[csf('dtls_id')]]=$row[csf('dtls_id')];
							array_push($dtls_id_arr,$row[csf('dtls_id')]);
						}
                    }
                    $poId = chop(implode(",",array_unique(array_filter($orderIdArray))),',');

                    $po_no_library = return_library_array("select id, po_number from wo_po_break_down where id in($poId)", "id", "po_number");
                    // $barcode_library = return_library_array("select barcode_no, po_breakdown_id from pro_roll_details where po_breakdown_id in($poId)", "po_breakdown_id", "barcode_no");

					//$roll_sql=sql_select("select barcode_no, po_breakdown_id from pro_roll_details where dtls_id=".$row[csf('dtls_id')]."");
				//	echo "select barcode_no, po_breakdown_id from pro_roll_details where dtls_id=".$row[csf('dtls_id')]."";
					//$barcode_no=$roll_sql[0][csf('barcode_no')];

					/* echo "select barcode_no, po_breakdown_id from pro_roll_details where status_active=1 and is_deleted = 0 ".where_con_using_array($dtls_id_arr,0,'dtls_id').""; */
					$roll_sql=sql_select("select dtls_id,barcode_no, po_breakdown_id from pro_roll_details where entry_form=2 and status_active=1 and is_deleted = 0 ".where_con_using_array($dtls_id_arr,0,'dtls_id')." order by dtls_id");

					$barcodedata = array();
					foreach($roll_sql as $row)
					{
						$barcodedata[$row[csf('dtls_id')]]['barcode_no']=$row[csf('barcode_no')];
					}

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
                              <td width="100" align="center"><?
							  //echo $barcode_no;
							  echo $barcodedata[$row[csf('dtls_id')]]['barcode_no'];
							  ?></td>
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

/*
|------------------------------------------------------------------------
| for receive_popup
|------------------------------------------------------------------------
*/
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
						$sql_22 = "select a.recv_number as booking_no, a.id, b.trans_id
						from inv_receive_master a, pro_grey_prod_entry_dtls b
						where a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2
						and b.status_active=1 and b.is_deleted=0 and a.booking_id = '".$program_no."'  and a.company_id = ".$companyId."";
                    }
                    else
                    {
						$sql_22 = "select a.recv_number as booking_no, a.id as id, b.trans_id
						from inv_receive_master a, pro_grey_prod_entry_dtls b
						where a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2
						and b.status_active=1 and b.is_deleted=0 and a.booking_id = '".$program_no."' and b.trans_id = 0 and a.company_id = ".$companyId."
						union
						select b.booking_no, b.id as id, null as trans_id
						from wo_booking_dtls a, wo_booking_mst b
						where a.booking_no = b.booking_no and a.program_no = '".$program_no."' and a.booking_type = 3
						and a.process = 1 and b.item_category = 12
						and a.status_active = 1 and a.is_deleted = 0
						and b.status_active = 1 and b.is_deleted = 0";
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
					if($booking_id != "0")
					{
						$sql_extend = " union
						select a.recv_number, a.receive_date, sum(b.grey_receive_qnty) as qnty
						from inv_receive_master a, pro_grey_prod_entry_dtls b
						where a.id=b.mst_id and a.item_category=13 and a.entry_form=22 and a.receive_basis in (9)
						and b.status_active=1 and b.is_deleted=0 and a.company_id = $companyId
						and a.booking_id in ($booking_id)
						group by a.recv_number, a.receive_date ";
					}

					$sql =  "select b.recv_number, b.receive_date, sum(a.qnty) qnty
					from pro_roll_details a, inv_receive_master b
					where a.entry_form = 58 and a.mst_id = b.id
					and a.booking_no = '$program_no'
					and a.status_active = 1 and a.is_deleted = 0 and b.company_id = $companyId
					group by b.recv_number, b.receive_date
					union
					select a.recv_number, a.receive_date, sum(b.grey_receive_qnty) as qnty
					from inv_receive_master a, pro_grey_prod_entry_dtls b
					where a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2
					and b.status_active=1 and b.is_deleted=0 and a.booking_id = '$program_no' and a.company_id = $companyId
					group by a.recv_number,a.receive_date
					$sql_extend ";   //and b.trans_id <> 0
					$sql_data =  "select b.recv_number, b.receive_date, sum(a.qnty) qnty
					from pro_roll_details a, inv_receive_master b
					where a.entry_form = 58 and a.mst_id = b.id
					and a.booking_no = '$program_no'
					and a.status_active = 1 and a.is_deleted = 0 and b.company_id = $companyId
					group by b.recv_number, b.receive_date

					";   //and b.trans_id <> 0
                    //echo $sql1;
					$result=sql_select($sql_data);
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

/*
|------------------------------------------------------------------------
| for issue_return_popup
|------------------------------------------------------------------------
*/
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
                    $sql="select a.recv_number, a.receive_date, sum(b.cons_quantity) as qnty from inv_receive_master a, inv_transaction b  where a.id = b.mst_id and a.id in ($ids) and b.status_active = 1 and b.is_deleted = 0 and a.entry_form = 9 and a.company_id = $companyID and b.transaction_type =4 and a.receive_basis in(3,8) group by a.recv_number, a.receive_date";
					//echo $sql;
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
?>