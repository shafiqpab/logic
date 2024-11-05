<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../../includes/common.php');
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------
$company_library = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
$buyer_short_name_library = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
$costing_per_id_library = return_library_array("select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
$item_library = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
$color_name_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
$country_name_library = return_library_array("select id, country_name from lib_country", "id", "country_name");
$order_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");
$lib_supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
$lib_mkt_team_member_info_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}

if ($action == "cbo_dealing_merchant") {
	echo create_drop_down("cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name", "id,team_member_name", 1, "-- Select Team Member --", $selected, "");
}

$tmplte = explode("**", $data);
if ($tmplte[0] == "viewtemplate") $template = $tmplte[1];
else $template = $lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template == "") $template = 1;


if ($action == "report_generate") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$company_name = str_replace("'", "", $cbo_company_name);

	$buyer_id_cond = "";
	if (str_replace("'", "", $cbo_buyer_name) == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				$buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else {
				$buyer_id_cond = "";
			}
		} else {
			$buyer_id_cond = "";
		}
	} else {
		$buyer_id_cond = " and a.buyer_name=$cbo_buyer_name"; //.str_replace("'","",$cbo_buyer_name)
	}


	if (str_replace("'", "", $cbo_team_leader) == 0) {
		$team_cond = "";
	} else {
		$team_cond = " and a.team_leader=$cbo_team_leader";
	}

	if (str_replace("'", "", $cbo_dealing_merchant) == 0) {
		$team_member_cond = "";
	} else {
		$team_member_cond = " and a.dealing_marchant=$cbo_dealing_merchant";
	}


	$txt_job_no = str_replace("'", "", $txt_job_no);
	$txt_job_no = trim($txt_job_no);
	if ($txt_job_no != "" || $txt_job_no != 0) {
		$year = substr(str_replace("'", "", $cbo_year_selection), -2);
		$job_no = $company_library[$company_name] . "-" . $year . "-" . str_pad($txt_job_no, 5, 0, STR_PAD_LEFT);
		$jobcond = "and a.job_no='" . $job_no . "'";
	} else {
		$jobcond = "";
	}

	if (str_replace("'", "", $txt_order_no) != "") {
		$ordercond = " and b.po_number like '%" . str_replace("'", "", $txt_order_no) . "%'";
	} else {
		$ordercond = "";
	}
	if (str_replace("'", "", $txt_style_ref) != "") {
		$stylecond = " and a.style_ref_no like '%" . str_replace("'", "", $txt_style_ref) . "%'";
	} else {
		$stylecond = "";
	}
	if (str_replace("'", "", $txt_internal_ref) != "") {
		$interRefcond = " and b.grouping like '%" . str_replace("'", "", $txt_internal_ref) . "%'";
	} else {
		$interRefcond = "";
	}



	$serch_by = str_replace("'", "", $cbo_search_by);
	$date_cond = '';

	if (str_replace("'", "", $txt_date_from) != "" && str_replace("'", "", $txt_date_to) != "") {
		$start_date = (str_replace("'", "", $txt_date_from));
		$end_date = (str_replace("'", "", $txt_date_to));
		if ($serch_by == 1) {
			$date_cond = "and c.country_ship_date between '$start_date' and '$end_date'";
		}
		if ($serch_by == 2) {
			$date_cond = "and b.po_received_date between '$start_date' and '$end_date'";
		}
		if ($serch_by == 3) {
			if ($db_type == 0) {
				$date_cond = "and date(b.insert_date) between '$start_date' and '$end_date'";
			}
			if ($db_type == 2) {
				$date_cond = "and trunc(b.insert_date) BETWEEN TO_DATE('$start_date') AND TO_DATE('$end_date')";
				//$date_cond="and to_char(b.insert_date,'DD-MON-YYYY') between '$start_date' and '$end_date'";
			}
		}
	}
	$insert_date = "";
	if ($db_type == 0) {
		$insert_date = "date(b.insert_date) as insert_date,";
	}
	if ($db_type == 2) {
		$insert_date = "to_char(b.insert_date,'DD-MON-YYYY') as insert_date,";
	}

	$financial_para = array();
	$sql_std_para = sql_select("select interest_expense,income_tax,cost_per_minute from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and	is_deleted=0 order by id");
	foreach ($sql_std_para as $sql_std_row) {
		$financial_para[interest_expense] = $sql_std_row[csf('interest_expense')];
		$financial_para[income_tax] = $sql_std_row[csf('income_tax')];
		$financial_para[cost_per_minute] = $sql_std_row[csf('cost_per_minute')];
	}

	$job_arr = array();
	$po_arr = array();
	$po_qty_arr = array();
	$report_data = array();
	$sql_po = sql_select("select a.id as jobid,a.buyer_name, a.product_code,a.dealing_marchant,a.style_ref_no,a.job_no,a.order_uom,a.total_set_qnty,b.id,b.po_number,b.grouping as ref_no,b.po_received_date,b.unit_price,$insert_date c.item_number_id,c.id as color_size_table_id,c.country_id,c.country_ship_date,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty    from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.company_name=$company_name $buyer_id_cond $team_cond $team_member_cond $jobcond $ordercond $stylecond $interRefcond $date_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by c.country_ship_date,a.job_no_prefix_num,b.id");
	foreach ($sql_po as $sql_po_row) {
		$report_data[country_id][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] = $sql_po_row[csf('buyer_name')];
		$report_data[buyer_name][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] = $sql_po_row[csf('buyer_name')];
		$report_data[month_id][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] = date("M'y", strtotime($sql_po_row[csf('country_ship_date')]));
		$report_data[product_code][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] = $sql_po_row[csf('product_code')];
		$report_data[dealing_marchant][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] = $sql_po_row[csf('dealing_marchant')];
		$report_data[job_no][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] = $sql_po_row[csf('job_no')];
		$report_data[style_ref_no][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] = $sql_po_row[csf('style_ref_no')];
		$report_data[po_number][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] = $sql_po_row[csf('po_number')];
		$report_data[ref_no][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] = $sql_po_row[csf('ref_no')];

		$report_data[po_received_date][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] = $sql_po_row[csf('po_received_date')];
		$report_data[unit_price][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] = $sql_po_row[csf('unit_price')];
		$report_data[insert_date][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] = $sql_po_row[csf('insert_date')];
		$report_data[country_id][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] = $sql_po_row[csf('country_id')];
		$report_data[country_ship_date][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] = $sql_po_row[csf('country_ship_date')];
		$report_data[order_quantity][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] += $sql_po_row[csf('order_quantity')];
		$report_data[plan_cut_qnty][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] += $sql_po_row[csf('plan_cut_qnty')];
		$report_data[order_uom][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] = $sql_po_row[csf('order_uom')];
		$report_data[total_set_qnty][$sql_po_row[csf('id')]][$sql_po_row[csf('country_id')]] = $sql_po_row[csf('total_set_qnty')];
		$job_arr[$sql_po_row[csf('jobid')]] = $sql_po_row[csf('jobid')];
		$po_arr[$sql_po_row[csf('id')]] = $sql_po_row[csf('id')];
		$po_qty_arr[plan_cut_qnty][$sql_po_row[csf('id')]] += $sql_po_row[csf('plan_cut_qnty')];
		$po_qty_arr[order_quantity][$sql_po_row[csf('id')]] += $sql_po_row[csf('order_quantity')];
	}

	$job = array_chunk($job_arr, 1000, true);
	$job_cond_in = "";
	$ji = 0;
	foreach ($job as $key => $value) {
		if ($ji == 0) {
			$job_cond_in = "id in(" . implode(",", $value) . ")";
		} else {
			$job_cond_in .= " or id in(" . implode(",", $value) . ")";
		}
		$ji++;
	}
	$job_cond_in2 = "";
	$ji = 0;
	foreach ($job as $key => $value) {
		if ($ji == 0) {
			$job_cond_in2 = "job_id in(" . implode(",", $value) . ")";
		} else {
			$job_cond_in2 .= " or job_id in(" . implode(",", $value) . ")";
		}
		$ji++;
	}

	$po = array_chunk($po_arr, 1000, true);
	$po_cond_in = "";
	$po_cond_in1 = "";
	$pi = 0;
	foreach ($po as $keyp => $valuep) {
		if ($pi == 0) {
			$po_cond_in = "po_break_down_id in(" . implode(",", $valuep) . ")";
			$po_cond_in1 = "c.po_breakdown_id in(" . implode(",", $valuep) . ")";
		} else {
			$po_cond_in .= " or po_break_down_id in(" . implode(",", $valuep) . ")";
			$po_cond_in1 = "c.po_breakdown_id in(" . implode(",", $valuep) . ")";
		}
		$pi++;
	}




	$lib_yarn_count = return_library_array("select id,yarn_count from  lib_yarn_count ", 'id', 'yarn_count');
	$precost_data_arr = array();
	$sql_precost = sql_select("select a.id,a.job_no,a.fabric_description,a.gsm_weight,a.costing_per,b.count_id   from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_fab_yarn_cost_dtls b where a.job_no=b.job_no and a.id=b.fabric_cost_dtls_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.id in(select min(id) as id from  wo_pre_cost_fabric_cost_dtls where $job_cond_in2 group by job_no) order by a.id ASC");
	foreach ($sql_precost as $row_precost) {
		$precost_data_arr[fabric_description][$row_precost[csf('job_no')]] = $row_precost[csf('fabric_description')];
		$precost_data_arr[gsm_weight][$row_precost[csf('job_no')]] = $row_precost[csf('gsm_weight')];
		$precost_data_arr[costing_per][$row_precost[csf('job_no')]] = $row_precost[csf('costing_per')];
		$precost_data_arr[yarn_count][$row_precost[csf('job_no')]] .= $lib_yarn_count[$row_precost[csf('count_id')]] . ",";;
	}


	$precost_data_arr_avg = array();
	$sql_precost_avg = sql_select("select a.id,a.job_no, a.sew_smv,a.costing_per,c.fab_knit_req_kg,b.fabric_cost,b.trims_cost,b.embel_cost,b.wash_cost,b.comm_cost,b.commission,b.lab_test,b.inspection,b.cm_cost,b.freight,b.currier_pre_cost,b.certificate_pre_cost,b.common_oh,b.total_cost,b.depr_amor_pre_cost,b.price_dzn from wo_pre_cost_mst a, wo_pre_cost_dtls b left join wo_pre_cost_sum_dtls c on b.job_no=c.job_no and c.is_deleted=0 and c.status_active=1 where a.$job_cond_in2 and a.job_no=b.job_no  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");

	foreach ($sql_precost_avg as $row_precost_avg) {
		$precost_data_arr_avg[sew_smv][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('sew_smv')];
		$precost_data_arr_avg[costing_per][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('costing_per')];
		$precost_data_arr_avg[fab_knit_req_kg][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('fab_knit_req_kg')];

		$precost_data_arr_avg[fabric_cost][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('fabric_cost')];
		$precost_data_arr_avg[trims_cost][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('trims_cost')];
		$precost_data_arr_avg[embel_cost][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('embel_cost')];
		$precost_data_arr_avg[wash_cost][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('wash_cost')];

		$precost_data_arr_avg[comm_cost][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('comm_cost')];
		$precost_data_arr_avg[commission][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('commission')];
		$precost_data_arr_avg[lab_test][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('lab_test')];
		$precost_data_arr_avg[inspection][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('inspection')];

		$precost_data_arr_avg[cm_cost][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('cm_cost')];
		$precost_data_arr_avg[freight][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('freight')];
		$precost_data_arr_avg[currier_pre_cost][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('currier_pre_cost')];
		$precost_data_arr_avg[certificate_pre_cost][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('certificate_pre_cost')];

		$precost_data_arr_avg[common_oh][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('common_oh')];
		$precost_data_arr_avg[depr_amor_pre_cost][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('depr_amor_pre_cost')];
		$precost_data_arr_avg[total_cost][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('total_cost')];
		$precost_data_arr_avg[price_dzn][$row_precost_avg[csf('job_no')]] = $row_precost_avg[csf('price_dzn')];

		$net_fob = $row_precost_avg[csf('price_dzn')] - $row_precost_avg[csf('commission')];

		$cost_of_material_services = $row_precost_avg[csf('fabric_cost')] + $row_precost_avg[csf('trims_cost')] + $row_precost_avg[csf('embel_cost')] + $row_precost_avg[csf('lab_test')] + $row_precost_avg[csf('inspection')] + $row_precost_avg[csf('freight')] + $row_precost_avg[csf('currier_pre_cost')] + $row_precost_avg[csf('certificate_pre_cost')] + $row_precost_avg[csf('wash_cost')];

		$contributions_value_additions = $net_fob - $cost_of_material_services;
		$gross_profit = $contributions_value_additions - $row_precost_avg[csf('cm_cost')];

		$precost_data_arr_avg[gross_profit][$row_precost_avg[csf('job_no')]] = $gross_profit;

		$interest_expense = $net_fob * $financial_para[interest_expense] / 100;
		$income_tax = $net_fob * $financial_para[income_tax] / 100;
		$net_profit = $gross_profit - ($row_precost_avg[csf('comm_cost')] + $row_precost_avg[csf('common_oh')] + $row_precost_avg[csf('depr_amor_pre_cost')] + $interest_expense + $income_tax);
		$precost_data_arr_avg[net_profit][$row_precost_avg[csf('job_no')]] = $net_profit;
	}
	$sample_app_arr = array();
	$sql_sample_app = sql_select("select b.id,c.country_id,d.approval_status,max(d.approval_status_date) as approval_status_date     from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_po_sample_approval_info d where a.id=b.job_id and a.job_no=c.job_no_mst and a.job_no=d.job_no_mst and b.id=c.po_break_down_id and b.id=d.po_break_down_id and c.id=d.color_number_id and d.sample_type_id=24 and d.approval_status=3  and a.$job_cond_in and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 	and d.status_active=1 group by b.id, c.country_id,d.approval_status");
	foreach ($sql_sample_app as $row_sample_app) {
		$sample_app_arr[$row_sample_app[csf('id')]][$row_sample_app[csf('country_id')]] = $row_sample_app[csf('approval_status_date')];
	}
	$fabdelcomp_arr = array();
	$sql_fabdelcomp = sql_select("select c.po_breakdown_id,max(a.issue_date) as issue_date from  inv_issue_master a, inv_transaction b, order_wise_pro_details c where  a.id=b.mst_id and b.id=c.trans_id and a.entry_form=18 and a.item_category=2 and a.issue_purpose=9 and transaction_type=2 and $po_cond_in1  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by c.po_breakdown_id");
	foreach ($sql_fabdelcomp as $row_fabdelcomp) {
		$fabdelcomp_arr[$row_fabdelcomp[csf('po_breakdown_id')]] = $row_fabdelcomp[csf('issue_date')];
	}


	$gmtsitem_ratio_array = array();
	$gmtsitem_ratio_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where $job_cond_in2"); // where job_no ='FAL-14-01157'
	foreach ($gmtsitem_ratio_sql as $gmtsitem_ratio_sql_row) {
		$gmtsitem_ratio_array[$gmtsitem_ratio_sql_row[csf('job_no')]][$gmtsitem_ratio_sql_row[csf('gmts_item_id')]] = $gmtsitem_ratio_sql_row[csf('set_item_ratio')];
	}

	$costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst where $job_cond_in2", "job_no", "costing_per"); // where job_no ='FAL-14-01157'
	$cons_arr = array();
	$sql_pre_cost_cons = sql_select("select a.job_no, b.po_break_down_id, a.id as pre_cost_fabric_cost_dtls_id, a.item_number_id, a.fab_nature_id, b.color_size_table_id, b.color_number_id, b.gmts_sizes as size_number_id, b.cons, b.requirment, a.rate FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.$job_cond_in2 and a.status_active=1 and a.is_deleted=0 order by a.id"); 
	foreach ($sql_pre_cost_cons as $cons_row) {
		//$cons_arr[$cons_row[csf('job_no')]][$cons_row[csf('po_break_down_id')]][$cons_row[csf('pre_cost_fabric_cost_dtls_id')]][$cons_row[csf('item_number_id')]][$cons_row[csf('color_number_id')]][$cons_row[csf('size_number_id')]]['cons']=$cons_row[csf('cons')];
		$cons_arr[$cons_row[csf('job_no')]][$cons_row[csf('po_break_down_id')]][$cons_row[csf('pre_cost_fabric_cost_dtls_id')]][$cons_row[csf('item_number_id')]][$cons_row[csf('color_number_id')]][$cons_row[csf('size_number_id')]]['requirment'] = $cons_row[csf('requirment')];
		//$cons_arr[$cons_row[csf('job_no')]][$cons_row[csf('po_break_down_id')]][$cons_row[csf('pre_cost_fabric_cost_dtls_id')]][$cons_row[csf('item_number_id')]][$cons_row[csf('color_number_id')]][$cons_row[csf('size_number_id')]]['rate']=$cons_row[csf('rate')];
	}
	$fab_kgs_arr = array();
	//$application_data=array();
	$sql = "select a.job_no,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty ,d.id as pre_cost_dtls_id,d.fab_nature_id,d.fabric_source   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no  and b.id=c.po_break_down_id $shipment_date  and a.$job_cond_in and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id,pre_cost_dtls_id"; //and a.job_no='FAL-14-01157' and a.company_name=1 
	$data_arr = sql_select($sql);
	foreach ($data_arr as $row) {
		$costing_per_qty = 0;
		$costing_per = $costing_per_arr[$row[csf('job_no')]];
		if ($costing_per == 1) {
			$costing_per_qty = 12;
		}
		if ($costing_per == 2) {
			$costing_per_qty = 1;
		}
		if ($costing_per == 3) {
			$costing_per_qty = 24;
		}
		if ($costing_per == 4) {
			$costing_per_qty = 36;
		}
		if ($costing_per == 5) {
			$costing_per_qty = 48;
		}

		$set_item_ratio = $gmtsitem_ratio_array[$row[csf('job_no')]][$row[csf('item_number_id')]];

		//$cons=$cons_arr[$row[csf('job_no')]][$row[csf('id')]][$row[csf('pre_cost_dtls_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['cons'];
		$requirment = $cons_arr[$row[csf('job_no')]][$row[csf('id')]][$row[csf('pre_cost_dtls_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['requirment'];
		/*$rate=0;
	$rate=$cons_arr[$row[csf('job_no')]][$row[csf('id')]][$row[csf('pre_cost_dtls_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['rate'];
	if($rate=="")
	{
	  $rate=0;	
	}*/
		$req_fin_fab_qnty = def_number_format(($row[csf("plan_cut_qnty")] / ($costing_per_qty * $set_item_ratio)) * $cons, 5, "");
		$req_grey_fab_qnty = def_number_format(($row[csf("plan_cut_qnty")] / ($costing_per_qty * $set_item_ratio)) * $requirment, 5, "");
		$amount = def_number_format(($req_grey_fab_qnty * $rate), 5, "");
		if ($row[csf('fab_nature_id')] == 2) {
			$fab_kgs_arr[$row[csf('id')]][$row[csf('country_id')]] += $req_grey_fab_qnty;
		}

		if ($row[csf('fab_nature_id')] == 3) {
		}
	}

	$data_arr = sql_select("select po_break_down_id, country_id, sum(ex_factory_qnty) as ex_factory_qnty, max(ex_factory_date) as ex_factory_date from pro_ex_factory_mst where $po_cond_in and status_active=1 and is_deleted=0 group by po_break_down_id, country_id");
	foreach ($data_arr as $row) {
		$exFactory_arr[ex_factory_qnty][$row[csf('po_break_down_id')]][$row[csf('country_id')]] = $row[csf('ex_factory_qnty')];
		$exFactory_arr[ex_factory_date][$row[csf('po_break_down_id')]][$row[csf('country_id')]] = $row[csf('ex_factory_date')];
	}
	//echo "select sum(a.discount_ammount) as discount_ammount ,sum(a.freight_amnt_by_supllier) as freight_amnt_by_supllier, c.po_breakdown_id from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls c where a.id=c.mst_id and $po_cond_in1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id";
	$discount_air_freight_arr = array();
	$sql_discount_air_freight = sql_select("select sum(a.discount_ammount) as discount_ammount ,sum(a.freight_amnt_by_supllier) as freight_amnt_by_supllier, c.po_breakdown_id from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls c where a.id=c.mst_id and $po_cond_in1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id");
	foreach ($sql_discount_air_freight as $row_discount_air_freight) {
		$discount_air_freight_arr[discount_ammount][$row_discount_air_freight[csf('po_breakdown_id')]] = $row_discount_air_freight[csf('discount_ammount')];
		$discount_air_freight_arr[freight_amnt_by_supllier][$row_discount_air_freight[csf('po_breakdown_id')]] = $row_discount_air_freight[csf('freight_amnt_by_supllier')];
	}

	$data_arr_cut = sql_select("select po_break_down_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where $po_cond_in and  production_type ='1' and status_active=1 and is_deleted=0 group by po_break_down_id, country_id");
	foreach ($data_arr_cut as $row_cut) {
		$cut_qty_arr[$row_cut[csf('po_break_down_id')]][$row_cut[csf('country_id')]] = $row_cut[csf('production_quantity')];
	}

	// "select c.po_breakdown_id,max(a.issue_date) as issue_date from  inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=18 and a.item_category=2 and a.issue_purpose=9 and transaction_type=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by c.po_breakdown_id";



	if ($template == 1) {
		ob_start();
?>
		<div style="width:3030px">
			<fieldset style="width:100%;">
				<table width="3030">
					<tr class="form_caption">
						<td colspan="25" align="center">Merchandising KPI Report</td>
					</tr>
					<tr class="form_caption">
						<td colspan="25" align="center"><? echo $company_library[$company_name]; ?></td>
					</tr>
				</table>
				<table class="rpt_table" width="3030" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="50">Month</th>
						<th width="50">Buyer</th>
						<th width="50">Dept Code</th>


						<th width="100">Team Member</th>
						<th width="100">Job No</th>
						<th width="100">Style Ref</th>

						<th width="90">Order No</th>
						<th width="70">Internal Ref.</th>
						<th width="80">Order Rcvd Date</th>
						<th width="80">Order Insert Date</th>
						<th width="80">Country</th>
						<th width="80">Country TOD</th>

						<th width="150">Construction N Composition</th>

						<th width="50">GSM</th>
						<th width="80">Yarn Count</th>
						<th width="80">Order Qnty</th>
						<th width="50">UOM</th>
						<th width="80">Order Qnty (Pcs)</th>
						<th width="80">Plan Cut Qnty (Pcs)</th>
						<th width="50">Avg. Fab Con/ Dzn</th>

						<th width="80">Fabric (Kg)</th>
						<th width="80">Sample Apvl</th>
						<th width="80">Fab del Comp Date</th>
						<th width="50">FOB</th>
						<th width="50">SMV</th>
						<th width="50">CM /Dzn</th>
						<th width="80">Ship Date</th>

						<th width="100">Shipped Qty (Pcs)</th>
						<th width="50">Short shipment</th>
						<th width="50">Excess shipment </th>
						<th width="50">Short ship %</th>
						<th width="50">Excess ship % </th>
						<th width="80">Gross Profit(Dzn)</th>
						<th width="80"> Net Profit (Dzn)</th>
						<th width="80">Actual L/T (Days)</th>
						<th width="70">Discount</th>

						<th width="90">Air Freight</th>
						<th width="90">CTS Ratio</th>
						<th width="">T. Wastage %</th>

					</thead>
				</table>
				<div style="width:3050px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="3030" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<?
						$tot_order_quantity_pcs_or_set = 0;
						$tot_order_qty = 0;
						$tot_plan_cut_qnty = 0;
						$tot_fab_kgs = 0;
						$tot_exfactory_qty = 0;
						$tot_shortshipment = 0;
						$tot_overshipment = 0;
						$tot_discount = 0;
						$tot_air_frieght = 0;
						$i = 1;
						foreach ($report_data[country_id] as $po_id => $country_id) {
							//print_r($po_id)
							foreach ($country_id as $key => $value) {
								if ($i % 2 == 0) $bgcolor = "#E9F3FF";
								else $bgcolor = "#FFFFFF";
						?>
								<tr align="center" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30"><? echo $i; ?></td>
									<td width="50"><? echo $report_data[month_id][$po_id][$key];  ?></td>
									<td width="50" style=" word-break: break-all"><? echo $buyer_short_name_library[$report_data[buyer_name][$po_id][$key]];  ?></td>
									<td width="50" style=" word-break: break-all"><? echo $report_data[product_code][$po_id][$key];  ?></td>

									<td width="100" style=" word-break: break-all"><? echo $lib_mkt_team_member_info_arr[$report_data[dealing_marchant][$po_id][$key]];  ?></td>
									<td width="100" style=" word-break: break-all"><? echo $report_data[job_no][$po_id][$key];  ?></td>
									<td width="100" style=" word-break: break-all"><? echo $report_data[style_ref_no][$po_id][$key];  ?></td>


									<td width="90" style=" word-break: break-all"><? echo $report_data[po_number][$po_id][$key];  ?></td>
									<td width="70" style=" word-break: break-all"><? echo $report_data[ref_no][$po_id][$key];  ?></td>

									<td width="80" style=" word-break: break-all"><? echo change_date_format($report_data[po_received_date][$po_id][$key], "dd-mm-yyyy", "-");  ?></td>
									<td width="80" style=" word-break: break-all"><? echo change_date_format($report_data[insert_date][$po_id][$key], "dd-mm-yyyy", "-");  ?></td>
									<td width="80" style=" word-break: break-all"><? echo $country_name_library[$report_data[country_id][$po_id][$key]];  ?></td>
									<td width="80" style=" word-break: break-all"><? echo change_date_format($report_data[country_ship_date][$po_id][$key], "dd-mm-yyyy", "-");  ?></td>


									<td width="150" style=" word-break: break-all"><? echo $precost_data_arr[fabric_description][$report_data[job_no][$po_id][$key]]; ?></td>

									<td width="50" style=" word-break: break-all"><? echo $precost_data_arr[gsm_weight][$report_data[job_no][$po_id][$key]]; ?></td>
									<td width="80" style=" word-break: break-all"><? echo trim($precost_data_arr[yarn_count][$report_data[job_no][$po_id][$key]], ","); ?></td>
									<td width="80" align="right">
										<?
										$order_quantity_pcs_or_set = $report_data[order_quantity][$po_id][$key] / $report_data[total_set_qnty][$po_id][$key];
										$tot_order_quantity_pcs_or_set += $order_quantity_pcs_or_set;
										echo number_format($report_data[order_quantity][$po_id][$key] / $report_data[total_set_qnty][$po_id][$key], 0);
										?></td>
									<td width="50" style=" word-break: break-all"><? echo $unit_of_measurement[$report_data[order_uom][$po_id][$key]];  ?></td>
									<td width="80" align="right" style=" word-break: break-all">
										<?
										$order_qty = $report_data[order_quantity][$po_id][$key];
										$plan_cut_qnty = $report_data[plan_cut_qnty][$po_id][$key];
										$tot_order_qty += $order_qty;
										$tot_plan_cut_qnty += $plan_cut_qnty;
										echo $report_data[order_quantity][$po_id][$key];
										?>
									</td>
									<td width="80" align="right" style=" word-break: break-all"><? echo $plan_cut_qnty; ?></td>
									<td width="50" align="right" style=" word-break: break-all" title="<? echo $precost_data_arr[costing_per][$report_data[job_no][$po_id][$key]]; ?>">
										<?
										$avgcons = 0;
										$costingper = $precost_data_arr_avg[costing_per][$report_data[job_no][$po_id][$key]];
										if ($costingper == 1) {
											$avgcons = $precost_data_arr_avg[fab_knit_req_kg][$report_data[job_no][$po_id][$key]];
										}
										if ($costingper == 2) {
											$avgcons = $precost_data_arr_avg[fab_knit_req_kg][$report_data[job_no][$po_id][$key]] * 12;
										}
										if ($costingper == 3) {
											$avgcons = $precost_data_arr_avg[fab_knit_req_kg][$report_data[job_no][$po_id][$key]] / 2;
										}
										if ($costingper == 4) {
											$avgcons = $precost_data_arr_avg[fab_knit_req_kg][$report_data[job_no][$po_id][$key]] / 3;
										}
										if ($costingper == 5) {
											$avgcons = $precost_data_arr_avg[fab_knit_req_kg][$report_data[job_no][$po_id][$key]] / 4;
										}
										//echo $avgcons;
										echo number_format($fab_kgs_arr[$po_id][$key] / $plan_cut_qnty * 12, 4);
										?>
									</td>

									<td width="80" align="right" style=" word-break: break-all">
										<?
										//echo number_format(($plan_cut_qnty/12)*$avgcons,4); 
										//$fab_kgs=$fab_kgs_arr[$po_id][$key];
										$tot_fab_kgs += $fab_kgs_arr[$po_id][$key];
										echo number_format($fab_kgs_arr[$po_id][$key], 4);
										?>
									</td>
									<td width="80" style=" word-break: break-all"><? echo change_date_format($sample_app_arr[$po_id][$key], "dd-mm-yyyy", "-"); ?></td>
									<td width="80" style=" word-break: break-all"><? echo change_date_format($fabdelcomp_arr[$po_id], "dd-mm-yyyy", "-"); ?></td>
									<td width="50" align="right" style=" word-break: break-all"><? echo $report_data[unit_price][$po_id][$key];  ?></td>
									<td width="50" align="right" style=" word-break: break-all"><? echo $precost_data_arr_avg[sew_smv][$report_data[job_no][$po_id][$key]] ?></td>
									<td width="50" align="right" style=" word-break: break-all">
										<?
										$cm_cost = 0;
										$costingper = $precost_data_arr_avg[costing_per][$report_data[job_no][$po_id][$key]];
										if ($costingper == 1) {
											$cm_cost = $precost_data_arr_avg[cm_cost][$report_data[job_no][$po_id][$key]];
										}
										if ($costingper == 2) {
											$cm_cost = $precost_data_arr_avg[cm_cost][$report_data[job_no][$po_id][$key]] * 12;
										}
										if ($costingper == 3) {
											$cm_cost = $precost_data_arr_avg[cm_cost][$report_data[job_no][$po_id][$key]] / 2;
										}
										if ($costingper == 4) {
											$cm_cost = $precost_data_arr_avg[cm_cost][$report_data[job_no][$po_id][$key]] / 3;
										}
										if ($costingper == 5) {
											$cm_cost = $precost_data_arr_avg[cm_cost][$report_data[job_no][$po_id][$key]] / 4;
										}
										echo $cm_cost;
										//echo $precost_data_arr_avg[cm_cost][$report_data[job_no][$po_id][$key]] 
										?>
									</td>
									<td width="80" style=" word-break: break-all"><? echo change_date_format($exFactory_arr[ex_factory_date][$po_id][$key], "dd-mm-yyyy", "-");  ?></td>

									<td width="100" align="right">
										<?
										$exfactory_qty = $exFactory_arr[ex_factory_qnty][$po_id][$key];
										$tot_exfactory_qty += $exfactory_qty;
										echo $exFactory_arr[ex_factory_qnty][$po_id][$key];
										?>
									</td>
									<td width="50" align="right">
										<?
										$overshipment = 0;
										$shortshipment = 0;
										$short_or_over_ship = $order_qty - $exfactory_qty;
										if ($short_or_over_ship < 0) {
											$overshipment = $exfactory_qty - $order_qty;
										}
										if ($short_or_over_ship >= 0) {
											$shortshipment = $order_qty - $exfactory_qty;
										}
										echo $shortshipment;
										$tot_shortshipment += $shortshipment;
										$tot_overshipment += $overshipment;
										?>
									</td>
									<td width="50" align="right"><? echo $overshipment;  ?> </td>
									<td width="50" align="right"><? echo number_format((($shortshipment) / $order_qty) * 100, 2);  ?> </td>
									<td width="50" align="right"><? echo number_format((($overshipment) / $order_qty) * 100, 2);  ?> </td>
									<td width="80" align="right"><? echo number_format($precost_data_arr_avg[gross_profit][$report_data[job_no][$po_id][$key]], 4); ?></td>
									<td width="80" align="right"> <? echo number_format($precost_data_arr_avg[net_profit][$report_data[job_no][$po_id][$key]], 4); ?></td>
									<td width="80"><? echo datediff("d", $report_data[po_received_date][$po_id][$key], $report_data[country_ship_date][$po_id][$key]); ?></td>
									<td width="70" align="right">
										<?
										$discount = number_format(($discount_air_freight_arr[discount_ammount][$po_id] / $po_qty_arr[order_quantity][$po_id]) * $order_qty, 4);
										echo $discount;
										$tot_discount += $discount;
										?>
									</td>

									<td width="90" align="right">
										<?
										$air_frieght = number_format(($discount_air_freight_arr[freight_amnt_by_supllier][$po_id] / $po_qty_arr[order_quantity][$po_id]) * $order_qty, 4);
										echo $air_frieght;
										$tot_air_frieght += $air_frieght;
										?>
									</td>
									<td width="90" align="right" title="<? echo $cut_qty_arr[$po_id][$key]; ?>">
										<?
										$cts_ratio = $exfactory_qty / $cut_qty_arr[$po_id][$key] * 100;

										echo number_format($cts_ratio, 2);
										?>
										<!--CTS Ratio-->
									</td>
									<td width=""><!--T. Wastage %--></td>

								</tr>
						<?
								$i++;
							}
						}
						?>
					</table>

				</div>
				<table class="rpt_table" width="3030" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<th width="30"><!--SL--></th>
						<th width="50"><!--Month--></th>
						<th width="50"><!--Buyer--></th>
						<th width="50"><!--Dept--></th>

						<th width="100"><!--Team Member--></th>
						<th width="100"><!--Job No--></th>
						<th width="100"><!--Style Ref--></th>


						<th width="90"><!--Order No--></th>
						<th width="70"><!--Order No--></th>
						<th width="80"><!--Order Rcvd Date--></th>
						<th width="80"><!--Order Insert Date--></th>
						<th width="80"><!--Country--></th>
						<th width="80"><!--Country TOD--></th>

						<th width="150"><!--Constration N Composition--></th>

						<th width="50"><!--GSM--></th>
						<th width="80"><!--Yarn Count--></th>
						<th width="80"><? echo number_format($tot_order_quantity_pcs_or_set, 0) ?><!--Order Qnty--></th>
						<th width="50"><!--UOM--></th>
						<th width="80"><? echo number_format($tot_order_qty, 0) ?></th>
						<th width="80"><? echo number_format($tot_plan_cut_qnty, 0) ?></th>
						<th width="50"><? echo number_format($tot_fab_kgs / ($tot_plan_cut_qnty / 12), 4) ?></th>

						<th width="80"><? echo number_format($tot_fab_kgs, 4) ?></th>
						<th width="80"><!--Sample Apvl--></th>
						<th width="80"><!--Fab del Comp--></th>
						<th width="50"><!--FOB--></th>
						<th width="50"><!--SMV--></th>
						<th width="50"><!--CM /Dzn--></th>
						<th width="80"><!--Ex-fty--></th>
						<th width="100"><? echo number_format($tot_exfactory_qty) ?></th>
						<th width="50"><? echo number_format($tot_shortshipment) ?><!--short shipment--></th>
						<th width="50"><? echo number_format($tot_overshipment) ?><!--Excess shipment--> </th>
						<th width="50"><? echo number_format((($tot_shortshipment) / $tot_order_qty) * 100, 4);  ?><!--short ship %--></th>
						<th width="50"><? echo number_format((($tot_overshipment) / $tot_order_qty) * 100, 4);  ?></th>
						<th width="80"><!--Gross Profit--></th>
						<th width="80"><!--Net Profit--></th>
						<th width="80"><!--Actual L/T--></th>
						<th width="70"><? echo number_format($tot_discount, 2) ?></th>
						<th width="90"><? echo number_format($tot_air_frieght, 2) ?></th>
						<th width="90"><!--CTS Ratio--></th>
						<th width=""><!--T. Wastage %--></th>

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
	echo "$total_data****$filename****$tot_rows";
	exit();
}









if ($action == "booking_info") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
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


					$conversion_factor_array = array();
					$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
					$conversion_factor = sql_select("select id ,conversion_factor from  lib_item_group ");
					foreach ($conversion_factor as $row_f) {
						$conversion_factor_array[$row_f[csf('id')]]['con_factor'] = $row_f[csf('conversion_factor')];
					}

					$i = 1;
					$country_arr_data = array();
					$sql_data = sql_select("select c.country_id,c.po_break_down_id,c.job_no_mst from wo_po_color_size_breakdown c  where c.po_break_down_id in($po_id) and c.status_active=1 and c.is_deleted=0 group by c.country_id,c.po_break_down_id,c.job_no_mst  ");
					foreach ($sql_data as $row_c) {
						$country_arr_data[$row_c[csf('po_break_down_id')]][$row_c[csf('job_no_mst')]]['country'] = $row_c[csf('country_id')];
					}



					$item_description_arr = array();
					$wo_sql_trim = sql_select("select b.id,b.item_color,b.job_no, b.po_break_down_id, b.description,b.brand_supplier,b.item_size from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id and a.pre_cost_fabric_cost_dtls_id=$trim_dtla_id and a.is_deleted=0 and a.status_active=1 and a.job_no=b.job_no  group by b.id,b.po_break_down_id,b.job_no,b.description,b.brand_supplier,b.item_size,b.item_color");
					foreach ($wo_sql_trim as $row_trim) {
						$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]][$trim_dtla_id]['description'] = $row_trim[csf('description')];
					}

					$boking_cond = "";
					$booking_no = explode(',', $book_num);
					foreach ($booking_no as $book_row) {
						if ($boking_cond == "") $boking_cond = "and a.booking_no in('$book_row'";
						else  $boking_cond .= ",'$book_row'";
					}
					if ($boking_cond != "") $boking_cond .= ")";
					$wo_sql = "select a.booking_no, a.booking_date, a.supplier_id,b.job_no,b.country_id_string, b.po_break_down_id,sum(b.wo_qnty) as wo_qnty,b.uom from wo_booking_mst a, wo_booking_dtls b 
					where  a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 
					and b.status_active=1 and b.is_deleted=0 and  b.job_no='$job_no' and b.trim_group=$item_name and b.po_break_down_id in($po_id) and b.pre_cost_fabric_cost_dtls_id=$trim_dtla_id $boking_cond group by  b.po_break_down_id,b.job_no,
					a.booking_no, a.booking_date, a.supplier_id,b.uom,b.country_id_string";
					$dtlsArray = sql_select($wo_sql);

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
					?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30">
								<p><? echo $i; ?></p>
							</td>
							<td width="100">
								<p><? echo $row[csf('booking_no')]; ?></p>
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
							<td width="80" align="right" title="<? echo 'conversion_factor=' . $conversion_factor_rate; ?>">
								<p><? echo number_format($row[csf('wo_qnty')] * $conversion_factor_rate, 2); ?></p>
							</td>
							<td width="60" align="center">
								<p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p>
							</td>
							<td width="100">
								<p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p>
							</td>
						</tr>
					<?
						$tot_qty += $row[csf('wo_qnty')] * $conversion_factor_rate;
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty, 2); ?></td>
						<td align="right">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
