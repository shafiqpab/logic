<?
include('../../../../includes/common.php');
session_start();
extract($_REQUEST);
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$date = date('Y-m-d');

$user_id = $_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];



if ($action == "load_drop_down_brand") {
	echo create_drop_down("cbo_brand_name", 100, "select id, brand_name from lib_buyer_brand where buyer_id in ('$data') and status_active =1 and is_deleted=0  order by brand_name ASC", "id,brand_name", 1, "--Select--", "", "");
	exit();
}
if ($action == "load_drop_down_team_leader") {
	echo create_drop_down("cbo_team_leader", 100, "select id,team_leader_name from lib_marketing_team  where id='$data' and status_active=1 and is_deleted=0 order by team_leader_name", "id,team_leader_name", 1, "-Team Leader-", $selected, "");
	exit();
}


if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 130, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "- All Buyer -", $selected, "", "load_drop_down( 'requires/data_entry_followup_report_controller', this.value, 'load_drop_down_brand', 'brand_td');");
	exit();
}



if ($action == "load_drop_down_season") {
	echo create_drop_down("cbo_season", 100, "select id, season_name from lib_buyer_season where buyer_id in ($data) and status_active =1 and is_deleted=0 order by season_name ASC", "id,season_name", 1, "-- Select Season--", "", "");
	exit();
}

if ($action == "load_drop_down_dealing_merchant") {
	echo create_drop_down("cbo_dealing_merchant", 150, "select id,team_member_name from lib_mkt_team_member_info where team_id in ('$data') and status_active =1 and is_deleted=0 and data_level_security=1 order by team_member_name", "id,team_member_name", 1, "-- Select Team Member --", $selected, "");
	exit();
}

if ($action == "load_drop_down_team_leader") {
	echo create_drop_down("cbo_team_leader", 100, "select id,team_leader_name from lib_marketing_team  where id='$data' and status_active=1 and is_deleted=0 order by team_leader_name", "id,team_leader_name", 1, "-Team Leader-", $selected, "");
	exit();
}

if ($action == "report_generate") {
	$company_name = str_replace("'", "", $cbo_company_name);
	$txt_style_ref = str_replace("'", "", $txt_style_ref);
	$buyer_name = str_replace("'", "", $cbo_buyer_name);
	$team_name = str_replace("'", "", $cbo_team_name);
	$cbo_dealing_merchant = str_replace("'", "", $cbo_dealing_merchant);
	$search_by = str_replace("'", "", $cbo_search_by);
	$search_string = str_replace("'", "", $txt_search_string);

	$txt_ref = str_replace("'", "", $txt_ref);
	$date_from = str_replace("'", "", $txt_date_from);
	$date_to = str_replace("'", "", $txt_date_to);
	$category_by = str_replace("'", "", $cbo_category_by);
	$year_id = str_replace("'", "", $cbo_year);

	$cbo_season = str_replace("'", "", $cbo_season);

	$txt_job_no = str_replace("'", "", $txt_job_no);
	$cbo_brand_name = str_replace("'", "", $cbo_brand_name);
	$cbo_season_year = str_replace("'", "", $cbo_season_year);
	$cbo_team_leader = str_replace("'", "", $cbo_team_leader);

	//
	//if($data[0]==0) $company_name="%%"; else $company_name=$data[0];
	//if($data[1]==0) $buyer_name="%%"; else $buyer_name=$data[1];
	/*if($company_name==0 && $style_owner==0 && $buyer_name==0 && $date_from=="" && $date_to=="" )
    {
        echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select Company or Style Owner or Buyer first.";
        die;
    }*/
	if ($cbo_season != 0) $season_cond = "and a.season_buyer_wise=$cbo_season ";
	else $season_cond = "";
	//$brand_cond="";
	if ($cbo_brand_name > 0) $brand_id_cond = "and a.brand_id in ($cbo_brand_name)";
	else $brand_id_cond = "";



	if ($cbo_season_year > 0) $season_yr_cond = "and a.season_year =$cbo_season_year";
	else $season_yr_cond = "";
	if ($cbo_team_leader > 0) $team_leader_cond = "and a.team_leader in ($cbo_team_leader)";
	else $team_leader_cond = "";
	if ($team_name > 0) $team_leader_cond = "and a.team_leader in ($team_name)";
	else $team_leader_cond = "";
	if ($cbo_dealing_merchant > 0) $dealing_merchant_cond = "and a.dealing_marchant in ($cbo_dealing_merchant)";
	else $dealing_merchant_cond = "";

	// $season_yr_cond="and a.brand_id=$cbo_brand_name ";
	//season_year



	if (trim($txt_job_no) != "") $job_cond = "and a.job_no_prefix_num=$txt_job_no";
	else $job_cond = "";
	if (trim($txt_style_ref) != "") $style_ref_cond = "and a.style_ref_no=$txt_style_ref";
	else $style_ref_cond = "";


	if ($buyer_name == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				$buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				$buyer_id_cond2 = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else {
				$buyer_id_cond = "";
				$buyer_id_cond2 = "";
			}
		} else {
			$buyer_id_cond = "";
			$buyer_id_cond2 = "";
		}
	} else {
		$buyer_id_cond = " and a.buyer_name in ($buyer_name) "; //.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2 = " and a.buyer_id in ($buyer_name)";
	}

	if (trim($date_from) != "") $start_date = $date_from;
	if (trim($date_to) != "") $end_date = $date_to;
	if (trim($txt_ref) != "") $ref_cond = "and b.grouping='$txt_ref'";
	else $ref_cond = "";




	if ($db_type == 0) {
		$start_date = change_date_format($date_from, 'yyyy-mm-dd', '-');
		$end_date = change_date_format($date_to, 'yyyy-mm-dd', '-');
	} else if ($db_type == 2) {
		$start_date = change_date_format($date_from, 'yyyy-mm-dd', '-', 1);
		$end_date = change_date_format($date_to, 'yyyy-mm-dd', '-', 1);
	}

	//$cbo_category_by=$data[7]; $caption_date='';
	if ($category_by == 1) {
		if ($start_date != "" && $end_date != "") $date_cond = "and to_date(to_char(qc.insert_date, 'DD-MON-YYYY')) BETWEEN '$start_date' AND '$end_date'";
		else $date_cond = "";
	} else if ($category_by == 2) {
		if ($start_date != "" && $end_date != "") $date_cond = "and to_date(to_char(b.insert_date, 'DD-MON-YYYY')) BETWEEN '$start_date' AND '$end_date'";
		else $date_cond = "";
	} else if ($category_by == 3) {
		if ($start_date != "" && $end_date != "") {
			if ($db_type == 0) $date_cond = " and b.pub_shipment_date between '$start_date' and '$end_date' ";
			else if ($db_type == 2) $date_cond = " and b.pub_shipment_date between '$start_date' and '$end_date' ";
		} else $date_cond = "";
	} else if ($category_by == 4) {
		if ($start_date != "" && $end_date != "") $date_cond = "and to_date(to_char(fs.insert_date, 'DD-MON-YYYY')) BETWEEN '$start_date' AND '$end_date'";
		else $date_cond = "";
	}
	//echo $date_cond;

	if ($search_by == 1) {
		if ($search_string == "") $search_string_cond = "";
		else $search_string_cond = " and b.po_number like '$search_string'";
	} else if ($search_by == 2) {
		if ($search_string == "") $search_string_cond = "";
		else $search_string_cond = " and a.style_ref_no like '$search_string'";
	}
	if ($db_type == 0) {
		if ($year_id != 0) $year_cond = " and YEAR(a.insert_date)=$year_id";
		else $year_cond = "";
	} else if ($db_type == 2) {
		if ($year_id != 0) $year_cond = " and to_char(a.insert_date,'YYYY')=$year_id";
		else $year_cond = "";
	}


	$user_name_arr = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
	$buyer_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$bank_name_arr = return_library_array("select id, bank_name from lib_bank", 'id', 'bank_name');
	$company_short_name_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$company_name_arr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$buyer_wise_season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", 'id', 'season_name');
	$team_leader_name_arr = return_library_array("select id,team_leader_name from lib_marketing_team", 'id', 'team_leader_name');
	$dealing_marchant_arr = return_library_array("select id,team_member_name from  lib_mkt_team_member_info", 'id', 'team_member_name');
	$factory_merchant_arr = return_library_array("select a.id, a.team_member_name from lib_mkt_team_member_info a, lib_marketing_team b where a.team_id=b.id  and a.status_active =1 and a.is_deleted=0 order by a.team_member_name", 'id', 'team_member_name');
	$imge_arr = return_library_array("select master_tble_id, image_location from common_photo_library where file_type=1", 'master_tble_id', 'image_location');
	$supplier_arr = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id  and a.status_active =1 and a.is_deleted=0 order by supplier_name", 'id', 'supplier_name');


	// $buycost_sql="select ready_to_approve,job_id from qc_confirm_mst where status_active = 1 and  is_deleted = 0  ";				

	// $buycost_data=sql_select($buycost_sql);
	// foreach($buycost_data as $bcval){
	// 	$buycost_arr[$bcval[csf('job_id')]]['ready_to_app']=$bcval[csf('ready_to_approve')];
	// }




	$buycost_arr = return_library_array("select job_id,job_id from qc_confirm_mst", 'job_id', 'job_id');
	$bank_name_arr = return_library_array("select id, bank_name from lib_bank", 'id', 'bank_name');

	// $supplier_name=return_field_value("contact_person","lib_supplier","id and is_deleted=0 and status_active=1");
	ob_start();
