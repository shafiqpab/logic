<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.trims.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id = $_SESSION['logic_erp']['user_id'];

$buyer_cond = set_user_lavel_filtering(' and buy.id', 'buyer_id');
$company_cond = set_user_lavel_filtering(' and comp.id', 'company_id');
//--------------------------------------------------------------------------------------------------------------------


if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/accessories_followup_report_controller_v2',this.value, 'load_drop_down_season_buyer', 'season_td');");
	exit();
}
if ($action == "load_drop_down_season_buyer") {
	echo create_drop_down("cbo_season_name", 100, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC", "id,season_name", 1, "-- Select Season--", "", "");
	exit();
}
if ($action == "set_print_button") {
	$print_report_format = return_field_value("format_id", "lib_report_template", "template_name ='" . $data . "' and module_id=2 and report_id=54 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n";
	die;
}

if ($action == "job_no_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data = explode('_', $data);
	//print_r ($data);

?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_style_name = new Array();

		function check_all_data() {
			var tbl_row_count = document.getElementById('list_view').rows.length;
			tbl_row_count = tbl_row_count - 0;
			for (var i = 1; i <= tbl_row_count; i++) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value(functionParam);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(strCon) {
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			var selectStyle = splitSTR[3];
			toggle(document.getElementById('tr_' + str), '#FFFFCC');

			if (jQuery.inArray(selectID, selected_id, selectStyle) == -1) {
				selected_id.push(selectID);
				selected_name.push(selectDESC);
				selected_style_name.push(selectStyle);
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == selectID) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
				selected_style_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			var style = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				style += selected_style_name[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			style = style.substr(0, style.length - 1);
			$('#txt_job_id').val(id);
			$('#txt_job_no').val(name);
			$('#txt_style_ref').val(style);
		}
	</script>
	<?
	if (str_replace("'", "", $data[1]) == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") $buyer_id = " and buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else $buyer_id = "";
		} else $buyer_id = "";
	} else $buyer_id = " and buyer_name='$data[1]'";

	if ($data[3] == 0) $brandCond = "";
	else $brandCond = " and brand_id=$data[3]";
	if ($data[4] == 0) $seasonCond = "";
	else $seasonCond = " and season_buyer_wise=$data[4]";
	if ($data[5] == 0) $seasonYearCond = "";
	else $seasonYearCond = " and season_year=$data[5]";
	if ($db_type == 0) {
		if (str_replace("'", "", $data[2]) != 0) $year_cond = " and year(insert_date)=" . str_replace("'", "", $data[2]) . "";
		else $year_cond = "";
	} else {
		if (str_replace("'", "", $data[2]) != 0) $year_cond = " and to_char(insert_date,'YYYY')=" . str_replace("'", "", $data[2]) . "";
		else $year_cond = "";
	}

	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ", "id", "team_member_name");
	$teamMemberArr = return_library_array("select id,team_leader_name from lib_marketing_team ", "id", "team_leader_name");

	$sql = "select id, job_no, job_no_prefix_num, style_ref_no, product_dept, dealing_marchant, team_leader from wo_po_details_master where status_active=1 and is_deleted=0 $company_id $buyer_id $year_cond $brandCond $seasonCond $seasonYearCond group by id, job_no, job_no_prefix_num, style_ref_no, product_dept, dealing_marchant, team_leader order by id DESC ";

	//echo $sql;die;

	$arr = array(2 => $product_dept, 3 => $marchentrArr, 4 => $teamMemberArr);
	echo  create_list_view("list_view", "Job No,Style Ref.,Prod. Dept.,Marchant,Team Name", "100,110,110,150,150", "680", "360", 0, $sql, "js_set_value", "id,job_no_prefix_num,style_ref_no", "", 1, "0,0,product_dept,dealing_marchant,team_leader", $arr, "job_no_prefix_num,style_ref_no,product_dept,dealing_marchant,team_leader", "", 'setFilterGrid("list_view",-1);', '0,0,0,0,0', '', 1);
	echo "<input type='hidden' id='txt_job_id' />";
	echo "<input type='hidden' id='txt_job_no' />";
	echo "<input type='hidden' id='txt_style_ref' />";
	exit();
}

if ($action == "po_no_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data = explode('_', $data);
	//print_r ($data);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('list_view').rows.length;
			tbl_row_count = tbl_row_count - 0;
			for (var i = 1; i <= tbl_row_count; i++) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value(functionParam);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(strCon) {
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			toggle(document.getElementById('tr_' + str), '#FFFFCC');

			if (jQuery.inArray(selectID, selected_id) == -1) {
				selected_id.push(selectID);
				selected_name.push(selectDESC);
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == selectID) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			var job = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
		}
	</script>
	<?
	if ($data[0] == 0) $company_name = "";
	else $company_name = " and a.company_name='$data[0]'";
	if (str_replace("'", "", $data[1]) == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") $buyer_id = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else $buyer_id = "";
		} else $buyer_id = "";
	} else $buyer_id = " and a.buyer_name='$data[1]'";

	if ($data[3] == 0) $brandCond = "";
	else $brandCond = " and a.brand_id=$data[3]";
	if ($data[4] == 0) $seasonCond = "";
	else $seasonCond = " and a.season_buyer_wise=$data[4]";
	if ($data[5] == 0) $seasonYearCond = "";
	else $seasonYearCond = " and a.season_year=$data[5]";
	if ($db_type == 0) {
		if (str_replace("'", "", $data[3]) != 0) $year_cond = " and year(a.insert_date)=" . str_replace("'", "", $data[3]) . "";
		else $year_cond = "";
	} else {
		if (str_replace("'", "", $data[3]) != 0) $year_cond = " and to_char(a.insert_date,'YYYY')=" . str_replace("'", "", $data[3]) . "";
		else $year_cond = "";
	}

	$sql = "select b.id, b.po_number, b.job_no_mst, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down  b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 $company_name $buyer_name $year_cond $brandCond $seasonCond $seasonYearCond group by b.id, b.po_number, b.job_no_mst, b.pub_shipment_date order by b.id DESC";

	//echo  $sql;die;
	$arr = array(3 => $garments_item);
	echo  create_list_view("list_view", "PO No.,Job No.,Pub Shipment Date", "100,100,80", "400", "360", 0, $sql, "js_set_value", "id,po_number", "", 1, "0,0,0", $arr, "po_number,job_no_mst,pub_shipment_date", "", 'setFilterGrid("list_view",-1);', '0,0,3', '', 1);
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

$tmplte = explode("**", $data);
if ($tmplte[0] == "viewtemplate") $template = $tmplte[1];
else $template = $lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template == "") $template = 1;


if ($action == "report_generate") {
	$company_library = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
	$buyer_short_name_library = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	//$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
	$item_library = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
	$team_leader_name_arr = return_library_array("select id,team_leader_name from lib_marketing_team", 'id', 'team_leader_name');
	$country_name_library = return_library_array("select id, country_name from lib_country", "id", "country_name");
	//$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
	$lib_supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$company_name = str_replace("'", "", $cbo_company_name);
	$serch_by = str_replace("'", "", $cbo_search_by);
	$buyer_id_cond = "";
	if (str_replace("'", "", $cbo_buyer_name) == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				$buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else $buyer_id_cond = "";
		} else $buyer_id_cond = "";
	} else $buyer_id_cond = " and a.buyer_name=$cbo_buyer_name"; //.str_replace("'","",$cbo_buyer_name)

	//condition add
	if ($db_type == 0) {
		if (str_replace("'", "", $cbo_year) != 0) $year_cond = " and year(a.insert_date)=" . str_replace("'", "", $cbo_year) . "";
		else $year_cond = "";
	} else {
		if (str_replace("'", "", $cbo_year) != 0) $year_cond = " and to_char(a.insert_date,'YYYY')=" . str_replace("'", "", $cbo_year) . "";
		else $year_cond = "";
	}

	$txt_job_no = str_replace("'", "", $txt_job_no);
	$txt_job_no = trim($txt_job_no);
	if ($txt_job_no != "" || $txt_job_no != 0) $jobcond = "and a.job_no_prefix_num='" . $txt_job_no . "'";
	else $jobcond = "";

	if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond = "";
	else $item_group_cond = "and e.trim_group in(" . str_replace("'", "", $cbo_item_group) . ")";

	$date_type = str_replace("'", "", $cbo_date_type);
	$start_date = str_replace("'", "", trim($txt_date_from));
	$end_date = str_replace("'", "", trim($txt_date_to));

	$season_year = str_replace("'", "", $cbo_season_year);
	$brand_id = str_replace("'", "", $cbo_brand_id);
	$season_name = str_replace("'", "", $cbo_season_name);
	$cbo_team_leader = str_replace("'", "", $cbo_team_leader);
	$product_dept_id = str_replace("'", "", $cbo_dept);
	if ($cbo_team_leader > 0) $team_leader_cond = "and a.team_leader in ($cbo_team_leader)";
	else $team_leader_cond = "";
	if (trim($product_dept_id) > 0) $product_dept_cond = "and a.product_dept in ($product_dept_id)";
	else $product_dept_cond = "";
	//echo $cbo_team_leader.'D'; $team_leader_cond $product_dept_cond


	if ($brand_id > 0) $brand_cond = "and a.brand_id='" . $brand_id . "'";
	else $brand_cond = "";
	if ($season_year > 0) $season_year_cond = "and a.season_year='" . $season_year . "'";
	else $season_year_cond = "";
	if ($season_name > 0) $season_buyer_wise_cond = "and a.season_buyer_wise='" . $season_name . "'";
	else $season_buyer_wise_cond = "";

	$date_cond = '';
	if ($date_type == 1) {
		if ($start_date != "" && $end_date != "") $date_cond = "and c.country_ship_date between '$start_date' and '$end_date'";
		else $date_cond = "";
	} else if ($date_type == 2) {
		if ($start_date == "" && $end_date == "") $date_cond = "";
		else $date_cond = "and b.pub_shipment_date between '$start_date' and '$end_date'";
	} else if ($date_type == 3) {
		if ($start_date == "" && $end_date == "") $date_cond = "";
		else $date_cond = "and b.shipment_date between '$start_date' and '$end_date'";
	} else if ($date_type == 4) {
		if ($db_type == 0) {
			if ($start_date == "" && $end_date == "") $date_cond = "";
			else $date_cond = "and b.insert_date between '" . $start_date . "' and '" . $end_date . " 23:59:59'";
		} else {
			if ($start_date == "" && $end_date == "") $date_cond = "";
			else $date_cond = "and b.insert_date between '" . $start_date . "' and '" . $end_date . " 11:59:59 PM'";
		}
	}

	if (str_replace("'", "", $hidd_job_id) != "")  $jobcond = "and a.id in(" . str_replace("'", "", $hidd_job_id) . ")";
	else  if (str_replace("'", "", $txt_job_no) == "") $jobcond = "";
	else $jobcond = "and a.job_no_prefix_num like '%" . str_replace("'", "", $txt_job_no) . "%' ";

	if (str_replace("'", "", $hidd_job_id) != "")  $style_ref_cond = "and a.id in(" . str_replace("'", "", $hidd_job_id) . ")";
	else  if (str_replace("'", "", $txt_style_ref) == "") $style_ref_cond = "";
	else $style_ref_cond = "and a.style_ref_no like '%" . str_replace("'", "", $txt_style_ref) . "%' ";

	if (str_replace("'", "", $hidd_po_id) != "") {
		$ordercond = "and b.id in (" . str_replace("'", "", $hidd_po_id) . ")";
		$jobcond = "";
	} else if (str_replace("'", "", $txt_order_no) == "") $ordercond = "";
	else $ordercond = "and b.po_number like '%" . str_replace("'", "", $txt_order_no) . "%' ";

	//echo $ordercond.'system'.$jobcond.'rah';
	$file_no = str_replace("'", "", $txt_file_no);
	$internal_ref = str_replace("'", "", $txt_internal_ref);
	if ($file_no == "") $file_no_cond = "";
	else $file_no_cond = " and b.file_no='" . trim($file_no) . "' ";
	if ($internal_ref == "") $internal_ref_cond = "";
	else $internal_ref_cond = " and b.grouping='" . trim($internal_ref) . "' ";
	//echo $file_no_cond.'=='.$internal_ref_cond;die;
	$print_report_format = return_field_value("format_id", "lib_report_template", "template_name =" . $company_name . " and module_id=2 and report_id=43 and is_deleted=0 and status_active=1");
	$print_button = explode(",", $print_report_format);

	if ($print_button[0] == 50) $precost_button = "preCostRpt";
	else if ($print_button[0] == 25) $precost_button = "budgetsheet2";
	else if ($print_button[0] == 730) $precost_button = "budgetsheet";
	else if ($print_button[0] == 51) $precost_button = "preCostRpt2";
	else if ($print_button[0] == 52) $precost_button = "bomRpt";
	else if ($print_button[0] == 63) $precost_button = "bomRpt2";
	else if ($print_button[0] == 156) $precost_button = "accessories_details";
	else if ($print_button[0] == 157) $precost_button = "accessories_details2";
	else if ($print_button[0] == 158) $precost_button = "preCostRptWoven";
	else if ($print_button[0] == 159) $precost_button = "bomRptWoven";
	else if ($print_button[0] == 170) $precost_button = "preCostRpt3";
	else if ($print_button[0] == 171) $precost_button = "preCostRpt4";
	else if ($print_button[0] == 142) $precost_button = "preCostRptBpkW";
	else if ($print_button[0] == 192) $precost_button = "checkListRpt";
	else if ($print_button[0] == 197) $precost_button = "bomRpt3";
	else if ($print_button[0] == 211) $precost_button = "mo_sheet";
	else if ($print_button[0] == 221) $precost_button = "fabric_cost_detail";
	else if ($print_button[0] == 173) $precost_button = "preCostRpt5";
	else if ($print_button[0] == 238) $precost_button = "summary";
	else if ($print_button[0] == 215) $precost_button = "budget3_details";
	else if ($print_button[0] == 270) $precost_button = "preCostRpt6";
	else if ($print_button[0] == 769) $precost_button = "preCostRpt7";
	else  $precost_button = "";

	$print_report_format_v3 = return_field_value("format_id", "lib_report_template", "template_name =" . $company_name . " and module_id=2 and report_id=161 and is_deleted=0 and status_active=1");
	$print_button_v3 = explode(",", $print_report_format_v3);

	if ($print_button_v3[0] == 50) $action_v3 = "preCostRpt";

	if ($print_button_v3[0] == 51) $action_v3 = "preCostRpt2";
	if ($print_button_v3[0] == 52) $action_v3 = "bomRpt";
	if ($print_button_v3[0] == 63) $action_v3 = "bomRpt2";
	if ($print_button_v3[0] == 156) $action_v3 = "accessories_details";
	if ($print_button_v3[0] == 157) $action_v3 = "accessories_details2";
	if ($print_button_v3[0] == 158) $action_v3 = "preCostRptWoven";
	if ($print_button_v3[0] == 159) $action_v3 = "bomRptWoven";
	if ($print_button_v3[0] == 170) $action_v3 = "preCostRpt3";
	if ($print_button_v3[0] == 171) $action_v3 = "preCostRpt4";
	if ($print_button_v3[0] == 142) $action_v3 = "preCostRptBpkW";
	if ($print_button_v3[0] == 192) $action_v3 = "checkListRpt";
	if ($print_button_v3[0] == 197) $action_v3 = "bomRpt3";
	if ($print_button_v3[0] == 211) $action_v3 = "mo_sheet";
	if ($print_button_v3[0] == 221) $action_v3 = "fabric_cost_detail";
	if ($print_button_v3[0] == 173) $action_v3 = "preCostRpt5";
	if ($print_button_v3[0] == 238) $action_v3 = "summary";
	if ($print_button_v3[0] == 215) $action_v3 = "budget3_details";
	if ($print_button_v3[0] == 270) $action_v3 = "preCostRpt6";
	if ($print_button_v3[0] == 581) $action_v3 = "costsheet";
	if ($print_button_v3[0] == 730) $action_v3 = "budgetsheet";
	if ($print_button_v3[0] == 759) $action_v3 = "materialSheet";
	if ($print_button_v3[0] == 351) $action_v3 = "bomRpt4";
	if ($print_button_v3[0] == 268) $action_v3 = "budget_4";
	if ($print_button_v3[0] == 381) $action_v3 = "mo_sheet_2";
	if ($print_button_v3[0] == 405) $action_v3 = "materialSheet2";
	if ($print_button_v3[0] == 765) $action_v3 = "bomRpt5";
	if ($print_button_v3[0] == 403) $action_v3 = "mo_sheet_3";

	//Precost v2 print button.........................................................
	$pre_cost2_print_button_arr=return_library_array( "select template_name,format_id from lib_report_template where module_id = 2 and report_id = 43 and is_deleted = 0 and status_active=1", "template_name", "format_id"  );
	$woven_pre_cost2_print_button_arr=return_library_array( "select template_name,format_id from lib_report_template where module_id = 2 and report_id = 122 and is_deleted = 0 and status_active=1", "template_name", "format_id"  );
	list($first_print_button)=explode(',',$pre_cost2_print_button_arr[$company_name]);
	list($woven_first_print_button)=explode(',',$woven_pre_cost2_print_button_arr[$company_name]);
	$print_button_action_arr=array(50=>'preCostRpt',51=>'preCostRpt2',52=>'bomRpt',63=>'bomRpt2',156=>'accessories_details',157=>'accessories_details2',158=>'preCostRptWoven',159=>'bomRptWoven',170=>'preCostRpt3',171=>'preCostRpt4',142=>'preCostRptBpkW',192=>'checkListRpt',197=>'bomRpt3');
	$woven_print_button_action_arr=array(311=>'bom_epm_woven',51=>'preCostRpt2',158=>'preCostRptWoven',159=>'bomRptWoven',192=>'checkListRpt',307=>'basic_cost',313=>'mkt_source_cost',381=>'mo_sheet_2',260=>'bomRptWoven_2',761=>'bom_pcs_woven',403=>'mo_sheet_3',170=>'preCostRpt3',473=>'slgCostRpt',770=>'bom_pcs_woven2');
	$woven_print_button_action = $woven_print_button_action_arr[$woven_first_print_button];

	if (str_replace("'", "", $cbo_search_by) == 1) {
		ob_start();
	?>
		<div style="width:2920px">
			<fieldset style="width:100%;">
				<table width="2920">
					<tr class="form_caption">
						<td colspan="30" align="center">Accessories Followup Report</td>
					</tr>
					<tr class="form_caption">
						<td colspan="30" align="center"><? echo $company_library[$company_name]; ?></td>
					</tr>
				</table>
				<table class="rpt_table" width="2910" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="50">Buyer</th>
						<th width="100">Job No</th>
						<th width="100">Prod. Dept</th>
						<th width="100">Style Ref</th>
						<th width="100">Team Leader</th>
						<th width="100">Internal Ref</th>
						<th width="100">File No</th>

						<th width="90">Order No</th>
						<th width="80">Order Qnty</th>
						<th width="50">UOM</th>
						<th width="80">Qnty (Pcs)</th>
						<th width="80">Shipment Date</th>
						<th width="80">Shipping Status</th>
						<th width="100">Trims Name</th>
						<th width="100">Remark</th>
						<th width="100">Brand/Sup Ref</th>
						<th width="60">Appr Req.</th>
						<th width="80">Approve Status</th>
						<th width="100">Item Entry Date</th>
						<th width="100">Req Qnty</th>
						<th width="100">Pre Costing Value</th>
						<th width="90">WO Qnty</th>
						<th width="60">Trims UOM</th>
						<th width="100">WO Value</th>
						<th width="150">Supplier</th>
						<th width="70">WO Delay Days</th>
						<th width="70">Delivery Date</th>
						<th width="90">In-House Qnty</th>
						<th width="90">In-House Value</th>
						<th width="90">Receive Balance</th>
						<th width="90">Issue to Prod.</th>
						<th>Left Over/Balance</th>
					</thead>
				</table>
				<div style="width:2930px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="2910" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<?
						$item_arr = array();
						$conversion_factor_array = array();
						$conversion_factor = sql_select("select id,trim_uom,order_uom,conversion_factor from lib_item_group where status_active=1 and is_deleted=0 ");
						foreach ($conversion_factor as $row_f) {
							$conversion_factor_array[$row_f[csf('id')]]['con_factor'] = $row_f[csf('conversion_factor')];
							$conversion_factor_array[$row_f[csf('id')]]['cons_uom'] = $row_f[csf('trim_uom')];
							$item_arr[$row_f[csf('id')]]['order_uom'] = $row_f[csf('order_uom')];
						}
						unset($conversion_factor);

						
						$today = date("Y-m-d");

						$txt_style_arr = explode(",", str_replace("'", "", $txt_style_ref));

						$n = 0;
						$style_arr = "";
						foreach ($txt_style_arr as $row) {
							if ($n == 0) {
								$style_arr .= "'" . $row . "'";
								$n++;
							} else {
								$style_arr .= ",'" . $row . "'";
							}
						}


						$condition = new condition();
						if (str_replace("'", "", $company_name) > 0) {
							$condition->company_name("=$company_name");
						}
						if (str_replace("'", "", $cbo_buyer_name) > 0) {
							$condition->buyer_name("=$cbo_buyer_name");
						}
						if (str_replace("'", "", $txt_job_no) != '') {
							$condition->job_no_prefix_num("in ($txt_job_no)");
						}
						if (str_replace("'", "", $txt_style_ref) != '') {
							$condition->style_ref_no("in ($style_arr)");
						}

						if (str_replace("'", "", $txt_order_no) != '') {
							$condition->po_number("like '%" . str_replace("'", "", $txt_order_no) . "%'");
						}

						if (str_replace("'", "", $txt_file_no) != '') {
							$condition->file_no("='" . str_replace("'", "", $txt_file_no) . "'");
						}

						if (str_replace("'", "", $txt_internal_ref) != '') {
							$condition->grouping("='" . str_replace("'", "", $txt_internal_ref) . "'");
						}

						if (str_replace("'", "", $txt_date_from) != "" && str_replace("'", "", $txt_date_to) != "") {
							$start_date = (str_replace("'", "", $txt_date_from));
							$end_date = (str_replace("'", "", $txt_date_to));
							//$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
							if ($date_type == 1) {
								$condition->country_ship_date(" between '$start_date' and '$end_date'");
							}
							if ($date_type == 2) {
								$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
							}
						}

						$condition->init();
						$trim = new trims($condition);
						//echo $trim->getQuery(); die;
						// $trim_group_qty_arr=$trim->getQtyArray_by_orderAndPrecostdtlsid();
						$trim_group_qty_arr = $trim->getQtyArray_by_orderCountryAndPrecostdtlsid();

						$sql_qry = "SELECT a.buyer_name,a.garments_nature, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.team_leader, a.product_dept, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, sum(c.order_quantity) as order_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, a.total_set_qnty, b.pub_shipment_date, b.shiping_status, d.costing_per, e.id as trim_dtla_id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, d.costing_date, d.costing_per,d.entry_from FROM wo_po_details_master a JOIN wo_po_break_down b ON a.id = b.job_id JOIN wo_po_color_size_breakdown c ON a.id = c.job_id AND b.id = c.po_break_down_id AND c.job_id = b.job_id JOIN wo_pre_cost_mst d ON a.id = d.job_id AND d.job_id = b.job_id AND d.job_id = c.job_id JOIN wo_pre_cost_trim_cost_dtls e ON e.job_id = d.job_id AND e.job_id = b.job_id AND e.job_id = c.job_id LEFT JOIN wo_pre_cost_trim_co_cons_dtls f ON c.job_id = f.job_id AND c.po_break_down_id = f.po_break_down_id AND f.job_id = e.job_id AND e.id = f.wo_pre_cost_trim_cost_dtls_id AND f.job_id = b.job_id AND f.job_id = a.id where f.cons > 0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name', 'buyer_id')." $date_cond $style_ref_cond $year_cond $jobcond $ordercond $file_no_cond $internal_ref_cond $brand_cond $season_year_cond $season_buyer_wise_cond $team_leader_cond $product_dept_cond group by a.buyer_name,a.garments_nature, a.job_no, a.job_no_prefix_num, a.style_ref_no,a.team_leader,a.product_dept, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, a.total_set_qnty, b.pub_shipment_date, b.shiping_status, d.costing_per, e.id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date,d.costing_date,d.costing_per,d.entry_from order by b.id, e.trim_group";

						//echo $sql_qry;die;

						$sql_query = sql_select($sql_qry); $po_data_arr = array(); $po_id_string = ""; $poIdArray=array(); $trimID = '';
						if(count($sql_query)<1)
						{
							echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
							die;
						}
						
						foreach ($sql_query as $row) {
							$trimID .= $row[csf('trim_dtla_id')] . ",";
							$poIdArray[$row[csf('id')]]=$row[csf('id')];
						}
						
						$con = connect();
						execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1) and ENTRY_FORM=31");
						oci_commit($con);
						
						fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 31, 1, $poIdArray, $empty_arr);//Po ID
						disconnect($con);
						
						$sql_po_qty_country_wise_arr = array(); $po_job_arr = array();
						$sql_po_qty_country_wise = sql_select("SELECT b.id, b.job_no_mst, c.country_id, c.order_quantity as order_quantity_pcs, b.po_quantity from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,gbl_temp_engine d where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and c.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $year_cond $jobcond $ordercond"); 
					
						foreach ($sql_po_qty_country_wise as $sql_po_qty_country_wise_row) {
							/* $sql_po_qty_country_wise_arr[$sql_po_qty_country_wise_row[csf('id')]][$sql_po_qty_country_wise_row[csf('country_id')]] += $sql_po_qty_country_wise_row[csf('order_quantity_pcs')];
							$po_job_arr[$sql_po_qty_country_wise_row[csf('id')]] = $sql_po_qty_country_wise_row[csf('job_no_mst')]; */
							$po_qnty_arr[$sql_po_qty_country_wise_row[csf('id')]]['order_quantity_pcs'] += $sql_po_qty_country_wise_row[csf('order_quantity_pcs')];
							$po_qnty_arr[$sql_po_qty_country_wise_row[csf('id')]]['order_quantity'] = $sql_po_qty_country_wise_row[csf('po_quantity')];
						}
						unset($sql_po_qty_country_wise);
						
					 	$sqlcountry = "select a.po_break_down_id, a.wo_pre_cost_trim_cost_dtls_id, a.country_id from wo_pre_cost_trim_co_cons_dtls a, gbl_temp_engine d where  a.cons > 0 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=1";

						$sqlcountryres = sql_select($sqlcountry);
						$trimCountryArr = array();
						foreach ($sqlcountryres as $row) {
							$trimCountryArr[$row[csf('po_break_down_id')]][$row[csf('wo_pre_cost_trim_cost_dtls_id')]] .= $row[csf('country_id')] . ',';
						}
						unset($sqlcountryres);
						
						$app_sql=sql_select("select a.job_no_mst, a.accessories_type_id, a.approval_status from wo_po_trims_approval_info a, gbl_temp_engine d where a.is_deleted=0 and a.status_active=1 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=1");
						
						$app_status_arr = array();
						foreach ($app_sql as $row) {
							$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]] = $row[csf("approval_status")];
						}
						unset($app_sql);

						//print_r($trimCountryArr); die;

						$tot_rows = count($sql_query);
						$i = 1; $jobwisedata = array();
						foreach ($sql_query as $row) {
							$po_qty = 0; $req_qnty = 0; $req_value = 0;
							$countryId = implode(",", array_filter(array_unique(explode(",", $trimCountryArr[$row[csf('id')]][$row[csf('trim_dtla_id')]]))));

							if ($countryId == 0 || $countryId == '') {
								$req_qnty += $trim_group_qty_arr[$row[csf('id')]][$row[csf('country_id')]][$row[csf('trim_dtla_id')]];
							} else {
								$country_id = array_unique(explode(",", $countryId));
								foreach ($country_id as $c_id) {
									$req_qnty += $trim_group_qty_arr[$row[csf('id')]][$c_id][$row[csf('trim_dtla_id')]];
								}
							}
							$req_value = $row[csf('rate')] * $req_qnty;

							$jobwisedata[$row[csf('job_no')]]['po_id'] .= $row[csf('id')] . ",";

							$po_data_arr[$row[csf('id')]][job_no] = $row[csf('job_no')];
							$po_data_arr[$row[csf('id')]][garments_nature] = $row[csf('garments_nature')];
							$po_data_arr[$row[csf('id')]][costing_date] = $row[csf('costing_date')];
							$po_data_arr[$row[csf('id')]][costing_per] = $row[csf('costing_per')];
							$po_data_arr[$row[csf('id')]][buyer_name] = $row[csf('buyer_name')];
							$po_data_arr[$row[csf('id')]][job_no_prefix_num] = $row[csf('job_no_prefix_num')];
							$po_data_arr[$row[csf('id')]][style_ref_no] = $row[csf('style_ref_no')];
							$po_data_arr[$row[csf('id')]][team_leader] = $row[csf('team_leader')];
							$po_data_arr[$row[csf('id')]][product_dept_id] = $row[csf('product_dept')];
							$po_data_arr[$row[csf('id')]][entry_form] = $row[csf('entry_form')];
							$po_data_arr[$row[csf('id')]][grouping] = $row[csf('grouping')];
							$po_data_arr[$row[csf('id')]][file_no] = $row[csf('file_no')];
							$po_data_arr[$row[csf('id')]][order_uom] = $row[csf('order_uom')];
							$po_data_arr[$row[csf('id')]][po_id] = $row[csf('id')];
							$po_data_arr[$row[csf('id')]][po_number] = $row[csf('po_number')];
							$po_data_arr[$row[csf('id')]][order_quantity_set] = $po_qnty_arr[$row[csf('id')]]['order_quantity'];
							$po_data_arr[$row[csf('id')]][order_quantity] = $po_qnty_arr[$row[csf('id')]]['order_quantity_pcs'];
							$po_data_arr[$row[csf('id')]][pub_shipment_date] = change_date_format($row[csf('pub_shipment_date')]);
							$po_data_arr[$row[csf('id')]][shiping_status] = $row[csf('shiping_status')];
							$po_id_string .= $row[csf('id')] . ",";

							$po_data_arr[$row[csf('id')]][trim_dtla_id][$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')]; // for rowspan
							$po_data_arr[$row[csf('id')]][trim_group][$row[csf('trim_group')]] = $row[csf('trim_group')];


							$po_data_arr[$row[csf('id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')]; // for rowspannn
							$po_data_arr[$row[csf('id')]][trim_group_dtls][$row[csf('trim_dtla_id')]] = $row[csf('trim_group')];


							$po_data_arr[$row[csf('id')]][remark][$row[csf('trim_dtla_id')]] = $row[csf('remark')];
							$po_data_arr[$row[csf('id')]][entry_form][$row[csf('trim_dtla_id')]] = $row[csf('entry_form')];
							$po_data_arr[$row[csf('id')]][brand_sup_ref][$row[csf('trim_dtla_id')]] = $row[csf('brand_sup_ref')];
							$po_data_arr[$row[csf('id')]][apvl_req][$row[csf('trim_dtla_id')]] = $row[csf('apvl_req')];
							$po_data_arr[$row[csf('id')]][insert_date][$row[csf('trim_dtla_id')]] = $row[csf('insert_date')];
							$po_data_arr[$row[csf('id')]][req_qnty][$row[csf('trim_dtla_id')]] = $req_qnty;
							$po_data_arr[$row[csf('id')]][req_value][$row[csf('trim_dtla_id')]] = $req_value;
							$po_data_arr[$row[csf('id')]][cons_uom][$row[csf('trim_dtla_id')]] = $conversion_factor_array[$row[csf('trim_group')]]['cons_uom']; //$row[csf('cons_uom')];

							$po_data_arr[$row[csf('id')]][trim_group_from][$row[csf('trim_dtla_id')]] = "Pre_cost";

							$po_data_arr[$row[csf('id')]][rate][$row[csf('trim_dtla_id')]] = $row[csf('rate')];
							$po_data_arr[$row[csf('id')]][description][$row[csf('trim_dtla_id')]] = $row[csf('description')];
							$po_data_arr[$row[csf('id')]][country_id][$row[csf('trim_dtla_id')]] = $countryId; //implode(",",array_unique(explode(",",$row[csf('country_id')])));
						}
						unset($sql_query);
						//echo "<pre>";print_r($po_data_arr);die;
					
						$transfer_sql_in = sql_select("SELECT  a.from_order_id, a.to_order_id, b.item_group, b.transfer_qnty as qnty FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, gbl_temp_engine d WHERE a.id = b.mst_id and a.to_order_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ");



						$transfer_in_data = array();
						foreach ($transfer_sql_in as $row) {
							$transfer_in_data[$row[csf('to_order_id')]][$row[csf('item_group')]] += $row[csf('qnty')];
						}
						unset($transfer_sql_in);
						
						$transfer_sql_out = sql_select("SELECT  a.from_order_id, a.to_order_id, b.item_group, b.transfer_qnty as qnty FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, gbl_temp_engine d WHERE a.id = b.mst_id and a.from_order_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

						$transfer_out_data = array();
						foreach ($transfer_sql_out as $row) {
							$transfer_out_data[$row[csf('from_order_id')]][$row[csf('item_group')]] += $row[csf('qnty')];
						}
						unset($transfer_sql_out);

						if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond2 = ""; else $item_group_cond2 = "and b.trim_group in(" . str_replace("'", "", $cbo_item_group) . ")";

						$wo_sql_without_precost = sql_select("select min(a.booking_date) as booking_date, b.job_no,b.delivery_date, LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, LISTAGG(CAST(a.supplier_id || '**' || a.pay_mode AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(nullif(b.amount , 0)/nullif(b.exchange_rate , 0)) as amount, sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b, gbl_temp_engine d where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name and b.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=1 $item_group_cond2 and b.is_workable=1 group by b.po_break_down_id,b.delivery_date, b.trim_group, b.job_no, b.pre_cost_fabric_cost_dtls_id"); //and item_from_precost=2

						$style_data_arr1 = array();
						foreach ($wo_sql_without_precost as $wo_row_without_precost) {
							if (array_key_exists($wo_row_without_precost[csf('po_break_down_id')], $po_data_arr)) {
								$conversion_factor_rate = $conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['con_factor'];
								$cons_uom = $conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['cons_uom'];
								$booking_no = $wo_row_without_precost[csf('booking_no')];
								$supplier_id = $wo_row_without_precost[csf('supplier_id')];
								$wo_qnty = $wo_row_without_precost[csf('wo_qnty')]; //*$conversion_factor_rate;
								$amount = $wo_row_without_precost[csf('amount')];
								$wo_date = $wo_row_without_precost[csf('booking_date')];

								if ($wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] == "" || $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] == 0) {
									$trim_dtla_id = max($po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_dtla_id]) + 1;
									$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_dtla_id][$trim_dtla_id] = $trim_dtla_id; // for rowspan
									$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group][$wo_row_without_precost[csf('trim_group')]] = $wo_row_without_precost[csf('trim_group')];
									$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id] = $trim_dtla_id; // for rowspannn
									$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group_dtls][$trim_dtla_id] = $wo_row_without_precost[csf('trim_group')];
									$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][cons_uom][$trim_dtla_id] = $cons_uom;

									$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group_from][$trim_dtla_id] = "Booking Without Pre_cost";
								} else {
									$trim_dtla_id = $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')]; //delivery_date
								}
								$delivery_date = $wo_row_without_precost[csf('delivery_date')];

								$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_qnty][$trim_dtla_id] += $wo_qnty;
								$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][amount][$trim_dtla_id] += $amount;
								$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_date][$trim_dtla_id] = $wo_date;
								$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][delivery_date][$trim_dtla_id] .= change_date_format($delivery_date) . ',';
								//	echo $delivery_date.'d';
								$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_qnty_trim_group][$wo_row_without_precost[csf('trim_group')]] += $wo_qnty;

								$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][booking_no][$trim_dtla_id] .= $booking_no . ',';
								$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][supplier_id][$trim_dtla_id] = $supplier_id;
								$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][conversion_factor_rate][$trim_dtla_id] = $conversion_factor_rate;
							}
						}
						unset($wo_sql_without_precost);
						//print_r($bookingNO_data_arr);

						if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond3 = ""; else $item_group_cond3 = "and a.item_group_id in(" . str_replace("'", "", $cbo_item_group) . ")";

						$wo_sql_without_precost = array();
						
						$receive_qty_data = sql_select("select b.po_breakdown_id, a.item_group_id, sum(b.quantity) as quantity, a.rate from inv_receive_master c,product_details_master d, inv_trims_entry_dtls a, order_wise_pro_details b, gbl_temp_engine e where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and d.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and b.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=31 and e.ref_from=1 $item_group_cond3 group by b.po_breakdown_id, a.item_group_id,a.rate order by a.item_group_id ");

						foreach ($receive_qty_data as $row) {
							if (array_key_exists($row[csf('po_breakdown_id')], $po_data_arr)) {
								if ($po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]] == "" || $po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]] == 0) {
									$cons_uom = $conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];
									$trim_dtla_id = max($po_data_arr[$row[csf('po_breakdown_id')]][trim_dtla_id]) + 1;
									$po_data_arr[$row[csf('po_breakdown_id')]][trim_dtla_id][$trim_dtla_id] = $trim_dtla_id; // for rowspan
									$po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]] = $row[csf('item_group_id')];
									$po_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$trim_dtla_id] = $trim_dtla_id; // for rowspannn
									$po_data_arr[$row[csf('po_breakdown_id')]][trim_group_dtls][$trim_dtla_id] = $row[csf('item_group_id')];
									$po_data_arr[$row[csf('po_breakdown_id')]][cons_uom][$trim_dtla_id] = $cons_uom;
									$po_data_arr[$row[csf('po_breakdown_id')]][trim_group_from][$trim_dtla_id] = "Trim Receive";
								}
								$po_data_arr[$row[csf('po_breakdown_id')]][inhouse_qnty][$row[csf('item_group_id')]] += $row[csf('quantity')];
								$po_data_arr[$row[csf('po_breakdown_id')]][inhouse_rate][$row[csf('item_group_id')]] = $row[csf('rate')];
								$po_data_arr[$row[csf('po_breakdown_id')]][inhouse_value][$row[csf('item_group_id')]] += $row[csf('quantity')] * $row[csf('rate')];
							}
						}
						unset($receive_qty_data);

						if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond4 = ""; else $item_group_cond4 = "and c.item_group_id in(" . str_replace("'", "", $cbo_item_group) . ")";

					
						$receive_rtn_qty_data = sql_select("select d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d, gbl_temp_engine e where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and b.transaction_type=3 and a.entry_form=49 and c.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=31 and e.ref_from=1 $item_group_cond4 group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");



						foreach ($receive_rtn_qty_data as $row) {
							if (array_key_exists($row[csf('po_breakdown_id')], $po_data_arr)) {
								$inhouse_rate = $po_data_arr[$row[csf('po_breakdown_id')]][inhouse_rate][$row[csf('item_group_id')]];
								$po_data_arr[$row[csf('po_breakdown_id')]][receive_rtn_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
								$po_data_arr[$row[csf('po_breakdown_id')]][receive_rtn_value][$row[csf('item_group_id')]] += $row[csf('quantity')] * $inhouse_rate;
							}
						}
						unset($receive_rtn_qty_data);

						$issue_rtn_qty_data = array();
						
						$issue_qty_data = sql_select("SELECT b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity,a.issue_qnty  from  inv_issue_master d,product_details_master p, inv_trims_issue_dtls a, order_wise_pro_details b, gbl_temp_engine e where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and p.item_category_id=4 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=31 and e.ref_from=1 $item_group_cond3 group by b.po_breakdown_id, a.item_group_id, a.issue_qnty");

						foreach ($issue_qty_data as $row) {
							if (array_key_exists($row[csf('po_breakdown_id')], $po_data_arr)) {
								$po_data_arr[$row[csf('po_breakdown_id')]][issue_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
								$po_data_arr[$row[csf('po_breakdown_id')]][issue_qnty_total][$row[csf('item_group_id')]] += $row[csf('issue_qnty')];
							}
						}
						unset($issue_qty_data);
						
						if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond5 = ""; else $item_group_cond5 = "and p.item_group_id in(" . str_replace("'", "", $cbo_item_group) . ")";
						$sql_issue_ret = ("select c.po_breakdown_id as po_id, p.item_group_id, SUM(c.quantity) as quantity
            from inv_transaction b, order_wise_pro_details c, product_details_master p, gbl_temp_engine e
            where c.entry_form=73 and p.id=b.prod_id  and b.id=c.trans_id and b.transaction_type=4 and p.item_category_id=4 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=31 and e.ref_from=1 $item_group_cond5 group by c.po_breakdown_id,p.item_group_id");
						$issue_result = sql_select($sql_issue_ret);
						$issue_qty_data_arr = array();
						foreach ($issue_result as $row) {
							$issue_qty_data_arr[$row[csf('po_id')]][issue_ret_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
						}
						unset($issue_result);
						
						$con = connect();
						execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1) and ENTRY_FORM=31");
						oci_commit($con);
						disconnect($con);
						
						$issue_qty_data = array(); $total_pre_costing_value = 0; $total_wo_value = 0; $summary_array = array();
						$i = 1;
						//echo "<pre>";print_r($po_data_arr);die;
						foreach ($po_data_arr as $key => $value) {
							$rowspan = count($value[trim_dtla_id]);
							// $rowspan=count($value[trim_group]);
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30" title="<? echo $po_qty; ?>" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
								<td width="50" rowspan="<? echo $rowspan; ?>">
									<p><? echo $buyer_short_name_library[$value[buyer_name]]; ?>&nbsp;</p>
								</td>
								<td width="100" align="center" rowspan="<? echo $rowspan; ?>">
									<p>
										<?php
										$buyer_name = $value[buyer_name];
										$costing_date = $value[costing_date];
										$costing_per = $value[costing_per];
										$style_ref_no = $value[style_ref_no];
										$po_id = rtrim($jobwisedata[$value[job_no]]['po_id'], ",");
										?>
										<a href='#report_details' onclick="generate_report_v3('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $style_ref_no; ?>','<? echo $buyer_name; ?>','<? echo $costing_date; ?>','<? echo $po_id; ?>','<? echo $action_v3; ?>');">
											<? echo $value[job_no_prefix_num]; ?>
										</a>

										&nbsp;
									</p>
								</td>
								<td width="100" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;">
									<p><? echo $product_dept[$value['product_dept_id']]; ?>&nbsp;</p>
								</td>
								<td width="100" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;">
									<p><? echo $value[style_ref_no]; ?>&nbsp;</p>
								</td>
								<td width="100" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;">
									<p><? echo $team_leader_name_arr[$value['team_leader']]; ?>&nbsp;</p>
								</td>
								<td width="100" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;">
									<p><? echo $value[grouping]; ?></p>
								</td>
								<td width="100" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;">
									<p><? echo $value[file_no]; ?></p>
								</td>
								<td width="90" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;">
									<p>

										<?php
											$buyer_name = $value[buyer_name];
											$costing_date = $value[costing_date];
											$costing_per = $value[costing_per];
											$style_ref_no = $value[style_ref_no];
											$garments_nature = $value[garments_nature];
											$po_id = rtrim($jobwisedata[$value[job_no]]['po_id'], ",");
										?>
										<? if($garments_nature==3){?>
											<a href='#report_details' onclick="generate_woven_report('<? echo $company_name; ?>','<? echo $value[job_no]; ?>', '<? echo $buyer_name; ?>','<? echo $style_ref_no; ?>','<? echo $costing_date; ?>','<? echo $po_id; ?>','<? echo $woven_print_button_action;?>');"> <? $po_number = $value[po_number]; echo $po_number;?></a>

										<? }else {?>
											<a href='#report_details' onclick="generate_report('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $precost_button; ?>');">
	
										<? 
											$po_number = $value[po_number];
											//$po_number=implode(",", $value[po_id]);
											echo $po_number;
										} ?>
										</a>&nbsp;
									</p>
								</td>
								<td width="80" align="right" rowspan="<? echo $rowspan; ?>">
									<p>
										<a href='#report_details' onclick="order_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $value[po_id]; ?>', '<? echo $value[buyer_name]; ?>' ,'order_qty_data');"><? echo fn_number_format($value[order_quantity_set], 0, '.', ''); ?>
										</a>

									</p>
								</td>

								<td width="50" align="center" rowspan="<? echo $rowspan; ?>">
									<p><? echo $unit_of_measurement[$value[order_uom]]; ?></p>
								</td>
								<td width="80" align="right" rowspan="<? echo $rowspan; ?>">
									<p><? echo fn_number_format($value[order_quantity], 0, '.', ''); ?></p>
								</td>
								<td width="80" align="center" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;">
									<p>
										<?
										$pub_shipment_date = $value[pub_shipment_date];
										echo $pub_shipment_date;
										?>
										&nbsp;
									</p>
								</td>
								<td width="80" align="center" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;"><?= $shipment_status[$value[shiping_status]]; ?></td>
								<? //$total_pre_costing_value=0;
								foreach ($value[trim_group] as $key_trim => $value_trim) {
									$gg = 1;
									$summary_array[trim_group][$key_trim] = $key_trim;
									foreach ($value[$key_trim] as $key_trim1 => $value_trim1) {
										$rowspannn = count($value[$key_trim]);
										// $rowspannn=1;
										if ($gg == 1) {


								?>
								<td width="100" title="<? echo $value[trim_group_from][$key_trim1];  ?>" style="word-break: break-all;">
									<p><? echo $item_library[$value[trim_group_dtls][$key_trim1]];?>&nbsp;</p>
								</td>
								<td width="100" title="<? //echo $value[trim_group_from][$key_trim1]; ?>">
									<p><? echo $value[remark][$key_trim1]; ?> </p>
								</td>

											<td width="100">
												<p>
													<?
													echo $value[brand_sup_ref][$key_trim1];
													//echo $row[csf('brand_sup_ref')];
													?>
													&nbsp;</p>
											</td>
											<td width="60" align="center">
												<p>
													<?

													if ($value[apvl_req][$key_trim1] == 1) echo "Yes";
													else echo "&nbsp;";
													?>
													&nbsp;
												</p>
											</td>
											<td width="80" align="center">

												<?
												if ($value[apvl_req][$key_trim1] == 1) {
													$app_status = $app_status_arr[$value[job_no]][$value[trim_group_dtls][$key_trim1]];
													$approved_status = $approval_status[$app_status];
													$summary_array[item_app][$key_trim][all] += 1;
													if ($app_status == 3) {
														$summary_array[item_app][$key_trim][app] += 1;
													}
												} else {
													$approved_status = "";
												}
												echo $approved_status;
												$bookingNos = rtrim($value[booking_no][$key_trim1], ',');
												?>


											</td>

											<td width="100" align="right">
												<p>
													<?
													$insert_date = explode(" ", $value[insert_date][$key_trim1]);
													echo change_date_format($insert_date[0], '', '', ''); //echo change_date_format($row[csf('pre_date')],'','',1);
													?></p>
											</td>
											<td width="100" align="right">
												<p>
													<a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $value[po_id]; ?>', '<? echo $value[buyer_name]; ?>','<? echo $value[rate][$key_trim1]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>' ,'<? echo $bookingNos; ?>','<? echo $value[description][$key_trim1]; ?>','<? echo $value[country_id][$key_trim1]; ?>','<? echo $value[trim_dtla_id][$key_trim1]; ?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
														<?
														$req_qty = fn_number_format($value[req_qnty][$key_trim1], 2, '.', '');
														echo $req_qty;
														$summary_array[req_qnty][$key_trim] += $value[req_qnty][$key_trim1];
														?>
													</a>
												</p>
											</td>

											<td width="100" align="right">
												<p>
													<?
													echo fn_number_format($value[req_value][$key_trim1], 2);
													$total_pre_costing_value += $value[req_value][$key_trim1];
													?>

												</p>
											</td>
											<?
											// $conversion_factor_rate=$conversion_factor_array[$row[csf('trim_group')]]['con_factor'];
											$wo_qnty = fn_number_format($value[wo_qnty][$key_trim1], 2, '.', '');
											if ($wo_qnty > $req_qty) {
												$color_wo = "red";
											} else if ($wo_qnty < $req_qty) {
												$color_wo = "yellow";
											} else {
												$color_wo = "";
											}

											$supplier_name_string = "";
											$supplier_id_arr = array_unique(explode(',', $value[supplier_id][$key_trim1]));
											foreach ($supplier_id_arr as $supplier_id_arr_key => $supplier_id_arr_value) {
												$ex_sup_data = explode("**", $supplier_id_arr_value);

												if ($ex_sup_data[1] == 3 || $ex_sup_data[1] == 5) $suplier_name_arr = $company_library;
												else $suplier_name_arr = $lib_supplier_arr;
												$supplier_name_string .= $suplier_name_arr[$ex_sup_data[0]] . ",";
											}

											$booking_no_arr = array_unique(explode(',', $bookingNos));
											//$booking_no_arr_d=implode(',',$booking_no_arr);
											//print $order_id.'='.	$trim_id;// $wo_qty_array[$order_id][$trim_id]['booking_no'];
											$main_booking_no_large_data = "";
											foreach ($booking_no_arr as $booking_no1) {
												//if($booking_no1>0)
												//{
												if ($main_booking_no_large_data == "") $main_booking_no_large_data = $booking_no1;
												else $main_booking_no_large_data .= "," . $booking_no1;
												//}
												//print($main_booking_no_large_data);
											}
											?>
											<td width="90" align="right" title="<? //echo 'conversion_factor='.$value[conversion_factor_rate][$key_trim1];
																				?>" bgcolor="<? echo $color_wo; ?>">
												<p><a href='#report_details' onclick="openmypage('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','<? echo $value[job_no]; ?>','<? echo $main_booking_no_large_data; ?>','<? echo $value[trim_dtla_id][$key_trim1]; ?>','booking_info');">
														<?
														//$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]['wo_qnty']
														echo fn_number_format($value[wo_qnty][$key_trim1], 2, '.', '');
														$summary_array[wo_qnty][$key_trim] += $value[wo_qnty][$key_trim1];
														?>
													</a></p>
											</td>
											<td width="60" title="Order UOM From Item Group" align="center">
												<p>
													<?

													echo $unit_of_measurement[$item_arr[$value[trim_group_dtls][$key_trim1]]['order_uom']];
													//echo $unit_of_measurement[$value[cons_uom][$key_trim1]];
													$summary_array[cons_uom][$key_trim] = $item_arr[$value[trim_group_dtls][$key_trim1]]['order_uom'];
													//$summary_array[cons_uom][$key_trim]=$value[cons_uom][$key_trim1];
													?></p>
											</td>
											<td width="100" align="right" title="<? echo fn_number_format($value[rate][$key_trim1], 2, '.', ''); ?>">
												<p>
													<?
													echo fn_number_format($value[amount][$key_trim1], 2, '.', '');
													$total_wo_value += $value[amount][$key_trim1];
													?>

												</p>
											</td>

											<td width="150" align="left">
												<p><? echo rtrim($supplier_name_string, ","); ?></p>
											</td>
											<td width="70" align="right" title="<? echo change_date_format($value[wo_date][$key_trim1]); ?>">
												<p>
													<?
													$tot = change_date_format($insert_date[0]);
													if ($value[wo_qnty][$key_trim1] <= 0) {
														$daysOnHand = datediff('d', $tot, $today);
													} else {
														$wo_date = $value[wo_date][$key_trim1];
														$wo_date = change_date_format($wo_date);
														$daysOnHand = datediff('d', $tot, $wo_date);;
													}
													echo $daysOnHand;
													$delivery_date_cal = rtrim($value[delivery_date][$key_trim1], ',');
													$delivery_date = implode(",", array_unique(explode(',', $delivery_date_cal)));

													?></p>
											</td>
											<td width="70" align="left">
												<p><? echo $delivery_date; ?></p>
											</td>
											<?
											//$po_data_arr[$row[csf('po_breakdown_id')]][receive_rtn_qty][$row[csf('item_group_id')]]
											//$transfer_out_data[$row[csf('item_group')]];
											// $transfer_in_data[$row[csf('item_group')]];

											$inhouse_qnty = $value[inhouse_qnty][$key_trim] + $transfer_in_data[$value[po_id]][$key_trim] - $value[receive_rtn_qty][$key_trim];
											$inhouse_rate = $value[inhouse_rate][$key_trim];
											//$inhouse_value=$inhouse_qnty*$inhouse_rate;
											$inhouse_value = ($value[inhouse_value][$key_trim] - $value[receive_rtn_value][$key_trim]) + ($transfer_in_data[$value[po_id]][$key_trim] * $inhouse_rate);

											$balance = $value[wo_qnty_trim_group][$key_trim] - $inhouse_qnty;
											$conv_rate = $conversion_factor_array[$value[trim_group_dtls][$key_trim1]]['con_factor'];
											$issue_qnty = $value[issue_qty][$key_trim];
											$issue_qnty_total = $value[issue_qnty_total][$key_trim];
											$issue_ret_qty = $issue_qty_data_arr[$key][issue_ret_qty][$key_trim];
											$tot_issue = ((($issue_qnty + $transfer_out_data[$value[po_id]][$key_trim]) - $issue_ret_qty) / $conv_rate);
											//echo $value[po_id].'='.$issue_qnty.'='.$transfer_out_data[$value[po_id]][$key_trim].'='.$issue_ret_qty.'<br>';
											//	$left_overqty=$inhouse_qnty-$tot_issue;//($issue_qnty-$issue_ret_qty);
											$left_overqty = $inhouse_qnty - $issue_qnty;

											$summary_array[inhouse_qnty][$key_trim] += $inhouse_qnty;
											$summary_array[inhouse_qnty_bl][$key_trim] += $balance;
											$summary_array[issue_qty][$key_trim] += $issue_qnty; //$issue_qnty-$issue_ret_qty;
											//$summary_array[issue_qty][$key_trim]+=($tot_issue+$transfer_out_data[$value[po_id]][$key_trim]);//$issue_qnty-$issue_ret_qty;
											$summary_array[inhouse_value][$key_trim] += $inhouse_value;
											$summary_array[left_overqty][$key_trim] += $left_overqty;
											$summary_array[left_overvalue][$key_trim] += ($left_overqty * $value[rate][$key_trim1]);

											?>

											<td width="90" align="right" title="<? echo "Inhouse-Qty: " . $value[inhouse_qnty][$key_trim] . "\nReturn Qty: " . $value[receive_rtn_qty][$key_trim]; ?> Transfer In : <?= $transfer_in_data[$value[po_id]][$key_trim] ?> trim: <?= $key_trim; ?>" rowspan="<? echo $rowspannn; ?>">
												<p><a href='#report_details' onclick="openmypage_inhouse('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_inhouse_info');"><? echo fn_number_format($inhouse_qnty, 2, '.', ''); ?></a></p>
											</td>

											<td width="90" align="right" rowspan="<? echo $rowspannn; ?>">
												<a href='#report_details' onclick="openmypage_inhouse('<? echo $value[po_id]; ?>','<? echo $key_trim; ?>','booking_inhouse_value_info');">
													<? echo fn_number_format($inhouse_value, 2, '.', ''); ?>
												</a>
											</td>

											<td width="90" align="right" rowspan="<? echo $rowspannn; ?>">
												<p><? echo fn_number_format($balance, 2, '.', ''); ?></p>

											</td>
											<td width="90" align="right" title="<? echo "Issue-Qty: " . $issue_qnty_total . "\nReturn Qty: " . $issue_ret_qty . "\nConv. Rate: " . $conv_rate; ?>" rowspan="<? echo $rowspannn; ?>">
												<p>
													<a href='#report_details' onclick="openmypage_issue('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_issue_info');">
														<?
														//$total_issue_qty = $issue_qnty + $transfer_out_data[$value[po_id]][$key_trim];
														$total_issue_qty = $issue_qnty + $transfer_out_data[$value[po_id]][$key_trim]- $issue_ret_qty;
														echo number_format($total_issue_qty, 2);
														$summary_array[issue_to_prod][$key_trim] += (($issue_qnty - $issue_ret_qty) / $conv_rate + $transfer_out_data[$value[po_id]][$key_trim]) * $value[rate][$key_trim1];
														?>

													</a>
												</p>
											</td>
											<td align="right" title="<? echo $tot_issue ?>" rowspan="<? echo $rowspannn; ?>">
												<p><? echo fn_number_format($left_overqty+$issue_ret_qty-$transfer_out_data[$value[po_id]][$key_trim], 2, '.', ''); ?></p>
											</td>

										<?
											$total_in_val += $inhouse_value;
										} else {
										?>

											<td width="100" title="<? echo $value[trim_group_from][$key_trim1];  ?>" style="word-break: break-all;">
												<p>
													<? echo $item_library[$value[trim_group_dtls][$key_trim1]]; ?>
													&nbsp;</p>
											</td>
											<td width="100">
												<p>
													<?
													echo $value[remark][$key_trim1];
													//echo $row[csf('brand_sup_ref')];
													?>
													&nbsp;</p>
											</td>
											<td width="100">
												<p>
													<?
													echo $value[brand_sup_ref][$key_trim1];
													//echo $row[csf('brand_sup_ref')];
													?>
													&nbsp;</p>
											</td>
											<td width="60" align="center">
												<p>
													<?

													if ($value[apvl_req][$key_trim1] == 1) echo "Yes";
													else echo "&nbsp;";
													?>
													&nbsp;
												</p>
											</td>
											<td width="80" align="center">

												<?
												if ($value[apvl_req][$key_trim1] == 1) {
													$app_status = $app_status_arr[$value[job_no]][$value[trim_group_dtls][$key_trim1]];
													$approved_status = $approval_status[$app_status];
													$summary_array[item_app][$key_trim][all] += 1;
													if ($app_status == 3) {
														$summary_array[item_app][$key_trim][app] += 1;
													}
												} else {
													$approved_status = "";
												}
												echo $approved_status;
												?>

											</td>

											<td width="100" align="right">
												<p>
													<?
													$insert_date = explode(" ", $value[insert_date][$key_trim1]);
													echo change_date_format($insert_date[0], '', '', ''); //echo change_date_format($row[csf('pre_date')],'','',1);
													?>&nbsp;</p>
											</td>
											<td width="100" align="right">
												<p>
													<a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $value[po_id]; ?>', '<? echo $value[buyer_name]; ?>','<? echo $value[rate][$key_trim1]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>' ,'<? echo $value[booking_no][$key_trim1]; ?>','<? echo $value[description][$key_trim1]; ?>','<? echo $value[country_id][$key_trim1]; ?>','<? echo $value[trim_dtla_id][$key_trim1]; ?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
														<?
														$req_qty = fn_number_format($value[req_qnty][$key_trim1], 2, '.', '');
														echo $req_qty;
														$summary_array[req_qnty][$key_trim] += $value[req_qnty][$key_trim1];
														?>
													</a>

												</p>
											</td>

											<td width="100" align="right">
												<p>
													<?
													echo fn_number_format($value[req_value][$key_trim1], 2);
													$total_pre_costing_value += $value[req_value][$key_trim1];
													?>

												</p>
											</td>
											<?
											$wo_qnty = fn_number_format($value[wo_qnty][$key_trim1], 2);
											if ($wo_qnty > $req_qty) {
												$color_wo = "red";
											} else if ($wo_qnty < $req_qty) {
												$color_wo = "yellow";
											} else {
												$color_wo = "";
											}

											$supplier_name_string = "";
											$supplier_id_arr = array_unique(explode(',', $value[supplier_id][$key_trim1]));
											foreach ($supplier_id_arr as $supplier_id_arr_key => $supplier_id_arr_value) {
												$ex_sup_data = explode("**", $supplier_id_arr_value);

												if ($ex_sup_data[1] == 3 || $ex_sup_data[1] == 5) $suplier_name_arr = $company_library;
												else $suplier_name_arr = $lib_supplier_arr;
												$supplier_name_string .= $suplier_name_arr[$ex_sup_data[0]] . ",";
											}

											$booking_no_arr = array_unique(explode(',', $value[booking_no][$key_trim1]));
											//$booking_no_arr_d=implode(',',$booking_no_arr);
											//print $order_id.'='.	$trim_id;// $wo_qty_array[$order_id][$trim_id]['booking_no'];
											$main_booking_no_large_data = "";
											foreach ($booking_no_arr as $booking_no1) {
												//if($booking_no1>0)
												//{
												if ($main_booking_no_large_data == "") $main_booking_no_large_data = $booking_no1;
												else $main_booking_no_large_data .= "," . $booking_no1;
												//}
												//print($main_booking_no_large_data);
											}
											?>
											<td width="90" align="right" title="<? //echo 'conversion_factor='.$value[conversion_factor_rate][$key_trim1];
																				?>" bgcolor="<? echo $color_wo; ?>">
												<p><a href='#report_details' onclick="openmypage('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','<? echo $value[job_no]; ?>','<? echo $main_booking_no_large_data; ?>','<? echo $value[trim_dtla_id][$key_trim1]; ?>','booking_info');">
														<?
														//$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]['wo_qnty']
														echo fn_number_format($value[wo_qnty][$key_trim1], 2, '.', '');
														$summary_array[wo_qnty][$key_trim] += $value[wo_qnty][$key_trim1];

														?>
													</a></p>
											</td>

											<td width="60" align="center">
												<p>
													<?
													$unit_of_measurement[$item_arr[$value[trim_group_dtls][$key_trim1]]['order_uom']];
													//echo $unit_of_measurement[$value[cons_uom][$key_trim1]];
													$summary_array[cons_uom][$key_trim] = $item_arr[$value[trim_group_dtls][$key_trim1]]['order_uom']; //$value[cons_uom][$key_trim1];
													?>
												</p>
											</td>

											<td width="100" align="right" title="<? echo fn_number_format($value[rate][$key_trim1], 2, '.', ''); ?>">
												<p>
													<?
													echo fn_number_format($value[amount][$key_trim1], 2, '.', '');
													$total_wo_value += $value[amount][$key_trim1];
													?>

												</p>
											</td>
											<td width="150" align="left">
												<p>
													<? echo rtrim($supplier_name_string, ","); ?>
												</p>
											</td>
											<td width="70" align="right" title="<? echo change_date_format($value[wo_date][$key_trim1]); ?>">
												<p>
													<?
													$tot = change_date_format($insert_date[0]);
													if ($value[wo_qnty][$key_trim1] <= 0) {
														$daysOnHand = datediff('d', $tot, $today);
													} else {
														$wo_date = $value[wo_date][$key_trim1];
														$wo_date = change_date_format($wo_date);
														$daysOnHand = datediff('d', $tot, $wo_date);;
													}
													echo $daysOnHand;
													?>
												</p>
											</td>
											<td width="70" align="left">
												<p>
													<? echo $delivery_date = change_date_format($value[delivery_date][$key_trim1]); ?>
												</p>
											</td>
										<?
										}

										?>
							</tr>

					<?

										$gg++;
									} // end  foreach($value[$key_trim] as $key_trim1=>$value_trim1)
								}
					?>
				<?
							$i++;
						}
						$po_data_arr = array();
				?>
					</table>
					<table class="rpt_table" width="2910" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
							<th width="30"></th>
							<th width="50"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="90"></th>
							<th width="80" align="right" id="total_order_qnty"><? //echo fn_number_format($total_order_qnty,0); 
																				?></th>
							<th width="50"></th>
							<th width="80" align="right" id="total_order_qnty_in_pcs"><? //echo fn_number_format($total_order_qnty_in_pcs,0); 
																						?></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="60"></th>
							<th width="80"></th>
							<th width="100"></th>
							<th width="100" align="right" id="value_req_qnty"><? //echo fn_number_format($total_req_qnty,2); 
																				?></th>
							<th width="100" align="right" id="value_pre_costing"><? echo fn_number_format($total_pre_costing_value, 2); ?></th>
							<th width="90" align="right" id="value_wo_qty"><? //echo fn_number_format($total_wo_qnty,2); 
																			?></th>
							<th width="60" align="right"></th>
							<th width="100" align="right" id="td_wo_val"><? echo fn_number_format($total_wo_value, 2); ?></th>
							<th width="150" align="right" id=""></th>
							<th width="70" align="right">
								<p><? //echo fn_number_format($req_value,2,'.',''); 
									?>&nbsp;</p>
							</th>
							<th width="70" align="right">
								<p><? //echo fn_number_format($req_value,2,'.',''); 
									?>&nbsp;</p>
							</th>
							<th width="90" align="right" id="value_in_qty"><? //echo fn_number_format($total_in_qnty,2); 
																			?></th>
							<th width="90" align="right" id="value_in_value"><? echo fn_number_format($total_in_val, 2); ?></th>

							<th width="90" align="right" id="value_rec_qty"><? //echo fn_number_format($total_rec_bal_qnty,2); 
																			?></th>
							<th width="90" align="right" id="value_issue_qty"><? //echo fn_number_format($total_issue_qnty,2); 
																				?></th>
							<th align="right" id="value_leftover_qty"><? //echo fn_number_format($total_leftover_qnty,2); 
																		?></th>
						</tfoot>
					</table>
				</div>
				<table>
					<tr>
						<td height="17"></td>
					</tr>
				</table>
				<u><b>Summary</b></u>
				<table class="rpt_table" width="1400" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="110">Item</th>
						<th width="60">UOM</th>
						<th width="80">Approved %</th>
						<th width="110">Req Qty</th>
						<th width="110">WO Qty</th>
						<th width="80">WO %</th>
						<th width="110">In-House Qty</th>
						<th width="80">In-House %</th>
						<th width="80">In-House Value</th>
						<th width="110">In-House Balance Qty</th>
						<th width="110">Issue Qty</th>
						<th width="80">Issue %</th>
						<th width="80">Issue Value</th>
						<th width="80">Left Over</th>
						<th>Left Over Value</th>
					</thead>
					<?
					$conversion_factor_array = array();
					$item_arr = array();
					$conversion_factor = sql_select("select id,trim_uom,order_uom,conversion_factor from lib_item_group where status_active=1  ");
					foreach ($conversion_factor as $row_f) {
						$conversion_factor_array[$row_f[csf('id')]]['con_factor'] = $row_f[csf('conversion_factor')];
						//$conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
						//$item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('order_uom')];
					}
					unset($conversion_factor);
					$z = 1;
					$tot_req_qnty_summary = 0;
					// echo "<pre>";
					// 	print_r($summary_array[trim_group]);
					// echo "</pre>";
					foreach ($summary_array[trim_group] as $key_trim => $value) {
						if ($z % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
						//$tot_req_qnty_summary+=$value['req'];
						//$tot_wo_qnty_summary+=$value['wo'];
						//$tot_in_qnty_summary+=$value['in'];
						//$tot_issue_qnty_summary+=$value['issue'];
						//$tot_leftover_qnty_summary+=$value['leftover'];
						$con_factor = $conversion_factor_array[$key_trim]['con_factor'];
						$req_qnty_cal = $summary_array[req_qnty][$key_trim] / $con_factor;

						$inhouse_value = $summary_array[inhouse_value][$key_trim];
						$left_overqty = $summary_array[left_overvalue][$key_trim];
						$issue_to_prod = $summary_array[issue_to_prod][$key_trim];
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr2_<? echo $z; ?>','<? echo $bgcolor; ?>')" id="tr2_<? echo $z; ?>">
							<td><? echo $z; ?></td>
							<td>
								<p><? echo $item_library[$key_trim]; ?></p>
							</td>
							<td align="center">
								<?
								echo $unit_of_measurement[$summary_array[cons_uom][$key_trim]];
								?></td>
							<td align="right"><? $app_perc = ($summary_array[item_app][$key_trim][app] * 100) / $summary_array[item_app][$key_trim][all];
												if ($summary_array[item_app][$key_trim][app]) echo fn_number_format($app_perc, 2);
												else echo ""; ?></td>
							<td align="right"><? echo fn_number_format($summary_array[req_qnty][$key_trim], 2); ?></td>
							<td align="right"><? echo fn_number_format($summary_array[wo_qnty][$key_trim], 2); ?></td>
							<td align="right" title="WO Qty/Req Qty/Conv. Factor(<? echo $con_factor ?>)*100"><? $wo_per = $summary_array[wo_qnty][$key_trim] / $req_qnty_cal * 100;
																												if ($summary_array[wo_qnty][$key_trim])  echo fn_number_format($wo_per, 2) . '%';
																												else echo ""; ?></td>
							<td align="right"><? echo fn_number_format($summary_array[inhouse_qnty][$key_trim], 2); ?></td>
							<td align="right"><? $in_per = $summary_array[inhouse_qnty][$key_trim] / $summary_array[wo_qnty][$key_trim] * 100;
												if ($summary_array[inhouse_qnty][$key_trim]) echo fn_number_format($in_per, 2) . '%';
												else echo "" ?></td>
							<td align="right">
								<? echo fn_number_format($inhouse_value, 2) ?>
							</td>
							<td align="right"><? echo fn_number_format($summary_array[inhouse_qnty_bl][$key_trim], 2);
												$in_house_bal += ($value['wo'] - $value['in']); ?></td>
							<td align="right"><? echo fn_number_format($summary_array[issue_qty][$key_trim], 2); ?></td>

							<td align="right"><? $wo_per = $summary_array[issue_qty][$key_trim] / $summary_array[wo_qnty][$key_trim] * 100;
												if ($summary_array[issue_qty][$key_trim]) echo fn_number_format($wo_per, 2) . '%';
												else echo ""; ?></td>
							<td align="right">
								<? echo fn_number_format($issue_to_prod, 2) ?>
							</td>
							<td align="right"><? echo fn_number_format($summary_array[left_overqty][$key_trim], 2); ?></td>

							<td align="right">
								<? echo fn_number_format($left_overqty, 2) ?>
							</td>
						</tr>
					<?
						$z++;
					}
					$summary_array = array();
					?>
					<tfoot>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_req_qnty_summary,2); 
											?>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_wo_qnty_summary,2); 
											?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_in_qnty_summary,2); 
											?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($in_house_bal,2); 
											?>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_issue_qnty_summary,2); 
											?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_leftover_qnty_summary,2); 
											?>&nbsp;</th>
					</tfoot>
				</table>
			</fieldset>
		</div>
	<?
	}

	//===========================================================================================================================================================

	if (str_replace("'", "", $cbo_search_by) == 2) {
		ob_start();
		?>
		<div style="width:2640px">
			<fieldset style="width:100%;">
				<table width="2500">
					<tr class="form_caption">
						<td colspan="26" align="center">Accessories Followup Report</td>
					</tr>
					<tr class="form_caption">
						<td colspan="26" align="center"><? echo $company_library[$company_name]; ?></td>
					</tr>
				</table>
				<table class="rpt_table" width="2630" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="50">Buyer</th>
						<th width="100">Job No</th>
						<th width="100">Style Ref</th>
						<th width="100">Team Leader</th>
						<th width="100">Prod. Dept </th>
						<th width="100">Internal Ref</th>
						<th width="100">File No</th>
						<th width="90">Order No</th>
						<th width="80">Order Qnty</th>
						<th width="50">UOM</th>
						<th width="80">Qnty (Pcs)</th>
						<th width="80">Shipment Date</th>
						<th width="100">Trims Name</th>
						<th width="100">Brand/Sup Ref</th>
						<th width="60">Appr Req.</th>
						<th width="80">Approve Status</th>
						<th width="100">Item Entry Date</th>
						<th width="100">Req Qnty</th>
						<th width="100">Pre Costing Value</th>
						<th width="90">WO Qnty</th>
						<th width="60">Trims UOM</th>
						<th width="100">WO Value</th>
						<th width="150">Supplier</th>
						<th width="70">WO Delay Days</th>
						<th width="70">Delivery Date</th>
						<th width="90">In-House Qnty</th>
						<th width="90">Receive Balance</th>
						<th width="90">Issue to Prod.</th>
						<th>Left Over/Balance</th>
					</thead>
				</table>
				<div style="width:2650px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="2630" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<?
						$conversion_factor_array = array(); $item_arr = array();
						$conversion_factor = sql_select("select id ,trim_uom,order_uom,conversion_factor from  lib_item_group  ");
						foreach ($conversion_factor as $row_f) {
							$conversion_factor_array[$row_f[csf('id')]]['con_factor'] = $row_f[csf('conversion_factor')];
							$conversion_factor_array[$row_f[csf('id')]]['cons_uom'] = $row_f[csf('trim_uom')];
							$item_arr[$row_f[csf('id')]]['order_uom'] = $row_f[csf('order_uom')];
						}
						unset($conversion_factor);
						
						$today = date("Y-m-d");
						$condition = new condition();
						if (str_replace("'", "", $company_name) > 0) {
							$condition->company_name("=$company_name");
						}
						if (str_replace("'", "", $cbo_buyer_name) > 0) {
							$condition->buyer_name("=$cbo_buyer_name");
						}
						if (str_replace("'", "", $txt_job_no) != '') {
							$condition->job_no_prefix_num("=$txt_job_no");
						}
						if (str_replace("'", "", $txt_style_ref) != '') {
							$condition->style_ref_no("='" . str_replace("'", "", $txt_style_ref) . "'");
						}

						if (str_replace("'", "", $txt_order_no) != '') {
							$condition->po_number("='" . str_replace("'", "", $txt_order_no) . "'");
						}

						if (str_replace("'", "", $txt_file_no) != '') {
							$condition->file_no("='" . str_replace("'", "", $txt_file_no) . "'");
						}

						if (str_replace("'", "", $txt_internal_ref) != '') {
							$condition->grouping("='" . str_replace("'", "", $txt_internal_ref) . "'");
						}

						if (str_replace("'", "", $txt_date_from) != "" && str_replace("'", "", $txt_date_to) != "") {
							$start_date = (str_replace("'", "", $txt_date_from));
							$end_date = (str_replace("'", "", $txt_date_to));
							if ($date_type == 1) {
								$condition->country_ship_date(" between '$start_date' and '$end_date'");
							}
							if ($date_type == 2) {
								$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
							}
						}
						$condition->init();
						$trim = new trims($condition);
						//echo $trim->getQuery(); die;
						$trim_group_qty_arr = $trim->getQtyArray_by_orderAndItemid();
						$trim_qty = $trim->getQtyArray_by_orderCountryAndPrecostdtlsid();

						$sql_query = sql_select("SELECT a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.team_leader, a.product_dept, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, sum(c.order_quantity) as order_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, b.pub_shipment_date, d.costing_per, e.id as trim_dtla_id, e.trim_group, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, f.cons, f.cons as cons_cal FROM wo_po_details_master a JOIN wo_po_break_down b ON a.id = b.job_id JOIN wo_po_color_size_breakdown c ON a.id = c.job_id AND b.id = c.po_break_down_id AND c.job_id = b.job_id JOIN wo_pre_cost_mst d ON a.id = d.job_id AND d.job_id = b.job_id AND d.job_id = c.job_id JOIN wo_pre_cost_trim_cost_dtls e ON e.job_id = d.job_id AND e.job_id = b.job_id AND e.job_id = c.job_id LEFT JOIN wo_pre_cost_trim_co_cons_dtls f ON c.job_id = f.job_id AND c.po_break_down_id = f.po_break_down_id AND f.job_id = e.job_id AND e.id = f.wo_pre_cost_trim_cost_dtls_id AND f.job_id = b.job_id AND f.job_id = a.id where f.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $year_cond $jobcond $ordercond $file_no_cond $internal_ref_cond $brand_cond $season_year_cond $season_buyer_wise_cond $team_leader_cond $product_dept_cond group by a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, a.total_set_qnty, a.team_leader, a.product_dept, b.pub_shipment_date, d.costing_per, e.id, e.trim_group, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, f.cons, f.pcs order by b.id, e.trim_group");

						$poid = ""; $trimID = ''; $poididarr = array(); $trimidarr = array(); $style_data_arr = array(); $po_id_string = "";
						foreach ($sql_query as $row) {
							$poid .= $row[csf('id')] . ",";
							$trimID .= $row[csf('trim_dtla_id')] . ",";
							$poididarr[$row[csf('id')]] = $row[csf('id')];
							$trimidarr[$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')];
						}
						$con = connect();
						execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (2,3) and ENTRY_FORM=31");
						oci_commit($con);
						fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 31, 2, $poididarr, $empty_arr);//PO ID
						fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 31, 3, $trimidarr, $empty_arr);//trim_dtla_id
						disconnect($con);
			
						$app_sql=sql_select("select a.job_no_mst, a.accessories_type_id, a.approval_status from wo_po_trims_approval_info a, gbl_temp_engine d where a.is_deleted=0 and a.status_active=1 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=2");
						$app_status_arr=array();
						foreach($app_sql as $row)
						{
							$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
						}
						unset($app_sql);

						$sqlcountry="select a.po_break_down_id, a.wo_pre_cost_trim_cost_dtls_id, a.country_id from wo_pre_cost_trim_co_cons_dtls a, gbl_temp_engine b,gbl_temp_engine c where a.po_break_down_id=b.ref_val and b.user_id = ".$user_id." and b.ref_from =2 and b.entry_form=31 and a.wo_pre_cost_trim_cost_dtls_id=c.ref_val and c.user_id = ".$user_id." and c.ref_from=3 and c.entry_form=31 and a.cons > 0";
						//echo $sqlcountry;
						$sqlcountryres=sql_select($sqlcountry); $trimCountryArr=array();
						foreach($sqlcountryres as $row)
						{
							$trimCountryArr[$row[csf('po_break_down_id')]][$row[csf('wo_pre_cost_trim_cost_dtls_id')]].=$row[csf('country_id')].',';
						}
						unset($sqlcountryres);
						
						$sql_po_qty_country_wise_arr=array(); $po_job_arr=array();
						$sql_po_qty_country_wise=sql_select("select b.id, b.job_no_mst, c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, gbl_temp_engine d where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=2 and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $year_cond $jobcond $ordercond group by b.id, b.job_no_mst, c.country_id order by b.id, b.job_no_mst, c.country_id");
						foreach( $sql_po_qty_country_wise as $sql_po_qty_country_wise_row)
						{
							$sql_po_qty_country_wise_arr[$sql_po_qty_country_wise_row[csf('id')]][$sql_po_qty_country_wise_row[csf('country_id')]]=$sql_po_qty_country_wise_row[csf('order_quantity_set')];
							$po_job_arr[$sql_po_qty_country_wise_row[csf('id')]]=$sql_po_qty_country_wise_row[csf('job_no_mst')];
						}
						unset($sql_po_qty_country_wise);

						$tot_rows = count($sql_query); $i = 1;
						
						if($tot_rows<1)
						{
							echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
							die;
						}
						
						foreach ($sql_query as $row) {
							$dzn_qnty = 0;
							if ($row[csf('costing_per')] == 1) $dzn_qnty = 12;
							else if ($row[csf('costing_per')] == 3) $dzn_qnty = 12 * 2;
							else if ($row[csf('costing_per')] == 4) $dzn_qnty = 12 * 3;
							else if ($row[csf('costing_per')] == 5) $dzn_qnty = 12 * 4;
							else $dzn_qnty = 1;

							$po_qty = $req_qnty = 0;

							$countryId = "";
							$countryId = implode(",", array_filter(array_unique(explode(",", $trimCountryArr[$row[csf('id')]][$row[csf('trim_dtla_id')]]))));
							if ($countryId == 0 || $countryId == '') {
								//$po_qty=$row[csf('order_quantity')];
								$req_qnty += $trim_qty[$row[csf('id')]][$countryId][$row[csf('trim_dtla_id')]];
							} else {
								$country_id = array_unique(explode(",", $countryId));
								foreach ($country_id as $cid) {
									$req_qnty += $trim_qty[$row[csf('id')]][$cid][$row[csf('trim_dtla_id')]];
								}
							}
							$poididarr[$row[csf('id')]] = $row[csf('id')];
							// $req_qnty=($row[csf('cons_cal')]/$dzn_qnty)*$po_qty;
							$req_value = $row[csf('rate')] * $req_qnty;

							$style_data_arr[$row[csf('job_no')]][job_no] = $row[csf('job_no')];
							$style_data_arr[$row[csf('job_no')]][team_leader] = $row[csf('team_leader')];
							$style_data_arr[$row[csf('job_no')]][product_dept] = $row[csf('product_dept')];
							$style_data_arr[$row[csf('job_no')]][buyer_name] = $row[csf('buyer_name')];
							$style_data_arr[$row[csf('job_no')]][job_no_prefix_num] = $row[csf('job_no_prefix_num')];
							$style_data_arr[$row[csf('job_no')]][style_ref_no] = $row[csf('style_ref_no')];
							$style_data_arr[$row[csf('job_no')]][grouping] = $row[csf('grouping')];
							$style_data_arr[$row[csf('job_no')]][file_no] = $row[csf('file_no')];

							$style_data_arr[$row[csf('job_no')]][order_uom] = $row[csf('order_uom')];
							$style_data_arr[$row[csf('job_no')]][po_id][$row[csf('id')]] = $row[csf('id')];
							$style_data_arr[$row[csf('job_no')]][po_number][$row[csf('id')]] = $row[csf('po_number')];
							$style_data_arr[$row[csf('job_no')]][order_quantity_set][$row[csf('id')]] = $row[csf('order_quantity_set')];
							$style_data_arr[$row[csf('job_no')]][order_quantity][$row[csf('id')]] = $row[csf('order_quantity')];
							$style_data_arr[$row[csf('job_no')]][pub_shipment_date][$row[csf('id')]] = change_date_format($row[csf('pub_shipment_date')]);
							$po_id_string .= $row[csf('id')] . ",";

							$style_data_arr[$row[csf('job_no')]][trim_dtla_id][$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')]; // for rowspan
							$style_data_arr[$row[csf('job_no')]][trim_group][$row[csf('trim_group')]] = $row[csf('trim_group')];
							$style_data_arr[$row[csf('job_no')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')]; // for rowspannn
							$style_data_arr[$row[csf('job_no')]][trim_group_dtls][$row[csf('trim_dtla_id')]] = $row[csf('trim_group')];

							$style_data_arr[$row[csf('job_no')]][brand_sup_ref][$row[csf('trim_dtla_id')]] = $row[csf('brand_sup_ref')];
							$style_data_arr[$row[csf('job_no')]][apvl_req][$row[csf('trim_dtla_id')]] = $row[csf('apvl_req')];
							$style_data_arr[$row[csf('job_no')]][insert_date][$row[csf('trim_dtla_id')]] = $row[csf('insert_date')];
							$style_data_arr[$row[csf('job_no')]][req_qnty][$row[csf('trim_dtla_id')]] += $req_qnty;
							$style_data_arr[$row[csf('job_no')]][req_value][$row[csf('trim_dtla_id')]] += $req_value;
							$style_data_arr[$row[csf('job_no')]][cons_uom][$row[csf('trim_dtla_id')]] = $conversion_factor_array[$row[csf('trim_group')]]['cons_uom']; //$row[csf('cons_uom')];

							$style_data_arr[$row[csf('job_no')]][trim_group_from][$row[csf('trim_dtla_id')]] = "Pre_cost";

							$style_data_arr[$row[csf('job_no')]][rate][$row[csf('trim_dtla_id')]] = $row[csf('rate')];
							$style_data_arr[$row[csf('job_no')]][description][$row[csf('trim_dtla_id')]] = $row[csf('description')];
							$style_data_arr[$row[csf('job_no')]][country_id][$row[csf('trim_dtla_id')]] .= $row[csf('country_id')] . ",";
						}
						unset($sql_query);

						$transfer_sql_in = sql_select("SELECT  a.from_order_id, a.to_order_id, b.item_group, b.transfer_qnty as qnty FROM inv_item_transfer_mst a, inv_item_transfer_dtls b,gbl_temp_engine t WHERE a.id = b.mst_id   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.to_order_id=t.ref_val and t.user_id = " . $user_id . " and t.ref_from=2 and t.entry_form=31 ");

						$transfer_in_data = array();
						foreach ($transfer_sql_in as $row) {
							$transfer_in_data[$row[csf('from_order_id')]][$row[csf('item_group')]] += $row[csf('qnty')];
						}
						unset($transfer_sql_in);
						
						$transfer_sql_out = sql_select("SELECT  a.from_order_id, a.to_order_id, b.item_group, b.transfer_qnty as qnty FROM inv_item_transfer_mst a, inv_item_transfer_dtls b,gbl_temp_engine t WHERE a.id = b.mst_id   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.from_order_id=t.ref_val and t.user_id = " . $user_id . " and t.ref_from =2 and t.entry_form=31");

						$transfer_out_data = array();
						foreach ($transfer_sql_out as $row) {
							$transfer_out_data[$row[csf('from_order_id')]][$row[csf('item_group')]] += $row[csf('qnty')];
						}
						unset($transfer_sql_out);
						
						if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond2 = ""; else $item_group_cond2 = "and b.trim_group in(" . str_replace("'", "", $cbo_item_group) . ")";

						$wo_sql_without_precost = sql_select("select min(a.booking_date) as booking_date, b.job_no, b.delivery_date, LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, LISTAGG(CAST(a.supplier_id || '**' || a.pay_mode AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount) as amount, sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b,gbl_temp_engine t where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name and b.po_break_down_id=t.ref_val and t.user_id = " . $user_id . " and t.ref_from=2 and t.entry_form=31 $item_group_cond2 group by b.po_break_down_id, b.delivery_date, b.trim_group, b.job_no, b.pre_cost_fabric_cost_dtls_id"); //and item_from_precost=2
						
						$style_data_arr1 = array();
						foreach ($wo_sql_without_precost as $wo_row_without_precost) {
							$conversion_factor_rate = $conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['con_factor'];
							$cons_uom = $conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['cons_uom'];
							$booking_no = $wo_row_without_precost[csf('booking_no')];
							$supplier_id = $wo_row_without_precost[csf('supplier_id')];
							$wo_qnty = $wo_row_without_precost[csf('wo_qnty')]; //*$conversion_factor_rate;
							$amount = $wo_row_without_precost[csf('amount')];
							$wo_date = $wo_row_without_precost[csf('booking_date')];
							$delivery_date = $wo_row_without_precost[csf('delivery_date')];

							if ($wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] == "" || $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] == 0) {
								$trim_dtla_id = max($style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_dtla_id]) + 1;
								$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_dtla_id][$trim_dtla_id] = $trim_dtla_id; // for rowspan
								$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group][$wo_row_without_precost[csf('trim_group')]] = $wo_row_without_precost[csf('trim_group')];
								$style_data_arr[$wo_row_without_precost[csf('job_no')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id] = $trim_dtla_id; // for rowspannn
								$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group_dtls][$trim_dtla_id] = $wo_row_without_precost[csf('trim_group')];
								$style_data_arr[$wo_row_without_precost[csf('job_no')]][cons_uom][$trim_dtla_id] = $cons_uom;

								$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group_from][$trim_dtla_id] = "Booking Without Pre_cost";
							} else {
								$trim_dtla_id = $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
							}
							//$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_dtla_id][$trim_dtla_id]=$trim_dtla_id;// for rowspan
							//$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group][$wo_row_without_precost[csf('trim_group')]]=$wo_row_without_precost[csf('trim_group')];
							// $style_data_arr[$wo_row_without_precost[csf('job_no')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
							//$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group_dtls][$trim_dtla_id]=$wo_row_without_precost[csf('trim_group')];

							$style_data_arr[$wo_row_without_precost[csf('job_no')]][wo_qnty][$trim_dtla_id] += $wo_qnty;
							$style_data_arr[$wo_row_without_precost[csf('job_no')]][amount][$trim_dtla_id] += $amount;
							$style_data_arr[$wo_row_without_precost[csf('job_no')]][wo_date][$trim_dtla_id] = $wo_date;
							$style_data_arr[$wo_row_without_precost[csf('job_no')]][delivery_date][$trim_dtla_id] = $delivery_date;
							$style_data_arr[$wo_row_without_precost[csf('job_no')]][wo_qnty_trim_group][$wo_row_without_precost[csf('trim_group')]] += $wo_qnty;

							$style_data_arr[$wo_row_without_precost[csf('job_no')]][booking_no][$trim_dtla_id] .= $booking_no . ",";
							$style_data_arr[$wo_row_without_precost[csf('job_no')]][supplier_id][$trim_dtla_id] .= $supplier_id . ",";
							$style_data_arr[$wo_row_without_precost[csf('job_no')]][conversion_factor_rate][$trim_dtla_id] = $conversion_factor_rate;
						}
						unset($wo_sql_without_precost);
						
						if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond3 = ""; else $item_group_cond3 = "and a.item_group_id in(" . str_replace("'", "", $cbo_item_group) . ")";

						$receive_qty_data = sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b,gbl_temp_engine t where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and d.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id=t.ref_val and t.user_id = " . $user_id . " and t.ref_from=2 and t.entry_form=31 $item_group_cond3 group by b.po_breakdown_id, a.item_group_id order by a.item_group_id ");

						foreach ($receive_qty_data as $row) {
							if ($style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group][$row[csf('item_group_id')]] == "" || $style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group][$row[csf('item_group_id')]] == 0) {
								$cons_uom = $conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];
								$trim_dtla_id = max($style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_dtla_id]) + 1;
								$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_dtla_id][$trim_dtla_id] = $trim_dtla_id; // for rowspan
								$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group][$row[csf('item_group_id')]] = $row[csf('item_group_id')];
								$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][$row[csf('item_group_id')]][$trim_dtla_id] = $trim_dtla_id; // for rowspannn
								$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group_dtls][$trim_dtla_id] = $row[csf('item_group_id')];
								$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][cons_uom][$trim_dtla_id] = $cons_uom;

								$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group_from][$trim_dtla_id] = "Trim Receive";
							}
							$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][inhouse_qnty][$row[csf('item_group_id')]] += $row[csf('quantity')];
						}
						unset($receive_qty_data);
						
						if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond4 = ""; else $item_group_cond4 = "and c.item_group_id in(" . str_replace("'", "", $cbo_item_group) . ")";

						$receive_rtn_qty_data = sql_select("select d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e ,gbl_temp_engine t where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and c.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id=t.ref_val and t.user_id = " . $user_id . " and t.ref_from=2 and t.entry_form=31 $item_group_cond4 group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");
						foreach ($receive_rtn_qty_data as $row) {
							$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][receive_rtn_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
						}
						unset($receive_rtn_qty_data);

						$issue_qty_data = sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b, gbl_temp_engine t where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and p.item_category_id=4 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.po_breakdown_id=t.ref_val and t.user_id = " . $user_id . " and t.ref_from=2 and t.entry_form=31 $item_group_cond3 group by b.po_breakdown_id, a.item_group_id");
						foreach ($issue_qty_data as $row) {
							$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][issue_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
						}
						unset($issue_qty_data);
						if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond5 = ""; else $item_group_cond5 = "and p.item_group_id in(" . str_replace("'", "", $cbo_item_group) . ")";
						$issue_qty_data_arr = array();
						$sql_issue_ret = ("select c.po_breakdown_id as po_id, p.item_group_id,SUM(c.quantity) as quantity
					from  inv_transaction b, order_wise_pro_details c, product_details_master p, gbl_temp_engine t
					where c.entry_form=73 and p.id=b.prod_id  and b.id=c.trans_id and b.transaction_type=4 and p.item_category_id=4 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id=t.ref_val and t.user_id = " . $user_id . " and t.ref_from=2 and t.entry_form=31 $item_group_cond5 group by c.po_breakdown_id,p.item_group_id");
						$issue_result = sql_select($sql_issue_ret);
						foreach ($issue_result as $row) {
							$issue_qty_data_arr[$row[csf('po_id')]][issue_ret_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
						}
						unset($issue_result);
						
						$con = connect();
						execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (2,3) and ENTRY_FORM=31");
						oci_commit($con);
						disconnect($con);

						$total_pre_costing_value = 0; $total_wo_value = 0; $summary_array = array(); $i = 1;
						
						foreach ($style_data_arr as $key => $value) {
							$rowspan = count($value[trim_dtla_id]);
							//$rowspan=count($value[trim_group]);
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30" title="<? echo $po_qty; ?>" rowspan="<? echo $rowspan; ?>"><p><? echo $i; ?>&nbsp;</p></td>
								<td width="50" rowspan="<? echo $rowspan; ?>"><p><? echo $buyer_short_name_library[$value[buyer_name]]; ?>&nbsp;</p></td>
								<td width="100" align="center" rowspan="<? echo $rowspan; ?>"><p><? echo $value[job_no_prefix_num]; ?>&nbsp;</p></td>
								<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $value[style_ref_no]; ?>&nbsp;</p></td>
								<td width="100" rowspan="<?=$rowspan; ?>" style="word-break: break-all;"><p><?=$team_leader_name_arr[$value['team_leader']]; ?>&nbsp;</p></td>
								<td width="100" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;"><p><? echo $product_dept[$value['product_dept']]; ?>&nbsp;</p></td>
								<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $value[grouping]; ?></p></td>
								<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $value[file_no]; ?></p></td>
								<td width="90" rowspan="<?=$rowspan; ?>"><p><a href='#report_details' onclick="generate_report('<?=$company_name; ?>','<?=$value[job_no]; ?>','preCostRpt');"><? $po_number = implode(",", $value[po_number]); $po_id = implode(",", $value[po_id]); echo $po_number; ?></a>&nbsp;</p></td>
								<td width="80" align="right" rowspan="<? echo $rowspan; ?>"><p><a href='#report_details' onclick="order_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $po_id; ?>', '<? echo $value[buyer_name]; ?>' ,'order_qty_data');"><? echo fn_number_format(array_sum($value[order_quantity_set]), 0, '.', ''); ?></a></p></td>
								<td width="50" align="center" rowspan="<? echo $rowspan; ?>"><p><? echo $unit_of_measurement[$value[order_uom]]; ?>&nbsp;</p></td>
								<td width="80" align="right" rowspan="<? echo $rowspan; ?>"><p><? echo fn_number_format(array_sum($value[order_quantity]), 0, '.', ''); ?></p></td>
								<td width="80" align="center" rowspan="<?=$rowspan; ?>"><p><? $pub_shipment_date = implode(",", $value[pub_shipment_date]); echo $pub_shipment_date; ?>&nbsp;</p></td>
								<?
								foreach ($value[trim_group] as $key_trim => $value_trim) {
									$summary_array[trim_group][$key_trim] = $key_trim;
									$gg = 1;
									foreach ($value[$key_trim] as $key_trim1 => $value_trim1) {
										$rowspannn = count($value[$key_trim]);
										//$rowspannn=1;
										if ($gg == 1) { ?>
											<td width="100" title="<? echo $value[trim_group_from][$key_trim1];  ?>">
												<p>
													<?
													echo $item_library[$value[trim_group_dtls][$key_trim1]];
													//echo $value[trim_group_dtls][$key_trim1];
													?>
												</p>
											</td>
											<td width="100">
												<p>
													<?
													echo $value[brand_sup_ref][$key_trim1];
													//echo $row[csf('brand_sup_ref')];
													?>
												</p>
											</td>
											<td width="60" align="center">
												<p>
													<?

													if ($value[apvl_req][$key_trim1] == 1) echo "Yes";
													else echo "&nbsp;";
													?>
													&nbsp;
												</p>
											</td>
											<td width="80" align="center">
												<p>
													<?
													if ($value[apvl_req][$key_trim1] == 1) {
														$app_status = $app_status_arr[$value[job_no]][$value[trim_group_dtls][$key_trim1]];
														$approved_status = $approval_status[$app_status];
														$summary_array[item_app][$key_trim][all] += 1;
														if ($app_status == 3) {
															$summary_array[item_app][$key_trim][app] += 1;
														}
													} else {
														$approved_status = "";
													}
													echo $approved_status;
													?>
													&nbsp;
												</p>
											</td>

											<td width="100" align="right">
												<p>
													<?
													$insert_date = explode(" ", $value[insert_date][$key_trim1]);
													echo change_date_format($insert_date[0], '', '', ''); //echo change_date_format($row[csf('pre_date')],'','',1);
													?>&nbsp;</p>
											</td>
											<td width="100" align="right">
												<p>
													<a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $po_id; ?>', '<? echo $value[buyer_name]; ?>','<? echo $value[rate][$key_trim1]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>' ,'<? echo $value[booking_no][$key_trim1]; ?>','<? echo $value[description][$key_trim1]; ?>','<? echo " "; //rtrim($value[country_id][$key_trim1],",");
																																																																																																					?>','<? echo $value[trim_dtla_id][$key_trim1]; ?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
														<?
														$req_qty = fn_number_format($value[req_qnty][$key_trim1], 2, '.', '');
														echo $req_qty;
														$summary_array[req_qnty][$key_trim] += $value[req_qnty][$key_trim1];
														?>
													</a>
												</p>
											</td>
											<td width="100" align="right">
												<p><? echo fn_number_format($value[req_value][$key_trim1], 2);
													$total_pre_costing_value += $value[req_value][$key_trim1]; ?></p>
											</td>
											<?
											// $conversion_factor_rate=$conversion_factor_array[$row[csf('trim_group')]]['con_factor'];
											$wo_qnty = fn_number_format($value[wo_qnty][$key_trim1], 2, '.', '');
											if ($wo_qnty > $req_qty) {
												$color_wo = "red";
											} else if ($wo_qnty < $req_qty) {
												$color_wo = "yellow";
											} else {
												$color_wo = "";
											}

											$supplier_name_string = "";
											$supplier_id_arr = array_unique(explode(',', rtrim($value[supplier_id][$key_trim1], ",")));
											foreach ($supplier_id_arr as $supplier_id_arr_key => $supplier_id_arr_value) {
												$ex_sup_data = explode("**", $supplier_id_arr_value);
												if ($ex_sup_data[1] == 3 || $ex_sup_data[1] == 5) $suplier_name_arr = $company_library;
												else $suplier_name_arr = $lib_supplier_arr;
												$supplier_name_string .= $suplier_name_arr[$ex_sup_data[0]] . ",";
											}

											$booking_no_arr = array_unique(explode(',', rtrim($value[booking_no][$key_trim1], ",")));
											//$booking_no_arr_d=implode(',',$booking_no_arr);
											//print $order_id.'='.	$trim_id;// $wo_qty_array[$order_id][$trim_id]['booking_no'];
											$main_booking_no_large_data = "";
											foreach ($booking_no_arr as $booking_no1) {
												//if($booking_no1>0)
												//{
												if ($main_booking_no_large_data == "") $main_booking_no_large_data = $booking_no1;
												else $main_booking_no_large_data .= "," . $booking_no1;
												//}
												//print($main_booking_no_large_data);
											}
											?>
											<td width="90" align="right" title="<? //echo 'conversion_factor='.$value[conversion_factor_rate][$key_trim1];
																				?>" bgcolor="<? echo $color_wo; ?>">
												<p><a href='#report_details' onclick="openmypage('<? echo $po_id; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','<? echo $value[job_no]; ?>','<? echo $main_booking_no_large_data; ?>','<? echo $value[trim_dtla_id][$key_trim1]; ?>','booking_info');">
														<?
														//$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]['wo_qnty']
														echo fn_number_format($value[wo_qnty][$key_trim1], 2, '.', '');
														$summary_array[wo_qnty][$key_trim] += $value[wo_qnty][$key_trim1];
														?>
													</a></p>
											</td>
											<td width="60" align="center">
												<p>
													<?
													echo $unit_of_measurement[$item_arr[$value[trim_group_dtls][$key_trim1]]['order_uom']];
													//echo $unit_of_measurement[$value[cons_uom][$key_trim1]];
													$summary_array[cons_uom][$key_trim] = $item_arr[$value[trim_group_dtls][$key_trim1]]['order_uom']; //$value[cons_uom][$key_trim1];
													?>
													&nbsp;
												</p>
											</td>
											<td width="100" align="right" title="<? echo fn_number_format($value[rate][$key_trim1], 2, '.', ''); ?>">
												<p>
													<?
													echo fn_number_format($value[amount][$key_trim1], 2, '.', '');
													$total_wo_value += $value[amount][$key_trim1];
													?>

												</p>
											</td>

											<td width="150" align="left">
												<p>
													<?
													echo rtrim($supplier_name_string, ',');
													?>

												</p>
											</td>
											<td width="70" align="right" title="<? echo change_date_format($value[wo_date][$key_trim1]); ?>">
												<p>
													<?

													$tot = change_date_format($insert_date[0]);
													if ($value[wo_qnty][$key_trim1] <= 0) {
														$daysOnHand = datediff('d', $tot, $today);
													} else {
														$wo_date = $value[wo_date][$key_trim1];
														$wo_date = change_date_format($wo_date);
														$daysOnHand = datediff('d', $tot, $wo_date);;
													}
													echo $daysOnHand;

													?>&nbsp;</p>
											</td>
											<td width="70">
												<? $delivery_date = $value[delivery_date][$key_trim1];
												echo $delivery_date = change_date_format($delivery_date); ?>
											</td>
											<?
											$poIdArr = array_unique(explode(",", $po_id));
											$transfer_out = 0;
											$transfer_id = $issue_ret_qty = 0;
											foreach ($poIdArr as $poId) {
												$transfer_out += $transfer_out_data[$poId][$key_trim];
												$transfer_id += $transfer_in_data[$poId][$key_trim];
												$issue_ret_qty += $issue_qty_data_arr[$poId][issue_ret_qty][$key_trim]; //issue_qty_data_arr[$key][issue_ret_qty][$key_trim]
											}
											$inhouse_qnty = ($value[inhouse_qnty][$key_trim] + $transfer_id) - $value[receive_rtn_qty][$key_trim];
											$balance = $value[wo_qnty_trim_group][$key_trim] - $inhouse_qnty;
											$conv_rate = $conversion_factor_array[$value[trim_group_dtls][$key_trim1]]['con_factor'];

											$issue_qnty = $value[issue_qty][$key_trim] + $transfer_out;
											//echo $value[issue_qty][$key_trim].'+'.$transfer_out.'='.$issue_qty_data_arr[$key][issue_ret_qty][$key_trim].',';
											$issue_ret_qnty = $issue_ret_qty;
											$left_overqty = $inhouse_qnty - ($issue_qnty - $issue_ret_qnty / $conv_rate);
											$summary_array[inhouse_qnty][$key_trim] += $inhouse_qnty;
											$summary_array[inhouse_qnty_bl][$key_trim] += $balance;
											$summary_array[issue_qty][$key_trim] += ($issue_qnty - $issue_ret_qnty) / $conv_rate;
											$summary_array[left_overqty][$key_trim] += $left_overqty;

											$value_rate = $value[rate][$key_trim1];

											$summary_array[inhouse_value][$key_trim] += ($inhouse_qnty * $value_rate);

											$summary_array[issue_to_prod][$key_trim] += (($issue_qnty - $issue_ret_qnty) / $conv_rate) * $value_rate;

											$summary_array[left_overvalue][$key_trim] += ($left_overqty * $value_rate);
											$summary_array[rate][$key_trim] = $value_rate;
											?>
											<td width="90" align="right" title="<? echo "Inhouse-Qty: " . $value[inhouse_qnty][$key_trim] . "\nReturn Qty: " . $value[receive_rtn_qty][$key_trim]; ?>" rowspan="<? echo $rowspannn; ?>">
												<p><a href='#report_details' onclick="openmypage_inhouse('<? echo $po_id; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_inhouse_info');"><? echo fn_number_format($inhouse_qnty, 2, '.', ''); ?></a></p>
											</td>
											<td width="90" align="right" rowspan="<? echo $rowspannn; ?>">
												<p><? echo fn_number_format($balance, 2, '.', ''); ?></p>
											</td>
											<td width="90" align="right" title="<? echo "issue-Qty: " . $issue_qnty . "\nReturn Qty: " . $issue_ret_qnty . "\nConv Rate: " . $conv_rate; ?>" rowspan="<? echo $rowspannn; ?>">
												<p><a href='#report_details' onclick="openmypage_issue('<? echo $po_id; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_issue_info');">
														<? echo fn_number_format(($issue_qnty - $issue_ret_qnty) + $transfer_out_data[$key_trim], 2, '.', ''); ?></a></p>
											</td>
											<td align="right" rowspan="<? echo $rowspannn; ?>">
												<p><? echo fn_number_format($left_overqty, 2, '.', ''); ?></p>
											</td>
										<?
											$total_in_val += $inhouse_qnty;
										} else {
										?>
											<td width="100" title="<? echo $value[trim_group_from][$key_trim1];  ?>">
												<p>
													<? echo $item_library[$value[trim_group_dtls][$key_trim1]]; ?>
													&nbsp;</p>
											</td>
											<td width="100">
												<p>
													<?
													echo $value[brand_sup_ref][$key_trim1];
													//echo $row[csf('brand_sup_ref')];
													?>
													&nbsp;</p>
											</td>
											<td width="60" align="center">
												<p>
													<?
													if ($value[apvl_req][$key_trim1] == 1) echo "Yes";
													else echo "&nbsp;";
													?>
													&nbsp;
												</p>
											</td>
											<td width="80" align="center">
												<p>
													<?
													if ($value[apvl_req][$key_trim1] == 1) {

														$app_status = $app_status_arr[$value[job_no]][$value[trim_group_dtls][$key_trim1]];
														$approved_status = $approval_status[$app_status];
														$summary_array[item_app][$key_trim][all] += 1;
														if ($app_status == 3) {
															$summary_array[item_app][$key_trim][app] += 1;
														}
													} else {
														$approved_status = "";
													}
													echo $approved_status;
													?>
													&nbsp;
												</p>
											</td>
											<td width="100" align="right">
												<p>
													<?
													$insert_date = explode(" ", $value[insert_date][$key_trim1]);
													echo change_date_format($insert_date[0], '', '', ''); //echo change_date_format($row[csf('pre_date')],'','',1);
													?>&nbsp;</p>
											</td>
											<td width="100" align="right">
												<p>
													<a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $po_id; ?>', '<? echo $value[buyer_name]; ?>','<? echo $value[rate][$key_trim1]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>' ,'<? echo $value[booking_no][$key_trim1]; ?>','<? echo $value[description][$key_trim1]; ?>','<? echo rtrim($value[country_id][$key_trim1], ","); ?>','<? echo $value[trim_dtla_id][$key_trim1]; ?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
														<?
														$req_qty = fn_number_format($value[req_qnty][$key_trim1], 2, '.', '');
														echo $req_qty;
														$summary_array[req_qnty][$key_trim] += $value[req_qnty][$key_trim1];
														?>
													</a>

												</p>
											</td>
											<td width="100" align="right">
												<p><? echo fn_number_format($value[req_value][$key_trim1], 2);
													$total_pre_costing_value += $value[req_value][$key_trim1]; ?></p>
											</td>
											<?
											$wo_qnty = fn_number_format($value[wo_qnty][$key_trim1], 2);
											if ($wo_qnty > $req_qty) { $color_wo = "red"; } else if ($wo_qnty < $req_qty) { $color_wo = "yellow"; } else { $color_wo = ""; }

											$supplier_name_string = "";
											$supplier_id_arr = array_unique(explode(',', rtrim($value[supplier_id][$key_trim1], ",")));
											foreach ($supplier_id_arr as $supplier_id_arr_key => $supplier_id_arr_value) {
												$ex_sup_data = explode("**", $supplier_id_arr_value);
												if ($ex_sup_data[1] == 3 || $ex_sup_data[1] == 5) $suplier_name_arr = $company_library;
												else $suplier_name_arr = $lib_supplier_arr;
												$supplier_name_string .= $suplier_name_arr[$ex_sup_data[0]] . ",";
											}

											$booking_no_arr = array_unique(explode(',', rtrim($value[booking_no][$key_trim1], ",")));
											$main_booking_no_large_data = "";
											foreach ($booking_no_arr as $booking_no1) {
												if ($main_booking_no_large_data == "") $main_booking_no_large_data = $booking_no1;
												else $main_booking_no_large_data .= "," . $booking_no1;
											}
											?>
											<td width="90" align="right" title="<? //echo 'conversion_factor='.$value[conversion_factor_rate][$key_trim1]; ?>" bgcolor="<? echo $color_wo; ?>">
												<p><a href='#report_details' onclick="openmypage('<? echo $po_id; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','<? echo $value[job_no]; ?>','<? echo $main_booking_no_large_data; ?>','<? echo $value[trim_dtla_id][$key_trim1]; ?>','booking_info');">
														<?
														//$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]['wo_qnty']
														echo fn_number_format($value[wo_qnty][$key_trim1], 2, '.', '');
														$summary_array[wo_qnty][$key_trim] += $value[wo_qnty][$key_trim1];
														?>
													</a></p>
											</td>

											<td width="60" align="center">
												<p><?
													echo $unit_of_measurement[$item_arr[$value[trim_group_dtls][$key_trim1]]['order_uom']];
													$summary_array[cons_uom][$key_trim] = $item_arr[$value[trim_group_dtls][$key_trim1]]['order_uom']; 
													?>&nbsp;</p>
											</td>

											<td width="100" align="right" title="<? echo fn_number_format($value[rate][$key_trim1], 2, '.', ''); ?>">
												<p>
													<?
													echo fn_number_format($value[amount][$key_trim1], 2, '.', '');
													$total_wo_value += $value[amount][$key_trim1];
													?>
												</p>
											</td>
											<td width="150" align="left"><p><? echo rtrim($supplier_name_string, ','); ?></p></td>
											<td width="70" align="right" title="<? echo change_date_format($value[wo_date][$key_trim1]); ?>">
												<p>
													<?
													$tot = change_date_format($insert_date[0]);
													if ($value[wo_qnty][$key_trim1] <= 0) {
														$daysOnHand = datediff('d', $tot, $today);
													} else {
														$wo_date = $value[wo_date][$key_trim1];
														$wo_date = change_date_format($wo_date);
														$daysOnHand = datediff('d', $tot, $wo_date);;
													}
													echo $daysOnHand;
													?>&nbsp;</p>
											</td>
											<td width="70">
												<? $delivery_date = $value[delivery_date][$key_trim1];
												echo $delivery_date = change_date_format($delivery_date); ?>
											</td>
										<?
										}
										?>
									</tr>
									<?
									$gg++;
								} // end  foreach($value[$key_trim] as $key_trim1=>$value_trim1)
							}
							$i++;
						}
					?>
					</table>
					<table class="rpt_table" width="2630" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
							<th width="30"></th>
							<th width="50"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="90"></th>
							<th width="80" align="right" id="total_order_qnty"><? //echo fn_number_format($total_order_qnty,0); ?></th>
							<th width="50"></th>
							<th width="80" align="right" id="total_order_qnty_in_pcs"><? //echo fn_number_format($total_order_qnty_in_pcs,0); ?></th>
							<th width="80"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="60"></th>
							<th width="80"></th>
							<th width="100"></th>
							<th width="100" align="right" id="value_req_qnty"><? //echo fn_number_format($total_req_qnty,2); ?></th>
							<th width="100" align="right" id="value_pre_costing"><? echo fn_number_format($total_pre_costing_value, 2); ?></th>
							<th width="90" align="right" id="value_wo_qty"><? //echo fn_number_format($total_wo_qnty,2); ?></th>
							<th width="60" align="right"></th>
							<th width="100" align="right" id=""><? echo fn_number_format($total_wo_value, 2); ?></th>
							<th width="150" align="right" id=""></th>
							<th width="70" align="right"><p><? //echo fn_number_format($req_value,2,'.',''); ?>&nbsp;</p></th>
							<th width="70" align="right"><p><? //echo fn_number_format($req_value,2,'.',''); ?>&nbsp;</p></th>
							<th width="90" align="right" id="value_in_qty"><? //echo fn_number_format($total_in_val,2); ?></th>
							<th width="90" align="right" id="value_rec_qty"><? //echo fn_number_format($total_rec_bal_qnty,2); ?></th>
							<th width="90" align="right" id="value_issue_qty"><? //echo fn_number_format($total_issue_qnty,2); ?></th>
							<th align="right" id="value_leftover_qty"><? //echo fn_number_format($total_leftover_qnty,2); ?></th>
						</tfoot>
					</table>
				</div>
				<table>
					<tr>
						<td height="15"></td>
					</tr>
				</table>
				<u><b>Summary</b></u>
				<table class="rpt_table" width="1400" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="110">Item</th>
						<th width="60">UOM</th>
						<th width="80">Approved %</th>
						<th width="110">Req Qty</th>
						<th width="110">WO Qty</th>
						<th width="80">WO %</th>
						<th width="110">In-House Qty</th>
						<th width="80">In-House %</th>
						<th width="80">In-House Value</th>
						<th width="110">In-House Balance Qty</th>
						<th width="110">Issue Qty</th>
						<th width="80">Issue %</th>
						<th width="80">Issue Value</th>
						<th width="80">Left Over</th>
						<th>Left Over Value</th>
					</thead>
					<?
					$z = 1;
					$tot_req_qnty_summary = 0;
					foreach ($summary_array[trim_group] as $key_trim => $value) {
						if ($z % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$inhouse_value = $summary_array[inhouse_value][$key_trim];
						$left_overvalue = $summary_array[left_overvalue][$key_trim];
						$issue_to_prod = $summary_array[issue_to_prod][$key_trim];
						$rate = $summary_array[rate][$key_trim];
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr2_<? echo $z; ?>','<? echo $bgcolor; ?>')" id="tr2_<? echo $z; ?>">
							<td><? echo $z; ?></td>
							<td>
								<p><? echo $item_library[$key_trim]; ?></p>
							</td>
							<td align="center">
								<?
								echo $unit_of_measurement[$summary_array[cons_uom][$key_trim]];
								?></td>
							<td align="right"><? $app_perc = ($summary_array[item_app][$key_trim][app] * 100) / $summary_array[item_app][$key_trim][all];
												echo fn_number_format($app_perc, 2); ?></td>
							<td align="right"><? echo fn_number_format($summary_array[req_qnty][$key_trim], 2); ?></td>
							<td align="right"><? echo fn_number_format($summary_array[wo_qnty][$key_trim], 2); ?></td>
							<td align="right"><? $wo_per = $summary_array[wo_qnty][$key_trim] / $summary_array[req_qnty][$key_trim] * 100;
												echo fn_number_format($wo_per, 2) . '%'; ?></td>
							<td align="right"><? echo fn_number_format($summary_array[inhouse_qnty][$key_trim], 2); ?></td>
							<td align="right"><? $in_per = $summary_array[inhouse_qnty][$key_trim] / $summary_array[wo_qnty][$key_trim] * 100;
												echo fn_number_format($in_per, 2) . '%'; ?></td>
							<td align="right"><? echo fn_number_format($inhouse_value, 2); ?></td>
							<td align="right"><? echo fn_number_format($summary_array[inhouse_qnty_bl][$key_trim], 2);
												$in_house_bal += ($value['wo'] - $value['in']); ?></td>
							<td align="right"><? echo fn_number_format($summary_array[issue_qty][$key_trim], 2); ?></td>
							<td align="right"><? $wo_per = $summary_array[issue_qty][$key_trim] / $summary_array[wo_qnty][$key_trim] * 100;
												echo fn_number_format($wo_per, 2) . '%'; ?></td>
							<td align="right"><? echo fn_number_format($issue_to_prod, 2); ?></td>
							<td align="right"><? echo fn_number_format($summary_array[left_overqty][$key_trim], 2); ?></td>
							<td align="right" title="rate: <?= $rate; ?>"><? echo fn_number_format($left_overvalue, 2); ?></td>
						</tr>
					<?
						$z++;
					}
					?>
					<tfoot>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_req_qnty_summary,2); 
											?></th>
						<th align="right"><? //echo fn_number_format($tot_wo_qnty_summary,2); 
											?></th>
						<th>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_in_qnty_summary,2); 
											?></th>
						<th>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($in_house_bal,2); 
											?></th>
						<th align="right"><? //echo fn_number_format($tot_issue_qnty_summary,2); 
											?></th>
						<th>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_leftover_qnty_summary,2); 
											?></th>
					</tfoot>
				</table>
			</fieldset>
		</div>
	<?
	}

	foreach (glob("*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$action";
	exit();
}

if ($action == "report_generate2") {
	$company_library = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
	$buyer_short_name_library = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$item_library = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
	$country_name_library = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$lib_supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$process = array(&$_POST);
	// print_r($process);
	// die;
	extract(check_magic_quote_gpc($process));
	$company_name = str_replace("'", "", $cbo_company_name);
	$serch_by = str_replace("'", "", $cbo_search_by);
	$buyer_id_cond = "";
	if (str_replace("'", "", $cbo_buyer_name) == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				$buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else $buyer_id_cond = "";
		} else $buyer_id_cond = "";
	} else {
		$buyer_id_cond = " and a.buyer_name=$cbo_buyer_name"; //.str_replace("'","",$cbo_buyer_name)
	}

	//condition add
	if ($db_type == 0) {
		if (str_replace("'", "", $cbo_year) != 0) $year_cond = " and year(a.insert_date)=" . str_replace("'", "", $cbo_year) . "";
		else $year_cond = "";
	} else {
		if (str_replace("'", "", $cbo_year) != 0) $year_cond = " and to_char(a.insert_date,'YYYY')=" . str_replace("'", "", $cbo_year) . "";
		else $year_cond = "";
	}

	//echo '=='.$cbo_company_name.'___'.$txt_job_no.'___'.$txt_order_no.'___';

	$txt_job_no = str_replace("'", "", $txt_job_no);
	$txt_job_no = trim($txt_job_no);
	if ($txt_job_no != "" || $txt_job_no != 0) {
		$jobcond = "and a.job_no_prefix_num='" . $txt_job_no . "'";
	} else  $jobcond = "";

	if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond = "";
	else $item_group_cond = "and e.trim_group in(" . str_replace("'", "", $cbo_item_group) . ")";

	$date_type = str_replace("'", "", $cbo_date_type);
	$start_date = str_replace("'", "", trim($txt_date_from));
	$end_date = str_replace("'", "", trim($txt_date_to));
	$date_cond = '';
	if ($date_type == 1) {
		if ($start_date != "" && $end_date != "") $date_cond = "and c.country_ship_date between '$start_date' and '$end_date'";
		else $date_cond = "";
	} else if ($date_type == 2) {
		if ($start_date == "" && $end_date == "") $date_cond = "";
		else $date_cond = "and b.pub_shipment_date between '$start_date' and '$end_date'";
	} else if ($date_type == 3) {
		if ($start_date == "" && $end_date == "") $date_cond = "";
		else $date_cond = "and b.shipment_date between '$start_date' and '$end_date'";
	} else if ($date_type == 4) {
		if ($db_type == 0) {
			if ($start_date == "" && $end_date == "") $date_cond = "";
			else $date_cond = "and b.insert_date between '" . $start_date . "' and '" . $end_date . " 23:59:59'";
		} else {
			if ($start_date == "" && $end_date == "") $date_cond = "";
			else $date_cond = "and b.insert_date between '" . $start_date . "' and '" . $end_date . " 11:59:59 PM'";
		}
	}

	if (str_replace("'", "", $hidd_job_id) != "")  $jobcond = "and a.id in(" . str_replace("'", "", $hidd_job_id) . ")";
	else  if (str_replace("'", "", $txt_job_no) == "") $jobcond = "";
	else $jobcond = "and a.job_no_prefix_num like '%" . str_replace("'", "", $txt_job_no) . "%' ";

	if (str_replace("'", "", $hidd_job_id) != "")  $style_ref_cond = "and a.id in(" . str_replace("'", "", $hidd_job_id) . ")";
	else  if (str_replace("'", "", $txt_style_ref) == "") $style_ref_cond = "";
	else $style_ref_cond = "and a.style_ref_no like '%" . str_replace("'", "", $txt_style_ref) . "%' ";

	if (str_replace("'", "", $hidd_po_id) != "") {
		$ordercond = "and b.id in (" . str_replace("'", "", $hidd_po_id) . ")";
		$jobcond = "";
	} else if (str_replace("'", "", $txt_order_no) == "") $ordercond = "";
	else $ordercond = "and b.po_number like '%" . str_replace("'", "", $txt_order_no) . "%' ";

	$file_no = str_replace("'", "", $txt_file_no);
	$internal_ref = str_replace("'", "", $txt_internal_ref);
	if ($file_no == "") $file_no_cond = "";
	else $file_no_cond = " and b.file_no='" . trim($file_no) . "' ";
	if ($internal_ref == "") $internal_ref_cond = "";
	else $internal_ref_cond = " and b.grouping='" . trim($internal_ref) . "' ";
	//echo $file_no_cond.'=='.$internal_ref_cond;die;
	$print_report_format = return_field_value("format_id", "lib_report_template", "template_name =" . $company_name . " and module_id=2 and report_id=43 and is_deleted=0 and status_active=1");

	$print_button = explode(",", $print_report_format);
	$print_button_first = array_shift($print_button);
	//echo $print_button_first.'D';
	if ($print_button_first == 50) $precost_button = "preCostRpt";
	else if ($print_button_first == 25) $precost_button = "budgetsheet2";
	else if ($print_button_first == 51) $precost_button = "preCostRpt2";
	else if ($print_button_first == 52) $precost_button = "bomRpt";
	else if ($print_button_first == 63) $precost_button = "bomRpt2";
	else if ($print_button_first == 156) $precost_button = "accessories_details";
	else if ($print_button_first == 157) $precost_button = "accessories_details2";
	else if ($print_button_first == 158) $precost_button = "preCostRptWoven";
	else if ($print_button_first == 159) $precost_button = "bomRptWoven";
	else if ($print_button_first == 170) $precost_button = "preCostRpt3";
	else if ($print_button_first == 171) $precost_button = "preCostRpt4";
	else if ($print_button_first == 142) $precost_button = "preCostRptBpkW";
	else if ($print_button_first == 192) $precost_button = "checkListRpt";
	else if ($print_button_first == 197) $precost_button = "bomRpt3";
	else if ($print_button_first == 211) $precost_button = "mo_sheet";
	else if ($print_button_first == 221) $precost_button = "fabric_cost_detail";
	else if ($print_button_first == 173) $precost_button = "preCostRpt5";
	else if ($print_button_first == 238) $precost_button = "summary";
	else if ($print_button_first == 215) $precost_button = "budget3_details";
	else if ($print_button_first == 270) $precost_button = "preCostRpt6";
	else if ($print_button_first == 769) $precost_button = "preCostRpt7";
	else  $precost_button = "";

	if (str_replace("'", "", $cbo_search_by) == 1) {
		ob_start();
	?>
		<div style="width:2600px">
			<fieldset style="width:100%;">
				<table width="2600">
					<tr class="form_caption">
						<td colspan="27" align="center">Accessories Followup Report</td>
					</tr>
					<tr class="form_caption">
						<td colspan="27" align="center"><? echo $company_library[$company_name]; ?></td>
					</tr>
				</table>
				<table class="rpt_table scroll" width="2500" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="50">Buyer</th>
						<th width="100">Job No</th>
						<th width="100">Style Ref</th>
						<th width="100">Internal Ref</th>
						<th width="100">File No</th>

						<th width="90">Order No</th>
						<th width="80">Order Qty</th>
						<th width="50">UOM</th>
						<th width="80">Qnty (Pcs)</th>
						<th width="80">Shipment Date</th>
						<th width="100">Trims Name</th>
						<th width="100">Remark</th>
						<th width="100">Brand/Sup Ref</th>
						<th width="60">Appr Req.</th>
						<th width="80">Approve Status</th>
						<th width="100">Item Entry Date</th>
						<th width="100">Req Qnty</th>
						<th width="100">Pre Costing Value</th>
						<th width="90">WO Qnty</th>
						<th width="60">Trims UOM</th>
						<th width="100">WO Value</th>
						<th width="150">Supplier</th>
						<th width="70">WO Delay Days</th>
						<th width="90">In-House Qnty</th>
						<th width="90">In-House Value</th>
						<th width="90">Receive Balance</th>
						<th width="90">Issue to Prod.</th>
						<th width="70">Left Over/Balance</th>
					</thead>

				</table>
				<div style="width:2520px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="2500" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<?
						$conversion_factor_array = array();
						$conversion_factor = sql_select("select id ,trim_uom,conversion_factor from  lib_item_group  ");
						foreach ($conversion_factor as $row_f) {
							$conversion_factor_array[$row_f[csf('id')]]['con_factor'] = $row_f[csf('conversion_factor')];
							$conversion_factor_array[$row_f[csf('id')]]['cons_uom'] = $row_f[csf('trim_uom')];
						}
						unset($conversion_factor);
						
						$today = date("Y-m-d");

						$condition = new condition();
						if (str_replace("'", "", $company_name) > 0) {
							$condition->company_name("=$company_name");
						}
						if (str_replace("'", "", $cbo_buyer_name) > 0) {
							$condition->buyer_name("=$cbo_buyer_name");
						}
						if (str_replace("'", "", $txt_job_no) != '') {
							$condition->job_no_prefix_num("=$txt_job_no");
						}
						if (str_replace("'", "", $txt_style_ref) != '') {
							$condition->style_ref_no("='" . str_replace("'", "", $txt_style_ref) . "'");
						}

						if (str_replace("'", "", $txt_order_no) != '') {
							$condition->po_number("='" . str_replace("'", "", $txt_order_no) . "'");
						}

						if (str_replace("'", "", $txt_file_no) != '') {
							$condition->file_no("='" . str_replace("'", "", $txt_file_no) . "'");
						}

						if (str_replace("'", "", $txt_internal_ref) != '') {
							$condition->grouping("='" . str_replace("'", "", $txt_internal_ref) . "'");
						}

						if (str_replace("'", "", $txt_date_from) != "" && str_replace("'", "", $txt_date_to) != "") {
							$start_date = (str_replace("'", "", $txt_date_from));
							$end_date = (str_replace("'", "", $txt_date_to));
							if ($date_type == 1) {
								$condition->country_ship_date(" between '$start_date' and '$end_date'");
							}
							if ($date_type == 2) {
								$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
							}
						}

						$condition->init();
						$trim = new trims($condition);
						//echo $trim->getQuery(); die;
						$trim_group_qty_arr = $trim->getQtyArray_by_orderAndItemid();
						$trim_qty = $trim->getQtyArray_by_orderCountryAndPrecostdtlsid();
						
						$sql_qry = "SELECT a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, sum(c.order_quantity) as order_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, a.total_set_qnty, b.pub_shipment_date
					from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
					where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond " . set_user_lavel_filtering(' and a.buyer_name', 'buyer_id') . " $date_cond $style_ref_cond $year_cond $jobcond  $ordercond $file_no_cond $internal_ref_cond
					group by a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, a.total_set_qnty, b.pub_shipment_date
					order by b.id";
						//echo $sql_qry;
						$sql_query = sql_select($sql_qry); $tot_rows = count($sql_query); $poididarr = array(); $po_data_arr = array(); $po_id_string = "";
						if($tot_rows<1)
						{
							echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
							die;
						}
						foreach ($sql_query as $row) {
							$poididarr[$row[csf('id')]] = $row[csf('id')];
							$dzn_qnty = 0;
							if ($row[csf('costing_per')] == 1) $dzn_qnty = 12;
							else if ($row[csf('costing_per')] == 3) $dzn_qnty = 12 * 2;
							else if ($row[csf('costing_per')] == 4) $dzn_qnty = 12 * 3;
							else if ($row[csf('costing_per')] == 5) $dzn_qnty = 12 * 4;
							else $dzn_qnty = 1;

							$po_qty = 0; $req_qnty = $req_value = 0; 

							$po_data_arr[$row[csf('id')]][job_no] = $row[csf('job_no')];
							$po_data_arr[$row[csf('id')]][buyer_name] = $row[csf('buyer_name')];
							$po_data_arr[$row[csf('id')]][job_no_prefix_num] = $row[csf('job_no_prefix_num')];
							$po_data_arr[$row[csf('id')]][style_ref_no] = $row[csf('style_ref_no')];

							$po_data_arr[$row[csf('id')]][grouping] = $row[csf('grouping')];
							$po_data_arr[$row[csf('id')]][file_no] = $row[csf('file_no')];
							$po_data_arr[$row[csf('id')]][order_uom] = $row[csf('order_uom')];
							$po_data_arr[$row[csf('id')]][po_id] = $row[csf('id')];
							$po_data_arr[$row[csf('id')]][po_number] = $row[csf('po_number')];
							$po_data_arr[$row[csf('id')]][dzn_qnty] = $dzn_qnty;
						}
						unset($sql_query);
						
						$con = connect();
						execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from =4 and ENTRY_FORM=31");
						oci_commit($con);
						fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 31, 4, $poididarr, $empty_arr);//PO ID
						disconnect($con);
						
						$app_sql=sql_select("select a.job_no_mst, a.accessories_type_id, a.approval_status from wo_po_trims_approval_info a, gbl_temp_engine d where a.is_deleted=0 and a.status_active=1 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=4");
						$app_status_arr = array();
						foreach ($app_sql as $row) {
							$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]] = $row[csf("approval_status")];
						}
						unset($app_sql);

						$sql_po_qty_country_wise_arr=array(); $po_job_arr=array();
						$sql_po_qty_country_wise=sql_select("select b.id, b.job_no_mst, c.country_id, c.order_quantity as order_quantity_pcs, b.po_quantity
						from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, gbl_temp_engine d
						where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=4 and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $year_cond $jobcond $ordercond ");
						
						foreach( $sql_po_qty_country_wise as $sql_po_qty_country_wise_row)
						{
							$sql_po_qty_country_wise_arr[$sql_po_qty_country_wise_row[csf('id')]][$sql_po_qty_country_wise_row[csf('country_id')]]+=$sql_po_qty_country_wise_row[csf('order_quantity_pcs')];
							$po_job_arr[$sql_po_qty_country_wise_row[csf('id')]]=$sql_po_qty_country_wise_row[csf('job_no_mst')];
							$po_qnty_arr[$sql_po_qty_country_wise_row[csf('id')]]['order_quantity_pcs']+=$sql_po_qty_country_wise_row[csf('order_quantity_pcs')];
							$po_qnty_arr[$sql_po_qty_country_wise_row[csf('id')]]['order_quantity']=$sql_po_qty_country_wise_row[csf('po_quantity')];
						}
						unset($sql_po_qty_country_wise);

					$sql_trim = "SELECT  d.costing_per, e.id as trim_dtla_id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate as rate_item, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, f.rate, f.cons,f.country_id,f.po_break_down_id as id 
					from wo_pre_cost_trim_co_cons_dtls f, wo_pre_cost_trim_cost_dtls e, wo_pre_cost_mst d, gbl_temp_engine t
					where  f.job_id=e.job_id and e.id=f.wo_pre_cost_trim_cost_dtls_id $item_group_cond and f.cons > 0 and e.job_id =d.job_id and f.is_deleted=0 and f.status_active=1 and e.is_deleted=0 and e.status_active=1 and d.is_deleted=0 and d.status_active=1 and t.user_id = " . $user_id . " and t.ref_from=4 and t.entry_form=31 and t.ref_val=f.po_break_down_id
					group by  d.costing_per, e.id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, f.rate, f.cons,f.country_id,f.po_break_down_id
					order by f.po_break_down_id, e.trim_group";
					$res_trim = sql_select($sql_trim);
						//echo $sql_qry;die;
					if (count($res_trim)<1) {
						echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
						die;
					}
					$i = 1;
					foreach ($res_trim as $row) {
						$po_qty = 0; $req_qnty = $req_value = 0; //$country_id='';
						$dzn_qnty = $po_data_arr[$row[csf('id')]][dzn_qnty];

						if ($row[csf('country_id')] == 0) {
							$req_qnty += $trim_qty[$row[csf('id')]][$row[csf('country_id')]][$row[csf('trim_dtla_id')]];
						} else {
							$country_id = array_unique(explode(",", $row[csf('country_id')]));
							foreach ($country_id as $cid) {
								$req_qnty += $trim_qty[$row[csf('id')]][$cid][$row[csf('trim_dtla_id')]];
							}
						}

						// $req_qnty=($row[csf('cons')]/$dzn_qnty)*$po_qty;
						$req_value = $row[csf('rate')] * $req_qnty;
						$po_data_arr[$row[csf('id')]][order_quantity_set] = $po_qnty_arr[$row[csf('id')]]['order_quantity'];
						$po_data_arr[$row[csf('id')]][order_quantity] = $po_qnty_arr[$row[csf('id')]]['order_quantity_pcs'];
						$po_data_arr[$row[csf('id')]][pub_shipment_date] = change_date_format($row[csf('pub_shipment_date')]);
						$po_id_string .= $row[csf('id')] . ",";

						$po_data_arr[$row[csf('id')]][trim_dtla_id][$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')]; // for rowspan
						$po_data_arr[$row[csf('id')]][trim_group][$row[csf('trim_group')]] = $row[csf('trim_group')];
						$po_data_arr[$row[csf('id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')]; // for rowspannn
						$po_data_arr[$row[csf('id')]][trim_group_dtls][$row[csf('trim_dtla_id')]] = $row[csf('trim_group')];
						$po_data_arr[$row[csf('id')]][remark][$row[csf('trim_dtla_id')]] = $row[csf('remark')];

						$po_data_arr[$row[csf('id')]][brand_sup_ref][$row[csf('trim_dtla_id')]] = $row[csf('brand_sup_ref')];
						$po_data_arr[$row[csf('id')]][apvl_req][$row[csf('trim_dtla_id')]] = $row[csf('apvl_req')];
						$po_data_arr[$row[csf('id')]][insert_date][$row[csf('trim_dtla_id')]] = $row[csf('insert_date')];

						$po_data_arr[$row[csf('id')]][req_qnty][$row[csf('trim_dtla_id')]] = $req_qnty;
						$po_data_arr[$row[csf('id')]][req_value][$row[csf('trim_dtla_id')]] = $req_value;
						$po_data_arr[$row[csf('id')]][cons_uom][$row[csf('trim_dtla_id')]] = $conversion_factor_array[$row[csf('trim_group')]]['cons_uom']; //$row[csf('cons_uom')];

						$po_data_arr[$row[csf('id')]][trim_group_from][$row[csf('trim_dtla_id')]] = "Pre_cost";

						$po_data_arr[$row[csf('id')]][rate][$row[csf('trim_dtla_id')]] = $row[csf('rate')];
						$po_data_arr[$row[csf('id')]][description][$row[csf('trim_dtla_id')]] = $row[csf('description')];
						$po_data_arr[$row[csf('id')]][country_id][$row[csf('trim_dtla_id')]] = implode(",", array_unique(explode(",", $row[csf('country_id')]))); 
					}
					unset($trimCountryArr);

					$wo_sql_without_precost = sql_select("select min(a.booking_date) as booking_date, b.job_no, LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, LISTAGG(CAST( a.supplier_id || '**' || a.pay_mode AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount/b.exchange_rate) as amount, sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b, gbl_temp_engine t where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name  and t.user_id = " . $user_id . " and t.ref_from=4 and t.entry_form=31 and t.ref_val=b.po_break_down_id  group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id"); //and item_from_precost=2
						
					foreach ($wo_sql_without_precost as $wo_row_without_precost) {
						$conversion_factor_rate = $conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['con_factor'];
						$cons_uom = $conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['cons_uom'];
						$booking_no = $wo_row_without_precost[csf('booking_no')];
						$supplier_id = $wo_row_without_precost[csf('supplier_id')];
						$wo_qnty = $wo_row_without_precost[csf('wo_qnty')]; //*$conversion_factor_rate;
						$amount = $wo_row_without_precost[csf('amount')];
						$wo_date = $wo_row_without_precost[csf('booking_date')];

						if ($wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] == "" || $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] == 0) {
							$trim_dtla_id = max($po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_dtla_id]) + 1;
							$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_dtla_id][$trim_dtla_id] = $trim_dtla_id; // for rowspan
							$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group][$wo_row_without_precost[csf('trim_group')]] = $wo_row_without_precost[csf('trim_group')];
							$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id] = $trim_dtla_id; // for rowspannn
							$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group_dtls][$trim_dtla_id] = $wo_row_without_precost[csf('trim_group')];
							$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][cons_uom][$trim_dtla_id] = $cons_uom;

							$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group_from][$trim_dtla_id] = "Booking Without Pre_cost";
						} else {
							$trim_dtla_id = $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
						}
						//$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_dtla_id][$trim_dtla_id]=$trim_dtla_id;// for rowspan
						//$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group][$wo_row_without_precost[csf('trim_group')]]=$wo_row_without_precost[csf('trim_group')];
						// $style_data_arr[$wo_row_without_precost[csf('job_no')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						//$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group_dtls][$trim_dtla_id]=$wo_row_without_precost[csf('trim_group')];

						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_qnty][$trim_dtla_id] += $wo_qnty;
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][amount][$trim_dtla_id] += $amount;
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_date][$trim_dtla_id] = $wo_date;
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_qnty_trim_group][$wo_row_without_precost[csf('trim_group')]] += $wo_qnty;

						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][booking_no][$trim_dtla_id] = $booking_no;
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][supplier_id][$trim_dtla_id] = $supplier_id;
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][conversion_factor_rate][$trim_dtla_id] = $conversion_factor_rate;
					}
					unset($wo_sql_without_precost);

					$receive_qty_data = sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity,a.rate  from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a, order_wise_pro_details b, gbl_temp_engine t where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   and t.user_id = " . $user_id . " and t.ref_from =4 and t.entry_form=31 and t.ref_val=b.po_breakdown_id group by b.po_breakdown_id, a.item_group_id,a.rate order by a.item_group_id ");

					foreach ($receive_qty_data as $row) {
						if ($po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]] == "" || $po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]] == 0) {
							$cons_uom = $conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];
							$trim_dtla_id = max($po_data_arr[$row[csf('po_breakdown_id')]][trim_dtla_id]) + 1;
							$po_data_arr[$row[csf('po_breakdown_id')]][trim_dtla_id][$trim_dtla_id] = $trim_dtla_id; // for rowspan
							$po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]] = $row[csf('item_group_id')];
							$po_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$trim_dtla_id] = $trim_dtla_id; // for rowspannn
							$po_data_arr[$row[csf('po_breakdown_id')]][trim_group_dtls][$trim_dtla_id] = $row[csf('item_group_id')];
							$po_data_arr[$row[csf('po_breakdown_id')]][cons_uom][$trim_dtla_id] = $cons_uom;
							$po_data_arr[$row[csf('po_breakdown_id')]][trim_group_from][$trim_dtla_id] = "Trim Receive";
						}
						$po_data_arr[$row[csf('po_breakdown_id')]][inhouse_qnty][$row[csf('item_group_id')]] += $row[csf('quantity')];
						$po_data_arr[$row[csf('po_breakdown_id')]][inhouse_rate][$row[csf('item_group_id')]] = $row[csf('rate')];
					}
					unset($receive_qty_data);

					$receive_rtn_qty_data = sql_select("select d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e, gbl_temp_engine t where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and t.user_id = " . $user_id . " and t.ref_from=4 and t.entry_form=31 and t.ref_val=d.po_breakdown_id group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");

					foreach ($receive_rtn_qty_data as $row) {
						$po_data_arr[$row[csf('po_breakdown_id')]][receive_rtn_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
					}
					unset($receive_rtn_qty_data);

					$issue_qty_data = sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a, order_wise_pro_details b, gbl_temp_engine t where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and t.user_id = " . $user_id . " and t.ref_from=4 and t.entry_form=31 and t.ref_val=b.po_breakdown_id  group by b.po_breakdown_id, a.item_group_id");
					foreach ($issue_qty_data as $row) {
						$po_data_arr[$row[csf('po_breakdown_id')]][issue_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
					}
					unset($issue_qty_data);

					$transfer_sql_in = sql_select("SELECT  a.from_order_id,a.to_order_id,b.item_group,b.transfer_qnty as qnty FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, gbl_temp_engine c WHERE a.id = b.mst_id and c.user_id=" . $user_id ." and c.ref_from=4 and c.entry_form=31 and c.ref_val=a.to_order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

					foreach ($transfer_sql_in as $row) {
						$po_data_arr[$row[csf('to_order_id')]][transfer_in][$row[csf('item_group')]] += $row[csf('qnty')];
					}
					unset($transfer_sql_in);

					$transfer_sql_out = sql_select("SELECT a.from_order_id, a.to_order_id, b.item_group, b.transfer_qnty as qnty
					FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, gbl_temp_engine c
					WHERE a.id = b.mst_id and c.user_id=" . $user_id ." and c.ref_from=4 and c.entry_form=31 and c.ref_val=a.from_order_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

					$transfer_out_data = array();
					foreach ($transfer_sql_out as $row) {
						$transfer_out_data[$row[csf('from_order_id')]][$row[csf('item_group')]] += $row[csf('qnty')];
					}
					unset($transfer_sql_out);

					$issue_qty_data_arr = array();
					$sql_issue_ret = ("select c.po_breakdown_id as po_id, p.item_group_id,SUM(c.quantity) as quantity
				from inv_transaction b, order_wise_pro_details c, product_details_master p, gbl_temp_engine d
				where c.entry_form=73 and p.id=b.prod_id and d.user_id=" . $user_id ." and d.ref_from=4 and d.entry_form=31 and d.ref_val=c.po_breakdown_id and b.id=c.trans_id and b.transaction_type=4 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 group by c.po_breakdown_id,p.item_group_id");
					$issue_result = sql_select($sql_issue_ret);
					foreach ($issue_result as $row) {
						$issue_qty_data_arr[$row[csf('po_id')]][issue_ret_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
					}
					unset($issue_result);
					
					$con = connect();
					execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=4 and ENTRY_FORM=31");
					oci_commit($con);
					disconnect($con);
						
					$total_pre_costing_value = 0; $total_wo_value = 0; $summary_array = array();
					$i = 1; $x = 0;
						
						foreach ($po_data_arr as $key => $value) {
							$z = 1;
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							foreach ($value[trim_group] as $key_trim => $value_trim) {
								$y = 1;
								$summary_array[trim_group][$key_trim] = $key_trim;
								foreach ($value[$key_trim] as $key_trim1 => $value_trim1) {
									if ($z == 1) $style_color = ''; else $style_color = $bgcolor . "; border: none";
									$z++; $x++; $y++;
									if ($y == 1) $style_colory = ''; else $style_colory = $bgcolor . "; border: none";
									
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $x; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $x; ?>">
										<td width="30" style=" color: <? echo $style_color ?>" title="<? echo $po_qty; ?>"><? echo $i; ?></td>
										<td width="50" style=" color: <? echo $style_color ?>">
											<p><? echo $buyer_short_name_library[$value[buyer_name]]; ?>&nbsp;</p>
										</td>
										<td width="100" style=" color: <? echo $style_color ?>" align="center">
											<p><? echo $value[job_no_prefix_num]; ?>&nbsp;</p>
										</td>
										<td width="100" style="word-break: break-all;color: <? echo $style_color ?>">
											<p><? echo $value[style_ref_no]; ?>&nbsp;</p>
										</td>
										<td width="100" style="word-break: break-all;color: <? echo $style_color ?>">
											<p><? echo $value[grouping]; ?></p>
										</td>
										<td width="100" style="word-break: break-all;color: <? echo $style_color ?>">
											<p><? echo $value[file_no]; ?></p>
										</td>
										<td width="90" style="word-break: break-all;color: <? echo $style_color ?>">
											<p>
												<a style="word-break: break-all;color: <? echo $style_color ?>" href='#report_details' onclick="generate_report('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $precost_button; ?>');">
													<?
													$po_number = $value[po_number];
													//$po_number=implode(",", $value[po_id]);
													echo $po_number;
													?>
												</a>&nbsp;
											</p>
										</td>
										<td width="80" style="word-break: break-all;color: <? echo $style_color ?>" align="right">
											<p>
												<a style="word-break: break-all;color: <? echo $style_color ?>" href='#report_details' onclick="order_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $value[po_id]; ?>', '<? echo $value[buyer_name]; ?>' ,'order_qty_data');"><? echo fn_number_format($value[order_quantity_set], 0, '.', ''); ?>
												</a>
												&nbsp;
											</p>
										</td>

										<td width="50" align="center" style="word-break: break-all;color: <? echo $style_color ?>">
											<p><? echo $unit_of_measurement[$value[order_uom]]; ?>&nbsp;</p>
										</td>
										<td width="80" align="right" style="word-break: break-all;color: <? echo $style_color ?>">
											<p><? echo fn_number_format($value[order_quantity], 0, '.', ''); ?>&nbsp;</p>
										</td>
										<td width="80" align="center" style="word-break: break-all;color: <? echo $style_color ?>">
											<p>
												<?
												$pub_shipment_date = $value[pub_shipment_date];
												echo $pub_shipment_date;
												?>
												&nbsp;
											</p>
										</td>
										<td width="100" title="<? echo $value[trim_group_from][$key_trim1];  ?>" style="word-break: break-all;">
											<p>
												<?= $item_library[$value[trim_group_dtls][$key_trim1]];	//echo $value[trim_group_dtls][$key_trim1];
												?>
											</p>
										</td>
										<td width="100" title="<? //echo $value[trim_group_from][$key_trim1];  
																?>">
											<p>
												<?
												//echo $item_library[$value[trim_group_dtls][$key_trim1]];
												echo $value[remark][$key_trim1];
												?>
												&nbsp;</p>
										</td>

										<td width="100">
											<p>
												<?
												echo $value[brand_sup_ref][$key_trim1];
												//echo $row[csf('brand_sup_ref')];
												?>
												&nbsp;</p>
										</td>
										<td width="60" align="center">
											<p>
												<?

												if ($value[apvl_req][$key_trim1] == 1) echo "Yes";
												else echo "&nbsp;";
												?>
												&nbsp;
											</p>
										</td>
										<td width="80" align="center">
											<?
											if ($value[apvl_req][$key_trim1] == 1) {
												$app_status = $app_status_arr[$value[job_no]][$value[trim_group_dtls][$key_trim1]];
												$approved_status = $approval_status[$app_status];
												$summary_array[item_app][$key_trim][all] += 1;
												if ($app_status == 3) {
													$summary_array[item_app][$key_trim][app] += 1;
												}
											} else {
												$approved_status = "";
											}
											echo $approved_status;
											?>
										</td>
										<td width="100" align="right">
											<p>
												<?
												$insert_date = explode(" ", $value[insert_date][$key_trim1]);
												echo change_date_format($insert_date[0], '', '', ''); //echo change_date_format($row[csf('pre_date')],'','',1);
												?></p>
										</td>
										<td width="100" align="right">
											<p>
												<a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $value[po_id]; ?>', '<? echo $value[buyer_name]; ?>','<? echo $value[rate][$key_trim1]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>' ,'<? echo $value[booking_no][$key_trim1]; ?>','<? echo $value[description][$key_trim1]; ?>','<? echo $value[country_id][$key_trim1]; ?>','<? echo $value[trim_dtla_id][$key_trim1]; ?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
													<?
													$req_qty = fn_number_format($value[req_qnty][$key_trim1], 2, '.', '');
													echo $req_qty;
													$summary_array[req_qnty][$key_trim] += $value[req_qnty][$key_trim1];
													?>
												</a>
											</p>
										</td>
										<td width="100" align="right">
											<p>
												<?
												echo fn_number_format($value[req_value][$key_trim1], 2);
												$total_pre_costing_value += $value[req_value][$key_trim1];
												?>

											</p>
										</td>
										<?
										// $conversion_factor_rate=$conversion_factor_array[$row[csf('trim_group')]]['con_factor'];
										$wo_qnty = fn_number_format($value[wo_qnty][$key_trim1], 2, '.', '');
										if ($wo_qnty > $req_qty) {
											$color_wo = "red";
										} else if ($wo_qnty < $req_qty) {
											$color_wo = "yellow";
										} else {
											$color_wo = "";
										}

										$supplier_name_string = "";
										$supplier_id_arr = array_unique(explode(',', $value['supplier_id'][$key_trim1]));
										//print_r($supplier_id_arr);
										foreach ($supplier_id_arr as $supplier_id_arr_key => $supplier_id_arr_value) {
											$ex_sup_data = explode("**", $supplier_id_arr_value);
											if ($ex_sup_data[1] == 3 || $ex_sup_data[1] == 5) $suplier_name_arr = $company_library;
											else $suplier_name_arr = $lib_supplier_arr;
											$supplier_name_string .= $suplier_name_arr[$ex_sup_data[0]] . ",";
										}

										$booking_no_arr = array_unique(explode(',', $value[booking_no][$key_trim1]));
										//$booking_no_arr_d=implode(',',$booking_no_arr);
										//print $order_id.'='.	$trim_id;// $wo_qty_array[$order_id][$trim_id]['booking_no'];
										$main_booking_no_large_data = "";
										foreach ($booking_no_arr as $booking_no1) {
											//if($booking_no1>0)
											//{
											if ($main_booking_no_large_data == "") $main_booking_no_large_data = $booking_no1;
											else $main_booking_no_large_data .= "," . $booking_no1;
											//}
											//print($main_booking_no_large_data);
										}
										?>
										<td width="90" align="right" title="<? echo 'conversion_factor=' . $value[conversion_factor_rate][$key_trim1]; ?>" bgcolor="<? echo $color_wo; ?>">
											<p><a href='#report_details' onclick="openmypage('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','<? echo $value[job_no]; ?>','<? echo $main_booking_no_large_data; ?>','<? echo $value[trim_dtla_id][$key_trim1]; ?>','booking_info');">
													<?
													//$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]['wo_qnty']
													echo fn_number_format($value[wo_qnty][$key_trim1], 2, '.', '');
													$summary_array[wo_qnty][$key_trim] += $value[wo_qnty][$key_trim1];
													?>
												</a></p>
										</td>
										<td width="60" align="center">
											<p>
												<?
												echo $unit_of_measurement[$value[cons_uom][$key_trim1]];
												$summary_array[cons_uom][$key_trim] = $value[cons_uom][$key_trim1];
												?></p>
										</td>
										<td width="100" align="right" title="<? echo fn_number_format($value[rate][$key_trim1], 2, '.', ''); ?>">
											<p>
												<?
												echo fn_number_format($value[amount][$key_trim1], 2, '.', '');
												$total_wo_value += $value[amount][$key_trim1];
												?>

											</p>
										</td>

										<td width="150" align="left">
											<p>
												<? echo rtrim($supplier_name_string, ","); ?>
											</p>
										</td>

										<td width="70" align="right" title="<? echo change_date_format($value[wo_date][$key_trim1]); ?>">
											<p>
												<?

												$tot = change_date_format($insert_date[0]);
												if ($value[wo_qnty][$key_trim1] <= 0) {
													$daysOnHand = datediff('d', $tot, $today);
												} else {
													$wo_date = $value[wo_date][$key_trim1];
													$wo_date = change_date_format($wo_date);
													$daysOnHand = datediff('d', $tot, $wo_date);;
												}
												echo $daysOnHand;
												?>&nbsp;</p>
										</td>
										<?

										$inhouse_qnty = $value[inhouse_qnty][$key_trim] + $value[transfer_in][$key_trim] - $value[receive_rtn_qty][$key_trim];
										$inhouse_rate = $value[inhouse_rate][$key_trim];
										$inhouse_value = $inhouse_qnty * $inhouse_rate;
										$balance = $value[wo_qnty_trim_group][$key_trim] - $inhouse_qnty;
										$issue_qnty = $value[issue_qty][$key_trim];
										$issue_ret_qnty = $issue_qty_data_arr[$key][issue_ret_qty][$key_trim];
										$conv_rate = $conversion_factor_array[$value[trim_group_dtls][$key_trim1]]['con_factor'];
										$tot_issue = ((($issue_qnty + $transfer_out_data[$value[po_id]][$key_trim]) - $issue_ret_qnty) / $conv_rate);
										// $left_overqty=$inhouse_qnty-($issue_qnty-$issue_ret_qnty);
										//$left_overqty=$inhouse_qnty-$tot_issue;
										$left_overqty = $inhouse_qnty - $issue_qnty;
										if (!is_null(trim($style_colory))) {
											$summary_array[inhouse_qnty][$key_trim] += $inhouse_qnty;
											$summary_array[inhouse_value][$key_trim] += $inhouse_value;
											$summary_array[inhouse_qnty_bl][$key_trim] += $balance;
											$summary_array[issue_qty][$key_trim] += $issue_qnty - $issue_ret_qnty;
											$summary_array[left_overqty][$key_trim] += $left_overqty;
										}
										?>

										<td width="90" align="right" style=" color: <? echo $style_colory ?>" title="<? echo "Inhouse-Qty: " . $value[inhouse_qnty][$key_trim] . "\nReturn Qty: " . $value[receive_rtn_qty][$key_trim]; ?>"><a style=" color: <? echo $style_colory ?>" href='#report_details' onclick="openmypage_inhouse('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_inhouse_info');"><? echo fn_number_format($inhouse_qnty, 2, '.', ''); ?></a></td>
										<td width="90" align="right">
											<a href='#report_details' onclick="openmypage_inhouse('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_inhouse_value_info');">
												<? echo fn_number_format($inhouse_value, 2, '.', ''); ?>
											</a>
										</td>
										<td width="90" align="right" style=" color: <? echo $style_colory ?>"><? echo fn_number_format($balance, 2, '.', ''); ?></td>
										<td width="90" align="right" title="<? echo "Issue-Qty: " . $issue_qnty . "\nReturn Qty: " . $issue_ret_qnty; ?>" style=" color: <? echo $style_colory ?>"><a style=" color: <? echo $style_colory ?>" href='#report_details' onclick="openmypage_issue('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_issue_info');"><? echo fn_number_format($issue_qnty - $issue_ret_qnty, 2, '.', ''); ?></a></td>
										<td align="right" style=" color: <? echo $style_colory ?>" width="70"><? echo fn_number_format($left_overqty, 2, '.', ''); ?></td>
									</tr>
						<?
								} // end  foreach($value[$key_trim] as $key_trim1=>$value_trim1)
							}
							$i++;
						}
						$po_data_arr = array();
						?>

					</table>
				</div>
				<table class="rpt_table" width="2500" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<th width="30"></th>
						<th width="50"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="90"></th>
						<th width="80" align="right" id="total_order_qnty"><? //echo fn_number_format($total_order_qnty,0); 
																			?></th>
						<th width="50"></th>
						<th width="80" align="right" id="total_order_qnty_in_pcs"><? //echo fn_number_format($total_order_qnty_in_pcs,0); 
																					?></th>
						<th width="80"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="60"></th>
						<th width="80"></th>
						<th width="100"></th>
						<th width="100" align="right" id="value_req_qnty"><? //echo fn_number_format($total_req_qnty,2); 
																			?></th>
						<th width="100" align="right" id="value_pre_costing"><? //echo fn_number_format($total_pre_costing_value,2); 
																				?></th>
						<th width="90" align="right" id=""><? //echo fn_number_format($total_wo_qnty,2); 
															?></th>
						<th width="60" align="right"></th>
						<th width="100" align="right" id="value_wo_qty"><? //echo fn_number_format($total_wo_value,2); 
																		?></th>
						<th width="150" align="right" id=""></th>
						<th width="70" align="right">
							<p><? //echo fn_number_format($req_value,2,'.',''); 
								?>&nbsp;</p>
						</th>
						<th width="90" align="right" id="value_in_qty"><? //echo fn_number_format($total_in_qnty,2); 
																		?></th>
						<th width="90" align="right" id="value_in_val"><? //echo fn_number_format($total_in_qnty,2); 
																		?></th>

						<th width="90" align="right" id="value_rec_qty"><? //echo fn_number_format($total_rec_bal_qnty,2); 
																		?></th>
						<th width="90" align="right" id="value_issue_qty"><? //echo fn_number_format($total_issue_qnty,2); 
																			?></th>
						<th width="70" align="right" id="value_leftover_qty"><? //echo fn_number_format($total_leftover_qnty,2); 
																				?></th>
					</tfoot>
				</table>
				<table>
					<tr>
						<td height="17"></td>
					</tr>
				</table>
				<u><b>Summary</b></u>
				<table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="110">Item</th>
						<th width="60">UOM</th>
						<th width="80">Approved %</th>
						<th width="110">Req Qty</th>
						<th width="110">WO Qty</th>
						<th width="80">WO %</th>
						<th width="110">In-House Qty</th>
						<th width="80">In-House %</th>
						<th width="110">In-House Balance Qty</th>
						<th width="110">Issue Qty</th>
						<th width="80">Issue %</th>
						<th>Left Over</th>
					</thead>
					<?
					$z = 1;
					$tot_req_qnty_summary = 0;
					foreach ($summary_array[trim_group] as $key_trim => $value) {
						if ($z % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
						//$tot_req_qnty_summary+=$value['req'];
						//$tot_wo_qnty_summary+=$value['wo'];
						//$tot_in_qnty_summary+=$value['in'];
						//$tot_issue_qnty_summary+=$value['issue'];
						//$tot_leftover_qnty_summary+=$value['leftover'];
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr2_<? echo $z; ?>','<? echo $bgcolor; ?>')" id="tr2_<? echo $z; ?>">
							<td width="30"><? echo $z; ?></td>
							<td width="110">
								<p><? echo $item_library[$key_trim]; ?></p>
							</td>
							<td width="60" align="center">
								<?
								echo $unit_of_measurement[$summary_array[cons_uom][$key_trim]];
								?></td>
							<td width="80" align="right"><? $app_perc = ($summary_array[item_app][$key_trim][app] * 100) / $summary_array[item_app][$key_trim][all];
															echo fn_number_format($app_perc, 2); ?></td>
							<td width="110" align="right"><? echo fn_number_format($summary_array[req_qnty][$key_trim], 2); ?></td>
							<td width="110" align="right"><? echo fn_number_format($summary_array[wo_qnty][$key_trim], 2); ?></td>
							<td width="80" align="right"><? $wo_per = $summary_array[wo_qnty][$key_trim] / $summary_array[req_qnty][$key_trim] * 100;
															echo fn_number_format($wo_per, 2) . '%'; ?></td>
							<td width="110" align="right"><? echo fn_number_format($summary_array[inhouse_qnty][$key_trim], 2); ?></td>
							<td width="80" align="right"><? $in_per = $summary_array[inhouse_qnty][$key_trim] / $summary_array[wo_qnty][$key_trim] * 100;
															echo fn_number_format($in_per, 2) . '%'; ?></td>
							<td width="110" align="right"><? echo fn_number_format($summary_array[inhouse_qnty_bl][$key_trim], 2);
															$in_house_bal += ($value['wo'] - $value['in']); ?></td>
							<td width="110" align="right"><? echo fn_number_format($summary_array[issue_qty][$key_trim], 2); ?></td>
							<td width="80" align="right"><? $wo_per = $summary_array[issue_qty][$key_trim] / $summary_array[wo_qnty][$key_trim] * 100;
															echo fn_number_format($wo_per, 2) . '%'; ?></td>
							<td align="right"><? echo fn_number_format($summary_array[left_overqty][$key_trim], 2); ?></td>
						</tr>
					<?
						$z++;
					}
					$summary_array = array();
					?>
					<tfoot>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_req_qnty_summary,2); 
											?>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_wo_qnty_summary,2); 
											?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_in_qnty_summary,2); 
											?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($in_house_bal,2); 
											?>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_issue_qnty_summary,2); 
											?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_leftover_qnty_summary,2); 
											?>&nbsp;</th>
					</tfoot>
				</table>
			</fieldset>
		</div>
	<?
	}

	//===========================================================================================================================================================

	if (str_replace("'", "", $cbo_search_by) == 2) {
		//echo "test";die;
		ob_start();
	?>
		<div style="width:2400px">
			<fieldset style="width:100%;">
				<table width="2400">
					<tr class="form_caption">
						<td colspan="26" align="center">Accessories Followup Report</td>
					</tr>
					<tr class="form_caption">
						<td colspan="26" align="center"><? echo $company_library[$company_name]; ?></td>
					</tr>
				</table>
				<table class="rpt_table" width="2360" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="50">Buyer</th>
						<th width="100">Job No</th>
						<th width="100">Style Ref</th>
						<th width="100">Internal Ref</th>
						<th width="100">File No</th>
						<th width="90">Order No</th>
						<th width="80">Order Qty</th>
						<th width="50">UOM</th>
						<th width="80">Qnty (Pcs)</th>
						<th width="80">Shipment Date</th>
						<th width="100">Trims Name</th>
						<th width="100">Brand/Sup Ref</th>
						<th width="60">Appr Req.</th>
						<th width="80">Approve Status</th>
						<th width="100">Item Entry Date</th>
						<th width="100">Req Qnty</th>
						<th width="100">Pre Costing Value</th>
						<th width="90">WO Qnty</th>
						<th width="60">Trims UOM</th>
						<th width="100">WO Value</th>
						<th width="150">Supplier</th>
						<th width="70">WO Delay Days</th>
						<th width="90">In-House Qnty</th>
						<th width="90">Receive Balance</th>
						<th width="90">Issue to Prod.</th>
						<th>Left Over/Balance</th>
					</thead>
				</table>
				<div style="width:2380px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="2360" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<?

						$conversion_factor_array = array();
						//echo "select id ,trim_uom,conversion_factor from  lib_item_group  ";die;
						$conversion_factor = sql_select("select id ,trim_uom,conversion_factor from  lib_item_group  ");
						foreach ($conversion_factor as $row_f) {
							$conversion_factor_array[$row_f[csf('id')]]['con_factor'] = $row_f[csf('conversion_factor')];
							$conversion_factor_array[$row_f[csf('id')]]['cons_uom'] = $row_f[csf('trim_uom')];
						}
						unset($conversion_factor);
						
						
						//echo "3701";
						$condition = new condition();
						if (str_replace("'", "", $txt_job_no) != '') {
							$condition->job_no_prefix_num("=$txt_job_no");
						}
						if (str_replace("'", "", $txt_order_no) != '') {
							//$condition->po_number("=$txt_order_no"); 
							$order_nos = str_replace("'", "", $txt_order_no);
							$condition->po_number(" like '%$order_nos%'");
						}

						if (str_replace("'", "", $txt_date_from) != "" && str_replace("'", "", $txt_date_to) != "") {
							$start_date = (str_replace("'", "", $txt_date_from));
							$end_date = (str_replace("'", "", $txt_date_to));
						}

						if (str_replace("'", "", $txt_date_from) != "" && str_replace("'", "", $txt_date_to) != "") {
							if ($date_type == 1) {
								$condition->country_ship_date(" between '$start_date' and '$end_date'");
							}
							if ($date_type == 2) {
								$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
							}
						}
						//echo "3730";
						$condition->init();
						$trim = new trims($condition);
						//$trim_qty=$trim->getQtyArray_by_orderAndPrecostdtlsid();
						//echo $trim->getQuery();die;
						//echo "3734";
						$trim_qty = $trim->getQtyArray_by_jobAndPrecostdtlsid();
						$trim_amount = $trim->getAmountArray_by_jobAndPrecostdtlsid();
						//print_r($trim_amount); die;
						//echo "3739";
						$style_data_arr = array();
						$po_id_string = "";
						$today = date("Y-m-d");

						$sql_query = sql_select("SELECT a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, sum(c.order_quantity) as order_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, a.total_set_qnty, b.pub_shipment_date
					from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
					where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond " . set_user_lavel_filtering(' and a.buyer_name', 'buyer_id') . " $date_cond $style_ref_cond $year_cond $jobcond  $ordercond $file_no_cond $internal_ref_cond
					group by a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, a.total_set_qnty, b.pub_shipment_date
					order by b.id");

					$tot_rows = count($sql_query);
					$i = 1; $poididarr=array();
					foreach ($sql_query as $row) {
						$dzn_qnty = 0;
						if ($row[csf('costing_per')] == 1) $dzn_qnty = 12;
						else if ($row[csf('costing_per')] == 3) $dzn_qnty = 12 * 2;
						else if ($row[csf('costing_per')] == 4) $dzn_qnty = 12 * 3;
						else if ($row[csf('costing_per')] == 5) $dzn_qnty = 12 * 4;
						else $dzn_qnty = 1;
						$poididarr[$row[csf('id')]] = $row[csf('id')];
						$po_qty = 0; $req_qnty = 0; $req_value = 0;

						$style_data_arr[$row[csf('job_no')]][job_no] = $row[csf('job_no')];
						$style_data_arr[$row[csf('job_no')]][buyer_name] = $row[csf('buyer_name')];
						$style_data_arr[$row[csf('job_no')]][job_no_prefix_num] = $row[csf('job_no_prefix_num')];
						$style_data_arr[$row[csf('job_no')]][style_ref_no] = $row[csf('style_ref_no')];
						$style_data_arr[$row[csf('job_no')]][grouping] = $row[csf('grouping')];
						$style_data_arr[$row[csf('job_no')]][file_no] = $row[csf('file_no')];

						$style_data_arr[$row[csf('job_no')]][order_uom] = $row[csf('order_uom')];
						$style_data_arr[$row[csf('job_no')]][po_id][$row[csf('id')]] = $row[csf('id')];
						$style_data_arr[$row[csf('job_no')]][po_number][$row[csf('id')]] = $row[csf('po_number')];
						$style_data_arr[$row[csf('job_no')]][order_quantity_set][$row[csf('id')]] = $row[csf('order_quantity_set')];
						$style_data_arr[$row[csf('job_no')]][order_quantity][$row[csf('id')]] = $row[csf('order_quantity')];
						$style_data_arr[$row[csf('job_no')]][pub_shipment_date][$row[csf('id')]] = change_date_format($row[csf('pub_shipment_date')]);
						$po_id_string .= $row[csf('id')] . ",";
					}

					$con = connect();
					execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=5 and ENTRY_FORM=31");
					oci_commit($con);
					fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 31, 5, $poididarr, $empty_arr);//PO ID
					disconnect($con);
					
					$app_sql=sql_select("select a.job_no_mst, a.accessories_type_id, a.approval_status from wo_po_trims_approval_info a, gbl_temp_engine d where a.is_deleted=0 and a.status_active=1 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=5");
					$app_status_arr = array();
					foreach ($app_sql as $row) {
						$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]] = $row[csf("approval_status")];
					}
					unset($app_sql);
					
					$sql_po_qty_country_wise_arr = array(); $po_job_arr = array(); $style_po_qty_arr = array();

					$sql_po_qty_country_wise = sql_select("select  b.id, b.job_no_mst, c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, gbl_temp_engine d where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and b.id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=5 and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $year_cond $file_no_cond $internal_ref_cond group by b.id, b.job_no_mst, c.country_id order by b.id, b.job_no_mst, c.country_id");
					foreach ($sql_po_qty_country_wise as $sql_po_qty_country_wise_row) {
						$sql_po_qty_country_wise_arr[$sql_po_qty_country_wise_row[csf('id')]][$sql_po_qty_country_wise_row[csf('country_id')]] = $sql_po_qty_country_wise_row[csf('order_quantity_set')];
						$po_job_arr[$sql_po_qty_country_wise_row[csf('id')]] = $sql_po_qty_country_wise_row[csf('job_no_mst')];
						$style_po_qty_arr[$sql_po_qty_country_wise_row[csf('job_no_mst')]]['order_qty_set'] += $sql_po_qty_country_wise_row[csf('order_quantity_set')];
						$style_po_qty_arr[$sql_po_qty_country_wise_row[csf('job_no_mst')]]['po_qty'] += $sql_po_qty_country_wise_row[csf('order_quantity')];
					}
					//print_r($style_po_qty_arr);
					unset($sql_po_qty_country_wise);


					$sql_trim = "SELECT  d.costing_per, e.id as trim_dtla_id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate as rate_item, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, f.rate, f.cons, f.country_id, f.po_break_down_id as id, f.cons as cons_cal, d.job_no
					from wo_pre_cost_trim_co_cons_dtls f, wo_pre_cost_trim_cost_dtls e, 
					wo_pre_cost_mst d, gbl_temp_engine t
					where f.job_id=e.job_id and e.id=f.wo_pre_cost_trim_cost_dtls_id $item_group_cond and f.cons > 0 and e.job_id =d.job_id and f.is_deleted=0 and f.status_active=1 and e.is_deleted=0 and e.status_active=1 and d.is_deleted=0 and d.status_active=1 and t.user_id = " . $user_id . " and t.ref_from=5 and t.entry_form=31 and t.ref_val=f.po_break_down_id
					group by  d.costing_per, e.id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, f.rate, f.cons,f.country_id,f.po_break_down_id,d.job_no
					order by f.po_break_down_id, e.trim_group";
					//echo $sql_trim;
					$res_trim = sql_select($sql_trim);

					foreach ($res_trim as $row) {
						$poididarr[$row[csf('id')]] = $row[csf('id')];
						if ($row[csf('country_id')] == 0) {
							$po_qty = $po_arr[$row[csf('job_no')]]['order_quantity'];
							//$po_qty=$po_arr[$rowp[csf('job_no')]]['order_quantity'];
							//$req_qnty+=$trim_qty[$rowp[csf('po_break_down_id')]][$rowp[csf('country_id')]][$rowp[csf('trim_dtla_id')]];
						} else {
							$country_id = explode(",", $row[csf('country_id')]);
							for ($cou = 0; $cou <= count($country_id); $cou++) {
								$po_qty += $sql_po_qty_country_wise_arr[$row[csf('po_break_down_id')]][$country_id[$cou]];
								//$po_qty+=$sql_po_qty_country_wise_arr[$rowp[csf('po_break_down_id')]][$country_id[$cou]];
								//$req_qnty+=$trim_qty[$rowp[csf('po_break_down_id')]][$country_id[$cou]][$rowp[csf('trim_dtla_id')]];
							}
						}
						$req_qnty = $trim_qty[$row[csf('job_no')]][$row[csf('trim_dtla_id')]];
						$req_value = $trim_amount[$row[csf('job_no')]][$row[csf('trim_dtla_id')]];

						$style_data_arr[$row[csf('job_no')]][trim_dtla_id][$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')]; // for rowspan
						$style_data_arr[$row[csf('job_no')]][trim_group][$row[csf('trim_group')]] = $row[csf('trim_group')];
						$style_data_arr[$row[csf('job_no')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')]; // for rowspannn
						$style_data_arr[$row[csf('job_no')]][trim_group_dtls][$row[csf('trim_dtla_id')]] = $row[csf('trim_group')];

						$style_data_arr[$row[csf('job_no')]][brand_sup_ref][$row[csf('trim_dtla_id')]] = $row[csf('brand_sup_ref')];
						$style_data_arr[$row[csf('job_no')]][apvl_req][$row[csf('trim_dtla_id')]] = $row[csf('apvl_req')];
						$style_data_arr[$row[csf('job_no')]][insert_date][$row[csf('trim_dtla_id')]] = $row[csf('insert_date')];
						$style_data_arr[$row[csf('job_no')]][req_qnty][$row[csf('trim_dtla_id')]] = $req_qnty;
						$style_data_arr[$row[csf('job_no')]][req_value][$row[csf('trim_dtla_id')]] = $req_value;
						$style_data_arr[$row[csf('job_no')]][cons_uom][$row[csf('trim_dtla_id')]] = $row[csf('cons_uom')];

						$style_data_arr[$row[csf('job_no')]][trim_group_from][$row[csf('trim_dtla_id')]] = "Pre_cost";
						$style_data_arr[$row[csf('job_no')]][rate][$row[csf('trim_dtla_id')]] = $row[csf('rate')];
						$style_data_arr[$row[csf('job_no')]][description][$row[csf('trim_dtla_id')]] = $row[csf('description')];
						$style_data_arr[$row[csf('job_no')]][country_id][$row[csf('trim_dtla_id')]] .= $row[csf('country_id')] . ",";
					}

					unset($sql_query);
					$po_id_string = rtrim($po_id_string, ",");
					if ($po_id_string == "") {
						echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
						die;
					}
						
					//echo "select min(a.booking_date) as booking_date ,b.job_no,LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name and b.po_break_down_id in($po_id_string) group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id";
					$wo_sql_without_precost = sql_select("SELECT min(a.booking_date) as booking_date ,b.job_no,LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no,LISTAGG(CAST( a.supplier_id || '**' || a.pay_mode AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b,gbl_temp_engine t where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name  and t.user_id = " . $user_id . " and t.ref_from=5 and t.entry_form=31 and t.ref_val=b.po_break_down_id group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id"); //and item_from_precost=2
						
						foreach ($wo_sql_without_precost as $wo_row_without_precost)
						{
							$conversion_factor_rate = $conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['con_factor'];
							$cons_uom = $conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['cons_uom'];
							$booking_no = $wo_row_without_precost[csf('booking_no')];
							$supplier_id = $wo_row_without_precost[csf('supplier_id')];
							$wo_qnty = $wo_row_without_precost[csf('wo_qnty')] * $conversion_factor_rate;
							$amount = $wo_row_without_precost[csf('amount')];
							$wo_date = $wo_row_without_precost[csf('booking_date')];

							if ($wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] == "" || $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] == 0) {
								$trim_dtla_id = max($style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_dtla_id]) + 1;
								$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_dtla_id][$trim_dtla_id] = $trim_dtla_id; // for rowspan
								$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group][$wo_row_without_precost[csf('trim_group')]] = $wo_row_without_precost[csf('trim_group')];
								$style_data_arr[$wo_row_without_precost[csf('job_no')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id] = $trim_dtla_id; // for rowspannn
								$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group_dtls][$trim_dtla_id] = $wo_row_without_precost[csf('trim_group')];
								$style_data_arr[$wo_row_without_precost[csf('job_no')]][cons_uom][$trim_dtla_id] = $cons_uom;

								$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group_from][$trim_dtla_id] = "Booking Without Pre_cost";
							} else {
								$trim_dtla_id = $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
							}
							$style_data_arr[$wo_row_without_precost[csf('job_no')]][wo_qnty][$trim_dtla_id] += $wo_qnty;
							$style_data_arr[$wo_row_without_precost[csf('job_no')]][amount][$trim_dtla_id] += $amount;
							$style_data_arr[$wo_row_without_precost[csf('job_no')]][wo_date][$trim_dtla_id] = $wo_date;
							$style_data_arr[$wo_row_without_precost[csf('job_no')]][wo_qnty_trim_group][$wo_row_without_precost[csf('trim_group')]] += $wo_qnty;

							$style_data_arr[$wo_row_without_precost[csf('job_no')]][booking_no][$trim_dtla_id] .= $booking_no . ",";
							$style_data_arr[$wo_row_without_precost[csf('job_no')]][supplier_id][$trim_dtla_id] .= $supplier_id . ",";
							$style_data_arr[$wo_row_without_precost[csf('job_no')]][conversion_factor_rate][$trim_dtla_id] = $conversion_factor_rate;
						}
						unset($wo_sql_without_precost);

						$receive_qty_data = sql_select("SELECT b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and t.user_id = " . $user_id . " and t.ref_from=5 and t.entry_form=31 and t.ref_val=b.po_breakdown_id group by b.po_breakdown_id, a.item_group_id order by a.item_group_id ");

						foreach ($receive_qty_data as $row) {
							if ($style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group][$row[csf('item_group_id')]] == "" || $style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group][$row[csf('item_group_id')]] == 0) {
								$cons_uom = $conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];
								$trim_dtla_id = max($style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_dtla_id]) + 1;
								$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_dtla_id][$trim_dtla_id] = $trim_dtla_id; // for rowspan
								$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group][$row[csf('item_group_id')]] = $row[csf('item_group_id')];
								$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][$row[csf('item_group_id')]][$trim_dtla_id] = $trim_dtla_id; // for rowspannn
								$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group_dtls][$trim_dtla_id] = $row[csf('item_group_id')];
								$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][cons_uom][$trim_dtla_id] = $cons_uom;

								$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group_from][$trim_dtla_id] = "Trim Receive";
							}
							$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][inhouse_qnty][$row[csf('item_group_id')]] += $row[csf('quantity')];
						}
						unset($receive_qty_data);

						$receive_rtn_qty_data = sql_select("SELECT d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e,gbl_temp_engine t  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and t.user_id = " . $user_id . " and t.ref_from=5 and t.entry_form=31 and t.ref_val=d.po_breakdown_id  group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");
						foreach ($receive_rtn_qty_data as $row) {
							//$style_data_arr[$row[csf('po_breakdown_id')]][receive_rtn_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
							$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][receive_rtn_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
						}
						unset($receive_rtn_qty_data);

						$issue_qty_data = sql_select("SELECT b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b, gbl_temp_engine t where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and t.user_id = " . $user_id . " and t.ref_from=5 and t.entry_form=31 and t.ref_val=b.po_breakdown_id  group by b.po_breakdown_id, a.item_group_id");
						foreach ($issue_qty_data as $row) {
							$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][issue_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
						}
						unset($issue_qty_data);
						
						$issue_qty_data_arr = array();
						$sql_issue_ret = ("SELECT c.po_breakdown_id as po_id, p.item_group_id, SUM(c.quantity) as quantity
					from inv_transaction b, order_wise_pro_details c, product_details_master p, gbl_temp_engine t
					where c.entry_form=73 and p.id=b.prod_id and b.id=c.trans_id and b.transaction_type=4 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and t.user_id = " . $user_id . " and t.ref_from=5 and t.entry_form=31 and t.ref_val=c.po_breakdown_id group by c.po_breakdown_id,p.item_group_id");
						$issue_result = sql_select($sql_issue_ret);
						foreach ($issue_result as $row) {
							$issue_qty_data_arr[$row[csf('po_id')]][issue_ret_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
						}
						unset($issue_result);
						
						$con = connect();
						execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=5 and ENTRY_FORM=31");
						oci_commit($con);
						disconnect($con);

						$total_pre_costing_value = 0; $total_wo_value = 0; $summary_array = array();
						$i = 1; $x = 0;
						
						foreach ($style_data_arr as $key => $value) {
							$z = 1;
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							foreach ($value[trim_group] as $key_trim => $value_trim) {
								$y = 1;
								$summary_array[trim_group][$key_trim] = $key_trim;
								foreach ($value[$key_trim] as $key_trim1 => $value_trim1) {
									if ($z == 1) $style_color = ''; else $style_color = $bgcolor . "; border: none";

									$z++;

									if ($y == 1) $style_colory = ''; else $style_colory = $bgcolor . "; border: none";
									$x++;
									$y++;
						?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $x; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $x; ?>">
										<td width="30" style="word-break: break-all;color: <? echo $style_color ?>" title="<? echo $po_qty; ?>"><? echo $i; ?></td>
										<td width="50" style="word-break: break-all;color: <? echo $style_color ?>"><? echo $buyer_short_name_library[$value[buyer_name]]; ?></td>
										<td width="100" style="word-break: break-all;color: <? echo $style_color ?>" align="center"><? echo $value[job_no_prefix_num]; ?></td>
										<td width="100" style="word-break: break-all;color: <? echo $style_color ?>"><? echo $value[style_ref_no]; ?></td>
										<td width="100" style="word-break: break-all;color: <? echo $style_color ?>"><? echo $value[grouping]; ?></td>
										<td width="100" style="word-break: break-all;color: <? echo $style_color ?>"><? echo $value[file_no]; ?></td>
										<td width="90" style="word-break: break-all;color: <? echo $style_color ?>">
											<a style="word-break: break-all;color: <? echo $style_color ?>" href='#report_details' onclick="generate_report('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','preCostRpt');">
												<?
												$po_number = implode(",", $value[po_number]);
												$po_id = implode(",", $value[po_id]);
												echo $po_number;
												?>
											</a>
										</td>
										<td width="80" style="word-break: break-all;color: <? echo $style_color ?>" align="right">
											<a style="word-break: break-all;color: <? echo $style_color ?>" href='#report_details' onclick="order_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $po_id; ?>', '<? echo $value[buyer_name]; ?>' ,'order_qty_data');"><? echo fn_number_format(array_sum($value[order_quantity_set]), 0, '.', ''); ?>
											</a>
										</td>
										<td width="50" align="center" style="word-break: break-all;color: <? echo $style_color ?>"><? echo $unit_of_measurement[$value[order_uom]]; ?></td>
										<td width="80" align="right" style="word-break: break-all;color: <? echo $style_color ?>"><? echo fn_number_format(array_sum($value[order_quantity]), 0, '.', ''); ?></td>
										<td width="80" align="center" style="word-break: break-all;color: <? echo $style_color ?>">
											<?
											$pub_shipment_date = implode(",", $value[pub_shipment_date]);
											echo $pub_shipment_date;
											?>
										</td>
										<td width="100" title="<? echo $value[trim_group_from][$key_trim1];  ?>">
											<p>
												<?
												echo $item_library[$value[trim_group_dtls][$key_trim1]];
												//echo $value[trim_group_dtls][$key_trim1];
												?>
												&nbsp;</p>
										</td>
										<td width="100">
											<p>
												<?
												echo $value[brand_sup_ref][$key_trim1];
												//echo $row[csf('brand_sup_ref')];
												?>
												&nbsp;</p>
										</td>
										<td width="60" align="center">
											<p>
												<?

												if ($value[apvl_req][$key_trim1] == 1) echo "Yes";
												else echo "&nbsp;";
												?>
												&nbsp;
											</p>
										</td>
										<td width="80" align="center">
											<p>
												<?
												if ($value[apvl_req][$key_trim1] == 1) {
													$app_status = $app_status_arr[$value[job_no]][$value[trim_group_dtls][$key_trim1]];
													$approved_status = $approval_status[$app_status];
													$summary_array[item_app][$key_trim][all] += 1;
													if ($app_status == 3) {
														$summary_array[item_app][$key_trim][app] += 1;
													}
												} else {
													$approved_status = "";
												}
												echo $approved_status;
												?>
												&nbsp;
											</p>
										</td>
										<td width="100" align="right">
											<p>
												<?
												$insert_date = explode(" ", $value[insert_date][$key_trim1]);
												echo change_date_format($insert_date[0], '', '', ''); //echo change_date_format($row[csf('pre_date')],'','',1);
												?>&nbsp;</p>
										</td>
										<td width="100" align="right">
											<p>
												<a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $po_id; ?>', '<? echo $value[buyer_name]; ?>','<? echo $value[rate][$key_trim1]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>' ,'<? echo $value[booking_no][$key_trim1]; ?>','<? echo $value[description][$key_trim1]; ?>','<? echo rtrim($value[country_id][$key_trim1], ","); ?>','<? echo $value[trim_dtla_id][$key_trim1]; ?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
													<?
													$req_qty = fn_number_format($value[req_qnty][$key_trim1], 2, '.', '');
													echo $req_qty;
													$summary_array[req_qnty][$key_trim] += $value[req_qnty][$key_trim1];
													?>
												</a>
												&nbsp;
											</p>
										</td>
										<td width="100" align="right">
											<p><? echo fn_number_format($value[req_value][$key_trim1], 2);
												$total_pre_costing_value += $value[req_value][$key_trim1]; ?>&nbsp;</p>
										</td>
										<?
										// $conversion_factor_rate=$conversion_factor_array[$row[csf('trim_group')]]['con_factor'];
										$wo_qnty = fn_number_format($value[wo_qnty][$key_trim1], 2, '.', '');
										if ($wo_qnty > $req_qty) { $color_wo = "red"; } else if ($wo_qnty < $req_qty) { $color_wo = "yellow"; } else { $color_wo = ""; }

										$supplier_name_string = "";
										$supplier_id_arr = array_unique(explode(',', rtrim($value['supplier_id'][$key_trim1], ",")));

										foreach ($supplier_id_arr as $supplier_id_arr_key => $supplier_id_arr_value) {
											$ex_sup_data = explode("**", $supplier_id_arr_value);
											if ($ex_sup_data[1] == 3 || $ex_sup_data[1] == 5) $suplier_name_arr = $company_library;
											else $suplier_name_arr = $lib_supplier_arr;
											$supplier_name_string .= $suplier_name_arr[$ex_sup_data[0]] . ",";
										}
										$booking_no_arr = array_unique(explode(',', rtrim($value[booking_no][$key_trim1], ",")));
										//$booking_no_arr_d=implode(',',$booking_no_arr);
										//print $order_id.'='.	$trim_id;// $wo_qty_array[$order_id][$trim_id]['booking_no'];
										$main_booking_no_large_data = "";
										foreach ($booking_no_arr as $booking_no1) {
											//if($booking_no1>0)
											//{
											if ($main_booking_no_large_data == "") $main_booking_no_large_data = $booking_no1;
											else $main_booking_no_large_data .= "," . $booking_no1;
											//}
											//print($main_booking_no_large_data);
										}
										?>
										<td width="90" align="right" title="<? echo 'conversion_factor=' . $value[conversion_factor_rate][$key_trim1]; ?>" bgcolor="<? echo $color_wo; ?>">
											<p><a href='#report_details' onclick="openmypage('<? echo $po_id; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','<? echo $value[job_no]; ?>','<? echo $main_booking_no_large_data; ?>','<? echo $value[trim_dtla_id][$key_trim1]; ?>','booking_info');">
													<?
													//$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]['wo_qnty']
													echo fn_number_format($value[wo_qnty][$key_trim1], 2, '.', '');
													$summary_array[wo_qnty][$key_trim] += $value[wo_qnty][$key_trim1];
													?>
												</a>&nbsp;</p>
										</td>
										<td width="60" align="center">
											<p>
												<?
												echo $unit_of_measurement[$value[cons_uom][$key_trim1]];
												$summary_array[cons_uom][$key_trim] = $value[cons_uom][$key_trim1];
												?>
												&nbsp;
											</p>
										</td>
										<td width="100" align="right" title="<? echo fn_number_format($value[rate][$key_trim1], 2, '.', ''); ?>">
											<p>
												<?
												echo fn_number_format($value[amount][$key_trim1], 2, '.', '');
												$total_wo_value += $value[amount][$key_trim1];
												?>
												&nbsp;
											</p>
										</td>

										<td width="150" align="left">
											<p>
												<?
												echo rtrim($supplier_name_string, ',');
												?>

											</p>
										</td>
										<td width="70" align="right" title="<? echo change_date_format($value[wo_date][$key_trim1]); ?>">
											<p>
												<?

												$tot = change_date_format($insert_date[0]);
												if ($value[wo_qnty][$key_trim1] <= 0) {
													$daysOnHand = datediff('d', $tot, $today);
												} else {
													$wo_date = $value[wo_date][$key_trim1];
													$wo_date = change_date_format($wo_date);
													$daysOnHand = datediff('d', $tot, $wo_date);;
												}
												echo $daysOnHand;
												?>&nbsp;</p>
										</td>
										<?
										$inhouse_qnty = $value[inhouse_qnty][$key_trim] - $value[receive_rtn_qty][$key_trim];
										$balance = $value[wo_qnty_trim_group][$key_trim] - $inhouse_qnty;
										$issue_qnty = $value[issue_qty][$key_trim];
										$issue_ret_qnty = $issue_qty_data_arr[issue_ret_qty][$key_trim];
										$left_overqty = $inhouse_qnty - ($issue_qnty - $issue_ret_qnty);
										if (!is_null(trim($style_colory))) {
											$summary_array[inhouse_qnty][$key_trim] += $inhouse_qnty;
											$summary_array[inhouse_qnty_bl][$key_trim] += $balance;
											$summary_array[issue_qty][$key_trim] += $issue_qnty - $issue_ret_qnty;
											$summary_array[left_overqty][$key_trim] += $left_overqty;
										}
										?>

										<td width="90" style="word-break: break-all;color: <? echo $style_colory ?>" align="right" title="<? echo "Inhouse-Qty: " . $value[inhouse_qnty][$key_trim] . "\nReturn Qty: " . $value[receive_rtn_qty][$key_trim]; ?>"><a style="word-break: break-all;color: <? echo $style_colory ?>" href='#report_details' onclick="openmypage_inhouse('<? echo $po_id; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_inhouse_info');"><? echo fn_number_format($inhouse_qnty, 2, '.', ''); ?></a></td>
										<td width="90" style="word-break: break-all;color: <? echo $style_colory ?>" align="right"><? echo fn_number_format($balance, 2, '.', ''); ?></td>
										<td width="90" title="<? echo "Issue-Qty: " . $issue_qnty . "\nReturn Qty: " . $issue_ret_qnty; ?>" style="word-break: break-all;color: <? echo $style_colory ?>" align="right"><a style="word-break: break-all;color: <? echo $style_colory ?>" href='#report_details' onclick="openmypage_issue('<? echo $po_id; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_issue_info');"><? echo fn_number_format($issue_qnty - $issue_ret_qnty, 2, '.', ''); ?></a></td>
										<td align="right" style="word-break: break-all;color: <? echo $style_colory ?>"><? echo fn_number_format($left_overqty, 2, '.', ''); ?></td>
									</tr>
									<?
								} // end  foreach($value[$key_trim] as $key_trim1=>$value_trim1)
							}
							$i++;
						}
						?>

					</table>
					<table class="rpt_table" width="2360" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
							<th width="30"></th>
							<th width="50"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="90"></th>
							<th width="80" align="right" id="total_order_qnty"><? //echo fn_number_format($total_order_qnty,0); ?></th>
							<th width="50"></th>
							<th width="80" align="right" id="total_order_qnty_in_pcs"><? //echo fn_number_format($total_order_qnty_in_pcs,0); ?></th>
							<th width="80"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="60"></th>
							<th width="80"></th>
							<th width="100"></th>
							<th width="100" align="right" id="value_req_qnty"><? //echo fn_number_format($total_req_qnty,2); ?></th>
							<th width="100" align="right" id="value_pre_costing"><? echo fn_number_format($total_pre_costing_value, 2); ?></th>
							<th width="90" align="right" id=""><? //echo fn_number_format($total_wo_qnty,2); ?></th>
							<th width="60" align="right"></th>
							<th width="100" align="right" id="value_wo_qty"><? echo fn_number_format($total_wo_value, 2); ?></th>
							<th width="150" align="right" id=""></th>
							<th width="70" align="right"><p><? //echo fn_number_format($req_value,2,'.',''); ?>&nbsp;</p></th>
							<th width="90" align="right" id="value_in_qty"><? //echo fn_number_format($total_in_qnty,2); ?></th>
							<th width="90" align="right" id="value_rec_qty"><? //echo fn_number_format($total_rec_bal_qnty,2); ?></th>
							<th width="90" align="right" id="value_issue_qty"><? //echo fn_number_format($total_issue_qnty,2); ?></th>
							<th align="right" id="value_leftover_qty"><? //echo fn_number_format($total_leftover_qnty,2); ?></th>
						</tfoot>
					</table>
				</div>
				<table>
					<tr>
						<td height="15"></td>
					</tr>
				</table>
				<u><b>Summary</b></u>
				<table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="110">Item</th>
						<th width="60">UOM</th>
						<th width="80">Approved %</th>
						<th width="110">Req Qty</th>
						<th width="110">WO Qty</th>
						<th width="80">WO %</th>
						<th width="110">In-House Qty</th>
						<th width="80">In-House %</th>
						<th width="110">In-House Balance Qty</th>
						<th width="110">Issue Qty</th>
						<th width="80">Issue %</th>
						<th>Left Over</th>
					</thead>
					<?
					$z = 1;
					$tot_req_qnty_summary = 0;
					foreach ($summary_array[trim_group] as $key_trim => $value) {
						if ($z % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						//$tot_req_qnty_summary+=$value['req'];
						//$tot_wo_qnty_summary+=$value['wo'];
						//$tot_in_qnty_summary+=$value['in'];
						//$tot_issue_qnty_summary+=$value['issue'];
						//$tot_leftover_qnty_summary+=$value['leftover'];
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr2_<? echo $z; ?>','<? echo $bgcolor; ?>')" id="tr2_<? echo $z; ?>">
							<td width="30"><? echo $z; ?></td>
							<td width="110"><p><? echo $item_library[$key_trim]; ?></p></td>
							<td width="60" align="center"><? echo $unit_of_measurement[$summary_array[cons_uom][$key_trim]]; ?></td>
							<td width="80" align="right"><? $app_perc = ($summary_array[item_app][$key_trim][app] * 100) / $summary_array[item_app][$key_trim][all];
															echo fn_number_format($app_perc, 2); ?></td>
							<td width="110" align="right"><? echo fn_number_format($summary_array[req_qnty][$key_trim], 2); ?></td>
							<td width="110" align="right"><? echo fn_number_format($summary_array[wo_qnty][$key_trim], 2); ?></td>
							<td width="80" align="right"><? $wo_per = $summary_array[wo_qnty][$key_trim] / $summary_array[req_qnty][$key_trim] * 100;
															echo fn_number_format($wo_per, 2) . '%'; ?></td>
							<td width="110" align="right"><? echo fn_number_format($summary_array[inhouse_qnty][$key_trim], 2); ?></td>
							<td width="80" align="right"><? $in_per = $summary_array[inhouse_qnty][$key_trim] / $summary_array[wo_qnty][$key_trim] * 100;
															echo fn_number_format($in_per, 2) . '%'; ?></td>
							<td width="110" align="right"><? echo fn_number_format($summary_array[inhouse_qnty_bl][$key_trim], 2);
															$in_house_bal += ($value['wo'] - $value['in']); ?></td>
							<td width="110" align="right"><? echo fn_number_format($summary_array[issue_qty][$key_trim], 2); ?></td>
							<td width="80" align="right"><? $wo_per = $summary_array[issue_qty][$key_trim] / $summary_array[wo_qnty][$key_trim] * 100;
															echo fn_number_format($wo_per, 2) . '%'; ?></td>
							<td align="right"><? echo fn_number_format($summary_array[left_overqty][$key_trim], 2); ?></td>
						</tr>
					<?
						$z++;
					}
					?>
					<tfoot>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_req_qnty_summary,2); ?>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_wo_qnty_summary,2); ?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_in_qnty_summary,2); ?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($in_house_bal,2); ?>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_issue_qnty_summary,2); ?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo fn_number_format($tot_leftover_qnty_summary,2); ?>&nbsp;</th>
					</tfoot>
				</table>
			</fieldset>
		</div>
		<?
	}

	foreach (glob("*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$action";
	exit();
}

if ($action == "report_generate3") {
	$buyer_library = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$item_library = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	//$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );

	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$company_name = str_replace("'", "", $cbo_company_name);
	$serch_by = str_replace("'", "", $cbo_search_by);
	$cbo_season_name = str_replace("'", "", $cbo_season_name);

	$buyer_id_cond = "";
	if (str_replace("'", "", $cbo_buyer_name) == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				$buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else $buyer_id_cond = "";
		} else $buyer_id_cond = "";
	} else $buyer_id_cond = " and a.buyer_name=$cbo_buyer_name"; //.str_replace("'","",$cbo_buyer_name)

	//condition add
	if ($db_type == 0) {
		if (str_replace("'", "", $cbo_year) != 0) $year_cond = " and year(a.insert_date)=" . str_replace("'", "", $cbo_year) . "";
		else $year_cond = "";
	} else {
		if (str_replace("'", "", $cbo_year) != 0) $year_cond = " and to_char(a.insert_date,'YYYY')=" . str_replace("'", "", $cbo_year) . "";
		else $year_cond = "";
	}

	if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond = "";
	else $item_group_cond = "and c.trim_group in(" . str_replace("'", "", $cbo_item_group) . ")";

	$date_type = str_replace("'", "", $cbo_date_type);
	$start_date = str_replace("'", "", trim($txt_date_from));
	$end_date = str_replace("'", "", trim($txt_date_to));
	$date_cond = '';
	if ($date_type == 1) {
		if ($start_date != "" && $end_date != "") $date_cond = "and c.country_ship_date between '$start_date' and '$end_date'";
		else $date_cond = "";
	} else if ($date_type == 2) {
		if ($start_date == "" && $end_date == "") $date_cond = "";
		else $date_cond = "and b.pub_shipment_date between '$start_date' and '$end_date'";
	} else if ($date_type == 3) {
		if ($start_date == "" && $end_date == "") $date_cond = "";
		else $date_cond = "and b.shipment_date between '$start_date' and '$end_date'";
	} else if ($date_type == 4) {
		if ($db_type == 0) {
			if ($start_date == "" && $end_date == "") $date_cond = "";
			else $date_cond = "and b.insert_date between '" . $start_date . "' and '" . $end_date . " 23:59:59'";
		} else {
			if ($start_date == "" && $end_date == "") $date_cond = "";
			else $date_cond = "and b.insert_date between '" . $start_date . "' and '" . $end_date . " 11:59:59 PM'";
		}
	}

	if (str_replace("'", "", $hidd_job_id) != "")  $jobcond = "and a.id in(" . str_replace("'", "", $hidd_job_id) . ")";
	else  if (str_replace("'", "", $txt_job_no) == "") $jobcond = "";
	else $jobcond = "and a.job_no_prefix_num like '%" . str_replace("'", "", $txt_job_no) . "%' ";

	if (str_replace("'", "", $hidd_job_id) != "")  $style_ref_cond = "and a.id in(" . str_replace("'", "", $hidd_job_id) . ")";
	else  if (str_replace("'", "", $txt_style_ref) == "") $style_ref_cond = "";
	else $style_ref_cond = "and a.style_ref_no like '%" . str_replace("'", "", $txt_style_ref) . "%' ";

	if (str_replace("'", "", $hidd_po_id) != "") {
		$ordercond = "and b.id in (" . str_replace("'", "", $hidd_po_id) . ")";
		$jobcond = "";
	} else if (str_replace("'", "", $txt_order_no) == "") $ordercond = "";
	else $ordercond = "and b.po_number like '%" . str_replace("'", "", $txt_order_no) . "%' ";

	$file_no = str_replace("'", "", $txt_file_no);
	$internal_ref = str_replace("'", "", $txt_internal_ref);
	if ($file_no == "") $file_no_cond = "";
	else $file_no_cond = " and b.file_no='" . trim($file_no) . "' ";
	if ($internal_ref == "") $internal_ref_cond = "";
	else $internal_ref_cond = " and b.grouping='" . trim($internal_ref) . "' ";
	$season_cond = "";
	if ($cbo_season_name > 0) $season_cond = " and (a.season_matrix='" . trim($cbo_season_name) . "' or a.season_buyer_wise='" . trim($cbo_season_name) . "')";
	$cbo_team_leader = str_replace("'", "", $cbo_team_leader);
	$product_dept_id = str_replace("'", "", $cbo_dept);
	if ($cbo_team_leader > 0) $team_leader_cond = "and a.team_leader in ($cbo_team_leader)";
	else $team_leader_cond = "";
	if (trim($product_dept_id) > 0) $product_dept_cond = "and a.product_dept in ($product_dept_id)";
	else $product_dept_cond = "";

	ob_start();
	?>
	<div style="width:1320px">
		<table width="1300">
			<tr class="form_caption">
				<td colspan="14" align="center">Accessories Followup Report V2</td>
			</tr>
		</table>
		<table class="rpt_table" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<tr>
					<th width="50" rowspan="2">SL</th>
					<th width="170" rowspan="2">Item Name</th>
					<th width="130" rowspan="2">Name of Color</th>
					<th width="80" rowspan="2">Size</th>
					<th width="60" rowspan="2">UOM</th>
					<th width="80" rowspan="2">Req Qty</th>
					<th width="80" rowspan="2">WO Qty</th>
					<th width="240" colspan="3">Received Status</th>
					<th width="160" colspan="2">Issue Status</th>
					<th width="80">Closing Status</th>
					<th rowspan="2">Remaks</th>
				</tr>
				<tr>
					<th width="80">Qty</th>
					<th width="80"> % </th>
					<th width="80">In-House Balance Qty</th>
					<th width="80">Issue Qty</th>
					<th width="80">%</th>
					<th width="80"> Qty</th>
				</tr>
			</thead>
		</table>
		<div style="width:1320px; max-height:270px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<?
		$sql_qry = "select a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, b.file_no, b.grouping, b.id as po_id, b.po_number, (b.po_quantity*a.total_set_qnty) as order_quantity, b.pub_shipment_date, e.item_color_number_id as item_color, e.item_size, e.cons as bdg_req, e.pcs as bdg_pcs, f.id as trim_cost_dtls_id, f.trim_group, f.cons_uom as uom, d.cons, c.id as trim_dtla_id, c.remark, d.id as trim_book_con_dtls_id
        from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_trim_co_cons_dtls e, wo_pre_cost_trim_cost_dtls f
		left join wo_booking_dtls c on f.job_no=c.job_no and f.trim_group=c.trim_group and c.booking_type=2 and c.is_deleted=0 and c.status_active=1
		left join wo_trim_book_con_dtls d on c.id=d.wo_trim_booking_dtls_id and d.cons>0 and d.is_deleted=0 and d.status_active=1
        where a.job_no=b.job_no_mst and b.id=e.po_break_down_id and e.wo_pre_cost_trim_cost_dtls_id=f.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and f.is_deleted=0 and f.status_active=1 and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $year_cond $jobcond $ordercond $file_no_cond $internal_ref_cond $season_cond $team_leader_cond $product_dept_cond
        order by a.buyer_name, a.style_ref_no, c.trim_group";
				//echo $sql_qry;//die;
				$sql_query = sql_select($sql_qry); //$order_wise_data=array();
				$po_book_data = array(); $po_data = array(); $po_id_string = ""; $po_check = array(); $poididarr=array();
				foreach ($sql_query as $row) {
					$buy_key = $row[csf('buyer_name')] . "_" . $row[csf('style_ref_no')];
					if ($po_check[$row[csf('po_id')]] == "") {
						$po_id_string .= $row[csf('po_id')] . ",";
						$po_check[$row[csf('po_id')]] = $buy_key;
						$po_data[$buy_key]["po_number"] .= $row[csf('po_number')] . ",";
						$po_data[$buy_key]["order_quantity"] += $row[csf('order_quantity')];
						if ($job_check[$row[csf('job_no')]] == "") {
							$po_check[$row[csf('job_no')]] = $row[csf('job_no')];
							$po_data[$buy_key]["job_no"] .= $row[csf('job_no')] . ",";
						}
					}
					$poididarr[$row[csf('po_id')]]=$row[csf('po_id')];
					$req_qnty = 0;
					if ($dtls_data_check[$row[csf('po_id')]][$row[csf('trim_group')]][$row[csf('trim_cost_dtls_id')]] == "") {
						$dtls_data_check[$row[csf('po_id')]][$row[csf('trim_group')]][$row[csf('trim_cost_dtls_id')]] = $row[csf('po_id')];
						$po_book_data[$buy_key][$row[csf('trim_group')]]["remark"] = $row[csf('remark')];
						$po_book_data[$buy_key][$row[csf('trim_group')]]["uom"] = $row[csf('uom')];

						$req_qnty = (($row[csf("bdg_req")] / $row[csf("bdg_pcs")]) * $row[csf('order_quantity')]);
						$po_book_data[$buy_key][$row[csf('trim_group')]]["req_qnty"] += $req_qnty;
					}

					if ($booking_id_check[$row[csf('trim_book_con_dtls_id')]][$row[csf('trim_group')]] == "") {
						$booking_id_check[$row[csf('trim_book_con_dtls_id')]][$row[csf('trim_group')]] = $row[csf('trim_book_con_dtls_id')];
						$po_book_data[$buy_key][$row[csf('trim_group')]]["cons"] += $row[csf('cons')];
						$po_book_data[$buy_key][$row[csf('trim_group')]]["trim_dtla_id"] .= $row[csf('trim_book_con_dtls_id')] . ",";
					}

					//trim_dtla_id
					/*if($booking_id_check[$row[csf('trim_dtla_id')]][$row[csf('trim_group')]]=="")
			{
				$booking_id_check[$row[csf('trim_dtla_id')]][$row[csf('trim_group')]]=$row[csf('trim_dtla_id')];
				$po_book_data[$buy_key][$row[csf('trim_group')]]["trim_dtla_id"].=$row[csf('trim_dtla_id')].",";

			}*/
				}
				unset($sql_query);
				
				$con = connect();
				execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=6 and ENTRY_FORM=31");
				oci_commit($con);
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 31, 6, $poididarr, $empty_arr);//PO ID
				disconnect($con);
				
				$po_id_string = rtrim($po_id_string, ",");
				if ($po_id_string == "") {
					echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
					die;
				}

				$trims_sql = "select c.po_breakdown_id, c.entry_form, c.trans_type, d.item_group_id, d.item_color, d.item_size,
		(case when c.entry_form=24 and c.trans_type=1 then c.quantity else 0 end) as rcv_qnty,
		(case when c.entry_form=24 and c.trans_type=1 then c.id else 0 end) as rcv_trans_id,
		(case when c.entry_form=25 and c.trans_type=2 then c.quantity else 0 end) as issue_qnty,
		(case when c.entry_form=25 and c.trans_type=2 then c.id else 0 end) as issue_trans_id,
		(case when c.entry_form=49 and c.trans_type=3 then c.quantity else 0 end) as rcv_rtn_qnty,
		(case when c.entry_form=49 and c.trans_type=3 then c.id else 0 end) as rcv_rtn_trans_id,
		(case when c.entry_form=73 and c.trans_type=4 then c.quantity else 0 end) as issue_rtn_qnty,
		(case when c.entry_form=73 and c.trans_type=4 then c.id else 0 end) as issue_rtn_trans_id
		from order_wise_pro_details c, product_details_master d, gbl_temp_engine e
		where c.prod_id=d.id and c.entry_form in(24,25,49,73) and d.entry_form=24 and c.status_active=1 and c.is_deleted=0 and e.user_id = ".$user_id." and e.ref_from =6 and e.entry_form=31 and e.ref_val=c.po_breakdown_id ";
				//echo $trims_sql;//die;
				$trims_qty_data = sql_select($trims_sql);
				$trims_data = array();
				$check_receive_poBreakdownId = array();
				$check_issue_poBreakdownId = array();
				$check_receiveRtn_poBreakdownId = array();
				$check_issueRtn_poBreakdownId = array();
				foreach ($trims_qty_data as $row) {
					if ($check_receive_poBreakdownId[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]] == "") {
						if ($row[csf('entry_form')] == 24 && $row[csf('trans_type')] == 1) {
							$check_receive_poBreakdownId[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]] = $row[csf('po_breakdown_id')];
							$trims_data[$po_check[$row[csf("po_breakdown_id")]]][$row[csf("item_group_id")]]["rcv_qnty"] += $row[csf("rcv_qnty")];
							if ($row[csf("rcv_trans_id")] > 0) {
								$trims_data[$po_check[$row[csf("po_breakdown_id")]]][$row[csf("item_group_id")]]["rcv_trans_id"] .= $row[csf("rcv_trans_id")] . ",";
							}
						}
					}

					if ($check_issue_poBreakdownId[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]] == "") {
						if ($row[csf('entry_form')] == 25 && $row[csf('trans_type')] == 2) {
							$check_issue_poBreakdownId[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]] = $row[csf('po_breakdown_id')];
							$trims_data[$po_check[$row[csf("po_breakdown_id")]]][$row[csf("item_group_id")]]["issue_qnty"] += $row[csf("issue_qnty")];
							if ($row[csf("issue_trans_id")] > 0) {
								$trims_data[$po_check[$row[csf("po_breakdown_id")]]][$row[csf("item_group_id")]]["issue_trans_id"] .= $row[csf("issue_trans_id")] . ",";
							}
						}
					}

					if ($check_receiveRtn_poBreakdownId[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]] == "") {
						if ($row[csf('entry_form')] == 49 && $row[csf('trans_type')] == 3) {
							$check_receiveRtn_poBreakdownId[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]] = $row[csf('po_breakdown_id')];
							$trims_data[$po_check[$row[csf("po_breakdown_id")]]][$row[csf("item_group_id")]]["rcv_rtn_qnty"] += $row[csf("rcv_rtn_qnty")];
							if ($row[csf("rcv_rtn_trans_id")] > 0) {
								$trims_data[$po_check[$row[csf("po_breakdown_id")]]][$row[csf("item_group_id")]]["rcv_rtn_trans_id"] .= $row[csf("rcv_rtn_trans_id")] . ",";
							}
						}
					}

					if ($check_issueRtn_poBreakdownId[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]] == "") {
						if ($row[csf('entry_form')] == 73 && $row[csf('trans_type')] == 4) {
							$check_issueRtn_poBreakdownId[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]] = $row[csf('po_breakdown_id')];
							$trims_data[$po_check[$row[csf("po_breakdown_id")]]][$row[csf("item_group_id")]]["issue_rtn_qnty"] += $row[csf("issue_rtn_qnty")];
							if ($row[csf("issue_rtn_trans_id")] > 0) {
								$trims_data[$po_check[$row[csf("po_breakdown_id")]]][$row[csf("item_group_id")]]["issue_rtn_trans_id"] .= $row[csf("issue_rtn_trans_id")] . ",";
							}
						}
					}
				}
				unset($trims_qty_data);
				$con = connect();
				execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=6 and ENTRY_FORM=31");
				oci_commit($con);
				disconnect($con);

				$i = 1;
				foreach ($po_book_data as $key => $style_value) {
				?>
					<tr bgcolor="#FFFFCC">
						<td colspan="14"><? $key_ref = explode("_", $key);
											echo "Buyer Name : " . $buyer_library[$key_ref[0]] . ", Style Name : " . $key_ref[1] . ", Job No : " . chop($po_data[$key]["job_no"], ",") . ", Gmts Qty : " . $po_data[$key]["order_quantity"] . ", Order No : " . chop($po_data[$key]["po_number"], ","); ?></td>
					</tr>
					<?
					foreach ($style_value as $group_id => $value) {
						$rcv_qnty = $trims_data[$key][$group_id]["rcv_qnty"] - $trims_data[$key][$group_id]["rcv_rtn_qnty"];
						$issue_qnty = $trims_data[$key][$group_id]["issue_qnty"] - $trims_data[$key][$group_id]["issue_rtn_qnty"];
						$rcv_percent = (($rcv_qnty / $value["cons"]) * 100);
						$rcv_bal = $value["cons"] - $rcv_qnty;
						$issue_percent = (($issue_qnty / $value["cons"]) * 100);
						$close_bal = $rcv_qnty - $issue_qnty;
						$rcv_rcvRtn_trns_id = "";
						$iss_issRtn_trns_id = "";
						if (chop($trims_data[$key][$group_id]["rcv_trans_id"], ",") != "") {
							$rcv_rcvRtn_trns_id = chop($trims_data[$key][$group_id]["rcv_trans_id"], ",");
						}
						if (chop($trims_data[$key][$group_id]["rcv_rtn_trans_id"], ",") != "") {
							if ($rcv_rcvRtn_trns_id != "") {
								$rcv_rcvRtn_trns_id .= "," . chop($trims_data[$key][$group_id]["rcv_rtn_trans_id"], ",");
							} else {
								$rcv_rcvRtn_trns_id = chop($trims_data[$key][$group_id]["rcv_rtn_trans_id"], ",");
							}
						}

						if (chop($trims_data[$key][$group_id]["issue_trans_id"], ",") != "") {
							$iss_issRtn_trns_id = chop($trims_data[$key][$group_id]["issue_trans_id"], ",");
						}
						if (chop($trims_data[$key][$group_id]["issue_rtn_trans_id"], ",") != "") {
							if ($rcv_rcvRtn_trns_id != "") {
								$iss_issRtn_trns_id .= "," . chop($trims_data[$key][$group_id]["issue_rtn_trans_id"], ",");
							} else {
								$iss_issRtn_trns_id = chop($trims_data[$key][$group_id]["issue_rtn_trans_id"], ",");
							}
						}


						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="50" align="center"><? echo $i; ?></td>
							<td width="170" style="word-break: break-all;">
								<p><? echo $item_library[$group_id]; ?>&nbsp;</p>
							</td>
							<td width="130" style="word-break: break-all;">
								<p><? echo $color_library[$color_id]; ?>&nbsp;</p>
							</td>
							<td width="80" style="word-break: break-all;">
								<p><? echo $size_id; ?>&nbsp;</p>
							</td>
							<td width="60" align="center">
								<p><? echo $unit_of_measurement[$value["uom"]]; ?></p>
							</td>
							<td width="80" align="right"><? echo fn_number_format($value["req_qnty"], 2, '.', '');
															$tot_req_qnty += $value["req_qnty"]; ?></td>
							<td width="80" align="right" title="<? echo "book dtls id:" . chop($value["trim_dtla_id"], ","); ?>"><a href='#report_details' onclick="openmypage_booking_info('<? echo chop($value["trim_dtla_id"], ","); ?>','booking_info_dtls');"><? echo fn_number_format($value["cons"], 2, '.', '');
																																																																	$tot_wo_qnty += $value["cons"]; ?></a></td>
							<td width="80" align="right" title="<? echo "rcv rcvrtn trans id:" . $rcv_rcvRtn_trns_id; ?>"><a href='#report_details' onclick="openmypage_inhouse_info('<? echo $rcv_rcvRtn_trns_id; ?>','booking_inhouse_info_dtls');"><? echo fn_number_format($rcv_qnty, 2, '.', '');
																																																														$tot_rcv_qnty += $rcv_qnty; ?></a></td>
							<td width="80" align="right"><? if ($rcv_qnty > 0) echo fn_number_format($rcv_percent, 2, '.', '');
															else echo "0.00"; ?></td>
							<td width="80" align="right"><? echo fn_number_format($rcv_bal, 2, '.', '');
															$tot_rcv_bal += $rcv_bal; ?></td>
							<td width="80" align="right" title="<? echo "issue issuertn trans id:" . $iss_issRtn_trns_id; ?>"><a href='#report_details' onclick="openmypage_issue_info('<? echo $iss_issRtn_trns_id; ?>','booking_issue_info_dtls');"><? echo fn_number_format($issue_qnty, 2, '.', '');
																																																														$tot_issue_qnty += $issue_qnty; ?></a></td>
							<td width="80" align="right"><? if ($issue_qnty > 0) echo fn_number_format($issue_percent, 2, '.', ''); else echo "0.00"; ?></td>
							<td width="80" align="right"><? echo fn_number_format($close_bal, 2, '.', ''); $tot_close_bal += $close_bal; ?></td>
							<td><p><? echo $value["remark"]; ?>&nbsp;</p></td>
						</tr>
				<?
						$i++;
					}
				}
				?>
			</table>
		</div>
	</div>
	<?

	foreach (glob("*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$action";
	exit();
}

if ($action == "report_generate4") {
	$company_library = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
	$buyer_short_name_library = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$item_library = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
	$country_name_library = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$lib_supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$company_name = str_replace("'", "", $cbo_company_name);
	$serch_by = str_replace("'", "", $cbo_search_by);
	$buyer_id_cond = "";
	if (str_replace("'", "", $cbo_buyer_name) == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				$buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else $buyer_id_cond = "";
		} else $buyer_id_cond = "";
	} else $buyer_id_cond = " and a.buyer_name=$cbo_buyer_name"; //.str_replace("'","",$cbo_buyer_name)

	if ($db_type == 0) {
		if (str_replace("'", "", $cbo_year) != 0) $year_cond = " and year(a.insert_date)=" . str_replace("'", "", $cbo_year) . "";
		else $year_cond = "";
	} else {
		if (str_replace("'", "", $cbo_year) != 0) $year_cond = " and to_char(a.insert_date,'YYYY')=" . str_replace("'", "", $cbo_year) . "";
		else $year_cond = "";
	}

	$txt_job_no = str_replace("'", "", $txt_job_no);
	$txt_job_no = trim($txt_job_no);
	if (str_replace("'", "", $hidd_job_id) == "") {
		$txt_job_no = str_replace("'", "", $txt_job_no);
		$txt_job_no = trim($txt_job_no);
		if ($txt_job_no != "" || $txt_job_no != 0) $jobcond = "and a.job_no_prefix_num='" . $txt_job_no . "'";
		else $jobcond = "";

		if (str_replace("'", "", $txt_job_no) == "") $job_no_cond = "";
		else $job_no_cond = " and a.job_no_prefix_num in (" . str_replace("'", "", $txt_job_no) . ") ";

		if (str_replace("'", "", $txt_style_ref) != "") $style_ref_cond = " and a.style_ref_no ='" . str_replace("'", "", $txt_style_ref) . "'";
		else $style_ref_cond = "";
	} else if (str_replace("'", "", $hidd_job_id) != "") $jobcond = "and a.id in (" . str_replace("'", "", $hidd_job_id) . ")";
	else $jobcond = "";


	if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond = "";
	else $item_group_cond = "and e.trim_group in(" . str_replace("'", "", $cbo_item_group) . ")";

	$date_type = str_replace("'", "", $cbo_date_type);
	$start_date = str_replace("'", "", trim($txt_date_from));
	$end_date = str_replace("'", "", trim($txt_date_to));
	$date_cond = '';
	if ($date_type == 1) {
		if ($start_date != "" && $end_date != "") $date_cond = "and c.country_ship_date between '$start_date' and '$end_date'";
		else $date_cond = "";
	} else if ($date_type == 2) {
		if ($start_date == "" && $end_date == "") $date_cond = "";
		else $date_cond = "and b.pub_shipment_date between '$start_date' and '$end_date'";
	} else if ($date_type == 3) {
		if ($start_date == "" && $end_date == "") $date_cond = "";
		else $date_cond = "and b.shipment_date between '$start_date' and '$end_date'";
	} else if ($date_type == 4) {
		if ($db_type == 0) {
			if ($start_date == "" && $end_date == "") $date_cond = "";
			else $date_cond = "and b.insert_date between '" . $start_date . "' and '" . $end_date . " 23:59:59'";
		} else {
			if ($start_date == "" && $end_date == "") $date_cond = "";
			else $date_cond = "and b.insert_date between '" . $start_date . "' and '" . $end_date . " 11:59:59 PM'";
		}
	}
	if (str_replace("'", "", $txt_order_no) != "") $ordercond = " and b.po_number = '" . str_replace("'", "", $txt_order_no) . "'";
	else $ordercond = "";

	if (str_replace("'", "", $hidd_po_id) == "") {
		if (str_replace("'", "", $txt_order_no) != "") $ordercond = " and b.po_number = '" . str_replace("'", "", $txt_order_no) . "'";
		else $ordercond = "";
	} else if (str_replace("'", "", $hidd_po_id) != "") $ordercond = "and b.id in (" . str_replace("'", "", $hidd_po_id) . ")";
	else $ordercond = "";
	$file_no = str_replace("'", "", $txt_file_no);
	$internal_ref = str_replace("'", "", $txt_internal_ref);
	if ($file_no == "") $file_no_cond = "";
	else $file_no_cond = " and b.file_no='" . trim($file_no) . "' ";
	if ($internal_ref == "") $internal_ref_cond = "";
	else $internal_ref_cond = " and b.grouping='" . trim($internal_ref) . "' ";

	if (str_replace("'", "", $cbo_search_by) == 1) {
		if ($template == 1) {
			ob_start();
		?>

			<div style="width:1850px">
				<fieldset style="width:100%;">
					<table width="1850">
						<tr class="form_caption">
							<td colspan="21" align="center">Accessories Followup Report V2</td>
						</tr>
						<tr class="form_caption">
							<td colspan="21" align="center"><? echo $company_library[$company_name]; ?></td>
						</tr>
					</table>
					<table class="rpt_table scroll" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<thead>
							<th width="30">SL</th>
							<th width="50">Buyer</th>
							<th width="100">Job No</th>
							<th width="100">Style Ref</th>

							<th width="90">Order No</th>
							<th width="80">Order Qty</th>
							<th width="50">UOM</th>
							<th width="80">Qnty (Pcs)</th>
							<th width="80">Shipment Date</th>
							<th width="100">Trims Name</th>
							<th width="140">Item Description</th>
							<th width="100">Item Entry Date</th>
							<th width="100">Req Qnty</th>
							<th width="90">WO Qnty</th>
							<th width="60">Trims UOM</th>
							<th width="150">Supplier</th>
							<th width="70">WO Delay Days</th>
							<th width="90">In-House Qnty</th>
							<th width="90">Receive Balance</th>
							<th width="90">Issue to Prod.</th>
							<th width="70">Left Over/Balance</th>
						</thead>
						<tbody>

							<?
							$conversion_factor_array = array();
							$conversion_factor = sql_select("select id ,trim_uom,conversion_factor from  lib_item_group  ");
							foreach ($conversion_factor as $row_f) {
								$conversion_factor_array[$row_f[csf('id')]]['con_factor'] = $row_f[csf('conversion_factor')];
								$conversion_factor_array[$row_f[csf('id')]]['cons_uom'] = $row_f[csf('trim_uom')];
							}
							unset($conversion_factor);

							$po_data_arr = array(); $po_id_string = "";

							$today = date("Y-m-d");
							$condition = new condition();
						if (str_replace("'", "", $company_name) > 0) {
							$condition->company_name("=$company_name");
						}
						if (str_replace("'", "", $cbo_buyer_name) > 0) {
							$condition->buyer_name("=$cbo_buyer_name");
						}
						if (str_replace("'", "", $txt_job_no) != '') {
							$condition->job_no_prefix_num("=$txt_job_no");
						}
						if (str_replace("'", "", $txt_style_ref) != '') {
							$condition->style_ref_no("='" . str_replace("'", "", $txt_style_ref) . "'");
						}

						if (str_replace("'", "", $txt_order_no) != '') {
							$condition->po_number("='" . str_replace("'", "", $txt_order_no) . "'");
						}

						if (str_replace("'", "", $txt_file_no) != '') {
							$condition->file_no("='" . str_replace("'", "", $txt_file_no) . "'");
						}

						if (str_replace("'", "", $txt_internal_ref) != '') {
							$condition->grouping("='" . str_replace("'", "", $txt_internal_ref) . "'");
						}

						if (str_replace("'", "", $txt_date_from) != "" && str_replace("'", "", $txt_date_to) != "") {
							$start_date = (str_replace("'", "", $txt_date_from));
							$end_date = (str_replace("'", "", $txt_date_to));
							if ($date_type == 1) {
								$condition->country_ship_date(" between '$start_date' and '$end_date'");
							}
							if ($date_type == 2) {
								$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
							}
						}

						$condition->init();
						$trim = new trims($condition);
						//echo $trim->getQuery(); die;
							$trim_qty = $trim->getQtyArray_by_orderCountryAndPrecostdtlsid();
							$sql_query = sql_select("select a.buyer_name, a.job_no, a.job_no_prefix_num, style_ref_no, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, sum(c.order_quantity) as order_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, a.total_set_qnty, b.pub_shipment_date, d.costing_per, e.id as trim_dtla_id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, f.cons, f.country_id, f.cons as cons_cal
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c left join wo_pre_cost_trim_co_cons_dtls f on c.job_no_mst=f.job_no and c.po_break_down_id=f.po_break_down_id and f.cons > 0 join wo_pre_cost_trim_cost_dtls e on f.job_no=e.job_no and e.id=f.wo_pre_cost_trim_cost_dtls_id $item_group_cond
		join wo_pre_cost_mst d on e.job_no =d.job_no
		where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1       and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and
		a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $year_cond $jobcond  $ordercond $file_no_cond $internal_ref_cond
		group by a.buyer_name, a.job_no, a.job_no_prefix_num, style_ref_no, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, a.total_set_qnty, b.pub_shipment_date, d.costing_per, e.id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, f.cons, f.pcs, f.country_id order by b.id, e.trim_group");


							$tot_rows = count($sql_query);
							$i = 1; $poididarr=array();
							foreach ($sql_query as $row) {
								$dzn_qnty = 0;
								if ($row[csf('costing_per')] == 1) $dzn_qnty = 12;
								else if ($row[csf('costing_per')] == 3) $dzn_qnty = 12 * 2;
								else if ($row[csf('costing_per')] == 4) $dzn_qnty = 12 * 3;
								else if ($row[csf('costing_per')] == 5) $dzn_qnty = 12 * 4;
								else $dzn_qnty = 1;

								$po_qty = 0;
								if ($row[csf('country_id')] == 0) {
									$po_qty = $row[csf('order_quantity')];
								} else {
									$country_id = explode(",", $row[csf('country_id')]);
									for ($cou = 0; $cou <= count($country_id); $cou++) {
										$po_qty += $sql_po_qty_country_wise_arr[$row[csf('id')]][$country_id[$cou]];
									}
								}
								if ($row[csf('country_id')] == 0) {
									$req_qnty += $trim_qty[$row[csf('id')]][$row[csf('country_id')]][$row[csf('trim_dtla_id')]];
								} else {
									$country_id = array_unique(explode(",", $row[csf('country_id')]));
									foreach ($country_id as $cid) {
										$req_qnty += $trim_qty[$row[csf('id')]][$cid][$row[csf('trim_dtla_id')]];
									}
								}	
								
								$poididarr[$row[csf('id')]]=$row[csf('id')];

								//$req_qnty=($row[csf('cons_cal')]/$dzn_qnty)*$po_qty;
								//$req_qnty = ($row[csf('cons_cal')] / $dzn_qnty) * ($po_qty / $row[csf('total_set_qnty')]);
								$req_value = $row[csf('rate')] * $req_qnty;

								$po_data_arr[$row[csf('id')]][job_no] = $row[csf('job_no')];
								$po_data_arr[$row[csf('id')]][buyer_name] = $row[csf('buyer_name')];
								$po_data_arr[$row[csf('id')]][job_no_prefix_num] = $row[csf('job_no_prefix_num')];
								$po_data_arr[$row[csf('id')]][style_ref_no] = $row[csf('style_ref_no')];

								$po_data_arr[$row[csf('id')]][grouping] = $row[csf('grouping')];
								$po_data_arr[$row[csf('id')]][file_no] = $row[csf('file_no')];
								$po_data_arr[$row[csf('id')]][order_uom] = $row[csf('order_uom')];
								$po_data_arr[$row[csf('id')]][po_id] = $row[csf('id')];
								$po_data_arr[$row[csf('id')]][po_number] = $row[csf('po_number')];
								$po_data_arr[$row[csf('id')]][order_quantity_set] = $po_qnty_arr[$row[csf('id')]]['order_quantity']; //$row[csf('order_quantity_set')];
								$po_data_arr[$row[csf('id')]][order_quantity] = $po_qnty_arr[$row[csf('id')]]['order_quantity_pcs'];
								$po_data_arr[$row[csf('id')]][pub_shipment_date] = change_date_format($row[csf('pub_shipment_date')]);
								$po_id_string .= $row[csf('id')] . ",";

								$po_data_arr[$row[csf('id')]][trim_dtla_id][$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')]; // for rowspan
								$po_data_arr[$row[csf('id')]][trim_group][$row[csf('trim_group')]] = $row[csf('trim_group')];
								$po_data_arr[$row[csf('id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')]; // for rowspannn
								$po_data_arr[$row[csf('id')]][trim_group_dtls][$row[csf('trim_dtla_id')]] = $row[csf('trim_group')];
								$po_data_arr[$row[csf('id')]][remark][$row[csf('trim_dtla_id')]] = $row[csf('remark')];

								$po_data_arr[$row[csf('id')]][brand_sup_ref][$row[csf('trim_dtla_id')]] = $row[csf('brand_sup_ref')];
								$po_data_arr[$row[csf('id')]][apvl_req][$row[csf('trim_dtla_id')]] = $row[csf('apvl_req')];
								$po_data_arr[$row[csf('id')]][insert_date][$row[csf('trim_dtla_id')]] = $row[csf('insert_date')];
								$po_data_arr[$row[csf('id')]][req_qnty][$row[csf('trim_dtla_id')]] += $req_qnty;
								$po_data_arr[$row[csf('id')]][req_value][$row[csf('trim_dtla_id')]] += $req_value;
								$po_data_arr[$row[csf('id')]][cons_uom][$row[csf('trim_dtla_id')]] = $row[csf('cons_uom')];

								$po_data_arr[$row[csf('id')]][trim_group_from][$row[csf('trim_dtla_id')]] = "Pre_cost";
								$po_data_arr[$row[csf('id')]][rate][$row[csf('trim_dtla_id')]] = $row[csf('rate')];
								$po_data_arr[$row[csf('id')]][description][$row[csf('trim_dtla_id')]] = $row[csf('description')];
								$po_data_arr[$row[csf('id')]][country_id][$row[csf('trim_dtla_id')]] = $row[csf('country_id')];
							}
							unset($sql_query);
							
							$po_id_string = rtrim($po_id_string, ",");
							if ($po_id_string == "") {
								echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
								die;
							}
							
							$con = connect();
							execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=7 and ENTRY_FORM=31");
							oci_commit($con);
							fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 31, 7, $poididarr, $empty_arr);//PO ID
							disconnect($con);
							
							$app_sql=sql_select("select a.job_no_mst, a.accessories_type_id, a.approval_status from wo_po_trims_approval_info a, gbl_temp_engine d where a.is_deleted=0 and a.status_active=1 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=7");
							$app_status_arr = array();
							foreach ($app_sql as $row) {
								$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]] = $row[csf("approval_status")];
							}
							unset($app_sql);
							
							$sql_po_qty_country_wise_arr = array(); $po_job_arr = array();
							$sql_po_qty_country_wise = sql_select("select  b.id,b.job_no_mst,b.po_quantity,c.country_id,(c.order_quantity) as order_quantity_pcs, (c.order_quantity/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, gbl_temp_engine d where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=7 and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $year_cond $jobcond $ordercond order by b.id,b.job_no_mst,c.country_id");

							foreach ($sql_po_qty_country_wise as $sql_po_qty_country_wise_row) {
								$sql_po_qty_country_wise_arr[$sql_po_qty_country_wise_row[csf('id')]][$sql_po_qty_country_wise_row[csf('country_id')]] += $sql_po_qty_country_wise_row[csf('order_quantity_set')];
								$po_job_arr[$sql_po_qty_country_wise_row[csf('id')]] = $sql_po_qty_country_wise_row[csf('job_no_mst')];

								$po_qnty_arr[$sql_po_qty_country_wise_row[csf('id')]]['order_quantity_pcs'] += $sql_po_qty_country_wise_row[csf('order_quantity_pcs')];
								$po_qnty_arr[$sql_po_qty_country_wise_row[csf('id')]]['order_quantity'] = $sql_po_qty_country_wise_row[csf('po_quantity')];
							}
							unset($sql_po_qty_country_wise);
							//	$sql_po_qty_country_wise=array();

							$wo_sql_without_precost = sql_select("select min(a.booking_date) as booking_date ,b.job_no,LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no,LISTAGG(CAST(a.supplier_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty,sum(b.amount/b.exchange_rate) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b, gbl_temp_engine d where a.item_category=4 and a.booking_no=b.booking_no and  b.amount>0 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and b.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=7 and a.company_id=$company_name group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id"); //and item_from_precost=2

							foreach ($wo_sql_without_precost as $wo_row_without_precost) {

								$conversion_factor_rate = $conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['con_factor'];
								$cons_uom = $conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['cons_uom'];
								$booking_no = $wo_row_without_precost[csf('booking_no')];
								$supplier_id = $wo_row_without_precost[csf('supplier_id')];
								$wo_qnty = $wo_row_without_precost[csf('wo_qnty')]; //*$conversion_factor_rate;
								$amount = $wo_row_without_precost[csf('amount')];
								$wo_date = $wo_row_without_precost[csf('booking_date')];

								if ($wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] == "" || $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] == 0) {
									$trim_dtla_id = max($po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_dtla_id]) + 1;
									$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_dtla_id][$trim_dtla_id] = $trim_dtla_id; // for rowspan
									$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group][$wo_row_without_precost[csf('trim_group')]] = $wo_row_without_precost[csf('trim_group')];
									$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id] = $trim_dtla_id; // for rowspannn
									$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group_dtls][$trim_dtla_id] = $wo_row_without_precost[csf('trim_group')];
									$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][cons_uom][$trim_dtla_id] = $cons_uom;

									$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group_from][$trim_dtla_id] = "Booking Without Pre_cost";
								} else {
									$trim_dtla_id = $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
								}

								$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_qnty][$trim_dtla_id] += $wo_qnty;
								$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][amount][$trim_dtla_id] += $amount;
								$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_date][$trim_dtla_id] = $wo_date;
								$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_qnty_trim_group][$wo_row_without_precost[csf('trim_group')]] += $wo_qnty;

								$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][booking_no][$trim_dtla_id] = $booking_no;
								$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][supplier_id][$trim_dtla_id] = $supplier_id;
								$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][conversion_factor_rate][$trim_dtla_id] = $conversion_factor_rate;
							}
							unset($wo_sql_without_precost);

							$receive_qty_data = sql_select("select c.po_breakdown_id,b.item_group_id,sum(c.quantity) as quantity,b.rate  from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c, product_details_master d, gbl_temp_engine e
		where a.id=b.mst_id and a.entry_form=24 and a.item_category=4 and b.id=c.dtls_id and b.prod_id=d.id and b.trans_id=c.trans_id and c.trans_type=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=31 and e.ref_from=7 group by c.po_breakdown_id, b.item_group_id,b.rate order by b.item_group_id");

							foreach ($receive_qty_data as $row) {
								if ($po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]] == "" || $po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]] == 0) {
									$cons_uom = $conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];
									$trim_dtla_id = max($po_data_arr[$row[csf('po_breakdown_id')]][trim_dtla_id]) + 1;
									$po_data_arr[$row[csf('po_breakdown_id')]][trim_dtla_id][$trim_dtla_id] = $trim_dtla_id; // for rowspan
									$po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]] = $row[csf('item_group_id')];
									$po_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$trim_dtla_id] = $trim_dtla_id; // for rowspannn
									$po_data_arr[$row[csf('po_breakdown_id')]][trim_group_dtls][$trim_dtla_id] = $row[csf('item_group_id')];
									$po_data_arr[$row[csf('po_breakdown_id')]][cons_uom][$trim_dtla_id] = $cons_uom;
									$po_data_arr[$row[csf('po_breakdown_id')]][trim_group_from][$trim_dtla_id] = "Trim Receive";
								}
								$po_data_arr[$row[csf('po_breakdown_id')]][inhouse_qnty][$row[csf('item_group_id')]] += $row[csf('quantity')];
								$po_data_arr[$row[csf('po_breakdown_id')]][inhouse_rate][$row[csf('item_group_id')]] = $row[csf('rate')];
							}
							unset($receive_qty_data);
							
							$receive_rtn_qty_data = sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, c.avg_rate_per_unit as rate from product_details_master c, order_wise_pro_details d, gbl_temp_engine e where d.prod_id=c.id and d.trans_type=3 and d.entry_form=49 and d.status_active=1 and d.is_deleted=0 and d.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=31 and e.ref_from=7 order by c.item_group_id ASC");
							foreach ($receive_rtn_qty_data as $row) {
								$ord_uom_qty = 0;
								$receive_rtn_amt = 0;
								$ord_uom_qty = $row[csf('quantity')];
								$receive_rtn_amt = $ord_uom_qty * $row[csf('rate')];
								$po_data_arr[$row[csf('po_breakdown_id')]][receive_rtn_qty][$row[csf('item_group_id')]] += $ord_uom_qty;
								$po_data_arr[$row[csf('po_breakdown_id')]][receive_rtn_amt][$row[csf('item_group_id')]] += $receive_rtn_amt;
							}
							unset($receive_rtn_qty_data);

							$issue_qty_data = sql_select("select b.po_breakdown_id, a.item_group_id, b.quantity as quantity  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a, order_wise_pro_details b, gbl_temp_engine e where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=31 and e.ref_from=7");
							foreach ($issue_qty_data as $row) {
								$po_data_arr[$row[csf('po_breakdown_id')]][issue_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
							}
							unset($issue_qty_data);
							
							$issue_qty_data_arr = array();
							$sql_issue_ret = ("select c.po_breakdown_id as po_id, p.item_group_id,SUM(c.quantity) as quantity
					from   inv_transaction b, order_wise_pro_details c, product_details_master p, gbl_temp_engine e
					where  c.entry_form=73 and p.id=b.prod_id  and b.id=c.trans_id and b.transaction_type=4 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=31 and e.ref_from=7 group by c.po_breakdown_id,p.item_group_id");
							$issue_result = sql_select($sql_issue_ret);
							foreach ($issue_result as $row) {
								$issue_qty_data_arr[$row[csf('po_id')]][issue_ret_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
							}
							unset($sql_issue_ret);
							
							$con = connect();
							execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=7 and ENTRY_FORM=31");
							oci_commit($con);
							disconnect($con);
							
							$issue_qty_data = array(); $total_pre_costing_value = 0; $total_wo_value = 0; $summary_array = array();
							$i = 1; $x = 0;
							foreach ($po_data_arr as $key => $value) {
								$z = 1;
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								foreach ($value[trim_group] as $key_trim => $value_trim) {
									$y = 1;
									$summary_array[trim_group][$key_trim] = $key_trim;
									foreach ($value[$key_trim] as $key_trim1 => $value_trim1) {
										if ($z == 1) { $style_color = ''; } else { }
										$z++;

										if ($y == 1) { $style_colory = ''; } else { }
										$x++;
										$y++;
							?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $x; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $x; ?>">
											<td width="30" style=" color: <? echo $style_color ?>" title="<? echo $po_qty; ?>"><? echo $i; ?></td>
											<td width="50" style=" color: <? echo $style_color ?>">
												<p><? echo $buyer_short_name_library[$value[buyer_name]]; ?>&nbsp;</p>
											</td>
											<td width="100" style=" color: <? echo $style_color ?>" align="center">
												<p><? echo $value[job_no_prefix_num]; ?>&nbsp;</p>
											</td>
											<td width="100" style="word-break: break-all;color: <? echo $style_color ?>">
												<p><? echo $value[style_ref_no]; ?>&nbsp;</p>
											</td>

											<td width="90" style="word-break: break-all;color: <? echo $style_color ?>">
												<p><? $po_number = $value[po_number];
													echo $po_number; ?></p>
											</td>
											<td width="80" style="word-break: break-all;color: <? echo $style_color ?>" align="right">
												<p>
													<? echo fn_number_format($value[order_quantity_set], 0, '.', ''); ?>

													&nbsp;
												</p>
											</td>

											<td width="50" align="center" style="word-break: break-all;color: <? echo $style_color ?>">
												<p><? echo $unit_of_measurement[$value[order_uom]]; ?>&nbsp;</p>
											</td>
											<td width="80" align="right" style="word-break: break-all;color: <? echo $style_color ?>">
												<p><? echo fn_number_format($value[order_quantity], 0, '.', ''); ?>&nbsp;</p>
											</td>
											<td width="80" align="center" style="word-break: break-all;color: <? echo $style_color ?>">
												<p>
													<?
													$pub_shipment_date = $value[pub_shipment_date];
													echo $pub_shipment_date;
													?>
													&nbsp;
												</p>
											</td>
											<td width="100" title="<? echo $value[trim_group_from][$key_trim1];  ?>" style="word-break: break-all;">
												<p>
													<?
													echo $item_library[$value[trim_group_dtls][$key_trim1]];
													?>
													&nbsp;</p>
											</td>
											<td width="140" title="<? echo $value[description][$key_trim1]; ?>" style="word-break: break-all;">
												<p><? echo $value[description][$key_trim1]; ?>&nbsp;</p>
											</td>
											<td width="100" align="right">
												<p>
													<?
													$insert_date = explode(" ", $value[insert_date][$key_trim1]);
													echo change_date_format($insert_date[0], '', '', '');
													?></p>
											</td>
											<td width="100" align="right">
												<p>

													<?
													$req_qty = fn_number_format($value[req_qnty][$key_trim1], 2, '.', '');
													echo $req_qty;
													$summary_array[req_qnty][$key_trim] += $value[req_qnty][$key_trim1];
													?>
												</p>
											</td>

											<?
											$wo_qnty = fn_number_format($value[wo_qnty][$key_trim1], 2, '.', '');
											if ($wo_qnty > $req_qty) { $color_wo = "red"; } else if ($wo_qnty < $req_qty) { $color_wo = "yellow"; } else { $color_wo = ""; }
											$supplier_name_string = "";
											$supplier_id_arr = array_unique(explode(',', $value[supplier_id][$key_trim1]));
											foreach ($supplier_id_arr as $supplier_id_arr_key => $supplier_id_arr_value) {
												$supplier_name_string .= $lib_supplier_arr[$supplier_id_arr_value] . ",";
											}

											$booking_no_arr = array_unique(explode(',', $value[booking_no][$key_trim1]));
											$main_booking_no_large_data = "";
											foreach ($booking_no_arr as $booking_no1) {
												if ($main_booking_no_large_data == "") $main_booking_no_large_data = $booking_no1;
												else $main_booking_no_large_data .= "," . $booking_no1;
											}
											?>
											<td width="90" align="right" title="<? echo 'conversion_factor=' . $value[conversion_factor_rate][$key_trim1]; ?>" bgcolor="<? echo $color_wo; ?>">
												<p>
													<?
													//$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]['wo_qnty']
													echo fn_number_format($value[wo_qnty][$key_trim1], 2, '.', '');
													$summary_array[wo_qnty][$key_trim] += $value[wo_qnty][$key_trim1];
													?>
												</p>
											</td>
											<td width="60" align="center">
												<p>
													<?
													echo $unit_of_measurement[$value[cons_uom][$key_trim1]];
													$summary_array[cons_uom][$key_trim] = $value[cons_uom][$key_trim1];
													?></p>
											</td>
											<td width="150" align="left">
												<p>
													<? echo rtrim($supplier_name_string, ","); ?>
												</p>
											</td>
											<td width="70" align="right" title="<? echo change_date_format($value[wo_date][$key_trim1]); ?>">
												<p>
													<?
													$tot = change_date_format($insert_date[0]);
													if ($value[wo_qnty][$key_trim1] <= 0) {
														$daysOnHand = datediff('d', $tot, $today);
													} else {
														$wo_date = $value[wo_date][$key_trim1];
														$wo_date = change_date_format($wo_date);
														$daysOnHand = datediff('d', $tot, $wo_date);;
													}
													echo $daysOnHand;
													?>&nbsp;</p>
											</td>
											<?
											$inhouse_qnty = $value[inhouse_qnty][$key_trim] - $value[receive_rtn_qty][$key_trim];
											$inhouse_rate = $value[inhouse_rate][$key_trim];
											$inhouse_value = $inhouse_qnty * $inhouse_rate;
											$balance = $value[wo_qnty_trim_group][$key_trim] - $inhouse_qnty;
											$issue_qnty = $value[issue_qty][$key_trim];
											$issue_ret_qnty = $issue_qty_data_arr[$key][issue_ret_qty][$key_trim];
											$left_overqty = $inhouse_qnty - ($issue_qnty - $issue_ret_qnty);

											$summary_array[inhouse_qnty][$key_trim] += $inhouse_qnty;
											$summary_array[inhouse_value][$key_trim] += $inhouse_value;
											$summary_array[inhouse_qnty_bl][$key_trim] += $balance;
											$summary_array[issue_qty][$key_trim] += $issue_qnty - $issue_ret_qnty;
											$summary_array[left_overqty][$key_trim] += $left_overqty;
											?>
											<td width="90" align="right" style=" color: <? echo $style_colory ?>" title="<? echo "Inhouse-Qty: " . $value[inhouse_qnty][$key_trim] . "\nReturn Qty: " . $value[receive_rtn_qty][$key_trim]; ?>"><? echo fn_number_format($inhouse_qnty, 2, '.', ''); ?></td>
											<td width="90" align="right" style=" color: <? echo $style_colory ?>"><? echo fn_number_format($balance, 2, '.', ''); ?></td>
											<td width="90" align="right" title="<? echo "Issue-Qty: " . $issue_qnty . "\nReturn Qty: " . $issue_ret_qnty; ?>" style=" color: <? echo $style_colory ?>"><? echo fn_number_format($issue_qnty - $issue_ret_qnty, 2, '.', ''); ?></td>
											<td width="70" align="right" style=" color: <? echo $style_colory ?>"><? echo fn_number_format($left_overqty, 2, '.', ''); ?></td>
										</tr>
									<?
									}
								}
								$i++;
							}
							$po_data_arr = array();
							?>
						</tbody>
					</table>
					<!--<table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<th width="30"></th>
						<th width="50"></th>
						<th width="100"></th>
                        <th width="100"></th>
						<th width="90"></th>
						<th width="80" align="right"><? //echo fn_number_format($total_order_qnty,0); 
														?></th>
						<th width="50"></th>
						<th width="80" align="right"><? //echo fn_number_format($total_order_qnty_in_pcs,0); 
														?></th>
						<th width="80"></th>
						<th width="100"></th>
                        <th width="140"></th>
                        <th width="100"></th>
						<th width="100" align="right"><? //echo fn_number_format($total_req_qnty,2); 
														?></th>
						<th width="90" align="right"><? //echo fn_number_format($total_wo_qnty,2); 
														?></th>
                        <th width="60" align="right"></th>
                        <th width="150" align="right"></th>
                        <th width="70" align="right"><? //echo fn_number_format($req_value,2,'.',''); 
														?></th>
                        <th width="90" align="right"><? //echo fn_number_format($total_in_qnty,2); 
														?></th>

						<th width="90" align="right"><? //echo fn_number_format($total_rec_bal_qnty,2); 
														?></th>
						<th width="90" align="right"><? //echo fn_number_format($total_issue_qnty,2); 
														?></th>
						<th width="70" align="right"><? //echo fn_number_format($total_leftover_qnty,2); 
														?></th>
					</tfoot>
				</table>-->

				</fieldset>
			</div>
	<?
		}
	}

	foreach (glob("*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$action";
	exit();
}

if ($action == "report_generate5") {
	$company_library = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
	$buyer_short_name_library = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$item_library = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
	$team_leader_name_arr = return_library_array("select id,team_leader_name from lib_marketing_team", 'id', 'team_leader_name');
	$country_name_library = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$lib_supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$company_name = str_replace("'", "", $cbo_company_name);
	$serch_by = str_replace("'", "", $cbo_search_by);
	$buyer_id_cond = "";
	if (str_replace("'", "", $cbo_buyer_name) == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				$buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else $buyer_id_cond = "";
		} else $buyer_id_cond = "";
	} else $buyer_id_cond = " and a.buyer_name=$cbo_buyer_name"; //.str_replace("'","",$cbo_buyer_name)

	//condition add
	if ($db_type == 0) {
		if (str_replace("'", "", $cbo_year) != 0) $year_cond = " and year(a.insert_date)=" . str_replace("'", "", $cbo_year) . "";
		else $year_cond = "";
	} else {
		if (str_replace("'", "", $cbo_year) != 0) $year_cond = " and to_char(a.insert_date,'YYYY')=" . str_replace("'", "", $cbo_year) . "";
		else $year_cond = "";
	}

	$txt_job_no = str_replace("'", "", $txt_job_no);
	$txt_job_no = trim($txt_job_no);
	if ($txt_job_no != "" || $txt_job_no != 0) $jobcond = "and a.job_no_prefix_num='" . $txt_job_no . "'";
	else $jobcond = "";

	if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond = "";
	else $item_group_cond = "and e.trim_group in(" . str_replace("'", "", $cbo_item_group) . ")";

	$date_type = str_replace("'", "", $cbo_date_type);
	$start_date = str_replace("'", "", trim($txt_date_from));
	$end_date = str_replace("'", "", trim($txt_date_to));

	$season_year = str_replace("'", "", $cbo_season_year);
	$brand_id = str_replace("'", "", $cbo_brand_id);
	$season_name = str_replace("'", "", $cbo_season_name);
	$cbo_team_leader = str_replace("'", "", $cbo_team_leader);
	$product_dept_id = str_replace("'", "", $cbo_dept);
	if ($cbo_team_leader > 0) $team_leader_cond = "and a.team_leader in ($cbo_team_leader)";
	else $team_leader_cond = "";
	if (trim($product_dept_id) > 0) $product_dept_cond = "and a.product_dept in ($product_dept_id)";
	else $product_dept_cond = "";
	if ($brand_id > 0) $brand_cond = "and a.brand_id='" . $brand_id . "'";
	else $brand_cond = "";
	if ($season_year > 0) $season_year_cond = "and a.season_year='" . $season_year . "'";
	else $season_year_cond = "";
	if ($season_name > 0) $season_buyer_wise_cond = "and a.season_buyer_wise='" . $season_name . "'";
	else $season_buyer_wise_cond = "";

	$date_cond = '';
	if ($date_type == 1) {
		if ($start_date != "" && $end_date != "") $date_cond = "and c.country_ship_date between '$start_date' and '$end_date'";
		else $date_cond = "";
	} else if ($date_type == 2) {
		if ($start_date == "" && $end_date == "") $date_cond = "";
		else $date_cond = "and b.pub_shipment_date between '$start_date' and '$end_date'";
	} else if ($date_type == 3) {
		if ($start_date == "" && $end_date == "") $date_cond = "";
		else $date_cond = "and b.shipment_date between '$start_date' and '$end_date'";
	} else if ($date_type == 4) {
		if ($db_type == 0) {
			if ($start_date == "" && $end_date == "") $date_cond = "";
			else $date_cond = "and b.insert_date between '" . $start_date . "' and '" . $end_date . " 23:59:59'";
		} else {
			if ($start_date == "" && $end_date == "") $date_cond = "";
			else $date_cond = "and b.insert_date between '" . $start_date . "' and '" . $end_date . " 11:59:59 PM'";
		}
	}

	if (str_replace("'", "", $hidd_job_id) != "")  $jobcond = "and a.id in(" . str_replace("'", "", $hidd_job_id) . ")";
	else  if (str_replace("'", "", $txt_job_no) == "") $jobcond = "";
	else $jobcond = "and a.job_no_prefix_num like '%" . str_replace("'", "", $txt_job_no) . "%' ";

	if (str_replace("'", "", $hidd_job_id) != "")  $style_ref_cond = "and a.id in(" . str_replace("'", "", $hidd_job_id) . ")";
	else  if (str_replace("'", "", $txt_style_ref) == "") $style_ref_cond = "";
	else $style_ref_cond = "and a.style_ref_no like '%" . str_replace("'", "", $txt_style_ref) . "%' ";

	if (str_replace("'", "", $hidd_po_id) != "") {
		$ordercond = "and b.id in (" . str_replace("'", "", $hidd_po_id) . ")";
		$jobcond = "";
	} else if (str_replace("'", "", $txt_order_no) == "") $ordercond = "";
	else $ordercond = "and b.po_number like '%" . str_replace("'", "", $txt_order_no) . "%' ";
	$file_no = str_replace("'", "", $txt_file_no);
	$internal_ref = str_replace("'", "", $txt_internal_ref);
	if ($file_no == "") $file_no_cond = "";
	else $file_no_cond = " and b.file_no='" . trim($file_no) . "' ";
	if ($internal_ref == "") $internal_ref_cond = "";
	else $internal_ref_cond = " and b.grouping='" . trim($internal_ref) . "' ";

	if (str_replace("'", "", $cbo_search_by) == 1) {
		ob_start();
	?>
		<div style="width:700px">
			<?

			$item_arr = array();
			$conversion_factor_array = array();
			$conversion_factor = sql_select("select id,trim_uom,order_uom,conversion_factor from lib_item_group  ");
			foreach ($conversion_factor as $row_f) {
				$conversion_factor_array[$row_f[csf('id')]]['con_factor'] = $row_f[csf('conversion_factor')];
				$conversion_factor_array[$row_f[csf('id')]]['cons_uom'] = $row_f[csf('trim_uom')];
				$item_arr[$row_f[csf('id')]]['order_uom'] = $row_f[csf('order_uom')];
			}
			unset($conversion_factor);
			
			$today = date("Y-m-d");

			$txt_style_arr = explode(",", str_replace("'", "", $txt_style_ref));

			$n = 0;
			$style_arr = "";
			foreach ($txt_style_arr as $row) {
				if ($n == 0) {
					$style_arr .= "'" . $row . "'";
					$n++;
				} else {
					$style_arr .= ",'" . $row . "'";
				}
			}

			$condition = new condition();
			if (str_replace("'", "", $company_name) > 0) {
				$condition->company_name("=$company_name");
			}
			if (str_replace("'", "", $cbo_buyer_name) > 0) {
				$condition->buyer_name("=$cbo_buyer_name");
			}
			if (str_replace("'", "", $txt_job_no) != '') {
				$condition->job_no_prefix_num("in ($txt_job_no)");
			}
			if (str_replace("'", "", $txt_style_ref) != '') {
				$condition->style_ref_no("in ($style_arr)");
			}
			if (str_replace("'", "", $txt_order_no) != '') {
				$condition->po_number("like '%" . str_replace("'", "", $txt_order_no) . "%'");
			}
			if (str_replace("'", "", $txt_file_no) != '') {
				$condition->file_no("='" . str_replace("'", "", $txt_file_no) . "'");
			}
			if (str_replace("'", "", $txt_internal_ref) != '') {
				$condition->grouping("='" . str_replace("'", "", $txt_internal_ref) . "'");
			}

			if (str_replace("'", "", $txt_date_from) != "" && str_replace("'", "", $txt_date_to) != "") {
				$start_date = (str_replace("'", "", $txt_date_from));
				$end_date = (str_replace("'", "", $txt_date_to));
				//$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
				if ($date_type == 1) {
					$condition->country_ship_date(" between '$start_date' and '$end_date'");
				}
				if ($date_type == 2) {
					$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
				}
			}

			$condition->init();
			$trim = new trims($condition);
			//echo $trim->getQuery(); die;
			// $trim_group_qty_arr=$trim->getQtyArray_by_orderAndPrecostdtlsid();
			$trim_group_qty_arr = $trim->getQtyArray_by_orderCountryAndPrecostdtlsid();

			$sql_qry = "SELECT a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no,a.team_leader,a.product_dept, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, sum(c.order_quantity) as order_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, a.total_set_qnty, b.pub_shipment_date, b.shiping_status, d.costing_per, e.id as trim_dtla_id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date,d.costing_date,d.costing_per from wo_po_details_master a join wo_po_break_down b on a.job_no=b.job_no_mst join wo_po_color_size_breakdown c on a.job_no=c.job_no_mst and b.id=c.po_break_down_id join wo_pre_cost_mst d on a.job_no =d.job_no join wo_pre_cost_trim_cost_dtls e on e.job_no = d.job_no $item_group_cond left join  wo_pre_cost_trim_co_cons_dtls f on c.job_no_mst=f.job_no and c.po_break_down_id=f.po_break_down_id and f.job_no=e.job_no and e.id=f.wo_pre_cost_trim_cost_dtls_id where f.cons > 0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and a.company_name=$company_name $buyer_id_cond " . set_user_lavel_filtering(' and a.buyer_name', 'buyer_id') . " $date_cond $style_ref_cond $year_cond $jobcond $ordercond $file_no_cond $internal_ref_cond $brand_cond	$season_year_cond $season_buyer_wise_cond $team_leader_cond $product_dept_cond group by a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no,a.team_leader,a.product_dept, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, a.total_set_qnty, b.pub_shipment_date, b.shiping_status, d.costing_per, e.id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date,d.costing_date,d.costing_per order by b.id, e.trim_group";
			$sql_query = sql_select($sql_qry);
			$poid = ""; $trimID = ''; $poididarr=array();
			
			foreach ($sql_query as $row) {
				$poid .= $row[csf('id')] . ",";
				$trimID .= $row[csf('trim_dtla_id')] . ",";
				$poididarr[$row[csf('id')]]=$row[csf('id')];
			}
			
			$con = connect();
			execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=8 and ENTRY_FORM=31");
			oci_commit($con);
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 31, 8, $poididarr, $empty_arr);//PO ID
			disconnect($con);
			
			$app_sql=sql_select("select a.job_no_mst, a.accessories_type_id, a.approval_status from wo_po_trims_approval_info a, gbl_temp_engine d where a.is_deleted=0 and a.status_active=1 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=8");
			$app_status_arr = array();
			foreach ($app_sql as $row) {
				$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]] = $row[csf("approval_status")];
			}
			unset($app_sql);

			$sql_po_qty_country_wise_arr = array(); $po_job_arr = array();

			$sql_po_qty_country_wise = sql_select("select b.id, b.job_no_mst, c.country_id, c.order_quantity as order_quantity_pcs, b.po_quantity
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, gbl_temp_engine d
			where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=8 and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $year_cond $jobcond $ordercond");
			foreach ($sql_po_qty_country_wise as $sql_po_qty_country_wise_row) {
				$sql_po_qty_country_wise_arr[$sql_po_qty_country_wise_row[csf('id')]][$sql_po_qty_country_wise_row[csf('country_id')]] += $sql_po_qty_country_wise_row[csf('order_quantity_pcs')];
				$po_job_arr[$sql_po_qty_country_wise_row[csf('id')]] = $sql_po_qty_country_wise_row[csf('job_no_mst')];
				$po_qnty_arr[$sql_po_qty_country_wise_row[csf('id')]]['order_quantity_pcs'] += $sql_po_qty_country_wise_row[csf('order_quantity_pcs')];
				$po_qnty_arr[$sql_po_qty_country_wise_row[csf('id')]]['order_quantity'] = $sql_po_qty_country_wise_row[csf('po_quantity')];
			}
			unset($sql_po_qty_country_wise);

			$po_data_arr = array(); $po_id_string = "";

			$q = 0;
			$j = 0;
			
			$sqlcountry = "select a.po_break_down_id, a.wo_pre_cost_trim_cost_dtls_id, a.country_id from wo_pre_cost_trim_co_cons_dtls a, gbl_temp_engine b where a.cons > 0 and a.po_break_down_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=31 and b.ref_from=8";
			$sqlcountryres = sql_select($sqlcountry);
			$trimCountryArr = array();
			foreach ($sqlcountryres as $row) {
				$trimCountryArr[$row[csf('po_break_down_id')]][$row[csf('wo_pre_cost_trim_cost_dtls_id')]] .= $row[csf('country_id')] . ',';
			}
			unset($sqlcountryres);

			//print_r($trimCountryArr); die;

			$tot_rows = count($sql_query);
			$i = 1; $jobwisedata = array();
			foreach ($sql_query as $row) {
				$po_qty = 0; $req_qnty = 0; $req_value = 0;
				$countryId = implode(",", array_filter(array_unique(explode(",", $trimCountryArr[$row[csf('id')]][$row[csf('trim_dtla_id')]]))));

				if ($countryId == 0 || $countryId == '') {
					$req_qnty += $trim_group_qty_arr[$row[csf('id')]][$row[csf('country_id')]][$row[csf('trim_dtla_id')]];
				} else {
					$country_id = array_unique(explode(",", $countryId));
					foreach ($country_id as $c_id) {
						$req_qnty += $trim_group_qty_arr[$row[csf('id')]][$c_id][$row[csf('trim_dtla_id')]];
					}
				}
				$req_value = $row[csf('rate')] * $req_qnty;

				$jobwisedata[$row[csf('job_no')]]['po_id'] .= $row[csf('id')] . ",";

				$po_data_arr[$row[csf('id')]][job_no] = $row[csf('job_no')];
				$po_data_arr[$row[csf('id')]][costing_date] = $row[csf('costing_date')];
				$po_data_arr[$row[csf('id')]][costing_per] = $row[csf('costing_per')];
				$po_data_arr[$row[csf('id')]][buyer_name] = $row[csf('buyer_name')];
				$po_data_arr[$row[csf('id')]][job_no_prefix_num] = $row[csf('job_no_prefix_num')];
				$po_data_arr[$row[csf('id')]][style_ref_no] = $row[csf('style_ref_no')];
				$po_data_arr[$row[csf('id')]][team_leader] = $row[csf('team_leader')];
				$po_data_arr[$row[csf('id')]][product_dept_id] = $row[csf('product_dept')];

				$po_data_arr[$row[csf('id')]][grouping] = $row[csf('grouping')];
				$po_data_arr[$row[csf('id')]][file_no] = $row[csf('file_no')];
				$po_data_arr[$row[csf('id')]][order_uom] = $row[csf('order_uom')];
				$po_data_arr[$row[csf('id')]][po_id] = $row[csf('id')];
				$po_data_arr[$row[csf('id')]][po_number] = $row[csf('po_number')];
				$po_data_arr[$row[csf('id')]][order_quantity_set] = $po_qnty_arr[$row[csf('id')]]['order_quantity'];
				$po_data_arr[$row[csf('id')]][order_quantity] = $po_qnty_arr[$row[csf('id')]]['order_quantity_pcs'];
				$po_data_arr[$row[csf('id')]][pub_shipment_date] = change_date_format($row[csf('pub_shipment_date')]);
				$po_data_arr[$row[csf('id')]][shiping_status] = $row[csf('shiping_status')];
				$po_id_string .= $row[csf('id')] . ",";

				$po_data_arr[$row[csf('id')]][trim_dtla_id][$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')]; // for rowspan
				$po_data_arr[$row[csf('id')]][trim_group][$row[csf('trim_group')]] = $row[csf('trim_group')];
				$po_data_arr[$row[csf('id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')]; // for rowspannn
				$po_data_arr[$row[csf('id')]][trim_group_dtls][$row[csf('trim_dtla_id')]] = $row[csf('trim_group')];
				$po_data_arr[$row[csf('id')]][remark][$row[csf('trim_dtla_id')]] = $row[csf('remark')];

				$po_data_arr[$row[csf('id')]][brand_sup_ref][$row[csf('trim_dtla_id')]] = $row[csf('brand_sup_ref')];
				$po_data_arr[$row[csf('id')]][apvl_req][$row[csf('trim_dtla_id')]] = $row[csf('apvl_req')];
				$po_data_arr[$row[csf('id')]][insert_date][$row[csf('trim_dtla_id')]] = $row[csf('insert_date')];
				$po_data_arr[$row[csf('id')]][req_qnty][$row[csf('trim_dtla_id')]] = $req_qnty;
				$po_data_arr[$row[csf('id')]][req_value][$row[csf('trim_dtla_id')]] = $req_value;
				$po_data_arr[$row[csf('id')]][cons_uom][$row[csf('trim_dtla_id')]] = $conversion_factor_array[$row[csf('trim_group')]]['cons_uom']; //$row[csf('cons_uom')];

				$po_data_arr[$row[csf('id')]][trim_group_from][$row[csf('trim_dtla_id')]] = "Pre_cost";
				$po_data_arr[$row[csf('id')]][rate][$row[csf('trim_dtla_id')]] = $row[csf('rate')];
				$po_data_arr[$row[csf('id')]][description][$row[csf('trim_dtla_id')]] = $row[csf('description')];
				$po_data_arr[$row[csf('id')]][country_id][$row[csf('trim_dtla_id')]] = $countryId; //implode(",",array_unique(explode(",",$row[csf('country_id')])));
			}

			//echo "<pre>";print_r($po_data_arr);die;

			unset($sql_query);
			$po_id_string = rtrim($po_id_string, ",");
			if ($po_id_string == "") {
				echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
				die;
			}

			$transfer_sql_in = sql_select("SELECT  a.from_order_id, a.to_order_id, b.item_group, b.transfer_qnty as qnty
				FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, gbl_temp_engine d
				WHERE a.id = b.mst_id and a.to_order_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=8  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ");

			$transfer_in_data = array();
			foreach ($transfer_sql_in as $row) {
				$transfer_in_data[$row[csf('to_order_id')]][$row[csf('item_group')]] += $row[csf('qnty')];
			}
			unset($transfer_sql_in);
			$transfer_sql_out = sql_select("SELECT a.from_order_id, a.to_order_id, b.item_group, b.transfer_qnty as qnty
		                    FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, gbl_temp_engine d
		                    WHERE a.id = b.mst_id and a.from_order_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=8 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

			$transfer_out_data = array();
			foreach ($transfer_sql_out as $row) {
				$transfer_out_data[$row[csf('from_order_id')]][$row[csf('item_group')]] += $row[csf('qnty')];
			}

			if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond2 = "";
			else $item_group_cond2 = "and b.trim_group in(" . str_replace("'", "", $cbo_item_group) . ")";

			$wo_sql_without_precost = sql_select("select min(a.booking_date) as booking_date, b.job_no, LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, LISTAGG(CAST(a.supplier_id || '**' || a.pay_mode AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(nullif(b.amount , 0)/nullif(b.exchange_rate , 0)) as amount, sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b, gbl_temp_engine d where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name and b.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=8 $item_group_cond2 group by b.po_break_down_id, b.trim_group, b.job_no, b.pre_cost_fabric_cost_dtls_id"); //and item_from_precost=2

			foreach ($wo_sql_without_precost as $wo_row_without_precost) {
				if (array_key_exists($wo_row_without_precost[csf('po_break_down_id')], $po_data_arr)) {
					$conversion_factor_rate = $conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['con_factor'];
					$cons_uom = $conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['cons_uom'];
					$booking_no = $wo_row_without_precost[csf('booking_no')];
					$supplier_id = $wo_row_without_precost[csf('supplier_id')];
					$wo_qnty = $wo_row_without_precost[csf('wo_qnty')]; //*$conversion_factor_rate;
					$amount = $wo_row_without_precost[csf('amount')];
					$wo_date = $wo_row_without_precost[csf('booking_date')];

					if ($wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] == "" || $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] == 0) {
						$trim_dtla_id = max($po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_dtla_id]) + 1;
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_dtla_id][$trim_dtla_id] = $trim_dtla_id; // for rowspan
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group][$wo_row_without_precost[csf('trim_group')]] = $wo_row_without_precost[csf('trim_group')];
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id] = $trim_dtla_id; // for rowspannn
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group_dtls][$trim_dtla_id] = $wo_row_without_precost[csf('trim_group')];
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][cons_uom][$trim_dtla_id] = $cons_uom;

						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group_from][$trim_dtla_id] = "Booking Without Pre_cost";
					} else {
						$trim_dtla_id = $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
					}

					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_qnty][$trim_dtla_id] += $wo_qnty;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][amount][$trim_dtla_id] += $amount;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_date][$trim_dtla_id] = $wo_date;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_qnty_trim_group][$wo_row_without_precost[csf('trim_group')]] += $wo_qnty;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][booking_no][$trim_dtla_id] = $booking_no;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][supplier_id][$trim_dtla_id] = $supplier_id;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][conversion_factor_rate][$trim_dtla_id] = $conversion_factor_rate;
				}
			}
			unset($wo_sql_without_precost);

			if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond3 = ""; else $item_group_cond3 = "and a.item_group_id in(" . str_replace("'", "", $cbo_item_group) . ")";
			
			$receive_qty_data = sql_select("select b.po_breakdown_id, a.item_group_id, sum(b.quantity) as quantity, a.rate from inv_receive_master c,product_details_master d,inv_trims_entry_dtls a, order_wise_pro_details b, gbl_temp_engine e where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and d.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and b.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=31 and e.ref_from=8 $item_group_cond3 group by b.po_breakdown_id, a.item_group_id,a.rate order by a.item_group_id ");

			foreach ($receive_qty_data as $row) {
				if (array_key_exists($row[csf('po_breakdown_id')], $po_data_arr)) {
					if ($po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]] == "" || $po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]] == 0) {
						$cons_uom = $conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];
						$trim_dtla_id = max($po_data_arr[$row[csf('po_breakdown_id')]][trim_dtla_id]) + 1;
						$po_data_arr[$row[csf('po_breakdown_id')]][trim_dtla_id][$trim_dtla_id] = $trim_dtla_id; // for rowspan
						$po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]] = $row[csf('item_group_id')];
						$po_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$trim_dtla_id] = $trim_dtla_id; // for rowspannn
						$po_data_arr[$row[csf('po_breakdown_id')]][trim_group_dtls][$trim_dtla_id] = $row[csf('item_group_id')];
						$po_data_arr[$row[csf('po_breakdown_id')]][cons_uom][$trim_dtla_id] = $cons_uom;
						$po_data_arr[$row[csf('po_breakdown_id')]][trim_group_from][$trim_dtla_id] = "Trim Receive";
					}
					$po_data_arr[$row[csf('po_breakdown_id')]][inhouse_qnty][$row[csf('item_group_id')]] += $row[csf('quantity')];
					$po_data_arr[$row[csf('po_breakdown_id')]][inhouse_rate][$row[csf('item_group_id')]] = $row[csf('rate')];
					$po_data_arr[$row[csf('po_breakdown_id')]][inhouse_value][$row[csf('item_group_id')]] += $row[csf('quantity')] * $row[csf('rate')];
				}
			}
			unset($receive_qty_data);
			
			if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond4 = "";
			else $item_group_cond4 = "and c.item_group_id in(" . str_replace("'", "", $cbo_item_group) . ")";
			$receive_rtn_qty_data = sql_select("select d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d, gbl_temp_engine e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id   and b.transaction_type=3 and a.entry_form=49 and c.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=31 and e.ref_from=8 $item_group_cond4 group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");

			foreach ($receive_rtn_qty_data as $row) {
				if (array_key_exists($row[csf('po_breakdown_id')], $po_data_arr)) {
					$inhouse_rate = $po_data_arr[$row[csf('po_breakdown_id')]][inhouse_rate][$row[csf('item_group_id')]];
					$po_data_arr[$row[csf('po_breakdown_id')]][receive_rtn_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
					$po_data_arr[$row[csf('po_breakdown_id')]][receive_rtn_value][$row[csf('item_group_id')]] += $row[csf('quantity')] * $inhouse_rate;
				}
			}
			unset($receive_rtn_qty_data);

			$issue_rtn_qty_data = array();
			$issue_qty_data = sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity,a.issue_qnty  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a, order_wise_pro_details b, gbl_temp_engine e where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and p.item_category_id=4 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=31 and e.ref_from=8 $item_group_cond3 group by b.po_breakdown_id, a.item_group_id, a.issue_qnty");
			foreach ($issue_qty_data as $row) {
				if (array_key_exists($row[csf('po_breakdown_id')], $po_data_arr)) {
					$po_data_arr[$row[csf('po_breakdown_id')]][issue_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
					$po_data_arr[$row[csf('po_breakdown_id')]][issue_qnty_total][$row[csf('item_group_id')]] += $row[csf('issue_qnty')];
				}
			}
			unset($issue_qty_data);
			
			if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond5 = "";
			else $item_group_cond5 = "and p.item_group_id in(" . str_replace("'", "", $cbo_item_group) . ")";
			$sql_issue_ret = ("select c.po_breakdown_id as po_id, p.item_group_id, SUM(c.quantity) as quantity
            from   inv_transaction b, order_wise_pro_details c, product_details_master p, gbl_temp_engine e
            where  c.entry_form=73 and p.id=b.prod_id  and b.id=c.trans_id and b.transaction_type=4 and p.item_category_id=4 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=31 and e.ref_from=8 $item_group_cond5 group by c.po_breakdown_id,p.item_group_id");
			$issue_result = sql_select($sql_issue_ret);
			$issue_qty_data_arr = array();
			foreach ($issue_result as $row) {
				$issue_qty_data_arr[$row[csf('po_id')]][issue_ret_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
			}
			unset($issue_result);
			
			$con = connect();
			execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=8 and ENTRY_FORM=31");
			oci_commit($con);
			disconnect($con);
			$issue_qty_data = array(); $total_pre_costing_value = 0; $total_wo_value = 0; $summary_array = array(); $i = 1;
			//echo "<pre>";print_r($po_data_arr);die;
			
			foreach ($po_data_arr as $key => $value) {
				$buyer_name = $value[buyer_name];
				$costing_date = $value[costing_date];
				$costing_per = $value[costing_per];
				$style_ref_no = $value[style_ref_no];
				$po_id = rtrim($jobwisedata[$value[job_no]]['po_id'], ",");
				foreach ($value[trim_group] as $key_trim => $value_trim) {
					$gg = 1;
					$summary_array[trim_group][$key_trim] = $key_trim;
					foreach ($value[$key_trim] as $key_trim1 => $value_trim1) {
						$summary_array[item_app][$key_trim][all] += 1;
						if ($app_status == 3) {
							$summary_array[item_app][$key_trim][app] += 1;
						}

						$summary_array[req_qnty][$key_trim] += $value[req_qnty][$key_trim1];
						$summary_array[amount][$key_trim] += $value[amount][$key_trim1];
						$summary_array[wo_qnty][$key_trim] += $value[wo_qnty][$key_trim1];
						$summary_array[cons_uom][$key_trim] = $item_arr[$value[trim_group_dtls][$key_trim1]]['order_uom'];
						$summary_array[inhouse_qnty][$key_trim] += $inhouse_qnty;
						$summary_array[inhouse_qnty_bl][$key_trim] += $balance;
						$summary_array[issue_qty][$key_trim] += $issue_qnty; //$issue_qnty-$issue_ret_qty;
						//$summary_array[issue_qty][$key_trim]+=($tot_issue+$transfer_out_data[$value[po_id]][$key_trim]);//$issue_qnty-$issue_ret_qty;
						$summary_array[inhouse_value][$key_trim] += $inhouse_value;
						$summary_array[left_overqty][$key_trim] += $left_overqty;
						$summary_array[left_overvalue][$key_trim] += ($left_overqty * $value[rate][$key_trim1]);
						$summary_array[issue_to_prod][$key_trim] += (($issue_qnty - $issue_ret_qty) / $conv_rate + $transfer_out_data[$value[po_id]][$key_trim]) * $value[rate][$key_trim1];
					}
				}
			}
			$po_data_arr = array();
			?>
			</table>
		</div>
		<table>
			<tr>
				<td height="8"></td>
			</tr>
		</table>
		<table width="700">
			<tr class="form_caption">
				<td colspan="8" align="center">Accessories Followup Report</td>
			</tr>
			<tr class="form_caption">
				<td colspan="8" align="center"><? echo $company_library[$company_name]; ?></td>
			</tr>
		</table>
		<table class="rpt_table" width="690" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<th width="30">SL</th>
				<th width="110">Item</th>
				<th width="60">UOM</th>
				<th width="80">Approved %</th>
				<th width="110">Req Qty</th>
				<th width="110">WO Qty</th>
				<th width="110">WO Value</th>
				<th width="80">WO %</th>
			</thead>
			<?
			
			$conversion_factor_array = array(); $item_arr = array();
			$conversion_factor = sql_select("select id,trim_uom,order_uom,conversion_factor from lib_item_group where status_active=1  ");
			foreach ($conversion_factor as $row_f) {
				$conversion_factor_array[$row_f[csf('id')]]['con_factor'] = $row_f[csf('conversion_factor')];
			}
			unset($conversion_factor);
			$z = 1; $tot_req_qnty_summary = 0;
			foreach ($summary_array[trim_group] as $key_trim => $value) {
				if ($z % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				$con_factor = $conversion_factor_array[$key_trim]['con_factor'];
				$req_qnty_cal = $summary_array[req_qnty][$key_trim] / $con_factor;

				$inhouse_value = $summary_array[inhouse_value][$key_trim];
				$left_overqty = $summary_array[left_overvalue][$key_trim];
				$issue_to_prod = $summary_array[issue_to_prod][$key_trim];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr2_<? echo $z; ?>','<? echo $bgcolor; ?>')" id="tr2_<? echo $z; ?>">
					<td><? echo $z; ?></td>
					<td><p><? echo $item_library[$key_trim]; ?></p></td>
					<td align="center"><? echo $unit_of_measurement[$summary_array[cons_uom][$key_trim]]; ?></td>
					<td align="right"><? $app_perc = ($summary_array[item_app][$key_trim][app] * 100) / $summary_array[item_app][$key_trim][all];
										if ($summary_array[item_app][$key_trim][app]) echo fn_number_format($app_perc, 2); else echo ""; ?></td>
					<td align="right"><? echo fn_number_format($summary_array[req_qnty][$key_trim], 2);
										$tot_req_qnty += $summary_array[req_qnty][$key_trim]; ?></td>
					<td align="right"><? echo fn_number_format($summary_array[wo_qnty][$key_trim], 2);
										$tot_wo_qnty += $summary_array[wo_qnty][$key_trim]; ?></td>
					<td align="right"><? echo fn_number_format($summary_array[amount][$key_trim], 2);
										$tot_wo_value += $summary_array[amount][$key_trim]; ?></td>
					<td align="right" title="WO Qty/Req Qty/Conv. Factor(<? echo $con_factor ?>)*100"><? $wo_per = $summary_array[wo_qnty][$key_trim] / $req_qnty_cal * 100;
																										if ($summary_array[wo_qnty][$key_trim])  echo fn_number_format($wo_per, 2) . '%';
																										else echo ""; ?></td>
				</tr>
			<?
				$z++;
			}
			$summary_array = array();
			?>
			<tfoot>

				<th colspan="4" align="right">Total</th>
				<th align="right"><? echo fn_number_format($tot_req_qnty, 2); ?>&nbsp;</th>
				<th align="right"><? echo fn_number_format($tot_wo_qnty, 2); ?>&nbsp;</th>
				<th align="right"><? echo fn_number_format($tot_wo_value, 2); ?>&nbsp;</th>
				<th align="right"><? //echo fn_number_format($tot_req_qnty_summary,2); 
									?>&nbsp;</th>
			</tfoot>
		</table>
		</div>
	<?
	}

	foreach (glob("*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$action";
	exit();
}

if ($action == "report_generate6") {
	$company_library = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
	$buyer_short_name_library = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$item_library = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
	$team_leader_name_arr = return_library_array("select id,team_leader_name from lib_marketing_team", 'id', 'team_leader_name');
	$country_name_library = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$lib_supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$company_name = str_replace("'", "", $cbo_company_name);
	$serch_by = str_replace("'", "", $cbo_search_by);
	$buyer_id_cond = "";
	if (str_replace("'", "", $cbo_buyer_name) == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				$buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else $buyer_id_cond = "";
		} else $buyer_id_cond = "";
	} else $buyer_id_cond = " and a.buyer_name=$cbo_buyer_name"; //.str_replace("'","",$cbo_buyer_name)

	//condition add
	if ($db_type == 0) {
		if (str_replace("'", "", $cbo_year) != 0) $year_cond = " and year(a.insert_date)=" . str_replace("'", "", $cbo_year) . "";
		else $year_cond = "";
	} else {
		if (str_replace("'", "", $cbo_year) != 0) $year_cond = " and to_char(a.insert_date,'YYYY')=" . str_replace("'", "", $cbo_year) . "";
		else $year_cond = "";
	}

	$txt_job_no = str_replace("'", "", $txt_job_no);
	$txt_job_no = trim($txt_job_no);
	if ($txt_job_no != "" || $txt_job_no != 0) $jobcond = "and a.job_no_prefix_num='" . $txt_job_no . "'";
	else $jobcond = "";

	if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond = "";
	else $item_group_cond = "and e.trim_group in(" . str_replace("'", "", $cbo_item_group) . ")";

	$date_type = str_replace("'", "", $cbo_date_type);
	$start_date = str_replace("'", "", trim($txt_date_from));
	$end_date = str_replace("'", "", trim($txt_date_to));

	$season_year = str_replace("'", "", $cbo_season_year);
	$brand_id = str_replace("'", "", $cbo_brand_id);
	$season_name = str_replace("'", "", $cbo_season_name);
	$cbo_team_leader = str_replace("'", "", $cbo_team_leader);
	$product_dept_id = str_replace("'", "", $cbo_dept);
	if ($cbo_team_leader > 0) $team_leader_cond = "and a.team_leader in ($cbo_team_leader)";
	else $team_leader_cond = "";
	if (trim($product_dept_id) > 0) $product_dept_cond = "and a.product_dept in ($product_dept_id)";
	else $product_dept_cond = "";
	//echo $cbo_team_leader.'D'; $team_leader_cond $product_dept_cond

	if ($brand_id > 0) $brand_cond = "and a.brand_id='" . $brand_id . "'";
	else $brand_cond = "";
	if ($season_year > 0) $season_year_cond = "and a.season_year='" . $season_year . "'";
	else $season_year_cond = "";
	if ($season_name > 0) $season_buyer_wise_cond = "and a.season_buyer_wise='" . $season_name . "'";
	else $season_buyer_wise_cond = "";

	$date_cond = '';
	if ($date_type == 1) {
		if ($start_date != "" && $end_date != "") $date_cond = "and c.country_ship_date between '$start_date' and '$end_date'";
		else $date_cond = "";
	} else if ($date_type == 2) {
		if ($start_date == "" && $end_date == "") $date_cond = "";
		else $date_cond = "and b.pub_shipment_date between '$start_date' and '$end_date'";
	} else if ($date_type == 3) {
		if ($start_date == "" && $end_date == "") $date_cond = "";
		else $date_cond = "and b.shipment_date between '$start_date' and '$end_date'";
	} else if ($date_type == 4) {
		if ($db_type == 0) {
			if ($start_date == "" && $end_date == "") $date_cond = "";
			else $date_cond = "and b.insert_date between '" . $start_date . "' and '" . $end_date . " 23:59:59'";
		} else {
			if ($start_date == "" && $end_date == "") $date_cond = "";
			else $date_cond = "and b.insert_date between '" . $start_date . "' and '" . $end_date . " 11:59:59 PM'";
		}
	}

	if (str_replace("'", "", $hidd_job_id) != "")  $jobcond = "and a.id in(" . str_replace("'", "", $hidd_job_id) . ")";
	else  if (str_replace("'", "", $txt_job_no) == "") $jobcond = "";
	else $jobcond = "and a.job_no_prefix_num like '%" . str_replace("'", "", $txt_job_no) . "%' ";

	if (str_replace("'", "", $hidd_job_id) != "")  $style_ref_cond = "and a.id in(" . str_replace("'", "", $hidd_job_id) . ")";
	else  if (str_replace("'", "", $txt_style_ref) == "") $style_ref_cond = "";
	else $style_ref_cond = "and a.style_ref_no like '%" . str_replace("'", "", $txt_style_ref) . "%' ";

	if (str_replace("'", "", $hidd_po_id) != "") {
		$ordercond = "and b.id in (" . str_replace("'", "", $hidd_po_id) . ")";
		$jobcond = "";
	} else if (str_replace("'", "", $txt_order_no) == "") $ordercond = "";
	else $ordercond = "and b.po_number like '%" . str_replace("'", "", $txt_order_no) . "%' ";

	$file_no = str_replace("'", "", $txt_file_no);
	$internal_ref = str_replace("'", "", $txt_internal_ref);
	if ($file_no == "") $file_no_cond = "";
	else $file_no_cond = " and b.file_no='" . trim($file_no) . "' ";
	if ($internal_ref == "") $internal_ref_cond = "";
	else $internal_ref_cond = " and b.grouping='" . trim($internal_ref) . "' ";
	$width = 1240;
	if (str_replace("'", "", $cbo_search_by) == 1) {
		ob_start();
	?>
		<div style="width:<? echo $width; ?>px">
			<fieldset style="width:100%;" align="center">
				<table width="<? echo $width; ?>">
					<tr class="form_caption">
						<td colspan="10" align="center">Accessories Followup Report</td>
					</tr>
					<tr class="form_caption">
						<td colspan="10" align="center"><? echo $company_library[$company_name]; ?></td>
					</tr>
				</table>
				<?

				$item_arr = array(); $conversion_factor_array = array();
				$conversion_factor = sql_select("select id,trim_uom,order_uom,conversion_factor from lib_item_group  ");
				foreach ($conversion_factor as $row_f) {
					$conversion_factor_array[$row_f[csf('id')]]['con_factor'] = $row_f[csf('conversion_factor')];
					$conversion_factor_array[$row_f[csf('id')]]['cons_uom'] = $row_f[csf('trim_uom')];
					$item_arr[$row_f[csf('id')]]['order_uom'] = $row_f[csf('order_uom')];
				}
				unset($conversion_factor);
				
				$today = date("Y-m-d");
				$txt_style_arr = explode(",", str_replace("'", "", $txt_style_ref));

				$n = 0;
				$style_arr = "";
				foreach ($txt_style_arr as $row) {
					if ($n == 0) {
						$style_arr .= "'" . $row . "'";
						$n++;
					} else {
						$style_arr .= ",'" . $row . "'";
					}
				}

				$condition = new condition();
				if (str_replace("'", "", $company_name) > 0) {
					$condition->company_name("=$company_name");
				}
				if (str_replace("'", "", $cbo_buyer_name) > 0) {
					$condition->buyer_name("=$cbo_buyer_name");
				}
				if (str_replace("'", "", $txt_job_no) != '') {
					$condition->job_no_prefix_num("in ($txt_job_no)");
				}
				if (str_replace("'", "", $txt_style_ref) != '') {
					$condition->style_ref_no("in ($style_arr)");
				}

				if (str_replace("'", "", $txt_order_no) != '') {
					$condition->po_number("like '%" . str_replace("'", "", $txt_order_no) . "%'");
				}

				if (str_replace("'", "", $txt_file_no) != '') {
					$condition->file_no("='" . str_replace("'", "", $txt_file_no) . "'");
				}

				if (str_replace("'", "", $txt_internal_ref) != '') {
					$condition->grouping("='" . str_replace("'", "", $txt_internal_ref) . "'");
				}

				if (str_replace("'", "", $txt_date_from) != "" && str_replace("'", "", $txt_date_to) != "") {
					$start_date = (str_replace("'", "", $txt_date_from));
					$end_date = (str_replace("'", "", $txt_date_to));
					//$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
					if ($date_type == 1) {
						$condition->country_ship_date(" between '$start_date' and '$end_date'");
					}
					if ($date_type == 2) {
						$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
					}
				}

				$condition->init();
				$trim = new trims($condition);
				$trim_group_qty_arr = $trim->getQtyArray_by_orderCountryAndPrecostdtlsid();

				$sql_qry = "SELECT a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no,a.team_leader,a.product_dept, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, sum(c.order_quantity) as order_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, a.total_set_qnty, b.pub_shipment_date, b.shiping_status, d.costing_per, e.id as trim_dtla_id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date,d.costing_date,d.costing_per from wo_po_details_master a join wo_po_break_down b on a.job_no=b.job_no_mst join wo_po_color_size_breakdown c on a.job_no=c.job_no_mst and b.id=c.po_break_down_id join wo_pre_cost_mst d on a.job_no =d.job_no join wo_pre_cost_trim_cost_dtls e on e.job_no = d.job_no $item_group_cond left join  wo_pre_cost_trim_co_cons_dtls f on c.job_no_mst=f.job_no and c.po_break_down_id=f.po_break_down_id and f.job_no=e.job_no and e.id=f.wo_pre_cost_trim_cost_dtls_id where f.cons > 0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and a.company_name=$company_name $buyer_id_cond " . set_user_lavel_filtering(' and a.buyer_name', 'buyer_id') . " $date_cond $style_ref_cond $year_cond $jobcond $ordercond $file_no_cond $internal_ref_cond $brand_cond	$season_year_cond $season_buyer_wise_cond $team_leader_cond $product_dept_cond group by a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no,a.team_leader,a.product_dept, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, a.total_set_qnty, b.pub_shipment_date, b.shiping_status, d.costing_per, e.id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date,d.costing_date,d.costing_per order by b.id, e.trim_group";

				$sql_query = sql_select($sql_qry); $po_data_arr = array(); $po_id_string = "";
				$poid = ""; $trimID = ''; $poIdArray=array();
				foreach ($sql_query as $row) {
					$poid .= $row[csf('id')] . ",";
					$trimID .= $row[csf('trim_dtla_id')] . ",";
					$poIdArray[$row[csf('id')]]=$row[csf('id')];
				}
				
				$con = connect();
				execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=9 and ENTRY_FORM=31");
				oci_commit($con);
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 31, 9, $poIdArray, $empty_arr);//PO ID
				disconnect($con);
				
				$sqlcountry="select a.po_break_down_id, a.wo_pre_cost_trim_cost_dtls_id, a.country_id from wo_pre_cost_trim_co_cons_dtls a, gbl_temp_engine d where a.cons > 0 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=9";
				$sqlcountryres = sql_select($sqlcountry);
				$trimCountryArr = array();
				foreach ($sqlcountryres as $row) {
					$trimCountryArr[$row[csf('po_break_down_id')]][$row[csf('wo_pre_cost_trim_cost_dtls_id')]] .= $row[csf('country_id')] . ',';
				}
				unset($sqlcountryres);
				
				$app_sql=sql_select("select a.job_no_mst, a.accessories_type_id, a.approval_status from wo_po_trims_approval_info a, gbl_temp_engine d where a.is_deleted=0 and a.status_active=1 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=9");
				$app_status_arr=array();
				foreach($app_sql as $row)
				{
					$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
				}
				unset($app_sql);
				
				$sql_po_qty_country_wise_arr = array(); $po_job_arr = array();

				$sql_po_qty_country_wise = sql_select("select b.id, b.job_no_mst, c.country_id, c.order_quantity as order_quantity_pcs, b.po_quantity
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, gbl_temp_engine d
			where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=9 and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $year_cond $jobcond $ordercond");
				foreach ($sql_po_qty_country_wise as $sql_po_qty_country_wise_row) {
					$sql_po_qty_country_wise_arr[$sql_po_qty_country_wise_row[csf('id')]][$sql_po_qty_country_wise_row[csf('country_id')]] += $sql_po_qty_country_wise_row[csf('order_quantity_pcs')];
					$po_job_arr[$sql_po_qty_country_wise_row[csf('id')]] = $sql_po_qty_country_wise_row[csf('job_no_mst')];
					$po_qnty_arr[$sql_po_qty_country_wise_row[csf('id')]]['order_quantity_pcs'] += $sql_po_qty_country_wise_row[csf('order_quantity_pcs')];
					$po_qnty_arr[$sql_po_qty_country_wise_row[csf('id')]]['order_quantity'] = $sql_po_qty_country_wise_row[csf('po_quantity')];
				}
				unset($sql_po_qty_country_wise);

				//print_r($trimCountryArr); die;

				$tot_rows = count($sql_query); $i = 1; $jobwisedata = array();
				foreach ($sql_query as $row) {
					$po_qty = 0; $req_qnty = 0; $req_value = 0;
					$countryId = implode(",", array_filter(array_unique(explode(",", $trimCountryArr[$row[csf('id')]][$row[csf('trim_dtla_id')]]))));

					if ($countryId == 0 || $countryId == '') {
						$req_qnty += $trim_group_qty_arr[$row[csf('id')]][$row[csf('country_id')]][$row[csf('trim_dtla_id')]];
					} else {
						$country_id = array_unique(explode(",", $countryId));
						foreach ($country_id as $c_id) {
							$req_qnty += $trim_group_qty_arr[$row[csf('id')]][$c_id][$row[csf('trim_dtla_id')]];
						}
					}
					$req_value = $row[csf('rate')] * $req_qnty;

					$jobwisedata[$row[csf('job_no')]]['po_id'] .= $row[csf('id')] . ",";

					$po_data_arr[$row[csf('id')]][job_no] = $row[csf('job_no')];
					$po_data_arr[$row[csf('id')]][costing_date] = $row[csf('costing_date')];
					$po_data_arr[$row[csf('id')]][costing_per] = $row[csf('costing_per')];
					$po_data_arr[$row[csf('id')]][buyer_name] = $row[csf('buyer_name')];
					$po_data_arr[$row[csf('id')]][job_no_prefix_num] = $row[csf('job_no_prefix_num')];
					$po_data_arr[$row[csf('id')]][style_ref_no] = $row[csf('style_ref_no')];
					$po_data_arr[$row[csf('id')]][team_leader] = $row[csf('team_leader')];
					$po_data_arr[$row[csf('id')]][product_dept_id] = $row[csf('product_dept')];

					$po_data_arr[$row[csf('id')]][grouping] = $row[csf('grouping')];
					$po_data_arr[$row[csf('id')]][file_no] = $row[csf('file_no')];
					$po_data_arr[$row[csf('id')]][order_uom] = $row[csf('order_uom')];
					$po_data_arr[$row[csf('id')]][po_id] = $row[csf('id')];
					$po_data_arr[$row[csf('id')]][po_number] = $row[csf('po_number')];
					$po_data_arr[$row[csf('id')]][order_quantity_set] = $po_qnty_arr[$row[csf('id')]]['order_quantity'];
					$po_data_arr[$row[csf('id')]][order_quantity] = $po_qnty_arr[$row[csf('id')]]['order_quantity_pcs'];
					$po_data_arr[$row[csf('id')]][pub_shipment_date] = change_date_format($row[csf('pub_shipment_date')]);
					$po_data_arr[$row[csf('id')]][shiping_status] = $row[csf('shiping_status')];
					$po_id_string .= $row[csf('id')] . ",";

					$po_data_arr[$row[csf('id')]][trim_dtla_id][$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')]; // for rowspan
					$po_data_arr[$row[csf('id')]][trim_group][$row[csf('trim_group')]] = $row[csf('trim_group')];


					$po_data_arr[$row[csf('id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')]; // for rowspannn
					$po_data_arr[$row[csf('id')]][trim_group_dtls][$row[csf('trim_dtla_id')]] = $row[csf('trim_group')];


					$po_data_arr[$row[csf('id')]][remark][$row[csf('trim_dtla_id')]] = $row[csf('remark')];

					$po_data_arr[$row[csf('id')]][brand_sup_ref][$row[csf('trim_dtla_id')]] = $row[csf('brand_sup_ref')];
					$po_data_arr[$row[csf('id')]][apvl_req][$row[csf('trim_dtla_id')]] = $row[csf('apvl_req')];
					$po_data_arr[$row[csf('id')]][insert_date][$row[csf('trim_dtla_id')]] = $row[csf('insert_date')];
					$po_data_arr[$row[csf('id')]][req_qnty][$row[csf('trim_dtla_id')]]+= $req_qnty;
					$po_data_arr[$row[csf('id')]][req_value][$row[csf('trim_dtla_id')]] = $req_value;
					$po_data_arr[$row[csf('id')]][cons_uom][$row[csf('trim_dtla_id')]] = $conversion_factor_array[$row[csf('trim_group')]]['cons_uom']; //$row[csf('cons_uom')];

					$po_data_arr[$row[csf('id')]][trim_group_from][$row[csf('trim_dtla_id')]] = "Pre_cost";

					$po_data_arr[$row[csf('id')]][rate][$row[csf('trim_dtla_id')]] = $row[csf('rate')];
					$po_data_arr[$row[csf('id')]][description][$row[csf('trim_dtla_id')]] = $row[csf('description')];
					$po_data_arr[$row[csf('id')]][country_id][$row[csf('trim_dtla_id')]] = $countryId; //implode(",",array_unique(explode(",",$row[csf('country_id')])));
					$item_group_arr[$row[csf('trim_group')]] = $row[csf('trim_group')];
					$item_req_arr[$row[csf('id')]]['req_qnty'][$row[csf('trim_group')]]+= $req_qnty;
				}

				$receive_rtn_qty_data = sql_select("select d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d, gbl_temp_engine e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id   and b.transaction_type=3 and a.entry_form=49 and c.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=31 and e.ref_from=9 $item_group_cond4 group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");

			foreach ($receive_rtn_qty_data as $row) {
				if (array_key_exists($row[csf('po_breakdown_id')], $po_data_arr)) {
					$inhouse_rate = $po_data_arr[$row[csf('po_breakdown_id')]][inhouse_rate][$row[csf('item_group_id')]];
					$po_data_arr[$row[csf('po_breakdown_id')]][receive_rtn_qty][$row[csf('item_group_id')]] += $row[csf('quantity')];
					$po_data_arr[$row[csf('po_breakdown_id')]][receive_rtn_value][$row[csf('item_group_id')]] += $row[csf('quantity')] * $inhouse_rate;
				}
			}

			$receive_rtn_qty_data = array();

				$tot_item = count($item_group_arr);
				$width += $tot_item * 100;
				?>
				<table class="rpt_table" width="<? echo $width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<tr>
							<th colspan="6">&nbsp; </th>
							<th colspan="<? echo count($item_group_arr); ?>">Accessories Details</th>
						</tr>
						<tr>
							<th width="30">SL</th>
							<th width="50">Buyer</th>
							<th width="100">Style Ref</th>
							<th width="90">Order No</th>
							<th width="80">Order Qnty</th>
							<th width="80">Shipment Date</th>
							<?
							foreach ($item_group_arr as $item_key => $item) {
							?>
								<th width="100"><? echo $item_library[$item_key]; ?></th>
							<?
							}
							?>
						</tr>
					</thead>
				</table>
				<?

				if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond2 = "";
				else $item_group_cond2 = "and b.trim_group in(" . str_replace("'", "", $cbo_item_group) . ")";

				$wo_sql_without_precost = sql_select("select min(a.booking_date) as booking_date, b.job_no,b.delivery_date, LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, LISTAGG(CAST(a.supplier_id || '**' || a.pay_mode AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(nullif(b.amount , 0)/nullif(b.exchange_rate , 0)) as amount, sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b, gbl_temp_engine d where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name and b.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=9 $item_group_cond2 group by b.po_break_down_id,b.delivery_date, b.trim_group, b.job_no, b.pre_cost_fabric_cost_dtls_id"); //and item_from_precost=2


				foreach ($wo_sql_without_precost as $wo_row_without_precost) {
					if (array_key_exists($wo_row_without_precost[csf('po_break_down_id')], $po_data_arr)) {
						$wo_qnty = $wo_row_without_precost[csf('wo_qnty')]; //*$conversion_factor_rate;
						$wo_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_qnty][$wo_row_without_precost[csf('trim_group')]] += $wo_qnty;
					}
				}


				if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond3 = "";
				else $item_group_cond3 = "and a.item_group_id in(" . str_replace("'", "", $cbo_item_group) . ")";

				$wo_sql_without_precost = array();
				$receive_qty_data = sql_select("select b.po_breakdown_id, a.item_group_id, sum(b.quantity) as quantity, a.rate from inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b, gbl_temp_engine d where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and d.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and b.po_breakdown_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=9 $item_group_cond3 group by b.po_breakdown_id, a.item_group_id,a.rate order by a.item_group_id ");

				foreach ($receive_qty_data as $row) {
					if (array_key_exists($row[csf('po_breakdown_id')], $po_data_arr)) {
						$po_data_arr[$row[csf('po_breakdown_id')]][inhouse_qnty][$row[csf('item_group_id')]] += $row[csf('quantity')];
					}
				}

				$con = connect();
				execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=9 and ENTRY_FORM=31");
				oci_commit($con);
				disconnect($con);
				$i = 1; ?>
				<div style="width:<? echo $width + 20; ?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="<? echo $width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<tbody>
							<?
							foreach ($po_data_arr as $key => $value) {
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30" title="<? echo $po_qty; ?>"><? echo $i; ?></td>
									<td width="50">
										<p><? echo $buyer_short_name_library[$value[buyer_name]]; ?>&nbsp;</p>
									</td>
									<td width="100" style="word-break: break-all;">
										<p><? echo $value[style_ref_no]; ?>&nbsp;</p>
									</td>
									<td width="90" style="word-break: break-all;">
										<p><?
											$po_number = $value[po_number];
											//$po_number=implode(",", $value[po_id]);
											echo $po_number;
											?>
										</p>
									</td>
									<td width="80" align="right">
										<p><? echo fn_number_format($value[order_quantity_set], 0, '.', ''); ?>

										</p>
									</td>
									<td width="80" align="center" style="word-break: break-all;">
										<p>
											<?
											$pub_shipment_date = $value[pub_shipment_date];
											echo $pub_shipment_date;
											?>
											&nbsp;
										</p>
									</td>
									<?
									foreach ($item_group_arr as $item_key => $item) {
										$wo_order = $wo_data_arr[$key]['wo_qnty'][$item_key];
										$req_qnty=$po_data_arr[$key][receive_rtn_qty][$item_key];
										$req = $item_req_arr[$key]['req_qnty'][$item_key];
										$rec = $po_data_arr[$key]['inhouse_qnty'][$item_key];
										$inhouse=$rec-$req_qnty;
										//$per = fn_number_format(($rec / $req) * 100, 0, '.', '');
										$per = fn_number_format($inhouse / $wo_order * 100, 0, '.', '');
									?>
										<td width="100" title="<?=$inhouse.'/ '.$wo_order.'*100';?>" align="center" style="word-break: break-all;"><? //if($wo_order >0)if ($per != 0) echo $per . " %";
																										//else echo "0%"; 
																										?>
											<? if ($wo_order > 0)	echo $per . "%";
											else echo ""; ?>
										</td>
									<?
									}
									?>
								<?
								$i++;
							}
							$po_data_arr = array();
								?>
								</tr>
						</tbody>
					</table>
				</div>
			</fieldset>
		</div>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		<script>
			setFilterGrid("table_body", -1);
		</script>
	<?
	}



	//===========================================================================================================================================================
	$width = 1240;
	if (str_replace("'", "", $cbo_search_by) == 2) {

		ob_start();
	?>
		<div style="width:<? echo $width; ?>px">
			<fieldset style="width:100%;" align="center">
				<table width="<? echo $width; ?>">
					<tr class="form_caption">
						<td colspan="10" align="center">Accessories Followup Report</td>
					</tr>
					<tr class="form_caption">
						<td colspan="10" align="center"><? echo $company_library[$company_name]; ?></td>
					</tr>
				</table>
				<?

				$conversion_factor_array = array();
				$item_arr = array();
				$conversion_factor = sql_select("select id ,trim_uom,order_uom,conversion_factor from  lib_item_group  ");
				foreach ($conversion_factor as $row_f) {
					$conversion_factor_array[$row_f[csf('id')]]['con_factor'] = $row_f[csf('conversion_factor')];
					$conversion_factor_array[$row_f[csf('id')]]['cons_uom'] = $row_f[csf('trim_uom')];
					$item_arr[$row_f[csf('id')]]['order_uom'] = $row_f[csf('order_uom')];
				}
				unset($conversion_factor);

				
				$today = date("Y-m-d");
				$condition = new condition();
				if (str_replace("'", "", $company_name) > 0) {
					$condition->company_name("=$company_name");
				}
				if (str_replace("'", "", $cbo_buyer_name) > 0) {
					$condition->buyer_name("=$cbo_buyer_name");
				}
				if (str_replace("'", "", $txt_job_no) != '') {
					$condition->job_no_prefix_num("=$txt_job_no");
				}
				if (str_replace("'", "", $txt_style_ref) != '') {
					$condition->style_ref_no("='" . str_replace("'", "", $txt_style_ref) . "'");
				}

				if (str_replace("'", "", $txt_order_no) != '') {
					$condition->po_number("='" . str_replace("'", "", $txt_order_no) . "'");
				}

				if (str_replace("'", "", $txt_file_no) != '') {
					$condition->file_no("='" . str_replace("'", "", $txt_file_no) . "'");
				}

				if (str_replace("'", "", $txt_internal_ref) != '') {
					$condition->grouping("='" . str_replace("'", "", $txt_internal_ref) . "'");
				}

				if (str_replace("'", "", $txt_date_from) != "" && str_replace("'", "", $txt_date_to) != "") {
					$start_date = (str_replace("'", "", $txt_date_from));
					$end_date = (str_replace("'", "", $txt_date_to));
					if ($date_type == 1) {
						$condition->country_ship_date(" between '$start_date' and '$end_date'");
					}
					if ($date_type == 2) {
						$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
					}
				}
				$condition->init();
				$trim = new trims($condition);
				//echo $trim->getQuery(); die;
				$trim_group_qty_arr = $trim->getQtyArray_by_orderAndItemid();
				$trim_qty = $trim->getQtyArray_by_orderCountryAndPrecostdtlsid();

				$sql_query = sql_select("SELECT a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.team_leader, a.product_dept, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, sum(c.order_quantity) as order_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, b.pub_shipment_date, d.costing_per, e.id as trim_dtla_id, e.trim_group, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, f.cons, f.cons as cons_cal from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id join wo_pre_cost_mst d on a.id =d.job_id  join wo_pre_cost_trim_cost_dtls e on e.job_id = d.job_id $item_group_cond left join  wo_pre_cost_trim_co_cons_dtls f on c.job_id=f.job_id and c.po_break_down_id=f.po_break_down_id and f.job_id=e.job_id and e.id=f.wo_pre_cost_trim_cost_dtls_id where f.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $year_cond $jobcond $ordercond $file_no_cond $internal_ref_cond $brand_cond $season_year_cond $season_buyer_wise_cond $team_leader_cond $product_dept_cond group by a.buyer_name, a.job_no, a.job_no_prefix_num, style_ref_no, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, a.total_set_qnty, a.team_leader, a.product_dept, b.pub_shipment_date, d.costing_per, e.id, e.trim_group, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, f.cons, f.pcs order by b.id, e.trim_group");


				$poid = ""; $trimID = '';
				$poididarr = array(); $trimidarr = array(); $style_data_arr = array(); $po_id_string = "";
				foreach ($sql_query as $row) {
					$poid .= $row[csf('id')] . ",";
					$trimID .= $row[csf('trim_dtla_id')] . ",";
					$poididarr[$row[csf('id')]] = $row[csf('id')];
					$trimidarr[$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')];
				}
				$con = connect();
				execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = " . $user_id . " and ref_from =10 and ENTRY_FORM=31");
				oci_commit($con);
				
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 31, 10, $poididarr, $empty_arr); //PO ID
				disconnect($con);
				
				$app_sql=sql_select("select a.job_no_mst, a.accessories_type_id, a.approval_status from wo_po_trims_approval_info a, gbl_temp_engine d where a.is_deleted=0 and a.status_active=1 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=10");
				$app_status_arr=array();
				foreach($app_sql as $row)
				{
					$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
				}
				unset($app_sql);

				$sql_po_qty_country_wise_arr = array(); $po_job_arr = array();
				$sql_po_qty_country_wise = sql_select("select  b.id,b.job_no_mst,c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, gbl_temp_engine d where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=31 and d.ref_from=10 and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $year_cond $jobcond $ordercond group by   b.id,b.job_no_mst,c.country_id order by b.id,b.job_no_mst,c.country_id");
				foreach ($sql_po_qty_country_wise as $sql_po_qty_country_wise_row) {
					$sql_po_qty_country_wise_arr[$sql_po_qty_country_wise_row[csf('id')]][$sql_po_qty_country_wise_row[csf('country_id')]] = $sql_po_qty_country_wise_row[csf('order_quantity_set')];
					$po_job_arr[$sql_po_qty_country_wise_row[csf('id')]] = $sql_po_qty_country_wise_row[csf('job_no_mst')];
				}
				unset($sql_po_qty_country_wise);


				$sqlcountry = "select a.po_break_down_id, a.wo_pre_cost_trim_cost_dtls_id, a.country_id from wo_pre_cost_trim_co_cons_dtls a, gbl_temp_engine b where a.po_break_down_id=b.ref_val and b.user_id = " . $user_id . " and b.ref_from =10 and b.entry_form=31 and  a.cons > 0 ";
				//echo $sqlcountry;
				$sqlcountryres = sql_select($sqlcountry);
				$trimCountryArr = array();
				foreach ($sqlcountryres as $row) {
					$trimCountryArr[$row[csf('po_break_down_id')]][$row[csf('wo_pre_cost_trim_cost_dtls_id')]] .= $row[csf('country_id')] . ',';
				}
				unset($sqlcountryres);

				$tot_rows = count($sql_query); $i = 1; $poididarr = array();
				foreach ($sql_query as $row) {
					$dzn_qnty = 0;
					if ($row[csf('costing_per')] == 1) $dzn_qnty = 12;
					else if ($row[csf('costing_per')] == 3) $dzn_qnty = 12 * 2;
					else if ($row[csf('costing_per')] == 4) $dzn_qnty = 12 * 3;
					else if ($row[csf('costing_per')] == 5) $dzn_qnty = 12 * 4;
					else $dzn_qnty = 1;

					$po_qty = $req_qnty = 0;

					$countryId = "";
					$countryId = implode(",", array_filter(array_unique(explode(",", $trimCountryArr[$row[csf('id')]][$row[csf('trim_dtla_id')]]))));
					if ($countryId == 0 || $countryId == '') {
						//$po_qty=$row[csf('order_quantity')];
						$req_qnty += $trim_qty[$row[csf('id')]][$countryId][$row[csf('trim_dtla_id')]];
					} else {
						$country_id = array_unique(explode(",", $countryId));
						foreach ($country_id as $cid) {
							$req_qnty += $trim_qty[$row[csf('id')]][$cid][$row[csf('trim_dtla_id')]];
						}
					}
					$poididarr[$row[csf('id')]] = $row[csf('id')];
					// $req_qnty=($row[csf('cons_cal')]/$dzn_qnty)*$po_qty;
					$req_value = $row[csf('rate')] * $req_qnty;

					$style_data_arr[$row[csf('job_no')]][job_no] = $row[csf('job_no')];
					$style_data_arr[$row[csf('job_no')]][team_leader] = $row[csf('team_leader')];
					$style_data_arr[$row[csf('job_no')]][product_dept] = $row[csf('product_dept')];
					$style_data_arr[$row[csf('job_no')]][buyer_name] = $row[csf('buyer_name')];
					$style_data_arr[$row[csf('job_no')]][job_no_prefix_num] = $row[csf('job_no_prefix_num')];
					$style_data_arr[$row[csf('job_no')]][style_ref_no] = $row[csf('style_ref_no')];
					$style_data_arr[$row[csf('job_no')]][grouping] = $row[csf('grouping')];
					$style_data_arr[$row[csf('job_no')]][file_no] = $row[csf('file_no')];

					$style_data_arr[$row[csf('job_no')]][order_uom] = $row[csf('order_uom')];
					$style_data_arr[$row[csf('job_no')]][po_id][$row[csf('id')]] = $row[csf('id')];
					$style_data_arr[$row[csf('job_no')]][po_number][$row[csf('id')]] = $row[csf('po_number')];
					$style_data_arr[$row[csf('job_no')]][order_quantity_set][$row[csf('id')]] = $row[csf('order_quantity_set')];
					$style_data_arr[$row[csf('job_no')]][order_quantity][$row[csf('id')]] = $row[csf('order_quantity')];
					$style_data_arr[$row[csf('job_no')]][pub_shipment_date][$row[csf('id')]] = change_date_format($row[csf('pub_shipment_date')]);
					$po_id_string .= $row[csf('id')] . ",";

					$style_data_arr[$row[csf('job_no')]][trim_dtla_id][$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')]; // for rowspan
					$style_data_arr[$row[csf('job_no')]][trim_group][$row[csf('trim_group')]] = $row[csf('trim_group')];
					$style_data_arr[$row[csf('job_no')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')]; // for rowspannn
					$style_data_arr[$row[csf('job_no')]][trim_group_dtls][$row[csf('trim_dtla_id')]] = $row[csf('trim_group')];

					$style_data_arr[$row[csf('job_no')]][brand_sup_ref][$row[csf('trim_dtla_id')]] = $row[csf('brand_sup_ref')];
					$style_data_arr[$row[csf('job_no')]][apvl_req][$row[csf('trim_dtla_id')]] = $row[csf('apvl_req')];
					$style_data_arr[$row[csf('job_no')]][insert_date][$row[csf('trim_dtla_id')]] = $row[csf('insert_date')];
					$style_data_arr[$row[csf('job_no')]][req_qnty][$row[csf('trim_dtla_id')]] += $req_qnty;
					$style_data_arr[$row[csf('job_no')]][req_value][$row[csf('trim_dtla_id')]] += $req_value;
					$style_data_arr[$row[csf('job_no')]][cons_uom][$row[csf('trim_dtla_id')]] = $conversion_factor_array[$row[csf('trim_group')]]['cons_uom']; //$row[csf('cons_uom')];

					$style_data_arr[$row[csf('job_no')]][trim_group_from][$row[csf('trim_dtla_id')]] = "Pre_cost";

					$style_data_arr[$row[csf('job_no')]][rate][$row[csf('trim_dtla_id')]] = $row[csf('rate')];
					$style_data_arr[$row[csf('job_no')]][description][$row[csf('trim_dtla_id')]] = $row[csf('description')];
					$style_data_arr[$row[csf('job_no')]][country_id][$row[csf('trim_dtla_id')]] .= $row[csf('country_id')] . ",";
					$item_group_arr[$row[csf('trim_group')]] = $row[csf('trim_group')];
					$item_req_arr[$row[csf('job_no')]]['req_qnty'][$row[csf('trim_group')]] = $req_qnty;
				}
				$tot_item = count($item_group_arr);
				$width += $tot_item * 100;
				
				unset($sql_query);
				$po_id_string = rtrim($po_id_string, ",");
				if ($po_id_string == "") {
					echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
					die;
				}
				?>
				<table class="rpt_table" width="<? echo $width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<tr>
							<th colspan="6">&nbsp; </th>
							<th colspan="<? echo count($item_group_arr); ?>">Accessories Details</th>
						</tr>
						<tr>
							<th width="30">SL</th>
							<th width="50">Buyer</th>
							<th width="100">Style Ref</th>
							<th width="90">Order No</th>
							<th width="80">Order Qnty</th>
							<th width="80">Shipment Date</th>
							<?
							foreach ($item_group_arr as $item_key => $item) {
							?>
								<th width="100"><? echo $item_library[$item_key]; ?></th>
							<?
							}
							?>
						</tr>
					</thead>
				</table>
				<?	

				if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond3 = ""; else $item_group_cond3 = "and a.item_group_id in(" . str_replace("'", "", $cbo_item_group) . ")";

				$receive_qty_data = sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a, order_wise_pro_details b, gbl_temp_engine t where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and d.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id=t.ref_val and t.user_id = " . $user_id . " and t.ref_from=10 and t.entry_form=31  $item_group_cond3 group by b.po_breakdown_id, a.item_group_id order by a.item_group_id ");

				foreach ($receive_qty_data as $row) {

					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][inhouse_qnty][$row[csf('item_group_id')]] += $row[csf('quantity')];
				}
				unset($receive_qty_data);

				if (str_replace("'", "", $cbo_item_group) == "") $item_group_cond2 = ""; else $item_group_cond2 = "and b.trim_group in(" . str_replace("'", "", $cbo_item_group) . ")";
				$wo_sql_without_precost = sql_select("select min(a.booking_date) as booking_date ,b.job_no,b.delivery_date, LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, LISTAGG(CAST(a.supplier_id || '**' || a.pay_mode AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount) as amount, sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b, gbl_temp_engine t where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name and b.po_break_down_id=t.ref_val and t.user_id = " . $user_id . " and t.ref_from=10 and t.entry_form=31 $item_group_cond2 group by b.po_break_down_id,b.delivery_date,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id"); //and item_from_precost=2


				foreach ($wo_sql_without_precost as $wo_row_without_precost) {
					$wo_qnty = $wo_row_without_precost[csf('wo_qnty')]; //*$conversion_factor_rate;
					$wo_data_arr[$wo_row_without_precost[csf('job_no')]][wo_qnty][$wo_row_without_precost[csf('trim_group')]] += $wo_qnty;
				}
				unset($wo_sql_without_precost);
				
				$con = connect();
				execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=10 and ENTRY_FORM=31");
				oci_commit($con);
				disconnect($con);
				?>
				<table class="rpt_table" width="<? echo $width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<?
					$i = 1;
					foreach ($style_data_arr as $key => $value) {

						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" title="<? echo $po_qty; ?>"><? echo $i; ?></td>
							<td width="50">
								<p><? echo $buyer_short_name_library[$value[buyer_name]]; ?>&nbsp;</p>
							</td>
							<td width="100" style="word-break: break-all;">
								<p><? echo $value[style_ref_no]; ?>&nbsp;</p>
							</td>
							<td width="90" style="word-break: break-all;">
								<p><?
									$po_number = implode(",", $value[po_number]);
									$po_id = implode(",", $value[po_id]);
									echo $po_number;
									?>
								</p>
							</td>
							<td width="80" align="right">
								<p><? echo fn_number_format(array_sum($value[order_quantity_set]), 0, '.', ''); ?>

								</p>
							</td>
							<td width="80" align="center" style="word-break: break-all;">
								<p>
									<?
									$pub_shipment_date = implode(",", $value[pub_shipment_date]);
									echo $pub_shipment_date;
									?>
									&nbsp;
								</p>
							</td>
							<?
							foreach ($item_group_arr as $item_key => $item) {
								$wo_order = $wo_data_arr[$key]['wo_qnty'][$item_key];
								$req = $item_req_arr[$key]['req_qnty'][$item_key];
								$rec = $style_data_arr[$key]['inhouse_qnty'][$item_key];
								//$per = fn_number_format(($rec / $req) * 100, 0, '.', '');
								$per = fn_number_format($rec / $wo_order * 100, 0, '.', '');
							?>
								<td width="100" align="center" style="word-break: break-all;"><? if ($wo_order > 0)	echo $per . "%"; else echo ""; ?></td>
							<?
							}
						$i++;
					}
					$style_data_arr = array();
						?>
						</tr>
				</table>
			</fieldset>
		</div>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		<script>
			setFilterGrid("table_body", -1);
		</script>
	<?
	}


	foreach (glob("*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$action";
	exit();
}

if ($action == "booking_info_dtls") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function generate_trim_report(action, txt_booking_no, cbo_company_name, id_approved_id, cbo_isshort, template_id) {
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
			if (r == true) {
				show_comment = "1";
			} else {
				show_comment = "0";
			}
			if (template_id != 67) {
				var data = "action=" + action + '&report_title=Country and Order Wise Trims Booking&show_comment=' + show_comment + '&txt_booking_no=' + "'" + txt_booking_no + "'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_isshort=' + cbo_isshort + '&link=1';
				//freeze_window(5);
				http.open("POST", "../../woven_order/requires/trims_booking_controller.php", true);
			} else {
				var data = "action=" + action + '&report_title=Country and Order Wise Trims Booking&show_comment=' + show_comment + '&txt_booking_no=' + "'" + txt_booking_no + "'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&report_type=1&link=1';
				http.open("POST", "../../woven_order/requires/trims_booking_multi_job_controllerurmi.php", true);
			}
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;

		}

		function generate_trim_report_reponse() {
			if (http.readyState == 4) {
				$('#data_panel').html(http.responseText);
				var w = window.open("Surprise", "../_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
					'<html><head><title></title></head><body>' + document.getElementById('data_panel').innerHTML + '</body</html>');
				d.close();
			}
		}

		/*function new_window()
		{

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"  /><title></title></head><body style="font-size:12px; font-family:Arial Narrow">'+document.getElementById('view_part').innerHTML+'</body</html>');
			d.close();
		}*/
	</script>


	<!--<div style="width:620px" align="center"><input type="button" value="Print Preview" onClick="new_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:620px; margin-left:3px" id="view_part">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td align="center" colspan="7"><strong> WO Summary</strong> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<th width="120">Wo No</th>
					<th width="75">Wo Date</th>
					<th width="100">Supplier</th>
					<th width="90">Wo Qty</th>
					<th width="70">Rate</th>
					<th>Amount</th>
				</thead>
				<tbody>
					<?

					$print_report_format = return_field_value("format_id", "lib_report_template", "template_name ='" . $cbo_company_name . "' and module_id=2 and report_id in(5,6) and is_deleted=0 and status_active=1");
					//echo 	$print_report_format;
					$print2 = return_field_value("format_id", "lib_report_template", "template_name ='" . $cbo_company_name . "' and module_id=2 and report_id in(26) and is_deleted=0 and status_active=1");

					$report = max(explode(',', $print_report_format));
					if ($print2 == 67) {
						$report = 67;
					} else if ($print2 == 183) {
						$report = 183;
					}
					else if ($print2 == 404) {
						$report = 404;
					}
					else if ($print2 == 419) {
						$report = 419;
					}
					if ($report == 13) {
						$reporAction = "show_trim_booking_report";
					} elseif ($report == 14) {
						$reporAction = "show_trim_booking_report1";
					} elseif ($report == 15) {
						$reporAction = "show_trim_booking_report2";
					} elseif ($report == 16) {
						$reporAction = "show_trim_booking_report3";
					} elseif ($report == 67) {
						$reporAction = "show_trim_booking_report2";
					} elseif ($report == 183) {
						$reporAction = "show_trim_booking_report3";
					}elseif ($report == 404) {
						$reporAction = "show_trim_booking_report21";
					}elseif ($report == 419) {
						$reporAction = "show_trim_booking_report22";
					}

					$conversion_factor_array = array();
					$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
					$conversion_factor = sql_select("select id ,conversion_factor from  lib_item_group ");
					foreach ($conversion_factor as $row_f) {
						$conversion_factor_array[$row_f[csf('id')]]['con_factor'] = $row_f[csf('conversion_factor')];
					}

					$i = 1;

					$wo_sql = "select a.booking_no, a.booking_date, a.supplier_id, a.is_approved, a.is_short, sum(c.cons) as wo_qnty, sum(c.amount) as wo_amount
				from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c
				where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and c.cons>0 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.id in($booking_dtls_id)
				group by  a.booking_no, a.booking_date, a.supplier_id, a.is_approved, a.is_short";
					$dtlsArray = sql_select($wo_sql);
					foreach ($dtlsArray as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$wo_rate = $row[csf('wo_amount')] / $row[csf('wo_qnty')];
					?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td align="center"><? echo $i; ?></td>
							<td title="Multiple Order Wise Or Country and Order Wise Page">
								<p><a href="#" onClick="generate_trim_report('<? echo $reporAction; ?>','<? echo $row[csf('booking_no')]; ?>',<? echo $cbo_company_name; ?>,<? echo $row[csf('is_approved')]; ?>,<? echo $row[csf('is_short')]; ?>,<? echo $report; ?>)"><? echo $row[csf('booking_no')]; ?></a></p>
							</td>

							<td align="center">
								<p><? echo change_date_format($row[csf('booking_date')]); ?></p>
							</td>
							<td>
								<p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p>
							</td>
							<td align="right"><? echo fn_number_format($row[csf('wo_qnty')], 2, ".", "");
												$tot_wo_qnty += $row[csf('wo_qnty')]; ?></td>
							<td align="right"><? echo fn_number_format($wo_rate, 4, ".", "") ?></td>
							<td align="right"><? echo fn_number_format($row[csf('wo_amount')], 2, ".", "");
												$tot_wo_amount += $row[csf('wo_amount')]; ?></td>
						</tr>
					<?
						$tot_qty += $row[csf('wo_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">Total</td>
						<td align="right"><? echo fn_number_format($tot_wo_qnty, 2); ?></td>
						<td align="right">&nbsp;</td>
						<td align="right"><? echo fn_number_format($tot_wo_amount, 2); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
		<div style="display:none" id="data_panel"></div>
	</fieldset>
<?
	exit();
}

if ($action == "booking_inhouse_info") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<th width="80">Prod. ID</th>
					<th width="100">Recv. ID</th>
					<th width="100">Chalan No</th>
					<th width="100">Recv. Date</th>
					<th width="80">Item Description.</th>
					<th width="80">Recv. Qty.</th>
					<th width="80">Reject Qty.</th>
				</thead>
				<tbody>
					<?
					$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
					$i = 1;

					$receive_rtn_data = array();
					$receive_rtn_qty_data = sql_select("select a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'  group by a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id order by c.item_group_id");

					foreach ($receive_rtn_qty_data as $row) {
						$receive_rtn_data[$row[csf('id')]][issue_number] = $row[csf('issue_number')];
						$receive_rtn_data[$row[csf('id')]][issue_date] = $row[csf('issue_date')];
						$receive_rtn_data[$row[csf('id')]][quantity] = $row[csf('quantity')];
					}

					$receive_qty_data = "select a.id, c.po_breakdown_id,b.item_group_id,b.prod_id as prod_id,a.challan_no,b.item_description, a.recv_number, a.receive_date, SUM(c.quantity) as quantity, sum(reject_receive_qnty) as reject_receive_qnty
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c , product_details_master d
					where a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and b.id=c.dtls_id and b.prod_id=d.id and b.trans_id=c.trans_id and c.trans_type=1 and  c.po_breakdown_id in($po_id)  and b.item_group_id='$item_name' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  c.po_breakdown_id,b.item_group_id,b.prod_id,a.id,b.item_description, a.recv_number,a.challan_no, a.receive_date";

					$dtlsArray = sql_select($receive_qty_data);

					foreach ($dtlsArray as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

					?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30">
								<p><? echo $i; ?></p>
							</td>
							<td width="80">
								<p><? echo $row[csf('prod_id')]; ?></p>
							</td>
							<td width="100" align="center">
								<p><? echo $row[csf('recv_number')]; ?></p>
							</td>
							<td width="100" align="center">
								<p><? echo $row[csf('challan_no')]; ?></p>
							</td>
							<td width="100" align="center">
								<p><? echo  change_date_format($row[csf('receive_date')]); ?></p>
							</td>
							<td width="80" align="center">
								<p><? echo $row[csf('item_description')]; ?></p>
							</td>
							<td width="80" align="right">
								<p><? echo fn_number_format($row[csf('quantity')], 2); ?></p>
							</td>
							<td width="80" align="right">
								<p><? echo fn_number_format($row[csf('reject_receive_qnty')], 2); ?></p>
							</td>
						</tr>
					<?
						$tot_qty += $row[csf('quantity')];
						$tot_rej_qty += $row[csf('reject_receive_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right"></td>
						<td align="right">Total</td>
						<td><? echo fn_number_format($tot_qty, 2); ?></td>
						<td><? echo fn_number_format($tot_rej_qty, 2); ?></td>
					</tr>
				</tfoot>
			</table>

			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<th width="80">Prod. ID</th>
					<th width="100">Return. ID</th>
					<th width="100">Chalan No</th>
					<th width="100">Return Date</th>
					<th width="80">Item Description.</th>
					<th width="160">Return Qty.</th>
				</thead>
				<tbody>
					<?
					$receive_rtn_qty_data = sql_select("select a.issue_number, a.issue_date , d.po_breakdown_id, c.item_group_id, d.quantity as quantity, b.prod_id, c.item_description
					from inv_issue_master a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and b.transaction_type=3 and d.trans_type=3 and a.entry_form=49 and d.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'");


					foreach ($receive_rtn_qty_data as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$qty = 0;
						$qty = $row[csf('quantity')];
						//$qty=$row[csf('quantity')]/$item_arr[$row[csf('item_group_id')]]['order_uom'];
					?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30">
								<p><? echo $i; ?></p>
							</td>
							<td width="80">
								<p><? echo $row[csf('prod_id')]; ?></p>
							</td>
							<td width="100" align="center">
								<p><? echo $row[csf('issue_number')]; ?></p>
							</td>
							<td width="100" align="center">
								<p><? echo $row[csf('challan_no')]; ?></p>
							</td>
							<td width="100" align="center">
								<p><? echo  change_date_format($row[csf('issue_date')]); ?></p>
							</td>
							<td width="80" align="center">
								<p><? echo $row[csf('item_description')]; ?></p>
							</td>
							<td width="160" align="right">
								<p><? echo fn_number_format($qty, 2); ?></p>
							</td>
						</tr>
					<?
						$tot_rtn_qty += $qty;
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right"></td>
						<td align="right">Total</td>
						<td><? echo fn_number_format($tot_rtn_qty, 2); ?></td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="5" align="right"></td>
						<td align="right">Balance</td>
						<td><? echo fn_number_format($tot_qty - $tot_rtn_qty, 2); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
<?
	exit();
}

if ($action == "booking_issue_info_dtls") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>

	<script>
		function print_window() {
			//$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_div').innerHTML + '</body</html>');
			d.close();
			//$("#table_body_popup tr:first").show();
		}

		function mrr_report(company_id, mrr_id, mrr_no) {
			var report_title = "Trims Issue Multi Ref";
			//print_report( company_id+'*'+mrr_id+'*'+report_title, "trims_receive_entry_print", "../../../inventory/trims_store/requires/trims_receive_multi_ref_entry_v2_controller" );
			print_report(company_id + '*' + mrr_id + '*' + mrr_no + '*' + report_title, 'trims_issue_entry_print', "../../../inventory/trims_store/requires/trims_issue_multi_ref_controller");
			return;
		}
	</script>
	<!--<div style="width:670px" align="center"><input type="button" value="Print Preview" onClick="new_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:880px; margin-left:3px" id="view_part">
		<div style="width:width:1070px;" align="center">
			<input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton" />
		</div>
		<div id="report_div" align="center">
			<table border="1" class="rpt_table" rules="all" width="880" cellpadding="0" cellspacing="0" align="center">
				<caption>Issue Details </caption>
				<thead>
					<th width="30">Sl</th>
					<th width="110">Issue. No</th>
					<th width="100">Challan No</th>
					<th width="75">Issue. Date</th>
					<th width="140">Item Description.</th>
					<th width="80">Item Color</th>
					<th width="50">Item Size</th>
					<th width="80">Issue. Qty.</th>
					<th width="80">Floor No</th>
					<th>Line No</th>
				</thead>
				<tbody>
					<?
					$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
					$line_arr = return_library_array("select id, line_name from lib_sewing_line", "id", "line_name");
					$color_name_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
					$size_name_library = return_library_array("select id, size_name from lib_size", "id", "size_name");

					$i = 1;

					$mrr_sql = ("select a.id, a.company_id, a.issue_number, a.challan_no, a.issue_date, b.floor_id, b.sewing_line, b.item_color_id,b.item_size,p.id as prod_id, p.product_name_details, sum(c.quantity) as quantity
					from  inv_issue_master a, inv_trims_issue_dtls b, order_wise_pro_details c, product_details_master p
					where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=c.trans_id and p.id=b.prod_id and p.id=c.prod_id and c.trans_type=2 and a.entry_form=25 and c.entry_form=25 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.id in($tr_id)
					group by a.id, a.company_id, a.issue_number, a.challan_no, a.issue_date, b.floor_id, b.sewing_line,b.item_color_id,b.item_size, p.id, p.product_name_details order by p.id ");
					//echo $mrr_sql;
					$dtlsArray = sql_select($mrr_sql);

					foreach ($dtlsArray as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						//$color_name_library[ $size_name_library[
					?>

						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td align="center">
								<p><? echo $i; ?></p>
							</td>
							<td>
								<p><a href="#" onClick="mrr_report(<? echo $row[csf('company_id')]; ?>,<? echo $row[csf('id')]; ?>,'<? echo $row[csf('issue_number')]; ?>')"><? echo $row[csf('issue_number')]; ?></a></p>
							</td>
							<td align="center">
								<p><? echo $row[csf('challan_no')]; ?></p>
							</td>
							<td align="center">
								<p><? echo  change_date_format($row[csf('issue_date')]); ?></p>
							</td>
							<td align="center">
								<p><? echo $row[csf('product_name_details')]; ?></p>
							</td>
							<td align="center">
								<p><? echo $color_name_library[$row[csf('item_color_id')]]; ?></p>
							</td>
							<td align="center">
								<p><? echo $row[csf('item_size')]; ?></p>
							</td>
							<td align="right"><? echo fn_number_format($row[csf('quantity')], 2);
												$tot_issue_quantity += $row[csf('quantity')]; ?></td>
							<td align="center">
								<p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p>
							</td>
							<td align="center">
								<p><? echo $line_arr[$row[csf('sewing_line')]]; ?></p>
							</td>
						</tr>
					<?
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="7" align="right">Total</td>
						<td align="right"><? echo fn_number_format($tot_issue_quantity, 2); ?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			$issue_rtn_qty_data = sql_select("select a.recv_number, a.receive_date, c.id as prod_id, c.product_name_details, sum(d.quantity) as quantity
					from inv_receive_master a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and b.transaction_type=4 and d.trans_type=4 and a.entry_form=73 and d.entry_form=73 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.id in($tr_id)
					group by a.recv_number, a.receive_date, c.id, c.product_name_details");
			if (count($issue_rtn_qty_data) > 0) {
			?>
				<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
					<caption>Issue Return Details </caption>
					<thead>
						<th width="30">Sl</th>
						<th width="110">Issue. No</th>
						<th width="75">Issue. Date</th>
						<th width="140">Item Description.</th>
						<th width="80">Issue. Qty.</th>
						<th width="80">Floor No</th>
						<th>Line No</th>
					</thead>
					<tbody>
						<?


						foreach ($issue_rtn_qty_data as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
						?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td align="center">
									<p><? echo $i; ?></p>
								</td>
								<td>
									<p><? echo $row[csf('recv_number')]; ?></p>
								</td>
								<td align="center">
									<p><? echo  change_date_format($row[csf('receive_date')]); ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('product_name_details')]; ?></p>
								</td>
								<td align="right"><? echo fn_number_format($row[csf('quantity')], 2);
													$tot_issueRtn_quantity += $row[csf('quantity')]; ?></td>
								<td align="center">
									<p><? //echo $floor_arr[$row[csf('floor_id')]]; 
										?></p>
								</td>
								<td align="center">
									<p><? //echo $line_arr[$row[csf('sewing_line')]]; 
										?></p>
								</td>
							</tr>
						<?
							$tot_rtn_qty += $row[csf('quantity')];
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="4" align="right">Total</td>
							<td align="right"><? echo fn_number_format($tot_issueRtn_quantity, 2); ?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>


					</tfoot>
				</table>
			<?
			}
			?>
			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">
				</tfoot>
				<tr class="tbl_bottom">
					<td colspan="4" align="right">Balance</td>
					<td align="right"><? echo fn_number_format($tot_issue_quantity - $tot_issueRtn_quantity, 2); ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
<?
	exit();
}

if ($action == "booking_info") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

?>

	<script>
		function generate_trim_report(action, txt_booking_no, cbo_company_name, id_approved_id, cbo_isshort, template_id) {
			var show_comment = '';
			var r = confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
			if (r == true) show_comment = "1";
			else show_comment = "0";

			if (template_id != 67) {
				var data = "action=" + action + '&report_title=Country and Order Wise Trims Booking&show_comment=' + show_comment + '&txt_booking_no=' + "'" + txt_booking_no + "'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_isshort=' + cbo_isshort + '&link=1';
				//freeze_window(5);
				http.open("POST", "../../woven_order/requires/trims_booking_controller.php", true);
			} else {
				var data = "action=" + action + '&report_title=Country and Order Wise Trims Booking&show_comment=' + show_comment + '&txt_booking_no=' + "'" + txt_booking_no + "'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&report_type=1&link=1';
				http.open("POST", "../../woven_order/requires/trims_booking_multi_job_controllerurmi.php", true);
			}
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;

		}

		function generate_trim_report_reponse() {
			if (http.readyState == 4) {
				$('#data_panel').html(http.responseText);
				var w = window.open("Surprise", "../_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
					'<html><head><title></title></head><body>' + document.getElementById('data_panel').innerHTML + '</body</html>');
				d.close();
			}
		}
	</script>




	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td align="center" colspan="8"><strong> WO Summary</strong> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<th width="100">Wo No</th>
					<th width="75">Wo Date</th>
					<th width="100">Country</th>
					<th width="200">Item Description</th>
					<th width="80">Wo Qty</th>
					<th width="60">UOM</th>
					<th width="100">Supplier</th>
				</thead>
				<tbody>
					<?

					$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
					$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

					$conversion_factor_array = array();

					$conversion_factor = sql_select("select id ,conversion_factor from  lib_item_group ");
					foreach ($conversion_factor as $row_f) {
						$conversion_factor_array[$row_f[csf('id')]]['con_factor'] = $row_f[csf('conversion_factor')];
					}
					unset($conversion_factor);

					$i = 1;
					$country_arr_data = array();
					$sql_data = sql_select("select c.country_id,c.po_break_down_id,c.job_no_mst from wo_po_color_size_breakdown c  where c.po_break_down_id in($po_id) and c.status_active=1 and c.is_deleted=0 group by c.country_id,c.po_break_down_id,c.job_no_mst  ");
					foreach ($sql_data as $row_c) {
						$country_arr_data[$row_c[csf('po_break_down_id')]][$row_c[csf('job_no_mst')]]['country'] = $row_c[csf('country_id')];
					}
					unset($sql_data);

					$item_description_arr = array();
					$wo_sql_trim = sql_select("select b.id,b.item_color,b.job_no, b.po_break_down_id, b.description,b.brand_supplier,b.item_size from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id and a.job_no=b.job_no  and a.pre_cost_fabric_cost_dtls_id=$trim_dtla_id and a.is_deleted=0 and a.status_active=1 and b.po_break_down_id in($po_id)  group by b.id,b.po_break_down_id,b.job_no,b.description,b.brand_supplier,b.item_size,b.item_color");

					foreach ($wo_sql_trim as $row_trim) {
						$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]][$trim_dtla_id]['description'] = $row_trim[csf('description')];
					}
					unset($wo_sql_trim);

					$boking_cond = "";
					$booking_no = explode(',', $book_num);
					foreach ($booking_no as $book_row) {
						if ($boking_cond == "") $boking_cond = "and a.booking_no in('$book_row'";
						else  $boking_cond .= ",'$book_row'";
					}
					if ($boking_cond != "") $boking_cond .= ")";
					/*$wo_sql="select max(a.is_short)as is_short,max(a.is_approved) as is_approved,a.booking_no, a.booking_date, a.supplier_id,b.job_no,b.country_id_string, b.po_break_down_id,sum(b.wo_qnty) as wo_qnty,b.uom from wo_booking_mst a, wo_booking_dtls b
					where  a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1
					and b.status_active=1 and b.is_deleted=0 and  b.job_no='$job_no' and b.trim_group=$item_name and b.po_break_down_id in($po_id) and b.pre_cost_fabric_cost_dtls_id=$trim_dtla_id $boking_cond group by  b.po_break_down_id,b.job_no,
					a.booking_no, a.booking_date, a.supplier_id,b.uom,b.country_id_string";*/
					$wo_sql = "select max(a.is_short)as is_short,max(a.is_approved) as is_approved,a.booking_no, a.booking_date, a.supplier_id, a.pay_mode, b.job_no,b.country_id_string, b.po_break_down_id,sum(b.wo_qnty) as wo_qnty,b.uom from wo_booking_mst a, wo_booking_dtls b
					where  a.booking_no=b.booking_no and a.item_category=4 
					and b.status_active=1 and b.is_deleted=0 and  b.job_no='$job_no' and b.trim_group=$item_name and b.po_break_down_id in($po_id) and b.pre_cost_fabric_cost_dtls_id in($trim_dtla_id,0)  and a.is_deleted=0 and a.status_active=1  $boking_cond group by  b.po_break_down_id,b.job_no,
					a.booking_no, a.booking_date, a.supplier_id, a.pay_mode, b.uom,b.country_id_string"; /*As Item from precost No,so 0 is added after $trim_dtla_id and it is set to in() function */
					$dtlsArray = sql_select($wo_sql);



					$print_report_format = return_field_value("format_id", "lib_report_template", "template_name ='" . $cbo_company_name . "' and module_id=2 and report_id in(5,6) and is_deleted=0 and status_active=1");
					//echo 	$print_report_format;
					$print2 = return_field_value("format_id", "lib_report_template", "template_name ='" . $cbo_company_name . "' and module_id=2 and report_id in(26) and is_deleted=0 and status_active=1");

					$report = max(explode(',', $print_report_format));
					if ($print2 == 67) $report = 67;
					else if ($print2 == 183) $report = 183;

					if ($report == 13) {
						$reporAction = "show_trim_booking_report";
					} elseif ($report == 14) {
						$reporAction = "show_trim_booking_report1";
					} elseif ($report == 15) {
						$reporAction = "show_trim_booking_report2";
					} elseif ($report == 16) {
						$reporAction = "show_trim_booking_report3";
					} elseif ($report == 67) {
						$reporAction = "show_trim_booking_report2";
					} elseif ($report == 183) {
						$reporAction = "show_trim_booking_report3";
					}


					foreach ($dtlsArray as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$description = $item_description_arr[$row[csf('po_break_down_id')]][$row[csf('job_no')]][$trim_dtla_id]['description'];
						$conversion_factor_rate = $conversion_factor_array[$item_name]['con_factor'];
						$country_arr_data = explode(',', $row[csf('country_id_string')]);
						$country_name_data = "";
						foreach ($country_arr_data as $country_row) {
							if ($country_name_data == "") $country_name_data = $country_name_library[$country_row];
							else $country_name_data .= "," . $country_name_library[$country_row];
						}

						$supplier_name_str = "";
						if ($row[csf('pay_mode')] == 3 || $row[csf('pay_mode')] == 5) $supplier_name_str = $company_arr[$row[csf('supplier_id')]];
						else $supplier_name_str = $supplier_arr[$row[csf('supplier_id')]];
					?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30">
								<p><? echo $i; ?></p>
							</td>
							<td width="100" title="Multiple Order Wise Or Country and Order Wise Page">
								<p><a href="#" onClick="generate_trim_report('<? echo $reporAction; ?>','<? echo $row[csf('booking_no')]; ?>',<? echo $cbo_company_name; ?>,<? echo $row[csf('is_approved')]; ?>,<? echo $row[csf('is_short')]; ?>,<? echo $report; ?>)"><? echo $row[csf('booking_no')]; ?></a></p>
							</td>
							<td width="75">
								<p><? echo change_date_format($row[csf('booking_date')]); ?></p>
							</td>
							<td width="100">
								<p><? echo $country_name_data; ?></p>
							</td>
							<td width="200">
								<p><? echo $description; ?></p>
							</td>
							<td width="80" align="right" title="<? //echo 'conversion_factor='.$conversion_factor_rate; 
																?>">
								<p><? echo fn_number_format($row[csf('wo_qnty')], 2); ?></p>
							</td>
							<td width="60" align="center">
								<p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p>
							</td>
							<td width="100">
								<p><? echo $supplier_name_str; ?></p>
							</td>
						</tr>
					<?
						$tot_qty += $row[csf('wo_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right"><? echo fn_number_format($tot_qty, 2); ?></td>
						<td align="right">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
		<div style="display:none" id="data_panel"></div>
	</fieldset>
<?
	exit();
}

if ($action == "booking_inhouse_info_dtls") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	//echo "DSDD";die;
?>
	<script>
		function print_window() {
			//$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_div').innerHTML + '</body</html>');
			d.close();
			//$("#table_body_popup tr:first").show();
		}

		function mrr_report(company_id, mrr_id) {
			var report_title = "Trims Receive Entry Multi Ref.v2";
			print_report(company_id + '*' + mrr_id + '*' + report_title, "trims_receive_entry_print", "../../../inventory/trims_store/requires/trims_receive_multi_ref_entry_v2_controller");
		}
	</script>

	<!--<div style="width:770px" align="center"><input type="button" value="Print Preview" onClick="new_window()" style="width:100px"  class="formbutton"/></div> id="scroll_body"-->
	<fieldset style="width:1070px; margin-left:3px" id="view_part">
		<div style="width:width:1070px;" align="center">
			<input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton" />
		</div>
		<div id="report_div" align="center">
			<table border="1" class="rpt_table" rules="all" width="1070" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td align="center" colspan="12"><strong> Receive Summary</strong> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1070" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<th width="110">MRR No.</th>
					<th width="100">Challan No.</th>
					<th width="75">MRR Date</th>
					<th width="130">Supplier</th>
					<th width="140">Description</th>
					<th width="70">Gmts. Color</th>
					<th width="50">Gmts. Size</th>
					<th width="70">Item Color</th>
					<th width="50">Item Size</th>
					<th width="80">MRR Qty.</th>
					<th width="80">Rate</th>
					<th>Amount</th>
				</thead>
				<tbody>
					<?
					$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
					$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
					$size_library = return_library_array("select id, size_name from lib_size", "id", "size_name");
					$i = 1;

					$receive_qty_data = "select a.id, a.company_id, a.recv_number,a.challan_no, a.receive_date, a.supplier_id, d.id as prod_id, d.product_name_details, d.color as gmt_color, d.gmts_size, d.item_color, d.item_size, sum(c.quantity) as mrr_quantity, sum(c.quantity*b.order_rate) as mrr_amount
				from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d
				where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and b.prod_id=d.id and a.entry_form=24 and c.entry_form=24 and b.item_category=4 and b.transaction_type=1 and c.trans_type=1 and  c.id in($tr_id) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				group by a.id, a.company_id, a.recv_number,a.challan_no, a.receive_date, a.supplier_id, d.id, d.product_name_details, d.color, d.gmts_size, d.item_color, d.item_size order by d.id";
					//echo $receive_qty_data;
					$dtlsArray = sql_select($receive_qty_data);
					$prod_wise_ord_rate = array();
					foreach ($dtlsArray as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$mrr_rate = $row[csf('mrr_amount')] / $row[csf('mrr_quantity')];
						$prod_wise_ord_rate[$row[csf('prod_id')]] = $mrr_rate;
					?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td align="center"><? echo $i; ?></td>
							<td>
								<p><a href="#" onClick="mrr_report(<? echo $row[csf('company_id')]; ?>,<? echo $row[csf('id')]; ?>)"><? echo $row[csf('recv_number')]; ?></a></p>
							</td>
							<td>
								<p><? echo  $row[csf('challan_no')]; ?></p>
							</td>
							<td align="center">
								<p><? echo  change_date_format($row[csf('receive_date')]); ?></p>
							</td>

							<td>
								<p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p>
							</td>
							<td>
								<p><? echo $row[csf('product_name_details')]; ?></p>
							</td>
							<td>
								<p><? echo $color_library[$row[csf('gmt_color')]]; ?></p>
							</td>
							<td>
								<p><? echo $size_library[$row[csf('gmts_size')]]; ?></p>
							</td>
							<td>
								<p><? echo $color_library[$row[csf('item_color')]]; ?></p>
							</td>
							<td>
								<p><? echo $row[csf('item_size')]; ?></p>
							</td>
							<td align="right">
								<p><? echo fn_number_format($row[csf('mrr_quantity')], 2);
									$tot_mrr_quantity += $row[csf('mrr_quantity')]; ?></p>
							</td>
							<td align="right">
								<p><? echo fn_number_format($mrr_rate, 4); ?></p>
							</td>
							<td align="right">
								<p><? echo fn_number_format($row[csf('mrr_amount')], 2);
									$tot_mrr_amount += $row[csf('mrr_amount')]; ?></p>
							</td>
						</tr>
					<?

						$tot_rej_qty += $row[csf('reject_receive_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="10" align="right">Total</td>
						<td><? echo fn_number_format($tot_mrr_quantity, 2); ?></td>
						<td align="right">&nbsp;</td>
						<td><? echo fn_number_format($tot_mrr_amount, 2); ?></td>
					</tr>
				</tfoot>
			</table>
			<?
			$receive_rtn_qty_data = sql_select("select a.issue_number, a.issue_date, a.supplier_id, d.id as prod_id, d.product_name_details, d.color as gmt_color, d.gmts_size, d.item_color, d.item_size, sum(c.quantity) as quantity
					from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and b.prod_id=d.id and b.transaction_type=3 and c.trans_type=3 and a.entry_form=49 and c.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.id in($tr_id)
					group by a.issue_number, a.issue_date, a.supplier_id, d.id, d.product_name_details, d.color, d.gmts_size, d.item_color, d.item_size");

			if (count($receive_rtn_qty_data) > 0) {

			?>
				<table border="1" class="rpt_table" rules="all" width="1070" cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td align="center" colspan="11"><strong> Receive Return Summary</strong> </td>
					</tr>
				</table>
				<table border="1" class="rpt_table" rules="all" width="1070" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<th width="30">Sl</th>
						<th width="110">MRR No.</th>

						<th width="75">MRR Date</th>
						<th width="130">Supplier</th>
						<th width="140">Description</th>
						<th width="70">Gmts. Color</th>
						<th width="50">Gmts. Size</th>
						<th width="70">Item Color</th>
						<th width="50">Item Size</th>
						<th width="80">MRR Qty.</th>
						<th width="80">Rate</th>
						<th>Amount</th>
					</thead>
					<tbody>
						<?


						foreach ($receive_rtn_qty_data as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$ord_rate = $prod_wise_ord_rate[$row[csf('prod_id')]];
							$ord_amount = $row[csf('quantity')] * $ord_rate;
						?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td align="center">
									<p><? echo $i; ?></p>
								</td>
								<td>
									<p><? echo $row[csf('issue_number')]; ?></p>
								</td>

								<td align="center">
									<p><? echo change_date_format($row[csf('issue_date')]); ?></p>
								</td>
								<td>
									<p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p>
								</td>
								<td>
									<p><? echo $row[csf('product_name_details')]; ?></p>
								</td>
								<td>
									<p><? echo $color_library[$row[csf('gmt_color')]]; ?></p>
								</td>
								<td>
									<p><? echo $size_library[$row[csf('gmts_size')]]; ?></p>
								</td>
								<td>
									<p><? echo $color_library[$row[csf('item_color')]]; ?></p>
								</td>
								<td>
									<p><? echo $row[csf('item_size')]; ?></p>
								</td>
								<td align="right">
									<p><? echo fn_number_format($row[csf('quantity')], 2);
										$tot_quantity += $row[csf('quantity')]; ?></p>
								</td>
								<td align="right">
									<p><? echo fn_number_format($ord_rate, 2); ?></p>
								</td>
								<td align="right">
									<p><? echo fn_number_format($ord_amount, 2);
										$tot_amount += $ord_amount; ?></p>
								</td>
							</tr>
						<?
							$tot_rtn_qty += $qty;
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="9" align="right">Total</td>
							<td align="right"><? echo fn_number_format($tot_quantity, 2); ?></td>
							<td align="right">&nbsp;</td>
							<td align="right"><? echo fn_number_format($tot_amount, 2); ?></td>
						</tr>

					</tfoot>
				</table>
			<?
			}
			?>
			<table border="1" class="rpt_table" rules="all" width="1070" cellpadding="0" cellspacing="0" align="center">
				<tfoot>
					<tr>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Balance:</td>
						<td><? echo fn_number_format($tot_mrr_quantity - $tot_quantity, 2); ?></td>
						<td align="right">&nbsp;</td>
						<td><? echo fn_number_format($tot_mrr_amount - $tot_amount, 2); ?></td>
					</tr>
					<tfoot>
						</tr>
			</table>
		</div>
	</fieldset>
<?
	exit();
}

if ($action == "booking_inhouse_info") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:880px;">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="880" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<th width="50">Prod. ID</th>
					<th width="110">Recv. ID</th>
					<th width="80">Chalan No</th>
					<th width="70">Recv. Date</th>
					<th width="230">Item Description.</th>
					<th width="100">Color</th>
					<th width="80">Recv. Qty.</th>
					<th>Reject Qty.</th>
				</thead>
				<tbody>
					<?
					$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
					$color_name_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
					$i = 1;

					$receive_rtn_data = array();
					$receive_rtn_qty_data = sql_select("select a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'  group by a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id order by c.item_group_id");

					foreach ($receive_rtn_qty_data as $row) {
						$receive_rtn_data[$row[csf('id')]][issue_number] = $row[csf('issue_number')];
						$receive_rtn_data[$row[csf('id')]][issue_date] = $row[csf('issue_date')];
						$receive_rtn_data[$row[csf('id')]][quantity] = $row[csf('quantity')];
					}

					$receive_qty_data = "SELECT a.id, c.po_breakdown_id,b.item_group_id,b.prod_id as prod_id,a.challan_no,b.item_description, a.recv_number, a.receive_date, SUM(c.quantity) as quantity, sum(reject_receive_qnty) as reject_receive_qnty, d.item_color
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c , product_details_master d
					where a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and b.id=c.dtls_id and b.prod_id=d.id and b.trans_id=c.trans_id and c.trans_type=1  and c.prod_id=d.id and  c.po_breakdown_id in($po_id)  and b.item_group_id='$item_name' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0
					group by  c.po_breakdown_id, b.item_group_id, b.prod_id, a.id, b.item_description, a.recv_number,a.challan_no, a.receive_date, d.item_color";
					//echo $receive_qty_data;
					$dtlsArray = sql_select($receive_qty_data);

					foreach ($dtlsArray as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

					?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td align="center">
								<p><? echo $i; ?></p>
							</td>
							<td align="center">
								<p><? echo $row[csf('prod_id')]; ?></p>
							</td>
							<td align="center">
								<p><? echo $row[csf('recv_number')]; ?></p>
							</td>
							<td align="center">
								<p><? echo $row[csf('challan_no')]; ?></p>
							</td>
							<td align="center">
								<p><? echo  change_date_format($row[csf('receive_date')]); ?></p>
							</td>
							<td>
								<p><? echo $row[csf('item_description')]; ?></p>
							</td>
							<td title="<? echo $row[csf('item_color')]; ?>">
								<p><? echo $color_name_library[$row[csf('item_color')]]; ?></p>
							</td>
							<td align="right">
								<p><? echo fn_number_format($row[csf('quantity')], 2); ?></p>
							</td>
							<td align="right">
								<p><? echo fn_number_format($row[csf('reject_receive_qnty')], 2); ?></p>
							</td>
						</tr>
					<?
						$tot_qty += $row[csf('quantity')];
						$tot_rej_qty += $row[csf('reject_receive_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="6" align="right"></td>
						<td align="right">Total</td>
						<td><? echo fn_number_format($tot_qty, 2); ?></td>
						<td><? echo fn_number_format($tot_rej_qty, 2); ?></td>
					</tr>
				</tfoot>
			</table>
			<table border="1" class="rpt_table" rules="all" width="880" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<th width="50">Prod. ID</th>
					<th width="110">Return. ID</th>
					<th width="80">Chalan No</th>
					<th width="70">Return Date</th>
					<th width="230">Item Description.</th>
					<th width="100">Color</th>
					<th>Return Qty</th>
				</thead>
				<tbody>
					<?
					$dtlsArray = sql_select($receive_qty_data);

					foreach ($dtlsArray as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						if ($receive_rtn_data[$row[csf('id')]][quantity] > 0) {
					?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td align="center">
									<p><? echo $i; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('prod_id')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $receive_rtn_data[$row[csf('id')]][issue_number]; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('challan_no')]; ?></p>
								</td>
								<td width="100" align="center">
									<p><? echo  change_date_format($receive_rtn_data[$row[csf('id')]][issue_date]); ?></p>
								</td>
								<td>
									<p><? echo $row[csf('item_description')]; ?></p>
								</td>
								<td title="<? echo $row[csf('item_color')]; ?>">
									<p><? echo $color_name_library[$row[csf('item_color')]]; ?></p>
								</td>
								<td align="right">
									<p><? echo fn_number_format($receive_rtn_data[$row[csf('id')]][quantity], 2); ?></p>
								</td>
							</tr>
					<?
							$tot_rtn_qty += $receive_rtn_data[$row[csf('id')]][quantity];
							$i++;
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="6" align="right"></td>
						<td align="right">Total</td>
						<td><? echo fn_number_format($tot_rtn_qty, 2); ?></td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="6" align="right"></td>
						<td align="right">Balance</td>
						<td><? echo fn_number_format($tot_qty - $tot_rtn_qty, 2); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
<?
	disconnect($con);
	exit();
}

if ($action == "booking_inhouse_value_info") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<th width="50">Prod. ID</th>
					<th width="90">Recv. ID</th>
					<th width="100">WO/PI No</th>
					<th width="80">Recv. Date</th>
					<th width="150">Item Description.</th>
					<th width="80">Recv. Qty.</th>
					<th width="80">Rate</th>
					<th width="80">Amount</th>
					<th>Reject Qty.</th>
				</thead>
				<tbody>
					<?
					$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
					$item_library = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
					$i = 1;

					$receive_rtn_data = array();
					//$receive_rtn_qty_data=sql_select("select a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'  group by a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id order by c.item_group_id");
					$receive_rtn_qty_data = sql_select("select a.issue_number,a.issue_date,d.po_breakdown_id,c.id as prod_id,c.product_name_details, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id  and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'  group by a.issue_number,a.issue_date,d.po_breakdown_id,c.id,c.product_name_details, c.item_group_id order by c.item_group_id");
					//echo "select a.issue_number,a.issue_date,d.po_breakdown_id,c.id as prod_id,c.item_description, c.item_group_id,sum(d.quantity) as quantity,sum(b.rcv_amount) as rcv_amount   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id  and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'  group by a.issue_number,a.issue_date,d.po_breakdown_id,c.id,c.item_description, c.item_group_id order by c.item_group_id";


					foreach ($receive_rtn_qty_data as $row) {
						$receive_rtn_data[$row[csf('id')]][issue_number] = $row[csf('issue_number')];
						$receive_rtn_data[$row[csf('id')]][issue_date] = $row[csf('issue_date')];
						$receive_rtn_data[$row[csf('id')]][quantity] = $row[csf('quantity')];
					}

					$receive_qty_data = "select a.id, c.po_breakdown_id,b.item_group_id,b.prod_id as prod_id,a.challan_no,b.item_description, a.recv_number, a.receive_date, SUM(c.quantity) as quantity, sum(reject_receive_qnty) as reject_receive_qnty,b.rate,e.po_number
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c , product_details_master d,wo_po_break_down e
					where e.id=c.po_breakdown_id and a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and b.id=c.dtls_id and b.prod_id=d.id and b.trans_id=c.trans_id and c.trans_type=1 and  c.po_breakdown_id in($po_id)  and b.item_group_id='$item_name' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  c.po_breakdown_id,b.item_group_id,b.prod_id,a.id,b.item_description, a.recv_number,a.challan_no, a.receive_date,b.rate,e.po_number";


					$dtlsArray = sql_select($receive_qty_data);

					foreach ($dtlsArray as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

					?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30">
								<p><? echo $i; ?></p>
							</td>
							<td width="50">
								<p><? echo $row[csf('prod_id')]; ?></p>
							</td>
							<td width="90" align="center">
								<p><? echo $row[csf('recv_number')]; ?></p>
							</td>
							<td width="100" align="center">
								<p><? echo $row[csf('po_number')]; ?></p>
							</td>
							<td width="80" align="center">
								<p><? echo  change_date_format($row[csf('receive_date')]); ?></p>
							</td>
							<td width="150" align="center">
								<p><? echo $row[csf('item_group_id')]; ?></p>
							</td>
							<td width="80" align="right">
								<p><? echo fn_number_format($row[csf('quantity')], 2); ?></p>
							</td>

							<td width="80" align="right">
								<p><? echo fn_number_format($row[csf('rate')], 2); ?></p>
							</td>
							<td width="80" align="right">
								<p><? echo fn_number_format($row[csf('quantity')] * $row[csf('rate')], 2); ?></p>
							</td>

							<td align="right">
								<p><? echo fn_number_format($row[csf('reject_receive_qnty')], 2); ?></p>
							</td>
						</tr>
					<?
						$tot_qty += $row[csf('quantity')];
						$tot_amount += $row[csf('quantity')] * $row[csf('rate')];
						$tot_rej_qty += $row[csf('reject_receive_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right"></td>
						<td align="right">Total</td>
						<td><? echo fn_number_format($tot_qty, 2); ?></td>
						<td></td>
						<td><? echo fn_number_format($tot_amount, 2); ?></td>
						<td><? echo fn_number_format($tot_rej_qty, 2); ?></td>
					</tr>
				</tfoot>
			</table>

			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<th width="80">Prod. ID</th>
					<th width="100">Return. ID</th>
					<th width="100">Chalan No</th>
					<th width="100">Return Date</th>
					<th width="80">Item Description.</th>
					<th width="160">Return Qty.</th>

				</thead>
				<tbody>
					<?
					// in rec return page not found recv rate,thats why amount miss match in rev return page.
					foreach ($receive_rtn_qty_data as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

					?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30">
								<p><? echo $i; ?></p>
							</td>
							<td width="80">
								<p><? echo $row[csf('prod_id')]; ?></p>
							</td>
							<td width="100" align="center">
								<p><? echo $row[csf('issue_number')]; ?></p>
							</td>
							<td width="100" align="center">
								<p><? echo $row[csf('challan_no')]; ?></p>
							</td>
							<td width="100" align="center">
								<p><? echo  change_date_format($row[csf('issue_date')]); ?></p>
							</td>
							<td width="80" align="center">
								<p><? echo $row[csf('product_name_details')]; ?></p>
							</td>
							<td width="160" align="right">
								<p><? echo fn_number_format($row[csf('quantity')], 2); ?></p>
							</td>

						</tr>
					<?
						$tot_rtn_qty += $row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right"></td>
						<td align="right">Total</td>
						<td><? echo fn_number_format($tot_rtn_qty, 2); ?></td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="5" align="right"></td>
						<td align="right">Qty Balance</td>
						<td><? echo fn_number_format($tot_qty - $tot_rtn_qty, 2); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
<?
	exit();
}

if ($action == "booking_issue_info") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<!--	<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<caption>Issue Details </caption>
				<thead>
					<th width="30">Sl</th>
					<th width="80">Prod. ID</th>
					<th width="100">Issue. ID</th>
					<th width="100">Chalan No</th>
					<th width="100">Issue. Date</th>
					<th width="80">Item Description.</th>
					<th width="100">Issue. Qty.</th>
				</thead>
				<tbody>
					<?
					$conversion_factor_array = array();
					$item_arr = array();
					$conversion_factor = sql_select("select id ,trim_uom,order_uom,conversion_factor from  lib_item_group  ");
					foreach ($conversion_factor as $row_f) {
						$conversion_factor_array[$row_f[csf('id')]]['con_factor'] = $row_f[csf('conversion_factor')];
						$conversion_factor_array[$row_f[csf('id')]]['cons_uom'] = $row_f[csf('trim_uom')];
						$item_arr[$row_f[csf('id')]]['order_uom'] = $row_f[csf('order_uom')];
					}
					$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
					$i = 1;
					//$wo_sql="select a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description,sum(a.cons_qnty) as cons_qnty  from inv_receive_master b, inv_trims_entry_dtls a where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 group by a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description";

					$mrr_sql = ("select a.id, a.issue_number,a.challan_no,b.prod_id,p.item_group_id, a.issue_date,b.item_description,SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p
					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and p.item_group_id='$item_name' group by c.po_breakdown_id,p.item_group_id,b.item_description,a.issue_number,a.id,a.issue_date,b.prod_id,a.challan_no ");

					$dtlsArray = sql_select($mrr_sql);

					//echo $dtlsArray.'DD';
					if (count($dtlsArray) <= 0) {
						$general_item_issue_sql = "select e.issue_number,e.challan_no,e.issue_date, b.prod_id,a.item_group_id as item_group_id, a.item_description as item_description, b.cons_quantity as quantity, b.cons_amount
			from product_details_master a, inv_transaction b, wo_po_break_down c, wo_po_details_master d,inv_issue_master e 
			where a.id=b.prod_id and b.order_id=c.id and c.job_no_mst=d.job_no and e.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.id in($po_id) and a.item_group_id='$item_name'";
						$dtlsArray = sql_select($general_item_issue_sql);
					}

					foreach ($dtlsArray as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$conv_fact = $conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];

					?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30">
								<p><? echo $i; ?></p>
							</td>
							<td width="80" align="center">
								<p><? echo $row[csf('prod_id')]; ?></p>
							</td>
							<td width="100">
								<p><? echo $row[csf('issue_number')]; ?></p>
							</td>
							<td width="100" align="center">
								<p><? echo $row[csf('challan_no')]; ?></p>
							</td>
							<td width="100" align="center">
								<p><? echo  change_date_format($row[csf('issue_date')]); ?></p>
							</td>
							<td width="80" align="center">
								<p><? echo $row[csf('item_description')]; ?></p>
							</td>
							<td width="100" align="right">
								<p><? echo fn_number_format($row[csf('quantity')] / $conv_fact, 2); ?></p>
							</td>
						</tr>
					<?
						$tot_qty += $row[csf('quantity')] / $conv_fact;
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right"></td>
						<td align="right">Total</td>
						<td><? echo fn_number_format($tot_qty, 2); ?></td>
					</tr>
				</tfoot>
			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<caption> Return Details</caption>
				<thead>
					<th width="30">Sl</th>
					<th width="80">Prod. ID</th>
					<th width="100">Return. ID</th>
					<th width="100">Chalan No</th>
					<th width="100">Return Date</th>
					<th width="80">Item Description.</th>
					<th width="100">Return. Qty.</th>
				</thead>
				<tbody>
					<?
					//$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$k = 1;
					$ret_tot_qty = 0;
					//$wo_sql="select a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description,sum(a.cons_qnty) as cons_qnty  from inv_receive_master b, inv_trims_entry_dtls a where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 group by a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description";

					$mrr_sql_ret = ("select a.id, a.recv_number,a.challan_no,b.prod_id, p.item_group_id,a.receive_date,p.item_description,SUM(c.quantity) as quantity
					from   inv_receive_master a,inv_transaction b, order_wise_pro_details c,product_details_master p
					where a.id=b.mst_id  and a.entry_form=73 and c.entry_form=73 and p.id=b.prod_id  and b.id=c.trans_id and b.transaction_type=4 and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and p.item_group_id='$item_name' group by c.po_breakdown_id,p.item_group_id,p.item_description,a.recv_number,a.challan_no,b.prod_id, a.receive_date,a.challan_no ");

					$dtlsArray_data = sql_select($mrr_sql_ret);

					foreach ($dtlsArray_data as $row) {
						if ($k % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$conv_fact = $conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];

					?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
							<td width="30">
								<p><? echo $k; ?></p>
							</td>
							<td width="80" align="center">
								<p><? echo $row[csf('prod_id')]; ?></p>
							</td>
							<td width="100">
								<p><? echo $row[csf('recv_number')]; ?></p>
							</td>
							<td width="100" align="center">
								<p><? echo $row[csf('challan_no')]; ?></p>
							</td>
							<td width="100" align="center">
								<p><? echo  change_date_format($row[csf('receive_date')]); ?></p>
							</td>
							<td width="80" align="center">
								<p><? echo $row[csf('item_description')]; ?></p>
							</td>
							<td width="100" align="right">
								<p><? echo fn_number_format($row[csf('quantity')] / $conv_fact, 2); ?></p>
							</td>
						</tr>
					<?
						$ret_tot_qty += $row[csf('quantity')] / $conv_fact;
						$k++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right"></td>
						<td align="right">Total</td>
						<td><? echo fn_number_format($ret_tot_qty, 2); ?></td>
					</tr>

					<tr class="tbl_bottom">
						<td colspan="5" align="right"></td>
						<td align="right">Total Balance</td>
						<td><? echo fn_number_format($tot_qty - $ret_tot_qty, 2); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
<?
	exit();
}

if ($action == "order_qty_data") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$buyer_short_name_library = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$country_name_library = return_library_array("select id, country_name from lib_country", "id", "country_name");
?>
	<!--	<div style="width:780px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
-->
	<fieldset style="width:770px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<th width="80">Buyer Name</th>
					<th width="100">Order No</th>
					<th width="100">Country</th>
					<th width="80">Order Qty.</th>

				</thead>
				<tbody>
					<?
					$i = 1;
					$order_arr = return_library_array("select id, po_number from wo_po_break_down where id in($po_id)", "id", "po_number");

					$gmt_item_id = return_field_value("item_number_id", "wo_po_color_size_breakdown", "po_break_down_id='$po_id'");
					$country_id = return_field_value("country_id", "wo_po_color_size_breakdown", "po_break_down_id='$po_id'");
					//echo $gmt_item_id;
					$sql_po_qty = sql_select("select sum(c.order_quantity) as order_quantity,c.country_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($po_id) and c.item_number_id=' $gmt_item_id' and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,c.country_id ");
					list($sql_po_qty_row) = $sql_po_qty;
					$po_qty = $sql_po_qty_row[csf('order_quantity')];

					//$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");



					$sql = " select sum( c.order_quantity) as po_quantity ,c.country_id,c.po_break_down_id from wo_po_color_size_breakdown c  where c.po_break_down_id in($po_id) and c.status_active=1 and c.is_deleted=0 group by c.country_id,c.po_break_down_id";

					$dtlsArray = sql_select($sql);

					foreach ($dtlsArray as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

					?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30">
								<p><? echo $i; ?></p>
							</td>
							<td width="80" align="center">
								<p><? echo $buyer_short_name_library[$buyer]; ?></p>
							</td>
							<td width="100">
								<p><? echo $order_arr[$row[csf('po_break_down_id')]]; ?></p>
							</td>
							<td width="100" align="center">
								<p><? echo $country_name_library[$row[csf('country_id')]]; ?></p>
							</td>
							<td width="80" align="right">
								<p><? echo fn_number_format($row[csf('po_quantity')], 2); ?></p>
							</td>

						</tr>
					<?
						$tot_qty += $row[csf('po_quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="3" align="right"></td>
						<td align="right">Total</td>
						<td><? echo fn_number_format($tot_qty, 2); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
<?
	exit();
}

if ($action == "order_req_qty_data") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$buyer_short_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$po_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");

?>
	<!--<div style="width:680px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton"/></div>-->
	<fieldset style="width:670px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<th width="80">Buyer Name</th>
					<th width="100">Order No</th>
					<th width="100">Item Description</th>
					<th width="100">Country</th>
					<th width="80">Req. Qty.</th>
					<th>Req. Rate</th>
				</thead>
				<tbody>
					<?
					//echo $po_id;
					$condition = new condition();
					$condition->job_no("='$job_no'");

					$condition->po_id("in($po_id)");

					if (str_replace("'", "", $start_date) != "" && str_replace("'", "", $end_date) != "") {
						$condition->country_ship_date(" between '$start_date' and '$end_date'");
					}

					$condition->init();
					$trim = new trims($condition);
					$trim_qty = $trim->getQtyArray_by_orderCountryAndPrecostdtlsid();

					$country_id_str = "";
					if ($start_date == "" && $end_date == "") $date_cond = "";
					else $date_cond = "and country_ship_date between '$start_date' and '$end_date'";
					$sql_color_size = "select id, country_id from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and job_no_mst='$job_no' and status_active=1 and is_deleted=0 $date_cond";
					$sql_color_size_res = sql_select($sql_color_size);
					foreach ($sql_color_size_res as $row) {
						if ($country_id_str == "") $country_id_str = $row[csf('id')];
						else $country_id_str .= ',' . $row[csf('id')];
					}
					$excountry_id = array_filter(array_unique(explode(",", $country_id_str)));
					if ($excountry_id != "") $country_idcond = "and c.color_size_table_id in ($excountry_id)";
					else $country_idcond = "";

					$sql = "select  b.id as trim_dtla_id, b.description, b.rate, b.amount,  c.cons, c.country_id, c.po_break_down_id, b.job_no
					from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b, wo_pre_cost_trim_co_cons_dtls c 
					where c.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and a.job_no='$job_no' and c.po_break_down_id in ($po_id) and b.id=$trim_dtla_id and c.cons>0  and b.is_deleted=0 and b.status_active=1
					group by  b.id, b.description, b.rate, b.amount,  c.cons, c.country_id, c.po_break_down_id, b.job_no order by b.trim_group";

					$dtlsArray = sql_select($sql);
					$pre_cost_data_arr = array();
					foreach ($dtlsArray as $row) {
						$excountry_id = array_unique(explode(",", $row[csf('country_id')]));
						$req_qty = 0;
						foreach ($excountry_id as $country_id) {
							//$req_qty=$trim_qty[$row[csf('po_break_down_id')]][$country_id][$row[csf('trim_dtla_id')]];
							$pre_cost_data_arr[$row[csf('po_break_down_id')]][$country_id][$row[csf('trim_dtla_id')]] = $req_qty;
						}
					}
					unset($dtlsArray);
					$i = 1;
					foreach ($pre_cost_data_arr as $po_id => $po_data) {
						foreach ($po_data as $country_id => $country_data) {
							foreach ($country_data as $description => $req_qty) {
								//if(in_array($country_id,$excountry_id))
								//{
								if ($i % 2 == 0) $bgcolor = "#E9F3FF";
								else $bgcolor = "#FFFFFF";
								//echo $po_id.'='.$country_id.'='.$description.', '; 
								$trim_req_qty = $trim_qty[$po_id][$country_id][$description];

					?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30">
										<p><? echo $i; ?></p>
									</td>
									<td width="80" align="center">
										<p><? echo $buyer_short_arr[$buyer]; ?></p>
									</td>
									<td width="100">
										<p><? echo $po_arr[$po_id]; ?></p>
									</td>
									<td width="100">
										<p><? //echo $description;//$description; 
											?></p>
									</td>
									<td width="100" align="center">
										<p><? echo $country_arr[$country_id]; ?></p>
									</td>
									<td width="80" align="right">
										<p><? echo fn_number_format($trim_req_qty, 2); ?></p>
									</td>
									<td align="right">
										<p><? echo fn_number_format($rate, 4); ?></p>
									</td>
								</tr>
					<?
								$tot_qty += $trim_req_qty;
								$i++;
								//}
							}
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td align="right">&nbsp;</td>
						<td align="right" colspan="4">Total</td>
						<td align="right"><? echo fn_number_format($tot_qty, 2); ?></td>
						<td>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
<?
	exit();
}
?>