<?
	exit();
}
disconnect($con);
?>
<?
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
					<th width="100">Recv. Qty.</th>
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

					$receive_qty_data = ("select a.id, c.po_breakdown_id,b.item_group_id,b.prod_id as prod_id,a.challan_no,b.item_description, a.recv_number, a.receive_date, SUM(c.quantity) as quantity
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c 
					where a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=1 and  c.po_breakdown_id in($po_id)  and b.item_group_id='$item_name' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  c.po_breakdown_id,b.item_group_id,b.prod_id,a.id,b.item_description, a.recv_number,a.challan_no, a.receive_date");

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
							<td width="100" align="right">
								<p><? echo number_format($row[csf('quantity')], 2); ?></p>
							</td>
						</tr>
					<?
						$tot_qty += $row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right"></td>
						<td align="right">Total</td>
						<td><? echo number_format($tot_qty, 2); ?></td>
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
					<th width="100">Return Qty.</th>
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
								<td width="30">
									<p><? echo $i; ?></p>
								</td>
								<td width="80">
									<p><? echo $row[csf('prod_id')]; ?></p>
								</td>
								<td width="100" align="center">
									<p><? echo $receive_rtn_data[$row[csf('id')]][issue_number]; ?></p>
								</td>
								<td width="100" align="center">
									<p><? echo $row[csf('challan_no')]; ?></p>
								</td>
								<td width="100" align="center">
									<p><? echo  change_date_format($receive_rtn_data[$row[csf('id')]][issue_date]); ?></p>
								</td>
								<td width="80" align="center">
									<p><? echo $row[csf('item_description')]; ?></p>
								</td>
								<td width="100" align="right">
									<p><? echo number_format($receive_rtn_data[$row[csf('id')]][quantity], 2); ?></p>
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
						<td colspan="5" align="right"></td>
						<td align="right">Total</td>
						<td><? echo number_format($tot_rtn_qty, 2); ?></td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="5" align="right"></td>
						<td align="right">Balance</td>
						<td><? echo number_format($tot_qty - $tot_rtn_qty, 2); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
<?
	exit();
}
disconnect($con);
?>

