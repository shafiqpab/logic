<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
extract($_REQUEST);
$permission = $_SESSION['page_permission'];

include('../../includes/common.php');
include('../../includes/class4/class.conditions.php');
include('../../includes/class4/class.reports.php');
include('../../includes/class4/class.fabrics.php');
include('../../includes/class4/class.emblishments.php');
include('../../includes/class4/class.washes.php');
//include('../../includes/class4/class.yarns.php');
include('../../includes/class4/class.conversions.php');

$user_id = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$menu_id = $_SESSION['menu_id'];

$userCredential = sql_select("SELECT unit_id as company_id, brand_id FROM user_passwd where id=$user_id");

$brand_id = $userCredential[0][csf('brand_id')];
$userbrand_idCond = "";

if ($brand_id != '') {
	$userbrand_idCond = " and id in ( $brand_id)";
}

if ($db_type == 0) $year_cond = "SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
else if ($db_type == 2) $year_cond = "to_char(a.insert_date,'YYYY') as year";

if ($db_type == 0) $year_cond_groupby = "SUBSTRING_INDEX(a.insert_date, '-', 1)";
else if ($db_type == 2) $year_cond_groupby = "to_char(a.insert_date,'YYYY')";

if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/pre_costing_approval_v3_controller', this.value, 'load_drop_down_brand', 'brand_td'); load_drop_down('requires/pre_costing_approval_v3_controller', this.value, 'load_drop_down_season', 'season_td');");
	exit();
}

