<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
include('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.conversions.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id = $_SESSION['logic_erp']['user_id'];
$permission = $_SESSION['page_permission'];

//---------------------------------------------------- Start-----------------------------------------------------------------------------

if ($action == "variable_chack") {
	extract($_REQUEST);
	//echo "$company";
	$company = str_replace("'", "", $company);
	$variable_setting = return_field_value("color_from_library", "variable_order_tracking", "variable_list=24 and company_name=$company");
	echo $variable_setting;
	exit;
}

if ($action == "button_setting_data") {
	extract($_REQUEST);
	$print_report_format = return_field_value("format_id", "lib_report_template", "template_name ='" . $data . "' and module_id=2 and report_id =7 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('" . $print_report_format . "');\n";
	exit;
}

if ($action == "load_drop_down_supplier") {

	if ($data == 5 || $data == 3) {
		echo create_drop_down("cbo_supplier_name", 140, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name", 1, "-- Select Company --", "", "", 0, "");
	} else {
		$sql = "SELECT DISTINCT a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company c  where a.id=c.supplier_id  and a.status_active IN(1,3)  and a.id in(select supplier_id from lib_supplier_party_type where party_type in (2,22))  group by a.id,a.supplier_name 
		";
		//echo $sql;
		echo create_drop_down("cbo_supplier_name", 140, "$sql", "id,supplier_name", 1, "--Select Supplier--", $selected, "", "");
	}

	exit();
}

if ($action == "check_conversion_rate") //Conversion Exchange Rate
{
	$data = explode("**", $data);
	if ($db_type == 0) {
		$conversion_date = change_date_format($data[1], "Y-m-d", "-", 1);
	} else {
		$conversion_date = change_date_format($data[1], "d-M-y", "-", 1);
	}
	$currency_rate = set_conversion_rate($data[0], $conversion_date, $data[2]);
	echo "1" . "_" . $currency_rate;
	exit;
}

if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 145, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
	exit;
}

if ($action == "load_drop_down_color") {
	$sql = "SELECT a.stripe_color AS STRIPE_COLOR FROM wo_pre_stripe_color a, inv_material_allocation_dtls b WHERE a.job_no=b.job_no and a.job_no='" . $data . "' and b.qnty>0  and a.yarn_dyed=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 GROUP BY a.stripe_color";
	$sql_rslt = sql_select($sql);
	$stripe_color_arr = array();
	foreach ($sql_rslt as $row) {
		$stripe_color_arr[$row['STRIPE_COLOR']] = $row['STRIPE_COLOR'];
	}

	if (empty($stripe_color_arr)) {
		$sql = "SELECT a.stripe_color AS STRIPE_COLOR FROM wo_pre_stripe_color a WHERE a.job_no='" . $data . "' and a.yarn_dyed=1 and a.status_active=1 and a.is_deleted=0 GROUP BY a.stripe_color";
		$sql_rslt = sql_select($sql);
		foreach ($sql_rslt as $row) {
			$stripe_color_arr[$row['STRIPE_COLOR']] = $row['STRIPE_COLOR'];
		}
	}

	$stripe_color_no = 0;
	if (!empty($stripe_color_arr)) {
		$stripe_color_no = implode(",", $stripe_color_arr);
	}

	echo create_drop_down("txt_yern_color", 90, "select id,color_name from  lib_color where id in($stripe_color_no) ", "id,color_name", 1, "-- Select color--", $selected, "get_php_form_data(document.getElementById('txt_job_no').value+'**'+this.value+'**'+document.getElementById('update_id').value+'**'+document.getElementById('dtls_update_id').value+'**'+document.getElementById('txt_fab_booking_no').value+'**'+document.getElementById('txt_po_id').value+'**'+document.getElementById('cbo_company_name').value, 'populate_budge_req_data', 'requires/yarn_dyeing_charge_booking_controller' )");
	exit;
}

if ($action == "load_drop_down_po_color") {
	$sql_color = sql_select("select job_no_mst,color_number_id as color_id from wo_po_color_size_breakdown where po_break_down_id in($data) and status_active=1 and is_deleted=0");

	foreach ($sql_color as $row) {
		$color_id .= $row[csf('color_id')] . ',';
		$job_no_mst = $row[csf('job_no_mst')];
	}

	$color_id = rtrim($color_id, ',');
	$gmts_color_id = implode(",", array_unique(explode(",", $color_id)));
	//echo "select job_no_mst,color_number_id as color_id from wo_po_color_size_breakdown where po_break_down_id in($data) and status_active=1 and is_deleted=0";die;

	if ($db_type == 0) {
		$color_ids = return_field_value("group_concat(distinct stripe_color) as color_number_id", "wo_pre_stripe_color", "job_no='$job_no_mst' and color_number_id in($gmts_color_id) and status_active=1 and is_deleted=0", "color_number_id");
	} else if ($db_type == 2) {
		$color_ids = return_field_value("LISTAGG(cast(stripe_color as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY stripe_color) as color_number_id", "wo_pre_stripe_color", "job_no='$job_no_mst'  and color_number_id in($gmts_color_id) and status_active=1 and is_deleted=0", "color_number_id");

		//echo "select LISTAGG(cast(stripe_color as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY stripe_color) as color_number_id from wo_pre_stripe_color where job_no='$job_no_mst'  and color_number_id in($gmts_color_id) and status_active=1 and is_deleted=0";
	}

	$stripe_color_no = implode(",", array_unique(explode(",", $color_ids)));

	if (empty($stripe_color_no)) $stripe_color_no = 0;
	//echo "select id,color_name from  lib_color where id in($stripe_color_no)";
	echo create_drop_down("txt_yern_color", 90, "select id,color_name from  lib_color where id in($stripe_color_no)", "id,color_name", 1, "-- Select color--", $selected, "get_php_form_data(document.getElementById('txt_job_no').value+'**'+this.value+'**'+document.getElementById('update_id').value+'**'+document.getElementById('dtls_update_id').value+'**'+document.getElementById('txt_fab_booking_no').value+'**'+document.getElementById('txt_po_id').value+'**'+document.getElementById('cbo_company_name').value, 'populate_budge_req_data', 'requires/yarn_dyeing_charge_booking_controller' )");
	exit;
}


if ($action == "populate_budge_req_data") {
	$data_ref = explode("**", $data);
	$job_no = $data_ref[0];
	$yarn_color = $data_ref[1];
	$Wo_mst_id = $data_ref[2];
	$wo_dtls_id = $data_ref[3];
	$booking_no = $data_ref[4];
	$po_ids = $data_ref[5];
	$company_id = $data_ref[6];

	if ($Wo_mst_id > 0) {
		$mst_cond = "  and a.mst_id!=$Wo_mst_id";
		$booking_id_cond = " and b.booking_id=$Wo_mst_id";
	} else {
		$mst_cond = "";
		$booking_id_cond = "";
	}
	//echo $wo_dtls_id.'DDX';
	if ($wo_dtls_id > 0)
		$dtls_cond = "  and a.id!=$wo_dtls_id";
	else
		$dtls_cond = "";

	$stripe_info = sql_select("select a.stripe_color,a.color_number_id, a.measurement,a.totfidder, a.pre_cost_fabric_cost_dtls_id from wo_pre_stripe_color a where  a.job_no='" . $data_ref[0] . "' and a.status_active=1");

	$total_measurement = array();
	$stripe_color_measurement = array();
	if (!empty($stripe_info)) {
		foreach ($stripe_info as $row) {
			$measurement_or_totfidder = ($row[csf('measurement')] != "" && $row[csf('measurement')] > 0) ? $row[csf('measurement')] : $row[csf('totfidder')];
			$total_measurement[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]] += $measurement_or_totfidder;
			$stripe_color_measurement[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('stripe_color')]][$row[csf('color_number_id')]] += $measurement_or_totfidder;
		}
	}

	/* echo "<pre>";
	print_r($total_measurement);
	die; */

	$stripe_required = 0;

	$stripe_required_data = sql_select("select b.pre_cost_fabric_cost_dtls_id,b.gmts_color_id as color_number_id, sum(b.grey_fab_qnty) grey_fab_qnty from wo_booking_dtls b where b.job_no='$data_ref[0]' and b.booking_no='$booking_no' and b.status_active=1 and b.is_deleted=0 group by b.pre_cost_fabric_cost_dtls_id,b.gmts_color_id");

	if (!empty($stripe_required_data)) {
		$booking_req_qty = 0;
		$booking_req_amount = 0;

		foreach ($stripe_required_data as $row) {
			$stripe_required_by_presoct_id[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]['quantity'] = $row[csf('grey_fab_qnty')];
			$booking_req_qty += $row[csf('grey_fab_qnty')];
		}

		foreach ($stripe_required_data as $row) {
			$total_measurements = $total_measurement[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]];
			$total_color_measurement = $stripe_color_measurement[$row[csf('pre_cost_fabric_cost_dtls_id')]][$data_ref[1]][$row[csf('color_number_id')]];
			$stripe_required_qty = $stripe_required_by_presoct_id[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]['quantity'];

			$measurement_perc = ($total_color_measurement / $total_measurements) * 100;
			$booking_color_wise_stripe_required_qty = ($measurement_perc / 100) * $stripe_required_qty;

			$booking_color_wise_stripe_required_qty = (is_nan($booking_color_wise_stripe_required_qty)) ? 0 : $booking_color_wise_stripe_required_qty;
			$booking_stripe_required_qty[$data_ref[1]][] = $booking_color_wise_stripe_required_qty;
		}
	}

	$rate_from_library = 0;
	$rate_from_library = return_field_value("is_serveice_rate_lib", "variable_settings_production", "service_process_id=19 and company_name=" . $company_id . " and status_active=1 and is_deleted=0 ");

	$vs_merchandising_sql = "select exeed_budge_qty,exeed_budge_amount,amount_exceed_level,exceed_qty_level from variable_order_tracking where company_name=$company_id and item_category_id=1 and variable_list=26 and status_active=1 and is_deleted=0";
	$vs_merchandising_result = sql_select($vs_merchandising_sql);
	$exeed_budge_qty = $vs_merchandising_result[0][csf('exeed_budge_qty')];
	$exeed_budge_amount = $vs_merchandising_result[0][csf('exeed_budge_amount')];
	$amount_exceed_level = $vs_merchandising_result[0][csf('amount_exceed_level')];
	$exceed_yes_no = $vs_merchandising_result[0][csf('exceed_qty_level')];
	//echo $exeed_budge_qty."=".$exeed_budge_amount."=".$amount_exceed_level."=".$exceed_yes_no; die;

	$dyeing_charge_asper_budget = return_field_value("unit_charge as dyeing_charge", " wo_pre_cos_conv_color_dtls", "job_no='$job_no' and fabric_color_id=$yarn_color and status_active=1 and is_deleted=0 ", "dyeing_charge");

	$pre_cost_conversion_sql = "select id as PRE_COST_CONVERSION_ID from wo_pre_cost_fab_conv_cost_dtls where job_no='$job_no' and cons_process =30 and status_active=1 and is_deleted=0";
	$pre_cost_conversion_sql_result = sql_select($pre_cost_conversion_sql);

	$pre_cost_conversion_id_arr = array();
	foreach ($pre_cost_conversion_sql_result as $row) {
		$pre_cost_conversion_id_arr[$row['PRE_COST_CONVERSION_ID']] = $row['PRE_COST_CONVERSION_ID'];
	}

	$condition = new condition();
	if ($job_no != '') {
		$condition->job_no("in('$job_no')");
	}
	$condition->init();

	$conversion = new conversion($condition);
	//echo $conversion->getQuery();
	$conv_amount_arr = $conversion->getAmountArray_by_conversionid();
	$conv_qty_arr = $conversion->getQtyArray_by_conversionid();

	$pre_cost_yarn_deying_amount = 0;
	foreach ($conv_qty_arr as $cPrecostdtlsid => $cuomval) {
		foreach ($cuomval as $cuom => $cvalue) {
			foreach ($pre_cost_conversion_id_arr as $pre_cost_conversion_id) {
				//echo $uom.'=';
				//$pre_cost_yarn_deying_qty += fn_number_format($cvalue,2,".","");
				if ($pre_cost_conversion_id == $cPrecostdtlsid) {
					$pre_cost_yarn_deying_amount += fn_number_format($conv_amount_arr[$pre_cost_conversion_id][12], 2, ".", "");
				}
			}
		}
	}

	$vs_exceed_percentage_amount = ($exceed_yes_no == 1 && $exeed_budge_amount > 0) ? ($pre_cost_yarn_deying_amount / 100) * $exeed_budge_amount : 0;

	$prev_wo_sql = "select JOB_NO,FAB_BOOKING_NO,YARN_COLOR,YARN_WO_QTY,AMOUNT from  wo_yarn_dyeing_dtls where job_no='$data_ref[0]' and status_active=1 and is_deleted=0 $dtls_cond"; //and yarn_color=$data_ref[1]

	$prev_wo_sql_resutl = sql_select($prev_wo_sql);
	$work_order_prev_qty_arr = $work_order_prev_amount_arr = array();
	foreach ($prev_wo_sql_resutl as $row) {
		$work_order_prev_qty_arr[$row['JOB_NO']][$row['FAB_BOOKING_NO']][$row['YARN_COLOR']] += $row['YARN_WO_QTY'];
		$work_order_prev_amount_arr[$row['JOB_NO']]['amount'] += $row['AMOUNT'];
	}

	/* echo "<pre>";
	print_r($work_order_prev_amount_arr);
	die; */

	$prev_booking_qty = $work_order_prev_qty_arr[$data_ref[0]][$booking_no][$data_ref[1]];
	$prev_booking_amount = $work_order_prev_amount_arr[$data_ref[0]]['amount'];

	$issue_rtn_sql = "select sum(c.cons_quantity) as issue_return_qnty,sum(c.cons_amount) as issue_return_amount from wo_yarn_dyeing_dtls a, inv_receive_master b, inv_transaction c,order_wise_pro_details d where a.mst_id=b.booking_id and b.id=c.mst_id and c.id=d.trans_id and a.job_no='$job_no' and a.fab_booking_no='$booking_no' and a.yarn_color=$yarn_color and b.entry_form=9 and b.item_category=1 and c.item_category=1 and c.transaction_type=4 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $booking_id_cond $dtls_cond  and po_breakdown_id in($po_ids) group by a.job_no, a.yarn_color";

	$issue_rtn_result = sql_select($issue_rtn_sql);
	$issue_return_qnty = $issue_rtn_result[0][csf('issue_return_qnty')];
	$issue_return_amount = $issue_rtn_result[0][csf('issue_return_amount')];

	$booking_color_wise_required_qty  = array_sum($booking_stripe_required_qty[$data_ref[1]]);
	$cum_bal = ($booking_color_wise_required_qty + $issue_return_qnty) - $prev_booking_qty;
	$booking_cum_bal = ($booking_req_qty + $issue_return_qnty) - $prev_booking_qty;
	$cumalitive_precost_yarn_deing_amount = ($pre_cost_yarn_deying_amount + $vs_exceed_percentage_amount + $issue_return_amount) - $prev_booking_amount;

	//echo $booking_req_qty.'='.$prev_booking_qty.'D';
	echo "$('#txt_budget_wo_qty').val('" . number_format($cum_bal, 2, '.', '') . "');\n";
	echo "$('#txt_booking_req_qty').val('" . number_format($booking_req_qty, 2, '.', '') . "');\n";
	echo "$('#txt_booking_req_qty').attr('booking_bal'," . number_format($booking_cum_bal, 2, '.', '') . ");\n";
	echo "$('#txt_dyeing_charge').attr('booking_bal'," . number_format($booking_cum_bal, 2, '.', '') . ");\n";
	echo "$('#txt_dyeing_charge').attr('placeholder','" . $dyeing_charge_asper_budget . "');\n";
	echo "$('#txt_amount').attr('placeholder','" . number_format($cumalitive_precost_yarn_deing_amount, 2, '.', '') . "');\n";
	echo "$('#txt_amount').attr('title','" . number_format($cumalitive_precost_yarn_deing_amount, 2, '.', '') . "');\n";
	echo "$('#hdn_precost_yarn_dyeing_cumalitive_amount').val('" . number_format($cumalitive_precost_yarn_deing_amount, 2, '.', '') . "');\n";
	echo "$('#hdn_exceed_budge_qty').val('" . number_format($exeed_budge_qty, 2, '.', '') . "');\n"; // from vs 
	echo "$('#hdn_exceed_budge_amount').val('" . number_format($exeed_budge_amount, 2, '.', '') . "');\n"; // from vs 
	echo "$('#hdn_amount_exceed_level').val('" . $amount_exceed_level . "');\n"; // from vs 
	echo "$('#hdn_exceed_yes_no').val('" . $exceed_yes_no . "');\n"; // from vs 
	echo "$('#txt_budget_wo_amount').val('" . number_format($cum_bal_amount, 2, '.', '') . "');\n";
	echo "$('#txt_booking_req_amount').val('" . number_format($booking_req_amount, 2, '.', '') . "');\n";
	//echo "$('#txt_booking_req_amount').attr('booking_amount'," . number_format($booking_cum_bal_amount, 2, '.', '') . ");\n";
	echo "document.getElementById('service_rate_from').value = '" . $rate_from_library . "';\n";
	exit;
}

$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
$color_arr = return_library_array("select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name");
$count_arr = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
$brand_arr = return_library_array("Select id, brand_name from  lib_brand where  status_active=1", 'id', 'brand_name');
$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