?>
	<div align="center">
		<div align="center">

			<h3 style="width:100%;" align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel</h3>

			<div id="content_report_panel">
				<table width="4460" border="1" class="rpt_table" rules="all" align="left">
					<thead>
						<tr>
							<!-- width=1300 -->
							<th rowspan="2" width="50">SL</th>
							<th rowspan="2" width="100">Company</th>
							<th rowspan="2" width="100">Buyer</th>
							<th rowspan="2" width="100">Style</th>
							<th rowspan="2" width="80">Season</th>
							<th rowspan="2" width="100">Job Insert By</th>
							<th rowspan="2" width="120"> Team Leader</th>
							<th rowspan="2" width="120">Dealing Merchant</th>
							<th rowspan="2" width="100">Factory Merchant</th>
							<th rowspan="2" width="80">Buyer Costing</th>
							<th rowspan="2" width="60">Job Year</th>
							<th rowspan="2" width="60">Job No Sufix</th>
							<th rowspan="2" width="100">Job No</th>
							<th rowspan="2" width="100">Job Insert Date</th>
							<th rowspan="2" width="80">Job Qnty(Pcs)</th>
							<th rowspan="2" width="80">Job Qnty(DZN)</th>
							<th rowspan="2" width="100">Job Amount (USD)</th>
							<th rowspan="2" width="70">No Of PO</th>

							<!-- width=1030 -->
							<th rowspan="2" width="70">Booking Costing App.</th>
							<th rowspan="2" width="100">Booking No</th>
							<th rowspan="2" width="80">Ready To Approved</th>
							<th rowspan="2" width="60">Booking App.</th>
							<th rowspan="2" width="80">Booking Type(WO)</th>
							<th rowspan="2" width="100">Supplier</th>
							<th rowspan="2" width="80">Fabric Source</th>
							<th rowspan="2" width="80">Garments Item</th>
							<th rowspan="2" width="100">Fabric Des.</th>
							<th rowspan="2" width="80">Booking Qty.</th>
							<th rowspan="2" width="80">Booking Amount</th>
							<th rowspan="2" width="100">Booking Date</th>



							<th rowspan="2" width="100">FSO No</th>
							<th rowspan="2" width="100">FSO Qty</th>

							<!-- Knitting Program -->
							<th colspan="2" width="80">Yarn</th>
							<th rowspan="2" width="80">Balance </th>

							<!-- Knitting Program -->
							<th colspan="3" width="80">Knitting Program</th>
							<th rowspan="2" width="80">Balance </th>

							<!-- Knitting Production -->
							<th colspan="3" width="80">Knitting Production</th>



							<!-- width=720 -->
							<th rowspan="2" width="80">Gray Rcv </th>
							<th rowspan="2" width="80">Gray Issue </th>
							<th rowspan="2" width="80">Stock In Hand </th>
							<th rowspan="2" width="80">Batch Qty.</th>
							<th rowspan="2" width="80">Dyeing Prod.</th>
							<th rowspan="2" width="80">Finish Prod.</th>
							<th rowspan="2" width="80">Textile Rcvd</th>
							<th rowspan="2" width="80">Textile Issue</th>
							<th rowspan="2" width="80">Textile In Hand</th>
							<th rowspan="2" width="80">Issue Blance</th>



						</tr>
						<tr>

							<!-- yarn -->
							<th width="80">Demand</th>
							<th width="80">Issue</th>

							<!-- Knitting Program -->
							<th width="80">In House</th>
							<th width="80">Out Bound</th>
							<th width="80"> Prog Qty </th>


							<!-- Knitting Production -->
							<th width="80">In House</th>
							<th width="80">Out Bound</th>
							<th width="80"> knit Prod </th>

						</tr>
					</thead>
				</table>
				<div style="max-height:400px; overflow-y:scroll; float:left; width:4480px;" id="scroll_body">
					<table width="4460" border="1" class="rpt_table" rules="all" id="table_body" align="left">
						<?php


						$currency_data = sql_select("select id,company_id,currency,conversion_rate,marketing_rate,con_date from currency_conversion_rate where status_active=1 and is_deleted=0 and company_id in ($company_name) order by id asc ");
						//  $currency=$currency_data[0][csf("conversion_rate")];

						foreach ($currency_data as $cval) {
							$currency_arr[$cval[csf('company_id')]]['currency_rate'] = $cval[csf('conversion_rate')];
						}

						//  echo "<pre>";
						//  print_r($currency_arr);


						$sql = "select a.id as job_id,b.id as po_id,a.company_name,to_char(a.insert_date,'YYYY') as year,a.job_no,a.job_no_prefix_num,a.style_ref_no,a.buyer_name,a.season_buyer_wise,a.job_quantity,a.total_price,a.team_leader,a.dealing_marchant,a.factory_marchant,a.inserted_by, b.po_number, b.po_received_date,b.pub_shipment_date, b.shipment_date, b.po_quantity, b.unit_price, b.po_total_price, b.excess_cut, b.plan_cut ,d.booking_no,d.booking_type,d.wo_qnty,d.fin_fab_qnty,d.adjust_qty,d.amount,a.gmts_item_id,e.ready_to_approved,e.supplier_id,e.booking_date,e.fabric_source,e.is_short,e.entry_form,e.pay_mode,a.insert_date,a.quotation_id,a.season_year from wo_po_details_master a LEFT JOIN qc_mst qc on a.style_ref_no=qc.style_ref,wo_po_break_down b LEFT JOIN wo_booking_dtls d on d.PO_BREAK_DOWN_ID=b.id and    d.status_active=1 and d.is_deleted=0  LEFT JOIN wo_booking_mst e on d.booking_no=e.booking_no	INNER JOIN fabric_sales_order_mst fs
						ON fs.booking_id=e.id where a.job_no=b.job_no_mst and a.company_name in ($company_name) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $date_cond  $job_cond $style_ref_cond $team_leader_cond $dealing_merchant_cond $buyer_id_cond $ref_cond $brand_id_cond $season_cond $season_yr_cond $year_cond order by a.job_no asc, d.booking_no";

						//echo $sql;die;
						$data_array = sql_select($sql);
						$main_data = array();
						foreach ($data_array as $val) {

							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['company'] = $val[csf('company_name')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['job_no'] = $val[csf('job_no')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['job_insert_date'] = $val[csf('insert_date')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['job_id'] = $val[csf('job_id')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['factory_marchant'] = $val[csf('factory_marchant')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['inserted_by'] = $user_name_arr[$val[csf('inserted_by')]];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['year'] = $val[csf('year')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['job_no_prefix_num'] = $val[csf('job_no_prefix_num')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['style_ref_no'] = $val[csf('style_ref_no')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['buyer_name'] = $val[csf('buyer_name')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['season_buyer_wise'] = $val[csf('season_buyer_wise')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['season_year'] = $val[csf('season_year')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['pay_mode'] = $val[csf('pay_mode')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['job_quantity'] = $val[csf('job_quantity')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['total_price'] = $val[csf('total_price')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['team_leader'] = $val[csf('team_leader')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['dealing_marchant'] = $val[csf('dealing_marchant')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['ready_to_approved'] = $val[csf('ready_to_approved')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['booking_date'] = $val[csf('booking_date')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['supplier_id'] = $val[csf('supplier_id')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['order_qty'] += $val[csf('po_quantity')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['booking_type'] = $val[csf('booking_type')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['fabric_source'] = $val[csf('fabric_source')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['entry_form'] = $val[csf('entry_form')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['is_short'] = $val[csf('is_short')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['quotation_id'] = $val[csf('quotation_id')];




							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['wo_qnty'] += $val[csf('fin_fab_qnty')] - $val[csf('adjust_qty')];
							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['amount'] += $val[csf('amount')];

							$po_id_arr[$val[csf('company_name')]][$val[csf('job_no')]][$val[csf('booking_no')]][$val[csf('po_id')]] = $val[csf('po_id')];

							$main_data[$val[csf('job_no')]][$val[csf('booking_no')]]['item'] = explode(",", $val[csf('gmts_item_id')]);
							// $item_arr[$val[csf('job_no')]]
							$booking_arr[$val[csf('booking_no')]] = $val[csf('booking_no')];
							$job_arr[$val[csf('job_no')]] = $val[csf('job_no')];

							$booking_job_arr[$val[csf('booking_no')]][$val[csf('job_no')]] = $val[csf('job_no')];

							$po_arr[$val[csf('po_id')]] = $val[csf('po_id')];
						}


						//=======================================================================job qty pcs================================================================

						$po_sql = "select country_id, item_number_id, cutup, country_ship_date, code_id, ultimate_country_id, size_number_id, ul_country_code, pack_type, pack_qty, pcs_per_pack, order_quantity, plan_cut_qnty, order_total, country_avg_rate,po_break_down_id from wo_po_color_size_breakdown where  is_deleted=0 " . where_con_using_array($po_arr, 1, 'po_break_down_id') . "   and status_active in (1,2,3) order by country_ship_date";

						$po_qty_data = sql_select($po_sql);
						foreach ($po_qty_data as $pqty) {


							$po_wise_qty_pcs[$pqty[csf('po_break_down_id')]]['job_qty_pcs'] += $pqty[csf('order_quantity')];
						}

						// echo "<pre>";
						// print_r($booking_job_arr);
						//==========================================================================================
						$precost_sql = "select ready_to_approved,job_no,costing_date,costing_per from wo_pre_cost_mst where status_active = 1 and  is_deleted = 0 " . where_con_using_array($job_arr, 1, 'job_no') . " ";

						$precost_data = sql_select($precost_sql);
						foreach ($precost_data as $pcval) {
							$precost_arr[$pcval[csf('job_no')]]['ready_to_app'] = $pcval[csf('ready_to_approved')];
							$precost_arr[$pcval[csf('job_no')]]['costing_date'] = $pcval[csf('costing_date')];
							$precost_arr[$pcval[csf('job_no')]]['costing_per'] = $pcval[csf('costing_per')];
						}





						//=====================================FSO===================================================

						$fso_data = sql_select("select a.id, a.job_no, a.company_id, a.sales_booking_no, a.booking_id,a.po_job_no,sum(c.grey_qty) as grey_qty from fabric_sales_order_mst a left join wo_booking_mst b 	on a.booking_id=b.id ,fabric_sales_order_dtls c where a.status_active=1 and a.is_deleted=0 and c.mst_id=a.id  " . where_con_using_array($booking_arr, 1, 'b.booking_no') . "  group by a.id, a.job_no, a.company_id, a.sales_booking_no, a.booking_id,a.po_job_no");



						foreach ($fso_data as $vals) {
							$booking_wise_data_arr[$vals[csf('sales_booking_no')]]['fso_no'] = $vals[csf('job_no')];
							$booking_wise_data_arr[$vals[csf('sales_booking_no')]]['grey_qty'] += $vals[csf('grey_qty')];
						}

						//=====================================Knitting Program===================================================
						$plna_knit_sql = "select a.booking_no,b.id, b.knitting_source, b.knitting_party,b.width_dia_type,b.program_qnty from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.knitting_source in (1,3) " . where_con_using_array($booking_arr, 1, 'a.booking_no') . "";

						$plna_knit_data = sql_select($plna_knit_sql);

						foreach ($plna_knit_data as $pval) {
							if ($pval[csf('knitting_source')] == 1) {
								$booking_wise_data_arr[$pval[csf('booking_no')]]['in_knit'] += $pval[csf('program_qnty')];
							} elseif ($pval[csf('knitting_source')] == 3) {
								$booking_wise_data_arr[$pval[csf('booking_no')]]['out_knit'] += $pval[csf('program_qnty')];
							}
						}

						//=======================================Knitting Production=====================================================

						$plan_knit_prod_sql = "select a.id,a.job_no as po_number,a.sales_booking_no,a.booking_type,grey_receive_qnty, e.knitting_source	from fabric_sales_order_mst a, fabric_sales_order_dtls b ,pro_roll_details c,pro_grey_prod_entry_dtls d,inv_receive_master e	where a.id=b.mst_id  and c.po_breakdown_id=a.id and c.entry_form=2 and c.status_active=1 and c.DTLS_ID=d.id and e.id=d.mst_id " . where_con_using_array($booking_arr, 1, 'a.sales_booking_no') . "  group by a.id, a.job_no,a.sales_booking_no,a.booking_type ,grey_receive_qnty, e.knitting_source";
						// echo $plan_knit_prod_sql;
						$plna_knit_prod_data = sql_select($plan_knit_prod_sql);

						foreach ($plna_knit_prod_data as $kpval) {
							if ($kpval[csf('knitting_source')] == 1) {
								$booking_wise_data_arr[$kpval[csf('sales_booking_no')]]['in_knit_prod'] += $kpval[csf('grey_receive_qnty')];
							} elseif ($kpval[csf('knitting_source')] == 3) {
								$booking_wise_data_arr[$kpval[csf('sales_booking_no')]]['out_knit_prod'] += $kpval[csf('grey_receive_qnty')];
							}
						}

						//=======================================Gray Rcv=====================================================

						$gray_rec_sql = "SELECT   a.recv_number,a.booking_id,a.booking_no, a.knitting_source, b.id as dtls_id, b.prod_id,b.trans_id,c.qnty,c.rate, c.qc_pass_qnty,c.po_breakdown_id,d.job_no as fso_no,d.sales_booking_no,d.po_job_no
						FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c left join fabric_sales_order_mst d on c.po_breakdown_id=d.id	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58  and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 " . where_con_using_array($booking_arr, 1, 'd.sales_booking_no') . "";
						$gray_rec_sql_data = sql_select($gray_rec_sql);

						foreach ($gray_rec_sql_data as $grval) {

							$booking_wise_data_arr[$grval[csf('sales_booking_no')]]['gray_rcv_qnty'] += $grval[csf('qnty')];
						}


						//=============================================Gray Issue=================================================

						$gray_issue_sql = "SELECT a.dtls_id, a.qnty, a.po_breakdown_id, c.trans_id,b.job_no as fso_no,b.sales_booking_no,b.po_job_no from pro_roll_details a left join fabric_sales_order_mst b on a.po_breakdown_id=b.id, inv_grey_fabric_issue_dtls c  where a.dtls_id=c.id and a.entry_form=61 and a.status_active=1 					and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 " . where_con_using_array($booking_arr, 1, 'b.sales_booking_no') . "";


						$gray_issue_data = sql_select($gray_issue_sql);

						foreach ($gray_issue_data as $gival) {

							$booking_wise_data_arr[$gival[csf('sales_booking_no')]]['gray_issue_qnty'] += $gival[csf('qnty')];
						}


						//===========================================Batch Qty======================================================

						$batch_sql = "SELECT a.batch_no, a.batch_weight, a.booking_no, a.sales_order_no, a.sales_order_id,b.sales_booking_no	from pro_batch_create_mst a,fabric_sales_order_mst b where  a.SALES_ORDER_NO=b.job_no " . where_con_using_array($booking_arr, 1, 'a.booking_no') . "";


						$batch_data = sql_select($batch_sql);

						foreach ($batch_data as $bval) {

							$booking_wise_data_arr[$bval[csf('sales_booking_no')]]['batch_qnty'] += $bval[csf('batch_weight')];
						}


						//============================================dyeing Prod============================================
						$dye_prod_sql = "select a.id,a.company_id,a.batch_id,a.batch_no,sum(b.batch_qty) as batch_qty,sum(b.production_qty) as production_qty , c.booking_no,c.sales_order_no from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst c  where a.id=b.mst_id and a.batch_id=c.id  and a.entry_form=35  and a.result=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.load_unload_id in(2) " . where_con_using_array($booking_arr, 1, 'c.booking_no') . "   group by a.id,a.batch_id,a.batch_no,a.company_id , c.booking_no,c.sales_order_no order by a.id";

						$dye_prod_data = sql_select($dye_prod_sql);

						foreach ($dye_prod_data as $dval) {

							$booking_wise_data_arr[$dval[csf('booking_no')]]['dye_prod_qnty'] += $dval[csf('production_qty')];
						}


						//===========================================finis Prod=========================================================

						$finis_prod_sql = "select  b.id, b.trans_id, b.prod_id, b.batch_id, b.receive_qnty, b.reject_qty,b.wgt_lost_qty, b.order_id,b.grey_used_qty , c.booking_no,c.sales_order_no from inv_receive_master a, pro_finish_fabric_rcv_dtls b ,pro_batch_create_mst c  where a.id=b.mst_id and b.batch_id=c.id  and a.item_category=2 and a.entry_form=7 " . where_con_using_array($booking_arr, 1, 'c.booking_no') . "";

						$finis_prod_data = sql_select($finis_prod_sql);

						foreach ($finis_prod_data as $fval) {

							$booking_wise_data_arr[$fval[csf('booking_no')]]['finish_prod_qnty'] += $fval[csf('receive_qnty')];
						}
						//  echo "<pre>";
						//  print_r($main_data);die;

						//=======================================Textile Rcvd========================================================

						$textile_rcv_sql = "select a.receive_basis,b.id, b.prod_id, b.batch_id,b.receive_qnty, b.reject_qty,b.order_id , c.booking_no,c.sales_order_no from  inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c 
						where  b.status_active = 1 and b.is_deleted = 0 and a.id=b.mst_id and b.batch_id=c.id  " . where_con_using_array($booking_arr, 1, 'c.booking_no') . "";

						$textile_rcv_data = sql_select($textile_rcv_sql);
						foreach ($textile_rcv_data as $trval) {

							$booking_wise_data_arr[$trval[csf('booking_no')]]['textile_rcv_qnty'] += $trval[csf('receive_qnty')];
						}



						//=============================================Textile issue===============================================



						$textile_issue_sql = "select a.issue_number,b.id dtls_id, b.batch_id, b.prod_id,sum(b.issue_qnty) delivery_qnty, b.order_id,b.trans_id,c.batch_no, c.booking_no,c.sales_order_no from inv_issue_master a,inv_finish_fabric_issue_dtls b,pro_batch_create_mst c  where a.id=b.mst_id  and b.batch_id=c.id  and a.status_active='1' and a.is_deleted='0' and b.status_active=1 and b.is_deleted=0 " . where_con_using_array($booking_arr, 1, 'c.booking_no') . " group by a.issue_number,b.id , b.batch_id, b.prod_id, b.order_id,b.trans_id,c.batch_no, c.booking_no,c.sales_order_no";

						$textile_issue_data = sql_select($textile_issue_sql);
						foreach ($textile_issue_data as $trval) {

							$booking_wise_data_arr[$trval[csf('booking_no')]]['textile_issue_qnty'] += $trval[csf('delivery_qnty')];
						}

						//=============================================YARN===============================================



						$yarn_demand_sql = "SELECT A.ID, A.YARN_QNTY, A.REQUISITION_NO, A.PROD_ID, A.KNIT_ID, C.SALES_BOOKING_NO, C.JOB_NO, C.BUYER_ID, C.CUSTOMER_BUYER, C.WITHIN_GROUP, D.KNITTING_SOURCE, D.KNITTING_PARTY,e.demand_qnty FROM PPL_YARN_REQUISITION_ENTRY A, PPL_PLANNING_INFO_ENTRY_DTLS D, PPL_PLANNING_ENTRY_PLAN_DTLS B, FABRIC_SALES_ORDER_MST C, ppl_yarn_demand_entry_dtls e WHERE A.KNIT_ID=B.DTLS_ID AND A.KNIT_ID=D.ID AND B.DTLS_ID=D.ID and e.requisition_no=a.requisition_no and e.requisition_no=a.requisition_no AND B.BOOKING_NO = C.SALES_BOOKING_NO " . where_con_using_array($booking_arr, 1, 'c.sales_booking_no') . "";
						$yarn_demand_data = sql_select($yarn_demand_sql);
						foreach ($yarn_demand_data as $row) {

							$booking_wise_data_arr[$row[csf('sales_booking_no')]]['demand_qnty'] += $row[csf('demand_qnty')];
						}


						$yarn_issue_sql = "SELECT A.ID, A.YARN_QNTY, A.REQUISITION_NO, A.PROD_ID, A.KNIT_ID, C.SALES_BOOKING_NO, C.JOB_NO, C.BUYER_ID, C.CUSTOMER_BUYER, C.WITHIN_GROUP, D.KNITTING_SOURCE, D.KNITTING_PARTY,e.cons_quantity FROM PPL_YARN_REQUISITION_ENTRY A, PPL_PLANNING_INFO_ENTRY_DTLS D, PPL_PLANNING_ENTRY_PLAN_DTLS B, FABRIC_SALES_ORDER_MST C, inv_transaction e WHERE A.KNIT_ID=B.DTLS_ID AND A.KNIT_ID=D.ID AND B.DTLS_ID=D.ID and e.requisition_no=a.requisition_no and e.requisition_no=a.requisition_no AND B.BOOKING_NO = C.SALES_BOOKING_NO " . where_con_using_array($booking_arr, 1, 'c.sales_booking_no') . "";
						$yarn_issue_data = sql_select($yarn_issue_sql);
						foreach ($yarn_issue_data as $row) {

							$booking_wise_data_arr[$row[csf('sales_booking_no')]]['issue_qnty'] += $row[csf('cons_quantity')];
						}


						//=============================================approval page wise approved==========================================
						$approval_sql = "select a.id, a.booking_no_prefix_num, a.booking_no,a.fabric_source, a.booking_type, a.is_short,a.booking_date, a.is_approved,a.entry_form,d.job_no from wo_booking_mst a, approval_history b, wo_po_break_down c,wo_booking_dtls d where a.id=b.mst_id and a.booking_no=d.booking_no and d.job_no=c.job_no_mst  and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.current_approval_status=1 " . where_con_using_array($job_arr, 1, 'd.job_no') . "  group by  a.id, a.booking_no_prefix_num, a.booking_no,a.fabric_source, a.booking_type, a.is_short,a.booking_date, a.is_approved,a.entry_form,d.job_no";


						$approval_data = sql_select($approval_sql);
						foreach ($approval_data as $apval) {
							$approval_data[$apval[csf('booking_no')]]['ready_to_app'] = $apval[csf('is_approved')];
						}



						$i = 1;


						foreach ($main_data as $job_id => $booking_data) {
							foreach ($booking_data as $booking_id => $row) {

								$knit_prog = $booking_wise_data_arr[$booking_id]['in_knit'] + $booking_wise_data_arr[$booking_id]['out_knit'];
								$knit_prod = $booking_wise_data_arr[$booking_id]['in_knit_prod'] + $booking_wise_data_arr[$booking_id]['out_knit_prod'];
								$yarn_balance = $booking_wise_data_arr[$booking_id]['demand_qnty'] - $booking_wise_data_arr[$booking_id]['issue_qnty'];
								$stock_in_hand = $booking_wise_data_arr[$booking_id]['gray_rcv_qnty'] - $booking_wise_data_arr[$booking_id]['gray_issue_qnty'];
								$textile_in_hand = $booking_wise_data_arr[$booking_id]['textile_rcv_qnty'] - $booking_wise_data_arr[$booking_id]['textile_issue_qnty'];
								$po_id = implode(",", $po_id_arr[$row['company']][$row['job_no']][$booking_id]);
								$job_no = $row['job_no'];
								$style_ref = $row['style_ref_no'];

								$variable = "'" . $row['company'] . '_' . $row['buyer_name'] . '_' . $style_ref . '_' . $job_no . '_' . $row['job_id'] . '_' . $row['quotation_id'] . '_' . $precost_arr[$row['job_no']]['costing_date'] . '_' . $po_id . '_' . $precost_arr[$row['job_no']]['costing_per'] . "'";



								$job_qty_pcs = 0;
								foreach ($po_id_arr[$row['company']][$row['job_no']][$booking_id] as $val) {
									$job_qty_pcs += $po_wise_qty_pcs[$val]['job_qty_pcs'];
								}



								$costingPer = $precost_arr[$row['job_no']]['costing_per'];
								if ($costingPer == 1) {
									$order_price_per_dzn = 12;
									$costing_for = " DZN";
								} else if ($costingPer == 2) {
									$order_price_per_dzn = 1;
									$costing_for = " PCS";
								} else if ($costingPer == 3) {
									$order_price_per_dzn = 24;
									$costing_for = " 2 DZN";
								} else if ($costingPer == 4) {
									$order_price_per_dzn = 36;
									$costing_for = " 3 DZN";
								} else if ($costingPer == 5) {
									$order_price_per_dzn = 48;
									$costing_for = " 4 DZN";
								}
						?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

									<td width="50"><? echo $i; ?></td>
									<td width="100"><? echo $company_short_name_arr[$row['company']]; ?></td>
									<td width="100">
										<div style="word-wrap:break-word; width:100px"><? echo $buyer_short_name_arr[$row['buyer_name']]; ?></div>
									</td>
									<td width="100">
										<div style="word-wrap:break-word; width:100px"><? echo $row['style_ref_no']; ?></div>
									</td>
									<td width="80">
										<div style="word-wrap:break-word; width:80px"><? echo $buyer_wise_season_arr[$row['season_buyer_wise']] . "-" . $row['season_year']; ?></div>
									</td>
									<td width="100"><? echo $row['inserted_by']; ?></td>
									<td width="120">
										<div style="word-wrap:break-word; width:120px"><? echo $team_leader_name_arr[$row['team_leader']]; ?> </div>
									</td>
									<td width="120">
										<div style="word-wrap:break-word; width:120px"><? echo $dealing_marchant_arr[$row['dealing_marchant']]; ?></div>
									</td>
									<td width="100">
										<div style="word-wrap:break-word; width:100px"><? echo $factory_merchant_arr[$row['factory_marchant']]; ?></div>
									</td>
									<td width="80"><? if ($buycost_arr[$row['job_id']]) {
														echo "confirm";
													} ?></td>
									<td width="60"><? echo $row['year']; ?></td>
									<td width="60"><? echo $row['job_no_prefix_num']; ?></td>

									<td width="100" style="word-break:break-all">
										<p><a href="#" onClick="precost_bom_pop('materialSheet',<?= $variable; ?>);"><? echo $row['job_no']; ?></a></p>
									</td>

									<td width="100"><?= $row['job_insert_date'] ?></td>
									<td width="80"><? echo number_format($job_qty_pcs, 2); //$row['order_qty']; 
													?></td>
									<td width="80" title="<?= $costing_for . '=' . $order_price_per_dzn; ?>"><? echo number_format($job_qty_pcs / 12, 2); ?></td>
									<td width="100"><? echo number_format($row['total_price'], 2); ?></td>
									<td width="70"><?= count($po_id_arr[$row['company']][$row['job_no']][$booking_id]); ?></td>

									<td width="70"><?= $yes_no[$precost_arr[$row['job_no']]['ready_to_app']]; ?></td>

									<td width="100"><?= $booking_id; ?></td>

									<td width="80"><? echo $yes_no[$row['ready_to_approved']]; ?></td>
									<td width="60"><?= $yes_no[$approval_data[$booking_id]['ready_to_app']]; ?></td>
									<td width="80">
										<?php
										if ($row['booking_type'] == 1 && $row['is_short'] == 2) {
											echo "Main";
										} elseif ($row['booking_type'] == 4 && $row['is_short'] == 2) {
											echo "Sample";
										} elseif ($row['entry_form'] == 88) {
											echo "Short";
										}
										?>

									</td>
									<td width="100"><?

													if ($row['pay_mode'] == 3 || $row['pay_mode'] == 5) {
														echo $company_name_arr[$row['supplier_id']];
													} else {
														echo $supplier_arr[$row['supplier_id']];
													}


													?></td>
									<td width="80"><?= $fabric_source[$row['fabric_source']]; ?></td>
									<td width="80"><? foreach ($row['item'] as $item) {
														echo $garments_item[$item] . ",";
													}; ?></td>
									<td width="100" align="center"><a href='##' onClick="generate_popup('<?= $job_id; ?>','<?= $booking_id; ?>','fab_desc_popup')"> view</a> </td>
									<td width="80"><?= number_format($row['wo_qnty'], 2, '.', ''); ?></td>
									<td width="80"><?= number_format($row['amount'] / $currency_arr[$row['company']]['currency_rate'], 2, '.', ''); ?></td>
									<td width="100"><? echo change_date_format($row['booking_date'], 'dd-mm-yyyy', '-'); ?></td>


									<td width="100"><?= $booking_wise_data_arr[$booking_id]['fso_no']; ?></td>
									<td width="100" title="Booking wise Qnty show.Not Job Wise"><?= number_format($booking_wise_data_arr[$booking_id]['grey_qty'], 2); ?></td>


									<td width="80"><?= number_format($booking_wise_data_arr[$booking_id]['demand_qnty'], 2); ?></td>
									<td width="80"><?= number_format(($booking_wise_data_arr[$booking_id]['issue_qnty']), 2); ?></td>
									<td width="80"><?= number_format($yarn_balance, 2); ?></td>

									<td width="80"><?= number_format($booking_wise_data_arr[$booking_id]['in_knit'], 2); ?></td>
									<td width="80"><?= number_format(($booking_wise_data_arr[$booking_id]['out_knit']), 2); ?></td>
									<td width="80"><?= number_format($knit_prog, 2); ?></td>
									<td width="80"><?= number_format(($booking_wise_data_arr[$booking_id]['grey_qty'] - $knit_prog), 2); ?></td>


									<td width="80"><?= number_format($booking_wise_data_arr[$booking_id]['in_knit_prod'], 2);	?></td>
									<td width="80"><?= number_format($booking_wise_data_arr[$booking_id]['out_knit_prod'], 2); ?></td>
									<td width="80"><?= number_format($knit_prod, 2); ?></td>


									<td width="80"><?= number_format($booking_wise_data_arr[$booking_id]['gray_rcv_qnty'], 2); ?></td>
									<td width="80"><?= number_format($booking_wise_data_arr[$booking_id]['gray_issue_qnty'], 2); ?> </td>
									<td width="80"><?= number_format($stock_in_hand, 2); ?> </td>
									<td width="80"><?= number_format($booking_wise_data_arr[$booking_id]['batch_qnty'], 2); ?></td>
									<td width="80"><?= number_format($booking_wise_data_arr[$booking_id]['dye_prod_qnty'], 2); ?></td>
									<td width="80"><?= number_format($booking_wise_data_arr[$booking_id]['finish_prod_qnty'], 2); ?></td>
									<td width="80"><?= number_format($booking_wise_data_arr[$booking_id]['textile_rcv_qnty'], 2); ?></td>
									<td width="80"><?= number_format($booking_wise_data_arr[$booking_id]['textile_issue_qnty'], 2); ?></td>
									<td width="80"><?= number_format($textile_in_hand, 2); ?></td>
									<td width="80"><?= number_format($row['wo_qnty'] - $textile_in_hand, 2, '.', ''); ?></td>
								</tr>


						<?
								$wo_qty += $row['wo_qnty'];
								$wo_amount += $row['amount'] / $currency_arr[$row['company']]['currency_rate'];
								$fso_qnty += $booking_wise_data_arr[$booking_id]['grey_qty'];
								$in_knit += $booking_wise_data_arr[$booking_id]['in_knit'];
								$out_knit += $booking_wise_data_arr[$booking_id]['out_knit'];
								$tot_knit_prog += $knit_prog;
								$knit_bal += $booking_wise_data_arr[$booking_id]['grey_qty'] - $knit_prog;;
								$in_knit_prod += $booking_wise_data_arr[$booking_id]['in_knit_prod'];
								$out_knit_prod += $booking_wise_data_arr[$booking_id]['out_knit_prod'];
								$tot_knit_prod += $knit_prod;
								$gray_rcv_qnty += $booking_wise_data_arr[$booking_id]['gray_rcv_qnty'];
								$gray_issue_qnty += $booking_wise_data_arr[$booking_id]['gray_issue_qnty'];
								$tot_stock_in_hand += $stock_in_hand;
								$batch_qnty += $booking_wise_data_arr[$booking_id]['batch_qnty'];
								$dye_prod_qnty += $booking_wise_data_arr[$booking_id]['dye_prod_qnty'];
								$finish_prod_qnty += $booking_wise_data_arr[$booking_id]['finish_prod_qnty'];
								$textile_rcv_qnty += $booking_wise_data_arr[$booking_id]['textile_rcv_qnty'];
								$textile_issue_qnty += $booking_wise_data_arr[$booking_id]['textile_issue_qnty'];
								$tot_textile_in_hand += $textile_in_hand;
								$issue_bal += $row['wo_qnty'] - $textile_in_hand;
								$booking_no = $booking_id;
								$i++;
							}
						}
						?>
					</table>
					<!-- <table width="3920" border="1" class="rpt_table" rules="all"  align="left">
					<tfoot>
				    <tr>
						
						<td  width="50"></td>
						<td  width="100"></td>
						<td  width="100"></td>
                        <td  width="100"></td>
						<td  width="80"></td>
						<td  width="120"></td>
						<td  width="120"></td>
						<td  width="80"></td>
						<td  width="60"></td>
						<td  width="60"></td>
						<td  width="100"></td>
						<td  width="80" id="order_qty"><? echo number_format($row['order_qty'], 2); ?></td>
						<td  width="80" id="order_qty_dzn"><? echo number_format(($row['order_qty'] / 12), 2); ?></td>
						<td  width="100" id="order_price"><? echo number_format($row['total_price'], 2); ?></td>
						<td  width="70"></td>					
						<td  width="70"></td>					
						<td  width="100"></td>
						<td  width="80"></td>
						<td  width="60"></td>
						<td  width="80"></td>						                       
                        <td  width="100"></td>
						<td  width="80"></td>		
						<td  width="80"></td>
						<td  width="100" align="center"></td>						
						<td  width="80" id="wo_qty"><?= $wo_qty; ?></td>
						<td  width="80" id="wo_amount"><?= $wo_amount; ?></td>
						<td  width="100"></td>

					
						<td  width="100"></td>
                        <td  width="100" id="fso_qnty"><?= $fso_qnty; ?></td>						

								
						<td  width="80" id="in_knit"><?= $in_knit; ?></td>
						<td  width="80" id="out_knit"><?= $out_knit; ?></td>
						<td  width="80" id="knit_prog"><?= $tot_knit_prog; ?></td>
						<td  width="80" id="knit_bal"><?= $knit_bal; ?></td>

										
						<td   width="80" id="in_knit_prod"><?= $in_knit_prod; ?></td>
						<td   width="80" id="out_knit_prod"><?= $out_knit_prod; ?></td>
						<td   width="80" id="knit_prod"><?= $knit_prod; ?></td>

								
						<td  width="80" id="gray_rcv_qnty"><?= $gray_rcv_qnty; ?></td>
						<td  width="80" id="gray_issue_qnty"><?= $gray_issue_qnty; ?> </td>
						<td  width="80" id="stock_in_hand"><?= $stock_in_hand; ?> </td>
						<td  width="80" id="batch_qnty"><?= $batch_qnty; ?></td>
						<td  width="80" id="dye_prod_qnty"><?= $dye_prod_qnty; ?></td>
						<td  width="80" id="finish_prod_qnty"><?= $finish_prod_qnty; ?></td>
						<td  width="80" id="textile_rcv_qnty"><?= $textile_rcv_qnty; ?></td>
						<td  width="80" id="textile_issue_qnty"><?= $textile_issue_qnty; ?></td>
						<td  width="80" id="textile_in_hand"><?= $textile_in_hand; ?></td>
						<td  width="80" id="issue_bal"><?= $issue_bal; ?></td>
					</tr>
					</tfoot>
				</table> -->
				</div>

			</div>
		</div>
	</div>
	<?



	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html****$filename****1****$type";
	exit();
	disconnect($con);
	exit();
}

if ($action == "show_image") {
	echo load_html_head_contents("Set Entry", "../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	?>
	<table>
		<tr>
			<?
			foreach ($data_array as $row) {
			?>
				<td><img src='../../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
			<?
			}
			?>
		</tr>
	</table>
<?
	exit();
}

if ($action == "last_ex_factory_Date") {
	echo load_html_head_contents("Last Ex-Factory Details", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	//echo $id;//$job_no;
	$buyerArr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$po_arr = "select a.buyer_name, a.style_ref_no, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($id) ";
	$sql_po = sql_select($po_arr);
?>
	<script>
		function generate_print_report(action, company_id, sys_number, ex_factory_date) {
			var report_title = "Garments Delivery Entry";
			print_report(company_id + '*' + sys_number + '*' + ex_factory_date + '*' + report_title + '*' + 5, "ExFactoryPrintSonia", "../../../../production/requires/garments_delivery_entry_controller")
		}
	</script>
	<div style="width:100%" align="center">
		<fieldset style="width:550px">
			<div class="form_caption" align="center"><strong>Last Ex-Factory Details</strong></div><br />
			<div style="width:100%">
				<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th colspan="2">Buyer: <? echo $buyerArr[$sql_po[0][csf('buyer_name')]]; ?></th>
							<th colspan="2">Style : <? echo $sql_po[0][csf('style_ref_no')]; ?></th>
							<th colspan="2">Po: <? echo $sql_po[0][csf('po_number')]; ?></th>
						</tr>
						<tr>
							<th width="35">SL</th>
							<th width="90">Ex-fac. Date</th>
							<th width="120">Challan No.</th>
							<th width="100">Ex-Fact. Qnty.</th>
							<th width="100">Ex-Fact. Return Qnty.</th>
							<th>Trans. Com.</th>
						</tr>
					</thead>
				</table>
			</div>
			<div style="width:100%; max-height:400px;">
				<table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
					<?
					$i = 1;
					$job_po_qnty = 0;
					$job_plan_qnty = 0;
					$job_total_price = 0;
					/* $ex_fac_sql="SELECT id, ex_factory_date, ex_factory_qnty, challan_no, transport_com,
				CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
				from pro_ex_factory_mst where po_break_down_id in($id) and status_active=1 and is_deleted=0";*/

					$ex_fac_sql = ("select a.company_id, a.id, a.sys_number, b.ex_factory_date, b.challan_no, b.transport_com,
		CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
		CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($id) ");
					//echo $ex_fac_sql;
					$sql_dtls = sql_select($ex_fac_sql);

					foreach ($sql_dtls as $row_real) {
						if ($i % 2 == 0) $bgcolor = "#EFEFEF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="35"><? echo $i; ?></td>
							<td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
							<td width="120"><a href="#" onClick="generate_print_report('<? echo 'ExFactoryPrintSonia'; ?>','<? echo $row_real[csf('company_id')]; ?>','<? echo $row_real[csf('id')]; ?>','<? echo change_date_format($row_real[csf("ex_factory_date")]); ?>')"><? echo $row_real[csf("challan_no")]; ?></a></td>
							<td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
							<td width="100" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
							<td><? echo $row_real[csf("transport_com")]; ?></td>
						</tr>
					<?
						$rec_qnty += $row_real[csf("ex_factory_qnty")];
						$return_qnty += $row_real[csf("ex_factory_return_qnty")];
						$i++;
					}
					?>
					<tfoot>
						<tr>
							<th colspan="3">Total</th>
							<th><? echo number_format($rec_qnty, 2); ?></th>
							<th><? echo number_format($return_qnty, 2); ?></th>
							<th>&nbsp;</th>
						</tr>

						<tr>
							<th colspan="3">Total Balance</th>
							<th colspan="3" align="right"><? echo number_format($rec_qnty - $return_qnty, 2); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
	</div>
	<div style="display:none" id="data_panel"></div>
	<script type="text/javascript" src="../../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../../js/jquerybarcode.js"></script>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
<?
	exit();
}

if ($action == "fab_desc_popup") {
	//require_once('../../../../includes/common.php');
	echo load_html_head_contents("Farbic Description Details", "../../../../", 1, 1, $unicode, '', '');

	extract($_REQUEST);
	//echo $id;//$job_no;

	$color_name_arr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	// $sql="select c.id,c.po_number,a.company_id,b.fin_fab_qnty,b.rate,b.amount,b.fabric_color_id,b.construction,	b.copmposition,b.dia_width,b.gsm_weight,b.uom,b.color_type,b.adjust_qty,b.body_part	 from wo_po_break_down c, wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and c.job_no_mst=b.job_no and c.id=b.po_break_down_id and a.booking_no='$booking_no' and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1";
	$sql = "select a.po_break_down_id, b.body_part_id, b.color_type_id, b.construction, b.composition, b.gsm_weight, a.fabric_color_id, a.item_size,	a.dia_width, a.fin_fab_qnty,a.adjust_qty, a.process_loss_percent, a.uom, a.grey_fab_qnty, a.rate, a.amount, a.id, a.pre_cost_fabric_cost_dtls_id FROM wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b WHERE a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no='" . $booking_no . "' and a.job_no='$job_no' and a.is_short in (1,2) and a.status_active=1 and	a.is_deleted=0";

	$sql_data = sql_select($sql);
?>

	<div style="width:100%" align="center">
		<fieldset style="width:1200px">
			<div class="form_caption" align="center"><strong>Farbic Description Details</strong></div><br />
			<div style="width:100%">
				<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th width="35">SL</th>
							<th width="90">Body Part</th>
							<th width="100">Color Type</th>
							<th width="120">Constraction</th>
							<th width="150">Composition</th>
							<th width="50">GSM</th>
							<th width="50">Dia</th>
							<th width="80">Fab Color</th>
							<th width="80">Fab. Qty.</th>
							<th width="60">Rate</th>
							<th width="80">Adjust Qty</th>
							<th width="100">Adjust Amount</th>
							<th width="80">Booking Qty.</th>
							<th width="60">UOM</th>
						</tr>
					</thead>
				</table>
			</div>
			<div style="width:100%; max-height:400px;">
				<table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
					<?
					$i = 1;
					//$ex_fac_sql="SELECT id, ex_factory_date, ex_factory_qnty, challan_no, transport_com from pro_ex_factory_mst where po_break_down_id in($id) and status_active=1 and is_deleted=0";
					//echo $ex_fac_sql;


					foreach ($sql_data as $row) {
						if ($i % 2 == 0) $bgcolor = "#EFEFEF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="35"><? echo $i; ?></td>
							<td width="90"><?= $body_part[$row[csf("body_part_id")]]; ?></td>
							<td width="100"><?= $color_type[$row[csf("color_type_id")]]; ?></td>
							<td width="120"><?= $row[csf("construction")]; ?></td>
							<td width="150"><?= $row[csf("composition")]; ?></td>
							<td width="50"><?= $row[csf("gsm_weight")]; ?></td>
							<td width="50"><?= $row[csf("dia_width")]; ?></td>
							<td width="80" align="right"><?= $color_name_arr[$row[csf("fabric_color_id")]]; ?></td>
							<td width="80" align="right"><?= $row[csf("fin_fab_qnty")]; ?></td>
							<td width="60" align="right"><?= $row[csf("rate")]; ?></td>
							<td width="80" align="right"><?= $row[csf("adjust_qty")]; ?></td>
							<td width="100" align="right"><?= $row[csf("fin_fab_qnty")] * $row[csf("rate")]; ?></td>
							<td width="80" align="right"><?= $row[csf("fin_fab_qnty")] + $row[csf("adjust_qty")]; ?></td>
							<td width="60"><?= $row[csf("uom")]; ?></td>
						</tr>
					<?
						$rec_qnty += $row[csf("fin_fab_qnty")];
						$wo_qnty += $row[csf("fin_fab_qnty")] + $row[csf("adjust_qty")];

						$i++;
					}
					?>
					<tfoot>

						<tr>
							<th colspan="8">Total Balance</th>
							<th width="80" align="right"><? echo number_format($rec_qnty, 2); ?></th>
							<th width="60" align="right"></th>
							<th width="80" align="right"></th>
							<th width="100" align="right"></th>
							<th width="80" align="right"><?= $wo_qnty; ?></th>
							<th width="60" align="right"></th>

						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
	</div>
	<div style="display:none" id="data_panel"></div>
	<script type="text/javascript" src="../../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../../js/jquerybarcode.js"></script>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
<?
	exit();
}


?>