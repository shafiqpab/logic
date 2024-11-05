<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.fabrics.php');
$user_id = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$colorname_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
$buyer_short_library = return_library_array("select id,buyer_name from lib_buyer", "id", "buyer_name");

if ($action == "load_drop_down_buyer") {

	echo create_drop_down("cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/daily_production_progress_report_controller',this.value, 'load_drop_down_brand', 'brand_td');load_drop_down( 'requires/daily_production_progress_report_controller',this.value, 'load_drop_down_buyer_season', 'buyer_season_td');");
}


if ($action == "print_report_button_setting") {

	$print_report_format = return_field_value("format_id", "lib_report_template", "template_name ='" . $data . "' and module_id=7 and report_id=59 and is_deleted=0 and status_active=1");
	echo $print_report_format;die;
}

if ($action == "load_drop_down_brand") {
	echo create_drop_down("cbo_brand_name", 100, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC", "id,brand_name", 1, "--Select--", "", "");
	exit();
}
if ($action == "load_drop_down_buyer_season") {
	echo create_drop_down("cbo_buyer_season_name", 100, "select season_name,id from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC", "id,season_name", 1, "--Select--", "", "");
	exit();
}




if ($db_type == 0) $insert_year = "SUBSTRING_INDEX(a.insert_date, '-', 1)";
if ($db_type == 2) $insert_year = "extract( year from b.insert_date)";
//item style------------------------------//
if ($action == "style_wise_search") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
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
			//alert(strCon);
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			if ($('#tr_' + str).css("display") != 'none') {
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
			//alert(name);
			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
		}
	</script>
<?
	extract($_REQUEST);
	if ($company == 0) $company_name = "";
	else $company_name = "and a.company_name=$company";
	if ($buyer == 0) $buyer_name = "";
	else $buyer_name = "and a.buyer_name=$buyer";

	if ($brand_name == 0) $brand_name_cond = "";
	else $brand_name_cond = "and a.brand_id=$brand_name";
	if ($buyer_season_name == 0) $buyer_season_name_cond = "";
	else $buyer_season_name_cond = "and a.season_buyer_wise=$buyer_season_name";
	if ($buyer_season_year == 0) $buyer_season_year_cond = "";
	else $buyer_season_year_cond = "and a.season_year=$buyer_season_year";

	if (str_replace("'", "", $job_id) != "")  $job_cond = "and a.id in(" . str_replace("'", "", $job_id) . ")";
	else  if (str_replace("'", "", $job_no) == "") $job_cond = "";
	else $job_cond = "and a.job_no_prefix_num in ( " . str_replace("'", "", $job_no) . ")";
	$arr = array();
	$sql = "SELECT b.id,a.style_ref_no,b.po_number,a.job_no_prefix_num,$insert_year as year from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst $company_name $buyer_name $job_cond $brand_name_cond $buyer_season_name_cond $buyer_season_year_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by job_no_prefix_num";
	echo create_list_view("list_view", "Style Refference,Order Numbers,Job no,Year", "150,120,100,100", "480", "310", 0, $sql, "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,po_number,job_no_prefix_num,year", "", "setFilterGrid('list_view',-1)", "0", "", 1);
	// echo $sql;
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

//order wise browse------------------------------//
if ($action == "job_wise_search")
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
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

			if ($('#tr_' + str).css("display") != 'none') {

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
	extract($_REQUEST);
	// echo $brand_name."__".$buyer_season_name."__".$buyer_season_year."__".$company;
	if ($company == 0) $company_name = "";
	else $company_name = " and b.company_name=$company"; //job_no
	if ($buyer == 0) $buyer_name = "";
	else $buyer_name = "and b.buyer_name=$buyer";
	if ($brand_name == 0) $brand_name_cond = "";
	else $brand_name_cond = "and b.brand_id=$brand_name";
	if ($buyer_season_name == 0) $buyer_season_name_cond = "";
	else $buyer_season_name_cond = "and b.season_buyer_wise=$buyer_season_name";
	if ($buyer_season_year == 0) $buyer_season_year_cond = "";
	else $buyer_season_year_cond = "and b.season_year=$buyer_season_year";
	$sql = "SELECT  a.id ,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$insert_year as year
		    from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and
	        b.status_active=1 and b.is_deleted=0 $company_name  $buyer_name $brand_name_cond $buyer_season_name_cond $buyer_season_year_cond order by job_no_mst";
	// echo $sql;
	echo create_list_view("list_view", "Order Number,Job No,Year,Style Ref", "130,130,80,80", "500", "310", 0, $sql, "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,style_ref_no", "", "setFilterGrid('list_view',-1)", "0", "", 1);
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}


//order wise browse------------------------------//
if ($action == "order_wise_search")
 {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
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
			if ($('#tr_' + str).css("display") != 'none') {
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
	extract($_REQUEST);
	//echo $job_no;die;
	if ($company == 0) $company_name = "";
	else $company_name = " and b.company_name=$company";
	if ($buyer == 0) $buyer_name = "";
	else $buyer_name = "and b.buyer_name=$buyer";
	if ($brand_name == 0) $brand_name_cond = "";
	else $brand_name_cond = "and b.brand_id=$brand_name";
	if ($buyer_season_name == 0) $buyer_season_name_cond = "";
	else $buyer_season_name_cond = "and b.season_buyer_wise=$buyer_season_name";
	if ($buyer_season_year == 0) $buyer_season_year_cond = "";
	else $buyer_season_year_cond = "and b.season_year=$buyer_season_year";

	if (str_replace("'", "", $job_id) != "")  $job_cond = "and b.id in(" . str_replace("'", "", $job_id) . ")";
	else  if (str_replace("'", "", $job_no) == "") $job_cond = "";
	else $job_cond = " and b.job_no_prefix_num in (" . str_replace("'", "", $job_no) . ")";

	if (str_replace("'", "", $style_id) != "")  $style_cond = "and b.id in(" . str_replace("'", "", $style_id) . ")";
	else  if (str_replace("'", "", $style_no) == "") $style_cond = "";
	else $style_cond = "and b.style_ref_no='" . $style_no . "'";
	$sql = "SELECT distinct a.id,a.po_number,b.style_ref_no,b.job_no_prefix_num,$insert_year as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_name $job_cond  $buyer_name $style_cond $brand_name_cond $buyer_season_name_cond $buyer_season_year_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by job_no_prefix_num";
	// echo $sql;//die;
	echo create_list_view("list_view", "Order Number,Job No, Year,Style Ref", "150,100,100,150", "550", "310", 0, $sql, "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,style_ref_no", "", "setFilterGrid('list_view',-1)", "0", "", 1);
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}



if ($action == "generate_report")
{
	$process = array(&$_POST);
	// echo "<pre>";
	// print_r($process);
	extract(check_magic_quote_gpc($process));
	$lineArr = return_library_array("select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr = return_library_array("select id, line_number from prod_resource_mst", 'id', 'line_number');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'buyer_name');

	$job_cond_id = "";
	$style_cond = "";
	$order_cond = "";
	$type = str_replace("'", "", $type);
	$cbo_session_year = $cbo_year;
	$cbo_year = str_replace("'", "", $cbo_year_selection);
	// echo $cbo_session_year."__".$cbo_year;
	if (str_replace("'", "", $cbo_company_name) == 0) $company_name = "";
	else $company_name = " and b.company_name=" . str_replace("'", "", $cbo_company_name) . "";
	if (str_replace("'", "", $cbo_buyer_name) == 0)  $buyer_name = "";
	else $buyer_name = "and b.buyer_name=" . str_replace("'", "", $cbo_buyer_name) . "";

	if (str_replace("'", "", $hidden_job_id) != "")  $job_cond_id = "and b.id in(" . str_replace("'", "", $hidden_job_id) . ")";
	else  if (str_replace("'", "", $txt_job_no) == "") $job_cond_id = "";
	else $job_cond_id = "and b.job_no_prefix_num='" . str_replace("'", "", $txt_job_no) . "'";
	if (str_replace("'", "", $hidden_style_id) != "")  $style_cond = "and b.id in(" . str_replace("'", "", $hidden_style_id) . ")";
	else  if (str_replace("'", "", $txt_style_no) == "") $style_cond = "";
	else $style_cond = "and b.style_ref_no like '%" . str_replace("'", "", $txt_style_no) . "%' ";
	if (str_replace("'", "", $hidden_order_id) != "") {
		$order_cond = "and a.id in (" . str_replace("'", "", $hidden_order_id) . ")";
		$job_cond = "";
	} else if (str_replace("'", "", $txt_order_no) == "") $order_cond = "";
	else $order_cond = "and a.po_number like '%" . str_replace("'", "", $txt_order_no) . "%' ";
	$shipping_status_cond = "";
	if (str_replace("'", "", $cbo_status) == 3) $shipping_status_cond = " and d.shiping_status=3";
	else if (str_replace("'", "", $cbo_status) == 2) $shipping_status_cond = " and d.shiping_status!=3";
	else $shipping_status_cond = "";

	if (str_replace("'", "", $cbo_brand_name) == 0) $brand_name_cond = "";
	else $brand_name_cond = "and b.brand_id=" . str_replace("'", "", $cbo_brand_name) . "";
	if (str_replace("'", "", $cbo_buyer_season_name) == 0) $season_name_cond = "";
	else $season_name_cond = "and b.season_buyer_wise=" . str_replace("'", "", $cbo_buyer_season_name) . "";
	if (str_replace("'", "", $cbo_session_year) == 0) $season_year_cond = "";
	else $season_year_cond = "and b.season_year=" . str_replace("'", "", $cbo_session_year) . "";

	$prod_reso_allo = return_field_value("auto_update", "variable_settings_production", "company_name=$cbo_company_name and variable_list=23 and is_deleted=0 and status_active=1");
	$variable_arr = sql_select("select cutting_update,printing_emb_production,sewing_production,cutting_input from variable_settings_production where company_name=" . str_replace("'", "", $cbo_company_name) . " and variable_list=1 and status_active=1");
	foreach ($variable_arr as $row_var) {
		$variable_cutting = $row_var[csf('cutting_update')];
		$variable_print = $row_var[csf('printing_emb_production')];
		$variable_delivery = $row_var[csf('cutting_input')];
		$variable_sew = $row_var[csf('sewing_production')];
	}
	if ($variable_cutting != $variable_print || $variable_cutting != $variable_delivery || $variable_cutting != $variable_sew) {
		if ($variable_cutting == 1) {
	?>
			<script>
				alert("Report Can't be generated dew to mixed variable setting for production update.(Pls. contact to system admin)");
				// return;
			</script>
		<?
			die;
		}
	}
	if ($type == 1) //Show
	{
		$po_number_data = array();
		$production_data_arr = array();
		$po_number_id = array();

		if (str_replace("'", "", $txt_production_date) != "") {
			$po_id_array = array();
			$sql = "SELECT po_break_down_id from PRO_GARMENTS_PRODUCTION_MST where status_active=1 and production_date=$txt_production_date";
			$res = sql_select($sql);
			foreach ($res as $v) {
				$po_id_array[$v['PO_BREAK_DOWN_ID']] = $v['PO_BREAK_DOWN_ID'];
			}
			// ================== fin fab ================
			$sql = "SELECT a.po_breakdown_id FROM order_wise_pro_details a,inv_transaction b
		  WHERE a.trans_id = b.id
		  and b.status_active=1 and a.entry_form in(18,15,16,37) and a.quantity!=0 and  b.is_deleted=0 and b.transaction_date=$txt_production_date";
			// echo $sql;die;
			$res = sql_select($sql);
			foreach ($res as $v) {
				$po_id_array[$v['PO_BREAKDOWN_ID']] = $v['PO_BREAKDOWN_ID'];
			}
			unset($res);
			if (count($po_id_array) > 0) {
				$po_id_cond = where_con_using_array($po_id_array, 0, "a.id");
			}
		}

		if ($variable_cutting == 2 || $variable_cutting == 3) {
			if (str_replace("'", "", trim($txt_date_from)) == "" || str_replace("'", "", trim($txt_date_to)) == "") $country_ship_date = "";
			else $country_ship_date = " and d.country_ship_date between $txt_date_from and $txt_date_to";

			$pro_date_sql = sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,b.buyer_name,
		  b.style_ref_no as style,d.country_ship_date,d.color_number_id,d.item_number_id,b.brand_id,b.season_buyer_wise,b.season_year
		  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
		  where a.job_id=b.id and a.id=d.po_break_down_id and  a.is_deleted=0 and a.status_active=1 and
		  b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and
		  b.status_active=1 $company_name $buyer_name $style_cond $order_cond $job_cond_id $country_ship_date $shipping_status_cond 		$brand_name_cond $season_name_cond $season_year_cond $po_id_cond order by a.job_no_mst,a.po_number,d.country_ship_date");

			$po_id_marge = array();
			$po_country_arr = array();
			$po_plan_cutqty_arr = array();
			foreach ($pro_date_sql as $row) {
				$po_number_data[$row[csf('id')]][$row[csf('country_ship_date')]]['id'] = $row[csf('id')];
				$po_number_data[$row[csf('id')]][$row[csf('country_ship_date')]]['job_no'] = $row[csf('job_no_mst')];
				$po_number_data[$row[csf('id')]][$row[csf('country_ship_date')]]['po_number'] = $row[csf('po_number')];
				$po_number_data[$row[csf('id')]][$row[csf('country_ship_date')]]['po_quantity'] += $row[csf('order_qty')];
				$po_number_data[$row[csf('id')]][$row[csf('country_ship_date')]]['plan_qty'] += $row[csf('plan_qty')];
				$po_number_data[$row[csf('id')]][$row[csf('country_ship_date')]]['buyer_name'] = $row[csf('buyer_name')];
				$po_number_data[$row[csf('id')]][$row[csf('country_ship_date')]]['style'] = $row[csf('style')];
				$po_number_data[$row[csf('id')]][$row[csf('country_ship_date')]]['item_number_id'][] = $row[csf('item_number_id')];
				$po_number_data[$row[csf('id')]][$row[csf('country_ship_date')]]['color_id'] = $row[csf('color_number_id')];
				$po_plan_cutqty_arr[$row[csf('id')]]['plan_qty'] += $row[csf('plan_qty')];

				if (!in_array($row[csf('country_ship_date')], $po_country_arr[$row[csf('id')]])) {

					$po_id_marge[$row[csf('id')]]['marge_ship_date'] += 1;
				}
				$po_country_arr[$row[csf('id')]][$row[csf('country_ship_date')]] = $row[csf('country_ship_date')];
				$po_number_id[$row[csf('id')]] = $row[csf('id')];
			}

			$sew_line_arr = array();
			if ($db_type == 0) {
				$sql_line = sql_select("SELECT group_concat(distinct a.sewing_line) as line_id,a.po_break_down_id from pro_garments_production_mst a
			  where  a.production_type='4' and a.is_deleted=0 and a.status_active=1 and a.production_date=" . $txt_production_date . "
			  group by a.po_break_down_id");
			}
			if ($db_type == 2) {

				$sql_line = sql_select("SELECT listagg(cast(a.sewing_line as varchar2(4000)),',') within group (order by a.sewing_line) as line_id,
			  a.po_break_down_id ,c.country_ship_date
			  from pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c
			  where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0
			  and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id and a.production_type='4' and a.is_deleted=0 and a.status_active=1
			  and a.production_date=" . $txt_production_date . "
			  group by a.po_break_down_id,c.country_ship_date");
			}
			foreach ($sql_line as $row_sew) {
				$sew_line_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('country_ship_date')]]['line'] = implode(',', array_unique(explode(',', $row_sew[csf('line_id')])));
			}
			// print_r($sew_line_arr);die;
			$po_number_id = implode(",", array_unique($po_number_id));
			if ($po_number_id == "") $po_number_id = 0;

			if ($db_type == 0) $concate_item = " group_concat(distinct a.sewing_line) as line_id";
			if ($db_type == 2) $concate_item = " listagg(cast(a.sewing_line as varchar2(4000)),',') within group (order by a.po_break_down_id) as line_id ";

			//$concate_item,
			$po_id_cond1 = str_replace("a.id", "a.po_break_down_id", $po_id_cond);
			$production_mst_sql = sql_select("SELECT a.po_break_down_id,c.country_ship_date,
		  sum(CASE WHEN b.production_type =1 and a.production_date=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END) AS cutting_qnty,
		  sum(CASE WHEN b.production_type =1 and a.production_date<=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END) AS cutting_total,
		  sum(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END)
		  AS printing_qnty,
		  sum(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date<=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END)
		  AS printing_qnty_total ,
		  sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END)
		  AS printreceived_qnty,
		  sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date<=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END)
		  AS printreceived_total,
		  sum(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END)
		  AS embl_qnty,
		  sum(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date<=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END)
		  AS embl_total,
		  sum(CASE WHEN b.production_type =3  and a.embel_name=2 and a.production_date=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END)
		  AS emblreceived_qnty,
		  sum(CASE WHEN b.production_type =3  and a.embel_name=2 and a.production_date<=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END)
		  AS emblreceived_total,
		  sum(CASE WHEN b.production_type =2 and a.embel_name=3 and a.production_date=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END)
		  AS wash_qnty,
		  sum(CASE WHEN b.production_type =2 and a.embel_name=3 and a.production_date<=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END)
		  AS wash_total,
		  sum(CASE WHEN b.production_type =3 and a.embel_name=3 and a.production_date=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END)
		  AS washreceived_qnty,
		  sum(CASE WHEN b.production_type =3 and a.embel_name=3 and a.production_date<=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END)
		  AS washreceived_total,
		  sum(CASE WHEN b.production_type =2 and a.embel_name=4 and a.production_date=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END)
		  AS sp_qnty,
		  sum(CASE WHEN b.production_type =2 and a.embel_name=4 and a.production_date<=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END)
		  AS sp_qnty_total ,
		  sum(CASE WHEN b.production_type =3  and a.embel_name=4 and a.production_date=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END)
		  AS spreceived_qnty,
		  sum(CASE WHEN b.production_type =3  and a.embel_name=4 and a.production_date<=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END)
		  AS spreceived_totol,
		  min(CASE WHEN b.production_type =4 THEN a.production_date end) AS min_input_date,
		  min(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date<=" . $txt_production_date . " THEN a.production_date end)
		  as min_embl_date,
		  sum(CASE WHEN b.production_type =4 and a.production_date=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END) AS sewingin_qnty,
		  sum(CASE WHEN b.production_type =4 and a.production_date<=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END) AS sewingin_total,
		  sum(CASE WHEN b.production_type =5 and a.production_date=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END) AS sewingout_qnty,
		  sum(CASE WHEN b.production_type =5 and a.production_date<=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END) AS sewingout_total,
		  sum(CASE WHEN b.production_type =8 and a.production_date=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END) AS finish_qnty,
		  sum(CASE WHEN b.production_type =8 and a.production_date<=" . $txt_production_date . " THEN b.production_qnty ELSE 0 END) AS finish_total
		  from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c where a.id=b.mst_id
		  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		  and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id $po_id_cond1 group by a.po_break_down_id,c.country_ship_date");
			//and a.po_break_down_id in (".str_replace("'","",$po_number_id).")  group by a.po_break_down_id,c.country_ship_date
			foreach ($production_mst_sql as $val) {
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['cutting_qnty'] = $val[csf('cutting_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['cutting_total'] = $val[csf('cutting_total')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['printing_qnty'] = $val[csf('printing_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['printreceived_qnty'] = $val[csf('printreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['printing_qnty_total'] = $val[csf('printing_qnty_total')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['printreceived_total'] = $val[csf('printreceived_total')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['emblreceived_total'] = $val[csf('emblreceived_total')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['emblreceived_qnty'] = $val[csf('emblreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['embl_total'] = $val[csf('embl_total')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['embl_qnty'] = $val[csf('embl_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['wash_qnty'] = $val[csf('wash_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['wash_total'] = $val[csf('wash_total')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['washreceived_qnty'] = $val[csf('washreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['washreceived_total'] = $val[csf('washreceived_total')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['sp_qnty'] = $val[csf('sp_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['sp_qnty_total'] = $val[csf('sp_qnty_total')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['spreceived_qnty'] = $val[csf('spreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['spreceived_totol'] = $val[csf('spreceived_totol')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['min_input_date'] = $val[csf('min_input_date')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['min_embl_date'] = $val[csf('min_embl_date')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['sewingin_qnty'] = $val[csf('sewingin_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['sewingin_total'] = $val[csf('sewingin_total')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['sewingout_qnty'] = $val[csf('sewingout_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['sewingout_total'] = $val[csf('sewingout_total')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['finish_qnty'] = $val[csf('finish_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['finish_total'] = $val[csf('finish_total')];
				$production_data_arr[$val[csf('po_break_down_id')]][$val[csf('country_ship_date')]]['line_id'] = $val[csf('line_id')];
				$po_number_gmt[] = $val[csf('po_break_down_id')];
			}

			//print_r($production_data_arr);die;
			$po_id_cond2 = str_replace("a.id", "a.po_break_down_id", $po_id_cond);
			$sql_fabric_qty = sql_select("SELECT a.po_breakdown_id,
		  sum(CASE WHEN b.transaction_date <= " . $txt_production_date . " AND a.trans_type =2  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
		  ELSE 0 END ) AS grey_fabric_issue,
		  sum(CASE WHEN b.transaction_date <= " . $txt_production_date . " AND a.trans_type =4  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
		  ELSE 0 END ) AS grey_fabric_issue_return,
		  sum(CASE WHEN b.transaction_date <= " . $txt_production_date . " AND a.trans_type =1  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
		  ELSE 0 END ) AS finish_fabric_rece,
		  sum(CASE WHEN b.transaction_date <= " . $txt_production_date . " AND a.trans_type =2  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
		  ELSE 0 END ) AS finish_fabric_rece_return,
		  sum(CASE WHEN b.transaction_date = " . $txt_production_date . " AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
		  ELSE 0 END ) AS fabric_qty,
		  sum(CASE WHEN b.transaction_date <" . $txt_production_date . " AND a.trans_type =2 and b.item_category=2  AND a.entry_form =18 THEN a.quantity
		  ELSE 0 END ) AS fabric_qty_pre,
		  sum(CASE WHEN b.transaction_date = " . $txt_production_date . " AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
		  sum(CASE WHEN b.transaction_date <" . $txt_production_date . " AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_pre,
		  sum(CASE WHEN b.transaction_date = " . $txt_production_date . " AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty,
		  sum(CASE WHEN b.transaction_date <" . $txt_production_date . " AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_pre
		  FROM order_wise_pro_details a,inv_transaction b
		  WHERE a.trans_id = b.id
		  and b.status_active=1 and a.entry_form in(18,15,16,37) and a.quantity!=0 and  b.is_deleted=0 $po_id_cond2  group by a.po_breakdown_id");
			//AND a.po_breakdown_id in (".str_replace("'","",$po_number_id).")
			$fabric_pre_qty = array();
			$fabric_today_qty = array();
			$total_fabric = array();
			$fabric_balance = array();
			$fabric_wip = array();
			foreach ($sql_fabric_qty as $value) {
				$fabric_wip[$value[csf("po_breakdown_id")]]['issue'] = $value[csf("grey_fabric_issue")] - $value[csf("grey_fabric_issue_return")];
				$fabric_wip[$value[csf("po_breakdown_id")]]['receive'] = $value[csf("finish_fabric_rece")] - $value[csf("finish_fabric_rece_return")];

				$fabric_pre_qty[$value[csf("po_breakdown_id")]] = $value[csf("fabric_qty_pre")] + $value[csf("trans_in_pre")] - $value[csf("trans_out_pre")];
				$fabric_today_qty[$value[csf("po_breakdown_id")]] = $value[csf("fabric_qty")] + $value[csf("trans_in_qty")] - $value[csf("trans_out_qty")];

				$po_id_fab[] = $value[csf("po_breakdown_id")];
			}
			// print_r($fabric_today_qty);die;
			$po_id_cond3 = str_replace("a.id", "a.po_break_down_id", $po_id_cond);
			$sql_sewing = sql_select("SELECT b.po_break_down_id,b.color_number_id,sum( b.cons )/count( b.color_number_id ) AS conjunction,b.pcs
		  FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a
		  WHERE a.id = b.pre_cost_fabric_cost_dtls_id  $po_id_cond3
		  GROUP BY b.po_break_down_id,b.color_number_id,a.body_part_id,b.pcs");
			// AND b.po_break_down_id in (".str_replace("'","",$po_number_id).")
			$con_per_pcs = array();
			foreach ($sql_sewing as $row_sew) {
				$con_avg[$row_sew[csf('po_break_down_id')]][$row_sew[csf('color_number_id')]] += str_replace("'", "", $row_sew[csf("conjunction")]);
				$con_per_pcs[$row_sew[csf('po_break_down_id')]] = $con_avg[$row_sew[csf('po_break_down_id')]][$row_sew[csf('color_number_id')]] / str_replace("'", "", $row_sew[csf("pcs")]);
			}
		}
		$sql_tna_date = sql_select("select a.po_number_id, a.task_start_date,a.task_finish_date	from  tna_process_mst a, lib_tna_task b
	  where b.task_name=a.task_number  and task_name=84");
		$tna_date_arr = array();
		foreach ($sql_tna_date as $tna_val) {
			$tna_date_arr[$tna_val[csf('po_number_id')]]['tna_start'] = $tna_val[csf('task_start_date')];
			$tna_date_arr[$tna_val[csf('po_number_id')]]['tna_end'] = $tna_val[csf('task_finish_date')];
		}
		ob_start();
		//and po_number_id in (".str_replace("'","",$po_number_id).")
		?>
		<fieldset style="width:2430px;">
			<table width="1880" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold"> Dailssy Production Progress Report</td>
				</tr>
				<tr style="border:none;">
					<td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
						Company Name:<? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?>
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? echo "Date: " . change_date_format(str_replace("'", "", $txt_production_date)); ?>
					</td>
				</tr>
			</table>
			<br />
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3970" class="rpt_table">
				<thead>
					<tr>
						<th width="40" rowspan="2">SL</th>
						<th width="80" rowspan="2">Buyer</th>
						<th width="100" rowspan="2">Style</th>
						<th width="100" rowspan="2">Order No</th>
						<th width="100" rowspan="2">Item</th>
						<th width="70" rowspan="2">Order Qty.</th>
						<th width="70" rowspan="2">Req.Qty Plan </th>
						<th width="70" rowspan="2">Actual Ship Date</th>
						<th width="70" rowspan="2">Input Date</th>
						<th width="180" colspan="3">Fini. Fabric Status</th>
						<th width="180" colspan="3">Cutting Status</th>
						<th width="300" colspan="5">Printing</th>
						<th width="300" colspan="5">Embroidery</th>
						<th width="300" colspan="5">Wash</th>
						<th width="300" colspan="5">Special</th>
						<th width="100" rowspan="2">Line No</th>
						<th width="300" colspan="6">Sewing Status</th>
						<th width="180" colspan="3">Finishing Status</th>
						<th width="50" rowspan="2">Runing Line</th>
						<th width="70" rowspan="2">Days req.as per Actual prod.</th>
						<th width="60" rowspan="2">Days remaning</th>
						<th width="60" rowspan="2">Prod.tgt should be per day.</th>
						<th width="60" rowspan="2">S/D as per req.day</th>
						<th width="80" rowspan="2">Delay/ Early </th>
						<th width="60" rowspan="2">Result</th>
						<th width="70" rowspan="2">Additional Line Req.</th>
						<th width="80" rowspan="2"> Cutting Com.%(Basis On Req.Qty)</th>
						<th width="80" rowspan="2"> Printing Com.%(Basis On Req.Qty)</th>
						<th width="80" rowspan="2"> Input Com.%(Basis On Req.Qty)</th>
						<th width="80" rowspan="2">Output Com.%(Basis On Req.Qty)</th>
						<th width="80" rowspan="2">Fini.Com.%(Basis On Order Qty.)</th>
						<th rowspan="2">Remarks</th>
					</tr>
					<tr>
						<th width="60" rowspan="2">Req.Fab</th>
						<th width="60" rowspan="2">Rcv.Fab</th>
						<th width="60" rowspan="2">Balance</th>
						<th width="60" rowspan="2">Daily Cutting</th>
						<th width="60" rowspan="2">Cutt.Com</th>
						<th width="60" rowspan="2">Balance</th>
						<th width="60" rowspan="2">Today Send </th>
						<th width="60" rowspan="2">Total Send </th>
						<th width="60" rowspan="2">Today Rcvd</th>
						<th width="60" rowspan="2">Total Rcvd</th>
						<th width="60" rowspan="2">Print Bal</th>
						<th width="60" rowspan="2">Today Send</th>
						<th width="60" rowspan="2">Total Send</th>
						<th width="60" rowspan="2">Today Rcvd </th>
						<th width="60" rowspan="2">Total Rcvd </th>
						<th width="60" rowspan="2">Embl Bal.</th>
						<th width="60" rowspan="2">Today Send</th>
						<th width="60" rowspan="2">Total Send</th>
						<th width="60" rowspan="2">Today Rcvd </th>
						<th width="60" rowspan="2">Total Rcvd </th>
						<th width="60" rowspan="2">Wash Bal.</th>
						<th width="60" rowspan="2">Today Send</th>
						<th width="60" rowspan="2">Total Send </th>
						<th width="60" rowspan="2">Today Rcvd </th>
						<th width="60" rowspan="2">Total Rcvd </th>
						<th width="60" rowspan="2">Sp. Bal.</th>
						<th width="60" rowspan="2">Input Today</th>
						<th width="60" rowspan="2">Input Total</th>
						<th width="60" rowspan="2">Input Stock</th>
						<th width="60" rowspan="2">Daily Q.c</th>
						<th width="60" rowspan="2">Total QC</th>
						<th width="60" rowspan="2">Q.c Bal</th>
						<th width="60" rowspan="2">Daily Finishing</th>
						<th width="60" rowspan="2">Total Finish </th>
						<th width="60" rowspan="2">Balance</th>
					</tr>
				</thead>
			</table>
			<div style="max-height:425px; overflow-y:scroll; width:3990px; margin-left:18px" id="scroll_body">
				<table cellspacing="0" border="1" class="rpt_table" width="3970" rules="all" id="table_body">
					<?
					$total_cut = 0;
					$total_print_iss = 0;
					$total_embl_iss = 0;
					$total_wash_iss = 0;
					$total_sp_iss = 0;
					$total_print_receive = 0;
					$total_sp_rec = 0;
					$total_embl_rec = 0;
					$total_wash_receive = 0;
					$total_sp_rec = 0;
					$total_sew_input = 0;
					$total_sew_out = 0;
					$total_delivery_cut = 0;
					$cutting_balance = 0;
					$print_issue_balance = 0;
					$print_rec_balance = 0;
					$deliv_cut_bal = 0;
					$total_sew_input_balance = 0;
					$input_percentage = 0;
					$inhand = 0;
					$i = 1;


					foreach ($po_number_data as $po_id => $po_arr) {

						foreach ($po_arr as $color_id => $color_arr) {

							//***********************for line********************************************************
							$line_id_all = $sew_line_arr[$po_id][$color_id]['line']; //$sew_line_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('country_ship_date')]]['line']
							//print_r($line_id_all);
							$line_name = "";
							$total_line = 0;
							foreach (array_unique(explode(",", $line_id_all)) as $l_id) {
								if ($line_name != "") $line_name .= ",";
								if ($prod_reso_allo == 1) {
									$line_name .= $lineArr[$prod_reso_arr[$l_id]];
								} else {
									$line_name .= $lineArr[$l_id];
								}
								$total_line++;
							}
							$item_name = "";
							foreach (array_unique($po_number_data[$po_id][$color_id]['item_number_id']) as $i_id) {
								if ($item_name != "") $item_name .= ",";
								$item_name .= $garments_item[$i_id];
							}
							$fabric_pre = $fabric_pre_qty[$po_id];
							$fabric_today = $fabric_today_qty[$po_id];
							$total_fabric = $fabric_pre + $fabric_today;
							$possible_cut_qty = $total_fabric / $con_per_pcs[$po_id][$color_id];
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";

							$fabric_balance = $fabric_qty - $total_fabric;
							$total_cut = $production_data_arr[$po_id][$color_id]['cutting_total'];
							$cutting_balance = $po_number_data[$po_id][$color_id]['plan_qty'] - $total_cut;
							$total_print_iss = $production_data_arr[$po_id][$color_id]['printing_qnty_total'];
							$total_embl_iss = $production_data_arr[$po_id][$color_id]['embl_total'];
							$total_embl_rec = $production_data_arr[$po_id][$color_id]['emblreceived_total'];
							$total_print_receive = $production_data_arr[$po_id][$color_id]['printreceived_total'];
							$total_sp_iss = $production_data_arr[$po_id][$color_id]['sp_qnty_total'];
							$total_wash_receive = $production_data_arr[$po_id][$color_id]['washreceived_total'];
							$total_wash_iss = $production_data_arr[$po_id][$color_id]['wash_total'];
							$total_sp_rec = $production_data_arr[$po_id][$color_id]['spreceived_totol'];
							$total_finish = $production_data_arr[$po_id][$color_id]['finish_total'];
							$print_balance = $total_print_iss - $total_print_receive;
							$embl_balance = $total_embl_iss - $total_embl_rec;
							$wash_balance = $total_wash_iss - $total_wash_receive;
							$sp_balance = $total_sp_iss - $total_sp_rec;
							$total_sew_input = $production_data_arr[$po_id][$color_id]['sewingin_total'];
							$total_sew_out = $production_data_arr[$po_id][$color_id]['sewingout_total'];
							$total_sew_balance = $po_number_data[$po_id][$color_id]['plan_qty'] - $total_sew_out;
							$input_percentage = ($total_sew_input / $po_number_data[$po_id][$color_id]['plan_qty']) * 100;
							$inhand = 0;
							if ($total_print_iss != 0 && $total_embl_iss != 0) {
								if (date("Y-m-d", strtotime($production_data_arr[$po_id][$color_id]['min_printin_date'])) <= date("Y-m-d", strtotime($production_data_arr[$po_id][$color_id]['min_embl_date']))) {
									$inhand = ($total_cut + $total_embl_rec) - ($total_sew_input + $total_print_iss);
								} else {
									$inhand = $total_cut - ($total_embl_iss - $total_print_receive) - $total_sew_input;
								}
							} else if ($total_print_iss != 0) {
								$inhand = (($total_cut + $total_print_receive) - ($total_print_iss + $total_sew_input));
							} else if ($total_embl_iss != 0) {
								$inhand = ($total_cut + $total_embl_rec) - ($total_embl_iss + $total_sew_input);
							}
							// for grand total ********************************************************************************************************************
							$grand_possible_cut_qty += $possible_cut_qty;
							$grand_total_order += $po_number_data[$po_id][$color_id]['po_quantity'];
							$grand_total_plan += $po_number_data[$po_id][$color_id]['plan_qty'];
							$grand_today_cut += $production_data_arr[$po_id][$color_id]['cutting_qnty'];
							$grand_total_cut += $total_cut;
							$grand_cutting_balance += $cutting_balance;
							$grand_today_print_iss += $production_data_arr[$po_id][$color_id]['printing_qnty'];
							$grand_total_print_iss += $total_print_iss;
							$grand_today_embl_iss += $production_data_arr[$po_id][$color_id]['embl_qnty'];
							$grand_total_embl_iss += $total_embl_iss;
							$grand_today_wash_iss += $production_data_arr[$po_id][$color_id]['wash_qnty'];
							$grand_total_wash_iss += $total_wash_iss;
							$grand_today_sp_iss += $production_data_arr[$po_id][$color_id]['sp_qnty'];
							$grand_total_sp_iss += $total_sp_iss;
							$grand_today_print_rec += $production_data_arr[$po_id][$color_id]['printreceived_qnty'];
							$grand_total_print_rec += $total_print_receive;
							$grand_print_issue_balance = $print_issue_balance;
							$grand_today_wash_rec += $production_data_arr[$po_id][$color_id]['washreceived_qnty'];
							$grand_total_wash_rec += $total_wash_receive;
							$grand_today_sp_rec += $production_data_arr[$po_id][$color_id]['spreceived_qnty'];
							$grand_total_embl_rec += $total_embl_rec;
							$grand_today_embl_rec += $production_data_arr[$po_id][$color_id]['emblreceived_qnty'];
							$grand_total_sp_rec += $total_sp_rec;
							$grand_today_sew += $production_data_arr[$po_id][$color_id]['sewingin_qnty'];
							$grand_total_sew += $total_sew_input;
							$grand_today_out += $production_data_arr[$po_id][$color_id]['sewingout_qnty'];
							$grand_total_out += $total_sew_out;
							$grand_total_sew_bal += $total_sew_balance;
							$grand_today_finish += $production_data_arr[$po_id][$color_id]['finish_qnty'];
							$grand_total_finish += $total_finish;
					?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>
								<td width="80">
									<p><? echo $buyer_short_library[$po_number_data[$po_id][$color_id]['buyer_name']]; ?></p>
								</td>
								<td width="100" align="left">
									<p><? echo $po_number_data[$po_id][$color_id]['style']; ?></p>
								</td>
								<td width="100" align="left">
									<p><? echo $po_number_data[$po_id][$color_id]['po_number']; ?></p>
								</td>
								<td width="100" align="left">
									<p><? echo $item_name; ?></p>
								</td>
								<td width="70" align="right"><? echo $po_number_data[$po_id][$color_id]['po_quantity']; ?></td>
								<td width="70" align="right"><? echo $po_number_data[$po_id][$color_id]['plan_qty']; ?></td>
								<td width="70" align="center"><? echo  change_date_format($color_id);  ?></td>
								<td width="70" align="center"><? echo change_date_format($production_data_arr[$po_id][$color_id]['min_input_date']);  ?></td>
								<?
								if (!in_array($po_id, $po_check_arr)) {
									$fabric_qty = $po_plan_cutqty_arr[$po_id]['plan_qty'] * $con_per_pcs[$po_id];
									$grand_fabric_total += $total_fabric;
									$grand_total_fabric_qty += $fabric_qty;
									$grand_fabric_bal += $fabric_qty - $total_fabric;
								?>
									<td width="60" align="right" rowspan="<? echo $po_id_marge[$po_id]['marge_ship_date']; ?>"><? echo number_format($fabric_qty, 2); ?></td>
									<td width="60" align="right" rowspan="<? echo $po_id_marge[$po_id]['marge_ship_date']; ?>"><? echo number_format($total_fabric, 2); ?></td>
									<td width="60" align="right" rowspan="<? echo $po_id_marge[$po_id]['marge_ship_date']; ?>"><? echo number_format($fabric_qty - $total_fabric, 2); ?></td>
								<?
								}
								?>
								<td width="60" align="right"><? echo $production_data_arr[$po_id][$color_id]['cutting_qnty']; ?></td>
								<td width="60" align="right"><? echo $total_cut; ?></td>
								<td width="60" align="right" title="Plan Cut Qty-Total Cuttin Qty"><? echo number_format($cutting_balance, 2); ?></td>
								<td width="60" align="right"><? echo $production_data_arr[$po_id][$color_id]['printing_qnty']; ?></td>
								<td width="60" align="right"><? echo $production_data_arr[$po_id][$color_id]['printing_qnty_total']; ?></td>
								<td width="60" align="right"><? echo $production_data_arr[$po_id][$color_id]['printreceived_qnty']; ?></td>
								<td width="60" align="right"><? echo $total_print_receive; ?></td>
								<td width="60" align="right" title="Total Print Issue- Total Print Receive"><? echo number_format($print_balance, 0); ?></td>
								<td width="60" align="right"><? echo $production_data_arr[$po_id][$color_id]['embl_qnty']; ?></td>
								<td width="60" align="right"><? echo $total_embl_iss; ?></td>
								<td width="60" align="right"><? echo $production_data_arr[$po_id][$color_id]['emblreceived_qnty']; ?></td>
								<td width="60" align="right"><? echo $total_embl_rec; ?></td>
								<td width="60" align="right" title="Total Embl. Issue- Total Embl. Receive"><? echo number_format($embl_balance, 0); ?></td>
								<td width="60" align="right"><? echo $production_data_arr[$po_id][$color_id]['wash_qnty']; ?></td>
								<td width="60" align="right"><? echo $total_wash_iss; ?></td>
								<td width="60" align="right"><? echo $production_data_arr[$po_id][$color_id]['washreceived_qnty']; ?></td>
								<td width="60" align="right"><? echo $total_wash_receive; ?></td>
								<td width="60" align="right" title="Total Wash Issue- Total Wash Receive"><? echo number_format($wash_balance, 0); ?></td>
								<td width="60" align="right"><? echo $production_data_arr[$po_id][$color_id]['sp_qnty']; ?></td>
								<td width="60" align="right"><? echo $total_sp_iss; ?></td>
								<td width="60" align="right"><? echo $production_data_arr[$po_id][$color_id]['spreceived_qnty']; ?></td>
								<td width="60" align="right"><? echo $total_sp_rec; ?></td>
								<td width="60" align="right" title="Total Sp. Issue- Total Sp. Receive"><? echo number_format($sp_balance, 0); ?></td>
								<td width="100" align="center">
									<p><? echo  $line_name; ?></p>
								</td>
								<td width="60" align="right"><? echo $production_data_arr[$po_id][$color_id]['sewingin_qnty']; ?></td>
								<td width="60" align="right"><? echo $total_sew_input; ?></td>
								<td width="60" align="right" title="Total Cutting Qty- Total Sewing Input"><? echo $total_cut - $total_sew_input;  ?></td>
								<td width="60" align="right"><? echo $production_data_arr[$po_id][$color_id]['sewingout_qnty']; ?></td>
								<td width="60" align="right"><? echo $total_sew_out; ?></td>
								<td width="60" align="right" title="Total Plan Cut Qty- Total  Qc Qty"><? echo number_format($total_sew_balance, 0); ?></td>
								<td width="60" align="right"><? echo $production_data_arr[$po_id][$color_id]['finish_qnty']; ?></td>
								<td width="60" align="right"><? echo $total_finish; ?></td>
								<td width="60" align="right" title="Total Order Qty - Total Finish Qty"><? $qc_balance = $total_finish - $po_number_data[$po_id][$color_id]['po_quantity'];
																										echo number_format($qc_balance, 0);
																										?>
								</td>
								<td width="50" align="right" title="Today Running Line Number"><? $running_line = $total_line;
																								if ($running_line > 0) echo $running_line;
																								else echo 0; ?></td>
								<td width="70" align="right" title="QC Balance / Daily QC "><? $days_requard = ($total_sew_balance / $production_data_arr[$po_id][$color_id]['sewingout_qnty']);
																							echo number_format($days_requard, 0);
																							?>
								</td>
								<td width="60" align="right" title="Ship Date - Present Days"><? echo	$days_run = datediff("d", str_replace("'", "", $txt_production_date), $color_id); ?></td>
								<td width="60" align="right" title="QC Balance / Days Remaning "><? $prd_sh_be = ($total_sew_balance / $days_run);
																									echo floor($prd_sh_be); ?></td>
								<td width="60" align="right" title="Reporting Date + Days Req. As per Actual Production +2">
									<?
									$date_plass = $days_requard + 2;
									$sddate = add_date(str_replace("'", "", $txt_production_date), $date_plass);
									echo change_date_format($sddate);
									?>
								</td>
								<td width="80" align="right" title="Actual Ship Date - S/D as per Req. days	">
									<p>
										<?
										$delay = "";
										if ($days_requard != 0) {
											$delay = datediff("d", $sddate, $color_id);
											if ($delay > 0) {
												echo $delay . " Days Early";
											} else {
												echo -$delay . " Days Delay";
											}
										}
										?></p>
								</td>
								<td width="60" align="right" title="Ship Date - Present Days"><? // if(date("Y-m-d",strtotime($sddate))> date("Y-m-d",strtotime($txt_production_date)))
																								if ($delay > 0) echo "Safe";
																								else         echo "Danger";
																								?>
								</td>
								<td width="70" align="right" title="(Pro.Tgt. Should be per day/ ( Daily QC / Runing Line))- Runing Line"><?
																																			if ($running_line > 0) {
																																				if (((floor($prd_sh_be) / ($production_data_arr[$po_id][$color_id]['sewingout_qnty'] / $running_line)) - $running_line) > 0) {
																																					echo number_format(((floor($prd_sh_be) / ($production_data_arr[$po_id][$color_id]['sewingout_qnty'] / $running_line)) - $running_line), 2);
																																				}
																																			}
																																			?></td>
								<td width="80" align="right" title="Total Cut Qty/ Req.Qty*100"><? echo number_format(($total_cut / $po_number_data[$po_id][$color_id]['plan_qty'] * 100), 2) . " %"; ?></td>
								<td width="80" align="right" title="Receive Qty/ Req.Qty*100"><? echo number_format(($total_print_receive / $po_number_data[$po_id][$color_id]['plan_qty'] * 100), 2) . " %"; ?></td>
								<td width="80" align="right" title="Input Qty/ Req.Qty*100"><? echo number_format(($total_sew_input / $po_number_data[$po_id][$color_id]['plan_qty'] * 100), 2) . " %"; ?></td>
								<td width="80" align="right" title="Total QC Qty/ Req.Qty*100"><? echo number_format(($total_sew_out / $po_number_data[$po_id][$color_id]['plan_qty'] * 100), 2) . " %"; ?></td>
								<td width="80" align="right" title="Total Finish/ Qty(Pcs)*100"><? echo number_format(($total_finish / $po_number_data[$po_id][$color_id]['po_quantity'] * 100), 2) . " %"; ?></td>
								<td align="center">
									<?
									if (($total_finish / $po_number_data[$po_id][$color_id]['po_quantity'] * 100) >= 100) echo "Sewing  Ok";
									else    if (($total_finish / $po_number_data[$po_id][$color_id]['po_quantity'] * 100) >= 100) echo "Input Ok";
									else if (($total_finish / $po_number_data[$po_id][$color_id]['po_quantity'] * 100) == 0);
									echo "Yet To Input";
									?>
								</td>
							</tr>
					<?
							$po_check_arr[] = $po_id;
							$i++;
						}
					}
					?>
					<tfoot>
						<tr>
							<th width="40"><? // echo $i;
											?></th>
							<th width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]];
											?></th>
							<th width="100">
								</td>
							<th width="100">
								</td>
							<th width="100"><strong>Grand Total:</strong></th>
							<th width="70" id="grand_total_order"><? echo $grand_total_order; ?></th>
							<th width="70" id="grand_total_plan"><? echo $grand_total_plan; ?></th>
							<th width="70"> <strong></strong></th>
							<th width="70" align="right"></th>
							<th width="60" align="right" id="grand_total_fabric_qty"><? echo number_format($grand_total_fabric_qty, 2); ?></th>
							<th width="60" align="right" id="grand_fabric_total"><? echo $grand_fabric_total; ?></th>
							<th width="60" align="right" id="grand_fabric_bal"><? echo number_format($grand_fabric_bal, 2); ?></th>
							<th width="60" align="right" id="grand_today_cut"><? echo $grand_today_cut; ?></th>
							<th width="60" align="right" id="grand_total_cut"><? echo $grand_total_cut; ?></th>
							<th width="60" align="right" id="grand_cutting_balance"><? echo $grand_cutting_balance; ?></th>
							<th width="60" align="right" id="grand_today_print_iss"><? echo $grand_today_print_iss; ?></th>
							<th width="60" align="right" id="grand_total_print_iss"><? echo $grand_total_print_iss; ?></th>
							<th width="60" align="right" id="grand_today_print_rec"><? echo $grand_today_print_rec; ?></th>
							<th width="60" align="right" id="grand_total_print_rec"><? echo $grand_total_print_rec; ?></th>
							<th width="60" align="right" id="grand_print_bal"><? echo $grand_total_print_iss - $grand_total_print_rec; ?></th>
							<th width="60" align="right" id="grand_today_embl_iss"><? echo $grand_today_embl_iss; ?></th>
							<th width="60" align="right" id="grand_total_embl_iss"><? echo $grand_total_embl_iss; ?></th>
							<th width="60" align="right" id="grand_today_embl_rec"><? echo $grand_today_embl_rec; ?></th>
							<th width="60" align="right" id="grand_total_embl_rec"><? echo $grand_total_embl_rec; ?></th>
							<th width="60" align="right" id="grand_total_embl_bal"><? echo $grand_total_embl_iss - $grand_total_embl_rec; ?></th>
							<th width="60" align="right" id="grand_today_wash_iss"><? echo $grand_today_wash_iss; ?></th>
							<th width="60" align="right" id="grand_total_wash_iss"><? echo $grand_total_wash_iss; ?></th>
							<th width="60" align="right" id="grand_today_wash_rec"><? echo $grand_today_wash_rec; ?></th>
							<th width="60" align="right" id="grand_total_wash_rec"><? echo $grand_total_wash_rec; ?></th>
							<th width="60" align="right" id="grand_total_wash_bal"><? echo $grand_total_wash_iss - $grand_total_wash_rec; ?></th>
							<th width="60" align="right" id="grand_today_sp_iss"><? echo $grand_today_sp_iss; ?></th>
							<th width="60" align="right" id="grand_total_sp_iss"><? echo $grand_total_sp_iss; ?></th>
							<th width="60" align="right" id="grand_today_sp_rec"><? echo $grand_today_sp_rec; ?></th>
							<th width="60" align="right" id="grand_total_sp_rec"><? echo $grand_total_sp_rec; ?></th>
							<th width="60" align="right" id="grand_total_sp_bal"><? echo $grand_total_sp_iss - $grand_total_sp_rec; ?></th>
							<th width="60" align="right"><? // echo line name;
															?></th>
							<th width="60" align="right" id="grand_today_sew"><? echo $grand_today_sew; ?></th>
							<th width="60" align="right" id="grand_total_sew"><? echo $grand_total_sew; ?></th>
							<th width="60" align="right" id="grand_total_stock"><? echo $grand_total_cut - $grand_total_sew; ?></th>
							<th width="60" align="right" id="grand_today_out"><? echo $grand_today_out; ?></th>
							<th width="60" align="right" id="grand_total_out"><? echo $grand_total_out; ?></th>
							<th width="60" align="right" id="grand_total_sew_bal"><? echo $grand_total_sew_bal; ?></th>
							<th width="60" align="right" id="grand_today_finish"><? echo $grand_today_finish; ?></th>
							<th width="60" align="right" id="grand_total_finish"><? echo $grand_total_finish; ?></th>
							<th width="60" align="right" id="grand_total_finish_bal"><? echo $grand_total_finish - $grand_total_order; ?></th>
							<th width="50" align="right"><? // echo running line;
															?></th>
							<th width="70" align="right"></th>
							<th width="60" align="right"></th>
							<th width="60" align="right"></th>
							<th width="60" align="right"></th>
							<th width="80" align="right"></th>
							<th width="60" align="right"></th>
							<th width="70" align="right"></th>
							<th width="80" align="right"></th>
							<th width="80" align="right"></th>
							<th width="80" align="right"></th>
							<th width="80" align="right"></th>
							<th width="80" align="right"></th>
							<th align="right"></th>
						</tr>
					</tfoot>


				</table>
			</div>
			</div>

		</fieldset>
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
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename";
		exit();
	} else if ($type == 2) //Show2
	{
		//rehan for northern
		$cbo_company_name = str_replace("'", "", $cbo_company_name);
		$cbo_year = str_replace("'", "", $cbo_year_selection);
		if ($db_type == 2) {
			$year_cond = ($cbo_year) ? " and to_char(b.insert_date,'YYYY')=$cbo_year" : "";
		} else {
			$year_cond = ($cbo_year) ? " and year(b.insert_date)=$cbo_year" : "";
		}
		$order_cond = "";
		$order_cond2 = "";
		$job_cond_id2 = "";
		$jobs = explode(",", str_replace("'", "", $txt_job_no));
		$jobs_id = "'" . implode("','", $jobs) . "'";
		if (str_replace("'", "", $hidden_job_id))  $job_cond_id2 = "and b.id in(" . str_replace("'", "", $hidden_job_id) . ")";

		if (str_replace("'", "", $txt_job_no))  $job_no_cond2 = " and b.job_no_prefix_num in($jobs_id)";

		if (str_replace("'", "", $hidden_order_id)) {
			$order_cond2 = " and c.id in (" . str_replace("'", "", $hidden_order_id) . ")";
		}

		if (str_replace("'", "", $txt_order_no))  $order_no_cond2 = "and c.po_number =(" . trim($txt_order_no) . ")";
		//if (str_replace("'","",$txt_order_no)=="") $order_cond=""; else $order_cond=" and c.po_number like '%".str_replace("'","",$txt_order_no)."%' ";
		$shipping_status_cond = "";
		if (str_replace("'", "", $cbo_status) == 3) $shipping_status_cond = " and c.shiping_status=3";
		else if (str_replace("'", "", $cbo_status) == 2) $shipping_status_cond = " and c.shiping_status=2";
		else if (str_replace("'", "", $cbo_status) == 1) $shipping_status_cond = " and c.shiping_status=1";
		else $shipping_status_cond = "";

		if (str_replace("'", "", trim($txt_date_from)) == "" || str_replace("'", "", trim($txt_date_to)) == "") $country_ship_date = "";
		else $country_ship_date = " and e.country_ship_date between $txt_date_from and $txt_date_to";


		$prod_reso_allo = return_field_value("auto_update", "variable_settings_production", "company_name='$cbo_company_name' and variable_list=23 and is_deleted=0 and status_active=1");
		/*if(str_replace("'", "", $txt_order_no)=="" )
		{
			$order_cond2="";
		}*/
		if (str_replace("'", "", $txt_job_no) == "") {
			$job_cond_id2 = "";
		}


		$companyArr = return_library_array("SELECT id,company_name FROM lib_company WHERE status_active=1 and is_deleted=0 and id='$cbo_company_name' ", "id", "company_name");

		$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');
		// $line_array =return_library_array("SELECTselect id, line_name from lib_sewing_line",'id','line_name');
		$lineDataArr = sql_select("select id, line_name from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial"); 
		//echo $lineDataArr;die;
		foreach($lineDataArr as $lRow)
			{
				$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
				$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
				$lastSlNo=$lRow[csf('sewing_line_serial')];
				$line_n=$lRow[csf('line_name')];
			}

			
			
			//echo "<pre>";print_r($lineArr);die;
		// $line_lib = return_library_array("SELECT id,line_name from lib_sewing_line where company_name='$cbo_company_name'", "id", "line_name");
		// if ($prod_reso_allo == 1) {

		// 	$line_libr = "SELECT id,line_number from prod_resource_mst where company_id='$cbo_company_name' and is_deleted=0 ";
		// 	foreach (sql_select($line_libr) as $row) {
		// 		$line = '';
		// 		$line_number = explode(",", $row[csf('line_number')]);
		// 		foreach ($line_number as $val) {
		// 			if ($line == '') $line = $line_lib[$val];
		// 			else $line .= "," . $line_lib[$val];
		// 		}
		// 		$line_lib_resource[$row[csf('id')]] = $line;
		// 	}
		// }
		$color_lib = return_library_array("SELECT id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		if (str_replace("'", "", $txt_production_date) != "") {
			if ($db_type == 0) {
				$production_date = change_date_format(str_replace("'", "", $txt_production_date), "yyyy-mm-dd", "");
			} else if ($db_type == 2) {
				$production_date = change_date_format(str_replace("'", "", $txt_production_date), "", "", 1);
			}
			$date_cond = " and a.production_date='$production_date'";
		}

		if ($db_type == 0) $group_concat = "group_concat(distinct(a.sewing_line)) as sewing_line";
		if ($db_type == 2) $group_concat = "listagg(a.sewing_line,',') within group (order by a.sewing_line) as sewing_line";



		$sql = "SELECT b.style_ref_no,b.buyer_name ,a.po_break_down_id,c.po_number,a.item_number_id,e.color_number_id,c.shipment_date,c.shiping_status,a.prod_reso_allo,
    	( case when a.production_type=4 and d.production_type=4 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_sew_input,


    	( case when a.production_type=5 and d.production_type=5 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_sew_output,


    	( case when a.production_type=1 and d.production_type=1 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_cutting,



    	( case when a.production_type=2 and d.production_type=2  and a.embel_name=1 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_print_issue,

    	( case when a.production_type=2 and d.production_type=2  and a.embel_name=2 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_emb_issue,

    	( case when a.production_type=2 and d.production_type=2  and a.embel_name=3 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_wash_issue,



    	( case when a.production_type=3 and d.production_type=3  and a.embel_name=1 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_print_receive,


    	( case when a.production_type=3 and d.production_type=3  and a.embel_name=2 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_emb_receive,



    	( case when a.production_type=3 and d.production_type=3  and a.embel_name=3 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_wash_receive,


    	( case when a.production_type=8 and d.production_type=8   and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_finish
    	,
    	( case when a.production_type=7 and d.production_type=7   and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_iron
    	,
    	( case when a.production_type=11 and d.production_type=11   and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_poly
    	,
    	( case when a.production_type=4 and d.production_type=4  then d.production_qnty else 0 end ) as total_sew_input,


    	( case when a.production_type=5 and d.production_type=5  then d.production_qnty else 0 end ) as total_sew_out,

    	( case when a.production_type=1 and d.production_type=1  then d.production_qnty else 0 end ) as total_cutting,

    	( case when a.production_type=2 and d.production_type=2 and a.embel_name=1 then d.production_qnty else 0 end ) as total_print_issue,

    	( case when a.production_type=2 and d.production_type=2 and a.embel_name=2 then d.production_qnty else 0 end ) as total_emb_issue,

    	( case when a.production_type=2 and d.production_type=2 and a.embel_name=3 then d.production_qnty else 0 end ) as total_wash_issue,

    	( case when a.production_type=3 and d.production_type=3 and a.embel_name=1 then d.production_qnty else 0 end ) as total_print_receive,

    	( case when a.production_type=3 and d.production_type=3 and a.embel_name=2 then d.production_qnty else 0 end ) as total_emb_receive,

    	( case when a.production_type=3 and d.production_type=3 and a.embel_name=3 then d.production_qnty else 0 end ) as total_wash_receive,

    	( case when a.production_type=8 and d.production_type=8  then d.production_qnty else 0 end ) as total_finish,
    	( case when a.production_type=7 and d.production_type=7  then d.production_qnty else 0 end ) as total_iron,
    	( case when a.production_type=11 and d.production_type=11  then d.production_qnty else 0 end ) as total_poly,
		( case when a.production_type=5 and d.production_type=5  then a.sewing_line else 0 end ) as sewing_line

       FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a, pro_garments_production_dtls d, wo_po_color_size_breakdown e  WHERE b.id = c.job_id  and c.id = a.po_break_down_id and a.id = d.mst_id   and d.color_size_break_down_id = e.id and a.po_break_down_id=e.po_break_down_id and a.status_active = 1 and a.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0  and c.status_active = 1 and c.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0
           $company_name  $buyer_name $job_cond_id2 $job_no_cond2 $order_no_cond2 $shipping_status_cond  $order_cond2 $country_ship_date $brand_name_cond $season_name_cond $season_year_cond ";//   group by b.style_ref_no ,b.buyer_name ,a.po_break_down_id,c.po_number,a.item_number_id,e.color_number_id ,c.shipment_date ,c.shiping_status,a.sewing_line,a.prod_reso_allo 
		  //echo $sql; die;
		$production_data = sql_select($sql);
		$style_po_wise_line = array();
		$po_lib_arr = array();
		
		foreach ($production_data as $vals) 
		{
			if($vals[csf('prod_reso_allo')]==1)
			{
				
				$sewing_line_ids[$vals["SEWING_LINE"]]=$lineArr[$prod_reso_arr[$vals["SEWING_LINE"]]];


				//$sl_ids_arr = explode(",", $sewing_line_ids);
				// foreach($sl_ids_arr as $val)
				// {
				// 	if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line=$lineArr[$val];
				// }
			}
			
			else
			{
				$sewing_line_id2[$vals["SEWING_LINE"]]=$lineArr[$vals["SEWING_LINE"]];
			
			}
			$line_arr=array_merge($sewing_line_ids,$sewing_line_id2);

			$line_str=implode(", ",$line_arr);
			
			$style_po_wise_line[$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]] =$line_str;

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_sew_input"] += $vals[csf("today_sew_input")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_sew_output"] += $vals[csf("today_sew_output")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_cutting"] += $vals[csf("today_cutting")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_print_issue"] += $vals[csf("today_print_issue")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_emb_issue"] += $vals[csf("today_emb_issue")];



			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_wash_issue"] += $vals[csf("today_wash_issue")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_print_receive"] += $vals[csf("today_print_receive")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_emb_receive"] += $vals[csf("today_emb_receive")];



			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_wash_receive"] += $vals[csf("today_wash_receive")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_finish"] += $vals[csf("today_finish")];
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_iron"] += $vals[csf("today_iron")];
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_poly"] += $vals[csf("today_poly")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["sewing_line"] = $vals[csf("sewing_line")];
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["shiping_status"] = $vals[csf("shiping_status")];

			$style_wise_sub[$vals[csf("style_ref_no")]]["today_sew_input"] += $vals[csf("today_sew_input")];

			$style_wise_sub[$vals[csf("style_ref_no")]]["today_sew_output"] += $vals[csf("today_sew_output")];

			$style_wise_sub[$vals[csf("style_ref_no")]]["today_cutting"] += $vals[csf("today_cutting")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["today_finish"] += $vals[csf("today_finish")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["today_iron"] += $vals[csf("today_iron")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["today_poly"] += $vals[csf("today_poly")];
			$today_all_po_arr[$vals[csf("po_break_down_id")]] = $vals[csf("po_break_down_id")];
			$style_wise_buyer_arr[$vals[csf("style_ref_no")]] = $vals[csf("buyer_name")];
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_sew_input"] += $vals[csf("total_sew_input")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_sew_out"] += $vals[csf("total_sew_out")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_cutting"] += $vals[csf("total_cutting")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_print_issue"] += $vals[csf("total_print_issue")];
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_emb_issue"] += $vals[csf("total_emb_issue")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_wash_issue"] += $vals[csf("total_wash_issue")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_print_receive"] += $vals[csf("total_print_receive")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_emb_receive"] += $vals[csf("total_emb_receive")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_wash_receive"] += $vals[csf("total_wash_receive")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_finish"] += $vals[csf("total_finish")];
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_poly"] += $vals[csf("total_poly")];
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_iron"] += $vals[csf("total_iron")];

			$style_wise_sub[$vals[csf("style_ref_no")]]["total_sew_input"] += $vals[csf("total_sew_input")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_sew_out"] += $vals[csf("total_sew_out")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_cutting"] += $vals[csf("total_cutting")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_print_issue"] += $vals[csf("total_print_issue")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_print_receive"] += $vals[csf("total_print_receive")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_wash_issue"] += $vals[csf("total_wash_issue")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_wash_receive"] += $vals[csf("total_wash_receive")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_emb_issue"] += $vals[csf("total_emb_issue")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_emb_receive"] += $vals[csf("total_emb_receive")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_finish"] += $vals[csf("total_finish")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_iron"] += $vals[csf("total_iron")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_poly"] += $vals[csf("total_poly")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["finish_wip"] += ($vals[csf("total_finish")] - $vals[csf("total_poly")]);
			if ($vals[csf("total_wash_receive")] > 0) {

				$style_wise_sub[$vals[csf("style_ref_no")]]["iron_wip"] += ($vals[csf("total_iron")] - $vals[csf("total_wash_receive")]);
			} else {
				$style_wise_sub[$vals[csf("style_ref_no")]]["iron_wip"] += ($vals[csf("total_iron")] - $vals[csf("total_sew_out")]);
			}

			$style_wise_sub[$vals[csf("style_ref_no")]]["wash_wip"] += ($vals[csf("total_wash_receive")] - $vals[csf("total_wash_issue")]);

			$style_wise_sub[$vals[csf("style_ref_no")]]["poly_wip"] += ($vals[csf("total_poly")] - $vals[csf("total_iron")]);

			$style_wise_sub[$vals[csf("style_ref_no")]]["sewin_wip"] += ($vals[csf("total_sew_input")] - $vals[csf("total_print_receive")]);
			$style_wise_sub[$vals[csf("style_ref_no")]]["sewout_wip"] += ($vals[csf("total_sew_out")] - $vals[csf("total_sew_input")]);
			$style_wise_sub[$vals[csf("style_ref_no")]]["print_wip"] += ($vals[csf("total_print_receive")] - $vals[csf("total_print_issue")]);
			$style_wise_sub[$vals[csf("style_ref_no")]]["emb_wip"] += ($vals[csf("total_emb_receive")] - $vals[csf("total_emb_issue")]);

			$po_lib_arr[$vals[csf("po_break_down_id")]] = $vals[csf("po_number")];
		}
		//  print_r($style_po_wise_line);
		$today_all_po_id = implode(",",  $today_all_po_arr);
		$all_po_arr = explode(",", $today_all_po_id);
		$po_chunk_cond = "";
		if ($db_type == 2 and count($all_po_arr) > 999) {

			$all_po_arr = array_chunk($all_po_arr, 999);
			foreach ($all_po_arr as $key => $val) {
				$values = implode(",", $val);
				if ($po_chunk_cond == "") {
					$po_chunk_cond = " and ( a.po_break_down_id in ($values) ";
				} else {
					$po_chunk_cond .= " or a.po_break_down_id in ($values) ) ";
				}
			}
		} else {
			$po_chunk_cond = " and a.po_break_down_id in ($today_all_po_id) ";
		}
		$new_po_cond = str_replace("a.po_break_down_id", "b.id", $po_chunk_cond);
		$style_wise_ctq_arr = array();
		$style_wise_ctq_sql = "SELECT a.style_ref_no,sum(case when c.production_date=$txt_production_date then c.carton_qty else 0 end ) as today_cartoon,sum(c.carton_qty) as total_cartoon from wo_po_details_master a,wo_po_break_down b,pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  c.production_type=8 and  $new_po_cond group by  a.style_ref_no";
		foreach (sql_select($style_wise_ctq_sql) as $keys => $vals) {
			$style_wise_ctq_arr[$vals[csf("style_ref_no")]]["today_cartoon"] += $vals[csf("today_cartoon")];
			$style_wise_ctq_arr[$vals[csf("style_ref_no")]]["total_cartoon"] += $vals[csf("total_cartoon")];
		}

		$new_po_cond2 = str_replace("a.po_break_down_id", "c.po_break_down_id", $po_chunk_cond);
		$style_wise_ins_arr = array();
		$po_wise_ins_sql = "SELECT a.style_ref_no,b.id, sum(case when inspection_date=$txt_production_date then inspection_qnty else 0 end ) as today_inspection,sum(inspection_qnty) as total_inspection from wo_po_details_master a,wo_po_break_down b,pro_buyer_inspection c where a.id=b.job_id and b.id=c.po_break_down_id and  a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  $new_po_cond2 group by a.style_ref_no,b.id ";
		foreach (sql_select($po_wise_ins_sql) as $keys => $vals) {
			$style_wise_ins_arr[$vals[csf("style_ref_no")]][$vals[csf("id")]]["today_inspection"] += $vals[csf("today_inspection")];
			$style_wise_ins_arr[$vals[csf("style_ref_no")]][$vals[csf("id")]]["total_inspection"] += $vals[csf("total_inspection")];
		}




		//echo $po_chunk_cond;die;

		/*$sql_previous = "SELECT b.style_ref_no ,a.po_break_down_id,c.po_number,a.item_number_id,e.color_number_id,c.shipment_date,$group_concat ,
    	sum( case when a.production_type=4 and d.production_type=4  then d.production_qnty else 0 end ) as total_sew_input,


    	sum( case when a.production_type=5 and d.production_type=5  then d.production_qnty else 0 end ) as total_sew_out,

    	sum( case when a.production_type=1 and d.production_type=1  then d.production_qnty else 0 end ) as total_cutting,

    	sum( case when a.production_type=2 and d.production_type=2 and a.embel_name=1 then d.production_qnty else 0 end ) as total_print_issue,

    	sum( case when a.production_type=2 and d.production_type=2 and a.embel_name=2 then d.production_qnty else 0 end ) as total_emb_issue,

    	sum( case when a.production_type=2 and d.production_type=2 and a.embel_name=3 then d.production_qnty else 0 end ) as total_wash_issue,

    	sum( case when a.production_type=3 and d.production_type=3 and a.embel_name=1 then d.production_qnty else 0 end ) as total_print_receive,

    	sum( case when a.production_type=3 and d.production_type=3 and a.embel_name=2 then d.production_qnty else 0 end ) as total_emb_receive,

    	sum( case when a.production_type=3 and d.production_type=3 and a.embel_name=3 then d.production_qnty else 0 end ) as total_wash_receive,

    	sum( case when a.production_type=8 and d.production_type=8  then d.production_qnty else 0 end ) as total_finish

       FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a, pro_garments_production_dtls d, wo_po_color_size_breakdown e  WHERE b.job_no = c.job_no_mst  and c.id = a.po_break_down_id and a.id = d.mst_id   and d.color_size_break_down_id = e.id and a.po_break_down_id=e.po_break_down_id and a.status_active = 1 and a.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0  and c.status_active = 1 and c.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0
          and d.color_size_break_down_id is not null and d.color_size_break_down_id <> 0 $company_name  $buyer_name $job_cond_id  $style_cond $shipping_status_cond $order_cond $order_cond2 $po_chunk_cond  group by b.style_ref_no ,a.po_break_down_id,c.po_number,a.item_number_id,e.color_number_id ,c.shipment_date order by b.style_ref_no ,a.po_break_down_id,c.po_number,a.item_number_id,e.color_number_id,c.shipment_date";
        $production_data2 = sql_select($sql_previous);
        foreach($production_data2 as $vals)
        {


            $data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]] ["total_sew_input"]+=$vals[csf("total_sew_input")];

            $data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]] ["total_sew_out"]+=$vals[csf("total_sew_out")];

            $data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]] ["total_cutting"]+=$vals[csf("total_cutting")];

            $data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]] ["total_print_issue"]+=$vals[csf("total_print_issue")];
            $data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]] ["total_emb_issue"]+=$vals[csf("total_emb_issue")];

            $data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]] ["total_wash_issue"]+=$vals[csf("total_wash_issue")];


            $data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]] ["total_print_receive"]+=$vals[csf("total_print_receive")];

            $data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]] ["total_emb_receive"]+=$vals[csf("total_emb_receive")];

            $data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]] ["total_wash_receive"]+=$vals[csf("total_wash_receive")];

            $data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]] ["total_finish"]+=$vals[csf("total_finish")];

            $style_wise_sub[$vals[csf("style_ref_no")]]["total_sew_input"]+=$vals[csf("total_sew_input")];
            $style_wise_sub[$vals[csf("style_ref_no")]]["total_sew_out"]+=$vals[csf("total_sew_out")];
            $style_wise_sub[$vals[csf("style_ref_no")]]["total_cutting"]+=$vals[csf("total_cutting")];
            $style_wise_sub[$vals[csf("style_ref_no")]]["total_print_issue"]+=$vals[csf("total_print_issue")];
            $style_wise_sub[$vals[csf("style_ref_no")]]["total_print_receive"]+=$vals[csf("total_print_receive")];
            $style_wise_sub[$vals[csf("style_ref_no")]]["total_wash_issue"]+=$vals[csf("total_wash_issue")];
            $style_wise_sub[$vals[csf("style_ref_no")]]["total_wash_receive"]+=$vals[csf("total_wash_receive")];
            $style_wise_sub[$vals[csf("style_ref_no")]]["total_emb_issue"]+=$vals[csf("total_emb_issue")];
            $style_wise_sub[$vals[csf("style_ref_no")]]["total_emb_receive"]+=$vals[csf("total_emb_receive")];
            $style_wise_sub[$vals[csf("style_ref_no")]]["total_finish"]+=$vals[csf("total_finish")];
            if($vals[csf("total_wash_receive")]>0)
            {
            	$style_wise_sub[$vals[csf("style_ref_no")]]["finish_wip"]+=($vals[csf("total_finish")]-$vals[csf("total_wash_receive")]);
            }
            else
            {
            	$style_wise_sub[$vals[csf("style_ref_no")]]["finish_wip"]+=($vals[csf("total_finish")]-$vals[csf("total_sew_out")]);
            }

            $style_wise_sub[$vals[csf("style_ref_no")]]["wash_wip"]+=($vals[csf("total_wash_receive")]-$vals[csf("total_wash_issue")]);
            $style_wise_sub[$vals[csf("style_ref_no")]]["sewin_wip"]+=($vals[csf("total_sew_input")]-$vals[csf("total_print_receive")]);
            $style_wise_sub[$vals[csf("style_ref_no")]]["sewout_wip"]+=($vals[csf("total_sew_out")]-$vals[csf("total_sew_input")]);
            $style_wise_sub[$vals[csf("style_ref_no")]]["print_wip"]+=($vals[csf("total_print_receive")]-$vals[csf("total_print_issue")]);
            $style_wise_sub[$vals[csf("style_ref_no")]]["emb_wip"]+=($vals[csf("total_emb_receive")]-$vals[csf("total_emb_issue")]);


        }
		*/


		// query for cutting delivery to input challan
		$cutting_delevery_sql = "SELECT b.style_ref_no ,a.po_break_down_id,c.shipment_date,c.po_number,a.item_number_id,e.color_number_id,
    	sum( case when a.production_type=9 and d.production_type=9   and a.cut_delivery_date=$txt_production_date then d.production_qnty else 0 end ) as today_cutting_delivery,
    	sum( case when a.production_type=9 and d.production_type=9 then d.production_qnty else 0 end ) as total_cutting_delivery

       FROM wo_po_details_master b, wo_po_break_down c,pro_cut_delivery_order_dtls a, pro_cut_delivery_color_dtls d, wo_po_color_size_breakdown e  WHERE b.id = c.job_id  and c.id = a.po_break_down_id and a.id = d.mst_id   and d.color_size_break_down_id = e.id and a.po_break_down_id=e.po_break_down_id and a.status_active = 1 and a.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0  and c.status_active = 1 and c.is_deleted = 0
          and d.color_size_break_down_id is not null and d.color_size_break_down_id <> 0 $company_name  $buyer_name $job_no_cond2   $shipping_status_cond $order_cond $order_cond2  $po_chunk_cond    group by b.style_ref_no ,a.po_break_down_id,c.po_number,a.item_number_id,e.color_number_id ,c.shipment_date";

		foreach (sql_select($cutting_delevery_sql) as $vals) {
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_cutting_delivery"] += $vals[csf("today_cutting_delivery")];
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_cutting_delivery"] += $vals[csf("total_cutting_delivery")];

			$style_wise_sub[$vals[csf("style_ref_no")]]["today_cutting_delivery"] += $vals[csf("today_cutting_delivery")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_cutting_delivery"] += $vals[csf("total_cutting_delivery")];
		}



		foreach ($data_array as $style_id => $style_data) {
			$kk = 0;
			$ccc = 0;
			foreach ($style_data as $item_id => $item_data) {


				foreach ($item_data as $po_id => $po_data) {
					$color_span = 0;
					foreach ($po_data as $shipment_date => $ship_date_data) {


						foreach ($ship_date_data as $color_id => $color_data) {
							$buyer_ins_row_span_arr[$style_id][$po_id] += 1;
							$color_span++;
							$ccc++;
							$kk++;
						}
					}
					$style_wise_span_array[$style_id][$item_id][$po_id] = $color_span;
				}
			}
			$style_wise_buyer_span[$style_id] = $kk;
		}


		$po_id_arr = str_replace("a.po_break_down_id", "id", $po_chunk_cond);
		$po_id_arr2 = str_replace("a.po_break_down_id", "b.po_break_down_id", $po_chunk_cond);
		$po_id_arr3 = str_replace("a.po_break_down_id", "po_breakdown_id", $po_chunk_cond);


		$plan_cut_sql = "SELECT a.style_ref_no,b.po_break_down_id,b.item_number_id,b.color_number_id, SUM(b.plan_cut_qnty) as plan_cut ,sum(b.order_quantity) as order_quantity FROM wo_po_details_master a, wo_po_color_size_breakdown b  WHERE a.id=b.job_id and a.status_active=1 and  b.status_active=1 AND b.is_deleted=0  $po_id_arr2 GROUP BY a.style_ref_no,b.po_break_down_id,b.item_number_id,b.color_number_id ";

		$plan_cut_result = sql_select($plan_cut_sql);
		foreach ($plan_cut_result as $val_plan) {
			$plan_cut_arr[$val_plan[csf("po_break_down_id")]][$val_plan[csf("item_number_id")]][$val_plan[csf("color_number_id")]]["plan_cut"] += $val_plan[csf("plan_cut")];
			$plan_cut_arr[$val_plan[csf("po_break_down_id")]][$val_plan[csf("item_number_id")]][$val_plan[csf("color_number_id")]]["order_quantity"] += $val_plan[csf("order_quantity")];

			$plan_cut_arr_gross[$val_plan[csf("po_break_down_id")]]["order_quantity"] += $val_plan[csf("order_quantity")];

			$style_wise_sub[$val_plan[csf("style_ref_no")]]["plan_cut"] += $val_plan[csf("plan_cut")];
			$style_wise_sub[trim($val_plan[csf("style_ref_no")])]["order_quantity"] += $val_plan[csf("order_quantity")];
		}


		$sql_result_variable = sql_select("select ex_factory,production_entry from variable_settings_production where company_name=$cbo_company_name and variable_list=1 and status_active=1");
		$production_level = $sql_result_variable[0][csf("ex_factory")];
		if ($production_level != 1) {
			$ex_fac_sql = "SELECT  a.style_ref_no,c.id as po_id,f.item_number_id,f.color_number_id,sum(CASE WHEN d.entry_form!=85 THEN e.production_qnty ELSE 0 END)-sum(CASE WHEN d.entry_form=85 THEN e.production_qnty ELSE 0 END) as product_qty
					FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d, pro_ex_factory_dtls e,wo_po_color_size_breakdown f
					WHERE
					a.id = c.job_id and
					c.id=d.po_break_down_id  and
					d.id=e.mst_id and
					e.color_size_break_down_id=f.id and
					c.id=f.po_break_down_id and a.company_name=$cbo_company_name  and a.is_deleted =0 and
					a.status_active =1 and d.is_deleted =0 and
					d.status_active =1 and e.is_deleted =0 and
					e.status_active =1 and c.id in ($today_all_po_id)
					group by a.style_ref_no,c.id,f.item_number_id,f.color_number_id";
			foreach (sql_select($ex_fac_sql) as $key => $value) {
				$ex_fac_arr[$value[csf("po_id")]][$value[csf("item_number_id")]][$value[csf("color_number_id")]] = $value[csf("product_qty")];
				$style_wise_sub[$value[csf("style_ref_no")]]["ex_fact"] += $value[csf("product_qty")];
			}
		} else {
			$ex_fac_sql = "SELECT  a.style_ref_no,b.po_break_down_id ,sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as product_qty
					FROM wo_po_details_master a,wo_po_break_down c, pro_ex_factory_mst b WHERE a.job_no=c.job_no_mst and c.id=b.po_break_down_id and  b.is_deleted =0 and
					 b.status_active =1  and c.status_active=1 and c.is_deleted=0 and a.status_active=1  $po_id_arr2
					group by po_break_down_id,a.style_ref_no ";

			foreach (sql_select($ex_fac_sql) as $key => $value) {
				$ex_fac_arr[$value[csf("po_break_down_id")]] += $value[csf("product_qty")];
				$style_wise_sub[$value[csf("style_ref_no")]]["ex_fact"] += $value[csf("product_qty")];
			}
		}
		$sql_pre_cost = "SELECT a.po_break_down_id,a.color_number_id,(b.plan_cut_qnty/a.pcs)*a.requirment  as cons from wo_pre_cost_fabric_cost_dtls c ,wo_pre_cos_fab_co_avg_con_dtls a,wo_po_color_size_breakdown b where c.id=a.PRE_COST_FABRIC_COST_DTLS_ID and a.COLOR_SIZE_TABLE_ID=b.id and b.po_break_down_id=a.po_break_down_id and   a.po_break_down_id <>0 and c.uom=12  $po_chunk_cond  ";
		foreach (sql_select($sql_pre_cost) as $key => $val) {
			$precost_finish_arr[$val[csf("po_break_down_id")]][$val[csf("color_number_id")]] += $val[csf("cons")];
		}
		$budget_sql = "SELECT a.po_break_down_id,b.contrast_color_id ,b.gmts_color_id from wo_pre_cos_fab_co_avg_con_dtls a,wo_pre_cos_fab_co_color_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id $po_chunk_cond";
		foreach (sql_select($budget_sql) as $key => $val) {
			$contrast_wise_gmt[$val[csf("po_break_down_id")]][$val[csf("contrast_color_id")]] = $val[csf("gmts_color_id")];
		}


		$sql_knit_recd = "SELECT po_breakdown_id,color_id,quantity from order_wise_pro_details where  entry_form=37  $po_id_arr3";

		foreach (sql_select($sql_knit_recd) as $key => $val) {
			if (isset($contrast_wise_gmt[$val[csf("po_breakdown_id")]][$val[csf("color_id")]]))
				$knit_recd_arr[$val[csf("po_breakdown_id")]][$contrast_wise_gmt[$val[csf("po_breakdown_id")]][$val[csf("color_id")]]] += $val[csf("quantity")];
			else $knit_recd_arr[$val[csf("po_breakdown_id")]][$val[csf("color_id")]] += $val[csf("quantity")];
		}




		ob_start();
		?>

		<script type="text/javascript">
			setFilterGrid('table_body', -1);
		</script>
		<br> <br> <br>
		<div>
			<table width="3480" cellpadding="0" cellspacing="0">

				<tr>
					<td colspan="52" align="center"><b style="font-size: 21px;">Daily Production Progress Report </b></td>
				</tr>
				<tr>
					<td colspan="52" align="center"><b style="font-size: 21px;"><? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?></b> </td>
				</tr>
				<tr>
					<td colspan="52">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="52" align="left"><b style="font-size: 21px;">Date: <? echo str_replace("'", "",  $txt_production_date); ?> </b></td>
				</tr>
			</table>
			<table class="rpt_table" width="3480" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
				<thead>

					<tr>
						<th width="40" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>SI</p>
						</th>
						<th width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Buyer</p>
						</th>
						<th width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Style</p>
						</th>
						<th width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>PO NO</p>
						</th>
						<th width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Color</p>
						</th>
						<th width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Item</p>
						</th>
						<th width="80" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Order Qty</p>
						</th>

						<th width="80" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Plan Cut Qty</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Fabric Status (KG)</p>
						</th>
						<th width="100" style='word-break:break-all;word-wrap: break-word;'>
							<p>&nbsp;</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Cutting</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Print</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>EMB</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Cutting Issue</p>
						</th>
						<th width="80" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Line</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Sewing Input</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Sewing Output</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Wash</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Iron</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Poly</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Finishing</p>
						</th>
						<th width="120" style='word-break:break-all;word-wrap: break-word;' colspan="2">
							<p>Carton</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Buyer inspection</p>
						</th>
						<th width="80" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Ex-Factory</p>
						</th>
						<th width="80" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Excess</p>
						</th>
						<th width="80" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Short</p>
						</th>
						<th width="80" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Remarks</p>
						</th>

					</tr>

					<tr>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Req</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Rcvd</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Fab Bal</p>
						</th>
						<th width="100" style='word-break:break-all;word-wrap: break-word;'>
							<p>Shipdate</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Day</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>TTL Cut</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Cut Bal</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Sent</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Rcvd</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>WIP</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Sent</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Rcvd</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>WIP</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Day</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Input</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>WIP</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Day</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Input</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>WIP</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Day</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Total</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>WIP</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Sent</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Rcvd</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>WIP</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Day</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Total</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>WIP</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Day</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Total</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>WIP</p>
						</th>

						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Day</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Total</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>WIP</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Day</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Total</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Day</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Total</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>WIP</p>
						</th>
					</tr>


				</thead>
			</table>

			<div style="width:3498px;max-height:400px;overflow-y:scroll;float: left; " id="scroll_body">
				<table class="rpt_table" width="3480" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">

					<?


					$m = 1;
					$jj = 0;
					$gr_wise_buyer_ins_today = 0;
					$gr_wise_buyer_ins_total = 0;
					$gr_wise_buyer_ins_wip = 0;
					$gr_wise_carton_today = 0;
					$gr_wise_carton_total = 0;
					$grand_total_finsish_req = 0;
					$grand_total_finsish_recd = 0;
					$grand_total_finsish_bal = 0;
					$grand_ex_fac_excess = 0;
					$grand_ex_fac_short = 0;
					$grand_sewing_wip = 0;
					$grand_cutting_delivery_wip = 0;

					$grand_poly_today = 0;
					$grand_poly_total = 0;
					$grand_poly_wip = 0;

					$grand_finish_today = 0;
					$grand_finish_total = 0;
					$grand_finish_wip = 0;

					$grand_iron_today = 0;
					$grand_iron_total = 0;
					$grand_iron_wip = 0;


					foreach ($data_array as $style_id => $style_data) {
						$pp = 0;
						$style_wise_finsish_req = 0;
						$style_wise_buyer_ins_today = 0;
						$style_wise_buyer_ins_total = 0;
						$style_wise_buyer_ins_wip = 0;
						$style_wise_carton_today = 0;
						$style_wise_carton_total = 0;

						$style_wise_finsish_recd = 0;
						$style_wise_finsish_bal = 0;
						$style_ex_fac_excess = 0;
						$style_ex_fac_short = 0;

						$style_sewing_wip = 0;
						$style_poly_wip = 0;
						$style_cutting_delivery_wip = 0;
						foreach ($style_data as $item_id => $item_data) {

							foreach ($item_data as $po_id => $po_data) {
								$l = 0;
								$nn = 0;
								foreach ($po_data as $shipment_date => $ship_date_data) {



									foreach ($ship_date_data as  $color_id => $color_data) {
										if ($m % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";
										$jj++;


					?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $jj; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $jj; ?>">

											<?
											if ($pp == 0) {
											?>
												<td width="40" valign="middle" align="center" rowspan="<? echo $style_wise_buyer_span[$style_id]; ?>" style='word-break:break-all;word-wrap: break-word;'>
													<p><? echo $m;
														$m++; ?></p>
												</td>
												<td valign='middle' align="center" width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="<? echo $style_wise_buyer_span[$style_id]; ?>">
													<p><? echo $buyer_arr[$style_wise_buyer_arr[$style_id]]; ?></p>
												</td>
												<td valign='middle' align="center" width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="<? echo $style_wise_buyer_span[$style_id]; ?>">
													<p><? echo $style_id; ?></p>
												</td>
											<?
											}
											if ($l == 0) {


											?>



												<td valign='middle' align="center" width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>">
													<p><? echo $po_lib_arr[$po_id]; ?></p>
												</td>
											<?

											}
											?>



											<td width="100" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_lib[$color_id]; ?></p>
											</td>
											<?
											if ($l == 0) {
											?>
												<td valign='middle' align="center" width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>">
													<p><? echo $garments_item[$item_id]; ?></p>
												</td>

											<?

											}
											$fab_bal = ($knit_recd_arr[$po_id][$color_id]) * 1 - ($precost_finish_arr[$po_id][$color_id]) * 1;
											$sewing_in_wip = $color_data["total_sew_input"] - $color_data["total_cutting_delivery"];
											$swin_bal_color = ($sewing_in_wip < 0) ? " color:red; " : "color:black; ";
											$fab_bal_color = ($fab_bal < 0) ? " color:red; " : "color:black; ";
											$cutting_bal = $color_data["total_cutting"] - $plan_cut_arr[$po_id][$item_id][$color_id]["plan_cut"];
											$cutting_bal_color = ($cutting_bal < 0) ? " color:red; " : "color:black; ";
											$print_bal = ($color_data["total_print_receive"] - $color_data["total_print_issue"]);

											$print_bal_color = ($print_bal < 0) ? " color:red; " : "color:black; ";
											$emb_bal = ($color_data["total_emb_receive"] - $color_data["total_emb_issue"]);
											$emb_bal_color = ($emb_bal < 0) ? " color:red; " : "color:black; ";
											$swout_wip = $color_data["total_sew_out"] - $color_data["total_sew_input"];
											$swout_wip_color = ($swout_wip < 0) ? " color:red; " : "color:black; ";
											$wash_bal = ($color_data["total_wash_receive"] - $color_data["total_wash_issue"]);
											$wash_bal_color = ($wash_bal < 0) ? " color:red; " : "color:black; ";


											$iron_bal = ($color_data["total_wash_receive"]) ? $color_data["total_iron"] - $color_data["total_wash_receive"] : $color_data["total_iron"] - $color_data["total_sew_out"];
											$iron_bal_color = ($iron_bal < 0) ? " color:red; " : "color:black; ";

											// $poly_bal=($color_data["total_poly"]-$color_data["total_iron"]);
											if ($color_data["total_iron"]) {
												$poly_bal = ($color_data["total_poly"] - $color_data["total_iron"]);
											} else if ($color_data["total_wash_receive"]) {
												$poly_bal = ($color_data["total_poly"] - $color_data["total_iron"]);
											} else {
												$poly_bal = ($color_data["total_poly"] - $color_data["total_sew_out"]);
											}

											$poly_bal_color = ($poly_bal < 0) ? " color:red; " : "color:black; ";

											$inspection_bal = ($style_wise_ins_arr[$style_id][$po_id]["total_inspection"] - $style_wise_sub[$style_id]["total_finish"]);
											$inspection_bal_color = ($inspection_bal < 0) ? " color:red; " : "color:black; ";


											$style_wise_finsish_req += $precost_finish_arr[$po_id][$color_id];
											$style_wise_finsish_recd += $knit_recd_arr[$po_id][$color_id];
											$style_wise_finsish_bal += $fab_bal;
											$grand_total_finsish_req += $precost_finish_arr[$po_id][$color_id];
											$grand_total_finsish_recd += $knit_recd_arr[$po_id][$color_id];
											$grand_total_finsish_bal += $fab_bal;
											$style_sewing_wip += $sewing_in_wip;
											$grand_sewing_wip += $sewing_in_wip;
											if (($color_data["total_print_receive"] * 1) > 0 || ($color_data["total_emb_receive"]) * 1 > 0) {
												if (($color_data["total_print_receive"] * 1) > 0 && ($color_data["total_emb_receive"]) * 1 <= 0) {
													$cutting_delivery_wip = ($color_data["total_cutting_delivery"] - $color_data["total_print_receive"]);
													$style_cutting_delivery_wip += $cutting_delivery_wip;
													$grand_cutting_delivery_wip += $cutting_delivery_wip;
													$cutting_delivery_bal_color = ($cutting_delivery_wip < 0) ? " color:red; " : "color:black; ";
												} else {
													$cutting_delivery_wip = ($color_data["total_cutting_delivery"] - $color_data["total_emb_receive"]);
													$style_cutting_delivery_wip += $cutting_delivery_wip;
													$grand_cutting_delivery_wip += $cutting_delivery_wip;
													$cutting_delivery_bal_color = ($cutting_delivery_wip < 0) ? " color:red; " : "color:black; ";
												}
											} else {
												$cutting_delivery_wip = ($color_data["total_cutting_delivery"] - $color_data["total_cutting"]);
												$style_cutting_delivery_wip += $cutting_delivery_wip;
												$grand_cutting_delivery_wip += $cutting_delivery_wip;
												$cutting_delivery_bal_color = ($cutting_delivery_wip < 0) ? " color:red; " : "color:black; ";
											}

											//if($color_data["total_wash_receive"]>0)
											// {
											$finsh_bal = $color_data["total_finish"] - $color_data["total_poly"];
											//}
											//else
											//{
											//	$finsh_bal=  $color_data["total_finish"]-$color_data["total_sew_out"];
											// }
											$finsh_bal_color = ($finsh_bal < 0) ? " color:red; " : "color:black; ";



											?>

											<td width="80" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo   $plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"]; ?></p>
											</td>
											<td width="80" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo  $plan_cut_arr[$po_id][$item_id][$color_id]["plan_cut"]; ?></p>
											</td>
											<td width="60" id="defaidult_zero1" class="default_zero" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? if ($precost_finish_arr[$po_id][$color_id] == "") echo 0;
													else  echo number_format($precost_finish_arr[$po_id][$color_id], 2); ?></p>
											</td>
											<td width="60" id="default_zero2" class="default_zero" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? if ($knit_recd_arr[$po_id][$color_id] == "") echo 0;
													else  echo number_format($knit_recd_arr[$po_id][$color_id], 2); ?></p>
											</td>
											<td width="60" id="default_zero3" class="default_zero" align="center" style='word-break:break-all;word-wrap: break-word; <? echo $fab_bal_color; ?>'>
												<p><? echo number_format($fab_bal, 2); ?></p>
											</td>
											<td width="100" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo change_date_format($shipment_date); ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["today_cutting"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_cutting"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $cutting_bal_color; ?>'>
												<p><? echo $cutting_bal; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_print_issue"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_print_receive"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $print_bal_color; ?>'>
												<p> <? echo $print_bal; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_emb_issue"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_emb_receive"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $emb_bal_color; ?>'>
												<p><? echo $emb_bal;  ?></p>
											</td>
											<td width="60" id="default_zero4" class="default_zero" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? if ($color_data["today_cutting_delivery"] == "") echo 0;
													else   echo $color_data["today_cutting_delivery"]; ?></p>
											</td>
											<td width="60" id="default_zero5" class="default_zero" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? if ($color_data["total_cutting_delivery"] == "") echo 0;
													else echo $color_data["total_cutting_delivery"]; ?></p>
											</td>

											<td width="60" id="default_zero6" class="default_zero" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $cutting_delivery_bal_color; ?>'>
												<p> <? echo $cutting_delivery_wip;  ?> </p>
											</td>

											<?
											if ($l == 0) {
											?>
												<td valign='middle' align="center" width="80" style='word-break:break-all;word-wrap: break-word;' rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>">
													<p>
														<?

														// $line = array_unique(explode(",", $style_po_wise_line[$style_id][$po_id]));

														// $line_name = "";
														// foreach ($line as $v) {
														// 	if ($prod_reso_allo == 1) {
														// 		$line_name .= $line_lib_resource[$v] . ",";
														// 	} else {
														// 		$line_name .= $line_lib[$v] . ",";
														// 	}
														// }
														// echo trim($line_name, ","); 
														echo trim($style_po_wise_line[$style_id][$po_id],','); ?></p> ?></p>
												</td>

											<?

											}
											?>



											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["today_sew_input"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_sew_input"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $swin_bal_color; ?>'>
												<p><? echo  $sewing_in_wip  ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["today_sew_output"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_sew_out"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $swout_wip_color; ?>'>
												<p><? echo $swout_wip; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_wash_issue"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_wash_receive"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $wash_bal_color; ?>'>
												<p><? echo  $wash_bal; ?></p>
											</td>

											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["today_iron"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_iron"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $iron_bal_color; ?>'>
												<p><? echo  $iron_bal; ?></p>
											</td>

											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["today_poly"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_poly"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $poly_bal_color; ?>'>
												<p><? echo  $poly_bal; ?></p>
											</td>
											<?
											$grand_poly_today += $color_data["today_poly"];
											$grand_poly_total += $color_data["total_poly"];
											$grand_poly_wip += $poly_bal;
											$style_poly_wip += $poly_bal;

											$grand_finish_today += $color_data["today_finish"];
											$grand_finish_total += $color_data["total_finish"];
											$grand_finish_wip += $color_data["finsh_bal"];

											$grand_iron_today += $color_data["today_iron"];
											$grand_iron_total += $color_data["total_iron"];
											$grand_iron_wip += $iron_bal;
											?>


											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["today_finish"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_finish"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $finsh_bal_color; ?>'>
												<p>
													<? echo $finsh_bal; ?></p>
											</td>
											<?
											if ($pp == 0) {

												$style_wise_carton_today += $style_wise_ctq_arr[$style_id]["today_cartoon"];
												$style_wise_carton_total += $style_wise_ctq_arr[$style_id]["total_cartoon"];
												$gr_wise_carton_today += $style_wise_ctq_arr[$style_id]["today_cartoon"];
												$gr_wise_carton_total += $style_wise_ctq_arr[$style_id]["total_cartoon"];


											?>
												<td width="60" id="default_zero7" class="default_zero" valign="middle" align="center" rowspan="<? echo $style_wise_buyer_span[$style_id] + 1; ?>" style='word-break:break-all;word-wrap: break-word;'>
													<p><? if ($style_wise_ctq_arr[$style_id]["today_cartoon"] == "") echo 0;
														else echo $style_wise_ctq_arr[$style_id]["today_cartoon"]; ?></p>
												</td>
												<td width="60" id="default_zero8" class="default_zero" valign="middle" align="center" rowspan="<? echo $style_wise_buyer_span[$style_id] + 1; ?>" style='word-break:break-all;word-wrap: break-word;'>
													<p><? if ($style_wise_ctq_arr[$style_id]["total_cartoon"] == "") echo 0;
														else echo $style_wise_ctq_arr[$style_id]["total_cartoon"]; ?></p>
												</td>
											<?
											}

											if ($l == 0) {
												$border_top = "";
												$today_ins = $style_wise_ins_arr[$style_id][$po_id]["today_inspection"];
												$total_ins = $style_wise_ins_arr[$style_id][$po_id]["total_inspection"];
												if ($po_item_style_buyer_ins[$style_id][$po_id] == 1) {
													$today_ins = "";
													$total_ins = "";
													$inspection_bal = "";
													$border_top = " border-top:2px solid $bgcolor;";
												}
												$style_wise_buyer_ins_today += $today_ins;
												$style_wise_buyer_ins_total += $total_ins;
												$style_wise_buyer_ins_wip += $inspection_bal;
												$gr_wise_buyer_ins_today += $today_ins;
												$gr_wise_buyer_ins_total += $total_ins;
												$gr_wise_buyer_ins_wip += $inspection_bal;


											?>

												<td width="60" id="default_zero9" class="default_zero" rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>" valign='middle' align="center" style='word-break:break-all;word-wrap: break-word;<? echo $border_top; ?>'>
													<p><? if ($today_ins == "") echo 0;
														else echo $today_ins; ?></p>
												</td> <? $new_style_id = "'" . $style_id . "'"; ?>
												<td width="60" id="default_zero10" class="default_zero" rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>" valign='middle' align="center" style='word-break:break-all;word-wrap: break-word;<? echo $border_top; ?>'><a href="##" onClick="openmypage_buyer_ins(<? echo $new_style_id; ?>, <? echo $po_id; ?>,'remarks_popup');"><? if ($total_ins == "") echo 0;
																																																																																																		else echo $total_ins; ?></a> </td>
												<td id="default_zero11" class="default_zero" width="60" rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>" valign='middle' align="center" style='word-break:break-all;word-wrap: break-word;<? echo $border_top; ?>'>
													<p>
														<? if ($inspection_bal == "") echo 0;
														else echo $inspection_bal; ?></p>
												</td>
											<?
												$po_item_style_buyer_ins[$style_id][$po_id] = 1;
											}
											$nn++;
											$pp++;
											?>



											<?
											if ($production_level != 1) {
											?>
												<td width="80" id="default_zero12" class="default_zero" align="center" style='word-break:break-all;word-wrap: break-word;'>
													<p> <? if ($ex_fac_arr[$po_id][$item_id][$color_id] == "") echo 0;
														else echo $ex_fac_arr[$po_id][$item_id][$color_id];
														$excess_qnty = ($plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"]) - ($ex_fac_arr[$po_id][$item_id][$color_id]) * 1;
														// echo $excess_qnty.'=excess cut';
														if ($excess_qnty < 0) {
															$excess = $excess_qnty;
															$excess_color = " color:red;";
															$style_ex_fac_excess += $excess;
															$grand_ex_fac_excess += $excess;
															$short = 0;

														} else {
															$excess_color = "";
															$short = $excess_qnty;
															$excess = "";
															$style_ex_fac_short += $short;

															$style_ex_fac_short += $short;

														}


														?></p>
												</td>
												<td class="default_zero" id="default_zero13" width="80" align="center" style="word-break:break-all;word-wrap: break-word;<? echo  $excess_color; ?>""  ><p><? if ($excess == "") echo 0;
																																																			else echo  $excess; ?></p></td>
						                   		     <td width=" 80" align="center" style='word-break:break-all;word-wrap: break-word;'>
													<p><? echo  $short; ?></p>
												</td>
												<?
											} else {
												if ($l == 0) {
													$excess_qnty = ($plan_cut_arr_gross[$po_id]["order_quantity"]) - ($ex_fac_arr[$po_id]) * 1;
													if ($excess_qnty < 0) {
														$excess = $excess_qnty;
														$excess_color = " color:red;";
														$style_ex_fac_excess += $excess;
														$grand_ex_fac_excess += $excess;
														$short = 0;
													} else {
														$excess_color = "";
														$short = $excess_qnty;
														$excess = "";
														$style_ex_fac_short += $short;
														$grand_ex_fac_short += $short;
													}

												?>
													<td width="80" align="center" rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>" style='word-break:break-all;word-wrap: break-word;'>
														<p> <? echo $ex_fac_arr[$po_id]; ?>
														</p>
													</td>
													<td width="80" align="center" rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>" style="word-break:break-all;word-wrap: break-word;<? echo  $excess_color; ?>""  ><p><? echo  $excess; ?></p></td>
								                   		     <td width=" 80" align="center" rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>" style='word-break:break-all;word-wrap: break-word;'>
														<p><? echo  $short; ?></p>
													</td>
													<td width="80" align="center" rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>" style="word-break:break-all;word-wrap: break-word;<? echo  $excess_color; ?>""  ><p><? echo  $excess; ?></p></td>



											<?
												}
											}
											?>
											<td width="80" style='word-break:break-all;word-wrap: break-word;' align="center">
												<p>
													<?
													$ship_status = $color_data["shiping_status"];
													// $ship_status_value=($ship_status==2 || $ship_status==1)? " Pending " : " Full Shipment";
													echo $shipment_status[$ship_status];

													?>

												</p>
											</td>

										</tr>


						<?

										$l++;
									}
								}
							}
						}
						$style_cutting_bal = $style_wise_sub[$style_id]["total_cutting"] - $style_wise_sub[trim($style_id)]["plan_cut"];
						$style_cutting_bal_color = ($style_cutting_bal < 0) ? " color:red; " : "color:black; ";
						$style_wise_finsish_bal_color = ($style_wise_finsish_bal < 0) ? " color:red; " : "color:black; ";
						$style_wise_print_bal_color = ($style_wise_sub[$style_id]["print_wip"] < 0) ? " color:red; " : "color:black; ";
						$style_wise_sewout_bal_color = ($style_wise_sub[$style_id]["sewout_wip"] < 0) ? " color:red; " : "color:black; ";
						$style_wise_emb_bal_color = ($style_wise_sub[$style_id]["emb_wip"] < 0) ? " color:red; " : "color:black; ";
						$style_wise_cutting_delivery_bal_color = ($style_cutting_delivery_wip < 0) ? " color:red; " : "color:black; ";
						$style_wise_swin_bal_color = ($style_sewing_wip < 0) ? " color:red; " : "color:black; ";
						$style_wise_wash_bal_color = ($style_wise_sub[$style_id]["wash_wip"] < 0) ? " color:red; " : "color:black; ";
						$style_wise_iron_bal_color = ($style_wise_sub[$style_id]["iron_wip"] < 0) ? " color:red; " : "color:black; ";
						$style_wise_poly_bal_color = ($style_poly_wip < 0) ? " color:red; " : "color:black; ";









						?>
						<tr bgcolor="#EAEAEA" onClick="change_color('tr222_<? echo $jj; ?>', '<? echo $bgcolor; ?>')" id="tr222_<? echo $jj; ?>">
							<th colspan="6" align="center" style='word-break:break-all;word-wrap: break-word;'>
								<p> Style Total</p>
								</td>
							<th align="center" width="80" style='word-break:break-all;word-wrap: break-word;'>
								<p><? echo $style_wise_sub[trim($style_id)]["order_quantity"]; ?> </p>
								</td>
							<th align="center" width="80" style='word-break:break-all;word-wrap: break-word;'>
								<p><? echo $style_wise_sub[trim($style_id)]["plan_cut"]; ?> </p>
								</td>

							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
								<p><? if ($style_wise_finsish_req == "") echo 0;
									else echo number_format($style_wise_finsish_req, 2); ?> </p>
							</th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
								<p><? if ($style_wise_finsish_recd == "") echo 0;
									else echo number_format($style_wise_finsish_recd, 2); ?> </p>
							</th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_finsish_bal_color; ?>'>
								<p><? echo number_format($style_wise_finsish_bal, 2); ?> </p>
							</th>
							<th style='word-break:break-all;word-wrap: break-word;'>
								<p>&nbsp;</p>
							</th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["today_cutting"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_cutting"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_cutting_bal_color; ?>'> <? echo $style_cutting_bal; ?> </th>

							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_print_issue"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_print_receive"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_print_bal_color; ?>'> <? echo $style_wise_sub[$style_id]["print_wip"]; ?> </th>


							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_emb_issue"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_emb_receive"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_emb_bal_color; ?>'> <? echo $style_wise_sub[$style_id]["emb_wip"]; ?> </th>


							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? if ($style_wise_sub[$style_id]["today_cutting_delivery"] == "") echo 0;
																												else echo $style_wise_sub[$style_id]["today_cutting_delivery"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? if ($style_wise_sub[$style_id]["total_cutting_delivery"] == "") echo 0;
																												else echo $style_wise_sub[$style_id]["total_cutting_delivery"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_cutting_delivery_bal_color; ?>'> <? echo $style_cutting_delivery_wip; ?> </th>
							<th width="80">&nbsp;</th>

							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["today_sew_input"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_sew_input"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_swin_bal_color; ?>'> <? echo $style_sewing_wip; ?> </th>


							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["today_sew_output"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_sew_out"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_sewout_bal_color; ?>'> <? echo $style_wise_sub[$style_id]["sewout_wip"]; ?> </th>


							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_wash_issue"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_wash_receive"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_wash_bal_color; ?>'> <? echo $style_wise_sub[$style_id]["wash_wip"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["today_iron"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_iron"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_iron_bal_color; ?>'> <? echo $style_wise_sub[$style_id]["iron_wip"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["today_poly"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_poly"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_poly_bal_color; ?>'> <? echo $style_poly_wip; ?> </th>

							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["today_finish"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_finish"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_finish_bal_color; ?>'> <? echo $style_wise_sub[$style_id]["finish_wip"]; ?> </th>

							<th width="60"> <? echo $style_wise_buyer_ins_today; ?> </th>
							<th width="60"> <? echo $style_wise_buyer_ins_total; ?> </th>
							<th width="60"> <? echo $style_wise_buyer_ins_wip; ?> </th>

							<th width="80" align="center" style='word-break:break-all;word-wrap: break-word;'> <? if ($style_wise_sub[$style_id]["ex_fact"] == "") echo 0;
																												else echo $style_wise_sub[$style_id]["ex_fact"]; ?> </th>
							<th width="80" align="center">
								<p> <? echo $style_ex_fac_excess; ?> </p>
							</th>
							<th width="80" align="center">
								<p> <? echo $style_ex_fac_short; ?> </p>
							</th>


							<th width="80">&nbsp;</th>

						</tr>

					<?
					}

					$gr_order_qnty = 0;
					$gr_plan_qnty = 0;
					$gr_today_cutting = 0;
					$gr_total_cutting = 0;
					$gr_total_print_issue = 0;
					$gr_total_print_receive = 0;
					$gr_total_emb_issue = 0;
					$gr_print_wip = 0;
					$gr_emb_wip = 0;
					$gr_total_emb_receive = 0;
					$gr_today_cutting_delivery = 0;
					$gr_total_cutting_delivery = 0;
					$gr_today_sew_input = 0;
					$gr_total_sew_input = 0;
					$gr_sewin_wip = 0;
					$gr_today_sew_output = 0;
					$gr_total_sew_out = 0;
					$gr_sewout_wip = 0;
					$gr_total_wash_issue = 0;
					$gr_total_wash_receive = 0;
					$gr_wash_wip = 0;
					$gr_today_finish = 0;
					$gr_total_finish = 0;
					$gr_finish_wip = 0;
					$gr_ex_fact = 0;

					foreach ($style_wise_sub as $key => $vals) {
						$gr_order_qnty += $vals["order_quantity"];
						$gr_plan_qnty += $vals["plan_cut"];
						$gr_today_cutting += $vals["today_cutting"];
						$gr_total_cutting += $vals["total_cutting"];
						$gr_total_print_issue += $vals["total_print_issue"];
						$gr_total_print_receive += $vals["total_print_receive"];
						$gr_print_wip += $vals["print_wip"];
						$gr_emb_wip += $vals["emb_wip"];
						$gr_total_emb_issue += $vals["total_emb_issue"];
						$gr_total_emb_receive += $vals["total_emb_receive"];
						$gr_today_cutting_delivery += $vals["today_cutting_delivery"];
						$gr_total_cutting_delivery += $vals["total_cutting_delivery"];
						$gr_today_sew_input += $vals["today_sew_input"];
						$gr_total_sew_input += $vals["total_sew_input"];
						$gr_sewin_wip += $vals["sewin_wip"];
						$gr_today_sew_output += $vals["today_sew_output"];
						$gr_total_sew_out += $vals["total_sew_out"];
						$gr_sewout_wip += $vals["sewout_wip"];
						$gr_total_wash_issue += $vals["total_wash_issue"];
						$gr_total_wash_receive += $vals["total_wash_receive"];
						$gr_wash_wip += $vals["wash_wip"];
						$gr_today_finish += $vals["today_finish"];
						$gr_total_finish += $vals["total_finish"];
						$gr_finish_wip += $vals["finish_wip"];
						$gr_ex_fact += $vals["ex_fact"];
					}

					$gr_cutting_bal_color = ($gr_total_cutting - $gr_plan_qnty < 0) ? " color:red; " : "color:black; ";
					$gr_print_wip_color = ($gr_print_wip < 0) ? " color:red; " : "color:black; ";
					$gr_emb_wip_color = ($gr_emb_wip < 0) ? " color:red; " : "color:black; ";
					$grand_cutting_delivery_wip_color = ($grand_cutting_delivery_wip < 0) ? " color:red; " : "color:black; ";
					$grand_sewing_wip_color = ($grand_sewing_wip < 0) ? " color:red; " : "color:black; ";
					$gr_sewout_wip_color = ($gr_sewout_wip < 0) ? " color:red; " : "color:black; ";
					$gr_wash_wip_color = ($gr_wash_wip < 0) ? " color:red; " : "color:black; ";
					$gr_finish_wip_color = ($gr_finish_wip < 0) ? " color:red; " : "color:black; ";
					$grand_total_finsish_bal_color = ($grand_total_finsish_bal < 0) ? " color:red; " : "color:black; ";
					$gr_iron_wip_color = ($grand_iron_wip < 0) ? " color:red; " : "color:black; ";
					$gr_poly_wip_color = ($grand_poly_wip < 0) ? " color:red; " : "color:black; ";
					$gr_finish_wip_color = ($grand_finish_wip < 0) ? " color:red; " : "color:black; ";


					?>

					<tr bgcolor="#A4C2EA" onClick="change_color('tr333_<? echo $jj; ?>', '<? echo $bgcolor; ?>')" id="tr333_<? echo $jj; ?>">
						<th colspan="6" align="center" style='word-break:break-all;word-wrap: break-word;'>
							<p> Grand Total</p>
							</td>
						<th align="center" width="80" style='word-break:break-all;word-wrap: break-word;'>
							<p><? echo $gr_order_qnty; ?> </p>
							</td>
						<th align="center" width="80" style='word-break:break-all;word-wrap: break-word;'>
							<p><? echo $gr_plan_qnty; ?> </p>
							</td>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
							<p> <? echo number_format($grand_total_finsish_req, 2); ?> </p>
						</th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
							<p> <? echo number_format($grand_total_finsish_recd, 2); ?> </p>
						</th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $grand_total_finsish_bal_color; ?>'>
							<p> <? echo number_format($grand_total_finsish_bal, 2); ?> </p>
						</th>
						<th style='word-break:break-all;word-wrap: break-word;'>&nbsp; </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_today_cutting; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_cutting; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $gr_cutting_bal_color; ?>'> <? echo  $gr_total_cutting - $gr_plan_qnty; ?> </th>

						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_print_issue; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_print_receive; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $gr_print_wip_color; ?>'> <? echo $gr_print_wip; ?> </th>


						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_emb_issue; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_emb_receive; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $gr_emb_wip_color; ?>'> <? echo $gr_emb_wip; ?> </th>


						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_today_cutting_delivery; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_cutting_delivery; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $grand_cutting_delivery_wip_color; ?>'> <? echo $grand_cutting_delivery_wip; ?> </th>
						<th width="80">&nbsp;</th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_today_sew_input; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_sew_input; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $grand_sewing_wip_color; ?>'> <? echo $grand_sewing_wip; ?> </th>


						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_today_sew_output; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_sew_out; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $gr_sewout_wip_color; ?>'> <? echo $gr_sewout_wip; ?> </th>


						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_wash_issue; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_wash_receive; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $gr_wash_wip_color; ?>'> <? echo $gr_wash_wip; ?> </th>

						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $grand_iron_today; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $grand_iron_total; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $gr_iron_wip_color; ?>'> <? echo $grand_iron_wip; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $grand_poly_today; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $grand_poly_total; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $gr_poly_wip_color; ?>'> <? echo $grand_poly_wip; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $grand_finish_today; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $grand_finish_total; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $gr_finish_wip_color; ?>'> <? echo $grand_finish_wip; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_wise_carton_today; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'><? echo $gr_wise_carton_total; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_wise_buyer_ins_today; ?></th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_wise_buyer_ins_total; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_wise_buyer_ins_wip; ?> </th>
						<th width="80" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_ex_fact; ?> </th>
						<th width="80" align="center">
							<p> <? echo $grand_ex_fac_excess; ?> </p>
						</th>
						<th width="80" align="center">
							<p> <? echo $grand_ex_fac_short; ?> </p>
						</th>
						</th>

						<th width="80">&nbsp;</th>

					</tr>


				</table>

			</div>


		</div>



		<?
		foreach (glob("$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}
		$name = time();
		$filename = $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		echo "$total_data####$filename";
		exit();
	} else if ($type == 3) //Show3
	{
		$cbo_company_name = str_replace("'", "", $cbo_company_name);
		$cbo_year = str_replace("'", "", $cbo_year_selection);
		if ($db_type == 2) {
			$year_cond = ($cbo_year) ? " and to_char(b.insert_date,'YYYY')=$cbo_year" : "";
		} else {
			$year_cond = ($cbo_year) ? " and year(b.insert_date)=$cbo_year" : "";
		}
		$order_cond = "";
		$order_cond2 = "";
		$job_cond_id2 = "";
		$jobs = explode(",", str_replace("'", "", $txt_job_no));
		$jobs_id = "'" . implode("','", $jobs) . "'";
		if (str_replace("'", "", $hidden_job_id))  $job_cond_id2 = "and b.id in(" . str_replace("'", "", $hidden_job_id) . ")";

		if (str_replace("'", "", $txt_job_no))  $job_no_cond2 = " and b.job_no_prefix_num in($jobs_id)";

		if (str_replace("'", "", $hidden_order_id)) {
			$order_cond2 = " and c.id in (" . str_replace("'", "", $hidden_order_id) . ")";
		}

		if (str_replace("'", "", $txt_order_no))  $order_no_cond2 = "and c.po_number =(" . trim($txt_order_no) . ")";
		//if (str_replace("'","",$txt_order_no)=="") $order_cond=""; else $order_cond=" and c.po_number like '%".str_replace("'","",$txt_order_no)."%' ";
		$shipping_status_cond = "";
		if (str_replace("'", "", $cbo_status) == 3) $shipping_status_cond = " and c.shiping_status=3";
		else if (str_replace("'", "", $cbo_status) == 2) $shipping_status_cond = " and c.shiping_status=2";
		else if (str_replace("'", "", $cbo_status) == 1) $shipping_status_cond = " and c.shiping_status=1";
		else $shipping_status_cond = "";

		if (str_replace("'", "", trim($txt_date_from)) == "" || str_replace("'", "", trim($txt_date_to)) == "") $country_ship_date = "";
		else $country_ship_date = " and e.country_ship_date between $txt_date_from and $txt_date_to";


		$prod_reso_allo = return_field_value("auto_update", "variable_settings_production", "company_name='$cbo_company_name' and variable_list=23 and is_deleted=0 and status_active=1");
		/*if(str_replace("'", "", $txt_order_no)=="" )
		{
			$order_cond2="";
		}*/
		if (str_replace("'", "", $txt_job_no) == "") {
			$job_cond_id2 = "";
		}


		$companyArr = return_library_array("SELECT id,company_name FROM lib_company WHERE status_active=1 and is_deleted=0 and id='$cbo_company_name' ", "id", "company_name");

		$line_lib = return_library_array("SELECT id,line_name from lib_sewing_line where company_name='$cbo_company_name'", "id", "line_name");
		if ($prod_reso_allo == 1) {

			$line_libr = "SELECT id,line_number from prod_resource_mst where company_id='$cbo_company_name' and is_deleted=0 ";
			foreach (sql_select($line_libr) as $row) {
				$line = '';
				$line_number = explode(",", $row[csf('line_number')]);
				foreach ($line_number as $val) {
					if ($line == '') $line = $line_lib[$val];
					else $line .= "," . $line_lib[$val];
				}
				$line_lib_resource[$row[csf('id')]] = $line;
			}
		}
		$color_lib = return_library_array("SELECT id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		if (str_replace("'", "", $txt_production_date) != "") {
			if ($db_type == 0) {
				$production_date = change_date_format(str_replace("'", "", $txt_production_date), "yyyy-mm-dd", "");
			} else if ($db_type == 2) {
				$production_date = change_date_format(str_replace("'", "", $txt_production_date), "", "", 1);
			}
			$date_cond = " and a.production_date='$production_date'";
		}

		if ($db_type == 0) $group_concat = "group_concat(distinct(a.sewing_line)) as sewing_line";
		if ($db_type == 2) $group_concat = "listagg(a.sewing_line,',') within group (order by a.sewing_line) as sewing_line";



		$sql = "SELECT b.style_ref_no,b.buyer_name ,a.po_break_down_id,c.po_number,a.item_number_id,e.color_number_id,c.shipment_date,c.shiping_status,a.sewing_line ,
    	sum( case when a.production_type=4 and d.production_type=4 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_sew_input,


    	sum( case when a.production_type=5 and d.production_type=5 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_sew_output,


    	sum( case when a.production_type=1 and d.production_type=1 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_cutting,



    	sum( case when a.production_type=2 and d.production_type=2  and a.embel_name=1 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_print_issue,

    	sum( case when a.production_type=2 and d.production_type=2  and a.embel_name=2 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_emb_issue,

    	sum( case when a.production_type=2 and d.production_type=2  and a.embel_name=3 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_wash_issue,



    	sum( case when a.production_type=3 and d.production_type=3  and a.embel_name=1 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_print_receive,


    	sum( case when a.production_type=3 and d.production_type=3  and a.embel_name=2 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_emb_receive,



    	sum( case when a.production_type=3 and d.production_type=3  and a.embel_name=3 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_wash_receive,


    	sum( case when a.production_type=8 and d.production_type=8   and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_finish
    	,
    	sum( case when a.production_type=7 and d.production_type=7   and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_iron
    	,
    	sum( case when a.production_type=80 and d.production_type=80   and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_finishing_entry,

    	sum( case when a.production_type=11 and d.production_type=11   and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_poly
    	,
    	sum( case when a.production_type=4 and d.production_type=4  then d.production_qnty else 0 end ) as total_sew_input,


    	sum( case when a.production_type=5 and d.production_type=5  then d.production_qnty else 0 end ) as total_sew_out,

    	sum( case when a.production_type=1 and d.production_type=1  then d.production_qnty else 0 end ) as total_cutting,

    	sum( case when a.production_type=2 and d.production_type=2 and a.embel_name=1 then d.production_qnty else 0 end ) as total_print_issue,

    	sum( case when a.production_type=2 and d.production_type=2 and a.embel_name=2 then d.production_qnty else 0 end ) as total_emb_issue,

		sum( case when a.production_type=2 and d.production_type=2 and a.embel_name=4 then d.production_qnty else 0 end ) as total_special_work_issue,

    	sum( case when a.production_type=2 and d.production_type=2 and a.embel_name=3 then d.production_qnty else 0 end ) as total_wash_issue,

    	sum( case when a.production_type=3 and d.production_type=3 and a.embel_name=1 then d.production_qnty else 0 end ) as total_print_receive,

    	sum( case when a.production_type=3 and d.production_type=3 and a.embel_name=2 then d.production_qnty else 0 end ) as total_emb_receive,

		sum( case when a.production_type=3 and d.production_type=3 and a.embel_name=4 then d.production_qnty else 0 end ) as total_special_work_rcv,

    	sum( case when a.production_type=3 and d.production_type=3 and a.embel_name=3 then d.production_qnty else 0 end ) as total_wash_receive,

    	sum( case when a.production_type=8 and d.production_type=8  then d.production_qnty else 0 end ) as total_finish,
    	sum( case when a.production_type=7 and d.production_type=7  then d.production_qnty else 0 end ) as total_iron,
    	sum( case when a.production_type=11 and d.production_type=11  then d.production_qnty else 0 end ) as total_poly,
    	sum( case when a.production_type=80 and d.production_type=80  then d.production_qnty else 0 end ) as total_finishing_entry

       FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a, pro_garments_production_dtls d, wo_po_color_size_breakdown e  WHERE b.job_no = c.job_no_mst  and c.id = a.po_break_down_id and a.id = d.mst_id   and d.color_size_break_down_id = e.id and a.po_break_down_id=e.po_break_down_id and a.status_active = 1 and a.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0  and c.status_active = 1 and c.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0
           $company_name  $buyer_name $job_cond_id2 $job_no_cond2 $order_no_cond2   $shipping_status_cond  $order_cond2 $country_ship_date  $year_cond $brand_name_cond $season_name_cond $season_year_cond
          group by b.style_ref_no ,b.buyer_name ,a.po_break_down_id,c.po_number,a.item_number_id,e.color_number_id ,c.shipment_date ,c.shiping_status,a.sewing_line ";
		//echo $sql;die;
		$production_data = sql_select($sql);
		$style_po_wise_line = array();
		foreach ($production_data as $vals) {
			if ($style_po_wise_line[$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]] == "") {
				$style_po_wise_line[$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]] .= $vals[csf("sewing_line")];
			} else {
				$style_po_wise_line[$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]] .= ',' . $vals[csf("sewing_line")];
			}
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_sew_input"] += $vals[csf("today_sew_input")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_sew_output"] += $vals[csf("today_sew_output")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_cutting"] += $vals[csf("today_cutting")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_print_issue"] += $vals[csf("today_print_issue")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_emb_issue"] += $vals[csf("today_emb_issue")];



			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_wash_issue"] += $vals[csf("today_wash_issue")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_print_receive"] += $vals[csf("today_print_receive")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_emb_receive"] += $vals[csf("today_emb_receive")];



			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_wash_receive"] += $vals[csf("today_wash_receive")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_finish"] += $vals[csf("today_finish")];
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_iron"] += $vals[csf("today_iron")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_finishing_entry"] += $vals[csf("today_finishing_entry")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_poly"] += $vals[csf("today_poly")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["sewing_line"] = $vals[csf("sewing_line")];
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["shiping_status"] = $vals[csf("shiping_status")];

			$style_wise_sub[$vals[csf("style_ref_no")]]["today_sew_input"] += $vals[csf("today_sew_input")];

			$style_wise_sub[$vals[csf("style_ref_no")]]["today_sew_output"] += $vals[csf("today_sew_output")];

			$style_wise_sub[$vals[csf("style_ref_no")]]["today_cutting"] += $vals[csf("today_cutting")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["today_finish"] += $vals[csf("today_finish")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["today_iron"] += $vals[csf("today_iron")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["today_finishing_entry"] += $vals[csf("today_finishing_entry")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["today_poly"] += $vals[csf("today_poly")];
			$today_all_po_arr[$vals[csf("po_break_down_id")]] = $vals[csf("po_break_down_id")];
			$style_wise_buyer_arr[$vals[csf("style_ref_no")]] = $vals[csf("buyer_name")];
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_sew_input"] += $vals[csf("total_sew_input")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_sew_out"] += $vals[csf("total_sew_out")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_cutting"] += $vals[csf("total_cutting")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_print_issue"] += $vals[csf("total_print_issue")];
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_emb_issue"] += $vals[csf("total_emb_issue")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_special_work_issue"] += $vals[csf("total_special_work_issue")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_wash_issue"] += $vals[csf("total_wash_issue")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_print_receive"] += $vals[csf("total_print_receive")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_emb_receive"] += $vals[csf("total_emb_receive")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_special_work_rcv"] += $vals[csf("total_special_work_rcv")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_wash_receive"] += $vals[csf("total_wash_receive")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_finish"] += $vals[csf("total_finish")];
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_poly"] += $vals[csf("total_poly")];
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_iron"] += $vals[csf("total_iron")];
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_finishing_entry"] += $vals[csf("total_finishing_entry")];

			$style_wise_sub[$vals[csf("style_ref_no")]]["total_sew_input"] += $vals[csf("total_sew_input")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_sew_out"] += $vals[csf("total_sew_out")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_cutting"] += $vals[csf("total_cutting")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_print_issue"] += $vals[csf("total_print_issue")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_print_receive"] += $vals[csf("total_print_receive")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_wash_issue"] += $vals[csf("total_wash_issue")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_wash_receive"] += $vals[csf("total_wash_receive")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_emb_issue"] += $vals[csf("total_emb_issue")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_emb_receive"] += $vals[csf("total_emb_receive")];

			$style_wise_sub[$vals[csf("style_ref_no")]]["total_special_work_issue"] += $vals[csf("total_special_work_issue")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_special_work_rcv"] += $vals[csf("total_special_work_rcv")];

			$style_wise_sub[$vals[csf("style_ref_no")]]["total_finish"] += $vals[csf("total_finish")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_iron"] += $vals[csf("total_iron")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_finishing_entry"] += $vals[csf("total_finishing_entry")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_poly"] += $vals[csf("total_poly")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["finish_wip"] += ($vals[csf("total_finish")] - $vals[csf("total_poly")]);
			if ($vals[csf("total_wash_receive")] > 0) {

				$style_wise_sub[$vals[csf("style_ref_no")]]["iron_wip"] += ($vals[csf("total_iron")] - $vals[csf("total_wash_receive")]);
			} else {
				$style_wise_sub[$vals[csf("style_ref_no")]]["iron_wip"] += ($vals[csf("total_iron")] - $vals[csf("total_sew_out")]);
			}

			$style_wise_sub[$vals[csf("style_ref_no")]]["wash_wip"] += ($vals[csf("total_wash_receive")] - $vals[csf("total_wash_issue")]);

			$style_wise_sub[$vals[csf("style_ref_no")]]["finishing_wip"] += ($vals[csf("total_finishing_entry")] - $vals[csf("today_finishing_entry")]);
			// $style_wise_sub[$vals[csf("style_ref_no")]]["finishing_wip"]+= 12;

			$style_wise_sub[$vals[csf("style_ref_no")]]["poly_wip"] += ($vals[csf("total_poly")] - $vals[csf("total_iron")]);

			$style_wise_sub[$vals[csf("style_ref_no")]]["sewin_wip"] += ($vals[csf("total_sew_input")] - $vals[csf("total_print_receive")]);
			$style_wise_sub[$vals[csf("style_ref_no")]]["sewout_wip"] += ($vals[csf("total_sew_out")] - $vals[csf("total_sew_input")]);
			$style_wise_sub[$vals[csf("style_ref_no")]]["print_wip"] += ($vals[csf("total_print_receive")] - $vals[csf("total_print_issue")]);
			$style_wise_sub[$vals[csf("style_ref_no")]]["emb_wip"] += ($vals[csf("total_emb_receive")] - $vals[csf("total_emb_issue")]);

			$style_wise_sub[$vals[csf("style_ref_no")]]["special_work_wip"] += ($vals[csf("total_special_work_rcv")] - $vals[csf("total_special_work_issue")]);
		}
		//  print_r($style_po_wise_line);
		// echo "<pre>";
		// print_r($style_wise_sub);die;
		$today_all_po_id = implode(",",  $today_all_po_arr);
		$all_po_arr = explode(",", $today_all_po_id);
		$po_chunk_cond = "";
		if ($db_type == 2 and count($all_po_arr) > 999) {

			$all_po_arr = array_chunk($all_po_arr, 999);
			foreach ($all_po_arr as $key => $val) {
				$values = implode(",", $val);
				if ($po_chunk_cond == "") {
					$po_chunk_cond = " and ( a.po_break_down_id in ($values) ";
				} else {
					$po_chunk_cond .= " or a.po_break_down_id in ($values) ) ";
				}
			}
		} else {
			$po_chunk_cond = " and a.po_break_down_id in ($today_all_po_id) ";
		}
		$new_po_cond = str_replace("a.po_break_down_id", "b.id", $po_chunk_cond);
		$style_wise_ctq_arr = array();
		$style_wise_ctq_sql = "SELECT a.style_ref_no,sum(case when c.production_date=$txt_production_date then c.carton_qty else 0 end ) as today_cartoon,sum(c.carton_qty) as total_cartoon from wo_po_details_master a,wo_po_break_down b,pro_garments_production_mst c where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  c.production_type=8 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id $new_po_cond group by  a.style_ref_no";
		foreach (sql_select($style_wise_ctq_sql) as $keys => $vals) {
			$style_wise_ctq_arr[$vals[csf("style_ref_no")]]["today_cartoon"] += $vals[csf("today_cartoon")];
			$style_wise_ctq_arr[$vals[csf("style_ref_no")]]["total_cartoon"] += $vals[csf("total_cartoon")];
		}

		// Woven Carton Qnty Sql

		$style_wise_woven_ctq_arr = array();
		$style_wise_woven_ctq_sql = " SELECT a.style_ref_no,
		SUM (
			CASE
				WHEN d.EX_FACTORY_DATE = $txt_production_date THEN d.carton_qnty
				ELSE 0
			END)
			AS today_cartoon,
		SUM (d.carton_qnty)
			AS total_cartoon
		FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c, pro_ex_factory_mst d
		where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and
		 d.status_active = 1
        and d.is_deleted = 0 and  c.production_type=8 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and  b.id = d.po_break_down_id $new_po_cond group by  a.style_ref_no";
		// echo $style_wise_woven_ctq_sql;die;
		foreach (sql_select($style_wise_woven_ctq_sql) as $keys => $vals) {
			$style_wise_woven_ctq_arr[$vals[csf("style_ref_no")]]["today_cartoon"] += $vals[csf("today_cartoon")];
			$style_wise_woven_ctq_arr[$vals[csf("style_ref_no")]]["total_cartoon"] += $vals[csf("total_cartoon")];
		}
		// echo "<pre>";
		// print_r($style_wise_woven_ctq_arr);

		$new_po_cond2 = str_replace("a.po_break_down_id", "c.po_break_down_id", $po_chunk_cond);
		$style_wise_ins_arr = array();
		$po_wise_ins_sql = "SELECT a.style_ref_no,b.id, sum(case when inspection_date=$txt_production_date then inspection_qnty else 0 end ) as today_inspection,sum(inspection_qnty) as total_inspection from wo_po_details_master a,wo_po_break_down b,pro_buyer_inspection c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and  a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  $new_po_cond2 group by a.style_ref_no,b.id ";
		foreach (sql_select($po_wise_ins_sql) as $keys => $vals) {
			$style_wise_ins_arr[$vals[csf("style_ref_no")]][$vals[csf("id")]]["today_inspection"] += $vals[csf("today_inspection")];
			$style_wise_ins_arr[$vals[csf("style_ref_no")]][$vals[csf("id")]]["total_inspection"] += $vals[csf("total_inspection")];
		}

		//echo $po_chunk_cond;die;

		// query for cutting delivery to input challan
		$cutting_delevery_sql = "SELECT b.style_ref_no ,a.po_break_down_id,c.shipment_date,c.po_number,a.item_number_id,e.color_number_id,
    	sum( case when a.production_type=9 and d.production_type=9   and a.cut_delivery_date=$txt_production_date then d.production_qnty else 0 end ) as today_cutting_delivery,
    	sum( case when a.production_type=9 and d.production_type=9 then d.production_qnty else 0 end ) as total_cutting_delivery

       FROM wo_po_details_master b, wo_po_break_down c,pro_cut_delivery_order_dtls a, pro_cut_delivery_color_dtls d, wo_po_color_size_breakdown e  WHERE b.job_no = c.job_no_mst  and c.id = a.po_break_down_id and a.id = d.mst_id   and d.color_size_break_down_id = e.id and a.po_break_down_id=e.po_break_down_id and a.status_active = 1 and a.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0  and c.status_active = 1 and c.is_deleted = 0
          and d.color_size_break_down_id is not null and d.color_size_break_down_id <> 0 $company_name  $buyer_name $job_no_cond2   $shipping_status_cond $order_cond $order_cond2  $po_chunk_cond    group by b.style_ref_no ,a.po_break_down_id,c.po_number,a.item_number_id,e.color_number_id ,c.shipment_date";

		foreach (sql_select($cutting_delevery_sql) as $vals) {
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_cutting_delivery"] += $vals[csf("today_cutting_delivery")];
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_cutting_delivery"] += $vals[csf("total_cutting_delivery")];

			$style_wise_sub[$vals[csf("style_ref_no")]]["today_cutting_delivery"] += $vals[csf("today_cutting_delivery")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_cutting_delivery"] += $vals[csf("total_cutting_delivery")];
		}



		foreach ($data_array as $style_id => $style_data) {
			$kk = 0;
			$ccc = 0;
			foreach ($style_data as $item_id => $item_data) {


				foreach ($item_data as $po_id => $po_data) {
					$color_span = 0;
					foreach ($po_data as $shipment_date => $ship_date_data) {


						foreach ($ship_date_data as $color_id => $color_data) {
							$buyer_ins_row_span_arr[$style_id][$po_id] += 1;
							$color_span++;
							$ccc++;
							$kk++;
						}
					}
					$style_wise_span_array[$style_id][$item_id][$po_id] = $color_span;
				}
			}
			$style_wise_buyer_span[$style_id] = $kk;
		}


		$po_id_arr = str_replace("a.po_break_down_id", "id", $po_chunk_cond);
		$po_id_arr2 = str_replace("a.po_break_down_id", "b.po_break_down_id", $po_chunk_cond);
		$po_id_arr3 = str_replace("a.po_break_down_id", "po_breakdown_id", $po_chunk_cond);

		$po_lib_arr = return_library_array("SELECT id,po_number FROM wo_po_break_down WHERE status_active=1 AND is_deleted=0 $po_id_arr  ", "id", "po_number");

		$plan_cut_sql = "SELECT a.style_ref_no,b.po_break_down_id,b.item_number_id,b.color_number_id, SUM(b.plan_cut_qnty) as plan_cut ,sum(b.order_quantity) as order_quantity FROM wo_po_details_master a, wo_po_color_size_breakdown b  WHERE a.job_no=b.job_no_mst and a.status_active=1 and  b.status_active=1 AND b.is_deleted=0  $po_id_arr2 GROUP BY a.style_ref_no,b.po_break_down_id,b.item_number_id,b.color_number_id ";

		$plan_cut_result = sql_select($plan_cut_sql);
		foreach ($plan_cut_result as $val_plan) {
			$plan_cut_arr[$val_plan[csf("po_break_down_id")]][$val_plan[csf("item_number_id")]][$val_plan[csf("color_number_id")]]["plan_cut"] += $val_plan[csf("plan_cut")];
			$plan_cut_arr[$val_plan[csf("po_break_down_id")]][$val_plan[csf("item_number_id")]][$val_plan[csf("color_number_id")]]["order_quantity"] += $val_plan[csf("order_quantity")];

			$plan_cut_arr_gross[$val_plan[csf("po_break_down_id")]]["order_quantity"] += $val_plan[csf("order_quantity")];

			$style_wise_sub[$val_plan[csf("style_ref_no")]]["plan_cut"] += $val_plan[csf("plan_cut")];
			$style_wise_sub[trim($val_plan[csf("style_ref_no")])]["order_quantity"] += $val_plan[csf("order_quantity")];
		}


		$sql_result_variable = sql_select("select ex_factory,production_entry from variable_settings_production where company_name=$cbo_company_name and variable_list=1 and status_active=1");
		$production_level = $sql_result_variable[0][csf("ex_factory")];
		if ($production_level != 1) {
			$ex_fac_sql = "SELECT  a.style_ref_no,c.id as po_id,f.item_number_id,f.color_number_id,sum(CASE WHEN d.entry_form!=85 THEN e.production_qnty ELSE 0 END)-sum(CASE WHEN d.entry_form=85 THEN e.production_qnty ELSE 0 END) as product_qty
					FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d, pro_ex_factory_dtls e,wo_po_color_size_breakdown f
					WHERE
					a.job_no = c.job_no_mst and
					c.id=d.po_break_down_id  and
					d.id=e.mst_id and
					e.color_size_break_down_id=f.id and
					c.id=f.po_break_down_id and a.company_name=$cbo_company_name  and a.is_deleted =0 and
					a.status_active =1 and e.is_deleted =0 and
					e.status_active =1 and c.id in ($today_all_po_id)
					group by a.style_ref_no,c.id,f.item_number_id,f.color_number_id";
			foreach (sql_select($ex_fac_sql) as $key => $value) {
				$ex_fac_arr[$value[csf("po_id")]][$value[csf("item_number_id")]][$value[csf("color_number_id")]] = $value[csf("product_qty")];
				$style_wise_sub[$value[csf("style_ref_no")]]["ex_fact"] += $value[csf("product_qty")];
			}
		} else {
			$ex_fac_sql = "SELECT  a.style_ref_no,b.po_break_down_id ,sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as product_qty
					FROM wo_po_details_master a,wo_po_break_down c, pro_ex_factory_mst b WHERE a.job_no=c.job_no_mst and c.id=b.po_break_down_id and  b.is_deleted =0 and
					 b.status_active =1  and c.status_active=1 and c.is_deleted=0 and a.status_active=1  $po_id_arr2
					group by po_break_down_id,a.style_ref_no ";

			foreach (sql_select($ex_fac_sql) as $key => $value) {
				$ex_fac_arr[$value[csf("po_break_down_id")]] += $value[csf("product_qty")];
				$style_wise_sub[$value[csf("style_ref_no")]]["ex_fact"] += $value[csf("product_qty")];
			}
		}
		$sql_pre_cost = "SELECT a.po_break_down_id,a.color_number_id,(b.plan_cut_qnty/a.pcs)*a.requirment  as cons from wo_pre_cost_fabric_cost_dtls c ,wo_pre_cos_fab_co_avg_con_dtls a,wo_po_color_size_breakdown b where c.id=a.PRE_COST_FABRIC_COST_DTLS_ID and a.COLOR_SIZE_TABLE_ID=b.id and b.po_break_down_id=a.po_break_down_id and   a.po_break_down_id <>0 and c.uom=12  $po_chunk_cond  ";
		foreach (sql_select($sql_pre_cost) as $key => $val) {
			$precost_finish_arr[$val[csf("po_break_down_id")]][$val[csf("color_number_id")]] += $val[csf("cons")];
		}
		// echo "<pre>";
		// print_r($precost_finish_new_arr);
		$budget_sql = "SELECT a.po_break_down_id,b.contrast_color_id ,b.gmts_color_id from wo_pre_cos_fab_co_avg_con_dtls a,wo_pre_cos_fab_co_color_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id $po_chunk_cond";
		foreach (sql_select($budget_sql) as $key => $val) {
			$contrast_wise_gmt[$val[csf("po_break_down_id")]][$val[csf("contrast_color_id")]] = $val[csf("gmts_color_id")];
		}

		// getQtyArray_by_orderGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish

		// $sql_knit_recd="SELECT po_breakdown_id,color_id,quantity from order_wise_pro_details where  entry_form=37  $po_id_arr3";

		// foreach(sql_select($sql_knit_recd) as $key=>$val)
		// {
		// 	if(isset($contrast_wise_gmt[$val[csf("po_breakdown_id")]][$val[csf("color_id")]]))
		// 		$knit_recd_arr[$val[csf("po_breakdown_id")]][$contrast_wise_gmt[$val[csf("po_breakdown_id")]][$val[csf("color_id")]]]+=$val[csf("quantity")];
		// 	else $knit_recd_arr[$val[csf("po_breakdown_id")]][$val[csf("color_id")]]+=$val[csf("quantity")];
		// }

		$sql_woven_recd = "SELECT c.po_breakdown_id,c.color_id,c.quantity,a.id,b.id,c.dtls_id
				from inv_issue_master a,
					 inv_wvn_finish_fab_iss_dtls b ,
					 order_wise_pro_details c
				where
				 a.id = b.mst_id
				 and b.id = c.dtls_id
				 and c.entry_form=19
				 and  c.trans_type=2
				 $po_id_arr3";

		foreach (sql_select($sql_woven_recd) as $key => $val) {
			if (isset($contrast_wise_gmt[$val[csf("po_breakdown_id")]][$val[csf("color_id")]]))
				$woven_recd_arr[$val[csf("po_breakdown_id")]][$contrast_wise_gmt[$val[csf("po_breakdown_id")]][$val[csf("color_id")]]] += $val[csf("quantity")];
			else $woven_recd_arr[$val[csf("po_breakdown_id")]][$val[csf("color_id")]] += $val[csf("quantity")];
		}

		// echo"<pre>";
		// print_r($woven_recd_arr);

		$condition = new condition();
		$condition->po_id_in($today_all_po_id);
		$condition->init();
		$fabric = new fabric($condition);
		//  echo $fabric->getQuery(); die;
		$fabric_costing_arr = $fabric->getQtyArray_by_orderGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish();
		// echo"<pre>";
		// print_r($fabric_costing_arr);


		ob_start();
		?>

		<script type="text/javascript">
			setFilterGrid('table_body', -1);
		</script>
		<br> <br> <br>
		<div>
			<table width="3300" cellpadding="0" cellspacing="0">

				<tr>
					<td colspan="52" align="center"><b style="font-size: 21px;">Daily Production Progress Report </b></td>
				</tr>
				<tr>
					<td colspan="52" align="center"><b style="font-size: 21px;"><? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?></b> </td>
				</tr>
				<tr>
					<td colspan="52">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="52" align="left"><b style="font-size: 21px;">Date: <? echo str_replace("'", "",  $txt_production_date); ?> </b></td>
				</tr>
			</table>
			<table class="rpt_table" width="3300" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
				<thead>

					<tr>
						<th width="40" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>SI</p>
						</th>
						<th width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Buyer</p>
						</th>
						<th width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Style</p>
						</th>
						<th width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>PO NO</p>
						</th>
						<th width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Color</p>
						</th>
						<th width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Item</p>
						</th>
						<th width="80" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Order Qty.</p>
						</th>
						<th width="80" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Plan Cut Qty.</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Cutting Fabric Status (Yds)</p>
						</th>
						<th width="100" style='word-break:break-all;word-wrap: break-word;'>
							<p>&nbsp;</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Cutting</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Print</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>EMB</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Special Works</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Cutting Issue</p>
						</th>
						<th width="80" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Line</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Sewing Input</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Sewing Output</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Wash</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Finishing Entry</p>
						</th>
						<!-- <th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3" ><p>Poly</p></th> -->
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Packing & Finishing</p>
						</th>
						<th width="120" style='word-break:break-all;word-wrap: break-word;' colspan="2">
							<p>Carton</p>
						</th>
						<th width="180" style='word-break:break-all;word-wrap: break-word;' colspan="3">
							<p>Buyer inspection</p>
						</th>
						<th width="80" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Ex-Factory</p>
						</th>
						<th width="80" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Excess</p>
						</th>
						<th width="80" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Short</p>
						</th>
						<th width="80" style='word-break:break-all;word-wrap: break-word;' rowspan="2">
							<p>Shipping <br> Status</p>
						</th>

					</tr>

					<tr>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Req</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Rcvd</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Fab Bal</p>
						</th>
						<th width="100" style='word-break:break-all;word-wrap: break-word;'>
							<p>Shipdate</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Day</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>TTL Cut</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;' title=" Cutting = ( Total Cutting - Plan Cut ) ">
							<p>Cut Bal</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Sent</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Rcvd</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;' title=" Print = (Total Print Receive - Total Print Issue) ">
							<p>WIP</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Sent</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Rcvd</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;' title="EMB = (Total EMB Receive - Total EMB Send)">
							<p>WIP</p>
						</th>

						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Sent</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Rcvd</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;' title="EMB = (Total EMB Receive - Total EMB Send)">
							<p>WIP</p>
						</th>

						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Day</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Input</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;' title="Cutting Issue = ( Cutting Issue Input - EMB Rcvd )">
							<p>WIP</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Day</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Input</p>
						</th>
						<th width="60" title="Sewing Input = (Total Sewing input - Total Cutting Delivery) " style='word-break:break-all;word-wrap: break-word;'>
							<p>WIP</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Day</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Total</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;' title=" Sewing Output =(Total Sewing Out - Total Sewing Input) ">
							<p>WIP</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Sent</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Rcvd</p>
						</th>
						<th width="60" title="WIP = (Wash Revd - Wash Send)" style='word-break:break-all;word-wrap: break-word;'>
							<p>WIP</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Day</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Total</p>
						</th>
						<th width="60" title="Finishing Entry = (Finishing Entry Total - Total Wash Received)" style='word-break:break-all;word-wrap: break-word;'>
							<p>WIP</p>
						</th>
						<!-- <th width="60"   style='word-break:break-all;word-wrap: break-word;' ><p>Day</p></th>
	                	<th width="60"   style='word-break:break-all;word-wrap: break-word;' ><p>Total</p></th>
	                	<th width="60" title="Poly= (Total Poly - Total Sewing Output)"  style='word-break:break-all;word-wrap: break-word;' ><p>WIP</p></th> -->

						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Day</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Total</p>
						</th>
						<th width="60" title=" Packing & Finishing = (Total Packing & Finishing - Total Poly) " style='word-break:break-all;word-wrap: break-word;'>
							<p>WIP</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Day</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Total</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Day</p>
						</th>
						<th width="60" style='word-break:break-all;word-wrap: break-word;'>
							<p>Total</p>
						</th>
						<th width="60" title="Buyer Inspection = Buyer Inspection Total - Packing & Finishing Total" style='word-break:break-all;word-wrap: break-word;'>
							<p>WIP</p>
						</th>
					</tr>


				</thead>
			</table>

			<div style="width:3318px;max-height:400px;overflow-y:scroll;float: left; " id="scroll_body">
				<table class="rpt_table" width="3300" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">

					<?


					$m = 1;
					$jj = 0;
					$gr_wise_buyer_ins_today = 0;
					$gr_wise_buyer_ins_total = 0;
					$gr_wise_buyer_ins_wip = 0;
					$gr_wise_carton_today = 0;
					$gr_wise_carton_total = 0;
					$grand_total_finsish_req = 0;
					$grand_total_finsish_recd = 0;
					$grand_total_finsish_bal = 0;
					$grand_ex_fac_excess = 0;
					$grand_ex_fac_short = 0;
					$grand_sewing_wip = 0;
					$grand_cutting_delivery_wip = 0;

					$grand_poly_today = 0;
					$grand_poly_total = 0;
					$grand_poly_wip = 0;

					$grand_finish_today = 0;
					$grand_finish_total = 0;
					$grand_finish_wip = 0;

					$grand_iron_today = 0;
					$grand_iron_total = 0;
					$grand_iron_wip = 0;


					foreach ($data_array as $style_id => $style_data) {
						$pp = 0;
						$style_wise_finsish_req = 0;
						$style_wise_buyer_ins_today = 0;
						$style_wise_buyer_ins_total = 0;
						$style_wise_buyer_ins_wip = 0;
						$style_wise_carton_today = 0;
						$style_wise_carton_total = 0;

						$style_wise_finsish_recd = 0;
						$style_wise_finsish_bal = 0;
						$style_ex_fac_excess = 0;
						$style_ex_fac_short = 0;
						$style_sewing_wip = 0;
						$style_poly_wip = 0;
						$style_finishing_entry_wip = 0;
						$style_cutting_delivery_wip = 0;
						foreach ($style_data as $item_id => $item_data) {

							foreach ($item_data as $po_id => $po_data) {
								$l = 0;
								$nn = 0;
								foreach ($po_data as $shipment_date => $ship_date_data) {



									foreach ($ship_date_data as  $color_id => $color_data) {
										if ($m % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";
										$jj++;


					?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $jj; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $jj; ?>">

											<?
											if ($pp == 0) {
											?>
												<td width="40" valign="middle" align="center" rowspan="<? echo $style_wise_buyer_span[$style_id]; ?>" style='word-break:break-all;word-wrap: break-word;'>
													<p><? echo $m;
														$m++; ?></p>
												</td>
												<td valign='middle' align="center" width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="<? echo $style_wise_buyer_span[$style_id]; ?>">
													<p><? echo $buyer_arr[$style_wise_buyer_arr[$style_id]]; ?></p>
												</td>
												<td valign='middle' align="center" width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="<? echo $style_wise_buyer_span[$style_id]; ?>">
													<p><? echo $style_id; ?></p>
												</td>
											<?
											}
											if ($l == 0) {


											?>



												<td valign='middle' align="center" width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>">
													<p><? echo $po_lib_arr[$po_id]; ?></p>
												</td>
											<?

											}
											?>



											<td width="100" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_lib[$color_id]; ?></p>
											</td>
											<?
											if ($l == 0) {
											?>
												<td valign='middle' align="center" width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>">
													<p><? echo $garments_item[$item_id]; ?></p>
												</td>

											<?

											}
											$fab_bal = ($woven_recd_arr[$po_id][$color_id]) * 1 - (array_sum($fabric_costing_arr['woven']['grey'][$po_id][$item_id][$color_id])) * 1;
											$sewing_in_wip = $color_data["total_sew_input"] - $color_data["total_cutting_delivery"];
											$swin_bal_color = ($sewing_in_wip < 0) ? " color:red; " : "color:black; ";
											$fab_bal_color = ($fab_bal < 0) ? " color:red; " : "color:black; ";
											$cutting_bal = $color_data["total_cutting"] - $plan_cut_arr[$po_id][$item_id][$color_id]["plan_cut"];
											$cutting_bal_color = ($cutting_bal < 0) ? " color:red; " : "color:black; ";
											$print_bal = ($color_data["total_print_receive"] - $color_data["total_print_issue"]);

											$print_bal_color = ($print_bal < 0) ? " color:red; " : "color:black; ";
											$emb_bal = ($color_data["total_emb_receive"] - $color_data["total_emb_issue"]);

											$special_work_bal = ($color_data["total_special_work_rcv"] - $color_data["total_special_work_issue"]);

											$special_work_bal_color = ($special_work_bal < 0) ? " color:red; " : "color:black; ";

											$emb_bal_color = ($emb_bal < 0) ? " color:red; " : "color:black; ";
											$swout_wip = $color_data["total_sew_out"] - $color_data["total_sew_input"];
											$swout_wip_color = ($swout_wip < 0) ? " color:red; " : "color:black; ";
											$wash_bal = ($color_data["total_wash_receive"] - $color_data["total_wash_issue"]);
											$wash_bal_color = ($wash_bal < 0) ? " color:red; " : "color:black; ";


											$iron_bal = ($color_data["total_wash_receive"]) ? $color_data["total_iron"] - $color_data["total_wash_receive"] : $color_data["total_iron"] - $color_data["total_sew_out"];
											$iron_bal_color = ($iron_bal < 0) ? " color:red; " : "color:black; ";

											//  $finishing_rec_bal=($color_data["total_wash_receive"])? $color_data["total_finishing_entry"]-$color_data["total_wash_receive"] : $color_data["total_finishing_entry"]-$color_data["total_sew_out"];

											$finishing_rec_bal = $color_data["total_finishing_entry"] - $color_data["total_wash_receive"];
											//  $finishing_rec_bal= ($color_data["total_finishing_entry"] + $color_data["total_poly"]) -$color_data["total_wash_receive"];
											//  $finishing_rec_bal_color=($finishing_rec_bal<0)? " color:red; " : "color:black; ";
											$finishing_rec_bal_color = ($finishing_rec_bal + $poly_bal < 0) ? " color:red; " : "color:black; ";

											// $poly_bal=($color_data["total_poly"]-$color_data["total_iron"]);
											//  if($color_data["total_iron"])
											//  {
											//  	 $poly_bal=($color_data["total_poly"]-$color_data["total_iron"]);

											//  }
											//  else if($color_data["total_wash_receive"])
											//  {
											//  	$poly_bal=($color_data["total_poly"]-$color_data["total_iron"]);
											//  }
											//  else
											//  {
											$poly_bal = ($color_data["total_poly"] - $color_data["total_sew_out"]);
											//  }

											$poly_bal_color = ($poly_bal < 0) ? " color:red; " : "color:black; ";

											$inspection_bal = ($style_wise_ins_arr[$style_id][$po_id]["total_inspection"] - $style_wise_sub[$style_id]["total_finish"]);
											$inspection_bal_color = ($inspection_bal < 0) ? " color:red; " : "color:black; ";


											$style_wise_finsish_req += array_sum($fabric_costing_arr['woven']['grey'][$po_id][$item_id][$color_id]);
											// $style_wise_finsish_req +=$precost_finish_arr[$po_id][$color_id];
											$style_wise_finsish_recd += $woven_recd_arr[$po_id][$color_id];
											$style_wise_finsish_bal += $fab_bal;
											// $grand_total_finsish_req +=$precost_finish_arr[$po_id][$color_id];
											$grand_total_finsish_req += array_sum($fabric_costing_arr['woven']['grey'][$po_id][$item_id][$color_id]);
											$grand_total_finsish_recd += $woven_recd_arr[$po_id][$color_id];
											$grand_total_finsish_bal += $fab_bal;
											$style_sewing_wip += $sewing_in_wip;
											$grand_sewing_wip += $sewing_in_wip;
											if (($color_data["total_print_receive"] * 1) > 0 || ($color_data["total_emb_receive"]) * 1 > 0) {
												if (($color_data["total_print_receive"] * 1) > 0 && ($color_data["total_emb_receive"]) * 1 <= 0) {
													$cutting_delivery_wip = ($color_data["total_cutting_delivery"] - $color_data["total_print_receive"]);
													$style_cutting_delivery_wip += $cutting_delivery_wip;
													$grand_cutting_delivery_wip += $cutting_delivery_wip;
													$cutting_delivery_bal_color = ($cutting_delivery_wip < 0) ? " color:red; " : "color:black; ";
												} else {
													$cutting_delivery_wip = ($color_data["total_cutting_delivery"] - $color_data["total_emb_receive"]);
													$style_cutting_delivery_wip += $cutting_delivery_wip;
													$grand_cutting_delivery_wip += $cutting_delivery_wip;
													$cutting_delivery_bal_color = ($cutting_delivery_wip < 0) ? " color:red; " : "color:black; ";
												}
											} else {
												$cutting_delivery_wip = ($color_data["total_cutting_delivery"] - $color_data["total_cutting"]);
												$style_cutting_delivery_wip += $cutting_delivery_wip;
												$grand_cutting_delivery_wip += $cutting_delivery_wip;
												$cutting_delivery_bal_color = ($cutting_delivery_wip < 0) ? " color:red; " : "color:black; ";
											}

											//if($color_data["total_wash_receive"]>0)
											// {
											$finsh_bal = $color_data["total_finish"] - $color_data["total_poly"];
											//}
											//else
											//{
											//	$finsh_bal=  $color_data["total_finish"]-$color_data["total_sew_out"];
											// }
											$finsh_bal_color = ($finsh_bal < 0) ? " color:red; " : "color:black; ";



											?>

											<td width="80" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo   $plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"]; ?></p>
											</td>
											<td width="80" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo  $plan_cut_arr[$po_id][$item_id][$color_id]["plan_cut"]; ?></p>
											</td>
											<td width="60" id="defaidult_zero1" class="default_zero" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><?
													$fab_req = array_sum($fabric_costing_arr['woven']['grey'][$po_id][$item_id][$color_id]);
													if ($fab_req == "") echo 0;
													else  echo number_format($fab_req, 2); ?></p>
											</td>
											<td width="60" id="default_zero2" class="default_zero" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? if ($woven_recd_arr[$po_id][$color_id] == "") echo 0;
													else  echo number_format($woven_recd_arr[$po_id][$color_id], 2); ?></p>
											</td>
											<td width="60" title=" fab_bal= (Rcvd * 1) - ( Req * 1) " id="default_zero3" class="default_zero" align="center" style='word-break:break-all;word-wrap: break-word; <? echo $fab_bal_color; ?>'>
												<p><? echo number_format($fab_bal, 2); ?></p>
											</td>
											<td width="100" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo change_date_format($shipment_date); ?> &nbsp;</p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["today_cutting"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_cutting"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $cutting_bal_color; ?>'>
												<p><? echo $cutting_bal; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_print_issue"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_print_receive"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $print_bal_color; ?>'>
												<p> <? echo $print_bal; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_emb_issue"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_emb_receive"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $emb_bal_color; ?>'>
												<p><? echo $emb_bal;  ?></p>
											</td>

											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_special_work_issue"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_special_work_rcv"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $special_work_bal_color; ?>'>
												<p><? echo $special_work_bal;  ?></p>
											</td>

											<td width="60" id="default_zero4" class="default_zero" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? if ($color_data["today_cutting_delivery"] == "") echo 0;
													else   echo $color_data["today_cutting_delivery"]; ?></p>
											</td>
											<td width="60" id="default_zero5" class="default_zero" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? if ($color_data["total_cutting_delivery"] == "") echo 0;
													else echo $color_data["total_cutting_delivery"]; ?></p>
											</td>

											<td width="60" id="default_zero6" class="default_zero" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $cutting_delivery_bal_color; ?>'>
												<p> <? echo $cutting_delivery_wip;  ?> </p>
											</td>

											<?
											if ($l == 0) {
											?>
												<td valign='middle' align="center" width="80" style='word-break:break-all;word-wrap: break-word;' rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>">
													<p>
														<?

														$line = array_unique(explode(",", $style_po_wise_line[$style_id][$po_id]));

														$line_name = "";
														foreach ($line as $v) {
															if ($prod_reso_allo == 1) {
																$line_name .= $line_lib_resource[$v] . ",";
															} else {
																$line_name .= $line_lib[$v] . ",";
															}
														}
														echo trim($line_name, ","); ?></p>
												</td>

											<?

											}
											?>



											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["today_sew_input"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_sew_input"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $swin_bal_color; ?>'>
												<p><? echo  $sewing_in_wip  ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["today_sew_output"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_sew_out"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $swout_wip_color; ?>'>
												<p><? echo $swout_wip; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_wash_issue"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_wash_receive"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $wash_bal_color; ?>'>
												<p><? echo  $wash_bal; ?></p>
											</td>

											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $today_finishing = $color_data["today_finishing_entry"] + $color_data["today_poly"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $total_finishing = $color_data["total_finishing_entry"] + $color_data["total_poly"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $finishing_rec_bal_color; ?>'>
												<p><? echo  $finishing_rec_bal + $poly_bal; ?></p>
											</td>

											<!-- <td width="60"  align="center"   style='word-break:break-all;word-wrap: break-word;' ><p><? //echo $color_data["today_poly"]."pp";
																																			?></p></td>
					                   		     <td width="60"  align="center"   style='word-break:break-all;word-wrap: break-word;' ><p><? // echo $color_data["total_poly"];
																																				?></p></td>
					                   		    <td width="60"  align="center"   style='word-break:break-all;word-wrap: break-word;<? //echo $poly_bal_color;
																																		?>' >
					                   		    <p><? // echo  $poly_bal;
														?></p>
					                   		    </td> -->
											<?
											$grand_poly_today += $color_data["today_poly"];
											$grand_poly_total += $color_data["total_poly"];
											$grand_poly_wip += $poly_bal;
											$style_poly_wip += $poly_bal;
											$style_finishing_entry_wip += $finishing_rec_bal;

											$grand_finish_today += $color_data["today_finish"];
											$grand_finish_total += $color_data["total_finish"];
											$grand_finish_wip += $color_data["finsh_bal"];

											$grand_iron_today += $color_data["today_iron"];
											$grand_iron_total += $color_data["total_iron"];
											$grand_iron_wip += $iron_bal;

											$grand_finishing_entry_today += $color_data["today_finishing_entry"];
											$grand_finishing_entry_total += $color_data["total_finishing_entry"];
											$grand_finishing_rec_wip += $finishing_rec_bal;
											?>


											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["today_finish"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
												<p><? echo $color_data["total_finish"]; ?></p>
											</td>
											<td width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $finsh_bal_color; ?>'>
												<p>
													<? echo $finsh_bal; ?></p>
											</td>
											<?
											if ($pp == 0) {

												// $style_wise_carton_today+=$style_wise_ctq_arr[$style_id]["today_cartoon"];
												// $style_wise_carton_total+=$style_wise_ctq_arr[$style_id]["total_cartoon"];
												// $gr_wise_carton_today+=$style_wise_ctq_arr[$style_id]["today_cartoon"];
												// $gr_wise_carton_total+=$style_wise_ctq_arr[$style_id]["total_cartoon"];

												$style_wise_carton_today += $style_wise_woven_ctq_arr[$style_id]["today_cartoon"];
												$style_wise_carton_total += $style_wise_woven_ctq_arr[$style_id]["total_cartoon"];
												$gr_wise_carton_today += $style_wise_woven_ctq_arr[$style_id]["today_cartoon"];
												$gr_wise_carton_total += $style_wise_woven_ctq_arr[$style_id]["total_cartoon"];


											?>
												<td width="60" id="default_zero7" class="default_zero" valign="middle" align="center" rowspan="<? echo $style_wise_buyer_span[$style_id] + 1; ?>" style='word-break:break-all;word-wrap: break-word;'>
													<p><? if ($style_wise_woven_ctq_arr[$style_id]["today_cartoon"] == "") echo 0;
														else echo $style_wise_woven_ctq_arr[$style_id]["today_cartoon"]; ?></p>
												</td>
												<td width="60" id="default_zero8" class="default_zero" valign="middle" align="center" rowspan="<? echo $style_wise_buyer_span[$style_id] + 1; ?>" style='word-break:break-all;word-wrap: break-word;'>
													<p><? if ($style_wise_woven_ctq_arr[$style_id]["total_cartoon"] == "") echo 0;
														else echo $style_wise_woven_ctq_arr[$style_id]["total_cartoon"]; ?></p>
												</td>
											<?
											}

											if ($l == 0) {
												$border_top = "";
												$today_ins = $style_wise_ins_arr[$style_id][$po_id]["today_inspection"];
												$total_ins = $style_wise_ins_arr[$style_id][$po_id]["total_inspection"];
												if ($po_item_style_buyer_ins[$style_id][$po_id] == 1) {
													$today_ins = "";
													$total_ins = "";
													$inspection_bal = "";
													$border_top = " border-top:2px solid $bgcolor;";
												}
												$style_wise_buyer_ins_today += $today_ins;
												$style_wise_buyer_ins_total += $total_ins;
												$style_wise_buyer_ins_wip += $inspection_bal;
												$gr_wise_buyer_ins_today += $today_ins;
												$gr_wise_buyer_ins_total += $total_ins;
												$gr_wise_buyer_ins_wip += $inspection_bal;


											?>

												<td width="60" id="default_zero9" class="default_zero" rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>" valign='middle' align="center" style='word-break:break-all;word-wrap: break-word;<? echo $border_top; ?>'>
													<p><? if ($today_ins == "") echo 0;
														else echo $today_ins; ?></p>
												</td> <? $new_style_id = "'" . $style_id . "'"; ?>
												<td width="60" id="default_zero10" class="default_zero" rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>" valign='middle' align="center" style='word-break:break-all;word-wrap: break-word;<? echo $border_top; ?>'><a href="##" onClick="openmypage_buyer_ins(<? echo $new_style_id; ?>, <? echo $po_id; ?>,'remarks_popup');"><? if ($total_ins == "") echo 0;
																																																																																																		else echo $total_ins; ?></a> </td>
												<td id="default_zero11" class="default_zero" width="60" rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>" valign='middle' align="center" style='word-break:break-all;word-wrap: break-word;<? echo $border_top; ?>'>
													<p>
														<? if ($inspection_bal == "") echo 0;
														else echo $inspection_bal; ?></p>
												</td>
											<?
												$po_item_style_buyer_ins[$style_id][$po_id] = 1;
											}
											$nn++;
											$pp++;
											?>



											<?
											if ($production_level != 1) {
											?>
												<td width="80" id="default_zero12" class="default_zero" align="center" style='word-break:break-all;word-wrap: break-word;'>
													<p> <? if ($ex_fac_arr[$po_id][$item_id][$color_id] == "") echo 0;
														else echo $ex_fac_arr[$po_id][$item_id][$color_id];
														$excess_qnty = ($plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"]) - ($ex_fac_arr[$po_id][$item_id][$color_id]) * 1;
														// echo $excess_qnty.'=excess cut';
														if ($excess_qnty < 0) {
															$excess = $excess_qnty;
															$excess_color = " color:red;";
															$style_ex_fac_excess += $excess;
															$grand_ex_fac_excess += $excess;
															$short = 0;
														} else {
															$excess_color = "";
															$short = $excess_qnty;
															$excess = "";
															$style_ex_fac_short += $short;
															$grand_ex_fac_short += $short;
														}


														?></p>
												</td>
												<td class="default_zero" id="default_zero13" width="80" align="center" style="word-break:break-all;word-wrap: break-word;<? echo  $excess_color; ?>""  ><p><? if ($excess == "") echo 0;
																																																			else echo  $excess; ?></p></td>
						                   		     <td width=" 80" align="center" style='word-break:break-all;word-wrap: break-word;'>
													<p><? echo  $short; ?></p>
												</td>
												<?
											} else {
												if ($l == 0) {
													$excess_qnty = ($plan_cut_arr_gross[$po_id]["order_quantity"]) - ($ex_fac_arr[$po_id]) * 1;
													if ($excess_qnty < 0) {
														$excess = $excess_qnty;
														$excess_color = " color:red;";
														$style_ex_fac_excess += $excess;
														$grand_ex_fac_excess += $excess;
														$short = 0;
													} else {
														$excess_color = "";
														$short = $excess_qnty;
														$excess = "";
														$style_ex_fac_short += $short;
														$grand_ex_fac_short += $short;
													}

												?>
													<td width="80" align="center" rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>" style='word-break:break-all;word-wrap: break-word;'>
														<p> <? echo $ex_fac_arr[$po_id]; ?>
														</p>
													</td>
													<td width="80" align="center" rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>" style="word-break:break-all;word-wrap: break-word;<? echo  $excess_color; ?>""  ><p><? echo  $excess; ?></p></td>
								                   		     <td width=" 80" align="center" rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>" style='word-break:break-all;word-wrap: break-word;'>
														<p><? echo  $short; ?></p>
													</td>



											<?
												}
											}
											?>
											<td width="80" style='word-break:break-all;word-wrap: break-word;' align="center">
												<p>
													<?
													$ship_status = $color_data["shiping_status"];
													// $ship_status_value=($ship_status==2 || $ship_status==1)? " Pending " : " Full Shipment";
													echo $shipment_status[$ship_status];

													?>

												</p>
											</td>

										</tr>


						<?

										$l++;
									}
								}
							}
						}
						$style_cutting_bal = $style_wise_sub[$style_id]["total_cutting"] - $style_wise_sub[trim($style_id)]["plan_cut"];
						$style_cutting_bal_color = ($style_cutting_bal < 0) ? " color:red; " : "color:black; ";
						$style_wise_finsish_bal_color = ($style_wise_finsish_bal < 0) ? " color:red; " : "color:black; ";
						$style_wise_print_bal_color = ($style_wise_sub[$style_id]["print_wip"] < 0) ? " color:red; " : "color:black; ";
						$style_wise_sewout_bal_color = ($style_wise_sub[$style_id]["sewout_wip"] < 0) ? " color:red; " : "color:black; ";
						$style_wise_emb_bal_color = ($style_wise_sub[$style_id]["emb_wip"] < 0) ? " color:red; " : "color:black; ";
						$style_wise_special_work_bal_color = ($style_wise_sub[$style_id]["special_work_wip"] < 0) ? " color:red; " : "color:black; ";
						$style_wise_cutting_delivery_bal_color = ($style_cutting_delivery_wip < 0) ? " color:red; " : "color:black; ";
						$style_wise_swin_bal_color = ($style_sewing_wip < 0) ? " color:red; " : "color:black; ";
						$style_wise_wash_bal_color = ($style_wise_sub[$style_id]["wash_wip"] < 0) ? " color:red; " : "color:black; ";
						// $style_wise_iron_bal_color=(  $style_wise_sub[$style_id]["finishing_wip"]<0)? " color:red; " : "color:black; ";
						// $style_wise_iron_bal_color=(  $style_finishing_entry_wip<0)? " color:red; " : "color:black; ";
						$style_wise_iron_bal_color = ($style_finishing_entry_wip + $style_poly_wip < 0) ? " color:red; " : "color:black; ";
						$style_wise_poly_bal_color = ($style_poly_wip < 0) ? " color:red; " : "color:black; ";









						?>
						<tr bgcolor="#EAEAEA" onClick="change_color('tr222_<? echo $jj; ?>', '<? echo $bgcolor; ?>')" id="tr222_<? echo $jj; ?>">
							<th colspan="6" align="center" style='word-break:break-all;word-wrap: break-word;'>
								<p> Style Total</p>
								</td>
							<th align="center" width="80" style='word-break:break-all;word-wrap: break-word;'>
								<p><? echo $style_wise_sub[trim($style_id)]["order_quantity"]; ?> </p>
								</td>
							<th align="center" width="80" style='word-break:break-all;word-wrap: break-word;'>
								<p><? echo $style_wise_sub[trim($style_id)]["plan_cut"]; ?> </p>
								</td>

							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
								<p><? if ($style_wise_finsish_req == "") echo 0;
									else echo number_format($style_wise_finsish_req, 2); ?> </p>
							</th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
								<p><? if ($style_wise_finsish_recd == "") echo 0;
									else echo number_format($style_wise_finsish_recd, 2); ?> </p>
							</th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_finsish_bal_color; ?>'>
								<p><? echo number_format($style_wise_finsish_bal, 2); ?> </p>
							</th>
							<th style='word-break:break-all;word-wrap: break-word;'>
								<p>&nbsp;</p>
							</th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["today_cutting"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_cutting"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_cutting_bal_color; ?>'> <? echo $style_cutting_bal; ?> </th>

							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_print_issue"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_print_receive"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_print_bal_color; ?>'> <? echo $style_wise_sub[$style_id]["print_wip"]; ?> </th>


							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_emb_issue"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_emb_receive"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_emb_bal_color; ?>'> <? echo $style_wise_sub[$style_id]["emb_wip"]; ?> </th>

							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_special_work_issue"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_special_work_rcv"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_special_work_bal_color; ?>'> <? echo $style_wise_sub[$style_id]["special_work_wip"]; ?> </th>


							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? if ($style_wise_sub[$style_id]["today_cutting_delivery"] == "") echo 0;
																												else echo $style_wise_sub[$style_id]["today_cutting_delivery"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? if ($style_wise_sub[$style_id]["total_cutting_delivery"] == "") echo 0;
																												else echo $style_wise_sub[$style_id]["total_cutting_delivery"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_cutting_delivery_bal_color; ?>'> <? echo $style_cutting_delivery_wip; ?> </th>
							<th width="80">&nbsp;</th>

							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["today_sew_input"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_sew_input"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_swin_bal_color; ?>'> <? echo $style_sewing_wip; ?> </th>


							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["today_sew_output"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_sew_out"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_sewout_bal_color; ?>'> <? echo $style_wise_sub[$style_id]["sewout_wip"]; ?> </th>


							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_wash_issue"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_wash_receive"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_wash_bal_color; ?>'> <? echo $style_wise_sub[$style_id]["wash_wip"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["today_finishing_entry"] + $style_wise_sub[$style_id]["today_poly"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_finishing_entry"] + $style_wise_sub[$style_id]["total_poly"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_iron_bal_color; ?>'> <? echo $style_finishing_entry_wip + $style_poly_wip; ?> </th>

							<!-- <th width="60" align="center" style='word-break:break-all;word-wrap: break-word;' > <? // echo $style_wise_sub[$style_id]["today_poly"];
																														?> </th>
			            	<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;' > <? // echo $style_wise_sub[$style_id]["total_poly"];
																												?> </th>
			            	<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? //echo $style_wise_poly_bal_color;
																											?>' > <? // echo $style_poly_wip;
																																						?> </th> -->

							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["today_finish"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $style_wise_sub[$style_id]["total_finish"]; ?> </th>
							<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $style_wise_finish_bal_color; ?>'> <? echo $style_wise_sub[$style_id]["finish_wip"]; ?> </th>

							<th width="60"> <? echo $style_wise_buyer_ins_today; ?> </th>
							<th width="60"> <? echo $style_wise_buyer_ins_total; ?> </th>
							<th width="60"> <? echo $style_wise_buyer_ins_wip; ?> </th>

							<th width="80" align="center" style='word-break:break-all;word-wrap: break-word;'> <? if ($style_wise_sub[$style_id]["ex_fact"] == "") echo 0;
																												else echo $style_wise_sub[$style_id]["ex_fact"]; ?> </th>
							<th width="80" align="center">
								<p> <? echo $style_ex_fac_excess; ?> </p>
							</th>
							<th width="80" align="center">
								<p> <? echo $style_ex_fac_short; ?> </p>
							</th>

							<th width="80">&nbsp;</th>

						</tr>

					<?
					}

					$gr_order_qnty = 0;
					$gr_plan_qnty = 0;
					$gr_today_cutting = 0;
					$gr_total_cutting = 0;
					$gr_total_print_issue = 0;
					$gr_total_print_receive = 0;
					$gr_total_emb_issue = 0;

					$gr_total_special_work_issue = 0;
					$gr_total_special_work_receive = 0;
					$gr_special_work_wip = 0;

					$gr_print_wip = 0;
					$gr_emb_wip = 0;
					$gr_total_emb_receive = 0;
					$gr_today_cutting_delivery = 0;
					$gr_total_cutting_delivery = 0;
					$gr_today_sew_input = 0;
					$gr_total_sew_input = 0;
					$gr_sewin_wip = 0;
					$gr_today_sew_output = 0;
					$gr_total_sew_out = 0;
					$gr_sewout_wip = 0;
					$gr_total_wash_issue = 0;
					$gr_total_wash_receive = 0;
					$gr_wash_wip = 0;
					$gr_today_finish = 0;
					$gr_total_finish = 0;
					$gr_finish_wip = 0;
					$gr_ex_fact = 0;

					foreach ($style_wise_sub as $key => $vals) {
						$gr_order_qnty += $vals["order_quantity"];
						$gr_plan_qnty += $vals["plan_cut"];
						$gr_today_cutting += $vals["today_cutting"];
						$gr_total_cutting += $vals["total_cutting"];
						$gr_total_print_issue += $vals["total_print_issue"];
						$gr_total_print_receive += $vals["total_print_receive"];
						$gr_print_wip += $vals["print_wip"];
						$gr_emb_wip += $vals["emb_wip"];
						$gr_total_emb_issue += $vals["total_emb_issue"];
						$gr_total_emb_receive += $vals["total_emb_receive"];


						$gr_total_special_work_issue += $vals["total_special_work_issue"];;
						$gr_total_special_work_receive += $vals["total_special_work_rcv"];;
						$gr_special_work_wip += $vals["special_work_wip"];;



						$gr_today_cutting_delivery += $vals["today_cutting_delivery"];
						$gr_total_cutting_delivery += $vals["total_cutting_delivery"];
						$gr_today_sew_input += $vals["today_sew_input"];
						$gr_total_sew_input += $vals["total_sew_input"];
						$gr_sewin_wip += $vals["sewin_wip"];
						$gr_today_sew_output += $vals["today_sew_output"];
						$gr_total_sew_out += $vals["total_sew_out"];
						$gr_sewout_wip += $vals["sewout_wip"];
						$gr_total_wash_issue += $vals["total_wash_issue"];
						$gr_total_wash_receive += $vals["total_wash_receive"];
						$gr_wash_wip += $vals["wash_wip"];
						$gr_today_finish += $vals["today_finish"];
						$gr_total_finish += $vals["total_finish"];
						$gr_finish_wip += $vals["finish_wip"];
						$gr_ex_fact += $vals["ex_fact"];
					}

					$gr_cutting_bal_color = ($gr_total_cutting - $gr_plan_qnty < 0) ? " color:red; " : "color:black; ";
					$gr_print_wip_color = ($gr_print_wip < 0) ? " color:red; " : "color:black; ";
					$gr_emb_wip_color = ($gr_emb_wip < 0) ? " color:red; " : "color:black; ";
					$gr_special_work_wip_color = ($gr_special_work_wip < 0) ? " color:red; " : "color:black; ";
					$grand_cutting_delivery_wip_color = ($grand_cutting_delivery_wip < 0) ? " color:red; " : "color:black; ";
					$grand_sewing_wip_color = ($grand_sewing_wip < 0) ? " color:red; " : "color:black; ";
					$gr_sewout_wip_color = ($gr_sewout_wip < 0) ? " color:red; " : "color:black; ";
					$gr_wash_wip_color = ($gr_wash_wip < 0) ? " color:red; " : "color:black; ";
					$gr_finish_wip_color = ($gr_finish_wip < 0) ? " color:red; " : "color:black; ";
					$grand_total_finsish_bal_color = ($grand_total_finsish_bal < 0) ? " color:red; " : "color:black; ";
					// $gr_iron_wip_color=($grand_iron_wip<0)? " color:red; " : "color:black; ";
					$gr_iron_wip_color = ($grand_iron_wip + $grand_poly_wip < 0) ? " color:red; " : "color:black; ";
					$gr_finishing_rec_wip_color = ($grand_finishing_rec_wip < 0) ? " color:red; " : "color:black; ";
					$gr_poly_wip_color = ($grand_poly_wip < 0) ? " color:red; " : "color:black; ";
					$gr_finish_wip_color = ($grand_finish_wip < 0) ? " color:red; " : "color:black; ";


					?>

					<tr bgcolor="#A4C2EA" onClick="change_color('tr333_<? echo $jj; ?>', '<? echo $bgcolor; ?>')" id="tr333_<? echo $jj; ?>">
						<th colspan="6" align="center" style='word-break:break-all;word-wrap: break-word;'>
							<p> Grand Total</p>
							</td>
						<th align="center" width="80" style='word-break:break-all;word-wrap: break-word;'>
							<p><? echo $gr_order_qnty; ?> </p>
							</td>
						<th align="center" width="80" style='word-break:break-all;word-wrap: break-word;'>
							<p><? echo $gr_plan_qnty; ?> </p>
							</td>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
							<p> <? echo number_format($grand_total_finsish_req, 2); ?> </p>
						</th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'>
							<p> <? echo number_format($grand_total_finsish_recd, 2); ?> </p>
						</th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $grand_total_finsish_bal_color; ?>'>
							<p> <? echo number_format($grand_total_finsish_bal, 2); ?> </p>
						</th>
						<th style='word-break:break-all;word-wrap: break-word;'>&nbsp; </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_today_cutting; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_cutting; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $gr_cutting_bal_color; ?>'> <? echo  $gr_total_cutting - $gr_plan_qnty; ?> </th>

						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_print_issue; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_print_receive; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $gr_print_wip_color; ?>'> <? echo $gr_print_wip; ?> </th>


						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_emb_issue; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_emb_receive; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $gr_emb_wip_color; ?>'> <? echo $gr_emb_wip; ?> </th>

						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_special_work_issue; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_special_work_receive; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $gr_special_work_wip_color; ?>'> <? echo $gr_emb_wip; ?> </th>


						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_today_cutting_delivery; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_cutting_delivery; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $grand_cutting_delivery_wip_color; ?>'> <? echo $grand_cutting_delivery_wip; ?> </th>
						<th width="80">&nbsp;</th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_today_sew_input; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_sew_input; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $grand_sewing_wip_color; ?>'> <? echo $grand_sewing_wip; ?> </th>


						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_today_sew_output; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_sew_out; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $gr_sewout_wip_color; ?>'> <? echo $gr_sewout_wip; ?> </th>


						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_wash_issue; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_total_wash_receive; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $gr_wash_wip_color; ?>'> <? echo $gr_wash_wip; ?> </th>

						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $grand_finishing_entry_today + $grand_poly_today; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $grand_finishing_entry_total + $grand_poly_total; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $gr_iron_wip_color; ?>'> <? echo $grand_finishing_rec_wip + $grand_poly_wip; ?> </th>

						<!-- <th width="60" align="center" style='word-break:break-all;word-wrap: break-word;' > <? // echo $grand_poly_today;
																													?>  </th>
			            	<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;' > <? // echo $grand_poly_total;
																												?> </th>
			            	<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $gr_poly_wip_color; ?>' > <? // echo $grand_poly_wip;
																																				?> </th> -->

						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $grand_finish_today; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $grand_finish_total; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;<? echo $gr_finishing_rec_wip_color; ?>'> <? echo $grand_finish_wip; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_wise_carton_today; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'><? echo $gr_wise_carton_total; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_wise_buyer_ins_today; ?></th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_wise_buyer_ins_total; ?> </th>
						<th width="60" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_wise_buyer_ins_wip; ?> </th>
						<th width="80" align="center" style='word-break:break-all;word-wrap: break-word;'> <? echo $gr_ex_fact; ?> </th>
						<th width="80" align="center">
							<p> <? echo $grand_ex_fac_excess; ?> </p>
						</th>
						<th width="80" align="center">
							<p> <? echo $grand_ex_fac_short; ?> </p>
						</th>
						<th width="80">&nbsp;</th>

					</tr>


				</table>

			</div>


		</div>



		<?
		foreach (glob("$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}
		$name = time();
		$filename = $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		echo "$total_data####$filename";
		exit();
	} else if ($type == 4) //Show4//kamrul
	{
		$cbo_company_name=str_replace("'", "", $cbo_company_name);
		if (str_replace("'", "", $cbo_buyer_name) == 0)  $buyer_name = "";
		else $buyer_name = "and b.buyer_name=" . str_replace("'", "", $cbo_buyer_name) . "";
		$cbo_year = str_replace("'", "", $cbo_year_selection);
		if ($db_type == 2) {
			$year_cond = ($cbo_year) ? " and to_char(b.insert_date,'YYYY')=$cbo_year" : "";
		} else {
			$year_cond = ($cbo_year) ? " and year(b.insert_date)=$cbo_year" : "";
		}
		$order_cond = "";
		$order_cond2 = "";
		$job_cond_id2 = "";
		$jobs = explode(",", str_replace("'", "", $txt_job_no));
		$jobs_id = "'" . implode("','", $jobs) . "'";
		if (str_replace("'", "", $hidden_job_id))  $job_cond_id2 = "and b.id in(" . str_replace("'", "", $hidden_job_id) . ")";

		if (str_replace("'", "", $txt_job_no))  $job_no_cond2 = " and b.job_no_prefix_num in($jobs_id)";

		if (str_replace("'", "", $hidden_order_id)) {
			$order_cond2 = " and c.id in (" . str_replace("'", "", $hidden_order_id) . ")";
		}

		if (str_replace("'", "", $txt_order_no))  $order_no_cond2 = "and c.po_number =(" . trim($txt_order_no) . ")";

		$shipping_status_cond = "";
		if (str_replace("'", "", $cbo_status) == 3) $shipping_status_cond = " and c.shiping_status=3";
		else if (str_replace("'", "", $cbo_status) == 2) $shipping_status_cond = " and c.shiping_status=2";
		else if (str_replace("'", "", $cbo_status) == 1) $shipping_status_cond = " and c.shiping_status=1";
		else $shipping_status_cond = "";

		if (str_replace("'", "", trim($txt_date_from)) == "" || str_replace("'", "", trim($txt_date_to)) == "") $country_ship_date = "";
		else $country_ship_date = " and e.country_ship_date between $txt_date_from and $txt_date_to";


		$prod_reso_allo = return_field_value("auto_update", "variable_settings_production", "company_name='$cbo_company_name' and variable_list=23 and is_deleted=0 and status_active=1");
		/*if(str_replace("'", "", $txt_order_no)=="" )
		{
			$order_cond2="";
		}*/
		if (str_replace("'", "", $txt_job_no) == "") {
			$job_cond_id2 = "";
		}


		$companyArr = return_library_array("SELECT id,company_name FROM lib_company WHERE status_active=1 and is_deleted=0 and id='$cbo_company_name' ", "id", "company_name");

		$line_lib = return_library_array("SELECT id,line_name from lib_sewing_line where company_name='$cbo_company_name'", "id", "line_name");
		if ($prod_reso_allo == 1) {

			$line_libr = "SELECT id,line_number from prod_resource_mst where company_id='$cbo_company_name' and is_deleted=0 ";
			foreach (sql_select($line_libr) as $row) {
				$line = '';
				$line_number = explode(",", $row[csf('line_number')]);
				foreach ($line_number as $val) {
					if ($line == '') $line = $line_lib[$val];
					else $line .= "," . $line_lib[$val];
				}
				$line_lib_resource[$row[csf('id')]] = $line;
			}
		}
		$color_lib = return_library_array("SELECT id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		if (str_replace("'", "", $txt_production_date) != "") {
			if ($db_type == 0) {
				$production_date = change_date_format(str_replace("'", "", $txt_production_date), "yyyy-mm-dd", "");
			} else if ($db_type == 2) {
				$production_date = change_date_format(str_replace("'", "", $txt_production_date), "", "", 1);
			}
			$date_cond = " and a.production_date='$production_date'";
		}

		if ($db_type == 0) $group_concat = "group_concat(distinct(a.sewing_line)) as sewing_line";
		if ($db_type == 2) $group_concat = "listagg(a.sewing_line,',') within group (order by a.sewing_line) as sewing_line";



		$sql = "SELECT b.style_ref_no,b.buyer_name ,a.po_break_down_id,c.po_number,a.item_number_id,e.color_number_id,a.sewing_line ,c.shipment_date,
    	sum( case when a.production_type=4 and d.production_type=4 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_sew_input,

    	sum( case when a.production_type=5 and d.production_type=5 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_sew_output,

    	sum( case when a.production_type=1 and d.production_type=1 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_cutting,

    	sum( case when a.production_type=2 and d.production_type=2  and a.embel_name=3 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_wash_issue,

    	sum( case when a.production_type=3 and d.production_type=3  and a.embel_name=3 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_wash_receive,

    	sum( case when a.production_type=8 and d.production_type=8   and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_finish,

    	sum( case when a.production_type=4 and d.production_type=4  then d.production_qnty else 0 end ) as total_sew_input,

    	sum( case when a.production_type=5 and d.production_type=5  then d.production_qnty else 0 end ) as total_sew_out,

    	sum( case when a.production_type=1 and d.production_type=1  then d.production_qnty else 0 end ) as total_cutting,

    	sum( case when a.production_type=2 and d.production_type=2 and a.embel_name=3 then d.production_qnty else 0 end ) as total_wash_issue,

    	sum( case when a.production_type=3 and d.production_type=3 and a.embel_name=3 then d.production_qnty else 0 end ) as total_wash_receive,

    	sum( case when a.production_type=8 and d.production_type=8  then d.production_qnty else 0 end ) as total_finish

       FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a, pro_garments_production_dtls d, wo_po_color_size_breakdown e  WHERE b.id = c.job_id  and c.id = a.po_break_down_id and a.id = d.mst_id   and d.color_size_break_down_id = e.id and a.po_break_down_id=e.po_break_down_id  and a.status_active = 1 and a.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0  and c.status_active = 1 and c.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0
           $company_name  $buyer_name $job_cond_id2 $job_no_cond2 $order_no_cond2   $shipping_status_cond  $order_cond2 $country_ship_date  $year_cond $brand_name_cond $season_name_cond $season_year_cond
          group by b.style_ref_no ,b.buyer_name ,a.po_break_down_id,c.po_number,a.item_number_id,e.color_number_id ,c.shipment_date ,c.shiping_status,a.sewing_line ";
		//echo $sql; die;
		$production_data = sql_select($sql);
		$style_po_wise_line = array();
		foreach ($production_data as $vals) {
			if ($style_po_wise_line[$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]] == "") {
				$style_po_wise_line[$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]] .= $vals[csf("sewing_line")];
			} else {
				$style_po_wise_line[$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]] .= ',' . $vals[csf("sewing_line")];
			}
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_sew_input"] += $vals[csf("today_sew_input")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_sew_output"] += $vals[csf("today_sew_output")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_cutting"] += $vals[csf("today_cutting")];




			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_emb_issue"] += $vals[csf("today_emb_issue")];



			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_wash_issue"] += $vals[csf("today_wash_issue")];




			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_emb_receive"] += $vals[csf("today_emb_receive")];



			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_wash_receive"] += $vals[csf("today_wash_receive")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_finish"] += $vals[csf("today_finish")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["sewing_line"] = $vals[csf("sewing_line")];



			$style_wise_sub[$vals[csf("style_ref_no")]]["today_sew_input"] += $vals[csf("today_sew_input")];

			$style_wise_sub[$vals[csf("style_ref_no")]]["today_sew_output"] += $vals[csf("today_sew_output")];

			$style_wise_sub[$vals[csf("style_ref_no")]]["today_cutting"] += $vals[csf("today_cutting")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["today_finish"] += $vals[csf("today_finish")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["today_iron"] += $vals[csf("today_iron")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["today_finishing_entry"] += $vals[csf("today_finishing_entry")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["today_poly"] += $vals[csf("today_poly")];
			$today_all_po_arr[$vals[csf("po_break_down_id")]] = $vals[csf("po_break_down_id")];
			$style_wise_buyer_arr[$vals[csf("style_ref_no")]] = $vals[csf("buyer_name")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_sew_input"] += $vals[csf("total_sew_input")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_sew_out"] += $vals[csf("total_sew_out")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_cutting"] += $vals[csf("total_cutting")];



			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_wash_issue"] += $vals[csf("total_wash_issue")];


			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_wash_receive"] += $vals[csf("total_wash_receive")];

			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_finish"] += $vals[csf("total_finish")];




			$style_wise_sub[$vals[csf("style_ref_no")]]["total_sew_input"] += $vals[csf("total_sew_input")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_sew_out"] += $vals[csf("total_sew_out")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_cutting"] += $vals[csf("total_cutting")];

			$style_wise_sub[$vals[csf("style_ref_no")]]["total_wash_issue"] += $vals[csf("total_wash_issue")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_wash_receive"] += $vals[csf("total_wash_receive")];

			$style_wise_sub[$vals[csf("style_ref_no")]]["total_finish"] += $vals[csf("total_finish")];







			$style_wise_sub[$vals[csf("style_ref_no")]]["sewout_wip"] += ($vals[csf("total_sew_out")] - $vals[csf("total_sew_input")]);
		}
		// print_r($data_array);
		//  echo "<pre>";
		// print_r($style_po_wise_line);die;
		$today_all_po_id = implode(",",  $today_all_po_arr);
		$all_po_arr = explode(",", $today_all_po_id);
		$po_chunk_cond = "";
		if ($db_type == 2 and count($all_po_arr) > 999) {

			$all_po_arr = array_chunk($all_po_arr, 999);
			foreach ($all_po_arr as $key => $val) {
				$values = implode(",", $val);
				if ($po_chunk_cond == "") {
					$po_chunk_cond = " and ( a.po_break_down_id in ($values) ";
				} else {
					$po_chunk_cond .= " or a.po_break_down_id in ($values) ) ";
				}
			}
		} else {
			$po_chunk_cond = " and a.po_break_down_id in ($today_all_po_id) ";
		}
		$new_po_cond = str_replace("a.po_break_down_id", "b.id", $po_chunk_cond);
		$style_wise_ctq_arr = array();
		$style_wise_ctq_sql = "SELECT a.style_ref_no,sum(case when c.production_date=$txt_production_date then c.carton_qty else 0 end ) as today_cartoon,sum(c.carton_qty) as total_cartoon from wo_po_details_master a,wo_po_break_down b,pro_garments_production_mst c where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  c.production_type=8 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id $new_po_cond group by  a.style_ref_no";
		foreach (sql_select($style_wise_ctq_sql) as $keys => $vals) {
			$style_wise_ctq_arr[$vals[csf("style_ref_no")]]["today_cartoon"] += $vals[csf("today_cartoon")];
			$style_wise_ctq_arr[$vals[csf("style_ref_no")]]["total_cartoon"] += $vals[csf("total_cartoon")];
		}

		// Woven Carton Qnty Sql

		$style_wise_woven_ctq_arr = array();
		$style_wise_woven_ctq_sql = " SELECT a.style_ref_no,
		SUM (
			CASE
				WHEN d.EX_FACTORY_DATE = $txt_production_date THEN d.carton_qnty
				ELSE 0
			END)
			AS today_cartoon,
		SUM (d.carton_qnty)
			AS total_cartoon
		FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c, pro_ex_factory_mst d
		where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and
		 d.status_active = 1
        and d.is_deleted = 0 and  c.production_type=8 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and  b.id = d.po_break_down_id $new_po_cond group by  a.style_ref_no";
		// echo $style_wise_woven_ctq_sql;die;
		foreach (sql_select($style_wise_woven_ctq_sql) as $keys => $vals) {
			$style_wise_woven_ctq_arr[$vals[csf("style_ref_no")]]["today_cartoon"] += $vals[csf("today_cartoon")];
			$style_wise_woven_ctq_arr[$vals[csf("style_ref_no")]]["total_cartoon"] += $vals[csf("total_cartoon")];
		}
		// echo "<pre>";
		// print_r($style_wise_woven_ctq_arr);

		$new_po_cond2 = str_replace("a.po_break_down_id", "c.po_break_down_id", $po_chunk_cond);
		$style_wise_ins_arr = array();
		$po_wise_ins_sql = "SELECT a.style_ref_no,b.id, sum(case when inspection_date=$txt_production_date then inspection_qnty else 0 end ) as today_inspection,sum(inspection_qnty) as total_inspection from wo_po_details_master a,wo_po_break_down b,pro_buyer_inspection c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and  a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  $new_po_cond2 group by a.style_ref_no,b.id ";
		foreach (sql_select($po_wise_ins_sql) as $keys => $vals) {
			$style_wise_ins_arr[$vals[csf("style_ref_no")]][$vals[csf("id")]]["today_inspection"] += $vals[csf("today_inspection")];
			$style_wise_ins_arr[$vals[csf("style_ref_no")]][$vals[csf("id")]]["total_inspection"] += $vals[csf("total_inspection")];
		}

		//echo $po_chunk_cond;die;

		// query for cutting delivery to input challan
		$cutting_delevery_sql = "SELECT b.style_ref_no ,a.po_break_down_id,c.shipment_date,c.po_number,a.item_number_id,e.color_number_id,
    	sum( case when a.production_type=9 and d.production_type=9   and a.cut_delivery_date=$txt_production_date then d.production_qnty else 0 end ) as today_cutting_delivery,
    	sum( case when a.production_type=9 and d.production_type=9 then d.production_qnty else 0 end ) as total_cutting_delivery

       FROM wo_po_details_master b, wo_po_break_down c,pro_cut_delivery_order_dtls a, pro_cut_delivery_color_dtls d, wo_po_color_size_breakdown e  WHERE b.job_no = c.job_no_mst  and c.id = a.po_break_down_id and a.id = d.mst_id   and d.color_size_break_down_id = e.id and a.po_break_down_id=e.po_break_down_id and a.status_active = 1 and a.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0  and c.status_active = 1 and c.is_deleted = 0
          and d.color_size_break_down_id is not null and d.color_size_break_down_id <> 0 $company_name  $buyer_name $job_no_cond2   $shipping_status_cond $order_cond $order_cond2  $po_chunk_cond    group by b.style_ref_no ,a.po_break_down_id,c.po_number,a.item_number_id,e.color_number_id ,c.shipment_date";

		foreach (sql_select($cutting_delevery_sql) as $vals) {
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_cutting_delivery"] += $vals[csf("today_cutting_delivery")];
			$data_array[$vals[csf("style_ref_no")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_cutting_delivery"] += $vals[csf("total_cutting_delivery")];

			$style_wise_sub[$vals[csf("style_ref_no")]]["today_cutting_delivery"] += $vals[csf("today_cutting_delivery")];
			$style_wise_sub[$vals[csf("style_ref_no")]]["total_cutting_delivery"] += $vals[csf("total_cutting_delivery")];
		}



		foreach ($data_array as $style_id => $style_data) {
			$kk = 0;
			$ccc = 0;
			foreach ($style_data as $item_id => $item_data) {


				foreach ($item_data as $po_id => $po_data) {
					$color_span = 0;
					foreach ($po_data as $shipment_date => $ship_date_data) {


						foreach ($ship_date_data as $color_id => $color_data) {
							$buyer_ins_row_span_arr[$style_id][$po_id] += 1;
							$color_span++;
							$ccc++;
							$kk++;
						}
					}
					$style_wise_span_array[$style_id][$item_id][$po_id] = $color_span;
				}
			}
			$style_wise_buyer_span[$style_id] = $kk;
		}


		$po_id_arr = str_replace("a.po_break_down_id", "id", $po_chunk_cond);
		$po_id_arr2 = str_replace("a.po_break_down_id", "b.po_break_down_id", $po_chunk_cond);
		$po_id_arr3 = str_replace("a.po_break_down_id", "po_breakdown_id", $po_chunk_cond);

		$po_lib_arr = return_library_array("SELECT id,po_number FROM wo_po_break_down WHERE status_active=1 AND is_deleted=0 $po_id_arr  ", "id", "po_number");

		$plan_cut_sql = "SELECT a.style_ref_no,b.po_break_down_id,b.item_number_id,b.color_number_id, SUM(b.plan_cut_qnty) as plan_cut ,sum(b.order_quantity) as order_quantity FROM wo_po_details_master a, wo_po_color_size_breakdown b  WHERE a.job_no=b.job_no_mst and a.status_active=1 and  b.status_active=1 AND b.is_deleted=0  $po_id_arr2 GROUP BY a.style_ref_no,b.po_break_down_id,b.item_number_id,b.color_number_id ";

		$plan_cut_result = sql_select($plan_cut_sql);
		foreach ($plan_cut_result as $val_plan) {
			$plan_cut_arr[$val_plan[csf("po_break_down_id")]][$val_plan[csf("item_number_id")]][$val_plan[csf("color_number_id")]]["plan_cut"] += $val_plan[csf("plan_cut")];
			$plan_cut_arr[$val_plan[csf("po_break_down_id")]][$val_plan[csf("item_number_id")]][$val_plan[csf("color_number_id")]]["order_quantity"] += $val_plan[csf("order_quantity")];

			$plan_cut_arr_gross[$val_plan[csf("po_break_down_id")]]["order_quantity"] += $val_plan[csf("order_quantity")];

			$style_wise_sub[$val_plan[csf("style_ref_no")]]["plan_cut"] += $val_plan[csf("plan_cut")];
			$style_wise_sub[trim($val_plan[csf("style_ref_no")])]["order_quantity"] += $val_plan[csf("order_quantity")];
		}


		$sql_result_variable = sql_select("select ex_factory,production_entry from variable_settings_production where company_name=$cbo_company_name and variable_list=1 and status_active=1");
		$production_level = $sql_result_variable[0][csf("ex_factory")];
		if ($production_level != 1) {
			$ex_fac_sql = "SELECT  a.style_ref_no,c.id as po_id,f.item_number_id,f.color_number_id,sum(CASE WHEN d.entry_form!=85 THEN e.production_qnty ELSE 0 END)-sum(CASE WHEN d.entry_form=85 THEN e.production_qnty ELSE 0 END) as product_qty
					FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d, pro_ex_factory_dtls e,wo_po_color_size_breakdown f
					WHERE
					a.job_no = c.job_no_mst and
					c.id=d.po_break_down_id  and
					d.id=e.mst_id and
					e.color_size_break_down_id=f.id and
					c.id=f.po_break_down_id and a.company_name=$cbo_company_name  and a.is_deleted =0 and
					a.status_active =1 and e.is_deleted =0 and
					e.status_active =1 and c.id in ($today_all_po_id)
					group by a.style_ref_no,c.id,f.item_number_id,f.color_number_id";
			foreach (sql_select($ex_fac_sql) as $key => $value) {
				$ex_fac_arr[$value[csf("po_id")]][$value[csf("item_number_id")]][$value[csf("color_number_id")]] = $value[csf("product_qty")];
				$style_wise_sub[$value[csf("style_ref_no")]]["ex_fact"] += $value[csf("product_qty")];
			}
		} else {
			$ex_fac_sql = "SELECT  a.style_ref_no,b.po_break_down_id ,sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as product_qty
					FROM wo_po_details_master a,wo_po_break_down c, pro_ex_factory_mst b WHERE a.job_no=c.job_no_mst and c.id=b.po_break_down_id and  b.is_deleted =0 and
					 b.status_active =1  and c.status_active=1 and c.is_deleted=0 and a.status_active=1  $po_id_arr2
					group by po_break_down_id,a.style_ref_no ";

			foreach (sql_select($ex_fac_sql) as $key => $value) {
				$ex_fac_arr[$value[csf("po_break_down_id")]] += $value[csf("product_qty")];
				$style_wise_sub[$value[csf("style_ref_no")]]["ex_fact"] += $value[csf("product_qty")];
			}
		}
		$sql_pre_cost = "SELECT a.po_break_down_id,a.color_number_id,(b.plan_cut_qnty/a.pcs)*a.requirment  as cons from wo_pre_cost_fabric_cost_dtls c ,wo_pre_cos_fab_co_avg_con_dtls a,wo_po_color_size_breakdown b where c.id=a.PRE_COST_FABRIC_COST_DTLS_ID and a.COLOR_SIZE_TABLE_ID=b.id and b.po_break_down_id=a.po_break_down_id and   a.po_break_down_id <>0 and c.uom=12  $po_chunk_cond  ";
		foreach (sql_select($sql_pre_cost) as $key => $val) {
			$precost_finish_arr[$val[csf("po_break_down_id")]][$val[csf("color_number_id")]] += $val[csf("cons")];
		}
		// echo "<pre>";
		// print_r($precost_finish_new_arr);
		$budget_sql = "SELECT a.po_break_down_id,b.contrast_color_id ,b.gmts_color_id from wo_pre_cos_fab_co_avg_con_dtls a,wo_pre_cos_fab_co_color_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id $po_chunk_cond";
		foreach (sql_select($budget_sql) as $key => $val) {
			$contrast_wise_gmt[$val[csf("po_break_down_id")]][$val[csf("contrast_color_id")]] = $val[csf("gmts_color_id")];
		}




		// echo"<pre>";


		ob_start();
		?>

		<script type="text/javascript">
			setFilterGrid('table_body', -1);
		</script>
		<br> <br> <br>
		<div style="width:2170px;max-height:400px;">
			<table width="2150" cellpadding="0" cellspacing="0">

				<tr>
					<td align="center"><b style="font-size: 21px;">Daily Production Progress Report </b></td>
				</tr>
				<tr>
					<td align="center"><b style="font-size: 21px;"><? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?></b> </td>
				</tr>
				<tr>
					<td align="center"><b style="font-size: 21px;">Date: <? echo str_replace("'", "",  $txt_production_date); ?> </b></td>
				</tr>
			</table>
			<table class="rpt_table" width="2150" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
				<thead>

					<tr>
						<th width="40">
							<p>SI</p>
						</th>
						<th width="100">
							<p>Buyer</p>
						</th>
						<th width="100">
							<p>Style</p>
						</th>
						<th width="100">
							<p>Item</p>
						</th>
						<th width="100">
							<p>PO NO</p>
						</th>
						<th width="100">
							<p>Color</p>
						</th>

						<th width="90">
							<p>Order Qty.</p>
						</th>


						<th width="90">
							<p>Today Cutting</p>
						</th>
						<th width="90">
							<p>Total Cutting</p>
						</th>
						<th width="90">
							<p>Cuting Balance</p>
						</th>
						<th width="90">
							<p>Line</p>
						</th>
						<th width="90">
							<p>Today Input</p>
						</th>
						<th width="90">
							<p>Total Input</p>
						</th>
						<th width="90">
							<p>Input Balance</p>
						</th>
						<th width="90">
							<p>Today Output</p>
						</th>
						<th width="90">
							<p>Total Output</p>
						</th>
						<th width="90">
							<p>Output Balance</p>
						</th>
						<th width="105">
							<p>Today Wash Sent</p>
						</th>
						<th width="105">
							<p>Total Wash Sent </p>
						</th>
						<th width="105">
							<p>Today Wash Rcv</p>
						</th>
						<th width="105">
							<p>Total Wash Rcv</p>
						</th>
						<th width="105">
							<p>Wash Balance</p>
						</th>
						<th width="90">
							<p>Today Pack</p>
						</th>
						<th width="90">
							<p>Total Pack</p>
						</th>
						<th width="90">
							<p>Pack Balance</p>
						</th>

					</tr>


				</thead>
			</table>

			<div style="width:2170px;max-height:400px; overflow-y:scroll;float: left;" id="scroll_body">
				<table class="rpt_table" width="2150" cellpadding="0" cellspacing="0">

					<?


					$m = 1;
					$jj = 0;


					$gr_today_cutting = 0;
					$gr_total_cutting = 0;
					$gr_total_cutting_bal = 0;

					$gr_today_sew_input = 0;
					$gr_total_sew_input = 0;
					$gr_total_sew_input_bal = 0;

					$gr_today_sew_output = 0;
					$gr_total_sew_out = 0;
					$gr_total_sew_out_bal = 0;
					$gr_today_wash_issue = 0;
					$gr_total_wash_issue = 0;
					$gr_total_wash_issue_bal = 0;
					$gr_today_wash_receive = 0;
					$gr_total_wash_receive = 0;
					$gr_total_wash_receive_bal = 0;
					$gr_today_finish = 0;
					$gr_total_finish = 0;
					$grand_total_finsish_bal = 0;


					foreach ($data_array as $style_id => $style_data) {
						$pp = 0;


						$style_sewing_wip = 0;

						foreach ($style_data as $item_id => $item_data) {

							foreach ($item_data as $po_id => $po_data) {
								$l = 0;
								$nn = 0;
								foreach ($po_data as $shipment_date => $ship_date_data) {



									foreach ($ship_date_data as  $color_id => $color_data) {
										if ($m % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";
										$jj++;


					?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $jj; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $jj; ?>">

											<?

											?>
											<td width="40" align="center">
												<p><? echo $m;
													$m++; ?></p>
											</td>
											<td align="center" width="101" <? echo $style_wise_buyer_span[$style_id]; ?>>
												<p><? echo $buyer_arr[$style_wise_buyer_arr[$style_id]]; ?></p>
											</td>
											<td align="center" width="100" <? echo $style_wise_buyer_span[$style_id]; ?>>
												<p><? echo $style_id; ?></p>
											</td>
											<?



											?>
											<td align="center" width="102" <? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>>
												<p><? echo $garments_item[$item_id]; ?></p>
											</td>

											<?
											?>



											<td align="center" width="101" <? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>>
												<p><? echo $po_lib_arr[$po_id]; ?></p>
											</td>
											<?


											?>



											<td width="101" align="center">
												<p><? echo $color_lib[$color_id]; ?></p>
											</td>
											<?

											$sewing_in_wip = $color_data["total_sew_input"] - $color_data["total_cutting_delivery"];
											$swin_bal_color = ($sewing_in_wip < 0) ? " color:red; " : "color:black; ";
											$fab_bal_color = ($fab_bal < 0) ? " color:red; " : "color:black; ";
											$cutting_bal = $plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"] - $color_data["total_cutting"];




											$swout_wip = $color_data["total_sew_out"] - $color_data["total_sew_input"];
											$swout_wip_color = ($swout_wip < 0) ? " color:red; " : "color:black; ";
											$wash_bal = ($color_data["total_wash_issue"] - $color_data["total_wash_receive"]);



											?>

											<td width="91" align="center">
												<p><? echo   $plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"]; ?></p>
											</td>



											<td width="91" align="center">
												<p><? echo $color_data["today_cutting"]; ?></p>
											</td>
											<td width="90" align="center">
												<p><? echo $color_data["total_cutting"]; ?></p>
											</td>
											<td width="90" align="center">
												<p><? echo $cutting_bal; ?></p>
											</td>


											<?

											?>
											<td align="center" width="80" <? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>>
												<p>
													<?

													$line = array_filter(array_unique(explode(",", $style_po_wise_line[$style_id][$po_id])));

													$line_name = "";
													foreach ($line as $v) {
														if ($prod_reso_allo == 1) {
															$line_name .= $line_lib_resource[$v] . ",";
														} else {
															$line_name .= $line_lib[$v] . ",";
														}
													}
													echo chop($line_name, ","); ?></p>
											</td>


											<?
											$l++

											?>



											<td width="90" align="center">
												<p><? echo $color_data["today_sew_input"]; ?></p>
											</td>
											<td width="90" align="center">
												<p><? echo $color_data["total_sew_input"]; ?></p>
											</td>
											<td width="90" align="center"><? echo $color_data["total_cutting"] - $color_data["total_sew_input"]; ?></p>
											</td>
											<td width="90" align="center">
												<p><? echo $color_data["today_sew_output"]; ?></p>
											</td>
											<td width="90" align="center">
												<p><? echo $color_data["total_sew_out"]; ?></p>
											</td>
											<td width="90" align="center">
												<p><? echo $color_data["total_sew_input"] - $color_data["total_sew_out"]; ?></p>
											</td>
											<td width="105" align="center">
												<p><? echo $color_data["today_wash_issue"]; ?></p>
											</td>
											<td width="105" align="center">
												<p><? echo $color_data["total_wash_issue"]; ?></p>
											</td>
											<td width="105" align="center">
												<p><? echo $color_data["today_wash_receive"]; ?></p>
											</td>
											<td width="105" align="center">
												<p><? echo $color_data["total_wash_receive"]; ?></p>
											</td>
											<td width="105" align="center" <? echo $wash_bal_color; ?>'>
												<p><? echo  $wash_bal; ?></p>
											</td>



											<td width="90" align="center">
												<p><? echo $color_data["today_finish"]; ?></p>
											</td>
											<td width="90" align="center">
												<p><? echo $color_data["total_finish"]; ?></p>
											</td>
											<td width="90" align="center">
												<p><? echo $color_data["total_wash_receive"] - $color_data["total_finish"]; ?></p>
											</td>






										</tr>


					<?

										$l++;


										$gr_today_cutting += $color_data["today_cutting"];
										$gr_total_cutting += $color_data["total_cutting"];;
										$gr_total_cutting_bal += $cutting_bal;

										$gr_today_sew_input += $color_data["today_sew_input"];
										$gr_total_sew_input += $color_data["total_sew_input"];
										$gr_total_sew_input_bal += $color_data["total_cutting"] - $color_data["total_sew_input"];

										$gr_today_sew_output += $color_data["today_sew_output"];
										$gr_total_sew_out += $color_data["total_sew_out"];
										$gr_total_sew_out_bal += $color_data["total_sew_input"] - $color_data["total_sew_out"];
										$gr_today_wash_issue += $color_data["today_wash_issue"];
										$gr_total_wash_issue += $color_data["total_wash_issue"];
										$gr_today_wash_receive += $color_data["today_wash_receive"];

										$gr_total_wash_receive += $color_data["total_wash_receive"];
										$gr_total_wash_receive_bal += $wash_bal;
										$gr_today_finish += $color_data["today_finish"];
										$gr_total_finish += $color_data["total_finish"];
										$grand_total_finsish_bal += $color_data["total_wash_receive"] - $color_data["total_finish"];
									}
								}
							}
						}
					}


					?>
					<tr bgcolor="#A4C2EA" onClick="change_color('tr333_<? echo $jj; ?>', '<? echo $bgcolor; ?>')" id="tr333_<? echo $jj; ?>">
						<th width="40">
							<p></p>
						</th>
						<th width="100">
							<p></p>
						</th>
						<th width="100">
							<p></p>
						</th>
						<th width="100">
							<p></p>
						</th>
						<th width="100">
							<p></p>
						</th>
						<th width="100">
							<p></p>
						</th>

						<th width="90">
							<p></p>
						</th>


						<th width="90">
							<p><? echo $gr_today_cutting; ?></p>
						</th>
						<th width="90">
							<p><? echo $gr_total_cutting ?></p>
						</th>
						<th width="90">
							<p><? echo $gr_total_cutting_bal ?></p>
						</th>
						<th width="90">
							<p>Grand Total</p>
						</th>
						<th width="90">
							<p><? echo $gr_today_sew_input; ?></p>
						</th>
						<th width="90">
							<p><? echo $gr_total_sew_input ?></p>
						</th>
						<th width="90">
							<p><? echo $gr_total_sew_input_bal; ?></p>
						</th>
						<th width="90">
							<p><? echo $gr_today_sew_output; ?></p>
						</th>
						<th width="90">
							<p><? echo $gr_total_sew_out; ?></p>
						</th>
						<th width="90">
							<p><? echo $gr_total_sew_out_bal; ?></p>
						</th>
						<th width="105">
							<p><? echo $gr_today_wash_issue; ?></p>
						</th>
						<th width="105">
							<p><? echo $gr_total_wash_issue; ?> </p>
						</th>
						<th width="105">
							<p><? echo $gr_today_wash_receive; ?></p>
						</th>
						<th width="105">
							<p><? echo $gr_total_wash_receive; ?></p>
						</th>
						<th width="105">
							<p><? echo $gr_total_wash_receive_bal; ?></p>
						</th>
						<th width="90">
							<p><? echo $gr_today_finish; ?></p>
						</th>
						<th width="90">
							<p><? echo $gr_total_finish; ?></p>
						</th>
						<th width="90">
							<p><? echo $grand_total_finsish_bal; ?></p>
						</th>

					</tr>






				</table>

			</div>


		</div>



		<?
		foreach (glob("$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}
		$name = time();
		$filename = $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		echo "$total_data####$filename";
		exit();
	} else if ($type == 5) //Show5
	{
		$cbo_company_name=str_replace("'", "",$cbo_company_name);
		$cbo_year = str_replace("'", "", $cbo_year_selection);
		if ($db_type == 2) {
			$year_cond = ($cbo_year) ? " and to_char(b.insert_date,'YYYY')=$cbo_year" : "";
		} else {
			$year_cond = ($cbo_year) ? " and year(b.insert_date)=$cbo_year" : "";
		}
		$order_cond = "";
		$order_cond2 = "";
		$job_cond_id2 = "";
		$jobs = explode(",", str_replace("'", "", $txt_job_no));
		$jobs_id = "'" . implode("','", $jobs) . "'";
		if (str_replace("'", "", $hidden_job_id))  $job_cond_id2 = "and b.id in(" . str_replace("'", "", $hidden_job_id) . ")";

		if (str_replace("'", "", $txt_job_no))  $job_no_cond2 = " and b.job_no_prefix_num in($jobs_id)";

		if (str_replace("'", "", $hidden_order_id)) {
			$order_cond2 = " and c.id in (" . str_replace("'", "", $hidden_order_id) . ")";
		}

		if (str_replace("'", "", $txt_order_no))  $order_no_cond2 = "and c.po_number =(" . trim($txt_order_no) . ")";

		$shipping_status_cond = "";
		if (str_replace("'", "", $cbo_status) == 3) $shipping_status_cond = " and c.shiping_status=3";
		else if (str_replace("'", "", $cbo_status) == 2) $shipping_status_cond = " and c.shiping_status=2";
		else if (str_replace("'", "", $cbo_status) == 1) $shipping_status_cond = " and c.shiping_status=1";
		else $shipping_status_cond = "";

		if (str_replace("'", "", trim($txt_date_from)) == "" || str_replace("'", "", trim($txt_date_to)) == "") $country_ship_date = "";
		else $country_ship_date = " and e.country_ship_date between $txt_date_from and $txt_date_to";


		$prod_reso_allo = return_field_value("auto_update", "variable_settings_production", "company_name='$cbo_company_name' and variable_list=23 and is_deleted=0 and status_active=1");

		if (str_replace("'", "", $txt_job_no) == "") {
			$job_cond_id2 = "";
		}

		$data_file=sql_select("select image_location, master_tble_id from common_photo_library where is_deleted=0 and form_name='knit_order_entry_front' and file_type= 1");
		// echo "<pre>"; print_r($data_file); die;
		$system_file_arr=array();
		foreach($data_file as $row)
		{
		$system_file_arr[$row['MASTER_TBLE_ID']]=$row['IMAGE_LOCATION'];
		}
		unset($data_file);

		$data_file2=sql_select("select image_location, master_tble_id from common_photo_library where is_deleted=0 and form_name='knit_order_entry_back' and file_type= 1");
		// echo "<pre>"; print_r($data_file); die;
		$system_file_arr2=array();
		foreach($data_file2 as $row)
		{
		$system_file_arr2[$row['MASTER_TBLE_ID']]=$row['IMAGE_LOCATION'];
		}
		unset($data_file2);

		$data_file3=sql_select("select image_location, master_tble_id from common_photo_library where is_deleted=0 and form_name='knit_order_entry' and file_type= 2");
		// echo "<pre>"; print_r($data_file); die;
		$system_file_arr3=array();
		foreach($data_file3 as $row)
		{
		$system_file_arr3[$row['MASTER_TBLE_ID']]=$row['IMAGE_LOCATION'];
		}
		unset($data_file3);


		$companyArr = return_library_array("SELECT id,company_name FROM lib_company WHERE status_active=1 and is_deleted=0 and id='$cbo_company_name' ", "id", "company_name");

		$line_lib = return_library_array("SELECT id,line_name from lib_sewing_line where company_name='$cbo_company_name'", "id", "line_name");
		if ($prod_reso_allo == 1) {

			$line_libr = "SELECT id,line_number from prod_resource_mst where company_id='$cbo_company_name' and is_deleted=0 ";
			foreach (sql_select($line_libr) as $row) {
				$line = '';
				$line_number = explode(",", $row[csf('line_number')]);
				foreach ($line_number as $val) {
					if ($line == '') $line = $line_lib[$val];
					else $line .= "," . $line_lib[$val];
				}
				$line_lib_resource[$row[csf('id')]] = $line;
			}
		}
		$color_lib = return_library_array("SELECT id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		if (str_replace("'", "", $txt_production_date) != "") {
			if ($db_type == 0) {
				$production_date = change_date_format(str_replace("'", "", $txt_production_date), "yyyy-mm-dd", "");
			} else if ($db_type == 2) {
				$production_date = change_date_format(str_replace("'", "", $txt_production_date), "", "", 1);
			}
			$date_cond = " and a.production_date='$production_date'";
		}

		if ($db_type == 0) $group_concat = "group_concat(distinct(a.sewing_line)) as sewing_line";
		if ($db_type == 2) $group_concat = "listagg(a.sewing_line,',') within group (order by a.sewing_line) as sewing_line";

		//--------------- Main-Query----------------------------//

		$sql = "SELECT b.id as job_id,b.job_no, b.style_ref_no,b.buyer_name ,a.po_break_down_id,c.po_number,a.item_number_id,e.color_number_id,c.shipment_date,c.shiping_status,a.sewing_line ,
    	sum( case when a.production_type=4 and d.production_type=4 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_sew_input,

    	sum( case when a.production_type=5 and d.production_type=5 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_sew_output,

    	sum( case when a.production_type=1 and d.production_type=1 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_cutting,

    	sum( case when a.production_type=2 and d.production_type=2  and a.embel_name=1 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_print_issue,

    	sum( case when a.production_type=2 and d.production_type=2  and a.embel_name=2 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_emb_issue,

    	sum( case when a.production_type=2 and d.production_type=2  and a.embel_name=3 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_wash_issue,

    	sum( case when a.production_type=3 and d.production_type=3  and a.embel_name=1 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_print_receive,


    	sum( case when a.production_type=3 and d.production_type=3  and a.embel_name=2 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_emb_receive,



    	sum( case when a.production_type=3 and d.production_type=3  and a.embel_name=3 and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_wash_receive,


    	sum( case when a.production_type=8 and d.production_type=8   and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_finish
    	,
    	sum( case when a.production_type=7 and d.production_type=7   and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_iron
    	,
    	sum( case when a.production_type=80 and d.production_type=80   and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_finishing_entry,

    	sum( case when a.production_type=11 and d.production_type=11   and a.production_date=$txt_production_date then d.production_qnty else 0 end ) as today_poly
    	,
    	sum( case when a.production_type=4 and d.production_type=4  then d.production_qnty else 0 end ) as total_sew_input,


    	sum( case when a.production_type=5 and d.production_type=5  then d.production_qnty else 0 end ) as total_sew_out,

    	sum( case when a.production_type=1 and d.production_type=1  then d.production_qnty else 0 end ) as total_cutting,

    	sum( case when a.production_type=2 and d.production_type=2 and a.embel_name=1 then d.production_qnty else 0 end ) as total_print_issue,

    	sum( case when a.production_type=2 and d.production_type=2 and a.embel_name=2 then d.production_qnty else 0 end ) as total_emb_issue,

		sum( case when a.production_type=2 and d.production_type=2 and a.embel_name=4 then d.production_qnty else 0 end ) as total_special_work_issue,

    	sum( case when a.production_type=2 and d.production_type=2 and a.embel_name=3 then d.production_qnty else 0 end ) as total_wash_issue,

    	sum( case when a.production_type=3 and d.production_type=3 and a.embel_name=1 then d.production_qnty else 0 end ) as total_print_receive,

    	sum( case when a.production_type=3 and d.production_type=3 and a.embel_name=2 then d.production_qnty else 0 end ) as total_emb_receive,

		sum( case when a.production_type=3 and d.production_type=3 and a.embel_name=4 then d.production_qnty else 0 end ) as total_special_work_rcv,

    	sum( case when a.production_type=3 and d.production_type=3 and a.embel_name=3 then d.production_qnty else 0 end ) as total_wash_receive,

    	sum( case when a.production_type=8 and d.production_type=8  then d.production_qnty else 0 end ) as total_finish,
    	sum( case when a.production_type=7 and d.production_type=7  then d.production_qnty else 0 end ) as total_iron,
    	sum( case when a.production_type=11 and d.production_type=11  then d.production_qnty else 0 end ) as total_poly,
    	sum( case when a.production_type=80 and d.production_type=80  then d.production_qnty else 0 end ) as total_finishing_entry

       FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a, pro_garments_production_dtls d, wo_po_color_size_breakdown e  WHERE b.job_no = c.job_no_mst  and c.id = a.po_break_down_id and a.id = d.mst_id   and d.color_size_break_down_id = e.id and a.po_break_down_id=e.po_break_down_id and a.status_active = 1 and a.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0  and c.status_active = 1 and c.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0
           $company_name  $buyer_name $job_cond_id2 $job_no_cond2 $order_no_cond2   $shipping_status_cond  $order_cond2 $country_ship_date  $year_cond $brand_name_cond $season_name_cond $season_year_cond
          group by b.id,b.job_no, b.style_ref_no ,b.buyer_name ,a.po_break_down_id,c.po_number,a.item_number_id,e.color_number_id ,c.shipment_date ,c.shiping_status,a.sewing_line ";
		// echo $sql;die;


		// if(!$wash_check)
		// {
		// 	$qty_source=4; // issue id
		// }
		$production_data = sql_select($sql);
		$style_po_wise_line = array();
		$job_id_array = array();

		foreach ($production_data as $vals) {
			$job_id_array[$vals['JOB_ID']] = $vals['JOB_ID'];
			if ($style_po_wise_line[$vals[csf("job_id")]][$vals[csf("po_break_down_id")]] == "") {
				$style_po_wise_line[$vals[csf("job_id")]][$vals[csf("po_break_down_id")]] .= $vals[csf("sewing_line")];
			} else {
				$style_po_wise_line[$vals[csf("job_id")]][$vals[csf("po_break_down_id")]] .= ',' . $vals[csf("sewing_line")];
			}
			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_sew_input"] += $vals[csf("today_sew_input")];
			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["style_ref_no"] = $vals[csf("style_ref_no")];


			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_sew_output"] += $vals[csf("today_sew_output")];


			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_cutting"] += $vals[csf("today_cutting")];


			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_print_issue"] += $vals[csf("today_print_issue")];


			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_emb_issue"] += $vals[csf("today_emb_issue")];



			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_wash_issue"] += $vals[csf("today_wash_issue")];


			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_print_receive"] += $vals[csf("today_print_receive")];


			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_emb_receive"] += $vals[csf("today_emb_receive")];



			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_wash_receive"] += $vals[csf("today_wash_receive")];


			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_finish"] += $vals[csf("today_finish")];
			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_iron"] += $vals[csf("today_iron")];

			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_finishing_entry"] += $vals[csf("today_finishing_entry")];

			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_poly"] += $vals[csf("today_poly")];


			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["sewing_line"] = $vals[csf("sewing_line")];
			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["shiping_status"] = $vals[csf("shiping_status")];

			$style_wise_sub[$vals[csf("job_id")]]["today_sew_input"] += $vals[csf("today_sew_input")];

			$style_wise_sub[$vals[csf("job_id")]]["today_sew_output"] += $vals[csf("today_sew_output")];

			$style_wise_sub[$vals[csf("job_id")]]["today_cutting"] += $vals[csf("today_cutting")];
			$style_wise_sub[$vals[csf("job_id")]]["today_finish"] += $vals[csf("today_finish")];
			$style_wise_sub[$vals[csf("job_id")]]["today_iron"] += $vals[csf("today_iron")];
			$style_wise_sub[$vals[csf("job_id")]]["today_finishing_entry"] += $vals[csf("today_finishing_entry")];
			$style_wise_sub[$vals[csf("job_id")]]["today_poly"] += $vals[csf("today_poly")];
			$today_all_po_arr[$vals[csf("po_break_down_id")]] = $vals[csf("po_break_down_id")];
			$style_wise_buyer_arr[$vals[csf("job_id")]] = $vals[csf("buyer_name")];
			$style_wise_job_arr[$vals[csf("job_id")]] = $vals[csf("job_no")];
			$style_wise_arr[$vals[csf("job_id")]] = $vals[csf("style_ref_no")];
			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_sew_input"] += $vals[csf("total_sew_input")];

			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_sew_out"] += $vals[csf("total_sew_out")];

			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_cutting"] += $vals[csf("total_cutting")];

			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_print_issue"] += $vals[csf("total_print_issue")];
			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_emb_issue"] += $vals[csf("total_emb_issue")];

			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_special_work_issue"] += $vals[csf("total_special_work_issue")];

			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_wash_issue"] += $vals[csf("total_wash_issue")];


			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_print_receive"] += $vals[csf("total_print_receive")];

			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_emb_receive"] += $vals[csf("total_emb_receive")];

			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_special_work_rcv"] += $vals[csf("total_special_work_rcv")];

			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_wash_receive"] += $vals[csf("total_wash_receive")];

			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_finish"] += $vals[csf("total_finish")];
			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_poly"] += $vals[csf("total_poly")];
			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_iron"] += $vals[csf("total_iron")];
			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_finishing_entry"] += $vals[csf("total_finishing_entry")];

			$style_wise_sub[$vals[csf("job_id")]]["total_sew_input"] += $vals[csf("total_sew_input")];
			$style_wise_sub[$vals[csf("job_id")]]["total_sew_out"] += $vals[csf("total_sew_out")];
			$style_wise_sub[$vals[csf("job_id")]]["total_cutting"] += $vals[csf("total_cutting")];
			$style_wise_sub[$vals[csf("job_id")]]["total_print_issue"] += $vals[csf("total_print_issue")];
			$style_wise_sub[$vals[csf("job_id")]]["total_print_receive"] += $vals[csf("total_print_receive")];
			$style_wise_sub[$vals[csf("job_id")]]["total_wash_issue"] += $vals[csf("total_wash_issue")];
			$style_wise_sub[$vals[csf("job_id")]]["today_wash_issue"] += $vals[csf("today_wash_issue")];
			$style_wise_sub[$vals[csf("job_id")]]["today_wash_receive"] += $vals[csf("today_wash_receive")];
			$style_wise_sub[$vals[csf("job_id")]]["total_wash_receive"] += $vals[csf("total_wash_receive")];
			$style_wise_sub[$vals[csf("job_id")]]["total_emb_issue"] += $vals[csf("total_emb_issue")];
			$style_wise_sub[$vals[csf("job_id")]]["total_emb_receive"] += $vals[csf("total_emb_receive")];

			$style_wise_sub[$vals[csf("job_id")]]["total_special_work_issue"] += $vals[csf("total_special_work_issue")];
			$style_wise_sub[$vals[csf("job_id")]]["total_special_work_rcv"] += $vals[csf("total_special_work_rcv")];

			$style_wise_sub[$vals[csf("job_id")]]["total_finish"] += $vals[csf("total_finish")];
			$style_wise_sub[$vals[csf("job_id")]]["total_iron"] += $vals[csf("total_iron")];
			$style_wise_sub[$vals[csf("job_id")]]["total_finishing_entry"] += $vals[csf("total_finishing_entry")];
			$style_wise_sub[$vals[csf("job_id")]]["total_poly"] += $vals[csf("total_poly")];
			$style_wise_sub[$vals[csf("job_id")]]["finish_wip"] += ($vals[csf("total_finish")] - $vals[csf("total_poly")]);
			if ($vals[csf("total_wash_receive")] > 0) {

				$style_wise_sub[$vals[csf("job_id")]]["iron_wip"] += ($vals[csf("total_iron")] - $vals[csf("total_wash_receive")]);
			} else {
				$style_wise_sub[$vals[csf("job_id")]]["iron_wip"] += ($vals[csf("total_iron")] - $vals[csf("total_sew_out")]);
			}

			$style_wise_sub[$vals[csf("job_id")]]["wash_wip"] += ($vals[csf("total_wash_issue")] - $vals[csf("total_sew_out")]);

			$style_wise_sub[$vals[csf("job_id")]]["wash_rcv_wip"] += ($vals[csf("total_wash_receive")] - $vals[csf("total_wash_issue")]);

			$style_wise_sub[$vals[csf("job_id")]]["finishing_wip"] += ($vals[csf("total_finishing_entry")] - $vals[csf("today_finishing_entry")]);
			// $style_wise_sub[$vals[csf("style_ref_no")]]["finishing_wip"]+= 12;

			$style_wise_sub[$vals[csf("job_id")]]["poly_wip"] += ($vals[csf("total_poly")] - $vals[csf("total_iron")]);

			$style_wise_sub[$vals[csf("job_id")]]["sewin_wip"] += ($vals[csf("total_sew_input")] - $vals[csf("total_print_receive")]);
			$style_wise_sub[$vals[csf("job_id")]]["sewout_wip"] += ($vals[csf("total_sew_out")] - $vals[csf("total_sew_input")]);
			$style_wise_sub[$vals[csf("job_id")]]["print_wip"] += ($vals[csf("total_print_receive")] - $vals[csf("total_print_issue")]);
			$style_wise_sub[$vals[csf("job_id")]]["emb_wip"] += ($vals[csf("total_emb_receive")] - $vals[csf("total_emb_issue")]);

			$style_wise_sub[$vals[csf("job_id")]]["special_work_wip"] += ($vals[csf("total_special_work_rcv")] - $vals[csf("total_special_work_issue")]);
		}
		//  print_r($style_po_wise_line);
		// echo "<pre>";
		// print_r($finish_qty_wip_array);die;
		$job_cond = where_con_using_array($job_id_array, 0, "a.job_id");

		// $sql_finish_input = ("SELECT emb_name ,job_no,job_id from WO_PRE_COST_EMBE_COST_DTLS where emb_name=3 and is_deleted=0 and status_active=1 $job_cond");
		//echo $sql_finish_input;die;
		$sql_finish_input=("SELECT a.job_id,a.job_no,a.po_break_down_id,a.item_number_id,a.color_number_id,a.size_number_id,b.emb_name from wo_pre_cos_emb_co_avg_con_dtls a,wo_pre_cost_embe_cost_dtls b where requirment is not null  and requirment>0  and b.id=a.pre_cost_emb_cost_dtls_id and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 $job_cond");
	    //echo $sql_finish_input;die;
		$finish_wip_arr=array();
		foreach(sql_select($sql_finish_input) as $v)
		{
			$finish_wip_arr[$v['PO_BREAK_DOWN_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]=$v['EMB_NAME'];
		}
        //   echo "<pre>" ;print_r($finish_wip_arr);die;
		$today_all_po_id = implode(",",  $today_all_po_arr);
		$all_po_arr = explode(",", $today_all_po_id);
		$po_chunk_cond = "";
		if ($db_type == 2 and count($all_po_arr) > 999) {

			$all_po_arr = array_chunk($all_po_arr, 999);
			foreach ($all_po_arr as $key => $val) {
				$values = implode(",", $val);
				if ($po_chunk_cond == "") {
					$po_chunk_cond = " and ( a.po_break_down_id in ($values) ";
				} else {
					$po_chunk_cond .= " or a.po_break_down_id in ($values) ) ";
				}
			}
		} else {
			$po_chunk_cond = " and a.po_break_down_id in ($today_all_po_id) ";
		}
		$new_po_cond = str_replace("a.po_break_down_id", "b.id", $po_chunk_cond);
		$style_wise_ctq_arr = array();
		$style_wise_ctq_sql = "SELECT a.id as job_id,a.style_ref_no,sum(case when c.production_date=$txt_production_date then c.carton_qty else 0 end ) as today_cartoon,sum(c.carton_qty) as total_cartoon from wo_po_details_master a,wo_po_break_down b,pro_garments_production_mst c where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  c.production_type=8 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id $new_po_cond group by a.id, a.style_ref_no";
		foreach (sql_select($style_wise_ctq_sql) as $keys => $vals) {
			$style_wise_ctq_arr[$vals[csf("job_id")]]["today_cartoon"] += $vals[csf("today_cartoon")];
			$style_wise_ctq_arr[$vals[csf("job_id")]]["total_cartoon"] += $vals[csf("total_cartoon")];
		}

		// Woven Carton Qnty Sql

		$style_wise_woven_ctq_arr = array();
		$style_wise_woven_ctq_sql = " SELECT a.id as job_id,a.style_ref_no,
		SUM (
			CASE
				WHEN d.EX_FACTORY_DATE = $txt_production_date THEN d.carton_qnty
				ELSE 0
			END)
			AS today_cartoon,
		SUM (d.carton_qnty)
			AS total_cartoon
		FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c, pro_ex_factory_mst d
		where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and
		 d.status_active = 1
        and d.is_deleted = 0 and  c.production_type=8 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and  b.id = d.po_break_down_id $new_po_cond group by  a.id,a.style_ref_no";
		// echo $style_wise_woven_ctq_sql;die;
		foreach (sql_select($style_wise_woven_ctq_sql) as $keys => $vals) {
			$style_wise_woven_ctq_arr[$vals[csf("job_id")]]["today_cartoon"] += $vals[csf("today_cartoon")];
			$style_wise_woven_ctq_arr[$vals[csf("job_id")]]["total_cartoon"] += $vals[csf("total_cartoon")];
		}
		// echo "<pre>";
		// print_r($style_wise_woven_ctq_arr);

		$new_po_cond2 = str_replace("a.po_break_down_id", "c.po_break_down_id", $po_chunk_cond);
		$style_wise_ins_arr = array();
		$po_wise_ins_sql = "SELECT a.id as job_id,a.style_ref_no,b.id, sum(case when inspection_date=$txt_production_date then inspection_qnty else 0 end ) as today_inspection,sum(inspection_qnty) as total_inspection from wo_po_details_master a,wo_po_break_down b,pro_buyer_inspection c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and  a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  $new_po_cond2 group by a.style_ref_no,b.id ";
		foreach (sql_select($po_wise_ins_sql) as $keys => $vals) {
			$style_wise_ins_arr[$vals[csf("job_id")]][$vals[csf("id")]]["today_inspection"] += $vals[csf("today_inspection")];
			$style_wise_ins_arr[$vals[csf("job_id")]][$vals[csf("id")]]["total_inspection"] += $vals[csf("total_inspection")];
		}

		//echo $po_chunk_cond;die;

		// query for cutting delivery to input challan
		$cutting_delevery_sql = "SELECT b.id as job_id,b.job_no, b.style_ref_no ,a.po_break_down_id,c.shipment_date,c.po_number,a.item_number_id,e.color_number_id,
    	sum( case when a.production_type=9 and d.production_type=9   and a.cut_delivery_date=$txt_production_date then d.production_qnty else 0 end ) as today_cutting_delivery,
    	sum( case when a.production_type=9 and d.production_type=9 then d.production_qnty else 0 end ) as total_cutting_delivery

       FROM wo_po_details_master b, wo_po_break_down c,pro_cut_delivery_order_dtls a, pro_cut_delivery_color_dtls d, wo_po_color_size_breakdown e  WHERE b.job_no = c.job_no_mst  and c.id = a.po_break_down_id and a.id = d.mst_id   and d.color_size_break_down_id = e.id and a.po_break_down_id=e.po_break_down_id and a.status_active = 1 and a.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0  and c.status_active = 1 and c.is_deleted = 0
          and d.color_size_break_down_id is not null and d.color_size_break_down_id <> 0 $company_name  $buyer_name $job_no_cond2   $shipping_status_cond $order_cond $order_cond2  $po_chunk_cond    group by b.id,b.job_no,b.style_ref_no ,a.po_break_down_id,c.po_number,a.item_number_id,e.color_number_id ,c.shipment_date";
		// echo $cutting_delevery_sql;die;
		foreach (sql_select($cutting_delevery_sql) as $vals) {
			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["today_cutting_delivery"] += $vals[csf("today_cutting_delivery")];
			$data_array[$vals[csf("job_id")]][$vals[csf("item_number_id")]][$vals[csf("po_break_down_id")]][$vals[csf("shipment_date")]][$vals[csf("color_number_id")]]["total_cutting_delivery"] += $vals[csf("total_cutting_delivery")];

			$style_wise_sub[$vals[csf("job_id")]]["today_cutting_delivery"] += $vals[csf("today_cutting_delivery")];
			$style_wise_sub[$vals[csf("job_id")]]["total_cutting_delivery"] += $vals[csf("total_cutting_delivery")];
		}



		foreach ($data_array as $job_id => $job_data)
		{
			$kk = 0;
			$ccc = 0;
			foreach ($job_data as $item_id => $item_data) {


				foreach ($item_data as $po_id => $po_data) {
					$color_span = 0;
					foreach ($po_data as $shipment_date => $ship_date_data) {


						foreach ($ship_date_data as $color_id => $color_data) {
							$buyer_ins_row_span_arr[$job_id][$po_id] += 1;
							$color_span++;
							$ccc++;
							$kk++;
						}
					}
					$style_wise_span_array[$job_id][$item_id][$po_id] = $color_span;
				}
			}
			$style_wise_buyer_span[$job_id] = $kk;
		}


		$po_id_arr = str_replace("a.po_break_down_id", "id", $po_chunk_cond);
		$po_id_arr2 = str_replace("a.po_break_down_id", "b.po_break_down_id", $po_chunk_cond);
		$po_id_arr3 = str_replace("a.po_break_down_id", "po_breakdown_id", $po_chunk_cond);

		$po_lib_arr = return_library_array("SELECT id,po_number FROM wo_po_break_down WHERE status_active=1 AND is_deleted=0 $po_id_arr  ", "id", "po_number");

		$plan_cut_sql = "SELECT a.id as job_id, a.style_ref_no,b.po_break_down_id,b.item_number_id,b.color_number_id, SUM(b.plan_cut_qnty) as plan_cut ,sum(b.order_quantity) as order_quantity FROM wo_po_details_master a, wo_po_color_size_breakdown b  WHERE a.job_no=b.job_no_mst and a.status_active=1 and  b.status_active=1 AND b.is_deleted=0  $po_id_arr2 GROUP BY a.id, a.style_ref_no,b.po_break_down_id,b.item_number_id,b.color_number_id ";

		$plan_cut_result = sql_select($plan_cut_sql);
		foreach ($plan_cut_result as $val_plan) {
			$plan_cut_arr[$val_plan[csf("po_break_down_id")]][$val_plan[csf("item_number_id")]][$val_plan[csf("color_number_id")]]["plan_cut"] += $val_plan[csf("plan_cut")];
			$plan_cut_arr[$val_plan[csf("po_break_down_id")]][$val_plan[csf("item_number_id")]][$val_plan[csf("color_number_id")]]["order_quantity"] += $val_plan[csf("order_quantity")];

			$plan_cut_arr_gross[$val_plan[csf("po_break_down_id")]]["order_quantity"] += $val_plan[csf("order_quantity")];

			$style_wise_sub[$val_plan[csf("job_id")]]["plan_cut"] += $val_plan[csf("plan_cut")];
			$style_wise_sub[trim($val_plan[csf("job_id")])]["order_quantity"] += $val_plan[csf("order_quantity")];
		}


		$sql_result_variable = sql_select("select ex_factory,production_entry from variable_settings_production where company_name=$cbo_company_name and variable_list=1 and status_active=1");
		$production_level = $sql_result_variable[0][csf("ex_factory")];
		if ($production_level != 1) {
			$ex_fac_sql = "SELECT a.id as job_id, a.style_ref_no,c.id as po_id,f.item_number_id,f.color_number_id,sum(CASE WHEN d.entry_form!=85 THEN e.production_qnty ELSE 0 END)-sum(CASE WHEN d.entry_form=85 THEN e.production_qnty ELSE 0 END) as product_qty
					FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d, pro_ex_factory_dtls e,wo_po_color_size_breakdown f
					WHERE
					a.job_no = c.job_no_mst and
					c.id=d.po_break_down_id  and
					d.id=e.mst_id and
					e.color_size_break_down_id=f.id and
					c.id=f.po_break_down_id and a.company_name=$cbo_company_name  and a.is_deleted =0 and
					a.status_active =1 and e.is_deleted =0 and
					e.status_active =1 and c.id in ($today_all_po_id)
					group by  a.id,a.style_ref_no,c.id,f.item_number_id,f.color_number_id";
			foreach (sql_select($ex_fac_sql) as $key => $value) {
				$ex_fac_arr[$value[csf("po_id")]][$value[csf("item_number_id")]][$value[csf("color_number_id")]] = $value[csf("product_qty")];
				$style_wise_sub[$value[csf("job_id")]]["ex_fact"] += $value[csf("product_qty")];
			}
		} else {
			$ex_fac_sql = "SELECT a.id as job_id, a.style_ref_no,b.po_break_down_id ,sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as product_qty
					FROM wo_po_details_master a,wo_po_break_down c, pro_ex_factory_mst b WHERE a.job_no=c.job_no_mst and c.id=b.po_break_down_id and  b.is_deleted =0 and
					 b.status_active =1  and c.status_active=1 and c.is_deleted=0 and a.status_active=1  $po_id_arr2
					group by po_break_down_id,a.style_ref_no, a.id ";

			foreach (sql_select($ex_fac_sql) as $key => $value) {
				$ex_fac_arr[$value[csf("po_break_down_id")]] += $value[csf("product_qty")];
				$style_wise_sub[$value[csf("job_id")]]["ex_fact"] += $value[csf("product_qty")];
			}
		}
		$sql_pre_cost = "SELECT a.po_break_down_id,a.color_number_id,(b.plan_cut_qnty/a.pcs)*a.requirment  as cons from wo_pre_cost_fabric_cost_dtls c ,wo_pre_cos_fab_co_avg_con_dtls a,wo_po_color_size_breakdown b where c.id=a.PRE_COST_FABRIC_COST_DTLS_ID and a.COLOR_SIZE_TABLE_ID=b.id and b.po_break_down_id=a.po_break_down_id and   a.po_break_down_id <>0 and c.uom=12  $po_chunk_cond  ";
		foreach (sql_select($sql_pre_cost) as $key => $val) {
			$precost_finish_arr[$val[csf("po_break_down_id")]][$val[csf("color_number_id")]] += $val[csf("cons")];
		}
		// echo "<pre>";
		// print_r($precost_finish_new_arr);
		$budget_sql = "SELECT a.po_break_down_id,b.contrast_color_id ,b.gmts_color_id from wo_pre_cos_fab_co_avg_con_dtls a,wo_pre_cos_fab_co_color_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id $po_chunk_cond";
		foreach (sql_select($budget_sql) as $key => $val) {
			$contrast_wise_gmt[$val[csf("po_break_down_id")]][$val[csf("contrast_color_id")]] = $val[csf("gmts_color_id")];
		}

		$sql_woven_recd = "SELECT c.po_breakdown_id,c.color_id,c.quantity,a.id,b.id,c.dtls_id
				from inv_issue_master a,
					 inv_wvn_finish_fab_iss_dtls b ,
					 order_wise_pro_details c
				where
				 a.id = b.mst_id
				 and b.id = c.dtls_id
				 and c.entry_form=19
				 and  c.trans_type=2
				 $po_id_arr3";

		foreach (sql_select($sql_woven_recd) as $key => $val) {
			if (isset($contrast_wise_gmt[$val[csf("po_breakdown_id")]][$val[csf("color_id")]]))
				$woven_recd_arr[$val[csf("po_breakdown_id")]][$contrast_wise_gmt[$val[csf("po_breakdown_id")]][$val[csf("color_id")]]] += $val[csf("quantity")];
			else $woven_recd_arr[$val[csf("po_breakdown_id")]][$val[csf("color_id")]] += $val[csf("quantity")];
		}

		// echo"<pre>";
		// print_r($woven_recd_arr);

		$condition = new condition();
		$condition->po_id_in($today_all_po_id);
		$condition->init();
		$fabric = new fabric($condition);
		//  echo $fabric->getQuery(); die;
		$fabric_costing_arr = $fabric->getQtyArray_by_orderGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish();
		// echo"<pre>";
		// print_r($fabric_costing_arr);


		ob_start();
		?>

		<script type="text/javascript">
			setFilterGrid('table_body', -1);
		</script>
		<br> <br> <br>
		<div>
			<table width="3620" cellpadding="0" cellspacing="0">

				<tr>
					<td colspan="52" align="center"><b style="font-size: 21px;">Daily Production Progress Report </b></td>
				</tr>
				<tr>
					<td colspan="52" align="center"><b style="font-size: 21px;"><? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?></b> </td>
				</tr>
				<tr>
					<td colspan="52">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="52" align="left"><b style="font-size: 21px;">Date: <? echo str_replace("'", "",  $txt_production_date); ?> </b></td>
				</tr>
			</table>
			<table class="rpt_table" width="3620" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
				<thead>

					<tr>
						<th width="40" rowspan="2">
							<p>SI</p>
						</th>
						<th width="100" rowspan="2">
							<p>Buyer</p>
						</th>
						<th width="100" rowspan="2">
							<p>Job No</p>
						</th>
						<th width="100" rowspan="2">
							<p>Style</p>
						</th>
						<th width="100" rowspan="2">
							<p>Image/File</p>
						</th>
						<th width="100" rowspan="2">
							<p>PO NO</p>
						</th>
						<th width="100" rowspan="2">
							<p>Color</p>
						</th>
						<th width="120" rowspan="2">
							<p>Item</p>
						</th>
						<th width="100" rowspan="2">
							<p>Wash Status</p>
						</th>
						<th width="80" rowspan="2">
							<p>Order Qty.</p>
						</th>
						<th width="80" rowspan="2">
							<p>Plan Cut Qty.</p>
						</th>
						<th width="180" colspan="3">
							<p>Cutting Fabric Status (Yds)</p>
						</th>
						<th width="100">
							<p>&nbsp;</p>
						</th>
						<th width="180" colspan="3">
							<p>Cutting</p>
						</th>
						<th width="180" colspan="3">
							<p>Print</p>
						</th>
						<th width="180" colspan="3">
							<p>EMB</p>
						</th>
						<th width="180" colspan="3">
							<p>Special Works</p>
						</th>

						<th width="80" rowspan="2">
							<p>Line</p>
						</th>
						<th width="180" colspan="3">
							<p>Sewing Input</p>
						</th>
						<th width="180" colspan="3">
							<p>Sewing Output</p>
						</th>
						<th width="180" colspan="3">
							<p>Wash Send</p>
						</th>

						<th width="180" colspan="3">
							<p>Wash Rcv</p>
						</th>
						<th width="180" colspan="3">
							<p>Finishing Input</p>
						</th>
						<th width="180" colspan="3">
							<p>Poly</p>
						</th>
						<th width="180" colspan="3">
							<p>Packing & Finishing</p>
						</th>
						<th width="80" rowspan="2">
							<p>Ex-Factory</p>
						</th>
						<th width="80" rowspan="2">
							<p>Excess/Short</p>
						</th>

						<th width="80" rowspan="2">
							<p>Order To Ship Short%</p>
						</th>
						<th width="80" rowspan="2">
							<p>Cut to Ship Short%</p>
						</th>
						<th width="150" rowspan="2">
							<p>Shipping <br> Status</p>
						</th>

					</tr>

					<tr>
						<th width="60">
							<p>Req</p>
						</th>
						<th width="60">
							<p>Rcvd</p>
						</th>
						<th width="60">
							<p>Fab Bal</p>
						</th>
						<th width="10">
							<p>Shipdate</p>
						</th>
						<th width="60">
							<p>Day</p>
						</th>
						<th width="60">
							<p>TTL Cut</p>
						</th>
						<th width="60" title=" Cutting = ( Total Cutting - Order Qty ) ">
							<p>Cut Bal</p>
						</th>
						<th width="60">
							<p>Sent</p>
						</th>
						<th width="60">
							<p>Rcvd</p>
						</th>
						<th width="60" title=" Print = (Total Print Receive - Total Print Issue) ">
							<p>WIP</p>
						</th>
						<th width="60">
							<p>Sent</p>
						</th>
						<th width="60">
							<p>Rcvd</p>
						</th>
						<th width="60" title="EMB = (Total EMB Receive - Total EMB Send)">
							<p>WIP</p>
						</th>

						<th width="60">
							<p>Sent</p>
						</th>
						<th width="60">
							<p>Rcvd</p>
						</th>
						<th width="60" title="Special Works= (Total Special Work Recv - Total Special Work Sent)">
							<p>WIP</p>
						</th>

						<th width="60">
							<p>Day</p>
						</th>
						<th width="60">
							<p>Input</p>
						</th>
						<th width="60" title="Sewing Input = (Total Sewing input - Total Cutting)">
							<p>WIP</p>
						</th>
						<th width="60">
							<p>Day</p>
						</th>
						<th width="60">
							<p>Output</p>
						</th>
						<th width="60" title="Sewing Output =(Total Sewing Out - Total Sewing Input)">
							<p>WIP</p>
						</th>
						<th width="60">
							<p>Day</p>
						</th>
						<th width="60">
							<p>Total</p>
						</th>
						<th width="60" title=" Wash Sent= (Total Wash Sent - Total Sewing Output )If Non-wash then (Wash Sent=0)">
							<p>WIP</p>
						</th>
						<th width="60">
							<p>Day</p>
						</th>
						<th width="60">
							<p>Total</p>
						</th>
						<th width="60" title=" Wash Receive= (Total Wash Receive - Total Wash Sent)  ">
							<p>WIP</p>
						</th>
						<th width="60">
							<p>Day</p>
						</th>
						<th width="60">
							<p>Total</p>
						</th>
						<th width="60" title="Finishing Input=(Total Finishing Input - Total Wash Receive)  If Non-wash then Finishing Input=(Total Finishing Input - Total Sewing Output) ">
							<p>WIP</p>
						</th>

						<th width="60">
							<p>Day</p>
						</th>
						<th width="60">
							<p>Total</p>
						</th>
						<th width="60" title="Poly=(Total Poly Entry - Total Finishing Input)">
							<p>WIP</p>
						</th>
						<th width="60">
							<p>Day</p>
						</th>
						<th width="60">
							<p>Total</p>
						</th>
						<th width="60" title="Packaging & Finishing= (Total Packaging & Finishing - Total Poly)">
							<p>WIP</p>
						</th>



					</tr>


				</thead>
			</table>
			<div style="width:3640px;max-height:400px;overflow-y:scroll;float: left; " id="scroll_body">
				<table class="rpt_table" width="3620" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
					<tbody>
						<?
						$m = 1;
						$jj = 0;
						$gr_wise_buyer_ins_today = 0;
						$gr_wise_buyer_ins_total = 0;
						$gr_wise_buyer_ins_wip = 0;
						$gr_wise_carton_today = 0;
						$gr_wise_carton_total = 0;
						$grand_total_finsish_req = 0;
						$grand_total_finsish_recd = 0;
						$grand_total_finsish_bal = 0;
						$grand_ex_fac_excess = 0;
						$grand_ex_fac_short = 0;
						$grand_ex_fac_short_per =0 ;
						$gr_cut_per=0;
						$grand_excess_quantity=0;
						$grand_sewing_wip = 0;
						$grand_cutting_delivery_wip = 0;

						$grand_poly_today = 0;
						$grand_poly_total = 0;
						$grand_poly_wip = 0;

						$grand_finish_today = 0;
						$grand_finish_total = 0;
						$grand_finish_wip = 0;

						// echo "<pre>"; print_r($data_array); die;
						foreach ($data_array as $job_id => $job_data)
						{
							$pp = 0;
							$style_wise_finsish_req = 0;

							$style_wise_finsish_recd = 0;
							$style_wise_finsish_bal = 0;
							$style_ex_fac_excess = 0;
							$style_ex_fac_short = 0;
							$style_ex_fac_short_per = 0;
							$style_cut_per=0;
							$style_excesss_quantity=0;
							$style_sewing_wip = 0;
							$style_poly_wip = 0;
							$style_finishing_entry_wip = 0;
							$style_cutting_delivery_wip = 0;
							foreach ($job_data as $item_id => $item_data)
							{

								foreach ($item_data as $po_id => $po_data)
								{
									$l = 0;
									$nn = 0;
									foreach ($po_data as $shipment_date => $ship_date_data)
									{
										foreach ($ship_date_data as  $color_id => $color_data)
										{
											if ($m % 2 == 0)
												$bgcolor = "#E9F3FF";
											else
												$bgcolor = "#FFFFFF";
											$jj++;

											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $jj; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $jj; ?>">

												<?
												if ($pp == 0) {
												?>
													<td width="40" valign="middle" align="center"  rowspan="<? echo $style_wise_buyer_span[$job_id]; ?>">
														<p><? echo $m;
															$m++; ?></p>
													</td>
													<td valign='middle'  align="center" width="100" rowspan="<? echo $style_wise_buyer_span[$job_id]; ?>">
														<p><? echo $buyer_arr[$style_wise_buyer_arr[$job_id]]; ?></p>
													</td>
													<td  valign='middle' align="center"  width="100" rowspan="<? echo $style_wise_buyer_span[$job_id]; ?>">
														<p><? echo $style_wise_job_arr[$job_id] ; ?></p>
													</td>
													<td valign='middle' align="center" width="100"  rowspan="<? echo $style_wise_buyer_span[$job_id]; ?>">
														<p><? echo $style_wise_arr[$job_id] ?></p>
													</td>
													<td valign='middle' align="center" width="100"  rowspan="<? echo $style_wise_buyer_span[$job_id]; ?>">
														<?
															$style_wise_job = $style_wise_job_arr[$job_id];
															if(($system_file_arr[$style_wise_job])!='')
															{
																?>
																	<input type="button" class="image_uploader" id="system_id" style="width:80px" value="IMAGE FRONT" onClick="openmypage_file('requires/daily_production_progress_report_controller.php?action=file_show&mst_id=<?=$style_wise_job_arr[$job_id];?>&type=1&file_type=1?>','File View'),1"/>
																<?
															}
															if(($system_file_arr2[$style_wise_job])!='')
															{
																?>
																	<input type="button" class="image_uploader" id="system_id" style="width:80px" value="IMAGE BACK" onClick="openmypage_file('requires/daily_production_progress_report_controller.php?action=file_show&mst_id=<?=$style_wise_job_arr[$job_id];?>&type=2&file_type=1?>','File View'),1"/>
																<?
															}
															if(($system_file_arr3[$style_wise_job])!='')
															{
																?>
																	<input type="button" class="image_uploader" id="system_id" style="width:80px" value="FILE" onClick="openmypage_file('requires/daily_production_progress_report_controller.php?action=file_show&mst_id=<?=$style_wise_job_arr[$job_id];?>&type=3&file_type=2?>','File View'),1"/>
																<?
															}
														?>
													</td>
												<?
												}
												if ($l == 0) {

												?>
													<td valign='middle'align="center"    width="100" rowspan="<? echo $style_wise_span_array[$job_id][$item_id][$po_id]; ?>">
														<p><? echo $po_lib_arr[$po_id]; ?></p>
													</td>
												<?

												}
												?>
												<td width="100"  align="center" >
													<p><? echo $color_lib[$color_id]; ?></p>
												</td>
												<?
												if($finish_wip_arr[$po_id][$item_id][$color_id]==3)
												{
												$wash_status="Yes";
												}else{
												$wash_status="NO";
												}
												if ($l == 0) {
												?>
													<td valign='middle' align="center" width="120" style='word-break:break-all;word-wrap: break-word;' rowspan="<? echo $style_wise_span_array[$job_id][$item_id][$po_id]; ?>">
														<p><? echo $garments_item[$item_id]; ?></p>
													</td>

													<td valign='middle' align="center" width="100" style='word-break:break-all;word-wrap: break-word;' rowspan="<? echo $style_wise_span_array[$job_id][$item_id][$po_id]; ?>">
													<p><? echo $wash_status ; ?></p>
												</td>
												<?

												}
												$fab_bal = ($woven_recd_arr[$po_id][$color_id]) * 1 - (array_sum($fabric_costing_arr['woven']['grey'][$po_id][$item_id][$color_id])) * 1;
												$sewing_in_wip = $color_data["total_sew_input"] - $color_data["total_cutting"];
												$cutting_bal = $color_data["total_cutting"] -  $plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"];
												$print_bal = ($color_data["total_print_receive"] - $color_data["total_print_issue"]);

												$emb_bal = ($color_data["total_emb_receive"] - $color_data["total_emb_issue"]);

												$special_work_bal = ($color_data["total_special_work_rcv"] - $color_data["total_special_work_issue"]);

												$swout_wip = $color_data["total_sew_out"] - $color_data["total_sew_input"];
												$swout_wip_color = ($swout_wip < 0) ? " color:red; " : "color:black; ";
												$wash_bal = ($color_data["total_wash_issue"] - $color_data["total_sew_out"]);
												if($finish_wip_arr[$po_id][$item_id][$color_id]==3)
												{
												$wash_bal=($color_data['total_wash_issue']- $color_data['total_sew_out']);
												}else{
												$wash_bal=0;
												}

												$wash_rcv_bal = ($color_data["total_wash_receive"] - $color_data["total_wash_issue"]);
												$wash_bal_color = ($wash_bal < 0) ? " color:red; " : "color:black; ";
												$wash_rcv_bal_color = ($wash_rcv_bal < 0) ? " color:red; " : "color:black; ";


												$iron_bal = ($color_data["total_wash_receive"]) ? $color_data["total_iron"] - $color_data["total_wash_receive"] : $color_data["total_iron"] - $color_data["total_sew_out"];
												$iron_bal_color = ($iron_bal < 0) ? " color:red; " : "color:black; ";


												if($finish_wip_arr[$po_id][$item_id][$color_id]==3)
												{
												$finishing_rec_bal=($color_data["total_finishing_entry"] - $color_data['total_wash_receive']);
												}else{
												$finishing_rec_bal=($color_data["total_finishing_entry"] - $color_data["total_sew_out"]);
												}


												$poly_bal = ($color_data["total_poly"] - $color_data["total_finishing_entry"]);

												$poly_bal_color = ($poly_bal < 0) ? " color:red; " : "color:black; ";

												$inspection_bal = ($style_wise_ins_arr[$job_id][$po_id]["total_inspection"] - $style_wise_sub[$job_id]["total_finish"]);
												$inspection_bal_color = ($inspection_bal < 0) ? " color:red; " : "color:black; ";
												$finsh_bal = $color_data["total_finish"] - $color_data["total_poly"];




												$style_wise_finsish_req += array_sum($fabric_costing_arr['woven']['grey'][$po_id][$item_id][$color_id]);

												$style_wise_finsish_recd += $woven_recd_arr[$po_id][$color_id];
												$style_wise_finsish_bal += $fab_bal;

												$grand_total_finsish_req += array_sum($fabric_costing_arr['woven']['grey'][$po_id][$item_id][$color_id]);
												$grand_total_finsish_recd += $woven_recd_arr[$po_id][$color_id];
												$grand_total_finsish_bal += $fab_bal;
												$style_sewing_wip += $sewing_in_wip;
												$grand_sewing_wip += $sewing_in_wip;
												if (($color_data["total_print_receive"] * 1) > 0 || ($color_data["total_emb_receive"]) * 1 > 0) {
													if (($color_data["total_print_receive"] * 1) > 0 && ($color_data["total_emb_receive"]) * 1 <= 0) {
														$cutting_delivery_wip = ($color_data["total_cutting_delivery"] - $color_data["total_print_receive"]);
														$style_cutting_delivery_wip += $cutting_delivery_wip;
														$grand_cutting_delivery_wip += $cutting_delivery_wip;
														$cutting_delivery_bal_color = ($cutting_delivery_wip < 0) ? " color:red; " : "color:black; ";
													} else {
														$cutting_delivery_wip = ($color_data["total_cutting_delivery"] - $color_data["total_emb_receive"]);
														$style_cutting_delivery_wip += $cutting_delivery_wip;
														$grand_cutting_delivery_wip += $cutting_delivery_wip;
														$cutting_delivery_bal_color = ($cutting_delivery_wip < 0) ? " color:red; " : "color:black; ";
													}
												} else {
													$cutting_delivery_wip = ($color_data["total_cutting_delivery"] - $color_data["total_cutting"]);
													$style_cutting_delivery_wip += $cutting_delivery_wip;
													$grand_cutting_delivery_wip += $cutting_delivery_wip;
													$cutting_delivery_bal_color = ($cutting_delivery_wip < 0) ? " color:red; " : "color:black; ";
												}




												?>


												<td width="80" align="center" >
													<p><? echo   $plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"]; ?></p>
												</td>
												<td width="80" align="center" >
													<p><? echo  $plan_cut_arr[$po_id][$item_id][$color_id]["plan_cut"]; ?></p>
												</td>
												<td width="60" id="defaidult_zero1" class="default_zero" align="center" >
													<p><?
														$fab_req = array_sum($fabric_costing_arr['woven']['grey'][$po_id][$item_id][$color_id]);
														if ($fab_req == "") echo 0;
														else  echo number_format($fab_req, 2); ?></p>
												</td>
												<td width="60" id="default_zero2" class="default_zero" align="center" >
													<p><? if ($woven_recd_arr[$po_id][$color_id] == "") echo 0;
														else  echo number_format($woven_recd_arr[$po_id][$color_id], 2); ?></p>
												</td>
												<td width="60" title=" fab_bal= (Rcvd * 1) - ( Req * 1) " id="default_zero3" class="default_zero" align="center"  <? echo $fab_bal_color; ?>>
													<p><? echo number_format($fab_bal, 2); ?></p>
												</td>
												<td width="100" align="center" >
													<p><? echo change_date_format($shipment_date); ?></p>&nbsp;
												</td>
												<td width="60" align="center" >
													<p><? echo $color_data["today_cutting"]; ?></p>
												</td>
												<td width="60" align="center" >
													<p><? echo $color_data["total_cutting"]; ?></p>
												</td>
												<td width="60" align="center"  <? echo $cutting_bal_color; ?>>
													<p><? echo $cutting_bal; ?></p>
												</td>
												<td width="60" align="center" >
													<p><? echo $color_data["total_print_issue"]; ?></p>
												</td>
												<td width="60" align="center" >
													<p><? echo $color_data["total_print_receive"]; ?></p>
												</td>
												<td width="60" align="center"  <? echo $print_bal_color; ?>>
													<p> <? echo $print_bal; ?></p>
												</td>
												<td width="60" align="center" >
													<p><? echo $color_data["total_emb_issue"]; ?></p>
												</td>
												<td width="60" align="center" >
													<p><? echo $color_data["total_emb_receive"]; ?></p>
												</td>
												<td width="60" align="center"  <? echo $emb_bal_color; ?>>
													<p><? echo $emb_bal;  ?></p>
												</td>

												<td width="60" align="center" >
													<p><? echo $color_data["total_special_work_issue"]; ?></p>
												</td>
												<td width="60" align="center" >
													<p><? echo $color_data["total_special_work_rcv"]; ?></p>
												</td>
												<td width="60" align="center"  <? echo $special_work_bal_color; ?>>
													<p><? echo $special_work_bal;  ?></p>
												</td>


												<?
												if ($l == 0) {
												?>
													<td valign='middle' align="center"  width="80" rowspan="<? echo $style_wise_span_array[$job_id][$item_id][$po_id]; ?>">
														<p>
															<?

															$line = array_filter(array_unique(explode(",", $style_po_wise_line[$job_id][$po_id])));

															$line_name = "";
															foreach ($line as $v) {
																if ($prod_reso_allo == 1) {
																	$line_name .= $line_lib_resource[$v] . ",";
																} else {
																	$line_name .= $line_lib[$v] . ",";
																}
															}
															echo trim($line_name, ","); ?></p>
													</td>

												<?

												}
												?>

												<td width="60" align="center" >
													<p><? echo $color_data["today_sew_input"]; ?></p>
												</td>
												<td width="60" align="center" >
													<p><? echo $color_data["total_sew_input"]; ?></p>
												</td>
												<td width="60" align="center"  <? echo $swin_bal_color; ?>>
													<p><? echo  $sewing_in_wip  ?></p>
												</td>

												<td width="60" align="center" >
													<p><? echo $color_data["today_sew_output"]; ?></p>
												</td>
												<td width="60" align="center" >
													<p><? echo $color_data["total_sew_out"]; ?></p>
												</td>
												<td width="60" align="center"  <? echo $swout_wip_color; ?>>
													<p><? echo $swout_wip; ?></p>
												</td>

												<td width="60" align="center" >
													<p><? echo $color_data["today_wash_issue"]; ?></p>
												</td>

												<td width="60" align="center" >
													<p><? echo $color_data["total_wash_issue"]; ?></p>
												</td>
												<td width="60" align="center"  <? echo $wash_bal_color; ?>>
													<p><? echo  $wash_bal; ?></p>
												</td>

												<td width="60" align="center" >
													<p><? echo $color_data["today_wash_receive"]; ?></p>
												</td>
												<td width="60" align="center" >
													<p><? echo $color_data["total_wash_receive"]; ?></p>
												</td>
												<td width="60" align="center"  <? echo $wash_rcv_bal_color; ?>>
													<p><? echo  $wash_rcv_bal; ?></p>
												</td>

												<td width="60" align="center" >
													<p><? echo $today_finishing = $color_data["today_finishing_entry"]; ?></p>
												</td>
												<td width="60" align="center" >
													<p><? echo $total_finishing = $color_data["total_finishing_entry"]  ?></p>
												</td>
												<td width="60" align="center"  <? echo $finishing_rec_bal_color; ?>>
													<p><? echo $finishing_rec_bal; ?></p>
												</td>

												<td width="60" align="center" >
													<p><? echo $color_data["today_poly"]; ?></p>
												</td>
												<td width="60" align="center" >
													<p><? echo $color_data["total_poly"]; ?></p>
												</td>
												<td width="60" align="center"  <? echo $poly_bal_color; ?>>
													<p><? echo  $poly_bal; ?></p>
												</td>
												<?
												$grand_poly_today += $color_data["today_poly"];
												$grand_poly_total += $color_data["total_poly"];
												$grand_poly_wip += $poly_bal;
												$style_poly_wip += $poly_bal;
												$style_finishing_entry_wip += $finishing_rec_bal;

												$grand_finish_today += $color_data["today_finish"];
												$grand_finish_total += $color_data["total_finish"];
												$grand_finish_wip += $finishing_rec_bal;

												$grand_iron_today += $color_data["today_iron"];
												$grand_iron_total += $color_data["total_iron"];
												$grand_iron_wip += $iron_bal;

												$grand_finishing_entry_today += $color_data["today_finishing_entry"];
												$grand_finishing_entry_total += $color_data["total_finishing_entry"];
												$grand_finishing_rec_wip += $finishing_rec_bal;
												?>


												<td width="60" align="center" >
													<p><? echo $color_data["today_finish"]; ?></p>
												</td>
												<td width="60" align="center" >
													<p><? echo $color_data["total_finish"]; ?></p>
												</td>
												<td width="60" align="center"  <? echo $finsh_bal_color; ?>>
													<p>
														<? echo $finsh_bal; ?></p>
												</td>
												<?
												if ($pp == 0) {


												?>

												<?
												}

												if ($l == 0) {

												?>

												<?
													$po_item_style_buyer_ins[$job_id][$po_id] = 1;
												}
												$nn++;
												$pp++;
												?>

												<?
												if ($production_level != 1) {
												?>
													<td width="80" id="default_zero12" class="default_zero" align="center" >
														<p> <? if ($ex_fac_arr[$po_id][$item_id][$color_id] == "") echo 0;
															else echo $ex_fac_arr[$po_id][$item_id][$color_id];
															$excess_qnty = ($plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"]) - ($ex_fac_arr[$po_id][$item_id][$color_id]) * 1;

															if ($excess_qnty < 0) {
																$excess = $excess_qnty;
																$excess_color = " color:red;";
																$style_ex_fac_excess += $excess;
																$grand_ex_fac_excess += $excess;
																$short = 0;
															} else {
																$excess_color = "";
																$short = $excess_qnty;
																$excess = "";
																$style_ex_fac_short += $short;
																$style_ex_fac_short_per +=  ($short /$plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"])*100;
																$grand_ex_fac_short += $short;
																$grand_ex_fac_short_per += ($short /$plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"])*100;
															}

															?></p>
													</td>
													<td class="default_zero" id="default_zero13" width="80" align="center"  <? echo  $excess_color; ?>>
														<p><? if($ex_fac_arr[$po_id][$item_id][$color_id]>$plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"]){
															$excess_quantity=$plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"]-$ex_fac_arr[$po_id][$item_id][$color_id];
															echo $excess_quantity;
														  }
														  else{
															 $excess_quantity=$plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"]-$ex_fac_arr[$po_id][$item_id][$color_id];
														  }
														  echo -($excess_quantity);
														  $style_excesss_quantity+=$excess_quantity;
														  $grand_excess_quantity+=$excess_quantity;


														 ?></p>


													</td>

													<?
														$short_per = number_format (($plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"] -$ex_fac_arr[$po_id][$item_id][$color_id])/$plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"]*100,2)
													?>
													<td width="80" title="[{Order_Qty-Ex_Fac Qty}/Order_Qty]*100" align="center" style="<?= $short_per >1 ?'background:yellow':'' ?>;" rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>">
													<p><? echo $short_per; ?></p>
													</td>
													<?
														$cut_per = number_format (($color_data["total_cutting"] -$ex_fac_arr[$po_id][$item_id][$color_id])/$color_data["total_cutting"]*100,2);
													?>
													<td width="80"  title="[{Cut_Qty-Ex_Fac Qty}/Cut_Qty]*100" align="center" style="<?= $cut_per >1 ?'background:yellow':'' ?>;" rowspan="<? echo $style_wise_span_array[$style_id][$item_id][$po_id]; ?>">
														<p><? echo $cut_per; ?></p>
														<? $style_cut_per+=$cut_per;
														   $gr_cut_per+=$cut_per;

													     ?>
													</td>
													<?
												} else {
													if ($l == 0) {
														$excess_qnty = ($plan_cut_arr_gross[$po_id]["order_quantity"]) - ($ex_fac_arr[$po_id]) * 1;
														if ($excess_qnty < 0) {
															$excess = $excess_qnty;
															$excess_color = " color:red;";
															$style_ex_fac_excess += $excess;
															$grand_ex_fac_excess += $excess;
															$short = 0;
														} else {
															$excess_color = "";
															$short = $excess_qnty;
															$excess = "";
															$style_ex_fac_short += $short;
															$style_ex_fac_short_per +=  ($short /$plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"]);
															$grand_ex_fac_short += $short;
															// $grand_ex_fac_short_per += (($short /$plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"]))*100;
														}

													?>
														<td width="80" align="center"  rowspan="<? echo $style_wise_span_array[$job_id][$item_id][$po_id]; ?>">
															<p> <? echo $ex_fac_arr[$po_id]; ?>
															</p>
														</td>
														<td width="80" align="center"  rowspan="<? echo $style_wise_span_array[$job_id][$item_id][$po_id]; ?>" <? echo  $excess_color; ?>>
															<p><? echo  $excess; ?></p>
														</td>
														<td width="80" align="center"  rowspan="<? echo $style_wise_span_array[$job_id][$item_id][$po_id]; ?>">
															<p><? echo  $short; ?></p>
														</td>

												<?
													}
												}
												?>
												<td width="150" align="center" >
													<p>
														<?
														$ship_status = $color_data["shiping_status"];

														echo $shipment_status[$ship_status];

														?>
													</p>
												</td>
											</tr>


											<?

											$l++;
										}
									}
								}
							}
								$style_cutting_bal = $style_wise_sub[$job_id]["total_cutting"] - $style_wise_sub[trim($job_id)]["order_quantity"];
								$style_cutting_bal_color = ($style_cutting_bal < 0) ? " color:red; " : "color:black; ";
								$style_wise_finsish_bal_color = ($style_wise_finsish_bal < 0) ? " color:red; " : "color:black; ";
								$style_wise_print_bal_color = ($style_wise_sub[$job_id]["print_wip"] < 0) ? " color:red; " : "color:black; ";
								$style_wise_sewout_bal_color = ($style_wise_sub[$job_id]["sewout_wip"] < 0) ? " color:red; " : "color:black; ";
								$style_wise_emb_bal_color = ($style_wise_sub[$job_id]["emb_wip"] < 0) ? " color:red; " : "color:black; ";
								$style_wise_special_work_bal_color = ($style_wise_sub[$job_id]["special_work_wip"] < 0) ? " color:red; " : "color:black; ";
								$style_wise_cutting_delivery_bal_color = ($style_cutting_delivery_wip < 0) ? " color:red; " : "color:black; ";
								$style_wise_swin_bal_color = ($style_sewing_wip < 0) ? " color:red; " : "color:black; ";
								$style_wise_wash_bal_color = ($wash_bal < 0) ? " color:red; " : "color:black; ";
								$style_wise_wash_rcv_bal_color = ($style_wise_sub[$job_id]["wash_rcv_wip"] < 0) ? " color:red; " : "color:black; ";

								$style_wise_iron_bal_color = ($style_finishing_entry_wip + $style_poly_wip < 0) ? " color:red; " : "color:black; ";
								$style_wise_poly_bal_color = ($style_poly_wip < 0) ? " color:red; " : "color:black; ";


								?>
								<tr bgcolor="#EAEAEA" onClick="change_color('tr222_<? echo $jj; ?>', '<? echo $bgcolor; ?>')" id="tr222_<? echo $jj; ?>">
									<th colspan="9" align="center">
										<p> Style Total</p>
										</td>
									<th align="center" width="80">
										<p><? echo $style_wise_sub[trim($job_id)]["order_quantity"]; ?> </p>
										</td>
									<th align="center" width="80">
										<p><? echo $style_wise_sub[trim($job_id)]["plan_cut"]; ?> </p>
										</td>

									<th width="60" align="center">
									<p><? if ($style_wise_finsish_req == "") echo 0;
										else echo number_format($style_wise_finsish_req, 2); ?> </p>
									</th>
									<th width="60" align="center">
									<p><? if ($style_wise_finsish_recd == "") echo 0;
										else echo number_format($style_wise_finsish_recd, 2); ?> </p>
									</th>
									<th width="60" align="center" <? echo $style_wise_finsish_bal_color; ?>>
									<p><? echo number_format($style_wise_finsish_bal, 2); ?> </p>
									</th>
									<th>
										<p>&nbsp;</p>
									</th>
									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["today_cutting"]; ?> </th>
									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["total_cutting"]; ?> </th>
									<th width="60" align="center" <? echo $style_cutting_bal_color; ?>> <? echo $style_cutting_bal; ?> </th>

									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["total_print_issue"]; ?> </th>
									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["total_print_receive"]; ?> </th>
									<th width="60" align="center" <? echo $style_wise_print_bal_color; ?>> <? echo $style_wise_sub[$job_id]["print_wip"]; ?> </th>


									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["total_emb_issue"]; ?> </th>
									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["total_emb_receive"]; ?> </th>
									<th width="60" align="center" <? echo $style_wise_emb_bal_color; ?>> <? echo $style_wise_sub[$job_id]["emb_wip"]; ?> </th>

									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["total_special_work_issue"]; ?> </th>
									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["total_special_work_rcv"]; ?> </th>
									<th width="60" align="center" <? echo $style_wise_special_work_bal_color; ?>> <? echo $style_wise_sub[$job_id]["special_work_wip"]; ?> </th>

									<th width="80">&nbsp;</th>

									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["today_sew_input"]; ?> </th>
									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["total_sew_input"]; ?> </th>
									<th width="60" align="center" <? echo $style_wise_swin_bal_color; ?>> <? echo $style_sewing_wip; ?> </th>

									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["today_sew_output"]; ?> </th>
									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["total_sew_out"]; ?> </th>
									<th width="60" align="center" <? echo $style_wise_sewout_bal_color; ?>> <? echo $style_wise_sub[$job_id]["sewout_wip"]; ?> </th>

									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["today_wash_issue"]; ?> </th>
									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["total_wash_issue"]; ?> </th>
									<th width="60" align="center" <? echo $style_wise_wash_bal_color; ?>> <? echo $wash_bal; ?> </th>

									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["today_wash_receive"]; ?> </th>
									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["total_wash_receive"]; ?> </th>
									<th width="60" align="center" <? echo $style_wise_wash_rcv_bal_color; ?>> <? echo $style_wise_sub[$job_id]["wash_rcv_wip"]; ?> </th>

									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["today_finishing_entry"]; ?> </th>
									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["total_finishing_entry"]; ?> </th>
									<th width="60" align="center" <? echo $style_wise_iron_bal_color; ?>> <? echo $style_finishing_entry_wip; ?> </th>

									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["today_poly"]; ?> </th>
									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["total_poly"]; ?> </th>
									<th width="60" align="center" <? echo $style_wise_poly_bal_color; ?>> <? echo $style_poly_wip; ?> </th>

									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["today_finish"]; ?> </th>
									<th width="60" align="center"> <? echo $style_wise_sub[$job_id]["total_finish"]; ?> </th>
									<th width="60" align="center" <? echo $style_wise_finish_bal_color; ?>> <? echo $style_wise_sub[$job_id]["finish_wip"]; ?> </th>


									<th width="80" align="center"> <? if ($style_wise_sub[$job_id]["ex_fact"] == "") echo 0;
									else echo $style_wise_sub[$job_id]["ex_fact"]; ?> </th>
									<th width="80" align="center">
									<p> <? echo $style_excesss_quantity; ?> </p>
									</th>

									<th width="80" align="center">
									<p> <? echo number_format($style_ex_fac_short_per,2); ?> </p>
									</th>
									<th width="80" align="center">
									<p> <? echo $style_cut_per; ?> </p>
									</th>

									<th width="150">&nbsp;</th>

								</tr>

							<?
						}
						?>
					</tbody>
					<?

						$gr_order_qnty = 0;
						$gr_plan_qnty = 0;
						$gr_today_cutting = 0;
						$gr_total_cutting = 0;
						$gr_total_print_issue = 0;
						$gr_total_print_receive = 0;
						$gr_total_emb_issue = 0;

						$gr_total_special_work_issue = 0;
						$gr_total_special_work_receive = 0;
						$gr_special_work_wip = 0;

						$gr_print_wip = 0;
						$gr_emb_wip = 0;
						$gr_total_emb_receive = 0;
						$gr_today_cutting_delivery = 0;
						$gr_total_cutting_delivery = 0;
						$gr_today_sew_input = 0;
						$gr_total_sew_input = 0;
						$gr_sewin_wip = 0;
						$gr_today_sew_output = 0;
						$gr_total_sew_out = 0;
						$gr_sewout_wip = 0;
						$gr_total_wash_issue = 0;
						$gr_total_wash_receive = 0;
						$gr_wash_wip = 0;
						$gr_today_finish = 0;
						$gr_total_finish = 0;
						$gr_finish_wip = 0;
						$gr_ex_fact = 0;
						$gr_today_wash_receive = 0;

						foreach ($style_wise_sub as $key => $vals)
						{
							$gr_order_qnty += $vals["order_quantity"];
							$gr_plan_qnty += $vals["plan_cut"];
							$gr_today_cutting += $vals["today_cutting"];
							$gr_total_cutting += $vals["total_cutting"];
							$gr_total_print_issue += $vals["total_print_issue"];
							$gr_total_print_receive += $vals["total_print_receive"];
							$gr_print_wip += $vals["print_wip"];
							$gr_emb_wip += $vals["emb_wip"];
							$gr_total_emb_issue += $vals["total_emb_issue"];
							$gr_total_emb_receive += $vals["total_emb_receive"];

							$gr_total_special_work_issue += $vals["total_special_work_issue"];;
							$gr_total_special_work_receive += $vals["total_special_work_rcv"];;
							$gr_special_work_wip += $vals["special_work_wip"];;


							$gr_today_cutting_delivery += $vals["today_cutting_delivery"];
							$gr_total_cutting_delivery += $vals["total_cutting_delivery"];
							$gr_today_sew_input += $vals["today_sew_input"];
							$gr_total_sew_input += $vals["total_sew_input"];
							$gr_sewin_wip += $vals["sewin_wip"];
							$gr_today_sew_output += $vals["today_sew_output"];
							$gr_total_sew_out += $vals["total_sew_out"];
							$gr_sewout_wip += $vals["sewout_wip"];
							$gr_today_wash_issue += $vals["today_wash_issue"];
							$gr_total_wash_issue += $vals["total_wash_issue"];
							$gr_today_wash_receive += $vals["today_wash_receive"];
							$gr_total_wash_receive += $vals["total_wash_receive"];
							$gr_wash_wip += $wash_bal;
							$gr_rcv_wash_wip += $vals["wash_rcv_wip"];
							$gr_today_finish += $vals["today_finish"];
							$gr_total_finish += $vals["total_finish"];
							$gr_finish_wip += $vals["finish_wip"];
							$gr_ex_fact += $vals["ex_fact"];
							if($ship_status==3)
							{
								$total_short_per+=$short_per;
								$number_short_per=count($short_per);
								$grand_ex_per=($total_short_per/$number_short_per)*100;
							}
						}

						$gr_cutting_bal_color = ($gr_total_cutting - $gr_plan_qnty < 0) ? " color:red; " : "color:black; ";
						$gr_print_wip_color = ($gr_print_wip < 0) ? " color:red; " : "color:black; ";
						$gr_emb_wip_color = ($gr_emb_wip < 0) ? " color:red; " : "color:black; ";
						$gr_special_work_wip_color = ($gr_special_work_wip < 0) ? " color:red; " : "color:black; ";
						$grand_cutting_delivery_wip_color = ($grand_cutting_delivery_wip < 0) ? " color:red; " : "color:black; ";
						$grand_sewing_wip_color = ($grand_sewing_wip < 0) ? " color:red; " : "color:black; ";
						$gr_sewout_wip_color = ($gr_sewout_wip < 0) ? " color:red; " : "color:black; ";
						$gr_wash_wip_color = ($gr_wash_wip < 0) ? " color:red; " : "color:black; ";
						$gr_wash_rcv_wip_color = ($gr_rcv_wash_wip < 0) ? " color:red; " : "color:black; ";

						$grand_total_finsish_bal_color = ($grand_total_finsish_bal < 0) ? " color:red; " : "color:black; ";

						$gr_iron_wip_color = ($grand_iron_wip + $grand_poly_wip < 0) ? " color:red; " : "color:black; ";

						$gr_poly_wip_color = ($grand_poly_wip < 0) ? " color:red; " : "color:black; ";




						?>

						<tr bgcolor="#A4C2EA" onClick="change_color('tr333_<? echo $jj; ?>', '<? echo $bgcolor; ?>')" id="tr333_<? echo $jj; ?>">
							<th colspan="9" align="center">
								<p> Grand Total</p>
								</td>
							<th align="center" width="80">
								<p><? echo $gr_order_qnty; ?> </p>
								</td>
							<th align="center" width="80">
								<p><? echo $gr_plan_qnty; ?> </p>
								</td>
							<th width="60" align="center">
								<p> <? echo number_format($grand_total_finsish_req, 2); ?> </p>
							</th>
							<th width="60" align="center">
								<p> <? echo number_format($grand_total_finsish_recd, 2); ?> </p>
							</th>
							<th width="60" align="center" <? echo $grand_total_finsish_bal_color; ?>>
								<p> <? echo number_format($grand_total_finsish_bal, 2); ?> </p>
							</th>
							<th>&nbsp; </th>
							<th width="60" align="center"> <? echo $gr_today_cutting; ?> </th>
							<th width="60" align="center"> <? echo $gr_total_cutting; ?> </th>
							<th width="60" align="center" <? echo $gr_cutting_bal_color; ?>> <? echo  $gr_total_cutting - $gr_order_qnty; ?> </th>

							<th width="60" align="center"> <? echo $gr_total_print_issue; ?> </th>
							<th width="60" align="center"> <? echo $gr_total_print_receive; ?> </th>
							<th width="60" align="center" <? echo $gr_print_wip_color; ?>> <? echo $gr_print_wip; ?> </th>


							<th width="60" align="center"> <? echo $gr_total_emb_issue; ?> </th>
							<th width="60" align="center"> <? echo $gr_total_emb_receive; ?> </th>
							<th width="60" align="center" <? echo $gr_emb_wip_color; ?>> <? echo $gr_emb_wip; ?> </th>

							<th width="60" align="center"> <? echo $gr_total_special_work_issue; ?> </th>
							<th width="60" align="center"> <? echo $gr_total_special_work_receive; ?> </th>
							<th width="60" align="center" <? echo $gr_special_work_wip_color; ?>> <? echo $gr_special_work_wip; ?> </th>



							<th width="80">&nbsp;</th>
							<th width="60" align="center"> <? echo $gr_today_sew_input; ?> </th>
							<th width="60" align="center"> <? echo $gr_total_sew_input; ?> </th>
							<th width="60" align="center" <? echo $grand_sewing_wip_color; ?>> <? echo $grand_sewing_wip; ?> </th>


							<th width="60" align="center"> <? echo $gr_today_sew_output; ?> </th>
							<th width="60" align="center"> <? echo $gr_total_sew_out; ?> </th>
							<th width="60" align="center" <? echo $gr_sewout_wip_color; ?>> <? echo $gr_sewout_wip; ?> </th>


							<th width="60" align="center"> <? echo $gr_today_wash_issue; ?> </th>
							<th width="60" align="center"> <? echo $gr_total_wash_issue; ?> </th>
							<th width="60" align="center" <? echo $gr_wash_wip_color; ?>> <? echo $gr_wash_wip; ?> </th>

							<th width="60" align="center"> <? echo $gr_today_wash_receive; ?> </th>
							<th width="60" align="center"> <? echo $gr_total_wash_receive; ?> </th>
							<th width="60" align="center" <? echo $gr_wash_rcv_wip_color; ?>> <? echo $gr_rcv_wash_wip; ?> </th>

							<th width="60" align="center"> <? echo $grand_finishing_entry_today; ?> </th>
							<th width="60" align="center"> <? echo $grand_finishing_entry_total; ?> </th>
							<th width="60" align="center" <? echo $gr_iron_wip_color; ?>> <? echo $grand_finishing_rec_wip; ?> </th>

							<th width="60" align="center"> <? echo $grand_poly_today; ?> </th>
							<th width="60" align="center"> <? echo $grand_poly_total; ?> </th>
							<th width="60" align="center" <? echo $gr_poly_wip_color; ?>> <? echo $grand_poly_wip; ?> </th>

							<th width="60" align="center"> <? echo $grand_finish_today; ?> </th>
							<th width="60" align="center"> <? echo $grand_finish_total; ?> </th>
							<th width="60" align="center" <? echo $gr_finishing_rec_wip_color; ?>> <? echo $gr_finish_wip; ?> </th>

							<th width="80" align="center">
								<p> <? echo $gr_ex_fact; ?> </p>
							</th>
							<th width="80" align="center">
								<p> <? echo $grand_excess_quantity; ?> </p>
							</th>

							<th width="80" align="center">
								<p> <? echo number_format($grand_ex_per,2); ?> </p>
							</th>
							<th width="80" align="center">
								<p> <? echo number_format($gr_cut_per,2); ?> </p>
							</th>
							<th width="150">&nbsp;</th>

						</tr>

				</table>

			</div>



		<?
		foreach (glob("$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}
		$name = time();
		$filename = $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		echo "$total_data####$filename";
		exit();
	}
}


if ($action == "finish_fabric") {
	echo load_html_head_contents("Job Color Size", "../../../", 1, 1, $unicode);
	extract($_REQUEST);


	$insert_cond = "   and  d.production_date='$insert_date'";
	// if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	$sql_job = sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");
		?>
		<div id="data_panel" align="center" style="width:100%">
			<fieldset style="width:820px">
				<table width="800px" align="center" border="1" rules="all" class="rpt_table">
					<thead>
						<tr>
							<th width="200">Buyer Name</th>
							<th width="100">Job No </th>
							<th width="100">Style Reff.</th>
							<th width="100">Country</th>
							<th width="100">Order No</th>
							<th width="100">Ship Date</th>
							<th width="100">Order Qty</th>
						</tr>
					</thead>
					<tbody>
						<?

						foreach ($sql_job as $row) {
							// if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
								<td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
								<td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
								<td align="center">
									<p><? echo $country_arr[$row[csf('country_id')]]; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('po_number')]; ?></p>
								</td>
								<td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
								<td align="right"><? echo $row[csf('order_qty')];
													$total_qty += $row[csf('order_qty')]; ?></td>
							</tr>
						<?
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="6">Total</th>
							<th><? echo $total_qty; ?></th>
						</tr>
					</tfoot>
				</table>
			</fieldset>
			<br />
			<?

			$sql_fabric = "SELECT a.po_breakdown_id,a.color_id,
		sum(CASE WHEN b.transaction_date <= " . $txt_production_date . " AND a.trans_type =2  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
	    ELSE 0 END ) AS grey_fabric_issue,
		sum(CASE WHEN b.transaction_date <= " . $txt_production_date . " AND a.trans_type =4  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
	    ELSE 0 END ) AS grey_fabric_issue_return,
		sum(CASE WHEN b.transaction_date <= " . $txt_production_date . " AND a.trans_type =1  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
	    ELSE 0 END ) AS finish_fabric_rece,
		sum(CASE WHEN b.transaction_date <= " . $txt_production_date . " AND a.trans_type =2  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
	    ELSE 0 END ) AS finish_fabric_rece_return,
		sum(CASE WHEN b.transaction_date = " . $txt_production_date . " AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
	    ELSE 0 END ) AS fabric_qty,
		sum(CASE WHEN b.transaction_date <" . $txt_production_date . " AND a.trans_type =2 and b.item_category=2  AND a.entry_form =18 THEN a.quantity
		ELSE 0 END ) AS fabric_qty_pre,
		sum(CASE WHEN b.transaction_date = " . $txt_production_date . " AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
		sum(CASE WHEN b.transaction_date <" . $txt_production_date . " AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_pre,
		sum(CASE WHEN b.transaction_date = " . $txt_production_date . " AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty,
		sum(CASE WHEN b.transaction_date <" . $txt_production_date . " AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_pre
		FROM order_wise_pro_details a,inv_transaction b
	    WHERE a.trans_id = b.id
		and b.status_active=1 and a.entry_form in(18,15,16,37) and a.quantity!=0 and  b.is_deleted=0 AND a.po_breakdown_id
		in (" . str_replace("'", "", $po_number_id) . ") group by a.po_breakdown_id,a.color_id";



			//echo $sql_cutting_delevery;
			$colorArr = return_library_array("select id,color_name from lib_color", "id", "color_name");
			$itemSizeArr = return_library_array("select id,size_name from  lib_size ", "id", "size_name");
			$job_size_array = array();
			$job_size_qnty_array = array();
			$job_color_array = array();
			$job_color_qnty_array = array();
			$job_color_size_qnty_array = array();
			$sql_data = sql_select($sql_cutting_delevery);
			$production_details_arr = array();
			$production_size_details_arr = array();
			foreach ($sql_data as $row) {
				$job_size_array[$order_number][$row[csf('size_number_id')]] = $row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('size_number_id')]] += $row[csf('production_qnty')];
				$job_color_array[$order_number][$row[csf('color_number_id')]] = $row[csf('color_number_id')];
				$job_color_qnty_array['color_total'] += $row[csf('production_qnty')];
				//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];

				$production_details_arr[$row[csf('id')]]['country'] = $row[csf('country_id')];
				$production_details_arr[$row[csf('id')]]['color'] = $row[csf('color_number_id')];
				$production_details_arr[$row[csf('id')]]['production_date'] = $row[csf('cut_delivery_date')];
				$production_details_arr[$row[csf('id')]]['challan_no'] = $row[csf('challan_no')];
				$production_details_arr[$row[csf('id')]]['product_qty'] += $row[csf('production_qnty')];
				//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
				$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty'] += $row[csf('production_qnty')];
			}
			// print_r($production_size_details_arr);die;
			$job_color_tot = 0;
			?>
			<div id="data_panel" align="center" style="width:100%">
				<fieldset style="width:820px">
					<label> <strong>Po Number: <? echo $order_number; ?><strong><label />
								<table width="" align="center" border="1" rules="all" class="rpt_table">
									<thead>
										<tr>
											<th width="180">ID</th>
											<th width="70">Date</th>
											<th width="70">Fabric Qty.</th>
										</tr>
									</thead>
									<?
									$i = 1;
									foreach ($production_details_arr as $key_c => $value_c) {
										if ($i % 2 == 0) $bgcolor = "#E9F3FF";
										else $bgcolor = "#FFFFFF";
										//if($value_c != "")
										//{
									?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
											<td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
											<td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
											<td align="right"><? echo  $value_c['challan_no']; ?></td>
											<?
											foreach ($job_size_array[$order_number] as $key_s => $value_s) {

											?>
												<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$key_s]['product_qty']; ?></td>
											<?

											}
											?>
											<td align="right"><? echo  $value_c['product_qty'];
																$job_color_tot += $job_color_qnty_array[$value_po][$value_c]; ?></td>

										</tr>
									<?
										$i++;
										//}
									}
									?>
									<tfoot>
										<tr bgcolor="<? // echo $bgcolor;
														?>">
											<th></th>
											<th></th>
											<th></th>
											<th>Total</th>

											<?
											foreach ($job_size_array[$order_number] as $key_s => $value_s) {
												if ($value_s != "") {
											?>
													<th width="60" align="right"><? echo $job_size_qnty_array[$key_s]; ?></th>
											<?
												}
											}
											?>
											<th align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
										</tr>
									</tfoot>
								</table>
								<br />
				</fieldset>
			</div>
		<?
	}


	if ($action == "cutting_delivery_popup") {
		echo load_html_head_contents("Job Color Size", "../../../", 1, 1, $unicode);
		extract($_REQUEST);
		$insert_cond = "   and  d.production_date='$insert_date'";
		// if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
		$sql_job = sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");
		?>
			<div id="data_panel" align="center" style="width:100%">
				<fieldset style="width:820px">
					<table width="800px" align="center" border="1" rules="all" class="rpt_table">
						<thead>
							<tr>
								<th width="200">Buyer Name</th>
								<th width="100">Job No </th>
								<th width="100">Style Reff.</th>
								<th width="100">Country</th>
								<th width="100">Order No</th>
								<th width="100">Ship Date</th>
								<th width="100">Order Qty</th>
							</tr>
						</thead>
						<tbody>
							<?

							foreach ($sql_job as $row) {
								// if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
									<td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
									<td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
									<td align="center">
										<p><? echo $country_arr[$row[csf('country_id')]]; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('po_number')]; ?></p>
									</td>
									<td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
									<td align="right"><? echo $row[csf('order_qty')];
														$total_qty += $row[csf('order_qty')]; ?></td>
								</tr>
							<?
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="6">Total</th>
								<th><? echo $total_qty; ?></th>
							</tr>
						</tfoot>
					</table>
				</fieldset>
				<br />
				<?
				$sql_cutting_delevery = "select a.id,a.cut_delivery_date,a.challan_no ,b.production_qnty,c.size_number_id,c.color_number_id,c.country_id
		from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c
	    where a.id=b.mst_id
	    and b.color_size_break_down_id=c.id
		and a.po_break_down_id=c.po_break_down_id
		and a.po_break_down_id=$order_id
		and c.color_number_id=$color_id
		and a.status_active=1 and a.is_deleted=0
		and  b.status_active=1  and b.is_deleted=0
		  ";
				//echo $sql_cutting_delevery;
				$colorArr = return_library_array("select id,color_name from lib_color", "id", "color_name");
				$itemSizeArr = return_library_array("select id,size_name from  lib_size ", "id", "size_name");
				$job_size_array = array();
				$job_size_qnty_array = array();
				$job_color_array = array();
				$job_color_qnty_array = array();
				$job_color_size_qnty_array = array();
				$sql_data = sql_select($sql_cutting_delevery);
				$production_details_arr = array();
				$production_size_details_arr = array();
				foreach ($sql_data as $row) {
					$job_size_array[$order_number][$row[csf('size_number_id')]] = $row[csf('size_number_id')];
					$job_size_qnty_array[$row[csf('size_number_id')]] += $row[csf('production_qnty')];
					$job_color_array[$order_number][$row[csf('color_number_id')]] = $row[csf('color_number_id')];
					$job_color_qnty_array['color_total'] += $row[csf('production_qnty')];
					//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];

					$production_details_arr[$row[csf('id')]]['country'] = $row[csf('country_id')];
					$production_details_arr[$row[csf('id')]]['color'] = $row[csf('color_number_id')];
					$production_details_arr[$row[csf('id')]]['production_date'] = $row[csf('cut_delivery_date')];
					$production_details_arr[$row[csf('id')]]['challan_no'] = $row[csf('challan_no')];
					$production_details_arr[$row[csf('id')]]['product_qty'] += $row[csf('production_qnty')];
					//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
					$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty'] += $row[csf('production_qnty')];
				}
				// print_r($production_size_details_arr);die;
				$job_color_tot = 0;
				?>
				<div id="data_panel" align="center" style="width:100%">
					<fieldset style="width:820px">
						<label> <strong>Po Number: <? echo $order_number; ?><strong><label />
									<table width="" align="center" border="1" rules="all" class="rpt_table">
										<thead>
											<tr>
												<th width="180">Color</th>
												<th width="70">Country</th>
												<th width="70">Date</th>
												<th width="70">Challan</th>
												<?
												foreach ($job_size_array[$order_number] as $key => $value) {
													if ($value != "") {
												?>
														<th width="60"><? echo $itemSizeArr[$value]; ?></th>
												<?
													}
												}
												?>
												<th width="70">Color Total</th>
											</tr>
										</thead>
										<?
										$i = 1;
										foreach ($production_details_arr as $key_c => $value_c) {
											if ($i % 2 == 0) $bgcolor = "#E9F3FF";
											else $bgcolor = "#FFFFFF";
											//if($value_c != "")
											//{
										?>
											<tr bgcolor="<? echo $bgcolor; ?>">
												<td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
												<td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
												<td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
												<td align="right"><? echo  $value_c['challan_no']; ?></td>
												<?
												foreach ($job_size_array[$order_number] as $key_s => $value_s) {

												?>
													<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$key_s]['product_qty']; ?></td>
												<?

												}
												?>
												<td align="right"><? echo  $value_c['product_qty'];
																	$job_color_tot += $job_color_qnty_array[$value_po][$value_c]; ?></td>

											</tr>
										<?
											$i++;
											//}
										}
										?>
										<tfoot>
											<tr bgcolor="<? // echo $bgcolor;
															?>">
												<th></th>
												<th></th>
												<th></th>
												<th>Total</th>

												<?
												foreach ($job_size_array[$order_number] as $key_s => $value_s) {
													if ($value_s != "") {
												?>
														<th width="60" align="right"><? echo $job_size_qnty_array[$key_s]; ?></th>
												<?
													}
												}
												?>
												<th align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
											</tr>
										</tfoot>
									</table>
									<br />
					</fieldset>
				</div>
			<?
		}

		if ($action == "cutting_and_sewing_remarks") {
			extract($_REQUEST);
			echo load_html_head_contents("Remarks", "../../../", 1, 1, $unicode, '', '');
			$insert_cond = "   and  production_date='$insert_date'";
			?>
				<div align="center">
					<fieldset style="width:480px">
						<legend>Cutting</legend>
						<?
						$sql = "SELECT  d.id,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=1  and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
         e.status_active =1   $insert_cond group by d.id,f.color_number_id,d.remarks,d.production_date order by d.id";
						//echo $sql;
						echo  create_list_view("list_view_1", "ID,Date,Production Qnty,Remarks", "80,70,70,280", "600", "220", 1, $sql, "", "", "", 1, '0,0,0,0', $arr, "id,production_date,product_qty,remarks", "../requires/daily_production_progress_report_controller", '', '0,3,1,0', '0,0,0,product_qty,0');
						?>
					</fieldset>
					<br />
					<fieldset style="width:480px">
						<legend>Cutting Delivery to Input</legend>
						<?
						$sql_cutting_delevery = "select a.id,a.cut_delivery_date ,a.remarks,
		sum(b.production_qnty) AS cut_delivery_qnty
		from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c
	    where a.id=b.mst_id
	    and b.color_size_break_down_id=c.id
		and a.po_break_down_id=c.po_break_down_id
		and a.po_break_down_id=$order_id
		and c.color_number_id=$color_id
	    group by a.id,a.cut_delivery_date ,a.remarks";
						// echo $sql_cutting_delevery;
						echo  create_list_view("list_view_1", "ID,Date,Production Qnty,Remarks", "80,70,70,280", "600", "220", 1, $sql_cutting_delevery, "", "", "", 1, '0,0,0,0', $arr, "id,cut_delivery_date,cut_delivery_qnty,remarks", "../requires/daily_production_progress_report_controller", '', '0,3,1,0', '0,0,0,cut_delivery_qnty,0');

						?>
					</fieldset>
					<br />
					<fieldset style="width:480px">
						<legend>Print/Embr Issue</legend>
						<?
						$sql = "SELECT  d.id,d.embel_name,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=2 and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1   $insert_cond group by d.id,d.embel_name,f.color_number_id,d.remarks,d.production_date order by d.embel_name";
						$arr = array(1 => $emblishment_name_array);
						echo  create_list_view("list_view_1", "ID,Embel. Name,Date,Production Qnty,Remarks", "80,100,70,70,180", "600", "220", 1, $sql, "", "", "", 1, '0,embel_name,0,0,0', $arr, "id,embel_name,production_date,product_qty,remarks", "../requires/daily_production_progress_report_controller", '', '0,0,3,1,0', '0,0,0,0,product_qty,0');
						?>
					</fieldset>
					<br />
					<fieldset style="width:480px">
						<legend>Print/Embr Receive</legend>
						<?
						$sql = "SELECT  d.id,d.embel_name,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=3 and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1   $insert_cond group by d.id,d.embel_name,f.color_number_id,d.remarks,d.production_date order by d.embel_name";
						$arr = array(1 => $emblishment_name_array);
						echo  create_list_view("list_view_1", "ID,Embel. Name,Date,Production Qnty,Remarks", "80,100,70,70,180", "600", "220", 1, $sql, "", "", "", 1, '0,embel_name,0,0,0', $arr, "id,embel_name,production_date,product_qty,remarks", "../requires/daily_production_progress_report_controller", '', '0,0,3,1,0', '0,0,0,0,product_qty,0');
						?>
					</fieldset>
					<br />

					<fieldset style="width:480px">
						<legend>Sewing Input</legend>
						<?
						$sql = "SELECT  d.id,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=4  and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
         e.status_active =1   $insert_cond group by d.id,f.color_number_id,d.remarks,d.production_date order by d.id";
						//echo $sql;
						echo  create_list_view("list_view_1", "ID,Date,Production Qnty,Remarks", "80,70,70,280", "600", "220", 1, $sql, "", "", "", 1, '0,0,0,0', $arr, "id,production_date,product_qty,remarks", "../requires/daily_production_progress_report_controller", '', '0,3,1,0', '0,0,0,product_qty,0');
						?>
					</fieldset>
				</div>
			<?
			exit();
		}
		if ($action == "emblishment_popup") {
			echo load_html_head_contents("Job Color Size", "../../../", 1, 1, $unicode);
			extract($_REQUEST);


			$insert_cond = "   and  d.production_date='$insert_date'";
			// if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
			$sql_job = sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");
			?>
				<div id="data_panel" align="center" style="width:100%">
					<fieldset style="width:820px">
						<table width="800px" align="center" border="1" rules="all" class="rpt_table">
							<thead>
								<tr>
									<th width="200">Buyer Name</th>
									<th width="100">Job No </th>
									<th width="100">Style Reff.</th>
									<th width="100">Country</th>
									<th width="100">Order No</th>
									<th width="100">Ship Date</th>
									<th width="100">Order Qty</th>
								</tr>
							</thead>
							<tbody>
								<?

								foreach ($sql_job as $row) {
									// if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
									<tr bgcolor="<? echo $bgcolor; ?>">
										<td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
										<td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
										<td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
										<td align="center">
											<p><? echo $country_arr[$row[csf('country_id')]]; ?></p>
										</td>
										<td align="center">
											<p><? echo $row[csf('po_number')]; ?></p>
										</td>
										<td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
										<td align="right"><? echo $row[csf('order_qty')];
															$total_qty += $row[csf('order_qty')]; ?></td>
									</tr>
								<?
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="6">Total</th>
									<th><? echo $total_qty; ?></th>
								</tr>
							</tfoot>
						</table>
					</fieldset>
					<br />
					<?
					$sql = "SELECT  d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,d.challan_no,d.production_date,f.country_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=$type  and
		    f.color_number_id=$color_id and
			d.embel_name=$embl_type  and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond";
					//echo $sql;
					$colorArr = return_library_array("select id,color_name from lib_color", "id", "color_name");
					$itemSizeArr = return_library_array("select id,size_name from  lib_size ", "id", "size_name");
					$job_size_array = array();
					$job_size_qnty_array = array();
					$job_color_array = array();
					$job_color_qnty_array = array();
					$job_color_size_qnty_array = array();
					$sql_data = sql_select($sql);
					$production_details_arr = array();
					$production_size_details_arr = array();
					foreach ($sql_data as $row) {
						$job_size_array[$order_number][$row[csf('size_number_id')]] = $row[csf('size_number_id')];
						$job_size_qnty_array[$row[csf('size_number_id')]] += $row[csf('product_qty')];
						$job_color_array[$order_number][$row[csf('color_number_id')]] = $row[csf('color_number_id')];
						$job_color_qnty_array['color_total'] += $row[csf('product_qty')];
						//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];

						$production_details_arr[$row[csf('id')]]['country'] = $row[csf('country_id')];
						$production_details_arr[$row[csf('id')]]['color'] = $row[csf('color_number_id')];
						$production_details_arr[$row[csf('id')]]['production_date'] = $row[csf('production_date')];
						$production_details_arr[$row[csf('id')]]['challan_no'] = $row[csf('challan_no')];
						$production_details_arr[$row[csf('id')]]['product_qty'] += $row[csf('product_qty')];
						//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
						$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty'] += $row[csf('product_qty')];
					}
					// print_r($production_size_details_arr);die;
					$job_color_tot = 0;
					?>
					<div id="data_panel" align="center" style="width:100%">
						<fieldset style="width:820px">
							<label> <strong>Po Number: <? echo $order_number; ?><strong><label />
										<table width="" align="center" border="1" rules="all" class="rpt_table">
											<thead>
												<tr>
													<th width="180">Color</th>
													<th width="70">Country</th>
													<th width="70">Date</th>
													<th width="70">Challan</th>
													<?
													foreach ($job_size_array[$order_number] as $key => $value) {
														if ($value != "") {
													?>
															<th width="60"><? echo $itemSizeArr[$value]; ?></th>
													<?
														}
													}
													?>
													<th width="70">Color Total</th>
												</tr>
											</thead>
											<?
											$i = 1;
											foreach ($production_details_arr as $key_c => $value_c) {
												if ($i % 2 == 0) $bgcolor = "#E9F3FF";
												else $bgcolor = "#FFFFFF";
												//if($value_c != "")
												//{
											?>
												<tr bgcolor="<? echo $bgcolor; ?>">
													<td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
													<td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
													<td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
													<td align="right"><? echo  $value_c['challan_no']; ?></td>
													<?
													foreach ($job_size_array[$order_number] as $key_s => $value_s) {

													?>
														<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$key_s]['product_qty']; ?></td>
													<?

													}
													?>
													<td align="right"><? echo  $value_c['product_qty'];
																		$job_color_tot += $job_color_qnty_array[$value_po][$value_c]; ?></td>

												</tr>
											<?
												$i++;
												//}
											}
											?>
											<tfoot>
												<tr bgcolor="<? // echo $bgcolor;
																?>">
													<th></th>
													<th></th>
													<th></th>
													<th>Total</th>

													<?
													foreach ($job_size_array[$order_number] as $key_s => $value_s) {
														if ($value_s != "") {
													?>
															<th width="60" align="right"><? echo $job_size_qnty_array[$key_s]; ?></th>
													<?
														}
													}
													?>
													<th align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
												</tr>
											</tfoot>
										</table>
										<br />
						</fieldset>
					</div>
				<?
			}
			if ($action == "cutting_and_sewing_popup") {
				echo load_html_head_contents("Job Color Size", "../../../", 1, 1, $unicode);
				extract($_REQUEST);
				if ($type == 1)  $insert_cond = "   and  d.production_date='$insert_date'";
				if ($type == 2)  $insert_cond = "   and  d.production_date<='$insert_date'";
				$sql_job = sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");
				?>
					<div id="data_panel" align="center" style="width:100%">
						<fieldset style="width:820px">
							<table width="800px" align="center" border="1" rules="all" class="rpt_table">
								<thead>
									<tr>
										<th width="200">Buyer Name</th>
										<th width="100">Job No </th>
										<th width="100">Style Reff.</th>
										<th width="100">Country</th>
										<th width="100">Order No</th>
										<th width="100">Ship Date</th>
										<th width="100">Order Qty</th>
									</tr>
								</thead>
								<tbody>
									<?

									foreach ($sql_job as $row) {
										// if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
											<td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
											<td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
											<td align="center">
												<p><? echo $country_arr[$row[csf('country_id')]]; ?></p>
											</td>
											<td align="center">
												<p><? echo $row[csf('po_number')]; ?></p>
											</td>
											<td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
											<td align="right"><? echo $row[csf('order_qty')];
																$total_qty += $row[csf('order_qty')]; ?></td>
										</tr>
									<?
									}
									?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="6">Total</th>
										<th><? echo $total_qty; ?></th>
									</tr>
								</tfoot>
							</table>
						</fieldset>
						<br />
						<?



						$sql = "SELECT  d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,d.challan_no,d.production_date,f.country_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=$type  and
			f.color_number_id=$color_id and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond";
						//echo $sql;
						$colorArr = return_library_array("select id,color_name from lib_color", "id", "color_name");
						$itemSizeArr = return_library_array("select id,size_name from  lib_size ", "id", "size_name");
						$job_size_array = array();
						$job_size_qnty_array = array();
						$job_color_array = array();
						$job_color_qnty_array = array();
						$job_color_size_qnty_array = array();
						$sql_data = sql_select($sql);
						$production_details_arr = array();
						$production_size_details_arr = array();
						foreach ($sql_data as $row) {
							$job_size_array[$order_number][$row[csf('size_number_id')]] = $row[csf('size_number_id')];
							$job_size_qnty_array[$row[csf('size_number_id')]] += $row[csf('product_qty')];
							$job_color_array[$order_number][$row[csf('color_number_id')]] = $row[csf('color_number_id')];
							$job_color_qnty_array['color_total'] += $row[csf('product_qty')];
							//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];

							$production_details_arr[$row[csf('id')]]['country'] = $row[csf('country_id')];
							$production_details_arr[$row[csf('id')]]['color'] = $row[csf('color_number_id')];
							$production_details_arr[$row[csf('id')]]['production_date'] = $row[csf('production_date')];
							$production_details_arr[$row[csf('id')]]['challan_no'] = $row[csf('challan_no')];
							$production_details_arr[$row[csf('id')]]['product_qty'] += $row[csf('product_qty')];
							//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
							$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty'] += $row[csf('product_qty')];
						}
						// print_r($production_size_details_arr);die;
						$job_color_tot = 0;
						?>
						<div id="data_panel" align="center" style="width:100%">
							<fieldset style="width:820px">
								<label> <strong>Po Number: <? echo $order_number; ?><strong><label />
											<table width="" align="center" border="1" rules="all" class="rpt_table">
												<thead>
													<tr>
														<th width="180">Color</th>
														<th width="70">Country</th>
														<th width="70">Date</th>
														<th width="70">Challan</th>
														<?
														foreach ($job_size_array[$order_number] as $key => $value) {
															if ($value != "") {
														?>
																<th width="60"><? echo $itemSizeArr[$value]; ?></th>
														<?
															}
														}
														?>
														<th width="70">Color Total</th>
													</tr>
												</thead>
												<?
												$i = 1;
												foreach ($production_details_arr as $key_c => $value_c) {
													if ($i % 2 == 0) $bgcolor = "#E9F3FF";
													else $bgcolor = "#FFFFFF";
													//if($value_c != "")
													//{
												?>
													<tr bgcolor="<? echo $bgcolor; ?>">
														<td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
														<td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
														<td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
														<td align="right"><? echo  $value_c['challan_no']; ?></td>
														<?
														foreach ($job_size_array[$order_number] as $key_s => $value_s) {

														?>
															<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$key_s]['product_qty']; ?></td>
														<?

														}
														?>
														<td align="right"><? echo  $value_c['product_qty'];
																			$job_color_tot += $job_color_qnty_array[$value_po][$value_c]; ?></td>

													</tr>
												<?
													$i++;
													//}
												}
												?>
												<tfoot>
													<tr bgcolor="<? // echo $bgcolor;
																	?>">
														<th></th>
														<th></th>
														<th></th>
														<th>Total</th>

														<?
														foreach ($job_size_array[$order_number] as $key_s => $value_s) {
															if ($value_s != "") {
														?>
																<th width="60" align="right"><? echo $job_size_qnty_array[$key_s]; ?></th>
														<?
															}
														}
														?>
														<th align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
													</tr>
												</tfoot>
											</table>
											<br />
							</fieldset>
						</div>
					<?
				}

				if ($action == "remarks_popup") {
					extract($_REQUEST);
					$data = explode("_", $data);
					echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
					$inp_com = return_library_array("select distinct a.id, a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.id=b.supplier_id and  b.party_type=41 and a.status_active=1 and a.is_deleted=0 order by a.supplier_name ", "id", "supplier_name");


					$style_wise_ins_arr = array();
					$style_wise_ins_sql = "SELECT inspection_company,inspected_by,inspection_date,inspection_qnty,inspection_status,inspection_cause   from wo_po_details_master a,wo_po_break_down b,pro_buyer_inspection c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and  a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and a.style_ref_no='$data[0]' and b.id='$data[1]' group by inspection_company,inspected_by,inspection_date,inspection_qnty,inspection_status,inspection_cause  ";




					?>


						</head>

						<body>
							<div align="center" style="width:100%;">


								<table width="450" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">

									<thead>
										<tr>
											<td colspan="4" align="center"><strong>Buyer Inspection Summary</strong></td>
										</tr>

										<tr>
											<th width="110">Inp. Date</th>
											<th width="110">Inspected By</th>
											<th width="110">Inspec. Qty</th>
											<th width="110">Inspec. Status</th>

										</tr>
									</thead>
								</table>
								<div style="max-height:300px; overflow:auto;">
									<table id="table_body" width="450" border="1" rules="all" class="rpt_table">
										<?

										foreach (sql_select($style_wise_ins_sql) as $keys => $vals) {
										?>
											<tr>
												<td align="center"><? echo change_date_format($vals[csf("inspection_date")]); ?></td>
												<td align="center"><? echo $inspected_by_arr[$vals[csf("inspected_by")]]; ?></td>
												<td align="center"><? echo $vals[csf("inspection_qnty")]; ?></td>
												<td align="center"><? echo $inspection_status[$vals[csf("inspection_status")]]; ?></td>
											</tr>


										<?

										}
										?>





									</table>
								</div>



								<script>
									setFilterGrid("table_body", -1);
								</script>

							</div>

						</body>
						<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

						</html>

					<?

					exit();
				}

if($action=="file_show")
{
	echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_cond= "";
	if($type == 1 && $file_type == 1){$sql_cond .=" and form_name= 'knit_order_entry_front' and file_type= 1 ";} //front image
	if($type == 2 && $file_type == 1){$sql_cond .=" and form_name= 'knit_order_entry_back' and file_type= 1 ";} //back image
	if($type == 3 && $file_type == 2){$sql_cond .=" and form_name= 'knit_order_entry' and file_type= 2 ";} //xls file

	$data_array=sql_select("SELECT image_location, real_file_name from common_photo_library where master_tble_id in ('$mst_id') $sql_cond and is_deleted=0");

	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        {
			?>
            <td><a href="<? echo "../../../".$row['IMAGE_LOCATION']; ?>" target="_new">
            	<img src="<? echo "../../../".'file_upload/blank_file.png'; ?>" width="80" height="60"> <br>
				<?=$row["REAL_FILE_NAME"];?>
			</a>
            </td>
			<?
        }
        ?>
        </tr>
    </table>
    <?
	exit();
}