if ($action == "booking_search_popup") {
	echo load_html_head_contents("Booking Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);

?>
	<script>
		function js_set_value(str) {
			// wo/pi id
			$("#hidden_tbl_id").val(str);
			parent.emailwindow.hide();
		}
	</script>

	<div align="center" style="width:900px;">
		<form name="searchjob" id="searchjob" autocomplete="off">
			<table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<th width="145">Company</th>
					<th width="145">Buyer</th>
					<th width="80">Booking No</th>
					<th width="80">Style No</th>
					<th width="100">Job No</th>
					<th width="80">Order No</th>
					<th width="80">Budget Version</th>
					<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('searchjob','search_div','')" /></th>
				</thead>
				<tbody>
					<tr>
						<td>
							<?
							echo create_drop_down("cbo_company_name", 145, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", str_replace("'", "", $company)/*$selected */, "load_drop_down( 'yarn_dyeing_charge_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
							?>

							<input class="datepicker" type="hidden" style="width:130px" name="txt_booking_date" id="txt_booking_date" value="<? echo str_replace("'", "", $txt_booking_date) ?>" disabled />
						</td>
						</td>

						<td align="center" id="buyer_td">
							<?
							$blank_array = "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
							echo create_drop_down("cbo_buyer_name", 145, $blank_array, "id,buyer_name", 1, "-- Select Buyer --", 0);
							?>
						</td>
						<td align="center">
							<input type="text" name="txt_fab_booking_no" id="txt_fab_booking_no" class="text_boxes" style="width:75px" />
						</td>
						<td align="center">
							<input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:75px" />
						</td>
						<td align="center">
							<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px" value="<? echo $txt_job_no; ?>" />
						</td>
						<td align="center">
							<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:75px" />
						</td>
						<td align="center">
							<?
							$pre_cost_class_arr = array(1 => 'Pre Cost 1', 2 => 'Pre Cost 2', 3 => 'Pre Cost 3');
							echo create_drop_down("cbo_budget_version", 100, $pre_cost_class_arr, "", 0, "-- Select Version --", $budget_version, '', 1);
							?>
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_fab_booking_no').value+'_'+document.getElementById('cbo_budget_version').value, 'create_booking_search_list_view', 'search_div', 'yarn_dyeing_charge_booking_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:70px;" />
						</td>
					</tr>
				</tbody>
			</table>
			<br>
			<div align="center" valign="top" id="search_div"> </div>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "create_booking_search_list_view") {
	$data = explode("_", $data);
	$cbo_company_name = str_replace("'", "", $data[0]);
	$cbo_buyer_name = str_replace("'", "", $data[1]);
	$txt_job_no = str_replace("'", "", $data[2]);
	$txt_style_no = str_replace("'", "", $data[3]);
	$txt_order_no = str_replace("'", "", $data[4]);
	$fab_booking_no = str_replace("'", "", $data[5]);
	$budget_version = str_replace("'", "", $data[6]);

	$sql_cond = "";
	if ($cbo_company_name != 0) $sql_cond = " and a.company_name='$cbo_company_name'";
	if ($cbo_buyer_name != 0) $sql_cond .= " and a.buyer_name='$cbo_buyer_name'";
	if ($txt_job_no != "") $sql_cond .= " and a.job_no LIKE '%$txt_job_no%'";
	if ($fab_booking_no != "") $sql_cond .= " and d.booking_no LIKE '%$fab_booking_no%'";
	if ($txt_style_no != "") $sql_cond .= " and a.style_ref_no like '%$txt_style_no%'";
	if ($txt_order_no != "") $sql_cond .= " and b.po_number like '%$txt_order_no%'";

	if ($budget_version == 1) //Version 1
	{
		$entry_form = "and c.entry_from=111";
	} else if ($budget_version == 2) //Version 2
	{
		$entry_form = "and c.entry_from=158";
	} else //Version 3
	{
		//$entry_form="and c.entry_from=237";
		$entry_form = "and c.entry_from=520";
	}

	//wo_booking_dtls
	if ($db_type == 0) {
		$sql = "select a.id, d.booking_no, a.company_name, a.buyer_name, a.style_ref_no, (b.id) as poid, year(a.insert_date) as year from  wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c,wo_booking_dtls d where a.job_no=b.job_no_mst and a.job_no=c.job_no  and d.po_break_down_id=b.id and d.job_no=a.job_no and c.job_no=d.job_no and d.booking_type in(1,4) and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and d.color_type in(2,3,4,6,32,33,44,47,48,63) $sql_cond $entry_form group by a.job_no order by a.insert_date desc";
	} else if ($db_type == 2) {

		$sql = "select a.id, c.job_no,d.booking_no, a.company_name, a.buyer_name, a.style_ref_no,  b.id as poid, to_char(a.insert_date,'YYYY') as year,d.is_short from  wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c,wo_booking_dtls d,wo_pre_cost_fabric_cost_dtls e where a.job_no=b.job_no_mst  and a.job_no=c.job_no  and d.po_break_down_id=b.id and d.job_no=a.job_no and c.job_no=d.job_no and d.pre_cost_fabric_cost_dtls_id=e.id and d.booking_type in(1,4) and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.color_type_id in(2,3,4,6,32,33,44,47,48,63) $sql_cond $entry_form order by a.insert_date desc";
	}

	$nameArray = sql_select($sql);
	$poidstr = "";
	foreach ($nameArray as $row) {
		if ($poidstr == "") $poidstr = $row[csf('poid')];
		else $poidstr .= ',' . $row[csf('poid')];

		$booking_arr[$row[csf('booking_no')]]['poid'] .= $row[csf('poid')] . ',';
		$booking_arr[$row[csf('booking_no')]]['company_name'] = $row[csf('company_name')];
		$booking_arr[$row[csf('booking_no')]]['buyer_name'] = $row[csf('buyer_name')];
		$booking_arr[$row[csf('booking_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$booking_arr[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
		$booking_arr[$row[csf('booking_no')]]['year'] = $row[csf('year')];
		$booking_arr[$row[csf('booking_no')]]['is_short'] = $row[csf('is_short')];
		//$booking_arr[$row[csf('booking_no')]]['job_no']=$row[csf('job_no')];
	}

	$ponoId = implode(",", array_filter(array_unique(explode(",", $poidstr))));
	$po_ids = count(explode(",", $poidstr));
	$poidCond = "";
	if ($db_type == 2 && $po_ids > 1000) {
		$poidCond = " and (";
		$ponoIdArr = array_chunk(explode(",", $ponoId), 999);
		foreach ($ponoIdArr as $ids) {
			$ids = implode(",", $ids);
			$poidCond .= " id in($ids) or";
		}
		$poidCond = chop($poidCond, 'or ');
		$poidCond .= ")";
	} else $poidCond = " and id in($ponoId)";

	$poDataArr = array();
	$poIdDataArray = sql_select("SELECT id, po_number, file_no, grouping from wo_po_break_down where status_active=1 $poidCond ");
	foreach ($poIdDataArray as $row) {
		$poDataArr[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$poDataArr[$row[csf('id')]]['file'] = $row[csf('file_no')];
		$poDataArr[$row[csf('id')]]['ref'] = $row[csf('grouping')];
	}
	unset($poIdDataArray);
	//echo $sql;
	echo '<input type="hidden" id="hidden_tbl_id">';
?>
	<div style="width:800px;" align="left">

		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width="50">Year</th>
				<th width="120">Booking No</th>
				<th width="130">Buyer</th>
				<th width="120"> Job No</th>
				<th width="150"> Style Ref</th>

				<th>Order No.</th>

			</thead>
		</table>
		<div style="width:790px; overflow-y:scroll; max-height:250px;" id="buyer_list_view">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table" id="tbl_list_search">
				<?

				$i = 1;
				$nameArray = sql_select($sql);
				foreach ($booking_arr as $bookino_no => $selectResult) {
					$expoid = array_unique(explode(",", $selectResult[("poid")])); //
					$is_short = $selectResult[("is_short")];
					$poNo = "";
					$fileNo = "";
					$refNo = "";
					foreach ($expoid as $pid) {
						if ($poNo == "") $poNo = $poDataArr[$pid]['po'];
						else $poNo .= ',' . $poDataArr[$pid]['po'];
						if ($fileNo == "") $fileNo = $poDataArr[$pid]['file'];
						else $fileNo .= ',' . $poDataArr[$pid]['file'];
						if ($refNo == "") $refNo = $poDataArr[$pid]['ref'];
						else $refNo .= ',' . $poDataArr[$pid]['ref'];
					}

					$po_number = implode(",", array_filter(array_unique(explode(",", $poNo))));
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";
					$file_no = implode(",", array_filter(array_unique(explode(",", $fileNo))));
					$int_ref_no = implode(",", array_filter(array_unique(explode(",", $refNo))));
					//echo $is_short;
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value('<? echo $bookino_no; ?>'+'_'+'<? echo $budget_version; ?>'+'_'+'<? echo $is_short; ?>'); ">

						<td width="40">
							<p> <? echo $i; ?></p>
						</td>
						<td width="50" align="center">
							<p><? echo $selectResult[('year')]; ?></p>
						</td>
						<td width="120" align="center">
							<p><? echo $bookino_no; ?></p>
						</td>
						<td width="130">
							<p><? echo  $buyer_arr[$selectResult[('buyer_name')]]; ?></p>
						</td>
						<td width="120">
							<p><? echo $selectResult[('job_no')]; ?></p>
						</td>
						<td width="150">
							<p><? echo $selectResult[('style_ref_no')]; ?></p>
						</td>

						<td>
							<p> <? echo $po_number; ?></p>
						</td>
					</tr>
				<?
					$i++;
				}
				?>
			</table>
		</div>
	</div>
<?
}

if ($action == "order_search_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);

?>
	<script>
		function js_set_value(str) {
			// wo/pi id
			$("#hidden_tbl_id").val(str);
			parent.emailwindow.hide();
		}
	</script>

	<div align="center" style="width:900px;">
		<form name="searchjob" id="searchjob" autocomplete="off">
			<table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<th width="145">Company</th>
					<th width="145">Buyer</th>
					<th width="80">Job No</th>
					<th width="80">Style No</th>
					<th width="80">Order No</th>
					<th width="80">File No</th>
					<th width="80">Int. Ref. No</th>
					<th width="80">Budget Version</th>
					<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('searchjob','search_div','')" /></th>
				</thead>
				<tbody>
					<tr>
						<td>
							<?
							echo create_drop_down("cbo_company_name", 145, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", str_replace("'", "", $company)/*$selected */, "load_drop_down( 'yarn_dyeing_charge_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
							?>

							<input class="datepicker" type="hidden" style="width:130px" name="txt_booking_date" id="txt_booking_date" value="<? echo str_replace("'", "", $txt_booking_date) ?>" disabled />
						</td>

						</td>

						<td align="center" id="buyer_td">
							<?
							$blank_array = "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
							echo create_drop_down("cbo_buyer_name", 145, $blank_array, "id,buyer_name", 1, "-- Select Buyer --", 0);
							?>
						</td>
						<td align="center">
							<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:75px" />
						</td>
						<td align="center">
							<input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:75px" />
						</td>
						<td align="center">
							<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:75px" />
						</td>
						<td align="center">
							<input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:75px" />
						</td>
						<td align="center">
							<input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:75px" />
						</td>
						<td align="center">
							<?
							$pre_cost_class_arr = array(1 => 'Pre Cost 1', 2 => 'Pre Cost 2', 3 => 'Pre Cost 3');
							echo create_drop_down("cbo_budget_version", 100, $pre_cost_class_arr, "", 0, "-- Select Version --", $budget_version, '', 1);
							?>
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_booking_date').value+'_'+document.getElementById('cbo_budget_version').value, 'create_job_search_list_view', 'search_div', 'yarn_dyeing_charge_booking_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:70px;" />
						</td>
					</tr>
				</tbody>
			</table>
			<br>
			<div align="center" valign="top" id="search_div"> </div>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "create_job_search_list_view") {
	$data = explode("_", $data);
	$cbo_company_name = str_replace("'", "", $data[0]);
	$cbo_buyer_name = str_replace("'", "", $data[1]);
	$txt_job_no = str_replace("'", "", $data[2]);
	$txt_style_no = str_replace("'", "", $data[3]);
	$txt_order_no = str_replace("'", "", $data[4]);
	$txt_file_no = str_replace("'", "", $data[5]);
	$txt_ref_no = str_replace("'", "", $data[6]);
	$txt_booking_date = str_replace("'", "", $data[7]);
	$budget_version = str_replace("'", "", $data[8]);

	$sql_cond = "";
	if ($cbo_company_name != 0) $sql_cond = " and a.company_name='$cbo_company_name'";
	if ($cbo_buyer_name != 0) $sql_cond .= " and a.buyer_name='$cbo_buyer_name'";
	if ($txt_job_no != "") $sql_cond .= " and a.job_no_prefix_num='$txt_job_no'";
	if ($txt_style_no != "") $sql_cond .= " and a.style_ref_no like '%$txt_style_no%'";
	if ($txt_order_no != "") $sql_cond .= " and b.po_number like '%$txt_order_no%'";
	if ($txt_file_no != "") $sql_cond .= " and b.file_no like '%$txt_file_no%'";
	if ($txt_ref_no != "") $sql_cond .= " and b.grouping like '%$txt_ref_no%'";

	$approval_allow = sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and b.page_id=25 and b.status_active=1 and b.is_deleted=0 and a.setup_date=(SELECT MAX(setup_date) FROM approval_setup_mst  WHERE company_id='$data[0]') order by b.id desc ");

	if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 1)
		$approval_cond = "and c.approved in (1,3)";
	else if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 2)
		$approval_cond = "and c.approved in (1)";
	else if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 0)
		$approval_cond = "and c.approved in (1,3)";
	else $approval_cond = "";

	if ($budget_version == 1) //Version 1
	{
		$entry_form = "and c.entry_from=111";
	} else if ($budget_version == 2) //Version 2
	{
		$entry_form = "and c.entry_from=158";
	} else //Version 3
	{
		//$entry_form="and c.entry_from=237";
		$entry_form = "and c.entry_from=520";
	}

	if ($db_type == 0) {
		$sql = "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, group_concat(b.po_number) as po_number, group_concat(b.file_no) as file_no, group_concat(b.grouping) as grouping, year(a.insert_date) as year from  wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.shiping_status not in(3) $approval_cond $sql_cond $entry_form $approval_cond group by a.job_no order by a.insert_date desc";
	} else if ($db_type == 2) {

		$sql = "SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, 
		 rtrim(xmlagg(xmlelement(e,b.po_number,',').extract('//text()') order by b.po_number).GetClobVal(),',') AS po_number,
		 rtrim(xmlagg(xmlelement(e,b.file_no,',').extract('//text()') order by b.file_no).GetClobVal(),',') AS file_no, 
		 rtrim(xmlagg(xmlelement(e,b.GROUPING,',').extract('//text()') order by b.GROUPING).GetClobVal(),',') AS GROUPING, 
		 TO_CHAR (a.insert_date, 'YYYY') AS YEAR FROM wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c,wo_pre_cost_fab_conv_cost_dtls d WHERE a.id = b.job_id AND b.job_id=c.job_id and c.job_id=d.job_id  AND a.id = c.job_id AND a.id=d.job_id AND d.cons_process=30 AND d.charge_unit>0 AND a.status_active = 1 AND a.is_deleted=0 AND b.status_active = 1 AND b.is_deleted=0 AND c.status_active = 1 AND c.is_deleted=0 AND d.status_active = 1 AND d.is_deleted=0 AND b.shiping_status NOT IN (3) $approval_cond $sql_cond $entry_form $approval_cond GROUP BY a.ID, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date ORDER BY a.insert_date DESC";
	}
	//echo $sql;
	echo '<input type="hidden" id="hidden_tbl_id">';
?>
	<div style="width:810px;" align="left">

		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width="50">Year</th>
				<th width="60">Job No</th>
				<th width="130">Buyer</th>
				<th width="100">Style Ref.NO</th>
				<th width="100">File No</th>
				<th width="100">Internal Ref. No</th>
				<th>Order No.</th>
			</thead>
		</table>
		<div style="width:808px; overflow-y:scroll; max-height:250px;" id="buyer_list_view">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table" id="tbl_list_search">
				<?

				$i = 1;
				$nameArray = sql_select($sql);
				foreach ($nameArray as $selectResult) {
					$po_number = implode(",", array_unique(explode(",", $selectResult[csf("po_number")]->load())));
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";
					$file_no = implode(",", array_unique(explode(",", $selectResult[csf('file_no')]->load())));
					$int_ref_no = implode(",", array_unique(explode(",", $selectResult[csf('grouping')]->load())));
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('job_no')]; ?>'+'_'+'<? echo $file_no; ?>'+'_'+'<? echo $int_ref_no; ?>'+'_'+'<? echo $budget_version; ?>'); ">

						<td width="40">
							<p> <? echo $i; ?></p>
						</td>
						<td width="50" align="center">
							<p><? echo $selectResult[csf('year')]; ?></p>
						</td>
						<td width="60" align="center">
							<p><? echo $selectResult[csf("job_no_prefix_num")]; ?></p>
						</td>
						<td width="130">
							<p><? echo  $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p>
						</td>
						<td width="100">
							<p><? echo $selectResult[csf('style_ref_no')]; ?></p>
						</td>
						<td width="100">
							<p><? echo $file_no; ?></p>
						</td>
						<td width="100">
							<p><? echo $int_ref_no; ?></p>
						</td>
						<td style="max-width:210px;">
							<p> <? echo $po_number; ?></p>
						</td>
					</tr>
				<?
					$i++;
				}
				?>
			</table>
		</div>
	</div>
	<?
	exit();
}

if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$txt_job_no = str_replace("'", "", $txt_job_no);
	$txt_job_id = str_replace("'", "", $txt_job_id);
	$txt_pro_id = str_replace("'", "", $txt_pro_id);
	$cbo_uom = str_replace("'", "", $cbo_uom);
	$cbo_uom = ($cbo_uom == "") ? 0 : $cbo_uom;;
	$cbo_count = str_replace("'", "", $cbo_count);
	$cbo_count = ($cbo_count == "") ? 0 : $cbo_count;
	$txt_item_des = str_replace("'", "", $txt_item_des);
	$color_id = str_replace("'", "", $color_id);
	$cbo_color_range = str_replace("'", "", $cbo_color_range);
	$cbo_color_range = ($cbo_color_range == "") ? 0 : $cbo_color_range;
	$txt_ref_no = str_replace("'", "", $txt_ref_no);
	$txt_yern_color = str_replace("'", "", $txt_yern_color);
	$is_short = str_replace("'", "", $cbo_is_short);
	$txt_file_no = trim($txt_file_no);

	$txt_dyeing_charge = str_replace("'", "", $txt_dyeing_charge);
	$txt_dyeing_charge = ($txt_dyeing_charge == "") ? 0 : $txt_dyeing_charge;
	$txt_amount = str_replace("'", "", $txt_amount);


	/**
	 * Descripttion : Budget amount can't greater than yarn dyeing amount if vs setting controll yes
	 */

	$vs_merchandising_sql = "select exeed_budge_qty,exeed_budge_amount,amount_exceed_level,exceed_qty_level from variable_order_tracking where company_name=$cbo_company_name and item_category_id=1 and variable_list=26 and status_active=1 and is_deleted=0";
	$vs_merchandising_result = sql_select($vs_merchandising_sql);
	$exeed_budge_qty = $vs_merchandising_result[0][csf('exeed_budge_qty')];
	$exeed_budge_amount = $vs_merchandising_result[0][csf('exeed_budge_amount')];
	$amount_exceed_level = $vs_merchandising_result[0][csf('amount_exceed_level')];
	$exceed_yes_no = $vs_merchandising_result[0][csf('exceed_qty_level')];
	//echo "10**" . $exeed_budge_qty . "=" . $exeed_budge_amount . "=" . $amount_exceed_level . "=" . $exceed_yes_no;
	//die;

	$pre_cost_conversion_sql = "select id as PRE_COST_CONVERSION_ID from wo_pre_cost_fab_conv_cost_dtls where job_no='$txt_job_no' and cons_process =30 and status_active=1 and is_deleted=0";
	$pre_cost_conversion_sql_result = sql_select($pre_cost_conversion_sql);

	$pre_cost_conversion_id_arr = array();
	foreach ($pre_cost_conversion_sql_result as $row) {
		$pre_cost_conversion_id_arr[$row['PRE_COST_CONVERSION_ID']] = $row['PRE_COST_CONVERSION_ID'];
	}

	$condition = new condition();
	if ($txt_job_no != '') {
		$condition->job_no("in('$txt_job_no')");
	}
	$condition->init();

	$conversion = new conversion($condition);
	//echo $conversion->getQuery();
	$conv_amount_arr = $conversion->getAmountArray_by_conversionid();
	$conv_qty_arr = $conversion->getQtyArray_by_conversionid();

	/* echo "10**<pre>";
	 print_r($conv_qty_arr);
	 die; */

	$pre_cost_yarn_deying_amount = 0;
	foreach ($conv_qty_arr as $cPrecostdtlsid => $cuomval) {
		foreach ($cuomval as $cuom => $cvalue) {

			foreach ($pre_cost_conversion_id_arr as $pre_cost_conversion_id) {
				//echo $uom.'=';
				//$pre_cost_yarn_deying_qty += fn_number_format($cvalue,2,".","");
				if ($pre_cost_conversion_id == $cPrecostdtlsid) {
					$pre_cost_yarn_deying_amount += fn_number_format($conv_amount_arr[$pre_cost_conversion_id][12], 2, ".", "");
				}
			}
		}
	}

	$dtls_cond = (str_replace("'", "", $dtls_update_id) > 0) ? " and id!=$dtls_update_id " : "";
	$booking_id_cond = (str_replace("'", "", $update_id) > 0) ? "  and b.booking_id=$update_id " : "";

	$prev_wo_sql = "select sum(amount) as AMOUNT from  wo_yarn_dyeing_dtls where job_no='$txt_job_no'and status_active=1 and is_deleted=0 $dtls_cond";
	$prev_wo_sql_resutl = sql_select($prev_wo_sql);
	$prev_booking_amount = $prev_wo_sql_resutl[0]['AMOUNT'];

	$issue_rtn_sql = "select sum(c.cons_amount) as issue_return_amount from wo_yarn_dyeing_dtls a, inv_receive_master b, inv_transaction c,order_wise_pro_details d where a.mst_id=b.booking_id and b.id=c.mst_id and c.id=d.trans_id and a.job_no='$txt_job_no'  and b.entry_form=9 and b.item_category=1 and c.item_category=1 and c.transaction_type=4 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $booking_id_cond ";

	$issue_rtn_result = sql_select($issue_rtn_sql);
	$issue_return_amount = $issue_rtn_result[0][csf('issue_return_amount')];

	$vs_exceed_percentage_amount = ($exceed_yes_no == 1 && $exeed_budge_amount > 0) ? ($pre_cost_yarn_deying_amount / 100) * $exeed_budge_amount : 0;

	$cumalitive_precost_yarn_deing_amount = ($pre_cost_yarn_deying_amount + $vs_exceed_percentage_amount + $issue_return_amount) - $prev_booking_amount;
	$cumalitive_precost_yarn_deing_amount = ($cumalitive_precost_yarn_deing_amount == "") ? 0 : $cumalitive_precost_yarn_deing_amount;

	//echo "10**test" . $cumalitive_precost_yarn_deing_amount;
	//die;

	if ($operation == 0 || $operation == 1) {
		if ((number_format($txt_amount, 2, ".", "")) > (number_format($cumalitive_precost_yarn_deing_amount, 2, ".", ""))) {
			$cumalitive_precost_yarn_deing_amount = number_format($cumalitive_precost_yarn_deing_amount, 2, ".", "");
			echo "23**Dyeing amount can not be greater than conversion cost amount\nCumalitive conversion cost amount=$cumalitive_precost_yarn_deing_amount";
			disconnect($con);
			die;
		}
	}

	// End  budget amount vs yarnd dyeing amount validation

	$stripe_info = sql_select("select a.stripe_color,a.color_number_id, (a.measurement || a.totfidder) as measurement, a.pre_cost_fabric_cost_dtls_id from wo_pre_stripe_color a where  a.job_no='" . $txt_job_no . "' and a.status_active=1");

	$total_measurement = array();
	$stripe_color_measurement = array();
	if (!empty($stripe_info)) {
		foreach ($stripe_info as $row) {
			$total_measurement[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]] += $row[csf('measurement')];
			$stripe_color_measurement[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('stripe_color')]][$row[csf('color_number_id')]] += $row[csf('measurement')];
		}
	}

	/* echo "10**<pre>";
	print_r($stripe_color_measurement);
	die; */

	$stripe_required_data = sql_select("select b.pre_cost_fabric_cost_dtls_id,b.gmts_color_id as color_number_id, sum(b.grey_fab_qnty) grey_fab_qnty from wo_booking_dtls b where b.job_no='" . $txt_job_no . "' and b.booking_no=$txt_fab_booking_no and b.status_active=1 and b.is_deleted=0 group by b.pre_cost_fabric_cost_dtls_id,b.gmts_color_id order by b.pre_cost_fabric_cost_dtls_id desc");

	$stripe_required = 0;
	$booking_stripe_required_qty = array();
	if (!empty($stripe_required_data)) {
		$booking_req_qty = 0;
		foreach ($stripe_required_data as $row) {
			$stripe_required_by_presoct_id[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]] = $row[csf('grey_fab_qnty')];
			$booking_req_qty += $row[csf('grey_fab_qnty')];
		}

		foreach ($stripe_required_data as $row) {
			$total_measurements = $total_measurement[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]];
			$total_color_measurement = $stripe_color_measurement[$row[csf('pre_cost_fabric_cost_dtls_id')]][$txt_yern_color][$row[csf('color_number_id')]];
			$stripe_required = $stripe_required_by_presoct_id[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]];
			$measurement_perc = ($total_color_measurement / $total_measurements) * 100;

			//echo $total_color_measurement . "==" . $total_measurements . "<br>";
			$booking_color_wise_stripe_required += ($measurement_perc / 100) * $stripe_required;
			$booking_color_wise_stripe_required = (is_nan($booking_color_wise_stripe_required)) ? 0 : $booking_color_wise_stripe_required;
			$booking_stripe_required_qty[$txt_yern_color][] = $booking_color_wise_stripe_required;
		}
	}

	$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=18 and item_category_id = 1");
	$auto_allocation_from_requisition_feature = return_field_value("auto_allocate_yarn_from_requis", " variable_settings_production", "company_name=$cbo_company_name and variable_list=6 and status_active=1 and is_deleted=0", "auto_allocate_yarn_from_requis");

	if ($variable_set_allocation == 1 && $auto_allocation_from_requisition_feature != 1) // Menual Allocation
	{
		$sql_allocation = sql_select("select a.item_id,a.job_no,b.yarn_count_id,b.yarn_comp_type1st,b.yarn_comp_percent1st,b.yarn_type,a.qnty as allocated_qnty from inv_material_allocation_mst a, product_details_master b where a.item_id=b.id and a.item_id=" . str_replace("'", "", $txt_pro_id) . " and a.job_no='$txt_job_no' and a.status_active=1 and a.is_deleted=0");

		foreach ($sql_allocation as $row) {
			$yarn_count_id = $row[csf('yarn_count_id')];
			$yarn_comp_type1st = $row[csf('yarn_comp_type1st')];
			$yarn_comp_percent1st = $row[csf('yarn_comp_percent1st')];
			$yarn_type = $row[csf('yarn_type')];
			$job_total_allocation_arr[$row[csf('job_no')]][$row[csf('item_id')]] += $row[csf('allocated_qnty')];
		}

		if ($yarn_count_id != "") {
			$count_id_cond = "and b.count=$yarn_count_id";
		}
		if ($yarn_comp_type1st != "") {
			$yarn_comp_type1st_cond = "and b.yarn_comp_type1st=$yarn_comp_type1st";
		}
		if ($yarn_comp_percent1st != "") {
			$yarn_comp_percent1st_cond = "and b.yarn_comp_percent1st=$yarn_comp_percent1st";
		}

		if ($db_type == 0) {
			$ydsw_sql = "select x.wo_num,x.job_no,x.product_id,sum(x.yarn_wo_qty) yarn_wo_qty from(select group_concat(distinct(a.yarn_dyeing_prefix_num)) as wo_num,b.job_no,b.product_id,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(41,42,114,135,94) and b.entry_form in(41,42,114,135,94) and b.job_no='$txt_job_no' and b.product_id=" . str_replace("'", "", $txt_pro_id) . " group by b.job_no,b.product_id
				union all
			select group_concat(distinct(a.yarn_dyeing_prefix_num)) as wo_num,b.job_no,b.product_id,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(125) and b.entry_form in(125) and b.job_no='$txt_job_no' $count_id_cond $yarn_comp_type1st_cond $yarn_comp_percent1st_cond group by b.job_no,b.product_id )x group by x.wo_num,x.job_no,x.product_id";
		} else {
			$ydsw_sql = "select x.wo_num,x.job_no,x.product_id,sum(x.yarn_wo_qty) yarn_wo_qty from(select LISTAGG(a.yarn_dyeing_prefix_num, ',') WITHIN GROUP (ORDER BY b.id) as wo_num,b.job_no,b.product_id,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(41,42,114,135,94) and b.entry_form in(41,42,114,135,94) and b.job_no='$txt_job_no' and b.product_id=" . str_replace("'", "", $txt_pro_id) . " group by b.job_no,b.product_id
			union all
			select LISTAGG(a.yarn_dyeing_prefix_num, ',') WITHIN GROUP (ORDER BY b.id) as wo_num,b.job_no,b.product_id,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(125) and b.entry_form in(125) and b.job_no='$txt_job_no' $count_id_cond $yarn_comp_type1st_cond $yarn_comp_percent1st_cond group by b.job_no,b.product_id )x group by x.wo_num,x.job_no,x.product_id";
		}

		$check_ydsw = sql_select($ydsw_sql);
		$wo_numbers = implode(",", array_unique(explode(",", $check_ydsw[0][csf("wo_num")])));
		$previous_ydsw_qty = $check_ydsw[0][csf('yarn_wo_qty')];

		$all_booking_no = '';
		$get_job_booking = sql_select("select a.booking_no from wo_booking_dtls a where a.job_no='$txt_job_no' and a.status_active=1 and a.is_deleted=0 group by  booking_no");
		foreach ($get_job_booking as $booking_row) {
			$all_booking_no .= "'" . $booking_row[csf('booking_no')] . "',";
		}

		$booking_nos = rtrim($all_booking_no, ',');

		if ($booking_nos != "") {
			if ($db_type == 0) {
				$all_knit_id = return_field_value("group_concat(distinct(b.id)) as knit_id", "ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b", "a.id=b.mst_id and a.booking_no in($booking_nos) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "knit_id");
			} else {
				$all_knit_id = return_field_value("LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as knit_id", "ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b", "a.id=b.mst_id and a.booking_no in($booking_nos) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "knit_id");
				$all_knit_id = implode(",", array_unique(explode(",", $all_knit_id)));
			}
		}

		if ($all_knit_id != "") {
			if ($db_type == 0) {
				$req_sql = "select a.booking_no,group_concat(distinct(c.requisition_no)) as requisition_no,c.prod_id,sum(c.yarn_qnty) as yarn_qnty from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c where a.id=b.mst_id and b.id=c.knit_id and b.id in ($all_knit_id) and c.prod_id=" . str_replace("'", "", $txt_pro_id) . " and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.booking_no,c.prod_id";
			} else {
				$req_sql = "select a.booking_no,LISTAGG(c.requisition_no, ',') WITHIN GROUP (ORDER BY c.requisition_no) as requisition_no,c.prod_id,sum(c.yarn_qnty) as yarn_qnty from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c where a.id=b.mst_id and b.id=c.knit_id and b.id in ($all_knit_id) and c.prod_id=" . str_replace("'", "", $txt_pro_id) . " and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.booking_no,c.prod_id";
			}
			//echo "10**".$req_sql; die();
			$req_result = sql_select($req_sql);
			$requisition_nos = $req_result[0][csf('requisition_no')];
			$previous_requsition_qty =  $req_result[0][csf('yarn_qnty')];
		}

		$txt_pro_id  = str_replace("'", "", $txt_pro_id);
		$txt_wo_qty  = str_replace("'", "", $txt_wo_qty);
		$hdn_wo_qty  = str_replace("'", "", $hdn_wo_qty);

		$job_total_allocation_qty = $job_total_allocation_arr[$txt_job_no][$txt_pro_id];
		//echo "10**".$job_total_allocation_qty."-".$previous_ydsw_qty."+".$previous_requsition_qty; die();
		if ($operation == 0) {
			$allocation_balance = ($job_total_allocation_qty - ($previous_ydsw_qty + $previous_requsition_qty));
			//if($txt_wo_qty>$allocation_balance)
			if (number_format($txt_wo_qty, 2, '.', '') > number_format($allocation_balance, 2, '.', '')) {
				echo "40**Work order QTY not available\nAllocation QTY=$job_total_allocation_qty\nWork Order No:$wo_numbers\nPrevious work order QTY=$previous_ydsw_qty\nRequsition No:$requisition_nos\nRequsition QTY=$previous_requsition_qty\nBalance=$allocation_balance";
				disconnect($con);
				die();
			}
		} else if ($operation == 1) {
			$allocation_balance = ($job_total_allocation_qty + $hdn_wo_qty - ($previous_ydsw_qty + $previous_requsition_qty));
			//if($txt_wo_qty>$allocation_balance)
			if (number_format($txt_wo_qty, 2, '.', '') > number_format($allocation_balance, 2, '.', '')) {
				echo "40**Work order QTY not available\nAllocation QTY=$job_total_allocation_qty\nWork Order No=$wo_numbers\nPrevious work order QTY=$previous_ydsw_qty\nRequsition No=$requisition_nos\nRequsition QTY=$previous_requsition_qty\nBalance=" . $allocation_balance;
				disconnect($con);
				die();
			}
		}
	}

	if ($operation == 0) {
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$txt_pro_id = str_replace("'", "", $txt_pro_id);
		$yern_color_id = str_replace("'", "", $txt_yern_color);

		$prev_booking = return_field_value("sum(yarn_wo_qty) as yarn_wo_qty", " wo_yarn_dyeing_dtls", "job_no='$txt_job_no' and fab_booking_no=$txt_fab_booking_no and yarn_color=$txt_yern_color and product_id=$txt_pro_id and status_active=1 and is_deleted=0 group by job_no, yarn_color", "yarn_wo_qty");

		$sql_select = "select sum(yarn_wo_qty) as yarn_wo_qty  from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.job_no='$txt_job_no' and b.yarn_color=$yern_color_id and b.product_id=$txt_pro_id and a.ydw_no=$txt_booking_no and a.entry_form=41 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
		$sql_result = sql_select($sql_select);
		$yarn_wo_qty_found = 0;
		$yarn_wo_qty_found = $sql_result[0][csf('yarn_wo_qty')];
		if ($yarn_wo_qty_found > 0) {
			echo "23**Work Order Entry Does Not Allow , Same As following Fabric,Lot,Color,Job No,WO No";
			disconnect($con);
			die;
		}

		$issue_return_qnty = return_field_value("sum(c.cons_quantity) as issue_return_qnty", " wo_yarn_dyeing_dtls a, inv_receive_master b, inv_transaction c ", " a.mst_id=b.booking_id and b.id=c.mst_id  and a.job_no='$txt_job_no' and a.yarn_color=$txt_yern_color and a.product_id=$txt_pro_id and b.entry_form=9 and b.item_category=1 and c.item_category=1 and c.transaction_type=4 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by a.job_no, a.yarn_color", "issue_return_qnty");

		$previous_wo_qnty = ($prev_booking - $issue_return_qnty);
		$wo_qnty = number_format(str_replace("'", "", $txt_wo_qty) + $previous_wo_qnty, 2, ".", "");
		$booking_color_wise_stripe_required = number_format(array_sum($booking_stripe_required_qty[$txt_yern_color]), 2, ".", "");

		if ($is_short == 2) {
			if (($wo_qnty * 1) > ($booking_color_wise_stripe_required * 1)) {
				echo "40**Work Order Quantity Does Not Allow More Then Fabric Required.\nFabric Required =  $booking_color_wise_stripe_required";
				disconnect($con);
				die;
			}
		}

		if ($variable_set_allocation == 1) // allocation system yes 
		{
			if ($variable_set_allocation == 1 && $auto_allocation_from_requisition_feature == 1) //allocation system yes with auto allocation with requsition 
			{
				$product_stock = return_field_value("available_qnty", "product_details_master", "id=$txt_pro_id");
			} else {
				$product_stock = return_field_value("allocated_qnty", "product_details_master", "id=$txt_pro_id");
			}
		} else {
			$product_stock = return_field_value("available_qnty", "product_details_master", "id=$txt_pro_id");
		}


		if ($variable_set_allocation == 1) // allocation system yes 
		{
			if (str_replace("'", "", $txt_wo_qty) > $product_stock) {
				echo "40**Allocated Qnty No Available.";
				disconnect($con);
				die;
			}
		} else {
			if (str_replace("'", "", $txt_wo_qty) > $product_stock) {
				echo "40**Available Qnty No Available.";
				disconnect($con);
				die;
			}
		}

		if (str_replace("'", "", $update_id) != "") //update
		{
			$id = return_field_value("id", " wo_yarn_dyeing_mst", "id=$update_id"); //check sys id for update or insert
			$field_array = "supplier_id*booking_date*delivery_date*delivery_date_end*dy_delivery_date_start*dy_delivery_date_end*currency*ecchange_rate*pay_mode*source*attention*tenor*is_short*budget_version*updated_by*update_date*status_active*is_deleted";
			$data_array = "" . $cbo_supplier_name . "*" . $txt_booking_date . "*" . $txt_delivery_date . "*" . $txt_delivery_end . "*" . $dy_delevery_start . "*" . $dy_delevery_end . "*" . $cbo_currency . "*" . $txt_exchange_rate . "*" . $cbo_pay_mode . "*" . $cbo_source . "*" . $txt_attention . "*" . $txt_tenor . "*" . $cbo_is_short . "*" . $cbo_budget_version . "*'" . $user_id . "'*'" . $pc_date_time . "'*1*0";
			$return_no = str_replace("'", '', $txt_booking_no);
		} else // new insert
		{
			$id = return_next_id_by_sequence("WO_YARN_DYEING_MST_YDW_PK_SEQ", "wo_yarn_dyeing_mst", $con);
			$new_sys_number = explode("*", return_next_id_by_sequence("WO_YARN_DYEING_MST_YDW_PK_SEQ", "wo_yarn_dyeing_mst", $con, 1, $cbo_company_name, 'YDW', 999, date("Y", time()), 0));

			$field_array = "id,yarn_dyeing_prefix,yarn_dyeing_prefix_num,ydw_no,entry_form,company_id,supplier_id,item_category_id,booking_date,delivery_date,delivery_date_end,dy_delivery_date_start,dy_delivery_date_end,currency,ecchange_rate,pay_mode,source,attention,tenor,is_short,budget_version,ready_to_approved,booking_without_order,inserted_by,insert_date,status_active,is_deleted";
			$data_array = "(" . $id . ",'" . $new_sys_number[1] . "','" . $new_sys_number[2] . "','" . $new_sys_number[0] . "',41," . $cbo_company_name . "," . $cbo_supplier_name . "," . $cbo_item_category_id . "," . $txt_booking_date . "," . $txt_delivery_date . "," . $txt_delivery_end . "," . $dy_delevery_start . "," . $dy_delevery_end . "," . $cbo_currency . "," . $txt_exchange_rate . "," . $cbo_pay_mode . "," . $cbo_source . "," . $txt_attention . "," . $txt_tenor . "," . $cbo_is_short . "," . $cbo_budget_version . "," . $cbo_ready_to_approved . ",0,'" . $user_id . "','" . $pc_date_time . "',1,0)";
			// inv_gate_in_mst master table entry here END---------------------------------------//
			$return_no = str_replace("'", '', $new_sys_number[0]);
		}

		//for transaction log
		$log_entry_form = 41;
		$log_ref_id = $id;
		$log_ref_number = $return_no;

		$dtlsid = return_next_id("id", "wo_yarn_dyeing_dtls", 1);
		$field_array_dts = "id,mst_id,job_no,fab_booking_no,product_id,job_no_id,entry_form,count,yarn_description,yarn_color,color_range,uom,yarn_wo_qty,dyeing_charge,amount,no_of_bag,no_of_cone,min_require_cone,file_no,internal_ref_no,remarks,referance_no,status_active,is_deleted";
		$data_array_dts = "(" . $dtlsid . "," . $id . ",'" . $txt_job_no . "'," . $txt_fab_booking_no . ",'" . $txt_pro_id . "'," . $txt_job_id . ",41,'" . $cbo_count . "','" . $txt_item_des . "'," . $txt_yern_color . ",'" . $cbo_color_range . "'," . $cbo_uom . "," . $txt_wo_qty . "," . $txt_dyeing_charge . "," . $txt_amount . "," . $txt_bag . "," . $txt_cone . "," . $txt_min_req_cone . "," . $txt_file_no . "," . $txt_int_ref_no . "," . $txt_remarks . ",'" . $txt_ref_no . "',1,0)";

		if ($variable_set_allocation == 1 && $auto_allocation_from_requisition_feature == 1) {
			$sql_allocation = "select a.id from inv_material_allocation_mst a where a.item_id=" . str_replace("'", "", $txt_pro_id) . " and a.job_no='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and a.entry_form=41";
			$check_allocation_array = sql_select($sql_allocation);

			// if allocation found
			if (!empty($check_allocation_array)) {
				$mst_id = $check_allocation_array[0][csf('id')];
				$rID3 = execute_query("update inv_material_allocation_dtls set qnty=(qnty+$txt_wo_qty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and job_no='$txt_job_no' and item_id=$txt_pro_id", 0);

				$rID2 = execute_query("update inv_material_allocation_mst set qnty=(qnty+$txt_wo_qty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id and entry_form=41", 0);
			} else {
				$id_allocation = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
				$field_array_allocation_mst = "id,mst_id,entry_form,job_no,allocation_date,item_id,qnty,inserted_by,insert_date";
				$data_array_allocation_mst = "(" . $id_allocation . "," . $id . ",41,'" . $txt_job_no . "'," . $txt_booking_date . "," . $txt_pro_id . "," . $txt_wo_qty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$id_allocation_dtls = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
				$field_array_allocation_dtls = "id,mst_id,job_no,allocation_date,item_id,qnty,inserted_by,insert_date";
				$data_array_allocation_dtls = "(" . $id_allocation_dtls . "," . $id_allocation . ",'" . $txt_job_no . "'," . $txt_booking_date . "," . $txt_pro_id . "," . $txt_wo_qty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				//echo "10**";
				//echo "INSERT INTO inv_material_allocation_mst (".$field_array_allocation_mst.") VALUES ".$data_array_allocation_mst;die;
				$rID2 = sql_insert("inv_material_allocation_mst", $field_array_allocation_mst, $data_array_allocation_mst, 0);
				$rID3 = true;
				if ($data_array_allocation_dtls != '') {
					$rID3 = sql_insert("inv_material_allocation_dtls", $field_array_allocation_dtls, $data_array_allocation_dtls, 0);
				}
			}

			$prod_id = str_replace("'", "", $txt_pro_id);
			$txt_wo_qty = str_replace("'", "", $txt_wo_qty);
			$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$txt_wo_qty) where id=$prod_id", 0);
			$rID_adjal = execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty),update_date='" . $pc_date_time . "' where id=$prod_id  ", 0);

			//for transaction log
			$sql_prod = sql_select("SELECT CURRENT_STOCK, ALLOCATED_QNTY, AVAILABLE_QNTY, DYED_TYPE FROM PRODUCT_DETAILS_MASTER WHERE ID = " . $prod_id);
			foreach ($sql_prod as $row) {
				$log_prod_id = $prod_id;
				$log_current_stock = $row['CURRENT_STOCK'];
				$log_allocated_qty = $row['ALLOCATED_QNTY'];
				$log_available_qty = $row['AVAILABLE_QNTY'];
				$log_dyed_type = $row['DYED_TYPE'];
			}
		} else {
			$rID3 = $rID2 = $rID4 = $rID_adjal = true;
		}

		if (str_replace("'", "", $update_id) != "") //update
		{
			$rID = sql_update("wo_yarn_dyeing_mst", $field_array, $data_array, "id", $id, 1);
		} else {
			$rID = sql_insert("wo_yarn_dyeing_mst", $field_array, $data_array, 0);
		}

		$dtlsrID = sql_insert("wo_yarn_dyeing_dtls", $field_array_dts, $data_array_dts, 1);
		//echo "10**".$rID ."**". $dtlsrID ."**". $rID3 ."**". $rID2 ."**". $rID4 ."**". $rID_adjal;die;

		if ($db_type == 0) {
			if ($rID && $dtlsrID && $rID3 && $rID2 && $rID4 && $rID_adjal) {
				mysql_query("COMMIT");
				echo "0**" . str_replace("'", '', $return_no) . "**" . str_replace("'", '', $id);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", '', $return_no) . "**" . str_replace("'", '', $id);
			}
		}

		if ($db_type == 1 || $db_type == 2) {
			if ($rID && $dtlsrID && $rID3 && $rID2 && $rID4 && $rID_adjal) {
				//for transaction log
				$log_data = array();
				$log_data['entry_form'] = $log_entry_form;
				$log_data['ref_id'] = $log_ref_id;
				$log_data['ref_number'] = $log_ref_number;
				$log_data['product_id'] = $log_prod_id;
				$log_data['current_stock'] = $log_current_stock;
				$log_data['allocated_qty'] = $log_allocated_qty;
				$log_data['available_qty'] = $log_available_qty;
				$log_data['dyed_type'] = $log_dyed_type;
				$log_data['insert_date'] = $pc_date_time;
				manage_allocation_transaction_log($log_data);
				//end for transaction log

				oci_commit($con);
				echo "0**" . str_replace("'", '', $return_no) . "**" . str_replace("'", '', $id);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", '', $return_no) . "**" . str_replace("'", '', $id);
			}
		}

		disconnect($con);
		die;
	} else if ($operation == 1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$dtls_update_id = str_replace("'", "", $dtls_update_id);

		//check update id
		if (str_replace("'", "", $update_id) == "") {
			echo "15";
			disconnect($con);
			exit();
		}

		$is_approved = return_field_value("is_approved", "wo_yarn_dyeing_mst", "ydw_no=$txt_booking_no");
		if ($is_approved == 3) {
			$is_approved = 1;
		}
		if ($is_approved == 1) {
			echo "approve**" . str_replace("'", "", $txt_booking_no);
			disconnect($con);
			die;
		}
		$txt_pro_id = str_replace("'", "", $txt_pro_id);

		$prev_booking = return_field_value("sum(yarn_wo_qty) as yarn_wo_qty", " wo_yarn_dyeing_dtls", "job_no='$txt_job_no' and fab_booking_no=$txt_fab_booking_no and yarn_color=$txt_yern_color and product_id=$txt_pro_id and status_active=1 and is_deleted=0 group by job_no, yarn_color", "yarn_wo_qty");

		$issue_return_qnty = return_field_value("sum(c.cons_quantity) as issue_return_qnty", " wo_yarn_dyeing_dtls a, inv_receive_master b, inv_transaction c ", " a.mst_id=b.booking_id and b.id=c.mst_id  and a.job_no='$txt_job_no' and a.yarn_color=$txt_yern_color  and a.product_id=$txt_pro_id and b.entry_form=9 and b.item_category=1 and c.item_category=1 and c.transaction_type=4 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by a.job_no, a.yarn_color", "issue_return_qnty");

		$previous_wo_qnty = ($prev_booking - $issue_return_qnty);
		$hdn_wo_qty = str_replace("'", "", $hdn_wo_qty) * 1;
		$txt_wo_qty = str_replace("'", "", $txt_wo_qty) * 1;
		$wo_qnty = number_format($txt_wo_qty + ($previous_wo_qnty - $hdn_wo_qty), 2, ".", "");
		$booking_color_wise_stripe_required = number_format(array_sum($booking_stripe_required_qty[$txt_yern_color]), 2, ".", "");

		if ($is_short == 2) {
			if (($wo_qnty * 1) > ($booking_color_wise_stripe_required * 1)) {
				echo "40**Work Order Quantity Does Not Allow More Then Fabric Required\n$wo_qnty\n$booking_color_wise_stripe_required";
				disconnect($con);
				die;
			}
		}

		$all_issue_qty = return_field_value("sum(c.cons_quantity) as issue_qnty", " inv_issue_master b, inv_transaction c ", "b.id=c.mst_id and b.entry_form=3 and b.buyer_job_no='$txt_job_no' and b.booking_no=$txt_booking_no and c.prod_id=$txt_pro_id and c.dyeing_color_id=$txt_yern_color and b.item_category=1 and c.item_category=1 and c.transaction_type=2 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by b.buyer_job_no", "issue_qnty");

		$total_issue_return_qty = return_field_value("sum(c.cons_quantity) as issue_ret_qnty", " inv_receive_master b, inv_transaction c ", "b.id=c.mst_id and b.entry_form=9 and b.booking_no=$txt_booking_no and c.prod_id=$txt_pro_id and c.dyeing_color_id=$txt_yern_color and b.item_category=1 and c.item_category=1 and c.transaction_type=4 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by b.booking_no", "issue_ret_qnty");

		//echo "13**".$all_issue_qty."===".$total_issue_return_qty; die;
		//$update_wo_qty = ($txt_wo_qty + ($hdn_wo_qty-$txt_wo_qty));
		$balance_issue_qty = number_format($all_issue_qty - $total_issue_return_qty, 2, '.', '');
		$wo_qty = number_format($txt_wo_qty, 2, '.', '');

		if ($wo_qty < $balance_issue_qty) {
			echo "13**Work order quantity can not be less than issue balance quantity.\nIssue balance quantity ** " . $balance_issue_qty;
			disconnect($con);
			die;
		}

		if ($db_type == 0) {
			$sql_rcv = sql_select("select group_concat(distinct(b.recv_number)) as RECV_NUMBER,c.dyeing_color_id as DYEING_COLOR_ID,d.DYEING_CHARGE, sum(c.cons_quantity) as RCV_QTY from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls d, inv_receive_master b, inv_transaction c where a.id=d.mst_id and a.id=$update_id and d.id=$dtls_update_id and b.booking_id = a.id and b.id = c.mst_id and a.ydw_no = $txt_booking_no and b.receive_basis = 2 and b.receive_purpose = 2 and b.entry_form = 1 and c.dyeing_color_id = $txt_yern_color and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and b.item_category = 1 and c.item_category = 1 group by c.dyeing_color_id,d.dyeing_charge");
		} else {
			$sql_rcv = sql_select("select LISTAGG(b.recv_number, ',') WITHIN GROUP (ORDER BY b.recv_number) as RECV_NUMBER,c.dyeing_color_id as DYEING_COLOR_ID,d.DYEING_CHARGE, sum(c.cons_quantity) as RCV_QTY from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls d, inv_receive_master b, inv_transaction c where a.id=d.mst_id and a.id=$update_id and d.id=$dtls_update_id and b.booking_id = a.id and b.id = c.mst_id and a.ydw_no = $txt_booking_no and b.receive_basis = 2 and b.receive_purpose = 2 and b.entry_form = 1 and c.dyeing_color_id = $txt_yern_color and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and b.item_category = 1 and c.item_category = 1 group by c.dyeing_color_id,d.dyeing_charge");
		}

		$rcv_qty = number_format($sql_rcv[0]['RCV_QTY'], 2, '.', '');
		$rcv_numbers = $sql_rcv[0]['RECV_NUMBER'];
		$prev_dyeing_charge = $sql_rcv[0]['DYEING_CHARGE'];

		if ($rcv_numbers != "" && ($prev_dyeing_charge != str_replace("'", "", $txt_dyeing_charge))) {
			echo "13**Receive Found\nDyeing charge can not be change\nReceived Quantity=$rcv_qty\nReceived Number's" . "**" . $rcv_numbers;
			disconnect($con);
			die;
		} else if ($wo_qty < $rcv_qty) {
			echo "13**Receive Found\nWorke order quantity can not be less than received quantity\nReceived Quantity=$rcv_qty\nReceived Number's" . "**" . $rcv_numbers;
			disconnect($con);
			die;
		}

		if ($variable_set_allocation == 1) // allocation system yes 
		{
			if ($variable_set_allocation == 1 && $auto_allocation_from_requisition_feature == 1) // allocation system yes auto allocation 
			{
				$product_stock = return_field_value("available_qnty", "product_details_master", "id=$txt_pro_id") + $hdn_wo_qty;
			} else {
				$product_stock = return_field_value("allocated_qnty", "product_details_master", "id=$txt_pro_id") + $hdn_wo_qty;
			}
		} else {
			$product_stock = return_field_value("available_qnty", "product_details_master", "id=$txt_pro_id") + $hdn_wo_qty;
		}

		if (str_replace("'", "", $txt_wo_qty) > $product_stock) {
			echo "40**Yarn Wo. Qnty No Available.";
			disconnect($con);
			die;
		}

		//wo_yarn_dyeing_mst master table UPDATE here START----------------------//	".$txt_pro_id.",
		$field_array = "supplier_id*booking_date*delivery_date*delivery_date_end*dy_delivery_date_start*dy_delivery_date_end*currency*ecchange_rate*pay_mode*source*attention*tenor*is_short*budget_version*ready_to_approved*updated_by*update_date*status_active*is_deleted";
		$data_array = "" . $cbo_supplier_name . "*" . $txt_booking_date . "*" . $txt_delivery_date . "*" . $txt_delivery_end . "*" . $dy_delevery_start . "*" . $dy_delevery_end . "*" . $cbo_currency . "*" . $txt_exchange_rate . "*" . $cbo_pay_mode . "*" . $cbo_source . "*" . $txt_attention . "*" . $txt_tenor . "*" . $cbo_is_short . "*" . $cbo_budget_version . "*" . $cbo_ready_to_approved . "*'" . $user_id . "'*'" . $pc_date_time . "'*1*0";

		$field_array_dtls = "job_no*fab_booking_no*product_id*job_no_id*count*yarn_description*yarn_color*color_range*uom*yarn_wo_qty*dyeing_charge*amount*no_of_bag*no_of_cone*min_require_cone*file_no*internal_ref_no*remarks*referance_no";
		$data_array_dtls = "'" . $txt_job_no . "'*" . $txt_fab_booking_no . "*'" . $txt_pro_id . "'*" . $txt_job_id . "*" . $cbo_count . "*'" . $txt_item_des . "'*" . $txt_yern_color . "*" . $cbo_color_range . "*" . $cbo_uom . "*" . $txt_wo_qty . "*" . $txt_dyeing_charge . "*" . $txt_amount . "*" . $txt_bag . "*" . $txt_cone . "*" . $txt_min_req_cone . "*" . $txt_file_no . "*" . $txt_int_ref_no . "*" . $txt_remarks . "*'" . $txt_ref_no . "'";

		//for transaction log
		$log_entry_form = 41;
		$log_ref_id = str_replace("'", "", $update_id);
		$log_ref_number = str_replace("'", "", $txt_booking_no);

		if ($variable_set_allocation == 1 && $auto_allocation_from_requisition_feature == 1) {
			$txt_wo_qty = str_replace("'", "", $txt_wo_qty);

			// IF USER CHANGE LOT WHILE UPDATE
			if (str_replace("'", "", $txt_pro_id) != str_replace("'", "", $hdn_pre_prod_id)) {
				// DECREASE PREVIOUS LOT ALLOCATION START
				$sql_allocation = "select a.id from inv_material_allocation_mst a where a.item_id=" . str_replace("'", "", $hdn_pre_prod_id) . " and a.job_no='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and a.entry_form=41";
				$check_allocation_array = sql_select($sql_allocation);

				$hdn_wo_qty = str_replace("'", "", $hdn_wo_qty);
				// if allocation found
				if (!empty($check_allocation_array)) {
					$mst_id = $check_allocation_array[0][csf('id')];
					$rID3 = execute_query("update inv_material_allocation_dtls set qnty=(qnty-$hdn_wo_qty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and job_no='$txt_job_no' and item_id=$hdn_pre_prod_id", 0);

					$rID2 = execute_query("update inv_material_allocation_mst set qnty=(qnty-$hdn_wo_qty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id and entry_form=41", 0);
				}

				// update previous product global allocation
				$prod_id = str_replace("'", "", $hdn_pre_prod_id);
				$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty-$hdn_wo_qty) where id=$prod_id", 0);
				$rID_adjal = execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty),update_date='" . $pc_date_time . "' where id=$prod_id  ", 0);
				//DECREASE PREVIOUS LOT ALLOCATION END

				//for transaction log
				$sql_prod = sql_select("SELECT CURRENT_STOCK, ALLOCATED_QNTY, AVAILABLE_QNTY, DYED_TYPE FROM PRODUCT_DETAILS_MASTER WHERE ID = " . $prod_id);
				foreach ($sql_prod as $row) {
					$log_prod_id = $prod_id;
					$log_current_stock = $row['CURRENT_STOCK'];
					$log_allocated_qty = $row['ALLOCATED_QNTY'];
					$log_available_qty = $row['AVAILABLE_QNTY'];
					$log_dyed_type = $row['DYED_TYPE'];
				}

				//NEW LOT ALLOCATION PROCESS START
				$new_prod_id = str_replace("'", "", $txt_pro_id);
				$sql_allocation = "select a.id from inv_material_allocation_mst a where a.item_id=$new_prod_id and a.job_no='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and a.entry_form=41";
				$check_allocation_array = sql_select($sql_allocation);

				// if allocation found
				if (!empty($check_allocation_array)) {

					$mst_id = $check_allocation_array[0][csf('id')];
					//$txt_wo_qty = str_replace("'","",$txt_wo_qty);
					$new_allocation_qnty = str_replace("'", "", $txt_wo_qty);
					$rID3 = execute_query("update inv_material_allocation_dtls set qnty=(qnty+$new_allocation_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and job_no='$txt_job_no' and item_id=$new_prod_id", 0);

					$rID2 = execute_query("update inv_material_allocation_mst set qnty=(qnty+$new_allocation_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id and entry_form=41", 0);
				} else {
					$new_allocation_qnty = str_replace("'", "", $txt_wo_qty);
					$id_allocation = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
					$field_array_allocation_mst = "id,mst_id,entry_form,job_no,allocation_date,item_id,qnty,inserted_by,insert_date";
					$data_array_allocation_mst = "(" . $id_allocation . "," . $update_id . ",41,'" . $txt_job_no . "'," . $txt_booking_date . "," . $new_prod_id . "," . $new_allocation_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$id_allocation_dtls = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
					$field_array_allocation_dtls = "id,mst_id,job_no,allocation_date,item_id,qnty,inserted_by,insert_date";
					$data_array_allocation_dtls = "(" . $id_allocation_dtls . "," . $id_allocation . ",'" . $txt_job_no . "'," . $txt_booking_date . "," . $new_prod_id . "," . $new_allocation_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$rID2 = sql_insert("inv_material_allocation_mst", $field_array_allocation_mst, $data_array_allocation_mst, 0);
					$rID3 = true;
					if ($data_array_allocation_dtls != '') {
						$rID3 = sql_insert("inv_material_allocation_dtls", $field_array_allocation_dtls, $data_array_allocation_dtls, 0);
					}
				}

				$rID5 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$new_allocation_qnty) where id=$new_prod_id", 0);
				$rID_adjal_new = execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty),update_date='" . $pc_date_time . "' where id=$new_prod_id  ", 0);
				//NEW LOT ALLOCATION PROCESS END

				//for transaction log
				$sql_prod = sql_select("SELECT CURRENT_STOCK, ALLOCATED_QNTY, AVAILABLE_QNTY, DYED_TYPE FROM PRODUCT_DETAILS_MASTER WHERE ID = " . $new_prod_id);
				foreach ($sql_prod as $row) {
					$log_prod_id1 = $new_prod_id;
					$log_current_stock1 = $row['CURRENT_STOCK'];
					$log_allocated_qty1 = $row['ALLOCATED_QNTY'];
					$log_available_qty1 = $row['AVAILABLE_QNTY'];
					$log_dyed_type1 = $row['DYED_TYPE'];
				}
			} else {
				$sql_allocation = "select a.* from inv_material_allocation_mst a where a.item_id=" . str_replace("'", "", $txt_pro_id) . " and a.job_no='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and a.entry_form=41";
				$check_allocation_array = sql_select($sql_allocation);

				// if allocation found
				if (!empty($check_allocation_array)) {
					$mst_id = $check_allocation_array[0][csf('id')];
					$txt_wo_qty = str_replace("'", "", $txt_wo_qty);
					$hdn_wo_qty = str_replace("'", "", $hdn_wo_qty);
					$allocation_qnty = ($txt_wo_qty - $hdn_wo_qty);
					$rID3 = execute_query("update inv_material_allocation_dtls set qnty=(qnty+$allocation_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and job_no='$txt_job_no' and item_id=$txt_pro_id", 0);

					$rID2 = execute_query("update inv_material_allocation_mst set qnty=(qnty+$allocation_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id and entry_form=41", 0);
				} else {
					$allocation_qnty = str_replace("'", "", $txt_wo_qty);
					$id_allocation = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
					$field_array_allocation_mst = "id,mst_id,entry_form,job_no,allocation_date,item_id,qnty,inserted_by,insert_date";
					$data_array_allocation_mst = "(" . $id_allocation . "," . $update_id . ",41,'" . $txt_job_no . "'," . $txt_booking_date . "," . $txt_pro_id . "," . $txt_wo_qty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$id_allocation_dtls = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
					$field_array_allocation_dtls = "id,mst_id,job_no,allocation_date,item_id,qnty,inserted_by,insert_date";
					$data_array_allocation_dtls = "(" . $id_allocation_dtls . "," . $id_allocation . ",'" . $txt_job_no . "'," . $txt_booking_date . "," . $txt_pro_id . "," . $txt_wo_qty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$rID2 = sql_insert("inv_material_allocation_mst", $field_array_allocation_mst, $data_array_allocation_mst, 0);

					$rID3 = true;
					if ($data_array_allocation_dtls != '') {
						$rID3 = sql_insert("inv_material_allocation_dtls", $field_array_allocation_dtls, $data_array_allocation_dtls, 0);
					}
				}

				$prod_id = str_replace("'", "", $txt_pro_id);
				$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$allocation_qnty) where id=$prod_id", 0);
				$rID_adjal = execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty),update_date='" . $pc_date_time . "' where id=$prod_id  ", 0);

				//for transaction log
				$sql_prod = sql_select("SELECT CURRENT_STOCK, ALLOCATED_QNTY, AVAILABLE_QNTY, DYED_TYPE FROM PRODUCT_DETAILS_MASTER WHERE ID = " . $prod_id);
				foreach ($sql_prod as $row) {
					$log_prod_id = $prod_id;
					$log_current_stock = $row['CURRENT_STOCK'];
					$log_allocated_qty = $row['ALLOCATED_QNTY'];
					$log_available_qty = $row['AVAILABLE_QNTY'];
					$log_dyed_type = $row['DYED_TYPE'];
				}

				$rID_adjal_new = $rID5 = true;
			}
		} else {

			$rID3 = $rID2 = $rID4 = $rID_adjal = $rID_adjal_new = $rID5 = true;
		}

		$rID = sql_update("wo_yarn_dyeing_mst", $field_array, $data_array, "id", $update_id, 1);
		$dtlsrID = sql_update("wo_yarn_dyeing_dtls", $field_array_dtls, $data_array_dtls, "id", $dtls_update_id, 1);

		//echo "10**insert into inv_material_allocation_mst (".$field_array_allocation_mst.") values ".$data_array_allocation_mst;die;

		//echo "10**".$rID ."**". $dtlsrID ."**". $rID3 ."**". $rID2 ."**". $rID4 ."**". $rID_adjal ."**". $rID_adjal_new ."**". $rID5;die;
		//echo "10**".$variable_set_allocation ."**". $auto_allocation_from_requisition_feature;die;

		$return_no = str_replace("'", '', $txt_booking_no);
		if ($db_type == 0) {
			if ($rID && $dtlsrID && $rID3 && $rID2 && $rID4 && $rID_adjal && $rID_adjal_new && $rID5) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", '', $return_no) . "**" . str_replace("'", '', $update_id);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", '', $return_no) . "**" . str_replace("'", '', $update_id);
			}
		} else if ($db_type == 2) {
			if ($rID && $dtlsrID && $rID3 && $rID2 && $rID4 && $rID_adjal && $rID_adjal_new && $rID5) {
				//for transaction log
				$log_data = array();
				$log_data['entry_form'] = $log_entry_form;
				$log_data['ref_id'] = $log_ref_id;
				$log_data['ref_number'] = $log_ref_number;
				$log_data['product_id'] = $log_prod_id;
				$log_data['current_stock'] = $log_current_stock;
				$log_data['allocated_qty'] = $log_allocated_qty;
				$log_data['available_qty'] = $log_available_qty;
				$log_data['dyed_type'] = $log_dyed_type;
				$log_data['insert_date'] = $pc_date_time;
				manage_allocation_transaction_log($log_data);
				//end for transaction log

				if (str_replace("'", "", $txt_pro_id) != str_replace("'", "", $hdn_pre_prod_id)) {
					//for transaction log
					$log_data = array();
					$log_data['entry_form'] = $log_entry_form;
					$log_data['ref_id'] = $log_ref_id;
					$log_data['ref_number'] = $log_ref_number;
					$log_data['product_id'] = $log_prod_id1;
					$log_data['current_stock'] = $log_current_stock1;
					$log_data['allocated_qty'] = $log_allocated_qty1;
					$log_data['available_qty'] = $log_available_qty1;
					$log_data['dyed_type'] = $log_dyed_type1;
					$log_data['insert_date'] = $pc_date_time1;
					manage_allocation_transaction_log($log_data);
					//end for transaction log
				}

				oci_commit($con);
				echo "1**" . str_replace("'", '', $return_no) . "**" . str_replace("'", '', $update_id);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", '', $return_no) . "**" . str_replace("'", '', $update_id);
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 2) // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$update_id = str_replace("'", "", $update_id);
		$dtls_update_id = str_replace("'", "", $dtls_update_id);
		$prod_id = str_replace("'", "", $txt_pro_id);
		$is_approved = return_field_value("is_approved", "wo_yarn_dyeing_mst", "ydw_no=$txt_booking_no");
		if ($is_approved == 3) {
			$is_approved = 1;
		}
		if ($is_approved == 1) {
			echo "approve**" . str_replace("'", "", $txt_booking_no);
			disconnect($con);
			die;
		}

		//and b.buyer_job_no='$txt_job_no' and b.booking_no=$txt_booking_no and c.dyeing_color_id=$txt_yern_color and c.prod_id=$txt_pro_id
		$check_issue = return_field_value("issue_number", "inv_issue_master a, inv_transaction b", "a.booking_no=$txt_booking_no and a.buyer_job_no='$txt_job_no' and b.prod_id=$prod_id and b.dyeing_color_id=$txt_yern_color  and a.id=b.mst_id and a.item_category=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		if ($check_issue != "") {
			echo "13**Issue Found,Delete not allowed.\nIssue No ** " . $check_issue;
			disconnect($con);
			die;
		}

		$recv_number = return_field_value("recv_number", "wo_yarn_dyeing_mst a,inv_receive_master b, inv_transaction c", " b.booking_id=a.id and b.id=c.mst_id and a.ydw_no=$txt_booking_no and b.receive_basis=2 and b.receive_purpose=2 and b.entry_form=1 and c.dyeing_color_id=$txt_yern_color and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.item_category=1 and c.item_category=1 and c.transaction_type=1 group by recv_number");

		if ($recv_number != "") {
			echo "13**Receive Found,Update not allowed." . "**" . $recv_number;
			disconnect($con);
			die;
		}
		$txt_booking_no = str_replace("'", "", $txt_booking_no);

		// master table delete here---------------------------------------
		if ($update_id == "" || $update_id == 0) {
			echo "15**0";
			disconnect($con);
			die;
		}

		//$rID = sql_update("wo_non_order_info_mst",'status_active*is_deleted','0*1',"id",$mst_id,1);
		$dtlsrID = sql_update("wo_yarn_dyeing_dtls", 'status_active*is_deleted', '0*1', "id", $dtls_update_id, 1);

		if ($variable_set_allocation == 1 && $auto_allocation_from_requisition_feature == 1) {
			$sql_allocation = "select a.* from inv_material_allocation_mst a where a.item_id=" . str_replace("'", "", $txt_pro_id) . " and a.job_no='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and a.entry_form=41";
			$check_allocation_array = sql_select($sql_allocation);
			$hdn_wo_qty = str_replace("'", "", $hdn_wo_qty);

			// if allocation found
			if (!empty($check_allocation_array)) {
				$mst_id = $check_allocation_array[0][csf('id')];
				$rID3 = execute_query("update inv_material_allocation_dtls set qnty=(qnty-$hdn_wo_qty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and job_no='$txt_job_no' and item_id=$txt_pro_id", 0);

				$rID2 = execute_query("update inv_material_allocation_mst set qnty=(qnty-$hdn_wo_qty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id and entry_form=41", 0);
			}

			$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty-$hdn_wo_qty) where id=$prod_id", 0);
			$rID_adjal = execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty),update_date='" . $pc_date_time . "' where id=$prod_id  ", 0);

			//for transaction log
			$sql_prod = sql_select("SELECT CURRENT_STOCK, ALLOCATED_QNTY, AVAILABLE_QNTY, DYED_TYPE FROM PRODUCT_DETAILS_MASTER WHERE ID = " . $prod_id);
			foreach ($sql_prod as $row) {
				$log_entry_form = 41;
				$log_ref_id = str_replace("'", "", $update_id);
				$log_ref_number = str_replace("'", "", $txt_booking_no);
				$log_prod_id = $prod_id;
				$log_current_stock = $row['CURRENT_STOCK'];
				$log_allocated_qty = $row['ALLOCATED_QNTY'];
				$log_available_qty = $row['AVAILABLE_QNTY'];
				$log_dyed_type = $row['DYED_TYPE'];
			}
		} else {
			$rID3 = $rID2 = $rID4 = $rID_adjal = true;
		}

		//echo "10**".$dtlsrID ."&&". $rID3 ."&&". $rID2 ."&&". $rID4 ."&&". $rID_adjal;die;
		if ($db_type == 0) {
			if ($dtlsrID && $rID3 && $rID2 && $rID4 && $rID_adjal) {
				mysql_query("COMMIT");
				echo "2**" . $txt_booking_no . "**" . $update_id;
			} else {
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		if ($db_type == 2 || $db_type == 1) {
			if ($dtlsrID) {
				//for transaction log
				$log_data = array();
				$log_data['entry_form'] = $log_entry_form;
				$log_data['ref_id'] = $log_ref_id;
				$log_data['ref_number'] = $log_ref_number;
				$log_data['product_id'] = $log_prod_id;
				$log_data['current_stock'] = $log_current_stock;
				$log_data['allocated_qty'] = $log_allocated_qty;
				$log_data['available_qty'] = $log_available_qty;
				$log_data['dyed_type'] = $log_dyed_type;
				$log_data['insert_date'] = $pc_date_time;
				manage_allocation_transaction_log($log_data);
				//end for transaction log

				oci_commit($con);
				echo "2**" . $txt_booking_no . "**" . $update_id;
			} else {
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "show_dtls_list_view") {
	if ($db_type == 0) {
		$sql = "select a.id, a.job_no, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.referance_no, group_concat(b.file_no) as file_no, group_concat(b.grouping) as internal_ref_no from wo_yarn_dyeing_dtls a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.mst_id='$data' group by a.id, a.job_no, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.referance_no";
	} else {
		$sql = "select a.id, a.job_no, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.referance_no, listagg(cast(b.file_no as varchar(4000)), ',') within group(order by b.file_no) as file_no, listagg(cast(b.grouping as varchar(4000)), ',') within group(order by b.grouping) as internal_ref_no from wo_yarn_dyeing_dtls a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.mst_id='$data' group by a.id, a.job_no, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.referance_no";
	}
	$sql_result = sql_select($sql);

	if (count($sql_result) > 0) {
	?>
		<table width="1200" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			<thead>
				<tr>
					<th width="40">SL</th>
					<th width="80">Job No</th>
					<th width="60">Count</th>
					<th width="150">Description</th>
					<th width="100">Color</th>
					<th width="60">UOM</th>
					<th width="70">WO QTY</th>
					<th width="70">Charge</th>
					<th width="80">Amount</th>
					<th width="60">No of Bag</th>
					<th width="60">No of Cone</th>
					<th width="70">Minimum Require Cone</th>
					<th width="90">Ref NO</th>
					<th width="90">File No</th>
					<th>Internal Ref. No</th>
				</tr>
			</thead>
			<tbody>
				<?
				$i = 1;

				foreach ($sql_result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;" onClick="get_php_form_data(<? echo $row[csf("id")]; ?>, 'child_form_input_data', 'requires/yarn_dyeing_charge_booking_controller' );">
						<td>
							<p><? echo $i; ?></p>
						</td>
						<td>
							<p><? echo $row[csf("job_no")]; ?>&nbsp;</p>
						</td>
						<td>
							<p><? echo $count_arr[$row[csf("count")]]; ?>&nbsp;</p>
						</td>
						<td>
							<p><? echo $row[csf("yarn_description")]; ?>&nbsp;</p>
						</td>
						<td>
							<p><? echo $color_arr[$row[csf("yarn_color")]]; ?>&nbsp;</p>
						</td>
						<td align="center">
							<p><? echo $unit_of_measurement[$row[csf("uom")]]; ?>&nbsp;</p>
						</td>
						<td align="right"><? echo number_format($row[csf("yarn_wo_qty")], 0); ?></td>
						<td align="right"><? echo number_format($row[csf("dyeing_charge")], 2); ?></td>
						<td align="right"><? echo number_format($row[csf("amount")], 2); ?></td>
						<td align="center">
							<p><? echo $row[csf("no_of_bag")]; ?>&nbsp;</p>
						</td>
						<td align="center">
							<p><? echo $row[csf("no_of_cone")]; ?>&nbsp;</p>
						</td>
						<td align="center">
							<p><? echo $row[csf("min_require_cone")]; ?>&nbsp;</p>
						</td>
						<td align="center">
							<p><? echo $row[csf("referance_no")]; ?>&nbsp;</p>
						</td>
						<td align="center">
							<p><? echo implode(",", array_unique(explode(",", $row[csf("file_no")]))); ?>&nbsp;</p>
						</td>
						<td align="center">
							<p><? echo implode(",", array_unique(explode(",", $row[csf("internal_ref_no")]))); ?>&nbsp;</p>
						</td>
					</tr>
				<?
					$i++;
				}
				?>
			<tbody>
		</table>
	<?
	}
	exit();
}

if ($action == "child_form_input_data") {
	if ($db_type == 0) {
		$sql = "select a.id, a.mst_id, a.job_no, a.product_id,a.fab_booking_no, a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no, group_concat(b.file_no) as file_no, group_concat(b.grouping) as internal_ref_no , c.pay_mode
		from wo_yarn_dyeing_dtls a, wo_po_break_down b , wo_yarn_dyeing_mst c
		where a.job_no=b.job_no_mst and a.id='$data' and a.mst_id = c.id 
		group by a.id, a.mst_id, a.job_no,a.fab_booking_no, a.product_id, a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no,c.pay_mode";
	} else {
		$sql = "select a.id, a.mst_id, a.job_no, a.product_id,a.fab_booking_no,a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no, listagg(cast(b.file_no as varchar(4000)), ',') within group(order by b.file_no) as file_no, listagg(cast(b.grouping as varchar(4000)), ',') within group(order by b.grouping) as internal_ref_no ,  c.pay_mode
		from wo_yarn_dyeing_dtls a, wo_po_break_down b , wo_yarn_dyeing_mst c
		where a.job_no=b.job_no_mst and a.id='$data' and a.mst_id = c.id 
		group by a.id, a.mst_id, a.job_no, a.product_id, a.fab_booking_no,a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no,c.pay_mode";
	}
	//echo $sql;
	$sql_re = sql_select($sql);
	foreach ($sql_re as $row) {
		$fab_booking_no = $row[csf("fab_booking_no")];
		echo "$('#txt_job_no').val('" . $row[csf("job_no")] . "');\n";
		echo "$('#txt_fab_booking_no').val('" . $row[csf("fab_booking_no")] . "');\n";
		echo "$('#txt_job_id').val('" . $row[csf("job_no_id")] . "');\n";
		echo "$('#txt_pro_id').val(" . $row[csf("product_id")] . ");\n";
		echo "$('#hdn_pre_prod_id').val(" . $row[csf("product_id")] . ");\n";
		$lot = return_field_value("lot", " product_details_master", "id=" . $row[csf("product_id")] . "", "lot");

		if ($row[csf("product_id")] > 0) {
			echo "$('#txt_lot').val('$lot');\n";
			echo "$('#cbo_count').val(" . $row[csf("count")] . ").attr('disabled',true);\n";
			echo "$('#txt_item_des').val('" . $row[csf("yarn_description")] . "').attr('disabled',true);\n";
		} else {
			echo "$('#txt_lot').val('$lot');\n";
			echo "$('#cbo_count').val(" . $row[csf("count")] . ");\n";
			echo "$('#txt_item_des').val('" . $row[csf("yarn_description")] . "');\n";
		}

		$job_ref = $row[csf("job_no")];
		$yarn_color_id = $row[csf("yarn_color")];
		$color_rangeId = $row[csf("color_range")];
		$fab_booking_no = $row[csf("fab_booking_no")];
		$yarn_wo_qty = $row[csf("yarn_wo_qty")];
		$dyeing_charge = $row[csf("dyeing_charge")];
		$no_of_bag = $row[csf("no_of_bag")];
		$amount = $row[csf("amount")];
		$no_of_cone = $row[csf("no_of_cone")];
		$min_require_cone = $row[csf("min_require_cone")];
		$remarks = $row[csf("remarks")];
		$referance_no = $row[csf("referance_no")];
		$file_no = $row[csf("file_no")];
		$internal_ref_no = $row[csf("internal_ref_no")];
		$uom_id = $row[csf("uom")];
		$mst_id = $row[csf("mst_id")];
		$dtls_updateId = $row[csf("id")];

		$product_id = $row[csf("product_id")];
		$company_id = return_field_value("a.company_id as company_id", "wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b", " b.mst_id=a.id and b.id=$data  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "company_id");

		$variavle_info = sql_select("select auto_allocate_yarn_from_requis from variable_settings_production where company_name=$company_id and variable_list=6 and status_active=1 and is_deleted=0");
		$variavle_info_inv = sql_select("select allocation from variable_settings_inventory where company_name=$company_id and item_category_id=1 and variable_list =18 and status_active=1 and is_deleted=0");

		$variable_set_allocation = $variavle_info_inv[0][csf("allocation")]; // ALLOCATION FEATURE
		$auto_allocation_from_requisition_feature = $variavle_info[0][csf("auto_allocate_yarn_from_requis")]; // AUTO ALLOCATION

		if ($variable_set_allocation == 1 && $auto_allocation_from_requisition_feature == 2) {
			if ($lot != "" && $fab_booking_no != "") {
				$sql_allowcate = sql_select("select a.id,a.lot,b.po_break_down_id as po_id from product_details_master a,inv_material_allocation_mst b where a.id=b.item_id and b.job_no='$job_ref' and b.booking_no='$fab_booking_no' and a.id=$product_id and a.item_category_id=1  and a.status_active=1 and b.status_active=1");
				foreach ($sql_allowcate as $roww) {
					$po_id = $roww[csf("po_id")];
				}

				echo "load_drop_down( 'requires/yarn_dyeing_charge_booking_controller','$po_id', 'load_drop_down_po_color', 'color_td' );\n";
			} else {
				echo "load_drop_down( 'requires/yarn_dyeing_charge_booking_controller','$job_ref', 'load_drop_down_color', 'color_td' );\n";
			}
		} else {
			echo "load_drop_down( 'requires/yarn_dyeing_charge_booking_controller','$job_ref', 'load_drop_down_color', 'color_td' );\n";
		}
		//echo $yarn_color_id.',A';
		echo "$('#txt_po_id').val('" . $po_id . "');\n";
		echo "$('#txt_yern_color').val('" . $yarn_color_id . "');\n";
		echo "$('#cbo_color_range').val('" . $color_rangeId . "');\n";
		echo "$('#cbo_uom').val('" . $uom_id . "');\n";
		echo "$('#txt_wo_qty').val(" . $yarn_wo_qty . ");\n";
		echo "$('#hdn_wo_qty').val(" . $yarn_wo_qty . ");\n";

		echo "$('#txt_job_no').attr('disabled',true);\n";
		echo "$('#txt_yern_color').attr('disabled',true);\n";
		echo "$('#txt_lot').attr('disabled',true);\n";

		$stripe_info = sql_select("select a.stripe_color,a.color_number_id, a.measurement, a.pre_cost_fabric_cost_dtls_id from wo_pre_stripe_color a where  a.job_no='" . $job_ref . "' and a.status_active=1");

		$total_measurement = array();
		$stripe_color_measurement = array();
		if (!empty($stripe_info)) {
			foreach ($stripe_info as $row) {
				$total_measurement[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]] += $row[csf('measurement')];
				$stripe_color_measurement[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('stripe_color')]][$row[csf('color_number_id')]] += $row[csf('measurement')];
			}
		}

		$stripe_required_data = sql_select("select b.pre_cost_fabric_cost_dtls_id,b.gmts_color_id as color_number_id, sum(b.grey_fab_qnty) grey_fab_qnty,sum(b.amount) grey_fab_amount from wo_booking_dtls b where b.job_no='$job_ref' and b.booking_no='$fab_booking_no' and b.status_active=1 and b.is_deleted=0 group by b.pre_cost_fabric_cost_dtls_id,b.gmts_color_id order by b.pre_cost_fabric_cost_dtls_id desc");
		$stripe_required = 0;
		$booking_stripe_required_qty = array();
		if (!empty($stripe_required_data)) {
			$booking_req_qty = 0;
			foreach ($stripe_required_data as $row) {
				$stripe_required_by_presoct_id[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]['qty'] = $row[csf('grey_fab_qnty')];
				$stripe_required_by_presoct_id[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]['amount'] = $row[csf('grey_fab_amount')];
				$booking_req_qty += $row[csf('grey_fab_qnty')];
				$booking_req_amount += $row[csf('grey_fab_amount')];
			}

			foreach ($stripe_required_data as $row) {
				$total_measurements = $total_measurement[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]];
				$total_color_measurement = $stripe_color_measurement[$row[csf('pre_cost_fabric_cost_dtls_id')]][$yarn_color_id][$row[csf('color_number_id')]];
				$stripe_required_qty = $stripe_required_by_presoct_id[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]['qty'];
				$stripe_required_amount = $stripe_required_by_presoct_id[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]['amount'];

				$measurement_perc = ($total_color_measurement / $total_measurements) * 100;
				$booking_color_wise_stripe_required_chk = ($measurement_perc / 100) * $stripe_required_qty;
				$booking_color_wise_stripe_required_qty = (is_nan($booking_color_wise_stripe_required_chk)) ? 0 : $booking_color_wise_stripe_required_chk;

				$booking_color_wise_stripe_required_amount_chk = ($measurement_perc / 100) * $stripe_required_amount;
				$booking_color_wise_stripe_required_amount = (is_nan($booking_color_wise_stripe_required_amount_chk)) ? 0 : $booking_color_wise_stripe_required_amount_chk;


				//$booking_color_wise_stripe_required_qty += ($measurement_perc/100)*$stripe_required_qty;
				//$booking_color_wise_stripe_required_qty = (is_nan($booking_color_wise_stripe_required_qty))?0:$booking_color_wise_stripe_required_qty;
				//$booking_stripe_required_qty[$yarn_color_id][]=$booking_color_wise_stripe_required_qty;


				$booking_stripe_required_qty[$yarn_color_id] += $booking_color_wise_stripe_required_qty; //Issue id=4386
				$booking_stripe_required_amount[$yarn_color_id] += $booking_color_wise_stripe_required_amount; //Issue id=4386
			}
		}

		// $booking_color_wise_stripe_required_qty = array_sum($booking_stripe_required_qty[$yarn_color_id]);
		$booking_color_wise_stripe_required_qty = $booking_stripe_required_qty[$yarn_color_id];
		if ($booking_color_wise_stripe_required_qty > 0) $booking_color_wise_stripe_required_qty = $booking_color_wise_stripe_required_qty;
		else $booking_color_wise_stripe_required_qty = 0;

		$booking_color_wise_stripe_required_amount = $booking_stripe_required_amount[$yarn_color_id];
		if ($booking_color_wise_stripe_required_amount > 0) $booking_color_wise_stripe_required_amount = $booking_color_wise_stripe_required_amount;
		else $booking_color_wise_stripe_required_amount = 0;

		$bookingCond = "";
		if ($fab_booking_no != "") //fab_booking_no
		{
			$bookingCond = " and fab_booking_no='$fab_booking_no'";
		}

		$prev_booking_sql = "select sum(yarn_wo_qty) as yarn_wo_qty,sum(amount) as yarn_wo_amount from wo_yarn_dyeing_dtls where job_no='" . $job_ref . "' and yarn_color=" . $yarn_color_id . " and mst_id!=" . $mst_id . " and status_active=1 and is_deleted=0 $bookingCond ";
		$prev_booking_sql_result = sql_select($prev_booking_sql);
		$prev_booking_qty = $prev_booking_sql_result[0][csf('yarn_wo_qty')];
		$prev_booking_amount = $prev_booking_sql_result[0][csf('yarn_wo_amount')];
		//echo $stripe_required.'='.$prev_booking.'DADDDDDD';

		$cu_bal_qty = $booking_color_wise_stripe_required_qty - $prev_booking_qty;
		$cu_bal_qty = number_format($cu_bal_qty, 2, '.', '');

		$cu_bal_amount = $booking_color_wise_stripe_required_amount - $prev_booking_amount;
		$cu_bal_amount = number_format($cu_bal_amount, 2, '.', '');

		echo "$('#txt_budget_wo_qty').val(" . $cu_bal_qty . ");\n";
		echo "$('#txt_budget_wo_amount').val(" . $cu_bal_amount . ");\n";

		echo "$('#txt_dyeing_charge').val(" . $dyeing_charge . ");\n";
		echo "$('#txt_amount').val(" . $amount . ");\n";

		echo "$('#txt_bag').val(" . $no_of_bag . ");\n";
		echo "$('#txt_cone').val(" . $no_of_cone . ");\n";
		echo "$('#txt_min_req_cone').val(" . $min_require_cone . ");\n";
		echo "$('#txt_remarks').val('" . $remarks . "');\n";
		echo "$('#txt_ref_no').val('" . $referance_no . "');\n";
		$file_no = implode(",", array_unique(explode(",", $file_no)));
		echo "$('#txt_file_no').val('" . $file_no . "');\n";
		$internal_ref_no = implode(",", array_unique(explode(",", $internal_ref_no)));
		echo "$('#txt_int_ref_no').val('" . $internal_ref_no . "');\n";
		echo "fnc_calculate();\n";

		echo "$('#dtls_update_id').val(" . $dtls_updateId . ");\n";
		echo "set_button_status(1, permission, 'fnc_yarn_dyeing',1,0);\n";
	}
}

if ($action == "yern_dyeing_booking_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo "$company";die;
	if ($db_type == 0) $select_field_grp = "group by a.id order by supplier_name";
	else if ($db_type == 2) $select_field_grp = "group by a.id,a.supplier_name order by supplier_name";

	$current_date = date('d-m-Y');
	$previous_day = date("d-m-Y", strtotime(date("d-m-Y") . '-60 days'));
	//echo $current_date."##".$previous_day;die;
	?>
	<script>
		function set_checkvalue() {
			if (document.getElementById('chk_job_wo_po').value == 0)
				document.getElementById('chk_job_wo_po').value = 1;
			else
				document.getElementById('chk_job_wo_po').value = 0;
		}

		function js_set_value(id) {
			$("#hidden_sys_number").val(id);
			//$("#hidden_id").val(id);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div style="width:930px;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th colspan="5">
								<? echo create_drop_down("cbo_search_category", 130, $string_search_type, '', 1, "-- Search Catagory --"); ?>
							</th>
							<th colspan="3" style="text-align:right"><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without Job</th>
						</tr>
						<tr>
							<th width="120">Buyer Name</th>
							<th width="130">Supplier Name</th>
							<th width="100">Booking No</th>
							<th width="100">Job No</th>
							<th width="70">Ref No</th>
							<th width="130" colspan="2">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchorderfrm_1','search_div','','','','');" /></th>
						</tr>
					</thead>
					<tbody>
						<tr class="general">
							<td> <? echo create_drop_down("cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", "", "", 0); ?>
							</td>
							<td>
								<?
								if ($pay_mode == 5 || $pay_mode == 3) {
									echo create_drop_down("cbo_supplier_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name", 1, "-- Select Supplier --", "", "", 0, "");
								} else {
									echo create_drop_down("cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company c where a.id=c.supplier_id and c.tag_company=$company and a.status_active =1 and a.id in(select supplier_id from lib_supplier_party_type where party_type=2) and a.id in(select supplier_id from lib_supplier_party_type where party_type=21) group by a.id,a.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select Supplier --", $selected, "", 0);
								}

								?>
							</td>
							<td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>

							<td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
							<td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:70px"></td>
							<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" /></td>
							<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" /> </td>
							<td>
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_ref_no').value, 'create_sys_search_list_view', 'search_div', 'yarn_dyeing_charge_booking_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" valign="middle" colspan="7">
								<? echo load_month_buttons(1);  ?>
								<input type="hidden" id="hidden_sys_number" value="hidden_sys_number" />
								<input type="hidden" id="hidden_id" value="hidden_id" />
							</td>
						</tr>
					</tbody>
				</table>
				<div id="search_div"></div>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_sys_search_list_view") {
	$ex_data = explode("_", $data);
	$supplier = $ex_data[0];
	$fromDate = $ex_data[1];
	$toDate = $ex_data[2];
	$company = $ex_data[3];
	$buyer_val = $ex_data[4];
	$chk_job_wo_po = trim($ex_data[9]);
	$ref_no = trim($ex_data[10]);

	if ($ref_no != "") {
		$ref_sql = sql_select("select job_id from wo_po_break_down where grouping='$ref_no'");
		foreach ($ref_sql as $row) {
			$job_id_arr[$row[csf("job_id")]] = $row[csf("job_id")];
		}
	}
	$job_ids = implode(",", $job_id_arr);
	if ($job_ids != "") $job_ids_cond = "and d.id in($job_ids)";
	else $job_ids_cond = "";
	//echo $job_ids;

	if ($supplier != 0)  $supplier = "and a.supplier_id='$supplier'";
	else  $supplier = "";
	if ($company != 0)  $company = " and a.company_id='$company'";
	else  $company = "";
	if ($buyer_val != 0)  $buyer_cond = "and d.buyer_name='$buyer_val'";
	else  $buyer_cond = "";
	if ($db_type == 0) {
		$booking_year_cond = " and SUBSTRING_INDEX(a.insert_date, '-', 1)=$ex_data[8]";
		$year_cond = " and SUBSTRING_INDEX(d.insert_date, '-', 1)=$ex_data[8]";
		if ($fromDate != 0 && $toDate != 0) $sql_cond = "and a.booking_date  between '" . change_date_format($fromDate, 'yyyy-mm-dd') . "' and '" . change_date_format($toDate, 'yyyy-mm-dd') . "'";
	} else if ($db_type == 2) {
		$booking_year_cond = " and to_char(a.insert_date,'YYYY')=$ex_data[8]";
		$year_cond = " and to_char(d.insert_date,'YYYY')=$ex_data[8]";
		if ($fromDate != 0 && $toDate != 0) $sql_cond = "and a.booking_date  between '" . change_date_format($fromDate, 'mm-dd-yyyy', '/', 1) . "' and '" . change_date_format($toDate, 'mm-dd-yyyy', '/', 1) . "'";
	}

	if ($ex_data[5] == 4 || $ex_data[5] == 0) {
		if (str_replace("'", "", $ex_data[7]) != "") $job_cond = " and d.job_no_prefix_num like '%$ex_data[7]%' $year_cond ";
		else  $job_cond = "";
		if (str_replace("'", "", $ex_data[6]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num like '%$ex_data[6]%'  $booking_year_cond  ";
		else $booking_cond = "";
	} else if ($ex_data[5] == 1) {
		if (str_replace("'", "", $ex_data[7]) != "") $job_cond = " and d.job_no_prefix_num ='$ex_data[7]' ";
		else  $job_cond = "";
		if (str_replace("'", "", $ex_data[6]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num ='$ex_data[6]'   ";
		else $booking_cond = "";
	} else if ($ex_data[5] == 2) {
		if (str_replace("'", "", $ex_data[7]) != "") $job_cond = " and d.job_no_prefix_num like '$ex_data[7]%'  $year_cond";
		else  $job_cond = "";
		if (str_replace("'", "", $ex_data[6]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num like '$ex_data[6]%'  $booking_year_cond  ";
		else $booking_cond = "";
	} else if ($ex_data[5] == 3) {
		if (str_replace("'", "", $ex_data[7]) != "") $job_cond = " and d.job_no_prefix_num like '%$ex_data[7]'  $year_cond";
		else  $job_cond = "";
		if (str_replace("'", "", $ex_data[6]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num like '%$ex_data[6]'  $booking_year_cond  ";
		else $booking_cond = "";
	}

	if ($db_type == 0) $select_year = "year(a.insert_date) as year";
	else $select_year = "to_char(a.insert_date,'YYYY') as year";
	if ($chk_job_wo_po == 1) {
		$sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode, a.source, a.attention, $select_year, 0 as job_no_id, null as job_no, 0 as buyer_name, null as po_number
		from wo_yarn_dyeing_mst a
		where a.status_active=1 and a.is_deleted=0 and a.entry_form=41 and a.id not in(select mst_id from wo_yarn_dyeing_dtls where job_no_id>0 and entry_form=41  and status_active=1 and  is_deleted=0) $company $supplier  $sql_cond  $booking_cond order by a.id DESC";
	} else {
		if ($db_type == 0) {
			$sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,year(a.insert_date) as year,group_concat(distinct b.job_no_id) as job_no_id, group_concat(distinct b.job_no) as job_no,d.buyer_name
			from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, wo_po_details_master d
			where a.id=b.mst_id and b.job_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=41 and b.entry_form=41 $company $supplier  $sql_cond  $buyer_cond $job_cond $booking_cond $job_ids_cond
			order by a.id DESC";
		}
		//LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
		else if ($db_type == 2) {
			$sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year, d.buyer_name, LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(b.job_no_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no_id) as job_no_id
			from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, wo_po_details_master d
			where a.id=b.mst_id and b.job_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.entry_form=41 and b.entry_form=41 $company $supplier  $sql_cond  $buyer_cond  $job_cond $booking_cond $job_ids_cond
			group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date,d.buyer_name order by a.id DESC";
		}

		//echo $sql;//die;

		$nameArray = sql_select($sql);
		$all_job_id = "";
		foreach ($nameArray as $row) {
			$all_job_id .= $row[csf("job_no_id")] . ",";
		}
		//echo $all_job_id;die;


		$all_job_id = chop($all_job_id, ",");
		if ($all_job_id != "") {
			$all_job_id = array_chunk(array_unique(explode(",", $all_job_id)), 999);

			$po_sql = "select p.mst_id as mst_id, b.id, b.po_number,b.grouping as ref_no from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b where p.job_no_id=a.id and a.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0";
			$p = 1;
			foreach ($all_job_id as $job_id) {
				//$po_sql
				if ($p == 1) $po_sql .= " and (a.id in(" . implode(',', $job_id) . ")";
				else $po_sql .= " or a.id in(" . implode(',', $job_id) . ")";
				$p++;
			}
			$po_sql .= ")";

			$po_result = sql_select($po_sql);
			$po_data = array();
			foreach ($po_result as $row) {
				$po_data[$row[csf("mst_id")]] .= $row[csf("po_number")] . ",";
				$po_ref_arr[$row[csf("mst_id")]] .= $row[csf("ref_no")] . ",";
			}
		}
	}

?>
	<div style="width:930px;" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="50">Booking no</th>
				<th width="40">Year</th>
				<th width="120">Job No</th>
				<th width="300">Order No</th>
				<th width="100">Buyer Name</th>
				<th width="120">Supplier Name</th>
				<th width="70">Booking Date</th>
				<th>Delevary Date</th>
			</thead>
		</table>
		<div style="width:930px; margin-left:3px; overflow-y:scroll; max-height:270px;" id="buyer_list_view">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="912" class="rpt_table" id="tbl_list_search">
				<?
				$i = 1;
				$nameArray = sql_select($sql);
				//var_dump($nameArray);die;
				foreach ($nameArray as $selectResult) {
					$job_no = implode(",", array_unique(explode(",", $selectResult[csf("job_no")])));
					$job_no_id = implode(",", array_unique(explode(",", $selectResult[csf("job_no_id")])));
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";
					$pay_mode_id = $selectResult[csf("pay_mode")];
					if ($pay_mode_id == 3 || $pay_mode_id == 5) {
						$supplier = $company_library[$selectResult[csf('supplier_id')]];
					} else {
						$supplier = $supplier_arr[$selectResult[csf('supplier_id')]];
					}
					$ref_no = implode(",", array_unique(explode(",", chop($po_ref_arr[$selectResult[csf("id")]], ","))));
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('ydw_no')]; ?>'); ">
						<td width="30" align="center">
							<p><? echo $i; ?></p>
						</td>
						<td width="50" align="center">
							<p> <? echo $selectResult[csf('yarn_dyeing_prefix_num')]; ?></p>
						</td>
						<td width="40" align="center">
							<p> <? echo $selectResult[csf('year')]; ?></p>
						</td>
						<td width="120">
							<p><? echo $job_no; ?></p>
						</td>
						<td width="300" title="<? echo $ref_no; ?>" style="word-break:break-all">
							<? //$po_ref_arr[$row[csf("mst_id")]]


							$po_no = implode(",", array_unique(explode(",", chop($po_data[$selectResult[csf("id")]], ","))));
							echo $po_no;
							?></td>
						<td width="100">
							<p> <? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p>
						</td>
						<td width="120">
							<p><? echo $supplier; ?></p>
						</td>
						<td width="70">
							<p><? echo change_date_format($selectResult[csf('booking_date')]); ?></p>
						</td>
						<td>
							<p><? echo change_date_format($selectResult[csf('delivery_date')]); ?></p>
						</td>
					</tr>
				<?
					$i++;
				}
				?>
			</table>
		</div>
	</div>
<?
	exit();
}

if ($action == "populate_master_from_data") {

	$sql = "select  a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention, a.tenor, a.is_short, a.budget_version, a.ready_to_approved, a.is_approved from wo_yarn_dyeing_mst a where a.id=$data";
	//echo $sql;die;
	$res = sql_select($sql);
	foreach ($res as $row) {
		echo "$('#txt_booking_no').val('" . $row[csf("ydw_no")] . "');\n";
		echo "$('#cbo_company_name').val('" . $row[csf("company_id")] . "');\n";
		if ($row[csf("budget_version")] != 0 || $row[csf("budget_version")] == '') {
			echo "$('#cbo_budget_version').val('" . $row[csf("budget_version")] . "');\n";
			echo "$('#cbo_budget_version').attr('disabled',true);\n";
		}

		//echo "$('#hidden_type').val(".$row[csf("piworeq_type")].");\n";

		echo "$('#txt_booking_date').val('" . change_date_format($row[csf("booking_date")]) . "');\n";
		echo "$('#txt_delivery_date').val('" . change_date_format($row[csf("delivery_date")]) . "');\n";
		//echo "$('#txt_delivery_date').val('".change_date_format($row[csf("delivery_date")])."');\n";
		echo "$('#cbo_currency').val('" . $row[csf("currency")] . "');\n";
		echo "set_exchang('" . $row[csf("currency")] . "');\n";
		echo "$('#txt_exchange_rate').val('" . $row[csf("ecchange_rate")] . "');\n";
		echo "$('#cbo_pay_mode').val('" . $row[csf("pay_mode")] . "');\n";

		echo "load_drop_down( 'requires/yarn_dyeing_charge_booking_controller', '" . $row[csf("pay_mode")] . "', 'load_drop_down_supplier', 'supplier_td' );\n";

		echo "$('#cbo_supplier_name').val('" . $row[csf("supplier_id")] . "');\n";

		$total_issue_qty = return_field_value("sum(c.cons_quantity) as cons_quantity", "wo_yarn_dyeing_mst a,inv_issue_master b, inv_transaction c", " b.booking_id=a.id and b.id=c.mst_id and a.id=" . $row[csf("id")] . " and b.booking_id=" . $row[csf("id")] . " and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.item_category=1 and c.item_category=1 and c.transaction_type=2", "cons_quantity");

		if ($total_issue_qty > 0) {
			echo "$('#cbo_supplier_name').attr('disabled',true);\n";
		}

		echo "$('#txt_attention').val('" . $row[csf("attention")] . "');\n";
		echo "$('#txt_tenor').val('" . $row[csf("tenor")] . "');\n";
		echo "$('#cbo_source').val('" . $row[csf("source")] . "');\n";
		echo "$('#txt_delivery_end').val('" . change_date_format($row[csf("delivery_date_end")]) . "');\n";
		echo "$('#dy_delevery_start').val('" . change_date_format($row[csf("dy_delivery_date_start")]) . "');\n";
		echo "$('#dy_delevery_end').val('" . change_date_format($row[csf("dy_delivery_date_end")]) . "');\n";
		echo "$('#update_id').val(" . $row[csf("id")] . ");\n"; //cbo_is_short
		echo "$('#cbo_is_short').val(" . $row[csf("is_short")] . ");\n";
		echo "$('#cbo_ready_to_approved').val(" . $row[csf("ready_to_approved")] . ");\n";
		if ($row[csf("is_approved")] == 1) {
			echo "$('#approved').html('Approved');\n";
		} elseif ($row[csf("is_approved")] == 3) {
			echo "$('#approved').html('Partial Approved');\n";
		} else {
			echo "$('#approved').html('');\n";
		}
		//right side list view
		//echo "show_list_view(".$row[csf("piworeq_type")]."+'**'+".$row[csf("pi_wo_req_id")].",'show_product_listview','list_product_container','requires/get_out_entry_controller','');\n";
	}
	exit();
}

if ($action == "dyeing_search_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$company = str_replace("'", "", $company);
?>
	<script>
		function js_set_value(str) {
			$("#hidden_rate").val(str);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:590px;">
			<fieldset>
				<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
					<table width="570" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
						<thead>
							<tr>
								<th width="40">Sl No.</th>
								<th width="170">Const. Compo.</th>
								<th width="100">Process Name</th>
								<th width="100">Color</th>
								<th width="90">Rate</th>
								<th>UOM</th>
							</tr>
						</thead>
					</table>
					<?
					$sql = "select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where comapny_id=$company and process_id=30 and status_active=1";
					//echo $sql;
					$sql_result = sql_select($sql);
					?>
					<div style="width:570px; overflow-y:scroll; max-height:240px;font-size:12px; overflow-x:hidden; cursor:pointer;">
						<table width="570" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" id="table_charge">
							<tbody>
								<?
								$i = 1;
								foreach ($sql_result as $row) {
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf("in_house_rate")]; ?>)">
										<td width="40" align="center"><? echo $i;  ?></td>
										<td width="170"><? echo $row[csf("const_comp")]; ?></td>
										<td width="100" align="center"><? echo $conversion_cost_head_array[$row[csf("process_id")]]; ?></td>
										<td width="100"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
										<td width="90" align="right"><? echo number_format($row[csf("in_house_rate")], 2); ?></td>
										<td><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
									</tr>
								<?
									$i++;
								}
								?>
								<input type="hidden" id="hidden_rate" />
							</tbody>
						</table>
					</div>
				</form>
			</fieldset>
		</div>
	</body>
	<script type="text/javascript">
		setFilterGrid("table_charge", -1)
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "lot_search_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$company = str_replace("'", "", $company);
	$job_no = str_replace("'", "", $job_no);
	$fab_booking_no = str_replace("'", "", $fab_booking_no);
?>
	<script>
		function js_set_value2(str) {
			//alert(str);
			$("#hidden_product").val(str);
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
		<div style="width:695px;" align="center">
			<fieldset>
				<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" width="395">
						<thead>
							<tr>
								<th width="250">Lot</th>
								<th width="">&nbsp;</th>
							</tr>
						</thead>
						<tr class="general">
							<td align="center">
								<input name="txt_lot_search" id="txt_lot_search" class="text_boxes" style="width:150px" placeholder="Write">
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $company ?>+'_'+document.getElementById('txt_lot_search').value+'_'+'<? echo $job_no ?>'+'_'+'<? echo $fab_booking_no ?>', 'create_lot_search_list_view', 'search_div', 'yarn_dyeing_charge_booking_controller', 'setFilterGrid(\'table_charge\',-1)')" style="width:100px;" />
							</td>
					</table>
					<br>
					<table width="695" align="center">
						<tr>
							<td align="center" valign="top" id="search_div"></td>
						</tr>
					</table>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "create_lot_search_list_view") {
	$data = explode('_', $data);
	$company = str_replace("'", "", $data[0]);
	$lot_search = str_replace("'", "", $data[1]);
	$job_no = str_replace("'", "", $data[2]);
	$booking_no = str_replace("'", "", $data[3]);

	if ($lot_search != '') $lot_cond = "and lot ='$lot_search'";
	else  $lot_cond = "";
	if ($company != '') $com_cond = "and company_id =$company";
	else  $com_cond = "";
	if ($booking_no != '') $booking_cond = "and b.booking_no='$booking_no'";
	else  $booking_cond = "";
?>
	</head>

	<body>
		<div style="width:945px;">
			<fieldset>
				<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
					<table width="945" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
						<thead>
							<tr>
								<th width="30">Sl</th>
								<th width="100">Lot No</th>
								<th width="90">Brand</th>
								<th width="280">Product Name Details</th>
								<th width="80">Stock</th>
								<th width="80">Allocated to Order</th>
								<th width="80">Un Allocated Qty.</th>
								<th width="60">Age (Days)</th>
								<th width="60">DOH</th>
								<th>UOM</th>
							</tr>
						</thead>
					</table>
					<?
					// VARIABLE QUERY TO CHECK ALLOCATION FEATURE IS SET AND IF AUTO ALLOCATION FROM REQUISITION IS SET
					$variavle_info = sql_select("select auto_allocate_yarn_from_requis from variable_settings_production where company_name=$company and variable_list=6 and status_active=1 and is_deleted=0");

					$variavle_info_inv = sql_select("select allocation from variable_settings_inventory where company_name=$company and variable_list =18 and status_active=1 and is_deleted=0 and item_category_id=1");

					$po_id_arr = array();
					$variable_set_allocation = $variavle_info_inv[0][csf("allocation")]; // ALLOCATION FEATURE
					$auto_allocation_from_requisition_feature = $variavle_info[0][csf("auto_allocate_yarn_from_requis")]; // AUTO ALLOCATION
					//echo $variable_set_allocation.'='.$auto_allocation_from_requisition_feature;
					if ($variable_set_allocation == 1) {
						if ($auto_allocation_from_requisition_feature == 1) {
							$sql = "select id,product_name_details,allocated_qnty,available_qnty,lot,item_code,unit_of_measure,yarn_count_id,brand,current_stock,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,color from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0 $com_cond $lot_cond";
							$sql_result = sql_select($sql);
						} else {
							$sql = "select a.id,a.product_name_details,a.allocated_qnty,a.available_qnty,a.lot,a.item_code,a.unit_of_measure,a.yarn_count_id,a.brand, a.current_stock,a.yarn_comp_type1st,a.yarn_comp_percent1st,a.yarn_comp_type2nd,a.yarn_comp_percent2nd,a.yarn_type,a.color,b.po_break_down_id as po_id,b.qnty from product_details_master a,inv_material_allocation_dtls b where a.id=b.item_id and b.job_no='$job_no' and b.qnty>0  and a.item_category_id=1 $com_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_cond  $lot_cond";
							$sql_result = sql_select($sql);
							foreach ($sql_result as $row) {
								$product_ids[] = $row[csf("id")];
								$po_id_arr[$row[csf("id")]][$row[csf("po_id")]] = $row[csf("po_id")];
							}
							$prod_cond = " and prod_id in(" . implode(",", $product_ids) . ")";
						}
					} else {
						$sql = "select id,product_name_details,allocated_qnty,available_qnty,lot,item_code,unit_of_measure,yarn_count_id,brand,current_stock,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,color from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0 $com_cond $lot_cond";
						$sql_result = sql_select($sql);
					}

					$date_array = array();
					$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 $prod_cond group by prod_id";
					$result_returnRes_date = sql_select($returnRes_date);
					foreach ($result_returnRes_date as $row) {
						$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
						$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
					}
					//echo $sql;
					?>

					<div style="width:945px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;">
						<table width="945" cellspacing="0" cellpadding="0" border="0" class="rpt_table" style="cursor:pointer" rules="all" id="table_charge">
							<tbody>
								<?
								$i = 1;
								if (!empty($sql_result)) {
									foreach ($sql_result as $row) {
										if ($row[csf("current_stock")] > 0) {
											if ($check_lot[$row[csf("id")]] != $row[csf("id")]) {
												$check_lot[$row[csf("id")]] = $row[csf("id")];
												if (!empty($po_id_arr[$row[csf("id")]])) {
													$arr_po = array();
													foreach ($po_id_arr[$row[csf("id")]] as $key => $val) {
														$arr_po[$val] = $val;
													}
													$row[csf("po_id")] = implode(',', $arr_po);
												}

												if ($i % 2 == 0)
													$bgcolor = "#E9F3FF";
												else
													$bgcolor = "#FFFFFF";

												$item_description = "";
												//echo $row[csf("po_id")];
												$item_description = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "% ";
												if ($composition[$row[csf("yarn_comp_type2nd")]] != "")
													$item_description .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "% ";
												$item_description .= $yarn_type[$row[csf("yarn_type")]] . " " . $color_arr[$row[csf("color")]];
												$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
												$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['maxate'], date("Y-m-d"));
								?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value2('<?= str_replace(array("\r", "\n"), '', $item_description) . '**' . $row[csf("yarn_count_id")] . '**' . $row[csf("lot")] . '**' . $row[csf("id")] . '**' . $row[csf("po_id")] . '**' . $variable_set_allocation . '**' . $auto_allocation_from_requisition_feature; ?>')">
													<td width="30" align="center">
														<p><? echo $i;  ?></p>
													</td>
													<td width="100" align="center">
														<p><? echo $row[csf("lot")]; ?></p>
													</td>
													<td width="90">
														<p><? echo $brand_arr[$row[csf("brand")]]; ?></p>
													</td>
													<td width="280"><? echo $row[csf("product_name_details")]; ?></p>
													</td>
													<td width="80" align="right">
														<p><? echo number_format($row[csf("current_stock")], 3); ?></p>
													</td>
													<td width="80" align="right" title="AllocatedQty=<? echo $row[csf("qnty")]; ?>">
														<p><? echo number_format($row[csf("allocated_qnty")], 2); ?></p>
													</td>
													<td width="80" align="right">
														<p><? echo number_format($row[csf("available_qnty")], 2); ?></p>
													</td>
													<td width="60" align="right">
														<p><? echo $ageOfDays; ?></p>
													</td>
													<td width="60" align="right">
														<p><? echo $daysOnHand; ?></p>
													</td>
													<td align="center">
														<p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p>
													</td>
												</tr>
								<?
												$i++;
											}
										}
									}
								} else {
									echo "<tr><td colspan='10' style='color:red; text-align:center; font-weight:bold;'>No data found</td></tr>";
								}
								?>
								<input type="hidden" id="hidden_product" style="width:100px;" />
							</tbody>
						</table>
					</div>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "lot_search_popup2") //Old--Not Used
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$company = str_replace("'", "", $company);
	$job_no = str_replace("'", "", $job_no);
	//echo $job_no;die;
?>
	<script>
		function js_set_value42(str) {
			alert(str);
			$("#hidden_product").val(str);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div style="width:595px;">
			<fieldset>
				<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
					<table width="595" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
						<thead>
							<tr>
								<th width="40">Sl No.</th>
								<th width="100">Lot No</th>
								<th width="90">Brand</th>
								<th width="200">Product Name Details</th>
								<th width="80">Stock</th>
								<th>UOM</th>
							</tr>
						</thead>
					</table>
					<?

					$sql = "select id,product_name_details,lot,item_code,unit_of_measure,yarn_count_id,brand,current_stock,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,color from product_details_master where company_id='$company' and item_category_id=1";

					//echo $sql;
					$sql_result = sql_select($sql);
					?>
					<div style="width:595px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;">
						<table width="595" cellspacing="0" cellpadding="0" border="1" class="rpt_table" id="table_charge" style="cursor:pointer" rules="all">
							<tbody>
								<?
								$i = 1;
								foreach ($sql_result as $row) {
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value2('<? echo $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . " " . $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . " " . $yarn_type[$row[csf("yarn_type")]] . " " . $color_arr[$row[csf("color")]]; ?>,<? echo $row[csf("yarn_count_id")]; ?>,<? echo $row[csf("lot")]; ?>,<? echo $row[csf("id")]; ?>')">
										<td width="40" align="center">
											<p><? echo $i;  ?></p>
										</td>
										<td width="100" align="center">
											<p><? echo $row[csf("lot")]; ?></p>
										</td>
										<td width="90">
											<p><? echo $brand_arr[$row[csf("brand")]]; ?></p>
										</td>
										<td width="200">v<? echo $row[csf("product_name_details")]; ?></p>
										</td>
										<td width="80" align="right">
											<p><? echo $row[csf("current_stock")]; ?></p>
										</td>
										<td>
											<p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p>
										</td>
									</tr>
								<?
									$i++;
								}
								?>
								<input type="text" id="hidden_product" style="width:200px;" />
							</tbody>
						</table>
					</div>
				</form>
			</fieldset>
		</div>
	</body>
	<script type="text/javascript">
		setFilterGrid("table_charge", -1)
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "terms_condition_popup") {
	echo load_html_head_contents("Order Search", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
		var permission = '<? echo $permission; ?>';

		function add_break_down_tr(i) {
			var row_num = $('#tbl_termcondi_details tr').length - 1;
			if (row_num != i) {
				return false;
			} else {
				i++;

				$("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) {
							var id = id.split("_");
							return id[0] + "_" + i
						},
						'name': function(_, name) {
							return name + i
						},
						'value': function(_, value) {
							return value
						}
					});
				}).end().appendTo("#tbl_termcondi_details");
				$('#increase_' + i).removeAttr("onClick").attr("onClick", "add_break_down_tr(" + i + ");");
				$('#decrease_' + i).removeAttr("onClick").attr("onClick", "fn_deletebreak_down_tr(" + i + ")");
				$('#termscondition_' + i).val("");
				$("#tbl_termcondi_details tr:last").find("td:first").html(i);
			}

		}

		function fn_deletebreak_down_tr(rowNo) {

			var numRow = $('table#tbl_termcondi_details tbody tr').length;
			if (numRow == rowNo && rowNo != 1) {
				$('#tbl_termcondi_details tbody tr:last').remove();
			}

		}

		function fnc_fabric_booking_terms_condition(operation) {
			var row_num = $('#tbl_termcondi_details tr').length - 1;
			var data_all = "";
			for (var i = 1; i <= row_num; i++) {
				if (form_validation('termscondition_' + i, 'Term Condition') == false) {
					return;
				}
				data_all = data_all + get_submitted_data_string('txt_booking_no*termscondition_' + i, "../../../");
				//alert(data_all);
			}
			var data = "action=save_update_delete_fabric_booking_terms_condition&operation=" + operation + '&total_row=' + row_num + data_all;
			//	alert(data);
			//freeze_window(operation);
			http.open("POST", "yarn_dyeing_charge_booking_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
		}

		function fnc_fabric_booking_terms_condition_reponse() {
			if (http.readyState == 4) {
				// alert(http.responseText);
				var reponse = trim(http.responseText).split('**');
				if (reponse[0].length > 2) reponse[0] = 10;
				if (reponse[0] == 0 || reponse[0] == 1) {
					//$('#txt_terms_condision_book_con').val(reponse[1]);
					parent.emailwindow.hide();
					set_button_status(1, permission, 'fnc_fabric_booking_terms_condition', 1, 1);
				}
			}
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<? echo load_freeze_divs("../../../", $permission);  ?>
			<fieldset>
				<form id="termscondi_1" autocomplete="off">
					<input type="text" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'", "", $txt_booking_no) ?>" class="text_boxes" readonly />
					<input type="hidden" id="txt_terms_condision_book_con" name="txt_terms_condision_book_con">

					<table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
						<thead>
							<tr>
								<th width="50">Sl</th>
								<th width="530">Terms</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?
							//echo "select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no";
							$data_array = sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no order by id"); // quotation_id='$data'
							if (count($data_array) > 0) {
								$button_status = 1;
								$i = 0;
								foreach ($data_array as $row) {
									$i++;
							?>
									<tr id="settr_1" align="center">
										<td>
											<? echo $i; ?>
										</td>
										<td>
											<input type="text" id="termscondition_<? echo $i; ?>" name="termscondition_<? echo $i; ?>" style="width:95%" class="text_boxes" value="<? echo $row[csf('terms')]; ?>" />
										</td>
										<td>
											<input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
											<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
										</td>
									</tr>
								<?
								}
							} else {
								$button_status = 0;
								$data_array = sql_select("select id, terms from  lib_yarn_dyeing_terms_con where is_default=1 "); // quotation_id='$data'
								foreach ($data_array as $row) {
									$i++;
								?>
									<tr id="settr_1" align="center">
										<td>
											<? echo $i; ?>
										</td>
										<td>
											<input type="text" id="termscondition_<? echo $i; ?>" name="termscondition_<? echo $i; ?>" style="width:95%" class="text_boxes" value="<? echo $row[csf('terms')]; ?>" />
										</td>
										<td>
											<input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
											<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
										</td>
									</tr>
							<?
								}
							}
							?>
						</tbody>
					</table>
					<table width="650" cellspacing="0" class="" border="0">
						<tr>
							<td align="center" height="15" width="100%"> </td>
						</tr>
						<tr>
							<td align="center" width="100%" class="button_container">
								<?
								echo load_submit_buttons($permission, "fnc_fabric_booking_terms_condition", $button_status, 0, "reset_form('termscondi_1','','','','')", 1);
								?>
							</td>
						</tr>
					</table>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "save_update_delete_fabric_booking_terms_condition") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	if ($operation == 0 || $operation == 1)  // Insert Here and Update Here
	{
		$con = connect();

		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$id = return_next_id("id", "wo_booking_terms_condition", 1);
		$field_array = "id,booking_no,terms";
		for ($i = 1; $i <= $total_row; $i++) {
			$termscondition = "termscondition_" . $i;
			if ($i != 1) $data_array .= ",";
			$data_array .= "(" . $id . "," . $txt_booking_no . "," . $$termscondition . ")";
			$id = $id + 1;
		}
		//echo "INSERT INTO wo_booking_terms_condition (".$field_array.") VALUES ".$data_array;die;
		//echo "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."";
		$rID_de3 = execute_query("delete from wo_booking_terms_condition where  booking_no =" . $txt_booking_no . "", 0);
		$rID = sql_insert("wo_booking_terms_condition", $field_array, $data_array, 1);
		// check_table_status( $_SESSION['menu_id'],0);
		if ($db_type == 0) {
			if ($rID && $rID_de3) {
				mysql_query("COMMIT");
				echo $operation . "**" . $txt_booking_no;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $txt_booking_no;
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID_de3) {
				oci_commit($con);
				echo $operation . "**" . $txt_booking_no;
			} else {
				oci_rollback($con);
				echo "10**" . $txt_booking_no;
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "show_with_multiple_job") {
	extract($_REQUEST);
	$form_name = str_replace("'", "", $form_name);
	$cbo_company_name = str_replace("'", "", $cbo_company_name);
	$update_id = str_replace("'", "", $update_id);
	$txt_booking_no = str_replace("'", "", $txt_booking_no);

	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_name = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$pay_mode = str_replace("'", "", $cbo_pay_mode);
	$new_supplier_name = str_replace("'", "", $cbo_supplier_name);

	$buyer_name_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$brand_arr = return_library_array("select id,brand_name from  lib_brand", 'id', 'brand_name');
	$job_quantity_arr = return_library_array("select job_no,job_quantity from wo_po_details_master", 'job_no', 'job_quantity');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");

	$nameArray = sql_select("select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,a.ecchange_rate, a.is_short from wo_yarn_dyeing_mst a where a.id=$update_id");
	foreach ($nameArray as $result) {
		$work_order = $result[csf('ydw_no')];
		$supplier_id = $result[csf('supplier_id')];
		$booking_date = $result[csf('booking_date')];
		$attention = $result[csf('attention')];
		$delivery_date = $result[csf('delivery_date')];
		$delivery_date_end = $result[csf('delivery_date_end')];
		$dy_delivery_start = $result[csf('dy_delivery_date_start')];
		$dy_delivery_end = $result[csf('dy_delivery_date_end')];
		$currency_id = $result[csf('currency')];
		$exchange_rate = $result[csf('ecchange_rate')];
		$is_short = $result[csf('is_short')];
	}
	$style_no = "select a.style_ref_no, a.job_no, a.buyer_name,b.id as po_id, b.po_number, b.file_no, b.grouping as inter_ref_no, p.id as dtls_id
	from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b
	where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.mst_id=$update_id and  p.status_active=1 and p.is_deleted=0";
	$sql_result = sql_select($style_no);
	$po_ids = '';
	foreach ($sql_result as $row) {
		if ($po_ids == '') $po_ids = $row[csf("po_id")];
		else $po_ids .= "," . $row[csf("po_id")];
	}

	$fab_wo_no_arr = array();
	$fabric_booking = sql_select("select a.booking_no,a.job_no from wo_booking_dtls a,wo_po_break_down b where a.po_break_down_id=b.id and a.booking_type=1 and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  b.id in($po_ids) group by a.booking_no,a.job_no");
	$booking_nos = '';
	foreach ($fabric_booking as $row) {
		if ($booking_nos == '') $booking_nos = $row[csf('booking_no')];
		else $booking_nos .= "," . $row[csf('booking_no')];
	}
?>
	<div style="width:1220px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="100">
				</td>
				<td width="1000">
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
								echo $company_library[$cbo_company_name];
								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result) {
								?>

									Email Address: <? echo $result[csf('email')]; ?>
									Website No: <? echo $result[csf('website')];
											}

												?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong>Yarn Dyeing Work Order <? if ($is_short == 1) echo " (Short) "; ?> </strong>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?
		?>
		<table width="900" style="" align="center">
			<tr>
				<td width="350" style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td width="120"><b>To</b> </td>
							<td width="230">:&nbsp;&nbsp;<?
															if ($pay_mode == 3 || $pay_mode == 5) {
																echo $company_library[$new_supplier_name];
															} else {
																echo $supplier_name[$new_supplier_name];
															}
															//echo $supplier_arr[$supplier_id];
															?></td>
						</tr>

						<tr>
							<td><b>Wo No.</b> </td>
							<td>:&nbsp;&nbsp;<? echo $work_order; ?> </td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td>:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Booking Date</b></td>
							<td>:&nbsp;&nbsp;<? if ($booking_date != "0000-00-00" || $booking_date != "") echo change_date_format($booking_date);
												else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td>:&nbsp;&nbsp;<? echo $currency[$currency_id]; ?></td>
						</tr>
						<tr style="font-size:12px">
							<td><b>Fabric Booking No.</b></td>
							<td>:
								<?
								echo $booking_nos;
								?>
							</td>
						</tr>

					</table>
				</td>
				<td width="350" style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td width="120"><b>G/Y Issue Start</b> </td>
							<td width="230">:&nbsp;&nbsp;<? if ($delivery_date != "0000-00-00" || $delivery_date != "") echo change_date_format($delivery_date);
															else echo ""; ?> </td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td>:&nbsp;&nbsp;<? if ($delivery_date_end != "0000-00-00" || $delivery_date_end != "") echo change_date_format($delivery_date_end);
												else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td>:&nbsp;&nbsp;<? if ($dy_delivery_start != "0000-00-00" || $dy_delivery_start != "") echo change_date_format($dy_delivery_start);
												else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td>:&nbsp;&nbsp;<? if ($dy_delivery_end != "0000-00-00" || $dy_delivery_end != "") echo change_date_format($dy_delivery_end);
												else echo ""; ?></td>
						</tr>
					</table>
				</td>
				<td width="200" style="font-size:12px">
					<?
					$image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$update_id' and form_name='$form_name'", "image_location");
					?>
					<img src="<? echo '../../' . $image_location; ?>" width="120" height="100" border="2" />
				</td>
			</tr>
		</table>
		</br>

		<table width="1220" align="center" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="30" align="center">Sl</th>
					<th width="65" align="center">Color</th>
					<th width="80" align="center">Color Range</th>
					<th width="70" align="center">File No</th>
					<th width="70" align="center">Internal Ref. No</th>
					<th align="center" width="50">Ref No.</th>
					<th align="center" width="70">Style Ref.No.</th>
					<th width="30" align="center">Yarn Count</th>
					<th width="140" align="center">Yarn Description</th>
					<th width="40" align="center">Brand</th>
					<th width="40" align="center">Lot</th>
					<th width="40" align="center">UOM</th>
					<th width="60" align="center">WO Qty</th>
					<th width="50" align="center">Dyeing Rate</th>
					<th width="70" align="center">Amount</th>
					<th align="center" width="40">Min Req. Cone</th>
					<th align="center" width="80">Job No.</th>
					<th align="center" width="80">Buyer</th>
					<th align="center">Order No</th>
				</tr>
			</thead>
			<?

			$product_sql = sql_select("select id, lot, brand from product_details_master");
			$product_data_array = array();
			foreach ($product_sql as $row) {
				$product_data_array[$row[csf("id")]]["lot"] = $row[csf("lot")];
				$product_data_array[$row[csf("id")]]["brand"] = $row[csf("brand")];
			}

			if ($db_type == 0) {
				$sql = "select a.id, a.ydw_no, b.id as dtls_id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty as yarn_wo_qty, b.dyeing_charge, b.amount as amount, b.min_require_cone, b.referance_no, c.style_ref_no, c.buyer_name, group_concat(d.po_number) as po_number, group_concat(d.file_no) as file_no, group_concat(d.grouping) as internal_ref_no
			from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_po_details_master c, wo_po_break_down d
			where a.id=b.mst_id and b.job_no_id=c.id and c.job_no=d.job_no_mst and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 and a.id=$update_id
			group by a.id, a.ydw_no, b.id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty, b.dyeing_charge, b.amount, b.min_require_cone, b.referance_no, c.style_ref_no, c.buyer_name order by b.id";
			} else {
				//listagg(CAST(c.id as VARCHAR(4000)),',') within group (order by c.id) as po_id
				$sql = "select a.id, a.ydw_no, b.id as dtls_id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty as yarn_wo_qty, b.dyeing_charge, b.amount as amount, b.min_require_cone, b.referance_no, c.style_ref_no, c.buyer_name, listagg(CAST(d.po_number as VARCHAR(4000)),',') within group (order by d.po_number) as po_number, listagg(CAST(d.file_no as VARCHAR(4000)),',') within group (order by d.file_no) as file_no, listagg(CAST(d.grouping as VARCHAR(4000)),',') within group (order by d.grouping) as internal_ref_no
			from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_po_details_master c, wo_po_break_down d
			where a.id=b.mst_id and b.job_no_id=c.id and c.job_no=d.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 and a.id=$update_id
			group by a.id, a.ydw_no, b.id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty, b.dyeing_charge, b.amount, b.min_require_cone, b.referance_no, c.style_ref_no, c.buyer_name order by b.id";
			}

			$sql_result = sql_select($sql);
			$total_qty = 0;
			$total_amount = 0;
			$i = 1;
			$buyer = 0;
			$order_no = "";
			$tot_job_no = '';
			$total_dtls_id = 0;
			foreach ($sql_result as $row) {

				if ($tot_job_no == "") $tot_job_no = $row[csf("job_no")];
				else $tot_job_no = $tot_job_no . "," . $row[csf("job_no")];
				if ($total_dtls_id == 0) $total_dtls_id = $row[csf("dtls_id")];
				else $total_dtls_id = $total_dtls_id . "," . $row[csf("dtls_id")];
				$job_strip_color[$row[csf("job_no")]] .= $row[csf("yarn_color")] . ",";
				$all_stripe_color .= $row[csf("yarn_color")] . ",";

				$product_id = $row[csf("product_id")];
				//var_dump($product_id);
				if ($product_id) {
					$lot_amt = $product_data_array[$product_id]["lot"];
					$brand = $product_data_array[$product_id]["brand"];
				}
				$all_job_arr[] = $row[csf("job_no")];
				$all_style_arr[] = $row[csf("style_ref_no")];
				//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);

				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

			?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center">
						<p><? echo $i; ?></p>
					</td>
					<td>
						<p><? echo $color_arr[$row[csf("yarn_color")]]; ?>&nbsp;</p>
					</td>
					<td>
						<p><? echo $color_range[$row[csf("color_range")]]; ?>&nbsp;</p>
					</td>
					<td>
						<p><? echo $row[csf("file_no")]; ?>&nbsp;</p>
					</td>
					<td>
						<p><? echo $row[csf("internal_ref_no")]; ?>&nbsp;</p>
					</td>
					<td align="center">
						<p><? echo $row[csf("referance_no")]; ?>&nbsp;</p>
					</td>
					<td align="center">
						<p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p>
					</td>
					<td align="center">
						<p><? echo $count_arr[$row[csf("count")]]; ?>&nbsp;</p>
					</td>
					<td>
						<p><? echo $row[csf("yarn_description")]; ?> &nbsp;</p>
					</td>
					<td>
						<p><? echo $brand_arr[$brand]; ?>&nbsp;</p>
					</td>
					<td align="center">
						<p><? echo $lot_amt; ?>&nbsp;</p>
					</td>
					<td align="center">
						<p><? echo "KG"; ?>&nbsp;</p>
					</td>
					<td align="right"><? echo $row[csf("yarn_wo_qty")];
										$total_qty += $row[csf("yarn_wo_qty")]; ?></td>
					<td align="right"><? echo $row[csf("dyeing_charge")]; ?></td>
					<td align="right"><? echo number_format($row[csf("amount")], 2);
										$total_amount += $row[csf("amount")]; ?> </td>
					<td align="center">
						<p><? echo $row[csf("min_require_cone")]; ?>&nbsp;</p>
					</td>
					<td align="center">
						<p><? echo $row[csf("job_no")]; ?>&nbsp;</p>
					</td>
					<td align="center">
						<p> <? echo $buyer_arr[$row[csf("buyer_name")]]; ?>&nbsp;</p>
					</td>
					<td align="center">
						<div style="width:120px; word-wrap:break-word"> <? echo implode(",", array_unique(explode(",", $row[csf('po_number')]))); ?> </div>
					</td>
				</tr>
			<?
				$i++;
				$yarn_count_des = "";
				$style_no = "";
			}
			?>
			<tr>
				<td colspan="12" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
				<td align="right"><b><? echo $total_qty; ?></b></td>
				<td align="right">&nbsp;</td>
				<td align="right"><b><? echo number_format($total_amount, 2); ?></b></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<?
			$mcurrency = "";
			$dcurrency = "";
			if ($currency_id == 1) {
				$mcurrency = 'Taka';
				$dcurrency = 'Paisa';
			}
			if ($currency_id == 2) {
				$mcurrency = 'USD';
				$dcurrency = 'CENTS';
			}
			if ($currency_id == 3) {
				$mcurrency = 'EURO';
				$dcurrency = 'CENTS';
			}
			?>
			<tr>
				<td colspan="19" align="center">Total Dyeing Amount (in word): &nbsp;<? echo number_to_words(def_number_format($total_amount, 2, ""), $mcurrency, $dcurrency); //echo number_to_words($total_amount,"USD", "CENTS");
																						?> </td>
			</tr>
		</table>


		<!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		echo get_spacial_instruction($txt_booking_no);
		if ($show_comment == 1) {
			$all_stripe_color = implode(",", array_unique(explode(",", chop($all_stripe_color, ","))));
			$job_cond_arr = array_unique(explode(",", $tot_job_no));
			//$job_cond_arr=array_unique(explode(",",$tot_job_no));
			foreach ($job_cond_arr as $job_no_st) {
				$job_cond_string .= "'" . $job_no_st . "',";
			}
			$job_cond_string = chop($job_cond_string, ",");
			//echo $job_cond_string.jahid;die;
			$condition = new condition();
			if (str_replace("'", "", $job_cond_string) != '') {
				$condition->job_no(" in($job_cond_string)");
			}
			$condition->init();
			$conversion = new conversion($condition);
			$conversion_costing_arr_process = $conversion->getAmountArray_by_jobAndProcess();
		?>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1220" class="rpt_table">
				<thead>
					<tr>
						<th colspan="9" align="center"><b> Comments</b> </th>
					</tr>
					<tr>
						<th width="50">SL</th>
						<th width="150">Job No</th>
						<th width="200">PO No</th>
						<th width="200">Ship Date</th>
						<th width="100">Pre-Cost Value</th>
						<th width="110">WO Value</th>
						<th width="110">Short WO Value</th>
						<th width="110">Balance</th>
						<th>Comments </th>
					</tr>
				</thead>
				<tbody>
					<?

					if ($db_type == 0) {
						$job_po_sql = sql_select("select job_no_mst, group_concat(po_number) as po_number, group_concat(shipment_date) as shipment_date from wo_po_break_down where job_no_mst in('" . implode($job_cond_arr, "','") . "') and status_active=1 and is_deleted=0 group by job_no_mst");
					} else {
						$job_po_sql = sql_select("select job_no_mst, listagg(cast(po_number as varchar(4000)),',') within group(order by po_number) as po_number, listagg(cast(shipment_date as varchar(4000)),',') within group(order by shipment_date) as shipment_date from wo_po_break_down where job_no_mst in('" . implode($job_cond_arr, "','") . "') and status_active=1 and is_deleted=0 group by job_no_mst");
					}
					$job_po_data = array();
					foreach ($job_po_sql as $row) {
						$job_po_data[$row[csf("job_no_mst")]]["po_number"] = $row[csf("po_number")];
						$job_po_data[$row[csf("job_no_mst")]]["shipment_date"] = $row[csf("shipment_date")];
					}


					$prev_wo_data = sql_select("select b.job_no_id, b.yarn_color,
				sum(case when a.is_short<>1 then b.amount else 0 end) as amount,
				sum(case when a.is_short=1 then b.amount else 0 end) as short_amount
				from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.entry_form=41 and b.status_active=1 and b.is_deleted=0 and b.job_no in('" . implode($job_cond_arr, "','") . "') and b.id not in($total_dtls_id) group by b.job_no_id, b.yarn_color");
					foreach ($prev_wo_data as $row) {
						$prev_job_entry[$row[csf("job_no_id")]][$row[csf("yarn_color")]] += $row[csf("amount")];
						$prev_job_short_entry[$row[csf("job_no_id")]][$row[csf("yarn_color")]] += $row[csf("short_amount")];
					}
					if ($db_type == 0) {
						$yarn_data = "select b.job_no_id, max(a.currency) as currency, b.job_no,
				sum(case when a.is_short<>1 then b.amount else 0 end) as amount,
				sum(case when a.is_short=1 then b.amount else 0 end) as short_amount, group_concat(b.yarn_color) as yarn_color
				from wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst a
				where a.id=b.mst_id and b.id in($total_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				group by b.job_no_id, b.job_no";
					} else {
						$yarn_data = "select b.job_no_id, max(a.currency) as currency, b.job_no,
				sum(case when a.is_short<>1 then b.amount else 0 end) as amount,
				sum(case when a.is_short=1 then b.amount else 0 end) as short_amount,
				listagg( cast(b.yarn_color as varchar(4000)),',') within group (order by b.yarn_color) as yarn_color
				from wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst a
				where a.id=b.mst_id and b.id in($total_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				group by b.job_no_id, b.job_no";
					}

					//echo $yarn_data."<br>";

					$nameArray = sql_select($yarn_data);
					foreach ($nameArray as $selectResult) {

						$prev_qnty = 0;
						$strip_color_arr = array_unique(explode(",", $selectResult[csf("yarn_color")]));
						foreach ($strip_color_arr as $strip_id) {
							$prev_qnty += $prev_job_entry[$selectResult[csf("job_no_id")]][$strip_id];
							$prev_short_qnty += $prev_job_short_entry[$selectResult[csf("job_no_id")]][$strip_id];
						}

						if ($selectResult[csf("currency")] == 2) {
							$tot_yarn_dyeing = $selectResult[csf("amount")] + $prev_qnty;
							$tot_yarn_dyeing_short = $selectResult[csf("short_amount")] + $prev_short_qnty;
						} else {
							$current_date = date("Y-m-d");
							$currency_rate = set_conversion_rate(2, $current_date);
							$tot_yarn_dyeing = $selectResult[csf("amount")] + $prev_qnty;
							$tot_yarn_dyeing = $tot_yarn_dyeing / $currency_rate;

							$tot_yarn_dyeing_short = $selectResult[csf("short_amount")] + $prev_short_qnty;
							$tot_yarn_dyeing_short = $tot_yarn_dyeing_short / $currency_rate;
						}
						//echo $convamount;
						$po_no = $job_po_data[$selectResult[csf('job_no')]]['po_number'];
						$shipment_date = array_unique(explode(",", $job_po_data[$selectResult[csf('job_no')]]['shipment_date']));
						$ship_date = "";
						foreach ($shipment_date as $date_row) {
							if ($ship_date == '') $ship_date = change_date_format($date_row);
							else $ship_date .= "," . change_date_format($date_row);
						}
						$pre_cost_yarn_deying = $conversion_costing_arr_process[$selectResult[csf('job_no')]][30];
						//$job_wise_badge_val[$selectResult[csf('job_no')]];
					?>
						<tr>
							<td align="center"><? echo $i; ?></td>
							<td>
								<p><? echo $selectResult[csf('job_no')]; ?>&nbsp;</p>
							</td>
							<td>
								<p><? echo $po_no; ?>&nbsp;</p>
							</td>
							<td>
								<p><? echo $ship_date; ?>&nbsp;</p>
							</td>
							<td align="right"><? echo number_format($pre_cost_yarn_deying, 2); ?></td>
							<td align="right"><? echo number_format($tot_yarn_dyeing, 2); ?> </td>
							<td align="right"><? echo number_format($tot_yarn_dyeing_short, 2); ?> </td>
							<td align="right"><? $tot_balance = $pre_cost_yarn_deying - $tot_yarn_dyeing;
												echo number_format($tot_balance, 2); ?></td>
							<td>
								<?
								if ($pre_cost_yarn_deying > $tot_yarn_dyeing) {
									echo "Less Booking";
								} else if ($pre_cost_yarn_deying < $tot_yarn_dyeing) {
									echo "Over Booking";
								} else if ($pre_cost_yarn_deying == $tot_yarn_dyeing) {
									echo "As Per";
								} else {
									echo "&nbsp;";
								}
								?>
							</td>
						</tr>
					<?
						$tot_pre_yarn_dyeing += $pre_cost_yarn_deying;
						$total_yarn_dyeing += $tot_yarn_dyeing;
						$total_yarn_dyeing_short += $tot_yarn_dyeing_short;
						$tot_balance_yarn_dyeing += $tot_balance;
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="4" align="right"> <b>Total</b></th>
						<th align="right"><? echo number_format($tot_pre_yarn_dyeing, 2); ?></th>
						<th align="right"><? echo number_format($total_yarn_dyeing, 2); ?></th>
						<th align="right"><? echo number_format($total_yarn_dyeing_short, 2); ?></th>
						<th align="right"><? echo number_format($tot_balance_yarn_dyeing, 2); ?></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
		<?
		}
		?>
	</div>
	<div>
		<?
		echo signature_table(43, $cbo_company_name, "1220px");
		echo "****" . custom_file_name($txt_booking_no, implode(',', $all_style_arr), implode(',', $all_job_arr));
		?>
	</div>
<?
}

if ($action == "show_with_multiple_job_without_rate") {

	//echo "uuuu";die;
	//echo load_html_head_contents("Yarn Dyeing WO", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$form_name = str_replace("'", "", $form_name);
	$cbo_company_name = str_replace("'", "", $cbo_company_name);
	$update_id = str_replace("'", "", $update_id);
	$txt_booking_no = str_replace("'", "", $txt_booking_no);

	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_name = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$pay_mode = str_replace("'", "", $cbo_pay_mode);
	$new_supplier_name = str_replace("'", "", $cbo_supplier_name);

	$buyer_name_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$brand_arr = return_library_array("select id,brand_name from  lib_brand", 'id', 'brand_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
?>
	<div style="width:1140px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="100">
				</td>
				<td width="900">
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
								echo $company_library[$cbo_company_name];
								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result) {
								?>
									Email Address: <? echo $result[csf('email')]; ?>
									Website No: <? echo $result[csf('website')];
											}
												?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong>Yarn Dyeing Work Order </strong>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;
		$nameArray = sql_select("select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.pay_mode,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id");
		foreach ($nameArray as $result) {
			$work_order = $result[csf('ydw_no')];
			$supplier_id = $result[csf('supplier_id')];
			$booking_date = $result[csf('booking_date')];
			$currency_id = $result[csf('currency')];
			$attention = $result[csf('attention')];
			$pay_mode_id = $result[csf('pay_mode')];
			$delivery_date = $result[csf('delivery_date')];
			$delivery_date_end = $result[csf('delivery_date_end')];
			$dy_delivery_start = $result[csf('dy_delivery_date_start')];
			$dy_delivery_end = $result[csf('dy_delivery_date_end')];
		}
		$style_no = "select a.style_ref_no, a.job_no, a.buyer_name,b.id as po_id, b.po_number, b.file_no, b.grouping as inter_ref_no, p.id as dtls_id
		from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b
		where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.mst_id=$update_id and  p.status_active=1 and p.is_deleted=0";
		$sql_result = sql_select($style_no);
		$po_ids = '';
		foreach ($sql_result as $row) {
			if ($po_ids == '') $po_ids = $row[csf("po_id")];
			else $po_ids .= "," . $row[csf("po_id")];
		}

		$fab_wo_no_arr = array();
		$fabric_booking = sql_select("select a.booking_no,a.job_no from wo_booking_dtls a,wo_po_break_down b where a.po_break_down_id=b.id and a.booking_type=1 and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  b.id in($po_ids) group by a.booking_no,a.job_no");
		$booking_nos = '';
		foreach ($fabric_booking as $row) {
			if ($booking_nos == '') $booking_nos = $row[csf('booking_no')];
			else $booking_nos .= "," . $row[csf('booking_no')];
		}
		?>
		<style type="text/css">
			.word_wrap_break {
				word-wrap: break-word;
				word-break: break-all;
			}
		</style>
		<table width="900" align="left">
			<tr>
				<td width="350" style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td width="120"><b>To</b> </td>
							<td width="230">:&nbsp;&nbsp;<?
															if ($pay_mode == 3 || $pay_mode == 5) {
																echo $company_library[$new_supplier_name];
															} else {
																echo $supplier_name[$new_supplier_name];
															}

															// if($pay_mode_id==5 || $pay_mode_id==3){
															// 	echo $company_library[$supplier_id];
															// }
															// else{
															// 	echo $supplier_arr[$supplier_id];
															// }
															?></td>
						</tr>

						<tr>
							<td><b>Wo No.</b> </td>
							<td>:&nbsp;&nbsp;<? echo $work_order; ?> </td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td>:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Booking Date</b></td>
							<td>:&nbsp;&nbsp;<? if ($booking_date != "0000-00-00" || $booking_date != "") echo change_date_format($booking_date);
												else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td>:&nbsp;&nbsp;<? echo $currency[$currency_id]; ?></td>
						</tr>
						<tr>
							<td><b>Fabric Booking No.</b></td>
							<td colspan="4">:&nbsp;&nbsp;<p><? echo $booking_nos ?></p>
							</td>
						</tr>
					</table>
				</td>
				<td width="350" style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td width="120"><b>G/Y Issue Start</b> </td>
							<td width="230">:&nbsp;&nbsp;<? if ($delivery_date != "0000-00-00" || $delivery_date != "") echo change_date_format($delivery_date);
															else echo ""; ?> </td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td>:&nbsp;&nbsp;<? if ($delivery_date_end != "0000-00-00" || $delivery_date_end != "") echo change_date_format($delivery_date_end);
												else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td>:&nbsp;&nbsp;<? if ($dy_delivery_start != "0000-00-00" || $dy_delivery_start != "") echo change_date_format($dy_delivery_start);
												else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td>:&nbsp;&nbsp;<? if ($dy_delivery_end != "0000-00-00" || $dy_delivery_end != "") echo change_date_format($dy_delivery_end);
												else echo ""; ?></td>
						</tr>
					</table>
				</td>
				<td width="200" style="font-size:12px">
					<?
					$image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$update_id' and form_name='$form_name'", "image_location");
					?>
					<img src="<? echo '../../' . $image_location; ?>" width="120" height="100" border="2" />
				</td>
			</tr>
		</table>
		</br>

		<table width="1140" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="30" align="center">Sl</th>
					<th width="65" align="center">Color</th>
					<th width="80" align="center">Color Range</th>
					<th align="center" width="50">Ref No.</th>
					<th align="center" width="70">Style Ref.No.</th>
					<th width="30" align="center">Yarn Count</th>
					<th width="140" align="center">Yarn Description</th>
					<th width="60" align="center">Brand</th>
					<th width="50" align="center">Lot</th>
					<th width="40" align="center">UOM</th>
					<th width="60" align="center">WO Qty</th>
					<th align="center" width="50">Min Req. Cone</th>
					<th width="80">Job No.</th>
					<th width="80">Buyer</th>
					<th width="110">Order No</th>
					<th>File No <br> Internal Ref No</th>
				</tr>
			</thead>
			<?
			/*if($db_type==0) $select_field_grp="group by  b.job_no_id, b.count, b.yarn_color, b.color_range order by b.id";
            else if($db_type==2) $select_field_grp="group by b.job_no_id, b.yarn_color, b.color_range,a.id, a.ydw_no,b.id,b.product_id,b.job_no,b.yarn_description,b.count,b.color_range,b.dyeing_charge,b.min_require_cone,b.referance_no order by b.id ";
            $multi_job_arr=array();
            $style_no=sql_select("select a.style_ref_no,a.job_no,a.buyer_name,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.status_active=1 and b.is_deleted=0");

            foreach($style_no as $row_s)
            {
            $multi_job_arr[$row_s[csf('job_no')]]['style']=$row_s[csf('style_ref_no')];
            $multi_job_arr[$row_s[csf('job_no')]]['buyer']=$row_s[csf('buyer_name')];
            $multi_job_arr[$row_s[csf('job_no')]]['po_no'].=$row_s[csf('po_number')].",";
        }	*/

			$product_sql = sql_select("select id, lot, brand from product_details_master");
			$product_data_array = array();
			foreach ($product_sql as $row) {
				$product_data_array[$row[csf("id")]]["lot"] = $row[csf("lot")];
				$product_data_array[$row[csf("id")]]["brand"] = $row[csf("brand")];
			}

			if ($db_type == 0) {
				$sql = "select a.id, a.ydw_no, b.id as dtls_id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty as yarn_wo_qty, b.dyeing_charge, b.amount as amount, b.min_require_cone, b.referance_no, c.style_ref_no, c.buyer_name, group_concat(d.po_number) as po_number, group_concat(d.file_no) as file_no, group_concat(d.grouping) as internal_ref_no
        	from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_po_details_master c, wo_po_break_down d
        	where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and b.job_no_id=c.id and c.job_no=d.job_no_mst and a.id=$update_id
        	group by a.id, a.ydw_no, b.id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty, b.dyeing_charge, b.amount, b.min_require_cone, b.referance_no, c.style_ref_no, c.buyer_name order by b.id";
			} else {
				//listagg(CAST(c.id as VARCHAR(4000)),',') within group (order by c.id) as po_id
				$sql = "select a.id, a.ydw_no, b.id as dtls_id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty as yarn_wo_qty, b.dyeing_charge, b.amount as amount, b.min_require_cone, b.referance_no, c.style_ref_no, c.buyer_name, listagg(CAST(d.po_number as VARCHAR(4000)),',') within group (order by d.po_number) as po_number, listagg(CAST(d.file_no as VARCHAR(4000)),',') within group (order by d.file_no) as file_no, listagg(CAST(d.grouping as VARCHAR(4000)),',') within group (order by d.grouping) as internal_ref_no
        	from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_po_details_master c, wo_po_break_down d
        	where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and b.job_no_id=c.id and c.job_no=d.job_no_mst and a.id=$update_id
        	group by a.id, a.ydw_no, b.id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty, b.dyeing_charge, b.amount, b.min_require_cone, b.referance_no, c.style_ref_no, c.buyer_name order by b.id";
			}
			//echo $sql;
			$sql_result = sql_select($sql);
			$total_qty = 0;
			$total_amount = 0;
			$i = 1;
			$buyer = 0;
			$order_no = "";
			foreach ($sql_result as $row) {
				$product_id = $row[csf("product_id")];
				//var_dump($product_id);
				if ($product_id) {
					$lot_amt = $product_data_array[$product_id]["lot"];
					$brand = $product_data_array[$product_id]["brand"];
				}
				$all_job_arr[] = $row[csf("job_no")];
				$all_style_arr[] = $row[csf("style_ref_no")];
				//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);

				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

			?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center" valign="middle"><? echo $i; ?></td>
					<td valign="top">
						<p><? echo $color_arr[$row[csf("yarn_color")]]; ?></p>
					</td>
					<td valign="top">
						<p><? echo $color_range[$row[csf("color_range")]]; ?></p>
					</td>
					<td align="center" valign="top">
						<p><? echo $row[csf("referance_no")]; ?></p>
					</td>
					<td align="center" valign="top">
						<p> <? echo $row[csf("style_ref_no")]; ?> </p>
					</td>
					<td align="center" valign="top">
						<p><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; 
							?></p>
					</td>
					<td valign="top">
						<p class="word_wrap_break"><? echo $row[csf("yarn_description")]; ?> </p>
					</td>
					<td valign="top">
						<p><? echo $brand_arr[$brand]; ?></p>
					</td>
					<td align="center" valign="top">
						<p><? echo $lot_amt; ?></p>
					</td>
					<td align="center" valign="top">
						<p><? echo "KG"; ?></p>
					</td>
					<td align="right"><? echo $row[csf("yarn_wo_qty")];
										$total_qty += $row[csf("yarn_wo_qty")]; ?></td>
					<td align="center" valign="top">
						<p><? echo $row[csf("min_require_cone")]; ?></p>
					</td>
					<td align="center" valign="top">
						<p><? echo $row[csf("job_no")]; ?></p>
					</td>
					<td align="center" valign="top">
						<p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?> </p>
					</td>
					<td align="center" valign="top">
						<p class="word_wrap_break"><? echo implode(",", array_unique(explode(",", $row[csf("po_number")]))); ?> </p>
					</td>
					<td align="center" valign="top">
						<p class="word_wrap_break"><? echo implode(",", array_unique(explode(",", $row[csf("file_no")]))) . "<br>" . implode(",", array_unique(explode(",", $row[csf("internal_ref_no")]))); ?> </p>
					</td>
				</tr>
			<?
				$i++;
				$yarn_count_des = "";
				$style_no = "";
			}
			?>
			<tr>
				<td colspan="10" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
				<td align="right"><b><? echo $total_qty; ?></b></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>

		<? echo get_spacial_instruction($txt_booking_no); ?>
		<br>
		<?
		$lib_designation_arr = return_library_array(" select id,custom_designation from lib_designation", "id", "custom_designation");
		$user_lib_designation_arr = return_library_array("SELECT id,designation from user_passwd", "id", "designation");
		$user_lib_name_arr = return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
		$mst_id = return_field_value("id as mst_id", "wo_yarn_dyeing_mst", "ydw_no='$txt_booking_no'", "mst_id");
		//echo $mst_id.'ssD';
		//and b.un_approved_date is null
		$approve_data_array = sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=30  group by  b.approved_by order by b.approved_by asc");

		$unapprove_data_array = sql_select("select b.id,b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=30  order by b.approved_date,b.approved_by");

		if (count($approve_data_array) > 0) {
		?>
			<table width="850" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
				<thead>
					<tr style="border:1px solid black;">
						<th colspan="5" style="border:1px solid black;">Approval Status</th>
					</tr>
					<tr style="border:1px solid black;">
						<th width="3%" style="border:1px solid black;">Sl</th>
						<th width="40%" style="border:1px solid black;">Name</th>
						<th width="30%" style="border:1px solid black;">Designation</th>
						<th width="27%" style="border:1px solid black;">Approval Date</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					foreach ($approve_data_array as $row) {
					?>
						<tr style="border:1px solid black;">
							<td width="3%" style="border:1px solid black;"><? echo $i; ?></td>
							<td width="40%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]]; ?></td>
							<td width="30%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]]; ?></td>
							<td width="27%" style="border:1px solid black;text-align:center"><? echo date("d-m-Y h:i:s", strtotime($row[csf('approved_date')])); ?></td>

						</tr>
					<?
						$i++;
					}
					?>
				</tbody>
			</table>
		<?
		}
		?>
		<br>
		<?
		if (count($unapprove_data_array) > 0) {
			$sql_unapproved = sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=29 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id");
			$unapproved_request_arr = array();
			foreach ($sql_unapproved as $rowu) {
				$unapproved_request_arr[$rowu[csf('booking_id')]] = $rowu[csf('approval_cause')];
			}
		?>
			<table width="850" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
				<thead>
					<tr style="border:1px solid black;">
						<th colspan="6" style="border:1px solid black;">Approval/Un Approval History</th>
					</tr>
					<tr style="border:1px solid black;">
						<th width="3%" style="border:1px solid black;">Sl</th>
						<th width="30%" style="border:1px solid black;">Name</th>
						<th width="20%" style="border:1px solid black;">Designation</th>
						<th width="5%" style="border:1px solid black;">Approval Status</th>
						<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
						<th width="22%" style="border:1px solid black;"> Date</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					foreach ($unapprove_data_array as $row) {

					?>
						<tr style="border:1px solid black;">
							<td width="3%" style="border:1px solid black;"><? echo $i; ?></td>
							<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]]; ?></td>
							<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]]; ?></td>
							<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes'; ?></td>
							<td width="20%" style="border:1px solid black;"><? echo ''; ?></td>
							<td width="22%" style="border:1px solid black;text-align:center"><? if ($row[csf('approved_date')] != "") echo date("d-m-Y h:i:s", strtotime($row[csf('approved_date')]));
																								else echo ""; ?></td>
						</tr>
						<?
						$i++;
						$un_approved_date = explode(" ", $row[csf('un_approved_date')]);
						$un_approved_date = $un_approved_date[0];
						if ($db_type == 0) //Mysql
						{
							if ($un_approved_date == "" || $un_approved_date == "0000-00-00") $un_approved_date = "";
							else $un_approved_date = $un_approved_date;
						} else {
							if ($un_approved_date == "") $un_approved_date = "";
							else $un_approved_date = $un_approved_date;
						}

						if ($un_approved_date != "") {
						?>
							<tr style="border:1px solid black;">
								<td width="3%" style="border:1px solid black;"><? echo $i; ?></td>
								<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]]; ?></td>
								<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]]; ?></td>
								<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No'; ?></td>
								<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id]; ?></td>
								<td width="22%" style="border:1px solid black;text-align:center"><? if ($row[csf('un_approved_date')] != "") echo date("d-m-Y h:i:s", strtotime($row[csf('un_approved_date')]));
																									else echo ""; ?></td>
							</tr>

					<?
							$i++;
						}
					}
					?>
				</tbody>
			</table>
		<?
		}
		?>
		<br />

	</div>
	<div>
		<?
		echo signature_table(43, $cbo_company_name, "1140px");
		echo "****" . custom_file_name($txt_booking_no, implode(',', $all_style_arr), implode(',', $all_job_arr));
		?>
	</div>
<?
}

if ($action == "show_without_rate_booking_report") {
	//echo "uuuu";die;
	//echo load_html_head_contents("Yarn Dyeing WO", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$form_name = str_replace("'", "", $form_name);
	$cbo_company_name = str_replace("'", "", $cbo_company_name);
	$update_id = str_replace("'", "", $update_id);
	$txt_booking_no = str_replace("'", "", $txt_booking_no);
	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_name = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$pay_mode = str_replace("'", "", $cbo_pay_mode);
	$new_supplier_name = str_replace("'", "", $cbo_supplier_name);

	$buyer_name_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$brand_arr = return_library_array("select id,brand_name from  lib_brand", 'id', 'brand_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
?>
	<div style="width:1000px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>

				<td width="700">
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
								echo $company_library[$cbo_company_name];
								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result) {
								?>

									Email Address: <? echo $result[csf('email')]; ?>
									Website No: <? echo $result[csf('website')];
											}

												?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong>Yarn Dyeing Work Order </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">

				</td>
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;
		$nameArray = sql_select("select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.pay_mode,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id");
		foreach ($nameArray as $result) {
			$work_order = $result[csf('ydw_no')];
			$supplier_id = $result[csf('supplier_id')];
			$booking_date = $result[csf('booking_date')];
			$currency_id = $result[csf('currency')];
			$attention = $result[csf('attention')];
			$delivery_date = $result[csf('delivery_date')];
			$pay_mode_id = $result[csf('pay_mode')];
			$delivery_date_end = $result[csf('delivery_date_end')];
			$dy_delivery_start = $result[csf('dy_delivery_date_start')];
			$dy_delivery_end = $result[csf('dy_delivery_date_end')];
		}
		$varcode_work_order_no = $work_order;

		?>
		<table width="950" style="" align="center">
			<tr>
				<td width="350" style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td width="120"><b>To</b> </td>
							<td width="230">:&nbsp;&nbsp;<?

															if ($pay_mode == 3 || $pay_mode == 5) {
																echo $company_library[$new_supplier_name];
															} else {
																echo $supplier_name[$new_supplier_name];
															}

															// if($pay_mode_id==5 || $pay_mode_id==3){
															// 	echo $company_library[$supplier_id];
															// }
															// else{
															// 	echo $supplier_arr[$supplier_id];
															// }
															//echo $supplier_arr[$supplier_id];
															?></td>
						</tr>

						<tr>
							<td><b>Wo No.</b> </td>
							<td>:&nbsp;&nbsp;<? echo $work_order; ?> </td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td>:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Booking Date</b></td>
							<td>:&nbsp;&nbsp;<? if ($booking_date != "0000-00-00" || $booking_date != "") echo change_date_format($booking_date);
												else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td>:&nbsp;&nbsp;<? echo $currency[$currency_id]; ?></td>
						</tr>
					</table>
				</td>
				<td width="350" style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td width="120"><b>G/Y Issue Start</b> </td>
							<td width="230">:&nbsp;&nbsp;<? if ($delivery_date != "0000-00-00" || $delivery_date != "") echo change_date_format($delivery_date);
															else echo ""; ?> </td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td>:&nbsp;&nbsp;<? if ($delivery_date_end != "0000-00-00" || $delivery_date_end != "") echo change_date_format($delivery_date_end);
												else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td>:&nbsp;&nbsp;<? if ($dy_delivery_start != "0000-00-00" || $dy_delivery_start != "") echo change_date_format($dy_delivery_start);
												else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td>:&nbsp;&nbsp;<? if ($dy_delivery_end != "0000-00-00" || $dy_delivery_end != "") echo change_date_format($dy_delivery_end);
												else echo ""; ?></td>
						</tr>
					</table>
				</td>
				<td width="250" style="font-size:12px">
					<?
					$image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$update_id' and form_name='$form_name'", "image_location");
					?>
					<img src="<? echo '../../' . $image_location; ?>" width="120" height="100" border="2" />
				</td>
			</tr>
		</table>
		</br>

		<?

		$style_no = "select a.style_ref_no, a.job_no, a.buyer_name,b.id as po_id, b.po_number, b.file_no, b.grouping as inter_ref_no, p.id as dtls_id
	from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b
	where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.mst_id=$update_id and  p.status_active=1 and p.is_deleted=0";

		//echo $style_no;

		$sql_result = sql_select($style_no);
		$style_all = $total_dtls_id = $tot_job_no = $total_buyer = $total_order_no = $all_file = $all_inter_ref = "";
		$po_ids = '';
		foreach ($sql_result as $row) {
			$total_dtls_id .= $row[csf("dtls_id")] . ",";
			$style_all .= $row[csf("style_ref_no")] . ",";
			$tot_job_no .= $row[csf("job_no")] . ",";
			$total_buyer .= $row[csf("buyer_name")] . ",";
			$total_order_no .= $row[csf("po_number")] . ",";
			$all_file .= $row[csf("file_no")] . ",";
			$all_inter_ref .= $row[csf("inter_ref_no")] . ",";
			if ($po_ids == '') $po_ids = $row[csf("po_id")];
			else $po_ids .= "," . $row[csf("po_id")];
		}
		$total_dtls_id = chop($total_dtls_id, " , ");
		$style_all = chop($style_all, " , ");
		$tot_job_no = chop($tot_job_no, " , ");
		$total_buyer = chop($total_buyer, " , ");
		$total_order_no = chop($total_order_no, " , ");
		$all_file = chop($all_file, " , ");
		$all_inter_ref = chop($all_inter_ref, " , ");
		$fab_wo_no_arr = array();
		$fabric_booking = sql_select("select a.booking_no,a.job_no from wo_booking_dtls a,wo_po_break_down b where a.po_break_down_id=b.id and a.booking_type=1 and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  b.id in($po_ids) group by a.booking_no,a.job_no");
		$booking_nos = '';
		foreach ($fabric_booking as $row) {
			if ($booking_nos == '') $booking_nos = $row[csf('booking_no')];
			else $booking_nos .= "," . $row[csf('booking_no')];
		}
		?>

		<table width="950" align="center">
			<tr style="font-size:12px">
				<td width="120"><b>Style </b></td>
				<td width="830" valign="top">:&nbsp;
					<?
					$style_all_arr = array_unique(explode(",", $style_all));
					$all_style = "";
					foreach ($style_all_arr as $row) {
						$all_style .= $row . ",";
					}
					$all_style = chop($all_style, " , ");
					echo $all_style;
					?>
				</td>
			</tr>
			<tr style="font-size:12px">
				<td><b>Job No </b></td>
				<td valign="top">:&nbsp;
					<?
					$all_job_arr = array_unique(explode(",", $tot_job_no));
					$all_job = "";
					foreach ($all_job_arr as $row) {
						$all_job .= $row . ",";
					}
					$all_job = chop($all_job, " , ");
					echo $all_job;
					?>
				</td>
			</tr>
			<tr style="font-size:12px">
				<td valign="top"><b>Buyer </b> </td>
				<td valign="top">:&nbsp;
					<?
					$buyer_id_arr = array_unique(explode(",", $total_buyer));
					$all_buyer = "";
					foreach ($buyer_id_arr as $row) {
						$all_buyer .= $buyer_name_arr[$row] . ",";
					}
					$all_buyer = chop($all_buyer, " , ");
					echo $all_buyer;
					?>
				</td>
			</tr>
			<tr style="font-size:12px">
				<td valign="top"><b>Order No</b></td>
				<td valign="top">: &nbsp;
					<?
					$all_order_arr = array_unique(explode(",", $total_order_no));
					$all_order = "";
					foreach ($all_order_arr as $row) {
						$all_order .= $row . ",";
					}
					$all_order = chop($all_order, " , ");
					echo $all_order;
					?>
				</td>
			</tr>
			<tr style="font-size:12px">
				<td valign="top"><b>Fabric Booking No.</b></td>
				<td valign="top">: &nbsp;
					<?
					echo $booking_nos;
					?>
				</td>
			</tr>

		</table>
		<table width="950" align="center" style="font-size:12px">
			<tr>
				<td width="120" valign="top"><b>File No</b></td>
				<td width="355" valign="top">: &nbsp;
					<?
					$all_file_arr = array_unique(explode(",", $all_file));
					$all_file = "";
					foreach ($all_file_arr as $row) {
						$all_file .= $row . ",";
					}
					$all_file = chop($all_file, " , ");
					echo $all_file;
					?>
				</td>
				<td width="120" valign="top"><b>Internal Ref. No</b></td>
				<td valign="top">: &nbsp;
					<?
					$all_inter_ref_arr = array_unique(explode(",", $all_inter_ref));
					$all_ref = "";
					foreach ($all_inter_ref_arr as $row) {
						$all_ref .= $row . ",";
					}
					$all_ref = chop($all_ref, " , ");
					echo $all_ref;
					?>
				</td>
			</tr>
		</table>



		<table width="1000" style="" align="center" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="40" align="center">Sl</th>
					<th width="70" align="center">Color</th>
					<th width="85" align="center">Color Range</th>
					<th align="center" width="70">Ref NO.</th>
					<th width="40" align="center">Yarn Count</th>
					<th width="190" align="center">Yarn Description</th>
					<th width="70" align="center">Brand</th>
					<th width="65" align="center">Lot</th>
					<th width="50" align="center">UOM</th>
					<th width="70" align="center">WO Qty</th>
					<th align="center" width="65">Min Req. Cone</th>
					<th align="center">Remarks/Shade</th>
				</tr>
			</thead>
			<?
			if ($db_type == 0) $select_f_grp = "group by count, yarn_color, color_range order by id";
			else if ($db_type == 2) $select_f_grp = "group by yarn_color, color_range,id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,remarks order by id ";

			$sql_color = "select id,product_id,job_no,job_no_id,yarn_color,yarn_description,count,color_range,sum(yarn_wo_qty) as yarn_wo_qty,dyeing_charge,sum(amount) as amount,min_require_cone,referance_no,remarks
		from
		wo_yarn_dyeing_dtls
		where
		status_active=1 and id in($total_dtls_id) $select_f_grp";
			//echo $sql_color;die;
			$sql_result = sql_select($sql_color);
			$total_qty = 0;
			$total_amount = 0;
			$i = 1;
			$buyer = 0;
			$order_no = "";
			foreach ($sql_result as $row) {
				$product_id = $row[csf("product_id")];
				if ($product_id) {
					$sql_brand = sql_select("select lot,brand from product_details_master where id in($product_id)");
					foreach ($sql_brand as $row_barand) {
						$lot_amt = $row_barand[csf("lot")];
						$brand = $row_barand[csf("brand")];
					}
				}


				//$yarn_count=explode(" ",$row[csf("yarn_description")]);

				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
			?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td><? echo $color_arr[$row[csf("yarn_color")]]; ?></td>
					<td><? echo $color_range[$row[csf("color_range")]]; ?></td>
					<td align="center"><? echo $row[csf("referance_no")]; ?></td>
					<!--<td align="center">
				<?
				/*$style_no=return_field_value( "style_ref_no", " wo_po_details_master","job_no='".$row[csf("job_no")]."'");
				 echo $style_no;*/
				?>
			</td>-->
					<td align="center"><? echo $count_arr[$row[csf("count")]]; ?></td>
					<td>
						<?

						echo $row[csf("yarn_description")];
						//echo $row[csf("yarn_description")];
						?>
					</td>
					<td><? echo $brand_arr[$brand]; ?></td>
					<td align="center"><? echo $lot_amt; ?></td>

					<td align="center"><? echo "KG"; ?></td>
					<td align="right"><? echo $row[csf("yarn_wo_qty")];
										$total_qty += $row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
					<td align="center"><? echo $row[csf("min_require_cone")]; ?></td>
					<td align="center"><? echo $row[csf("remarks")]; ?></td>
				</tr>
			<?
				$i++;
			}
			?>
			<tr>
				<td colspan="9" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
				<td align="right"><b><? echo $total_qty; ?></b></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<? echo get_spacial_instruction($txt_booking_no); ?>

	</div>
	<div>
		<?
		echo signature_table(43, $cbo_company_name, "1000px");
		echo "****" . custom_file_name($txt_booking_no, $all_style, $all_job);
		?>
	</div>

	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>', 'barcode_img_id');
	</script>
<?
}

if ($action == "show_trim_booking_report") {
	//echo "uuuu";die;
	//echo load_html_head_contents("Yarn Dyeing WO", "../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$form_name = str_replace("'", "", $form_name);
	$cbo_company_name = str_replace("'", "", $cbo_company_name);
	$update_id = str_replace("'", "", $update_id);
	//echo $update_id.'DD';die;
	$txt_booking_no = str_replace("'", "", $txt_booking_no);

	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_name = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$pay_mode = str_replace("'", "", $cbo_pay_mode);
	$new_supplier_name = str_replace("'", "", $cbo_supplier_name);

	$buyer_name_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$brand_arr = return_library_array("select id,brand_name from  lib_brand", 'id', 'brand_name');
	$job_quantity_arr = return_library_array("select job_no,job_quantity from wo_po_details_master", 'job_no', 'job_quantity');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//echo $show_comment;

	$nameArray = sql_select("SELECT a.id, a.ydw_no,a.booking_date,a.supplier_id,a.pay_mode,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,a.ecchange_rate, a.is_short, a.inserted_by from wo_yarn_dyeing_mst a where a.id=$update_id");
	$inserted_by = $data_result[0]['INSERTED_BY'];
	foreach ($nameArray as $result) {
		$work_order = $result[csf('ydw_no')];
		$supplier_id = $result[csf('supplier_id')];
		$booking_date = $result[csf('booking_date')];
		$attention = $result[csf('attention')];
		$delivery_date = $result[csf('delivery_date')];
		$delivery_date_end = $result[csf('delivery_date_end')];
		$dy_delivery_start = $result[csf('dy_delivery_date_start')];
		$dy_delivery_end = $result[csf('dy_delivery_date_end')];
		$currency_id = $result[csf('currency')];
		$pay_mode_id = $result[csf('pay_mode')];
		$exchange_rate = $result[csf('ecchange_rate')];
		$is_short = $result[csf('is_short')];
	}

	$varcode_work_order_no = $work_order;


?>
	<div style="width:1000px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="700">
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
								echo $company_library[$cbo_company_name];
								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result) {
								?>

									Email Address: <? echo $result[csf('email')]; ?>
									Website No: <? echo $result[csf('website')];
											}

												?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong>Yarn Dyeing Work Order <? if ($is_short == 1) echo " (Short) "; ?> </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">

				</td>
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;


		?>
		<table width="950" style="" align="center">
			<tr>
				<td width="350" style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td width="120"><b>To</b> </td>
							<td width="230">:&nbsp;&nbsp;<?
															if ($pay_mode == 3 || $pay_mode == 5) {
																echo $company_library[$new_supplier_name];
															} else {
																echo $supplier_name[$new_supplier_name];
															}
															// if($pay_mode_id==5 || $pay_mode_id==3){
															// 	echo $company_library[$supplier_id];
															// }
															// else{
															// 	echo $supplier_name_arr[$supplier_id];
															// }
															?></td>
						</tr>

						<tr>
							<td><b>Wo No.</b> </td>
							<td>:&nbsp;&nbsp;<? echo $work_order; ?> </td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td>:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Booking Date</b></td>
							<td>:&nbsp;&nbsp;<? if ($booking_date != "0000-00-00" || $booking_date != "") echo change_date_format($booking_date);
												else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td>:&nbsp;&nbsp;<? echo $currency[$currency_id]; ?></td>
						</tr>
					</table>
				</td>
				<td width="350" style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td width="120"><b>G/Y Issue Start</b> </td>
							<td width="230">:&nbsp;&nbsp;<? if ($delivery_date != "0000-00-00" || $delivery_date != "") echo change_date_format($delivery_date);
															else echo ""; ?> </td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td>:&nbsp;&nbsp;<? if ($delivery_date_end != "0000-00-00" || $delivery_date_end != "") echo change_date_format($delivery_date_end);
												else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td>:&nbsp;&nbsp;<? if ($dy_delivery_start != "0000-00-00" || $dy_delivery_start != "") echo change_date_format($dy_delivery_start);
												else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td>:&nbsp;&nbsp;<? if ($dy_delivery_end != "0000-00-00" || $dy_delivery_end != "") echo change_date_format($dy_delivery_end);
												else echo ""; ?></td>
						</tr>
					</table>
				</td>
				<td width="250" style="font-size:12px">
					<?
					$image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$update_id' and form_name='$form_name'", "image_location");
					?>
					<img src="<? echo '../../' . $image_location; ?>" width="120" height="100" border="2" />
				</td>
			</tr>
		</table>
		</br>

		<?
		/*$multi_job_arr=array();
				$style_no=sql_select("select a.style_ref_no,a.job_no,b.pub_shipment_date,a.buyer_name,b.po_number,b.po_quantity from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.status_active=1 and b.is_deleted=0");

				foreach($style_no as $row_s)
				{
					$multi_job_arr[$row_s[csf('job_no')]]['style_ref_no']=$row_s[csf('style_ref_no')];
					$multi_job_arr[$row_s[csf('job_no')]]['buyer']=$row_s[csf('buyer_name')];
					$multi_job_arr[$row_s[csf('job_no')]]['po_no'].=$row_s[csf('po_number')].",";
					$multi_job_arr[$row_s[csf('job_no')]]['ship'].=$row_s[csf('pub_shipment_date')].",";
					$multi_job_arr[$row_s[csf('job_no')]]['po_quantity']+=$row_s[csf('po_quantity')];

				}
	   $sql="select a.id, a.ydw_no,b.job_no,b.yarn_color,b.id as dtls_id
			from
					wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
					a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";
					//echo $sql;
	   $sql_result=sql_select($sql);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";$tot_job_no="";$total_buyer="";$total_order_no="";$total_dtls_id=0;$style_all="";
	   $total_order_qty=0;
		foreach($sql_result as $row)
		{
			if($total_dtls_id==0) $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];

			if($tot_job_no=="") $tot_job_no=$row[csf("job_no")]; else $tot_job_no=$tot_job_no.",".$row[csf("job_no")];
			if($style_all=="") $style_all=$multi_job_arr[$row[csf('job_no')]]['style_ref_no']; else $style_all=$style_all.",".$multi_job_arr[$row[csf('job_no')]]['style_ref_no'];
			$bayer=$multi_job_arr[$row[csf('job_no')]]['buyer'];
			if($total_buyer=="") $total_buyer=$bayer; else $total_buyer=$total_buyer.",".$bayer;
			$order_no=$multi_job_arr[$row[csf('job_no')]]['po_no'];
			if($total_order_no=="") $total_order_no=$order_no; else $total_order_no=$total_order_no.",".$order_no;


		}
		$total_order_no=substr($total_order_no,0,-1);
		//var_dump($total_dtls_id);
		//die;*/

		$style_no = "select a.style_ref_no, a.job_no, a.buyer_name, b.id as po_id,b.po_number, b.file_no, b.grouping as inter_ref_no, b.po_quantity, p.id as dtls_id
		from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b
		where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.mst_id=$update_id and  p.status_active=1 and p.is_deleted=0";
		$sql_result = sql_select($style_no);
		$style_all = $total_dtls_id = $tot_job_no = $total_buyer = $total_order_no = $all_file = $all_inter_ref = ""; //$total_order_qty=0;
		$po_ids = '';
		foreach ($sql_result as $row) {
			$total_dtls_id .= $row[csf("dtls_id")] . ",";
			$style_all .= $row[csf("style_ref_no")] . ",";
			$tot_job_no .= $row[csf("job_no")] . ",";
			$total_buyer .= $row[csf("buyer_name")] . ",";
			$total_order_no .= $row[csf("po_number")] . ",";
			$all_file .= $row[csf("file_no")] . ",";
			$all_inter_ref .= $row[csf("inter_ref_no")] . ",";
			//$total_order_qty+=$row[csf("po_quantity")];
			if ($po_ids == '') $po_ids = $row[csf("po_id")];
			else $po_ids .= "," . $row[csf("po_id")];
		}
		$total_dtls_id = chop($total_dtls_id, " , ");
		$style_all = chop($style_all, " , ");
		$tot_job_no = chop($tot_job_no, " , ");
		$total_buyer = chop($total_buyer, " , ");
		$total_order_no = chop($total_order_no, " , ");
		$all_file = chop($all_file, " , ");
		$all_inter_ref = chop($all_inter_ref, " , ");

		$fab_wo_no_arr = array();
		$fabric_booking = sql_select("select a.booking_no,a.job_no from wo_booking_dtls a,wo_po_break_down b where a.po_break_down_id=b.id and a.booking_type=1 and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  b.id in($po_ids) group by a.booking_no,a.job_no");
		$booking_nos = '';
		foreach ($fabric_booking as $row) {
			if ($booking_nos == '') $booking_nos = $row[csf('booking_no')];
			else $booking_nos .= "," . $row[csf('booking_no')];
		}
		$style_po = "select a.job_no, sum(distinct b.po_quantity) as po_quantity
		from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b
		where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.mst_id=$update_id and  p.status_active=1 and p.is_deleted=0 group by a.job_no";
		$sql_po = sql_select($style_po);
		$total_order_qty = 0;
		foreach ($sql_po as $row) {
			$total_order_qty += $row[csf('po_quantity')];
		}

		?>

		<table width="950" align="center">
			<tr style="font-size:12px">
				<td width="120"><b>Style </b></td>
				<td width="830" valign="top">:&nbsp;
					<?
					$style_all_arr = array_unique(explode(",", $style_all));
					$all_style = "";
					foreach ($style_all_arr as $row) {
						$all_style .= $row . ",";
					}
					$all_style = chop($all_style, " , ");
					echo $all_style;
					?>
				</td>
			</tr>
			<tr style="font-size:12px">
				<td><b>Job No </b></td>
				<td valign="top">:&nbsp;
					<?
					$all_job_arr = array_unique(explode(",", $tot_job_no));
					$all_job = "";
					foreach ($all_job_arr as $row) {
						$all_job .= $row . ",";
					}
					$all_job = chop($all_job, " , ");
					echo $all_job;
					?>
				</td>
			</tr>
			<tr style="font-size:12px">
				<td valign="top"><b>Buyer </b> </td>
				<td valign="top">:&nbsp;
					<?
					$buyer_id_arr = array_unique(explode(",", $total_buyer));
					$all_buyer = "";
					foreach ($buyer_id_arr as $row) {
						$all_buyer .= $buyer_name_arr[$row] . ",";
					}
					$all_buyer = chop($all_buyer, " , ");
					echo $all_buyer;
					?>
				</td>
			</tr>
			<tr style="font-size:12px">
				<td valign="top"><b>Order No</b></td>
				<td valign="top">: &nbsp;
					<?
					$all_order_arr = array_unique(explode(",", $total_order_no));
					$all_order = "";
					foreach ($all_order_arr as $row) {
						$all_order .= $row . ",";
					}
					$all_order = chop($all_order, " , ");
					echo $all_order;
					?>
				</td>
			</tr>

			<tr style="font-size:12px">
				<td valign="top"><b>Order Qty.</b></td>
				<td valign="top">: &nbsp;
					<?
					echo $total_order_qty;

					?>
				</td>
			</tr>
			<tr style="font-size:12px">
				<td valign="top"><b>Fabric Booking No.</b></td>
				<td valign="top">: &nbsp;
					<?
					echo $booking_nos;
					?>
				</td>
			</tr>
		</table>


		<table width="1000" align="center" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="30" align="center">Sl</th>
					<th width="70" align="center">Color</th>
					<th width="80" align="center">Color Range</th>
					<th align="center" width="50">Ref No.</th>
					<th width="60" align="center">File No</th>
					<th width="60" align="center">Internal Ref. No</th>
					<th width="30" align="center">Yarn Count</th>
					<th width="160" align="center">Yarn Description</th>
					<th width="50" align="center">Brand</th>
					<th width="50" align="center">Lot</th>
					<th width="50" align="center">UOM</th>
					<th width="60" align="center">WO Qty</th>
					<th width="50" align="center">Dyeing Rate</th>
					<th width="70" align="center">Amount</th>
					<th align="center" width="50">Min Req. Cone</th>
					<th align="center">Remarks/ Shade</th>
				</tr>
			</thead>
			<?
			$sql_color = "select id,product_id,job_no,job_no_id,yarn_color,yarn_description,count,color_range,yarn_wo_qty as yarn_wo_qty,dyeing_charge,amount as amount,min_require_cone,referance_no, remarks, file_no, internal_ref_no
			from
			wo_yarn_dyeing_dtls
			where
			status_active=1 and id in($total_dtls_id)";
			//echo $sql_color;die;
			$sql_result = sql_select($sql_color);
			$total_qty = 0;
			$total_amount = 0;
			$i = 1;
			$buyer = 0;
			$order_no = "";
			$job_strip_color = array();
			foreach ($sql_result as $row) {
				$product_id = $row[csf("product_id")];
				//var_dump($product_id);
				$job_strip_color[$row[csf("job_no")]] .= $row[csf("yarn_color")] . ",";
				$all_stripe_color .= $row[csf("yarn_color")] . ",";
				if ($product_id) {
					$sql_brand = sql_select("select lot,brand from product_details_master where id in($product_id)");
					foreach ($sql_brand as $row_barand) {
						$lot_amt = $row_barand[csf("lot")];
						$brand = $row_barand[csf("brand")];
					}
				}

				//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);

				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";


			?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center">
						<p><? echo $i; ?></p>
					</td>
					<td>
						<p><? echo $color_arr[$row[csf("yarn_color")]]; ?></p>
					</td>
					<td>
						<p><? echo $color_range[$row[csf("color_range")]]; ?></p>
					</td>
					<td align="center">
						<p><? echo $row[csf("referance_no")]; ?></p>
					</td>
					<td>
						<p><? echo $row[csf("file_no")]; ?></p>
					</td>
					<td>
						<p><? echo $row[csf("internal_ref_no")]; ?></p>
					</td>
					<td align="center">
						<p><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; 
							?></p>
					</td>
					<td>
						<p> <? echo $row[csf("yarn_description")]; ?></p>
					</td>
					<td>
						<p><? echo $brand_arr[$brand]; ?></p>
					</td>
					<td align="center">
						<p><? echo $lot_amt; ?></p>
					</td>
					<td align="center">
						<p><? echo "KG"; ?></p>
					</td>
					<td align="right"><? echo $row[csf("yarn_wo_qty")];
										$total_qty += $row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
					<td align="right"><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</td>
					<td align="right"><? echo number_format($row[csf("amount")], 2);
										$total_amount += $row[csf("amount")]; ?></td>
					<td align="center">
						<p><? echo $row[csf("min_require_cone")]; ?></p>
					</td>
					<td align="center">
						<p><? echo $row[csf("remarks")]; ?></p>
					</td>
				</tr>
			<?
				$i++;
				$yarn_count_des = "";
				$style_no = "";
			}
			?>
			<tr>
				<td colspan="11" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
				<td align="right"><b><? echo $total_qty; ?></b></td>
				<td align="right">&nbsp;</td>
				<td align="right"><b><? echo number_format($total_amount, 2); ?></b></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<?
			$mcurrency = "";
			$dcurrency = "";
			if ($currency_id == 1) {
				$mcurrency = 'Taka';
				$dcurrency = 'Paisa';
			}
			if ($currency_id == 2) {
				$mcurrency = 'USD';
				$dcurrency = 'CENTS';
			}
			if ($currency_id == 3) {
				$mcurrency = 'EURO';
				$dcurrency = 'CENTS';
			}
			?>
			<tr>
				<td colspan="16" align="center">Total Dyeing Amount (in word): &nbsp;<? echo number_to_words(def_number_format($total_amount, 2, ""), $mcurrency, $dcurrency); ?> </td>
			</tr>
		</table>

		<? echo get_spacial_instruction($txt_booking_no); ?>

		<br> <br>
		<?
		if ($show_comment == 1) {
			$job_cond_arr = array_unique(explode(",", $tot_job_no));
			foreach ($job_cond_arr as $job_no_st) {
				$job_cond_string .= "'" . $job_no_st . "',";
			}
			$job_cond_string = chop($job_cond_string, ",");
			//echo $job_cond_string.jahid;die;
			$condition = new condition();
			if (str_replace("'", "", $job_cond_string) != '') {
				$condition->job_no(" in($job_cond_string)");
			}
			$condition->init();
			$conversion = new conversion($condition);
			$conversion_costing_arr_process = $conversion->getAmountArray_by_jobAndProcess();
			//echo $conversion->getQuery();die;
			//print_r($conversion_costing_arr_process);
			//echo "jahid";die;
			//getAmountArray_by_jobAndProcess
		?>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table">
				<thead>
					<tr>
						<th colspan="9" align="center"><b> Comments</b> </th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="120">Job No</th>
						<th width="180">PO No</th>
						<th width="150">Ship Date</th>
						<th width="80">Pre-Cost Value</th>
						<th width="80">WO Value</th>
						<th width="80">Short WO Value</th>
						<th width="80">Balance</th>
						<th>Comments </th>
					</tr>
				</thead>
				<tbody>
					<?
					$all_stripe_color = implode(",", array_unique(explode(",", chop($all_stripe_color, ","))));

					//print_r($job_strip_color);
					if ($db_type == 0) {
						$job_po_sql = sql_select("select job_no_mst, group_concat(po_number) as po_number, group_concat(shipment_date) as shipment_date from wo_po_break_down where job_no_mst in('" . implode($job_cond_arr, "','") . "') and status_active=1 and is_deleted=0 group by job_no_mst");
					} else {
						$job_po_sql = sql_select("select job_no_mst, listagg(cast(po_number as varchar(4000)),',') within group(order by po_number) as po_number, listagg(cast(shipment_date as varchar(4000)),',') within group(order by shipment_date) as shipment_date from wo_po_break_down where job_no_mst in('" . implode($job_cond_arr, "','") . "') and status_active=1 and is_deleted=0 group by job_no_mst");
					}
					$job_po_data = array();
					foreach ($job_po_sql as $row) {
						$job_po_data[$row[csf("job_no_mst")]]["po_number"] = $row[csf("po_number")];
						$job_po_data[$row[csf("job_no_mst")]]["shipment_date"] = $row[csf("shipment_date")];
					}


					//var_dump($job_wise_badge_val);die;
					$total_dtls_id = implode(",", array_unique(explode(",", $total_dtls_id)));
					$prev_wo_data = sql_select("select b.job_no_id, b.yarn_color,
            	sum(case when a.is_short<>1 then b.amount else 0 end) as amount,
            	sum(case when a.is_short=1 then b.amount else 0 end) as short_amount
            	from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.entry_form=41 and b.status_active=1 and b.is_deleted=0 and b.job_no in('" . implode($job_cond_arr, "','") . "') and b.id not in($total_dtls_id) group by b.job_no_id, b.yarn_color");
					foreach ($prev_wo_data as $row) {

						$prev_job_entry[$row[csf("job_no_id")]] += $row[csf("amount")];
						$prev_job_short_entry[$row[csf("job_no_id")]] += $row[csf("short_amount")];
					}

					if ($db_type == 0) {
						$yarn_data = "select b.job_no_id, max(a.currency) as currency, b.job_no,
				sum(case when a.is_short<>1 then b.amount else 0 end) as amount,
				sum(case when a.is_short=1 then b.amount else 0 end) as short_amount, group_concat(b.yarn_color) as yarn_color
				from wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst a
				where a.id=b.mst_id and b.id in($total_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				group by b.job_no_id, b.job_no";
					} else {
						$yarn_data = "select b.job_no_id, max(a.currency) as currency, b.job_no,
				sum(case when a.is_short<>1 then b.amount else 0 end) as amount,
				sum(case when a.is_short=1 then b.amount else 0 end) as short_amount,
				listagg( cast(b.yarn_color as varchar(4000)),',') within group (order by b.yarn_color) as yarn_color
				from wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst a
				where a.id=b.mst_id and b.id in($total_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				group by b.job_no_id, b.job_no";
					}


					//echo $yarn_data."<br>";

					$nameArray = sql_select($yarn_data);
					foreach ($nameArray as $selectResult) {
						//$prev_qnty=0;
						$prev_qnty = $prev_job_entry[$selectResult[csf("job_no_id")]];
						$prev_short_qnty = $prev_job_short_entry[$selectResult[csf("job_no_id")]];
						/*$strip_color_arr=array_unique(explode(",",$selectResult[csf("yarn_color")]));
				foreach($strip_color_arr as $strip_id)
				{
					$prev_qnty+=$prev_job_entry[$selectResult[csf("job_no_id")]][$strip_id];
					$prev_short_qnty+=$prev_job_short_entry[$selectResult[csf("job_no_id")]][$strip_id];

				}*/

						if ($selectResult[csf("currency")] == 2) {
							$tot_yarn_dyeing = $selectResult[csf("amount")] + $prev_qnty;
							$tot_yarn_dyeing_short = $selectResult[csf("short_amount")] + $prev_short_qnty;
						} else {
							$current_date = date("Y-m-d");
							$currency_rate = set_conversion_rate(2, $current_date);
							$tot_yarn_dyeing = $selectResult[csf("amount")] + $prev_qnty;
							$tot_yarn_dyeing = $tot_yarn_dyeing / $currency_rate;

							$tot_yarn_dyeing_short = $selectResult[csf("short_amount")] + $prev_short_qnty;
							$tot_yarn_dyeing_short = $tot_yarn_dyeing_short / $currency_rate;
						}
						//echo $convamount;
						$po_no = $job_po_data[$selectResult[csf('job_no')]]['po_number'];
						$shipment_date = array_unique(explode(",", $job_po_data[$selectResult[csf('job_no')]]['shipment_date']));
						$ship_date = "";
						foreach ($shipment_date as $date_row) {
							if ($ship_date == '') $ship_date = change_date_format($date_row);
							else $ship_date .= "," . change_date_format($date_row);
						}
						$pre_cost_yarn_deying = $conversion_costing_arr_process[$selectResult[csf('job_no')]][30];
						//$job_wise_badge_val[$selectResult[csf('job_no')]];
					?>
						<tr>
							<td align="center"><? echo $i; ?></td>
							<td>
								<p><? echo $selectResult[csf('job_no')]; ?> &nbsp;</p>
							</td>
							<td>
								<p><? echo $po_no; ?>&nbsp;</p>
							</td>
							<td>
								<p><? echo $ship_date; ?>&nbsp;</p>
							</td>
							<td align="right"><? echo number_format($pre_cost_yarn_deying, 2); ?></td>
							<td align="right"><? echo number_format($tot_yarn_dyeing, 2); ?> </td>
							<td align="right"><? echo number_format($tot_yarn_dyeing_short, 2); ?> </td>
							<td align="right"><? $tot_balance = $pre_cost_yarn_deying - $tot_yarn_dyeing;
												echo number_format($tot_balance, 2); ?></td>
							<td>
								<?
								if ($pre_cost_yarn_deying > $tot_yarn_dyeing) {
									echo "Less Booking";
								} else if ($pre_cost_yarn_deying < $tot_yarn_dyeing) {
									echo "Over Booking";
								} else if ($pre_cost_yarn_deying == $tot_yarn_dyeing) {
									echo "As Per";
								} else {
									echo "&nbsp;";
								}
								?>
							</td>
						</tr>
					<?
						$tot_pre_yarn_dyeing += $pre_cost_yarn_deying;
						$total_yarn_dyeing += $tot_yarn_dyeing;
						$total_yarn_dyeing_short += $tot_yarn_dyeing_short;
						$tot_balance_yarn_dyeing += $tot_balance;
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="4" align="right"> <b>Total</b></th>
						<th align="right"><? echo number_format($tot_pre_yarn_dyeing, 2); ?></th>
						<th align="right"><? echo number_format($total_yarn_dyeing, 2); ?></th>
						<th align="right"><? echo number_format($total_yarn_dyeing_short, 2); ?></th>
						<th align="right"><? echo number_format($tot_balance_yarn_dyeing, 2); ?></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
		<?
		}
		?>
	</div>
	<div>
		<?

		$lib_designation_arr = return_library_array(" select id,custom_designation from lib_designation", "id", "custom_designation");
		$user_lib_designation_arr = return_library_array("SELECT id,designation from user_passwd", "id", "designation");
		$user_lib_name_arr = return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

		$mst_id = return_field_value("id as mst_id", "wo_yarn_dyeing_mst", "id=$wo_ord_id", "mst_id");
		//echo $mst_id.'ssD';
		//and b.un_approved_date is null
		$approve_data_array = sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=30  group by  b.approved_by order by b.approved_by asc");

		$unapprove_data_array = sql_select("select b.id,b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=30  order by b.approved_date,b.approved_by");

		if (count($approve_data_array) > 0) {
		?>
			<table width="850" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
				<thead>
					<tr style="border:1px solid black;">
						<th colspan="5" style="border:1px solid black;">Approval Status</th>
					</tr>
					<tr style="border:1px solid black;">
						<th width="3%" style="border:1px solid black;">Sl</th>
						<th width="40%" style="border:1px solid black;">Name</th>
						<th width="30%" style="border:1px solid black;">Designation</th>
						<th width="27%" style="border:1px solid black;">Approval Date</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					foreach ($approve_data_array as $row) {
					?>
						<tr style="border:1px solid black;">
							<td width="3%" style="border:1px solid black;"><? echo $i; ?></td>
							<td width="40%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]]; ?></td>
							<td width="30%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]]; ?></td>
							<td width="27%" style="border:1px solid black;text-align:center"><? echo date("d-m-Y h:i:s", strtotime($row[csf('approved_date')])); ?></td>

						</tr>
					<?
						$i++;
					}
					?>
				</tbody>
			</table>
		<?
		}
		?>
		<br>
		<?
		if (count($unapprove_data_array) > 0) {
			$sql_unapproved = sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=29 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id");
			$unapproved_request_arr = array();
			foreach ($sql_unapproved as $rowu) {
				$unapproved_request_arr[$rowu[csf('booking_id')]] = $rowu[csf('approval_cause')];
			}
		?>
			<table width="850" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
				<thead>
					<tr style="border:1px solid black;">
						<th colspan="6" style="border:1px solid black;">Approval/Un Approval History</th>
					</tr>
					<tr style="border:1px solid black;">
						<th width="3%" style="border:1px solid black;">Sl</th>
						<th width="30%" style="border:1px solid black;">Name</th>
						<th width="20%" style="border:1px solid black;">Designation</th>
						<th width="5%" style="border:1px solid black;">Approval Status</th>
						<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
						<th width="22%" style="border:1px solid black;"> Date</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					foreach ($unapprove_data_array as $row) {

					?>
						<tr style="border:1px solid black;">
							<td width="3%" style="border:1px solid black;"><? echo $i; ?></td>
							<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]]; ?></td>
							<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]]; ?></td>
							<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes'; ?></td>
							<td width="20%" style="border:1px solid black;"><? echo ''; ?></td>
							<td width="22%" style="border:1px solid black;text-align:center"><? if ($row[csf('approved_date')] != "") echo date("d-m-Y h:i:s", strtotime($row[csf('approved_date')]));
																								else echo ""; ?></td>
						</tr>
						<?
						$i++;
						if ($row[csf('un_approved_date')] != "") {
						?>
							<tr style="border:1px solid black;">
								<td width="3%" style="border:1px solid black;"><? echo $i; ?></td>
								<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]]; ?></td>
								<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]]; ?></td>
								<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No'; ?></td>
								<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id]; ?></td>
								<td width="22%" style="border:1px solid black;text-align:center"><? if ($row[csf('un_approved_date')] != "") echo date("d-m-Y h:i:s", strtotime($row[csf('un_approved_date')]));
																									else echo ""; ?></td>
							</tr>

					<?
							$i++;
						}
					}
					?>
				</tbody>
			</table>
		<?
		}
		?>
		<br />
		<?
		$user_lib_name = return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
		echo signature_table(43, $cbo_company_name, "1000px", '', '', $user_lib_name[$inserted_by]);
		echo "****" . custom_file_name($txt_booking_no, $all_style, $all_job);
		?>
	</div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>', 'barcode_img_id');
	</script>