<?
if ($action == "booking_issue_info") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<!--	<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
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
					$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
					$i = 1;
					//$wo_sql="select a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description,sum(a.cons_qnty) as cons_qnty  from inv_receive_master b, inv_trims_entry_dtls a where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 group by a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description";

					$mrr_sql = ("select a.id, a.issue_number,a.challan_no,b.prod_id, a.issue_date,b.item_description,SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 
					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and p.item_group_id='$item_name' group by c.po_breakdown_id,p.item_group_id,b.item_description,a.issue_number,a.id,a.issue_date,b.prod_id,a.challan_no ");

					$dtlsArray = sql_select($mrr_sql);

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
								<p><? echo number_format($row[csf('quantity')], 2); ?></p>
							</td>
						</tr>
					<?
						$tot_qty += $row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right"></td>
						<td align="right">Total</td>
						<td><? echo number_format($tot_qty, 2); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
<?
	exit();
}
disconnect($con);
?>
<?
if ($action == "order_qty_data") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
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
								<p><? echo number_format($row[csf('po_quantity')], 2); ?></p>
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
						<td><? echo number_format($tot_qty, 2); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
<?
	exit();
}
disconnect($con);
?>
<?
if ($action == "order_req_qty_data") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

?>
	<!--	<div style="width:680px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
