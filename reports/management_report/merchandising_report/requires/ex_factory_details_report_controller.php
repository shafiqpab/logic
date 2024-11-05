<?
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission'] = $permission;
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id = $_SESSION['logic_erp']['user_id'];

$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer", "id", "buyer_name");
$buyer_buffer_arr = return_library_array("select id,delivery_buffer_days from  lib_buyer", "id", "delivery_buffer_days");
$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
$supp_library = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
$lib_country = return_library_array("select id,country_name from lib_country", "id", "country_name");

$location_library = return_library_array("select id,location_name from lib_location", "id", "location_name");
$floor_library = return_library_array("select id,floor_name from lib_prod_floor", "id", "floor_name");

if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 100, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "- All Buyer -", $selected, "");
	exit();
}

if ($action == "load_drop_delivery_company") {
	$explode_data = explode("**", $data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if ($data == 3) {
		if ($db_type == 0) {
			echo create_drop_down("cbo_delivery_company_name", 150, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name", "id,supplier_name", 0, "--- Select ---", $selected, "", 0, 0);
		} else {
			echo create_drop_down("cbo_delivery_company_name", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 0, "--Select--", $selected, "");
		}
	} else if ($data == 1) {
		echo create_drop_down("cbo_delivery_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 0, "-- Select Delivery Company --", '', "load_drop_down( 'requires/ex_factory_details_report_controller', this.value, 'load_drop_down_location', 'location' );", 0);
	} else
		echo create_drop_down("cbo_delivery_company_name", 150, $blank_array, "", 0, "--- Select ---", $selected, "", 0, 0);
	exit();
}


if ($action == "load_drop_down_location") {
	$companies = "'" . $data . "'";
	echo create_drop_down("cbo_location_name", 120, "SELECT id,location_name from lib_location where company_id in($data) and status_active =1 and is_deleted=0 group by id,location_name order by location_name", "id,location_name", 0, "-- Select --", $selected, "load_drop_down( 'requires/ex_factory_details_report_controller', $companies+'**'+this.value, 'load_drop_down_del_floor', 'del_floor_td' );");
}
if ($action == "load_drop_down_del_floor") {
	$data = explode('**', $data);
	$data[0] = str_replace("'", "", $data[0]);
	echo create_drop_down("cbo_del_floor", 105, "select id,floor_name from lib_prod_floor where company_id in($data[0]) and location_id='$data[1]' and status_active =1 and is_deleted=0 and production_process=11 order by floor_name", "id,floor_name", 0, "-- Select Floor --", $selected, "");
	exit();
}

if ($action == "report_generate") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$company_name = str_replace("'", "", $cbo_company_name);
	$buyer_name = str_replace("'", "", $cbo_buyer_name);
	$delivery_company_name = str_replace("'", "", $cbo_delivery_company_name);
	$location_name = str_replace("'", "", $cbo_location_name);
	$shipping_status = str_replace("'", "", $cbo_shipping_status);
	$date_from = str_replace("'", "", $txt_date_from);
	$date_to = str_replace("'", "", $txt_date_to);
	$reportType = str_replace("'", "", $reportType);

	// =========================== MAKING QUERY  COND ============================

	$shiping_status_cond = ($shipping_status != "") ? " and b.shiping_status in($shipping_status)" : "";

	if ($date_from != "" && $date_to != "") {
		$date_cond = "and c.ex_factory_date between '$date_from' and  '$date_to' ";
	} else {
		$date_cond = "";
	}

	if ($delivery_company_name) {
		$del_comp_cond = "and e.delivery_company_id in($delivery_company_name) ";
	} else {
		$del_comp_cond = "";
	}
	if ($location_name) {
		$del_location_cond .= "and e.location_id in($cbo_location_name) ";
	} else {
		$del_location_cond = "";
	}
	if ($company_name) {
		$company_cond = " and a.company_name in($company_name)";
	} else {
		$company_cond = "";
	}
	if (str_replace("'", "", $cbo_buyer_name)) {
		$buyer_cond1 = " and a.buyer_name = " . str_replace("'", "",  $cbo_buyer_name);
	}

	// =============================================== MAIN QUERY =========================================
	$sql = "SELECT a.company_name,a.job_no_prefix_num,f.item_number_id, a.buyer_name, a.style_ref_no,a.ship_mode as po_ship_mode,b.id as po_id, b.po_number,b.shiping_status,b.unit_price, c.shiping_mode, e.delivery_company_id as del_com,e.delivery_location_id as  del_loc,f.cutup_date,f.country_ship_date, max(c.ex_factory_date) as ex_factory_date, sum(d.production_qnty) as ex_fact_qty, sum(c.total_carton_qnty) as carton_qnty,e.attention
	from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c, pro_ex_factory_dtls d, pro_ex_factory_delivery_mst e, wo_po_color_size_breakdown f
	where a.id=b.job_id and b.id=c.po_break_down_id and a.id=f.job_id and b.id=f.po_break_down_id and c.id=d.mst_id and e.id=c.delivery_mst_id and f.id=d.color_size_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.is_deleted=0 and e.is_deleted=0 and  e.is_deleted=0 and f.is_deleted=0 and f.status_active=1 $company_cond $buyer_cond1 $del_comp_cond $del_location_cond $shiping_status_cond $date_cond 
	group by a.company_name,a.job_no_prefix_num,f.item_number_id,a.buyer_name,a.style_ref_no, a.ship_mode,b.id ,b.po_number,b.shiping_status,c.shiping_mode, e.delivery_company_id,e.delivery_location_id,f.cutup_date,f.country_ship_date,b.unit_price,e.attention";

	//echo $sql;die;
	$sql_res = sql_select($sql);
	if (count($sql_res) < 1) {
?>
		<style type="text/css">
			.alert {
				padding: 12px 35px 12px 14px;
				margin-bottom: 18px;
				text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
				background-color: #fcf8e3;
				border: 1px solid #fbeed5;
				-webkit-border-radius: 4px;
				-moz-border-radius: 4px;
				border-radius: 4px;
				color: #c09853;
				font-size: 16px;
				text-align: left;
			}

			.alert strong {
				font-size: 18px;
			}

			.alert-danger,
			.alert-error {
				background-color: #f2dede;
				border-color: #eed3d7;
				color: #b94a48;
			}
		</style>
		<div style="margin:20px auto; width: 90%">
			<div class="alert alert-error">
				<strong>Oh Snap!</strong> Change a few things up and try submitting again.
			</div>
		</div>
	<?
		die();
	}
	$main_array = array();
	$buyer_summary_array = array();
	$po_id_arr = array();
	$item_number_arr = array();
	foreach ($sql_res as $row) {
		$po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
		$main_array[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('del_com')]][$row[csf('del_loc')]]['job_no_prefix_num'] = $row[csf('job_no_prefix_num')];
		$main_array[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('del_com')]][$row[csf('del_loc')]]['po_number'] = $row[csf('po_number')];
		$main_array[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('del_com')]][$row[csf('del_loc')]]['shiping_status'] = $row[csf('shiping_status')];
		$main_array[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('del_com')]][$row[csf('del_loc')]]['shiping_mode'] = $row[csf('shiping_mode')];
		$main_array[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('del_com')]][$row[csf('del_loc')]]['po_ship_mode'] = $row[csf('po_ship_mode')];
		$main_array[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('del_com')]][$row[csf('del_loc')]]['cutup_date'] = $row[csf('cutup_date')];
		$main_array[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('del_com')]][$row[csf('del_loc')]]['ex_factory_date'] = $row[csf('ex_factory_date')];
		$main_array[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('del_com')]][$row[csf('del_loc')]]['country_ship_date'] = $row[csf('country_ship_date')];
		$main_array[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('del_com')]][$row[csf('del_loc')]]['attention'] = $row[csf('attention')];

		$main_array[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('del_com')]][$row[csf('del_loc')]]['ex_fact_qty'] += $row[csf('ex_fact_qty')];
		$main_array[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('del_com')]][$row[csf('del_loc')]]['carton_qnty'] = $row[csf('carton_qnty')];

		$main_array[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('del_com')]][$row[csf('del_loc')]]['ITEM_NUMBER_ID'] .= $garments_item[$row[csf('item_number_id')]] . ",";

		// ================== for buyer summary =====================
		$buyer_summary_array[$row[csf('buyer_name')]]['cur_ex_fact_qty'] 	+= $row[csf('ex_fact_qty')];
		$buyer_summary_array[$row[csf('buyer_name')]]['unit_price'] 		+= $row[csf('unit_price')];
		$buyer_summary_array[$row[csf('buyer_name')]]['cur_ex_fact_val'] 	+= $row[csf('ex_fact_qty')] * $row[csf('unit_price')];

		$buyer_bufer_days 	= $buyer_buffer_arr[$row[csf('buyer_name')]];
		$cutup_date 		= $row[csf('cutup_date')];
		$ex_factory_date 	= $row[csf('ex_factory_date')];
		$country_ship_date 	= $row[csf('country_ship_date')];
		// ========== add buyer_bufer_days ================
		if ($buyer_bufer_days) {

			$cutup_date = strtotime($cutup_date);
			$exten_date = date('d-M-y', strtotime("+ $buyer_bufer_days", $cutup_date));
		} else {
			$exten_date = $cutup_date;
		}
		// ================ for shipment status wise qnty ==========================
		if (strtotime($country_ship_date) > strtotime($ex_factory_date)) {
			$buyer_summary_array[$row[csf('buyer_name')]]['early_qty'] 		+= $row[csf('ex_fact_qty')];
			$main_array[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('del_com')]][$row[csf('del_loc')]]['status'] = "Early";
		} else if (strtotime($exten_date) > strtotime($ex_factory_date)) {
			$buyer_summary_array[$row[csf('buyer_name')]]['ontime_qty'] 	+= $row[csf('ex_fact_qty')];
			$main_array[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('del_com')]][$row[csf('del_loc')]]['status'] = "Ontime";
		} else if (strtotime($exten_date) < strtotime($ex_factory_date)) {
			$buyer_summary_array[$row[csf('buyer_name')]]['late_qty'] 		+= $row[csf('ex_fact_qty')];
			$main_array[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('del_com')]][$row[csf('del_loc')]]['status'] = "Late";
		}
	}
	$poIds = implode(",", $po_id_arr);
	// ========================= FOR PO ID ARRAY ==========================
	if (count($po_id_arr) > 999 && $db_type == 2) {
		$po_chunk = array_chunk($po_id_arr, 999);
		$po_ids_cond = "";
		foreach ($po_chunk as $vals) {
			$imp_ids = implode(",", $vals);
			if ($po_ids_cond == "") {
				$po_ids_cond .= " and ( b.id in ($imp_ids) ";
			} else {
				$po_ids_cond .= " or b.id in ($imp_ids) ";
			}
		}
		$po_ids_cond .= " )";
	} else {
		$po_ids_cond = " and b.id in($poIds) ";
	}
	// echo "<pre>";
	// print_r($buyer_summary_array);
	// echo "</pre>";

	// ================================== GETTING PO QNTY =======================================
	$po_qty_sql = "SELECT a.buyer_name, a.style_ref_no, b.id as po_id, sum(b.po_quantity*a.total_set_qnty) as po_qty,(b.unit_price/a.total_set_qnty) as unit_price from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id $po_ids_cond and a.status_active=1 and b.status_active=1  and a.is_deleted=0 and b.is_deleted=0 group by a.buyer_name, a.style_ref_no, b.id,b.unit_price,a.total_set_qnty";
	//echo $po_qty_sql;die;
	$po_qty_sql_res = sql_select($po_qty_sql);
	$po_qnty_array = array();
	$buyer_po_qnty_array = array();
	foreach ($po_qty_sql_res as $row) {
		$po_qnty_array[$row[csf('style_ref_no')]][$row[csf('buyer_name')]][$row[csf('po_id')]]['po_qty'] += $row[csf('po_qty')];
		$po_qnty_array[$row[csf('style_ref_no')]][$row[csf('buyer_name')]][$row[csf('po_id')]]['unit_price'] += $row[csf('unit_price')];

		$buyer_po_qnty_array[$row[csf('buyer_name')]] += $row[csf('po_qty')];
	}
	// echo "<pre>";
	// print_r($po_qnty_array);
	// echo "</pre>";

	// ================================= GETTING ex-fact and CARTON QNTY ===========================================
	$exfact_sql = "SELECT c.po_break_down_id as po_id, e.delivery_company_id AS del_com, e.delivery_location_id AS del_loc, SUM (c.total_carton_qnty) AS carton_qnty,sum(c.ex_factory_qnty) as ex_fact_qty,e.buyer_id FROM pro_ex_factory_mst c, pro_ex_factory_delivery_mst e 
		WHERE e.id = c.delivery_mst_id AND c.status_active = 1 AND e.status_active = 1 and  e.is_deleted=0 and c.is_deleted=0 $del_comp_cond $del_location_cond $date_cond 
		GROUP BY c.po_break_down_id, e.delivery_company_id, e.delivery_location_id,e.buyer_id";
	$exfact_sql_res = sql_select($exfact_sql);
	$carton_qty_array = array();
	$exfact_qty_array = array();
	foreach ($exfact_sql_res as $row) {
		$carton_qty_array[$row[csf('po_id')]][$row[csf('del_com')]][$row[csf('del_loc')]] = $row[csf('carton_qnty')];
		$exfact_qty_array[$row[csf('po_id')]][$row[csf('del_com')]][$row[csf('del_loc')]] = $row[csf('ex_fact_qty')];
	}


	ob_start();
	?>
	<style type="text/css">
		.gd-color {
			background: #f0f9ff;
			/* Old browsers */
			background: -moz-linear-gradient(top, #f0f9ff 0%, #cbebff 47%, #a1dbff 100%);
			/* FF3.6-15 */
			background: -webkit-linear-gradient(top, #f0f9ff 0%, #cbebff 47%, #a1dbff 100%);
			/* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom, #f0f9ff 0%, #cbebff 47%, #a1dbff 100%);
			/* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f0f9ff', endColorstr='#a1dbff', GradientType=0);
			/* IE6-9 */
			font-weight: bold;
		}

		.gd-color2 {
			background: rgb(247, 251, 252);
			/* Old browsers */
			background: -moz-linear-gradient(top, rgba(247, 251, 252, 1) 0%, rgba(217, 237, 242, 1) 40%, rgba(173, 217, 228, 1) 100%);
			/* FF3.6-15 */
			background: -webkit-linear-gradient(top, rgba(247, 251, 252, 1) 0%, rgba(217, 237, 242, 1) 40%, rgba(173, 217, 228, 1) 100%);
			/* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom, rgba(247, 251, 252, 1) 0%, rgba(217, 237, 242, 1) 40%, rgba(173, 217, 228, 1) 100%);
			/* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f7fbfc', endColorstr='#add9e4', GradientType=0);
			/* IE6-9 */
			font-weight: bold;
		}

		.gd-color3 {
			background: rgb(254, 255, 255);
			/* Old browsers */
			background: -moz-linear-gradient(top, rgba(254, 255, 255, 1) 0%, rgba(221, 241, 249, 1) 35%, rgba(160, 216, 239, 1) 100%);
			/* FF3.6-15 */
			background: -webkit-linear-gradient(top, rgba(254, 255, 255, 1) 0%, rgba(221, 241, 249, 1) 35%, rgba(160, 216, 239, 1) 100%);
			/* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom, rgba(254, 255, 255, 1) 0%, rgba(221, 241, 249, 1) 35%, rgba(160, 216, 239, 1) 100%);
			/* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#feffff', endColorstr='#a0d8ef', GradientType=0);
			/* IE6-9 */
			border: 1px solid #dccdcd;
			font-weight: bold;
		}
	</style>
	<div style="width:3100x;">
		<div style="width:1000px">
			<table width="930" cellspacing="0" align="center">
				<tr>
					<td align="center" colspan="12" class="form_caption">
						<strong style="font-size:16px;">
							<?
							$multi_com_id = array_unique(explode(",", str_replace("'", "", $cbo_company_name)));
							$multi_com_name = '';
							foreach ($multi_com_id as $key => $value) {
								$multi_com_name .= $company_library[$value] . ",";
							}
							?>
							Company Name:<? echo chop($multi_com_name, ","); //echo $company_library[str_replace("'","",$cbo_company_name)]; 
											?> </strong>
					</td>
				</tr>
				<tr class="form_caption">
					<td colspan="12" align="center" class="form_caption"> <strong style="font-size:15px;">Ex-Factory Details Report</strong></td>
				</tr>
				<tr align="center">
					<td colspan="12" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
				</tr>
			</table>
			<!-- ================================== SUMMARY PART ==================================== -->
			<table width="1010" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
				<thead>
					<th width="30">SL</th>
					<th width="100">Buyer Name</th>
					<th width="80">Curr. Ex-Fact. Qty.</th>
					<th width="80">Curr. Ex-Fact. Qty. DZN</th>
					<th width="80">Avg. Price</th>
					<th width="80">Curr. Ex-Fact. Val</th>
					<th width="80">Early Qnty</th>
					<th width="80">On Time Qty</th>
					<th width="80">Late Qty</th>
					<th width="80">Extra Qty.</th>
					<th width="80">Extra Value </th>
					<th width="80">Short Qty</th>
					<th width="80">Short Value</th>
				</thead>
			</table>
			<table width="1010" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
				<tbody>
					<?
					$buyer_idArr=array();
					foreach ($main_array as $com_id => $company_data) 
					{
						foreach ($company_data as $buyer_id => $buyer_data) 
						{
							 foreach ($buyer_data as $style => $style_data) 
							 {
									foreach ($style_data as $po_id => $po_data)
									 {
										foreach ($po_data as $del_com_id => $del_com_data) 
										{
											foreach ($del_com_data as $del_loc_id => $row) 
											{
												$po_qty 			= $po_qnty_array[$style][$buyer_id][$po_id]['po_qty'];
												$unit_price 		= $po_qnty_array[$style][$buyer_id][$po_id]['unit_price'];
												// $ex_fact_qty 		= $exfact_qty_array[$po_id][$del_com_id][$del_loc_id];
												
												$ex_fact_qty 		= $row['ex_fact_qty'];
												//echo $ex_fact_qty."<br>";
												$ex_fact_val 		= $unit_price * $ex_fact_qty;
												$ex_fact_avg_rate 	= $ex_fact_val / $ex_fact_qty;
												$carton_qnty 		= $carton_qty_array[$po_id][$del_com_id][$del_loc_id];
												$excess_shortage_qty = $po_qty - $ex_fact_qty;
												//echo $excess_shortage_qty."<br>";
												$excess_shortage_val = $excess_shortage_qty * $unit_price;

												$extra_quantity=$po_qty - $ex_fact_qty;
												
												
												$buyer_idArr[$buyer_id]['cur_ex_fact_qty']+=$ex_fact_qty;
												$buyer_idArr[$buyer_id]['excess_shortage_qty']+=$excess_shortage_qty;
												
												$buyer_idArr[$buyer_id]['excess_shortage_val']+=$excess_shortage_val;
												//$buyer_idArr[$buyer_id]['early_qty']+=$buyer_summary_array[$buyer_id]['early_qty'];
												//$buyer_idArr[$buyer_id]['late_qty']+=$ex_fact_qty;
												$buyer_idArr[$buyer_id]['cur_ex_fact_val']+=$row['ex_fact_qty']*$unit_price;
												$buyer_idArr[$buyer_id]['unit_price']+=$unit_price;
												if($excess_shortage_qty<0)
												{
													
													$buyer_idArr[$buyer_id]['extra_qty']+=$excess_shortage_qty;

												}
												
											}
										}
									}
							 }
							
						}
					}
					//echo"<pre>";print_r($buyer_idArr);die;
					$sl = 1;
					$sm_ex_qty = 0;
					$sm_cur_ex_fact_dzn_qty = 0;
					$sm_ex_price = 0;
					$sm_ex_val = 0;
					$sm_cur_ex_val = 0;
					$sm_early_qty = 0;
					$sm_on_tim_qty = 0;
					$sm_extra_qty = 0;
					$sm_extra_val = 0;
					$sm_short_qty = 0;
					$sm_short_val = 0;
					
					foreach ($buyer_idArr as $buyer_key => $row) {
						if ($sl % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
						$cur_ex_fact_qty 	= $row['cur_ex_fact_qty'];
						$unit_price 		= $row['unit_price'];
						$early_qty 			= $buyer_summary_array[$buyer_key]['early_qty']/ $cur_ex_fact_qty * 100;
						$late_qty  			= $buyer_summary_array[$buyer_key]['early_qty'] / $cur_ex_fact_qty * 100;
						$ontime_qty  		= $buyer_summary_array[$buyer_key]['ontime_qty'] / $cur_ex_fact_qty * 100;
						$po_qty 			= $po_qnty_array[$$buyer_key]['po_qty'];
						//echo $unit_price."<br>";
					//	$buyer_po_qnty=$buyer_po_qnty_array[$buyer_key];early_qty


						//$cur_ex_fact_val	= $cur_ex_fact_qty*$unit_price;

						$cur_ex_fact_val	= $row['cur_ex_fact_val'];

						$avg_price 			= $cur_ex_fact_val / $cur_ex_fact_qty;
						$order_quantity		= $buyer_po_qnty_array[$buyer_key];
						//echo $cur_ex_fact_qty;
						
						
						$short_quantity		= $row['excess_shortage_qty'];
						$extra_quantity		= $row['extra_qty'];
						$extra_value		= $extra_quantity * $unit_price;
						$short_value		= $row['excess_shortage_val'];					
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
						<?
						    
							?>
							<td width="30"><? echo $sl; ?></td>
							<td width="100" align="left"><? echo $buyer_arr[$buyer_key]; ?></td>
							<td width="80" align="right"><? echo number_format($cur_ex_fact_qty, 0); ?></td>
							<td width="80" align="right"><? $cur_ex_fact_dzn_qty = $cur_ex_fact_qty / 12;
															echo number_format($cur_ex_fact_dzn_qty, 2); ?></td>
							<td width="80" align="right"><? echo number_format($avg_price, 2); ?></td>
							<td width="80" align="right"><? echo number_format($cur_ex_fact_val, 2); ?></td>
							<td width="80" align="right"><? echo number_format($early_qty, 0); ?>%</td>
							<td width="80" align="right"><? echo number_format($ontime_qty, 0); ?>%</td>
							<td width="80" align="right"><? echo number_format($late_qty, 0); ?>%</td>
							<td width="80" align="right"><? echo number_format($extra_quantity, 0); ?></td>
							<td width="80" align="right"><? echo number_format($extra_value, 2); ?></td>
							<td width="80" align="right"><? echo number_format($short_quantity, 0); ?></td>
							<td width="80" align="right"><? echo number_format($short_value, 2); ?></td>
						</tr>
					<?
						$sl++;
						$sm_ex_qty += $cur_ex_fact_qty;
						$sm_cur_ex_fact_dzn_qty += $cur_ex_fact_dzn_qty;
						$sm_ex_price += $avg_price;
						$sm_ex_val += $cur_ex_fact_val;
						$sm_cur_ex_val += $early_qty;
						$sm_early_qty += $ontime_qty;
						$sm_on_tim_qty += $late_qty;
						$sm_extra_qty += $extra_quantity;
						$sm_extra_val += $extra_value;
						$sm_short_qty += $short_quantity;
						$sm_short_val += $short_value;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th></th>
						<th>Total</th>
						<th><? echo number_format($sm_ex_qty, 0); ?></th>
						<th><? echo number_format($sm_cur_ex_fact_dzn_qty, 2); ?></th>
						<th><? //echo number_format($sm_ex_price,2);
							?></th>
						<th><? echo number_format($sm_ex_val, 2); ?></th>
						<th><? //echo number_format($sm_cur_ex_val,2);
							?></th>
						<th><? //echo number_format($sm_early_qty,0);
							?></th>
						<th><? //echo number_format($sm_on_tim_qty,0);
							?></th>
						<th><? echo number_format($sm_extra_qty, 0); ?></th>
						<th><? echo number_format($sm_extra_val, 2); ?></th>
						<th><? echo number_format($sm_short_qty, 0); ?></th>
						<th><? echo number_format($sm_short_val, 2); ?></th>
					</tr>
				</tfoot>
			</table>
		</div>
		<br />
		<div style="margin-top: 20px;">
			<table width="2040">
				<tr>
					<td colspan="21" class="form_caption">
						<strong style="font-size:16px;">Ex-Factory Details Report</strong>
					</td>
				</tr>
			</table>
			<!-- ===================================== START DETAILS PART ===================================== -->
			<table width="2040" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_2">
				<thead>
					<th width="40">SL</th>
					<th width="60">Job</th>
					<th width="100">Buyer Name</th>
					<th width="100">Order NO</th>
					<th width="100">Style No</th>
					<th width="100">Item</th>
					<th width="100">Del Company</th>
					<th width="100">Del Location</th>
					<th width="100">PO Ship Mood</th>
					<th width="100">Ex-Fact. Mood</th>
					<th width="80">PO Qty (Pcs)</th>
					<th width="80">PO Qty (Dzn)</th>
					<th width="80">Unit Price</th>
					<th width="80">Cut Off Date</th>
					<th width="80">Ex-Fact. Date</th>
					<th width="80">Cur. Ex-Fact. Qty(pcs)</th>
					<th width="80">Cur. Ex-Fact. Qty(Dzn)</th>
					<th width="80">Cur. Ex-Fact. Avg. Rate</th>
					<th width="80">Cur. Ex-Fact. Val</th>
					<th width="80">Total Carton Qty</th>
					<th width="80">Excess/ Shortage Qty</th>
					<th width="80">Excess/ Shortage Value</th>
					<th width="80">Shipment Status</th>
					<th width="100">Cause Of Delay</th>
				</thead>
			</table>
			<div style="width:2060px; overflow-y:scroll; overflow-x:hidden; max-height:300px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="2040">
					<tbody>
						<?
						$i = 1;
						$gt_ex_fact_qty 		= 0;
						$gt_ex_fact_qty_dzn 		= 0;
						$gt_ex_fact_rate 		= 0;
						$gt_ex_fact_val 		= 0;
						$gt_carton_qty 			= 0;
						$gt_shortage_excess_qty = 0;
						$gt_shortage_excess_val = 0;
						foreach ($main_array as $com_id => $company_data) {
						?>
							<tr class="gd-color">
								<td colspan="24" height="30" valign="middle"><strong> LC Company : <? echo $company_library[$com_id]; ?></strong></td>
							</tr>
							<?
							$com_ex_fact_qty 	 	= 0;
							$com_ex_fact_qty_dzn 	 	= 0;
							$com_ex_fact_rate 	 	= 0;
							$com_ex_fact_val 	 	= 0;
							$com_carton_qty 		= 0;
							$com_shortage_excess_qty = 0;
							$com_shortage_excess_val = 0;
							foreach ($company_data as $buyer_id => $buyer_data) {
								$by_ex_fact_qty 		= 0;
								$by_ex_fact_qty_dzn 		= 0;
								$by_ex_fact_rate 		= 0;
								$by_ex_fact_val 		= 0;
								$by_carton_qty 			= 0;
								$by_shortage_excess_qty = 0;
								$by_shortage_excess_val = 0;
								foreach ($buyer_data as $style => $style_data) {
									foreach ($style_data as $po_id => $po_data) {
										foreach ($po_data as $del_com_id => $del_com_data) {
											foreach ($del_com_data as $del_loc_id => $row) {
												$po_qty 			= $po_qnty_array[$style][$buyer_id][$po_id]['po_qty'];
												
												$unit_price 		= $po_qnty_array[$style][$buyer_id][$po_id]['unit_price'];
												// $ex_fact_qty 		= $exfact_qty_array[$po_id][$del_com_id][$del_loc_id];
												$ex_fact_qty 		= $row['ex_fact_qty'];
												$ex_fact_val 		= $unit_price * $ex_fact_qty;
												$ex_fact_avg_rate 	= $ex_fact_val / $ex_fact_qty;
												$carton_qnty 		= $carton_qty_array[$po_id][$del_com_id][$del_loc_id];
												$excess_shortage_qty = $po_qty - $ex_fact_qty;


												$excess_shortage_val = $excess_shortage_qty * $unit_price;
												
													
												if ($i % 2 == 0) $bgcolor = "#E9F3FF";
												else $bgcolor = "#FFFFFF";
							?>
												<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
													<td width="40"><? echo $i; ?></td>
													<td width="60"><? echo $row['job_no_prefix_num']; ?></td>
													<td width="100"><? echo $buyer_arr[$buyer_id]; ?></td>
													<td width="100">
														<p><? echo $row['po_number']; ?></p>
													</td>
													<td width="100">
														<p><? echo $style; ?> </p>
													</td>
													<td width="100"><? echo implode(",", array_unique(explode(",", chop($row["ITEM_NUMBER_ID"], ',')))); ?> </td>
													<td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $company_library[$del_com_id]; ?> </td>
													<td width="100"><? echo $location_library[$del_loc_id]; ?></td>
													<td width="100"><? echo $shipment_mode[$row['po_ship_mode']]; ?></td>
													<td width="100"><? echo $shipment_mode[$row['shiping_mode']]; ?></td>
													<td width="80" align="right"><? echo number_format($po_qty, 0); ?></td>
													<td width="80" align="right">
														<?
														$po_qty_dzn = $po_qty / 12;
														echo number_format($po_qty_dzn, 2); ?></td>
													<td width="80" align="right"><? echo $unit_price; ?></td>
													<td width="80" align="center">&nbsp;<? echo change_date_format($row['cutup_date']); ?>&nbsp;</td>
													<td width="80" align="center">&nbsp;<? echo change_date_format($row['ex_factory_date']); ?>&nbsp;</td>
													<td width="80" align="right">
														<a href="javascript:void(0)" onclick="openPopup('<? echo $com_id . '_' . $buyer_id . '_' . $po_id . '_' . $del_com_id . '_' . $del_loc_id . '_' . $date_from . '_' . $date_to; ?>')">
															<? echo number_format($ex_fact_qty, 0); ?>
														</a>
													</td>
													<td width="80" align="right"><? $ex_fact_qty_dzn = $ex_fact_qty / 12;
																					echo number_format($ex_fact_qty_dzn, 2); ?></td>
													<td width="80" align="right"><? echo number_format($ex_fact_avg_rate, 2); ?></td>
													<td width="80" align="right"><? echo number_format($ex_fact_val, 2); ?></td>
													<td width="80" align="right"><? echo number_format($carton_qnty, 0); ?></td>
													<td width="80" align="right"><? echo number_format($excess_shortage_qty, 0); ?></td>
													<td width="80" align="right"><? echo number_format($excess_shortage_val, 2); ?></td>
													<td width="80"><? echo $shipment_status[$row['shiping_status']]; ?></td>
													<td width="100"><? echo $row['ATTENTION']; ?></td>
												</tr>
								<?
												$i++;
												// for buyer wise total
												$by_ex_fact_qty 		+= $ex_fact_qty;
												$by_ex_fact_qty_dzn 	+= $ex_fact_qty_dzn;
												//$by_ex_fact_rate 		+= $ex_fact_avg_rate;
												$by_ex_fact_val 		+= $ex_fact_val;
												$by_carton_qty 			+= $carton_qnty;
												$by_shortage_excess_qty += $excess_shortage_qty;
												$by_shortage_excess_val += $excess_shortage_val;

												// for lc company wise total
												$com_ex_fact_qty 	 	+= $ex_fact_qty;
												$com_ex_fact_qty_dzn	+= $ex_fact_qty_dzn;
												$com_ex_fact_rate 	 	+= $ex_fact_avg_rate;
												$com_ex_fact_val 	 	+= $ex_fact_val;
												$com_carton_qty 	 	+= $carton_qnty;
												$com_shortage_excess_qty += $excess_shortage_qty;
												$com_shortage_excess_val += $excess_shortage_val;

												// for grand total
												$gt_ex_fact_qty 		+= $ex_fact_qty;
												$gt_ex_fact_qty_dzn 		+= $ex_fact_qty_dzn;
												$gt_ex_fact_rate 		+= $ex_fact_avg_rate;
												$gt_ex_fact_val 		+= $ex_fact_val;
												$gt_carton_qty 			+= $carton_qnty;
												$gt_shortage_excess_qty += $excess_shortage_qty;
												$gt_shortage_excess_val += $excess_shortage_val;
												
											}
										}
									}
								}
								?>
								<tr class="gd-color">
									<td colspan="15" align="right"><strong> Buyer Wise Total </strong></td>
									<td width="80" align="right"><? echo number_format($by_ex_fact_qty, 0); ?></td>
									<td width="80" align="right"><? echo number_format($by_ex_fact_qty_dzn, 2); ?></td>
									<td width="80" align="right"><? echo number_format($by_ex_fact_val / $by_ex_fact_qty, 2); ?></td>
									<td width="80" align="right"><? echo number_format($by_ex_fact_val, 2); ?></td>
									<td width="80" align="right"><? echo number_format($by_carton_qty, 0); ?></td>
									<td width="80" align="right"><? echo number_format($by_shortage_excess_qty, 0); ?></td>
									<td width="80" align="right"><? echo number_format($by_shortage_excess_val, 2); ?></td>
									<td width="80" align=""></td>
									<td width="100" align=""></td>
								</tr>
							<?
							}
							?>
							<tr class="gd-color2">
								<td colspan="15" align="right"><strong> LC Company Wise Total </strong></td>
								<td width="80" align="right"><? echo number_format($com_ex_fact_qty, 0); ?></td>
								<td width="80" align="right"><? echo number_format($com_ex_fact_qty_dzn, 2); ?></td>
								<td width="80" align="right"><? echo number_format($com_ex_fact_val / $com_ex_fact_qty, 2); ?></td>
								<td width="80" align="right"><? echo number_format($com_ex_fact_val, 2); ?></td>
								<td width="80" align="right"><? echo number_format($com_carton_qty, 0); ?></td>
								<td width="80" align="right"><? echo number_format($com_shortage_excess_qty, 0); ?></td>
								<td width="80" align="right"><? echo number_format($com_shortage_excess_val, 2); ?></td>
								<td width="80" align=""></td>
								<td width="100" align=""></td>
							</tr>
						<?
						}
						?>

					</tbody>
				</table>
			</div>
			<div>
				<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="2040" id="tbl_footer">
					<tfoot>
						<tr class="gd-color3">
							<td width="40"></td>
							<td width="60"></td>
							<td width="100"></td>
							<td width="100"> </td>
							<td width="100"> </td>
							<td width="100"> </td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="80" align="right">Grand Total</td>

							<td width="80" align="right"><? echo number_format($gt_ex_fact_qty, 0); ?></td>
							<td width="80" align="right"><? echo number_format($gt_ex_fact_qty_dzn, 2); ?></td>
							<td width="80" align="right"><? echo number_format($gt_ex_fact_val / $gt_ex_fact_qty, 2); ?></td>
							<td width="80" align="right"><? echo number_format($gt_ex_fact_val, 2); ?></td>
							<td width="80" align="right"><? echo number_format($gt_carton_qty, 0); ?></td>
							<td width="80" align="right"><? echo number_format($gt_shortage_excess_qty, 0); ?></td>
							<td width="80" align="right"><? echo number_format($gt_shortage_excess_val, 2); ?></td>

							<td width="80"></td>
							<td width="100"></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
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
	$filename = $user_id . "_" . $name . ".xls";
	echo "$total_data####$filename####$reportType";
	exit();
}

if ($action == "report_generate_monthly") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$company_name = str_replace("'", "", $cbo_company_name);
	$buyer_name = str_replace("'", "", $cbo_buyer_name);
	$delivery_company_name = str_replace("'", "", $cbo_delivery_company_name);
	$location_name = str_replace("'", "", $cbo_location_name);
	$shipping_status = str_replace("'", "", $cbo_shipping_status);
	$date_from = str_replace("'", "", $txt_date_from);
	$date_to = str_replace("'", "", $txt_date_to);
	$reportType = str_replace("'", "", $reportType);
	$invoice_qty_array = return_library_array("select id,invoice_quantity from com_export_invoice_ship_mst", "id", "invoice_quantity");
	$invoice_val_array = return_library_array("select id,invoice_value from com_export_invoice_ship_mst", "id", "invoice_value");

	// =========================== MAKING QUERY  COND ============================

	$shiping_status_cond = ($shipping_status != "") ? " and b.shiping_status in($shipping_status)" : "";

	if ($date_from != "" && $date_to != "") {
		$date_cond = "and c.ex_factory_date between '$date_from' and  '$date_to' ";
	} else {
		$date_cond = "";
	}

	if ($delivery_company_name) {
		$del_comp_cond = "and e.delivery_company_id in($delivery_company_name) ";
	} else {
		$del_comp_cond = "";
	}
	if ($location_name) {
		$del_location_cond .= "and e.location_id in($cbo_location_name) ";
	} else {
		$del_location_cond = "";
	}
	if ($company_name) {
		$company_cond = " and a.company_name in($company_name)";
	} else {
		$company_cond = "";
	}
	if (str_replace("'", "", $cbo_buyer_name)) {
		$buyer_cond1 = " and a.buyer_name = " . str_replace("'", "",  $cbo_buyer_name);
	}

	// =============================================== MAIN QUERY =========================================
	$sql = "SELECT a.company_name,a.job_no_prefix_num,a.order_uom,b.shipment_date,f.item_number_id, a.buyer_name, a.style_ref_no,a.ship_mode as po_ship_mode,b.id as po_id, b.po_number,b.shiping_status,b.unit_price, c.shiping_mode, e.delivery_company_id as del_com,e.delivery_location_id as  del_loc,f.cutup_date,f.country_ship_date, max(c.ex_factory_date) as ex_factory_date, sum(d.production_qnty) as ex_fact_qty, sum(c.total_carton_qnty) as carton_qnty,e.attention,c.invoice_no
	from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c, pro_ex_factory_dtls d, pro_ex_factory_delivery_mst e, wo_po_color_size_breakdown f
	where a.id=b.job_id and b.id=c.po_break_down_id and a.id=f.job_id and b.id=f.po_break_down_id and c.id=d.mst_id and e.id=c.delivery_mst_id and f.id=d.color_size_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 $company_cond $buyer_cond1 $del_comp_cond $del_location_cond $shiping_status_cond $date_cond 
	group by a.company_name,a.job_no_prefix_num,a.order_uom,b.shipment_date,f.item_number_id,a.buyer_name,a.style_ref_no, a.ship_mode,b.id ,b.po_number,b.shiping_status,c.shiping_mode, e.delivery_company_id,e.delivery_location_id,f.cutup_date,f.country_ship_date,b.unit_price,e.attention,c.invoice_no";

	//echo $sql;die;
	$sql_res = sql_select($sql);
	if (count($sql_res) < 1) {
	?>
		<style type="text/css">
			.alert {
				padding: 12px 35px 12px 14px;
				margin-bottom: 18px;
				text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
				background-color: #fcf8e3;
				border: 1px solid #fbeed5;
				-webkit-border-radius: 4px;
				-moz-border-radius: 4px;
				border-radius: 4px;
				color: #c09853;
				font-size: 16px;
				text-align: left;
			}

			.alert strong {
				font-size: 18px;
			}

			.alert-danger,
			.alert-error {
				background-color: #f2dede;
				border-color: #eed3d7;
				color: #b94a48;
			}
		</style>
		<div style="margin:20px auto; width: 90%">
			<div class="alert alert-error">
				<strong>Oh Snap!</strong> Change a few things up and try submitting again.
			</div>
		</div>
	<?
		die();
	}
	$main_array = array();
	$buyer_summary_array = array();
	$po_id_arr = array();
	$item_number_arr = array();
	foreach ($sql_res as $row) {
		$po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
		$monthYear = date('M-Y', strtotime($row[csf('ex_factory_date')]));
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['job_no_prefix_num'] = $row[csf('job_no_prefix_num')];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['company_name'] = $row[csf('company_name')];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['po_number'] = $row[csf('po_number')];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['shipment_date'] = $row[csf('shipment_date')];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['ship_status'] = $row[csf('shiping_status')];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['shiping_mode'] = $row[csf('shiping_mode')];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['order_uom'] = $row[csf('order_uom')];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['po_ship_mode'] = $row[csf('po_ship_mode')];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['cutup_date'] = $row[csf('cutup_date')];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['ex_factory_date'] = $row[csf('ex_factory_date')];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['country_ship_date'] = $row[csf('country_ship_date')];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['attention'] = $row[csf('attention')];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['invoice_qty'] += $invoice_qty_array[$row[csf('invoice_no')]];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['invoice_val'] += $invoice_val_array[$row[csf('invoice_no')]];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['ex_fact_qty'] += $row[csf('ex_fact_qty')];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['unit_price'] = $row[csf('unit_price')];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['ex_fact_val'] += $row[csf('ex_fact_qty')]*$row[csf('unit_price')];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['carton_qnty'] = $row[csf('carton_qnty')];
		$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['ITEM_NUMBER_ID'] .= $garments_item[$row[csf('item_number_id')]] . ",";

		// ================== for buyer summary =====================
		$buyer_summary_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]]['company_name'] 	     = $row[csf('company_name')];
		$buyer_summary_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]]['cur_ex_fact_qty'] 	+= $row[csf('ex_fact_qty')];
		$buyer_summary_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]]['unit_price'] 		+= $row[csf('unit_price')];
		$buyer_summary_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]]['cur_ex_fact_val'] 	+= $row[csf('ex_fact_qty')] * $row[csf('unit_price')];
		$buyer_summary_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]]['invoice_qty'] 	         += $invoice_qty_array[$row[csf('invoice_no')]];
		$buyer_summary_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]]['invoice_val'] 	         += $invoice_val_array[$row[csf('invoice_no')]];

		$buyer_bufer_days 	= $buyer_buffer_arr[$row[csf('buyer_name')]];
		$cutup_date 		= $row[csf('cutup_date')];
		$ex_factory_date 	= $row[csf('ex_factory_date')];
		$country_ship_date 	= $row[csf('country_ship_date')];
		// ========== add buyer_bufer_days ================
		if ($buyer_bufer_days) {

			$cutup_date = strtotime($cutup_date);
			$exten_date = date('d-M-y', strtotime("+ $buyer_bufer_days", $cutup_date));
		} else {
			$exten_date = $cutup_date;
		}
		// ================ for shipment status wise qnty ==========================
		if (strtotime($country_ship_date) > strtotime($ex_factory_date)) {
			$buyer_summary_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]]['early_qty'] 		+= $row[csf('ex_fact_qty')];
			$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['status'] = "Early";
		} else if (strtotime($exten_date) > strtotime($ex_factory_date)) {
			$buyer_summary_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]]['ontime_qty'] 	+= $row[csf('ex_fact_qty')];
			$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['ontime_qty'] = $row[csf('ex_fact_qty')];
			$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['status'] = "Ontime";
		} else if (strtotime($exten_date) < strtotime($ex_factory_date)) {
			$buyer_summary_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]]['late_qty'] 		+= $row[csf('ex_fact_qty')];
			$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['status'] = "Late";
			$main_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['late_qty'] = $row[csf('ex_fact_qty')];
		}
	}
	$poIds = implode(",", $po_id_arr);
	// ========================= FOR PO ID ARRAY ==========================
	if (count($po_id_arr) > 999 && $db_type == 2) {
		$po_chunk = array_chunk($po_id_arr, 999);
		$po_ids_cond = "";
		foreach ($po_chunk as $vals) {
			$imp_ids = implode(",", $vals);
			if ($po_ids_cond == "") {
				$po_ids_cond .= " and ( b.id in ($imp_ids) ";
			} else {
				$po_ids_cond .= " or b.id in ($imp_ids) ";
			}
		}
		$po_ids_cond .= " )";
	} else {
		$po_ids_cond = " and b.id in($poIds) ";
	}
	 /* echo "<pre>";
	print_r($buyer_summary_array);
	echo "</pre>";  */

	// ================================== GETTING PO QNTY =======================================
	$po_qty_sql = "SELECT a.company_name,a.buyer_name, a.style_ref_no, b.id as po_id, sum(b.po_quantity*a.total_set_qnty) as po_qty,b.unit_price,max(c.ex_factory_date) as ex_factory_date from wo_po_details_master a,wo_po_break_down b, pro_ex_factory_mst c where a.id=b.job_id and b.id=c.po_break_down_id $po_ids_cond and a.status_active=1 and b.status_active=1  and c.status_active=1 group by a.company_name,a.buyer_name, a.style_ref_no, b.id,b.unit_price";
	$po_qty_sql_res = sql_select($po_qty_sql);
	$po_qnty_array = array();
	$buyer_po_qnty_array = array();
	$monthYear = date('M-Y', strtotime($row[csf('ex_factory_date')]));
	foreach ($po_qty_sql_res as $row) {
		$po_qnty_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['po_qty'] = $row[csf('po_qty')];
		$po_qnty_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]['unit_price'] = $row[csf('unit_price')];
		$buyer_po_qnty_array[$row[csf('company_name')]][$monthYear][$row[csf('buyer_name')]]['po_qty'] += $row[csf('po_qty')];
	}
	// echo "<pre>";
	// print_r($po_qnty_array);
	// echo "</pre>";




	ob_start();
	?>
	<style type="text/css">
		.gd-color {
			background: #f0f9ff;
			/* Old browsers */
			background: -moz-linear-gradient(top, #f0f9ff 0%, #cbebff 47%, #a1dbff 100%);
			/* FF3.6-15 */
			background: -webkit-linear-gradient(top, #f0f9ff 0%, #cbebff 47%, #a1dbff 100%);
			/* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom, #f0f9ff 0%, #cbebff 47%, #a1dbff 100%);
			/* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f0f9ff', endColorstr='#a1dbff', GradientType=0);
			/* IE6-9 */
			font-weight: bold;
		}

		.gd-color2 {
			background: rgb(247, 251, 252);
			/* Old browsers */
			background: -moz-linear-gradient(top, rgba(247, 251, 252, 1) 0%, rgba(217, 237, 242, 1) 40%, rgba(173, 217, 228, 1) 100%);
			/* FF3.6-15 */
			background: -webkit-linear-gradient(top, rgba(247, 251, 252, 1) 0%, rgba(217, 237, 242, 1) 40%, rgba(173, 217, 228, 1) 100%);
			/* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom, rgba(247, 251, 252, 1) 0%, rgba(217, 237, 242, 1) 40%, rgba(173, 217, 228, 1) 100%);
			/* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f7fbfc', endColorstr='#add9e4', GradientType=0);
			/* IE6-9 */
			font-weight: bold;
		}

		.gd-color3 {
			background: rgb(254, 255, 255);
			/* Old browsers */
			background: -moz-linear-gradient(top, rgba(254, 255, 255, 1) 0%, rgba(221, 241, 249, 1) 35%, rgba(160, 216, 239, 1) 100%);
			/* FF3.6-15 */
			background: -webkit-linear-gradient(top, rgba(254, 255, 255, 1) 0%, rgba(221, 241, 249, 1) 35%, rgba(160, 216, 239, 1) 100%);
			/* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom, rgba(254, 255, 255, 1) 0%, rgba(221, 241, 249, 1) 35%, rgba(160, 216, 239, 1) 100%);
			/* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#feffff', endColorstr='#a0d8ef', GradientType=0);
			/* IE6-9 */
			border: 1px solid #dccdcd;
			font-weight: bold;
		}
	</style>
	<div style="width:3100x;">
		<div style="width:1000px">
			<table width="930" cellspacing="0" align="center">
				<tr>
					<td align="center" colspan="12" class="form_caption">
						<strong style="font-size:16px;">
							<?
							$multi_com_id = array_unique(explode(",", str_replace("'", "", $cbo_company_name)));
							$multi_com_name = '';
							foreach ($multi_com_id as $key => $value) {
								$multi_com_name .= $company_library[$value] . ",";
							}
							?>
							Company Name:<? echo chop($multi_com_name, ","); //echo $company_library[str_replace("'","",$cbo_company_name)]; 
											?> </strong>
					</td>
				</tr>
				<tr class="form_caption">
					<td colspan="12" align="center" class="form_caption"> <strong style="font-size:15px;">Ex-Factory Details Report</strong></td>
				</tr>
				<tr align="center">
					<td colspan="12" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
				</tr>
			</table>
			<!-- ================================== SUMMARY PART ==================================== -->
			<table width="830" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
				<thead>
					<th width="30">SL</th>
					<th width="100">Company</th>
					<th width="100">Month</th>
					<th width="100">Buyer Name</th>
					<th width="100">Style QTY</th>
					<th width="100">Ex-Fact. Qty.</th>
					<th width="100">Ex-Fact. Val</th>
					<th width="100">Invoice Qty</th>
					<th width="100">Invoice Value</th>
				</thead>
			</table>
			<table width="830" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
				<tbody>
					<?
					$sl = 1;
					$s = 1;

					foreach ($buyer_summary_array as $com_id => $company_data) {
						$sm_ex_qty      = 0;
						$sm_ex_val      = 0;
						$sm_invoice_qty = 0;
						$sm_invoice_val = 0;
						$sm_po_qty      = 0;
						foreach ($company_data as $month_key => $month_data) {
							$flag = 0;
							foreach ($month_data as $buyer_key => $row) {
								if ($s % 2 == 0) $bgcolor = "#E9F3FF";
								else $bgcolor = "#FFFFFF";
								$cur_ex_fact_qty 	= $row['cur_ex_fact_qty'];
								$unit_price 		= $row['unit_price'];
								$po_qty 			= $buyer_po_qnty_array[$com_id][$month_key][$buyer_key]['po_qty'];
								$cur_ex_fact_val	= $row['cur_ex_fact_val'];
								$invoice_qnty		= $row['invoice_qty'];
								$invoice_val		= $row['invoice_val'];
								if ($flag == 0) {
					?>
									<tr>
										<td rowspan="<?= count($month_data); ?>" width="30"><? echo $sl; ?></td>
										<td rowspan="<?= count($month_data); ?>" width="100" align="left"><? echo $company_library[$com_id]; ?></td>
										<td rowspan="<?= count($month_data); ?>" width="100" align="left"><? echo $month_key; ?></td>
									<?
								} else {
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $s; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $s; ?>">

									<? } ?>

									<td width="100" align="left"><? echo $buyer_arr[$buyer_key]; ?></td>
									<td width="100" align="right"><? echo number_format($po_qty, 0); ?></td>
									<td width="100" align="right"><? echo number_format($cur_ex_fact_qty, 0); ?></td>
									<td width="100" align="right"><? echo number_format($cur_ex_fact_val, 2); ?></td>
									<td width="100" align="right"><? echo number_format($invoice_qnty, 0); ?></td>
									<td width="100" align="right"><? echo number_format($invoice_val, 2); ?></td>
									</tr>
							<?

								$sm_ex_qty 	    += $cur_ex_fact_qty;
								$sm_ex_val	    += $cur_ex_fact_val;
								$sm_po_qty      += $po_qty;
								$sm_invoice_qty += $invoice_qnty;
								$sm_invoice_val += $invoice_val;
								$s++;
								$flag = 1;
							}

							$sl++;
						}
							?>
							<tr>
								<td align="right" colspan="4"><b>Total</b></td>
								<td align="right"><b><? echo number_format($sm_po_qty, 2); ?></b></td>
								<td align="right"><b><? echo number_format($sm_ex_qty, 2); ?></b></td>
								<td align="right"><b><? echo number_format($sm_ex_val, 2); ?></b></td>
								<td align="right"><b><? echo number_format($sm_invoice_qty, 2); ?></b></td>
								<td align="right"><b><? echo number_format($sm_invoice_val, 0); ?></b></td>
							</tr>
						<?

						$tot_sm_po_qty += $sm_po_qty;
						$tot_sm_ex_qty += $sm_ex_qty;
						$tot_sm_ex_val += $sm_ex_val;
						$tot_sm_invoice_qty += $sm_invoice_qty;
						$tot_sm_invoice_val += $sm_invoice_val;
					}

						?>
						<tr>
							<td align="right" colspan="4"><b> Grand Total</td>
							<td align="right"><b><? echo number_format($tot_sm_po_qty, 2); ?></b></td>
							<td align="right"><b><? echo number_format($tot_sm_ex_qty, 2); ?></b></td>
							<td align="right"><b><? echo number_format($tot_sm_ex_val, 2); ?></b></td>
							<td align="right"><b><? echo number_format($tot_sm_invoice_qty, 2); ?></b></td>
							<td align="right"><b><? echo number_format($tot_sm_invoice_val, 0); ?></b></td></b>
						</tr>
				</tbody>
			</table>
		</div>
		<br />
		<div style="margin-top: 20px;">
			<table width="2040">
				<tr>
					<td colspan="21" class="form_caption">
						<strong style="font-size:16px;">Ex-Factory Details Report</strong>
					</td>
				</tr>
			</table>
			<!-- ===================================== START DETAILS PART ===================================== -->
			<table width="1780" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_2">
				<thead>
					<th width="40">SL</th>
					<th width="100">Company</th>
					<th width="100">Buyer Name</th>
					<th width="60">Job</th>
					<th width="100">Style No</th>
					<th width="100">Order NO</th>
					<th width="100">Shipment Date</th>
					<th width="60">UOM</th>
					<th width="80">Style Qty (Pcs)</th>
					<th width="80">FOB</th>
					<th width="80">Value($)</th>
					<th width="80">Ex-Fact. Qty(pcs)</th>
					<th width="80">Ex-Fact. Val</th>
					<th width="80">Ex-Fact. Date</th>
					<th width="80">On Time Qty</th>
					<th width="80">On Time(%)</th>
					<th width="80">Late Qnty</th>
					<th width="80">Late(%)</th>
					<th width="80">Shipment Status</th>
					<th width="80">Invoice Qty</th>
					<th width="80">Invoice Value</th>
					<th width="80">Difference</th>
				</thead>
			</table>
			<div style="width:1800px; overflow-y:scroll; overflow-x:hidden; max-height:300px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="1780">
					<tbody>
						<?
						$i = 1;
						$gt_ex_fact_qty 		= 0;
						$gt_ex_fact_qty_dzn 	= 0;
						$gt_ex_fact_rate 		= 0;
						$gt_ex_fact_val 		= 0;
						$gt_carton_qty 			= 0;
						$gt_shortage_excess_qty = 0;
						$gt_shortage_excess_val = 0;
						foreach ($main_array as $com_id => $company_data) {
						?>
							<tr class="gd-color">
								<td colspan="23" height="30" valign="middle"><strong> Company : <? echo $company_library[$com_id];
																									?></strong></td>
							</tr>
							<?
							$com_ex_fact_qty 	 	= 0;
							$com_ex_fact_qty_dzn 	 	= 0;
							$com_ex_fact_rate 	 	= 0;
							$com_ex_fact_val 	 	= 0;
							$com_carton_qty 		= 0;
							$com_shortage_excess_qty = 0;
							$com_shortage_excess_val = 0;
							foreach ($company_data as $month_id => $month_data) {
								foreach ($month_data as $buyer_id => $buyer_data) {
									$tot_po_qty 		+= 0;
											$tot_po_val 	    += 0;
											$tot_ex_fact_qty    += 0;
											$tot_ex_fact_val 	+= 0;
											$tot_late_qty       += 0;
											$tot_ontime_qty 	+= 0;
											$tot_invoice_qty 	+= 0;
											$tot_invoice_val 	+= 0;
									foreach ($buyer_data as $style => $style_data) {
										foreach ($style_data as $po_id => $row) {

											$po_qty 			= $po_qnty_array[$com_id][$month_id][$buyer_id][$style][$po_id]['po_qty'];
											$unit_price 		= $po_qnty_array[$com_id][$month_id][$buyer_id][$style][$po_id]['unit_price'];
											// $ex_fact_qty 		= $exfact_qty_array[$po_id][$del_com_id][$del_loc_id];
											$ex_fact_qty 		= $row['ex_fact_qty'];
											$ex_fact_val 		= $row['ex_fact_val'];
											$ontime_qty			= $row['ontime_qty'];
											$status 		    = $row['status'];
											$ontime_per			= $ontime_qty / $ex_fact_qty * 100;
											$late_qty			= $row['late_qty'];
											$invoice_qty		= $row['invoice_qty'];
											$invoice_val		= $row['invoice_val'];
											$late_per			= $late_qty / $ex_fact_qty * 100;
											//$ex_fact_val 		= $unit_price * $ex_fact_qty;
											$ex_fact_avg_rate 	= $ex_fact_val / $ex_fact_qty;
											$carton_qnty 		= $carton_qty_array[$po_id][$del_com_id][$del_loc_id];
											$excess_shortage_qty = $po_qty - $ex_fact_qty;
											$excess_shortage_val = $excess_shortage_qty * $unit_price;

											if ($i % 2 == 0) $bgcolor = "#E9F3FF";
											else $bgcolor = "#FFFFFF";
							?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
												<td width="40" align="center"><? echo $i; ?></td>
												<td width="100" align="center"><? echo $company_library[$com_id]; ?></td>
												<td width="100" align="center"><? echo $buyer_arr[$buyer_id]; ?></td>
												<td width="60" align="center"><? echo $row['job_no_prefix_num']; ?></td>
												<td width="100" align="center">
													<p><? echo $style; ?> </p>
												</td>
												<td width="100" align="center">
													<p><? echo $row['po_number']; ?></p>
												</td>
												<td width="100" align="center"><? echo change_date_format($row['shipment_date']); ?></td>
												<td width="60" align="center"><? echo $unit_of_measurement[$row['order_uom']]; ?></td>
												<td width="80" align="right"><? echo number_format($po_qty, 0); ?></td>
												<td width="80" align="right"><? echo $unit_price; ?></td>
												<td width="80" align="right"><? echo number_format($po_qty * $unit_price, 0); ?></td>

												<td width="80" align="right">
													<a href="javascript:void(0)" onclick="openPopup('<? echo $com_id . '_' . $buyer_id . '_' . $po_id . '_' . $del_com_id . '_' . $del_loc_id . '_' . $date_from . '_' . $date_to; ?>')">
														<? echo number_format($ex_fact_qty, 0); ?>
													</a>
												</td>
												<td width="80" align="right"><? echo number_format($ex_fact_val, 2); ?></td>
												<td width="80" align="center">&nbsp;<? echo change_date_format($row['ex_factory_date']); ?>&nbsp;</td>
												<td width="80" align="right"><? echo number_format($ontime_qty, 0); ?></td>
												<td width="80" align="right"><? echo number_format($ontime_per, 0); ?></td>
												<td width="80" align="right"><? echo number_format($late_qty, 2); ?></td>
												<td width="80" align="right"><? echo number_format($late_per, 2); ?></td>
												<td width="80" align="center"><? echo $status; ?></td>
												<td width="80" align="right"><? echo number_format($invoice_qty, 2); ?></td>
												<td width="80" align="right"><? echo number_format($invoice_val, 2); ?></td>
												<td width="80" align="right"><? echo number_format($ex_fact_val - $invoice_val, 2); ?></td>
											</tr>
						<?
											$i++;
											$tot_po_qty 		+= $po_qty;
											$tot_po_val 	    += $po_qty * $unit_price;
											$tot_ex_fact_qty    += $ex_fact_qty;
											$tot_ex_fact_val 	+= $ex_fact_val;
											$tot_late_qty       += $late_qty;
											$tot_ontime_qty 	+= $ontime_qty;
											$tot_invoice_qty 	+= $invoice_qty;
											$tot_invoice_val 	+= $invoice_val;
										}
									}
								}
							}
						}
						?>

					</tbody>
				</table>
			</div>
			<div>
				<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="1780" id="tbl_footer">
					<tfoot>
						<tr class="gd-color3">
							<th width="40"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="60"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="60"></th>
							<th width="80"><? //echo number_format($tot_po_qty, 0); ?></th>
							<th width="80"><? //echo number_format($ex_fact_val-$invoice_val,2); 
											?></th>
							<th width="80"><? //echo number_format($tot_po_val, 0); ?>Total</th>
							<th width="80"><? echo number_format($tot_ex_fact_qty, 2); ?></th>
							<th width="80"><? echo number_format($tot_ex_fact_val, 2); ?></th>
							<th width="80"><? //echo number_format($ex_fact_val-$invoice_val,2); 
											?></th>
							<th width="80"><? echo number_format($tot_ontime_qty, 2); ?></th>
							<th width="80"><? //echo number_format($tot_ontime_val,2); 
											?></th>
							<th width="80"><? echo number_format($tot_late_qty, 2); ?></th>
							<th width="80"><? //echo number_format($tot_late_val,2); 
											?></th>
							<th width="80"><? //echo number_format($ex_fact_val-$invoice_val,2); 
											?></th>
							<th width="80"><? echo number_format($tot_invoice_qty, 2); ?></th>
							<th width="80"><? echo number_format($tot_invoice_val, 0); ?></th>
							<th width="80"><? //echo number_format($ex_fact_val-$invoice_val,2); 
											?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
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
	$filename = $user_id . "_" . $name . ".xls";
	echo "$total_data####$filename####$reportType";
	exit();
}



if ($action == "ex_factory_popup") {
	echo load_html_head_contents("Report Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	// print_r($data);
	$ex_data 	= explode("_", $data);
	$company 	= $ex_data[0];
	$buyer 		= $ex_data[1];
	$po_id 		= $ex_data[2];
	$del_com 	= $ex_data[3];
	$del_loc 	= $ex_data[4];
	$date_from 	= $ex_data[5];
	$date_to 	= $ex_data[6];

	// ========================== load library =======================
	$colorArr = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ", "id", "size_name");
	$poArr = return_library_array("select id,po_number from  wo_po_break_down where status_active=1 and id=$po_id ", "id", "po_number");

	// =========================== making query cond ================================
	$sql_cond = "";
	$sql_cond = ($company) ? " and c.company_id = $company" : "";
	$sql_cond .= ($buyer) ? " and c.buyer_id = $buyer" : "";
	$sql_cond .= ($po_id) ? " and a.po_break_down_id = $po_id" : "";
	$sql_cond .= ($del_com) ? " and c.delivery_company_id = $del_com" : "";
	$sql_cond .= ($del_loc) ? " and c.delivery_location_id = $del_loc" : "";
	$sql_cond .= ($date_from && $date_to) ? " and a.ex_factory_date between '$date_from' and '$date_to' " : "";
	// echo $sql_cond;
	// =========================== start query ==========================================
	$sql = "SELECT d.id,d.color_number_id, d.size_number_id,d.country_id,a.po_break_down_id as po_id,sum(b.production_qnty) as qty ,e.unit_price,e.po_number
	from pro_ex_factory_mst a, pro_ex_factory_dtls b, pro_ex_factory_delivery_mst c, wo_po_color_size_breakdown d,wo_po_break_down e 
	where a.id=b.mst_id and c.id=a.delivery_mst_id and a.po_break_down_id=d.po_break_down_id and d.id=b.color_size_break_down_id and e.status_active=1 and e.id=a.po_break_down_id and e.id=d.po_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and c.is_deleted=0 and d.is_deleted=0  and e.is_deleted=0  $sql_cond 
	group by d.id,d.color_number_id, d.size_number_id,d.country_id,a.po_break_down_id,e.unit_price,e.po_number order by d.id";
	// echo $sql;
	$sql_res = sql_select($sql);
	$data_array = array();
	$color_size_qty_arr = array();
	$size_arr = array();
	foreach ($sql_res as $val) {
		$data_array[$val[csf('po_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['unit_price'] = $val[csf('unit_price')];
		$color_size_qty_arr[$val[csf('color_number_id')]][$val[csf('size_number_id')]] += $val[csf('qty')];
		// $color_size_qty_arr[$val[csf('color_number_id')]][$val[csf('size_number_id')]] = $val[csf('qty')];
		$size_arr[$val[csf('size_number_id')]] = $val[csf('size_number_id')];
	}
	// echo "<pre>";
	// print_r($data_array);
	// echo "</pre>";
	$tbl_width = 440 + (count($size_arr) * 60);
	$colspan = count($size_arr);
	?>
	<style type="text/css">
		hr {
			border-top: 1px solid #8DAFDA;
			border-width: 1px;
		}
	</style>
	<div style="width:100%" align="center">
		<fieldset style="width:800px">
			<div class="form_caption" align="center"><strong>Size Wise Ship Qty</strong></div><br />
			<table cellpadding="0" width="<? echo $tbl_width; ?>" class="rpt_table" rules="all" border="1">
				<thead>
					<tr>
						<th rowspan="2" width="30">SL</th>
						<th rowspan="2" width="130">Country</th>
						<th rowspan="2" width="100">Order</th>
						<th rowspan="2" width="100">Color Name</th>
						<th colspan="<? echo $colspan; ?>">Size</th>
						<th rowspan="2" width="80">Total</th>
					</tr>
					<tr>
						<?
						foreach ($size_arr as $size_key => $val) {
						?>
							<th width="60"><? echo $itemSizeArr[$size_key]; ?></th>
						<?
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					$grand_total_qty = 0;
					$grand_total_val = 0;
					$grand_total_qty_arr = array();
					$grand_total_val_arr = array();

					foreach ($data_array as $po_key => $po_arr) {
						$hr_total_qty_arr = array();
						foreach ($po_arr as $country_key => $country_arr) {
							foreach ($country_arr as $color_key => $color_arr) {
					?>
								<tr>
									<td><? echo $i; ?></td>
									<td><? echo $lib_country[$country_key]; ?></td>
									<td><? echo $poArr[$po_key]; ?></td>
									<td><? echo $colorArr[$color_key]; ?>
										<hr>Value
									</td>
									<?
									$v_total = 0;
									$v_price_total = 0;
									$hr_total_val_arr = array();
									foreach ($size_arr as $size_key => $size_val) {
									?>
										<td align="right">
											<? echo $color_size_qty_arr[$color_key][$size_key]; ?>
											<hr>
											<? $price = $color_size_qty_arr[$color_key][$size_key] * $color_arr['unit_price'];
											echo number_format($price, 2); ?>
										</td>
									<?
										$v_total += $color_size_qty_arr[$color_key][$size_key];
										$v_price_total += $price;
										$hr_total_qty_arr[$size_key] += $color_size_qty_arr[$color_key][$size_key];
										$hr_total_val_arr[$size_key] += $color_size_qty_arr[$color_key][$size_key] * $color_arr['unit_price'];


										$grand_total_qty_arr[$size_key] += $color_size_qty_arr[$color_key][$size_key];
										$grand_total_val_arr[$size_key] += $color_size_qty_arr[$color_key][$size_key] * $color_arr['unit_price'];


										$grand_total_qty += $color_size_qty_arr[$color_key][$size_key];
										$grand_total_val += $color_size_qty_arr[$color_key][$size_key] * $color_arr['unit_price'];
									}
									?>
									<td align="right">
										<? echo $v_total; ?>
										<hr>
										<? echo number_format($v_price_total, 2); ?>
									</td>
								</tr>
						<?
								$i++;
							}
						}
						?>
						<tr>
							<th colspan="4" align="right"><strong>Sub Total Qty :</strong>
								<hr><strong>Sub Total Value :</strong>
							</th>
							<?
							$gtotal = 0;
							foreach ($size_arr as $size_key => $val) {
							?>
								<th align="right">
									<? echo $hr_total_qty_arr[$size_key]; ?>
									<hr>
									<? echo $hr_total_val_arr[$size_key]; ?>
								</th>
							<?
								$gtotal_qty += $hr_total_qty_arr[$size_key];
								$gtotal_val += $hr_total_val_arr[$size_key];
							}
							?>
							<th align="right"><? echo $gtotal_qty; ?>
								<hr> <? echo $gtotal_val; ?>
							</th>
						</tr>
					<?
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="4" align="right"><strong>Grand Total Qty :</strong></th>
						<?
						$gtotal = 0;
						foreach ($size_arr as $size_key => $val) {
						?>
							<th align="right"><? echo $grand_total_qty_arr[$size_key]; ?></th>
						<?
						}
						?>
						<th align="right"><? echo $grand_total_qty; ?> </th>
					</tr>
					<tr>
						<th colspan="4" align="right"><strong>Grand Total Value:</strong></th>
						<?
						$gtotal = 0;
						foreach ($size_arr as $size_key => $val) {
						?>
							<th align="right"><? echo $grand_total_val_arr[$size_key]; ?></th>
						<?
						}
						?>
						<th><? echo $grand_total_val; ?></th>

					</tr>
				</tfoot>
			</table>
		</fieldset>
	</div>
<?
}
disconnect($con);
?>