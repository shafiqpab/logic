<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
	header("location:login.php");

require_once('../../../../includes/common.php');
$user_name = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

function get_users_buyer()
{
	$byr_str = '';
	if ($_SESSION['logic_erp']['data_level_secured'] == 1) {
		if ($_SESSION['logic_erp']['buyer_id'] != '') {
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
/* if($action=="company_wise_report_button_setting")
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
} */

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

/*
|------------------------------------------------------------------------
| for job_no_search_popup
|------------------------------------------------------------------------
*/
if ($action == "job_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1, '', '', '');
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
					<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>PO Company</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="170">Please Enter Sales No</th>
							<th>Booking Date</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
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
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
								</td>
								<td align="center">
									<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
									<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
								</td>
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value, 'create_job_no_search_list_view', 'search_div', 'program_and_count_wise_yarn_issue_sales_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>

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
	echo load_html_head_contents("Machine Info", "../../../../", 1, 1, '', '', '');
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

/*
|------------------------------------------------------------------------
| for booking_no_search_popup
|------------------------------------------------------------------------
*/
if ($action == "booking_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str) {
			var booking_no = str.split("_");
			//alert(str);
			$('#hide_booking_no').val(str);
			parent.emailwindow.hide();
		}

		function show_data_list() {
			if ($("#txt_search_common").val() == "" && $("#cbo_buyer_name").val() == 0) {
				if ($("#txt_date_from").val() == "" && $("#txt_date_to").val() == "") {
					alert("Please select any reference");
					return;
				}

			}
			show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value + '**' + document.getElementById('cbo_year_selection').value + '**' + document.getElementById('cbo_within_group').value, 'create_order_no_search_list_view', 'search_div', 'program_and_count_wise_yarn_issue_sales_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
		}
	</script>

	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:780px;">
					<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Within Group</th>
							<th> PO Buyer</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="170">Search</th>
							<th>Booking Date</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
							<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />

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
									$dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../../') ";
									echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", 4, "", $dd, 0);
									?>
								</td>
								<td align="center" id="search_by_td">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
								</td>
								<td align="center">
									<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" readonly>To
									<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" readonly>
								</td>
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_data_list();" style="width:100px;" />
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
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>

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

		if ($year_id != 0) $year_cond = "$year_field_con=$year_id";
		else $year_cond = "";
		if ($year_id != 0) $year_cond2 = "$year_field_con2=$year_id";
		else $year_cond2 = "";
	} else {
		if ($year_id != 0) $year_cond = "and year(d.insert_date) =$year_id";
		else $year_cond = "";
		if ($year_id != 0) $year_cond2 = "and year(a.insert_date) =$year_id";
		else $year_cond2 = "";
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


	if ($within_group == 1) {
		$sql = "SELECT a.id,a.booking_no, d.job_no, $year_field,a.company_id,a.buyer_id,a.booking_date,c.po_number,d.style_ref_no from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d, fabric_sales_order_mst e where  c.job_no_mst=d.job_no and b.po_break_down_id=c.id and b.job_no=d.job_no and a.booking_no=b.booking_no and a.booking_no=e.sales_booking_no and e.within_group=1 and a.status_active=1 and a.is_deleted=0 and e.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $year_cond group by a.id,a.booking_no, d.job_no,a.company_id,a.buyer_id,a.insert_date,a.booking_date,c.po_number,d.style_ref_no
        order by a.booking_no, a.booking_date";
	} else {
		$sql = "SELECT null as id,a.sales_booking_no as booking_no, null as job_no, to_char(a.insert_date,'YYYY') as year,a.company_id,a.buyer_id, a.booking_date,null as po_number,a.style_ref_no from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  and a.within_group=2 and a.sales_booking_no like '$search_string'  $buyer_id_cond $date_cond $year_cond2 group by a.sales_booking_no, a.insert_date, a.company_id,a.buyer_id, a.booking_date, a.style_ref_no order by a.sales_booking_no, a.booking_date";
	}

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Booking No,Job No,Style,PO No,Booking Date", "120,100,100,100,100,100,100", "760", "220", 0, $sql, "js_set_value", "booking_no", "", 1, "company_id,buyer_id,0,0,0,0,0", $arr, "company_id,buyer_id,booking_no,job_no,style_ref_no,po_number,booking_date", "", '', '0,0,0,0,0,0,3', '', 1);
	exit();
}


$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
$machine_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
$feeder = array(1 => "Full Feeder", 2 => "Half Feeder");

//--------------------------------------------------------------------------------------------------------------------

/* $tmplte = explode("**", $data);
if ($tmplte[0] == "viewtemplate")
	$template = $tmplte[1];
else
	$template = $lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template == "")
	$template = 1; */

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


	$cbo_knitting_status = str_replace("'", "", $cbo_knitting_status);
	$based_on = str_replace("'", "", $cbo_based_on);
	$presentationType = str_replace("'", "", $presentationType);
	$type = str_replace("'", "", $cbo_type);
	$cbo_buyer_id = str_replace("'", "", $cbo_buyer_id);
	$company_name = $cbo_company_name;
	$companyId = str_replace("'", "", $cbo_company_name);
	$txt_int_ref = str_replace("'", "", $txt_int_ref);

	$yarn_count_dtls = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", "id", "yarn_count");

	//for po company
	$buyer_id_cond = '';
	if (str_replace("'", "", $cbo_buyer_name) == 0) {
		$byr_id = get_users_buyer();
		if ($byr_id != '') {
			$buyer_id_cond = " AND A.BUYER_ID IN (" . $byr_id . ")";
		}
	} else {
		$buyer_id_cond = " AND A.BUYER_ID IN (" . $cbo_buyer_name . ")";
	}


	if (str_replace("'", "", $cbo_party_type) == 0)
		$party_type = "%%";
	else
		$party_type = str_replace("'", "", $cbo_party_type);

	$year_cond = "";

	if ($cbo_buyer_id != 0) {
		$poBuyerCond = " AND A.BUYER_ID = " . $cbo_buyer_id;
		$po_buyer_cond = " AND D.PO_BUYER = " . $cbo_buyer_id;
	} else {
		$poBuyerCond = "";
		$po_buyer_cond = "";
	}

	//echo $po_buyer_cond;

	$cbo_year = str_replace("'", "", $cbo_year);
	$year_cond = "";
	if (trim($cbo_year) != 0) {
		$year_cond = " AND TO_CHAR(D.INSERT_DATE,'YYYY') = " . $cbo_year;
	}

	$sales_no_cond = "";
	if (str_replace("'", "", $txt_sales_no) != "")
	{
		$chk_prefix_sales_no = explode("-", str_replace("'", "", $txt_sales_no));
		if ($chk_prefix_sales_no[3] != "") {
			$sales_number = "%" . trim(str_replace("'", "", $txt_sales_no));
			$sales_no_cond = " AND D.JOB_NO LIKE '" . $sales_number . "'";
		} else {
			$sales_number = trim(str_replace("'", "", $txt_sales_no));
			$sales_no_cond = " AND D.JOB_NO_PREFIX_NUM = '" . $sales_number . "'";
		}
	}

	$booking_search_cond = "";
	if (str_replace("'", "", trim($txt_booking_no)) != "") {
		$booking_number = "%" . trim(str_replace("'", "", $txt_booking_no)) . "%";
		$booking_search_cond = "  AND D.SALES_BOOKING_NO LIKE '$booking_number'";
	}

	$year_field = "TO_CHAR(E.INSERT_DATE,'YYYY') AS YEAR";

	//for program date
	if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "") {
		if ($based_on == 2) {
			$date_cond = " AND B.PROGRAM_DATE BETWEEN " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
		} else {
			$date_cond = " AND B.START_DATE BETWEEN " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
		}
	} else {
		$date_cond = "";
	}

	if ($cbo_buyer_id != 0) {
		$buyer_cond = " AND E.PO_BUYER = " . $cbo_buyer_id;
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
		$knitting_source_cond = " AND B.KNITTING_SOURCE = " . $type;
	else
		$knitting_source_cond = "";

	$status_cond = "";
	if ($cbo_knitting_status != "")
		$status_cond = " AND B.STATUS IN(" . $cbo_knitting_status . ")";

	if ($txt_int_ref != "")
	{
		$internal_ref_cond = " AND D.GROUPING =trim('$txt_int_ref') ";
		$book_data_sql = "SELECT A.ID AS BOOKING_ID,D.GROUPING AS INTERNAL_REF,D.JOB_NO_MST FROM WO_BOOKING_MST A,WO_BOOKING_DTLS B,WO_PO_DETAILS_MASTER C, WO_PO_BREAK_DOWN D WHERE A.ID=B.BOOKING_MST_ID AND B.JOB_NO=C.JOB_NO AND C.JOB_NO=D.JOB_NO_MST AND A.COMPANY_ID = ".$company_name." $internal_ref_cond AND A.BOOKING_TYPE IN(1,4) AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 GROUP BY A.ID ,D.GROUPING,D.JOB_NO_MST";
		//echo $book_data_sql;
		$rslt_book_data = sql_select($book_data_sql);
		$job_no_arr = array();
		$job_no_check = array();
		foreach ($rslt_book_data as $row)
		{
			if (!$job_no_check[$row['JOB_NO_MST']])
			{
				$job_no_check[$row['JOB_NO_MST']] = $row['JOB_NO_MST'];
				array_push($job_no_arr,$row['JOB_NO_MST']);
			}

		}
		unset($rslt_book_data);
		$job_no_cond = "".where_con_using_array($job_no_arr,1,'D.PO_JOB_NO')."";
	}

	if ($txt_machine_no != "")
	{
		if ($db_type == 0) {
			$machine_id_ref = return_field_value("group_concat(id) as machine_id", "lib_machine_name", "machine_no='$txt_machine_no'", "machine_id");
		} else if ($db_type == 2) {
			$machine_id_ref = return_field_value("LISTAGG(cast(id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as machine_id", "lib_machine_name", "machine_no='$txt_machine_no'", "machine_id");
		}

		$sql = "SELECT A.BOOKING_NO, B.ID, B.START_DATE, B.END_DATE, B.COLOR_ID, B.KNITTING_SOURCE, B.KNITTING_PARTY, B.MACHINE_DIA, B.FABRIC_DIA, B.MACHINE_GG, C.ID AS DTLS_ID, C.PO_ID,
            C.GSM_WEIGHT, C.FABRIC_DESC, C.DETERMINATION_ID, C.YARN_DESC, C.PROGRAM_QNTY, D.BUYER_ID, D.WITHIN_GROUP, D.STYLE_REF_NO, D.JOB_NO, D.BOOKING_ENTRY_FORM, D.BOOKING_ID, D.PO_JOB_NO, E.YARN_QNTY, F.ID as PROD_ID, F.YARN_COUNT_ID, F.LOT, F.YARN_COMP_TYPE1ST, F.YARN_COMP_PERCENT1ST, F.YARN_COMP_TYPE2ND,
			F.YARN_COMP_PERCENT2ND FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B,
            PPL_PLANNING_ENTRY_PLAN_DTLS C, FABRIC_SALES_ORDER_MST D, PPL_YARN_REQUISITION_ENTRY E, PRODUCT_DETAILS_MASTER F,  PPL_ENTRY_MACHINE_DATEWISE G
            WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND C.PO_ID = D.ID  and b.id=E.knit_id and E.prod_id=F.id AND C.IS_SALES = 1 $job_no_cond AND G.MACHINE_ID IN(" . $machine_id_ref . ") AND A.COMPANY_ID = " . $company_name . "
            AND B.KNITTING_PARTY LIKE '" . $party_type . "' AND B.MACHINE_DIA LIKE '" . $machine_dia . "' AND
            B.ID LIKE '" . $program_no . "' AND A.IS_SALES=1 AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1  AND B.IS_DELETED=0 AND
            B.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND C.STATUS_ACTIVE=1 " . $buyer_id_cond . $po_buyer_cond . $sales_no_cond . $year_cond . $booking_search_cond . $date_cond . $status_cond . $knitting_source_cond . "
             ORDER BY B.ID, A.BOOKING_NO ";

	}
	else {
		$sql = "SELECT A.BOOKING_NO, B.ID, B.START_DATE, B.END_DATE, B.COLOR_ID, B.KNITTING_SOURCE, B.KNITTING_PARTY, B.MACHINE_DIA, B.FABRIC_DIA, B.MACHINE_GG, C.ID AS DTLS_ID, C.PO_ID,
            C.GSM_WEIGHT, C.FABRIC_DESC, C.DETERMINATION_ID, C.YARN_DESC, C.PROGRAM_QNTY, D.BUYER_ID, D.WITHIN_GROUP, D.STYLE_REF_NO, D.JOB_NO, D.BOOKING_ENTRY_FORM, D.BOOKING_ID, D.PO_JOB_NO, E.YARN_QNTY, F.ID as PROD_ID, F.YARN_COUNT_ID, F.LOT, F.YARN_COMP_TYPE1ST, F.YARN_COMP_PERCENT1ST, F.YARN_COMP_TYPE2ND,
			F.YARN_COMP_PERCENT2ND FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B,
            PPL_PLANNING_ENTRY_PLAN_DTLS C, FABRIC_SALES_ORDER_MST D, PPL_YARN_REQUISITION_ENTRY E, PRODUCT_DETAILS_MASTER F
            WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND C.PO_ID = D.ID  and b.id=E.knit_id and E.prod_id=F.id AND C.IS_SALES = 1 $job_no_cond AND A.COMPANY_ID = " . $company_name . "
            AND B.KNITTING_PARTY LIKE '" . $party_type . "' AND B.MACHINE_DIA LIKE '" . $machine_dia . "' AND
            B.ID LIKE '" . $program_no . "' AND A.IS_SALES=1 AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1  AND B.IS_DELETED=0 AND
            B.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND C.STATUS_ACTIVE=1 " . $buyer_id_cond . $po_buyer_cond . $sales_no_cond . $year_cond . $booking_search_cond . $date_cond . $status_cond . $knitting_source_cond . "
            ORDER BY B.ID, A.BOOKING_NO ";
	}

	//echo $sql;
	$nameArray = sql_select($sql);
	if (empty($nameArray)) {
		echo get_empty_data_msg();
		die;
	}

	if (!empty($nameArray))
	{
		$con = connect();
		$r_id3=execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_name." and ref_from in (1,2) and ENTRY_FORM = 60");

		if ($r_id3) {
			oci_commit($con);
		}
	}

	$print_data = array();
	$sales_booking_qty = array();
	$sales_prog_qty = array();
	$duplicate_check = array();
	$duplicate_check2 = array();
	$po_job_no_arr = array();
	$planIdArr = array();
	$programNoChk = array();
	$bookingIdChk = array();
	$allBookingId = array();
	$allProgramNo = array();
	foreach ($nameArray as $row)
	{

		$planIdArr[$row['ID']] = $row['ID'];

		if($bookingIdChk[$row['BOOKING_ID']] == "")
		{
			$bookingIdChk[$row['BOOKING_ID']] = $row['BOOKING_ID'];
			$allBookingId[$row["BOOKING_ID"]] = $row["BOOKING_ID"];
		}

		if($programNoChk[$row['ID']] == "")
		{
			$programNoChk[$row['ID']] = $row['ID'];
			$allProgramNo[$row["ID"]] = $row["ID"];
		}

		//for factory name
		if ($row['KNITTING_SOURCE'] == 1) {
			$row['FACTORY_NAME'] = $company_library[$row['KNITTING_PARTY']];
		} else {
			$row['FACTORY_NAME'] = $supplier_details[$row['KNITTING_PARTY']];
		}
		//end for factory name

		//for knitting party
		if ($row['WITHIN_GROUP'] == 1) {
			$row['KNITTING_PARTY'] = $company_library[$row['BUYER_ID']];
		} else {
			$row['KNITTING_PARTY'] = $buyer_arr[$row['BUYER_ID']];
		}
		//end for knitting party

		//for color
		$color_arr = array();
		$exp_color = array();
		$exp_color = explode(",", $row['COLOR_ID']);
		foreach ($exp_color as $key => $val) {
			$color_arr[$val] = $color_library[$val];
		}
		//end for color

		//for fabric type
		$exp_fab_desc = array();
		$exp_fab_desc = explode(",", $row['FABRIC_DESC']);
		$row['FABRIC_TYPE'] = $exp_fab_desc[0];
		//end for fabric type

		// for yarn comp
		$compos = '';
		if ($row['YARN_COMP_PERCENT2ND'] != 0) {
			$compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
		} else {
			$compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']];
		}
		// End for yarn comp

		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['PO_ID'] = $row['PO_ID'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['KNITTING_SOURCE'] = $row['KNITTING_SOURCE'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['KNITTING_PARTY'] = $row['KNITTING_PARTY'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['START_DATE'] = change_date_format($row['START_DATE']);
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['END_DATE'] = change_date_format($row['END_DATE']);
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['FABRIC_TYPE'] = $row['FABRIC_TYPE'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['FABRIC_GSM'] = $row['GSM_WEIGHT'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['FABRIC_DIA'] = $row['FABRIC_DIA'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['MACHINE_DIA'] = $row['MACHINE_DIA'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['MACHINE_GG'] = $row['MACHINE_GG'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['FACTORY_NAME'] = $row['FACTORY_NAME'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['FABRIC_COLOR'] = implode(', ', $color_arr);
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['PO_JOB_NO'] = $row['PO_JOB_NO'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['LOT'] = $row['LOT'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['COMPOS'] = $compos;
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['YARN_COUNT_ID'] = $row['YARN_COUNT_ID'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['YARN_QNTY'] += $row['YARN_QNTY'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']][$row['PROD_ID']]['BOOKING_ID'] = $row['BOOKING_ID'];


		//for Program qty
		if ($duplicate_check1[$row['ID']] != $row['ID'])
		{
			$duplicate_check1[$row['ID']] = $row['ID'];
			$print_data_prog_qnty[$row['JOB_NO']][$row['BOOKING_NO']][$row['ID']]['PROGRAM_QNTY'] += $row['PROGRAM_QNTY'];
		}

	}

	$allBookingId = array_filter($allBookingId);
	//var_dump($allBookingId);die;
	if(!empty($allBookingId))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 60, 1,$allBookingId, $empty_arr);
		//die;
		$booking_sql = "SELECT A.ID AS BOOKING_ID,D.GROUPING AS INTERNAL_REF FROM WO_BOOKING_MST A,WO_BOOKING_DTLS B,WO_PO_DETAILS_MASTER C, WO_PO_BREAK_DOWN D, GBL_TEMP_ENGINE E WHERE A.ID=B.BOOKING_MST_ID AND B.JOB_NO=C.JOB_NO AND C.JOB_NO=D.JOB_NO_MST AND COMPANY_ID = ".$company_name." AND A.ID=E.REF_VAL AND E.USER_ID=$user_name AND E.REF_FROM IN (1) AND E.ENTRY_FORM=60 AND A.BOOKING_TYPE IN(1,4) AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 GROUP BY A.ID ,D.GROUPING";
		//echo $booking_sql;die;
		$rslt_booking_sql = sql_select($booking_sql);
		$booking_info_arr = array();
		foreach ($rslt_booking_sql as $row)
		{
			$booking_info_arr[$row['BOOKING_ID']]['INTERNAL_REF'] = $row['INTERNAL_REF'];
		}
		unset($rslt_booking_sql);
	}

	//for issue

	$allProgramNo = array_filter($allProgramNo);
	//var_dump($allProgramNo);die;
	if(!empty($allProgramNo))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 60, 2,$allProgramNo, $empty_arr);
		//die;

		$knit_issue_arr = array();

		$sql_data = sql_select("SELECT B.ID AS TRANS_ID, C.KNIT_ID AS PROGRAM_NO, B.CONS_QUANTITY, A.ID AS ISSUE_ID, D.LOT, D.YARN_COUNT_ID, D.ID AS PROD_ID FROM INV_ISSUE_MASTER A, INV_TRANSACTION B, PPL_YARN_REQUISITION_ENTRY C, PRODUCT_DETAILS_MASTER D, GBL_TEMP_ENGINE E WHERE A.ID = B.MST_ID AND B.REQUISITION_NO = C.REQUISITION_NO AND B.PROD_ID = D.ID AND C.PROD_ID = D.ID AND C.KNIT_ID=E.REF_VAL AND E.USER_ID=$user_name and E.REF_FROM IN (2) and E.ENTRY_FORM=60 AND B.RECEIVE_BASIS in(3,8) AND A.ISSUE_BASIS in(3,8) AND A.ENTRY_FORM = 3 AND A.COMPANY_ID = " . $company_name . " AND B.ITEM_CATEGORY = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0");
		$transId_chk = array();
		foreach ($sql_data as $row)
		{
			if ($transId_chk[$row['TRANS_ID']] == "")
			{
				$transId_chk[$row['TRANS_ID']] = $row['TRANS_ID'];
				$knit_issue_arr[$row['PROGRAM_NO']][$row['PROD_ID']]['qnty'] += $row['CONS_QUANTITY'];
			}
		}
		unset($sql_data);
		//var_dump($knit_issue_arr);die;
		//end for issue

		//for issue return
		$knit_issue_return = array();

		$sql_iss_return = sql_select("SELECT B.ID AS TRANS_ID, B.PROD_ID, A.ID, B.CONS_QUANTITY AS ISS_RETURN_QTY, A.BOOKING_ID, C.KNIT_ID FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, PPL_YARN_REQUISITION_ENTRY C, GBL_TEMP_ENGINE D WHERE A.ID = B.MST_ID AND B.REQUISITION_NO = C.REQUISITION_NO AND B.PROD_ID=C.PROD_ID AND C.KNIT_ID=D.REF_VAL AND D.USER_ID=$user_name and D.REF_FROM IN (2) and D.ENTRY_FORM=60 AND A.ITEM_CATEGORY = 1 AND B.TRANSACTION_TYPE = 4 AND A.ENTRY_FORM = 9 AND B.COMPANY_ID = " . $company_name . " AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND A.IS_DELETED = 0 AND A.STATUS_ACTIVE = 1 AND A.RECEIVE_BASIS in(3,8) AND B.RECEIVE_BASIS in(3,8)");
		$transId_chk = array();
		foreach ($sql_iss_return as $row) {
			if ($transId_chk[$row['TRANS_ID']] == "") {
				$transId_chk[$row['TRANS_ID']] = $row['TRANS_ID'];
				$knit_issue_return_arr[$row['KNIT_ID']][$row['PROD_ID']]['ret_qnty'] += $row['ISS_RETURN_QTY'];
				//$knit_issue_return_arr[$row['KNIT_ID']]['ids'] .= $row['ID'].",";
			}
		}
		unset($sql_iss_return);
		//end for issue return
	}


	// echo "<pre>";
	// print_r($print_data_prog_qnty);
	$r_id3 = execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_name." and ref_from in (1,2) and ENTRY_FORM=60");
	if ($r_id3) $flag = 1;
	else $flag = 0;

	if ($flag == 1) {
		oci_commit($con);
		disconnect($con);
	}

	$sales_order_count = array();
	$prog_count = array();
	$prod_id_count = array();
	foreach ($print_data as $k_sales_no => $v_sales_no)
	{
		foreach ($v_sales_no as $k_booking_no => $v_booking_no)
		{
			foreach ($v_booking_no as $k_prog_no => $v_prog_no)
			{
				foreach ($v_prog_no as $k_prod_id => $row)
				{
					$sales_order_count[$k_sales_no]++;
					$prog_count[$k_sales_no][$k_prog_no]++;
					$prod_id_count[$k_sales_no][$k_prog_no][$k_prod_id]++;
				}
			}
		}
	}

	$col = 20;
	//$colspan = 31;
	$tbl_width = 1940;


	ob_start();
	?>
	<style>
			.wrd_brk{word-break: break-all;word-wrap: break-word;}

		.cls_tot {
			text-align: right;
			font-weight: bold;
		}
	</style>
	<fieldset style="width:<? echo $tbl_width; ?>px;">
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width - 40; ?>">
			<tr>
				<td align="center" width="100%" colspan="<? echo $col; ?>" style="font-size:16px">
					<strong>Program and Count Wise Yarn Issue Report [Sales]</strong>
				</td>

			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width - 40; ?>" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width='120'>Company Name</th>
				<th width="120">Sales Order No</th>
				<th width="100">Style</th>
				<th width="100">Booking No</th>
				<th width="80">Program No</th>
				<th width="100">Program Qty</th>
				<th width="100">Internal Ref.</th>
				<th width="100">Start Date</th>
				<th width="100">T.O.D</th>
				<th width="100">Fabric Type</th>
				<th width="150">Party Name</th>
				<th width="80">Yarn Count</th>
				<th width="150">DESCRIPTION OF <br> YARN</th>
				<th width="80">YARN LOT</th>
				<th width="100">Yarn Req</th>
				<th width="80">Yarn Delivery</th>
				<th width="80">Yarn Delivery <br> Bal Qty</th>
				<th width="80">Yarn Return</th>
				<th width="80">Ttl Y/Bal</th>
			</thead>
		</table>
		<div style="width:<? echo $tbl_width - 20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width - 40; ?>" class="rpt_table" id="tbl_list_search">
				<tbody>
					<?
					$i = 1;

					$g_tot_yarn_req = 0;
					$g_tot_knit_issue_qty = 0;
					$g_tot_ydbq_qty = 0;
					$g_tot_knit_issue_rtn_qty = 0;
					$g_tot_ttlYb_qty = 0;
					foreach ($print_data as $k_sales_no => $v_sales_no)
					{
						$sub_tot_yarn_req = 0;
						$sub_tot_knit_issue_qty = 0;
						$sub_tot_ydbq_qty = 0;
						$sub_tot_knit_issue_rtn_qty = 0;
						$sub_tot_ttlYb_qty = 0;

						foreach ($v_sales_no as $k_booking_no => $v_booking_no)
						{
							foreach ($v_booking_no as $k_prog_no => $v_prog_no)
							{
								foreach ($v_prog_no as $k_prod_id => $row)
								{
									//var_dump($row);
									$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
									$sales_span = $sales_order_count[$k_sales_no];
									$prog_span = $prog_count[$k_sales_no][$k_prog_no];
									$prod_id_span = $prod_id_count[$k_sales_no][$k_prog_no][$k_prod_id];
									?>
									<tr bgcolor="#FFFFFF" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" class="cls_break">
										<?
										if (!in_array($k_sales_no, $sales_chk)) {
											$sales_chk[] = $k_sales_no;
										?>
											<td class="wrd_brk" width="40" rowspan="<? echo $sales_span; ?>" valign="middle"><? echo $i; ?></td>
											<td class="wrd_brk" width='120' rowspan="<? echo $sales_span; ?>" valign="middle"><? echo $row['KNITTING_PARTY']; ?></td>
											<td class="wrd_brk" width="120" rowspan="<? echo $sales_span; ?>" valign="middle"><? echo $k_sales_no; ?></td>
											<td class="wrd_brk" width="100" rowspan="<? echo $sales_span; ?>" valign="middle"><? echo $row['STYLE_REF_NO']; ?></td>
											<td class="wrd_brk" width="100" rowspan="<? echo $sales_span; ?>" valign="middle"><? echo $k_booking_no; ?></td>
										<?
										}
										if (!in_array($k_sales_no . "**" . $k_prog_no, $prog_chk)) {
											$prog_chk[] = $k_sales_no . "**" . $k_prog_no;
										?>
											<td class="wrd_brk" width="80" rowspan="<? echo $prog_span; ?>" valign="middle"><? echo $k_prog_no; ?></td>
											<td class="wrd_brk" width="100" rowspan="<? echo $prog_span; ?>" valign="middle">
												<?
												$program_qnty = $print_data_prog_qnty[$k_sales_no][$k_booking_no][$k_prog_no]['PROGRAM_QNTY'];
												echo decimal_format($program_qnty, '1', ',');
												?>
											</td>
											<td class="wrd_brk" width="100" rowspan="<? echo $prog_span; ?>" valign="middle"><? echo $booking_info_arr[$row['BOOKING_ID']]['INTERNAL_REF']; ?></td>
											<td class="wrd_brk" width="100" rowspan="<? echo $prog_span; ?>" valign="middle"><? echo $row['START_DATE']; ?></td>
											<td class="wrd_brk" width="100" rowspan="<? echo $prog_span; ?>" valign="middle"><? echo $row['END_DATE']; ?></td>
											<td class="wrd_brk" width="100" rowspan="<? echo $prog_span; ?>" valign="middle"><? echo $row['FABRIC_TYPE']; ?></td>
											<td class="wrd_brk" width="150" rowspan="<? echo $prog_span; ?>" valign="middle"><? echo $row['FACTORY_NAME']; ?></td>
										<?
										}

										if (!in_array($k_sales_no . "**" . $k_prog_no . "**" . $k_prod_id, $prod_id_chk)) {
											$prod_id_chk[] = $k_sales_no . "**" . $k_prog_no . "**" . $k_prod_id;
										?>
											<td class="wrd_brk" width="80" rowspan="<? echo $prod_id_span; ?>"><? echo $yarn_count_dtls[$row['YARN_COUNT_ID']]; ?></td>
											<td class="wrd_brk" width="150" rowspan="<? echo $prod_id_span; ?>"><? echo $row['COMPOS']; ?></td>
											<td class="wrd_brk" width="80" rowspan="<? echo $prod_id_span; ?>"><? echo $row['LOT']; ?></td>
											<td class="wrd_brk" width="100" rowspan="<? echo $prod_id_span; ?>" align="right"><? echo number_format($row['YARN_QNTY'], 2); ?></td>
											<td class="wrd_brk" width="80" rowspan="<? echo $prod_id_span; ?>" align="right">
												<?
												$knit_issue_qnty = $knit_issue_arr[$k_prog_no][$k_prod_id]['qnty'];
												echo number_format($knit_issue_qnty, 2);
												?></td>
											<td class="wrd_brk" width="80" rowspan="<? echo $prod_id_span; ?>" align="right" title="(Yarn Req-Yarn Delivery)">
												<?
												$ydbq = $row['YARN_QNTY'] - $knit_issue_qnty;
												echo number_format($ydbq, 2); ?>
											</td>
											<td class="wrd_brk" width="80" rowspan="<? echo $prod_id_span; ?>" align="right" >
												<? $knit_issue_rtn_qnty = $knit_issue_return_arr[$k_prog_no][$k_prod_id]['ret_qnty'];
												echo number_format($knit_issue_rtn_qnty, 2);
												?>
											</td>
											<td class="wrd_brk" width="80" rowspan="<? echo $prod_id_span; ?>" align="right" title="(Yarn Delivery Bal Qty+Yarn Return)">
												<?
												$ttlYb = $ydbq + $knit_issue_rtn_qnty;
												echo number_format($ttlYb, 2); ?>
											</td>
										<? } ?>
									</tr>
									<?

									$sub_tot_yarn_req += $row['YARN_QNTY'];
									$g_tot_yarn_req += $row['YARN_QNTY'];

									$sub_tot_knit_issue_qty += $knit_issue_qnty;
									$g_tot_knit_issue_qty += $knit_issue_qnty;

									$sub_tot_ydbq_qty += $ydbq;
									$g_tot_ydbq_qty += $ydbq;

									$sub_tot_knit_issue_rtn_qty += $knit_issue_rtn_qnty;
									$g_tot_knit_issue_rtn_qty += $knit_issue_rtn_qnty;

									$sub_tot_ttlYb_qty += $ttlYb;
									$g_tot_ttlYb_qty += $ttlYb;
								}
							}
						}
						?>
						<tr bgcolor="#CCCCCC">
							<th class="cls_tot" align="right" colspan="15">Booking Sub Total :&nbsp; </th>
							<th class="cls_tot" align="right"><?php echo number_format($sub_tot_yarn_req, 2, '.', ''); ?>&nbsp;</th>
							<th class="cls_tot" align="right"><? echo  number_format($sub_tot_knit_issue_qty, 2, '.', ''); ?>&nbsp;</th>
							<th class="cls_tot" align="right"><? echo number_format($sub_tot_ydbq_qty, 2, '.', ''); ?>&nbsp;</th>
							<th class="cls_tot" align="right"><? echo number_format($sub_tot_knit_issue_rtn_qty, 2, '.', ''); ?>&nbsp;</th>
							<th class="cls_tot" align="right"><? echo number_format($sub_tot_ttlYb_qty, 2, '.', ''); ?>&nbsp;</th>
						</tr>

					<?
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="15" ><b>Grand Total:</b></th>
						<th class="cls_tot"><? echo number_format($g_tot_yarn_req, 2, '.', ''); ?></th>
						<th class="cls_tot"><? echo number_format($g_tot_knit_issue_qty, 2, '.', ''); ?></th>
						<th class="cls_tot"><? echo number_format($g_tot_ydbq_qty, 2, '.', ''); ?></th>
						<th class="cls_tot"><? echo number_format($g_tot_knit_issue_rtn_qty, 2, '.', ''); ?></th>
						<th class="cls_tot"><? echo number_format($g_tot_ttlYb_qty, 2, '.', ''); ?></th>
					</tr>
				</tfoot>

			</table>
		</div>
	</fieldset>
	<?


	echo "<br />Execution Time: " . (microtime(true) - $started) . 'S';
	foreach (glob("$user_name*.xls") as $filename) {
		if (@filemtime($filename) < (time() - $seconds_old))
			@unlink($filename);
	}
	//---------end------------//
	$html = ob_get_contents();
	ob_clean();
	$total_data = $html;
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
| for report_generate_count
|------------------------------------------------------------------------
*/
if ($action == "report_generate_count")
{
	$started = microtime(true);
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$txt_machine_no = str_replace("'", "", $txt_machine_no);


	$cbo_knitting_status = str_replace("'", "", $cbo_knitting_status);
	$based_on = str_replace("'", "", $cbo_based_on);
	$presentationType = str_replace("'", "", $presentationType);
	$type = str_replace("'", "", $cbo_type);
	$cbo_buyer_id = str_replace("'", "", $cbo_buyer_id);
	$company_name = $cbo_company_name;
	$companyId = str_replace("'", "", $cbo_company_name);
	$txt_int_ref = str_replace("'", "", $txt_int_ref);

	$yarn_count_dtls = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", "id", "yarn_count");


	//for po company
	$buyer_id_cond = '';
	if (str_replace("'", "", $cbo_buyer_name) == 0) {
		$byr_id = get_users_buyer();
		if ($byr_id != '') {
			$buyer_id_cond = " AND A.BUYER_ID IN (" . $byr_id . ")";
		}
	} else {
		$buyer_id_cond = " AND A.BUYER_ID IN (" . $cbo_buyer_name . ")";
	}


	if (str_replace("'", "", $cbo_party_type) == 0)
		$party_type = "%%";
	else
		$party_type = str_replace("'", "", $cbo_party_type);

	$year_cond = "";

	if ($cbo_buyer_id != 0) {
		$poBuyerCond = " AND A.BUYER_ID = " . $cbo_buyer_id;
		$po_buyer_cond = " AND D.PO_BUYER = " . $cbo_buyer_id;
	} else {
		$poBuyerCond = "";
		$po_buyer_cond = "";
	}

	//echo $po_buyer_cond;

	$cbo_year = str_replace("'", "", $cbo_year);
	$year_cond = "";
	if (trim($cbo_year) != 0) {
		$year_cond = " AND TO_CHAR(D.INSERT_DATE,'YYYY') = " . $cbo_year;
	}

	$sales_no_cond = "";
	if (str_replace("'", "", $txt_sales_no) != "")
	{
		$chk_prefix_sales_no = explode("-", str_replace("'", "", $txt_sales_no));
		if ($chk_prefix_sales_no[3] != "") {
			$sales_number = "%" . trim(str_replace("'", "", $txt_sales_no));
			$sales_no_cond = " AND D.JOB_NO LIKE '" . $sales_number . "'";
		} else {
			$sales_number = trim(str_replace("'", "", $txt_sales_no));
			$sales_no_cond = " AND D.JOB_NO_PREFIX_NUM = '" . $sales_number . "'";
		}
	}

	$booking_search_cond = "";
	if (str_replace("'", "", trim($txt_booking_no)) != "") {
		$booking_number = "%" . trim(str_replace("'", "", $txt_booking_no)) . "%";
		$booking_search_cond = "  AND D.SALES_BOOKING_NO LIKE '$booking_number'";
	}

	$year_field = "TO_CHAR(E.INSERT_DATE,'YYYY') AS YEAR";

	//for program date
	if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "") {
		if ($based_on == 2) {
			$date_cond = " AND B.PROGRAM_DATE BETWEEN " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
		} else {
			$date_cond = " AND B.START_DATE BETWEEN " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
		}
	} else {
		$date_cond = "";
	}

	if ($cbo_buyer_id != 0) {
		$buyer_cond = " AND E.PO_BUYER = " . $cbo_buyer_id;
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
		$knitting_source_cond = " AND B.KNITTING_SOURCE = " . $type;
	else
		$knitting_source_cond = "";

	$status_cond = "";
	if ($cbo_knitting_status != "")
		$status_cond = " AND B.STATUS IN(" . $cbo_knitting_status . ")";

	if ($txt_int_ref != "")
	{
		$internal_ref_cond = " AND D.GROUPING =trim('$txt_int_ref') ";
		$book_data_sql = "SELECT A.ID AS BOOKING_ID,D.GROUPING AS INTERNAL_REF,D.JOB_NO_MST FROM WO_BOOKING_MST A,WO_BOOKING_DTLS B,WO_PO_DETAILS_MASTER C, WO_PO_BREAK_DOWN D WHERE A.ID=B.BOOKING_MST_ID AND B.JOB_NO=C.JOB_NO AND C.JOB_NO=D.JOB_NO_MST AND A.COMPANY_ID = ".$company_name." $internal_ref_cond AND A.BOOKING_TYPE IN(1,4) AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 GROUP BY A.ID ,D.GROUPING,D.JOB_NO_MST";
		//echo $book_data_sql;
		$rslt_book_data = sql_select($book_data_sql);
		$job_no_arr = array();
		$job_no_check = array();
		foreach ($rslt_book_data as $row)
		{
			if (!$job_no_check[$row['JOB_NO_MST']])
			{
				$job_no_check[$row['JOB_NO_MST']] = $row['JOB_NO_MST'];
				array_push($job_no_arr,$row['JOB_NO_MST']);
			}

		}
		unset($rslt_book_data);
		$job_no_cond = "".where_con_using_array($job_no_arr,1,'D.PO_JOB_NO')."";
	}

	if ($txt_machine_no != "")
	{
		if ($db_type == 0) {
			$machine_id_ref = return_field_value("group_concat(id) as machine_id", "lib_machine_name", "machine_no='$txt_machine_no'", "machine_id");
		} else if ($db_type == 2) {
			$machine_id_ref = return_field_value("LISTAGG(cast(id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as machine_id", "lib_machine_name", "machine_no='$txt_machine_no'", "machine_id");
		}

		$sql = "SELECT A.BOOKING_NO, B.ID, B.START_DATE, B.END_DATE, B.COLOR_ID, B.KNITTING_SOURCE, B.KNITTING_PARTY, B.MACHINE_DIA, B.FABRIC_DIA, B.MACHINE_GG, C.ID AS DTLS_ID, C.PO_ID,
            C.GSM_WEIGHT, C.FABRIC_DESC, C.DETERMINATION_ID, C.YARN_DESC, C.PROGRAM_QNTY, D.BUYER_ID, D.WITHIN_GROUP, D.STYLE_REF_NO, D.JOB_NO, D.BOOKING_ENTRY_FORM, D.BOOKING_ID, D.PO_JOB_NO, E.YARN_QNTY, F.ID as PROD_ID, F.YARN_COUNT_ID, F.LOT, F.YARN_COMP_TYPE1ST, F.YARN_COMP_PERCENT1ST, F.YARN_COMP_TYPE2ND,
			F.YARN_COMP_PERCENT2ND, F.YARN_TYPE,D.CUSTOMER_BUYER, F.SUPPLIER_ID FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B,
            PPL_PLANNING_ENTRY_PLAN_DTLS C, FABRIC_SALES_ORDER_MST D, PPL_YARN_REQUISITION_ENTRY E, PRODUCT_DETAILS_MASTER F,  PPL_ENTRY_MACHINE_DATEWISE G
            WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND C.PO_ID = D.ID  and b.id=E.knit_id and E.prod_id=F.id AND C.IS_SALES = 1 $job_no_cond AND G.MACHINE_ID IN(" . $machine_id_ref . ") AND A.COMPANY_ID = " . $company_name . "
            AND B.KNITTING_PARTY LIKE '" . $party_type . "' AND B.MACHINE_DIA LIKE '" . $machine_dia . "' AND
            B.ID LIKE '" . $program_no . "' AND A.IS_SALES=1 AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1  AND B.IS_DELETED=0 AND
            B.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND C.STATUS_ACTIVE=1 " . $buyer_id_cond . $po_buyer_cond . $sales_no_cond . $year_cond . $booking_search_cond . $date_cond . $status_cond . $knitting_source_cond . "
             ORDER BY B.ID, A.BOOKING_NO ";

	}
	else {
		$sql = "SELECT A.BOOKING_NO, B.ID, B.START_DATE, B.END_DATE, B.COLOR_ID, B.KNITTING_SOURCE, B.KNITTING_PARTY, B.MACHINE_DIA, B.FABRIC_DIA, B.MACHINE_GG, C.ID AS DTLS_ID, C.PO_ID,
            C.GSM_WEIGHT, C.FABRIC_DESC, C.DETERMINATION_ID, C.YARN_DESC, C.PROGRAM_QNTY, D.BUYER_ID, D.WITHIN_GROUP, D.STYLE_REF_NO, D.JOB_NO, D.BOOKING_ENTRY_FORM, D.BOOKING_ID, D.PO_JOB_NO, E.YARN_QNTY, F.ID as PROD_ID, F.YARN_COUNT_ID, F.LOT, F.YARN_COMP_TYPE1ST, F.YARN_COMP_PERCENT1ST, F.YARN_COMP_TYPE2ND,
			F.YARN_COMP_PERCENT2ND, F.YARN_TYPE,D.CUSTOMER_BUYER, F.SUPPLIER_ID FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B,
            PPL_PLANNING_ENTRY_PLAN_DTLS C, FABRIC_SALES_ORDER_MST D, PPL_YARN_REQUISITION_ENTRY E, PRODUCT_DETAILS_MASTER F
            WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND C.PO_ID = D.ID  and b.id=E.knit_id and E.prod_id=F.id AND C.IS_SALES = 1 $job_no_cond AND A.COMPANY_ID = " . $company_name . "
            AND B.KNITTING_PARTY LIKE '" . $party_type . "' AND B.MACHINE_DIA LIKE '" . $machine_dia . "' AND
            B.ID LIKE '" . $program_no . "' AND A.IS_SALES=1 AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1  AND B.IS_DELETED=0 AND
            B.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND C.STATUS_ACTIVE=1 " . $buyer_id_cond . $po_buyer_cond . $sales_no_cond . $year_cond . $booking_search_cond . $date_cond . $status_cond . $knitting_source_cond . "
            ORDER BY B.ID, A.BOOKING_NO ";
	}

	//echo $sql;
	$nameArray = sql_select($sql);
	if (empty($nameArray)) {
		echo get_empty_data_msg();
		die;
	}

	if (!empty($nameArray))
	{
		$con = connect();
		$r_id33=execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_name." and ref_from in (1,2,3,4) and ENTRY_FORM = 60");

		if ($r_id33)
		{
			oci_commit($con);
			disconnect($con);
		}
	}

	$planIdArr = array();
	$bookingIdChk = array();
	$programNoChk = array();
	$allBookingId = array();
	$allProgramNo = array();
	$poIdChk = array();

	$print_data = array();
	$sales_booking_qty = array();
	$sales_prog_qty = array();
	$duplicate_check = array();
	$duplicate_check2 = array();
	$po_job_no_arr = array();
	$all_sales_id_arr = array();
	$salesIdChk = array();
	foreach ($nameArray as $row)
	{
		$planIdArr[$row['ID']] = $row['ID'];

		if($programNoChk[$row['ID']] == "")
		{
			$programNoChk[$row['ID']] = $row['ID'];
			$allProgramNo[$row["ID"]] = $row["ID"];
		}

		if($bookingIdChk[$row['BOOKING_ID']] == "")
		{
			$bookingIdChk[$row['BOOKING_ID']] = $row['BOOKING_ID'];
			$allBookingId[$row["BOOKING_ID"]] = $row["BOOKING_ID"];
		}

		if($poIdChk[$row['PO_ID']] == "")
		{
			$poIdChk[$row['PO_ID']] = $row['PO_ID'];
			$allPoId[$row["PO_ID"]] = $row["PO_ID"];
		}
		//for factory name
		if ($row['KNITTING_SOURCE'] == 1) {
			$row['FACTORY_NAME'] = $company_library[$row['KNITTING_PARTY']];
		} else {
			$row['FACTORY_NAME'] = $supplier_details[$row['KNITTING_PARTY']];
		}
		//end for factory name

		//for knitting party
		if ($row['WITHIN_GROUP'] == 1) {
			$row['KNITTING_PARTY'] = $company_library[$row['BUYER_ID']];
		} else {
			$row['KNITTING_PARTY'] = $buyer_arr[$row['BUYER_ID']];
		}
		//end for knitting party

		//for color
		$color_arr = array();
		$exp_color = array();
		$exp_color = explode(",", $row['COLOR_ID']);
		foreach ($exp_color as $key => $val) {
			$color_arr[$val] = $color_library[$val];
		}
		//end for color

		//for fabric type
		$exp_fab_desc = array();
		$exp_fab_desc = explode(",", $row['FABRIC_DESC']);
		$row['FABRIC_TYPE'] = $exp_fab_desc[0];
		//end for fabric type

		// for yarn comp
		$compos = '';
		if ($row['YARN_COMP_PERCENT2ND'] != 0) {
			$compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
		} else {
			$compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']];
		}
		// End for yarn comp

		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['PO_ID'] = $row['PO_ID'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['KNITTING_SOURCE'] = $row['KNITTING_SOURCE'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['KNITTING_PARTY'] = $row['KNITTING_PARTY'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['START_DATE'] = change_date_format($row['START_DATE']);
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['END_DATE'] = change_date_format($row['END_DATE']);
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['FABRIC_TYPE'] = $row['FABRIC_TYPE'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['FABRIC_GSM'] = $row['GSM_WEIGHT'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['FABRIC_DIA'] = $row['FABRIC_DIA'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['MACHINE_DIA'] = $row['MACHINE_DIA'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['MACHINE_GG'] = $row['MACHINE_GG'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['FACTORY_NAME'] = $row['FACTORY_NAME'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['FABRIC_COLOR'] = implode(', ', $color_arr);
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['PO_JOB_NO'] = $row['PO_JOB_NO'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['LOT'] = $row['LOT'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['COMPOS'] = $compos;
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['YARN_COUNT_ID'] = $row['YARN_COUNT_ID'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['YARN_QNTY'] += $row['YARN_QNTY'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['BOOKING_ID'] = $row['BOOKING_ID'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['YARN_TYPE'] = $row['YARN_TYPE'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['CUSTOMER_BUYER'] = $row['CUSTOMER_BUYER'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['SUPPLIER_ID'] = $row['SUPPLIER_ID'];
		$print_data[$row['JOB_NO']][$row['BOOKING_NO']][$row['PROD_ID']]['PROGRAM_NO'] .= $row['ID'].',';

		/* if($salesIdChk[$row['SALES_ID']]=="")
		{
			$salesIdChk[$row['SALES_ID']]=$row['SALES_ID'];
			array_push($all_sales_id_arr,$row['SALES_ID']);
		} */

	}
	//echo "<pre>";print_r($print_data);

	/* $sql_issue = "SELECT a.id,a.issue_basis,a.issue_purpose,a.booking_id,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type, sum(c.quantity) as issue_qty,b.job_no,c.is_sales from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and (a.issue_purpose in(1,2) or a.issue_purpose in(1,2,7,12,15,38,46) and b.job_no is not null) and c.trans_type=2 and a.entry_form=3 and b.item_category=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 ".where_con_using_array($all_sales_id_arr,0,'c.po_breakdown_id')." group by a.id,a.issue_basis,a.issue_purpose,a.booking_id,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type,b.job_no,c.is_sales";
	echo $sql_issue; */


	$allBookingId = array_filter($allBookingId);
	//var_dump($allBookingId);die;
	if(!empty($allBookingId))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 60, 1,$allBookingId, $empty_arr);
		//die;
		$booking_sql = "SELECT A.ID AS BOOKING_ID,D.GROUPING AS INTERNAL_REF FROM WO_BOOKING_MST A,WO_BOOKING_DTLS B,WO_PO_DETAILS_MASTER C, WO_PO_BREAK_DOWN D, GBL_TEMP_ENGINE E WHERE A.ID=B.BOOKING_MST_ID AND B.JOB_NO=C.JOB_NO AND C.JOB_NO=D.JOB_NO_MST AND COMPANY_ID = ".$company_name." AND A.ID=E.REF_VAL AND E.USER_ID=$user_name AND E.REF_FROM IN (1) AND E.ENTRY_FORM=60 AND A.BOOKING_TYPE IN(1,4) AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 GROUP BY A.ID ,D.GROUPING";
		//echo $booking_sql;die;
		$rslt_booking_sql = sql_select($booking_sql);
		$booking_info_arr = array();
		foreach ($rslt_booking_sql as $row)
		{
			$booking_info_arr[$row['BOOKING_ID']]['INTERNAL_REF'] = $row['INTERNAL_REF'];
		}
		unset($rslt_booking_sql);
	}

	$allPoId = array_filter($allPoId);
	//var_dump($allBookingId);die;
	if(!empty($allPoId))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 60, 2,$allPoId, $empty_arr);

		$alloc_sql_no ="SELECT a.job_no, a.booking_no, a.qnty, a.po_break_down_id,a.item_id
		from inv_material_allocation_mst a,fabric_sales_order_mst b, GBL_TEMP_ENGINE c
		where a.po_break_down_id=cast(b.id as varchar2(4000)) and b.company_id=$company_name and b.id=c.ref_val and c.user_id=$user_name and c.ref_from IN (2) and c.entry_form=60 and  a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";
		//echo $alloc_sql_no; die;//(a.is_dyied_yarn != 1 or a.is_dyied_yarn is null) and
		$alloc_sql_result_no = sql_select($alloc_sql_no);
		$allocation_data = array();
		foreach($alloc_sql_result_no as $row)
		{
			$allocation_data[$row[csf("job_no")]][$row[csf("booking_no")]][$row[csf("item_id")]]['alloc_qnty'] += $row[csf("qnty")];
		}
		unset($alloc_sql_result_no);
	}

	$allProgramNo = array_filter($allProgramNo);
	//var_dump($allProgramNo);die;
	if(!empty($allProgramNo))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 60, 3,$allProgramNo, $empty_arr);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 60, 4,$allProgramNo, $empty_arr);
		//die;

		//for issue
		$knit_issue_arr = array();

		$sql_data = sql_select("SELECT B.ID AS TRANS_ID, C.KNIT_ID AS PROGRAM_NO, B.CONS_QUANTITY, A.ID AS ISSUE_ID, D.LOT, D.YARN_COUNT_ID, D.ID AS PROD_ID FROM INV_ISSUE_MASTER A, INV_TRANSACTION B, PPL_YARN_REQUISITION_ENTRY C, PRODUCT_DETAILS_MASTER D, GBL_TEMP_ENGINE E WHERE A.ID = B.MST_ID AND B.REQUISITION_NO = C.REQUISITION_NO AND B.PROD_ID = D.ID AND C.PROD_ID = D.ID AND C.KNIT_ID=E.REF_VAL AND E.USER_ID=$user_name and E.REF_FROM IN (3) and E.ENTRY_FORM=60 AND B.RECEIVE_BASIS in(3,8) AND A.ISSUE_BASIS in(3,8) AND A.ENTRY_FORM = 3 AND A.COMPANY_ID = " . $company_name . " AND B.ITEM_CATEGORY = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0");
		$transId_chk = array();
		$issueId_chk = array();
		$issueIdArr = array();
		foreach ($sql_data as $row)
		{
			if ($transId_chk[$row['TRANS_ID']] == "")
			{
				$transId_chk[$row['TRANS_ID']] = $row['TRANS_ID'];
				$knit_issue_arr[$row['PROGRAM_NO']][$row['PROD_ID']]['qnty'] += $row['CONS_QUANTITY'];
				$knit_issue_arr[$row['PROGRAM_NO']][$row['PROD_ID']]['issue_id'] .= $row['ISSUE_ID'].',';
			}
			if ($issueId_chk[$row['ISSUE_ID']] == "")
			{
				$issueId_chk[$row['ISSUE_ID']] = $row['ISSUE_ID'];
				$issueIdArr[$row["ISSUE_ID"]] = $row["ISSUE_ID"];
			}
		}
		unset($sql_data);
		//var_dump($knit_issue_arr);die;
		//end for issue

		//for issue return
		//$knit_issue_return = array();

		// $sql_iss_return = sql_select("SELECT B.ID AS TRANS_ID, B.PROD_ID, A.ID, B.CONS_QUANTITY AS ISS_RETURN_QTY, A.BOOKING_ID, C.KNIT_ID FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, PPL_YARN_REQUISITION_ENTRY C, GBL_TEMP_ENGINE D WHERE A.ID = B.MST_ID AND B.REQUISITION_NO = C.REQUISITION_NO AND B.PROD_ID=C.PROD_ID AND C.KNIT_ID=D.REF_VAL AND D.USER_ID=$user_name and D.REF_FROM IN (4) and D.ENTRY_FORM=60 AND A.ITEM_CATEGORY = 1 AND B.TRANSACTION_TYPE = 4 AND A.ENTRY_FORM = 9 AND B.COMPANY_ID = " . $company_name . " AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND A.IS_DELETED = 0 AND A.STATUS_ACTIVE = 1 AND A.RECEIVE_BASIS in(3,8) AND B.RECEIVE_BASIS in(3,8)");
		// $transRtnId_chk = array();
		// $knit_issue_return_arr = array();
		// foreach ($sql_iss_return as $row)
		// {
		// 	if ($transRtnId_chk[$row['TRANS_ID']] == "")
		// 	{
		// 		$transRtnId_chk[$row['TRANS_ID']] = $row['TRANS_ID'];
		// 		$knit_issue_return_arr[$row['KNIT_ID']][$row['PROD_ID']]['ret_qnty'] += $row['ISS_RETURN_QTY'];
		// 		//$knit_issue_return_arr[$row['KNIT_ID']]['ids'] .= $row['ID'].",";
		// 	}
		// }
		// unset($sql_iss_return);
		//end for issue return

		if(!empty($issueIdArr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 60, 4,$issueIdArr, $empty_arr);
			//die;
			$sql_iss_return = "SELECT B.ID AS TRANS_ID, B.PROD_ID, A.ID, B.CONS_QUANTITY AS ISS_RETURN_QTY, A.BOOKING_ID, C.KNIT_ID, B.ISSUE_ID FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, PPL_YARN_REQUISITION_ENTRY C, GBL_TEMP_ENGINE E WHERE A.ID = B.MST_ID AND B.REQUISITION_NO = C.REQUISITION_NO AND B.PROD_ID=C.PROD_ID AND A.ITEM_CATEGORY = 1 AND B.TRANSACTION_TYPE = 4 AND A.ENTRY_FORM = 9 AND B.COMPANY_ID = " . $company_name . " AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND A.IS_DELETED = 0 AND A.STATUS_ACTIVE = 1 AND A.RECEIVE_BASIS in(3,8) AND B.RECEIVE_BASIS in(3,8) AND B.ISSUE_ID=E.REF_VAL AND E.USER_ID=$user_name and E.REF_FROM IN (4) and E.ENTRY_FORM=60";
			//echo $sql_iss_return;die;
			$sql_iss_return_rslt = sql_select($sql_iss_return);
			$transRtnId_chk = array();
			$knit_issue_return_arr = array();
			foreach ($sql_iss_return_rslt as $row)
			{
				if ($transRtnId_chk[$row['TRANS_ID']] == "")
				{
					$transRtnId_chk[$row['TRANS_ID']] = $row['TRANS_ID'];
					$knit_issue_return_arr[$row['ISSUE_ID']][$row['PROD_ID']]['ret_qnty'] += $row['ISS_RETURN_QTY'];
				}
			}
			unset($sql_iss_return_rslt);
			//echo "<pre>";print_r($knit_issue_return_arr);
			//end for issue return
		}
	}

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_name." and ref_from in (1,2,3,4) and ENTRY_FORM=60");
	oci_commit($con);
	disconnect($con);

	/* $sales_order_count = array();
	$prod_id_count = array();
	foreach ($print_data as $k_sales_no => $v_sales_no)
	{
		foreach ($v_sales_no as $k_booking_no => $v_booking_no)
		{
			foreach ($v_booking_no as $k_prod_id => $row)
			{
				$sales_order_count[$k_sales_no]++;
				$prod_id_count[$k_sales_no][$k_prod_id]++;
			}
		}
	} */

	$col = 17;
	$tbl_width = 1550;

	ob_start();
	?>
	<style>
			.wrd_brk{word-break: break-all;word-wrap: break-word;}

		.cls_tot {
			text-align: right;
			font-weight: bold;
		}
	</style>
	<fieldset style="width:<? echo $tbl_width; ?>px;">
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width - 40; ?>">
			<tr>
				<td align="center" width="100%" colspan="<? echo $col; ?>" style="font-size:16px">
					<strong>Count Wise Yarn Allocation Vs Issue Report</strong>
				</td>

			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width - 40; ?>" class="rpt_table">
			<thead>
				<th class="wrd_brk" width="30">SL</th>
				<th class="wrd_brk" width='120'>Company Name</th>
				<th class="wrd_brk" width="100">Buyer</th>
				<th class="wrd_brk" width="100">Booking No</th>
				<th class="wrd_brk" width="100">FSO</th>
				<th class="wrd_brk" width="80">Internal Ref.</th>
				<th class="wrd_brk" width="100">Party Name</th>
				<th class="wrd_brk" width="100">Yarn Supplier</th>
				<th class="wrd_brk" width="100">Count</th>
				<th class="wrd_brk" width="100">Composition</th>
				<th class="wrd_brk" width="80">Yarn Type</th>
				<th class="wrd_brk" width="80">Lot</th>
				<th class="wrd_brk" width="80">Allocated Qty</th>
				<th class="wrd_brk" width="80">Issue Qty</th>
				<th class="wrd_brk" width="80">Issue Rtn Qty</th>
				<th class="wrd_brk" width="80">Net Issue Qty</th>
				<th class="wrd_brk" width="">Issue Balance</th>
			</thead>
		</table>
		<div style="width:<? echo $tbl_width - 20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width - 40; ?>" class="rpt_table" id="tbl_list_search">
				<tbody>
					<?
					$i = 1;

					$g_tot_alloc_qnty = 0;
					$g_tot_issue_qnty = 0;
					$g_tot_issue_balance = 0;
					$alloc_qnty = 0;
					foreach ($print_data as $k_sales_no => $v_sales_no)
					{
						foreach ($v_sales_no as $k_booking_no => $v_booking_no)
						{
							foreach ($v_booking_no as $k_prod_id => $row)
							{
								//var_dump($row);
								$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";

								$program_nos = array_unique(explode(",",chop($row['PROGRAM_NO'] ,",")));
								$knit_issue_qnty = $ret_qnty = $issue_id='';
								foreach ($program_nos as $val)
								{
									$knit_issue_qnty += $knit_issue_arr[$val][$k_prod_id]['qnty'];
									//$ret_qnty +=$knit_issue_return_arr[$val][$k_prod_id]['ret_qnty'];
									$issue_id .= $knit_issue_arr[$val][$k_prod_id]['issue_id'].',';
								}

								$alloc_qnty = $allocation_data[$k_sales_no][$k_booking_no][$k_prod_id]['alloc_qnty']

								?>
								<tr bgcolor="#FFFFFF" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" class="cls_break">

									<td class="wrd_brk" width="30" align="center"><? echo $i;?></td>
									<td class="wrd_brk" width='120' align="center"><? echo $row['KNITTING_PARTY']; ?></td>
									<td class="wrd_brk" width="100" align="center"><? echo $buyer_arr[$row['CUSTOMER_BUYER']]?></td>
									<td class="wrd_brk" width="100" align="center"><? echo $k_booking_no; ?></td>
									<td class="wrd_brk" width="100" align="center"><? echo $k_sales_no; ?></td>
									<td class="wrd_brk" width="80" align="center"><? echo $booking_info_arr[$row['BOOKING_ID']]['INTERNAL_REF']; ?></td>
									<td class="wrd_brk" width="100" align="center"><? echo $row['FACTORY_NAME']; ?></td>
									<td class="wrd_brk" width="100" align="center"><? echo $supplier_details[$row['SUPPLIER_ID']]?></td>
									<td class="wrd_brk" width="100" align="center"><? echo $yarn_count_dtls[$row['YARN_COUNT_ID']]; ?></td>
									<td class="wrd_brk" width="100" align="center"><? echo $row['COMPOS']; ?></td>
									<td class="wrd_brk" width="80" align="center"><? echo $yarn_type[$row['YARN_TYPE']]; ?></td>
									<td class="wrd_brk" width="80" align="center"><? echo $row['LOT']; ?></td>
									<td class="wrd_brk" width="80" title="<? echo $k_prod_id;?>" align="right"><? echo number_format($alloc_qnty,2,".","");?></td>
									<td class="wrd_brk" width="80" align="right"><? echo number_format($knit_issue_qnty,2,".","");?></td>
									<td class="wrd_brk" width="80" align="right" title="<? echo $issue_id;?>">
										<?
										$issue_ids = array_filter(array_unique(explode(",",chop($issue_id ,","))));
										//echo "<pre>";print_r($issue_ids);
										$ret_qnty = 0;
										foreach ($issue_ids as $val)
										{
											$ret_qnty += $knit_issue_return_arr[$val][$k_prod_id]['ret_qnty'];
										}
										echo number_format($ret_qnty,2,".","");
										?>
									</td>
									<td class="wrd_brk" width="80" align="right">
										<?
										$net_issue_qnty = ($knit_issue_qnty-$ret_qnty);
										echo number_format($net_issue_qnty,2,".","");
										?>
									</td>
									<td class="wrd_brk" width="" align="right">
										<?
											$issue_balance = $alloc_qnty-$net_issue_qnty;
											echo number_format($issue_balance,2,".","");
										?>
									</td>
								</tr>
								<?

								$g_tot_alloc_qnty += $alloc_qnty;
								$g_tot_issue_qnty += $knit_issue_qnty;
								$g_tot_ret_qnty += $ret_qnty;
								$g_net_issue_qnty += $net_issue_qnty;
								$g_tot_issue_balance += $issue_balance;
								$i++;
							}
						}

					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="12" ><b> Total:</b></th>
						<th class="cls_tot"><? echo number_format($g_tot_alloc_qnty, 2, '.', ''); ?></th>
						<th class="cls_tot"><? echo number_format($g_tot_issue_qnty, 2, '.', ''); ?></th>
						<th class="cls_tot"><? echo number_format($g_tot_ret_qnty, 2, '.', ''); ?></th>
						<th class="cls_tot"><? echo number_format($g_net_issue_qnty, 2, '.', ''); ?></th>
						<th class="cls_tot"><? echo number_format($g_tot_issue_balance, 2, '.', ''); ?></th>
					</tr>
				</tfoot>

			</table>
		</div>
	</fieldset>
	<?

	echo "<br />Execution Time: " . (microtime(true) - $started) . 'S';
	foreach (glob("$user_name*.xls") as $filename) {
		if (@filemtime($filename) < (time() - $seconds_old))
			@unlink($filename);
	}
	//---------end------------//
	$html = ob_get_contents();
	ob_clean();
	$total_data = $html;
	$html = strip_tags($html, '<table><thead><tbody><tfoot><tr><td><th>');
	$name = time();
	$filename = $user_name . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	$filename = "requires/" . $user_name . "_" . $name . ".xls";
	echo "$total_data####$filename";
	exit();
}
?>