if ($action == "load_drop_down_season") {
	echo create_drop_down("cbo_season_id", 80, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC", "id,season_name", 1, "-Select Season-", "", "");
	exit();
}

if ($action == "load_drop_down_brand") {
	echo create_drop_down("cbo_brand", 80, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC", "id,brand_name", 1, "-Brand-", $selected, "");
	exit();
}

if ($action == "load_drop_down_buyer_new_user") {
	$data = explode("_", $data);
	$log_sql = sql_select("SELECT user_level,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE id = '$data[1]' AND valid = 1");
	foreach ($log_sql as $r_log) {
		if ($r_log[csf('IS_DATA_LEVEL_SECURED')] == 1) {
			if ($r_log[csf('BUYER_ID')] != "") $buyer_cond = " and buy.id in (" . $r_log[csf('BUYER_ID')] . ")";
			else $buyer_cond = "";
		} else $buyer_cond = "";
	}
	echo create_drop_down("cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}


if ($action == "populate_cm_compulsory") {
	$cm_cost_compulsory = return_field_value("cm_cost_compulsory", "variable_order_tracking", "company_name ='" . $data . "' and variable_list=22 and is_deleted=0 and status_active=1");
	echo $cm_cost_compulsory;
	exit();
}


function getSequence($parameterArr = array())
{
	$lib_buyer_arr = implode(',', (array_keys($parameterArr['lib_buyer_arr'])));
	//$buyer_brand_arr=$parameterArr['lib_brand_arr'];

	//Electronic app setup data.....................
	$sql = "SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	//echo $sql;die;
	$sql_result = sql_select($sql);
	$dataArr = array();
	foreach ($sql_result as $rows) {


		if ($rows['BUYER_ID'] == '') {
			$rows['BUYER_ID'] = $lib_buyer_arr;
		}

		$temp_brand_arr = array(0 => 0);
		foreach (explode(',', $rows['BUYER_ID']) as $buyer_id) {
			if (count(array_keys($parameterArr['lib_brand_arr'][$buyer_id])) > 0) {
				$temp_brand_arr[] = implode(',', (array_keys($parameterArr['lib_brand_arr'][$buyer_id])));
			}
		}
		if ($rows['BRAND_ID'] == '') {
			$rows['BRAND_ID'] = implode(',', explode(',', implode(',', $temp_brand_arr)));
		}


		$dataArr['sequ_by'][$rows['SEQUENCE_NO']] = $rows;
		$dataArr['user_by'][$rows['USER_ID']] = $rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']] = $rows['SEQUENCE_NO'];
	}


	//var_dump($dataArr['user_by'][181]);die;


	return $dataArr;
}



function getFinalUser($parameterArr = array())
{
	$lib_buyer_arr = implode(',', (array_keys($parameterArr['lib_buyer_arr'])));
	//$buyer_brand_arr=$parameterArr['lib_brand_arr'];

	//Electronic app setup data.....................
	$sql = "SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	//echo $sql;die;
	$sql_result = sql_select($sql);
	foreach ($sql_result as $rows) {

		if ($rows['BUYER_ID'] == '') {
			$rows['BUYER_ID'] = $lib_buyer_arr;
		}

		$temp_brand_arr = array(0 => 0);
		foreach (explode(',', $rows['BUYER_ID']) as $buyer_id) {
			if (count(array_keys($parameterArr['lib_brand_arr'][$buyer_id])) > 0) {
				$temp_brand_arr[] = implode(',', (array_keys($parameterArr['lib_brand_arr'][$buyer_id])));
			}
		}
		if ($rows['BRAND_ID'] == '') {
			$rows['BRAND_ID'] = implode(',', explode(',', implode(',', $temp_brand_arr)));
		}

		$usersDataArr[$rows['USER_ID']]['BUYER_ID'] = explode(',', $rows['BUYER_ID']);
		$usersDataArr[$rows['USER_ID']]['BRAND_ID'] = explode(',', $rows['BRAND_ID']);
		$userSeqDataArr[$rows['USER_ID']] = $rows['SEQUENCE_NO'];
	}



	$finalSeq = array();
	foreach ($parameterArr['match_data'] as $sys_id => $bbtsRows) {

		foreach ($userSeqDataArr as $user_id => $seq) {
			if (
				(in_array($bbtsRows['buyer'], $usersDataArr[$user_id]['BUYER_ID'])  || $bbtsRows['buyer'] == 0)
				&& (in_array($bbtsRows['brand'], $usersDataArr[$user_id]['BRAND_ID']) || $bbtsRows['brand'] == 0)
			) {
				$finalSeq[$sys_id][$user_id] = $seq;
			}
		}
	}

	return array('final_seq' => $finalSeq, 'user_seq' => $userSeqDataArr);
}


function archive($dataArr = array()){
	global $con;


	$flag = 1; $rIDArr = array();
	if (count($dataArr['approved_no_by_job_id_array']) > 0) {
		$job_ids  = implode(',', array_keys($dataArr['approved_no_by_job_id_array']));
		$approved_string = "";
		foreach ($dataArr['approved_no_by_job_id_array'] as $key => $value) {
			$approved_string .= " WHEN $key THEN $value";
		}
		$approved_string_mst = "CASE id " . $approved_string . " END";
		$approved_string_dtls = "CASE job_id " . $approved_string . " END";


		//------------wo_po_dtls_mst_his----------------------------------
		$sqljob="insert into wo_po_dtls_mst_his (id, job_id, approved_no, approval_page, garments_nature, job_no_prefix, job_no_prefix_num, job_no, quotation_id, order_repeat_no, company_name, buyer_name, style_ref_no, product_dept, product_code, location_name, style_description, ship_mode, region, team_leader, dealing_marchant, remarks, job_quantity, avg_unit_price, currency_id, total_price, packing, agent_name, product_category, order_uom, gmts_item_id, set_break_down, total_set_qnty, set_smv, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, pro_sub_dep, client_id, item_number_id, factory_marchant, qlty_label, is_excel, style_owner, booking_meeting_date, bh_merchant, copy_from, season_buyer_wise, is_repeat, repeat_job_no, ready_for_budget, working_location_id, gauge, fabric_composition, design_source_id, yarn_quality, season_year, brand_id, inquiry_id, body_wash_color, sustainability_standard, fab_material, quality_level, requisition_no, working_company_id, fit_id)
			select '', id, $approved_string_mst, 15, garments_nature, job_no_prefix, job_no_prefix_num, job_no, quotation_id, order_repeat_no, company_name, buyer_name, style_ref_no, product_dept, product_code, location_name, style_description, ship_mode, region, team_leader, dealing_marchant, remarks, job_quantity, avg_unit_price, currency_id, total_price, packing, agent_name, product_category, order_uom, gmts_item_id, set_break_down, total_set_qnty, set_smv, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, pro_sub_dep, client_id, item_number_id, factory_marchant, qlty_label, is_excel, style_owner, booking_meeting_date, bh_merchant, copy_from, season_buyer_wise, is_repeat, repeat_job_no, ready_for_budget, working_location_id, gauge, fabric_composition, design_source_id, yarn_quality, season_year, brand_id, inquiry_id, body_wash_color, sustainability_standard, fab_material, quality_level, requisition_no, working_company_id, fit_id
		from wo_po_details_master where id in ($job_ids)";
		// echo "10**".$sqljob;die;
		
		//------------wo_po_dtls_item_set_his----------------------------------
		$sqlsetitem="insert into wo_po_dtls_item_set_his (id, approval_page, set_dtls_id, approved_no, job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, quot_id, aop, aopseq, ws_id, job_id, bush, bushseq, peach, peachseq, yd, ydseq, printdiff, embrodiff, washdiff, spwdiff)
			select '', 15, id, $approved_string_dtls, job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, quot_id, aop, aopseq, ws_id, job_id, bush, bushseq, peach, peachseq, yd, ydseq, printdiff, embrodiff, washdiff, spwdiff from wo_po_details_mas_set_details where job_id in ($job_ids)";
		//echo "10**".$sqlsetitem;die;
		
		//------------wo_po_break_down_his----------------------------------
		$sqlpo="insert into wo_po_break_down_his (id, approval_page, po_id, approved_no, job_no_mst, po_number, pub_shipment_date, excess_cut, po_received_date, po_quantity, unit_price, plan_cut, country_name, po_total_price, shipment_date, is_deleted, is_confirmed, details_remarks, delay_for, packing, grouping, projected_po_id, tna_task_from_upto, inserted_by, insert_date, updated_by, update_date, status_active, shiping_status, original_po_qty, factory_received_date, original_avg_price, pp_meeting_date, matrix_type, no_of_carton, actual_po_no, round_type, doc_sheet_qty, up_charge, pack_price, sc_lc, with_qty, extended_ship_date, sewing_company_id, sewing_location_id, extend_ship_mode, sea_discount, air_discount, job_id, pack_handover_date, etd_ldd, file_year, file_no, rfi_date)
			select '', 15, id, $approved_string_dtls, job_no_mst, po_number, pub_shipment_date, excess_cut, po_received_date, po_quantity, unit_price, plan_cut, country_name, po_total_price, shipment_date, is_deleted, is_confirmed, details_remarks, delay_for, packing, grouping, projected_po_id, tna_task_from_upto, inserted_by, insert_date, updated_by, update_date, status_active, shiping_status, original_po_qty, factory_received_date, original_avg_price, pp_meeting_date, matrix_type, no_of_carton, actual_po_no, round_type, doc_sheet_qty, up_charge, pack_price, sc_lc, with_qty, extended_ship_date, sewing_company_id, sewing_location_id, extend_ship_mode, sea_discount, air_discount, job_id, pack_handover_date, txt_etd_ldd, file_year, file_no, rfi_date from wo_po_break_down where job_id in ($job_ids) and is_deleted=0";
		//echo "10**".$sqlpo;die;
		
		//------------wo_po_color_size_his----------------------------------
		$sqlcolorsize="insert into wo_po_color_size_his (id, approval_page, color_size_id, approved_no, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, country_mst_id, article_number, item_number_id, country_id, size_number_id, color_number_id, order_quantity, order_rate, order_total, excess_cut_perc, plan_cut_qnty, is_deleted, is_used, inserted_by, insert_date, updated_by, update_date, status_active, is_locked, cutup_date, cutup, country_ship_date, shiping_status, country_remarks, country_type, packing, color_order, size_order, ultimate_country_id, code_id, ul_country_code, pack_qty, pcs_per_pack, pack_type, pcs_pack, assort_qty, solid_qty, barcode_suffix_no, barcode_year, barcode_no, job_id, extended_ship_date, proj_qty, proj_amt, country_avg_rate)
			Select '', 15, id, $approved_string_dtls, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, country_mst_id, article_number, item_number_id, country_id, size_number_id, color_number_id, order_quantity, order_rate, order_total, excess_cut_perc, plan_cut_qnty, is_deleted, is_used, inserted_by, insert_date, updated_by, update_date, status_active, is_locked, cutup_date, cutup, country_ship_date, shiping_status, country_remarks, country_type, packing, color_order, size_order, ultimate_country_id, code_id, ul_country_code, pack_qty, pcs_per_pack, pack_type, pcs_pack, assort_qty, solid_qty, barcode_suffix_no, barcode_year, barcode_no, job_id, extended_ship_date, proj_qty, proj_amt, country_avg_rate from wo_po_color_size_breakdown where job_id in ($job_ids) and is_deleted=0 ";	
		//echo "10**".$sqlcolorsize;die;
		
		//------------wo_pre_cost_mst_histry----------------------------------
		$sqlBom="insert into wo_pre_cost_mst_histry(id, approved_no, pre_cost_mst_id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, ready_to_approved, budget_minute, sew_efficiency_source, entry_from, job_id, refusing_cause, sourcing_date, sourcing_inserted_date, sourcing_inserted_by, sourcing_update_date, sourcing_updated_by, sourcing_ready_to_approved, sourcing_approved, sourcing_remark, main_fabric_co, sourcinng_refusing_cause, approved_sequ_by, isorder_change, ready_to_source, approval_page)
			select '', $approved_string_dtls, id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, ready_to_approved, budget_minute, sew_efficiency_source, entry_from, job_id, refusing_cause, sourcing_date, sourcing_inserted_date, sourcing_inserted_by, sourcing_update_date, sourcing_updated_by, sourcing_ready_to_approved, sourcing_approved, sourcing_remark, main_fabric_co, sourcinng_refusing_cause, approved_sequ_by, isorder_change, ready_to_source, 15
		from wo_pre_cost_mst where job_id in ($job_ids)";
		//echo "10**".$sqlBom;die;
		
		//------------wo_pre_cost_dtls_histry----------------------------------
		$sql_bom_dtls="insert into wo_pre_cost_dtls_histry(id, approved_no, pre_cost_dtls_id, job_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent,	lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, cost_pcs_set, cost_pcs_set_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, margin_pcs_bom, margin_bom_per, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, depr_amor_pre_cost, depr_amor_po_price, interest_cost, interest_percent, incometax_cost, incometax_percent, deffdlc_cost, deffdlc_percent, design_cost, design_percent, studio_cost, studio_percent, job_id, sourcing_inserted_date, sourcing_inserted_by, sourcing_update_date, sourcing_updated_by, sourcing_fabric_cost, sourcing_trims_cost,incentives_pre_cost, sourcing_embel_cost, sourcing_wash_cost, approval_page)
				select '', $approved_string_dtls, id, job_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent,	lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, cost_pcs_set, cost_pcs_set_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, margin_pcs_bom, margin_bom_per, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, depr_amor_pre_cost, depr_amor_po_price, interest_cost, interest_percent, incometax_cost, incometax_percent, deffdlc_cost, deffdlc_percent, design_cost, design_percent, studio_cost, studio_percent, job_id, sourcing_inserted_date, sourcing_inserted_by, sourcing_update_date, sourcing_updated_by, sourcing_fabric_cost, sourcing_trims_cost,incentives_pre_cost, sourcing_embel_cost, sourcing_wash_cost, 15 from wo_pre_cost_dtls where job_id in ($job_ids)";
		//echo "10**".$sql_bom_dtls;die;

		//------------wo_pre_cost_fabric_cost_dtls_h----------------------------------
		$sql_fabric_cost_dtls="insert into wo_pre_cost_fabric_cost_dtls_h(id, approved_no, pre_cost_fabric_cost_dtls_id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, color_size_sensitive, color, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, consumption_basis, process_loss_method, cons_breack_down, msmnt_break_down, color_break_down, yarn_breack_down, marker_break_down, width_dia_type, avg_cons_yarn, gsm_weight_yarn, uom, body_part_type, sample_id, job_id, gsm_weight_type, nominated_supp_multi, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, sourcing_nominated_supp, quotdtlsid, budget_on, source_id, is_synchronized, approval_page)
			select '', $approved_string_dtls, id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, color_size_sensitive, color, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, consumption_basis, process_loss_method, cons_breack_down, msmnt_break_down, color_break_down, yarn_breack_down, marker_break_down, width_dia_type, avg_cons_yarn, gsm_weight_yarn, uom, body_part_type, sample_id, job_id, gsm_weight_type, nominated_supp_multi, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, sourcing_nominated_supp, quotdtlsid, budget_on, source_id, is_synchronized, 15 from wo_pre_cost_fabric_cost_dtls where job_id in ($job_ids)";
		//echo "10**".$sql_fabric_cost_dtls;die;
		
		//------------WO_PRE_FAB_AVG_CON_DTLS_H----------------------------------
		$sql_fabric_cons_dtls="insert into wo_pre_fab_avg_con_dtls_h(id, approved_no, fab_con_id, pre_cost_fabric_cost_dtls_id, job_no, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons, rate, amount, remarks, length, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, length_sleeve, width_sleeve, job_id, fina_char, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, cons_pcs, item_color, approval_page)
			select '', $approved_string_dtls, id,  pre_cost_fabric_cost_dtls_id, job_no, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons, rate, amount, remarks, length, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, length_sleeve, width_sleeve, job_id, fina_char, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, cons_pcs, item_color, 15 from wo_pre_cos_fab_co_avg_con_dtls where job_id in ($job_ids)";
		//echo "10**"$sql_fabric_cons_dtls;die;
		
		//-------------wo_pre_fab_concolor_dtls_h-----------------------------------------------
		$sql_concolor_cst="insert into wo_pre_fab_concolor_dtls_h (id, approved_no, contrast_id, pre_cost_fabric_cost_dtls_id, job_no, gmts_color_id, gmts_color, contrast_color_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, approval_page)
			select
			'', $approved_string_dtls, id, pre_cost_fabric_cost_dtls_id, job_no, gmts_color_id, gmts_color, contrast_color_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, 15 from wo_pre_cos_fab_co_color_dtls where job_id in ($job_ids)";
		//echo "10**".$sql_concolor_cst;die;
		
		//-------------wo_pre_stripe_color_h-----------------------------------------------
		$sql_stripecolor_cst="insert into wo_pre_stripe_color_h (id, approved_no, stripe_id, job_no, item_number_id, pre_cost_fabric_cost_dtls_id, color_number_id, stripe_color, measurement, uom, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, totfidder, fabreq, fabreqtotkg, yarn_dyed, sales_dtls_id, size_number_id, po_break_down_id, lenth, width, sample_color, sample_per, cons, excess_per, job_id, stripe_type, approval_page)
			select
			'', $approved_string_dtls, id, job_no, item_number_id, pre_cost_fabric_cost_dtls_id, color_number_id, stripe_color, measurement, uom, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, totfidder, fabreq, fabreqtotkg, yarn_dyed, sales_dtls_id, size_number_id, po_break_down_id, lenth, width, sample_color, sample_per, cons, excess_per, job_id, stripe_type, 15 from wo_pre_stripe_color where job_id in ($job_ids)";
		//echo "10**".$sql_stripecolor_cst;die;

		//-------------wo_pre_cost_fab_yarn_cst_dtl_h-----------------------------------------------
		$sql_precost_fab_yarn_cst="insert into wo_pre_cost_fab_yarn_cst_dtl_h (id, approved_no, pre_cost_fab_yarn_cost_dtls_id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_cons_qnty, supplier_id, color, consdznlbs, rate_dzn, job_id, yarn_finish, yarn_spinning_system, certification, approval_page)
			select
			'', $approved_string_dtls, id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_cons_qnty, supplier_id, color, consdznlbs, rate_dzn, job_id, yarn_finish, yarn_spinning_system, certification, 15 from wo_pre_cost_fab_yarn_cost_dtls where job_id in ($job_ids)";
			//echo "10**".$sql_precost_fab_yarn_cst;die;
			
		//-----------------------------------------wo_pre_cost_fab_con_cst_dtls_h-----------------------------------------
		$sql_precost_fab_con_cst_dtls="insert into  wo_pre_cost_fab_con_cst_dtls_h(id, approved_no, pre_cost_fab_conv_cst_dtls_id, job_no, fabric_description, cons_process, req_qnty, charge_unit, amount, color_break_down, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_req_qnty, process_loss, job_id, approval_page)
			select '', $approved_string_dtls, id, job_no, fabric_description, cons_process, req_qnty, charge_unit, amount, color_break_down, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_req_qnty, process_loss, job_id, 15 from wo_pre_cost_fab_conv_cost_dtls where job_id in ($job_ids)";
		//echo "10**".$sql_precost_fab_con_cst_dtls;die;
			
		//-------------------  WO_PRE_CONV_COLOR_DTLS_H------------------------------------------------------------
		$sql_conv_color_dtls="insert into wo_pre_conv_color_dtls_h(id, approved_no, conv_color_id, fabric_cost_dtls_id, conv_cost_dtls_id, job_no, job_id, convchargelibraryid, gmts_color_id, fabric_color_id, cons, unit_charge, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, approval_page)
			select '', $approved_string_dtls, id, fabric_cost_dtls_id, conv_cost_dtls_id, job_no, job_id, convchargelibraryid, gmts_color_id, fabric_color_id, cons, unit_charge, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, 15 from wo_pre_cos_conv_color_dtls where job_id in ($job_ids)";
		//echo "10**".$sql_conv_color_dtls;die;
			
		//------------wo_pre_cost_trim_cost_dtls_his------------------------------	----------------------
		$sql_precost_trim_cost_dtls="insert into  wo_pre_cost_trim_cost_dtls_his(id, approved_no, pre_cost_trim_cost_dtls_id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, cons_breack_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, remark, country, calculatorstring, unit_price, inco_term, add_price, seq, job_id, nominated_supp_multi, material_source, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, sourcing_nominated_supp, tot_cons, ex_per, quotdtlsid, source_id, item_print, is_synchronized, approval_page)
			select '', $approved_string_dtls, id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, cons_breack_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, remark, country, calculatorstring, unit_price, inco_term, add_price, seq, job_id, nominated_supp_multi, material_source, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, sourcing_nominated_supp, tot_cons, ex_per, quotdtlsid, source_id, item_print, is_synchronized, 15 from wo_pre_cost_trim_cost_dtls  where job_id in ($job_ids)";
		//echo "10**".$sql_precost_trim_cost_dtls;die;

		//---------------------------wo_pre_cost_trim_co_cons_dtl_h--------------------------------------------------
		$sql_precost_trim_co_cons_dtl="insert into wo_pre_cost_trim_co_cons_dtl_h( id, approved_no, pre_cost_trim_co_cons_dtls_id, wo_pre_cost_trim_cost_dtls_id, job_no, po_break_down_id, item_size, cons, place, pcs, country_id, excess_per, tot_cons, ex_cons, item_number_id, color_number_id, item_color_number_id, size_number_id, rate, amount, gmts_pcs, color_size_table_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rate_cal_data, sourcing_rate, sourcing_amount, sourcing_update_date, sourcing_updated_by, sourcing_inserted_date, sourcing_inserted_by, cons_pcs, approval_page)
			select '', $approved_string_dtls, id, wo_pre_cost_trim_cost_dtls_id, job_no, po_break_down_id, item_size, cons, place, pcs, country_id, excess_per, tot_cons, ex_cons, item_number_id, color_number_id, item_color_number_id, size_number_id, rate, amount, gmts_pcs, color_size_table_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rate_cal_data, sourcing_rate, sourcing_amount, sourcing_update_date, sourcing_updated_by, sourcing_inserted_date, sourcing_inserted_by, cons_pcs, 15 from wo_pre_cost_trim_co_cons_dtls where job_id in ($job_ids)";
		//echo "10**".$sql_precost_trim_co_cons_dtl;die;

		//-------------------  wo_pre_cost_embe_cost_dtls_his------------------------------------------------------------
		$sql_precost_embe_cost_dtls="insert into wo_pre_cost_embe_cost_dtls_his(id, approved_no, pre_cost_embe_cost_dtls_id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, budget_on, country, body_part_id, job_id, nominated_supp_multi, sourcing_nominated_supp, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, quotdtlsid, is_synchronized, approval_page)
			select '', $approved_string_dtls, id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, budget_on, country, body_part_id, job_id, nominated_supp_multi, sourcing_nominated_supp, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, quotdtlsid, is_synchronized, 15 from wo_pre_cost_embe_cost_dtls where job_id in ($job_ids)";
		//echo "10**".$sql_precost_embe_cost_dtls;die;
			
		//-------------------  WO_PRE_EMB_AVG_CON_DTLS_H------------------------------------------------------------
		$sql_embe_cons_dtls="insert into wo_pre_emb_avg_con_dtls_h(id, approved_no, emb_cons_id, pre_cost_emb_cost_dtls_id, job_no, po_break_down_id, item_number_id, color_number_id, size_number_id, requirment, rate, amount, gmts_pcs, color_size_table_id, rate_lib_id, country_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, approval_page)
			select '', $approved_string_dtls, id, pre_cost_emb_cost_dtls_id, job_no, po_break_down_id, item_number_id, color_number_id, size_number_id, requirment, rate, amount, gmts_pcs, color_size_table_id, rate_lib_id, country_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, 15 from wo_pre_cos_emb_co_avg_con_dtls where job_id in ($job_ids)";
		//echo "10**".$sql_embe_cons_dtls;die;
		
		//----------------------------------wo_pre_cost_comarc_cost_dtls_h----------------------------------------
		$sql_comarc_cost_dtls="insert into wo_pre_cost_comarc_cost_dtls_h( id, approved_no, pre_cost_comarci_cost_dtls_id, job_no, item_id, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, approval_page)
			select '', $approved_string_dtls, id, job_no, item_id, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, 15 from wo_pre_cost_comarci_cost_dtls where job_id in ($job_ids)";
		//echo "10**".$sql_comarc_cost_dtls;die;

		//-------------------------------------wo_pre_cost_commis_cost_dtls_h-------------------------------------------
		$sql_commis_cost_dtls="insert into wo_pre_cost_commis_cost_dtls_h (id, approved_no, pre_cost_commiss_cost_dtls_id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, approval_page)
			select '', $approved_string_dtls, id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, 15 from wo_pre_cost_commiss_cost_dtls where job_id in ($job_ids)";
		//echo "10**".$sql_commis_cost_dtls;die;
		
		//-------------------------------------wo_pre_cost_sum_dtls_histroy-------------------------------------------
		$sql_sum_dtls="insert into wo_pre_cost_sum_dtls_histroy (id, approved_no, pre_cost_sum_dtls_id, job_no, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, avg, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, fab_woven_fin_req_yds, fab_knit_fin_req_kg, job_id, approval_page)
			select '', $approved_string_dtls, id, job_no, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, avg, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, fab_woven_fin_req_yds, fab_knit_fin_req_kg, job_id, 15 from wo_pre_cost_sum_dtls where job_id in ($job_ids)";
		//echo "10**".$sql_sum_dtls;die;
		
		
		if ($flag == 1) {
			$rIDArr[1] = execute_query($sqljob, 1);
			if($rIDArr[1]){$flag = 1;}else{$flag = 0;}
		}

		
		
		if ($flag == 1)//JOB SET ITEM
		{
			$rIDArr[2]=execute_query($sqlsetitem,1);
			if($rIDArr[2]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//JOB PO
		{
			$rIDArr[3]=execute_query($sqlpo,1);
			if($rIDArr[3]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//JOB PO COLOR SIZE
		{
			$rIDArr[4]=execute_query($sqlcolorsize,1);
			if($rIDArr[4]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//BOM MST
		{
			$rIDArr[5]=execute_query($sqlBom,1);
			if($rIDArr[5]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//BOM DTLS
		{
			$rIDArr[]=execute_query($sql_bom_dtls,1);
			if($rIDArr[1]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//BOM FABRIC DTLS
		{
			$rIDArr[6]=execute_query($sql_fabric_cost_dtls,1);
			if($rIDArr[6]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//BOM FABRIC CONS
		{
			$rIDArr[7]=execute_query($sql_fabric_cons_dtls,1);
			if($rIDArr[7]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//BOM FABRIC CONTRAST COLOR
		{
			$rIDArr[8]=execute_query($sql_concolor_cst,1);
			if($rIDArr[8]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//BOM FABRIC STRIPE COLOR
		{
			$rIDArr[9]=execute_query($sql_stripecolor_cst,1);
			if($rIDArr[9]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//BOM YARN
		{
			$rIDArr[10]=execute_query($sql_precost_fab_yarn_cst,1);
			if($rIDArr[10]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//BOM CONV COST
		{
			$rIDArr[]=execute_query($sql_precost_fab_con_cst_dtls,1);
			if($rIDArr[1]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//BOM CONV COLOR
		{
			$rIDArr[11]=execute_query($sql_conv_color_dtls,1);
			if($rIDArr[11]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//BOM TRIM
		{
			$rIDArr[12]=execute_query($sql_precost_trim_cost_dtls,1);
			if($rIDArr[12]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//BOM TRIM CONS
		{
			$rIDArr[13]=execute_query($sql_precost_trim_co_cons_dtl,1);
			if($rIDArr[13]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//BOM EMB
		{
			$rIDArr[14]=execute_query($sql_precost_embe_cost_dtls,1);
			if($rIDArr[14]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//BOM EMB CONS
		{
			$rIDArr[15]=execute_query($sql_embe_cons_dtls,1);
			if($rIDArr[15]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//BOM COMMARCIAL
		{
			$rIDArr[16]=execute_query($sql_comarc_cost_dtls,1);
			if($rIDArr[16]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//BOM COMMISION
		{
			$rIDArr[17]=execute_query($sql_commis_cost_dtls,1);
			if($rIDArr[17]){$flag = 1;}else{$flag = 0;}
		}
		
		if ($flag == 1)//BOM SUM DTLS
		{
			$rIDArr[18]=execute_query($sql_sum_dtls,1);
			if($rIDArr[18]){$flag = 1;}else{$flag = 0;}
		}

	}

	

	return $flag;
}




$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
$user_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name");
$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');

$brand_sql = "select ID, BUYER_ID,BRAND_NAME from lib_buyer_brand where STATUS_ACTIVE=1 and IS_DELETED=0 and BRAND_NAME is not null";
$brand_sql_rs = sql_select($brand_sql);
foreach ($brand_sql_rs as $row) {
	$brand_arr[$row['ID']] = $row['BRAND_NAME'];
	if ($row['BRAND_NAME'] != '') {
		$buyer_brand_arr[$row['BUYER_ID']][$row['ID']] = $row['BRAND_NAME'];
	}
}



if ($action == "report_generate") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$job_no = str_replace("'", "", $txt_job_no);
	$file_no = str_replace("'", "", $txt_file_no);
	$internal_ref = str_replace("'", "", $txt_internal_ref);
	$job_year = str_replace("'", "", $cbo_year);
	$txt_styleref = str_replace("'", "", $txt_styleref);
	$company_id = str_replace("'", "", $cbo_company_name);
	$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
	$cbo_brand = str_replace("'", "", $cbo_brand);
	$txt_date = str_replace("'", "", $txt_date);
	$cbo_season_id = str_replace("'", "", $cbo_season_id);
	$approval_type = str_replace("'", "", $cbo_approval_type);
	$cbo_get_upto = str_replace("'", "", $cbo_get_upto);
	$txt_date = str_replace("'", "", $txt_date);
	$cbo_style_owner_id = str_replace("'", "", $cbo_style_owner_id);
	$txt_alter_user_id = str_replace("'", "", $txt_alter_user_id);
	//............................................................................

	$user_id_approval = ($txt_alter_user_id != '') ? $txt_alter_user_id : $user_id;


	$buyer_arr[0] = 0;
	$buyer_brand_arr[0] = 0;
	$electronicDataArr = getSequence(array('company_id' => $company_id, 'entry_form' => 15, 'user_id' => $user_id_approval, 'lib_buyer_arr' => $buyer_arr, 'lib_brand_arr' => $buyer_brand_arr, 'product_dept_arr' => 0, 'lib_item_cat_arr' => 0, 'lib_store_arr' => 0));


	if ($job_no) {
		$whereCon = " and a.JOB_NO like('%$job_no')";
		$where_con = " and a.JOB_NO like('%$job_no')";
	}
	if ($file_no) {
		$whereCon .= " and c.file_no like('%$file_no')";
	}
	if ($txt_styleref) {
		$whereCon .= " and b.style_ref_no like('%$txt_styleref')";
		$where_con .= " and b.style_ref_no like('%$txt_styleref')";
	}
	if ($cbo_season_id) {
		$whereCon .= " and b.SEASON_BUYER_WISE=$cbo_season_id";
		$where_con .= " and b.SEASON_BUYER_WISE=$cbo_season_id";
	}
	if ($cbo_buyer_name) {
		$whereCon .= " and b.buyer_name =$cbo_buyer_name";
		$where_con .= " and b.buyer_name =$cbo_buyer_name";
	}
	if ($cbo_brand) {
		$whereCon .= " and b.brand_id =$cbo_brand";
		$where_con .= " and b.brand_id =$cbo_brand";
	}
	if ($file_no) {
		$whereCon .= " and c.file_no =$file_no";
	}
	if ($internal_ref) {
		$whereCon .= " and c.grouping =$internal_ref";
	}
	if ($job_year) {
		$whereCon .= " and to_char(b.insert_date,'YYYY') =$job_year";
		$where_con .= " and to_char(b.insert_date,'YYYY') =$job_year";
	}
	if ($txt_date != "") {
		if ($cbo_get_upto == 1) {
			$whereCon .= " and a.costing_date>'$txt_date'";
			$where_con .= " and a.costing_date>'$txt_date'";
		} else if ($cbo_get_upto == 2) {
			$whereCon .= " and a.costing_date<='$txt_date'";
			$where_con .= " and a.costing_date<='$txt_date'";
		} else if ($cbo_get_upto == 3) {
			$whereCon .= " and a.costing_date='$txt_date'";
			$where_con .= " and a.costing_date='$txt_date'";
		}
	}
	if ($cbo_style_owner_id) {
		$whereCon .= " and b.STYLE_OWNER=$cbo_style_owner_id";
	}

	$internalRefCond = "rtrim(xmlagg(xmlelement(e,c.grouping,',').extract('//text()') order by c.grouping).GetClobVal(),',')";
	$fileNoCond = "rtrim(xmlagg(xmlelement(e,c.file_no,',').extract('//text()') order by c.file_no).GetClobVal(),',')";



	if ($approval_type == 2) // Un-Approve
	{

		//Match data..................................
		if ($electronicDataArr['user_by'][$user_id_approval]['BUYER_ID']) {
			$where_con .= " and b.BUYER_NAME in(" . $electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'] . ",0)";
			$electronicDataArr['sequ_by'][0]['BUYER_ID'] = $electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'];
		}
		if ($electronicDataArr['user_by'][$user_id_approval]['BRAND_ID']) {
			$where_con .= " and b.BRAND_ID in(" . $electronicDataArr['user_by'][$user_id_approval]['BRAND_ID'] . ",0)";
			$electronicDataArr['sequ_by'][0]['BRAND_ID'] = $electronicDataArr['user_by'][$user_id_approval]['BRAND_ID'];
		}

		// echo $where_con;die;

		$data_mas_sql = " select a.ID,b.BRAND_ID,b.BUYER_NAME from wo_pre_cost_mst a,wo_po_details_master b where b.id=a.job_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.APPROVED<>1 and a.READY_TO_APPROVED=1 and b.COMPANY_NAME=$company_id $where_con";
	 //echo $data_mas_sql; die;


		$tmp_sys_id_arr = array();
		$data_mas_sql_res = sql_select($data_mas_sql);
		foreach ($data_mas_sql_res as $row) {
			for ($seq = ($electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO'] - 1); $seq >= 0; $seq--) {
				if ((in_array($row['BUYER_NAME'], explode(',', $electronicDataArr['sequ_by'][$seq]['BUYER_ID'])) || $row['BUYER_NAME'] == 0) && (in_array($row['BRAND_ID'], explode(',', $electronicDataArr['sequ_by'][$seq]['BRAND_ID'])) || $row['BRAND_ID'] == 0)) {
					if ($electronicDataArr['sequ_by'][$seq]['BYPASS'] == 1) {
						$tmp_sys_id_arr[$seq][$row['ID']] = $row['ID'];
					} else {
						$tmp_sys_id_arr[$seq][$row['ID']] = $row['ID'];
						break;
					}
				}
			}
		}
		//..........................................Match data;

		// print_r($tmp_sys_id_arr);die;


		$sql = '';
		for ($seq = 0; $seq <= count($electronicDataArr['sequ_arr']); $seq++) {
			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq], 0, 'a.ID');

			if ($tmp_sys_id_arr[$seq]) {
				if ($sql != '') {
					$sql .= " UNION ALL ";
				}

				$sql .= "select a.ENTRY_FROM,a.COSTING_PER,a.ID, b.QUOTATION_ID, b.JOB_NO_PREFIX_NUM, to_char(b.insert_date,'YYYY') as YEAR, b.id as JOB_ID, a.JOB_NO, b.BUYER_NAME,b.BRAND_ID, b.SEASON_BUYER_WISE as SEASON, b.SEASON_YEAR, b.STYLE_REF_NO, a.COSTING_DATE, a.APPROVED, a.INSERTED_BY, min(c.shipment_date) as MINSHIP_DATE, max(c.shipment_date) as MAXSHIP_DATE, b.JOB_QUANTITY, (b.job_quantity*b.total_set_qnty) as JOB_QTY_PCS, b.TOTAL_PRICE,$internalRefCond as internalRef, $fileNoCond as fileNo, (select max(APPROVED_NO) from approval_history where ENTRY_FORM=15 and mst_id = a.id) as APPROVED_NO from wo_pre_cost_mst a, wo_po_details_master b, wo_po_break_down c where b.company_name=$company_id and a.approved<>1 and a.APPROVED_SEQU_BY=$seq $sys_con and  a.job_id=b.id and b.id=c.job_id and a.ready_to_approved=1 and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $whereCon group by a.ENTRY_FROM,a.costing_per,b.id, b.quotation_id, b.job_no_prefix_num, to_char(b.insert_date,'YYYY'), a.id, a.job_no, b.buyer_name,b.brand_id, b.SEASON_BUYER_WISE, b.season_year, b.style_ref_no, a.costing_date, a.approved, b.inserted_by, b.job_quantity, b.total_set_qnty, b.total_price, a.INSERTED_BY";
			}
		}

		//echo $sql;die;

	} else {
		$sql = "select a.ENTRY_FROM,a.COSTING_PER,a.ID, b.QUOTATION_ID, b.JOB_NO_PREFIX_NUM, to_char(b.insert_date,'YYYY') as YEAR, b.id as JOB_ID, a.JOB_NO, b.BUYER_NAME,b.BRAND_ID, b.SEASON_BUYER_WISE as SEASON, b.SEASON_YEAR, b.STYLE_REF_NO, a.COSTING_DATE, a.APPROVED, a.INSERTED_BY, min(c.shipment_date) as MINSHIP_DATE, max(c.shipment_date) as MAXSHIP_DATE, b.JOB_QUANTITY, (b.job_quantity*b.total_set_qnty) as JOB_QTY_PCS, b.TOTAL_PRICE,$internalRefCond as internalRef, $fileNoCond as fileNo , (select max(APPROVED_NO) from approval_history where ENTRY_FORM=15 and mst_id = a.id) as APPROVED_NO
		from wo_pre_cost_mst a, wo_po_details_master b, wo_po_break_down c,APPROVAL_MST d 
		where a.job_id=b.id and b.id=c.job_id and d.mst_id=a.id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and a.READY_TO_APPROVED=1 and d.SEQUENCE_NO={$electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']} and a.APPROVED_SEQU_BY=d.SEQUENCE_NO and d.ENTRY_FORM=15 $whereCon and b.COMPANY_NAME=$company_id 
		group by a.ENTRY_FROM,a.costing_per,b.id, b.quotation_id, b.job_no_prefix_num, to_char(b.insert_date,'YYYY'), a.id, a.job_no, b.buyer_name,b.brand_id, b.SEASON_BUYER_WISE, b.season_year, b.style_ref_no, a.costing_date, a.approved, b.inserted_by, b.job_quantity, b.total_set_qnty, b.total_price, a.INSERTED_BY";
	}
   //echo $sql;die;

	$nameArray = sql_select($sql);
	$jobFobValue_arr = array();
	$jobIds_arr = array();
	foreach ($nameArray as $row) {
		$jobFobValue_arr[$row['JOB_NO']] = $row['TOTAL_PRICE'];
		$jobIds_arr[$row['JOB_ID']] = $row['JOB_ID'];
	}
	$jobIds = implode(',', $jobIds_arr);

	$jobId_cond = where_con_using_array($jobIds_arr, 0, 'job_id');



	$bomDtls_arr = array();
	$bomDtlssql = sql_select("select JOB_NO, FABRIC_COST_PERCENT, TRIMS_COST_PERCENT, EMBEL_COST_PERCENT, WASH_COST_PERCENT, CM_COST_PERCENT, MARGIN_PCS_SET_PERCENT,MARGIN_BOM_PER from wo_pre_cost_dtls where status_active=1 and is_deleted=0 $jobId_cond");

	foreach ($bomDtlssql as $row) {
		$bomDtls_arr[$row['JOB_NO']]['trimper'] = $row['TRIMS_COST_PERCENT'];
		$bomDtls_arr[$row['JOB_NO']]['cm'] = $row['CM_COST_PERCENT'];
		$bomDtls_arr[$row['JOB_NO']]['ms'] = $row['FABRIC_COST_PERCENT'] + $row['TRIMS_COST_PERCENT'] + $row['EMBEL_COST_PERCENT'] + $row['WASH_COST_PERCENT'];
		$bomDtls_arr[$row['JOB_NO']]['margin'] = $row['MARGIN_PCS_SET_PERCENT'];
		$bomDtls_arr[$row['JOB_NO']]['net_margin'] = $row['MARGIN_BOM_PER'];
		$fabric_cost_percentArr[$row['JOB_NO']] = $row['FABRIC_COST_PERCENT'];
	}
	unset($bomDtlssql);



	$condition = new condition();
	$condition->company_name("=$cbo_company_name");
	if (str_replace("'", "", $cbo_buyer_name) > 0) {
		$condition->buyer_name("=$cbo_buyer_name");
	}


	if ($jobIds != '') {
		$condition->jobid_in("$jobIds");
	}
	if (str_replace("'", "", $txt_file_no) != '') {
		$condition->file_no("=$txt_file_no");
	}
	if (str_replace("'", "", $txt_internal_ref) != '') {
		$condition->grouping("=$txt_internal_ref");
	}

	$condition->init();
	$fabric = new fabric($condition);
	$fabric_amount_job_uom = $fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
	$wash = new wash($condition);
	$wash_data_array = $wash->getAmountArray_by_jobAndEmbtype();
	$emblishment = new emblishment($condition);
	$emblishment_data_array = $emblishment->getAmountArray_by_jobAndEmbname();
	$conversion = new conversion($condition);
	unset($data_arr_fabric);


	$sql_unapproved = sql_select("select BOOKING_ID,APPROVAL_CAUSE from fabric_booking_approval_cause where  entry_form=15 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr = array();
	foreach ($sql_unapproved as $rowu) {
		$unapproved_request_arr[$rowu['BOOKING_ID']] = $rowu['APPROVAL_CAUSE'];
	}
	$seasonArr = return_library_array("select id,season_name from lib_buyer_season ", "id", "season_name");
	$brandArr = return_library_array("select id,brand_name from lib_buyer_brand ", "id", "brand_name");
	//Pre cost button---------------------------------
	
	$print_report_format_ids = return_field_value("format_id", "lib_report_template", "template_name=" . $cbo_company_name . " and module_id=2 and report_id = 161 and is_deleted=0 and status_active=1");
	$format_ids = explode(",", $print_report_format_ids);
  	//print_r($format_ids);
	$print_report_format = set_print_button(['COMPANY_ID'=>$cbo_company_name,'MODULE_ID'=>2,'REPORT_ID'=>43,'USER_ID'=>$user_id]);
	 $format_ids2 = explode(",", $print_report_format);

     //print_r($format_ids2);
	// echo "select format_id from lib_report_template where template_name=" . $cbo_company_name . " and module_id=2 and report_id =161 and is_deleted=0 and status_active=1";die;

	//print_r($format_ids2);
 



	$width = 2150;
?>
	<form name="requisitionApproval_2" id="requisitionApproval_2">
		<fieldset style="width:<?= $width + 20; ?>px; margin-top:10px">
			<legend>Pre-Costing Approval</legend>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" align="left">
				<thead>
					<th width="40"></th>
					<th width="30">SL</th>
					<th width="100">Job No</th>
					<th width="70">Master Style/Internal Ref.</th>
					<th width="110">Buyer</th>
					<th width="40">Year</th>
					<th width="80">Brand</th>
					<th width="80">Season</th>
					<th width="50">Season Year</th>
					<th width="130">Style Ref.</th>
					<th width="70">Costing Date</th>
					<th width="70">Ship Start</th>
					<th width="70">Ship End</th>
					<th width="70">Job Qty(Pcs)</th>
					<th width="60">Avg. Rate</th>
					<th width="80">Total Value</th>
					<th width="60" title="(Woven Finish/Total Price)*100">Fabric %</th>
					<th width="60">Trims %</th>
					<th width="60">Embel. Cost %</th>
					<th width="60">Gmts.Wash%</th>
					<th width="60">CM %</th>
					<th width="100">Gross Margin %</th>
					<th width="100">Net Margin %</th>
					<th width="140">Unapproved Request</th>
					<th width="65">Insert By</th>
					<th width="100">Approved Date</th>
					<th width="120">Approved Cause</th>
					<th width="120">Refusing Cause</th>
				</thead>
			</table>
			<div style="width:<?= $width + 20; ?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" id="tbl_list_search" align="left">
					<tbody>
						<?
						$i = 1; //die;
						$aop_cost_arr = array(35, 36, 37, 40);
						foreach ($nameArray as $row) 
						{
							$bgcolor = ($i % 2 == 0)?"#E9F3FF":"#FFFFFF";

							$value = $row[csf('id')];
						

								if($row['ENTRY_FROM'] == 158){  
									if ($format_ids2[0] == 129) $action = 'budget5';
									else if ($format_ids2[0] == 730) $action = 'budgetsheet';
									else if ($format_ids2[0] == 50) $action = 'preCostRpt';
									else if ($format_ids2[0] == 51) $action = 'preCostRpt2';
									else if ($format_ids2[0] == 63) $action = 'bomRpt2';
								}
								else {
									if ($format_ids[0] == 51) $action = 'preCostRpt2';
									else if ($format_ids[0] == 307) $action = 'basic_cost';
									else if ($format_ids[0] == 311) $action = 'bom_epm_woven';
									else if ($format_ids[0] == 313) $action = 'mkt_source_cost';
									else if ($format_ids[0] == 158) $action = 'preCostRptWoven';
									else if ($format_ids[0] == 159) $action = 'bomRptWoven';
									else if ($format_ids[0] == 192) $action = 'checkListRpt';
									else if ($format_ids[0] == 761) $action = 'bom_pcs_woven';
									else if ($format_ids[0] == 381) $action = 'mo_sheet_2';
									else if ($format_ids[0] == 403) $action = 'mo_sheet_3';
									else if ($format_ids[0] == 25) $action = 'budgetsheet2';
									else if ($format_ids[0] == 221) $action = 'fabric_cost_detail';
									else if ($format_ids[0] == 730) $action = 'budgetsheet';
									else if ($format_ids[0] == 50) $action = 'preCostRpt';
									else if ($format_ids[0] == 51) $action = 'preCostRpt2';
									else if ($format_ids[0] == 63) $action = 'bomRpt2';
									else if ($format_ids[0] == 171) $action = 'preCostRpt4';
									else if ($format_ids[0] == 173) $action = 'preCostRpt5';
									else if ($format_ids[0] == 405) $action = 'materialSheet2';
									else if ($format_ids[0] == 769) $action = 'preCostRpt7';
									else if ($format_ids[0] == 874) $action = 'preCostRpt13';
									else if ($format_ids[0] == 52) $action = 'bomRpt';
									else if ($format_ids[0] == 158) $action = 'preCostRptWoven';
									else if ($format_ids[0] == 170) $action = 'preCostRpt3';
									else if ($format_ids[0] == 351) $action = 'bomRpt4';
									else if ($format_ids[0] == 268) $action = 'budget_4';
									else if ($format_ids[0] == 765) $action = 'bomRpt5';
									else if ($format_ids[0] == 882) $action = 'bomRpt4_v2';
								}
									//else if ($format_ids[0] == 769) $action = 'preCostRpt7';


								//echo $action;die;

								$function_arr = array();
								foreach(range(1,$row['APPROVED_NO']) as $version){
									$function_arr[$version] = "generate_worder_report('" . $action . "','" . $row[csf('job_no')] . "'," . $cbo_company_name . "," . $row[csf('buyer_name')] . ",'" . $row[csf('style_ref_no')] . "','" . $row[csf('costing_date')] . "','','" . $row[csf('costing_per')] . "'," . $version . "," . $row['ENTRY_FROM'] . "," . $row['JOB_ID'] . ");";
								}




								$jobavgRate = 0;
								$int_ref = "";
								$file_numbers = "";
								$jobavgRate = $row[csf('total_price')] / $row[csf('job_quantity')];
								if ($db_type == 2) $row[csf('internalRef')] = $row[csf('internalRef')]->load();

								$int_ref = implode(",", array_unique(explode(",", chop($row[csf('internalRef')], ","))));
								$finishPercent = $trimPercent = $fabpurchase_per = $aopamt = $yarn_dyeingAmt = $yarn_dyeingPer = $msper = $aopPer = $cmper = $marginper = 0;
								$trimPercent = $bomDtls_arr[$row[csf('job_no')]]['trimper'];

								$finishPercent = $fabric_cost_percentArr[$row[csf('job_no')]];

								

								$washPercent = (array_sum($wash_data_array[$row[csf('job_no')]]) / $row[csf('total_price')]) * 100;
								$emblishmentPercent = (array_sum($emblishment_data_array[$row[csf('job_no')]]) / $row[csf('total_price')]) * 100;

								foreach ($aop_cost_arr as $aop_process_id) {
									$aopamt += array_sum($conv_amount_arr[$row[csf('job_no')]][$aop_process_id]);
								}
								$aopPer = ($aopamt / $row[csf('total_price')]) * 100;

								$msper = $bomDtls_arr[$row[csf('job_no')]]['ms'];
								$cmper = $bomDtls_arr[$row[csf('job_no')]]['cm'];
								$marginper = $bomDtls_arr[$row[csf('job_no')]]['margin'];
								$netmarginper = $bomDtls_arr[$row[csf('job_no')]]['net_margin'];

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>" align="center">
									<td width="40" align="center" valign="middle">
										<input type="checkbox" id="tbl_<?= $i; ?>" />
										<input id="booking_id_<?= $i; ?>" name="booking_id[]" type="hidden" value="<?= $value; ?>" />
										<input id="booking_no_<?= $i; ?>" name="booking_no[]" type="hidden" value="<?= $row[csf('job_no')]; ?>" />
										<input id="hidden_job_id_<?= $i; ?>" name="hidden_job_id[]" type="hidden" value="<?= $row['JOB_ID']; ?>" />
										<input id="approval_id_<?= $i; ?>" name="approval_id[]" type="hidden" value="<?= $row[csf('approval_id')]; ?>" />
									</td>
									<td width="30" align="center"><?= $i; ?></td>
									<td width="100">
										<? 
										if($row['APPROVED_NO'] != ''){
											foreach(range(1,$row['APPROVED_NO']) as $version){?>
												<a href='##' onclick="<?= $function_arr[$version]; ?>"><?= $row[csf('job_no_prefix_num')]; ?> V-<?= $version;?></a>,
											<? } 
										}
										else{
											?>
											<a href='##' onclick="<?= $function_arr[0]; ?>"><?= $row[csf('job_no_prefix_num')]; ?></a>
											<?
										}
										?>
										
									
									</td>
									<td width="70"><?= $int_ref; ?></td>
									<td width="110">
										<p><?= $buyer_arr[$row[csf('buyer_name')]]; ?></p>
									</td>
									<td width="40" align="center"><?= $row[csf('year')]; ?></td>
									<td width="80"><?= $brandArr[$row[csf('brand_id')]]; ?></td>
									<td width="80"><?= $seasonArr[$row[csf('season')]]; ?></td>
									<td width="50"><?= $row[csf('season_year')]; ?></td>
									<td width="125"><?= $row[csf('style_ref_no')]; ?></td>
									<td width="70" align="center"><? if ($row[csf('costing_date')] != "0000-00-00") echo change_date_format($row[csf('costing_date')]); ?></td>
									<td align="center" width="70"><? if ($row[csf('minship_date')] != "0000-00-00") echo change_date_format($row[csf('minship_date')]); ?></td>
									<td align="center" width="70"><? if ($row[csf('maxship_date')] != "0000-00-00") echo change_date_format($row[csf('maxship_date')]); ?></td>
									<td width="70" align="right"><?= number_format($row[csf('job_qty_pcs')]); ?></td>
									<td width="60" align="right"><?= number_format($jobavgRate, 4); ?></td>
									<td width="80" align="right"><?= number_format($row[csf('total_price')], 2); ?></td>

									<td width="60" align="right"><?= number_format($finishPercent, 2); ?></td>
									<td width="60" align="right"><?= number_format($trimPercent, 2); ?></td>
									<td width="60" align="right"><?= number_format($emblishmentPercent, 2); ?></td>
									<td width="60" align="right"><?= number_format($washPercent, 2); ?></td>
									<td width="60" align="right" id="tdCm_<?= $i; ?>"><?= number_format($cmper, 2); ?></td>
									<td width="100" align="right"><?= number_format($marginper, 2); ?></td>
									<td width="100" align="right"><?= number_format($netmarginper, 2); ?></td>

									<td width="140"><? if ($approval_type == 1) echo $unapproved_request_arr[$value]; ?> </td>
									<td width="65"><? echo ucfirst($user_arr[$row[csf('inserted_by')]]); ?></td>
									<td align="center" width="100"><? if ($row[csf('approved_date')] != "0000-00-00") echo change_date_format($row[csf('approved_date')]); ?></td>
									<?
									$booking_id = $row[csf('id')];
									$approved_reason_arr = sql_select("SELECT id,approval_cause from fabric_booking_approval_cause where  booking_id='$booking_id' order by id desc ");
										// print_r($approved_reason_arr);
									?>

									<td width="120"> <input style="width:100px;" type="text" class="text_boxes" name="txtAppCause_<? echo $row[csf('id')]; ?>" id="txtAppCause_<? echo $row[csf('id')]; ?>" placeholder="browse" onClick="openmypage_approve_cause('requires/pre_costing_approval_v3_controller.php?action=approve_cause_popup','Refusing Cause','<? echo $row[csf('id')]; ?>');" value="<? echo $approved_reason_arr[0][csf('approval_cause')];?>" /></td>
									<?
									$mst_id = $row[csf('id')];
									$refusing_reason_arr = sql_select("SELECT id,refusing_reason from refusing_cause_history where  mst_id='$mst_id' order by id desc ");
									//	 print_r($refusing_reason_arr);
									?>

									<td width="120"> <input style="width:100px;" type="text" class="text_boxes" name="txtCause_<? echo $row[csf('id')]; ?>" id="txtCause_<? echo $row[csf('id')]; ?>" placeholder="browse" onClick="openmypage_refusing_cause('requires/pre_costing_approval_controller.php?action=refusing_cause_popup','Refusing Cause','<? echo $row[csf('id')]; ?>');" value="<? echo $refusing_reason_arr[0][csf('refusing_reason')];?>" /></td>
								</tr>
								<?
								$i++;
						 

								if ($all_approval_id != "") {
								$con = connect();
								$rID = sql_multirow_update("approval_history", "current_approval_status", 0, "id", $all_approval_id, 1);
									if ($db_type == 2 || $db_type == 1) {
										if ($rID == 1) {
											oci_commit($con);
											echo $msg . "**" . $response;
										} else {
											oci_rollback($con);
											echo $msg . "**" . $response;
										}
									}
									disconnect($con);
								}
							$denyBtn = "";
							$denyBtnMsg = "";
							$btnmsg = "";
							if ($approval_type == 2) {
								$denyBtn = "";
								$denyBtnMsg = "Deny";
							} else {
								$denyBtn = " display:none";
								$denyBtnMsg = "";
							}
						}
						?>
					</tbody>
				</table>
			</div>
			<table cellspacing="0" cellpadding="0" border="0" rules="all" width="<?= $width; ?>" class="rpt_table" align="left">
				<tfoot>
					<td width="40" align="center"><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
					<td colspan="2" align="left">
						<input type="button" value="<? if ($approval_type == 1) echo "Un-Approve";else echo "Approve"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)" />&nbsp;
						<input type="button" value="<?= $denyBtnMsg; ?>" class="formbutton" style="width:100px;<?= $denyBtn; ?>" onClick="submit_approved(<?= $i; ?>,5);" />

					</td>
				</tfoot>
			</table>
		</fieldset>
	</form>
<?
	exit();
}



if ($action == "approve") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$con = connect();

	$cbo_company_name = str_replace("'", "", $cbo_company_name);
	$txt_alter_user_id = str_replace("'", "", $txt_alter_user_id);
	$approval_type = str_replace("'", "", $approval_type);
	$booking_nos = str_replace("'", "", $booking_nos);
	$booking_ids = str_replace("'", "", $booking_ids);
	$approval_ids = str_replace("'", "", $approval_ids);
	$job_ids = str_replace("'", "", $job_ids);
	$user_id_approval = ($txt_alter_user_id) ? $txt_alter_user_id : $user_id;




	$sql = "select A.ID,a.APPROVED,b.BUYER_NAME,b.BRAND_ID,a.READY_TO_APPROVED from wo_pre_cost_mst a, wo_po_details_master b where a.job_id=b.id and a.id in($booking_ids)";
	$sqlResult = sql_select($sql);
	foreach ($sqlResult as $row) {
		if ($row['READY_TO_APPROVED'] != 1) {
			echo "Ready to approved NO is not allow";
			die;
		}
		$matchDataArr[$row['ID']] = array('buyer' => $row['BUYER_NAME'], 'brand' => $row['BRAND_ID'], 'item' => 0, 'store' => 0);
		$approved_status_arr[$row['ID']] = $row['APPROVED'];
	}


	$buyer_brand_arr[0] = 0;
	$finalDataArr = getFinalUser(array('company_id' => $cbo_company_name, 'entry_form' => 15, 'lib_buyer_arr' => $buyer_arr, 'lib_brand_arr' => $buyer_brand_arr, 'lib_item_cat_arr' => $item_cat_arr, 'lib_store_arr' => $lib_store_arr, 'product_dept_arr' => $product_dept, 'match_data' => $matchDataArr));

	$sequ_no_arr_by_sys_id = $finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];

	//echo count($finalDataArr['user_seq']);die;


	if ($approval_type == 2) {

		//$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and APPROVED_BY=$user_id_approval and entry_form=15 group by mst_id", "mst_id", "approved_no");
		$max_precost_his_approved_no_arr = return_library_array("select pre_cost_mst_id, max(approved_no) as approved_no from wo_pre_cost_mst_histry where pre_cost_mst_id in($booking_ids)  group by pre_cost_mst_id", "pre_cost_mst_id", "approved_no");

		$id = return_next_id("id", "approval_mst", 1);
		$ahid = return_next_id("id", "approval_history", 1);

		$booking_nos_arr = explode(',', $booking_nos);
		$job_ids_arr = explode(',', $job_ids);
		$target_app_id_arr = explode(',', $booking_ids);
		$i = 0;
		foreach ($target_app_id_arr as $mst_id) {
			$approved = (max($finalDataArr['final_seq'][$mst_id]) == $user_sequence_no) ? 1 : 3;

			if ($data_array != '') {
				$data_array .= ",";
			}
			$data_array .= "(" . $id . ",15," . $mst_id . "," . $user_sequence_no . "," . $user_id_approval . ",'" . $pc_date_time . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','" . $user_ip . "')";
			$id = $id + 1;


			//$approved_no = ($max_approved_no_arr[$mst_id] == '') ? 1 : $max_approved_no_arr[$mst_id] + 1;
			$approved_no = ($max_precost_his_approved_no_arr[$mst_id] == '') ? 1 : $max_precost_his_approved_no_arr[$mst_id] + 1;

			$approved_status = $approved_status_arr[$mst_id] * 1;
			if ($approved_status == 0 || $approved_status == 2) {
				$pre_his_approved_no = ($max_precost_his_approved_no_arr[$mst_id] == '') ? 1 : $max_precost_his_approved_no_arr[$mst_id] + 1;
				//$approved_no_array[$booking_nos_arr[$i]] = $pre_his_approved_no;
				$approved_no_by_job_id_array[$job_ids_arr[$i]] = $pre_his_approved_no;
				//$target_job_no_arr[$booking_nos_arr[$i]] = $booking_nos_arr[$i];
			}
			$i++;



			if ($history_data_array != "") $history_data_array .= ",";
			$history_data_array .= "(" . $ahid . ",15," . $mst_id . "," . $approved_no . ",'" . $user_sequence_no . "',1," . $user_id_approval . ",'" . $pc_date_time . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1," . $approved . ")";
			$ahid++;

			//mst data.......................
			//$approved=(max($finalDataArr[final_seq][$mst_id])==$user_sequence_no)?1:3;
			$data_array_up[$mst_id] = explode(",", ("" . $approved . "," . $user_sequence_no . ",'" . $pc_date_time . "'," . $user_id_approval . ""));
		}

		$flag = 1;
		if ($flag == 1) {
			$field_array = "id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,USER_IP";
			$rID1 = sql_insert("approval_mst", $field_array, $data_array, 0);
			if ($rID1) $flag = 1;
			else $flag = 0;
		}


		if ($flag == 1) {
			$field_array_up = "APPROVED*APPROVED_SEQU_BY*APPROVED_DATE*APPROVED_BY";
			$rID2 = execute_query(bulk_update_sql_statement("wo_pre_cost_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr));
			if ($rID2) $flag = 1;
			else $flag = 0;
		}

		if ($flag == 1) {
			$query = "UPDATE approval_history SET current_approval_status=0 WHERE entry_form=15 and mst_id in ($booking_ids)";
			$rID3 = execute_query($query, 1);
			if ($rID3) $flag = 1;
			else $flag = 0;
		}

		if ($flag == 1) {
			$field_array = "id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
			$rID4 = sql_insert("approval_history", $field_array, $history_data_array, 0);
			if ($rID4) $flag = 1;
			else $flag = 0;
		}

		
		if (count($approved_no_by_job_id_array)) {
			$flag = archive(['approved_no_by_job_id_array' => $approved_no_by_job_id_array]);
			//echo $flag;oci_rollback($con);die;
		}

		// echo "10**" . $rID1 . "," . $rID2 . "," . $rID3 . "," . $rID4 . "," . $rID5 . "," . $rID6 . "," . $rID7 . "," . $rID8 . "," . $rID9 . "," . $rID10 . "," . $rID11 . "," . $rID12 . "," . $rID13 . "," . $rID14 . "," . $rID15 . "," . $rID16 . "," . $flag;
		// oci_rollback($con);
		// die;

		if ($flag == 1){$msg = '19';} else{ $msg = '21';}
	} else if ($approval_type == 1) {

		$booking_ids_all = explode(",", $booking_ids);
		$booking_ids = '';
		$app_ids = '';
		foreach ($booking_ids_all as $value) {
			$data = explode('**', $value);
			$booking_id = $data[0];
			$app_id = $data[1];

			if ($booking_ids == '') $booking_ids = $booking_id;
			else $booking_ids .= "," . $booking_id;
			if ($app_ids == '') $app_ids = $app_id;
			else $app_ids .= "," . $app_id;
		}

		$next_user_app = sql_select("select id from approval_history where mst_id in($booking_ids) and entry_form=15 and sequence_no >$user_sequence_no and current_approval_status=1 group by id");

		if (count($next_user_app) > 0) {
			echo "25**unapproved";
			disconnect($con);
			die;
		}

		$rID1 = sql_multirow_update("wo_pre_cost_mst", "approved*ready_to_approved*APPROVED_SEQU_BY", '0*0*0', "id", $booking_ids, 0);
		if ($rID1) $flag = 1;
		else $flag = 0;

		if ($flag == 1) {
			$query = "delete from approval_mst  WHERE entry_form=15 and mst_id in ($booking_ids)";
			$rID3 = execute_query($query, 1);
			if ($rID3) $flag = 1;
			else $flag = 0;
		}

		if ($flag == 1) {
			$unapproved_status = "UPDATE fabric_booking_approval_cause SET status_active=0,is_deleted=1 WHERE entry_form=15 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id in ($booking_ids)";
			$rID4 = execute_query($unapproved_status, 1);
			if ($rID4) $flag = 1;
			else $flag = 0;
		}

		if ($flag == 1) {
			$query = "UPDATE approval_history SET current_approval_status=0,IS_SIGNING=0, un_approved_by='" . $user_id_approval . "', un_approved_date='" . $pc_date_time . "', updated_by='" . $user_id_approval . "', update_date='" . $pc_date_time . "' ,APPROVED=0 WHERE entry_form=15 and current_approval_status=1 and mst_id in ($booking_ids)";
			$rID5 = execute_query($query, 1);
			if ($rID5) $flag = 1;
			else $flag = 0;
		}

		if ($flag == 1) {
			$query = "UPDATE approval_history SET current_approval_status=0,APPROVED=0,IS_SIGNING=0 WHERE entry_form=15 and mst_id in ($booking_ids) and approved_by <> $user_id_approval ";
			$rID2 = execute_query($query, 1);
			if ($rID2) $flag = 1;
			else $flag = 0;
		}


		// echo "10**".$rID1.",".$rID2.",".$rID3.",".$rID4.",".$rID5.",".$flag;oci_rollback($con);die;

		$response = $booking_ids;
		if ($flag == 1) $msg = '20';
		else $msg = '22';
	} else if ($approval_type == 5) {
		$sqlBookinghistory = "select id, mst_id from approval_history where current_approval_status=1 and entry_form=15 and mst_id in ($booking_ids) ";

		$nameArray = sql_select($sqlBookinghistory);
		$bookidstr = "";
		$approval_id = "";

		foreach ($nameArray as $row) {
			if ($bookidstr == "") $bookidstr = $row[csf('mst_id')];
			else $bookidstr .= ',' . $row[csf('mst_id')];
			if ($approval_id == "") $approval_id = $row[csf('id')];
			else $approval_id .= ',' . $row[csf('id')];
		}

		$appBookNoId = implode(",", array_filter(array_unique(explode(",", $bookidstr))));
		$approval_ids = implode(",", array_filter(array_unique(explode(",", $approval_id))));



		$rID = sql_multirow_update("wo_pre_cost_mst", "approved*ready_to_approved", '2*0', "id", $booking_ids, 0);
		if ($rID) $flag = 1;
		else $flag = 0;

		//$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
		if ($approval_ids != "") {
			$query = "UPDATE approval_history SET current_approval_status=0, un_approved_by='" . $user_id_approval . "', un_approved_date='" . $pc_date_time . "', updated_by='" . $user_id . "', update_date='" . $pc_date_time . "', APPROVED=2 WHERE entry_form=15 and current_approval_status=1 and id in ($approval_ids)";
			//echo "10**".$query;
			$rID2 = execute_query($query, 0);
			if ($flag == 1) {
				if ($rID2) $flag = 1;
				else $flag = 0;
			}
		}

		//echo "10**".$rID.'='.$rID2.'='.$rID4.'='.$flag; die;
		$response = $booking_ids;
		if ($flag == 1) $msg = '50';
		else $msg = '51';
	}


	if ($flag == 1) {
		oci_commit($con);
		echo $msg . "**" . $response;
	} else {
		oci_rollback($con);
		echo $msg . "**" . $response;
	}
	disconnect($con);
	die;
}

if ($action == "refusing_cause_popup") {
	extract($_REQUEST);
	echo load_html_head_contents("Refusing Cause Info", "../../", 1, 1, $unicode);
	$permission = "1_1_1_1";

	$sql_cause = "select refusing_reason from refusing_cause_history where entry_form=15 and mst_id='$quo_id'";

	$nameArray_cause = sql_select($sql_cause);
	$app_cause = '';
	foreach ($nameArray_cause as $row) {
		$app_cause .= $row[csf("refusing_reason")] . ",";
	}
	$app_cause = chop($app_cause, ",");
	//print_r($app_cause);
 ?>
	<script>
		var permission = '<?= $permission; ?>';

		function set_values(cause) {
			var refusing_cause = document.getElementById('txt_refusing_cause').value;
			if (refusing_cause == '') {
				document.getElementById('txt_refusing_cause').value = refusing_cause;
				parent.emailwindow.hide();
			} else {
				alert("Please save refusing cause first or empty");
				return;
			}
		}

		function fnc_cause_info(operation) {
			var refusing_cause = $("#txt_refusing_cause").val();
			var quo_id = $("#hidden_quo_id").val();
			if (form_validation('txt_refusing_cause', 'Refusing Cause') == false) {
				return;
			} else {
				var data = "action=save_update_delete_refusing_cause&operation=" + operation + "&refusing_cause=" + refusing_cause + "&quo_id=" + quo_id;
				http.open("POST", "pre_costing_approval_controller.php", true);
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_cause_info_reponse;
			}
		}

		function fnc_cause_info_reponse() {
			if (http.readyState == 4) {
				var response = trim(http.responseText).split('**');
				if (response[0] == 0) {
					alert("Data saved successfully");
					//document.getElementById('txt_refusing_cause').value =response[1];
					parent.emailwindow.hide();
				} else {
					alert("Data not saved");
					return;
				}
			}
		}
	</script>

	<body onload="set_hotkey();">
		<div align="center" style="width:100%;">
			<fieldset style="width:470px;">
				<legend>Refusing Cause</legend>
				<form name="causeinfo_1" id="causeinfo_1" autocomplete="off">
					<table cellpadding="0" cellspacing="2" width="470px">
						<tr>
							<td width="100" class="must_entry_caption">Refusing Cause</td>
							<td>
								<input type="text" name="txt_refusing_cause" id="txt_refusing_cause" class="text_boxes" style="width:320px;height: 100px;" value="<?= $cause; ?>" />
								<input type="hidden" name="hidden_quo_id" id="hidden_quo_id" value="<? echo $quo_id; ?>">
							</td>
						</tr>
						<tr>
							<td colspan="4" align="center" class="button_container">
								<?
								if (!empty($app_cause)) {
									echo load_submit_buttons($permission, "fnc_cause_info", 1, 0, "reset_form('causeinfo_1','','')", 1);
								} else {
									echo load_submit_buttons($permission, "fnc_cause_info", 0, 0, "reset_form('causeinfo_1','','','','','');", 1);
								}
								?> </br>
								<input type="button" class="formbutton" value="Close" name="close_buttons" id="close_buttons" onClick="set_values();" style="width:50px;height: 35px;">
							</td>
						</tr>
						<tr>
							<td colspan="4" align="center">&nbsp;</td>
						</tr>
					</table>
				</form>
			</fieldset>
			<?
			$sqlHis = "select approval_cause from approval_cause_refusing_his where entry_form=15 and approval_type=1 and booking_id='$quo_id' and status_active=1 and is_deleted=0 order by id Desc";
			$sqlHisRes = sql_select($sqlHis);
			?>
			<table align="center" cellspacing="0" width="420" class="rpt_table" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th>Refusing History</th>
				</thead>
			</table>
			<div style="width:420px; overflow-y:scroll; max-height:260px;" align="center">
				<table align="center" cellspacing="0" width="403" class="rpt_table" border="1" rules="all">
					<?
					$i = 1;
					foreach ($sqlHisRes as $hrow) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>');">
							<td width="30"><?= $i; ?></td>
							<td style="word-break:break-all"><?= $hrow[csf('approval_cause')]; ?></td>
						</tr>
					<?
						$i++;
					}
					?>
				</table>
			</div>
		</div>
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</body>
 <?
	exit();
}

if ($action == "approve_cause_popup") {
	extract($_REQUEST);
	echo load_html_head_contents("Refusing Cause Info", "../../", 1, 1, $unicode);
	$permission = "1_1_1_1";

	$sql_cause = "select approval_cause from fabric_booking_approval_cause where entry_form=15 and booking_id='$quo_id'";

	$nameArray_cause = sql_select($sql_cause);
	$app_cause = '';
	foreach ($nameArray_cause as $row) {
		$app_cause .= $row[csf("approval_cause")] . ",";
	}
	$app_cause = chop($app_cause, ",");
	//print_r($app_cause);
 ?>
	<script>
		var permission = '<?= $permission; ?>';

		function set_values(cause) {
			var refusing_cause = document.getElementById('txt_approved_cause').value;
			if (refusing_cause == '') {
				document.getElementById('txt_approved_cause').value = refusing_cause;
				parent.emailwindow.hide();
			} else {
				alert("Please save cause first or empty");
				return;
			}
		}

		function fnc_cause_info(operation) {
			var refusing_cause = $("#txt_approved_cause").val();
			var quo_id = $("#hidden_quo_id").val();
			if (form_validation('txt_approved_cause', 'Approve Cause') == false) {
				return;
			} else {
				var data = "action=save_update_delete_approval_cause&operation=" + operation + "&refusing_cause=" + refusing_cause + "&quo_id=" + quo_id;
				http.open("POST", "pre_costing_approval_v3_controller.php", true);
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_cause_info_reponse;
			}
		}

		function fnc_cause_info_reponse() {
			if (http.readyState == 4) {
				var response = trim(http.responseText).split('**');
				if (response[0] == 0) {
					alert("Data saved successfully");
					//document.getElementById('txt_refusing_cause').value =response[1];
					parent.emailwindow.hide();
				} else {
					alert("Data not saved");
					return;
				}
			}
		}
	</script>

	<body onload="set_hotkey();">
		<div align="center" style="width:100%;">
			<fieldset style="width:470px;">
				<legend>Refusing Cause</legend>
				<form name="causeinfo_1" id="causeinfo_1" autocomplete="off">
					<table cellpadding="0" cellspacing="2" width="470px">
						<tr>
							<td width="100" class="must_entry_caption">Refusing Cause</td>
							<td>
								<input type="text" name="txt_approved_cause" id="txt_approved_cause" class="text_boxes" style="width:320px;height: 100px;" value="<?= $cause; ?>" />
								<input type="hidden" name="hidden_quo_id" id="hidden_quo_id" value="<? echo $quo_id; ?>">
							</td>
						</tr>
						<tr>
							<td colspan="4" align="center" class="button_container">
								<?
								if (!empty($app_cause)) {
									echo load_submit_buttons($permission, "fnc_cause_info", 1, 0, "reset_form('causeinfo_1','','')", 1);
								} else {
									echo load_submit_buttons($permission, "fnc_cause_info", 0, 0, "reset_form('causeinfo_1','','','','','');", 1);
								}
								?> </br>
								<input type="button" class="formbutton" value="Close" name="close_buttons" id="close_buttons" onClick="set_values();" style="width:50px;height: 35px;">
							</td>
						</tr>
						<tr>
							<td colspan="4" align="center">&nbsp;</td>
						</tr>
					</table>
				</form>
			</fieldset>
			<?
			$sqlHis = "select approval_cause from approval_cause_refusing_his where entry_form=15 and approval_type=1 and booking_id='$quo_id' and status_active=1 and is_deleted=0 order by id Desc";
			$sqlHisRes = sql_select($sqlHis);
			?>
			<table align="center" cellspacing="0" width="420" class="rpt_table" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th>Refusing History</th>
				</thead>
			</table>
			<div style="width:420px; overflow-y:scroll; max-height:260px;" align="center">
				<table align="center" cellspacing="0" width="403" class="rpt_table" border="1" rules="all">
					<?
					$i = 1;
					foreach ($sqlHisRes as $hrow) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>');">
							<td width="30"><?= $i; ?></td>
							<td style="word-break:break-all"><?= $hrow[csf('approval_cause')]; ?></td>
						</tr>
					<?
						$i++;
					}
					?>
				</table>
			</div>
		</div>
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</body>
 <?
	exit();
}

if($action=="save_update_delete_approval_cause")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $_REQUEST ));
	$flag=1;
	if(is_duplicate_field( "approval_cause", "approval_cause_refusing_his", "approval_cause='".$refusing_cause."' and entry_form=15 and booking_id='".str_replace("'", "", $quo_id)."' and approval_type=1 and status_active=1 and is_deleted=0" )==1)
	{
		//
	}
	else
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//$id_his=return_next_id( "id", "approval_cause_refusing_his", 1);
		$idpre=return_field_value("max(id) as id", "refusing_cause_history", "mst_id=".$quo_id." and entry_form=15 group by mst_id","id");
		$sqlHis="insert into approval_cause_refusing_his( id, cause_id, entry_form, booking_id, approval_type, approval_cause, inserted_by, insert_date, updated_by, update_date)
				select '', id, entry_form, mst_id, 1, refusing_reason, inserted_by, insert_date, updated_by, update_date from refusing_cause_history where mst_id=".$quo_id." and entry_form=15 and id=$idpre"; //die;
		
		if(count($sqlHis)>0)
		{
			$rID3=execute_query($sqlHis,0);
			if($flag==1)
			{
				if($rID3==1) $flag=1; else $flag=0;
			}
		}
	}
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
		// $get_history = sql_select("SELECT id from approval_history where mst_id='$quo_id' and entry_form =15 and current_approval_status=1");
		$id=return_next_id( "id", "fabric_booking_approval_cause", 1);
		$field_array = "id,entry_form,booking_id,approval_cause,inserted_by,insert_date";
		$data_array = "(".$id.",15,".$quo_id.",'".$refusing_cause."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,1);
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "0**$refusing_cause";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "0**$refusing_cause";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
		//$id=return_next_id( "id", "refusing_cause_history", 1);
		
		$idpre=return_field_value("max(id) as id", "fabric_booking_approval_cause", "booking_id=".$quo_id." and entry_form=15 group by booking_id","id");
		$field_array="approval_cause*updated_by*update_date";
		$data_array="'".$refusing_cause."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id",$idpre,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**".$rID.'='.$rID3.'='.$flag; die;
		
		if($db_type==0)
		{
			if( $flag==1)
			{
				mysql_query("COMMIT");
				echo "0**$refusing_cause";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**$refusing_cause";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}


if ($action == "img") {
	echo load_html_head_contents("Image View", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
			<table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
				<tr>
					<?
					$i = 0;
					$sql = "select image_location from common_photo_library where master_tble_id='$id' and form_name='knit_order_entry' and file_type=1";

					$result = sql_select($sql);
					foreach ($result as $row) {
						$i++;
					?>
						<!--<td align="center"><? echo $row[csf('image_location')]; ?></td>-->
						<td align="center"><img width="300px" height="180px" src="../../<? echo $row[csf('image_location')]; ?>" /></td>
					<?
						if ($i % 2 == 0) echo "</tr><tr>";
					}
					?>
				</tr>
			</table>
		</div>
	</fieldset>
<?
	exit();
}

if ($action == 'user_popup') {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, '', 1, '');
?>
	<script>
		function js_set_value(id) {
			document.getElementById('selected_id').value = id;
			parent.emailwindow.hide();
		}
	</script>

	<form>
		<input type="hidden" id="selected_id" name="selected_id" />
		<?php
		$custom_designation = return_library_array("select id,custom_designation from lib_designation ", 'id', 'custom_designation');
		$Department = return_library_array("select id,department_name from  lib_department ", 'id', 'department_name');;
		$sql = "select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id and b.entry_form=15 order by b.sequence_no";
		//echo $sql;
		$arr = array(2 => $custom_designation, 3 => $Department);
		echo  create_list_view("list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,", "630", "360", 0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr, "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);');
		?>

	</form>
	<script language="javascript" type="text/javascript">
		setFilterGrid("tbl_style_ref");
	</script>


	<?
}

if ($action == "populate_cm_compulsory") {
	$cm_cost_compulsory = return_field_value("cm_cost_compulsory", "variable_order_tracking", "company_name ='" . $data . "' and variable_list=22 and is_deleted=0 and status_active=1");
	echo $cm_cost_compulsory;
	exit();
}

if ($action == "app_mail_notification") {

	require('../../mailer/class.phpmailer.php');
	require('../../auto_mail/setting/mail_setting.php');

	list($sysId, $mailId, $txt_alter_user, $type) = explode('__', $data);
	$sysId = str_replace('*', ',', $sysId);

	$txt_alter_user = str_replace("'", "", $txt_alter_user);
	$user_id = ($txt_alter_user != '') ? $txt_alter_user : $user_id;

	$user_maill_arr = return_library_array("select id,USER_EMAIL from  user_passwd where USER_EMAIL is not null", 'id', 'USER_EMAIL');



	$sql = "select a.ID,a.APPROVED,a.INSERTED_BY,b.JOB_NO,b.STYLE_REF_NO,b.COMPANY_NAME,b.BUYER_NAME from wo_pre_cost_mst a,wo_po_details_master b where a.JOB_NO=b.JOB_NO and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in($sysId)";

	$sql_dtls = sql_select($sql);
	$dataArr = array();
	$insertUserMailToArr = array($mailId);
	foreach ($sql_dtls as $rows) {
		$dataArr['company'][$rows['COMPANY_NAME']] = $rows['COMPANY_NAME'];
		$dataArr['buyer_arr'][$rows['BUYER_NAME']] = $rows['BUYER_NAME'];
		$dataArr['data'][$rows['COMPANY_NAME']][$rows['ID']] = $rows;

		if ($rows['APPROVED'] == 1 || $type == 5 || $type == 1) {
			$insertUserMailToArr[$rows['INSERTED_BY']] = $user_maill_arr[$rows['INSERTED_BY']];
		}
		if ($rows['APPROVED'] == 1) {
			$greetingMsgForinsertBy = "Dear Concerned,	<br />Job Is Approved following reference.<br />";
			$subjectForinsertBy = "Pre-cost approved";
		} else if ($type == 5) {
			$greetingMsgForinsertBy = "Dear Concerned,	<br />Job Is Deny following reference.<br />";
			$subjectForinsertBy = "Pre-cost deny";
		} else if ($type == 1) {
			$greetingMsgForinsertBy = "Dear Concerned,	<br />Job Is Unapproved following reference.<br />";
			$subjectForinsertBy = "Pre-cost unapproved";
		}
	}


	foreach ($dataArr['company'] as $company_name) {

		$user_sequence_no = return_field_value("sequence_no", "electronic_approval_setup", "company_id=$company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");


		$elcetronicSql = "SELECT a.BUYER_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1 AND a.IS_DELETED=0 and a.entry_form = 15 and a.company_id=$company_name and a.SEQUENCE_NO > $user_sequence_no order by a.SEQUENCE_NO"; //and a.page_id=2150 and a.entry_form=46
		//echo $elcetronicSql;die;
		$mailToArr = array($mailId);
		$elcetronicSqlRes = sql_select($elcetronicSql);
		foreach ($elcetronicSqlRes as $rows) {

			if ($rows['BUYER_ID'] != '') {
				foreach (explode(',', $rows['BUYER_ID']) as $bi) {
					if ($rows[USER_EMAIL] != '' && in_array($bi, $dataArr['buyer_arr'])) {
						$mailToArr[] = $rows[USER_EMAIL];
					}
				}
				if ($rows['BYPASS'] == 2) {
					break;
				}
			} else {
				if ($rows[USER_EMAIL]) {
					$mailToArr[] = $rows[USER_EMAIL];
				}
				if ($rows['BYPASS'] == 2) {
					break;
				}
			}
		}




		ob_start();
	?>
		<table rules="all" border="1">
			<tr bgcolor="#CCCCCC">
				<td>SL</td>
				<td>Company</td>
				<td>Job No</td>
				<td>Style Ref</td>
				<td>Buyer</td>
			</tr>
			<?php
			$i = 1;
			foreach ($dataArr[data][$company_name] as $row) {

			?>
				<tr>
					<td><?= $i; ?></td>
					<td><?= $company_arr[$company_name] ?></td>
					<td><?= $row['JOB_NO'] ?></td>
					<td><?= $row['STYLE_REF_NO'] ?></td>
					<td><?= $buyer_arr[$row['BUYER_NAME']] ?></td>
				</tr>
			<?php } ?>
		</table>
<?

		$message = ob_get_contents();
		ob_clean();


		$to = implode(',', $mailToArr);
		$insertUserMail = implode(',', $insertUserMailToArr);

		//echo $insertUserMail;die;

		if ($type == 2) {
			$header = mailHeader();
			$greetingMsg = "Dear Concerned,	<br />Job Is Approved following reference.<br />";
			$subject = "Pre-costing approval WVN";
			$body = $greetingMsg . $message;
			if ($to != "") echo sendMailMailer($to, $subject, $body, $from_mail);
		}

		if ($insertUserMail != '') {
			$body = $greetingMsgForinsertBy . $message;
			echo sendMailMailer($insertUserMail, $subjectForinsertBy, $body, $from_mail);
		}
	}
	exit();
}


?>