<?
}

if ($action == "show_print_booking_report") {
	//echo "uuuu";//die;
	//echo load_html_head_contents("Yarn Dyeing WO", "../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$form_name = str_replace("'", "", $form_name);
	$cbo_company_name = str_replace("'", "", $cbo_company_name);
	$update_id = str_replace("'", "", $update_id);
	$txt_booking_no = str_replace("'", "", $txt_booking_no);
	$path = str_replace("'", "", $path);
	if ($path == 1) $path = "../../";
	$buyer_name_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$brand_arr = return_library_array("select id,brand_name from  lib_brand", 'id', 'brand_name');
	//$job_quantity_arr=return_library_array( "select job_no,job_quantity from wo_po_details_master",'job_no','job_quantity');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//echo $show_comment;
	$image_arr = return_library_array("select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');


	$nameArray = sql_select("select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.pay_mode,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,a.ecchange_rate, a.is_short from wo_yarn_dyeing_mst a where a.id=$update_id");
	foreach ($nameArray as $result) {
		$work_order = $result[csf('ydw_no')];
		$supplier_id = $result[csf('supplier_id')];
		$booking_date = $result[csf('booking_date')];
		$attention = $result[csf('attention')];
		$delivery_date = $result[csf('delivery_date')];
		$delivery_date_end = $result[csf('delivery_date_end')];
		$dy_delivery_start = $result[csf('dy_delivery_date_start')];
		$dy_delivery_end = $result[csf('dy_delivery_date_end')];
		$currency_id = $result[csf('currency')];
		$pay_mode_id = $result[csf('pay_mode')];
		$exchange_rate = $result[csf('ecchange_rate')];
		$is_short = $result[csf('is_short')];
	}
	$varcode_work_order_no = $work_order;
?>
	<div style="width:1060px" align="center">

		<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
			<tr>
				<td width="100">
					<img src='../../<? echo $image_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
				</td>
				<td width="1000">
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
								echo $company_library[$cbo_company_name];
								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result) {
								?>
									Plot No: <? echo $result[csf('plot_no')]; ?>
									Level No: <? echo $result[csf('level_no')] ?>
									Road No: <? echo $result[csf('road_no')]; ?>
									Block No: <? echo $result[csf('block_no')]; ?>
									City No: <? echo $result[csf('city')]; ?>
									Zip Code: <? echo $result[csf('zip_code')]; ?>
									Province No: <?php echo $result[csf('province')]; ?>
									Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
									Email Address: <? echo $result[csf('email')]; ?>
									Website No: <? echo $result[csf('website')];
											}
												?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong>Yarn Dyeing Work Order <? if ($is_short == 1) echo " (Short) "; ?> </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">

				</td>
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;

		$supplier_address_arr = return_library_array("select id,address_1 from   lib_supplier", 'id', 'address_1');
		$company_address_arr = return_library_array("select id,city from   lib_company", 'id', 'city');
		?>
		<table width="950" style="" align="center">
			<tr>
				<td width="350" style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td width="120"><b>To</b><br>
								<b>Address </b>
							</td>
							<td width="230">:&nbsp;&nbsp;<?
															if ($pay_mode_id == 3 ||  $pay_mode_id == 5) {
																$supp_add = $company_address_arr[$supplier_id];
															} else {
																$supp_add = $supplier_address_arr[$supplier_id];
															}
															if ($pay_mode_id == 5 || $pay_mode_id == 3) {
																echo $company_library[$supplier_id];
															} else {
																echo $supplier_arr[$supplier_id];
															}
															echo "<br>";
															echo $supp_add;
															?></td>
						</tr>
						<tr style="font-size:12px">
							<td valign="top"><b>Pay Mode</b></td>
							<td valign="top">: &nbsp;<?= $pay_mode[$pay_mode_id]; ?></td>
						</tr>
						<tr>
							<td><b>Wo No.</b> </td>
							<td>:&nbsp;&nbsp;<? echo $work_order; ?> </td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td>:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Booking Date</b></td>
							<td>:&nbsp;&nbsp;<? if ($booking_date != "0000-00-00 00:00:00" || $booking_date != "") echo change_date_format($booking_date);
												else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td>:&nbsp;&nbsp;<? echo $currency[$currency_id]; ?></td>
						</tr>
					</table>
				</td>
				<td width="350" style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td width="120"><b>G/Y Issue Start</b> </td>
							<td width="230">:&nbsp;&nbsp;<? if ($delivery_date != "0000-00-00 00:00:00" || $delivery_date != "") echo change_date_format($delivery_date);
															else echo ""; ?> </td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td>:&nbsp;&nbsp;<? if ($delivery_date_end != "0000-00-00 00:00:00" || $delivery_date_end != "") echo change_date_format($delivery_date_end);
												else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td>:&nbsp;&nbsp;<? if ($dy_delivery_start != "0000-00-00 00:00:00" || $dy_delivery_start != "") echo change_date_format($dy_delivery_start);
												else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td>:&nbsp;&nbsp;<? if ($dy_delivery_end != "0000-00-00 00:00:00" || $dy_delivery_end != "") echo change_date_format($dy_delivery_end);
												else echo ""; ?></td>
						</tr>
					</table>
				</td>
				<td width="250" style="font-size:12px">
					<?
					$image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$update_id' and form_name='$form_name'", "image_location");
					?>
					<img src="<? echo '../../' . $image_location; ?>" width="120" height="100" border="2" />
				</td>
			</tr>
		</table>
		</br>

		<?


		/*   $sql="select a.id, a.ydw_no,b.job_no,b.yarn_color,b.id as dtls_id
			from
					wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
					a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";
					//echo $sql;
	   $sql_result=sql_select($sql);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";$tot_job_no="";$total_buyer="";$total_order_no="";$total_dtls_id=0;$style_all="";
	   $total_order_qty=0;
		foreach($sql_result as $row)
		{
			if($total_dtls_id==0) $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];

			if($tot_job_no=="") $tot_job_no=$row[csf("job_no")]; else $tot_job_no=$tot_job_no.",".$row[csf("job_no")];
			if($style_all=="") $style_all=$multi_job_arr[$row[csf('job_no')]]['style_ref_no']; else $style_all=$style_all.",".$multi_job_arr[$row[csf('job_no')]]['style_ref_no'];
			$bayer=$multi_job_arr[$row[csf('job_no')]]['buyer'];
			if($total_buyer=="") $total_buyer=$bayer; else $total_buyer=$total_buyer.",".$bayer;
			$order_no=$multi_job_arr[$row[csf('job_no')]]['po_no'];
			if($total_order_no=="") $total_order_no=$order_no; else $total_order_no=$total_order_no.",".$order_no;


		}
		$total_order_no=substr($total_order_no,0,-1);
		//var_dump($total_dtls_id);
		//die;*/

		$style_no = "select a.style_ref_no, a.job_no, a.buyer_name, b.id as po_id,b.po_number,p.fab_booking_no, b.file_no, b.grouping as inter_ref_no, b.po_quantity, p.id as dtls_id
		from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b
		where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.mst_id=$update_id and  p.status_active=1 and p.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$sql_result = sql_select($style_no);
		$style_all = $total_dtls_id = $tot_job_no = $total_buyer = $total_order_no = $all_file = $all_inter_ref = ""; //$total_order_qty=0;
		$po_ids = '';
		foreach ($sql_result as $row) {
			$total_dtls_id .= $row[csf("dtls_id")] . ",";
			$style_all .= $row[csf("style_ref_no")] . ",";
			$tot_job_no .= $row[csf("job_no")] . ",";
			$total_buyer .= $row[csf("buyer_name")] . ",";
			$total_order_no .= $row[csf("po_number")] . ",";
			$all_file .= $row[csf("file_no")] . ",";
			$all_inter_ref .= $row[csf("inter_ref_no")] . ",";
			//$total_order_qty+=$row[csf("po_quantity")];
			if ($po_ids == '') $po_ids = $row[csf("po_id")];
			else $po_ids .= "," . $row[csf("po_id")];
			if ($row[csf("fab_booking_no")] != '') {
				$booking_nosArr[$row[csf("fab_booking_no")]] = $row[csf("fab_booking_no")];
			}
		}
		$booking_nos = implode(",", $booking_nosArr);
		$total_dtls_id = chop($total_dtls_id, " , ");
		$style_all = chop($style_all, " , ");
		$tot_job_no = chop($tot_job_no, " , ");
		$total_buyer = chop($total_buyer, " , ");
		$total_order_no = chop($total_order_no, " , ");
		$all_file = chop($all_file, " , ");
		$all_inter_ref = chop($all_inter_ref, " , ");
		/*$fab_wo_no_arr=array();
		$fabric_booking=sql_select("select a.booking_no,a.job_no from wo_booking_dtls a,wo_po_break_down b where a.po_break_down_id=b.id and a.booking_type=1 and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  b.id in($po_ids) group by a.booking_no,a.job_no");
		//$booking_nos='';
		foreach($fabric_booking as $row)
		{
			//if($booking_nos=='') $booking_nos=$row[csf('booking_no')];else $booking_nos.=",".$row[csf('booking_no')];
		}*/

		$style_po = "select a.job_no,p.fab_booking_no, sum(distinct b.po_quantity) as po_quantity
		from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b
		where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.mst_id=$update_id and  p.status_active=1 and p.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no,p.fab_booking_no";
		$sql_po = sql_select($style_po);
		$total_order_qty = 0;
		$booking_nos = "";
		foreach ($sql_po as $row) {
			$total_order_qty += $row[csf('po_quantity')];
			$row[csf('job_no')] .= $row[csf('fab_booking_no')] . ',';
			if ($row[csf("fab_booking_no")] != "") {
				if ($booking_nos == '') $booking_nos = $row[csf("fab_booking_no")];
				else $booking_nos .= "," . $row[csf("fab_booking_no")];
			}
		}
		//$fab_booking_nos=chop($fab_booking_nos,",");
		//echo $fab_booking_nos.'DDDD';
		?>

		<table width="950" align="center">

			<tr style="font-size:12px">
				<td><b>Job No </b></td>
				<td valign="top">:&nbsp;
					<?
					$all_job_arr = array_unique(explode(",", $tot_job_no));
					$all_job = "";
					foreach ($all_job_arr as $row) {
						$all_job .= $row . ",";
					}
					$all_job = chop($all_job, " , ");
					echo $all_job;
					?>
				</td>
			</tr>

			<tr style="font-size:12px">
				<td valign="top"><b>Order No</b></td>
				<td valign="top" width="450">
					<p>: &nbsp;
						<?
						$all_order_arr = array_unique(explode(",", $total_order_no));
						$all_order = "";
						foreach ($all_order_arr as $row) {
							$all_order .= $row . ",";
						}
						$all_orders = chop($all_order, " , ");
						$all_orders = implode(",", array_unique(explode(",", $all_orders)));
						echo $all_orders;
						?>
					</p>
				</td>
			</tr>

			<tr style="font-size:12px">
				<td valign="top"><b>Order Qty.</b></td>
				<td valign="top">: &nbsp;
					<?
					echo $total_order_qty;

					?>
				</td>
				<? if ($show_comment == 1) { ?>
					<td valign="top"><b>Style No</b></td>
					<td valign="top">: &nbsp;
						<?
						$style_all = implode(",", array_unique(explode(",", $style_all)));
						echo $style_all;

						?>
					</td>
				<? } ?>
			</tr>
			<tr style="font-size:12px">
				<td valign="top"><b>Fabric Booking No.</b></td>
				<td valign="top">: &nbsp;
					<?
					echo $booking_nos;
					?>
				</td>
				<? if ($show_comment == 1) { ?>
					<td valign="top"><b>Buyer Name</b></td>
					<td valign="top">: &nbsp;
						<?
						$total_buyer = implode(",", array_unique(explode(",", $total_buyer)));
						echo $total_buyer;

						?>
					</td>
				<? } ?>
			</tr>
		</table>

		<table width="1060" align="center" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="30" align="center">Sl</th>
					<th width="70" align="center">Color</th>
					<th width="80" align="center">Color Range</th>
					<th align="center" width="50">Ref No.</th>
					<th width="60" align="center">File No</th>
					<th width="60" align="center">Internal Ref. No</th>
					<th width="30" align="center">Yarn Count</th>
					<th width="60" align="center">GSM</th>
					<th width="160" align="center">Yarn Description</th>
					<th width="50" align="center">Brand</th>
					<th width="50" align="center">Lot</th>
					<th width="50" align="center">UOM</th>
					<th width="60" align="center">WO Qty</th>
					<th width="50" align="center">Dyeing Rate</th>
					<th width="70" align="center">Amount</th>
					<th align="center" width="50">Min Req. Cone</th>
					<th align="center">Remarks/ Shade</th>
				</tr>
			</thead>
			<?
			$sql_color = "select id,product_id,job_no,job_no_id,yarn_color,yarn_description,count,color_range,yarn_wo_qty as yarn_wo_qty,dyeing_charge,amount as amount,min_require_cone,referance_no, remarks, file_no, internal_ref_no
			from
			wo_yarn_dyeing_dtls
			where
			status_active=1 and id in($total_dtls_id)";
			//echo $sql_color;die;
			$sql_result = sql_select($sql_color);
			$total_qty = 0;
			$total_amount = 0;
			$i = 1;
			$buyer = 0;
			$order_no = "";
			$job_strip_color = array();
			foreach ($sql_result as $row) {
				$product_id = $row[csf("product_id")];
				//var_dump($product_id);
				$job_strip_color[$row[csf("job_no")]] .= $row[csf("yarn_color")] . ",";
				$all_stripe_color .= $row[csf("yarn_color")] . ",";
				if ($product_id) {
					$sql_brand = sql_select("select lot,brand,gsm from product_details_master where id in($product_id)");
					//echo "select lot,brand,gsm from product_details_master where id in($product_id)";
					foreach ($sql_brand as $row_barand) {
						$lot_amt = $row_barand[csf("lot")];
						$brand = $row_barand[csf("brand")];
						$gsm = $row_barand[csf("gsm")];
					}
				}

				//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);

				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";


			?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center">
						<p><? echo $i; ?></p>
					</td>
					<td>
						<p><? echo $color_arr[$row[csf("yarn_color")]]; ?></p>
					</td>
					<td>
						<p><? echo $color_range[$row[csf("color_range")]]; ?></p>
					</td>
					<td align="center">
						<p><? echo $row[csf("referance_no")]; ?></p>
					</td>
					<td>
						<p><? echo $row[csf("file_no")]; ?></p>
					</td>
					<td>
						<p><? echo $row[csf("internal_ref_no")]; ?></p>
					</td>
					<td align="center">
						<p><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; 
							?></p>
					</td>
					<td align="center">
						<p><? echo $gsm; ?></p>
					</td>
					<td>
						<p> <? echo $row[csf("yarn_description")]; ?></p>
					</td>
					<td>
						<p><? echo $brand_arr[$brand]; ?></p>
					</td>
					<td align="center">
						<p><? echo $lot_amt; ?></p>
					</td>
					<td align="center">
						<p><? echo "KG"; ?></p>
					</td>
					<td align="right"><? echo number_format($row[csf("yarn_wo_qty")], 2, '.', '');
										$total_qty += $row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
					<td align="right"><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</td>
					<td align="right"><? echo number_format($row[csf("amount")], 2);
										$total_amount += $row[csf("amount")]; ?></td>
					<td align="center">
						<p><? echo $row[csf("min_require_cone")]; ?></p>
					</td>
					<td align="center">
						<p><? echo $row[csf("remarks")]; ?></p>
					</td>
				</tr>
			<?
				$i++;
				$yarn_count_des = "";
				$style_no = "";
			}
			?>
			<tr>
				<td colspan="12" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
				<td align="right"><b><? echo number_format($total_qty, 2, '.', ''); ?></b></td>
				<td align="right">&nbsp;</td>
				<td align="right"><b><? echo number_format($total_amount, 2); ?></b></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<?
			$mcurrency = "";
			$dcurrency = "";
			if ($currency_id == 1) {
				$mcurrency = 'Taka';
				$dcurrency = 'Paisa';
			}
			if ($currency_id == 2) {
				$mcurrency = 'USD';
				$dcurrency = 'CENTS';
			}
			if ($currency_id == 3) {
				$mcurrency = 'EURO';
				$dcurrency = 'CENTS';
			}
			?>
			<tr>
				<td colspan="17" align="center">Total Dyeing Amount (in word): &nbsp;<? echo number_to_words(def_number_format($total_amount, 2, ""), $mcurrency, $dcurrency); ?> </td>
			</tr>
		</table>

		<? echo get_spacial_instruction($txt_booking_no); ?>

		<br> <br>
		<?
		if ($show_comment == 1) {
			$job_cond_arr = array_unique(explode(",", $tot_job_no));
			foreach ($job_cond_arr as $job_no_st) {
				$job_cond_string .= "'" . $job_no_st . "',";
			}
			$job_cond_string = chop($job_cond_string, ",");
			//echo $job_cond_string.jahid;die;
			$condition = new condition();
			if (str_replace("'", "", $job_cond_string) != '') {
				$condition->job_no(" in($job_cond_string)");
			}
			$condition->init();
			$conversion = new conversion($condition);
			$conversion_costing_arr_process = $conversion->getAmountArray_by_jobAndProcess();
			//echo $conversion->getQuery();die;
			//print_r($conversion_costing_arr_process);
			//echo "jahid";die;
			//getAmountArray_by_jobAndProcess
		?>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1060" class="rpt_table">
				<thead>
					<tr>
						<th colspan="9" align="center"><b> Comments</b> </th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="120">Job No</th>
						<th width="180">PO No</th>
						<th width="150">Ship Date</th>
						<th width="80">Pre-Cost Value</th>
						<th width="80">WO Value</th>
						<th width="80">Short WO Value</th>
						<th width="80">Balance</th>
						<th>Comments </th>
					</tr>
				</thead>
				<tbody>
					<?
					$all_stripe_color = implode(",", array_unique(explode(",", chop($all_stripe_color, ","))));

					//print_r($job_strip_color);
					if ($db_type == 0) {
						$job_po_sql = sql_select("select job_no_mst, group_concat(po_number) as po_number, group_concat(shipment_date) as shipment_date from wo_po_break_down where job_no_mst in('" . implode($job_cond_arr, "','") . "') and status_active=1 and is_deleted=0 group by job_no_mst");
					} else {
						$job_po_sql = sql_select("select job_no_mst, listagg(cast(po_number as varchar(4000)),',') within group(order by po_number) as po_number, listagg(cast(shipment_date as varchar(4000)),',') within group(order by shipment_date) as shipment_date from wo_po_break_down where job_no_mst in('" . implode($job_cond_arr, "','") . "') and status_active=1 and is_deleted=0 group by job_no_mst");
					}
					$job_po_data = array();
					foreach ($job_po_sql as $row) {
						$job_po_data[$row[csf("job_no_mst")]]["po_number"] = $row[csf("po_number")];
						$job_po_data[$row[csf("job_no_mst")]]["shipment_date"] = $row[csf("shipment_date")];
					}


					$total_dtls_id = implode(",", array_unique(explode(",", $total_dtls_id)));
					$prev_wo_data = sql_select("select b.job_no_id, b.yarn_color,
	            	sum(case when a.is_short<>1 then b.amount else 0 end) as amount,
	            	sum(case when a.is_short=1 then b.amount else 0 end) as short_amount
	            	from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.entry_form=41 and b.status_active=1 and b.is_deleted=0 and b.job_no in('" . implode($job_cond_arr, "','") . "') and b.id not in($total_dtls_id) group by b.job_no_id, b.yarn_color");
					foreach ($prev_wo_data as $row) {


						$prev_job_entry[$row[csf("job_no_id")]] += $row[csf("amount")];
						$prev_job_short_entry[$row[csf("job_no_id")]] += $row[csf("short_amount")];
					}

					if ($db_type == 0) {
						$yarn_data = "select b.job_no_id, max(a.currency) as currency, b.job_no,
					sum(case when a.is_short<>1 then b.amount else 0 end) as amount,
					sum(case when a.is_short=1 then b.amount else 0 end) as short_amount, group_concat(b.yarn_color) as yarn_color, a.id as wo_ord_id
					from wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst a
					where a.id=b.mst_id and b.id in($total_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					group by b.job_no_id, b.job_no, a.id";
					} else {
						$yarn_data = "select b.job_no_id, max(a.currency) as currency, b.job_no,
					sum(case when a.is_short<>1 then b.amount else 0 end) as amount,
					sum(case when a.is_short=1 then b.amount else 0 end) as short_amount,
					listagg( cast(b.yarn_color as varchar(4000)),',') within group (order by b.yarn_color) as yarn_color, a.id as wo_ord_id
					from wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst a
					where a.id=b.mst_id and b.id in($total_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					group by b.job_no_id, b.job_no, a.id";
					}


					//echo $yarn_data."<br>";

					$nameArray = sql_select($yarn_data);
					foreach ($nameArray as $selectResult) {
						//$prev_qnty=0;
						$prev_qnty = $prev_job_entry[$selectResult[csf("job_no_id")]];
						$prev_short_qnty = $prev_job_short_entry[$selectResult[csf("job_no_id")]];
						$wo_ord_id = $selectResult[csf("wo_ord_id")];
						/*$strip_color_arr=array_unique(explode(",",$selectResult[csf("yarn_color")]));
					foreach($strip_color_arr as $strip_id)
					{
						$prev_qnty+=$prev_job_entry[$selectResult[csf("job_no_id")]][$strip_id];
						$prev_short_qnty+=$prev_job_short_entry[$selectResult[csf("job_no_id")]][$strip_id];

					}*/

						if ($selectResult[csf("currency")] == 2) {
							$tot_yarn_dyeing = $selectResult[csf("amount")] + $prev_qnty;
							$tot_yarn_dyeing_short = $selectResult[csf("short_amount")] + $prev_short_qnty;
						} else {
							$current_date = date("Y-m-d");
							$currency_rate = set_conversion_rate(2, $current_date);
							$tot_yarn_dyeing = $selectResult[csf("amount")] + $prev_qnty;
							$tot_yarn_dyeing = $tot_yarn_dyeing / $currency_rate;

							$tot_yarn_dyeing_short = $selectResult[csf("short_amount")] + $prev_short_qnty;
							$tot_yarn_dyeing_short = $tot_yarn_dyeing_short / $currency_rate;
						}
						//echo $convamount;
						$po_no = $job_po_data[$selectResult[csf('job_no')]]['po_number'];
						$shipment_date = array_unique(explode(",", $job_po_data[$selectResult[csf('job_no')]]['shipment_date']));
						$ship_date = "";
						foreach ($shipment_date as $date_row) {
							if ($ship_date == '') $ship_date = change_date_format($date_row);
							else $ship_date .= "," . change_date_format($date_row);
						}
						$pre_cost_yarn_deying = array_sum($conversion_costing_arr_process[$selectResult[csf('job_no')]][30]);
						//$job_wise_badge_val[$selectResult[csf('job_no')]];
					?>
						<tr>
							<td align="center"><? echo $i; ?></td>
							<td>
								<p><? echo $selectResult[csf('job_no')]; ?> &nbsp;</p>
							</td>
							<td>
								<p><? echo $po_no; ?>&nbsp;</p>
							</td>
							<td>
								<p><? echo $ship_date; ?>&nbsp;</p>
							</td>
							<td align="right"><? echo number_format($pre_cost_yarn_deying, 2); ?></td>
							<td align="right"><? echo number_format($tot_yarn_dyeing, 2); ?> </td>
							<td align="right"><? echo number_format($tot_yarn_dyeing_short, 2); ?> </td>
							<td align="right"><? $tot_balance = $pre_cost_yarn_deying - $tot_yarn_dyeing;
												echo number_format($tot_balance, 2); ?></td>
							<td>
								<?
								if ($pre_cost_yarn_deying > $tot_yarn_dyeing) {
									echo "Less Booking";
								} else if ($pre_cost_yarn_deying < $tot_yarn_dyeing) {
									echo "Over Booking";
								} else if ($pre_cost_yarn_deying == $tot_yarn_dyeing) {
									echo "As Per";
								} else {
									echo "&nbsp;";
								}
								?>
							</td>
						</tr>
					<?
						$tot_pre_yarn_dyeing += $pre_cost_yarn_deying;
						$total_yarn_dyeing += $tot_yarn_dyeing;
						$total_yarn_dyeing_short += $tot_yarn_dyeing_short;
						$tot_balance_yarn_dyeing += $tot_balance;
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="4" align="right"> <b>Total</b></th>
						<th align="right"><? echo number_format($tot_pre_yarn_dyeing, 2); ?></th>
						<th align="right"><? echo number_format($total_yarn_dyeing, 2); ?></th>
						<th align="right"><? echo number_format($total_yarn_dyeing_short, 2); ?></th>
						<th align="right"><? echo number_format($tot_balance_yarn_dyeing, 2); ?></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
		<?
		}

		?>
	</div>
	<br />
	<div>
		<?

		$lib_designation_arr = return_library_array(" select id,custom_designation from lib_designation", "id", "custom_designation");
		$user_lib_designation_arr = return_library_array("SELECT id,designation from user_passwd", "id", "designation");
		$user_lib_name_arr = return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

		$mst_id = return_field_value("id as mst_id", "wo_yarn_dyeing_mst", "id=$wo_ord_id", "mst_id");

		$approve_data_array = sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=30  group by b.approved_by");

		$unapprove_data_array = sql_select("select b.id,b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=30  order by b.approved_date");
		//echo "select b.id,b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=30  order by b.approved_date";

		if (count($approve_data_array) > 0) {
		?>
			<table width="850" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
				<thead>
					<tr style="border:1px solid black;">
						<th colspan="5" style="border:1px solid black;">Approval Status</th>
					</tr>
					<tr style="border:1px solid black;">
						<th width="3%" style="border:1px solid black;">Sl</th>
						<th width="40%" style="border:1px solid black;">Name</th>
						<th width="30%" style="border:1px solid black;">Designation</th>
						<th width="27%" style="border:1px solid black;">Approval Date</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					foreach ($approve_data_array as $row) {
					?>
						<tr style="border:1px solid black;">
							<td width="3%" style="border:1px solid black;"><? echo $i; ?></td>
							<td width="40%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]]; ?></td>
							<td width="30%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]]; ?></td>
							<td width="27%" style="border:1px solid black;text-align:center"><? echo date("d-m-Y h:i:s", strtotime($row[csf('approved_date')])); ?></td>

						</tr>
					<?
						$i++;
					}
					?>
				</tbody>
			</table>
		<?
		}
		?>
		<br>
		<?
		if (count($unapprove_data_array) > 0) {
			$sql_unapproved = sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=29 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id");
			$unapproved_request_arr = array();
			foreach ($sql_unapproved as $rowu) {
				$unapproved_request_arr[$rowu[csf('booking_id')]] = $rowu[csf('approval_cause')];
			}
		?>
			<table width="850" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
				<thead>
					<tr style="border:1px solid black;">
						<th colspan="6" style="border:1px solid black;">Approval/Un Approval History</th>
					</tr>
					<tr style="border:1px solid black;">
						<th width="3%" style="border:1px solid black;">Sl</th>
						<th width="30%" style="border:1px solid black;">Name</th>
						<th width="20%" style="border:1px solid black;">Designation</th>
						<th width="5%" style="border:1px solid black;">Approval Status</th>
						<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
						<th width="22%" style="border:1px solid black;"> Date</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					foreach ($unapprove_data_array as $row) {

					?>
						<tr style="border:1px solid black;">
							<td width="3%" style="border:1px solid black;"><? echo $i; ?></td>
							<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]]; ?></td>
							<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]]; ?></td>
							<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes'; ?></td>
							<td width="20%" style="border:1px solid black;"><? echo ''; ?></td>
							<td width="22%" style="border:1px solid black;text-align:center"><? if ($row[csf('approved_date')] != "") echo date("d-m-Y h:i:s", strtotime($row[csf('approved_date')]));
																								else echo ""; ?></td>
						</tr>
						<?
						$i++;
						if ($row[csf('un_approved_date')] != "" && $row[csf('un_approved_date')] != "0000-00-00 00:00:00") {
						?>
							<tr style="border:1px solid black;">
								<td width="3%" style="border:1px solid black;"><? echo $i; ?></td>
								<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]]; ?></td>
								<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]]; ?></td>
								<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No'; ?></td>
								<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id]; ?></td>
								<td width="22%" style="border:1px solid black;text-align:center"><? if ($row[csf('un_approved_date')] != "") echo date("d-m-Y h:i:s", strtotime($row[csf('un_approved_date')]));
																									else echo ""; ?></td>
							</tr>

					<?
							$i++;
						}
					}
					?>
				</tbody>
			</table>
		<?
		}
		?>
		<br />
		<?
		echo signature_table(43, $cbo_company_name, "1000px");
		echo "****" . custom_file_name($txt_booking_no, $all_style, $all_job);
		?>
	</div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>', 'barcode_img_id');
	</script>
<?
}
?>