-->
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
					<th width="">Req. Rate</th>

				</thead>
				<tbody>
					<?

					// $gmt_item_id=return_field_value("item_number_id", "wo_po_color_size_breakdown", "po_break_down_id='$po_id'");
					//$country_id=return_field_value("country_id", "wo_po_color_size_breakdown", "po_break_down_id='$po_id'");
					//$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,c.country_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='".$po_id."' and c.item_number_id=' $gmt_item_id' and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,c.country_id ");
					//list($sql_po_qty_row)=$sql_po_qty;
					//$po_qty=$sql_po_qty_row[csf('order_quantity')];



					$req_arr = array();
					$red_data = sql_select("select a.id,a.job_no,a.cons, a.po_break_down_id  from wo_pre_cost_trim_co_cons_dtls a , wo_pre_cost_trim_cost_dtls b where b.id=a.wo_pre_cost_trim_cost_dtls_id and b.trim_group=$item_group and a.job_no='$job_no' and a.po_break_down_id in($po_id) and b.id=$trim_dtla_id");
					foreach ($red_data as $row_data) {
						$req_arr[$row_data[csf('po_break_down_id')]][$row_data[csf('job_no')]]['cons'] = $row_data[csf('cons')];
					}
					//print_r($req_arr);

					$wo_sql_trim = sql_select("select b.id,b.job_no, b.po_break_down_id, b.description from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id and a.is_deleted=0 and a.status_active=1 and a.job_no=b.job_no  group by b.id,b.po_break_down_id,b.job_no,b.description ");
					foreach ($wo_sql_trim as $row_trim) {
						$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['job_no'] = $row_trim[csf('job_no')];
						$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['description'] = $row_trim[csf('description')];
					}

					/*$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");*/

					$costing_per_id = return_field_value("costing_per", "wo_pre_cost_mst", "job_no ='$job_no'");
					$date_cond = "and c.country_ship_date between '$start_date' and '$end_date'";

					$dzn_qnty = 0;
					if ($costing_per_id == 1) {
						$dzn_qnty = 12;
					} else if ($costing_per_id == 3) {
						$dzn_qnty = 12 * 2;
					} else if ($costing_per_id == 4) {
						$dzn_qnty = 12 * 3;
					} else if ($costing_per_id == 5) {
						$dzn_qnty = 12 * 4;
					} else {
						$dzn_qnty = 1;
					}


					$i = 1;

					if ($country_id_string == 0) {
						$contry_cond = "";
					} else {
						$contry_cond = "and c.country_id in(" . $country_id_string . ")";
					}

					// $sql=" select  sum(c.order_quantity) as po_quantity ,c.country_id as country_id from wo_po_color_size_breakdown c  where   c.job_no_mst='$job_no' and c.po_break_down_id=$po_id $contry_cond  and c.status_active=1 and c.is_deleted=0 group by c.country_id ";
					$sql = "select  b.id,b.job_no_mst,c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  c.job_no_mst='$job_no' and c.po_break_down_id in($po_id) $contry_cond  $date_cond  group by   b.id,b.job_no_mst,c.country_id order by b.id,b.job_no_mst,c.country_id";

					$dtlsArray = sql_select($sql);
					foreach ($dtlsArray as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$cons = $req_arr[$row[csf('id')]][$job_no]['cons'];
						$req_qty = ($row[csf('order_quantity_set')] / $dzn_qnty) * $cons;
						//$descript=$item_description_arr[$po_id][$job_no]['description'];
					?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30">
								<p><? echo $i; ?></p>
							</td>
							<td width="80" align="center">
								<p><? echo $buyer_short_name_library[$buyer]; ?></p>
							</td>
							<td width="100">
								<p><? echo $order_arr[$row[csf('id')]]; ?></p>
							</td>
							<td width="100">
								<p><? echo $description; ?></p>
							</td>
							<td width="100" align="center">
								<p><? echo  $country_name_library[$row[csf('country_id')]]; ?></p>
							</td>
							<td width="80" align="right">
								<p><? echo number_format($req_qty, 2); ?></p>
							</td>
							<td width="" align="right">
								<p><? echo number_format($rate, 4); ?></p>
							</td>

						</tr>
					<?
						$tot_qty += $req_qty;
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td align="right"></td>
						<td colspan="4" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty, 2); ?> </td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
<?
	exit();
}
disconnect($con);
?>