<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../../includes/common.php');
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------
$company_library = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
$buyer_short_name_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
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
			$date_cond = "and b.shipment_date between '$start_date' and '$end_date'";
		}
		if ($serch_by == 2) {
			$date_cond = "and b.po_received_date between '$start_date' and '$end_date'";
		}
		if ($serch_by == 3) {
			$date_cond = "and trunc(b.insert_date) BETWEEN TO_DATE('$start_date') AND TO_DATE('$end_date')";
		}
	}
	$insert_date = "";
	if ($db_type == 0) {
		$insert_date = "date(b.insert_date) as insert_date,";
	}
	if ($db_type == 2) {
		$insert_date = "to_char(b.insert_date,'DD-MON-YYYY') as insert_date,";
	}


	$job_arr = array();
	$po_arr = array();
	$po_qty_arr = array();
	$main_data = array();
	$sql_main= sql_select("SELECT a.id AS jobid, a.buyer_name as buyer, a.style_ref_no, a.job_no, a.order_uom,b.id as did FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no = b.job_no_mst and a.company_name=$company_name $buyer_id_cond $jobcond $stylecond $date_cond and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 order by a.job_no");
	foreach ($sql_main as $sql_main_row) {
		$main_data[$sql_main_row[csf('job_no')]]['buyer_name']= $sql_main_row[csf('buyer')];
		$main_data[$sql_main_row[csf('job_no')]]['job_no']= $sql_main_row[csf('job_no')];
		$main_data[$sql_main_row[csf('job_no')]]['style_ref_no']= $sql_main_row[csf('style_ref_no')];
		$main_data[$sql_main_row[csf('job_no')]]['did']= $sql_main_row[csf('did')];
	}
		//echo '<pre>';print_r($main_data);die;
	foreach ($sql_main as $row) {
		$poIds .= $row[csf('did')] . ",";
	}
	//echo '<pre>';print_r($main_data);die;
	$noOfPos = count(explode(",", chop($poIds, ',')));
	//echo $noOfPos;die;
	$poIds = chop($poIds, ',');
	$poIds_cond ="";
	$poIds_cond_inv2 = "";
	$poIds_cond_prod = "";
	if ($db_type == 2 && $noOfPos > 1000) {
		$poIds_cond_pre = " and (";
		$poIds_cond_suff .= ")";
		$poIdsArr = array_chunk(explode(",", $poIds), 999);
		foreach ($poIdsArr as $ids) {
			$ids = implode(",", $ids);
			$poIds_cond .= " b.po_break_down_id in($ids) or ";
			$poIds_cond_inv2 .= " c.po_breakdown_id in($ids) or ";
			$poIds_cond_prod .= " po_break_down_id in($ids) or ";
		}
		$poIds_cond_inv2 = $poIds_cond_pre . chop($poIds_cond_inv2, 'or ') . $poIds_cond_suff;
		$poIds_cond = $poIds_cond_pre . chop($poIds_cond, 'or ') . $poIds_cond_suff;
		$poIds_cond_prod = $poIds_cond_pre . chop($poIds_cond_prod, 'or ') . $poIds_cond_suff;
	} else {
		$poIds_cond_inv2 = " and c.po_breakdown_id in($poIds)";
		$poIds_cond = " and b.po_break_down_id in($poIds)";
		$poIds_cond_prod = " and po_break_down_id in($poIds)";
	}
	$reqSQL = "select b.job_no,b.po_break_down_id, c.fab_nature_id, (b.requirment/b.pcs)*a.plan_cut_qnty as requirment
						  from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c 
						  where a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and b.pre_cost_fabric_cost_dtls_id=c.id and a.job_no_mst=c.job_no and a.item_number_id=c.item_number_id and a.is_deleted=0 and a.status_active=1 and b.pcs>0 $poIds_cond";
                        $reqSQLresult = sql_select($reqSQL);
                        $reqArr = array();
                        $woven_reqArr = array();
                        foreach ($reqSQLresult as $val) {
                            $reqArr[$val[csf('job_no')]][$val[csf('po_break_down_id')]] += $val[csf('requirment')];
                        }
						 //echo '<pre>';print_r($reqArr);die;
						$fabricSQL = "select a.item_category,b.job_no, b.po_break_down_id, SUM(b.grey_fab_qnty) as grey_fab_qnty, SUM(b.fin_fab_qnty) as fin_fab_qnty
						from wo_booking_mst a, wo_booking_dtls b ,wo_pre_cost_fabric_cost_dtls c
						where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and a.booking_type!=3 and a.item_category in (2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $poIds_cond group by a.item_category, b.po_break_down_id,b.job_no";
				   $fabricSQLresult = sql_select($fabricSQL);
				   $fabricArr = array();
				   foreach ($fabricSQLresult as $key => $val) {
						$fabricArr[$val[csf('job_no')]][$val[csf('po_break_down_id')]] += $val[csf('grey_fab_qnty')];	  
				   }
				   $fabric_booking_perc = $fabricArr[$val[csf('job_no')]][$val[csf('po_break_down_id')]] * 100 /   $reqArr[$val[csf('job_no')]][$val[csf('po_break_down_id')]];
				//echo '<pre>';print_r($fabricArr);die;
				   $trimsSQL = "select po_break_down_id,SUM(CASE WHEN approval_status=3 THEN 1 ELSE 0 END) as apprv_status,
							SUM(CASE WHEN approval_status<>4 THEN 1 ELSE 0 END) as total_po
							from wo_po_trims_approval_info where current_status=1 and is_deleted=0 and status_active=1 $poIds_cond_prod group by po_break_down_id";
                        $trimsSQLresult = sql_select($trimsSQL);
                        $trimsAPParr = array();
                        foreach ($trimsSQLresult as $key => $val) {
                            $trimsAPParr[$val[csf('po_break_down_id')]]['apprv_status'] = $val[csf('apprv_status')];
                            $trimsAPParr[$val[csf('po_break_down_id')]]['total_po'] = $val[csf('total_po')];
                        }
						$trims_perc = $trimsAPParr[$val[csf('po_break_down_id')]]['apprv_status'] * 100 /  $trimsAPParr[$val[csf('po_break_down_id')]]['total_po'];
	$sql_buttonfab= sql_select("SELECT a.job_no,b.id,a.quotation_id,e.item_category, e.fabric_source,e.is_approved,e.entry_form as entry_fab_id,e.booking_type FROM wo_po_details_master a, wo_po_break_down b, wo_booking_dtls d,wo_booking_mst e WHERE a.job_no = b.job_no_mst and a.job_no = d.job_no and d.job_no = b.job_no_mst and d.booking_no=e.booking_no and e.is_deleted = 0 and e.status_active = 1  and e.entry_form=108 and a.company_name=$company_name $buyer_id_cond $jobcond $ordercond $stylecond $date_cond and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and d.is_deleted = 0 and d.status_active = 1");
	foreach ($sql_buttonfab as $sql_buttonfab_row) {
		$button_datafab[$sql_buttonfab_row[csf('job_no')]]['entry_fab_id']= $sql_buttonfab_row[csf('entry_fab_id')];
		$button_datafab[$sql_buttonfab_row[csf('job_no')]]['item_category']= $sql_buttonfab_row[csf('item_category')];
		$button_datafab[$sql_buttonfab_row[csf('job_no')]]['fabric_source']= $sql_buttonfab_row[csf('fabric_source')];
		$button_datafab[$sql_buttonfab_row[csf('job_no')]]['is_approved']= $sql_buttonfab_row[csf('is_approved')];
		$button_datafab[$sql_buttonfab_row[csf('job_no')]]['booking_type']= $sql_buttonfab_row[csf('booking_type')];
	}
	$sql_buttonsamfab= sql_select("SELECT a.job_no,b.id,a.quotation_id,e.item_category, e.fabric_source,e.is_approved,e.entry_form as entry_fab_id,e.booking_type FROM wo_po_details_master a, wo_po_break_down b, wo_booking_dtls d,wo_booking_mst e WHERE a.job_no = b.job_no_mst and a.job_no = d.job_no and d.job_no = b.job_no_mst and d.booking_no=e.booking_no and e.is_deleted = 0 and e.status_active = 1  and e.booking_type=4 and a.company_name=$company_name $buyer_id_cond $jobcond $ordercond $stylecond $date_cond and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and d.is_deleted = 0 and d.status_active = 1");
	foreach ($sql_buttonsamfab as $sql_buttonsamfab_row) {
		$button_datasamfab[$sql_buttonsamfab_row[csf('job_no')]]['id']= $sql_buttonsamfab_row[csf('id')];
		$button_datasamfab[$sql_buttonsamfab_row[csf('job_no')]]['item_category']= $sql_buttonsamfab_row[csf('item_category')];
		$button_datasamfab[$sql_buttonsamfab_row[csf('job_no')]]['fabric_source']= $sql_buttonsamfab_row[csf('fabric_source')];
		$button_datasamfab[$sql_buttonsamfab_row[csf('job_no')]]['is_approved']= $sql_buttonsamfab_row[csf('is_approved')];
		$button_datasamfab[$sql_buttonsamfab_row[csf('job_no')]]['booking_type']= $sql_buttonsamfab_row[csf('booking_type')];
	}
	$sql_buttontrim= sql_select("SELECT a.job_no,b.id,a.quotation_id,e.item_category, e.fabric_source,e.is_approved,e.entry_form as entry_fab_id,e.booking_type FROM wo_po_details_master a, wo_po_break_down b, wo_booking_dtls d,wo_booking_mst e WHERE a.job_no = b.job_no_mst and a.job_no = d.job_no and d.job_no = b.job_no_mst and d.booking_no=e.booking_no and e.is_deleted = 0 and e.status_active = 1  and e.booking_type=2 and e.is_short=2 and a.company_name=$company_name $buyer_id_cond  $jobcond $ordercond $stylecond $date_cond and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and d.is_deleted = 0 and d.status_active = 1");
	foreach ($sql_buttontrim as $sql_buttontrim_row) {
		$button_datatrim[$sql_buttontrim_row[csf('job_no')]]['id']= $sql_buttontrim_row[csf('id')];
		$button_datatrim[$sql_buttontrim_row[csf('job_no')]]['entry_fab_id']= $sql_buttontrim_row[csf('entry_fab_id')];
		$button_datatrim[$sql_buttontrim_row[csf('job_no')]]['item_category']= $sql_buttontrim_row[csf('item_category')];
		$button_datatrim[$sql_buttontrim_row[csf('job_no')]]['fabric_source']= $sql_buttontrim_row[csf('fabric_source')];
		$button_datatrim[$sql_buttontrim_row[csf('job_no')]]['is_approved']= $sql_buttontrim_row[csf('is_approved')];
		$button_datatrim[$sql_buttontrim_row[csf('job_no')]]['booking_type']= $sql_buttontrim_row[csf('booking_type')];
	}
	//echo '<pre>';print_r($button_datatrim);die;	
	 $sql_button= sql_select("SELECT a.job_no,b.id,a.quotation_id,c.entry_from, c.costing_date d FROM wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c WHERE a.job_no = b.job_no_mst AND a.job_no = c.job_no AND c.job_no = b.job_no_mst and a.company_name=$company_name $buyer_id_cond $jobcond $ordercond $stylecond $date_cond and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and c.is_deleted = 0 and c.status_active = 1 ");
	foreach ($sql_button as $sql_button_row) {
		$button_data[$sql_button_row[csf('job_no')]]['quotation_id']= $sql_button_row[csf('quotation_id')];
		$button_data[$sql_button_row[csf('job_no')]]['entry_from']= $sql_button_row[csf('entry_from')];
		$button_data[$sql_button_row[csf('job_no')]]['id']= $sql_button_row[csf('id')];
		$button_data[$sql_button_row[csf('job_no')]]['costing_date']= $sql_button_row[csf('costing_date')];
	} 

	$sql_po= sql_select("SELECT a.id AS jobid, a.buyer_name as buyer, a.product_code, a.dealing_marchant, a.style_ref_no, a.job_no, a.order_uom, a.total_set_qnty, b.id, b.po_number, b.GROUPING AS ref_no, b.po_received_date, b.unit_price, TO_CHAR (b.insert_date, 'DD-MON-YYYY') AS insert_date,c.booking_no, (CASE WHEN c.booking_type = 1 AND c.is_short = 2 THEN c.booking_no END) AS fab_booking,(CASE WHEN c.booking_type = 1 AND c.is_short = 2 THEN e.booking_no_prefix_num END) AS fab_booking_pre, (CASE WHEN c.booking_type = 4 AND c.is_short = 2 THEN c.booking_no END) AS sam_booking,(CASE WHEN c.booking_type = 4 AND c.is_short = 2 THEN e.booking_no_prefix_num END) AS sam_booking_pre, (CASE WHEN c.booking_type = 2 AND c.is_short = 2 THEN c.booking_no END) AS trim_booking,(CASE WHEN c.booking_type = 2 AND c.is_short = 2 THEN e.booking_no_prefix_num END) AS trim_booking_pre, (CASE WHEN c.booking_type = 3 AND c.is_short = 2 THEN c.booking_no END) AS aop_booking,(CASE WHEN c.booking_type = 3 AND c.is_short = 2 THEN e.booking_no_prefix_num END) AS aop_booking_pre, (CASE WHEN c.booking_type = 6 AND c.is_short = 2 and d.emb_name=1 THEN c.booking_no END) AS print_booking,(CASE WHEN c.booking_type = 6 AND c.is_short = 2 and d.emb_name=1 THEN e.booking_no_prefix_num END) AS print_booking_pre, (CASE WHEN c.booking_type = 6 AND c.is_short = 2 and d.emb_name=2 THEN c.booking_no END) AS emb_booking,(CASE WHEN c.booking_type = 6 AND c.is_short = 2 and d.emb_name=2 THEN e.booking_no_prefix_num END) AS emb_booking_pre, (CASE WHEN c.booking_type = 3 AND c.entry_form_id = 176  THEN c.booking_no END) AS fabric_booking,(CASE WHEN c.booking_type = 3 AND c.entry_form_id = 176  THEN e.booking_no_prefix_num END) AS fabric_booking_pre FROM wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c left join wo_pre_cost_embe_cost_dtls d on c.job_no=d.job_no and c.pre_cost_fabric_cost_dtls_id=d.id and d.is_deleted = 0 and d.status_active = 1 left join wo_booking_mst e on e.id=c.booking_mst_id and  e.is_deleted = 0 and e.status_active = 1 WHERE a.job_no = b.job_no_mst AND a.job_no = c.job_no AND c.job_no = b.job_no_mst AND b.id = c.po_break_down_id and a.company_name=$company_name $buyer_id_cond $jobcond $ordercond $stylecond $date_cond and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and c.is_deleted = 0 and c.status_active = 1 order by c.booking_no,a.job_no_prefix_num, b.id");
	foreach ($sql_po as $sql_po_row) {
		if($sql_po_row[csf('fab_booking')]!=""){$report_data[$sql_po_row[csf('job_no')]]['fab_booking'].= $sql_po_row[csf('fab_booking')].",";}
		if($sql_po_row[csf('sam_booking')]!=""){$report_data[$sql_po_row[csf('job_no')]]['sam_booking'].= $sql_po_row[csf('sam_booking')].",";}
		if($sql_po_row[csf('trim_booking')]!=""){$report_data[$sql_po_row[csf('job_no')]]['trim_booking'].= $sql_po_row[csf('trim_booking')].",";}
		if($sql_po_row[csf('aop_booking')]!=""){$report_data[$sql_po_row[csf('job_no')]]['aop_booking'].= $sql_po_row[csf('aop_booking')].",";}
		if($sql_po_row[csf('print_booking')]!=""){$report_data[$sql_po_row[csf('job_no')]]['print_booking'].= $sql_po_row[csf('print_booking')].",";}
		if($sql_po_row[csf('fabric_booking')]!=""){$report_data[$sql_po_row[csf('job_no')]]['fabric_booking'].= $sql_po_row[csf('fabric_booking')].",";}
		if($sql_po_row[csf('emb_booking')]!=""){$report_data[$sql_po_row[csf('job_no')]]['emb_booking'].= $sql_po_row[csf('emb_booking')].",";}
		if($sql_po_row[csf('fab_booking_pre')]!=""){$report_data[$sql_po_row[csf('job_no')]]['fab_booking_pre'].= $sql_po_row[csf('fab_booking_pre')].",";}
		if($sql_po_row[csf('sam_booking_pre')]!=""){$report_data[$sql_po_row[csf('job_no')]]['sam_booking_pre'].= $sql_po_row[csf('sam_booking_pre')].",";}
		if($sql_po_row[csf('trim_booking_pre')]!=""){$report_data[$sql_po_row[csf('job_no')]]['trim_booking_pre'].= $sql_po_row[csf('trim_booking_pre')].",";}
		if($sql_po_row[csf('aop_booking_pre')]!=""){$report_data[$sql_po_row[csf('job_no')]]['aop_booking_pre'].= $sql_po_row[csf('aop_booking_pre')].",";}
		if($sql_po_row[csf('print_booking_pre')]!=""){$report_data[$sql_po_row[csf('job_no')]]['print_booking_pre'].= $sql_po_row[csf('print_booking_pre')].",";}
		if($sql_po_row[csf('fabric_booking_pre')]!=""){$report_data[$sql_po_row[csf('job_no')]]['fabric_booking_pre'].= $sql_po_row[csf('fabric_booking_pre')].",";}
		if($sql_po_row[csf('emb_booking_pre')]!=""){$report_data[$sql_po_row[csf('job_no')]]['emb_booking_pre'].= $sql_po_row[csf('emb_booking_pre')].",";}
	}
	$sql_emb= sql_select("SELECT a.job_no,c.emb_name,c.emb_type, d.item_number_id FROM wo_po_details_master a, wo_po_break_down b, wo_pre_cost_embe_cost_dtls c, wo_po_color_size_breakdown d WHERE a.job_no = b.job_no_mst AND c.job_id=d.job_id  and b.id=d.po_break_down_id and a.company_name=$company_name $buyer_id_cond $jobcond $ordercond $stylecond $date_cond and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and c.is_deleted = 0 and c.status_active = 1 ");
	foreach ($sql_emb as $sql_button_row) {
		$emb_item_array[$sql_button_row[csf("job_no")]]['emb_name']=$sql_button_row[csf("emb_name")];
		$emb_item_array[$sql_button_row[csf("job_no")]]['item_number_id']=$sql_button_row[csf("item_number_id")];
		$emb_item_array[$sql_button_row[csf("job_no")]]['emb_type']=$sql_button_row[csf("emb_type")];
	}
	//echo '<pre>';print_r($emb_item_array);yarn_dyeing_prefix_num
	$sql_td= sql_select("SELECT a.job_no,d.yd_wo,d.id as booking_id,e.job_no as fso_no,(CASE WHEN d.entry_form = 41 THEN d.yd_wo END) AS yd_wo,(CASE WHEN d.entry_form = 41 THEN d.yarn_dyeing_prefix_num END) AS yarn_dyeing_prefix_num FROM wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_yarn_dyeing_mst d,wo_yarn_dyeing_dtls e WHERE a.job_no = b.job_no_mst AND a.job_no = c.job_no AND c.job_no = b.job_no_mst AND b.id = c.po_break_down_id and d.id=e.mst_id and a.job_no=e.job_no and c.booking_no=e.FAB_BOOKING_NO and a.company_name=$company_name $buyer_id_cond $jobcond $ordercond $stylecond $date_cond and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and c.is_deleted = 0 and c.status_active = 1 and d.is_deleted = 0 and d.status_active = 1 and e.is_deleted = 0 and e.status_active = 1");
	foreach ($sql_td as $sql_td_row) {
		if($sql_td_row[csf('yd_wo')]!=""){$yarn_data[$sql_td_row[csf('job_no')]]['yd_wo'].= $sql_td_row[csf('yd_wo')].",";}
		if($sql_td_row[csf('yarn_dyeing_prefix_num')]!=""){$yarn_data[$sql_td_row[csf('job_no')]]['yarn_dyeing_prefix_num'].= $sql_td_row[csf('yarn_dyeing_prefix_num')].",";}
		$yarn_data[$sql_td_row[csf('job_no')]]['booking_id']= $sql_td_row[csf('booking_id')];
		$yarn_data[$sql_td_row[csf('job_no')]]['fso_no']= $sql_td_row[csf('fso_no')];
	}
	$sql_knit= sql_select("SELECT a.job_no,d.wo_no,d.id as booking_id,e.FABRIC_SALES_ORDER_NO as fso_no,d.wo_number_prefix_num FROM wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, knitting_work_order_mst d,knitting_work_order_dtls e WHERE a.job_no = b.job_no_mst AND a.job_no = c.job_no AND c.job_no = b.job_no_mst AND b.id = c.po_break_down_id and d.id=e.mst_id and c.booking_no=e.BOOKING_NO and a.company_name=$company_name $buyer_id_cond $jobcond $ordercond $stylecond $date_cond and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and c.is_deleted = 0 and c.status_active = 1 and d.is_deleted = 0 and d.status_active = 1 and e.is_deleted = 0 and e.status_active = 1");
	foreach ($sql_knit as $sql_knit_row) {
		if($sql_knit_row[csf('wo_no')]!=""){$knit_data[$sql_knit_row[csf('job_no')]]['wo_no'].= $sql_knit_row[csf('wo_no')].",";}
		if($sql_knit_row[csf('wo_number_prefix_num')]!=""){$knit_data[$sql_knit_row[csf('job_no')]]['wo_number_prefix_num'].= $sql_knit_row[csf('wo_number_prefix_num')].",";}
		$knit_data[$sql_knit_row[csf('job_no')]]['booking_id']= $sql_knit_row[csf('booking_id')];
		$knit_data[$sql_knit_row[csf('job_no')]]['fso_no']= $sql_knit_row[csf('fso_no')];
	}
	$sql_fso= sql_select("SELECT a.job_no,d.job_no as fso_no FROM wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, fabric_sales_order_mst d WHERE a.job_no = b.job_no_mst AND a.job_no = c.job_no AND c.job_no = b.job_no_mst AND b.id = c.po_break_down_id and c.booking_no=d.SALES_BOOKING_NO and c.BOOKING_MST_ID=d.BOOKING_ID and a.company_name=$company_name $buyer_id_cond $jobcond $ordercond $stylecond $date_cond and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and c.is_deleted = 0 and c.status_active = 1 and d.is_deleted = 0 and d.status_active = 1");
	foreach ($sql_fso as $sql_fso_row) {
		if($sql_fso_row[csf('fso_no')]!=""){$fso_data[$sql_fso_row[csf('job_no')]]['fso_no'].= $sql_fso_row[csf('fso_no')].",";}
	}
	//echo '<pre>';print_r($emb_item_array);
	$sql_rn= sql_select("SELECT a.job_no,d.requisition_no as requisition_no,e.wo_number_prefix_num FROM wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c,wo_non_order_info_dtls d,wo_non_order_info_mst e WHERE a.job_no = b.job_no_mst AND a.job_no = c.job_no AND c.job_no = b.job_no_mst AND b.id = c.po_break_down_id and a.job_no=d.job_no and c.booking_no=d.booking_no and e.id=d.mst_id and a.company_name=$company_name $buyer_id_cond $jobcond $ordercond $stylecond $date_cond and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and c.is_deleted = 0 and c.status_active = 1 and d.is_deleted = 0 and d.status_active = 1 ");
	foreach ($sql_rn as $sql_rn_row) {
		if($sql_rn_row[csf('requisition_no')]!=""){$req_data[$sql_rn_row[csf('job_no')]]['requisition_no'].= $sql_rn_row[csf('requisition_no')].",";}
		if($sql_rn_row[csf('wo_number_prefix_num')]!=""){$req_data[$sql_rn_row[csf('job_no')]]['wo_number_prefix_num'].= $sql_rn_row[csf('wo_number_prefix_num')].",";}
	}

	$print_report_format_ids = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id in (43) and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format_ids);
	$row_id=$format_ids[0];


	$print_report_format_arr3=sql_select("select format_id,template_name from lib_report_template where module_id=2 and report_id=35 and is_deleted=0 and status_active=1 and template_name in ($company_name) ");
	foreach($print_report_format_arr3 as $row){
		$format_ids=explode(",",$row[csf('format_id')]);
		$report_btn_arr[108][$row[csf('template_name')]]=$format_ids[0];					
	}
	if($company_name!=0) $cbo_company_cond="and template_name in($company_name)";else $cbo_company_cond="";
	$sample_booking_print_report_format_arr=return_library_array( "select template_name, format_id from lib_report_template where  module_id=2 and report_id=3 and is_deleted=0 and status_active=1 $cbo_company_cond", "template_name", "format_id");
	$print_report_format_arr26=return_library_array( "select template_name, format_id from lib_report_template where  module_id=2 and report_id=26 and is_deleted=0 and status_active=1 $cbo_company_cond", "template_name", "format_id");
	$emb_print_report_format_arr = return_field_value("format_id", "lib_report_template", "module_id=2 and report_id=89 and is_deleted=0 and status_active=1 $cbo_company_cond");
	$print_report_format_arr=return_library_array( "select template_name, format_id from lib_report_template where  module_id=2 and report_id=11 and is_deleted=0 and status_active=1 $cbo_company_cond", "template_name", "format_id");
	$print_report_format_aop=return_field_value("format_id"," lib_report_template"," module_id=2 and report_id=49 and is_deleted=0 and status_active=1 $cbo_company_cond");
	$Yarn_Dying_Work_order_Sales=return_field_value("format_id"," lib_report_template"," module_id=2 and report_id=228 and is_deleted=0 and status_active=1 $cbo_company_cond");

	if ($template == 1) {
		ob_start();?>
		<div style="width:1730px">
			<fieldset style="width:100%;">
				<table width="1730">
					<tr class="form_caption">
						<td colspan="21" align="center">Work Followup Report</td>
					</tr>
					<tr class="form_caption">
						<td colspan="21" align="center"><? echo $company_library[$company_name]; ?></td>
					</tr>
				</table>
				<table class="rpt_table" width="1730" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="80">Company</th>
						<th width="80">Buyer</th>
						<th width="80">Job No</th>
						<th width="100">Style Ref</th>
						<th width="80">Sample Fabric Booking</th>
						<th width="80">Fabric Booking No.</th>
						<th width="80">FB % on Budget Qty</th>
						<th width="80">Yarn Booking No.</th>
						<th width="80">YB % on Budget Qty</th>
						<th width="80">Trims Booking No.</th>
						<th width="80">TB % on Budget Qty</th>
						<th width="80">Print Wo.</th>
						<th width="80">Embroidery Wo. No.</th>
						<th width="80">AOP WO.</th>
						<th width="80">FSO No.</th>
						<th width="80">Yarn Service WO.</th>
						<th width="80">Y/D WO. No.</th>
						<th width="80">Knitting Sub. Con. WO. No.</th>
						<th width="80">Dyeing Sub. Con. WO. No.</th>
						<th width="80">Fabric Service WO.</th>


					</thead>
				</table>
				<div style="width:1750px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="1730" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<?
						$i = 1;
						foreach ($main_data as $key => $value) {
							//print_r($po_id)
							$fab_booking=implode(",",array_filter(array_unique(explode(",",$report_data[$key]['fab_booking']))));
							$fab_booking_pre=implode(",",array_filter(array_unique(explode(",",$report_data[$key]['fab_booking_pre']))));
							$sam_booking=implode(",",array_filter(array_unique(explode(",",$report_data[$key]['sam_booking']))));
							$sam_booking_pre=implode(",",array_filter(array_unique(explode(",",$report_data[$key]['sam_booking_pre']))));
							$trim_booking=implode(",",array_filter(array_unique(explode(",",$report_data[$key]['trim_booking']))));
							$trim_booking_pre=implode(",",array_filter(array_unique(explode(",",$report_data[$key]['trim_booking_pre']))));
							$aop_booking=implode(",",array_filter(array_unique(explode(",",$report_data[$key]['aop_booking']))));
							$aop_booking_pre=implode(",",array_filter(array_unique(explode(",",$report_data[$key]['aop_booking_pre']))));
							$print_booking=implode(",",array_filter(array_unique(explode(",",$report_data[$key]['print_booking']))));
							$print_booking_pre=implode(",",array_filter(array_unique(explode(",",$report_data[$key]['print_booking_pre']))));
							$fabric_booking=implode(",",array_filter(array_unique(explode(",",$report_data[$key]['fabric_booking']))));
							$fabric_booking_pre=implode(",",array_filter(array_unique(explode(",",$report_data[$key]['fabric_booking_pre']))));
							$emb_booking=implode(",",array_filter(array_unique(explode(",",$report_data[$key]['emb_booking']))));
							$emb_booking_pre=implode(",",array_filter(array_unique(explode(",",$report_data[$key]['emb_booking_pre']))));
							$yd_no=implode(",",array_filter(array_unique(explode(",",$yarn_data[$key]['yd_wo']))));
							$yarn_dyeing_prefix_num=implode(",",array_filter(array_unique(explode(",",$yarn_data[$key]['yarn_dyeing_prefix_num']))));
							$knit_no=implode(",",array_filter(array_unique(explode(",",$knit_data[$key]['wo_no']))));
							$knit_wo_number_prefix_num=implode(",",array_filter(array_unique(explode(",",$knit_data[$key]['wo_number_prefix_num']))));
							
							$req_no=implode(",",array_filter(array_unique(explode(",",$req_data[$key]['requisition_no']))));
							$wo_number_prefix_num=implode(",",array_filter(array_unique(explode(",",$req_data[$key]['wo_number_prefix_num']))));
							$fso_no=implode(",",array_filter(array_unique(explode(",",$fso_data[$key]['fso_no']))));
							$all_po_id=implode(",",array_unique(explode(",",chop($button_datasamfab[$key][id],","))));
							$sample_booking_withorder_print_report_ids=$sample_booking_print_report_format_arr[$company_name];
							$sample_booking_with_print_ids=explode(",",$sample_booking_withorder_print_report_ids);
							$samp_booking_with_first_print_button=array_shift($sample_booking_with_print_ids);
							$print_report_format_ids26=$print_report_format_arr26[$company_name];
							$format_ids26=explode(",",$print_report_format_ids26);
							$print_report_format_ids=$print_report_format_arr[$company_name];
							$format_ids=explode(",",$print_report_format_ids);
							$first_print_button=array_shift($format_ids);
							$format_ids_aop=explode(",",$print_report_format_aop);
							$first_print_button_aop=array_shift($format_ids_aop);
							$ydw_sales_reportArr= explode(',',$Yarn_Dying_Work_order_Sales);
							$ydw_sales_report=$ydw_sales_reportArr[0];
							
							/* $po_br_ids=$button_data[$key]['id'];
							if(!empty($po_br_ids)){
						   		$po_br_idss= explode(",", $po_br_ids);
							} */
							$fabric_nature=$button_datafab[$key][item_category];
							//echo $fabric_nature;

								if($row_id==50){$action='preCostRpt'; } //report_btn_1;
								else if($row_id==51){$action='preCostRpt2';} //report_btn_2;
								else if($row_id==52){$action='bomRpt';} //report_btn_3;
								else if($row_id==63){$action='bomRpt2';} //report_btn_4;
								else if($row_id==156){$action='accessories_details';} //report_btn_5;
								else if($row_id==157){$action='accessories_details2';} //report_btn_6;
								else if($row_id==158){$action='preCostRptWoven';} //report_btn_7;
								else if($row_id==159){$action='bomRptWoven';} //report_btn_8;
								else if($row_id==170){$action='preCostRpt3';} //report_btn_9;
								else if($row_id==171){$action='preCostRpt4';} //report_btn_10;
								else if($row_id==173){$action='preCostRpt5';} //report_btn_10;
								else if($row_id==211){$action='mo_sheet';}
								else if($row_id==142){$action='preCostRptBpkW';}
								else if($row_id==197){$action='bomRpt3';}
								else if($row_id==192){$action='checkListRpt';}
								else if($row_id==221){$action='fabric_cost_detail';}
								else if($row_id==238){$action='summary';}
								else if($row_id==215){$action='budget3_details';}
								else if($row_id==730){$action='budgetsheet';}
								else if($row_id==800){$action='preCostRpt11';}
								$function="generate_worder_report('".$action."','".$main_data[$key][job_no]."',".$company_name.",".$main_data[$key][buyer_name].",'".$main_data[$key][style_ref_no]."','".$button_data[$key][costing_date]."',".$button_data[$key][entry_from].",'".$button_data[$key][quotation_id]."');"; 

									
								 if($button_datafab[$key][booking_type]==1 && $button_datafab[$key][entry_fab_id]==108){
									$row_id=$report_btn_arr[$button_datafab[$key][entry_fab_id]][$company_name];
									if($row_id==723){
										$variable="<a href='#' onClick=\"generate_worder_report3('".$fab_booking."','".$company_name."','".$po_br_ids."','".$button_datafab[$key][item_category]."','".$button_datafab[$key][fabric_source]."','".$main_data[$key][job_no]."','".$button_datafab[$key][is_approved]."','".$row_id."','".$button_datafab[$key][entry_fab_id]."','print_booking_17','".$i."',".$fabric_nature.")\"> ".$fab_booking_pre."<a/>";
									}
									else if($row_id==426){
										$variable="<a href='#' onClick=\"generate_worder_report3('".$report_data[$key][fab_booking]."','".$company_name."','".$po_br_ids."','".$button_datafab[$key][item_category]."','".$button_datafab[$key][fabric_source]."','".$main_data[$key][job_no]."','".$button_datafab[$key][is_approved]."','".$row_id."','".$button_datafab[$key][entry_fab_id]."','show_fabric_booking_report_print23','".$i."',".$fabric_nature.")\"> ".$report_data[$key][fab_booking]."<a/>";
									}else if($row_id==502){
										$variable="<a href='#' onClick=\"generate_worder_report3('".$report_data[$key][fab_booking]."','".$company_name."','".$po_br_ids."','".$button_datafab[$key][item_category]."','".$button_datafab[$key][fabric_source]."','".$main_data[$key][job_no]."','".$button_datafab[$key][is_approved]."','".$row_id."','".$button_datafab[$key][entry_fab_id]."','show_fabric_booking_report26','".$i."',".$fabric_nature.")\"> ".$report_data[$key][fab_booking]."<a/>";
									}
								}
								if($button_datasamfab[$key][booking_type]==4){
									$page_name="Sample Fabric Booking With order";
										if($samp_booking_with_first_print_button==177) //Print Booking
										{
											$action_type="show_fabric_booking_report4"; 
											$variable2="<a href='##'  title='$page_name' onClick=\"generate_fabric_report('".$sam_booking."','".$company_name."','".$all_po_id."','".$button_datasamfab[$key][item_category]."','".$button_datasamfab[$key][fabric_source]."','".$main_data[$key][job_no]."','". $button_datasamfab[$key][is_approved]."','".$action_type."','".$i."')\">".$sam_booking_pre."<a/>";
										}
								}
								if($format_ids26[0]==746 && $button_datatrim[$key][entry_fab_id]==87)
								{ 
									$variable3="<a href='##' onClick=\"generate_trims_report(".$button_datatrim[$key][entry_fab_id].",'". $trim_booking."','".$company_name."','".$all_po_ids."','".$button_datatrim[$key][item_category]."','".$button_datatrim[$key][fabric_source]."','".$main_data[$key][job_no]."','". $button_datatrim[$key][is_approved]."','show_trim_booking_report7','".$i."')\"> ". $trim_booking_pre." </a>";	
								}

								$emb_format_ids=explode(",",$emb_print_report_format_arr);
								$first_print_button_emb=array_shift($emb_format_ids);
								$action_button="";
								if($first_print_button_emb==13)
								{
									$action_button="show_trim_booking_report2";
								}
								else if($first_print_button_emb==235)
								{
									$action_button="show_trim_booking_report9";
								}
								else if($first_print_button_emb==15)
								{
									$action_button="show_trim_booking_report3";
								}
								else if($first_print_button_emb==16)
								{
									$action_button="show_trim_booking_report4";
								}
								else if($first_print_button_emb==177)
								{
									$action_button="show_trim_booking_report5";
								}
								else if($first_print_button_emb==175)
								{
									$action_button="show_trim_booking_report6";
								}
								else if($first_print_button_emb==746)
								{
									$action_button="show_trim_booking_report7";
								}
							$variable4="<a href='##' title='Print Actual' onClick=\"generate_emb_report('".$row[("entry_form")]."','".$print_booking."','".$company_name."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$main_data[$key][job_no]."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$key]['emb_name']."','".$emb_item_array[$key]['item_number_id']."','".$action_button."','".$i."','".$row[("supplier_id")]."','','')\" >".$print_booking_pre." </a>"; //actual print
							
							 if($first_print_button==116)
							{ 
							 $wo_typw_id=2;
							 $variable5="<a href='##' title='PB' onClick=\"generate_service_report('".$wo_typw_id."','".$fabric_booking."','".$company_name."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$main_data[$key][job_no]."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','show_trim_booking_report2','".$i."')\">".$fabric_booking_pre." </a>";	  
							} 
							if($first_print_button_aop==164)
							{ 
							$wo_typw_id=164;
							 $variable6="<a href='##' title='BPKW' onClick=\"generate_aop_report('".$first_print_button_aop."','".$aop_booking."','".$company_name."','".$all_po_id."','".$cbo_category."','".$row[("fabric_source")]."','".$main_data[$key][job_no]."','". $row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','".$emb_item_array[$job_number]['emb_name']."','".$emb_item_array[$job_number]['item_number_id']."','show_trim_booking_report2','".$i."')\">".$aop_booking_pre." </a>";	  
							}
							if($ydw_sales_report==2){$reporAction="generate_report";}
							$variable8="<a  href='##'  onClick=\"generate_yarn_dying_report('".$row[csf("entry_form")]."','".$yd_no."','".$company_name."','".$yarn_data[$key]['booking_id']."','".$cbo_category."','','','".$row[("is_approved")]."','".$cbo_category."','".$row[("is_short")]."','','','".$reporAction."','".$i."')\">".$yarn_dyeing_prefix_num."  <a/>";
								
								if ($i % 2 == 0) $bgcolor = "#E9F3FF";
								else $bgcolor = "#FFFFFF";
						?>
								<tr align="center" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30"><? echo $i; ?></td>
									<td width="80" style=" word-break: break-all"><? echo $company_library[$company_name];  ?></td>
									<td width="80" style=" word-break: break-all"><? echo $buyer_short_name_library[$main_data[$key][buyer_name]];  ?></td>
									<td width="80" style=" word-break: break-all"><a href='##' onclick="<?=$function; ?>"><?=$main_data[$key][job_no]; ?></td>
									<td width="100" style=" word-break: break-all"><? echo $main_data[$key][style_ref_no];  ?></td>
									<td width="80" style=" word-break: break-all"><? echo $variable2;  ?></td>
									<td width="80" style=" word-break: break-all"><? echo $variable;  ?></td>
									<td width="80" style=" word-break: break-all"><?
									
									  //$fabric_booking_perc = $fabricArr[$key][did] * 100 /  $reqArr[$key][did];
										// if(is_finite($fabric_booking_perc) || is_nan($fabric_booking_perc)){$fabric_booking_perc=0;}
										 echo fn_number_format(($fabric_booking_perc), 2) . " %";
									?></td>
									<td width="80" style=" word-break: break-all"><? echo $wo_number_prefix_num;  ?></td>
									<td width="80" style=" word-break: break-all"><? //echo $buyer_short_name_library[$main_data[buyer_name][$po_id][$key]];  ?></td>
									<td width="80" style=" word-break: break-all"><? echo $variable3;  ?></td>
									<td width="80" style=" word-break: break-all"><? //if(is_infinite($trims_perc) || is_nan($trims_perc)){$trims_perc=0;}
                                                echo fn_number_format(($trims_perc), 2) . " %"; ?></td>
									<td width="80" style=" word-break: break-all"><? echo $variable4;  ?></td>
									<td width="80" style=" word-break: break-all"><? echo $variable4;  ?></td>
									<td width="80" style=" word-break: break-all"><? echo $variable6;;  ?></td>
									<td width="80" style=" word-break: break-all"><? echo $fso_no;  ?></td>
									<td width="80" style=" word-break: break-all"><? echo $main_data[style_ref_no][$po_id][$key];  ?></td>
									<td width="80" style=" word-break: break-all"><? echo $variable8;  ?></td>
									<td width="80" style=" word-break: break-all"><? echo $knit_wo_number_prefix_num;  ?></td>
									<td width="80" style=" word-break: break-all"><? echo $main_data[job_no][$po_id][$key];  ?></td>
									<td width="80" style=" word-break: break-all"><? echo $variable5;  ?></td>

								</tr>
						<?
								$i++;
						}
						?>
					</table>

				</div>
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